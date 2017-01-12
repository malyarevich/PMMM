<?php

    /* ==================================================

    Directory Post Type Functions

    ================================================== */


    /* DIRECTORY CATEGORY
    ================================================== */
    if ( !function_exists('sf_directory_category_register') ) {
        function sf_directory_category_register() {

            $directory_permalinks = get_option( 'sf_directory_permalinks' );

            $args = array(
                "label"             => __( 'Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Category', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $directory_permalinks['category_base'] ) ? __( 'directory-category', 'swift-framework-plugin' ) : __( $directory_permalinks['category_base'] , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'directory-category', 'directory', $args );
        }
        add_action( 'init', 'sf_directory_category_register' );
    }


    /* DIRECTORY LOCATION
    ================================================== */
    if ( !function_exists('sf_directory_location_register') ) {
        function sf_directory_location_register() {
    		
    		$directory_permalinks = get_option( 'sf_directory_permalinks' );
    		$directory_permalink  = empty( $directory_permalink['location_base'] ) ? __( 'directory-location', 'swift-framework-plugin' ) : __( $directory_permalinks['location_base'] , 'swift-framework-plugin' );

            $args = array(
                "label"             => __( 'Locations', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Location', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => $directory_permalink != "directory-location" ? array(
                    'slug'       => untrailingslashit( $directory_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'query_var'         => true
            );

            register_taxonomy( 'directory-location', 'directory', $args );
        }
        add_action( 'init', 'sf_directory_location_register' );
    }


    /* DIRECTORY POST TYPE
    ================================================== */
    if ( !function_exists('sf_directory_register') ) {
        function sf_directory_register() {

            $directory_permalinks = get_option( 'sf_directory_permalinks' );
            $directory_permalink  = empty( $directory_permalinks['directory_base'] ) ? __( 'directory', 'swift-framework-plugin' ) : __( $directory_permalinks['directory_base'] , 'swift-framework-plugin' );

            $labels = array(
                'name'               => _x( 'Directory', 'post type general name', 'swift-framework-plugin' ),
                'singular_name'      => _x( 'Directory Item', 'post type singular name', 'swift-framework-plugin' ),
                'add_new'            => _x( 'Add New', 'directory item', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Directory Item', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Directory Item', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Directory Item', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Diretory Item', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Directory Items', 'swift-framework-plugin' ),
                'not_found'          => __( 'No directory items have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => false,
                'menu_icon'         => 'dashicons-groups',
                'hierarchical'      => false,
                'rewrite'           => $directory_permalink != "directory" ? array(
                    'slug'       => untrailingslashit( $directory_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'revisions' ),
                'has_archive'       => true,
                'taxonomies'        => array( 'directory-category', 'directory-location' )

            );

            register_post_type( 'directory', $args );
        }
        add_action( 'init', 'sf_directory_register' );
    }


    /* DIRECTORY POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_directory_edit_columns') ) {
        function sf_directory_edit_columns( $columns ) {
            $columns = array(
                "cb"                 => "<input type=\"checkbox\" />",
                "thumbnail"          => "",
                "title"              => __( "Directory Item", 'swift-framework-plugin' ),
                "description"        => __( "Description", 'swift-framework-plugin' ),
                "location"           => __( "Address", 'swift-framework-plugin' ),
                "directory-category" => __( "Categories", 'swift-framework-plugin' )
            );

            return $columns;
        }
        add_filter( "manage_edit-directory_columns", "sf_directory_edit_columns" );
    }

?>