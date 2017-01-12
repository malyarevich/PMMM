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
 * @category  Admin
 * @copyright Copyright (c) 2015-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Admin class
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Admin {


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add 'flagged' status link
		add_filter( 'review_status_links', array( $this, 'contribution_status_links' ) );

		// Add custom contribution columns
		add_filter( 'manage_woocommerce_page_reviews_columns',          array( $this, 'add_custom_contributions_columns' ) );
		add_filter( 'manage_woocommerce_page_reviews_sortable_columns', array( $this, 'make_custom_contributions_sortable_columns' ) );

		// Render custom column contents
		add_action( 'manage_reviews_custom_column', array( $this, 'custom_contribution_column' ), 10, 2 );
		add_filter( 'review_column_parent_link',    array( $this, 'contribution_column_parent_link' ), 10, 2 );

		// Add type/media type filters to contributions screen
		add_action( 'restrict_manage_reviews', array( $this, 'restrict_manage_contribution_types' ), 1 );
		add_action( 'restrict_manage_reviews', array( $this, 'restrict_manage_contributions' ) );

		// Filter/order contributions by custom fields
		if ( version_compare( get_bloginfo( 'version' ), '4.2', '<' ) ) {
			add_filter( 'pre_get_comments', array( $this, 'modify_contributions_query' ) );
		} else {
			add_filter( 'parse_comment_query', array( $this, 'modify_contributions_query' ) );
		}

		// Load frontend styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// Process custom contribution actions
		add_action( 'admin_init', array( $this, 'process_contribution_action' ), 99 );

		// Display messages
		add_action( 'load-woocommerce_page_reviews', array( $this, 'enqueue_contribution_messages' ) );

		// Add contribution-related settings
		add_filter( 'woocommerce_products_general_settings', array( $this, 'add_contribution_settings' ) );

		// add product reviews admin report
		add_filter( 'woocommerce_admin_reports', array( $this, 'add_admin_reports' ) );

		// Add meta boxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

		// Save contibution title comment meta field
		add_action( 'comment_edit_redirect', array( $this, 'save_contribution_title_meta_box' ), 1, 2 );

		// Show contribution title in review list
		add_action( 'woocommerce_reviews_list_before_comment_text', array( $this, 'add_title_in_review_list' ) );

		// Filter Edit Comment screen title & heading
		add_filter( 'gettext', array( $this, 'filter_edit_comments_screen_translations' ), 9, 2 );

	}


	/**
	 * Add 'flagged' custom status link to contributions screen
	 *
	 * @since 1.0.0
	 * @param array $status_links
	 * @return array
	 */
	public function contribution_status_links( Array $status_links ) {

		global $post_id, $comment_status, $comment_type, $wpdb;

		/* translators: Placeholders: %s contribution flagged n times */
		$label = _n_noop( 'Flagged <span class="count">(<span class="flagged-count">%s</span>)</span>', 'Flagged <span class="count">(<span class="flagged-count">%s</span>)</span>', 'woocommerce-product-reviews-pro' );

		// Prepare the base link
		$link = add_query_arg( 'page', 'reviews', 'admin.php' );

		// Add comment type to the base link
		if ( ! empty( $comment_type ) && 'all' !== $comment_type ) {
			$link = add_query_arg( 'comment_type', $comment_type, $link );
		}

		$link = remove_query_arg( 'comment_status', $link );
		$link = add_query_arg( 'is_flagged', 1, $link );

		// Are we viewing flagged contributions?
		$is_flagged = isset( $_REQUEST['is_flagged'] ) && $_REQUEST['is_flagged'];
		$class      = $is_flagged ? ' class="current"' : '';

		// Remove current class from "all" when viewing flagged contributions
		if ( $is_flagged ) {
			$status_links['all'] = str_replace( 'current', '', $status_links['all'] );
		}

		// Fetch number of flagged contributions,
		// optionally filtered by current post_id (product)
		$where_comment = $post_id ? $wpdb->prepare( " AND c.comment_post_ID = %d", $post_id ) : '';
		$num_flagged   = $wpdb->get_var( "SELECT COUNT(c.comment_ID) FROM $wpdb->comments c LEFT JOIN $wpdb->commentmeta m ON c.comment_ID = m.comment_id WHERE m.meta_key = 'flag_count' AND m.meta_value > 0" . $where_comment );

		// If viewing contributiuons for a specific product, add that to the link as well
		if ( $post_id ) {
			$link = add_query_arg( 'p', absint( $post_id ), $link );
		}

		// Translate and format link
		$label = "<a href='" . esc_url( $link ) . "'$class>" . sprintf(
			translate_nooped_plural( $label, $num_flagged ),
			number_format_i18n( $num_flagged )
		) . '</a>';

		// Insert flagged status after spam
		$status_links = SV_WC_Helper::array_insert_after( $status_links, 'spam', array( 'is_flagged' => $label ) );

		return $status_links;
	}


	/**
	 * Add custom contribution columns
	 *
	 * @since 1.0.0
	 * @param  array $columns
	 * @return array
	 */
	public function add_custom_contributions_columns( Array $columns ) {

		$columns = SV_WC_Helper::array_insert_after( $columns, 'cb',      array( 'type'  => __( 'Type',  'woocommerce-product-reviews-pro' ) ) );
		$columns = SV_WC_Helper::array_insert_after( $columns, 'comment', array( 'votes' => __( 'Votes', 'woocommerce-product-reviews-pro' ) ) );
		$columns = SV_WC_Helper::array_insert_after( $columns, 'votes',   array( 'flags' => _x( 'Flags', 'number of times contribution has been flagged', 'woocommerce-product-reviews-pro' ) ) );

		return $columns;
	}


	/**
	 * Make custom columns sortable
	 *
	 * @since 1.0.0
	 * @param array $sortable Columns
	 * @return array $sortable Filtered columns to make sortable
	 */
	public function make_custom_contributions_sortable_columns( $sortable ) {

		$sortable['type']  = 'comment_type';
		$sortable['votes'] = 'comment_karma';
		$sortable['flags'] = 'flag_count';

		return $sortable;
	}


	/**
	 * Output custom contribution column content
	 *
	 * @since 1.0.0
	 * @param string $column_name
	 * @param int $comment_id
	 */
	public function custom_contribution_column( $column_name, $comment_id ) {

		global $comment;

		switch ( $column_name ) {

			case 'type':

				$contribution       = wc_product_reviews_pro_get_contribution_type( $comment->comment_type );
				$contribution_type  = $contribution->type;
				$contribution_title = $contribution->get_title();

				// Handle existing WooCommerce reviews (comments) before plugin activation
				if ( empty( $comment->comment_type ) ) {
					$contribution_type  = 0 !== (int) $comment->comment_parent ? 'contribution_comment' : 'review';
					$contribution_title = 'review' === $contribution_type      ? __( 'Review', 'woocommerce-product-reviews-pro' ) : __( 'Comment', 'woocommerce-product-reviews-pro' );
				}

				/* translators: Placeholders: %1$s - Contribution type (CSS class), %2$s - Contribution type name */
				printf( '<span class="contribution-type contribution-type-%1$s">%2$s</span>', esc_attr( $contribution_type ), esc_html( $contribution_title ) );

			break;

			case 'votes':

				$contribution = wc_product_reviews_pro_get_contribution( $comment );
				echo (int) $contribution->get_positive_votes(); ?><span class="vote vote-up"   data-comment-id="<?php echo $comment->comment_ID; ?>" title="<?php esc_attr_e( 'Positive votes', 'woocommerce-product-reviews-pro' ); ?>"></span><br><?php
				echo (int) $contribution->get_negative_votes(); ?><span class="vote vote-down" data-comment-id="<?php echo $comment->comment_ID; ?>" title="<?php esc_attr_e( 'Negative votes', 'woocommerce-product-reviews-pro' ); ?>"></span><?php

			break;

			case 'flags':

				$contribution = wc_product_reviews_pro_get_contribution( $comment );
				echo intval( $contribution->get_flag_count() );

			break;

		}

	}


	/**
	 * Load admin styles and scripts
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix the current URL filename, ie edit.php, post.php, etc
	 */
	public function load_styles_scripts( $hook_suffix ) {

		if ( 'woocommerce_page_reviews' == $hook_suffix ) {

			// admin CSS
			wp_enqueue_style( 'wc-product-reviews-pro-admin', wc_product_reviews_pro()->get_plugin_url() . '/assets/css/admin/wc-product-reviews-pro-admin.min.css', null, WC_Product_Reviews_Pro::VERSION );
		}

		if ( ( 'woocommerce_page_wc-settings' == $hook_suffix && isset( $_REQUEST['tab'] ) && 'products' == $_REQUEST['tab'] ) ||
				( 'comment.php' == $hook_suffix && isset( $_GET['action'] ) && 'editcomment' == $_GET['action'] )
		 ) {

			// admin-settings JS
			wp_enqueue_script( 'wc-product-reviews-pro-admin', wc_product_reviews_pro()->get_plugin_url() . '/assets/js/admin/wc-product-reviews-pro-admin.min.js', array( 'jquery' ), WC_Product_Reviews_Pro::VERSION );
			wp_localize_script( 'wc-product-reviews-pro-admin', 'wc_product_reviews_pro_admin', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'wc-product-reviews-pro-admin' ),
				'i18n'       => array(
					'ays_remove_attachment'     => __( 'Are you sure you want to remove the attachment from this contribution?', 'woocommerce-product-reviews-pro' ),
					'error_removing_attachment' => __( 'There was an error removing the attachment. Please try again later.', 'woocommerce-product-reviews-pro' ),
				)
			) );
		}

		if ( 'edit.php' == $hook_suffix && isset( $_GET['post_type'] ) && 'product' == $_GET['post_type'] ) {
			wc_enqueue_js("
				$('#wpbody').on('click', '#doaction, #doaction2', function(){
					var tax = 'product_review_qualifier';
					$('tr.inline-editor textarea[name=\"tax_input['+tax+']\"]').suggest( ajaxurl + '?action=ajax-tag-search&tax=' + tax, { delay: 500, minchars: 2, multiple: true, multipleSep: inlineEditL10n.comma } );
				});
			");
		}
	}


	/**
	 * Output the contribution type selector
	 *
	 * @since 1.0.0
	 */
	public function restrict_manage_contribution_types() {
		global $comment_type;

		?>
		<select name="comment_type">
			<option value=""><?php esc_html_e( 'All contribution types', 'woocommerce-product-reviews-pro' ); ?></option>
			<?php

				$contribution_types = array();

				foreach ( wc_product_reviews_pro()->get_contribution_types() as $type ) {

					$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
					$contribution_types[ $type ] = $contribution_type->get_title();
				}

				/**
				 * Filter the comment types dropdown menu.
				 *
				 * @param array $contribution_types An array of contribution types.
				 * @since 1.0.0
				 */
				$contribution_types = apply_filters( 'admin_contribution_types_dropdown', $contribution_types );

				foreach ( $contribution_types as $type => $label ) {

					echo "\t<option value='" . esc_attr( $type ) . "'" . selected( $comment_type, $type, false ) . '>' . esc_html( $label ) . '</option>' . PHP_EOL;
				}
			?>
		</select>
		<?php
	}


	/**
	 * Add media type dropdown to contributions list screen filter
	 *
	 * Also adds the is_flagged hidden input to the filter form
	 *
	 * @since 1.0.0
	 */
	public function restrict_manage_contributions() {

		$is_flagged = isset( $_REQUEST['is_flagged'] ) ? $_REQUEST['is_flagged'] : '';

		$media_options = array(
			''      => __( 'All media', 'woocommerce-product-reviews-pro' ),
			'photo' => __( 'Photo', 'woocommerce-product-reviews-pro' ),
			'video' => __( 'Video', 'woocommerce-product-reviews-pro' ),
		);

		$current = isset( $_REQUEST['media'] ) ? $_REQUEST['media'] : '';

		?>
		<select name="media">
			<?php foreach ( $media_options as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>

		<input type="hidden" name="is_flagged" value="<?php echo esc_attr( $is_flagged ); ?>" />
		<?php
	}

	/**
	 * Modify contributions query
	 *
	 * @since 1.0.0
	 * @see https://core.trac.wordpress.org/ticket/23469
	 * @see https://gist.github.com/markjaquith/681af58ce22d79c08c09
	 * @param \WP_Comment_Query $query The WP_Comment_Query instance
	 * @return object The (modified) query instance
	 */
	public function modify_contributions_query( $query ) {

		$screen = get_current_screen();

		if ( is_object( $screen ) && 'woocommerce_page_reviews' === $screen->id ) {

			// FILTER

			// Get the existing meta_query or create one now
			$meta_query = $query->query_vars['meta_query'] ? $query->query_vars['meta_query'] : array();
			$modified_meta_query = false;

			// Filter by attached media type
			if ( isset( $_REQUEST['media'] ) && $_REQUEST['media'] ) {

				$meta_query[] = array(
					'key'   => 'attachment_type',
					'value' => $_REQUEST['media'],
				);

				$modified_meta_query = true;
			}

			// Support querying flagged contributions
			if ( isset( $_REQUEST['is_flagged'] ) && $_REQUEST['is_flagged'] ) {

				$meta_query[] = array(
					'key'     => 'flag_count',
					'value'   => 1,
					'compare' => '>=',
					'type'    => 'NUMERIC'
				);

				$modified_meta_query = true;
			}

			// ORDERBY

			if ( 'flag_count' === $query->query_vars['orderby'] ) {

				// If we are only viewing flagged comments, this is easy
				if ( isset( $_REQUEST['is_flagged'] ) && $_REQUEST['is_flagged'] ) {

					$query->query_vars['orderby']  = 'meta_value_num';
					$query->query_vars['meta_key'] = 'flag_count';
				}

				// Otherwise, we need to pull up some magic
				else {

					add_filter( 'comments_clauses', array( $this, 'orderby_flag_count' ), 1, 2 );
				}
			}

			// Re-parse meta-query if modified
			if ( $modified_meta_query ) {

				$query->query_vars['meta_query'] = $meta_query;

				// WP 4.1 and under require reparsing the query vars, see https://core.trac.wordpress.org/ticket/23469
				if ( version_compare( get_bloginfo( 'version' ), '4.2', '<' ) ) {
					$query->meta_query->parse_query_vars( $query->query_vars );
				}
			}
		}

		return $query;
	}


	/**
	 * Process the selected action for a single contribution
	 *
	 * @since 1.0.0
	 */
	public function process_contribution_action() {

		if ( ! isset( $_REQUEST['action'] ) || ! isset( $_REQUEST['c'] ) ) {
			return;
		}

		switch ( $_REQUEST['action'] ) {
			case 'flagcomment' :

				$comment_id = absint( $_REQUEST['c'] );

				check_admin_referer( 'delete-comment_' . $comment_id );

				$noredir = isset( $_REQUEST['noredir'] );

				if ( ! $comment = get_comment( $comment_id ) ) {
					comment_footer_die( __( 'Oops, no comment with this ID.', 'woocommerce-product-reviews-pro' ) . sprintf( ' <a href="%s">' . __( 'Go back', 'woocommerce-product-reviews-pro' ) . '</a>.', 'admin.php?page=contributions' ) );
				}

				if ( '' != wp_get_referer() && ! $noredir && false === strpos( wp_get_referer(), 'page=contributions' ) ) {
					$redir = wp_get_referer();

				} elseif ( '' != wp_get_original_referer() && ! $noredir ) {
					$redir = wp_get_original_referer();

				} else {
					$redir = admin_url( 'admin.php?page=contributions' );
				}

				$redir = remove_query_arg( array( 'ids', 'flagged' ), $redir );

				$contribution = wc_product_reviews_pro_get_contribution( $comment_id );

				if ( $contribution && $contribution->flag() ) {
					$redir = add_query_arg( array( 'flagged' => '1' ), $redir );
				}

				wp_redirect( esc_url_raw( $redir ) );
				exit;

			break;
		}

	}


	/**
	 * Filter comment SQL clauses when sorting by flag_count
	 *
	 * Since WP_Meta_Query doesn't really support this kind of query,
	 * we need to construct it 'manually' by modifying the comment
	 * query SQL clauses. This ensures that when sorting by flag_count,
	 * ALL the comments are returned, regardless if they have the flag_count
	 * meta or not.
	 *
	 * @since 1.0.0
	 * @param array $pieces
	 * @return array modified pieces
	 */
	public function orderby_flag_count( $pieces ) {
		global $wpdb;

		$pieces['join']   .= " LEFT JOIN $wpdb->commentmeta cm ON ( wp_comments.comment_ID = cm.comment_id AND cm.meta_key = 'flag_count' )";
		$pieces['where']  .= " AND ( cm.meta_key = 'flag_count' OR cm.comment_id IS NULL )";
		$pieces['orderby'] = "cm.meta_value+0";

		return $pieces;
	}


	/**
	 * Enqueue (hook) admin notices to be shown on contributions screen
	 *
	 * @since 1.0.0
	 */
	public function enqueue_contribution_messages() {

		add_action( 'admin_notices', array( $this, 'contribution_admin_notices' ) );
	}


	/**
	 * Add contribution admin notices
	 *
	 * @since 1.0.0
	 */
	public function contribution_admin_notices() {

		$messages = array();

		if ( isset( $_REQUEST['flagged'] ) ) {

			$flagged = isset( $_REQUEST['flagged'] ) ? (int) $_REQUEST['flagged'] : 0;

			if ( $flagged > 0 ) {
				/* translators: Placeholders: %s count of contributions flagged */
				$messages[] = sprintf( _n( '%s contribution flagged', '%s contributions flagged', $flagged, 'woocommerce-product-reviews-pro' ), $flagged );

				echo '<div id="moderated" class="updated"><p>' . implode( "<br/>\n", $messages ) . '</p></div>';
			}
		}
	}


	/**
	 * Show parent comment edit link in contribution parent column
	 *
	 * @since 1.0.0
	 * @param string $link
	 * @param object $comment
	 * @return string
	 */
	public function contribution_column_parent_link( $link, $comment ) {

		$link = '&mdash;';

		// This contribution is a comment/response to another contribution
		if ( $comment->comment_parent ) {

			$parent_comment = get_comment( $comment->comment_parent );

			$type = $parent_comment->comment_type ? $parent_comment->comment_type : 'review';
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );

			ob_start();
			edit_comment_link( sprintf( _x( '%1$s by %2$s', '[contribution type] by [author name]', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title(), $parent_comment->comment_author ) );
			$link = ob_get_clean();
		}

		return $link;
	}


	/**
	 * Add contribution settings to product settings page
	 *
	 * @since 1.0.0
	 * @param array $settings
	 * @return array
	 */
	public function add_contribution_settings( $settings ) {

		$contribution_settings = $this->get_contribution_settings();
		$new_settings          = array();

		foreach ( $settings as $setting ) {

			$new_settings[] = $setting;

			if ( 'product_rating_options' === $setting['id'] && 'title' === $setting['type'] ) {

				foreach ( $contribution_settings as $contribution_setting ) {
					$new_settings[] = $contribution_setting;
				}
			}
		}

		$settings = $new_settings;

		return $settings;
	}

	/**
	 * Ger contribution settings for the product settings page
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_contribution_settings() {

		// Prepare contribution type options
		$contribution_types = wc_product_reviews_pro()->get_contribution_types();
		$contribution_type_options = array();

		foreach ( $contribution_types as $type ) {

			$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
			$contribution_type_options[ $type ] = $contribution_type->get_title();
		}

		// Prepare contribution settings
		$contribution_settings = array(
			array(
				'title'    => __( 'Contributions types', 'woocommerce-product-reviews-pro' ),
				'desc'     => __( 'Select which contribution types to enable', 'woocommerce-product-reviews-pro' ),
				'id'       => 'wc_product_reviews_pro_enabled_contribution_types',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc_tip' => true,
				'options'  => array(
					'all'      => __( 'Enable all contribution types', 'woocommerce-product-reviews-pro' ),
					'specific' => __( 'Enable specific contribution types only', 'woocommerce-product-reviews-pro' )
				),
			),
			array(
				'title'   => __( 'Specific contribution types', 'woocommerce-product-reviews-pro' ),
				'desc'    => '',
				'id'      => 'wc_product_reviews_pro_specific_enabled_contribution_types',
				'class'   => 'wc-enhanced-select',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multiselect',
				'options' => $contribution_type_options,
			),
			array(
				'title'     => __( 'Admin Badges', 'woocommerce-product-reviews-pro' ),
				'type'      => 'text',
				'desc'      => __( 'Leave blank to disable badges.', 'woocommerce-product-reviews-pro' ),
				'desc_tip'  => __( 'Enter the text to use on badges displayed on admin and shop manager contributions.', 'woocommerce-product-reviews-pro' ),
				'id'        => 'wc_product_reviews_pro_contribution_badge',
				'default'   => 'Admin',
			),
			array(
				'title'    => __( 'Sorting order', 'woocommerce-product-reviews-pro' ),
				'desc'     => __( 'Choose how contributions are sorted on product pages', 'woocommerce-product-reviews-pro' ),
				'id'       => 'wc_product_reviews_pro_contributions_orderby',
				'default'  => 'most_helpful',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc_tip' => true,
				'options'  => array(
					'most_helpful' => __( 'Most helpful first', 'woocommerce-product-reviews-pro' ),
					'newest'       => __( 'Newest first', 'woocommerce-product-reviews-pro' ),
				),
			),
			array(
				'title'    => __( 'Minimum word count', 'woocommerce-product-reviews-pro' ),
				'desc_tip' => __( 'Users need to enter at least this amount of words when posting a contribution', 'woocommerce-product-reviews-pro' ),
				'type'     => 'text',
				'id'       => 'wc_product_reviews_pro_min_word_count',
			),
			array(
				'title'    => __( 'Maximum word count', 'woocommerce-product-reviews-pro' ),
				'desc_tip' => __( 'Maximum number of words users can enter when posting a contribution', 'woocommerce-product-reviews-pro' ),
				'type'     => 'text',
				'id'       => 'wc_product_reviews_pro_max_word_count',
			),
			array(
				'title'    => __( 'Moderation', 'woocommerce-product-reviews-pro' ),
				'desc'     => __( 'Contributions must be manually approved', 'woocommerce-product-reviews-pro' ),
				'type'     => 'checkbox',
				'id'       => 'wc_product_reviews_pro_contribution_moderation'
			),
		);

		return $contribution_settings;
	}


	/**
	 * Add product reviews reports
	 *
	 * @since 1.0.0
	 * @param array $reports
	 * @return array
	 */
	public function add_admin_reports( $reports ) {

		$reports['reviews'] = array(
			'title'   => __( 'Reviews', 'woocommerce-product-reviews-pro' ),
			'reports' => array(
				'most_reviews' => array(
					'title'       => __( 'Most Reviews', 'woocommerce-product-reviews-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_most_reviews_admin_report' ),
				),
				'highest_rating' => array(
					'title'       => __( 'Highest Rating', 'woocommerce-product-reviews-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_highest_rating_admin_report' ),
				),
				'lowest_rating' => array(
					'title'       => __( 'Lowest Rating', 'woocommerce-product-reviews-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_lowest_rating_admin_report' ),
				),
			),
		);

		return $reports;
	}


	/**
	 * Output the most reviewed products report
	 *
	 * @since 1.0.0
	 */
	public static function get_most_reviews_admin_report() {

		require_once( wc_product_reviews_pro()->get_plugin_path() . '/includes/admin/class-wc-product-reviews-pro-admin-report-most-reviews.php' );
		$report = new WC_Product_Reviews_Pro_Admin_Report_Most_Reviews();
		$report->output_report();
	}


	/**
	 * Output the highest rated products report
	 *
	 * @since 1.0.0
	 */
	public static function get_highest_rating_admin_report() {

		require_once( wc_product_reviews_pro()->get_plugin_path() . '/includes/admin/class-wc-product-reviews-pro-admin-report-highest-rating.php' );
		$report = new WC_Product_Reviews_Pro_Admin_Report_Highest_Rating();
		$report->output_report();
	}


	/**
	 * Output the lowest rated products report
	 *
	 * @since 1.0.0
	 */
	public static function get_lowest_rating_admin_report() {

		require_once( wc_product_reviews_pro()->get_plugin_path() . '/includes/admin/class-wc-product-reviews-pro-admin-report-lowest-rating.php' );
		$report = new WC_Product_Reviews_Pro_Admin_Report_Lowest_Rating();
		$report->output_report();
	}


	/**
	 * Add contribution meta boxes
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		// Product contributions
		if ( 'comment' === get_current_screen()->id && isset( $_GET['c'] ) ) {

			$comment_id = intval( $_GET['c'] );
			$comment    = get_comment( $comment_id );

			// Bail out if comment not found
			if ( ! $comment ) {
				return;
			}

			if ( in_array( $comment->comment_type, wc_product_reviews_pro()->get_contribution_types() ) ) {

				$contribution = wc_product_reviews_pro_get_contribution( $comment );

				if ( in_array( $contribution->get_type(), array( 'review', 'photo', 'video' ) ) ) {
					add_meta_box( 'wc-product-reviews-pro-title', __( 'Title', 'woocommerce-product-reviews-pro' ), array( $this, 'contribution_title_meta_box' ), 'comment', 'normal', 'high' );
				}

				add_meta_box( 'wc-product-reviews-pro-stats', __( 'Stats', 'woocommerce-product-reviews-pro' ), array( $this, 'contribution_stats_meta_box' ), 'comment', 'normal', 'high' );
				add_meta_box( 'wc-product-reviews-pro-flags', __( 'Flags', 'woocommerce-product-reviews-pro' ), array( $this, 'contribution_flags_meta_box' ), 'comment', 'normal', 'high' );

				if ( in_array( $contribution->get_type(), array( 'video', 'photo' ) ) || $contribution->has_attachment() ) {
					add_meta_box( 'wc-product-reviews-pro-attachment', __( 'Attached media', 'woocommerce-product-reviews-pro' ), array( $this, 'contribution_attachment_meta_box' ), 'comment', 'normal', 'high' );
				}
			}
		}
	}


	/**
	 * Output the title meta box HTML
	 *
	 * @since 1.0.4
	 */
	public function contribution_title_meta_box() {
		global $comment;

		wp_nonce_field( 'wc_product_reviews_pro_save_comment_meta', 'wc_product_reviews_comment_meta_nonce' );

		$title = get_comment_meta( $comment->comment_ID, 'title', true );
		echo '<input type="text" name="title" value="' . esc_attr( $title ) . '" id="title" style="width:100%;">';
	}


	/**
	 * Saves the title comment meta
	 *
	 * @since 1.0.4
	 * @param string $location The URI the user will be redirected to.
	 * @param int $comment_id The ID of the comment being edited.
	 * @return string The URI the user should be redirected to.
	 */
	public function save_contribution_title_meta_box( $location, $comment_id ) {

		// $comment_id is required
		if ( empty( $comment_id ) ) {
			return $location;
		}

		// Check if the title is set
		if ( ! isset( $_POST['title'] ) ) {
			return $location;
		}

		// Check the nonce
		if ( empty( $_POST['wc_product_reviews_comment_meta_nonce'] ) || ! wp_verify_nonce( $_POST['wc_product_reviews_comment_meta_nonce'], 'wc_product_reviews_pro_save_comment_meta' ) ) {
			return $location;
		}

		// Check if user has permission to edit comments
		if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
			return $location;
		}

		$comment      = get_comment( $comment_id );
		$contribution = wc_product_reviews_pro_get_contribution( $comment );

		// save the comment meta if the contribution type supports the title field
		if ( in_array( $contribution->get_type(), array( 'review', 'photo', 'video' ) ) ) {

			update_comment_meta( $comment_id, 'title', $_POST['title'] );
		}

		return $location;
	}


	/**
	 * Output the stats meta box HTML
	 *
	 * @since 1.0.0
	 */
	public function contribution_stats_meta_box() {

		global $comment;

		$contribution = wc_product_reviews_pro_get_contribution( $comment );
		$contribution_type = wc_product_reviews_pro_get_contribution_type( $contribution->get_type() );

		echo '<p>' . esc_html__( 'Type', 'woocommerce-product-reviews-pro' ) . ': ' . $contribution_type->get_title() . '</p>';

		echo '<p>' . esc_html__( 'Product', 'woocommerce-product-reviews-pro' ) . ': <a href="' . get_edit_post_link( $contribution->product_id ) . '">' . get_the_title( $contribution->product_id ) . '</a></p>';

		echo '<p>' . esc_html__( 'Upvotes', 'woocommerce-product-reviews-pro' ) . ': ' . absint( $contribution->get_positive_votes() ) . '</p>';

		echo '<p>' . esc_html__( 'Downvotes', 'woocommerce-product-reviews-pro' ) . ': ' . absint( $contribution->get_negative_votes() ) . '</p>';

		echo '<p>' . esc_html__( 'Flags', 'woocommerce-product-reviews-pro' ) . ': ' . absint( $contribution->get_flag_count() ) . '</p>';


		$review_qualifiers = wp_get_post_terms( $contribution->product_id, 'product_review_qualifier' );

		if ( ! empty( $review_qualifiers ) ) {

			$applied_qualifiers = array();

			foreach ( $review_qualifiers as $review_qualifier ) {

				if ( $value = get_comment_meta( $contribution->id, 'wc_product_reviews_pro_review_qualifier_' . $review_qualifier->term_id, true ) ) {
					$applied_qualifiers[ $review_qualifier->name ] = $value;
				}
			}

			if ( ! empty( $applied_qualifiers ) ) {

				echo '<h4>' . esc_html__( 'Qualifiers', 'woocommerce-product-reviews-pro' ) . '</h4>';

				foreach ( $applied_qualifiers as $qualifier => $value ) {

					echo '<p>' . esc_html( $review_qualifier->name . ' - ' . $value ) . '</p>';
				}
			}
		}
	}


	/**
	 * Output the flags meta box HTML
	 *
	 * @since 1.0.0
	 */
	public function contribution_flags_meta_box() {

		global $comment;

		$flag_reasons = get_comment_meta( $comment->comment_ID, 'flag_reason' );

		if ( ! empty( $flag_reasons ) ) {

			foreach ( $flag_reasons as $key => $reason ) {
				echo '<p>' . ( $key + 1 ) . ' - ' . esc_html( $reason ) . '</p>';
			}
		} else {
			esc_html_e( 'No flag reasons given', 'woocommerce-product-reviews-pro' );
		}

	}


	/**
	 * Output the attachment meta box HTML
	 *
	 * @since 1.0.0
	 */
	public function contribution_attachment_meta_box() {

		global $comment;

		$contribution      = wc_product_reviews_pro_get_contribution( $comment );
		$attachment_url    = $contribution->get_attachment_url();
		$attachment_id     = $contribution->get_attachment_id();
		$image             = wp_get_attachment_image( $attachment_id, 'large' );
		$attachment_exists = $attachment_id && $image || $attachment_url;

		// Attachment controls
		if ( $attachment_exists ) {
			echo '<p>';
				if ( $attachment_url ) {
					echo __( 'Source:', 'woocommerce-product-reviews-pro' ) .  ' <a href="' . $attachment_url . '">' . $attachment_url . '</a>';
				} else if ( $attachment_id ) {
					echo '<a href="' . get_edit_post_link( $attachment_id ) . '">' . esc_html__( 'Edit attachment', 'woocommerce-product-reviews-pro' ) . '</a>';
				}
				echo ' | <a href="#" class="remove-attachment" data-comment-id="' . esc_attr( $comment->comment_ID ) . '">' . esc_html__( 'Remove attachment', 'woocommerce-product-reviews-pro' ) . '</a>';
			echo '</p>';

			// Display photo
			if ( 'photo' == $contribution->get_attachment_type() ) {
				if ( $attachment_url ) {
					echo '<img alt="" src="' . esc_url( $attachment_url ) . '" />';
				}
				else if ( $image  ) {
					echo $image;
				}
			}

			// Embed video, or simply display a link
			if ( 'video' == $contribution->get_attachment_type() && $attachment_url ) {
				$embed_code = wp_oembed_get( $attachment_url );
				echo $embed_code ? $embed_code : '<p>' . sprintf( '<a href="%1$s">%2$s</a>', $attachment_url, $attachment_url ) . '</p>';
			}
		} else {
			echo '<p>' . esc_html__( 'Attachment has been removed', 'woocommerce-product-reviews-pro' ) . '</p>';
		}

	}


	/**
	 * Display comment title just before the comment text
	 *
	 * @since 1.0.0
	 */
	public function add_title_in_review_list() {
		global $comment;

		if ( $title = get_comment_meta( $comment->comment_ID, 'title', true ) ) {
			echo '<h3 class="contribution-title">' . esc_html( $title ) . '</h3>';
		}
	}


	/**
	 * Replace Edit/Moderate Comment title/headline with Edit {$type}, when editing/moderating a contribution
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
		if ( ! $comment && isset( $_GET['action'] ) && 'editcomment' == $_GET['action'] && isset( $_GET['c'] ) ) {
			$comment_id = intval( $_GET['c'] );
			$comment = get_comment( $comment_id );
		}

		// Bail out if no comment type is set
		if ( ! $comment || ! $comment->comment_type ) {
			return $translation;
		}

		$contribution_types = wc_product_reviews_pro()->get_contribution_types();

		// Only replace the translated text if we are editing a comment left on a product,
		// which effectively means it's a review
		if ( in_array( $comment->comment_type, $contribution_types ) ) {

			$contribution_type = wc_product_reviews_pro_get_contribution_type( $comment->comment_type );

			switch ( $text ) {
				case 'Edit Comment':
					$translation = $contribution_type->get_edit_text();
					break;
				case 'Moderate Comment':
					$translation = $contribution_type->get_moderate_text();
					break;
			}
		}

		return $translation;
	}


}
