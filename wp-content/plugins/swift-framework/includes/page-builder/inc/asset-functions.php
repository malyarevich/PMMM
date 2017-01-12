<?php

    /*
    *
    *	Swift Page Builder - Asset Functions Class
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */


    /* AJAXURL GLOBAL
    ================================================== */
    if ( !function_exists('spb_ajaxurl_global') ) {
		function spb_ajaxurl_global() {
		?>
			<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
			</script>
			<?php
		}
		add_action('wp_head','spb_ajaxurl_global');
	}

    /* GET IMAGE SIZES
    ================================================== */
    if ( ! function_exists( 'spb_get_image_sizes' ) ) {
        function spb_get_image_sizes() {
            $image_sizes = array();
            foreach ( get_intermediate_image_sizes() as $size_name ) {
                $image_sizes[ $size_name ] = $size_name;
            }
            $image_sizes[ 'full' ] = 'full';
            $image_sizes = apply_filters('spb_image_sizes', $image_sizes);
            return $image_sizes;
        }
    }

    /* COUNTDOWN SHORTCODE LOCALE
    ================================================== */
    if ( ! function_exists( 'sf_countdown_shortcode_locale' ) ) {
        function sf_countdown_shortcode_locale() {
            ?>
            <div id="countdown-locale" data-label_year="<?php _e( 'Year', 'swift-framework-plugin' ); ?>"
                 data-label_years="<?php _e( 'Years', 'swift-framework-plugin' ); ?>"
                 data-label_month="<?php _e( 'Month', 'swift-framework-plugin' ); ?>"
                 data-label_months="<?php _e( 'Months', 'swift-framework-plugin' ); ?>"
                 data-label_weeks="<?php _e( 'Weeks', 'swift-framework-plugin' ); ?>"
                 data-label_week="<?php _e( 'Week', 'swift-framework-plugin' ); ?>"
                 data-label_days="<?php _e( 'Days', 'swift-framework-plugin' ); ?>"
                 data-label_day="<?php _e( 'Day', 'swift-framework-plugin' ); ?>"
                 data-label_hours="<?php _e( 'Hours', 'swift-framework-plugin' ); ?>"
                 data-label_hour="<?php _e( 'Hour', 'swift-framework-plugin' ); ?>"
                 data-label_mins="<?php _e( 'Mins', 'swift-framework-plugin' ); ?>"
                 data-label_min="<?php _e( 'Min', 'swift-framework-plugin' ); ?>"
                 data-label_secs="<?php _e( 'Secs', 'swift-framework-plugin' ); ?>"
                 data-label_sec="<?php _e( 'Sec', 'swift-framework-plugin' ); ?>"></div>
        <?php
        }
        add_action( 'wp_footer', 'sf_countdown_shortcode_locale' );
    }


    if ( ! function_exists( 'sf_get_embed_src' ) ) {
        function sf_get_embed_src( $url ) {
            if ( strpos( $url, 'youtube' ) ) {
                preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $video_id );
                $youtube_params = apply_filters( 'sf_youtube_embed_src_params', '?autoplay=1' );
                if ( is_ssl() ) {
                    if ( isset( $video_id[1] ) ) {
                        return 'https://www.youtube.com/embed/' . $video_id[1] . $youtube_params;
                    }
                } else {
                    if ( isset( $video_id[1] ) ) {
                        return 'http://www.youtube.com/embed/' . $video_id[1] . $youtube_params;
                    }
                }
            } else {
                $url          = str_replace( 'https://', 'http://', $url );
                $video_id     = sf_get_vimeoid( $url );
                $time_stamp = explode('#',$url);
                $video_id  = (!empty($time_stamp[1]))?$video_id.'#'.$time_stamp[1]:$video_id;
                $vimeo_params = apply_filters( 'sf_vimeo_embed_src_params', '?title=0&byline=0&portrait=0&autoplay=1' );
                if ( is_ssl() ) {
                    if ( $video_id != "" ) {
                        return 'https://player.vimeo.com/video/' . $video_id . $vimeo_params;
                    }
                } else {
                    if ( $video_id != "" ) {
                        return 'http://player.vimeo.com/video/' . $video_id . $vimeo_params;
                    }
                }
            }
        }
    }

    /* LIGHTBOX WRAP CLASS
    ================================================== */
    if ( ! function_exists( 'spb_lightbox_wrap_class' ) ) {
        function spb_lightbox_wrap_class() {
            return apply_filters( 'spb_lightbox_wrap_class', '' );
        }   
    }


    /* LIGHTBOX LINK CONFIG
    ================================================== */
    if ( ! function_exists( 'spb_lightbox_link_config' ) ) {
        function spb_lightbox_link_config( $atts ) {
            
            // GET UPLOADS URL
            $uploads = wp_upload_dir(); 

            // IMAGE
            $image = wp_get_attachment_metadata( $atts['image_id'] );

            // CLASS
            $link_class = 'lightbox';
            if ( isset($atts['extra_class']) && $atts['extra_class'] != "" ) {
                $link_class .= ' ' . $atts['extra_class'];
            }
            $link_class = apply_filters( 'spb_lightbox_link_class', $link_class );
            if ( isset($atts['is_slider']) && $atts['is_slider'] ) {
                $link_class .= ' is-slider-link';
            }
            $link_config = 'class="' . $link_class . '"';

            // LIGHTBOX ID
            if ( isset($atts['lightbox_id']) && $atts['lightbox_id'] != "" ) {
                $link_config .= ' data-rel="ilightbox[' . $atts["lightbox_id"] . ']"';  
            }

            // CAPTION
            if ( isset($atts['caption']) && $atts['caption'] != "" ) {
                $link_config .= ' data-caption="' . $atts['caption'] . '"';   
            }

            // TITLE
            if ( isset($atts['title']) && $atts['title'] != "" ) {
                $link_config .= ' data-title="' . $atts['title'] . '" title="' . $atts['title'] . '"';   
            }

            // IMAGE HREF
            if ( $image ) {
                $url = esc_url( $uploads['baseurl'] . '/' . $image['file'] );
                $link_config .= ' href="' . $url . '"';
            }

            // RETURN
            return apply_filters( 'spb_lightbox_link_config', $link_config, $atts );
        }   
    }


    /* CONTAINER OVERLAY
    ================================================== */
	if ( !function_exists('spb_container_overlay') ) {
		function spb_container_overlay() {
				$preloader = "";
				if ( function_exists( 'sf_get_preloader_svg') ) {
					$preloader = sf_get_preloader_svg( true );
				}
			?>

			<div class="sf-container-overlay">
				<div class="sf-loader">
					<?php echo $preloader; ?>
				</div>
			</div>

		<?php }
		add_action( 'wp_footer', 'spb_container_overlay' );
	}


    /* TEAM MEMBER AJAX
    ================================================== */
    if ( !function_exists('spb_team_member_ajax') ) {
		function spb_team_member_ajax() {

			$postID = '';

			if ( ! empty( $_REQUEST['post_id'] ) ) {
	            $postID = $_REQUEST['post_id'];
	        }

	        $args = array(
	        	'p' 		=> $postID,
	        	'post_type' => 'team'
	        );
	        $query  = new WP_Query($args);

		    if ($query->have_posts()) {
		        while ( $query->have_posts() ) {
		            $query->the_post();

		            $member_name     	= get_the_title();
		            $member_position 	= spb_get_post_meta( $postID, 'sf_team_member_position', true );
	                $custom_excerpt  	= spb_get_post_meta( $postID, 'sf_custom_excerpt', true );
	                $member_email       = spb_get_post_meta( $postID, 'sf_team_member_email', true );
				    $member_phone       = spb_get_post_meta( $postID, 'sf_team_member_phone_number', true );
				    $member_twitter     = spb_get_post_meta( $postID, 'sf_team_member_twitter', true );
				    $member_facebook    = spb_get_post_meta( $postID, 'sf_team_member_facebook', true );
				    $member_linkedin    = spb_get_post_meta( $postID, 'sf_team_member_linkedin', true );
				    $member_skype       = spb_get_post_meta( $postID, 'sf_team_member_skype', true );
				    $member_google_plus = spb_get_post_meta( $postID, 'sf_team_member_google_plus', true );
				    $member_instagram   = spb_get_post_meta( $postID, 'sf_team_member_instagram', true );
				    $member_dribbble    = spb_get_post_meta( $postID, 'sf_team_member_dribbble', true );

				    $unfiltered_content = str_replace( '<!--more-->', '', $query->post->post_content );
					$filtered_content   = apply_filters( 'the_content', $unfiltered_content );
	                $member_bio 	 	= $filtered_content;
	                $member_image_url   = wp_get_attachment_url( get_post_thumbnail_id(), 'full' );

		            $data = '
		                <div class="team-member-ajax-content">
		                	<a href="#" class="team-ajax-close">&times;</a>
		                	<figure class="profile-image-wrap">
			                	<div class="inner-wrap">';
									if ( $member_image_url != "" ) {
									$data .= '<img itemprop="image" src="' . esc_url( $member_image_url ) . '" alt="' . $member_name . '"/>';
									}
						        $data .= '<h1 class="entry-title">' . $member_name . '</h1>
						        			<h3 class="entry-subtitle">' . $member_position . '</h3>
						        </div>
					        	<div class="backdrop" style="background-image: url(' . esc_url( $member_image_url ) . ');"></div>
					        </figure>
					        <div class="content-wrap">
			                    <div class="entry-content">' . do_shortcode($member_bio) . '</div>
		                	</div>
		                </div>
		                <footer class="team-member-aux">
		                		<div class="member-aux-inner clearfix">
			                		<ul class="member-contact">';
				                        if ( $member_phone ) {
				                            $data .= '<li class="phone"><span itemscope="telephone">' . esc_attr($member_phone) . '</span>
				                            </li>';
				                        }
				                        if ( $member_email ) {
				                            $data .= '<li class="email"><span itemscope="email"><a href="mailto:' . sanitize_email($member_email) . '">' . $member_email . '</a></span>
				                            </li>';
				                        }
				                    $data .= '</ul>
			                		<ul class="social-icons">';
				                        if ( $member_twitter ) {
				                        	$data .= '<li class="twitter"><a href="http://www.twitter.com/' . esc_attr($member_twitter) . '" target="_blank"><i class="fa-twitter"></i><i class="fa-twitter"></i></a>
				                            </li>';
				                       	}
				                       	if ( $member_facebook ) {
				                            $data .= '<li class="facebook"><a href="' . esc_url($member_facebook) . '" target="_blank"><i class="fa-facebook"></i><i class="fa-facebook"></i></a></li>';
				                        }
				                        if ( $member_linkedin ) {
				                            $data .= '<li class="linkedin"><a href="' . esc_url($member_linkedin) . '" target="_blank"><i class="fa-linkedin"></i><i class="fa-linkedin"></i></a></li>';
				                       	}
				                       	if ( $member_google_plus ) {
				                            $data .= '<li class="googleplus"><a href="' . esc_url($member_google_plus) . '" target="_blank"><i class="fa-google-plus"></i><i class="fa-google-plus"></i></a></li>';
				                        }
				                        if ( $member_skype ) {
				                            $data .= '<li class="skype"><a href="skype:' . esc_attr($member_skype) . '" target="_blank"><i class="fa-skype"></i><i class="fa-skype"></i></a></li>';
				                      	}
				                      	if ( $member_instagram ) {
				                            $data .= '<li class="instagram"><a href="' . esc_url($member_instagram) . '" target="_blank"><i class="fa-instagram"></i><i class="fa-instagram"></i></a></li>';
				                        }
				                        if ( $member_dribbble ) {
				                            $data .= '<li class="dribbble"><a href="http://www.dribbble.com/' . esc_attr($member_dribbble) . '" target="_blank"><i class="fa-dribbble"></i><i class="fa-dribbble"></i></a></li>';
				                    	}
				                    $data .= '</ul>
			                    </div>
		                	</footer>
		            </div>  
		            ';

		        }
		    } 
		    else {
		        $data = __( "Couldn't find team member, please try again.", "swift-framework-admin" );
		    }
		    wp_reset_postdata();

		    echo '<div id="postdata">'.$data.'</div>';

		    die();
		}
		add_action( 'wp_ajax_nopriv_spb_team_member_ajax', 'spb_team_member_ajax' );
		add_action( 'wp_ajax_spb_team_member_ajax', 'spb_team_member_ajax' );
	}


    /* USER DIRECTORY LISTINGS
    ================================================== */
    if ( ! function_exists( 'sf_directory_user_listings' ) ) {
        function sf_directory_user_listings($user_id) {

            wp_reset_query();         
            $search_term = "";
            $listing_output = "";
            $excerpt_length = "";

            $search_query_args = array(
                's'                => $search_term,
                'post_type'        => 'directory',
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'suppress_filters' => false,
                'author'           => $user_id,
            
            );   

            $search_query_args = http_build_query( $search_query_args );
            $search_results    = get_posts( $search_query_args );

            foreach ( $search_results as $result ) {
           
                $post_excerpt = $result->post_content;

                if ( $excerpt_length != '') {

                    if ( $custom_excerpt != '' ) {
                        $post_excerpt = sf_custom_excerpt( $custom_excerpt, $excerpt_length );
                    } else {
                        $post_excerpt = sf_excerpt( $excerpt_length );
                    }

                }else{
                        $post_excerpt = sf_excerpt( 200 );
                }

                $post_excerpt = $result->post_content;
                $post_terms = get_the_terms( $result->ID, 'directory-category' );
                $term_slug  = " ";
                $category_list_text = "";

                if ( ! empty( $post_terms ) ) {
                    foreach ( $post_terms as $post_term ) {
                        $term_slug = $term_slug . $post_term->slug . ' ';
                        $category_list_text .= $post_term->name. ' | ';
                    }
                }

                $category_list_text = rtrim($category_list_text, "| ");

                $location_terms = get_the_terms( $result->ID, 'directory-location' );
                $location_text = "";


                if ( ! empty( $location_terms ) ) {
                    foreach ( $location_terms as $location_term ) {
                        $location_text .= $location_term->name. ' | ';
                    }

                    $location_text = rtrim($location_text, "| ");

                    if ( $category_list_text != ''){
                        $location_text = '| ' . $location_text;
                    }

                }

                $pin_img_url      = wp_get_attachment_image_src( spb_get_post_meta( $result->ID, 'sf_directory_map_pin', true ), 'full' );
                $img_src          = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), 'thumb-image' );
                $pin_logo_url     = $pin_img_url[0];  
                $pin_thumbnail    = $img_src[0];
                $pin_link    = esc_url( spb_get_post_meta( $result->ID, 'sf_directory_pin_link', true ) );
                $pin_link    = esc_url( spb_get_post_meta( $result->ID, 'sf_directory_pin_link', true ) );

                $listing_output .= '<div class="directory-results container"><div class="directory-list-results">';
                $listing_output .= '<div class="directory-item clearfix">';

                // Item thumb if provided
                if ( $pin_thumbnail != "" ) {
                    $listing_output .= '<figure class="animated-overlay overlay-alt"><img itemprop="image" src="' . $pin_thumbnail . '" alt="' . $result->post_title .'"><a href="' . $pin_link . '" class="link-to-post"></a><div class="figcaption-wrap"></div><figcaption><div class="thumb-info"><h4>' . $result->post_title . '</h4></div></figcaption></figure>';
                }

                // Item details
                $listing_output .= '<div class="directory-item-details"><h3>' . $result->post_title . '</h3><div class="item-meta">' . $category_list_text . $location_text . '</div><div class="excerpt" itemprop="description"><p>' . $post_excerpt . '</p> </div></div>';

                $listing_output .= '</div>';

                $listing_output .= '<a class="edit-listing" data-listing-id="' . $result->ID . '" href="#" target="_blank">Edit</a>';
                $listing_output .= '<a class="delete-listing" data-listing-id="' . $result->ID . '" href="#" target="_blank">Delete</a>';
                $listing_output .= '</div></div>';

            }
            
            //DELETE CONFIRMATION MODAL HTML
            $listing_output .= '<div id="modal-from-dom" class="modal-delete-listing modal fade"><div class="modal-header"><h3>' . __( "Delete Directory Listing", "swiftframework" ) . '</h3></div>';
            $listing_output .= '<div class="modal-body"><p>' . __( "You are about to delete this directory listing, this procedure is irreversible.", "swiftframework" ) . '</p><p>' . __( "Do you want to proceed?", "swiftframework" ) .'</p></div>';
            $listing_output .= '<div class="modal-footer"><a href="#" class="btn danger delete-listing-confirmation">' . __( "Yes", "swiftframework" ) . '</a><a href="#" class="btn secondary cancel-delete-listing">' . __( "No", "swiftframework" ) . '</a></div></div>';

            echo $listing_output;

        }
    }

    
    /* DIRECTORY EDIT MODAL FRONT END 
    ================================================== */
    if ( ! function_exists( 'sf_edit_directory_item' ) ) {
        function sf_edit_directory_item() {
            
            if ( isset( $_REQUEST['listing_id'] ) ){
                $listing_id = $_REQUEST['listing_id'];

            }

            $listing_output = "";
        
            $categories     = wp_get_post_terms( $listing_id, "directory-category" );
            $locations      = wp_get_post_terms( $listing_id, "directory-location" );
            $featured_image_url = wp_get_attachment_image_src( spb_get_post_meta($listing_id, '_thumbnail_id', true), 'medium' );          
            $pin_image_url = wp_get_attachment_image_src( spb_get_post_meta($listing_id, 'sf_directory_map_pin', true), 'medium' );          

            $listing_output .= '<div id="edit-modal-header"><h2>' .  __( "Edit", "swiftframework" ) .' ' . get_the_title( $listing_id ) . ' ' .  __( "Listing", "swiftframework" ) . '</h2><div class="edit_form_actions"><a href="#" class="cancel-listing-modal">Cancel</a><a href="#" class="save-listing-modal button-primary" data-listing-id="' . $listing_id . '">' . __( 'Save', 'swiftframework' ) . '</a></div></div>';
            $listing_output .=  '<div class="directory-submit-wrap">';    
            $listing_output .=  '<form id="add-directory-entry" name="add-directory-entry" method="post" action="" enctype="multipart/form-data">';
                        
            // Title
            $listing_output .=  '<p><label for="directory_title">' . __( "Title", "swiftframework" ) . '</label><br/>';
            $listing_output .=  '<input type="text" id="directory_title" value="' . get_the_title( $listing_id ) . '" tabindex="1" size="20" name="directory_title"/></p>';
            
            // Description
            $listing_output .=  '<p><label for="description">' . __( "Description", "swiftframework" ) . '</label><br/>';
            $listing_output .=  '<textarea id="directory_description" tabindex="3" name="directory_description" cols="50" rows="6">' . get_post_field('post_content', $listing_id) . '</textarea></p>';
            
            //Dropdown translation text
            $choosecatmsg = __( 'Choose a Category', 'swiftframework' );
            $chooselocmsg = __( 'Choose a Location', 'swiftframework' );

            //Directory Category
            $argscat = array(
                        'id'               => 'directory-cat',
                        'name'             => 'directory-cat',
                        'show_option_none' => $choosecatmsg,
                        'tab_index'        => 4,
                        'taxonomy'         => 'directory-category',
                        'hide_empty'       => 0,
                        'echo'             => 0,
                        'selected'         => $categories[0]->term_id
                    );

            //Directory Location
            $argsloc = array(
                        'id'               => 'directory-loc',
                        'name'             => 'directory-loc',
                        'show_option_none' => $chooselocmsg,
                        'tab_index'        => 4,
                        'taxonomy'         => 'directory-location',
                        'hide_empty'       => 0,
                        'echo'             => 0,
                        'selected'         => $locations[0]->term_id
                    );

            // Category
            $listing_output .=  '<p><label for="description">' . __( "Category", "swiftframework" ) . '</label></p>';

            $categories_cat =  wp_dropdown_categories( $argscat ); 
            $listing_output .=  $categories_cat;

            // Location
            $listing_output .=  '<p><label for="description">' . __( "Location", "swiftframework" ) . '</label></p>';

            $categories_loc =  wp_dropdown_categories( $argsloc ); 
            $listing_output .=  $categories_loc;

            // Address 
            $listing_output .=  '<p><label for="sf_directory_address">' . __( "Address", "swiftframework" ) . '</label>';
            $listing_output .=  '<input type="text" value="' . spb_get_post_meta( $listing_id, 'sf_directory_address', true ) . '" tabindex="5" size="16" name="sf_directory_address" id="sf_directory_address"/>';
            $listing_output .=  '<a href="#" id="sf_directory_calculate_coordinates" class="sf-button accent hide-if-no-js">' . __( "Generate Coordinates", "swiftframework" ) . '</a></p>';
            
            // Latitude Coordinate
            $listing_output .=  '<p><label for="sf_directory_lat_coord">' . __( "Latitude Coordinate", "swiftframework" ) . '</label>';
            $listing_output .=  '<input type="text" value="' . spb_get_post_meta( $listing_id, 'sf_directory_lat_coord', true ) . '" tabindex="5" size="16" name="sf_directory_lat_coord" id="sf_directory_lat_coord"/></p>';
            
             // Longitude Coordinate
            $listing_output .=  '<p><label for="sf_directory_lng_coord">' . __( "Longitude Coordinate", "swiftframework" ) . '</label>';
            $listing_output .=  '<input type="text" value="' . spb_get_post_meta( $listing_id, 'sf_directory_lng_coord', true ) . '" tabindex="5" size="16" name="sf_directory_lng_coord" id="sf_directory_lng_coord"/></p>';
            
            // Pin Image
            $listing_output .=  '<p><label for="file">' .  __( "Pin Image", "swiftframework" ) . '</label></p>';
            $listing_output .=  '<img  src="' . $pin_image_url[0] . '" >';
            $listing_output .=  '<p><input type="file" name="pin_image" id="pin_image"></p>';
            
            // Directory Featured Image 
            $listing_output .=  '<p><label for="file">' . __( "Featured Image", "swiftframework" ) . '</label></p>';
            $listing_output .=  '<img  src="' . $featured_image_url[0] . '" >';
            $listing_output .=  '<p><input type="file" name="featured_image" id="featured_image"></p>';
            
            // Pin Link Button
            $listing_output .=  '<p><label for="sf_directory_pin_link">' . __( "Pin Link", "swiftframework" ) . '</label>';
            $listing_output .=  '<input type="text" value="' . spb_get_post_meta( $listing_id, 'sf_directory_pin_link', true ) . '" tabindex="5" size="16" name="sf_directory_pin_link" id="sf_directory_pin_link"/></p>';
            
             // Pin Button Text
            $listing_output .=  '<p><label for="sf_directory_pin_button_text">' . __( "Pin Button Text", "swiftframework" ) . '</label>';
            $listing_output .=  '<input type="text" value="' . spb_get_post_meta( $listing_id, 'sf_directory_pin_button_text', true ) . '" tabindex="5" size="16" name="sf_directory_pin_button_text" id="sf_directory_pin_button_text"/></p>';
 
            //Form Key 
            $listing_output .= '<input type="hidden" id="form_key" name="form_key" value="frontend_edit_listing">';
           
            //Listing Id 
            $listing_output .= '<input type="hidden" id="listing_id" name="listing_id" value="' . $listing_id . '">';
                   
            echo $listing_output;

            die();
        }
    }
    add_action( 'wp_ajax_sf_edit_directory_item', 'sf_edit_directory_item' );
    add_action( 'wp_ajax_nopriv_sf_edit_directory_item', 'sf_edit_directory_item' );


    /* DIRECTORY SAVE LISTING
    ================================================== */
    if ( ! function_exists( 'sf_save_directory_item' ) ) {
        function sf_save_directory_item() {

            if( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_REQUEST['form_key'] ) && $_REQUEST['form_key'] == 'frontend_edit_listing' )  {
       
                //LISTING ID
                if ( isset( $_REQUEST['listing_id'] ) ){
                    $listing_id = $_REQUEST['listing_id'];
                }

                //TITLE
                if ( isset( $_REQUEST['directory_title'] ) ){
                    $title = $_REQUEST['directory_title'];
                }

                //DESCRIPTION
                if ( isset( $_REQUEST['directory_description'] ) ){
                    $description = $_REQUEST['directory_description'];
                }

                //CATEGORY
                if ( isset( $_REQUEST['directory-cat'] ) ){
                    $category = $_REQUEST['directory-cat'];
                }

                //LOCATION
                if ( isset( $_REQUEST['directory-loc'] ) ){
                    $location = $_REQUEST['directory-loc'];
                }

                //ADDRESS
                if ( isset( $_REQUEST['sf_directory_address'] ) ){
                    $address = $_REQUEST['sf_directory_address'];
                }

                //LATITUDE
                if ( isset( $_REQUEST['sf_directory_lat_coord'] ) ){
                    $latitude = $_REQUEST['sf_directory_lat_coord'];
                }

                //LONGITUDE
                if ( isset( $_REQUEST['sf_directory_lng_coord'] ) ){
                    $longitude = $_REQUEST['sf_directory_lng_coord'];
                }

                //PIN LINK
                if ( isset( $_REQUEST['sf_directory_pin_link'] ) ){
                    $pin_link = $_REQUEST['sf_directory_pin_link'];
                }

                //PIN BUTTON TEXT
                if ( isset( $_REQUEST['sf_directory_pin_button_text'] ) ){
                    $pin_button_text = $_REQUEST['sf_directory_pin_button_text'];
                }

                           
                $dir_post = array(
                 'ID'           => $listing_id,
                 'post_title'   =>  $title,
                 'post_content'   =>  $description,
          
                );

                //Add Taxonomy terms(Location/Category)
                wp_set_object_terms( $listing_id, (int) $category, 'directory-category', false );
                wp_set_object_terms( $listing_id, (int) $location, 'directory-location', false );

                //Update listing post fields
                wp_update_post( $dir_post );

                update_post_meta( $listing_id, 'sf_directory_address', $address );
                update_post_meta( $listing_id, 'sf_directory_lat_coord', $latitude );
                update_post_meta( $listing_id, 'sf_directory_lng_coord', $longitude );
                update_post_meta( $listing_id, 'sf_directory_pin_button_text', $pin_button_text );
                update_post_meta( $listing_id, 'sf_directory_pin_link', $pin_link );

                //Proccess Images
                if ( $_FILES ) {

                    $post_id = $_REQUEST['listing_id'];
                    foreach ( $_FILES as $file => $array ) {
                            
                            if( $array['name'] != '' ){
                                $newupload = sf_insert_attachment( $file, $post_id );

                                if ( $file == 'pin_image' ) {
                                    update_post_meta( $post_id, 'sf_directory_map_pin', $newupload );
                                } else  {    
                                    update_post_meta( $post_id, '_thumbnail_id', $newupload );
                                }
                            }
                    }
                }

           
            }

        }
    }
    add_action( 'init', 'sf_save_directory_item' );
    

    /* DIRECTORY DELETE LISTING
    ================================================== */
    if ( ! function_exists( 'sf_delete_directory_item' ) ) {
        function sf_delete_directory_item() {
            
            if ( isset( $_REQUEST['listing_id'] ) ){
                $listing_id = $_REQUEST['listing_id'];

            }

            wp_delete_post( $listing_id );

            die();

        }   
    }

