<?php

    /*
    *
    *	Swift Page Builder - Testimonial Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_testimonial extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $order = $page_link = $text_size = $items = $el_class = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'       => '',
                'text_size'   => '',
                'item_count'  => '-1',
                'order'       => '',
                'category'    => '',
                'pagination'  => 'no',
                'display_type'=> 'standard',
                'columns'     => '1',
                'page_link'   => '',
                'el_class'    => '',
                'el_position' => '',
                'width'       => '1/2'
            ), $atts ) );

            $output = '';
            
            $next_icon = apply_filters( 'sf_next_icon', '<i class="ss-navigateright"></i>' );

            // Enqueue script
            if ( $display_type == "masonry" ) {
                wp_enqueue_script( 'isotope' );
            }
            
            // CATEGORY SLUG MODIFICATION
            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );


            // TESTIMONIAL QUERY SETUP
            global $post, $wp_query;

            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

            if ( $pagination == "yes" ) {
                $order = "date";
            }

            $testimonials_args = array(
                'orderby'               => $order,
                'post_type'             => 'testimonials',
                'post_status'           => 'publish',
                'paged'                 => $paged,
                'testimonials-category' => $category_slug,
                'posts_per_page'        => $item_count
            );

            $testimonials = new WP_Query( $testimonials_args );

            if ( $display_type == "masonry" ) {
                $items .= '<ul class="testimonials masonry-items spb-isotope col-' . $columns . ' gutters row clearfix" data-layout-mode="masonry">';
            } else {
                $items .= '<ul class="testimonials clearfix">';
            }

            /* COLUMN VARIABLE CONFIG
            ================================================== */
            $item_class = "";
            if ( $display_type == "masonry" ) {
                if ( $columns == "1" ) {
                    $item_class = "col-sm-12 ";
                } else if ( $columns == "2" ) {
                    $item_class = "col-sm-6 ";
                } else if ( $columns == "3" ) {
                    $item_class = "col-sm-4 ";
                } else if ( $columns == "4" ) {
                    $item_class = "col-sm-3 ";
                } else if ( $columns == "5" ) {
                    $item_class = "col-sm-sf-5 ";
                }
            }

            // TESTIMONIAL LOOP

            while ( $testimonials->have_posts() ) : $testimonials->the_post();

                $testimonial_text         = apply_filters( 'the_content', get_the_content() );
                $testimonial_cite         = spb_get_post_meta( $post->ID, 'sf_testimonial_cite', true );
                $testimonial_cite_subtext = spb_get_post_meta( $post->ID, 'sf_testimonial_cite_subtext', true );
                $testimonial_image        = rwmb_meta( 'sf_testimonial_cite_image', 'type=image', $post->ID );

                foreach ( $testimonial_image as $detail_image ) {
                    $testimonial_image_url = $detail_image['url'];
                    break;
                }

                if ( ! $testimonial_image ) {
                    $testimonial_image     = get_post_thumbnail_id();
                    $testimonial_image_url = wp_get_attachment_url( $testimonial_image, 'full' );
                }
                $testimonial_size = apply_filters( 'spb_testimonial_image_size', 70 );
                $testimonial_image = spb_image_resizer( $testimonial_image_url, $testimonial_size, $testimonial_size, true, false );

                if ( $testimonial_cite != "" ) {
                    $items .= '<li class="testimonial has-cite '.$item_class.'">';
                } else {
                    $items .= '<li class="testimonial '.$item_class.'">';
                }
                $items .= '<div class="testimonial-text">' . do_shortcode( $testimonial_text ) . '</div>';
                $items .= '<div class="testimonial-cite">';
                if ( $testimonial_image ) {
                    $items .= '<img src="' . $testimonial_image[0] . '" width="' . $testimonial_image[1] . '" height="' . $testimonial_image[2] . '" alt="' . $testimonial_cite . '" />';
                    $items .= '<div class="cite-text has-cite-image"><span class="cite-name">' . $testimonial_cite . '</span><span class="cite-subtext">' . $testimonial_cite_subtext . '</span></div>';
                } else {
                    $items .= '<div class="cite-text"><span class="cite-name">' . $testimonial_cite . '</span><span class="cite-subtext">' . $testimonial_cite_subtext . '</span></div>';
                }
                $items .= '</div>';
                $items .= '</li>';

            endwhile;

            wp_reset_postdata();

            $items .= '</ul>';

            if ( $page_link == "yes" ) {
                global $sf_options;
                $testimonials_page = __( $sf_options['testimonial_page'], 'swift-framework-plugin' );

                if ( $testimonials_page ) {
                    $items .= '<a href="' . get_permalink( $testimonials_page ) . '" class="read-more">' . __( "More", 'swift-framework-plugin' ) . $next_icon .'</a>';
                }
            }

            // PAGINATION
            if ( $pagination == "yes" ) {

                $items .= '<div class="pagination-wrap">';
                $items .= pagenavi( $testimonials );
                $items .= '</div>';

            }

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $el_class .= ' testimonial';

            $output .= "\n\t" . '<div class="spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content testimonial-wrap ' . $text_size . '">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '' ) : '';
            $output .= "\n\t\t\t" . $items;
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

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
                "heading"     => __( "Text size", 'swift-framework-plugin' ),
                "param_name"  => "text_size",
                "value"       => array(
                    __( 'Normal', 'swift-framework-plugin' ) => "normal",
                    __( 'Large', 'swift-framework-plugin' )  => "large"
                ),
                "description" => __( "Choose the size of the text.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "class"       => "",
                "heading"     => __( "Number of items", 'swift-framework-plugin' ),
                "param_name"  => "item_count",
                "value"       => "6",
                "description" => __( "The number of testimonials to show per page. Leave blank to show ALL testimonials.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Testimonials Order", 'swift-framework-plugin' ),
                "param_name"  => "order",
                "value"       => array(
                    __( 'Random', 'swift-framework-plugin' ) => "rand",
                    __( 'Latest', 'swift-framework-plugin' ) => "date"
                ),
                "description" => __( "Choose the order of the testimonials.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "select-multiple",
                "heading"     => __( "Testimonials category", 'swift-framework-plugin' ),
                "param_name"  => "category",
                "value"       => sf_get_category_list( 'testimonials-category' ),
                "description" => __( "Choose the category for the testimonials.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Pagination", 'swift-framework-plugin' ),
                "param_name"  => "pagination",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "description" => __( "Show testimonial pagination (1/1 width element only).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Testimonials page link", 'swift-framework-plugin' ),
                "param_name"  => "page_link",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "description" => __( "Include a link to the testimonials page (which you must choose in the theme options).", 'swift-framework-plugin' )
            ),
    );

    if ( spb_get_theme_name() == "uplift" ) {
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Display Type", 'swift-framework-plugin' ),
            "param_name"  => "display_type",
            "value"       => array(
                __( 'Standard', 'swift-framework-plugin' ) => "standard",
                __( 'Masonry', 'swift-framework-plugin' )  => "masonry"
            ),
            "std"         => 'standard',
            "description" => __( "Choose the display type for the asset.", 'swift-framework-plugin' )
        );
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Column count", 'swift-framework-plugin' ),
            "param_name"  => "columns",
            "value"       => array( "5", "4", "3", "2", "1" ),
            "required"       => array("display_type", "=", "masonry"),
            "std"         => '3',
            "description" => __( "How many columns to display.", 'swift-framework-plugin' )
        );
    }

    $params[] = array(
            "type"        => "textfield",
            "heading"     => __( "Extra class", 'swift-framework-plugin' ),
            "param_name"  => "el_class",
            "value"       => "",
            "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
        );


    /* SPBMap
    ================================================== */
    SPBMap::map( 'spb_testimonial', array(
        "name"          => __( "Testimonials", 'swift-framework-plugin' ),
        "base"          => "spb_testimonial",
        "class"         => "",
        "icon"          => "icon-testimonials",
        "wrapper_class" => "clearfix",
        "controls"      => "full",
        "params"        => $params
    ) );
