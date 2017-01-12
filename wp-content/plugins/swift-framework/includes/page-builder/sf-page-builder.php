<?php

    if ( ! defined( 'ABSPATH' ) ) {
        die( '-1' );
    }


    /* DEFINITIONS
    ================================================== */
    define( 'SPB_VERSION', '4.0' );
    define( 'SPB_PATH', dirname( __FILE__ ) );
    $spb_settings = Array(
        'SPB_ROOT'       => SPB_PATH . '/',
        'SPB_DIR'        => basename( SPB_PATH ) . '/',
        'SPB_ASSETS'     => plugin_dir_url( __FILE__ ) . 'assets/',
        'SPB_ASSETS_FRONTEND'     => plugin_dir_url( __FILE__ ) . 'frontend-assets/',
        'SPB_INC'        => SPB_PATH . '/inc/',
        'SPB_SHORTCODES' => SPB_PATH . '/shortcodes/'
    );
    $inc_dir      = $spb_settings['SPB_INC'];
    define( 'SPB_SHORTCODES', $spb_settings['SPB_SHORTCODES'] );


    /* INCLUDE INC FILES
    ================================================== */
    require_once( $inc_dir . 'abstract.php' );
    require_once( $inc_dir . 'asset-functions.php' );
    require_once( $inc_dir . 'helpers.php' );
    require_once( $inc_dir . 'mapper.php' );
    require_once( $inc_dir . 'shortcodes.php' );
    require_once( $inc_dir . 'builder.php' );
    require_once( $inc_dir . 'layouts.php' );
    require_once( $inc_dir . 'templates.php' );


    /* INCLUDE SHORTCODE FILES
    ================================================== */
    if ( ! function_exists( 'spb_register_assets' ) ) {
        function spb_register_assets() {
            $pb_assets = array();
            $path      = dirname( __FILE__ ) . '/shortcodes/';
            $folders   = scandir( $path, 1 );
            foreach ( $folders as $file ) {
                if ( $file == '.' || $file == '..' || $file == '.DS_Store' || strpos($file,'.php') != true ) {
                    continue;
                }
                $file               = substr( $file, 0, - 4 );
                $pb_assets[ $file ] = SPB_SHORTCODES . $file . '.php' ;
            }

            $pb_assets = apply_filters( 'spb_assets_filter', $pb_assets );

            if ( ! sf_gravityforms_activated() ) {
                unset( $pb_assets['gravityforms'] );
            }
            if ( ! sf_woocommerce_activated() ) {
                unset( $pb_assets['products'] );
                unset( $pb_assets['product-reviews'] );
            }
            if ( ! sf_gopricing_activated() ) {
                unset( $pb_assets['gopricing'] );
            }
            if ( ! sf_ninjaforms_activated() ) {
                unset( $pb_assets['ninjaforms'] );
            }

            // Load Each Asset
            foreach ( $pb_assets as $asset ) {
                require_once( $asset );
            }

        }

        if ( is_admin() ) {
            add_action( 'admin_init', 'spb_register_assets', 2 );
        }
        if ( ! is_admin() ) {
            add_action( 'wp', 'spb_register_assets', 2 );
        }
    }


    /* INCLUDE BUILDER SETUP
    ================================================== */
    require_once( $inc_dir . 'build.php' );


    /* LAYOUT & SHORTCODE SETUP
    ================================================== */
    require_once( $inc_dir . 'default-map.php' );


    /* INITIALISE BUILDER
    ================================================== */
    $wpSPB_setup = is_admin() ? new SFPageBuilderSetupAdmin() : new SFPageBuilderSetup();
    $wpSPB_setup->init( $spb_settings );
