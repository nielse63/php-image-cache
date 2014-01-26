<?php

date_default_timezone_set('UTC');
require '../src/ImageCache/ImageCache.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Image Cache Test</title>
</head>
<body>
	<?php 
		// I've defined the base URL to be included in the source, as well as the images directory
		$image = new ImageCache\ImageCache( dirname(__FILE__) . '/images' );

		// Compress the image with either the full URL source, or its location on the server
		$d = $image->compress( 'http://placehold.it/500x300.gif');
	?>
		<img src="<?php echo $d['src']; ?>" height="<?php echo $d['height']; ?>" width="<?php echo $d['width']; ?>">
	
</body>
</html>