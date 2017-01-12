<?php

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class WC_GZDP_Invoice_Cancellation extends WC_GZDP_Invoice {

	public function __construct( $invoice ) {
		parent::__construct( $invoice );
		$this->type = 'cancellation';
		$this->parent = false;
		if ( $this->parent_id && get_post( $this->parent_id ) )
			$this->set_parent( $this->parent_id );
	}

	public function set_parent( $invoice_id ) {
		if ( ! get_post( $invoice_id ) )
			return false;
		$invoice = wc_gzdp_get_invoice( $invoice_id );
		update_post_meta( $this->id, '_invoice_parent_id', $invoice->id );
		update_post_meta( $this->id, '_invoice_parent_number', $invoice->get_number() );
		update_post_meta( $this->id, '_invoice_parent_title', $invoice->get_title() );
		$this->parent = $invoice;
	}

	public function negate_string( $val ) {
		return ( substr( $val, 0, 1 ) === '-' ? substr( $val, 1 ) : '-' . $val );
	}

	public function get_totals() {

		$totals = parent::get_totals();
		
		foreach( $totals as $key => $total ) {

			if ( ! isset( $total[ 'invoice_data' ] ) || ( isset( $total[ 'invoice_data' ] ) && ! $total[ 'invoice_data' ] ) ) {
				$totals[ $key ][ 'value' ] = $this->negate_string( $totals[ $key ][ 'value' ] );
			}

		}

		return $totals;
	}

	public function get_title_pdf() {
		$title = parent::get_title_pdf();
		return sprintf( __( '%s <span class="number-smaller">to %s</span>', 'woocommerce-germanized-pro' ), $title, $this->parent_title );
	}

	public function refresh( $data = array(), $order = NULL ) {
		global $wpdb;
		
		if ( ( ! isset( $data[ 'invoice_parent' ] ) || empty( $data[ 'invoice_parent' ] ) ) && ! $this->parent )
			return false;
		
		if ( isset( $data[ 'invoice_parent' ] ) )
			$this->set_parent( absint( $data[ 'invoice_parent' ] ) );
		
		if ( is_null( $order ) && $this->parent->order )
			$order = wc_get_order( $this->parent->order );
		
		$this->update_status( 'wc-gzdp-paid' );
		
		if ( $this->parent )
			$this->parent->update_status( 'wc-gzdp-cancelled' );
		
		if ( $this->is_locked() && ! isset( $data[ 'invoice_force_rebuilt' ] ) )
			return false;
		
		// Update Post
		$date = date_i18n( 'Y-m-d H:i:s', strtotime( ( ! empty( $data[ 'invoice_date' ] ) ? $data[ 'invoice_date' ] : false ) ) );
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date = %s, post_date_gmt = %s WHERE ID = %s", $date, get_gmt_from_date( $date ), $this->id ) );

		if ( $this->parent ) {

			// Update from parent invoice
			update_post_meta( $this->id, '_invoice_address', $this->parent->address );
			update_post_meta( $this->id, '_invoice_recipient', $this->parent->recipient );
			update_post_meta( $this->id, '_invoice_payment_method', $this->parent->payment_method );
			update_post_meta( $this->id, '_invoice_payment_method_title', $this->parent->payment_method_title );
			update_post_meta( $this->id, '_invoice_order', $this->parent->order );
			update_post_meta( $this->id, '_invoice_currency', $this->parent->currency );

			// Negative amounts
			if ( $this->parent->items ) {
				$items = $this->parent->items;
				foreach ( $items as $key => $item ) {
					$items[ $key ][ 'line_subtotal' ] *= -1;
					$items[ $key ][ 'line_total' ] *= -1;
					$items[ $key ][ 'line_subtotal_tax' ] *= -1;
					$items[ $key ][ 'line_tax' ] *= -1;
				}
				update_post_meta( $this->id, '_invoice_items', $items );
			}

			if ( $this->parent->tax_totals ) {
				$tax_totals = $this->parent->tax_totals;
				foreach ( $tax_totals as $key => $total )
					$tax_totals[ $key ]->amount *= -1;
				update_post_meta( $this->id, '_invoice_tax_totals', $tax_totals );
			}

			if ( $this->parent->fee_totals ) {
				$items = $this->parent->fee_totals;
				foreach ( $items as $key => $item ) {
					$items[ $key ][ 'line_subtotal' ] *= -1;
					$items[ $key ][ 'line_total' ] *= -1;
					$items[ $key ][ 'line_subtotal_tax' ] *= -1;
					$items[ $key ][ 'line_tax' ] *= -1;
				}
				update_post_meta( $this->id, '_invoice_fee_totals', $items );
			}

			if ( $this->parent->totals ) {
				$totals = $this->parent->totals;
				foreach ( $totals as $key => $total )
					$totals[ $key ] *= -1;
				update_post_meta( $this->id, '_invoice_totals', $totals );
			}

		}

		// Invoice Number
		if ( $this->is_new() )
			$this->generate_number();

		if ( $order )
			$this->refresh_order_invoices( $order );
		$this->populate();
		$file = $this->generate_pdf();
		$this->save_attachment( $file );
	}

	public function get_submit_button_text() {
		return ( $this->is_new() ? sprintf( __( 'Cancel %s', 'woocommerce-germanized-pro' ), $this->parent->get_title() ) : __( 'Regenerate Cancellation', 'woocommerce-germanized-pro' ) );
	}

}