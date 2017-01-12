<?php

    /*
    *
    *	Swift Page Builder - Campaigns Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_campaigns extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $output = $gutters = $fullwidth = $exclude_categories = $show_read_more = $offset = $posts_order = $content_output = $items = $item_figure = $el_position = '';

            extract( shortcode_atts( array(
                'title'              => '',
                "fullwidth"          => "no",
                "columns"            => '4',
                "item_count"         => '5',
                "category"           => '',
                "pagination"         => "no",
                "social_integration" => 'no',
                'el_position'        => '',
                'width'              => '1/1',
                'el_class'           => ''
            ), $atts ) );

            $width = spb_translateColumnWidthToSpan( $width );

            // Enqueue script
            wp_enqueue_script( 'isotope' );
            

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $fullwidth == "yes" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }


            /* CATEGORY SLUG MODIFICATION
            ================================================== */
            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );


            /* CAMPAIGN QUERY SETUP
            ================================================== */
            global $post, $wp_query;

            if ( get_query_var( 'paged' ) ) {
                $paged = get_query_var( 'paged' );
            } elseif ( get_query_var( 'page' ) ) {
                $paged = get_query_var( 'page' );
            } else {
                $paged = 1;
            }

            $campaign_args = array(
                'post_type'      => 'download',
                'post_status'    => 'publish',
                'paged'          => $paged,
                'download_category'  => $category_slug,
                'posts_per_page' => $item_count,
            );

            $campaign_items = new WP_Query( $campaign_args );

            $item_class = "";

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


            /* CAMPAIGN ITEMS
            ================================================== */
            $items .= '<ul class="campaign-items masonry-items clearfix">';
            while ( $campaign_items->have_posts() ) : $campaign_items->the_post();

                $items .= sf_get_campaign_item( $item_class );

            endwhile;
            $items .= '</ul>';


            /* FINAL OUTPUT
            ================================================== */
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            $output .= "\n\t" . '<div class="spb_campaigns_widget campaigns-wrap spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $widget_title, '', $fullwidth ) : '';
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

    SPBMap::map( 'spb_campaigns', array(
        "name"   => __( "Campaigns", 'swift-framework-plugin' ),
        "base"   => "spb_campaigns",
        "class"  => "spb_campaigns",
        "icon"   => "spb-icon-campaigns",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Full Width", 'swift-framework-plugin' ),
                "param_name"  => "fullwidth",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "std" => "no",
                "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Column count", 'swift-framework-plugin' ),
                "param_name"  => "columns",
                "value"       => array( "5", "4", "3", "2", "1" ),
                "description" => __( "How many columns to display.'", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "class"       => "",
                "heading"     => __( "Number of items", 'swift-framework-plugin' ),
                "param_name"  => "item_count",
                "value"       => "5",
                "description" => __( "The number of campaigns to show per page.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "select-multiple",
                "heading"     => __( "Campaigns category", 'swift-framework-plugin' ),
                "param_name"  => "category",
                "value"       => sf_get_category_list( 'download_category' ),
                "description" => __( "Choose the category for the campaigns.", 'swift-framework-plugin' )
            ),
            array(
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