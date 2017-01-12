<?php

    /*
    *
    *	Swift Page Builder - Recent Posts Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_recent_posts extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            global $sf_options;

            $title = $width = $wrap_class = $title_class = $excerpt_length = $item_class = $offset = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'          => '',
                'item_columns'   => '3',
                'display_type'   => 'standard',
                'carousel'       => 'no',
                "item_count"     => '4',
                "category"       => '',
                "offset"         => 0,
                "posts_order"    => 'ASC',
                "excerpt_length" => '20',
                'fullwidth'      => 'no',
                'gutters'        => 'yes',
                'button_enabled' => 'no',
                'pagination'     => 'no',
                'post_type'      => 'post',
                'el_position'    => '',
                'width'          => '1/1',
                'el_class'       => ''
            ), $atts ) );

            // Enqueue
            if ( $carousel == "yes" ) {
                wp_enqueue_script( 'owlcarousel' );
            }

            $view_all_icon = apply_filters( 'sf_view_all_icon' , '<i class="ss-layergroup"></i>' );
            $pagination_view_icon = apply_filters( 'sf_pagination_view_icon' , '<i class="sf-icon-quickview"></i>' );

            /* CATEGORY SLUG MODIFICATION
            ================================================== */
            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );


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


            /* BLOG QUERY SETUP
            ================================================== */
            global $post, $wp_query;

            if ( get_query_var( 'paged' ) ) {
                $paged = get_query_var( 'paged' );
            } elseif ( get_query_var( 'page' ) ) {
                $paged = get_query_var( 'page' );
            } else {
                $paged = 1;
            }

            $recent_post_args = array(
                'post_type'      => $post_type,
                'post_status'    => 'publish',
                'category_name'  => $category_slug,
                'posts_per_page' => $item_count,
                'offset'         => $offset,
                'order'          => $posts_order
            );

            $recent_posts = new WP_Query( $recent_post_args );

            $count        = 0;
            $image_width  = 270;
            $image_height = 270;

            if ( $item_columns == "1" ) {
                $image_width  = 720;
                $image_height = 720;
                $item_class = 'col-sm-12';
            } else if ( $item_columns == "2" ) {
                $image_width  = 540;
                $image_height = 540;
                $item_class   = 'col-sm-6';
            } else if ( $item_columns == "3" ) {
                $image_width  = 360;
                $image_height = 360;
                $item_class   = 'col-sm-4';
            } else if ( $item_columns == "5" ) {
                $image_width  = 360;
                $image_height = 360;
                $item_class   = 'col-sm-sf-5';
            } else {
                $item_class = 'col-sm-3';
            }

            if ($display_type == "standard-row") {
            	$item_class = 'col-sm-12';
            }

            $list_class = 'posts-type-' . $display_type;

            if ( $display_type == "standard" || ( $display_type == "standard-row" && $carousel == "no" ) ) {
                $list_class .= ' row';
            }

            if ( $gutters == "no" ) {
                $list_class .= ' no-gutters';
            }

            if ( $display_type == "showcase" ) {
                $carousel = "yes";
                $list_class .= ' staged-carousel';
                if ( $pagination == "yes" ) {
                    $wrap_class = "has-pagination ";
                }
                $title_class = "center-title";
                if ( $sidebars == "no-sidebars" ) {
                    $fullwidth = "yes";
                }
            }

            if ( $display_type == "list" ) {
                $carousel = "no";
            }

            if ( $carousel == "yes" ) {
                global $sf_carouselID;
                if ( $sf_carouselID == "" ) {
                    $sf_carouselID = 1;
                } else {
                    $sf_carouselID ++;
                }
                $item_class = 'carousel-item';
            }

            if ( $display_type == "bold" ) {
                $item_class .= ' vr-standard';
            }

            if ( $carousel == "yes" ) {
                $items .= '<div class="posts-carousel carousel-wrap">';
                if ( $display_type == "showcase" ) {
                    $items .= spb_carousel_arrows(true);
                }
                $items .= '<div id="carousel-' . $sf_carouselID . '" class="recent-posts carousel-items ' . $list_class . ' clearfix" data-columns="' . $item_columns . '" data-auto="false" data-pagination="'.$pagination.'">';
            } else {
                $items .= '<div class="recent-posts ' . $list_class . ' clearfix">';
            }
            while ( $recent_posts->have_posts() ) : $recent_posts->the_post();

                $count++;
                if ( $count > $item_count ) {
                    break;
                }

                $items .= sf_get_recent_post_item( $post, $display_type, $excerpt_length, $item_class );

            endwhile;

            wp_reset_postdata();

            if ( $carousel == "yes" ) {
                $items .= '</div></div>';
            } else {
                $items .= '</div>';
            }

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }

            $el_class = $this->getExtraClass( $el_class, $fullwidth );
            $width    = spb_translateColumnWidthToSpan( $width );

            $has_button  = false;
            $page_button = $title_wrap_class = $view_all = "";
            $blog_page   = __( $sf_options['blog_page'], 'swift-framework-plugin' );
            if ( $category_slug != "" && strpos($category_slug, ',') != true ) {
                $has_button    = true;
                $category_id   = get_category_by_slug( $category_slug );
                $category_link = get_category_link( $category_id );
                $page_button   = '<a class="sf-button medium white sf-icon-stroke " href="' . esc_url( $category_link ) . '">' . $view_all_icon . '<span class="text">' . __( "VIEW ALL ARTICLES", 'swift-framework-plugin' ) . '</span></a>';
                $view_all = '<a class="view-all hidden" href="' . esc_url( $category_link ) . '">' . $pagination_view_icon . '</a>';
            } else if ( $blog_page != "" ) {
                $has_button  = true;
                $page_button = '<a class="sf-button medium white sf-icon-stroke " href="' . get_permalink( $blog_page ) . '">' . $view_all_icon . '<span class="text">' . __( "VIEW ALL ARTICLES", 'swift-framework-plugin' ) . '</span></a>';
                $view_all = '<a class="view-all hidden" href="' . get_permalink( $blog_page ) . '">' . $pagination_view_icon . '</a>';
            }
            if ( $has_button && $button_enabled == "yes" ) {
                $title_wrap_class .= 'has-button ';
            }
            if ( $fullwidth ) {
                $title_wrap_class .= 'container ';
            }

            $output .= "\n\t" . '<div class="spb_recent_posts_widget spb_content_element ' . $wrap_class . $width . $el_class . '">';
            if ( $display_type == "showcase" && $pagination == "yes" ) {
                $output .= "\n\t\t" . $view_all;
            }
            $output .= "\n\t\t" . '<div class="spb-asset-content">';

            if ( spb_get_theme_name() == "joyn" ) {

                if ( $title != '' ) {
                    $output .= "\n\t\t" . '<div class="title-wrap clearfix ' . $title_wrap_class . '">';
                    $output .= '<h3 class="spb-heading '.$title_class.'"><span>' . $title . '</span></h3>';
                    $output .= '</div>';
                }

            } else {

                $output .= "\n\t\t" . '<div class="title-wrap clearfix ' . $title_wrap_class . '">';
                if ( $title != '' ) {
                    $output .= '<h3 class="spb-heading '.$title_class.'"><span>' . $title . '</span></h3>';
                }
                if ( $carousel == "yes" && $display_type != "showcase" ) {
                    $output .= spb_carousel_arrows();
                }
                if ( $has_button && $button_enabled == "yes" ) {
                    $output .= $page_button;
                }
                $output .= '</div>';

            }

            $output .= "\n\t\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            global $sf_include_isotope, $sf_include_carousel;
            $sf_include_isotope = true;

            if ( $carousel == "yes" ) {
                $sf_include_carousel = true;
            }

            return $output;

        }
    }

    $display_types = array(
        __( 'Standard', 'swift-framework-plugin' )   => "standard",
        __( 'Standard Row', 'swift-framework-plugin' )   => "standard-row",
        __( 'Bold', 'swift-framework-plugin' )       => "bold",
        __( 'Bold (Alt)', 'swift-framework-plugin' ) => "bright",
        __( 'Post List', 'swift-framework-plugin' )  => "list",
    );

    if ( spb_get_theme_name() == "atelier" ) {
    	$display_types = array(
    	    __( 'Standard', 'swift-framework-plugin' )   => "standard",
    	    __( 'Standard Row', 'swift-framework-plugin' )   => "standard-row",
    	    __( 'Post List', 'swift-framework-plugin' )  => "list",
    	);
    }

    if ( spb_theme_supports('posts-showcase') ) {
        $display_types = array(
            __( 'Standard', 'swift-framework-plugin' )   => "standard",
            __( 'Standard Row', 'swift-framework-plugin' )   => "standard-row",
            __( 'Post List', 'swift-framework-plugin' )  => "list",
            __( 'Post Showcase', 'swift-framework-plugin' )  => "showcase",
        );
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
                "heading"     => __( "Display Type", 'swift-framework-plugin' ),
                "param_name"  => "display_type",
                "value"       => $display_types,
                "description" => __( "Choose the display type for the posts.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Carousel", 'swift-framework-plugin' ),
                "param_name"  => "carousel",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "buttonset_on"  => "yes",
                "std"         => "no",
                "required"    => array("display_type", "or", "standard,bold,standard-row"),
                "description" => __( "Enables carousel funcitonality in the asset.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Columns", 'swift-framework-plugin' ),
                "param_name"  => "item_columns",
                "std"         => '5',
                "value"       => array(
                    __( '5', 'swift-framework-plugin' ) => "5",
                    __( '4', 'swift-framework-plugin' ) => "4",
                    __( '3', 'swift-framework-plugin' ) => "3",
                    __( '2', 'swift-framework-plugin' ) => "2",
                    __( '1', 'swift-framework-plugin' ) => "1",
                ),
                "required"       => array("display_type", "or", "standard,bold,standard-row"),
                "description" => __( "Choose the amount of columns you would like for the asset.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "class"       => "",
                "heading"     => __( "Number of items", 'swift-framework-plugin' ),
                "param_name"  => "item_count",
                "value"       => "4",
                "description" => __( "The number of blog items to show per page.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "select-multiple",
                "heading"     => __( "Blog category", 'swift-framework-plugin' ),
                "param_name"  => "category",
                "value"       => sf_get_category_list( 'category' ),
                "description" => __( "Choose the category for the blog items.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Posts offset", 'swift-framework-plugin' ),
                "param_name"  => "offset",
                "value"       => "0",
                "description" => __( "The offset for the start of the posts that are displayed, e.g. enter 5 here to start from the 5th post.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Posts order", 'swift-framework-plugin' ),
                "param_name"  => "posts_order",
                "value"       => array(
                    __( "Descending", 'swift-framework-plugin' ) => "DESC",
                    __( "Ascending", 'swift-framework-plugin' )  => "ASC"
                ),
                "description" => __( "The order of the posts.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Excerpt Length", 'swift-framework-plugin' ),
                "param_name"  => "excerpt_length",
                "value"       => "20",
                "description" => __( "The length of the excerpt for the posts.", 'swift-framework-plugin' )
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
                "type"        => "buttonset",
                "heading"     => __( "Blog Page Button", 'swift-framework-plugin' ),
                "param_name"  => "button_enabled",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Select if you'd like to include a button in the title bar to link through to all blog posts. The page is set in Theme Options > Custom Post Type Options, unless you have selected a category above, in which case it will link to that category.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Post Type Override", 'swift-framework-plugin' ),
                "param_name"  => "post_type",
                "value"       => spb_get_post_types(),
                "description" => __( "If you'd like to override the post type for which the content is of this asset is made up of, then you can select it here.", 'swift-framework-plugin' )
            ),
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Include Pagination", 'swift-framework-plugin' ),
            "param_name"  => "pagination",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' )             => "yes",
                __( "No", 'swift-framework-plugin' )             => "no"
            ),
            "required"       => array("display_type", "=", "showcase"),
            "description" => __( "If you would like to include pagination on the showcase asset, then enable it here.", 'swift-framework-plugin' )
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
    SPBMap::map( 'spb_recent_posts', array(
        "name"   => __( "Recent Posts", 'swift-framework-plugin' ),
        "base"   => "spb_recent_posts",
        "class"  => "spb_recent_posts spb_tab_media",
        "icon"   => "icon-recent-posts",
        "params" => $params
    ) );
