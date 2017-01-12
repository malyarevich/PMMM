<?php
/**
 * Plugin Name: WooCommerce Product Reviews Pro
 * Plugin URI: http://www.woothemes.com/products/woocommerce-product-reviews-pro/
 * Description: Extend WooCommerce product reviews to add video, photo, comment, and question contribution types, as well as review filtering, voting, and flagging.
 * Author: WooThemes / SkyVerge
 * Author URI: http://www.woothemes.com
 * Version: 1.6.5
 * Text Domain: woocommerce-product-reviews-pro
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2015-2016 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Product-Reviews-Pro
 * @author    SkyVerge
 * @category  Reviews
 * @copyright Copyright (c) 2015-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '43662c2508f9242c6ba1da8c535510a0', '570800' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.4.0', __( 'WooCommerce Product Reviews Pro', 'woocommerce-product-reviews-pro' ), __FILE__, 'init_woocommerce_product_reviews_pro', array(
	'minimum_wc_version'   => '2.4.13',
	'minimum_wp_version'   => '4.1',
	'backwards_compatible' => '4.4.0',
) );

function init_woocommerce_product_reviews_pro() {


/**
 * # WooCommerce Product Reviews Pro Main Plugin Class
 *
 * ## Plugin Overview
 *
 * ## Features
 *
 * ## Frontend Considerations
 *
 * ## Admin Considerations
 *
 * ## Database
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro extends SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.6.5';

	/** @var WC_Product_Reviews_Pro single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'product_reviews_pro';

	/** plugin meta prefix */
	const PLUGIN_PREFIX = 'wc_product_reviews_pro_';

	/** plugin text domain, DEPRECATED in 1.4.0 */
	const TEXT_DOMAIN = 'woocommerce-product-reviews-pro';

	/** @var \WC_Product_Reviews_Pro_Admin instance */
	protected $admin;

	/** @var \WC_Product_Reviews_Pro_Frontend instance */
	protected $frontend;

	/** @var \WC_Product_Reviews_Pro_AJAX instance */
	protected $ajax;

	/** @var \WC_Product_Reviews_Pro_Review_Qualifiers instance */
	protected $review_qualifiers;

	/** @var \WC_Product_Reviews_Pro_Contribution_Factory instance */
	protected $contribution_factory;

	/** @var  \WC_Product_Reviews_Pro_Query instance */
	protected $query;


	/**
	 * Initializes the plugin
	 *
	 * @since 1.0.0
	 * @return \WC_Product_Reviews_Pro
	 */
	public function __construct() {

		parent::__construct( self::PLUGIN_ID, self::VERSION );

		// admin
		if ( is_admin() && ! is_ajax() ) {

			// delay standard install so we can use get_comments()
			remove_action( 'wp_loaded', array( $this, 'do_install' ) );
			add_action( 'admin_init', array( $this, 'do_install' ) );
		}

		// include required files
		$this->includes();

		add_action( 'wp_update_comment_count', array( $this, 'clear_transients' ) );

		add_filter( 'pre_option_woocommerce_enable_review_rating', array( $this, 'filter_enable_review_rating' ) );

		// register widgets; make sure we're later than the WC widgets so we can extend that class
		add_action( 'widgets_init', array( $this, 'register_widgets' ), 15 );

		// TODO move the following hooks & corresponding callbacks into an integrations class {FN 2016-05-23}

		// Points and Rewards compatibility - ensure only allowed contributions are counted towards earned review points
		add_filter( 'wc_points_rewards_review_post_comments_args',    array( $this, 'points_rewards_review_get_comments_args' ), 10, 2 );
		add_filter( 'wc_points_rewards_review_approve_comments_args', array( $this, 'points_rewards_review_get_comments_args' ), 10, 2 );

		// Points and Rewards compatibility - ensure points are only added for allowed contribution types
		add_filter( 'wc_points_rewards_pro_post_add_product_review_points',    array( $this, 'points_rewards_review_add_product_review_points' ), 10, 2 );
		add_filter( 'wc_points_rewards_pro_approve_add_product_review_points', array( $this, 'points_rewards_review_add_product_review_points' ), 10, 2 );

		// Tab Manager compatibility - correct the review count in the Reviews tab title
		add_filter( 'wc_tab_manager_reviews_tab_title_review_count', array( $this, 'tab_manager_set_reviews_tab_title_review_count' ), 10, 2 );
	}


	/**
	 * Include required files
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// query handler
		$this->query = $this->load_class( '/includes/class-wc-product-reviews-pro-query.php', 'WC_Product_Reviews_Pro_Query' );

		require_once( $this->get_plugin_path() . '/includes/class-wc-product-reviews-pro-emails.php' );

		$this->review_qualifiers = $this->load_class( '/includes/class-wc-product-reviews-pro-review-qualifiers.php', 'WC_Product_Reviews_Pro_Review_Qualifiers' );

		$this->contribution_factory = $this->load_class( '/includes/class-wc-product-reviews-pro-contribution-factory.php', 'WC_Product_Reviews_Pro_Contribution_Factory' );

		// contribution types handler
		require_once( $this->get_plugin_path() . '/includes/class-wc-product-reviews-pro-contribution-type.php' );
		// contribution types
		require_once( $this->get_plugin_path() . '/includes/abstract-wc-contribution.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-contribution-review.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-contribution-question.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-contribution-video.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-contribution-photo.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-contribution-comment.php' );

		require_once( $this->get_plugin_path() . '/includes/wc-product-reviews-pro-contribution-functions.php' );

		// load frontend
		if ( ! is_admin() || is_ajax() ) {
			$this->frontend_includes();
		}

		// load admin
		if ( is_admin() && ! is_ajax() ) {
			$this->admin_includes();
		}

		// load ajax
		$this->ajax = $this->load_class( '/includes/class-wc-product-reviews-pro-ajax.php', 'WC_Product_Reviews_Pro_AJAX' );
	}


	/**
	 * Include required frontend files
	 *
	 * @since 1.0.0
	 */
	private function frontend_includes() {

		require_once( $this->get_plugin_path() . '/includes/wc-product-reviews-pro-template-functions.php' );

		$this->frontend = $this->load_class( '/includes/frontend/class-wc-product-reviews-pro-frontend.php', 'WC_Product_Reviews_Pro_Frontend' );
	}


	/**
	 * Include required admin files
	 *
	 * @since 1.0.0
	 */
	private function admin_includes() {

		// TODO since WC_Reviews hasn't been merged into WC core anymore, perhaps we should move this into the extension itself {FN 2016-05-23}
		require_once( $this->get_plugin_path() . '/lib/class-wc-reviews.php' );

		$this->admin = $this->load_class( '/includes/admin/class-wc-product-reviews-pro-admin.php', 'WC_Product_Reviews_Pro_Admin' );
	}


	/**
	 * Load plugin text domain.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::load_translation()
	 */
	public function load_translation() {

		load_plugin_textdomain( 'woocommerce-product-reviews-pro', false, dirname( plugin_basename( $this->get_file() ) ) . '/i18n/languages' );
	}


	/**
	 * Get the Admin instance
	 *
	 * @since 1.6.0
	 * @return \WC_Product_Reviews_Pro_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Get the Frontend instance
	 *
	 * @since 1.6.0
	 * @return \WC_Product_Reviews_Pro_Frontend
	 */
	public function get_frontend_instance() {
		return $this->frontend;
	}


	/**
	 * Get the Ajax instance
	 *
	 * @since 1.6.0
	 * @return \WC_Product_Reviews_Pro_AJAX
	 */
	public function get_ajax_instance() {
		return $this->ajax;
	}


	/**
	 * Get the Review Qualifiers instance
	 *
	 * @since 1.6.0
	 * @return \WC_Product_Reviews_Pro_Review_Qualifiers
	 */
	public function get_review_qualifiers_instance() {
		return $this->review_qualifiers;
	}


	/**
	 * Get the Contribution Factory instance
	 *
	 * @since 1.6.0
	 * @return \WC_Product_Reviews_Pro_Contribution_Factory
	 */
	public function get_contribution_factory_instance() {
		return $this->contribution_factory;
	}


	/**
	 * Get the Query instance
	 *
	 * @since 1.6.0
	 * @return \WC_Product_Reviews_Pro_Query
	 */
	public function get_query_instance() {
		return $this->query;
	}


	/**
	 * Get contribution types
	 *
	 * @since 1.0.0
	 * @return array of contribution type names, ie ['review', 'question', 'video', 'photo', 'contribution_comment']
	 */
	public function get_contribution_types() {

		/**
		 * Filter the contribution types
		 *
		 * @since 1.0.0
		 * @param array $contribution_types The contribution types.
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_types', array(
			'review',
			'question',
			'video',
			'photo',
			'contribution_comment',
		) );
	}


	/**
	 * Get enabled contribution types
	 *
	 * @since 1.0.0
	 * @return array of contribution type names,
	 *          ie ['review', 'question', 'video', 'photo', 'contribution_comment']
	 */
	public function get_enabled_contribution_types() {

		if ( 'all' === get_option( 'wc_product_reviews_pro_enabled_contribution_types' ) ) {
			return $this->get_contribution_types();
		} else {
			return (array) get_option( 'wc_product_reviews_pro_specific_enabled_contribution_types' );
		}
	}


	/**
	 * Register Product Reviews Pro widgets
	 *
	 * @since 1.5.0
	 */
	public function register_widgets() {

		$contribution_types = $this->get_enabled_contribution_types();

		// bail if we don't have any enabled contribution types, no need to load widgets
		if ( empty( $contribution_types ) ) {
			return;
		}

		// remove contribution comments, they won't get a widget
		if ( ( $key = array_search( 'contribution_comment', $contribution_types ) ) !== false ) {
			unset( $contribution_types[ $key ] );
		}

		// load widgets only for enabled contribution types
		foreach ( $contribution_types as $type ) {

			// only load widgets for the types we expect to find and have widgets for
			if ( ! in_array( $type, array( 'review', 'question', 'video', 'photo' ), true ) ) {
				continue;
			}

			$path = 'includes/widgets/wc-product-reviews-pro-recent-' . $type . 's-widget.php';
			require_once( $path );

			$widget_class = 'WC_Product_Reviews_Pro_Recent_' . ucwords( $type ) . 's_Widget';
			register_widget( $widget_class );
		}
	}


	/**
	 * Filter the woocommerce_enable_review_rating option
	 *
	 * Checks if reviews have been enabled. If disabled, returns 'no'.
	 *
	 * @since 1.0.0
	 * @param  string $enabled
	 * @return string 'no' if reviews are disabled, pass-thru otherwise
	 */
	public function filter_enable_review_rating( $enabled ) {
		return ! in_array( 'review', $this->get_enabled_contribution_types(), true ) ? 'no' : $enabled;
	}


	/**
	 * Get the reviews product tab title based on enabled contribution types
	 *
	 * @since 1.4.3
	 * @param int $product_id The product ID
	 * @return string The reviews tab title based on enabled contribution types
	 */
	public function get_reviews_tab_title( $product_id ) {

		$title = '';

		$enabled_contribution_types = $this->get_enabled_contribution_types();

		// do not take contribution_comments into account
		if ( ( $key = array_search( 'contribution_comment', $enabled_contribution_types ) ) !== false ) {
			unset( $enabled_contribution_types[ $key ] );
		}

		// bail with empty title if none of the types are enabled
		if ( empty( $enabled_contribution_types ) ) {
			return $title;
		}

		// for single types, get their type-specific tab title
		elseif ( 1 === count( $enabled_contribution_types ) ) {

			$type              = $enabled_contribution_types[0];
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
			$count             = wc_product_reviews_pro_get_comments_number( $product_id, $type );
			$title             = $contribution_type->get_tab_title( $count );
		}

		// otherwise, get the Discussions title and correct number of contributions
		else {

			$count             = wc_product_reviews_pro_get_comments_number( $product_id, $enabled_contribution_types );
			$contribution_type = wc_product_reviews_pro_get_contribution_type( null );
			$title             = $contribution_type->get_tab_title( $count );
		}

		return $title;
	}


	/**
	 * Filter the get_comments arguments when a comment is posted or approved
	 *
	 * This ensures that only allowed contribution types are counted towards
	 * previously awarded points
	 *
	 * @since 1.0.6
	 * @param array $args The get_comments array of arguments
	 * @return array
	 */
	public function points_rewards_review_get_comments_args( $args ) {

		/**
		 * Filter the array of contribution types which should award points
		 *
		 * @since 1.0.6
		 * @param array $contribution_types The array of contribution types
		 */
		$comment_types = apply_filters( 'wc_product_reviews_pro_review_points_contribution_types', array( 'review' ) );

		return array_merge( $args, array( 'type' => $comment_types ) );
	}


	/**
	 * Filter if points should be added for a particular comment id on posting
	 * or approving a review.
	 *
	 * This ensures that points are only rewarded for the review contribution type
	 * but allows users to filter the types if needed.
	 *
	 * @since 1.0.6
	 * @param bool $add_points True if points should be awarded for this contribution (default),
	 *                          false otherwise
	 * @param int $comment_id The comment ID
	 * @return bool True if points should be awarded for this contribution, false otherwise
	 */
	public function points_rewards_review_add_product_review_points( $add_points, $comment_id ) {

		$comment = get_comment( $comment_id );

		// bail if there is an issue with retrieving the comment object
		if ( ! $comment ) {
			return $add_points;
		}

		/**
		 * Filter the array of contribution types which should award points
		 *
		 * @since 1.0.6
		 * @param array $contribution_types The array of contribution types
		 */
		$comment_types = apply_filters( 'wc_product_reviews_pro_review_points_contribution_types', array( 'review' ) );

		return in_array( $comment->comment_type, $comment_types );
	}


	/**
	 * Filter the Tab Manager Reviews tab title review count
	 *
	 * @since 1.4.3
	 * @param int $review_count The review count
	 * @param \WC_Product $product
	 * @return int The filtered review count
	 */
	public function tab_manager_set_reviews_tab_title_review_count( $review_count, $product ) {

		// get enabled contribution types
		$enabled_contribution_types = $this->get_enabled_contribution_types();

		// do not take contribution_comments into account
		if ( ( $key = array_search( 'contribution_comment', $enabled_contribution_types ) ) !== false ) {
			unset( $enabled_contribution_types[ $key ] );
		}

		return wc_product_reviews_pro_get_comments_number( $product->id, $enabled_contribution_types );
	}



	/** Admin methods ******************************************************/


	/**
	 * Render a notice for the user to read the docs before adding add-ons
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$this->get_admin_notice_handler()->add_admin_notice(
			/* translators: Placeholders: %1$s opening <a> html tag - %2$s closing </a> html tag - %3$s opening <a> html tag - %4$s closing </a> html tag - %5$s opening <a> html tag - %6$s closing </a> html tag */
			sprintf(
				__( 'Thanks for installing Product Reviews Pro! Before getting started, please take a moment to %1$sread the documentation%2$s, configure %3$ssettings%4$s or %5$semails%6$s :) ', 'woocommerce-product-reviews-pro' ),
				'<a href="http://docs.woothemes.com/document/woocommerce-product-reviews-pro/" target="_blank">',
				'</a>',
				'<a href="' . admin_url( "admin.php?page=wc-settings&tab=products" ) . '">',
				'</a>',
				'<a href="' . admin_url( "admin.php?page=wc-settings&tab=email&section=wc_product_reviews_pro_emails_new_comment" ) . '">',
				'</a>'
			),
			'read-the-docs-notice',
			array( 'always_show_on_settings' => false, 'notice_class' => 'updated' )
		);
	}


	/**
	 * Clear transients for a review.
	 *
	 * TODO: Can be removed when we drop support for WC 2.4 and use `WC_Product::get_rating_count()`
	 *
	 * @internal
	 * @see https://github.com/woothemes/woocommerce/issues/7214
	 * @param mixed $post_id the post identifier
	 */
	public function clear_transients( $post_id ) {
		$post_id = absint( $post_id );

		$transient_version = WC_Cache_Helper::get_transient_version( 'product' );

		delete_transient( 'wc_average_rating_' . $post_id .        $transient_version );
		delete_transient( 'wc_rating_count_' .   $post_id .        $transient_version );
		delete_transient( 'wc_rating_count_' .   $post_id . '_1' . $transient_version );
		delete_transient( 'wc_rating_count_' .   $post_id . '_2' . $transient_version );
		delete_transient( 'wc_rating_count_' .   $post_id . '_3' . $transient_version );
		delete_transient( 'wc_rating_count_' .   $post_id . '_4' . $transient_version );
		delete_transient( 'wc_rating_count_' .   $post_id . '_5' . $transient_version );
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Product Reviews Pro Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.0.0
	 * @see wc_product_reviews_pro()
	 * @return WC_Product_Reviews_Pro
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Product Reviews Pro', 'woocommerce-product-reviews-pro' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = '' ) {
		return admin_url( 'admin.php?page=wc-settings&tab=products' );
	}


	/**
	 * Returns true if on the gateway settings page
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the settings page
	 */
	public function is_plugin_settings() {
		return isset( $_GET['page'] ) && 'reviews' == $_GET['page'];
	}


	/**
	 * Gets the plugin documentation url
	 *
	 * @since 1.1.0
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woothemes.com/document/woocommerce-product-reviews-pro/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 1.1.0
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'http://support.woothemes.com/';
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Flush rewrite rules upon activation
	 *
	 * @since 1.6.0
	 * @see \SV_WC_Plugin::activate()
	 */
	public function activate() {

		flush_rewrite_rules();
	}


	/**
	 * Flush rewrite rules upon deactivation
	 *
	 * @since 1.6.0
	 * @see \SV_WC_Plugin::deactivate()
	 */
	public function deactivate() {

		flush_rewrite_rules();
	}


	/**
	 * Flush rewrite rules upon upgrade
	 *
	 * @since 1.6.0
	 * @see \SV_WC_Plugin::do_install()
	 * @see \SV_WC_Plugin::upgrade()
	 * @param string $installed_version
	 */
	protected function upgrade( $installed_version ) {

		flush_rewrite_rules();
	}


	/**
	 * Installation routine
	 *
	 * @since 1.0.0
	 * @see \SV_WC_Plugin::do_install()
	 * @see \SV_WC_Plugin::install()
	 */
	protected function install() {
		global $wpdb;

		// Default settings
		update_option( 'wc_product_reviews_pro_enabled_contribution_types', 'all' );
		update_option( 'wc_product_reviews_pro_contributions_orderby',      'most_helpful' );
		update_option( 'wc_product_reviews_pro_contribution_moderation',    get_option( 'comment_moderation' ) ? 'yes' : 'no' );

		// Set comment_type to 'review' on all comments that have a product as
		// their parent and no type set.  Page through comments in blocks to
		// avoid out of memory errors
		$offset           = (int) get_option( 'wc_product_reviews_pro_install_offset', 0 );
		$records_per_page = 500;

		do {

			$record_ids = get_comments( array(
				'post_type' => 'product',
				'type'      => '',
				'fields'    => 'ids',
				'offset'    => $offset,
				'number'    => $records_per_page,
			) );

			// some sort of bad database error: deactivate the plugin and display an error
			if ( is_wp_error( $record_ids ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				deactivate_plugins( 'woocommerce-product-reviews-pro/woocommerce-product-reviews-pro.php' );

				wp_die(
					sprintf( /* translators: Placeholders: %1$s - plugin name, %2$s - error message(s) */
						__( 'Error activating and installing %1$s: %2$s', 'woocommerce-product-reviews-pro' ),
						$this->get_plugin_name(),
						'<ul><li>' . implode( '</li><li>', $record_ids->get_error_messages() ) . '</li></ul>' ) .
					'<a href="' . admin_url( 'plugins.php' ) . '">' . esc_html__( '&laquo; Go Back', 'woocommerce-product-reviews-pro' ) . '</a>'
				);
			}

			if ( is_array( $record_ids ) ) {
				foreach ( $record_ids as $id ) {
					$wpdb->query( "UPDATE {$wpdb->comments} SET comment_type = 'review' WHERE comment_type = '' AND comment_ID = {$id}" );
				}
			}

			// increment offset
			$offset += $records_per_page;
			// and keep track of how far we made it in case we hit a script timeout
			update_option( 'wc_product_reviews_pro_install_offset', $offset );

		// while full set of results returned (meaning there may be more results still to retrieve)
		} while( count( $record_ids ) === $records_per_page );

		flush_rewrite_rules();
	}


	/** Deprecated methods ******************************************************/


	/**
	 * Handle deprecated properties calls for backwards compatibility
	 *
	 * TODO remove this as part of WC 2.7 compatibility update {FN 2016-05-23}
	 *
	 * @since 1.6.0
	 * @param string $property
	 * @return null|void|mixed
	 */
	public function __get( $property ) {

		$called     = __CLASS__ . '::' . $property;
		$main_class = 'wc_product_reviews_pro()';
		$deprecated = $main_class . '->' . $property;

		switch ( $property ) {

			case 'admin' :
				_deprecated_function( $deprecated, '1.6.0', $main_class . '->get_admin_instance()' );
				return $this->get_admin_instance();

			case 'ajax' :
				_deprecated_function( $deprecated, '1.6.0', $main_class . '->get_ajax_instance()' );
				return $this->get_ajax_instance();

			case 'frontend' :
				_deprecated_function( $deprecated, '1.6.0', $main_class . '->get_frontend_instance()' );
				return $this->get_frontend_instance();

			default :
				trigger_error( "Call to undefined property {$called}", E_USER_ERROR );
				return null;

		}
	}


}

/**
 * Returns the One True Instance of Product Reviews Pro
 *
 * @since 1.0.0
 * @return \WC_Product_Reviews_Pro
 */
function wc_product_reviews_pro() {
	return WC_Product_Reviews_Pro::instance();
}

// fire it up!
wc_product_reviews_pro();

} // init_woocommerce_product_reviews_pro()
