<?php

/**
 * The primary class file for PHP Image Compressor & Caching
 *
 * This file is to be used in any PHP project that requires image compression
 *
 * @package PHP Image Compressor & Caching
 * @author Erik Nielsen (erik@312development.com) (http://312development.com)
 *
 */

namespace ImageCache;

class ImageCache
{
    private $root; /** @string  */
    private $src_root; /** @string  */
    private $created_dir; /** @bool  */
    private $opts; /** @array  */
    private $base; /** @string  */
    private $pre_memory_limit; /** @string; gets the users memory limit */

    public function __construct( $dir = null, $create_dir = true, $opts = array() )
    {
        /**
         * @param $dir (string/null) - The base directory that houses the image being compressed
         * @param $create_dir (bool) - Whether or not to create a new directory for the compressed images
         * @param $opts (array) - An array of available options that the user can include to the overwrite default settings; will be included later
         */

        $defaults = array(
            'quality' => 90,
            'directory' => 'compressed'
        );

        if (is_null($dir)) {
            $dir = dirname(dirname(__DIR__));
        }

        $this->root = $dir;
        // $this->base = $filebase;
        $this->opts = array_merge( $defaults, $opts );

        if (! $create_dir) {
            return $this;
        }
        
        $this->createDirectory();
        return $this;
    }

    public function createDirectory()
    {
        /**
         * 
         * Creates a new directory, if so requested to by the constructor function
         * 
         * @return $this (obj) - Returns the class for continuance
         */

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

    public function compress($src)
    {
        /**
         * 
         * The primary function - reads the image, the compresses, moves, and returns a cached copy
         * 
         * @param $src (string) - The image that is to be compressed
         * @return $out (array) - Information on the newly compressed image, including the new source with modtime query, the height, and the width
         */

        if(strpos($src, 'htt') !== false) {
	        if(!$this->isLocal($src)) {
	        	$info = pathinfo($src);
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
	    	$src = $this->makesource( $this->root . '/' . $filename );
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
	    $src = $this->makesource( $this->root . '/' . $filename );
        $out = array(
        	'src' => $src,
        	'width' => $newinfo[0],
        	'height' => $newinfo[1]
    	);
    	return $out;
    }

    private function isLocal($src)
    {
    	$cururl = strtolower(reset(explode('/', $_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['SERVER_NAME'] . '/';
    	if( strstr($cururl, $src) )
    		return true;
    	return false;
    }

    private function makesource($dir) {
    	$cururl = strtolower(reset(explode('/', $_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['SERVER_NAME'];
    	$base = $_SERVER['DOCUMENT_ROOT'];
    	$localpath = str_replace($base, '', $dir);
    	return $cururl . $localpath;
    }

    private function allocateMemory($method)
    {
        /**
         * 
         * Allocates additional memory to the program if needed
         * 
         * @param $method (string) - Either 'set' or 'reset' - if anything else, will return false
         * 
         */

        if ($method === 'set') {
            $amt = ini_get('memory_limit');
            $this->pre_memory_limit = $amt;

            if (intval($amt) < 128) {
                ini_set('memory_limit', '128M');
            }
        } else if ($method === 'reset') {
            $orig_mem = $this->pre_memory_limit;
            ini_set('memory_limit', $orig_mem . 'M');
        }
    }

    private function checkExists($img)
    {
        /**
         * 
         * Checks if the compressed version of the image already exists
         * 
         * @param $img (string) - The basename of the image we're checking for
         * @return $out (array) - Information on the newly compressed image, including the new source with modtime query, the height, and the width
         * @return false (bool) - Returns false if the image doesn't exist
         */

        if (file_exists($this->root . '/' . $img)) {
            $info = getimagesize($this->root . '/' . $img);
            $path = pathinfo($this->root . '/' . $img);
            $exp = explode('/', $path['dirname']);
            $src = '/' . end($exp) . '/' . $path['basename'];
            $src .= '?modtime=' . filemtime($this->root . '/' . $path['basename']);
            $out = array(
                'src' => $this->base . $src,
                'width' => $info[0],
                'height' => $info[1]
            );
            $this->setHeaders(false);
            return $out;
        }
        return false;
    }

    public function getFilename($file)
    {
        /**
         * 
         * Just grabs the filename without the file extension
         * 
         * @param $file (string) - The filename whose name we want
         * @return $filename (string) - The filename without the extension
         */

        $pathinfo = pathinfo($file);
        $filename = $pathinfo['filename'];
        return $filename;
    }

    private function debug($a)
    {
        /**
         * 
         * Basic debug function
         * 
         */

        echo '<pre>';
        print_r($a);
        echo '</pre><hr>';
    }
}
