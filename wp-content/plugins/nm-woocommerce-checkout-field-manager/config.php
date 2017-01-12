<?php
/*
 * this file contains pluing meta information and then shared
 * between pluging and admin classes
 * 
 * [1]
 * TODO: change this meta as plugin needs
 */


$plugin_meta_confm		= array('name'			=> 'Woo Checkout Field Manager',
							'shortname'		=> 'nm_cofm',
							'path'			=> untrailingslashit(plugin_dir_path( __FILE__ )),
							'url'			=> untrailingslashit(plugin_dir_url( __FILE__ )),
							'db_version'	=> 3.0,
							'logo'			=> plugin_dir_url( __FILE__ ) . 'images/logo.png',
							'menu_position'	=> 99);

/*
 * TODO: change the function name
*/
function get_plugin_meta_cofm(){
	
	global $plugin_meta_confm;
	
	//print_r($plugin_meta);
	
	return $plugin_meta_confm;
}

function nm_personalizedcheckout_pa($arr){
	
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

function get_woo_products(){
		
		
		$products_count = 100;		//apply_filters('product_count', 100);
		$args = array( 'post_type' => 'product', 'posts_per_page' => 5, 'post_status' => 'publish' );

	    $pro_qry = new WP_Query( $args );
	
		//var_dump($pro_qry);
		$prod_array = array();
		if( $pro_qry->have_posts() ) {
		    while ( $pro_qry->have_posts() ) {
				$pro_qry->the_post();
				//echo '<li>' . get_the_title() . '</li>';
				$prod_array[get_the_ID()] = get_the_title();
			}
		}
	
	    wp_reset_query(); 

		return $prod_array;
}


if( !function_exists('nm_wc_add_notice')){
function nm_wc_add_notice($string, $type="error"){
 	
 	global $woocommerce;
 	if( version_compare( $woocommerce->version, 2.1, ">=" ) ) {
 		wc_add_notice( $string, $type );
	    // Use new, updated functions
	} else {
	   $woocommerce->add_error ( $string );
	}
 }
}