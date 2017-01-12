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
			<?php echo WFGM_Common_Helper::translate( 'General Settings' ) ?>
		</p>
	</div>
	<form class="wfgm-general-settings" method="post" action="">
		<?php wp_nonce_field( 'wfgm_general_settings', '_wfgm_general_nonce' ); ?>
		<table class="form-table">
			<tbody>
				<tr class="wfgm-border-bottom">
					<th scope="row">
						<label for="popup_overlay"><?php echo WFGM_Common_Helper::translate( 'Popup Overlay' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Black half-transparent background]' ) ?></i></a>
					</th>
					<td></td>
					<td>
						<?php
							$checked = '';
							$overlay = WFGM_Settings_Helper::get( 'popup_overlay', true, 'global_options' );
							if( $overlay ) {
								$checked = 'checked';
							}
						?>
					  <label class="switch switch-green">
					    <input type="checkbox" class="checkbox switch-input"  name="_wfgm_popup_overlay" id="popup_overlay" <?php echo $checked ?>>
					    <span class="switch-label" data-on="On" data-off="Off"></span>
					    <span class="switch-handle"></span>
					  </label>
					</td>
				</tr>
				<tr class="wfgm-border-bottom">
					<th scope="row">
						<label for="so_product_page"><?php echo WFGM_Common_Helper::translate( 'SO on this product' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Modal window as Popup on product page and it depends on - Popup Overlay, Alert Popup Heading Text, Okay Text]' ) ?></i></a>
					</th>
					<td>
						<?php
						$so_product_page = WFGM_Settings_Helper::get( 'so_product_page', false, 'global_options' );
						if( false === $so_product_page ) {
                            $so_product_page = WFGM_Common_Helper::translate( 'On this product active special offer. For every {X} item we give {Y} free gift.' );
						}
						?>

						<input type="text" name="_wfgm_so_product_page" id="so_product_page" class="regular-text" value="<?php echo $so_product_page ?>" />
						<i>
							<p>{X} - Product for gift,</p>
							<p>{Y} - Gift's product,</p>
						</i>
					</td>
					<td>
						<?php
						$checked_so_product_page_enabled = '';
						$so_product_page_enabled = WFGM_Settings_Helper::get( 'so_product_page_enabled', true, 'global_options' );
						if( $so_product_page_enabled ) {
							$checked_so_product_page_enabled = 'checked';
						}
						?>
						<label class="switch switch-green">
							<input type="checkbox" class="checkbox switch-input"  name="_wfgm_so_product_page_enabled" id="so_product_page_enabled" <?php echo $checked_so_product_page_enabled ?>>
							<span class="switch-label" data-on="On" data-off="Off"></span>
							<span class="switch-handle"></span>
						</label>
					</td>
				</tr>
				<tr class="wfgm-border-bottom">
					<th scope="row">
						<label for="so_add_more"><?php echo WFGM_Common_Helper::translate( 'SO add more' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
					</th>
					<td>
						<?php
						$so_add_more = WFGM_Settings_Helper::get( 'so_add_more', false, 'global_options' );
						if( false === $so_add_more ) {
							$so_add_more = WFGM_Common_Helper::translate( 'Special offer. add more for gift.' );
						}
						?>
						<input type="text" name="_wfgm_so_add_more" id="so_add_more" class="regular-text" value="<?php echo $so_add_more ?>" />
						<p></p>
					</td>
					<td>
						<?php
						$checked_so_add_more_enabled = '';
						$so_add_more_enabled = WFGM_Settings_Helper::get( 'so_add_more_enabled', true, 'global_options' );
						if( $so_add_more_enabled ) {
							$checked_so_add_more_enabled = 'checked';
						}
						?>
						<label class="switch switch-green">
							<input type="checkbox" class="checkbox switch-input"  name="_wfgm_so_add_more_enabled" id="so_add_more_enabled" <?php echo $checked_so_add_more_enabled ?>>
							<span class="switch-label" data-on="On" data-off="Off"></span>
							<span class="switch-handle"></span>
						</label>
					</td>
				</tr>
				<tr class="wfgm-border-bottom">
					<th scope="row">
						<label for="so_congrat"><?php echo WFGM_Common_Helper::translate( 'SO congratulation' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Notice of added product in the cart. Show in field of notices]' ) ?></i></a>
					</th>
					<td>
						<?php
						$so_congrat = WFGM_Settings_Helper::get( 'so_congrat', false, 'global_options' );
						if( false === $so_congrat ) {
							$so_congrat = WFGM_Common_Helper::translate( '{Y} x {title} were added to your cart.' );
						}
						?>
						<input type="text" name="_wfgm_so_congrat" id="so_congrat" class="regular-text" value="<?php echo $so_congrat ?>" />
						<i>
							<p>{Y} - Gift's product,</p>
							<p>{title} - Title of product,</p>
						</i>
					</td>
					<td>
						<?php
						$checked_so_congrat_enabled = '';
						$so_congrat_enabled = WFGM_Settings_Helper::get( 'so_congrat_enabled', true, 'global_options' );
						if( $so_congrat_enabled ) {
							$checked_so_congrat_enabled = 'checked';
						}
						?>
						<label class="switch switch-green">
							<input type="checkbox" class="checkbox switch-input"  name="_wfgm_so_congrat_enabled" id="so_congrat_enabled" <?php echo $checked_so_congrat_enabled ?>>
							<span class="switch-label" data-on="On" data-off="Off"></span>
							<span class="switch-handle"></span>
						</label>
					</td>
				</tr>

				<tr class="wfgm-border-bottom">
					<th scope="row">
						<label for="so_congrat_save_money"><?php echo WFGM_Common_Helper::translate( 'SO congratulations and you save' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Notice of all saved money by gifted products in the cart. Show in field of notices]' ) ?></i></a>
					</th>
					<td>
						<?php
						$so_congrat_save_money = WFGM_Settings_Helper::get( 'so_congrat_save_money', false, 'global_options' );
						if( false === $so_congrat_save_money ) {
							$so_congrat_save_money = WFGM_Common_Helper::translate( 'Congratulations! You save {sum(N*Y*price)} {currency}.' );
						}
						?>
						<input type="text" name="_wfgm_so_congrat_save_money" id="so_congrat_save_money" class="regular-text"
							   value="<?php echo $so_congrat_save_money ?>" />
						<i>
							<p>{sum(N*Y*price)} - Sum price of all gifts product in the cart,</p>
							<p>{currency} - Currency of price,</p>
							<b>{currency} was added to {sum(N*Y*price)} in current version. And now should be:</b>
							<p>
								'{sum(N*Y*price)} {currency}'
						</i>
						<b>(with space!)</b>
						<i>
							</p>
						</i>
					</td>
					<td>
						<?php
						$checked_so_congrat_save_money_enabled = '';
						$so_congrat_save_money_enabled = WFGM_Settings_Helper::get( 'so_congrat_save_money_enabled', true, 'global_options' );
						if( $so_congrat_save_money_enabled ) {
							$checked_so_congrat_save_money_enabled = 'checked';
						}
						?>
						<label class="switch switch-green">
							<input type="checkbox" class="checkbox switch-input"  name="_wfgm_so_congrat_save_money_enabled" id="so_congrat_save_money_enabled" <?php echo $checked_so_congrat_save_money_enabled ?>>
							<span class="switch-label" data-on="On" data-off="Off"></span>
							<span class="switch-handle"></span>
						</label>
					</td>
				</tr>
				<tr class="wfgm-border-bottom">
					<th scope="row">
						<label for="so_deleted_gift"><?php echo WFGM_Common_Helper::translate( 'SO deleted gift' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Notice of deleted product from the cart. Show in field of notices]' ) ?></i></a>
					</th>
					<td>
						<?php
						$so_deleted_gift = WFGM_Settings_Helper::get( 'so_deleted_gift', false, 'global_options' );
						if( false === $so_deleted_gift ) {
							$so_deleted_gift = WFGM_Common_Helper::translate( '{Y} x {title} were deleted from your cart.' );
						}
						?>
						<input type="text" name="_wfgm_so_deleted_gift" id="so_deleted_gift" class="regular-text"
							   value="<?php echo $so_deleted_gift ?>" />
						<i>
							<p>{Y} - Gift's product,</p>
							<p>{title} - Title of product,</p>
						</i>
					</td>
					<td>
						<?php
						$checked_so_deleted_gift_enabled = '';
						$so_deleted_gift_enabled = WFGM_Settings_Helper::get( 'so_deleted_gift_enabled', true, 'global_options' );
						if( $so_deleted_gift_enabled ) {
							$checked_so_deleted_gift_enabled = 'checked';
						}
						?>
						<label class="switch switch-green">
							<input type="checkbox" class="checkbox switch-input"  name="_wfgm_so_deleted_gift_enabled" id="deleted_gift_enabled" <?php echo $checked_so_deleted_gift_enabled ?>>
							<span class="switch-label" data-on="On" data-off="Off"></span>
							<span class="switch-handle"></span>
						</label>
					</td>
				</tr>


				<!--/*       */-->

				<tr>
					<th scope="row">
						<label for="popup_heading"><?php echo WFGM_Common_Helper::translate( 'Popup Heading Text' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
					</th>
					<td>
						<?php
						$heading = WFGM_Settings_Helper::get( 'popup_heading', false, 'global_options' );
						if( false === $heading ) {
							$heading = WFGM_Common_Helper::translate( 'Take your free gift' );
						}
						?>
						<input type="text" name="_wfgm_popup_heading" id="popup_heading" class="regular-text" value="<?php echo $heading ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="popup_heading_msg"><?php echo WFGM_Common_Helper::translate( 'Alert Popup Heading Text' ) ?></label>
					</th>
					<td>
						<?php
						$heading_msg = WFGM_Settings_Helper::get( 'popup_heading_msg', false, 'global_options' );
						if( false === $heading_msg ) {
							$heading_msg = WFGM_Common_Helper::translate( 'Message for you!' );
						}
						?>
						<input type="text" name="_wfgm_popup_heading_msg" id="popup_heading_msg" class="regular-text" value="<?php echo $heading_msg ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="popup_add_gift_text"><?php echo WFGM_Common_Helper::translate( 'Add Gift Text' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
					</th>
					<td>
						<?php
						$add_gift_text = WFGM_Settings_Helper::get( 'popup_add_gift_text', false, 'global_options' );
						if( false === $add_gift_text ) {
							$add_gift_text = WFGM_Common_Helper::translate( 'Add Gifts' );
						}
						?>
						<input type="text" name="_wfgm_popup_add_gift_text" id="popup_add_gift_text" class="regular-text" value="<?php echo $add_gift_text ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="popup_cancel_text"><?php echo WFGM_Common_Helper::translate( 'Cancel Text' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
					</th>
					<td>
						<?php
						$cancel_text = WFGM_Settings_Helper::get( 'popup_cancel_text', false, 'global_options' );
						if( false === $cancel_text ) {
							$cancel_text = WFGM_Common_Helper::translate( 'No Thanks' );
						}
						?>
						<input type="text" name="_wfgm_popup_cancel_text" id="popup_cancel_text" class="regular-text" value="<?php echo $cancel_text ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="ok_text"><?php echo WFGM_Common_Helper::translate( 'Okay Text' ) ?></label>
					</th>
					<td>
						<?php
						$ok_text = WFGM_Settings_Helper::get( 'ok_text', false, 'global_options' );
						if( false === $ok_text ) {
							$ok_text = WFGM_Common_Helper::translate( 'Okay' );
						}
						?>
						<input type="text" name="_wfgm_ok_text" id="ok_text" class="regular-text" value="<?php echo $ok_text ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="invalid_condition"><?php echo WFGM_Common_Helper::translate( 'Invalid Gift Condition Text' ) ?></label>
						<br/>
						<a for="popup_overlay"><i><?php echo WFGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
					</th>
					<td>
						<?php
						$invalidText = WFGM_Settings_Helper::get( 'invalid_condition_text', false, 'global_options' );
						if( false === $invalidText ) {
							$invalidText = WFGM_Common_Helper::translate( 'Gift items removed as gift criteria isnt fulfilled' );
						}
						?>
						<input type="text" name="_wfgm_invalid_condition_text" id="invalid_condition" class="regular-text"
							   value="<?php echo $invalidText ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="type_text"><?php echo WFGM_Common_Helper::translate( 'Type Text' ) ?></label>
					</th>
					<td>
						<?php
						$type_text = WFGM_Settings_Helper::get( 'type_text', false, 'global_options' );
						if( false === $type_text ) {
							$type_text = WFGM_Common_Helper::translate( 'Type' );
						}
						?>
						<input type="text" name="_wfgm_type_text" id="type_text" class="regular-text"
							   value="<?php echo $type_text ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="free_item_text"><?php echo WFGM_Common_Helper::translate( 'Free Item Text' ) ?></label>
					</th>
					<td>
						<?php
						$free_item_text = WFGM_Settings_Helper::get( 'free_item_text', false, 'global_options' );
						if( false === $free_item_text ) {
							$free_item_text = WFGM_Common_Helper::translate( 'Free Item' );
						}
						?>
						<input type="text" name="_wfgm_free_item_text" id="free_item_text" class="regular-text"
							   value="<?php echo $free_item_text ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="hidden" name="_wfgm_general_settings_submitted" value="Y" />
			<input type="submit" value="<?php echo WFGM_Common_Helper::translate( 'Save Changes' ) ?>" class="button-primary" />
		</p>
	</form>
</div>