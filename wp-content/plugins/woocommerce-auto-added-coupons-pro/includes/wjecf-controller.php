<?php

defined('ABSPATH') or die();

/**
 * The main controller for WooCommerce Extended Coupon Features
 */
class WJECF_Controller {

	// Coupon message codes
	//NOTE: I use prefix 79 for this plugin; there's no guarantee that other plugins don't use the same values!
	const E_WC_COUPON_MIN_MATCHING_SUBTOTAL_NOT_MET  = 79100;
	const E_WC_COUPON_MAX_MATCHING_SUBTOTAL_NOT_MET  = 79101;
	const E_WC_COUPON_MIN_MATCHING_QUANTITY_NOT_MET  = 79102;
	const E_WC_COUPON_MAX_MATCHING_QUANTITY_NOT_MET  = 79103;
	const E_WC_COUPON_SHIPPING_METHOD_NOT_MET        = 79104;
	const E_WC_COUPON_PAYMENT_METHOD_NOT_MET         = 79105;
	const E_WC_COUPON_NOT_FOR_THIS_USER              = 79106;

	protected $debug_mode = false;
	protected $log = array();
	public $options = false;

	//Plugin data
	public $plugin_path; // Has trailing slash
	public $plugin_url; // Has trailing slash
	public $version; // Use for js

	/**
	 * Singleton Instance
	 *
	 * @static
	 * @return Singleton Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	protected static $_instance = null;

	
	public function __construct() {   
		$this->debug_mode = false && defined( 'WP_DEBUG' ) && WP_DEBUG;
		//Paths
		$this->plugin_path = plugin_dir_path( dirname(__FILE__) );
		$this->plugin_url = plugins_url( '/', dirname( __FILE__ ) );

		$filename = $this->is_pro() ? "woocommerce-jos-autocoupon-pro.php" : "woocommerce-jos-autocoupon.php" ;
		
		//Version
		$default_headers = array(
			'Version' => 'Version',
		);
		$plugin_data = get_file_data( $this->plugin_path . $filename, $default_headers, 'plugin' );
		$this->version = $plugin_data['Version'];
	}

	public function start() {
		add_action('init', array( $this, 'init_hook' ));
	}

	public function init_hook() {

		$this->controller_init();
		
		/**
		 * Fires before the WJECF plugins are initialised.
		 * 
		 * Perfect hook for themes or plugins to load custom WJECF plugins.
		 * 
		 * @since 2.3.7
		 **/
		do_action( 'wjecf_init_plugins');

		//Start the plugins
        foreach ( WJECF()->get_plugins() as $name => $plugin ) {
        	if ( $plugin->plugin_is_enabled() ) {

        		foreach( $plugin->get_plugin_dependencies() as $dependency_name ) {
        			$dependency = $this->get_plugin( $dependency_name );
        			if ( ! $dependency || ! $dependency->plugin_is_enabled() ) {
        				error_log('Unable to initialize ' . $name . ' because it requires ' . $dependency_name );
        				continue;
        			}
        		}

	        	$plugin->init_hook();
	        	if ( is_admin() ) {
	        		$plugin->init_admin_hook();
	        	}
        	}
        }
	}

	public function controller_init() {
		if ( ! class_exists('WC_Coupon') ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_woocommerce_not_found' ) );
			return;
		}

		$this->log( "INIT " . ( is_ajax() ? "AJAX" : is_admin() ? "ADMIN" : "FRONTEND" ) . "  " . $_SERVER['REQUEST_URI'] );

		$this->init_options();
		
		//Frontend hooks

		//assert_coupon_is_valid (which raises exception on invalid coupon) can only be used on WC 2.3.0 and up
		if ( WJECF_WC()->check_woocommerce_version('2.3.0') ) {
			add_filter('woocommerce_coupon_is_valid', array( $this, 'assert_coupon_is_valid' ), 10, 2 );
		} else {
			add_filter('woocommerce_coupon_is_valid', array( $this, 'coupon_is_valid' ), 10, 2 );
		}

		add_filter('woocommerce_coupon_error', array( $this, 'woocommerce_coupon_error' ), 10, 3 );
		add_action('woocommerce_coupon_loaded', array( $this, 'woocommerce_coupon_loaded' ), 10, 1);
		add_filter('woocommerce_coupon_get_discount_amount', array( $this, 'woocommerce_coupon_get_discount_amount' ), 10, 5);
        add_action( 'wp_footer', array( $this, 'render_log' ) ); //Log
	}

	protected $plugins = array();

	/**
	 * Load a WJECF Plugin (class name)
	 * @param string $class_name The class name of the plugin
	 * @return bool True if succeeded, otherwise false
	 */
	public function add_plugin( $class_name ) {
		if ( class_exists( $class_name ) && ! isset( $this->plugins[ $class_name ] ) ) {
			$the_plugin = new $class_name();

			foreach( $the_plugin->get_plugin_dependencies() as $dependency ) {
				if ( ! class_exists( $dependency ) ) {
					$this->log( 'Unknown dependency: ' . $dependency . ' for plugin ' . $class_name );
					return false;
				}

				if ( isset( $this->plugins[ $class_name ] ) ) {
					continue; //dependency is al geladen
				}

				$this->add_plugin( $dependency );
			}

			$this->plugins[ $class_name ] = $the_plugin;
			$this->log( 'Loaded plugin: ' . $class_name );
			return true;
		}
		return false;
	}

	public function get_plugins() {
		return $this->plugins;
	}

	/**
	 * Retrieves the WJECF Plugin
	 * @param string $class_name 
	 * @return object|bool The plugin if found, otherwise returns false
	 */
	public function get_plugin( $class_name ) {
		if ( isset( $this->plugins[ $class_name ] ) ) {
			return $this->plugins[ $class_name ];
		} else {
			return false;
		}
	}		
	
	public function init_options() {
        $this->options = get_option( 'wjecf_options' );
		if (false === $this->options) {
			$this->options = array( 'db_version' => 0 );
		}
	}

/* FRONTEND HOOKS */

	/**
	 * Notifies that WooCommerce has not been detected.
	 * @return void
	 */
    public function admin_notice_woocommerce_not_found() {
        $msg = __( 'WooCommerce Extended Coupon Features is disabled because WooCommerce could not be detected.', 'woocommerce-jos-autocoupon' );
        echo '<div class="error"><p>' . $msg . '</p></div>';
    }

	//2.2.2
	public function woocommerce_coupon_loaded ( $coupon ) {
		if ( ! is_admin() ) {
			//2.2.2 Allow coupon even if minimum spend not reached
			if ( get_post_meta( $coupon->id, '_wjecf_allow_below_minimum_spend', true ) == 'yes' ) {
				//HACK: Overwrite the minimum amount with 0 so WooCommerce will allow the coupon
				$coupon->wjecf_minimum_amount_for_discount = $coupon->minimum_amount;
				$coupon->minimum_amount = 0;
			}
		}
	}

	//2.2.2
	public function woocommerce_coupon_get_discount_amount ( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		//2.2.2 No value if minimum spend not reached
		if (isset( $coupon->wjecf_minimum_amount_for_discount ) ) {
			if ( wc_format_decimal( $coupon->wjecf_minimum_amount_for_discount ) > wc_format_decimal( WC()->cart->subtotal ) ) {
				return 0;
			};
		}
		return $discount;
	}

	/**
	 * Overwrite coupon error message, if $err_code is an error code of this plugin
	 * @param string $err Original error message
	 * @param int $err_code Error code
	 * @param WC_Coupon $coupon The coupon
	 * @return string Overwritten error message
	 */
	public function woocommerce_coupon_error( $err, $err_code, $coupon ) {
		switch ( $err_code ) {
			case self::E_WC_COUPON_MIN_MATCHING_SUBTOTAL_NOT_MET:
				$min_price = wc_price( get_post_meta( $coupon->id, '_wjecf_min_matching_product_subtotal', true ) );
				$err = sprintf( __( 'The minimum subtotal of the matching products for this coupon is %s.', 'woocommerce-jos-autocoupon' ), $min_price );
			break;
			case self::E_WC_COUPON_MAX_MATCHING_SUBTOTAL_NOT_MET:
				$max_price = wc_price( get_post_meta( $coupon->id, '_wjecf_max_matching_product_subtotal', true ) );
				$err = sprintf( __( 'The maximum subtotal of the matching products for this coupon is %s.', 'woocommerce-jos-autocoupon' ), $max_price );
			break;
			case self::E_WC_COUPON_MIN_MATCHING_QUANTITY_NOT_MET:
				$min_matching_product_qty = intval( get_post_meta( $coupon->id, '_wjecf_min_matching_product_qty', true ) );
				$err = sprintf( __( 'The minimum quantity of matching products for this coupon is %s.', 'woocommerce-jos-autocoupon' ), $min_matching_product_qty );
			break;
			case self::E_WC_COUPON_MAX_MATCHING_QUANTITY_NOT_MET:
				$max_matching_product_qty = intval( get_post_meta( $coupon->id, '_wjecf_min_matching_product_qty', true ) );
				$err = sprintf( __( 'The maximum quantity of matching products for this coupon is %s.', 'woocommerce-jos-autocoupon' ), $max_matching_product_qty );
			break;
			case self::E_WC_COUPON_SHIPPING_METHOD_NOT_MET:
				$err = __( 'The coupon is not valid for the currently selected shipping method.', 'woocommerce-jos-autocoupon' );
			break;
			case self::E_WC_COUPON_PAYMENT_METHOD_NOT_MET:
				$err = __( 'The coupon is not valid for the currently selected payment method.', 'woocommerce-jos-autocoupon' );
			break;
			case self::E_WC_COUPON_NOT_FOR_THIS_USER:
				$err = sprintf( __( 'Sorry, it seems the coupon "%s" is not yours.', 'woocommerce-jos-autocoupon' ), $coupon->code );
			break;
			default:
				//Do nothing
			break;
		}
		return $err;
	}

	/**
	 * Extra validation rules for coupons.
	 * @param bool $valid 
	 * @param WC_Coupon $coupon 
	 * @return bool True if valid; False if not valid.
	 */
	public function coupon_is_valid ( $valid, $coupon ) {
		try {
			return $this->assert_coupon_is_valid( $valid, $coupon );
		} catch ( Exception $e ) {
			return false;
		}
	}	

	/**
	 * Extra validation rules for coupons. Throw an exception when not valid.
	 * @param bool $valid 
	 * @param WC_Coupon $coupon 
	 * @return bool True if valid; False if already invalid on function call. In any other case an Exception will be thrown.
	 */
	public function assert_coupon_is_valid ( $valid, $coupon ) {

		//Not valid? Then it will never validate, so get out of here
		if ( ! $valid ) {
			return false;
		}
		
		//============================
		//Test if ALL products are in the cart (if AND-operator selected instead of the default OR)
		$products_and = get_post_meta( $coupon->id, '_wjecf_products_and', true ) == 'yes';
		if ( $products_and && sizeof( $coupon->product_ids ) > 1 ) { // We use > 1, because if size == 1, 'AND' makes no difference		
			//Get array of all cart product and variation ids
			$cart_item_ids = array();
			$cart = WC()->cart->get_cart();
			foreach( $cart as $cart_item_key => $cart_item ) {
				$cart_item_ids[] = $cart_item['product_id'];
				$cart_item_ids[] = $cart_item['variation_id'];
			}
            //Filter used by WJECF_WPML hook
			$cart_item_ids = apply_filters( 'wjecf_get_product_ids', $cart_item_ids );

			//check if every single product is in the cart
			foreach( apply_filters( 'wjecf_get_product_ids', $coupon->product_ids ) as $product_id ) {
				if ( ! in_array( $product_id, $cart_item_ids ) ) {
					throw new Exception( WC_Coupon::E_WC_COUPON_NOT_APPLICABLE );
				}
			}		
		}

		//============================
		//Test if products form ALL categories are in the cart (if AND-operator selected instead of the default OR)
		$categories_and = get_post_meta( $coupon->id, '_wjecf_categories_and', true ) == 'yes';
		if ( $categories_and && sizeof( $coupon->product_categories ) > 1 ) { // We use > 1, because if size == 1, 'AND' makes no difference		
			//Get array of all cart product and variation ids
			$cart_product_cats = array();
			$cart = WC()->cart->get_cart();
			foreach( $cart as $cart_item_key => $cart_item ) {
				$cart_product_cats = array_merge ( $cart_product_cats,  wp_get_post_terms( $cart_item['product_id'], 'product_cat', array( "fields" => "ids" ) ) );
			}
            //Filter used by WJECF_WPML hook
			$cart_product_cats = apply_filters( 'wjecf_get_product_cat_ids', $cart_product_cats );
			//check if every single category is in the cart
			foreach( apply_filters( 'wjecf_get_product_cat_ids', $coupon->product_categories ) as $cat_id ) {
				if ( ! in_array( $cat_id, $cart_product_cats ) ) {
					$this->log( $cat_id . " is not in " . print_r($cart_product_cats, true));
					throw new Exception( WC_Coupon::E_WC_COUPON_NOT_APPLICABLE );
				}
			}
		}
		
		//============================
		//Test min/max quantity of matching products
		//
		//For all items in the cart:
		//  If coupon contains both a product AND category inclusion filter: the item is counted if it matches either one of them
		//  If coupon contains either a product OR category exclusion filter: the item will NOT be counted if it matches either one of them
		//  If sale items are excluded by the coupon: the item will NOT be counted if it is a sale item
		//  If no filter exist, all items will be counted

		$multiplier = null; //null = not initialized

		//Validate quantity
		$min_matching_product_qty = intval( get_post_meta( $coupon->id, '_wjecf_min_matching_product_qty', true ) );
		$max_matching_product_qty = intval( get_post_meta( $coupon->id, '_wjecf_max_matching_product_qty', true ) );
		if ( $min_matching_product_qty > 0 || $max_matching_product_qty > 0 ) {
			//Count the products
			$qty = $this->get_quantity_of_matching_products( $coupon );
			if ( $min_matching_product_qty > 0 && $qty < $min_matching_product_qty ) throw new Exception( self::E_WC_COUPON_MIN_MATCHING_QUANTITY_NOT_MET );
			if ( $max_matching_product_qty > 0 && $qty > $max_matching_product_qty ) throw new Exception( self::E_WC_COUPON_MAX_MATCHING_QUANTITY_NOT_MET );
			
			if ( $min_matching_product_qty > 0 ) {
				$multiplier = self::min_value( floor( $qty / $min_matching_product_qty ), $multiplier );
			}
		}	

		//Validate subtotal (2.2.2)
		$min_matching_product_subtotal = floatval( get_post_meta( $coupon->id, '_wjecf_min_matching_product_subtotal', true ) );
		$max_matching_product_subtotal = floatval( get_post_meta( $coupon->id, '_wjecf_max_matching_product_subtotal', true ) );
		if ( $min_matching_product_subtotal > 0 || $max_matching_product_subtotal > 0 ) { 
			$subtotal = $this->get_subtotal_of_matching_products( $coupon );
			if ( $min_matching_product_subtotal > 0 && $subtotal < $min_matching_product_subtotal ) throw new Exception( self::E_WC_COUPON_MIN_MATCHING_SUBTOTAL_NOT_MET );
			if ( $max_matching_product_subtotal > 0 && $subtotal > $max_matching_product_subtotal ) throw new Exception( self::E_WC_COUPON_MAX_MATCHING_SUBTOTAL_NOT_MET );

			if ( $min_matching_product_subtotal > 0 ) {
				$multiplier = self::min_value( floor( $subtotal / $min_matching_product_subtotal ), $multiplier );
			}
		}

		//============================
		//Test restricted shipping methods
		$shipping_method_ids = $this->get_coupon_shipping_method_ids( $coupon->id );
		if ( sizeof( $shipping_method_ids ) > 0 ) {
			$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			$chosen_shipping = empty( $chosen_shipping_methods ) ? '' : $chosen_shipping_methods[0]; 
			$chosen_shipping = explode( ':', $chosen_shipping); //UPS and USPS stores extra data, seperated by colon
			$chosen_shipping = $chosen_shipping[0];
			
			if ( ! in_array( $chosen_shipping, $shipping_method_ids ) ) {
				throw new Exception( self::E_WC_COUPON_SHIPPING_METHOD_NOT_MET );
			}
		}
		
		//============================
		//Test restricted payment methods
		$payment_method_ids = $this->get_coupon_payment_method_ids( $coupon->id );
		if ( sizeof( $payment_method_ids ) > 0 ) {			
			$chosen_payment_method = isset( WC()->session->chosen_payment_method ) ? WC()->session->chosen_payment_method : array();	
			
			if ( ! in_array( $chosen_payment_method, $payment_method_ids ) ) {
				throw new Exception( self::E_WC_COUPON_PAYMENT_METHOD_NOT_MET );
			}
		}			


		//============================
		//Test restricted user ids and roles
		//NOTE: If both customer id and role restrictions are provided, the coupon matches if either the id or the role matches
		$coupon_customer_ids = $this->get_coupon_customer_ids( $coupon->id );
		$coupon_customer_roles = $this->get_coupon_customer_roles( $coupon->id );
		if ( sizeof( $coupon_customer_ids ) > 0 || sizeof( $coupon_customer_roles ) > 0 ) {		
			$user = wp_get_current_user();

			//If both fail we invalidate. Otherwise it's ok
			if ( ! in_array( $user->ID, $coupon_customer_ids ) && ! array_intersect( $user->roles, $coupon_customer_roles ) ) {
				throw new Exception( self::E_WC_COUPON_NOT_FOR_THIS_USER );
			}
		}
		
		//============================
		//Test excluded user roles
		$coupon_excluded_customer_roles = $this->get_coupon_excluded_customer_roles( $coupon->id );
		if ( sizeof( $coupon_excluded_customer_roles ) > 0 ) {		
			$user = wp_get_current_user();

			//Excluded customer roles
			if ( array_intersect( $user->roles, $coupon_excluded_customer_roles ) ) {
				throw new Exception( self::E_WC_COUPON_NOT_FOR_THIS_USER );
			}
		}

		//We use our own filter (instead of woocommerce_coupon_is_valid) for easier compatibility management
		//e.g. WC prior to 2.3.0 can't handle Exceptions; while 2.3.0 and above require exceptions
		do_action( 'wjecf_assert_coupon_is_valid', $coupon );

		if ( $coupon->minimum_amount ) {
			 $multiplier = self::min_value( floor( WC()->cart->subtotal / $coupon->minimum_amount ), $multiplier );
		}


		$this->coupon_multiplier_values[ $coupon->code ] = $multiplier;
		//error_log("multiplier " . $coupon->code . " = " . $multiplier );

		return true;	// VALID!		
	}

	/**
	 * Return the lowest multiplier value
	 */
	private static function min_value( $value, $current_multiplier_value = null ) {
		return ( $current_multiplier_value === null || $value < $current_multiplier_value ) ? $value : $current_multiplier_value;
	}

	/**
	 * The amount of times the minimum spend / quantity / subtotal values are reached
	 * @return int 1 or more if coupon is valid, otherwise 0
	 */
	public function get_coupon_multiplier_value( $coupon ) {
		$coupon = $this->get_coupon( $coupon );

		//If coupon validation was not executed, the value is unknown
		if ( ! isset( $this->coupon_multiplier_values[ $coupon->code ] ) ) {
			if ( ! $this->coupon_is_valid( true, $coupon ) ) {
				return 0;
			}
		}
		$multiplier = $this->coupon_multiplier_values[ $coupon->code ];

		//error_log("get multiplier " . $coupon->code . " = " . $multiplier );
		return $multiplier;
	}

	//Temporary storage
	private $coupon_multiplier_values = array();

	
	/**
	 * (API FUNCTION)
	 * The total amount of the products in the cart that match the coupon restrictions
	 * since 2.2.2-b3
	 */
	public function get_quantity_of_matching_products( $coupon ) {
		$coupon = $this->get_coupon( $coupon );

		$qty = 0;
		foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = $cart_item['data'];			
			if ($this->coupon_is_valid_for_product( $coupon, $_product, $cart_item ) ) {
				$qty += $cart_item['quantity'];
			}
		}
		return $qty;
	}

	/**
	 * (API FUNCTION)
	 * The total value of the products in the cart that match the coupon restrictions
	 * since 2.2.2-b3
	 */
	public function get_subtotal_of_matching_products( $coupon ) {
		$coupon = $this->get_coupon( $coupon );

		$subtotal = 0;
		foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = $cart_item['data'];
			if ($this->coupon_is_valid_for_product( $coupon, $_product, $cart_item ) ) {
				$subtotal += $_product->get_price() * $cart_item['quantity'];
			}
		}
		return $subtotal;
	}

	/**
	 * (API FUNCTION)
	 * Test if coupon is valid for the product 
	 * (this function is used to count the quantity of matching products)
	 */
	public function coupon_is_valid_for_product( $coupon, $product, $values = array() ) {
		//Do not count the free products
		if ( isset( $values['_wjecf_free_product_coupon'] ) ) {
			return false;
		}

		//Save coupon fields that will be temporary overwritten
		$overwritten_values = $this->restore_values( $coupon );
		$overwritten_values['discount_type'] = $coupon->discount_type;

		//$coupon->is_valid_for_product only works for these types
		if ( ! WJECF_WC()->coupon_is_type( $coupon, array( 'fixed_product', 'percent_product' ) ) ) {
			$overwritten_values['discount_type'] = $overwritten_values['type'] = $coupon->discount_type;			
			$coupon->discount_type = $coupon->type = 'fixed_product';
		}

		$valid = $coupon->is_valid_for_product( $product, $values );

		//Restore fields
		foreach( $overwritten_values as $key => $value ) {
			$coupon->$key = $value;
		}

		return $valid;
	}	


	
	// =====================

	/**
	 * Retrieve the id of the product or the variation id if it's a variant.
	 * @Returns int|bool The variation or product id. False if not a valid product
	 */
	public function get_product_or_variation_id( $product ) {
		if ( $product instanceof WC_Product_Variation ) {
			return $product->variation_id;
		} elseif ( $product instanceof WC_Product ) {
			return $product->id;
		} else {
			return false;
		}
	}

	/**
	 * Get a WC_Coupon object
	 * @param WC_Coupon|string $coupon The coupon code or a WC_Coupon object
	 * @return WC_Coupon The coupon object
	 */
	public function get_coupon( $coupon ) {
		if ( is_int( $coupon ) ) {
			global $wpdb;
			$coupon = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE id = %s AND post_type = 'shop_coupon' AND post_status = 'publish'", $coupon ) );
		}
		if ( ! ( $coupon instanceof WC_Coupon ) ) {
			$coupon = new WC_Coupon( $coupon );
		}
		return $coupon;
	}

	/**
	 * Get array of the selected shipping methods ids.
	 * @param  int $coupon_id The coupon id
	 * @return array Id's of the shipping methods or an empty array.
	 */	
	public function get_coupon_shipping_method_ids($coupon_id) {
		$v = get_post_meta( $coupon_id, '_wjecf_shipping_methods', true );
		if ($v == '') {
			$v = array();
		}
		
		return $v;
	}

/**
 * Get array of the selected payment method ids.
 * @param  int $coupon_id The coupon id
 * @return array  Id's of the payment methods or an empty array.
 */	
	public function get_coupon_payment_method_ids($coupon_id) {
		$v = get_post_meta( $coupon_id, '_wjecf_payment_methods', true );
		if ($v == '') {
			$v = array();
		}
		
		return $v;
	}
	
/**
 * Get array of the selected customer ids.
 * @param  int $coupon_id The coupon id
 * @return array  Id's of the customers (users) or an empty array.
 */	
	public function get_coupon_customer_ids($coupon_id) {
		$v = get_post_meta( $coupon_id, '_wjecf_customer_ids', true );
		//$v = array_map( 'intval', explode(",", get_post_meta( $coupon_id, '_wjecf_customer_ids', true ) ) );
		if ($v == '') {
			$v = array();
		} else {
			$v = array_map( 'intval', explode(",", $v ) );
		}
		
		return $v;
	}
	
/**
 * Get array of the selected customer role ids.
 * @param  int $coupon_id The coupon id
 * @return array  Id's (string) of the customer roles or an empty array.
 */	
	public function get_coupon_customer_roles($coupon_id) {
		$v = get_post_meta( $coupon_id, '_wjecf_customer_roles', true );
		if ($v == '') {
			$v = array();
		}
		
		return $v;
	}	

/**
 * Get array of the excluded customer role ids.
 * @param  int $coupon_id The coupon id
 * @return array  Id's (string) of the excluded customer roles or an empty array.
 */	
	public function get_coupon_excluded_customer_roles($coupon_id) {
		$v = get_post_meta( $coupon_id, '_wjecf_excluded_customer_roles', true );
		if ($v == '') {
			$v = array();
		}
		
		return $v;
	}	

	public function is_pro() {
		return $this instanceof WJECF_Pro_Controller;
	}

// ===========================================================================
// START - OVERWRITE INFO MESSAGES
// ===========================================================================

	/**
	 * 2.3.4
	 * If a 'Coupon applied' message is displayed by WooCommerce, replace it by another message (or no message)
	 * @param WC_Coupon $coupon The coupon to replace the message for
	 * @param string $new_message The new message. Set to empty string if no message must be displayed
	 */
	public function start_overwrite_success_message( $coupon, $new_message = '' ) {
		$this->overwrite_coupon_message[ $coupon->code ] = array( $coupon->get_coupon_message( WC_Coupon::WC_COUPON_SUCCESS ) => $new_message );
		add_filter( 'woocommerce_coupon_message', array( $this, 'filter_woocommerce_coupon_message' ), 10, 3 );
	}

	/**
	 * 2.3.4
	 * Stop overwriting messages
	 */
	public function stop_overwrite_success_message() {
		remove_filter( 'woocommerce_coupon_message', array( $this, 'filter_woocommerce_coupon_message' ), 10 );
		$this->overwrite_coupon_message = array();
	}

	private $overwrite_coupon_message = array(); /* [ 'coupon_code' => [ old_message' => 'new_message' ] ] */

	function filter_woocommerce_coupon_message( $msg, $msg_code, $coupon ) {
		if ( isset( $this->overwrite_coupon_message[ $coupon->code ][ $msg ] ) ) {
			$msg = $this->overwrite_coupon_message[ $coupon->code ][ $msg ];
		}
		return $msg;
	}

// ===========================================================================
// END - OVERWRITE INFO MESSAGES
// ===========================================================================


// ===========================================================================
// START - OVERWRITE COUPON DATA
// Allows for non-persistent overwriting of fields during a single PHP call.
// ===========================================================================

	protected $original_coupon_data = array();
	/**
	 * Overwrite a coupon field. Remembers a copy of original value
	 * @param WC_Coupon $coupon 
	 * @param string $key 
	 * @param mixed $value 
	 */
	public function overwrite_value( $coupon, $key, $value ) {
		if ( ! isset( $this->original_coupon_data[ $coupon->code ][ $key ] ) ) { //never overwrite original
			$this->original_coupon_data[ $coupon->code ][ $key ] = $coupon->$key;
		}
		$coupon->$key = $value;
	}

	/**
	 * Retrieve original coupon field value (in the case it is overwritten using overwrite)
	 * @param WC_Coupon $coupon 
	 * @param string $key 
	 * @return mixed The original value
	 */
	public function original_value( $coupon, $key ) {
		if ( isset( $this->original_coupon_data[ $coupon->code ][ $key ] ) ) {
			return $this->original_coupon_data[ $coupon->code ][ $key ];
		} else {
			return $coupon->$key;
		}
	}


	/**
	 * Restore original values to the coupon fields
	 * @param WC_Coupon $coupon 
	 * @param array $keys The fields to restore; will restore all if omitted
	 * @return array Array with the keys and values of the fields that were overwritten
	 */
	public function restore_values( $coupon, $keys = null ) {
		$overwritten_values = array();
		if ( isset( $this->original_coupon_data[ $coupon->code ] ) ) {
			foreach( $this->original_coupon_data[ $coupon->code ] as $key => $value ) {
				if ( $keys === null || in_array( $key, $keys ) ) {
					$overwritten_values[ $key ] = $coupon->$key;
					$coupon->$key = $value;
				}
			}
		}
		return $overwritten_values;
	}

	/**
	 * Restore original value to the coupon field
	 * @param WC_Coupon $coupon 
	 * @param string $key 
	 */
	public function restore_value( $coupon, $key ) {
		$coupon->$key = $this->original_value( $coupon, $key );
		unset( $this->original_coupon_data[ $coupon->code ][ $key ] );
	}

// ===========================================================================
// END - OVERWRITE COUPON DATA
// ===========================================================================



	private $_session_data = null;
	/**
	 * Read something from the session
	 * @param string $key The key for identification
	 * @param any $default The default value (Default: false)
	 * 
	 * @return The saved value if found, otherwise the default value
	 */
	public function get_session( $key, $default = false ) {
		if ( $this->_session_data == null) {
			$this->_session_data = WC()->session->get( '_wjecf_session_data', array() );
		}
		return isset( $this->_session_data[ $key ] ) ? $this->_session_data[ $key ] : $default;
	}

	/**
	 * Save something in the session
	 * 
	 * @param string $key The key for identification
	 * @param anything $value The value to store
	 */
	public function set_session( $key, $value ) {
		if ( $this->_session_data == null) {
			$this->_session_data = WC()->session->get( '_wjecf_session_data', array() );
		}
		$this->_session_data[ $key ] = $value;
		if ( $value !== null ) {
			WC()->session->set( '_wjecf_session_data', $this->_session_data );
		} else {

		}
	}


	/**
	 * Get overwritable template filename
	 * @param string $template_name 
	 * @return string Template filename
	 */
	public function get_template_filename( $template_name ) {
		$template_path = 'woocommerce-auto-added-coupons';

		$plugin_template_path = plugin_dir_path( dirname(__FILE__) ) . 'templates/';

		//Get template overwritten file
		$template = locate_template( trailingslashit( $template_path ) . $template_name );

		// Get default template
		if ( ! $template ) {
			$template = $plugin_template_path . $template_name;
		}

		return $template;
	}

	/**
	 * Include a template file, either from this plugins directory or overwritten in the themes directory
	 * @param type $template_name 
	 * @return type
	 */
	public function include_template( $template_name, $variables = array() ) {
		extract( $variables );
		include( $this->get_template_filename( $template_name ) );
	}
	
	/**
	 * Log message for debugging
	 * @param string $string The message to log
	 * @param int $skip_backtrace Defaults to 0, amount of items to skip in backtrace to fetch class and method name
	 */
	public function log( $string, $skip_backtrace = 0) {
		if ( $this->debug_mode ) {
			$nth = 1 + $skip_backtrace;
			$bt = debug_backtrace();
			$class = $bt[$nth]['class'];
			$function = $bt[$nth]['function'];

			$row = array(
				'time' => time(),
				'class' => $class,
				'function' => $function,
				'filter' => current_filter(),
				'message' => $string,
			);
			$this->log[] = $row;
			error_log(  $row['filter'] . '   ' . $row['class'] . '::' . $row['function'] . '   ' . 	$row['message']	);
		}
	}

	/**
	 * Output the log as html
	 */
	public function render_log() {
		if ( $this->debug_mode && current_user_can( 'manage_options' ) && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) {
			echo "<table class='soft79_wjecf_log'>";
			foreach( $this->log as $row ) {
				$cells = array(
					date("H:i:s", $row['time']),
					esc_html( $row['filter'] ),
					esc_html( $row['class'] . '::' . $row['function'] ),
					esc_html( $row['message'] ),
				);
				echo "<tr><td>" . implode( "</td><td>", $cells ) . "</td></tr>";
			}
			$colspan = isset( $cells ) ? count( $cells ) : 1;
			echo "<tr><td colspan='" . $colspan . "'>Current coupons in cart: " . implode( ", ", WC()->cart->applied_coupons ) . "</td></tr>";
			echo "</table>";
		}
	}

}
