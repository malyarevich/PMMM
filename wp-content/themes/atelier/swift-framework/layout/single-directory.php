<?php
    global $sf_options, $sf_sidebar_config;

    /* META SETUP */
	$fullscreen = 'yes';
    $default_sidebar_config = $sf_options['default_sidebar_config'];
    $default_left_sidebar   = $sf_options['default_left_sidebar'];
    $default_right_sidebar  = $sf_options['default_right_sidebar'];

    $sidebar_config = sf_get_post_meta( $post->ID, 'sf_sidebar_config', true );
    $left_sidebar   = sf_get_post_meta( $post->ID, 'sf_left_sidebar', true );
    $right_sidebar  = sf_get_post_meta( $post->ID, 'sf_right_sidebar', true );
    
    /* MAP META */
    $map_lat_cord = sf_get_post_meta( $post->ID, 'sf_directory_lat_coord', true );
    $map_lng_cord = sf_get_post_meta( $post->ID, 'sf_directory_lng_coord', true );
    $map_address = sf_get_post_meta( $post->ID, 'sf_directory_address', true );
    $map_pin = sf_get_post_meta( $post->ID, 'sf_directory_map_pin', true );
    $map_pin_button_text = sf_get_post_meta( $post->ID, 'sf_directory_pin_button_text', true );
    $map_pin_link = sf_get_post_meta( $post->ID, 'sf_directory_pin_link', true );   

    if ( $sidebar_config == "" ) {
        $sidebar_config = $default_sidebar_config;
    }
    if ( $left_sidebar == "" ) {
        $left_sidebar = $default_left_sidebar;
    }
    if ( $right_sidebar == "" ) {
        $right_sidebar = $default_right_sidebar;
    }
    $page_content_class = $content_wrap_class = "";

    /* SIDEBAR SETUP */
    if ( $sidebar_config == "left-sidebar" ) {
    	$fullscreen = 'no';
        add_action( 'sf_after_post_content', 'sf_post_left_sidebar', 10 );
    } else if ( $sidebar_config == "right-sidebar" ) {
    	$fullscreen = 'no';
        add_action( 'sf_after_post_content', 'sf_post_right_sidebar', 10 );
    }

    /* PAGE BUILDER CHECK */
    $page_content_class = "container";

    /* CONTENT WRAP */
    $cont_width = $sidebar_width = "";
    if ($sf_options['sidebar_width'] == "reduced") {
    	$cont_width = apply_filters("sf_base_layout_cont_width_reduced", "col-sm-9");
    	$sidebar_width = apply_filters("sf_base_layout_cont_width_reduced_sidebar", "col-sm-3");
    } else {
    	$cont_width = apply_filters("sf_base_layout_cont_width", "col-sm-8");
    	$sidebar_width = apply_filters("sf_base_layout_cont_width_sidebar", "col-sm-4");
    }
    if ( $sidebar_config == "right-sidebar" ) {
        $content_wrap_class = apply_filters( 'sf_post_content_wrap_class', $cont_width . ' content-left' );
    } else if ( $sidebar_config == "left-sidebar" ) {
        $content_wrap_class = apply_filters( 'sf_post_content_wrap_class', $cont_width . ' content-right' );
    } else {
        $content_wrap_class = apply_filters( 'sf_post_content_wrap_class_nosidebar', 'col-sm-12 ' );
    }

    remove_action( 'sf_post_after_article', 'sf_post_pagination', 5 );
    remove_action( 'sf_post_after_article', 'sf_post_related_articles', 10 );
    remove_action( 'sf_post_after_article', 'sf_post_comments', 20 );
?>

<?php while (have_posts()) : the_post(); ?>

    <!-- OPEN article -->
    <article <?php post_class( 'clearfix single-directory' ); ?> id="<?php the_ID(); ?>">

        <?php
            /**
             * @hooked - sf_post_detail_heading - 10
             * @hooked - sf_post_detail_media - 20
             **/
            do_action( 'sf_post_article_start' );
        ?>
 
        <section class="page-content clearfix <?php echo esc_attr($page_content_class); ?>">
        

            <?php
                do_action( 'sf_before_post_content' );
            ?>
            

            <div class="content-wrap <?php echo esc_attr($content_wrap_class); ?> clearfix" itemprop="articleBody">
                <?php
                    /**
                     * @hooked - sf_post_detail_media - 10 (standard)
                     **/
                    do_action( 'sf_post_content_start' );
                ?>
                <?php the_content(); ?>
                <div class="link-pages"><?php wp_link_pages(); ?></div>
                
                <div class="single-directory-map">
                <h3><?php _e( 'Location', 'swiftframework' )?></h3>
       
                     <?php echo do_shortcode('[spb_gmaps size="400" type="roadmap" zoom="14" saturation="color" fullscreen="'.$fullscreen.'" width="1/1" el_position="first last"] [spb_map_pin pin_title="' . get_the_title() . '" address="' . $map_address .'" pin_latitude="' . $map_lat_cord . '" pin_longitude="' . $map_lng_cord . '" pin_image="' . $map_pin . '" pin_link="' . $map_pin_link . '" pin_button_text="' . $map_pin_button_text . '" width="1/1" el_position="first last"][/spb_map_pin] [/spb_gmaps]'); ?>
    
               </div>


                <?php
                    /**
                     * @hooked - sf_post_review - 20
                     * @hooked - sf_post_share - 30
                     * @hooked - sf_post_details - 40
                     **/
                    do_action( 'sf_post_content_end' );
                ?>
            </div>

            <?php
                /**
                 * @hooked - sf_post_left_sidebar - 10
                 * @hooked - sf_post_right_sidebar - 10
                 **/
                do_action( 'sf_after_post_content' );
            ?>

        </section>

        <?php
            do_action( 'sf_post_article_end' );
        ?>

        <!-- CLOSE article -->
    </article>
   
    <section class="article-extras">

        <?php
            /**
             * @hooked - sf_post_pagination - 5
             * @hooked - sf_post_related_articles - 10
             * @hooked - sf_post_comments - 20
             **/
            do_action( 'sf_post_after_article' );
        ?>

    </section>
   

<?php endwhile; ?>
