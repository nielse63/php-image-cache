<?php require '../ImageCache.php'; ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Image Cache Test</title>
</head>
<body>
	<?php 
		$image = new ImageCache('http://example.com/test/images', dirname(__FILE__) . '/images');
	?>
		<?php $d = $image->compress('500x300.gif'); ?>
		<img src="<?php echo $d['src']; ?>" height="<?php echo $d['height']; ?>" width="<?php echo $d['width']; ?>">
		<?php $d = $image->compress('200x125.gif'); ?>
		<img src="<?php echo $d['src']; ?>" height="<?php echo $d['height']; ?>" width="<?php echo $d['width']; ?>">
	
</body>
</html>