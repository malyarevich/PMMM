<?php
/**
 * Checkout Mutlistep Next Step Button
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<div class="step-buttons">

	<?php if ( $step->has_prev() ) : ?>

		<a class="prev-step-button step-trigger" id="prev-step-<?php echo $step->get_id();?>" data-href="<?php echo $step->prev->get_id(); ?>" href="#step-<?php echo $step->prev->get_id(); ?>">
			<?php echo sprintf( _x( 'Back to step %s', 'multistep', 'woocommerce-germanized-pro' ), $step->prev->number ); ?>
		</a>

	<?php endif; ?>

	<?php if ( $step->has_next() ) : ?>

		<button class="button alt next-step-button" type="submit" name="woocommerce_gzdp_checkout_next_step" id="next-step-<?php echo $step->get_id();?>" data-current="<?php echo $step->get_id();?>" data-next="<?php echo $step->next->get_id(); ?>">
			<?php echo sprintf( _x( 'Continue with step %s', 'multistep', 'woocommerce-germanized-pro' ), $step->next->number ); ?>
		</button>

	<?php endif; ?>

	<div class="clear"></div>

</div>