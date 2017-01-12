<?php
plugins_url( '/templates/images/Bacchus_Gold_Logo.png', dirname( __FILE__ ) );

add_action('woocommerce_after_add_to_cart_button', array( $this, 'wbgm_add_btn_add_to_cart_gold'), 10, 2);
