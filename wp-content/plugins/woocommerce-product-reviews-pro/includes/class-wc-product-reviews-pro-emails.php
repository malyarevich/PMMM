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
 * @package   WC-Product-Reviews-Pro/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Product Reviews Pro Emails class
 *
 * This class handles all email-related functionality in Product Reviews Pro.
 *
 * @since 1.3.0
 */
class WC_Product_Reviews_Pro_Emails {


	/**
	 * Set up Product Reviews Pro emails
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		// Hook in WC emails
		add_filter( 'woocommerce_email_classes', array( $this, 'add_emails' ), 10, 1 );

		// Hook in WP comments for contribution replies notifications
		add_action( 'wp_insert_comment',     array( $this, 'comment_notification' ), 50, 2 );
		add_action( 'wp_set_comment_status', array( $this, 'comment_status_change' ), 50, 2 );

		// Process unsubscribe request from emails
		add_action( 'woocommerce_init', array( $this, 'comment_notifications_unsubscribe_request' ) );

		// @see comment_notification method
		add_action( 'wc_product_reviews_pro_new_comment_email', array( 'WC_Emails', 'send_transactional_email' ), 10, 4 );
	}


	/**
	 * Add Product Reviews Pro emails to WC emails
	 *
	 * @since 1.3.0
	 * @param array $emails
	 * @return array $emails
	 */
	public function add_emails( $emails ) {

		$emails['WC_Product_Reviews_Pro_Emails_New_Comment'] = require_once( wc_product_reviews_pro()->get_plugin_path() . '/includes/class-wc-product-reviews-pro-emails-new-comment.php' );

		return $emails;
	}


	/**
	 * Hooks wp_insert_comment for new comments that are approved
	 *
	 * Sends email notifications to subscribers of replies to product contribution comments
	 *
	 * @since 1.3.0
	 * @param int $comment_id
	 * @param \WP_Comment $comment
	 */
	public function comment_notification( $comment_id, $comment ) {

		// Only fired for replies that have a top level comment
		if ( 1 == $comment->comment_approved && $comment->comment_parent > 0 ) {

			$product = wc_get_product( $comment->comment_post_ID );

			if ( $product ) {

				$contribution = wc_product_reviews_pro_get_contribution( $comment );

				$top_level  = wc_product_reviews_pro_get_top_level_contribution( $contribution );
				$users      = get_comment_meta( $top_level->id, 'wc_product_reviews_pro_notify_users', true );

				if ( ! empty( $users ) ) {

					do_action( 'wc_product_reviews_pro_new_comment_email', $users, $product, $top_level, $contribution );
				}
			}
		}
	}


	/**
	 * Hooks wp_set_comment_status when a comment status changes to 'approve'
	 *
	 * Sends email notifications to comment subscribers upon comment approval
	 *
	 * @since 1.3.0
	 * @param int $comment_id
	 * @param string $comment_status
	 */
	public function comment_status_change( $comment_id, $comment_status ) {

		if ( 'approve' == $comment_status ) {

			$comment = get_comment( $comment_id );

			if ( isset( $comment->comment_ID ) ) {

				$this->comment_notification( $comment->comment_ID, $comment );
			}
		}
	}


	/**
	 * Process link to unsubscribe from new comment notifications
	 *
	 * @since 1.3.0
	 */
	public function comment_notifications_unsubscribe_request() {

		if ( isset( $_GET['wc_prp_comments_notifications'] ) && isset( $_GET['user'] ) && isset( $_GET['contribution'] ) ) {

			if ( 'unsubscribe' != $_GET['wc_prp_comments_notifications'] ) {
				return;
			}

			$user            = intval( $_GET['user'] );
			$contribution_id = intval( $_GET['contribution'] );
			$contribution    = wc_product_reviews_pro_get_contribution( $contribution_id );

			$result = wc_product_reviews_pro_add_comment_notification_subscriber( 'unsubscribe', $user, $contribution );

			if ( ! is_null( $result ) ) {
				wc_add_notice( __( 'You will be no longer receiving email notifications for comments on the review you had subscribed to.', 'woocommerce-product-reviews-pro' ), 'success' );
			} else {
				wc_add_notice( __( 'An error occurred. Your request could not be processed.', 'woocommerce-product-reviews-pro' ), 'error' );
			}
		}
	}


}


new WC_Product_Reviews_Pro_Emails();
