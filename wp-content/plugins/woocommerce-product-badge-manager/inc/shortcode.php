<?php
// create shortcode
function woo_pro_badges($atts){
  extract(shortcode_atts(array(
    'margintop' => '30px',
    'marginbottom' => '30px',
    'size' => 50,
    'tooltip' => 'yes',
    'tooltip_animation' => 'fade',
    'badge_description_page' => 'true',
  ), $atts));

  // create unique id
  static $id = 1;
  // global var to work with product data
  global $product, $woocommerce, $wpdb;  
  // product id
  $product_id = get_the_ID();
  // get product author
  $author = get_the_author_meta('ID');

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
  if (rwmb_meta( 'selected_badges', $args = array(), get_the_ID() )) {
    $show_badges_id = rwmb_meta( 'selected_badges', $args = array(), get_the_ID() );
  }else {
    $show_badges_id = array('no_badges_to_show' => 'no_badges_to_show');
  }

  // return shortcode with badge div
   $return_string = '<div class="badge_post_main" data-margintop="'.$margintop.'" data-marginbottom="'.$marginbottom.'">';

   if ($tooltip === 'yes') {
     // check condition and use tooltip
    $return_string .= '
      <script type="text/javascript" charset="utf-8">
        jQuery(document).ready(function() {
            jQuery(".yesshowtooltip'.$id.'").tooltipster({
              animation: "'.$tooltip_animation.'"
            });
        });
      </script>
    ';
   }

   // badge query
   query_posts(array('post_type' => 'woo_product_bages', 'orderby' => 'menu_order', 'order' => 'ASC', 'showposts' => -1));
   if (have_posts()) :
      while (have_posts()) : the_post();
        // get meta
        $badge_id = get_the_id();
        $badge_thumb_id = get_post_thumbnail_id();
        $badge_thumb_url = wp_get_attachment_thumb_url($badge_thumb_id,'thumbnail-size', true);

        // get custom fields value in array
        $show_cats = array();
        if ( rwmb_meta( 'woo_pro_badges_cat', $args = array(), get_the_ID() ) ) {
          $show_cat = rwmb_meta( 'woo_pro_badges_cat', $args = array(), get_the_ID() );
          foreach ($show_cat as $key => $value) {
            $show_cats[$value->term_id] = array($key = $value->term_id);
          }
        } else {
         $show_cat = array('no_category_assigned' => 'no_category_assigned');
        }

        // make custom arrow to fetch
        // $show_cats = array();
        // foreach ($show_cat as $key => $value) {
        //   $show_cats[$value->term_id] = array($key = $value->term_id);
        // }

        // get user meta from badge
        if (rwmb_meta( 'woo_pro_published_author', $args = array(), get_the_ID() )) {
          $show_author = rwmb_meta( 'woo_pro_published_author', $args = array(), get_the_ID() );
        } else {
          $show_author = array("no_author_assigned" => "No Author Assigned With This Product");
        }
        
        // check if badge have custom permalink from metabox
        $custom_badge_link = rwmb_meta( 'custom_badge_link', $args = array(), get_the_ID() );
        if ( !empty($custom_badge_link ) ) {
          $badge_desc_link = rwmb_meta( 'custom_badge_link', $args = array(), get_the_ID() );
        } else {
          $badge_desc_link = get_permalink();
        }
    
        // check condition and show results
        if ( in_array($badge_id, $show_badges_id) || array_intersect_key($result, $show_cats) || in_array($author, $show_author) ) {
          if ($badge_description_page === 'true') { 
            $return_string .= '<div class="single_badge" data-width="'.$size.'"><a href="'.$badge_desc_link.'"><img class="badge_thumb '.$tooltip.'showtooltip'.$id.'" title="'.get_the_title().'" src="'.$badge_thumb_url.'" alt="'.get_the_title().'" /></a></div>';
           } elseif ($badge_description_page === 'false') { 
            $return_string .= '<div class="single_badge" data-width="'.$size.'"><img class="badge_thumb '.$tooltip.'showtooltip'.$id.'" title="'.get_the_title().'" src="'.$badge_thumb_url.'" alt="'.get_the_title().'" /></div>';
           }
        }
      endwhile;
   endif;
   $return_string .= '</div>';

   wp_reset_query();

   // increment unique id
   $id++;

   // return everything
   return $return_string;
}
add_shortcode('woo_pro_badges', 'woo_pro_badges');

// shortcode for author page
function woo_author_bages($atts){
  extract(shortcode_atts(array(
    'margintop' => '30px',
    'marginbottom' => '30px',
    'size' => 50,
    'tooltip' => 'yes',
    'tooltip_animation' => 'fade',
    'badge_description_page' => 'true',
  ), $atts));
  // create unique id
  static $id = 1;
  // get product author
  $author = get_the_author_meta('ID');

  // return shortcode with badge div
   $return_string = '<div class="badge_post_main" data-margintop="'.$margintop.'" data-marginbottom="'.$marginbottom.'">';

   if ($tooltip === 'yes') {
     // check condition and use tooltip
    $return_string .= '
      <script type="text/javascript" charset="utf-8">
        jQuery(document).ready(function() {
            jQuery(".yesshowtooltip'.$id.'").tooltipster({
              animation: "'.$tooltip_animation.'"
            });
        });
      </script>
    ';
   }

   // badge query
   query_posts(array('post_type' => 'woo_product_bages', 'orderby' => 'menu_order', 'order' => 'ASC', 'showposts' => -1));
   if (have_posts()) :
      while (have_posts()) : the_post();
        // get meta
        $badge_id = get_the_id();
        $badge_thumb_id = get_post_thumbnail_id();
        $badge_thumb_url = wp_get_attachment_thumb_url($badge_thumb_id,'thumbnail-size', true);

        // get user meta from badge
        if (rwmb_meta( 'woo_pro_published_author', $args = array(), get_the_ID() )) {
          $show_author = rwmb_meta( 'woo_pro_published_author', $args = array(), get_the_ID() );
        } else {
          $show_author = array("no_author_assigned" => "No Author Assigned With This Product");
        }
        
        // check if badge have custom permalink from metabox
        $custom_badge_link = rwmb_meta( 'custom_badge_link', $args = array(), get_the_ID() );
        if ( !empty($custom_badge_link ) ) {
          $badge_desc_link = rwmb_meta( 'custom_badge_link', $args = array(), get_the_ID() );
        } else {
          $badge_desc_link = get_permalink();
        }

        // check condition and show results
        if ( in_array($author, $show_author) ) {
          if ($badge_description_page === 'true') { 
            $return_string .= '<div class="single_badge" data-width="'.$size.'"><a href="'.$badge_desc_link.'"><img class="badge_thumb '.$tooltip.'showtooltip'.$id.'" title="'.get_the_title().'" src="'.$badge_thumb_url.'" alt="'.get_the_title().'" /></a></div>';
           } elseif ($badge_description_page === 'false') { 
            $return_string .= '<div class="single_badge" data-width="'.$size.'"><img class="badge_thumb '.$tooltip.'showtooltip'.$id.'" title="'.get_the_title().'" src="'.$badge_thumb_url.'" alt="'.get_the_title().'" /></div>';
           }
        }
      endwhile;
   endif;
   $return_string .= '</div>';

   wp_reset_query();

   // increment unique id
   $id++;

   // return everything
   return $return_string;
}
add_shortcode('woo_author_bages', 'woo_author_bages');

// create shortcode for badge filter
function woo_pro_badges_filter($atts){
  extract(shortcode_atts(array(
    'title' => '',
    'margintop' => '',
    'marginbottom' => '',
    'size' => 50,
    'tooltip' => 'yes',
    'tooltip_animation' => 'fade'
  ), $atts));
  // create unique id
  static $id = 1;

  // return shortcode with badge div
   $return_string = '<div class="badge_post_main" data-margintop="'.$margintop.'" data-marginbottom="'.$marginbottom.'">';

   if ($tooltip === 'yes') {
     // check condition and use tooltip
    $return_string .= '
      <script type="text/javascript" charset="utf-8">
        jQuery(document).ready(function() {
            jQuery(".yesshowtooltip'.$id.'").tooltipster({
              animation: "'.$tooltip_animation.'"
            });
        });
      </script>
    ';
   }
    
    if($title) {
        $return_string .= '<h3 class="product_filter_title">'.$title.'</h3>';
    }

   // badge query
   query_posts(array('post_type' => 'woo_product_bages', 'orderby' => 'menu_order', 'order' => 'ASC', 'showposts' => -1));
   if (have_posts()) :
      while (have_posts()) : the_post();
        // get meta
        $badge_id = get_the_id();
        $badge_thumb_id = get_post_thumbnail_id();
        $badge_thumb_url = wp_get_attachment_thumb_url($badge_thumb_id,'thumbnail-size', true);
    
        // show results
		$return_string .= '<div class="single_badge" data-width="'.$size.'"><a href="'.get_permalink().'"><img class="badge_thumb '.$tooltip.'showtooltip'.$id.'" title="'.get_the_title().'" src="'.$badge_thumb_url.'" alt="'.get_the_title().'" /></a></div>';
      endwhile;
   endif;
   $return_string .= '</div>';

   wp_reset_query();

   // increment unique id
   $id++;

   // return everything
   return $return_string;
}
add_shortcode('woo_pro_badges_filter', 'woo_pro_badges_filter');