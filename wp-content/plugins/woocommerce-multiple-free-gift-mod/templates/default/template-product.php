<?php
global $post, $product;

	$prod_id=$post->ID;
	$so_id =  WFGM_Product_Helper::get_product_tags($prod_id);;

	$X = $this->_wfgm_so_array[$so_id]['X'];
	$Y = $this->_wfgm_so_array[$so_id]['Y'];

	$modal = '';
	$overlay = WFGM_Settings_Helper::get( 'popup_overlay', true, 'global_options' );
	if( $overlay ):
		$modal .= '<div class="wfgm-overlay"  style="display:none"></div>';
	endif;
	$modal .= '<div class="wfgm-so-popup" style="display:none"> <h2 class="wfgm-title">';

	$heading = WFGM_Settings_Helper::get( 'popup_heading_msg', false, 'global_options' );
	if( false !== $heading ) {
		$modal .= $heading;
	} else {
		$modal .= WFGM_Common_Helper::translate( 'Message for you!' );
	}
	$modal .=  '</h2><div class="wfgm-so">';


	$is_gift = false;
	$modal .= '<div class="wfgm-so-item-box">';
	$modal .= '<div class="wfgm-so-item-text"><p>';
	$so_on_this_product = WFGM_Settings_Helper::get('so_product_page_text', false, 'global_options');

	if (false === $so_on_this_product) {
		$so_on_this_product =  WFGM_Common_Helper::translate('On this product active special offer. For every {X} item we give {Y} free gift.');
	}
$so_on_this_product = str_replace(
	'{Y}',
	$this->_wfgm_so_array[$so_id]['Y'],
	$so_on_this_product);
$so_on_this_product = str_replace(
		'{X}',
		$this->_wfgm_so_array[$so_id]['X'],
	$so_on_this_product);
	$modal .= $so_on_this_product . '</p></div></div>';

	$modal .= '<div class="wfgm-so-actions"><button class="button" id="wfgm-btn-ok">';
	$add_gift_text = WFGM_Settings_Helper::get('ok_text', false, 'global_options');
	if (false !== $add_gift_text) {
		$modal .= $add_gift_text;
	} else {
		$modal .= WFGM_Common_Helper::translate('Okay');
	}
	$modal .= '</button></div>';
	$modal .= '</div></div>';

	$popup_so_product_page_enabled = WFGM_Settings_Helper::get('so_product_page_enabled', false, 'global_options');

	if ( $popup_so_product_page_enabled ) {
		if( $so_id !== 0){
			echo $modal;
		}
	}