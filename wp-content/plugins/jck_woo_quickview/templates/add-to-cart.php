<?php global $post, $product, $woocommerce; ?>

<?php if($product->product_type == "grouped") { ?>

    <a href="<?php echo get_the_permalink($product->ID); ?>" rel="nofollow" class="button  product_type_grouped"><?php _e('View products','woocommerce'); ?></a>

<?php } else { ?>

    <?php do_action($this->slug.'-before-addtocart'); ?>

    <?php do_action( 'woocommerce_' . $product->product_type . '_add_to_cart'  ); ?>

    <?php do_action($this->slug.'-after-addtocart'); ?>

    <?php

    wc_get_template( 'single-product/add-to-cart/variation.php' );

    $wc_add_to_cart = array(
        'ajax_url'                => WC()->ajax_url(),
        'wc_ajax_url'             => WC_AJAX::get_endpoint( "%%endpoint%%" ),
        'i18n_view_cart'          => esc_attr__( 'View Cart', 'woocommerce' ),
        'cart_url'                => apply_filters( 'woocommerce_add_to_cart_redirect', apply_filters( 'woocommerce_get_cart_url', wc_get_page_permalink( 'cart' ) ) ),
        'is_cart'                 => is_cart(),
        'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' )
    );

    $wc_add_to_cart_variation = array(
        'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
        'i18n_make_a_selection_text'       => esc_attr__( 'Select product options before adding this product to your cart.', 'woocommerce' ),
        'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' )
    );

    $includes_url         = includes_url();
    $suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $lightbox_en          = 'yes' === get_option( 'woocommerce_enable_lightbox' );
    $ajax_cart_en         = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
    $assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
    $frontend_script_path = $assets_path . 'js/frontend/';

    $add_to_cart_variation_script_id = $this->slug."-add-to-cart-variation";
    $wp_util_script_id = $this->slug."-wp-util";
    $underscore_script_id = $this->slug."-underscore";
    ?>

    <script type="text/javascript">

        var wc_add_to_cart_params = <?php echo json_encode($wc_add_to_cart); ?>,
            wc_add_to_cart_variation_params = <?php echo json_encode($wc_add_to_cart_variation); ?>;

        // add +/- buttons to qty

        jQuery( '#jckqv div.quantity:not(.buttons_added), #jckqv td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<div class="<?php echo $this->slug; ?>-qty-spinners"><input type="button" value="+" data-dir="plus" class="<?php echo $this->slug; ?>-qty-spinner <?php echo $this->slug; ?>-qty-spinners__plus" /><input type="button" value="-" data-dir="minus" class="<?php echo $this->slug; ?>-qty-spinner <?php echo $this->slug; ?>-qty-spinners__minus" /></div>' );


        // remove script from previous modal

        jQuery('#<?php echo $underscore_script_id; ?>').remove();
        jQuery('#<?php echo $wp_util_script_id; ?>').remove();
        jQuery('#<?php echo $add_to_cart_variation_script_id; ?>').remove();

        // add script again
        // needs to be added every time of it doesn't work effectively

        // Underscore

        if(typeof _ !== 'function') {

            var underscore_script    = document.createElement("script");
            underscore_script.type   = "text/javascript";
            underscore_script.src    = "<?php echo $includes_url . '/js/underscore.min.js'; ?>";
            underscore_script.id     = "<?php echo $underscore_script_id; ?>";

            jQuery("head").append(underscore_script);

        }

        // wp-util

        if(typeof wp.template !== 'function') {

            var wp_util_script    = document.createElement("script");
            wp_util_script.type   = "text/javascript";
            wp_util_script.src    = "<?php echo $includes_url . '/js/wp-util' . $suffix . '.js'; ?>";
            wp_util_script.id     = "<?php echo $wp_util_script_id; ?>";

            jQuery("head").append(wp_util_script);

        }

        // add-to-cart-variation

        var add_to_cart_variation_script    = document.createElement("script");
        add_to_cart_variation_script.type   = "text/javascript";
        add_to_cart_variation_script.src    = "<?php echo $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js'; ?>";
        add_to_cart_variation_script.id     = "<?php echo $add_to_cart_variation_script_id; ?>";

        jQuery("head").append(add_to_cart_variation_script);

        <?php if($product->product_type == "variable") { ?>

            <?php $available_variations = $product->get_available_variations() ?>

            var jck_available_variations = <?php echo json_encode($available_variations); ?>;

        <?php } ?>

    </script>

<?php } ?>