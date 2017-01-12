<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! function_exists( 'wc_gzdp_invoice_download_button' ) ) {

	function wc_gzdp_invoice_download_button( $actions, $order ) {
		
		$invoices = wc_gzdp_get_invoices_by_order( $order );
		
		if ( ! empty( $invoices ) ) {
			foreach ( $invoices as $invoice ) {

				if ( ! in_array( $invoice->type, wc_gzdp_get_invoice_frontend_types() ) )
					continue;

				$actions[ 'invoice_' . $invoice->type ] = array(
					'url' => wc_gzdp_get_invoice_download_url( $invoice->id ),
					'name' => $invoice->get_title(),
				);
			}
		}
		return $actions;
	}

}

if ( ! function_exists( 'wc_gzdp_invoice_download_html' ) ) {

	function wc_gzdp_invoice_download_html( $order_id ) {
		
		$order = wc_get_order( $order_id );
		global $invoices;

		$invoices = wc_gzdp_get_invoices_by_order( $order );

		foreach ( $invoices as $key => $invoice ) {
			if ( ! in_array( $invoice->type, wc_gzdp_get_invoice_frontend_types() ) )
				unset( $invoices[ $key ] );
		}

		$invoices = array_values( $invoices );

		if ( ! empty( $invoices ) )
			wc_get_template( 'invoice/download.php' );
	}

}

?>