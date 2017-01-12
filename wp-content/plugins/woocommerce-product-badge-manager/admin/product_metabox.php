<?php
// return metabox
return array(
		array(
			'type' => 'multiselect',
			'name' => 'woo_pro_badges',
			'label' => __('Select Badges', 'woo_product_badge_manager_txtd'),		
			'description' => __('Select Product Badges to show for this product.', 'woo_product_badge_manager_txtd'),			
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'get_woo_pro_badges',
					),
				),
			),
		),
	);
