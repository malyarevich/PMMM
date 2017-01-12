<?php
/*
Plugin Name: WooCommerce Product Filter
Plugin URI: http://www.mihajlovicnenad.com/product-filter
Description: Advanced product filter for any Wordpress template! - mihajlovicnenad.com
Author: Mihajlovic Nenad
Version: 6.0.2
Author URI: http://www.mihajlovicnenad.com
Text Domain: prdctfltr
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( !class_exists( 'PrdctfltrInit' ) ) :

	final class PrdctfltrInit {

		public static $version = '6.0.2';

		protected static $_instance = null;

		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			do_action( 'prdctfltr_loading' );

			$this->includes();

			$this->init_hooks();

			do_action( 'prdctfltr_loaded' );
		}

		private function init_hooks() {
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			add_action( 'admin_init', array( $this, 'check_version_582' ), 10 );
			add_action( 'init', array( $this, 'init' ), 0 );
		}

		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		public function includes() {

			include_once( 'lib/pf-characteristics.php' );
			include_once( 'lib/pf-widget.php' );

			if ( $this->is_request( 'admin' ) ) {

				include_once ( 'lib/pf-settings.php' );
				$purchase_code = get_option( 'wc_settings_prdctfltr_purchase_code', '' );

				if ( $purchase_code ) {
					require 'lib/update/plugin-update-checker.php';
					$pf_check = PucFactory::buildUpdateChecker(
						'http://mihajlovicnenad.com/envato/verify_json.php?k=' . $purchase_code,
						__FILE__
					);
				}

			}

			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_includes();
			}
		}

		public function frontend_includes() {
			include_once( 'lib/pf-frontend.php' );
			include_once( 'lib/pf-shortcode.php' );
			include_once( 'lib/pf-variable-override.php' );
		}

		public function include_template_functions() {

		}

		public function init() {

			do_action( 'before_prdctfltr_init' );

			$this->load_plugin_textdomain();

			do_action( 'after_prdctfltr_init' );

		}

		public function load_plugin_textdomain() {

			$domain = 'prdctfltr';
			$dir = untrailingslashit( WP_LANG_DIR );
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			if ( $loaded = load_textdomain( $domain, $dir . '/plugins/' . $domain . '-' . $locale . '.mo' ) ) {
				return $loaded;
			}
			else {
				load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
			}

		}

		public function setup_environment() {

		}

		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		public function template_path() {
			return apply_filters( 'prdctfltr_template_path', '/templates/' );
		}

		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function plugin_basename() {
			return untrailingslashit( plugin_basename( __FILE__ ) );
		}

		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		public function version() {
			return self::$version;
		}

		function check_version_582() {

			$version = get_option( 'wc_settings_prdctfltr_version', '5.8.1' );

			if ( version_compare( '5.8.2', $version, '>' ) ) {
				add_action( 'admin_init', array( &$this, 'fix_database_582' ), 100 );
			}

		}

		function fix_database_582() {

			global $wpdb;

			$wpdb->query( "update $wpdb->options set autoload='yes' where option_name like '%prdctfltr%';" );
			$wpdb->query( "delete from $wpdb->options where option_name like '_transient_prdctfltr_%';" );
			$wpdb->query( "delete from $wpdb->options where option_name like '_transient_%_prdctfltr_%';" );
			$wpdb->query( "delete from $wpdb->options where option_name like 'wc_settings_prdctfltr_%_end';" );
			$wpdb->query( "delete from $wpdb->options where option_name like 'wc_settings_prdctfltr_%_title' and option_value = '' ;" );
			delete_option( 'wc_settings_prdctfltr_force_categories' );
			delete_option( 'wc_settings_prdctfltr_force_emptyshop' );
			delete_option( 'wc_settings_prdctfltr_force_search' );
			delete_option( 'wc_settings_prdctfltr_caching' );
			delete_option( 'wc_settings_prdctfltr_selected' );
			delete_option( 'wc_settings_prdctfltr_attributes' );
			update_option( 'wc_settings_prdctfltr_version', self::$version, 'yes' );

		}

		function activate() {

			if ( false !== get_transient( 'prdctfltr_default' ) ) {
				delete_transient( 'prdctfltr_default' );
			}

			$active_presets = get_option( 'prdctfltr_templates', array() );

			if ( !empty( $active_presets ) && is_array( $active_presets ) ) {
				foreach( $active_presets as $k => $v ) {
					if ( false !== ( $transient = get_transient( 'prdctfltr_' . $k ) ) ) {
						delete_transient( 'prdctfltr_' . $k );
					}
				}
			}

		}

	}

	function Prdctfltr() {
		return PrdctfltrInit::instance();
	}

	PrdctfltrInit::instance();

endif;

?>