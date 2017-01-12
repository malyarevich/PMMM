<?php

    /*
    *
    *   Swift Page Builder - Default Shortcodes
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    /* TEXT BLOCK ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_text_block extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $el_class = $width = $el_position = $inline_style = $form_content = $custom_css = $bk_image_global = '';

            extract( shortcode_atts( array(
                'title'              => '',
                'icon'               => '',
                'padding_vertical'   => '0',
                'padding_horizontal' => '0',
                'animation'          => '',
                'animation_delay'    => '',
                'el_class'           => '',
                'el_position'        => '',
                'form_content'       => '',
                'width'              => '1/2',
                'custom_css'         => '',
                'bk_image_global'    => '',
            ), $atts ) );

            if ( $form_content != '' ){
                $content = html_entity_decode($form_content);
            }  
           
            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $el_class .= ' spb_text_column';

            
            if( $custom_css != "" ){
                
                $pos = strpos( $custom_css, 'margin-bottom' );
                if ($pos !== false) { 
                    //$el_class .= ' mt0 mb0';        
                 }
            

                $inline_style .= $custom_css;
                $img_url = wp_get_attachment_image_src( $bk_image_global, 'full' );

                if( isset( $img_url ) && $img_url[0] != "" ) {
                    $inline_style .= 'background-image: url(' . $img_url[0] . ');';
                }
            }else{

                if ( $padding_vertical != "" ) {
                    $inline_style .= 'padding-top:' . $padding_vertical . '%;padding-bottom:' . $padding_vertical . '%;';
                }
                if ( $padding_horizontal != "" ) {
                    $inline_style .= 'padding-left:' . $padding_horizontal . '%;padding-right:' . $padding_horizontal . '%;';
                }
            }

            $icon_output = "";

            if ( $icon ) {
                $icon_output = '<i class="' . $icon . '"></i>';
            }

            if ( $animation != "" && $animation != "none" ) {
                $output .= "\n\t" . '<div class="spb_content_element sf-animation ' . $width . $el_class . '" data-animation="' . $animation . '" data-delay="' . $animation_delay . '">';
            } else {
                $output .= "\n\t" . '<div class="spb_content_element ' . $width . $el_class . '">';
            }

            $output .= "\n\t\t" . '<div class="spb-asset-content" style="' . $inline_style . '">';
            if ( $icon_output != "" ) {
                $output .= ( $title != '' ) ? "\n\t\t\t" . '<div class="title-wrap"><h3 class="spb-heading spb-icon-heading"><span>' . $icon_output . '' . $title . '</span></h3></div>' : '';
            } else {
                $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, 'spb-text-heading' ) : '';
            }
            $output .= "\n\t\t\t" . do_shortcode( $content );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', false ) . $output . $this->endRow( $el_position, '', false );

            return $output;
        }
    }
    
   
   SPBMap::map( 'spb_text_block', array(
            "name"          => __( "Text Block", 'swift-framework-plugin' ),
            "base"          => "spb_text_block",
            "class"         => "spb_tab_media",
            "icon"          => "icon-text-block",
            "wrapper_class" => "clearfix",
            "controls"      => "full",
            "params"        => array(
                array(
                    "type"        => "textfield",
                    "holder"      => "div",
                    "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                    "param_name"  => "title",
                    "value"       => "",
                    "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Title icon", 'swift-framework-plugin' ),
                    "param_name"  => "icon",
                    "value"       => "",
                    "description" => __( "Icon to the left of the title text. This is the class name for the icon, e.g. fa-cloud", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textarea_html",
                    "holder"      => "div",
                    "class"       => "",
                    "heading"     => __( "Text", 'swift-framework-plugin' ),
                    "param_name"  => "content",
                    "value"       => '',
                    //"value" => __("<p>This is a text block. Click the edit button to change this text.</p>", 'swift-framework-plugin'),
                    "description" => __( "Enter your content.", 'swift-framework-plugin' )
                ),
                array(
                    "type"       => "section_tab",
                    "param_name" => "animation_options_tab",
                    "heading"    => __( "Animation", 'swift-framework-plugin' ),
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
                    "type"        => "textfield",
                    "heading"     => __( "Data Form Content", 'swift-framework-plugin' ),
                    "param_name"  => "form_content",
                    "value"       => "",
                    "description" => __( "This is a hidden field that is used to save the content when using forms inside the content.", 'swift-framework-plugin' )
                )
            )
        )
    );


    /* BOXED CONTENT ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_boxed_content extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $type = $bg_style = $inline_style = $custom_bg_colour = $custom_text_colour = $padding_vertical = $padding_horizontal = $el_class = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'              => '',
                'type'               => '',
                'custom_bg_colour'   => '',
                'custom_text_colour' => '',
                'box_link'           => '',
                'box_link_target'    => '_self',
                'padding_vertical'   => '0',
                'padding_horizontal' => '0',
                'el_class'           => '',
                'el_position'        => '',
                'width'              => '1/2'
            ), $atts ) );

            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            if ( $custom_bg_colour != "" ) {
                $bg_style .= 'background: ' . $custom_bg_colour . '!important;';
            }

            if ( $custom_text_colour != "" ) {
                $inline_style .= 'color: ' . $custom_text_colour . '!important;';
            }

            if ( $padding_vertical != "" ) {
                $inline_style .= 'padding-top:' . $padding_vertical . '%;padding-bottom:' . $padding_vertical . '%;';
            }
            if ( $padding_horizontal != "" ) {
                $inline_style .= 'padding-left:' . $padding_horizontal . '%;padding-right:' . $padding_horizontal . '%;';
            }

            $output .= "\n\t" . '<div class="spb_content_element spb_box_content ' . $width . $el_class . '">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '' ) : '';
            $output .= "\n\t" . '<div class="spb-bg-color-wrap ' . $type . '" style="' . $bg_style . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';

            if ( $box_link != "" ) {
                $output .= '<a href="' . $box_link . '" target="' . $box_link_target . '" class="box-link"></a>';
            }

            $output .= "\n\t\t";
            if ( $inline_style != "" ) {
                $output .= '<div class="box-content-wrap" style="' . $inline_style . '">' . do_shortcode( $content ) . '</div>';
            } else {
                $output .= '<div class="box-content-wrap">' . do_shortcode( $content ) . '</div>';
            }
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    $target_arr = array(
        __( "Same window", 'swift-framework-plugin' ) => "_self",
        __( "New window", 'swift-framework-plugin' )  => "_blank"
    );

    SPBMap::map( 'spb_boxed_content', array(
        "name"          => __( "Boxed Content", 'swift-framework-plugin' ),
        "base"          => "spb_boxed_content",
        "class"         => "spb_tab_media",
        "icon"          => "icon-boxed-content",
        "wrapper_class" => "clearfix",
        "controls"      => "full",
        "params"        => array(
            array(
                "type"        => "textfield",
                "holder"      => "div",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textarea_html",
                "holder"      => "div",
                "class"       => "",
                "heading"     => __( "Text", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "value"       => __( "<p>This is a boxed content block. Click the edit button to edit this text.</p>", 'swift-framework-plugin' ),
                "description" => __( "Enter your content.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Box type", 'swift-framework-plugin' ),
                "param_name"  => "type",
                "value"       => array(
                    __( 'Coloured', 'swift-framework-plugin' )          => "coloured",
                    __( 'White with stroke', 'swift-framework-plugin' ) => "whitestroke"
                ),
                "description" => __( "Choose the surrounding box type for this content", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Background color", 'swift-framework-plugin' ),
                "param_name"  => "custom_bg_colour",
                "value"       => "",
                "description" => __( "Provide a background colour here. If blank, your colour customisaer settings will be used.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Text colour", 'swift-framework-plugin' ),
                "param_name"  => "custom_text_colour",
                "value"       => "",
                "description" => __( "Provide a text colour here. If blank, your colour customisaer settings will be used.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "textfield",
                "heading"    => __( "Overlay Link", 'swift-framework-plugin' ),
                "param_name" => "box_link",
                "value"      => "",
                "description" => __( "Optionally provide a link here. This will overlay the boxed content and make the asset a whole clickable link.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "dropdown",
                "heading"    => __( "Overlay Link Target", 'swift-framework-plugin' ),
                "param_name" => "box_link_target",
                "value"      => $target_arr
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Padding - Vertical", 'swift-framework-plugin' ),
                "param_name"  => "padding_vertical",
                "value"       => "0",
                "step"        => "1",
                "min"         => "0",
                "max"         => "20",
                "description" => __( "Adjust the vertical padding for the text block (percentage).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Padding - Horizontal", 'swift-framework-plugin' ),
                "param_name"  => "padding_horizontal",
                "value"       => "0",
                "step"        => "1",
                "min"         => "0",
                "max"         => "20",
                "description" => __( "Adjust the horizontal padding for the text block (percentage).", 'swift-framework-plugin' )
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


    /* DIVIDER ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_divider extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $with_line = $fullwidth = $type = $width = $el_class = $text = '';
            extract( shortcode_atts( array(
                'with_line'     => '',
                'type'          => '',
                'heading_text'  => '',
                'top_margin'    => '0px',
                'bottom_margin' => '30px',
                'fullwidth'     => '',
                'text'          => '',
                'width'         => '1/1',
                'el_class'      => '',
                'el_position'   => ''
            ), $atts ) );

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $fullwidth == "yes" && $width == "1/1" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }

            $width = spb_translateColumnWidthToSpan( $width );

            $up_icon = apply_filters( 'sf_up_icon' , '<i class="ss-up"></i>' );

            $style = "margin-top: " . $top_margin . "; margin-bottom: " . $bottom_margin . ";";

            $output = '';
            if ( $fullwidth ) {
                $output .= '<div class="divider-wrap spb-full-width-element ' . $width . '">';
            } else {
                $output .= '<div class="divider-wrap ' . $width . '">';
            }
            if ( $type == "heading" ) {
                $output .= '<div class="spb_divider ' . $el_class . '" style="' . $style . '">';
                $output .= '<h3 class="divider-heading">' . $heading_text . '</h3>';
                $output .= '</div>' . $this->endBlockComment( 'divider' ) . "\n";
            } else {
                $output .= '<div class="spb_divider ' . $type . ' spb_content_element ' . $el_class . '" style="' . $style . '">';
                if ( $type == "go_to_top" ) {
                    $output .= '<a class="animate-top" href="#">' . $text . '</a>';
                } else if ( $type == "go_to_top_icon1" ) {
                    $output .= '<a class="animate-top" href="#">' . $up_icon . '</a>';
                } else if ( $type == "go_to_top_icon2" ) {
                    $output .= '<a class="animate-top" href="#">' . $text . $up_icon . '</a>';
                }
                $output .= '</div>' . $this->endBlockComment( 'divider' ) . "\n";
            }

            $output .= '</div>';

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            return $output;
        }
    }

    SPBMap::map( 'spb_divider', array(
        "name"        => __( "Divider", 'swift-framework-plugin' ),
        "base"        => "spb_divider",
        "class"       => "spb_divider",
        'icon'        => 'icon-divider',
        "controls"    => '',
        "params"      => array(
            array(
                "type"        => "dropdown",
                "heading"     => __( "Divider type", 'swift-framework-plugin' ),
                "param_name"  => "type",
                "value"       => array(
                    __( 'Standard', 'swift-framework-plugin' )           => "standard",
                    __( 'Thin', 'swift-framework-plugin' )               => "thin",
                    __( 'Dotted', 'swift-framework-plugin' )             => "dotted",
                    __( 'Heading', 'swift-framework-plugin' )            => "heading",
                    __( 'Go to top (text)', 'swift-framework-plugin' )   => "go_to_top",
                    __( 'Go to top (Icon 1)', 'swift-framework-plugin' ) => "go_to_top_icon1",
                    __( 'Go to top (Icon 2)', 'swift-framework-plugin' ) => "go_to_top_icon2"
                ),
                "description" => __( "Select divider type.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Divider Heading Text", 'swift-framework-plugin' ),
                "param_name"  => "heading_text",
                "value"       => '',
                "description" => __( "The text for the the 'Heading' divider type.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Go to top text", 'swift-framework-plugin' ),
                "param_name"  => "text",
                "value"       => __( "Go to top", 'swift-framework-plugin' ),
                "required"       => array("blog_type", "or", "go_to_top,go_to_top_icon1,go_to_top_icon2"),
                "description" => __( "The text for the 'Go to top (text)' divider type.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Top Margin", 'swift-framework-plugin' ),
                "param_name"  => "top_margin",
                "value"       => __( "0px", 'swift-framework-plugin' ),
                "description" => __( "Set the margin above the divider (include px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Bottom Margin", 'swift-framework-plugin' ),
                "param_name"  => "bottom_margin",
                "value"       => __( "30px", 'swift-framework-plugin' ),
                "description" => __( "Set the margin below the divider (include px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Full width", 'swift-framework-plugin' ),
                "param_name"  => "fullwidth",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "buttonset_on"  => "yes",
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
        ),
        "js_callback" => array( "init" => "spbTextSeparatorInitCallBack" )
    ) );


    /* BLANK SPACER ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_blank_spacer extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $height = $el_class = '';
            extract( shortcode_atts( array(
                'height'         => '',
                'width'          => '',
                'responsive_vis' => '',
                'el_position'    => '',
                'el_class'       => '',
            ), $atts ) );

            $responsive_vis = str_replace( "_", " ", $responsive_vis );
            $width          = spb_translateColumnWidthToSpan( $width );
            $el_class       = $this->getExtraClass( $el_class ) . ' ' . $responsive_vis;

            $output = '';
            $output .= '<div class="blank_spacer ' . $width . ' ' . $el_class . '" style="height:' . $height . ';">';
            $output .= '</div>' . $this->endBlockComment( 'divider' ) . "\n";

            $output = $this->startRow( $el_position, '', false ) . $output . $this->endRow( $el_position, '', false );

            return $output;
        }
    }

    SPBMap::map( 'spb_blank_spacer', array(
        "name"   => __( "Blank Spacer", 'swift-framework-plugin' ),
        "base"   => "spb_blank_spacer",
        "class"  => "spb_blank_spacer",
        'icon'   => 'icon-blank-spacer',
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Height", 'swift-framework-plugin' ),
                "param_name"  => "height",
                "value"       => __( "30px", 'swift-framework-plugin' ),
                "description" => __( "The height of the spacer, in px (required).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Responsive Visiblity", 'swift-framework-plugin' ),
                "param_name"  => "responsive_vis",
                "holder"      => 'indicator',
                "value"       => spb_responsive_vis_list(),
                "description" => __( "Set the responsive visiblity for the row, if you would only like it to display on certain display sizes.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        ),
    ) );


    /* MESSAGE BOX ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_message extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $color = '';
            extract( shortcode_atts( array(
                'color'       => 'alert-info',
                'el_position' => ''
            ), $atts ) );
            $output = '';
            if ( $color == "alert-block" ) {
                $color = "";
            }

            $width = spb_translateColumnWidthToSpan( "1/1" );

            $output .= '<div class="' . $width . '"><div class="alert spb_content_element ' . $color . '"><div class="messagebox_text">' . spb_format_content( $content ) . '</div></div></div>' . $this->endBlockComment( 'alert box' ) . "\n";
            //$output .= '<div class="spb_messagebox message '.$color.'"><div class="messagebox_text">'.spb_format_content($content).'</div></div>';
            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    SPBMap::map( 'spb_message', array(
        "name"          => __( "Message Box", 'swift-framework-plugin' ),
        "base"          => "spb_message",
        "class"         => "spb_messagebox spb_controls_top_right spb_tab_ui",
        "icon"          => "icon-message-box",
        "wrapper_class" => "alert",
        "controls"      => "edit_popup_delete",
        "params"        => array(
            array(
                "type"        => "dropdown",
                "heading"     => __( "Message box type", 'swift-framework-plugin' ),
                "param_name"  => "color",
                "value"       => array(
                    __( 'Informational', 'swift-framework-plugin' ) => "alert-info",
                    __( 'Warning', 'swift-framework-plugin' )       => "alert-block",
                    __( 'Success', 'swift-framework-plugin' )       => "alert-success",
                    __( 'Error', 'swift-framework-plugin' )         => "alert-error"
                ),
                "description" => __( "Select message type.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textarea_html",
                "holder"      => "div",
                "class"       => "messagebox_text",
                "heading"     => __( "Message text", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "value"       => __( "<p>This is a message box. Click the edit button to edit this text.</p>", 'swift-framework-plugin' ),
                "description" => __( "Message text.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        ),
        "js_callback"   => array( "init" => "spbMessageInitCallBack" )
    ) );


    /* TOGGLE ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_toggle extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $title = $el_class = $open = null;
            extract( shortcode_atts( array(
                'title'       => __( "Click to toggle", 'swift-framework-plugin' ),
                'el_class'    => '',
                'open'        => 'false',
                'el_position' => '',
                'width'       => '1/1'
            ), $atts ) );
            $output = '';

            $width = spb_translateColumnWidthToSpan( $width );

            $el_class = $this->getExtraClass( $el_class );
            $open     = ( $open == 'true' ) ? ' spb_toggle_title_active' : '';
            $el_class .= ( $open == ' spb_toggle_title_active' ) ? ' spb_toggle_open' : '';
            $output .= '<div class="toggle-wrap ' . $width . '">';
            $output .= '<h4 class="spb_toggle' . $open . '">' . $title . '</h4><div class="spb_toggle_content' . $el_class . '">' . spb_format_content( $content ) . '</div>' . $this->endBlockComment( 'toggle' ) . "\n";
            $output .= '</div>';
            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    SPBMap::map( 'spb_toggle', array(
        "name"   => __( "Toggle", 'swift-framework-plugin' ),
        "base"   => "spb_toggle",
        "class"  => "spb_faq spb_tab_ui",
        "icon"   => "icon-toggle",
        "params" => array(
            array(
                "type"        => "textfield",
                "class"       => "toggle_title",
                "heading"     => __( "Toggle title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => __( "Toggle title", 'swift-framework-plugin' ),
                "description" => __( "Toggle block title.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textarea_html",
                "holder"      => "div",
                "class"       => "toggle_content",
                "heading"     => __( "Toggle content", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "value"       => __( "<p>The toggle content goes here, click the edit button to change this text.</p>", 'swift-framework-plugin' ),
                "description" => __( "Toggle block content.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Default state", 'swift-framework-plugin' ),
                "param_name"  => "open",
                "value"       => array(
                    __( "Closed", 'swift-framework-plugin' ) => "false",
                    __( "Open", 'swift-framework-plugin' )   => "true"
                ),
                "description" => __( "Select this if you want toggle to be open by default.", 'swift-framework-plugin' )
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

