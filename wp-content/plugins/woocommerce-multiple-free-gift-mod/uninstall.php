<?php
	if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit();
	}

	//cleanup plugin data
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_title=%s AND post_type=%s", 'wfgm_gift_product', 'product_variation' ) );

	//delete single gift options
	delete_post_meta_by_key( '_wfgm_single_gift_allowed' );
	delete_post_meta_by_key( '_wfgm_single_gift_products' );
	delete_post_meta_by_key( '_wfgm_gift_product' );

	//delete global options
	delete_option( '_wfgm_global_enabled' );
	delete_option( '_wfgm_global_settings' );
	delete_option( '_wfgm_criteria' );
	delete_option( '_wfgm_popup_overlay' );
	delete_option( '_wfgm_popup_heading' );
	delete_option( '_wfgm_invalid_condition_text' );
	delete_option( '_wfgm_popup_add_gift_text' );
	delete_option( '_wfgm_popup_cancel_text' );
