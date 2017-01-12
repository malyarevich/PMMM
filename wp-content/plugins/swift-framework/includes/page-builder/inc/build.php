<?php

    /*
    *
    *   Swift Page Builder - Build Class
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    if ( ! defined( 'ABSPATH' ) ) {
        die( '-1' );
    }

    class SFPageBuilderSetup extends SFPageBuilderAbstract {

        public static $version = '4.0';
        protected $swift_page_builder;

        public function __construct() {
        }

        public function init( $settings ) {
            parent::init( $settings );
            $this->swift_page_builder = SwiftPageBuilder::getInstance();
            $this->setUpTheme();
        }

        public function activate() {
            //add_option( 'spb_do_activation_redirect', true );
        }

        public function setUpTheme() {
            $this->addAction( 'wp_enqueue_scripts', 'spb_register_frontend_css' );
            $this->addAction( 'wp_enqueue_scripts', 'spb_register_frontend_js' );
        }

        public function spb_register_frontend_css() {

            global $sf_opts, $is_IE;
            $enable_min_styles = false;
            if ( isset( $sf_opts['enable_min_styles'] ) ) {
                $enable_min_styles = $sf_opts['enable_min_styles'];
            }

            // Register Styles
            wp_register_style( 'spb-frontend', $this->frontendAssetURL( 'css/spb-styles.css' ), false, NULL, 'all' );
            wp_register_style( 'spb-frontend-min', $this->frontendAssetURL( 'css/spb-styles.min.css' ), false, NULL, 'all' );

            // Enqueue Style
            if ( $enable_min_styles && !$is_IE ) {
                wp_enqueue_style( 'spb-frontend-min' );
            } else {
                wp_enqueue_style( 'spb-frontend' );
            }
        }

        public function spb_register_frontend_js() {

            global $sf_opts, $is_IE;
            $enable_min_scripts = false;
            if ( isset( $sf_opts['enable_min_scripts'] ) ) {
                $enable_min_scripts = $sf_opts['enable_min_scripts'];
            }

            // Register Scripts
            wp_register_script( 'jquery-ui', $this->frontendAssetURL( 'js/lib/jquery-ui-1.11.4.custom.min.js' ), 'jquery', NULL, true );
            wp_register_script( 'parallax', $this->frontendAssetURL( 'js/lib/jquery.parallax.min.js' ), array( 'jquery' ), NULL, true );
            wp_register_script( 'mlparallax', $this->frontendAssetURL( 'js/lib/jquery.mlparallax.min.js' ), array( 'jquery' ), NULL, true );
            wp_register_script( 'easypiechart', $this->frontendAssetURL( 'js/lib/jquery.easypiechart.min.js' ), array( 'jquery' ), NULL, true );
            wp_register_script( 'countto', $this->frontendAssetURL( 'js/lib/jquery.countto.min.js' ), array( 'jquery' ), NULL, true );
            wp_register_script( 'countdown', $this->frontendAssetURL( 'js/lib/jquery.countdown.min.js' ), array( 'jquery' ), NULL, true );
            wp_register_script( 'vivus', $this->frontendAssetURL( 'js/lib/vivus.min.js' ), array( 'jquery' ), NULL, true );
            wp_register_script( 'isotope', $this->frontendAssetURL( 'js/lib/isotope.pkgd.min.js' ), array( 'jquery' ), NULL, true );
            wp_register_script( 'isotope-packery', $this->frontendAssetURL( 'js/lib/packery-mode.pkgd.min.js' ), array( 'jquery' ), NULL, true );              
            wp_register_script( 'matchHeight', $this->frontendAssetURL( 'js/lib/jquery.matchHeight-min.js' ), array( 'jquery' ), NULL, true );              
            wp_register_script( 'prism', $this->frontendAssetURL( 'js/lib/prism.js' ), array( 'jquery' ), NULL, true );              

            // Enqueue Script
            wp_enqueue_script('modernizr', $this->frontendAssetURL( 'js/lib/modernizr-custom.js' ), NULL, NULL, TRUE);
            if ( $enable_min_scripts && !$is_IE ) {
                wp_enqueue_script( 'spb-frontend-js-min', $this->frontendAssetURL( 'js/spb-functions.min.js' ), array( 'jquery' ), NULL, true );
            } else {
                wp_enqueue_script( 'spb-frontend-js', $this->frontendAssetURL( 'js/spb-functions.js' ), array( 'jquery' ), NULL, true );

            }
        }
    }

    /* SETUP FOR ADMIN
    ================================================== */

    class SFPageBuilderSetupAdmin extends SFPageBuilderSetup {

        public function __construct() {
            parent::__construct();
        }

        public function setUpTheme() {

            $this->swift_page_builder->addAction( 'edit_post', 'saveMetaBoxes' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_get_element_backend_html', 'elementBackendHtmlJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_shortcodes_to_builder', 'spbShortcodesJS_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_show_edit_form', 'showEditFormJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_show_small_edit_form', 'showSmallEditFormJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_save_template', 'saveTemplateJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_load_template', 'loadTemplateJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_save_element', 'saveElementJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_load_element', 'loadElementJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_delete_element', 'deleteElementJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_sf_load_template', 'loadSFTemplateJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_delete_template', 'deleteTemplateJavascript_callback' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_save_pb_history', 'spb_save_pb_history' );
            $this->swift_page_builder->addAction( 'wp_ajax_spb_track_used_elements', 'spb_track_used_elements' );

            /* Add dynamic admin css */
            $this->addAction( 'admin_footer-post.php', 'spb_admin_css' );
            $this->addAction( 'admin_footer-post-new.php', 'spb_admin_css' );

            /* Add specific CSS class by filter */
            $this->addFilter( 'body_class', 'spb_body_class' );
            $this->addFilter( 'admin_body_class', 'spb_admin_body_class' );
            $this->addFilter( 'get_media_item_args', 'spb_js_forcesend' );

            $this->addAction( 'admin_init', 'spb_edit_page', 5 );

            $this->addAction( 'admin_init', 'spb_register_css' );
            $this->addAction( 'admin_init', 'spb_register_js' );

            $this->addAction( 'admin_print_scripts-post.php', 'spb_scripts' );
            $this->addAction( 'admin_print_scripts-post-new.php', 'spb_scripts' );
        }

        public function spb_body_class( $classes ) {
            $classes[] = 'page-builder page-builder-version-' . SPB_VERSION;
            return $classes;
        }

        public function spb_admin_body_class( $classes ) {

            global $sf_opts;
            $show_textblock_text = false;

            if ( isset($sf_opts['show_textblock_text']) ) {
                $show_textblock_text = $sf_opts['show_textblock_text'];
            }

            if ( $show_textblock_text ) {
               $classes = $classes . ' spb-show-holder-text';
            }

            return $classes;
        }

        public function spb_admin_css(  ) {
            global $sf_opts;
            $spb_edit_modal_width = "";
            if ( isset($sf_opts['spb_edit_modal_width']) ) {
                $spb_edit_modal_width = $sf_opts['spb_edit_modal_width'];
            }
            ?>
            
            <style type="text/css" media="screen">
                <?php if ( $spb_edit_modal_width != "" ) {
                    echo '.spb_edit_form_elements {width: '.$spb_edit_modal_width.'px;}';
                } ?>
            </style>

            <?php
        }

        public function spb_js_forcesend( $args ) {
            $args['send'] = true;

            return $args;
        }

        public function spb_scripts() {
  
            $enable_min_scripts = true;

            // Styles
            wp_enqueue_style( 'materialize-components-css' );                
            wp_enqueue_style( 'spb-bootstrap' );
            wp_enqueue_style( 'ui-custom-theme' );
            if ( $enable_min_scripts ) {
                wp_enqueue_style( 'page-builder-min' );
            } else {
                wp_enqueue_style( 'page-builder' );  
            }
            wp_enqueue_style( 'colorpicker' );
            wp_enqueue_style( 'uislider' );
            wp_enqueue_style( 'chosen' );
            if ( spb_theme_supports('icon-mind-font') ) {
            wp_enqueue_style( 'ss-iconmind' );
            }
            if ( spb_theme_supports('gizmo-icon-font') ) {
            wp_enqueue_style( 'ss-gizmo' );
            }
            if ( spb_theme_supports('nucleo-general-font') ) {
            wp_enqueue_style( 'nucleo' );
            }
            if ( spb_theme_supports('simple-line-icons-font') ) {
            wp_enqueue_style( 'simple-line-icons' );
            }
            wp_enqueue_style( 'fontawesome' );
            wp_enqueue_style( 'materialicons' );

            // Scripts
            wp_enqueue_script( 'spb-bootstrap' );
            wp_enqueue_script( 'materialize' );  
            //wp_enqueue_script( 'materialize-init' );
            wp_enqueue_script( 'jquery-ui' );  
            wp_enqueue_script( 'jquery-ui-tabs' );
            wp_enqueue_script( 'jquery-ui-droppable' );
            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            wp_enqueue_script( 'jquery-ui-button' );
            if ( $enable_min_scripts ) {
                wp_enqueue_script( 'page-builder-min' );
            } else {
                wp_enqueue_script( 'page-builder' );
            }
            wp_enqueue_script( 'colorpicker-js' );
            wp_enqueue_script( 'uislider-js' );
            wp_enqueue_script( 'chosen-js' );
            wp_enqueue_script( 'spb-maps' );
            wp_enqueue_style( 'swift-pb-font' );   
            wp_enqueue_script( 'touch-punch' );
            
        }

        public function spb_register_js() {

            $gmaps_api_key = get_option('sf_gmaps_api_key');
            $wp_locale = get_locale();
         
            if ( $wp_locale != '' ) {
                $wp_locale = '&language=' . $wp_locale;
            }
            
            wp_register_script( 'touch-punch', $this->assetURL( 'js/jquery.ui.touch-punch.js'), 'jquery' , null, true );
            wp_register_script( 'jquery-ui', $this->assetURL( 'js/jquery-ui.min.js'), 'jquery' , null, true );
            wp_register_script( 'materialize', $this->assetURL( 'materialize/js/materialize.js' ), array( 'jquery' ), SPB_VERSION, true );
            wp_register_script( 'page-builder', $this->assetURL( 'js/page-builder.js' ), array( 'jquery' ), SPB_VERSION, true );
            wp_register_script( 'page-builder-min', $this->assetURL( 'js/page-builder.min.js' ), array( 'jquery' ), SPB_VERSION, true );
            wp_register_script( 'spb-bootstrap', $this->assetURL( 'js/bootstrap.min.js' ), false, SPB_VERSION, true );
            wp_register_script( 'colorpicker-js', $this->assetURL( 'js/jquery.minicolors.min.js' ), array( 'jquery' ), SPB_VERSION, true );
            wp_register_script( 'uislider-js', $this->assetURL( 'js/jquery.nouislider.min.js' ), array( 'jquery' ), SPB_VERSION, true );
            wp_register_script( 'chosen-js', $this->assetURL( 'js/chosen.jquery.min.js' ), array( 'jquery' ), SPB_VERSION, true );
            wp_register_script( 'spb-maps', '//maps.google.com/maps/api/js?key=' . $gmaps_api_key . $wp_locale, 'jquery', null, true );
        }
  
        public function spb_register_css() {    
            wp_register_style( 'swift-pb-font', $this->assetURL( 'css/swift-pb.css' ), false, null, false );
            wp_register_style( 'materialize-components-css', $this->assetURL( 'materialize/css/materialize.css' ), false, null, false );
            wp_register_style( 'spb-bootstrap', $this->assetURL( 'css/bootstrap.css' ), false, SPB_VERSION, false );
            wp_register_style( 'page-builder', $this->assetURL( 'css/page-builder.css' ), false, null, false );
            wp_register_style( 'page-builder-min', $this->assetURL( 'css/page-builder.min.css' ), false, null, false );
            wp_register_style( 'colorpicker', $this->assetURL( 'css/jquery.minicolors.css' ), false, null, false );
            wp_register_style( 'uislider', $this->assetURL( 'css/jquery.nouislider.min.css' ), false, null, false );
            wp_register_style( 'chosen', $this->assetURL( 'css/chosen.min.css' ), false, null, false );
            if ( spb_theme_supports('icon-mind-font') ) {
            wp_register_style( 'ss-iconmind', get_template_directory_uri() . '/css/iconmind.css', array(), null, 'all' );
            }
            if ( spb_theme_supports('gizmo-icon-font') ) {
            wp_register_style( 'ss-gizmo', get_template_directory_uri() . '/css/ss-gizmo.css', array(), null, 'all' );
            }
            if ( spb_theme_supports('nucleo-general-font') ) {
            wp_register_style( 'nucleo', get_template_directory_uri() . '/css/iconfont.css', array(), null, 'all' );
            }
            if ( spb_theme_supports('simple-line-icons-font') ) {
            wp_register_style( 'simple-line-icons', get_template_directory_uri() . '/css/simple-line-icons.css', array(), null, 'all' );
            }
            wp_register_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), null, 'all' );
            wp_register_style( 'materialicons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), null, 'all' );
        }
  
        public function spb_edit_page() {


            $pt_array = $this->swift_page_builder->getPostTypes();   
            foreach ( $pt_array as $pt ) {  
                add_meta_box( 'spb', __( 'Swift Page Builder', 'swift-framework-plugin' ), Array(
                        $this->swift_page_builder->getLayout(),
                        'output'
                    ), $pt, 'normal', 'high' );
            }
        }
    }

?>