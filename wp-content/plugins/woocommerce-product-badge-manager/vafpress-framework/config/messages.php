<?php

return array(

	////////////////////////////////////////
	// Localized JS Message Configuration //
	////////////////////////////////////////

	/**
	 * Validation Messages
	 */
	'validation' => array(
		'alphabet'     => __('Value needs to be Alphabet', 'woo_product_badge_manager_txtd'),
		'alphanumeric' => __('Value needs to be Alphanumeric', 'woo_product_badge_manager_txtd'),
		'numeric'      => __('Value needs to be Numeric', 'woo_product_badge_manager_txtd'),
		'email'        => __('Value needs to be Valid Email', 'woo_product_badge_manager_txtd'),
		'url'          => __('Value needs to be Valid URL', 'woo_product_badge_manager_txtd'),
		'maxlength'    => __('Length needs to be less than {0} characters', 'woo_product_badge_manager_txtd'),
		'minlength'    => __('Length needs to be more than {0} characters', 'woo_product_badge_manager_txtd'),
		'maxselected'  => __('Select no more than {0} items', 'woo_product_badge_manager_txtd'),
		'minselected'  => __('Select at least {0} items', 'woo_product_badge_manager_txtd'),
		'required'     => __('This is required', 'woo_product_badge_manager_txtd'),
	),

	/**
	 * Import / Export Messages
	 */
	'util' => array(
		'import_success'    => __('Import succeed, option page will be refreshed..', 'woo_product_badge_manager_txtd'),
		'import_failed'     => __('Import failed', 'woo_product_badge_manager_txtd'),
		'export_success'    => __('Export succeed, copy the JSON formatted options', 'woo_product_badge_manager_txtd'),
		'export_failed'     => __('Export failed', 'woo_product_badge_manager_txtd'),
		'restore_success'   => __('Restoration succeed, option page will be refreshed..', 'woo_product_badge_manager_txtd'),
		'restore_nochanges' => __('Options identical to default', 'woo_product_badge_manager_txtd'),
		'restore_failed'    => __('Restoration failed', 'woo_product_badge_manager_txtd'),
	),

	/**
	 * Control Fields String
	 */
	'control' => array(
		// select2 select box
		'select2_placeholder' => __('Select option(s)', 'woo_product_badge_manager_txtd'),
		// fontawesome chooser
		'fac_placeholder'     => __('Select an Icon', 'woo_product_badge_manager_txtd'),
	),

);

/**
 * EOF
 */