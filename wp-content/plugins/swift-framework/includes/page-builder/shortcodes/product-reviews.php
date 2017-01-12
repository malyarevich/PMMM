<?php

    /*
    *
    *	Swift Page Builder - Product Reviews Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_product_reviews extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $order = $page_link = $text_size = $items = $el_class = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'       => '',
                'text_size'   => '',
                'item_count'  => '-1',
                'display_type' => 'standard',
                'order_by'    => 'rand',
                'order'       => 'ASC',
                'link'        => 'no',
                'show_thumbnail' => 'yes',
                'show_rating' => 'yes',
                'columns'     => '1',
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

            // REVIEWS QUERY SETUP
            global $woocommerce,  $wpdb, $product;

            if ( $display_type == "masonry" ) {
                $items .= '<ul class="product-reviews testimonials woocommerce masonry-items spb-isotope col-' . $columns . ' gutters row clearfix" data-layout-mode="masonry">';
            } else {
                $items .= '<ul class="product-reviews testimonials woocommerce clearfix">';
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

            // PRODUCT REVIEWS LOOP
            $rand_numeric = rand(1, 2500000);
            
            if ($order_by == 'rand') {
                $order_by = 'RAND ('.$rand_numeric.')';
            } else if ($order_by == 'title') {
                $order_by = 'p.post_title' . ' ' . $order;
            } else if ($order_by == 'date') {
                $order_by = 'c.comment_date'  . ' ' . $order;
            }

            $query = "SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0 ORDER BY ".$order_by." LIMIT 0, ". $item_count;
            
            $comments_products = $wpdb->get_results($query, OBJECT);
            if ($comments_products) {
                foreach ($comments_products as $comment_product) {
                    $id_ = $comment_product->comment_post_ID;
                    $name_author =  $comment_product->comment_author;
                    $comment_id  =  $comment_product->comment_ID;
                    $_product = get_product( $id_ );
                    $rating =  intval( get_comment_meta( $comment_id, 'rating', true ) );
                    $rating_html = $_product->get_rating_html( $rating );

                    if ( get_the_title($id_) ) { 
                         $link_title = get_the_title($id_); 
                    }  else { 
                         $link_title = $id_;
                    }   

                    $image_link   = wp_get_attachment_image_src( get_post_thumbnail_id($id_), 'shop_thumbnail');
                    $items .= '<li id="comment-'.$comment_id.'" class="testimonial has-cite ' . $item_class . '">';
                        $items .= '<div class="content-comment testimonial-text">';
                        if ( $link == "yes" ) {
                        $items .= '<a href="'.get_comment_link($comment_id).'" title="'.esc_attr($link_title).'"></a>';
                        }
                        $items .= '<div class="product-details clearfix">';                                  
                        if ( has_post_thumbnail( $id_ ) && $show_thumbnail == "yes" ) { 
                            $items .= get_the_post_thumbnail($id_, 'shop_thumbnail');
                        }
                        $items .= '<h4>' . $link_title . '</h4>';
                        if ( $show_thumbnail == "yes" ) { 
                            $items .= $rating_html;
                        }
                        $items .= '</div>';
                        $items .= get_comment_excerpt( $comment_id );
                        $items .= '</div>';
                        $items .= '<div class="testimonial-cite">';
                        $items .= get_avatar( $comment_product->comment_author_email, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '' );
                        $items .= '<div class="cite-text has-cite-image">';
                        $items .= '<span class="cite-name">'.$name_author.'</span>';
                        $items .= '</div>';
                    $items .= '</li>';                        
                }
            } else {
                $items .= '<li><p>' . __( 'No products reviews found.', 'swift-framework-plugin' ) . '</p></li>'; 
            }

            $items .= '</ul>';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $el_class .= ' testimonial';

            $output .= "\n\t" . '<div class="spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content product-reviews-wrap ' . $text_size . '">';
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
                "heading"     => __( "Order by", 'swift-framework-plugin' ),
                "param_name"  => "order_by",
                "std"         => "date",
                "value"       => array(
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
                    "std"         => "DESC",
                    "value"       => array(
                        __( "Ascending", 'swift-framework-plugin' )  => "ASC",
                        __( "Descending", 'swift-framework-plugin' ) => "DESC"
                    ),
                    "description" => __( "Select if you'd like the items to be ordered in ascending or descending order.", 'swift-framework-plugin' )
            ),
    );

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

    $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Link to the review", 'swift-framework-plugin' ),
            "param_name"  => "link",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
            ),
            "std" => "no",
            "buttonset_on"  => "yes",
            "description" => __( "Select 'Yes' if you'd like the review to be clickable through to the product page.", 'swift-framework-plugin' )
        );

    $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Show product thumbnail", 'swift-framework-plugin' ),
            "param_name"  => "show_thumbnail",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
            ),
            "std" => "yes",
            "buttonset_on"  => "yes",
            "description" => __( "Select 'Yes' if you'd like to show the product thumbnail.", 'swift-framework-plugin' )
        );


    $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Show rating", 'swift-framework-plugin' ),
            "param_name"  => "show_rating",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
            ),
            "std" => "yes",
            "buttonset_on"  => "yes",
            "description" => __( "Select 'Yes' if you'd like to show the rating.", 'swift-framework-plugin' )
        );

    $params[] = array(
            "type"        => "textfield",
            "heading"     => __( "Extra class", 'swift-framework-plugin' ),
            "param_name"  => "el_class",
            "value"       => "",
            "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
        );


    /* SPBMap
    ================================================== */
    SPBMap::map( 'spb_product_reviews', array(
        "name"          => __( "Product Reviews", 'swift-framework-plugin' ),
        "base"          => "spb_product_reviews",
        "class"         => "",
        "icon"          => "icon-testimonials",
        "wrapper_class" => "clearfix",
        "controls"      => "full",
        "params"        => $params
    ) );
