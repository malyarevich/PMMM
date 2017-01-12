<?php

    if (vp_option('woo_badge_man_opt.show_filter_on_archvie') == 1) {
        // options value from admin
        $filter_title = vp_option('woo_badge_man_opt.show_filter_title');
        $archive_single_product_badge_top = vp_option('woo_badge_man_opt.archive_single_product_badge_top');
        $archive_single_product_badge_bottom = vp_option('woo_badge_man_opt.archive_single_product_badge_bottom');
        $archive_single_product_badge_width = vp_option('woo_badge_man_opt.archive_single_product_badge_width');
        $archive_single_product_show_tooltip = vp_option('woo_badge_man_opt.archive_single_product_show_tooltip');
        $archive_single_product_show_tooltip_animation = vp_option('woo_badge_man_opt.archive_single_product_show_tooltip_animation');

        // show badge shortcode
        echo do_shortcode('[woo_pro_badges_filter title="'.$filter_title.'" margintop="'.$archive_single_product_badge_top.'" marginbottom="'.$archive_single_product_badge_bottom.'" size="'.$archive_single_product_badge_width.'" tooltip="'.$archive_single_product_show_tooltip.'" tooltip_animation="'.$archive_single_product_show_tooltip_animation.'"]');
    }

    // global var to work with data
    global $wpdb;
    // get badge id
    $badge_id = get_the_ID();
    // get custom fields value in array
    if ( vp_metabox('woo_pro_badge_cat_meta_box.woo_pro_badges_cat') ) {
     $show_cat = vp_metabox('woo_pro_badge_cat_meta_box.woo_pro_badges_cat');
    } else {
     $show_cat = array('no_category_assigned' => 'no_category_assigned');
    }

    // make custom arrow to fetch
    $show_cats = array();
    foreach ($show_cat as $key => $value) {
      $show_cats[$value] = array($key = $value);
    }
?>

<h3 class="product_from">Product From "<?php the_title(); ?>"</h3>

<div class="row">
<?php query_posts(array('post_type' => 'product', 'post_status' => 'publish', 'showposts' => -1)); ?>
<?php if (have_posts()) : while ( have_posts() ) : the_post(); ?>
    
    <?php
    // product id
    $product_id = get_the_ID();

    // get results from db
    $results = $wpdb->get_results( "SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = $product_id", ARRAY_A );
    // get results into arrow with key
    $result = array();
    if (!empty($results)) {
        foreach ($results as $key => $value) {
          foreach ($value as $key => $value) {
            $result[$value] = array($key => $value);
          }
        }
    } else {
        $result[] = array("no_cat_in_db" => "no_cat_in_db");
    }

    // get custom fields value in array
    if (vp_metabox('woo_pro_badge_meta_box.woo_pro_badges')) {
        $show_badges_id = vp_metabox('woo_pro_badge_meta_box.woo_pro_badges');
    }else {
        $show_badges_id = array('no_badges_to_show' => 'no_badges_to_show');
    }
    
    // instance product class
    $product = new WC_Product( get_the_ID() );
    // get product price
    $price = $product->price;
    
    //check condition to show product
    if(in_array($badge_id, $show_badges_id) || array_intersect_key($result, $show_cats)) {
        ?>
            <div class="<?php the_ID(); ?> col-lg-<?php echo vp_option('woo_badge_man_opt.archive_desktop_large_version'); ?> col-md-<?php echo vp_option('woo_badge_man_opt.archive_desktop_medium_version'); ?> col-sm-<?php echo vp_option('woo_badge_man_opt.archive_desktop_tablet_version'); ?> col-xs-<?php echo vp_option('woo_badge_man_opt.archive_desktop_mobile_version'); ?>">
               <div class="badge_archive_single">
                   <a href="<?php the_permalink(); ?>" alt="<?php the_title(); ?>">
                        <?php echo woocommerce_get_product_thumbnail(); ?>
                    </a>
                    <a href="<?php the_permalink(); ?>" class="badge_archive_single_title" alt="<?php the_title(); ?>">
                        <?php the_title(); ?>
                    </a>
                    <span class="badge_archive_single_product_price">
                        <?php echo get_woocommerce_currency_symbol(); echo $price; ?>
                    </span>
                    <span class="badge_archive_single_cartbtn">
                        <a data-background="<?php vp_option('woo_badge_man_opt.cart_btn_background'); ?>" href="<?php echo do_shortcode('[add_to_cart_url id="'.get_the_ID().'"]'); ?>">
                            <?php echo __('Add To Cart', 'woo_product_badge_manager_txtd');?>
                        </a>
                    </span>
                </div>
            </div>
        <?php
    }

    ?>

<?php endwhile; endif; // end of the loop. ?>
<?php wp_reset_query(); ?>
</div>