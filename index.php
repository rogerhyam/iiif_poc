<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="uv/uv.css">
    <script src="uv/lib/offline.js"></script>
    <script src="uv/helpers.js"></script>
    <title>IIIF Proof of Concept Place</title>
    <style>
        #uv {
            width: 100%;
            height: 1000px;
        }
    </style>
</head>
<body>
	
<h1>IIIF Proof of Concept Place</h1>
<p>This is a place to check out and demo IIIF in natural history collections and integration with CETAF Identifiers.</p>

<ul>
	<li><a href="rbge_demo.php" >RBGE Specimens</a> - This is a test to see if we can write a IIIF end point that wraps around our existing Zoomify image tile based system. We will need to do it as we don't want to rewrite our whole specimen digitisation pipeline. Others may take a similar approach.</li>
	<li><a href="cetaf_id/index.php">CETAF ID to IIIF Mapper</a> - This is a test to see if we can map from CETAF IDs to associated IIIF end points or to spoof those endpoints if they are not available.</li>
</ul>


</body>
</html>