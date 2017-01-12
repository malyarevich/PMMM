<?php
/*
 * Followig class handling date input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Date_cofm extends NM_Inputs_cofm{
	
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
		
		$this -> title 		= __ ( 'Date Input', 'nm-cofm' );
		$this -> desc		= __ ( 'regular date input', 'nm-cofm' );
		$this -> settings	= self::get_settings();
		
		$this -> input_scripts = array('shipped'		=> array('jquery-ui-datepicker'));
		
		add_action ( 'wp_enqueue_scripts', array ($this, 'load_input_scripts'));
		
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
		'date_formats' => array (
				'type' => 'select',
				'title' => __ ( 'Date formats', 'nm-cofm' ),
				'desc' => __ ( 'Select date format.', 'nm-cofm' ),
				'options' => array (
						'mm/dd/yy' => 'Default - mm/dd/yyyy',
						'dd/mm/yy' => 'dd/mm/yyyy',
						'yy-mm-dd' => 'ISO 8601 - yy-mm-dd',
						'd M, y' => 'Short - d M, y',
						'd MM, y' => 'Medium - d MM, y',
						'DD, d MM, yy' => 'Full - DD, d MM, yy',
						'\'day\' d \'of\' MM \'in the year\' yy' => 'With text - \'day\' d \'of\' MM \'in the year\' yy',
						'\'Month\' MM \'day\' d \'in the year\' yy' => 'With text - \'Month\' January \'day\' 7 \'in the year\' yy'
				) 
		),
		'year_range' => array (
				'type' => 'text',
				'title' => __ ( 'Year Range', 'nm-cofm' ),
				'desc' => __ ( 'Set Year Range To:From e.g: 1950:2015', 'nm-cofm' ) 
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
		
		$_html = '<input type="text" ';
		
		foreach ($args as $attr => $value){
			
			$_html .= $attr.'="'.stripslashes( $value ).'"';
		}
		
		if($content)
			$_html .= 'value="' . stripslashes($content	) . '"';
		
		$_html .= ' />';
		
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

					$("#<?php echo $args['id'];?>").datepicker({ 	changeMonth: true,
						changeYear: true,
						dateFormat: $("#<?php echo $args['id'];?>").attr('data-format'),
						yearRange: "<?php echo $args['year_range'];?>",
						});
				});
				
				//--></script>
				<?php
		}
}