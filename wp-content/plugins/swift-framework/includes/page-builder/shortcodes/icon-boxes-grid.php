<?php

    /*
    *
    *	Swift Page Builder - Icon Boxes Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_icon_box_grid extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $el_class = $text_color = $image_url = $image_object = $width = $el_position = '';

            extract( shortcode_atts( array(
                'columns'         => '3',
                'colour_style'    => 'dark',
                'el_class'        => '',
                'el_position'     => '',
                'width'           => '1/1'
            ), $atts ) );

            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_icon_box_grid ' . $width . $el_class . '" data-columns="'.$columns.'" data-colour="'.$colour_style.'">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t" . do_shortcode( $content );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            global $sf_include_carousel;
            $sf_include_carousel = true;

            return $output;
        }
    }

    if( get_option('sf_theme') == 'uplift' ) {

        SPBMap::map( 'spb_icon_box_grid', array(
        "name"          => __( "Icon Box Grid", 'swift-framework-plugin' ),
        "base"          => "spb_icon_box_grid",
        "class"         => "spb_tab_ui",
        "icon"          => "icon-icon-grid",
        "wrapper_class" => "clearfix",
        "params"        => array(
            array(
                "type"        => "dropdown",
                "heading"     => __( "Column count", 'swift-framework-plugin' ),
                "param_name"  => "columns",
                "value"       => array( "3", "4" ),
                "std"         => '3',
                "description" => __( "How many columns to display.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Colour Style", 'swift-framework-plugin' ),
                "param_name"  => "colour_style",
                "value"       => array(
                    __( 'Light', 'swift-framework-plugin' ) => "light",
                    __( 'Dark', 'swift-framework-plugin' )  => "dark"
                ),
                "std"         => 'dark',
                "description" => __( "Select the colour style for the text on the icon boxes.", 'swift-framework-plugin' )
            ),
             array(
                "type"        => "textarea_html",
                "holder"      => "div",
                "class"       => "",
                "heading"     => __( "Icon Sections", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "description" => __( "Enter your content.", 'swift-framework-plugin' ),
                "param_type"  => "repeater",
                "master"      => "spb_icon_box_grid",
            ),
            array(
                "type"       => "section",
                "param_name" => "ib_misc_options",
                "heading"    => __( "Misc Options", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
			),
		  
			
        )
        ) );
    }