<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Invoice_Packing_Slip extends WC_GZDP_Invoice {

	public function __construct( $invoice ) {
		parent::__construct( $invoice );
		$this->type = 'packing_slip';
	}

	public function get_address() {
		if ( $this->shipping_address )
			return $this->shipping_address;
		return parent::get_address();
	}

	public function generate_number() {

		if ( $this->get_option( 'enable_numbering' ) === 'yes' )
			return parent::generate_number();	

		global $wpdb;
		
		$number = ( $this->order_number ? $this->order_number : $this->order );
		update_post_meta( $this->id, '_invoice_number', $number );
		update_post_meta( $this->id, '_invoice_number_formatted', $this->get_title() );
		
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_title = %s WHERE ID = %s", $this->get_title(), $this->id ) );
	}

	public function get_number() {

		if ( $this->get_option( 'enable_numbering' ) === 'yes' )
			return parent::get_number();	

		global $post;
		
		if ( ! $this->number && ( isset( $post ) && $post->post_type == 'shop_order' ) ) {
			$order = wc_get_order( $post->ID );
			return $order->get_order_number();
		}
		
		return $this->number; 
	}

	public function get_title_pdf() {

		if ( $this->get_option( 'print_number' ) === 'yes' )
			return parent::get_title_pdf();

		$type = wc_gzdp_get_invoice_types( $this->type );
		return apply_filters( 'woocommerce_gzdp_invoice_title_pdf', '<span class="invoice-desc">' . $type[ 'title' ] . '</span>', $this );
	}

	public function refresh_post_data( $data = array(), $order ) {
		$data[ 'invoice_date' ] = date_i18n( 'Y-m-d H:i:s' );
		parent::refresh_post_data( $data, $order );
	}

	public function refresh( $data = array(), $order = NULL ) {
		parent::refresh( $data, $order );
		update_post_meta( $this->id, '_invoice_exclude', 1 );
	}

	public function filter_export_data( $data = array() ) {
		unset( $data[ 'status' ] );
		unset( $data[ 'delivered' ] );
		return $data;
	}

	public function get_submit_button_text() {
		return ( $this->is_new() ? __( 'Generate Packing Slip', 'woocommerce-germanized-pro' ) : __( 'Regenerate Packing Slip', 'woocommerce-germanized-pro' ) );
	}

}