<div id="wbgm_setting" class="wrap">
    <div class="header clearfix">
        <div class="left">
            <?php
            echo '<img src="' . plugins_url( 'img/wbgm-logo.png', dirname( __FILE__ ) ) . '" class="wbgm-logo" />';
            ?>
        </div>
        <div class="left">
            <p class="title"><?php echo WBGM_Common_Helper::translate( 'WooCommerce Bacchus Gold Loyalty Program Plugin' ) ?></p>
        </div>
    </div>
    <div class="options_group margin-top-20">
        <p class="switcher">
            <?php echo WBGM_Common_Helper::translate( 'Create Criteria' ) ?>
        </p>
    </div>
    <div id="wbgm_free_gift_global_settings">
        <form name="wbgm_main_menu_form" method="post" action="">
            <h2></h2>
            <?php wp_nonce_field( 'wbgm_criteria_settings', '_wbgm_criteria_nonce' ); ?>
            <div class="_wbgm-criteria-wrap">
                <div class="wbgm-criteria">
                    <?php
                    /*QWERTY*/
                    $all_criteria = WBGM_Settings_Helper::get( '', false, 'criteria', false );

                    $criteria = $condition = array();
                    if( $all_criteria ) {
                        $criteria = $all_criteria['criteria-1'];
                        $condition = $criteria['condition'];
                    }
                    ?>
                    <div class="wbgm-criteria-item shadow" data-id='1'>
                        <input type="text" name="_wbgm_criteria[criteria-1][name]" placeholder="<?php echo WBGM_Common_Helper::translate( 'Name this criteria' )  ?>"
                               required class="wbgm-criteria-name wbgm-input-full" value="<?php echo isset($criteria['name']) ? $criteria['name'] : '' ?>" />
                        <div class="wbgm-criteria-options-wrap" data-id='1'>
                            <select name="_wbgm_criteria[criteria-1][condition][]" class="wbgm-condition-selector">
                                <option value="num_products" <?php echo ( ! empty($condition) && $condition[0] == 'num_products' ) ? 'selected' : '' ?> >
                                    <?php echo WBGM_Common_Helper::translate( 'Total number of items in cart' ) ?>
                                </option>
                            </select>
                            <select name="_wbgm_criteria[criteria-1][condition][]" class="wbgm-comparison">
                                <option value="!=" <?php echo ( ! empty($condition) && $condition[1] == '!=' ) ? 'selected' : '' ?>>
                                    <?php echo WBGM_Common_Helper::translate( 'is not equal to' ) ?>
                                </option>
                            </select>
                            <input type="text" name="_wbgm_criteria[criteria-1][condition][]" value="<?php echo isset($condition[2]) ? $condition[2] : '' ?>"
                                   class="wbgm-input-small wbgm-adjust-position wbgm-condition-value" required />
                        </div>
                    </div>
                </div>
            </div>

            <div class="options_group">
                <p>
                    <input type="hidden" name="_wbgm_criteria_hidden" value="Y" />
                    <button class="button-primary" type="submit"><?php echo WBGM_Common_Helper::translate( 'Save' ) ?></button>
                </p>
            </div>
        </form>
    </div>
</div>