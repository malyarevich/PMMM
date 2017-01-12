<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
//Original:\plugins\woocommerce\templates\loop\price.php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
?>

<?php if ( $price_html = $product->get_price_html() ) : ?>
	<?php $min_value = apply_filters( 'woocommerce_quantity_input_min', '', $product ); ?>
	<?php if ( 1 < $min_value ): ?>
	<span class="price price-custom"><?php echo $price_html; ?></span>
	<?php else: ?>
		<span class="price"><?php echo $price_html; ?></span>
	<?php endif; ?>
<?php endif; ?>
