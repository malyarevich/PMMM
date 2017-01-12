<?php
/**
 * WPML Helper
 *
 * Specific configuration for WPML
 *
 * @class 		WC_GZD_WPML_Helper
 * @category	Class
 * @author 		vendidero
 */
class WC_GZDP_WPML_Helper {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	public function __construct() {
		
		if ( ! $this->is_activated() ) 
			return;

		add_action( 'init', array( $this, 'init' ), 10 );
	}

	public function init() {
		add_action( 'woocommerce_gzdp_invoice_language_update', array( $this, 'observe_invoice_language_update' ), 0, 2 );
		add_action( 'woocommerce_gzd_wpml_lang_changed', array( $this, 'reload_locale' ) );
		add_action( 'woocommerce_gzdp_before_invoice_refresh', array( $this, 'add_invoice_translatable' ), 10, 1 );

		// Multistep step name refresh after init
		$this->refresh_step_names();
	}

	public function refresh_step_names() {
		if ( isset( WC_germanized_pro()->multistep_checkout ) ) {

			$step_names = WC_germanized_pro()->multistep_checkout->get_step_names();
			$steps = WC_germanized_pro()->multistep_checkout->steps;

			foreach ( $steps as $key => $step ) {
				$step->title = $step_names[ $step->id ];
			}
		}
	}

	public function add_invoice_translatable( $invoice ) {
		
		global $sitepress;
		
		if ( function_exists( 'wpml_add_translatable_content' ) )
			wpml_add_translatable_content( 'post_invoice', $invoice->id, ( get_post_meta( $invoice->id, 'wpml_language', true ) ) ? get_post_meta( $invoice->id, 'wpml_language', true ) : $sitepress->get_default_language() );

	}

	public function reload_locale( $lang ) {
		WC_germanized_pro()->load_plugin_textdomain();
	}

	public function observe_invoice_language_update( $invoice, $language ) {
		$lang = null;

		if ( $lang = get_post_meta( $invoice->id, 'wpml_language', true ) ) {
			if ( class_exists( 'WC_GZD_WPML_Helper' ) ) {
				WC_GZD_WPML_Helper::instance()->set_language( $lang );
			} else {
				WC_germanized()->compatibilities[ 'wpml' ]->set_language( $lang );
			}
		}
	}

	public function is_activated() {
		return WC_GZDP_Dependencies::instance()->is_wpml_activated();
	}

}

return WC_GZDP_WPML_Helper::instance();