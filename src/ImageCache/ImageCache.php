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

namespace ImageCache;

class ImageCache
{
	/**
	 * Stores the image source given for reference
	 */
	private $image_source;

	/**
	 * Allow the user to set the options for the setup
	 */
	public $options;

	/**
	 * Constructor. Passes $options as a parameter
	 */
	public function __construct( $options = array() )
	{
		$defaults = array(
			'echo' => true, 				// Determines whether the resulting source should be echoed or returned
			'cache_time' => ( 3600 * 48 ), 	// How long the image should be cached for.  Defaults to 2 days.
			'keep_local' => true, 			// If the file is remote, this option allows the user to download and store it locally.
			'cached_image_directory' => dirname( __FILE__ ) . '/image-cache'
		);
		$this->options = (object) array_merge( $defaults, $options );
		$this->make_cache_directory();
		return $this;
	}

	private function make_cache_directory()
	{
		if( ! $this->options->keep_local )
			return;
		if( is_dir( $this->options->cached_image_directory ) )
			return;
		mkdir( $this->options->cached_image_directory );
	}

	/**
	 * Outputs the image src, cached and ready to go.
	 */
	public function serve( $src )
	{

	}

	/**
	 * Caches the image.  When using this method, the image will be compressed and then cached
	 * 
	 * @scope Public
	 */
	private function cache()
	{

	}

	/**
	 * Compresses the image.  Public in case the user just wants to compress, and not cache the image.
	 * 
	 * @scope Public
	 */
	private function compress()
	{

	}

	/**
	 * Determines whether the image is remote or being pulled from a local directory
	 * 
	 * @scope Private
	 */
	private function is_image_local()
	{

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
	
	private $original_image_source;

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
		$this->original_image_source = $source;
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

	private function throwError( $message = null, $error = null ) {
		if( is_null( $message ) )
			$message = 'Unknown Error:';
		echo $message . "\n";
		return $this->debug( $error );
	}
	*/
}
