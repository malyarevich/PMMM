<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		WC_Post_types
 * @version		2.2.0
 * @package		WooCommerce/Classes/Products
 * @category	Class
 * @author 		WooThemes
 */
class WC_GZDP_Post_types {

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 10 );
	}

	/**
	 * Register core post types
	 */
	public static function register_post_types() {
		
		if ( post_type_exists( 'invoice' ) )
			return;

		do_action( 'woocommerce_register_post_type' );

		register_post_type(
			'invoice',
			array(
				'labels'              => array(
						'name'               => __( 'Invoices', 'woocommerce-germanized-pro' ),
						'singular_name'      => __( 'Invoice', 'woocommerce-germanized-pro' ),
						'add_new'            => __( 'Add Invoice', 'woocommerce-germanized-pro' ),
						'add_new_item'       => __( 'Add New Invoice', 'woocommerce-germanized-pro' ),
						'edit'               => __( 'Edit', 'woocommerce-germanized-pro' ),
						'edit_item'          => __( 'Edit Invoice', 'woocommerce-germanized-pro' ),
						'new_item'           => __( 'New Invoice', 'woocommerce-germanized-pro' ),
						'view'               => __( 'View Invoice', 'woocommerce-germanized-pro' ),
						'view_item'          => __( 'View Invoice', 'woocommerce-germanized-pro' ),
						'search_items'       => __( 'Search Invoices', 'woocommerce-germanized-pro' ),
						'not_found'          => __( 'No Invoices found', 'woocommerce-germanized-pro' ),
						'not_found_in_trash' => __( 'No Invoices found in trash', 'woocommerce-germanized-pro' ),
						'parent'             => __( 'Parent Invoices', 'woocommerce-germanized-pro' ),
						'menu_name'          => _x( 'Invoices', 'Admin menu name', 'woocommerce-germanized-pro' )
					),
				'description'         => __( 'This is where store invoices are stored.', 'woocommerce-germanized-pro' ),
				'public'              => false,
				'show_ui'             => false,
				'capability_type'     => 'shop_order',
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => false,
				'can_export'		  => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title', 'custom-fields' ),
				'has_archive'         => false,
			)
		);
	}

	/**
	 * Register our custom post statuses, used for order status
	 */
	public static function register_post_status() {
		register_post_status( 'wc-gzdp-pending', array(
			'label'                     => _x( 'Pending payment', 'Invoice status', 'woocommerce-germanized-pro' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
		) );
		register_post_status( 'wc-gzdp-paid', array(
			'label'                     => _x( 'Paid', 'Invoice status', 'woocommerce-germanized-pro' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
		) );
		register_post_status( 'wc-gzdp-cancelled', array(
			'label'                     => _x( 'Cancelled', 'Invoice status', 'woocommerce-germanized-pro' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
		) );
	}

}

WC_GZDP_Post_types::init();