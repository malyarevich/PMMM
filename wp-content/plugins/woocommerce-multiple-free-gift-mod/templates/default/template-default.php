<?php

    $cart_products = array();


    $products_in_cart = WC()->cart->cart_contents;
    foreach ($products_in_cart as $temp_var => $value) {

        $prod_data = $value['data'];
        $prod_id = $value['product_id'];
        $prod_quantity = $value['quantity'];
        $so_id = WFGM_Product_Helper::get_product_tags($prod_id);

        ?>
        <script>
            console.log(<?php echo json_encode()?>);
        </script>
        <?php

        if ($so_id !== 0 && $this->_wfgm_so_array[$so_id]['X'] <= $prod_quantity) {
            $count_of_N = floor(intval($prod_quantity) / intval($this->_wfgm_so_array[$so_id]['X']));
        } else {
            $count_of_N = 0;
        }

        $cart_products[] = [
            'id' => $prod_id,
            'data' => $prod_data,
            'qty' => $prod_quantity,
            'N' => $count_of_N,
            'so_id' => $so_id];
    }

    $modal = '';
    $overlay = WFGM_Settings_Helper::get('popup_overlay', true, 'global_options');
    if ($overlay):

        $modal .= '<div class="wfgm-overlay"  style="display:none"></div>';
    endif;
    $modal .= '<div class="wfgm-popup" style="display:none"> <h2 class="wfgm-title">';

    $heading = WFGM_Settings_Helper::get('popup_heading', false, 'global_options');
    if (false !== $heading) {
        $modal .= $heading;
    } else {
        $modal .= WFGM_Common_Helper::translate('Take your free gift');
    }
    $modal .= '</h2><div class="wfgm-gifts"><form onsubmit="JavaScript:this.subbut.disabled=true; this.subbut.value=\'Please wait...\';"
     action="' . admin_url('admin-ajax.php') . '" method="post"><input type="hidden" name="action" value="wfgm_add_gifts" />';
    $modal .= '<div class="wfgm-gifts-list">';

    wp_nonce_field('wfgm_add_free_gifts', '_wfgm_nonce');
    $is_gift = false;
        foreach ($cart_products as $cart_product) {
            $repl_data = $cart_product['data'];
            if ($cart_product['N'] < 1) {
                continue;
            }

            $is_gift = true;

            $modal .= '<div class="wfgm-item-box"><div class="wfgm-gift-item"><div class="wfgm-heading">';
            $modal .= '<input type="checkbox" class="wfgm-checkbox disabled" name="wfgm_free_items[]" id="wfgm-item-' .
                $repl_data->detail->ID . '" value="' . $repl_data->detail->ID . '" checked style="display:none"/>' .
                '<input type="hidden" name="wfgm_free_item_count[]"  id="wfgm-item-' .
                $repl_data->detail->ID . '" value="' . $cart_product['N'] * $this->_wfgm_so_array[$cart_product['so_id']]['Y'] .
                '"/><label for="wfgm-item-' .
                $repl_data->detail->ID . '" class="wfgm-title"><img src="' .
                $repl_data->image . '" style="width:150px; height:150px;" alt="" /></label><h3>' .
                $repl_data->detail->post_title . '</h3></div></div><div class="wfgm-gift-item-text"><p>';
            $so_congrat_save_money = WFGM_Settings_Helper::get('so_congrat_save_money', false, 'global_options');

            if (false === $so_congrat_save_money) {
                $so_congrat_save_money = WFGM_Common_Helper::translate('Congratulations! We added {N*Y} free gifts. You save {N*Y*price}{currency}.');
            }

            $repl_price = $repl_data->price;
            $so_congrat_save_money = str_replace(
                '{N*Y*price}',
                $cart_product['N'] * $this->_wfgm_so_array[$cart_product['so_id']]['Y'] * $repl_price,
                $so_congrat_save_money);
            $so_congrat_save_money = str_replace(
                '{currency}',
                get_woocommerce_currency(),
                $so_congrat_save_money);
            $so_congrat_save_money = str_replace(
                '{N*Y}',
                $cart_product['N'] * $this->_wfgm_so_array[$cart_product['so_id']]['Y'],
                $so_congrat_save_money);
            $modal .= $so_congrat_save_money . '</p></div></div>';
        }


    $modal .= '</div>';
    $modal .= '<div class="wfgm-actions"><button class="button wfgm-button wfgm-add-gifts" name="subbut">';
    $add_gift_text = WFGM_Settings_Helper::get('popup_add_gift_text', false, 'global_options');
    if (false !== $add_gift_text) {
        $modal .= $add_gift_text;
    } else {
        $modal .= WFGM_Common_Helper::translate('Add Gifts');
    }
    $modal .= '</button><div class="wfgm-save-div"></div></div>';
    $modal .= '</form></div></div>';
    $is_enable = WFGM_Settings_Helper::get('so_congrat_save_money_enabled', false, 'global_options');

    if( $is_gift ) {
        if( $is_enable ) {
            /*echo $modal;*/
        }
    }
?>