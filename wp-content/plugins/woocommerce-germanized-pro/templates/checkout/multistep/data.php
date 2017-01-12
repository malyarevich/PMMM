<?php
/**
 * Checkout Order Step Customer Data
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$payment_gateway = false;
$gateways = WC()->payment_gateways()->get_available_payment_gateways();
$method = WC()->session->get( 'chosen_payment_method' );

if ( $method && isset( $gateways[ $method ] ) )
	$payment_gateway = $gateways[ $method ];

?>

<div class="woocommerce-gzpd-checkout-verify-data">

	<div class="col2-set addresses">

		<div class="col-1">

			<header class="title">
					<h4><?php _e( 'Billing Details', 'woocommerce-germanized-pro' ); ?></h4>
			</header>

			<address>
				<?php
					if ( ! $multistep->get_formatted_billing_address() ) {
						_e( 'N/A', 'woocommerce-germanized-pro' );
					} else {
						echo $multistep->get_formatted_billing_address();
					}
				?>
				<?php 
					if ( WC()->checkout->get_value( 'billing_email' ) )  {
						echo "<br/>" . WC()->checkout->get_value( 'billing_email' );
					}
				?>
			</address>

			<p><a href="#step-address" class="edit step-trigger" data-href="address"><?php echo _x( 'edit', 'multistep', 'woocommerce-germanized-pro' ); ?></a></p>

			<?php if ( $payment_gateway ) : ?>

				<header class="title">
					<h4><?php echo _x( 'Payment Method', 'multistep', 'woocommerce-germanized-pro' ); ?></h4>
				</header>
	 
				<p class="wc-gzdp-payment-gateway"><?php echo $payment_gateway->get_title(); ?></p>

				<p><a href="#step-payment" class="edit step-trigger" data-href="payment"><?php echo _x( 'edit', 'multistep', 'woocommerce-germanized-pro' ); ?></a></p>

			<?php endif; ?>

		</div><!-- /.col-1 -->

		<div class="col-2">

			<header class="title">
				<h4><?php _e( 'Shipping Address', 'woocommerce-germanized-pro' ); ?></h4>
			</header>
			<address>
				<?php
					if ( ! $multistep->get_formatted_shipping_address() ) {
						echo _x( 'Same as billing address', 'multistep', 'woocommerce-germanized-pro' );
					} else {
						echo $multistep->get_formatted_shipping_address();
					}
				?>
			</address>

			<p><a href="#step-address" class="edit step-trigger" data-href="address"><?php echo _x( 'edit', 'multistep', 'woocommerce-germanized-pro' ); ?></a></p>

		</div><!-- /.col-2 -->

	</div><!-- /.col2-set -->

</div>