<?php
/*
 * Followig class handling select input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Select_cofm extends NM_Inputs_cofm{
	
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
		
		$this -> title 		= __ ( 'Select-box Input', 'nm-cofm' );
		$this -> desc		= __ ( 'regular select-box input', 'nm-cofm' );
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
								'desc' => __ ( 'Small description, it will shown as first option', 'nm-cofm' ) 
						),
						'error_message' => array (
								'type' => 'text',
								'title' => __ ( 'Error message', 'nm-cofm' ),
								'desc' => __ ( 'Insert the error message for validation.', 'nm-cofm' ) 
						),
						
						/*'options' => array (
								'type' => 'textarea',
								'title' => __ ( 'Add options', 'nm-cofm' ),
								'desc' => __ ( 'Type each option/line', 'nm-cofm' )
						),*/
						
						'options' => array (
								'type' => 'paired',
								'title' => __ ( 'Add options', 'nm-cofm' ),
								'desc' => __ ( 'Type option with price (optionally). Prices only applied for Order Fields', 'nm-cofm' )
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
						
						'selected' => array (
								'type' => 'text',
								'title' => __ ( 'Selected option', 'nm-cofm' ),
								'desc' => __ ( 'Type option name (given above) if you want already selected.', 'nm-cofm' ) 
						),
						'multiple' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Multiple', 'nm-cofm' ),
								'desc' => __ ( 'Multiple values selected by user', 'nm-cofm' ) 
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
		
		//legacy support
		if( isset($options) && ! is_array($options) ){
			$options = explode("\n", $options);
			foreach($options as $opt){
				$options_temp[] = array('option' => $opt, 'price' => '');
			}
			
			$options = $options_temp;
		}
		
		
		$_html = '<select ';
		
		foreach ($args as $attr => $value){
			
			$_html .= $attr.'="'.stripslashes( $value ).'"';
		}
		
		$_html .= '>';
		
		$_html .= '<option value="">'.__('Select option', 'nm-cofm').'</option>';
		
		foreach($options as $opt)
		{
				
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
				
			$_html .= '<option data-price="'.$opt['price'].'" value="'.$opt['option'].'" '. selected($default, $opt['option'], false).'>';
			$_html .= $output;
			$_html .= '</option>';
		}
		
		$_html .= '</select>';
		
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

					$(".nm-cometa-box select").on('change', function(){
					
						get_all_priced_options();
					});
				});
				
				
				
				//--></script>
				<?php
		}
}