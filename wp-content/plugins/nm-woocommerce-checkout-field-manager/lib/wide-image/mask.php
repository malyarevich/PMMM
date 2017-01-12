<?php 
/*
 * using WideImage library
 */
?>

<html>
<head>
<title>Showing Image</title>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>

var file_name = 'poor.jpg';
jQuery(function($){

	
	
});

function load_image(filter){

	var data = {image_name: file_name, filter: filter};
	
	jQuery.get('sendimage.php', data, function(resp){

		console.log(resp);
		
		file_name = resp.rev_name;
		var image_path = 'images/'+file_name;
		
		jQuery('div').html('<img src='+image_path+' />'); 
	},'json');
	
}
</script>
</head>

<body>
<h1>Wide Image filters</h1>

<div></div>

<a href="javascript:load_image('mask')">Mask image</a>
</body>
</html>