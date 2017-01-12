<?php
/**
 * EVO_Shortcodes class.
 *
 * @class 		EVO_Shortcodes
 * @version		2.4
 * @package		EventON/Classes
 * @category	Class
 * @author 		AJDE
 */

class EVO_Shortcodes {
	public function __construct(){
		// regular shortcodes
		add_shortcode('add_ajde_evcal',array($this,'eventon_show_calendar'));	// for eventon ver < 2.0.8	
		add_shortcode('add_eventon',array($this,'eventon_show_calendar'));
		add_shortcode('add_eventon_list',array($this,'events_list'));		
		add_shortcode('add_eventon_tabs',array($this,'eventon_tabs'));	
		add_shortcode('add_single_eventon', array($this,'single_event_box'));	
	}	
	
	// Tab view for eventon calendar
		function eventon_tabs($atts){
			$defaults = array(
				'tab1'=>'Calendar View',
				'tab1shortcode'=>'add_eventon'
			);
			$args = array_merge($defaults, $atts);

			ob_start();
			echo "<div class='evo_tab_view'>";
			echo "<ul class='evo_tabs'>";
			for($x=1; $x<=4; $x++){
				if(empty($args['tab'.$x]) || empty($args['tab'.$x.'shortcode'])) continue;

				echo "<li class='evo_tab ". ($x==1? 'selected':'')."' data-tab='tab_".'tab'.$x."'>".$args['tab'.$x]."</li>";
			}
			echo "</ul>";

			echo "<div class='evo_tab_container'>";
			for($x=1; $x<=4; $x++){
				if(empty($args['tab'.$x]) || empty($args['tab'.$x.'shortcode'])) continue;

				echo "<div class='evo_tab_section ". ($x==1?'visible':'') ." tab_".'tab'.$x."'>";
				$shortcode = '['. $args['tab'.$x.'shortcode'] . ']';
				
				echo do_shortcode($shortcode);
				echo "</div>";
			}
			echo "</div>";
			return ob_get_clean();
		}

	/*	Show multiple month calendar */
		public function events_list($atts){
			
			global $eventon;
			
			add_filter('eventon_shortcode_defaults', array($this,'event_list_shortcode_defaults'), 10, 1);
			
			// connect to support arguments
			$supported_defaults = $eventon->evo_generator->get_supported_shortcode_atts();
			
			$args = shortcode_atts( $supported_defaults, $atts ) ;	
						
			// OUT PUT	
			// check if member only calendar
			if($eventon->frontend->is_member_only($args) ){			
				ob_start();				
				echo $eventon->evo_generator->generate_events_list($args);			
				return ob_get_clean();		
			}else{
				echo $eventon->frontend->nonMemberCalendar();
			}	
		}
	
	// add new default shortcode arguments
		public function event_list_shortcode_defaults($arr){		
			return array_merge($arr, array(
				'hide_empty_months'=>'no',
				'show_year'=>'no',
			));		
		}
	
	/** Show single month calendar shortcode */
		public function eventon_show_calendar($atts){
			global $eventon;
			
			// connect to support arguments
			$supported_defaults = apply_filters('eventon_shortcode_default_values', $eventon->evo_generator->shell->get_supported_shortcode_atts());	
			
			$args = shortcode_atts( $supported_defaults, $atts ) ;				
			$args = apply_filters('eventon_shortcode_argument_update', $args);	
			
			// OUT PUT
			
			// check if member only calendar
			if($eventon->frontend->is_member_only($args) ){			
				ob_start();				
				echo $eventon->evo_generator->eventon_generate_calendar($args);			
				return ob_get_clean();
			}else{
				echo $eventon->frontend->nonMemberCalendar();
			}
		}

	// single events
		function single_event_box($atts){
			global $eventon;

			add_filter('eventon_shortcode_defaults', array($this,'evoSE_add_shortcode_defaults'), 10, 1);
			$supported_defaults = $eventon->evo_generator->shell->get_supported_shortcode_atts();	

			$args = shortcode_atts( $supported_defaults, $atts ) ;
			//print_r($args);
			
			if(empty($args['id'])) return false; // when the id value was not passed


			// user interaction for this event box
				$ev_uxval = 4; // default open as event page
				$external_url = '';
				if( $args['open_as_popup']=='yes' || $args['ev_uxval']==3){
					$ev_uxval = 3;
					$args['show_exp_evc'] = 'no';// override expended event card
				}elseif(  $args['ev_uxval']=='X'){
					$ev_uxval = 'X';
				}elseif(  $args['ev_uxval']=='2' && !empty($args['ext_url'])){// external link
					$ev_uxval = '2';
					$external_url = $args['ext_url'];
				}elseif(  $args['ev_uxval']=='1' ){// slidedown
					$ev_uxval = 1;
				}

				// update calendar ux_val to 4 so eventcard HTML content will not load on eventbox
				if( ($ev_uxval==3 && $args['show_exp_evc']!='no') || $ev_uxval==1){}else{
					$eventon->evo_generator->process_arguments(array('ux_val'=>4));	
				}
					

			$eventon->evo_generator->is_eventcard_hide_forcer= true;
			//$eventon->evo_generator->is_eventcard_hide_forcer= false;
			$opt = $eventon->evo_generator->evopt1;

				// google map variables
				$evcal_gmap_format = ($opt['evcal_gmap_format']!='')?$opt['evcal_gmap_format']:'roadmap';	
				$evcal_gmap_zooml = ($opt['evcal_gmap_zoomlevel']!='')?$opt['evcal_gmap_zoomlevel']:'12';	
					
				$evcal_gmap_scrollw = (!empty($opt['evcal_gmap_scroll']) && $opt['evcal_gmap_scroll']=='yes')?'false':'true';				
			// get individual event content from calendar generator function
				$modified_event_ux = ($args['show_exp_evc']=='yes'  )? null: 4;
				$event = $eventon->evo_generator->get_single_event_data(
					$args['id'], 
					$args['lang'],
					$args['repeat_interval'],
					$args
				);
			
			// other event box variables
			$ev_excerpt = ($args['show_excerpt']=='yes')? "data-excerpt='1'":null;
			$ev_expand = ($args['show_exp_evc']=='yes')? "data-expanded='1'":null;
			
			ob_start();
				
			echo "<div class='ajde_evcal_calendar eventon_single_event eventon_event ' >";
			echo "<div class='evo-data' ".$ev_excerpt." ".$ev_expand." data-ux_val='{$ev_uxval}' data-exturl='{$external_url}' data-mapscroll='".$evcal_gmap_scrollw."' data-mapformat='".$evcal_gmap_format."' data-mapzoom='".$evcal_gmap_zooml."' ></div> ";
			echo "<div id='evcal_list' class='eventon_events_list ".($ev_uxval=='X'?'noaction':null)."'>";
			echo $event[0]['content'];
			echo "</div></div>";
				
			
			return ob_get_clean();
		}
		// add new default shortcode arguments
		function evoSE_add_shortcode_defaults($arr){			
			return array_merge($arr, array(
				'id'=>0,
				'show_excerpt'=>'no',
				'show_exp_evc'=>'no',
				'open_as_popup'=>'no',
				'ev_uxval'=>4,
				'repeat_interval'=>0,
				'ext_url'=>''
			));			
		}
}