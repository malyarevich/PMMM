<?php

    /* ==================================================

    Portfolio Post Type Functions

    ================================================== */


    /* PORTFOLIO CATEGORY
    ================================================== */
    if ( !function_exists('sf_portfolio_category_register') ) {
        function sf_portfolio_category_register() {

            $portfolio_permalinks = get_option( 'sf_portfolio_permalinks' );

            $args = array(
                "label"             => __( 'Portfolio Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Portfolio Category', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $portfolio_permalinks['category_base'] ) ? __( 'portfolio-category', 'swift-framework-plugin' ) : __( $portfolio_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );
            register_taxonomy( 'portfolio-category', 'portfolio', $args );
        }

        add_action( 'init', 'sf_portfolio_category_register' );
    }


    /* PORTFOLIO POST TYPE
    ================================================== */
    if ( !function_exists('sf_portfolio_register') ) {
        function sf_portfolio_register() {

            $portfolio_permalinks = get_option( 'sf_portfolio_permalinks' );
            $portfolio_permalink  = empty( $portfolio_permalinks['portfolio_base'] ) ? __( 'portfolio', 'swift-framework-plugin' ) : __( $portfolio_permalinks['portfolio_base'] , 'swift-framework-plugin' );

            $labels = array(
                'name'               => __( 'Portfolio', 'swift-framework-plugin' ),
                'singular_name'      => __( 'Portfolio Item', 'swift-framework-plugin' ),
                'add_new'            => __( 'Add New', 'portfolio item', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Portfolio Item', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Portfolio Item', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Portfolio Item', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Portfolio Item', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Portfolio', 'swift-framework-plugin' ),
                'not_found'          => __( 'No portfolio items have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-format-image',
                'hierarchical'      => false,
                'rewrite'           => $portfolio_permalink != "portfolio" ? array(
                    'slug'       => untrailingslashit( $portfolio_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'supports'          => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields', 'excerpt', 'revisions', 'comments' ),
                'has_archive'       => true,
                'taxonomies'        => array( 'portfolio-category' )
            );

            register_post_type( 'portfolio', $args );
        }

        add_action( 'init', 'sf_portfolio_register' );
    }


    /* PORTFOLIO POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_portfolio_edit_columns') ) {
        function sf_portfolio_edit_columns( $columns ) {
            $columns = array(
                "cb"                 => "<input type=\"checkbox\" />",
                "thumbnail"          => "",
                "title"              => __( "Portfolio Item", 'swift-framework-plugin' ),
                "description"        => __( "Description", 'swift-framework-plugin' ),
                "portfolio-category" => __( "Categories", 'swift-framework-plugin' )
            );

            return $columns;
        }
        add_filter( "manage_edit-portfolio_columns", "sf_portfolio_edit_columns" );
    }

?>