<?php

    /*
    *
    *	Swift Page Builder - Portfolio Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_portfolio extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $exclude_categories = $multi_size_ratio = $hover_style = $output = $tax_terms = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'              => '',
                'display_type'       => 'standard',
                'multi_size_ratio'   => '1/1',
                'fullwidth'          => 'no',
                'gutters'            => 'yes',
                'columns'            => '4',
                'show_title'         => 'yes',
                'show_subtitle'      => 'yes',
                'show_excerpt'       => 'no',
                'hover_show_excerpt' => 'no',
                'excerpt_length'     => '20',
                'item_count'         => '-1',
                'category'           => '',
                'order'              => 'desc',
                'order_by'           => 'none',
                'portfolio_filter'   => 'yes',
                'pagination'         => 'no',
                'button_enabled'     => 'no',
                'hover_style'        => 'default',
                'post_type'			 => 'portfolio',
                'el_position'        => '',
                'width'              => '1/1',
                'el_class'           => ''
            ), $atts ) );

            $view_all_icon = apply_filters( 'sf_view_all_icon' , '<i class="ss-layergroup"></i>' );


            // Enqueue script
            wp_enqueue_script( 'isotope' );
            if ( $display_type == "multi-size-masonry" ) {
                wp_enqueue_script( 'isotope-packery' );
            }


            /* SIDEBAR CONFIG
            ================================================== */
            global $sf_sidebar_config, $sf_options;

            $sidebars = '';
            if ( ( $sf_sidebar_config == "left-sidebar" ) || ( $sf_sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sf_sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }

            /* PORTFOLIO ITEMS
            ================================================== */
            $items = sf_portfolio_items( $atts );


            /* PAGE BUILDER OUTPUT
            ================================================== */
            $width    = spb_translateColumnWidthToSpan( $width );
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            $has_button     = false;
            $page_button    = $title_wrap_class = "";
            $portfolio_page = __( $sf_options['portfolio_page'], 'swift-framework-plugin' );
            if ( $portfolio_page != "" ) {
                $has_button  = true;
                $page_button = '<a class="sf-button medium white sf-icon-stroke " href="' . get_permalink( $portfolio_page ) . '">' . $view_all_icon . '<span class="text">' . __( "VIEW ALL PROJECTS", 'swift-framework-plugin' ) . '</span></a>';
            }

            if ( $portfolio_filter == "yes" ) {
                $title_wrap_class .= 'has-filter ';
            } else if ( $has_button && $button_enabled == "yes" ) {
                $title_wrap_class .= 'has-button ';
            }
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                $title_wrap_class .= 'container ';
            }

            $output .= "\n\t" . '<div class="spb_portfolio_widget portfolio-wrap spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $title != '' || $portfolio_filter == "yes" ) {
                $output .= "\n\t\t" . '<div class="title-wrap clearfix ' . $title_wrap_class . '">';
                if ( $title != '' ) {
                    $output .= '<h2 class="spb-heading"><span>' . $title . '</span></h2>';
                }
                if ( $portfolio_filter == "yes" ) {
                    $output .= sf_portfolio_filter( '', $post_type );
                } else if ( $has_button && $button_enabled == "yes" ) {
                    $output .= $page_button;
                }
                $output .= '</div>';
            }
            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );

            global $sf_include_isotope, $sf_has_portfolio;
            $sf_include_isotope = true;
            $sf_has_portfolio   = true;

            return $output;

        }
    }

    /* PARAMS
    ================================================== */
    $pagination_types = array(
        __( 'None', 'swift-framework-plugin' ) => "none",
        __( 'Standard', 'swift-framework-plugin' )  => "standard"
    );

    $pagination_types = apply_filters( 'spb_portfolio_pagination_types', $pagination_types );
    
    $params = array(
        array(
            "type"        => "textfield",
            "heading"     => __( "Widget title", 'swift-framework-plugin' ),
            "param_name"  => "title",
            "value"       => "",
            "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Display type", 'swift-framework-plugin' ),
            "param_name"  => "display_type",
            "value"       => array(
                __( 'Standard', 'swift-framework-plugin' )           => "standard",
                __( 'Gallery', 'swift-framework-plugin' )            => "gallery",
                __( 'Masonry', 'swift-framework-plugin' )            => "masonry",
                __( 'Masonry Gallery', 'swift-framework-plugin' )    => "masonry-gallery",
                __( 'Multi Size Masonry', 'swift-framework-plugin' ) => "multi-size-masonry"
            ),
            "description" => __( "Select the type of portfolio you'd like to show.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Multi Size Masonry Ratio", 'swift-framework-plugin' ),
            "param_name"  => "multi_size_ratio",
            "value"       => array( "1/1", "4/3" ),
            "required"       => array("display_type", "=", "multi-size-masonry"),
            "description" => __( "Choose whether to display 4/3, or 1/1 ratio thumbnails.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Full Width", 'swift-framework-plugin' ),
            "param_name"  => "fullwidth",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes"
            ),
            "buttonset_on"  => "yes",
            "std" => "no",
            "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Gutters", 'swift-framework-plugin' ),
            "param_name"  => "gutters",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Select if you'd like spacing between the items, or not.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Column count", 'swift-framework-plugin' ),
            "param_name"  => "columns",
            "value"       => array( "5", "4", "3", "2", "1" ),
            "required"       => array("display_type", "!=", "multi-size-masonry"),
            "description" => __( "How many portfolio columns to display.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show title text", 'swift-framework-plugin' ),
            "param_name"  => "show_title",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("display_type", "or", "standard,masonry"),
            "description" => __( "Show the item title text.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show subtitle text", 'swift-framework-plugin' ),
            "param_name"  => "show_subtitle",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("display_type", "or", "standard,masonry"),
            "description" => __( "Show the item subtitle text.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show item excerpt", 'swift-framework-plugin' ),
            "param_name"  => "show_excerpt",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("display_type", "or", "standard,masonry"),
            "description" => __( "Show the item excerpt text.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Excerpt Hover", 'swift-framework-plugin' ),
            "param_name"  => "hover_show_excerpt",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "std"         => "no",
            "required"       => array("display_type", "or", "gallery,masonry-gallery,multi-size-masonry"),
            "description" => __( "Show the item excerpt on hover, instead of the arrow button.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Excerpt Length", 'swift-framework-plugin' ),
            "param_name"  => "excerpt_length",
            "value"       => "20",
            "description" => __( "The length of the excerpt for the posts.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Number of items", 'swift-framework-plugin' ),
            "param_name"  => "item_count",
            "value"       => "12",
            "description" => __( "The number of portfolio items to show per page. Leave blank to show ALL portfolio items.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "select-multiple",
            "heading"     => __( "Portfolio category", 'swift-framework-plugin' ),
            "param_name"  => "category",
            "value"       => sf_get_category_list( 'portfolio-category' ),
            "description" => __( "Choose the category from which you'd like to show the portfolio items.", 'swift-framework-plugin' )
        ),
        array(
        	    "type"        => "dropdown",
        	    "heading"     => __( "Order by", 'swift-framework-plugin' ),
        	    "param_name"  => "order_by",
        	    "value"       => array(
        	        __( 'None', 'swift-framework-plugin' ) => "none",
        	        __( 'ID', 'swift-framework-plugin' )  => "ID",
        	        __( 'Title', 'swift-framework-plugin' )  => "title",
        	        __( 'Date', 'swift-framework-plugin' )  => "date",
        	        __( 'Random', 'swift-framework-plugin' )  => "rand",
        	    ),
        	    "description" => __( "Select how you'd like the items to be ordered.", 'swift-framework-plugin' )
        ),
        array(
        	    "type"        => "dropdown",
        	    "heading"     => __( "Order", 'swift-framework-plugin' ),
        	    "param_name"  => "order",
        	    "value"       => array(
        	        __( "Descending", 'swift-framework-plugin' ) => "DESC",
        	        __( "Ascending", 'swift-framework-plugin' )  => "ASC"
        	    ),
        	    "description" => __( "Select if you'd like the items to be ordered in ascending or descending order.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Filter", 'swift-framework-plugin' ),
            "param_name"  => "portfolio_filter",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Show the portfolio category filter above the items.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Pagination", 'swift-framework-plugin' ),
            "param_name"  => "pagination",
            "value"       => $pagination_types,
            "description" => __( "Show portfolio pagination.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Portfolio Page Button", 'swift-framework-plugin' ),
            "param_name"  => "button_enabled",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("portfolio_filter", "=", "yes"),
            "description" => __( "Select if you'd like to include a button in the title bar to link through to all portfolio items. The page is set in Theme Options > Custom Post Type Options.", 'swift-framework-plugin' )
        )
    );

    if ( spb_get_theme_name() == "joyn" ) {
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Thumbnail Hover Style", 'swift-framework-plugin' ),
            "param_name"  => "hover_style",
            "value"       => array(
                __( 'Default', 'swift-framework-plugin' )     => "default",
                __( 'Standard', 'swift-framework-plugin' )    => "gallery-standard",
                __( 'Gallery Alt', 'swift-framework-plugin' ) => "gallery-alt-one",
                //__('Gallery Alt Two', 'swift-framework-plugin') => "gallery-alt-two",
            ),
            "description" => __( "Choose the thumbnail hover style for the asset. If set to 'Default', then this uses the thumbnail type set in the theme options.", 'swift-framework-plugin' )
        );
    }

    $params[] = array(
        "type"       => "section",
        "param_name" => "advanced_options",
        "heading"    => __( "Advanced Options", 'swift-framework-plugin' ),
    );

    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Post Type Override", 'swift-framework-plugin' ),
        "param_name"  => "post_type",
        "value"       => spb_get_post_types(),
        "description" => __( "If you'd like to override the post type for which the content is of this asset is made up of, then you can select it here.", 'swift-framework-plugin' )
    );

    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Extra class", 'swift-framework-plugin' ),
        "param_name"  => "el_class",
        "value"       => "",
        "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
    );


    /* SHORTCODE MAP
    ================================================== */
    SPBMap::map( 'spb_portfolio', array(
        "name"   => __( "Portfolio", 'swift-framework-plugin' ),
        "base"   => "spb_portfolio",
        "class"  => "spb_portfolio spb_tab_media",
        "icon"   => "icon-portfolio",
        "params" => $params
    ) );
