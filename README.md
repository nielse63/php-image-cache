# Image Cache v. 1.0.0

Image Cache is a very simple PHP class that accepts an image source and will compress and cache the file, move it to a new directory, and returns the new source for the image.

## Current Status

[![Build Status](https://travis-ci.org/nielse63/php-image-cache.png?branch=master)](https://travis-ci.org/nielse63/php-image-cache)

Employing <a href="http://travis-ci.org" taret="_blank">travis-ci.org</a> for continuous integration testing and assurance of code validity.

## Installation

Install <a href="http://getcomposer.org" target="_blank">Composer</a> by opening Terminal and navigating to the directory in which you'd like to install Image Cache.

Download Composer:

```bash
curl -sS https://getcomposer.org/installer | php
```

Create a `composer.json` file:

```json
	{
		"require" : {
			"nielse63/phpimagecache": "dev-master"
		}
	}
```

Navigate to your project root and run the `install` command.

```bash
$ php composer.phar install
```

From there, include the `vendor/autoload.php` file in your project, and initialize the class as normal.

More information on installing and using Composer can be found at <a href="http://getcomposer.org" target="_blank">getcomposer.org</a>, and dependency information on the package can be found at <a href="https://packagist.org/packages/nielse63/phpimagecache" target="_blank">packagist.org</a>.

## Testing

### Manual Testing

To test the script manually by receiving visual output, setup a virtual host and load `demo/index.php` in your browser.  Three examples are set in that file: the original image called from an outside source; a cached example referencing the outside source via an absolute URL; and an internal source referencing a file path.

Using Chrome Developer Tools you can see the difference in load times between the external source (non-cached image) and the internally stored and cached image.

### Unit Testing

Some extremely basic unit tests are included with the script and can be run using <a href="http://phpunit.de/" target="_blank">PHP Unit</a>.  I'm working on continuing to build up these tests and would more than welcome any contributions to the tests.

To execute the tests in a bundled script (along with rebuilding the docs), clone the repository, navigate to the root of the repo in terminal, and execute:

```bash
$: sh build
```

Assuming you have the `phpunit` and `phpdoc` commands intalled, the tests will pass and docs will be rebuilt.

## What's Next

<a href="https://github.com/nielse63/php-image-cache/blob/master/src/ImageCache/ImageCache.php">See the source</a> for a full to do list of changes that I wish to accomplish moving forward.

## Contributing

Contributing to the project would be a massive help in maintaining and extending the script. The module is being used on a larger scale than I initially imagined, and continuing to maintain it is becoming a little time consuming for just me.

If you're interested in contributing, <a href="https://github.com/nielse63/php-image-cache/pulls" taret="_blank">issue a pull request</a> on Github or email me directly at <a href="mailto:erik@312development.com">erik@312development.com</a>.

For any issues found or extensions you'd like to see, feel free to <a href="https://github.com/nielse63/php-image-cache/issues" taret="_blank">submit an issue ticket</a> so we can start a discussion about the viability of the problem and how it can be resolved.

## License

Creative Commons Attribution Lisence:

<a href="http://freedomdefined.org/Licenses/CC-BY">http://freedomdefined.org/Licenses/CC-BY</a>

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/nielse63/php-image-cache/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

