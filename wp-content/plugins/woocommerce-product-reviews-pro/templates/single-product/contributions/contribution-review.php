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
 * Display Review contributions
 *
 * @type \WP_Comment $comment
 * @type \WC_Contribution $contribution
 * 
 * @since 1.2.0
 * @version 1.2.0
 */

$title          = $contribution->get_title();
$rating         = $contribution->get_rating();
$rating_enabled = $rating && 'yes' === get_option( 'woocommerce_enable_review_rating' );

?>

<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<?php // Display the karma markup.
		wc_product_reviews_pro_contribution_karma( $contribution ); ?>

		<div class="comment-text">

			<?php echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '', get_comment_author() ); ?>

			<?php if ( $title || $rating_enabled ) : ?>

				<h3 class="contribution-title review-title">

					<?php if ( $rating_enabled ) : ?>

						<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'woocommerce-product-reviews-pro' ), $rating ) ); ?>">
							<span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%;">
								<?php printf( __( '<strong itemprop="ratingValue">%d</strong> out of 5', 'woocommerce-product-reviews-pro' ), esc_attr( $rating ) ) ; ?>
							</span>
						</span>

					<?php endif; ?>

					<?php if ( $title ) : ?>

						<span itemprop="name"><?php echo esc_html( $title ); ?></span>

					<?php endif; ?>

				</h3>

			<?php endif; ?>

			<?php // Display the meta markup.
			wc_product_reviews_pro_contribution_meta( $contribution ); ?>

			<?php wc_product_reviews_pro_review_qualifiers( $contribution ); ?>

			<div itemprop="reviewBody" class="description"><?php comment_text(); ?></div>

			<?php // Display the attachments.
			wc_product_reviews_pro_contribution_attachments( $contribution ); ?>

			<?php // Display the actions markup.
			wc_product_reviews_pro_contribution_actions( $contribution ); ?>

			<?php wc_product_reviews_pro_contribution_flag_form( $comment ); ?>

		</div>
	</div>
