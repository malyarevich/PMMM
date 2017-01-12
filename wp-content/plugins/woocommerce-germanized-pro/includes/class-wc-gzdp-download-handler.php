<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Download handler
 *
 * Handle digital downloads.
 *
 * @class 		WC_Download_Handler
 * @version		2.2.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_GZDP_Download_Handler {

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'download_invoice' ) );
	}

	/**
	 * Check if we need to download a file and check validity
	 */
	public static function download_invoice() {
		global $wp;
		
		if ( isset( $wp->query_vars[ 'view-bill' ] ) ) {
		
			$invoice_id = absint( $wp->query_vars[ 'view-bill' ] );
		
			if ( ! empty( $invoice_id ) ) {
		
				$invoice = wc_gzdp_get_invoice( $invoice_id );
		
				if ( $invoice ) {
					
					$order_id = $invoice->order;
					
					if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'view_order', $order_id ) )
						wp_die( __( 'Cheatin huh?', 'woocommerce-germanized-pro' ) );
					
					self::download( $invoice );
				}
			}
		}
	}

	public static function download( $pdf, $force = false ) {
		
		if ( ! $pdf->has_attachment() || ! file_exists( $pdf->get_pdf_path() ) )
			wp_die( __( 'This file does not exist', 'woocommerce-germanized-pro' ) );
		
		$file = $pdf->get_pdf_path();
		$filename = $pdf->get_filename();
		
		self::out( $filename, $file, $force );
	}

	public static function out( $filename, $path, $force ) {
		header( 'Content-type: application/pdf' );
		header( 'Content-Disposition: ' . ( ( get_option( 'woocommerce_gzdp_invoice_download_force' ) == 'yes' || $force ) ? 'attachment' : 'inline' ) . '; filename="' . $filename . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Length: ' . filesize( $path ) );
		header( 'Accept-Ranges: bytes' );
		@readfile( $path );
		exit();
	}
}

WC_GZDP_Download_Handler::init();
