<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Invoice_Preview extends WC_GZDP_Invoice {

	public $number;

	public function __construct() {
		
		$this->content_type = 'invoice';
		$this->order_obj = new WC_Order();
		$this->order = 1;
		$this->type = 'preview';
		$this->number = get_option( 'wc_gzdp_invoice_simple' ) + 1;
		
		$this->recipient = array(
			'firstname' => _x( 'Max', 'invoices-preview', 'woocommerce-germanized-pro' ),
			'lastname' => _x( 'Mustermann', 'invoices-preview', 'woocommerce-germanized-pro' ),
		);
		
		$this->address = implode( '<br/>', array(
			_x( 'Max Mustermann', 'invoices-preview', 'woocommerce-germanized-pro' ),
			_x( 'Musterstraße 35', 'invoices-preview', 'woocommerce-germanized-pro' ),
			_x( '12209 Musterstadt', 'invoices-preview', 'woocommerce-germanized-pro' ),
		) );
		
		$base_rate = WC_Tax::get_shop_base_rate();
		$base_rate_id = key( $base_rate );
		$calc_tax = true;
		
		if ( get_option( 'woocommerce_gzd_small_enterprise' ) == 'yes' )
			$calc_tax = false;

		$this->items = array();
		$total = 0;
		$total_tax = 0;
		$shipping = 0;
		
		for ( $i = 0; $i < 30; $i++ ) {
			
			$qty = 1;
			$subtotal_gross = 35.95;
			$subtotal_tax = ( $calc_tax ? array_sum( WC_Tax::calc_tax( $subtotal_gross, $base_rate, true ) ) : 0 );
			$subtotal_net = $subtotal_gross - $subtotal_tax;
			$subtotal = $subtotal_net;
			$item_total = $qty * $subtotal;
			$item_tax = $qty * $subtotal_tax;
			
			$item = array(
				'name' => _x( 'Testprodukt', 'invoices-preview', 'woocommerce-germanized-pro' ),
				'item_meta' => array(),
				'type' => 'line_item',
				'qty' => $qty,
				'line_total' => $item_total,
				'line_subtotal' => $subtotal,
				'line_tax' => $item_tax,
				'line_subtotal_tax' => $subtotal_tax,
			);

			$total += $subtotal_gross;
			$total_tax += $item_tax;

			array_push( $this->items, $item );
		}
		
		$this->totals = array(
			'subtotal' => $total - $shipping - $total_tax,
			'subtotal_gross' => $total - $shipping,
			'total' => $total,
			'shipping' => $shipping,
			'tax' => $total_tax,
			'discount' => 0,
		);
		
		$this->order_obj->order_total = $total;
		$this->order_obj->order_tax = $total_tax;
		
		if ( $calc_tax ) {
			$tax_totals = array(
				'amount' => $total_tax,
				'rate_id' => $base_rate_id,
				'formatted_amount' => wc_price( $total_tax ),
			);
			$this->tax_totals = array();
			$this->tax_totals[ 'DE' ] = (object) $tax_totals;
		}		
	}

	public function get_order() {
		return $this->order_obj;
	}

	public function get_status( $readable = false ) {
		return 'wc-gzdp-pending';
	}

	public function get_date( $format ) {
		return date( $format );
	}

	public function generate_pdf( $preview = false ) {
		add_filter( 'woocommerce_gzdp_invoice_item_no_tax_rate_html', array( $this, 'force_static_tax_rate' ), 10, 3 );
		parent::generate_pdf( $preview );
	}

	public function force_static_tax_rate( $display, $item, $order ) {

		$order = $this->get_order();

		if ( ! empty( $order->order_tax ) )
			return '19%';
		
		return $display;
	}

	public function get_totals() {
		
		$totals = parent::get_totals();
		$totals[ 'cart_subtotal' ][ 'value' ] = $totals[ 'order_total' ][ 'value' ];

		return $totals;
	}

}