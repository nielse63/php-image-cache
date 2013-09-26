<?php

function debug($a) {
	echo '<pre>';
	print_r($a);
	echo '</pre><hr>';
}

function loadImage($src) {
	header("Cache-Control: private, max-age=10800, pre-check=10800"); 
	header("Pragma: private"); 
	// Set to expire in 2 days 
	header("Expires: " . date(DATE_RFC822,strtotime(" 2 day"))); 
	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){ 
	  // if the browser has a cached version of this image, send 304 
	  header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304); 
	  exit; 
	} 


	// Generate an image below either using GD or a file reader such as: 
	// readfile(), fread(), file_get_contents(), etc. 

	$info = pathinfo($src);
	// debug($info);
	switch($info["extension"]){ 
	    case "jpg": 
	        $mime = "image/jpeg"; 
	        break; 
	    case "gif": 
	        $mime = "image/gif"; 
	        break; 
	    case "png": 
	        $mime = "image/png"; 
	        break; 
	} 
	header("content-type: $mime"); 
	// readfile($src);

	echo '<img src="http://test/' . $src . '?modtime=' . filemtime($_SERVER['DOCUMENT_ROOT'] . '/' . $src) . '">';
}

function setHeaders() {
	header("Cache-Control: private, max-age=10800, pre-check=10800"); 
	header("Pragma: private"); 
}

function compress($source, $destination, $quality = 90) {
	$source = $source . '?modtime=' . filemtime($_SERVER['DOCUMENT_ROOT'] . '/' . $destination);
	$info = getimagesize($source);
	if ($info['mime'] == 'image/jpeg') 
		$image = imagecreatefromjpeg($source); 
	elseif ($info['mime'] == 'image/gif') 
		$image = imagecreatefromgif($source); 
	elseif ($info['mime'] == 'image/png') 
		$image = imagecreatefrompng($source); 
	imagejpeg($image, $destination, $quality);
	$info = getimagesize($destination);
	return array('src' => $destination . '?modtime=' . filemtime($_SERVER['DOCUMENT_ROOT'] . '/' . $destination), 'height' => $info[1], 'width' => $info[0]);
}

?>