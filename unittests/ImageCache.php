<?php

class ImageCacheTest extends PHPUnit_Framework_TestCase
{
	public function canFindFile()
    {
        $this->assertFileExists('../src/ImageCache/ImageCache.php');
    }
}