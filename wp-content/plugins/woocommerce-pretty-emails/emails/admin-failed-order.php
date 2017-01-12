<?php
/**
 * Admin failed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-failed-order.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer)
 * will need to copy the new files to your theme to maintain compatibility. We try to do this
 * as little as possible, but it does happen. When this occurs the version of the template file will
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php include( MBWPE_TPL_PATH.'/settings.php' ); ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( 'Payment for order #%d from %s has failed. The order was as follows:', 'woocommerce' ), $order->get_order_number(), $order->get_formatted_billing_full_name() ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, true, false ); ?>

<h2><a href="<?php echo admin_url( 'post.php?post=' . $order->id . '&action=edit' ); ?>"><?php printf( __( 'Order: %s', 'woocommerce'), $order->get_order_number() ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ); ?>)</h2>

<table cellspacing="0" cellpadding="6" style="border-collapse:collapse; width: 100%; border: 1px solid <?php echo $bordercolor;?>;" border="1" bordercolor="<?php echo $bordercolor;?>">
	<thead>
		<tr>
			<th scope="col" style="<?php echo $missingstyle;?>text-align:left; border: 1px solid <?php echo $bordercolor;?>;"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th scope="col" style="<?php echo $missingstyle;?>text-align:center; border: 1px solid <?php echo $bordercolor;?>;"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th scope="col" style="<?php echo $missingstyle;?>text-align:center; border: 1px solid <?php echo $bordercolor;?>;"><?php _e( 'Price', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php include( MBWPE_TPL_PATH.'/tbody.php' ); ?>
	</tbody>
	<?php include( MBWPE_TPL_PATH.'/tfoot.php' ); ?>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, true, false, $email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, true, false, $email ); ?>

<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) : ?>

<h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>

	<?php if ( $order->billing_email ) : ?>
		<p><strong><?php _e( 'Email:', 'woocommerce' ); ?></strong> <?php echo $order->billing_email; ?></p>
	<?php endif; ?>
	<?php if ( $order->billing_phone ) : ?>
		<p><strong><?php _e( 'Tel:', 'woocommerce' ); ?></strong> <?php echo $order->billing_phone; ?></p>
	<?php endif; ?>	
	
	<?php wc_get_template( 'emails/email-addresses.php', array( 'order' => $order ) ); ?>

<?php else : ?>

	<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>

<?php include( MBWPE_TPL_PATH.'/treatments.php' ); ?>
