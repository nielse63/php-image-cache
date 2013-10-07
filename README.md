# Image Cache

Image Cache is a very simple PHP class that accepts an image source and will compress and cache the file, move it to a new directory, and returns the new source for the image.

## Installation

You can either install the script manually using the `require` method:

```
require 'ImageCache.php';
```

Or (preferred) you can install the script with <a href="http://getcomposer.org" target="_blank">Composer</a>.

Install Composer in the root directory of your project, and create a `composer.json` file.

In your `composer.json` file:

```
	{
		"require" : {
			"nielse63/phpimagecache": "dev-master"
		}
	}
```

This is currently the first release of Image Cache, so in this example you'll be able to update your script with any updates made to Image Cache.  If, however, you don't want access to any potential updates, remove the tilda form the "version" value.

Navigate to your project root and run the `install` command of `composer.phar`.

```
$ php composer.phar install
```

From there, include the `vendor/autoload.php` file in your project, and initilize the class as normal.

## Testing

To test the script, install the full project on your server and navigate to the test directory. This ccontains an index file with example functions and an image directory with several images.  Run the `test/index.php` file to ensure that the script is compressing and compiling the sample images.  If working correctly, a new directory, "compressed" will appear in your images folder.

## Deploying

Include the script in your project either with Composer or via the manual `require` method and create a new instance of the class, using the appropriate parameters if needed:

`$image = new ImageCache();`

Possible parameters include:

```
$image = new ImageCache(
	$filebase = '', $dir = null, $create_dir = true, $opts = array()
);
/**
 * @param $filebase (string) - The base URL that will be included in the final output for the image source; used if image source is an absolute URL
 * @param $dir (string/null) - The base directory that houses the image being compressed
 * @param $create_dir (bool) - Whether or not to create a new directory for the compressed images
 * @param $opts (array) - An array of available options that the user can include to the overwrite default settings
 */
```

Then compress the image by calling it by it's filename:

`$compressed = $image->compress('image.png');`

This will return an array of information on the compressed image, including the source of the compressed image, the height, and the width.  It can be included in your PHP file as such:

```
<img src="<?php echo $compressed['src']; ?>" height="<?php echo $compressed['height']; ?>" width="<?php echo $compressed['width']; ?>">
```

## What's Next

Future development changes include:
<ul>
	<li>Integration with Tim Thumb and other image cropping libraries</li>
	<li>Increase browser capabilities (and further testing down to IE 8)</li>
	<li>Fixing the issue with 304 headers being ignored</li>
	<li>More options, better documentation, more forms of output, etc.</li>
</ul>

## Contributing changes

Contributing to the project would be a massive help in maintaining and extending the script.  It has a lot of potential, and any help would be awesome.

If you're interested in contributing, <a href="https://github.com/nielse63/image-cache/pulls" taret="_blank">issue a pull request</a> on Github or email me directly at <a href="mailto:erik@312development.com">erik@312development.com</a>.

## License

Create Commons Attribution Lisence:

<a href="http://freedomdefined.org/Licenses/CC-BY">http://freedomdefined.org/Licenses/CC-BY</a>

### .gitignore

Allows for files to be ignored in builds.

### .travis.yml

This is used on <a href="http://travis-ci.org" taret="_blank">travis-ci.org</a> for continuous integration testing.