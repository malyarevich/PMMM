<?php

    /*
    *
    *   Swift Page Builder - Blog Shortcode
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_blog extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $output = $gutters = $fullwidth = $columns = $hover_style = $show_read_more = $offset = $order = $content_output = $items = $item_figure = $el_position = '';

            extract( shortcode_atts( array(
                'title'              => '',
                'blog_type'          => 'standard',
                'gutters'            => 'yes',
                'columns'            => '4',
                'fullwidth'          => 'no',
                'include_sticky'     => 'no',
                'show_title'         => 'yes',
                'show_excerpt'       => 'yes',
                'show_details'       => 'yes',
                'offset'             => '0',
                'order_by'           => 'date',
                'order'              => 'DESC',
                'excerpt_length'     => '20',
                'show_read_more'     => 'yes',
                'item_count'         => '5',
                'category'           => '',
                'exclude_categories' => '',
                'pagination'         => 'no',
                'social_integration' => 'no',
                'twitter_username'   => '',
                'instagram_id'       => '',
                'instagram_token'    => '',
                'blog_filter'        => '',
                'basic_blog_filter'  => '',
                'alt_styling'        => 'no',
                'hover_style'        => 'default',
                'content_output'     => 'excerpt',
                'post_type'          => 'post',
                'el_position'        => '',
                'width'              => '1/1',
                'el_class'           => ''
            ), $atts ) );


            $width = spb_translateColumnWidthToSpan( $width );

            // Enqueue script
            wp_enqueue_script( 'isotope' );


            /* SIDEBAR CONFIG
            ================================================== */
            $sidebar_config = spb_get_post_meta( get_the_ID(), 'sf_sidebar_config', true );

            $sidebars = '';
            if ( ( $sidebar_config == "left-sidebar" ) || ( $sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sidebar_config == "both-sidebars" ) {
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


            /* BLOG ITEMS
            ================================================== */
            $items = sf_blog_items( $atts );


            /* FINAL OUTPUT
            ================================================== */
            $title_wrap_class = "";
            if ( $blog_filter == "yes" ) {
                $title_wrap_class .= 'has-filter ';
            }
            if ( $fullwidth ) {
                $title_wrap_class .= 'container ';
            }
            if ( $fullwidth == "yes" ) {
                $el_class .= ' spb-full-width-element';
            }
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            $output .= "\n\t" . '<div class="spb_blog_widget blog-wrap spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $title != '' || $blog_filter == "yes" ) {
                $output .= "\n\t\t" . '<div class="title-wrap clearfix ' . $title_wrap_class . '">';
                if ( $title != '' ) {
                    $output .= '<h2 class="spb-heading"><span>' . $title . '</span></h2>';
                }
                if ( $blog_filter == "yes" ) {
                    $filter_style = "";
                    if ( $basic_blog_filter == "yes" ) {
                       $filter_style = "basic"; 
                    }
                    $output .= sf_post_filter( $filter_style, $post_type, $category );
                }
                $output .= '</div>';
            }
            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            global $sf_has_blog, $sf_include_imagesLoaded;
            $sf_include_imagesLoaded = true;
            $sf_has_blog             = true;

            return $output;

        }
    }

    $blog_types = array(
        __( 'Standard', 'swift-framework-plugin' ) => "standard",
        __( 'Timeline', 'swift-framework-plugin' ) => "timeline",
        __( 'Bold', 'swift-framework-plugin' )     => "bold",
        __( 'Mini', 'swift-framework-plugin' )     => "mini",
        __( 'Masonry', 'swift-framework-plugin' )  => "masonry",
    );

    if ( spb_get_theme_name() == "atelier" ) {
        $blog_types = array(
            __( 'Standard', 'swift-framework-plugin' ) => "standard",
            __( 'Bold', 'swift-framework-plugin' )     => "bold",
            __( 'Mini', 'swift-framework-plugin' )     => "mini",
            __( 'Masonry', 'swift-framework-plugin' )  => "masonry",
        );
    }

    if ( spb_get_theme_name() == "uplift" ) {
        $blog_types = array(
            __( 'Standard', 'swift-framework-plugin' ) => "standard",
            __( 'Timeline', 'swift-framework-plugin' ) => "timeline",
            __( 'Masonry', 'swift-framework-plugin' )  => "masonry",
        );
    }

    $blog_types = apply_filters( 'spb_blog_display_types', $blog_types );
    

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
            "heading"     => __( "Blog type", 'swift-framework-plugin' ),
            "param_name"  => "blog_type",
            "value"       => $blog_types,
            "std"         => "masonry",
            "description" => __( "Select the display type for the blog.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Masonry gutters", 'swift-framework-plugin' ),
            "param_name"  => "gutters",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("blog_type", "=", "masonry"),
            "description" => __( "Select if you'd like spacing between the items, or not (Masonry type only).", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Masonry Columns", 'swift-framework-plugin' ),
            "param_name"  => "columns",
            "value"       => array( "5", "4", "3", "2", "1" ),
            "std"         => "4",
            "required"       => array("blog_type", "=", "masonry"),
            "description" => __( "How many blog masonry columns to display. NOTE: Only for the masonry blog type, and not when fullwidth mode is selected, as this is adaptive.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Full Width", 'swift-framework-plugin' ),
            "param_name"  => "fullwidth",
            "std"         => "no",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Number of items", 'swift-framework-plugin' ),
            "param_name"  => "item_count",
            "value"       => "5",
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
                "heading"     => __( "Order by", 'swift-framework-plugin' ),
                "param_name"  => "order_by",
                "std"         => "date",
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
                "std"         => "DESC",
                "value"       => array(
                    __( "Ascending", 'swift-framework-plugin' )  => "ASC",
                    __( "Descending", 'swift-framework-plugin' ) => "DESC"
                ),
                "description" => __( "Select if you'd like the items to be ordered in ascending or descending order.", 'swift-framework-plugin' )
        ),
        // array(
        //     "type"        => "buttonset",
        //     "heading"     => __( "Include Sticky Posts", 'swift-framework-plugin' ),
        //     "param_name"  => "include_sticky",
        //     "value"       => array(
        //         __( 'No', 'swift-framework-plugin' )  => "no",
        //         __( 'Yes', 'swift-framework-plugin' ) => "yes"
        //     ),
        //     "buttonset_on"  => "yes",
        //     "std"         => "no",
        //     "description" => __( "Enables sticky posts to be included in the results.", 'swift-framework-plugin' )
        // ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show title text", 'swift-framework-plugin' ),
            "param_name"  => "show_title",
            "std"         => "yes",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Show the item title text.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show item excerpt", 'swift-framework-plugin' ),
            "param_name"  => "show_excerpt",
            "std"         => "yes",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("blog_type", "!=", "bold"),
            "description" => __( "Show the item excerpt text.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show item details", 'swift-framework-plugin' ),
            "param_name"  => "show_details",
            "std"         => "yes",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Show the item details.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Excerpt Length", 'swift-framework-plugin' ),
            "param_name"  => "excerpt_length",
            "value"       => "20",
            "required"       => array("blog_type", "!=", "bold"),
            "description" => __( "The length of the excerpt for the posts.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Content Output", 'swift-framework-plugin' ),
            "param_name"  => "content_output",
            "value"       => array(
                __( "Excerpt", 'swift-framework-plugin' )      => "excerpt",
                __( "Full Content", 'swift-framework-plugin' ) => "full_content"
            ),
            "required"       => array("blog_type", "!=", "bold"),
            "description" => __( "Choose whether to display the excerpt or the full content for the post. Full content is not available for the masonry or bold view types.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show read more link", 'swift-framework-plugin' ),
            "param_name"  => "show_read_more",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Show a read more link below the excerpt. NOTE: Not used in Bold or Masonry types.", 'swift-framework-plugin' )
        ),
        array(
            "type"       => "section_tab",
            "param_name" => "social_options_tab",
            "heading"    => __( "Social Integration", 'swift-framework-plugin' ),
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Social Integration", 'swift-framework-plugin' ),
            "param_name"  => "social_integration",
            "std"         => "no",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Enable social integration within the blog posts (only on Masonry blog types). NOTE: This will only integrate Twitter/Instagram posts to the first page of your blog, and won't be included in any further pages, or content loaded via infinite scroll or AJAX. It therefore works best when you show a high number of blog posts on a single page, and pagination MUST be set to none.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Twitter Username", 'swift-framework-plugin' ),
            "param_name"  => "twitter_username",
            "value"       => "",
            "required"       => array("social_integration", "=", "yes"),
            "description" => __( "Enter your twitter username here to include tweets in the blog grid. Ensure you have the Twitter oAuth plugin installed and your details added.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Number of Instagram items", 'swift-framework-plugin' ),
            "param_name"  => "insta_item_count",
            "value"       => "",
            "required"       => array("social_integration", "=", "yes"),
            "description" => __( "The number of instagram items to show. If you haven't already, you'll need to set up your Instagram account in Swift Framework > Instagram Auth.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Number of Tweets", 'swift-framework-plugin' ),
            "param_name"  => "tweet_item_count",
            "value"       => "",
            "required"       => array("social_integration", "=", "yes"),
            "description" => __( "The number of tweets to show.", 'swift-framework-plugin' )
        ),
        array(
            "type"       => "section_tab",
            "param_name" => "aux_options_tab",
            "heading"    => __( "Aux", 'swift-framework-plugin' ),
        ),
        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Filter", 'swift-framework-plugin' ),
            "param_name"  => "blog_filter",
            "std"         => "yes",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Show the blog category filter above the items.", 'swift-framework-plugin' )
        ),
    );

    if ( spb_get_theme_name() == "uplift" ) {
        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Basic Filter", 'swift-framework-plugin' ),
            "param_name"  => "basic_blog_filter",
            "std"         => "no",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("blog_filter", "=", "yes"),
            "description" => __( "Set the blog filter to be basic, simply a category filter.", 'swift-framework-plugin' )
        );
    }

    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Pagination", 'swift-framework-plugin' ),
        "param_name"  => "pagination",
        "value"       => array(
            __( "Infinite scroll", 'swift-framework-plugin' )  => "infinite-scroll",
            __( "Load more (AJAX)", 'swift-framework-plugin' ) => "load-more",
            __( "Standard", 'swift-framework-plugin' )         => "standard",
            __( "None", 'swift-framework-plugin' )             => "none"
        ),
        "description" => __( "Show pagination.", 'swift-framework-plugin' )
    );

    if ( spb_get_theme_name() == "atelier" ) {
        $params[] = array(
            "type"       => "section_tab",
            "param_name" => "styling_options_tab",
            "heading"    => __( "Styling", 'swift-framework-plugin' ),
        );
        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Alt Styling", 'swift-framework-plugin' ),
            "param_name"  => "alt_styling",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Select 'Yes' if you'd like the standard blog item content to be boxed.", 'swift-framework-plugin' )
        );
    }

    if ( spb_get_theme_name() == "joyn" ) {
        $params[] = array(
            "type"       => "section_tab",
            "param_name" => "styling_options_tab",
            "heading"    => __( "Styling", 'swift-framework-plugin' ),
        );
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Thumbnail Hover Style", 'swift-framework-plugin' ),
            "param_name"  => "hover_style",
            "value"       => array(
                __( 'Default', 'swift-framework-plugin' )     => "default",
                __( 'Standard', 'swift-framework-plugin' )    => "gallery-standard",
                __( 'Gallery Alt', 'swift-framework-plugin' ) => "gallery-alt-one",
            ),
            "description" => __( "Choose the thumbnail hover style for the asset. If set to 'Default', then this uses the thumbnail type set in the theme options.", 'swift-framework-plugin' )
        );
    }

    $params[] = array(
        "type"       => "section_tab",
        "param_name" => "advanced_options_tab",
        "heading"    => __( "Advanced", 'swift-framework-plugin' ),
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
    SPBMap::map( 'spb_blog', array(
        "name"   => __( "Blog", 'swift-framework-plugin' ),
        "base"   => "spb_blog",
        "class"  => "spb_blog spb_tab_media",
        "icon"   => "icon-blog",
        "params" => $params
    ) );
