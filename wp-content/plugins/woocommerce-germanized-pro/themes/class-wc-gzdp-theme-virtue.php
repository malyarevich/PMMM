<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Theme_Virtue extends WC_GZDP_Theme {

	public function __construct( $template ) {
		parent::__construct( $template );
	}

	public function set_priorities() {
		$this->priorities = array(

		);
	}

	public function custom_hooks() {
		
		if ( has_action( 'woocommerce_single_product_summary', 'woocommerce_gzd_template_single_price_unit' ) )
			add_action( 'wc_gzdp_single_product_legal_price_info', 'woocommerce_gzd_template_single_price_unit', 0 );
		if ( has_action( 'woocommerce_single_product_summary', 'woocommerce_gzd_template_single_legal_info' ) )
			add_action( 'wc_gzdp_single_product_legal_price_info', 'woocommerce_gzd_template_single_legal_info', 1 );

		// Remove GZD Actions
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_gzd_template_single_price_unit', wc_gzd_get_hook_priority( 'single_price_unit' ) );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_gzd_template_single_legal_info', wc_gzd_get_hook_priority( 'single_legal_info' ) );

		// Footer
		$this->footer_info();
		remove_action ( 'wp_footer', 'woocommerce_gzd_template_footer_vat_info', wc_gzd_get_hook_priority( 'footer_vat_info' ) );
		remove_action ( 'wp_footer', 'woocommerce_gzd_template_footer_sale_info', wc_gzd_get_hook_priority( 'footer_sale_info' ) );

	}

	public function footer_info() {
		
		global $virtue;
		
		if ( isset( $virtue[ 'footer_text' ] ) ) {
			if ( has_action( 'wp_footer', 'woocommerce_gzd_template_footer_vat_info' ) )
				$virtue[ 'footer_text' ] .= ' [gzd_vat_info]';
			if ( has_action( 'wp_footer', 'woocommerce_gzd_template_footer_sale_info' ) )
				$virtue[ 'footer_text' ] .= ' [gzd_sale_info]';
		}

	}

}