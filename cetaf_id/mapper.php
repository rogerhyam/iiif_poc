<?php
	
// commercial sites this would be set lower for production
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

// this will do the 
require __DIR__ . '/vendor/autoload.php';

$uri = base64_decode(@$_GET['cetaf_uri']);
$uri = trim($uri);

// check we have a uri to deal with and fail if not
if(!filter_var($uri, FILTER_VALIDATE_URL)){
	
	header("HTTP/1.1 400 Bad Request");
	echo "<h1>400 Bad Request</h1>";
	echo "<p>You need to provide a valid URI that has been base64 encoded as the cetaf_uri parameter.</p>";
	echo "<p>You passed: ". @$_GET['cetaf_uri'] ."</p>";
	echo "<p>Decodes to: ". $uri  ."</p>";
	exit;
}

echo $uri;

// call for RDF URI
$curl = get_curl_handle($uri);
curl_setopt($curl, CURLOPT_HTTPHEADER, array( "Accept: application/rdf+xml"));
$response = run_curl_request($curl);

if($response->info['http_code'] != 303){
	
	header("HTTP/1.1 502 Bad Gateway");
	echo "<h1>502 Bad Gateway</h1>";
	echo "<p>Requesting RDF by passing 'Accept: application/rdf+xml' header to $uri</p>";
	echo "<p>Should have got 303 redirect to RDF source but got ".$response->info['http_code']." instead.</p>";
	exit;

}

// load RDF data
$rdf_uri = $response->info['redirect_url'];
$curl = get_curl_handle($rdf_uri);
curl_setopt($curl, CURLOPT_HTTPHEADER, array( "Accept: application/rdf+xml"));
$response = run_curl_request($curl);

// we generously allow there to be a second 303 redirect 
// if we wrote more elegant code this would be handled better.
if($response->info['http_code'] == 303){
	$rdf_uri = $response->info['redirect_url'];
	$curl = get_curl_handle($rdf_uri);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array( "Accept: application/rdf+xml"));
	$response = run_curl_request($curl);
}


if($response->info['http_code'] != 200){
	
	header("HTTP/1.1 502 Bad Gateway");
	echo "<h1>502 Bad Gateway</h1>";
	echo "<p>Requesting RDF by passing 'Accept: application/rdf+xml' header to $rdf_uri</p>";
	echo "<p>Should have got 200 with RDF data but got ".$response->info['http_code']." instead.</p>";
	exit;

}

$doc = new EasyRdf_Graph($rdf_uri);
$doc->load($rdf_uri,'rdfxml');

// now then...
// The specimen_uri is the one passed in.
// That doesn't mean it is the one the assertions are made about.
// They may be made about a different version of the URI that is joined to this one by an owl:sameAs.
$sames = $doc->resourcesMatching('owl:sameAs', $doc->resource($uri));
if(count($sames) > 0){
	$specimen_uri = $sames[0]->getUri();
}else{
	$specimen_uri = $uri;
}
echo "\n<br/>" . $rdf_uri;
echo "\n<br/>" . $specimen_uri;

echo '<hr/>';

// get the dc:related resources
$resources = $doc->allResources($specimen_uri, "dc:relation");

// look for one that is of type http://iiif.io/api/presentation/3#Manifest 
foreach($resources as $r){
	echo $r->getUri() . "\n<br/>";
	$types = $doc->allResources($r->getUri(), "dc:type");
	foreach($types as $t){
		// if we find a resource which is of type http://iiif.io/api/presentation/3#Manifest 
		// we can redirect to that URI - job done.
		if($t->getUri() == 'http://iiif.io/api/presentation/3#Manifest'){
			
			if(@$_GET['viewer']){
				$target = '../viewer.php?manifest_uri=' . base64_encode($r->getUri());
			}else{
				$target = $r->getUri();
			}
			
			header("HTTP/1.1 303 See Other");
			header("Location: " . $target);
			echo "<h1>303 See Other</h1>";
			echo "<p>Your IIIF Manifest if over here.</p>";
			exit;
		}
	}
}

// got this far - lets add a Paris hack!
// find resources that have dc:subject = $specimen_id
// and dc:type = http://purl.org/dc/dcmitype/StillImage
// and dc:format = 	"image/jpeg"
$with_subject = $doc->resourcesMatching("dc:subject", $doc->resource($specimen_uri));
foreach($with_subject as $r){
	echo $r->getUri() . "<br/>";
	foreach($r->allResources("dc:type") as $t){
		if($t->getUri() == "http://purl.org/dc/dcmitype/StillImage"){
			foreach($r->allLiterals("dc:format") as $f){
				if($f == "image/jpeg"){
					
					$jpg_uri = base64_encode($r->getUri());
					$data_uri = base64_encode($rdf_uri);
					$manifest_uri = "http://" . $_SERVER['HTTP_HOST']  . "/cetaf_id/spoof_manifest.php?jpg=$jpg_uri&rdf=$data_uri";
					
					if(@$_GET['viewer']){
						$target = '../viewer.php?manifest_uri=' . base64_encode($manifest_uri);
					}else{
						$target = $manifest_uri;
					}
					
					header("HTTP/1.1 303 See Other");
					header("Location: $target");
					echo "<h1>303 See Other</h1>";
					echo "<p>Your IIIF Manifest if over here.</p>";
					exit;
					
				}
			}
		}
	}
}


echo $doc->dump('html');




/* H E L P E R  - F U N C T I O N S */


function get_curl_handle($uri){
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'CETAF to IIIF Mapper');
    curl_setopt($ch, CURLOPT_HEADER, 1);
    return $ch;
}

function run_curl_request($curl){
   
   $out['response'] = curl_exec($curl);
   
   $out['error'] = curl_errno($curl);
   
    if(!$out['error']){
        // no error
        $out['info'] = curl_getinfo($curl);
        $out['headers'] = get_headers_from_curl_response($out);
        $out['body'] = trim(substr($out['response'], $out['info']["header_size"]));

    }else{
        // we are in error
        $out['error_message'] = curl_error($curl);
    }
    
    // we close it down after it has been run
    curl_close($curl);
    
    return (object)$out;
    
}

/**
 * cURL returns headers as sting so we need to chop them into
 * a useable array - even though the info is in the 
 */
function get_headers_from_curl_response($out){
    
    $headers = array();
    
    // may be multiple header blocks - we want the last
    $headers_block = substr($out['response'], 0, $out['info']["header_size"]-1);
    $blocks = explode("\r\n\r\n", $headers_block);
    $header_text = trim($blocks[count($blocks) -1]);

    foreach (explode("\r\n", $header_text) as $i => $line){
        if ($i === 0){
            $headers['http_code'] = $line;
        }else{
            list ($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
        }
    }

    return $headers;
}

function get_body_from_curl_response($response){
    return trim(substr($response, strpos($response, "\r\n\r\n")));
}


?>