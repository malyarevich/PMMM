<?php
    /*
    *
    *	Swift Slider
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    if ( ! defined( 'ABSPATH' ) ) {
        die( '-1' );
    }


    /* DEFINITIONS
    ================================================== */
    define( 'SS_VERSION', '2.0' );
    define( 'SS_PATH', plugin_dir_path( __FILE__ ) );
    define( 'SS_INC_PATH', SS_PATH . 'inc' );
    define( 'SS_ASSET_URL', plugin_dir_url( __FILE__ ) . 'assets' );


    /* SLIDER POST TYPE
    ================================================== */
    include_once( SS_INC_PATH . '/ss-post-type.php' );


    /* SLIDER OUTPUT
    ================================================== */
    include_once( SS_INC_PATH . '/ss-display.php' );


    /* SLIDER META BOXES
    ================================================== */
    include_once( SS_INC_PATH . '/ss-meta.php' );


    /* SLIDER CAPTION SHORTCODES
    ================================================== */
    include_once( SS_INC_PATH . '/ss-shortcodes.php' );


    /* SLIDER BACKEND CSS
    ================================================== */
    function ss_backend_css() {

        // Register Scripts
        wp_register_style( 'ss-backend', SS_ASSET_URL . '/css/ss-backend.css', array(), null, 'all' );

        // Enqueue Scripts
        wp_enqueue_style( 'ss-backend' );

    }

    add_action( 'admin_init', 'ss_backend_css' );


    /* SLIDER BACKEND JS
    ================================================== */
    function ss_backend_js() {

        // Register Scripts
        wp_register_script( 'ss-backend', SS_ASSET_URL . '/js/ss-backend.js', 'jquery', null, true );

        // Enqueue Scripts
        wp_enqueue_script( 'ss-backend' );

    }

    add_action( 'admin_init', 'ss_backend_js' );


    /* SLIDER FRONTEND CSS
    ================================================== */
    function ss_frontend_css() {

	    global $sf_opts, $is_IE;
	    $enable_min_styles = false;

        if ( isset( $sf_opts['enable_min_styles'] ) ) {
        	$enable_min_styles = $sf_opts['enable_min_styles'];
		}

        // Register Styles
        wp_register_style( 'swift-slider', SS_ASSET_URL . '/css/swift-slider.css', array(), null, 'all' );
        wp_register_style( 'swift-slider-min', SS_ASSET_URL . '/css/swift-slider.min.css', array(), null, 'all' );

        // Enqueue Style
        if ( $enable_min_styles && !$is_IE ) {
            wp_enqueue_style( 'swift-slider-min' );
        } else {
            wp_enqueue_style( 'swift-slider' );
        }

    }

    add_action( 'wp_enqueue_scripts', 'ss_frontend_css' );


    /* SLIDER FRONTEND JS
    ================================================== */
    function ss_frontend_js() {

        global $sf_opts;
        $enable_min_scripts = $sf_opts['enable_min_scripts'];

        // Register Scripts
        wp_register_script( 'swift-slider', SS_ASSET_URL . '/js/swift-slider.js', 'jquery', null, true );
        wp_register_script( 'swift-slider-min', SS_ASSET_URL . '/js/swift-slider.min.js', 'jquery', null, true );

        // Enqueue Script
        if ( $enable_min_scripts ) {
            wp_enqueue_script( 'swift-slider-min' );
        } else {
            wp_enqueue_script( 'swift-slider' );
        }

    }

    add_action( 'wp_enqueue_scripts', 'ss_frontend_js' );


    function ss_frontend_js_tag( $tag, $handle ) {
        if ( 'swift-slider' !== $handle && 'swift-slider-min' !== $handle ) {
            return $tag;
        }
        return str_replace( ' src', ' data-cfasync="true" src', $tag );
    }
    add_filter( 'script_loader_tag', 'ss_frontend_js_tag', 10, 2 );


    /* WPML DUPLICATION FIX
    ================================================== */
    function sf_wpml_override_post_duplication( $value_to_filter, $target_language, $meta_data ) {
       if ( $meta_data[ 'key' ] === 'sf_ss_category' && $meta_data[ 'context' ] === 'custom_field' ) {
          $tag = get_term_by( 'slug', $value_to_filter, 'swift-slider-category');

          $translated_id = apply_filters( 'wpml_object_id', (int)$tag->term_id, 'swift-slider-category', true, $target_language  );

          $translated_term = get_term_by( 'id', $translated_id, 'swift-slider-category');

          $value_to_filter = $translated_term->slug;
       }

       return $value_to_filter;
    }
    add_filter( 'wpml_duplicate_generic_string', 'sf_wpml_override_post_duplication', 10, 3 );
    