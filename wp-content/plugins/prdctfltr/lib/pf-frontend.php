<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	class WC_Prdctfltr {

		public static $version = '6.0.2';

		public static $dir;
		public static $path;
		public static $url_path;
		public static $settings;

		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		function __construct() {

			global $prdctfltr_global;

			self::$dir = trailingslashit( Prdctfltr()->plugin_path() );
			self::$path = trailingslashit( Prdctfltr()->plugin_path() );
			self::$url_path = trailingslashit( Prdctfltr()->plugin_url() );

			self::$settings['permalink_structure'] = get_option( 'permalink_structure' );
			self::$settings['wc_settings_prdctfltr_disable_scripts'] = get_option( 'wc_settings_prdctfltr_disable_scripts', array() );
			self::$settings['wc_settings_prdctfltr_ajax_js'] = get_option( 'wc_settings_prdctfltr_ajax_js', '' );
			self::$settings['wc_settings_prdctfltr_custom_tax'] = get_option( 'wc_settings_prdctfltr_custom_tax', 'no' );
			self::$settings['wc_settings_prdctfltr_enable'] = get_option( 'wc_settings_prdctfltr_enable', 'yes' );

			self::$settings['wc_settings_prdctfltr_enable_overrides'] = get_option( 'wc_settings_prdctfltr_enable_overrides', array( 'orderby', 'result-count' ) );

			foreach( self::$settings['wc_settings_prdctfltr_enable_overrides'] as $k => $v ) {
				self::$settings['wc_settings_prdctfltr_enable_overrides'][$k] = 'loop/' . $v . '.php';
			}

			self::$settings['wc_settings_prdctfltr_enable_action'] = get_option( 'wc_settings_prdctfltr_enable_action', '' );
			self::$settings['wc_settings_prdctfltr_default_templates'] = get_option( 'wc_settings_prdctfltr_default_templates', 'no' );
			self::$settings['wc_settings_prdctfltr_instock'] = get_option( 'wc_settings_prdctfltr_instock', 'no' );
			self::$settings['wc_settings_prdctfltr_use_ajax'] = get_option( 'wc_settings_prdctfltr_use_ajax', 'no' );
			self::$settings['wc_settings_prdctfltr_ajax_class'] = get_option( 'wc_settings_prdctfltr_ajax_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_category_class'] = get_option( 'wc_settings_prdctfltr_ajax_category_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_product_class'] = get_option( 'wc_settings_prdctfltr_ajax_product_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_pagination_class'] = get_option( 'wc_settings_prdctfltr_ajax_pagination_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_count_class'] = get_option( 'wc_settings_prdctfltr_ajax_count_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_orderby_class'] = get_option( 'wc_settings_prdctfltr_ajax_orderby_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_columns'] = get_option( 'wc_settings_prdctfltr_ajax_columns', '4' );
			self::$settings['wc_settings_prdctfltr_ajax_rows'] = get_option( 'wc_settings_prdctfltr_ajax_rows', '4' );
			self::$settings['wc_settings_prdctfltr_force_redirects'] = get_option( 'wc_settings_prdctfltr_force_redirects', 'no' );
			self::$settings['wc_settings_prdctfltr_use_analytics'] = get_option( 'wc_settings_prdctfltr_use_analytics', 'no' );
			self::$settings['wc_settings_prdctfltr_shop_page_override'] = get_option( 'wc_settings_prdctfltr_shop_page_override', '' );
			self::$settings['wc_settings_prdctfltr_clearall'] = get_option( 'wc_settings_prdctfltr_clearall', array() );
			self::$settings['wc_settings_prdctfltr_showon_product_cat'] = get_option( 'wc_settings_prdctfltr_showon_product_cat', array() );
			self::$settings['wc_settings_prdctfltr_hideempty'] = get_option( 'wc_settings_prdctfltr_hideempty', 'no' ) == 'yes' ? 1 : 0;
			self::$settings['wc_settings_prdctfltr_pagination_type'] = get_option( 'wc_settings_prdctfltr_pagination_type', 'default' );
			self::$settings['wc_settings_prdctfltr_remove_single_redirect'] = get_option( 'wc_settings_prdctfltr_remove_single_redirect', 'yes' );
			self::$settings['wc_settings_prdctfltr_product_animation'] = get_option( 'wc_settings_prdctfltr_product_animation', 'default' );
			self::$settings['wc_settings_prdctfltr_filtering_mode'] = get_option( 'wc_settings_prdctfltr_filtering_mode', 'simple' );
			self::$settings['wc_settings_prdctfltr_after_ajax_scroll'] = get_option( 'wc_settings_prdctfltr_after_ajax_scroll', 'products' );
			self::$settings['wc_settings_prdctfltr_taxonomy_relation'] = get_option( 'wc_settings_prdctfltr_taxonomy_relation', 'AND' );
			//self::$settings['wc_settings_prdctfltr_termcount'] = get_option( 'wc_settings_prdctfltr_termcount', 'deep' );
			self::$settings['wc_settings_prdctfltr_ajax_pagination'] = get_option( 'wc_settings_prdctfltr_ajax_pagination', '' );
			self::$settings['wc_settings_prdctfltr_ajax_permalink'] = get_option( 'wc_settings_prdctfltr_ajax_permalink', '' );
			self::$settings['wc_settings_prdctfltr_ajax_failsafe'] = get_option( 'wc_settings_prdctfltr_ajax_failsafe', array( 'wrapper', 'product' ) );
			self::$settings['wc_settings_prdctfltr_force_action'] = get_option( 'wc_settings_prdctfltr_force_action', 'no' );

			self::$settings['wc_settings_prdctfltr_more_overrides'] = get_option( 'wc_settings_prdctfltr_more_overrides', false );
			if ( self::$settings['wc_settings_prdctfltr_more_overrides'] === false ) {
				self::$settings['wc_settings_prdctfltr_more_overrides'] = array( 'product_cat', 'product_tag' );
				if ( self::$settings['wc_settings_prdctfltr_custom_tax'] == 'yes' ) {
					self::$settings['wc_settings_prdctfltr_more_overrides'][] = 'characteristics';
				}
			}

			add_filter( 'woocommerce_locate_template', array( &$this, 'prdctrfltr_add_loop_filter' ), 10, 3 );
			add_filter( 'wc_get_template_part', array( &$this, 'prdctrfltr_add_filter' ), 10, 3 );
			add_filter( 'wcml_multi_currency_is_ajax', array( &$this, 'wcml_currency' ), 50, 1 );

			if ( in_array( self::$settings['wc_settings_prdctfltr_enable'], array( 'no', 'action' ) ) && self::$settings['wc_settings_prdctfltr_default_templates'] == 'yes' ) {
				add_filter( 'woocommerce_locate_template', array( &$this, 'prdctrfltr_add_loop_filter_blank' ), 10, 3 );
				add_filter( 'wc_get_template_part', array( &$this, 'prdctrfltr_add_filter_blank' ), 10, 3 );
			}

			if ( self::$settings['wc_settings_prdctfltr_enable'] == 'action' && self::$settings['wc_settings_prdctfltr_enable_action'] !== '' ) {
				$curr_action = explode( ':', self::$settings['wc_settings_prdctfltr_enable_action'] );
				if ( isset( $curr_action[1] ) ) {
					$curr_action[1] = floatval( $curr_action[1] );
				}
				else {
					$curr_action[1] = 10;
				}
				add_filter( $curr_action[0], array( &$this, 'prdctfltr_get_filter' ), $curr_action[1] );
			}

			add_filter( 'body_class', array( $this, 'add_body_class' ) );
/*			if ( self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes' ) {
				add_filter( 'woocommerce_pagination_args', array( &$this, 'prdctfltr_pagination_filter' ), 999, 1 );
			}*/

			//add_action( 'init', array( &$this, 'prdctfltr_text_domain' ), 1000 );
			add_action( 'wp_enqueue_scripts', array( &$this, 'prdctfltr_scripts' ) );
			add_action( 'wp_footer', array( &$this, 'localize_scripts' ) );
			add_filter( 'pre_get_posts', array( &$this, 'prdctfltr_wc_query' ), 999999, 1 );
			add_filter( 'parse_tax_query', array( &$this, 'prdctfltr_wc_tax' ), 999999, 1 );
			add_action( 'prdctfltr_filter_after', array( &$this, 'prdctfltr_add_css' ) );

			if ( !is_admin() ) {
				add_filter( 'woocommerce_shortcode_products_query', 'WC_Prdctfltr::add_wc_shortcode_filter', 999999 );
				if ( self::$settings['permalink_structure'] !== '' ) {
					if ( self::$settings['wc_settings_prdctfltr_force_redirects'] !== 'yes' ) {
						add_action( 'template_redirect', array( &$this, 'prdctfltr_redirect' ), 999999 );
					}
				}
			}

			if ( self::$settings['wc_settings_prdctfltr_remove_single_redirect'] == 'yes' ) {
				add_filter( 'woocommerce_redirect_single_search_result', array( &$this, 'remove_single_redirect' ), 999 );
			}
			if ( self::$settings['wc_settings_prdctfltr_use_analytics'] == 'yes' ) {
				add_action( 'wp_ajax_nopriv_prdctfltr_analytics', array( &$this, 'prdctfltr_analytics' ) );
				add_action( 'wp_ajax_prdctfltr_analytics', array( &$this, 'prdctfltr_analytics' ) );
			}

			add_action( 'prdctfltr_output', array( &$this, 'prdctfltr_get_filter' ), 10 );

		}

		function prdctfltr_scripts() {

			$curr_scripts = self::$settings['wc_settings_prdctfltr_disable_scripts'];

			wp_register_style( 'prdctfltr-font', self::$url_path .'lib/font/styles.css', false, self::$version );
			wp_enqueue_style( 'prdctfltr-font' );

			wp_register_style( 'prdctfltr-main-css', self::$url_path .'lib/css/prdctfltr.css', false, self::$version );
			wp_enqueue_style( 'prdctfltr-main-css' );

			if ( !in_array( 'mcustomscroll', $curr_scripts ) ) {

				wp_register_style( 'prdctfltr-scrollbar-css', self::$url_path .'lib/css/jquery.mCustomScrollbar.css', false, self::$version );
				wp_enqueue_style( 'prdctfltr-scrollbar-css' );

				wp_register_script( 'prdctfltr-scrollbar-js', self::$url_path .'lib/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), self::$version, true );
				wp_enqueue_script( 'prdctfltr-scrollbar-js' );

			}

			if ( !in_array( 'isotope', $curr_scripts ) ) {
				wp_register_script( 'prdctfltr-isotope-js', self::$url_path .'lib/js/isotope.js', array( 'jquery' ), self::$version, true );
				wp_enqueue_script( 'prdctfltr-isotope-js' );
			}

			if ( !in_array( 'ionrange', $curr_scripts ) ) {

				wp_register_style( 'prdctfltr-ionrange-css', self::$url_path .'lib/css/ion.rangeSlider.css', false, self::$version );
				wp_enqueue_style( 'prdctfltr-ionrange-css' );

				wp_register_script( 'prdctfltr-ionrange-js', self::$url_path .'lib/js/ion.rangeSlider.min.js', array( 'jquery' ), self::$version, true );
				wp_enqueue_script( 'prdctfltr-ionrange-js' );

			}

			wp_register_script( 'prdctfltr-history', self::$url_path .'lib/js/history.js', array( 'jquery' ), self::$version, true );
			wp_enqueue_script( 'prdctfltr-history' );

			wp_register_script( 'prdctfltr-main-js', self::$url_path .'lib/js/prdctfltr_main.js', array( 'jquery', 'hoverIntent' ), self::$version, true );
			wp_enqueue_script( 'prdctfltr-main-js' );


		}

		function localize_scripts() {

			global $prdctfltr_global;

			if ( !isset( $prdctfltr_global['init'] ) ) {
				wp_dequeue_script( 'prdctfltr-scrollbar-js' );
				wp_dequeue_script( 'prdctfltr-isotope-js' );
				wp_dequeue_script( 'prdctfltr-ionrange-js' );
				wp_dequeue_script( 'prdctfltr-history' );
				wp_dequeue_script( 'prdctfltr-main-js' );
			}
			else if ( wp_script_is( 'prdctfltr-main-js', 'enqueued' ) ) {
				global $wp_rewrite;

				$curr_args = array(
					'ajax' => admin_url( 'admin-ajax.php' ),
					'url' => self::$url_path,
					'page_rewrite' => $wp_rewrite->pagination_base,
					'js' => self::$settings['wc_settings_prdctfltr_ajax_js'],
					'use_ajax' => self::$settings['wc_settings_prdctfltr_use_ajax'],
					'ajax_class' => self::$settings['wc_settings_prdctfltr_ajax_class'],
					'ajax_category_class' => self::$settings['wc_settings_prdctfltr_ajax_category_class'],
					'ajax_product_class' => self::$settings['wc_settings_prdctfltr_ajax_product_class'],
					'ajax_pagination_class' => self::$settings['wc_settings_prdctfltr_ajax_pagination_class'],
					'ajax_count_class' => self::$settings['wc_settings_prdctfltr_ajax_count_class'],
					'ajax_orderby_class' => self::$settings['wc_settings_prdctfltr_ajax_orderby_class'],
					'ajax_pagination_type' => self::$settings['wc_settings_prdctfltr_pagination_type'],
					'ajax_animation' => self::$settings['wc_settings_prdctfltr_product_animation'],
					'ajax_scroll' => self::$settings['wc_settings_prdctfltr_after_ajax_scroll'],
					'analytics' => self::$settings['wc_settings_prdctfltr_use_analytics'],
					'clearall' => self::$settings['wc_settings_prdctfltr_clearall'],
					'permalinks' => self::$settings['wc_settings_prdctfltr_ajax_permalink'],
					'ajax_failsafe' => is_array( self::$settings['wc_settings_prdctfltr_ajax_failsafe'] ) ? self::$settings['wc_settings_prdctfltr_ajax_failsafe'] : array(),
					'localization' => array(
						'close_filter' => __( 'Close filter', 'prdctfltr' ),
						'filter_terms' => __( 'Filter terms', 'prdctfltr' ),
						'ajax_error' => __( 'AJAX Error!', 'prdctfltr' ),
						'show_more' => __( 'Show More', 'prdctfltr' ),
						'show_less' => __( 'Show Less', 'prdctfltr' ),
						'noproducts' => __( 'No products found!', 'prdctfltr' ),
						'clearall' => __( 'Clear all filters', 'prdctfltr' ),
						'getproducts' => __( 'Show products', 'prdctfltr' )
					),
					'js_filters' => ( isset( $prdctfltr_global['filter_js'] ) ? $prdctfltr_global['filter_js'] : array() ),
					'pagefilters' => ( isset( $prdctfltr_global['pagefilters'] ) ? $prdctfltr_global['pagefilters'] : array() ),
					'rangefilters' => ( isset( $prdctfltr_global['ranges'] ) ? $prdctfltr_global['ranges'] : array() ),
					'priceratio' => ( isset( $prdctfltr_global['price_ratio'] ) ? $prdctfltr_global['price_ratio'] : 1 ),
					'ajax_query_vars' => ( isset( self::$settings['ajax_query_vars'] ) ? self::$settings['ajax_query_vars'] : '' ),
					'orderby' => ( isset( $prdctfltr_global['default_order']['orderby'] ) ? $prdctfltr_global['default_order']['orderby'] : 'menu_order title' ),
					'order' => ( isset( $prdctfltr_global['default_order']['order'] ) ? $prdctfltr_global['default_order']['order'] : 'ASC' )
				);

				wp_localize_script( 'prdctfltr-main-js', 'prdctfltr', $curr_args );
			}

		}

		public static function make_global( $set, $query = array() ) {

			global $prdctfltr_global;

			if ( ( isset( $prdctfltr_global['done_filters'] ) && $prdctfltr_global['done_filters'] === true ) === false || $query == 'AJAX' ) :

			$stop = true;

			if ( $query == 'AJAX' || $query == 'FALSE' ) {
				$stop = false;
			}

			if ( $stop === true && ( isset( $query->query_vars['wc_query'] ) && $query->query_vars['wc_query'] == 'product_query' ) !== false && !is_admin() ) {
				$stop = false;
			}

			if ( $stop === true && ( isset( $query->query_vars['prdctfltr'] ) && $query->query_vars['prdctfltr'] == 'active' ) !== false ) {
				$stop = false;
			}

			if ( $stop === false ) {

				$taxonomies = array();
				$taxonomies_data = array();
				$permalink_taxonomies = array();
				$permalink_taxonomies_data = array();
				$misc = array();
				$rng_terms = array();
				$mta_terms = array();
				$rng_for_activated = array();
				$mta_for_activated = array();
				$mta_for_array = array();

				$product_taxonomies = get_object_taxonomies( 'product' );
				if ( ( $product_type = array_search( 'product_type', $product_taxonomies ) ) !== false ) {
					unset( $product_taxonomies[$product_type] );
				}

				$sc_args = array();

				$prdctfltr_global['taxonomies'] = $product_taxonomies;

				if ( isset( $prdctfltr_global['sc_query'] ) && is_array( $prdctfltr_global['sc_query'] ) ) {
					foreach( $prdctfltr_global['sc_query'] as $sck => $scv ) {
						if ( in_array( $sck, $product_taxonomies ) ) {
							continue;
						}
						$sc_args[$sck] = $scv;
					}
				}

				$set = array_merge( $sc_args, $set );

				if ( isset( $set ) && !empty( $set ) ) {

					$get = $set;

					if ( isset( $get['search_products'] ) && !empty( $get['search_products'] ) && !isset( $get['s'] ) ) {
						$get['s'] = $get['search_products'];
					}

					$allowed = array( 'orderby', 'order', 'min_price', 'max_price', 'instock_products', 'sale_products', 'products_per_page', 's', 'vendor' );

					foreach( $get as $k => $v ){
						if ( $v == '' ) {
							continue;
						}

						if ( in_array( $k, $allowed ) ) {
							if ( $k == 'order' ) {
								$misc[$k] = strtoupper( $v );
							}
							else if ( $k == 'orderby' ) {
								$misc[$k] = strtolower( $v );
							}
							else if ( in_array( $k, array( 'min_price', 'max_price', 'products_per_page' ) ) ) {
								$misc[$k] = intval( $v );
							}
							else {
								$misc[$k] = $v;
							}
						}
						else if ( taxonomy_exists( $k ) ) {

							if ( strpos( $v, ',' ) ) {
								$selected = explode( ',', $v );
								$taxonomies_data[$k . '_relation'] = 'IN';
							}
							else if ( strpos( $v, '+' ) ) {
								$selected = explode( '+', $v );
								$taxonomies_data[$k . '_relation'] = 'AND';
							}
							else {
								$selected = array( $v );
							}
							$taxonomies_data[$k . '_string'] = $v;

							if ( substr( $k, 0, 3 ) == 'pa_' ) {

								$f_attrs[] = 'attribute_' . $k;

								foreach( $selected as $val ) {
									if ( term_exists( $val, $k ) !== null ) {
										$taxonomies[$k][] = $val;
										$f_terms[] = self::prdctfltr_utf8_decode($val);
									}
								}

							}
							else {
								foreach( $selected as $val ) {
									if ( term_exists( $val, $k ) !== null ) {
										$taxonomies[$k][] = $val;
									}
								}

							}

						}
						else if ( substr($k, 0, 4) == 'rng_' ) {

							if ( substr($k, 0, 8) == 'rng_min_' ) {
								$rng_for_activated[$k] = $v;
								$rng_terms[str_replace('rng_min_', '', $k)]['min'] = $v;
							}
							else if ( substr($k, 0, 8) == 'rng_max_' ) {
								$rng_for_activated[$k] = $v;
								$rng_terms[str_replace('rng_max_', '', $k)]['max'] = $v;
							}
							else if ( substr($k, 0, 12) == 'rng_orderby_' ) {
								$rng_terms[str_replace('rng_orderby_', '', $k)]['orderby'] = $v;
							}
							else if ( substr($k, 0, 10) == 'rng_order_' ) {
								$rng_terms[str_replace('rng_order_', '', $k)]['order'] = $v;
							}

						}
						else if ( substr($k, 0, 4) == apply_filters( 'prdctfltr_meta_key_prefix', 'mta_' ) ) {
							$mta_key = substr($k, 4, -5);
							$mta_type = self::get_meta_type( substr($k, -4, 1) );
							$mta_compare = self::get_meta_compare( substr($k, -2, 2) );

							if ( strpos( $v, ',' ) ) {
								$mta_selected = explode( ',', $v );
								$mta_relation = 'OR';
							}
							else if ( strpos( $v, '+' ) ) {
								$mta_selected = explode( '+', $v );
								$mta_relation = 'AND';
							}
							else {
								$mta_selected = $v;
							}

							$mta_for_activated[$k] = $v;
							$mta_for_array[$k] = is_array( $mta_selected ) ? $mta_selected : array( $mta_selected );
							if ( is_array( $mta_selected ) ) {
								$mta_terms['relation'] = $mta_relation; 
								foreach( $mta_selected as $mta_sngl ) {
									if ( strpos( $mta_compare, 'BETWEEN') > -1 && strpos( $mta_sngl, '-' ) ) {
										$mta_sngl = explode( '-', $mta_sngl );
									}
									$mta_terms[] = array(
										'key' => $mta_key,
										'type' => $mta_type,
										'compare' => $mta_compare,
										'value' => $mta_sngl
									);
								}
							}
							else {
								if ( strpos( $mta_compare, 'BETWEEN') > -1 && strpos( $mta_selected, apply_filters( 'prdctfltr_meta_key_between_separator', '-' ) ) ) {
									$mta_selected = explode( apply_filters( 'prdctfltr_meta_key_between_separator', '-' ), $mta_selected );
								}
								$mta_terms[] = array(
									'key' => $mta_key,
									'type' => $mta_type,
									'compare' => $mta_compare,
									'value' => $mta_selected
								);
							}

						}

					}

					if ( !empty( $rng_terms ) ) {

						foreach ( $rng_terms as $rng_name => $rng_inside ) {

							if ( !in_array( $rng_name, array( 'price' ) ) ) {

								if ( ( isset($rng_inside['min']) && isset($rng_inside['max']) ) === false ) {
									continue;
								}

								if ( isset($rng_terms[$rng_name]['orderby']) && $rng_terms[$rng_name]['orderby'] == 'number' ) {
									$attr_args = array(
										'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty'],
										'orderby' => 'slug'
									);
									$sort_args = array(
										'order' => ( isset( $rng_terms[$rng_name]['order'] ) ? $rng_terms[$rng_name]['order'] : 'ASC' )
									);
									$curr_attributes = self::prdctfltr_get_terms( $rng_name, $attr_args );
									$curr_attributes = self::prdctfltr_sort_terms_naturally( $curr_attributes, $sort_args );
								}
								else if ( isset($rng_terms[$rng_name]['orderby']) && $rng_terms[$rng_name]['orderby'] !== '' ) {
									$attr_args = array(
										'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty'],
										'orderby' => $rng_terms[$rng_name]['orderby'],
										'order' => ( isset( $rng_terms[$rng_name]['order'] ) ? $rng_terms[$rng_name]['order'] : 'ASC' )
									);
									$curr_attributes = self::prdctfltr_get_terms( $rng_name, $attr_args );
								}
								else {
									$attr_args = array(
										'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty']
									);
									$curr_attributes = self::prdctfltr_get_terms( $rng_name, $attr_args );
								}

								if ( empty( $curr_attributes ) ) {
									continue;
								}

								$rng_found = false;

								$curr_ranges = array();

								foreach ( $curr_attributes as $c => $s ) {
									if ( $rng_found == true ) {
										$curr_ranges[] = $s->slug;
										if ( $s->slug == $rng_inside['max'] ) {
											$rng_found = false;
											continue;
										}
									}
									if ( $s->slug == $rng_inside['min'] && $rng_found === false ) {
										$rng_found = true;
										$curr_ranges[] = $s->slug;
									}
								}

								$taxonomies[$rng_name] = $curr_ranges;
								$taxonomies_data[$rng_name.'_string'] = implode( $curr_ranges, ',' );
								$taxonomies_data[$rng_name.'_relation'] = 'IN';

								$f_attrs[] = 'attribute_' . $rng_name;

								foreach ( $curr_ranges as $cr ) {
									$f_terms[] = $cr;
								}

							}

						}

					}

				}

				if ( is_product_taxonomy() || isset( $prdctfltr_global['sc_query'] ) && !empty( $prdctfltr_global['sc_query'] ) /*|| isset( $prdctfltr_global['ajax_adds'] ) && !empty( $prdctfltr_global['ajax_adds'] )*/ ) {

					$check_links = apply_filters( 'prdctfltr_check_permalinks', $product_taxonomies );

					foreach( $check_links as $check_link ) {

						$curr_link = false;
						$pf_helper = array();
						$pf_helper_real = array();

						if ( 1 == 1 ) {

							if ( !isset( $set[$check_link] ) && ( $curr_var = get_query_var( $check_link ) ) !== '' ) {
								$curr_link = $curr_var;
							}
							else if ( !isset( $set[$check_link] ) && isset( $prdctfltr_global['sc_query'][$check_link] ) && $prdctfltr_global['sc_query'][$check_link] !== '' ) {
								$curr_link = $prdctfltr_global['sc_query'][$check_link];
							}
							/*else if ( !isset( $set[$check_link] ) && isset( $prdctfltr_global['ajax_adds'][$check_link] ) && $prdctfltr_global['ajax_adds'][$check_link] !== '' ) {
								$curr_link = $prdctfltr_global['ajax_adds'][$check_link];
							}*/
							else {
								$curr_link = false;
							}

							if ( $curr_link ) {

								if ( strpos( $curr_link, ',' ) ) {
									$pf_helper = explode( ',', $curr_link );
									$permalink_taxonomies_data[$check_link.'_relation'] = 'IN';
								}
								else if ( strpos( $curr_link, '+' ) ) {
									$pf_helper = explode( '+', $curr_link );
									$permalink_taxonomies_data[$check_link.'_relation'] = 'AND';
								}
								else {
									$pf_helper = array( $curr_link );
									$permalink_taxonomies_data[$check_link.'_relation'] = 'IN';
								}

								foreach( $pf_helper as $val ) {
									if ( term_exists( $val, $check_link ) !== null ) {
										$pf_helper_real[] = $val;
										$f_terms[] = self::prdctfltr_utf8_decode($val);
									}
								}

								$permalink_taxonomies[$check_link] = $pf_helper_real;
								$permalink_taxonomies_data[$check_link . '_string'] = $curr_link;
								$f_attrs[] = 'attribute_' . $check_link;

							}

						}

					}

				}

				$prdctfltr_global['done_filters'] = true;
				$prdctfltr_global['taxonomies_data'] = $taxonomies_data;
				$prdctfltr_global['active_taxonomies'] = $taxonomies;
				$prdctfltr_global['active_misc'] = $misc;
				$prdctfltr_global['range_filters'] = $rng_terms;
				$prdctfltr_global['meta_filters'] = $mta_terms;
				$prdctfltr_global['meta_data'] = $mta_for_activated;
				$prdctfltr_global['active_filters'] = array_merge( $prdctfltr_global['active_taxonomies'], $prdctfltr_global['active_misc'], $rng_for_activated, $mta_for_array );

				$prdctfltr_global['active_permalinks'] = $permalink_taxonomies;
				$prdctfltr_global['permalinks_data'] = $permalink_taxonomies_data;

				if ( !empty( $prdctfltr_global['active_permalinks'] ) && ( is_shop() || is_product_taxonomy() ) ) {
					$prdctfltr_global['sc_query'] = $prdctfltr_global['active_permalinks'];
				}

				$prdctfltr_global['active_in_filter'] = $prdctfltr_global['active_filters'];
				if ( isset( $prdctfltr_global['sc_query'] ) && !is_array( $prdctfltr_global['sc_query'] ) ) {
					foreach ( $check_links as $check_link ) {
						if ( isset( $prdctfltr_global['sc_query'][$check_link] ) && isset( $prdctfltr_global['active_in_filter'][$check_link] ) && $prdctfltr_global['sc_query'][$check_link] == $prdctfltr_global['active_in_filter'][$check_link] ) {
							unset( $prdctfltr_global['active_in_filter'][$check_link] );
						}
						
					}
				}

				$prdctfltr_global['pf_activated'] = array_merge( $prdctfltr_global['active_in_filter'], $prdctfltr_global['active_permalinks'] );
				self::$settings['pf_activated'] = $prdctfltr_global['pf_activated'];

				if ( isset( $f_attrs ) ) {
					$prdctfltr_global['f_attrs'] = $f_attrs;
				}
				if ( isset( $f_terms ) ) {
					$prdctfltr_global['f_terms'] = $f_terms;
				}
			}

			endif;

		}

		function prdctfltr_wc_query( $query ) {

			if ( is_admin() && ( isset( $query->query_vars['prdctfltr'] ) && $query->query_vars['prdctfltr'] == 'active' ) == false ) {
				return $query;
			}

			self::make_global( $_REQUEST, $query );

			global $prdctfltr_global;

			$stop = true;
			$wc_check_query = 'notactive';
			$pf_check_query = 'notactive';

			if ( !$query->is_main_query() ) {
				if ( ( isset( $query->query_vars['prdctfltr'] ) && $query->query_vars['prdctfltr'] == 'active' ) === false ) {
					return $query;
				}
			}

			if ( $stop === true && ( isset( $query->query_vars['wc_query'] ) && $query->query_vars['wc_query'] == 'product_query' ) !== false && !is_admin() ) {
				$wc_check_query = 'active';
				$stop = false;
			}

			if ( $stop === true && ( isset( $query->query_vars['prdctfltr'] ) && $query->query_vars['prdctfltr'] == 'active' ) !== false ) {
				$pf_check_query = 'active';
				$stop = false;
			}


			if ( empty( $prdctfltr_global['active_filters'] ) && empty( $prdctfltr_global['active_permalinks'] ) ) {
				return $query;
			}


			if ( $stop === true ) {
				return $query;
			}

			$curr_args = array();
			$f_attrs = array();
			$f_terms = array();
			$rng_terms = array();

			$pf_not_allowed = array( 'product_type' );
			$product_taxonomies = $prdctfltr_global['taxonomies'];


			$pf_allowed = array( 'products_per_page', 'instock_products', 'orderby' );

			$pf_not_allowed = array( 'product_cat', 'product_tag', 'characteristics', 'min_price', 'max_price', 'sale_products', 'instock_products', 'products_per_page', 'widget_search', 'page_id', 'lang' );

			if ( isset( $prdctfltr_global['active_filters'] ) ) {

				$pf_activated =  $prdctfltr_global['active_filters'];

				$allowed = array( 'orderby', 'min_price', 'max_price', 'instock_products', 'sale_products', 'products_per_page' );

				if ( isset( $prdctfltr_global['range_filters'] ) ) {
					$rng_terms = $prdctfltr_global['range_filters'];
				}

				if ( isset( $prdctfltr_global['f_attrs'] ) ) {

					$f_attrs = $prdctfltr_global['f_attrs'];

					if ( isset( $prdctfltr_global['f_terms'] ) ) {
						$f_terms = $prdctfltr_global['f_terms'];
					}

				}

			}

			$prdctfltr_global['default_order']['orderby'] = isset( $query->query_vars['orderby'] ) ? $query->query_vars['orderby'] : '';
			$prdctfltr_global['default_order']['order'] = isset( $query->query_vars['order'] ) ? $query->query_vars['order'] : '';

			if ( !isset( $pf_activated['orderby'] ) && isset( $query->query_vars['orderby'] ) && $query->query_vars['orderby'] !== '' ) {
				$pf_activated['orderby'] = $query->query_vars['orderby'];
				$pf_activated['order'] = isset( $query->query_vars['order'] ) ? $query->query_vars['order'] : '';
			}

			if ( isset( $pf_activated['orderby'] ) && $pf_activated['orderby'] !== '' ) {

				$orderby = '';
				$order = '';

				$orderby_value = explode( '-', $pf_activated['orderby'] );
				$orderby       = esc_attr( $orderby_value[0] );
				$order         = isset( $pf_activated['order'] ) && !empty( $pf_activated['order'] ) ? ( $pf_activated['order'] == 'DESC' ? 'DESC' : 'ASC' ) : ( isset( $orderby_value[1] ) && !empty( $orderby_value[1] ) ? $orderby_value[1] : '' );

				$orderby = strtolower( $orderby );
				$order   = strtoupper( $order );

				switch ( $orderby ) {

					case 'rand' :
						$curr_args['orderby']  = 'rand';
					break;
					case 'date' :
					case 'date ID' :
						$curr_args['orderby']  = 'date';
						$curr_args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'price' :
						global $wpdb;
						$curr_args['orderby']  = "meta_value_num {$wpdb->posts}.ID";
						$curr_args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$curr_args['meta_key'] = '_price';
					break;
					case 'popularity' :
						$curr_args['meta_key'] = 'total_sales';
						add_filter( 'posts_clauses', array( WC()->query, 'order_by_popularity_post_clauses' ) );
					break;
					case 'rating' :
						add_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
					break;
					case 'title' :
						$curr_args['orderby']  = 'title';
						$curr_args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
					break;
					case 'menu_order' :
					case 'menu_order title' :
						$curr_args['orderby']  = $orderby;
						$curr_args['order'] = $order == 'DESC' ? 'DESC' : 'ASC';
					break;
					case 'comment_count' :
						$curr_args['orderby'] = 'comment_count';
						$curr_args['order']   = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					default :
						$curr_args['orderby'] = $orderby;
						$curr_args['order']   = $order == 'ASC' ? 'ASC' : 'DESC';
					break;

				}

			}

			if ( !isset($pf_activated['min_price']) && !isset($pf_activated['rng_min_price']) && isset($query->query['min_price']) && $query->query['min_price'] !== '' ) {
				$pf_activated['min_price'] = $query->query['min_price'];
			}
			if ( !isset($pf_activated['max_price']) && !isset($pf_activated['rng_max_price']) && isset($query->query['max_price']) && $query->query['max_price'] !== '' ) {
				$pf_activated['max_price'] = $query->query['max_price'];
			}

			if ( ( isset($pf_activated['min_price']) || isset($pf_activated['max_price']) ) !== false || ( isset($pf_activated['rng_min_price']) && isset($pf_activated['rng_max_price']) ) !== false || ( isset($pf_activated['sale_products']) || isset($query->query['sale_products']) ) !== false ) {
				add_filter( 'posts_join' , array( &$this, 'prdctfltr_join_price' ), 99997 );
				add_filter( 'posts_where' , array( &$this, 'prdctfltr_price_filter' ), 99998, 2 );
			}

			if ( !isset($pf_activated['instock_products']) && isset($query->query['instock_products']) && $query->query['instock_products'] !== '' ) {
				$pf_activated['instock_products'] = $query->query['instock_products'];
			}

			$curr_instock = self::$settings['wc_settings_prdctfltr_instock'];

			if ( ( ( ( isset( $pf_activated['instock_products'] ) && $pf_activated['instock_products'] !== '' && ( $pf_activated['instock_products'] == 'in' || $pf_activated['instock_products'] == 'out' ) ) || $curr_instock == 'yes' ) !== false ) && ( !isset( $pf_activated['instock_products'] ) || $pf_activated['instock_products'] !== 'both' ) ) {

				$notify = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) )+1;
				$notify_out = $notify-1;

				$curr_count = count( $f_attrs );

				if ( $curr_count > 0 ) {

					$curr_atts =  implode( '","', array_map( 'esc_sql', $f_attrs ) );
					$curr_terms = implode( '","', array_map( 'esc_sql', $f_terms ) );

					global $wpdb;

					$pf_exclude_product = $wpdb->get_results( $wpdb->prepare( '
						SELECT DISTINCT %1$s.post_parent as post_parent, %1$s.ID as ID FROM %1$s

						INNER JOIN %2$s AS s1 ON (%1$s.ID = s1.post_id)
						INNER JOIN %2$s AS s2 ON (%1$s.ID = s2.post_id) AND s2.meta_key = "_stock_status"
						INNER JOIN %2$s AS s3 ON (%1$s.ID = s3.post_id) AND s3.meta_key = "_manage_stock"
						INNER JOIN %2$s AS s4 ON (%1$s.ID = s4.post_id)

						WHERE 1=1
						AND %1$s.post_status = "publish"
						AND %1$s.post_type IN ( "product", "product_variation" )

						AND (
							(
								s1.meta_key IN ("'.$curr_atts.'")
								AND s1.meta_value IN ("'.$curr_terms.'","")
								AND (
									(
										s3.meta_key = "_manage_stock"
										AND s3.meta_value = "no"
										AND s2.meta_key = "_stock_status"
										AND s2.meta_value = "outofstock"
									)
									OR
									(
										s3.meta_key = "_manage_stock"
										AND s3.meta_value = "yes"
										AND s4.meta_key = "_stock"
										AND s4.meta_value < ' . $notify . '
									)
								)
								
							)
							OR (
								%1$s.post_parent = "0"
								AND (
									(
										s3.meta_key = "_manage_stock"
										AND s3.meta_value = "no"
										AND s2.meta_key = "_stock_status"
										AND s2.meta_value = "outofstock"
									)
									OR
									(
										s3.meta_key = "_manage_stock"
										AND s3.meta_value = "yes"
										AND s4.meta_key = "_stock"
										AND s4.meta_value < ' . $notify . '
									)
								)
							)
						)
						GROUP BY %1$s.ID
						ORDER BY %1$s.ID ASC
					', $wpdb->posts, $wpdb->postmeta ) );

					$pf_include_product = $wpdb->get_results( $wpdb->prepare( '
						SELECT DISTINCT %1$s.post_parent as post_parent FROM %1$s

						INNER JOIN %2$s AS s1 ON (%1$s.ID = s1.post_id) AND s1.meta_key IN ("'.$curr_atts.'")
						INNER JOIN %2$s AS s2 ON (%1$s.ID = s2.post_id) AND s2.meta_key = "_stock_status"
						INNER JOIN %2$s AS s3 ON (%1$s.ID = s3.post_id) AND s3.meta_key = "_manage_stock"
						INNER JOIN %2$s AS s4 ON (%1$s.ID = s4.post_id)

						WHERE 1=1
						AND %1$s.post_status = "publish"
						AND %1$s.post_type = "product_variation"

						AND (
							s1.meta_value IN ("'.$curr_terms.'","")
							AND (
								(
									s3.meta_key = "_manage_stock"
									AND s3.meta_value = "no"
									AND s2.meta_key = "_stock_status"
									AND s2.meta_value = "instock"
								)
								OR
								(
									s3.meta_key = "_manage_stock"
									AND s3.meta_value = "yes"
									AND s4.meta_key = "_stock"
									AND s4.meta_value > ' . $notify_out . '
								)
							)
						)
						GROUP BY %1$s.post_parent
						HAVING COUNT(DISTINCT s1.meta_value) = ' . count( $f_attrs ) .'
						ORDER BY %1$s.post_parent ASC
					', $wpdb->posts, $wpdb->postmeta ) );

				}
				else {

					global $wpdb;

					$pf_exclude_product = $wpdb->get_results( $wpdb->prepare( '
						SELECT DISTINCT %1$s.post_parent as post_parent, %1$s.ID as ID FROM %1$s

						INNER JOIN %2$s AS s2 ON (%1$s.ID = s2.post_id) AND s2.meta_key = "_stock_status"
						INNER JOIN %2$s AS s3 ON (%1$s.ID = s3.post_id) AND s3.meta_key = "_manage_stock"
						INNER JOIN %2$s AS s4 ON (%1$s.ID = s4.post_id)

						WHERE 1=1
						AND %1$s.post_status = "publish"
						AND %1$s.post_type IN ( "product", "product_variation" )

						AND (
							%1$s.post_parent = "0"
							AND (
								(
									s3.meta_key = "_manage_stock"
									AND s3.meta_value = "no"
									AND s2.meta_key = "_stock_status"
									AND s2.meta_value = "outofstock"
								)
								OR
								(
									s3.meta_key = "_manage_stock"
									AND s3.meta_value = "yes"
									AND s4.meta_key = "_stock"
									AND s4.meta_value < ' . $notify . '
								)
							)
						)
						GROUP BY %1$s.ID
						ORDER BY %1$s.ID ASC
					', $wpdb->posts, $wpdb->postmeta ) );

				}

				if ( isset( $pf_exclude_product ) && is_array( $pf_exclude_product ) ) {

					$curr_out = array();
					if ( isset( $pf_include_product ) && is_array( $pf_include_product ) ) {
						foreach ( $pf_include_product as $p ) {
							if ( intval( $p->post_parent ) !== 0 && !in_array( $p->post_parent, $curr_out ) ) {
								$curr_out[] = $p->post_parent;
							}
						}
					}


					$curr_in = array();
					foreach ( $pf_exclude_product as $p ) {
						if ( intval( $p->post_parent ) == 0 && !in_array( $p->ID, $curr_in ) ) {
							$curr_in[] = $p->ID;
						}
						else if ( !in_array( $p->post_parent, $curr_in ) && !in_array( $p->post_parent, $curr_out ) ) {
							$curr_in[] = $p->post_parent;
						}
					}

					if ( isset($pf_activated['instock_products']) && $pf_activated['instock_products'] == 'out' ) {
						$curr_args = array_merge( $curr_args, array(
									'post__in' => $curr_in
							) );
					}
					else {
						$curr_args = array_merge( $curr_args, array(
									'post__not_in' => $curr_in
							) );
					}
				}

			}

			if ( isset($pf_activated['products_per_page']) && $pf_activated['products_per_page'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
					'posts_per_page' => floatval( $pf_activated['products_per_page'] )
				) );
			}

			if ( isset( $pf_activated['s'] ) && $pf_activated['s'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
					's' => $pf_activated['s']
				) );
			}

			if ( isset( $pf_activated['vendor'] ) && $pf_activated['vendor'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
					'author' => $pf_activated['vendor']
				) );
			}

			$product_metas = $prdctfltr_global['meta_filters'];
			if ( !empty( $product_metas ) ) {
				$curr_args['meta_query']['relation'] = 'AND';
				$curr_args['meta_query'][] = $product_metas;
				$curr_args['meta_query'][] = $query->get( 'meta_query' );
			}

			foreach ( $curr_args as $k => $v ) {
				$query->set( $k, $v );
			}

		}

		function prdctfltr_wc_tax( $query ) {

			if ( is_admin() && ( isset( $query->query_vars['prdctfltr'] ) && $query->query_vars['prdctfltr'] == 'active' ) == false ) {
				return $query;
			}

			self::make_global( $_REQUEST, $query );

			global $prdctfltr_global;

			$stop = true;
			$wc_check_query = 'notactive';
			$pf_check_query = 'notactive';
			$curr_args = array();

			if ( empty( $prdctfltr_global['active_filters'] ) && empty( $prdctfltr_global['active_permalinks'] ) ) {
				$prdctfltr_global['categories_active'] = true;
				return $query;
			}

			if ( !$query->is_main_query() ) {
				if ( ( isset( $query->query_vars['prdctfltr'] ) && $query->query_vars['prdctfltr'] == 'active' ) === false ) {
					return $query;
				}
			}

			if ( ( isset( $query->query_vars['wc_query'] ) && $query->query_vars['wc_query'] == 'product_query' ) !== false && !is_admin() ) {
				$wc_check_query = 'active';
				$stop = false;
			}

			if ( $stop === true && ( isset( $query->query_vars['prdctfltr'] ) && $query->query_vars['prdctfltr'] == 'active' ) !== false ) {
				$pf_check_query = 'active';
				$stop = false;
			}

			if ( $stop === true ) {
				return $query;
			}

			if ( !empty( $prdctfltr_global['active_taxonomies'] ) || !empty( $prdctfltr_global['active_permalinks'] ) ) {

				$pf_activated = $prdctfltr_global['active_taxonomies'];

				$pf_tax_query = array();

				foreach ( $pf_activated as $k => $v ) {
					$relation = isset( $prdctfltr_global['taxonomies_data'][$k . '_relation'] ) && $prdctfltr_global['taxonomies_data'][$k.'_relation'] == 'AND' ? 'AND' : 'IN';
					if ( count( $v ) > 1 ) {
						if ( $relation == 'AND' ) {
							$precompile = array();
							foreach( $v as $k12 => $v12 ) {

								$asked_term = get_term_by( 'slug', $v12, $k );
								$child_terms = get_term_children( $asked_term->term_id, $k );

								if ( !empty( $child_terms ) ) {
									$precompile[] = array( 'taxonomy' => $k, 'field' => 'term_id', 'terms' => array_merge( $child_terms, array( $asked_term->term_id ) ), 'include_children' => false, 'operator' => 'IN' );
								}
								else {
									$precompile[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v12, 'include_children' => false, 'operator' => 'IN' );
								}
							}

							$precompile['relation'] = 'AND';

							$pf_tax_query[] = $precompile;
						}
						else {
							$pf_tax_query[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v, 'include_children' => true, 'operator' => 'IN' );
						}
					}
					else {
						$pf_tax_query[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v, 'include_children' => true, 'operator' => 'IN' );
					}
				}

				$pf_permalinks = $prdctfltr_global['active_permalinks'];

				foreach ( $pf_permalinks as $k => $v ) {
					$relation = isset( $prdctfltr_global['permalinks_data'][$k . '_relation'] ) && $prdctfltr_global['permalinks_data'][$k . '_relation'] == 'AND' ? 'AND' : 'IN';
					if ( count( $v ) > 1 ) {
						if ( $relation == 'AND' ) {
							$precompile = array();
							foreach( $v as $k12 => $v12 ) {

								$asked_term = get_term_by( 'slug', $v12, $k );
								$child_terms = get_term_children( $asked_term->term_id, $k );

								if ( !empty( $child_terms ) ) {
									$precompile[] = array( 'taxonomy' => $k, 'field' => 'term_id', 'terms' => array_merge( $child_terms, array( $asked_term->term_id ) ), 'include_children' => false, 'operator' => 'IN' );
								}
								else {
									$precompile[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v12, 'include_children' => false, 'operator' => 'IN' );
								}
							}

							$precompile['relation'] = 'AND';

							$pf_tax_query[] = $precompile;
						}
						else {
							$pf_tax_query[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v, 'include_children' => true, 'operator' => 'IN' );
						}
					}
					else {
						$pf_tax_query[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v, 'include_children' => true, 'operator' => 'IN' );
					}
				}

				if ( !empty( $pf_tax_query ) ) {

					$pf_tax_query['relation'] = self::$settings['wc_settings_prdctfltr_taxonomy_relation'] == 'OR' ? 'OR' : 'AND';

					$query->tax_query->queries = $pf_tax_query;
					$query->query_vars['tax_query'] = $query->tax_query->queries;
					$prdctfltr_global['tax_query'] = $query->tax_query->queries;
				}
			}

			if ( empty( $pf_activated ) ) {
				$prdctfltr_global['categories_active'] = true;
			}
			else {
				foreach( $pf_activated as $k => $v ) {
					if ( in_array( $k, array( 'orderby', 'order', 'products_per_page', 'instock_products', 'product_cat', 'sale_products', 's' ) ) ) {
						$cat_allowed = true;
					}
					else {
						$cat_not_allowed = true;
					}
				}

				if ( isset( $cat_not_allowed ) || $query->is_paged() ) {
					$prdctfltr_global['categories_active'] = false;
				}
				else if ( isset( $cat_allowed ) ) {
					$prdctfltr_global['categories_active'] = true;
				}

			}

		}

		function prdctfltr_join_price( $join ) {
			global $wpdb, $prdctfltr_global;
			$pf_activated = $prdctfltr_global['active_filters'];
			$meta_key = isset( $pf_activated['sale_products'] ) && $pf_activated['sale_products'] == 'on' ? '_sale_price' : '_price';
			$join .= " INNER JOIN $wpdb->postmeta AS pf_price ON $wpdb->posts.ID = pf_price.post_id AND pf_price.meta_key = '" . $meta_key . "' ";
			return $join;

		}

		function prdctfltr_price_filter( $where, &$wp_query ) {
			global $wpdb, $prdctfltr_global;

			$pf_activated = $prdctfltr_global['active_filters'];

			if ( isset( $pf_activated['sale_products'] ) && $pf_activated['sale_products'] == 'on' ) {

				$pf_sale = true;
				$pf_where_keys = array(
					array(
						'_sale_price'/*,'_min_variation_sale_price'*/
					),
					array(
						'_sale_price'/*,'_max_variation_sale_price'*/
					)
				);

			}
			else {

				$pf_sale = false;
				$pf_where_keys = array(
					array(
						'_price'/*,'_min_variation_price'*/
					),
					array(
						'_price'/*,'_max_variation_price'*/
					)
				);

			}

			if ( isset( $wp_query->query_vars['rng_min_price'] ) ) {
				$_min_price = $wp_query->query_vars['rng_min_price'];
			}
			if ( isset( $wp_query->query_vars['min_price'] ) ) {
				$_min_price =  $wp_query->query_vars['min_price'];
			}
			if ( isset( $pf_activated['rng_min_price'] ) ) {
				$_min_price = $pf_activated['rng_min_price'];
			}
			if ( isset( $pf_activated['min_price'] ) ) {
				$_min_price =  $pf_activated['min_price'];
			}
			if ( !isset( $_min_price ) ) {

				$prices = WC_Prdctfltr::get_filtered_price();
				$_min = floor( $prices->min_price );

			}

			if ( isset( $wp_query->query_vars['rng_max_price'] ) ) {
				$_max_price = $wp_query->query_vars['rng_max_price'];
			}
			if ( isset( $wp_query->query_vars['max_price'] ) ) {
				$_max_price =  $wp_query->query_vars['max_price'];
			}
			if ( isset( $pf_activated['rng_max_price'] ) ) {
				$_max_price = $pf_activated['rng_max_price'];
			}
			if ( isset( $pf_activated['max_price'] ) ) {
				$_max_price =  $pf_activated['max_price'];
			}

			if ( !isset( $_max_price ) ) {

				$prices = !isset( $prices ) ? WC_Prdctfltr::get_filtered_price() : $prices;

				$_max = ceil( $prices->max_price );

			}

			if ( ( isset($_min_price) || isset($_max_price) ) !== false ) {

				if ( !isset( $_min_price) ) {
					$_min_price = $_min;
				}

				if ( !isset( $_max_price) ) {
					$_max_price = $_max;
				}
				$_max_price = floatval( $_max_price ) + 0.999;

				$where .= " AND ( ( pf_price.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $pf_where_keys[0] ) ) . "') AND pf_price.meta_value >= $_min_price AND pf_price.meta_value <= $_max_price AND pf_price.meta_value != '' ) OR ( pf_price.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $pf_where_keys[1] ) ) . "') AND pf_price.meta_value >= $_min_price AND pf_price.meta_value <= $_max_price AND pf_price.meta_value != '' ) ) ";
			}
			else if ( $pf_sale === true ) {
				$where .= " AND ( pf_price.meta_key IN ('_sale_price') AND pf_price.meta_value > 0 ) ";
			}

			remove_filter( 'posts_where' , 'prdctfltr_price_filter' );

			return $where;
			
		}

		function prdctrfltr_add_filter( $template, $slug, $name ) {

			global $prdctfltr_global;

			if ( $slug == 'loop/no-products-found.php' && !isset( self::$settings['did_noproduct'] ) ) {
				self::$settings['did_noproduct'] = true;
				if ( $name ) {
					$path = self::$path . WC()->template_path() . "{$slug}-{$name}.php";
				} else {
					$path = self::$path . WC()->template_path() . "{$slug}.php";
				}

				return file_exists( $path ) ? $path : $template;
			}
			else if ( in_array( $slug, self::$settings['wc_settings_prdctfltr_enable_overrides'] ) ) {
				if ( self::$settings['wc_settings_prdctfltr_enable'] == 'yes' ) {
					if ( $name ) {
						$path = self::$path . WC()->template_path() . "{$slug}-{$name}.php";
					} else {
						$path = self::$path . WC()->template_path() . "{$slug}.php";
					}

					return file_exists( $path ) ? $path : $template;
				}
			}
			else if ( !isset( $prdctfltr_global['sc_init'] ) && self::$settings['wc_settings_prdctfltr_pagination_type'] !== 'default' && $slug == 'loop/pagination.php' && self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes' && is_woocommerce() ) {
				if ( $name ) {
					$path = self::$path . WC()->template_path() . "{$slug}-{$name}.php";
				} else {
					$path = self::$path . WC()->template_path() . "{$slug}.php";
				}
				return file_exists( $path ) ? $path : $template;
			}

			return $template;

		}

		function prdctrfltr_add_loop_filter( $template, $template_name, $template_path ) {

			global $prdctfltr_global;

			if ( $template_name == 'loop/no-products-found.php' && !isset( self::$settings['did_noproducts'] ) ) {
				self::$settings['did_noproducts'] = true;
				$path = self::$path . $template_path . $template_name;
				return file_exists( $path ) ? $path : $template;
			}
			else if ( in_array( $template_name, self::$settings['wc_settings_prdctfltr_enable_overrides'] ) ) {
				if ( self::$settings['wc_settings_prdctfltr_enable'] == 'yes' ) {
					$path = self::$path . $template_path . $template_name;
					return file_exists( $path ) ? $path : $template;
				}
			}
			else if ( !isset( $prdctfltr_global['sc_init'] ) && self::$settings['wc_settings_prdctfltr_pagination_type'] !== 'default' && $template_name == 'loop/pagination.php' && self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes' && is_woocommerce() ) {
				$path = self::$path . $template_path . $template_name;
				return file_exists( $path ) ? $path : $template;
			}

			return $template;


		}

		function prdctrfltr_add_filter_blank ( $template, $slug, $name ) {

			if ( in_array( $slug, array( 'loop/orderby.php', 'loop/result-count.php' ) ) ) {
				if ( $name ) {
					$path = self::$path . 'blank/' . WC()->template_path() . "{$slug}-{$name}.php";
				} else {
					$path = self::$path . 'blank/' . WC()->template_path() . "{$slug}.php";
				}

				return file_exists( $path ) ? $path : $template;
			}

			return $template;

		}

		function prdctrfltr_add_loop_filter_blank ( $template, $template_name, $template_path ) {

			if ( in_array( $template_name, array( 'loop/orderby.php', 'loop/result-count.php' ) ) ) {

				$path = self::$path . 'blank/' . $template_path . $template_name;
				return file_exists( $path ) ? $path : $template;

			}

			return $template;

		}

		function prdctfltr_redirect() {

			if ( !empty( $_REQUEST ) ) {

				if ( is_shop() || is_product_taxonomy() ) {

					$request = array();
					foreach( $_REQUEST as $k3 => $v3 ) {
						if ( taxonomy_exists( $k3 ) ) {
							if ( strpos( $v3, ' ' ) > -1 ) {
								$v3 = str_replace( ' ', '+', $v3 );
							}
						}
						$request[$k3] = $v3;
					}

					global $wp_rewrite, $wp_query;

					$current = $wp_query->get_queried_object();
					if ( !isset( $current->taxonomy ) || !$current->taxonomy ) {
						if ( isset( $request['product_cat'] ) && $request['product_cat'] !== '' ) {
							$current = new stdClass();
							$current->taxonomy = 'product_cat';
							$current->slug = $request['product_cat'];
						}
					}

					if ( isset( $current->taxonomy ) ) {

						if ( isset( $request[$current->taxonomy] ) ) {

							if ( strpos( $request[$current->taxonomy], ',' ) || strpos( $request[$current->taxonomy], '+' ) || strpos ( $request[$current->taxonomy], ' ' ) ) {
								$rewrite = $wp_rewrite->get_extra_permastruct( $current->taxonomy );
								if ( $rewrite !== false ) {
									if ( strpos( $request[$current->taxonomy], ',' ) ) {
										$terms = explode( ',', $request[$current->taxonomy] );
									}
									else if ( strpos( $request[$current->taxonomy], '+' ) ) {
										$terms = explode( '+', $request[$current->taxonomy] );
									}

									foreach( $terms as $term ) {
										$checked = get_term_by( 'slug', $term, $current->taxonomy );
										if ( !is_wp_error( $checked ) ) {
	/*										if ( $checked->parent !== 0 ) {*/
												$parents[] = $checked->parent;
	/*										}*/
										}
									}

									$parent_slug = '';
									if ( isset( $parents ) ) {
										$parents_unique = array_unique( $parents );
										if ( count( $parents_unique ) == 1 && $parents_unique[0] !== 0 ) {
											$not_found = false;
											$parent_check = $parents_unique[0];
											while ( $not_found === false ) {
												if ( $parent_check !== 0 ) {

													$checked = get_term_by( 'id', $parent_check, $current->taxonomy );
													if ( !is_wp_error( $checked ) ) {
														if ( $checked->parent !== 0 ) {
															$get_parent = $checked->slug;
															$parent_slug =  $get_parent . '/' . $parent_slug;
															$parent_check = $checked->parent;
														}
														else {
															$not_found = true;
														}
													}
													else {
														$not_found = true;
													}
												}
												else {
													$not_found = true;
												}
											}
										}
									}

									$redirect = preg_replace( '/\?.*/', '', get_bloginfo( 'url' ) ) . '/' . str_replace( '%' . $current->taxonomy . '%', $parent_slug . $request[$current->taxonomy], $rewrite );
								}
							}
							else {
								$link = get_term_link( $request[$current->taxonomy], $current->taxonomy );
								if ( !is_wp_error( $link ) ) {
									$redirect = preg_replace( '/\?.*/', '', $link );
								}
							}

							if ( isset( $redirect ) ) {

								$redirect = untrailingslashit( $redirect );

								unset( $request[$current->taxonomy] );

								if ( !empty( $request ) ) {

									$req = '';

									foreach( $request as $k => $v ) {
										if ( $v == '' || in_array( $k, apply_filters('prdctfltr_block_request', array( 'woocs_order_emails_is_sending' ) ) ) ) {
											unset( $request[$k] );
											continue;
										}

										$req .= $k . '=' . $v . '&';
									}

									$redirect = $redirect . '/?' . $req;

									if ( substr( $redirect, -1 ) == '&' ) {
										$redirect = substr( $redirect, 0, -1 );
									}

									if ( substr( $redirect, -1 ) == '?' ) {
										$redirect = substr( $redirect, 0, -1 );
									}

								}

								if ( isset( $redirect ) ) {

									wp_redirect( $redirect, 302 );
									exit();

								}

							}

						}

					}

				}

			}
			else {
				$uri  = $_SERVER['REQUEST_URI'];
				$qPos = strpos( $uri, '?' );

				if ( $qPos === strlen( $uri ) - 1 ) {
					wp_redirect( substr( $uri, 0, $qPos ), 302 );
					exit();
				}
			}

		}

		public static function prdctrfltr_search_array( $array, $attrs ) {
			$results = array();
			$found = 0;

			foreach ( $array as $subarray ) {

				if ( isset( $subarray['attributes'] ) ) {
					foreach ( $attrs as $k => $v ) {
						if ( in_array($v, $subarray['attributes'] ) ) {
							$found++;
						}
					}
				}
				if ( count($attrs) == $found ) {
					$results[] = $subarray;
				}
				else if ( $found > 0 ) {
					$results[] = $subarray;
				}
				$found = 0;

			}

			return $results;
		}

		public static function prdctfltr_sort_terms_hierarchicaly( Array &$cats, Array &$into, $parentId = 0 ) {
			foreach ( $cats as $i => $cat ) {
				if ( $cat->parent == $parentId ) {
					$into[$cat->term_id] = $cat;
					unset($cats[$i]);
				}
			}
			foreach ( $into as $topCat ) {
				$topCat->children = array();
				self::prdctfltr_sort_terms_hierarchicaly( $cats, $topCat->children, $topCat->term_id );
			}
		}

		public static function prdctfltr_sort_terms_naturally( $terms, $args ) {

			$sort_terms = array();

			foreach($terms as $term) {
				$sort_terms[$term->name] = $term;
			}

			uksort( $sort_terms, 'strnatcmp' );

			if ( strtoupper( $args['order'] ) == 'DESC' ) {
				$sort_terms = array_reverse( $sort_terms );
			}

			return $sort_terms;

		}

		public static function prdctfltr_get_filter() {

			if ( !isset( self::$settings['get_filter'] ) ) {
				self::$settings['get_filter'] = current_filter();
				include( self::$dir . 'woocommerce/loop/product-filter.php' );
			}

		}

		public static function prdctfltr_get_between( $content, $start, $end ){
			$r = explode($start, $content);
			if (isset($r[1])){
				$r = explode($end, $r[1]);
				return $r[0];
			}
			return '';
		}

		public static function prdctfltr_utf8_decode( $str ) {
			$str = preg_replace( "/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode( $str ) );
			return html_entity_decode( $str, null, 'UTF-8' );
		}

		public static function prdctfltr_wpml_get_id( $id ) {
			if( function_exists( 'icl_object_id' ) ) {
				return icl_object_id( $id, 'page', true );
			}
			else {
				return $id;
			}
		}

		public static function prdctfltr_wpml_translate_terms( $curr_include, $attr ) {

			if ( empty( $curr_include ) ) {
				return $curr_include;
			}

			global $sitepress;

			if( function_exists( 'icl_object_id' ) && is_object( $sitepress ) ) {

				$translated_include = array();

				$default_language = $sitepress->get_default_language();
				$current_language = $sitepress->get_current_language();

				foreach( $curr_include as $curr ) {
					$current_term = get_term_by( 'slug', $curr, $attr );

					if($current_term) {

						$term_id = $current_term->term_id;
						if ( $default_language != $current_language ) {
							$term_id = icl_object_id( $term_id, $attr, false, $current_language );
						}

						$term = get_term( $term_id, $attr );
						$translated_include[] = $term->slug;

					}
				}

				return $translated_include;
			}
			else {
				return $curr_include;
			}
		}

		public static function prdctfltr_wpml_language() {

			if ( isset( self::$settings['wpml_language'] ) ) {
				return self::$settings['wpml_language'];
			}
			else {
				if ( class_exists( 'SitePress' ) ) {
					global $sitepress;

					$default_language = $sitepress->get_default_language();
					$current_language = $sitepress->get_current_language();

					if ( $default_language != $current_language ) {
						$language = sanitize_title( $current_language );
						self::$settings['wpml_language'] = $language;
						return $language;
					}
					else {
						return false;
					}

				}
				else {
					return false;
				}
			}

		}

		public static function prdctfltr_check_appearance() {

			if ( !empty( self::$settings['wc_settings_prdctfltr_showon_product_cat'] ) && !is_shop() && is_product_category() ) {
				if ( !is_product_category( self::$settings['wc_settings_prdctfltr_showon_product_cat'] ) ) {
					return false;
				}
			}

			global $prdctfltr_global;

			$curr_shop_disable = get_option( 'wc_settings_prdctfltr_shop_disable', 'no' );

			if ( $curr_shop_disable == 'yes' && is_shop() && !is_product_category() ) {
				return false;
			}

			$curr_display_disable = get_option( 'wc_settings_prdctfltr_disable_display', array() );

			if ( is_shop() && !is_product_category() && in_array( get_option( 'woocommerce_shop_page_display' ), $curr_display_disable ) ) {
				return false;
			}

			if ( is_product_category() ) {

				$pf_queried_term = get_queried_object();
				$display_type = get_woocommerce_term_meta( $pf_queried_term->term_id, 'display_type', true );
				
				$display_type = ( $display_type == '' ? get_option( 'woocommerce_category_archive_display' ) : $display_type );

				if ( in_array( $display_type, $curr_display_disable ) ) {
					return false;
				}

			}

		}

		public static function prdctfltr_get_styles() {
			global $prdctfltr_global;
			$curr_options = self::$settings['instance'];

			$curr_styles = array(
				( in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_arrow', 'pf_arrow_inline', 'pf_default', 'pf_default_inline', 'pf_select', 'pf_default_select', 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right', 'pf_fullscreen' ) ) ? $curr_options['wc_settings_prdctfltr_style_preset'] : 'pf_default' ),
				( $curr_options['wc_settings_prdctfltr_always_visible'] == 'no' && $curr_options['wc_settings_prdctfltr_disable_bar'] == 'no' || in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right', 'pf_fullscreen' ) ) ? 'prdctfltr_slide' : 'prdctfltr_always_visible' ),
				( $curr_options['wc_settings_prdctfltr_click_filter'] == 'no' ? 'prdctfltr_click' : 'prdctfltr_click_filter' ),
				( $curr_options['wc_settings_prdctfltr_limit_max_height'] == 'no' ? 'prdctfltr_rows' : 'prdctfltr_maxheight' ),
				( $curr_options['wc_settings_prdctfltr_custom_scrollbar'] == 'no' ? 'prdctfltr_scroll_default' : 'prdctfltr_scroll_active' ),
				( $curr_options['wc_settings_prdctfltr_disable_bar'] == 'no' || in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ? '' : 'prdctfltr_disable_bar' ),
				$curr_options['wc_settings_prdctfltr_style_mode'],
				( $curr_options['wc_settings_prdctfltr_adoptive'] == 'no' ? '' : $curr_options['wc_settings_prdctfltr_adoptive_style'] ),
				$curr_options['wc_settings_prdctfltr_style_checkboxes'],
				( $curr_options['wc_settings_prdctfltr_show_search'] == 'no' ? '' : 'prdctfltr_search_fields' ),
				$curr_options['wc_settings_prdctfltr_style_hierarchy'],
				( $curr_options['wc_settings_prdctfltr_tabbed_selection'] == 'yes' ? 'prdctfltr_tabbed_selection' : '' ),
				( $curr_options['wc_settings_prdctfltr_adoptive'] !== 'no' && $curr_options['wc_settings_prdctfltr_adoptive_reorder'] == 'yes' ? 'prdctfltr_adoptive_reorder' : '' ),
				( $curr_options['wc_settings_prdctfltr_selected_reorder'] == 'yes' ? 'prdctfltr_selected_reorder' : '' )

			);

			$pf_step_filter = '';
			if ( isset( $prdctfltr_global['sc_init'] ) && isset( $prdctfltr_global['sc_ajax'] ) ) {
				$pf_action = isset( $curr_options['wc_settings_prdctfltr_custom_action'] ) && $curr_options['wc_settings_prdctfltr_custom_action'] !== '' ? $curr_options['wc_settings_prdctfltr_custom_action'] : isset( $prdctfltr_global['action'] ) && $prdctfltr_global['action'] !== '' ? $prdctfltr_global['action'] : '';
				if ( $pf_action !== '' ) {
					self::$settings['instance']['wc_settings_prdctfltr_click_filter'] = 'yes';
					self::$settings['instance']['step_filter'] = true;
					$curr_styles[] = 'prdctfltr_step_filter';
					$curr_styles[1] = 'prdctfltr_always_visible';
					$curr_styles[2] = 'prdctfltr_click_filter';
				}
			}

			if ( $curr_options['wc_settings_prdctfltr_disable_reset'] == 'yes' ) {
				$curr_styles[] = 'pf_remove_clearall';
			}

			if ( in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_arrow', 'pf_arrow_inline', 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right', 'pf_fullscreen' ) ) ) {
				self::$settings['instance']['wc_settings_prdctfltr_always_visible'] = 'no';
				self::$settings['instance']['wc_settings_prdctfltr_disable_bar'] = 'no';
			}

			return $curr_styles;

		}

		public static function prdctfltr_get_settings() {

			global $prdctfltr_global;

			$pf_activated = ( isset ( $prdctfltr_global['active_filters'] ) && is_array( $prdctfltr_global['active_filters'] ) ? $prdctfltr_global['active_filters'] : array() );

			if ( isset ( $prdctfltr_global['active_permalinks'] ) && is_array( $prdctfltr_global['active_permalinks'] ) ) {
				$pf_activated = array_merge( $prdctfltr_global['active_permalinks'], $pf_activated );
			}

			if ( isset( $prdctfltr_global['preset'] ) && $prdctfltr_global['preset'] !== '' ) {
				$get_options = $prdctfltr_global['preset'];
			}

			if ( !isset( $prdctfltr_global['disable_overrides'] ) || ( isset( $prdctfltr_global['disable_overrides'] ) && $prdctfltr_global['disable_overrides'] !== 'yes' ) ) {

				$curr_overrides = get_option( 'prdctfltr_overrides', array() );

				$pf_check_overrides = self::$settings['wc_settings_prdctfltr_more_overrides'];

				foreach ( $pf_check_overrides as $pf_check_override ) {

					$override = ( isset( $pf_activated[$pf_check_override][0] ) ? $pf_activated[$pf_check_override][0] : '' );

					if ( $override !== '' ) {

						if ( term_exists( $override, $pf_check_override ) == null ) {
							continue;
						}

						if ( is_array( $curr_overrides ) && isset( $curr_overrides[$pf_check_override] ) ) {

							if ( array_key_exists( $override, $curr_overrides[$pf_check_override] ) ) {
								$get_options = $curr_overrides[$pf_check_override][$override];
								break;
							}

							else if ( is_taxonomy_hierarchical( $pf_check_override ) ) {
								$curr_check = get_term_by( 'slug', $override, $pf_check_override );

								if ( $curr_check->parent !== 0 ) {

									$parents = get_ancestors( $curr_check->term_id, $pf_check_override );

									foreach( $parents as $parent_id ) {
										$curr_check_parent = get_term_by( 'id', $parent_id, $pf_check_override );
										if ( array_key_exists( $curr_check_parent->slug, $curr_overrides[$pf_check_override]) ) {
											$get_options = $curr_overrides[$pf_check_override][$curr_check_parent->slug];
											break;
										}
									}

								}
							}

						}
					}
				}
			}

			if ( !isset( $get_options ) && self::$settings['wc_settings_prdctfltr_shop_page_override'] !== '' && is_shop() && !is_product_taxonomy() ) {
				$get_options = self::$settings['wc_settings_prdctfltr_shop_page_override'];
			}

			if ( isset( $get_options ) && $get_options !== '' ) {
				$prdctfltr_global['preset'] = $get_options;
			}

			if ( isset( $get_options ) ) {
				$curr_or_presets = get_option( 'prdctfltr_templates', array() );
				if ( isset( $curr_or_presets ) && is_array( $curr_or_presets ) ) {
					if ( array_key_exists( $get_options, $curr_or_presets ) ) {
						$get_curr_options = json_decode( stripslashes( $curr_or_presets[$get_options] ), true );
						foreach( $get_curr_options as $k => $v ) {
							if ( $v === null ) {
								unset( $get_curr_options[$k] );
							}
						}
					}
				}
			}

			$pf_chck_settings = array(
				'wc_settings_prdctfltr_style_preset' => 'pf_default',
				'wc_settings_prdctfltr_always_visible' => 'no',
				'wc_settings_prdctfltr_click_filter' => 'no',
				'wc_settings_prdctfltr_limit_max_height' => 'no',
				'wc_settings_prdctfltr_max_height' => 150,
				'wc_settings_prdctfltr_custom_scrollbar' => 'no',
				'wc_settings_prdctfltr_disable_bar' => 'no',
				'wc_settings_prdctfltr_icon' => '',
				'wc_settings_prdctfltr_max_columns' => 6,
				'wc_settings_prdctfltr_adoptive' => 'no',
				'wc_settings_prdctfltr_cat_adoptive' => 'no',
				'wc_settings_prdctfltr_tag_adoptive' => 'no',
				'wc_settings_prdctfltr_chars_adoptive' => 'no',
				'wc_settings_prdctfltr_price_adoptive' => 'no',
				'wc_settings_prdctfltr_orderby_title' => '',
				'wc_settings_prdctfltr_price_title' => '',
				'wc_settings_prdctfltr_price_range' => 100,
				'wc_settings_prdctfltr_price_range_add' => 100,
				'wc_settings_prdctfltr_price_range_limit' => 6,
				'wc_settings_prdctfltr_cat_title' => '',
				'wc_settings_prdctfltr_cat_orderby' => '',
				'wc_settings_prdctfltr_cat_order' => '',
				'wc_settings_prdctfltr_cat_relation' => 'IN',
				'wc_settings_prdctfltr_cat_limit' => 0,
				'wc_settings_prdctfltr_cat_hierarchy' => 'no',
				'wc_settings_prdctfltr_cat_multi' => 'no',
				'wc_settings_prdctfltr_include_cats' => array(),
				'wc_settings_prdctfltr_tag_title' => '',
				'wc_settings_prdctfltr_tag_orderby' => '',
				'wc_settings_prdctfltr_tag_order' => '',
				'wc_settings_prdctfltr_tag_relation' => 'IN',
				'wc_settings_prdctfltr_tag_limit' => 0,
				'wc_settings_prdctfltr_tag_multi' => 'no',
				'wc_settings_prdctfltr_include_tags' => array(),
				'wc_settings_prdctfltr_custom_tax_title' => '',
				'wc_settings_prdctfltr_custom_tax_orderby' => '',
				'wc_settings_prdctfltr_custom_tax_order' => '',
				'wc_settings_prdctfltr_custom_tax_relation' => 'IN',
				'wc_settings_prdctfltr_custom_tax_limit' => 0,
				'wc_settings_prdctfltr_chars_multi' => 'no',
				'wc_settings_prdctfltr_include_chars' => array(),
				'wc_settings_prdctfltr_disable_sale' => 'no',
				'wc_settings_prdctfltr_noproducts' => '',
				'wc_settings_prdctfltr_meta_filters' => array(),
				'wc_settings_prdctfltr_advanced_filters' => array(),
				'wc_settings_prdctfltr_range_filters' => array(),
				'wc_settings_prdctfltr_disable_instock' => 'no',
				'wc_settings_prdctfltr_title' => '',
				'wc_settings_prdctfltr_style_mode' => 'pf_mod_multirow',
				'wc_settings_prdctfltr_instock_title' => '',
				'wc_settings_prdctfltr_disable_reset' => 'no',
				'wc_settings_prdctfltr_include_orderby' => array( 'menu_order', 'popularity', 'rating', 'date' ,'price', 'price-desc' ),
				'wc_settings_prdctfltr_adoptive_style' => 'pf_adptv_default',
				'wc_settings_prdctfltr_show_counts' => 'no',
				'wc_settings_prdctfltr_show_counts_mode' => 'default',
				'wc_settings_prdctfltr_disable_showresults' => 'no',
				'wc_settings_prdctfltr_orderby_none' => 'no',
				'wc_settings_prdctfltr_price_none' => 'no',
				'wc_settings_prdctfltr_cat_none' => 'no',
				'wc_settings_prdctfltr_tag_none' => 'no',
				'wc_settings_prdctfltr_chars_none' => 'no',
				'wc_settings_prdctfltr_perpage_title' => '',
				'wc_settings_prdctfltr_perpage_label' => '',
				'wc_settings_prdctfltr_perpage_range' => 20,
				'wc_settings_prdctfltr_perpage_range_limit' => 5,
				'wc_settings_prdctfltr_cat_mode' => 'showall',
				'wc_settings_prdctfltr_style_checkboxes' => 'prdctfltr_round',
				'wc_settings_prdctfltr_cat_hierarchy_mode' => 'no',
				'wc_settings_prdctfltr_show_search' => 'no',
				'wc_settings_prdctfltr_style_hierarchy' => 'prdctfltr_hierarchy_circle',
				'wc_settings_prdctfltr_button_position' => 'bottom',
				'wc_settings_prdctfltr_submit' => '',
				'wc_settings_prdctfltr_loader' => 'spinning-circles',
				'wc_settings_prdctfltr_cat_term_customization' => '',
				'wc_settings_prdctfltr_tag_term_customization' => '',
				'wc_settings_prdctfltr_chars_term_customization' => '',
				'wc_settings_prdctfltr_price_term_customization' => '',
				'wc_settings_prdctfltr_perpage_term_customization' => '',
				'wc_settings_prdctfltr_price_filter_customization' => '',
				'wc_settings_prdctfltr_perpage_filter_customization' => '',
				'wc_settings_prdctfltr_orderby_term_customization' => '',
				'wc_settings_prdctfltr_instock_term_customization' => '',
				'wc_settings_prdctfltr_custom_action' => '',
				'wc_settings_prdctfltr_search_title' => __( 'Search Products', 'prdctfltr' ),
				'wc_settings_prdctfltr_search_placeholder' => __( 'Product keywords', 'prdctfltr' ),
				'wc_settings_prdctfltr_adoptive_mode' => 'permalink',
				'wc_settings_prdctfltr_adoptive_depend' => array(),
				'wc_settings_prdctfltr_perpage_description' => '',
				'wc_settings_prdctfltr_instock_description' => '',
				'wc_settings_prdctfltr_orderby_description' => '',
				'wc_settings_prdctfltr_search_description' => '',
				'wc_settings_prdctfltr_price_description' => '',
				'wc_settings_prdctfltr_cat_description' => '',
				'wc_settings_prdctfltr_tag_description' => '',
				'wc_settings_prdctfltr_custom_tax_description' => '',
				'wc_settings_prdctfltr_vendor_title' => '',
				'wc_settings_prdctfltr_vendor_description' => '',
				'wc_settings_prdctfltr_include_vendor' => '',
				'wc_settings_prdctfltr_vendor_term_customization' => '',
				'wc_settings_prdctfltr_tabbed_selection' => '',
				'wc_settings_prdctfltr_collector' => 'off',
				'wc_settings_prdctfltr_adoptive_reorder' => 'yes',
				'wc_settings_prdctfltr_selected_reorder' => 'no',
			);

			if ( isset( $get_curr_options ) ) {

				$curr_options = $get_curr_options;

				$curr_options = array_merge( $pf_chck_settings, $curr_options );

				$wc_settings_prdctfltr_active_filters = $curr_options['wc_settings_prdctfltr_active_filters'];

				$wc_settings_prdctfltr_attributes = array();

				if ( is_array( $wc_settings_prdctfltr_active_filters ) ) {

					foreach ( $wc_settings_prdctfltr_active_filters as $k ) {
						if ( substr( $k, 0, 3 ) == 'pa_' ) {
							$wc_settings_prdctfltr_attributes[] = $k;
						}
					}

					foreach ( $wc_settings_prdctfltr_attributes as $k => $attr ) {

						$curr_array = array(
							'wc_settings_prdctfltr_' . $attr . '_hierarchy' => 'no',
							'wc_settings_prdctfltr_' . $attr . '_hierarchy_mode' => 'no',
							'wc_settings_prdctfltr_' . $attr . '_mode' => 'showall',
							'wc_settings_prdctfltr_' . $attr . '_limit' => 0,
							'wc_settings_prdctfltr_' . $attr . '_none' => 'no',
							'wc_settings_prdctfltr_' . $attr . '_adoptive' => 'no',
							'wc_settings_prdctfltr_' . $attr . '_title' => '',
							'wc_settings_prdctfltr_' . $attr . '_description' => '',
							'wc_settings_prdctfltr_' . $attr . '_orderby' => '',
							'wc_settings_prdctfltr_' . $attr . '_order' => '',
							'wc_settings_prdctfltr_' . $attr . '_relation' => 'IN',
							'wc_settings_prdctfltr_' . $attr => 'pf_attr_text',
							'wc_settings_prdctfltr_' . $attr . '_multi' => 'no',
							'wc_settings_prdctfltr_include_'.$attr => array(),
							'wc_settings_prdctfltr_' . $attr . '_term_customization' => ''
						);

						$curr_options = array_merge( $curr_array, $curr_options );

					}
				}

			}
			else {

				$wc_settings_prdctfltr_active_filters = get_option( 'wc_settings_prdctfltr_active_filters', array( 'sort','price','cat' ) );

				$wc_settings_prdctfltr_attributes = array();

				if ( is_array( $wc_settings_prdctfltr_active_filters ) ) {

					foreach ( $wc_settings_prdctfltr_active_filters as $k ) {
						if ( substr( $k, 0, 3 ) == 'pa_' ) {
							$wc_settings_prdctfltr_attributes[] = $k;
						}
					}
				}

				$curr_options = array(
					'wc_settings_prdctfltr_active_filters' => $wc_settings_prdctfltr_active_filters
				);
				

				foreach ( $pf_chck_settings as $z => $x) {
					$curr_options[$z] = get_option( $z, $x );
				}

				foreach ( $wc_settings_prdctfltr_attributes as $k => $attr ) {
					$curr_options['wc_settings_prdctfltr_' . $attr . '_hierarchy'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_hierarchy', 'no' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_hierarchy_mode'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_hierarchy_mode', 'no' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_mode'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_mode', 'showall' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_limit'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_limit', 'no' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_none'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_none', 'no' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_adoptive'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_adoptive', 'no' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_title'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_title', '' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_description'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_description', '' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_orderby'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_orderby', '' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_order'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_order', '' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_relation'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_relation', 'IN' );
					$curr_options['wc_settings_prdctfltr_' . $attr] = get_option( 'wc_settings_prdctfltr_' . $attr, 'pf_attr_text' );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_multi'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_multi', 'no' );
					$curr_options['wc_settings_prdctfltr_include_' . $attr] = get_option( 'wc_settings_prdctfltr_include_' . $attr, array() );
					$curr_options['wc_settings_prdctfltr_' . $attr . '_term_customization'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_term_customization', array() );
				}

			}

			$curr_options['preset'] = isset( $get_options ) && $get_options !== '' ? $get_options : 'default';

			if ( !isset( self::$settings['widget'] ) ) {
				if ( isset( $curr_options['wc_settings_prdctfltr_style_mode'] ) ) {
					if ( !in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_select', 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
						//$curr_mod = $curr_options['wc_settings_prdctfltr_style_mode'];
					}
					else {
						$curr_options['wc_settings_prdctfltr_style_mode'] = 'pf_mod_multirow';
					}
				}
				else {
					$curr_options['wc_settings_prdctfltr_style_mode'] = 'pf_mod_multirow';
				}
				//$curr_widget_add = '';
			}
			else {
				$curr_options['wc_settings_prdctfltr_style_preset'] = self::$settings['widget']['style'];
				$curr_options['wc_settings_prdctfltr_style_mode'] = 'pf_mod_multirow';
				//$curr_widget_add = ' data-preset="' . $prdctfltr_global['widget_options']['style'].'" data-template="' . $prdctfltr_global['widget_options']['preset'] . '"';
			}
			$curr_options = apply_filters( 'prdctfltr_get_settings', $curr_options );

			self::$settings['instance'] = isset( self::$settings['instance'] ) ? array_merge( self::$settings['instance'], $curr_options ) : $curr_options;

			if ( $curr_options['wc_settings_prdctfltr_button_position'] == 'top' ) {
				add_action( 'prdctfltr_filter_form_before', 'WC_Prdctfltr::prdctfltr_filter_buttons', 10, 2 );
				remove_action( 'prdctfltr_filter_form_after', 'WC_Prdctfltr::prdctfltr_filter_buttons');
			}
			else if ( $curr_options['wc_settings_prdctfltr_button_position'] == 'both' ) {
				add_action( 'prdctfltr_filter_form_after', 'WC_Prdctfltr::prdctfltr_filter_buttons', 10, 2 );
				add_action( 'prdctfltr_filter_form_before', 'WC_Prdctfltr::prdctfltr_filter_buttons', 10, 2 );
			}
			else {
				add_action( 'prdctfltr_filter_form_after', 'WC_Prdctfltr::prdctfltr_filter_buttons', 10, 2 );
				remove_action( 'prdctfltr_filter_form_before', 'WC_Prdctfltr::prdctfltr_filter_buttons');
			}

			return $curr_options;

		}

		public static function prdctfltr_get_terms( $curr_term, $curr_term_args ) {

			global $prdctfltr_global;

			$pf_activated = ( isset( $prdctfltr_global['active_filters'] ) ? $prdctfltr_global['active_filters'] : array() );

			$curr_term_args['hide_empty'] = self::$settings['wc_settings_prdctfltr_hideempty'];

			if ( !isset( $pf_activated['orderby'] ) && ( defined('DOING_AJAX') && DOING_AJAX ) === false || !isset( $pf_activated['orderby'] ) ) {
				$curr_terms = get_terms( $curr_term, $curr_term_args );
			}
			else if ( isset( $pf_activated['orderby'] ) ) {
				$curr_keep = $pf_activated['orderby'];
				unset( $pf_activated['orderby'] );
				$curr_terms = get_terms( $curr_term, $curr_term_args );
				$pf_activated['orderby'] = $curr_keep;
			}

			return $curr_terms;

		}

		public static function prdctfltr_in_array( $needle, $haystack ) {
			return in_array( strtolower( $needle ), array_map( 'strtolower', $haystack ) );
		}

/*
		function prdctfltr_pagination_filter( $args ) {

			if ( self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes' && is_woocommerce() ) {

				if ( strpos( $args['base'], 'paged=' ) < 1 ) {
					$args['base'] = esc_url( untrailingslashit( $args['base'] ) . '/?paged=%#%' );
				}

			}

			return $args;
		}
*/

		public static function prdctfltr_filter_buttons( $curr_options, $pf_activated ) {

			global $prdctfltr_global;

			$pf_activated = ( isset( $prdctfltr_global['active_in_filter'] ) ? $prdctfltr_global['active_in_filter'] : array() );

			$curr_elements = ( self::$settings['instance']['wc_settings_prdctfltr_active_filters'] !== NULL ? self::$settings['instance']['wc_settings_prdctfltr_active_filters'] : array() );

			ob_start();
		?>
			<div class="prdctfltr_buttons">
			<?php
				if ( self::$settings['instance']['wc_settings_prdctfltr_click_filter'] == 'no' ) {
			?>
				<a class="button prdctfltr_woocommerce_filter_submit" href="#">
					<?php
						if ( self::$settings['instance']['wc_settings_prdctfltr_submit'] !== '' ) {
							echo self::$settings['instance']['wc_settings_prdctfltr_submit'];
						}
						else {
							_e( 'Filter selected', 'prdctfltr' );
						}
					?>
				</a>
			<?php
				}
				if ( self::$settings['instance']['wc_settings_prdctfltr_disable_sale'] == 'no' ) {
				?>
				<span class="prdctfltr_sale">
					<?php
					printf('<label%2$s><input name="sale_products" type="checkbox"%3$s/><span>%1$s</span></label>', __('Show only products on sale' , 'prdctfltr'), ( isset($pf_activated['sale_products']) ? ' class="prdctfltr_active"' : '' ), ( isset($pf_activated['sale_products']) ? ' checked' : '' ) );
					?>
				</span>
				<?php
				}
				if ( self::$settings['instance']['wc_settings_prdctfltr_disable_instock'] == 'no' && !in_array('instock', $curr_elements) ) {
				?>
				<span class="prdctfltr_instock">
				<?php
					$curr_instock = get_option( 'wc_settings_prdctfltr_instock', 'no' );

					if ( $curr_instock == 'yes' ) {
						printf('<label%2$s><input name="instock_products" type="checkbox" value="both"%3$s/><span>%1$s</span></label>', __('Show out of stock products' , 'prdctfltr'), ( isset($pf_activated['instock_products']) ? ' class="prdctfltr_active"' : '' ), ( isset($pf_activated['instock_products']) ? ' checked' : '' ) );
					}
					else {
						printf('<label%2$s><input name="instock_products" type="checkbox" value="in"%3$s/><span>%1$s</span></label>', __('In stock only' , 'prdctfltr'), ( isset($pf_activated['instock_products']) ? ' class="prdctfltr_active"' : '' ), ( isset($pf_activated['instock_products']) ? ' checked' : '' ) );
					}
			?>
				</span>
			<?php
				}
			?>
			</div>
		<?php
			$out = ob_get_clean();

			echo $out;
		}

		public static function get_customized_term( $value, $name, $count, $customization, $checked = '' ) {

			if ( !isset( $customization['style'] ) ) {
				return;
			}

			$key = 'term_' . $value;
			$tooltip = 'tooltip_' . $value;
			$input = '';

			if ( $checked !== '' ) {
				$input = '<input type="checkbox" value="' . $value . '"' . $checked . '/>';
			}

			$tip = ( $value == '' ? __( 'None', 'prdctfltr' ) : ( isset( $customization['settings'][$tooltip] ) ? $customization['settings'][$tooltip] : false ) );
			$count = $count !== false ? ' <span class="prdctfltr_customize_count">' . $count . '</span>' : '';

			switch ( $customization['style'] ) {
				case 'text':
					$insert = '<span class="prdctfltr_customize_' . $customization['settings']['type'] . ' prdctfltr_customize"><span class="prdctfltr_customize_name">' . $name . '</span>' . $count . ( $tip !== false ? '<span class="prdctfltr_tooltip"><span>' . $tip . '</span></span>' : '' ) . $input . '</span>';
				break;
				case 'color':
					if ( !isset( $customization['settings'][$key] ) ) {
						$customization['settings'][$key] = 'transparent';
					}
					$insert = '<span class="prdctfltr_customize_block prdctfltr_customize"><span class="prdctfltr_customize_color" style="background-color:' . $customization['settings'][$key] . ';"></span>' . $count . ( $tip !== false ? '<span class="prdctfltr_tooltip"><span>' . $tip . '</span></span>' : '' ) . $input . '<span class="prdctfltr_customization_search">' . $name . '</span></span>';
				break;
				case 'image':
					if ( !isset( $customization['settings'][$key] ) ) {
						$customization['settings'][$key] = self::$url_path . '/lib/images/pf-transparent.gif';
					}
					$insert = '<span class="prdctfltr_customize_block prdctfltr_customize"><span class="prdctfltr_customize_image"><img src="' . esc_url( $customization['settings'][$key] ) . '" /></span>' . $count . ( $tip !== false ? '<span class="prdctfltr_tooltip"><span>' . $tip . '</span></span>' : '' ) . $input . '<span class="prdctfltr_customization_search">' . $name . '</span></span>';
				break;
				case 'image-text':
					if ( !isset( $customization['settings'][$key] ) ) {
						$customization['settings'][$key] = self::$url_path . '/lib/images/pf-transparent.gif';
					}
					$insert = '<span class="prdctfltr_customize_block prdctfltr_customize"><span class="prdctfltr_customize_image_text"><img src="' . esc_url( $customization['settings'][$key] ) . '" /></span>' . $count . ( $tip !== false ? '<span class="prdctfltr_customize_image_text_tip">' . $tip . '</span><span class="prdctfltr_customization_search">' . $name . '</span>' : $name ) . $input . '</span>';
				break;
				case 'select':
					$insert = '<span class="prdctfltr_customize_select prdctfltr_customize"><span class="prdctfltr_customize_name">' . $name . '</span>' . $count . $input . '</span>';
				break;
				default :
					if ( isset( $customization['settings'][$key] ) ) {
						$insert = $customization['settings'][$key];
					}
				break;
			}

			if ( !isset( $insert ) ) {
				$insert = '';
			}

			return $insert;

		}

		public static function add_customized_terms_css( $id, $customization ) {

			if ( $customization['settings']['type'] == 'border' ) {
				$css_entry = sprintf( '%1$s .prdctfltr_customize {border-color:%2$s;color:%2$s;}%1$s label.prdctfltr_active .prdctfltr_customize {border-color:%3$s;color:%3$s;}%1$s label.pf_adoptive_hide .prdctfltr_customize {border-color:%4$s;color:%4$s;}', '.' . $id, $customization['settings']['normal'], $customization['settings']['active'], $customization['settings']['disabled'] );
			}
			else if ( $customization['settings']['type'] == 'background' ) {
				$css_entry = sprintf( '%1$s .prdctfltr_customize {background-color:%2$s;}%1$s label.prdctfltr_active .prdctfltr_customize {background-color:%3$s;}%1$s label.pf_adoptive_hide .prdctfltr_customize {background-color:%4$s;}', '.' . $id, $customization['settings']['normal'], $customization['settings']['active'], $customization['settings']['disabled'] );
			}
			else if ( $customization['settings']['type'] == 'round' ) {
				$css_entry = sprintf( '%1$s .prdctfltr_customize {background-color:%2$s;border-radius:50%%;}%1$s label.prdctfltr_active .prdctfltr_customize {background-color:%3$s;}%1$s label.pf_adoptive_hide .prdctfltr_customize {background-color:%4$s;}', '.' . $id, $customization['settings']['normal'], $customization['settings']['active'], $customization['settings']['disabled'] );
			}
			else {
				$css_entry = '';
			}

			if ( !isset( self::$settings['css'] ) ) {
				self::$settings['css'] = $css_entry;
			}
			else {
				self::$settings['css'] .= $css_entry;
			}

		}

		public static function prdctfltr_add_css() {
			if ( isset( self::$settings['css'] ) ) {
?>
				<style type="text/css">
					<?php echo self::$settings['css']; ?>
				</style>
<?php
			}
		}

		public static function get_filter_customization( $filter, $key ) {

			$language = self::prdctfltr_wpml_language();

			if ( $key !== '' ) {
				if ( $language !== false ) {
					$get_customization = get_option( $key . '_' . $language, '' );
				}
				else {
					$get_customization = get_option( $key, '' );
				}

				if ( $get_customization !== '' && isset( $get_customization['filter'] ) && $get_customization['filter'] = $filter ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array();
			}

			return $customization;

		}

		function prdctfltr_analytics() {

			check_ajax_referer( 'prdctfltr_analytics', 'pf_nonce' );

			$data = isset( $_POST['pf_filters'] ) ? $_POST['pf_filters'] : '';
			$defaults = array(
				'sale_products' => 'default',
				'instock_products' => 'default',
				'orderby' => 'default'
			);
			$data = array_merge( $defaults, $data );

			if ( empty( $data ) ) {
				die();
				exit;
			}

			$forbidden = array( 'rng_min_price', 'rng_max_price', 'order' );
			foreach( $data as $k => $v ) {
				if ( in_array( $k, $forbidden ) ) {
					unset( $data[$k] );
				}
				else if ( substr( $k, 0, 4 ) == 'rng_' ) {
					unset( $data[$k] );
				}
				else if ( in_array( $k, array( 'min_price', 'max_price' ) ) ) {
					if ( isset( $data['min_price'] ) ) {
						$data['price'] = isset( $data['max_price'] ) ? $v . '-' . $data['max_price'] : $v . '+';
						unset( $data['min_price'] );
						if ( isset( $data['max_price'] ) ) {
							unset( $data['max_price'] );
						}
					}
					else {
						unset( $data['max_price'] );
					}
				}
			}

			$stats = get_option( 'wc_settings_prdctfltr_filtering_analytics_stats', array() );

			foreach( $data as $k =>$v ) {
				if ( strpos( $v, ',' ) ) {
					$selected = explode( ',', $v );
				}
				else if ( strpos( $v, '+' ) ) {
					$selected = explode( '+', $v );
				}
				else {
					$selected = array( $v );
				}
				foreach ( $selected as $k2 => $v2 ) {
					if ( array_key_exists( $k, $stats ) ) {
						if ( array_key_exists( $v2, $stats[$k] ) ) {
							$stats[$k][$v2] = $stats[$k][$v2] + 1;
						}
						else {
							$stats[$k][$v2] = 1;
						}
					}
					else {
						$stats[$k][$v2] = 1;
					}
				}

			}

			update_option( 'wc_settings_prdctfltr_filtering_analytics_stats', $stats );

			die( 'Updated!' );
			exit;
		}

		public static function get_term_count( $has, $of ) {
			if ( isset( self::$settings['instance']['wc_settings_prdctfltr_show_counts_mode'] ) ) {

				$set = self::$settings['instance']['wc_settings_prdctfltr_show_counts_mode'];

				switch( $set ) {
					case 'default' :
						return $has . apply_filters( 'prdctfltr_count_separator', '/' ) . $of;
					break;
					case 'count' :
						return $has;
					break;
					case 'total' :
						return $of;
					break;
					default:
						return '';
					break;
				}
			}
		}

		public static function nice_number( $n ) {
			$n = ( 0 + str_replace( ',', '', $n ) );

			if( !is_numeric( $n ) ){
				return false;
			}

			if ( $n > 1000000000000 ) {
				return round( ( $n / 1000000000000 ) , 1 ).' ' . __( 'trillion' , 'prdctfltr' );
			}
			else if ( $n > 1000000000 ) {
				return round( ( $n / 1000000000 ) , 1 ).' ' . __( 'billion' , 'prdctfltr' );
			}
			else if ( $n > 1000000 ) {
				return round( ( $n / 1000000 ) , 1 ).' ' . __( 'million' , 'prdctfltr' );
			}
			else if ( $n > 1000 ) {
				return round( ( $n / 1000 ) , 1 ).' ' . __( 'thousand' , 'prdctfltr' );
			}

			return number_format($n);
		}

		public static function price_to_float( $s ) {

			$s = str_replace( ',', '.', $s );
			$s = preg_replace('/&.*?;/', '', $s);

			$hasCents = (substr( $s, -3, 1 ) == '.');
			$s = str_replace( '.', '', $s );
			if ( $hasCents ) {
				$s = substr( $s, 0, -2 ) . '.' . substr( $s, -2 );
			}

			return (float) $s;
		}

		public static function add_wc_shortcode_filter( $query_args, $atts = array(), $loop_name = '' ) {
			$query_args['prdctfltr'] = 'active';
			return $query_args;
		}

		public static function get_filtered_price( $mode = 'yes' ) {

			global $wpdb, $prdctfltr_global;

			$tax_query  = ( $mode =='yes' && isset( $prdctfltr_global['tax_query'] ) ? $prdctfltr_global['tax_query'] : array() );

			if ( empty( $tax_query ) ) {
				global $wp_the_query;
				$tax_query = isset( $wp_the_query->tax_query->queries ) && !empty( $wp_the_query->tax_query->queries ) ? $wp_the_query->tax_query->queries : array();
			}

			$tax_query  = new WP_Tax_Query( $tax_query );

			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
			$sql  = "SELECT min( CAST( price_meta.meta_value AS UNSIGNED ) ) as min_price, max( CAST( price_meta.meta_value AS UNSIGNED ) ) as max_price FROM {$wpdb->posts} ";
			$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'];
			$sql .= " 	WHERE {$wpdb->posts}.post_type = ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
						AND {$wpdb->posts}.post_status = 'publish'
						AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price', '_min_variation_price' ) ) ) ) . "')
						AND price_meta.meta_value > '' ";
			$sql .= $tax_query_sql['where'];

			$prices = $wpdb->get_row( $sql );

			if ( $prices->min_price < 0 && $prices->max_price <= 0 && $mode == 'yes' ) {
				return self::get_filtered_price( 'no' );
			}
			else if ( $prices->min_price >= 0 && $prices->min_price < $prices->max_price ) {
				return $prices;
			}
			else {

				$_min = floor( $wpdb->get_var(
					$wpdb->prepare('
						SELECT min(meta_value + 0)
						FROM %1$s
						LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
						WHERE ( meta_key = \'%3$s\' OR meta_key = \'%4$s\' )
						AND meta_value != ""
						', $wpdb->posts, $wpdb->postmeta, '_price', '_min_variation_price' )
					)
				);

				$_max = ceil( $wpdb->get_var(
					$wpdb->prepare('
						SELECT max(meta_value + 0)
						FROM %1$s
						LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
						WHERE ( meta_key = \'%3$s\' OR meta_key = \'%4$s\' )
						AND meta_value != ""
						', $wpdb->posts, $wpdb->postmeta, '_price', '_max_variation_price' )
				) );

				$prices = new stdClass();

				if ( $_min >= 0 && $_min < $_max ) {
					$prices->min_price = $_min;
					$prices->max_price = $_max;
				}
				else {
					$prices->min_price = 0;
					$prices->max_price = 1000;
				}

				return $prices;
			}

		}

		/*remove*/
		public static function published_term_count( $term_id, $taxonomy ) {
			if( $term_id && $taxonomy ){
				global $wpdb;
				$query = "
					SELECT COUNT(ID) FROM $wpdb->posts
					LEFT JOIN $wpdb->term_relationships ON
					($wpdb->posts.ID = $wpdb->term_relationships.object_id)
					LEFT JOIN $wpdb->term_taxonomy ON
					($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
					WHERE $wpdb->posts.post_status = 'publish'
					AND $wpdb->posts.post_type IN ('product')
					AND $wpdb->term_taxonomy.taxonomy = '" . $taxonomy . "'
					AND $wpdb->term_taxonomy.term_id = '" . $term_id . "'
				";

				return intval( $wpdb->get_var($query) );
			}
			else {
				return 0;
			}

		}

		function add_body_class( $classes ) {
			if ( is_shop() || is_product_taxonomy() ) {
				if ( self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes' ) {
					$classes[] = 'prdctfltr-ajax';
				}
				$classes[] = 'prdctfltr-shop';
			}

			return $classes;
		}

		function debug() {
			global $prdctfltr_global;
		?>
			<div class="prdctfltr_debug"><?php var_dump( $prdctfltr_global ); ?></div>
		<?php
		}

		function remove_single_redirect() {
			return false;
		}

		public static function get_catalog_ordering_args() {

			$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

			return $orderby_value;

		}

		public static function get_taxnomy_terms( $terms, $customization, $curr_include, $curr_fo, $curr_cat_selected, $output_terms, $parent = false ) {

			foreach ( $terms as $term ) {

				if ( !empty( $curr_include ) && !in_array( $term->slug, $curr_include ) ) {
					continue;
				}

				if ( !empty( $term->children ) ) {
					global $wpdb;

					$pf_childs = get_term_children( $term->term_id, $curr_fo['filter'] );
					if ( empty( $pf_childs ) ) {
						$pf_parent = '
							SELECT SUM(%1$s.count) as count FROM %1$s
							WHERE %1$s.term_id = "' . $term->term_id . '"
							OR %1$s.parent = "' . $term->term_id . '"
						';
					}
					else {
						$pf_parent = '
							SELECT SUM(%1$s.count) as count FROM %1$s
							WHERE %1$s.term_id = "' . $term->term_id . '"
							OR %1$s.parent IN ("' . implode( '","', array_map( 'esc_sql', array_merge( $pf_childs, array( $term->term_id ) ) ) ) . '")
						';
					}


					$pf_count = $wpdb->get_var( $wpdb->prepare( $pf_parent, $wpdb->term_taxonomy ) );

					$term_count_real = $pf_count;
				}
				else {
					$term_count_real = $term->count;
				}

				if ( !empty( $curr_fo['settings']['customization'] ) ) {

					$term_count = ( self::$settings['instance']['wc_settings_prdctfltr_show_counts'] == 'no' || $term_count_real == '0' ? false : ( self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && $curr_fo['settings']['adoptive'] == 'yes' &&  isset( $output_terms[$curr_fo['filter']][$term->slug] ) && $output_terms[$curr_fo['filter']][$term->slug] != $term_count_real ? self::get_term_count( $output_terms[$curr_fo['filter']][$term->slug], $term_count_real ) : ( self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && $curr_fo['settings']['adoptive'] == 'yes' && !empty( $output_terms[$curr_fo['filter']] ) && !isset( $output_terms[$curr_fo['filter']][$term->slug] ) ? self::get_term_count( 0, $term_count_real ) : $term_count_real ) ) );

					$curr_insert = self::get_customized_term( $term->slug, $term->name, $term_count, $customization );

				}
				else {

					$term_count = ( self::$settings['instance']['wc_settings_prdctfltr_show_counts'] == 'no' || $term_count_real == '0' ? '' : ' <span class="prdctfltr_count">' . ( self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && $curr_fo['settings']['adoptive'] == 'yes' &&  isset( $output_terms[$curr_fo['filter']][$term->slug] ) && $output_terms[$curr_fo['filter']][$term->slug] != $term_count_real ? self::get_term_count( $output_terms[$curr_fo['filter']][$term->slug], $term_count_real ) : ( self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && $curr_fo['settings']['adoptive'] == 'yes' && !empty( $output_terms[$curr_fo['filter']] ) && !isset( $output_terms[$curr_fo['filter']][$term->slug] ) ? self::get_term_count( 0, $term_count_real ) : $term_count_real ) ) . '</span>' );

					$curr_insert = $term->name . $term_count;

				}

				$pf_adoptive_class = '';

				if ( $curr_fo['settings']['adoptive'] == 'yes' && isset( $output_terms[$curr_fo['filter']] ) && !empty( $output_terms[$curr_fo['filter']] ) && !array_key_exists( $term->slug, $output_terms[$curr_fo['filter']] ) ) {
					$pf_adoptive_class = ' pf_adoptive_hide';
				}

				printf('<label class="%6$s%4$s%7$s%8$s"><input type="checkbox" value="%1$s"%3$s%9$s /><span>%2$s</span>%5$s</label>', $term->slug, $curr_insert, ( in_array( $term->slug, $curr_cat_selected ) ? ' checked' : '' ), ( in_array( $term->slug, $curr_cat_selected ) ? ' prdctfltr_active' : '' ), ( !empty( $term->children ) ? '<i class="prdctfltr-plus"></i>' : '' ), $pf_adoptive_class, ( !empty( $term->children ) && in_array( $term->slug, $curr_cat_selected ) ? ' prdctfltr_clicked' : '' ), ' prdctfltr_ft_' . sanitize_title( $term->slug ), ( $parent !== false ? ' data-parent="' . $parent . '"' : '' ) );

				if ( isset( $curr_fo['settings']['hierarchy'] ) && $curr_fo['settings']['hierarchy'] == 'yes' && !empty( $term->children ) ) {

					printf( '<div class="prdctfltr_sub" data-sub="%1$s">', $term->slug );

					self::get_taxnomy_terms( $term->children, $customization, $curr_include, $curr_fo, $curr_cat_selected, $output_terms, $term->slug );

					printf( '</div>' );

				}

			}

		}

		function wcml_currency( $actions ) {
			$actions[] = 'prdctfltr_respond_550';
			return $actions;
		}

		public static function get_filter_selection( $filter ) {
			global $prdctfltr_global;

			if ( self::$settings['instance']['wc_settings_prdctfltr_disable_showresults'] !== 'no' ) {
				return;
			}

			if ( in_array( $filter, array( 'byprice' ) ) && !isset( self::$settings['pf_activated']['min_price'] ) ) {
				return;
			}

			if ( !isset( self::$settings['pf_activated'][$filter] ) && !in_array( $filter, array( 'byprice', 'meta', 'search_products' ) ) ) {
				return;
			}

			if ( !isset( self::$settings['widget'] ) ) {

				if ( self::$settings['instance']['wc_settings_prdctfltr_disable_bar'] == 'no' ) {
					if ( !in_array( self::$settings['instance']['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
						return;
					}
				}

			}

			if ( isset( $prdctfltr_global['sc_init'] ) && isset( $prdctfltr_global['sc_query'][$filter] ) && self::$settings['pf_activated'][$filter] == $prdctfltr_global['sc_query'][$filter] ) {
				return;
			}

			$meta = substr( $filter, 0, 4 ) == apply_filters( 'prdctfltr_meta_key_prefix', 'mta_' ) && isset( self::$settings['pf_activated'][$filter] ) ? ' data-slug="' . ( !is_array( self::$settings['pf_activated'][$filter] ) ? self::$settings['pf_activated'][$filter] : implode( ',', self::$settings['pf_activated'][$filter] ) ) . '"' : '';

			ob_start();

			switch( $filter ) {
				case 'byprice' :

					$min_price = strip_tags( wc_price( self::$settings['pf_activated']['min_price'] ) );
					if ( isset( self::$settings['pf_activated']['max_price'] ) && self::$settings['pf_activated']['max_price'] !== '' ) {
						$max_price = strip_tags( wc_price( self::$settings['pf_activated']['max_price'] ) );
					}

					echo $min_price . ( isset( $max_price ) ? ' - ' . $max_price : ' +' );

				break;
				case 'orderby' :
					echo self::catalog_ordering( self::$settings['pf_activated'][$filter] );
				break;
				case 'vendor' :
					echo get_userdata( intval( self::$settings['pf_activated'][$filter] ) )->display_name;
				break;
				case 'instock_products' :
					echo self::catalog_instock( self::$settings['pf_activated'][$filter] );
				break;
				case 's' :
				case 'search_products' :
					if ( isset( self::$settings['pf_activated']['s'] ) ) {
						echo ucfirst( self::$settings['pf_activated']['s'] );
					}
				break;
				default :
					if ( is_array( self::$settings['pf_activated'][$filter] ) ) {

						$pf_i=0;
						$pf_meta_title = '';

						foreach( self::$settings['pf_activated'][$filter] as $selected ) {

							$pf_meta_title .= ( $pf_i !== 0 ? ', ' : '' ) . ucfirst( $selected );
							$pf_i++;
						}
						echo $pf_meta_title;
					}
					else {
						echo ucfirst( self::$settings['pf_activated'][$filter] );
					}


				break;
			}

			$show_selected = ob_get_clean();

			if ( !empty( $show_selected ) ) {
				echo '<span class="prdctfltr_title_selected"><a href="#" class="prdctfltr_title_remove" data-key="' . $filter . '"' . $meta . '><i class="prdctfltr-delete"></i></a> <span class="prdctfltr_selected_title">' . $show_selected . '</span><span class="prdctfltr_title_selected_separator"> / </span></span>';
			}



		}

		public static function get_range_filter_selection( $attr ) {

			if ( self::$settings['instance']['wc_settings_prdctfltr_disable_showresults'] !== 'no' ) {
				return;
			}

			if ( !isset( self::$settings['pf_activated']['rng_min_' . $attr] ) ) {
				return;
			}

			if ( !isset( self::$settings['widget'] ) ) {

				if ( self::$settings['instance']['wc_settings_prdctfltr_disable_bar'] == 'no' ) {
					if ( !in_array( self::$settings['instance']['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
						return;
					}
				}

			}

			echo '<span class="prdctfltr_title_selected"><a href="#" class="prdctfltr_title_remove" data-key="rng_' . $attr . '"><i class="prdctfltr-delete"></i></a> <span>';

			if ( $attr == 'price' ) {
				echo strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', self::$settings['pf_activated']['rng_min_' . $attr] ) ) ) . ' - ' . strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', self::$settings['pf_activated']['rng_max_' . $attr] ) ) );
			}
			else {
				$pf_f_term = get_term_by('slug', self::$settings['pf_activated']['rng_min_' . $attr], $attr);
				$pf_s_term = get_term_by('slug', self::$settings['pf_activated']['rng_max_' . $attr], $attr);
				echo $pf_f_term->name . ' - ' . $pf_s_term->name;
			}

			echo '</span><span class="prdctfltr_title_selected_separator"> / </span></span>';

		}

		public static function get_dynamic_filter_title( $type, $attr, $p, $title ) {

			$args = apply_filters( 'prdctfltr_filter_title_args', array(
				'filter' => 'rng_' . $attr,
				'title' => $title,
				'before' => '<span class="prdctfltr_' . ( isset( self::$settings['widget'] ) ? 'widget' : 'regular' ) . '_title">',
				'after' => '</span>',
			) );

			extract( $args );

			echo $before;

			self::get_range_filter_selection( $attr );

			if ( self::$settings['instance']['wc_settings_prdctfltr_' . $type . '_filters']['pfr_title'][$p] != '' ) {
				echo self::$settings['instance']['wc_settings_prdctfltr_' . $type . '_filters']['pfr_title'][$p];
			}
			else {
				echo $title;
			}
		?>
			<i class="prdctfltr-down"></i>
		<?php
			echo $after;

		}

		public static function get_filter_title( $filter, $title, $option ) {

			$args = apply_filters( 'prdctfltr_filter_title_args', array(
				'filter' => $filter,
				'title' => $title,
				'before' => '<span class="prdctfltr_' . ( isset( self::$settings['widget'] ) ? 'widget' : 'regular' ) . '_title">',
				'after' => '</span>',
			) );

			extract( $args );

			echo $before;

			self::get_filter_selection( $filter );

			if ( $option !== 'meta' && self::$settings['instance']['wc_settings_prdctfltr_' . $option . '_title'] != '' ) {
				echo self::$settings['instance']['wc_settings_prdctfltr_' . $option . '_title'];
			}
			else {
				echo $title;
			}
		?>
			<i class="prdctfltr-down"></i>
		<?php
			echo $after;

		}

		public static function catalog_instock( $get = '' ) {

			$array = apply_filters( 'prdctfltr_catalog_instock', array(
				'both'    => __( 'All Products', 'prdctfltr' ),
				'in'  => __( 'In Stock', 'prdctfltr' ),
				'out' => __( 'Out Of Stock', 'prdctfltr' )
			) );

			if ( $get !=='' && array_key_exists( $get, $array ) ) {
				return $array[$get];
			}

			return $array;

		}

		public static function catalog_ordering( $get = '' ) {

			$pf_order_default = array(
				''                 => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) ),
				'comment_count'    => __( 'Review Count', 'prdctfltr' ),
				'popularity'       => __( 'Popularity', 'prdctfltr' ),
				'rating'           => __( 'Average rating', 'prdctfltr' ),
				'date'             => __( 'Newness', 'prdctfltr' ),
				'price'            => __( 'Price: low to high', 'prdctfltr' ),
				'price-desc'       => __( 'Price: high to low', 'prdctfltr' ),
				'rand'             => __( 'Random Products', 'prdctfltr' ),
				'title'            => __( 'Product Name', 'prdctfltr' )
			);

			if ( !empty( self::$settings['instance']['wc_settings_prdctfltr_include_orderby'] ) ) {

				foreach ( $pf_order_default as $k => $v ) {
					if ( $k !== '' && !in_array( $k, self::$settings['instance']['wc_settings_prdctfltr_include_orderby'] ) ) {
						unset( $pf_order_default[$k] );
					}
				}
			}

			$array = apply_filters( 'prdctfltr_catalog_orderby', $pf_order_default );

			if ( $get !=='' && array_key_exists( $get, $array ) ) {
				return $array[$get];
			}

			return $array;

		}

		public static function get_customization( $option ) {

			if ( $option !== '' ) {

				$get_customization = '';
				$language = WC_Prdctfltr::prdctfltr_wpml_language();

				if ( isset( $language ) && $language !== false ) {
					$get_customization = get_option( $option . '_' . $language, '' );
					$option = $option . '_' . $language;
				}

				if ( $get_customization == '' ) {
					$get_customization = get_option( $option, '' );
				}
				

				if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
					$customization_class = ' prdctfltr_terms_customized  prdctfltr_terms_customized_' . $get_customization['style'] . ' ' . $option;

					$customization = $get_customization;
					if ( $customization['style'] == 'text' ) {
						WC_Prdctfltr::add_customized_terms_css( $option, $customization );
					}
				}
			}
			if ( !isset( $customization ) ) {
				$customization = array();
				$customization_class = ' prdctfltr_text';
			}

			return array(
				'options' => $customization,
				'class' => $customization_class
			);

		}

		public static function get_top_bar_selected( $type = '' ) {

			$pf_activated = self::$settings['pf_activated'];

			if ( !empty( $pf_activated ) ) {

				global $prdctfltr_global;
				$active_filters = self::$settings['active_filters'];

				foreach( $pf_activated as $k => $v ) {

					if ( !array_key_exists( $k, $active_filters ) && substr( $k, 0, 4 ) !== 'rng_' && substr( $k, 0, 4 ) !== apply_filters( 'prdctfltr_meta_key_prefix', 'mta_' ) && !in_array( $k, array( 'products_per_page', 's', 'sale_products', 'instock_products', 'orderby', 'min_price', 'max_price', 'vendor' ) ) ) {
						continue;
					}

					if ( substr( $k, 0, 10 ) == 'rng_order_' || substr( $k, 0, 12 ) == 'rng_orderby_' || $k == 'order' ) {
						continue;
					}
					/* if ( isset( $prdctfltr_global['sc_init'] ) && isset( $prdctfltr_global['sc_query'] ) ) {
						if ( array_key_exists( $k, $prdctfltr_global['sc_query'] ) && $v == $prdctfltr_global['sc_query'][$k] ) {
							continue;
						}
					}*/

					$show = true;
					$add_parent = null;
					ob_start();

					switch( $k ) {
						case 'vendor' :
							if ( isset( $pf_activated['vendor'] ) && isset( $active_filters['vendor'] ) && in_array( $pf_activated['vendor'], $active_filters['vendor'] ) ) {
								$pf_vendor = get_userdata( intval ( $pf_activated['vendor'] ) );
								echo __( 'Vendor', 'prdctfltr' ) . ': '. $pf_vendor->display_name;
							}
						break;
						case 's' :
							if ( isset( $pf_activated['s'] ) && isset( $active_filters['s'] ) ) {
								echo __( 'Search', 'prdctfltr' ) . ': '. $pf_activated['s'];
							}
						break;
						case 'products_per_page' :
							if ( isset( $pf_activated['products_per_page'] ) ) {
								echo $pf_activated['products_per_page'] . ' ' . __( 'Products per page', 'prdctfltr' );
							}
						break;
						case 'sale_products' :
							if ( isset( $pf_activated['sale_products'] ) ) {
								echo __( 'Products on sale', 'prdctfltr' );
							}
						break;
						case 'instock_products' :
							if ( isset( $pf_activated['instock_products'] ) ) {
								$catalog_instock = self::catalog_instock();
								echo $catalog_instock[$pf_activated['instock_products']];
							}
						break;
						case 'orderby' :
							if ( isset( $pf_activated['orderby'] ) ) {
								$catalog_orderby = self::catalog_ordering();
								if ( $pf_activated['orderby'] !== $prdctfltr_global['default_order']['orderby'] && isset( $catalog_orderby[$pf_activated['orderby']] ) ) {
									echo $catalog_orderby[$pf_activated['orderby']];
								}
							}
						break;
						case 'min_price' :
							if ( isset( $pf_activated['min_price'] ) && $pf_activated['min_price'] !== '' ) {

								$min_price = $pf_activated['min_price'];

								if ( isset( $pf_activated['max_price'] ) && $pf_activated['max_price'] !== '' ) {
									$max_price = $pf_activated['max_price'];
								}
								else {
									$max_price = '+';
								}

								echo strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $min_price ) ) ) . ( isset( $max_price ) && $max_price > 0 ? ' - ' . strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $max_price ) ) ) : $max_price );

								$k = 'byprice';
							}
						break;
						case 'max_price' :
						break;
						case 'rng_min_price' :
							if ( isset( $pf_activated['rng_min_price'] ) && $pf_activated['rng_min_price'] !== '' ) {

								$min_price = $pf_activated['rng_min_price'];

								if ( isset( $pf_activated['rng_max_price'] ) && $pf_activated['rng_max_price'] !== '' ) {
									$max_price = $pf_activated['rng_max_price'];
								}
								else {
									$max_price = '+';
								}

								echo __( 'Price range', 'prdctfltr' ) . ' ' . strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $min_price ) ) ) . ' &rarr; ' . strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $max_price ) ) );

								$k = 'rng_price';
							}
						break;
						case 'rng_max_price' :
						break;
						default :
							if ( substr( $k, 0, 4 ) == 'rng_' ) {

								$true_val = substr($k, 8);

								if ( substr($k, 0, 8) == 'rng_max_' || $k == 'rng_min_price' || $k == 'rng_max_price' ) {
									continue;
								}

								if ( term_exists( $v, $true_val ) !== null ) {
									$curr_term = get_term_by( 'slug', $v, $true_val );
									$curr_selected['min'] = $curr_term->name;
								}
								if ( isset( $pf_activated['rng_max_' . $true_val] ) ) {
									if ( term_exists( $pf_activated['rng_max_' . $true_val], $true_val ) !== null ) {
										$curr_term = get_term_by( 'slug', $pf_activated['rng_max_' . $true_val], $true_val );
										$curr_selected['max'] = $curr_term->name;
									}
								}

								echo __( 'From', 'prdctfltr' ) . ' ' . $curr_selected['min'] . ' ' . __( 'to' , 'prdctfltr' ) . ' ' . $curr_selected['max'];

							}
							else if ( substr( $k, 0, 4 ) == apply_filters( 'prdctfltr_meta_key_prefix', 'mta_' ) ) {
								if ( isset( $pf_activated[$k] ) ) {

									$curr_selected = isset( $pf_activated[$k] ) ? $pf_activated[$k] : array();

									$pf_i=0;
									$pf_meta_title = '';
									$pf_meta_selected = '';
									$pf_meta_active = false;

									foreach( $curr_selected as $selected ) {

										/*if ( !in_array( $selected, $active_filters[$k] ) ) {
											continue;
										}*/

										$pf_meta_title .= ( $pf_i !== 0 ? ', ' : '' ) . ucfirst( $selected );
										$pf_meta_selected .= ( $pf_i !== 0 ? ',' : '' ) . $selected;

										$pf_i++;
										$pf_meta_active = true;
									}
									if ( $pf_meta_active ) {
										echo $pf_meta_title;
									}
									$add_parent = ' data-slug="' . $pf_meta_selected . '"';
								}
							}
							else {

								if ( array_key_exists( $k, $prdctfltr_global['range_filters'] ) ) {
									continue;
								}

								if ( $k == 'cat' || $k == 'tag' ) {
									$k = 'product_' . $k;
								}

								$curr_selected = isset( $pf_activated[$k] ) ? $pf_activated[$k] : array();

								if ( substr( $k, 0, 3 ) == 'pa_' && $v !== '' ) {
									$pf_attr_title = wc_attribute_label( $k ) . ': ';
								}
								else {
									$pf_attr_title = '';
								}

								$pf_attr_slug = '';

								$pf_i=0;
								$pf_attr_active = false;
								$pf_attr_parent = array();

								foreach( $curr_selected as $selected ) {
									if ( !in_array( $selected, $active_filters[$k] ) ) {
										continue;
									}

									if ( term_exists( $selected, $k ) !== null ) {
										$curr_term = get_term_by( 'slug', $selected, $k );

										$pf_attr_title .= ( $pf_i !== 0 ? ', ' : '' ) . $curr_term->name;
										$pf_attr_slug .= ( $pf_i !== 0 ? ',' : '' ) . $curr_term->slug;
										$pf_attr_parent[] = $curr_term->parent;

										$pf_i++;
										$pf_attr_active = true;
									}

								}

								$doNot = null;
								if ( !empty( $pf_attr_parent ) ) {
									$firstValue = current( $pf_attr_parent );
									foreach ( $pf_attr_parent as $val ) {
										if ( $firstValue !== $val ) {
											$doNot = true;
										}
									}
									if ( !isset( $doNot ) && $pf_attr_parent[0] !== 0 ) {
										$curr_parent = get_term_by( 'id', $pf_attr_parent[0], $k );
									}
								}

								if ( $pf_attr_active ) {
									echo $pf_attr_title;
								}
								$k = ( $k == 'characteristics' ? 'char' : $k );
								$add_parent = ' data-slug="' . ( isset( $curr_parent ) ? $curr_parent->slug . '>' : '' ) . $pf_attr_slug . '"';

							}

						break;
					}
					$selected = ob_get_clean();
					$separator = $type == '' ? apply_filters( 'prdctfltr_top_bar_selected_separator', '<span class="prdctfltr_title_selected_separator"> / </span>' ) : '';

					if ( !empty( $selected ) ) {
						echo '<span class="prdctfltr_title_selected" data-filter="' . $k . '">' . $separator . '<span>' . $selected .  ' </span>' . ( $show === true ? ' <a href="#" class="prdctfltr_title_remove" data-key="' . $k . '"' . ( isset( $add_parent ) ? $add_parent : '' ) . '><i class="prdctfltr-delete"></i></a>' : '' ) . '</span>';
					}

				}
			}

		}

		public static function check_meta_settings() {
			return array(
				'pfm_title' => '',
				'pfm_description' => '',
				'pfm_key' => '',
				'pfm_compare' => '',
				'pfm_type' => '',
				'pfm_term_customization' => '',
				'pfm_filter_customization' => ''
			);
		}

		public static function check_advanced_settings() {
			return array(
				'pfa_title' => '',
				'pfa_description' => '',
				'pfa_include' => array(),
				'pfa_orderby' => 'name',
				'pfa_order' => 'ASC',
				'pfa_multi' => 'no',
				'pfa_relation' => 'IN',
				'pfa_adoptive' => 'no',
				'pfa_none' => 'no',
				'pfa_hierarchy' => 'no',
				'pfa_hierarchy_mode' => 'no',
				'pfa_mode' => 'showall',
				'pfa_style' => 'pf_attr_text',
				'pfa_limit' => 0,
				'pfm_term_customization' => ''
			);
		}

		public static function check_range_settings() {
			return array(
				'pfr_title' => '',
				'pfr_description' => '',
				'pfr_taxonomy' => '',
				'pfr_include' => array(),
				'pfr_orderby' => 'name',
				'pfr_order' => 'ASC',
				'pfr_style' => 'no',
				'pfr_grid' => 'no',
				'pfr_adoptive' => 'no',
				'pfr_custom' => ''
			);
		}

		public static function make_filter() {

			global $wp_query;

			if ( is_shop() || is_product_taxonomy() || is_search() || isset( $wp_query->query_vars['wc_query'] ) && $wp_query->query_vars['wc_query'] == 'product_query' ) {
				$pf_paged = max( 1, $wp_query->get( 'paged' ) );
				$pf_per_page = $wp_query->get( 'posts_per_page' );
				$pf_total = $wp_query->found_posts;
				$pf_first = ( $pf_per_page * $pf_paged ) - $pf_per_page + 1;
				$pf_last = $wp_query->get( 'offset' ) > 0 ? min( $pf_total, $wp_query->get( 'offset' ) + $wp_query->get( 'posts_per_page' ) ) : min( $pf_total, $wp_query->get( 'posts_per_page' ) * $pf_paged );
				$pf_request = $wp_query->request;

				WC_Prdctfltr::$settings['ajax_query_vars'] = $wp_query->query_vars;
			}
			else if ( isset( WC_Prdctfltr::$settings['sc_instance'] ) ) {
				$pf_paged = WC_Prdctfltr::$settings['sc_instance']['paged'];
				$pf_per_page = WC_Prdctfltr::$settings['sc_instance']['per_page'];
				$pf_total = WC_Prdctfltr::$settings['sc_instance']['total'];
				$pf_first = WC_Prdctfltr::$settings['sc_instance']['first'];
				$pf_last = WC_Prdctfltr::$settings['sc_instance']['last'];
				$pf_request = WC_Prdctfltr::$settings['sc_instance']['request'];
			}
			else {
				$pf_paged = 1;

				$default_args = array(
					'prdctfltr'				=> 'active',
					'wc_query'				=> 'product_query',
					'post_type'				=> 'product',
					'post_status'			=> 'publish',
					'posts_per_page'		=> apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
					'paged'					=> $pf_paged,
					'meta_query'			=> array(
						array(
							'key'			=> '_visibility',
							'value'			=> array( 'catalog', 'visible' ),
							'compare'		=> 'IN'
						)
					)
				);

				$products = new WP_Query( $default_args );

				$pf_per_page = $products->get( 'posts_per_page' );
				$pf_total = $products->found_posts;
				$pf_first = ( $pf_per_page * $pf_paged ) - $pf_per_page + 1;
				$pf_last = $products->get( 'offset' ) > 0 ? min( $pf_total, $products->get( 'offset' ) + $products->get( 'posts_per_page' ) ) : min( $pf_total, $products->get( 'posts_per_page' ) * $pf_paged );
				$pf_request = $products->request;
			}

			self::$settings['instance'] = array(
				'paged'     => $pf_paged,
				'per_page'  => $pf_per_page,
				'total'     => $pf_total,
				'first'     => $pf_first,
				'last'      => $pf_last,
				'request'   => $pf_request
			);

			self::prdctfltr_get_settings();

		}

		public static function get_top_bar_showing() {
			$pf_step_filter = isset( self::$settings['instance']['step_filter'] ) ? 'yes' : '';

			if ( self::$settings['instance']['wc_settings_prdctfltr_noproducts'] !== '' && self::$settings['instance']['total'] == 0 ) {
				echo ' / ' . __( 'No products found!', 'prdctfltr' );
			}
			else if ( self::$settings['instance']['total'] == 0 ) {
				echo ' / ' . __( 'No products found!', 'prdctfltr' );
			}
			else if ( self::$settings['instance']['total'] == 1 ) {
				if ( $pf_step_filter !== '' ) {
					echo ' / ' . __( 'Found a single result', 'prdctfltr' );
				}
				else {
					echo ' / ' . __( 'Showing the single result', 'prdctfltr' );
				}
			}
			else if ( self::$settings['instance']['total'] <= self::$settings['instance']['per_page'] || -1 == self::$settings['instance']['per_page'] ) {
				if ( $pf_step_filter !== '' ) {
					echo ' / ' . __( 'Found', 'prdctfltr') . ' ' . self::$settings['instance']['total'] . ' ' . __( 'results', 'prdctfltr' );
				}
				else {
					echo ' / ' . __( 'Showing all', 'prdctfltr') . ' ' . self::$settings['instance']['total'] . ' ' . __( 'results', 'prdctfltr' );
				}
			}
			else {
				if ( $pf_step_filter !== '' ) {
					echo ' / ' . __( 'Found', 'prdctfltr' ) . ' ' . self::$settings['instance']['total'] . ' ' . __( 'results', 'prdctfltr' );
				}
				else {
					echo ' / ' . __( 'Showing', 'prdctfltr' ) . ' ' . self::$settings['instance']['first'] . ' - ' . self::$settings['instance']['last'] . ' ' . __( 'of', 'prdctfltr' ) . ' ' . self::$settings['instance']['total'] . ' ' . __( 'results', 'prdctfltr' );
				}
			}

		}

		public static function get_top_bar() {

			if ( !isset( self::$settings['widget'] ) && self::$settings['instance']['wc_settings_prdctfltr_disable_bar'] == 'no' ) {

				$icon = self::$settings['instance']['wc_settings_prdctfltr_icon'];
			?>
				<span class="prdctfltr_filter_title">
					<a class="prdctfltr_woocommerce_filter<?php echo ' pf_ajax_' . ( self::$settings['instance']['wc_settings_prdctfltr_loader'] !== '' ? self::$settings['instance']['wc_settings_prdctfltr_loader'] : 'oval' ); ?>" href="#"><i class="<?php echo ( $icon == '' ? 'prdctfltr-bars' : $icon ); ?>"></i></a>
					<span class="prdctfltr_woocommerce_filter_title">
				<?php
					if ( self::$settings['instance']['wc_settings_prdctfltr_title'] !== '' ) {
						echo self::$settings['instance']['wc_settings_prdctfltr_title'];
					}
					else {
						_e( 'Filter products', 'prdctfltr' );
					}
				?>
					</span>
				<?php
					if ( self::$settings['instance']['wc_settings_prdctfltr_disable_showresults'] == 'no' ) {
						self::get_top_bar_selected();
						self::get_top_bar_showing();
					}
				?>
				</span>
			<?php
			}

		}

		public static function get_action() {

			global $prdctfltr_global;

			$action = '';

			if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_home() ) {
				if ( self::$settings['wc_settings_prdctfltr_force_action'] == 'yes' ) {
					if ( is_product_taxonomy() ) {
						$action = ' action=""';
					}
					else {
						$action = ' action="' . get_permalink( self::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) ) . '"';
					}
				}
				else {
					$action = ' action="' . get_permalink( self::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) ) . '"';
				}
			}
			else if ( is_page() ) {
				global $wp;
				if ( self::$settings['permalink_structure'] == '' ) {
					$action = ' action="' . esc_url( remove_query_arg( array( 'page', 'paged' ), esc_url( add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) ) ) ) . '"';
				} else {
					$action = ' action="' . preg_replace( '%\/page/[0-9]+%', '', home_url( $wp->request ) ) . '"';
				}
			}
			else {
				$action = ' action="' . get_permalink( self::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) ) . '"';
			}

			if ( isset( self::$settings['instance']['wc_settings_prdctfltr_custom_action'] ) && !empty( self::$settings['instance']['wc_settings_prdctfltr_custom_action'] ) ) {
				$action = ' action="' . esc_url( self::$settings['instance']['wc_settings_prdctfltr_custom_action'] ) . '"';
			}

			if ( isset( $prdctfltr_global['action'] ) && $prdctfltr_global['action'] !== '' ) {
				$action = ' action="' . esc_url( $prdctfltr_global['action'] ) . '"';
			}

			return $action;

		}

		public static function get_meta_compare( $compare ) {

			switch ( $compare ) {

				case 11 :
				case '!=' :
					return $compare !== '!=' ? '!=' : 11;
				break;

				case 12 :
				case '>' :
					return $compare !== '>' ? '>' : 12;
				break;

				case 13 :
				case '<' :
					return $compare !== '<' ? '<' : 13;
				break;

				case 14 :
				case '>=' :
					return $compare !== '>=' ? '>=' : 14;
				break;

				case 15 :
				case '<=' :
					return $compare !== '<=' ? '<=' : 15;
				break;

				case 16 :
				case 'LIKE' :
					return $compare !== 'LIKE' ? 'LIKE' : 16;
				break;

				case 17 :
				case 'NOT LIKE' :
					return $compare !== 'NOT LIKE' ? 'NOT LIKE' : 17;
				break;

				case 18 :
				case 'IN' :
					return $compare !== 'IN' ? 'IN' : 18;
				break;

				case 19 :
				case 'NOT IN' :
					return $compare !== 'NOT IN' ? 'NOT IN' : 19;
				break;

				case 20 :
				case 'EXISTS' :
					return $compare !== 'EXISTS' ? 'EXISTS' : 20;
				break;

				case 21 :
				case 'NOT EXISTS' :
					return $compare !== 'NOT EXISTS' ? 'NOT EXISTS' : 21;
				break;

				case 22 :
				case 'NOT EXISTS' :
					return $compare !== 'NOT EXISTS' ? 'NOT EXISTS' : 22;
				break;

				case 23 :
				case 'BETWEEN' :
					return $compare !== 'BETWEEN' ? 'BETWEEN' : 23;
				break;

				case 24 :
				case 'NOT BETWEEN' :
					return $compare !== 'NOT BETWEEN' ? 'NOT BETWEEN' : 24;
				break;

				case 10 :
				case '=' :
				default :
					return $compare !== '=' ? '=' : 10;
				break;
			}

		}

		public static function get_meta_type( $type ) {

			switch ( $type ) {

				case 1 :
				case 'BINARY' :
					return $type !== 'BINARY' ? 'BINARY' : 1;
				break;

				case 2 :
				case 'CHAR' :
					return $type !== 'CHAR' ? 'CHAR' : 2;
				break;

				case 3 :
				case 'DATE' :
					return $type !== 'DATE' ? 'DATE' : 3;
				break;

				case 4 :
				case 'DATETIME' :
					return $type !== 'DATETIME' ? 'DATETIME' : 4;
				break;

				case 5 :
				case 'DECIMAL' :
					return $type !== 'DECIMAL' ? 'DECIMAL' : 5;
				break;

				case 6 :
				case 'SIGNED' :
					return $type !== 'SIGNED' ? 'SIGNED' : 6;
				break;

				case 7 :
				case 'UNSIGNED' :
					return $type !== 'UNSIGNED' ? 'UNSIGNED' : 7;
				break;

				case 8 :
				case 'TIME' :
					return $type !== 'TIME' ? 'TIME' : 8;
				break;

				case 0 :
				case 'NUMERIC' :
				default :
					return $type !== 'NUMERIC' ? 'NUMERIC' : 0;
				break;
			}

		}

		public static function build_meta_key( $key, $compare, $type ) {
			return apply_filters( 'prdctfltr_meta_key_prefix', 'mta_' ) . $key . '_' . self::get_meta_type( $type ) . '_' . self::get_meta_compare( $compare );
		}

		public static function get_filter_tag_parameters() {

			$curr_styles = self::prdctfltr_get_styles();

			echo 'class="prdctfltr_wc prdctfltr_woocommerce woocommerce ' . ( isset( self::$settings['widget'] ) ? 'prdctfltr_wc_widget' : 'prdctfltr_wc_regular' ) . ' ' . preg_replace( '/\s+/', ' ', implode( $curr_styles, ' ' ) ) . '"';
			echo isset( self::$settings['widget'] ) ? ' data-preset="' . self::$settings['widget']['style'] . '" data-template="' . self::$settings['widget']['preset'] . '"' : '';
			echo self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes'? ' data-page="' . self::$settings['instance']['paged'] . '"' : '';
			echo ' data-loader="' . ( self::$settings['instance']['wc_settings_prdctfltr_loader'] !== '' ? self::$settings['instance']['wc_settings_prdctfltr_loader'] : 'oval' ) . '"';
			echo ( WC_Prdctfltr::prdctfltr_wpml_language() !== false ? ' data-lang="' . ICL_LANGUAGE_CODE . '"' : '' );
			echo self::$settings['wc_settings_prdctfltr_use_analytics'] == 'yes' ? ' data-nonce="' . $nonce = wp_create_nonce( 'prdctfltr_analytics' ) . '"' : '';

		}


	}

	add_action( 'woocommerce_init', array( 'WC_Prdctfltr', 'init' ) );

?>