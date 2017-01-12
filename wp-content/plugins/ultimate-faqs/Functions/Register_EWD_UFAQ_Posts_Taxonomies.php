<?php
add_action( 'init', 'EWD_UFAQ_Create_Posttype' );
function EWD_UFAQ_Create_Posttype() {
		$Slug_Base = get_option("EWD_UFAQ_Slug_Base");
		
		$labels = array(
				'name' => __('FAQs', 'EWD_UFAQ'),
				'singular_name' => __('FAQ', 'EWD_UFAQ'),
				'menu_name' => __('FAQs', 'EWD_UFAQ'),
				'add_new' => __('Add New', 'EWD_UFAQ'),
				'add_new_item' => __('Add New FAQ', 'EWD_UFAQ'),
				'edit_item' => __('Edit FAQ', 'EWD_UFAQ'),
				'new_item' => __('New FAQ', 'EWD_UFAQ'),
				'view_item' => __('View FAQ', 'EWD_UFAQ'),
				'search_items' => __('Search FAQs', 'EWD_UFAQ'),
				'not_found' =>  __('Nothing found', 'EWD_UFAQ'),
				'not_found_in_trash' => __('Nothing found in Trash', 'EWD_UFAQ'),
				'parent_item_colon' => ''
		);

		$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'has_archive' => true,
				'menu_icon' => null,
				'rewrite' => array('slug' => $Slug_Base),
				'capability_type' => 'post',
				'menu_position' => null,
				'menu_icon' => 'dashicons-format-status',
				'supports' => array('title','editor','author','excerpt','comments')
	  ); 

	register_post_type( 'ufaq' , $args );
}

function EWD_UFAQ_Create_Category_Taxonomy() {

	register_taxonomy('ufaq-category', 'ufaq', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => true,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => __('FAQ Categories', 'EWD_UFAQ'),
			'singular_name' => __('FAQ Category', 'EWD_UFAQ'),
			'search_items' =>  __('Search FAQ Categories', 'EWD_UFAQ'),
			'all_items' => __('All FAQ Categories', 'EWD_UFAQ'),
			'parent_item' => __('Parent FAQ Category', 'EWD_UFAQ'),
			'parent_item_colon' => __('Parent FAQ Category:', 'EWD_UFAQ'),
			'edit_item' => __('Edit FAQ Category', 'EWD_UFAQ'),
			'update_item' => __('Update FAQ Category', 'EWD_UFAQ'),
			'add_new_item' => __('Add New FAQ Category', 'EWD_UFAQ'),
			'new_item_name' => __('New FAQ Category Name', 'EWD_UFAQ'),
			'menu_name' => __('FAQ Categories', 'EWD_UFAQ'),
		),
		'query_var' => true
	));

	register_taxonomy('ufaq-tag', 'ufaq', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => false,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => __('FAQ Tags', 'EWD_UFAQ'),
			'singular_name' => __('FAQ Tag', 'EWD_UFAQ'),
			'search_items' =>  __('Search FAQ Tags', 'EWD_UFAQ'),
			'all_items' => __('All FAQ Tags', 'EWD_UFAQ'),
			'parent_item' => __('Parent FAQ Tag', 'EWD_UFAQ'),
			'parent_item_colon' => __('Parent FAQ Tag:', 'EWD_UFAQ'),
			'edit_item' => __('Edit FAQ Tag', 'EWD_UFAQ'),
			'update_item' => __('Update FAQ Tag', 'EWD_UFAQ'),
			'add_new_item' => __('Add New FAQ Tag', 'EWD_UFAQ'),
			'new_item_name' => __('New FAQ Tag Name', 'EWD_UFAQ'),
			'menu_name' => __('FAQ Tags', 'EWD_UFAQ'),
		)
	));
}
add_action( 'init', 'EWD_UFAQ_Create_Category_Taxonomy', 0 );

?>
