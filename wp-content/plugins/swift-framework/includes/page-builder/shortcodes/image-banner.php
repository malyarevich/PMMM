<?php

class SwiftPageBuilderShortcode_spb_image_banner extends SwiftPageBuilderShortcode {

    public function content( $atts, $content = null ) {

        $el_class = $width = $image_size = $animation = $image_link = $link_target = $el_position = $el_class = $image = $img_url = '';

        extract( shortcode_atts( array(
            'title'           => '',
            'width'           => '1/1',
            'image'           => $image,
            'image_size'      => '',
            'fixed_height'    => '',
            'content_pos'	  => 'center',
            'content_textalign' => 'center',
            'animation'         => 'none',
            'animation_delay' => '200',
            'image_link'      => '',
            'link_target'     => '',
            'el_position'     => '',
            'el_class'        => ''
        ), $atts ) );

        if ( $image_size == "" ) {
            $image_size = "large";
        }

        $output = '';
        $image_id = preg_replace( '/[^\d]/', '', $image );
        $img      = spb_getImageBySize( array(
            'attach_id'  => $image_id,
            'thumb_size' => $image_size
        ) );
        $image_width = $image_height = "";
        $image_src_size = 'large';
        if ( $image_size == "full" ) {
            $image_src_size = 'full';  
        }
        $img_object  = wp_get_attachment_image_src( $image, $image_src_size );
        if ( is_array( $img_object ) && !empty( $img_object ) ) {
        	$img_url = $img_object[0];
        	$image_width = $img_object[1];
        	$image_height = $img_object[2];
        }
		$image_meta 		= spb_get_attachment_meta( $image_id );
		$image_alt = $image_title = $caption_html = "";
		if ( isset($image_meta) ) {
			$image_title 		= esc_attr( $image_meta['title'] );
			$image_alt 			= esc_attr( $image_meta['alt'] );
		}

		if ( $image_alt == "" ) {
			$image_alt = $image_title;
		}

        $el_class = $this->getExtraClass( $el_class );
        $width    = spb_translateColumnWidthToSpan( $width );

        $output .= "\n\t" . '<div class="spb_content_element spb_image_banner ' . $width . $el_class . '">';
        $output .= "\n\t\t" . '<div class="spb-asset-content">';
        $output .= "\n\t\t" . do_shortcode( '[sf_imagebanner image_id="'.$image_id.'" image_size="'.$image_size.'" fixed_height="'.$fixed_height.'" image="'.$img_url.'" image_width="'.$image_width.'" image_height="'.$image_height.'" image_alt="'.$image_alt.'" animation="'.$animation.'" animation_delay="'.$animation_delay.'" contentpos="'.$content_pos.'" textalign="'.$content_textalign.'" href="'.$image_link.'" target="'.$link_target.'"]'.$content.'[/sf_imagebanner]' );
        $output .= "\n\t\t" . '</div>';
        $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

        $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

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
                $output .= ( $img ? $img['thumbnail'] : '<img width="150" height="150" src="' . SwiftPageBuilder::getInstance()->assetURL( 'img/blank_f7.gif' ) . '" class="attachment-thumbnail" alt="" title="" />' ) . '<a href="#" class="column_edit_trigger' . ( $img && ! empty( $img['p_img_large'][0] ) ? ' image-exists' : '' ) . '"><i class="spb-icon-single-image"></i>' . __( 'No image yet! Click here to select it now.', 'swift-framework-plugin' ) . '</a>';
            }
        } else {
            $output .= '<' . $param['holder'] . ' class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
        }

        return $output;
    }
}

SPBMap::map( 'spb_image_banner', array(
    "name"   => __( "Image Banner", 'swift-framework-plugin' ),
    "base"   => "spb_image_banner",
    "class"  => "spb_image_banner_widget spb_tab_media",
    "icon"   => "icon-image-banner",
    "params" => array(
        array(
            "type"        => "attach_image",
            "heading"     => __( "Image", 'swift-framework-plugin' ),
            "param_name"  => "image",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "dropdown-id",
            "heading"     => __( "Image Size", 'swift-framework-plugin' ),
            "param_name"  => "image_size",
            "value"       => spb_get_image_sizes(),
            "std"         => 'full',
            "description" => __( "Select the source size for the image (NOTE: this doesn't affect it's size on the front-end, only the quality).", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Fixed image height", 'swift-framework-plugin' ),
            "param_name"  => "fixed_height",
            "value"       => "",
            "description" => __( "If you wish to fix the image height, for purposes such as a grid, then please provide a numeric value here (no px).", 'swift-framework-plugin' )
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
            "type"        => "dropdown",
            "heading"     => __( "Content Position", 'swift-framework-plugin' ),
            "param_name"  => "content_pos",
            "value"       => array(
                __( "Left", 'swift-framework-plugin' )               => "left",
                __( "Center", 'swift-framework-plugin' )              => "center",
                __( "Right", 'swift-framework-plugin' )             => "right",
            ),
            "description" => __( "Choose the alignment for the content.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Content Text Align", 'swift-framework-plugin' ),
            "param_name"  => "content_textalign",
            "value"       => array(
                __( "Left", 'swift-framework-plugin' )               => "left",
                __( "Center", 'swift-framework-plugin' )              => "center",
                __( "Right", 'swift-framework-plugin' )             => "right",
            ),
            "description" => __( "Choose the alignment for the text within the content.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Animation", 'swift-framework-plugin' ),
            "param_name"  => "animation",
            "value"       => spb_animations_list(),
            "description" => __( "Select an intro animation for the content that will show it when it appears within the viewport.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Animation Delay", 'swift-framework-plugin' ),
            "param_name"  => "animation_delay",
            "value"       => "200",
            "description" => __( "If you wish to add a delay to the animation, then you can set it here (default 200) (ms).", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Add link to image", 'swift-framework-plugin' ),
            "param_name"  => "image_link",
            "value"       => "",
            "description" => __( "If you would like the image to link to a URL, then enter it here. NOTE: this will override the lightbox functionality if you have enabled it.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Link target", 'swift-framework-plugin' ),
            "param_name"  => "link_target",
            "value"       => array(
                __( "Self", 'swift-framework-plugin' )       => "_self",
                __( "New Window", 'swift-framework-plugin' ) => "_blank"
            ),
            "description" => __( "Select if you want the link to open in a new window", 'swift-framework-plugin' )
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
