<?php

    /*
    *
    *	Swift Page Builder - Galleries Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_galleries extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $exclude_categories = $output = $tax_terms = $hover_style = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'          => '',
                'display_type'   => 'standard',
                'link_type'      => 'lightbox',
                'fullwidth'      => 'no',
                'gutters'        => 'yes',
                'columns'        => '4',
                'show_title'     => 'yes',
                'show_subtitle'  => 'yes',
                'show_excerpt'   => 'no',
                "excerpt_length" => '20',
                'item_count'     => '-1',
                'category'       => '',
                'gallery_filter' => 'yes',
                'pagination'     => 'no',
                'hover_style'    => 'default',
                'el_position'    => '',
                'width'          => '1/1',
                'el_class'       => ''
            ), $atts ) );


            // Enqueue script
            if ( $display_type == "masonry" || $display_type == "masonry-gallery" ) {
                wp_enqueue_script( 'isotope' );
            }


            /* SIDEBAR CONFIG
            ================================================== */
            global $sf_sidebar_config;

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
            if ( $fullwidth == "yes" && $sidebars == 'no-sidebars' ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }


            /* GALLERIES
            ================================================== */
            $items = sf_galleries( $display_type, $link_type, $fullwidth, $gutters, $columns, $show_title, $show_subtitle, $show_excerpt, $excerpt_length, $item_count, $category, $pagination, $sidebars, $hover_style );


            /* PAGE BUILDER OUTPUT
            ================================================== */
            $width    = spb_translateColumnWidthToSpan( $width );
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            $title_wrap_class = "";

            if ( $gallery_filter == "yes" ) {
                $title_wrap_class .= 'has-filter ';
            }
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                $title_wrap_class .= 'container ';
            }

            $output .= "\n\t" . '<div class="spb_galleries_widget galleries-wrap spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $title != '' || $gallery_filter == "yes" ) {
                $output .= "\n\t\t" . '<div class="title-wrap clearfix ' . $title_wrap_class . '">';
                if ( $title != '' ) {
                    $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
                }
                if ( $gallery_filter == "yes" ) {
                    $output .= sf_gallery_filter( '', $category );
                }
                $output .= '</div>';
            }
            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            global $sf_include_isotope, $sf_has_galleries;
            $sf_include_isotope = true;
            $sf_has_galleries   = true;

            return $output;

        }
    }

    /* PARAMS
    ================================================== */
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
                __( 'Standard', 'swift-framework-plugin' )        => "standard",
                __( 'Gallery', 'swift-framework-plugin' )         => "gallery",
                __( 'Masonry', 'swift-framework-plugin' )         => "masonry",
                __( 'Masonry Gallery', 'swift-framework-plugin' ) => "masonry-gallery"
            ),
            "description" => __( "Select the type of galleries layout you'd like to show.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Item Link", 'swift-framework-plugin' ),
            "param_name"  => "link_type",
            "value"       => array(
                __( 'Lightbox Gallery', 'swift-framework-plugin' ) => "lightbox",
                __( 'Gallery Page', 'swift-framework-plugin' )     => "page",
            ),
            "description" => __( "Select if you'd like the gallery thumbnail link to link through to the gallery page, or to open up the gallery in a lighbox.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Full Width", 'swift-framework-plugin' ),
            "param_name"  => "fullwidth",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
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
            "description" => __( "How many gallery columns to display.", 'swift-framework-plugin' )
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
            "heading"     => __( "Show excerpt", 'swift-framework-plugin' ),
            "param_name"  => "show_excerpt",
            "value"       => array(
                 __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"          
            ),
            "buttonset_on"  => "yes",
            "required"       => array("display_type", "or", "standard,masonry"),
            "description" => __( "Show the excerpt text.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Excerpt Length", 'swift-framework-plugin' ),
            "param_name"  => "excerpt_length",
            "value"       => "20",
            "required"       => array("display_type", "or", "standard,masonry"),
            "description" => __( "The length of the excerpt for the posts.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Number of galleries", 'swift-framework-plugin' ),
            "param_name"  => "item_count",
            "value"       => "12",
            "description" => __( "The number of galleries to show per page. Leave blank to show ALL.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "select-multiple",
            "heading"     => __( "Gallery category", 'swift-framework-plugin' ),
            "param_name"  => "category",
            "value"       => sf_get_category_list( 'gallery-category' ),
            "description" => __( "Choose the category from which you'd like to show galleries.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Filter", 'swift-framework-plugin' ),
            "param_name"  => "gallery_filter",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Show the gallery category filter above the items.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Pagination", 'swift-framework-plugin' ),
            "param_name"  => "pagination",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Show pagination.", 'swift-framework-plugin' )
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
        "type"        => "textfield",
        "heading"     => __( "Extra class", 'swift-framework-plugin' ),
        "param_name"  => "el_class",
        "value"       => "",
        "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
    );

    /* SHORTCODE MAP
    ================================================== */
    SPBMap::map( 'spb_galleries', array(
        "name"   => __( "Galleries", 'swift-framework-plugin' ),
        "base"   => "spb_galleries",
        "class"  => "spb_galleries spb_tab_media",
        "icon"   => "icon-galleries",
        "params" => $params
    ) );
