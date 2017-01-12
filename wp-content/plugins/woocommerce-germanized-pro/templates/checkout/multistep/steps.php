<?php
/**
 * Checkout Mutlistep Steps
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<?php if ( ! empty( $multistep->steps ) ) : ?>

	<ul class="step-nav nav-wizard">
		
	<?php foreach ( $multistep->steps as $key => $step ) : ?>

		<?php if ( $step->is_activated() ) : ?>

		<li>
			<a <?php echo ( $step->number == 1 ? 'href="#step-' . $step->get_id() . '"' : '' ); ?> data-href="<?php echo $step->get_id(); ?>" class="step step-<?php echo $step->number; ?> step-<?php echo $step->get_id(); ?>">
				<span class="step-number"><?php echo $step->number; ?></span>
				<span class="step-title"><?php echo $step->get_title(); ?></span>
			</a>
		</li>

		<?php endif; ?>

	<?php endforeach; ?>

	</ul>

<?php endif; ?>