<?php

// post thumbnail support
add_theme_support('post-thumbnails', array('woo_product_bages'));

// Creating a function to create our CPT
add_action( 'init', 'woo_pro_badge_man_custom_post' ); 
function woo_pro_badge_man_custom_post() {
 
    $labels = array( 
  	'name'               => __( 'WOO Product Badges', 'woo_product_badge_manager_txtd' ),
		'singular_name'      => __( 'Product Badge', 'woo_product_badge_manager_txtd' ),
		'add_new'            => __( 'Add New Product Badge', 'woo_product_badge_manager_txtd' ),
		'add_new_item'       => __( 'Add New Product Badge', 'woo_product_badge_manager_txtd}' ),
		'edit_item'          => __( 'Edit Badge', 'woo_product_badge_manager_txtd' ),
		'new_item'           => __( 'New Product Badge', 'woo_product_badge_manager_txtd' ),
		'view_item'          => __( 'View Badge', 'woo_product_badge_manager_txtd' ),
		'search_items'       => __( 'Search Product Badges', 'woo_product_badge_manager_txtd' ),
		'not_found'          => __( 'No Product Badges Found', 'woo_product_badge_manager_txtd' ),
		'not_found_in_trash' => __( 'No Product Badges Found In Trash', 'woo_product_badge_manager_txtd' ),
		'parent_item_colon'  => __( 'Parent Badge', 'woo_product_badge_manager_txtd' ),
		'menu_name'          => __( 'WOO Product Badges', 'woo_product_badge_manager_txtd' ),
    );
 
    $args = array( 
		'labels'              => $labels,
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		//'menu_icon'         => '',
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post', 
		'supports'            => array( 'title', 'thumbnail', 'editor' ),
    );
 
    register_post_type( 'woo_product_bages', $args );
}
