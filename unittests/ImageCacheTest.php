<?php

chdir( dirname( __FILE__ ) );
require_once '../src/ImageCache/ImageCache.php';

class ImageCacheTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @author nielse63
	 */
	public function testCanFindFile()
	{
		$this->assertFileExists( '../src/ImageCache/ImageCache.php' );
	}

    /**
	 * @author nielse63
     * @depends testCanFindFile
     */
	public function testClassDoesHaveAttributes()
	{
		$this->assertClassHasAttribute( 'image_src', 'ImageCache' );
		$this->assertClassHasAttribute( 'is_remote', 'ImageCache' );
		$this->assertClassHasAttribute( 'options', 'ImageCache' );
		$this->assertClassHasAttribute( 'cached_image_directory', 'ImageCache' );
		$this->assertClassHasAttribute( 'cached_filename', 'ImageCache' );
		$this->assertClassHasAttribute( 'gd_version', 'ImageCache' );
		$this->assertClassHasAttribute( 'memory_limit', 'ImageCache' );
		$this->assertClassHasAttribute( 'file_mime_type', 'ImageCache' );
		$this->assertClassHasAttribute( 'file_extension', 'ImageCache' );
		$this->assertClassHasAttribute( 'local_image_src', 'ImageCache' );
		$this->assertClassHasAttribute( 'src_filesize', 'ImageCache' );
		$this->assertClassHasAttribute( 'cached_filesize', 'ImageCache' );
	}

	/**
	 * @author nielse63
     * @depends testCanFindFile
	 */
	public function testCanRunImageCache()
	{
		$imagecache = new ImageCache();
		$this->assertTrue( $imagecache->can_run_image_cache() );
	}
}
