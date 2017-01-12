<?php
/** 
 * front end events map
 * @version 0.1
 */
class evoem_frontend{

	public function __construct(){
		add_action( 'init', array( $this, 'register_styles_scripts' ) , 15);

		add_action('eventon_save_meta', array($this,'evmap_save_meta_values'),10,2);
		add_action('evo_cal_footer',array($this, 'calendar_footer'), 10);

		// calendar header button
		add_filter('evo_cal_above_header_btn', array($this, 'header_allmap_button'), 10, 2);
	}

	//	Styles for the tab page
		public function register_styles_scripts(){
			global $evoemap;
			
			wp_register_style( 'eventon_em_styles',$evoemap->assets_path.'evmap_style.css');
			wp_register_script('eventon_em_infobox',$evoemap->assets_path.'infobox.js', array('jquery'), $evoemap->version, true );
			wp_register_script('evoemap_cluster',$evoemap->assets_path.'js/markerclusterer.js', array('jquery'), $evoemap->version, true );
			wp_register_script('eventon_em_marker',$evoemap->assets_path.'markerwithlabel_packed.js', array('jquery'), $evoemap->version, true );
			wp_register_script('eventon_em_script',$evoemap->assets_path.'evmap_script.js', array('jquery'), $evoemap->version, true );				
			
			
			if(has_eventon_shortcode('add_eventon_evmap')){	
				$this->print_scripts();
			}

			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));		
		}			
		//
		public function print_scripts(){
			wp_enqueue_script('eventon_em_infobox');
			wp_enqueue_script('evoemap_cluster');
			wp_enqueue_script('eventon_em_marker');
			wp_enqueue_script('eventon_em_script');
		}
		function print_styles(){
			wp_enqueue_style( 'eventon_em_styles');	
		}

	// include focus in header section
		function header_allmap_button($array, $args){
			if(!empty($args['focusmap']) && $args['focusmap']=='yes'){
				$new['evo-mapfocus']='All Map';
				$array = array_merge($new, $array);
			}
			return $array;
		}

	//	MAIN function to generate the calendar outter shell
		public function generate_evo_em($args){
		
			global $eventon, $wpdb;	
			
			$this->front_end_init($args);
			$this->only_em_actions();
			$this->is_running_em = true;
			$this->print_scripts();	

			$fdd = $this->this_cal;
			//print_r($fdd);
			
			ob_start();
				if($args['map_type']=='upcoming'){
					echo $eventon->evo_generator->get_calendar_header(array(
						'month'=>$fdd['month'], 
						'year'=>$fdd['year'],
						'date_header'=>false,
						'range_start'=>$fdd['focus_start_date_range'],
						'range_end'=>$fdd['focus_end_date_range'],
						'header_title'=> (!empty($args['map_title'])?$args['map_title']:'') ,
						'send_unix'=>true,
						'_html_evcal_list'=>false,
						'_html_sort_section'=>true
						)
					);
				}else{
					echo $eventon->evo_generator->get_calendar_header(array(
						'focused_month_num'=>$fdd['month'], 
						'focused_year'=>$fdd['year'],
						'_html_evcal_list'=>false,
						'_html_sort_section'=>true
						)
					);
				}

				// map section
				echo $this->append_map_section($args);

				// calendar events
				$months_event_array = $eventon->evo_generator->generate_event_data( 
					$this->events_list, 
					$fdd['focus_start_date_range']
				);
				
				echo $eventon->evo_generator->evo_process_event_list_data($months_event_array, $args);
				echo "</div>";

				echo $eventon->evo_generator->calendar_shell_footer();
				if($args['map_type']=='upcoming'){
					echo '<a class="evo-mapfocus evo_btn">'. evo_lang_get('evoEM_l2','All Map').'</a>';
				}

			$this->remove_only_em_actions();
			return ob_get_clean();
			
		}
		
		function front_end_init($args, $type=''){
			global $eventon, $wpdb;	

			$current_timestamp =  current_time('timestamp');

			// upcoming events list
			if($args['map_type']=='upcoming'){
				$number_of_months = !empty($shortcode['number_of_months'])? $shortcode['number_of_months']:12;
				$month_dif = '+';
				$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

				$focused_month_num = ($number_of_months>0)?				
					date('n',  $unix_dif):
					date('n',$current_timestamp);

				$focused_year = ($number_of_months>0)?				
					date('Y', $unix_dif):
					date('Y',$current_timestamp);	
				$restrain_day = date('t', mktime(0, 0, 0, $focused_month_num+1, 0, $focused_year));
				$focus_start_date_range = $current_timestamp;
				$focus_end_date_range =  mktime(23,59,59,($focused_month_num),$restrain_day, ($focused_year));
			}else{
			// default month view of event map
				$month_incre = (!empty($args['month_incre']))?$args['month_incre']:0;

				// calendar focus date range
					$focused_month_num = (!empty($args['fixed_month']))?
						$args['fixed_month']:
						date('n', strtotime($month_incre.' month', $current_timestamp) );

					$focused_year = (!empty($args['fixed_year']))?
						$args['fixed_year']:
						date('Y', strtotime($month_incre.' month', $current_timestamp) );

					// load entire month of events on load or not
					$end_day = $this->days_in_month( $focused_month_num, $focused_year);
					$start_day = 1;

					// DAY RANGES
					$focus_start_date_range = mktime( 0,0,0,$focused_month_num,$start_day,$focused_year );
					$focus_end_date_range = mktime(23,59,59,($focused_month_num),$end_day, ($focused_year));
					
			}

			// ONLY this cal
			$this->this_cal['month'] = $focused_month_num;
			$this->this_cal['year'] = $focused_year;
			$this->this_cal['focus_start_date_range'] = $focus_start_date_range;
			$this->this_cal['focus_end_date_range'] = $focus_end_date_range;

			// Add extra arguments to shortcode arguments
			$new_arguments = array(
				'focus_start_date_range'=>$this->this_cal['focus_start_date_range'],
				'focus_end_date_range'=>$this->this_cal['focus_end_date_range'],
			);
			

			$args = (!empty($args) && is_array($args))? array_merge($args, $new_arguments): $new_arguments;
			// PROCESS arguments for WP_Query to get events list
			$args__ = $eventon->evo_generator->process_arguments($args, true);
			$this->shortcode_args=$args__;


			// load events lists for given arguments
			$this->events_list = $eventon->evo_generator->evo_get_wp_events_array('', $args__);

		}
		function calendar_footer(){
			$this->is_running_em=false;
		}	
	
	
	//	Calendar with map of events
		public function append_map_section($args){			
			global $evoemap;
			
			$evOpt = get_option('evcal_options_evcal_1');
			
			ob_start();

				$locations = $this->get_locations_list();

				$show_alle = (!empty($args['show_alle']) && $args['show_alle']=='yes')? 'yes':'no';
				$loc_page = (!empty($args['loc_page']) && $args['loc_page']=='yes')? 'yes':'no';
				$lightbox = (!empty($args['lightbox']) && $args['lightbox']=='yes')? 'yes':'no';
				
				// check default markers set if not get marker url
				$marker_url = (!empty($evOpt['evo_map_markers']) && $evOpt['evo_map_markers']=='yes')?
					'def': urlencode($evoemap->addon_data['plugin_url']);
				$disableCluster = (!empty($evOpt['evomap_clusters']) && $evOpt['evomap_clusters']=='yes')? 'yes':'no';

				// default lat long
					$latlon = !empty($evOpt['evomap_def_latlon'])? $evOpt['evomap_def_latlon']: '45.523062,-122.676482';
					$latlon = str_replace(' ', '', $latlon);
					$latlon = explode(',', $latlon);

				//echo "<div id='eventon_loadbar_section'><div id='eventon_loadbar'></div></div>";
				
				$mapID = rand(10,40);

				echo "<div class='evomap_section'>";

				echo "<div class='evomap_progress'><span></span></div>";

				echo "<div id='evoGEO_map_".$mapID."' class='evoGEO_map' ".( (!empty($this->shortcode_args['map_height']))? 'style="height:'.$this->shortcode_args['map_height'].'px;"':null )." data-clusters='{$disableCluster}'></div>				
				<p class='evomap_noloc' style='display:none'>".evo_lang_get('evoEM_l3','No Events Available')."</p>
				<div class='evoGEO_locations' style='display:none;' data-txt='".evo_lang_get('evoEM_l1','Events at this location')."' data-markerurl='{$marker_url}' data-count='{$locations['count']}' data-locurl='".site_url()."' data-loclink='".$loc_page."' data-filepath='{$evoemap->plugin_url}/assets/images/m' data-dlat='{$latlon[0]}' data-dlon='{$latlon[1]}'>".$locations['content']."</div>
					<div class='evomap_debug' style='display:none'></div>
				</div>";
				
				echo "<div class='evoEM_list' data-showe='{$show_alle}' data-lightbox='{$lightbox}'>";
				echo "<div id='evcal_list' class='eventon_events_list evoEM'>";
				
			return ob_get_clean();
		}
			
		// get locations list
			public function get_locations_list(){

				$events = $this->events_list;

				$locations = array();
					$count = 0;

					// go through all the event on hand
					foreach($events as $event){
						
						$pmv = $event['event_pmv'];
						$ri = !empty($event['event_repeat_interval']) ? $event['event_repeat_interval']:0;
						
						// location taxonomy 
						 	$evo_location_tax_id = (!empty($pmv['evo_location_tax_id']))? $pmv['evo_location_tax_id'][0]: false;

						if(!$evo_location_tax_id) continue; // skip is no location taxonomy ID

						// get location taxonomy data
							$LOCATIONterm = get_term_by('id',$evo_location_tax_id, 'event_location');

						if(!$LOCATIONterm) return false;

							$LOCMETA = get_option( "taxonomy_$evo_location_tax_id" );
					

						if(!empty($LOCMETA['location_lon']) && !empty($LOCMETA['location_lat']) ){
							$key = $LOCMETA['location_lat'].$LOCMETA['location_lon']; // array key

							// if there is a repeating instance
							if( isset($locations[$key]) && in_array( $event['event_id'], $locations[$key]['events']) ){
								$ri_addition = !empty($locations[$key]['ri'])? $locations[$key]['ri']+1: 1;
								$locations[$key]['ri']= $ri_addition;
							}	

							if( 
								(!empty($locations[$key]['events']) && !in_array( $event['event_id'], $locations[$key]['events']) )
								|| empty($locations[$key]) 
							){

								$eventids = !empty($locations[$key]['events'])? $locations[$key]['events']: array();
								$eventids[] = $event['event_id'];

								// location type
									//$location_type = (!empty($loc_lan) && !empty($loc_lon) )? 'lanlat':(!empty($loc_add)? 'address': false);
									$location_type = 'latlng';

								// location address
								$coordinates = $LOCMETA['location_lat'].','.$LOCMETA['location_lon'];
								$address = !empty($LOCMETA['location_address'])? $LOCMETA['location_address']: '';
								$name = $LOCATIONterm->name;

								$locations[$key] = array(
									'events'=>$eventids,
									'coordinates'=>$coordinates,
									'address'=>$address,
									'name'=>$name,
								);
								$count ++;
							}													
						}
					}// endforeach

					// /print_r($locations);
					$count = 0;
					foreach($locations as $ll){
						$locations_[$count] = $ll;
						$count++;
					}

					ob_start();

					if(!empty($locations_))
						echo json_encode($locations_);

					// go through all the locations
					/*foreach($locations as $location){
						// location type
						$location_type = $location['type'];
						$locationData = $location['locationData'];
						$ids = implode(',',$location['events']);
						echo "<p data-eventids='{$ids}' data-location_name='".($location['name'])."' data-locationData='{$locationData}' data-type='{$location_type}'></p>";
					}*/

				return array('content'=>ob_get_clean(), 'count'=>$count);

			}
		
	//	Save the location slug when event data is saved
		public function evmap_save_meta_values($fields, $post_id){
			global $post;
			
			if(!empty($_POST[ 'evcal_location']) ){
				$location_slug = sanitize_title($_POST[ 'evcal_location' ]);
				update_post_meta( $post->ID, 'evcal_location_slug', $location_slug);
			}else{
				delete_post_meta( $post_id, 'evcal_location_slug');
			}
			
		}

	// SUPPORT FUNCTIONS
		// ONLY for EM calendar actions 
		public function only_em_actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);		
			
		}
		public function remove_only_em_actions(){
			//add_filter('eventon_cal_class', array($this, 'remove_eventon_cal_class'), 10, 1);	
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));
			
		}

		// add class name to calendar header for EM
		function eventon_cal_class($name){
			$name[]='eventmap';
			return $name;
		}
		// remove class name to calendar header for EM
		function remove_eventon_cal_class($name){
			if(($key = array_search('eventmap', $name)) !== false) {
			    unset($name[$key]);
			}
			return $name;
		}

	// return fays in given month
		function days_in_month($month, $year) { 
			return date('t', mktime(0, 0, 0, $month+1, 0, $year)); 
		}
	// Append associative array elements
		function array_push_associative(&$arr) {
		   $ret='';
		   $args = func_get_args();
		   foreach ($args as $arg) {
			   if (is_array($arg)) {
				   foreach ($arg as $key => $value) {
					   $arr[$key] = $value;
					   $ret++;
				   }
			   }else{
				   $arr[$arg] = "";
			   }
		   }
		   return $ret;
		}

}