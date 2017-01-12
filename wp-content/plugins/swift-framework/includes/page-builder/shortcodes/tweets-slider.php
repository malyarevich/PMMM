<?php

    /*
    *
    *	Swift Page Builder - Tweets Slider Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_tweets_slider extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $order = $items = $el_class = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'            => '',
                'twitter_username' => '',
                'tweets_count'     => '6',
                'animation'        => 'fade',
                'autoplay'         => 'yes',
                'el_class'         => '',
                'el_position'      => '',
                'width'            => '1/1'
            ), $atts ) );

            // ENQUEUE SCRIPT
            wp_enqueue_script('lightSlider');


            $output = '';

            if ( $autoplay == "yes" ) {
                $items .= '<div class="flexslider tweets-slider content-slider" data-animation="' . $animation . '" data-autoplay="yes"><ul class="slides cS-hidden">';
            } else {
                $items .= '<div class="flexslider tweets-slider content-slider" data-animation="' . $animation . '" data-autoplay="no"><ul class="slides cS-hidden">';
            }

            $items .= sf_get_tweets( $twitter_username, $tweets_count );

            $items .= '</ul></div>';

            $sidebar_config = spb_get_post_meta( get_the_ID(), 'sf_sidebar_config', true );

            $sidebars = '';
            if ( ( $sidebar_config == "left-sidebar" ) || ( $sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $el_class .= ' testimonial';

            $output .= "\n\t" . '<div class="spb_tweets_slider_widget ' . $width . $el_class . '">';
            $output .= "\n\t" . '<div class="spb-bg-color-wrap">';
            $output .= "\n\t\t" . '<div class="spb-asset-content spb_wrapper slider-wrap">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . '<div class="heading-wrap"><h3 class="spb-heading spb-center-heading">' . $title . '</h3></div>' : '';
            if ( $title == "" ) {
            $output .= "\n\t\t\t" . '<div class="tweet-icon"><i class="fa-twitter"></i></div>';
            }
            $output .= "\n\t\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            if ( $sidebars == "no-sidebars" ) {
                $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );
            } else {
                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
            }

            global $sf_include_carousel;
            $sf_include_carousel = true;

            return $output;
        }
    }

    SPBMap::map( 'spb_tweets_slider', array(
        "name"          => __( "Tweets Slider", 'swift-framework-plugin' ),
        "base"          => "spb_tweets_slider",
        "class"         => "spb-tweets-slider spb_tab_media",
        "icon"          => "icon-tweet-slider",
        "wrapper_class" => "clearfix",
        "params"        => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Twitter username", 'swift-framework-plugin' ),
                "param_name"  => "twitter_username",
                "value"       => "",
                "description" => __( "The twitter username you'd like to show the latest tweet for. Make sure to not include the @.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "class"       => "",
                "heading"     => __( "Number of tweets", 'swift-framework-plugin' ),
                "param_name"  => "tweets_count",
                "value"       => "6",
                "description" => __( "The number of tweets to show.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Slider autoplay", 'swift-framework-plugin' ),
                "param_name"  => "autoplay",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Select if you want the slider to autoplay or not.", 'swift-framework-plugin' )
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
