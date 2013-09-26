<?php
	// header("Pragma: private"); 
	require 'functions.php';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<?php 
		// loadImage('500x300.gif');
		$source_img = '500x300.gif';
		$destination_img = '500x300-compressed.jpg';
		$d = compress($source_img, $destination_img);
		debug($d);
	?>
		<img src="<?php echo $d['src']; ?>" height="<?php echo $d['height']; ?>" width="<?php echo $d['width']; ?>">
	
</body>
</html>