<?php

    /*
    *
    *	Swift Page Builder - Button Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_button extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'text'                  => '',
                'size'                  => '',
                'colour'                => '',
                'type'                  => '',
                'icon'                  => '',
                'link'                  => '',
                'target'                => '',
                'button_text'           => '',
                'button_size'           => 'large',
                'button_colour'         => '',
                'button_type'           => '',
                'rounded'               => '',
                'button_dropshadow'     => '',
                'button_icon'           => '',
                'button_link'           => '',
                'button_target'         => '',
                'align'                 => 'center',
                'animation'             => '',
                'animation_delay'       => '',
                'el_position'           => '',
                'el_class'              => '',
                'width'                 => '1/1'
            ), $atts ) );

            // fix
            if ( $type != "" ) {
                $button_type = $type;
            }
            if ( $size != "" ) {
                $button_size = $size;
            }
            if ( $colour != "" ) {
                $button_colour = $colour;
            }
            if ( $text != "" ) {
                $button_text = $text;
            }
            if ( $icon != "" ) {
                $button_icon = $icon;
            }
            if ( $link != "" ) {
                $button_link = $link;
            }
            if ( $target != "" ) {
                $button_target = $target;
            }

            // lowercase
            $button_size = strtolower($button_size); 
            $button_colour = strtolower($button_colour);
            $button_type = strtolower($button_type);

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_button spb_content_element ' . $width . $el_class . '" data-align="'.$align.'" data-animation="' . $animation . '" data-delay="' . $animation_delay . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t\t" . do_shortcode( '[sf_button type="' . $button_type . '" colour="' . $button_colour . '" size="' . $button_size . '" link="' . $button_link . '" target="' . $button_target . '" rounded="' . $rounded . '" dropshadow="' . $button_dropshadow . '" icon="' . $button_icon . '"]' . $button_text . '[/sf_button]' );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    /* PARAMS
    ================================================== */  

    $button_types = array(
        'standard' => __('Standard', 'swiftframework'),
        'bordered' => __('Bordered', 'swiftframework'),
        'rotate-3d' => __('3D Rotate', 'swiftframework'),
        'stroke-to-fill' => __('Stroke To Fill', 'swiftframework'),
        'sf-icon-reveal' => __('Icon Reveal', 'swiftframework'),
        'sf-icon-stroke' => __('Icon', 'swiftframework'),
    );
    
    if (!spb_theme_supports( '3drotate-button' )) {
        unset($button_types['rotate-3d']);
    }
    if (!spb_theme_supports( 'bordered-button' )) {
        unset($button_types['bordered']);
    }
    
    $button_sizes = array(
        'standard' => __('Standard', 'swiftframework'),
        'large' => __('Large', 'swiftframework'),
    );
    
    $button_colours = array(
        'accent' => __('Accent', 'swiftframework'),
        'black' => __('Black', 'swiftframework'),
        'white' => __('White', 'swiftframework'),
        'blue'  => __('Blue', 'swiftframework'),
        'grey'  => __('Grey', 'swiftframework'),
        'lightgrey' => __('Light Grey', 'swiftframework'),
        'orange'    => __('Orange', 'swiftframework'),
        'green' => __('Green', 'swiftframework'),
        'pink'  => __('Pink', 'swiftframework'),
        'gold'  => __('Gold', 'swiftframework'),
        'transparent-light' => __('Transparent - Light', 'swiftframework'),
        'transparent-dark'  => __('Transparent - Dark', 'swiftframework'),
    );
    
    $button_types = apply_filters( 'sf_shortcode_button_types', $button_types );
    $button_sizes = apply_filters( 'sf_shortcode_button_sizes', $button_sizes );
    $button_colours = apply_filters( 'sf_shortcode_button_colours', $button_colours );

    $params = array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Button Text", 'swift-framework-plugin' ),
                "param_name"  => "button_text",
                "value"       => "",
                "description" => __( "Enter the button text here.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown-id",
                "heading"     => __( "Button Size", 'swift-framework-plugin' ),
                "param_name"  => "button_size",
                "value"       => $button_sizes,
                "description" => __( "Choose the button size.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown-id",
                "heading"     => __( "Button Colour", 'swift-framework-plugin' ),
                "param_name"  => "button_colour",
                "value"       => $button_colours,
                "description" => __( "Choose the button colour.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown-id",
                "heading"     => __( "Button Type", 'swift-framework-plugin' ),
                "param_name"  => "button_type",
                "value"       => $button_types,
                "description" => __( "Choose the button type.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Rounded Button", 'swift-framework-plugin' ),
                "param_name"  => "rounded",
                "value"       => array(
                    __( "No", 'swift-framework-plugin' )  => "no",
                    __( "Yes", 'swift-framework-plugin' ) => "yes",
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Select if you want the button to be rounded.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Button Drop Shadow", 'swift-framework-plugin' ),
                "param_name"  => "button_dropshadow",
                "value"       => array(
                    __( "No", 'swift-framework-plugin' )  => "no",
                    __( "Yes", 'swift-framework-plugin' ) => "yes",
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Select if you want the button to have a drop shadow.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "icon-picker",
                "heading"     => __( "Button Icon", 'swift-framework-plugin' ),
                "param_name"  => "button_icon",
                "value"       => "",
                "description" => ''
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Button Link", 'swift-framework-plugin' ),
                "param_name"  => "button_link",
                "value"       => "",
                "description" => __( "Enter the button link here, be sure to include http://.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Button Link Target", 'swift-framework-plugin' ),
                "param_name"  => "button_target",
                "value"       => array(
                    __( "Self", 'swift-framework-plugin' )       => "_self",
                    __( "New Window", 'swift-framework-plugin' ) => "_blank"
                ),
                "description" => __( "Select if you want the link to open in a new window or in the same window.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Button align", 'swift-framework-plugin' ),
                "param_name"  => "align",
                "value"       => array(
                    __( "Left", 'swift-framework-plugin' )   => "left",
                    __( "Center", 'swift-framework-plugin' ) => "center",
                    __( "Right", 'swift-framework-plugin' ) => "right"
                ),
                "std"         => 'center',
                "description" => __( "Choose the button alignment.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section",
                "param_name" => "tb_animation_options",
                "heading"    => __( "Animation Options", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Intro Animation", 'swift-framework-plugin' ),
                "param_name"  => "animation",
                "value"       => spb_animations_list(),
                "description" => __( "Select an intro animation for the text block that will show it when it appears within the viewport.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Animation Delay", 'swift-framework-plugin' ),
                "param_name"  => "animation_delay",
                "value"       => "0",
                "description" => __( "If you wish to add a delay to the animation, then you can set it here (ms).", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section",
                "param_name" => "btn_misc_options",
                "heading"    => __( "Misc Options", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        );


    /* SHORTCODE MAP
    ================================================== */  
    SPBMap::map( 'spb_button', array(
        "name"   => __( "Button", 'swift-framework-plugin' ),
        "base"   => "spb_button",
        "class"  => "spb_button spb_tab_ui",
        "icon"   => "icon-button",
        "params" => $params,
    ) );
