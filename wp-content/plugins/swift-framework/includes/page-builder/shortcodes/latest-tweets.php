<?php

    /*
    *
    *	Swift Page Builder - Latest Tweets Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_latest_tweets extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $tweets_count = $pb_margin_bottom = $pb_border_bottom = $pb_border_top = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'el_position'      => '',
                'tweets_count'     => '1',
                'pb_margin_bottom' => 'no',
                'pb_border_bottom' => 'no',
                'pb_border_top'    => 'no',
                'width'            => '1/1',
                'twitter_username' => '',
                'el_class'         => ''
            ), $atts ) );

            if ( $pb_margin_bottom == "yes" ) {
                $el_class .= ' pb-margin-bottom';
            }
            if ( $pb_border_bottom == "yes" ) {
                $el_class .= ' pb-border-bottom';
            }
            if ( $pb_border_top == "yes" ) {
                $el_class .= ' pb-border-top';
            }

            $tweet_output = sf_get_tweets( $twitter_username, $tweets_count );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_latest_tweets_widget ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content spb_wrapper latest-tweets-wrap clearfix">';
            $output .= "\n\t\t\t" . '<div class="twitter-bird"><i class="fa-twitter"></i></div><ul class="tweet-wrap">' . $tweet_output . "</ul>";
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_latest_tweets', array(
        "name"   => __( "Tweets", 'swift-framework-plugin' ),
        "base"   => "spb_latest_tweets",
        "class"  => "spb-latest-tweets spb_tab_media",
        "icon"   => "icon-latest-tweets",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Twitter username", 'swift-framework-plugin' ),
                "param_name"  => "twitter_username",
                "value"       => "",
                "description" => __( "The twitter username you'd like to show the latest tweet for. Make sure to not include the @.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Number of Tweets", 'swift-framework-plugin' ),
                "param_name"  => "tweets_count",
                "value"       => "1",
                "description" => __( "The number of tweets you'd like to show.", 'swift-framework-plugin' )
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
