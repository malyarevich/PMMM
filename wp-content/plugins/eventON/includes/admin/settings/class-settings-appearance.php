<?php
/**
 * Appearance settings for eventon
 * @version 2.4.8
 */

class evoadmin_set_appearance{
	function __construct($evcal_opt)	{
		$this->evcal_opt = $evcal_opt;
	}
	function get(){
		return apply_filters('eventon_appearance_add', 
			array(
				array('id'=>'evo_notice_1','type'=>'notice','name'=>sprintf(__('Once you make changes to appearance make sure to clear browser and website cache to see results. <br/>Can not find appearance? <a href="%s" target="_blank">See how you can add custom styles to change additional appearances</a>','eventon'),'http://www.myeventon.com/documentation/change-css-calendar/') )
				
				,array('id'=>'evoapp_code_1', 'type'=>'customcode','code'=>$this->appearance_theme_selector(), )
				,array('id'=>'fc_mcolor','type'=>'multicolor','name'=>__('Multiple colors','eventon'),
					'variations'=>array(
						array('id'=>'evcal_hexcode', 'default'=>'206177', 'name'=>__('Primary Calendar Color','eventon')),
						array('id'=>'evcal_header1_fc', 'default'=>'C6C6C6', 'name'=>'Header Month/Year text color'),
						array('id'=>'evcal__fc2', 'default'=>'ABABAB', 'name'=>'Calendar Date color'),
					)
				),
				array('id'=>'evcal_font_fam','type'=>'text','name'=>__('Primary Calendar Font family <i>(Note: type the name of the font that is supported in your website. eg. Arial)</i>','eventon')),
				array('id'=>'evcal_arrow_hide','type'=>'yesno','name'=>__('Hide month navigation arrows','eventon'), 'legend'=>'You can also hide individual calendar navigation arrows via shortcode variable hide_arrows="yes"'),
				array('id'=>'evo_arrow_right','type'=>'yesno','name'=>__('Align month navigation arrows to rightside of the calendar','eventon'),'legend'=>'This will align the month navigation arrows to the right side border of the calendar as oppose to next to month title text.'),
			

				// Calendar Header
				array('id'=>'evcal_fcx','type'=>'hiddensection_open','name'=>__('Calendar Header','eventon'), 'display'=>'none'),
				array('id'=>'fs_sort_options','type'=>'fontation','name'=>__('Sort Options Text','eventon'),
					'variations'=>array(
						array('id'=>'evcal__sot', 'name'=>'Default State', 'type'=>'color', 'default'=>'B8B8B8'),
						array('id'=>'evcal__sotH', 'name'=>'Hover State', 'type'=>'color', 'default'=>'d8d8d8'),
					)
				),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Jump Months Trigger Button','eventon'),
					'variations'=>array(
						array('id'=>'evcal__jm001', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
						array('id'=>'evcal__jm002', 'name'=>'Background Color', 'type'=>'color', 'default'=>'ADADAD'),
						array('id'=>'evcal__jm001H', 'name'=>'Text Color (Hover)', 'type'=>'color', 'default'=>'ffffff'),
						array('id'=>'evcal__jm002H', 'name'=>'Background Color (Hover)', 'type'=>'color', 'default'=>'d3d3d3'),						
					)
				),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Jumper - Month/Year Buttons','eventon'),
					'variations'=>array(
						array('id'=>'evcal__jm003', 'name'=>'Text Color', 'type'=>'color', 'default'=>'a0a09f'),
						array('id'=>'evcal__jm004', 'name'=>'Background Color', 'type'=>'color', 'default'=>'f5f5f5'),
						array('id'=>'evcal__jm003H', 'name'=>'Text Color (Hover)', 'type'=>'color', 'default'=>'a0a09f'),
						array('id'=>'evcal__jm004H', 'name'=>'Background Color (Hover)', 'type'=>'color', 'default'=>'e6e6e6'),							
					)
				),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Jumper - Month/Year Buttons: Current','eventon'),
					'variations'=>array(
						array('id'=>'evcal__jm006', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
						array('id'=>'evcal__jm007', 'name'=>'Background Color', 'type'=>'color', 'default'=>'CFCFCF'),
					)
				),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Jumper - Month/Year Buttons: Active','eventon'),
					'variations'=>array(
						array('id'=>'evcal__jm008', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
						array('id'=>'evcal__jm009', 'name'=>'Background Color', 'type'=>'color', 'default'=>'f79191'),
					)
				),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Current month Button','eventon'),
					'variations'=>array(
						array('id'=>'evcal__thm001', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
						array('id'=>'evcal__thm002', 'name'=>'Background Color', 'type'=>'color', 'default'=>'ADADAD'),
						array('id'=>'evcal__thm001H', 'name'=>'Text Color (Hover)', 'type'=>'color', 'default'=>'ffffff'),
						array('id'=>'evcal__thm002H', 'name'=>'Background Color (Hover)', 'type'=>'color', 'default'=>'d3d3d3'),						
					)
				),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Arrow Circle','eventon'),
					'variations'=>array(
						array('id'=>'evcal__jm010', 'name'=>'Line Color', 'type'=>'color', 'default'=>'e2e2e2'),
						array('id'=>'evcal__jm011', 'name'=>'Background Color', 'type'=>'color', 'default'=>'ffffff'),
						array('id'=>'evcal__jm010H', 'name'=>'Line Color (Hover)', 'type'=>'color', 'default'=>'e2e2e2'),
						array('id'=>'evcal__jm011H', 'name'=>'Background Color (Hover)', 'type'=>'color', 'default'=>'ededed'),
						array('id'=>'evcal__jm01A', 'name'=>'The arrow color', 'type'=>'color', 'default'=>'e2e2e2'),
						array('id'=>'evcal__jm01AH', 'name'=>'The arrow color (Hover)', 'type'=>'color', 'default'=>'ffffff'),
					)
				),				
				array('id'=>'evcal_ftovrr','type'=>'hiddensection_close'),


				// event top
				array('id'=>'evcal_fcx','type'=>'hiddensection_open','name'=>__('EventTop Styles','eventon'), 'display'=>'none'),
					array('id'=>'evcal__fc3','type'=>'color','name'=>__('Event Title font color','eventon'), 'default'=>'6B6B6B'),
					array('id'=>'evcal__fc3st','type'=>'color','name'=>__('Event Sub Title font color','eventon'), 'default'=>'6B6B6B'),
					array('id'=>'evcal__fc6','type'=>'color','name'=>__('Text under event title (on EventTop. Eg. Time, location etc.)','eventon'),'default'=>'8c8c8c'),
					array('id'=>'evcal__fc7','type'=>'color','name'=>__('Category title color (eg. Event Type)','eventon'),'default'=>'c8c8c8'),
					array('id'=>'evcal__evcbrb0','type'=>'color','name'=>__('Event Top Border Color','eventon'), 'default'=>'e5e5e5'),

					array('id'=>'fs_fonti','type'=>'fontation','name'=>__('Background Color','eventon'),
						'variations'=>array(
							array('id'=>'evcal__bgc4', 'name'=>'Default State', 'type'=>'color', 'default'=>'fafafa'),
							array('id'=>'evcal__bgc4h', 'name'=>'Hover State', 'type'=>'color', 'default'=>'f4f4f4'),
							array('id'=>'evcal__bgc5', 'name'=>'Featured Event - Default State', 'type'=>'color', 'default'=>'F9ECE4'),
							array('id'=>'evcal__bgc5h', 'name'=>'Featured Event - Hover State', 'type'=>'color', 'default'=>'FAE4D7'),
						)
					),
					
					array('id'=>'fs_eventtop_tag','type'=>'fontation','name'=>__('General EventTop Tags','eventon'),
						'variations'=>array(
							array('id'=>'fs_eventtop_tag_1', 'name'=>'Background-color', 'type'=>'color', 'default'=>'F79191'),
							array('id'=>'fs_eventtop_tag_2', 'name'=>'Font Color', 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_cancel_event','type'=>'fontation','name'=>__('Canceled Events Tag','eventon'),
						'variations'=>array(
							array('id'=>'evcal__cancel_event_1', 'name'=>'Background-color', 'type'=>'color', 'default'=>'F79191'),
							array('id'=>'evcal__cancel_event_2', 'name'=>'Font Color', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__cancel_event_3', 'name'=>'Background Strips Color 1', 'type'=>'color', 'default'=>'FDF2F2'),
							array('id'=>'evcal__cancel_event_4', 'name'=>'Background Strips Color 2', 'type'=>'color', 'default'=>'FAFAFA'),
						)
					),
					array('id'=>'fs_eventtop_cmd','type'=>'fontation','name'=>__('Custom Field Buttons','eventon'),
						'variations'=>array(
							array('id'=>'evoeventtop_cmd_btn', 'name'=>'Background-color', 'type'=>'color', 'default'=>'237dbd'),
							array('id'=>'evoeventtop_cmd_btnA', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
						)
					),
				array('id'=>'evcal_fcx','type'=>'hiddensection_close',),
				

				// eventCard Styles
				array('id'=>'evcal_fcxx','type'=>'hiddensection_open','name'=>__('EventCard Styles','eventon'), 'display'=>'none'),
				array('id'=>'fs_fonti1','type'=>'fontation','name'=>'Section Title Text',
					'variations'=>array(
						array('id'=>'evcal__fc4', 'type'=>'color', 'default'=>'6B6B6B'),
						array('id'=>'evcal_fs_001', 'type'=>'font_size', 'default'=>'18px'),
					)
				),
				array('id'=>'evcal__fc5','type'=>'color','name'=>__('General Font Color','eventon'), 'default'=>'656565'),
				array('id'=>'evcal__bc1','type'=>'color','name'=>'Event Card Background Color', 'default'=>'eaeaea', 'rgbid'=>'evcal__bc1_rgb'),			
				array('id'=>'evcal__bc1H','type'=>'color','name'=>'Event Card Background Color (Hover on clickable section)', 'default'=>'d8d8d8'),			
				array('id'=>'evcal__evcbrb','type'=>'color','name'=>'Event Card Border Color', 'default'=>'cdcdcd'),

					// get direction fiels
					array('id'=>'evcal_fcx','type'=>'subheader','name'=>__('Get Directions Field','eventon')),
					array('id'=>'fs_fonti3','type'=>'fontation','name'=>__('Get Directions','eventon'),
						'variations'=>array(
							array('id'=>'evcal_getdir_001', 'name'=>'Background Color', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal_getdir_002', 'name'=>'Text Color', 'type'=>'color', 'default'=>'888888'),
							array('id'=>'evcal_getdir_003', 'name'=>'Button Icon Color', 'type'=>'color', 'default'=>'858585'),
						)
					),			

					array('id'=>'evcal_fcx','type'=>'subheader','name'=>__('Buttons','eventon')),
					array('id'=>'fs_fonti3','type'=>'fontation','name'=>__('Button Color','eventon'),
						'variations'=>array(
							array('id'=>'evcal_gen_btn_bgc', 'name'=>'Default State', 'type'=>'color', 'default'=>'237ebd'),
							array('id'=>'evcal_gen_btn_bgcx', 'name'=>'Hover State', 'type'=>'color', 'default'=>'237ebd'),
						)
					),array('id'=>'fs_fonti4','type'=>'fontation','name'=>__('Button Text Color','eventon'),
						'variations'=>array(
							array('id'=>'evcal_gen_btn_fc', 'name'=>'Default State', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal_gen_btn_fcx', 'name'=>'Hover State', 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_fonti5','type'=>'fontation','name'=>__('Close Button Color','eventon'),
						'variations'=>array(
							array('id'=>'evcal_closebtn', 'name'=>'Default State', 'type'=>'color', 'default'=>'eaeaea'),
							array('id'=>'evcal_closebtnx', 'name'=>'Hover State', 'type'=>'color', 'default'=>'c7c7c7'),
						)
					),
					array('id'=>'evcal_fcx','type'=>'hiddensection_close',),

					// featured events
					array('id'=>'evcal_fcx','type'=>'subheader','name'=>__('Featured Events','eventon')),
					array('id'=>'evo_fte_override','type'=>'yesno','name'=>'Override featured event color','legend'=>'This will override the event color you chose for featured event with a different color.','afterstatement'=>'evo_fte_override'),
					array('id'=>'evo_fte_override','type'=>'begin_afterstatement'),
						array('id'=>'evcal__ftec','type'=>'color','name'=>'Featured event left bar color', 'default'=>'ca594a'),
					array('id'=>'evcal_ftovrr','type'=>'end_afterstatement'),

				// single events
				array('id'=>'evose','type'=>'hiddensection_open','name'=>__('Social Media Styles','eventon'), 'display'=>'none'),
				array('id'=>'evose','type'=>'fontation','name'=>'Social Media Icons',
				'variations'=>array(
					array('id'=>'evose_1', 'name'=>'Icon Color','type'=>'color', 'default'=>'888686'),			
					array('id'=>'evose_3', 'name'=>'Icon Box Color','type'=>'color', 'default'=>'eaeaea'),
					array('id'=>'evose_2', 'name'=>'Icon Color (:Hover)','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evose_4', 'name'=>'Icon Box Color (:Hover)','type'=>'color', 'default'=>'9e9e9e'),
					array('id'=>'evose_5', 'name'=>'Icon right border Color','type'=>'color', 'default'=>'cdcdcd')
					,				
				)),
				array('id'=>'evose','type'=>'hiddensection_close','name'=>'Social Media Styles')
			)
		);
	}

	function appearance_theme_selector(){			
		ob_start();

			echo  '<h4 class="acus_header">'.__('Calendar Themes','eventon').'</h4>
			<input id="evo_cal_theme" name="evo_cal_theme" value="'.( (!empty($this->evcal_opt[1]['evo_cal_theme']))? $this->evcal_opt[1]['evo_cal_theme']:null).'" type="hidden"/>
			<div id="evo_theme_selection">';

			// scan for themes
			$dir = AJDE_EVCAL_PATH.'/themes/';				
			$a = scandir($dir);
			
			$themes =$the = '';
			foreach($a as $file){
				if($file!= '.' && $file!= '..'){
					$base = basename($file,'.php');
					$themes[$base] = $file;
					if(file_exists($dir.$file)){
						include_once($dir.$file);
						$the[] = array('name'=>$base, 'content'=>$theme);
					}
				}
			}


				echo "<p id='evo_themejson' style='display:none'>".json_encode($the)."</p>";
				$evo_theme_current =  !empty($this->evcal_opt[1]['evo_theme_current'])? $this->evcal_opt[1]['evo_theme_current']: 'default';

			?>
				<p class='evo_theme_selection'><?php _e('Current Theme:','eventon');?> <b><select name='evo_theme_current'>
					<option value='default'><?php _e('Default','eventon');?></option>
					<?php
						if(!empty($themes)){
							foreach($themes as $base=>$theme){
								echo "<option value='{$base}' ". ($base==$evo_theme_current? "selected='selected'":null).">".$base.'</option>';
							}
						}
					?>
				</select></b>
					<span class='evo_theme'>
						<span name='evcal__fc2' style='background-color:#<?php echo $this->colr('evcal__fc2','ABABAB' );?>' data-default='ABABAB'></span>
						<span name='evcal_header1_fc' style='background-color:#<?php echo $this->colr('evcal_header1_fc','C6C6C6' );?>' data-default='C6C6C6'></span>
						<span name='evcal__bgc4' style='background-color:#<?php echo $this->colr('evcal__bgc4','fafafa' );?>' data-default='fafafa'></span>
						<span name='evcal__fc3' style='background-color:#<?php echo $this->colr('evcal__fc3','6B6B6B' );?>' data-default='6B6B6B'></span>
						<span name='evcal__jm010' style='background-color:#<?php echo $this->colr('evcal__jm010','e2e2e2' );?>' data-default='e2e2e2'></span>
					</span>
				</p>
				
				<p style='clear:both'><i><?php _e('Themes are in <strong>Beta stage</strong> and we are working on it & addin more themes.','eventon');?></i></p>
				<p style='clear:both'><i><strong><?php _e('NOTE:','eventon');?></strong> <?php _e('After changing theme make sure to click "save changed"','eventon');?></i></p>
	
			<?php

			echo '</div>';

		return ob_get_clean();
	}
	// get options
		private function colr($var, $def){
			return (!empty($this->evcal_opt[1][$var]))? $this->evcal_opt[1][$var]: $def;
		}
}