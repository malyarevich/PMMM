<?php

    /*
*
*	Swift Page Builder - Multilayer Parallax Shortcode
*	------------------------------------------------
*	Swift Framework
* 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
*
*/   

    class SwiftPageBuilderShortcode_spb_multilayer_parallax extends SwiftPageBuilderShortcode {

        public function contentAdmin( $atts, $content = null ) {
            $width                = $custom_markup = '';
            $shortcode_attributes = array( 'width' => '1/1' );
            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {

                    if ( is_string( $param['value'] ) ) {
                        $shortcode_attributes[ $param['param_name'] ] = __( $param['value'], 'swift-framework-plugin' );
                    } else {
                        $shortcode_attributes[ $param['param_name'] ] = $param['value'];
                    }
                } else if ( $param['param_name'] == 'content' && $content == null ) {
                    $content = __( $param['value'], 'swift-framework-plugin' );
                }
            }
            extract( shortcode_atts(
                $shortcode_attributes
                , $atts ) );

            $output = '';

            $tmp = '';

            $output .= do_shortcode( $content );
            $elem = $this->getElementHolder( $width );

            $iner = '';
            foreach ( $this->settings['params'] as $param ) {
                $custom_markup = '';
                $param_value   = isset( ${$param['param_name']} ) ? ${$param['param_name']} : null;

                if ( is_array( $param_value ) ) {
                    // Get first element from the array
                    reset( $param_value );
                    $first_key   = key( $param_value );
                    $param_value = $param_value[ $first_key ];
                }
                $iner .= $this->singleParamHtmlHolder( $param, $param_value );
            }


            if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
                if ( $content != '' ) {
                    $custom_markup = str_ireplace( "%content%", $tmp . $content, $this->settings["custom_markup"] );
                } else if ( $content == '' && isset( $this->settings["default_content"] ) && $this->settings["default_content"] != '' ) {
                    $custom_markup = str_ireplace( "%content%", $this->settings["default_content"], $this->settings["custom_markup"] );
                }

                $iner .= do_shortcode( $custom_markup );
            }
            $elem   = str_ireplace( '%spb_element_content%', $iner, $elem );
            $output = $elem;

            return $output;
        }

        public function content( $atts, $content = null ) {

            $title = $size = $width = $el_class = '';
            extract( shortcode_atts( array(
                'width'       => '1/1',
                'fullscreen'  => 'false',
                'maxheight'   => '600',
                'x_scalar'    => '20',
                'y_scalar'    => '20',
                'el_class'    => '',
                'el_position' => ''
            ), $atts ) );

            // Enqueue script
            wp_enqueue_script('mlparallax');

            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            $size     = str_replace( array( 'px', ' ' ), array( '', '' ), $size );

            global $sf_ml_parallax_layer;
            $sf_ml_parallax_layer = 1000;

            $output .= "\n\t" . '<div class="spb_multilayer_parallax spb_content_element ' . $width . $el_class . '" data-xscalar="' . $x_scalar . '" data-yscalar="' . $y_scalar . '" data-fullscreen="' . $fullscreen . '" data-max-height="' . $maxheight . '" style="height'.$maxheight.';">';
            $output .= "\n\t\t\t" . do_shortcode( $content );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );

            global $sf_include_ml_parallax;
            $sf_include_ml_parallax = true;

            return $output;
        }
  
    }

    SPBMap::map( 'spb_multilayer_parallax', array(
        "name"            => __( "Multilayer Parallax", 'swift-framework-plugin' ),
        "base"            => "spb_multilayer_parallax",
        "controls"        => "full",
        "class"           => "spb_multilayer_parallax spb_tab_media",
        "icon"            => "icon-multilayer-parallax",
        "params"          => array(
            array(
                "type"        => "buttonset",
                "heading"     => __( "Fullscreen", 'swift-framework-plugin' ),
                "param_name"  => "fullscreen",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "true",
                    __( 'No', 'swift-framework-plugin' )  => "false"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Choose if you would like the slider to be window height.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Slider Max Height", 'swift-framework-plugin' ),
                "param_name"  => "maxheight",
                "value"       => "600",
                "description" => __( "Set the maximum height that the Swift Slider should display at (optional) (no px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Horizontal scale movement", 'swift-framework-plugin' ),
                "param_name"  => "x_scalar",
                "value"       => "20",
                "description" => __( "Multiplies the X-axis input motion by this value, increasing or decreasing the sensitivity of the layer motion.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Vertical scale movement", 'swift-framework-plugin' ),
                "param_name"  => "y_scalar",
                "value"       => "20",
                "description" => __( "Multiplies the Y-axis input motion by this value, increasing or decreasing the sensitivity of the layer motion.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        ),
        "custom_markup"   => '
	           <div class="spb_tabs_holder">
		              %content%
	           </div>
              <div class="container-helper"><a href="#" class="add-parallax-layer btn-floating waves-effect waves-light"><span class="icon-add"></span></a></div>',
        'default_content' => '
				[spb_multilayer_parallax_layer layer_title="' . __( "Layer 1", 'swift-framework-plugin' ) . '"]' . __( 'This is a Parallax Layer text. Click the edit button to change it.', 'swift-framework-plugin' ) . '[/spb_multilayer_parallax_layer]'
        


    ) );


    /* MULTILAYER PARALLAX LAYER
    ================================================== */

    class SwiftPageBuilderShortcode_spb_multilayer_parallax_layer extends SwiftPageBuilderShortcode {


        public function contentAdmin( $atts, $content ) {
            $custom_markup        = '';
            $shortcode_attributes = array( 'width' => '1/1' );

            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {
                    if ( is_string( $param['value'] ) ) {
                        $shortcode_attributes[ $param['param_name'] ] = __( $param['value'], 'swift-framework-plugin' );
                    } else {
                        $shortcode_attributes[ $param['param_name'] ] = $param['value'];
                    }
                } else if ( $param['param_name'] == 'content' && $content == null ) {
                    $content = __( $param['value'], 'swift-framework-plugin' );
                }
            }
            extract( shortcode_atts(
                $shortcode_attributes
                , $atts ) );

            $output = '';

            $elem = $this->getElementHolder( $width );

            $iner = '';

            foreach ( $this->settings['params'] as $param ) {

                $param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : null;

                if ( is_array( $param_value ) ) {
                    // Get first element from the array
                    reset( $param_value );
                    $first_key   = key( $param_value );
                    $param_value = $param_value[ $first_key ];


                }

                if ( $param['param_name'] == 'layer_image' && $content != '' ) {
                    $iner .= $this->singleParamHtmlHolderParallaxImage( $param, $param_value );
                } else {
                    $iner .= $this->singleParamHtmlHolder( $param, $param_value );
                }

            }

            $tmp = '';
            if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
                if ( $content != '' ) {
                    $custom_markup = "";
                } else if ( $content == '' && isset( $this->settings["default_content"] ) && $this->settings["default_content"] != '' ) {

                    $custom_markup = "";
                }

                $iner .= do_shortcode( $custom_markup );
            }
            $elem = str_ireplace( '%spb_element_content%', $iner, $elem );

            $output = $elem;
            $output = '<div class="row-fluid spb_column_container not-column-inherit not-sortable">' . $output . '</div>';


            return $output;
        }

        public function singleParamHtmlHolder( $param, $value ) {
            $output = '';

            $param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
            $type       = isset( $param['type'] ) ? $param['type'] : '';
            $class      = isset( $param['class'] ) ? $param['class'] : '';

              if ( isset( $param['holder'] ) == false || $param['holder'] == 'hidden' ) {
                $output .= '<input type="hidden" class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
                if ( ( $param['type'] ) == 'attach_image' ) {
                    $img = spb_getImageBySize( array(
                        'attach_id'  => (int) preg_replace( '/[^\d]/', '', $value ),
                        'thumb_size' => 'thumbnail'
                    ) ); 
   

                    $output .= ( $img ? $img['p_img_small'] : '<img width="66" height="66" src="' . SwiftPageBuilder::getInstance()->assetURL( 'img/blank_f7.gif' ) . '" class="attachment-thumbnail" alt="" title="" />' ) . '<a href="#" class="column_edit_trigger' . ( $img && ! empty( $img['p_img_small'][0] ) ? ' image-exists' : '' ) . '"><i class="spb-icon-single-image"></i>' . __( 'No image yet! Click here to select it now.', 'swift-framework-plugin' ) . '</a>';
                }
            } else {
                $output .= '<' . $param['holder'] . ' class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';

            }

            return $output;
        }

        public function singleParamHtmlHolderParallaxImage( $param, $value ) {

            $output = '';  

            $param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
            $type       = isset( $param['type'] ) ? $param['type'] : '';
            $class      = isset( $param['class'] ) ? $param['class'] : '';

            if ( isset( $param['holder'] ) == false || $param['holder'] == 'hidden' ) {
                $output .= '<input type="hidden" class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
                if ( ( $param['type'] ) == 'attach_image' ) {

  
                    $img = spb_getImageBySize( array(
                        'attach_id'  => (int) preg_replace( '/[^\d]/', '', $value ),
                        'thumb_size' => 'thumbnail'
                    ) );
                    $output .= ( $img ? $img['p_img_small'] : '<img width="66" height="66" src="' . SwiftPageBuilder::getInstance()->assetURL( 'img/blank_f7.gif' ) . '" class="attachment-thumbnail" alt="" title="" />' ) . '<a href="#" class="hide-layer-image column_edit_trigger' . ( $img && ! empty( $img['p_img_small'][0] ) ? ' image-exists' : '' ) . '"><i class="spb-icon-single-image"></i>' . __( 'No image yet! Click here to select it now.', 'swift-framework-plugin' ) . '</a>';
                }
            } else {
                $output .= '<' . $param['holder'] . ' class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
            }

            return $output;
        }


        protected function content( $atts, $content = null ) {

            $layer_image;
            extract( shortcode_atts( array(
                'layer_image'     => '',
                'layer_depth'     => '0.00',
                'layer_type'      => 'original',
                'layer_bg_pos'    => 'center center',
                'layer_bg_repeat' => 'no-repeat',
                'text_layer'      => 'no'
            ), $atts ) );
            $output = '';

            //$width = spb_translateColumnWidthToSpan($width);
            $img_url      = wp_get_attachment_image_src( $layer_image, 'full' );
            $inline_style = $output = "";

            //$layer_bg_pos = str_replace('_', ' ', $layer_bg_pos);

            global $sf_ml_parallax_layer;
            $sf_ml_parallax_layer --;

            $inline_style .= 'z-index: ' . $sf_ml_parallax_layer . ';';

            if ( $text_layer == "yes" ) {
	            $output .= '<div id="spb-mlp-' . $sf_ml_parallax_layer . '" class="layer container" data-depth="' . $layer_depth . '" style="' . $inline_style . '">' . "\n";
                $output .= '<div class="layer-bg content-layer" data-layer-bg-pos="' . $layer_bg_pos . '">' . do_shortcode( $content ) . '</div>' . "\n";
                $output .= '</div>' . "\n";
            } else {
            	$output .= '<div id="spb-mlp-' . $sf_ml_parallax_layer . '" class="layer slice-layer" data-depth="' . $layer_depth . '" style="' . $inline_style . '">' . "\n";
                if ( $img_url ) {
                    $output .= '<div class="layer-bg" data-layer-type="' . $layer_type . '" data-layer-bg-pos="' . $layer_bg_pos . '" data-layer-repeat="' . $layer_bg_repeat . '" style="background-image:url(' . $img_url[0] . ');"></div>' . "\n";
                }
                $output .= '</div>' . "\n";
            }

            return $output;
        }
    }

    SPBMap::map( 'spb_multilayer_parallax_layer', array(
            "name"     => __( "Multilayer Parallax Layer", 'swift-framework-plugin' ),
            "base"     => "spb_multilayer_parallax_layer",
            "class"    => "",
            "icon"     => "spb_multilayer_parallax_layer",
            "controls" => "delete_edit",
            "params"   => array(
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Title", 'swift-framework-plugin' ),
                    "param_name"  => "layer_title",
                    "value"       => "",
                    "description" => __( "It's only showed in Swift Page Builder to identify the Layers. Leave it empty if not needed.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "attach_image",
                    "holder"  => "hidden",
                    "heading"     => __( "Layer Image", 'swift-framework-plugin' ),
                    "param_name"  => "layer_image",
                    "value"       => "",
                    "description" => "Choose an image."
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Layer Type", 'swift-framework-plugin' ),
                    "param_name"  => "layer_type",
                    "value"       => array(
                        __( 'Original', 'swift-framework-plugin' ) => "original",
                        __( 'Cover', 'swift-framework-plugin' )    => "cover"
                    ),
                    "description" => __( "If you would like the image to cover the height/width of the asset, then please select the 'Cover' option - this is ideal for the background layers of the asset.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Background Position", 'swift-framework-plugin' ),
                    "param_name"  => "layer_bg_pos",
                    "value"       => array(
                        __( 'Center', 'swift-framework-plugin' )        => "center center",
                        __( 'Center Left', 'swift-framework-plugin' )   => "left center",
                        __( 'Center Right', 'swift-framework-plugin' )  => "right center",
                        __( 'Top Left', 'swift-framework-plugin' )      => "left top",
                        __( 'Top Right', 'swift-framework-plugin' )     => "right top",
                        __( 'Top Center', 'swift-framework-plugin' )    => "center top",
                        __( 'Bottom Left', 'swift-framework-plugin' )   => "left bottom",
                        __( 'Bottom Right', 'swift-framework-plugin' )  => "right bottom",
                        __( 'Bottom Center', 'swift-framework-plugin' ) => "center bottom",
                    ),
                    "description" => __( "Select the alignment for the background image within the asset - this is ideal for placement of images that aren't cover layers.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Background Repeat", 'swift-framework-plugin' ),
                    "param_name"  => "layer_bg_repeat",
                    "value"       => array(
                        __( 'No Repeat', 'swift-framework-plugin' )    => "no-repeat",
                        __( 'Repeat X + Y', 'swift-framework-plugin' ) => "repeat",
                        __( 'Repeat X', 'swift-framework-plugin' )     => "repeat-x",
                        __( 'Repeat Y', 'swift-framework-plugin' )     => "repeat-y",
                    ),
                    "description" => __( "Select if you would like the background image to repeat, and if so on which axis.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Layer Depth", 'swift-framework-plugin' ),
                    "param_name"  => "layer_depth",
                    "value"       => array(
                        "0.00",
                        "0.10",
                        "0.20",
                        "0.30",
                        "0.40",
                        "0.50",
                        "0.60",
                        "0.70",
                        "0.80",
                        "0.90",
                        "1.00"
                    ),
                    "description" => __( "Choose the depth for the layer, where a depth of 0 will cause the layer to remain stationary, and a depth of 1 will cause the layer to move by the total effect of the calculated motion. Values inbetween 0 and 1 will cause the layer to move by an amount relative to the supplied ratio.", 'swift-framework-plugin' )
                ),
                array(
                    "type"       => "section",
                    "param_name" => "section_misc_options",
                    "heading"    => __( "Optional Text", 'swift-framework-plugin' ),
                    "value"      => ''
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Text Layer Enable", 'swift-framework-plugin' ),
                    "param_name"  => "text_layer",
                    "value"       => array(
                        __( 'No', 'swift-framework-plugin' )  => "no",
                         __( 'Yes', 'swift-framework-plugin' ) => "yes"
                    ),
                    "buttonset_on"  => "yes",
                    "description" => __( "Select if you would like this layer to be a text layer.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textarea_html",
                    "holder"      => "div",
                    "class"       => "",
                    "heading"     => __( "Text Layer Content", 'swift-framework-plugin' ),
                    "param_name"  => "content",
                    "value"       => '',
                    "required"       => array("text_layer", "=", "yes"),
                    "description" => __( "Enter your content if you have set this to be a text layer.", 'swift-framework-plugin' )
                ),
            )
        )
    );
