<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Unit_Price_Helper {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	private function __construct() {
		
		// Unit auto calculation
		add_filter( 'woocommerce_gzd_product_saveable_data', array( $this, 'calculate_unit_price' ), 10, 2 );
		
		add_action( 'woocommerce_bulk_edit_variations', array( $this, 'bulk_save_variations_unit_price' ), 0, 4 );
		add_action( 'woocommerce_product_quick_edit_save', array( $this, 'quick_edit_save_unit_price' ), 0, 1 );
		add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'bulk_edit_save_unit_price' ), 0, 1 );
	}

	public function calculate_unit_price( $data, $post_id ) {

		$product = wc_get_product( $post_id );
		$parent = false;
		$data_replaceable = $data;

		// Set inherited values
		if ( $product->is_type( 'variation' ) ) {

			$parent = wc_get_product( $product->parent );

			$inherited = array(
				'unit',
				'unit_base',
				'unit_product',
			);

			foreach ( $inherited as $inherit ) {
				if ( ! isset( $data[ '_' . $inherit ] ) || empty( $data[ '_' . $inherit ] ) ) {
					$data_replaceable[ '_' . $inherit ] = isset( $data[ '_parent_' . $inherit ] ) ? $data[ '_parent_' . $inherit ] : $parent->$inherit;
				}
			}
		}

		$mandatory = array(
			'_unit_price_auto',
			'_unit',
			'_unit_base',
		);

		foreach ( $mandatory as $mand ) {

			if ( ! isset( $data_replaceable[ $mand ] ) || empty( $data_replaceable[ $mand ] ) )
				return $data;
		
		}

		$base = $data_replaceable[ '_unit_base' ];
		// If product_base is not available, divide by base
		$product_base = $base;

		if ( ! isset( $data_replaceable[ '_unit_product' ] ) || empty( $data_replaceable[ '_unit_product' ] ) ) {
			// Set base multiplicator to 1
			$base = 1;
		} else {
			$product_base = $data_replaceable[ '_unit_product' ];
		}

		$data[ '_unit_price_regular' ] = wc_format_decimal( ( $product->get_regular_price() / $product_base ) * $base, wc_get_price_decimals() );
		$data[ '_unit_price_sale' ] = '';

		if ( $product->get_sale_price() )
			$data[ '_unit_price_sale' ] = wc_format_decimal( ( $product->get_sale_price() / $product_base ) * $base, wc_get_price_decimals() );

		return $data;
	}

	public function get_product_unit_price_data( $product ) {

		$unit_data = array(
			'_unit_price_auto' => $product->unit_price_auto,
			'_unit_base' => $product->unit_base,
			'_unit_product' => $product->unit_product,
			'_unit' => $product->unit,
			'_sale_price' => $product->get_sale_price(),
			'_sale_price_dates_from' => $product->sale_price_date_from,
			'_sale_price_dates_to' => $product->sale_price_date_to,
			'product-type' => $product->get_type(),
		);

		return $unit_data;
	}

	public function save_unit_price( $product ) {

		if ( method_exists( WC_Germanized_Meta_Box_Product_Data, 'save_unit_price' ) ) {

			$id = ( $product->is_type( 'variation' ) ? $product->variation_id : $product->id );
			
			$unit_data = $this->get_product_unit_price_data( $product );
			$unit_data = apply_filters( 'woocommerce_gzd_product_saveable_data', $unit_data, $id );
			
			WC_Germanized_Meta_Box_Product_Data::save_unit_price( $id, $unit_data, $product->is_type( 'variation' ) );
		}
	}

	public function bulk_save_variations_unit_price( $bulk_action, $data, $product_id, $variations ) {
		foreach ( $variations as $variation_id ) {
			$product = wc_get_product( $variation_id );
			$this->save_unit_price( $product, $unit_data );
		}
	}

	public function quick_edit_save_unit_price( $product ) {
		$this->save_unit_price( $product, $unit_data );
	}

	public function bulk_edit_save_unit_price( $product ) {
		$this->save_unit_price( $product, $unit_data );
	}

}

WC_GZDP_Unit_Price_Helper::instance();