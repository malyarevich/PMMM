<?php

    /*
    *
    *	Swift Page Builder - Animated Headline Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_animated_headline extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $headline_class = $el_position = '';

            extract( shortcode_atts( array(
                'before_text'       => '',
                'after_text'        => '',
                'animated_strings'  => '',
                'animation_type'    => 'rotate-1',
                'textalign'   => 'left',
                'textstyle'   => 'h3',
                'textcolor'   => '',
                'width'       => '1/1',
                'el_position' => '',
                'el_class'    => ''
            ), $atts ) );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            $animation_type = str_replace( "_", " ", $animation_type );
            $inline_style = "";

            if ( $textcolor != "" ) {
                $inline_style .= 'color:' . $textcolor . ';';
            }

            if ( $textstyle == "impact-text" ) {
                $textstyle = "div";
                $headline_class = "impact-text";
            }

            if ( $textstyle == "impact-text-large" ) {
                $textstyle = "div";
                $headline_class = "impact-text-large";
            }

            $output .= "\n\t" . '<div class="spb-animated-headline spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t\t" . '<' . $textstyle . ' class="sf-headline text-' . $textalign . ' ' . $animation_type . ' ' . $headline_class . '" style="' . $inline_style . '">';
            if ( $before_text ) {
            $output .= "\n\t\t\t\t" .  $before_text;
            }
            $output .= "\n\t\t\t\t" . '<span class="sf-words-wrapper">';
                $strings_array = explode( ',' , $animated_strings );
                $i = 0;
                foreach( $strings_array as $string ) {
                    if ( $i == 0 ) {
                        $output .= "\n\t\t\t\t\t" . '<b class="is-visible">' . $string . '</b>';
                    } else {
                        $output .= "\n\t\t\t\t\t" . '<b>' . $string . '</b>';
                    }
                    $i++;
                }
            $output .= "\n\t\t\t\t" . '</span>';
            if ( $after_text != "" ) {
            $output .= "\n\t\t\t\t" . $after_text;
            }
            $output .= "\n\t\t\t" . '</' . $textstyle . '>';
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_animated_headline', array(
        "name"   => __( "Animated Headline", 'swift-framework-plugin' ),
        "base"   => "spb_animated_headline",
        "class"  => "spb_animated_headline spb_tab_media",
        "icon"   => "icon-animated-headline",
        "params" => array(
            array(
                "type"        => "textfield",
                "holder"      => "div",
                "heading"     => __( "Before Text", 'swift-framework-plugin' ),
                "param_name"  => "before_text",
                "value"       => "",
                "description" => __( "The text which you would like to display before the animated strings.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "holder"      => "div",
                "heading"     => __( "Animated Strings", 'swift-framework-plugin' ),
                "param_name"  => "animated_strings",
                "value"       => "",
                "description" => __( "Provide the text that you wish to animate here - this should be comma delimited e.g. 'pizza,pasta,bread'.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "holder"      => "div",
                "heading"     => __( "After Text", 'swift-framework-plugin' ),
                "param_name"  => "after_text",
                "value"       => "",
                "description" => __( "The text which you would like to display after the animated strings.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Animation Type", 'swift-framework-plugin' ),
                "param_name"  => "animation_type",
                "value"       => array(
                    "Rotate (1)"  => "rotate-1",
                    "Rotate (2)"  => "letters rotate-2",
                    "Rotate (3)"  => "letters rotate-3",
                    "Type"   => "letters type",
                    "Loading Bar"   => "loading-bar",
                    "Slide"   => "slide",
                    "Clip"   => "clip",
                    "Zoom"   => "zoom",
                    "Scale" => "letters scale",
                    "Push" => "push"
                ),
                "description" => __( "Choose the animation type.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Text Style", 'swift-framework-plugin' ),
                "param_name"  => "textstyle",
                "value"       => array(
                    "H1"   => "h1",
                    "H2"   => "h2",
                    "H3"   => "h3",
                    "H4"   => "h4",
                    "H5"   => "h5",
                    "H6"   => "h6",
                    "Body" => "div",
                    "Impact Text" => "impact-text",
                    "Impact Text (Large)" => "impact-text-large"
                ),
                "description" => __( "Choose the text style.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Text Align", 'swift-framework-plugin' ),
                "param_name"  => "textalign",
                "value"       => array(
                    "Left"   => "left",
                    "Center"   => "center",
                    "Right"   => "right",
                ),
                "description" => __( "Choose the text align.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Text Color", 'swift-framework-plugin' ),
                "param_name"  => "textcolor",
                "value"       => "",
                "description" => __( "Select a colour for the text here.", 'swift-framework-plugin' )
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
