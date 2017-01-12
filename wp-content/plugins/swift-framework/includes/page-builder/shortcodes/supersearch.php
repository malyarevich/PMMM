<?php

    /*
    *
    *	Swift Page Builder - SuperSearch Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_supersearch extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $contained = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'       => '',
                'fullwidth'   => '',
                'el_position' => '',
                'width'       => '1/1',
                'el_class'    => ''
            ), $atts ) );


            /* PAGE BUILDER OUTPUT
            ================================================== */
            $width    = spb_translateColumnWidthToSpan( $width );
            if ( $fullwidth == "yes" && $width == "col-sm-12" ) {
                $fullwidth = true;
                $contained = "true";
            } else {
                $fullwidth = false;
                $contained = "false";
            }
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            $output .= "\n\t" . '<div class="spb_supersearch_widget spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '', $fullwidth ) : '';
            $output .= "\n\t\t" . do_shortcode( '[sf_supersearch contained="'.$contained.'"]' );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            return $output;

        }
    }

    SPBMap::map( 'spb_supersearch', array(
        "name"   => __( "Super Search", 'swift-framework-plugin' ),
        "base"   => "spb_supersearch",
        "class"  => "spb_supersearch spb_tab_ui",
        "icon"   => "icon-super-search",
        "params" => array(
            array(
                "type"        => "buttonset",
                "heading"     => __( "Full width", 'swift-framework-plugin' ),
                "param_name"  => "fullwidth",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "std" => "no",
                "description" => __( "Select yes if you'd like the divider to be full width (only to be used with no sidebars, and with Standard/Thin/Dotted divider types).", 'swift-framework-plugin' )
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
