<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Checkout_Step_Address extends WC_GZDP_Checkout_Step {

	public function __construct( $id, $title ) {

		parent::__construct( $id, $title, '#customer_details' );

	}

	public function submit() {

		// Temporarily set cart to not need payment to stop validating payment method input data
		add_filter( 'woocommerce_cart_needs_payment', array( $this, 'remove_payment_validation' ), PHP_INT_MAX );

		parent::submit();

		// Remove filter again
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'remove_payment_validation_filter' ), 0 );

	}

	public function remove_payment_validation() {
		return false;
	}

	public function remove_payment_validation_filter() {
		remove_filter( 'woocommerce_cart_needs_payment', array( $this, 'remove_payment_validation' ), PHP_INT_MAX );
	}

}