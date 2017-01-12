<?php
	if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit();
	}

	//cleanup plugin data
	global $wpdb;
	$wpdb->query(
	    $wpdb->prepare
        (
            "DELETE FROM $wpdb->posts WHERE post_title=%s AND post_type=%s",
            'wbgm_gift_product',
            'product_variation'
        )
    );

	//delete single gift options
	delete_post_meta_by_key( '_wbgm_single_gift_allowed' );
	delete_post_meta_by_key( '_wbgm_single_gift_products' );
	delete_post_meta_by_key( '_wbgm_gift_product' );

	//delete global options
	delete_option( '_wbgm_global_enabled' );
	delete_option( '_wbgm_global_settings' );
	delete_option( '_wbgm_criteria' );
	delete_option( '_wbgm_popup_overlay' );
	delete_option( '_wbgm_popup_heading' );
    delete_option( '_wbgm_popup_heading_msg' );
	delete_option( '_wbgm_invalid_condition_text' );
	delete_option( '_wbgm_btn_adding_to_cart_text' );
	delete_option( '_wbgm_popup_cancel_text' );
	delete_option( '_wbgm_type_text' );
	delete_option( '_wbgm_free_item_text' );
	delete_option( '_wbgm_ok_text' );
