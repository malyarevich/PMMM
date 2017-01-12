<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Invoice extends WC_GZDP_Post_PDF {

	public function __construct( $invoice ) {
		parent::__construct( $invoice );
		$this->content_type = 'invoice';
		$this->type = 'simple';
	}

	public function is_cancellation() {
		return $this->is_type( 'cancellation' );
	}

	public function get_address() {
		return $this->address;
	}

	public function is_cancelled() {
		return ( $this->get_status( true ) == 'cancelled' ? true : false );
	}

	public function get_submit_button_text() {
		return ( $this->is_new() ? __( 'Generate Invoice', 'woocommerce-germanized-pro' ) : __( 'Regenerate Invoice', 'woocommerce-germanized-pro' ) );
	}

	public function get_sender_address( $type = '' ) {
		return ( $this->get_option( 'address' . ( ! empty( $type ) ? '_' . $type : '' ) ) ? explode( "\n", $this->get_option( 'address' . ( ! empty( $type ) ? '_' . $type : '' ) ) ) : array() );
	}

	public function get_number() {
		$number = $this->number;
		if ( ! $this->number )
			$number = __( 'X', 'woocommerce-germanized-pro' );
		return $number;
	}

	public function has_number() {
		$number = $this->number;
		return ( ( ! $number || empty( $number ) ) ? false : true );
	}

	public function get_title( $html = false ) {
		$type = wc_gzdp_get_invoice_types( $this->type );
		$format = $this->get_option( 'number_format', '' );
		$number = $this->number_formatted;
		
		if ( ! $number || empty( $number ) )
			$number = $this->number_format( $format );
		
		if ( $html )
			$number = str_replace( $type[ 'title' ], '<span class="invoice-desc">' . $type[ 'title' ] . '</span>', $number );
		
		return apply_filters( 'woocommerce_gzdp_invoice_title', $number, $this, $html );
	}

	public function number_format( $format ) {
		$type = wc_gzdp_get_invoice_types( $this->type );
		return str_replace( 
			array( 
				'{type}', 
				'{number}',
				'{order_number}',
				'{d}', 
				'{m}', 
				'{y}'
			), 
			array( 
				$type[ 'title' ], 
				( $this->get_option( 'number_leading_zeros' ) ? str_pad( $this->get_number(), absint( $this->get_option( 'number_leading_zeros' ) ), '0', STR_PAD_LEFT ) : $this->get_number() ), 
				$this->get_order_number(),
				$this->get_date( 'd' ), 
				$this->get_date( 'm' ),
				$this->get_date( 'Y' ) 
			), 
			$format 
		);
	}

	public function get_status( $readable = false ) {
		return ( $readable ? str_replace( 'wc-gzdp-', '', $this->post->post_status ) : $this->post->post_status );
	}

	public function is_delivered() {
		return ( $this->delivered ? true : false );
	}

	public function get_delivery_date( $format = 'd.m.Y H:i' ) {
	 	return ( $this->delivery_date ? date_i18n( $format, strtotime( $this->delivery_date ) ) : false );
	}

	public function is_new() {
		return ( $this->has_number() ? false : true );
	}

	public function is_locked() {
		return ( $this->locked ? true : false );
	}

	public function get_email_class() {
		return 'WC_GZDP_Email_Customer_Invoice_' . ucfirst( $this->type );
	}

	public function filter_export_data( $data = array() ) {
		return $data;
	}

	public function send_to_customer() {
		$mailer = WC()->mailer();
		foreach ( $mailer->get_emails() as $key => $mail ) {
			if ( $key == $this->get_email_class() )
				$mail->trigger( $this );
		}
		$this->mark_as_sent();
	}

	public function mark_as_sent() {
		update_post_meta( $this->id, '_invoice_delivered', true );
		update_post_meta( $this->id, '_invoice_delivery_date', current_time( 'mysql' ) );
		update_post_meta( $this->id, '_invoice_locked', true );
	}

	public function get_summary() {
		return sprintf( __( 'Total Invoice Amount: %s', 'woocommerce-germanized-pro' ), wc_price( $this->totals[ 'total' ] ) );
	}

	public function get_order_number() {
		return ( $this->order_number ? $this->order_number : $this->order );
	}

	public function update_status( $status = '' ) {
		
		$org_status = $status;
		$status = str_replace( 'wc-gzdp-', '', $status );

		if ( empty( $status ) || $this->get_status( true ) == $status )
			return false;
		
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_status = %s WHERE ID = %s", "wc-gzdp-" . $status, $this->id ) );

		do_action( 'wc_gzdp_invoice_status_changed', $this, $this->id );
		do_action( 'wc_gzdp_invoice_status_changed_to_' . $status, $this, $this->id );
		do_action( 'wc_gzdp_invoice_status_changed_from_' . $this->get_status( true ) . '_to_' . $status, $this, $this->id );

		$this->populate();
		$this->post->post_status = $org_status;

	}

	public function refresh_post_data( $data = array(), $order ) {

		global $wpdb;

		$date = date_i18n( 'Y-m-d H:i:s' );
		
		if ( isset( $data[ 'invoice_date' ] ) && ! empty( $data[ 'invoice_date' ] ) )
			$date = $data[ 'invoice_date' ];

		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date = %s, post_date_gmt = %s WHERE ID = %s", $date, get_gmt_from_date( $date ), $this->id ) );
		
		$this->populate();
		
	}

	public function get_order() {
		$order = $this->order;
		
		if ( ! is_object( $order ) )
			$order = wc_get_order( $order );
	
		return $order;
	}

	public function get_totals() {

		$order = $this->get_order();

		$totals = array();

		if ( ! $order )
			return $totals;

		$totals = $order->get_order_item_totals();
		
		if ( isset( $totals[ 'payment_method' ] ) )
			unset( $totals[ 'payment_method' ] );

		foreach ( $totals as $key => $val ) {
			
			if ( strpos( $key, 'tax_' ) !== false ) {
				unset( $totals[ $key ] );
				continue;
			}

			// Replace :
			$totals[ $key ][ 'label' ] = str_replace( ':', '', $totals[ $key ][ 'label' ] );  
		}

		$totals[ 'net_price' ] = array(
			'label' => __( 'Total net', 'woocommerce-germanized-pro' ),
			'value' => wc_price( $this->totals[ 'total' ] - $this->totals[ 'tax' ], array( 'currency' => $this->currency ) ),
			'invoice_data' => true,
		);

		if ( $this->tax_totals ) {
			foreach ( $this->tax_totals as $code => $tax ) {
				$totals[ 'tax_' . $tax->rate_id ] = array( 
					'label' => wc_gzdp_get_tax_label( $tax->rate_id ),
					'value' => wc_price( $tax->amount, array( 'currency' => $this->currency ) ),
					'invoice_data' => true,
				);
			}
		}

		if ( isset( $totals[ 'order_total' ] ) ) {
			$totals[ 'order_total' ][ 'value' ] = wc_price( $this->totals[ 'total' ], array( 'currency' => $this->currency ) );
			$totals[ 'order_total' ][ 'invoice_data' ] = true;
			$totals[ 'order_total' ][ 'classes' ] = array( 'footer-total' );
		}

		if ( isset( $totals[ 'cart_subtotal' ] ) ) {
			$totals[ 'cart_subtotal' ][ 'classes' ] = array( 'footer-first' );
		}

		return apply_filters( 'woocommerce_gzdp_invoice_totals', $totals, $this );
	}

	public function refresh( $data = array(), $order = NULL ) {
		
		global $wpdb;
		
		$this->populate();

		$data = apply_filters( 'woocommerce_gzdp_invoice_refresh_data', $data, $this );

		$status = ( ! empty( $data[ 'invoice_status' ] ) ? $data[ 'invoice_status' ] : $this->get_status() );
		$this->update_status( $status );
		
		if ( $this->is_locked() && ! isset( $data[ 'invoice_force_rebuilt' ] ) )
			return false;
		
		if ( ! is_object( $order ) && $this->order )
			$order = wc_get_order( $this->order );

		// Bind invoice to order language if available
		$lang = null;
		if ( $lang = get_post_meta( $order->id, 'wpml_language', true ) ) {
			update_post_meta( $this->id, 'wpml_language', $lang );
			do_action( 'woocommerce_gzdp_invoice_language_update', $this, $lang );
		}
		
		// Update Post
		$this->refresh_post_data( $data, $order );
		
		// Update Meta
		delete_post_meta( $this->id, '_invoice_tax_totals' );
		delete_post_meta( $this->id, '_invoice_fee_totals' );
		delete_post_meta( $this->id, '_invoice_refunds' );

		update_post_meta( $this->id, '_invoice_address', $order->get_formatted_billing_address() );
		update_post_meta( $this->id, '_invoice_shipping_address', $order->get_formatted_shipping_address() );
		update_post_meta( $this->id, '_invoice_recipient', array( 'firstname' => $order->billing_first_name, 'lastname' => $order->billing_last_name, 'mail' => $order->billing_email ) );
		update_post_meta( $this->id, '_invoice_items', $order->get_items() );
		update_post_meta( $this->id, '_invoice_currency', $order->get_order_currency() );
		
		if ( $order->get_tax_totals() ) {
			$tax_totals = $order->get_tax_totals();
			foreach ( $tax_totals as $key => $tax ) {
				$tax_totals[ $key ]->amount = $tax_totals[ $key ]->amount - $order->get_total_tax_refunded_by_rate_id( $tax->rate_id );
				$tax_totals[ $key ]->formatted_amount = wc_price( $tax_totals[ $key ]->amount );
			}
			update_post_meta( $this->id, '_invoice_tax_totals', $tax_totals );
		}
		if ( $order->get_items( 'fee' ) )
			update_post_meta( $this->id, '_invoice_fee_totals', $order->get_items( 'fee' ) );
		if ( $order->get_refunds() )
			update_post_meta( $this->id, '_invoice_refunds', $order->get_refunds() );
		
		update_post_meta( $this->id, '_invoice_payment_method', $order->payment_method );
		update_post_meta( $this->id, '_invoice_payment_method_title', $order->payment_method_title );

		$fee_total = 0;
		foreach ( $order->get_fees() as $item )
			$fee_total += $item['line_total'];

		$subtotal_gross = 0;
		foreach ( $order->get_items() as $item )
			$subtotal_gross += $order->get_line_total( $item, true, true );

		update_post_meta( $this->id, '_invoice_totals', array( 
			'subtotal' => $order->get_subtotal(),
			'subtotal_gross' => $subtotal_gross,
			'shipping' => $order->get_total_shipping(),
			'fee' => $fee_total,
			'discount' => ( $order->get_total_discount() ? $order->get_total_discount() * -1 : 0 ),
			'total_before_refund' => $order->get_total(),
			'refunded' => ( $order->get_total_refunded() ? ( $order->get_total_refunded() - $order->get_total_tax_refunded() ) * -1 : 0 ), 
			'tax_refunded' => ( $order->get_total_tax_refunded() ? $order->get_total_tax_refunded() * -1 : 0 ), 
			'tax' => $order->get_total_tax() - $order->get_total_tax_refunded(), 
			'total' => ( $order->get_total() - $order->get_total_refunded() ),
		) );

		update_post_meta( $this->id, '_invoice_order', $order->id );
		update_post_meta( $this->id, '_invoice_order_number', $order->get_order_number() );
		update_post_meta( $this->id, '_invoice_order_data', array( 'date' => $order->order_date ) );
		
		// Invoice Number
		if ( $this->is_new() )
			$this->generate_number();

		do_action( 'woocommerce_gzdp_before_invoice_refresh', $this );
		
		$this->refresh_order_invoices( $order );
		parent::refresh();
	}

	protected function generate_number() {
		global $wpdb;
		$number = wc_gzdp_get_next_invoice_number( $this->type );
		update_post_meta( $this->id, '_invoice_number', $number );
		update_post_meta( $this->id, '_invoice_number_formatted', $this->get_title() );
		// Update Post Title
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_title = %s WHERE ID = %s", $this->get_title(), $this->id ) );
	}

	public function refresh_order_invoices( $order ) {
		$order_invoices = get_post_meta( $order->id, '_invoices', true );
		if ( $order_invoices && ! in_array( $this->id, $order_invoices ) )
			array_push( $order_invoices, $this->id );
		else if ( ! $order_invoices )
			$order_invoices = array( $this->id );
		update_post_meta( $order->id, '_invoices', $order_invoices );
	}

	public function delete( $bypass_trash = false ) {
		if ( $this->order ) {
			$invoices = get_post_meta( $this->order, '_invoices', true );
			if ( ! empty( $invoices ) ) {
				foreach ( $invoices as $key => $invoice ) {
					if ( $invoice == $this->id )
						unset( $invoices[ $key ] );
				}
				$invoices = array_values( $invoices );
				if ( ! empty( $invoices ) )
					update_post_meta( $this->order, '_invoices', $invoices );
				else
					delete_post_meta( $this->order, '_invoices' );
			}
		}
		parent::delete( $bypass_trash );
	}

}

?>