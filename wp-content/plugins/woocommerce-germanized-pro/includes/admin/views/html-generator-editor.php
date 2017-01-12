<?php
/**
 * Admin View: Generator Editor
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

$page_key = ( isset( WC_GZDP_Admin_Generator::instance()->pages[ $current_section ] ) ? WC_GZDP_Admin_Generator::instance()->pages[ $current_section ] : false );

?>

<div class="wc-gzdp-generator-result">

	<?php wp_editor( $html, 'wc_gzdp_generator_content', array( 'media_buttons' => false ) ); ?>

	<p class="submit">
		<?php if ( $page_key && get_option( 'woocommerce_' . $page_key . '_page_id' ) ) : ?>
			<input type="hidden" name="generator_page_id" value="<?php echo get_option( 'woocommerce_' . $page_key . '_page_id' ); ?>" />
			<input type="hidden" name="generator" value="<?php echo $current_section; ?>" />
			<input type="submit" class="button-primary" value="<?php echo sprintf( _x( 'Save as %s', 'generator', 'woocommerce-germanized-pro' ), get_the_title( get_option( 'woocommerce_' . $page_key . '_page_id' ) ) ); ?>" />
			<?php if ( $current_section != 'agbs' ) : ?>
				<span class="form-row" style="margin-left: 1.5em">
					<input type="checkbox" name="generator_page_append" value="1" />
					<?php echo _x( 'Append content instead of replacing it.', 'generator', 'woocommerce-germanized-pro' ); ?>
				</span>
			<?php endif ; ?>
		<?php endif; ?>
	</p>

</div>