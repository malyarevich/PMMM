<?php

    /*
    *
    *	Swift Page Builder Shortcode Mapper
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SPBMap {

        protected static $sc = Array();
        protected static $layouts = Array();

        public static function layout( $array ) {
            self::$layouts[] = $array;
        }

        public static function getLayouts() {
            return self::$layouts;
        }

        public static function map( $name, $attributes ) {

            if ( empty( $attributes['name'] ) ) {
                trigger_error( __( "Wrong name for shortcode:" . $name . ". Name required", 'swift-framework-plugin' ) );
            } elseif ( empty( $attributes['base'] ) ) {
                trigger_error( __( "Wrong base for shortcode:" . $name . ". Base required", 'swift-framework-plugin' ) );
            } else {

                self::$sc[ $name ]           = $attributes;
                self::$sc[ $name ]['params'] = Array();

                if ( ! empty( $attributes['params'] ) ) {
                    $attributes_keys = Array();

                    if ( $name != 'spb_row' ){
                        $asset_name =   array(
                            "type"        => "textfield",  
                            "heading"     => __( "Element Name", 'swift-framework-plugin' ),
                            "param_name"  => "element_name",
                            "value"       => "",
                            "description" => __( "Element Name. Use it to easily recognize the elements in the page builder mode.", 'swift-framework-plugin' )
                        );

                        array_unshift($attributes['params'], $asset_name);

                        $asset_name =  array(
                             "type"       => "section_tab",
                             "param_name" => "general_tab",
                             "heading"    => __( "General", 'swift-framework-plugin' ),
                             "value"      =>  "" );
                        
                        array_unshift($attributes['params'], $asset_name);   

                    }

                    $pad_horizontal_default = 0;
                    
                    if ( $name == 'spb_column' ){
                        $pad_horizontal_default = 15;
                    }

                    if ( $name == 'spb_row' || $name == 'spb_text_block' || $name == 'spb_column' ){
                        $asset_name =  array(
                                 "type"       => "section_tab",
                                 "param_name" => "design_tab",
                                 "heading"    => __( "Design Options", 'swift-framework-plugin' ),
                                 "value"      =>          "" );
                            
                        array_push($attributes['params'], $asset_name);   
                            
                        $asset_name = array(
                                "type"        => "buttonset",
                                "heading"     => __( "Simplified Controls", 'swift-framework-plugin' ),
                                "param_name"  => "simplified_controls",
                                "value"       => array(
                                                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                                                    __( 'No', 'swift-framework-plugin' )  => "no"
                                                 ),
                                "buttonset_on"  => "yes",
                                "description" => __( "Choose if you want to see a simplied fields for the Margin/Border/Padding or a more detailed fields.", 'swift-framework-plugin' )
                        );
                        
                        array_push($attributes['params'], $asset_name);   

                        $asset_name = array(
                                "type"        => "buttonset",
                                "heading"     => __( "Use Percentages", 'swift-framework-plugin' ),
                                "param_name"  => "custom_css_percentage",
                                "value"       => array(
                                                    __( 'No', 'swift-framework-plugin' ) => "no",
                                                    __( 'Yes', 'swift-framework-plugin' )  => "yes"
                                                 ),
                                "buttonset_on"  => "yes",
                                "description" => __( "Choose if you want to use percentages or pixels instead.", 'swift-framework-plugin' )
                        );
                        
                        array_push($attributes['params'], $asset_name);  

                        $asset_name = array(
                                "type"        => "uislider",
                                "heading"     => __( "Padding - Vertical", 'swift-framework-plugin' ),
                                "param_name"  => "padding_vertical",
                                "value"       => "0",
                                "step"        => "1",
                                "min"         => "0",
                                "max"         => "800",
                                "description" => __( "Adjust the vertical padding for the text block (percentage).", 'swift-framework-plugin' )
                        );
                        
                        array_push($attributes['params'], $asset_name);  
                        
                        $asset_name = array(
                                "type"        => "uislider",
                                "heading"     => __( "Padding - Horizontal", 'swift-framework-plugin' ),
                                "param_name"  => "padding_horizontal",
                                "value"       => $pad_horizontal_default,  
                                "step"        => "1",
                                "min"         => "0",
                                "max"         => "800",
                                "description" => __( "Adjust the horizontal padding for the text block (percentage).", 'swift-framework-plugin' )
                        );
                        
                        array_push($attributes['params'], $asset_name);  
                        
                        $asset_name = array(
                                "type"        => "uislider",
                                "heading"     => __( "Margin - Vertical", 'swift-framework-plugin' ),
                                "param_name"  => "margin_vertical",
                                "value"       => "0",
                                "step"        => "1",
                                "min"         => "0",
                                "max"         => "800",
                                "description" => __( "Adjust the vertical margin for the text block (percentage).", 'swift-framework-plugin' )
                        );
                        
                        array_push($attributes['params'], $asset_name);  

                        $asset_name = array(
                                 "type"        => "textfield",
                                 "heading"     => __( "Box Adjustments", 'swift-framework-plugin' ),
                                 "param_name"  => "custom_css",
                                 "value"       => "",
                                 "param_type"  => "css-field",
                                 "description"  => __( "Set the custom css values for the element. You can use the simplified controls or the detailed ones by changing it in the option above.", "swift-framework-plugin")
                                 );

                        array_push($attributes['params'], $asset_name);    
                    
                        $asset_name = array(
                                "type"        => "uislider",
                                "heading"     => __( "Border Size", 'swift-framework-plugin' ),
                                "param_name"  => "border_size",
                                "value"       => "0",
                                "step"        => "1",
                                "min"         => "0",
                                "max"         => "50",
                                "description" => __( "Adjust the border size of the element. You can use the detailed controls to specify different values for Top/Left/Right/Bottom .", 'swift-framework-plugin' )
                        );
                        
                        array_push($attributes['params'], $asset_name);  

                        $asset_name = array(
                                 "type"        => "colorpicker",
                                 "heading"     => __( "Border color", 'swift-framework-plugin' ),
                                 "param_name"  => "border_color_global",
                                 "value"       => "",
                                 "description" => __( "Set the border colour for the asset.", 'swift-framework-plugin' ));
                 
                        array_push($attributes['params'], $asset_name);
                         
                        $border_styling = array(
                                                __( "Theme Defaults", 'swift-framework-plugin' ) => "default",
                                                __( "Solid", 'swift-framework-plugin' )  => "solid",
                                                __( "Dotted", 'swift-framework-plugin' )  => "dotted",
                                                __( "Dashed", 'swift-framework-plugin' )  => "dashed",
                                                __( "None", 'swift-framework-plugin' )  => "none",
                                                __( "Hidden", 'swift-framework-plugin' )  => "hidden",
                                                __( "Double", 'swift-framework-plugin' )  => "double",
                                                __( "Groove", 'swift-framework-plugin' )  => "groove",
                                                __( "Ridge", 'swift-framework-plugin' )  => "ridge",
                                                __( "Inset", 'swift-framework-plugin' )  => "inset",
                                                __( "Outset", 'swift-framework-plugin' )  => "outset",
                                                __( "Initial", 'swift-framework-plugin' )  => "initial",
                                                __( "Inherit", 'swift-framework-plugin' )  => "inherit"
                            );  

                        $asset_name = array(
                                "type"        => "dropdown",
                                "heading"     => __( "Border Styling", 'swift-framework-plugin' ),
                                "param_name"  => "border_styling_global",
                                "value"       => $border_styling,
                                "description" => __( "Border Styling", 'swift-framework-plugin' ));

                 
                        array_push($attributes['params'], $asset_name);
                            
                        if( $name != 'spb_column' && $name != 'spb_row'){
                                
                            $asset_name =  array(
                                        "type"        => "colorpicker",
                                        "heading"     => __( "Background color", 'swift-framework-plugin' ),
                                        "param_name"  => "back_color_global",
                                        "value"       => "",
                                        "description" => __( "Set the background colour for the asset.", 'swift-framework-plugin' ));
                 
                            array_push($attributes['params'], $asset_name);

                            $asset_name =  array(
                                        "type"        => "attach_image",
                                        "heading"     => __( "Background Image", 'swift-framework-plugin' ),
                                        "param_name"  => "bk_image_global",
                                        "value"       => "");

                            array_push($attributes['params'], $asset_name);
                        }
                            
                        $asset_name = array(
                                "type"        => "textfield",
                                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                                "param_name"  => "el_class",
                                "value"       => "",
                                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
                        );
                            
                        array_push($attributes['params'], $asset_name);
                            
                    }
                

                foreach ( $attributes['params'] as $attribute ) {
                        $key = array_search( $attribute['param_name'], $attributes_keys );
                        if ( $key === false ) {
                            $attributes_keys[]             = $attribute['param_name'];
                            self::$sc[ $name ]['params'][] = $attribute;
                        } else {
                            self::$sc[ $name ]['params'][ $key ] = $attribute;
                        }
                    }
                }
                SwiftPageBuilder::getInstance()->addShortCode( self::$sc[ $name ] );
            }

        }

        public static function getShortCodes() {
            return self::$sc;
        }

        public static function getShortCode( $name ) {
            return self::$sc[ $name ];
        }

        public static function dropParam( $name, $attribute_name ) {
            foreach ( self::$sc[ $name ]['params'] as $index => $param ) {
                if ( $param['param_name'] == $attribute_name ) {
                    unset( self::$sc[ $name ]['params'][ $index ] );

                    return;
                }
            }
        }

        /* Extend params for settings */
        public static function addParam( $name, $attribute = Array() ) {
            if ( ! isset( self::$sc[ $name ] ) ) {
                return trigger_error( __( "Wrong name for shortcode:" . $name . ". Name required", 'swift-framework-plugin' ) );
            } elseif ( ! isset( $attribute['param_name'] ) ) {
                trigger_error( __( "Wrong attribute for '" . $name . "' shortcode. Attribute 'param_name' required", 'swift-framework-plugin' ) );
            } else {

                $replaced = false;

                foreach ( self::$sc[ $name ]['params'] as $index => $param ) {
                    if ( $param['param_name'] == $attribute['param_name'] ) {
                        $replaced                              = true;
                        self::$sc[ $name ]['params'][ $index ] = $attribute;
                    }
                }

                if ( $replaced === false ) {
                    self::$sc[ $name ]['params'][] = $attribute;
                }

                SwiftPageBuilder::getInstance()->addShortCode( self::$sc[ $name ] );
            }
        }

        public static function dropShortcode( $name ) {
            unset( self::$sc[ $name ] );
            SwiftPageBuilder::getInstance()->removeShortCode( $name );

        }

        public static function showAllD() {
            $a = Array();
            foreach ( self::$sc as $key => $params ) {
                foreach ( $params['params'] as $p ) {
                    if ( ! isset( $a[ $p['type'] ] ) ) {
                        $a[ $p['type'] ] = $p;
                    }
                }
            }

            //var_dump( array_keys( $a ) );

        }

    }

?>
