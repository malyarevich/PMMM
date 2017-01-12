<?php
    
    /*
    *
    *   Swift Page Builder - Pricing Table
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_pricing_table extends SwiftPageBuilderShortcode {

        public function __construct( $settings ) {
            parent::__construct( $settings );
            SwiftPageBuilder::getInstance()->addShortCode( array( 'base' => 'spb_pricing_column' ) );
        }

        protected function content( $atts, $content = null ) {
            $widget_title = $type = $active_section = $interval = $width = $el_position = $el_class = '';
            //
            extract( shortcode_atts( array(
                'widget_title'   => '',
                'columns'        => '',
                'width'          => '1/1',
                'el_position'    => '',
                'el_class'       => ''
            ), $atts ) );
            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_pricing_table spb_content_element ' . $width . $el_class . ' not-column-inherit" data-active="' . $active_section . '" data-columns="'.$columns.'">'; //data-interval="'.$interval.'"
            $output .= "\n\t\t" . '<div class="spb_wrapper spb-asset-content spb_pricing_table_wrapper">';
            $output .= ( $widget_title != '' ) ? "\n\t\t\t" . $this->spb_title( $widget_title, 'spb_pricing_table_heading' ) : '';
            $output .= "\n\t\t\t" . '<div class="row">';
            $output .= "\n\t\t\t\t" . spb_format_content( $content );
            $output .= "\n\t\t\t" . '</div>';
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }

       
    }  

    SPBMap::map( 'spb_pricing_table', array(
        "name"            => __( "Pricing Table", 'swift-framework-plugin' ),
        "base"            => "spb_pricing_table",
        "controls"        => "full",
        "class"           => "spb_pricing_table",
        "icon"            => "icon-pricing-table",
        "params"          => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "widget_title",
                "value"       => "",
                "description" => __( "What text use as widget title. Leave blank if no title is needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Column count", 'swift-framework-plugin' ),
                "param_name"  => "columns",
                "value"       => array( "4", "3", "2", "1" ),
                "std"         => '3',
                "description" => __( "How many columns would you like the pricing table to display in.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section_tab",
                "param_name" => "column_options_tab",
                "heading"    => __( "Columns", 'swift-framework-plugin' ),
            ),
             array(
                "type"        => "textarea_html",
                "holder"      => "div",
                "class"       => "",
                "heading"     => __( "Pricing Columns", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "value"       => "",
                "description" => __( "Specify your pricing columns.", 'swift-framework-plugin' ),
                "param_type"  => "repeater",
                "master"      => "spb_pricing_table",
            ),
            array(
                "type"       => "section_tab",
                "param_name" => "misc_options_tab",
                "heading"    => __( "Misc", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "textfield",  
                "heading"     => __( "Extra class name", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            ),
        )
    ) );


    /*
    *
    *   Swift Page Builder - Pricing Table Column
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_pricing_column extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $highlight_class = $name = $highlight_column = $price = $period= $btn_text = $href = $target = $width = $position = $el_class = '';
            extract( shortcode_atts( array(
                'name'              => '',
                'highlight_column'  => '',
                'price'             => '',
                'period'            => '',
                'btn_text'          => '',
                'href'              => '',
                'target'            => '',
                'button_colour'     => 'lightgrey',
                'el_class'          => '',
                'el_position'       => '',
                'width'             => '',
            ), $atts ) );
            $output = '';

            $width    = spb_translateColumnWidthToSpan( $width );
            $el_class = $this->getExtraClass( $el_class );

            if ( $highlight_column == "yes" ) {
                 $highlight_class = "highlight";
                 $button_colour = "accent";
            }

            if ( $target == 'same' || $target == '_self' ) {
                $target = '_self';
            }
            if ( $target != '' ) {
                $target = $target;
            }

            $output .= '<div class="spb-pricing-column-wrap spb_content_element clearfix ' . $width . ' ' . $position . $el_class . '">' . "\n";
                $output .= '<div class="sf-pricing-column ' . $highlight_class .  '">';
                    if ( $highlight_column == "yes" ) {
                    $output .= '<div class="sf-pricing-tag" data-text="' . __( "Popular", "swift-framework-plugin" ) . '"></div>';
                    }
                    $output .= '<div class="sf-pricing-name">';
                        $output .= '<h6>' . $name . '</h6>';
                        $output .= '<div class="divide"></div>';
                        $output .= '<h1 class="sf-pricing-price">' . $price . '<span class="period">' . $period . '</span></h1>';
                    $output .= '</div>';
                    $output .= '<div class="sf-pricing-features">';
                    $output .=  do_shortcode( $content );
                    $output .= '</div>';
                    if ( $btn_text != "" ) {
                        $output .= '<div class="sf-pricing-button"><a class="sf-button standard '.$button_colour.'" href="' . $href . '" target="' . $target . '">'. $btn_text . '</a></div>';
                    }
                $output .= '</div>';
            $output .= '</div> ' . $this->endBlockComment( '.spb-pricing-column-wrap' ) . "\n";
            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
            return $output;
        }


        public function contentAdmin( $atts, $content ) {
            $width                = $custom_markup = '';
            $shortcode_attributes = array( 'width' => '1/3' );
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
                $iner .= $this->singleParamHtmlHolder( $param, $param_value );
            }

            $tmp = '';
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
    }

    $target_arr = array(
        __( "Same window", 'swift-framework-plugin' ) => "_self",
        __( "New window", 'swift-framework-plugin' )  => "_blank"
    );

    SPBMap::map( 'spb_pricing_column', array(
        "name"     => __( "Pricing Column", 'swift-framework-plugin' ),
        "base"     => "spb_pricing_column",
        "class"    => "button_grey",
        "icon"     => "icon-parallax",
        "controls" => "full",
        "params"   => array(
           
            array(
                "type"        => "textfield",
                "heading"     => __( "Plan Name", 'swift-framework-plugin' ),
                "param_name"  => "name",
                "holder"      => 'h4',
                "value"       => "",
                "description" => __( "Enter the text for the pricing plan name here.", 'swift-framework-plugin' )
            ),
             array(
                "type"        => "textarea_html",
                "holder"      => "div",
                "class"       => "",
                "heading"     => __( "Features Rows", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "value"       => "",
                "description" => __( "Specify your pricing columns.", 'swift-framework-plugin' ),
                "param_type"  => "repeater",
                "master"      => "spb_pricing_column",
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Highlight Column", 'swift-framework-plugin' ),
                "param_name"  => "highlight_column",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Select if you'd like that this column will be highlighted.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Header Background color", 'swift-framework-plugin' ),
                "param_name"  => "header_bg_color",
                "value"       => "",
                "description" => __( "Select a background colour for the Pricing column header here.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Plan Price", 'swift-framework-plugin' ),
                "param_name"  => "price",
                "value"       => "",
                "description" => __( "Enter the value for the pricing plan price here.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Plan Period", 'swift-framework-plugin' ),
                "param_name"  => "period",
                "value"       => "",
                "description" => __( "Enter the value for the plan period. ex: Per Month/ Per year/ Weekly/ Monthly, etc.", 'swift-framework-plugin' )
            ),    
            array(
                "type"        => "textfield",
                "heading"     => __( "Button Text", 'swift-framework-plugin' ),
                "param_name"  => "btn_text",
                "value"       => "",
                "description" => __( "The text that appears on the button.", 'swift-framework-plugin' )
            ),            
            array(
                "type"        => "textfield",
                "heading"     => __( "Link URL", 'swift-framework-plugin' ),
                "param_name"  => "href",
                "value"       => "",
                "description" => __( "The link for the Pricing Plan Call to Action.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "dropdown",
                "heading"    => __( "Link Target", 'swift-framework-plugin' ),
                "param_name" => "target",
                "value"      => $target_arr
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

 /*
    *
    *   Swift Page Builder - Pricing Table Column Feature
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

 

    class SwiftPageBuilderShortcode_spb_pricing_column_feature extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $inline_style = $feature_name = $bg_color_feature = $text_color_feature = $el_class = '';
            extract( shortcode_atts( array(
                'feature_name'  => '',
                'bg_color'      => '',
                'text_color'    => '',
                'el_class'      => '',
            ), $atts ) );

            $output = '';

            $el_class = $this->getExtraClass( $el_class );

            if ( $bg_color_feature != "" ) {
                $inline_style .= 'background-color:' . $bg_color . ';';
            }
            if ( $text_color_feature != "" ) {
                $inline_style .= 'color:' . $text_color . ';';
            }

            $output .= '<div class="spb-pricing-column-feature spb_content_element clearfix '  . $el_class .'" style="' . $inline_style . '">' . "\n";
            $output .= $feature_name;
            $output .= '</div>';
          
            return $output;
        }

    }

    SPBMap::map( 'spb_pricing_column_feature', array(
        "name"     => __( "Pricing Column Feature", 'swift-framework-plugin' ),
        "base"     => "spb_pricing_column_feature",
        "class"    => "button_grey",
        "icon"     => "icon-parallax",
        "controls" => "edit_popup_delete",
        "params"   => array(
           
            array(
                "type"        => "textfield",
                "heading"     => __( "Feature Name", 'swift-framework-plugin' ),
                "param_name"  => "feature_name",
                "holder"      => 'h6',
                "value"       => __( "Feature name", 'swift-framework-plugin' ),
                "description" => __( "Enter the text for the feature name here.", 'swift-framework-plugin' )
            ),
             array(
                "type"        => "colorpicker",
                "heading"     => __( "Background color", 'swift-framework-plugin' ),
                "param_name"  => "bg_color_feature",
                "value"       => "",
                "description" => __( "Select a background colour for the Pricing Feature row.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Text color", 'swift-framework-plugin' ),
                "param_name"  => "text_color_feature",
                "value"       => "",
                "description" => __( "Select a Text color for the Pricing Feature Row.", 'swift-framework-plugin' )
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
