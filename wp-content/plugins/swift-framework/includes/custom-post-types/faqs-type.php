<?php

	/* ==================================================

	FAQs Post Type Functions

	================================================== */

	/* FAQS CATEGORY
	================================================== */
    if ( !function_exists('sf_faqs_category_register') ) {
    	function sf_faqs_category_register() {

    		$faqs_permalinks = get_option( 'sf_faqs_permalinks' );

    	    $args = array(
    	        "label" 						=> __('Topics', 'swift-framework-plugin'),
    	        "singular_label" 				=> __('Topic', 'swift-framework-plugin'),
    	        'public'                        => true,
    	        'hierarchical'                  => true,
    	        'show_ui'                       => true,
    	        'show_in_nav_menus'             => false,
    	        'args'                          => array( 'orderby' => 'term_order' ),
    	        'rewrite'           => array(
                    'slug'       => empty( $faqs_permalinks['category_base'] ) ? __( 'faqs-category', 'swift-framework-plugin' ) : __( $faqs_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
    	    );

    	    register_taxonomy( 'faqs-category', 'faqs', $args );
    	}
    	add_action( 'init', 'sf_faqs_category_register' );
    }


	/* FAQS POST TYPE
    ================================================== */
    if ( !function_exists('sf_faqs_register') ) {
        function sf_faqs_register() {

    		$faqs_permalinks = get_option( 'sf_faqs_permalinks' );
            $faqs_permalink  = empty( $faqs_permalinks['faqs_base'] ) ? __( 'faqs', 'swift-framework-plugin' ) : __( $faqs_permalinks['faqs_base'] , 'swift-framework-plugin' );

            $labels = array(
                'name' => __('FAQs', 'swift-framework-plugin'),
                'singular_name' => __('Question', 'swift-framework-plugin'),
                'add_new' => __('Add New', 'swift-framework-plugin'),
                'add_new_item' => __('Add New Question', 'swift-framework-plugin'),
                'edit_item' => __('Edit Question', 'swift-framework-plugin'),
                'new_item' => __('New Question', 'swift-framework-plugin'),
                'view_item' => __('View Question', 'swift-framework-plugin'),
                'search_items' => __('Search Questions', 'swift-framework-plugin'),
                'not_found' =>  __('No questions have been added yet', 'swift-framework-plugin'),
                'not_found_in_trash' => __('Nothing found in Trash', 'swift-framework-plugin'),
                'parent_item_colon' => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'=> 'dashicons-editor-help',
                'rewrite'           => $faqs_permalink != "faqs" ? array(
                    'slug'       => untrailingslashit( $faqs_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'supports' => array('title', 'editor', 'revisions'),
                'has_archive' => true,
                'taxonomies' => array('faqs-category', 'post_tag')
            );

            register_post_type( 'faqs', $args );
        }
        add_action( 'init', 'sf_faqs_register' );
    }


	/* FAQS POST TYPE COLUMNS
	================================================== */
    if ( !function_exists('faqs_edit_columns') ) {
    	function faqs_edit_columns($columns){
            $columns = array(
                "cb" => "<input type=\"checkbox\" />",
                "title" => __("Question", 'swift-framework-plugin'),
                "description" => __("Answer", 'swift-framework-plugin'),
                "faqs-category" => __("Topics", 'swift-framework-plugin')
            );

            return $columns;
    	}

    	add_filter("manage_edit-faqs_columns", "faqs_edit_columns");
    }
?>