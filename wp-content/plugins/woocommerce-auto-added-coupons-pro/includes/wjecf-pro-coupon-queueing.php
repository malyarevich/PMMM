<?php

defined('ABSPATH') or die();

//Queue coupons that are invalid when customer applies, but might validate later
class WJECF_Pro_Coupon_Queueing extends Abstract_WJECF_Plugin {
	public function __construct() {	
		$this->set_plugin_data( array(
			'description' => __( 'Allow coupons that are invalid upon application to be applied to the cart once they become valid.', 'woocommerce-jos-autocoupon' ),
			'dependencies' => array(),
			'can_be_disabled' => true
		) );		
	}
	
	public function init_hook() {

		//NOTE: apply_valid_queued_coupons() is called from plugin WJECF_AutoCoupon
		//Therefore no action hooks are required in here

		add_filter('woocommerce_coupon_error', array( $this, 'filter_woocommerce_coupon_error'), 10, 3);
		add_action('woocommerce_removed_coupon', array( $this, 'on_woocommerce_removed_coupon' ), 10, 1);
	}

	function filter_woocommerce_coupon_error( $err, $err_code, $coupon ) {
		//If adding a coupon has failed (restrictions not met) queue the coupon so it will be applied once valid

		//Using filter woocommerce_coupon_error is a bit hacky; it is called if applying a coupon failed (invalidated),
		//but also on any is_valid() call that returns false; therefore we have to doublecheck that it really was 
		//the customer who tried to apply the coupon on the cart

		//NOTE: $coupon can be null on certain calls
		if ( $coupon == null ) {
			return $err;
		}

		//Don't queue in these cases
		if ( in_array( $err_code, array( 
			WC_Coupon::E_WC_COUPON_ALREADY_APPLIED,
			WC_Coupon::E_WC_COUPON_NOT_EXIST,
			WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED,
			WC_Coupon::E_WC_COUPON_EXPIRED
		) ) ) {
			return $err;
		}

		//Ignore AutoCoupons
		$wjecf_autocoupon = WJECF()->get_plugin('WJECF_AutoCoupon');
		if ( $wjecf_autocoupon !== false && $wjecf_autocoupon->is_auto_coupon( $coupon ) ) {
			return $err;
		}

		//Was it really added by customer?
		$do_queue = isset( $_GET['apply_coupon'] ) && sanitize_text_field( $_GET['apply_coupon'] ) == $coupon->code; //Coupon by url using WJECF_Autocoupon
		$do_queue |= isset( $_POST['apply_coupon'] ) && sanitize_text_field( $_POST['apply_coupon'] ) == $coupon->code; //Form submit on cart page
		$do_queue |= isset( $_GET['wc-ajax'] ) && $_GET['wc-ajax'] == 'apply_coupon' && sanitize_text_field( $_POST['coupon_code'] ) == $coupon->code; //Ajax

		if ( $do_queue ) {
			wc_add_notice( sprintf( __( 'Coupon \'%s\' will be applied when it\'s conditions are met.', 'woocommerce-jos-autocoupon' ), $coupon->code ) );
			$this->queue_coupon_code($coupon->code);
		}

		return $err;
	}

	function on_woocommerce_removed_coupon( $coupon_code ) {
		$do_unqueue = isset( $_GET['wc-ajax'] ) && $_GET['wc-ajax'] == 'remove_coupon' && sanitize_text_field( $_POST['coupon'] ) == $coupon_code;
		$do_unqueue |= isset( $_GET['remove_coupon'] ) && sanitize_text_field( $_GET['remove_coupon'] ) == $coupon_code;

		if ( $do_unqueue ) {
			$this->unqueue_coupon_code( $coupon_code );
		}
	}

	/**
	 * Apply the valid queued coupons
	 * (Queued coupons are coupons that the customer tried to apply; but was not yet valid)
	 * @return void
	 */
	public function apply_valid_queued_coupons() {
		//2.3.3 Keep track of apply_coupon coupons and apply when they validate
		if ( WJECF()->is_pro() ) {
			$this->log( "()" );
			$queued_coupon_codes = $this->get_queued_coupon_codes();
			$this->log( "Queued coupons: " . implode( ' ', $queued_coupon_codes ) );
			foreach( $queued_coupon_codes as $coupon_code ) {
				if ( ! WC()->cart->has_discount( $coupon_code )  ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( $coupon->is_valid() ) {
						$this->log( sprintf( "Applying queued coupon %s", $coupon->code ) );
						$new_succss_msg = sprintf(
							__("Coupon '%s' applied.", 'woocommerce-jos-autocoupon'), 
							__($coupon->code, 'woocommerce-jos-autocoupon')
						);

						WJECF()->start_overwrite_success_message( $coupon, $new_succss_msg );
						WC()->cart->add_discount( $coupon->code ); //Causes calculation and will remove other coupons if it's an individual coupon
						WJECF()->stop_overwrite_success_message();

						//$calc_needed = false; //Already done by adding the discount
					} elseif ( ! $coupon->exists ) {
						//Remove non-existent
						if( ( $key = array_search($coupon_code, $queued_coupon_codes ) ) !== false ) {
							unset( $queued_coupon_codes[$key] );
							$this->set_queued_coupon_codes( $queued_coupon_codes );
						}
						//wc_add_notice( $coupon->get_coupon_error( WC_Coupon::E_WC_COUPON_NOT_EXIST ), 'error' );
					}
				}
			}
		}
	}

	/**
	 * Get the queued coupon codes from the session
	 * @param bool $exclude_if_in_cart If true, the coupons that are applied in the cart will not be returned
	 * @return array The queued coupon codes
	 */
	public function get_queued_coupon_codes( $exclude_if_in_cart = false ) {
		$coupon_codes = WC()->session->get( 'wjecf_queued_coupons' , array() );		
		if ( $exclude_if_in_cart ) {
			foreach( $coupon_codes as $key => $coupon_code ) {
				if ( WC()->cart->has_discount( $coupon_code ) ) {
					unset( $coupon_codes[$key] );
				}
			}
		}
		return $coupon_codes;
	}
	/**
	 * Save the queued coupon codes in the session
	 * @param array $coupon_codes 
	 * @return void
	 */
	public function set_queued_coupon_codes( $coupon_codes ) {
		WC()->session->set( 'wjecf_queued_coupons' , array_unique( $coupon_codes ) );
	}

	private function queue_coupon_code( $coupon_code ) {
		$queued_coupon_codes = $this->get_queued_coupon_codes();
		if ( ! in_array( $coupon_code, $queued_coupon_codes ) ) {
			$queued_coupon_codes[] = $coupon_code;
			$this->set_queued_coupon_codes( $queued_coupon_codes );
		}
	}

	private function unqueue_coupon_code( $coupon_code ) {
		$queued_coupon_codes = $this->get_queued_coupon_codes();
		if( ( $key = array_search( $coupon_code, $queued_coupon_codes ) ) !== false ) {
			unset( $queued_coupon_codes[$key] );
			$this->set_queued_coupon_codes( $queued_coupon_codes );
		}		
	}	

}