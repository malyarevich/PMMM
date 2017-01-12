<?php
/*
 * Followig class handling text input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Time_cofm extends NM_Inputs_cofm{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		$this -> plugin_meta = get_plugin_meta_cofm();
		
		$this -> title 		= __ ( 'Time Input', 'nm-cofm' );
		$this -> desc		= __ ( 'regular time input', 'nm-cofm' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
						'title' => array (
								'type' => 'text',
								'title' => __ ( 'Title', 'nm-cofm' ),
								'desc' => __ ( 'It will be shown as field label', 'nm-cofm' ) 
						),
						'data_name' => array (
								'type' => 'text',
								'title' => __ ( 'Data name', 'nm-cofm' ),
								'desc' => __ ( 'Note:Use only lowercase characters and underscores. <br>Do not remove prefix (billing_, shipping_)', 'nm-cofm' ) 
						),
						'description' => array (
								'type' => 'text',
								'title' => __ ( 'Description', 'nm-cofm' ),
								'desc' => __ ( 'Small description, it will be display near time title.', 'nm-cofm' ) 
						),
						'error_message' => array (
								'type' => 'text',
								'title' => __ ( 'Error message', 'nm-cofm' ),
								'desc' => __ ( 'Insert the error message for validation.', 'nm-cofm' ) 
						),
				
						/*'max_length' => array (
								'type' => 'text',
								'title' => __ ( 'Max. Length', 'nm-cofm' ),
								'desc' => __ ( 'Max. characters allowed, leave blank for default', 'nm-cofm' )
						),
						
 						'min_length' => array (
 								'type' => 'text',
 								'title' => __ ( 'Min. Length', 'nm-cofm' ),
 								'desc' => __ ( 'Min. characters allowed, leave blank for default', 'nm-cofm' )
 						),*/
						
						'required' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Required', 'nm-cofm' ),
								'desc' => __ ( 'Select this if it must be required.', 'nm-cofm' ) 
						),
						'class' => array (
								'type' => 'text',
								'title' => __ ( 'Class', 'nm-cofm' ),
								'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'nm-cofm' ) 
						),
						'width' => array (
								'type' => 'text',
								'title' => __ ( 'Width', 'nm-cofm' ),
								'desc' => __ ( 'Type field width in % e.g: 50%', 'nm-cofm' ) 
						),
						'logic' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Enable conditional logic', 'nm-cofm' ),
								'desc' => __ ( 'Tick it to turn conditional logic to work below', 'nm-cofm' )
						),
						'conditions' => array (
								'type' => 'html-conditions',
								'title' => __ ( 'Conditions', 'nm-cofm' ),
								'desc' => __ ( 'Tick it to turn conditional logic to work below', 'nm-cofm' )
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
		
		$_html = '<input type="time" ';
		
		foreach ($args as $attr => $value){
			
			$_html .= $attr.'="'.stripslashes( $value ).'"';
		}
		
		if($content)
			$_html .= 'value="' . stripslashes($content	) . '"';
		
		$_html .= ' />';
		
		echo $_html;
	}
}