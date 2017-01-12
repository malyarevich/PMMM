<?php
/*
	events map admin functions
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoem_admin{

	public function __construct(){
		add_filter('eventon_settings_lang_tab_content', array($this,'evoEM_language_additions'), 10, 1);
		add_filter('eventon_settings_tab1_arr_content', array($this,'evoEM_map_settings'), 10, 1);
	}

	// language settings additinos
	function evoEM_language_additions($_existen){
		$new_ar = array(
				array('type'=>'togheader','name'=>'Event Map Text'),
				array('label'=>'Events at this location','name'=>'evoEM_l1','legend'=>''),
				array('label'=>'No Events Available','name'=>'evoEM_l3'),
				array('label'=>'All Map','name'=>'evoEM_l2',),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
	}

	function evoEM_map_settings($array){
		$array[1]['fields'][] = array('id'=>'evoem', 'type'=>'subheader','name'=>'Event Map Addon Settings');
		$array[1]['fields'][] = array('id'=>'evo_map_markers','type'=>'yesno','name'=>'Show Default Map Markers','legend'=>'This will show default map markers on event map as for some the dynamic marker is not working swtiching to this would be the solution.',);
		$array[1]['fields'][] = array('id'=>'evomap_def_latlon','type'=>'text','name'=>'Default Latitude and longitude for map (separate by comma)','legend'=>'This will be the map location focused when there are no events on map.','default'=>'eg. 123,-234.83');
		$array[1]['fields'][] = array('id'=>'evomap_clusters','type'=>'yesno','name'=>'Disable map clusters','legend'=>'This will stop creating map clusters on the map for multiple map markers',);

		return $array;
	}
}