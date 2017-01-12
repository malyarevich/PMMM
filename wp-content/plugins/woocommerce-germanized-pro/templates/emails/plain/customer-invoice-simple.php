<?php
/**
 * Customer invoice plain email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

echo "= " . $email_heading . " =\n\n";

echo apply_filters( 'wc_gzdp_email_invoice_text_plain', sprintf( __( 'Thank you very much for your order %s. For your reference please see %s corresponding to your order %s which we attached to this email.', 'woocommerce-germanized-pro' ), $invoice->get_order_number(), $invoice->get_title(), $invoice->get_order_number() ), $invoice ) . "\n\n";

if ( 'yes' === $show_pay_link && $order->has_status( 'pending' ) ) {

	printf( __( 'An order has been created for you on %s. To pay for this order please use the following link: %s', 'woocommerce-germanized-pro' ), get_bloginfo( 'name', 'display' ), $order->get_checkout_payment_url() ) . "\n\n";

}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );