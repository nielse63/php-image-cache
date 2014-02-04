<?php

require './ImageCache.php';

class ImageCacheTest extends PHPUnit_Framework_TestCase
{

	public function testCanFindFile()
    {
        $this->assertFileExists( '../src/ImageCache/ImageCache.php' );
    }

    public function testTestFileExists()
    {
    	$this->assertTrue( file_exists( 'ImageCache.php' ) );
    }

    /**
     * @depends testTestFileExists
     */
	public function testClassDoesHaveAttributes()
    {
        $this->assertClassHasAttribute( 'cached_directory', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'original_root', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'created_dir', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'options', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'compressed', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'original_image_source', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'src', 'ImageCache\ImageCache' );
    }
}
