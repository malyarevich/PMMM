<?php

    /*
    *
    *	Swift Page Builder - Chart Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_chart extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'percentage'     => '100',
                'chart_size'     => '70',
                'bar_colour'     => '#ff9900',
                'track_colour'   => '#f7f7f7',
                'chart_content'  => '',
                'chart_align'    => 'center',
                'el_position'    => '',
                'el_class'       => '',
                'width'          => '1/1'
            ), $atts ) );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            // Enqueue script
            wp_enqueue_script( 'easypiechart' );

            // Output
            $output .= "\n\t" . '<div class="spb_chart spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t\t" . do_shortcode( '[sf_chart percentage="' . $percentage . '" size="' . $chart_size . '" barcolour="' . $bar_colour . '" trackcolour="' . $track_colour . '" content="' . $chart_content . '" align="' . $chart_align . '"]' );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }



    /* PARAMS
    ================================================== */
    $params = array(
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Percentage", 'swift-framework-plugin' ),
                    "param_name"  => "percentage",
                    "value"       => "100",
                    "description" => __( "Enter the percentage of the chart value. NOTE: This must be between 0-100, numeric only.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Content", 'swift-framework-plugin' ),
                    "param_name"  => "chart_content",
                    "value"       => "",
                    "description" => __( "Enter the content for the center of the chart, i.e. a number or percentage. NOTE: if you'd like to include an icon here, just enter the icon class name, i.e. 'fa-magic'.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Chart Size", 'swift-framework-plugin' ),
                    "param_name"  => "chart_size",
                    "value"       => array(
                        "Standard"   => "70",
                        "Large"   => "170",
                    ),
                    "description" => __( "Chose the size of the chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "colorpicker",
                    "heading"     => __( "Bar Colour", 'swift-framework-plugin' ),
                    "param_name"  => "bar_colour",
                    "value"       => "",
                    "description" => __( "Select a colour for the bar on the chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "colorpicker",
                    "heading"     => __( "Track Colour", 'swift-framework-plugin' ),
                    "param_name"  => "track_colour",
                    "value"       => "",
                    "description" => __( "Select a colour for the track of the chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Chart Align", 'swift-framework-plugin' ),
                    "param_name"  => "chart_align",
                    "value"       => array(
                        "Left"   => "left",
                        "Center"   => "center",
                        "Right"   => "right",
                    ),
                    "description" => __( "Chose the alignment of the chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                    "param_name"  => "el_class",
                    "value"       => "",
                    "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
                )
            );

    /* SHORTCODE MAP
    ================================================== */  
    SPBMap::map( 'spb_chart', array(
        "name"   => __( "Chart", 'swift-framework-plugin' ),
        "base"   => "spb_chart",
        "class"  => "spb_chart",
        "icon"   => "icon-chart",
        "params" => $params,
    ) );
