<?php

if (!defined('ABSPATH')) {
    die;
}

class Guaven_woo_search_front
{
    private $cookieprods;
    public $wpml_existence;

    public function __construct()
    {
        if (get_option('guaven_woos_ispers') != '') {
            $this->cookieprods = 1;
        }
    }

    public function personal_interest_collector()
    {
        if (is_singular('product') and !empty($this->cookieprods)) {
            global $post;

            $products_personal = '';

            if (!empty($_COOKIE['guaven_woos_lastvisited'])) {
                $products_personal = stripslashes($_COOKIE['guaven_woos_lastvisited']);
            }

            $guaven_woo_search_admin = new Guaven_woo_search_admin();

            $_regular_price = get_post_meta($post->ID, '_regular_price', true);
            if ((int) $_regular_price == 0) {
                $_regular_price = get_post_meta($post->ID, '_price', true);
            }

            $sale_expiration = (int) strtotime(get_post_meta($post->ID, '_sale_price_dates_to', true)) - time();
            $sale_start = (int) strtotime(get_post_meta($post->ID, '_sale_price_dates_from', true)) - time();
            $sale_meta = ($sale_start < 0 and $sale_expiration > 0) ? get_post_meta($post->ID, '_sale_price', true) : '';

            if ($_regular_price > 0) {
                $_regular_price = number_format((float) $_regular_price, 2, '.', ' ');
            }
            if ($sale_meta > 0) {
                $sale_meta = number_format((float) $sale_meta, 2, '.', ' ');
            }

            $new_products_personal = array(
                'ID' => $post->ID,
                'thumb' => wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID)),
                'url' => get_permalink($post->ID),
                'title' => get_the_title($post->ID),
                'price' => $_regular_price,
                'sale' => $sale_meta,
                'currency' => get_woocommerce_currency_symbol(),
            );

            $htmlkeys = '<li class=\"guaven_woos_suggestion_list\" tabindex=\"'.$post->ID.'\">  '.$guaven_woo_search_admin->parse_template($new_products_personal,'persprod').' </li>';

            $products_personal = str_replace($htmlkeys, '', $products_personal);

            $max_cookie_res_count = get_option('guaven_woos_persmax') > 0 ? get_option('guaven_woos_persmax') : 5;

            $htmlkeys .= $products_personal;

            $htmlkeys_arr = explode('<li', $htmlkeys);
            if (count($htmlkeys_arr) > ($max_cookie_res_count + 1)) {
                $htmlkeys_arr = array_slice($htmlkeys_arr, 0, ($max_cookie_res_count + 1));
                $htmlkeys = implode('<li', $htmlkeys_arr);
            }

            setcookie('guaven_woos_lastvisited', $htmlkeys, time() + 86400, '/', null, 0);
        }
    }

    public function enqueue()
    {
        $js_css_version = (int) get_option('guaven_woos_jscss_version') + 1.44;

        if (get_option('guaven_woos_firstrun') != '') {
            wp_enqueue_script('guaven_woos', plugin_dir_url(__FILE__).'assets/guaven_woos.js', array(
                'jquery',
            ), $js_css_version, true);
        }

        if (get_option('guaven_woos_nojsfile') == '') {
            wp_enqueue_script('guaven_woos_data', plugin_dir_url(__FILE__).'assets/guaven_woos_data.js', array(
                'guaven_woos',
            ), $js_css_version, true);
        }

        wp_enqueue_style('guaven_woos', plugin_dir_url(__FILE__).'assets/guaven_woos.css', array(), $js_css_version);
    }

    public function inline_js()
    {
        require_once plugin_dir_path(dirname(__FILE__)).'public/view.php';
    }

    public function get_string($str)
    {
        if (strpos($str, 'wpml') === false) {
            return $str;
        }

        if (defined('ICL_LANGUAGE_CODE')) {
            $strarr = simplexml_load_string(urldecode(html_entity_decode(esc_attr(get_option('guaven_woos_showinit_n')))));
            $current_language = ICL_LANGUAGE_CODE;
            if (empty($strarr->$current_language)) {
                return $str;
            }

            return $strarr->$current_language;
        }

        //return $str;
    }

    public function backend_search_filter_old($where = '')
    {
        $is_woo = 1;

        if (isset($_GET['s']) and isset($_GET['post_type']) and $_GET['post_type'] == 'product') {
            global $wpdb;
            $sss = esc_sql($_GET['s']);
            $csql = "select a.ID as pid from $wpdb->posts a inner join $wpdb->postmeta b on a.ID=b.post_id
  where ".($is_woo == 1 ? "a.post_type='product' and" : '')." a.post_status='publish' and (b.meta_value like '%".$sss."%' or a.post_title like '%".$sss."%'
  or a.post_content like '%".$sss."%'
  )
      ";

            $sss_sql = $wpdb->get_results($csql);

            $prids = array();
            foreach ($sss_sql as $value) {
                $prids[] = $value->pid;
            }

            $csql2 = "select b.object_id as pid from $wpdb->terms a inner join $wpdb->term_relationships b on a.term_id=b.term_taxonomy_id where a.name like '%".$sss."%'";
            $sss_sql2 = $wpdb->get_results($csql2);
            foreach ($sss_sql2 as $value) {
                $prids[] = $value->pid;
            }
            $prids = array_unique($prids);

            if (!empty($prids)) {
                $where .= " or ( $wpdb->posts.ID in (".implode(',', $prids).') )';
            }

            return $where;
        }
    }

    public function backend_search_filter($where = '')
    {
        $is_woo = 1;$checkkeyword='';
        $backend_enable=get_option('guaven_woos_backend');
        if (!empty($_COOKIE['prids_object_cookie']) and !empty($_COOKIE['prids_keyword_cookie'])){
          $sanitize_cookie=preg_replace("/[^0-9,.]/", "", $_COOKIE['prids_object_cookie']);
          $checkkeyword=urldecode($_COOKIE['prids_keyword_cookie']);
        }
        if ($backend_enable!='' and !is_admin() and isset($_GET['s']) and $checkkeyword==$_GET['s']
         and isset($_GET['post_type']) and $_GET['post_type'] == 'product' and !empty($sanitize_cookie)) {
            global $wpdb;
            $where .= " or ( $wpdb->posts.ID in (".esc_sql(substr($sanitize_cookie, 0, -1)).') )';
        }

        return $where;
    }

    function guaven_woos_tracker_inserter($failsuccess,$state,$froback,$unid){
      $failed_arr=explode(", ",$failsuccess);
      $failed_arr_f[count($failed_arr)-1]=$failed_arr[count($failed_arr)-1];
      for($i=count($failed_arr)-2;$i>=0;$i--){
        if (strpos($failed_arr[$i+1],$failed_arr[$i])===false) $failed_arr_f[$i]=$failed_arr[$i];
      }
      $failed_arr_f=array_unique($failed_arr_f);
    //  var_dump($failed_arr_f);
    global $wpdb;
      foreach($failed_arr_f as $faf) {
        if (!empty($faf)){
        $wpdb->insert(
  $wpdb->prefix."woos_search_analytics",
  array(
    'keyword' => $faf,
    'created_date' => date("Y:m:d"),
    'user_info' => $unid,
    'state' => $state,
    'device_type'=>(wp_is_mobile()?'mobile':'desktop'),
    'side' => $froback
  ),
  array('%s','%s','%s','%s','%s') );}
      }
    }

    function guaven_woos_tracker_callback(){
      if (!isset($_POST["failed"]) or !isset($_POST["success"]) or !isset($_POST["corrected"]) or !isset($_POST["unid"])) exit;
      global $wpdb;
      $current_timestamp=time();
      $addcontrol=esc_attr($_POST["addcontrol"]);
      if ($current_timestamp-intval($addcontrol)>15) exit;
      check_ajax_referer('guaven_woos_tracker_'.$addcontrol, 'ajnonce');
      $this->guaven_woos_tracker_inserter($_POST["failed"],'fail','frontend',$_POST["unid"]);
      $this->guaven_woos_tracker_inserter($_POST["success"],'success','frontend',$_POST["unid"]);
      $this->guaven_woos_tracker_inserter($_POST["corrected"],'corrected','frontend',$_POST["unid"]);
      exit;
    }

    function add_async_attribute($tag, $handle) {
      if (get_option('guaven_woos_async')=='' or 'guaven_woos_data' !== $handle )
        return $tag;
    return str_replace( ' src', ' async="async" src', $tag );
}


}
