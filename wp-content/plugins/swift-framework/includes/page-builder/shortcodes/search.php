<?php

    /*
    *
    *	Swift Page Builder - Search Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_search extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $input_class = $el_class = $output = $post_type = $search_form = $el_position = '';

            extract( shortcode_atts( array(
                'el_position'       => '',
                'search_input_text' => '',
                'input_size'        => 'standard',
                'post_type'         => '',
                'width'             => '1/1',
                'twitter_username'  => '',
                'el_class'          => ''
            ), $atts ) );

            if ( $input_size == "large" ) {
                $input_class = 'input-large';
            } else {
                $input_class = 'input-standard';
            }

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $search_form .= '<form method="get" class="search-form search-widget" action="' . get_home_url() . '/">';
            $search_form .= '<input type="text" placeholder="' . $search_input_text . '" name="s" class="' . $input_class . '" />';
            if ( $post_type != "any" ) {
                $search_form .= '<input type="hidden" name="post_type" value="' . $post_type . '" />';
            }
            $search_form .= '</form>';

            $output .= "\n\t" . '<div class="spb_search_widget spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t" . $search_form;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_search', array(
        "name"   => __( "Search", 'swift-framework-plugin' ),
        "base"   => "spb_search",
        "class"  => "spb_search spb_tab_ui",
        "icon"   => "icon-search-element",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Input placeholder text", 'swift-framework-plugin' ),
                "param_name"  => "search_input_text",
                "value"       => "Search",
                "description" => __( "Enter the text that appearas as default in the search input.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Search box size", 'swift-framework-plugin' ),
                "param_name"  => "input_size",
                "value"       => array(
                    __( 'Standard', 'swift-framework-plugin' ) => "standard",
                    __( 'Large', 'swift-framework-plugin' )    => "large"
                ),
                "description" => __( "Set the size for the search input box.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Search results post type", 'swift-framework-plugin' ),
                "param_name"  => "post_type",
                "value"       => array(
                    __( 'All', 'swift-framework-plugin' ) => "any",
                    __( 'Products', 'swift-framework-plugin' )    => "product"
                ),
                "description" => __( "Set whether you would like the site search limited to products, or all content.", 'swift-framework-plugin' )
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
