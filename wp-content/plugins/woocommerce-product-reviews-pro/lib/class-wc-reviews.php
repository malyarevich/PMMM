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
 * @package   WC-Product-Reviews-Pro/Lib
 * @author    SkyVerge
 * @category  Admin
 * @copyright Copyright (c) 2015-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Reviews class
 *
 * @since 1.0.0
 */
class WC_Reviews {


	/** string reviews page hook name **/
	private $reviews_page_hook;


	/** @var \WC_Reviews_List_Table instance **/
	private $reviews_list_table;


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		require_once( plugin_dir_path( __FILE__ ) . 'wc-review-functions.php' );

		// Add admin menu items
		add_action( 'admin_menu', array( $this, 'add_menu_items' ) );

		// Highlight correct parent when editing a review
		add_filter( 'parent_file', array( $this, 'edit_review_parent_file' ) );

		// Enqueue styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ), 99 );

		// Add review screen to WooCommerce screen IDs
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_review_screen_id' ) );

		// Filter Edit Comment screen title & heading
		add_filter( 'gettext', array( $this, 'filter_edit_comments_screen_translations' ), 10, 2 );

		// Exclude product reviews from comments screen
		add_filter( 'comments_clauses', array( $this, 'exclude_reviews_from_comments' ), 10, 2 );

		// Filter comments count
		add_filter( 'wp_count_comments', array( $this, 'queue_count_comments_modifier' ), 1, 2 );

	}


	/**
	 * Add admin menu items
	 */
	public function add_menu_items() {

		$label = __( 'Reviews', 'woocommerce-product-reviews-pro' );

		// Unapproved reviews have status 0
		// @see WC_Contribution::$moderation
		$count = (int) wc_count_reviews( 'all', '0' );

		if ( $count ) {
			$label .= ' <span class="awaiting-mod count-' . $count . '"><span class="pending-count">' . $count . '</span></span>';
		}

		// Add reviews list table
		$page = add_submenu_page(
			'woocommerce',
			__( 'Reviews', 'woocommerce-product-reviews-pro' ),
			$label,
			'edit_posts',
			'reviews',
			array( $this, 'render_reviews_list_table' )
		);

		// WordPress generates the page hook name automatically and there
		// is no way to manually set or filter it, so to be sure we use
		// the correct hook name, we store a reference to it.
		$this->reviews_page_hook = $page;

		// Hook screen options to edit reviews page load
		add_action( "load-{$page}", array( $this, 'load_reviews_screen' ) );
	}


	/**
	 * Highlight WooCommerce -> Reviews admin menu item when editing a review
	 *
	 * Besides modifying the filterable $parent_file, this function modifies the
	 * global $submenu_file variable.
	 *
	 * @param string $parent_file
	 * @return string $parent_file
	 */
	public function edit_review_parent_file( $parent_file ) {

		global $submenu_file;
		$screen = get_current_screen();

		if ( 'comment' === $screen->id ) {

			$comment = get_comment( $_GET['c'] );

			if ( 'product' === get_post_type( $comment->comment_post_ID ) ) {

				$parent_file  = 'woocommerce';
				$submenu_file = 'reviews';
			}
		}

		return $parent_file;
	}


	/**
	 * Load reviews screen
	 */
	public function load_reviews_screen() {

		// Enqueue edit-comments.js
		wp_enqueue_script( 'admin-comments' );
		enqueue_comment_hotkeys_js();

		// Load & instantiate the reviews list table class
		require_once( plugin_dir_path( __FILE__ ) . 'class-wc-reviews-list-table.php' );

		// Supplying the screen name allows using many built-in
		// filters, such as `manage_{$screen}_columns`, etc.
		$this->reviews_list_table = new WC_Reviews_List_Table(
			array( 'screen' => $this->reviews_page_hook )
		);

		// Process bulk actions
		$this->reviews_list_table->process_bulk_action();

		// Add screen options
		$this->add_reviews_screen_options();
	}


	/**
	 * Add screen options to reviews screen
	 */
	public function add_reviews_screen_options() {

		// Add 'reviews per page' screen option
		add_screen_option( 'per_page', array( 'label' => _x( 'Reviews', 'reviews per page (screen options)', 'woocommerce-product-reviews-pro' ) ) );
	}


	/**
	 * Render reviews list table
	 */
	public function render_reviews_list_table() {

		// Prepare items
		$this->reviews_list_table->prepare_items();

		$comment_status = isset( $_REQUEST['comment_status'] ) ? $_REQUEST['comment_status'] : '';

		?>
		<div class="wrap">

			<h2><?php echo get_admin_page_title(); ?></h2>

			<?php $this->display_messages(); ?>

			<?php $this->reviews_list_table->views(); ?>

			<form id="reviews-filter" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

				<?php $this->reviews_list_table->search_box( __( 'Search reviews', 'woocommerce-product-reviews-pro' ), 'reviews' ); ?>

				<input type="hidden" name="comment_status" value="<?php echo esc_attr( $comment_status ); ?>" />
				<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr( current_time( 'mysql' , 1 ) ); ?>" />

				<?php $this->reviews_list_table->display(); ?>
			</form>

		</div>
		<?php

		wp_comment_reply( '-1', true, 'detail' );
		wp_comment_trashnotice();
	}


	/**
	 * Display messages related to reviews
	 */
	public function display_messages() {

		if ( isset( $_REQUEST['approved'] ) || isset( $_REQUEST['deleted'] ) || isset( $_REQUEST['trashed'] ) || isset( $_REQUEST['untrashed'] ) || isset( $_REQUEST['spammed'] ) || isset( $_REQUEST['unspammed'] ) || isset( $_REQUEST['same'] ) ) {

			$approved  = isset( $_REQUEST['approved']  ) ? (int) $_REQUEST['approved']  : 0;
			$deleted   = isset( $_REQUEST['deleted']   ) ? (int) $_REQUEST['deleted']   : 0;
			$trashed   = isset( $_REQUEST['trashed']   ) ? (int) $_REQUEST['trashed']   : 0;
			$untrashed = isset( $_REQUEST['untrashed'] ) ? (int) $_REQUEST['untrashed'] : 0;
			$spammed   = isset( $_REQUEST['spammed']   ) ? (int) $_REQUEST['spammed']   : 0;
			$unspammed = isset( $_REQUEST['unspammed'] ) ? (int) $_REQUEST['unspammed'] : 0;
			$same      = isset( $_REQUEST['same'] )      ? (int) $_REQUEST['same']      : 0;

			$messages = array();

			if ( $approved > 0 ) {
				$messages[] = sprintf( _n( '%s comment approved', '%s comments approved', $approved, 'woocommerce-product-reviews-pro' ), $approved );
			}

			if ( $spammed > 0 ) {
				$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : 0;
				$messages[] = sprintf( _n( '%s comment marked as spam.', '%s comments marked as spam.', $spammed, 'woocommerce-product-reviews-pro' ), $spammed ) . ' <a href="' . esc_url( wp_nonce_url( "edit-comments.php?doaction=undo&action=unspam&ids=$ids", "bulk-comments" ) ) . '">' . __('Undo', 'wc-product-reviews-pro') . '</a><br />';
			}

			if ( $unspammed > 0 ) {
				$messages[] = sprintf( _n( '%s comment restored from the spam', '%s comments restored from the spam', $unspammed, 'woocommerce-product-reviews-pro' ), $unspammed );
			}

			if ( $trashed > 0 ) {
				$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : 0;
				$messages[] = sprintf( _n( '%s comment moved to the Trash.', '%s comments moved to the Trash.', $trashed, 'woocommerce-product-reviews-pro' ), $trashed ) . ' <a href="' . esc_url( wp_nonce_url( "edit-comments.php?doaction=undo&action=untrash&ids=$ids", "bulk-comments" ) ) . '">' . __('Undo', 'wc-product-reviews-pro') . '</a><br />';
			}

			if ( $untrashed > 0 ) {
				$messages[] = sprintf( _n( '%s comment restored from the Trash', '%s comments restored from the Trash', $untrashed, 'woocommerce-product-reviews-pro' ), $untrashed );
			}

			if ( $deleted > 0 ) {
				$messages[] = sprintf( _n( '%s comment permanently deleted', '%s comments permanently deleted', $deleted, 'woocommerce-product-reviews-pro' ), $deleted );
			}

			if ( $same > 0 && $comment = get_comment( $same ) ) {
				switch ( $comment->comment_approved ) {
					case '1' :
						$messages[] = __( 'This comment is already approved.', 'woocommerce-product-reviews-pro' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . __( 'Edit comment', 'woocommerce-product-reviews-pro' ) . '</a>';
						break;
					case 'trash' :
						$messages[] = __( 'This comment is already in the Trash.', 'woocommerce-product-reviews-pro' ) . ' <a href="' . esc_url( admin_url( 'edit-comments.php?comment_status=trash' ) ) . '"> ' . __( 'View Trash', 'woocommerce-product-reviews-pro' ) . '</a>';
						break;
					case 'spam' :
						$messages[] = __( 'This comment is already marked as spam.', 'woocommerce-product-reviews-pro' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . __( 'Edit comment', 'woocommerce-product-reviews-pro' ) . '</a>';
						break;
				}
			}

			echo $messages ? '<div id="moderated" class="updated"><p>' . implode( "<br/>\n", $messages ) . '</p></div>' : '';
		}
	}


	/**
	 * Enqueue admin styles
	 *
	 * @param string $hook_suffix
	 */
	public function enqueue_scripts_styles( $hook_suffix ) {

		if ( 'woocommerce_page_reviews' === $hook_suffix ) {
			wp_enqueue_style( 'wc-reviews-admin', plugins_url( null, __FILE__ ) . '/wc-reviews-admin.css' );
		}
	}


	/**
	 * Add reviews screen ID to WooCommerce screen IDs
	 *
	 * @param array $screen_ids List of screen IDs
	 * @return array
	 */
	public function add_review_screen_id( $screen_ids ) {

		$wc_screen_id      = sanitize_title( __( 'WooCommerce', 'woocommerce-product-reviews-pro' ) );
		$reviews_screen_id = $wc_screen_id . '_page_reviews';

		if ( ! in_array( $reviews_screen_id, $screen_ids ) ) {
			$screen_ids[] = $reviews_screen_id;
		}

		return $screen_ids;
	}


	/**
	 * Replace Edit/Moderate Comment title/headline with Edit Review, when editing/moderating a review
	 *
	 * @param  string $translation Translated text.
	 * @param  string $text        Text to translate.
	 * @return string              Translated text.
	 */
	public function filter_edit_comments_screen_translations( $translation, $text ) {

		$replace_texts = array( 'Edit Comment', 'Moderate Comment' );

		// Bail out if not a text we should replace
		if ( ! in_array( $text, $replace_texts ) ) {
			return $translation;
		}

		global $comment;

		// Try to get comment from query params
		if ( ! $comment && isset( $_GET['action'] ) && 'editcomment' === $_GET['action'] && isset( $_GET['c'] ) ) {
			$comment_id = (int) $_GET['c'];
			$comment = get_comment( $comment_id );
		}

		// Only replace the translated text if we are editing a comment left on a product,
		// which effectively means its a review
		if ( $comment && 'product' === get_post_type( $comment->comment_post_ID ) ) {

			switch ( $text ) {

				case 'Edit Comment':
					$translation = __( 'Edit Review', 'woocommerce-product-reviews-pro' );
				break;

				case 'Moderate Comment':
					$translation = __( 'Moderate Review', 'woocommerce-product-reviews-pro' );
				break;

			}
		}

		return $translation;
	}


	/**
	 * Exclude reviews from comments screen
	 *
	 * This code should exclude product review comments from queries.
	 * Some queries (like the recent comments widget on the dashboard) are hardcoded
	 * and are not filtered.
	 *
	 * @param array $pieces
	 * @param WP_Comment_Query $query
	 * @return array
	 */
	public function exclude_reviews_from_comments( array $pieces, $query ) {

		if ( is_admin() ) {

			$screen = get_current_screen();

			if ( is_object( $screen ) && 'edit-comments' === $screen->id ) {
				global $wpdb;

				if ( ! $pieces['join'] ) {
					$pieces['join'] = '';
				}

				if ( ! strstr( $pieces['join'], "JOIN $wpdb->posts" ) ) {
					$pieces['join'] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";
				}

				if ( $pieces['where'] ) {
					$pieces['where'] .= ' AND ';
				}

				$pieces['where'] .= " $wpdb->posts.post_type NOT IN ('product')";

			}
		}

		return $pieces;
	}


	/**
	 * Queue count_comments modifier
	 *
	 * @param  array  $stats   An empty array.
	 * @param  int    $post_id Optional. The post ID.
	 * @return array
	 */
	public function queue_count_comments_modifier( $stats, $post_id ) {

		if ( ! $post_id && empty( $stats )  ) {
			add_filter( 'query', array( $this, 'filter_comments_count_query' ) );
		}

		return $stats;
	}


	/**
	 * Filter count comments query to return correct count for comments and reviews
	 *
	 * This filter should be used sparingly - only hook it when needed.
	 * As a precaution, this filter unhooks itself after it's done.
	 *
	 * @param  string $query The original query.
	 * @return string        The modified query.
	 */
	public function filter_comments_count_query( $query ) {

		// Sanity-check - is it really the comments count query?
		if ( false !== strpos( $query, "SELECT comment_approved, COUNT( * ) AS num_comments" ) ) {

			global $wpdb;
			$from = "FROM {$wpdb->comments}";
			$join = "FROM {$wpdb->comments} c LEFT JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID";

			$query = str_replace( $from, $join, $query );

			$has_where = ( false !== strpos( $query, "WHERE" ) );
			$where = ( $has_where ? " AND " : " WHERE " );

			// The global `wc_counting_reviews` variable is set by
			// by the `wc_count_reviews` function
			if ( isset( $GLOBALS['wc_counting_reviews'] ) && $GLOBALS['wc_counting_reviews'] ) {
				$where .= "p.post_type IN ('product') ";
			} else {
				$where .= "p.post_type NOT IN ('product', 'shop_order') ";
			}

			$query = str_replace( "GROUP BY", $where . "GROUP BY", $query );

			// Finally, unhook the filter to avoid unnecessary filtering
			// of other queries
			remove_filter( 'query', array( $this, 'filter_count_comments_query' ) );
		}

		return $query;
	}


}

new WC_Reviews();
