<?php

// Loading Css
function woo_pro_badge_manager_css(){
     wp_enqueue_style( 'woo_pro_badge_manager_frameowrk_css', plugins_url( '/css/framework.css' , __FILE__ ) );
     wp_enqueue_style( 'woo_pro_badge_manager_custom_css', plugins_url( '/css/custom.css' , __FILE__ ) );
     wp_enqueue_style( 'woo_pro_badge_manager_tool_tip_css', plugins_url( '/css/tooltipster.css' , __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'woo_pro_badge_manager_css' );

//Loading javaScript
function woo_pro_badge_manager_js(){
	
	wp_enqueue_script( 'woo_pro_badge_manager_tool_tip_js', plugins_url( '/js/jquery.tooltipster.min.js' , __FILE__ ) , array( 'jquery') , false);
	wp_enqueue_script( 'woo_pro_badge_manager_custom_js', plugins_url( '/js/custom.js' , __FILE__ ) , array( 'jquery') , false);
}
add_action( 'wp_enqueue_scripts', 'woo_pro_badge_manager_js' );

//Load style shit and javascript for admin
function woo_pro_badge_manager_admin_js(){
	
	// admin css
    wp_enqueue_style( 'woo_pro_badge_admin_css', plugins_url( '/css/admin.css' , __FILE__ ) );
    // admin js
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script( 'woo_pro_badge_manager_post_sorter_js', plugins_url( '/js/post-sorter.js' , __FILE__ ) , array( 'jquery') , false);

}
add_action( 'admin_enqueue_scripts', 'woo_pro_badge_manager_admin_js' );