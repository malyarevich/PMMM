<div id="wbgm_setting" class="wrap">
    <div class="header clearfix">
        <div class="left">
            <?php
            echo '<img src="' . plugins_url( 'img/wbgm-logo.png', dirname( __FILE__ ) ) . '" class="wbgm-logo" />';
            ?>
        </div>
        <div class="left">
            <p class="title"><?php echo WBGM_Common_Helper::translate( 'WooCommerce Multiple Free Gift Mod' ) ?></p>
        </div>
    </div>
    <div class="options_group margin-top-20">
        <p class="switcher">
            <?php echo WBGM_Common_Helper::translate( 'General Settings' ) ?>
        </p>
    </div>
    <form class="wbgm-general-settings" method="post" action="">
        <?php wp_nonce_field( 'wbgm_general_settings', '_wbgm_general_nonce' ); ?>
        <table class="form-table">
            <tbody>
            <tr class="wbgm-border-bottom">
                <th scope="row">
                    <label for="popup_overlay"><?php echo WBGM_Common_Helper::translate( 'Popup Overlay' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Black half-transparent background]' ) ?></i></a>
                </th>
                <td></td>
                <td>
                    <?php
                    $checked = '';
                    $overlay = WBGM_Settings_Helper::get( 'popup_overlay', true, 'global_options' );
                    if( $overlay ) {
                        $checked = 'checked';
                    }
                    ?>
                    <label class="switch switch-green">
                        <input type="checkbox" class="checkbox switch-input"  name="_wbgm_popup_overlay" id="popup_overlay" <?php echo $checked ?>>
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </td>
            </tr>
            <tr class="wbgm-border-bottom">
                <th scope="row">
                    <label for="so_product_page"><?php echo WBGM_Common_Helper::translate( 'SO on this product' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Modal window as Popup on product page and it depends on - Popup Overlay, Alert Popup Heading Text, Okay Text]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $so_product_page = WBGM_Settings_Helper::get( 'so_product_page', false, 'global_options' );
                    if( false === $so_product_page ) {
                        $so_product_page = WBGM_Common_Helper::translate( 'On this product active special offer. For every {X} item we give {Y} free gift.' );
                    }
                    ?>

                    <input type="text" name="_wbgm_so_product_page" id="so_product_page" class="regular-text" value="<?php echo $so_product_page ?>" />
                    <i>
                        <p>{X} - Product for gift,</p>
                        <p>{Y} - Gift's product,</p>
                    </i>
                </td>
                <td>
                    <?php
                    $checked_so_product_page_enabled = '';
                    $so_product_page_enabled = WBGM_Settings_Helper::get( 'so_product_page_enabled', true, 'global_options' );
                    if( $so_product_page_enabled ) {
                        $checked_so_product_page_enabled = 'checked';
                    }
                    ?>
                    <label class="switch switch-green">
                        <input type="checkbox" class="checkbox switch-input"  name="_wbgm_so_product_page_enabled" id="so_product_page_enabled" <?php echo $checked_so_product_page_enabled ?>>
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </td>
            </tr>
            <tr class="wbgm-border-bottom">
                <th scope="row">
                    <label for="so_add_more"><?php echo WBGM_Common_Helper::translate( 'SO add more' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $so_add_more = WBGM_Settings_Helper::get( 'so_add_more', false, 'global_options' );
                    if( false === $so_add_more ) {
                        $so_add_more = WBGM_Common_Helper::translate( 'Special offer. add more for gift.' );
                    }
                    ?>
                    <input type="text" name="_wbgm_so_add_more" id="so_add_more" class="regular-text" value="<?php echo $so_add_more ?>" />
                    <p></p>
                </td>
                <td>
                    <?php
                    $checked_so_add_more_enabled = '';
                    $so_add_more_enabled = WBGM_Settings_Helper::get( 'so_add_more_enabled', true, 'global_options' );
                    if( $so_add_more_enabled ) {
                        $checked_so_add_more_enabled = 'checked';
                    }
                    ?>
                    <label class="switch switch-green">
                        <input type="checkbox" class="checkbox switch-input"  name="_wbgm_so_add_more_enabled" id="so_add_more_enabled" <?php echo $checked_so_add_more_enabled ?>>
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </td>
            </tr>
            <tr class="wbgm-border-bottom">
                <th scope="row">
                    <label for="so_congrat"><?php echo WBGM_Common_Helper::translate( 'SO congratulation' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Notice of added product in the cart. Show in field of notices]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $so_congrat = WBGM_Settings_Helper::get( 'so_congrat', false, 'global_options' );
                    if( false === $so_congrat ) {
                        $so_congrat = WBGM_Common_Helper::translate( '{Y} x {title} were added to your cart.' );
                    }
                    ?>
                    <input type="text" name="_wbgm_so_congrat" id="so_congrat" class="regular-text" value="<?php echo $so_congrat ?>" />
                    <i>
                        <p>{Y} - Gift's product,</p>
                        <p>{title} - Title of product,</p>
                    </i>
                </td>
                <td>
                    <?php
                    $checked_so_congrat_enabled = '';
                    $so_congrat_enabled = WBGM_Settings_Helper::get( 'so_congrat_enabled', true, 'global_options' );
                    if( $so_congrat_enabled ) {
                        $checked_so_congrat_enabled = 'checked';
                    }
                    ?>
                    <label class="switch switch-green">
                        <input type="checkbox" class="checkbox switch-input"  name="_wbgm_so_congrat_enabled" id="so_congrat_enabled" <?php echo $checked_so_congrat_enabled ?>>
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </td>
            </tr>

            <tr class="wbgm-border-bottom">
                <th scope="row">
                    <label for="so_congrat_save_money"><?php echo WBGM_Common_Helper::translate( 'SO congratulations and you save' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Notice of all saved money by gifted products in the cart. Show in field of notices]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $so_congrat_save_money = WBGM_Settings_Helper::get( 'so_congrat_save_money', false, 'global_options' );
                    if( false === $so_congrat_save_money ) {
                        $so_congrat_save_money = WBGM_Common_Helper::translate( 'Congratulations! You save {sum(N*Y*price)} {currency}.' );
                    }
                    ?>
                    <input type="text" name="_wbgm_so_congrat_save_money" id="so_congrat_save_money" class="regular-text"
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
                    $so_congrat_save_money_enabled = WBGM_Settings_Helper::get( 'so_congrat_save_money_enabled', true, 'global_options' );
                    if( $so_congrat_save_money_enabled ) {
                        $checked_so_congrat_save_money_enabled = 'checked';
                    }
                    ?>
                    <label class="switch switch-green">
                        <input type="checkbox" class="checkbox switch-input"  name="_wbgm_so_congrat_save_money_enabled" id="so_congrat_save_money_enabled" <?php echo $checked_so_congrat_save_money_enabled ?>>
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </td>
            </tr>
            <tr class="wbgm-border-bottom">
                <th scope="row">
                    <label for="so_deleted_gift"><?php echo WBGM_Common_Helper::translate( 'SO deleted gift' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Notice of deleted product from the cart. Show in field of notices]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $so_deleted_gift = WBGM_Settings_Helper::get( 'so_deleted_gift', false, 'global_options' );
                    if( false === $so_deleted_gift ) {
                        $so_deleted_gift = WBGM_Common_Helper::translate( '{Y} x {title} were deleted from your cart.' );
                    }
                    ?>
                    <input type="text" name="_wbgm_so_deleted_gift" id="so_deleted_gift" class="regular-text"
                           value="<?php echo $so_deleted_gift ?>" />
                    <i>
                        <p>{Y} - Gift's product,</p>
                        <p>{title} - Title of product,</p>
                    </i>
                </td>
                <td>
                    <?php
                    $checked_so_deleted_gift_enabled = '';
                    $so_deleted_gift_enabled = WBGM_Settings_Helper::get( 'so_deleted_gift_enabled', true, 'global_options' );
                    if( $so_deleted_gift_enabled ) {
                        $checked_so_deleted_gift_enabled = 'checked';
                    }
                    ?>
                    <label class="switch switch-green">
                        <input type="checkbox" class="checkbox switch-input"  name="_wbgm_so_deleted_gift_enabled" id="deleted_gift_enabled" <?php echo $checked_so_deleted_gift_enabled ?>>
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </td>
            </tr>


            <!--/*       */-->

            <tr>
                <th scope="row">
                    <label for="popup_heading"><?php echo WBGM_Common_Helper::translate( 'Popup Heading Text' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $heading = WBGM_Settings_Helper::get( 'popup_heading', false, 'global_options' );
                    if( false === $heading ) {
                        $heading = WBGM_Common_Helper::translate( 'Take your free gift' );
                    }
                    ?>
                    <input type="text" name="_wbgm_popup_heading" id="popup_heading" class="regular-text" value="<?php echo $heading ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="popup_heading_msg"><?php echo WBGM_Common_Helper::translate( 'Alert Popup Heading Text' ) ?></label>
                </th>
                <td>
                    <?php
                    $heading_msg = WBGM_Settings_Helper::get( 'popup_heading_msg', false, 'global_options' );
                    if( false === $heading_msg ) {
                        $heading_msg = WBGM_Common_Helper::translate( 'Message for you!' );
                    }
                    ?>
                    <input type="text" name="_wbgm_popup_heading_msg" id="popup_heading_msg" class="regular-text" value="<?php echo $heading_msg ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="btn_adding_to_cart_text"><?php echo WBGM_Common_Helper::translate( 'Adding to cart text' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[for Gratis Bacchus Gold Artikel btn prop data-loading-text]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $add_gift_text = WBGM_Settings_Helper::get( 'btn_adding_to_cart_text', false, 'global_options' );
                    if( false === $add_gift_text ) {
                        $add_gift_text = WBGM_Common_Helper::translate( 'Adding to cart...' );
                    }
                    ?>
                    <input type="text" name="_wbgm_btn_adding_to_cart_text" id="btn_adding_to_cart_text" class="regular-text" value="<?php echo $add_gift_text ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="popup_cancel_text"><?php echo WBGM_Common_Helper::translate( 'Cancel Text' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $cancel_text = WBGM_Settings_Helper::get( 'popup_cancel_text', false, 'global_options' );
                    if( false === $cancel_text ) {
                        $cancel_text = WBGM_Common_Helper::translate( 'No Thanks' );
                    }
                    ?>
                    <input type="text" name="_wbgm_popup_cancel_text" id="popup_cancel_text" class="regular-text" value="<?php echo $cancel_text ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ok_text"><?php echo WBGM_Common_Helper::translate( 'Okay Text' ) ?></label>
                </th>
                <td>
                    <?php
                    $ok_text = WBGM_Settings_Helper::get( 'ok_text', false, 'global_options' );
                    if( false === $ok_text ) {
                        $ok_text = WBGM_Common_Helper::translate( 'Okay' );
                    }
                    ?>
                    <input type="text" name="_wbgm_ok_text" id="ok_text" class="regular-text" value="<?php echo $ok_text ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="invalid_condition"><?php echo WBGM_Common_Helper::translate( 'Invalid Gift Condition Text' ) ?></label>
                    <br/>
                    <a for="popup_overlay"><i><?php echo WBGM_Common_Helper::translate( '[Non-realized in this version]' ) ?></i></a>
                </th>
                <td>
                    <?php
                    $invalidText = WBGM_Settings_Helper::get( 'invalid_condition_text', false, 'global_options' );
                    if( false === $invalidText ) {
                        $invalidText = WBGM_Common_Helper::translate( 'Gift items removed as gift criteria isnt fulfilled' );
                    }
                    ?>
                    <input type="text" name="_wbgm_invalid_condition_text" id="invalid_condition" class="regular-text"
                           value="<?php echo $invalidText ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="type_text"><?php echo WBGM_Common_Helper::translate( 'Type Text' ) ?></label>
                </th>
                <td>
                    <?php
                    $type_text = WBGM_Settings_Helper::get( 'type_text', false, 'global_options' );
                    if( false === $type_text ) {
                        $type_text = WBGM_Common_Helper::translate( 'Typ' );
                    }
                    ?>
                    <input type="text" name="_wbgm_type_text" id="type_text" class="regular-text"
                           value="<?php echo $type_text ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="free_item_text"><?php echo WBGM_Common_Helper::translate( 'Gratis Bacchus Gold Artikel Text' ) ?></label>
                </th>
                <td>
                    <?php
                    $free_item_text = WBGM_Settings_Helper::get( 'free_item_text', false, 'global_options' );
                    if( false === $free_item_text ) {
                        $free_item_text = WBGM_Common_Helper::translate( 'Gratis Bacchus Gold Artikel' );
                    }
                    ?>
                    <input type="text" name="_wbgm_free_item_text" id="free_item_text" class="regular-text"
                           value="<?php echo $free_item_text ?>" />
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="hidden" name="_wbgm_general_settings_submitted" value="Y" />
            <input type="submit" value="<?php echo WBGM_Common_Helper::translate( 'Save Changes' ) ?>" class="button-primary" />
        </p>
    </form>
</div>