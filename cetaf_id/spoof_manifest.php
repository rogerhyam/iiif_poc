<?php

// commercial sites this would be set lower for production
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

// this will do the 
require __DIR__ . '/vendor/autoload.php';

// get the jpg URI

$jpg_uri = base64_decode(@$_GET['jpg']);
if(!filter_var($jpg_uri, FILTER_VALIDATE_URL)){
	header("HTTP/1.1 400 Bad Request");
	echo "<h1>400 Bad Request</h1>";
	echo "<p>You need to provide a valid URI that has been base64 encoded as the jpg parameter.</p>";
	echo "<p>You passed: ". @$_GET['jpg'] ."</p>";
	echo "<p>Decodes to: ". $jpg_uri  ."</p>";
	exit;
}
$jpg_file_name = @$_GET['jpg'] . '.jpg';

// get the rdf URI
$rdf_uri = base64_decode(@$_GET['rdf']);
if(!filter_var($rdf_uri, FILTER_VALIDATE_URL)){
	header("HTTP/1.1 400 Bad Request");
	echo "<h1>400 Bad Request</h1>";
	echo "<p>You need to provide a valid URI that has been base64 encoded as the rdf parameter.</p>";
	echo "<p>You passed: ". @$_GET['rdf'] ."</p>";
	echo "<p>Decodes to: ". $rdf_uri  ."</p>";
	exit;
}

// cache the image if we haven't already
if(!file_exists('cache/'. $jpg_file_name)){
	file_put_contents('cache/'. $jpg_file_name, fopen($jpg_uri, 'r'));
}

// echo $jpg_uri;
// echo "<br>";
// echo $rdf_uri;
	
// start building the JSON-LD objec to return

$base_url = 'http://'. $_SERVER['HTTP_HOST'] . '/cetaf_id/iiif/' . @$_GET['jpg'];

$out = new stdClass();
$out->context = array("http://www.w3.org/ns/anno.jsonld","http://iiif.io/api/presentation/3/context.json");
$out->id = "$base_url/manifest";
$out->type = "Manifest";

$out->label = create_label("Specimen: banana");

// FIXME - Get the data for the labels from the RDF metadata.

$out->summary = new stdClass();
$out->summary = array("Summary of Specimen: banana");
$out->viewingDirection = "left-to-right";

$canvas = new stdClass();
$out->items = array($canvas);
$canvas->id = "$base_url/canvas";
$canvas->type = "Canvas";
$canvas->label = create_label("Scan");

$canvas->thumbnail = array();
$canvas->thumbnail[] = new stdClass();
$canvas->thumbnail[0]->id = $base_url;
$canvas->thumbnail[0]->type = "Image";
$canvas->thumbnail[0]->service = array();
$canvas->thumbnail[0]->service[0] = new stdClass();
$canvas->thumbnail[0]->service[0]->id = $base_url;
$canvas->thumbnail[0]->service[0]->type = "ImageService3";
$canvas->thumbnail[0]->service[0]->profile = "level0";

$image_size = getimagesize('cache/'. $jpg_file_name);
$canvas->height = $image_size[1];
$canvas->width = $image_size[0];

// annotation page
$canvas->items = array();
$image_anno_page = new stdClass();
$canvas->items[] = $image_anno_page;
$image_anno_page->id = "$base_url/annotation_page";
$image_anno_page->type = "AnnotationPage";

// annotation
$image_anno = new stdClass();
$image_anno_page->items = array($image_anno);
$image_anno->id = "$base_url/annotation";
$image_anno->type = "Annotation";
$image_anno->motivation = "Painting";
$image_anno->body = new stdClass();
$image_anno->body->id = "$base_url/info.json";
$image_anno->body->type = "Image";
$image_anno->body->format = "image/jpeg";

$service = new stdClass();
$service->id = $base_url;
$service->type = "ImageService3";
$service->profile = "level0";

$image_anno->body->service = array($service);

$image_anno->body->height = $image_size[1];
$image_anno->body->width = $image_size[0];

$image_anno->target = "$base_url/canvas";


$json = json_encode( $out, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES );

// total hack to add the @ to the context attribute (not acceptable in php)
$json = str_replace('"context":','"@context":', $json);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
echo $json;


/* -------------------------- */

function create_label($txt){
	$out = new stdClass();
	$out->en = array($txt);
	return $out; 
}
	
?>