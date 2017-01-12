<?php

    /*
    *
    *	Swift Page Builder - Cleints Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_clients extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $carousel = $item_class = $output = $tax_terms = $filter = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'            => '',
                'item_count'       => '-1',
                'item_columns'     => '4',
                'category'         => '',
                'carousel'         => 'no',
                'carousel_autoplay' => '',
                'link_target'      => '_blank',
                'pagination'       => 'no',
                'el_position'      => '',
                'width'            => '1/1',
                'el_class'         => ''
            ), $atts ) );

            // Enqueue
            if ( $carousel == "yes" ) {
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

            // CLIENTS QUERY SETUP

            global $post, $wp_query, $sf_carouselID;

            if ( $sf_carouselID == "" ) {
                $sf_carouselID = 1;
            } else {
                $sf_carouselID ++;
            }


            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

            $client_args = array(
                'post_type'        => 'clients',
                'post_status'      => 'publish',
                'paged'            => $paged,
                'clients-category' => $category_slug,
                'posts_per_page'   => $item_count
            );

            $clients_items = new WP_Query( $client_args );

            global $column_width;

            $img_scale  = apply_filters( 'sf_client_img_scale', 1 );
            $client_width  = 300;

            if ( $item_columns == "5" ) {
                $item_class = 'col-sm-sf-5';
                $client_width  = 210;
            } else if ( $item_columns == "6" ) {
                $item_class = 'col-sm-2';
                $client_width  = 170;
            } else if ( $item_columns == "4" ) {
                $item_class = 'col-sm-3';
                $client_width  = 260;
            } else if ( $item_columns == "3" ) {
                $item_class    = 'col-sm-4';
                $client_width  = 400;
            }
            if ( $item_columns == "2" ) {
                $item_class    = 'col-sm-6';
                $client_width  = 600;
            }

            $client_height = $client_width * $img_scale;

            if ( $carousel_autoplay != "" && $carousel_autoplay !== "0" ) {
                $carousel_autoplay = 'data-autoplay="'.$carousel_autoplay.'"';
            }

            if ( $carousel == "yes" ) {
                $items .= '<div id="carousel-' . $sf_carouselID . '" class="clients-items carousel-items clearfix" data-columns="' . $item_columns . '" '.$carousel_autoplay.'>';
            } else {
                $items .= '<div class="clients-items clearfix">';
            }

            // CLIENTS LOOP

            while ( $clients_items->have_posts() ) : $clients_items->the_post();

                $client_image    = get_post_thumbnail_id();
                $client_img_url  = wp_get_attachment_url( $client_image, 'full' );
                $client_link_url = spb_get_post_meta( $post->ID, 'sf_client_link', true );
                $image_alt       = esc_attr( spb_get_post_meta( $client_image, '_wp_attachment_image_alt', true ) );

                if ( $carousel == "yes" ) {
                    $items .= '<div class="clearfix carousel-item">';
                } else {
                    $items .= '<div class="' . $item_class . ' client-item clearfix">';
                }
                $items .= '<figure>';

                $image = spb_image_resizer( $client_img_url, $client_width, $client_height, false, false );

                if ( $image ) {

                    if ( $client_link_url ) {
                        $items .= '<a href="' . $client_link_url . '" target="' . $link_target . '"><img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $image_alt . '" /></a>';
                    } else {
                        $items .= '<img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $image_alt . '" />';
                    }

                }

                $items .= '</figure>';

                $items .= '</div>';

            endwhile;

            wp_reset_postdata();

            $items .= '</div>';

            // PAGINATION
            if ( $pagination == "yes" ) {

                $items .= '<div class="pagination-wrap">';

                $items .= pagenavi( $clients_items );

                $items .= '</div>';

            }

            // PAGE BUILDER OUPUT

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_clients_widget clients-wrap carousel-asset spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t" . '<div class="title-wrap clearfix">';
            if ( $title != '' ) {
                $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
            }
            if ( $carousel == "yes" ) {
                $output .= spb_carousel_arrows();
            }
            $output .= '</div>';
            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            global $sf_include_carousel;
            $sf_include_carousel = true;

            return $output;

        }
    }

    SPBMap::map( 'spb_clients', array(
        "name"   => __( "Clients", 'swift-framework-plugin' ),
        "base"   => "spb_clients",
        "class"  => "clients",
        "icon"   => "icon-clients",
        "params" => array(
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
                "description" => __( "The number of clients to show per page. Leave blank to show ALL clients.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Columns", 'swift-framework-plugin' ),
                "param_name"  => "item_columns",
                "value"       => array(
                    __( '2', 'swift-framework-plugin' ) => "2",
                    __( '3', 'swift-framework-plugin' ) => "3",
                    __( '4', 'swift-framework-plugin' ) => "4",
                    __( '5', 'swift-framework-plugin' ) => "5",
                    __( '6', 'swift-framework-plugin' ) => "6"
                ),
                "std"         => '4',
                "description" => __( "Choose the amount of columns you would like for the clients asset.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "select-multiple",
                "heading"     => __( "Clients category", 'swift-framework-plugin' ),
                "param_name"  => "category",
                "value"       => sf_get_category_list( 'clients-category' ),
                "description" => __( "Choose the category for the client items.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Enable Carousel", 'swift-framework-plugin' ),
                "param_name"  => "carousel",
                "value"       => array(
                    __( "Yes", 'swift-framework-plugin' ) => "yes",
                    __( "No", 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Enable carousel functionality.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Carousel Autoplay", 'swift-framework-plugin' ),
                "param_name"  => "carousel_autoplay",
                "value"       => "",
                "required"       => array("carousel", "=", "yes"),
                "description" => __( "Enable carousel autoplay. Enter time in miliseconds for the carousel to auto-rotate. Leave blank to disable.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Link target", 'swift-framework-plugin' ),
                "param_name"  => "link_target",
                "value"       => array(
                    __( "Self", 'swift-framework-plugin' )       => "_self",
                    __( "New Window", 'swift-framework-plugin' ) => "_blank"
                ),
                "description" => __( "Select if you want the client link to open in a new window", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Pagination", 'swift-framework-plugin' ),
                "param_name"  => "pagination",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "description" => __( "Show clients pagination (non-carousel only).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        )
    ) );
