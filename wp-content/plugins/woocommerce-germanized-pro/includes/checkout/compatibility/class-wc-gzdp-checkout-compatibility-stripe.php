<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Checkout_Compatibility_Stripe {

	public function __construct() {
		add_action( 'woocommerce_gzdp_checkout_scripts', array( $this, 'set_scripts' ), 10, 2 );
	}

	public function set_scripts( $multistep, $assets ) {
		// Multistep Checkout
		wp_register_script( 'wc-gzdp-stripe-multistep-helper', WC_germanized_pro()->plugin_url() . '/assets/js/checkout-multistep-stripe-helper' . $assets->suffix . '.js', array( 'wc-gzdp-checkout-multistep' ), WC_GERMANIZED_PRO_VERSION, true );
		wp_enqueue_script( 'wc-gzdp-stripe-multistep-helper' );
	}

}