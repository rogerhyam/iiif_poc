<?php

// commercial sites this would be set lower for production
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

if(@$_GET['rotation'] != '0') throw_badness('Rotation other than 0 is not supported.');
if(@$_GET['quality'] != 'default') throw_badness('Only default quality is supported.');

$jpg_file_name = @$_GET['jpg'] . '.jpg';

if(!file_exists('cache/'. $jpg_file_name)){
	throw_badness('Image file does not exist. Did you request the IIIF manifest first?');
}

$image_size = getimagesize('cache/'. $jpg_file_name);

// find out the region
if($_GET['region'] == 'full'){
	$region_x = 0;
	$region_y = 0;
	$region_w = $image_size[0];
	$region_h = $image_size[1];
}else{
	$region = explode(',', $_GET['region']);
	list($region_x, $region_y, $region_w, $region_h) = $region;
}

// find out the size
if($_GET['size'] == 'max'){
	$size_w = $image_size[0];
	$size_h = $image_size[1];
}else{
	$size = explode(',', $_GET['size']);
	list($size_w, $size_h) = $size;
}

// do we have a cached version of this view
$cached_file_name = $region_x .'_'. $region_y .'_'. $region_w .'_'. $region_h .'_'. $size_w .'_'. $size_h . '.jpg';
$cached_file_path = "cache/" . @$_GET['jpg'] . '/' . $cached_file_name;

if(file_exists($cached_file_path)){
	header('Content-Type: image/jpeg');
	header("Access-Control-Allow-Origin: *");
	readfile($cached_file_path);
	exit;
}

// no cached file 
// check we have a directory for the cached ones to live in
if(!file_exists("cache/" . @$_GET['jpg'])){
	mkdir("cache/" . @$_GET['jpg']);
}

// load the original file image
$image = new Imagick("cache/". $jpg_file_name);

// crop it if we need to
if($_GET['region'] != 'full'){
	$image->cropImage($region_w, $region_h, $region_x, $region_y);
}

// resize it without fussing
$w = $size_w ? $size_w : null;
$h = $size_h ? $size_h : null; 
$image->thumbnailImage($w, $h); 

// write it to the cache
//$image->writeImage($cached_file_path);

file_put_contents($cached_file_path, $image);

// return it
header('Content-Type: image/jpeg');
header("Access-Control-Allow-Origin: *");
echo $image->getImagesBlob();
exit;


// if we get to here we return the image - nothing else to do
//header('Content-Type: image/jpeg');
//header("Access-Control-Allow-Origin: *");
//readfile('cache/'. $jpg_file_name);

function throw_badness($message){
	header("HTTP/1.1 400 Bad Request");
	echo "<h1>400 Bad Request</h1>";
	echo "<p>$message</p>";
	exit;
}

?>

