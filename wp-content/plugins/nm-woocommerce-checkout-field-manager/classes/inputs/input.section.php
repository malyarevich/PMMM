<?php
/*
 * Followig class handling text input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Section_cofm extends NM_Inputs_cofm{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	
	/*
	 * check if section is started
	 */
	var $is_section_stared;
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		$this -> plugin_meta = get_plugin_meta_cofm();
		
		$this -> title 		= __ ( 'HTML Block', 'nm-cofm' );
		$this -> desc		= __ ( 'Add html/text', 'nm-cofm' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
						'title' => array (
								'type' => 'text',
								'title' => __ ( 'Title', 'nm-cofm' ),
								'desc' => __ ( 'It will as section heading wrapped in h2', 'nm-cofm' ) 
						),
						'description' => array (
								'type' => 'textarea',
								'title' => __ ( 'Description', 'nm-cofm' ),
								'desc' => __ ( 'Type description, it will be diplay under section heading.', 'nm-cofm' ) 
						),
						'product_visibility' => array (
								'type' => 'text',
								'title' => __ ( 'Visible this field only for Product specified', 'nm-cofm' ),
								'desc' => __ ( 'Provide list of Product IDs separated by commas. Leave it blank for default', 'nm-cofm' ) 
						),
						'category_visibility' => array (
								'type' => 'text',
								'title' => __ ( 'Visible this field only for Category specified', 'nm-cofm' ),
								'desc' => __ ( 'Provide list of Category IDs separated by commas. Leave it blank for default', 'nm-cofm' ) 
						),
						'user_visibility' => array (
								'type' => 'text',
								'title' => __ ( 'Visible this field only for User Role specified', 'nm-cofm' ),
								'desc' => __ ( 'Provide list of User Roles separated by commas e.g: customer,author. Leave it blank for default', 'nm-cofm' ) 
						),
				);
	
	}
	
	
	/*
	 * @params: args
	*/
	function render_input($args, $content=""){
		
		
		$_html =  '<section id="'.$args['id'].'">';
		$_html .= '<div style="clear: both"></div>';
		
		$_html .= '<header class="webcontact-section-header">';
		$_html .= '<h2>'. stripslashes( $args['title'] ).'</h2>';
		$_html .= '<p id="box-'.$args['id'].'">'. stripslashes( $args['description']).'</p>';
		$_html .= '</header>';
		
		$_html .= '<div style="clear: both"></div>';
		$_html .= '</section>';
		
		echo $_html;
	}
}