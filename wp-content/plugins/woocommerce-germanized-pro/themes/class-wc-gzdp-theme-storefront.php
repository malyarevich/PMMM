<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Theme_Storefront extends WC_GZDP_Theme {

	public function __construct( $template ) {
		parent::__construct( $template );
	}

	public function set_priorities() {
		$this->priorities = array(

		);
	}

	public function custom_hooks() {
		add_action( 'storefront_footer', array( $this, 'init_footer' ), 30 );
		
		if ( get_option( 'woocommerce_gzdp_checkout_enable' ) === 'yes' )
			add_action( 'wp_enqueue_scripts', array( $this, 'deregister_sticky_payment' ), 30 );
	}

	public function deregister_sticky_payment() {
		wp_dequeue_script( 'storefront-sticky-payment' );
	}

	public function init_footer() {
		if ( has_action( 'wp_footer', 'woocommerce_gzd_template_footer_vat_info' ) )
			echo do_shortcode( '[gzd_vat_info]' );
		if ( has_action( 'wp_footer', 'woocommerce_gzd_template_footer_sale_info' ) )
			echo do_shortcode( '[gzd_sale_info]' );
		remove_action ( 'wp_footer', 'woocommerce_gzd_template_footer_vat_info', wc_gzd_get_hook_priority( 'footer_vat_info' ) );
		remove_action ( 'wp_footer', 'woocommerce_gzd_template_footer_sale_info', wc_gzd_get_hook_priority( 'footer_sale_info' ) );
	}

}