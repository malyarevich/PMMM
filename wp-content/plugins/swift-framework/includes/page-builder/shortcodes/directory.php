<?php

    /*
    *
    *	Swift Page Builder - Directory Users Listings Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_directory_user_listings extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $el_position = $output = '';

            extract( shortcode_atts( array(
                'el_position'      => '',
                'width'            => '1/1',
                'el_class'         => ''
            ), $atts ) );

            // Enqueue
            wp_enqueue_script('google-maps');

            $current_user = wp_get_current_user();
            $users_listings_output = sf_directory_user_listings($current_user->ID);

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_directory_user_listings_widget directory-results user-listing-results ' . $width . $el_class . '"   data-ajax-url="' . admin_url('admin-ajax.php') . '" >';
            $output .= "\n\t\t" . '<div class="spb-asset-content spb_wrapper latest-tweets-wrap clearfix">';
            $output .= "\n\t\t\t" . '<ul class="listings-wrap">' . $users_listings_output . "</ul>";
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_directory_user_listings', array(
        "name"   => __( "Directory User Listings", 'swift-framework-plugin' ),
        "base"   => "spb_directory_user_listings",
        "class"  => "spb-latest-tweets",
        "icon"   => "icon-directory-user-listing",
        "params" => array(
           
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        )
    ) );

       /* DIRECTORY ITENS MAPS ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_directory extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            global $wp, $wp_rewrite;

            $title = $address = $img_file_url = $pin_image = $size = $zoom = $directory_category = $directory_map_filter = $directory_map_results = $directory_map_filter_pos = $color = $saturation = $type = $el_position = $width = $pagination = $item_count = $excerpt_length = $el_class = '';

            extract( shortcode_atts( array(
                'title'                    => '',
                'address'                  => '',
                'directory_category'       => '',
                'order'                    => '',
                'directory_map_filter'     => '',
                'directory_map_filter_pos' => '',
                'directory_map_results'    => '',
                'excerpt_length'           => '',
                'item_count'               => '-1',
                'pagination'               => 'no',
                'size'                     => 200,
                'zoom'                     => 14,
                'color'                    => '',
                'saturation'               => '',
                'type'                     => 'm',
                'pin_image'                => '',
                'fullscreen'               => 'no',
                'el_position'              => '',
                'width'                    => '1/1',
                'el_class'                 => ''
            ), $atts ) );
            $output = '';

            // Enqueue
            wp_enqueue_script('google-maps');

            $current_url = home_url(add_query_arg(array(),$wp->request));
            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            $ajax_url = admin_url( 'admin-ajax.php' );
            $size     = str_replace( array( 'px', ' ' ), array( '', '' ), $size );
            if ( $fullscreen == "yes" && $width == "col-sm-12" ) {
                $fullscreen = true;
            } else {
                $fullscreen = false;
            }

            if ( $pin_image != "" ) {
                $img_url = wp_get_attachment_image_src( $pin_image, 'full' );
                if ( is_array( $img_url ) ) {
                    $img_file_url = $img_url[0];
                }
            }

            if ( $fullscreen ) {
                $output .= "\n\t" . '<div class="spb_directory_widget spb_gmaps_widget fullscreen-map spb_content_element '  .$width. $el_class . '">';
            } else {
                $output .= "\n\t" . '<div class="spb_directory_widget spb_gmaps_widget spb_content_element ' .$width. $el_class . '">';
            }

            $output .= "\n\t\t" . '<div class="spb-asset-content">';

            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '', $fullscreen ) : '';

            if ( $directory_map_filter_pos == "above" && $directory_map_filter == 'yes' ) {

                $output .= "\n\t" . '<div class="spb_directory_filter_above container">';
                $dir_placeholder = __( "What are you looking for?", 'swift-framework-plugin' );
                $category_term = "";

                //Search Text
                if ( isset($_POST['dir-search-value']) ) {
                    $dir_search_val = $_POST['dir-search-value'];
                } else {
                    $dir_search_val = "";
                }

                //Location Option
                if ( isset($_POST['dir-location']) ) {
                    $dir_location_val = $_POST['dir-location'];
                } else {
                    $dir_location_val = "";
                }

                //Directory Category
                if ( isset($_POST['dir-category-id']) ) {
                    //$dir_location_val = $_POST['dir-category-id'];
                    $directory_category = $_POST['dir-category-id'];
                } else {
                    $dir_location_val = "";
                }

  
                //Map Filter
                $output .= '<form action="" method="post" class="directory-search-form" data-url="' . $current_url . '" data-page-base="' . $wp_rewrite->pagination_base . '">';
                $output .= '<div class="filter-search-container">';
                $output .= '<input type="text" name="dir-search-value" id="dir-search-value" value="' . $dir_search_val . '" placeholder="'.$dir_placeholder.'"></div>';
                $output .= '<div class="directory-filter">'. sf_directory_location_filter() . '</div>';
                $output .= '<div class="directory-filter"> ' . sf_directory_category_filter( $category_term,  $directory_category ) . '</div>';
                $output .= '<div class="directory-search-container"><a class="btn read-more-button directorySearch" name="directory-search-button" id="directory-search-button">' . __( "Search", 'swift-framework-plugin' ) . '</a></div>';
                $output .= '</form>';
                $output .= '</div>' . $this->endBlockComment( $width );

            }

            $output .= '<div class="spb_map_wrapper">';

            $output .= '<div class="map-directory-canvas" style="width:100%;height:' . $size . 'px;" data-address="' . $address . '" data-zoom="' . $zoom . '" data-maptype="' . $type . '" data-ajaxurl="' . $ajax_url . '" data-excerpt="' . $excerpt_length . '" data-pagination="' . $pagination . '" data-mapcolor="' . $color . '" data-directory-category="' . $directory_category . '" data-directory-map-filter="' . $directory_map_filter . '" data-directory-map-filter-pos="' . $directory_map_filter_pos . '" data-directory-map-results="' . $directory_map_results . '" data-mapsaturation="' . $saturation . '" data-pinimage="' . $img_file_url . '"></div></div>';

            if ( $directory_map_filter_pos == "below" && $directory_map_filter == 'yes' ) {

                $output .= "\n\t" . '<div class="directory-filter-wrap container"><div class="spb_directory_filter_below container">';
                $dir_placeholder = __( "What are you looking for?", 'swift-framework-plugin' );
                $category_term = "";

                //Search Text
                if ( isset($_POST['dir-search-value']) ) {
                    $dir_search_val = $_POST['dir-search-value'];
                } else {
                    $dir_search_val = "";
                }

                //Location Option
                if ( isset($_POST['dir-location']) ) {
                    $dir_location_val = $_POST['dir-location'];
                } else {
                    $dir_location_val = "";
                }

                //Map Filter
                $output .= '<form action="" method="post" class="directory-search-form" data-url="' . $current_url . '" data-page-base="' . $wp_rewrite->pagination_base . '">';
                $output .= '<div class="filter-search-container">';
                $output .= '<input type="text" name="dir-search-value" id="dir-search-value" value="' . $dir_search_val . '" placeholder="'.$dir_placeholder.'"></div>';
                $output .= '<div class="directory-filter">'. sf_directory_location_filter() . '</div>';
                $output .= '<div class="directory-filter"> ' . sf_directory_category_filter( $category_term, $directory_category ) . '</div>';
                $output .= '<div class="directory-search-container"><a class="btn read-more-button directorySearch" name="directory-search-button" id="directory-search-button">' . __( "Search", 'swift-framework-plugin' ) . '</a></div>';
                $output .= '</form>';
                $output .= '</div></div>' . $this->endBlockComment( $width );

            } 

            if ( $fullscreen ) {
            $output .= "\n\t\t" . '<div class="directory-results container">';
            } else {
            $output .= "\n\t\t" . '<div class="directory-results">';
            }

            if ( $directory_map_results != 'map' ) {

                // ITEMS OUTPUT
                $items = sf_directory_items($excerpt_length, $pagination, $item_count, $directory_category, $order);
                $output .= $items;

            }

            $output .= "\n\t\t" . '</div>';

            $output .= "\n\t" . '</div></div>';

            if ( $fullscreen && $width == "col-sm-12" ) {
                $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );
            } else {
               $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
            }

            global $sf_include_maps;
            $sf_include_maps = true;

            return $output;
        }
    }

    SPBMap::map( 'spb_directory', array(
        "name"     => __( "Directory Map", 'swift-framework-plugin' ),
        "base"     => "spb_directory",
        "controls" => "full",
        "class"    => "spb_directory",
        "icon"     => "icon-directory-map",
        "params"   => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Map Height", 'swift-framework-plugin' ),
                "param_name"  => "size",
                "value"       => "300",
                "description" => __( 'Enter map height in pixels. Example: 300.', 'swift-framework-plugin' )
            ),
            array(
                "type"        => "select-multiple",
                "heading"     => __( "Directory category", 'swift-framework-plugin' ),
                "param_name"  => "directory_category",
                "value"       => sf_get_category_list( 'directory-category' ),
                "description" => __( "Choose the category from which you'd like to show the directory items.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Order", 'swift-framework-plugin' ),
                "param_name"  => "order",
                "std"         => "date",
                "value"       => array(
                    __( 'Default', 'swift-framework-plugin' ) => "standard",
                    __( 'Date (Ascending)', 'swift-framework-plugin' )  => "date-asc",
                    __( 'Date (Descending)', 'swift-framework-plugin' )  => "date-desc",
                    __( 'Title (Ascending)', 'swift-framework-plugin' )  => "title-asc",
                    __( 'Title (Descending)', 'swift-framework-plugin' )  => "title-desc",
                ),
                "description" => __( "Select how you'd like the items to be ordered.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Map Filter", 'swift-framework-plugin' ),
                "param_name"  => "directory_map_filter",
                "value"       => array(
                    __( "Yes", 'swift-framework-plugin' ) => "yes",
                    __( "No", 'swift-framework-plugin' )  => "no"
                    
                ),
                "buttonset_on"  => "yes",
                "description" => __( "If yes, will be added a filter to refine the results.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Map Filter Position", 'swift-framework-plugin' ),
                "param_name"  => "directory_map_filter_pos",
                "value"       => array(
                    __( "Above", 'swift-framework-plugin' ) => "above",
                    __( "Below", 'swift-framework-plugin' ) => "below"
                ),
                "required"       => array("directory_map_filter", "=", "yes"),
                "description" => __( "Choose the position of the Map Filter(above or below).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Display Results", 'swift-framework-plugin' ),
                "param_name"  => "directory_map_results",
                "value"       => array(
                    __( "Map", 'swift-framework-plugin' )        => "map",
                    __( "List", 'swift-framework-plugin' )       => "list",
                    __( "Map & List", 'swift-framework-plugin' ) => "maplist"
                ),
                "description" => __( "Choose how the results will be displayed.", 'swift-framework-plugin' )
            ),
             array(
                "type"        => "textfield",
                "heading"     => __( "Excerpt Length", 'swift-framework-plugin' ),
                "param_name"  => "excerpt_length",
                "value"       => "",
                "description" => __( "The length of the excerpt for the list results text(leave empty for full description.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "class"       => "",
                "heading"     => __( "Number of items", 'swift-framework-plugin' ),
                "param_name"  => "item_count",
                "value"       => "12",
                "description" => __( "The number of directory items to show per page. Leave blank to show ALL directory items.", 'swift-framework-plugin' )
            ),
           array(
                "type"        => "buttonset",
                "heading"     => __( "Pagination", 'swift-framework-plugin' ),
                "param_name"  => "pagination",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "description" => __( "Show directory pagination.", 'swift-framework-plugin' )
            ),
           array(
                "type"        => "dropdown",
                "heading"     => __( "Map Type", 'swift-framework-plugin' ),
                "param_name"  => "type",
                "value"       => array(
                    __( "Map", 'swift-framework-plugin' )       => "roadmap",
                    __( "Satellite", 'swift-framework-plugin' ) => "satellite",
                    __( "Hybrid", 'swift-framework-plugin' )    => "hybrid",
                    __( "Terrain", 'swift-framework-plugin' )   => "terrain"
                ),
                "description" => __( "Select map display type. NOTE, if you set a color below, then only the standard Map type will show.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "dropdown",
                "heading"    => __( "Map Zoom", 'swift-framework-plugin' ),
                "param_name" => "zoom",
                "value"      => array(
                    __( "14 - Default", 'swift-framework-plugin' ) => 14,
                    1,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7,
                    8,
                    9,
                    10,
                    11,
                    12,
                    13,
                    15,
                    16,
                    17,
                    18,
                    19,
                    20
                ),
                 "description" => __( "This zoom field will only work for 1 map pin. When displaying more than 1 result the zoom will be calculate based on the pins of the map.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Map Color", 'swift-framework-plugin' ),
                "param_name"  => "color",
                "value"       => "",
                "description" => __( 'If you would like, you can enter a hex color here to style the map by changing the hue.', 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Map Saturation", 'swift-framework-plugin' ),
                "param_name"  => "saturation",
                "value"       => array(
                    __( "Color", 'swift-framework-plugin' )        => "color",
                    __( "Mono (Light)", 'swift-framework-plugin' ) => "mono-light",
                    __( "Mono (Dark)", 'swift-framework-plugin' )  => "mono-dark"
                ),
                "description" => __( "Set whether you would like the map to be in color or mono (black/white).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Fullscreen Display", 'swift-framework-plugin' ),
                "param_name"  => "fullscreen",
                "value"       => array(
                    __( "Yes", 'swift-framework-plugin' ) => "yes",
                    __( "No", 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "no",
                "description" => __( "If yes, the map will be displayed from screen edge to edge.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        ),

    ) );