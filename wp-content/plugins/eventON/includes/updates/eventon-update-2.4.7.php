<?php
/**
 * Update EVO to 2.4.7
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon/Admin/Updates
 * @version     2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $eventon;

// save location & organizer term meta into one meta field
	
	$options = get_option( "evo_tax_meta");
	foreach(array(
		'event_location','event_organizer'
	) as $tax){
		$terms = get_terms($tax, array(
			'hide_empty' => false
		));
		$debug = '';
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
			foreach($terms as $term){
				$termid = $term->term_id;

				if(empty($termid)) continue;

				$termmeta = get_option( "taxonomy_".$termid);
				
				if(!empty($termmeta)){
					evo_save_term_metas($tax,$termid, $termmeta,'' );
					delete_option('taxonomy_'.$term->term_id);
				}
			}
		}
	}