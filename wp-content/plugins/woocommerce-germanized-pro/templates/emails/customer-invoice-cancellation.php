<?php
/**
 * Customer invoice email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo wpautop( apply_filters( 'wc_gzdp_email_invoice_cancellation_text', sprintf( __( 'Hi there. An invoice to your order has been cancelled. For your reference please see %s to %s which we attached to this email.', 'woocommerce-germanized-pro' ), $invoice->get_title(), $invoice->parent->get_title() ), $invoice ) ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>