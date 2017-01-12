<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	$curr_variable = get_option( 'wc_settings_prdctfltr_use_variable_images', 'no' );

	if ( $curr_variable == 'yes' ) {

		function prdctfltr_switch_thumbnails( $html, $post_ID, $post_thumbnail_id, $size, $attr ) {

			global $product;

			if ( is_object( $product ) && method_exists( $product, 'is_type' ) && $product->is_type( 'variable' ) ) {

				global $prdctfltr_global;

				$pf_activated = isset( $prdctfltr_global['active_filters'] ) ? $prdctfltr_global['active_filters'] : array();
				$pf_permalinks = isset( $prdctfltr_global['active_permalinks'] ) ? $prdctfltr_global['active_permalinks'] : array();

				$pf_activated = array_merge( $pf_activated, $pf_permalinks );

				if ( !empty( $pf_activated ) ) {
					$attrs = array();
					foreach( $pf_activated as $k => $v ){
						if ( substr( $k, 0, 3 ) == 'pa_' ) {
							$attrs = $attrs + array(
								$k => $v[0]
							);
						}
					}

					if ( count( $attrs ) > 0 ) {
						$curr_var = $product->get_available_variations();
						foreach( $curr_var as $key => $var ) {
							$curr_var_set[$key]['attributes'] = $var['attributes'];
							$curr_var_set[$key]['variation_id'] = $var['variation_id'];
						}
						$found = WC_Prdctfltr::prdctrfltr_search_array( $curr_var_set, $attrs );
					}
				}

			}

			if ( isset( $found[0] ) && $found[0]['variation_id'] && has_post_thumbnail( $found[0]['variation_id'] ) ) {
				$image = wp_get_attachment_image( get_post_thumbnail_id( $found[0]['variation_id'] ), $size, false, $attr );
				return $image;
			}
			else {
				return $html;
			}

		}
		add_action( 'post_thumbnail_html', 'prdctfltr_switch_thumbnails', 999, 5 );

	}

?>