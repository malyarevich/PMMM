<?php

    /*
    *
    *	Swift Page Builder - GoPricing Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_gopricing extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'        => '',
                "pricing_table" => '',
                'el_position'   => '',
                'width'         => '1/1',
                'el_class'      => ''
            ), $atts ) );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_gopricing_widget spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $widget_title, '' ) : '';
            if ( $pricing_table != "" ) {
                $output .= "\n\t\t" . do_shortcode( '[go_pricing id="' . $pricing_table . '"]' );
            }
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_gopricing', array(
        "name"   => __( "Go Pricing Table", 'swift-framework-plugin' ),
        "base"   => "spb_gopricing",
        "class"  => "spb_gopricing",
        "icon"   => "spb-icon-gopricing",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown-id",
                "heading"     => __( "Go Pricing Table", 'swift-framework-plugin' ),
                "param_name"  => "pricing_table",
                "value"       => sf_gopricing_list(),
                "description" => __( "Select the Pricing Table that you wish to show. These can be added in the Go Pricing admin area.", 'swift-framework-plugin' )
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