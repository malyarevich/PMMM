<?php

    /*
    *
    *   Swift Page Builder - Helpers Class
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    /* CHECK THEME FEATURE SUPPORT
    ================================================== */
    if ( !function_exists( 'spb_theme_supports' ) ) {
        function spb_theme_supports( $feature ) {
            $supports = get_theme_support( 'swiftframework' );
            $supports = $supports[0];
            if ( !isset( $supports[ $feature ] ) || $supports[ $feature ] == "") {
                return false;
            } else {
                return isset( $supports[ $feature ] );
            }
        }
    }

    /* CHECK WOOCOMMERCE IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_woocommerce_activated' ) ) {
        function sf_woocommerce_activated() {
            if ( class_exists( 'woocommerce' ) ) {
                return true;
            } else {
                return false;
            }
        }
    }


    /* CHECK WPML IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_wpml_activated' ) ) {
        function sf_wpml_activated() {
            if ( function_exists('icl_object_id') ) {
                return true;
            } else {
                return false;
            }
        }
    }


    /* CHECK GRAVITY FORMS IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_gravityforms_activated' ) ) {
        function sf_gravityforms_activated() {
            if ( class_exists( 'GFForms' ) ) {
                return true;
            } else {
                return false;
            }
        }
    }


    /* CHECK NINJA FORMS IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_ninjaforms_activated' ) ) {
        function sf_ninjaforms_activated() {
            if ( function_exists( 'ninja_forms_shortcode' ) ) {
                return true;
            } else {
                return false;
            }
        }
    }

    /* GET NINJA FORMS LIST
    ================================================== */
    if ( ! function_exists( 'sf_ninjaforms_list' ) ) {
        function sf_ninjaforms_list() {

            if ( !is_admin() || !sf_ninjaforms_activated() ) {
                return;
            }

            $forms       = ninja_forms_get_all_forms();
            $forms_array = array();

            if ( ! empty( $forms ) ) {
                foreach ( $forms as $form ):
                    $forms_array[ $form['id'] ] = $form['data']['form_title'];
                endforeach;
            }

            return $forms_array;
        }
    }

    /* CHECK GP PRICING IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_gopricing_activated' ) ) {
        function sf_gopricing_activated() {
            if ( class_exists( 'GW_GoPricing' ) ) {
                return true;
            } else {
                return false;
            }
        }
    }

    /* GET GRAVITY FORMS LIST
    ================================================== */
    if ( ! function_exists( 'sf_gravityforms_list' ) ) {
        function sf_gravityforms_list() {

            if ( !is_admin() ) {
                return;
            }

            $forms       = RGFormsModel::get_forms( null, 'title' );
            $forms_array = array();

            if ( ! empty( $forms ) ) {
                foreach ( $forms as $form ):
                    $forms_array[ $form->id ] = $form->title;
                endforeach;
            }

            return $forms_array;
        }
    }


    /* GET GO PRICING TABLES LIST
    ================================================== */
    if ( ! function_exists( 'sf_gopricing_list' ) ) {
        function sf_gopricing_list() {

            if ( !is_admin() ) {
                return;
            }

            if( defined('GW_GO_PREFIX') ){
            
                $tables_data = get_option( GW_GO_PREFIX . 'tables' );

                 if ( ! empty( $tables_data ) ) {
                    foreach ( $tables_data as $pricing_table ) {
                        $ptables_array[ $pricing_table['table-id'] ] = esc_attr( $pricing_table['table-name'] );
                    }
                }


            } else{
   
                $args = array( 'post_type' => 'go_pricing_tables');  
        
                $posts_query = new WP_Query( $args );
        
                if ( empty( $posts_query->posts ) ) return;
        
                wp_reset_query();

                $posts = $posts_query->posts;
                foreach ( (array)$posts as $post ) {
        
                    $tables_data[$post->ID] = array(
                        'postid' => $post->ID,
                        'name' => $post->post_title, 
                        'id' => $post->post_excerpt
                    );
            
                }

                $ptables_array  = array();
       
                if ( ! empty( $tables_data ) ) {
                    foreach ( $tables_data as $pricing_table ) {           
                        $ptables_array[ $pricing_table['id'] ] = esc_attr( $pricing_table['name'] );
                    }
                }

            }   
         
            return $ptables_array;
        }
    }

    /* GET CUSTOM POST TYPE TAXONOMY LIST
    ================================================== */
    if ( ! function_exists( 'sf_get_category_list' ) ) {
        function sf_get_category_list( $category_name, $filter = 0, $category_child = "", $frontend_display = false ) {

            if ( !$frontend_display && !is_admin() ) {
                return;
            }

            if ( $category_name == "product-category" ) {
                $category_name = "product_cat";
            }

            if ( ! $filter ) {

                $get_category  = get_categories( array( 'taxonomy' => $category_name ) );
                $category_list = array( '0' => 'All' );

                foreach ( $get_category as $category ) {
                    if ( isset( $category->slug ) ) {
                        $category_list[] = $category->slug;
                    }
                }

                return $category_list;

            } else if ( $category_child != "" && $category_child != "All" ) {

                $childcategory = get_term_by( 'slug', $category_child, $category_name );
                $get_category  = get_categories( array(
                        'taxonomy' => $category_name,
                        'child_of' => $childcategory->term_id
                    ) );
                $category_list = array( '0' => 'All' );

                foreach ( $get_category as $category ) {
                    if ( isset( $category->cat_name ) ) {
                        $category_list[] = $category->slug;
                    }
                }

                return $category_list;

            } else {

                $get_category  = get_categories( array( 'taxonomy' => $category_name ) );
                $category_list = array( '0' => 'All' );

                foreach ( $get_category as $category ) {
                    if ( isset( $category->cat_name ) ) {
                        $category_list[] = $category->cat_name;
                    }
                }

                return $category_list;
            }
        }
    }  


    /* GET ATTACHMENT META
    ================================================== */
    if ( ! function_exists( 'spb_get_attachment_meta' ) ) {
        function spb_get_attachment_meta( $attachment_id ) {
    
            $attachment = get_post( $attachment_id );
    
            if ( isset( $attachment ) ) {
                return array(
                    'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
                    'caption' => $attachment->post_excerpt,
                    'description' => $attachment->post_content,
                    'href' => get_permalink( $attachment->ID ),
                    'src' => $attachment->guid,
                    'title' => $attachment->post_title
                );
            }
        }
    }


    /* PERFORMANCE FRIENDLY GET META FUNCTION
    ================================================== */
    if ( !function_exists( 'spb_get_post_meta' ) ) {
        function spb_get_post_meta( $id, $key = "", $single = false ) {

            $GLOBALS['spb_post_meta'] = isset( $GLOBALS['spb_post_meta'] ) ? $GLOBALS['spb_post_meta'] : array();
            if ( ! isset( $id ) ) {
                return;
            }
            if ( ! is_array( $id ) ) {
                if ( ! isset( $GLOBALS['spb_post_meta'][ $id ] ) ) {
                    //$GLOBALS['sf_post_meta'][ $id ] = array();
                    $GLOBALS['spb_post_meta'][ $id ] = get_post_meta( $id );
                }
                if ( ! empty( $key ) && isset( $GLOBALS['spb_post_meta'][ $id ][ $key ] ) && ! empty( $GLOBALS['spb_post_meta'][ $id ][ $key ] ) ) {
                    if ( $single ) {
                        return maybe_unserialize( $GLOBALS['spb_post_meta'][ $id ][ $key ][0] );
                    } else {
                        return array_map( 'maybe_unserialize', $GLOBALS['spb_post_meta'][ $id ][ $key ] );
                    }
                }

                if ( $single ) {
                    return '';
                } else {
                    return array();
                }

            }

            return get_post_meta( $id, $key, $single );
        }
    }
    

    /* SHORTCODE FIX
    ================================================== */
    if ( ! function_exists( 'spb_shortcode_content_filter' ) ) :
        function spb_shortcode_content_filter( $content ) {
            // array of custom shortcodes requiring the fix
            $block = join( "|", array(
                    "alert",
                    "sf_button",
                    "icon",
                    "sf_iconbox",
                    "sf_imagebanner",
                    "social",
                    "sf_social_share",
                    "highlight",
                    "decorative_ampersand",
                    "blockquote1",
                    "blockquote2",
                    "blockquote3",
                    "pullquote",
                    "one_half",
                    "one_half_last",
                    "one_third",
                    "one_third_last",
                    "two_third",
                    "two_third_last",
                    "one_fourth",
                    "one_fourth_last",
                    "three_fourth",
                    "three_fourth_last",
                    "one_half",
                    "one_half_last",
                    "progress_bar",
                    "chart",
                    "sf_count",
                    "sf_countdown",
                    "sf_tooltip",
                    "sf_modal",
                    "sf_fullscreenvideo",
                    "sf_visibility",
                    "table",
                    "trow",
                    "thcol",
                    "tcol",
                    "list",
                    "list_item",
                    "hr",
                    "accordion",
                    "panel",
                    "tabs",
                    "tab",
                    "sf_supersearch",
                    "gallery",
                    "spb_accordion",
                    "spb_accordion_tab",
                    "spb_blog",
                    "spb_boxed_content",
                    "spb_clients",
                    "spb_codesnippet",
                    "spb_divider",
                    "spb_faqs",
                    "spb_gallery",
                    "spb_googlechart",
                    "spb_gmaps",
                    "spb_latest_tweets",
                    "spb_message",
                    "spb_parallax",
                    "spb_portfolio",
                    "spb_portfolio_carousel",
                    "spb_portfolio_showcase",
                    "spb_posts_carousel",
                    "spb_products",
                    "spb_products_mini",
                    "spb_recent_posts",
                    "spb_slider",
                    "spb_sitemap",
                    "spb_search",
                    "spb_supersearch",
                    "spb_tabs",
                    "spb_tab",
                    "spb_text_block",
                    "spb_team",
                    "spb_testimonial",
                    "spb_testimonial_carousel",
                    "spb_testimonial_slider",
                    "spb_toggle",
                    "spb_tour",
                    "spb_tweets_slider",
                    "spb_video",
                    "spb_blank_spacer",
                    "spb_image",
                    "spb_blog_grid",
                    "spb_promo_bar",
                    "spb_gravityforms",
                    "spb_campaigns",
                    "spb_column",
                    "spb_row",
                    "spb_icon_box",
                    "spb_multilayer_parallax",
                    "spb_multilayer_parallax_layer",
                    "spb_image_banner",
                    "spb_icon_box_grid",
                    "spb_icon_box_grid_element",
                    "spb_section"
                ) );
            // opening tag
            $rep = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );
            // closing tag
            $rep = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep );

            return $rep;
        }
        add_filter( "the_content", "spb_shortcode_content_filter" );
    endif;

    /* SPB TEMPLATE LIST FUNCTION
    ================================================== */
    if ( ! function_exists( 'sf_list_spb_sections' ) ) {
        function sf_list_spb_sections() {

            if ( !is_admin() ) {
                return;
            }

            $spb_sections_list  = array();
            $spb_sections_query = new WP_Query( array( 'post_type' => 'spb-section', 'posts_per_page' => - 1 ) );
            while ( $spb_sections_query->have_posts() ) : $spb_sections_query->the_post();
                $spb_sections_list[ get_the_title() ] = get_the_ID();
            endwhile;
            wp_reset_postdata();

            if ( empty( $spb_sections_list ) ) {
                $spb_sections_list[] = "No SPB Templates found";
            }

            return $spb_sections_list;
        }
    }

    /* GALLERY LIST FUNCTION
    ================================================== */
    if ( ! function_exists( 'sf_list_galleries' ) ) {
        function sf_list_galleries() {
            $galleries_list  = array();
            $galleries_query = new WP_Query( array( 'post_type' => 'galleries', 'posts_per_page' => - 1 ) );
            while ( $galleries_query->have_posts() ) : $galleries_query->the_post();
                $galleries_list[ get_the_title() ] = get_the_ID();
            endwhile;
            wp_reset_postdata();

            if ( empty( $galleries_list ) ) {
                $galleries_list[] = "No galleries found";
            }

            return $galleries_list;
        }
    }

    /* ATTRIBUTE MAP
    ================================================== */
    function spb_map( $attributes ) {
        if ( ! isset( $attributes['base'] ) ) {
            trigger_error( "Wrong spb_map object. Base attribute is required", E_USER_ERROR );
            die();
        }
        SPBMap::map( $attributes['base'], $attributes );
    }


    /* GET IMAGE BY SIZE
    ================================================== */
    function spb_getImageBySize(
        $params = array(
            'post_id'    => null,
            'attach_id'  => null,
            'thumb_size' => 'thumbnail'
        )
    ) {
        //array( 'post_id' => $post_id, 'thumb_size' => $grid_thumb_size )
        if ( ( ! isset( $params['attach_id'] ) || $params['attach_id'] == null ) && ( ! isset( $params['post_id'] ) || $params['post_id'] == null ) ) {
            return;
        }
        $post_id = isset( $params['post_id'] ) ? $params['post_id'] : 0;

        if ( $post_id ) {
            $attach_id = get_post_thumbnail_id( $post_id );
        } else {
            $attach_id = $params['attach_id'];
        }

        $thumb_size = $params['thumb_size'];

        global $_wp_additional_image_sizes;
        $thumbnail = '';

        if ( is_string( $thumb_size ) && ( ( ! empty( $_wp_additional_image_sizes[ $thumb_size ] ) && is_array( $_wp_additional_image_sizes[ $thumb_size ] ) ) || in_array( $thumb_size, array(
                        'thumbnail',
                        'thumb',
                        'medium',
                        'large',
                        'full'
                    ) ) )
        ) {
           
            $thumbnail = wp_get_attachment_image( $attach_id, $thumb_size );
            //TODO APPLY FILTER
        }

        $p_img = $p_img_small = "";

        if ( $thumbnail == '' && $attach_id ) {
            if ( is_string( $thumb_size ) ) {
                $thumb_size = str_replace( array( 'px', ' ', '*', '&times;' ), array( '', '', 'x', 'x' ), $thumb_size );
                $thumb_size = explode( "x", $thumb_size );
            }

            // Resize image to custom size
            if ( $attach_id != "" && isset( $thumb_size[0] ) && isset( $thumb_size[1] ) ) {
                $p_img = spb_resize( $attach_id, null, $thumb_size[0], $thumb_size[1], true );
            }
            $alt = trim(strip_tags( get_post_meta($attach_id, '_wp_attachment_image_alt', true) ));

            if ( empty($alt) ) {
                $attachment = get_post($attach_id);
                if ( $attachment ) {
                    $alt = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
                }
            }
            if ( empty($alt) ) {
                if ( $attachment ) {
                    $alt = trim(strip_tags( $attachment->post_title )); // Finally, use the title
                }
            }

        }
        
        if ( $p_img != '' ) {
            $p_img_small = '<img src="' . $p_img['url'] . '" width="66" height="66"  class="attachment-thumbnail" />';
        } else {
            $img = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
            if ( isset($img[0]) ) {
                $p_img_small = '<img src="' . $img[0] . '" width="66" height="66"  class="attachment-thumbnail" />';
            }
        }

        $p_img_large = wp_get_attachment_image_src( $attach_id, 'large' );

        return array( 'thumbnail' => $thumbnail, 'p_img_large' => $p_img_large, 'p_img_small' => $p_img_small );
    }


    /* GET COLUMN CONTROLS
    ================================================== */
    function spb_getColumnControls( $width ) {
        switch ( $width ) {

            case "span2" :
                $w = "1/6";
                break;

            case "span3" :
                $w = "1/4";
                break;

            case "span4" :
                $w = "1/3";
                break;

            case "span6" :
                $w = "1/2";
                break;

            case "span8" :
                $w = "2/3";
                break;

            case "span9" :
                $w = "3/4";
                break;

            case "span12" :
                $w = "1/1";
                break;

            default :
                $w = $width;
        }

        return $w;
    }

    /* CONVERT COLUMN TO FRACTIONAL
    ================================================== */
    function spb_translateColumnWidthToFractional( $width ) {
        switch ( $width ) {

            case "span2" :
                $w = "1/6";
                break;

            case "span3" :
                $w = "1/4";
                break;

            case "span4" :
                $w = "1/3";
                break;

            case "span6" :
                $w = "1/2";
                break;

            case "span8" :
                $w = "2/3";
                break;

            case "span9" :
                $w = "3/4";
                break;

            case "span12" :
                $w = "1/1";
                break;

            default :
                $w = $width;
        }

        return $w;
    }

    /* Convert 2 to
    ---------------------------------------------------------- */
    function spb_translateColumnsCountToSpanClass( $grid_columns_count ) {
        $teaser_width = '';
        switch ( $grid_columns_count ) {
            case '1' :
                $teaser_width = 'span12';
                break;
            case '2' :
                $teaser_width = 'span6';
                break;
            case '3' :
                $teaser_width = 'span4';
                break;
            case '4' :
                $teaser_width = 'span3';
                break;
            case '6' :
                $teaser_width = 'span2';
                break;
        }

        return $teaser_width;
    }

    function spb_translateColumnWidthToSpanEditor( $width ) {

        switch ( $width ) {

            case "1/6" :

                $w = "span2";

                break;

            case "1/4" :

                $w = "span3";

                break;

            case "1/3" :

                $w = "span4";

                break;

            case "1/2" :

                $w = "span6";

                break;

            case "2/3" :

                $w = "span8";

                break;

            case "3/4" :

                $w = "span9";

                break;

            case "1/1" :

                $w = "span12";

                break;

            default :
                $w = $width;
        }

        return $w;
    }


    function spb_translateColumnWidthToSpan( $width ) {

        switch ( $width ) {

            case "1/6" :

                $w = "col-sm-2";

                break;

            case "1/4" :

                $w = "col-sm-3";

                break;

            case "1/3" :

                $w = "col-sm-4";

                break;

            case "1/2" :

                $w = "col-sm-6";

                break;

            case "2/3" :

                $w = "col-sm-8";

                break;

            case "3/4" :

                $w = "col-sm-9";

                break;

            case "1/1" :

                $w = "col-sm-12";

                break;

            default :
                $w = $width;
        }

        return $w;
    }


    /* ANIMATIONS LIST
    ================================================== */
    function spb_animations_list() {

        if ( function_exists( 'sf_get_animations_list' ) ) {

            return sf_get_animations_list(true);

        } else {

            $array = array(
                __( "None", 'swift-framework-plugin' )                  => "none",
                __( "Bounce", 'swift-framework-plugin' )                => "bounce",
                __( "Flash", 'swift-framework-plugin' )                 => "flash",
                __( "Pulse", 'swift-framework-plugin' )                 => "pulse",
                __( "Rubberband", 'swift-framework-plugin' )            => "rubberBand",
                __( "Shake", 'swift-framework-plugin' )                 => "shake",
                __( "Swing", 'swift-framework-plugin' )                 => "swing",
                __( "TaDa", 'swift-framework-plugin' )                  => "tada",
                __( "Wobble", 'swift-framework-plugin' )                => "wobble",
                __( "Bounce In", 'swift-framework-plugin' )             => "bounceIn",
                __( "Bounce In Down", 'swift-framework-plugin' )     => "bounceInDown",
                __( "Bounce In Left", 'swift-framework-plugin' )     => "bounceInLeft",
                __( "Bounce In Right", 'swift-framework-plugin' )    => "bounceInRight",
                __( "Bounce In Up", 'swift-framework-plugin' )       => "bounceInUp",
                __( "Fade In", 'swift-framework-plugin' )            => "fadeIn",
                __( "Fade In Down", 'swift-framework-plugin' )       => "fadeInDown",
                __( "Fade In Down Big", 'swift-framework-plugin' )   => "fadeInDownBig",
                __( "Fade In Left", 'swift-framework-plugin' )       => "fadeInLeft",
                __( "Fade In Left Big", 'swift-framework-plugin' )   => "fadeInLeftBig",
                __( "Fade In Right", 'swift-framework-plugin' )      => "fadeInRight",
                __( "Fade In Right Big", 'swift-framework-plugin' )  => "fadeInRightBig",
                __( "Fade In Up", 'swift-framework-plugin' )         => "fadeInUp",
                __( "Fade In Up Big", 'swift-framework-plugin' )     => "fadeInUpBig",
                __( "Flip", 'swift-framework-plugin' )              => "flip",
                __( "Flip In X", 'swift-framework-plugin' )          => "flipInX",
                __( "Flip In Y", 'swift-framework-plugin' )          => "flipInY",
                __( "Lightspeed In", 'swift-framework-plugin' )      => "lightSpeedIn",
                __( "Rotate In", 'swift-framework-plugin' )          => "rotateIn",
                __( "Rotate In Down Left", 'swift-framework-plugin' ) => "rotateInDownLeft",
                __( "Rotate In Down Right", 'swift-framework-plugin' ) => "rotateInDownRight",
                __( "Rotate In Up Left", 'swift-framework-plugin' )  => "rotateInUpLeft",
                __( "Rotate In Up Right", 'swift-framework-plugin' ) => "rotateInUpRight",
                __( "Roll In", 'swift-framework-plugin' )            => "rollIn",
                __( "Zoom In", 'swift-framework-plugin' )            => "zoomIn",
                __( "Zoom In Down", 'swift-framework-plugin' )       => "zoomInDown",
                __( "Zoom In Left", 'swift-framework-plugin' )       => "zoomInLeft",
                __( "Zoom In Right", 'swift-framework-plugin' )      => "zoomInRight",
                __( "Zoom In Up", 'swift-framework-plugin' )         => "zoomInUp",
                __( "Slide In Down", 'swift-framework-plugin' )      => "slideInDown",
                __( "Slide In Left", 'swift-framework-plugin' )      => "slideInLeft",
                __( "Slide In Right", 'swift-framework-plugin' )     => "slideInRight",
                __( "Slide In Up", 'swift-framework-plugin' )        => "slideInUp",
            );

            $array = apply_filters( 'spb_animations_list_array', $array );
            return $array;

        }
    }

    /* RESPONSIVE VIS LIST
    ================================================== */
    function spb_responsive_vis_list() {

        $array = array(
            __( 'Visible Globally', 'swift-framework-plugin' )          => "",
            __( 'Hidden on Desktop', 'swift-framework-plugin' )          => "hidden-lg_hidden-md",
            __( 'Hidden on Desktop', 'swift-framework-plugin' )          => "hidden-lg_hidden-md",
            __( 'Hidden on Tablet', 'swift-framework-plugin' )           => "hidden-sm",
            __( 'Hidden on Desktop + Tablet', 'swift-framework-plugin' ) => "hidden-lg_hidden-md_hidden-sm",
            __( 'Hidden on Desktop + Phone', 'swift-framework-plugin' )  => "hidden-lg_hidden-md_hidden-xs",
            __( 'Hidden on Tablet + Phone', 'swift-framework-plugin' )   => "hidden-xs_hidden-sm",
            __( 'Hidden on Phone', 'swift-framework-plugin' )            => "hidden-xs"
        );
        return $array;

    }

    /* CAROUSEL ARROW OUTPUT
    ================================================== */
    if ( ! function_exists( 'spb_carousel_arrows' ) ) {
        function spb_carousel_arrows( $nowrap = false ) {
            $carousel_arrows = "";
            if ( $nowrap ) {
                $carousel_arrows = apply_filters('spb_carousel_arrows_nowrap_html', '<a href="#" class="carousel-prev"><i class="ss-navigateleft"></i></a><a href="#" class="carousel-next"><i class="ss-navigateright"></i></a>');
            } else {
                $carousel_arrows = apply_filters('spb_carousel_arrows_html', '<div class="carousel-arrows"><a href="#" class="carousel-prev"><i class="ss-navigateleft"></i></a><a href="#" class="carousel-next"><i class="ss-navigateright"></i></a></div>');
            }
            return $carousel_arrows;

        }
    }

    /* GET POST TYPES
    ================================================== */
    if ( ! function_exists( 'spb_get_post_types' ) ) {
        function spb_get_post_types() {
            $args       = array(
                'public' => true
            );
            $post_types = get_post_types($args);
            array_unshift($post_types, "");

            // Unset specfic results
            unset($post_types['attachment']);
            unset($post_types['spb-section']);
            unset($post_types['swift-slider']);

            return $post_types;
        }
    }

    /* GET PRODUCTS
    ================================================== */
    if ( ! function_exists( 'spb_get_products' ) ) {
        function spb_get_products() {

            if ( !is_admin() ) {
                return;
            }

            $attr = array(
                'post_type'       => array( 'product', 'product_variation' ),
                'fields'          => 'ids',
                "orderby"          => "name",
                "order"            => "asc",
                'posts_per_page'   => -1
            );
            $results = get_posts($attr);
            $products_array = array();

            $products_array[] = "";
            foreach ($results as $id) {
                $title = get_the_title($id);
                $products_array[$id] = $title;
            }

            wp_cache_flush();

            return $products_array;
        }
    }

    /* GET THEME NAME
    ================================================== */
    if ( ! function_exists( 'spb_get_theme_name' ) ) {
        function spb_get_theme_name() {
            return get_option( 'sf_theme');
        }
    }
    
    /* GET PRODUCT CATEGORIES
    ================================================== */
    if ( ! function_exists( 'spb_get_product_categories' ) ) {
        function spb_get_product_categories() {
 
            $get_category  = get_categories( array( 'taxonomy' => 'product_cat' ) );
           
            $category_list = array();
            
            //dummy empty value
            $category_list[''] = "";

            foreach ( $get_category as $category ) {
                  $category_list[$category->slug] = $category->cat_name ;
            }

            return $category_list;

        }
    }

    /* GET SVG ICONS
    ================================================== */
    if ( ! function_exists( 'spb_get_svg_icons' ) ) {
        function spb_get_svg_icons() {

            $svg_icons = array();
            $svg_icons = apply_filters( 'spb_svg_icons_list', $svg_icons );

            if ( empty($svg_icons) ) {
                return "Unfortunately there are no svg icons available to use.";
            } else {
                return $svg_icons;
            }
        }
    }


    if ( ! function_exists( 'spb_get_svg_icons' ) ) {
        function spb_get_svg_icons() {

            if ( !spb_theme_supports('nucleo-svg-icons') ) {
                return '';
            }

            $coloured_svg_icons = array(
                'arrows-color-1_cloud-download-95' => 'arrows-color-1_cloud-download-95', 
                'arrows-color-1_cloud-upload-96' => 'arrows-color-1_cloud-upload-96', 
                'arrows-color-2_file-download-87' => 'arrows-color-2_file-download-87', 
                'arrows-color-2_file-upload-86' => 'arrows-color-2_file-upload-86', 
                'arrows-color-2_lines' => 'arrows-color-2_lines', 
                'arrows-color-2_replay' => 'arrows-color-2_replay', 
                'arrows-color-3_cloud-refresh' => 'arrows-color-3_cloud-refresh', 
                'business-color_agenda' => 'business-color_agenda', 
                'business-color_award-48' => 'business-color_award-48', 
                'business-color_award-49' => 'business-color_award-49', 
                'business-color_badge' => 'business-color_badge', 
                'business-color_board-28' => 'business-color_board-28', 
                'business-color_board-29' => 'business-color_board-29', 
                'business-color_board-30' => 'business-color_board-30', 
                'business-color_briefcase-26' => 'business-color_briefcase-26', 
                'business-color_building' => 'business-color_building', 
                'business-color_bulb-63' => 'business-color_bulb-63', 
                'business-color_business-contact-85' => 'business-color_business-contact-85', 
                'business-color_business-contact-86' => 'business-color_business-contact-86', 
                'business-color_business-contact-87' => 'business-color_business-contact-87', 
                'business-color_business-contact-88' => 'business-color_business-contact-88', 
                'business-color_businessman-03' => 'business-color_businessman-03', 
                'business-color_calculator' => 'business-color_calculator', 
                'business-color_cheque' => 'business-color_cheque', 
                'business-color_connect' => 'business-color_connect', 
                'business-color_factory' => 'business-color_factory', 
                'business-color_globe' => 'business-color_globe', 
                'business-color_goal-64' => 'business-color_goal-64', 
                'business-color_gold' => 'business-color_gold', 
                'business-color_hammer' => 'business-color_hammer', 
                'business-color_handout' => 'business-color_handout', 
                'business-color_handshake' => 'business-color_handshake', 
                'business-color_hierarchy-55' => 'business-color_hierarchy-55', 
                'business-color_payment' => 'business-color_payment', 
                'business-color_pig' => 'business-color_pig', 
                'business-color_pin' => 'business-color_pin', 
                'business-color_progress' => 'business-color_progress', 
                'business-color_safe' => 'business-color_safe', 
                'business-color_signature' => 'business-color_signature', 
                'emoticons-color_bomb' => 'emoticons-color_bomb', 
                'emoticons-color_manga-62' => 'emoticons-color_manga-62', 
                'emoticons-color_sad' => 'emoticons-color_sad', 
                'emoticons-color_smile' => 'emoticons-color_smile', 
                'envir-color_recycling' => 'envir-color_recycling', 
                'envir-color_save-planet' => 'envir-color_save-planet', 
                'food-color_beer-95' => 'food-color_beer-95', 
                'food-color_carrot' => 'food-color_carrot', 
                'food-color_cocktail' => 'food-color_cocktail', 
                'food-color_cutlery-77' => 'food-color_cutlery-77', 
                'food-color_donut' => 'food-color_donut', 
                'food-color_fish' => 'food-color_fish', 
                'food-color_grape' => 'food-color_grape', 
                'food-color_moka' => 'food-color_moka', 
                'food-color_mug' => 'food-color_mug', 
                'food-color_pizza-slice' => 'food-color_pizza-slice', 
                'gestures-color_double-tap' => 'gestures-color_double-tap', 
                'gestures-color_pinch' => 'gestures-color_pinch', 
                'gestures-color_scroll-horitontal' => 'gestures-color_scroll-horitontal', 
                'gestures-color_scroll-vertical' => 'gestures-color_scroll-vertical', 
                'gestures-color_stretch' => 'gestures-color_stretch', 
                'gestures-color_touch-id' => 'gestures-color_touch-id', 
                'holidays-color_cockade' => 'holidays-color_cockade', 
                'holidays-color_gift' => 'holidays-color_gift', 
                'holidays-color_gift-exchange' => 'holidays-color_gift-exchange', 
                'holidays-color_message' => 'holidays-color_message', 
                'location-color_appointment' => 'location-color_appointment', 
                'location-color_flag-complex' => 'location-color_flag-complex', 
                'location-color_map-gps' => 'location-color_map-gps', 
                'location-color_map-pin' => 'location-color_map-pin', 
                'location-color_treasure-map-40' => 'location-color_treasure-map-40', 
                'location-color_world-pin' => 'location-color_world-pin', 
                'media-color-1_action-74' => 'media-color-1_action-74', 
                'media-color-1_album' => 'media-color-1_album', 
                'media-color-1_audio-91' => 'media-color-1_audio-91', 
                'media-color-1_camera-18' => 'media-color-1_camera-18', 
                'media-color-1_flash-24' => 'media-color-1_flash-24', 
                'media-color-1_frame-12' => 'media-color-1_frame-12', 
                'media-color-1_grid' => 'media-color-1_grid', 
                'media-color-1_image-01' => 'media-color-1_image-01', 
                'media-color-1_kid' => 'media-color-1_kid', 
                'media-color-1_layers' => 'media-color-1_layers', 
                'media-color-1_play-69' => 'media-color-1_play-69', 
                'media-color-1_polaroid-user' => 'media-color-1_polaroid-user', 
                'media-color-1_sd' => 'media-color-1_sd', 
                'media-color-1_shake' => 'media-color-1_shake', 
                'media-color-1_speaker' => 'media-color-1_speaker', 
                'media-color-1_sport' => 'media-color-1_sport', 
                'media-color-1_ticket-76' => 'media-color-1_ticket-76', 
                'media-color-1_touch' => 'media-color-1_touch', 
                'media-color-1_videocamera-71' => 'media-color-1_videocamera-71', 
                'media-color-2_guitar' => 'media-color-2_guitar', 
                'media-color-2_music-cloud' => 'media-color-2_music-cloud', 
                'media-color-2_note-03' => 'media-color-2_note-03', 
                'media-color-2_radio' => 'media-color-2_radio', 
                'media-color-2_remix' => 'media-color-2_remix', 
                'media-color-2_sound-wave' => 'media-color-2_sound-wave', 
                'nature-color_chicken' => 'nature-color_chicken', 
                'nature-color_forest' => 'nature-color_forest', 
                'nature-color_mountain' => 'nature-color_mountain', 
                'nature-color_tree-02' => 'nature-color_tree-02', 
                'objects-color_anchor' => 'objects-color_anchor', 
                'objects-color_battery' => 'objects-color_battery', 
                'objects-color_bow' => 'objects-color_bow', 
                'objects-color_cone' => 'objects-color_cone', 
                'objects-color_diamond' => 'objects-color_diamond', 
                'objects-color_globe' => 'objects-color_globe', 
                'objects-color_key-25' => 'objects-color_key-25', 
                'objects-color_key-26' => 'objects-color_key-26', 
                'objects-color_lamp' => 'objects-color_lamp', 
                'objects-color_leaf-38' => 'objects-color_leaf-38', 
                'objects-color_planet' => 'objects-color_planet', 
                'objects-color_puzzle-09' => 'objects-color_puzzle-09', 
                'objects-color_puzzle-10' => 'objects-color_puzzle-10', 
                'objects-color_spaceship' => 'objects-color_spaceship', 
                'objects-color_support-16' => 'objects-color_support-16', 
                'objects-color_support-17' => 'objects-color_support-17', 
                'objects-color_umbrella-13' => 'objects-color_umbrella-13', 
                'tech-color_computer-old' => 'tech-color_computer-old', 
                'tech-color_headphones' => 'tech-color_headphones', 
                'tech-color_keyboard-wifi' => 'tech-color_keyboard-wifi', 
                'tech-color_laptop-front' => 'tech-color_laptop-front', 
                'tech-color_mobile-button' => 'tech-color_mobile-button', 
                'tech-color_print' => 'tech-color_print', 
                'tech-color_tablet' => 'tech-color_tablet', 
                'tech-color_tv-old' => 'tech-color_tv-old', 
                'tech-color_watch' => 'tech-color_watch', 
                'tech-color_webcam-38' => 'tech-color_webcam-38', 
                'tech-color_wifi' => 'tech-color_wifi', 
                'text-color_capitalize' => 'text-color_capitalize', 
                'text-color_caps-all' => 'text-color_caps-all', 
                'text-color_list-numbers' => 'text-color_list-numbers', 
                'text-color_quote' => 'text-color_quote', 
                'ui-color-1_attach-87' => 'ui-color-1_attach-87', 
                'ui-color-1_calendar-grid-61' => 'ui-color-1_calendar-grid-61', 
                'ui-color-1_check' => 'ui-color-1_check', 
                'ui-color-1_dashboard-29' => 'ui-color-1_dashboard-29', 
                'ui-color-1_dashboard-30' => 'ui-color-1_dashboard-30', 
                'ui-color-1_edit-72' => 'ui-color-1_edit-72', 
                'ui-color-1_edit-76' => 'ui-color-1_edit-76', 
                'ui-color-1_email-83' => 'ui-color-1_email-83', 
                'ui-color-1_eye-17' => 'ui-color-1_eye-17', 
                'ui-color-1_eye-ban-18' => 'ui-color-1_eye-ban-18', 
                'ui-color-1_home-52' => 'ui-color-1_home-52', 
                'ui-color-1_home-minimal' => 'ui-color-1_home-minimal', 
                'ui-color-1_lock' => 'ui-color-1_lock', 
                'ui-color-1_lock-open' => 'ui-color-1_lock-open', 
                'ui-color-1_notification-70' => 'ui-color-1_notification-70', 
                'ui-color-1_pencil' => 'ui-color-1_pencil', 
                'ui-color-1_preferences' => 'ui-color-1_preferences', 
                'ui-color-1_preferences-rotate' => 'ui-color-1_preferences-rotate', 
                'ui-color-1_send' => 'ui-color-1_send', 
                'ui-color-1_settings' => 'ui-color-1_settings', 
                'ui-color-1_simple-add' => 'ui-color-1_simple-add', 
                'ui-color-1_simple-remove' => 'ui-color-1_simple-remove', 
                'ui-color-1_trash' => 'ui-color-1_trash', 
                'ui-color-1_ui-04' => 'ui-color-1_ui-04', 
                'ui-color-1_zoom' => 'ui-color-1_zoom', 
                'ui-color-1_zoom-in' => 'ui-color-1_zoom-in', 
                'ui-color-1_zoom-out' => 'ui-color-1_zoom-out', 
                'ui-color-2_archive' => 'ui-color-2_archive', 
                'ui-color-2_battery-half' => 'ui-color-2_battery-half', 
                'ui-color-2_battery-low' => 'ui-color-2_battery-low', 
                'ui-color-2_chat-content' => 'ui-color-2_chat-content', 
                'ui-color-2_favourite-28' => 'ui-color-2_favourite-28', 
                'ui-color-2_grid-45' => 'ui-color-2_grid-45', 
                'ui-color-2_grid-48' => 'ui-color-2_grid-48', 
                'ui-color-2_hourglass' => 'ui-color-2_hourglass', 
                'ui-color-2_lab' => 'ui-color-2_lab', 
                'ui-color-2_layers' => 'ui-color-2_layers', 
                'ui-color-2_like' => 'ui-color-2_like', 
                'ui-color-2_link-69' => 'ui-color-2_link-69', 
                'ui-color-2_paragraph' => 'ui-color-2_paragraph', 
                'ui-color-2_target' => 'ui-color-2_target', 
                'ui-color-2_tile-56' => 'ui-color-2_tile-56', 
                'ui-color-2_time' => 'ui-color-2_time', 
                'ui-color-2_time-alarm' => 'ui-color-2_time-alarm', 
                'ui-color-2_time-countdown' => 'ui-color-2_time-countdown', 
                'ui-color-2_webpage' => 'ui-color-2_webpage', 
                'ui-color-2_window-add' => 'ui-color-2_window-add', 
                'ui-color-2_window-delete' => 'ui-color-2_window-delete', 
                'users-color_badge-13' => 'users-color_badge-13', 
                'users-color_man-37' => 'users-color_man-37', 
                'users-color_man-45' => 'users-color_man-45', 
                'users-color_multiple-11' => 'users-color_multiple-11', 
                'users-color_parent' => 'users-color_parent', 
                'users-color_woman-24' => 'users-color_woman-24', 
                'users-color_woman-46' => 'users-color_woman-46', 
                'weather-color_celsius' => 'weather-color_celsius', 
                'weather-color_fahrenheit' => 'weather-color_fahrenheit', 
                'weather-color_wind' => 'weather-color_wind',
            );

            $outline_svg_icons = array(
                'arrows-1_circle-down-40' => 'arrows-1_circle-down-40', 
                'arrows-1_circle-right-09' => 'arrows-1_circle-right-09', 
                'arrows-1_cloud-download-93' => 'arrows-1_cloud-download-93', 
                'arrows-1_cloud-upload-94' => 'arrows-1_cloud-upload-94', 
                'arrows-1_direction-53' => 'arrows-1_direction-53', 
                'arrows-1_fullscreen-double-74' => 'arrows-1_fullscreen-double-74', 
                'arrows-1_loop-82' => 'arrows-1_loop-82', 
                'arrows-1_minimal-down' => 'arrows-1_minimal-down', 
                'arrows-1_minimal-left' => 'arrows-1_minimal-left', 
                'arrows-1_minimal-right' => 'arrows-1_minimal-right', 
                'arrows-1_minimal-up' => 'arrows-1_minimal-up', 
                'arrows-1_refresh-69' => 'arrows-1_refresh-69', 
                'arrows-1_share-91' => 'arrows-1_share-91', 
                'arrows-1_shuffle-98' => 'arrows-1_shuffle-98', 
                'arrows-2_file-download-89' => 'arrows-2_file-download-89', 
                'arrows-2_file-upload-88' => 'arrows-2_file-upload-88', 
                'arrows-2_lines' => 'arrows-2_lines', 
                'arrows-2_log-out' => 'arrows-2_log-out', 
                'business-outline_agenda' => 'business-outline_agenda', 
                'business-outline_award-48' => 'business-outline_award-48', 
                'business-outline_award-49' => 'business-outline_award-49', 
                'business-outline_badge' => 'business-outline_badge', 
                'business-outline_board-28' => 'business-outline_board-28', 
                'business-outline_board-29' => 'business-outline_board-29', 
                'business-outline_board-30' => 'business-outline_board-30', 
                'business-outline_briefcase-24' => 'business-outline_briefcase-24', 
                'business-outline_building' => 'business-outline_building', 
                'business-outline_bulb-63' => 'business-outline_bulb-63', 
                'business-outline_business-contact-86' => 'business-outline_business-contact-86', 
                'business-outline_business-contact-88' => 'business-outline_business-contact-88', 
                'business-outline_businessman-04' => 'business-outline_businessman-04', 
                'business-outline_calculator' => 'business-outline_calculator', 
                'business-outline_chart' => 'business-outline_chart', 
                'business-outline_chart-bar-32' => 'business-outline_chart-bar-32', 
                'business-outline_chart-pie-35' => 'business-outline_chart-pie-35', 
                'business-outline_chart-pie-36' => 'business-outline_chart-pie-36', 
                'business-outline_connect' => 'business-outline_connect', 
                'business-outline_contacts' => 'business-outline_contacts', 
                'business-outline_currency-dollar' => 'business-outline_currency-dollar', 
                'business-outline_currency-euro' => 'business-outline_currency-euro', 
                'business-outline_currency-pound' => 'business-outline_currency-pound', 
                'business-outline_currency-yen' => 'business-outline_currency-yen', 
                'business-outline_factory' => 'business-outline_factory', 
                'business-outline_globe' => 'business-outline_globe', 
                'business-outline_goal-64' => 'business-outline_goal-64', 
                'business-outline_gold' => 'business-outline_gold', 
                'business-outline_handout' => 'business-outline_handout', 
                'business-outline_handshake' => 'business-outline_handshake', 
                'business-outline_hierarchy-55' => 'business-outline_hierarchy-55', 
                'business-outline_laptop-71' => 'business-outline_laptop-71', 
                'business-outline_laptop-91' => 'business-outline_laptop-91', 
                'business-outline_money-12' => 'business-outline_money-12', 
                'business-outline_notes' => 'business-outline_notes', 
                'business-outline_pig' => 'business-outline_pig', 
                'business-outline_pin' => 'business-outline_pin', 
                'business-outline_plug' => 'business-outline_plug', 
                'business-outline_scale' => 'business-outline_scale', 
                'business-outline_sign' => 'business-outline_sign', 
                'business-outline_signature' => 'business-outline_signature', 
                'cards-outline_amazon' => 'cards-outline_amazon', 
                'cards-outline_amex' => 'cards-outline_amex', 
                'cards-outline_android' => 'cards-outline_android', 
                'cards-outline_apple' => 'cards-outline_apple', 
                'cards-outline_mastercard' => 'cards-outline_mastercard', 
                'cards-outline_paypal' => 'cards-outline_paypal', 
                'cards-outline_stripe' => 'cards-outline_stripe', 
                'cards-outline_visa' => 'cards-outline_visa', 
                'clothes-outline_bag-21' => 'clothes-outline_bag-21', 
                'clothes-outline_shirt-business' => 'clothes-outline_shirt-business', 
                'clothes-outline_tie-bow' => 'clothes-outline_tie-bow', 
                'design-outline_app' => 'design-outline_app', 
                'design-outline_artboard' => 'design-outline_artboard', 
                'design-outline_book-open' => 'design-outline_book-open', 
                'design-outline_brush' => 'design-outline_brush', 
                'design-outline_bullet-list' => 'design-outline_bullet-list', 
                'design-outline_code' => 'design-outline_code', 
                'design-outline_code-editor' => 'design-outline_code-editor', 
                'design-outline_command' => 'design-outline_command', 
                'design-outline_compass' => 'design-outline_compass', 
                'design-outline_copy' => 'design-outline_copy', 
                'design-outline_crop' => 'design-outline_crop', 
                'design-outline_design' => 'design-outline_design', 
                'design-outline_image' => 'design-outline_image', 
                'design-outline_measure-17' => 'design-outline_measure-17', 
                'design-outline_mobile-design' => 'design-outline_mobile-design', 
                'design-outline_mobile-dev' => 'design-outline_mobile-dev', 
                'design-outline_mouse-10' => 'design-outline_mouse-10', 
                'design-outline_note-code' => 'design-outline_note-code', 
                'design-outline_paint-16' => 'design-outline_paint-16', 
                'design-outline_paint-bucket-40' => 'design-outline_paint-bucket-40', 
                'design-outline_palette' => 'design-outline_palette', 
                'design-outline_pantone' => 'design-outline_pantone', 
                'design-outline_paper-design' => 'design-outline_paper-design', 
                'design-outline_paper-dev' => 'design-outline_paper-dev', 
                'design-outline_path-exclude' => 'design-outline_path-exclude', 
                'design-outline_pen-tool' => 'design-outline_pen-tool', 
                'design-outline_phone' => 'design-outline_phone', 
                'design-outline_photo-editor' => 'design-outline_photo-editor', 
                'design-outline_scissors-dashed' => 'design-outline_scissors-dashed', 
                'design-outline_tablet-mobile' => 'design-outline_tablet-mobile', 
                'design-outline_text' => 'design-outline_text', 
                'design-outline_vector' => 'design-outline_vector', 
                'design-outline_wand-99' => 'design-outline_wand-99', 
                'design-outline_webpage' => 'design-outline_webpage', 
                'design-outline_window-paragraph' => 'design-outline_window-paragraph', 
                'education-outline_abc' => 'education-outline_abc', 
                'education-outline_atom' => 'education-outline_atom', 
                'education-outline_award-55' => 'education-outline_award-55', 
                'education-outline_backpack-58' => 'education-outline_backpack-58', 
                'education-outline_board-51' => 'education-outline_board-51', 
                'education-outline_book-open' => 'education-outline_book-open', 
                'education-outline_grammar-check' => 'education-outline_grammar-check', 
                'education-outline_language' => 'education-outline_language', 
                'education-outline_microscope' => 'education-outline_microscope', 
                'education-outline_paper' => 'education-outline_paper', 
                'education-outline_pencil-47' => 'education-outline_pencil-47', 
                'education-outline_school' => 'education-outline_school', 
                'emoticons-outline_bomb' => 'emoticons-outline_bomb', 
                'emoticons-outline_cry-15' => 'emoticons-outline_cry-15', 
                'emoticons-outline_fist' => 'emoticons-outline_fist', 
                'emoticons-outline_like' => 'emoticons-outline_like', 
                'emoticons-outline_manga-62' => 'emoticons-outline_manga-62', 
                'emoticons-outline_sad' => 'emoticons-outline_sad', 
                'emoticons-outline_smile' => 'emoticons-outline_smile', 
                'emoticons-outline_speechless' => 'emoticons-outline_speechless', 
                'emoticons-outline_surprise' => 'emoticons-outline_surprise', 
                'envir-outline_recycling' => 'envir-outline_recycling', 
                'envir-outline_save-planet' => 'envir-outline_save-planet', 
                'files-2_ai-illustrator' => 'files-2_ai-illustrator', 
                'files-2_asp' => 'files-2_asp', 
                'files-2_css' => 'files-2_css', 
                'files-2_docx' => 'files-2_docx', 
                'files-2_gif' => 'files-2_gif', 
                'files-2_html' => 'files-2_html', 
                'files-2_jpg-jpeg' => 'files-2_jpg-jpeg', 
                'files-2_js-javascript-jquery' => 'files-2_js-javascript-jquery', 
                'files-3_pdf' => 'files-3_pdf', 
                'files-3_php' => 'files-3_php', 
                'files-3_png' => 'files-3_png', 
                'files-3_psd-photoshop' => 'files-3_psd-photoshop', 
                'files-3_sql' => 'files-3_sql', 
                'files-3_txt' => 'files-3_txt', 
                'files-3_xml' => 'files-3_xml', 
                'files-3_zip' => 'files-3_zip', 
                'files-outline_archive-3d-content' => 'files-outline_archive-3d-content', 
                'files-outline_paper' => 'files-outline_paper', 
                'files-outline_single-content-02' => 'files-outline_single-content-02', 
                'files-outline_single-copies' => 'files-outline_single-copies', 
                'files-outline_single-copy-04' => 'files-outline_single-copy-04', 
                'files-outline_single-paragraph' => 'files-outline_single-paragraph', 
                'files-outline_zip-55' => 'files-outline_zip-55', 
                'food-outline_beer-95' => 'food-outline_beer-95', 
                'food-outline_cocktail' => 'food-outline_cocktail', 
                'food-outline_cutlery-77' => 'food-outline_cutlery-77', 
                'food-outline_donut' => 'food-outline_donut', 
                'food-outline_moka' => 'food-outline_moka', 
                'food-outline_mug' => 'food-outline_mug', 
                'food-outline_pizza-slice' => 'food-outline_pizza-slice', 
                'food-outline_tea' => 'food-outline_tea', 
                'gestures-outline_double-tap' => 'gestures-outline_double-tap', 
                'gestures-outline_pinch' => 'gestures-outline_pinch', 
                'gestures-outline_scroll-horitontal' => 'gestures-outline_scroll-horitontal', 
                'gestures-outline_scroll-vertical' => 'gestures-outline_scroll-vertical', 
                'gestures-outline_stretch' => 'gestures-outline_stretch', 
                'gestures-outline_tap-02' => 'gestures-outline_tap-02', 
                'gestures-outline_touch-id' => 'gestures-outline_touch-id', 
                'health-outline_apple' => 'health-outline_apple', 
                'health-outline_brain' => 'health-outline_brain', 
                'health-outline_dna-27' => 'health-outline_dna-27', 
                'health-outline_sleep' => 'health-outline_sleep', 
                'health-outline_steps' => 'health-outline_steps', 
                'health-outline_wheelchair' => 'health-outline_wheelchair', 
                'holidays-outline_cockade' => 'holidays-outline_cockade', 
                'holidays-outline_gift' => 'holidays-outline_gift', 
                'holidays-outline_gift-exchange' => 'holidays-outline_gift-exchange', 
                'holidays-outline_message' => 'holidays-outline_message', 
                'location-outline_flag-complex' => 'location-outline_flag-complex', 
                'location-outline_map-gps' => 'location-outline_map-gps', 
                'location-outline_map-pin' => 'location-outline_map-pin', 
                'location-outline_worl-marker' => 'location-outline_worl-marker', 
                'location-outline_world' => 'location-outline_world', 
                'media-1_action-74' => 'media-1_action-74', 
                'media-1_album' => 'media-1_album', 
                'media-1_audio-92' => 'media-1_audio-92', 
                'media-1_camera-19' => 'media-1_camera-19', 
                'media-1_countdown-34' => 'media-1_countdown-34', 
                'media-1_flash-24' => 'media-1_flash-24', 
                'media-1_frame-12' => 'media-1_frame-12', 
                'media-1_image-01' => 'media-1_image-01', 
                'media-1_image-02' => 'media-1_image-02', 
                'media-1_layers' => 'media-1_layers', 
                'media-1_movie-62' => 'media-1_movie-62', 
                'media-1_player' => 'media-1_player', 
                'media-1_shake' => 'media-1_shake', 
                'media-1_speaker' => 'media-1_speaker', 
                'media-1_sport' => 'media-1_sport', 
                'media-1_touch' => 'media-1_touch', 
                'media-1_video-66' => 'media-1_video-66', 
                'media-2_headphones' => 'media-2_headphones', 
                'media-2_headphones-mic' => 'media-2_headphones-mic', 
                'media-2_note-03' => 'media-2_note-03', 
                'media-2_radio' => 'media-2_radio', 
                'media-2_sound-wave' => 'media-2_sound-wave', 
                'objects-outline_anchor' => 'objects-outline_anchor', 
                'objects-outline_bow' => 'objects-outline_bow', 
                'objects-outline_diamond' => 'objects-outline_diamond', 
                'objects-outline_dice' => 'objects-outline_dice', 
                'objects-outline_globe' => 'objects-outline_globe', 
                'objects-outline_key-26' => 'objects-outline_key-26', 
                'objects-outline_lamp' => 'objects-outline_lamp', 
                'objects-outline_planet' => 'objects-outline_planet', 
                'objects-outline_puzzle-09' => 'objects-outline_puzzle-09', 
                'objects-outline_puzzle-10' => 'objects-outline_puzzle-10', 
                'objects-outline_skull' => 'objects-outline_skull', 
                'objects-outline_spaceship' => 'objects-outline_spaceship', 
                'objects-outline_support-16' => 'objects-outline_support-16', 
                'objects-outline_support-17' => 'objects-outline_support-17', 
                'objects-outline_umbrella-13' => 'objects-outline_umbrella-13', 
                'shopping-outline_box-3d-50' => 'shopping-outline_box-3d-50', 
                'shopping-outline_cart' => 'shopping-outline_cart', 
                'shopping-outline_cart-add' => 'shopping-outline_cart-add', 
                'shopping-outline_cart-remove' => 'shopping-outline_cart-remove', 
                'shopping-outline_credit-card' => 'shopping-outline_credit-card', 
                'shopping-outline_credit-locked' => 'shopping-outline_credit-locked', 
                'shopping-outline_delivery-fast' => 'shopping-outline_delivery-fast', 
                'shopping-outline_delivery-time' => 'shopping-outline_delivery-time', 
                'shopping-outline_discount' => 'shopping-outline_discount', 
                'shopping-outline_gift' => 'shopping-outline_gift', 
                'shopping-outline_list' => 'shopping-outline_list', 
                'shopping-outline_newsletter' => 'shopping-outline_newsletter', 
                'shopping-outline_tag-content' => 'shopping-outline_tag-content', 
                'sport-outline_boxing' => 'sport-outline_boxing', 
                'sport-outline_crown' => 'sport-outline_crown', 
                'sport-outline_flag-finish' => 'sport-outline_flag-finish', 
                'sport-outline_podium-trophy' => 'sport-outline_podium-trophy', 
                'sport-outline_tactic' => 'sport-outline_tactic', 
                'sport-outline_user-run' => 'sport-outline_user-run', 
                'tech-outline_computer-old' => 'tech-outline_computer-old', 
                'tech-outline_desktop-screen' => 'tech-outline_desktop-screen', 
                'tech-outline_keyboard-mouse' => 'tech-outline_keyboard-mouse', 
                'tech-outline_laptop-front' => 'tech-outline_laptop-front', 
                'tech-outline_mobile' => 'tech-outline_mobile', 
                'tech-outline_player-19' => 'tech-outline_player-19', 
                'tech-outline_print' => 'tech-outline_print', 
                'tech-outline_tablet-button' => 'tech-outline_tablet-button', 
                'tech-outline_tv-old' => 'tech-outline_tv-old', 
                'tech-outline_watch' => 'tech-outline_watch', 
                'tech-outline_wifi' => 'tech-outline_wifi', 
                'text-outline_capitalize' => 'text-outline_capitalize', 
                'text-outline_caps-all' => 'text-outline_caps-all', 
                'text-outline_list-numbers' => 'text-outline_list-numbers', 
                'text-outline_quote' => 'text-outline_quote', 
                'transportation-outline_bike-sport' => 'transportation-outline_bike-sport', 
                'transportation-outline_light-traffic' => 'transportation-outline_light-traffic', 
                'travel-outline_world' => 'travel-outline_world', 
                'ui-1_attach-87' => 'ui-1_attach-87', 
                'ui-1_bell-54' => 'ui-1_bell-54', 
                'ui-1_calendar-grid-61' => 'ui-1_calendar-grid-61', 
                'ui-1_check-circle-07' => 'ui-1_check-circle-07', 
                'ui-1_dashboard-29' => 'ui-1_dashboard-29', 
                'ui-1_drop' => 'ui-1_drop', 
                'ui-1_email-85' => 'ui-1_email-85', 
                'ui-1_eye-17' => 'ui-1_eye-17', 
                'ui-1_eye-ban-18' => 'ui-1_eye-ban-18', 
                'ui-1_home-52' => 'ui-1_home-52', 
                'ui-1_lock' => 'ui-1_lock', 
                'ui-1_lock-open' => 'ui-1_lock-open', 
                'ui-1_notification-70' => 'ui-1_notification-70', 
                'ui-1_pencil' => 'ui-1_pencil', 
                'ui-1_preferences-circle' => 'ui-1_preferences-circle', 
                'ui-1_preferences-circle-rotate' => 'ui-1_preferences-circle-rotate', 
                'ui-1_send' => 'ui-1_send', 
                'ui-1_settings' => 'ui-1_settings', 
                'ui-1_settings-gear-65' => 'ui-1_settings-gear-65', 
                'ui-1_trash-simple' => 'ui-1_trash-simple', 
                'ui-1_ui-04' => 'ui-1_ui-04', 
                'ui-1_zoom-split' => 'ui-1_zoom-split', 
                'ui-1_zoom-split-in' => 'ui-1_zoom-split-in', 
                'ui-1_zoom-split-out' => 'ui-1_zoom-split-out', 
                'ui-2_alert-exclamation' => 'ui-2_alert-exclamation', 
                'ui-2_alert-question' => 'ui-2_alert-question', 
                'ui-2_alert-i' => 'ui-2_alert-i', 
                'ui-2_archive' => 'ui-2_archive', 
                'ui-2_chart-bar-53' => 'ui-2_chart-bar-53', 
                'ui-2_chat-round-content' => 'ui-2_chat-round-content', 
                'ui-2_disk' => 'ui-2_disk', 
                'ui-2_favourite-28' => 'ui-2_favourite-28', 
                'ui-2_favourite-31' => 'ui-2_favourite-31', 
                'ui-2_grid-48' => 'ui-2_grid-48', 
                'ui-2_grid-49' => 'ui-2_grid-49', 
                'ui-2_lab' => 'ui-2_lab', 
                'ui-2_layers' => 'ui-2_layers', 
                'ui-2_link-69' => 'ui-2_link-69', 
                'ui-2_menu-34' => 'ui-2_menu-34', 
                'ui-2_menu-35' => 'ui-2_menu-35', 
                'ui-2_paragraph' => 'ui-2_paragraph', 
                'ui-2_share-bold' => 'ui-2_share-bold', 
                'ui-2_tile-56' => 'ui-2_tile-56', 
                'ui-2_time' => 'ui-2_time', 
                'ui-2_time-countdown' => 'ui-2_time-countdown', 
                'users-outline_man-23' => 'users-outline_man-23', 
                'users-outline_multiple-11' => 'users-outline_multiple-11', 
                'users-outline_woman-25' => 'users-outline_woman-25',
            );
            $coloured_svg_icons = apply_filters( 'spb_coloured_svg_icons', $coloured_svg_icons );
            $outline_svg_icons = apply_filters( 'spb_outline_svg_icons', $outline_svg_icons );

            // Output
            $svg_icon_output = '';
            foreach ( $coloured_svg_icons as $icon ) {
                $svg_icon_output .= '<li class="svg-icon bg-image-icon" data-icon="'.$icon.'"><i class="svg-icon-picker-item '.$icon.'"></i></li>';
            }
            foreach ( $outline_svg_icons as $icon ) {
                $svg_icon_output .= '<li class="svg-icon bg-image-icon" data-icon="'.$icon.'"><i class="svg-icon-picker-item outline-svg '.$icon.'"></i></li>';
            }
            
            return $svg_icon_output;
        }
    }

    if ( !function_exists( 'spb_theme_svg_icons' ) ) {
        function spb_theme_svg_icons( $svg_icons ) {

            // Check if we can access files
            if ( !function_exists('glob') ) {
                return $svg_icons;
            }

            // Path to directory to scan
            $folder = '/swift-builder-icons/svg/';
            $directory = get_template_directory() . $folder;
            $uri = get_template_directory_uri() . $folder;

            // Get all svg files
            $svgs = glob($directory . "*.svg");

            // If no files, return
            if ( empty($svgs) ) {
                return;
            }

            // Add each icon to array
            foreach($svgs as $svg) {
                $svg = basename($svg);
                $key = $uri . $svg;
                if ( !array_key_exists( $key, $svg_icons ) ) {
                    $svg_icons[ $key ] = $svg;
                }
            }

            // Return array
            return $svg_icons; 
        }
        add_filter( 'spb_svg_icons_list', 'spb_theme_svg_icons' );
    }

    /* VIDEO EMBED FUNCTIONS
    ================================================== */
    function spb_get_vimeoid( $url ) {
        $regex = '~
                    # Match Vimeo link and embed code
                    (?:<iframe [^>]*src=")?     # If iframe match up to first quote of src
                    (?:                         # Group vimeo url
                        https?:\/\/             # Either http or https
                        (?:[\w]+\.)*            # Optional subdomains
                        vimeo\.com              # Match vimeo.com
                        (?:[\/\w]*\/videos?)?   # Optional video sub directory this handles groups links also
                        \/                      # Slash before Id
                        ([0-9]+)                # $1: VIDEO_ID is numeric
                        [^\s]*                  # Not a space
                    )                           # End group
                    "?                          # Match end quote if part of src
                    (?:[^>]*></iframe>)?        # Match the end of the iframe
                    (?:<p>.*</p>)?              # Match any title information stuff
                    ~ix';

        preg_match( $regex, $url, $matches );

        $vimeo_ID_fallback = substr( $url, strrpos( $url, '/' ) + 1 );

        if ( isset( $matches[1] ) ) {
            return $matches[1];
        } else {
            return $vimeo_ID_fallback;
        }
    }

    if ( ! function_exists( 'spb_video_embed' ) ) {
        function spb_video_embed( $url, $width = 640, $height = 480, $extra_params = '' ) {
            if ( strpos( $url, 'youtube' ) || strpos( $url, 'youtu.be' ) ) {
                return spb_video_youtube( $url, $width, $height, $extra_params );
            } else {
                return spb_video_vimeo( $url, $width, $height, $extra_params );
            }
        }
    }

    if ( ! function_exists( 'spb_video_youtube' ) ) {
        function spb_video_youtube( $url, $width = 640, $height = 480, $extra_params = '' ) {
            
            // Get video id
            preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $video_id );
            $video_id = $video_id[1];

            // Variables
            $params = '?showinfo=0&controls=1&modestbranding=1' . $extra_params;
            $youtube_params = apply_filters( 'spb_youtube_embed_params', $params );
            $video_padding = ( intval( $height, 10 ) / intval( $width, 10 ) ) * 100;
            $inline_style  = 'padding-bottom: ' . $video_padding . '%;';
            $ssl_override = apply_filters( 'spb_video_youtube_ssl', false );

            // Return
            $return = '';
            if ( is_ssl() || $ssl_override ) {
                $return = '<div class="video-wrap" style="' . $inline_style . '"><iframe itemprop="video" class="video-embed" src="https://www.youtube.com/embed/' . $video_id . $youtube_params . '" width="' . $width . '" height="' . $height . '" allowfullscreen></iframe></div>';
            } else {
                $return = '<div class="video-wrap" style="' . $inline_style . '"><iframe itemprop="video" class="video-embed" src="http://www.youtube.com/embed/' . $video_id . $youtube_params . '" width="' . $width . '" height="' . $height . '" allowfullscreen></iframe></div>';
            }
            $return = apply_filters( 'spb_video_youtube_embed_return', $return, $video_id, $width, $height );
            return $return;
        }
    }

    if ( ! function_exists( 'spb_video_vimeo' ) ) {
        function spb_video_vimeo( $url, $width = 640, $height = 480, $extra_params = '' ) {
            
            // Get video id
            $url          = str_replace( 'https://', 'http://', $url );
            $video_id     = spb_get_vimeoid( $url );

            // Variables
            $params = '?title=0&amp;byline=0&amp;portrait=0' . $extra_params;
            $vimeo_params = apply_filters( 'spb_vimeo_embed_params', $params );
            $video_padding = ( intval( $height, 10 ) / intval( $width, 10 ) ) * 100;
            $inline_style  = 'padding-bottom: ' . $video_padding . '%;';
            $ssl_override = apply_filters( 'spb_video_youtube_ssl', false );

            if ( $video_id == "" ) {
                return '<div class="video-wrap">' . __( 'Video not found', 'nota' ) . '</div>';
            }

            // Return
            $return = '';
            if ( is_ssl() || $ssl_override ) {
                $return = '<div class="video-wrap" style="' . $inline_style . '"><iframe itemprop="video" class="video-embed" src="https://player.vimeo.com/video/' . $video_id . $vimeo_params . '" width="' . $width . '" height="' . $height . '"></iframe></div>';
            } else {
                $return = '<div class="video-wrap" style="' . $inline_style . '"><iframe itemprop="video" class="video-embed" src="http://player.vimeo.com/video/' . $video_id . $vimeo_params . '" width="' . $width . '" height="' . $height . '"></iframe></div>';
            }
            $return = apply_filters( 'spb_video_vimeo_embed_return', $return, $video_id, $width, $height );
            return $return;
        }
    }

    if ( ! function_exists( 'spb_get_embed_src' ) ) {
        function spb_get_embed_src( $url ) {
            if ( strpos( $url, 'youtube' ) ) {
                preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $video_id );
                $youtube_params = apply_filters( 'spb_youtube_embed_src_params', '?autoplay=1' );
                if ( is_ssl() ) {
                    if ( isset( $video_id[1] ) ) {
                        return 'https://www.youtube.com/embed/' . $video_id[1] . $youtube_params;
                    }
                } else {
                    if ( isset( $video_id[1] ) ) {
                        return 'http://www.youtube.com/embed/' . $video_id[1] . $youtube_params;
                    }
                }
            } else {
                $url          = str_replace( 'https://', 'http://', $url );
                $video_id     = spb_get_vimeoid( $url );
                $time_stamp = explode('#',$url);
                $video_id  = (!empty($time_stamp[1]))?$video_id.'#'.$time_stamp[1]:$video_id;
                $vimeo_params = apply_filters( 'spb_vimeo_embed_src_params', '?title=0&byline=0&portrait=0&autoplay=1' );
                if ( is_ssl() ) {
                    if ( $video_id != "" ) {
                        return 'https://player.vimeo.com/video/' . $video_id . $vimeo_params;
                    }
                } else {
                    if ( $video_id != "" ) {
                        return 'http://player.vimeo.com/video/' . $video_id . $vimeo_params;
                    }
                }
            }
        }
    }

    /* FORMAT CONTENT
    ================================================== */
    function spb_format_content( $content ) {
        $content = do_shortcode( shortcode_unautop( $content ) );
        $content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );

        return $content;
    }

    if ( ! function_exists( 'shortcode_exists' ) ) {
        /**
         * Check if a shortcode is registered in WordPress.
         * Examples: shortcode_exists( 'caption' ) - will return true.
         * shortcode_exists( 'blah' ) - will return false.
         */
        function shortcode_exists( $shortcode = false ) {
            global $shortcode_tags;

            if ( ! $shortcode ) {
                return false;
            }

            if ( array_key_exists( $shortcode, $shortcode_tags ) ) {
                return true;
            }

            return false;
        }
    }


    function spb_fieldAttachedImages( $att_ids = array() ) {
        $output = '';
        foreach ( $att_ids as $th_id ) {
            $thumb_src = wp_get_attachment_image_src( $th_id, 'thumbnail' );

            if ( $thumb_src ) {
                $thumb_src = $thumb_src[0];
                $output .= '
                <li class="added">
                    <img rel="' . $th_id . '" src="' . $thumb_src . '" />
                    <span class="img-added">' . __( 'Added', 'swift-framework-plugin' ) . '</span>
                    <div class="sf-close-image-bar"><a title="Deselect" class="sf-close-delete-file" href="#">&times;</a>   </div>
                </li>';
            }
        }
        if ( $output != '' ) {
            return $output;
        }
    }

    function spb_removeNotExistingImgIDs( $param_value ) {
        $tmp       = explode( ",", $param_value );
        $return_ar = array();
        foreach ( $tmp as $id ) {
            if ( wp_get_attachment_image( $id ) ) {
                $return_ar[] = $id;
            }
        }
        $tmp = implode( ",", $return_ar );

        return $tmp;
    }


    /*
    * Resize images dynamically using wp built in functions
    * Victor Teixeira
    *
    * php 5.2+
    *
    * Exemplo de uso:
    *
    * <?php
     * $thumb = get_post_thumbnail_id();
     * $image = vt_resize( $thumb, '', 140, 110, true );
     * ?>
    * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
    *
    * @param int $attach_id
    * @param string $img_url
    * @param int $width
    * @param int $height
    * @param bool $crop
    * @return array
    */
    if ( ! function_exists( 'spb_resize' ) ) {
        function spb_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

            // this is an attachment, so we have the ID
            if ( $attach_id ) {
                $image_src        = wp_get_attachment_image_src( $attach_id, 'full' );
                $actual_file_path = get_attached_file( $attach_id );
                // this is not an attachment, let's use the image url
            } else if ( $img_url ) {
                $file_path        = parse_url( $img_url );
                $actual_file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
                $actual_file_path = ltrim( $file_path['path'], '/' );
                $actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
                $orig_size        = getimagesize( $actual_file_path );
                $image_src[0]     = $img_url;
                $image_src[1]     = $orig_size[0];
                $image_src[2]     = $orig_size[1];
            }
            $file_info = pathinfo( $actual_file_path );
            $extension = '.' . $file_info['extension'];

            // the image path without the extension
            $no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

            $cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

            // checking if the file size is larger than the target size
            // if it is smaller or the same size, stop right here and return
            if ( $image_src[1] > $width || $image_src[2] > $height ) {

                // the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
                if ( file_exists( $cropped_img_path ) ) {
                    $cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
                    $vt_image        = array(
                        'url'    => $cropped_img_url,
                        'width'  => $width,
                        'height' => $height
                    );

                    return $vt_image;
                }

                // $crop = false
                if ( $crop == false ) {
                    // calculate the size proportionaly
                    $proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
                    $resized_img_path  = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

                    // checking if the file already exists
                    if ( file_exists( $resized_img_path ) ) {
                        $resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

                        $vt_image = array(
                            'url'    => $resized_img_url,
                            'width'  => $proportional_size[0],
                            'height' => $proportional_size[1]
                        );

                        return $vt_image;
                    }
                }

                // no cache files - let's finally resize it
                $img_editor = wp_get_image_editor( $actual_file_path );

                if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
                    return array(
                        'url'    => '',
                        'width'  => '',
                        'height' => ''
                    );
                }

                $new_img_path = $img_editor->generate_filename();

                if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
                    return array(
                        'url'    => '',
                        'width'  => '',
                        'height' => ''
                    );
                }

                if ( spb_debug() ) {
                    var_dump( file_exists( $actual_file_path ) );
                    var_dump( $actual_file_path );
                }

                if ( ! is_string( $new_img_path ) ) {
                    return array(
                        'url'    => '',
                        'width'  => '',
                        'height' => ''
                    );
                }

                $new_img_size = getimagesize( $new_img_path );
                $new_img      = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

                // resized output
                $vt_image = array(
                    'url'    => $new_img,
                    'width'  => $new_img_size[0],
                    'height' => $new_img_size[1]
                );

                return $vt_image;
            }

            // default output - without resizing
            $vt_image = array(
                'url'    => $image_src[0],
                'width'  => $image_src[1],
                'height' => $image_src[2]
            );

            return $vt_image;
        }
    }

    if ( ! function_exists( 'spb_debug' ) ) {
        function spb_debug() {
            if ( isset( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'spb_debug' ) {
                return true;
            } else {
                return false;
            }
        }
    }

    function spb_js_force_send( $args ) {
        $args['send'] = true;

        return $args;
    }
