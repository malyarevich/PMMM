<?php
class WOO_PRO_BADGE_FILTER extends WP_Widget{
    // create construct of widget
    function __construct(){
      parent::__construct(
          // widget id
          'filter_badge_widget',
          // widget title
          __('Badge Product Filter', 'woo_product_badge_manager_txtd'),
          array(
             'classname' => 'filter_badge_widget',
             'description' => __('Product filters using badge list.', 'woo_product_badge_manager_txtd')
          )
      );
    }
    
    // widget fields
    function form($instance){
        $instance = wp_parse_args((array)$instance, array(
            'title'	=> '',
            'badge_size'	=> '',
            'tooltip'	=> '',
            'tooltip_animation'	=> ''
        ));
        $title = strip_tags($instance['title']);
        $badge_size = strip_tags($instance['badge_size']);
        $tooltip = strip_tags($instance['tooltip']);
        $tooltip_animation = strip_tags($instance['tooltip_animation']);
        include(WOO_PRODUCT_BADGE_MANAGER_DIR . '/widget/badge_filter_fields.php');
    }
    
    //save widget fields old value
    function update($new_instance, $old_instance){
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['badge_size'] = $new_instance['badge_size'];
        $instance['tooltip'] = $new_instance['tooltip'];
        $instance['tooltip_animation'] = $new_instance['tooltip_animation'];
        return $instance;
    }
    
    // extrat widget valule in front-end
    function widget($args,$instance){
        extract( $args, EXTR_SKIP );
        echo $before_widget;
        include(WOO_PRODUCT_BADGE_MANAGER_DIR . '/widget/badge_filter_front.php');
        echo $after_widget;
    }
}
add_action('widgets_init',create_function('','register_widget("WOO_PRO_BADGE_FILTER");') );
