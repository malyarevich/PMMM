<?php
/*
 * this file is managinga all checkout fieds in three sections
 * 1- Billing
 * 2- Shipping
 * 3- More
 */

?>

<style>
<!--
.clearfix{
	clear: both;
	display:block;
}

#nmcontact-form-meta{
	width: 100%;
}

#container-1 {
	width: 40%;
	border: 1px solid #E6E6E6;
}

.s-font {
	font-size: 11px;
	color: #AFAFAF;
	margin: 0px;
}
.headings {
	vertical-align: top;
	padding-top: 5px;
	font-weight: bold;
	width: 20%;
}

.form-meta-bttons{
	width: 20%;
	border: 1px solid;
	padding: 7px;
	float: left;
}

.form-meta-bttons table{
	display:none;
}

.form-meta-bttons > p{border: none; text-align: justify}

.form-meta-bttons .top-heading-text { float:left;padding:8px 0 0 5px;cursor: move;display: block;width: 90%;}
.form-meta-bttons .top-heading-icons { float:right;}

.form-meta-setting .top-heading-text{ float:left;padding:0;cursor: move;display: block;width: 90%;}
.form-meta-setting .top-heading-icons{ float:right;}


.form-meta-setting{
	float: right;
	width: 75%;
	/* border: 1px solid #d3d3d3; */
	min-height: 350px;
}

.form-meta-setting table{
display: table-cell;
}

.form-meta-setting table tr{line-height: 15px}

.form-meta-setting h3{
padding: 5px;
font-weight: bold;
cursor: pointer;
}

.form-meta-setting ul{
min-height: 300px;
/* border: 1px solid; */
}

.input-type-item{
text-align: center;
line-height: 0px;
cursor: move;
}

.form-meta-setting .input-item-heading{
	cursor: move;
	border-bottom-color: #dfdfdf;
text-shadow: #fff 0 1px 0;
-webkit-box-shadow: 0 1px 0 #fff;
box-shadow: 0 1px 0 #fff;
background: #f1f1f1;
background-image: -webkit-gradient(linear,left bottom,left top,from(#ececec),to(#f9f9f9));
background-image: -webkit-linear-gradient(bottom,#ececec,#f9f9f9);
background-image: -moz-linear-gradient(bottom,#ececec,#f9f9f9);
background-image: -o-linear-gradient(bottom,#ececec,#f9f9f9);
background-image: linear-gradient(to top,#ececec,#f9f9f9);
}

.table-column-title{width: 20%}
.table-column-input{width: 25%}
.table-column-desc{font-size: 11px;color: #AFAFAF;}

table#form-main-settings td{padding: 10px 0};


/*By Rameez*/
.ui-accordion .ui-accordion-header {
  cursor: pointer;
  position: relative;
  margin-top: 1px;
  zoom: 1;
}
#cofm-tabs .ui-accordion-content {
    max-width: 100%;
    background-color: #ffffff;
    color: #000;
    padding: 10px;
    border: 1px solid #389abe;
}
#cofm-tabs .ui-accordion-header {
    background-color: #ccc;
    margin: 0px;
	color: #ffffff;
	outline: none;
}
#cofm-tabs .ui-accordion-header, #nm-input-types li, .ui-draggable-dragging h3 {
    background-color: #389abe;
    background-image: -moz-linear-gradient(top,  #389abe 0%, #2a7b99 100%); /* FF3.6+ */
    background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#389abe), color-stop(100%,#2a7b99)); /* Chrome,Safari4+ */
    background-image: -webkit-linear-gradient(top,  #389abe 0%,#2a7b99 100%); /* Chrome10+,Safari5.1+ */
    background-image: -o-linear-gradient(top,  #389abe 0%,#2a7b99 100%); /* Opera 11.10+ */
    background-image: -ms-linear-gradient(top,  #389abe 0%,#2a7b99 100%); /* IE10+ */
    background-image: linear-gradient(to bottom,  #389abe 0%,#2a7b99 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#389abe', endColorstr='#2a7b99',GradientType=0 ); /* IE6-9 */
}
.nm-state-highlight {
	height: 30px;
	background-color: yellow !important;
	width: 100%;
}
#nm-input-types li h3 {
    margin: 0px;
    color: #fff;
    font-size: 14px;
    padding: 7px;
}
.ui-icon {
	background-image: none !important;
	text-indent: 0 !important;
    overflow: visible !important;
}
.ui-draggable-dragging table {
	display: none;
}
.ui-draggable-dragging h3 {
	padding: 10px;
}
//-->
</style>

<h2>
	<?php echo $this->plugin_meta['name']?>
</h2>
<p><?php _e('Manage all checkout fields below.', 'nm-cofm')?></p>

<div id="cofm-tabs">
  <ul>
    <li><a href="#billing-f"><?php _e('Billing address fields', 'nm-confm')?></a></li>
    <li><a href="#shipping-f"><?php _e('Shipping address fields', 'nm-confm')?></a></li>
    <li><a href="#order-f"><?php _e('Order fields', 'nm-confm')?></a></li>
  </ul>
  
  <!-- Billing section field manager -->
  <div id="billing-f">
    <p>
    <?php _e('Manage billing fields below:', 'nm-cofm')?>
    
    <button class="button" onclick="load_default_fields('billing')"><?php _e('Reset to default billing fields', 'nm-cofm')?></button>
    <button class="button-primary button" onclick="save_all_changes('billing')"><?php _e('Save all changes', 'nm-cofm')?></button>
    <span id="billing-fields-updating"></span> 
    </p>
    
    <div id="billing-section-fields">
    
    	<div class="form-meta-bttons">
			<p>
		<?php _e('select input type below and drag it on right side. Then set more options', 'nm-cofm')?>
		</p>

			<ul id="nm-input-types">
		<?php
		
		$billing_meta = $this -> get_meta_field('billing_meta');
		$billing_meta = json_decode($billing_meta, true);
		
		$supported_types = array('text', 'textarea', 'select');
		
		foreach ( $this -> inputs as $type => $meta ) {
			
			if(in_array($type, $supported_types)){

				echo '<li class="input-type-item" data-inputtype="' . $type . '">';
				echo '<div><h3><span class="top-heading-text">' . $meta -> title . '</span>';
				echo '<span class="top-heading-icons ui-icon dashicons dashicons-menu"></span>';
				// echo '<span class="top-heading-icons ui-icon-placehorder"></span>';
				echo '<span style="clear:both;display:block"></span>';
				echo '</h3>';
				
				// this function Defined below
				echo render_input_settings ( $meta -> settings, false, 'billing' );
				
				echo '</div></li>';
			}
		}
		?>
		</ul>
		</div>


		<div class="form-meta-setting" class="postbox-container">

			<div class="postbox">
				<h3>
					<span style="float: left"><?php _e('Drag Checkout Form Fields Here', 'nm-cofm')?></span>
					<span style="float: right"><span style="float: right"
						title="<?php _e('Collapse all', 'nm-cofm')?>"
						class="ui-icon ui-icon-circle-triangle-n"></span><span
						title="<?php _e('Expand all', 'nm-cofm')?>"
						class="ui-icon ui-icon-circle-triangle-s"></span></span> <span
						class="clearfix"></span>
				</h3>
				<div class="inside" style="background-color: #fff;">
					<ul id="billing-meta-input-holder">
					<?php render_existing_form_meta($billing_meta, $this -> inputs)?>
					</ul>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>
	</div>  
	
	
  </div>
  
  <!-- ENDS: Billing section field manager -->
  
  
  
  <!-- Shipping section field manager -->
  <div id="shipping-f">
    <p>
    <?php _e('Manage shipping fields below:', 'nm-cofm')?>
    
    
    <button class="button" onclick="load_default_fields('shipping')"><?php _e('Reset to default shipping fields', 'nm-cofm')?></button>
    <button class="button-primary button" onclick="save_all_changes('shipping')"><?php _e('Save all changes', 'nm-cofm')?></button>
    <span id="shipping-fields-updating"></span>  
    </p>
    
    
    <div id="shipping-section-fields">
		<div class="form-meta-bttons">
			<p>
		<?php _e('select input type below and drag it on right side. Then set more options', 'nm-cofm')?>
		
		</p>

			<ul id="nm-input-types">
		<?php
		
		$shipping_meta = $this -> get_meta_field('shipping_meta');
		$shipping_meta = json_decode($shipping_meta, true);
		
		$supported_types = array('text', 'textarea', 'select');
		
		foreach ( $this -> inputs as $type => $meta ) {
			
			if(in_array($type, $supported_types)){
			
				echo '<li class="input-type-item" data-inputtype="' . $type . '">';
				echo '<div><h3><span class="top-heading-text">' . $meta -> title . '</span>';
				echo '<span class="top-heading-icons ui-icon dashicons dashicons-menu"></span>';
				// echo '<span class="top-heading-icons ui-icon-placehorder"></span>';
				echo '<span style="clear:both;display:block"></span>';
				echo '</h3>';
				
				// this function Defined below
				echo render_input_settings ( $meta -> settings, false, 'shipping' );
				
				echo '</div></li>';
			}
		}
		?>
		</ul>
		</div>


		<div class="form-meta-setting" class="postbox-container">

			<div class="postbox">
				<h3>
					<span style="float: left"><?php _e('Drage form fields here', 'nm-cofm')?></span>
					<span style="float: right"><span style="float: right"
						title="<?php _e('Collapse all', 'nm-cofm')?>"
						class="ui-icon ui-icon-circle-triangle-n"></span><span
						title="<?php _e('Expand all', 'nm-cofm')?>"
						class="ui-icon ui-icon-circle-triangle-s"></span></span> <span
						class="clearfix"></span>
				</h3>
				<div class="inside" style="background-color: #fff;">
					<ul id="shipping-meta-input-holder">
					<?php render_existing_form_meta($shipping_meta, $this -> inputs)?>
					</ul>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>
	</div>  
  </div>
  
  <!-- ENDS: Shipping section field manager -->
  
  <!-- STARTS: Order section field manager -->
  
  <div id="order-f">
    
    <p>
    <?php _e('Manage order fields below:', 'nm-cofm')?>
    
    
    <button class="button" onclick="load_default_fields('order')"><?php _e('Reset to efault order field', 'nm-cofm')?></button>
    <button class="button-primary button" onclick="save_all_changes('order')"><?php _e('Save all changes', 'nm-cofm')?></button>
    <span id="order-fields-updating"></span>  
    </p>
    
    
    <div id="order-section-fields">
		<div class="form-meta-bttons">
			<p>
		<?php _e('select input type below and drag it on right side. Then set more options', 'nm-cofm')?>
		
		</p>

			<ul id="nm-input-types">
		<?php
		
		$order_meta = $this -> get_meta_field('order_meta');
		$order_meta = json_decode($order_meta, true);
		
		foreach ( $this -> inputs as $type => $meta ) {
			
			echo '<li class="input-type-item" data-inputtype="' . $type . '">';
			echo '<div><h3><span class="top-heading-text">' . $meta -> title . '</span>';
			echo '<span class="top-heading-icons ui-icon dashicons dashicons-menu"></span>';
			// echo '<span class="top-heading-icons ui-icon-placehorder"></span>';
			echo '<span style="clear:both;display:block"></span>';
			echo '</h3>';
			
			// this function Defined below
			echo render_input_settings ( $meta -> settings, false, 'order' );
			
			echo '</div></li>';
			// echo '<div><p>'.$data['desc'].'</p></div>';
		}
		?>
		</ul>
		</div>


		<div class="form-meta-setting" class="postbox-container">

			<div class="postbox">
				<h3>
					<span style="float: left"><?php _e('Drage form fields here', 'nm-cofm')?></span>
					<span style="float: right"><span style="float: right"
						title="<?php _e('Collapse all', 'nm-cofm')?>"
						class="ui-icon ui-icon-circle-triangle-n"></span><span
						title="<?php _e('Expand all', 'nm-cofm')?>"
						class="ui-icon ui-icon-circle-triangle-s"></span></span> <span
						class="clearfix"></span>
				</h3>
				<div class="inside" style="background-color: #fff;">
					<ul id="order-meta-input-holder">
					<?php render_existing_form_meta($order_meta, $this -> inputs)?>
					</ul>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>
	</div>  
  </div>
  
  <!-- ENDS: Order section field manager -->
</div>

<!-- ui dialogs -->
<div id="remove-meta-confirm"
	title="<?php _e('Are you sure?', 'nm-confm')?>">
	<p>
		<span class="ui-icon ui-icon-alert"
			style="float: left; margin: 0 7px 20px 0;"></span>
  <?php _e('Are you sure to remove this input field?', 'nm-personalizedproduct')?></p>
</div>
<?php 


function render_input_settings($settings, $values = '', $co_type='') {

	$setting_html = '<table>';
	foreach ( $settings as $meta_type => $data ) {

		// nm_personalizedproduct_pa($data);
		$colspan = ($data ['type'] == 'html-conditions' ? 'colspan="2"' : '');
			
			
		$setting_html .= '<tr>';
		$setting_html .= '<td class="table-column-title">' . $data ['title'] . '</td>';
		
		$data_options = (isset( $data ['options'] ) ? $data ['options'] : '' );
		if ($values){
			$meta_value = (isset( $values [$meta_type] ) ? $values [$meta_type] : '' );
			$setting_html .= '<td '.$colspan.' class="table-column-input" data-type="' . $data ['type'] . '" data-name="' . $meta_type . '">' . render_input_types ( $data ['type'], $meta_type, $meta_value, $data_options ) . '</td>';
		}else{
			
			$prefixed = ($meta_type == 'data_name') ? $co_type . '_' : null;
			$setting_html .= '<td '.$colspan.' class="table-column-input" data-type="' . $data ['type'] . '" data-name="' . $meta_type . '">' . render_input_types ( $data ['type'], $meta_type, $prefixed, $data_options ) . '</td>';
		}
			

		//removing the desc column for type: html-conditions
		if ($data ['type'] != 'html-conditions') {
			$setting_html .= '<td class="table-column-desc">' . $data ['desc'] . '</td>';;
		}

		$setting_html .= '</tr>';
	}

	$setting_html .= '</table>';

	return $setting_html;
}

/*
 * this function is rendring input field for settings
*/
function render_input_types($type, $name, $value = '', $options = '') {
	global $cofm;
	$html_input = '';

	// var_dump($value);
	if(!is_array($value))
		$value = stripslashes($value);

	switch ($type) {

		case 'text' :
			$html_input .= '<input type="text" name="' . $name . '" value="' . esc_html( $value ). '">';
			break;

		case 'textarea' :
			$html_input .= '<textarea name="' . $name . '">' . esc_html( $value ) . '</textarea>';
			break;

		case 'select' :
			$html_input .= '<select name="' . $name . '">';
			foreach ( $options as $key => $val ) {
				$selected = ($key == $value) ? 'selected="selected"' : '';
				$html_input .= '<option value="' . $key . '" ' . $selected . '>' . esc_html( $val ) . '</option>';
			}
			$html_input .= '</select>';
			break;
			
		case 'select_product' :
			$html_input .= '<select data-placeholder="'.__('Select product', 'nm-cofm').'" style="width:350px;" multiple class="chosen-select nm-wooproducts" name="' . $name . '">';
			$woo_products = get_woo_products();
			foreach ( $woo_products as $key => $val ) {
				$selected = ($key == $value) ? 'selected="selected"' : '';
				$html_input .= '<option value="' . $key . '" ' . $selected . '>' . esc_html( $val ) . '</option>';
			}
			$html_input .= '</select>';
			break;

		case 'paired' :
				
			//legacy support
			if($value){
				if( ! is_array($value) ){
					$value1[] = array('option' => $value, 'price' => '');
					$value = $value1;
				}
				foreach ($value as $option){
					$html_input .= '<div class="data-options" style="border: dashed 1px;">';
					$html_input .= '<input type="text" name="options[option]" value="'.$option['option'].'" placeholder="'.__('option','nm-cofm').'">';
					$html_input .= '<input type="text" name="options[price]" value="'.$option['price'].'" placeholder="'.__('price (if any)','nm-cofm').'">';
					$html_input	.= '<img class="add_option" src="'.$cofm->plugin_meta['url'].'/images/plus.png" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
					$html_input	.= '<img class="remove_option" src="'.$cofm->plugin_meta['url'].'/images/minus.png" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
					$html_input .= '</div>';
				}
			}else{
				$html_input .= '<div class="data-options" style="border: dashed 1px;">';
				$html_input .= '<input type="text" name="options[option]" placeholder="'.__('option','nm-cofm').'">';
				$html_input .= '<input type="text" name="options[price]" placeholder="'.__('price (if any)','nm-cofm').'">';
				$html_input	.= '<img class="add_option" src="'.$cofm->plugin_meta['url'].'/images/plus.png" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
				$html_input	.= '<img class="remove_option" src="'.$cofm->plugin_meta['url'].'/images/minus.png" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
				$html_input .= '</div>';
			}
				
			break;
				
		case 'checkbox' :
				
			if ($options) {
				foreach ( $options as $key => $val ) {
						
					parse_str ( $value, $saved_data );
					$checked = '';
					$editing_tools = (isset( $saved_data ['editing_tools'] ) ? $saved_data ['editing_tools'] : '');
					if ($editing_tools) {
						if (in_array($key, $editing_tools)) {
							$checked = 'checked="checked"';
						}
					}
					// $html_input .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
					$html_input .= '<input type="checkbox" value="' . $key . '" name="' . $name . '[]" ' . $checked . '> ' . $val . '<br>';
				}
			} else {
				$checked = ($value != '' ? 'checked = "checked"' : '' );
				$html_input .= '<input type="checkbox" name="' . $name . '" ' . $checked . '>';
			}
			break;
				
		case 'html-conditions' :
				
			// nm_personalizedproduct_pa($value);
			$rule_i = 1;
			if($value){


				$visibility_show = ($value['visibility'] == 'Show') ? 'selected="selected"' : '';
				$visibility_hide = ($value['visibility'] == 'Hide') ? 'selected="selected"' : '';
					
				$html_input	 = '<select name="condition_visibility">';
				$html_input .= '<option value="Show" '.$visibility_show.'>'.__('Show','nm-cofm').'</option>';
				$html_input .= '<option value="Hide" '.$visibility_hide.'>'.__('Hide', 'nm-cofm').'</option>';
				$html_input	.= '</select> ';
					
					
				$html_input .= __('only if', 'nm-cofm');
					
				$bound_all = ($value['bound'] == 'All') ? 'selected="selected"' : '';
				$bound_any = ($value['bound'] == 'Any') ? 'selected="selected"' : '';
					
				$html_input	.= '<select name="condition_bound">';
				$html_input .= '<option value="All" '.$bound_all.'>'.__('All','nm-cofm').'</option>';
				$html_input .= '<option value="Any" '.$bound_any.'>'.__('Any', 'nm-cofm').'</option>';
				$html_input	.= '</select> ';

				$html_input .= __(' of the following matches', 'nm-cofm');
					
					
				foreach ($value['rules'] as $condition){
						
						
					// conditional elements
					$html_input .= '<div class="webcontact-rules" id="rule-box-'.$rule_i.'">';
					$html_input .= '<br><strong>'.__('Rule # ', 'nm-cofm') . $rule_i++ .'</strong><br>';
					$html_input .= '<select name="condition_elements" data-existingvalue="'.$condition['elements'].'" onblur="load_conditional_values(this)"></select>';
						
					// is
						
					$operator_is 		= ($condition['operators'] == 'is') ? 'selected="selected"' : '';
					$operator_not 		= ($condition['operators'] == 'not') ? 'selected="selected"' : '';
					$operator_greater 	= ($condition['operators'] == 'greater then') ? 'selected="selected"' : '';
					$operator_less 		= ($condition['operators'] == 'less then') ? 'selected="selected"' : '';
						
					$html_input .= '<select name="condition_operators">';
					$html_input	.= '<option value="is" '.$operator_is.'>'.__('is','nm-cofm').'</option>';
					$html_input .= '<option value="not" '.$operator_not.'>'.__('not', 'nm-cofm').'</option>';
					$html_input .= '<option value="greater then" '.$operator_greater.'>'.__('greater then', 'nm-cofm').'</option>';
					$html_input .= '<option value="less then" '.$operator_less.'>'.__('less then', 'nm-cofm').'</option>';
					$html_input	.= '</select> ';
						
					// conditional elements values
					$html_input .= '<select name="condition_element_values" data-existingvalue="'.$condition['element_values'].'"></select>';
					$html_input	.= '<img class="add_rule" src="'.$cofm->plugin_meta['url'].'/images/plus.png" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
					$html_input	.= '<img class="remove_rule" src="'.$cofm->plugin_meta['url'].'/images/minus.png" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
					$html_input .= '</div>';
						
				}
			}else{

					
				$html_input	 = '<select name="condition_visibility">';
				$html_input .= '<option value="Show">'.__('Show','nm-cofm').'</option>';
				$html_input .= '<option value="Hide">'.__('Hide', 'nm-cofm').'</option>';
				$html_input	.= '</select> ';
					
				$html_input	.= '<select name="condition_bound">';
				$html_input .= '<option value="All">'.__('All','nm-cofm').'</option>';
				$html_input .= '<option value="Any">'.__('Any', 'nm-cofm').'</option>';
				$html_input	.= '</select> ';
					
				$html_input .= __(' of the following matches', 'nm-cofm');
				// conditional elements

				$html_input .= '<div class="webcontact-rules" id="rule-box-'.$rule_i.'">';
				$html_input .= '<br><strong>'.__('Rule # ', 'nm-cofm') . $rule_i++ .'</strong><br>';
				$html_input .= '<select name="condition_elements" onblur="load_conditional_values(this)"></select>';
					
				// is
					
				$html_input .= '<select name="condition_operators">';
				$html_input	.= '<option value="is">'.__('is','nm-cofm').'</option>';
				$html_input .= '<option value="not">'.__('not', 'nm-cofm').'</option>';
				$html_input .= '<option value="greater then">'.__('greater then', 'nm-cofm').'</option>';
				$html_input .= '<option value="less then">'.__('less then', 'nm-cofm').'</option>';
				$html_input	.= '</select> ';
					
				// conditional elements values
				$html_input .= '<select name="condition_element_values"></select>';
				$html_input	.= '<img class="add_rule" src="'.$cofm->plugin_meta['url'].'/images/plus.png" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
				$html_input	.= '<img class="remove_rule" src="'.$cofm->plugin_meta['url'].'/images/minus.png" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
				$html_input .= '</div>';
			}

			break;
				
		case 'pre-images' :
				

			//$html_input	.= '<textarea name="pre_upload_images">'.$pre_uploaded_images.'</textarea>';
			$html_input	.= '<div class="pre-upload-box">';
			$html_input	.= '<input name="pre_upload_image_button" type="button" value="'.__('Select/Upload Image', 'nm-cofm').'" />';
			//nm_personalizedcheckout_pa($value);
			if ($value) {
				foreach ($value as $pre_uploaded_image){

					$html_input .='<table>';
					$html_input .= '<tr>';
					$html_input .= '<td><img width="75" src="'.$pre_uploaded_image['link'].'">';
					$html_input .= '<input type="hidden" name="pre-upload-link" value="'.$pre_uploaded_image['link'].'"></td>';
					$html_input .= '<td><input style="width:100px" type="text" value="'.stripslashes($pre_uploaded_image['title']).'" name="pre-upload-title"><br>';
					$html_input .= '<input style="width:100px" type="text" value="'.stripslashes($pre_uploaded_image['price']).'" name="pre-upload-price"><br>';
					$html_input .= '<input style="width:100px; color:red" name="pre-upload-delete" type="button" class="button" value="Delete"><br>';
					$html_input .= '</td></tr>';
					$html_input .= '</table><br>';

				}
				//$pre_uploaded_images = $value;
			}

			$html_input .= '</div>';
				
			break;
	}

	return $html_input;
}


/*
 * this function is rendering the existing form meta
*/
function render_existing_form_meta($product_meta, $types) {
	if ($product_meta) {
		foreach ( $product_meta as $key => $meta ) {
				
			$type = $meta ['type'];
				
			// nm_personalizedproduct_pa($meta);
				
			echo '<li data-inputtype="' . $type . '"><div>';
			echo '<h3><span class="top-heading-text">' . $meta ['title'] . ' (' . $type . ')</span>';
			echo '<span class="top-heading-icons dashicons dashicons-trash"></span>';
			// echo '<span class="top-heading-icons ui-icon ui-icon-trash"></span>';
			echo '<span style="clear:both;display:block"></span></h3><div>';
				
			echo render_input_settings ( $types[$type] -> settings, $meta );
				
			echo '</div></div></li>';
		}
	}
}

?>
<script type="text/javascript">
<!--


//-->
</script>
