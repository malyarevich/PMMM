<?php

    /*
    *
    *   Swift Page Builder - Row Shortcode
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_row extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {
            $row_el_class = $el_class = $minimize_row = $width = $row_bg_color = $row_top_style = $row_bottom_style = $row_padding_vertical = $row_padding_horizontal = $row_margin_vertical = $remove_element_spacing = $el_position = $animation_output = $custom_css = $rowId = '';
            $row_classes    = array();
            $styles         = array();
            $inner_styles   = array();
            $parallax_layer_styles = array();

            extract( shortcode_atts( array(
                'wrap_type'               => 'content-width',
                'row_bg_color'            => '',
                'color_row_height'        => '',
                'row_style'               => '',
                'row_id'                  => '',
                'row_name'                => '',
                'row_header_style'        => '',
                'row_top_style'           => '',
                'row_bottom_style'        => '',
                'row_padding_vertical'    => '',
                'row_padding_horizontal'  => '',
                'row_margin_vertical'     => '0',
                'row_overlay_opacity'     => '0',
                'remove_element_spacing'  => '',
                'vertical_center'         => '',
                'row_col_spacing'         => '',
                'row_col_pos'             => 'default',
                'row_col_content_pos'     => '',
                'row_col_equal_heights'   => '',
                'row_bg_type'             => '',
                'bg_image'                => '',
                'bg_video_mp4'            => '',
                'bg_video_webm'           => '',
                'bg_video_ogg'            => '',
                'bg_video_loop'           => 'yes',
                'parallax_video_height'   => 'window-height',
                'parallax_image_height'   => 'content-height',
                'parallax_video_overlay'  => 'none',
                'parallax_image_movement' => 'fixed',
                'parallax_image_speed'    => '0.5',
                'bg_type'                 => '',
                'row_expanding'           => 'no',
                'row_expading_text_closed' => '',
                'row_expading_text_open'  => '',
                'row_animation'           => '',
                'row_animation_delay'     => '',
                'responsive_vis'          => '',
                'row_responsive_vis'      => '',
                'row_el_class'            => '',
                'el_position'             => '',
                'width'                   => '1/1',
                'custom_css'              => '',
                'el_class'                => ''
            ), $atts ) );


            // Enqueue scripts 
            if ( $parallax_image_movement == "parallax" || $parallax_image_movement == "stellar" ) {
                wp_enqueue_script( 'parallax' );
            }
            if ( $row_col_equal_heights == "yes" ) {
                wp_enqueue_script( 'matchHeight' );
            }

            // legacy checks
            $legacy = false;
            if ($row_responsive_vis == "" && $responsive_vis != "") {
                $row_responsive_vis = $responsive_vis;
            }
            $responsive_vis = str_replace( "_", " ", $row_responsive_vis );
            if ( $el_class != '' ) {  
                $row_el_class = $el_class;
            }
            if ( $vertical_center == true || $vertical_center == "true" ) {
                $row_col_content_pos = "center";
                $legacy = true;
            }
            if ( $wrap_type == "content-width" ) {
                $legacy = true;
                $wrap_type = 'full-width-contained';
            } else if ( $wrap_type == "full-width" ) {
                $legacy = true;
                $wrap_type = 'full-width-stretch';
            }

            // row classes
            $row_classes[]  = 'spb-row';
            $row_classes[]  = $this->getExtraClass( $row_el_class );
            $row_classes[]  = $responsive_vis;
            $orig_width     = $width;
            $width          = spb_translateColumnWidthToSpan( $width );
            $img_url        = wp_get_attachment_image_src( $bg_image, 'full' );
            if ( $remove_element_spacing == "yes" ) {
                $row_classes[] = 'spb-remove-element-spacing';
            }
            if ( $row_col_pos != "default" && !$legacy ) {
                $row_classes[] = 'spb-row-flex';
            }
            if ( $row_col_spacing != "" && $row_col_spacing != "0" ) {
                $row_classes[] = 'spb-row-col-spacing';
            }

            // Row background colour
            if ( $row_bg_color != "" ) {
                $styles[] = 'background-color:' . $row_bg_color . ';';
            }
            if ( $bg_type == "pattern" ) {
                $row_classes[] = 'row-bg-img-pattern';  
            }
            if ( $custom_css != "" ) {
                $styles[] = $custom_css;
                // Row background image
                if ( $row_bg_type != "color" && isset( $img_url ) && $img_url[0] != "" && !( $parallax_image_movement == "parallax" || $parallax_image_movement == "stellar" ) ) {
                    $styles[] = 'background-image: url(' . $img_url[0] . ');';
                    if ( $bg_type == "cover" ) {
                        $styles[] = 'background-size: cover;';  
                    }
                }    
            } else {
                // Row padding/margin
                if ( $row_padding_vertical != "" ) {
                    $inner_styles[] = 'padding-top:' . $row_padding_vertical . 'px;';
                    $inner_styles[] = 'padding-bottom:' . $row_padding_vertical . 'px;';
                }
                if ( $row_padding_horizontal != "" ) {
                    $styles[] = 'padding-left:' . $row_padding_horizontal . '%;';
                    $styles[] = 'padding-right:' . $row_padding_horizontal . '%;';
                }
                if ( $row_margin_vertical != "" ) {
                    $styles[] = 'margin-top:' . $row_margin_vertical . 'px;';
                    $styles[] = 'margin-bottom:' . $row_margin_vertical . 'px;';
                }

                // Row background image
                if ( $row_bg_type != "color" && isset( $img_url ) && $img_url[0] != "" && !( $parallax_image_movement == "parallax" || $parallax_image_movement == "stellar" ) ) {
                    $styles[] = 'background-image: url(' . $img_url[0] . ');';
                }    
            }

            // Row Parallax
            if ( $row_bg_type == "image" && ($parallax_image_movement == "parallax" || $parallax_image_movement == "stellar") ) {
                $row_classes[] = 'spb-row-parallax';
                $parallax_layer_styles[] = 'background-image: url(' . $img_url[0] . ');';
            }

            // Row animation
            if ( $row_animation != "" && $row_animation != "none" ) {
                $row_classes[] = 'spb-animation';
            }

            // Row height
            $row_height = "content-height";
            if ( $row_bg_type == "color" && $color_row_height != "" ) {
                $row_height = $color_row_height;
            } else if ( $row_bg_type == "image" && $parallax_image_height != "" ) {
                $row_height = $parallax_image_height;
            } if ( $row_bg_type == "video" && $parallax_video_height != "" ) {
                $row_height = $parallax_video_height;
            }

            // Data attributes
            $wrap_type = apply_filters( 'spb_row_wrap_type', $wrap_type );
            $row_data_atts = array();
            $row_data_atts[] = 'data-row-type="' . $row_bg_type. '"';
            $row_data_atts[] = 'data-wrap="' . $wrap_type. '"';
            $row_data_atts[] = 'data-image-movement="' . $parallax_image_movement . '"';
            if ( $wrap_type == "full-width-stretch" ) {
            $row_data_atts[] = 'data-content-stretch="true"';
            } else {
            $row_data_atts[] = 'data-content-stretch="false"';    
            }
            $row_data_atts[] = 'data-row-height="' . $row_height . '"';
            if ( $row_style != "" ) {
            $row_data_atts[] = 'data-row-style="' . $row_style . '"';
            }
            if ( $row_col_spacing != "" ) {
                $row_data_atts[] = 'data-col-spacing="' . $row_col_spacing . '"';
            }
            $row_data_atts[] = 'data-col-v-pos="' . $row_col_pos . '"';
            if ( $row_col_content_pos != "" ) {
                $row_data_atts[] = 'data-col-content-pos="' . $row_col_content_pos . '"';  
            }
            if ( $row_col_equal_heights == "yes" ) {
            $row_data_atts[] = 'data-col-equal-heights="true"';                
            }
            if ( ($parallax_image_movement == "parallax" || $parallax_image_movement == "stellar") && $parallax_image_speed != "" ) {
                $row_data_atts[] = 'data-parallax-speed="' . $parallax_image_speed . '"';   
            }
            if ( $row_top_style != "" && $row_top_style != "none" ) {
            $row_data_atts[] = 'data-top-style="' . $row_top_style . '"';
            }
            if ( $row_bottom_style != "" && $row_bottom_style != "none" ) {
            $row_data_atts[] = 'data-bottom-style="' . $row_bottom_style . '"';
            }
            if ( $row_animation != "" && $row_animation != "none" ) {
                $row_data_atts[] = 'data-animation="' . $row_animation . '"';
                $row_data_atts[] = 'data-delay="' . $row_animation_delay . '"';
            }
            if ( $legacy ) {
                $row_data_atts[] = 'data-legacy="true"';
            }
            $row_data_atts = apply_filters('spb_row_data_atts', $row_data_atts);


            // Start output buffer
            ob_start();

            // Start Row
            if ( $row_id != "" ) {
                $rowId = 'id="' . $row_id . '" data-rowname="' . $row_name . '" data-header-style="' . $row_header_style . '"';
            } else {
                $rowId = 'data-header-style="' . $row_header_style . '"';
            }
            echo $this->startRow( $el_position, '', true, $rowId );

            // Expanding Row
            if ( $row_expanding == "yes" ) {
                $row_classes[] = 'spb-row-expanding';
            ?>
                <a href="#" class="spb-row-expand-text container" data-closed-text="<?php echo $row_expading_text_closed; ?>" data-open-text="<?php echo $row_expading_text_open; ?>">
                    <span><?php echo $row_expading_text_closed; ?></span>
                </a>
            <?php } ?>

            <div class="<?php echo implode(' ', $row_classes); ?>" <?php echo implode(' ', $row_data_atts); ?> style="<?php echo implode('', $styles); ?>">

                <?php if ( $row_top_style == "slant-ltr" || $row_top_style == "slant-rtl" ) { ?>
                <div class="spb_row_slant_spacer"></div>
                <?php } ?>

                <?php if ( $row_bg_type == "video" ) : 
                    $loop = 'loop';
                    if ( $bg_video_loop == "no" ) {
                        $loop = '';
                    }
                ?>
                    
                <div class="video-wrap">
                    <?php if ( $img_url ) : ?>
                        <video class="parallax-video" poster="<?php echo $img_url[0]; ?>" preload="auto" autoplay muted <?php echo $loop; ?>>
                    <?php else : ?>
                        <video class="parallax-video" preload="auto" autoplay muted <?php echo $loop; ?>>
                    <?php endif; ?>

                        <?php if ( $bg_video_mp4 != "" ) : ?>
                            <source src="<?php echo $bg_video_mp4; ?>" type="video/mp4">
                        <?php endif; ?>
                        <?php if ( $bg_video_webm != "" ) : ?>
                            <source src="<?php echo $bg_video_webm; ?>" type="video/webm">
                        <?php endif; ?>
                        <?php if ( $bg_video_ogg != "" ) : ?>
                            <source src="<?php echo $bg_video_ogg; ?>" type="video/ogg">
                        <?php endif; ?>

                    </video>
                    
                    <?php if ( $parallax_video_overlay != "color" ) { ?>
                        <div class="row-overlay video-overlay overlay-<?php echo $parallax_video_overlay; ?>"></div>
                    <?php } ?>
                </div><!-- .video-wrap -->

                <?php endif;

                if ( $row_overlay_opacity != "0" && $parallax_video_overlay == "color" ) :
                    $opacity = intval( $row_overlay_opacity, 10 ) / 100;
                ?>
                <div class="row-overlay" style="<?php printf( 'background-color:%1$s;opacity:%2$s', $row_bg_color, $opacity ) ?>"></div>
                <?php elseif ( $row_overlay_opacity != "0" ) : ?>
                <div class="row-overlay overlay-<?php echo $parallax_video_overlay; ?>"></div>
                <?php endif; ?>

                <?php if ( $row_bg_type == "image" && ($parallax_image_movement == "parallax" || $parallax_image_movement == "stellar") ) : ?>
                <div class="spb-row-parallax-layer-wrap">
                    <div class="spb-row-parallax-layer" style="<?php echo implode('', $parallax_layer_styles); ?>"></div>
                </div>
                <?php endif; ?>

                <div class="spb_content_element clearfix" style="<?php echo implode('', $inner_styles); ?>">
                    <?php
                        // Check if inner shortcodes are columns
                        if ( strpos($content, '[spb_column') !== false ) {
                            // column found, just output content
                            echo spb_format_content( $content );
                        } else {
                            // no column found, output wrapper
                            if ( substr_count($content, '[spb') > 1) {
                                echo '<div class="spb-row-no-cols-wrapper spb-row-multi-col clearfix">';
                            } else {
                                echo '<div class="spb-row-no-cols-wrapper clearfix">';
                            }
                            echo spb_format_content( $content );
                            echo '</div><!-- .spb-row-no-cols-wrapper -->';
                        }
                    ?>
                </div><?php echo $this->endBlockComment( $width ); ?>

                <?php if ( $row_bottom_style == "slant-ltr" || $row_bottom_style == "slant-rtl" ) { ?>
                <div class="spb_row_slant_spacer"></div>
                <?php } ?>

            </div><!-- .sb-row -->

            <?php
            // End row
            echo $this->endRow( $el_position, '', true, true );
            ?>

            <div class="spb-row-sizer"></div>

        <?php 
            // Return output buffer
            return ob_get_clean();
        }

        public function contentAdmin( $atts, $content = null ) {
            $width = $custom_css_percentage = $row_el_class = $el_class = $bg_color = $element_name = $minimize_row = $row_responsive_vis = $padding_vertical = '';
            extract( shortcode_atts( array(
                'wrap_type'               => 'content-width',
                'row_el_class'            => '',
                'row_bg_color'            => '',
                'color_row_height'        => '',
                'row_style'               => '',
                'row_top_style'           => '',
                'row_bottom_style'        => '',
                'row_padding_vertical'    => '',
                'row_padding_horizontal'  => '',
                'row_margin_vertical'     => '0',
                'row_overlay_opacity'     => '0',
                'remove_element_spacing'  => '',
                'vertical_center'         => '',
                'row_col_pos'             => 'default',
                'row_col_content_pos'     => '',
                'row_col_equal_heights'   => '',
                'row_col_spacing'         => '',
                'row_id'                  => '',
                'row_name'                => '',
                'row_header_style'        => '',
                'row_bg_type'             => '',
                'bg_image'                => '',
                'bg_video_mp4'            => '',
                'bg_video_webm'           => '',
                'bg_video_ogg'            => '',
                'bg_video_loop'           => 'yes',
                'parallax_video_height'   => 'window-height',
                'parallax_image_height'   => 'content-height',
                'parallax_video_overlay'  => 'none',
                'parallax_image_movement' => 'fixed',
                'parallax_image_speed'    => '0.5',
                'bg_type'                 => '',
                'row_expanding'           => '',
                'row_expading_text_closed' => '',
                'row_expading_text_open'  => '',
                'row_animation'           => '',
                'row_animation_delay'     => '',
                'responsive_vis'          => '',
                'row_responsive_vis'      => '',
                'el_position'             => '',
                'element_name'            => '',
                'minimize_row'            => '',
                'width'                   => 'span12',
                'custom_css'              => '',
                'simplified_controls'     => '',
                'custom_css_percentage'   => '',
                'border_color_global'     => '',
                'border_styling_global'   => '',
                'back_color_global'       => '',
                'border_styling_global'   => '',
                'el_class'                => ''
            ), $atts ) );

            if ( $element_name == '' ){
                $element_name = __( "Row", 'swift-framework-plugin' );
            }

            $output = ''; 

            $output .= '<div data-element_type="spb_row" class="spb_row spb_sortable span12 spb_droppable not-column-inherit">';
            $output .= '<input type="hidden" class="spb_sc_base" name="element_name-spb_row" value="spb_row">';
            $output .= '<div class="controls sidebar-name"><span class="asset-name">' . $element_name . '</span>';
            $output .=  $this->getResponsiveIndicatorHtml( $row_responsive_vis );
            $output .= '<div class="controls_right row_controls"><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a><a class="element-save" href="#" title="Save"><span class="icon-save"></span></a><a class="column_clone" href="#" title="Duplicate"><span class="icon-duplicate"></span></a><a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a>';

            if( $minimize_row == 'yes' ){
                $output .= ' <a class="column_minimize" href="#" title="Minimize" style="display:none;"><i class="fa-minus"></i></a><a class="column_maximize" href="#" title="Maximize"><i class="fa-plus"></i></a></div></div><div class="spb_element_wrapper" style="display:none;">';    
            }else{
                $output .= ' <a class="column_minimize" href="#" title="Minimize"><i class="fa-minus"></i></a><a class="column_maximize" href="#" title="Maximize" style="display:none;"><i class="fa-plus"></i></a></div></div><div class="spb_element_wrapper">';
            }
            
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

            return $output;
        }
    }

    /* PARAMS
    ================================================== */
    $params = array(
         array(
            "type"       => "section_tab",
            "param_name" => "general_tab",
            "heading"    => __( "General", 'swift-framework-plugin' ),
        ),
        array(
               "type"        => "textfield",  
               "heading"     => __( "Element Name", 'swift-framework-plugin' ),
               "param_name"  => "element_name",
               "value"       => "",
               "description" => __( "Element Name. Use it to easily recognize the elements in the page builder mode.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Wrap type", 'swift-framework-plugin' ),
            "param_name"  => "wrap_type",
            "value"       => array(
                __( 'Standard Width', 'swift-framework-plugin' ) => "standard-width",
                __( 'Full Width (Contained)', 'swift-framework-plugin' ) => "full-width-contained",
                __( 'Full Width (Stretched)', 'swift-framework-plugin' ) => "full-width-stretch",
            ),
            "std"         => 'full-width-contained',
            "description" => __( "Select if you want to row to wrap the content to the grid, or if you want the content to be edge to edge.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Row Background Type", 'swift-framework-plugin' ),
            "param_name"  => "row_bg_type",
            "value"       => array(
                __( "Color", 'swift-framework-plugin' ) => "color",
                __( "Image", 'swift-framework-plugin' ) => "image",
                __( "Video", 'swift-framework-plugin' ) => "video"
            ),
            "description" => __( "Choose whether you want to use an image or video for the background of the parallax. This will decide what is used from the options below.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "colorpicker",
            "heading"     => __( "Background color", 'swift-framework-plugin' ),
            "param_name"  => "row_bg_color",
            "value"       => "",
            "description" => __( "Select a background colour for the row here.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Color Row Height", 'swift-framework-plugin' ),
            "param_name"  => "color_row_height",
            "value"       => array(
                __( "Content Height", 'swift-framework-plugin' ) => "content-height",
                __( "Window Height", 'swift-framework-plugin' )  => "window-height"
            ),
            "required"       => array("row_bg_type", "=", "color"),
            "description" => __( "If you are using this as a coloured row asset, then please choose whether you'd like asset to sized based on the content height or the window height.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Row Style", 'swift-framework-plugin' ),
            "param_name"  => "row_style",
            "value"       => array(
                '' => '',
                __( "Light", 'swift-framework-plugin' ) => "light",
                __( "Dark", 'swift-framework-plugin' )  => "dark"
            ),
            "description" => __( "Set the colour style for the row here, e.g. if set to Light, then inner element titles will be light (for use on dark background rows).", 'swift-framework-plugin' )
        ),
        array(
            "type"       => "section",
            "param_name" => "section_bg_image_options",
            "heading"    => __( "Row Background Image Options", 'swift-framework-plugin' ),
            "required"       => array("row_bg_type", "=", "image"),
        ),
        array(
            "type"       => "section",
            "param_name" => "section_bg_video_options",
            "heading"    => __( "Row Background Video Options", 'swift-framework-plugin' ),
            "required"       => array("row_bg_type", "=", "video"),
        ),
        array(
            "type"        => "attach_image",
            "heading"     => __( "Background Image", 'swift-framework-plugin' ),
            "param_name"  => "bg_image",
            "value"       => "",
            "required"       => array("row_bg_type", "!=", "color"),
            "description" => "Choose an image to use as the background for the parallax area. This is also used as the fallback if using the video display."
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Background Image Type", 'swift-framework-plugin' ),
            "param_name"  => "bg_type",
            "value"       => array(
                __( "Cover", 'swift-framework-plugin' )   => "cover",
                __( "Pattern", 'swift-framework-plugin' ) => "pattern"
            ),
            "required"       => array("row_bg_type", "=", "image"),
            "description" => __( "If you're uploading an image that you want to spread across the whole asset, then choose cover. Else choose pattern for an image you want to repeat.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Parallax Image Height", 'swift-framework-plugin' ),
            "param_name"  => "parallax_image_height",
            "value"       => array(
                __( "Content Height", 'swift-framework-plugin' ) => "content-height",
                __( "Window Height", 'swift-framework-plugin' )  => "window-height"
            ),
            "required"       => array("row_bg_type", "=", "image"),
            "description" => __( "If you are using this as an image parallax asset, then please choose whether you'd like asset to sized based on the content height or the height of the viewport window.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Background Movement", 'swift-framework-plugin' ),
            "param_name"  => "parallax_image_movement",
            "value"       => array(
                __( "Fixed", 'swift-framework-plugin' )             => "fixed",
                __( "Scroll", 'swift-framework-plugin' )            => "scroll",
                __( "Parallax", 'swift-framework-plugin' )          => "stellar",
            ),
            "required"       => array("row_bg_type", "=", "image"),
            "description" => __( "Choose the type of movement you would like the parallax image to have. Fixed means the background image is fixed on the page, Scroll means the image will scroll will the page, and stellar makes the image move at a seperate speed to the page, providing a layered effect.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Parallax Image Speed (Parallax Only)", 'swift-framework-plugin' ),
            "param_name"  => "parallax_image_speed",
            "value"       => array(
                __( "Standard", 'swift-framework-plugin' ) => "standard",
                __( "Fast", 'swift-framework-plugin' )     => "fast",
                __( "Slow", 'swift-framework-plugin' )     => "slow",
            ),
            "required"       => array("row_bg_type", "=", "image"),
            "description" => "The speed at which the parallax image moves in relation to the page scrolling."
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Background Video (MP4)", 'swift-framework-plugin' ),
            "param_name"  => "bg_video_mp4",
            "value"       => "",
            "required"       => array("row_bg_type", "=", "video"),
            "description" => "Provide a video URL in MP4 format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Background Video (WebM)", 'swift-framework-plugin' ),
            "param_name"  => "bg_video_webm",
            "value"       => "",
            "required"       => array("row_bg_type", "=", "video"),
            "description" => "Provide a video URL in WebM format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Background Video (Ogg)", 'swift-framework-plugin' ),
            "param_name"  => "bg_video_ogg",
            "value"       => "",
            "required"       => array("row_bg_type", "=", "video"),
            "description" => "Provide a video URL in OGG format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
        ),  
        array(
            "type"        => "buttonset",
            "heading"     => __( "Background Video Loop", 'swift-framework-plugin' ),
            "param_name"  => "bg_video_loop",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' )  => "yes",
                __( "No", 'swift-framework-plugin' ) => "no"
            ),
            "buttonset_on"  => "yes",
            "std"         => 'yes',
            "required"       => array("row_bg_type", "=", "video"),
            "description" => "Choose if you would like the background video to be looped."
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Parallax Video Height", 'swift-framework-plugin' ),
            "param_name"  => "parallax_video_height",
            "value"       => array(
                __( "Window Height", 'swift-framework-plugin' )  => "window-height",
                __( "Content Height", 'swift-framework-plugin' ) => "content-height"
            ),
            "required"       => array("row_bg_type", "=", "video"),
            "description" => __( "If you are using this as a video parallax asset, then please choose whether you'd like asset to sized based on the content height or the video height.", 'swift-framework-plugin' )
        ),
    );

    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Responsive Visiblity", 'swift-framework-plugin' ),
        "param_name"  => "row_responsive_vis",
        "holder"      => 'indicator',
        "value"       => spb_responsive_vis_list(),
        "description" => __( "Set the responsive visiblity for the row, if you would only like it to display on certain display sizes.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Row ID", 'swift-framework-plugin' ),
        "param_name"  => "row_id",
        "value"       => "",
        "description" => __( "If you wish to add an ID to the row, then add it here. You can then use the id to deep link to this section of the page. This is also used for one page navigation. NOTE: Make sure this is unique to the page!!", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Row Section Name", 'swift-framework-plugin' ),
        "param_name"  => "row_name",
        "value"       => "",
        "description" => __( "This is used for the one page navigation, to identify the row. If this is left blank, then the row will be left off of the one page navigation.", 'swift-framework-plugin' )
    );
    $params[] = array(  
        "type"        => "textfield",
        "heading"     => __( "Row Extra Class", 'swift-framework-plugin' ),
        "param_name"  => "row_el_class",
        "value"       => "",
        "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
    );

    $params[] = array(
        "type"       => "section_tab",
        "param_name" => "display_options_tab",
        "heading"    => __( "Display", 'swift-framework-plugin' ),
    );

    if ( spb_theme_supports('transparent-sticky-header') ) {
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Header Style", 'swift-framework-plugin' ),
            "param_name"  => "row_header_style",
            "value"       => array(
                "" => "",
                __( "Light", 'swift-framework-plugin' ) => "light",
                __( "Dark", 'swift-framework-plugin' ) => "dark",
            ),
            "description" => __( "If you have the transparent sticky header option enabled in the page meta options, then you can set the header style when scrolling over this row.", 'swift-framework-plugin' )
        );
    }

    if ( spb_theme_supports('advanced-row-styling') ) {
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Row top style", 'swift-framework-plugin' ),
            "param_name"  => "row_top_style",
            "value"       => array(
                __( "None", 'swift-framework-plugin' )             => "none",
                __( "Slant Left-to-right", 'swift-framework-plugin' ) => "slant-ltr",
                __( "Slant Right-to-left", 'swift-framework-plugin' ) => "slant-rtl",
            ),
            "description" => __( "Choose the top style for the row, or none.", 'swift-framework-plugin' )
        );
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Row bottom style", 'swift-framework-plugin' ),
            "param_name"  => "row_bottom_style",
            "value"       => array(
                __( "None", 'swift-framework-plugin' )             => "none",
                __( "Slant Left-to-right", 'swift-framework-plugin' ) => "slant-ltr",
                __( "Slant Right-to-left", 'swift-framework-plugin' ) => "slant-rtl",
            ),
            "description" => __( "Choose the bottom style for the row, or none.", 'swift-framework-plugin' )
        );
    }

    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Row Overlay style", 'swift-framework-plugin' ),
        "param_name"  => "parallax_video_overlay",
        "value"       => array(
            __( "None", 'swift-framework-plugin' )             => "none",
            __( "Color", 'swift-framework-plugin' )            => "color",
            __( "Light Grid", 'swift-framework-plugin' )       => "lightgrid",
            __( "Dark Grid", 'swift-framework-plugin' )        => "darkgrid",
            __( "Light Grid (Fat)", 'swift-framework-plugin' ) => "lightgridfat",
            __( "Dark Grid (Fat)", 'swift-framework-plugin' )  => "darkgridfat",
            __( "Light Diagonal", 'swift-framework-plugin' )   => "diaglight",
            __( "Dark Diagonal", 'swift-framework-plugin' )    => "diagdark",
            __( "Light Vertical", 'swift-framework-plugin' )   => "vertlight",
            __( "Dark Vertical", 'swift-framework-plugin' )    => "vertdark",
            __( "Light Horizontal", 'swift-framework-plugin' ) => "horizlight",
            __( "Dark Horizontal", 'swift-framework-plugin' )  => "horizdark",
        ),
        "description" => __( "If you would like an overlay to appear on top of the image/video, then you can select it here.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "uislider",
        "heading"     => __( "Row Overlay Opacity", 'swift-framework-plugin' ),
        "param_name"  => "row_overlay_opacity",
        "value"       => "30",
        "step"        => "5",
        "min"         => "0",
        "max"         => "100",
        "description" => __( "Adjust the overlay capacity if using an image or video option. This only has effect for the color overlay style option, and shows an overlay over the image/video at the desired opacity. Percentage.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "buttonset",
        "heading"     => __( "Remove Element Spacing", 'swift-framework-plugin' ),
        "param_name"  => "remove_element_spacing",
        "value"       => array(
            __( 'No', 'swift-framework-plugin' )  => "no",
            __( 'Yes', 'swift-framework-plugin' ) => "yes"
        ),
        "buttonset_on"  => "yes",
        "description" => __( "Enable this option if you wish to remove all spacing from the elements within the row.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Row Column Spacing", 'swift-framework-plugin' ),
        "param_name"  => "row_col_spacing",
        "value"       => array(
            __( '0px', 'swift-framework-plugin' )  => "0",
            __( '2px', 'swift-framework-plugin' )  => "2",
            __( '4px', 'swift-framework-plugin' )  => "4",
            __( '6px', 'swift-framework-plugin' )  => "6",
            __( '8px', 'swift-framework-plugin' )  => "8",
            __( '10px', 'swift-framework-plugin' ) => "10",
            __( '20px', 'swift-framework-plugin' ) => "20",
            __( '30px', 'swift-framework-plugin' ) => "30",
            __( '40px', 'swift-framework-plugin' ) => "40",
        ),
        "std" => "center",
        "description" => __( "Select the spacing between each column.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Row Column Position", 'swift-framework-plugin' ),
        "param_name"  => "row_col_pos",
        "value"       => array(
            __( 'Default', 'swift-framework-plugin' )  => "default",
            __( 'Top', 'swift-framework-plugin' )  => "top",
            __( 'Center', 'swift-framework-plugin' ) => "center",
            __( 'Bottom', 'swift-framework-plugin' ) => "bottom",
            __( 'Stretch', 'swift-framework-plugin' ) => "stretch",
        ),
        "std" => "default",
        "description" => __( "Select the columns position within the row. Please note: this setting won't take effect on older browsers such as IE9 and below.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Column Content Position", 'swift-framework-plugin' ),
        "param_name"  => "row_col_content_pos",
        "value"       => array(
            __( 'Default', 'swift-framework-plugin' ) => "",
            __( 'Top', 'swift-framework-plugin' )  => "top",
            __( 'Center', 'swift-framework-plugin' ) => "center",
            __( 'Bottom', 'swift-framework-plugin' ) => "bottom"
        ),
        "std" => "",
        "description" => __( "Select the content position within each column.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "buttonset",
        "heading"     => __( "Column Equal Heights", 'swift-framework-plugin' ),
        "param_name"  => "row_col_equal_heights",
        "value"       => array(
            __( "No", 'swift-framework-plugin' )             => "no",
            __( "Yes", 'swift-framework-plugin' )             => "yes"
        ),
        "buttonset_on"  => "yes",
        "std" => "no",
        "description" => __( "Set each inner column to be equal heights.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "buttonset",
        "heading"     => __( "Expanding Row", 'swift-framework-plugin' ),
        "param_name"  => "row_expanding",
        "value"       => array(
            __( "No", 'swift-framework-plugin' )             => "no",
            __( "Yes", 'swift-framework-plugin' )             => "yes"
        ),
        "buttonset_on"  => "yes",
        "description" => __( "If you would like the content to be hidden on load, and have a text link to expand the content, then select Yes.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Expanding Link Text (Content Closed)", 'swift-framework-plugin' ),
        "param_name"  => "row_expading_text_closed",
        "value"       => "",
        "required"       => array("row_expanding", "=", "yes"),
        "description" => __( "This is the text that is shown when the expanding row is closed.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Expanding Link Text (Content Open)", 'swift-framework-plugin' ),
        "param_name"  => "row_expading_text_open",
        "value"       => "",
        "required"       => array("row_expanding", "=", "yes"),
        "description" => __( "This is the text that is shown when the expanding row is open.", 'swift-framework-plugin' )
    );
    $params[] = array(
            "type"       => "section_tab",
            "param_name" => "animation_tab",
            "heading"    => __( "Animation", 'swift-framework-plugin' ),
    );
    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Intro Animation", 'swift-framework-plugin' ),
        "param_name"  => "row_animation",
        "value"       => spb_animations_list(),
        "description" => __( "Select an intro animation for the row which will show it when it appears within the viewport.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Animation Delay", 'swift-framework-plugin' ),
        "param_name"  => "row_animation_delay",
        "value"       => "0",
        "description" => __( "If you wish to add a delay to the animation, then you can set it here (ms).", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "buttonset",
        "heading"     => __( "Minimize Row", 'swift-framework-plugin' ),
        "param_name"  => "minimize_row",
        "value"       => array(
                    __( "Yes", 'swift-framework-plugin' )  => "yes",
                    __( "No", 'swift-framework-plugin' ) => "no"
                      
        ),
        "buttonset_on"  => "yes",
        "description" => "Choose if you would like to minimize the Row inside the Page Builder editing."
    );

    /* SHORTCODE MAP
    ================================================== */
    SPBMap::map( 'spb_row', array(
        "name"            => __( "Row", 'swift-framework-plugin' ),
        "base"            => "spb_row",
        "controls"        => "edit_delete", 
        "class"           => "spb_row spb_tab_layout",
        "icon"            => "icon-row",
        "content_element" => true,
        "template_name"   => "row",
        "params"          => $params
    ) );
