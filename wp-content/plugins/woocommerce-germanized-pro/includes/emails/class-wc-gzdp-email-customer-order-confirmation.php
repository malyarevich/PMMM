<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_GZDP_Email_Customer_Order_Confirmation' ) ) :

/**
 * Customer Processing Order Email
 *
 * An email sent to the customer when a new order is received/paid for.
 *
 * @class 		WC_Email_Customer_Processing_Order
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 */
class WC_GZDP_Email_Customer_Order_Confirmation extends WC_Email_Customer_Processing_Order {

	public function __construct() {

		parent::__construct();

		$this->customer_email = true;
		
		$this->id = 'customer_order_confirmation';
		$this->title = __( 'Order Confirmation', 'woocommerce-germanized-pro' );
		$this->description = __( 'This email will confirm an order to a customer. Will not be sent automatically. Will be sent after confirming the order.', 'woocommerce-germanized-pro' );

		// Remove default actions
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $this, 'trigger' ) );
	}

	public function init_form_fields() {
		parent::init_form_fields();
		unset( $this->form_fields[ 'subject' ] );
	}

}

endif;

return new WC_GZDP_Email_Customer_Order_Confirmation();