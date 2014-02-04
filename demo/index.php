<?php

date_default_timezone_set('UTC');
require '../src/ImageCache/ImageCache.php';

function debug( $a )
{
	echo '<pre>';
	print_r( $a );
	echo '</pre><hr>';
}

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
		$image = new ImageCache\ImageCache( dirname( __FILE__ ) . '/images', true, array( 'directory_name' => 'cached' ) );

		// Compress the image with either the full URL source, or its location on the server
		$image->compress( 'http://placehold.it/500x300.gif' );
		$image->compress( dirname( __FILE__ ) . '/images/500x300.gif' );
	?>
		<img src="http://placehold.it/500x300.gif">
		<img src="<?php echo $image->src; ?>">
		<img src="<?php echo $image->src; ?>">
	
</body>
</html>