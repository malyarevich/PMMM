<?php 

include 'WideImage.php';

//echo json_encode($_REQUEST); exit;

$image_name = $_REQUEST['image_name'];


$img_url = 'http://www.theproductionarea.net/lib/php-image-processing/wide-image/images/'.$image_name;
$image = WideImage::load($img_url);

switch ($_REQUEST['filter']){

	case 'resize':
		$new_image = $image->resize(200, 500);
		break;

	case 'rotate':
		$new_image = $image->rotate(35);
		break;

	case 'mask':

		$img_url = 'http://www.theproductionarea.net/lib/php-image-processing/wide-image/images/template-solid-1.png';
		$img = WideImage::load($img_url);
		$mask = $img -> getMask();

		$img_url = 'http://www.theproductionarea.net/lib/php-image-processing/wide-image/images/poor.jpg';
		$above_image = WideImage::load($img_url);
		$masked = $above_image -> applyMask($mask, "right-15", 50);

		break;

	case 'merge':

		$watermarked = 'http://www.theproductionarea.net/lib/php-image-processing/wide-image/images/1-rainbow.png';
		$watermark = WideImage::load($watermarked);

		if(isset($_REQUEST['width'])){
			$resized = $watermark -> resize(intval($_REQUEST['width']), intval($_REQUEST['height']))->rotate($_REQUEST['rotate']);
			$resized_name = 'resized.png';
			$resized -> saveToFile('images/'.$resized_name);
			$watermarked = 'http://www.theproductionarea.net/lib/php-image-processing/wide-image/images/'.$resized_name;
			$watermark = WideImage::load($watermarked);
		}

		$based = 'http://www.theproductionarea.net/lib/php-image-processing/wide-image/images/template-shell-2.png';
		$base = WideImage::load($based);

		$result = $base->merge($watermark, $_REQUEST['left'], $_REQUEST['top'], 95);

		break;
}


$extension = substr($image_name, -3);

$revised_name = 'merged.png';

$result -> saveToFile('images/'.$revised_name);

$resp = array(	'session'		=> $_SESSION['image_rev'],
		'rev_name'		=> $revised_name);

echo json_encode($resp);
?>