<?php
// Define Plugin Text Domain
add_action('after_setup_theme', 'woo_product_badge_manager_txtd');
function woo_product_badge_manager_txtd()
{
	load_theme_textdomain('woo_product_badge_manager_txtd', WOO_PRODUCT_BADGE_MANAGER_DIR . '/lang/');
}