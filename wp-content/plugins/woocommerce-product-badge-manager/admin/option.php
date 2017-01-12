<?php

return array(
	'title' => __('Woo Product Badge Manager Settings', 'woo_product_badge_manager_txtd'),
	'logo' => WOO_PRODUCT_BADGE_MANAGER_URI . 'images/logo.png',
	'menus' => array(
		array(
			'title' => __('Badge Options', 'woo_product_badge_manager_txtd'),
			'name' => 'woo_badge_options',
			'controls' => array(
				array(
					'type' => 'section',
					'title' => __('Show Badge In Single Product Page', 'woo_product_badge_manager_txtd'),
					'name' => 'single_product_badge_section',		
					'fields' => array(
						array(
							'type' => 'toggle',
							'name' => 'show_badge_single_page',					
							'label' => __('Enable/Disable', 'woo_product_badge_manager_txtd'),
							'default' => '1'
						),
						array(
							'type' => 'select',
							'name' => 'single_product_badge_position',
							'label' => __('Show Badge', 'woo_product_badge_manager_txtd'),
							'default' => '6',
							'items' => array(
								array(
									'value' => '4',
									'label' => __('Before Product Title', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '6',
									'label' => __('After Product Title', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '9',
									'label' => __('Before Product Price', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '11',
									'label' => __('After Product Price', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '19',
									'label' => __('Before Product Excerpt', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '21',
									'label' => __('After Product Excerpt', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '29',
									'label' => __('Before Product Cart Button', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '31',
									'label' => __('After Product Cart Button', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '49',
									'label' => __('Before Product Sharing Button', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '51',
									'label' => __('After Product Sharing Button', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '39',
									'label' => __('Before Product Meta', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '41',
									'label' => __('After Product Meta', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '98',
									'label' => __('Before Product Tabs', 'woo_product_badge_manager_txtd'),
								),
							),
						),
						array(
							'type' => 'textbox',
							'name' => 'single_product_badge_top',					
							'label' => __('Margin Top (Use px or %)', 'woo_product_badge_manager_txtd'),
							'default' => '15px'
						),
						array(
							'type' => 'textbox',
							'name' => 'single_product_badge_bottom',					
							'label' => __('Margin Bottom (Use px or %)', 'woo_product_badge_manager_txtd'),
							'default' => '15px'
						),
						array(
							'type' => 'slider',
							'name' => 'single_product_badge_width',					
							'label' => __('Badge Width', 'woo_product_badge_manager_txtd'),
							'min' => '5',
							'max' => '320',
							'default' => '50'
						),
						array(
							'type' => 'radiobutton',
							'name' => 'single_product_show_tooltip',
							'label' => __('Show Tool Tip', 'woo_product_badge_manager_txtd'),
							'default' => 'yes',
							'items' => array(
								array(
									'value' => 'yes',
									'label' => __('Yes Please', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'no',
									'label' => __('No Please', 'woo_product_badge_manager_txtd'),
								),
							),
						),
						array(
							'type' => 'select',
							'name' => 'single_product_show_tooltip_animation',
							'label' => __('Tool Tip Animation', 'woo_product_badge_manager_txtd'),
							'default' => 'grow',
							'items' => array(
								array(
									'value' => 'grow',
									'label' => __('Grow Up', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'fade',
									'label' => __('Fade In', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'swing',
									'label' => __('Swing Up', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'slide',
									'label' => __('Slide', 'woo_product_badge_manager_txtd'),
								),
							),
						),						
						array(
							'type' => 'radiobutton',
							'name' => 'single_product_badge_page_link',
							'label' => __('Badge Description Page / Badge Linking', 'woo_product_badge_manager_txtd'),
							'default' => 'false',
							'items' => array(
								array(
									'value' => 'true',
									'label' => __('Yes Please', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'false',
									'label' => __('No Please', 'woo_product_badge_manager_txtd'),
								),
							),
						),
					),
				),
			),
		),
		array(
			'title' => __('Badge Shortcode', 'woo_product_badge_manager_txtd'),
			'name' => 'badge_shortcode_gen',
			'controls' => array(
				array(
					'type' => 'section',
					'title' => __('Create Badge Shortcode', 'woo_product_badge_manager_txtd'),
					'name' => 'create_badge_shortcode',		
					'fields' => array(
						array(
							'type' => 'select',
							'name' => 'custom_shortcode_name',
							'label' => __('Select ShortCode For Author / Product', 'woo_product_badge_manager_txtd'),
							'default' => 'woo_pro_badges',
							'items' => array(
								array(
									'value' => 'woo_pro_badges',
									'label' => __('Product Page ShortCode', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'woo_author_bages',
									'label' => __('Author Page ShortCode', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'woo_pro_badges_filter',
									'label' => __('Badge Filter Shortcode', 'woo_product_badge_manager_txtd'),
								),
							),
						),
						array(
							'type' => 'textbox',
							'name' => 'shortcode_gen_badge_top',					
							'label' => __('Margin Top (Use px or %)', 'woo_product_badge_manager_txtd'),
							'default' => '15px'
						),
						array(
							'type' => 'textbox',
							'name' => 'shortcode_gen_badge_bottom',					
							'label' => __('Margin Bottom (Use px or %)', 'woo_product_badge_manager_txtd'),
							'default' => '15px'
						),
						array(
							'type' => 'slider',
							'name' => 'shortcode_gen_badge_width',					
							'label' => __('Badge Width', 'woo_product_badge_manager_txtd'),
							'min' => '5',
							'max' => '320',
							'default' => '50'
						),
						array(
							'type' => 'radiobutton',
							'name' => 'shortcode_gen_show_tooltip',
							'label' => __('Show Tool Tip', 'woo_product_badge_manager_txtd'),
							'default' => 'yes',
							'items' => array(
								array(
									'value' => 'yes',
									'label' => __('Yes Please', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'no',
									'label' => __('No Please', 'woo_product_badge_manager_txtd'),
								),
							),
						),
						array(
							'type' => 'select',
							'name' => 'shortcode_gen_show_tooltip_animation',
							'label' => __('Tool Tip Animation', 'woo_product_badge_manager_txtd'),
							'default' => 'grow',
							'items' => array(
								array(
									'value' => 'grow',
									'label' => __('Grow Up', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'fade',
									'label' => __('Fade In', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'swing',
									'label' => __('Swing Up', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'slide',
									'label' => __('Slide', 'woo_product_badge_manager_txtd'),
								),
							),
						),						
						array(
							'type' => 'radiobutton',
							'name' => 'shortcode_gen_badge_page_link',
							'label' => __('Badge Description Page / Badge Linking', 'woo_product_badge_manager_txtd'),
							'default' => 'false',
							'items' => array(
								array(
									'value' => 'true',
									'label' => __('Yes Please', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'false',
									'label' => __('No Please', 'woo_product_badge_manager_txtd'),
								),
							),
						),
						array(
						    'type' => 'textbox',
						    'name' => 'custom_badge_shortcode',
						    'label' => __('Shortcode Here', 'woo_product_badge_manager_txtd'),
						    'description' => __('User Above Options and generate a short code.', 'woo_product_badge_manager_txtd'),
						    'binding' => array(
						        'field' => 'custom_shortcode_name,shortcode_gen_badge_top,shortcode_gen_badge_bottom,shortcode_gen_badge_width,shortcode_gen_show_tooltip,shortcode_gen_show_tooltip_animation,shortcode_gen_badge_page_link',
						        'function' => 'custom_shortcode_generator_options_page'
						    )
						),						
					),
				),
			),
		),
		array(
			'title' => __('Badge Archive Options', 'woo_product_badge_manager_txtd'),
			'name' => 'badge_archvie_options',
			'controls' => array(
				array(
					'type' => 'toggle',
					'name' => 'enable_archive_list_on_archive_page',					
					'label' => __('Enable Product Archive On Badge Page', 'woo_product_badge_manager_txtd'),
					'default' => '1'
				),
				array(
					'type' => 'section',
					'title' => __('Archive Page Settings', 'woo_product_badge_manager_txtd'),
					'name' => 'badge_archive_page_settings',
					'fields' => array(
						array(
							'type' => 'toggle',
							'name' => 'show_filter_on_archvie',					
							'label' => __('Show Badge Filter On Archive Page', 'woo_product_badge_manager_txtd'),
							'default' => '1'
						),
						array(
							'type' => 'textbox',
							'name' => 'show_filter_title',
							'label' => __('Show Badge Filters Title', 'woo_product_badge_manager_txtd'),
							'default' => 'Filter Product Using Badge',
						),
						array(
							'type' => 'color',
							'name' => 'cart_btn_background',
							'label' => __('Cart Button Background Color', 'woo_product_badge_manager_txtd'),
							'default' => '#db2121',
						),
					),
				),
				array(
					'type' => 'section',
					'name' => 'badge_archive_section',
					'title' => __('Archive Badge Settings', 'woo_product_badge_manager_txtd'),
					'fields' => array(
						array(
							'type' => 'textbox',
							'name' => 'archive_single_product_badge_top',					
							'label' => __('Margin Top (Use px or %)', 'woo_product_badge_manager_txtd'),
							'default' => '0px'
						),
						array(
							'type' => 'textbox',
							'name' => 'archive_single_product_badge_bottom',					
							'label' => __('Margin Bottom (Use px or %)', 'woo_product_badge_manager_txtd'),
							'default' => '0px'
						),
						array(
							'type' => 'slider',
							'name' => 'archive_single_product_badge_width',					
							'label' => __('Badge Width', 'woo_product_badge_manager_txtd'),
							'min' => '5',
							'max' => '320',
							'default' => '50'
						),
						array(
							'type' => 'radiobutton',
							'name' => 'archive_single_product_show_tooltip',
							'label' => __('Show Tool Tip', 'woo_product_badge_manager_txtd'),
							'default' => 'yes',
							'items' => array(
								array(
									'value' => 'yes',
									'label' => __('Yes Please', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'no',
									'label' => __('No Please', 'woo_product_badge_manager_txtd'),
								),
							),
						),
						array(
							'type' => 'select',
							'name' => 'archive_single_product_show_tooltip_animation',
							'label' => __('Tool Tip Animation', 'woo_product_badge_manager_txtd'),
							'default' => 'grow',
							'items' => array(
								array(
									'value' => 'grow',
									'label' => __('Grow Up', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'fade',
									'label' => __('Fade In', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'swing',
									'label' => __('Swing Up', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => 'slide',
									'label' => __('Slide', 'woo_product_badge_manager_txtd'),
								),
							),
						),
					),
				),
				array(
					'type' => 'section',
					'name' => 'badge_archive_product_list',
					'title' => __('Archive Page Product List', 'woo_product_badge_manager_txtd'),
					'fields' => array(
						array(
							'type' => 'select',
							'name' => 'archive_desktop_large_version',
							'label' => __('Show Product Per Row On Large Desktop', 'woo_product_badge_manager_txtd'),
							'default' => '3',
								'items' => array(
									array(
										'value' => '1',
										'label' => __('12 Per Row', 'woo_product_badge_manager_txtd'),
									),
									array(
										'value' => '2',
										'label' => __('6 Per Row', 'woo_product_badge_manager_txtd'),
									),
									array(
										'value' => '3',
										'label' => __('4 Per Row', 'woo_product_badge_manager_txtd'),
									),
									array(
										'value' => '4',
										'label' => __('3 Per Row', 'woo_product_badge_manager_txtd'),
									),
									array(
										'value' => '6',
										'label' => __('2 Per Row', 'woo_product_badge_manager_txtd'),
									),
									array(
										'value' => '12',
										'label' => __('1 Per Row', 'woo_product_badge_manager_txtd'),
									),
								),
						),
						array(
							'type' => 'select',
							'name' => 'archive_desktop_medium_version',
							'label' => __('Show Product Per Row On Medium Desktop', 'woo_product_badge_manager_txtd'),
							'default' => '3',
							'items' => array(
								array(
									'value' => '1',
									'label' => __('12 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '2',
									'label' => __('6 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '3',
									'label' => __('4 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '4',
									'label' => __('3 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '6',
									'label' => __('2 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '12',
									'label' => __('1 Per Row', 'woo_product_badge_manager_txtd'),
								),
							),
						),
						array(
							'type' => 'select',
							'name' => 'archive_desktop_tablet_version',
							'label' => __('Show Product Per Row On Tablet', 'woo_product_badge_manager_txtd'),
							'default' => '4',
							'items' => array(
								array(
									'value' => '1',
									'label' => __('12 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '2',
									'label' => __('6 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '3',
									'label' => __('4 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '4',
									'label' => __('3 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '6',
									'label' => __('2 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '12',
									'label' => __('1 Per Row', 'woo_product_badge_manager_txtd'),
								),
							),
						),
						array(
							'type' => 'select',
							'name' => 'archive_desktop_mobile_version',
							'label' => __('Show Product Per Row On Mobile', 'woo_product_badge_manager_txtd'),
							'default' => '6',
							'items' => array(
								array(
									'value' => '1',
									'label' => __('12 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '2',
									'label' => __('6 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '3',
									'label' => __('4 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '4',
									'label' => __('3 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '6',
									'label' => __('2 Per Row', 'woo_product_badge_manager_txtd'),
								),
								array(
									'value' => '12',
									'label' => __('1 Per Row', 'woo_product_badge_manager_txtd'),
								),
							),
						),
					),
				),
			),
		),
	),
);