<?php

if (!defined('ABSPATH')) {
    die;
}

class Guaven_woo_search_admin
{
    public $woo_activeness;
    public $guaven_woos_firstrun;
    public $argv1;

    public function __construct()
    {
        if (get_option('guaven_woos_firstrun') == '') {
            $this->guaven_woos_firstrun = 1;
        }
    }

    public function run()
    {
        $this->save_settings();
        $cron_token = $this->cron_token();
        require_once plugin_dir_path(dirname(__FILE__)).'admin/view.php';
    }

    public function save_settings()
    {
        if ($this->woo_activeness != 1) {
            add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Error: Before using the plugin the main plugin WooCommerce has to be active. Activate it, then return to this page to continue', 'error');
        }

        if (isset($_POST['guaven_woos_nonce_f']) and wp_verify_nonce($_POST['guaven_woos_nonce_f'], 'guaven_woos_nonce')) {
            $this->to_default_runner();
            add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Success! All changes have been saved. Now just rebuild the cache.', 'updated');
        } elseif (isset($_POST['guaven_woos_reset_nonce_f']) and wp_verify_nonce($_POST['guaven_woos_reset_nonce_f'], 'guaven_woos_reset_nonce')) {
            $this->to_default_runner();
            add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Success! All settings now same as they were preinstalled', 'updated');
        } elseif (!empty($this->guaven_woos_firstrun)) {
            $this->to_default_runner();
            update_option('guaven_woos_firstrun', 1);
        }
    }

    private function to_default_runner()
    {
        $this->is_checked('guaven_woos_corr_act', 'checked');
        $this->is_checked('guaven_woos_showinit', 'checked');
        $this->is_checked('guaven_woos_ispers', '');
        $this->is_checked('guaven_woos_ispin', '');
        $this->is_checked('guaven_woos_nostock', '');
        $this->is_checked('guaven_woos_removehiddens', '');
        $this->is_checked('guaven_woos_variation_skus', '');
        $this->is_checked('guaven_woos_backend', '');
        $this->is_checked('guaven_woos_add_description_too', '');

        $this->string_setting('guaven_woos_autorebuild', '');
        $this->is_checked('guaven_woos_autorebuild_editor', '');

        $this->is_checked('guaven_woos_nomatch_pops', '');
        $this->string_setting('guaven_woos_popsmkey', 'total_sales');
        $this->string_setting('guaven_woos_popsmax', 5);

        $this->string_setting('guaven_woos_customorder', 'date');

        $this->is_checked('guaven_woos_catsearch', '');
        $this->string_setting('guaven_woos_catsearchmax', '5');

        $this->string_setting('guaven_woos_large_data', '');
        $this->string_setting('guaven_woos_exactmatch', '');

        $this->string_setting('guaven_woos_perst', 'Last watched products');
        $this->string_setting('guaven_woos_persmax', '5');
        $this->string_setting('guaven_woos_pinneds', '');
        $this->string_setting('guaven_woos_pinnedt', 'Featured products');
        $this->string_setting('guaven_woos_maxres', '10');
        $this->string_setting('guaven_woos_maxprod', '5000');
        $this->string_setting('guaven_woos_sugbarwidth', '100');
        $this->string_setting('guaven_woos_showinit_t', 'Find your product with fast search. Enter some keyword such as iphone, samsung, wear etc.');
        $this->string_setting('guaven_woos_showinit_n', 'No product found by your keyword');

        $this->string_setting('guaven_woos_customfields', '');
        $this->string_setting('guaven_woos_excluded_cats', '');
        $this->string_setting('guaven_woos_customtags', '');
        $this->string_setting('guaven_woos_custom_css', '');
        $this->string_setting('guaven_woos_synonyms', '');
        $this->string_setting('guaven_woos_async', '');

        $this->string_setting('guaven_woos_layout', addslashes($this->default_layout()));
        $this->string_setting('guaven_woos_min_symb_sugg', '3');
        $this->string_setting('guaven_woos_whentypo', '10');
    }

    private function is_checked($par, $defval = '')
    {
        if (isset($_POST[$par])) {
            $k = 'checked';
        } elseif (empty($_POST['guaven_woos_nonce_f']) and $defval != '') {
            $k = $defval;
        } else {
            $k = '';
        }
        update_option($par, $k);
    }

    private function string_setting($par, $def)
    {
        if (!empty($_POST[$par])) {
            $k = $_POST[$par];
        } else {
            $k = $def;
        }
        update_option($par, $k);
    }

    public function default_layout()
    {
        return '<a href="{url}"><div class="guaven_woos_div"><img class="guaven_woos_img" src="{imgurl}"></div><div class="guaven_woos_titlediv">{title}<br><small>{currency_regular}{price} {currency_sale}{saleprice}</small></div></a>';
    }

    public function cron_token()
    {
        $ret = get_option('guaven_woos_cronkey');
        if ($ret == '') {
            $ret = uniqid(time());
            update_option('guaven_woos_cronkey', $ret);
        }

        return $ret;
    }

/****** 1.3.1 - the next 5 new functions - for auto rebuilding */

private function dont_do_rebuild()
{
    $check_perm = get_option('guaven_woos_autorebuild');
    if ($check_perm == '') {
        $check_perm = 'b1a0';
    }
    $check_editor_role = get_option('guaven_woos_autorebuild_editor');
    if ($check_perm == 'b0a0') {
        return '0';
    } elseif (!current_user_can('manage_woocommerce')) {
        return '0';
    } elseif ($check_editor_role == '' and !current_user_can('manage_options')) {
        return '0';
    }

    return $check_perm;
}

    public function edit_hook_rebuilder()
    {
        if ($this->dont_do_rebuild() == '0' or $this->dont_do_rebuild() == 'b1a0') {
            return;
        }
        global $post;
        if (!empty($post->post_type) and $post->post_type == 'product') {
            update_option('do_woosearchbox_rebuild', time());
        }
    }

    public function do_rebuilder_at_footer()
    {
        if ($this->dont_do_rebuild() == '0') {
            return;
        }

        echo '<script>
    woos_dontclose=0;
    woos_data = {
      "action": "cache_rebuild_ajax",
      "ajnonce": "'.wp_create_nonce('cache_rebuild_ajax').'"
  };
window.onbeforeunload=function(){if (woos_dontclose==0) return; return "Cache rebuilding process is in progress... Are you sure to cancel it and close the page?";}
  jQuery(".rebuilder").click(function($) {
      guaven_woos_start_rebuild(woos_data);
  });
    function guaven_woos_start_rebuild(data) {
        jQuery(".Rebuild-SearchBox-Cache a").text("Rebuilding started...");
        woos_dontclose=1;
       jQuery.post(ajaxurl, data, function(response) {
              jQuery("#result_field").html(response+"% done...");
              jQuery("#result_field").css("display","block");
               if (response.indexOf("success_message") ==-1) {console.log("Woo Search Box Cache Rebuilding: "+response+"% done...");guaven_woos_start_rebuild(data);}
               else { jQuery("#result_field").html(response); jQuery(".Rebuild-SearchBox-Cache a").text("Rebuilding done!");woos_dontclose=0;
                 console.log("Woo Search Box Cache Rebuilding has been completed!"); }
           });
    }
';
        if (get_option('do_woosearchbox_rebuild') != '') {
            echo 'guaven_woos_start_rebuild(woos_data);';
        }
        echo '
jQuery(".Rebuild-SearchBox-Cache a").attr("href","javascript://");
</script>
  <style>.Rebuild-SearchBox-Cache a {background:#008ec2 !important}</style>';
    }

    public function woos_rebuild_top_button($wp_admin_bar)
    {
        if ($this->dont_do_rebuild() == '0' or $this->dont_do_rebuild() == 'b0a1' or !is_admin()) {
            return;
        }
        $args = array(
        'id' => 'my_page',
        'title' => 'Rebuild SearchBox Cache',
        'href' => 'javascript://',
        'meta' => array('class' => 'Rebuild-SearchBox-Cache rebuilder'),
    );
        $wp_admin_bar->add_node($args);
    }
/*******/

    public function cache_rebuild_ajax_callback()
    {
        $step_size = 100;

        global $wpdb;
        $pcount = $wpdb->get_var("select count(*) from $wpdb->posts where post_status='publish' and post_type='product'");

        if ($this->argv1 == $this->cron_token()) {
            $step_size = $pcount;
        } else {
            check_ajax_referer('cache_rebuild_ajax', 'ajnonce');
        }

        $mcount = (get_option('guaven_woos_maxprod') > 0 ? get_option('guaven_woos_maxprod') : 5000);
        $pcount = min($mcount, $pcount);

        $all_steps = ceil($pcount / $step_size);

        $msteps = (int) get_transient('guaven_woos_crs') + 1;

        $offset = $step_size * ($msteps - 1);

        if ($msteps == 1) {
            $this->cache_clean();

            $this->cache_rebuilder(0, array(), $step_size, 'guaven_woos_pinned_cache');

            if (get_option('guaven_woos_nomatch_pops') != '') {
                $max_pops_size = get_option('guaven_woos_maxres');
                $this->cache_rebuilder(0, array(), $max_pops_size, 'guaven_woos_populars_cache');
            }

            if (get_option('guaven_woos_catsearch') != '') {
                $max_pops_size = get_option('guaven_woos_maxres');
                $this->cache_category_rebuilder();
            }
        }

        $old_option_data = unserialize(get_option('guaven_woos_product_cache'));
        if (!is_array($old_option_data)) {
            $old_option_data = array();
        }

        set_transient('guaven_woos_crs', $msteps, 3600);

        $this->cache_rebuilder($offset, $old_option_data, $step_size, 'guaven_woos_product_cache', $pcount);

        if ($all_steps <= $msteps) {
            $final_version_for_js = $this->cache_final_prepare();

            $guaven_woows_jsfile = GUAVEN_WOO_SEARCH_PLUGIN_PATH.'public/assets/guaven_woos_data.js';
            if (is_writable(GUAVEN_WOO_SEARCH_PLUGIN_PATH.'public/assets/')) {
                file_put_contents($guaven_woows_jsfile, $final_version_for_js);
                chmod($guaven_woows_jsfile, 0777);
                update_option('guaven_woos_nojsfile', '');
                echo 'Separate JS file has been successfully generated.<br>';
            } else {
                echo '<b>Notice: </b> Your plugins folder is not writable by the system. That\'s why generated js data will be
            printed in your html code, not in separated js file.(it works in both cases, so don\'t worry) If you want to held js data separately, just make your plugins folder
            writable and then Rebuild the Cache again.<br><br>';
                update_option('guaven_woos_nojsfile', 1);
            }

            update_option('guaven_woos_js_data', $final_version_for_js);
            echo '<span class="success_message"></span>Cache rebuilding has been successfully completed!';

            $js_css_version = (int) get_option('guaven_woos_jscss_version') + 1;
            update_option('guaven_woos_jscss_version', $js_css_version);
            update_option('do_woosearchbox_rebuild', '');
            delete_transient('guaven_woos_crs');
            wp_die();
        }

        echo round($msteps * 10000 / $all_steps) / 100;

        wp_die();
    }

    private function cache_clean()
    {
        update_option('guaven_woos_product_cache', '');
        update_option('guaven_woos_pinned_cache', '');
    }

    private function cache_rebuilder($offset, $old_option_data, $step_size, $op_name = 'guaven_woos_product_cache', $totalproducts = 0)
    {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $step_size,
            'offset' => $offset,
            'suppress_filters' => true,
        );

        if (get_option('guaven_woos_customorder') != '') {
            $custom_order = get_option('guaven_woos_customorder');

            if (strpos($custom_order, 'meta:') !== false) {
                $args['meta_key'] = substr($custom_order, 5);
                if (is_int($args['meta_key'])) {
                    $args['orderby'] = 'meta_value_num';
                } else {
                    $args['orderby'] = 'meta_value';
                }
            } else {
                $args['orderby'] = $custom_order;
            }
            $args['order'] = 'DESC';
        }

        if ($op_name == 'guaven_woos_pinned_cache') {
            $pinstoimplode = get_option('guaven_woos_pinneds');
            $args['post__in'] = explode(',', $pinstoimplode);
        } elseif ($op_name == 'guaven_woos_populars_cache') {
            $popviews = get_option('guaven_woos_popsmkey');
            if ($popviews != '') {
                $args['meta_key'] = $popviews;
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                $args['posts_per_page'] = get_option('guaven_woos_popsmax');
            }
        }

        if (get_option('guaven_woos_nostock') == '') {
            $args['meta_query'] = array(array('key' => '_stock_status', 'value' => 'instock'));
        }

        if (get_option('guaven_woos_removehiddens') != '') {
            $args['meta_query'][] = array('key' => '_visibility', 'value' => 'hidden', 'compare' => '!=');
        }

        if (get_option('guaven_woos_excluded_cats') != ''){
          $args['tax_query'] = array(array(
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => explode(",",preg_replace("/[^0-9,.]/", "", get_option('guaven_woos_excluded_cats'))),
			'operator' => 'NOT IN',
		));
        }

        $products_for_json = get_posts($args);

        $products_array = array();
        foreach ($products_for_json as $key => $value) {
            $_regular_price = get_post_meta($value->ID, '_regular_price', true);
            if ((int) $_regular_price == 0) {
                $_regular_price = get_post_meta($value->ID, '_price', true);
            }

            //wpml part from v1.2.4
            $guaven_woos_wpml_key = '';
            if (defined('ICL_LANGUAGE_CODE') and function_exists('wpml_get_language_information')) {
                $woos_post_lan = wpml_get_language_information($value->ID);
                if (is_array($woos_post_lan)) {
                    $guaven_woos_wpml_key = '<span id="woolan_'.$woos_post_lan['locale'].'"></span>';
                }
            }

            //end of wpml part

            $title_and_hidden_sku = addslashes(get_the_title($value->ID).

            '<span class="woos_sku">'.get_post_meta($value->ID, '_sku', true).'</span>'.$guaven_woos_wpml_key);

            if (get_option('guaven_woos_variation_skus') != '') {
                //gather variation sku-s
           global $wpdb;
                $variation_skus_str = '';
                $variation_skus = $wpdb->get_results("select a.meta_value as mv from $wpdb->postmeta a inner join $wpdb->posts b on a.post_id=b.ID
where meta_key='_sku' and b.post_type='product_variation' and b.post_parent=".intval($value->ID));
                foreach ($variation_skus as  $vskkey) {
                    $variation_skus_str .= $vskkey->mv.', ';
                }
                if ($variation_skus_str != '') {
                    $title_and_hidden_sku .= addslashes('<span class="woos_sku woos_sku_variations">'.substr($variation_skus_str, 0, -2).'</span>');
                }
            }

            $add_description_too = get_option('guaven_woos_add_description_too');
            if ($add_description_too) {
                $title_and_hidden_sku .= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', addslashes(' <span class="guaven_woos_hidden_description">'.preg_replace('/\s+/', ' ', trim(strip_tags($value->post_content))).'</span>'));
            }

            $add_synonyms_too = get_option('guaven_woos_synonyms');
            if ($add_synonyms_too != '') {
                $ptitle = get_the_title($value->ID);
                $corresp_synonyms = $this->synonym_list($add_synonyms_too, $ptitle);
                if ($corresp_synonyms != '') {
                    $title_and_hidden_sku .= addslashes(' <span class="guaven_woos_hidden gwshd">'.$corresp_synonyms.'</span>');
                }
        //will remove guaven_woos_hidden from above line in 2017 january update
            }

            $add_tags_too = get_option('guaven_woos_customtags');
            if ($add_tags_too != '') {
                //taxonomies to be added
          $taxes_tba = explode(',', $add_tags_too);
                $tba_searchdata = '';
                foreach ($taxes_tba as $ttba) {
                    $term_list = wp_get_post_terms($value->ID,  $ttba);
                    foreach ($term_list as $term_single) {
                        $tba_searchdata .= $term_single->name.',';
                    }
                }

                $title_and_hidden_sku .= addslashes(' <span class="guaven_woos_hidden guaven_woos_hidden_tags">'.$tba_searchdata.'</span>');
            }

            $custom_fields = get_option('guaven_woos_customfields');
            $custom_fields_search_data = '';
            if ($custom_fields != '') {
                $cf_arr = explode(',', $custom_fields);
                foreach ($cf_arr as $cf_arr_el) {
                    $custom_fields_search_data .= ' '.get_post_meta($value->ID, $cf_arr_el, true);
                }
                $title_and_hidden_sku .= addslashes(' <span class="guaven_woos_hidden">'.$custom_fields_search_data.'</span>');
            }
            $check_sale_start=(int) strtotime(get_post_meta($value->ID, '_sale_price_dates_from', true));
            $sale_expiration = (int) strtotime(get_post_meta($value->ID, '_sale_price_dates_to', true)) - time();
            $sale_start = $check_sale_start - time();
            $sale_meta = ($check_sale_start==0 or ($sale_start < 0 and $sale_expiration > 0) ) ? get_post_meta($value->ID, '_sale_price', true) : '';

            if ($_regular_price > 0) {
                $_regular_price = number_format((float) $_regular_price, 2, '.', ' ');
            }
            if ($sale_meta > 0) {
                $sale_meta = number_format((float) $sale_meta, 2, '.', ' ');
            }
           //saves 20-30 bytes of traffic per product when there are more than 1000 products
           if ($totalproducts > 10) {
               $perlink = '?p='.$value->ID;
           } else {
               $perlink = get_permalink($value->ID);
           }

            $products_array[$value->ID] = array(
                'ID' => $value->ID,
                'thumb' => wp_get_attachment_thumb_url(get_post_thumbnail_id($value->ID)),
                'url' => $perlink,
                'title' => $title_and_hidden_sku,
                'price' => $_regular_price,
                'sale' => $sale_meta,
                'currency' => get_woocommerce_currency_symbol(),
            );
        }

        update_option($op_name, serialize(array_merge($old_option_data, $products_array)));
    }

    public function synonym_list($add_synonyms_too, $ptitle)
    {
        $corresp_synonyms = array();
        $synonym_list = explode(',', $add_synonyms_too);
        $synonym_list_res = array();
        $title_elements = explode(' ', addslashes(strtolower($ptitle)));
      //var_dump($title_elements);
      foreach ($synonym_list as $syn) {
          $syn_lr = explode('-', strtolower($syn));
    //    echo '<br>sins:'.$syn_lr[0].' - '.$syn_lr[1].' --<br>';
        if (in_array(trim($syn_lr[0]), $title_elements)) {
            $synonym_list_res[] = trim($syn_lr[1]);
        } elseif (in_array(trim($syn_lr[1]), $title_elements)) {
            $synonym_list_res[] = trim($syn_lr[0]);
        }
      }

        return implode(',', $synonym_list_res);
    }

    private function cache_category_rebuilder()
    {

      //update_option('guaven_woos_category_parent',16); //should be run in special cases
      $pcats_arg = array(
        'taxonomy' => 'product_cat', // >=WP 4.5.0
          'hide_empty' => false,
      );
        if (get_option('guaven_woos_category_parent') != '') {
            $pcats_arg['parent'] = (int) get_option('guaven_woos_category_parent');
        } //should be none in most cases
      $pcats = get_terms('product_cat', $pcats_arg);

        $products_cats_array = array();
        foreach ($pcats as $key => $value) {
            $products_cats_array[] = array(
                'ID' => $value->term_id,
                'title' => $value->name,
                'slug' => $value->slug,
                'parent' => $value->parent,
                'description' => $value->description,
            );
        }
        update_option('guaven_woos_category_cache', serialize($products_cats_array));
    }

    public function cache_final_prepare()
    {
        $guaven_woos_product_cache = unserialize(get_option('guaven_woos_product_cache'));
        $guaven_woos_pinned_cache = unserialize(get_option('guaven_woos_pinned_cache'));
        $guaven_woos_populars_cache = unserialize(get_option('guaven_woos_populars_cache'));

        $guaven_woos_category_cache = unserialize(get_option('guaven_woos_category_cache'));

        $cache_li_loop_main = $this->cache_li_loop($guaven_woos_product_cache);
        $cache_li_loop_pinned = $this->cache_li_loop($guaven_woos_pinned_cache);
        $cache_li_loop_pops = $this->cache_li_loop($guaven_woos_populars_cache);
        $cache_li_loop_cats = $this->cache_li_loop($guaven_woos_category_cache, 'product_cat');

        return 'var guaven_woos_cache_html={'.$cache_li_loop_main[0].'};
var guaven_woos_cache_keywords={' .$cache_li_loop_main[1].'};
var guaven_woos_pinned_html={' .$cache_li_loop_pinned[0].'};
var guaven_woos_pinned_keywords={' .$cache_li_loop_pinned[1].'};
var guaven_woos_populars_html={' .$cache_li_loop_pops[0].'};
var guaven_woos_populars_keywords={' .$cache_li_loop_pops[1].'};
var guaven_woos_category_html={' .$cache_li_loop_cats[0].'};
var guaven_woos_category_keywords={' .$cache_li_loop_cats[1].'};
';
    }

    public function cache_li_loop($guaven_woos_product_cache, $tip = '')
    {
        $gwsi = 0;
        $htmlkeys = '';
        $keywordkeys = '';
        if (is_array($guaven_woos_product_cache)) {
            foreach ($guaven_woos_product_cache as $guaven_woos_pck => $guaven_woos_pcv) {
                ++$gwsi;
                $htmlkeys .= $guaven_woos_pck.':"   <li class=\"guaven_woos_suggestion_list'.$tip.'\" tabindex=\"'.$gwsi.'\" id=\"prli_'.$guaven_woos_pcv["ID"].'\">  '.$this->parse_template($guaven_woos_pcv, $tip).' </li>",
   ';

                $keywordkeys .= $guaven_woos_pck.': "'.str_replace(array("\n", "\r"), '', addslashes(stripslashes($guaven_woos_pcv['title'])) ).'",
   ';
            }
        } else {
            return array(
                '',
                '',
            );
        }
        $htmlkeys=str_replace(
        array('  <li class=\"guaven_woos_suggestion_list'.$tip.'\" tabindex=\"','\"><div class=\"guaven_woos_div\"><img class=\"guaven_woos_img\" src=\"','\"></div><div class=\"guaven_woos_titlediv\">','</div></a> </li>'),
        array('{{l}}','{{d}}','{{i}}','{{e}}'),$htmlkeys);
        $keywordkeys=str_replace(array('</span> <span class=\"guaven_woos_hidden guaven_woos_hidden_tags\">'),array('{{s}}'),$keywordkeys);
        return array(
            $htmlkeys,
            $keywordkeys,
        );
    }

    public function parse_template($guaven_woos_pcv, $tip = '')
    {
        if ($tip == 'product_cat') {
            $parcat_s = '';
            if (!empty($guaven_woos_pcv['parent']) and $guaven_woos_pcv['parent'] > 0) {
                $parcat = get_term($guaven_woos_pcv['parent']);
                $parcat_s = '<span class="woos_cat_par_span">'.$parcat->name.' / </span>';
            }
            $pclink = get_term_link($guaven_woos_pcv['slug'], 'product_cat');
            if (is_wp_error($pclink)) {
                return;
            } //for rare cases
            return '<a class=\"guaven_woos_titlediv_cat\" href=\"'.$pclink.'\">'.addslashes($parcat_s.$guaven_woos_pcv['title']).'</a>';
        }

        $saleprice = $guaven_woos_pcv['sale'];

        $currency_regular = $guaven_woos_pcv['currency'];
        $price = $guaven_woos_pcv['price'];

        if ($saleprice > 0) {
            $price = '<span class=\"guaven_woos_thereissale\">'.$price.'</span>';
            $currency_sale = $guaven_woos_pcv['currency'];
        } else {
            $currency_sale = '';
        }

        if ((int) $price <= 0) {
            $currency_regular = '';
        }
       $wpuploaddir=wp_upload_dir();
        $find = array(
            '{url}',
            '{title}',
            '{imgurl}',
            '{price}',
            '{saleprice}',
            '{currency_regular}',
            '{currency_sale}',
        );
        $replace = array(
            $guaven_woos_pcv['url'],
            str_replace(array("\n", "\r"), '', $guaven_woos_pcv['title']),
            $guaven_woos_pcv['thumb'],
            $price,
            $saleprice,
            addslashes($currency_regular),
            addslashes($currency_sale),
        );

        if ($tip!='persprod') {$replace[1]='{{t}}';$replace[2]=str_replace($wpuploaddir['baseurl'],'{{u}}',$guaven_woos_pcv['thumb']);}

        if (!empty($guaven_woos_pcv['custom_fields'])) {
            foreach ($guaven_woos_pcv['custom_fields'] as $cs_key => $cs_value) {
                $find[] = '{'.$cs_key.'}';
                $replace[] = $cs_value;
            }
        }

        $data = get_option('guaven_woos_layout');

        return str_replace($find, $replace, $data);
    }

    public function admin_menu()
    {
        add_submenu_page('woocommerce', 'Guaven Woo Search', 'Search Suggestions', 'manage_options', __FILE__, array(
            $this,
            'run',
        ));
    }
}
