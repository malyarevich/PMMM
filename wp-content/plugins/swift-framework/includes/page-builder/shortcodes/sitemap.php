<?php

    /*
    *
    *	Swift Page Builder - Sitemap Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_sitemap extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $output = $el_class = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'       => '',
                'el_class'    => '',
                'el_position' => '',
                'width'       => '1/2'
            ), $atts ) );

            $sitemap = '[sf_sitemap]';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_content_element ' . $width . $el_class . '">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '' ) : '';
            $output .= "\n\t\t" . do_shortcode( $sitemap );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            //
            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    SPBMap::map( 'spb_sitemap', array(
        "name"          => __( "Sitemap", 'swift-framework-plugin' ),
        "base"          => "spb_sitemap",
        "class"         => "spb_tab_ui",
        "icon"          => "icon-sitemap",
        "wrapper_class" => "clearfix",
        "controls"      => "full",
        "params"        => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
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
