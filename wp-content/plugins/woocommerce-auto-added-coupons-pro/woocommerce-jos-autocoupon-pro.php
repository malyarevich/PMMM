<?php
/**
 * Plugin Name: WooCommerce Extended Coupon Features PRO
 * Plugin URI: http://www.soft79.nl
 * Description: Additional functionality for WooCommerce Coupons: Apply certain coupons automatically, allow applying coupons via an url, etc...
 * Version: 2.3.7.3
 * Author: Soft79
 * License: GPL2
 */
 
// Changelog: see readme.txt

/*
 TODO:
 - stop using get_plugin_data
 - WORK IN PROGRESS: Admin page: Option to enable/disable functionality
 - Admin page: Enable/disable debugging
 - Apply filter for autocoupon individual_use_filter
 - (PRO) Eval
*/


 
defined('ABSPATH') or die();

if ( ! function_exists( 'wjecf_load_plugin_textdomain' ) ) {

	/**
	 * Include the file once if it exists.
	 * @param string $filename
	 * @return void
	 */
	function wjecf_optional_include( $filename ) {
		if ( file_exists( dirname( __FILE__ ) . '/' . $filename ) ) {
			include_once( $filename );
		}
	}

	require_once( 'includes/wjecf-wc.php' );
	require_once( 'includes/wjecf-controller.php' );
	require_once( 'includes/abstract-wjecf-plugin.php' );
	require_once( 'includes/admin/wjecf-admin.php' );
	require_once( 'includes/admin/wjecf-admin-auto-upgrade.php' );
	//Optional
	wjecf_optional_include( 'includes/wjecf-autocoupon.php' );
	wjecf_optional_include( 'includes/wjecf-wpml.php' );
	//PRO
	wjecf_optional_include( 'includes/wjecf-pro-controller.php' );
	wjecf_optional_include( 'includes/wjecf-pro-free-products.php' );
	wjecf_optional_include( 'includes/wjecf-pro-coupon-queueing.php' );
	wjecf_optional_include( 'includes/wjecf-pro-product-filter.php' );
	wjecf_optional_include( 'includes/wjecf-pro-limit-discount-quantities.php' );
	wjecf_optional_include( 'includes/wjecf-pro-api.php' );	

	//Translations
	add_action( 'plugins_loaded', 'wjecf_load_plugin_textdomain' );
	function wjecf_load_plugin_textdomain() {
		load_plugin_textdomain('woocommerce-jos-autocoupon', false, basename(dirname(__FILE__)) . '/languages/' );
	}


	// Only Initiate the plugin if WooCommerce is active
	if ( WJECF_WC::instance()->get_woocommerce_version() == false ) {
		add_action( 'admin_notices', 'wjecf_admin_notice' );
	    function wjecf_admin_notice() {
	        $msg = __( 'WooCommerce Extended Coupon Features is disabled because WooCommerce could not be detected.', 'woocommerce-jos-autocoupon' );
	        echo '<div class="error"><p>' . $msg . '</p></div>';
	    }
	} else {	

		function WJECF_WC() {
			return WJECF_WC::instance();
		}		

		/**
		 * Get the instance of WJECF
		 * @return WJECF_Controller|WJECF_Pro_Controller The instance of WJECF
		 */
		function WJECF() {
			if ( class_exists( 'WJECF_Pro_Controller' ) ) { 
				return WJECF_Pro_Controller::instance();
			} else {
				return WJECF_Controller::instance();
			}
		}

		/**
		 * Get the instance of WJECF_Admin
		 * @return WJECF_Admin The instance of WJECF_Admin
		 */
		function WJECF_ADMIN() {
			return WJECF()->get_plugin('WJECF_Admin');
		}

		$wjecf_extended_coupon_features = WJECF();

		WJECF()->add_plugin('WJECF_Admin');
		WJECF()->add_plugin('WJECF_Admin_Auto_Upgrade');
		WJECF()->add_plugin('WJECF_AutoCoupon');
		WJECF()->add_plugin('WJECF_WPML');
		if ( WJECF()->is_pro() ) {
			WJECF()->add_plugin('WJECF_Pro_Free_Products');
			WJECF()->add_plugin('WJECF_Pro_Coupon_Queueing');
			WJECF()->add_plugin('WJECF_Pro_Product_Filter');
			WJECF()->add_plugin('WJECF_Pro_Limit_Discount_Quantities');
		}
		WJECF()->start();
	}

}


// =========================================================================================================
// Some snippets that might be useful
// =========================================================================================================

/* // HINT: Use this snippet in your theme if you use coupons with restricted emails and AJAX enabled one-page-checkout.

//Update the cart preview when the billing email is changed by the customer
add_filter( 'woocommerce_checkout_fields', function( $checkout_fields ) {
	$checkout_fields['billing']['billing_email']['class'][] = 'update_totals_on_change';
	return $checkout_fields;	
} );
 
// */ 
 

/* // HINT: Use this snippet in your theme if you want to update cart preview after changing payment method.
//Even better: In your theme add class "update_totals_on_change" to the container that contains the payment method radio buttons.
//Do this by overriding woocommerce/templates/checkout/payment.php

//Update the cart preview when payment method is changed by the customer
add_action( 'woocommerce_review_order_after_submit' , function () {
	?><script type="text/javascript">
		jQuery(document).ready(function($){
			$(document.body).on('change', 'input[name="payment_method"]', function() {
				$('body').trigger('update_checkout');
				//$.ajax( $fragment_refresh );
			});
		});
	</script><?php 
} );
// */
