<?php
// return metabox
return array(
		array(
			'type' => 'multiselect',
			'name' => 'woo_pro_badges_cat',
			'label' => __('Select Category To Assign With This Badge', 'woo_product_badge_manager_txtd'),		
			'description' => __('Select product category to show this badge for all product with this category.', 'woo_product_badge_manager_txtd'),			
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'get_woo_pro_cats',
					),
				),
			),
		),
		array(
			'type' => 'multiselect',
			'name' => 'woo_pro_published_author',
			'label' => __('Select Author To Assign With This Badge', 'woo_product_badge_manager_txtd'),		
			'description' => __('Select product author to show this badge for all product published by this author.', 'woo_product_badge_manager_txtd'),			
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'vp_get_users',
					),
				),
			),
		),
        array(
            'type'        => 'textbox',
            'name'        => 'custom_badge_link',
            'label'       => __('Badge Custom Description Page Link', 'woo_product_badge_manager_txtd'),
            'validation' => 'url',
        ),
	);
