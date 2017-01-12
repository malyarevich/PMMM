<div id="wfgm_setting" class="wrap">
	<div class="header clearfix">
		<div class="left">
			<?php
				echo '<img src="' . plugins_url( 'img/wfgm-logo.png', dirname( __FILE__ ) ) . '" class="wfgm-logo" />';
			?>
		</div>
		<div class="left">
			<p class="title"><?php echo WFGM_Common_Helper::translate( 'WooCommerce Bacchus Gold Loyalty Program Plugin' ) ?> </p>
		</div>
	</div>
	<?php $products = WFGM_Product_Helper::get_products(); ?>
	<div id="wfgm_free_gift_global_settings">
		<form name="wfgm_main_menu_form" method="post" action="">
			<h2></h2>
			<?php wp_nonce_field( 'wfgm_global_settings','_wfgm_global_nonce' ); ?>
			<?php if( $products->have_posts() ): ?>
				<div class="options_group">
					<p class="form-field wfgm_form_field switcher ">
						<?php
							$checked = '';
							if( WFGM_Settings_Helper::get( 'global_enabled', true, 'global_options' ) ) {
								$checked = 'checked';
							}
						?>
						<span><?php echo WFGM_Common_Helper::translate( 'Enable/Disable free gift' ) ?></span>
						<label class="wfgm_globally_enabled switch switch-green">
							<input type="checkbox" class="checkbox switch-input"  name="wfgm_globally_enabled" id="wfgm_globally_enabled" <?php echo $checked ?>>
							<span class="switch-label" data-on="On" data-off="Off"></span>
							<span class="switch-handle"></span>
						</label>
					</p>
				</div>

				<div class="wfgm-main-settings-wrapper">
					<?php
						$wfgm_global_settings = WFGM_Settings_Helper::get( '', false, 'global_settings', false );
						$condition = isset($wfgm_global_settings['criteria-1']) ? $wfgm_global_settings['criteria-1'] : null;
					?>
					<div class="wfgm-settings-repeater shadow" data-id="1">
						<div class="wfgm-draggable">
							<p class="form-field wfgm_form_field">
								<label for="wfgm_gifts_allowed" class="description">
									<?php echo WFGM_Common_Helper::translate( 'Number of gifts allowed' ); ?>
								</label>
								<input type="text" class="input-text input-small" name="_wfgm_criteria[criteria-1][num_allowed]" value="<?php echo ! empty($condition['num_allowed']) ? $condition['num_allowed'] : -1 ?>" />
								<label for="wfgm_gifts_allowed" class="description">
									<?php echo WFGM_Common_Helper::translate( '-1 for non-limited' ); ?>
								</label>
							</p>
						</div>
						<!--<hr class="wfgm-hr">
						<p>
							<label><?php /*echo WFGM_Common_Helper::translate( 'Select Gift Products, press Space key and press and hold Enter key' ) */?></label>
						</p>-->
						<!--<div class="_wfgm-repeat">
							<select class='wfgm-ajax-select' data-placeholder='<?php /*echo WFGM_Common_Helper::translate( 'Add new products to list' ) */?>' name='_wfgm_criteria[criteria-1][items][]' multiple>
							<?php
/*								if( ! empty($condition['items']) ):
									$products = WFGM_Product_Helper::get_products( array( 'post__in' => $condition['items'] ), -1 );
							*/?>
								<p class="wfgm-inputs wfgm-criteria-options-wrap">
									<?php
/*										if( $products->have_posts() ) {
											while( $products->have_posts() ) {
												$products->the_post();
												$selected = '';
												if( in_array( get_the_ID(), $condition['items'] ) ) {
													$selected = 'selected';
												}

												echo "<option value='" . get_the_ID() . "' {$selected} >" . get_the_title() . '</option>';
											}
										}
									*/?>
								</p>
							<?php /*endif; */?>
							</select>
						</div>-->
					</div>
				</div>

				<input type="hidden" name="_wfgm_global_hidden" value="Y" />
				<button class="button-primary" type="submit"><?php echo WFGM_Common_Helper::translate( 'Save' ) ?></button>
			<?php else: ?>
				<div class="options_group">
					<p class="wfgm-info-wrapper form-field wfgm_form_field switcher">
						<?php echo get_permalink( woocommerce_get_page_id( 'product' ) ) ?>
						<?php
							$message = WFGM_Common_Helper::translate( 'Please add some' );
							$message .= ' ';
							$message .= '<a href="edit.php?post_type=product">' . WFGM_Common_Helper::translate( 'products' ) . '</a>';
							$message .= ' ';
							$message .= WFGM_Common_Helper::translate( 'first.' );
							echo $message;
						?>
					</p>
				</div>
			<?php endif; ?>
		</form>
	</div>
</div>
