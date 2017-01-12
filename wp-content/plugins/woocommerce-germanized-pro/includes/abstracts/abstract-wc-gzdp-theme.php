<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

abstract class WC_GZDP_Theme {

	public $name;
	public $priorities = array();

	public function __construct( $template ) {

		$this->theme = wp_get_theme( $template );
		$this->name = $this->theme->get_template();

		$this->set_priorities();

		// Load before WC GZD Hooks
		add_filter( 'wc_gzd_frontend_hook_priority', array( $this, 'hooks' ), 0, 3 );
		add_filter( 'woocommerce_germanized_filter_template', array( $this, 'template_filter' ), 0, 4 );
		add_filter( 'woocommerce_gzdp_checkout_template_not_found', array( $this, 'template_filter' ), 0, 3 );

		add_action( 'after_setup_theme', array( $this, 'custom_hooks' ), 0 );
		add_action( 'after_setup_theme', array( $this, 'load_theme_support' ), 10 );
		add_action( 'wp_print_styles', array( $this, 'load_styles' ), 11 );
	}

	public function set_priorities() {

	}

	public function hooks( $prio, $hook, $gzd_priorities ) {
		if ( isset( $this->priorities[ $hook ] ) )
			return $this->priorities[ $hook ];
		return $prio;
	}

	public function load_theme_support() {
		add_theme_support( 'woocommerce-germanized' );
	}

	public function template_filter( $template, $template_name, $template_path ) {
		
		// Do not override child themes
		if ( strpos( $template, 'child' ) !== false )
			return $template;

		$custom_template = WC_germanized_pro()->plugin_path() . '/themes/' . $this->name . '/templates/' . $template_name;
			
		if ( file_exists( $custom_template ) )
			$template = $custom_template;

		return $template;
	}

	public function custom_hooks() {

	}

	public function load_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$css = WC_germanized_pro()->plugin_path() . '/themes/assets/css/wc-gzdp-' . $this->name . $suffix . '.css';
		
		if ( file_exists( $css ) ) {
			wp_register_style( 'wc-gzdp-' . $this->name, WC_germanized_pro()->plugin_url() . '/themes/assets/css/wc-gzdp-' . $this->name . $suffix . '.css', array(), WC_GERMANIZED_PRO_VERSION );	
			wp_enqueue_style( 'wc-gzdp-' . $this->name );
		}
	}

}