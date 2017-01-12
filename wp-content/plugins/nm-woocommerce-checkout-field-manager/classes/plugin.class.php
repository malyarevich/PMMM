<?php
/*
 * this is main plugin class
*/


/* ======= the model main class =========== */
if(!class_exists('NM_Framwork_V1_COFM')){
	$_framework = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'nm-framework.php';
	if( file_exists($_framework))
		include_once($_framework);
	else
		die('Reen, Reen, BUMP! not found '.$_framework);
}


/*
 * [1]
 * TODO: change the class name of your plugin
 */
class NM_Checkout_Field_Manager extends NM_Framwork_V1_COFM{

	static $tbl_cofm = 'nm_checkoutmeta';
	
	var $inputs;
	
	
	var $co_fields,$co_files;
	
	var $extra_item_fee;
	
	var $billing_default, $shipping_default;
	
	private static $ins = null;
	
	public static function init()
	{
		add_action('plugins_loaded', array(self::get_instance(), '_setup'));
	}
	
	public static function get_instance()
	{
		// create a new object if it doesn't exist.
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}
	/*
	 * plugin constructur
	*/
	function _setup() {
	
		// setting plugin meta saved in config.php
	
		add_action( 'woocommerce_init', array( $this, 'setup_checkoutmanager_plugin' ) );
	}
	
	/*
	 * plugin constructur
	 */
	function setup_checkoutmanager_plugin(){
		
		//setting plugin meta saved in config.php
		$this -> plugin_meta = get_plugin_meta_cofm();

		//getting saved settings
		$this -> plugin_settings = get_option($this->plugin_meta['shortname'].'_settings');
		
		// file upload dir name
		$this -> co_files = 'checkout_files';
		
		
		/*
		 * [2]
		 * TODO: update scripts array for SHIPPED scripts
		 * only use handlers
		 */
		//setting shipped scripts
		$this -> wp_shipped_scripts = array('jquery');
		
		
		/*
		 * [3]
		* TODO: update scripts array for custom scripts/styles
		*/
		//setting plugin settings
		$this -> plugin_scripts =  array(array(	'script_name'	=> 'scripts',
												'script_source'	=> '/js/script.js',
												'localized'		=> true,
												'type'			=> 'js'
										),
												array(	'script_name'	=> 'styles',
														'script_source'	=> '/plugin.styles.css',
														'localized'		=> false,
														'type'			=> 'style'
												),
				
												array (
														'script_name' => 'nm-ui-style',
														'script_source' => '/js/ui/css/smoothness/jquery-ui-1.10.3.custom.min.css',
														'localized' => false,
														'type' => 'style',
														'page_slug' => array (
																'nm-new-form'
														)
												)
										);
		
		/*
		 * [4]
		* TODO: localized array that will be used in JS files
		* Localized object will always be your pluginshortname_vars
		* e.g: pluginshortname_vars.ajaxurl
		*/
		$this -> localized_vars = array('ajaxurl' => admin_url( 'admin-ajax.php' ),
				'plugin_url' 		=> $this->plugin_meta['url'],
				'settings'			=> $this -> plugin_settings,
				'file_upload_path_thumb' => $this -> get_file_dir_url ( true ),
				'file_upload_path' => $this -> get_file_dir_url (),);
				
		/**
		 * setting Billing and Shippin default fields
		 * */
		 $this -> billing_default = array('billing_first_name','billing_last_name','billing_company','billing_address_1','billing_address_2','billing_city','billing_state','billing_postcode','billing_country','billing_email','billing_phone');
		$shipping_default = array('shipping_first_name','shipping_last_name','shipping_company','shipping_address_1','shipping_address_2','shipping_city','shipping_state','shipping_postcode','shipping_country',);
		
		
		/*
		 * [5]
		 * TODO: this array will grow as plugin grow
		 * all functions which need to be called back MUST be in this array
		 * setting callbacks
		 */
		//following array are functions name and ajax callback handlers
		$this -> ajax_callbacks = array('save_settings',		//do not change this action, is for admin
										'load_default_fields',
										'remove_default_fields',
										'update_all_co_fields',
										'save_edited_photo',
										'upload_file',
										'delete_file',
										'update_checkout',
										'validate_api',
										);
		
		/*
		 * plugin localization being initiated here
		 */
		add_action('init', array($this, 'wpp_textdomain'));
		
		
	
		
		/*
		 * hooking up scripts for front-end
		*/
		add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
		
		/*
		 * registering callbacks
		*/
		$this -> do_callbacks();
		
		
		// populating $inputs with NM_Inputs object
		$this -> inputs = self::get_all_inputs ();
		
		
		/*
		 * Process the checkout
		*/
		add_action('woocommerce_checkout_process', array($this, 'check_validation_before_checkout'));
		
		//filtering core checkout fields
		add_filter( 'woocommerce_checkout_fields' , array($this, 'override_co_fields') );
		//filterting billing and shipping default felds
		/**
		 * country
		 * first_name
		 * last_name
		 * company
		 * address_1
		 * address_2
		 * city
		 * state
		 * postcode
		 * */
		 add_filter('woocommerce_default_address_fields', array($this, 'override_address_fields'));
		
		/*
		 * adding checkout fields after order notes
		*/		
		add_action( 'woocommerce_after_order_notes' , array($this, 'render_co_fields_after_order') );
		
		/**
		 * Update the order meta with field value
		 **/
		add_action('woocommerce_checkout_update_order_meta', array($this, 'update_checkout_meta_data'));
		
		/*
		 * is a filter hook that allows you to add your new fields to the confirmation emails
		*/
		add_filter('woocommerce_email_order_meta_fields',	array($this, 'add_meta_in_order_email'), 10, 3);
		
		/*
		 * adding file download link/thumbs into order email
		*/
		add_action('woocommerce_email_order_meta', array($this, 'add_files_link_in_email'), 10, 2);
		
		
		/*
		 * another panel in orders to display files
		*/
		add_action( 'admin_init', array($this, 'render_files_in_orders') );
		
		/**
		 * Display billing field value on the order edit page
		 */
		add_action( 'woocommerce_admin_order_data_after_billing_address', array($this, 'add_meta_billing_area'), 10, 1 );
		
		
		/**
		 * Display shipping field value on the order edit page
		 */
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'add_meta_shipping_area'), 10, 1 );
		
		add_action('setup_styles_and_scripts_cofm', array($this, 'get_connected_to_load_it'));
		
		//show billing and shippin fields on my account page
		/*add_filter('woocommerce_order_formatted_billing_address', array($this, 'myaccount_billing_fields'), 10, 2);
		add_filter('woocommerce_order_formatted_billing_address', array($this, 'myaccount_shipping_fields'), 10, 2);*/
		
		add_action( 'woocommerce_cart_calculate_fees', array($this, 'add_option_fees') );	
		
		//handling woocommerce form fields to add extra attributes
		add_filter('woocommerce_form_field_text', array($this, 'render_co_form_field'), 10, 4);
		add_filter('woocommerce_form_field_textarea', array($this, 'render_co_form_field'), 10, 4);
		add_filter('woocommerce_form_field_select', array($this, 'render_co_form_field'), 10, 4);
	}
	
	
	
	/*
	 * =============== NOW do your JOB ===========================
	 * 
	 */
	
	// i18n and l10n support here
	// plugin localization
	function wpp_textdomain() {
		$locale_dir = dirname( plugin_basename( dirname(__FILE__ ) ) ) . '/locale/';//$this->plugin_meta['path'] . '/locale/';
		load_plugin_textdomain('nm-cofm', false, $locale_dir);
	}
	
	
	/*
	 * overriding default co fileds
	 */
	function override_co_fields($fields){
		
		//nm_personalizedcheckout_pa($fields);
		
		//loading all co fields
		$co_field_meta = $this -> get_all_co_fields();
		
		if ($co_field_meta -> billing_meta != '' && $co_field_meta -> billing_meta != 'null'){
			
			$billing_fields = $co_field_meta -> billing_meta;
			//$this->co_fields['billing'] = self::convert_co_fields( json_decode($billing_fields, true), 'billing' );
			
			$fields['billing'] = self::convert_co_fields( json_decode($billing_fields, true), 'billing' );
		}  
		
		if($co_field_meta -> shipping_meta != '' && $co_field_meta -> shipping_meta != 'null'){
			
			//shipping fields
			$shipping_fields = $co_field_meta -> shipping_meta;
			//$this->co_fields['shipping'] = self::convert_co_fields( json_decode($shipping_fields, true), 'shipping' );
			$fields['shipping'] = self::convert_co_fields( json_decode($shipping_fields, true), 'shipping' );
		}
		
		if($co_field_meta -> order_meta != '' && $co_field_meta -> order_meta != 'null'){
			
			//order fields
			$order_fields = $co_field_meta -> order_meta;
			$order_fields = self::convert_co_fields( json_decode($order_fields, true), 'order' );
			unset($fields['order']['order_comments']);
			if( isset($order_fields['order_comments']) ){
				$fields['order']['order_comments'] = $order_fields['order_comments'];
			}
			
		}
			
		//var_dump($fields);
		return $fields;
	}
	
	
	/**
	 * overiding address fields
	 * */
	 function override_address_fields($address_fields){
	 	
	 	return $address_fields;
	 }
	
	/*
	 * this function is converting nm-co-fields into
	 * required woocommerce co fields format
	 */
	function convert_co_fields($fields, $co_type){
		
		$temp_fields = array();
		
				
		if ($fields){
			
			//nm_personalizedcheckout_pa($fields);
			foreach ($fields as $field){
					
				
				$type 		= (isset($field['type']) ? $field['type'] : '');
				$description= (isset($field['description']) ? $field['description'] : '');
				$required 	= (isset($field['required']) && $field['required'] == 'on' ? true : false);
				$class		= (isset($field['class']) ? explode(',', $field['class']) : array('form-row','form-row-wide'));
				$field_name = (isset($field['data_name']) ? $field['data_name'] : '');
				$field_title = esc_attr($field['title']);
				
				//field visibility based on productid, category and user role
				$product_visibility	= (isset($field['product_visibility']) ? $field['product_visibility'] : '' );
				$category_visibility	= (isset($field['category_visibility']) ? $field['category_visibility'] : '' );
				$user_visibility	= (isset($field['user_visibility']) ? $field['user_visibility'] : '' );
				
				$visibilities = array();
				if( $product_visibility != '' ){
					$visibilities['product'] = $product_visibility;
				}
				if( $category_visibility != '' ){
					$visibilities['category'] = $category_visibility;
				}
				if( $user_visibility != '' ){
					$visibilities['user'] = $user_visibility;
				}
	
				if($visibilities){			
					if( ! $this -> field_should_visible($visibilities) ){
						continue; 
					}
				}
			
				// conditioned elements
				$custom_attributes = array();
				$fiel_logic = (isset( $field['logic'] ) ? $field['logic'] : '');
				if ( $fiel_logic == 'on' ) {
				
					$custom_attributes['data-visibility'] = $field['conditions']['visibility'];
					$custom_attributes['data-rules'] = json_encode($field['conditions']);
					$custom_attributes['data-fieldid'] = $field_name;
				}
				
				
				//nm_personalizedcheckout_pa($field);
				$options = '';
				if(isset($field['options']) && $field['options'] != ''){
				
				
					//setting default placeholder for select type
			        if($field['type'] == 'select' && $field['description'] == ''){
			        	 $select_label = 'Select '.$field_title;
			        	 
			        	$options[''] = sprintf(__('%s', 'nm-cofm'), $select_label);
			        }else{
			        	$options[''] = sprintf(__('%s', 'nm-cofm'), $field['description']);
			        }
				
					if( ! is_array($field['options']) ){
						$options_raw	= explode("\n", $field['options']);
						foreach ($options_raw as $opt){
							$options[$opt] = $opt;
						}
					}else{
						foreach ($field['options'] as $option){
							$options[$option['option']] = $option['option'];
						}
					}
				}
				
				
				
				
				# Output
		        //WMPL
		        /**
		         * retreive translations
		         */
		        if (function_exists ( 'icl_translate' )){
		        	
		            $field_title    = icl_translate('WooCheckout', 'COFM - '.$field_title, $field_title);
		            $description	= icl_translate('WooCheckout', 'COFM - '.$description, $description);
		            
		            //options
		            if(isset($field['options']) && $field['options'] != ''){
				
						//setting default placeholder for select type
				        if($field['type'] == 'select' && $description == ''){
				        	 $select_label = 'Select '.$field_title;
				        	 
				        	$options['description'] = sprintf(__('%s', 'nm-cofm'), $select_label);
				        }else{
				        	$options['description'] = sprintf(__('%s', 'nm-cofm'), $description);
				        }
						
						if( ! is_array($field['options']) ){
							$options_raw	= explode("\n", $field['options']);
							foreach ($options_raw as $opt){
								$options[$opt] = $opt;
							}
						}else{
							foreach ($field['options'] as $option){
								$options[$option['option']] = icl_translate('WooCheckout', 'COFM - '.$option['option'], $option['option']);
								
							}
						}
					}
		        }
		        
		        //var_dump($custom_attributes);
				$temp_fields[ $field_name ] = array('type'		=> $type,
						'label'		=>  $field_title,
						'required'	=> $required,
						'class'		=> $class,
						'placeholder'	=> stripslashes_deep ($description),
						'options'		=> stripslashes_deep ($options),
						'custom_attributes'	=> $custom_attributes,
						);
	
			}
			
		}
		
		return $temp_fields;
	}
	
	
	function render_co_form_field($field, $key, $args, $value){
		
		//nm_personalizedcheckout_pa($args);
		$extra_html = array();
		if($args['custom_attributes'] != null){
			
			foreach($args['custom_attributes'] as $key => $val){
				
				if( $key == 'data-visibility' && $val == 'Show'){
					$key = 'style';
					$val = 'display:none';
				}
				$extra_html[] = esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
			}
		}
		
		$field = preg_replace('/(<p\b[^><]*)>/i', '$1 '.implode( ' ', $extra_html ).'>', $field);
		//var_dump($field);
		return $field;
	}
	
	
	/*
	 * loading fields after order notes
	 */
	function render_co_fields_after_order(){
		
		//loading all co fields
		$co_field_meta = $this -> get_all_co_fields();
		
		$order_fields = $co_field_meta -> order_meta;
		$fields = json_decode($order_fields, true);
		
		
		//nm_personalizedcheckout_pa($fields);
		
		/*
		 * nm special fields
		* date
		* mask
		* upload/file input
		* color
		* image
		*/
		
		if($fields){
			
			//Checkout Editor Plugin generates errors and warnings.  To prevent these from conflicting with other things, we are going to disable warnings and errors.
			$err_level = error_reporting();
			error_reporting( 0 );
			
		$started_section = '';
		foreach ($fields as $field){
			
			//ignoring order_comment fields as it's being set in funciton override_co_fields
			if( $field['data_name'] == 'order_comments' ){
				
				continue;
			}
		
			
			$field = stripslashes_deep( $field );
			
			$title 				= (isset($field['title']) ? $field['title'] : '' );
			$type 				= (isset($field['type']) ? $field['type'] : '' );
			$type 				= (isset($field['type']) ? $field['type'] : '' );
			$name 				= (isset($field['data_name']) ? $field['data_name'] : '' );
			$name 				= strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $name ) );
			$width 				= (isset($field['width']) ? $field['width'] : 0 );
			$class 				= (isset($field['class']) ? $field['class'] : '' );
			$required 			= (isset($field['required']) ? $field['required'] : '' );
			$placeholder 		= (isset($field['description']) ? $field['description'] : '' );
			$error_message 		= (isset($field['error_message']) ? $field['error_message'] : '' );
			
			//field visibility based on productid, category and user role
			$product_visibility	= (isset($field['product_visibility']) ? $field['product_visibility'] : '' );
			$category_visibility	= (isset($field['category_visibility']) ? $field['category_visibility'] : '' );
			$user_visibility	= (isset($field['user_visibility']) ? $field['user_visibility'] : '' );
			
			$visibilities = array();
			if( $product_visibility != '' ){
				$visibilities['product'] = $product_visibility;
			}
			if( $category_visibility != '' ){
				$visibilities['category'] = $category_visibility;
			}
			if( $user_visibility != '' ){
				$visibilities['user'] = $user_visibility;
			}

			if($visibilities){			
				if( ! $this -> field_should_visible($visibilities) ){
					continue; 
				}
			}
		
			# Output
	        //WMPL
	        /**
	         * retreive translations
	         */
	        if (function_exists ( 'icl_translate' )){
	            $title    = icl_translate('NM_COFM', 'Woo Checkout Manager - '.$name, $title);
	        }
			
			// conditioned elements
			$visibility = '';
			$conditions_data = '';
			$fiel_logic = (isset( $field['logic'] ) ? $field['logic'] : '');
			if ( $fiel_logic == 'on' ) {
			
				if($field['conditions']['visibility'] == 'Show')
					$visibility = 'display: none';
			
				$conditions_data	= 'data-rules="'.esc_attr( json_encode($field['conditions'] )).'"';
			}
			
			$the_width = (intval ( $width ) > 0 ? intval ( $width ) - 1 . '%' : '');
			$the_margin = '1%';
			
			$class = ($class == '' ? 'form-row,form-row-wide' : $class);
			$class = str_replace(',', ' ', trim($class));
			
			if($required == 'on'){
				$label = $title . ' <abbr class="required" title="required">*</abbr>';
			}else{
				$label = $title;
			}
			
			$defaul_class = 'nm-cometa-box';
			if( $type == 'palettes')
				$defaul_class .= ' nm-color-palette';
			
			echo '<div class="'.$defaul_class.'" input-id="'.$name.'">';				
			switch($type){
				
				case 'text':
					
					$min_length = (isset($field['min_length']) ? $field['min_length'] : '');
					$max_length = (isset($field['max_length']) ? $field['max_length'] : '');
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'	=> $error_message,
									'placeholder'	=> $placeholder,
									'maxlength'	=> $max_length,
									'minlength'	=> $min_length,
									'class' => 'input-text',
									);
					echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
					
					$data_default = ( isset( $field['default_value'] ) ? $field['default_value'] : '');
					$this -> inputs[$type]	-> render_input($args, $data_default);					
					
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</p>';
					break;
				
				case 'time':
					
					$min_length = (isset($field['min_length']) ? $field['min_length'] : '');
					$max_length = (isset($field['max_length']) ? $field['max_length'] : '');
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'	=> $error_message,
									'maxlength'	=> $max_length,
									'minlength'	=> $min_length,
									'class' => 'input-text',
									);
					echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
					
					$data_default = ( isset( $field['default_value'] ) ? $field['default_value'] : '');
					$this -> inputs[$type]	-> render_input($args, $data_default);					
					
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</p>';
					break;
					
				case 'textarea':
					
					$min_length = (isset($field['min_length']) ? $field['min_length'] : '');
					$max_length = (isset($field['max_length']) ? $field['max_length'] : '');
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'	=> $error_message,
									'maxlength'	=> $max_length,
									'minlength'	=> $min_length,
									'placeholder'	=> $placeholder,
									'class' => 'input-text',
									);
					echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
					
					$data_default = ( isset( $field['default_value'] ) ? $field['default_value'] : '');
					$this -> inputs[$type]	-> render_input($args, $data_default);					
					
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</p>';
					break;
					
					
				case 'number':
					
					$min_val = (isset($field['min_value']) ? $field['min_value'] : '');
					$max_val = (isset($field['max_value']) ? $field['max_value'] : '');
					$step = (isset($field['step']) ? $field['step'] : '');
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'	=> $error_message,
									'placeholder'	=> $placeholder,
									'max'	=> $max_val,
									'min'	=> $min_val,
									'step'	=> $step,
									);
					echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
					
					$data_default = ( isset( $field['default_value'] ) ? $field['default_value'] : '');
					$this -> inputs[$type]	-> render_input($args, $data_default);					
					
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</p>';
					break;
			
				case 'date':
						
					$date_format = (isset($field['date_formats']) ? $field['date_formats'] : '');
					$year_range = (isset($field['year_range']) ? $field['year_range'] : 'c-10:c+10');
					
					$args = array(	'name'			=> $name,
					'id'			=> $name,
					'data-type'		=> $type,
					'data-req'		=> $required,
					'data-message'	=> $error_message,
					'data-format'	=> $date_format,
					'year_range'	=> $year_range,
					'placeholder'	=> $placeholder,
					'class'			=> 'input-text');
						
					echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
						
					$this -> inputs[$type]	-> render_input($args);
					echo '</p>';
					break;
					
				case 'dob':
						
					$args = array(	'name'			=> $name,
					'id'			=> $name,
					'data-type'		=> $type,
					'data-req'		=> $field['required'],
					'data-message'	=> $error_message,
					'class'			=> 'input-text');
						
					echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
						
					$this -> inputs[$type]	-> render_input($args);
					echo '</p>';
					break;
					
			
				case 'masked':
					
						$mask = ($field['mask'] == '' ? '9999' : $field['mask']);
						
						$args = array(	'name'			=> $name,
						'id'			=> $name,
						'data-type'		=> $type,
						'data-req'		=> $field['required'],
						'data-mask'		=> $mask,
						'data-ismask'	=> "no",
						'data-message'	=> $error_message,
						'placeholder'	=> $placeholder,
						'class'			=> 'input-text');
						
						echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
					
						$this -> inputs[$type]	-> render_input($args);
					
						//for validtion message
						echo '<span class="errors"></span>';
						echo '</p>';
						break;
						
						case 'hidden':
						
							$args = array(	'name'			=> $name,
							'id'			=> $name,
							'data-type'		=> $type,
							);
								
							$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);
							break;
							
							
						case 'color':
						
							$args = array(	'name'			=> $name,
							'id'			=> $name,
							'data-type'		=> $type,
							'data-req'		=> $field['required'],
							'data-message'	=> $error_message,
							'default-color'	=> $field['default_color'],
							'show-onload'	=> $field['show_onload'],
							'show-palletes'	=> $field['show_palletes'],
							'placeholder'	=> $placeholder,
							'class'			=> 'input-text');
						
							echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
							printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
						
							$this -> inputs[$type]	-> render_input($args);
							echo '</p>';
							break;
							
						case 'palettes':
						
							$args = array(	'name'			=> $name,
							'id'			=> $name,
							'data-type'		=> $type,
							'data-req'		=> $field['required'],
							'data-message'	=> $error_message,
							'data-label' 	=> $title,
							'placeholder'	=> $placeholder,
							'class'			=> 'input-text');
						
							echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
							printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
						
							$this -> inputs[$type]	-> render_input($args, $field['options']);
							echo '</p>';
							break;
							
						case 'checkbox':
						
							$defaul_checked = explode("\n", $field['checked']);
							$calculate			= (isset($field['calculate']) ? $field['calculate'] : 'fixed');
							$taxable			= (isset($field['taxable']) ? $field['taxable'] : '');
							
							echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
							printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
						
						
							$args = array(	'name'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $field['required'],
									'data-message'	=> $error_message,
									'data-label' => $title,
									'data-tax'	=> $taxable,
									'data-calculate' => $calculate,
										);
							$this -> inputs[$type]	-> render_input($args, $field['options'], $defaul_checked);
							
							echo '</p>';
						
							break;
							
					case 'select':
				
						$default_selected 	= (isset( $field['selected'] ) ? $field['selected'] : '' );
						$calculate			= (isset($field['calculate']) ? $field['calculate'] : 'fixed');
						$taxable			= (isset($field['taxable']) ? $field['taxable'] : '');
						$multiple			= (isset($field['multiple']) ? $field['multiple'] : '');
						
						$args = array(	'name'			=> $name,
										'id'			=> $name,
										'data-type'		=> $type,
										'data-req'		=> $required,
										'data-message'		=> $error_message,
										'data-label' => $title,
										'data-tax'	=> $taxable,
										'data-calculate' => $calculate,
										);
										
						if($multiple == 'on'){
							$args['multiple'] = 'multiple';
						}
						
						echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
						
						$this -> inputs[$type]	-> render_input($args, $field['options'], $default_selected);
					
						//for validtion message
						echo '<span class="errors"></span>';
						echo '</p>';
						break;
						
						
					case 'radio':
				
						$default_selected 	= (isset( $field['selected'] ) ? $field['selected'] : '' );
						$calculate			= (isset($field['calculate']) ? $field['calculate'] : 'fixed');
						$taxable			= (isset($field['taxable']) ? $field['taxable'] : '');
						
						$args = array(	'name'			=> $name,
										'id'			=> $name,
										'data-type'		=> $type,
										'data-req'		=> $required,
										'data-message'		=> $error_message,
										'data-label' => $title,
										'data-tax'	=> $taxable,
										'data-calculate' => $calculate,
										);
						echo '<p id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
						
						$this -> inputs[$type]	-> render_input($args, $field['options'], $default_selected);
					
						//for validtion message
						echo '<span class="errors"></span>';
						echo '</p>';
						break;
					
					
						case 'image':
								
							$default_selected = $field['selected'];
							$args = array(	'name'			=> $name,
							'id'			=> $name,
							'data-type'		=> $type,
							'data-req'		=> $field['required'],
							'data-message'	=> $error_message,
							'popup-width'	=> $field['popup_width'],
							'popup-height'	=> $field['popup_height'],
							'multiple-allowed' => $field['multiple_allowed']);
								
							echo '<div id="pre-uploaded-images-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
							printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
								
							$this -> inputs[$type]	-> render_input($args, $field['images'], $default_selected);
								
							//for validtion message
							echo '<span class="errors"></span>';
							echo '</div>';
							
							add_thickbox();
							break;
							
							
						case 'file':
						
							$label_select = ($field['button_label_select'] == '' ? __('Select files', 'nm-cofm') : $field['button_label_select']);
							$files_allowed = ($field['files_allowed'] == '' ? 1 : $field['files_allowed']);
							$file_types = ($field['file_types'] == '' ? 'jpg,png,gif' : $field['file_types']);
							$file_size = ($field['file_size'] == '' ? '10mb' : $field['file_size']);
							$chunk_size = ($field['chunk_size'] == '' ? '5mb' : $field['chunk_size']);
							$button_class = ($field['class'] == '' ? '' : $field['class']);
								
							$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $field['required'],
									'data-message'	=> $error_message,
									'button-label-select'	=> $label_select,
									'files-allowed'			=> $files_allowed,
									'file-types'			=> $file_types,
									'file-size'				=> $file_size,
									'button-class'			=> $button_class,
									'chunk-size'			=> $chunk_size,
									'popup-width'	=> $field['popup_width'],
									'popup-height'	=> $field['popup_height']);
								
							echo '<div id="box-'.$name.'" class="form-row form-row-wide fileupload-box" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
							printf( __('<label for="%1$s">%2$s</label>', 'nm-cofm'), $name, $label );
							echo '<div id="nm-uploader-area-'. $name.'" class="nm-uploader-area">';
								
							$this -> inputs[$type]	-> render_input($args);
						
							echo '<span class="errors"></span>';
						
							echo '</div>';		//.nm-uploader-area
							echo '</div>';
						
							// adding thickbox support
							add_thickbox();
							break;
							
					case 'section':
					
						
						$section_title 		= strtolower(preg_replace("![^a-z0-9]+!i", "_", $field['title']));
						$started_section 	= 'cofm-section-'.$section_title;
					
						$args = array(	'id'			=> $started_section,
								'data-type'		=> $type,
								'title'			=> $field['title'],
								'description'			=> $field['description'],
						);
					
						$this -> inputs[$type]	-> render_input($args);
					
						break;
									
					
					default:
						
						$options_wc= '';
						//echo '<div id="box-'.$name.'" class="'.$class.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						if ($field['type'] == 'select'){
							$options = explode("\n", $field['options']);
							
							foreach ($options as $opt){
								$options_wc[$opt] = $opt;
							}
						}
						
						$required = (isset($field['required']) && $field['required'] == 'on' ? true : false);
						$multiple = (isset($field['multiple']) && $field['multiple'] == 'on' ? array('multiple' => 'multiple') : array());
						
						woocommerce_form_field( $name, array(
						'type'          => $field['type'],
						'class'         => array($class),
						'label'         => $label,
						'placeholder'   => $placeholder,
						'options'		=> $options_wc,
						//'required'		=> $required,
						'custom_attributes'	=> $multiple,
						));
						
						//echo '</div>';
						break;
			}
			
			echo '</div>';		//.nm-cometa-box
		}
		
		
			error_reporting( $err_level );
		}
		
	}
	
	/*
	 * this is checking that required meta
	* is filled
	*/
	function check_validation_before_checkout(){
	
		//nm_personalizedcheckout_pa($_POST);
		
		global $woocommerce;
		
		
		//Checkout Editor Plugin generates errors and warnings.  To prevent these from conflicting with other things, we are going to disable warnings and errors.
		$err_level = error_reporting();
		error_reporting( 0 );
		
		
		//loading all co fields
		$co_field_meta = $this -> get_all_co_fields();
		
		$fields = array();
		foreach($co_field_meta as $type => $co_fields){
			
			$field_section = '';
			switch($type){
				case 'billing_meta':
					$field_section = 'billing';
					break;
				case 'shipping_meta':
					$field_section = 'shipping';
					break;
				default:
					$field_section = 'order';
			}
			//$order_fields = $co_field_meta -> order_meta;
			$fields = json_decode($co_fields, true);
			
			if ($fields){
		
				foreach ($fields as $field){
		
					$field_type = (isset($field ['type']) ? $field ['type'] : '');
					$field_title = (isset($field ['title']) ? $field ['title'] : '');
					$data_name = (isset($field ['data_name']) ? $field ['data_name'] : '');
					$error_message = (isset($field['error_message']) ? $field['error_message'] : '');
					
					$name = strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $data_name ) );
					$required = (isset($field['required']) && $field['required'] == 'on') ? true : false;
					$error_message = stripslashes ($error_message);
					
					//echo $name. ' is '.$required.'\r\n';
					if( ! array_key_exists($name, $_POST ) && //if field is not posted then why should continue below
						$field_type != 'checkbox' &&
						$field_type != 'file')		//also check it's NOT A CHECKBOX
						continue;
					
					
					if( function_exists('icl_translate') ){
						$error_message	= icl_translate('WooCheckout', 'COFM - '.$error_message, $error_message);
						$field_title	= icl_translate('WooCheckout', 'COFM - '.$field_title, $field_title);
					}
					
					$c_name = '_'.$name.'_';
					$posted_name = $_POST[$name];
					
					$condional_logic_checker = '';
					if(isset($_POST[$c_name])){
						$condional_logic_checker =  $_POST[$c_name];
						//disabling the validation for core checkout fields
						WC() -> checkout -> checkout_fields[ $field_section ][$name]['required'] = false;
						
					}elseif(isset($field['logic']) && $field['logic'] == 'on'){
						$condional_logic_checker = 'hidden';
						
					}else{
						$condional_logic_checker = 'showing';
					}
					
					//$woo_core_co_fields = WC() -> checkout -> checkout_fields;
					//nm_personalizedcheckout_pa($woo_core_co_fields);
					$error_message = ($error_message != '' ? '<strong>'.$field_title.'</strong>: '.$error_message : $field_title );
					
					if ($field['type'] == 'file') {
						$element_value = $_POST ['thefile_' . $name];
						if ($required && (count ( $element_value ) == 0) && $condional_logic_checker == 'showing') {
							
							nm_wc_add_notice( $error_message );
						}
					}else{
						
						if($required && $posted_name == '' && $condional_logic_checker == 'showing'){
							nm_wc_add_notice( $error_message );
						}	
					}
						
					}	
				}
			}	
	
			error_reporting( $err_level );
	}
	
	
	/*
	 * adding meta data into order's meta
	 */
	function update_checkout_meta_data($order_id){
	
		//nm_personalizedcheckout_pa($_POST); exit;
		
		//loading all co fields
		$co_field_meta = $this -> get_all_co_fields();
		
		$billing_fields = $co_field_meta -> billing_meta;
		$billing_fields = json_decode($billing_fields, true);
		
		$shipping_fields = $co_field_meta -> shipping_meta;
		$shipping_fields = json_decode($shipping_fields, true);
				
		$order_fields = $co_field_meta -> order_meta;
		$order_fields = json_decode($order_fields, true);
		
		if( $order_fields && $billing_fields && $shipping_fields ){
			$fields = array_merge($billing_fields, $shipping_fields, $order_fields);
		}elseif( $order_fields && $billing_fields ){
			$fields = array_merge($billing_fields, $order_fields);
		}elseif( $order_fields && $shipping_fields ){
			$fields = array_merge($shipping_fields, $order_fields);
		}elseif( $billing_fields && $shipping_fields ){
			$fields = array_merge($shipping_fields, $billing_fields);
		}elseif( $billing_fields ){
			$fields = $billing_fields;
		}elseif( $shipping_fields ){
			$fields = $shipping_fields;
		}elseif( $order_fields ){
			$fields = $order_fields;
		}else{
			$fields = NULL;
		}
			
		//$fields = array_merge($billing_fields, $shipping_fields, $order_fields);
		//nm_personalizedcheckout_pa($order_fields); exit;
		
		
		$billing_default = array('billing_country', 'billing_first_name','billing_last_name','billing_company','billing_address_1','billing_address_2','billing_city','billing_state','billing_postcode','billing_email','billing_phone');
		$shipping_default = array('shipping_country','shipping_first_name','shipping_last_name','shipping_company','shipping_address_1','shipping_address_2','shipping_city','shipping_state','shipping_postcode');
		$co_default_fields = array_merge($billing_default,$shipping_default);
		
		//nm_personalizedcheckout_pa($fields); exit;
		$all_attached_files = array();
		if ($fields){
	
			foreach ($fields as $field){
				
				if (!in_array($field['data_name'], $co_default_fields)){

					$value = '';
					$name = strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $field ['data_name'] ) );
					if($_POST[$name] != ''){
					
						if($field['type'] == 'checkbox' && is_array($_POST [$name]))
							$value = implode(', ', $_POST [$name]);
						else
							$value = $_POST[$name];
					
					}elseif($field['type'] == 'file'){
						if($_POST ['thefile_' . $name]){
							$value = implode(', ', $_POST ['thefile_' . $name]);
							$all_attached_files[$field['title']] = $_POST ['thefile_' . $name];
						}
							
					}elseif($field['type'] == 'dob'){
						if($_POST ['dob_day']){
							$value = $_POST ['dob_day'].'-'.$_POST ['dob_month'].'-'.$_POST ['dob_year'];
						}
							
					}
					
					//echo 'label '.$field['title'].' val '. $value.'<br>';
					update_post_meta($order_id, $field['title'], $value);					
				}	
				
			}
			
			//saving all attached file separately
			update_post_meta($order_id, 'Files attached', json_encode($all_attached_files));
		}

	}
	
	/**
	 * add our custom fields to WooCommerce order emails
	 * @param array $keys
	 * @return array
	 */
	public function add_meta_in_order_email($keys, $sent_to_admin, $order) {
		
		$co_field_meta = $this -> get_all_co_fields();
		
		$order_fields = $co_field_meta -> order_meta;
		$fields = json_decode($order_fields, true);
		
		if ($fields){
	
			foreach ($fields as $field){
			
				if($field['title']){
					$keys[] = array(
						'label' => wptexturize( $field['title'] ),
						'value' => wptexturize( get_post_meta( $order->id, $field['title'], true ) )
					);
				}
			}
		}
		
		return $keys;
	}
	
	
	/*
	 * adding files link in order email
	*/
	function add_files_link_in_email($order, $is_admin){
		
		// if (! $is_admin)
		// 	return;
	
		$uploaded_files = get_post_meta( $order->id, 'Files attached', true );
		$order_files = json_decode($uploaded_files);
		
		
		$base_path 	= $this -> setup_file_directory();
		
		if ($order_files) {
			
			foreach ( $order_files as $title => $files ) {
				
				echo '<strong>';
				printf(__('File attached %s:', 'nm-cofm'), $title);
				echo '</strong>';
				
				if($files){
					foreach ($files as $key => $file){
						
						
						// =========== renaming files by appending orderid as prefix ==============
						$new_filename = $order -> id . '-' . $file;
						$source_file = $base_path . $file;
						$destination = $base_path . $new_filename;
							
// 						if (file_exists ( $destination ))
// 							break;
							
						if (file_exists ( $source_file )) {
						
							if (! rename ( $source_file, $destination ))
								die ( 'Error while re-naming order image ' . $source_file );
						}
						// =========== renaming files by appending orderid as prefix ==============
						
						$ext = strtolower ( substr ( strrchr ( $new_filename, '.' ), 1 ) );
						
						if ($ext == 'png' || $ext == 'jpg' || $ext == 'gif' || $ext == 'jpeg')
							$src_thumb = $this -> get_file_dir_url ( true ) . $file;
						else
							$src_thumb = $this -> plugin_meta ['url'] . '/images/file.png';
						
						$src_file = $this -> get_file_dir_url () . $new_filename;
					
						echo '<table>';
						echo '<tr><td width="100"><img src="' . $src_thumb . '"><td><td><a href="' . $src_file . '" target="_blank">' . __ ( 'Download ' ) . $new_filename . '</a> ' . $this -> size_in_kb ( $new_filename ) . '</td>';
						
						$edited_path = $this->get_file_dir_path() . 'edits/' . $new_filename;
						if (file_exists($edited_path)) {
							$new_filename_url_edit = $this->get_file_dir_url () .  'edits/' . $new_filename;
							echo '<td><a href="' . $new_filename_url_edit . '" target="_blank">' . __ ( 'Download edited image', $this->plugin_meta ['shortname'] ) . '</a></td>';
						}
						
						echo '</tr>';
						echo '</table>';
						
					}
				}
				
			}
			
		}
		
		
		/**
		 * showing extra billing fields in email
		 * 
		 * @since 4.9
		 */
		$co_field_meta = $this -> get_all_co_fields();
		$billing_fields = $co_field_meta -> billing_meta;
		$billing_fields = json_decode($billing_fields, true);
		
		$has_fields = false;
		$billing_default = array('billing_first_name','billing_last_name','billing_company','billing_address_1','billing_address_2','billing_city','billing_state','billing_postcode','billing_country','billing_email','billing_phone');
		if ($billing_fields){
			foreach ($billing_fields as $field){
				
				if (!in_array($field['data_name'], $billing_default)){
					
					// only showing first field
					if( !$has_fields )
						echo '<h4>'.__('Extra Billing Fields', 'nm-cofm').'</h4>';
						
					echo '<p><strong>'.$field['title'].':</strong> ' . get_post_meta( $order->id, $field['title'], true ) . '</p>';
					$has_fields = true;
				}
				
			}
			if( $has_fields ) 
				echo '<hr>';
		}
		
		$shipping_fields = $co_field_meta -> shipping_meta;
		$shipping_fields = json_decode($shipping_fields, true);
	
		$shipping_default = array('shipping_first_name','shipping_last_name','shipping_company','shipping_address_1','shipping_address_2','shipping_city','shipping_state','shipping_postcode','shipping_country',);
		$has_fields = false;
		if ($shipping_fields){
			foreach ($shipping_fields as $field){
				if (!in_array($field['data_name'], $shipping_default)){
					// only showing first field
					if( !$has_fields )
						echo '<h4>'.__('Extra Billing Fields', 'nm-cofm').'</h4>';
						
					echo '<p><strong>'.$field['title'].':</strong> ' . get_post_meta( $order->id, $field['title'], true ) . '</p>';
					$has_fields = true;
				}
			}
			if( $has_fields ) 
				echo '<hr>';
		}
		
	}
	
	
	/*
	 * rendering meta box in orders
	*/
	function render_files_in_orders() {
	
		add_meta_box( 'orders_file_uploaded', __('Files attached/uploaded during Checkout', 'nm-cofm'),
		array($this,'display_uploaded_files'),
		'shop_order', 'normal', 'default');
		
		// adding meta box for pre-defined images selection
		add_meta_box ( 'selected_images_in_orders', __('Selected images/designs', 'nm-cofm'),
		array ( $this, 'display_selected_files'),
		'shop_order', 'normal', 'default' );
	}
	
	
	function display_uploaded_files( $order ) {
		// Retrieve current name of the Director and Movie Rating based on review ID
		$uploaded_files = get_post_meta( $order->ID, 'Files attached', true );
		$order_files = json_decode($uploaded_files);

		if($order_files){

			foreach ($order_files as $title => $files){

				echo '<strong>';
				printf(__('File attached %s:', 'nm-cofm'), $title);
				echo '</strong>';
				
				foreach ($files as $key => $file){

					$order_named = $order->ID .'-' . $file;
						
					$ext = substr(strrchr($order_named,'.'),1);
					if($ext == 'png' || $ext == 'jpg' || $ext == 'gif' || $ext == 'jpeg')
						$src_thumb = $this->get_file_dir_url(true).$file;
					else
						$src_thumb = $this->plugin_url.'/images/file.png';
						
					$src = $this->get_file_dir_url().$order_named;
						
					echo '<table>';
					echo '<tr><td width="100"><img src="'.$src_thumb.'"><td><td><a href="'.$src.'" target="_blank">'.__('Download ').$file.'</a> '.$this->size_in_kb($order_named).'</td></tr>';
					echo '</table>';
				}
			}

		}else {

			echo '<p>'.__('No file(s) attached/uploaded', $this->plugin_shortname).'</p>';
		}

			
	}
	
	/**
	 * displaying selected images in order panel
	 * @param unknown $order
	 */
	function display_selected_files($order) {
		// woo_pa($order);
		
		$co_field_meta = $this -> get_all_co_fields();
		$order_meta = json_decode($co_field_meta -> order_meta);
		//nm_personalizedcheckout_pa($order_meta);
	
		foreach ( $order_meta as $meta => $data ) {
				
			//nm_personalizedcheckout_pa($data);
			if ($data -> type == 'image') {
		
				$selected_files = get_post_meta( $order->ID, $data -> title, true );
				if (is_array($selected_files)) {		//mean checkbox is on
					
					if ($selected_files) {
						echo '<h3>';
						_e('Following files are selected by user', 'nm-cofm');
						echo '</h3>';
						foreach ($selected_files as $file){
							
							echo '<img width="350" src="'.$file.'">';
						}
													
					}
					
				}else{
					
					$selected_files = json_decode( $selected_files, true );
					if ($selected_files) {
					
						$title = $selected_files['title'];
						$link = $selected_files['link'];
						echo '<h3>';
						printf(__('File selected by user: %s', 'nm-cofm'), $title );
						echo '</h3>';
						echo '<img width="350" src="'.$link.'">';				
					
					}	
				}
				//
				//nm_personalizedcheckout_pa($selected_files);
				
			}
		}
	}
	
	
	/*
	 * returning defualt billing/shipping fields
	 */
	function load_default_fields(){
		
		//print_r($_REQUEST);
		extract ( $_REQUEST );
	
		$field_set = '';
		$dt_key = '';
		if($_REQUEST['section_type'] == 'billing'){
			
			$field_set = array(
								array('type'		=> 'country',
													'data_name'	=> 'billing_country',
													'title'	=> __('Country', 'nm-cofm'),
													'description' => __('Country', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide,address-field,update_totals_on_change',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'billing_first_name',
													'title'	=> __('First Name', 'nm-cofm'),
													'description' => __('First Name', 'nm-cofm'),
													'class'			=> 'form-row,form-row-first',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'billing_last_name',
													'title'	=> __('Last Name', 'nm-cofm'),
													'description' => __('Last Name', 'nm-cofm'),
													'class'			=> 'form-row,form-row-last',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'billing_company',
													'title'	=> __('Company Name', 'nm-cofm'),
													'description' => __('Company Name', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide',
													'required'	=> ''),
								array('type'		=> 'text',
													'data_name'	=> 'billing_address_1',
													'title'	=> __('Address 1', 'nm-cofm'),
													'description' => __('Address 1', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide,address-field',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'billing_address_2',
													'title'	=> __('Address 2', 'nm-cofm'),
													'description' => __('Address 2', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide,address-field',
													'required'	=> ''),
								array('type'		=> 'text',
													'data_name'	=> 'billing_city',
													'title'	=> __('City', 'nm-cofm'),
													'description' => __('City', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide,address-field',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'billing_state',
													'title'	=> __('State / Country', 'nm-cofm'),
													'description' => __('State / Country', 'nm-cofm'),
													'class'			=> 'form-row,form-row-first,address-field',
													'required'	=> ''),
								array('type'		=> 'text',
													'data_name'	=> 'billing_postcode',
													'title'	=> __('Postcode / ZIP', 'nm-cofm'),
													'description' => __('Postcode / ZIP', 'nm-cofm'),
													'class'			=> 'form-row,form-row-last,address-field',
													'required'	=> 'on'),
								
								array('type'		=> 'text',
													'data_name'	=> 'billing_email',
													'title'	=> __('Email', 'nm-cofm'),
													'description' => __('Email', 'nm-cofm'),
													'class'			=> 'form-row,form-row-first',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'billing_phone',
													'title'	=> __('Phone', 'nm-cofm'),
													'description' => __('Phone', 'nm-cofm'),
													'class'			=> 'form-row,form-row-last',
													'required'	=> 'on'),
					
			);
			
			$dt_key = 'billing_meta';
			
		}elseif($_REQUEST['section_type'] == 'shipping'){
			
			$field_set = array(
				
								array('type'		=> 'country',
													'data_name'	=> 'shipping_country',
													'title'	=> __('Country', 'nm-cofm'),
													'description' => __('Country', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide,address-field,update_totals_on_change',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'shipping_first_name',
													'title'	=> __('First Name', 'nm-cofm'),
													'description' => __('First Name', 'nm-cofm'),
													'class'			=> 'form-row,form-row-first',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'shipping_last_name',
													'title'	=> __('Last Name', 'nm-cofm'),
													'description' => __('Last Name', 'nm-cofm'),
													'class'			=> 'form-row, form-row-last',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'shipping_company',
													'title'	=> __('Company Name', 'nm-cofm'),
													'description' => __('Company Name', 'nm-cofm'),
													'required'	=> ''),
								array('type'		=> 'text',
													'data_name'	=> 'shipping_address_1',
													'title'	=> __('Address 1', 'nm-cofm'),
													'description' => __('Address 1', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide,address-field',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'shipping_address_2',
													'title'	=> __('Address 2', 'nm-cofm'),
													'description' => __('Address 2', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide,address-field',
													'required'	=> ''),
								array('type'		=> 'text',
													'data_name'	=> 'shipping_city',
													'title'	=> __('City', 'nm-cofm'),
													'description' => __('City', 'nm-cofm'),
													'class'			=> 'form-row,form-row-wide,address-field',
													'required'	=> 'on'),
								array('type'		=> 'text',
													'data_name'	=> 'shipping_state',
													'title'	=> __('State / Country', 'nm-cofm'),
													'description' => __('State / Country', 'nm-cofm'),
													'class'			=> 'form-row, form-row-first,address-field',
													'required'	=> ''),
								array('type'		=> 'text',
													'data_name'	=> 'shipping_postcode',
													'title'	=> __('Postcode / ZIP', 'nm-cofm'),
													'description' => __('Postcode / ZIP', 'nm-cofm'),
													'class'			=> 'form-row, form-row-last,address-field',
													'required'	=> 'on'),
			);
			
			$dt_key = 'shipping_meta';
		}else{
			
			$field_set = array(array('type'		=> 'textarea',
					'data_name'	=> 'order_comments',
					'title'	=> __('Order Notes', 'nm-cofm'),
					'class'			=> 'form-row, notes, woocommerce-validated',
					'description' => __('Notes about your order, e.g. special notes for delivery.', 'nm-cofm'),
					'required'	=> ''),
			);
				
			$dt_key = 'order_meta';
				
		}
		
		
		$res_id = $this -> insert_fields_into_db($dt_key, $field_set);
		
		// $wpdb->show_errors(); $wpdb->print_error();
		
		$resp = array ();
		$resp = array (
					'message' => __ ( 'Meta added successfully', 'nm-cofm' ),
					'status' => 'success',
					'cofm_id' => $res_id
			);
		
		echo json_encode ( $resp );
		
		die(0);
	}
	
	
	
	/*
	 * this function is inserting meta fields into table
	 */
	function insert_fields_into_db($dt_key, $field_set){
		
		global $wpdb;
		
		// check if checkout meta already exists
		$cofm_table = $wpdb->prefix . self::$tbl_cofm;
		$sql = "SELECT cofm_id
		FROM $cofm_table;";
		
		//WPML - compatibility
		//Register the strings that need translation
		if($field_set){
			foreach($field_set as $field){
				if (function_exists ( 'icl_register_string' )){
					icl_register_string('WooCheckout', 'COFM - '.$field['title'], $field['title']);
					icl_register_string('WooCheckout', 'COFM - '.$field['description'], $field['description']);
					icl_register_string('WooCheckout', 'COFM - '.$field['error_message'], $field['error_message']);
					
					//for options
					if( isset($field['options']) ){
						foreach($field['options'] as $option){
							icl_register_string('WooCheckout', 'COFM - '.$option['option'], $option['option']);
							echo 'regisgerting option '.$option['option'];
						}
					}
				}
			}
		}
		
		$existing_cofm_id = $wpdb -> get_var($sql);
		
		if ($existing_cofm_id == '') {
				
			$dt = array (
					$dt_key => json_encode ( $field_set ),
			);
		
			$format = array (
					'%s',
			);
			
			//nm_personalizedcheckout_pa($dt); exit;
				
			$res_id = $this -> insert_table ( self::$tbl_cofm, $dt, $format );
				
		}else{
				
			$dt = array (
					$dt_key => json_encode ( $field_set ),
			);
		
			$format = array (
					'%s',
			);
				
			$where = array (
					'cofm_id' => $existing_cofm_id
			);
				
				
			$where_format = array (
					'%d'
			);
				
			$res_id = $this -> update_table( self::$tbl_cofm, $dt,$where, $format, $where_format );
			//echo $res_id;
		}
		
		return $res_id;
	}
	
	
	/*
	 * removing billing/shipping fields
	 */
	function remove_default_fields(){
		
		global $wpdb;
		extract ( $_REQUEST );
		
		// check if checkout meta already exists
		$cofm_table = $wpdb->prefix . self::$tbl_cofm;
		$sql = "SELECT cofm_id
		FROM $cofm_table;";
		
		
		$existing_cofm_id = $wpdb -> get_var($sql);
		
		$dt_key = '';
		switch ($_REQUEST['section_type'])
		{
			case 'billing':
				$dt_key = 'billing_meta';
				break;
				
			case 'shipping':
				$dt_key = 'shipping_meta';
				break;
				
			case 'order':
				$dt_key = 'order_meta';
				break;
		}		
		
		
		if($existing_cofm_id != ''){
			
			$dt = array (
					$dt_key => '',
			);
			
			$format = array (
					'%s',
			);
				
			$where = array (
					'cofm_id' => $existing_cofm_id
			);
				
				
			$where_format = array (
					'%d'
			);
				
			$res_id = $this -> update_table( self::$tbl_cofm, $dt, $where, $format, $where_format );
			
		}
		
		$resp = array ();
		if ($res_id) {
		
			$resp = array (
					'message' => __ ( 'Fields updated successfully', 'nm-cofm' ),
					'status' => 'success',
					'cofm_id' => $res_id
			);
		} else {
		
			$resp = array (
					'message' => __ ( 'Error while updating fields, please try again', 'nm-cofm' ),
					'status' => 'failed',
					'cofm_id' => ''
			);
		}
		
		echo json_encode ( $resp );
		
		die(0);
		
	}
	
	/*
	 * this function is updating billing fields
	 */
	function update_all_co_fields(){
		
		//print_r($_REQUEST);
		
		extract($_REQUEST);
		
		$dt_key = '';
		switch ($fields_type)
		{
			case 'billing':
				$dt_key = 'billing_meta';
				break;
				
			case 'shipping':
				$dt_key = 'shipping_meta';
				break;
				
			case 'order':
				$dt_key = 'order_meta';
				break;
		}		
		

		$res_id = self::insert_fields_into_db($dt_key, $checkout_meta);

		$resp = array ();
		$resp = array (
					'message' => __ ( 'Meta updated successfully', 'nm-cofm' ),
					'status' => 'success',
					'cofm_id' => $res_id
			);
		
		echo json_encode ( $resp );
		
		die(0);
	}
	

	/*
	 * adding meta in billing area
	 */
	function add_meta_billing_area($order){
		
		//loading all co fields
		$co_field_meta = $this -> get_all_co_fields();
		
		$billing_fields = $co_field_meta -> billing_meta;
		$billing_fields = json_decode($billing_fields, true);
		
		$billing_default = array('billing_first_name','billing_last_name','billing_company','billing_address_1','billing_address_2','billing_city','billing_state','billing_postcode','billing_country','billing_email','billing_phone');
		if ($billing_fields){
			foreach ($billing_fields as $field){
				if (!in_array($field['data_name'], $billing_default)){
			
					echo '<h4>'.__('Customized Fields', 'nm-cofm').'</h4>';
					echo '<p><strong>'.$field['title'].':</strong> ' . get_post_meta( $order->id, $field['title'], true ) . '</p>';
				}
			}	
		}
		
		
	}
	
	
	/*
	 * adding meta in shipping area
	*/
	function add_meta_shipping_area($order){
	
		//loading all co fields
		$co_field_meta = $this -> get_all_co_fields();
	
		$shipping_fields = $co_field_meta -> shipping_meta;
		$shipping_fields = json_decode($shipping_fields, true);
	
		$shipping_default = array('shipping_first_name','shipping_last_name','shipping_company','shipping_address_1','shipping_address_2','shipping_city','shipping_state','shipping_postcode','shipping_country',);
		
		if ($shipping_fields){
			foreach ($shipping_fields as $field){
				if (!in_array($field['data_name'], $shipping_default)){
			
					echo '<h4>'.__('Customized Fields', 'nm-cofm').'</h4>';
					echo '<p><strong>'.$field['title'].':</strong> ' . get_post_meta( $order->id, $field['title'], true ) . '</p>';
				}
			}
		}
		
	
	}
	
	/*
	 * uploading file here
	*/
function upload_file() {
		
		
		header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
		header ( "Cache-Control: no-store, no-cache, must-revalidate" );
		header ( "Cache-Control: post-check=0, pre-check=0", false );
		header ( "Pragma: no-cache" );
		
		// setting up some variables
		$file_dir_path = $this->setup_file_directory ();
		$response = array ();
		if ($file_dir_path == 'errDirectory') {
			
			$response ['status'] = 'error';
			$response ['message'] = __ ( 'Error while creating directory', $this->plugin_shortname );
			die ( 0 );
		}
		
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds
		                        
		// 5 minutes execution time
		@set_time_limit ( 5 * 60 );
		
		// Uncomment this one to fake upload time
		// usleep(5000);
		
		// Get parameters
		$chunk = isset ( $_REQUEST ["chunk"] ) ? intval ( $_REQUEST ["chunk"] ) : 0;
		$chunks = isset ( $_REQUEST ["chunks"] ) ? intval ( $_REQUEST ["chunks"] ) : 0;
		$file_name = isset ( $_REQUEST ["name"] ) ? $_REQUEST ["name"] : '';
		
		// Clean the fileName for security reasons
		$file_name = preg_replace ( '/[^\w\._]+/', '_', $file_name );
		$file_name = strtolower($file_name);
		
		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists ( $file_dir_path . $file_name )) {
			$ext = strrpos ( $file_name, '.' );
			$file_name_a = substr ( $file_name, 0, $ext );
			$file_name_b = substr ( $file_name, $ext );
			
			$count = 1;
			while ( file_exists ( $file_dir_path . $file_name_a . '_' . $count . $file_name_b ) )
				$count ++;
			
			$file_name = $file_name_a . '_' . $count . $file_name_b;
		}
		
		// Remove old temp files
		if ($cleanupTargetDir && is_dir ( $file_dir_path ) && ($dir = opendir ( $file_dir_path ))) {
			while ( ($file = readdir ( $dir )) !== false ) {
				$tmpfilePath = $file_dir_path . $file;
				
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match ( '/\.part$/', $file ) && (filemtime ( $tmpfilePath ) < time () - $maxFileAge) && ($tmpfilePath != "{$file_path}.part")) {
					@unlink ( $tmpfilePath );
				}
			}
			
			closedir ( $dir );
		} else
			die ( '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}' );
		
		$file_path = $file_dir_path . $file_name;
		
		// Look for the content type header
		if (isset ( $_SERVER ["HTTP_CONTENT_TYPE"] ))
			$contentType = $_SERVER ["HTTP_CONTENT_TYPE"];
		
		if (isset ( $_SERVER ["CONTENT_TYPE"] ))
			$contentType = $_SERVER ["CONTENT_TYPE"];
			
			// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos ( $contentType, "multipart" ) !== false) {
			if (isset ( $_FILES ['file'] ['tmp_name'] ) && is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
				// Open temp file
				$out = fopen ( "{$file_path}.part", $chunk == 0 ? "wb" : "ab" );
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen ( $_FILES ['file'] ['tmp_name'], "rb" );
					
					if ($in) {
						while ( $buff = fread ( $in, 4096 ) )
							fwrite ( $out, $buff );
					} else
						die ( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}' );
					fclose ( $in );
					fclose ( $out );
					@unlink ( $_FILES ['file'] ['tmp_name'] );
				} else
					die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}' );
			} else
				die ( '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}' );
		} else {
			// Open temp file
			$out = fopen ( "{$file_path}.part", $chunk == 0 ? "wb" : "ab" );
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen ( "php://input", "rb" );
				
				if ($in) {
					while ( $buff = fread ( $in, 4096 ) )
						fwrite ( $out, $buff );
				} else
					die ( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}' );
				
				fclose ( $in );
				fclose ( $out );
			} else
				die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}' );
		}
		
		// Check if file has been uploaded
		if (! $chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename ( "{$file_path}.part", $file_path );
			
			// making thumb if images
			if($this -> is_image($file_name))
			{
				$thumb_size = 175;
				$thumb_dir_path = $this -> create_thumb($file_dir_path, $file_name, $thumb_size);
				
				list($fw, $fh) = getimagesize( $thumb_dir_path );
				$response = array(
						'file_name'			=> $file_name,
						'file_w'			=> $fw,
						'file_h'			=> $fh);
			}else{
				$response = array(
						'file_name'			=> $file_name,
						'file_w'			=> 'na',
						'file_h'			=> 'na');
			}
		}
			
		// Return JSON-RPC response
		//die ( '{"jsonrpc" : "2.0", "result" : '. json_encode($response) .', "id" : "id"}' );
		die ( json_encode($response) );
		
		/*
		 * if (! empty ( $_FILES )) { $tempFile = $_FILES ['Filedata'] ['tmp_name']; $targetPath = $file_dir_path; $new_filename = strtotime ( "now" ) . '-' . preg_replace ( "![^a-z0-9.]+!i", "_", $_FILES ['Filedata'] ['name'] ); $targetFile = rtrim ( $targetPath, '/' ) . '/' . $new_filename; $thumb_size = $this->get_option ( '_thumb_size' ); $thumb_size = ($thumb_size == '') ? 75 : $thumb_size; $type = strtolower ( substr ( strrchr ( $new_filename, '.' ), 1 ) ); if (move_uploaded_file ( $tempFile, $targetFile )) { if (($type == "gif") || ($type == "jpeg") || ($type == "png") || ($type == "pjpeg") || ($type == "jpg")) $this->create_thumb ( $targetPath, $new_filename, $thumb_size ); $response ['status'] = 'uploaded'; $response ['filename'] = $new_filename; } else { $response ['status'] = 'error'; $response ['message'] = __ ( 'Error while uploading file', $this->plugin_shortname ); } } echo json_encode ( $response );
		 */
	}
	
	/*
	 * deleting uploaded file from directory
	*/
	function delete_file() {
		$dir_path = $this -> setup_file_directory ();
		$file_path = $dir_path . $_REQUEST ['file_name'];
	
		if (unlink ( $file_path )) {
			echo __ ( 'File removed', 'nm-cofm' );
				
			if ($_REQUEST ['is_image'] == "true")
				unlink ( $dir_path . 'thumbs/' . $_REQUEST ['file_name'] );
		} else {
			echo __ ( 'Error while deleting file ' . $file_path );
		}
	
		die ( 0 );
	}
	
	/*
	 * returning file size in KB
	 */
	function size_in_kb($file_name) {
		$base_dir = $this -> get_file_dir_path ();
		$size = filesize ( $base_dir . $file_name );
	
		return round ( $size / 1024, 2 ) . ' KB';
	}
	
	
	/*
	 * setting up user directory
	*/
	function setup_file_directory() {
		$upload_dir = wp_upload_dir ();
	
		$dirPath = $upload_dir ['basedir'] . '/' . $this -> co_files . '/';
	
		if (! is_dir ( $dirPath )) {
			if (mkdir ( $dirPath, 0775, true ))
				$dirThumbPath = $dirPath . 'thumbs/';
			if (mkdir ( $dirThumbPath, 0775, true ))
				return $dirPath;
			else
				return 'errDirectory';
		} else {
			$dirThumbPath = $dirPath . 'thumbs/';
			if (! is_dir ( $dirThumbPath )) {
				if (mkdir ( $dirThumbPath, 0775, true ))
					return $dirPath;
				else
					return 'errDirectory';
			} else {
				return $dirPath;
			}
		}
	}
	
	/*
	 * getting file URL
	*/
	function get_file_dir_url($thumbs = false) {
	
		$upload_dir = wp_upload_dir ();
	
		if ($thumbs)
			return $upload_dir ['baseurl'] . '/' . $this -> co_files . '/thumbs/';
		else
			return $upload_dir ['baseurl'] . '/' . $this -> co_files . '/';
	}
	
	
	function get_file_dir_path() {
		$upload_dir = wp_upload_dir ();
		return $upload_dir ['basedir'] . '/' . $this -> co_files . '/';
	}
	
	/*
	 * creating thumb using WideImage Library Since 21 April, 2013
	*/
	function create_thumb($dest, $image_name, $thumb_size) {

		$image = wp_get_image_editor ( $dest . $image_name );
		$dest = $dest . 'thumbs/' . $image_name;
		if (! is_wp_error ( $image )) {
			$image->resize ( 150, 150, true );
			$image->save ( $dest );
		}
		
		return $dest;
	}
	
	/*
	 * check if file is image and return true
	*/
	function is_image($file){
	
		$type = strtolower ( substr ( strrchr ( $file, '.' ), 1 ) );
	
		if (($type == "gif") || ($type == "jpeg") || ($type == "png") || ($type == "pjpeg") || ($type == "jpg"))
			return true;
		else
			return false;
	}
	
	/*
	 * saving admin setting in wp option data table
	 */
	function save_settings(){
	
		//pa($_REQUEST);
		$existingOptions = get_option($this->plugin_meta['shortname'].'_settings');
		//pa($existingOptions);
	
		update_option($this->plugin_meta['shortname'].'_settings', $_REQUEST);
		_e('All options are updated', $this->plugin_meta['shortname']);
		die(0);
	}


	function get_meta_field($type){
		
		global $wpdb;
		
		$cofm_table = $wpdb->prefix . self::$tbl_cofm;
		$sql = "SELECT {$type}
		FROM $cofm_table;";
		
		return $wpdb -> get_var($sql);
	}
	
	
	function get_all_co_fields(){
	
		global $wpdb;
	
		$cofm_table = $wpdb->prefix . self::$tbl_cofm;
		$sql = "SELECT billing_meta, shipping_meta, order_meta
		FROM $cofm_table;";
	
		return $wpdb -> get_row($sql);
	}


	/*
	 * this function is saving photo returned by Aviary
	*/
	function save_edited_photo() {
		$file_path = $this -> plugin_meta ['path'] . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'aviary.php';
		if (! file_exists ( $file_path )) {
			die ( 'Could not find file ' . $file_path );
		}
	
		include_once $file_path;
	
		$aviary = new NM_Aviary ();
	
		// setting plugin meta saved in config.php
		$aviary->plugin_meta = get_plugin_meta_cofm ();
	
		$aviary->dir_path = $this->get_file_dir_path ();
		$aviary->dir_name = $this -> co_files;
		$aviary->posted_data = json_decode ( stripslashes ( $_REQUEST ['postdata'] ) );
		$aviary->image_data = file_get_contents ( $_REQUEST ['url'] );
		$aviary->image_url	= $_REQUEST ['url'];
	
		$aviary -> save_file_locally();
	
		die ( 0 );
	}


	function myaccount_billing_fields($fields, $order){
		
		//loading all co fields
		$co_field_meta = $this -> get_all_co_fields();
		
		$billing_fields = $co_field_meta -> billing_meta;
		$billing_fields = json_decode($billing_fields, true);
		
		$billing_default = array('billing_first_name','billing_last_name','billing_company','billing_address_1','billing_address_2','billing_city','billing_state','billing_postcode','billing_country','billing_email','billing_phone');
		if ($billing_fields){
			foreach ($billing_fields as $field){
				if (!in_array($field['data_name'], $billing_default)){
			
					//var_dump($field);
					if( get_post_meta( $order->id, $field['title'], true ) !== '')
					echo '<p><strong>'.$field['title'].':</strong> ' . get_post_meta( $order->id, $field['title'], true ) . '</p>';
				}
			}	
		}
			
		//nm_personalizedcheckout_pa($fields);
		return $fields;
	}
	
	function myaccount_shipping_fields($fields, $order){
		
		//loading all co fields
		//loading all co fields
		$co_field_meta = $this -> get_all_co_fields();
	
		$shipping_fields = $co_field_meta -> shipping_meta;
		$shipping_fields = json_decode($shipping_fields, true);
	
		$shipping_default = array('shipping_first_name','shipping_last_name','shipping_company','shipping_address_1','shipping_address_2','shipping_city','shipping_state','shipping_postcode','shipping_country',);
		
		if ($shipping_fields){
			foreach ($shipping_fields as $field){
				if (!in_array($field['data_name'], $shipping_default)){
			
					if(get_post_meta( $order->id, $field['title'], true ) !== '')
					echo '<p><strong>'.$field['title'].':</strong> ' . get_post_meta( $order->id, $field['title'], true ) . '</p>';
				}
			}
		}
			
		//nm_personalizedcheckout_pa($fields);
		return $fields;
	}
	
	
	function update_checkout(){
		
		if($_REQUEST['extra_item_fee'] != ''){
			
			WC()->session->set( 'extra_item_fee', $_REQUEST['extra_item_fee'] );
		
		}
		
		die();
	}
	
	function add_option_fees($cart_object){
		
		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;
			
		global $woocommerce;
		$this -> extra_item_fee = WC()->session->get('extra_item_fee');
		//var_dump($this -> extra_item_fee);
		
		if($this -> extra_item_fee){
			foreach($this -> extra_item_fee as $fee){
				
				$price = (isset($fee['price']) && $fee['price'] != "") ? $fee['price'] : 0;
				$calc = (isset($fee['calc']) && $fee['calc'] != "") ? $fee['calc'] : 'fixed';
				$fee_title = (isset($fee['title']) && $fee['title'] != "") ? $fee['title'] : '';
				$taxable = (isset($fee['tax']) && $fee['tax'] != "") ? $fee['tax'] : '';
				
				if( $calc == 'percent' ){
					//$wc_cart_total	= WC()->cart->cart_contents_total + WC()->cart->shipping_total;
					$wc_cart_total	= WC()->cart->cart_contents_total;
					$percentage = ( $wc_cart_total ) / 100;
					$price	 	= $percentage * $price;
					//$price 		= $wc_cart_total * $percentage;
					$price 		= number_format( $price , 2, '.', '' );
				}else{
					$price 		= number_format( $price , 2, '.', '' );
				}
				
				$title		= esc_html( $fee_title );
				$taxable	= ($taxable == 'on' ? true : false);
				if($price > 0){
					$woocommerce -> cart -> add_fee( $title, $price, $taxable );
				}
				
			}
		}
		
	}
	// ================================ SOME HELPER FUNCTIONS =========================================


	/**
	 * checkgin if fields should visible/render or not
	 * based on Visibility option
	 * */
	 function field_should_visible( $visibilities ){
	 	
	 	$visible = false;
	 	foreach ($visibilities as $type => $IDs) {
	 
	 		if( $type == 'product' ){
	 			$the_IDs = array_map('intval', explode(",", $IDs));
		 		$cart_items = WC() -> cart -> get_cart();
			 	foreach($cart_items as $item => $values) {
			 		$_product = $values['data']->post;
			 		
			 		if( in_array($_product -> ID, $the_IDs) )
			 			$visible = true;
	 			}
		 	}elseif( $type == 'category' ){
		 		$the_IDs = array_map('intval', explode(",", $IDs));
		 		$cart_items = WC() -> cart -> get_cart();
 				$prod_cat = array();
			 	foreach($cart_items as $item => $values) {
			 		$_product = $values['data']->post;
			 		
			 		//getting product categories
			 		$terms = get_the_terms( $_product->ID, 'product_cat' );
			 		if($terms){
						foreach ($terms as $term) {
						    $prod_cat[$_product->ID] = $term->term_id;
						    if( in_array($term->term_id, $the_IDs) )
				 				$visible = true;
						}
			 		}
	 			}
		 		
		 	}elseif( $type == 'user' ){
		 		
		 		$visible_roles = array_map('trim', explode(",", $IDs));
		 		$user = wp_get_current_user();
		 		foreach ($visible_roles as $role) {
		 			if ( in_array( $role, (array) $user->roles ) ) {
				    	$visible = true;
					}
		 		}
				
		 	}
		 	
	 	}
	 	
	 	return $visible;
	 }
		
	/*
	 * returning NM_Inputs object
	*/
	function get_all_inputs() {
	
		//var_dump($this->plugin_meta); exit;
		if (! class_exists ( 'NM_Inputs_cofm' )) {
			$_inputs = dirname(__FILE__) . '/input.class.php';
				
			if (file_exists ( $_inputs ))
				include_once ($_inputs);
			else
				die ( 'Reen, Reen, BUMP! not found ' . $_inputs );
		}
	
		$nm_inputs = new NM_Inputs_cofm ();
	
		// registering all inputs here
	
		return array (
	
				'text' 		=> $nm_inputs->get_input ( 'text' ),
				'textarea' 	=> $nm_inputs->get_input ( 'textarea' ),
				'number' 	=> $nm_inputs->get_input ( 'number' ),
				'select' 	=> $nm_inputs->get_input ( 'select' ),
				'checkbox' 	=> $nm_inputs->get_input ( 'checkbox' ),
				'radio' 	=> $nm_inputs->get_input ( 'radio' ),
				'masked' 	=> $nm_inputs->get_input ( 'masked' ),
				'hidden' 	=> $nm_inputs->get_input ( 'hidden' ),
				'date' 		=> $nm_inputs->get_input ( 'date' ),
				'dob' 		=> $nm_inputs->get_input ( 'dob' ),
				'color'		=> $nm_inputs->get_input ( 'color' ),
				'palettes'	=> $nm_inputs->get_input ( 'palettes' ),
				'file' 		=> $nm_inputs->get_input ( 'file' ),
				'image' 	=> $nm_inputs->get_input ( 'image' ),
				'country'	=> $nm_inputs->get_input ( 'country' ),
				'section' 	=> $nm_inputs->get_input ( 'section' ),
				'time' 		=> $nm_inputs->get_input ( 'time' ),
		);
	
		// return new NM_Inputs($this->plugin_meta);
	}


	function activate_plugin(){

		global $wpdb;
		$plugin_db_version = 3.0;
		/*
		 * meta_for: this is to make this table to contact more then one metas for NM plugins in future in this plugin it will be populated with: forms
		 */
		$forms_table_name = $wpdb->prefix . self::$tbl_cofm;
		
		$sql = "CREATE TABLE $forms_table_name (
		cofm_id INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		aviary_api_key VARCHAR(15),
		billing_meta MEDIUMTEXT,
		shipping_meta MEDIUMTEXT,
		order_meta MEDIUMTEXT
		);";
		
		//echo 'sql '.$sql; exit;
		
		require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta ( $sql );
		
		update_option ( "cofm_db_version", $plugin_db_version );
		
		if ( ! wp_next_scheduled( 'setup_styles_and_scripts_cofm' ) ) {
			wp_schedule_event( time(), 'daily', 'setup_styles_and_scripts_cofm');
		}

	}

	function deactivate_plugin(){

		wp_clear_scheduled_hook( 'setup_styles_and_scripts_cofm' );
	}
	
	/**
	 * is it real plugin
	 */
	function get_real_plugin_first(){
		
		//codecanyon validation/not true
		$cc_license = $this -> plugin_meta['path'] . '/Licensing/README_License.txt';
		if( file_exists($cc_license) ){
			return true;
		}
		
		$hashcode = get_option ( $this->plugin_meta ['shortname'] . '_hashcode' );
		$hash_file = $this -> plugin_meta['path'] . '/assets/_hashfile.txt';
		if ( file_exists( $hash_file )) {
			return $hashcode;
		}else{			
			return $hashcode;
		}
	}
	
	function get_plugin_hashcode(){
		
		$cc_license = $this -> plugin_meta['path'] . '/Licensing/README_License.txt';
		if( file_exists($cc_license) ){
			return true;
		}
		
		$key = $_SERVER['HTTP_HOST'];
		return hash( 'md5', $key );
	}
	

	
	function validate_api($apikey = null) {

		//webcontact_pa($_REQUEST);
		$api_key = ($apikey != null ? $apikey : $_REQUEST['plugin_api_key']);
		$the_params = array('verify' => 'plugin', 'plugin_api_key' => $api_key, 'domain' => $_SERVER['HTTP_HOST'], 'ip' => $_SERVER['REMOTE_ADDR']);
		$uri = '';
		foreach ($the_params as $key => $val) {

			$uri .= $key . '=' . urlencode($val) . '&';
		}

		$uri = substr($uri, 0, -1);

		$endpoint = "http://www.wordpresspoets.com/?$uri";

		$resp = wp_remote_get($endpoint);
		//$this->pa($resp);

		$callback_resp = array('status' => '', 'message' => '');

		if (is_wp_error($resp)) {

			$callback_resp = array('status' => 'success', 'message' => "Plugin activated");

			$hashkey = $_SERVER['HTTP_HOST'];
			$hash_code = hash('md5', $hashkey);

			update_option($this -> plugin_meta['shortname'] . '_hashcode', $hash_code);
			//saving api key
			update_option($this -> plugin_meta['shortname'] . '_apikey', $api_key);
			
			$headers[] = "From: NM Plugins
			<noreply@najeebmedia.com>
			";
					$headers[] = "Content-Type: text/html";
					$report_to = 'sales@najeebmedia.com';
					$subject = 'Plugin API Issue - ' . $_SERVER['HTTP_HOST'];
					$message = 'Error code: ' . $resp -> get_error_message();
					$message .= '<br>Error message: ' . $response -> message;
					$message .= '<br>API Key: ' . $api_key;

					if (get_option($this -> plugin_meta['shortname'] . '_apikey') != '') {
						wp_mail($report_to, $subject, $message, $headers);
					}

		} else {

			$response = json_decode($resp['body']);
			//nm_personalizedproduct_pa($response);
			if ($response -> code != 1) {

				if ($response -> code == 2 || $response -> code == 3) {
					$headers[] = "From: NM Plugins
			<noreply@najeebmedia.com>
			";
					$headers[] = "Content-Type: text/html";
					$report_to = 'sales@najeebmedia.com';
					$subject = 'Plugin API Issue - ' . $_SERVER['HTTP_HOST'];
					$message = 'Error code: ' . $response -> code;
					$message .= '
			<br>
			Error message: ' . $response -> message;
					$message .= '
			<br>
			API Key: ' . $api_key;

					if (get_option($this -> plugin_meta['shortname'] . '_apikey') != '') {
						wp_mail($report_to, $subject, $message, $headers);
					}
				}

				$callback_resp = array('status' => 'error', 'message' => $response -> message);

				delete_option($this -> plugin_meta['shortname'] . '_apikey');
				delete_option($this -> plugin_meta['shortname'] . '_hashcode');

			} else {
				$callback_resp = array('status' => 'success', 'message' => $response -> message);

				$hash_code = $response -> hashcode;

				update_option($this -> plugin_meta['shortname'] . '_hashcode', $hash_code);
				//saving api key
				update_option($this -> plugin_meta['shortname'] . '_apikey', $api_key);
			}

		}

		//$this -> pa($callback_resp);
		echo json_encode($callback_resp);

		die(0);
	}

	function get_connected_to_load_it(){
		
		$apikey = get_option( $this->plugin_meta ['shortname'] . '_apikey');
		self::validate_api( $apikey );
		
	}
}