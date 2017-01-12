<?php
/**
 * Main plugin class
 *
 * @package  woocommerce-bacchus-gold-member
 * @subpackage lib
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 */
class Woocommerce_Bacchus_Gold_Member
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
		add_action( 'plugins_loaded', array( $this, 'wbgm_validate_installation' ) );

		//load plugin textdomain
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		//enqueue necessary scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_global_scripts' ) );

		//add action links
        self::__init();
	}


    /**
     * Initialisation
     *
     * @see  add_action()
     * @since  0.0.0
     */

    private function __init() {
        add_action( 'wp_head', array( $this, 'wbgm_gf_activate' ) );
    }

	/**
	 * Plugin activation hook for gravity forms
	 *
	 * @access  public
	 * @since  0.0.0
	 *
	 * @return void
	 */
	public function wbgm_gf_activate()
	{
        add_action('gform_after_submission', array( $this, 'wbgm_bacchus_gold_activation'), 10, 2);
        add_action('gform_post_submission', array( $this, 'wbgm_bacchus_gold_activation'), 10, 2);
        add_action( 'gform_pre_submission',  array( $this, 'pre_submission_handler'), 10, 1 );
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
	public static function wbgm_activate()
	{
		update_option( '_wbgm_popup_overlay', 1 );
		update_option( '_wbgm_popup_heading', WBGM_Common_Helper::translate( 'Choose your free gift' ) );
		update_option( '_wbgm_invalid_condition_text', WBGM_Common_Helper::translate( 'Gift items removed as gift criteria isnt fulfilled' ) );
		update_option( '_wbgm_btn_adding_to_cart_text', WBGM_Common_Helper::translate( 'Add Gifts' ) );
		update_option( '_wbgm_popup_cancel_text', WBGM_Common_Helper::translate( 'Nein danke' ) );
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
		wp_enqueue_style( 'wbgm-styles', plugins_url( '/css/wbgm-styles.css', dirname( __FILE__ ) ) );

		//enqueue scripts
		wp_enqueue_script( 'wbgm-scripts', plugins_url( '/js/wbgm-scripts.js', dirname( __FILE__ ) ), array( 'jquery' ) );
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
	public function wbgm_validate_installation()
	{
		if( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'wbgm_plugin_required_notice' ) );
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
	public function wbgm_plugin_required_notice()
	{
		WBGM_Common_Helper::error_notice(
			WBGM_Common_Helper::translate(
				'WooCommerce Free Gift plugin requires
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
			WBGM_Common_Helper::$textDomain,
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/../languages/'
		);
	}

}
