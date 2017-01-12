<?php
/**
 * Event Class for one event
 * @version 2.4.10
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_this_event{
	public $event_id;
	public function __construct($event_id){
		$this->event_id = $event_id;
	}

	// Location data for an event
	public function get_location_data(){
		$event_id = $this->event_id;
		$location_terms = wp_get_post_terms($event_id, 'event_location');

		if ( $location_terms && ! is_wp_error( $location_terms ) ){

			$output = array();

			$evo_location_tax_id =  $location_terms[0]->term_id;
			$event_tax_meta_options = get_option( "evo_tax_meta");
			
			// check location term meta values on new and old
			$LocTermMeta = evo_get_term_meta( 'event_location', $evo_location_tax_id, $event_tax_meta_options);
			
			// location name
				$output['name'] = stripslashes( $location_terms[0]->name );

			// description
				if(!empty($location_terms[0]->description))
					$output['description'] = $location_terms[0]->description;

			// meta values
			foreach(array(
				'location_address','location_lat','location_lon','evo_loc_img'
			) as $key){
				if(empty($LocTermMeta[$key])) continue;
				$output[$key] = $LocTermMeta[$key];
			}
			
			return $output;
			
		}else{
			return false;
		}
	}

}