<div id="wbgm_setting" class="wrap">
    <div class="header clearfix">
        <div class="left">
            <?php
            echo '<img src="' . plugins_url( 'img/wbgm-logo.png', dirname( __FILE__ ) ) . '" class="wbgm-logo" />';
            ?>
        </div>
        <div class="left">
            <p class="title"><?php echo WBGM_Common_Helper::translate( 'WooCommerce Bacchus Gold Loyalty Program Plugin' ) ?> </p>
        </div>
    </div>
    <?php $products = WBGM_Product_Helper::get_products(); ?>
    <div id="wbgm_free_gift_global_settings">
        <form name="wbgm_main_menu_form" method="post" action="">
            <h2></h2>
            <?php wp_nonce_field( 'wbgm_global_settings','_wbgm_global_nonce' ); ?>
            <?php if( $products->have_posts() ): ?>
                <div class="options_group">
                    <p class="form-field wbgm_form_field switcher ">
                        <?php
                        $checked = '';
                        if( WBGM_Settings_Helper::get( 'global_enabled', true, 'global_options' ) ) {
                            $checked = 'checked';
                        }
                        ?>
                        <span><?php echo WBGM_Common_Helper::translate( 'Enable/Disable free gift' ) ?></span>
                        <label class="wbgm_globally_enabled switch switch-green">
                            <input type="checkbox" class="checkbox switch-input"  name="wbgm_globally_enabled" id="wbgm_globally_enabled" <?php echo $checked ?>>
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </p>
                </div>

                <div class="wbgm-main-settings-wrapper">
                    <?php
                    $wbgm_global_settings = WBGM_Settings_Helper::get( '', false, 'global_settings', false );
                    $condition = isset($wbgm_global_settings['criteria-1']) ? $wbgm_global_settings['criteria-1'] : null;
                    ?>
                    <div class="wbgm-settings-repeater shadow" data-id="1">
                        <div class="wbgm-draggable">
                            <p class="form-field wbgm_form_field">
                                <label for="wbgm_gifts_allowed" class="description">
                                    <?php echo WBGM_Common_Helper::translate( 'Number of gifts allowed' ); ?>
                                </label>
                                <input type="text" class="input-text input-small" name="_wbgm_criteria[criteria-1][num_allowed]" value="<?php echo ! empty($condition['num_allowed']) ? $condition['num_allowed'] : -1 ?>" />
                                <label for="wbgm_gifts_allowed" class="description">
                                    <?php echo WBGM_Common_Helper::translate( '-1 for non-limited' ); ?>
                                </label>
                            </p>
                        </div>
                        <!--<hr class="wbgm-hr">
						<p>
							<label><?php /*echo WBGM_Common_Helper::translate( 'Select Gift Products, press Space key and press and hold Enter key' ) */?></label>
						</p>-->
                        <!--<div class="_wbgm-repeat">
							<select class='wbgm-ajax-select' data-placeholder='<?php /*echo WBGM_Common_Helper::translate( 'Add new products to list' ) */?>' name='_wbgm_criteria[criteria-1][items][]' multiple>
							<?php
                        /*								if( ! empty($condition['items']) ):
                                                            $products = WBGM_Product_Helper::get_products( array( 'post__in' => $condition['items'] ), -1 );
                                                    */?>
								<p class="wbgm-inputs wbgm-criteria-options-wrap">
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

                <input type="hidden" name="_wbgm_global_hidden" value="Y" />
                <button class="button-primary" type="submit"><?php echo WBGM_Common_Helper::translate( 'Save' ) ?></button>
            <?php else: ?>
                <div class="options_group">
                    <p class="wbgm-info-wrapper form-field wbgm_form_field switcher">
                        <?php echo get_permalink( woocommerce_get_page_id( 'product' ) ) ?>
                        <?php
                        $message = WBGM_Common_Helper::translate( 'Please add some' );
                        $message .= ' ';
                        $message .= '<a href="edit.php?post_type=product">' . WBGM_Common_Helper::translate( 'products' ) . '</a>';
                        $message .= ' ';
                        $message .= WBGM_Common_Helper::translate( 'first.' );
                        echo $message;
                        ?>
                    </p>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
