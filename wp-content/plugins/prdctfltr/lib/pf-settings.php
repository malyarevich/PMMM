<?php


	if ( ! defined( 'ABSPATH' ) ) exit;

	class WC_Settings_Prdctfltr {

		public static function init() {
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::prdctfltr_admin_scripts' );
			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::prdctfltr_add_settings_tab', 49 );
			add_action( 'woocommerce_settings_tabs_settings_products_filter', __CLASS__ . '::prdctfltr_settings_tab' );
			add_action( 'woocommerce_update_options_settings_products_filter', __CLASS__ . '::prdctfltr_update_settings' );
			add_action( 'woocommerce_admin_field_pf_taxonomy', __CLASS__ . '::prdctfltr_pf_taxonomy', 10 );
			add_action( 'woocommerce_admin_field_pf_filter', __CLASS__ . '::prdctfltr_pf_filter', 10 );
			add_action( 'woocommerce_admin_field_pf_filter_analytics', __CLASS__ . '::prdctfltr_pf_filter_analytics', 10 );

			add_action( 'woocommerce_admin_settings_sanitize_option', __CLASS__ . '::prdctfltr_pf_taxonomy_sanitize', 10, 3 );

			add_action( 'wp_ajax_prdctfltr_admin_save', __CLASS__ . '::prdctfltr_admin_save' );
			add_action( 'wp_ajax_prdctfltr_admin_load', __CLASS__ . '::prdctfltr_admin_load' );
			add_action( 'wp_ajax_prdctfltr_admin_delete', __CLASS__ . '::prdctfltr_admin_delete' );
			add_action( 'wp_ajax_prdctfltr_or_add', __CLASS__ . '::prdctfltr_or_add' );
			add_action( 'wp_ajax_prdctfltr_or_remove', __CLASS__ . '::prdctfltr_or_remove' );
			add_action( 'wp_ajax_prdctfltr_m_fields', __CLASS__ . '::prdctfltr_m_fields' );
			add_action( 'wp_ajax_prdctfltr_c_fields', __CLASS__ . '::prdctfltr_c_fields' );
			add_action( 'wp_ajax_prdctfltr_c_terms', __CLASS__ . '::prdctfltr_c_terms' );
			add_action( 'wp_ajax_prdctfltr_r_fields', __CLASS__ . '::prdctfltr_r_fields' );
			add_action( 'wp_ajax_prdctfltr_r_terms', __CLASS__ . '::prdctfltr_r_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms', __CLASS__ . '::set_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms_new_style', __CLASS__ . '::set_terms_new' );
			add_action( 'wp_ajax_prdctfltr_set_terms_save_style', __CLASS__ . '::save_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms_remove_style', __CLASS__ . '::remove_terms' );
			add_action( 'wp_ajax_prdctfltr_set_filters', __CLASS__ . '::set_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_add', __CLASS__ . '::add_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_new_style', __CLASS__ . '::set_filters_new' );
			add_action( 'wp_ajax_prdctfltr_set_filters_save_style', __CLASS__ . '::save_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_remove_style', __CLASS__ . '::remove_filters' );
			add_action( 'wp_ajax_prdctfltr_reset', __CLASS__ . '::reset_options' );
			add_action( 'wp_ajax_prdctfltr_analytics_reset', __CLASS__ . '::analytics_reset' );

		}

		public static function prdctfltr_admin_scripts( $hook) {

			if ( isset( $_GET['page'], $_GET['tab'] ) && ( $_GET['page'] == 'wc-settings' || $_GET['page'] == 'woocommerce_settings' ) && $_GET['tab'] == 'settings_products_filter' ) {

				wp_register_style( 'prdctfltr-font', Prdctfltr()->plugin_url() . '/lib/font/styles.css', false, PrdctfltrInit::$version );
				wp_enqueue_style( 'prdctfltr-font' );

				wp_register_style( 'prdctfltr-admin', Prdctfltr()->plugin_url() . '/lib/css/admin.css', false, PrdctfltrInit::$version );
				wp_enqueue_style( 'prdctfltr-admin' );

				wp_register_script( 'prdctfltr-settings', Prdctfltr()->plugin_url() . '/lib/js/admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), PrdctfltrInit::$version, true );
				wp_enqueue_script( 'prdctfltr-settings' );

				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );

				if ( function_exists( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}

				$dec_separator = get_option( 'woocommerce_price_decimal_sep' );

				$curr_args = array(
					'ajax' => admin_url( 'admin-ajax.php' ),
					'url' => Prdctfltr()->plugin_url(),
					'decimal_separator' => $dec_separator,
					'characteristics' => get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ),
					'localization' => array(
						'activate' => __( 'Activate?', 'prdctfltr' ),
						'deactivate' => __( 'Deactivate?', 'prdctfltr' ),
						'delete' => __( 'Delete?', 'prdctfltr' ),
						'remove' => __( 'Remove?', 'prdctfltr' ),
						'remove_key' => __( 'Remove key from database?', 'prdctfltr' ),
						'add_override' => __( 'Add override?', 'prdctfltr' ),
						'remove_override' => __( 'Remove override?', 'prdctfltr' ),
						'override_notice' => __( 'Please select both term and the filter preset.', 'prdctfltr' ),
						'added' => __( 'Added!', 'prdctfltr' ),
						'load' => __( 'Load?', 'prdctfltr' ),
						'saved' => __( 'Saved!', 'prdctfltr' ),
						'ajax_error' => __( 'AJAX Error!', 'prdctfltr' ),
						'missing_settings' => __( 'Missing name or settings.', 'prdctfltr' ),
						'not_selected' => __( 'Not selected!', 'prdctfltr' ),
						'deleted' => __( 'Deleted!', 'prdctfltr' ),
						'customization_save' => __( 'Customization created!', 'prdctfltr' ) . ' ' . __( 'Please save your default or your current filter preset to make customization changes!', 'prdctfltr' ),
						'customization_removed' => __( 'Removed!', 'prdctfltr' ) . ' ' . __( 'Please save your default or your current filter preset to make customization changes!', 'prdctfltr' ),
						'delete_analytics' => __( 'Analytics data deleted!', 'prdctfltr' ),
						'adv_filter' => __( 'Advanced Filter', 'prdctfltr' ),
						'rng_filter' => __( 'Range Filter', 'prdctfltr' ),
						'mta_filter' => __( 'Meta Filter', 'prdctfltr' ),
						'decimal_error' =>  __( 'Use only numbers and the decimal separator!', 'prdctfltr' ) . ' ( ' . $dec_separator . ' )',
						'remove_override_single' =>  __( 'Remove Override', 'prdctfltr' ),
						'term_slug' => __( 'Term slug', 'prdctfltr' ),
						'filter_preset' => __( 'Filter Preset', 'prdctfltr' ),
						'loaded' => __( 'Loaded!', 'prdctfltr' ),
						'removed' => __( 'Removed!', 'prdctfltr' ),
						'invalid_key' => __( 'Invalid key! Cannot be removed from database! Please save your settings.', 'prdctfltr' ),
						'reset_options' => __( 'This action will reset ALL Product Filter options, presets and overrides! Are you sure?', 'prdctfltr' )
					)
				);
				wp_localize_script( 'prdctfltr-settings', 'prdctfltr', $curr_args );
			}

		}

		public static function prdctfltr_pf_filter_analytics( $field ) {

		if ( get_option( 'wc_settings_prdctfltr_use_analytics', 'no' ) == 'no' ) {
			return '';
		}

		global $woocommerce;
?>
		<tr valign="top" class="">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<?php echo '<img class="help_tip" data-tip="' . esc_attr( $field['desc'] ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />'; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
				<div class="prdctfltr_filtering_analytics_wrapper">
			<?php
				$stats = get_option( 'wc_settings_prdctfltr_filtering_analytics_stats', array() );

				if ( empty( $stats ) ) {
					_e( 'Filtering Analytics are empty! Please enable the filtering analytics and wait for the results! Thank you!', 'prdctfltr' );
				}
				else {
					?>
					<div class="prdctfltr_filtering_analytics_settings">
						<a href="#" class="button-primary prdctfltr_filtering_analytics_reset"><?php _e( 'Reset Analytics', 'prdctfltr' ); ?></a>
					</div>
					<?php

					foreach( $stats as $k => $v ) {
						$total_count = 0
					?>
						<div class="prdctfltr_filtering_analytics">
							<h3 class="prdctfltr_filtering_analytics_title">
							<?php
								$mode = 'default';
								if ( taxonomy_exists( $k ) ) {
									$mode = 'taxonomy';
									if ( substr( $k, 0, 3 ) == 'pa_' ) {
										$label = wc_attribute_label( $k );
									}
									else {
										if ( $k == 'product_cat' ) {
											$label = __( 'Categories', 'prdctfltr' );
										}
										else if ( $k == 'product_tag' ) {
											$label = __( 'Tags', 'prdctfltr' );
										}
										else if ( $k == 'characteristics' ) {
											$label = __( 'Characteristics', 'prdctfltr' );
										}
										else {
											$curr_term = get_taxonomy( $k );
											$label = $curr_term->name;
										}
									}
								}


								if ( $mode == 'taxonomy' ) {
									if ( !empty( $v ) && is_array( $v ) ) {
										foreach( $v as $vk => $vv ) {
											$term = get_term_by( 'slug', $vk, $k);
											$term_name = ucfirst( $term->name ) . ' ( ' . $v[$vk] .' )';

											$v[$term_name] = $v[$vk];
											$total_count = $total_count + $v[$vk];
											unset( $v[$vk] );
										}
										echo __( 'Filter', 'prdctfltr' ) . ' <em>' . ucfirst( $label ) . '</em> - ' . __( 'Total hits count:' ) . ' ' . $total_count;
									}
								}
								else {
									//$v = array_map( 'ucfirst', $v );
									echo __( 'Filter', 'prdctfltr' ) . ' <em>' . ucfirst( $k ) . '</em>';
								}
		
							?>
							</h3>
							<div id="prdctfltr_filtering_analytics_<?php echo sanitize_title( $k ); ?>" class="prdctfltr_filtering_analytics_chart" data-chart-title="<?php echo esc_attr( __( 'Filtering data for taxonomy', 'prdctfltr' ) . ': ' . $k ); ?>" data-chart="<?php echo esc_attr( json_encode( $v ) ); ?>"></div>
						</div>
					<?php
					}
			?>
					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
						(function( $){
						"use strict";

							google.load( 'visualization', '1.0', {'packages':['corechart']});

							google.setOnLoadCallback(drawCharts);

							function drawCharts() {

								$( '.prdctfltr_filtering_analytics_chart' ).each( function() {

									var el = $(this).attr( 'id' );
									var chartData = $.parseJSON( $(this).attr( 'data-chart' ));
									var chartDataTitle = $(this).attr( 'data-chart-title' );

									var chartArray = [];
									for (var key in chartData) {
										if (chartData.hasOwnProperty(key)) {
											chartArray.push([key, chartData[key]] );
										}
									};

									var data = new google.visualization.DataTable();
									data.addColumn( 'string', 'Term' );
									data.addColumn( 'number', 'Count' );
									data.addRows(chartArray);

									var options = {'title':chartDataTitle,'is3D':true,'chartArea':{'width':'100%','height':'80%'},'legend':{'position':'bottom'}};

									var chart = new google.visualization.PieChart(document.getElementById(el));
									chart.draw(data, options);

								});

							}
						})(jQuery);
					</script>
			<?php
				}
			?>
				</div>
			</td>
		</tr>
<?php
		}

		public static function prdctfltr_pf_filter( $field ) {

		global $woocommerce;
	?>
		<tr valign="top">
			<th scope="row" class="titledesc" style="display:none;">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<?php echo '<img class="help_tip" data-tip="' . esc_attr( $field['desc'] ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />'; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
				<?php

					$pf_filters_selected = get_option( 'wc_settings_prdctfltr_active_filters', array( 'sort','price','cat' ) );

					$curr_filters = array(
						'sort' => __( 'Sort By', 'prdctfltr' ),
						'price' => __( 'By Price', 'prdctfltr' ),
						'cat' => __( 'By Categories', 'prdctfltr' ),
						'tag' => __( 'By Tags', 'prdctfltr' ),
						'char' => __( 'By Characteristics', 'prdctfltr' ),
						'vendor' => __( 'Vendor', 'prdctfltr' ),
						'instock' => __( 'In Stock Filter', 'prdctfltr' ),
						'per_page' => __( 'Products Per Page', 'prdctfltr' ),
						'search' => __( 'Search Fitler', 'prdctfltr' )
					);

					if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'no' ) {
						unset( $curr_filters['char'] );
					}

					$curr_attr = array();
					if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
						foreach ( $attribute_taxonomies as $tax ) {
							$curr_label = !empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
							$curr_attr['pa_' . $tax->attribute_name] = ucfirst( $curr_label );
						}
					}

					$pf_filters = $curr_filters + $curr_attr;

				?>
				<div class="form-field prdctfltr_customizer_static">
					<div class="pf_element" data-filter="basic">
						<span><?php _e( 'General Settings', 'prdctfltr' ); ?></span>
						<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
						<div class="pf_options_holder"></div>
					</div>
					<div class="pf_element" data-filter="style">
						<span><?php _e( 'Filter Style', 'prdctfltr' ); ?></span>
						<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
						<div class="pf_options_holder"></div>
					</div>
				</div>
				<h3><?php _e( 'Available Filters', 'prdctfltr' ); ?></h3>
				<p class="form-field prdctfltr_customizer_fields">
				<?php
					foreach ( $pf_filters as $k => $v ) {
						if ( in_array( $k, $pf_filters_selected ) ) {
							$add['class'] = ' pf_active';
							$add['icon'] = '<i class="prdctfltr-eye"></i>';
						}
						else {
							$add['class'] = '';
							$add['icon'] = '<i class="prdctfltr-eye-disabled"></i>';
						}
				?>
					<a href="#" class="prdctfltr_c_add_filter<?php echo $add['class']; ?>" data-filter="<?php echo $k; ?>">
						<?php echo $add['icon']; ?> 
						<span><?php echo $v; ?></span>
					</a>
				<?php
					}
				?>
					<a href="#" class="prdctfltr_c_add pf_advanced"><i class="prdctfltr-plus"></i> <span><?php _e( 'Add advanced filter', 'prdctfltr' ); ?></span></a>
					<a href="#" class="prdctfltr_c_add pf_range"><i class="prdctfltr-plus"></i> <span><?php _e( 'Add range filter', 'prdctfltr' ); ?></span></a>
					<a href="#" class="prdctfltr_c_add pf_meta"><i class="prdctfltr-plus"></i> <span><?php _e( 'Add meta filter', 'prdctfltr' ); ?></span></a>
				</p>
				<div class="form-field prdctfltr_customizer">
				<?php

					if ( isset( $_POST['pfa_taxonomy'] ) ) {

						$pf_filters_advanced = array();

						for( $i = 0; $i < count( $_POST['pfa_taxonomy'] ); $i++ ) {
							$pf_filters_advanced['pfa_title'][$i] = $_POST['pfa_title'][$i];
							$pf_filters_advanced['pfa_description'][$i] = $_POST['pfa_description'][$i];
							$pf_filters_advanced['pfa_taxonomy'][$i] = $_POST['pfa_taxonomy'][$i];
							$pf_filters_advanced['pfa_include'][$i] = ( isset( $_POST['pfa_include'][$i] ) ? $_POST['pfa_include'][$i] : array() );
							$pf_filters_advanced['pfa_orderby'][$i] = ( isset( $_POST['pfa_orderby'][$i] ) ? $_POST['pfa_orderby'][$i] : '' );
							$pf_filters_advanced['pfa_order'][$i] = ( isset( $_POST['pfa_order'][$i] ) ? $_POST['pfa_order'][$i] : '' );
							$pf_filters_advanced['pfa_multiselect'][$i] = ( isset( $_POST['pfa_multiselect'][$i] ) ? $_POST['pfa_multiselect'][$i] : 'no' );
							$pf_filters_advanced['pfa_relation'][$i] = ( isset( $_POST['pfa_relation'][$i] ) ? $_POST['pfa_relation'][$i] : 'OR' );
							$pf_filters_advanced['pfa_adoptive'][$i] = ( isset( $_POST['pfa_adoptive'][$i] ) ? $_POST['pfa_adoptive'][$i] : 'no' );
							$pf_filters_advanced['pfa_none'][$i] = ( isset( $_POST['pfa_none'][$i] ) ? $_POST['pfa_none'][$i] : 'no' );
							$pf_filters_advanced['pfa_limit'][$i] = ( isset( $_POST['pfa_limit'][$i] ) ? $_POST['pfa_limit'][$i] : '' );
							$pf_filters_advanced['pfa_hierarchy'][$i] = ( isset( $_POST['pfa_hierarchy'][$i] ) ? $_POST['pfa_hierarchy'][$i] : 'no' );
							$pf_filters_advanced['pfa_hierarchy_mode'][$i] = ( isset( $_POST['pfa_hierarchy_mode'][$i] ) ? $_POST['pfa_hierarchy_mode'][$i] : 'no' );
							$pf_filters_advanced['pfa_mode'][$i] = ( isset( $_POST['pfa_mode'][$i] ) ? $_POST['pfa_mode'][$i] : 'showall' );
							$pf_filters_advanced['pfa_style'][$i] = ( isset( $_POST['pfa_style'][$i] ) ? $_POST['pfa_style'][$i] : 'pf_attr_text' );
							$pf_filters_advanced['pfa_term_customization'][$i] = ( isset( $_POST['pfa_term_customization'][$i] ) ? $_POST['pfa_term_customization'][$i] : '' );
						}

					}
					else {
						$pf_filters_advanced = get_option( 'wc_settings_prdctfltr_advanced_filters' );
					}

					if ( isset( $_POST['pfr_taxonomy'] ) ) {

						$pf_filters_range = array();

						for( $i = 0; $i < count( $_POST['pfr_taxonomy'] ); $i++ ) {
							$pf_filters_range['pfr_title'][$i] = $_POST['pfr_title'][$i];
							$pf_filters_range['pfr_description'][$i] = $_POST['pfr_description'][$i];
							$pf_filters_range['pfr_taxonomy'][$i] = $_POST['pfr_taxonomy'][$i];
							$pf_filters_range['pfr_include'][$i] = ( isset( $_POST['pfr_include'][$i] ) ? $_POST['pfr_include'][$i] : array() );
							$pf_filters_range['pfr_orderby'][$i] = ( isset( $_POST['pfr_orderby'][$i] ) ? $_POST['pfr_orderby'][$i] : '' );
							$pf_filters_range['pfr_order'][$i] = ( isset( $_POST['pfr_order'][$i] ) ? $_POST['pfr_order'][$i] : '' );
							$pf_filters_range['pfr_style'][$i] = ( isset( $_POST['pfr_style'][$i] ) ? $_POST['pfr_style'][$i] : 'flat' );
							$pf_filters_range['pfr_grid'][$i] = ( isset( $_POST['pfr_grid'][$i] ) ? $_POST['pfr_grid'][$i] : 'no' );
							$pf_filters_range['pfr_adoptive'][$i] = ( isset( $_POST['pfr_adoptive'][$i] ) ? $_POST['pfr_adoptive'][$i] : 'no' );
							$pf_filters_range['pfr_custom'][$i] = ( isset( $_POST['pfr_custom'][$i] ) ? stripslashes( $_POST['pfr_custom'][$i] ) : '' );
						}

					}
					else {
						$pf_filters_range = get_option( 'wc_settings_prdctfltr_range_filters' );
					}

					if ( isset( $_POST['pfm_key'] ) ) {

						$pf_filters_meta = array();

						for( $i = 0; $i < count( $_POST['pfm_key'] ); $i++ ) {
							$pf_filters_meta['pfm_title'][$i] = $_POST['pfm_title'][$i];
							$pf_filters_meta['pfm_description'][$i] = $_POST['pfm_description'][$i];
							$pf_filters_meta['pfm_key'][$i] = $_POST['pfm_key'][$i];
							$pf_filters_meta['pfm_compare'][$i] = ( isset( $_POST['pfm_compare'][$i] ) ? $_POST['pfm_compare'][$i] : '=' );
							$pf_filters_meta['pfm_type'][$i] = ( isset( $_POST['pfm_type'][$i] ) ? $_POST['pfm_type'][$i] : 'NUMERIC' );
							$pf_filters_meta['pfm_limit'][$i] = ( isset( $_POST['pfm_limit'][$i] ) ? $_POST['pfm_limit'][$i] : '' );
							$pf_filters_meta['pfm_multiselect'][$i] = ( isset( $_POST['pfm_multiselect'][$i] ) ? $_POST['pfm_multiselect'][$i] : 'no' );
							$pf_filters_meta['pfm_relation'][$i] = ( isset( $_POST['pfm_relation'][$i] ) ? $_POST['pfm_relation'][$i] : 'OR' );
							$pf_filters_meta['pfm_none'][$i] = ( isset( $_POST['pfm_none'][$i] ) ? $_POST['pfm_none'][$i] : 'no' );
							$pf_filters_meta['pfm_term_customization'][$i] = ( isset( $_POST['pfm_term_customization'][$i] ) ? $_POST['pfm_term_customization'][$i] : '' );
							$pf_filters_meta['pfm_filter_customization'][$i] = ( isset( $_POST['pfm_filter_customization'][$i] ) ? $_POST['pfm_filter_customization'][$i] : '' );
						}

					}
					else {
						$pf_filters_meta = get_option( 'wc_settings_prdctfltr_meta_filters' );
					}

					if ( $pf_filters_advanced === false ) {
						$pf_filters_advanced = array();
					}

					if ( $pf_filters_range === false ) {
						$pf_filters_range = array();
					}
					if ( $pf_filters_meta === false ) {
						$pf_filters_meta = array();
					}

					$i=0;$q=0;$y=0;

					foreach ( $pf_filters_selected as $v ) {
						if ( $v == 'advanced' && !empty( $pf_filters_advanced ) && isset( $pf_filters_advanced['pfa_taxonomy'][$i] ) ) {
					?>
							<div class="pf_element adv" data-filter="advanced" data-id="<?php echo $i; ?>">
								<span><?php _e( 'Advanced Filter', 'prdctfltr' ); ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder">
									<h3><?php _e( 'Advanced Fitler', 'prdctfltr' ); ?></h3>
									<p><?php echo __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_title_%1$s">%2$s</label>', $i, __( 'Filter Title', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfa_title[%1$s]" id="pfa_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $i, isset( $pf_filters_advanced['pfa_title'][$i] ) ? $pf_filters_advanced['pfa_title'][$i] : '' );
													?>
													<span class="description"><?php echo __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_description_%1$s">%2$s</label>', $i, __( 'Filter Description', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php _e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
													<?php
														printf( '<textarea name="pfa_description[%1$s]" id="pfa_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $i, ( isset( $pf_filters_advanced['pfa_description'][$i] ) ? stripslashes( $pf_filters_advanced['pfa_description'][$i] ) : '' ) );
													?>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													$taxonomies = get_object_taxonomies( 'product', 'object' );
													printf( '<label for="pfa_taxonomy_%1$s">%2$s</label>', $i, __( 'Select Taxonomy', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														printf( '<select id="pfa_taxonomy_%1$s" name="pfa_taxonomy[%1$s]" class="prdctfltr_adv_select" style="width:300px;margin-right:12px;">', $i) ;
														foreach ( $taxonomies as $k => $v ) {
															if ( in_array( $k, array( 'product_type' ) ) ) {
																continue;
															}
															echo '<option value="' . $k . '"' . ( $pf_filters_advanced['pfa_taxonomy'][$i] == $k ? ' selected="selected"' : '' ) .'>' . ( substr( $v->name, 0, 3 ) == 'pa_' ? wc_attribute_label( $v->name ) : $v->label ) . '</option>';
														}
														echo '</select>';
													?>
													<span class="description"><?php _e( 'Select filter product taxonomy.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_include_%1$s">%2$s</label>', $i, __( 'Select Terms', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-multiselect">
												<?php
													$tax = isset( $pf_filters_advanced['pfa_taxonomy'][$i] ) && taxonomy_exists( $pf_filters_advanced['pfa_taxonomy'][$i] ) ? $pf_filters_advanced['pfa_taxonomy'][$i] : $first_tax;
													if ( !empty( $tax ) ) {
														$args = array(
															'hide_empty' => 0,
															'echo' => 0,
															'hierarchical' => ( is_taxonomy_hierarchical( $pf_filters_advanced['pfa_taxonomy'][$i] ) ? 1 : 0 ),
															'name' => 'pfa_include[' . $i . '][]',
															'id' => 'pfa_include_' . $i,
															'class' => '',
															'depth' => 0,
															'taxonomy' => $pf_filters_advanced['pfa_taxonomy'][$i],
															'hide_if_empty' => true,
															'value_field' => 'slug'
														);

														$option_value = $pf_filters_advanced['pfa_include'][$i];
														$dropdown = wp_dropdown_categories( $args );
														$dropdown = str_replace( 'id=', ' style="width:300px;margin-right:12px;" multiple="multiple" id=', $dropdown );
														if ( is_array( $option_value ) ) {
															foreach ( $option_value as $key => $post_term ) { 
																$dropdown = str_replace(' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown); 
															}
														}
														else {
															$dropdown = str_replace( ' value="' . $option_value . '"', ' value="' . $option_value . '" selected="selected"', $dropdown );
														}
														echo $dropdown;
													}
													else {
														printf( '<select name="pfa_include[%1$s][]" id="pfa_include_%1$s" multiple="multiple" style="width:300px;margin-right:12px;"></select>', $i );
													}
												?>
													<span class="description"><?php echo __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_style_%1$s">%2$s</label>', $i, __( 'Appearance', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'pf_attr_text' => __( 'Text', 'prdctfltr' ),
															'pf_attr_imgtext' => __( 'Thumbnails with text', 'prdctfltr' ),
															'pf_attr_img' => __( 'Thumbnails only', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_style'][$i] ) && $pf_filters_advanced['pfa_style'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_style[%2$s]" id="pfa_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select style preset to use with the current taxonomy (works only with product attributes).', 'prdctfltr' ); ?> DEPRECATED DO NOT USE! USE PAINT BUCKET TOOL AND SET APPEARANCE TO TEXT!<em class="pf_deprecated"></em></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_orderby_%1$s">%2$s</label>', $i, __( 'Terms Order By', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$orderby_params = array(
															'' => __( 'None', 'prdctfltr' ),
															'id' => __( 'ID', 'prdctfltr' ),
															'name' => __( 'Name', 'prdctfltr' ),
															'number' => __( 'Number', 'prdctfltr' ),
															'slug' => __( 'Slug', 'prdctfltr' ),
															'count' => __( 'Count', 'prdctfltr' )
														);

														foreach ( $orderby_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_orderby'][$i] ) && $pf_filters_advanced['pfa_orderby'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_orderby[%2$s]" id="pfa_orderby_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select term ordering.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_order_%1$s">%2$s</label>', $i, __( 'Terms Order', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$order_params = array(
															'ASC' => __( 'ASC', 'prdctfltr' ),
															'DESC' => __( 'DESC', 'prdctfltr' )
														);

														foreach ( $order_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_order'][$i] ) && $pf_filters_advanced['pfa_order'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_order[%2$s]" id="pfa_order_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_limit_%1$s">%2$s</label>', $i, __( 'Limit Terms', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-number">
													<?php
														printf( '<input name="pfa_limit[%1$s]" id="pfa_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $i, isset( $pf_filters_advanced['pfa_limit'][$i] ) ? $pf_filters_advanced['pfa_limit'][$i] : '' ); ?>
													<span class="description"><?php _e( 'Limit number of terms to display in filter.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_hierarchy_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_hierarchy[%1$s]" id="pfa_hierarchy_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_hierarchy'][$i] ) && $pf_filters_advanced['pfa_hierarchy'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to enable hierarchy on current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Taxonomy Hierarchy Mode', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Taxonomy Hierarchy Mode', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_hierarchy_mode_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_hierarchy_mode[%1$s]" id="pfa_hierarchy_mode_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_hierarchy_mode'][$i] ) && $pf_filters_advanced['pfa_hierarchy_mode'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( ' Check this option to expand parent terms on load.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_mode_%1$s">%2$s</label>', $i, __( 'Taxonomy Hierarchy Filtering Mode', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'showall' => __( 'Show all', 'prdctfltr' ),
															'subonly' => __( 'Keep only child terms', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_mode'][$i] ) && $pf_filters_advanced['pfa_mode'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_mode[%2$s]" id="pfa_mode_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select terms relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Multi Select', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Multi Select', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_multiselect_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_multiselect[%1$s]" id="pfa_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_multiselect'][$i] ) && $pf_filters_advanced['pfa_multiselect'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to enable multi term selection.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_relation_%1$s">%2$s</label>', $i, __( 'Multi Select Terms Relation', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
															'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_relation'][$i] ) && $pf_filters_advanced['pfa_relation'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_relation[%2$s]" id="pfa_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select term relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Adoptive Filtering', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Adoptive Filtering', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_adoptive_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_adoptive[%1$s]" id="pfa_adoptive_" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_adoptive'][$i] ) && $pf_filters_advanced['pfa_adoptive'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Hide None', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Hide None', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_none_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_none[%1$s]" id="pfa_none_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_none'][$i] ) && $pf_filters_advanced['pfa_none'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to hide none in the current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_term_customization_%1$s">%2$s</label>', $i, __( 'Style Customization Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfa_term_customization[%1$s]" id="pfa_term_customization_%1$s" class="pf_term_customization" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $i, ( isset( $pf_filters_advanced['pfa_term_customization'][$i] ) ? $pf_filters_advanced['pfa_term_customization'][$i] : '' ) );
													?>
													<span class="description"><?php _e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?php
							$i++;
						}
						else if ( $v == 'range' && !empty( $pf_filters_range ) && isset( $pf_filters_range['pfr_taxonomy'][$q] ) ) {
					?>
							<div class="pf_element rng" data-filter="range" data-id="<?php echo $q; ?>">
								<span><?php _e( 'Range Filter', 'prdctfltr' ); ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder">
									<h3><?php _e( 'Range Fitler', 'prdctfltr' ); ?></h3>
									<p><?php echo __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
									<table cass="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_title_%1$s">%2$s</label>', $q, __( 'Filter Title', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfr_title[%1$s]" id="pfr_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $q, $pf_filters_range['pfr_title'][$q] );
													?>
													<span class="description"><?php echo __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_description_%1$s">%2$s</label>', $q, __( 'Filter Description', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php _e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
													<?php
														printf( '<textarea name="pfr_description[%1$s]" id="pfr_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $q, ( isset( $pf_filters_range['pfr_description'][$q] ) ? stripslashes( $pf_filters_range['pfr_description'][$q] ) : '' ) );
													?>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_taxonomy_%1$s">%2$s</label>', $q, __( 'Select Range', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$taxonomies = get_object_taxonomies( 'product', 'object' );
														printf( '<select name="pfr_taxonomy[%1$s]" id="pfr_taxonomy_%1$s" class="prdctfltr_rng_select"  style="width:300px;margin-right:12px;">', $q );
														echo '<option value="price"' . ( $pf_filters_range['pfr_taxonomy'][$q] == 'price' ? ' selected="selected"' : '' ) . '>' . __( 'Price range', 'prdctfltr' ) . '</option>';
														foreach ( $taxonomies as $k => $v ) {
															if ( substr( $k, 0, 3 ) == 'pa_' && $k !== 'product_type' ) {
																$curr_label = wc_attribute_label( $v->name );
																$curr_value = $v->name;
															}
															else {
																$curr_label = $v->label;
																$curr_value = $k;
															}
															echo '<option value="' . $curr_value . '"' . ( $pf_filters_range['pfr_taxonomy'][$q] == '' . $curr_value ? ' selected="selected"' : '' ) .'>' . $curr_label . '</option>';
														}
														echo '</select>';
													?>
													<span class="description"><?php _e( 'Enter title for the current range filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_include_%1$s">%2$s</label>', $q, __( 'Select Terms', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-multiselect">
													<?php
														if ( $pf_filters_range['pfr_taxonomy'][$q] !== 'price' ) {

															$args = array(
																'hide_empty' => 0,
																'echo' => 0,
																'hierarchical' => ( is_taxonomy_hierarchical( $pf_filters_range['pfr_taxonomy'][$q] ) ? 1 : 0 ),
																'name' => 'pfr_include_' . $q . '[]',
																'id' => 'pfr_include_' . $q,
																'class' => '',
																'depth' => 0,
																'taxonomy' => $pf_filters_range['pfr_taxonomy'][$q],
																'hide_if_empty' => true,
																'value_field' => 'slug'
															);

															$option_value = $pf_filters_range['pfr_include'][$q];
															$dropdown = wp_dropdown_categories( $args );
															$dropdown = str_replace( 'id=', ' style="width:300px;margin-right:12px;" multiple="multiple" id=', $dropdown );
															if ( is_array( $option_value ) ) {
																foreach ( $option_value as $key => $post_term ) { 
																	$dropdown = str_replace(' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown); 
																}
															}
															else {
																$dropdown = str_replace( ' value="' . $option_value . '"', ' value="' . $option_value . '" selected="selected"', $dropdown );
															}
															echo $dropdown;

															$add_disabled = '';

														}
														else {
															printf( '<select name="pfr_include[%1$s][]" id="pfr_include_%1$s" multiple="multiple" disabled style="width:300px;margin-right:12px;"></select></label>', $q );
															$add_disabled = ' disabled';
														}
													?>
													<span class="description"><?php echo __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_orderby_%1$s">%2$s</label>', $q, __( 'Terms Order By', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$orderby_params = array(
														'' => __( 'None', 'prdctfltr' ),
														'id' => __( 'ID', 'prdctfltr' ),
														'name' => __( 'Name', 'prdctfltr' ),
														'number' => __( 'Number', 'prdctfltr' ),
														'slug' => __( 'Slug', 'prdctfltr' ),
														'count' => __( 'Count', 'prdctfltr' )
													);
													foreach ( $orderby_params as $k => $v ) {
														$selected = ( isset( $pf_filters_range['pfr_orderby'][$q] ) && $pf_filters_range['pfr_orderby'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}
													printf( '<select name="pfr_orderby[%2$s]" id="pfr_orderby_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select></label>', $curr_options, $q, $add_disabled );
												?>
													<span class="description"><?php _e( 'Select term ordering.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_order_%1$s">%2$s</label>', $q, __( 'Terms Order', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$order_params = array(
														'ASC' => __( 'ASC', 'prdctfltr' ),
														'DESC' => __( 'DESC', 'prdctfltr' )
													);
													foreach ( $order_params as $k => $v ) {
														$selected = ( isset( $pf_filters_range['pfr_order'][$q] ) && $pf_filters_range['pfr_order'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}

													printf( '<select name="pfr_order[%2$s]" id="pfr_order_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $q, $add_disabled );
												?>
													<span class="description"><?php _e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_style_%1$s">%2$s</label>', $q, __( 'Select Style', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$catalog_style = array(
														'flat' => __( 'Flat', 'prdctfltr' ),
														'modern' => __( 'Modern', 'prdctfltr' ),
														'html5' => __( 'HTML5', 'prdctfltr' ),
														'white' => __( 'White', 'prdctfltr' )
													);
													foreach ( $catalog_style as $k => $v ) {
														$selected = ( $pf_filters_range['pfr_style'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}

													printf( '<select name="pfr_style[%2$s]" id="pfr_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $q );
												?>
													<span class="description"><?php _e( 'Select current range style.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Grid', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Grid', 'prdctfltr' );
														?>
														</legend>
														<label for="pfr_grid_<?php echo $q; ?>">
														<?php
															printf( '<input name="pfr_grid[%2$s]" id="pfr_grid_%2$s" type="checkbox" value="yes"%1$s />', ( $pf_filters_range['pfr_grid'][$q] == 'yes' ? ' checked="checked"' : '' ), $q );
															_e( 'Check this option to use grid in current range.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Adoptive Filtering', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Adoptive Filtering', 'prdctfltr' );
														?>
														</legend>
														<label for="pfr_adoptive_<?php echo $q; ?>">
														<?php
															printf( '<input name="pfr_adoptive[%2$s]" id="pfr_adoptive_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $pf_filters_range['pfr_adoptive'][$q] ) && $pf_filters_range['pfr_adoptive'][$q] == 'yes' ? ' checked="checked"' : '' ), $q );
															_e( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_custom_%1$s">%2$s</label>', $q, __( 'Custom Settings', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php _e( 'Enter custom settings for the range filter. Visit this page for more information ', 'prdctfltr' ); ?> <a href="http://ionden.com/a/plugins/ion.rangeSlider/demo.html" target="_blank">http://ionden.com/a/plugins/ion.rangeSlider/demo.html</a></p>
													<?php
														printf( '<textarea name="pfr_custom[%1$s]" id="pfr_custom_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $q, ( isset( $pf_filters_range['pfr_custom'][$q] ) ? stripslashes( $pf_filters_range['pfr_custom'][$q] ) : '' ) );
													?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?php
							$q++;
						}
						else if ( $v == 'meta' && !empty( $pf_filters_meta ) && isset( $pf_filters_meta['pfm_key'][$y] ) ) {
					?>
							<div class="pf_element mta" data-filter="meta" data-id="<?php echo $y; ?>">
								<span><?php _e( 'Meta Filter', 'prdctfltr' ); ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder">
									<h3><?php _e( 'Meta Fitler', 'prdctfltr' ); ?></h3>
									<p><?php echo __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_title_%1$s">%2$s</label>', $y, __( 'Filter Title', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfm_title[%1$s]" id="pfm_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $y, isset( $pf_filters_meta['pfm_title'][$y] ) ? $pf_filters_meta['pfm_title'][$y] : '' );
													?>
													<span class="description"><?php echo __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_description_%1$s">%2$s</label>', $y, __( 'Filter Description', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php _e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
													<?php
														printf( '<textarea name="pfm_description[%1$s]" id="pfm_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $y, ( isset( $pf_filters_meta['pfm_description'][$y] ) ? stripslashes( $pf_filters_meta['pfm_description'][$y] ) : '' ) );
													?>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_key_%1$s">%2$s</label>', $y, __( 'Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfm_key[%1$s]" id="pfm_key_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $y, isset( $pf_filters_meta['pfm_key'][$y] ) ? $pf_filters_meta['pfm_key'][$y] : '' );
													?>
													<span class="description"><?php echo __( 'Meta key.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_compare_%1$s">%2$s</label>', $y, __( 'Compare', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
							
													$meta_compares = array(
														array(
															'value' => '=',
															'label' => '='
														),
														array(
															'value' => '!=',
															'label' => '!='
														),
														array(
															'value' => '>',
															'label' => '>'
														),
														array(
															'value' => '<',
															'label' => '<'
														),
														array(
															'value' => '>=',
															'label' => '>='
														),
														array(
															'value' => '<=',
															'label' => '<='
														),
														array(
															'value' => 'LIKE',
															'label' => 'LIKE'
														),
														array(
															'value' => 'NOT LIKE',
															'label' => 'NOT LIKE'
														),
														array(
															'value' => 'IN',
															'label' => 'IN'
														),
														array(
															'value' => 'NOT IN',
															'label' => 'NOT IN'
														),
														array(
															'value' => 'EXISTS',
															'label' => 'EXISTS'
														),
														array(
															'value' => 'NOT EXISTS',
															'label' => 'NOT EXISTS'
														),
														array(
															'value' => 'BETWEEN',
															'label' => 'BETWEEN'
														),
														array(
															'value' => 'NOT BETWEEN',
															'label' => 'NOT BETWEEN'
														),
													);
													foreach ( $meta_compares as $k => $v ) {
														$selected = ( isset( $pf_filters_meta['pfm_compare'][$y] ) && $pf_filters_meta['pfm_compare'][$y] == $v['value'] ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $v['value'], $v['label'], $selected );
													}

													printf( '<select name="pfm_compare[%2$s]" id="pfm_compare_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $y );
												?>
													<span class="description"><?php _e( 'Meta compare.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_type_%1$s">%2$s</label>', $y, __( 'Type', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
							
													$meta_types = array(
														array(
															'value' => 'NUMERIC',
															'label' => 'NUMERIC'
														),
														array(
															'value' => 'BINARY',
															'label' => 'BINARY'
														),
														array(
															'value' => 'CHAR',
															'label' => 'CHAR'
														),
														array(
															'value' => 'DATE',
															'label' => 'DATE'
														),
														array(
															'value' => 'DATETIME',
															'label' => 'DATETIME'
														),
														array(
															'value' => 'DECIMAL',
															'label' => 'DECIMAL'
														),
														array(
															'value' => 'SIGNED',
															'label' => 'SIGNED'
														),
														array(
															'value' => 'TIME',
															'label' => 'TIME'
														),
														array(
															'value' => 'UNSIGNED',
															'label' => 'UNSIGNED'
														)
													);
													foreach ( $meta_types as $k => $v ) {
														$selected = ( isset( $pf_filters_meta['pfm_type'][$y] ) && $pf_filters_meta['pfm_type'][$y] == $v['value'] ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $v['value'], $v['label'], $selected );
													}

													printf( '<select name="pfm_type[%2$s]" id="pfm_type_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $y );
												?>
													<span class="description"><?php _e( 'Meta type.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_limit_%1$s">%2$s</label>', $y, __( 'Limit Terms', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-number">
													<?php
														printf( '<input name="pfm_limit[%1$s]" id="pfm_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $y, isset( $pf_filters_meta['pfm_limit'][$y] ) ? $pf_filters_meta['pfm_limit'][$y] : '' ); ?>
													<span class="description"><?php _e( 'Limit number of terms to display in filter.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Multi Select', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Multi Select', 'prdctfltr' );
														?>
														</legend>
														<label for="pfm_multiselect_<?php echo $y; ?>">
														<?php
															printf( '<input name="pfm_multiselect[%1$s]" id="pfm_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $y, ( isset( $pf_filters_meta['pfm_multiselect'][$y] ) && $pf_filters_meta['pfm_multiselect'][$y] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to enable multi term selection.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_relation_%1$s">%2$s</label>', $y, __( 'Multi Select Terms Relation', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
															'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset( $pf_filters_meta['pfm_relation'][$y] ) && $pf_filters_meta['pfm_relation'][$y] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfm_relation[%2$s]" id="pfm_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $y );
													?>
													<span class="description"><?php _e( 'Select term relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Hide None', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Hide None', 'prdctfltr' );
														?>
														</legend>
														<label for="pfm_none_<?php echo $y; ?>">
														<?php
															printf( '<input name="pfm_none[%1$s]" id="pfm_none_%1$s" type="checkbox" value="yes" %2$s />', $y, ( isset( $pf_filters_meta['pfm_none'][$y] ) && $pf_filters_meta['pfm_none'][$y] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to hide none in the current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_term_customization_%1$s">%2$s</label>', $y, __( 'Style Customization Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfm_term_customization[%1$s]" id="pfm_term_customization_%1$s" class="pf_term_customization" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $y, ( isset( $pf_filters_meta['pfm_term_customization'][$y] ) ? $pf_filters_meta['pfm_term_customization'][$y] : '' ) );
													?>
													<span class="description"><?php _e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_filter_customization_%1$s">%2$s</label>', $y, __( 'Terms Customization Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfm_filter_customization[%1$s]" id="pfm_filter_customization_%1$s" class="pf_filter_customization" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $y, ( isset( $pf_filters_meta['pfm_filter_customization'][$y] ) ? $pf_filters_meta['pfm_filter_customization'][$y] : '' ) );
													?>
													<span class="description"><?php _e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?php
							$y++;
						}
						else if ( !in_array( $v, array( 'advanced', 'range', 'meta' ) ) ) {
							if ( substr( $v, 0, 3 ) == 'pa_' && !taxonomy_exists( $v ) ) {
								continue;
							}
						?>
							<div class="pf_element" data-filter="<?php echo $v; ?>">
								<span><?php echo $pf_filters[$v]; ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder"></div>
							</div>
						<?php
						}
					}
				?>
				</div>

				<p class="form-field prdctfltr_hidden">
					<select name="wc_settings_prdctfltr_active_filters[]" id="wc_settings_prdctfltr_active_filters" class="hidden" multiple="multiple">
					<?php
						foreach ( $pf_filters_selected as $v ) {
							if ( $v == 'advanced' ) {
							?>
								<option value="<?php echo $v; ?>" selected="selected"><?php _e( 'Advanced Filter', 'prdctfltr' ); ?></option>
							<?php
							}
							else if ( $v == 'range' ) {
							?>
									<option value="<?php echo $v; ?>" selected="selected"><?php _e( 'Range Filter', 'prdctfltr' ); ?></option>
							<?php
							}
							else {
								if ( substr( $v, 0, 3 ) == 'pa_' && !taxonomy_exists( $v ) ) {
									continue;
								}
							?>
								<option value="<?php echo $v; ?>" selected="selected"><?php echo $pf_filters[$v]; ?></option>
							<?php
							}
						}
					?>
					</select>
				</p>

			</td>
		</tr><?php
		}

		public static function prdctfltr_add_settings_tab( $settings_tabs ) {
			$settings_tabs['settings_products_filter'] = __( 'Product Filter', 'prdctfltr' );
			return $settings_tabs;
		}

		public static function prdctfltr_settings_tab() {
			woocommerce_admin_fields( self::prdctfltr_get_settings( 'get' ) );
		}

		public static function prdctfltr_update_settings() {

			if ( isset( $_POST['pfa_taxonomy'] ) ) {

				$adv_filters = array();

				for( $i = 0; $i < count( $_POST['pfa_taxonomy'] ); $i++ ) {
					$adv_filters['pfa_title'][$i] = $_POST['pfa_title'][$i];
					$adv_filters['pfa_description'][$i] = $_POST['pfa_description'][$i];
					$adv_filters['pfa_taxonomy'][$i] = $_POST['pfa_taxonomy'][$i];
					$adv_filters['pfa_include'][$i] = ( isset( $_POST['pfa_include'][$i] ) ? $_POST['pfa_include'][$i] : array() );
					$adv_filters['pfa_orderby'][$i] = ( isset( $_POST['pfa_orderby'][$i] ) ? $_POST['pfa_orderby'][$i] : '' );
					$adv_filters['pfa_order'][$i] = ( isset( $_POST['pfa_order'][$i] ) ? $_POST['pfa_order'][$i] : '' );
					$adv_filters['pfa_multiselect'][$i] = ( isset( $_POST['pfa_multiselect'][$i] ) ? $_POST['pfa_multiselect'][$i] : 'no' );
					$adv_filters['pfa_relation'][$i] = ( isset( $_POST['pfa_relation'][$i] ) ? $_POST['pfa_relation'][$i] : 'OR' );
					$adv_filters['pfa_adoptive'][$i] = ( isset( $_POST['pfa_adoptive'][$i] ) ? $_POST['pfa_adoptive'][$i] : 'no' );
					$adv_filters['pfa_none'][$i] = ( isset( $_POST['pfa_none'][$i] ) ? $_POST['pfa_none'][$i] : 'no' );
					$adv_filters['pfa_limit'][$i] = ( isset( $_POST['pfa_limit'][$i] ) ? $_POST['pfa_limit'][$i] : '' );
					$adv_filters['pfa_hierarchy'][$i] = ( isset( $_POST['pfa_hierarchy'][$i] ) ? $_POST['pfa_hierarchy'][$i] : 'no' );
					$adv_filters['pfa_hierarchy_mode'][$i] = ( isset( $_POST['pfa_hierarchy_mode'][$i] ) ? $_POST['pfa_hierarchy_mode'][$i] : 'no' );
					$adv_filters['pfa_mode'][$i] = ( isset( $_POST['pfa_mode'][$i] ) ? $_POST['pfa_mode'][$i] : 'showall' );
					$adv_filters['pfa_style'][$i] = ( isset( $_POST['pfa_style'][$i] ) ? $_POST['pfa_style'][$i] : 'pf_attr_text' );
					$adv_filters['pfa_term_customization'][$i] = ( isset( $_POST['pfa_term_customization'][$i] ) ? $_POST['pfa_term_customization'][$i] : '' );
				}

				update_option( 'wc_settings_prdctfltr_advanced_filters', $adv_filters);

			}

			if ( isset( $_POST['pfr_taxonomy'] ) ) {

				$rng_filters = array();

				for( $i = 0; $i < count( $_POST['pfr_taxonomy'] ); $i++ ) {
					$rng_filters['pfr_title'][$i] = $_POST['pfr_title'][$i];
					$rng_filters['pfr_description'][$i] = $_POST['pfr_description'][$i];
					$rng_filters['pfr_taxonomy'][$i] = $_POST['pfr_taxonomy'][$i];
					$rng_filters['pfr_include'][$i] = ( isset( $_POST['pfr_include'][$i] ) ? $_POST['pfr_include'][$i] : array() );
					$rng_filters['pfr_orderby'][$i] = ( isset( $_POST['pfr_orderby'][$i] ) ? $_POST['pfr_orderby'][$i] : '' );
					$rng_filters['pfr_order'][$i] = ( isset( $_POST['pfr_order'][$i] ) ? $_POST['pfr_order'][$i] : '' );
					$rng_filters['pfr_style'][$i] = ( isset( $_POST['pfr_style'][$i] ) ? $_POST['pfr_style'][$i] : 'flat' );
					$rng_filters['pfr_grid'][$i] = ( isset( $_POST['pfr_grid'][$i] ) ? $_POST['pfr_grid'][$i] : 'no' );
					$rng_filters['pfr_adoptive'][$i] = ( isset( $_POST['pfr_adoptive'][$i] ) ? $_POST['pfr_adoptive'][$i] : 'no' );
					$rng_filters['pfr_custom'][$i] = ( isset( $_POST['pfr_custom'][$i] ) ? $_POST['pfr_custom'][$i] : 'no' );
				}

				update_option( 'wc_settings_prdctfltr_range_filters', $rng_filters);

			}

			if ( isset( $_POST['pfm_term_customization'] ) ) {

				$mta_filters = array();

				for( $i = 0; $i < count( $_POST['pfm_key'] ); $i++ ) {
					$mta_filters['pfm_title'][$i] = $_POST['pfm_title'][$i];
					$mta_filters['pfm_description'][$i] = $_POST['pfm_description'][$i];
					$mta_filters['pfm_key'][$i] = $_POST['pfm_key'][$i];
					$mta_filters['pfm_compare'][$i] = ( isset( $_POST['pfm_compare'][$i] ) ? $_POST['pfm_compare'][$i] : '=' );
					$mta_filters['pfm_type'][$i] = ( isset( $_POST['pfm_type'][$i] ) ? $_POST['pfm_type'][$i] : 'NUMERIC' );
					$mta_filters['pfm_multiselect'][$i] = ( isset( $_POST['pfm_multiselect'][$i] ) ? $_POST['pfm_multiselect'][$i] : 'no' );
					$mta_filters['pfm_limit'][$i] = ( isset( $_POST['pfm_limit'][$i] ) ? $_POST['pfm_limit'][$i] : '' );
					$mta_filters['pfm_relation'][$i] = ( isset( $_POST['pfm_relation'][$i] ) ? $_POST['pfm_relation'][$i] : 'OR' );
					$mta_filters['pfm_none'][$i] = ( isset( $_POST['pfm_none'][$i] ) ? $_POST['pfm_none'][$i] : 'no' );
					$mta_filters['pfm_term_customization'][$i] = ( isset( $_POST['pfm_term_customization'][$i] ) ? $_POST['pfm_term_customization'][$i] : '' );
					$mta_filters['pfm_filter_customization'][$i] = ( isset( $_POST['pfm_filter_customization'][$i] ) ? $_POST['pfm_filter_customization'][$i] : '' );
				}

				update_option( 'wc_settings_prdctfltr_meta_filters', $mta_filters);

			}

			if ( isset( $_POST['wc_settings_prdctfltr_active_filters'] ) ) {
				update_option( 'wc_settings_prdctfltr_active_filters', $_POST['wc_settings_prdctfltr_active_filters'] );
			}

			woocommerce_update_options( self::prdctfltr_get_settings( 'update' ) );

			delete_transient( 'prdctfltr_default' );

		}

		public static function prdctfltr_get_settings( $action = 'get' ) {

			$catalog_categories = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );
			$curr_cats = array();
			if ( !empty( $catalog_categories ) && !is_wp_error( $catalog_categories ) ){
				foreach ( $catalog_categories as $term ) {
					$curr_cats[self::prdctfltr_utf8_decode( $term->slug )] = $term->name;
				}
			}

			$catalog_tags = get_terms( 'product_tag', array( 'hide_empty' => 0 ) );
			$curr_tags = array();
			if ( !empty( $catalog_tags ) && !is_wp_error( $catalog_tags ) ){
				foreach ( $catalog_tags as $term ) {
					$curr_tags[self::prdctfltr_utf8_decode( $term->slug )] = $term->name;
				}
			}

			$catalog_chars = ( taxonomy_exists( 'characteristics' ) ? get_terms( 'characteristics', array( 'hide_empty' => 0 ) ) : array() );
			$curr_chars = array();
			if ( !empty( $catalog_chars ) && !is_wp_error( $catalog_chars ) ){
				foreach ( $catalog_chars as $term ) {
					$curr_chars[self::prdctfltr_utf8_decode( $term->slug )] = $term->name;
				}
			}

			$attribute_taxonomies = wc_get_attribute_taxonomies();

			$product_taxonomies = get_object_taxonomies( 'product' );

			$ready_tax = array();
			foreach( $product_taxonomies as $product_tax ) {
				if ( $product_tax == 'product_type' ) {
					continue;
				}
				$tax = get_taxonomy( $product_tax );

				$ready_tax[$product_tax] = $tax->labels->name;
			}

			$curr_filters = array(
				'sort' => __( 'Sort By', 'prdctfltr' ),
				'price' => __( 'By Price', 'prdctfltr' ),
				'vendor' => __( 'Vendor', 'prdctfltr' ),
				'instock' => __( 'In Stock Filter', 'prdctfltr' ),
				'per_page' => __( 'Products Per Page', 'prdctfltr' ),
				'search' => __( 'Search Fitler', 'prdctfltr' )
			);

			if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'no' ) {
				unset( $curr_filters['char'] );
			}

			$curr_attr = array();
			if ( $attribute_taxonomies ) {
				foreach ( $attribute_taxonomies as $tax ) {
					$curr_label = !empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
					$curr_attr['pa_' . $tax->attribute_name] = ucfirst( $curr_label );
				}
			}

			$pf_filters = $curr_filters + $curr_attr;

			foreach( $ready_tax as $k => $v ) {
				if ( !array_key_exists( $k, $pf_filters ) ) {
					$pf_filters[$k] = $v;
				}
			}

			$vendors = get_users( 'orderby=nicename' );
			$ready_vendors = array();

			foreach ( $vendors as $vendor ) {
				$ready_vendors[$vendor->ID] = $vendor->display_name;
			}

			if ( $action == 'get' ) {
		?>
		<ul class="subsubsub<?php echo ( isset( $_GET['section'] ) ? ' wcpf_mode_' . $_GET['section'] : ' wcpf_mode_presets' ); ?>">
		<?php
			$sections = array(
				'presets' => array(
					'title' => __( 'Default Filter and Filter Presets', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-filter"></i>'
				),
				'overrides' => array(
					'title' => __( 'Filter Overrides and Restrictions', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-overrides"></i>'
				),
				'advanced' => array(
					'title' => __( 'Installation and Advanced Options', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-terms"></i>'
				),
				'analytics' =>array(
					'title' => __( 'Filter Analytics', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-analytics"></i>'
				),
				'register' =>array(
					'title' => __( 'Register and Automatic Updates', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-update"></i>'
				)
			);

			$i=0;
			foreach ( $sections as $k => $v ) {

				$curr_class = ( isset( $_GET['section'] ) && $_GET['section'] == $k ) || ( !isset( $_GET['section'] ) && $k == 'presets' ) ? true : false;

				printf( '<li class="button-primary%5$s"><a href="%1$s"%3$s>%4$s %2$s</a></li>', admin_url( 'admin.php?page=wc-settings&tab=settings_products_filter&section=' . $k ), $v['title'], $curr_class !== false ? ' class="current"' : '', $v['icon'], $curr_class !== false ? ' active' : '' );

				$i++;
			}
			printf( '<li class="button-primary pink"><a href="%1$s" target="_blank"><i class="prdctfltr-check"></i> %2$s</a></li>', 'http://codecanyon.net/user/dzeriho/portfolio?ref=dzeriho', __( 'Get more awesome plugins for WooCommerce!', 'prdctfltr' ) );
			if ( isset( $_GET['section'] ) && $_GET['section'] == 'advanced' ) {
				printf( '<li class="button-primary red"><a href="%1$s" id="pf_reset_options" target="_blank"><i class="prdctfltr-delete"></i> %2$s</a></li>', '#', __( 'Reset Options!', 'prdctfltr' ) );
			}
		?>
		</ul>
		<br class="clear" />
		<?php
			}
			if ( isset( $_GET['section'] ) && $_GET['section'] == 'register' ) {

				$settings = array();

				$settings = array(
					'section_register_title' => array(
						'name' => __( 'Product Filter Registration', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'By entering your purchase code you will unlock the Automatic Updates option! Use one license per domain please!', 'prdctfltr' )
					),
					'prdctfltr_purchase_code' => array(
						'name' => __( 'Register Product Filter', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter your purchase code to get instant updated even before the codecanyon.net releases!', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_purchase_code',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_register_end' => array(
						'type' => 'sectionend'
					)
				);

			}
			else if ( isset( $_GET['section'] ) && $_GET['section'] == 'analytics' ) {

				$settings = array();

				$settings = array(
					'section_analytics_title' => array(
						'name' => __( 'Product Filter Analytics Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'Follow your customers filtering data. BETA VERSION Please note, this section and its features will be extended in the future updates.', 'prdctfltr' )
					),
					'prdctfltr_use_analytics' => array(
						'name' => __( 'Use Filtering Analytics', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use filtering analytics.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_analytics',
						'default' => 'no'
					),
					'prdctfltr_filtering_analytics' => array(
						'name' => __( 'Filtering Analytics', 'prdctfltr' ),
						'type' => 'pf_filter_analytics',
						'desc' => __( 'See what your customers are searching for.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_filtering_analytics',
						'default' => 'no'
					),
					'section_analytics_end' => array(
						'type' => 'sectionend'
					)
				);

			}
			else if ( isset( $_GET['section'] ) && $_GET['section'] == 'advanced' ) {
				$curr_theme = wp_get_theme();
				$more_overrides_std = ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'yes' ? array( 'product_cat', 'product_tag', 'characteristics' ) : array( 'product_cat', 'product_tag' ) );

				$settings = array(
					'section_general_title' => array(
						'name' => __( 'Product Filter Shop/Product Archives Installation Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'General installation settings for Shop and Product Archive pages.', 'prdctfltr' ) . '<div id="prdctfltr_installation"></div>'
					),
					'prdctfltr_enable' => array(
						'name' => __( 'Product Filter Shop/Product Archives Installation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select method for installing the Product Filter template in your Shop and Product Archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable',
						'options' => array(
							'yes' => __( 'Override Default WooCommerce Templates', 'prdctfltr' ),
							'no' => __( 'Use Widget', 'prdctfltr' ),
							'action' => __( 'Custom Action', 'prdctfltr' )
						),
						'default' => 'yes',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_enable_overrides' => array(
						'name' => __( 'Select Filtering Templates', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select which WooCommerce templates should the Product Filter replace. Use CTRL+Click to select multiple templates or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable_overrides',
						'options' => array(
							'orderby' => __( 'Order By', 'prdctfltr' ),
							'result-count' => __( 'Result Count', 'prdctfltr' )
						),
						'default' => array( 'orderby', 'result-count' ),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_enable_action' => array(
						'name' => __( 'Product Filter Custom Action', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom products action to initiate the Product Filter template.Use actions from your theme archive-product.php template. Please enter action name in following format action_name:priority. E.G. woocommerce_before_shop_loop:40 woocommerce_archive_description:50', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable_action',
						'default' => 'woocommerce_archive_description:50',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_default_templates' => array(
						'name' => __( 'Enable/Disable Default Orderby Templates', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide orderby.php and result-count.php templates.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_default_templates',
						'default' => 'no'
					),
					'section_general_end' => array(
						'type' => 'sectionend'
					),

					'section_ajax_title' => array(
						'name' => __( 'Product Filter AJAX Shop/Product Archives Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'AJAX Shop/Product Archives Settings - Setup this section to use AJAX on Shop and Product Archive pages. AJAX shortcodes also need correct jQuery selectors to work properly. If your theme is good you should not set anything. Check more information in the ', 'prdctfltr' ) . '<a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/#installation-sdgs" target="_blank">' . __( 'Specific Theme Installations', 'prdctfltr' ) . '</a>'
					),
					'prdctfltr_use_ajax' => array(
						'name' => __( 'Use AJAX On Product Archives', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use AJAX load on shop and product archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_ajax',
						'default' => 'no'
					),
					'prdctfltr_ajax_class' => array(
						'name' => __( 'AJAX Wrapper jQuery Selector', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom wrapper jQuery selector if the default setting is not working. Default selector: .products', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_category_class' => array(
						'name' => __( 'AJAX Category jQuery Selector', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom category jQuery selector if the default setting is not working. Default selector: .product-category', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_category_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_product_class' => array(
						'name' => __( 'AJAX Product jQuery Selector', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom products jQuery selector if the default setting is not working. Default selector: .type-product', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_product_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_pagination_class' => array(
						'name' => __( 'AJAX Pagination jQuery Selector', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom pagination jQuery selector if the default setting is not working. Default selector: .woocommerce-pagination', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_pagination_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),

					'prdctfltr_ajax_count_class' => array(
						'name' => __( 'AJAX Result Count jQuery Selector', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom result count jQuery selector if the default setting is not working. Default selector: .woocommerce-result-count', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_count_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),

					'prdctfltr_ajax_orderby_class' => array(
						'name' => __( 'AJAX Order By jQuery Selector', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom order by jQuery selector if the default setting is not working. Default selector: .woocommerce-ordering', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_orderby_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),

					'prdctfltr_ajax_columns' => array(
						'name' => __( 'AJAX Product Columns', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'In how many columns are your product displayed on the shop and product archive pages by default?', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_columns',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_rows' => array(
						'name' => __( 'AJAX Product Rows', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'In how many rows are your product displayed on the shop and product archive pages by default?', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_rows',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_pagination_type' => array(
						'name' => __( 'Select Pagination Type', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select pagination template to use.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_pagination_type',
						'options' => array(
							'default' => __( 'Default (In Theme)', 'prdctfltr' ),
							'prdctfltr-pagination-default' => __( 'Product Filter Pagination', 'prdctfltr' ),
							'prdctfltr-pagination-load-more' => __( 'Product Filter Load More', 'prdctfltr' )
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_pagination' => array(
						'name' => __( 'Custom Pagination Function', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Function for displaying pagination. Default function: woocommerce_pagination', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_pagination',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_product_animation' => array(
						'name' => __( 'Select Product Loading Animation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select animation when showing new products.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_product_animation',
						'options' => array(
							'none' => __( 'No Animation', 'prdctfltr' ),
							'default' => __( 'Fade Each Product', 'prdctfltr' ),
							'slide' => __( 'Slide Each Product', 'prdctfltr' ),
							'random' => __( 'Fade Random Products', 'prdctfltr' )
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_after_ajax_scroll' => array(
						'name' => __( 'AJAX Pagination Scroll', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select type of scrolling animation after using the AJAX pagination.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_after_ajax_scroll',
						'options' => array(
							'none' => __( 'No Animation', 'prdctfltr' ),
							'products' => __( 'Scroll to Products', 'prdctfltr' ),
							'top' => __( 'Scroll to Top', 'prdctfltr' )
						),
						'default' => 'products',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_permalink' => array(
						'name' => __( 'Disable AJAX Permalinks', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to disable browser address bar URL changes.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_permalink',
						'default' => 'no'
					),
					'prdctfltr_ajax_failsafe' => array(
						'name' => __( 'AJAX Failsafe Check', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select elemets to check before calling AJAX function in Shop/Product Archives.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_failsafe',
						'options' => array(
							'wrapper' => __( 'Products Wrapper', 'prdctfltr' ),
							'product' => __( 'Products Found', 'prdctfltr' ),
							'pagination' => __( 'Pagination', 'prdctfltr' )
						),
						'default' => array( 'wrapper', 'product' ),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_js' => array(
						'name' => __( 'AJAX jQuery and JS Refresh', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Input jQuery or JS code to execute after AJAX calls. This option is useful if the JS is broken after these calls.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_js',
						'default' => '',
						'css' 		=> 'min-width:600px;margin-top:12px;min-height:150px;',
					),
					'section_ajax_end' => array(
						'type' => 'sectionend'
					),

					'section_advanced_title' => array(
						'name' => __( 'Product Filter Advanced Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'Advanced Settings - These settings will affect all filters.', 'prdctfltr' )
					),

					'prdctfltr_custom_tax' => array(
						'name' => __( 'Use Characteristics', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Enable this option to get custom characteristics product meta box.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax',
						'default' => 'no',
					),
					'prdctfltr_clearall' => array(
						'name' => __( 'Clear All Action', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Selected filters will not be affected the Clear All action.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_clearall',
						'options' => $pf_filters,
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_instock' => array(
						'name' => __( 'Show In Stock Products by Default', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to show the In Stock products by default.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock',
						'default' => 'no'
					),
					'prdctfltr_hideempty' => array(
						'name' => __( 'Hide Empty Terms in Filters', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this checkbox to hide empty terms in filters.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_hideempty',
						'default' => 'no',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_use_variable_images' => array(
						'name' => __( 'Use Variable Images', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use variable images override on Shop and Product Archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_variable_images',
						'default' => 'no'
					),
					'prdctfltr_taxonomy_relation' => array(
						'name' => __( 'Filter Taxonomy Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Set filter relation for product taxonomies.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_taxonomy_relation',
						'options' => array(
							'AND' => __( 'AND', 'prdctfltr' ),
							'OR' => __( 'OR', 'prdctfltr' )
						),
						'default' => 'AND',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_disable_scripts' => array(
						'name' => __( 'Disable JavaScript Libraries', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select JavaScript libraries to disable. Use CTRL+Click to select multiple libraries or deselect all. Selected libraries will not be loaded.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_scripts',
						'options' => array(
							'ionrange' => __( 'Ion Range Slider', 'prdctfltr' ),
							'isotope' => __( 'Isotope', 'prdctfltr' ),
							'mcustomscroll' => __( 'Malihu jQuery Scrollbar', 'prdctfltr' )
						),
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_more_overrides' => array(
						'name' => __( 'Supported Filter Overrides', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select taxonomies that will support the Product Filter Overrides.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_more_overrides',
						'options' => $ready_tax,
						'default' => $more_overrides_std,
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_advanced_end' => array(
						'type' => 'sectionend'
					),
					'section_noneajax_title' => array(
						'name' => __( 'Product Filter Product Archives Settings (disabled AJAX)', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'Setup options when AJAX is disabled on Shop and Product Archives.', 'prdctfltr' )
					),
					'prdctfltr_force_product' => array(
						'name' => __( 'Force Post Type Variable', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option if you are having issues with the searches. This options should never be checked unless something is wrong with the template you are using. Option will add the ?post_type=product parameter when filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_product',
						'default' => 'no'
					),
					'prdctfltr_force_action' => array(
						'name' => __( 'Force Stay on Permalink', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to force filtering on the same permalink (URL).', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_action',
						'default' => 'no'
					),
					'prdctfltr_force_redirects' => array(
						'name' => __( 'Disable Product Filter Redirects', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option if you are having issues with the shop page redirects.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_redirects',
						'default' => 'no'
					),
					'prdctfltr_remove_single_redirect' => array(
						'name' => __( 'Single Product Redirect', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Uncheck to enable single product page redirect when only one product is found.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_remove_single_redirect',
						'default' => 'yes'
					),
					'section_noneajax_end' => array(
						'type' => 'sectionend'
					),
				);
			}
			else if ( ( isset( $_GET['section'] ) && $_GET['section'] == 'presets' ) || !isset( $_GET['section'] ) ) {
				if ( $action == 'get' ) {

					printf( '<h3>%1$s</h3><p>%2$s</p><p>', __( 'Product Filter Preset Manager', 'prdctfltr' ), __( 'Manage filter presets. Load, delete and save presets. Saved filter presets can be used with shortcodes, filter overrides and widgets. Default filter preset will always be used unless the preset is specified by shortcode, filter override or the widget parameter.', 'prdctfltr' ) );
			?>
							<select id="prdctfltr_filter_presets">
								<option value="default"><?php _e( 'Default', 'wcwar' ); ?></option>
								<?php
									$curr_presets = get_option( 'prdctfltr_templates', array() );

									if ( !empty( $curr_presets) ) {
										foreach ( $curr_presets as $k => $v ) {
									?>
											<option value="<?php echo $k; ?>"><?php echo $k; ?></option>
									<?php
										}
									}
								?>
							</select>
			<?php
					printf( '<a href="#" id="prdctfltr_save" class="button-primary">%1$s</a> <a href="#" id="prdctfltr_load" class="button-primary">%2$s</a> <a href="#" id="prdctfltr_delete" class="button-primary">%3$s</a> <a href="#" id="prdctfltr_reset_default" class="button-primary">%4$s</a> <a href="#" id="prdctfltr_save_default" class="button-primary">%5$s</a></p>', __( 'Save as preset', 'prdctfltr' ), __( 'Load', 'prdctfltr' ), __( 'Delete', 'prdctfltr' ), __( 'Reset to default', 'prdctfltr' ), __( 'Save as default preset', 'prdctfltr' ) );
					printf( '<p>%1$s: <span id="prdctfltr_slug_container">[prdctfltr_sc_products]</span></p>', __( 'To use selected preset in shortcodes on pages use the following syntax', 'prdctfltr' ) );

				}

				$settings = array(
					'section_basic_title' => array(
						'name'     => __( 'General Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter basic settings and appearance.', 'prdctfltr' ) . '<span class="wcpff_basic"></span>'
					),
					'prdctfltr_always_visible' => array(
						'name' => __( 'Always Visible', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'This option will make Product Filter visible without the slide up/down animation at all times.', 'prdctfltr' ) . ' <em>' . __( '(Does not work with the Arrow presets as these presets are absolutely positioned and the widget version)', 'prdctfltr' ) . '</em>',
						'id'   => 'wc_settings_prdctfltr_always_visible',
						'default' => 'no',
					),
					'prdctfltr_click_filter' => array(
						'name' => __( 'Instant Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to disable the filter button and use instant product filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_click_filter',
						'default' => 'no',
					),
					'prdctfltr_show_counts' => array(
						'name' => __( 'Show Term Products Count', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to show products count with the terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_counts',
						'default' => 'no',
					),
					'prdctfltr_show_search' => array(
						'name' => __( 'Show Term Search Fields', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to show search fields on supported filters.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_search',
						'default' => 'no',
					),
					'prdctfltr_adoptive' => array(
						'name' => __( 'Enable/Disable Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable the adoptive filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive',
						'default' => 'no',
					),
					'prdctfltr_adoptive_mode' => array(
						'name' => __( 'Select Adoptive Filtering Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select mode to use with the filtered terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_mode',
						'options' => array(
							'always' => __( 'Always Active', 'prdctfltr' ),
							'permalink' => __( 'Active on Permalinks and Filters', 'prdctfltr' ),
							'filter' => __( 'Active on Filters', 'prdctfltr' )
						),
						'default' => 'permalink',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_adoptive_style' => array(
						'name' => __( 'Select Adoptive Filtering Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style to use with the filtered terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_style',
						'options' => array(
							'pf_adptv_default' => __( 'Hide Terms', 'prdctfltr' ),
							'pf_adptv_unclick' => __( 'Disabled and Unclickable', 'prdctfltr' ),
							'pf_adptv_click' => __( 'Disabled but Clickable', 'prdctfltr' )
						),
						'default' => 'pf_adptv_default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_adoptive_depend' => array(
						'name' => __( 'Select Adoptive Filtering Dependencies', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Adoptive filters can depend only on ceratin taxonomies. Select taxonomies to include. Use CTRL+Click to select multiple taxonomies or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_depend',
						'options' => $ready_tax,
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_show_counts_mode' => array(
						'name' => __( 'Adoptive Term Products Count Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select how to display the product count when adoptive filtering is used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_counts_mode',
						'options' => array(
							'default' => __( 'Filtered Count / Total', 'prdctfltr' ),
							'count' => __( 'Filtered Count', 'prdctfltr' ),
							'total' => __( 'Total', 'prdctfltr' )
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_adoptive_reorder' => array(
						'name' => __( 'Reorder Adoptive Terms', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to reorder adoptive terms to front.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_reorder',
						'default' => 'yes',
					),
					'prdctfltr_tabbed_selection' => array(
						'name' => __( 'Stepped Filter Selection', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable stepped selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tabbed_selection',
						'default' => 'no',
					),
					'prdctfltr_collector' => array(
						'name' => __( 'Selected Terms Collector', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Display selected terms in a collector before filters. Select style.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_collector',
						'options' => array(
							'off' => __( 'Disabled', 'prdctfltr' ),
							'flat' => __( 'Flat', 'prdctfltr' ),
							'border' => __( 'Border', 'prdctfltr' )
						),
						'default' => 'off',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_selected_reorder' => array(
						'name' => __( 'Reorder Selected Terms', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to reorder selected terms to front.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_selected_reorder',
						'default' => 'no',
					),
					'prdctfltr_disable_bar' => array(
						'name' => __( 'Disable Top Bar', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the Product Filter top bar. This option will also make the filter always visible.', 'prdctfltr' ) . ' <em>' . __( '(Does not work with the Arrow presets as these presets are absolutely positioned and the widget version)', 'prdctfltr' ) . '</em>',
						'id'   => 'wc_settings_prdctfltr_disable_bar',
						'default' => 'no',
					),
					'prdctfltr_disable_showresults' => array(
						'name' => __( 'Disable Show Results Title', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the show results text from the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_showresults',
						'default' => 'no',
					),
					'prdctfltr_disable_sale' => array(
						'name' => __( 'Disable Sale Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the Product Filter sale button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_sale',
						'default' => 'no',
					),
					'prdctfltr_disable_instock' => array(
						'name' => __( 'Disable In Stock Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the Product Filter in stock button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_instock',
						'default' => 'no',
					),
					'prdctfltr_disable_reset' => array(
						'name' => __( 'Disable Clear All Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the Clear All button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_reset',
						'default' => 'no',
					),
					'prdctfltr_custom_action' => array(
						'name' => __( 'Override Filter Form Action', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Advanced users can override filter form action. Please check documentation for more details.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_action',
						'default' => '',
						'css' 		=> 'width:300px;margin-right:12px;',
					),
					'prdctfltr_noproducts' => array(
						'name' => __( 'Override No Products Action', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Input HTML/Shortcode to override the default action when no products are found. Default action means that random products will be shown when there are no products within the filter query.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_noproducts',
						'default' => '',
						'css' 		=> 'min-width:600px;margin-top:12px;min-height:150px;',
					),
					'section_basic_end' => array(
						'type' => 'sectionend'
					),
					'section_style_title' => array(
						'name'     => __( 'Filter Style', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter style settings.', 'prdctfltr' ) . '<span class="wcpff_style"></span>'
					),
					'prdctfltr_style_preset' => array(
						'name' => __( 'Select Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style.', 'prdctfltr' ) . ' ' . __( 'This option does not work with the widget version.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_preset',
						'options' => array(
							'pf_arrow' => __( 'Arrow', 'prdctfltr' ),
							'pf_arrow_inline' => __( 'Arrow Inline', 'prdctfltr' ),
							'pf_default' => __( 'Default', 'prdctfltr' ),
							'pf_default_inline' => __( 'Default Inline', 'prdctfltr' ),
							'pf_select' => __( 'Use Select Box', 'prdctfltr' ),
							'pf_sidebar' => __( 'Fixed Sidebar Left', 'prdctfltr' ),
							'pf_sidebar_right' => __( 'Fixed Sidebar Right', 'prdctfltr' ),
							'pf_sidebar_css' => __( 'Fixed Sidebar Left With Overlay', 'prdctfltr' ),
							'pf_sidebar_css_right' => __( 'Fixed Sidebar Right With Overlay', 'prdctfltr' ),
							'pf_fullscreen' => __( 'Full Screen', 'prdctfltr' ),
						),
						'default' => 'pf_default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_style_mode' => array(
						'name' => __( 'Select Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select mode to use with the filter..', 'prdctfltr' ) . ' ' . __( 'This option does not work with the widget version.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_mode',
						'options' => array(
							'pf_mod_row' => __( 'One Row', 'prdctfltr' ),
							'pf_mod_multirow' => __( 'Multiple Rows', 'prdctfltr' ),
							'pf_mod_masonry' => __( 'Masonry Filters', 'prdctfltr' )
						),
						'default' => 'pf_mod_multirow',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_max_columns' => array(
						'name' => __( 'Max Columns', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'This option sets the number of columns for the filter. This option does not work with the widget version or the fixed sidebar layouts.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_max_columns',
						'default' => 3,
						'custom_attributes' => array(
							'min' 	=> 1,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_limit_max_height' => array(
						'name' => __( 'Limit Max Height', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to limit the Max Height of for the filters.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_limit_max_height',
						'default' => 'no',
					),
					'prdctfltr_max_height' => array(
						'name' => __( 'Max Height', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Set the Max Height value.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_max_height',
						'default' => 150,
						'custom_attributes' => array(
							'min' 	=> 100,
							'max' 	=> 300,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_custom_scrollbar' => array(
						'name' => __( 'Use Custom Scroll Bars', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to override default browser scroll bars with javascrips scrollbars in Max Height mode.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_scrollbar',
						'default' => 'no',
					),
					'prdctfltr_style_checkboxes' => array(
						'name' => __( 'Select Checkbox Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style for the term checkboxes.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_checkboxes',
						'options' => array(
							'prdctfltr_round' => __( 'Round', 'prdctfltr' ),
							'prdctfltr_square' => __( 'Square', 'prdctfltr' ),
							'prdctfltr_checkbox' => __( 'Checkbox', 'prdctfltr' ),
							'prdctfltr_system' => __( 'System Checkboxes', 'prdctfltr' )
						),
						'default' => 'pf_round',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_style_hierarchy' => array(
						'name' => __( 'Select Hierarchy Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style for hierarchy terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_hierarchy',
						'options' => array(
							'prdctfltr_hierarchy_circle' => __( 'Circle', 'prdctfltr' ),
							'prdctfltr_hierarchy_filled' => __( 'Circle Solid', 'prdctfltr' ),
							'prdctfltr_hierarchy_lined' => __( 'Lined', 'prdctfltr' ),
							'prdctfltr_hierarchy_arrow' => __( 'Arrows', 'prdctfltr' )
						),
						'default' => 'prdctfltr_hierarchy_circle',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_button_position' => array(
						'name' => __( 'Select Filter Buttons Position', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select position of the filter buttons, top or bottom.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_button_position',
						'options' => array(
							'bottom' => __( 'Bottom', 'prdctfltr' ),
							'top' => __( 'Top', 'prdctfltr' ),
							'both' => __( 'Both', 'prdctfltr' )
						),
						'default' => 'bottom',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_icon' => array(
						'name' => __( 'Override Filter Icon', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter icon class to override the default Product Filter icon. Use icon class e.g. prdctfltr-filter or FontAwesome fa fa-shopping-cart or any other.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_icon',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_title' => array(
						'name' => __( 'Override Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override default filter heading (Filter Products).', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_submit' => array(
						'name' => __( 'Override Filter Submit Text', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override Filter selected, the default filter submit button text.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_submit',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_loader' => array(
						'name' => __( 'Select AJAX Loader Icon', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select AJAX loader icon.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_loader',
						'options' => array(
							'audio' => __( 'Audio', 'prdctfltr' ),
							'ball-triangle' => __( 'Ball Triangle', 'prdctfltr' ),
							'bars' => __( 'Bars', 'prdctfltr' ),
							'circles' => __( 'Circles', 'prdctfltr' ),
							'grid' => __( 'Grid', 'prdctfltr' ),
							'hearts' => __( 'Hearts', 'prdctfltr' ),
							'oval' => __( 'Oval', 'prdctfltr' ),
							'puff' => __( 'Puff', 'prdctfltr' ),
							'rings' => __( 'Rings', 'prdctfltr' ),
							'spinning-circles' => __( 'Spining Circles', 'prdctfltr' ),
							'tail-spin' => __( 'Tail Spin', 'prdctfltr' ),
							'circles' => __( 'Circles', 'prdctfltr' ),
							'three-dots' => __( 'Three Dots', 'prdctfltr' )
						),
						'default' => 'oval',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_style_end' => array(
						'type' => 'sectionend'
					),
					'section_title' => array(
						'name'     => __( 'Filter Manager', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Create filters! Greens are active, reds are not, blue buttons add as many filters as you need. Setup basic general settings and filter styles. Click the arrow down icon to customize each filter options. Click on the paint icon to customize the filter terms appearance if you do not like the default display options. In here you can add images, colors, custom styles. Click the cogs icon on supporting filters to customize filtering terms. Click the move icon to reorder filters, or use the X to remove them.', 'prdctfltr' )
					),
					'prdctfltr_filters' => array(
						'name' => __( 'Select Filters', 'prdctfltr' ),
						'type' => 'pf_filter',
						'desc' => __( 'Select Filters.', 'prdctfltr' )
					),
					'section_end' => array(
						'type' => 'sectionend'
					),

					'section_perpage_filter_title' => array(
						'name'     => __( 'Products Per Page', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_per_page"></span>',
					),
					'prdctfltr_perpage_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_perpage_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_perpage_label' => array(
						'name' => __( 'Override Products Per Page Filter Label', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter label for the products per page filter.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_label',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_perpage_range' => array(
						'name' => __( 'Per Page Filter Initial', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Initial products per page value.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_range',
						'default' => 20,
						'custom_attributes' => array(
							'min' 	=> 3,
							'max' 	=> 999,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_perpage_range_limit' => array(
						'name' => __( 'Per Page Filter Values', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Number of product per page values.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_range_limit',
						'default' => 5,
						'custom_attributes' => array(
							'min' 	=> 2,
							'max' 	=> 20,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_perpage_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'prdctfltr_perpage_filter_customization' => array(
						'name' => __( 'Terms Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_filter_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_filter_customization'
					),
					'section_perpage_filter_end' => array(
						'type' => 'sectionend'
					),

					'section_vendor_filter_title' => array(
						'name'     => __( 'Vendor', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_vendor"></span>'
					),
					'prdctfltr_vendor_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_vendor_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_vendor_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_vendor_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_vendor' => array(
						'name' => __( 'Select Vendors', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_vendor',
						'options' => $ready_vendors,
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_vendor_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_vendor_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_vendor_filter_end' => array(
						'type' => 'sectionend'
					),

					'section_instock_filter_title' => array(
						'name'     => __( 'In Stock', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_instock"></span>'
					),
					'prdctfltr_instock_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_instock_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_instock_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_instock_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_orderby_filter_title' => array(
						'name'     => __( 'Sort By', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_sort"></span>'
					),
					'prdctfltr_orderby_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_orderby_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_orderby' => array(
						'name' => __( 'Select Terms', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_orderby',
						'options' => array(
								'menu_order'    => __( 'Default', 'prdctfltr' ),
								'comment_count' => __( 'Review Count', 'prdctfltr' ),
								'popularity'    => __( 'Popularity', 'prdctfltr' ),
								'rating'        => __( 'Average rating', 'prdctfltr' ),
								'date'          => __( 'Newness', 'prdctfltr' ),
								'price'         => __( 'Price: low to high', 'prdctfltr' ),
								'price-desc'    => __( 'Price: high to low', 'prdctfltr' ),
								'rand'          => __( 'Random Products', 'prdctfltr' ),
								'title'         => __( 'Product Name', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_orderby_none' => array(
						'name' => __( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_none',
						'default' => 'no',
					),
					'prdctfltr_orderby_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_orderby_filter_end' => array(
						'type' => 'sectionend'
					),

					'section_search_filter_title' => array(
						'name'     => __( 'Search', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_search"></span>'
					),
					'prdctfltr_search_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_search_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_search_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_search_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_search_placeholder' => array(
						'name' => __( 'Override Search Filter Placeholder', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the search filter placeholder.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_search_placeholder',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_search_filter_end' => array(
						'type' => 'sectionend'
					),

					'section_price_filter_title' => array(
						'name'     => __( 'By Price', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_price"></span>'
					),
					'prdctfltr_price_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_price_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_price_range' => array(
						'name' => __( 'Price Range Filter Initial Price', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Initial price for the filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range',
						'default' => 100,
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_range_add' => array(
						'name' => __( 'Price Range Filter Price Add', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Price to add.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range_add',
						'default' => 100,
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_range_limit' => array(
						'name' => __( 'Price Range Filter Intervals', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Number of price intervals to use. E.G. You have set the initial price to 99.9, and the add price is set to 100, you will achieve filtering like 0-99.9, 99.9-199.9, 199.9- 299.9 for the number of times as set in the price intervals setting.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range_limit',
						'default' => 6,
						'custom_attributes' => array(
							'min' 	=> 2,
							'max' 	=> 20,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_none' => array(
						'name' => __( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_none',
						'default' => 'no',
					),
					'prdctfltr_price_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'prdctfltr_price_filter_customization' => array(
						'name' => __( 'Terms Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_filter_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_filter_customization'
					),
					'section_price_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_cat_filter_title' => array(
						'name'     => __( 'Category Filter', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_cat"></span>'
					),
					'prdctfltr_cat_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_cats' => array(
						'name' => __( 'Select Terms', 'prdctfltr' ),
						'type' => 'pf_taxonomy',
						'desc' => __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_cats',
						'options' => 'product_cat',
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_orderby' => array(
						'name' => __( 'Terms Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select terms ordering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_orderby',
						'options' => array(
								'' => __( 'None', 'prdctfltr' ),
								'id' => __( 'ID', 'prdctfltr' ),
								'name' => __( 'Name', 'prdctfltr' ),
								'number' => __( 'Number', 'prdctfltr' ),
								'slug' => __( 'Slug', 'prdctfltr' ),
								'count' => __( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_order' => array(
						'name' => __( 'Term Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_order',
						'options' => array(
								'ASC' => __( 'ASC', 'prdctfltr' ),
								'DESC' => __( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_limit' => array(
						'name' => __( 'Limit Terms', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Limit number of terms to display in filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_cat_hierarchy' => array(
						'name' => __( 'Use Hierarchy', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable hierarchy.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_hierarchy',
						'default' => 'no',
					),
					'prdctfltr_cat_hierarchy_mode' => array(
						'name' => __( 'Hierarchy Mode', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to expand parent terms on load.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_hierarchy_mode',
						'default' => 'no',
					),
					'prdctfltr_cat_mode' => array(
						'name' => __( 'Hierarchy Filtering Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select how to show terms upon filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_mode',
						'options' => array(
								'showall' => __( 'Show all', 'prdctfltr' ),
								'subonly' => __( 'Keep only child terms', 'prdctfltr' )
							),
						'default' => 'showall',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_multi' => array(
						'name' => __( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable multi term selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_multi',
						'default' => 'no',
					),
					'prdctfltr_cat_relation' => array(
						'name' => __( 'Multi Select Terms Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select term relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_relation',
						'options' => array(
								'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_adoptive' => array(
						'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_adoptive',
						'default' => 'no',
					),
					'prdctfltr_cat_none' => array(
						'name' => __( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_none',
						'default' => 'no',
					),
					'prdctfltr_cat_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_cat_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_tag_filter_title' => array(
						'name'     => __( 'Tag Filter', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_tag"></span>'
					),
					'prdctfltr_tag_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_tags' => array(
						'name' => __( 'Select Terms', 'prdctfltr' ),
						'type' => 'pf_taxonomy',
						'desc' => __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_tags',
						'options' => 'product_tag',
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_orderby' => array(
						'name' => __( 'Terms Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select terms ordering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_orderby',
						'options' => array(
								'' => __( 'None', 'prdctfltr' ),
								'id' => __( 'ID', 'prdctfltr' ),
								'name' => __( 'Name', 'prdctfltr' ),
								'number' => __( 'Number', 'prdctfltr' ),
								'slug' => __( 'Slug', 'prdctfltr' ),
								'count' => __( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_order' => array(
						'name' => __( 'Tags Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_order',
						'options' => array(
								'ASC' => __( 'ASC', 'prdctfltr' ),
								'DESC' => __( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_limit' => array(
						'name' => __( 'Limit Terms', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Limit number of terms to display in filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_tag_multi' => array(
						'name' => __( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable multi term selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_multi',
						'default' => 'no',
					),
					'prdctfltr_tag_relation' => array(
						'name' => __( 'Multi Select Terms Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select term relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_relation',
						'options' => array(
								'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_adoptive' => array(
						'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_adoptive',
						'default' => 'no',
					),
					'prdctfltr_tag_none' => array(
						'name' => __( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_none',
						'default' => 'no',
					),
					'prdctfltr_tag_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_tag_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_char_filter_title' => array(
						'name'     => __( 'Characteristics Filter', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_char"></span>'
					),
					'prdctfltr_custom_tax_title' => array(
						'name' => __( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_description' => array(
						'name' => __( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_chars' => array(
						'name' => __( 'Select Terms', 'prdctfltr' ),
						'type' => 'pf_taxonomy',
						'desc' => __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_chars',
						'options' => 'characteristics',
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_orderby' => array(
						'name' => __( 'Terms Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select terms ordering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_orderby',
						'options' => array(
								'' => __( 'None', 'prdctfltr' ),
								'id' => __( 'ID', 'prdctfltr' ),
								'name' => __( 'Name', 'prdctfltr' ),
								'number' => __( 'Number', 'prdctfltr' ),
								'slug' => __( 'Slug', 'prdctfltr' ),
								'count' => __( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_order' => array(
						'name' => __( 'Characteristics Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_order',
						'options' => array(
								'ASC' => __( 'ASC', 'prdctfltr' ),
								'DESC' => __( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_limit' => array(
						'name' => __( 'Limit Terms', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Limit number of terms to display in filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_chars_multi' => array(
						'name' => __( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable multi term selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_multi',
						'default' => 'no',
					),
					'prdctfltr_custom_tax_relation' => array(
						'name' => __( 'Multi Select Terms Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select term relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_relation',
						'options' => array(
								'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_chars_adoptive' => array(
						'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_adoptive',
						'default' => 'no',
					),
					'prdctfltr_chars_none' => array(
						'name' => __( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_none',
						'default' => 'no',
					),
					'prdctfltr_chars_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_char_filter_end' => array(
						'type' => 'sectionend'
					),

				);

				if ( $attribute_taxonomies) {
					$settings = $settings + array (
						
					);
					foreach ( $attribute_taxonomies as $tax) {

						$catalog_attrs = get_terms( 'pa_' . $tax->attribute_name, array( 'hide_empty' => 0 ) );
						$curr_attrs = array();
						if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
							foreach ( $catalog_attrs as $term ) {
								$curr_attrs[self::prdctfltr_utf8_decode( $term->slug )] = $term->name;
							}
						}

						$tax->attribute_label = !empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;

						$settings = $settings + array(
							'section_pa_' . $tax->attribute_name.'_title' => array(
								'name'     => ucfirst( $tax->attribute_label ) . ' ' . __( 'Filter', 'prdctfltr' ),
								'type'     => 'title',
								'desc'     => __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_pa_' . $tax->attribute_name . '"></span>'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_title' => array(
								'name' => __( 'Filter Title', 'prdctfltr' ),
								'type' => 'text',
								'desc' => __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_title',
								'default' => '',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_description' => array(
								'name' => __( 'Filter Description', 'prdctfltr' ),
								'type' => 'textarea',
								'desc' => __( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_description',
								'default' => '',
								'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
							),
							'prdctfltr_include_pa_' . $tax->attribute_name => array(
								'name' => __( 'Select Terms', 'prdctfltr' ),
								'type' => 'pf_taxonomy',
								'desc' => __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_include_pa_' . $tax->attribute_name,
								'options' => 'pa_' . $tax->attribute_name,
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name => array(
								'name' => __( 'Appearance', 'prdctfltr' ) . ' DEPRECATED DO NOT USE! USE PAINT BUCKET TOOL AND SET APPEARANCE TO TEXT!<em class="pf_deprecated"></em>',
								'type' => 'select',
								'desc' => __( 'Select style preset to use with the current attribute.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name,
								'options' => array(
									'pf_attr_text' => __( 'Text', 'prdctfltr' ),
									'pf_attr_imgtext' => __( 'Thumbnails with text', 'prdctfltr' ),
									'pf_attr_img' => __( 'Thumbnails only', 'prdctfltr' )
								),
								'default' => 'pf_attr_text',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_orderby' => array(
								'name' => __( 'Terms Order By', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select terms ordering.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_orderby',
								'options' => array(
										'' => __( 'None', 'prdctfltr' ),
										'id' => __( 'ID', 'prdctfltr' ),
										'name' => __( 'Name', 'prdctfltr' ),
										'number' => __( 'Number', 'prdctfltr' ),
										'slug' => __( 'Slug', 'prdctfltr' ),
										'count' => __( 'Count', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_order' => array(
								'name' => __( 'Terms Order', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select ascending or descending order.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_order',
								'options' => array(
										'ASC' => __( 'ASC', 'prdctfltr' ),
										'DESC' => __( 'DESC', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_limit' => array(
								'name' => __( 'Limit Terms', 'prdctfltr' ),
								'type' => 'number',
								'desc' => __( 'Limit number of terms to display in filter.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_limit',
								'default' => 0,
								'custom_attributes' => array(
									'min' 	=> 0,
									'max' 	=> 100,
									'step' 	=> 1
								),
								'css' => 'width:100px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_hierarchy' => array(
								'name' => __( 'Use Hierarchy', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( 'Check this option to enable terms hierarchy.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_hierarchy',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_hierarchy_mode' => array(
								'name' => __( 'Hierarchy Mode', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( ' Check this option to expand parent terms on load.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_hierarchy_mode',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_mode' => array(
								'name' => __( 'Hierarchy Filtering Mode', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select how to show terms upon filtering.', 'prdctfltr' ),
								'id'   => 'wc_settings_pa_' . $tax->attribute_name.'_mode',
								'options' => array(
										'showall' => __( 'Show all', 'prdctfltr' ),
										'subonly' => __( 'Keep only child terms', 'prdctfltr' )
									),
								'default' => 'showall',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_multi' => array(
								'name' => __( 'Use Multi Select', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( 'Check this option to enable multi term selection.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_multi',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_relation' => array(
								'name' => __( 'Multi Select Terms Relation', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select term relation when multiple terms are selected.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_relation',
								'options' => array(
										'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
										'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_adoptive' => array(
								'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_adoptive',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_none' => array(
								'name' => __( 'Hide None', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_none',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_term_customization' => array(
								'name' => __( 'Style Customization Key', 'prdctfltr' ),
								'type' => 'text',
								'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_term_customization',
								'default' => '',
								'css' => 'width:300px;margin-right:12px;',
								'class' => 'pf_term_customization'
							),
							'section_pa_' . $tax->attribute_name.'_end' => array(
								'type' => 'sectionend'
							),

						);
					}
				}

			}
			else if ( isset( $_GET['section'] ) && $_GET['section'] == 'overrides' ) {

				$curr_presets = get_option( 'prdctfltr_templates', array() );
				$curr_theme = wp_get_theme();

				$curr_presets_set = array();
				foreach( $curr_presets as $q => $w ) {
					$curr_presets_set[$q] = $q;
				}

				$settings = array(
					'section_overrides_filter_title' => array(
						'name'     => __( 'Shop and Archives Appearance', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup Shop and Product Archives appearance.', 'prdctfltr' )
					),
					'prdctfltr_shop_disable' => array(
						'name' => __( 'Enable/Disable Shop Page Product Filter', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option in order to disable the Product Filter on Shop page. This option can be useful for themes with custom Shop pages, if checked the default WooCommerce or', 'prdctfltr' ) . ' ' . $curr_theme->get( 'Name' ) . ' ' . __( 'filter template will be overriden only on product archives that support it.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_shop_disable',
						'default' => 'no'
					),
					'prdctfltr_shop_page_override' => array(
						'name' => __( 'Shop Page Override', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Override default template on the shop page.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_shop_page_override',
						'options' => array( '' => __( 'Default' ) ) + $curr_presets_set,
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_disable_display' => array(
						'name' => __( 'Shop/Category Display Types And Product Filter', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select what display types will not show the Product Filter.  Use CTRL+Click to select multiple display types or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_display',
						'options' => array(
							'subcategories' => __( 'Show Categories', 'prdctfltr' ),
							'both' => __( 'Show Both', 'prdctfltr' )
						),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_overrides_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_restrictions_title' => array(
						'name'     => __( 'Product Filter Restrictions', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Limit filter appearance with Product Filter restrictions.', 'prdctfltr' ) . '<span class="wcpfs_instock"></span>'
					),
					'prdctfltr_showon_product_cat' => array(
						'name' => __( 'Show Filter Only On Categories', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'To show filter only on certain categories in Shop and Product Archives, select them from the list.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_showon_product_cat',
						'options' => $curr_cats,
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_restrictions_end' => array(
						'type' => 'sectionend'
					)
				);
				if ( $action == 'get' ) {
					$curr_or_settings = get_option( 'prdctfltr_overrides', array() );
				?>
					<h3><?php _e( 'Product Filter Overrides and Restrictions', 'prdctfltr' ); ?></h3>
					<p><?php _e( 'Override default filters. Select the term you wish and the desired filter preset and click Add Override to add a filter preset override when filtering or browsing this term.', 'prdctfltr' ); ?></p>
				<?php

					$curr_overrides = get_option( 'wc_settings_prdctfltr_more_overrides', false );

					if ( $curr_overrides === false ) {
						$curr_overrides = array( 'product_cat', 'product_tag' );
						if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'yes' ) {
							$curr_overrides[] = 'characteristics';
						}
					}

					foreach ( $curr_overrides as $n ) {
						$get_dropdown = wp_dropdown_categories( array( 'hide_empty' => 0, 'echo' => 0, 'hierarchical' => ( is_taxonomy_hierarchical( $n ) ? 1 : 0 ), 'class' => 'prdctfltr_or_select', 'depth' => 0, 'taxonomy' => $n, 'hide_if_empty' => true, 'value_field' => 'slug', ) );
						if ( empty( $get_dropdown ) ) {
							continue;
						}
				?>
						<h3>
						<?php
							$curr_tax = get_taxonomy( $n );
							echo __( 'Product', 'prdctfltr' ) . ' ' . $curr_tax->labels->name . ' ' . __( 'Overrides', 'prdctfltr' );
						?>
						</h3>
						<p class="<?php echo $n; ?>">
						<?php
							if ( isset( $curr_or_settings[$n] ) ) {
								foreach ( $curr_or_settings[$n] as $k => $v ) {
							?>
							<span class="prdctfltr_override"><input type="checkbox" class="pf_override_checkbox" /> <?php echo __( 'Term slug', 'prdctfltr' ) . ' : <span class="slug">' . $k . '</span>'; ?> <?php echo __( 'Filter Preset', 'prdctfltr' ) . ' : <span class="preset">' . $v; ?></span> <a href="#" class="button prdctfltr_or_remove"><?php _e( 'Remove Override', 'prdctfltr' ); ?></a><span class="clearfix"></span></span>
							<?php
								}
							}
						?>
							<span class="prdctfltr_override_controls">
								<a href="#" class="button prdctfltr_or_remove_selected"><?php _e( 'Remove Selected Overrides', 'prdctfltr' ); ?></a> <a href="#" class="button prdctfltr_or_remove_all"><?php _e( 'Remove All Overrides', 'prdctfltr' ); ?></a>
							</span>
						<?php
							echo $get_dropdown;
						?>
							<select class="prdctfltr_filter_presets">
								<option value="default"><?php _e( 'Default', 'wcwar' ); ?></option>
								<?php
									if ( !empty( $curr_presets) ) {
										foreach ( $curr_presets as $k => $v ) {
									?>
											<option value="<?php echo $k; ?>"><?php echo $k; ?></option>
									<?php
										}
									}
								?>
							</select>
							<a href="#" class="button-primary prdctfltr_or_add"><?php _e( 'Add Override', 'prdctfltr' ); ?></a>
						</p>
				<?php
					}
				}
			}

			return apply_filters( 'wc_settings_products_filter_settings', $settings );
		}

		public static function prdctfltr_admin_save() {

			$curr_name = $_POST['curr_name'];

			$curr_data = array();
			$curr_data[$curr_name] = $_POST['curr_settings'];

			$curr_presets = get_option( 'prdctfltr_templates', array() );

			if ( isset( $curr_presets) && is_array( $curr_presets) ) {
				if ( array_key_exists( $curr_name, $curr_presets) ) {
					unset( $curr_presets[$curr_name] );
				}

				$curr_presets = $curr_presets + $curr_data;

				update_option( 'prdctfltr_templates', $curr_presets);

				delete_transient( 'prdctfltr_' . $curr_name );

				die( $curr_presets);
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_admin_load() {

			$curr_name = $_POST['curr_name'];

			$curr_presets = get_option( 'prdctfltr_templates', array() );
			if ( isset( $curr_presets ) && !empty( $curr_presets ) && is_array( $curr_presets ) ) {
				if ( array_key_exists( $curr_name, $curr_presets ) ) {
					die( stripslashes( $curr_presets[$curr_name] ) );
					exit;
				}
				die( '1' );
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_admin_delete() {

			$curr_name = $_POST['curr_name'];

			$curr_presets = get_option( 'prdctfltr_templates', array() );
			if ( isset( $curr_presets ) && !empty( $curr_presets ) && is_array( $curr_presets ) ) {
				if ( array_key_exists( $curr_name, $curr_presets ) ) {
					unset( $curr_presets[$curr_name] );
					update_option( 'prdctfltr_templates', $curr_presets );
				}

				delete_transient( 'prdctfltr_' . $curr_name );

				die( '1' );
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_or_add() {
			$curr_tax = $_POST['curr_tax'];
			$curr_term = $_POST['curr_term'];
			$curr_override = $_POST['curr_override'];

			$curr_overrides = get_option( 'prdctfltr_overrides', array() );

			$curr_data = array(
				$curr_tax => array( $curr_term => $curr_override )
			);

			if ( isset( $curr_overrides) && is_array( $curr_overrides) ) {
				if ( isset( $curr_overrides[$curr_tax] ) && isset( $curr_overrides[$curr_tax][$curr_term] )) {
					unset( $curr_overrides[$curr_tax][$curr_term] );
				}
				$curr_overrides = array_merge_recursive( $curr_overrides, $curr_data);
				update_option( 'prdctfltr_overrides', $curr_overrides);
				die( '1' );
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_or_remove() {
			$curr_tax = $_POST['curr_tax'];
			$curr_term = $_POST['curr_term'];
			$curr_overrides = get_option( 'prdctfltr_overrides', array() );

			if ( isset( $curr_overrides ) && is_array( $curr_overrides ) ) {
				if ( isset( $curr_overrides[$curr_tax] ) && isset( $curr_overrides[$curr_tax][$curr_term] ) ) {
					unset( $curr_overrides[$curr_tax][$curr_term] );
					update_option( 'prdctfltr_overrides', $curr_overrides );
					die( '1' );
					exit;
				}
			}

			die();
			exit;

		}

		public static function prdctfltr_m_fields() {

			$pf_id = ( isset( $_POST['pf_id'] ) ? $_POST['pf_id'] : 0 );

			ob_start();
		?>

			<h3><?php _e( 'Meta Fitler', 'prdctfltr' ); ?></h3>
			<p><?php echo __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_title_%1$s">%2$s</label>', $pf_id, __( 'Filter Title', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfm_title[%1$s]" id="pfm_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfm_title'] ) ? $_POST['pfm_title'] : '' );
							?>
							<span class="description"><?php echo __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_description_%1$s">%2$s</label>', $pf_id, __( 'Filter Description', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php _e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
							<?php
								printf( '<textarea name="pfm_description[%1$s]" id="pfm_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfm_description'] ) ? stripslashes( $_POST['pfm_description'] ) : '' ) );
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_key_%1$s">%2$s</label>', $pf_id, __( 'Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfm_key[%1$s]" id="pfm_key_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfm_key'] ) ? $_POST['pfm_key'] : '' );
							?>
							<span class="description"><?php echo __( 'Meta key.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_compare_%1$s">%2$s</label>', $pf_id, __( 'Compare', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
	
							$meta_compares = array(
								array(
									'value' => '=',
									'label' => '='
								),
								array(
									'value' => '!=',
									'label' => '!='
								),
								array(
									'value' => '>',
									'label' => '>'
								),
								array(
									'value' => '<',
									'label' => '<'
								),
								array(
									'value' => '>=',
									'label' => '>='
								),
								array(
									'value' => '<=',
									'label' => '<='
								),
								array(
									'value' => 'LIKE',
									'label' => 'LIKE'
								),
								array(
									'value' => 'NOT LIKE',
									'label' => 'NOT LIKE'
								),
								array(
									'value' => 'IN',
									'label' => 'IN'
								),
								array(
									'value' => 'NOT IN',
									'label' => 'NOT IN'
								),
								array(
									'value' => 'EXISTS',
									'label' => 'EXISTS'
								),
								array(
									'value' => 'NOT EXISTS',
									'label' => 'NOT EXISTS'
								),
								array(
									'value' => 'BETWEEN',
									'label' => 'BETWEEN'
								),
								array(
									'value' => 'NOT BETWEEN',
									'label' => 'NOT_BETWEEN'
								),
							);
							foreach ( $meta_compares as $k => $v ) {
								$selected = ( isset( $_POST['pfm_compare'] ) && $_POST['pfm_compare'] == $v['value'] ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $v['value'], $v['label'], $selected );
							}

							printf( '<select name="pfm_compare[%2$s]" id="pfm_compare_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
						?>
							<span class="description"><?php _e( 'Meta compare.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_type_%1$s">%2$s</label>', $pf_id, __( 'Type', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
	
							$meta_types = array(
								array(
									'value' => 'NUMERIC',
									'label' => 'NUMERIC'
								),
								array(
									'value' => 'BINARY',
									'label' => 'BINARY'
								),
								array(
									'value' => 'CHAR',
									'label' => 'CHAR'
								),
								array(
									'value' => 'DATE',
									'label' => 'DATE'
								),
								array(
									'value' => 'DATETIME',
									'label' => 'DATETIME'
								),
								array(
									'value' => 'DECIMAL',
									'label' => 'DECIMAL'
								),
								array(
									'value' => 'SIGNED',
									'label' => 'SIGNED'
								),
								array(
									'value' => 'TIME',
									'label' => 'TIME'
								),
								array(
									'value' => 'UNSIGNED',
									'label' => 'UNSIGNED'
								)
							);
							foreach ( $meta_types as $k => $v ) {
								$selected = ( isset( $_POST['pfm_type'] ) && $_POST['pfm_type'] == $v['value'] ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $v['value'], $v['label'], $selected );
							}

							printf( '<select name="pfm_type[%2$s]" id="pfm_type_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
						?>
							<span class="description"><?php _e( 'Meta type.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_limit_%1$s">%2$s</label>', $pf_id, __( 'Limit Terms', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-number">
							<?php
								printf( '<input name="pfm_limit[%1$s]" id="pfm_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $pf_id, isset( $_POST['pfm_limit'] ) ? $_POST['pfm_limit'] : '' ); ?>
							<span class="description"><?php _e( 'Limit number of terms to display in filter.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Multi Select', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Multi Select', 'prdctfltr' );
								?>
								</legend>
								<label for="pfm_multiselect_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfm_multiselect[%1$s]" id="pfm_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfm_multiselect'] ) && $_POST['pfm_multiselect'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to enable multi term selection.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_relation_%1$s">%2$s</label>', $pf_id, __( 'Multi Select Terms Relation', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
									'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset( $_POST['pfm_relation'] ) && $_POST['pfm_relation'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfm_relation[%2$s]" id="pfm_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select term relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Hide None', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Hide None', 'prdctfltr' );
								?>
								</legend>
								<label for="pfm_none_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfm_none[%1$s]" id="pfm_none_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfm_none'] ) && $_POST['pfm_none'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to hide none in the current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_term_customization_%1$s">%2$s</label>', $pf_id, __( 'Style Customization Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfm_term_customization[%1$s]" id="pfm_term_customization_%1$s" type="text" value="%2$s" class="pf_term_customization" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfm_term_customization'] ) ? $_POST['pfm_term_customization'] : '' );
							?>
							<span class="description"><?php _e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_filter_customization_%1$s">%2$s</label>', $pf_id, __( 'Terms Customization Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfm_filter_customization[%1$s]" id="pfm_filter_customization_%1$s" type="text" value="%2$s" class="pf_filter_customization" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfm_filter_customization'] ) ? $_POST['pfm_filter_customization'] : '' );
							?>
							<span class="description"><?php _e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
						</td>
					</tr>

				</tbody>
			</table>
		<?php

			$html = $pf_id . '%SPLIT%' . ob_get_clean();

			die( $html);
			exit;

		}

		public static function prdctfltr_c_fields() {

			$pf_id = ( isset( $_POST['pf_id'] ) ? $_POST['pf_id'] : 0 );

			ob_start();
		?>

			<h3><?php _e( 'Advanced Fitler', 'prdctfltr' ); ?></h3>
			<p><?php echo __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_title_%1$s">%2$s</label>', $pf_id, __( 'Filter Title', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfa_title[%1$s]" id="pfa_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfa_title'] ) ? $_POST['pfa_title'] : '' );
							?>
							<span class="description"><?php echo __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_description_%1$s">%2$s</label>', $pf_id, __( 'Filter Description', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php _e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
							<?php
								printf( '<textarea name="pfa_description[%1$s]" id="pfa_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfa_description'] ) ? stripslashes( $_POST['pfa_description'] ) : '' ) );
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							$taxonomies = get_object_taxonomies( 'product', 'object' );
							printf( '<label for="pfa_taxonomy_%1$s">%2$s</label>', $pf_id, __( 'Select Taxonomy', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								printf( '<select id="pfa_taxonomy_%1$s" name="pfa_taxonomy[%1$s]" class="prdctfltr_adv_select" style="width:300px;margin-right:12px;">', $pf_id) ;
								foreach ( $taxonomies as $k => $v ) {
									if ( in_array( $k, array( 'product_type' ) ) ) {
										continue;
									}
									if ( !isset( $first_tax ) ) {
										$first_tax = $k;
									}
									echo '<option value="' . $k . '"' . ( isset( $_POST['pfa_taxonomy'] ) && $_POST['pfa_taxonomy'] == $k ? ' selected="selected"' : '' ) .'>' . ( substr( $v->name, 0, 3 ) == 'pa_' ? wc_attribute_label( $v->name ) : $v->label ) . '</option>';
								}
								echo '</select>';
							?>
							<span class="description"><?php _e( 'Select filter product taxonomy.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_include_%1$s">%2$s</label>', $pf_id, __( 'Select Terms', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-multiselect">
						<?php
							$tax = isset( $_POST['pfa_taxonomy'] ) && taxonomy_exists( $_POST['pfa_taxonomy'] ) ? $_POST['pfa_taxonomy'] : $first_tax;
							if ( !empty( $tax ) ) {
								$args = array(
									'hide_empty' => 0,
									'echo' => 0,
									'hierarchical' => ( is_taxonomy_hierarchical( $tax ) ? 1 : 0 ),
									'name' => 'pfa_include[' . $pf_id . '][]',
									'id' => 'pfa_include_' . $pf_id,
									'class' => '',
									'depth' => 0,
									'taxonomy' => $tax,
									'hide_if_empty' => true,
									'value_field' => 'slug'
								);

								$option_value =  isset( $_POST['pfa_include'] ) ? $_POST['pfa_include'] : array();
								$dropdown = wp_dropdown_categories( $args );
								$dropdown = str_replace( 'id=', ' style="width:300px;margin-right:12px;" multiple="multiple" id=', $dropdown );
								if ( is_array( $option_value ) ) {
									foreach ( $option_value as $key => $post_term ) { 
										$dropdown = str_replace(' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown); 
									}
								}
								else {
									$dropdown = str_replace( ' value="' . $option_value . '"', ' value="' . $option_value . '" selected="selected"', $dropdown );
								}
								echo $dropdown;
							}
							else {
								printf( '<select name="pfa_include[%1$s][]" id="pfa_include_%1$s" multiple="multiple" style="width:300px;margin-right:12px;"></select>', $pf_id );
							}
						?>
							<span class="description"><?php echo __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_style_%1$s">%2$s</label>', $pf_id, __( 'Appearance', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'pf_attr_text' => __( 'Text', 'prdctfltr' ),
									'pf_attr_imgtext' => __( 'Thumbnails with text', 'prdctfltr' ),
									'pf_attr_img' => __( 'Thumbnails only', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_style'] ) && $_POST['pfa_style'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_style[%2$s]" id="pfa_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select style preset to use with the current taxonomy (works only with product attributes).', 'prdctfltr' ); ?> DEPRECATED DO NOT USE! USE PAINT BUCKET TOOL AND SET APPEARANCE TO TEXT!<em class="pf_deprecated"></em></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_orderby_%1$s">%2$s</label>', $pf_id, __( 'Terms Order By', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$orderby_params = array(
									'' => __( 'None', 'prdctfltr' ),
									'id' => __( 'ID', 'prdctfltr' ),
									'name' => __( 'Name', 'prdctfltr' ),
									'number' => __( 'Number', 'prdctfltr' ),
									'slug' => __( 'Slug', 'prdctfltr' ),
									'count' => __( 'Count', 'prdctfltr' )
								);

								foreach ( $orderby_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_orderby'] ) && $_POST['pfa_orderby'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_orderby[%2$s]" id="pfa_orderby_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select terms ordering.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_order_%1$s">%2$s</label>', $pf_id, __( 'Terms Order', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$order_params = array(
									'ASC' => __( 'ASC', 'prdctfltr' ),
									'DESC' => __( 'DESC', 'prdctfltr' )
								);

								foreach ( $order_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_order'] ) && $_POST['pfa_order'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_order[%2$s]" id="pfa_order_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_limit_%1$s">%2$s</label>', $pf_id, __( 'Limit Terms', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-number">
							<?php
								printf( '<input name="pfa_limit[%1$s]" id="pfa_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $pf_id, isset( $_POST['pfa_limit'] ) ? $_POST['pfa_limit'] : '' ); ?>
							<span class="description"><?php _e( 'Limit number of terms to display in filter.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_hierarchy_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_hierarchy[%1$s]" id="pfa_hierarchy_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_hierarchy'] ) && $_POST['pfa_hierarchy'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to enable hierarchy on current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Taxonomy Hierarchy Mode', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Taxonomy Hierarchy Mode', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_hierarchy_mode_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_hierarchy_mode[%1$s]" id="pfa_hierarchy_mode_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_hierarchy_mode'] ) && $_POST['pfa_hierarchy_mode'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( ' Check this option to expand parent terms on load.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_mode_%1$s">%2$s</label>', $pf_id, __( 'Taxonomy Hierarchy Filtering Mode', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'showall' => __( 'Show all', 'prdctfltr' ),
									'subonly' => __( 'Keep only child terms', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_mode'] ) && $_POST['pfa_mode'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_mode[%2$s]" id="pfa_mode_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select terms relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Multi Select', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Multi Select', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_multiselect_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_multiselect[%1$s]" id="pfa_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_multiselect'] ) && $_POST['pfa_multiselect'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to enable multi term selection.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_relation_%1$s">%2$s</label>', $pf_id, __( 'Multi Select Terms Relation', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
									'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_relation'] ) && $_POST['pfa_relation'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_relation[%2$s]" id="pfa_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select term relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Adoptive Filtering', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Adoptive Filtering', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_adoptive_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_adoptive[%1$s]" id="pfa_adoptive_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_adoptive'] ) && $_POST['pfa_adoptive'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Hide None', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Hide None', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_none_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_none[%1$s]" id="pfa_none_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_none'] ) && $_POST['pfa_none'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to hide none in the current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_term_customization_%1$s">%2$s</label>', $pf_id, __( 'Style Customization Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfa_term_customization[%1$s]" id="pfa_term_customization_%1$s" type="text" value="%2$s" class="pf_term_customization" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfa_term_customization'] ) ? $_POST['pfa_term_customization'] : '' );
							?>
							<span class="description"><?php _e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		<?php

			$html = $pf_id . '%SPLIT%' . ob_get_clean();

			die( $html);
			exit;

		}

		public static function prdctfltr_c_terms() {

			$tax = ( isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : '' );

			if ( $tax == '' ) {
				die();
				exit;
			}


			$args = array(
				'hide_empty' => 0,
				'echo' => 0,
				'hierarchical' => ( is_taxonomy_hierarchical( $tax ) ? 1 : 0 ),
				'name' => 'pfa_include_[%%][]',
				'id' => 'pfa_include_%%',
				'class' => '',
				'depth' => 0,
				'taxonomy' => $tax,
				'value_field' => 'slug'
			);

			$dropdown = wp_dropdown_categories( $args );
			$dropdown = str_replace( 'id=', ' style="width:300px;margin-right:12px;" multiple="multiple" id=', $dropdown );

			die( $dropdown );
			exit;

		}

		public static function prdctfltr_r_fields() {

			$pf_id = ( isset( $_POST['pf_id'] ) ? $_POST['pf_id'] : 0 );

			ob_start();
		?>

			<h3><?php _e( 'Range Fitler', 'prdctfltr' ); ?></h3>
			<p><?php echo __( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . __( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
			<table cass="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_title_%1$s">%2$s</label>', $pf_id, __( 'Filter Title', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfr_title[%1$s]" id="pfr_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfr_title'] ) ? $_POST['pfr_title'] : '' );
							?>
							<span class="description"><?php echo __( 'Override filter title.', 'prdctfltr' ) . ' ' . __( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_description_%1$s">%2$s</label>', $pf_id, __( 'Filter Description', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php _e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
							<?php
								printf( '<textarea name="pfr_description[%1$s]" id="pfr_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfr_description'] ) ? stripslashes( $_POST['pfr_description'] ) : '' ) );
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_taxonomy_%1$s">%2$s</label>', $pf_id, __( 'Select Range', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$taxonomies = get_object_taxonomies( 'product', 'object' );
								printf( '<select name="pfr_taxonomy[%1$s]" id="pfr_taxonomy_%1$s" class="prdctfltr_rng_select"  style="width:300px;margin-right:12px;">', $pf_id );
								echo '<option value="price"' . ( !isset( $_POST['pfr_taxonomy'] ) || $_POST['pfr_taxonomy'] == 'price' ? ' selected="selected"' : '' ) . '>' . __( 'Price range', 'prdctfltr' ) . '</option>';
								foreach ( $taxonomies as $k => $v ) {
									if ( substr( $k, 0, 3 ) == 'pa_' && $k !== 'product_type' ) {
										$curr_label = wc_attribute_label( $v->name );
										$curr_value = $v->name;
									}
									else {
										$curr_label = $v->label;
										$curr_value = $k;
									}
									echo '<option value="' . $curr_value . '"' . ( isset( $_POST['pfr_taxonomy'] ) && $_POST['pfr_taxonomy'] == $curr_value ? ' selected="selected"' : '' ) .'>' . $curr_label . '</option>';
								}
								echo '</select>';
							?>
							<span class="description"><?php _e( 'Enter title for the current range filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_include_%1$s">%2$s</label>', $pf_id, __( 'Select Terms', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-multiselect">
						<?php
							if ( isset( $_POST['pfr_taxonomy'] ) && $_POST['pfr_taxonomy'] !== 'price' ) {
								$tax = isset( $_POST['pfr_taxonomy'] ) ? $_POST['pfr_taxonomy'] : '';
								$args = array(
									'hide_empty' => 0,
									'echo' => 0,
									'hierarchical' => ( is_taxonomy_hierarchical( $tax ) ? 1 : 0 ),
									'name' => 'pfr_include_' . $pf_id . '[]',
									'id' => 'pfr_include_' . $pf_id,
									'class' => '',
									'depth' => 0,
									'taxonomy' => $tax,
									'hide_if_empty' => true,
									'value_field' => 'slug'
								);

								$option_value = isset( $_POST['pfr_include'] ) ? $_POST['pfr_include'] : array();
								$dropdown = wp_dropdown_categories( $args );
								$dropdown = str_replace( 'id=', ' style="width:300px;margin-right:12px;" multiple="multiple" id=', $dropdown );
								if ( is_array( $option_value ) ) {
									foreach ( $option_value as $key => $post_term ) { 
										$dropdown = str_replace(' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown); 
									}
								}
								else {
									$dropdown = str_replace( ' value="' . $option_value . '"', ' value="' . $option_value . '" selected="selected"', $dropdown );
								}
								echo $dropdown;
							}
							else {

								printf( '<select name="pfr_include[%1$s][]" id="pfr_include_%1$s" multiple="multiple" disabled style="width:300px;margin-right:12px;"></select></label>', $pf_id );
								$add_disabled = ' disabled';

							}
						?>
							<span class="description"><?php echo __( 'Select terms to include.', 'prdctfltr' ) . ' ' . __( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_orderby_%1$s">%2$s</label>', $pf_id, __( 'Terms Order By', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$orderby_params = array(
								'' => __( 'None', 'prdctfltr' ),
								'id' => __( 'ID', 'prdctfltr' ),
								'name' => __( 'Name', 'prdctfltr' ),
								'number' => __( 'Number', 'prdctfltr' ),
								'slug' => __( 'Slug', 'prdctfltr' ),
								'count' => __( 'Count', 'prdctfltr' )
							);
							foreach ( $orderby_params as $k => $v ) {
								$selected = ( isset( $_POST['pfr_orderby'] ) && $_POST['pfr_orderby'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}
							printf( '<select name="pfr_orderby[%2$s]" id="pfr_orderby_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select></label>', $curr_options, $pf_id, $add_disabled );
						?>
							<span class="description"><?php _e( 'Select terms ordering.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_order_%1$s">%2$s</label>', $pf_id, __( 'Terms Order', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$order_params = array(
								'ASC' => __( 'ASC', 'prdctfltr' ),
								'DESC' => __( 'DESC', 'prdctfltr' )
							);
							foreach ( $order_params as $k => $v ) {
								$selected = ( isset( $_POST['pfr_order'] ) && $_POST['pfr_order'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}

							printf( '<select name="pfr_order[%2$s]" id="pfr_order_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id, $add_disabled );
						?>
							<span class="description"><?php _e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_style_%1$s">%2$s</label>', $pf_id, __( 'Select Style', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$catalog_style = array(
								'flat' => __( 'Flat', 'prdctfltr' ),
								'modern' => __( 'Modern', 'prdctfltr' ),
								'html5' => __( 'HTML5', 'prdctfltr' ),
								'white' => __( 'White', 'prdctfltr' )
							);
							foreach ( $catalog_style as $k => $v ) {
								$selected = ( isset( $_POST['pfr_style'] ) && $_POST['pfr_style'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}

							printf( '<select name="pfr_style[%2$s]" id="pfr_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
						?>
							<span class="description"><?php _e( 'Select current range style.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Grid', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Grid', 'prdctfltr' );
								?>
								</legend>
								<label for="pfr_grid_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfr_grid[%2$s]" id="pfr_grid_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $_POST['pfr_grid'] ) && $_POST['pfr_grid'] == 'yes' ? ' checked="checked"' : '' ), $pf_id );
									_e( 'Check this option to use grid in current range.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Adoptive Filtering', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Adoptive Filtering', 'prdctfltr' );
								?>
								</legend>
								<label for="pfr_adoptive_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfr_adoptive[%2$s]" id="pfr_adoptive_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $_POST['pfr_adoptive'] ) && $_POST['pfr_adoptive'] == 'yes' ? ' checked="checked"' : '' ), $pf_id );
									_e( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_custom_%1$s">%2$s</label>', $pf_id, __( 'Custom Settings', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php _e( 'Enter custom settings for the range filter. Visit this page for more information ', 'prdctfltr' ); ?> <a href="http://ionden.com/a/plugins/ion.rangeSlider/demo.html" target="_blank">http://ionden.com/a/plugins/ion.rangeSlider/demo.html</a></p>
							<?php
								printf( '<textarea name="pfr_custom[%1$s]" id="pfr_custom_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfr_custom'] ) ? stripslashes( $_POST['pfr_custom'] ) : '' ) );
							?>
						</td>
					</tr>

				</tbody>
			</table>
		<?php

			$html = $pf_id . '%SPLIT%' . ob_get_clean();

			die( $html);
			exit;

		}

		public static function prdctfltr_r_terms() {

			$tax = ( isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : '' );

			if ( $tax == '' ) {
				die();
				exit;
			}

			$args = array(
				'hide_empty' => 0,
				'echo' => 0,
				'hierarchical' => ( is_taxonomy_hierarchical( $tax ) ? 1 : 0 ),
				'name' => 'pfr_include_[%%][]',
				'id' => 'pfr_include_%%',
				'class' => '',
				'depth' => 0,
				'taxonomy' => $tax,
				'hide_if_empty' => true,
				'value_field' => 'slug'
			);

			$dropdown = wp_dropdown_categories( $args );
			$dropdown = str_replace( 'id=', ' style="width:300px;margin-right:12px;" multiple="multiple" id=', $dropdown );

			die( $dropdown );
			exit;

		}

		public static function set_terms() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$addkey = isset( $_POST['addkey'] ) ? $_POST['addkey'] : '';

			if ( $filter == '' ) {
				die();
				exit;
			}

			$language = self::prdctfltr_wpml_language();

			if ( $key !== '' ) {
				if ( isset( $language ) && $language !== false ) {
					$get_customization = get_option( $key . '_' . $lanugage, '' );
				}
				else {
					$get_customization = get_option( $key, '' );
				}

				if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
					$customization = $get_customization;
				}

			}

			if ( !isset( $customization ) ) {
				$customization = array(
					'style' => 'text',
					'settings' => array()
				);
				$key = 'wc_settings_prdctfltr_term_customization_' . uniqid();
			}

			if ( $filter == 'advanced' ) {
				$advanced = isset( $_POST['advanced'] ) ? $_POST['advanced'] : '';

				if ( $filter == '' ) {
					die();
					exit;
				}

			}

			$curr_filter = $filter;

			switch ( $filter ) {

				case 'meta' :
				case 'price' :
				case 'per_page' :
					$baked_filters = self::get_terms( $filter, $customization, $addkey );
				break;
				case 'vendor' :
				case 'sort' :
				case 'instock' :
					$baked_filters = self::get_terms( $filter, $customization, $addkey );
				break;

				default :

					if ( $filter == 'cat' ) {
						$curr_filter = 'product_cat';
					}
					else if ( $filter == 'tag' ) {
						$curr_filter = 'product_tag';
					}
					else if ( $filter == 'char' ) {
						$curr_filter = 'characteristics';
					}
					else if ( $filter == 'advanced' ) {
						$curr_filter = $advanced;
					}
					else if ( substr( $filter, 0, 3) == 'pa_' ) {
						$curr_filter = $filter;
					}
					else {
						$curr_filter = '';
					}

					if ( $curr_filter == '' ) {
						die();
						exit;
					}

					$baked_filters = self::get_terms( $curr_filter, $customization, $addkey );

				break;

			}

			if ( isset( $baked_filters ) ) {

				ob_start();
?>
				<div class="prdctfltr_quickview_terms" data-key="<?php echo $key; ?>"<?php echo $addkey !== '' ? ' data-addkey="' . $addkey . '"' : ''; ?>>
					<span class="prdctfltr_quickview_close"><span class="prdctfltr_quickview_close_button"><?php _e( 'Click to discard any settings!', 'prdctfltr' ); ?></span></span>
					<div class="prdctfltr_quickview_terms_inner">
						<div class="prdctfltr_quickview_terms_settings">
							<span class="prdctfltr_set_terms" data-taxonomy="<?php echo $curr_filter; ?>"><?php _e( 'Taxonomy', 'prdctfltr' ); ?>: <code><?php echo $curr_filter; ?></code></span>
							<a href="#" class="button-primary prdctfltr_set_terms_save"><?php _e( 'Save Customization', 'prdctfltr' ); ?></a>
<?php

							$select_style = '<label class="pf_wpml"><span>' . __( 'Select Style', 'prdctfltr' ) . '</span> <select class="prdctfltr_set_terms_attr_select" name="style">';

							$styles = array(
								'text' => __( 'Text', 'prdctfltr' ),
								'color' => __( 'Color', 'prdctfltr' ),
								'image' => __( 'Thumbnail', 'prdctfltr' ),
								'image-text' => __( 'Thumbnail and Text', 'prdctfltr' ),
								'html' => __( 'HTML', 'prdctfltr' ),
								'select' => __( 'Select Box', 'prdctfltr' )
							);

							foreach ( $styles as $k => $v ) {
								$selected = $customization['style'] == $k ? ' selected="selected"' : '';
								$select_style .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
							}

							$select_style .= '</select></label>';

							echo $select_style;

							if ( function_exists( 'icl_get_languages' ) ) {
								$languages = icl_get_languages();

								$select_languages = '<label><span>' . __( 'Select Language', 'prdctfltr' ) . '</span> <select class="prdctfltr_set_terms_attr_select" name="lang">';

								foreach ( $languages as $k => $v ) {
									$selected = $language == $k ? ' selected="selected"' : '';
									$select_languages .= '<option value="' . $k . '" ' . $selected . '>' . $v['native_name'] . '</option>';

								}

								$select_languages .= '</select></label>';

								echo $select_languages;


							}
?>
						</div>
						<div class="prdctfltr_quickview_terms_manager">
							<?php echo $baked_filters; ?>
						</div>
					</div>
				</div>
<?php
				$html = ob_get_clean();
			}

			if ( isset( $html ) ) {
				die( $html );
				exit;
			}

			die();
			exit;

		}

		public static function set_terms_new() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$style = isset( $_POST['style'] ) ? $_POST['style'] : '';
			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$addkey = isset( $_POST['addkey'] ) ? $_POST['addkey'] : '';

			$language = self::prdctfltr_wpml_language();

			if ( $filter == '' || $style == '' ) {
				die();
				exit;
			}

			if ( $key !== '' ) {
				if ( $language !== false ) {
					$get_customization = get_option( $key . '_' . $language, '' );
				}
				else {
					$get_customization = get_option( $key, '' );
				}

				if ( $get_customization !== '' && isset( $get_customization['style'] ) && $get_customization['style'] = $style ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array(
					'style' => $style,
					'settings' => array()
				);
			}

			$html = self::get_terms( $filter, $customization, $addkey );

			die( $html );
			exit;

		}

		public static function get_terms( $filter, $customization, $addkey ) {

			if ( $filter == '' ) {
				return '';
			}

			$catalog_attrs = array();
			$curr_style = $customization['style'];
			$settings = $customization['settings'];

			if ( taxonomy_exists( $filter ) && !in_array( $filter, array( 'price', 'per_page' ) ) ) {
				$catalog_attrs = get_terms( $filter, array( 'hide_empty' => 0 ) );
			}
			else {
				switch ( $filter ) {
					case 'instock' :
						$curr_set = apply_filters( 'prdctfltr_catalog_instock', array(
							'both'    => __( 'All Products', 'prdctfltr' ),
							'in'      => __( 'In Stock', 'prdctfltr' ),
							'out'     => __( 'Out Of Stock', 'prdctfltr' )
						) );
						foreach( $curr_set as $k => $v ) {
							$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
						}
					break;
					case 'sort' :
						$curr_set = apply_filters( 'prdctfltr_catalog_orderby', array(
							''              => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) ),
							'menu_order'    => __( 'Default', 'prdctfltr' ),
							'comment_count' => __( 'Review Count', 'prdctfltr' ),
							'popularity'    => __( 'Popularity', 'prdctfltr' ),
							'rating'        => __( 'Average rating', 'prdctfltr' ),
							'date'          => __( 'Newness', 'prdctfltr' ),
							'price'         => __( 'Price: low to high', 'prdctfltr' ),
							'price-desc'    => __( 'Price: high to low', 'prdctfltr' ),
							'rand'          => __( 'Random Products', 'prdctfltr' ),
							'title'         => __( 'Product Name', 'prdctfltr' )
						) );
						foreach( $curr_set as $k => $v ) {
							$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
						}
					break;
					case 'price' :
						$filter_customization = self::get_filter_customization( 'price', $addkey );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {
							foreach( $filter_customization['settings'] as $k => $v ) {
								$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
							}
						}
						else {

							$curr_price_set = get_option( 'wc_settings_prdctfltr_price_range', 100 );
							$curr_price_add = get_option( 'wc_settings_prdctfltr_price_range_add', 100 );
							$curr_price_limit = get_option( 'wc_settings_prdctfltr_price_range_limit', 6 );

							if ( get_option( 'wc_settings_prdctfltr_price_none', 'no' ) == 'no' ) {
								$catalog_ready_price = array(
									'-' => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) )
								);
							}

							for ( $i = 0; $i < $curr_price_limit; $i++) {

								if ( $i == 0 ) {
									$min_price = 0;
									$max_price = $curr_price_set;
								}
								else {
									$min_price = $curr_price_set+( $i-1)*$curr_price_add;
									$max_price = $curr_price_set+$i*$curr_price_add;
								}

								$slug = $min_price . '-' . ( ( $i+1) == $curr_price_limit ? '' : $max_price );
								$name = wc_price( $min_price ) . ( $i+1 == $curr_price_limit ? '+' : ' - ' . wc_price( $max_price ) );

								$catalog_attrs[] = (object) array( 'slug' => $slug, 'name' => $name );

							}
						}
					break;
					case 'per_page' :
						$filter_customization = self::get_filter_customization( 'per_page', $addkey );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {
							foreach( $filter_customization['settings'] as $v ) {
								$catalog_attrs[] = (object) array( 'slug' => $v['value'], 'name' => $v['text'] );
							}
						}
						else {
							$curr_perpage_set = get_option( 'wc_settings_prdctfltr_perpage_range', 20 );
							$curr_perpage_limit = get_option( 'wc_settings_prdctfltr_perpage_range_limit', 5 );

							$curr_perpage = array();

							for ( $i = 1; $i <= $curr_perpage_limit; $i++) {

								$slug = $curr_perpage_set*$i;
								$name = $curr_perpage_set*$i . ' ' . ( get_option( 'wc_settings_prdctfltr_perpage_label', '' ) == '' ? __( 'Products', 'prdctfltr' ) : get_option( 'wc_settings_prdctfltr_perpage_label', '' ) );

								$catalog_attrs[] = (object) array( 'slug' => $slug, 'name' => $name );

							}
						}
					break;
					case 'meta' :
						$filter_customization = self::get_filter_customization( 'meta', $addkey );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {
							foreach( $filter_customization['settings'] as $v ) {
								$catalog_attrs[] = (object) array( 'slug' => $v['value'], 'name' => $v['text'] );
							}
						}
						else {
							$catalog_attrs[] = (object) array( 'slug' => '', 'name' => '' );
						}

					break;
					case 'vendor' :
						$vendors = get_users( 'orderby=nicename' );

						foreach ( $vendors as $vendor ) {
							$catalog_attrs[] = (object) array( 'slug' => $vendor->ID, 'name' => $vendor->display_name );
						}
					break;
					default :
						$catalog_attrs = array();
					break;
				}
			}

			if ( !empty( $catalog_attrs ) ) {

				ob_start();

				switch ( $curr_style ) {

					case 'text' :

						?>
							<div class="prdctfltr_st_term_style">
								<span class="prdctfltr_st_option">
									<em><?php _e( 'Type', 'prdctfltr' ); ?></em>
									<select name="type">
								<?php
									$styles = array(
										'border' => __( 'Border', 'prdctfltr' ),
										'background' => __( 'Background', 'prdctfltr' ),
										'round' => __( 'Round', 'prdctfltr' )
									);
									$selected = isset( $settings['type'] ) ? $settings['type'] : 'border';

									$c=0;
									foreach ( $styles as $k => $v ) {
										
								?>
										<option value="<?php echo $k; ?>"<?php echo $selected == $k ? ' selected="selected"' : ''; ?>><?php echo $v; ?></option>
								<?php
										$c++;
									}
								?>
									</select>
								</span>
								<span class="prdctfltr_st_option">
									<em><?php _e( 'Normal', 'prdctfltr' ); ?></em> <input class="prdctfltr_st_color" type="text" name="normal" value="<?php echo isset( $settings['normal'] ) ? $settings['normal'] : '#bbbbbb'; ?>" />
								</span>
								<span class="prdctfltr_st_option">
									<em><?php _e( 'Active', 'prdctfltr' ); ?></em> <input class="prdctfltr_st_color" type="text" name="active" value="<?php echo isset( $settings['active'] ) ? $settings['active'] : '#333333'; ?>" />
								</span>
								<span class="prdctfltr_st_option">
									<em><?php _e( 'Disabled', 'prdctfltr' ); ?></em> <input class="prdctfltr_st_color" type="text" name="disabled" value="<?php echo isset( $settings['disabled'] ) ? $settings['disabled'] : '#eeeeee'; ?>"/>
								</span>

							</div>
						<?php

							foreach ( $catalog_attrs as $term ) {

							?>
								<div class="prdctfltr_st_term prdctfltr_style_text" data-term="<?php echo $term->slug; ?>">
									<span class="prdctfltr_st_option prdctfltr_st_option_plaintext">
										<em><?php echo $term->name . ' ' . __( 'Tooltip', 'prdctfltr' ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
									</span>
								</div>
							<?php
							}

					break;


					case 'color' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_color" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_color">
									<em><?php echo $term->name . ' ' . __( 'Color', 'prdctfltr' ); ?></em> <input class="prdctfltr_st_color" type="text" name="term_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['term_' . $term->slug] ) ? $settings['term_' . $term->slug] : '#cccccc'; ?>" />
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . __( 'Tooltip', 'prdctfltr' ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;


					case 'image' :
					case 'image-text' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_image" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_imgurl">
									<em><?php echo $term->name . ' ' . __( 'Image URL', 'prdctfltr' ); ?></em> <input type="text" name="term_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['term_' . $term->slug] ) ? $settings['term_' . $term->slug] : ''; ?>" />
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_button">
									<em><?php _e( 'Add/Upload image', 'prdctfltr' ); ?></em> <a href="#" class="prdctfltr_st_upload_media button"><?php _e( 'Image Gallery', 'prdctfltr' ); ?></a>
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . ( $curr_style == 'image' ? __( 'Tooltip', 'prdctfltr' ) : __( 'Text', 'prdctfltr' ) ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;


					case 'html' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_html" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_html">
									<em><?php echo $term->name . ' ' . __( 'HTML', 'prdctfltr' ); ?></em> <textarea type="text" name="term_<?php echo $term->slug; ?>"><?php echo isset( $settings['term_' . $term->slug] ) ? stripslashes( $settings['term_' . $term->slug] ) : ''; ?></textarea>
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . __( 'Tooltip', 'prdctfltr' ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;

					case 'select' :
					?>
						<div class="prdctfltr_select">
							<?php _e( 'Select Box currently has no special options. !Important Do not use select boxes inside the select box mode!', 'prdctfltr' ); ?>
						</div>
					<?php
					break;

					default :
					break;

				}

				$html = ob_get_clean();

				return $html;

			}
			else {
				if ( $filter == 'meta' ) {
					return __( 'Meta filter not customized. Use the Cogs Wheel icon!', 'prdctfltr' );
				}
				return __( 'Error! No terms!', 'prdctfltr' );
			}

		}

		public static function save_terms() {

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $key == '' || $settings == '' ) {
				die();
				exit;
			}

			$language = self::prdctfltr_wpml_language();

			if ( isset( $settings['style'] ) ) {
				if ( $language !== false ) {
					$key = $key . '_' . $language;
				}

				$alt['style'] = $settings['style'];
				unset( $settings['style'] );
				$alt['settings'] = $settings;

				update_option( $key, $alt );

				die( 'Updated!' );
				exit;
			}

			die();
			exit;

		}

		public static function remove_terms() {

			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $settings !== '' ) {
				$get_customization = get_option( $key, '' );

				if ( $get_customization !== '' ) {
					delete_option( $key );

					die( 'Removed' );
					exit;
				}
			}

			die();
			exit;

		}

		public static function add_filters() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';

			if ( !isset( $filter ) ) {
				die();
				exit;
			}

			switch ( $filter ) {
				case 'price' :
					ob_start();
?>
					<div class="prdctfltr_quickview_filter">
						<span class="pf_min">
							<em><?php _e( 'Minimum', 'prdctfltr' ); ?></em>
							<input type="text" name="pf_min" value="" />
						</span>
						<span class="pf_max">
							<em><?php _e( 'Maximum', 'prdctfltr' ); ?></em>
							<input type="text" name="pf_max" value="" />
						</span>
						<span class="pf_text">
							<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
							<textarea name="pf_text"></textarea>
						</span>
						<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
					</div>
<?php
					$html = ob_get_clean();
					die( $html );
					exit;

				break;
				case 'per_page' :
					ob_start();
?>
					<div class="prdctfltr_quickview_filter">
						<span class="pf_value">
							<em><?php _e( 'Value', 'prdctfltr' ); ?></em>
							<input type="number" min="1" name="pf_value" value="" />
						</span>
						<span class="pf_text">
							<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
							<textarea name="pf_text"></textarea>
						</span>
						<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
					</div>
<?php
					$html = ob_get_clean();
					die( $html );
					exit;
				break;
				case 'meta' :
					ob_start();
				?>
					<div class="prdctfltr_quickview_filter">
						<span class="pf_value">
							<em><?php _e( 'Value', 'prdctfltr' ); ?></em>
							<input type="text" name="pf_value" value="" />
						</span>
						<span class="pf_text">
							<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
							<textarea name="pf_text"></textarea>
						</span>
						<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
					</div>
<?php
					$html = ob_get_clean();
					die( $html );
					exit;
				default :
				break;
			}

		}

		public static function set_filters() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';

			if ( !isset( $filter ) ) {
				die();
				exit;
			}

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';

			$language = self::prdctfltr_wpml_language();

			if ( $key !== '' ) {
				if ( isset( $language ) && $language !== false ) {
					$get_customization = get_option( $key . '_' . $lanugage, '' );
				}
				else {
					$get_customization = get_option( $key, '' );
				}

				if ( $get_customization !== '' ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array();
				$key = 'wc_settings_prdctfltr_filter_customization_' . uniqid();
			}

			ob_start();
?>
			<div class="prdctfltr_quickview_terms" data-key="<?php echo $key; ?>">
				<span class="prdctfltr_quickview_close"><span class="prdctfltr_quickview_close_button"><?php _e( 'Click to discard any settings!', 'prdctfltr' ); ?></span></span>
				<div class="prdctfltr_quickview_terms_inner">
					<div class="prdctfltr_quickview_filters_settings">
						<span class="prdctfltr_set_filters_type" data-filter="<?php echo $filter; ?>"><?php _e( 'Type', 'prdctfltr' ); ?>: <code><?php echo $filter; ?></code></span>
						<a href="#" class="button-primary prdctfltr_set_filters_save"><?php _e( 'Save Customization', 'prdctfltr' ); ?></a>
						<a href="#" class="button prdctfltr_set_filters_add"><?php _e( 'Add Filter', 'prdctfltr' ); ?></a>
<?php
						if ( function_exists( 'icl_get_languages' ) ) {
							$languages = icl_get_languages();

							$select_languages = '<label class="pf_wpml"><span>' . __( 'Select Language', 'prdctfltr' ) . '</span> <select class="prdctfltr_set_filters_attr_select" name="lang">';

							foreach ( $languages as $k => $v ) {
								$selected = $language == $k ? ' selected="selected"' : '';
								$select_languages .= '<option value="' . $k . '" ' . $selected . '>' . $v['native_name'] . '</option>';

							}

							$select_languages .= '</select></label>';

							echo $select_languages;


						}
?>
					</div>
					<div class="prdctfltr_quickview_filters_manager prdctfltr_quickview_filter_<?php echo $filter; ?>">
<?php
						self::get_filters( $filter, $customization );
?>
					</div>
				</div>
			</div>
<?php
			$html = ob_get_clean();

			die( $html );
			exit;

		}

		public static function set_filters_new() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';

			$language = self::prdctfltr_wpml_language();

			if ( $filter == '' ) {
				die();
				exit;
			}

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

			$html = self::get_filters( $filter, $customization );

			die( $html );
			exit;

		}

		public static function get_filters( $filter, $customization ) {

			switch ( $filter ) {

				case 'price' :

					if ( empty( $customization ) ) {

						$curr_prices = array();
						$curr_prices_currency = array();
						$catalog_ready_price = array();

						$curr_price_set = get_option( 'wc_settings_prdctfltr_price_range', '100' );
						$curr_price_add = get_option( 'wc_settings_prdctfltr_price_range_add', '100' );
						$curr_price_limit = get_option( 'wc_settings_prdctfltr_price_range_limit', '6' );

						if ( get_option( 'wc_settings_prdctfltr_price_none', 'no' ) == 'no' ) {
							$catalog_ready_price = array(
								'-' => __( 'None', 'prdctfltr' )
							);
						}
					}
					else {
						foreach( $customization['settings'] as $k => $v ) {
							$prices[] = array(
								'value' => $k,
								'text' => $v
							);
						}
						$curr_price_limit = count( $customization['settings'] );
					}

					for ( $i = 0; $i < $curr_price_limit; $i++ ) {

						if ( empty( $customization ) ) {

							if ( $i == 0 ) {
								$min_price = 0;
								$max_price = $curr_price_set;
							}
							else {
								$min_price = $curr_price_set+( $i-1)*$curr_price_add;
								$max_price = $curr_price_set+$i*$curr_price_add;
							}

							$curr_text = strip_tags( wc_price( $min_price ) . ( $i+1 == $curr_price_limit ? '+' : ' - ' . wc_price( $max_price ) ) );

						}
						else {
							$vals = explode( '-', $prices[$i]['value'] );
							$min_price = ( isset( $vals[0] ) ? $vals[0] : '' );
							$max_price = ( isset( $vals[1] ) ? $vals[1] : '' );
							$curr_text = ( isset( $prices[$i]['text'] ) ? $prices[$i]['text'] : '' );
						}
?>
						<div class="prdctfltr_quickview_filter">
							<span class="pf_min">
								<em><?php _e( 'Minimum', 'prdctfltr' ); ?></em>
								<input type="text" name="pf_min" value="<?php echo $min_price; ?>" />
							</span>
							<span class="pf_max">
								<em><?php _e( 'Maximum', 'prdctfltr' ); ?></em>
								<input type="text" name="pf_max" value="<?php echo ( ( $i+1) == $curr_price_limit ? '' : $max_price ); ?>" />
							</span>
							<span class="pf_text">
								<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
								<textarea name="pf_text"><?php echo stripslashes( $curr_text ); ?></textarea>
							</span>
							<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
						</div>
<?php
					}

				break;

				case 'per_page' :

					if ( empty( $customization ) ) {

						$curr_perpage_set = get_option( 'wc_settings_prdctfltr_perpage_range', '20' );
						$curr_perpage_limit = get_option( 'wc_settings_prdctfltr_perpage_range_limit', '5' );

						$curr_perpage = array();

						for ( $i = 1; $i <= $curr_perpage_limit; $i++ ) {
							$curr_perpage[$curr_perpage_set*$i] = $curr_perpage_set*$i . ' ' . ( $curr_options['wc_settings_prdctfltr_perpage_label'] == '' ? __( 'Products', 'prdctfltr' ) : $curr_options['wc_settings_prdctfltr_perpage_label'] );
						}

					}
					else {
						$curr_perpage_limit = count( $customization['settings'] );

						for ( $i = 0; $i < $curr_perpage_limit; $i++ ) {
							$curr_perpage[$customization['settings'][$i]['value']] = $customization['settings'][$i]['text'];
						}
					}

					foreach( $curr_perpage as $k => $v ) {
?>
						<div class="prdctfltr_quickview_filter">
							<span class="pf_value">
								<em><?php _e( 'Value', 'prdctfltr' ); ?></em>
								<input type="number" name="pf_value" min="1" value="<?php echo $k; ?>" />
							</span>
							<span class="pf_text">
								<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
								<textarea name="pf_text"><?php echo stripslashes( $v ); ?></textarea>
							</span>
							<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
						</div>
<?php

					}

				break;

				case 'meta' :

					if ( empty( $customization ) ) {

						$curr_meta = array(
							'' => ''
						);

					}
					else {
						$curr_meta_limit = count( $customization['settings'] );

						for ( $i = 0; $i < $curr_meta_limit; $i++ ) {
							$curr_meta[$customization['settings'][$i]['value']] = $customization['settings'][$i]['text'];
						}
					}

					foreach( $curr_meta as $k => $v ) {
?>
						<div class="prdctfltr_quickview_filter">
							<span class="pf_value">
								<em><?php _e( 'Value', 'prdctfltr' ); ?></em>
								<input type="text" name="pf_value" value="<?php echo $k; ?>" />
							</span>
							<span class="pf_text">
								<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
								<textarea name="pf_text"><?php echo stripslashes( $v ); ?></textarea>
							</span>
							<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
						</div>
<?php

					}

				break;

				default :
				break;

			}

		}

		public static function save_filters() {

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $key == '' || $filter == '' || $settings == '' ) {
				die();
				exit;
			}

			$language = self::prdctfltr_wpml_language();

			if ( $language !== false ) {
				$key = $key . '_' . $language;
			}

			$alt['filter'] = $filter;

			if ( $filter == 'price' ) {
				foreach ( $settings as $set ) {
					$alt['settings'][$set['min'] . '-' . $set['max']] = $set['text'];
				}
			}
			else {
				$alt['settings'] = $settings;
			}

			update_option( $key, $alt );

			die( 'Updated!' );
			exit;

		}

		public static function remove_filters() {

			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $settings !== '' ) {
				$get_customization = get_option( $key, '' );

				if ( $get_customization !== '' ) {
					delete_option( $key );

					die( 'Removed' );
					exit;
				}
			}

			die();
			exit;

		}

		public static function reset_options() {

			global $wpdb;

			$wpdb->query( "delete from $wpdb->options where option_name like '%prdctfltr%';" );

			update_option( 'wc_settings_prdctfltr_version', PrdctfltrInit::$version, 'yes' );

			die( 'Deleted!');
			exit;
		
		}
		public static function analytics_reset() {

			delete_option( 'wc_settings_prdctfltr_filtering_analytics_stats' );
			die( 'Updated!' );
			exit;

		}

		public static function prdctfltr_utf8_decode( $str ) {
			$str = preg_replace( "/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode( $str ) );
			return html_entity_decode( $str, null, 'UTF-8' );
		}

		public static function prdctfltr_wpml_language() {

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


		public static function prdctfltr_pf_taxonomy_sanitize( $value, $option, $raw_value ) {
			if ( $option['type'] == 'pf_taxonomy' ) {
				$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
				return $value;
			}
			return $value;
		}


		public static function prdctfltr_pf_taxonomy( $field ) {

			$option_value = WC_Admin_Settings::get_option( $field['id'], $field['default'] );
		?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
					<?php //echo $tooltip_html; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
					<?php
						$args = array(
							'hide_empty' => 0,
							'echo' => 0,
							'hierarchical' => ( is_taxonomy_hierarchical( $field['options'] ) ? 1 : 0 ),
							'name' => esc_attr( $field['id'] ) . '[]',
							'id' => esc_attr( $field['id'] ),
							'class' => esc_attr( $field['class'] ),
							'depth' => 0,
							'taxonomy' => $field['options'],
							'hide_if_empty' => true,
							'value_field' => 'slug'
						);

						$dropdown = wp_dropdown_categories( $args );
						$dropdown = str_replace( 'id=', ' style="width:300px;margin-right:12px;" multiple="multiple" id=', $dropdown );
						if ( is_array( $option_value ) ) {
							foreach ( $option_value as $key => $post_term ) { 
								$dropdown = str_replace(' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown); 
							}
						}
						else {
							$dropdown = str_replace( ' value="' . $option_value . '"', ' value="' . $option_value . '" selected="selected"', $dropdown );
						}
						echo $dropdown;
					?>
				</td>
			</tr>
		<?php

		}

	}

	add_action( 'init', 'WC_Settings_Prdctfltr::init' );

?>