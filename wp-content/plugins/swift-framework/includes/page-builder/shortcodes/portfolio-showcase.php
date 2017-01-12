<?php

    /*
    *
    *	Swift Page Builder - Portfolio Showcase Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_portfolio_showcase extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            global $sf_options;

            $title = $category = $wrap_class = $title_class = $item_class = $width = $el_class = $output = $filter = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'          => '',
                "item_count"     => '5',
                "category"       => 'all',
                'pagination'     => 'no',
                'el_position'    => '',
                'width'          => '1/1',
                'el_class'       => ''
            ), $atts ) );

            $pagination_view_icon = apply_filters( 'sf_pagination_view_icon' , '<i class="sf-icon-quickview"></i>' );

            // TYPE CHECK
            $alt_display = false;
            if ( spb_theme_supports( 'spb-port-showcase-alt' ) ) {
                $alt_display = true;
            }

            // Enqueue
            if ( $alt_display ) {
                wp_enqueue_script( 'owlcarousel' );
            }

            // CATEGORY SLUG MODIFICATION
            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );

            global $post, $wp_query;

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

            // OUTPUT
            if ( $alt_display ) {
                if ( $pagination == "yes" ) {
                    $wrap_class = "has-pagination ";
                }
                $title_class = "center-title";
                global $sf_carouselID;
                if ( $sf_carouselID == "" ) {
                    $sf_carouselID = 1;
                } else {
                    $sf_carouselID ++;
                }
                $items .= '<div class="port-carousel carousel-wrap">';
                $items .= spb_carousel_arrows(true);
                $items .= '<div id="carousel-' . $sf_carouselID . '" class="portfolio-showcase carousel-items staged-carousel gutters clearfix" data-columns="5" data-auto="false" data-pagination="'.$pagination.'">';
            } else {
                $items .= '<div class="portfolio-showcase-wrap"><ul class="portfolio-showcase-items clearfix" data-columns="' . $item_count . '">';
            }

            while ( $portfolio_items->have_posts() ) : $portfolio_items->the_post();

                if ( $alt_display) {

                    $items .= '<div itemscope class="clearfix carousel-item portfolio-item gallery-item">';
                    $items .= sf_portfolio_thumbnail( "gallery", "", "1/1", '3', "no", 0, "yes", "yes" );
                    $items .= '</div>';

                } else {

                    $thumb_img_url = "";

                    $item_title    = get_the_title();
                    $item_subtitle = spb_get_post_meta( $post->ID, 'sf_portfolio_subtitle', true );

                    $thumb_image              = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=full' );

                    foreach ( $thumb_image as $detail_image ) {
                        $thumb_img_url = $detail_image['url'];
                        break;
                    }

                    if ( ! $thumb_image || $thumb_img_url == "" ) {
                        $thumb_image   = get_post_thumbnail_id();
                        $thumb_img_url = wp_get_attachment_url( $thumb_image, 'full' );
                    }

                    $item_title = get_the_title();
                    $permalink  = get_permalink();
                    $item_link  = sf_portfolio_item_link();

                    $items .= '<li itemscope class="clearfix portfolio-item deselected-item ' . $item_class . '">';

                    // THUMBNAIL MEDIA TYPE SETUP
                    $image_width  = 700;
                    $image_height = 350;
                    if ( $item_count == "5" ) {
                        $image_width  = 500;
                        $image_height = 500;
                    }

                    if ( $thumb_img_url == "" ) {
                        $thumb_img_url = "default";
                    }

                    $image = spb_image_resizer( $thumb_img_url, $image_width, $image_height, true, false );

                    if ( $image ) {
                        $items .= '<a ' . $item_link['config'] . '>';
                        $items .= '<img itemprop="image" class="main-image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $item_title . '" />';
                        $items .= '</a>';
                    }

                    if ( $item_subtitle == "" ) {
                        $items .= '<div class="item-info">';
                        $items .= '<span class="item-title"><a href="' . $permalink . '">' . $item_title . '</a></span>';
                        $items .= '</div>';
                    } else {
                        $items .= '<div class="item-info has-subtitle">';
                        $items .= '<span class="item-title"><a href="' . $permalink . '">' . $item_title . '</a></span>';
                        $items .= '<span><a href="' . $permalink . '">' . $item_subtitle . '</a></span>';
                        $items .= '</div>';
                    }

                    $items .= '</li>';

                }

            endwhile;

            wp_reset_postdata();

            if ( $alt_display == "yes" ) { 
            $items .= '</div></div>';
            } else {
            $items .= '</div>';    
            }

            $width    = spb_translateColumnWidthToSpan( $width );
            $el_class = $this->getExtraClass( $el_class );

            $sidebar_config = spb_get_post_meta( get_the_ID(), 'sf_sidebar_config', true );

            $sidebars = '';
            if ( ( $sidebar_config == "left-sidebar" ) || ( $sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            $view_all = "";
            $portfolio_page   = __( $sf_options['portfolio_page'], 'swift-framework-plugin' );
            if ( $category_slug != "" && strpos($category_slug, ',') != true ) {
                $has_button    = true;
                $category_id   = get_cat_ID( $category_slug );
                $category_link = get_category_link( $category_id );
                $view_all = '<a class="view-all hidden" href="' . esc_url( $category_link ) . '">' . $pagination_view_icon . '</a>';
            } else if ( $portfolio_page != "" ) {
                $view_all = '<a class="view-all hidden" href="' . get_permalink( $portfolio_page ) . '">' . $pagination_view_icon . '</a>';
            }

            $output .= "\n\t" . '<div class="spb_portfolio_showcase_widget spb_content_element ' . $wrap_class . $width . $el_class . '">';
            if ( $pagination == "yes" ) {
                $output .= "\n\t\t" . $view_all;
            }
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, $title_class, true ) : '';
            $output .= "\n\t\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );

            global $sf_has_portfolio_showcase, $sf_include_carousel;
            if ( $alt_display ) {
                $sf_include_carousel = true;
            } else {
                $sf_has_portfolio_showcase = true;
            }

            return $output;

        }
    }


    /* PARAMS
    ================================================== */
    $count_options = array(
            "type"        => "dropdown",
            "heading"     => __( "Number of items", 'swift-framework-plugin' ),
            "param_name"  => "item_count",
            "value"       => array(
                __( '4', 'swift-framework-plugin' ) => "4",
                __( '5', 'swift-framework-plugin' ) => "5"
            ),
            "description" => __( "Choose the number of items to display for the asset.", 'swift-framework-plugin' )
        );
    if ( spb_theme_supports( 'spb-port-showcase-alt' ) ) {
        $count_options = array(
            "type"        => "textfield",
            "heading"     => __( "Number of items", 'swift-framework-plugin' ),
            "param_name"  => "item_count",
            "value"       => "8",
            "description" => __( "Choose the number of items to display for the asset.", 'swift-framework-plugin' )
        );
    }

    $params = array(
        array(
            "type"        => "textfield",
            "heading"     => __( "Widget title", 'swift-framework-plugin' ),
            "param_name"  => "title",
            "value"       => "",
            "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "select-multiple",
            "heading"     => __( "Portfolio category", 'swift-framework-plugin' ),
            "param_name"  => "category",
            "value"       => sf_get_category_list( 'portfolio-category' ),
            "description" => __( "Choose the category for the portfolio items.", 'swift-framework-plugin' )
        ),
        $count_options,
    );
    if ( spb_theme_supports( 'spb-port-showcase-alt' ) ) {
        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Include Pagination", 'swift-framework-plugin' ),
            "param_name"  => "pagination",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' )             => "yes",
                __( "No", 'swift-framework-plugin' )             => "no"
            ),
            "description" => __( "If you would like to include pagination on the showcase asset, then enable it here.", 'swift-framework-plugin' )
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
    SPBMap::map( 'spb_portfolio_showcase', array(
        "name"   => __( "Portfolio Showcase", 'swift-framework-plugin' ),
        "base"   => "spb_portfolio_showcase",
        "class"  => "spb_portfolio_showcase spb_showcase spb_tab_media",
        "icon"   => "icon-showcase",
        "params" => $params
    ) );
