<?php

    /*
    *
    *	Swift Page Builder - Icon Boxes Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_icon_box extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $el_class = $text_color = $image_url = $image_object = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'           => '',
                'icon'            => '',
                'character'       => '',
                'image'           => '',
                'svg_icon'        => '',
                'animate_svg'     => '',
                'box_type'        => '',
                'box_icon_type'   => '',
                'icon_color'      => '',
                'icon_bg_color'   => '',
                'text_color'      => '',
                'bg_color'        => '',
                'flip_text_color' => '',
                'flip_bg_color'   => '',
                'animated_box_style' => '',
                'animated_box_rounded' => '',
                'animation'       => '',
                'animation_delay' => '',
                'link'            => '',
                'target'          => '',
                'el_class'        => '',
                'el_position'     => '',
                'width'           => '1/1'
            ), $atts ) );

            $output = '';

            if ( $image != "" ) {
                $img_url      = wp_get_attachment_url( $image, 'full' );
                $image_object = spb_image_resizer( $img_url, 70, 70, true, false );
                $image_url    = $image_object[0];
            }

            $icon_box_output = do_shortcode( '[sf_iconbox icon="' . $icon . '" character="' . $character . '" image="' . $image_url . '" svg="' . $svg_icon . '" animate_svg="' . $animate_svg . '" type="' . $box_type . '" icon_type="' . $box_icon_type . '" title="' . $title . '" animation="' . $animation . '" animation_delay="' . $animation_delay . '" bg_color="' . $bg_color . '" text_color="' . $text_color . '" icon_color="' . $icon_color . '" icon_bg_color="' . $icon_bg_color . '" flip_text_color="' . $flip_text_color . '" flip_bg_color="' . $flip_bg_color . '" animated_box_style="' . $animated_box_style . '" animated_box_rounded="' . $animated_box_rounded . '" link="' . $link . '" target="' . $target . '"]' . $content . '[/sf_iconbox]' );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_icon_box ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t" . $icon_box_output;
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

    $icon_box_icon_types = array(
        '' => '',
        __( "Icon", 'swift-framework-plugin' )  => "icon",
        __( "SVG", 'swift-framework-plugin' )  => "svg",
        __( "Character", 'swift-framework-plugin' )  => "character",
        __( "Image", 'swift-framework-plugin' )  => "image",
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

    $icon_box_icon_types = apply_filters( 'spb_icon_box_icon_types', $icon_box_icon_types );
    $icon_box_types = apply_filters( 'spb_icon_box_types', $icon_box_types );

    SPBMap::map( 'spb_icon_box', array(
        "name"          => __( "Icon Box", 'swift-framework-plugin' ),
        "base"          => "spb_icon_box",
        "class"         => "spb_tab_ui",
        "icon"          => "icon-icon-box",
        "wrapper_class" => "clearfix",
        "params"        => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Icon Box title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Icon Box title text.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Icon Box Type", 'swift-framework-plugin' ),
                "param_name"  => "box_type",
                "value"       => $icon_box_types,
                "description" => __( "Choose the type of icon box.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Icon Box Icon Type", 'swift-framework-plugin' ),
                "param_name"  => "box_icon_type",
                "value"       => $icon_box_icon_types,
                "description" => __( "Choose the type of icon box.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "icon-picker",
                "heading"     => __( "Icon Box Icon", 'swift-framework-plugin' ),
                "param_name"  => "icon",
                "value"       => "",
                "required"    => array("box_icon_type", "=", "icon"),
                "description" => ''
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Icon Box Character", 'swift-framework-plugin' ),
                "param_name"  => "character",
                "value"       => "",
                "required"    => array("box_icon_type", "=", "character"),
                "description" => __( "Instead of an icon, you can optionally provide a single letter/digit here. NOTE: This will override the icon selection.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "attach_image",
                "heading"     => __( "Icon Box Image", 'swift-framework-plugin' ),
                "param_name"  => "image",
                "value"       => "",
                "required"    => array("box_icon_type", "=", "image"),
                "description" => __( "Instead of an icon, you can optionally upload an image here. This will be resized to 70px x 70px. NOTE: This will override the icon selection.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "icon-picker",
                "heading"     => __( "Icon Box SVG", 'swift-framework-plugin' ),
                "param_name"  => "svg_icon",
                "icon_type"   => "svg",
                "data"        => spb_get_svg_icons(),
                "value"       => "",
                "required"    => array("box_icon_type", "=", "svg"),
                "description" => __( "You can select an SVG icon here.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Animate SVG Icon", 'swift-framework-plugin' ),
                "param_name"  => "animate_svg",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                ),
                "required"    => array("box_icon_type", "=", "svg"),
                "description" => __( "If you are using an outline svg icon, then you can enable/disable the drawing animation on appear here.", 'swift-framework-plugin' )
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
            array(
                "type"        => "textfield",
                "heading"     => __( "Icon Box Link", 'swift-framework-plugin' ),
                "param_name"  => "link",
                "value"       => "",
                "description" => __( "If you would like, you can set a link here for the icon and title to link through to.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "dropdown",
                "heading"    => __( "Link Target", 'swift-framework-plugin' ),
                "param_name" => "target",
                "value"      => $target_arr
            ),
            array(
                "type"       => "section",
                "param_name" => "ib_animation_options",
                "heading"    => __( "Animation Options", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Intro Animation", 'swift-framework-plugin' ),
                "param_name"  => "animation",
                "value"       => spb_animations_list(),
                "description" => __( "Select an intro animation for the icon box that will show it when it appears within the viewport.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Animation Delay", 'swift-framework-plugin' ),
                "param_name"  => "animation_delay",
                "value"       => "0",
                "description" => __( "If you wish to add a delay to the animation, then you can set it here (ms).", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section_tab",
                "param_name" => "design_tab",
                "heading"    => __( "Design Options", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Icon color", 'swift-framework-plugin' ),
                "param_name"  => "icon_color",
                "value"       => "",
                "description" => __( "Set the icon colour for the icon box, or leave blank to use the default colours.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Icon Background color", 'swift-framework-plugin' ),
                "param_name"  => "icon_bg_color",
                "value"       => "",
                "description" => __( "Set the icon background colour for the icon box, or leave blank to use the default colours.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Text color", 'swift-framework-plugin' ),
                "param_name"  => "text_color",
                "value"       => "",
                "description" => __( "Set the text colour for the icon box, or leave blank to use the default colours.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Background color", 'swift-framework-plugin' ),
                "param_name"  => "bg_color",
                "value"       => "",
                "description" => __( "Set the background colour for the icon box, or leave blank to use the default colours.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Flip Background color", 'swift-framework-plugin' ),
                "param_name"  => "flip_bg_color",
                "value"       => "",
                "required"       => array("box_type", "or", "animated,animated-alt"),
                "description" => __( "Set the background colour for the back of the animated icon box, or leave blank to use the default colours.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Flip Text color", 'swift-framework-plugin' ),
                "param_name"  => "flip_text_color",
                "value"       => "",
                "required"       => array("box_type", "or", "animated,animated-alt"),
                "description" => __( "Set the text colour for the back of the animated icon box, or leave blank to use the default colours.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Animated Box Style", 'swift-framework-plugin' ),
                "param_name"  => "animated_box_style",
                "value"       => array(
                    __( 'Stroke', 'swift-framework-plugin' ) => "stroke",
                    __( 'Coloured', 'swift-framework-plugin' )  => "coloured"
                ),
                "std"         => 'coloured',
                "required"       => array("box_type", "=", "animated-alt"),
                "description" => __( "If you are using the animated icon box option, you can choose the style here.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Animated Box Rounded", 'swift-framework-plugin' ),
                "param_name"  => "animated_box_rounded",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "std"         => 'yes',
                "required"       => array("box_type", "=", "animated-alt"),
                "description" => __( "If you are using the animated icon box option, you can choose to set it to be rounded here.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section_tab",
                "param_name" => "misc_tab",
                "heading"    => __( "Misc Options", 'swift-framework-plugin' ),
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
