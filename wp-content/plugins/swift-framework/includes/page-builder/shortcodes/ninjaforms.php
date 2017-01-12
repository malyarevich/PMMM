<?php

    /*
    *
    *	Swift Page Builder - NinjaForms Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_ninjaforms extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                "ninja_form"  => '',
                'el_position' => '',
                'width'       => '1/1',
                'el_class'    => ''
            ), $atts ) );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_ninjaforms_widget spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $ninja_form != "" ) {
                $output .= "\n\t\t" . do_shortcode( '[ninja_forms_display_form id=' . $ninja_form . ']' );
            }
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_ninjaforms', array(
        "name"   => __( "Ninja Forms", 'swift-framework-plugin' ),
        "base"   => "spb_ninjaforms",
        "class"  => "spb_ninjaforms",
        "icon"   => "spb-icon-ninjaforms",
        "params" => array(
            array(
                "type"        => "dropdown-id",
                "heading"     => __( "Nina Form", 'swift-framework-plugin' ),
                "param_name"  => "ninja_form",
                "value"       => sf_ninjaforms_list(),
                "description" => __( "Select the Gravity Form instance that you wish to show.", 'swift-framework-plugin' )
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