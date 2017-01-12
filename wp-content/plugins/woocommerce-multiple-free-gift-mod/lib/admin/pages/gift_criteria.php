<div id="wfgm_setting" class="wrap">
	<div class="header clearfix">
		<div class="left">
			<?php
				echo '<img src="' . plugins_url( 'img/wfgm-logo.png', dirname( __FILE__ ) ) . '" class="wfgm-logo" />';
			?>
		</div>
		<div class="left">
			<p class="title"><?php echo WFGM_Common_Helper::translate( 'WooCommerce Multiple Free Gift Mod' ) ?></p>
		</div>
	</div>
	<div class="options_group margin-top-20">
		<p class="switcher">
			<?php echo WFGM_Common_Helper::translate( 'Create Criteria' ) ?>
		</p>
	</div>
	<div id="wfgm_free_gift_global_settings">
		<form name="wfgm_main_menu_form" method="post" action="">
			<h2></h2>
			<?php wp_nonce_field( 'wfgm_criteria_settings', '_wfgm_criteria_nonce' ); ?>
			<div class="_wfgm-criteria-wrap">
				<div class="wfgm-criteria">
					<?php
					/*QWERTY*/
					$all_criteria = WFGM_Settings_Helper::get( '', false, 'criteria', false );

						$criteria = $condition = array();
						if( $all_criteria ) {
							$criteria = $all_criteria['criteria-1'];
							$condition = $criteria['condition'];
						}
					?>
						<div class="wfgm-criteria-item shadow" data-id='1'>
							<input type="text" name="_wfgm_criteria[criteria-1][name]" placeholder="<?php echo WFGM_Common_Helper::translate( 'Name this criteria' )  ?>"
									required class="wfgm-criteria-name wfgm-input-full" value="<?php echo isset($criteria['name']) ? $criteria['name'] : '' ?>" />
								<div class="wfgm-criteria-options-wrap" data-id='1'>
									<select name="_wfgm_criteria[criteria-1][condition][]" class="wfgm-condition-selector">
										<option value="num_products" <?php echo ( ! empty($condition) && $condition[0] == 'num_products' ) ? 'selected' : '' ?> >
											<?php echo WFGM_Common_Helper::translate( 'Total number of items in cart' ) ?>
										</option>
									</select>
									<select name="_wfgm_criteria[criteria-1][condition][]" class="wfgm-comparison">
										<option value="!=" <?php echo ( ! empty($condition) && $condition[1] == '!=' ) ? 'selected' : '' ?>>
											<?php echo WFGM_Common_Helper::translate( 'is not equal to' ) ?>
										</option>
									</select>
									<input type="text" name="_wfgm_criteria[criteria-1][condition][]" value="<?php echo isset($condition[2]) ? $condition[2] : '' ?>"
										class="wfgm-input-small wfgm-adjust-position wfgm-condition-value" required />
								</div>
						</div>
				</div>
			</div>

			<div class="options_group">
				<p>
					<input type="hidden" name="_wfgm_criteria_hidden" value="Y" />
					<button class="button-primary" type="submit"><?php echo WFGM_Common_Helper::translate( 'Save' ) ?></button>
				</p>
			</div>
		</form>
	</div>
</div>