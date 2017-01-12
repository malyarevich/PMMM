<?php

    /*
    *
    *   Swift Page Builder - Column Shortcode
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_column extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $col_el_class = $padding_horizontal = $col_xs = $col_sm = $col_md = $col_lg = $el_class = $inline_style = $inner_inline_style = $width = $el_position = $custom_css ='';

            extract( shortcode_atts( array(
                'col_bg_color'                => '',
                'col_bg_image'                => '',
                'col_bg_type'                 => 'cover',
                'col_parallax_image_movement' => 'fixed',
                'col_parallax_image_speed'    => '0.5',
                'col_padding'                 => '',
                'col_vertical_offset'         => '0',
                'col_horizontal_offset'       => '0',
                'col_custom_zindex'           => '',
                'padding_horizontal'          => '0',
                'col_animation'               => '',
                'col_xs'                      => '',
                'col_sm'                      => '12',
                'col_md'                      => '',
                'col_lg'                      => '',
                'col_animation_delay'         => '',
                'col_responsive_vis'          => '',
                'col_el_class'                => '',
                'el_position'                 => '',
                'width'                       => '1/2',
                'custom_css'                  => '',
                'el_class'                    => '',
            ), $atts ) );

            $output = $animation_output = '';
            $parallax_layer_styles = array();

            $col_responsive_vis = str_replace( "_", " ", $col_responsive_vis );
            $col_el_class       = $this->getExtraClass( $col_el_class ) . ' ' . $col_responsive_vis;
            $orig_width         = $width;
            $width              = spb_translateColumnWidthToSpan( $width );
            $img_url            = wp_get_attachment_image_src( $col_bg_image, 'full' );

            if( $custom_css != "" ){
                $inner_inline_style .= $custom_css;
            }

            if ( $col_bg_color != "" ) {
                $inner_inline_style .= 'background-color:' . $col_bg_color . ';';
            }

            if ( isset( $img_url ) && $img_url[0] != "" && $col_parallax_image_movement != "stellar" ) {
                $inner_inline_style .= 'background-image: url(' . $img_url[0] . ');';
            }

            if ( $col_padding != "" &&  $custom_css == "" && $padding_horizontal == '') {  
                $inner_inline_style .= 'padding:' . $col_padding . '%;';
            }else if( $custom_css == "" && $padding_horizontal != '' ){
                $inner_inline_style .= 'padding-left:' . $padding_horizontal . 'px; padding-right:' . $padding_horizontal . 'px;';
            }

            if( $el_class != '' ) {
                $col_el_class = $el_class;
            }

            if ( $col_animation != "" && $col_animation != "none" ) {
                $col_el_class .= ' sf-animation';
                $animation_output = 'data-animation="' . $col_animation . '" data-delay="' . $col_animation_delay . '"';
            }        
  
            global $column_width;

            if ( $orig_width != "" ) {
                $column_width = $orig_width;
            }
             
            $col_res = '';

            if ( $col_xs != '' ){
                $col_res .= 'col-xs-' . $col_xs;
            }
            
            if ( $col_md != '' ){
                $col_res .= ' col-md-' . $col_md;
            }

            if ( $col_lg != '' ){
                $col_res .= ' col-lg-' . $col_lg;
            }

            if ( ($col_vertical_offset != "" || $col_horizontal_offset != "") && ($col_vertical_offset != "0" || $col_horizontal_offset != "0") ) {
                $col_el_class .= ' spb-col-custom-offset';
            }
            if ( $col_vertical_offset != "0" ) {
                $inline_style .= 'top: '.$col_vertical_offset.'px;';
            }
            if ( $col_horizontal_offset != "0" ) {
                $inline_style .= 'left: '.$col_horizontal_offset.'px;';
            }
            if ( $col_custom_zindex != "" ) {
                $inline_style .= 'z-index: '.$col_custom_zindex.';';
            }

            if ( $col_bg_image != "" && $img_url[0] != "" ) {
                if ( $col_parallax_image_movement == "stellar" ) {
                    wp_enqueue_script( 'parallax' );
                    $parallax_layer_styles[] = 'background-image: url(' . $img_url[0] . ');';
                    $output .= "\n\t" . '<div class="spb-column-container spb-row-parallax spb_parallax_asset spb-parallax-stellar spb_content_element bg-type-' . $col_bg_type . ' ' . $width . ' ' . $col_res . ' '. $col_el_class . '" ' . $animation_output . ' style="' . $inline_style . '">';
                } else {
                    $output .= "\n\t" . '<div class="spb-column-container spb_parallax_asset spb-parallax spb-parallax-' . $col_parallax_image_movement . ' spb_content_element bg-type-' . $col_bg_type . ' ' . $width . ' ' . $col_res . ' ' . $col_el_class . '" ' . $animation_output . ' style="' . $inline_style . '">';
                }
            } else {
                $output .= "\n\t" . '<div class="spb-column-container ' . $width . ' ' . $col_res . ' ' . $col_el_class . '" ' . $animation_output . ' style="'. $inline_style . ' ">';
            }
            $output .= "\n\t\t" . '<div class="spb-column-inner row clearfix" style="'.$inner_inline_style.'">';
            $output .= "\n\t\t" . spb_format_content( $content );
            $output .= "\n\t\t" . '</div>';

            if ( $col_parallax_image_movement == "parallax" || $col_parallax_image_movement == "stellar" ) {
            $output .= "\n\t\t" . '<div class="spb-row-parallax-layer-wrap">';
            $output .= "\n\t\t\t" . '<div class="spb-row-parallax-layer" style="' . implode('', $parallax_layer_styles) . '"></div>';
            $output .= "\n\t\t" . '</div>';
            }

            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $column_width = "";

            $output = $this->startRow( $el_position, $orig_width ) . $output . $this->endRow( $el_position, 'column' );

            if ( isset( $img_url ) && $img_url[0] != "" && $col_parallax_image_movement == "stellar" ) {
                global $sf_include_parallax;
                $sf_include_parallax = true;
            }

            return $output;
        }

        public function contentAdmin( $atts, $content = null ) {
            $width = $padding_horizontal = $col_responsive_vis = '';
            extract( shortcode_atts( array(
                'width'                       => 'column_12',
                'col_bg_color'                => '',
                'col_bg_image'                => '',
                'col_bg_type'                 => '',
                'col_parallax_image_movement' => '',
                'col_parallax_image_speed'    => '',
                'col_padding'                 => '',
                'col_vertical_offset'         => '',
                'col_horizontal_offset'       => '',
                'col_custom_zindex'           => '',
                'col_animation'               => '',
                'col_animation_delay'         => '',
                'col_xs'                      => '',
                'col_sm'                      => '12',
                'col_md'                      => '',
                'col_lg'                      => '',
                'col_responsive_vis'          => '',
                'el_position'                 => '',
                'col_el_class'                => '',
                'custom_css'                  => '',
                'simplified_controls'         => '',
                'border_color_global'         => '',
                'border_styling_global'       => '',
                'back_color_global'           => '',
                'border_styling_global'       => '',
                'padding_horizontal'          => '0',
                'padding_vertical'            => '',
                'margin_vertical'            => '',
                'custom_css_percentage'       => '',
                'custom_css'                  => '',
                'el_class'                    => ''

            ), $atts ) );

            $output = '';

            $column_controls = $this->getColumnControls( 'column',  $col_responsive_vis );
          

            if ( $width == 'column_14' || $width == '1/4' ) {
                $width = array( 'span3' );
                $col_sm = '3';
            } else if ( $width == 'column_14-14-14-14' ) {
                $width = array( 'span3', 'span3', 'span3', 'span3' );
                $col_sm = '3';
            } else if ( $width == 'column_14-12-14' ) {
                $width = array( 'span3', 'span6', 'span3' );
            } else if ( $width == 'column_12-14-14' ) {
                $width = array( 'span6', 'span3', 'span3' );
            } else if ( $width == 'column_14-14-12' ) {
                $width = array( 'span3', 'span3', 'span6' );
            } else if ( $width == 'column_13' || $width == '1/3' ) {
                $width = array( 'span4' );
                $col_sm = '4';
            } else if ( $width == 'column_13-23' ) {
                $width = array( 'span4', 'span8' );
                $col_sm = '';
            } else if ( $width == 'column_23-13' ) {
                $width = array( 'span8', 'span4' );
            } else if ( $width == 'column_13-13-13' ) {
                $width = array( 'span4', 'span4', 'span4' );
                $col_sm = '4';
            } else if ( $width == 'column_12' || $width == '1/2' ) {
                $width = array( 'span6' );
                $col_sm = '6';
            } else if ( $width == 'column_12-12' ) {
                $width = array( 'span6', 'span6' );
                $col_sm = '6';
            } else if ( $width == 'column_23' || $width == '2/3' ) {
                $width = array( 'span8' );
                $col_sm = '8';
            } else if ( $width == 'column_34' || $width == '3/4' ) {
                $width = array( 'span9' );
                $col_sm = '9';
            } else if ( $width == 'column_16' || $width == '1/6' ) {
                $width = array( 'span2' );
                $col_sm = '2';
            } else {
                $width = array( 'span12' );
                $col_sm = '12';
            }
 

            for ( $i = 0; $i < count( $width ); $i ++ ) {
                $output .= '<div data-element_type="spb_column" class="spb_column spb_sortable spb_droppable ' . $width[ $i ] . ' not-column-inherit">';
                $output .= '<input type="hidden" class="spb_sc_base" name="" value="spb_column" />';
                $output .= str_replace( "%column_size%", spb_translateColumnWidthToFractional( $width[ $i ] ), $column_controls );
                $output .= '<div class="spb_element_wrapper">';
                $output .= '<div class="row-fluid spb_column_container spb_sortable_container not-column-inherit">';

                $output .= do_shortcode( shortcode_unautop( $content ) );
                $output .= SwiftPageBuilder::getInstance()->getLayout()->getContainerHelper();
                $output .= '</div>';

                
                if ( isset( $this->settings['params'] ) ) {
                    $inner = '';
                    foreach ( $this->settings['params'] as $param ) {
                        $param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : '';
                        //var_dump($param_value);
                        if ( is_array( $param_value ) ) {
                            // Get first element from the array
                            reset( $param_value );
                            $first_key   = key( $param_value );
                            $param_value = $param_value[ $first_key ];
                        }
                        $inner .= $this->singleParamHtmlHolder( $param, $param_value );
                    }
                    $output .= $inner;
                }
                $output .= '</div>';
                $output .= '</div>';
            }

            return $output;
        }
    }

    SPBMap::map( 'spb_column', array(
        "name"            => __( "Column", 'swift-framework-plugin' ),
        "base"            => "spb_column",
        "controls"        => "full-column",
        "class"           => "spb_column spb_tab_layout",
        "icon"           => "icon-column",
        "params"          => array(
           
            array(
                "type"        => "dropdown",
                "heading"     => __( "Intro Animation", 'swift-framework-plugin' ),
                "param_name"  => "col_animation",
                "value"       => spb_animations_list(),
                "description" => __( "Select an intro animation for the column which will show it when it appears within the viewport.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Animation Delay", 'swift-framework-plugin' ),
                "param_name"  => "col_animation_delay",
                "value"       => "0",
                "description" => __( "If you wish to add a delay to the animation, then you can set it here (ms).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Responsive Visiblity", 'swift-framework-plugin' ),
                "param_name"  => "col_responsive_vis",
                "holder"      => 'indicator',
                "value"       => spb_responsive_vis_list(),
                "description" => __( "Set the responsive visiblity for the row, if you would only like it to display on certain display sizes.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "col_el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            ),
            array(
                 "type"       => "section_tab",
                 "param_name" => "background_tab",
                 "heading"    => __( "Background Options", 'swift-framework-plugin' ),
                 "value"      => "" 
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Background color", 'swift-framework-plugin' ),
                "param_name"  => "col_bg_color",
                "value"       => "",
                "description" => __( "Select a background colour for the row here.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "attach_image",
                "heading"     => __( "Background Image", 'swift-framework-plugin' ),
                "param_name"  => "col_bg_image",
                "value"       => "",
                "description" => "Choose an image to use as the background for the parallax area."
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Background Image Type", 'swift-framework-plugin' ),
                "param_name"  => "col_bg_type",
                "value"       => array(
                    __( "Cover", 'swift-framework-plugin' )   => "cover",
                    __( "Pattern", 'swift-framework-plugin' ) => "pattern"
                ),
                "description" => __( "If you're uploading an image that you want to spread across the whole asset, then choose cover. Else choose pattern for an image you want to repeat.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Background Image Movement", 'swift-framework-plugin' ),
                "param_name"  => "col_parallax_image_movement",
                "value"       => array(
                    __( "Fixed", 'swift-framework-plugin' )             => "fixed",
                    __( "Scroll", 'swift-framework-plugin' )            => "scroll",
                    __( "Stellar (dynamic)", 'swift-framework-plugin' ) => "stellar",
                ),
                "description" => __( "Choose the type of movement you would like the parallax image to have. Fixed means the background image is fixed on the page, Scroll means the image will scroll will the page, and stellar makes the image move at a seperate speed to the page, providing a layered effect.", 'swift-framework-plugin' )
            ),
            array( 
                "type"        => "textfield",
                "heading"     => __( "Parallax Image Speed", 'swift-framework-plugin' ),
                "param_name"  => "col_parallax_image_speed",
                "value"       => "0.5",
                "description" => "The speed at which the parallax image moves in relation to the page scrolling. For example, 0.5 would mean the image scrolls at half the speed of the standard page scroll. (Default 0.5)."
            ),
            array(
                 "type"       => "section_tab",
                 "param_name" => "design_tab",
                 "heading"    => __( "Design Options", 'swift-framework-plugin' ),
                 "value"      => "" 
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Padding", 'swift-framework-plugin' ),
                "param_name"  => "col_padding",
                "value"       => "0",
                "min"         => "0",
                "step"        => "1",
                "max"         => "20",
                "description" => __( "Adjust the padding for the column. (%)", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Vertical Offset", 'swift-framework-plugin' ),
                "param_name"  => "col_vertical_offset",
                "value"       => "0",
                "min"         => "-1000",
                "step"        => "10",
                "max"         => "1000",
                "description" => __( "Add a vertical offset to the column (px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Horizontal Offset", 'swift-framework-plugin' ),
                "param_name"  => "col_horizontal_offset",
                "value"       => "0",
                "min"         => "-1000",
                "step"        => "10",
                "max"         => "1000",
                "description" => __( "Add a horizontal offset to the column (px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Custom z-index", 'swift-framework-plugin' ),
                "param_name"  => "col_custom_zindex",
                "value"       => "0",
                "min"         => "0",
                "step"        => "1",
                "max"         => "100",
                "description" => __( "Add a custom z-index to the column, for use with offsets and layering.", 'swift-framework-plugin' )
            ),
            array(
                 "type"       => "section_tab",
                 "param_name" => "column_sizing_tab",
                 "heading"    => __( "Custom Sizing", 'swift-framework-plugin' ),
                 "value"      =>          "" 
            ),
            array( 
                "type"        => "dropdown",
                "heading"     => __( "XS", 'swift-framework-plugin' ),
                "param_name"  => "col_xs",
                "holder"      => 'indicator',
                "value"       => array('', '1', '2', '3','4','5', '6', '7', '8', '9', '10', '11', '12'),
                "description" => __( "XS", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "SM", 'swift-framework-plugin' ),
                "param_name"  => "col_sm",
                "holder"      => 'indicator',
                "value"       => array('1', '2', '3','4','5', '6', '7', '8', '9', '10', '11', '12'),
                "description" => __( "SM", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "MD", 'swift-framework-plugin' ),
                "param_name"  => "col_md",
                "holder"      => 'indicator',
                "value"       => array('', '1', '2', '3','4','5', '6', '7', '8', '9', '10', '11', '12'),
                "description" => __( "MD", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "LG", 'swift-framework-plugin' ),
                "param_name"  => "col_lg",
                "holder"      => 'indicator',
                "value"       => array('', '1', '2', '3','4','5', '6', '7', '8', '9', '10', '11', '12'),
                "description" => __( "LG", 'swift-framework-plugin' )
            ),
        )
    ) );
