<?php

// load jquery from wordpress own server
add_action('init', 'woo_product_badge_manager_jquery');
function woo_product_badge_manager_jquery() {
	wp_enqueue_script('jquery');
}

// list of all users
function getAllAuthors() {
	$get_authors = get_users(array("fields" => array('ID', 'display_name')));
	$site_authors = array();
	foreach ($get_authors as $key => $value) {
		$site_authors[$value->ID] = $value->display_name;
	}
	return $site_authors;
}

// load custom post for product badge
require_once(WOO_PRODUCT_BADGE_MANAGER_DIR . '/inc/create_badge.php');

// badge sorter function
require_once(WOO_PRODUCT_BADGE_MANAGER_DIR . '/inc/short_badge.php');

// create shortcode to show badge
require_once (WOO_PRODUCT_BADGE_MANAGER_DIR . '/inc/shortcode.php');

// load vafpress framework
require_once(WOO_PRODUCT_BADGE_MANAGER_DIR . '/vafpress-framework/bootstrap.php');

// load badges filter widget file
require_once(WOO_PRODUCT_BADGE_MANAGER_DIR . '/widget/badge_filter_widget.php');

// Create instance of Options
// Built path to options plugin array file
$plugin_opt  = WOO_PRODUCT_BADGE_MANAGER_DIR. '/admin/option.php';
// options object param
$plugin_options = new VP_Option(array(
	'is_dev_mode'           => false,                                  // dev mode, default to false
	'option_key'            => 'woo_badge_man_opt',                    // options key in db, required
	'page_slug'             => 'woo_product_badge_manager',            // options page slug, required
	'template'              => $plugin_opt,                              // template file path or array, required
	'menu_page'             => 'options-general.php',                  // parent menu slug or supply `array` (can contains 'icon_url' & 'position') for top level menu
	'use_auto_group_naming' => true,                                   // default to true
	'use_util_menu'         => true,                                   // default to true, shows utility menu
	'minimum_role'          => 'edit_theme_options',                   // default to 'edit_theme_options'
	'layout'                => 'boxed',                                // fluid or fixed, default to fixed
	'page_title'            => __( 'Woo Product Badge Manager Settings', 'woo_product_badge_manager_txtd' ), // page title
	'menu_label'            => __( 'Woo Product Badge Manager', 'woo_product_badge_manager_txtd' ), // menu label
));

// Built path to metabox template array file
// $mb_path_product  = WOO_PRODUCT_BADGE_MANAGER_DIR . '/admin/product_metabox.php';
// $mb_path_badges_cat  = WOO_PRODUCT_BADGE_MANAGER_DIR . '/admin/badges_cat_metabox.php';

// We can use array or file path to the array specification file.
// $mb_product = new VP_Metabox(array(
//     'id'          => 'woo_pro_badge_meta_box',
//     'types'       => array('product'),
//     'title'       => __('Product Badges Manager', 'woo_product_badge_manager_txtd'),
//     'priority'    => 'high',
//     'is_dev_mode' => false,
//     'template'    => $mb_path_product
// ));
// $mb_badges = new VP_Metabox(array(
//     'id'          => 'woo_pro_badge_cat_meta_box',
//     'types'       => array('woo_product_bages'),
//     'title'       => __('Assign Badges To Category', 'woo_product_badge_manager_txtd'),
//     'priority'    => 'high',
//     'is_dev_mode' => false,
//     'template'    => $mb_path_badges_cat
// ));

// load custom data to show in metabox
function get_woo_pro_badges() {
	$pro_badges = get_posts(array(
		'post_type' => 'woo_product_bages',
		'posts_per_page' => -1
	));	
	
	$result = array();
	foreach ($pro_badges as $badge)
	{
		$result[] = array('value' => $badge->ID, 'label' => $badge->post_title);
	}
	return $result;
}

function get_woo_pro_cats() {
	$pro_cats = get_categories(array(
		'menu_order'   => 'ASC',
		'hide_empty'   => 0,
		'hierarchical' => 1,
		'taxonomy'     => 'product_cat',
		'pad_counts'   => 1
	));	
	
	$result = array();
	foreach ($pro_cats as $cat)
	{
		$result[] = array('value' => $cat->term_id, 'label' => $cat->name);
	}
	return $result;
}

// add author capablity on product
add_action('init', 'woo_pro_post_user_support', 999 );
function woo_pro_post_user_support() {
    add_post_type_support( 'product', 'author' );
}

// bind shortcode and show again in options page
function custom_shortcode_generator_options_page($custom_shortcode_name = "",$shortcode_gen_badge_top = "",$shortcode_gen_badge_bottom = "",$shortcode_gen_badge_width = "",$shortcode_gen_show_tooltip = "",$shortcode_gen_show_tooltip_animation = "",$shortcode_gen_badge_page_link = "")
{
    $result = "[$custom_shortcode_name margintop='$shortcode_gen_badge_top' marginbottom='$shortcode_gen_badge_bottom' size='$shortcode_gen_badge_width' tooltip='$shortcode_gen_show_tooltip' tooltip_animation='$shortcode_gen_show_tooltip_animation' badge_description_page='$shortcode_gen_badge_page_link']";
    return $result;
}
VP_Security::instance()->whitelist_function('custom_shortcode_generator_options_page');

// Add settings link on plugin buttom
function badge_custom_links_plugin_bottom($links) { 
  $settings_link = '<a href="options-general.php?page=woo_product_badge_manager">'.__("Settings", "woo_product_badge_manager_txtd").'</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin_base_name = WOO_PRODUCT_BADGE_MANAGER_NAME; 
add_filter("plugin_action_links_$plugin_base_name", 'badge_custom_links_plugin_bottom' );

// add badge to product
require_once (WOO_PRODUCT_BADGE_MANAGER_DIR . '/inc/woo_add.php');

// product list using badge filter
function woo_product_list_badge_filter($content) {
	ob_start();
	include_once(WOO_PRODUCT_BADGE_MANAGER_DIR . '/inc/product_list.php');
	$content .= ob_get_clean();
	return $content;
}

/* Filter the single_template with our custom function*/
add_filter('single_template', 'woo_filtered_product_single_badge');

function woo_filtered_product_single_badge($single) {
    global $wp_query, $post;

/* Checks for single template by post type */
if ($post->post_type == "woo_product_bages"){
    add_action('the_content', 'woo_product_list_badge_filter');
}
    return $single;
}