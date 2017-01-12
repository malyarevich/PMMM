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
 * Product Reviews Pro contribution replies notification email
 *
 * Email notifications are sent when a new reply is posted to contributions
 * to users that wish to receive updates on contributions they subscribed to
 *
 * @since 1.3.0
 */
class WC_Product_Reviews_Pro_Emails_New_Comment extends WC_Email {

	
	/** @var WC_Product Product being reviewed */
	private $product = null;

	/** @var WC_Contribution Contribution replied to */
	private $contribution = null;

	/** @var WC_Contribution Reply to contribution */
	private $reply = null;


	/**
	 * Set properties
	 *
	 * @since 1.3.0
	 */
	function __construct() {

		$this->id             = 'wc_product_reviews_pro_new_comment_email';
		$this->title          = __( 'Contribution reply', 'woocommerce-product-reviews-pro' );
		$this->description    = __( 'Email users that wish to be notified whenever there is a new comment on a product contribution they subscribed to.', 'woocommerce-product-reviews-pro' );

		$this->template_html  = 'emails/contribution-comment-notification.php';
		$this->template_plain = 'emails/plain/contribution-comment-notification.php';

		$this->subject        = __( 'New reply posted on a {product_name} {contribution_type}', 'woocommerce-product-reviews-pro' );
		$this->heading        = __( 'A reply has been added to a {contribution_type} on {site_title}', 'woocommerce-product-reviews-pro' );

		// Find/replace
		$this->find     = array( '{blogname}', '{site_title}' );
		$this->replace  = array( $this->get_blogname(), $this->get_blogname() );

		// Triggers
		add_action( $this->id . '_notification', array( $this, 'trigger' ), 10, 4 );

		// Call parent constructor
		parent::__construct();
	}


	/**
	 * Is customer email
	 *
	 * @since 1.4.0
	 * @return true
	 */
	public function is_customer_email() {
		return true;
	}


	/**
	 * Trigger the new contribution reply notification email
	 *
	 * @since 1.3.0
	 * @param array $users An array of users IDs
	 * @param \WC_Product $product Product contributed to
	 * @param \WC_Contribution $contribution Original contribution comment
	 * @param \WC_Contribution $reply Contribution reply comment
	 */
	function trigger( $users, $product, $contribution, $reply ) {

		if ( $this->is_enabled() && ! empty( $users ) && is_array( $users ) ) {

			foreach ( $users as $user_id ) {

				// No need to notify the original author if is the one replying
				if ( $user_id == $reply->contributor_id ) {
					continue;
				}

				$this->object = get_user_by( 'id', $user_id );

				if ( $this->object instanceof WP_User ) {

					$this->recipient    = $this->object->user_email;
					$this->product      = $product;
					$this->contribution = $contribution;
					$this->reply        = $reply;

					if ( ! $this->get_recipient() || ! $this->contribution instanceof WC_Contribution ) {
						continue;
					}

					$this->find['product-name']      = '{product_name}';
					$this->replace['product-name']   = $this->product->get_title();

					if ( $contribution_type = wc_product_reviews_pro_get_contribution_type( $contribution->type ) ) {

						$this->find['contribution_type']    = '{contribution_type}';
						$this->replace['contribution_type'] = strtolower( $contribution_type->get_title() );
					}

					$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
				}

			}

		}

	}


	/**
	 * Get email HTML content
	 *
	 * @since 1.3.0
	 * @return string HTML content
	 */
	function get_content_html() {

		ob_start();

		wc_get_template( $this->template_html, array(
			'user'          => $this->object,
			'product'       => $this->product,
			'contribution'  => $this->contribution,
			'reply'         => $this->reply,
			'site_title'    => $this->get_blogname(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
		) );

		return ob_get_clean();
	}


	/**
	 * Get email plain text content
	 *
	 * @since 1.3.0
	 * @return string Plain text content
	 */
	function get_content_plain() {

		ob_start();

		wc_get_template( $this->template_plain, array(
			'user'          => $this->object,
			'product'       => $this->product,
			'contribution'  => $this->contribution,
			'reply'         => $this->reply,
			'site_title'    => $this->get_blogname(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
		) );

		return ob_get_clean();
	}


}


return new WC_Product_Reviews_Pro_Emails_New_Comment();
