<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="uv/uv.css">
    <script src="uv/lib/offline.js"></script>
    <script src="uv/helpers.js"></script>
    <title>CETAF to IIIF Proof of Concept</title>
    <style>
        #uv {
            width: 100%;
            height: 1000px;
        }
    </style>
</head>
<body>
	
<h1>CETAF to IIIF Proof of Concept</h1>
<p>
	Given a CETAF ID for a natural history specimen how might an application find an associated IIIF Manifest file so that it can be rendered in a IIIF compliant viewer?
</p>
<p>
	If the specimen does not have a IIIF resource associated with it but does have a large image is it possible to "spoof" a IIIF end point for that image file?
</p>

<h2>Mapping to IIIF Manifest</h2>

<p>Given a CETAF ID this service tries the following actions:</p>

<ol>
	<li>Calls URI asking for RDF response.
		<ol>
			<li>Gets parsable RDF metadata
				<ol>
					<li>Specimen metadata <strong>has</strong> dc:relation to resource of dc:type http://iiif.io/api/presentation/3#Manifest and dc:format "application/ld+json" -> Redirects to that IIIF manifest URI</li>
					<li>Specimen metadata <strong>lacks</strong> dc:relation to resouce of dc:type http://iiif.io/api/presentation/3#Manifest and dc:format "application/ld+json"
						<ol>
							<li>
								Can recover URI of high resolution JPEG from RDF -> Redirects to spoof IIIF end point here on this server that proxies for the big image. (Been done for Paris example)
							</li>
							<li>
								Fails with NOT FOUND error.
							</li>
						</ol>
					</li>
					
				</ol>
			</li>
			<li>Fails to get metadata -> Gives Up with not supported Error</li>
		</ol>
	</li>
	
</ol>


<h2>Give it a go!</h2>

<p>
<form action="mapper.php" method="GET" onsubmit="this.cetaf_uri.value = btoa(this.cetaf_uri.value)" >
	CETAF URI: <input type="text" name="cetaf_uri"  id="cetaf_uri" width="100" /> Show in viewer: <input type="checkbox" name="viewer" value="true" /> <input type="submit" value="Get Manifest">
</form>
</p>
	
<h3>Some Examples</h3>
<ul>
	<li>http://data.rbge.org.uk/herb/E00421509</li>
	<li></li>
	<li>http://coldb.mnhn.fr/catalognumber/mnhn/p/p00084058</li>
</ul>

</body>
</html>