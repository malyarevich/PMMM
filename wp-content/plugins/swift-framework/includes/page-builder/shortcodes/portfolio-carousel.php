<?php

    /*
    *
    *	Swift Page Builder - Portfolio Carousel Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_portfolio_carousel extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $category = $item_class = $width = $hover_style = $gutters = $fullwidth = $list_class = $el_class = $output = $filter = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'        => '',
                "item_count"   => '12',
                'item_columns' => '4',
                'fullwidth'    => 'no',
                'gutters'      => 'yes',
                "category"     => 'all',
                'hover_style'  => 'default',
                'el_position'  => '',
                'width'        => '1/1',
                'el_class'     => ''
            ), $atts ) );

            // Enqueue
            wp_enqueue_script( 'owlcarousel' );
            
            
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
            if ( is_singular( 'portfolio' ) ) {
                $sidebars = "no-sidebars";
            }

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }
            

            // CATEGORY SLUG MODIFICATION
            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );

            global $post, $wp_query, $sf_carouselID;

            if ( $sf_carouselID == "" ) {
                $sf_carouselID = 1;
            } else {
                $sf_carouselID ++;
            }

            $categories = explode(",", $category_slug);
            $translated_categories = '';
            foreach ($categories as $key => $category_slug) {
                $category_id_by_slug = get_term_by('slug', $category_slug, 'portfolio-category');
                if ( isset( $category_id_by_slug->term_id ) ) {
                    $translated_slug_id = apply_filters('wpml_object_id', $category_id_by_slug->term_id, 'custom taxonomy', true);
                    $translated_slug = get_term_by('id', $translated_slug_id, 'portfolio-category');
                    $translated_categories = $translated_categories.($key < count($categories)-1 ? $translated_slug->slug.',': $translated_slug->slug );
                }
            }

            $portfolio_args = array(
                'post_type'          => 'portfolio',
                'post_status'        => 'publish',
                'portfolio-category' => $translated_categories,
                'posts_per_page'     => $item_count,
                'no_found_rows'      => 1
            );

            $portfolio_items = new WP_Query( $portfolio_args );

            $count = 0;

            $figure_width  = 300;
            $figure_height = 225;

            if ( $item_columns == "3" ) {
                $figure_width  = 400;
                $figure_height = 300;
            }
            if ( $item_columns == "2" ) {
                $figure_width  = 600;
                $figure_height = 450;
            }

            // Thumb Type
            if ( $hover_style == "default" && function_exists( 'sf_get_thumb_type' ) ) {
                $list_class = sf_get_thumb_type();
            } else {
                $list_class = 'thumbnail-' . $hover_style;
            }

            if ( $gutters == "no" ) {
                $list_class .= ' no-gutters'; 
            } else {
                $list_class .= ' gutters'; 
            }

            $portfolio_carousel_item_class = apply_filters( 'spb_portfolio_carousel_item_class', '' );

            $items .= '<div id="carousel-' . $sf_carouselID . '" class="portfolio-items carousel-items clearfix ' . $list_class . '" data-columns="' . $item_columns . '" data-auto="false">';

            while ( $portfolio_items->have_posts() ) : $portfolio_items->the_post();

                $items .= '<div itemscope data-id="id-' . $count . '" class="clearfix carousel-item portfolio-item ' . $portfolio_carousel_item_class . '">';
                $items .= sf_portfolio_thumbnail( "gallery", "", "1/1", $item_columns, "no", 0, $gutters, $fullwidth );
                $items .= '</div>';
                $count ++;

            endwhile;

            wp_reset_postdata();

            $items .= '</div>';

            $width    = spb_translateColumnWidthToSpan( $width );
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            $output .= "\n\t" . '<div class="spb_portfolio_carousel_widget carousel-asset spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $fullwidth == "yes" ) {
                $output .= "\n\t\t" . '<div class="title-wrap container clearfix">';
            } else {
                $output .= "\n\t\t" . '<div class="title-wrap clearfix">';
            }
            if ( $title != '' ) {
                $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
            }
            $output .= spb_carousel_arrows();
            $output .= '</div>';
            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );

            global $sf_include_carousel, $sf_include_isotope;
            $sf_include_carousel = true;
            $sf_include_isotope  = true;

            return $output;

        }
    }


    /* SHORTCODE PARAMS
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
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Number of items", 'swift-framework-plugin' ),
            "param_name"  => "item_count",
            "value"       => "12",
            "description" => __( "The number of portfolio items to show in the carousel.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Columns", 'swift-framework-plugin' ),
            "param_name"  => "item_columns",
            "value"       => array(
                __( '2', 'swift-framework-plugin' ) => "2",
                __( '3', 'swift-framework-plugin' ) => "3",
                __( '4', 'swift-framework-plugin' ) => "4",
                __( '5', 'swift-framework-plugin' ) => "5"
            ),
            "std"         => "4",
            "description" => __( "Choose the amount of columns you would like for the asset.", 'swift-framework-plugin' )
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
            "std"         => 'no',
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
            "std"         => 'yes',
            "description" => __( "Select if you'd like spacing between the items, or not.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "select-multiple",
            "heading"     => __( "Portfolio category", 'swift-framework-plugin' ),
            "param_name"  => "category",
            "value"       => sf_get_category_list( 'portfolio-category' ),
            "description" => __( "Choose the category for the portfolio items.", 'swift-framework-plugin' )
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
    SPBMap::map( 'spb_portfolio_carousel', array(
        "name"   => __( "Portfolio Carousel", 'swift-framework-plugin' ),
        "base"   => "spb_portfolio_carousel",
        "class"  => "spb_portfolio_carousel spb_carousel spb_tab_media",
        "icon"   => "icon-portfolio-carousel",
        "params" => $params
    ) );

?>
