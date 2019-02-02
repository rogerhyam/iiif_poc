<?php

// commercial sites this would be set lower for production
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$base_url = 'http://'. $_SERVER['HTTP_HOST'] . '/cetaf_id/iiif/' . @$_GET['jpg'];
	
// generate the json
$out = new stdClass();
$out->__at__context = "http://iiif.io/api/image/3/context.json";
$out->id = "$base_url";
$out->__at__id = "$base_url";
$out->type = "ImageService3";
$out->protocol = "http://iiif.io/api/image"; 
$out->profile = "level0"; // what features are supported

$image_size = getimagesize('cache/'. @$_GET['jpg'] . '.jpg');
$out->width = $image_size[0];
$out->height = $image_size[1];
$out->maxWidth = $image_size[0];
$out->maxHeight = $image_size[1];
$out->maxArea = $image_size[0] * $image_size[1];

//print_r($out);
$json = json_encode( $out, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES );

// total hack to add the @ to the context attribute (not acceptable in php)
$json = str_replace('__at__','@', $json);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
echo $json;

	
?>