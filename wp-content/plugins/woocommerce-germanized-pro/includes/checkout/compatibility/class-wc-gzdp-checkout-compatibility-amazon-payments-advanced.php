<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Checkout_Compatibility_Amazon_Payments_Advanced {

	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'remove_ajax_hook' ), 20 );
		add_action( 'woocommerce_gzdp_checkout_scripts', array( $this, 'set_scripts' ), 10, 2 );
	}

	public function set_scripts( $multistep, $assets ) {
		// Multistep Checkout
		wp_register_script( 'wc-gzdp-amazon-multistep-helper', WC_germanized_pro()->plugin_url() . '/assets/js/checkout-multistep-amazon-helper' . $assets->suffix . '.js', array( 'wc-gzdp-checkout-multistep' ), WC_GERMANIZED_PRO_VERSION, true );
		
		wp_localize_script( 'wc-gzdp-amazon-multistep-helper', 'amazon_helper', array(
			'managed_by' => _x( 'Managed by Amazon', 'multistep', 'woocommerce-germanized-pro' ),
		) );

		wp_enqueue_script( 'wc-gzdp-amazon-multistep-helper' );
	}

	public function remove_ajax_hook() {
		// Remove payment validation filter of step 2
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'remove_step_address_validation_filter' ), 8 );
	}

	public function remove_step_address_validation_filter() {
		if ( isset( $_POST['payment_method'] ) && 'amazon_payments_advanced' === $_POST['payment_method'] ) {
			remove_all_filters( 'woocommerce_cart_needs_payment' );
		}
	}

}