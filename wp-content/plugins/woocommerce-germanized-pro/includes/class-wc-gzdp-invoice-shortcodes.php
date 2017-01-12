<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Adds Invoice Shortcodes
 *
 * @class 		WC_GZDP_Admin_Invoice_Shortcodes
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_GZDP_Invoice_Shortcodes {
	
	/**
	 * Initializes Shortcodes
	 */
	public static function init() {

		// Define shortcodes
		$shortcodes = array(
			'small_business_info'		=> __CLASS__ . '::small_business_info',
			'order_data'				=> __CLASS__ . '::order_data',
			'invoice_data'				=> __CLASS__ . '::invoice_data',
			'order_user_data'			=> __CLASS__ . '::order_user_data',
			'if_order_data'				=> __CLASS__ . '::if_order_data',
			'if_invoice_data'			=> __CLASS__ . '::if_invoice_data',
			'reverse_charge'			=> __CLASS__ . '::reverse_charge',
			'third_party_country'		=> __CLASS__ . '::third_party_country',
		);

		$shortcodes = apply_filters( 'wc_gzdp_invoice_shortcodes', $shortcodes );

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

	}

	public static function get_order() {
		
		$invoice = self::get_invoice();
		
		if ( ! $invoice || ! $invoice->order )
			return false;
		
		return $invoice->get_order();
	}

	public static function get_invoice() {
		global $post_pdf;
		
		if ( ! isset( $post_pdf ) || ! is_object( $post_pdf ) )
			return false;
		
		$invoice = (object) $post_pdf;
		
		return $invoice;
	}

	public static function reverse_charge( $atts ) {

		extract( shortcode_atts( array(), $atts ) );

		if ( ! $order = self::get_order() )
			return;

		$eu = WC()->countries->get_european_union_countries( 'eu_vat' );
		$return = '';

		// Check if vat id exists, that billing_country does not equal shop base country and that base + billing country is in EU (vat related)
		if ( $order->billing_vat_id && $order->billing_country !== WC()->countries->get_base_country() && in_array( WC()->countries->get_base_country(), $eu ) && in_array( $order->billing_country, $eu ) ) {
			$return = get_option( 'woocommerce_gzdp_invoice_reverse_charge_text' );
		}

		return apply_filters( 'woocommerce_gzdp_shortcode_reverse_charge', $return, $atts );

	}

	public static function third_party_country( $atts ) {

		extract( shortcode_atts( array(), $atts ) );

		if ( ! $order = self::get_order() )
			return;

		$eu = WC()->countries->get_european_union_countries( 'eu_vat' );
		$return = '';
		$total_tax = $order->get_total_tax();

		// Check if order taxes equal zero, base country is in EU and billing_country is not in EU
		if ( empty( $total_tax ) && in_array( WC()->countries->get_base_country(), $eu ) && ! in_array( $order->billing_country, $eu ) ) {
			$return = get_option( 'woocommerce_gzdp_invoice_third_party_country_text' );
		}

		return apply_filters( 'woocommerce_gzdp_shortcode_third_party_country', $return, $atts );

	}

	public static function small_business_info( $atts ) {

		$return = '';
		
		if ( get_option( 'woocommerce_gzd_small_enterprise' ) == 'yes' ) {
		
			ob_start();
			wc_get_template( 'global/small-business-info.php' );
			$return = ob_get_clean();
		
		}
		
		return apply_filters( 'woocommerce_gzdp_shortcode_small_enterprise_data', $return, $atts );
	}

	public static function order_data( $atts ) {

		extract( shortcode_atts( array(
			'meta' => '',
		), $atts ) );

		if ( ! $order = self::get_order() )
			return;
		
		$return = '';

		if ( $meta == 'status' ) {
		
			$return = wc_get_order_status_name( $order->get_status() );
		
		} else if ( $meta == 'payment_status' ) {
		
			$return = ( ( $order->paid_date || $order->completed_date ) ? _x( 'paid', 'invoices', 'woocommerce-germanized-pro' ) : _x( 'pending payment', 'invoices', 'woocommerce-germanized-pro' ) );
		
		} else if ( $meta == 'payment_info' ) {
		
			ob_start();
			do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id );
			$return = ob_get_clean();
		
		} else if ( $meta == "id" ) {
		
			$return = $order->get_order_number();
		
		} else if ( strpos( $meta, "date" ) !== false ) {
		
			$data = $order->$meta;
			$return = false;
		
			if ( $data && is_string( $data ) )
				$return = date_i18n( get_option( 'woocommerce_gzdp_invoice_date_format' ), strtotime( $data ) );
		
		} else if ( $meta == 'billing_address' ) {
		
			$return = $order->get_formatted_billing_address();
		
		} else if ( $meta == 'shipping_address' ) {
		
			$return = $order->get_formatted_shipping_address();
		
		} else if ( $meta == 'shipping_method' ) {
		
			$return = $order->get_shipping_method();
		
		} else if ( $meta == 'coupons' ) {
		
			$coupons = $order->get_used_coupons();
			$return = implode( ', ', $coupons );
		
		} else {
		
			$data = $order->$meta;

			// Fallback to get data without prefix "_"
			if ( ! $data )
				$data = get_post_meta( $order->id, $meta, true );
		
			if ( ! $data )
				$return = false;
			elseif ( is_array( $data ) )
				$return = implode( ', ', $data );
			else
				$return = $data;
		
		}
		
		return apply_filters( 'woocommerce_gzdp_shortcode_order_data', $return, $atts, $order );
	}

	public static function order_user_data( $atts ) {

		extract( shortcode_atts( array(
			'meta' => '',
		), $atts ) );

		if ( ! $order = self::get_order() )
			return;

		$user = get_user_by( 'id', $order->customer_user );
		
		if ( $user ) {
			$return = $user->$meta;
		}

		if ( get_user_meta( $order->customer_user, $meta, true ) ) {
			$return = get_user_meta( $order->customer_user, $meta, true );
		}

		return apply_filters( 'woocommerce_gzdp_shortcode_order_user_data', $return, $atts, $order );
	}

	public static function if_order_data( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'meta' 		=> '',
			'compare'   => 'equals',
			'value'		=> '',
		), $atts ) );

		$meta = self::order_data( array( 'meta' => $meta ) );

		$show = false;

		if ( $compare == "equals" ) {
			
			if ( $meta == $value )
				$show = true;

		} else if ( $compare == 'nempty' ) {

			if ( ! empty( $meta ) )
				$show = true;

		} else if ( $compare == "nequals" ) {

			if ( $meta != $value )
				$show = true;

		} else if ( $compare == "greater" ) {

			if ( is_numeric( $meta ) && is_numeric( $value ) && $meta < $value )
				$show = true;

		} else if ( $compare == "lesser" ) {

			if ( is_numeric( $meta ) && is_numeric( $value ) && $meta > $value )
				$show = true;

		}

		if ( $show )
			return do_shortcode( $content );

	}

	public static function invoice_data( $atts ) {

		extract( shortcode_atts( array(
			'meta' => '',
		), $atts ) );

		if ( ! $invoice = self::get_invoice() )
			return;

		$return = '';

		if ( $meta == 'status' ) {
		
			$statuses = wc_gzdp_get_invoice_statuses();
			$return = ( isset( $statuses[ $invoice->get_status() ] ) ? $statuses[ $invoice->get_status() ] : '' );
		
		} else if ( $meta == 'date' ) {
		
			$return = $invoice->get_date( $invoice->get_option( 'date_format' ) );
		
		} else {
		
			$data = $invoice->$meta;
		
			if ( ! $data )
				$return = false;
			elseif ( is_array( $data ) )
				$return = implode( ', ', $data );
			else
				$return = $data;
		}

		return apply_filters( 'woocommerce_gzdp_shortcode_invoice_data', $return, $atts, $invoice );
	}

	public static function if_invoice_data( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'meta' 		=> '',
			'compare'   => 'equals',
			'value'		=> '',
		), $atts ) );

		$meta = self::invoice_data( array( 'meta' => $meta ) );

		$show = false;

		if ( $compare == "equals" ) {
			
			if ( $meta == $value )
				$show = true;

		} else if ( $compare == 'nempty' ) {

			if ( ! empty( $meta ) )
				$show = true;

		} else if ( $compare == "nequals" ) {

			if ( $meta != $value )
				$show = true;

		} else if ( $compare == "greater" ) {

			if ( is_numeric( $meta ) && is_numeric( $value ) && $meta < $value )
				$show = true;

		} else if ( $compare == "lesser" ) {

			if ( is_numeric( $meta ) && is_numeric( $value ) && $meta > $value )
				$show = true;

		}

		if ( $show )
			return do_shortcode( $content );

	}

}

return WC_GZDP_Invoice_Shortcodes::init();