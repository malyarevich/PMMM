<?php

    /* ==================================================

    Team Post Type Functions

    ================================================== */


    /* TEAM CATEGORY
    ================================================== */
    if ( !function_exists('sf_team_category_register') ) {
        function sf_team_category_register() {

            $team_permalinks = get_option( 'sf_team_permalinks' );

            $args = array(
                "label"             => __( 'Team Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Team Category', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $team_permalinks['category_base'] ) ? __( 'team-category', 'swift-framework-plugin' ) : __( $team_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'team-category', 'team', $args );
        }
        add_action( 'init', 'sf_team_category_register' );
    }


    /* TEAM POST TYPE
    ================================================== */
    if ( !function_exists('sf_team_register') ) {
        function sf_team_register() {

            $team_permalinks = get_option( 'sf_team_permalinks' );
            $team_permalink  = empty( $team_permalinks['team_base'] ) ? __( 'team', 'swift-framework-plugin' ) : __( $team_permalinks['team_base']  , 'swift-framework-plugin' );

            $labels = array(
                'name'               => __( 'Team', 'swift-framework-plugin' ),
                'singular_name'      => __( 'Team Member', 'swift-framework-plugin' ),
                'add_new'            => __( 'Add New', 'team member', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Team Member', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Team Member', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Team Member', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Team Member', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Team Members', 'swift-framework-plugin' ),
                'not_found'          => __( 'No team members have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-groups',
                'rewrite'           => $team_permalink != "team" ? array(
                    'slug'       => untrailingslashit( $team_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'revisions' ),
                'has_archive'       => true,
                'taxonomies'        => array( 'team-category', 'post_tag' )
            );

            register_post_type( 'team', $args );
        }
        add_action( 'init', 'sf_team_register' );
    }


    /* TEAM POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_team_edit_columns') ) {
        function sf_team_edit_columns( $columns ) {
            $columns = array(
                "cb"            => "<input type=\"checkbox\" />",
                "thumbnail"     => "",
                "title"         => __( "Team Member", 'swift-framework-plugin' ),
                "description"   => __( "Description", 'swift-framework-plugin' ),
                "team-category" => __( "Categories", 'swift-framework-plugin' )
            );

            return $columns;
        }
        add_filter( "manage_edit-team_columns", "sf_team_edit_columns" );
    }

?>