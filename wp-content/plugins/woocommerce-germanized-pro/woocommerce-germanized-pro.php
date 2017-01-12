<?php
/**
 * Plugin Name: WooCommerce Germanized Pro
 * Plugin URI: https://www.vendidero.de/woocommerce-germanized
 * Description: Extends WooCommerce Germanized with professional features such as PDF invoices, legal text generators and many more.
 * Version: 1.5.8
 * Author: Vendidero
 * Author URI: https://vendidero.de
 * Requires at least: 3.8
 * Tested up to: 4.6
 * Requires at least WooCommerce: 2.4
 * Tested up to WooCommerce: 2.6
 * Requires at least WooCommerce Germanized: 1.6
 * Tested up to WooCommerce Germanized: 1.7
 *
 * Text Domain: woocommerce-germanized-pro
 * Domain Path: /i18n/languages/
 *
 * @author Vendidero
 */
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'WooCommerce_Germanized_Pro' ) ) :

final class WooCommerce_Germanized_Pro {

	/**
	 * Current WooCommerce Germanized Version
	 *
	 * @var string
	 */
	public $version = '1.5.8';

	/**
	 * Single instance of WooCommerce Germanized Main Class
	 *
	 * @var object
	 */
	protected static $_instance = null;

	/**
	 * @var WC_GZDP_Invoice_Factory
	 */
	public $invoice_factory = null;

	public $contract_helper = null;

	public $multistep_checkout = null;

	public $pdf_helper = null;

	public $plugin_file;

	/**
	 * Main WooCommerceGermanized Instance
	 *
	 * Ensures that only one instance of WooCommerceGermanized is loaded or can be loaded.
	 *
	 * @static
	 * @see WC_germanized()
	 * @return WooCommerceGermanized - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-germanized-pro' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-germanized-pro' ), '1.0' );
	}

	/**
	 * Global getter
	 *
	 * @param string  $key
	 * @return mixed
	 */
	public function __get( $key ) {
		return self::$key;
	}

	/**
	 * adds some initialization hooks and inits WooCommerce Germanized
	 */
	public function __construct() {

		// Auto-load classes on demand
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->plugin_file = plugin_basename( __FILE__ );

		// Always load textdomain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ), 0 );

		// Vendidero Helper Functions
		include_once( 'includes/vendidero/vendidero-functions.php' );

		// Check if dependecies are installed and up to date
		$init = WC_GZDP_Dependencies::instance( $this );
		
		if ( ! $init->is_loadable() )
			return;

		// Make sure Germanized Pro is being loaded after Germanized has been loaded
		add_action( 'woocommerce_germanized_loaded', array( $this, 'load' ) );
	}

	public function load() {

		// Define constants
		$this->define_constants();

		do_action( 'woocommerce_gzdp_before_load' );

		$this->includes();
		$this->load_modules();

		// Hooks
		add_action( 'init', array( $this, 'init' ), 0 );
		add_filter( 'plugin_action_links_' . $this->plugin_file, array( $this, 'action_links' ) );
		add_action( 'pre_get_posts', array( $this, 'hide_attachments' ) );

		add_filter( 'vendidero_updateable_products', array( $this, 'register_updates' ) );
		
		// Loaded action
		do_action( 'woocommerce_gzdp_loaded' );
	}

	/**
	 * Init WooCommerceGermanized when WordPress initializes.
	 */
	public function init() {
		
		// Before init action
		do_action( 'before_woocommerce_gzdp_init' );
		add_filter( 'woocommerce_locate_template', array( $this, 'filter_templates' ), 5, 3 );
		add_filter( 'woocommerce_email_subject_customer_processing_order', array( $this, 'set_processing_order_subject' ), 0, 2 );
		
		// Init action
		do_action( 'woocommerce_gzdp_init' );
	}

	/**
	 * Set processing order subject to static text for legal purposes (terms generator)
	 *  
	 * @param string $subject 
	 * @param object $order   
	 */
	public function set_processing_order_subject( $subject, $order ) {
		$email = WC()->mailer()->emails[ 'WC_Email_Customer_Processing_Order' ];
		return $email->format_string( __( 'Your {site_title} order receipt', 'woocommerce-germanized-pro' ) );
	}

	/**
	 * Define WC_Germanized Constants
	 */
	private function define_constants() {
		define( 'WC_GERMANIZED_PRO_PLUGIN_FILE', __FILE__ );
		define( 'WC_GERMANIZED_PRO_VERSION', $this->version );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {

		include_once 'includes/class-wc-gzdp-install.php';
		include_once 'includes/wc-gzdp-order-functions.php';
		include_once 'includes/abstracts/abstract-wc-gzdp-theme.php';
		include_once 'includes/abstracts/abstract-wc-gzdp-checkout-step.php';
		include_once 'includes/abstracts/abstract-wc-gzdp-post-pdf.php';

		if ( is_admin() ) {
			include_once 'includes/admin/class-wc-gzdp-admin.php';
			include_once 'includes/admin/settings/class-wc-gzdp-settings.php';
		}

		if ( defined( 'DOING_AJAX' ) ) 
			$this->ajax_includes();

		include_once 'includes/class-wc-gzdp-assets.php';

		// API
		include_once ( 'includes/api/class-wc-gzdp-rest-api.php' );

		// Unit Price Helper
		include_once( 'includes/class-wc-gzdp-unit-price-helper.php' );

		// Include PDF Helper if necessary
		if ( get_option( 'woocommerce_gzdp_invoice_enable' ) != 'no' || get_option( 'woocommerce_gzdp_legal_page_enable' ) != 'no' )
			include_once 'includes/class-wc-gzdp-pdf-helper.php';

		include_once ( 'includes/class-wc-gzdp-wpml-helper.php' );

	}

	/**
	 * Include required ajax files.
	 */
	public function ajax_includes() {
		include_once 'includes/class-wc-gzdp-ajax.php';
	}

	public function load_modules() {
		
		if ( get_option( 'woocommerce_gzdp_invoice_enable' ) != 'no' )
			$this->load_invoice_module();
		
		if ( get_option( 'woocommerce_gzdp_enable_vat_check' ) == 'yes' )
			$this->load_vat_module();
		
		$this->load_checkout_module();
		$this->load_legal_pdf_module();
		$this->load_contract_module();
		$this->load_generator_module();
		$this->load_theme_module();
	}

	/**
	 * Auto-load WC_Germanized classes on demand to reduce memory consumption.
	 *
	 * @param mixed   $class
	 * @return void
	 */
	public function autoload( $class ) {
		$path = $this->plugin_path() . '/includes/';
		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', $class ) . '.php';
		
		if ( strpos( $class, 'wc_gzdp_pdf' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/abstracts/';
			$file = str_replace( 'class-', 'abstract-', $file );
		} elseif ( strpos( $class, 'wc_gzdp_meta_box' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/admin/meta-boxes/';
		} elseif ( strpos( $class, 'wc_gzdp_admin' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/admin/';
		} elseif ( strpos( $class, 'wc_gzdp_theme' ) === 0 ) {
			$path = $this->plugin_path() . '/themes/';
		} elseif ( strpos( $class, 'wc_gzdp_checkout_step' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/checkout/';
		} else if ( strpos( $class, 'wc_gzdp_checkout_compatibility' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/checkout/compatibility/';
		}
		
		if ( $path && is_readable( $path . $file ) ) {
			include_once $path . $file;
			return;
		}
	}

	/**
	 * Filter WooCommerce templates to look for woocommerce-germanized-pro templates
	 *  
	 * @param  string $template      
	 * @param  string $template_name 
	 * @param  strin $template_path 
	 * @return string                
	 */
	public function filter_templates( $template, $template_name, $template_path ) {
		
		$template_path = $this->template_path();

		$template_name = apply_filters( 'woocommerce_gzdp_template_name', $template_name );

		// Check Theme
		$theme_template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);

		// Load Default
		if ( ! $theme_template && file_exists( apply_filters( 'woocommerce_gzdp_default_plugin_template', $this->plugin_path() . '/templates/' . $template_name, $template_name ) ) ) {
			
			$legacy_versions = array( '2.4' );
			$wc_version = substr( get_option( 'woocommerce_version', '2.6.0' ), 0, -2 );
			$template = apply_filters( 'woocommerce_gzdp_default_plugin_template', $this->plugin_path() . '/templates/' . $template_name, $template_name );

			foreach ( $legacy_versions as $legacy_version ) {

				if ( version_compare( $wc_version, $legacy_version, '<=' ) ) {

					$wc_template_legacy = str_replace( '.php', '', $template_name ) . '-' . str_replace( '.', '-', $legacy_version ) . '.php';
	
					// Load older version of the template if exists
					if ( file_exists( $this->plugin_path() . '/templates/' . $wc_template_legacy ) ) {
						$template = $this->plugin_path() . '/templates/' . $wc_template_legacy;
					}
				}
			}

		} else if ( $theme_template ) {
		
			$template = $theme_template;
		
		}
		
		return apply_filters( 'woocommerce_gzdp_filter_template', $template, $template_name, $template_path );

	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the language path
	 *
	 * @return string
	 */
	public function language_path() {
		return $this->plugin_path() . '/i18n/languages';
	}

	/**
	 * Path to template folter
	 *  
	 * @return string 
	 */
	public function template_path() {
		return apply_filters( 'woocommerce_gzd_template_path', 'woocommerce-germanized-pro/' );
	}

	public function load_invoice_module() {
		
		add_action( 'init', array( $this, 'add_query_vars' ), 15 );
		
		add_action( 'after_setup_theme', array( $this, 'include_invoice_template_functions' ), 12 );
		add_action( 'init', array( $this, 'init_invoice_module' ), 1 );

		include_once 'includes/abstracts/abstract-wc-gzdp-invoice.php';
		include_once 'includes/wc-gzdp-invoice-functions.php';
		include_once 'includes/class-wc-gzdp-invoice-helper.php';
		include_once 'includes/class-wc-gzdp-invoice-shortcodes.php';
		include_once 'includes/class-wc-gzdp-download-handler.php';

		if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && wc_gzdp_get_invoice_frontend_types() )
			include_once 'includes/wc-gzdp-invoice-template-hooks.php';

		// Post types
		include_once 'includes/class-wc-gzdp-post-types.php';
	}

	public function include_invoice_template_functions() {
		if ( ! is_admin() || defined( 'DOING_AJAX' ) )
			include_once 'includes/wc-gzdp-invoice-template-functions.php';
	}

	public function init_invoice_module() {
		add_filter( 'woocommerce_locate_core_template', array( $this, 'email_templates' ), 5, 3 );
		add_filter( 'woocommerce_email_classes', array( $this, 'add_emails' ) );
		$this->invoice_factory = new WC_GZDP_Invoice_Factory();    
	}

	public function load_generator_module() {
		if ( is_admin() )
			include_once 'includes/admin/class-wc-gzdp-admin-generator.php';
	}

	public function load_vat_module() {
		include_once 'includes/class-wc-gzdp-vat-helper.php';
	}

	public function load_contract_module() {
		if ( get_option( 'woocommerce_gzdp_contract_after_confirmation' ) == "yes" )
			$this->contract_helper = include_once 'includes/class-wc-gzdp-contract-helper.php';
	}

	public function load_checkout_module() {
		$this->multistep_checkout = include_once 'includes/class-wc-gzdp-multistep-checkout.php';
	}

	public function load_legal_pdf_module() {
		$this->legal_pdf_helper = include_once 'includes/class-wc-gzdp-legal-page-helper.php';
	}

	public function load_theme_module() {
		// Enables theme customizations for Germanized 1.3.2
		if ( version_compare( get_option( 'woocommerce_gzd_version' ), '1.3.2', '>=' ) )
			include_once $this->plugin_path() . '/includes/class-wc-gzdp-theme-helper.php';
	}

	public function register_updates( $products ) {
		array_push( $products, vendidero_register_product( $this->plugin_file, '148' ) );
		return $products;
	}

	public function get_vd_product() {
		return VD()->get_product( $this->plugin_file );
	}

	public function add_query_vars() {
		if ( function_exists( 'WC' ) && ! isset( WC()->query->query_vars[ 'view-bill' ] ) ) {
			// Manually add endpoint
			add_rewrite_endpoint( 'view-bill', EP_PAGES );
			// Add through WC()->query for WPML compatibility
			WC()->query->query_vars[ 'view-bill' ] = 'view-bill';
		}
	}

	/**
	 * Hide invoices from attachment listings
	 *  
	 * @param  object $query 
	 * @return object        
	 */
	public function hide_attachments( $query ) {
		
		$filter = false;
		$post_type = $query->get( 'post_type' );

		if ( $query->is_attachment || ( ! is_array( $post_type ) && $post_type == 'attachment' ) || ( is_array( $post_type ) && in_array( 'attachment', $post_type ) ) )
			$filter = true;

		if ( $filter ) {

			$meta_query = ( $query->get( 'meta_query' ) ? $query->get( 'meta_query' ) : array() );
			
			array_push( $meta_query, array(
				'key' => '_wc_gzdp_private',
				'compare' => 'NOT EXISTS',
			) );
			
			$query->set( 'meta_query', $meta_query );

		}

		return $query;
	}

	/**
	 * Add Custom Email templates
	 *
	 * @param array   $mails
	 * @return array
	 */
	public function add_emails( $mails ) {
		$mails[ 'WC_Email_Customer_Invoice' ] 	    			= include 'includes/emails/class-wc-gzdp-email-customer-invoice-simple.php';
		$mails[ 'WC_GZDP_Email_Customer_Invoice_Cancellation' ] = include 'includes/emails/class-wc-gzdp-email-customer-invoice-cancellation.php';
		return $mails;
	}

	/**
	 * Filter Email template to include WooCommerce Germanized template files
	 *
	 * @param string  $core_file
	 * @param string  $template
	 * @param string  $template_base
	 * @return string
	 */
	public function email_templates( $core_file, $template, $template_base ) {
		
		if ( ! file_exists( $template_base . $template ) && file_exists( $this->plugin_path() . '/templates/' . $template ) )
			$core_file = $this->plugin_path() . '/templates/' . $template;
		
		return apply_filters( 'woocommerce_germanized_pro_email_template_hook', $core_file, $template, $template_base );
	}

	public function get_upload_dir() {

		$this->set_upload_dir_filter();
		$upload_dir = wp_upload_dir();
		$this->unset_upload_dir_filter();

		return $upload_dir;
	}

	public function get_relative_upload_path( $path ) {

		$this->set_upload_dir_filter();
		$path = _wp_relative_upload_path( $path );
		$this->unset_upload_dir_filter();

		return $path;
	}

	public function set_upload_dir_filter() {
		add_filter( 'upload_dir', array( $this, "filter_upload_dir" ), 0, 1 );
	}

	public function unset_upload_dir_filter() {
		remove_filter( 'upload_dir', array( $this, "filter_upload_dir" ), 0 );
	}

	public function create_upload_folder() {
		
		$dir = WC_germanized_pro()->get_upload_dir();

		if ( ! @is_dir( $dir[ 'basedir' ] ) )
			@mkdir( $dir[ 'basedir' ] );

		if ( ! @is_dir( trailingslashit( $dir[ 'basedir' ] ) . 'fonts' ) )
			@mkdir( trailingslashit( $dir[ 'basedir' ] ) . 'fonts' );

		if ( ! file_exists( trailingslashit( $dir[ 'basedir' ] ) . '.htaccess' ) ) 
			@file_put_contents( trailingslashit( $dir[ 'basedir' ] ) . '.htaccess', 'deny from all' );

		if ( ! file_exists( trailingslashit( $dir[ 'basedir' ] ) . 'index.php' ) )
			@touch( trailingslashit( $dir[ 'basedir' ] ) . 'index.php' );

	}

	public function filter_upload_dir( $args ) {
		
		$upload_base = trailingslashit( $args[ 'basedir' ] );
		$upload_url = trailingslashit( $args[ 'baseurl' ] );
		
		$args[ 'basedir' ] = apply_filters( 'wc_germanized_pro_upload_path', $upload_base . 'wc-gzdp' );
		$args[ 'baseurl' ] = apply_filters( 'wc_germanized_pro_upload_url', $upload_url . 'wc-gzdp' );
		$args[ 'path' ] = $args[ 'basedir' ] . $args[ 'subdir' ];
		$args[ 'url' ] = $args[ 'baseurl' ] . $args[ 'subdir' ];

		return $args;
	}

	/**
	 * Load Localisation files for WooCommerce Germanized.
	 */
	public function load_plugin_textdomain() {
		$domain = 'woocommerce-germanized-pro';
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-germanized-pro' );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/i18n/languages/' );
	}

	/**
	 * Load a single translation by textdomain
	 *
	 * @param string  $path
	 * @param string  $textdomain
	 * @param string  $prefix
	 */
	public function load_translation( $path, $textdomain, $prefix ) {
		if ( is_readable( $path . $prefix . '-de_DE.mo' ) )
			load_textdomain( $textdomain, $path . $prefix . '-de_DE.mo' );
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @param mixed   $links
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=germanized' ) . '">' . __( 'Settings', 'woocommerce-germanized-pro' ) . '</a>',
			'<a href="https://vendidero.de/mein-konto">' . __( 'Support', 'woocommerce-germanized-pro' ) . '</a>',
		), $links );
	}

}

endif;

/**
 * Returns the global instance of WooCommerce Germanized
 */
function WC_germanized_pro() {
	return WooCommerce_Germanized_Pro::instance();
}

$GLOBALS['woocommerce_germanized_pro'] = WC_germanized_pro();

?>
