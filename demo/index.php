<?php require '../src/ImageCache/ImageCache.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Image Cache Test</title>
</head>
<body>
	<?php 
		// I've defined the base URL to be included in the source, as well as the images directory
		$image = new ImageCache\ImageCache();

		// Compress the image with either the full URL source, or its location on the server
		// $sample_one = $image->compress( 'http://placehold.it/500x300.gif' );
		// $sample_two = $image->compress( dirname( __FILE__ ) . '/images/500x300.gif' );
		// <img src="http://placehold.it/500x300.gif">
		// <img src="<?php echo $sample_one; ">
		// <img src="<?php echo $sample_two; ">
	?>
	
</body>
</html>