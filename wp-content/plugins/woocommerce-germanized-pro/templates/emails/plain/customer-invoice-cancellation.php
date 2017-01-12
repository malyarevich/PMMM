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

echo apply_filters( 'wc_gzdp_email_invoice_cancellation_text', sprintf( __( 'Hi there. An invoice to your order has been cancelled. For your reference please see %s to %s which we attached to this email.', 'woocommerce-germanized-pro' ), $invoice->get_title(), $invoice->parent->get_title() ), $invoice ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );