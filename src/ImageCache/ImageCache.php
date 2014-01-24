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
 */

namespace ImageCache;

class ImageCache
{
    private $root;              /** @string  */
    private $src_root;          /** @string  */
    private $created_dir;       /** @bool  */
    private $opts;              /** @array  */
    private $base;              /** @string  */
    private $pre_memory_limit;  /** @string; gets the users memory limit */

    /**
     * The primary constructor function.  It sets up the environment and returns the class object
     * 
     * @param string|null $dir 
     * @param bool $create_dir 
     * @param array $opts 
     * 
     * @return self Returns the class object for contiuance
     */
    public function __construct( $dir = null, $create_dir = true, $opts = array() )
    {
        $defaults = array(
            'quality' => 90,
            'directory' => 'compressed'
        );

        if (is_null($dir)) {
            $dir = dirname(dirname(__DIR__));
        }

        $this->root = $dir;
        $this->opts = array_merge( $defaults, $opts );

        if ( ! $create_dir )
            return $this;
        return $this->createDirectory();
    }

    /**
     * Creates the directory in which the cached images will be stored
     * 
     * @return self Returns the class object for contiuance
     */
    public function createDirectory()
    {
        if ( ! is_dir( $this->root . '/' . $this->opts['directory'] ) ) {
            try {
            	$cdir = $this->root . '/' . $this->opts['directory'];
		        mkdir($cdir, 0777);
		        $this->root = $cdir;
		        $this->created_dir = true;
		        return $this;
            } catch (\Exception $e) {
                echo 'There was an error creating the new directory:' . "\n";
                $this->debug($e);
            }
        }
        $this->src_root = $this->root;
        $this->root .= '/' . $this->opts['directory'];
        $this->created_dir = true;
        return $this;
    }

    /**
     * Reads the image and in turn compresses, relocations, and returns a cached copy
     * @param string $src 
     * 
     * @return array Information on the newly compressed image, including the new source with modtime query, the height, and the width
     */ 
    public function compress( $src )
    {
        if( strpos( $src, 'http' ) !== false ) {
	        if( ! $this->isLocal( $src) ) {
	        	$info = pathinfo( $src );
	        	$filename = $info['basename'];
	        	$localfile = $this->src_root . '/' . $filename;
	        	file_put_contents( $localfile, file_get_contents( $src ) );
	        } else {
	        	$localfile = $this->src_root . '/' . $filename;
	        }

	        if( ! file_exists( $localfile ) ) {
	        	echo 'The image file could not be located';
	        }
        } else {
	        if( ! file_exists( $src ) ) {
	        	echo 'The image file could not be located';
	        	exit;
	        }
	        $localfile = $src;
        }
        $fileinfo = pathinfo( $localfile );
        $filename = $fileinfo['basename'];
        if( file_exists( $this->root . '/' . $filename ) ) {
	        $info = getimagesize( $this->root . '/' . $filename );
	    	$src = $this->makeSource( $this->root . '/' . $filename );
        	$out = array(
        		'src' => $src,
        		'width' => $info[0],
        		'height' => $info[1]
    		);
        	return $out;
        }

        $info = getimagesize( $localfile );
        try {
	        $this->allocateMemory( 'set' );
	        switch( $info['mime'] ) {
	        	case 'image/jpeg' :
	        		$image = imagecreatefromjpeg( $localfile );
	        		break;
	        	case 'image/gif' :
	        		$image = imagecreatefromgif( $localfile );
	        		break;
	        	case 'image/png' :
	        		$image = imagecreatefrompng( $localfile );
	        		break;
	        }
	        $this->allocateMemory( 'reset' );
        } catch(\Exception $e) {
        	echo 'There was an error processing your image:' . "\n";
        	$this->debug($e);
        }
        if( $this->created_dir ) {
        	$newlocation = $this->root . '/' . $filename;
        } else {
        	$newlocation = $this->src_root . '/' . $filename;
        }
        imagejpeg( $image, $newlocation, $this->opts['quality'] );
        $newinfo = getimagesize( $newlocation );
        $modtime = filemtime( $newlocation );
	    $src = $this->makeSource( $this->root . '/' . $filename );
        $out = array(
        	'src' => $src,
        	'width' => $newinfo[0],
        	'height' => $newinfo[1]
    	);
    	return $out;
    }

    /**
     * Returns the filename basename without the extension, path, or URL
     * @param string $file The name of the file
     * 
     * @return string The name of the file sans extension
     */
    public function getFilename( $file )
    {
        $pathinfo = pathinfo( $file );
        $filename = $pathinfo['filename'];
        return $filename;
    }

    /**
     * Checks if the image is on the server currently being utilized
     * @param string $src 
     * @return bool Whether or not the image is local
     */
    private function isLocal( $src )
    {
    	$cururl = strtolower( reset( explode( '/', $_SERVER['SERVER_PROTOCOL'] ) ) ) . '://' . $_SERVER['SERVER_NAME'] . '/';
    	if( strstr( $cururl, $src ) )
    		return true;
    	return false;
    }

    /**
     * Creates an absolute URL based on the current protocol and location of the image on the server
     * @param string $dir 
     * 
     * @return string The 
     */
    private function makeSource( $dir )
    {
        $protocol = $_SERVER[ 'SERVER_PROTOCOL' ];
        $protocol_array = explode( ':', $protocol );
        $host = $_SERVER[ 'SERVER_NAME' ];

        $current_url_tmp = reset( $protocol_array ) . '://' . $host;
        $current_url = strtolower( $current_url_tmp );

        $base = $_SERVER['DOCUMENT_ROOT'];
        if( $base == '/' ) {
            $localpath = substr( $dir, 1 );
        } else {
            $localpath = str_replace( $base, '', $dir );
        }
        return $cururl . $localpath;
    }

    /**
     * Allocates memory usage to ensure that larger images can be handled without timing out
     * @param string $method The method being used to either "set" the new memory limit, or "reset" it back to the previous value
     */
    private function allocateMemory( $method )
    {
        if ( $method === 'set' ) {
            $amt = ini_get( 'memory_limit' );
            $this->pre_memory_limit = $amt;

            if ( intval( $amt ) < 128 ) {
                ini_set( 'memory_limit', '128M' );
            }
        } else if ( $method === 'reset' ) {
            $orig_mem = $this->pre_memory_limit;
            ini_set( 'memory_limit', $orig_mem . 'M' );
        }
    }

    /**
     * Determines if a cached version of the input image already exists
     * @param string $img The basename of the image we're checking against
     * 
     * @return bool
     */
    private function checkExists( $img )
    {
        if ( file_exists( $this->root . '/' . $img ) ) {
            $info = getimagesize( $this->root . '/' . $img );
            $path = pathinfo( $this->root . '/' . $img );
            $exp = explode( '/', $path['dirname'] );
            $src = '/' . end( $exp ) . '/' . $path['basename'];
            $src .= '?modtime=' . filemtime( $this->root . '/' . $path['basename'] );
            $out = array(
                'src' => $this->base . $src,
                'width' => $info[0],
                'height' => $info[1]
            );
            $this->setHeaders( false );
            return $out;
        }
        return false;
    }

    /**
     * A basis debug function for printing output - good if not using unit testing
     * @param mixed $a The input variable
     */
    private function debug( $a )
    {
        echo '<pre>';
        print_r($a);
        echo '</pre><hr>';
    }
}
