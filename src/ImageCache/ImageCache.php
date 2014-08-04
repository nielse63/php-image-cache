<?php
/**
 * The primary class file for PHP Image Compressor & Caching
 *
 * This file is to be used in any PHP project that requires image compression
 *
 * @package PHP Image Compressor & Caching
 * @author Erik Nielsen <erik@312development.com | @erikkylenielsen>
 * @license http://freedomdefined.org/Licenses/CC-BY MIT
 * @version GIT: $Id$
 * 
 * @todo I have a laundry list of things I'd like to complete.  Among them:
 *  - Ensure that 304 headers are being returned each time.
 *  - Code cleanup and ensure PS2 standards are being met
 *  - Improve documentation
 *  - Write better unit tests for CI
 *  - Continue to optimize for memory usage
 *  - Create a method that returns new images sizes
 *  - Create a method that resizes the image
 * 
 * 
 * http://dtbaker.net/web-development/how-to-cache-images-generated-by-php/
 */

// namespace ImageCache;

ob_start();

function debug( $a )
{
	echo '<pre>';
	print_r($a);
	echo '</pre><hr>';
}

function dump( $a )
{
	echo '<pre>';
	var_dump($a);
	echo '</pre><hr>';
}

class ImageCache
{

	const memory_value = 128;

	/**
	 * Stores the image source given for reference
	 */
	private $image_src;

	/**
	 * Stores the server's version of the GD Library, if enabled
	 */
	private $gd_version;

	/**
	 * The memory limit currently established on the server
	 */
	private $memory_limit;

	/**
	 * If the file is remote or not
	 */
	private $is_remote;

	/**
	 * The file mime type
	 */
	private $file_mime_type;

	/**
	 * The name of the cached file
	 */
	private $cached_filename;

	/**
	 * The extension of the file
	 */
	private $file_extension;

	/**
	 * Allow the user to set the options for the setup
	 */
	public $options;

	/**
	 * Constructor. Passes $options as a parameter
	 */
	public function __construct( $options = array() )
	{
		if( ! $this->can_run_image_cache() )
			$this->error( 'PHP Image Cache must be run on a server with a bundled GD version.' );
		$defaults = array(
			'echo' => false, 				// Determines whether the resulting source should be echoed or returned
			'cache_time' => 0, 	// How long the image should be cached for. If the value is 0, then the cache never expires. Default is 0, never expires.
			'cached_image_directory' => dirname( __FILE__ ) . '/php-image-cache'
		);
		$this->options = (object) array_merge( $defaults, $options );

		if( $this->make_cache_directory() )
			return $this;
		return $this->error( "\n" . 'Please check server settings.' );
	}

	public function can_run_image_cache()
	{
		$gd_info = gd_info();
		$this->gd_version = false;
		if( preg_match( '#bundled \((.+)\)$#i', $gd_info['GD Version'], $matches ) ) {
			$this->gd_version = (float) $matches[1];
		} else {
			$this->gd_version = (float) substr( $gd_info['GD Version'], 0, 3 );
		}
		return (bool) $this->gd_version;
	}

	/**
	 * Creates the cached directory
	 */
	private function make_cache_directory()
	{
		if( is_dir( $this->options->cached_image_directory ) )
			return true;
		try {
			mkdir( $this->options->cached_image_directory );
		} catch (Exception $e) {
			$this->error( 'There was an error creating the new directory:', $e );
			return false;
		}
		return true;
	}

	/**
	 * Outputs the image src, cached and ready to go.
	 */
	private function serve()
	{
		debug( headers_sent() );
		// $filename = $this->cached_filename;
		// $is_gzipped = false;
		// if( file_exists( $this->cached_filename . '.gz' ) ) {
		// 	$filename = $this->cached_filename . '.gz';
		// 	$is_gzipped = true;
		// }
		// header( 'Content-type: ' . $this->file_mime_type );
		// header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $filename ) ) . ' GMT' );
		// header( 'ETag: ' . md5( $filename ) );
		// if( $is_gzipped ) {
		// 	echo gzdecode( readgzfile( $filename ) );
		// 	// return ob_get_clean();
		// }
	}

	/**
	 * Compresses the image.  Public in case the user just wants to compress, and not cache the image.
	 * 
	 */
	private function compress()
	{
		// if( file_exists( $this->cached_filename . '.gz' ) )
		// 	return true;
		// $image_file = file_get_contents( $this->cached_filename );
		// $gz_data = gzencode( $image_file, 9 );
		// return file_put_contents( $this->cached_filename . '.gz', $gz_data );
		// if( ! file_exists( $_SERVER['DOCUMENT_ROOT'] . '/.htaccess' ) ) {
			/*
			?>
			# BEGIN EXPIRES
			<IfModule mod_expires.c>
				ExpiresActive On
				ExpiresDefault "access plus 10 days"
				ExpiresByType text/css "access plus 1 week"
				ExpiresByType text/plain "access plus 1 month"
				ExpiresByType image/gif "access plus 1 month"
				ExpiresByType image/png "access plus 1 month"
				ExpiresByType image/jpeg "access plus 1 month"
				ExpiresByType application/x-javascript "access plus 1 month"
				ExpiresByType application/javascript "access plus 1 week"
				ExpiresByType application/x-icon "access plus 1 year"
			</IfModule>
			# END EXPIRES

			<IfModule mod_headers.c>
				<FilesMatch "\.(js|css|xml|gz)$">
					Header append Vary Accept-Encoding
				</FilesMatch>
				<FilesMatch "\.(ico|jpe?g|png|gif|swf)$">  
					Header set Cache-Control "public"  
				</FilesMatch>  
				<FilesMatch "\.(css)$">  
					Header set Cache-Control "public"  
				</FilesMatch>  
				<FilesMatch "\.(js)$">  
					Header set Cache-Control "private"  
				</FilesMatch>  
				<FilesMatch "\.(x?html?|php)$">  
					Header set Cache-Control "private, must-revalidate"  
				</FilesMatch>
			</IfModule>
			<?php
			$content = "\n" . '<ifModule mod_gzip.c>' . "\n";
			$content .= "\t" . 'mod_gzip_on Yes' . "\n";
			$content .= "\t" . 'mod_gzip_dechunk Yes' . "\n";
			$content .= "\t" . 'mod_gzip_item_include mime ^image/.*' . "\n";
			$content .= "\t" . 'mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*' . "\n";
			$content .= '</ifModule>' . "\n";
			file_put_contents( $_SERVER['DOCUMENT_ROOT'] . '/.htaccess', $content );
		}
		*/
		// phpinfo();
		return true;
	}

	/**
	 * Fetch the image as a resource and save it into the cache directory.
	 * 
	 * @source http://stackoverflow.com/questions/9839150/image-compression-in-php
	 */
	private function fetch_image()
	{
		$image_size = getimagesize( $this->image_src );
		$image_width = $image_size[0];
		$image_height = $image_size[1];
		$file_mime_as_ext = end( @explode( '/', $this->file_mime_type ) );
		$image_dest_func = 'imagecreate';
		if( $this->gd_version >= 2 )
			$image_dest_func = 'imagecreatetruecolor';
		if( in_array( $file_mime_as_ext, array( 'gif', 'jpeg', 'png' ) ) ) {
			$image_src_func = 'imagecreatefrom' . $this->file_extension;
			$image_create_func = 'image' . $this->file_extension;
		} else {
			// Delegate the image resource to other methods
		}
		$image_src = @call_user_func( $image_src_func, $this->image_src );
		$image_dest = @call_user_func( $image_dest_func, $image_width, $image_height );
		if( $file_mime_as_ext === 'jpeg' ) {
			$background = imagecolorallocate( $image_dest, 255, 255, 255 );
			imagefill( $image_dest, 0, 0, $background );
		} elseif( in_array( $file_mime_as_ext, array( 'gif', 'png' ) ) ) {
			imagealphablending( $image_src, false );
	        imagesavealpha( $image_src, true );
	        imagealphablending( $image_dest, false );
	        imagesavealpha( $image_dest, true );
		}
		imagecopy( $image_dest, $image_src, 0, 0, 0, 0, $image_width, $image_width );
		switch( $file_mime_as_ext ) {
			case 'jpeg':
				$created = imagejpeg( $image_dest, $this->cached_filename, 85 );
				break;
			case 'png':
				$created = imagepng( $image_dest, $this->cached_filename, 8 );
				break;
			case 'gif':
				$created = imagegif( $image_dest, $this->cached_filename );
				break;
			default:
				return false;
				break;
		}
		imagedestroy( $image_src );
		imagedestroy( $image_dest );
		return $created;
	}

	/**
	 * Caches the image.  When using this method, the image will be compressed and then cached
	 * 
	 * @scope Public
	 */
	public function cache( $image )
	{
		if( ! is_string( $image ) )
			$this->error( 'Image source given must be a string.' );
		$this->image_src = strtolower( $image );
		$this->pre_set_class_vars();

		// If the image hasn't been server up at this point, fetch, compress, cache, and return
		// if( ! $this->fetch_image() )
		// 	$this->error( 'Could not copy image resource.' );
		if( ! $this->compress() )
			$this->error( 'Could not compress the image.' );
		return $this->serve();
	}

	/**
	 * Sets up all class variables in one central function.
	 */
	private function pre_set_class_vars()
	{
		$this->set_cached_filename();
		// if( $this->cached_file_exists() )
		// 	$this->error( 'File already exists.  Called at line ' . __LINE__ . '.' );
		$this->set_file_mime_type();
		$this->set_memory_limit();
		$this->set_is_remote();
	}

	/**
	 * Quick and dirty way to see if the file is remote or local.  Deeper checking comes
	 * later if we don't find a compressed & cached version of the file locally.
	 * 
	 * @scope Private
	 */
	private function is_image_local()
	{
		if( file_exists( $this->image_src ) )
			return true;
		$parsed_src = parse_url( $this->image_src );
		if( $_SERVER['HTTP_HOST'] === $parsed_src['host'] )
			return true;
		return false;
	}

	private function set_is_remote()
	{
		$this->is_remote = ! $this->is_image_local();
	}

	private function set_cached_filename()
	{
		$pathinfo = pathinfo( $this->image_src );
		$this->cached_filename = $this->options->cached_image_directory . '/' . md5( basename( $this->image_src ) ) . '.' . $pathinfo['extension'];
	}

	private function cached_file_exists()
	{
		if( file_exists( $this->cached_filename ) )
			return true;
		return false;
	}

	private function set_file_mime_type()
	{
		$image_type = exif_imagetype( $this->image_src );
		if( ! $image_type )
			$this->error( 'The file you supplied isn\'t a valid image.' );
		$this->file_mime_type = image_type_to_mime_type( $image_type );
		$this->file_extension = image_type_to_extension( $image_type, false );
	}

	private function set_memory_limit()
	{
		$this->memory_limit = (int) ini_get('memory_limit');
	}

	/**
	 * Displays an error and kills the script
	 * 
	 * @param String $status The message to be passed to the native `exit()` function
	 */
	private function error( $status = null )
	{
		if( is_null( $status ) )
			$status = 'Unknown Error:';
		exit( $status );
	}


	/**
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Break between version
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */

	/**
	 * @var string The current directory the class is being employed
	
	public $cached_directory;

	/**
	 * @var string The root of the source image
	
	public $original_root;

	/**
	 * @var bool If the directory containing the compressed images has already been created
	
	private $created_dir;

	/**
	 * @var array Options set by the user for the class
	
	private $options;

	/**
	 * @var string|int The original memory limit set by the server
	
	private $pre_memory_limit;

	/**
	 * @todo Add description
	
	static $compressed;

	/**
	 * @todo Add description
	
	private $original_image_src;

	/**
	 * @todo Add description
	
	public $src;

	/**
	 * The primary constructor function.  It sets up the environment and returns the class object
	 * 
	 * @param string $directory The base directory in which images are found - will soon be deprecated
	 * @param bool $create_new_directory Whether or not to create a new directory - will soon be deprecated
	 * @param object|array $opts The options array set by the user, or if none, defaults
	 * 
	 * @return self Returns the class object for contiuance
	
	public function __construct( $directory = null, $create_new_directory = true, $options = array() )
	{
		if ( is_null( $directory ) )
			$directory = dirname( dirname( __DIR__ ) );

		if( is_object( $options ) )
			$options = (array) $options;

		$defaults = array(
			'search_directory' => $directory,
			'quality' => 90,
			'directory' => 'compressed', // For backwards compatibility, but will soon be deprecated
			'directory_name' => 'compressed',
			'create_cached_directory' => true
		);

		$this->original_root = $directory;
		$this->options = array_merge( $defaults, $options );

		if( isset( $defaults['create_cached_directory'] ) && $defaults['create_cached_directory'] )
			return $this->createDirectory( $this->options['directory_name'] );

		if ( ! $create_new_directory )
			return $this;
		return $this->createDirectory();
	}

	/**
	 * Creates the directory in which the cached images will be stored
	 * 
	 * @return self Returns the class object for contiuance
	
	public function createDirectory( $directory_name = null )
	{
		if( ! $directory_name || is_null( $directory_name ) )
			$directory_name = $this->options['directory_name'];

		if( $this->created_dir && $this->cached_directory && is_dir( $this->cached_directory ) )
			return $this;

		$full_path = $this->original_root . '/' . $directory_name;
		if ( ! is_dir( $full_path ) ) {
			try {
				mkdir( $full_path, 0755 );
			} catch( \Exception $e ) {
				return $this->throwError( 'There was an error creating the new directory:', $e );
			}
		}
		$this->cached_directory = $full_path;
		$this->created_dir = true;
		return $this;
	}

	/**
	 * Reads the image and in turn compresses, relocations, and returns a cached copy
	 * 
	 * @param string $src Either a local image, with the relative or absolute directory path set during instatiation, or a URL
	 * 
	 * @return array Information on the newly compressed image, including the new source with modtime query, the height, and the width
	 
	public function compress( $source )
	{
		// Set variables for this method
		$this->original_image_src = $source;
		$filename = basename( $source );
		$localfile = $this->cached_directory . '/' . $filename;

		// Check if the image is already downloaded and cached
		if( $this->isInDirectory( $localfile ) )
			return $this->outputSource( $this->makeSource( $localfile ), $this->getModTime( $localfile ) );

		// If the image is derived from a filepath, return the URL
		if( $this->isInDirectory( $source ) )
			$source = $this->makeSource( $source );

		if( $this->isURL( $source ) && ! $this->is404( $source ) ) {
			$file_resource = file_get_contents( $source );
			try {
				file_put_contents( $localfile, $file_resource );
			} catch( \Exception $e ) {
				return $this->throwError( 'There was an error downloading the image:', $e );
			}
		} else {
			return $this->show404();
		}
		if( ! $this->isInDirectory( $localfile ) )
			return $this->throwError( 'There was an error downloading the image.  Try again.' );

		$this->src = $this->makeSource( $localfile );
		$mime = $this->getMimeType( $localfile );
		try {
			$this->increaseMemoryLimit();
			switch( $mime ) {
				case 'image/jpeg' :
				case 'image/jpg' :
					$imageResource = imagecreatefromjpeg( $localfile );
				break;
				case 'image/png' :
					$imageResource = imagecreatefrompng( $localfile );
				break;
				case 'image/gif' :
					$imageResource = imagecreatefromgif( $localfile );
				break;
			}
			$this->resetMemoryLimit();
		} catch( \Exception $e ) {
			$this->throwError( 'There was an error processing your image.', $e );
		}
		imagejpeg( $imageResource, $localfile, $this->options['quality'] );
		return $this->outputSource( $this->makeSource( $localfile ), $this->getModTime( $localfile ) );
	}

	private function outputSource( $source, $modtime )
	{
		$outputSource = $source . '?' . $modtime;
		$this->src = $outputSource;
		$this->setHeaders();
		return $outputSource;
	}

	private function setHeaders()
	{
		$expiryDate = date( 'D, d M Y H:i:s e', strtotime() + ( 3600 * 24 * 30 ) );
		header( 'Cache-Control: cache, must-revalidate' );
		header( 'Expires: ' . $expiryDate );
		header( 'HTTP/1.0 304 Not Modified' );
		$GLOBALS['http_response_code'] = 304;
	}

	private function getModtime( $file )
	{
		if( ! $this->isInDirectory( $file ) )
			return null;
		return filemtime( $file );
	}

	/**
	 * Returns the filename basename without the extension, path, or URL
	 * 
	 * @param string $file The name of the file
	 * 
	 * @return string The name of the file sans extension
	
	private function getFilename( $file )
	{
		$pathinfo = pathinfo( $file );
		$filename = $pathinfo['filename'];
		return $filename;
	}

	private function isURL( $source )
	{
		if( filter_var( $source, FILTER_VALIDATE_URL ) )
			return true;
		return false;
	}

	private function isInDirectory( $source )
	{
		if( is_file( $source ) && file_exists( $source ) )
			return true;
		return false;
	}

	private function is404( $source )
	{
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $source );
		curl_setopt( $ch, CURLOPT_NOBODY, true );
		curl_exec( $ch );
		if( curl_getinfo( $ch, CURLINFO_HTTP_CODE ) == '404' )
			return $this->show404();
		return false;
	}

	private function show404()
	{
		return $this->src = 'http://placehold.it/500x300&text=Image Not Found';
	}

	/**
	 * Checks if the image is on the server currently being utilized
	 * 
	 * @param string $src 
	 * 
	 * @return bool Whether or not the image is local
	
	private function isLocal( $src )
	{
		$cururl = strtolower( reset( explode( '/', $_SERVER['SERVER_PROTOCOL'] ) ) ) . '://' . $_SERVER['SERVER_NAME'] . '/';
		if( strstr( $cururl, $src ) )
			return true;
		return false;
	}

	/**
	 * Creates an absolute URL based on the current protocol and location of the image on the server
	 * 
	 * @param string $dir 
	 * 
	 * @return string The 
	
	private function makeSource( $dir )
	{
		$protocol = $_SERVER[ 'SERVER_PROTOCOL' ];
		$protocol_array = explode( '/', $protocol );
		$host = $_SERVER[ 'SERVER_NAME' ];

		$current_url_tmp = reset( $protocol_array ) . '://' . $host;
		$current_url = strtolower( $current_url_tmp );

		$base = $_SERVER['DOCUMENT_ROOT'];
		if( $base == '/' ) {
			$localpath = substr( $dir, 1 );
		} else {
			$localpath = str_replace( $base, '', $dir );
		}
		return $current_url . $localpath;
	}

	private function increaseMemoryLimit()
	{
		if( isset( $this->pre_memory_limit ) )
			return;
		$memory_limit = ini_get( 'memory_limit' );
		$int_memory_limit = intval( $memory_limit );
		if( $int_memory_limit >= 128 )
			return;
		$this->pre_memory_limit = $int_memory_limit;
		ini_set( 'memory_limit', '128M' );
	}

	private function resetMemoryLimit()
	{
		if( ! isset( $this->pre_memory_limit ) )
			return;
		ini_set( 'memory_limit', $this->pre_memory_limit . 'M' );
	}

	private function getMimeType( $image )
	{
		$info = getimagesize( $image );
		return $info['mime'];
	}

	/**
	* A basic debug function for printing output - good if not using unit testing
	* 
	* @param mixed $a The input variable
	
	private function debug( $a )
	{
		echo '<pre>';
		print_r($a);
		echo '</pre><hr>';
	}

	/**
	 * Memory management function that releases an object's value
	 * 
	 * @param mixed $a Any input that is to be released
	
	private function release( $a )
	{
		if( ! $a || is_null( $a ) )
			return;
		foreach( $a as $b ) {
			unset( $b );
		}
		unset( $a );
	}
	*/
}
