<?php

    /*
    *
    *	Swift Page Builder - Gravity Forms Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_gravityforms extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                "grav_form"   => '',
                "show_title"  => '',
                "show_desc"   => '',
                "ajax"        => '',
                'el_position' => '',
                'width'       => '1/1',
                'el_class'    => ''
            ), $atts ) );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_gravityforms_widget spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $grav_form != "" ) {
                $output .= "\n\t\t" . do_shortcode( '[gravityform id="' . $grav_form . '" title="' . $show_title . '" description="' . $show_desc . '" ajax="' . $ajax . '"]' );
            }
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_gravityforms', array(
        "name"   => __( "Gravity Forms", 'swift-framework-plugin' ),
        "base"   => "spb_gravityforms",
        "class"  => "spb_gravityforms",
        "icon"   => "spb-icon-gravityforms",
        "params" => array(
            array(
                "type"        => "dropdown-id",
                "heading"     => __( "Gravity Form", 'swift-framework-plugin' ),
                "param_name"  => "grav_form",
                "value"       => sf_gravityforms_list(),
                "description" => __( "Select the Gravity Form instance that you wish to show.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Show form title", 'swift-framework-plugin' ),
                "param_name"  => "show_title",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Show the form's title", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Show form description", 'swift-framework-plugin' ),
                "param_name"  => "show_desc",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Show the form's description", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",  
                "heading"     => __( "Enable AJAX", 'swift-framework-plugin' ),
                "param_name"  => "ajax",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Enable AJAX functionality for the form.", 'swift-framework-plugin' )
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