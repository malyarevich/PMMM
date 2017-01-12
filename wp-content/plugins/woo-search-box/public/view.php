<?php
if (!defined('ABSPATH')) {
    die;
}
?>
<script>
var focused=0;
guaven_engine_start_delay=<?php echo wp_is_mobile()?700:500;  ?>;
guaven_woos_showinit="<?php echo $this->get_string((get_option('guaven_woos_showinit') != '') ? get_option('guaven_woos_showinit_t') : '');?>";
guaven_woos_shownotfound="<?php echo $this->get_string((get_option('guaven_woos_showinit') != '') ? get_option('guaven_woos_showinit_n') : '');?>";
guaven_woos_populars_enabled=<?php   echo get_option('guaven_woos_nomatch_pops') != '' ? 1 : 0; ?>;
guaven_woos_categories_enabled=<?php echo get_option('guaven_woos_catsearch') != '' ? 1 : 0;?>;
cmaxcount=<?php echo get_option('guaven_woos_catsearchmax') > 0 ? get_option('guaven_woos_catsearchmax') : 5;?>;
guaven_woos_correction_enabled=<?php  echo get_option('guaven_woos_corr_act') != '' ? 1 : 0;?>;
guaven_woos_pinnedtitle="<?php if (get_option('guaven_woos_ispin') != '') echo $this->get_string(get_option('guaven_woos_pinnedt'));?>";
guaven_woos_sugbarwidth=<?php echo get_option('guaven_woos_sugbarwidth') > 0 ? (round(get_option('guaven_woos_sugbarwidth') * 100) / 10000) : '1';?>;
minkeycount=<?php echo get_option('guaven_woos_min_symb_sugg') > 0 ? get_option('guaven_woos_min_symb_sugg') : 3;?>;
maxcount=<?php echo get_option('guaven_woos_maxres') > 0 ? get_option('guaven_woos_maxres') : 10;?>;
maxtypocount=<?php echo get_option('guaven_woos_whentypo') > 0 ? get_option('guaven_woos_whentypo') : 'maxcount';?>;
guaven_woos_large_data=<?php echo get_option('guaven_woos_large_data') > 0 ? get_option('guaven_woos_large_data') : '0';?>;
guaven_woos_updir="<?php  $wpupdir=wp_upload_dir();echo $wpupdir['baseurl'];?>";
guaven_woos_exactmatch=<?php echo get_option('guaven_woos_exactmatch') > 0 ? get_option('guaven_woos_exactmatch') : '0';?>;
guaven_woos_backend=<?php echo get_option('guaven_woos_backend') !='' ? 1 : 0;?>;
guaven_woos_perst="<?php if ($this->cookieprods != '' and !empty($_COOKIE["guaven_woos_lastvisited"])) echo $this->get_string(get_option('guaven_woos_perst'));?>";
guaven_woos_persprod="<?php if ($this->cookieprods != '' and !empty($_COOKIE["guaven_woos_lastvisited"])) echo stripslashes($_COOKIE["guaven_woos_lastvisited"]);?>";
<?php
if (get_option('guaven_woos_data_tracking')!=''){
  ?>
guaven_woos_dttrr=1;
if (typeof(Storage) !== "undefined") {
guaven_woos_data = {
  "action": "guaven_woos_tracker",
  "ajnonce": "<?php  $controltime=time();  echo wp_create_nonce('guaven_woos_tracker_'.$controltime);?>",
  "addcontrol": "<?php echo $controltime; ?>",
};
guaven_woos_ajaxurl="<?php echo admin_url( 'admin-ajax.php' );?>";
}
  <?php
}
else echo 'guaven_woos_dttrr=0;';
        if (get_option('guaven_woos_nojsfile') != '') {
            echo get_option('guaven_woos_js_data');
        }
?>
guaven_woos_wpml="<?php
echo (defined('ICL_LANGUAGE_CODE')?'woolan_'.ICL_LANGUAGE_CODE:'');
?>";
  </script>
  <style>
<?php  if (get_option('guaven_woos_custom_css') != '') {
            echo stripslashes(get_option('guaven_woos_custom_css'));
        }
        ?>
  </style>
  <div class="guaven_woos_suggestion">
  </div>
