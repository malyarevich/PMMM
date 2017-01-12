<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Checkout_Step_Payment extends WC_GZDP_Checkout_Step {

	public function __construct( $id, $title ) {

		parent::__construct( $id, $title, '#order-payment' );

	}

	public function is_activated() {

		if ( ! WC()->cart->needs_payment() )
			return false;

		return true;

	}

	public function get_wrapper_classes() {

		$classes = parent::get_wrapper_classes();
		
		if ( get_option( 'woocommerce_gzdp_checkout_payment_validation' ) == 'no' )
			$classes[] = 'no-ajax';

		return $classes;

	}

}