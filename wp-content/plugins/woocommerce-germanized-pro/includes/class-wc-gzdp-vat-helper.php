<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_VAT_Helper {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		
		if ( ! is_admin() || defined( 'DOING_AJAX' ) )
			$this->frontend_hooks();
		
		add_filter( 'woocommerce_localisation_address_formats', array( $this, 'set_vat_field' ), 0, 1 );
		add_filter( 'woocommerce_default_address_fields', array( $this, 'add_vat_field' ), 0, 1 );
		add_filter( 'woocommerce_order_formatted_billing_address', array( $this, 'set_formatted_billing_address' ), 0, 2 );
		add_filter( 'woocommerce_admin_billing_fields', array( $this, 'set_admin_billing_address' ), 0, 1 );
		add_filter( 'woocommerce_get_country_locale', array( $this, 'hide_vat_field' ), 0, 1 );
		add_filter( 'woocommerce_country_locale_field_selectors', array( $this, 'hide_vat_field_js' ), 0, 1 );
		add_filter( 'woocommerce_formatted_address_replacements', array( $this, 'add_vat_address' ), 0, 2 );
		add_filter( 'woocommerce_customer_meta_fields', array( $this, 'add_vat_field_profile' ), 0, 1 );
		add_action( 'edit_user_profile_update', array( $this, 'save_vat_field_profile' ), 5 );
		add_action( 'personal_options_update', array( $this, 'save_vat_field_profile' ), 5 );
		add_filter( 'woocommerce_found_customer_details', array( $this, 'customer_details_load_vat_id' ), 10, 3 );
		add_filter( 'woocommerce_ajax_calc_line_taxes', array( $this, 'calc_order_taxes' ), 0, 4 );
	}

	public function frontend_hooks() {
		add_filter( 'woocommerce_shipping_fields', array( $this, 'remove_vat_field' ), 0, 1 );
		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'set_vat_prices_cart' ), 0, 1 );
		
		// Use cart calculate totals filter and check if we are validating checkout (compatibility to multistep checkout)
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'set_vat_prices_process_checkout' ), 0 );
		// Remove fee taxes if available and vat exempt from displaying in cart totals
		add_filter( 'woocommerce_cart_totals_fee_html', array( $this, 'remove_fee_taxes_cart' ), 10, 2 );
		
		add_filter( 'woocommerce_process_myaccount_field_billing_vat_id', array( $this, 'user_save_vat_id' ), 0, 1 );
		add_action( 'wp_login', array( $this, 'login_vat_exempt' ), 10, 2 );

		// If is VAT exempt (and net prices are used) set tax rounding precision to 2
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'set_tax_rounding' ) );
		// If prices do not include tax, set taxes to zero if vat exempt
		add_filter( 'woocommerce_calc_tax', array( $this, 'set_price_excluding_tax' ), 0, 5 );
		// Set min max prices for variable products to exclude tax if is vat exempt (pre 1.4.8)
		add_filter( 'woocommerce_variation_prices', array( $this, 'set_variable_exempt' ), 10, 3 );
		// Format VAT ID
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'set_vat_id_format' ), 10, 1 );
	}

	public function customer_details_load_vat_id( $customer_data, $user_id, $type_to_load ) {

		if ( 'billing' === $type_to_load ) {
			$customer_data[ 'billing_vat_id' ] = get_user_meta( $user_id, 'billing_vat_id', true );
		}

		return $customer_data;

	}

	public function get_vat_id_prefix_by_country( $country ) {

		$country = strtoupper( $country );

		$map = array(
			'GR' => 'EL'
		);

		if ( isset( $map[ $country ] ) )
			return $map[ $country ];

		return $country;
	}

	public function get_vat_id_from_string( $number, $expected_country = '' ) {

		$number = trim( preg_replace( "/[^a-z0-9.]+/i", "", sanitize_text_field( $number ) ) );

		if ( ! empty( $expected_country ) ) {
			$expected_country = $this->get_vat_id_prefix_by_country( $expected_country );
		}

		$maybe_country = substr( $number, 0, 2 );

		if ( empty( $expected_country ) ) {

			preg_match( "/^([A-Z]+)/", $maybe_country, $matches );
			
			if ( ! empty( $matches ) ) {
				$expected_country = $maybe_country;
				$number = substr( $number, 2 );
			} else {
				$expected_country = $this->get_vat_id_prefix_by_country( WC()->countries->get_base_country() );
			}
		} else if ( $maybe_country == $this->get_vat_id_prefix_by_country( $expected_country ) ) {
			$number = substr( $number, 2 );
		}

		return array( 
			"number" 	=> $number,
			"country" 	=> $expected_country,
		);

	}

	public function set_vat_id_format( $posted ) {
		if ( isset( $posted['billing_vat_id'] ) && ! empty( $posted['billing_vat_id'] ) ) {

			$country = ( isset( $posted['billing_country'] ) ? $posted['billing_country'] : WC()->countries->get_base_country() );
			$number = wc_clean( $posted['billing_vat_id'] );

			$elements = $this->get_vat_id_from_string( $number, $country );

			WC()->checkout()->posted['billing_vat_id'] = $elements[ 'country' ] . $elements[ 'number' ];
		}
	}

	public function set_variable_exempt( $prices_array, $product, $display ) {
		if ( WC()->customer && WC()->customer->is_vat_exempt() ) {
			foreach ( $prices_array as $type => $variations ) {
				foreach ( $variations as $variation_id => $price ) {
					$variation = $product->get_child( $variation_id );
					$prices_array[ $type ][ $variation_id ] = $variation->get_price_excluding_tax( 1, $price );
				}
				asort( $prices_array[ $type ] );
			}
		}
		return $prices_array;
	}

	public function remove_fee_taxes_cart( $cart_totals_fee_html, $fee ) {
		$cart_totals_fee_html = ( 'excl' == WC()->cart->tax_display_cart || ( WC()->customer && WC()->customer->is_vat_exempt() ) ) ? wc_price( $fee->amount ) : wc_price( $fee->amount + $fee->tax );
		return $cart_totals_fee_html;
	}

	public function set_price_excluding_tax( $taxes, $price, $rates, $price_includes_tax, $suppress_rounding ) {
		if ( ! wc_prices_include_tax() && ! $price_includes_tax && is_object( WC()->customer ) && WC()->customer->is_vat_exempt() )
			$taxes = array();
		return $taxes;
	}

	public function set_tax_rounding() {
		if ( is_object( WC()->customer ) && WC()->customer->is_vat_exempt() && WC()->cart->prices_include_tax )
			add_filter( 'woocommerce_calc_tax', array( $this, 'tax_round' ), 0, 5 );
	}

	public function tax_round( $taxes, $price, $rates, $price_includes_tax, $suppress_rounding ) {
		$taxes = array_map( 'round', $taxes, array( 2 ) );
		return $taxes;
	}

	public function user_save_vat_id( $vat_id = '' ) {
		
		if ( ! empty( $vat_id ) ) {

			$valid = false;

			$country = ( isset( $_POST[ 'billing_country' ] ) ? wc_clean( $_POST[ 'billing_country' ] ) : WC()->countries->get_base_country() );
			$number = wc_clean( $vat_id );

			$elements = $this->get_vat_id_from_string( $number, $country );

			if ( $this->validate( $elements[ 'country' ], $elements[ 'number' ] ) )
				$valid = true;

			if ( ! $valid ) {

				wc_add_notice( __( 'VAT ID seems to be invalid.', 'woocommerce-germanized-pro' ), 'error' );
				return '';
			
			}

		}

		return $vat_id;

	}

	public function login_vat_exempt( $user_login, $user ) {
		
		if ( get_option( 'woocommerce_gzdp_enable_vat_check_login' ) === 'no' )
			return;

		if ( $vat_id = get_user_meta( $user->ID, 'billing_vat_id', true ) ) {
			
			$elements = $this->get_vat_id_from_string( $vat_id );

			if ( $this->validate( $elements[ 'country' ], $elements[ 'number' ] ) )
				WC()->customer->set_is_vat_exempt( true ); 

		}

	}

	public function save_vat_field_profile(  ) {
		
		if ( isset( $_POST[ 'billing_vat_id' ] ) && ! empty( $_POST[ 'billing_vat_id' ] ) ) {

			$vat_id = sanitize_text_field( $_POST[ 'billing_vat_id' ] );
			$country = ( isset( $_POST[ 'billing_country' ] ) ? $_POST[ 'billing_country' ] : '' );

			$elements = $this->get_vat_id_from_string( $vat_id, $country );

			if ( ! $this->validate( $elements[ 'country' ], $elements[ 'number' ] ) ) {
				add_action( 'user_profile_update_errors', array( $this, 'save_vat_field_profile_error' ), 5, 3 );
			}

		}

	}

	public function save_vat_field_profile_error( $errors, $update, $user ) {
		$errors->add( 'billing_vat_id', __( 'VAT ID seems to be invalid but was still saved. Please check the ID again.', 'woocommerce-germanized-pro' ) );
	}

	public function add_vat_field_profile( $fields ) {

		$fields[ 'billing' ][ 'fields' ][ 'billing_vat_id' ] = array(
			'label'       => __( 'VAT ID', 'woocommerce-germanized-pro' ),
			'description' => '',
		);

		return $fields;

	}

	public function set_admin_billing_address( $fields ) {
		
		$fields[ 'vat_id' ] = array(
			'label' => __( 'VAT ID', 'woocommerce-germanized-pro' ),
			'show'  => false
		);

		return $fields;  
	}

	public function set_formatted_billing_address( $fields = array(), $order ) {
		
		$fields[ 'vat_id' ] = '';
		
		if ( $order->billing_vat_id )
			$fields[ 'vat_id' ] = $order->billing_vat_id;
		
		return $fields;
	}

	public function set_vat_prices_process_checkout() {
		
		$data = array();
		
		if ( is_checkout() && isset( $_POST[ 'billing_vat_id' ] ) ) {
		
			$data[ 'billing_vat_id' ] = $_POST[ 'billing_vat_id' ];
			$this->check_vat_exemption( $data );
		
		}
	}

	public function set_vat_prices_cart( $post_data ) {
		
		// Parse Array
		parse_str( $post_data, $post_data );
		$this->check_vat_exemption( $post_data );

	}

	public function check_vat_exemption( $post_data = array() ) {

		$tax_address = WC()->customer->get_taxable_address();
		WC()->customer->set_is_vat_exempt( false );

		if ( isset( $post_data[ 'billing_vat_id' ] ) && isset( $tax_address[0] ) && ! empty( $tax_address[0] ) && ! empty( $post_data[ 'billing_vat_id' ] ) && $this->country_supports_vat_id( $tax_address[0] ) ) {
			
			$vat_id_elements = $this->get_vat_id_from_string( $post_data[ 'billing_vat_id' ], $tax_address[0] );
			
			if ( WC_GZDP_VAT_Helper::instance()->validate( $vat_id_elements[ 'country' ], $vat_id_elements[ 'number' ] ) ) {
				
				WC()->customer->set_is_vat_exempt( true );    
			
			} else {
			
				wc_add_notice( __( 'VAT ID seems to be invalid.', 'woocommerce-germanized-pro' ), 'error' );
			}
		}
	}

	public function remove_vat_field( $fields ) {
		
		if ( isset( $fields[ 'shipping_vat_id' ] ) )
			unset( $fields[ 'shipping_vat_id' ] );
		
		return $fields;
	}

	public function add_vat_address( $replacements, $args ) {
		
		extract( $args );
		
		if ( isset( $vat_id ) )
			$replacements[ '{vat_id}' ] = $vat_id;
		else
			$replacements[ '{vat_id}' ] = '';
 		
 		return $replacements;
	}

	public function hide_vat_field_js( $fields ) {
		
		$fields[ 'vat_id' ] = '#billing_vat_id_field';
		return $fields;
	
	}

	public function country_supports_vat_id( $country ) {
		
		$supports_vat = false;
		
		if ( WC()->countries->get_base_country() !== $country && in_array( $country, WC()->countries->get_european_union_countries() ) )
			$supports_vat = true;
		
		return apply_filters( 'woocommerce_gzdp_country_supports_vat_id', $supports_vat, $country );
	}

	public function hide_vat_field( $locale ) {

		$applyable = array_merge( WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries() );

		foreach ( $applyable as $country => $name ) {
			
			if ( ! $this->country_supports_vat_id( $country ) ) {
				
				if ( ! isset( $locale[ $country ] ) )
					$locale[ $country ] = array();
				
				$locale[ $country ][ 'vat_id' ] = array( 'required' => false, 'hidden' => true );
			}

		}

		return $locale;
	}

	public function set_vat_field( $countries ) {
		
		foreach ( $countries as $country => $value ) {
			$countries[ $country ] .= "\n{vat_id}";
		}
		
		return $countries;
	}

	public function add_vat_field( $fields ) {

		$fields[ 'vat_id' ] = array(
			'label'       => __( 'VAT ID', 'woocommerce-germanized-pro' ),
			'placeholder' => __( 'VAT ID', 'woocommerce-germanized-pro' ),
			'required'    => $this->vat_field_is_required(),
			'clear'       => true,
			'class'       => array( 'form-row-wide' ),
		);

		return $fields;
	}

	public function vat_field_is_required() {

		// Check if VAT ID is not forced
		if ( 'yes' !== get_option( 'woocommerce_gzdp_force_virtual_product_business' ) )
			return false;

		// If it is forced check whether current cart contains virtual/downloadable product
		$items = ( WC()->cart ? WC()->cart->get_cart() : array() );

		if ( ! empty( $items ) ) {		

			foreach ( $items as $cart_item_key => $values ) {
				
				$_product = wc_get_product( $values[ 'data' ] );
				
				if ( $_product->is_downloadable() || $_product->is_virtual() )
					return true;
			}

		}

		return false;
	}

	public function validate( $country, $number ) {

		$country = $this->get_vat_id_prefix_by_country( $country );

		if ( get_transient( 'vat_id_validated_' . $country . $number ) )
			return true;

		$vat = new WC_GZDP_VAT_Validation();
		
		if ( $vat->check( $country, $number ) ) {

			if ( get_option( 'woocommerce_gzdp_vat_check_cache' ) ) {
				$days = (int) get_option( 'woocommerce_gzdp_vat_check_cache', 7 );
				set_transient( 'vat_id_validated_' . $country . $number, 'yes', $days * DAY_IN_SECONDS );
			}

			return true;
		}
		
		return false;

	}

	/**
	 * Item filter when manually recalculating order taxes to check for vat id
	 *  
	 * @param  array $items    
	 * @param  int $order_id 
	 * @param  string $country  
	 * @param  array $data post data
	 * @return array
	 */
	public function calc_order_taxes( $items, $order_id, $country, $data ) {

		remove_filter( 'get_post_metadata', array( $this, 'product_vat_exempt' ), 0 );

		if ( isset( $data[ 'vat_id' ] ) && $this->country_supports_vat_id( $country ) ) {

			$vat_id_elements = $this->get_vat_id_from_string( sanitize_text_field( $data[ 'vat_id' ] ), $country );

			// Is VAT exempt
			if ( WC_GZDP_VAT_Helper::instance()->validate( $vat_id_elements[ 'country' ], $vat_id_elements[ 'number' ] ) ) {
				// Remove product taxable status
				add_filter( 'get_post_metadata', array( $this, 'product_vat_exempt' ), 0, 4 );
				// Remove order taxes
				add_action( 'woocommerce_saved_order_items', array( $this, 'remove_order_vat' ), 0, 2 );
			}
		}
		return $items;
	}

	public function remove_order_vat( $order_id, $items ) {
		$order = wc_get_order( $order_id );
		$order->remove_order_items( 'tax' );
	}

	/**
	 * Temporarily adds a filter to stop products from being taxable - for admin order tax calculation only
	 */
	public function product_vat_exempt( $metadata, $object_id, $meta_key, $single ) {
		if ( '_tax_status' === $meta_key )
			return 'none';
	}

}
return WC_GZDP_VAT_Helper::instance();