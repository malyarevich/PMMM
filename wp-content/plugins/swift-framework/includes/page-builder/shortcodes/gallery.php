<?php

    /*
    *
    *	Swift Page Builder - Gallery Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_gallery extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $gallery_id = $output = $items = $hover_style = $masonry_gallery = $main_slider = $thumb_slider = $el_position = $gallery_images = $thumb_images = '';

            extract( shortcode_atts( array(
                'title'             => '',
                'gallery_id'        => '',
                'display_type'      => '',
                'columns'           => '',
                'fullwidth'         => '',
                'gutters'           => '',
                'image_size'        => '',
                'show_thumbs'       => '',
                'show_captions'     => '',
                'autoplay'          => 'no',
                'hover_style'       => 'default',
                'enable_lightbox'   => 'yes',
                'slider_transition' => 'slide',
                'el_position'       => '',
                'width'             => '1/1',
                'el_class'          => ''
            ), $atts ) );

            $search_icon = apply_filters( 'sf_search_icon' , '<i class="ss-search"></i>' );
            $view_icon_svg = apply_filters( 'sf_view_icon_svg' , '' );

            
            // Enqueue script
            if ( $display_type == "masonry" ) {
                wp_enqueue_script( 'isotope' );
            } else if ( $display_type == "slider" ) {
                wp_enqueue_script( 'lightSlider' );
            }


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


            /* GALLERY
            ================================================== */

            $gallery_args = array(
                'post_type'   => 'galleries',
                'post_status' => 'publish',
                'p'           => $gallery_id
            );

            $gallery_query = new WP_Query( $gallery_args );

            while ( $gallery_query->have_posts() ) : $gallery_query->the_post();


                if ( $display_type == "masonry" ) {

                    /* WRAP VARIABLE CONFIG
                    ================================================== */
                    $list_class = "";

                    if ( $fullwidth == "yes" ) {
                        $list_class .= ' portfolio-full-width';
                    }
                    if ( $gutters == "no" ) {
                        $list_class .= ' no-gutters';
                    } else {
                        $list_class .= ' gutters';
                    }

                    // Thumb Type
                    if ( $hover_style == "default" && function_exists( 'sf_get_thumb_type' ) ) {
                        $list_class .= ' ' . sf_get_thumb_type();
                    } else {
                        $list_class .= ' thumbnail-' . $hover_style;
                    }

                    /* COLUMN VARIABLE CONFIG
                    ================================================== */
                    $item_class = "";

                    $gallery_image_size = "gallery-image";

                    if ( $columns == "1" ) {
                        $item_class = "col-sm-12 ";
                        $gallery_image_size = "full";
                    } else if ( $columns == "2" ) {
                        $item_class = "col-sm-6 ";
                        $gallery_image_size = "gallery-image";
                    } else if ( $columns == "3" ) {
                        $item_class = "col-sm-4 ";
                        $gallery_image_size = "gallery-image";
                    } else if ( $columns == "4" ) {
                        $item_class = "col-sm-3 ";
                        $gallery_image_size = "thumb-image";
                    } else if ( $columns == "5" ) {
                        $item_class = "col-sm-sf-5 ";
                        $gallery_image_size = "thumb-image";
                    }

                    if ( $columns == "5" && $width != "1/1" ) {
                        $gallery_image_size = "thumb-square";
                    }

                    if ( $image_size != "" ) {
                        $gallery_image_size = $image_size;
                    }

                    if ( $enable_lightbox == "yes" ) {
                        $list_class .= ' ' . spb_lightbox_wrap_class();
                    }

                    $gallery_images = rwmb_meta( 'sf_gallery_images', 'type=image&size=' . $gallery_image_size );

                    $masonry_gallery .= '<div class="masonry-gallery filterable-items ' . $list_class . '">' . "\n";

                    $lightbox_id = $gallery_id . '-' . rand();

                    foreach ( $gallery_images as $image ) {

                        $masonry_gallery .= '<div class="gallery-image ' . $item_class . '">';
                        $masonry_gallery .= '<figure class="animated-overlay overlay-style">';

                        if ( $enable_lightbox == "yes" ) {
                            $atts = array(
                                'image_id'    => $image['ID'],
                                'lightbox_id' => $lightbox_id,
                                'caption'     => $image['caption'],
                                'extra_class' => '',
                                'is_slider'   => false
                            );
                            $link_config = spb_lightbox_link_config( $atts );
                            $masonry_gallery .= '<a ' . $link_config . '></a>';
                        }

                        $masonry_gallery .= '<img src="' . $image['url'] . '" width="' . $image['width'] . '" height="' . $image['height'] . '" alt="' . $image['alt'] . '" />';

                        if ( $show_captions == "yes" ) {
                            $masonry_gallery .= '<figcaption><div class="thumb-info">';
                            if ( $image['caption'] != "" ) {
                                $masonry_gallery .= '<h3>' . $image['caption'] . '</h3>';
                            } else {
                                $masonry_gallery .= '<h3>' . $image['title'] . '</h3>';
                            }
                            $masonry_gallery .= '</figcaption>' . "\n";
                        } else if ($enable_lightbox == "yes") {
                            $masonry_gallery .= '<figcaption><div class="thumb-info thumb-info-alt">';
                            if ( $view_icon_svg != "" ) {
                                $masonry_gallery .= $view_icon_svg;
                            } else {
                                $masonry_gallery .= $search_icon;
                            }
                            $masonry_gallery .= '</figcaption>' . "\n";
                        }
                        
                        $masonry_gallery .= '</figure>' . "\n";
                        $masonry_gallery .= '</div>' . "\n";
                    }

                    $masonry_gallery .= '</div>' . "\n";

                    $items .= $masonry_gallery;

                } else {

	                if ( spb_get_theme_name() == "atelier" || spb_get_theme_name() == "uplift" || spb_get_theme_name() == "nota" ) {

						$gallery_images = rwmb_meta( 'sf_gallery_images', 'type=image&size=thumb-square' );

                        $slider_class = 'flexslider gallery-slider';
                        if ( $enable_lightbox == "yes" ) {
                            $slider_class .= ' ' . spb_lightbox_wrap_class();
                        }

						$main_slider .= '<div class="' . $slider_class . '" data-transition="' . $slider_transition . '" data-autoplay="' . $autoplay . '" data-thumbs="'.$show_thumbs.'"><ul class="slides">' . "\n";

	                    $lightbox_id = $gallery_id . '-' . rand();

	                    foreach ( $gallery_images as $image ) {

							if ( $show_thumbs == "yes" ) {
								$main_slider .= '<li data-thumb="' . $image["url"] . '">';
							} else {
								$main_slider .= '<li>';
							}

                            if ( $enable_lightbox == "yes" ) {
                                $atts = array(
                                    'image_id'    => $image['ID'],
                                    'lightbox_id' => $lightbox_id,
                                    'caption'     => $image['caption'],
                                    'extra_class' => 'zoom',
                                    'is_slider'   => true
                                );
                                $link_config = spb_lightbox_link_config( $atts );
                                $main_slider .= '<a ' . $link_config . '><i class="fa-search-plus"></i></a>';
                            }

                            if ( isset($image['image_srcset']) ) {
                                $main_slider .= $image['image_srcset'];
                            } else {
                                $main_slider .= "<img src='{$image['full_url']}' alt='{$image['alt']}' />";
                            }

	                        if ( $show_captions == "yes" && $image['caption'] != "" ) {
	                            $main_slider .= '<p class="flex-caption">' . $image['caption'] . '</p>';
	                        }
	                        $main_slider .= "</li>" . "\n";
	                    }

	                    $main_slider .= '</ul></div>' . "\n";

	                    $items .= $main_slider;

	                } else {
	                    $gallery_images = rwmb_meta( 'sf_gallery_images', 'type=image&size=blog-image' );
	                    $thumb_images   = rwmb_meta( 'sf_gallery_images', 'type=image&size=thumb-square' );

	                    $main_slider .= '<div class="flexslider gallery-slider" data-transition="' . $slider_transition . '" data-autoplay="' . $autoplay . '"><ul class="slides">' . "\n";

	                    $lightbox_id = $gallery_id . '-' . rand();

	                    foreach ( $gallery_images as $image ) {

                            $main_slider .= "<li>";

                            if ( $enable_lightbox == "yes" ) {
                                $atts = array(
                                    'image_id'    => $image['ID'],
                                    'lightbox_id' => $lightbox_id,
                                    'caption'     => $image['caption'],
                                    'extra_class' => 'zoom',
                                    'is_slider'   => true
                                );
                                $link_config = spb_lightbox_link_config( $atts );
                                $main_slider .= '<a ' . $link_config . '>';
                            }

                            if ( isset($image['image_srcset']) ) {
                                $main_slider .= $image['image_srcset'];
                            } else {
                                $main_slider .= "<img src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' alt='{$image['alt']}' />";
                            }

                            if ( $enable_lightbox == "yes" ) {
                                $main_slider .= "</a>";
                            }

	                        if ( $show_captions == "yes" && $image['caption'] != "" ) {
	                            $main_slider .= '<p class="flex-caption">' . $image['caption'] . '</p>';
	                        }
	                        $main_slider .= "</li>" . "\n";
	                    }

	                    $main_slider .= '</ul></div>' . "\n";

	                    if ( $show_thumbs == "yes" ) {

	                        $thumb_slider .= '<div class="flexslider gallery-nav"><ul class="slides">' . "\n";

	                        foreach ( $thumb_images as $image ) {
	                            $thumb_slider .= "<li><img src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' alt='{$image['alt']}' /></li>" . "\n";
	                        }

	                        $thumb_slider .= '</ul></div>' . "\n";

	                    }

	                    $items .= $main_slider;
	                    $items .= $thumb_slider;
	                }
                }

            endwhile;

            wp_reset_postdata();


            /* PAGE BUILDER OUTPUT
            ================================================== */
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }
            $width    = spb_translateColumnWidthToSpan( $width );
            $el_class = $this->getExtraClass( $el_class, $fullwidth );

            $output .= "\n\t" . '<div class="spb_gallery_widget gallery-' . $display_type . ' spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '', $fullwidth ) : '';
            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );

            global $sf_has_gallery;
            $sf_has_gallery = true;

            return $output;

        }
    }

    /* PARAMS
    ================================================== */
    $list_galleries = array();

    if ( is_admin() ) {
        $list_galleries = array(
            "type"        => "dropdown",
            "heading"     => __( "Gallery", 'swift-framework-plugin' ),
            "param_name"  => "gallery_id",
            "value"       => sf_list_galleries(),
            "description" => __( "Choose the gallery which you'd like to display. You can add galleries in the left admin area.", 'swift-framework-plugin' )
        );
    } else {
        $list_galleries = array(
            "type"        => "dropdown",
            "heading"     => __( "Gallery", 'swift-framework-plugin' ),
            "param_name"  => "gallery_id",
            "value"       => "",
            "description" => __( "Choose the gallery which you'd like to display. You can add galleries in the left admin area.", 'swift-framework-plugin' )
        );
    }

    $params = array(
        array(
            "type"        => "textfield",
            "heading"     => __( "Widget title", 'swift-framework-plugin' ),
            "param_name"  => "title",
            "value"       => "",
            "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
        ),
        $list_galleries,
        array(
            "type"        => "dropdown",
            "heading"     => __( "Display Type", 'swift-framework-plugin' ),
            "param_name"  => "display_type",
            "value"       => array(
                __( "Slider", 'swift-framework-plugin' )  => "slider",
                __( "Masonry", 'swift-framework-plugin' ) => "masonry",
            ),
            "description" => __( "Choose the display type for the gallery asset.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Columns (Masonry only)", 'swift-framework-plugin' ),
            "param_name"  => "columns",
            "value"       => array( "5", "4", "3", "2", "1" ),
            "required"       => array("display_type", "=", "masonry"),
            "description" => __( "How many columns to display.'", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Full Width", 'swift-framework-plugin' ),
            "param_name"  => "fullwidth",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "std" => "no",
            "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Gutters", 'swift-framework-plugin' ),
            "param_name"  => "gutters",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("display_type", "=", "masonry"),
            "description" => __( "Select if you'd like spacing between the gallery items, or not.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown-id",
            "heading"     => __( "Image Size Override", 'swift-framework-plugin' ),
            "param_name"  => "image_size",
            "value"       => spb_get_image_sizes(),
            "std"         => 'full',
            "required"       => array("display_type", "=", "masonry"),
            "description" => __( "Override the source size for the images (NOTE: this doesn't affect it's size on the front-end, only the quality).", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Slider transition", 'swift-framework-plugin' ),
            "param_name"  => "slider_transition",
            "value"       => array(
                __( "Slide", 'swift-framework-plugin' ) => "slide",
                __( "Fade", 'swift-framework-plugin' )  => "fade"
            ),
            "required"       => array("display_type", "=", "slider"),
            "description" => __( "Choose the transition type for the slider.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show thumbnail nav", 'swift-framework-plugin' ),
            "param_name"  => "show_thumbs",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("display_type", "=", "slider"),
            "description" => __( "Show a thumbnail navigation display below the slider.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Enable Autoplay", 'swift-framework-plugin' ),
            "param_name"  => "autoplay",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "required"       => array("display_type", "=", "slider"),
            "description" => __( "Choose whether to autoplay the slider or not.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Show captions", 'swift-framework-plugin' ),
            "param_name"  => "show_captions",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Choose whether to show captions.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Enable gallery lightbox", 'swift-framework-plugin' ),
            "param_name"  => "enable_lightbox",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Enable lightbox functionality from the gallery.", 'swift-framework-plugin' )
        ),
    );

    if ( spb_get_theme_name() == "joyn" ) {
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Thumbnail Hover Style", 'swift-framework-plugin' ),
            "param_name"  => "hover_style",
            "value"       => array(
                __( 'Default', 'swift-framework-plugin' )     => "default",
                __( 'Standard', 'swift-framework-plugin' )    => "gallery-standard",
                __( 'Gallery Alt', 'swift-framework-plugin' ) => "gallery-alt-one",
                //__('Gallery Alt Two', 'swift-framework-plugin') => "gallery-alt-two",
            ),
            "description" => __( "Choose the thumbnail hover style for the asset. If set to 'Default', then this uses the thumbnail type set in the theme options.", 'swift-framework-plugin' )
        );
    }

    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Extra class", 'swift-framework-plugin' ),
        "param_name"  => "el_class",
        "value"       => "",
        "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
    );


    /* SHORTCODE MAP
    ================================================== */
    SPBMap::map( 'spb_gallery', array(
        "name"   => __( "Gallery", 'swift-framework-plugin' ),
        "base"   => "spb_gallery",
        "class"  => "spb_gallery spb_tab_media",
        "icon"   => "icon-gallery",
        "params" => $params
    ) );
