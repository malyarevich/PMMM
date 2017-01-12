<?php
/**
 * Single Event Related Class
 * @version 2.4.8
 */
class evo_sinevent{
	public function __construct(){
		$this->evo_opt = get_option('evcal_options_evcal_1');
	}
	function page_header(){
		wp_enqueue_style( 'evo_single_event');	
		global $post;
			
		get_header();
	}

	// page content
		function page_content(){
			global $eventon, $post;

			$cal_args = $eventon->evo_generator->shortcode_args;
			$lang = !empty($cal_args['lang'])? $cal_args['lang']:'L1';

			//_onlyloggedin
			$epmv = get_post_meta($post->ID);

			// only loggedin users can see single events
			$onlylogged_cansee = (!empty($this->evo_opt['evosm_loggedin']) && $this->evo_opt['evosm_loggedin']=='yes') ? true:false;
			$thisevent_onlylogged_cansee = (!empty($epmv['_onlyloggedin']) && $epmv['_onlyloggedin'][0]=='yes')? true:false;

			if( (!$onlylogged_cansee || ($onlylogged_cansee && is_user_logged_in() ) ) && 
				( !$thisevent_onlylogged_cansee || $thisevent_onlylogged_cansee && is_user_logged_in())  
			){				
				eventon_get_template_part( 'content', 'single-event' , AJDE_EVCAL_PATH.'/templates');	

			}else{
				echo "<p>".evo_lang('You must login to see this event', $lang)."<br/><a class='button' href=". wp_login_url() ." title='".evo_lang('Login', $lang)."'>".evo_lang('Login', $lang)."</a></p>";
			}
		}
	// sidebar 
		function sidebar(){
			// sidebar
			$opt = $this->evo_opt;
			if(!empty($opt['evosm_1']) && $opt['evosm_1'] =='yes'){
				
				if ( is_active_sidebar( 'evose_sidebar' ) ){

					?>
					<?php //get_sidebar('evose_sidebar'); ?>
					<div class='evo_page_sidebar'>
						<ul id="sidebar">
							<?php dynamic_sidebar( 'evose_sidebar' ); ?>
						</ul>
					</div>
					<?php
				}
			}
		}
		public function has_evo_se_sidebar(){
			return (!empty($this->evo_opt['evosm_1']) && $this->evo_opt['evosm_1'] =='yes')? true: false;
		}

	// redirect script
		function redirect_script(){
			ob_start();
			?>
			<script> 
				href = window.location.href;
				var cleanurl = href.split('#');
				hash =  window.location.hash.substr(1);
				hash_ri = hash.split('=');

				if(hash_ri[1]){
					if(href.indexOf('?') >0){
						redirect = cleanurl[0]+'&ri='+hash_ri[1];
					}else{
						redirect = cleanurl[0]+'?ri='+hash_ri[1];
					}
					window.location.replace( redirect );
				}
			</script>
			<?php

			echo ob_get_clean();
		}

	// get month year for event header
		function get_single_event_header($event_id, $repeat_interval='', $lang='L1'){
			
			$event_datetime = new evo_datetime();
			$pmv = get_post_custom($event_id);

			$adjusted_start_time = $event_datetime->get_int_correct_event_time($pmv,$repeat_interval);					
			$formatted_time = eventon_get_formatted_time($adjusted_start_time);				
			return get_eventon_cal_title_month($formatted_time['n'], $formatted_time['Y'], $lang);
		}
	// get repeat event page header
		function repeat_event_header($ri, $eventid){
			
			$ev_vals = get_post_meta($eventid);

			if( empty($ev_vals['evcal_repeat']) || (!empty($ev_vals['evcal_repeat']) && $ev_vals['evcal_repeat'][0]=='no') ) return false;

			$repeat_intervals = (!empty($ev_vals['repeat_intervals']))? 
							(is_serialized($ev_vals['repeat_intervals'][0])? unserialize($ev_vals['repeat_intervals'][0]): $ev_vals['repeat_intervals'][0] ) :false;		

			// if there are no repeat intervals or only one interval
			if($repeat_intervals && !is_array($repeat_intervals) && (is_array($repeat_intervals) && count($repeat_intervals)==1)) return false;

			$repeat_count = (count($repeat_intervals)-1)   ;
			$date = new evo_datetime();

			$event_permalink = get_permalink($eventid);
			
			echo "<div class='evose_repeat_header'><p><span class='title'>".evo_lang('This is a repeating event'). "</span>";
			echo "<span class='ri_nav'>";

			// previous link
			if($ri>0){ 
				$prev = $date->get_correct_formatted_event_repeat_time($ev_vals, ($ri-1));
				// /print_r($prev);
				$prev_link = $this->get_repeat_event_url($event_permalink, ($ri-1) );
				echo "<a href='{$prev_link}' class='prev' title='{$prev['start_']}'><b class='fa fa-angle-left'></b><em>{$prev['start_']}</em></a>";
			}

			// next link 
			if($ri<$repeat_count){
				$next = $date->get_correct_formatted_event_repeat_time($ev_vals, ($ri+1));
				//print_r($next); 
				$next_link = $this->get_repeat_event_url($event_permalink, ($ri+1) );
				echo "<a href='{$next_link}' class='next' title='{$next['start_']}'><em>{$next['start_']}</em><b class='fa fa-angle-right'></b></a>";
			}
			
			echo "</span><span class='clear'></span></p></div>";
		}

		function get_repeat_event_url($permalink, $ri){
			if(strpos($permalink, '?')!== false){ // ? exists
				return $permalink. '&ri='.$ri;
			}else{
				return $permalink. '?ri='.$ri;
			}
		}


}