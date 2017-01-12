<?php
/*
 * Followig class handling radio input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Palettes_cofm extends NM_Inputs_cofm{
	
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
		
		$this -> title 		= __ ( 'Color Palettes', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'color boxes', 'nm-personalizedproduct' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		return array (
					'title' => array (
							'type' => 'text',
							'title' => __ ( 'Title', 'nm-personalizedproduct' ),
							'desc' => __ ( 'It will be shown as field label', 'nm-personalizedproduct' ) 
					),
					'data_name' => array (
							'type' => 'text',
							'title' => __ ( 'Data name', 'nm-personalizedproduct' ),
							'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'nm-personalizedproduct' ) 
					),
					'description' => array (
							'type' => 'text',
							'title' => __ ( 'Description', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Small description, it will be diplay near name title.', 'nm-personalizedproduct' ) 
					),
					'error_message' => array (
							'type' => 'text',
							'title' => __ ( 'Error message', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Insert the error message for validation.', 'nm-personalizedproduct' ) 
					),
					
					'options' => array (
								'type' => 'paired',
								'title' => __ ( 'Add colors', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Type color code with price (optionally)', 'nm-personalizedproduct' )
					),
				
					'required' => array (
							'type' => 'checkbox',
							'title' => __ ( 'Required', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Select this if it must be required.', 'nm-personalizedproduct' ) 
					),
					
					'class' => array (
							'type' => 'text',
							'title' => __ ( 'Class', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'nm-personalizedproduct' ) 
					),
					'width' => array (
							'type' => 'text',
							'title' => __ ( 'Width', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Type field width in % e.g: 50%', 'nm-personalizedproduct' ) 
					),
					'logic' => array (
							'type' => 'checkbox',
							'title' => __ ( 'Enable conditional logic', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Tick it to turn conditional logic to work below', 'nm-personalizedproduct' )
					),
					'conditions' => array (
							'type' => 'html-conditions',
							'title' => __ ( 'Conditions', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Tick it to turn conditional logic to work below', 'nm-personalizedproduct' )
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
	 * @params: $options
	*/
	function render_input($args, $options="", $default=""){
		
		$_html = '';
		//adding color NONE
		$options[] = array('option' => '#fff', 'price' => '0');
		
		foreach($options as $opt)
		{

			if($opt['price']){
				$output	=  woocommerce_price($opt['price']);
			}else{
				$output	= '';
			}
			
			$field_id = $args['name'] . '-meta-'.strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $opt['option'] ) );
			
			$_html .= '<label for="'.$field_id.'"> <input id="'.$field_id.'" data-price="'.$opt['price'].'" type="radio" ';
			
			foreach ($args as $attr => $value){
					
				$_html .= $attr.'="'.stripslashes( $value ).'"';
			}
		
			//set border to black if color is white
			$palette_border = ($opt['option'] == '#fff' ? '1px solid #000' : '');
			
			$_html .= ' value="'.$opt['option'].'" '.checked($default, $opt['option'], false).'>';
			$_html .= '<div class="palette-box" style="border:'.$palette_border.'; background-color:'.trim($opt['option']).';">'.$output.'</div>';
		
			$_html .= '</label>';
		}
		
		echo $_html;
	}
}