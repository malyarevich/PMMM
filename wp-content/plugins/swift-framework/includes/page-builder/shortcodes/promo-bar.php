<?php

    /*
    *
    *	Swift Page Builder - Promo Bar Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_promo_bar extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $btn_color = $btn_text = $display_type = $target = $href = $promo_bar_text = $fullwidth = $inline_style = $inline_style_alt = $width = $position = $el_class = '';
            
            extract( shortcode_atts( array(
                'btn_color'      => 'accent',
                'btn_text'       => '',
                'btn_type'		 => 'dropshadow',
                'target'         => '',
                'display_type'   => '',
                'href'           => '',
                'shadow'         => 'yes',
                'promo_bar_text' => '',
                'promo_bar_text_size' => '',
                'width'          => '1/1',
                'bg_color'       => '',
                'text_color'     => '',
                'page_align'     => 'no',
                'fullwidth'      => 'no',
                'el_class'       => '',
                'el_position'    => '',
            ), $atts ) );
            $output = '';

            /* SIDEBAR CONFIG
            ================================================== */
            $sidebar_config = spb_get_post_meta( get_the_ID(), 'sf_sidebar_config', true );
            $sidebars = '';
            if ( ( $sidebar_config == "left-sidebar" ) || ( $sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }

            $width    = spb_translateColumnWidthToSpan( $width );
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            if ( $bg_color != "" ) {
                $inline_style .= 'background-color:' . $bg_color . ';';
            }
            if ( $text_color != "" ) {
                $inline_style_alt .= 'color:' . $text_color . ';';
            }

            // Button type
            $btn_type = str_replace("_", " ", $btn_type);

            if ( $target == 'same' || $target == '_self' ) {
                $target = '_self';
            }
            if ( $target != '' ) {
                $target = $target;
            }

            $next_icon = apply_filters( 'sf_next_icon' , '<i class="ss-navigateright"></i>' );

            $output .= '<div class="spb-promo-wrap spb_content_element clearfix ' . $width . ' ' . $position . $el_class . '">' . "\n";
            $output .= '<div class="sf-promo-bar ' . $display_type . ' text-size-'.$promo_bar_text_size.' page-align-'.$page_align.'" style="' . $inline_style . '">';
            if ( $page_align == "yes" ) {
                $output .= '<div class="container">';
            }
            if ( $display_type == "promo-button" ) {
                $output .= '<p class="'.$promo_bar_text_size.'" style="' . $inline_style_alt . '">' . $promo_bar_text . '</p>';
                $output .= '<a href="' . $href . '" target="' . $target . '" class="sf-button '.$btn_type.' ' . $btn_color . '">' . $btn_text . '</a>';
            } else if ( $display_type == "promo-arrow" ) {
                $output .= '<a href="' . $href . '" target="' . $target . '" style="' . $inline_style_alt . '"><p>' . $promo_bar_text . '</p>' . $next_icon . '</a>';
            } else if ( $display_type == "promo-text" ) {
                $output .= '<a href="' . $href . '" target="' . $target . '" class="'.$promo_bar_text_size.'" style="' . $inline_style_alt . '"><p>' . do_shortcode( $promo_bar_text ) . '</p></a>';
            } else {
                $output .= '<p>' . do_shortcode( $content ) . '</p>';
            }
            if ( $page_align == "yes" ) {
                $output .= '</div>';
            }
            $output .= '</div>';
            $output .= '</div> ' . $this->endBlockComment( '.spb-promo-wrap' ) . "\n";

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );
            
            return $output;
        }
    }

    $button_color = array(
        'Accent'              => 'accent',
        'Black'               => 'black',
        'White'               => 'white',
        'Grey'                => 'grey',
        'Light Grey'          => 'lightgrey',
        'Gold'                => 'gold',
        'Light Blue'          => 'lightblue',
        'Green'               => 'green',
        'Gold'                => 'gold',
        'Turquoise'           => 'turquoise',
        'Pink'                => 'pink',
        'Orange'              => 'orange',
        'Turquoise'           => 'turquoise',
        'Transparent - Light' => 'transparent-light',
        'Transparent - Dark'  => 'transparent-dark',
    );

    $button_type = array(
    	 __( "Standard", 'swift-framework-plugin' )	=> 'standard',
    	 __( "Drop Shadow", 'swift-framework-plugin' )	=> 'dropshadow',
    	 __( "Bordered", 'swift-framework-plugin' )	=> 'bordered',
    	 __( "Rounded", 'swift-framework-plugin' )	=> 'rounded',
    	 __( "Rounded Bordered", 'swift-framework-plugin' )	=> 'rounded bordered',
    );

    $target_arr = array(
        __( "Same window", 'swift-framework-plugin' ) => "_self",
        __( "New window", 'swift-framework-plugin' )  => "_blank"
    );

    $text_size = array(
        __( "Standard", 'swift-framework-plugin' ) => "standard",
        __( "Impact", 'swift-framework-plugin' )  => "impact-text",
        __( "Impact (Large)", 'swift-framework-plugin' )  => "impact-text-large"
    );

	$promo_bar_types = array(
	    __( 'Text + Button', 'swift-framework-plugin' )                => 'promo-button',
	    __( 'Text + Arrow (Full Bar Link)', 'swift-framework-plugin' ) => 'promo-arrow',
	    __( 'Text Only (Full Bar Link)', 'swift-framework-plugin' )    => 'promo-text',
	    __( 'Custom Content', 'swift-framework-plugin' )               => 'promo-custom'
	);

	if ( spb_get_theme_name() == "atelier" ) {
		$promo_bar_types = array(
		    __( 'Text + Button', 'swift-framework-plugin' )                => 'promo-button',
		    __( 'Text Only (Full Bar Link)', 'swift-framework-plugin' )    => 'promo-text',
		    __( 'Custom Content', 'swift-framework-plugin' )               => 'promo-custom'
		);
	}

    $promo_bar_types = apply_filters( 'spb_promo_bar_types', $promo_bar_types );

    SPBMap::map( 'spb_promo_bar', array(
        "name"     => __( "Promo Bar", 'swift-framework-plugin' ),
        "base"     => "spb_promo_bar",
        "class"    => "button_grey spb_tab_media",
        "icon"     => "icon-promo-bar",
        "controls" => "edit_popup_delete",
        "params"   => array(
            array(
                "type"        => "dropdown",
                "heading"     => __( "Display Type", 'swift-framework-plugin' ),
                "param_name"  => "display_type",
                "value"       => $promo_bar_types,
                "description" => __( "Choose the display type for the promo bar.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Promo Text", 'swift-framework-plugin' ),
                "param_name"  => "promo_bar_text",
                "holder"      => 'div',
                "required"       => array("display_type", "!=", "promo-custom"),
                "value"       => __( "Enter your text here", 'swift-framework-plugin' ),
                "description" => __( "Enter the text for the promo bar here, this is also where you can set the custom content if the option is selected above.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textarea_html",
                "heading"     => __( "Promo Content Text", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "holder"      => "div",
                "required"       => array("display_type", "=", "promo-custom"),
                "value"       => __( "Enter your text here", 'swift-framework-plugin' ),
                "description" => __( "Enter the text for the promo bar here, this is also where you can set the custom content if the option is selected above.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Promo Text Size", 'swift-framework-plugin' ),
                "param_name"  => "promo_bar_text_size",
                "value"       => $text_size,
                "description" => __( "Select the text size", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Button Text", 'swift-framework-plugin' ),
                "param_name"  => "btn_text",
                "value"       => __( "Button Text", 'swift-framework-plugin' ),
                "required"       => array("display_type", "=", "promo-button"),
                "description" => __( "The text that appears on the button.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Button Color", 'swift-framework-plugin' ),
                "param_name"  => "btn_color",
                "value"       => $button_color,
                "required"       => array("display_type", "=", "promo-button"),
                "description" => __( "Button color.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Button Type", 'swift-framework-plugin' ),
                "param_name"  => "btn_type",
                "value"       => $button_type,
                "required"       => array("display_type", "=", "promo-button"),
                "description" => __( "Button color.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Link URL", 'swift-framework-plugin' ),
                "param_name"  => "href",
                "value"       => "",
                "description" => __( "The link for the promo bar.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "dropdown",
                "heading"    => __( "Link Target", 'swift-framework-plugin' ),
                "param_name" => "target",
                "value"      => $target_arr
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Background color", 'swift-framework-plugin' ),
                "param_name"  => "bg_color",
                "value"       => "",
                "description" => __( "Select a background colour for the asset here.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "colorpicker",
                "heading"     => __( "Text color", 'swift-framework-plugin' ),
                "param_name"  => "text_color",
                "value"       => "",
                "description" => __( "Select a text colour for the asset here.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Align to page", 'swift-framework-plugin' ),
                "param_name"  => "page_align",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "description" => __( "Select if you'd like the asset to be aligned to the page (contained).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Full Width", 'swift-framework-plugin' ),
                "param_name"  => "fullwidth",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "std" => "no",
                "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
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
