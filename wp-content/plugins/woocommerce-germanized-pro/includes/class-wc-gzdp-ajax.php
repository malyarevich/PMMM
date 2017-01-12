<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce WC_AJAX
 *
 * AJAX Event Handler
 *
 * @class 		WC_AJAX
 * @version		2.2.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_GZDP_AJAX {

	public static function init() {

		$ajax_events = array(
			'checkout_validate_vat' => true,
			'confirm_order'         => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_woocommerce_gzdp_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			if ( $nopriv )
				add_action( 'wp_ajax_nopriv_woocommerce_gzdp_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	public static function confirm_order() {
		if ( current_user_can( 'edit_shop_orders' ) && check_admin_referer( 'woocommerce-gzdp-confirm-order' ) ) {
			$order_id = absint( $_GET['order_id'] );

			if ( $order_id ) {
				$order = wc_get_order( $order_id );
				WC_germanized_pro()->contract_helper->confirm_order( $order->id );
			}
		}

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' ) );
		die();
	}

	public static function checkout_validate_vat() {

		check_ajax_referer( 'update-order-review', 'security' );

		if ( ! isset( $_POST[ 'vat_id' ] ) || ! isset( $_POST[ 'country' ] ) )
			die();

		$country = sanitize_text_field( $_POST[ 'country' ] );
		
		$vat_id = trim( preg_replace("/[^a-z0-9.]+/i", "", sanitize_text_field( $_POST[ 'vat_id' ] ) ) );
		// Strip away country code
		if ( substr( $vat_id, 0, 2 ) == $country )
			$vat_id = substr( $vat_id, 2 );

		if ( WC_GZDP_VAT_Helper::instance()->validate( $country, $vat_id ) ) {
			// Add price vat filters..
			add_filter( 'woocommerce_cart_get_taxes', array( __CLASS__, "remove_taxes" ), 0, 2 );
			echo json_encode( array( 'valid' => true, 'vat_id' => $country . '-' . $vat_id ) );
		} else {
			wc_add_notice( __( 'VAT ID seems to be invalid.', 'woocommerce-germanized-pro' ), 'error' );
			ob_start();
			wc_print_notices();
			$messages = ob_get_clean();
			echo json_encode( array( 'valid' => false, 'error' => $messages ) );
		}

		die();

	}

	public static function remove_taxes( $taxes, $cart ) {
		return array();
	}

}

WC_GZDP_AJAX::init();
