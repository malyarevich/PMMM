<?php
/**
 * Admin View: Invoice Settings
 */

if ( ! defined( 'ABSPATH' ) )
	exit;
?>

<a class="button button-secondary" target="_blank" href="<?php echo admin_url( '?action=wc-gzdp-download-legal-page&_wpnonce=' . wp_create_nonce( 'wc-gzdp-download' ) . '&preview=true' ); ?>"><?php echo _x( 'Preview PDF', 'legal-page', 'woocommerce-germanized-pro' ); ?></a>
<a class="button button-secondary" style="margin-left: 1em" href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=germanized&section=pdf&action=wc-gzdp-regenerate-fonts&_wpnonce=' . wp_create_nonce( 'wc-gzdp-regenerate-fonts' ) ); ?>"><?php echo _x( 'Recompile fonts', 'invoices', 'woocommerce-germanized-pro' ); ?></a>