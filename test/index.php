<?php

date_default_timezone_set('UTC'); // to avoid PHP warning
require '../src/ImageCache/ImageCache.php';

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Image Cache Test</title>
</head>
<body>
	<?php 
		// I've defined the base URL to be included in the source, as well as the images directory
		$image = new ImageCache\ImageCache('http://test/test/images', dirname(__FILE__) . '/images');
		// Include the filename without the path information
		$d = $image->compress('500x300.gif');
	?>
		<img src="<?php echo $d['src']; ?>" height="<?php echo $d['height']; ?>" width="<?php echo $d['width']; ?>">
	
</body>
</html>