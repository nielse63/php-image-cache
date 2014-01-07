<?php

class ImageCacheTest extends PHPUnit_Framework_TestCase
{
	public function testFailure()
    {
        $this->assertFileExists('../src/ImageCache/ImageCache.php');
    }
}