<?php
$buffer = ob_get_contents(); 

ob_get_clean();
ob_start();

$buffer = str_replace('<h1>',"<h1 $h1style>", $buffer);
$buffer = str_replace('<h2>',"<br><h2 $h2style>", $buffer);
$buffer = str_replace('<h3>',"<br><h3 $h3style>", $buffer);
$buffer = str_replace('<p>',"<p $pstyle>", $buffer);
$buffer = str_replace('<li>',"<li $pstyle>", $buffer);

// Order number.
if( isset($order) && $order->get_order_number() )
$buffer = str_replace('{order_number}',$order->get_order_number(), $buffer);


// Order date.
if( isset($order) && function_exists('wc_date_format') )
$buffer = str_replace('{order_date}', date_i18n( wc_date_format(), strtotime( $order->order_date ) ), $buffer );

// Site name.
$buffer = str_replace('{site-title}',wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), $buffer );
$buffer = str_replace('{blogname}',wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), $buffer );

// Magic Properties.

$mps = array('billing_address_1', 'billing_address_2', 'billing_city', 'billing_company', 'billing_country', 'billing_email', 'billing_first_name', 'billing_last_name', 'billing_phone', 'billing_postcode', 'billing_state', 'cart_discount', 'cart_discount_tax', 'customer_ip_address', 'customer_user', 'customer_user_agen', 'order_currency', 'order_discount', 'order_key', 'order_shipping', 'order_shipping_tax', 'order_tax', 'order_total', 'payment_method', 'payment_method_title', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_company', 'shipping_country', 'shipping_first_name', 'shipping_last_name', 'shipping_method_title', 'shipping_postcode', 'shipping_state');

foreach ($mps as $mp) {
	if( isset($order) && isset($order->$mp) )
		$buffer = str_replace('{{'.$mp.'}}', $order->$mp, $buffer );
}


echo $buffer;