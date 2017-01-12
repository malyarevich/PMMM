<?php 
/*
 * using WideImage library
 */

//include 'demo/helpers/common.php';
//include 'WideImage.php';


$img_url = 'http://www.theproductionarea.net/lib/php-image-processing/wide-image/images/poor.jpg';
//$image = WideImage::load($img_url);
//$newImage = $image->resize(800, 500)->rotate(20);

//define('DEMO_PATH1', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR);
//echo 'the path '.DEMO_PATH1;
?>

<html>
<head>
<title>Showing Image</title>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
jQuery(function($){

	
	
});

function load_image(name){

	var data = {image: name};
	
	jQuery.get('sendimage.php', data, function(resp){

		console.log(resp);
		jQuery('div').html('<img src='+resp+' />'); 
		//jQuery('img').attr('src', resp);	
	});
	
}
</script>
</head>

<body>
<h1>Wide Image filters</h1>
<?php $var = 'image-processor.php?image=poor.jpg&amp;output=preset for demo&amp;colors=255&amp;dither=&amp;match_palette=&amp;demo=rotate&amp;angle=135';
echo '<img src="'.$var.'" />';
?>

<div></div>


</body>
</html>