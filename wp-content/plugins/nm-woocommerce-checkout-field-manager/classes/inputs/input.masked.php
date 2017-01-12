<?php
/*
 * Followig class handling text input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
* 
* 
* ::::::::::::::::::::::: CREDIT :::::::::::::::::::::::
* Copyright (c) 2007-2013 Josh Bush (digitalbush.com)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*/

class NM_Masked_cofm extends NM_Inputs_cofm{
	
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
		
		$this -> title 		= __ ( 'Masked Input', 'nm-cofm' );
		$this -> desc		= __ ( 'masked input', 'nm-cofm' );
		$this -> settings	= self::get_settings();
		
		
		$this -> input_scripts = array(	'shipped'		=> array(''),
		
										'custom'		=> array(
												array (
														'script_name' => 'mask_script',
														'script_source' => '/js/mask/jquery.maskedinput.min.js',
														'localized' => false,
														'type' => 'js',
														'depends'	=> array('jquery')
												),
													
										)
								);
		
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
				
		'mask' => array (
				'type' => 'text',
				'title' => __ ( 'Input Mask', 'nm-cofm' ),
				'desc' => __ ( 'Input mask e.g:<br>a - Represents an alpha character (A-Z,a-z)<br>9 - Represents a numeric character (0-9)<br>* - Represents an alphanumeric character (A-Z,a-z,0-9)', 'nm-cofm' )
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
		
		$_html = '<input type="text" ';
		
		foreach ($args as $attr => $value){
			
			$_html .= $attr.'="'.stripslashes( $value ).'"';
		}
		
		// mask as placeholder
		$_html .= 'placeholder="' . stripslashes($args['data-mask']) . '"';
		
		if($content)
			$_html .= 'value="' . stripslashes($content) . '"';
		
		$_html .= ' />';
		
		echo $_html;
		
		$this -> get_input_js($args);
	}
	
	
	/*
	 * following function is rendering JS needed for input
	*/
	function get_input_js($args){
	
		$input_mask =  $args['data-mask'];
		?>
	
			<script type="text/javascript">	
			<!--
			jQuery("#<?php echo $args['id'];?>").mask("<?php echo $input_mask;?>",{completed:function(){
				this.attr('data-ismask', 'yes');
				}
			});
			//--></script>
			<?php
	}
}