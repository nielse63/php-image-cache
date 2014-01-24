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
        $this->assertClassHasAttribute( 'root', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'src_root', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'created_dir', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'opts', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'base', 'ImageCache\ImageCache' );
        $this->assertClassHasAttribute( 'pre_memory_limit', 'ImageCache\ImageCache' );
    }
}
