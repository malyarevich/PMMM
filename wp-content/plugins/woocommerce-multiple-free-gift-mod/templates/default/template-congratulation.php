<?php

	$so_id =  WFGM_Product_Helper::get_product_tags($prod_id);;

	$X = $this->_wfgm_so_array[$so_id]['X'];//X
	$Y = $this->_wfgm_so_array[$so_id]['Y'];//Y
	$is_gift_in_cart = false;
	$message = '';

	?>
	<script>
		console.log(<?php echo json_encode();?>);
	</script>
	<?php
	$message .= '<div class="wfgm-so-item-text"><p>';
	$so_congrat_save_money_text = WFGM_Settings_Helper::get('so_congrat_save_money', false, 'global_options');

	if (false === $so_congrat_save_money_text) {
		$so_congrat_save_money_text =  WFGM_Common_Helper::translate('Congratulations! You save {sum(N*Y*price)} {currency}.');
	}
	$sum_save_money = 0;
	/*Create array with price*/
	$product_prices = array();
	foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
		$is_gift_product = ! empty( $cart_item['variation_id'] );
		if(! $is_gift_product) {
			$product_prices[$cart_item['product_id']] = $cart_item['data']->price;
		}
	}

	foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
		$is_gift_product = ! empty( $cart_item['variation_id'] );

		if( $is_gift_product) {
			$is_gift_in_cart = true;
			$sum_save_money += $cart_item['quantity'] * floatval($product_prices[$cart_item['product_id']]);
		}
	}

setlocale(LC_MONETARY, 'de_DE');
$so_congrat_save_money_text = str_replace(
	'{sum(N*Y*price)} {currency}',
	money_format('%.2n', $sum_save_money),
    $so_congrat_save_money_text);
$so_congrat_save_money_text = str_replace(
	'{currency}',
	get_woocommerce_currency(),
    $so_congrat_save_money_text);
	$message .= $so_congrat_save_money_text . '</p></div>';

	$popup_so_congrat_save_money_enabled = WFGM_Settings_Helper::get('so_congrat_save_money_enabled', false, 'global_options');


	if ( $popup_so_congrat_save_money_enabled ) {
		if($is_gift_in_cart == true) {
			$this->_wfgm_info_gifts['all'] = ['message' => $message, 'type' => 'success'];
		}
	}
	?>