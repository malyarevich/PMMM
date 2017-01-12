<?php

    /*
    *
    *	Swift Page Builder - SPB Template Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_section extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $size = $width = $spb_section_id = $el_position = $el_class = '';
            extract( shortcode_atts( array(
                'width'          => '1/1',
                'spb_section_id' => '',
                'el_position'    => '',
                'el_class'       => ''
            ), $atts ) );
            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            $size     = str_replace( array( 'px', ' ' ), array( '', '' ), $size );

            if ( $spb_section_id == "" ) {
                return;
            }

            $content_section = get_post( $spb_section_id );

            $output .= "\n\t" . '<div class="spb-section spb_content_element ' . $width . $el_class . '">';
            if ( isset( $content_section->post_content ) ) {
            	$content = $content_section->post_content;
            	$content = apply_filters( 'the_content', $content );
                $output .= "\n\t\t\t" . do_shortcode( $content );
            }
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            return $output;
        }

    }

    SPBMap::map( 'spb_section', array(
        "name"     => __( "SPB Section", 'swift-framework-plugin' ),
        "base"     => "spb_section",
        "controls" => "full",
        "class"    => "spb_section spb_tab_layout",
        "icon"     => "icon-spb",
        "params"   => array(
            array(
	            "type"        => "dropdown",
	            "heading"     => __( "SPB Section", 'swift-framework-plugin' ),
	            "param_name"  => "spb_section_id",
	            "value"       => sf_list_spb_sections(),
	            "description" => __( "Choose the SPB Section to display.", 'swift-framework-plugin' )
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
