# Image Cache

Image Cache is a very simple PHP class that accepts an image source and will compress and cache the file, move it to a new directory, and returns the new source for the image.

## Installation

#### Composer

#### Manual Installation

## Testing

To test the script, install the full project on your server and navigate to the test directory. This ccontains an index file with example functions and an image directory with two images.  Run the `test/index.php` file to ensure that the script is compressing and compiling the sample images.  If working correctly, a new directory, "compressed" will appear in your images folder.

## Deploying

Include the script in your project either with Composer or via the manual `require` method and create a new instance of the class, using the appropriate parameters if needed:

`
$image = new ImageCache();
`

Possible parameters include:

`
$image = new ImageCache(
	$filebase = '', $dir = null, $create_dir = true, $opts = array()
);
/**
 * @param $filebase (string) - The base URL that will be included in the final output for the image source; used if image source is an absolute URL
 * @param $dir (string/null) - The base directory that houses the image being compressed
 * @param $create_dir (bool) - Whether or not to create a new directory for the compressed images
 * @param $opts (array) - An array of available options that the user can include to the overwrite default settings
 */
`

Then compress the image by calling it by it's filename:

`
$compressed = $image->compress('image.png');
`

This will return an array of information on the compressed image, including the source of the compressed image, the height, and the width.  It can be included in your PHP file as such:

`
<img src="<?php echo $compressed['src']; ?>" height="<?php echo $compressed['height']; ?>" width="<?php echo $compressed['width']; ?>">
`

## Contributing changes

Just issue a pull request and push it forward.  It could definitely use work.

## License

Create Commons Attribution Lisence:

<a href="http://freedomdefined.org/Licenses/CC-BY">http://freedomdefined.org/Licenses/CC-BY</a>