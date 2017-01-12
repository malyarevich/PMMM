<?php

    /*
    *
    *	Swift Page Builder - Team Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_team extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $output = $filter = $social_icon_type = $items = $team_member_link_class = $el_position = '';

            extract( shortcode_atts( array(
                'title'        => '',
                'item_columns' => '3',
                'display_type' => 'standard',
                'carousel'     => 'no',
                "item_count"   => '12',
                "custom_image_height" => '',
                "category"     => '',
                'pagination'   => '',
                'post_ids'     => '',
                'profile_link' => 'yes',
                'ajax_overlay' => 'no',
                'fullwidth'    => 'no',
                'gutters'      => 'yes',
                'order'        => 'desc',
                'order_by'     => 'date',
                'el_position'  => '',
                'width'        => '1/1',
                'el_class'     => ''
            ), $atts ) );

            // Enqueue
            if ( $carousel == "yes" ) {
                wp_enqueue_script( 'owlcarousel' );
            }

            // CATEGORY SLUG MODIFICATION
            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );

			$contact_icon 	 	= apply_filters( 'sf_mail_icon', '<i class="ss-mail"></i>' );
			$phone_icon			= apply_filters( 'sf_phone_icon', '<i class="ss-phone"></i>' );

            /* SIDEBAR CONFIG
            ================================================== */
            global $sf_sidebar_config;

            $sidebars = '';
            if ( ( $sf_sidebar_config == "left-sidebar" ) || ( $sf_sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sf_sidebar_config == "both-sidebars" ) {
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


            /* QUERY CONFIG
            ================================================== */
            global $post, $wp_query;

            $paged        = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $team_args    = array(
                'post_type'           => 'team',
                'post_status'         => 'publish',
                'paged'               => $paged,
                'team-category'       => $category_slug,
                'posts_per_page'      => $item_count,
                'order'               => $order,
                'orderby'             => $order_by,
                'ignore_sticky_posts' => 1
            );

            if ( $post_ids != "" ) {
                $team_args['post__in'] = array($post_ids);
            }

            $team_members = new WP_Query( $team_args );

            $count        = 0;
            $image_width  = 270;
            $image_height = 270;

            if ( $item_columns == "1" ) {
                $item_class = 'col-sm-12';
            } else if ( $item_columns == "2" ) {
                $image_width  = 540;
                $image_height = 540;
                $item_class   = 'col-sm-6';
            } else if ( $item_columns == "3" ) {
                $image_width  = 360;
                $image_height = 360;
                $item_class   = 'col-sm-4';
            } else if ( $item_columns == "5" ) {
                $image_width  = 360;
                $image_height = 360;
                $item_class   = 'col-sm-sf-5';
            } else {
                $item_class = 'col-sm-3';
            }

            if ( $custom_image_height != "" ) {
                $image_height = $custom_image_height;
            }

            $list_class = 'display-type-' . $display_type;

            if ( $ajax_overlay == "yes" ) {
                $team_member_link_class = 'team-member-ajax';
            }

            if ( $gutters == "no" ) {
                $list_class .= ' no-gutters';
            }

            if ( $carousel == "yes" ) {
                global $sf_carouselID;
                if ( $sf_carouselID == "" ) {
                    $sf_carouselID = 1;
                } else {
                    $sf_carouselID ++;
                }
                $item_class = 'carousel-item';
                $items .= '<div class="team-carousel carousel-wrap"><div id="carousel-' . $sf_carouselID . '" class="team-members carousel-items ' . $list_class . ' clearfix" data-columns="' . $item_columns . '" data-auto="false">';
            } else {
                $items .= '<div class="team-members ' . $list_class . ' row clearfix">';
            }

            while ( $team_members->have_posts() ) : $team_members->the_post();
				
                $postID          = $post->ID;
                $member_name     = get_the_title();
                $member_position = spb_get_post_meta( $postID, 'sf_team_member_position', true );
                $custom_excerpt  = spb_get_post_meta( $postID, 'sf_custom_excerpt', true );
                $pb_legacy_active       = get_post_meta($postID, '_spb_js_status', true);
                $pb_active       = get_post_meta($postID, '_spb_status', true);
                $member_link     = get_permalink( $postID );
                $member_bio 	 = "";
                
                if ($pb_active == "true" || $pb_legacy_active == "true") {
                	if ( $custom_excerpt != "" ) {
                	    $member_bio = $custom_excerpt;
                	} else {
                		$member_bio = get_the_excerpt();
                	}
                } else {
	                if ( $custom_excerpt != "" ) {
	                    $member_bio = sf_custom_excerpt( $custom_excerpt, 1000 );
	                } else {
	                	$member_bio      = apply_filters( 'the_content', get_the_content( '' ) );
	                }
                }
                
                // Meta
                $member_email       = spb_get_post_meta( $postID, 'sf_team_member_email', true );
                $member_phone       = spb_get_post_meta( $postID, 'sf_team_member_phone_number', true );
                $member_twitter     = spb_get_post_meta( $postID, 'sf_team_member_twitter', true );
                $member_facebook    = spb_get_post_meta( $postID, 'sf_team_member_facebook', true );
                $member_linkedin    = spb_get_post_meta( $postID, 'sf_team_member_linkedin', true );
                $member_google_plus = spb_get_post_meta( $postID, 'sf_team_member_google_plus', true );
                $member_skype       = spb_get_post_meta( $postID, 'sf_team_member_skype', true );
                $member_instagram   = spb_get_post_meta( $postID, 'sf_team_member_instagram', true );
                $member_dribbble    = spb_get_post_meta( $postID, 'sf_team_member_dribbble', true );
                $member_behance     = spb_get_post_meta( $postID, 'sf_team_member_behance', true );
                $member_flickr      = spb_get_post_meta( $postID, 'sf_team_member_flickr', true );
                $member_vimeo       = spb_get_post_meta( $postID, 'sf_team_member_vimeo', true );
                $member_snapchat    = spb_get_post_meta( $postID, 'sf_team_member_snapchat', true );
                $view_profile_text  = __( "View Profile", 'swift-framework-plugin' );
                $thumb_image        = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=full' );
                $item_icon          = apply_filters( 'sf_team_hover_icon', "fa-pencil" );
                $item_svg_icon      = apply_filters( 'sf_team_hover_svg_icon', "" );
                
                // Thumb Image
                $thumb_img_url = "";
                    foreach ( $thumb_image as $detail_image ) {
                    $thumb_image_id = $detail_image['ID'];
                    $thumb_img_url  = $detail_image['url'];
                    break;
                }
                if ( ! $thumb_image ) {
                    $thumb_image    = get_post_thumbnail_id();
                    $thumb_image_id = $thumb_image;
                    $thumb_img_url  = wp_get_attachment_url( $thumb_image, 'full' );
                }                
                $image   = spb_image_resizer( $thumb_img_url, $image_width, $image_height, true, false );

                // Output
                $items .= '<div itemscope data-id="id-' . $count . '" class="clearfix team-member ' . $item_class . '">';

                if ( spb_theme_supports('minimal-team-hover') ) {
                    $items .= '<div class="team-member-item-wrap">';
                }

                $items .= '<figure class="animated-overlay">';

                if ( spb_theme_supports('minimal-team-hover') && $display_type != "gallery" ) {
                    $items .= '<a class="team-member-link ' . $team_member_link_class . '" href="' . get_permalink() . '" data-id="' . $postID . '"></a>';
                }

                if ( $display_type == "gallery" ) {
                    $items .= '<a class="team-gallery-mobile-link" href="' . get_permalink() . '" class="' . $team_member_link_class . '" data-id="' . $postID . '"></a>';
                }
                if ( $image ) {
                    $items .= '<img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $member_name . '" />';
                }

                $items .= '<figcaption class="team-' . $display_type . '">';

                if ( spb_theme_supports('minimal-team-hover') && $display_type != "gallery" ) {
                    $items .= '<div class="thumb-info thumb-info-alt">';
                    if ( $item_svg_icon != "" ) {
                        $items .= $item_svg_icon;
                    } else {
                        $items .= '<i class="'.$item_icon.'"></i>';
                    }
                    $items .= '</div>';
                } else {
                    $items .= '<div class="thumb-info">';

                    if ( $display_type == "gallery" ) {
                        if ( $profile_link == "yes" ) {
                            $items .= '<h4 class="team-member-name"><a href="' . get_permalink() . '" class="' . $team_member_link_class . '" data-id="' . $postID . '">' . $member_name . '</a></h4>';
                        } else {
                            $items .= '<h4 class="team-member-name">' . $member_name . '</h4>';
                        }
                        $items .= '<h5 class="team-member-position">' . $member_position . '</h5>';
                        $items .= '<div class="name-divide"></div>';
                    }

                    if ( ( $member_twitter ) || ( $member_facebook ) || ( $member_linkedin ) || ( $member_google_plus ) || ( $member_skype ) || ( $member_instagram ) || ( $member_dribbble ) || ( $member_behance ) || ( $member_flickr ) || ( $member_vimeo ) || ( $member_snapchat ) ) {
                        $items .= '<ul class="social-icons">';
                        if ( $member_twitter ) {
                            $items .= '<li class="twitter"><a href="http://www.twitter.com/' . $member_twitter . '" target="_blank"><i class="fa-twitter"></i><i class="fa-twitter"></i></a></li>';
                        }
                        if ( $member_facebook ) {
                            $items .= '<li class="facebook"><a href="' . $member_facebook . '" target="_blank"><i class="fa-facebook"></i><i class="fa-facebook"></i></a></li>';
                        }
                        if ( $member_linkedin ) {
                            $items .= '<li class="linkedin"><a href="' . $member_linkedin . '" target="_blank"><i class="fa-linkedin"></i><i class="fa-linkedin"></i></a></li>';
                        }
                        if ( $member_google_plus ) {
                            $items .= '<li class="googleplus"><a href="' . $member_google_plus . '" target="_blank"><i class="fa-google-plus"></i><i class="fa-google-plus"></i></a></li>';
                        }
                        if ( $member_skype ) {
                            $items .= '<li class="skype"><a href="skype:' . $member_skype . '" target="_blank"><i class="fa-skype"></i><i class="fa-skype"></i></a></li>';
                        }
                        if ( $member_instagram ) {
                            $items .= '<li class="instagram"><a href="' . $member_instagram . '" target="_blank"><i class="fa-instagram"></i><i class="fa-instagram"></i></a></li>';
                        }
                        if ( $member_dribbble ) {
                            $items .= '<li class="dribbble"><a href="http://www.dribbble.com/' . $member_dribbble . '" target="_blank"><i class="fa-dribbble"></i><i class="fa-dribbble"></i></a></li>';
                        }
                        if ( $member_behance ) {
                            $items .= '<li class="behance"><a href="' . $member_behance . '" target="_blank"><i class="fa-behance"></i><i class="fa-behance"></i></a></li>';
                        }
                        if ( $member_flickr ) {
                            $items .= '<li class="flickr"><a href="' . $member_flickr . '" target="_blank"><i class="fa-flickr"></i><i class="fa-flickr"></i></a></li>';
                        }
                        if ( $member_vimeo ) {
                            $items .= '<li class="vimeo"><a href="' . $member_vimeo . '" target="_blank"><i class="fa-vimeo"></i><i class="fa-vimeo"></i></a></li>';
                        }
                        if ( $member_snapchat ) {
                            $items .= '<li class="snapchat"><a href="' . $member_snapchat . '" target="_blank"><i class="fa-snapchat"></i><i class="fa-snapchat"></i></a></li>';
                        }
                        $items .= '</ul>';
                    }
                    if ( $display_type != "gallery" && $profile_link == "yes" ) {
                        $items .= '<a class="view-profile ' . $team_member_link_class . '" href="' . $member_link . '" data-id="' . $postID . '">' . $view_profile_text . '</a>';
                    }

                    $items .= '</div>';
                }

                $items .= '</figcaption>';

                $items .= '</figure>';

                if ( spb_theme_supports('minimal-team-hover') ) {
                    $items .= '<div class="team-member-details-wrap">';
                }

                if ( $display_type != "gallery" ) {
                    if ( $profile_link == "yes" ) {
                        $items .= '<h4 class="team-member-name"><a href="' . get_permalink() . '" class="' . $team_member_link_class . '" data-id="' . $postID . '">' . $member_name . '</a></h4>';
                    } else {
                        $items .= '<h4 class="team-member-name">' . $member_name . '</h4>';
                    }
                    $items .= '<h5 class="team-member-position">' . $member_position . '</h5>';
                }
                if ( $display_type == "standard" ) {

                    if ( spb_theme_supports('minimal-team-hover') ) {
                        $items .= '<div class="team-member-divider"></div>';
                    }

                    if ( $profile_link == "yes" ) {
                        $items .= '<div class="team-member-bio">' . $member_bio . '<a href="' . get_permalink() . '" class="read-more ' . $team_member_link_class . '" data-id="' . $postID . '">' . $view_profile_text . '</a></div>';
                    } else {
                        $items .= '<div class="team-member-bio">' . $member_bio . '</div>';
                        $items .= '<ul class="member-contact">';
                        if ( $member_email ) {
                            $items .= '<li>'.$contact_icon.'<span itemscope="email"><a href="mailto:' . $member_email . '">' . $member_email . '</a></span></li>';
                        }
                        if ( $member_phone ) {
                            $items .= '<li>'.$phone_icon.'<span itemscope="telephone">' . $member_phone . '</span></li>';
                        }
                        $items .= '</ul>';
                    }
                }

                if ( spb_theme_supports('minimal-team-hover') ) {
                    $items .= '</div>';
                    $items .= '</div>';
                }

                $items .= '</div>';
                $count ++;

            endwhile;

            wp_reset_postdata();

            if ( $carousel == "yes" ) {
                $items .= '</div></div>';
            } else {
                $items .= '</div>';
            }

            // PAGINATION
            if ( $pagination == "yes" && $carousel == "no" ) {
                $items .= '<div class="pagination-wrap">';
                $items .= pagenavi( $team_members );
                $items .= '</div>';
            }

            $el_class = $this->getExtraClass( $el_class, $fullwidth );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="team_list carousel-asset spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                $output .= "\n\t\t" . '<div class="title-wrap container">';
                if ( $title != '' ) {
                    $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
                }
                if ( $carousel == "yes" ) {
                    $output .= spb_carousel_arrows();
                }
                $output .= '</div>';
            } else {
                $output .= "\n\t\t" . '<div class="title-wrap clearfix">';
                if ( $title != '' ) {
                    $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
                }
                if ( $carousel == "yes" ) {
                    $output .= spb_carousel_arrows();
                }
                $output .= '</div>';
            }
            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );

            global $sf_include_isotope, $sf_has_team, $sf_include_carousel;
            $sf_include_isotope = true;
            $sf_has_team        = true;

            if ( $carousel == "yes" ) {
                $sf_include_carousel = true;
            }

            return $output;
        }
    }

    /* PARAMS
    ================================================== */
    $params = array(
        array(
            "type"        => "textfield",
            "heading"     => __( "Widget title", 'swift-framework-plugin' ),
            "param_name"  => "title",
            "value"       => "",
            "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Display Type", 'swift-framework-plugin' ),
            "param_name"  => "display_type",
            "value"       => array(
                __( 'Standard', 'swift-framework-plugin' )              => "standard",
                __( 'Standard (No Excerpt)', 'swift-framework-plugin' ) => "standard-alt",
                __( 'Gallery', 'swift-framework-plugin' )               => "gallery"
            ),
            "description" => __( "Choose the display type for the team members.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Carousel", 'swift-framework-plugin' ),
            "param_name"  => "carousel",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Enables carousel funcitonality in the asset.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Columns", 'swift-framework-plugin' ),
            "param_name"  => "item_columns",
            "value"       => array(
                __( '1', 'swift-framework-plugin' ) => "1",
                __( '2', 'swift-framework-plugin' ) => "2",
                __( '3', 'swift-framework-plugin' ) => "3",
                __( '4', 'swift-framework-plugin' ) => "4",
                __( '5', 'swift-framework-plugin' ) => "5"
            ),
            "std"         => '4',
            "description" => __( "Choose the amount of columns you would like for the team asset.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Number of items", 'swift-framework-plugin' ),
            "param_name"  => "item_count",
            "value"       => "12",
            "description" => __( "The number of team members to show per page.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Specific Team Member(s)", 'swift-framework-plugin' ),
            "param_name"  => "post_ids",
            "value"       => "",
            "description" => __( "Select specific team members to show here, providing the post ID in comma delimited format.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Custom Image Height", 'swift-framework-plugin' ),
            "param_name"  => "custom_image_height",
            "value"       => "",
            "description" => __( "Enter a value here if you would like to override the image height of the team member images. Numerical value (no px).", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "select-multiple",
            "heading"     => __( "Team category", 'swift-framework-plugin' ),
            "param_name"  => "category",
            "value"       => sf_get_category_list( 'team-category' ),
            "description" => __( "Choose the category for the team items.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Profile Link", 'swift-framework-plugin' ),
            "param_name"  => "profile_link",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Select if you'd like the team members to link through to the profile page.", 'swift-framework-plugin' )
        )
    );
    
    if ( spb_theme_supports( 'spb-team-ajax' ) ) {
        $params[] = array(
                "type"        => "buttonset",
                "heading"     => __( "AJAX Overlay", 'swift-framework-plugin' ),
                "param_name"  => "ajax_overlay",
                "value"       => array(
                    __( 'No', 'swift-framework-plugin' )  => "no",
                    __( 'Yes', 'swift-framework-plugin' ) => "yes"
                ),
                "buttonset_on"  => "yes",
                "std"         => 'no',
                "description" => __( "Select if you'd like the team profile links to open up the team member via an AJAX overlay. NOTE: Shortcodes/jquery functions will not function within this mode, works best when only", 'swift-framework-plugin' )
            );
    }
    $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Pagination", 'swift-framework-plugin' ),
            "param_name"  => "pagination",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Show team pagination.", 'swift-framework-plugin' )
        );
    $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Full Width", 'swift-framework-plugin' ),
            "param_name"  => "fullwidth",
            "value"       => array(
                __( 'No', 'swift-framework-plugin' )  => "no",
                __( 'Yes', 'swift-framework-plugin' ) => "yes"
            ),
            "buttonset_on"  => "yes",
            "std" => "no",
            "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
        );
    $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Gutters", 'swift-framework-plugin' ),
            "param_name"  => "gutters",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "buttonset_on"  => "yes",
            "description" => __( "Select if you'd like spacing between the items, or not.", 'swift-framework-plugin' )
        );
    $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Order by", 'swift-framework-plugin' ),
            "param_name"  => "order_by",
            "value"       => array(
                __( 'None', 'swift-framework-plugin' ) => "none",
                __( 'ID', 'swift-framework-plugin' )  => "ID",
                __( 'Title', 'swift-framework-plugin' )  => "title",
                __( 'Date', 'swift-framework-plugin' )  => "date",
                __( 'Random', 'swift-framework-plugin' )  => "rand",
            ),
            "description" => __( "Select how you'd like the items to be ordered.", 'swift-framework-plugin' )
        );
    $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Order", 'swift-framework-plugin' ),
            "param_name"  => "order",
            "value"       => array(
                __( "Descending", 'swift-framework-plugin' ) => "DESC",
                __( "Ascending", 'swift-framework-plugin' )  => "ASC"
            ),
            "description" => __( "Select if you'd like the items to be ordered in ascending or descending order.", 'swift-framework-plugin' )
        );
    $params[] = array(
            "type"        => "textfield",
            "heading"     => __( "Extra class", 'swift-framework-plugin' ),
            "param_name"  => "el_class",
            "value"       => "",
            "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
        );


    /* SHORTCODE MAP
    ================================================== */
    SPBMap::map( 'spb_team', array(
        "name"   => __( "Team", 'swift-framework-plugin' ),
        "base"   => "spb_team",
        "class"  => "team",
        "icon"   => "icon-team",
        "params" => $params
    ) );
