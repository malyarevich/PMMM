<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woothemes.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @package   WC-Product-Reviews-Pro/Templates
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Display a contribution's attachments
 *
 * @since 1.2.0
 * @version 1.4.3
 */
?>

<?php if ( 'photo' === $contribution->get_attachment_type() ) : ?>

    <?php if ( $image = wc_product_reviews_pro_get_contribution_attachment_image( $contribution ) ) : ?>

        <?php if ( $wrap_microdata ) : ?>
            <span itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
        <?php endif; ?>

        <?php echo $image; ?>

        <?php if ( $wrap_microdata ) : ?>
            </span>
        <?php endif; ?>

    <?php else : ?>
        <p class="attachment-removed"><?php _e( 'Photo has been removed', 'woocommerce-product-reviews-pro' ); ?></p>
    <?php endif; ?>

<?php endif; ?>

<?php if ( 'video' === $contribution->get_attachment_type() ) : ?>

    <?php if ( $attachment_url = $contribution->get_attachment_url() ) : ?>

		<?php $embed_code = wp_oembed_get( $attachment_url ); ?>
		<?php echo $embed_code ? $embed_code : sprintf( '<a href="%s">%s</a>', esc_url( $attachment_url ), $attachment_url ); ?>

    <?php else : ?>
        <p class="attachment-removed"><?php _e( 'Video has been removed', 'woocommerce-product-reviews-pro' ); ?></p>
    <?php endif; ?>

<?php endif; ?>
