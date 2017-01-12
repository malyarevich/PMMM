<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "swift_framework_opts";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => $opt_name,
        'use_cdn' => TRUE,
        'display_name' => 'Swift Framework Options',
        'display_version' => FALSE,
        'page_title' => 'Swift Framework Options',
        'update_notice' => TRUE,
        'intro_text' => '',
        'footer_text' => '',
        'menu_type' => 'submenu',
        'allow_sub_menu' => TRUE,
        'menu_title' => 'Options',
        'page_parent' => 'swift-framework',
        'customizer' => FALSE,
        'admin_bar'  => FALSE,
        'dev_mode'  => FALSE,
        'default_mark' => '*',
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => TRUE,
        'output_tag' => TRUE,
        'settings_api' => TRUE,
        'cdn_check_time' => '1440',
        'compiler' => TRUE,
        'page_permissions' => 'manage_options',
        'save_defaults' => TRUE,
        'show_import_export' => TRUE,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
        'google_update_weekly' => TRUE,
        'google_api_key' => 'AIzaSyDmMdQn34pqGXdRPWRV-ukvBN78Qv2lHoc'
    );

    // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
    // $args['share_icons'][] = array(
    //     'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
    //     'title' => 'Visit us on GitHub',
    //     'icon'  => 'el el-github'
    //     //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
    // );
    // $args['share_icons'][] = array(
    //     'url'   => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
    //     'title' => 'Like us on Facebook',
    //     'icon'  => 'el el-facebook'
    // );
    // $args['share_icons'][] = array(
    //     'url'   => 'http://twitter.com/reduxframework',
    //     'title' => 'Follow us on Twitter',
    //     'icon'  => 'el el-twitter'
    // );
    // $args['share_icons'][] = array(
    //     'url'   => 'http://www.linkedin.com/company/redux-framework',
    //     'title' => 'Find us on LinkedIn',
    //     'icon'  => 'el el-linkedin'
    // );

    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */

    /*
     * ---> START HELP TABS
     */

    $tabs = array(
        array(
            'id'      => 'redux-help-tab-1',
            'title'   => __( 'Theme Information 1', 'admin_folder' ),
            'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
        ),
        array(
            'id'      => 'redux-help-tab-2',
            'title'   => __( 'Theme Information 2', 'admin_folder' ),
            'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
        )
    );
    Redux::setHelpTab( $opt_name, $tabs );

    // Set the help sidebar
    $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'admin_folder' );
    Redux::setHelpSidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */


    Redux::setSection( $opt_name, array(
        'title' => __('Page Builder', 'swift-builder'),
        'desc' => '',
        'subsection' => false,
        'icon' => 'el el-icon-adjust-alt',
        'fields' => array(
            array(
                'id' => 'disable_spb',
                'type' => 'button_set',
                'title' => __('Swift Page Builder', 'swiftframework'),
                'subtitle' => __('Enable/Disable the Swift Page Builder functionality with this option.', 'swiftframework'),
                'desc' => '',
                'options' => array('1' => 'Disabled','0' => 'Enabled'),
                'default' => '0'
                ),
            // array(
            //     'id' => 'spb_dynamic_scripts',
            //     'type' => 'button_set',
            //     'title' => __('Dynamic scripts/styles output', 'swiftframework'),
            //     'subtitle' => __('Enable/Disable dynamic output for the SPB scripts/styles. If you enable this, then any page/post where the Page Builder is not active on, will not load the scripts/styles for the builder output. This is an experimental feature, so please test your pages.', 'swiftframework'),
            //     'desc' => '',
            //     'options' => array('0' => 'Disabled','1' => 'Enabled'),
            //     'default' => '0'
            //     ),
            array(
                'id' => 'spb_color_scheme',
                'type' => 'select',
                'title' => __('Page Builder Color', 'swiftframework'),
                'subtitle' => "Choose the color for the Page Builder.",
                'options' => array(
                            'spb-black'       => 'Black',
                            'spb-blue'        => 'Blue ',
                            'spb-blue-grey'   => 'Blue Grey',
                            'spb-cyan'        => 'Cyan',
                            'spb-grey'        => 'Grey',
                            'spb-indigo'      => 'Indigo',
                            'spb-light-green' => 'Light Green',
                            'spb-orange'      => 'Orange',
                            'spb-pink'        => 'Pink', 
                            'spb-teal'        => 'Teal',
                        ),
                'desc' => '',
                'default' => 'spb-blue'
                ),
            array(
                'id'        => 'spb-post-types',
                'type'      => 'select',
                'data'      => 'post_types',
                'multi'     => true,
                'default'   => array('page', 'post', 'portfolio', 'team', 'spb-section'),
                'title'     => __('Page Builder Post Types', 'swiftframework'),
                'desc'      => __('Select here which post types you would like to enable the page builder for.', 'swiftframework'),
            ),
            array(
                'id' => 'show_textblock_text',
                'type' => 'button_set',
                'title' => __('Show Text Block Text', 'swiftframework'),
                'subtitle' => __('Enable/Disable the display of text block text on the text block elements in the builder.', 'swiftframework'),
                'desc' => '',
                'options' => array('1' => 'Enabled','0' => 'Disabled'),
                'default' => '1'
                ),
            array(
                'id' => 'spb_edit_modal_width',
                'type' => 'slider',
                'title' => __('Element Edit Modal Width', 'swiftframework'),
                'subtitle' => __("Set the defailt width for the element edit modal, by default this is 620px.", 'swiftframework'),
                "default" => "620",
                "min" => "400",
                "step" => "20",
                "max" => "2000",
            ),
            array(
                 'id' => 'shortcode_mapper_field',
                 'type' => 'shortcode_mapper',
                 'title' => __('Shortcode Mapper', 'swiftframework'),
                 'subtitle' => __('The shortcode mapper allows you to include custom shortcodes in the Swift Page Builder.', 'swiftframework')
            ),
        )
    ) ); 

    Redux::setSection( $opt_name, array(
        'type' => 'divide',
        'id' => 'divide-1'
    ) );

    Redux::setSection( $opt_name, array(
        'title' => __('Swift Slider', 'swiftframework'),
        'desc' => '',
        'subsection' => false,
        'icon' => 'el el-icon-website',
        'fields' => array(
            array(
                'id' => 'disable_ss',
                'type' => 'button_set',
                'title' => __('Swift Slider', 'swiftframework'),
                'subtitle' => __('Enable/Disable the Swift Slider functionality with this option.', 'swiftframework'),
                'desc' => '',
                'options' => array('1' => 'Disabled','0' => 'Enabled'),
                'default' => '0'
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'type' => 'divide',
        'id' => 'divide-2'
    ) );

    Redux::setSection( $opt_name, array(
        'title' => __('Custom Post Types', 'swift-builder'),
        'desc' => '',
        'subsection' => false,
        'icon' => 'el el-icon-view-mode',
        'fields' => array(
            array(
                'id' => 'cpt-disable',
                'type' => 'checkbox',
                'title' => __('Disable Custom Post Types', 'swiftframework'),
                'subtitle' => __('You can disable the custom post types used within the theme here, by checking the corresponding box. NOTE: If you do not want to disable any, then make sure none of the boxes are checked.', 'swiftframework'),
                'options' => array(
                    'portfolio' => 'Portfolio',
                    'galleries' => 'Galleries',
                    'team' => 'Team',
                    'clients' => 'Clients',
                    'testimonials' => 'Testimonials',
                    'directory' => 'Directory',
                    'faqs' => 'FAQ',
                    'spb-section' => 'SPB Section',
                ),
                'default' => array(
                    'portfolio' => '0',
                    'galleries' => '0',
                    'team' => '0',
                    'clients' => '0',
                    'testimonials' => '0',
                    'directory' => '0',
                    'swift-slider' => '0'
                )
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'type' => 'divide',
        'id' => 'divide-3'
    ) );

    Redux::setSection( $opt_name, array(
        'title' => __('Performance', 'swift-builder'),
        'desc' => '',
        'subsection' => false,
        'icon' => 'el el-icon-fire',
        'fields' => array(
            array(
                'id' => 'enable_min_styles',
                'type' => 'button_set',
                'title' => __('Load pre-minified stylesheets', 'swift-builder'),
                'subtitle' => __('Enable this option to load pre-minified stlysheets, for faster page speed.', 'swift-builder'),
                'desc' => '',
                'options' => array('1' => 'On', '0' => 'Off'),
                'default' => '1'
                ),
            array(
                'id' => 'enable_min_scripts',
                'type' => 'button_set',
                'title' => __('Load pre-minified scripts', 'swift-builder'),
                'subtitle' => __('Enable this option to load pre-minified scripts, for faster page speed.', 'swift-builder'),
                'desc' => '',
                'options' => array('1' => 'On', '0' => 'Off'),
                'default' => '1'
                ),
        )
    ) );

    /*
     * <--- END SECTIONS
     */
