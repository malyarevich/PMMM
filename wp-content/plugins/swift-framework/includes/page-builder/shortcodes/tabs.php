<?php

    /*
    *
    *	Swift Page Builder - Tabs Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_tab extends SwiftPageBuilderShortcode {
        
        public function content( $atts, $content = null ) {
            $title = '';
            extract( shortcode_atts( array(
                'title' => __( "Tab", 'swift-framework-plugin' ),
                'icon'  => '',
                'id'    => ''
            ), $atts ) );
            $output = '';

            $id = sanitize_title( $id );

            if ( $id == '' ) {
				$output .= "\n\t\t\t" . '<div id="' . preg_replace( '/\s+/', '-', sanitize_title($title) ) . '" class="tab-pane load">';
            } else {
                $output .= "\n\t\t\t" . '<div id="' . preg_replace( '/\s+/', '-', $id ) . '" class="tab-pane load">';
            }
            $output .= "\n\t\t\t\t" . spb_format_content( $content );
            $output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment( '.spb_tab' );

            return $output;
        }

        public function contentAdmin( $atts, $content ) {
            
            $title    = '';
            $defaults = array( 'title' => __( 'Tab', 'swift-framework-plugin' ), 'icon' => '' );
            extract( shortcode_atts( $defaults, $atts ) );

            if ( strpos( $content, 'spb_map_pin' ) == true ) {
            } else {
                return '<div id="tab-' . sanitize_title( $title ) . '" class="row-fluid spb_column_container spb_sortable not-column-inherit not-sortable spb-dont-resize">' . do_shortcode( $content ) . SwiftPageBuilder::getInstance()->getLayout()->getContainerHelper() . '<div class="tabs_expanded_helper"><a href="#" class="add_element"><span class="icon-add"></span>Add Element</a><a href="#" class="add_tab"><span class="icon-add-tab"></span>Add Tab</a></div></div>';
              //  return '<div id="tab-' . sanitize_title( $title ) . '" class="row-fluid spb_column_container spb_sortable_container not-column-inherit">' . do_shortcode( $content ) . SwiftPageBuilder::getInstance()->getLayout()->getContainerHelper() . '</div>';
            }


        }
    }

    class SwiftPageBuilderShortcode_spb_tabs extends SwiftPageBuilderShortcode {

        public function __construct( $settings ) {
            parent::__construct( $settings );
            SwiftPageBuilder::getInstance()->addShortCode( array( 'base' => 'spb_tab' ) );
        }

        public function contentAdmin( $atts, $content = null ) {
            $width                = $custom_markup = '';
            $shortcode_attributes = array( 'width' => '1/1' );
            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {
                    //$shortcode_attributes[$param['param_name']] = $param['value'];
                    if ( is_string( $param['value'] ) ) {
                        $shortcode_attributes[ $param['param_name'] ] = __( $param['value'], 'swift-framework-plugin' );
                    } else {
                        $shortcode_attributes[ $param['param_name'] ] = $param['value'];
                    }
                } else if ( $param['param_name'] == 'content' && $content == null ) {
                    //$content = $param['value'];
                    $content = __( $param['value'], 'swift-framework-plugin' );
                }
            }
            extract( shortcode_atts(
                $shortcode_attributes
                , $atts ) );


            // Extract tab titles
            preg_match_all( '/spb_tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
            $tab_titles = array();
            $tab_icons  = array();
            $tab_ids    = array();

            if ( isset( $matches[1] ) ) {
                $tab_titles = $matches[1];
            }

            preg_match_all( '/spb_tab title="([^\"]+)" icon="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[2] ) ) {
                $tab_icons = $matches[2];
            }

            preg_match_all( '/" id="([^"]+)"/', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[1] ) ) {
                $tab_ids = $matches[1];
            }

            $output = '';

            $tmp = '';
            if ( count( $tab_titles ) ) {

                $tmp .= '<ul class="clearfix">';
                $tab_index = 0;
                foreach ( $tab_titles as $tab ) {

                    if ( isset( $tab_icons[ $tab_index ][0] ) ) {
                        $icon_text = $tab_icons[ $tab_index ][0];
                    } else {
                        $icon_text = '';
                    }

                    $tab_id = '';

                    if ( isset( $tab_ids[ $tab_index ][0] ) ) {
                        $tab_id = $tab_ids[ $tab_index ][0];
                    }

                    $tmp .= '<li id="' . $tab_id . '" data-title-icon="' . $icon_text . '"><a href="#tab-' . sanitize_title( $tab[0] ) . '"><span>' . $tab[0] . '</span></a><a class="delete_tab"><span class="icon-delete"></span></a><a class="edit_tab"><span class="icon-edit"></span></a></li>';
                    $tab_index ++;
                }
                $tmp .= '</ul>';
            } else {
                $output .= do_shortcode( $content );
            }
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
            //$elem = str_ireplace('%spb_element_content%', $iner, $elem);

            if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
                if ( $content != '' ) {
                    $custom_markup = str_ireplace( "%content%", $tmp . $content, $this->settings["custom_markup"] );
                } else if ( $content == '' && isset( $this->settings["default_content"] ) && $this->settings["default_content"] != '' ) {
                    $custom_markup = str_ireplace( "%content%", $this->settings["default_content"], $this->settings["custom_markup"] );
                }
                //$output .= do_shortcode($this->settings["custom_markup"]);
                $iner .= do_shortcode( $custom_markup );
            }
            $elem   = str_ireplace( '%spb_element_content%', $iner, $elem );
            $output = $elem;

            return $output;
        }

        public function content( $atts, $content = null ) {
            $tab_asset_title = $type = $interval = $center_tabs = $width = $el_position = $el_class = '';
            extract( shortcode_atts( array(
                'tab_asset_title' => '',
                'tabs_type'       => 'standard',
                'center_tabs'     => '',
                'interval'        => 0,
                'width'           => '1/1',
                'el_position'     => '',
                'el_class'        => ''
            ), $atts ) );
            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $element = 'spb_tabs';
            if ( 'spb_tour' == $this->shortcode ) {
                $element = 'spb_tour';
            }

            $tab_titles = array();
            $tab_icons  = array();
            $tab_ids    = array();

            // Extract tab titles
            preg_match_all( '/spb_tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[1] ) ) {
                $tab_titles = $matches[1];
            }

            preg_match_all( '/spb_tab title="([^\"]+)" icon="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[2] ) ) {
                $tab_icons = $matches[2];
            }

            preg_match_all( '/" id="([^"]+)"/', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[1] ) ) {
                $tab_ids = $matches[1];
            }

            $tabs_nav  = '';
            $tab_count = 0;

            if ( $center_tabs == "yes" ) {
                $tabs_nav .= '<ul class="nav nav-tabs center-tabs">';
            } else {
                $tabs_nav .= '<ul class="nav nav-tabs">';
            }
            if ( $tabs_type == "dynamic" ) {
                $tabs_nav .= '<li class="menu-icon"><i class="fa-bars"></i></li>';
            }

            foreach ( $tab_titles as $tab ) {

                if ( isset( $tab_ids[ $tab_count ][0] ) ) {
                    $tab_id = $tab_ids[ $tab_count ][0];
                } else {
                    $tab_id = $tab[0];
                }

                $tab_id = sanitize_title( $tab_id );

                if ( isset( $tab_icons[ $tab_count ][0] ) ) {
                    $icon_text = '<i class="' . $tab_icons[ $tab_count ][0] . '"></i>';
                } else {
                    $icon_text = '';
                }

                if ( $tab_count == 0 ) {
                    $tabs_nav .= '<li class="active"><a href="#' . preg_replace( "/[^A-Za-z0-9-]/i", "", ( strtolower( str_replace( ' ', '-', $tab_id ) ) ) ) . '" data-toggle="tab"><span>' . $icon_text . $tab[0] . '</span></a></li>';      
                } else {
                    $tabs_nav .= '<li><a href="#' . preg_replace( "/[^A-Za-z0-9-]/i", "", ( strtolower( str_replace( ' ', '-', $tab_id ) ) ) ) . '" data-toggle="tab"><span>' . $icon_text . $tab[0] . '</span></a></li>';                    
                }

                $tab_count ++;
            }
            $tabs_nav .= '</ul>' . "\n";

            $output .= "\n\t" . '<div class="' . $element . ' tabs-type-' . $tabs_type . ' spb_content_element ' . $width . $el_class . '" data-interval="' . $interval . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content spb_wrapper spb_tabs_wrapper">';
            $output .= ( $tab_asset_title != '' ) ? "\n\t\t\t" . $this->spb_title( $tab_asset_title, '' ) : '';
            $output .= "\n\t\t\t" . $tabs_nav;
            $output .= "\n\t\t\t" . '<div class="tab-content">';
            $output .= "\n\t\t\t" . spb_format_content( $content );
            $output .= "\n\t\t\t" . '</div>';
            if ( 'spb_tour' == $this->shortcode ) {
                $output .= "\n\t\t\t" . '<div class="spb_tour_next_prev_nav"> <span class="spb_prev_slide"><a href="#prev" title="' . __( 'Previous slide', 'swift-framework-plugin' ) . '">' . __( 'Previous slide', 'swift-framework-plugin' ) . '</a></span> <span class="spb_next_slide"><a href="#next" title="' . __( 'Next slide', 'swift-framework-plugin' ) . '">' . __( 'Next slide', 'swift-framework-plugin' ) . '</a></span></div>';
            }
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }
    
    $tabs_type = array(
        __( 'Standard', 'swift-framework-plugin' )               => "standard",
        __( 'Dynamic Icon/Text Menu', 'swift-framework-plugin' ) => "dynamic"
    );
    
    if ( spb_get_theme_name() == "atelier" ) {
    	$tabs_type = array(
    	    __( 'Standard', 'swift-framework-plugin' )               => "standard",
    	);	
    }

    SPBMap::map( 'spb_tabs', array(
        "name"            => __( "Tabs", 'swift-framework-plugin' ),
        "base"            => "spb_tabs",
        "controls"        => "full",
        "class"           => "spb_tabs spb_tab_ui",
        "icon"            => "icon-tabs",
        "params"          => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "tab_asset_title",
                "value"       => "",
                "description" => __( "What text use as widget title. Leave blank if no title is needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Tabs type", 'swift-framework-plugin' ),
                "param_name"  => "tabs_type",
                "value"       => $tabs_type,
                "description" => __( "Choose the tabs type.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Center tabs", 'swift-framework-plugin' ),
                "param_name"  => "center_tabs",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Choose if you'd like to center the tabs.", 'swift-framework-plugin' )
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
		  </div>',  
          'default_content' => '

            <ul class="clearfix ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all ui-sortable" role="tablist">
                 <li data-title-icon="" class="ui-state-default ui-corner-top ui-tabs-active ui-state-active ui-sortable-handle" role="tab" tabindex="0" aria-controls="tab-1" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true">
                     <a href="#tab-1" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1"><span>' . __( 'Tab 1', 'swift-framework-plugin' ) . '</span></a>
                     <a class="delete_tab"><span class="icon-delete"></span></a>
                     <a class="edit_tab"><span class="icon-edit"></span></a>
                </li>
                <li data-title-icon="" class="ui-state-default ui-corner-top ui-sortable-handle" role="tab" tabindex="-1" aria-controls="tab-2" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false">
                    <a href="#tab-2" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2"><span>' . __( 'Tab 2', 'swift-framework-plugin' ) . '</span></a>
                    <a class="delete_tab"><span class="icon-delete"></span></a>
                    <a class="edit_tab"><span class="icon-edit"></span></a>
                </li>
            </ul> 
            
            <div id="tab-1" class="row-fluid spb_column_container spb_sortable not-column-inherit not-sortable ui-sortable ui-droppable ui-tabs-panel ui-widget-content ui-corner-bottom spb-dont-resize" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false"> 

                 [spb_text_block width="1/1"] ' . __( 'This is a text block. Click the edit button to change this text.', 'swift-framework-plugin' ) . ' [/spb_text_block]

                <div class="tabs_expanded_helper">
                      <a href="#" class="add_element"><span class="icon-add"></span>' . __( "Add Element", 'swift-framework-plugin' ) .'</a>
                      <a href="#" class="add_tab"><span class="icon-add-tab"></span>' . __( "Add Tab", 'swift-framework-plugin' ) .'</a>
                </div>

                <div class="container-helper">
                      <a href="#" class="add-element-to-column btn-floating waves-effect waves-light light-green "><span class="icon-add"></span></a>
                </div>
                
            </div>

            <div id="tab-2" class="row-fluid spb_column_container spb_sortable not-column-inherit not-sortable ui-sortable ui-droppable ui-tabs-panel ui-widget-content ui-corner-bottom spb-dont-resize" aria-labelledby="ui-id-2" role="tabpanel" aria-hidden="true" style="display: none;"> 

                   [spb_text_block width="1/1"] ' . __( 'This is a text block. Click the edit button to change this text.', 'swift-framework-plugin' ) . ' [/spb_text_block]

                <div class="tabs_expanded_helper">
                      <a href="#" class="add_element"><span class="icon-add"></span>' . __( "Add Element", 'swift-framework-plugin' ) .'</a>
                      <a href="#" class="add_tab"><span class="icon-add-tab"></span>' . __( "Add Tab", 'swift-framework-plugin' ) .'</a>
                </div>

                <div class="container-helper">
                      <a href="#" class="add-element-to-column btn-floating waves-effect waves-light"><span class="icon-add"></span></a>
                </div>
                
            </div> 
        ',
        "js_callback"     => array( "init" => "spbTabsInitCallBack", "shortcode" => "spbTabsGenerateShortcodeCallBack" )
    ) );
