<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'yes' ) {

		function prdctfltr_characteristics() {

			$labels = array(
				'name'                       => _x( 'Characteristics', 'taxonomy general name', 'prdctfltr' ),
				'singular_name'              => _x( 'Characteristic', 'taxonomy singular name', 'prdctfltr' ),
				'search_items'               => __( 'Search Characteristics', 'prdctfltr' ),
				'popular_items'              => __( 'Popular Characteristics', 'prdctfltr' ),
				'all_items'                  => __( 'All Characteristics', 'prdctfltr' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit Characteristics', 'prdctfltr' ),
				'update_item'                => __( 'Update Characteristics', 'prdctfltr' ),
				'add_new_item'               => __( 'Add New Characteristic', 'prdctfltr' ),
				'new_item_name'              => __( 'New Characteristic Name', 'prdctfltr' ),
				'separate_items_with_commas' => __( 'Separate Characteristics with commas', 'prdctfltr' ),
				'add_or_remove_items'        => __( 'Add or remove Characteristics', 'prdctfltr' ),
				'choose_from_most_used'      => __( 'Choose from the most used Characteristics', 'prdctfltr' ),
				'not_found'                  => __( 'No Characteristics found', 'prdctfltr' ),
				'menu_name'                  => __( 'Characteristics', 'prdctfltr' ),
			);

			$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'characteristics' ),
			);

			register_taxonomy( 'characteristics', array( 'product' ), $args );
		}

		add_action( 'init', 'prdctfltr_characteristics', 0 );

	}

?>