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
 * @package   WC-Product-Reviews-Pro/Abstracts
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Abstract Contribution Class
 *
 * Thw WooCommerce contribution class handles contribution data
 *
 * @since 1.0.0
 */
abstract class WC_Contribution {


	/** @var int Contribution (comment) ID */
	public $id;

	/** @var int related Product (post) ID */
	public $product_id;

	/** @var string contributor name */
	public $contributor_name;

	/** @var string contributor email */
	public $contributor_email;

	/** @var string contributor IP */
	public $contributor_ip;

	/** @var string contribution date */
	public $contribution_date;

	/** @var string contribution date in gmt */
	public $contribution_date_gmt;

	/** @var string contribution content (text) */
	public $content;

	/** @var int net contribution vote, used as a roll-up of the sum of positive_votes/negative_votes meta in order to improve sort performance */
	public $karma;

	/** @var int contribution moderation status (0 = not approved, 1 = approved, 2 = flagged as inappropriate) */
	public $moderation;

	/** @var string contribution type */
	public $type;

	/** @var int contribution parent (used for threaded comments) */
	public $parent;

	/** @var int contributor (user) ID (if contributor was logged in) */
	public $contributor_id;

	/** @var string contribution_title (not used for questions or comments) */
	public $title;

	/** @var float numeric rating (only used for reviews) */
	public $rating;

	/** @var int number of positive votes */
	public $positive_votes;

	/** @var int number of negative votes */
	public $negative_votes;

	/** @var int number of times this contribution flagged inappropriate */
	public $flag_count;

	/** @var int attached media ID (not used for comments) */
	public $attachment_id;

	/** @var int attached media URL (not used for comments) */
	public $attachment_url;

	/** @var int attached media type (not used for comments) */
	public $attachment_type;

	/** @var \WP_Comment the actual comment object **/
	public $comment;

	/** @var string Failure message for vote and flag methods **/
	private $_failure_message;


	/**
	 * Constructor gets the comment object and sets the ID for the loaded contribution.
	 *
	 * @since 1.0.0
	 * @param int|\WC_Contribution|\WP_Comment $contribution Contribution ID, comment object, or contribution object
	 */
	public function __construct( $contribution ) {

		if ( is_numeric( $contribution ) ) {

			$this->id      = absint( $contribution );
			$this->comment = get_comment( $this->id );

		} elseif ( $contribution instanceof WC_Contribution ) {

			$this->id      = absint( $contribution->id );
			$this->comment = $contribution->comment;

		} elseif ( $contribution instanceof WP_Comment || isset( $contribution->comment_ID ) ) {

			$this->id      = absint( $contribution->comment_ID );
			$this->comment = $contribution;

		}

		// Populate contribution from database
		if ( $this->comment ) {

			$this->populate();
		}
	}


	/**
	 * Populates a contribution from the database.
	 *
	 * @since 1.0.0
	 */
	private function populate() {

		// Load comment data from database
		$comment = $this->get_comment_data();

		// Bail out if comment data is not available
		if ( ! $comment ) {
			return;
		}

		// Standard comment data
		$this->id                    = $comment->comment_ID;
		$this->product_id            = $comment->comment_post_ID;
		$this->contributor_name      = $comment->comment_author;
		$this->contributor_email     = $comment->comment_author_email;
		$this->contributor_ip        = $comment->comment_author_IP;
		$this->contribution_date     = $comment->comment_date;
		$this->contribution_date_gmt = $comment->comment_date_gmt;
		$this->content               = $comment->comment_content;
		$this->karma                 = $comment->comment_karma;
		$this->moderation            = $comment->comment_approved;
		$this->type                  = $comment->comment_type;
		$this->parent                = $comment->comment_parent;
		$this->contributor_id        = $comment->user_id;

		/**
		 * Filter meta keys to load contribution data from.
		 *
		 * @since 1.0.0
		 * @param array $keys Array of comment meta keys to load contribution data from.
		 */
		$meta_keys = apply_filters( "wc_contribution_{$this->type}_load_meta_keys", array(
			'title',
			'rating',
			'positive_votes',
			'negative_votes',
			'flag_count',
			'attachment_id',
			'attachment_url',
			'attachment_type',
		) );

		foreach ( $meta_keys as $key ) {

			$this->{$key} = get_comment_meta( $this->id, $key, true );
		}

	}


	/**
	 * Meta-method for returning contribution data, currently:
	 *
	 * + id
	 * + product_id
	 * + contributor_id
	 * + contributor_name
	 * + contributor_email
	 * + contributor_ip
	 * + contributor_date
	 * + contributor_date_gmt
	 * + content
	 * + karma
	 * + moderation
	 * + type
	 * + parent
	 * + title
	 * + rating
	 * + positive_votes
	 * + negative_votes
	 * + flag_count
	 * + attachment_id
	 * + attachment_url
	 * + attachment_type
	 *
	 * sample usage:
	 *
	 * `$rating = $contribution->get_rating()`
	 *
	 * To override this behaviour for a single property, implement
	 * a getter method on the class, for example `get_type`.
	 *
	 * TODO remove __call overloading and declare functions for better testability and readability
	 *
	 * @since 1.0.0
	 * @param string $method Called method
	 * @param array $args Method arguments
	 * @return string|bool
	 */
	public function __call( $method, $args ) {

		// get_* method
		if ( 0 === strpos( $method, 'get_' ) ) {

			$property = str_replace( 'get_', '', $method );

			// get attachment URLs for photos in the media folder, which will have an ID instead
			if ( 'get_attachment_url' === $method && ! $this->attachment_url && $this->attachment_id ) {
				return wp_get_attachment_url( $this->attachment_id );
			}

			return $this->$property;
		}

		return null;
	}


	/**
	 * Get the contribution's comment data.
	 *
	 * @since 1.0.0
	 * @return \WP_Comment
	 */
	public function get_comment_data() {
		return $this->comment;
	}


	/**
	 * Get the link to this contribution.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_permalink() {
		return get_comment_link( $this->id );
	}


	/**
	 * Get the total vote count.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_vote_count() {
		return $this->get_positive_votes() + $this->get_negative_votes();
	}


	/**
	 * Get helpfulness score
	 *
	 * 1 = most helpful, 0 = meh, -1 = awful
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_helpfulness_ratio() {

		return $this->get_positive_votes() && $this->get_vote_count() ? $this->get_positive_votes() / $this->get_vote_count() : 0;
	}


	/**
	 * Get users who have voted for this contribution
	 *
	 * @since 1.0.0
	 * @return array Associative array with user ID / vote pairs.
	 */
	public function get_users_votes() {

		$users_votes = get_comment_meta( $this->get_id(), 'users_votes', true );
		$users_votes = $users_votes ? $users_votes : array();

		return $users_votes;
	}


	/**
	 * Get users who have flagged this contribution
	 *
	 * @since 1.0.0
	 * @return array List with IDs of flagged users
	 */
	public function get_users_flagged() {

		$users_flagged = get_comment_meta( $this->get_id(), 'users_flagged', true );
		$users_flagged = $users_flagged ? explode( ',', $users_flagged ) : array();

		return $users_flagged;
	}


	/**
	 * Check if a user has flagged the contribution
	 *
	 * If user ID is not provided falls back to checking cookies.
	 *
	 * @since 1.0.0
	 * @param int|string $user_id Optional. Defaults to the current user, if logged in.
	 * @return bool True if user has flagged this contribution, false otherwise
	 */
	public function has_user_flagged( $user_id = '' ) {

		// Use the provided user ID or current user ID
		$user_id = $user_id ? $user_id : get_current_user_id();

		$flagged_comments = isset( $_COOKIE['wc_product_reviews_pro_flagged_comments'] )
											? explode( ',', $_COOKIE['wc_product_reviews_pro_flagged_comments'] )
											: array();

		// Rules:
		// * If user ID is in flagged users list, then user has flagged
		// * If no user ID is provided, and cookie is set, then user has flagged
		// * If user ID matches current user ID and cookie is set, then user has flagged
		return ( $user_id && in_array( $user_id, $this->get_users_flagged() )
				|| ( ! $user_id || $user_id == get_current_user_id() ) && in_array( $this->get_id(), $flagged_comments ) );
	}


	/**
	 * Get a specific user's vote
	 *
	 * @since 1.0.0
	 * @param int|string $user_id Optional. Defaults to the current user, if logged in.
	 * @return string Vote type if user has voted, null otherwise.
	 */
	public function get_user_vote( $user_id = '' ) {

		// Use the provided user ID or current user ID
		$user_id = $user_id ? $user_id : get_current_user_id();

		// Get all users' votes
		$users_votes = $this->get_users_votes();

		return isset( $users_votes[$user_id] ) ? $users_votes[$user_id] : null;
	}


	/**
	 * Check if a user has voted for the contribution
	 *
	 * @param string|int $user_id Optional. Defaults to the current user, if logged in.
	 * @return bool True if user has voted for this contribution, false otherwise
	 */
	public function has_user_voted( $user_id = '' ) {
		return (bool) $this->get_user_vote( $user_id );
	}


	/**
	 * Check if contribution has an attachment
	 *
	 * NB! This does not check if the attachment has been removed or not
	 *
	 * @since 1.0.0
	 * @return bool True if has attachment, false otherwise
	 */
	public function has_attachment() {

		return (bool) $this->get_attachment_type();
	}


	/**
	 * Get the message with reason why voting/flagging failed
	 *
	 * @since 1.0.0
	 * @return string Failure message
	 */
	public function get_failure_message() {

		return $this->_failure_message;
	}


	/**
	 * Get the voting url, used when JS is not enabled/supported
	 *
	 * @since 1.0.0
	 * @param string $type Vote type. 'positive' or 'negative'. Defaults to 'positive'.
	 * @param string $base_url Base url to use. Defaults to teh current URL.
	 * @return string Url
	 */
	public function get_vote_url( $type = 'positive', $base_url = '' ) {

		$base_url = $base_url ? $base_url : "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$vote_url = add_query_arg( 'action', 'vote_for_contribution', $base_url );
		$vote_url = add_query_arg( 'type', $type, $vote_url );
		$vote_url = add_query_arg( 'comment_id', $this->get_id(), $vote_url );

		return $vote_url;
	}


	/**
	 * Determine if this contribution is of the provided type
	 *
	 * @since 1.0.0
	 * @param string $type Contribution type to compare against
	 * @return boolean True if is the provided type, false otherwise
	 */
	public function is_type( $type ) {
		return $type == $this->get_type();
	}


	/**
	 * Cast a vote for this contribution
	 *
	 * @since 1.0.0
	 * @param string $type Optional. Vote type. 'positive' or 'negative'. Defaults to 'positive'
	 * @param string $user_id Optional. User ID to cast vote as, defaults to current user ID
	 * @return mixed Vote count if successful, false otherwise
	 */
	public function cast_vote( $type = 'positive', $user_id = '' ) {

		$comment_id = $this->get_id();

		// Use the provided user ID or current user ID
		$user_id = $user_id ? $user_id : get_current_user_id();

		// User id is required to vote
		if ( ! $user_id ) {
			$this->_failure_message = __( 'You must be logged in to vote', 'woocommerce-product-reviews-pro' );
			return false;
		}

		// Users are not allowed to vote for their own contribution
		if ( $user_id == $this->contributor_id ) {
			$this->_failure_message = __( "You can't vote for yourself", 'woocommerce-product-reviews-pro' );
			return false;
		}

		$votes = array(
			'positive' => intval( $this->get_positive_votes() ),
			'negative' => intval( $this->get_negative_votes() ),
		);

		$users_votes = $this->get_users_votes();

		// Special cases: user is removing or changing their vote
		if ( $this->has_user_voted( $user_id ) ) {

			$previous_type = $this->get_user_vote( $user_id );

			// Remove user's previous vote
			$votes[ $previous_type ]--;
			update_comment_meta( $comment_id, $previous_type . '_votes', $votes[ $previous_type ] );

			// Forget user's vote
			unset( $users_votes[ $user_id ] );
		}

		// Cast new vote if user has not voted before, OR, if they are
		// changing their vote
		if ( ! $this->has_user_voted() || $type != $previous_type ) {

			$votes[ $type ]++;
			update_comment_meta( $comment_id, $type . '_votes', $votes[ $type ] );

			// Remember that this user has now voted
			$users_votes[ $user_id ] = $type;
		}

		$this->positive_votes = $votes['positive'];
		$this->negative_votes = $votes['negative'];

		// Update comment karma
		wp_update_comment( array(
			'comment_ID'    => $comment_id,
			'comment_karma' => $this->positive_votes - $this->negative_votes,
		) );

		// Update user's votes
		update_comment_meta( $comment_id, 'users_votes', $users_votes );

		return $votes[ $type ];
	}


	/**
	 * Flag contribution for removal
	 *
	 * @since 1.0.0
	 * @param string $reason  optional Flag reason, optional
	 * @param string $user_id optional User ID to cast vote as
	 * @return boolean True if flagged successfully, false otherwise
	 */
	public function flag( $reason = null, $user_id = '' ) {

		$comment_id = $this->get_id();

		// Use the provided user ID or current user ID
		$user_id = $user_id ? $user_id : get_current_user_id();

		// Users are not allowed to flag more than once
		if ( $this->has_user_flagged( $user_id ) ) {
			$this->_failure_message = __( 'You have already flagged this contribution', 'woocommerce-product-reviews-pro' );
			return false;
		}

		// Users are not allowed to flag for their own contribution
		if ( is_user_logged_in() && $user_id == $this->contributor_id ) {
			$this->_failure_message = __( "You can't flag your own contributions!", 'woocommerce-product-reviews-pro' );
			return false;
		}

		// Should be good to go, flag contribution
		$this->flag_count++;
		update_comment_meta( $comment_id, 'flag_count', $this->get_flag_count() );

		// If a reason was provided, save it
		if ( $reason ) {

			add_comment_meta( $comment_id, 'flag_reason', $reason );
		}

		// Mark the current user as having flagged this contribution
		if ( $user_id ) {
			$users_flagged[] = $user_id;
			update_comment_meta( $comment_id, 'users_flagged', implode( ',', $users_flagged ) );
		}

		// Set flag cookie, expiring in 10 years
		if ( ! $user_id || get_current_user_id() == $user_id ) {
			$flagged_comments[] = $comment_id;
			setcookie( 'wc_product_reviews_pro_flagged_comments', implode( ',', $flagged_comments ), time() + ( 10 * YEAR_IN_SECONDS ) );
		}

		return true;
	}


}
