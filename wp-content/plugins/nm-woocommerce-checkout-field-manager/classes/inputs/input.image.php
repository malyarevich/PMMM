<?php
/*
 * Followig class handling pre-uploaded image control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Image_cofm extends NM_Inputs_cofm{
	
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
		
		$this -> title 		= __ ( 'Images', 'nm-cofm' );
		$this -> desc		= __ ( 'Images selection', 'nm-cofm' );
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
		
		'required' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Required', 'nm-cofm' ),
				'desc' => __ ( 'Select this if it must be required.', 'nm-cofm' ) 
		),
				
		'images' => array (
				'type' => 'pre-images',
				'title' => __ ( 'Select images', 'nm-cofm' ),
				'desc' => __ ( 'Select images from media library', 'nm-cofm' )
		),
				
		'multiple_allowed' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Multiple selection?', 'nm-cofm' ),
				'desc' => __ ( 'Allow users to select more then one images?.', 'nm-cofm' )
		),
				
		'selected' => array (
				'type' => 'text',
				'title' => __ ( 'Selected image', 'nm-personalizedproduct' ),
				'desc' => __ ( 'Type option title (given above) if you want it already selected.', 'nm-personalizedproduct' )
		),
				
		'popup_width' => array (
				'type' => 'text',
				'title' => __ ( 'Popup width', 'nm-cofm' ),
				'desc' => __ ( 'Popup window width in px e.g: 750', 'nm-cofm' )
		),
		
		'popup_height' => array (
				'type' => 'text',
				'title' => __ ( 'Popup height', 'nm-cofm' ),
				'desc' => __ ( 'Popup window height in px e.g: 550', 'nm-cofm' )
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
	function render_input($args, $images="", $default_selected = ""){
		
		// nm_personalizedproduct_pa($images);
		
		$_html = '<div class="pre_upload_image_box">';
			
		$img_index = 0;
		$popup_width	= $args['popup-width'] == '' ? 600 : $args['popup-width'];
		$popup_height	= $args['popup-height'] == '' ? 450 : $args['popup-height'];
		
		if ($images) {
			
			foreach ($images as $image){
					
				$image_title = isset($image['title']) ? $image['title'] : '';
				$image_link = isset($image['link']) ? $image['link'] : '';
				$image_price = isset($image['price']) ? $image['price'] : '';
				
				$_html .= '<div class="pre_upload_image">';
				$_html .= '<img width="75" src="'.$image_link.'" />';
				
				$field_id = 'f-meta-'.strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $image_title ) );
					
				// for bigger view
				$_html	.= '<div style="display:none" id="pre_uploaded_image_' . $args['id'].'-'.$img_index.'"><img style="margin: 0 auto;display: block;" src="' . $image_link . '" /></div>';
					
				$_html	.= '<div class="input_image">';
				if ($args['multiple-allowed'] == 'on') {
					$_html	.= '<input field-type="image" id="'.$field_id.'" type="checkbox" data-label="'.stripslashes( $image_title ).'" name="'.$args['name'].'[]" value="'.$image_link.'" />';
				}else{
					//default selected
					$checked = ($image_title == $default_selected ? 'checked = "checked"' : '' );
					$_html	.= '<input field-type="image" id="'.$field_id.'" type="radio" data-price="'.$image_price.'" data-label="'.stripslashes( $image_title ).'" name="'.$args['name'].'" value="'.esc_attr(json_encode($image)).'" '.$checked.' />';
				}
					
				// image big view
				$price = '';
				if(function_exists('woocommerce_price'))
					$price = woocommerce_price( $image_price );
				else
					$price = $image_price;
					
				$display_title = $image_title;
				if( $price != '' ){
					$display_title = $image_title . ' - '.$price;
				}
					
				$_html	.= '<a href="#TB_inline?width='.$popup_width.'&height='.$popup_height.'&inlineId=pre_uploaded_image_' . $args['id'].'-'.$img_index.'" class="thickbox" title="' . $image_title . '"><img width="15" src="' . $this -> plugin_meta['url'] . '/images/zoom.png" /></a>';
				$_html	.= '<div class="p_u_i_name">'.stripslashes( $display_title ) . '</div>';
				$_html	.= '</div>';	//input_image
					
					
				$_html .= '</div>';
					
				$img_index++;
			}
		}
		
		$_html .= '<div style="clear:both"></div>';		//container_buttons
			
		$_html .= '</div>';		//container_buttons
		
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
					
						jQuery(function($){

					$(".nm-cometa-box input:radio").on('change', function(){
					
							get_all_priced_options();
						});
					});
							
					});
					
					//--></script>
					<?php
			}
}