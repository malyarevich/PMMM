<?php
/*
 * Followig class handling date input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_DOB_cofm extends NM_Inputs_cofm{
	
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
		
		$this -> title 		= __ ( 'Date of birth Input', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'DOB Dropdowns input', 'nm-personalizedproduct' );
		$this -> settings	= self::get_settings();
		
		
		add_action ( 'wp_enqueue_scripts', array ($this, 'load_input_scripts'));
		
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
	 * @params: args
	*/
	function render_input($args, $content=""){
		
		$months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sept','Oct','Nov','Dec');
		
		$_html = '<label for="daydropdown" style="width:30%;float:left;margin-right: 2px;">
					Day: <select id="daydropdown" name="dob_day">';
		for($i=1; $i<=31; $i++){
			
			$_html .= '<option>'.$i.'</option>';
		}
		$_html	.= '</select></label>';
		$_html .= '<label for="monthdropdown" style="width:30%;float:left;margin-right: 2px;">
					Month: <select id="monthdropdown" name="dob_month">';
					
		foreach($months as $month){
			
			$_html .= '<option>'.$month.'</option>';
		}
		$_html		.= '</select></label>';
		
		
		$_html .= '<label for="yeardropdown" style="width:30%;float:left">
					Year: <select id="yeardropdown" name="dob_year">';
		for($i=1975; $i <= date('Y'); $i++){
			
			$_html .= '<option>'.$i.'</option>';
		}
		
		$_html	.= '</select></label>';
		
		echo $_html;
		
		$this -> get_input_js($args);
	}
	
	/*
	 * following function is rendering JS needed for input
	*/
	function get_input_js($args){
	?>
		
				<script type="text/javascript">	
				
				
				//--></script>
				<?php
		}
}