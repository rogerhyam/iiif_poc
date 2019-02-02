<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="uv/uv.css">
    <script src="uv/lib/offline.js"></script>
    <script src="uv/helpers.js"></script>
    <title>IIIF Demo on RBGE Specimens</title>
    <style>
        #uv {
            width: 100%;
            height: 1000px;
        }
    </style>
</head>
<body>
	
<h1>IIIF Demo on RBGE Specimens</h1>
<p>This is a wrapper around our existing Zoomify image service.</p>

<?php
	if(isset($_GET['barcode']) || isset($_GET['species']) ){
?>
	<div id="uv" class="uv"></div>
    <script>
        window.addEventListener('uvLoaded', function (e) {
            createUV('#uv', {
                //iiifResourceUri: 'http://wellcomelibrary.org/iiif/b18035723/manifest',
				//iiifResourceUri:'http://192.168.7.71/iiif/collection/Rhododendron/ponticum',
				//iiifResourceUri: 'http://192.168.7.73/cetaf_id/spoof_manifest.php?jpg=aHR0cDovL21lZGlhcGhvdG8ubW5obi5mci9tZWRpYS8xNDQyMzM1MDY2NDk0dTE1N1dDcU1xR3JXc01MNQ==&rdf=aHR0cHM6Ly9zY2llbmNlLm1uaG4uZnIvaW5zdGl0dXRpb24vbW5obi9jb2xsZWN0aW9uL3AvaXRlbS9wMDAwODQwNTgucmRm',
				
<?php
				if(isset($_GET['barcode'])){
					echo "iiifResourceUri:'http://". $_SERVER['HTTP_HOST'] . "/iiif/" . $_GET['barcode'] . "',";
				}else{
					echo "iiifResourceUri:'http://". $_SERVER['HTTP_HOST'] . "/iiif/collection/" . $_GET['species'] . "',";
				}
?>
				
				configUri: 'uv-config.json'
            }, new UV.URLDataProvider());
        }, false);
    </script>
    <script src="uv/uv.js"></script>
<?php
	}
?>
<p>
<form action="rbge_demo.php" method="GET">
	Barcode: <input type="text" name="barcode" />
	<input type="submit" value="Load Specimen">
</form>
</p>
	
<h3>Some Examples</h3>
<ul>
	<li><a href="rbge_demo.php?barcode=E00664331">E00664331</a></li>
	<li><a href="rbge_demo.php?barcode=E00010363">E00010363</a></li>
	<li><a href="rbge_demo.php?barcode=E00001237">E00001237</a></li>
</ul>


</body>
</html>