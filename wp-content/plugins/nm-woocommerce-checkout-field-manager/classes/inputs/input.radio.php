<?php
/*
 * Followig class handling radio input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Radio_cofm extends NM_Inputs_cofm{
	
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
		
		$this -> title 		= __ ( 'Radio Input', 'nm-cofm' );
		$this -> desc		= __ ( 'regular radio input', 'nm-cofm' );
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
							'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'nm-cofm' ) 
					),
					'description' => array (
							'type' => 'text',
							'title' => __ ( 'Description', 'nm-cofm' ),
							'desc' => __ ( 'Small description, it will be diplay near name title.', 'nm-cofm' ) 
					),
					'error_message' => array (
							'type' => 'text',
							'title' => __ ( 'Error message', 'nm-cofm' ),
							'desc' => __ ( 'Insert the error message for validation.', 'nm-cofm' ) 
					),
					
					'options' => array (
							'type' => 'paired',
							'title' => __ ( 'Add options', 'nm-cofm' ),
							'desc' => __ ( 'Type option with price (optionally)', 'nm-cofm' )
					),
				
					'taxable' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Taxable?', 'nm-cofm' ),
								'desc' => __ ( 'Tax applied on extra fee.', 'nm-cofm' ) 
						),
						
					'calculate' => array (
							'type' => 'select',
							'title' => __ ( 'Calculation', 'nm-cofm' ),
							'desc' => __ ( 'Fixed Price or Percentage of cart total', 'nm-cofm' ),
							'options'	=> array('fixed' => 'Fixed', 'percent' => 'Percent'),
					),
					
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
	 * @params: $options
	*/
	function render_input($args, $options="", $default=""){
		
		$_html = '';
		foreach($options as $opt)
		{
			$checked = ($opt['option'] == $default) ? 'checked="checked"' : '';

			if($opt['price']){
				
				$price_tag = '';
				if($args['data-calculate'] == 'percent'){
					$price_tag = $opt['price'] . '%';
				}else{
					$price_tag = woocommerce_price($opt['price']);
				}
				
				$output	= stripslashes(trim($opt['option'])) .' (+ ' . $price_tag .')';
			}else{
				$output	= stripslashes(trim($opt['option']));
			}
			
			$field_id = 'f-meta-'.strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $opt['option'] ) );
			
			$_html .= '<label for="'.$field_id.'"> <input id="'.$field_id.'" data-price="'.$opt['price'].'" type="radio" ';
			
			foreach ($args as $attr => $value){
					
				$_html .= $attr.'="'.stripslashes( $value ).'"';
			}
		
			$_html .= ' value="'.$opt['option'].'" '.$checked.'> ';
			$_html .= $output;
		
			$_html .= '</label>';
		}
		
		echo $_html;
		
		$this -> get_input_js($args);
	}
	
		/*
	 * following function is rendering JS needed for input
	*/
	function get_input_js($args){
		
	?>
		
				<script type="text/javascript">	
				<!--
				jQuery(function($){

					$(".nm-cometa-box input:radio").on('change', function(){
					
						get_all_priced_options();
					});
				});
				
				
				
				//--></script>
				<?php
		}
}