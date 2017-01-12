<?php

    /*
    *
    *   Swift Page Builder - Shortcodes Class
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    abstract class SwiftPageBuilderShortcode extends SFPageBuilderAbstract {

        protected $shortcode;

        protected $atts, $settings;

        public function __construct( $settings ) {
            $this->settings  = $settings;
            $this->shortcode = $this->settings['base'];
            $this->addShortCode( $this->shortcode, Array( $this, 'output' ) );
        }

        public function shortcode( $shortcode ) {

        }

        abstract protected function content( $atts, $content = null );

        public function contentAdmin( $atts, $content ) {
            $element = $this->shortcode;
            $output  = $custom_markup = $width = '';

            if ( $content != null ) {
                $content = wpautop( stripslashes( $content ) );
                $content = preg_replace( '/^\s+|\n|\r|\s+$/m', '', $content );
            }


            if ( isset( $this->settings['params'] ) ) {
                $shortcode_attributes = array( 'width' => '1/1' );
                foreach ( $this->settings['params'] as $param ) {
                    if ( $param['param_name'] != 'content' ) {
                        //var_dump($param['value']);
                        if ( isset( $param['value'] ) ) {
                            $shortcode_attributes[ $param['param_name'] ] = is_string( $param['value'] ) ? __( $param['value'], 'swift-framework-plugin' ) : $param['value'];
                        } else {
                            $shortcode_attributes[ $param['param_name'] ] = '';
                        }
                    } else if ( $param['param_name'] == 'content' && $content == null ) {
                        if ( isset($param['value']) ) {
                            $content = __( $param['value'], 'swift-framework-plugin' );
                        }
                    }
                }
                extract( shortcode_atts(
                    $shortcode_attributes
                    , $atts ) );
                
                $elem = $this->getElementHolder( $width );

                $iner = '';
                foreach ( $this->settings['params'] as $param ) {
                    $param_value = ${$param['param_name']};
                    //var_dump($param_value);
                    if ( is_array( $param_value ) ) {
                        // Get first element from the array
                        reset( $param_value );

                        if ( isset( $param['std'] ) ) {
                            $param_value = $param['std'];
                        } else {
                            $first_key   = key( $param_value );
                            $param_value = $param_value[ $first_key ];
                        }
                    }

                    $iner .= $this->singleParamHtmlHolder( $param, $param_value );
                }
                $elem = str_ireplace( '%spb_element_content%', $iner, $elem );
                $output .= $elem;
            } else {
                //This is used for shortcodes without params (like simple divider)
                // $column_controls = $this->getColumnControls($this->settings['controls']);
                $width = '1/1';

                $elem = $this->getElementHolder( $width );

                $iner = '';
                if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
                    if ( $content != '' ) {
                        $custom_markup = str_ireplace( "%content%", $content, $this->settings["custom_markup"] );
                    } else if ( $content == '' && isset( $this->settings["default_content"] ) && $this->settings["default_content"] != '' ) {
                        $custom_markup = str_ireplace( "%content%", $this->settings["default_content"], $this->settings["custom_markup"] );
                    }
                    //$output .= do_shortcode($this->settings["custom_markup"]);
                    $iner .= do_shortcode( $custom_markup );
                }
                $elem = str_ireplace( '%spb_element_content%', $iner, $elem );
                $output .= $elem;
            }

            return $output;
        }

        public function output( $atts, $content = null, $base = '' ) {
            $this->atts = $atts;
            $output     = '';

            $content = empty( $content ) && ! empty( $atts['content'] ) ? $atts['content'] : $content;

            if ( is_admin() ) {

                $output .= $this->contentAdmin( $this->atts, $content );
                
                
            }

            if ( empty( $output ) ) {
                
                $output .= $this->content( $this->atts, $content );
                
            }

            return $output;
        }

        public function getExtraClass( $el_class, $fullwidth = false ) {
            $output = '';
            if ( $el_class != '' ) {
                $output = " " . str_replace( ".", "", $el_class );
            }

            if ( $fullwidth ) {
                $output .= ' spb-full-width-element';
            }

            return $output;
        }

        /**
         * Create HTML comment for blocks
         *
         * @param $string
         *
         * @return string
         */

        public function endBlockComment( $string ) {
            return ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? '<!-- END ' . $string . ' -->' : '' );
        }

        /**
         * Asset title
         *
         * @param $title - title text
         *
         * @return string
         */

        public function spb_title( $title, $extraclass = "", $fullwidth = false ) {

            $output = '';

            if ( $fullwidth ) {
                $output .= '<div class="title-wrap container">';
            } else {
                $output .= '<div class="title-wrap">';
            }

            if ( $extraclass != "" ) {
                $output .= '<h3 class="spb-heading ' . $extraclass . '"><span>' . $title . '</span></h3>';
            } else {
                $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
            }

            $output .= '</div>';

            return $output;
        }

        /**
         * Start row comment for html shortcode block
         *
         * @param $position - block position
         *
         * @return string
         */

        public function startRow( $position, $col_width = "", $fullwidth = false, $id = "" ) {
            global $sf_sidebar_config;
            $el_class = "";

            if ( is_singular( 'portfolio' ) ) {
                $sf_sidebar_config = "no-sidebars";
            }

            if ( $id != "" && strpos($id, 'id=') !== false ) {
                $el_class = 'row-has-id';
            }

            if ( $id != "" && strpos($id, 'data-header-style=') !== false ) {
                $el_class .= ' dynamic-header-change';
            }

            $output = '';

            if ( strpos( $position, 'first' ) !== false ) {
                if ( $fullwidth ) {
                    $output = ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- START row (1) -->' . "\n" : '' ) . '<section ' . $id . ' class="row fw-row ' . $el_class . '">';
                } else {
                    $output = ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- START row (2) -->' . "\n" : '' ) . '<section ' . $id . ' class="row ' . $el_class . '">';
                }
                // if ( $fullwidth ) {
                //     $output = ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- START row (1) -->' . "\n" : '' ) . '<section ' . $id . ' class="row fw-row ' . $el_class . '">';
                // } else if ( $sf_sidebar_config == "no-sidebars" ) {
                //     $output = ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- START row (2) -->' . "\n" : '' ) . '<section ' . $id . ' class="container ' . $el_class . '"><div class="row">';
                // } else {
                //     $output = ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- START row (3) -->' . "\n" : '' ) . '<section ' . $id . ' class="row ' . $el_class . '">';
                // }
            }

            return $output;
        }

        /**
         * End row comment for html shortcode block
         *
         * @param $position -block position
         *
         * @return string
         */

        public function endRow( $position, $column = "", $fullwidth = false, $row = false ) {

            global $sf_sidebar_config;

            if ( is_singular( 'portfolio' ) ) {
                $sf_sidebar_config = "no-sidebars";
            }

            $output = '';
            if ( strpos( $position, 'last' ) !== false ) {
                if ( $row ) {
                    $output = '</section>' . ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- END row (1) --> ' . "\n" : '' . "\n" );
                } else if ( $fullwidth ) {
                    $output = '<div class="spb-fw-sizer"></div></section>' . ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- END row (1-fw) --> ' . "\n" : '' . "\n" );
                } else {
                    $output = '</section>' . ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- END row (2) --> ' . "\n" : '' . "\n" );
                }
                // if ( $fullwidth ) {
                //     $output = '</section>' . ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- END row (1) --> ' . "\n" : '' . "\n" );
                // } else if ( $sf_sidebar_config == "no-sidebars" ) {
                //     $output = '</div></section>' . ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- END row (2) --> ' . "\n" : '' . "\n" );
                // } else {
                //     $output = '</section>' . ( ! empty( $_GET['spb_debug'] ) && $_GET['spb_debug'] == 'true' ? "\n" . '<!-- END row (3) --> ' . "\n" : '' . "\n" );
                // }
            }

            return $output;
        }

        public function settings( $name ) {
            return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : null;
        }

        function getElementHolder( $width ) {  

            $output = $header_wrapper = $controls_wrapper  = '';
            $column_controls = $this->getColumnControls( $this->settings( 'controls' ), '' );


            if ( isset($this->atts['element_name']) && $this->atts['element_name'] != '' ){
                $el_name = $this->atts['element_name'];
            }else{
                $el_name = $this->settings["name"];
            }
            
            if( $this->settings["base"] == "spb_multilayer_parallax" ){
                $content_url = plugins_url() .'/swift-framework/includes/page-builder/assets/img/blank_f7.gif';
            }else{
                $content_url = "";
            }


            $output .= '<div data-element_type="' . $this->settings["base"] . '" class="' . $this->settings["base"] . ' spb_content_element spb_sortable ' . spb_translateColumnWidthToSpanEditor( $width ) . ' ' . $this->settings["class"] . '" data-content-url="' .$content_url . '">';
            $output .= '<input type="hidden" class="spb_sc_base" name="element_name-' . $this->shortcode . '" value="' . $this->settings["base"] . '" />';
            $output .= $this->getCallbacks( $this->shortcode );
            $output .= '<div class="spb_element_wrapper ' . $this->settings( "wrapper_class" ) . '">';
            
            if ( $this->settings["base"] == 'spb_accordion' || $this->settings["base"] == 'spb_tabs' || $this->settings["base"] == 'spb_tour'  || $this->settings["base"] == 'spb_gmaps' || $this->settings["base"] == 'spb_multilayer_parallax' ) {
  
                if ( $this->settings["base"] == 'spb_accordion' ){
                    $header_wrapper = 'accordion_header';
                    $controls_wrapper = 'spb_accordion_controls';
                }

                if ( $this->settings["base"] == 'spb_tabs' || $this->settings["base"] == 'spb_tour'  || $this->settings["base"] == 'spb_gmaps' || $this->settings["base"] == 'spb_multilayer_parallax' ){
                    $header_wrapper = 'tab_header';
                    $controls_wrapper = 'spb_tab_controls';
                }    

                $output .= '<div class="' . $header_wrapper . '"><div class="icon_holder"><span class="'.$this->settings["icon"].'"></span></div><div class="el_name_holder" data_default_name="' . $this->settings["name"] . '">'. $el_name  .'</div><div class="el_name_editor" >';                
                $output .= '<input name="el_name_editor"  id="el_name_editor" type="text" class="textfield validate" value="'. $el_name . ' " /><a class="el-name-save" href="#" title="Save"><span class="icon-save"></span></a></div>';   
                $output .= '<div class="' . $controls_wrapper . '"><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a><a class="element-save" href="#" title="Save"><span class="icon-save"></span></a>';
                $output .= '<a class="column_clone" href="#" title="Duplicate"><span class="icon-duplicate"></span></a><a class="column_edit" href="#" title="Edit" ><span class="icon-edit"></span></a><a href="#"><span class="icon-drag-handle"></span></a></div></div>';
                
            }else{
                $output .= '<div class="spb_elem_controls"><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a><a class="element-save" href="#" title="Save"><span class="icon-save"></span></a>';
                $output .= '<a class="column_clone" href="#" title="Duplicate"><span class="icon-duplicate"></span></a><a class="column_edit" href="#" title="Edit" ><span class="icon-edit"></span></a></div>';
                $output .= '<div class="spb_elem_handles"></div><div class="icon_holder"><span class="'.$this->settings["icon"].'"></span></div><div class="el_name_holder" data_default_name="' . $this->settings["name"] . '">'. $el_name  .'</div><div class="el_name_editor" ><input name="el_name_editor"  id="el_name_editor" type="text" class="validate textfield" value="'. $el_name . ' " /><a class="el-name-save" href="#" title="Save"><span class="icon-save"></span></a></div>';  

                
            }
            
            if ( $this->settings["base"] == 'spb_blank_spacer' ){
                if ( isset( $this->atts['responsive_vis'] ) ) {
                    $responsive_vis =  $this->atts['responsive_vis'];    
                } else {
                    $responsive_vis = '';
                }
                
                $output .=  $this->getResponsiveIndicatorHtml( $responsive_vis );
            }

            $output .= '%spb_element_content%';
            $output .= '</div> <!-- end .spb_element_wrapper -->';
            $output .= '</div> <!-- end #element-' . $this->shortcode . ' -->';

            return $output;
        }

        /* This returs block controls
   ---------------------------------------------------------- */
        public function getColumnControls( $controls, $responsive_vis ) {

            $controls_start = '<div class="controls sidebar-name">';
            $controls_end   = '</div>';
            $responsive_vis = $this->getResponsiveIndicatorHtml( $responsive_vis );
            $right_part_start = '<div class="controls_right">';
            $right_part_end   = '</div>';

            $controls_column_size = ' <div class="column_size_wrapper"> <a class="column_decrease" href="#" title="' . __( 'Decrease width', 'swift-framework-plugin' ) . '"></a> <span class="column_size">%column_size%</span> <a class="column_increase" href="#" title="' . __( 'Increase width', 'swift-framework-plugin' ) . '"></a> </div>';

            $controls_save        = ' <a class="element-save" href="#" title="' . __( 'Save', 'swift-framework-plugin' ) . '"><span class="icon-save"></span></a><a class="column_clone" href="#" title="' . __( 'Clone', 'swift-framework-plugin' ) . '"><span class="icon-duplicate"></span></a>';
            $controls_edit        = ' <a class="column_edit" href="#" title="' . __( 'Edit', 'swift-framework-plugin' ) . '"><span class="icon-edit"></span></a><a href="#"><span class="icon-drag-handle"></span></a>';
            $controls_popup       = ' <a class="column_popup" href="#" title="' . __( 'Pop up', 'swift-framework-plugin' ) . '"></a>';
            $controls_delete      = '<a class="column_delete" href="#" title="' . __( 'Delete', 'swift-framework-plugin' ) . '"><span class="icon-delete"></span></a>';
            $controls_only_delete = '<a class="column_delete" href="#" title="' . __( 'Delete', 'swift-framework-plugin' ) . '"><span class="icon-delete"></span></a>';
            // $delete_edit_row = '<a class="row_delete" title="'.__('Delete %element%', 'swift-framework-plugin').'">'.__('Delete %element%', 'swift-framework-plugin').'</a>';

            $column_controls_full              = $controls_start . $controls_column_size . $right_part_start . $controls_delete . $controls_save . $controls_edit . $right_part_end . $controls_end;
            $column_controls_full_col          = $controls_start . '<span class="asset-name">' . __( "Column", 'swift-framework-plugin' ) . '</span>' . $responsive_vis .  $controls_column_size . $right_part_start . $controls_delete . $controls_save . $controls_edit . $right_part_end . $controls_end;
            $column_controls_size_delete       = $controls_start . $controls_column_size . $right_part_start . $controls_delete . $controls_save . $right_part_end . $controls_end;
            $column_controls_popup_delete      = $controls_start . $right_part_start . $controls_delete . $controls_save . $right_part_end . $controls_end;
            $column_controls_delete            = $controls_start . $right_part_start . $controls_delete  . $controls_save . $right_part_end . $controls_end;
            $column_controls_edit_popup_delete = $controls_start . $right_part_start . $controls_delete . $controls_save . $controls_edit . $right_part_end . $controls_end;

            $column_controls_edit_delete = $controls_start . $right_part_start . $controls_delete . $controls_save . $controls_edit . $right_part_end . $controls_end;
            $column_controls_delete_edit = $controls_start . $right_part_start . $controls_only_delete . $controls_edit . $right_part_end . $controls_end;

            if ( $controls == 'popup_delete' ) {
                return $column_controls_popup_delete;
            } else if ( $controls == 'edit_popup_delete' ) {
                return $column_controls_edit_popup_delete;
            } else if ( $controls == 'edit_delete' ) {
                return $column_controls_edit_delete;
            } else if ( $controls == 'size_delete' ) {
                return $column_controls_size_delete;
            } else if ( $controls == 'popup_delete' ) {
                return $column_controls_popup_delete;
            } else if ( $controls == 'column' ) {
                return $column_controls_full_col;
            } else if ( $controls == 'delete_edit' ) {
                return $column_controls_delete_edit;
            } else {
                return $column_controls_full;
            }
        }

        /* This will fire callbacks if they are defined in map.php
       ---------------------------------------------------------- */
        public function getCallbacks( $id ) {
            $output = '';

            if ( isset( $this->settings['js_callback'] ) ) {
                foreach ( $this->settings['js_callback'] as $text_val => $val ) {
                    /* TODO: name explain */
                    $output .= '<input type="hidden" class="spb_callback spb_' . $text_val . '_callback " name="' . $text_val . '" value="' . $val . '" />';
                }
            }

            return $output;
        }
        public function getResponsiveIndicatorHtml( $value ) {

                $output = '';
                $responsive_vis = '';

                if ( $value == "hidden-lg_hidden-md" ) {
                    $responsive_vis = '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Desktop"><i class="icon-vis-desktop hidden_vis"></i><i class="icon-vis-tablet"></i><i class="icon-vis-phone"></i></a>';
                } else if ( $value == "hidden-sm" ) {
                    $responsive_vis = '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Tablet"><i class="icon-vis-desktop"></i><i class="icon-vis-tablet hidden_vis"></i><i class="icon-vis-phone"></i></a>';
                } else if ( $value == "hidden-lg_hidden-md_hidden-sm" ) {
                    $responsive_vis = '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Desktop + Tablet"><i class="icon-vis-desktop hidden_vis"></i><i class="icon-vis-tablet hidden_vis"></i><i class="icon-vis-phone"></i></a>';
                } else if ($value == "hidden-lg_hidden-md_hidden-xs" ) {
                    $responsive_vis = '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Desktop + Phone"><i class="icon-vis-desktop hidden_vis"></i><i class="icon-vis-tablet"></i><i class="icon-vis-phone hidden_vis"></i></a>';
                } else if ($value == "hidden-xs_hidden-sm" ) {
                    $responsive_vis = '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Tablet + Phone"><i class="icon-vis-desktop"></i><i class="icon-vis-tablet hidden_vis"></i><i class="icon-vis-phone hidden_vis"></i></a>';
                } else if ( $value == "hidden-xs" ) {
                    $responsive_vis = '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Phone"><i class="icon-vis-desktop"></i><i class="icon-vis-tablet"></i><i class="icon-vis-phone hidden_vis"></i></a>';
                }
  
                //if ( $responsive_vis != "" ) {
                    $output = '<div class="responsive-vis-indicator"><span class="icons">' . $responsive_vis . '</span></div>';
                //}

                return $output;

        }

        public function singleParamHtmlHolder( $param, $value ) {
            $output = '';
            // Compatibility fixes
            $old_names = array(
                'yellow_message',
                'blue_message',
                'green_message',
                'button_green',
                'button_grey',
                'button_yellow',
                'button_blue',
                   'button_red',
                'button_orange'
            );
            $new_names = array(
                'alert-block',
                'alert-info',
                'alert-success',
                'btn-success',
                'btn',
                'btn-info',
                'btn-primary',
                'btn-danger',
                'btn-warning'
            );
            $value     = str_ireplace( $old_names, $new_names, $value );
            //$value = __($value, 'swift-framework-plugin');
            //
            $param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
            $type       = isset( $param['type'] ) ? $param['type'] : '';
            $class      = isset( $param['class'] ) ? $param['class'] : '';

            if ( isset( $param['holder'] ) == false || $param['holder'] == 'hidden' ) {
                $output .= '<input type="hidden" class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
            } else if ( $param['holder'] == 'indicator' ) {
                $output .= '<input class="spb_param_value holder ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';

            } else if ( $param['holder'] == 'page_name' ) {
                $output .= '<input type="hidden" class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
                $page_name = get_the_title( $value );
                $output .= '<div class="holder page-name-holder">' . $page_name . '</div>';
            } else {
                $output .= '<' . $param['holder'] . ' class="spb_param_value holder ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
            }

            return $output;
        }
    }

    abstract class SwiftPageBuilderShortcode_UniversalAdmin extends SwiftPageBuilderShortcode {
        public function __construct( $settings ) {
            $this->settings = $settings;
            $this->addShortCode( $this->settings['base'], Array( $this, 'output' ) );
        }

        protected function content( $atts, $content = null ) {
            return '';
        }

        public function contentAdmin( $atts, $content ) {

            $element = $this->settings['base'];
            $output  = '';

            //if ( $content != NULL ) { $content = apply_filters('the_content', $content); }
            $content = '';
            if ( isset( $this->settings['params'] ) ) {
                $shortcode_attributes = array();
                foreach ( $this->settings['params'] as $param ) {
                    if ( $param['param_name'] != 'content' ) {
                        $shortcode_attributes[ $param['param_name'] ] = $param['value'];
                    } else if ( $param['param_name'] == 'content' && $content == null ) {
                        $content = $param['value'];
                    }
                }
                extract( shortcode_atts(
                    $shortcode_attributes
                    , $atts ) );
 
                $output .= '<div class="spb_edit_form_elements">';
                $output .= '<div class="spb-edit-modal-inner testing">';
                $output .= '<div id="edit-modal-header">';
                $output .= '<h2>' . __( 'Edit', 'swift-framework-plugin' ) . ' ' . __( $this->settings['name'], 'swift-framework-plugin' ) . '</h2>';
                $output .= '<div class="edit_form_actions"><a href="#" class="spb_save_edit_form button-primary">' . __( 'Save', 'swift-framework-plugin' ) . '</a></div>';
                $output .= '</div>';

                foreach ( $this->settings['params'] as $param ) {
                    $param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : null;

                    if ( is_array( $param_value ) ) {
                           
                        // Get first element from the array
                        reset( $param_value );
                        $first_key   = key( $param_value );
                        $param_value = $param_value[ $first_key ];

                    }


                
                    $output .= $this->singleParamEditHolder( $param, $param_value );
                }

                $output .= '</div>'; //close spb-edit-modal-inner
                $output .= '</div>'; //close spb_edit_form_elements
            }

            return $output;
        }

        protected function singleParamEditHolder( $param, $param_value ) {
            $output = '';
            $field_visibility = '';

            
            $row_el_class = '';

            if ( $param['type'] == "buttonset" ) {
                $row_el_class = 'lb_buttonset';
            }

            if ( $param['depreciated'] ) {
                $row_el_class .= ' spb-depreciated';
            }

            if ( isset($param['param_type']) && $param['param_type'] == "repeater" ) {
                $row_el_class = 'lb_repeater';
            }


            if ( isset($param['param_type']) && $param['param_type'] == "css-field" ) {
                $row_el_class = 'lb_css_field';
              
            } 

            if ( isset( $param['required'][0] ) ){
                $req_parent_id       = $param['required'][0];
                $req_parent_operator = $param['required'][1];
                $req_parent_value    = $param['required'][2];
                $field_visibility = 'hide';
                $output .= '<div class="row-fluid hide dependency-field ' . $row_el_class . '" data-parent-id="' . $req_parent_id . '" data-parent-operator="' . $req_parent_operator . '" data-parent-value="' . $req_parent_value . '">';
            } else {
                $output .= '<div class="row-fluid ' . $row_el_class . '">';                 
            }


            if ( isset($param['param_type']) && $param['param_type'] == "css-field" ) {

                 $output .= '<div class="label_wrapper lb_css_box"><label class="spb_element_label">' . __( $param['heading'], 'swift-framework-plugin' ) . '</label>';
                $output .= ( isset( $param['description'] ) ) ? '<a class="tooltipped" data-position="right" data-delay="50" data-tooltip="' . __( $param['description'], 'swift-framework-plugin' ) . '"><i class="material-icons prefix">info</i></a>' : '';
                $output .= '</div>';
            }


            if ( $param['type'] == "section" ) {
                $output .= '<div class="span12 spb_element_section">' . __( $param['heading'], 'swift-framework-plugin' ) . '</div>';
            } else if ( $param['type'] == "textfield" ) { 
                $output .= '<div class="edit_form_line span12 lb_'.$param['type'].'">';
                $output .= $this->singleParamEditForm( $param, $param_value );
                $output .= ( isset( $param['description'] )  && !isset($param['param_type'])) ? '<a class="tooltipped" data-position="left" data-delay="50" data-tooltip="' . __( $param['description'], 'swift-framework-plugin' ) . '"><i class="material-icons prefix">info</i></a>' : '';
                $output .= '</div>';
                $output .=  isset( $param['link'] ) ?  __( $param['link'], 'swift-framework-plugin' )  : '';

            } else {  
                //Repeater fields
                if ( isset($param['param_type']) &&  $param['param_type'] == 'repeater'  ) {
                    $output .= '<div class="label_wrapper lb_icon_section ' . $param['master'] . '"><label class="spb_element_label">' . __( $param['heading'], 'swift-framework-plugin' ) . '</label>';
                    $output .= ( isset( $param['description'] ) ) ? '<a class="tooltipped" data-position="right" data-delay="50" data-tooltip="' . __( $param['description'], 'swift-framework-plugin' ) . '"><i class="material-icons prefix">info</i></a>' : '';
                    $output .= '</div>';
                    $output .= $this->singleParamEditForm( $param, $param_value );
                } else {
                    $output .= '<div class="label_wrapper lb_'.$param['type'].'"><label class="spb_element_label">' . __( $param['heading'], 'swift-framework-plugin' ) . '</label>';
                    $output .= ( isset( $param['description'] ) ) ? '<a class="tooltipped" data-position="right" data-delay="50" data-tooltip="' . __( $param['description'], 'swift-framework-plugin' ) . '"><i class="material-icons prefix">info</i></a>' : '';
                    $output .= '</div>';
                    $output .= '<div class="edit_form_line">';
                    $output .= $this->singleParamEditForm( $param, $param_value );
                    $output .= '</div>';
                }

            }
            
            $output .= '</div>';

            return $output;
        }

        protected function extractRepeaterFields( $field_name, $value ){                        
                $text_length = strlen($field_name) + 2;
                $text = $field_name . '="';

                $text_ini_pos = strpos( $value , $text );
                $text_end_pos = strpos( substr($value, $text_ini_pos + $text_length ), '"' );
                $text_value = substr( $value, $text_ini_pos + $text_length, $text_end_pos );

                return $text_value;
        }  

        protected function extractCssFields( $field_name, $value ){                        
              
                $text_length = strlen($field_name) + 1;
                $text = $field_name . ':';
                $text_ini_pos = strpos( $value , $text );
                
                if ( $text_ini_pos  === false ) {
                    $text_value = '';
                } else{

                    if( strpos( substr($value, $text_ini_pos + $text_length ), '%' ) === false || $field_name == 'border-bottom' || $field_name == 'border-right' || $field_name == 'border-left' || $field_name == 'border-top' ){
                        $text_end_pos = strpos( substr($value, $text_ini_pos + $text_length ), 'px' );    
                    }else{
                        $text_end_pos = strpos( substr($value, $text_ini_pos + $text_length ), '%' );    
                    }
                    
                    $text_value = substr( $value, $text_ini_pos + $text_length, $text_end_pos );               
                }

                return trim( $text_value );
        }  

          protected function singleParamEditForm( $param, $param_value ) {
            $param_line = '';
            $max_ocurrences = 0;

            // Textfield - input
           if ( isset($param['param_type']) &&  $param['param_type'] == 'repeater' ) {

                //Icon Box Grid Repeater fields
                if ( $param['master'] == 'spb_icon_box_grid' ) {
                
                    $value = $param_value;                
                    $max_ocurrences =  substr_count($value, '[spb_icon_box_grid_element');
                    $param_line .= '<ul class="icon_section_holder spb_param_value">';  
                
                    for ( $i = 1; $i <= $max_ocurrences; $i++) {
                   
                        $shortcode_ini_pos = strpos( $value, '[spb_icon_box_grid_element') ;
                        $shortcode_end_pos = strpos($value, '[/spb_icon_box_grid_element]');
                        
                        //Title
                        $title_ini_pos = strpos($value, 'title="');
                        $title_end_pos = strpos(substr($value, $title_ini_pos+7), '"');
                        $title = substr($value, $title_ini_pos+7, $title_end_pos);
                        
                        //Link
                        $link_ini_pos = strpos($value, 'link="');
                        $link_end_pos = strpos(substr($value, $link_ini_pos+6), '"');
                        $link = substr($value, $link_ini_pos+6, $link_end_pos);

                        //Icon
                        $icon_ini_pos = strpos($value, 'icon="');
                        $icon_end_pos = strpos(substr($value, $icon_ini_pos+6), '"');
                        $icon = substr($value, $icon_ini_pos+6, $icon_end_pos);

                        //Target
                        $target_ini_pos = strpos($value, 'target="');
                        $target_end_pos = strpos(substr($value, $target_ini_pos+8), '"');
                        $target = substr($value, $target_ini_pos+8, $target_end_pos);

                        //Remove the processed shortcode from the string
                        $value =  substr($value,$shortcode_end_pos+28);

                        $selected_blank = $selected_self = "";
                        if ( $target == '_blank' ){
                            $selected_blank = 'selected';
                        } else {
                            $selected_self = 'selected';
                        }

                        $param_line .= '<li><div class="section_repeater"><div class="left_section"><div class="section_icon_image"><i class="' . $icon . '"></i></div><a href="#" class="section_add_icon"><span class="icon-add"></span></a></div><div class="right_section"><div class="right_top_section">';
                        $param_line .= '<input name="icon_title_' . $i . '" id="icon_title_' . $i . '" class="textfield validate active icon_title " placeholder="'  . __('Icon Box Title', 'swift-framework-plugin' ) . ' " type="text" value="' . $title . '" /><span class="icon-drag-handle"></span><span class="icon-delete"></span></div>';
                        $param_line .= '<div class="right_bottom_section"><input name="icon_link_' . $i . '" id="icon_link_' . $i . '" class="textfield validate active icon_link " placeholder="'  . __('Icon Box Link', 'swift-framework-plugin' ) . ' " type="text" value="' . $link . '" />';
                        $param_line .= '<select id="select_section_'. $i . '" class="icon_target"><option value="_self" '. $selected_self .'>Same Window</option><option value="_blank" '. $selected_blank .'>New Window</option></select></div>';
                        $param_line .= '</div><ul class="font-icon-grid repeater-grid svg-grid">'.spb_get_svg_icons().'</ul></div></li>';
                    }
            
                    if ( $max_ocurrences <= 0 ) {
                        $i = 1;
                        $param_line .= '<li><div class="section_repeater"><div class="left_section"><div class="section_icon_image" style="display:none;"></div><a href="#" class="section_add_icon"><span class="icon-add"></span></a></div><div class="right_section"><div class="right_top_section">';
                        $param_line .= '<input name="icon_title_' . $i . '" id="icon_title_' . $i . '" class="textfield validate active icon_title " placeholder="'  . __('Icon Box Title', 'swift-framework-plugin' ) . ' " type="text" value="" /><span class="icon-drag-handle"></span><span class="icon-delete"></span></div>';
                        $param_line .= '<div class="right_bottom_section"><input name="icon_link_' . $i . '" id="icon_link_' . $i . '" class="textfield validate active icon_link " placeholder="'  . __('Icon Box Link', 'swift-framework-plugin' ) . ' " type="text" value="" />';
                        $param_line .= '<select id="select_section_'. $i . '" class="icon_target"><option value="_self" selected>Same Window</option><option value="_blank">New Window</option></select></div>';
                        $param_line .= '</div><ul class="font-icon-grid repeater-grid svg-grid">'.spb_get_svg_icons().'</ul></div></li>';
                    }
                                                
                    $param_line .= '<div class="bottom_action"><a href="#" class="spb_add_new_icon_section">' . __('Add section', 'swift-framework-plugin' ) . '</a></div></ul>';

                }

                //Pricing table Repeater fields
                if ( $param['master'] == 'spb_pricing_table' ) {
                    
                    $param_feature_line =  $max_ocurrences_features = '';
                    $value = $param_value;
                    $max_ocurrences =  substr_count($value, '[/spb_pricing_column]');
                    $param_line .= '<ul class="collapsible popout pricing_column_holder spb_param_value '.$max_ocurrences.'" data-collapsible="accordion">';  

                    for( $i = 1; $i <= $max_ocurrences; $i++ ){
   
                        $shortcode_end_pos = strpos( $value, '[/spb_pricing_column]');
                        $content_ini_pos = strpos( $value, ']');
                        $column_content = substr( $value, $content_ini_pos +1 );
                        
                        // Get Column Name
                        $stored_value = $value;
                        $name  = $this->extractRepeaterFields( 'name' , $value );

                        //Remove the processed shortcode from the string
                        $value =  substr( $value, $shortcode_end_pos + 21 );
                        
                        $featured_end_pos = strpos( $column_content, '[/spb_pricing_column]');

                        $max_ocurrences_features =  substr_count( substr( $column_content, 0,  $featured_end_pos ), '[spb_pricing_column_feature');


                         
                        if ( $max_ocurrences_features > 0 ) {
                            $param_feature_line = '';
                            $param_feature_line .= '<ul class="collapsible popout pricing_column_feature_holder ' . $max_ocurrences_features . '" data-collapsible="accordion">';  
                        }

                        for( $z = 1; $z <= $max_ocurrences_features; $z++ ) {

                            $feature_end_pos = strpos( $column_content, '[/spb_pricing_column_feature]');
                            $param_feature_line .= $this->getPricingTableFeatureField($z, $column_content);
                            
                            //Remove the processed shortcode from the string 
                            $column_content =  substr( $column_content, $feature_end_pos + strlen( '[/spb_pricing_column_feature]' ) );
                                                        
                        }

                        if ( $max_ocurrences_features > 0 ) {
                            $param_feature_line .= '</ul><div class="bottom_action"><a href="#" class="spb_add_new_column_feature">' . __('Add Feature', 'swift-framework-plugin' ) . '</a></div>';
                        }

                        $param_line .= '<li><div class="collapsible-header">' . $name . '<div class="pricing_col_actions"><span class="icon-drag-handle"></span><span class="icon-delete"></span></div></div><div class="collapsible-body">';
                        $param_line .= '<div class="section_repeater">';
                        $param_line .= $this->getPricingTableColumn($i, $param_feature_line, $stored_value);
                        $param_line .= '</div></div></li>';
                    } 

                    if ( $max_ocurrences_features <= 0 ) {
                        $j = 1;
                        $param_feature_line = '<ul class="collapsible popout pricing_column_feature_holder" data-collapsible="accordion">';  
                        $param_feature_line .= $this->getPricingTableFeatureField($j);
                        $param_feature_line .= '</ul>';
                        $param_feature_line .= '<div class="bottom_action"><a href="#" class="spb_add_new_column_feature">' . __('Add Feature', 'swift-framework-plugin' ) . '</a></div>';
                    }
                               
                    if ( $max_ocurrences <= 0 ) {
                        $i = 1;
                        $param_line .= '<li><div class="collapsible-header">' .  __('Plan Name', 'swift-framework-plugin' ) . '<div class="pricing_col_actions"><span class="icon-drag-handle"></span><span class="icon-delete"></span></div></div><div class="collapsible-body">';
                        $param_line .= '<div class="section_repeater">';
                        $param_line .= $this->getPricingTableColumn($i, $param_feature_line);
                        $param_line .= '</div></li>';
                    }
                                                  
                    $param_line .= '<div class="bottom_action"><a href="#" class="spb_add_new_pricing_column">' . __('Add column', 'swift-framework-plugin' ) . '</a></div></ul>';

                }


                //Pricing table Features Rows Repeater fields
                if ( $param['master'] == 'spb_pricing_column' ) {

                    $value = $param_value;
                    $max_ocurrences =  substr_count($value, '[spb_pricing_column_feature');
                    $param_line .= '<ul class="collapsible popout pricing_column_holder spb_param_value" data-collapsible="accordion">';  

                    for( $i = 1; $i <= $max_ocurrences; $i++){
   
                        $shortcode_ini_pos = strpos( $value, '[spb_pricing_column_feature') ;
                        $shortcode_end_pos = strpos( $value, '[/spb_pricing_column_feature]');

                        //Remove the processed shortcode from the string
                        $stored_value = $value;
                        $value =  substr( $value, $shortcode_end_pos + strlen( '[/spb_pricing_column_feature]' ) );

                        $param_line .= '<li><div class="collapsible-header">' . $name . '<div class="pricing_col_actions"><span class="icon-drag-handle"></span><span class="icon-delete"></span></div></div><div class="collapsible-body">';
                        $param_line .= '<div class="section_repeater">';
                        $param_line .= $this->getPricingTableColumn($i, $featureField, $stored_value);
                        $param_line .= '</div></div></li>';

                    }
            
                    if ( $max_ocurrences <= 0 ) {
                        $i = 1;
                        $param_line .= '<li><div class="section_repeater"><div class="pricing_col_actions"><span class="icon-drag-handle"></span><span class="icon-delete"></span></div>';
                        $param_line .= $this->getPricingTableColumn($i, $featureField);
                        $param_line .= '</div></li>';
                    }
                                                  
                    $param_line .= '<div class="bottom_action"><a href="#" class="spb_add_new_pricing_column">' . __('Add column', 'swift-framework-plugin' ) . '</a></div></ul>';

                }

           } else if( isset($param['param_type']) &&  $param['param_type'] == 'css-field' ){
                
                $margin = "0";
                $border = "0";  
                $padding = "0";

                $mt = $this->extractCssFields( 'margin-top' , $param_value );               
                $mb = $this->extractCssFields( 'margin-bottom' , $param_value );
                $bt = $this->extractCssFields( 'border-top' , $param_value );
                $bl = $this->extractCssFields( 'border-left' , $param_value );
                $br = $this->extractCssFields( 'border-right' , $param_value );
                $bb = $this->extractCssFields( 'border-bottom' , $param_value );
                $pt = $this->extractCssFields( 'padding-top' , $param_value );
                $pl = $this->extractCssFields( 'padding-left' , $param_value );
                $pr = $this->extractCssFields( 'padding-right' , $param_value );
                $pb = $this->extractCssFields( 'padding-bottom' , $param_value );

                               
                if( $mt == $mb ){
                    $margin = $mt;  
                }

                if( $bt == $bl && $bl == $br && $br == $bb ){
                    $border = $bt;
                }

                if( $pt == $pl && $pl == $pr && $pr == $pb ){
                    $padding = $pt;
                }

                $param_line .= '<div class="css-detailed-controls">';
                $param_line .= '<input name="' . $param['param_name'] . '" class="spb_param_value spb-textinput ' . $param['param_name'] . ' ' . $param['type'] . '" type="hidden" value="' . $param_value . '" />';
                //Padding
                $param_line .= '<div class="css-top-label">' .  __('Padding Top', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<div class="css-top-label">' .  __('Padding Left', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<div class="css-top-label">' .  __('Padding Right', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<div class="css-top-label">' .  __('Padding Bottom', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<input name="custom-padding-top" class="spb-textinput cp-global custom-padding-top ' . $param['type'] . '" type="text" value="' . $pt . '" />';
                $param_line .= '<input name="custom-padding-left" class="spb-textinput cp-global custom-padding-left ' . $param['type'] . '" type="text" value="' . $pl . '" />';
                $param_line .= '<input name="custom-padding-right" class="spb-textinput cp-global custom-padding-right ' . $param['type'] . '" type="text" value="' . $pr . '" />';
                $param_line .= '<input name="custom-padding-bottom" class="spb-textinput cp-global custom-padding-bottom ' . $param['type'] . '" type="text" value="' . $pb . '" />';
                
                // Border 
                $param_line .= '<div class="css-top-label">' .  __('Border Top', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<div class="css-top-label">' .  __('Border Left', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<div class="css-top-label">' .  __('Border Right', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<div class="css-top-label">' .  __('Border Bottom', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<input name="custom-border-top" class="spb-textinput cb-global custom-border-top ' . $param['type'] . '" type="text" value="' . $bt . '" />';
                $param_line .= '<input name="custom-border-left" class="spb-textinput cb-global custom-border-left ' . $param['type'] . '" type="text" value="' . $bl . '" />';
                $param_line .= '<input name="custom-border-right" class="spb-textinput cb-global custom-border-right ' . $param['type'] . '" type="text" value="' . $br . '" />';
                $param_line .= '<input name="custom-border-bottom" class="spb-textinput cb-global custom-border-bottom ' . $param['type'] . '" type="text" value="' . $bb . '" />';
                
                //Margin
                $param_line .= '<div class="css-top-label">' .  __('Margin Top', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<div class="css-top-label">' .  __('Margin Bottom', 'swift-framework-plugin' ) . '</div>';
                $param_line .= '<input name="custom-margin-top" class="spb-textinput cm-global custom-margin-top ' . $param['type'] . '" type="text" value="' . $mt . '" />';
                $param_line .= '<input name="custom-margin-bottom" class="spb-textinput cm-global custom-margin-bottom ' . $param['type'] . '" type="text" value="' . $mb . '" />';

                $param_line .= '</div>';                
  
           }  

           else if ( $param['type'] == 'textfield' ) {
                $value = __( $param_value, 'swift-framework-plugin' );
                $value = $param_value;
                
                $active_input = '';

                if ( $value != ''){
                    $active_input = 'class="active"';
                }

                if ( $param['param_name'] == 'address' && $param_value == __( 'Click the edit button to change the map pin details.', 'swift-framework-plugin' ) ) {
                    $param_line .= '<input name="' . $param['param_name'] . '" class="textfield spb_param_value spb-textinput ' . $param['param_name'] . ' ' . $param['type'] . '" type="text" value="" data-previous-text="' . __( 'Click the edit button to change the map pin details.', 'swift-framework-plugin' ) . '"/>';
                } else { 
                    $param_line .= '<div class="input-field col s12"><input name="' . $param['param_name'] . '" id="' . $param['param_name'] . '" class="textfield validate active spb_param_value " type="text" value="' . $value . '" />';
                    $param_line .= '<label for="' . $param['param_name'] . '" ' . $active_input . '>' . $param['heading'] . '</label></div>';  
                     
                }
            // Textfield - input
            } else if ( $param['type'] == 'textfield_html' ) {
                $value = __( $param_value, 'swift-framework-plugin' );
                $param_value = htmlentities( $param_value );
                $param_line .= '<input name="' . $param['param_name'] . '" class="spb_param_value spb-textinput ' . $param['param_name'] . ' ' . $param['type'] . '" type="text" value="' . $value . '" />';
            } // Textfield - color
            else if ( $param['type'] == 'colorpicker' ) {
                $value = __( $param_value, 'swift-framework-plugin' );
                $value = $param_value;
                $param_line .= '<input name="' . $param['param_name'] . '" class="spb_param_value spb-colorpicker ' . $param['param_name'] . ' ' . $param['type'] . '" type="text" value="' . $value . '" maxlength="6" size"6" />';
            } // Slider - uislider
            else if ( $param['type'] == 'uislider2' ) {
                $value = $param_value;
                $min   = isset( $param['min'] ) ? $param['min'] : 0;
                $max   = isset( $param['max'] ) ? $param['max'] : 800;
                $step  = isset( $param['step'] ) ? $param['step'] : 5;
                $param_line .= '<div class="noUiSlider"></div><input name="' . $param['param_name'] . '" class="spb_param_value spb-uislider ' . $param['param_name'] . ' ' . $param['type'] . '" type="text" value="' . $value . '" maxlength="6" size"6" data-min="' . $min . '" data-max="' . $max . '" data-step="' . $step . '" />';
            }
             
            else if ( $param['type'] == 'uislider' ) {
                $value = $param_value;
                $min   = isset( $param['min'] ) ? $param['min'] : 0;  
                $max   = isset( $param['max'] ) ? $param['max'] : 800;
                $step  = isset( $param['step'] ) ? $param['step'] : 5;

                if ( $value != ''){
                    $active_input = 'class="active"';
                }
               
                 $param_line .= '<form action="#"><p class="range-field">
                 <span class="ui_slider_track_filled" style="width:' . $value/$max*100 .'%"></span>
                <input type="range" class="spb_param_value uislider" id="' . $param['param_name'] . '" name="' . $param['param_name'] . '" min="' . $min . '" max="' . $max . '" value="' . $value . '" step="' . $step . '"/>
                <div class="input-field lb_slider_input"><input name="' . $param['param_name'] . '_val" id="' . $param['param_name'] . '_val" min="' . $min . '" max="' . $max . '" class="validate active spb_param_value uisliderinput " type="text" value="' . $value . '" /></div>';
             
            }
            else if ( $param['type'] == 'buttonset' ) {
                                
                $param_value_off = $param_value_on = $checked = $buttonset_on = '';

                foreach ( $param['value'] as $text_val => $val ) {
                
                    $text_val = __(  $text_val , 'swift-framework-plugin' );

                    if ( isset( $param['buttonset_on'] ) ) {
                        $buttonset_on = $param['buttonset_on'];
                    } else {
                        $buttonset_on = 'yes';
                    }  
                    
                    if ( $buttonset_on == $val ) {  
                         $param_value_on = $val;
                    } else {
                         $param_value_off = $val;
                    }
           
                    $id =  $param['param_name'];

                }

                if ( $param_value == $param_value_on ) {
                            $checked = 'checked="checked"';
                }
                 
                $param_line .= '<div class="switch"><label><input type="checkbox" class="spb_param_value '.$param_value.' ' . $param['param_name'] . ' ' . $param['type'] .'" name="' . $param['param_name'] . '" id="' . $param['param_name'] . '" '. $checked .' data-value-on="' . $param_value_on .'"  data-value-off="' . $param_value_off .'"  ><span class="lever"></span></label></div>';

            }
            else if ( $param['type'] == 'dropdown' ) {

                $default = $param_value;

                if ( isset( $param['std'] ) ) {
                    $default = $param['std'];
                }

                $param_line .= '<div class="input-field col s12 m6 spb_param_value dropdown" ><select name="' . $param['param_name'] . '" class=" spb-input spb-select ' . $param['param_name'] . ' ' . $param['type'] . '" data-default="'.$default.'">';

                foreach ( $param['value'] as $text_val => $val ) {
                    if ( is_numeric( $text_val ) && is_string( $val ) || is_numeric( $text_val ) && is_numeric( $val ) ) {
                        $text_val = $val;
                    }
                    $text_val = __( $text_val, 'swift-framework-plugin' );
                    //$val      = strtolower( str_replace( array( " " ), array( "_" ), $val ) );
                    $val      = str_replace( array( " " ), array( "_" ), $val );
                    $selected = '';
                    if ( $val == $param_value ) {
                        $selected = ' selected="selected"';
                    }
                    $param_line .= '<option class="' . $val . '" value="' . $val . '"' . $selected . '>' . $text_val . '</option>';
                }
                $param_line .= '</select></div>';
            } // Dropdown (id) - select
            else if ( $param['type'] == 'dropdown-id' ) {
                $default = $param_value;

                if ( isset( $param['std'] ) ) {
                    $default = $param['std'];
                }

                $param_line .= '<div class="input-field col s12 m6 spb_param_value dropdown" ><select name="' . $param['param_name'] . '" class=" spb-input spb-select ' . $param['param_name'] . ' ' . $param['type'] . '" data-default="'.$default.'">';

                foreach ( $param['value'] as $val => $text_val ) {
                    if ( is_numeric( $text_val ) && is_string( $val ) || is_numeric( $text_val ) && is_numeric( $val ) ) {
                        $text_val = $val;
                    }
                    $text_val = __( $text_val, 'swift-framework-plugin' );
                    //$val      = strtolower( str_replace( array( " " ), array( "_" ), $val ) );
                    $val      = str_replace( array( " " ), array( "_" ), $val );
                    $selected = '';
                    if ( $val == $param_value ) {
                        $selected = ' selected="selected"';
                    }
                    $param_line .= '<option class="' . $val . '" value="' . $val . '"' . $selected . '>' . $text_val . '</option>';
                }
                $param_line .= '</select></div>';
            } // Multi - select
            else if ( $param['type'] == 'select-multiple' ) {
                $param_line .= '<div class="input-field col s12 m6 spb_param_value select-multiple" ><select multiple name="' . $param['param_name'] . '" class="spb-select ' . $param['param_name'] . ' ' . $param['type'] . '" type="hidden" value="" name="" multiple>';


                $selected_values = explode( ",", $param_value );
                $selected_values =  array_map('ltrim', $selected_values );

                
                foreach ( $param['value'] as $text_val => $val ) {

                   if ( is_numeric( $text_val ) && is_string( $val ) || is_numeric( $text_val ) && is_numeric( $val ) ) {
                        $text_val = $val;
                    }
                    $text_val = __( $text_val, 'swift-framework-plugin' );
                    $selected = '  ';   
                    
                   
                    if ( in_array( $val, $selected_values ) ) {
                        $selected = ' selected ';
                    }
                    
                    $param_line .= '<option id="' . $text_val . '"  value="' . $val . '"' . $selected . '>' . $val . '</option>';
                   
                    
                }
                $param_line .= '</select></div>'; 
            } // Multi (id) - select 
            else if ( $param['type'] == 'select-multiple-id' ) {
                   $param_line .= '<div class="input-field col s12 m6 spb_param_value dropdown" ><select multiple name="' . $param['param_name'] . '" class=" spb-select ' . $param['param_name'] . ' ' . $param['type'] . '" type="hidden" value="" name="" multiple>';
                //$param_line .= '<select multiple name="' . $param['param_name'] . '" class="spb_param_value spb-select ' . $param['param_name'] . ' ' . $param['type'] . '" type="hidden" value="" name="" multiple>';
    
                $selected_values = explode( ",", $param_value );
                
                $selected_values = array_map('ltrim', $selected_values);
    

                foreach ( $param['value'] as $val => $text_val ) {

                    $text_val = __( $text_val, 'swift-framework-plugin' );
                    $selected = '';


                    if ( in_array( $val, $selected_values )  ) {
                        $selected = ' selected="selected"';
                    }
                    
                    if ( $text_val != "" ) {
                        $param_line .= '<option id="' . $val . '" value="' . $val . '"' . $selected . '>' . $text_val . '</option>';
                    }
                    
                }
                $param_line .= '</select></div>';
            } // Encoded field
            else if ( $param['type'] == 'textarea_encoded' ) {
                $param_value = htmlentities( rawurldecode( base64_decode( $param_value ) ), ENT_COMPAT, 'UTF-8' );
                $param_line .= '<textarea name="' . $param['param_name'] . '" class="spb_param_value spb-textarea spb-encoded-textarea ' . $param['param_name'] . ' ' . $param['type'] . '">' . $param_value . '</textarea>';
            } // WYSIWYG field
            else if ( $param['type'] == 'textarea_html' ) {
                $param_line .= $this->getTinyHtmlTextArea( $param, $param_value );
            } // Checkboxes with post types
            else if ( $param['type'] == 'posttypes' ) {
                $param_line .= '<input class="spb_param_value spb-checkboxes" type="hidden" value="" name="" />';
                $args       = array(
                    'public' => true
                );
                $post_types = get_post_types( $args );
                foreach ( $post_types as $post_type ) {
                    $checked = "";
                    if ( $post_type != 'attachment' ) {
                        if ( in_array( $post_type, explode( ",", $param_value ) ) ) {
                            $checked = ' checked="checked"';
                        }
                        $param_line .= ' <input id="' . $post_type . '" class="' . $param['param_name'] . ' ' . $param['type'] . '" type="checkbox" name="' . $param['param_name'] . '"' . $checked . '> ' . $post_type;
                    }
                }
            } // Exploded textarea
            else if ( $param['type'] == 'exploded_textarea' ) {
                $param_value = str_replace( ",", "\n", $param_value );
                $param_line .= '<textarea name="' . $param['param_name'] . '" class="spb_param_value spb-textarea ' . $param['param_name'] . ' ' . $param['type'] . '">' . $param_value . '</textarea>';
            } // Big Regular textarea
            else if ( $param['type'] == 'textarea_raw_html' ) {
                // $param_value = __($param_value, 'swift-framework-plugin');
                $param_line .= '<textarea name="' . $param['param_name'] . '" class="spb_param_value spb-textarea_raw_html ' . $param['param_name'] . ' ' . $param['type'] . '" rows="16">' . htmlentities( rawurldecode( base64_decode( $param_value ) ), ENT_COMPAT, 'UTF-8' ) . '</textarea>';
            } // Regular textarea
            else if ( $param['type'] == 'textarea' ) {
                $param_value = __( $param_value, 'swift-framework-plugin' );
                $param_line .= '<textarea name="' . $param['param_name'] . '" class="spb_param_value spb-textarea ' . $param['param_name'] . ' ' . $param['type'] . '">' . $param_value . '</textarea>';
            } // Attach images
            else if ( $param['type'] == 'attach_images' ) {  
                // TODO: More native way
                $param_value = spb_removeNotExistingImgIDs( $param_value );
                $param_line .= '<input type="hidden" class="spb_param_value sf_gallery_widget_attached_images_ids ' . $param['param_name'] . ' ' . $param['type'] . '" name="' . $param['param_name'] . '" value="' . $param_value . '" />';
                $param_line .= '<a class="button sf_gallery_widget_add_images" href="#" title="' . __( 'Add images', 'swift-framework-plugin' ) . '">' . __( 'Add images', 'swift-framework-plugin' ) . '</a>';
                $param_line .= '<div class="sf_galSFages">';
                $param_line .= '<ul class="sf_gallery_widget_attached_images_list">';
                $param_line .= ( $param_value != '' ) ? spb_fieldAttachedImages( explode( ",", $param_value ) ) : '';
                $param_line .= '</ul>';
                $param_line .= '</div>'; 
                $param_line .= '<div class="spb_clear"></div>';
            } else if ( $param['type'] == 'attach_image' ) {
                // TODO: More native way
                $param_value = spb_removeNotExistingImgIDs( preg_replace( '/[^\d]/', '', $param_value ) );
                $param_line .= '<input type="hidden" class="spb_param_value sf_gallery_widget_attached_images_ids ' . $param['param_name'] . ' ' . $param['type'] . '" name="' . $param['param_name'] . '" value="' . $param_value . '" />';
                $param_line .= '<a class="button sf_gallery_widget_add_images" href="#" use-single="true" title="' . __( 'Add image', 'swift-framework-plugin' ) . '" data-uploader_title="' . __( 'Add image', 'swift-framework-plugin' ) . '">' . __( 'Add image', 'swift-framework-plugin' ) . '</a>';
                $param_line .= '<div class="sf_gallery_widget_attached_images">';
                $param_line .= '<ul class="sf_gallery_widget_attached_images_list">';
                $param_line .= ( $param_value != '' ) ? spb_fieldAttachedImages( explode( ",", $param_value ) ) : '';
                $param_line .= '</ul>';
                $param_line .= '</div>';
                $param_line .= '<div class="spb_clear"></div>';
            }       //
            else if ( $param['type'] == 'widgetised_sidebars' ) {
                $spb_sidebar_ids = Array();
                $sidebars        = $GLOBALS['wp_registered_sidebars'];
                $param_line .= '<div class="input-field col s12 m6 spb_param_value dropdown" ><select name="' . $param['param_name'] . '" class=" spb-input spb-select ' . $param['param_name'] . ' ' . $param['type'] . '" data-default="'.$default.'">';
            
                foreach ( $sidebars as $sidebar ) {
                    $selected = '';
                    if ( $sidebar["id"] == $param_value ) {
                        $selected = ' selected="selected"';
                    }
                    $sidebar_name = __( $sidebar["name"], 'swift-framework-plugin' );
                    $param_line .= '<option value="' . $sidebar["id"] . '"' . $selected . '>' . $sidebar_name . '</option>';
                }
                $param_line .= '</select></div>';
            } else if ( $param['type'] == 'icon-picker' ) {
                $value = __( $param_value, 'swift-framework-plugin' );
                $value = $param_value;
                $icon_type = isset( $param['icon_type'] ) ? $param['icon_type'] : 'font';
                $icon_grid_data = "";
                if ( $icon_type == "font" ) {
                    $icon_grid_data = sf_get_icons_list();
                } else if ( $icon_type == "svg" ) {
                    if ( is_array($param['data']) ) {
                        foreach ($param['data'] as $path => $icon) {
                            $icon_grid_data .= '<li class="svg-icon" data-icon="'.$icon.'" data-path="'.$path.'"><i class="svg-icon-picker-item" style="background-image: url(' . $path . ');"></i></li>';
                        }
                    } else {
                        $icon_grid_data = $param['data'];
                    }
                }
                
                if ( $icon_type == "svg" ) {
                    $param_line .= '<div class="span9 edit_form_line" style="display: none;"><input type="text" class="search-icon-grid textfield" placeholder="Search Icon"></div><input name="'.$param['param_name'].'" class="spb_param_value '.$param['param_name'].' '.$param['type'].'" type="text" value="'.$value.'" /><ul class="font-icon-grid std-grid">'.$icon_grid_data.'</ul>';
                } else {
                    $param_line .= '<div class="span9 edit_form_line"><input type="text" class="search-icon-grid textfield" placeholder="Search Icon"></div><input name="'.$param['param_name'].'" class="spb_param_value '.$param['param_name'].' '.$param['type'].'" type="text" value="'.$value.'" style="visibility: hidden;height: 0;" /><ul class="font-icon-grid std-grid">'.$icon_grid_data.'</ul>';
                }
            }


            return $param_line;
        }

        protected function getTinyHtmlTextArea( $param = array(), $param_value ) {
            $param_line = '';

            if ( function_exists( 'wp_editor' ) ) {
                $default_content = __( $param_value, 'swift-framework-plugin' );
                $output_value    = '';
                // WP 3.3+
                ob_start();
                wp_editor( $default_content, 'spb_tinymce_' . $param['param_name'], array(
                        'editor_class'  => 'spb_param_value spb-textarea spb_tinymce ' . $param['param_name'] . ' ' . $param['type'],
                        'media_buttons' => true,
                        'wpautop'       => true
                    ) );
                $output_value = ob_get_contents();
                ob_end_clean();
                $param_line .= $output_value;
            }

            return $param_line;
        }

        protected function getPricingTableColumn( $i, $featureField = "", $value = "" ) {
            $name = $highlight_column = $price = $period = $btn_text = $href = $target = $bg_color = $text_color = $el_class = "";
            $highlight_column_yes = $highlight_column_no = $selected_blank = $selected_self = "";
            $highlight_column = 'no';
            $target = "_self";
            
            $name = __('Plan Name', 'swift-framework-plugin' );
            
            // Extract Shortcode Param values 
            if ( $value != "" ) {                                                          
                $name              = $this->extractRepeaterFields( 'name' , $value );
                $highlight_column  = $this->extractRepeaterFields( 'highlight_column' , $value );
                $price             = $this->extractRepeaterFields( 'price' , $value );
                $period            = $this->extractRepeaterFields( 'period' , $value );
                $btn_text          = $this->extractRepeaterFields( 'btn_text' , $value );
                $href              = $this->extractRepeaterFields( 'href' , $value );
                $target            = $this->extractRepeaterFields( 'target' , $value );
                $el_class          = $this->extractRepeaterFields( 'el_class' , $value );   
            }

            if ( $highlight_column == 'yes' ) {
                $highlight_column_yes = 'selected';
            } else {
                $highlight_column_no = 'selected';
            }

            if ( $target == '_blank' ) {
                $selected_blank = 'selected';
            } else{
                $selected_self = 'selected';
            }

            $param_line = '<div class="price_column_holder">';

            //Plan Name                        
            $param_line .= '<div class="row"><div class="input-field col s12"><input name="plan_name_' . $i . '" id="plan_name_' . $i . '" class="textfield validate active plan_name" type="text"  value="' . $name . '" /><label for="plan_name_' . $i . '">'  . __('Plan Name', 'swift-framework-plugin' ) . '</label></div></div>';
            
            // Highlight Column
            $param_line .= '<div class="row"><div class="input-field col s12 plan_highlight_column"><select id="select_highlight_column_' . $i . '" ><option value="no" ' . $highlight_column_no . '>' . __('No', 'swift-framework-plugin' ) . '</option><option value="yes" ' . $highlight_column_yes . '>' . __('Yes', 'swift-framework-plugin' ) . '</option></select><label for="select_highlight_column_' . $i . '">'  . __('Highlight Column', 'swift-framework-plugin' ) . '</label></div></div>';
           
            // Plan Price                        
            $param_line .= '<div class="row"><div class="input-field col s12"><input name="plan_price_' . $i . '" id="plan_price_' . $i . '" class="textfield validate active plan_price" type="text" value="' . $price . '" /><label for="plan_price_' . $i . '">'  . __('Plan Price', 'swift-framework-plugin' ) . '</label></div></div>';

            // Plan Period
            $param_line .= '<div class="row"><div class="input-field col s12"><input name="plan_period_' . $i . '" id="plan_period_' . $i . '" class="textfield validate active plan_period" type="text" value="' . $period . '" /><label for="plan_period_' . $i . '">'  . __('Plan Period', 'swift-framework-plugin' ) . '</label></div></div>';

            // Features
            if ( $featureField != "" ) {
                $param_line .= '<div class="row"><label>' . __("Features", "swift-framework-plugin") . '</label>' . $featureField . '</div>';
            }
            // Button Text
            $param_line .= '<div class="row"><div class="input-field col s12"><input name="plan_button_text_' . $i . '" id="plan_button_text_' . $i . '" class="textfield validate active plan_button_text" type="text" value="' . $btn_text . '" /><label for="plan_button_text_' . $i . '">'  . __('Button Text', 'swift-framework-plugin' ) . '</label></div></div>';
            
            // Link Url
            $param_line .= '<div class="row"><div class="input-field col s12"><input name="plan_link_url_' . $i . '" id="plan_link_url_' . $i . '" class="textfield validate active plan_link_url" type="text" value="' . $href . '" /><label for="plan_link_url_' . $i . '">'  . __('Button Link URL', 'swift-framework-plugin' ) . '</label></div></div>';

            // Link Target
            $param_line .= '<div class="row"><div class="input-field col s12 plan_link_target"> <select id="select_link_target_' . $i . '"><option value="_self" ' . $selected_self . '>' . __('Same Window', 'swift-framework-plugin' ) . '</option><option value="_blank" ' . $selected_blank . '>' . __('New Window', 'swift-framework-plugin' ) . '</option></select><label for="select_link_target_' . $i . '">'  . __('Link Target', 'swift-framework-plugin' ) . '</label></div></div>';

            // Extra Class
            $param_line .= '<div class="row"><div class="input-field col s12"><input name="plan_extra_class_' . $i . '" id="plan_extra_class_' . $i . '" class="textfield validate active plan_extra_class" type="text" value="" /><label for="plan_extra_class_' . $i . '">'  . __('Extra Class', 'swift-framework-plugin' ) . '</label></div></div>';
            
            //HTML End 
            $param_line .= '</div>';

            return $param_line;
        }

        protected function getPricingTableFeatureField( $j, $column_content = "" ) {

            $feature_name = __('Feature Name', 'swift-framework-plugin' );
            $feature_el_class = "";
            
            //Extract Column Features Shortcode Param values
            if ( $column_content != "" ) {                
                $feature_name = $this->extractRepeaterFields( 'feature_name' , $column_content );
                $feature_el_class = $this->extractRepeaterFields( 'el_class' , $column_content );
            } 
               
            

            $param_feature_line = '<li><div class="collapsible-header">' . $feature_name . '<div class="pricing_col_actions"><span class="icon-drag-handle"></span><span class="icon-delete"></span></div></div><div class="collapsible-body"><div class="section_repeater_features">';
            $param_feature_line .= '<div class="row"><div class="input-field col s12"><input name="plan_feature_name_' . $j . '" id="plan_feature_name_' . $j . '" class="textfield validate active plan_feature_name" type="text"  value="' . $feature_name . '" /><label for="plan_feature_name_' . $j . '">'  . __('Feature Name', 'swift-framework-plugin' ) . '</label></div></div>';
            $param_feature_line .= '<div class="row"><div class="input-field col s12"><input name="plan_feature_name_el_class_' . $j . '" id="plan_feature_name_el_class_' . $j . '" class="textfield validate active feature_el_class" type="text"  value="' . $feature_el_class . '" /><label for="plan_feature_name_el_class_' . $j . '">'  . __('Extra class', 'swift-framework-plugin' ) . '</label></div></div>';
            $param_feature_line .= ' </div></div></li>';

            return $param_feature_line;
        }
    }


    class SwiftPageBuilderShortcode_Settings extends SwiftPageBuilderShortcode_UniversalAdmin {

        public function content( $atts, $content = null ) {
            return '';
        }

        public function contentAdmin( $atts, $content ) {

            $output = $header_output = '';


            //if ( $content != NULL ) { $content = apply_filters('the_content', $content); }
            if ( isset( $this->settings['params'] ) ) {
                $shortcode_attributes = array();

                foreach ( $this->settings['params'] as $param ) {
                    if ( $param['param_name'] != 'content' ) {
                        $shortcode_attributes[ $param['param_name'] ] = isset( $param['value'] ) ? $param['value'] : null;
                    } else if ( $param['param_name'] == 'content' && $content == null ) {
                        $content = $param['value'];
                    }
                }
                $shortcode_attributes['asset_name'] = '';
                extract( shortcode_atts(
                    $shortcode_attributes
                    , $atts ) );

                $header_output .= '<div class="spb_edit_form_elements">';
                $header_output .= '<div class="spb-edit-modal-inner">';
                $header_output .= '<div id="edit-modal-header">';
                $header_output .= '<h2>' . __( 'Edit', 'swift-framework-plugin' ) . ' ' . __( $this->settings['name'], 'swift-framework-plugin' ) . '</h2>';
                $header_output .= '<div class="edit_form_actions"><a href="#" id="cancel-background-options">' . __( 'Cancel', 'swift-framework-plugin' ) . '</a><a href="#" class="spb_save_edit_form button-primary">' . __( 'Save', 'swift-framework-plugin' ) . '</a></div>';
                $header_output .= '<div class="header_tabs">';

               

                $output .= '<div class="spb_edit_wrap">';

                $count_params = 0;

                foreach ( $this->settings['params'] as $param ) {
                    $count_params++;
                    
                    if(  $count_params == 1 ){
                        $active_tab = "active_tab";
                    }else{
                        $active_tab = "";
                    }
                        
                    $param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : null; 
                 
                    if ( is_array( $param_value ) ) {
                        // Get first element from the array
                        reset( $param_value );
                        $first_key   = key( $param_value );
                        $param_value = $param_value[ $first_key ];
                    }
                    
                  
                    
                    if ( $param['type'] == "section_tab" ) {
                         
                         $header_output .= '<div class="spb_element_section_tab ' . $active_tab . '" data-content-name="'. $param['param_name'] .'">' . __( $param['heading'], 'swift-framework-plugin' ) . '</div>';

                         if ( $count_params > 1 && $count_params < count($this->settings['params']) ){
                            $output .= '</div>';   
                         }

                         
                         $output .= '<div class="'. $param['param_name'] .' element_tab_content">';

                    } else {
                         $output .= $this->singleParamEditHolder( $param, $param_value );
                    }
                }

                $output .= '</div>'; //close spb_edit_wrap
            }

            $output = $header_output .'</div></div>' . $output;

            return $output;
        }
    }

?>
