<?php

defined('ABSPATH') or die();

/**
 * 
 * Interface to WooCommerce. Handles version differences / backwards compatibility.
 * 
 * @since 2.3.7.2
 */
class WJECF_WC {

    /**
     * Returns a specific item in the cart.
     *
     * @param string $cart_item_key Cart item key.
     * @return array Item data
	 */
	public function get_cart_item( $cart_item_key ) {		
    	if ( $this->check_woocommerce_version('2.2.9') ) {
			return WC()->cart->get_cart_item( $cart_item_key );
    	}

		return isset( WC()->cart->cart_contents[ $cart_item_key ] ) ? WC()->cart->cart_contents[ $cart_item_key ] : array();
   	}

	/**
	 * Get categories of a product (and anchestors)
	 * @param int $product_id 
	 * @return array product_cat_ids
	 */
	public function wc_get_product_cat_ids( $product_id ) {
		//Since WC 2.5.0
		if ( function_exists( 'wc_get_product_cat_ids' ) ) {
			return wc_get_product_cat_ids( $product_id );
		}

	    $product_cats = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );

	    foreach ( $product_cats as $product_cat ) {
	        $product_cats = array_merge( $product_cats, get_ancestors( $product_cat, 'product_cat' ) );
	    }
	    return $product_cats;
	}	

	/**
	 * Check the type of the coupon
	 * @param WC_Coupon $coupon The coupon to check
	 * @param string|array $type The type(s) we want to check for
	 * @return bool True if the coupon is of the type
	 */
	public function coupon_is_type( $coupon, $type ) {
		//Backwards compatibility 2.2.11
		if ( method_exists( $coupon, 'is_type' ) ) {
			return $coupon->is_type( $type );
		}
		
		return ( $coupon->discount_type == $type || ( is_array( $type ) && in_array( $coupon->discount_type, $type ) ) ) ? true : false;
	}

//ADMIN

	/**
	 * Display a WooCommerce help tip
	 * @param string $tip The tip to display
	 * @return string
	 */
	public function wc_help_tip( $tip ) {
		//Since WC 2.5.0
		if ( function_exists( 'wc_help_tip' ) ) {
			return wc_help_tip( $tip );
		}

		return '<img class="help_tip" style="margin-top: 21px;" data-tip="' . esc_attr( $tip ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
	}

   	/**
   	 * Renders a product selection <input>. Will use either select2 (WC2.3+) or chosen (< WC2.3)
   	 * @param string $dom_id 
   	 * @param string $field_name 
   	 * @param array $selected_ids Array of integers
   	 * @param string|null $placeholder 
   	 * @return void
   	 */
	public function render_admin_product_selector( $dom_id, $field_name, $selected_ids, $placeholder = null ) {
        $product_key_values = array();
        foreach ( $selected_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            if ( is_object( $product ) ) {
                $product_key_values[ esc_attr( $product_id ) ] = wp_kses_post( $product->get_formatted_name() );
            }
        }

		if ( $placeholder === null ) $placeholder = __( 'Search for a product…', 'woocommerce' );

		//In WooCommerce version 2.3.0 chosen was replaced by select2
    	if ( $this->check_woocommerce_version('2.3.0') ) {
			$this->render_admin_select2_product_selector( $dom_id, $field_name, $product_key_values, $placeholder );
		} else {
			$this->render_admin_chosen_product_selector( $dom_id, $field_name, $product_key_values, $placeholder );
		}
	}
	private function render_admin_chosen_product_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
		// $selected_keys_and_values must be an array of [ id => name ]

		echo '<select id="' . esc_attr( $dom_id ) . '" name="' . esc_attr( $field_name ) . '[]" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="' . esc_attr( $placeholder ) . '">';
		foreach ( $selected_keys_and_values as $product_id => $product_name ) {
			echo '<option value="' . $product_id . '" selected="selected">' . $product_name . '</option>';
		}
		echo '</select>';
	}
	private function render_admin_select2_product_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
		// $selected_keys_and_values must be an array of [ id => name ]

		$json_encoded = esc_attr( json_encode( $selected_keys_and_values ) );
	    echo '<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="' 
	    . esc_attr( $field_name ) . '" data-placeholder="' 
	    . esc_attr( $placeholder ) . '" data-action="woocommerce_json_search_products_and_variations" data-selected="' 
	    . $json_encoded . '" value="' . implode( ',', array_keys( $selected_keys_and_values ) ) . '" />';

	}   	


//VERSION

	/**
	 * Check whether WooCommerce version is greater or equal than $req_version
	 * @param string @req_version The version to compare to
	 * @return bool true if WooCommerce is at least the given version
	 */
	public function check_woocommerce_version( $req_version ) {
		return version_compare( $this->get_woocommerce_version(), $req_version, '>=' );
	}	

	private $wc_version = null;
	
	/**
	 * Get the WooCommerce version number
	 * @return string|bool WC Version number or false if WC not detected
	 */
	public function get_woocommerce_version() {
		if ($this->wc_version === null) {
		        // If get_plugins() isn't available, require it
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			
		    // Create the plugins folder and file variables
			$plugin_folder = get_plugins( '/woocommerce' );
			$plugin_file = 'woocommerce.php';
			
			// If the plugin version number is set, return it 
			if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
				$this->wc_version = $plugin_folder[$plugin_file]['Version'];
			} else {
				$this->wc_version = false; // Not found
			}

		}
		return $this->wc_version;
	}

//INSTANCE

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