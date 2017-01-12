<?php

    /*
    *
    *	Swift Page Builder - Icon Boxes Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_icon_box_grid_element extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $el_class = $text_color = $image_url = $image_object = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'           => '',
                'icon'            => '',
                'character'       => '',
                'image'           => '',
                'box_type'        => '',
                'icon_color'      => '',
                'icon_bg_color'   => '',
                'text_color'      => '',
                'bg_color'        => '',
                'flip_text_color' => '',
                'flip_bg_color'   => '',
                'animation'       => '',
                'animation_delay' => '',
                'link'            => '',
                'target'          => '_self',
                'el_class'        => '',
                'el_position'     => '',
                'width'           => '1/1'
            ), $atts ) );

            $has_image = false;
            $output = $icon_box = '';

            if ( $target == "" ) {
                $target = "_self";
            }

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_icon_box ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $link != "" ) {
                $output .= '<a class="box-link" href="' . $link . '" target="' . $target . '"></a>';
            }
            $output .= "\n\t\t\t" . '<div class="sf-icon-box-content-wrap clearfix">';
            $output .= "\n\t\t\t\t" . '<div class="sf-icon-box-inner-wrap clearfix">';
            $output .= "\n\t\t\t\t\t" . '<div class="grid-icon-wrap clearfix">' . do_shortcode( '[sf_icon size="medium" svg="' . $icon . '" character="' . $character . '" image="' . $image . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' ) . '</div>';
            $output .= "\n\t\t\t\t\t" . '<div class="divider-line"></div>';
            $output .= "\n\t\t\t\t\t" . '<h3>' . $title . '</h3>';
            $output .= "\n\t\t\t\t" . '</div>';
            $output .= "\n\t\t\t" . '</div>';
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            global $sf_include_carousel;
            $sf_include_carousel = true;

            return $output;
        }
    }

    $target_arr = array(
        __( "Same window", 'swift-framework-plugin' ) => "_self",
        __( "New window", 'swift-framework-plugin' )  => "_blank"
    );

    $icon_box_types = array(
        __( 'Standard', 'swift-framework-plugin' )                 => "standard",
        __( 'Standard with Title Icon', 'swift-framework-plugin' ) => "standard-title",
        __( 'Bold', 'swift-framework-plugin' )                     => "bold",
        __( 'Left Icon', 'swift-framework-plugin' )                => "left-icon",
        __( 'Left Icon Alt', 'swift-framework-plugin' )            => "left-icon-alt",
        __( 'Boxed One', 'swift-framework-plugin' )                => "boxed-one",
        __( 'Boxed Two', 'swift-framework-plugin' )                => "boxed-two",
        __( 'Boxed Three', 'swift-framework-plugin' )              => "boxed-three",
        __( 'Boxed Four', 'swift-framework-plugin' )               => "boxed-four",
        __( 'Animated', 'swift-framework-plugin' )                 => "animated",
    );

    if ( spb_get_theme_name() == "atelier" ) {

    	$icon_box_types = array(
    	    __( 'Standard', 'swift-framework-plugin' )                 => "standard",
    	    __( 'Standard with Title Icon', 'swift-framework-plugin' ) => "standard-title",
    	    __( 'Left Icon', 'swift-framework-plugin' )                => "left-icon",
    	    __( 'Left Icon Alt', 'swift-framework-plugin' )            => "left-icon-alt",
    	    __( 'Boxed One', 'swift-framework-plugin' )                => "boxed-one",
    	    __( 'Boxed Two', 'swift-framework-plugin' )                => "boxed-two",
    	    __( 'Boxed Three', 'swift-framework-plugin' )              => "boxed-three",
    	    __( 'Boxed Four', 'swift-framework-plugin' )               => "boxed-four",
    	    __( 'Animated', 'swift-framework-plugin' )                 => "animated",
    	);

    }

    SPBMap::map( 'spb_icon_box_grid_element', array(
        "name"          => __( "Icon Box Grid Element", 'swift-framework-plugin' ),
        "base"          => "spb_icon_box_grid_element",
        "class"         => "",
        "icon"          => "icon-icon-box",
        "wrapper_class" => "clearfix",
        "params"        => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Icon Box title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "holder"      => 'div',
                "value"       => "",
                "description" => __( "Icon Box title text.", 'swift-framework-plugin' )
            ),
              array(
                "type"        => "icon-picker",
                "heading"     => __( "Icon Box Icon", 'swift-framework-plugin' ),
                "param_name"  => "icon",
                "value"       => "",
                "description" => ''
            ),
           array(
                "type"        => "textarea_html",
                "holder"      => "div",
                "class"       => "",
                "heading"     => __( "Text", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "value"       => __( "click the edit button to change this text.", 'swift-framework-plugin' ),
                "description" => __( "Enter your content.", 'swift-framework-plugin' )
            ),
          )
    ) );