<?php

    /*
    *
    *	Swift Page Builder - Sidebar Widget Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_widget_area extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $output = $el_position = $sidebar_id = $title = $width = $el_class = $sidebar_id = '';

            extract( shortcode_atts( array(
                'el_position' => '',
                'width'       => '1/1',
                'el_class'    => '',
                'sidebar_id'  => ''
            ), $atts ) );

            if ( $sidebar_id == '' ) {
                echo 'Sidebar ID not set.';

                return null;
            }

            $el_class = $this->getExtraClass( $el_class );

            ob_start();
            dynamic_sidebar( $sidebar_id );
            $sidebar_value = ob_get_contents();
            ob_end_clean();

            $sidebar_value = trim( $sidebar_value );
            $sidebar_value = ( substr( $sidebar_value, 0, 3 ) == '<li' ) ? '<ul>' . $sidebar_value . '</ul>' : $sidebar_value;//

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_widget_area spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t" . $sidebar_value;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    SPBMap::map( 'spb_widget_area', array(
            "name"   => __( "Widget Area", 'swift-framework-plugin' ),
            "base"   => "spb_widget_area",
            "class"  => "spb_widget_area spb_tab_layout",
            "icon"   => "icon-widget-area",
            "params" => array(
                array(
                    "type"        => "widgetised_sidebars",
                    "heading"     => __( "Select Sidebar:", 'swift-framework-plugin' ),
                    "param_name"  => "sidebar_id",
                    "value"       => "Sidebar",
                    "description" => __( "Select an existing sidebar to add to the page.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                    "param_name"  => "el_class",
                    "value"       => "",
                    "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
                )
            )
        )
    );
