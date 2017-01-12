<?php

    /*
	*
	*	Swift Shortcodes & Generator Class
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
	*
	*/

    /* SHORTCODE GENERATOR SETUP
    ================================================== */
    // Create TinyMCE's editor button & plugin for Swift Framework Shortcodes
    if ( !function_exists('sf_shortcodegen_sc_button') ) {
        function sf_shortcodegen_sc_button() {
            if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
                add_filter( 'mce_external_plugins', 'sf_shortcodegen_add_tinymce_plugin' );
                add_filter( 'mce_buttons', 'sf_shortcodegen_register_shortcode_button' );
            }
        }
        add_action( 'init', 'sf_shortcodegen_sc_button' );
    }

    if ( !function_exists('sf_shortcodegen_register_shortcode_button') ) {
        function sf_shortcodegen_register_shortcode_button( $button ) {
            array_push( $button, 'separator', 'swiftframework_shortcodes' );

            return $button;
        }
    }

    if ( !function_exists('sf_shortcodegen_add_tinymce_plugin') ) {
        function sf_shortcodegen_add_tinymce_plugin( $plugins ) {
            $plugins['swiftframework_shortcodes'] = plugin_dir_url( __FILE__ ) . 'generator/tinymce.editor.plugin.js';
            return $plugins;
        }
    }

    if ( ! function_exists( 'swiftframework_shortcode_generator' ) ) {
        function swiftframework_shortcode_generator() {
            require_once( plugin_dir_path( __FILE__ ) . 'generator/interface.php' );   
            wp_die();
        }
        add_action( 'wp_ajax_swiftframework_shortcode_generator', 'swiftframework_shortcode_generator' );
        add_action( 'wp_ajax_nopriv_swiftframework_shortcode_generator', 'swiftframework_shortcode_generator' );
    }


    /* ADD SHORTCODE FUNCTIONALITY TO WIDGETS
    ================================================== */
    add_filter( 'widget_text', 'do_shortcode' );
    

    /* ==================================================

	SHORTCODES OUTPUT

	================================================== */
    if ( !function_exists('sf_current_theme') ) {
        function sf_current_theme() {

            $current_theme = "";
            if ( get_option('sf_theme') != "" ) {
                $current_theme = get_option('sf_theme');
            }

            return $current_theme;
        }
    }


    /* ALERT SHORTCODE
	================================================== */
    if ( !function_exists('sf_alert') ) {
        function sf_alert( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "type" => "info"
            ), $atts ) );

            return '<div class="alert ' . $type . '">' . do_shortcode( $content ) . '</div>';
        }
        add_shortcode( 'alert', 'sf_alert' );
    }

    /* ADD TO CART BUTTON SHORTCODE
    ================================================== */
    if ( !function_exists('sf_addtocart_button') ) {
        function sf_addtocart_button( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "product_id" => "",
                "colour"     => "",
                "extraclass" => ''
            ), $atts ) );

            $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );

            $button_class = 'add_to_cart_button ajax_add_to_cart product_type_simple sf-button ' . $colour . ' ' . $extraclass;

            $button_output = '<div class="add-to-cart-wrap add-to-cart-shortcode">';
            $button_output .= '<a href="'.$shop_page_url.'?add-to-cart='.$product_id.'" rel="nofollow" data-product_id="'.$product_id.'" class="'.$button_class.'" data-default_icon="sf-icon-add-to-cart" data-loading_text="'.__("Adding...", "swiftframework").'" data-added_text="'.__("Item added", "swiftframework").'" data-added_short="'.__("Added", "swiftframework").'" data-default_text="'.__("Add to cart", "swiftframework").'"><i class="sf-icon-add-to-cart"></i><span>'.__("Add to cart", "swiftframework").'</span></a>';
            $button_output .= '</div>';

            return $button_output;
        }
        add_shortcode( 'sf_addtocart_button', 'sf_addtocart_button' );
    }


    /* BUTTON SHORTCODE
	================================================== */
    if ( !function_exists('sf_button') ) {
        function sf_button( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "size"       => "standard",
                "colour"     => "",
                "type"       => "default",
                "link"       => "#",
                "target"     => '_self',
                "rounded"     => '',
                "dropshadow" => '',
                "icon"       => '',
                "extraclass" => ''
            ), $atts ) );

            $button_output = $type_class = "";

            if ( $type == "standard" ) {
                $type_class = "default";
            } else {
                $type_class = $type;
            }

            $button_class = 'sf-button ' . $size . ' ' . $colour . ' ' . $type_class . ' ' . $extraclass;

            if ( $rounded == "yes" ) {
                $button_class .= ' sf-button-rounded';
            }

            if ( $dropshadow == "yes" ) {
                $button_class .= " dropshadow";
            }

            if ( $icon != "" ) {
                $button_class .= " sf-button-has-icon";
            }

            if ( $type == "sf-icon-reveal" || $type == "sf-icon-stroke" ) {
                $button_output .= '<a class="' . $button_class . '" href="' . $link . '" target="' . $target . '">';
                $button_output .= '<i class="' . $icon . '"></i>';
                $button_output .= '<span class="text">' . do_shortcode( $content ) . '</span>';
                $button_output .= '</a>';
            } else {
                $button_output .= '<a class="' . $button_class . '" href="' . $link . '" target="' . $target . '">';
                if ( $type == "bordered" && sf_current_theme() != "atelier" ) {
                    $button_output .= '<span class="sf-button-border"></span>';
                }
                if ( $type == "rotate-3d" ) {
                    $button_output .= '<span class="text" data-text="' . $content . '">' . do_shortcode( $content ) . '</span>';
                } else {
                    $button_output .= '<span class="text">' . do_shortcode( $content ) . '</span>';
                }
                if ( $icon != "" ) {
                    $button_output .= '<i class="' . $icon . '"></i>';
                }
                $button_output .= '</a>';
            }

            return $button_output;
        }
        add_shortcode( 'sf_button', 'sf_button' );
    }

    /* ICON SHORTCODE
	================================================== */
    if ( !function_exists('sf_icon') ) {
        function sf_icon( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "size"      => "",
                "icon"      => "",
                "image"     => "",
                "svg"       => "",
                "animate_svg" => "",
                "character" => "",
                "cont"      => "",
                "float"     => "",
                "color"     => "",
                "bgcolor"   => "",
                "link"      => "",
                "target"    => "_self",
            ), $atts ) );

            if ( substr( $image, 0, 3 ) === "ss-" || substr( $image, 0, 3 ) === "fa-" || substr( $image, 0, 7 ) === "nucleo-" || substr( $image, 0, 3 ) === "sf-" ) {
                $icon  = $image;
                $image = "";
            }

            if ( strlen( $character ) > 1 ) {
                $character = substr( $character, 0, 2 );
            }

            global $sf_svg_icon_id;
            if ( $sf_svg_icon_id == "" ) {
                $sf_svg_icon_id = 1;
            } else {
                $sf_svg_icon_id ++;
            }

            $icon_output = "";

            if ( $image != "" ) {
                $icon_output .= '<span class="sf-icon image-display sf-icon-float-' . $float . '"><img src="' . $image . '" alt="icon box image"/></span>';
            } else if ( $svg != "" ) {

                // Enqueue script
                wp_enqueue_script( 'vivus' );

                $svg_el_class = '';
                if ( $color != "" ) {
                    $svg_el_class = 'has-color';
                }
                $svg_class = $directory = '';  
                if ( strpos($svg, '/') === false ) {
                    $svg = str_replace('svg-icon-picker-item ', '', $svg);
                    if (strpos($svg, 'outline-svg') !== false || strpos($svg, 'outline') !== false) {
                        $svg_el_class .= ' outline-svg';
                    }
                    $svg = str_replace('outline-svg ', '', $svg);
                    $directory = apply_filters( 'spb_svg_template_directory', get_template_directory_uri() . '/images/svgs/' );
                    $svg = $directory . $svg . '.svg';
                }

                if ( $animate_svg == "yes" ) {
                    $icon_output .= '<div id="sf-svg-'.$sf_svg_icon_id.'" class="sf-svg-icon-holder sf-svg-icon-animate ' . $svg_class . ' '.$svg_el_class.'" data-svg-src="' . $svg . '" data-anim-type="delayed" data-path-timing="ease-in" data-anim-timing="ease-out" style="stroke: '.$color.';"></div>';
                } else {
                    $icon_output .= '<div id="sf-svg-'.$sf_svg_icon_id.'" class="sf-svg-icon-holder sf-svg-icon-animate animation-disabled ' . $svg_class . ' '.$svg_el_class.'" data-svg-src="' . $svg . '" data-anim-type="delayed" data-path-timing="ease-in" data-anim-timing="ease-out" style="stroke: '.$color.';"></div>';
                }
            } else {
                if ( $cont == "yes" ) {
                    if ( $character != "" ) {
                        $icon_output .= '<div class="sf-icon-cont cont-' . $size . ' sf-icon-float-' . $float . '" style="background-color:' . $bgcolor . ';"><span class="sf-icon-character sf-icon sf-icon-' . $size . '" style="color:' . $color . ';">' . $character . '</span></div>';
                    } else {
                        $icon_output .= '<div class="sf-icon-cont cont-' . $size . ' sf-icon-float-' . $float . '" style="background-color:' . $bgcolor . ';"><i class="' . $icon . ' sf-icon sf-icon-' . $size . '" style="color:' . $color . ';"></i></div>';
                    }
                } else {
                    if ( $character != "" ) {
                        $icon_output .= '<span class="sf-icon-character sf-icon sf-icon-float-' . $float . ' sf-icon-' . $size . '" style="color:' . $color . ';">' . $character . '</span>';
                    } else {
                        $icon_output .= '<i class="' . $icon . ' sf-icon sf-icon-float-' . $float . ' sf-icon-' . $size . '" style="color:' . $color . ';"></i>';
                    }
                }
            }

            if ( $link != "" ) {
                $icon_output = '<a href="' . $link . '" target="' . $target . '" class="linked-icon">' . $icon_output . '</a>';
            }


            return $icon_output;
        }
        add_shortcode( 'icon', 'sf_icon' );
        add_shortcode( 'sf_icon', 'sf_icon' );
    }


    /* ICON BOX SHORTCODE
	================================================== */
    if ( !function_exists('sf_iconbox') ) {
        function sf_iconbox( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "type"            => "",
                "icon_type"       => "",
                "icon"            => "",
                "image"           => "",
                "character"       => "",
                "svg"             => "",
                "animate_svg"     => "",
                "color"           => "",
                "bg_color"        => "",
                "text_color"      => "",
                "icon_color"      => "",
                "icon_bg_color"   => "",
                'flip_text_color' => "",
                'flip_bg_color'   => "",
                "animated_box_style" => "",
                "animated_box_rounded" => "",
                "title"           => "",
                "animation"       => "",
                "animation_delay" => "",
                "link"            => "",
                "target"          => "_self",
            ), $atts ) );

            $icon_box = $extra_class = "";

            if ( $icon_type == "svg" ) {
                $image = "";
                $icon = "";
                $character = "";
            } else if ( $icon_type == "icon" ) {
                $image = "";
                $svg = "";
                $character = "";
            } else if ( $icon_type == "character" ) {
                $image = "";
                $icon = "";
                $svg = "";
            } else if ( $icon_type == "image" ) {
                $svg = "";
                $icon = "";
                $character = "";
            }

            if ( $type == "animated-alt" && $animated_box_style == "stroke" ) {
                $bg_color = "";
                $extra_class = "animated-stroke-style";
            }

            if ( $type == "animated-alt" && $animated_box_rounded == "no" ) {
                $extra_class = "animated-no-rounded";
            }

            if ( substr( $image, 0, 3 ) === "ss-" || substr( $image, 0, 3 ) === "fa-" || substr( $image, 0, 3 ) === "sf-" || substr( $image, 0, 5 ) === "icon-" ) {
                $icon  = $image;
                $image = "";
            }

            if ( substr( $image, 0, 4 ) === "http" ) {
                $extra_class = 'has-image';
            }

            if ( $animation != "" && $type != "animated" && $type != "animated-alt" ) {
                $icon_box .= '<div class="sf-icon-box sf-icon-box-' . $type . ' sf-animation sf-icon-' . $color . ' ' . $extra_class . '" data-animation="' . $animation . '" data-delay="' . $animation_delay . '" style="background-color:' . $bg_color . ';">';
            } else {
                $icon_box .= '<div class="sf-icon-box sf-icon-box-' . $type . ' ' . $extra_class . '">';
            }

            if ( $type == "animated" ) {
                if ( $link != "" ) {
                    $icon_box .= '<a class="box-link" href="' . $link . '" target="' . $target . '"></a>';
                }
                $icon_box .= '<div class="inner">';
                $icon_box .= '<div class="front" style="background-color:' . $bg_color . ';">';
                $icon_box .= do_shortcode( '[sf_icon size="large" icon="' . $icon . '" character="' . $character . '" image="' . $image . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'"]' );
            }

            if ( $type == "animated-alt" ) {
                if ( $link != "" ) {
                    $icon_box .= '<a class="box-link" href="' . $link . '" target="' . $target . '"></a>';
                }
                $icon_box .= '<div class="height-adjust"></div>';
                $icon_box .= '<div class="inner">';
                $icon_box .= '<div class="front" style="background-color:' . $bg_color . ';">';
                $icon_box .= '<div class="back-title" data-title="' . $title . '" style="color:' . $text_color . ';"></div>';
                $icon_box .= '<div class="front-inner-wrap">';
                $icon_box .= do_shortcode( '[sf_icon icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            }

            if ( $type == "standard" && sf_current_theme() == "joyn" ) {
                $icon_box .= do_shortcode( '[sf_icon size="large" icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type == "standard" ) {
                $icon_box .= do_shortcode( '[sf_icon size="large" icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="yes" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type == "left-icon" ) {
                $icon_box .= do_shortcode( '[sf_icon size="small" icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="yes" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type == "left-icon-alt" ) {
                $icon_box .= do_shortcode( '[sf_icon size="medium" icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type == "standard-center" ) {
                $icon_box .= do_shortcode( '[sf_icon icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type == "standard-center-contained" ) {
                $icon_box .= do_shortcode( '[sf_icon icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="yes" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type == "standard-left" ) {
                $icon_box .= do_shortcode( '[sf_icon icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type == "standard-left-contained" ) {
                $icon_box .= do_shortcode( '[sf_icon icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="yes" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type == "vertical" ) {
                $icon_box .= '<div class="icon-wrap">';
                $icon_box .= do_shortcode( '[sf_icon icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
                $icon_box .= '</div>';
            } else if ( $type == "vertical-contained" ) {
                $icon_box .= '<div class="icon-wrap">';
                $icon_box .= do_shortcode( '[sf_icon icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="yes" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
                $icon_box .= '</div>';
            } else if ( $type == "boxed-one" || $type == "boxed-three" ) {
                $icon_box .= do_shortcode( '[sf_icon size="medium" icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="yes" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            } else if ( $type != "boxed-two" && $type != "boxed-four" && $type != "standard-title" && $type != "animated" && $type != "animated-alt" ) {
                $icon_box .= do_shortcode( '[sf_icon size="large" icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            }

            if ( $type == "boxed-one" || $type == "boxed-two" || $type == "boxed-three" || $type == "boxed-four" ) {
                $icon_box .= '<div class="sf-icon-box-content-wrap clearfix" style="background-color:' . $bg_color . ';">';
            } else {
                $icon_box .= '<div class="sf-icon-box-content-wrap clearfix">';
            }
            if ( $type == "boxed-two" ) {
                $icon_box .= do_shortcode( '[sf_icon size="medium" icon="' . $icon . '" character="' . $character . '" image="' . $image . '" svg="' . $svg . '" animate_svg="' . $animate_svg . '" float="none" cont="no" color="' . $icon_color . '" bgcolor="'. $icon_bg_color .'" link="' . $link . '" target="' . $target . '"]' );
            }

            if ( $type == "boxed-four" || $type == "standard-title" ) {
                if ( $character != "" ) {
                    if ( $link != "" ) {
                        $icon_box .= '<h3 style="color:' . $text_color . ';"><a href="' . $link . '" target="' . $target . '"><span class="sf-icon-character sf-icon-' . $color . '">' . $character . '</span> ' . $title . '</a></h3>';
                    } else {
                        $icon_box .= '<h3 style="color:' . $text_color . ';"><span class="sf-icon-character sf-icon-' . $color . '">' . $character . '</span> ' . $title . '</h3>';
                    }
                } else {
                    if ( $link != "" ) {
                        $icon_box .= '<h3 style="color:' . $text_color . ';"><a href="' . $link . '" target="' . $target . '"><i class="' . $icon . ' sf-icon-' . $color . '"></i> ' . $title . '</a></h3>';
                    } else {
                        $icon_box .= '<h3 style="color:' . $text_color . ';"><i class="' . $icon . ' sf-icon-' . $color . '"></i> ' . $title . '</h3>';
                    }
                }
            } else {
                if ( $link != "" ) {
                    $icon_box .= '<h3 style="color:' . $text_color . ';"><a href="' . $link . '" target="' . $target . '">' . $title . '</a></h3>';
                } else {
                    $icon_box .= '<h3 style="color:' . $text_color . ';">' . $title . '</h3>';
                }
            }

            if ( $type != "animated" && $type != "animated-alt" && $type != "bold" ) {
                $icon_box .= '<div class="sf-icon-box-content" style="color:' . $text_color . ';">' . do_shortcode( $content ) . '</div>';
            }

            $icon_box .= '</div>';

            if ( $type == "animated" ) {
                $icon_box .= '</div>';
                $icon_box .= '<div class="back sf-icon-' . $color . '" style="background-color:' . $flip_bg_color . ';"><table>';
                $icon_box .= '<tbody><tr>';
                $icon_box .= '<td>';
                $icon_box .= '<h3 style="color:' . $flip_text_color . ';">' . $title . '</h3>';
                $icon_box .= '<div class="sf-icon-box-content" style="color:' . $flip_text_color . ';">' . do_shortcode( $content ) . '</div>';
                $icon_box .= '</td>';
                $icon_box .= '</tr>';
                $icon_box .= '</tbody></table></div>';
                $icon_box .= '</div>';
            }

            if ( $type == "animated-alt" ) {
                $icon_box .= '</div>';
                $icon_box .= '</div>';
                $icon_box .= '<div class="back" style="background-color:' . $flip_bg_color . ';">';
                $icon_box .= '<div class="back-inner-wrap">';
                $icon_box .= '<div class="sf-icon-box-content" style="color:' . $flip_text_color . ';">' . do_shortcode( $content ) . '</div>';
                $icon_box .= '</div>';
                $icon_box .= '</div>';
                $icon_box .= '</div>';
            }

            $icon_box .= '</div>';

            return $icon_box;

        }
        add_shortcode( 'sf_iconbox', 'sf_iconbox' );
    }


    /* IMAGE BANNER SHORTCODE
	================================================== */
	if ( ! function_exists( 'sf_imagebanner' ) ) {
	    function sf_imagebanner( $atts, $content = null ) {
	        extract( shortcode_atts( array(
                "image_id"   => "",
                "image_size"   => "",
                "fixed_height" => "",
	            "image"      => "",
	            "image_width" => "",
	            "image_height" => "",
	            "image_alt"	 => "",
	            "href"       => "",
	            "target"     => "_self",
                "animation"  => "fade-in",
                "animation_delay"  => "200",
	            "contentpos" => "center",
	            "textalign"  => "center",
	            "extraclass" => ""
	        ), $atts ) );

	        $image_banner = $img_style = "";
            $img_style_array = array();

            if ( $fixed_height != "" ) {
                $img_style = 'style="height:' . $fixed_height. 'px;object-fit:cover;width:100%;"';
                $img_style_array = array( 'style' => 'height:' . $fixed_height. 'px;object-fit:cover;width:100%;' );
            }

	        $image_banner .= '<div class="sf-image-banner ' . $extraclass . '">';

	        if ( $href != "" ) {
		        if ( sf_current_theme() == "atelier" || sf_current_theme() == "uplift" ) {
					$image_banner .= '<figure class="animated-overlay">';
				}
	            $image_banner .= '<a class="sf-image-banner-link" href="' . $href . '" target="' . $target . '"></a>';
	        }

	        $image_banner .= '<div class="image-banner-content sf-animation content-' . $contentpos . ' text-' . $textalign . '" data-animation="' . $animation . '" data-delay="' . $animation_delay . '">';
	        $image_banner .= do_shortcode( $content );
	        $image_banner .= '</div>';

            $image_banner .= '<div class="img-wrap">';
			if ( $image_id != "" && $image_size != "" ) {
                $img      = wp_get_attachment_image( $image_id, $image_size, false, $img_style_array );
                $image_banner .= $img;
            } else if ( $image_width != "" && $image_height != "" ) {
				$image_banner .= '<img src="' . $image . '" width="'. $image_width .'" height="'. $image_height .'" alt="'. $image_alt .'" '.$img_style.' />';
			} else {
	        	$image_banner .= '<img src="' . $image . '" alt="'. $image_alt .'" '.$img_style.' />';
			}
            $image_banner .= '</div>';

			if ( $href != "" && ( sf_current_theme() == "atelier" || sf_current_theme() == "uplift" ) ) {
				$image_banner .= '<figcaption></figcaption>';
				$image_banner .= '</figure>';
			}

	        $image_banner .= '</div>';

	        global $sf_has_imagebanner;
	        $sf_has_imagebanner = true;

	        return $image_banner;

	    }
	    add_shortcode( 'sf_imagebanner', 'sf_imagebanner' );
    }


    /* COLUMN SHORTCODES
	================================================== */
    if ( !function_exists('sf_one_third') ) {
        function sf_one_third( $atts, $content = null ) {
            return '<div class="one_third">' . do_shortcode( $content ) . '</div>';
        }
        add_shortcode( 'one_third', 'sf_one_third' );
    }

    if ( !function_exists('sf_one_third_last') ) {
        function sf_one_third_last( $atts, $content = null ) {
            return '<div class="one_third last">' . do_shortcode( $content ) . '</div><div class="clearboth"></div>';
        }
        add_shortcode( 'one_third_last', 'sf_one_third_last' );
    }

    if ( !function_exists('sf_two_third') ) {
        function sf_two_third( $atts, $content = null ) {
            return '<div class="two_third">' . do_shortcode( $content ) . '</div>';
        }
        add_shortcode( 'two_third', 'sf_two_third' );
    }

    if ( !function_exists('sf_two_third_last') ) {
        function sf_two_third_last( $atts, $content = null ) {
            return '<div class="two_third last">' . do_shortcode( $content ) . '</div><div class="clearboth"></div>';
        }
        add_shortcode( 'two_third_last', 'sf_two_third_last' );
    }

    if ( !function_exists('sf_one_half') ) {
        function sf_one_half( $atts, $content = null ) {
            return '<div class="one_half">' . do_shortcode( $content ) . '</div>';
        }
        add_shortcode( 'one_half', 'sf_one_half' );
    }

    if ( !function_exists('sf_one_half_last') ) {
        function sf_one_half_last( $atts, $content = null ) {
            return '<div class="one_half last">' . do_shortcode( $content ) . '</div><div class="clearboth"></div>';
        }
        add_shortcode( 'one_half_last', 'sf_one_half_last' );
    }

    if ( !function_exists('sf_one_fourth') ) {
        function sf_one_fourth( $atts, $content = null ) {
            return '<div class="one_fourth">' . do_shortcode( $content ) . '</div>';
        }
        add_shortcode( 'one_fourth', 'sf_one_fourth' );
    }

    if ( !function_exists('sf_one_fourth_last') ) {
        function sf_one_fourth_last( $atts, $content = null ) {
            return '<div class="one_fourth last">' . do_shortcode( $content ) . '</div><div class="clearboth"></div>';
        }
        add_shortcode( 'one_fourth_last', 'sf_one_fourth_last' );
    }

    if ( !function_exists('sf_three_fourth') ) {
        function sf_three_fourth( $atts, $content = null ) {
            return '<div class="three_fourth">' . do_shortcode( $content ) . '</div>';
        }
        add_shortcode( 'three_fourth', 'sf_three_fourth' );
    }

    if ( !function_exists('sf_three_fourth_last') ) {
        function sf_three_fourth_last( $atts, $content = null ) {
            return '<div class="three_fourth last">' . do_shortcode( $content ) . '</div><div class="clearboth"></div>';
        }
        add_shortcode( 'three_fourth_last', 'sf_three_fourth_last' );
    }

    if ( !function_exists('sf_one_fifth') ) {
        function sf_one_fifth( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "gutter" => "yes",
            ), $atts ) );
            if ( $gutter == "no" ) {
                return '<div class="one_fifth no-gutter">' . do_shortcode( $content ) . '</div>';
            } else {
                return '<div class="one_fifth">' . do_shortcode( $content ) . '</div>';
            }
        }
        add_shortcode( 'one_fifth', 'sf_one_fifth' );
    }

    if ( !function_exists('sf_one_fifth_last') ) {
        function sf_one_fifth_last( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "gutter" => "yes",
            ), $atts ) );
            if ( $gutter == "no" ) {
                return '<div class="one_fifth no-gutter last">' . do_shortcode( $content ) . '</div>';
            } else {
                return '<div class="one_fifth last">' . do_shortcode( $content ) . '</div>';
            }
        }
        add_shortcode( 'one_fifth_last', 'sf_one_fifth_last' );
    }


    /* TABLE SHORTCODES
	================================================= */
    if ( !function_exists('sf_table_wrap') ) {
        function sf_table_wrap( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "type" => ''
            ), $atts ) );

            $output = '<table class="sf-table ' . $type . '"><tbody>';
            $output .= do_shortcode( $content ) . '</tbody></table>';

            return $output;

        }
        add_shortcode( 'table', 'sf_table_wrap' );
        add_shortcode( 'sf_table', 'sf_table_wrap' );
    }

    if ( !function_exists('sf_table_row') ) {
        function sf_table_row( $atts, $content = null ) {

            $output = '<tr>';
            $output .= do_shortcode( $content ) . '</tr>';

            return $output;
        }
        add_shortcode( 'trow', 'sf_table_row' );
    }

    if ( !function_exists('sf_table_column') ) {
        function sf_table_column( $atts, $content = null ) {

            $output = '<td>';
            $output .= do_shortcode( $content ) . '</td>';

            return $output;
        }
        add_shortcode( 'tcol', 'sf_table_column' );
    }

    if ( !function_exists('sf_table_head') ) {
        function sf_table_head( $atts, $content = null ) {

            $output = '<th>';
            $output .= do_shortcode( $content ) . '</th>';

            return $output;
        }
        add_shortcode( 'thcol', 'sf_table_head' );
    }


    /* PRICING TABLE SHORTCODES
	================================================= */
    if ( !function_exists('sf_pt_wrap') ) {
        function sf_pt_wrap( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "type"    => '',
                "columns" => ''
            ), $atts ) );

            $output = '<div class="pricing-table-wrap ' . $type . ' columns-' . $columns . '">' . do_shortcode( $content ) . '</div>';

            return $output;

        }
        add_shortcode( 'pricing_table', 'sf_pt_wrap' );
    }

    if ( !function_exists('sf_pt_column') ) {
        function sf_pt_column( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "highlight" => ''
            ), $atts ) );

            if ( $highlight == "yes" ) {
                $output = '<div class="pricing-table-column column-highlight">' . do_shortcode( $content ) . '</div>';
            } else {
                $output = '<div class="pricing-table-column">' . do_shortcode( $content ) . '</div>';
            }

            return $output;
        }
        add_shortcode( 'pt_column', 'sf_pt_column' );
    }

    if ( !function_exists('sf_pt_price') ) {
        function sf_pt_price( $atts, $content = null ) {

            $output = '<div class="pricing-table-price">' . do_shortcode( $content ) . '</div>';

            return $output;
        }
        add_shortcode( 'pt_price', 'sf_pt_price' );
    }

    if ( !function_exists('sf_pt_package') ) {
        function sf_pt_package( $atts, $content = null ) {

            $output = '<div class="pricing-table-package">' . do_shortcode( $content ) . '</div>';

            return $output;
        }
        add_shortcode( 'pt_package', 'sf_pt_package' );
    }

    if ( !function_exists('sf_pt_details') ) {
        function sf_pt_details( $atts, $content = null ) {

            $output = '<div class="pricing-table-details">' . do_shortcode( $content ) . '</div>';

            return $output;
        }
        add_shortcode( 'pt_details', 'sf_pt_details' );
    }

    if ( !function_exists('sf_pt_button') ) {
        function sf_pt_button( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "link"   => "#",
                "target" => '_self'
            ), $atts ) );

            $output = '<br/>' . do_shortcode( '[sf_button link="' . $link . '" target="' . $target . '" type="standard" colour="accent"]' . $content . '[/sf_button]' );

            return $output;
        }
        add_shortcode( 'pt_button', 'sf_pt_button' );
    }


    /* LABELLED PRICING TABLE SHORTCODES
	================================================= */
    if ( !function_exists('sf_lpt_wrap') ) {
        function sf_lpt_wrap( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "columns" => ''
            ), $atts ) );

            $output = '<div class="pricing-table-wrap labelled-pricing-table columns-' . $columns . '">' . do_shortcode( $content ) . '</div>';

            return $output;

        }
        add_shortcode( 'labelled_pricing_table', 'sf_lpt_wrap' );
    }

    if ( !function_exists('sf_lpt_label_column') ) {
        function sf_lpt_label_column( $atts, $content = null ) {

            $output = '<div class="pricing-table-column label-column">' . do_shortcode( $content ) . '</div>';

            return $output;
        }
        add_shortcode( 'lpt_label_column', 'sf_lpt_label_column' );
    }

    if ( !function_exists('sf_lpt_column') ) {
        function sf_lpt_column( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "highlight" => ''
            ), $atts ) );

            if ( $highlight == "yes" ) {
                $output = '<div class="pricing-table-column column-highlight">' . do_shortcode( $content ) . '</div>';
            } else {
                $output = '<div class="pricing-table-column">' . do_shortcode( $content ) . '</div>';
            }

            return $output;
        }
        add_shortcode( 'lpt_column', 'sf_lpt_column' );
    }

    if ( !function_exists('sf_lpt_price') ) {
        function sf_lpt_price( $atts, $content = null ) {

            $output = '<div class="pricing-table-price">' . do_shortcode( $content ) . '</div>';

            return $output;
        }
        add_shortcode( 'lpt_price', 'sf_lpt_price' );
    }

    if ( !function_exists('sf_lpt_package') ) {
        function sf_lpt_package( $atts, $content = null ) {

            $output = '<div class="pricing-table-package">' . do_shortcode( $content ) . '</div>';

            return $output;
        }
        add_shortcode( 'lpt_package', 'sf_pt_package' );
    }

    if ( !function_exists('sf_lpt_row_label') ) {
        function sf_lpt_row_label( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "alt" => "",
            ), $atts ) );

            if ( $alt == "yes" ) {
                $output = '<div class="pricing-table-label-row alt-row">' . do_shortcode( $content ) . '</div>';
            } else {
                $output = '<div class="pricing-table-label-row">' . do_shortcode( $content ) . '</div>';
            }

            return $output;
        }
        add_shortcode( 'lpt_row_label', 'sf_lpt_row_label' );
    }

    if ( !function_exists('sf_lpt_row') ) {
        function sf_lpt_row( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "alt" => "",
            ), $atts ) );

            if ( $alt == "yes" ) {
                $output = '<div class="pricing-table-row alt-row">' . do_shortcode( $content ) . '</div>';
            } else {
                $output = '<div class="pricing-table-row">' . do_shortcode( $content ) . '</div>';
            }

            return $output;
        }
        add_shortcode( 'lpt_row', 'sf_lpt_row' );
    }

    if ( !function_exists('sf_lpt_button') ) {
        function sf_lpt_button( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "link"   => "#",
                "target" => '_self'
            ), $atts ) );

            $output = '<div class="lpt-button-wrap">' . do_shortcode( '[sf_button link="' . $link . '" target="' . $target . '" type="standard" colour="accent"]' . $content . '[/sf_button]</div>' );

            return $output;
        }
        add_shortcode( 'lpt_button', 'sf_lpt_button' );
    }

    /* TYPOGRAPHY SHORTCODES
	================================================= */

    // Highlight Text
    if ( !function_exists('sf_highlighted') ) {
        function sf_highlighted( $atts, $content = null ) {
            return '<span class="highlighted">' . do_shortcode( $content ) . '</span>';
        }
        add_shortcode( "highlight", "sf_highlighted" );
    }

    // Decorative Ampersand
    if ( !function_exists('sf_decorative_ampersand') ) {
        function sf_decorative_ampersand( $atts, $content = null ) {
            return '<span class="decorative-ampersand">&</span>';
        }
        add_shortcode( "decorative_ampersand", "sf_decorative_ampersand" );
    }

    // Dropcap type 1
    if ( !function_exists('sf_dropcap1') ) {
        function sf_dropcap1( $atts, $content = null ) {
            return '<span class="dropcap1">' . do_shortcode( $content ) . '</span>';
        }
        add_shortcode( "dropcap1", "sf_dropcap1" );
    }

    // Dropcap type 2
    if ( !function_exists('sf_dropcap2') ) {
        function sf_dropcap2( $atts, $content = null ) {
            return '<span class="dropcap2">' . do_shortcode( $content ) . '</span>';
        }
        add_shortcode( "dropcap2", "sf_dropcap2" );
    }

    // Dropcap type 3
    if ( !function_exists('sf_dropcap3') ) {
        function sf_dropcap3( $atts, $content = null ) {
            return '<span class="dropcap3">' . do_shortcode( $content ) . '</span>';
        }
        add_shortcode( "dropcap3", "sf_dropcap3" );
    }

    // Dropcap type 4
    if ( !function_exists('sf_dropcap4') ) {
        function sf_dropcap4( $atts, $content = null ) {
            return '<span class="dropcap4">' . do_shortcode( $content ) . '</span>';
        }
        add_shortcode( "dropcap4", "sf_dropcap4" );
    }

    // Blockquote type 1
    if ( !function_exists('sf_blockquote1') ) {
        function sf_blockquote1( $atts, $content = null ) {
            return '<blockquote class="blockquote1">' . do_shortcode( $content ) . '</blockquote>';
        }
        add_shortcode( "blockquote1", "sf_blockquote1" );
    }

    // Blockquote type 2
    if ( !function_exists('sf_blockquote2') ) {
        function sf_blockquote2( $atts, $content = null ) {
            return '<blockquote class="blockquote2">' . do_shortcode( $content ) . '</blockquote>';
        }
        add_shortcode( "blockquote2", "sf_blockquote2" );
    }

    // Blockquote type 3
    if ( !function_exists('sf_blockquote3') ) {
        function sf_blockquote3( $atts, $content = null ) {
            return '<blockquote class="blockquote3">' . do_shortcode( $content ) . '</blockquote>';
        }
        add_shortcode( "blockquote3", "sf_blockquote3" );
    }

    // Blockquote type 4
    if ( !function_exists('sf_pullquote') ) {
        function sf_pullquote( $atts, $content = null ) {
            return '<blockquote class="pullquote">' . do_shortcode( $content ) . '</blockquote>';
        }
        add_shortcode( "pullquote", "sf_pullquote" );
    }


    /* LISTS SHORTCODES
	================================================= */
    if ( !function_exists('sf_list') ) {
        function sf_list( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "extraclass" => ''
            ), $atts ) );
            
            $output = '<ul class="sf-list '.$extraclass.'">' . do_shortcode( $content ) . '</ul>';

            return $output;
        }
        add_shortcode( 'list', 'sf_list' );
    }

    if ( !function_exists('sf_list_item') ) {
        function sf_list_item( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "icon" => ''
            ), $atts ) );
            $output = '<li><i class="' . $icon . '"></i><span>' . do_shortcode( $content ) . '</span></li>';

            return $output;
        }
        add_shortcode( 'list_item', 'sf_list_item' );
    }


    /* DIVIDER SHORTCODE
	================================================= */
    if ( !function_exists('sf_horizontal_break') ) {
        function sf_horizontal_break( $atts, $content = null ) {
            return '<div class="horizontal-break"> </div>';
        }
        add_shortcode( "hr", "sf_horizontal_break" );
    }

    if ( !function_exists('sf_horizontal_break_bold') ) {
        function sf_horizontal_break_bold( $atts, $content = null ) {
            return '<div class="horizontal-break bold"> </div>';
        }
        add_shortcode( "hr_bold", "sf_horizontal_break_bold" );
    }

    /* SOCIAL SHORTCODE
	================================================= */
    if ( ! function_exists( 'sf_social_icons' ) ) {
        function sf_social_icons( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "type"  => '',
                "size"  => 'standard',
                "style" => ''
            ), $atts ) );

            $theme_opts_name = $sf_options = "";
            if ( get_option('sf_theme') != "" ) {
                $theme_opts_name = 'sf_' . get_option('sf_theme') . '_options';
                $sf_options        = get_option( $theme_opts_name );
            } else {
                global $sf_options;
            }

            $twitter    = $sf_options['twitter_username'];
            $facebook   = $sf_options['facebook_page_url'];
            $dribbble   = $sf_options['dribbble_username'];
            $vimeo      = $sf_options['vimeo_username'];
            $tumblr     = $sf_options['tumblr_username'];
            $skype      = $sf_options['skype_username'];
            $linkedin   = $sf_options['linkedin_page_url'];
            $googleplus = $sf_options['googleplus_page_url'];
            $flickr     = $sf_options['flickr_page_url'];
            $youtube    = $sf_options['youtube_url'];
            $pinterest  = $sf_options['pinterest_username'];
            $foursquare = $sf_options['foursquare_url'];
            $instagram  = $sf_options['instagram_username'];
            $github     = $sf_options['github_url'];
            $xing       = $sf_options['xing_url'];
            $rss        = $sf_options['rss_url'];
            $behance    = $sf_options['behance_url'];
            $soundcloud = $sf_options['soundcloud_url'];
            $deviantart = $sf_options['deviantart_url'];
            $yelp       = "";
            $vk         = "";
            $twitch     = "";
            $snapchat   = "";
            $whatsapp   = "";
            if ( isset( $sf_options['yelp_url'] ) ) {
                $yelp = $sf_options['yelp_url'];
            }
            if ( isset( $sf_options['vk_url'] ) ) {
                $vk = $sf_options['vk_url'];
            }
            if ( isset( $sf_options['twitch_url'] ) ) {
                $twitch = $sf_options['twitch_url'];
            }
            if ( isset( $sf_options['snapchat_url'] ) ) {
                $snapchat = $sf_options['snapchat_url'];
            }
            if ( isset( $sf_options['whatsapp_url'] ) ) {
                $whatsapp = $sf_options['whatsapp_url'];
            }

            $social_icons = '';

            if ( $type == '' ) {
                if ( $twitter ) {
                    $social_icons .= '<li class="twitter"><a href="http://www.twitter.com/' . $twitter . '" target="_blank"><i class="fa-twitter"></i><i class="fa-twitter"></i></a></li>' . "\n";
                }
                if ( $facebook ) {
                    $social_icons .= '<li class="facebook"><a href="' . $facebook . '" target="_blank"><i class="fa-facebook"></i><i class="fa-facebook"></i></a></li>' . "\n";
                }
                if ( $dribbble ) {
                    $social_icons .= '<li class="dribbble"><a href="http://www.dribbble.com/' . $dribbble . '" target="_blank"><i class="fa-dribbble"></i><i class="fa-dribbble"></i></a></li>' . "\n";
                }
                if ( $youtube ) {
                    $social_icons .= '<li class="youtube"><a href="' . $youtube . '" target="_blank"><i class="fa-youtube"></i><i class="fa-youtube"></i></a></li>' . "\n";
                }
                if ( $vimeo ) {
                    $social_icons .= '<li class="vimeo"><a href="http://www.vimeo.com/' . $vimeo . '" target="_blank"><i class="fa-vimeo-square"></i><i class="fa-vimeo-square"></i></a></li>' . "\n";
                }
                if ( $tumblr ) {
                    $social_icons .= '<li class="tumblr"><a href="http://' . $tumblr . '.tumblr.com/" target="_blank"><i class="fa-tumblr"></i><i class="fa-tumblr"></i></a></li>' . "\n";
                }
                if ( $skype ) {
                    $social_icons .= '<li class="skype"><a href="skype:' . $skype . '" target="_blank"><i class="fa-skype"></i><i class="fa-skype"></i></a></li>' . "\n";
                }
                if ( $linkedin ) {
                    $social_icons .= '<li class="linkedin"><a href="' . $linkedin . '" target="_blank"><i class="fa-linkedin"></i><i class="fa-linkedin"></i></a></li>' . "\n";
                }
                if ( $googleplus ) {
                    $social_icons .= '<li class="googleplus"><a href="' . $googleplus . '" target="_blank"><i class="fa-google-plus"></i><i class="fa-google-plus"></i></a></li>' . "\n";
                }
                if ( $flickr ) {
                    $social_icons .= '<li class="flickr"><a href="' . $flickr . '" target="_blank"><i class="fa-flickr"></i><i class="fa-flickr"></i></a></li>' . "\n";
                }
                if ( $pinterest ) {
                    $social_icons .= '<li class="pinterest"><a href="http://www.pinterest.com/' . $pinterest . '/" target="_blank"><i class="fa-pinterest"></i><i class="fa-pinterest"></i></a></li>' . "\n";
                }
                if ( $foursquare ) {
                    $social_icons .= '<li class="foursquare"><a href="' . $foursquare . '" target="_blank"><i class="fa-foursquare"></i><i class="fa-foursquare"></i></a></li>' . "\n";
                }
                if ( $instagram ) {
                    $social_icons .= '<li class="instagram"><a href="http://instagram.com/' . $instagram . '" target="_blank"><i class="fa-instagram"></i><i class="fa-instagram"></i></a></li>' . "\n";
                }
                if ( $github ) {
                    $social_icons .= '<li class="github"><a href="' . $github . '" target="_blank"><i class="fa-github"></i><i class="fa-github"></i></a></li>' . "\n";
                }
                if ( $xing ) {
                    $social_icons .= '<li class="xing"><a href="' . $xing . '" target="_blank"><i class="fa-xing"></i><i class="fa-xing"></i></a></li>' . "\n";
                }
                if ( $behance ) {
                    $social_icons .= '<li class="behance"><a href="' . $behance . '" target="_blank"><i class="fa-behance"></i><i class="fa-behance"></i></a></li>' . "\n";
                }
                if ( $deviantart ) {
                    $social_icons .= '<li class="deviantart"><a href="' . $deviantart . '" target="_blank"><i class="fa-deviantart"></i><i class="fa-deviantart"></i></a></li>' . "\n";
                }
                if ( $soundcloud ) {
                    $social_icons .= '<li class="soundcloud"><a href="' . $soundcloud . '" target="_blank"><i class="fa-soundcloud"></i><i class="fa-soundcloud"></i></a></li>' . "\n";
                }
                if ( $yelp ) {
                    $social_icons .= '<li class="yelp"><a href="' . $yelp . '" target="_blank"><i class="fa-yelp"></i><i class="fa-yelp"></i></a></li>' . "\n";
                }
                if ( $rss ) {
                    $social_icons .= '<li class="rss"><a href="' . $rss . '" target="_blank"><i class="fa-rss"></i><i class="fa-rss"></i></a></li>' . "\n";
                }
                if ( $vk ) {
                    $social_icons .= '<li class="vk"><a href="' . $vk . '" target="_blank"><i class="fa-vk"></i><i class="fa-vk"></i></a></li>' . "\n";
                }
                if ( $twitch ) {
                    $social_icons .= '<li class="twitch"><a href="' . $twitch . '" target="_blank"><i class="fa-twitch"></i><i class="fa-twitch"></i></a></li>' . "\n";
                }
                if ( $snapchat ) {
                    $social_icons .= '<li class="snapchat"><a href="' . $snapchat . '" target="_blank"><i class="fa-snapchat"></i><i class="fa-snapchat"></i></a></li>' . "\n";
                }
                if ( $whatsapp ) {
                    $social_icons .= '<li class="whatsapp"><a href="' . $whatsapp . '" target="_blank"><i class="fa-whatsapp"></i><i class="fa-whatsapp"></i></a></li>' . "\n";
                }
            } else {

                $social_type = explode( ',', $type );
                foreach ( $social_type as $id ) {
                    if ( $id == "twitter" ) {
                        $social_icons .= '<li class="twitter"><a href="http://www.twitter.com/' . $twitter . '" target="_blank"><i class="fa-twitter"></i><i class="fa-twitter"></i></a></li>' . "\n";
                    }
                    if ( $id == "facebook" ) {
                        $social_icons .= '<li class="facebook"><a href="' . $facebook . '" target="_blank"><i class="fa-facebook"></i><i class="fa-facebook"></i></a></li>' . "\n";
                    }
                    if ( $id == "dribbble" ) {
                        $social_icons .= '<li class="dribbble"><a href="http://www.dribbble.com/' . $dribbble . '" target="_blank"><i class="fa-dribbble"></i><i class="fa-dribbble"></i></a></li>' . "\n";
                    }
                    if ( $id == "youtube" ) {
                        $social_icons .= '<li class="youtube"><a href="' . $youtube . '" target="_blank"><i class="fa-youtube"></i><i class="fa-youtube"></i></a></li>' . "\n";
                    }
                    if ( $id == "vimeo" ) {
                        $social_icons .= '<li class="vimeo"><a href="http://www.vimeo.com/' . $vimeo . '" target="_blank"><i class="fa-vimeo-square"></i><i class="fa-vimeo-square"></i></a></li>' . "\n";
                    }
                    if ( $id == "tumblr" ) {
                        $social_icons .= '<li class="tumblr"><a href="http://' . $tumblr . '.tumblr.com/" target="_blank"><i class="fa-tumblr"></i><i class="fa-tumblr"></i></a></li>' . "\n";
                    }
                    if ( $id == "skype" ) {
                        $social_icons .= '<li class="skype"><a href="skype:' . $skype . '" target="_blank"><i class="fa-skype"></i><i class="fa-skype"></i></a></li>' . "\n";
                    }
                    if ( $id == "linkedin" ) {
                        $social_icons .= '<li class="linkedin"><a href="' . $linkedin . '" target="_blank"><i class="fa-linkedin"></i><i class="fa-linkedin"></i></a></li>' . "\n";
                    }
                    if ( $id == "googleplus" || $id == "google-plus" || $id == "google+" ) {
                        $social_icons .= '<li class="googleplus"><a href="' . $googleplus . '" target="_blank"><i class="fa-google-plus"></i><i class="fa-google-plus"></i></a></li>' . "\n";
                    }
                    if ( $id == "flickr" ) {
                        $social_icons .= '<li class="flickr"><a href="' . $flickr . '" target="_blank"><i class="fa-flickr"></i><i class="fa-flickr"></i></a></li>' . "\n";
                    }
                    if ( $id == "pinterest" ) {
                        $social_icons .= '<li class="pinterest"><a href="http://www.pinterest.com/' . $pinterest . '/" target="_blank"><i class="fa-pinterest"></i><i class="fa-pinterest"></i></a></li>' . "\n";
                    }
                    if ( $id == "foursquare" ) {
                        $social_icons .= '<li class="foursquare"><a href="' . $foursquare . '" target="_blank"><i class="fa-foursquare"></i><i class="fa-foursquare"></i></a></li>' . "\n";
                    }
                    if ( $id == "instagram" ) {
                        $social_icons .= '<li class="instagram"><a href="http://instagram.com/' . $instagram . '" target="_blank"><i class="fa-instagram"></i><i class="fa-instagram"></i></a></li>' . "\n";
                    }
                    if ( $id == "github" ) {
                        $social_icons .= '<li class="github"><a href="' . $github . '" target="_blank"><i class="fa-github"></i><i class="fa-github"></i></a></li>' . "\n";
                    }
                    if ( $id == "xing" ) {
                        $social_icons .= '<li class="xing"><a href="' . $xing . '" target="_blank"><i class="fa-xing"></i><i class="fa-xing"></i></a></li>' . "\n";
                    }
                    if ( $id == "behance" ) {
                        $social_icons .= '<li class="behance"><a href="' . $behance . '" target="_blank"><i class="fa-behance"></i><i class="fa-behance"></i></a></li>' . "\n";
                    }
                    if ( $id == "deviantart" ) {
                        $social_icons .= '<li class="deviantart"><a href="' . $deviantart . '" target="_blank"><i class="fa-deviantart"></i><i class="fa-deviantart"></i></a></li>' . "\n";
                    }
                    if ( $id == "soundcloud" ) {
                        $social_icons .= '<li class="soundcloud"><a href="' . $soundcloud . '" target="_blank"><i class="fa-soundcloud"></i><i class="fa-soundcloud"></i></a></li>' . "\n";
                    }
                    if ( $id == "yelp" ) {
                        $social_icons .= '<li class="yelp"><a href="' . $yelp . '" target="_blank"><i class="fa-yelp"></i><i class="fa-yelp"></i></a></li>' . "\n";
                    }
                    if ( $id == "rss" ) {
                        $social_icons .= '<li class="rss"><a href="' . $rss . '" target="_blank"><i class="fa-rss"></i><i class="fa-rss"></i></a></li>' . "\n";
                    }
                    if ( $id == "vk" ) {
                        $social_icons .= '<li class="vk"><a href="' . $vk . '" target="_blank"><i class="fa-vk"></i><i class="fa-vk"></i></a></li>' . "\n";
                    }
                    if ( $id == "twitch" ) {
                        $social_icons .= '<li class="twitch"><a href="' . $twitch . '" target="_blank"><i class="fa-twitch"></i><i class="fa-twitch"></i></a></li>' . "\n";
                    }
                    if ( $id == "snapchat" ) {
                        $social_icons .= '<li class="snapchat"><a href="' . $snapchat . '" target="_blank"><i class="fa-snapchat"></i><i class="fa-snapchat"></i></a></li>' . "\n";
                    }
                    if ( $id == "whatsapp" ) {
                        $social_icons .= '<li class="whatsapp"><a href="' . $whatsapp . '" target="_blank"><i class="fa-whatsapp"></i><i class="fa-whatsapp"></i></a></li>' . "\n";
                    }
                }
            }

            $output = '<ul class="social-icons ' . $size . ' ' . $style . '">' . "\n";
            $output .= $social_icons;
            $output .= '</ul>' . "\n";

            return $output;
        }

        add_shortcode( "social", "sf_social_icons" );
        add_shortcode( "sf_social", "sf_social_icons" );
    }


    /* SITEMAP SHORTCODE
	================================================= */
    if ( !function_exists('sf_sitemap') ) {
        function sf_sitemap( $params = array() ) {
            // default parameters
            extract( shortcode_atts( array(
                'title' => 'Site map',
                'id'    => 'sitemap',
                'depth' => 2
            ), $params ) );
            // create sitemap

            $sitemap = '<div class="sitemap-wrap clearfix">';

            $sitemap .= '<div class="sitemap-col">';

            $sitemap .= '<h3>' . __( "Pages", 'swift-framework-plugin' ) . '</h3>';

            $page_list = wp_list_pages( "title_li=&depth=$depth&sort_column=menu_order&echo=0" );
            if ( $page_list != '' ) {
                $sitemap .= '<ul>' . $page_list . '</ul>';
            }

            $sitemap .= '</div>';

            $sitemap .= '<div class="sitemap-col">';

            $sitemap .= '<h3>' . __( "Posts", 'swift-framework-plugin' ) . '</h3>';

            $post_list = wp_get_archives( 'type=postbypost&limit=20&echo=0' );
            if ( $post_list != '' ) {
                $sitemap .= '<ul>' . $post_list . '</ul>';
            }

            $sitemap .= '</div>';

            $sitemap .= '<div class="sitemap-col">';

            $sitemap .= '<h3>' . __( "Categories", 'swift-framework-plugin' ) . '</h3>';

            $category_list = wp_list_categories( 'sort_column=name&title_li=&depth=1&number=10&echo=0' );
            if ( $category_list != '' ) {
                $sitemap .= '<ul>' . $category_list . '</ul>';
            }

            $sitemap .= '<h3>' . __( "Archives", 'swift-framework-plugin' ) . '</h3>';

            $archive_list = wp_get_archives( 'type=monthly&limit=12&echo=0' );
            if ( $archive_list != '' ) {
                $sitemap .= '<ul>' . $archive_list . '</ul>';
            }

            $sitemap .= '</div>';

            $sitemap .= '</div>';

            return $sitemap;

        }
        add_shortcode( 'sf_sitemap', 'sf_sitemap' );
    }

    /* SERVICES PROGRESS BAR SHORTCODE
	================================================= */
    if ( !function_exists('sf_progress_bar') ) {
        function sf_progress_bar( $atts ) {
            extract( shortcode_atts( array(
                "percentage" => '',
                "name"       => '',
                "type"       => '',
                "value"      => '',
                "colour"     => ''
            ), $atts ) );

            if ( $type == "" ) {
                $type = "standard";
            }

            if ( $name == "" ) {
                $name = " ";
            }

            $service_bar_output = '';

            $service_bar_output .= '<div class="progress-bar-wrap progress-' . $type . '">' . "\n";
            if ( $colour != "" ) {
                $service_bar_output .= '<div class="bar-text"><span class="bar-name">' . $name . '</span> <span class="progress-value" style="color:' . $colour . '!important;">' . $value . '</span></div>' . "\n";
                $service_bar_output .= '<div class="progress ' . $type . '">' . "\n";
                $service_bar_output .= '<div class="bar" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100" data-value="' . $percentage . '" style="background-color:' . $colour . '!important;">' . "\n";
            } else {
                $service_bar_output .= '<div class="bar-text"><span class="bar-name">' . $name . '</span> <span class="progress-value">' . $value . '</span></div>' . "\n";
                $service_bar_output .= '<div class="progress ' . $type . '">' . "\n";
                $service_bar_output .= '<div class="bar" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100" data-value="' . $percentage . '">' . "\n";
            }
            $service_bar_output .= '</div>' . "\n";
            $service_bar_output .= '</div>' . "\n";
            $service_bar_output .= '</div>' . "\n";

            global $sf_has_progress_bar;
            $sf_has_progress_bar = true;

            return $service_bar_output;
        }
        add_shortcode( 'progress_bar', 'sf_progress_bar' );
        add_shortcode( 'sf_progress_bar', 'sf_progress_bar' );
    }

    /* CHART SHORTCODE
	================================================= */
    if ( !function_exists('sf_chart') ) {
        function sf_chart( $atts ) {
            extract( shortcode_atts( array(
                "percentage"  => '50',
                "size"        => '70',
                "barcolour"   => '',
                "trackcolour" => '',
                "content"     => '',
                "linewidth"   => '3',
                "align"       => ''
            ), $atts ) );

            // Enqueue script
            wp_enqueue_script( 'easypiechart' );

            $chart_output = '';

            if ( $barcolour == "" ) {
                $barcolour = get_option( 'accent_color', '#fb3c2d' );
            }
            if ( $trackcolour == "" ) {
                $trackcolour = '#f2f2f2';
            }

            // LINE WIDTH
            $linewidth = apply_filters( 'sf_chart_shortcode_width', $linewidth );
            if ( sf_current_theme() == "uplift" ) {
                $linewidth = apply_filters( 'sf_chart_shortcode_width', '2' );
            }

            // SHORTCODE OUTPUT
            $chart_output .= '<div class="chart-shortcode chart-' . $size . ' chart-' . $align . '" data-linewidth="' . $linewidth . '" data-percent="0" data-animatepercent="' . $percentage . '" data-size="' . $size . '" data-barcolor="' . $barcolour . '" data-trackcolor="' . $trackcolour . '">';
            if ( $content != "" ) {
                if ( strpos( $content, 'fa-' ) !== false || strpos( $content, 'ss-' ) !== false || strpos( $content, 'sf-im-' ) !== false || strpos( $content, 'sf-icon-' ) !== false ) {
                    $chart_output .= '<span><i class="' . $content . '"></i></span>';
                } else {
                    $chart_output .= '<span>' . $content . '</span>';
                }
            }
            $chart_output .= '</div>';

            // UPDATE GLOBAL
            global $sf_has_chart;
            $sf_has_chart = true;

            // RETURN
            return $chart_output;
        }
        add_shortcode( 'chart', 'sf_chart' );
        add_shortcode( 'sf_chart', 'sf_chart' );
    }


    /* TOOLTIP SHORTCODE
	================================================= */
    if ( !function_exists('sf_tooltip') ) {
        function sf_tooltip( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "title"     => '',
                "link"      => '#',
                "direction" => 'top'
            ), $atts ) );

            $tooltip_output = '<a href="' . $link . '" rel="tooltip" data-toggle="tooltip" data-original-title="' . $title . '" data-placement="' . $direction . '">' . do_shortcode( $content ) . '</a>';

            return $tooltip_output;
        }
        add_shortcode( 'sf_tooltip', 'sf_tooltip' );
    }


    /* MODAL SHORTCODE
	================================================= */
    if ( !function_exists('sf_modal') ) {
        function sf_modal( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "header"     => '',
                "link_type"  => '',
                "link_text"  => '',
                "btn_type"   => '',
                "btn_colour" => '',
                "btn_size"   => '',
                "btn_icon"   => '',
                "btn_text"   => ''
            ), $atts ) );

            global $sf_modalCount;

            if ( $sf_modalCount >= 0 ) {
                $sf_modalCount ++;
            } else {
                $sf_modalCount = 0;
            }

            $modal_output = "";

            $modal_delete_icon = apply_filters( 'sf_close_icon', '<i class="ss-delete"></i>' );

            if ( $link_type == "text" ) {
                $modal_output .= '<a class="modal-text-link" href="#modal-' . $sf_modalCount . '" role="button" data-toggle="modal">'.$link_text.'</a>';
            } else {
                $button_class = 'sf-button ' . $btn_size . ' ' . $btn_colour . ' ' . $btn_type;

                if ( $btn_type == "sf-icon-reveal" || $btn_type == "sf-icon-stroke" ) {
                    $modal_output .= '<a class="' . $button_class . '" href="#modal-' . $sf_modalCount . '" role="button" data-toggle="modal">';
                    $modal_output .= '<i class="' . $btn_icon . '"></i>';
                    $modal_output .= '<span class="text">' . $btn_text . '</span>';
                    $modal_output .= '</a>';
                } else {
                    $modal_output .= '<a class="' . $button_class . '" href="#modal-' . $sf_modalCount . '" role="button" data-toggle="modal"><span class="text">' . $btn_text . '</span></a>';
                }
            }

            $modal_output .= '<div id="modal-' . $sf_modalCount . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="' . $header . '" aria-hidden="true">';
            $modal_output .= '<div class="modal-dialog">';
            $modal_output .= '<div class="modal-content">';
            $modal_output .= '<div class="modal-header">';
            $modal_output .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">'.$modal_delete_icon.'</button>';
            $modal_output .= '<h3 id="modal-label">' . $header . '</h3>';
            $modal_output .= '</div>';
            $modal_output .= '<div class="modal-body">' . do_shortcode( $content ) . '</div>';
            $modal_output .= '</div>';
            $modal_output .= '</div>';
            $modal_output .= '</div>';

            return $modal_output;
        }
        add_shortcode( 'sf_modal', 'sf_modal' );
    }


    /* FULLSCREEN VIDEO SHORTCODE
	================================================= */
    if ( !function_exists('sf_fullscreen_video') ) {
        function sf_fullscreen_video( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "type"       => '',
                "imageurl"   => '',
                "imageheight" => '',
                "imagewidth" => '',
                "btntext"    => '',
                "videourl"   => '',
                "extraclass" => ''
            ), $atts ) );

            $fw_video_output = "";

            $video_embed_url = sf_get_embed_src( $videourl );

            if ( $type == "image-button" || $type == "image-button2" || $type == "image-button3" ) {

                $fw_video_output .= '<a href="#" class="fw-video-link fw-video-' . $type . ' fw-video-link-image ' . $extraclass . '" data-video="' . $video_embed_url . '">';

                if ( $type == "image-button3" ) {
                    $fw_video_output .= apply_filters( 'sf_fs_video_icon_alt3', '<i class="ss-video"></i>' );
                } else if ( $type == "image-button2" ) {
                    $fw_video_output .= apply_filters( 'sf_fs_video_icon_alt', '<i class="ss-play"></i>' );
                } else {
                    $fw_video_output .= apply_filters( 'sf_fs_video_icon', '<i class="ss-play"></i>' );
                }  

                $image_meta = 'alt="' . $btntext . '"';
                if ( $imageheight != "" ) {
                    $image_meta .= ' height="' . $imageheight . '"';
                }
                if ( $imagewidth != "" ) {
                    $image_meta .= ' width="' . $imagewidth . '"';
                }

                $fw_video_output .= '<img src="' . $imageurl . '" ' . $image_meta . ' />';

                $fw_video_output .= '</a>';

            } else if ( $type == "text-button" ) {

                $fw_video_output .= '<a href="#" class="fw-video-link fw-video-link-text sf-button sf-icon-stroke accent ' . $extraclass . '" data-video="' . $video_embed_url . '">';

                $fw_video_output .= apply_filters('sf_fs_video_icon', '<i class="ss-play"></i>');
                $fw_video_output .= '<span class="text">' . $btntext . '</span>';

                $fw_video_output .= '</a>';

            } else {

                $fw_video_output .= '<a href="#" class="fw-video-link fw-video-link-icon ' . $extraclass . '" data-video="' . $video_embed_url . '">';

                $fw_video_output .= apply_filters('sf_fs_video_icon', '<i class="ss-play"></i>');

                $fw_video_output .= '</a>';
            }

            return $fw_video_output;
        }
        add_shortcode( 'sf_fullscreenvideo', 'sf_fullscreen_video' );
    }


    /* RESPONSIVE VISIBILITY SHORTCODE
	================================================= */
    if ( !function_exists('sf_visibility') ) {
        function sf_visibility( $atts, $content = null ) {
            extract( shortcode_atts( array(
                "class" => ''
            ), $atts ) );

            $visibility_output = '<div class="' . $class . '">' . do_shortcode( $content ) . '</div>';

            return $visibility_output;
        }
        add_shortcode( 'sf_visibility', 'sf_visibility' );
    }


    /* YEAR SHORTCODE
	================================================= */
    if ( !function_exists('sf_year_shortcode') ) {
        function sf_year_shortcode() {
            $year = date( 'Y' );

            return $year;
        }
        add_shortcode( 'the-year', 'sf_year_shortcode' );
    }

    /* DAY SHORTCODE
	================================================= */
    if ( !function_exists('sf_day_shortcode') ) {
        function sf_day_shortcode() {
            $year = date( 'l' );

            return $year;
        }
        add_shortcode( 'sf-today', 'sf_day_shortcode' );
    }


    /* DATE SHORTCODE
	================================================= */
    if ( !function_exists('sf_date_shortcode') ) {
        function sf_date_shortcode( $atts ) {
            extract( shortcode_atts( array(
                "format" => 'F jS Y',
            ), $atts ) );

            $date = date( $format );

            return $date;
        }
        add_shortcode( 'sf-date', 'sf_date_shortcode' );
    }

    /* WORDPRESS LINK SHORTCODE
	================================================= */
    if ( !function_exists('sf_wordpress_link') ) {
        function sf_wordpress_link() {
            return '<a href="http://wordpress.org/" target="_blank">WordPress</a>';
        }
        add_shortcode( 'wp-link', 'sf_wordpress_link' );
    }

    /* COUNT SHORTCODE
	================================================= */
    if ( !function_exists('sf_count') ) {
        function sf_count( $atts ) {
            extract( shortcode_atts( array(
                "speed"     => '2000',
                "refresh"   => '25',
                "from"      => '0',
                "to"        => '',
                "prefix"    => '',
                "suffix"    => '',
                "commas"    => 'false',
                "icon"      => '',
                "subject"   => '',
                "textstyle" => '',
                "color"     => ''
            ), $atts ) );

            // Enqueue script
            wp_enqueue_script( 'countto' );

            $count_output = '';

            if ( $speed == "" ) {
                $speed = '2000';
            }
            if ( $refresh == "" ) {
                $refresh = '25';
            }

            $count_output .= '<div class="sf-count-asset" style="color: ' . $color . ';">';
            $count_output .= '<div class="count-number" data-from="' . $from . '" data-to="' . $to . '" data-speed="' . $speed . '" data-refresh-interval="' . $refresh . '" data-prefix="' . $prefix . '" data-suffix="' . $suffix . '" data-with-commas="' . $commas . '"></div>';
            if ( $icon != "" ) {
                $count_output .= '<div class="count-divider has-icon"><span class="icon-divide" style="background-color: ' . $color . ';"></span><i class="' . $icon . '"></i><span class="icon-divide" style="background-color: ' . $color . ';"></span></div>';
            } else {
                $count_output .= '<div class="count-divider"><span></span></div>';
            }
            if ( $textstyle == "h3" ) {
                $count_output .= '<h3 class="count-subject">' . $subject . '</h3>';
            } else if ( $textstyle == "h6" ) {
                $count_output .= '<h6 class="count-subject">' . $subject . '</h6>';
            } else {
                $count_output .= '<div class="count-subject">' . $subject . '</div>';
            }
            $count_output .= '</div>';

            return $count_output;
        }
        add_shortcode( 'sf_count', 'sf_count' );
    }


    /* COUNTDOWN SHORTCODE
	================================================= */
    if ( !function_exists('sf_countdown') ) {
        function sf_countdown( $atts ) {
            extract( shortcode_atts( array(
                "year"        => '',
                "month"       => '',
                "day"         => '',
                "fontsize"    => 'large',
                "displaytext" => ''
            ), $atts ) );

            // Enqueue script
            wp_enqueue_script( 'countdown' );

            $countdown_output = '';

            $countdown_output .= '<div class="sf-countdown text-' . $fontsize . '" data-year="' . $year . '" data-month="' . $month . '" data-day="' . $day . '"></div>';
            if ( $displaytext != "" ) {
                $countdown_output .= '<h3 class="countdown-subject">' . $displaytext . '</h3>';
            }

            global $sf_has_countdown;
            $sf_has_countdown = true;

            return $countdown_output;
        }
        add_shortcode( 'sf_countdown', 'sf_countdown' );
    }


    /* SOCIAL SHARE SHORTCODE
	================================================= */
	if ( !function_exists( 'sf_social_share' ) ) {
	    function sf_social_share( $atts = null ) {

	        extract( shortcode_atts( array(
	            "center" => '',
                "share_url" => '',
	        ), $atts ) );

            if ( sf_current_theme() == "atelier" ) {
                global $post;
                $image = wp_get_attachment_url( get_post_thumbnail_id() );
                $page_permalink = urlencode(get_the_permalink());
                $page_title = get_the_title();
                $page_thumb_id = get_post_thumbnail_id();
                $page_thumb_url = wp_get_attachment_url( $page_thumb_id );
                $share_output = "";

                if ( $share_url != "" ) {
                    $page_permalink = $share_url;
                }

                if ( $center == "yes" ) {
                    $share_output .= '<div class="sf-share-counts center-share-counts">';
                } else {
                    $share_output .= '<div class="sf-share-counts">';         
                    $share_output .= '<h3 class="share-text">'.__("Share", 'swift-framework-plugin').'</h3>';
                    $share_output .= '<a href="https://www.facebook.com/sharer/sharer.php?u='.$page_permalink.'" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=440,width=660\');return false;" class="sf-share-link sf-share-fb"><i class="fa-facebook"></i><span class="count">0</span></a>';
                    $share_output .= '<a href="http://twitter.com/share?text='.$page_title.'&url='.$page_permalink.'" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=440,width=660\');return false;" class="sf-share-link sf-share-twit"><i class="fa-twitter"></i><span class="count">0</span></a>';
                    $share_output .= '<a href="http://pinterest.com/pin/create/button/?url='.$page_permalink.'&media='.$page_thumb_url.'&description='.$page_title.'" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=690,width=750\');return false;" class="sf-share-link sf-share-pin"><i class="fa-pinterest"></i><span class="count">0</span></a>';
                    $share_output .= '</div>';
                }
                return $share_output;
            } else {
    	        global $post;
    	        $image = wp_get_attachment_url( get_post_thumbnail_id() );

    	        if ( $center == "yes" ) {
    	            $share_output = '<div class="article-share share-center" data-buttontext="' . __( "Share this", 'swift-framework-plugin' ) . '" data-image="' . $image . '"><share-button class="share-button"></share-button></div>';
    	        } else {
    	            $share_output = '<div class="article-share" data-buttontext="' . __( "Share this", 'swift-framework-plugin' ) . '" data-image="' . $image . '"><share-button class="share-button"></share-button></div>';
    	        }

    	        return apply_filters( 'sf_social_share_output', $share_output, $atts);
            }
	    }

	    add_shortcode( 'sf_social_share', 'sf_social_share' );
	}

    /* SWIFT SUPER SEARCH SHORTCODE
	================================================= */
    if ( !function_exists('sf_supersearch') ) {
        function sf_supersearch( $contained = "" ) {
            if ( function_exists( 'sf_super_search' ) ) {
                return sf_super_search($contained);
            } else {
                return "";
            }
        }
        add_shortcode( 'sf_supersearch', 'sf_supersearch' );
    }


    /* SWIFT GALLERY SHORTCODE
	================================================= */
    $theme_opts_name = "";
    if ( get_option('sf_theme') != "" ) {
        $theme_opts_name = 'sf_' . get_option('sf_theme') . '_options';
    }
    $sf_options        = get_option( $theme_opts_name );
    $disable_sfgallery = $sf_options['disable_sfgallery'];
    if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'tiled-gallery' ) ) :
        $disable_sfgallery = true;
    endif;

    if ( ! $disable_sfgallery ) {
        // Remove built in shortcode
        remove_shortcode( 'gallery', 'gallery_shortcode' );

        // Replace with custom shortcode
        if ( !function_exists('sf_gallery') ) {
            function sf_gallery( $attr ) {
                $post = get_post();

                static $instance = 0;
                $instance ++;

                if ( ! empty( $attr['ids'] ) ) {
                    // 'ids' is explicitly ordered, unless you specify otherwise.
                    if ( empty( $attr['orderby'] ) ) {
                        $attr['orderby'] = 'post__in';
                        $attr['include'] = $attr['ids'];
                    }
                }

                // Allow plugins/themes to override the default gallery template.
                $output = apply_filters( 'post_gallery', '', $attr );
                if ( $output != '' ) {
                    return $output;
                }

                // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
                if ( isset( $attr['orderby'] ) ) {
                    $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
                    if ( ! $attr['orderby'] ) {
                        unset( $attr['orderby'] );
                    }
                }

                extract( shortcode_atts( array(
                    'order'      => 'ASC',
                    'orderby'    => 'menu_order ID',
                    'id'         => $post ? $post->ID : 0,
                    'itemtag'    => 'dl',
                    'icontag'    => 'dt',
                    'captiontag' => 'dd',
                    'columns'    => 3,
                    'size'       => 'large',
                    'include'    => '',
                    'exclude'    => ''
                ), $attr, 'gallery' ) );

                $id = intval( $id );
                if ( 'RAND' == $order ) {
                    $orderby = 'none';
                }

                if ( ! empty( $include ) ) {
                    $_attachments = get_posts( array( 'include'        => $include,
                                                      'post_status'    => 'inherit',
                                                      'post_type'      => 'attachment',
                                                      'post_mime_type' => 'image',
                                                      'order'          => $order,
                                                      'orderby'        => $orderby
                        ) );

                    $attachments = array();
                    foreach ( $_attachments as $key => $val ) {
                        $attachments[ $val->ID ] = $_attachments[ $key ];
                    }
                } elseif ( ! empty( $exclude ) ) {
                    $attachments = get_children( array( 'post_parent'    => $id,
                                                        'exclude'        => $exclude,
                                                        'post_status'    => 'inherit',
                                                        'post_type'      => 'attachment',
                                                        'post_mime_type' => 'image',
                                                        'order'          => $order,
                                                        'orderby'        => $orderby
                        ) );
                } else {
                    $attachments = get_children( array( 'post_parent'    => $id,
                                                        'post_status'    => 'inherit',
                                                        'post_type'      => 'attachment',
                                                        'post_mime_type' => 'image',
                                                        'order'          => $order,
                                                        'orderby'        => $orderby
                        ) );
                }

                if ( empty( $attachments ) ) {
                    return '';
                }

                if ( is_feed() ) {
                    $output = "\n";
                    foreach ( $attachments as $att_id => $attachment ) {
                        $output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
                    }

                    return $output;
                }

                $itemtag    = tag_escape( $itemtag );
                $captiontag = tag_escape( $captiontag );
                $icontag    = tag_escape( $icontag );
                $valid_tags = wp_kses_allowed_html( 'post' );
                if ( ! isset( $valid_tags[ $itemtag ] ) ) {
                    $itemtag = 'dl';
                }
                if ( ! isset( $valid_tags[ $captiontag ] ) ) {
                    $captiontag = 'dd';
                }
                if ( ! isset( $valid_tags[ $icontag ] ) ) {
                    $icontag = 'dt';
                }

                $columns   = intval( $columns );
                $itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
                $float     = is_rtl() ? 'right' : 'left';

                $selector = "gallery-{$instance}";

                $gallery_style = $list_class = '';
                $size_class    = sanitize_html_class( $size );
    			$hover_style = "default";

                // Thumb Type
                if ( $hover_style == "default" && function_exists( 'sf_get_thumb_type' ) ) {
                    $list_class = sf_get_thumb_type();
                } else {
                    $list_class = 'thumbnail-' . $hover_style;
                }

                $output = "<div id='$selector' class='gallery-shortcode galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} {$list_class}'>";

                $i = 0;
                foreach ( $attachments as $id => $attachment ) {

                    $image_output = '<figure class="animated-overlay overlay-style">';

                    $image_file_url          = wp_get_attachment_image_src( $id, $size );
                    $image_file_lightbox_url = wp_get_attachment_url( $id, "full" );
                    $image_caption           = wptexturize( $attachment->post_excerpt );
                    $image_meta              = wp_get_attachment_metadata( $id );
                    $image_alt               = spb_get_post_meta( $id, '_wp_attachment_image_alt', true );
    				$image_title = get_the_title($id);

    				$image_output .= '<img src="'.$image_file_url[0].'" alt="'.$image_alt.'" />';

    				if ( ! empty( $attr['link'] ) && 'file' === $attr['link'] ) {
                        $lightbox_id = 'galleryid-' . $instance;
                        $atts = array(
                            'image_id'    => $id,
                            'lightbox_id' => $lightbox_id,
                            'title'       => $image_alt,
                            'caption'     => $image_caption,
                            'extra_class' => '',
                            'is_slider'   => false
                        );
                        $link_config = spb_lightbox_link_config( $atts );
                        $image_output .= '<a ' . $link_config . '></a>';
    				} elseif ( ! empty( $attr['link'] ) && 'none' === $attr['link'] ) {
    				} else {
    					$image_output .= '<a href="'.get_attachment_link( $id ).'"></a>';
    				}

    				if ($image_title != "") {
    					$image_output .= '<figcaption><div class="thumb-info">';
    					$image_output .= '<h4 itemprop="name headline">'.$image_title.'</h4>';
    				} else {
    					$image_output .= '<figcaption><div class="thumb-info thumb-info-alt">';
    					$image_output .= apply_filters('sf_search_icon', '<i class="ss-search"></i>');
    				}

    				$image_output .= '</div></figcaption>';
    				$image_output .= '</figure>';
    				if ($captiontag && trim($attachment->post_excerpt)) {
    					$image_output .= '<div class="gallery-item-caption">'.wptexturize($attachment->post_excerpt).'</div>';
    				}

                    $orientation = '';
                    if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
                        $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
                    }

                    $output .= "<{$itemtag} class='gallery-item'>";
                    $output .= "
    				<{$icontag} class='{$orientation}'>
    				$image_output
    				</{$icontag}>";
                    $output .= "</{$itemtag}>";
                }

                $output .= "
    			<br style='clear: both;' />
    			</div>\n";

                return $output;
            }
            add_shortcode( 'gallery', 'sf_gallery' );
        }
    }

    if ( !function_exists('swiftframework_oversize_media') ) :
        function swiftframework_oversize_media( $atts, $content = null ) {
            extract( shortcode_atts( array(
                'type' => 'image',
                'id'   => '',
                'caption_pos' => 'bottom-right'
            ), $atts ) );

            $output = "";

            if ( $type == "image" && $id != "" ) {
                $output .= '<figure class="oversize" data-caption-pos="' . $caption_pos . '">';
                $output .= wp_get_attachment_image( $id, 'full', false, array('itemprop' => 'image') );
                $output .= '<figcaption class="caption">' . do_shortcode( $content ) . '</figcaption>';
                $output .= '</figure>';
                return $output;
            } else {
                return false;
            }
        }
        add_shortcode( 'swiftframework_oversize_media', 'swiftframework_oversize_media' );
    endif;