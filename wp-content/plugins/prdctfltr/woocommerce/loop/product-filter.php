<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	WC_Prdctfltr::$settings['instance'] = null;

	do_action( 'prdctfltr_filter_hooks' );

	global $prdctfltr_global;

	if ( !defined( 'DOING_AJAX' ) && !isset( $prdctfltr_global['sc_init'] ) ) {
		if ( isset( $prdctfltr_global['widget_search'] ) ) {
			if ( is_page() && !is_shop() ) {
				if ( WC_Prdctfltr::prdctfltr_check_appearance() === false ) {
					return;
				}
			}
		}
		else {
			if ( WC_Prdctfltr::prdctfltr_check_appearance() === false ) {
				return;
			}
		}
	}

	if ( !isset( $prdctfltr_global['done_filters'] ) ) {
		WC_Prdctfltr::make_global( $_REQUEST, 'FALSE' );
	}

	WC_Prdctfltr::make_filter();

	$curr_elements = ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_active_filters'] !== NULL ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_active_filters'] : array() );

	if ( empty( $curr_elements ) ) {
		return;
	}

	$active_filters = array();
	$pf_n=0;
	$pf_r=0;
	foreach( $curr_elements as $el ) {

		$el_fil = false;

		if ( $el == 'cat' ) {
			$el_fil = 'product_cat';
		}
		else if ( $el == 'tag' ) {
			$el_fil = 'product_tag';
		}
		else if ( $el == 'char' ) {
			$el_fil = 'characteristics';
		}
		else if ( substr( $el, 0, 3 ) == 'pa_' ) {
			$el_fil = $el;
		}
		else if ( $el == 'advanced' ) {
			$el_fil = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_taxonomy'][$pf_n];
			$pf_n++;
		}
		else if ( $el == 'range' ) {
			$el_fil = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$pf_r];
			$pf_r++;
		}
		else if ( in_array( $el, array( 'sort', 'instock', 'price', 'per_page', 'search' ) ) ) {
			$el_fil = false;
		}

		if ( $el_fil !== false ) {
			$active_filters[$el_fil] = array();
		}

	}

	$default_args = array(
		'prdctfltr'				=> 'active',
		'wc_query'				=> 'product_query',
		'post_type'				=> 'product',
		'post_status'			=> 'publish',
		'posts_per_page'		=> apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
		'paged'					=> WC_Prdctfltr::$settings['instance']['paged'],
		'meta_query'			=> array(
			array(
				'key'			=> '_visibility',
				'value'			=> array( 'catalog', 'visible' ),
				'compare'		=> 'IN'
			)
		)
	);

	$curr_maxheight = ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_limit_max_height'] == 'yes' ? ' style="max-height:' . WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_max_height'] . 'px;"' : '' );

	$pf_activated = isset( $prdctfltr_global['pf_activated'] ) ? $prdctfltr_global['pf_activated'] : array();

	do_action( 'prdctfltr_filter_before', WC_Prdctfltr::$settings['instance'], $pf_activated );

	$prdctfltr_id = isset( $prdctfltr_global['unique_id'] ) ? $prdctfltr_global['unique_id'] : uniqid( 'prdctfltr-' );

	$prdctfltr_global['filter_js'][$prdctfltr_id] = array(
		'args' => $default_args,
		'atts' => isset( $prdctfltr_global['ajax_js'] ) ? $prdctfltr_global['ajax_js'] : array(),
		'atts_sc' => isset( $prdctfltr_global['ajax_atts'] ) ? $prdctfltr_global['ajax_atts'] : array(),
		'adds' => isset( $prdctfltr_global['ajax_adds'] ) ? $prdctfltr_global['ajax_adds'] : array(),
		'widget_search' => isset( $prdctfltr_global['widget_search'] ) ? 'yes' : 'no',
		'widget_options' => isset( $prdctfltr_global['widget_options'] ) ? $prdctfltr_global['widget_options'] : ''
	);

?>
	<div <?php WC_Prdctfltr::get_filter_tag_parameters(); ?> data-id="<?php echo $prdctfltr_id; ?>">
	<?php

		if ( in_array( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
			$curr_columns = 1;
		}
		else {
			$curr_mix_count = ( count( $curr_elements ) );
			$curr_columns = ( $curr_mix_count < WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_max_columns'] ? $curr_mix_count : WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_max_columns'] );
		}

		$curr_columns_class = ' prdctfltr_columns_' . $curr_columns;

		$pf_adoptive_active = false;
		switch ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_adoptive_mode'] ) {
			case 'always' :
				$pf_adoptive_active = true;
			break;
			case 'permalink' :
				if ( !empty( $prdctfltr_global['active_filters'] ) || !empty( $prdctfltr_global['active_permalinks'] ) ) {
					$pf_adoptive_active = true;
				}
			break;
			case 'filter' :
				if ( !empty( $prdctfltr_global['active_filters'] ) ) {
					$pf_adoptive_active = true;
				}
			break;
			default :
				$pf_adoptive_active = false;
			break;
		}

		if ( $pf_adoptive_active === true && WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && WC_Prdctfltr::$settings['instance']['total'] > 0 ) {

			$adpt_taxes = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_adoptive_depend'];
			$pf_products = array();

			if ( !empty( $adpt_taxes ) && is_array( $adpt_taxes ) ) {

				$adpt_go = false;
				foreach( $adpt_taxes as $adpt_key => $adpt_tax ) {
					if ( array_key_exists( $adpt_tax, $prdctfltr_global['active_filters'] ) ) {
						$adpt_go = true;
					}
					if ( array_key_exists( $adpt_tax, $prdctfltr_global['active_permalinks'] ) ) {
						$adpt_go = true;
					}
				}

				if ( $adpt_go === true ) {

					$adoptive_args = array(
						'post_type'				=> 'product',
						'post_status' 			=> 'publish',
						'fields'				=> 'ids',
						'posts_per_page' 		=> 10000000,
						'meta_query'			=> array(
							array(
								'key'			=> '_visibility',
								'value'			=> array( 'catalog', 'visible' ),
								'compare'		=> 'IN'
							)
						)
					);

					$tax_query = array();

					for ( $i = 0; $i < count( $adpt_taxes ); $i++ ) {

						if ( isset( $prdctfltr_global['active_filters'][$adpt_taxes[$i]] ) && taxonomy_exists( $adpt_taxes[$i] ) ) {
							$tax_query[] = array(
								'taxonomy' => $adpt_taxes[$i],
								'field' => 'slug',
								'terms' => $prdctfltr_global['active_filters'][$adpt_taxes[$i]]
							);
						}

						if ( isset( $prdctfltr_global['active_permalinks'][$adpt_taxes[$i]] ) && taxonomy_exists( $adpt_taxes[$i] ) ) {
							$tax_query[] = array(
								'taxonomy' => $adpt_taxes[$i],
								'field' => 'slug',
								'terms' => $prdctfltr_global['active_permalinks'][$adpt_taxes[$i]]
							);
						}

					}

					if ( !empty( $tax_query ) ) {
						$tax_query['relation'] = 'AND';
						$adoptive_args['tax_query'] = $tax_query;
					}

					$pf_help_products = new WP_Query( $adoptive_args );

					global $wpdb;
					$pf_products = $wpdb->get_results( $pf_help_products->request );

				}

			}
			else {

				$request = WC_Prdctfltr::$settings['instance']['request'];

				if ( !empty( $request ) && is_string( $request ) ) {

					$t_str = $request;

					$t_pos = strpos( $request, 'SQL_CALC_FOUND_ROWS' );
					if ( $t_pos !== false ) {
						$t_str = str_replace( 'SQL_CALC_FOUND_ROWS', '', $request );
					}

					$t_pos = strpos( $request, 'LIMIT' );
					if ( $t_pos !== false ) {
						$t_str = substr( $request, 0, $t_pos );
					}

					$t_str .= ' LIMIT 0,10000000 ';

					global $wpdb;
					$pf_products = $wpdb->get_results( $t_str );

				}

			}

			if ( !empty( $pf_products ) ) {

				$curr_in = array();
				foreach ( $pf_products as $p ) {
					if ( !isset( $p->ID ) ) {
						continue;
					}
					$curr_in[] = $p->ID;

				}

				if ( !empty( $curr_in ) && is_array( $curr_in ) ) {

					$adoptive_taxes = array();
					$mysql_adoptive_taxes = '';
					$pf_adoptive_taxes = get_object_taxonomies( 'product', 'names' );

					if ( !empty( $active_filters ) ) {
						foreach( $active_filters as $k24 => $v34 ) {
							$adoptive_taxes[] = $k24;
						}
						$mysql_adoptive_taxes = 'AND %3$s.taxonomy IN ("' . implode( '","', array_map( 'esc_sql', $adoptive_taxes ) ) . '")';
					}

					$output_terms = array();

					$pf_product_terms_query = '
						SELECT %4$s.slug, %3$s.parent, %3$s.taxonomy, COUNT(DISTINCT %1$s.ID) as count, SUM( %3$s.count ) as summi FROM %1$s
						INNER JOIN %2$s ON (%1$s.ID = %2$s.object_id)
						INNER JOIN %3$s ON (%2$s.term_taxonomy_id = %3$s.term_taxonomy_id )
						INNER JOIN %4$s ON (%3$s.term_id = %4$s.term_id )
						WHERE 1=1
						' . $mysql_adoptive_taxes . '
						AND %1$s.ID IN ("' . implode( '","', array_map( 'esc_sql', $curr_in ) ) . '")
						GROUP BY slug,taxonomy
					';

					$pf_product_terms = $wpdb->get_results( $wpdb->prepare( $pf_product_terms_query, $wpdb->posts, $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->terms ) );
					$pf_adpt_set = array();

					foreach ( $pf_product_terms as $p ) {

						if ( !isset( $output_terms[$p->taxonomy] ) ) {
							$output_terms[$p->taxonomy] = array();
						}

						if ( !array_key_exists( $p->slug, $output_terms[$p->taxonomy] ) ) {
							$output_terms[$p->taxonomy][$p->slug] = $p->count;
						}
						else {
							$output_terms[$p->taxonomy][$p->slug] = $p->count+(isset($output_terms[$p->taxonomy][$p->slug])?$output_terms[$p->taxonomy][$p->slug]:0);
						}

						$adpt_prnt = intval( $p->parent );
						if ( $adpt_prnt > 0 ) {
							while ( $adpt_prnt !== 0 ) {
								$adpt_prnt_term = get_term_by( 'id', $adpt_prnt, $p->taxonomy );
								$output_terms[$p->taxonomy][$adpt_prnt_term->slug] = $p->count+(isset($output_terms[$p->taxonomy][$adpt_prnt_term->slug])?$output_terms[$p->taxonomy][$adpt_prnt_term->slug]:0);
								$adpt_prnt = ( ( $adpt_prnt_val = intval( $adpt_prnt_term->parent ) ) > 0 ? $adpt_prnt_val : 0 );
							}
						}

					}

				}

			}

		}

		$q=0;$n=0;$y=0;$p=0;

		ob_start();

		foreach ( $curr_elements as $curr_el ) :

			$curr_fo = array();
			$customization = array();

			if ( !isset( $prdctfltr_global['widget_search'] ) && $q == $curr_columns && ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_style_mode'] == 'pf_mod_multirow' || WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_style_preset'] == 'pf_select' ) ) {
				$q = 0;
				echo '<div class="prdctfltr_clear"></div>';
			}

			switch ( $curr_el ) :

			case 'per_page' :

				$customization = WC_Prdctfltr::get_customization( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_perpage_term_customization'] );

			?>
				<div class="prdctfltr_filter prdctfltr_per_page<?php echo $customization['class']; ?>" data-filter="products_per_page">
					<input name="products_per_page" type="hidden"<?php echo ( isset( $pf_activated['products_per_page'] ) ? ' value="'.$pf_activated['products_per_page'].'"' : '' );?>>
					<?php
						WC_Prdctfltr::get_filter_title( 'products_per_page', __( 'Products Per Page', 'prdctfltr' ), 'perpage' );

						$curr_desc = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_perpage_description'] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_perpage_description'] : '';
						if ( $curr_desc != '' ) {
							printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
						}
					?>
					<div class="prdctfltr_add_scroll"<?php echo $curr_maxheight; ?>>
						<div class="prdctfltr_checkboxes">
					<?php

						$filter_customization = WC_Prdctfltr::get_filter_customization( 'per_page', WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_perpage_filter_customization'] );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {

							foreach( $filter_customization['settings'] as $v ) {
								$curr_perpage[$v['value']] = $v['text'];
							}

						}
						else {

							$curr_perpage_set = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_perpage_range'];
							$curr_perpage_limit = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_perpage_range_limit'];

							$curr_perpage = array();

							for ($i = 1; $i <= $curr_perpage_limit; $i++) {

								$curr_perpage[$curr_perpage_set*$i] = $curr_perpage_set*$i . ' ' . ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_perpage_label'] == '' ? __( 'Products', 'prdctfltr' ) : WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_perpage_label'] );

							}

						}

						foreach ( $curr_perpage as $id => $name ) {

							$checked = ( isset($pf_activated['products_per_page']) && $pf_activated['products_per_page'] == $id ? ' checked' : ' ' );

							if ( !empty( $customization['options'] ) ) {
								$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization['options'], $checked );
							}
							else {
								$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
							}

							printf( '<label%1$s>%2$s</label>', ( isset($pf_activated['products_per_page']) && $pf_activated['products_per_page'] == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );
						}
					?>
						</div>
					</div>
				</div>

			<?php break;

			case 'instock' :

				$catalog_instock = WC_Prdctfltr::catalog_instock();
				$customization = WC_Prdctfltr::get_customization( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_instock_term_customization'] );

			?>
				<div class="prdctfltr_filter prdctfltr_instock<?php echo $customization['class']; ?>" data-filter="instock_products">
					<input name="instock_products" type="hidden"<?php echo ( isset($pf_activated['instock_products'] ) ? ' value="'.$pf_activated['instock_products'].'"' : '' );?>>
					<?php

						WC_Prdctfltr::get_filter_title( 'instock_products', __( 'Product Availability', 'prdctfltr' ), 'instock' );

						$curr_desc = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_instock_description'] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_instock_description'] : '';
						if ( $curr_desc != '' ) {
							printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
						}
					?>
					<div class="prdctfltr_add_scroll"<?php echo $curr_maxheight; ?>>
						<div class="prdctfltr_checkboxes">
					<?php

						foreach ( $catalog_instock as $id => $name ) {

							$checked = ( isset($pf_activated['instock_products']) && $pf_activated['instock_products'] == $id ? ' checked' : ' ' );

							if ( !empty( $customization['options'] ) ) {
								$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization['options'], $checked );
							}
							else {
								$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
							}

							printf( '<label%1$s>%2$s</label>', ( isset($pf_activated['instock_products']) && $pf_activated['instock_products'] == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );

						}
					?>
						</div>
					</div>
				</div>

		<?php

			break;

			case 'sort' :

				$catalog_orderby = WC_Prdctfltr::catalog_ordering();
				$customization = WC_Prdctfltr::get_customization( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_orderby_term_customization'] );

			?>
				<div class="prdctfltr_filter prdctfltr_orderby<?php echo $customization['class']; ?>" data-filter="orderby">
					<input name="orderby" type="hidden"<?php echo ( isset($pf_activated['orderby'] ) ? ' value="'.$pf_activated['orderby'].'"' : '' );?>>
					<?php

						WC_Prdctfltr::get_filter_title( 'orderby', __( 'Sort by', 'prdctfltr' ), 'orderby' );

						$curr_desc = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_orderby_description'] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_orderby_description'] : '';
						if ( $curr_desc != '' ) {
							printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
						}
					?>
					<div class="prdctfltr_add_scroll"<?php echo $curr_maxheight; ?>>
						<div class="prdctfltr_checkboxes">
					<?php
						if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
							unset( $catalog_orderby['rating'] );
						}
						if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_orderby_none'] == 'yes' ) {
							unset( $catalog_orderby[''] );
						}

						foreach ( $catalog_orderby as $id => $name ) {

							$checked = ( isset($pf_activated['orderby']) && $pf_activated['orderby'] == $id ? ' checked' : ' ' );

							if ( !empty( $customization['options'] ) ) {
								$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization['options'], $checked );
							}
							else {
								$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
							}

							printf( '<label%1$s>%2$s</label>', ( isset($pf_activated['orderby']) && $pf_activated['orderby'] == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );

						}
					?>
						</div>
					</div>
				</div>

			<?php

			break;

			case 'price' :

				$customization = WC_Prdctfltr::get_customization( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_term_customization'] );

			?>
				<div class="prdctfltr_filter prdctfltr_byprice<?php echo $customization['class']; ?>"  data-filter="pf_byprice">
					<input name="min_price" type="hidden"<?php echo ( isset( $pf_activated['min_price'] ) ? ' value="' . $pf_activated['min_price'] . '"' : '' );?>>
					<input name="max_price" type="hidden"<?php echo ( isset( $pf_activated['max_price'] ) ? ' value="' . $pf_activated['max_price'] . '"' : '' );?>>
					<?php

						WC_Prdctfltr::get_filter_title( 'byprice', __( 'Price Range', 'prdctfltr' ), 'price' );

					$curr_desc = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_description'] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_description'] : '';
					if ( $curr_desc != '' ) {
						printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
					}

					$filter_customization = WC_Prdctfltr::get_filter_customization( 'price', WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_filter_customization'] );

					$catalog_ready_price = array();
					$curr_price = ( isset($pf_activated['min_price']) ? $pf_activated['min_price'].'-'.( isset($pf_activated['max_price']) ? $pf_activated['max_price'] : '' ) : '' );

					if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {

						if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_none'] == 'no' ) {
							$catalog_ready_price = array(
								'-' => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) )
							);
						}

						foreach( $filter_customization['settings'] as $k => $v ) {
							$pf_custom_ranges = explode( '-', $k );
							if ( $pf_custom_ranges[0] !== '' ) {
								$pf_custom_ranges[0] = strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $pf_custom_ranges[0] ) ) );
							}
							if ( $pf_custom_ranges[1] !== '' ) {
								$pf_custom_ranges[1] = strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $pf_custom_ranges[1] ) ) );
							}
							$add_num = implode( ' - ', $pf_custom_ranges );
							$catalog_ready_price[$k] = ( $add_num !== '' ? $add_num : '' );
						}

					}
					else {

						$curr_prices = array();
						$curr_prices_currency = array();

						$curr_price_set = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_range'];
						$curr_price_add = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_range_add'];
						$curr_price_limit = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_range_limit'];

						if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_price_none'] == 'no' ) {
							$catalog_ready_price = array(
								'-' => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) )
							);
						}

						for ( $i = 0; $i < $curr_price_limit; $i++ ) {

							if ( $i == 0 ) {
								$min_price = 0;
								$max_price = $curr_price_set;
							}
							else {
								$min_price = $curr_price_set+($i-1)*$curr_price_add;
								$max_price = $curr_price_set+$i*$curr_price_add;
							}

							$curr_prices[$i] = $min_price . '-' . ( ($i+1) == $curr_price_limit ? '' : $max_price );

							$curr_prices_currency[$i] = strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $min_price ) ) ) . ( $i+1 == $curr_price_limit ? '+' : ' - ' . strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $max_price ) ) ) );

							$catalog_ready_price = $catalog_ready_price + array(
								$curr_prices[$i] => $curr_prices_currency[$i]
							);

						}

					}

					$catalog_price = apply_filters( 'prdctfltr_catalog_price', $catalog_ready_price );

				?>
				<div class="prdctfltr_add_scroll"<?php echo $curr_maxheight; ?>>
					<div class="prdctfltr_checkboxes">
					<?php
						foreach ( $catalog_price as $id => $name ) {
							$checked = ( $curr_price == $id ? ' checked' : ' ' );

							if ( !empty( $customization['options'] ) ) {
								$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization['options'], $checked );
							}
							else {
								$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
							}

							printf( '<label%1$s>%2$s</label>', ( $curr_price == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );
						}
					?>
						</div>
					</div>
				</div>

			<?php break;

			case 'range' :

				foreach ( WC_Prdctfltr::check_range_settings() as $k => $v ) {
					if ( !isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters'][$k][$p] ) ) {
						WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters'][$k][$p] = $v;
					}
				}
				$adpt_rng = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_adoptive'][$p] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_adoptive'][$p] : 'no';
				$attr = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$p];

				if ( $attr !== 'price' && WC_Prdctfltr::$settings['instance']['total'] !== 0 && $adpt_rng == 'yes' && ( isset( $output_terms ) && ( !isset( $output_terms[$attr] ) || isset( $output_terms[$attr] ) && empty( $output_terms[$attr]) ) === true ) ) {
					continue;
				}

	?>
				<div class="prdctfltr_filter prdctfltr_range prdctfltr_<?php echo $attr; ?> <?php echo 'pf_rngstyle_' . WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_style'][$p]; ?>" data-filter="rng_<?php echo $attr; ?>">
					<input name="rng_min_<?php echo $attr; ?>" type="hidden"<?php echo ( isset( $pf_activated['rng_min_' . $attr] ) ? ' value="'.$pf_activated['rng_min_' . $attr].'"' : '' );?>>
					<input name="rng_max_<?php echo $attr; ?>" type="hidden"<?php echo ( isset( $pf_activated['rng_max_' . $attr] ) ? ' value="'.$pf_activated['rng_max_' . $attr].'"' : '' );?>>
				<?php
					if ( $attr !== 'price' ) {
					?>
						<input name="rng_orderby_<?php echo $attr; ?>" type="hidden" value="<?php echo WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p]; ?>">
						<input name="rng_order_<?php echo $attr; ?>" type="hidden" value="<?php echo !empty( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] : ''; ?>">
					<?php
					}
					?>
					<?php

						WC_Prdctfltr::get_dynamic_filter_title( 'range', $attr, $p, $attr == 'price' ? __( 'Price Range', 'prdctfltr' ) : wc_attribute_label( $attr ) );

						$curr_desc = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_description'][$p] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_description'][$p] : '';
						if ( $curr_desc != '' ) {
							printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
						}
					?>
					<div class="prdctfltr_add_scroll">
						<div class="prdctfltr_checkboxes">
					<?php

						$add_rng_js = '';

						$curr_rng_id = uniqid( 'prdctfltr_rng_' ) . $p;
						$prdctfltr_global['ranges'][$curr_rng_id] = array();
						$prdctfltr_global['ranges'][$curr_rng_id]['type'] = 'double';
						$prdctfltr_global['ranges'][$curr_rng_id]['min_interval'] = 1;

						if ( !in_array(WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$p], array( 'price' ) ) ) {

							$curr_include = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_include'][$p];

							$curr_include = WC_Prdctfltr::prdctfltr_wpml_translate_terms( $curr_include, $attr );

							if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p] == 'number' ) {
								$attr_args = array(
									'hide_empty' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_hideempty'],
									'orderby' => 'slug'
								);
								$curr_attributes = WC_Prdctfltr::prdctfltr_get_terms( $attr, $attr_args );
								$pf_sort_args = array(
									'order' => ( isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] : 'ASC' )
								);
								$curr_attributes = WC_Prdctfltr::prdctfltr_sort_terms_naturally( $curr_attributes, $pf_sort_args );
							}
							else {
								$attr_args = array(
									'hide_empty' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_hideempty'],
									'orderby' => ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p] !== '' ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p] : 'name' ),
									'order' => ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] !== '' ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] : 'ASC' )
								);
								$curr_attributes = WC_Prdctfltr::prdctfltr_get_terms( $attr, $attr_args );
							}

							$prdctfltr_global['ranges'][$curr_rng_id]['values'] = array();

							$c=0;

							foreach ( $curr_attributes as $attribute ) {

								if ( !empty( $curr_include ) && !in_array( $attribute->slug, $curr_include ) ) {
									continue;
								}

								if ( $adpt_rng == 'yes' && isset( $output_terms[$attr] ) && count( $output_terms[$attr] ) !== 1 ) {
									if ( !isset( $output_terms[$attr][$attribute->slug] ) ) {
										continue;
									}
								}

								if ( isset( $pf_activated['rng_min_' . $attr] ) && $pf_activated['rng_min_' . $attr] == $attribute->slug ) {
									$prdctfltr_global['ranges'][$curr_rng_id]['from'] = $c;
								}

								if ( isset( $pf_activated['rng_max_' . $attr] ) && $pf_activated['rng_max_' . $attr] == $attribute->slug ) {
									$prdctfltr_global['ranges'][$curr_rng_id]['to'] = $c;
								}

								$prdctfltr_global['ranges'][$curr_rng_id]['values'][] = '<span class=\'pf_range_val\'>' . $attribute->slug . '</span>' . $attribute->name;

								$c++;
							}

							$prdctfltr_global['ranges'][$curr_rng_id]['decorate_both'] = false;
							$prdctfltr_global['ranges'][$curr_rng_id]['values_separator'] = ' &rarr; ';

							if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p] !== '' ) {
								$add_rng_js = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p];
							}

						}
						else {

							$prices = WC_Prdctfltr::get_filtered_price( $adpt_rng );
							$pf_curr_min = floor( $prices->min_price );
							$pf_curr_max = ceil( $prices->max_price );

							if ( $pf_curr_min == $pf_curr_max ) {
								$pf_curr_min = $pf_curr_min-5;
								$pf_curr_max = $pf_curr_max+5;
							}

							$pf_curr_min = WC_Prdctfltr::price_to_float( strip_tags( wc_price( $pf_curr_min ) ) );
							$pf_curr_max = WC_Prdctfltr::price_to_float( strip_tags( wc_price( $pf_curr_max ) ) );

							$prdctfltr_global['ranges'][$curr_rng_id]['min'] = apply_filters( 'wcml_raw_price_amount', $pf_curr_min );
							$prdctfltr_global['ranges'][$curr_rng_id]['max'] = apply_filters( 'wcml_raw_price_amount', $pf_curr_max );

							if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p] !== '' ) {
								$add_rng_js = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p];
							}

							$currency_pos = get_option( 'woocommerce_currency_pos', 'left' );
							$currency = get_woocommerce_currency_symbol();

							switch ( $currency_pos ) {
								case 'right' :
								case 'right_space' :
									$prdctfltr_global['ranges'][$curr_rng_id]['postfix'] = $currency;
								break;
								case 'left_space' :
								case 'left' :
								default :
									$prdctfltr_global['ranges'][$curr_rng_id]['prefix'] = $currency;
								break;
							}

							if ( isset( $pf_activated['rng_min_' . $attr] ) ) {
								$prdctfltr_global['ranges'][$curr_rng_id]['from'] = apply_filters( 'wcml_raw_price_amount', floor( WC_Prdctfltr::price_to_float( strip_tags( wc_price( $pf_activated['rng_min_' . $attr] ) ) ) ) );
							}

							if ( isset( $pf_activated['rng_max_' . $attr] ) ) {
								$prdctfltr_global['ranges'][$curr_rng_id]['to'] = apply_filters( 'wcml_raw_price_amount', ceil( WC_Prdctfltr::price_to_float( strip_tags( wc_price($pf_activated['rng_max_' . $attr] ) ) ) ) );
							}

						}

						if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_grid'][$p] == 'yes' ) {
							$prdctfltr_global['ranges'][$curr_rng_id]['grid'] = true;
						}

						$pf_divide = apply_filters( 'wcml_raw_price_amount', WC_Prdctfltr::price_to_float( strip_tags( wc_price( 100 ) ) ) );
						$pf_divide_checked = $pf_divide > 0 ? $pf_divide : 100;
						$prdctfltr_global['price_ratio'] = 100/$pf_divide_checked;

						if ( $add_rng_js !== '' ) {

							$rng_set = explode( PHP_EOL, stripslashes( $add_rng_js ) );

							foreach( $rng_set as $rv ) {
								if ( $rv == '' ) {
									continue;
								}
								if ( substr( $rv, -1 ) == ',' ) {
									$rv = substr( $rv, 0, -1 );
								}
								$rng_pieces = explode( ':', $rv, 2 );
								if ( count( $rng_pieces ) == 2 ) {
									if ( substr( $rng_pieces[1], 0, 1 ) == "'" || substr( $rng_pieces[1], 0, 1 ) == '"' ) {
										$rng_pieces[1] = substr( $rng_pieces[1], 1 );
									}
									if ( substr( $rng_pieces[1], -1 ) == "'" || substr( $rng_pieces[1], -1 ) == '"' ) {
										$rng_pieces[1] = substr( $rng_pieces[1], 0, -1 );
									}
									$prdctfltr_global['ranges'][$curr_rng_id][$rng_pieces[0]] = $rng_pieces[1];
								}
							}

						}

						printf( '<input id="%1$s" class="pf_rng_%2$s" />', $curr_rng_id, WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$p] );
					?>
						</div>
					</div>
				</div>
				<?php

				$p++;
			break;

			case 'search' :

				$pf_srch = ( isset( $prdctfltr_global['sc_init'] ) && $prdctfltr_global['sc_init'] === true ? 'search_products' : 's' );
			?>
				<div class="prdctfltr_filter prdctfltr_search" data-filter="pf_search">
					<?php

						WC_Prdctfltr::get_filter_title( isset( $prdctfltr_global['sc_init'] ) ? 'search_products' : 's', __( 'Search', 'prdctfltr' ), 'search' );

						$curr_desc = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_search_description'] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_search_description'] : '';
						if ( $curr_desc != '' ) {
							printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
						}
					?>
					<div class="prdctfltr_add_scroll">
						<div class="prdctfltr_checkboxes">
					<?php
						$pf_placeholder = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_search_placeholder'] != '' ? esc_attr( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_search_placeholder'] ) : esc_attr( __( 'Product keywords', 'prdctfltr' ) );
						$curr_insert = '<input class="pf_search" name="' . $pf_srch .'" type="text"' . ( isset($pf_activated['s'] ) ? ' value="' . $pf_activated['s'] . '"' : '' ) . ' placeholder="' . $pf_placeholder . '">';
						printf( '<label%1$s>%2$s<a href="#" class="pf_search_trigger"></a></label>', ( isset($pf_activated['s'] ) ? ' class="prdctfltr_active"' : '' ), $curr_insert );
					?>
						</div>
					</div>
				</div>

		<?php
				$active_filters['s'] = get_search_query();

			break;

			case 'meta' :

				foreach ( WC_Prdctfltr::check_meta_settings() as $ck => $cv ) {
					if ( !isset(WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters'][$ck][$y]) ) {
						WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters'][$ck][$y] = $cv;
					}
				}

				$curr_meta = array();
				$checked_customization = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_term_customization'][$y] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_term_customization'][$y] : '' ;
				$checked_terms = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_filter_customization'][$y] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_filter_customization'][$y] : '' ;

				$curr_fo['settings']['title'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_title'][$y];
				$curr_fo['settings']['description'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_description'][$y];
				$curr_fo['settings']['compare'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_compare'][$y];
				$curr_fo['settings']['type'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_type'][$y];
				$curr_fo['settings']['limit'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_limit'][$y];
				$curr_fo['settings']['multi'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_multiselect'][$y];
				$curr_fo['settings']['relation'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_relation'][$y];
				$curr_fo['settings']['key'] = WC_Prdctfltr::build_meta_key( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_key'][$y], $curr_fo['settings']['compare'], $curr_fo['settings']['type'] );
				$curr_fo['settings']['none'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_none'][$y];;
				$curr_fo['settings']['customization'] = $checked_customization;
				$curr_fo['settings']['terms'] = $checked_terms;

				$curr_term_multi = $curr_fo['settings']['multi'] == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single';
				$curr_term_relation = $curr_fo['settings']['relation'] == 'AND' ? ' prdctfltr_merge_terms' : '';
				$curr_limit = intval( $curr_fo['settings']['limit'] );

				$customization = WC_Prdctfltr::get_customization( $checked_customization );

			?>
				<div class="prdctfltr_filter prdctfltr_meta<?php echo $customization['class']; ?><?php echo $curr_term_multi; ?><?php echo $curr_term_relation; ?>" data-filter="<?php echo $curr_fo['settings']['key']; ?>" data-limit="<?php echo $curr_limit !== 0 ? $curr_limit-1 : '0';?>">
					<input name="<?php echo $curr_fo['settings']['key']; ?>" type="hidden"<?php echo ( isset( $prdctfltr_global['meta_data'][$curr_fo['settings']['key']] ) ? ' value="' . $prdctfltr_global['meta_data'][$curr_fo['settings']['key']] . '"' : '' );?>>
					<?php
						WC_Prdctfltr::get_filter_title( $curr_fo['settings']['key'], $curr_fo['settings']['title'] == '' ? __( 'Product Meta', 'prdctfltr' ) : $curr_fo['settings']['title'], 'meta' );

						$curr_desc = isset( $curr_fo['settings']['description'] ) ? $curr_fo['settings']['description'] : '';
						if ( $curr_desc != '' ) {
							printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
						}
					?>
					<div class="prdctfltr_add_scroll"<?php echo $curr_maxheight; ?>>
						<div class="prdctfltr_checkboxes">
					<?php

						if ( $curr_fo['settings']['none'] == 'no' ) {
							if ( !empty( $customization['options'] ) ) {
								$curr_blank_element = WC_Prdctfltr::get_customized_term( '', apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) ), false, $customization['options'] );
							}
							else {
								$curr_blank_element = apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) );
							}

							printf('<label class="prdctfltr_ft_none"><input type="checkbox" value="" /><span>%1$s</span></label>', $curr_blank_element );
						}

						$filter_customization = WC_Prdctfltr::get_filter_customization( 'meta', $checked_terms );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {

							foreach( $filter_customization['settings'] as $v ) {
								$curr_meta[$v['value']] = $v['text'];
							}

						}
						else {

							_e( 'Error! No terms!', 'prdctfltr' );
							$curr_meta = array();

						}

						foreach ( $curr_meta as $id => $name ) {

							$checked = ( isset( $pf_activated[$curr_fo['settings']['key']] ) && in_array( $id,  $pf_activated[$curr_fo['settings']['key']] ) ? ' checked' : ' ' );

							if ( !empty( $customization['options'] ) ) {
								$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization['options'], $checked );
							}
							else {
								$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
							}

							printf( '<label%1$s><span>%2$s</span></label>', ( isset($pf_activated[$curr_fo['settings']['key']]) && in_array( $id,  $pf_activated[$curr_fo['settings']['key']] ) ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );
						}
					?>
					</div>
				</div>
			</div>
			<?php
				$y++;
			break;
			case 'vendor' :

				$catalog_vendor = array();

				$curr_include = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_include_vendor'];
				if ( !empty( $curr_include ) && is_array( $curr_include ) ) {
					foreach ( $curr_include as $pf_vendor ) {
						$pf_user = get_userdata( intval( $pf_vendor ) );
						$catalog_vendor[intval( $pf_vendor )] = $pf_user->display_name;
					}
				}
				else {
					$pf_vendors = get_users( 'orderby=nicename' );
					foreach ( $pf_vendors as $pf_vendor ) {
						$curr_include[] = $pf_vendor->ID;
						$catalog_vendor[$pf_vendor->ID] = $pf_vendor->display_name;
					}
				}

				$customization = WC_Prdctfltr::get_customization( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_vendor_term_customization'] );

			?>
				<div class="prdctfltr_filter prdctfltr_vendor<?php echo $customization['class']; ?>" data-filter="vendor">
					<input name="vendor" type="hidden"<?php echo ( isset($pf_activated['vendor'] ) ? ' value="'.$pf_activated['vendor'].'"' : '' );?>>
					<?php

						WC_Prdctfltr::get_filter_title( 'vendor', __( 'Vendor', 'prdctfltr' ), 'vendor' );

						$curr_desc = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_vendor_description'] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_vendor_description'] : '';
						if ( $curr_desc != '' ) {
							printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
						}
					?>
					<div class="prdctfltr_add_scroll"<?php echo $curr_maxheight; ?>>
						<div class="prdctfltr_checkboxes">
					<?php

						foreach ( $catalog_vendor as $id => $name ) {

							$checked = ( isset($pf_activated['vendor']) && $pf_activated['vendor'] == $id ? ' checked' : ' ' );

							if ( !empty( $customization['options'] ) ) {
								$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization['options'], $checked );
							}
							else {
								$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
							}

							printf( '<label%1$s>%2$s</label>', ( isset($pf_activated['vendor']) && $pf_activated['vendor'] == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );

						}
					?>
						</div>
					</div>
				</div>

		<?php

				$active_filters['vendor'] = $curr_include;

			break;

			default :

				$mod = '';
				if ( $curr_el == 'cat' ) {

					$curr_fo['filter'] = 'product_cat';
					$mod = 'regular';
				}
				else if ( $curr_el == 'tag' ) {

					$curr_fo['filter'] = 'product_tag';
					$mod = 'regular';
				}
				else if ( $curr_el == 'char' ) {

					$curr_fo['filter'] = 'characteristics';
					$mod = 'regular';
				}
				else if ( substr( $curr_el, 0, 3) == 'pa_' ) {

					$curr_fo['filter'] = $curr_el;
					$mod = 'attribute';
				}
				else if ( $curr_el == 'advanced' ) {

					$curr_fo['filter'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_taxonomy'][$n];
					$mod = 'advanced';
				}

				if ( empty( $mod ) ) {
					break;
				}

				if ( in_array( $mod, array( 'regular', 'attribute' ) ) ) {
					$curr_fo['settings']['title'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_title'];
					$curr_fo['settings']['description'] = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_description'] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_description'] : '';

					if ( $mod == 'attribute' ) {
						$curr_fo['settings']['include'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_include_' . $curr_el];
					}
					else {
						$curr_fo['settings']['include'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_include_' . $curr_el . 's'];
					}

					$checked_customization = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) .'_term_customization'] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) .'_term_customization'] : '' ;

					$curr_fo['settings']['orderby'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_orderby'];
					$curr_fo['settings']['order'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_order'];
					$curr_fo['settings']['limit'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_limit'];
					$curr_fo['settings']['multi'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) . '_multi'];
					$curr_fo['settings']['relation'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_relation'];
					$curr_fo['settings']['adoptive'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) . '_adoptive'];
					$curr_fo['settings']['none'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) . '_none'];
					$curr_fo['settings']['customization'] = $checked_customization;

					if ( $mod == 'attribute' || $curr_el == 'cat' ) {
						$curr_fo['settings']['hierarchy'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . $curr_el . '_hierarchy'];
						$curr_fo['settings']['hierarchy_mode'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . $curr_el . '_hierarchy_mode'];
						$curr_fo['settings']['mode'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . $curr_el . '_mode'];
					}
					if ( $mod == 'attribute' ) {
						$curr_fo['settings']['style'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_' . $curr_el];
					}
				}
				else {


					foreach ( WC_Prdctfltr::check_advanced_settings() as $ck => $cv ) {
						if ( !isset(WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters'][$ck][$n]) ) {
							WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters'][$ck][$n] = $cv;
						}
					}

					$checked_customization = isset( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_term_customization'][$n] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_term_customization'][$n] : '' ;

					$curr_fo['settings']['title'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_title'][$n];
					$curr_fo['settings']['description'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_description'][$n];
					$curr_fo['settings']['include'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_include'][$n];
					$curr_fo['settings']['orderby'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_orderby'][$n];
					$curr_fo['settings']['order'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_order'][$n];
					$curr_fo['settings']['limit'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_limit'][$n];
					$curr_fo['settings']['multi'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_multiselect'][$n];
					$curr_fo['settings']['relation'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_relation'][$n];
					$curr_fo['settings']['adoptive'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_adoptive'][$n];
					$curr_fo['settings']['none'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_none'][$n];

					$curr_fo['settings']['hierarchy'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_hierarchy'][$n];
					$curr_fo['settings']['hierarchy_mode'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_hierarchy_mode'][$n];
					$curr_fo['settings']['mode'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_mode'][$n];
					$curr_fo['settings']['style'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_style'][$n];
					$curr_fo['settings']['customization'] = $checked_customization;

				}

				if ( WC_Prdctfltr::$settings['instance']['total'] !== 0 && $curr_fo['settings']['adoptive'] == 'yes' && WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_adoptive_style'] == 'pf_adptv_default' && ( isset( $output_terms ) && ( !isset( $output_terms[$curr_fo['filter']] ) || isset( $output_terms[$curr_fo['filter']] ) && empty( $output_terms[$curr_fo['filter']]) ) === true ) ) {
					continue;
				}

				if ( $curr_fo['settings']['orderby'] == 'number' ) {
					$curr_term_args = array(
						'hide_empty' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_hideempty'],
						'orderby' => 'slug'
					);
					$pf_terms = WC_Prdctfltr::prdctfltr_get_terms( $curr_fo['filter'], $curr_term_args );
					$pf_sort_args = array(
						'order' => ( isset( $curr_fo['settings']['order'] ) ? $curr_fo['settings']['order'] : 'ASC' )
					);
					$pf_terms = WC_Prdctfltr::prdctfltr_sort_terms_naturally( $pf_terms, $pf_sort_args );
				}
				else {
					$curr_term_args = array(
						'hide_empty' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_hideempty'],
						'orderby' => ( $curr_fo['settings']['orderby'] !== '' ? $curr_fo['settings']['orderby'] : 'name' ),
						'order' => ( $curr_fo['settings']['order']!== '' ? $curr_fo['settings']['order'] : 'ASC' )
					);
					$pf_terms = WC_Prdctfltr::prdctfltr_get_terms( $curr_fo['filter'], $curr_term_args );
				}

				if ( !empty( $pf_terms ) && !is_wp_error( $pf_terms ) ) {

					$curr_cat_selected = array();

					if ( isset( $pf_activated[$curr_fo['filter']] ) ) {
						$curr_cat_selected = array_map( 'strtolower', $pf_activated[$curr_fo['filter']] );
					}

					if ( !isset( $prdctfltr_global['sc_init'] ) && empty( $curr_cat_selected ) && isset( $prdctfltr_global['active_permalinks'][$curr_fo['filter']] ) ) {
						$curr_cat_selected = array_map( 'strtolower', $prdctfltr_global['active_permalinks'][$curr_fo['filter']] );
					}

					if ( !empty( $curr_cat_selected ) ) {
						$curr_cat_selected = array_map( 'strtolower', $curr_cat_selected );
					}

					$curr_term_subonly = '';
					if ( isset( $curr_fo['settings']['mode'] ) && in_array( $curr_fo['settings']['mode'], array( 'subonly', 'subcategories' ) ) ) {
						$curr_term_subonly = ' prdctfltr_subonly';
					}

					$curr_include = array_map( 'strtolower', $curr_fo['settings']['include'] );
					if ( !empty( $curr_include ) ) {
						$curr_include = array_map( 'strtolower', $curr_include );
					}
					else {
						foreach ( $pf_terms as $term ) {
							$curr_include[] = strtolower( $term->slug );
						}
					}

					$curr_include = WC_Prdctfltr::prdctfltr_wpml_translate_terms( $curr_include, $curr_fo['filter'] );

					$pf_hierarchy_class = '';
					if ( isset( $curr_fo['settings']['hierarchy'] ) && $curr_fo['settings']['hierarchy'] == 'yes' ) {
						$pf_terms_sorted = array();
						WC_Prdctfltr::prdctfltr_sort_terms_hierarchicaly( $pf_terms, $pf_terms_sorted );
						$pf_terms = $pf_terms_sorted;
						$pf_hierarchy_class = ' prdctfltr_hierarchy';
					}

					$customization = WC_Prdctfltr::get_customization( $curr_fo['settings']['customization'] );

					if ( isset( $curr_fo['settings']['hierarchy'] ) && $curr_fo['settings']['hierarchy'] == 'yes' ) {
						if ( isset( $customization['style'] ) && $customization['style'] !== 'select' ) {
							$customization = array(
								'options' => array(),
								'class' => 'prdctfltr_text'
							);
						}
					}

					$curr_term_multi = $curr_fo['settings']['multi'] == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single';
					$curr_term_adoptive = $curr_fo['settings']['adoptive'] == 'yes' ? ' prdctfltr_adoptive' : '';
					$curr_term_relation = $curr_fo['settings']['relation'] == 'AND' ? ' prdctfltr_merge_terms' : '';
					$curr_term_expand = isset( $curr_fo['settings']['hierarchy_mode'] ) && $curr_fo['settings']['hierarchy_mode'] == 'yes' ? ' prdctfltr_expand_parents' : '';
					$curr_limit = intval( $curr_fo['settings']['limit'] );

					$tax_val = '';
					$tax_val = isset( $prdctfltr_global['taxonomies_data'][$curr_fo['filter'].'_string'] ) ? ' value="' . esc_attr( $prdctfltr_global['taxonomies_data'][$curr_fo['filter'].'_string'] ) . '"' : '';
					if ( $tax_val == '' && !empty( $curr_cat_selected ) ) {
						$tax_val = isset( $prdctfltr_global['permalinks_data'][$curr_fo['filter'].'_string'] ) ? ' value="' . esc_attr( $prdctfltr_global['permalinks_data'][$curr_fo['filter'].'_string'] ) . '"' : '';
					}

				?>
					<div class="prdctfltr_filter prdctfltr_attributes prdctfltr_<?php echo $curr_el; ?><?php echo $curr_term_multi; ?><?php echo $customization['class']; ?><?php echo $curr_term_adoptive; ?><?php echo $curr_term_relation; ?><?php echo $pf_hierarchy_class; ?><?php echo $curr_term_expand; ?><?php echo $curr_term_subonly; ?>" data-filter="<?php echo $curr_fo['filter']; ?>" data-limit="<?php echo $curr_limit !== 0 ? $curr_limit-1 : '0';?>">
					<?php
						$termAddParent = '';
						if ( !empty( $curr_cat_selected ) && isset( $curr_fo['settings']['hierarchy'] ) && $curr_fo['settings']['hierarchy'] == 'yes' ) {

							foreach( $curr_cat_selected as $tax_val_term ) {

								if ( term_exists( $tax_val_term, $curr_fo['filter'] ) !== null ) {
									$curr_term = get_term_by( 'slug', $tax_val_term, $curr_fo['filter'] );
									$pf_term_parent[] = $curr_term->parent;
								}

							}

							$doNotTerm = null;
							if ( !empty( $pf_term_parent ) ) {
								$firstValueTerm = current( $pf_term_parent );
								foreach ( $pf_term_parent as $valTerm ) {
									if ( $firstValueTerm !== $valTerm ) {
										$doNotTerm = true;
									}
								}
								if ( !isset( $doNotTerm ) && $pf_term_parent[0] !== 0 ) {
									$currParent = get_term_by( 'id', $pf_term_parent[0], $curr_fo['filter'] );
									$termAddParent = ' data-parent="' . $currParent->slug . '"';
								}
							}
						}
					?>
						<input name="<?php echo $curr_fo['filter']; ?>" type="hidden"<?php echo ( !empty( $curr_cat_selected ) ? $tax_val : '' ) . $termAddParent; ?> />
						<?php
							if ( isset($prdctfltr_global['widget_search']) ) {
								$pf_before_title = $before_title . '<span class="prdctfltr_widget_title">';
								$pf_after_title = '</span>' . $after_title;
							}
							else {
								$pf_before_title = '<span class="prdctfltr_regular_title">';
								$pf_after_title = '</span>';
							}

							echo $pf_before_title;

							$do_attr_title = null;
							if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_disable_showresults'] !== 'no' ) {
								$do_attr_title = true;
							}
							if ( !isset( $pf_activated[$curr_fo['filter']] ) ) {
								$do_attr_title = true;
							}
							if ( !isset( WC_Prdctfltr::$settings['widget'] ) ) {

								if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_disable_bar'] == 'no' ) {
									if ( !in_array( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
										$do_attr_title = true;
									}
								}

							}
							if ( isset( $prdctfltr_global['sc_init'] ) && isset( $prdctfltr_global['sc_query'][$curr_fo['filter']] ) && $pf_activated[$curr_fo['filter']] == $prdctfltr_global['sc_query'][$curr_fo['filter']] ) {
								$do_attr_title = true;
							}

							if ( !isset( $do_attr_title ) ) {

								if ( !empty( $curr_cat_selected ) && array_intersect( $curr_cat_selected, $curr_include ) ) {

									$pf_attr_title = '';
									$pf_attr_slug = '';

									$pf_i=0;
									$pf_attr_active = false;
									$pf_attr_parent = array();

									foreach( $curr_cat_selected as $selected ) {
										if ( !in_array( $selected, $curr_include ) ) {
											continue;
										}

										if ( term_exists( $selected, $curr_fo['filter'] ) !== null ) {
											$curr_term = get_term_by( 'slug', $selected, $curr_fo['filter'] );

											$pf_attr_title .= ( $pf_i !== 0 ? ', ' : '' ) . $curr_term->name;
											$pf_attr_slug .= ( $pf_i !== 0 ? ',' : '' ) . $curr_term->slug;
											$pf_attr_parent[] = $curr_term->parent;

											$pf_i++;
											$pf_attr_active = true;
										}

									}
									if ( !empty( $pf_attr_parent ) ) {
										$firstValue = current( $pf_attr_parent );
										foreach ( $pf_attr_parent as $val ) {
											if ( $firstValue !== $val ) {
												$doNot = true;
											}
										}
										if ( !isset( $doNot ) && $pf_attr_parent[0] !== 0 ) {
											$curr_parent = get_term_by( 'id', $pf_attr_parent[0], $curr_fo['filter'] );
										}
									}

									$pf_attr_title = '<a href="#" class="prdctfltr_title_remove" data-slug="' . ( isset( $curr_parent ) ? $curr_parent->slug . '>' : '' ) . $pf_attr_slug . '" data-key="' . ( $curr_fo['filter'] == 'characteristics' ? 'char' : $curr_fo['filter'] ) . '"><i class="prdctfltr-delete"></i></a> <span class="prdctfltr_selected_title">' . $pf_attr_title . '</span>';

									if ( isset( $pf_attr_active ) ) {
										echo '<span class="prdctfltr_title_selected">' . $pf_attr_title . '<span class="prdctfltr_title_selected_separator"> / </span>' . '</span>';
									}

								}

							}

							if ( $curr_fo['settings']['title'] != '' ) {
								echo $curr_fo['settings']['title'];
							}
							else {
								if ( substr( $curr_fo['filter'], 0, 3 ) == 'pa_' ) {
									echo wc_attribute_label( $curr_fo['filter'] );
								}
								else {
									if ( $curr_fo['filter'] == 'product_cat' ) {
										_e( 'Categories', 'prdctfltr' );
									}
									else if ( $curr_fo['filter'] == 'product_tag') {
										_e( 'Tags', 'prdctfltr' );
									}
									else if ( $curr_fo['filter'] == 'characteristics' ) {
										_e( 'Characteristics', 'prdctfltr' );
									}
									else {
										$curr_term = get_taxonomy( $curr_fo['filter'] );
										echo $curr_term->label;
									}
								}
							}

						?>
						<i class="prdctfltr-down"></i>
						<?php echo $pf_after_title; ?>
						<?php
							$curr_desc = isset( $curr_fo['settings']['description'] ) ? $curr_fo['settings']['description'] : '';
							if ( $curr_desc != '' ) {
								printf( '<div class="prdctfltr_description">%1$s</div>', $curr_desc );
							}
						?>
						<div class="prdctfltr_add_scroll"<?php echo $curr_maxheight; ?>>
							<div class="prdctfltr_checkboxes">
						<?php

							if ( $curr_fo['settings']['none'] == 'no' ) {
								if ( !empty( $customization['options'] ) ) {
									$curr_blank_element = WC_Prdctfltr::get_customized_term( '', apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) ), false, $customization['options'] );
								}
								else {
									$curr_blank_element = apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) );
								}

								printf('<label class="prdctfltr_ft_none"><input type="checkbox" value="" /><span>%1$s</span></label>', $curr_blank_element );
							}

							$active_filters[$curr_fo['filter']] = array_merge( $active_filters[$curr_fo['filter']], $curr_include );

							WC_Prdctfltr::get_taxnomy_terms( $pf_terms, $customization['options'], $curr_include, $curr_fo, $curr_cat_selected, ( isset( $output_terms ) ? $output_terms : null ) );

						?>
							</div>
						</div>
					</div>
			<?php
				}

				if ( $curr_el == 'advanced' ) {
					$n++;
				}

			break;

			endswitch;

			do_action( 'prdctfltr_after_filter', $q );

			$q++;

		endforeach;

		$pf_cached_filters = ob_get_clean();

		WC_Prdctfltr::$settings['active_filters'] = $active_filters;

		WC_Prdctfltr::get_top_bar();

	?>
	<form <?php echo WC_Prdctfltr::get_action(); ?> class="prdctfltr_woocommerce_ordering" method="get">
	<?php
		do_action( 'prdctfltr_filter_form_before', WC_Prdctfltr::$settings['instance'], $pf_activated );

		if ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_collector'] !== 'off' ) {
		?>
			<span class="prdctfltr_collector prdctfltr_collector_<?php echo WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_collector']; ?>">
				<?php WC_Prdctfltr::get_top_bar_selected( 'collector' ); ?>
			</span>
		<?php
		}
	?>
		<div class="prdctfltr_filter_wrapper<?php echo $curr_columns_class; ?>" data-columns="<?php echo $curr_columns; ?>">
			<div class="prdctfltr_filter_inner">
			<?php
				echo $pf_cached_filters;

				if ( !isset( $prdctfltr_global['widget_search'] ) ) {
					echo '<div class="prdctfltr_clear"></div>';
				}
			?>
			</div>
			<div class="prdctfltr_clear"></div>
		</div>
		<?php do_action( 'prdctfltr_filter_form_after', WC_Prdctfltr::$settings['instance'], $pf_activated ); ?>
		<div class="prdctfltr_add_inputs">
		<?php
			if ( !in_array( 'search', $curr_elements ) && isset( $pf_activated['s'] ) ) {
				echo '<input type="hidden" name="' . ( isset( $prdctfltr_global['sc_init'] ) ? 'search_products' : 's' ) . '" value="' . $pf_activated['s'] . '" />';
			}
			if ( isset($_GET['page_id']) ) {
				echo '<input type="hidden" name="page_id" value="' . $_GET['page_id'] . '" />';
			}
			if ( isset($_GET['lang']) ) {
				echo '<input type="hidden" name="lang" value="' . $_GET['lang'] . '" />';
			}
			$curr_posttype = get_option( 'wc_settings_prdctfltr_force_product', 'no' );
			if ( $curr_posttype == 'no' ) {
				if ( !isset( $pf_activated['s'] ) && WC_Prdctfltr::$settings['permalink_structure'] == '' && ( is_shop() || is_product_taxonomy() ) ) {
					echo '<input type="hidden" name="post_type" value="product" />';
				}
			}
			else {
				echo '<input type="hidden" name="post_type" value="product" />';
			}

			if ( isset( $pf_activated['orderby'] ) && !in_array( 'sort', $curr_elements ) ) {
				echo '<input type="hidden" name="orderby" value="' . $pf_activated['orderby'] . '" />';
			}

			if ( !isset( $prdctfltr_global['sc_init'] ) && !empty( $prdctfltr_global['active_permalinks'] ) ) {
				foreach ( $prdctfltr_global['active_permalinks'] as $pf_k => $pf_v ) {
					if ( !array_key_exists( $pf_k, $active_filters ) ) {
						echo '<input type="hidden" name="' . $pf_k . '" value="' . $prdctfltr_global['permalinks_data'][$pf_k . '_string'] . '" class="pf_added_input" />';
					}
					$prdctfltr_global['filter_js'][$prdctfltr_id]['adds'][$pf_k] = $prdctfltr_global['permalinks_data'][$pf_k . '_string'];
				}
			}

			if ( WC_Prdctfltr::$settings['permalink_structure'] == '' ) {
				if ( !empty( $pf_activated ) ) {
					foreach( $pf_activated as $k => $v ) {
						if ( !array_key_exists( $k, $active_filters ) ) {
							echo '<input type="hidden" name="' . $k . '" value="' . $prdctfltr_global['taxonomies_data'][$k . '_string'] . '" class="pf_added_input" />';
						}
					}
				}
			}

		?>
		</div>
	</form>
	</div>
<?php

	do_action( 'prdctfltr_filter_after', WC_Prdctfltr::$settings['instance'], $pf_activated );

	if ( isset( $prdctfltr_global['categories_active'] ) && $prdctfltr_global['categories_active'] === false ) {
		add_filter( 'woocommerce_is_filtered', create_function('', 'return true;') );
	}

	$prdctfltr_global['init'] = true;

?>