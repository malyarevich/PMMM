<?php 
	if ( ! defined( 'ABSPATH' ) ) { 
    	exit; // Exit if accessed directly
	}
?>
<div class="script_section">
<h2><?php _e( 'Header Scripts', TRACKING_SCRIPT_TEXTDOMAIN ); ?></h2>
<?php $header_scripts = unserialize(get_option(WP_HEADER_TRACKING_SCRIPT)); $i = 1; ?>
<div class="tracking_scripts">
	<?php if($header_scripts) { ?>
		<ul class="tracking_script_list">
		<?php foreach($header_scripts as $script) { ?>
			<div class="tracking_script">
				<i class="fa fa-sort" title="Drag to Sort"></i>
				<p><?php echo $i; ?></p>
				<div class="script_info">
					<input type="text" name="header_script_<?php echo $i; ?>_name" value="<?php echo $script->script_name; ?>" readonly="readonly">
					<input type="text" name="header_script_<?php echo $i; ?>_code" value="<?php echo $script->script_code; ?>" readonly="readonly">
				</div>
				<i class="active_tracking fa <?php if($script->active === true) { echo 'fa-check-circle'; } else { echo 'fa-circle-o'; } ?>" title="<?php if($script->active === true) { echo 'Deactivate Script'; } else { echo 'Activate Script'; } ?>"></i>
				<i class="edit_tracking fa fa-edit" title="Edit Script"></i>
				<i class="delete_tracking fa fa-times" title="Delete Script"></i>
				<input type="hidden" class="script_order" name="header_script_<?php echo $i; ?>_order" value="<?php echo $i; ?>">
				<input type="hidden" class="script_active" name="header_script_<?php echo $i; ?>_active" value="<?php if($script->active === true) { echo 'true'; } else { echo 'false'; } ?>">
				<input type="hidden" class="script_exists" name="header_script_<?php echo $i; ?>_exists" value="true">
			</div>
		<?php $i++; } ?>
		</ul>
	<?php } ?>
</div>
</div>
<div class="script_section">
<h2><?php _e( 'Footer Scripts', TRACKING_SCRIPT_TEXTDOMAIN ); ?></h2>
<?php $footer_scripts = unserialize(get_option(WP_FOOTER_TRACKING_SCRIPT)); $i = 1; ?>
<div class="tracking_scripts">
	<?php if($footer_scripts) { ?>
		<ul class="tracking_script_list">
		<?php foreach($footer_scripts as $script) { ?>
			<div class="tracking_script">
				<i class="fa fa-sort" title="Drag to Sort"></i>
				<p><?php echo $i; ?></p>
				<div class="script_info">
					<input type="text" name="footer_script_<?php echo $i; ?>_name" value="<?php echo $script->script_name; ?>" readonly="readonly">
					<input type="text" name="footer_script_<?php echo $i; ?>_code" value="<?php echo $script->script_code; ?>" readonly="readonly">
				</div>
				<i class="active_tracking fa <?php if($script->active === true) { echo 'fa-check-circle'; } else { echo 'fa-circle-o'; } ?>" title="<?php if($script->active === true) { echo 'Deactivate Script'; } else { echo 'Activate Script'; } ?>"></i>
				<i class="edit_tracking fa fa-edit" title="Edit Script"></i>
				<i class="delete_tracking fa fa-times" title="Delete Script"></i>
				<input type="hidden" class="script_order" name="footer_script_<?php echo $i; ?>_order" value="<?php echo $i; ?>">
				<input type="hidden" class="script_active" name="footer_script_<?php echo $i; ?>_active" value="<?php if($script->active === true) { echo 'true'; } else { echo 'false'; } ?>">
				<input type="hidden" class="script_exists" name="footer_script_<?php echo $i; ?>_exists" value="true">
			</div>
		<?php $i++; } ?>
		</ul>
	<?php } ?>
</div>
</div>
<?php submit_button('Update Scripts', 'primary', 'update_tracking_codes'); ?>
<input type="hidden" name="action" value="update_tracking_codes">