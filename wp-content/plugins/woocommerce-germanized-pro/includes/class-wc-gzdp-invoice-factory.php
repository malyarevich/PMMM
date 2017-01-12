<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Order Factory Class
 *
 * The WooCommerce order factory creating the right order objects
 *
 * @class 		WC_Order_Factory
 * @version		2.2.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_GZDP_Invoice_Factory {

	public function get_invoice( $the_invoice = false, $type = 'simple' ) {
		global $post;

		if ( false === $the_invoice ) {
			
			$types = wc_gzdp_get_invoice_types( $type );

			$args = array(
				'post_status' => 'auto-draft',
				'post_type' => 'invoice',
				'post_title' => sprintf( __( 'New %s', 'woocommerce-germanized-pro' ), $types[ 'title' ] ),
			);

			$the_invoice = wp_insert_post( $args );
			
			if ( ! is_wp_error( $the_invoice ) )
				update_post_meta( $the_invoice, '_type', $type );
		}
		
		if ( is_numeric( $the_invoice ) )
			$the_invoice = get_post( $the_invoice );

		if ( ! $the_invoice || ! is_object( $the_invoice ) )
			return false;

		$invoice_id  = absint( $the_invoice->ID );
		$invoice_type = ( get_post_meta( $the_invoice->ID, '_type', true ) ? get_post_meta( $the_invoice->ID, '_type', true ) : 'simple' );

		$classname = false;

		if ( $invoice_type = wc_gzdp_get_invoice_types( $invoice_type ) )
			$classname = $invoice_type['class_name'];

		if ( ! class_exists( $classname ) )
			$classname = 'WC_GZDP_Invoice_Simple';

		return new $classname( $the_invoice );
	}
}
