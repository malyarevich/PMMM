<?php
if ( !function_exists('spb_image_resizer') ) {
	function spb_image_resizer( $url, $width, $height = null, $crop = null, $single = true ) {

		$debug_mode = false;

		if ( isset($_GET['spb_debug']) ) {
			$debug_mode = $_GET['spb_debug'];
		}
		
		if ( $debug_mode ) {
			echo 'IMAGE DEBUG @1x ------ '."\n";
		}
		
		/* WPML Fix for Image issue in Different domain per language */
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			global $sitepress;
			$url = $sitepress->convert_url($url,$sitepress->get_default_language());
		}
		
		//validate inputs
		if (!$width OR !$url) {
			if ($debug_mode) {
				echo 'inputs not valid'."\n";
			}
			return false;
		}
		
		if ($url == "default") {
			$url = get_template_directory_uri()."/images/default-thumb.png";
			$image = array (
				0 => $url,
				1 => '1600',
				2 => '1600'
			);
			return $image;
		}

		//define upload path & dir
		$upload_info = wp_upload_dir();
		$upload_dir = $upload_info['basedir'];
		$upload_url = $upload_info['baseurl'];

		$http_prefix = "http://";
		$https_prefix = "https://";

		/* if the $url scheme differs from $upload_url scheme, make them match
		   if the schemes differe, images don't show up. */
		if (!strncmp($url,$https_prefix,strlen($https_prefix))) { //if url begins with https:// make $upload_url begin with https:// as well
			$upload_url = str_replace($http_prefix,$https_prefix,$upload_url);
		} elseif (!strncmp($url,$http_prefix,strlen($http_prefix))) { //if url begins with http:// make $upload_url begin with http:// as well
			$upload_url = str_replace($https_prefix,$http_prefix,$upload_url);
		}

		//check if $img_url is local
		if ( !sf_wpml_activated() ) {
//			if (strpos( $url, home_url() ) === false) {
//				if ($debug_mode) { echo 'media URL not the same as site URL'."\n"; }
//				$image = array (
//					0 => $url,
//					1 => $width,
//					2 => $height
//				);
//				return $image;
//			}
		}

		//define path of image
		$rel_path = str_replace( $upload_url, '', $url);
		$img_path = $upload_dir . $rel_path;

		//check if img path exists, and is an image indeed
		if ( !sf_wpml_activated() ) {
			if( !file_exists($img_path) OR !getimagesize($img_path) ) {
				if ($debug_mode) {
					echo 'file does not exist'."\n";
				}
				$image = array (
					0 => $url,
					1 => $width,
					2 => $height
				);
				return $image;
			}
		}

		//get image info
		$info = pathinfo($img_path);
		$ext = $info['extension'];
		list($orig_w,$orig_h) = getimagesize($img_path);

		//get image size after cropping
		$dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
		$dst_w = $dims[4];
		$dst_h = $dims[5];

		//use this to check if cropped image already exists, so we can return that instead
		$suffix = "{$dst_w}x{$dst_h}";
		$dst_rel_path = str_replace( '.'.$ext, '', $rel_path);
		$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";
		
		if ($debug_mode) { "begin resize checking."; }
		
		//if orig size is smaller
		if ($width >= $orig_w) {
			
			if ($debug_mode) { "original is smaller."; }
			
			if (!$dst_h) :
				//can't resize, so return original url
				if ($debug_mode) { "can't resize, so return original url"; }
				$img_url = $url;
				$dst_w = $orig_w;
				$dst_h = $orig_h;

			else :
				//else check if cache exists
				if (file_exists($destfilename) && getimagesize($destfilename)) {
					$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
					
					if ($debug_mode) {
						echo 'cache exists (1)'."\n";
					}
				}
				//else resize and return the new resized image url
				else {

					if (function_exists('wp_get_image_editor') ) {
						
						if ($debug_mode) {
							echo 'image editor exists (1)'."\n";
						}

						$editor = wp_get_image_editor($img_path);

						if ($debug_mode) { var_dump($editor); }

						if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) ) {
							$image = array (
								0 => $url,
								1 => $width,
								2 => $height
							);
							return $image;
						}

						$resized_file = $editor->save();

						if(!is_wp_error($resized_file)) {
							$resized_rel_path = str_replace( $upload_dir, '', $resized_file['path']);
							$img_url = $upload_url . $resized_rel_path;
						} else {
							return false;
						}

					} else {
						
						if ($debug_mode) {
							echo 'image editor does not exist (1)'."\n";
						}
						$image = array (
							0 => $url,
							1 => $width,
							2 => $height
						);
						return $image;
						
					}
				}

			endif;

		}
		//else check if cache exists
		elseif (file_exists($destfilename) && getimagesize($destfilename)) {
			$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
			
			if ($debug_mode) {
				echo 'cache exists (2)'."\n";
			}
			
		}
		//else, we resize the image and return the new resized image url
		else {

			if ( function_exists('wp_get_image_editor') ) {
				
				if ($debug_mode) {
					echo 'image editor exists (2)'."\n";
				}
				
				$editor = wp_get_image_editor($img_path);

				if ($debug_mode) { var_dump($editor); }

				if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) ) {
					$image = array (
						0 => $url,
						1 => $width,
						2 => $height
					);
					return $image;
				}

				$resized_file = $editor->save();

				if(!is_wp_error($resized_file)) {
					$resized_rel_path = str_replace( $upload_dir, '', $resized_file['path']);
					$img_url = $upload_url . $resized_rel_path;
				} else {
					return false;
				}
			} else {
				
				if ($debug_mode) {
					echo 'image editor does not exist (2)'."\n";
				}
				$image = array (
					0 => $url,
					1 => $width,
					2 => $height
				);
				return $image;
				
			}
		}
		
		if ($debug_mode) {
			echo 'return output'."\n";
		}

		//return the output
		if ($single) {
			//str return
			$image = $img_url;
		} else {
			//array return
			$image = array (
				0 => $img_url,
				1 => $dst_w,
				2 => $dst_h
			);
		}

		return $image;
	}
}
