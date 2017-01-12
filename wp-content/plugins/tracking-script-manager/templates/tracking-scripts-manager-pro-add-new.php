<?php 
	if ( ! defined( 'ABSPATH' ) ) { 
    	exit; // Exit if accessed directly
	}
?>
<div class="add_new_section script_section">
	<div id="add_page_tracking_script_location" class="add_tracking_scripts add_tracking_scripts_row">
    	<label for="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT_LOCATION; ?>"><?php _e( 'Identify the location where the tracking script should appear:', TRACKING_SCRIPT_TEXTDOMAIN ); ?></label>
		<div class="radio_buttons">
			<input type="radio" name="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT_LOCATION; ?>" value="header" checked="checked"><?php _e( 'Header', TRACKING_SCRIPT_TEXTDOMAIN ); ?>
			<input type="radio" name="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT_LOCATION; ?>" value="footer"><?php _e( 'Footer', TRACKING_SCRIPT_TEXTDOMAIN ); ?>
		</div>
	</div>
	<div id="add_page_tracking_script_global" class="add_tracking_scripts add_tracking_scripts_row">
    	<label for="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT_GLOBAL; ?>"><?php _e( 'Will the tracking script be global, i.e., does it need to appear on all pages?', TRACKING_SCRIPT_TEXTDOMAIN ); ?></label>
		<div class="radio_buttons">
			<input type="radio" name="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT_GLOBAL; ?>" value="yes" checked="checked"><?php _e( 'Yes', TRACKING_SCRIPT_TEXTDOMAIN ); ?>
			<input type="radio" name="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT_GLOBAL; ?>" value="no"><?php _e( 'No', TRACKING_SCRIPT_TEXTDOMAIN ); ?>
		</div>
	</div>
	<div id="add_page_tracking_script_post_type" class="add_tracking_scripts add_tracking_scripts_row" style="display: none;">
		<label class="single_line_label"><?php _e( 'Choose the content type:', TRACKING_SCRIPT_TEXTDOMAIN ); ?></label>
		<select name="tracking_scripts_new_post_type" id="tracking_scripts_new_post_type">
			<option value="none" id="none"><?php _e( 'Choose Post Type', TRACKING_SCRIPT_TEXTDOMAIN ); ?></option>
			<?php
				$first_post = null;
				$post_types = get_post_types(array('public' => true, 'show_ui' => true), 'objects');
				foreach($post_types as $post_type) {
					if(!$first_post) { $first_post = $post_type; }
			?>
				<option value="<?php echo $post_type->name; ?>" id="<?php echo $post_type->name; ?>"><?php echo ucwords($post_type->labels->name); ?></option>
			<?php
				}
			?>
		</select>
	</div>
	<div id="add_page_tracking_script_post" class="add_tracking_scripts add_tracking_scripts_row" style="display: none;">
		<label class="single_line_label"><?php _e( 'Choose the specific location:', TRACKING_SCRIPT_TEXTDOMAIN ); ?></label>
		<select name="tracking_scripts_new_post" id="tracking_scripts_new_post"></select>
	</div>
	<input type="hidden" name="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT_ID; ?>" id="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT_ID; ?>"/>
	<div id="add_page_tracking_script_code" class="add_tracking_scripts add_tracking_scripts_row">
		<label for="new_page_script_name" class="single_line_label"><?php _e( 'Script Name:', TRACKING_SCRIPT_TEXTDOMAIN ); ?></label>
		<input type="text" name="new_page_script_name"/>
		<label for="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT; ?>" class="single_line_label"><?php _e( 'Script Content:', TRACKING_SCRIPT_TEXTDOMAIN ); ?></label>
		<textarea rows="5" cols="40" name="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT; ?>" id="<?php echo WP_NEW_PAGE_TRACKING_SCRIPT; ?>" style="font-weight: normal;"></textarea>
	</div>
</div>
<?php submit_button('Add Script', 'primary', 'save_new_tracking_codes'); ?>
<input type="hidden" name="action" value="save_new_tracking_codes">