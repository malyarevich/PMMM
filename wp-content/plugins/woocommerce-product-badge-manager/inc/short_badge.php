<?php
/* ************************************
* Sort it for the Display List too
*************************************** */
add_filter( 'posts_orderby', 'woo_pro_badge_orderby');
function woo_pro_badge_orderby($orderby){
global $wpdb;

if (is_admin())
$orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";

return($orderby);
}

/* ************************************
* Ajax Sort for badges
*************************************** */
function woo_pro_badge_enable_badges_sort() {
    add_submenu_page('edit.php?post_type=woo_product_bages', __('Sort Product Badge', 'woo_product_badge_manager_txtd'), __('Sort Product Badges', 'woo_product_badge_manager_txtd'), 'edit_posts', basename(__FILE__), 'woo_pro_badge_sort_badges');
}
add_action('admin_menu' , 'woo_pro_badge_enable_badges_sort');

 
/**
 * Display Sort admin
 *
 * @return void
 * @author Soul
 **/
function woo_pro_badge_sort_badges() {
	$badges = new WP_Query('post_type=woo_product_bages&posts_per_page=-1&orderby=menu_order&order=ASC');
?>
	<div class="wrap">
	<h2><?php echo __("Sort Woo Product Badges", "woo_product_badge_manager_txtd"); ?> <img src="<?php echo home_url(); ?>/wp-admin/images/loading.gif" id="loading-animation" /></h2>
	<div class="description">
	<?php echo __("Drag and Drop the badges to order them", "woo_product_badge_manager_txtd"); ?>
	</div>
	<ul id="woo-bage-sort-list">
	<?php while ( $badges->have_posts() ) : $badges->the_post(); ?>
		<li id="<?php the_id(); ?>">
		<?php $image_url=wp_get_attachment_thumb_url( get_post_thumbnail_id() );	?>
		<?php if ($image_url) { echo '<img class="woo_pro_badges_admin_sort_image" src="'.$image_url.'" width="5%" height="5%" />'; } ?>
		<span class="woo_pro_badges_admin_sort_title"><?php the_title(); ?></span>
		</li>		
	<?php endwhile; ?>
	</div><!-- End div#wrap //-->
 
<?php
}

/**
 * Upadate the badges Sort order
 *
 * @return void
 * @author Soul
 **/
function woo_pro_badges_save_badges_order() {
	global $wpdb; // WordPress database class
 
	$order = explode(',', $_POST['order']);
	$counter = 0;
 
	foreach ($order as $sort_id) {
		$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $sort_id) );
		$counter++;
	}
	die(1);
}
add_action('wp_ajax_badges_sort', 'woo_pro_badges_save_badges_order');

?>