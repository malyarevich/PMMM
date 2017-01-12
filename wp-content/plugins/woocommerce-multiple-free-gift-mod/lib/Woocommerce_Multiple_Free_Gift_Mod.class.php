<?php
/**
 * Main plugin class
 *
 * @package  woocommerce-multiple-free-gift-mod
 * @subpackage lib
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0
 */
class Woocommerce_Multiple_Free_Gift_Mod
{
	/**
	 * Constructor
	 *
	 * @see  add_action()
	 * @since  0.0.0
	 */
	public function __construct()
	{
		//check if woocommerce plugin is installed and activated
		add_action( 'plugins_loaded', array( $this, 'wfgm_validate_installation' ) );

		//load plugin textdomain
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		//enqueue necessary scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_global_scripts' ) );

	}

	/**
	 * Plugin activation hook
	 *
	 * @access  public
	 * @since  0.0.0
	 *
	 * @see  add_option()
	 * 
	 * @return void
	 */
	public static function wfgm_activate()
	{
		update_option( '_wfgm_popup_overlay', 1 );
		update_option( '_wfgm_popup_heading', WFGM_Common_Helper::translate( 'Take your free gift' ) );
		update_option( '_wfgm_invalid_condition_text', WFGM_Common_Helper::translate( 'Gift items removed as gift criteria isnt fulfilled' ) );
		update_option( '_wfgm_popup_add_gift_text', WFGM_Common_Helper::translate( 'Add Gifts' ) );
		update_option( '_wfgm_popup_cancel_text', WFGM_Common_Helper::translate( 'No Thanks' ) );
	}

	/**
	 * Enqueue required global styles and scirpts
	 *
	 * @access public
	 * @since  0.0.0
	 *
	 * @see  wp_enqueue_style()
	 *
	 * @return void
	 */
	public function enqueue_global_scripts()
	{
		//enqueue styles
		wp_enqueue_style( 'wfgm-styles', plugins_url( '/css/wfgm-styles.css', dirname( __FILE__ ) ) );

		//enqueue scripts
		wp_enqueue_script( 'wfgm-scripts', plugins_url( '/js/wfgm-scripts.js', dirname( __FILE__ ) ), array( 'jquery' ) );
	}

	/**
	 * Add notice if bbPress plugin is not activated
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @see  add_action()
	 *
	 * @return void
	 */
	public function wfgm_validate_installation()
	{
		if( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'wfgm_plugin_required_notice' ) );
		}
	}

	/**
	 * Error notice: WooCommerce Plugin is required for this plugin to work
	 *
	 * @access public
	 * @since  0.0.0
	 *
	 * @return void
	 */
	public function wfgm_plugin_required_notice()
	{
		WFGM_Common_Helper::error_notice(
			WFGM_Common_Helper::translate(
				'Woocommerce Free Gift Mod plugin requires
				<a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>
				plugin to work. Please make sure that WooCommerce is installed and activated.'
			)
		);
	}

	/**
	 * Load the plugin's textdomain hooked to 'plugins_loaded'.
	 *
	 * @since 0.0.0
	 * @access public
	 *
	 * @see	load_plugin_textdomain()
	 * @see	plugin_basename()
	 * @action	plugins_loaded
	 *
	 * @return	void
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			WFGM_Common_Helper::$textDomain,
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/../languages/'
		);
	}

}
