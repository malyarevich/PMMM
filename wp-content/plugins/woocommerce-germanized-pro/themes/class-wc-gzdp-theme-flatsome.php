<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Theme_Flatsome extends WC_GZDP_Theme {

	public function __construct( $template ) {
		parent::__construct( $template );
	}

	public function set_priorities() {
		$this->priorities = array(
			'loop_price_unit' => 10,
			'loop_tax_info' => 11,
			'loop_shipping_costs_info' => 12,
			'loop_delivery_time_info' => 13,
			'loop_product_units' => 14,
		);
	}

	public function custom_hooks() {
		
		if ( has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_gzd_template_single_shipping_costs_info' ) )
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_gzd_template_single_shipping_costs_info', wc_gzd_get_hook_priority( 'loop_shipping_costs_info' ) );
		if ( has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_gzd_template_single_delivery_time_info' ) )
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_gzd_template_single_delivery_time_info', wc_gzd_get_hook_priority( 'loop_delivery_time_info' ) );
		if ( has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_gzd_template_single_tax_info' ) )
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_gzd_template_single_tax_info', wc_gzd_get_hook_priority( 'loop_tax_info' ) );
		if ( has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_gzd_template_single_product_units' ) )
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_gzd_template_single_product_units', wc_gzd_get_hook_priority( 'loop_product_units' ) );

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_gzd_template_single_shipping_costs_info', wc_gzd_get_hook_priority( 'loop_shipping_costs_info', false ) );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_gzd_template_single_delivery_time_info', wc_gzd_get_hook_priority( 'loop_delivery_time_info', false ) );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_gzd_template_single_tax_info', wc_gzd_get_hook_priority( 'loop_tax_info', false ) );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_gzd_template_single_product_units', wc_gzd_get_hook_priority( 'loop_product_units', false ) );

		$this->footer_init();
		remove_action ( 'wp_footer', 'woocommerce_gzd_template_footer_vat_info', wc_gzd_get_hook_priority( 'footer_vat_info' ) );
		remove_action ( 'wp_footer', 'woocommerce_gzd_template_footer_sale_info', wc_gzd_get_hook_priority( 'footer_sale_info' ) );
		
	}

	public function footer_init() {
		global $flatsome_opt;
		
		if ( isset( $flatsome_opt[ 'footer_left_text' ] ) ) {
			if ( has_action( 'wp_footer', 'woocommerce_gzd_template_footer_vat_info' ) )
				$flatsome_opt[ 'footer_left_text' ] .= do_shortcode( '[gzd_vat_info]' );
			if ( has_action( 'wp_footer', 'woocommerce_gzd_template_footer_sale_info' ) )
				$flatsome_opt[ 'footer_left_text' ] .= do_shortcode( '[gzd_sale_info]' );
		}

	}

}