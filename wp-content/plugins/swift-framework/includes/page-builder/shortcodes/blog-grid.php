<?php

    /*
    *
    *	Swift Page Builder - Blog Grid Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_blog_grid extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $output = $items_output = $el_position = '';

            extract( shortcode_atts( array(
                'title'            => '',
                'fullwidth'        => 'yes',
                "item_count"       => '5',
                "standard_columns" => '3',
                "instagram"     => '',
                "twitter_username" => '',
                'el_position'      => '',
                'width'            => '1/1',
                'el_class'         => ''
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

            /* COUNT CALC
            ================================================== */
            $item_class = "col-sm-4";
            if ( $fullwidth ) {
                $item_class = "col-sm-sf-5";
                $item_count = $item_count * 5;
            } else {
                if ( $standard_columns == '5' ) {
                    $item_class = "col-sm-sf-5";
                    $item_count = $item_count * 5;
                } else {
                    $item_class = "col-sm-4";
                    $item_count = $item_count * 3;
                }
            }

            $tweet_count = $instagram_count = floor( $item_count / 2 );

            if ( $tweet_count + $instagram_count < $item_count ) {
                $tweet_count = $item_count - $instagram_count;
            }

            if ( $twitter_username == "" ) {
                $tweet_count     = 0;
                $instagram_count = $item_count;
            }
            if ( $instagram != "yes" ) {
                $instagram_count = 0;
                $tweet_count     = $item_count;
            }


            /* ITEMS OUTPUT
            ================================================== */
            $items_output .= '<div class="blog-items blog-grid-items">';
            $items_output .= '<ul class="grid-items row clearfix">';
            $items_output .= '</ul>';


            /* TWEETS
            ================================================== */
            if ( $twitter_username != "" ) {
                $items_output .= '<ul class="blog-tweets" data-username="' . $twitter_username . '" data-count="' . $tweet_count .  '">' . sf_get_tweets( $twitter_username, $tweet_count, 'blog-grid', $item_class ) . '</ul>';
            }


            /* INSTAGRAMS
            ================================================== */
            $instagram_token = get_option('sf_instagram_access_token');
            $instagram_id = get_option('sf_instagram_user_id');

            if ( $instagram_id != "" && $instagram_token != "" ) {
                $items_output .= '<ul class="blog-instagrams" data-title="' . __( "Instagram", 'swift-framework-plugin' ) . '" data-count="' . $instagram_count . '" data-userid="' . $instagram_id . '" data-token="' . $instagram_token . '" data-itemclass="'.$item_class.'"></ul>';
            }


            $items_output .= '</div>';


            /* FINAL OUTPUT
            ================================================== */
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            $output .= "\n\t" . '<div class="spb_blog_grid_widget blog-wrap spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '', $fullwidth ) : '';
            $output .= "\n\t\t" . $items_output;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            global $sf_has_blog, $sf_include_imagesLoaded;
            $sf_include_imagesLoaded = true;
            $sf_has_blog             = true;

            return $output;

        }
    }

    SPBMap::map( 'spb_blog_grid', array(
        "name"   => __( "Social Grid", 'swift-framework-plugin' ),
        "base"   => "spb_blog_grid",
        "class"  => "spb_blog_grid spb_tab_media",
        "icon"   => "icon-social-grid",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "class"       => "",
                "heading"     => __( "Number of rows", 'swift-framework-plugin' ),
                "param_name"  => "item_count",
                "value"       => apply_filters( 'sf_blog_grid_item_counts', array( "1", "2", "3", "4", "5" ) ),
                "description" => __( "The number of grid rows to show.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "class"       => "",
                "heading"     => __( "Standard Width Columns", 'swift-framework-plugin' ),
                "param_name"  => "standard_columns",
                "value"       => apply_filters( 'sf_blog_grid_standard_item_columns', array( "3", "5" ) ),
                "description" => __( "The number of columns to show, when not using Full Width mode.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Twitter Username", 'swift-framework-plugin' ),
                "param_name"  => "twitter_username",
                "value"       => "",
                "description" => __( "Enter your twitter username here to include tweets in the blog grid. Ensure you have the Twitter oAuth plugin installed and your details added.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Include Instagram", 'swift-framework-plugin' ),
                "param_name"  => "instagram",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Select yes if you'd like to include instagram photos within the blog grid. If you haven't already, you'll need to set up your Instagram account in Swift Framework > Instagram Auth.", 'swift-framework-plugin' )
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
                "std" => "no",
                "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
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
