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
 * Review Contribution Factory Class
 *
 * Helper to get the right review contribution object
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Contribution_Factory {


	/**
	 * Get contribution
	 *
	 * @since 1.0.0
	 * @param bool $the_contribution (default: false)
	 * @param array $args (default: array())
	 * @return \WC_Contribution
	 */
	public function wc_product_reviews_pro_get_contribution( $the_contribution = false, $args = array() ) {

		global $comment;

		if ( false === $the_contribution ) {
			$the_contribution = $comment;
		} elseif ( is_numeric( $the_contribution ) ) {
			$the_contribution = get_comment( $the_contribution );
		}

		if ( ! $the_contribution ) {
			return false;
		}

		if ( is_object( $the_contribution ) ) {
			$comment_id   = absint( $the_contribution->comment_ID );
			$comment_type = $the_contribution->comment_type;
		}

		// Create a WC coding standards compliant class name e.g. WC_Product_Type_Class instead of WC_Product_type-class
		$classname = 'WC_Contribution_' . implode( '_', array_map( 'ucfirst', explode( '-', $comment_type ) ) );

		/**
		 * Filter classname so that the class can be overridden if extended.
		 *
		 * @since 1.0.0
		 * @param string $classname The class name.
		 * @param string $comment_type The comment type.
		 * @param int $comment_id The comment id.
		 */
		$classname = apply_filters( 'woocommerce_contribution_class', $classname, $comment_type, $comment_id );

		if ( ! class_exists( $classname ) ) {
			$classname = 'WC_Contribution_Review';
		}

		return new $classname( $the_contribution, $args );
	}


}
