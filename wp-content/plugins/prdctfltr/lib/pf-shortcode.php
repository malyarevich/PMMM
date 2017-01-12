<?php


	if ( ! defined( 'ABSPATH' ) ) exit;

	class WC_Prdctfltr_Shortcodes {

		public static $settings;

		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		function __construct() {

			add_shortcode( 'prdctfltr_sc_products', __CLASS__ . '::prdctfltr_sc_products' );
			add_shortcode( 'prdctfltr_sc_get_filter', __CLASS__ . '::prdctfltr_sc_get_filter' );

			add_action( 'woocommerce_before_subcategory', __CLASS__. '::add_category_support', 10, 1 );

			add_action( 'wp_ajax_nopriv_prdctfltr_respond_550', __CLASS__ . '::prdctfltr_respond_550' );
			add_action( 'wp_ajax_prdctfltr_respond_550', __CLASS__ . '::prdctfltr_respond_550' );

		}

		public static function add_category_support( $category ) {

			echo '<span class="prdctfltr_cat_support" style="display:none!important;" data-slug="' . $category->slug . '"></span>';

		}

		public static function get_categories() {

			global $wp_query, $prdctfltr_global;

			$defaults = array(
				'before'        => '',
				'after'         => '',
				'force_display' => false
			);

			$args = array();

			$args = wp_parse_args( $args, $defaults );

			extract( $args );

			$selected_term = isset( $prdctfltr_global['active_taxonomies']['product_cat'][0] ) ? $prdctfltr_global['active_taxonomies']['product_cat'][0] : '';

			if ( $selected_term !== '' ) {
				if ( term_exists( $selected_term, 'product_cat' ) ) {
					$term = get_term_by( 'slug', $selected_term, 'product_cat' );
				}
			}
			if ( !isset( $term ) ) {
				$term = (object) array( 'term_id' => 0 );
			}

			$parent_id = ( $term->term_id == 0 ? 0 : $term->term_id );

			$product_categories = get_categories( apply_filters( 'woocommerce_product_subcategories_args', array(
				'parent'       => $parent_id,
				'menu_order'   => 'ASC',
				'hide_empty'   => 0,
				'hierarchical' => 1,
				'taxonomy'     => 'product_cat',
				'pad_counts'   => 1
			) ) );

			if ( $product_categories ) {
				echo $before;

				foreach ( $product_categories as $category ) {
					wc_get_template( 'content-product_cat.php', array(
						'category' => $category
					) );
				}

				if ( $term->term_id !== 0 ) {
					$display_type = get_woocommerce_term_meta( $term->term_id, 'display_type', true );

					switch ( $display_type ) {
						case 'subcategories' :
							$wp_query->post_count    = 0;
							$wp_query->max_num_pages = 0;
						break;
						case '' :
							if ( get_option( 'woocommerce_category_archive_display' ) == 'subcategories' ) {
								$wp_query->post_count    = 0;
								$wp_query->max_num_pages = 0;
							}
						break;
					}
				}

				if ( $term->term_id == 0 && get_option( 'woocommerce_shop_page_display' ) == 'subcategories' ) {
					$wp_query->post_count    = 0;
					$wp_query->max_num_pages = 0;
				}

				echo $after;

				return true;
			}

		}

		public static function prdctfltr_sc_products( $atts, $content = null ) {

			$shortcode_atts = shortcode_atts( array(
				'preset' => '',
				'rows' => 4,
				'columns' => 4,
				'cat_columns' => 4,
				'fallback_css' => 'no',
				'ajax' => 'no',
				'pagination' => 'yes',
				'use_filter' => 'yes',
				'show_categories' => 'no',
				'show_products' => 'yes',
				'min_price' => '',
				'max_price' => '',
				'orderby' => '',
				'order' => '',
				'product_cat'=> '',
				'product_tag'=> '',
				'product_characteristics'=> '',
				'product_attributes'=> '',
				'sale_products' => '',
				'instock_products' => '',
				'http_query' => '',
				'disable_overrides' => 'yes',
				'disable_woo_filter' => 'no',
				'action' => '',
				'bot_margin' => 36,
				'class' => '',
				'shortcode_id' => ''
			), $atts );

			extract( $shortcode_atts );

			global $prdctfltr_global;

			$prdctfltr_global['sc_init'] = true;
			$prdctfltr_global['sc_products'] = true;

			if ( $disable_woo_filter == 'yes' ) {
				remove_filter( 'woocommerce_shortcode_products_query', array( 'WC_Prdctfltr', 'add_wc_shortcode_filter' ), 999999 );
			}

			$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : get_query_var( 'paged' );

			if ( $paged < 1 ) {
				$paged = 1;
			}

			$prdctfltr_global['unique_id'] = uniqid( 'prdctfltr-' );
			$prdctfltr_global['action'] = ( $action !== '' ? $action : '' );
			$prdctfltr_global['preset'] = ( $preset !== '' ? $preset : '' );
			$prdctfltr_global['disable_overrides'] = ( $disable_overrides == 'yes' ? 'yes' : 'no' );

			$query_args = array (
				'prdctfltr'				=> 'active',
				'post_type'				=> 'product',
				'post_status'			=> 'publish',
				'posts_per_page'		=> $columns*$rows,
				'paged'					=> $paged,
				'wc_query'				=> 'product_query',
				'meta_query'			=> array(
					array(
						'key'			=> '_visibility',
						'value'			=> array( 'catalog', 'visible' ),
						'compare'		=> 'IN'
					)
				)
			);

			$args = array();

			if ( $show_categories == 'yes' && $ajax == 'yes' ) {
				$prdctfltr_global['categories_active'] = true;
			}
			if ( $orderby !== '' ) {
				$args['orderby'] = $orderby;
			}
			if ( $order !== '' ) {
				$args['order'] = $order;
			}
			if ( $min_price !== '' ) {
				$args['min_price'] = $min_price;
			}
			if ( $max_price !== '' ) {
				$args['max_price'] = $max_price;
			}
			if ( $product_cat !== '' ) {
				$args['product_cat'] = $product_cat;
			}
			if ( $product_tag !== '' ) {
				$args['product_tag'] = $product_tag;
			}
			if ( $product_characteristics !== '' ) {
				$args['characteristics'] = $product_characteristics;
			}
			if ( $instock_products !== '' ) {
				$args['instock_products'] = $instock_products;
			}
			if ( $sale_products !== '' ) {
				$args['sale_products'] = $sale_products;
			}
			if ( $http_query !== '' ) {
				parse_str( html_entity_decode( $http_query ), $curr_http_args );
				$args = array_merge( $args, $curr_http_args );
			}
			$curr_request = isset( $_REQUEST ) ? $_REQUEST : array();

			$prdctfltr_global['sc_query'] = $args;

			WC_Prdctfltr::make_global( $curr_request, 'AJAX' );

			if ( $ajax == 'yes' ) {
				$add_ajax = ' data-page="' . $paged . '"';

				$prdctfltr_global['ajax_js'] = $args;
				$prdctfltr_global['ajax_atts'] = $atts;
				$prdctfltr_global['sc_ajax'] = true;
			}

			$prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['query_args'] = array_merge( $query_args, $args );

			$prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts'] = $shortcode_atts;
			$prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['args'] = $args;
			$prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['request'] = $curr_request;

			$bot_margin = ( int ) $bot_margin;
			$margin = " style='margin-bottom:" . $bot_margin . "px'";


			$opt = array(
				'pf_request' => array(),
				'pf_requested' => array(),
				'pf_filters' => array(),
				'pf_mode' => 'archive',
				'pf_widget_title' => null,
				'pf_set' => 'shortcode',
				'pf_paged' => $paged,
				'pf_pagefilters' => array(),
				'pf_shortcode' => '',
				'pf_offset' => 0,
				'pf_restrict' => '',
				'pf_adds' => array(),
				'pf_orderby_template' => null,
				'pf_count_template' => null
			);

			self::$settings['opt'] = $opt;

			$query = self::make_query();

			ob_start();

			if ( $use_filter == 'yes' ) {
				include( WC_Prdctfltr::$dir . 'woocommerce/loop/product-filter.php' );
			}

			echo self::get_products( $query );

			$pagination_args = array(
				'sc' => 'yes',
				'ajax' => $ajax,
				'type' => $pagination
			);

			echo self::get_pagination( $pagination_args );

			$cached = ob_get_clean();

			$prdctfltr_global['unique_id'] = null;

			if ( isset( WC_Prdctfltr::$settings['adoptive_terms'] ) ) {
				unset( WC_Prdctfltr::$settings['adoptive_terms'] );
			}

			wp_reset_query();
			wp_reset_postdata();
			$prdctfltr_global['sc_products'] = null;

			return '<div' . ( $shortcode_id !== '' ? ' id="' . $shortcode_id.'"' : '' ) . ' class="prdctfltr_sc_products woocommerce ' . 'columns-' . $columns . ( $ajax == 'yes' ? ' prdctfltr_ajax' : '' ) . ( $fallback_css == 'yes' ? ' prdctfltr_fallback_css prdctfltr_columns_fallback_' . $columns : '' ) . ( $class !== '' ? ' ' . $class : '' ) . '"' . $margin . ( $ajax == 'yes' ? $add_ajax : '' ) . '>' . do_shortcode( $cached ) . '</div>';

		}

		public static function prdctfltr_respond_550() {

			if ( !is_array( $_POST ) ) {
				die(0);
				exit;
			}

			$set = array(
				'pf_request' => array(),
				'pf_requested' => array(),
				'pf_filters' => array(),
				'pf_mode' => 'archive',
				'pf_widget_title' => null,
				'pf_set' => 'shortcode',
				'pf_paged' => '',
				'pf_pagefilters' => array(),
				'pf_shortcode' => null,
				'pf_offset' => 0,
				'pf_restrict' => '',
				'pf_adds' => array(),
				'pf_orderby_template' => null,
				'pf_count_template' => null,
				'pf_singlesc' => null,
				'pf_ajax_query_vars' => '',
				'pf_url' => ''
			);

			$opt = array();

			foreach( $set as $k => $v ) {
				if ( isset( $_POST[$k] ) && $_POST[$k] !== '' ) {
					$opt[$k] = $_POST[$k];
				}
				else {
					$opt[$k] = $v;
				}
			}

			self::$settings['opt'] = $opt;

			$pf_request = isset( $opt['pf_request'] ) ? $opt['pf_request'] : array();
			$pf_requested = isset( $opt['pf_requested'] ) ? $opt['pf_requested'] : array();

			if ( empty( $pf_request ) || empty( $pf_requested ) ) {
				die(0);
				exit;
			}

			global $prdctfltr_global;
			$prdctfltr_global['pagefilters'] = $opt['pf_pagefilters'];
			$prdctfltr_global['unique_id'] = key( $pf_requested );

			$active_filters = isset( $opt['pf_filters'] ) && is_array( $opt['pf_filters'] ) ? $opt['pf_filters'] : array();

			$curr_filters = array();

			foreach ( $active_filters as $k => $v ) {
				$curr_filters = array_merge( $curr_filters, $v );
			}

			if ( $opt['pf_set'] == 'shortcode' ) {

				$prdctfltr_global['sc_init'] = true;
				$prdctfltr_global['sc_query'] = $opt['pf_shortcode'];
				$prdctfltr_global['active_permalinks'] = $opt['pf_shortcode'];

				if ( isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts'] ) ) {
					extract( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts'] );
					$prdctfltr_global['ajax_js'] = isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['args'] ) ? $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['args'] : array();
					$prdctfltr_global['ajax_atts'] = isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts_sc'] ) ? $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts_sc'] : array();
				}
				else if ( is_array( $prdctfltr_global['pagefilters'] ) ) {
					$pf_pagefilters = $prdctfltr_global['pagefilters'];
					reset( $pf_pagefilters );
					$sc_key = key( $pf_pagefilters );
					extract( $prdctfltr_global['pagefilters'][$sc_key]['atts'] );
					$prdctfltr_global['ajax_js'] = isset( $prdctfltr_global['pagefilters'][$sc_key]['args'] ) ? $prdctfltr_global['pagefilters'][$sc_key]['args'] : array();
					$prdctfltr_global['ajax_atts'] = isset( $prdctfltr_global['pagefilters'][$sc_key]['atts_sc'] ) ? $prdctfltr_global['pagefilters'][$sc_key]['atts_sc'] : array();
				}

				$prdctfltr_global['sc_ajax'] = true;

				$prdctfltr_global['action'] = ( $action !== '' ? $action : '' );
				$prdctfltr_global['preset'] = ( $preset !== '' ? $preset : '' );
				$prdctfltr_global['disable_overrides'] = ( $disable_overrides == 'yes' ? 'yes' : 'no' );

				$pagination_args = array(
					'sc' => 'yes',
					'ajax' => 'yes',
					'type' => $pagination
				);

			}
			else {

				$prdctfltr_global['ajax_adds'] = $opt['pf_adds'];
				$curr_filters = array_merge( $opt['pf_adds'], $curr_filters );

				$pagination_args = array();
				$use_filter = 'yes';

			}

			if ( !isset( $prdctfltr_global['done_filters'] ) || $prdctfltr_global['done_filters'] !== true ) {
				WC_Prdctfltr::make_global( $curr_filters, 'AJAX' );
			}

			$data = array();

			$query = self::make_query();

			global $wp_query, $wp_rewrite;
			$wp_query = $query;

			/*if ( isset( $curr_filters['orderby'] ) && $curr_filters['orderby'] == $prdctfltr_global['default_order']['orderby'] ) {
				unset( $curr_filters['orderby'] );
			}*/
			$redirect = trailingslashit( preg_replace( '%\/page/[0-9]+%', '', $opt['pf_url'] ) );
			$_SERVER['REQUEST_URI'] = str_replace( get_bloginfo( 'url' ), '', $redirect );

			$filter_query_before = untrailingslashit( $redirect );
			if ( $query->query_vars['paged'] > 1 ) {
				global $paged;
				$paged = $query->query_vars['paged'];
				if ( WC_Prdctfltr::$settings['permalink_structure'] == '' ) {
					$filter_query_before = untrailingslashit( $redirect ) . '/' . '?paged=' . $query->query_vars['paged'];
				}
				else {
					$filter_query_before = untrailingslashit( $redirect ) . '/' . $wp_rewrite->pagination_base . '/' . $query->query_vars['paged'];
				}
			}

			foreach ( $curr_filters as $cfk => $cfv ) {

				if ( $opt['pf_set'] == 'shortcode' && array_key_exists( $cfk, $opt['pf_shortcode'] ) && $opt['pf_shortcode'][$cfk] == $cfv ) {
					continue;
				}

				if ( !isset( $filter_query ) ) {
					$filter_query = '/?' . $cfk . '=' . $cfv;
				}
				else {
					$filter_query .= '&' . $cfk . '=' . $cfv;
				}

			}

			$data['query'] = isset( $filter_query ) ? $filter_query_before . $filter_query : trailingslashit( $filter_query_before );

			if ( $opt['pf_set'] !== 'shortcode' ) {

				if ( WC_Prdctfltr::$settings['permalink_structure'] !== '' ) {

					$current = $wp_query->get_queried_object();

					if ( isset( $current->taxonomy ) && isset( $curr_filters[$current->taxonomy] ) ) {

						$rewrite = $wp_rewrite->get_extra_permastruct( $current->taxonomy );

						if ( $rewrite !== false ) {

							if ( strpos( $curr_filters[$current->taxonomy], ',' ) || strpos( $curr_filters[$current->taxonomy], '+' ) ) {
								if ( strpos( $curr_filters[$current->taxonomy], ',' ) ) {
									$terms = explode( ',', $curr_filters[$current->taxonomy] );
								}
								else if ( strpos( $curr_filters[$current->taxonomy], '+' ) ) {
									$terms = explode( ',', $curr_filters[$current->taxonomy] );
								}

								foreach( $terms as $term ) {
									$checked = get_term_by( 'slug', $term, $current->taxonomy );
									if ( !is_wp_error( $checked ) ) {
										/*if ( $checked->parent !== 0 ) {*/
											$parents[] = $checked->parent;
										/*}*/
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
													$get_parent = $checked->slug;
													$parent_slug =  $get_parent . '/' . $parent_slug;
													if ( $checked->parent !== 0 ) {
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

								$redirect = preg_replace( '/\?.*/', '', get_bloginfo( 'url' ) ) . '/' . str_replace( '%' . $current->taxonomy . '%', $parent_slug . $curr_filters[$current->taxonomy], $rewrite );
							}
							else {
								$link = get_term_link( $curr_filters[$current->taxonomy], $current->taxonomy );
								$redirect = preg_replace( '/\?.*/', '', $link );
							}

							unset( $curr_filters[$current->taxonomy] );

						}
						else {

							$redirect = get_permalink( WC_Prdctfltr::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) );

						}

						$redirect = untrailingslashit( $redirect );

						$_SERVER['REQUEST_URI'] = str_replace( get_bloginfo( 'url' ), '', trailingslashit( $redirect ) );

						if ( $query->query_vars['paged'] > 1 ) {
							$redirect = $redirect . '/' . $wp_rewrite->pagination_base . '/' . $query->query_vars['paged'];
						}

						if ( !empty( $curr_filters ) ) {

							$req = '';

							foreach( $curr_filters as $k => $v ) {
								if ( $v == '' || in_array( $k, apply_filters('prdctfltr_block_request', array( 'woocs_order_emails_is_sending' ) ) ) ) {
									continue;
								}

								$req .= $k . '=' . $v . '&';
							}

							$redirect = $redirect . '/?' . $req;

							if ( substr( $redirect, -1 ) == '&' ) {
								$redirect = substr( $redirect, 0, -1 );
							}

						}
						else {
							$redirect = trailingslashit( $redirect );
						}

						$data['query'] = $redirect;

					}
					else {

						$redirect = get_permalink( WC_Prdctfltr::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) );

						$redirect = untrailingslashit( $redirect );

						$_SERVER['REQUEST_URI'] = str_replace( get_bloginfo( 'url' ), '', trailingslashit( $redirect ) );

						if ( $query->query_vars['paged'] > 1 ) {
							$redirect = $redirect . '/' . $wp_rewrite->pagination_base . '/' . $query->query_vars['paged'];
						}

						if ( !empty( $curr_filters ) ) {

							$req = '';

							foreach( $curr_filters as $k => $v ) {
								if ( $v == '' || in_array( $k, apply_filters('prdctfltr_block_request', array( 'woocs_order_emails_is_sending' ) ) ) ) {
									continue;
								}

								$req .= $k . '=' . $v . '&';
							}

							$redirect = $redirect . '/?' . $req;

							if ( substr( $redirect, -1 ) == '&' ) {
								$redirect = substr( $redirect, 0, -1 );
							}

						}
						else {
							$redirect = trailingslashit( $redirect );
						}

						$data['query'] = $redirect;
					}
				}

			}

			foreach( $pf_request as $filter => $options ) {

				if ( in_array( $filter, $pf_requested ) ) {

					ob_start();

					$prdctfltr_global['unique_id'] = $filter;

					if ( $options['widget_search'] !== 'yes' ) {
						if ( $use_filter == 'yes' ) {
							if ( isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['preset'] ) && $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['preset'] !== '' ) {
								$prdctfltr_global['preset'] = $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['preset'];
							}
							include( WC_Prdctfltr::$dir . 'woocommerce/loop/product-filter.php' );
						}
					}
					else {

						$widget_options = $opt['pf_request'][$filter]['widget_options'];

						$defaults = array(
							'style' => 'pf_default',
							'preset' => '',
							'disable_overrides' => 'no',
							'action' => ''
						);

						foreach( $defaults as $k => $v ) {
							if ( !isset( $widget_options[$k] ) ) {
								$widget_options[$k] = $v;
							}
						}

						if ( isset( $opt['pf_widget_title'] ) ) {
							$curr_title = explode( '%%%', $opt['pf_widget_title'] );
						}

						the_widget( 'prdctfltr', 'preset=' . $widget_options['style'] . '&template=' . $widget_options['preset'] . '&disable_overrides=' . $widget_options['disable_overrides'], array( 'before_title' => stripslashes( $curr_title[0] ), 'after_title'=>stripslashes( $curr_title[1] ) ) );

					}

					$data[$filter] = ob_get_clean();

				}

			}

			$data['products'] = self::get_products( $query );

			if ( isset( $prdctfltr_global['ranges'] ) ) {
				$data['ranges'] = $prdctfltr_global['ranges'];
			}

			$data['pagination'] = self::get_pagination( $pagination_args );

			if ( isset( $opt['pf_count_template'] ) ) {

				if ( $wp_query->found_posts > 0 ) {
					ob_start();
					woocommerce_result_count();
					$data['count'] = ob_get_clean();
				}
				else {
					$data['count'] = '';
				}

			}

			if ( isset( $opt['pf_orderby_template'] ) ) {

				if ( !isset( $_GET['orderby'] ) && isset( $prdctfltr_global['active_filters']['orderby'] ) ) {
					$_GET['orderby'] = $prdctfltr_global['active_filters']['orderby'];
				}
				else if ( !isset( $_GET['orderby'] ) ) {
					$orderby  = WC_Prdctfltr::get_catalog_ordering_args();
					$_GET['orderby'] = $orderby;
				}

				if ( isset( $_GET['orderby'] ) ) {
					$orderby = $_GET['orderby'];
					ob_start();
					woocommerce_catalog_ordering();
					$data['orderby'] = ob_get_clean();
				}

			}

			wp_send_json($data);

		}

		public static function add_product_class( $classes ) {
			global $post;

			if ( !in_array( $post->post_type, $classes ) ) {
				$classes[] = $post->post_type;
			}

			return $classes;
		}


		public static function make_query() {

			global $prdctfltr_global, $woocommerce_loop;

			$opt = self::$settings['opt'];

			add_filter( 'post_class', __CLASS__ . '::add_product_class' );

			$per_page = apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) );
			$columns = apply_filters( 'loop_shop_columns', 4 );

			if ( $opt['pf_set'] == 'shortcode' ) {
				$sc_args = $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['args'];
				if ( isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['columns'] ) ) {
					$pf_col = intval( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['columns'] );
					$pf_row = intval( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['rows'] );
				}
				else if ( is_array( $prdctfltr_global['pagefilters'] ) ) {
					$pf_pagefilters = $prdctfltr_global['pagefilters'];
					reset( $pf_pagefilters );
					$sc_key = key( $pf_pagefilters );
					$pf_col = intval( $prdctfltr_global['pagefilters'][$sc_key]['atts']['columns'] );
					$pf_row = intval( $prdctfltr_global['pagefilters'][$sc_key]['atts']['rows'] );
				}


				$pf_per_page = $pf_col * $pf_row;
			}
			else {
				$pf_col = intval( WC_Prdctfltr::$settings['wc_settings_prdctfltr_ajax_columns'] );
				$pf_row = intval( WC_Prdctfltr::$settings['wc_settings_prdctfltr_ajax_rows'] );

				$pf_col = ( $pf_col > 0 ? $pf_col : 0 );
				$pf_row = ( $pf_row > 0 ? $pf_row : 0 );

				$pf_per_page = $pf_col*$pf_row;
				if ( $opt['pf_ajax_query_vars'] !== '' ) {
					$pf_ajax_args = $opt['pf_ajax_query_vars'];
					$pf_activated = isset( $prdctfltr_global['active_in_filter'] ) ? $prdctfltr_global['active_in_filter'] : array();
					if ( !isset( $pf_activated['orderby'] ) ) {
						$ordering = WC_Prdctfltr::get_catalog_ordering_args();
						if ( in_array( $ordering, array( 'popularity', 'rating' ) ) ) {
							switch ( $ordering ) {
								case 'popularity' :
									$pf_ajax_args['meta_key'] = 'total_sales';
									add_filter( 'posts_clauses', array( WC()->query, 'order_by_popularity_post_clauses' ) );
								break;
								case 'rating' :
									add_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
								break;
							}
						}
					}
				}

				$sc_args = array();

			}

			$args = array(
				'prdctfltr'				=> 'active',
				'wc_query'				=> 'product_query',
				'post_type'				=> 'product',
				'post_status'			=> 'publish',
				'posts_per_page' 		=> $pf_per_page > 0 ? $pf_per_page : $per_page,
				'paged'					=> $opt['pf_paged'],
				'meta_query'			=> array(
					array(
						'key'			=> '_visibility',
						'value'			=> array( 'catalog', 'visible' ),
						'compare'		=> 'IN'
					)
				)
			);
			//$args = array_merge( $args, $sc_args );

			if ( isset( $pf_ajax_args['orderby'] ) && !empty( $pf_ajax_args['orderby'] ) ) {
				$args['orderby'] = $pf_ajax_args['orderby'];
			}

			if ( isset( $pf_ajax_args['order'] ) && !empty( $pf_ajax_args['order'] ) ) {
				$args['order'] = $pf_ajax_args['order'];
			}

			if ( isset( $pf_ajax_args['meta_query'] ) && !empty( $pf_ajax_args['meta_query'] ) ) {
				$args['meta_query'] = $pf_ajax_args['meta_query'];
			}

			if ( isset( $pf_ajax_args['meta_key'] ) && !empty( $pf_ajax_args['meta_key'] ) ) {
				$args['meta_key'] = $pf_ajax_args['meta_key'];
			}

			if ( isset( $pf_ajax_args['meta_value'] ) && !empty( $pf_ajax_args['meta_value'] ) ) {
				$args['meta_value'] = $pf_ajax_args['meta_value'];
			}

			$offset = intval( $opt['pf_offset'] );

			if ( $offset > 0 ) {
				$args['offset'] =  $offset;
			}

			self::$settings['columns'] = $pf_col > 0 ? $pf_col : $columns;

			$products = new WP_Query( $args );

			$products->is_search = false;

			WC_Prdctfltr::$settings['sc_instance'] = array(
				'paged'			=> $opt['pf_paged'],
				'per_page'		=> $pf_per_page,
				'total'			=> $products->found_posts,
				'first'			=> ( $pf_per_page * $opt['pf_paged'] ) - $pf_per_page + 1,
				'last'			=> $offset > 0 ? min( $products->found_posts, $offset + $pf_per_page ) : min( $products->found_posts, $pf_per_page * $opt['pf_paged'] ),
				'request'		=> $products->request
			);

			return $products;

		}

		public static function get_pagination( $pagination ) {

			if ( isset( $pagination['type'] ) && $pagination['type'] == 'no' ) {
				return;
			}

			if ( !isset( self::$settings['instance'] ) ) {
				return;
			}

			if ( ( $custom_pagination = WC_Prdctfltr::$settings['wc_settings_prdctfltr_ajax_pagination'] ) !== '' ) {
				if ( function_exists( $custom_pagination ) ) {
					ob_start();
					call_user_func( $custom_pagination );
					$html = ob_get_clean();
					return $html;
				}
			}

			global $prdctfltr_global;

			if ( isset( $pagination['sc'] ) ) {

				$ajax = $pagination['ajax'];
				$type = $pagination['type'];

				if ( $ajax == 'yes' ) {
					switch ( $type ) {
						case 'yes' :
							$class = 'default';
						break;
						case 'override' :
							$class = 'prdctfltr-pagination-default';
						break;
						case 'loadmore' :
							$class = 'prdctfltr-pagination-load-more';
						break;
						default :
							$class = 'default';
						break;
					}
				}
				else {
					$class = 'default';
				}

				$prdctfltr_global['pagination_type'] = $class;

				global $wp_query, $wp_the_query;
				$remember_query = $wp_query;
				$wp_query = self::$settings['instance'];

				ob_start();

				if ( $prdctfltr_global['pagination_type'] == 'default' ) {

/*					if ( $ajax == 'yes' ) {
						add_filter( 'woocommerce_pagination_args', __CLASS__ . '::prdctfltr_pagination_filter', 999999, 1 );
					}*/

					wc_get_template( 'loop/pagination.php' );

				}
				else {
					include( WC_Prdctfltr::$dir . 'woocommerce/loop/pagination.php' );
				}

				$pagination = ob_get_clean();

				$wp_query = $remember_query;
			}
			else {
				$ajax = 'yes';
				$prdctfltr_global['pagination_type'] = WC_Prdctfltr::$settings['wc_settings_prdctfltr_pagination_type'];

				ob_start();

				wc_get_template( 'loop/pagination.php' );

				$pagination = ob_get_clean();
			}

			unset( $prdctfltr_global['pagination_type'] );

			return $pagination;

		}

		public static function get_products( $products ) {

			global $prdctfltr_global, $woocommerce_loop;

			$opt = self::$settings['opt'];

			$offset = intval( $opt['pf_offset'] );

			if ( isset( $prdctfltr_global['unique_id'] ) && isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['show_products'] ) ) {
				extract( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts'] );
			}
			else {
				$show_categories = 'archive';
				$cat_columns = '';
				$show_products = 'yes';
			}

			if ( $show_products == 'no' ) {
				return;
			}

			ob_start();

			self::$settings['instance'] = $products;

			$woocommerce_loop['columns'] = self::$settings['columns'];

			if ( $products->have_posts() ) {

				woocommerce_product_loop_start();

				if ( isset( $prdctfltr_global['categories_active'] ) && $prdctfltr_global['categories_active'] === true ) {

					if ( $show_categories == 'archive' ) {
						if ( isset( $cat_columns ) ) {
							$woocommerce_loop['columns'] = $cat_columns;
						}
						woocommerce_product_subcategories();
					}
					else if ( $show_categories == 'yes' ) {
						if ( isset( $cat_columns ) ) {
							$woocommerce_loop['columns'] = $cat_columns;
						}
						self::get_categories();
					}

				}

				if ( $offset > 0 ) {

					$curr_offset = $offset/$woocommerce_loop['columns'];
					$decimal = $curr_offset - (int) $curr_offset;

					if ( $decimal > 0 ) {
						$woocommerce_loop['loop'] = $decimal * $woocommerce_loop['columns'];
					}
					else {
						$woocommerce_loop['loop'] = 0;
					}

				}

				while ( $products->have_posts() ) : $products->the_post();

					$woocommerce_loop['columns'] = self::$settings['columns'];

					wc_get_template_part( 'content', 'product' );

				endwhile;

				woocommerce_product_loop_end();

			}
			else {
				include( WC_Prdctfltr::$dir . 'woocommerce/loop/no-products-found.php' );
			}

			return ob_get_clean();

		}

		public static function add_columns_filter() {
			return self::$settings['columns_ajax'];
		}

		public static function prdctfltr_sc_get_filter( $atts, $content = null ) {

			$shortcode_atts = shortcode_atts( array(
				'preset' => '',
				'rows' => 4,
				'ajax' => 'no',
				'disable_overrides' => 'yes',
				'disable_woo_filter' => 'no',
				'action' => '',
				'bot_margin' => 36,
				'class' => '',
				'shortcode_id' => ''
			), $atts );

			extract( $shortcode_atts );

			global $prdctfltr_global;

			$prdctfltr_global['sc_init'] = true;

			if ( $disable_woo_filter == 'yes' ) {
				remove_filter( 'woocommerce_shortcode_products_query', array( 'WC_Prdctfltr', 'add_wc_shortcode_filter' ), 999999 );
			}

			$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : get_query_var( 'paged' );

			if ( $paged < 1 ) {
				$paged = 1;
			}

			$prdctfltr_global['unique_id'] = uniqid( 'prdctfltr-' );
			$prdctfltr_global['action'] = ( $action !== '' ? $action : '' );
			$prdctfltr_global['preset'] = ( $preset !== '' ? $preset : '' );
			$prdctfltr_global['disable_overrides'] = ( $disable_overrides == 'yes' ? 'yes' : 'no' );

			$prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['query_args'] = array();
			$prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts'] = $shortcode_atts;
			$prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['args'] = array();
			$prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['request'] = array();

			if ( $ajax == 'yes' ) {
				$add_ajax = ' data-page="' . $paged . '"';
			}

			$bot_margin = ( int ) $bot_margin;
			$margin = " style='margin-bottom:" . $bot_margin . "px'";

			$opt = array(
				'pf_request' => array(),
				'pf_requested' => array(),
				'pf_filters' => array(),
				'pf_mode' => 'archive',
				'pf_widget_title' => null,
				'pf_set' => 'archive',
				'pf_paged' => $paged,
				'pf_pagefilters' => array(),
				'pf_shortcode' => '',
				'pf_offset' => 0,
				'pf_restrict' => '',
				'pf_adds' => array(),
				'pf_orderby_template' => null,
				'pf_count_template' => null
			);

			self::$settings['opt'] = $opt;

			ob_start();

			include( WC_Prdctfltr::$dir . 'woocommerce/loop/product-filter.php' );

			$cached = ob_get_clean();

			$prdctfltr_global['unique_id'] = null;
			wp_reset_query();
			wp_reset_postdata();

			return '<div' . ( $shortcode_id !== '' ? ' id="' . $shortcode_id.'"' : '' ) . ' class="prdctfltr_sc_filter woocommerce ' . ( $ajax == 'yes' ? ' prdctfltr_ajax' : '' ) . ( $class !== '' ? ' ' . $class : '' ) . '"' . $margin . ( $ajax == 'yes' ? $add_ajax : '' ) . '>' . do_shortcode( $cached ) . '</div>';

		}

/*
		public static function prdctfltr_pagination_filter( $args ) {

			if ( strpos( $args['base'], 'paged=' ) < 1 ) {
				$args['base'] = esc_url( untrailingslashit( $args['base'] ) . '/?paged=%#%' );
			}

			if ( isset( self::$settings['instance'] ) ) {
				$args['total'] = self::$settings['instance']->max_num_pages;
			}

			return $args;

		}
*/

	}

	add_action( 'init', array( 'WC_Prdctfltr_Shortcodes', 'init' ), 999 );

?>