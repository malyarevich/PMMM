<?php

    /*
    *
    *   Swift Page Builder - Progress Bar Shortcode
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_progress_bar extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'percentage'    => '',
                'text'          => '',
                'value'         => '',
                'colour'        => '#222',
                'width'         => '1/1',
                'el_position'   => '',
                'el_class'      => '',
                'width'         => '1/1'
            ), $atts ) );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_progress_bar spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t\t" . do_shortcode( '[sf_progress_bar percentage="' . $percentage . '" name="' . $text . '" value="' . $value . '" type="standard" colour="' . $colour . '"]' );
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
                    "heading"     => __( "Bar Percentage", 'swift-framework-plugin' ),
                    "param_name"  => "percentage",
                    "value"       => "10",
                    "description" => __( "The percentage of the progress bar.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Progress Text", 'swift-framework-plugin' ),
                    "param_name"  => "text",
                    "value"       => "",
                    "description" => __( "Enter the text that you'd like shown above the bar, i.e. 'COMPLETED'.
", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Progress Value", 'swift-framework-plugin' ),
                    "param_name"  => "value",
                    "value"       => "10%",
                    "description" => __( "Enter value that you'd like shown at the end of the bar on completion, i.e. '90%'", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "colorpicker",
                    "heading"     => __( "Bar Colour", 'swift-framework-plugin' ),
                    "param_name"  => "colour",
                    "value"       => "",
                    "description" => __( "Select a colour for the bar.", 'swift-framework-plugin' )
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
    SPBMap::map( 'spb_progress_bar', array(
        "name"   => __( "Progress Bar", 'swift-framework-plugin' ),
        "base"   => "spb_progress_bar",
        "class"  => "spb_progress_bar",
        "icon"   => "icon-progres-bars-v5",
        "params" => $params,
    ) );
