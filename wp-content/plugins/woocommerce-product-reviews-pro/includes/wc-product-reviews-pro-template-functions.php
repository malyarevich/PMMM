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
 * @package   WC-Product-Reviews-Pro/Template
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Template function overrides
 *
 * @since 1.0.0
 */


if ( ! function_exists( 'wc_product_reviews_pro_contributions' ) ) {

	/**
	 * Output the Contributions comments template.
	 *
	 * @since 1.0.0
	 * @param \WP_Comment $comment
	 * @param array $args
	 * @param int $depth
	 */
	function wc_product_reviews_pro_contributions( $comment, $args, $depth ) {

		$contribution = wc_product_reviews_pro_get_contribution( $comment );

		// The default template path.
		$template = 'single-product/contributions/contribution';

		// If a type-specific template exists, add the type to the template string.
		if ( file_exists( wc_locate_template( $template . '-' . $contribution->get_type() . '.php' ) ) ) {
			$template .= '-' . $contribution->get_type();
		}

		$template .= '.php';

		wc_get_template( $template, array(
			'contribution' => $contribution,
			'comment'      => $comment,
			'args'         => $args,
			'depth'        => $depth,
		) );
	}

}

/**
 * Display the contribution karma markup.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 */
function wc_product_reviews_pro_contribution_karma( $contribution ) {

	wc_get_template( 'single-product/contributions/karma.php', array(
		'contribution' => $contribution,
	) );
}

/**
 * Display the contribution meta markup.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 */
function wc_product_reviews_pro_contribution_meta( $contribution ) {

	wc_get_template( 'single-product/contributions/meta.php', array(
		'contribution' => $contribution,
		'comment'      => $contribution->get_comment_data(),
	) );
}

/**
 * Display the contribution attachments.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 */
function wc_product_reviews_pro_contribution_attachments( $contribution, $wrap_microdata = true ) {

	wc_get_template( 'single-product/contributions/attachments.php', array(
		'contribution'   => $contribution,
		'wrap_microdata' => $wrap_microdata,
	) );
}

/**
 * Get the contribution attachment image.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 * @return \WC_Contribution|false The contribution attachment image markup.
 */
function wc_product_reviews_pro_get_contribution_attachment_image( $contribution ) {

	$image = false;

	if ( $attachment_url = $contribution->get_attachment_url() ) {
        $image = '<img src="' . esc_url( $attachment_url ) . '" itemprop="contentUrl" />';
    } elseif ( $contribution->has_attachment() ) {

		/**
		 * Filter the attached image size.
		 *
		 * Note that this only applies to images that were uploaded from the user's computer.
		 *
		 * @since 1.2.0
		 * @param string|array $size The desired image size. Default: large.
		 * @param \WC_Contribution $contribution The current contribution.
		 */
		$image_size = apply_filters( 'wc_product_reviews_pro_contribution_image_size', 'large', $contribution );

        $image = wp_get_attachment_image( $contribution->get_attachment_id(), $image_size, false, array(
			'itemprop' => 'contentUrl',
		) );
    }

	/**
	 * Filter the attached image size.
	 *
	 * @since 1.2.0
	 * @param string $image The image markup.
	 * @param \WC_Contribution $contribution The current contribution.
	 */
	$image = apply_filters( 'wc_product_reviews_pro_contribution_image', $image, $contribution );

	return $image;
}


/**
 * Display the contribution actions markup.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 */
function wc_product_reviews_pro_contribution_actions( $contribution ) {

	$action = '';

	// Display a subscription action only for top level contributions
	if ( isset( $contribution->comment->comment_parent ) && $contribution->comment->comment_parent == 0 ) {

		if ( is_user_logged_in() && wc_product_reviews_pro_comment_notification_enabled() ) {

			$user = wp_get_current_user();

			if ( in_array( $user->ID, wc_product_reviews_pro_get_comment_notification_subscribers( $contribution ) ) ) {
				$action = 'unsubscribe';
			} else {
				$action = 'subscribe';
			}

		}

	}

	wc_get_template( 'single-product/contributions/actions.php', array(
		'contribution'  => $contribution,
		'notifications' => $action,
	) );
}

/**
 * Determine if a contribution supports the "upvoteCount" & "downvoteCount" schema properties.
 *
 * @since 1.4.3
 * @param \WC_Contribution $contribution the contribution object
 * @return bool
 */
function wc_product_reviews_pro_contribution_supports_upvote_downvote_schema( WC_Contribution $contribution ) {

	$type = $contribution->get_type();

	return 'question' === $type || 'video' === $type || 'contribution_comment' === $type;
}

if ( ! function_exists( 'wc_product_reviews_pro_contribution_comment_form' ) ) {

	/**
	 * Output the contribution comment form template.
	 *
	 * @since 1.0.0
	 * @param \WP_Comment $comment
	 * @param array $args
	 * @param int $depth
	 */
	function wc_product_reviews_pro_contribution_comment_form( $comment, $args, $depth ) {

		$contribution_types = wc_product_reviews_pro()->get_enabled_contribution_types();

		// if comments are disabled, bail
		if ( ! in_array( 'contribution_comment', $contribution_types, true ) ) {
			return;
		}

		if ( ! $comment->comment_parent ) {

			wc_get_template( 'single-product/form-contribution.php', array(
				'comment' => $comment,
				'args'    => $args,
				'depth'   => $depth,
				'type'    => 'contribution_comment',
			) );
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contribution_flag_form' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 * @param \WP_Comment $comment
	 */
	function wc_product_reviews_pro_contribution_flag_form( $comment ) {

		wc_get_template( 'single-product/form-flag-contribution.php', array(
			'comment' => $comment,
		) );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_review_qualifiers_form_controls' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 */
	function wc_product_reviews_pro_review_qualifiers_form_controls() {

		wc_get_template( 'single-product/form-control-review-qualifiers.php' );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_review_qualifiers' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 * @param \WC_Contribution $contribution
	 */
	function wc_product_reviews_pro_review_qualifiers( $contribution ) {

		wc_get_template( 'single-product/contributions/contribution-review-qualifiers.php', array(
			'contribution' => $contribution,
		) );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contributions_list_title' ) ) {

	/**
	 * Output the contributions list title
	 *
	 * @param string $current_type Optional
	 * @param int $count Optional
	 * @param int $rating Optional
	 */
	function wc_product_reviews_pro_contributions_list_title( $current_type = '', $count = 0, $rating = null ) {

		if ( ! $current_type ) {
			esc_html_e( 'What others are saying', 'woocommerce-product-reviews-pro' );
		} else {
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $current_type );
			echo $contribution_type->get_list_title( $count, $rating );
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contributions_list_no_results_text' ) ) {

	/**
	 * Output the no results text, depending on current type context
	 *
	 * @param string $current_type
	 */
	function wc_product_reviews_pro_contributions_list_no_results_text( $current_type = '' ) {

		if ( ! $current_type ) {
			esc_html_e( 'There are no contributions yet.', 'woocommerce-product-reviews-pro' );
		} else {
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $current_type );
			echo $contribution_type->get_no_results_text();
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contribution_list_table' ) ) {

	/**
	 * List the current user's Contributions as a table
	 *
	 * @since 1.6.0
	 */
	function wc_product_reviews_pro_contribution_list_table() {

		$product_reviews_pro = wc_product_reviews_pro();

		// get the comments for the user
		$comments = get_comments( array( 'user_id' => get_current_user_id() ) );

		// we'll pass in post types the comments have been left on,
		// so we can display comments only on products
		$comments_on = array();

		foreach ( $comments as $comment ) {

			$comments_on[] = get_post_type( $comment->comment_post_ID );
		}

		// get enabled contribution types
		$enabled_contribution_types = $product_reviews_pro->get_enabled_contribution_types();

		if ( ! empty( $enabled_contribution_types ) ) {

			wc_get_template(
				'myaccount/contribution-list.php',
				array(
					'comments'                   => $comments,
					'enabled_contribution_types' => $enabled_contribution_types,
					'comments_on'                => $comments_on,
				),
				'',
				$product_reviews_pro->get_plugin_path() . '/templates/'
			);
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_get_product_rating_count' ) ) {

	/**
	 * Get the product rating count
	 *
	 * TODO remove this function as part of WC 2.7 compatibility update {FN 2016-05-23}
	 *
	 * @deprecated since 1.4.0
	 * @param int $product_id WC Product id
	 * @param int|null $rating Optional. Rating value to get the count for.
	 *                         By default returns the count of all rating values.
	 * @return int Rating count
	 */
	function wc_product_reviews_pro_get_product_rating_count( $product_id, $rating = null ) {

		$product = wc_get_product( $product_id );

		return $product->get_rating_count( $rating );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_admin_badge' ) ) {

	/**
	 * Return the badge for admin / shop manager comments if set.
	 *
	 * @since 1.2.0
	 * @param \WP_Comment $comment
	 * @return string $badge The admin badge html
	 */
	function wc_product_reviews_pro_author_badge( $comment ) {

		$badge_text = get_option( 'wc_product_reviews_pro_contribution_badge' );

		/**
		 * Filter the badge text.
		 *
		 * @since 1.2.0
		 * @param string $badge_text The badge text
		 * @param \WP_Comment $comment The current comment
		 */
		$badge_text = apply_filters( 'wc_product_reviews_pro_contribution_badge_text', $badge_text, $comment );

		if ( empty ( $badge_text ) || ! is_object( $comment ) ) {
			return;
		}

		$badge = '';

		if ( $comment->user_id ) {

			$userdata = get_userdata( $comment->user_id );

			$roles = $userdata ? $userdata->roles : array();

			if ( in_array( 'administrator', $roles, true ) || in_array( 'shop_manager', $roles, true ) || user_can( $comment->user_id, 'manage_network' ) ) {

				$badge = '<span class="contribution-admin-badge">' . esc_html( $badge_text ) . '</span>';
			}
		}

		/**
		 * Filter the admin / shop manager badge markup.
		 *
		 * @since 1.2.0
		 * @param string $badgeThe badge markup
		 * @param \WP_Comment $comment The comment data
		 */
		$badge = apply_filters( 'wc_product_reviews_pro_author_badge', $badge, $comment );

		echo $badge;
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_get_enabled_types_name' ) ) {

	/**
	 * Get the name for the enabled contribution types
	 *
	 * @since 1.2.0
	 * @return string $type_title Title for enabled contributions
	 */
	function wc_product_reviews_pro_get_enabled_types_name() {

		$enabled_contribution_types = wc_product_reviews_pro()->get_enabled_contribution_types();

		// Do not take contribution_comments into account
		if ( ( $key = array_search( 'contribution_comment', $enabled_contribution_types ) ) !== false ) {
			unset( $enabled_contribution_types[$key] );
		}

		// For single types, get their type-specific section title
		if ( count( $enabled_contribution_types ) === 1 ) {

			$type = $enabled_contribution_types[0];
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );

			$type_title = strtolower( $contribution_type->get_title( true ) );
		} else {
			$type_title = __( 'contributions', 'woocommerce-product-reviews-pro' );
		}

		return apply_filters( 'wc_product_reviews_pro_enabled_types_name', $type_title, $enabled_contribution_types );
	}

}
