<?php

    /*
    *
    *	Swift Page Builder - Tour Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_tour extends SwiftPageBuilderShortcode {

        public function __construct( $settings ) {
            parent::__construct( $settings );
            SwiftPageBuilder::getInstance()->addShortCode( array( 'base' => 'spb_tab' ) );
        }

        public function contentAdmin( $atts, $content = null ) {
            $width                = $custom_markup = '';
            $shortcode_attributes = array(
            	'tour_asset_title' => '',
            	'interval'        => 0,
            	'width'           => '1/1',
            	'el_position'     => '',
            	'el_class'        => ''
            );
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

            global $sf_tour_pane_count;

            $sf_tour_pane_count = 0;

            $tab_titles = array();
            $tab_icons  = array();

            // Extract tab titles
            preg_match_all( '/spb_tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[1] ) ) {
                $tab_titles = $matches[1];
            }

            preg_match_all( '/spb_tab title="([^\"]+)" icon="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[2] ) ) {
                $tab_icons = $matches[2];
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
                    $tmp .= '<li data-title-icon="' . $icon_text . '"><a href="#tab-' . sanitize_title( $tab[0] ) . '"><span>' . $tab[0] . '</span></a><a class="delete_tab"><span class="icon-delete"></span></a><a class="edit_tab"><span class="icon-edit"></span></a></li>';
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
            $tour_asset_title = $type = $interval = $width = $el_position = $el_class = '';
            extract( shortcode_atts( array(
                'tab_asset_title' => '',
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

            // Extract tab titles
            preg_match_all( '/spb_tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[1] ) ) {
                $tab_titles = $matches[1];
            }

            preg_match_all( '/spb_tab title="([^\"]+)" icon="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
            if ( isset( $matches[2] ) ) {
                $tab_icons = $matches[2];
            }

            $tabs_nav  = '';
            $tab_count = 0;

            $tabs_nav .= '<ul class="nav nav-tabs">';
            foreach ( $tab_titles as $tab ) {

                if ( isset( $tab_icons[ $tab_count ][0] ) ) {
                    $icon_text = '<i class="' . $tab_icons[ $tab_count ][0] . '"></i>';
                } else {
                    $icon_text = '';
                }

                if ( $tab_count == 0 ) {
                    //$tabs_nav .= '<li class="active"><a href="#' . preg_replace( "#[[:punct:]]#", "", ( strtolower( str_replace( ' ', '-', $tab[0] ) ) ) ) . '" data-toggle="tab">' . $icon_text . $tab[0] . '</a></li>';
                    $tabs_nav .= '<li class="active"><a href="#' .  preg_replace( '/\s+/', '-', sanitize_title($tab[0]) ) . '" data-toggle="tab">' . $icon_text . $tab[0] . '</a></li>';
                } else {
                    $tabs_nav .= '<li><a href="#' .preg_replace( '/\s+/', '-', sanitize_title($tab[0]) ) . '" data-toggle="tab">' . $icon_text . $tab[0] . '</a></li>';
                }
                $tab_count ++;
            }
            $tabs_nav .= '</ul>' . "\n";

            $output .= "\n\t" . '<div class="' . $element . ' spb_tour spb_content_element ' . $width . $el_class . '" data-interval="' . $interval . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content spb_wrapper spb_tour_tabs_wrapper">';
            $output .= ( $tour_asset_title != '' ) ? "\n\t\t\t" . $this->spb_title( $tour_asset_title, '' ) : '';
            $output .= "\n\t\t\t" . '<div class="tabbable tabs-left">';
            $output .= "\n\t\t\t\t" . $tabs_nav;
            $output .= "\n\t\t\t\t" . '<div class="tab-content">';
            $output .= "\n\t\t\t\t" . spb_format_content( $content );
            $output .= "\n\t\t\t\t" . '</div>';
            $output .= "\n\t\t\t" . '</div>';
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    SPBMap::map( 'spb_tour', array(
        "name"            => __( "Tour Section", 'swift-framework-plugin' ),
        "base"            => "spb_tour",
        "controls"        => "full",
        "class"           => "spb_tour spb_tab_ui",
        "icon"            => "icon-tour",
        "wrapper_class"   => "clearfix",
        "params"          => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "tour_asset_title",
                "value"       => "",
                "description" => __( "What text use as widget title. Leave blank if no title is needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Auto rotate slides", 'swift-framework-plugin' ),
                "param_name"  => "interval",
                "value"       => array( 0, 3, 5, 10, 15 ),
                "description" => __( "Auto rotate slides each X seconds. Select 0 to disable.", 'swift-framework-plugin' )
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
		<div class="spb_tabs_holder clearfix">
			%content%
		</div>',
        'default_content' => '

            <ul class="clearfix ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all ui-sortable" role="tablist">
                 <li data-title-icon="" class="ui-state-default ui-corner-top ui-tabs-active ui-state-active ui-sortable-handle" role="tab" tabindex="0" aria-controls="tab-1" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true">
                     <a href="#tab-1" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1"><span>' . __( 'Slide 1', 'swift-framework-plugin' ) . '</span></a>
                     <a class="delete_tab"><span class="icon-delete"></span></a>
                     <a class="edit_tab"><span class="icon-edit"></span></a>
                </li>
                <li data-title-icon="" class="ui-state-default ui-corner-top ui-sortable-handle" role="tab" tabindex="-1" aria-controls="tab-2" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false">
                    <a href="#tab-2" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2"><span>' . __( 'Slide 2', 'swift-framework-plugin' ) . '</span></a>
                    <a class="delete_tab"><span class="icon-delete"></span></a>
                    <a class="edit_tab"><span class="icon-edit"></span></a>
                </li>
            </ul> 
            
            <div id="tab-1" class="row-fluid spb_column_container spb_sortable not-column-inherit not-sortable ui-sortable ui-droppable ui-resizable ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false"> 

                 [spb_text_block width="1/1"] ' . __( 'This is a text block. Click the edit button to change this text.', 'swift-framework-plugin' ) . ' [/spb_text_block]

                <div class="tabs_expanded_helper">
                      <a href="#" class="add_element"><span class="icon-add"></span>' . __( "Add Element", 'swift-framework-plugin' ) .'</a>
                      <a href="#" class="add_tab"><span class="icon-add-tab"></span>' . __( "Add Tab", 'swift-framework-plugin' ) .'</a>
                </div>
                
                <div class="container-helper">
                      <a href="#" class="add-element-to-column btn-floating waves-effect waves-light"><span class="icon-add"></span></a>
                </div>
                
            </div>

            <div id="tab-2" class="row-fluid spb_column_container spb_sortable not-column-inherit not-sortable ui-sortable ui-droppable ui-resizable ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="ui-id-2" role="tabpanel" aria-hidden="true" style="display: none;"> 

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
        'default_content222' => '
		<ul>
			<li><a href="#tab-1"><span>' . __( 'Slide 1', 'swift-framework-plugin' ) . '</span></a><a class="delete_tab"></a><a class="edit_tab"></a></li>
			<li><a href="#tab-2"><span>' . __( 'Slide 2', 'swift-framework-plugin' ) . '</span></a><a class="delete_tab"></a><a class="edit_tab"></a></li>
		</ul>
	
		<div id="tab-1" class="row-fluid spb_column_container spb_sortable_container not-column-inherit">
			[spb_text_block width="1/1"] ' . __( 'This is a text block. Click the edit button to change this text.', 'swift-framework-plugin' ) . ' [/spb_text_block]
		</div>
	
		<div id="tab-2" class="row-fluid spb_column_container spb_sortable_container not-column-inherit">
			[spb_text_block width="1/1"] ' . __( 'This is a text block. Click the edit button to change this text.', 'swift-framework-plugin' ) . ' [/spb_text_block]
		</div>',
        "js_callback"     => array( "init" => "spbTabsInitCallBack", "shortcode" => "spbTabsGenerateShortcodeCallBack" )
        
    ) );
