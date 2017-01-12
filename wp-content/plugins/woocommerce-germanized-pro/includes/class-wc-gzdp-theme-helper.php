<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Theme_Helper {

	protected static $_instance = null;
	public $themes = array();
	public $theme;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		$this->themes = array(
			'virtue', 
			'flatsome',
			'enfold',
			'storefront',
		);

		$current = wp_get_theme();

		if ( in_array( $current->get_template(), $this->themes ) )
			$this->load_theme( $current->get_template() );

	}

	public function load_theme( $template ) {

		if ( ! in_array( $template, $this->themes ) )
			return false;

		$classname = 'WC_GZDP_Theme_' . str_replace( '-', '_', ucfirst( sanitize_title( $template ) ) );

		if ( class_exists( $classname ) )
			$this->theme = new $classname( $template );
	}

	public function manually_embed_product_single_hooks( $product_id, $types = array( 'unit_price' => 10, 'legal' => 11, 'delivery_time' => 12, 'product_units' => 13 ) ) {

		global $product;
		$product = wc_get_product( $product_id );

		if ( isset( $types[ 'unit_price' ] ) && get_option( 'woocommerce_gzd_display_product_detail_unit_price' ) == 'yes' )
			add_action( 'woocommerce_gzd_single_product_info', 'woocommerce_gzd_template_single_price_unit', $types[ 'unit_price' ] );
		if ( isset( $types[ 'legal' ] ) && ( get_option( 'woocommerce_gzd_display_product_detail_tax_info' ) == 'yes' || get_option( 'woocommerce_gzd_display_product_detail_shipping_costs' ) == 'yes' ) )
			add_action( 'woocommerce_gzd_single_product_info', 'woocommerce_gzd_template_single_legal_info', $types[ 'legal' ] );
		if ( isset( $types[ 'delivery_time' ] ) && get_option( 'woocommerce_gzd_display_product_detail_delivery_time' ) == 'yes' )
			add_action( 'woocommerce_gzd_single_product_info', 'woocommerce_gzd_template_single_delivery_time_info', $types[ 'delivery_time' ] );
		if ( isset( $types[ 'product_units' ] ) && get_option( 'woocommerce_gzd_display_product_detail_product_units' ) == 'yes' )
			add_action( 'woocommerce_gzd_single_product_info', 'woocommerce_gzd_template_single_product_units', $types[ 'product_units' ] );

	}

	public function manually_embed_product_loop_hooks( $product_id, $types = array( 'unit_price' => 10, 'tax' => 11, 'shipping_costs' => 12, 'delivery_time' => 13, 'product_units' => 14 ) ) {

		global $product;
		$product = wc_get_product( $product_id );

		if ( isset( $types[ 'unit_price' ] ) && get_option( 'woocommerce_gzd_display_listings_unit_price' ) == 'yes' )
			add_action( 'woocommerce_gzd_loop_product_info', 'woocommerce_gzd_template_single_price_unit', $types[ 'unit_price' ] );
		if ( isset( $types[ 'tax' ] ) && get_option( 'woocommerce_gzd_display_listings_tax_info' ) == 'yes' )
			add_action( 'woocommerce_gzd_loop_product_info', 'woocommerce_gzd_template_single_tax_info', $types[ 'tax' ] );
		if ( isset( $types[ 'shipping_costs' ] ) && get_option( 'woocommerce_gzd_display_listings_shipping_costs' ) == 'yes' )
			add_action( 'woocommerce_gzd_loop_product_info', 'woocommerce_gzd_template_single_shipping_costs_info', $types[ 'shipping_costs' ] );
		if ( isset( $types[ 'delivery_time' ] ) && get_option( 'woocommerce_gzd_display_listings_delivery_time' ) == 'yes' )
			add_action( 'woocommerce_gzd_loop_product_info', 'woocommerce_gzd_template_single_delivery_time_info', $types[ 'delivery_time' ] );
		if ( isset( $types[ 'product_units' ] ) && get_option( 'woocommerce_gzd_display_listings_product_units' ) == 'yes' )
			add_action( 'woocommerce_gzd_loop_product_info', 'woocommerce_gzd_template_single_product_units', $types[ 'product_units' ] );

	}

}

return WC_GZDP_Theme_Helper::instance();