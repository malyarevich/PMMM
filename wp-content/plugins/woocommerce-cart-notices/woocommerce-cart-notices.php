<?php
/**
 * Plugin Name: WooCommerce Cart Notices
 * Plugin URI: http://www.woothemes.com/products/cart-notices/
 * Description: Add dynamic notices above the cart and checkout to help increase your sales!
 * Author: WooThemes / SkyVerge
 * Author URI: http://www.woothemes.com
 * Version: 1.6.0
 * Text Domain: woocommerce-cart-notices
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2016 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Cart-Notices
 * @author    SkyVerge
 * @category  Plugin
 * @copyright Copyright (c) 2012-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'cf25b8df1ffe2fa1880b076aa137f8d7', '18706' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.4.0', __( 'WooCommerce Cart Notices', 'woocommerce-cart-notices' ), __FILE__, 'init_woocommerce_cart_notices', array(
	'minimum_wc_version'   => '2.4.13',
	'minimum_wp_version'   => '4.1',
	'backwards_compatible' => '4.4.0',
) );

function init_woocommerce_cart_notices() {

/**
 * This plugin provides a set of configurable cart notices which can be
 * displayed on the cart/checkout page, or anywhere shortcodes are enabled.
 * This plugin adds a WooCommerce sub menu item named 'Cart Notices.'
 * The following cart notice types are available:
 *
 * * minimum amount - when the cart total is below a threshold
 * * deadline - before a certain time of day
 * * referer - when the visitor originated from a given site
 * * products - when the customer has certain products in their cart
 * * caregories - when the customer has products from certain categories in their cart
 *
 * The notice settings are stored in a custom table named 'cart_notices'.
 * There is a special 'data' column which will contain a serialized array of
 * values which depend on the notice type, this is the data that is specific
 * to each type of notice, and is the following:
 *
 * * minimum amount - 'minimum_order_amount' => float
 * * deadline - 'deadline_hour' => int (1-24), 'deadline_days' => array(0..6 => bool)
 * * referer - 'referer' => string (url)
 * * products - 'product_ids' => array(int)
 * * categories - 'category_ids' => array(int)
 */

class WC_Cart_Notices extends SV_WC_Plugin {


	const VERSION = '1.6.0';

	/** @var WC_Cart_Notices single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'cart_notices';

	/** plugin text domain, DEPRECATED in 1.5.0 */
	const TEXT_DOMAIN = 'woocommerce-cart-notices';

	/** @var string The plugin's id, used for various slugs and such */
	public $id = 'wc-cart-notices';

	/** @var array notices objects @see WC_Cart_Notices::get_notices() */
	private $notices = array();

	/** @var \WC_Cart_Notices_Admin the admin class */
	protected $admin;


	/**
	 * Initialize the main plugin class
	 */
	public function __construct() {

		parent::__construct( self::PLUGIN_ID, self::VERSION );

		// include required files
		$this->includes();

		// for uninstallation: see uninstall.php

		// store the client's referer, if needed
		add_action( 'woocommerce_init', array( $this, 'store_referer' ) );

		// add the notices to the top of the cart/checkout pages
		add_action( 'woocommerce_before_cart_contents', array( $this, 'add_cart_notice' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'add_cart_notice' ) );

		// add the notices shortcodes
		add_shortcode( 'woocommerce_cart_notice', array( $this, 'woocommerce_cart_notices_shortcode' ) );

		// allow shortcodes within notice text
		add_filter( 'woocommerce_cart_notice_minimum_amount_notice', 'do_shortcode' );
		add_filter( 'woocommerce_cart_notice_deadline_notice',       'do_shortcode' );
		add_filter( 'woocommerce_cart_notice_referer_notice',        'do_shortcode' );
		add_filter( 'woocommerce_cart_notice_products_notice',       'do_shortcode' );
		add_filter( 'woocommerce_cart_notice_categories_notice',     'do_shortcode' );

		// ajax search categories handler
		add_action( 'wp_ajax_wc_cart_notices_json_search_product_categories', array( $this, 'woocommerce_json_search_product_categories' ) );
	}


	/**
	 * Include required files
	 *
	 * @since 1.0.7
	 */
	private function includes() {

		if ( is_admin() && ! is_ajax() ) {
			$this->admin_includes();
		}
	}


	/**
	 * Include required admin files
	 *
	 * @since 1.0.7
	 */
	private function admin_includes() {

		// load admin
		$this->admin = $this->load_class( '/includes/admin/class-wc-cart-notices-admin.php', 'WC_Cart_Notices_Admin' );

		// add message handler
		$this->admin->message_handler = $this->get_message_handler();
	}


	/**
	 * Handle localization, WPML compatible
	 *
	 * @since 1.0
	 * @see SV_WC_Plugin::load_translation()
	 */
	public function load_translation() {

		load_plugin_textdomain( 'woocommerce-cart-notices', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
	}


	/**
	 * Invoked after woocommerce has finished loading,
	 * so we know sessions have been started.
	 */
	public function store_referer() {

		// If the referer notice is enabled...
		if ( $this->has_referer_notice() && isset( $_SERVER['HTTP_REFERER'] ) ) {

			$referer_host = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST );

			// ...and the referer host does not match the site...
			if ( $referer_host && $referer_host !== parse_url( site_url(), PHP_URL_HOST ) ) {

				// ...record it in the session
				WC()->session->wc_cart_notice_referer = $referer_host;
			}
		}
	}


	/** Frontend ************************************************************/


	/**
	 * Add any available cart notices
	 */
	public function add_cart_notice() {

		$messages = array();

		foreach ( $this->get_notices() as $notice ) {
			// build the notices based on the notice types.  Any notices that require arguments are handled specially
			$args = array();
			if ( 'minimum_amount' === $notice->type ) {
				$args['cart_contents_total'] = $this->get_cart_total();
			}
			if ( $notice->enabled && method_exists( $this, 'get_' . $notice->type . '_notice' ) && ( $message = $this->{ 'get_' . $notice->type . '_notice' }( $notice, $args ) ) ) {
				$messages[] = $message;
			}
		}

		echo implode( "\n", $messages );
	}


	/** Shortcode ************************************************************/


	/**
	 * WooCommerce Cart Notices shortcode handler
	 *
	 * @param $atts array associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public function woocommerce_cart_notices_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'type' => '',
			'name' => '',
		), $atts ) );

		if ( ! $type && ! $name ) {
			$type = 'all';
		}

		$messages = array();

		foreach ( $this->get_notices() as $notice ) {

			do_action( 'wc_cart_notices_process_notice_before', $notice );

			if ( 'all' === $type || $type === $notice->type || 0 === strcasecmp( $name, $notice->name ) ) {
				// build the notices based on the notice types.  Any notices that require arguments are handled specially
				$args = array();
				if ( 'minimum_amount' === $notice->type ) {
					$args['cart_contents_total'] = $this->get_cart_total();
				}

				if ( $notice->enabled && method_exists( $this, 'get_' . $notice->type . '_notice' ) && ( $message = $this->{'get_' . $notice->type . '_notice'}( $notice, $args ) ) ) {
					$messages[] = $message;
				}
			}
		}

		return implode( "\n", $messages );
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Cart Notices Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.3.0
	 * @see wc_cart_notices()
	 * @return WC_Cart_Notices
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Get the Admin instance
	 *
	 * @since 1.6.0
	 * @return \WC_Cart_Notices_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Gets the cart contents total (after calculation)
	 *
	 * @return string formatted price
	 */
	private function get_cart_total() {

		if ( ! WC()->cart->prices_include_tax ) {
			// if prices don't include tax, just return the subtotal excluding tax
			$cart_contents_total = WC()->cart->cart_contents_total;
		} else {
			// if prices do include tax, add the tax amount back in
			$cart_contents_total = WC()->cart->cart_contents_total + WC()->cart->tax_total;
		}

		return $cart_contents_total;
	}


	/**
	 * Returns the minimum amount cart notice HTML snippet
	 *
	 * TODO: Perhaps I should be checking the cart total excluding taxes.  Though, is this impossible if prices include taxes?
	 *
	 * @param object $notice the notice settings object
	 * @param array $args associative array of parameters, 'cart_contents_total' is required
	 * @return string minimum amount cart notice
	 */
	public function get_minimum_amount_notice( $notice, $args ) {

		// get the minimum order amount
		$minimum_order_amount     = $this->get_minimum_order_amount( $notice );

		$threshold_order_amount   = isset( $notice->data['threshold_order_amount'] ) ? $notice->data['threshold_order_amount'] : null;

		$order_thresholds = array(
			'minimum_order_amount'   => $minimum_order_amount,
			'threshold_order_amount' => $threshold_order_amount,
		);

		$order_thresholds = apply_filters( 'wc_cart_notices_order_thresholds', $order_thresholds, $notice, $args );

		$minimum_order_amount   = $order_thresholds['minimum_order_amount'];
		$threshold_order_amount = $order_thresholds['threshold_order_amount'];

		// no configured minimum order amount and no minimum order amount on the free shipping method or free shipping method is disabled
		if ( false === $minimum_order_amount ) {
			return false;
		}

		// misconfigured?
		if ( ! $notice->message ) {
			return false;
		}

		// they already meet the minimum order amount
		if ( is_numeric( $minimum_order_amount ) && $args['cart_contents_total'] >= $minimum_order_amount ) {
			return false;
		}

		// if they're below the thereshold order amount, bail with no notice
		if ( is_numeric( $threshold_order_amount ) && $args['cart_contents_total'] < $threshold_order_amount ) {
			return false;
		}

		$message = $notice->message;

		// get the minimum amount notice message, with the amount required, if needed
		$amount_under = wc_price( $minimum_order_amount - $args['cart_contents_total'] );
		if ( false !== strpos( $message, '{amount_under}' ) ) {
			$message = str_replace( '{amount_under}', $amount_under,  $message );
		}

		// add the call to action button/text if used
		$action = '';
		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// add the message variables for the benefit of the filter
		$args['amount_under'] = $amount_under;

		// return the notice
		return apply_filters( 'woocommerce_cart_notice_minimum_amount_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice, $args );
	}


	/**
	 * Returns the deadline notice based on the current time and configuration
	 *
	 * @param object $notice the notice settings object
	 * @return string deadline notice snippet, or false if there is no deadline notice at this time
	 */
	public function get_deadline_notice( $notice ) {

		// misconfigured?
		if ( ! $notice->message || ( false !== strpos( $notice->message, '{time}' ) && ! $notice->data['deadline_hour'] ) ) {
			return false;
		}

		$current_time = current_time( 'timestamp' );

		// enabled for today?
		$day_of_week = date( 'w', $current_time );

		if ( ! isset( $notice->data['deadline_days'][ $day_of_week ] ) || ! $notice->data['deadline_days'][ $day_of_week ] ) {
			return false;
		}

		$message = $notice->message;

		// get the deadline notice message, with the time remaining
		$minutes_of_day = (int) date( 'G', $current_time ) * 60 + (int) date( 'i', $current_time );
		$deadline_minutes = $notice->data['deadline_hour'] * 60;

		// already past the deadline?
		if ( $minutes_of_day > $deadline_minutes ) {
			return false;
		}

		$minutes_remaining = $deadline_minutes - $minutes_of_day;
		$hours = floor( $minutes_remaining / 60 );
		$minutes = $minutes_remaining % 60;

		// format the string
		$deadline_amount = '';

		if ( $hours ) {
			$deadline_amount .= sprintf( _n( '%d hour', '%d hours', $hours, 'woocommerce-cart-notices' ), $hours );
		}
		if ( $minutes ) {
			$deadline_amount .= ( $deadline_amount ? ' ' : '' ) . sprintf( _n( '%d minute', '%d minutes', $minutes, 'woocommerce-cart-notices' ), $minutes );
		}

		// add the time remaining, if required
		if ( false !== strpos( $message, '{time}' ) ) {
			$message = str_replace( '{time}', $deadline_amount,  $message );
		}

		// add the call to action button/text if used
		$action = '';
		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// add the message variables for the benefit of the filter
		$args['time']              = $deadline_amount;   // the formatted string
		$args['minutes_remaining'] = $minutes_remaining; // the number of minutes, for more advanced usage

		// return the notice
		return apply_filters( 'woocommerce_cart_notice_deadline_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-deadline woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice, $args );
	}


	/**
	 * Returns the referer cart notice HTML snippet
	 *
	 * @param object $notice the notice settings object
	 * @return string referer cart notice
	 */
	public function get_referer_notice( $notice ) {

		// get the referer
		if ( ! WC()->session->wc_cart_notice_referer ) {
			return false;
		}

		$client_referer_host = WC()->session->wc_cart_notice_referer;

		// misconfigured?
		if ( ! $notice->message || ! $notice->data['referer'] ) {
			return false;
		}

		$referer      = strpos( $notice->data['referer'], '://' ) === false ? 'http://' . $notice->data['referer'] : $notice->data['referer'];
		$referer_host = parse_url( $referer, PHP_URL_HOST );

		// referer matches?
		if ( $client_referer_host !== $referer_host ) {
			return false;
		}

		$message = $notice->message;

		// add the call to action button/text if used
		$action = '';
		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// return the notice (simple message, no args for this one)
		return apply_filters( 'woocommerce_cart_notice_referer_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-referer woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice );
	}


	/**
	 * Returns the products cart notice HTML snippet
	 *
	 * @param object $notice the notice settings object
	 * @return string products cart notice
	 */
	public function get_products_notice( $notice ) {

		// anything in the cart?
		if ( empty( WC()->cart->cart_contents ) ) {
			return false;
		}

		$all_products = apply_filters( 'wc_cart_notices_products_notice_all_products', false, $notice );

		// misconfigured?
		if ( ! $notice->message || ( empty( $notice->data['product_ids'] ) && ! $all_products ) ) {
			return false;
		}

		// are any of the selected products in the cart?
		$found_product_titles = array();
		$the_products         = array();
		$product_quantity     = 0;

		foreach ( WC()->cart->cart_contents as $cart_item ) {

			// check by main product id as well as variation id (if available).  That way
			//  a message can be set for a whole set of variable products, or for one individually
			$_product_id    = $cart_item['product_id'];
			$_variation_id  = isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] ? $cart_item['variation_id'] : null;

			if ( $all_products || in_array( $_product_id, $notice->data['product_ids'] ) || ( $_variation_id && in_array( $_variation_id, $notice->data['product_ids'] ) ) ) {
				$found_product_titles[ $_product_id ]  = $cart_item['data']->get_title();
				$the_products[]                        = $cart_item['data'];
				$product_quantity                     += $cart_item['quantity'];
			}
		}

		if ( empty( $found_product_titles ) ) {
			return false;
		}

		// any minimum/maximum quantity rules?
		$quantity_met = true;

		if ( isset( $notice->data['minimum_quantity'] ) && is_numeric( $notice->data['minimum_quantity'] ) && $product_quantity < $notice->data['minimum_quantity'] ) {
			$quantity_met = false;
		}
		if ( isset( $notice->data['maximum_quantity'] ) && is_numeric( $notice->data['maximum_quantity'] ) && $product_quantity > $notice->data['maximum_quantity'] ) {
			$quantity_met = false;
		}
		if ( ! $quantity_met ) {
			return false;
		}

		$shipping_country_code = '';
		$shipping_country_name = '';
		if ( isset( $notice->data['shipping_countries'] ) && $notice->data['shipping_countries']
		     && isset( WC()->customer ) && WC()->customer && WC()->customer->get_shipping_country() ) {

			if ( ! in_array( WC()->customer->get_shipping_country(), $notice->data['shipping_countries'] ) ) {
				return false;
			} else {
				// grab the matching country code/name
				$shipping_country_code = WC()->customer->get_shipping_country();
				$shipping_country_name = isset( WC()->countries->countries[ WC()->customer->get_shipping_country() ] ) ? WC()->countries->countries[ WC()->customer->get_shipping_country() ] : $shipping_country_code;
			}
		}

		$message = $notice->message;

		// get the products notice message, with the list of products, if needed
		$products = implode( ', ', $found_product_titles );
		if ( false !== strpos( $message, '{products}' ) ) {
			$message = str_replace( '{products}', $products,  $message );
		}
		if ( false !== strpos( $message, '{shipping_country_code}' ) ) {
			$message = str_replace( '{shipping_country_code}', $shipping_country_code,  $message );
		}
		if ( false !== strpos( $message, '{shipping_country_name}' ) ) {
			$message = str_replace( '{shipping_country_name}', $shipping_country_name,  $message );
		}
		if ( false !== strpos( $message, '{quantity}' ) ) {
			$message = str_replace( '{quantity}', $product_quantity,  $message );
		}
		if ( false !== strpos( $message, '{quantity_under}' ) ) {
			$quantity_under = isset( $notice->data['maximum_quantity'] ) && '' !== $notice->data['maximum_quantity'] ? $notice->data['maximum_quantity'] - $product_quantity + 1 : '';
			if ( $quantity_under < 0 ) {
				$quantity_under = '';
			}
			$message = str_replace( '{quantity_under}', $quantity_under,  $message );
		}
		if ( false !== strpos( $message, '{quantity_over}' ) ) {
			$quantity_over = isset( $notice->data['minimum_quantity'] ) && '' !== $notice->data['minimum_quantity'] ? $product_quantity - $notice->data['minimum_quantity'] + 1 : '';
			if ( $quantity_over < 0 ) {
				$quantity_over = '';
			}
			$message = str_replace( '{quantity_over}', $quantity_over,  $message );
		}

		// add the call to action button/text if used
		$action = '';
		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// add the message variables for the benefit of the filter
		$args['products']     = $products;     // the formatted string
		$args['the_products'] = $the_products; // the product objects, for more advanced usage
		$args['shipping_country_code'] = $shipping_country_code;
		$args['shipping_country_name'] = $shipping_country_name;

		// return the notice
		return apply_filters( 'woocommerce_cart_notice_products_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-products woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice, $args );
	}


	/**
	 * Returns the categories cart notice HTML snippet
	 *
	 * @param object $notice the notice settings object
	 * @return string categories cart notice
	 */
	public function get_categories_notice( $notice ) {

		// anything in the cart?
		if ( empty( WC()->cart->cart_contents ) ) {
			return false;
		}

		// misconfigured?
		if ( ! $notice->message || empty( $notice->data['category_ids'] ) ) {
			return false;
		}

		// are any of the selected categories in the cart?
		$found_category_ids = array();
		$product_names      = array();
		$the_products       = array();

		foreach ( $notice->data['category_ids'] as $category_id ) {

			foreach ( WC()->cart->cart_contents as $cart_item ) {

				if ( has_term( $category_id, 'product_cat', $cart_item['product_id'] ) ) {

					if ( ! in_array( $category_id, $found_category_ids ) ) {
						$found_category_ids[] = $category_id;
					}

					$product_names[ $cart_item['product_id'] ] = $cart_item['data']->get_title();
					$the_products[]                            = $cart_item['data'];
				}
			}
		}

		if ( empty( $found_category_ids ) ) {
			return false;
		}

		$message = $notice->message;

		// get the categories notice message, with the list of products, if needed
		$products = implode( ', ', $product_names );

		if ( false !== strpos( $message, '{products}' ) ) {
			$message = str_replace( '{products}', $products,  $message );
		}

		// get the categories notice message, with the list of categories, if needed
		$category_names = array();
		$the_categories = array();

		foreach ( $found_category_ids as $category_id ) {

			$category = get_term( $category_id, 'product_cat' );
			$category_names[] = $category->name;
			$the_categories[] = $category;

		}

		$category_names = array_unique( $category_names );
		$categories = implode( ', ', $category_names );

		if ( strpos( $message, '{categories}' ) !== false ) {
			$message = str_replace( '{categories}', $categories,  $message );
		}

		// add the call to action button/text if used
		$action = '';

		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// add the message variables for the benefit of the filter
		$args['products']       = $products;       // the formatted string
		$args['the_products']   = $the_products;   // the product objects, for more advanced usage
		$args['categories']     = $categories;     // the formatted string
		$args['the_categories'] = $the_categories; // the category objects, for more advanced usage

		// return the notice
		return apply_filters( 'woocommerce_cart_notice_categories_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-categories woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice, $args );
	}


	/**
	 * Get the minimum order amount.  This is returned from the Cart
	 * Notices plugin settings, if set, otherwise it is returned from
	 * the Free Shipping gateway if enabled and configured.
	 *
	 * @param object $notice the notice settings object
	 * @return float minimum order amount configured, for free shipping, or false otherwise
	 */
	public function get_minimum_order_amount( $notice ) {

		// configured minimum order amount?
		if ( $notice->data['minimum_order_amount'] ) {
			return $notice->data['minimum_order_amount'];
		}

		// load the shipping methods if not already available
		if ( 0 === count( $shipping_methods = WC()->shipping->get_shipping_methods() ) ) {
			$shipping_methods = WC()->shipping->load_shipping_methods();
		}

		// minimum order amount set for free shipping method?
		foreach ( $shipping_methods as $method ) {

			if ( "free_shipping" == $method->id && "yes" == $method->enabled && isset( $method->min_amount ) && $method->min_amount ) {

				return $method->min_amount;
			}
		}

		// no minimum amount configured, return false
		return false;
	}


	/**
	 * Load any notices from the database table
	 * and into the notices member
	 *
	 * @return array of notice objects
	 */
	public function get_notices() {
		global $wpdb;

		// Avoid database table not found errors when plugin is first installed
		// by checking if the plugin option exists
		if ( empty( $this->notices ) && get_option( $this->get_plugin_version_name() ) ) {

			$wpdb->hide_errors();
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cart_notices ORDER BY name ASC" );

			if ( ! empty( $results ) ) {

				foreach ( $results as $key => $result ) {
					$results[ $key ]->data = maybe_unserialize( $results[ $key ]->data );
				}

				$this->notices = $results;
			}
		}

		return $this->notices;
	}


	/**
	 * Returns true if at least one referer notice is enabled
	 *
	 * @return bool true if at least one referer notice is enabled, false otherwise
	 */
	private function has_referer_notice() {

		$notices = $this->get_notices();

		if ( ! empty( $notices ) && is_array( $notices ) ) {

			foreach ( $notices as $notice ) {

				if ( 'referer' === $notice->type && $notice->enabled ) {

					return true;
				}
			}
		}

		return false;
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.2
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Cart Notices', 'woocommerce-cart-notices' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.2
	 * @see SV_WC_Plugin::get_file
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Gets the plugin documentation URL
	 *
	 * @since 1.4.0
	 * @see   SV_WC_Plugin::get_documentation_url()
	 * @return string
	 */
	public function get_documentation_url() {
		return 'http://docs.woothemes.com/document/woocommerce-cart-notices/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since  1.4.0
	 * @see    SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'http://support.woothemes.com/';
	}


	/**
	 * Gets the plugin configuration URL
	 *
	 * @since 1.5.0
	 * @see SV_WC_Plugin::get_settings_link()
	 * @param string $plugin_id optional plugin identifier.  Note that this can be a
	 *        sub-identifier for plugins with multiple parallel settings pages
	 *        (ie a gateway that supports both credit cards and echecks)
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {
		return admin_url( 'admin.php?page=wc-cart-notices' );
	}


	/**
	 * Ajax function to return product categories matching the search term $x
	 *
	 * @param string $x search string
	 *
	 * @return string json encoded array of matching category names, or nothing
	 */
	public function woocommerce_json_search_product_categories( $x = '' ) {

		check_ajax_referer( 'search-product-categories', 'security' );

		$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

		if ( empty( $term ) ) {
			die();
		}

		$args = array(
			'search'     => $term,
			'hide_empty' => 0,
		);

		$categories = get_terms( 'product_cat', $args );

		$found_categories = array();

		if ( $categories ) {

			foreach ( $categories as $category ) {

				$found_categories[ $category->term_id ] = $category->name;
			}
		}

		echo json_encode( $found_categories );

		exit();
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 */
	protected function install() {
		global $wpdb;

		$wpdb->hide_errors();

		// initial install
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {

			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}

			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$table = $wpdb->prefix . 'cart_notices';
		$sql =
			"CREATE TABLE IF NOT EXISTS $table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			name varchar(100) NOT NULL,
			enabled boolean NOT NULL default false,
			type varchar(50) NOT NULL,
			message TEXT NOT NULL,
			action varchar(256) NOT NULL,
			action_url varchar(256) NOT NULL,
			data TEXT NOT NULL,
			date_added DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) " . $collate;
		dbDelta( $sql );
	}


	/**
	 * Handles upgrades
	 *
	 * @since 1.2.3
	 * @see SV_WC_Plugin::upgrade()
	 * @param string $installed_version the currently installed version
	 */
	protected function upgrade( $installed_version ) {

		// old db version option name was removed in 1.2.3
		if ( version_compare( $installed_version, '1.2.3', '<' ) ) {

			delete_option( 'wc_cart_notices_db_version' );
		}
	}


	/** Deprecated methods ******************************************************/


	/**
	 * Handle deprecated properties calls for backwards compatibility
	 *
	 * @since 1.6.0
	 * @param string $property
	 * @return null|void|mixed
	 */
	public function __get( $property ) {

		$called     = __CLASS__ . '::' . $property;
		$main_class = 'wc_cart_notices()';
		$deprecated = $main_class . '->' . $property;

		switch ( $property ) {

			case 'admin' :
				// TODO remove this as part of WC 2.7 compatibility update {FN 2016-05-19}
				_deprecated_function( $deprecated, '1.6.0', $main_class . '->get_admin_instance()' );
				return $this->get_admin_instance();

			default :
				trigger_error( "Call to undefined property {$called}", E_USER_ERROR );
				return null;
		}
	}


} // WC_Cart_Notices


/**
 * Returns the One True Instance of <plugin>
 *
 * @since 1.3.0
 * @return WC_Cart_Notices
 */
function wc_cart_notices() {
	return WC_Cart_Notices::instance();
}


// fire it up!
wc_cart_notices();

} // init_woocommerce_cart_notices()
