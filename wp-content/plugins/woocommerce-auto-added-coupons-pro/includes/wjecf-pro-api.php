<?php

defined('ABSPATH') or die();

// //UNCOMMENT THE FOLLOWING LINE TO PERFORM THE API EXAMPLE (will output in wp_footer): 
// require_once( 'wjecf-pro-api-example.php');

/**
 * Get the instance if the WooCommerce Extended Coupon Features API
 */
function WJECF_API() {
	return WJECF_Pro_API::instance();
}	

/**
 * API Functions for public use
 * 
 * Call the API by using WJECF_API()->api_function();
 * 
 */
class WJECF_Pro_API {

	/**
	 * API 2.3.0
	 * The total amount of the products in the cart that match the coupon restrictions
	 * 
	 * @param int|string|WC_Coupon $coupon The coupon
	 * 
	 * @return int Amount of matching products
	 */
	public function get_quantity_of_matching_products( $coupon ) {
		return WJECF()->get_quantity_of_matching_products( $coupon );
	}

	/**
	 * API 2.3.0
	 * The total value of the products in the cart that match the coupon restrictions
	 *
	 * @param int|string|WC_Coupon $coupon The coupon
	 * 
	 * @return int Subtotal of matching products
	 */
	public function get_subtotal_of_matching_products( $coupon ) {
		return WJECF()->get_subtotal_of_matching_products( $coupon );
	}

	/**
	 * API 2.3.0
	 * Verifies whether the coupon applies to the given product.
	 * This function will return false for Free Products (unlike WC_Coupon->is_valid_for_product )
	 *
	 * @param int|string|WC_Coupon $coupon The coupon
	 * @param WC_Product $product The product
	 * @param array $values Optional cart_item_data
	 *
	 * @return bool True if valid for the product, otherwise false
	 *
	 */
	public function coupon_is_valid_for_product( $coupon, $product, $values = array() ) {
		return WJECF()->coupon_is_valid_for_product( $coupon, $product, $values );
	}

	/**
	 * API 2.3.0
	 * Get array of the selected shipping methods ids.
	 * 
	 * @param int|string|WC_Coupon $coupon_id The coupon id (or coupon_code or a WC_Coupon object)
	 * 
	 * @return array Id's of the shipping methods or an empty array.
	 */	
	public function get_coupon_shipping_method_ids( $coupon ) {
		$coupon_id = $this->get_coupon_id( $coupon );
		return WJECF()->get_coupon_shipping_method_ids( $coupon_id );
	}


	/**
	 * API 2.3.0
	 * Get array of the selected payment method ids.
	 * 
	 * @param int|string|WC_Coupon $coupon_id The coupon id (or coupon_code or a WC_Coupon object)
	 * 
	 * @return array  Id's of the payment methods or an empty array.
	 */	
	public function get_coupon_payment_method_ids( $coupon ) {
		$coupon_id = $this->get_coupon_id( $coupon );
		return WJECF()->get_coupon_payment_method_ids( $coupon_id );		
	}

	/**
	 * API 2.3.0
	 * Get array of the selected customer ids.
	 * 
	 * @param int|string|WC_Coupon $coupon_id The coupon id (or coupon_code or a WC_Coupon object)
	 * @return array  Id's of the customers (users) or an empty array.
	 */	
	public function get_coupon_customer_ids( $coupon ) {	
		$coupon_id = $this->get_coupon_id( $coupon );
		return WJECF()->get_coupon_customer_ids( $coupon_id );
	}

	/**
	 * API 2.3.0
	 * Get array of the selected customer role ids.
	 * 
	 * @param int|string|WC_Coupon $coupon_id The coupon id (or coupon_code or a WC_Coupon object)
	 * @return array  Id's (string) of the customer roles or an empty array.
	 */	
	public function get_coupon_customer_roles( $coupon ) {
		$coupon_id = $this->get_coupon_id( $coupon );
		return WJECF()->get_coupon_customer_roles( $coupon_id );
	}

	/**
	 * API 2.3.0
	 * Get array of the excluded customer role ids.
	 * 
	 * @param int|string|WC_Coupon $coupon_id The coupon id (or coupon_code or a WC_Coupon object)
	 * @return array  Id's (string) of the excluded customer roles or an empty array.
	 */	
	public function get_coupon_excluded_customer_roles( $coupon ) {
		$coupon_id = $this->get_coupon_id( $coupon );
		return WJECF()->get_coupon_excluded_customer_roles( $coupon_id );
	}

// ===========================
// PLUGIN: WJECF_AutoCoupon
// ===========================

	/**
	 * API 2.3.0
	 * Get an array of all the Auto Coupons, in order of priority
	 * 
	 * @return array An array [ coupon_code => WC_Coupon ]
	 */
	public function get_all_auto_coupons() {
		return WJECF()->get_plugin('WJECF_AutoCoupon')->get_all_auto_coupons();
	}

	/**
	 * API 2.3.4
	 * Get an array of all the queued coupons in the session
	 * (Queued coupons are coupons that the customer tried to apply; but was not yet valid)
	 * @param bool $exclude_if_in_cart If true, the coupons that are applied in the cart will not be returned
	 * @return array An array [ coupon_code => WC_Coupon ]
	 */
	public function get_queued_coupons( $exclude_if_in_cart = false ) {
		$coupon_codes = WJECF()->get_plugin('WJECF_Pro_Coupon_Queueing')->get_queued_coupon_codes( $exclude_if_in_cart );
		$coupons = array();
		foreach( $coupon_codes as $coupon_code ) {
			$coupons[ $coupon_code ] = new WC_Coupon( $coupon_code );
		}
		return $coupons;
	}
	
	/**
	 * @deprecated
	 */
	public function get_by_url_coupons() {
		_deprecated_function( 'get_by_url_coupons', '2.3.4', 'get_queued_coupons' );
		return $this->get_queued_coupons();
	}


// ===========================
// PLUGIN: WJECF_Pro_Free_Products
// ===========================

	/**
	 * API 2.3.0
	 * Get array of the free product ids.
	 * 
	 * @param int|string|WC_Coupon $coupon_id The coupon id (or coupon_code or a WC_Coupon object)
	 * 
	 * @return array Id's of the free products or an empty array.
	 */	
	public function get_coupon_free_product_ids( $coupon ) {	
		$coupon_id = $this->get_coupon_id( $coupon );
		return WJECF()->get_plugin('WJECF_Pro_Free_Products')->get_coupon_free_product_ids( $coupon_id );
	}

	/**
	 * API 2.3.0
	 * Checks whether the user must choose a free product if this coupon is applied
	 * 
	 * @param int|string|WC_Coupon $coupon The coupon id (or coupon_code or a WC_Coupon object)
	 * 
	 * @return bool True if the user must select a free product
	 */
	public function must_select_free_product( $coupon ) {
		$coupon_id = $this->get_coupon_id( $coupon );
		return WJECF()->get_plugin('WJECF_Pro_Free_Products')->must_select_free_product( $coupon_id );
	}

	/**
	 * API 2.3.0
	 * 
	 * Get the 'select free gift'-message.
	 * 
	 * @param int|string|WC_Coupon $coupon The coupon id (or coupon_code or a WC_Coupon object)
	 * @param string $context 'raw' or 'display'. If display (default) is used, the translated value will be retrieved
	 * @return string|bool will be false if raw and empty.
	 */
	public function get_select_free_product_message( $coupon, $context = 'display' ) {
		$coupon_id = $this->get_coupon_id( $coupon );
		return WJECF()->get_plugin('WJECF_Pro_Free_Products')->get_select_free_product_message( $coupon_id, $context );
	}

	/**
	 * API 2.3.0
	 * 
	 * Get the 'select a free gift'-coupons that are currently in the cart
	 * 
	 * @return array The WC_Coupon objects
	 */
	public function get_applied_select_free_product_coupons() {
		return WJECF()->get_plugin('WJECF_Pro_Free_Products')->get_applied_select_free_product_coupons();
	}

	/**
	 * API 2.3.0
	 * 
	 * Get the id of the selected free gift for the coupon
	 *
	 * @param int|string|WC_Coupon $coupon The coupon id (or coupon_code or a WC_Coupon object)
	 * 
	 * @return int|bool The free product, or false if none selected
	 */
	public function get_session_selected_product( $coupon ) {
		$coupon_code = $this->get_coupon_code( $coupon );
		return WJECF()->get_plugin('WJECF_Pro_Free_Products')->get_session_selected_product( $coupon_code );

	}
// ===========================
// DEBUGGING
// ===========================

	/**
     * Log a message using the WJECF debugging functions
     * @param string $message 
     */    
    public function log( $message ) {
        WJECF()->log( $message, 1 );
    }

// ===========================
// END OF API FUNCTIONS
// ===========================

	/**
	 * Assure we are having a coupon id (source can be an id, a code, or a WC_Coupon object)
	 * 
	 * @param int|string|WC_Coupon $coupon_id The coupon id (or coupon_code or a WC_Coupon object)
	 * 
	 * @return int The coupon id
	 */
	protected function get_coupon_id( $coupon ) {	
		if ( is_int( $coupon ) ) {
			return $coupon;
		}
		$coupon = WJECF()->get_coupon( $coupon );
		return $coupon->id;
	}

	/**
	 * Assure we are having a coupon code (source can be an code, an id, or a WC_Coupon object)
	 * 
	 * @param int|string|WC_Coupon $coupon_id The coupon code (or id or a WC_Coupon object)
	 * 
	 * @return string The coupon code
	 */
	protected function get_coupon_code( $coupon ) {	
		if ( is_string( $coupon ) ) {
			return $coupon;
		}
		$coupon = WJECF()->get_coupon( $coupon );
		return $coupon->code;
	}

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

}
