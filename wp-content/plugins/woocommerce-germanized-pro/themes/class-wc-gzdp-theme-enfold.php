<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Theme_Enfold extends WC_GZDP_Theme {

	public function __construct( $template ) {
		parent::__construct( $template );
	}

	public function set_priorities() {
		$this->priorities = array(

		);
	}

	public function custom_hooks() {
		
		// Single Product unit price + legal info
		if ( has_action( 'woocommerce_single_product_summary', 'woocommerce_gzd_template_single_price_unit' ) )
			add_action( 'wc_gzdp_single_product_legal_price_info', 'woocommerce_gzd_template_single_price_unit', 0 );
		if ( has_action( 'woocommerce_single_product_summary', 'woocommerce_gzd_template_single_legal_info' ) )
			add_action( 'wc_gzdp_single_product_legal_price_info', 'woocommerce_gzd_template_single_legal_info', 1 );

		// Remove GZD Actions
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_gzd_template_single_price_unit', wc_gzd_get_hook_priority( 'single_price_unit' ) );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_gzd_template_single_legal_info', wc_gzd_get_hook_priority( 'single_legal_info' ) );

		// Loop legal info
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'open_loop_wrapper' ), ( wc_gzd_get_hook_priority( 'loop_tax_info' ) - 1 ) );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'close_loop_wrapper' ), ( wc_gzd_get_hook_priority( 'loop_delivery_time_info' ) + 1 ) );

		// Footer info
		$this->footer_init();
		remove_action ( 'wp_footer', 'woocommerce_gzd_template_footer_vat_info', wc_gzd_get_hook_priority( 'footer_vat_info' ) );
		remove_action ( 'wp_footer', 'woocommerce_gzd_template_footer_sale_info', wc_gzd_get_hook_priority( 'footer_sale_info' ) );

		// Avada Builder Loop Product Info
		add_filter( 'avf_masonry_loop_prepare', array( $this, 'masonry_loop_products' ), 10, 2 );

	}

	public function masonry_loop_products( $entry, $query ) {

		if ( ! isset( $entry['post_type'] ) || 'product' !== $entry['post_type'] )
			return $entry;

		ob_start();

		WC_GZDP_Theme_Helper::instance()->manually_embed_product_loop_hooks( $entry[ 'ID' ] );
		do_action( 'woocommerce_gzd_loop_product_info' );

		$html = ob_get_clean();

		// Remove href-links because not supported by mansory
		$entry[ 'text_after' ] .= '<div class="enfold-gzd-loop-info">' . strip_tags( $html, '<del><p><div><span>' ) . '</div>';
		return $entry;
	}

	public function open_loop_wrapper() {
		echo '<div class="inner_product_header inner_product_header_legal">';
	}

	public function close_loop_wrapper() {
		echo '</div>';
	}

	public function footer_init() {
		
		global $avia;

		if ( isset( $avia->options[ 'avia' ][ 'copyright' ] ) ) {
			if ( has_action( 'wp_footer', 'woocommerce_gzd_template_footer_vat_info' ) )
				$avia->options[ 'avia' ][ 'copyright' ] .= '[nolink]' . do_shortcode( '[gzd_vat_info]' );
			if ( has_action( 'wp_footer', 'woocommerce_gzd_template_footer_sale_info' ) )
				$avia->options[ 'avia' ][ 'copyright' ] .= '[nolink]' . do_shortcode( '[gzd_sale_info]' );
		}

	}

}