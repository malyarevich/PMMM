<?php

    /*
    *
    *	Swift Page Builder - Swift Slider Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_swift_slider extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'category'    => '',
                'fullscreen'  => 'false',
                'maxheight'   => '',
                'slidecount'  => '',
                'autoplay'    => '',
                'transition'  => '',
                'loop'        => '',
                'nav'         => 'true',
                'pagination'  => 'true',
                'continue'    => 'true',
                'el_position' => '',
                'fullwidth'   => '',
                'width'       => '1/1',
                'el_class'    => ''
            ), $atts ) );

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $fullwidth == "yes" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }

            $el_class = $this->getExtraClass( $el_class, $fullwidth );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_swift-slider spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t\t" . do_shortcode( '[swift_slider type="slider" category="' . $category . '" fullscreen="' . $fullscreen . '" max_height="' . $maxheight . '" slide_count="' . $slidecount . '" transition="'.$transition.'" loop="' . $loop . '" nav="' . $nav . '" pagination="' . $pagination . '" autoplay="' . $autoplay . '" continue="' . $continue . '"]' );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            global $sf_has_swiftslider;
            $sf_has_swiftslider = true;

            return $output;

        }
    }

    SPBMap::map( 'spb_swift_slider', array(
        "name"   => __( "Swift Slider", 'swift-framework-plugin' ),
        "base"   => "spb_swift_slider",
        "class"  => "spb_swiftslider spb_tab_media",
        "icon"   => "icon-swift-slider",
        "params" => array(
            array(
                "type"        => "buttonset",
                "heading"     => __( "Fullscreen", 'swift-framework-plugin' ),
                "param_name"  => "fullscreen",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "true",
                "description" => __( "Choose if you would like the slider to be window height.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Slider Max Height", 'swift-framework-plugin' ),
                "param_name"  => "maxheight",
                "value"       => "600",
                "description" => __( "Set the maximum height that the Swift Slider should display at (optional) (no px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Slide Count", 'swift-framework-plugin' ),
                "param_name"  => "slidecount",
                "value"       => "5",
                "description" => __( "Set the number of slides to show. If you'd like to show all, then please set this to -1.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "select-multiple",
                "heading"     => __( "Slide category", 'swift-framework-plugin' ),
                "param_name"  => "category",
                "value"       => sf_get_category_list( 'swift-slider-category' ),
                "description" => __( "Choose the category of slide that you would like to show, or all.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Autoplay", 'swift-framework-plugin' ),
                "param_name"  => "autoplay",
                "value"       => "",
                "description" => __( "If you would like the slider to auto-rotate, then set the autoplay rotate time in ms here. I.e. you would enter '5000' for the slider to rotate every 5 seconds.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Transition", 'swift-framework-plugin' ),
                "param_name"  => "transition",
                "value"       => array(
                    __( 'Slide', 'swift-framework-plugin' ) => "slide",
                    __( 'Fade', 'swift-framework-plugin' )  => "fade"
                ),
                "description" => __( "Select the transition between slides.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Loop", 'swift-framework-plugin' ),
                "param_name"  => "loop",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "true",
                "description" => __( "Select if you'd like the slider to loop.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Navigation", 'swift-framework-plugin' ),
                "param_name"  => "nav",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "true",
                "description" => __( 'Choose if you would like to display the left/right arrows on the slider.', 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Pagination", 'swift-framework-plugin' ),
                "param_name"  => "pagination",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "true",
                "description" => __( "Choose if you would like to display the slider pagination.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Continue", 'swift-framework-plugin' ),
                "param_name"  => "continue",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "true",
                "description" => __( "Choose if you would like to display the continue button on the slider to progress to the content. If you want to only display the slider on the page, and no content, then make sure you set this to NO.", 'swift-framework-plugin' )
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
                "description" => __( "Select if you'd like the slider to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
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
