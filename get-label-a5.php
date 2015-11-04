<!DOCTYPE html>
<html lang="en">
<head>

	<!-- About this script
  	––––––––––––––––––––––––––––––––––––––––––––––––––

		Theme Name: 	get-label-a5.php
		Description: 	This script queries the GatherContent API for a specifc item and presents 
						it as an HTML gallery label. This is a first version proof-of-concept. The 
						code here needs to be made more elegant in future versions.
		Author: 		Gareth de Walters, GatherContent, PebbleRoad
		Author URL:		www.aucklandmuseum.com

	-->

	  <!-- Basic Page Needs
	  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	  <meta charset="utf-8">
	  <title>Get exhibition label from GatherContent</title>
	  <meta name="description" content="">
	  <meta name="author" content="">

	  <!-- Mobile Specific Metas
	  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	  <meta name="viewport" content="width=device-width, initial-scale=1">

	  <!-- FONT
	  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	  <link rel="stylesheet" href="http://www.aucklandmuseum.com/client/css/aucklandmuseum/style.css" />

	  <!-- CSS
	  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	  <link rel="stylesheet" href="css/normalize.css">
	  <link rel="stylesheet" href="css/skeleton.css">
	  <link rel="stylesheet" href="css/override.css" />

	  <!-- Favicon
	  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	  <link rel="icon" type="image/png" href="images/favicon.png">

</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
    <div class="row">
      <div class="twelve columns" style="margin-top: 5%">

			<?php
			$username = '{{API_USER_NAME}}';
			$apikey = '{{API_KEY}}';

			$gcitem = $_GET["gcitem"];

			if(ctype_digit($gcitem)){

				$ch = curl_init();

				curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Accept: application/vnd.gathercontent.v0.5+json'));
				curl_setopt( $ch, CURLOPT_USERPWD, $username . ':' . $apikey);
				curl_setopt( $ch, CURLOPT_URL, 'https://api.gathercontent.com/items/' . $gcitem);
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_VERBOSE, true );
				curl_setopt( $ch, CURLINFO_HEADER_OUT, true);

				// Convert JSON string to Array
				$response = json_decode( curl_exec( $ch ), true);
				curl_close( $ch );

				// Get the GatherContent components fram array.
				$object_title = $response["data"]["config"][0]["elements"][0]["value"];
				$object_label = $response["data"]["config"][0]["elements"][1]["value"];
				$functional_description = $response["data"]["config"][0]["elements"][2]["value"];

				// Removes inline styles
				function stripStyles($content){	/* From: https://github.com/PebbleRoad/shortcode-parser-for-gather-content/blob/master/parse.php */
				    /* Remove inline style */
				    $text = preg_replace('/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $content);
				    /* Replace ” to " */
				    $text = preg_replace('/\”/i', '"', $text);    
				    $text = preg_replace('/\’/i', "'", $text);
				    $text = preg_replace('/&nbsp;/i', "'", $text);
				    return $text;
				}	

				$object_title = stripStyles($object_title);
				$object_label = stripStyles($object_label);

				// Deliver them!
				if (!empty($object_title)) {
				    print '<h1 id="objectheading">' . $object_title . '</h1>';
				}				
				if (!empty($object_label)) {
					print '<div id="objlabel">'; 
				    print $object_label;
				    print '</div>'; 
				}
				if (!empty($functional_description)) {
					print '<div id="funcdesc">'; 
				    print $functional_description;
				    print '</div>'; 
				}								

			} // End numercial check	

			?>

      </div>
    </div>
  </div>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>
