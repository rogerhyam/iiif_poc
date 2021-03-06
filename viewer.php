<?php

/*
	This is a simple page to load a viewer for a manifest
	The manifest URI is passed as a base64 string

*/
	
$manifest_uri = base64_decode(@$_GET['manifest_uri']);
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="uv/uv.css">
    <script src="uv/lib/offline.js"></script>
    <script src="uv/helpers.js"></script>
    <title>CETAF Mapper Viewer</title>
    <style>
        #uv {
            width: 100%;
            height: 1000px;
        }
    </style>
</head>
<body>
	
		<div id="uv" class="uv"></div>
	    <script>
	        window.addEventListener('uvLoaded', function (e) {
	            createUV('#uv', {
	                iiifResourceUri: '<?php echo $manifest_uri ?>',
					configUri: 'uv-config.json'
	            }, new UV.URLDataProvider());
	        }, false);
	    </script>
	    <script src="uv/uv.js"></script>
	
	
</body>
</html>
	
	