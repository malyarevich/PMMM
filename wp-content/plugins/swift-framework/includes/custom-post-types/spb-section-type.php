<?php

    /* ==================================================

    SPB Section Post Type Functions

    ================================================== */


    /* SPB SECTION CATEGORY
    ================================================== */
    if ( !function_exists('sf_spb_section_category_register') ) {
        function sf_spb_section_category_register() {
            $args = array(
                "label"             => __( 'Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Category', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'         => __( 'spb-section-category', 'swift-framework-plugin' ),
                    'with_front'   => false,
                    'hierarchical' => true,
                ),
                'query_var'         => true
            );

            register_taxonomy( 'spb-section-category', 'spb-section', $args );
        }
        add_action( 'init', 'sf_spb_section_category_register' );
    }


    /* SPB SECTION POST TYPE
    ================================================== */
    if ( !function_exists('sf_spb_section_register') ) {
        function sf_spb_section_register() {

            $labels = array(
                'name'               => _x( 'SPB Sections', 'post type general name', 'swift-framework-plugin' ),
                'singular_name'      => _x( 'SPB Section', 'post type singular name', 'swift-framework-plugin' ),
                'add_new'            => _x( 'Add New', 'SPB Section', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New SPB Section', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit SPB Section', 'swift-framework-plugin' ),
                'new_item'           => __( 'New SPB Section', 'swift-framework-plugin' ),
                'view_item'          => __( 'View SPB Section', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search SPB Sections', 'swift-framework-plugin' ),
                'not_found'          => __( 'No SPB Sections have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'              => $labels,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => false,
                'exclude_from_search' => true,
                'menu_icon'           => 'dashicons-schedule',
                'hierarchical'        => false,
                'rewrite'             => false,
                'supports'            => array( 'title', 'editor', 'revisions' ),
                'has_archive'         => true,
                'taxonomies'          => array( 'spb-section-category' )

            );

            register_post_type( 'spb-section', $args );
        }
        add_action( 'init', 'sf_spb_section_register' );
    }


    /* SPB POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_spb_section_edit_columns') ) {
        function sf_spb_section_edit_columns( $columns ) {
            $columns = array(
                "cb"                   => "<input type=\"checkbox\" />",
                "title"                => __( "SPB Section", 'swift-framework-plugin' ),
                "spb-section-category" => __( "Categories", 'swift-framework-plugin' )
            );

            return $columns;
        }
        add_filter( "manage_edit-spb-section_columns", "sf_spb_section_edit_columns" );
    }


    /* SPB FILTERS
    ================================================== */
    if ( !function_exists('sf_restrict_spb_sections_by_category') ) {
        function sf_restrict_spb_sections_by_category() {
            global $typenow;
            $post_type = 'spb-section';
            $taxonomy = 'spb-section-category';
            if ($typenow == $post_type) {
                $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
                $info_taxonomy = get_taxonomy($taxonomy);
                wp_dropdown_categories(array(
                    'show_option_all' => __("Show All {$info_taxonomy->label}"),
                    'taxonomy' => $taxonomy,
                    'name' => $taxonomy,
                    'orderby' => 'name',
                    'selected' => $selected,
                    'show_count' => true,
                    'hide_empty' => true,
                ));
            };
        }
        add_action('restrict_manage_posts', 'sf_restrict_spb_sections_by_category');
    }

    if ( !function_exists('sf_convert_id_to_term_in_query_spb_section') ) {
        function sf_convert_id_to_term_in_query_spb_section($query) {
            global $pagenow;
            $post_type = 'spb-section';
            $taxonomy = 'spb-section-category';
            $q_vars = &$query->query_vars;
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
                $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
                $q_vars[$taxonomy] = $term->slug;
            }
        }
        add_filter('parse_query', 'sf_convert_id_to_term_in_query_spb_section');
    }

?>