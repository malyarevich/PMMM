<?php
    global $post, $product;

if( $post->ID !== 50826 ) :
    /*echo '<script>console.log(' . json_encode($product) . ')</script>';*/
    ?>
</form>

<form id="wbgm-add-to-cart-bonus-form" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post"
    <?php
        if (!$this->wbgm_is_show_bonus_add_btn()){
            echo 'style="display:none"';
        }?>
>
    <input type="hidden" name="action" value="wbgm_add_bonus_gifts" />
    <input type="hidden" name="cur_price" value="<?php echo get_post_meta( $post->ID, '_price', true); ?>" />
            <div class="wbgm-gift-item" style="display: none">
                <div class="wbgm-heading">
                    <input type="checkbox" class="wbgm-checkbox" name="wbgm_free_items" id="wbgm-item-<?php echo $post->ID ?>" value="<?php echo $post->ID ?>" checked/>
                    <label for="wbgm-item-<?php echo $post->ID ?>" class="wbgm-title"></label>

                    <h3><?php echo $post->post_title ?></h3>
                </div>
            </div>
        <div class="wbgm-actions">
            <?php
            $btn_adding_item_text = WBGM_Settings_Helper::get( 'btn_adding_to_cart_text', false, 'global_options' );
            if( false == $btn_adding_item_text ) {
                $btn_adding_item_text = WBGM_Common_Helper::translate( 'Adding to cart...' );
            }
            ?>
            <button type="button" data-loading-text="<?php echo $btn_adding_item_text;?>" class="wbgm-gold-member-add-to-cart btn btn-warning" style="padding: 20px; font-weight: 600;">
                <?php
                    $btn_add_bonus_item_text = WBGM_Settings_Helper::get( 'btn_add_bonus_item_text', false, 'global_options' );
                    if( false !== $btn_add_bonus_item_text ) {
                        echo $btn_add_bonus_item_text;
                    } else {
                        echo WBGM_Common_Helper::translate( 'ALS BONUS-ARTIKEL FESTLEGEN' );
                    }
                ?>
            </button>
        </div>
    <div id="wbgm-scripts"></div>
    <?php endif;