<?php

    /*
    *
    *	Swift Page Builder - Revolution Slider Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_slider extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                "revslider_shortcode"   => '',
                "layerslider_shortcode" => '',
                'masterslider_id'       => '',
                'el_position'           => '',
                'fullwidth'             => '',
                'width'                 => '1/1',
                'el_class'              => ''
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

            $output .= "\n\t" . '<div class="spb_slider_widget spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content spb_wrapper slider-wrap">';
            if ( $revslider_shortcode != "" ) {
                $output .= "\n\t\t\t\t" . sf_return_slider( $revslider_shortcode );
            } else if ( $layerslider_shortcode != "" ) {
                $output .= "\n\t\t\t\t" . do_shortcode( '[layerslider id="' . $layerslider_shortcode . '"]' );
            } else if ( $masterslider_id != "" ) {
                $output .= "\n\t\t\t\t" . do_shortcode( '[masterslider id="' . $masterslider_id . '"]' );
            }
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            return $output;

        }
    }

    SPBMap::map( 'spb_slider', array(
        "name"   => __( "Rev/Layer/Master Slider", 'swift-framework-plugin' ),
        "base"   => "spb_slider",
        "class"  => "spb_revslider spb_tab_media",
        "icon"   => "icon-slider",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Revolution Slider Alias", 'swift-framework-plugin' ),
                "param_name"  => "revslider_shortcode",
                "value"       => "",
                "description" => __( "Enter the Revolution Slider alias here for the one that you wish to show. This can be found within the Revolution Slider Admin Panel.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "LayerSlider ID", 'swift-framework-plugin' ),
                "param_name"  => "layerslider_shortcode",
                "value"       => "",
                "description" => __( "Enter the LayerSlider ID here for the one that you wish to show. This can be found within the LayerSlider Admin Panel.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Master Slider ID", 'swift-framework-plugin' ),
                "param_name"  => "masterslider_id",
                "value"       => "",
                "description" => __( "Enter the Master Slider ID here for the one that you wish to show. This can be found within the Master Slider Admin Panel.", 'swift-framework-plugin' )
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
                "description" => __( "Select if you'd like the slide to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
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
