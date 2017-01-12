<?php

/**
 * Plugin Name: WooCommerce Pretty Emails
 * Description: Customize your WooCommerce emails.
 * Version: 1.7
 * Author: MB Création
 * Author URI: http://www.mbcreation.net
 * License: http://codecanyon.net/licenses/regular_extended
 *
 */



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Load the auto-update class
add_action( 'admin_init', 'activate_wpe_mbcreation' ); 

function activate_wpe_mbcreation()
{
	if(!class_exists('WPMBC_AutoUpdate'))
		require_once ( dirname(__FILE__).'/wp_autoupdate.php' );
	$plugin_data = get_plugin_data( __FILE__ );
	$plugin_current_version = $plugin_data['Version'];
	$plugin_remote_path = 'http://www.mbcreation.com/plugin/woocommerce-pretty-emails/';	
	$plugin_slug = plugin_basename( __FILE__ );
	new WPMBC_AutoUpdate( $plugin_current_version, $plugin_remote_path, $plugin_slug );	
}

/**
 * WooCommerce_Pretty_Emails
 * 
 * @since 1.0
 */

if ( ! class_exists( 'WooCommerce_Pretty_Emails' ) ) {

class WooCommerce_Pretty_Emails {



		private $_templates = array();

		private $_context = false;
		
		function __construct(){

			define('MBWPE_TPL_PATH', plugin_dir_path(__FILE__).'/emails');

			$this->_templates = array(
				
				'emails/email-header.php',
				'emails/email-footer.php',
				'emails/customer-completed-order.php',
				'emails/admin-new-order.php',
				'emails/customer-invoice.php',
				'emails/customer-new-account.php',
				'emails/customer-processing-order.php',
				'emails/customer-on-hold-order.php',
				'emails/customer-reset-password.php',
				'emails/customer-note.php',
				'emails/email-addresses.php',
				'emails/email-order-items.php',
				'emails/admin-cancelled-order.php',
				'emails/customer-refunded-order.php',
				'emails/admin-failed-order.php',
				'emails/email-customer-details.php'
			
			); 

			
			load_plugin_textdomain('mbc-pretty-emails', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
			add_action('plugins_loaded', array(&$this, 'hooks') );
			register_activation_hook( __FILE__ , array(&$this , 'install' ) );

		}

		public function hooks(){

			// Arrêter l'execution du plugin si WooCommerce n'exite plus.
			
			if( !class_exists( 'Woocommerce' ) )
			return;

			add_filter('woocommerce_email_settings', array( $this, 'extra_settings') );
			add_filter('wc_get_template', array( $this, 'custom_template') , 999, 5 );
			add_action( 'admin_init', array( $this, 'preview_emails' ) );


			// Since 1.6.
			add_action( 'woocommerce_pretty_before_template', array($this, 'display_banners'), 20 );
			// Since 1.7.
			add_action( 'woocommerce_pretty_before_template', array($this, 'add_attachment'), 30 );


		}


		public function extra_settings($settings){

			$settings[] = array( 'title' => __( 'WooCommerce Pretty Emails', 'mbc-pretty-emails' ), 'type' => 'title', 'desc' => __( 'Please feel free to send us an email if you have any issue at support@mbcreation.net', 'mbc-pretty-emails' ), 'id' => 'email_template_options_extra' );

			if ( $this->get_last_valid_order() ) :

			$settings[] = array( 'title' => __( 'Email Preview', 'mbc-pretty-emails' ), 'type' => 'title',
			 'desc' => sprintf(__( 

			 			'<a href="%s" target="_blank">'.__( 'Cancelled order', 'woocommerce' ).'</a> | '.
					 	'<a href="%s" target="_blank">'.__( 'New order', 'woocommerce' ).'</a> | '.
					 	'<a href="%s" target="_blank">'.__( 'Order on-hold', 'woocommerce' ).'</a> | '.
					 	'<a href="%s" target="_blank">'.__( 'Processing order', 'woocommerce' ).'</a> | '.
					 	'<a href="%s" target="_blank">'.__( 'Completed order', 'woocommerce' ).'</a> | '.
					 	'<a href="%s" target="_blank">'.__( 'Customer invoice', 'woocommerce' ).'</a> | '.
					 	'<a href="%s" target="_blank">'.__( 'Customer note', 'woocommerce' ).'</a> | '.
					 	'<a href="%s" target="_blank">'.__( 'New account', 'woocommerce' ).'</a> | '.
					 	'<a href="%s" target="_blank">'.__( 'Refunded order', 'woocommerce' ).'</a>'    
					 	),
			 			wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_Cancelled_Order'), 'pretty-preview-mail'),
					 	wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_New_Order'), 'pretty-preview-mail'),
					 	wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_Customer_On_Hold_Order'), 'pretty-preview-mail'),
					 	wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_Customer_Processing_Order'), 'pretty-preview-mail'),
					 	wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_Customer_Completed_Order'), 'pretty-preview-mail'),
					 	wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_Customer_Invoice'), 'pretty-preview-mail'),
					 	wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_Customer_Note'), 'pretty-preview-mail'),
					 	wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_Customer_New_Account'), 'pretty-preview-mail'),
					 	wp_nonce_url(admin_url('?pretty_email_admin_new_order=WC_Email_Customer_Refunded_Order'), 'pretty-preview-mail')

			 		), 
			 'id' => 'email_template_options_extra_preview' 
			 );

			else :

			$settings[] = array( 'title' => __( '! Email Preview is disabled !', 'mbc-pretty-emails' ), 'type' => 'title', 'desc' => __( 'You need to have at least one completed order to enable the preview mode.', 'mbc-pretty-emails' ), 'id' => 'email_template_options_extra' );


			endif;

			
			$settings[] = array(
				'title'    => __( 'Header Image Link', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter here an url to make your banner clickable.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_banner_link',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);


			$settings[] = array(
				'title'    => __( 'Company Logo', 'mbc-pretty-emails' ),
				'desc'     => sprintf(__( 'Enter a URL to display your logo. Upload your image using the <a href="%s">media uploader</a>.', 'mbc-pretty-emails' ), admin_url('media-new.php')),
				'id'       => 'woocommerce_email_mbc_logo',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Logo Link', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter here an url to make your logo clickable.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_logo_link',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);


			$settings[] = 	array(
				'title'    => __( 'Footer Logo width', 'mbc-pretty-emails' ),
				'desc'     => __( 'The footer logo width. Default <code>175</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_template_logo_width',
				'type'     => 'number',
				'custom_attributes' => array(
						'min'  => 0,
						'step' => 5
				),
				'css'      => 'width:6em;',
				'default'  => '175',
				'desc_tip' => __( 'Set this value to 0 if you want to remove the logo from the footer', 'mbc-pretty-emails' ),
				'autoload' => true
			);

			$settings[] = 	array(
				'title'    => __( 'Email Template Width', 'mbc-pretty-emails' ),
				'desc'     => __( 'The email template width. Default <code>700</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_template_width',
				'type'     => 'number',
				'custom_attributes' => array(
						'min'  => 320,
						'step' => 10
				),
				'css'      => 'width:6em;',
				'default'  => '700',
				'autoload' => true
			);

			$settings[] = array(
				'title'	   => __('Logo display template', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_header_theme',
				'desc'     => __( 'Header template (default) or centered logo (with menu above)', 'mbc-pretty-emails' ),
				'type'     => 'select',
				'default'  => 'default',
				'options'  => array(
					
					'default'=>__('Default','mbc-pretty-emails'),
					'centered'=>__('Centered Logo','mbc-pretty-emails')
				),

				'desc_tip' => __( 'This setting only apply on every emails', 'mbc-pretty-emails' ),
			);


			$settings[] = 	array(
				
				'title'    => __( 'Body font size', 'mbc-pretty-emails' ),
				'desc'     => __( 'Body font size in pixels. Default <code>12</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_bodyfontsize',
				'type'     => 'number',
				'custom_attributes' => array(
						'min'  => 9,
						'step' => 1
				),
				'css'      => 'width:6em;',
				'default'  => '12',
				'autoload' => true
			);

			$settings[] = array(

					'title'    => __( 'Body font family', 'mbc-pretty-emails' ),
					'id'       => 'woocommerce_email_mbc_body_font',
					'type'     => 'select',
					'default'  => "'Helvetica',Arial,sans-serif",
					'options'  => array(
						
						"'Arial', Arial, Sans-Serif"=> 'Arial',
						"'Arial Black', Arial, Sans-Serif"=>'Arial Black',
						"'Helvetica', Helvetica, Arial, Sans-Serif"=>'Helvetica',
						"'Impact', Helvetica, Arial, Sans-Serif"=>'Impact',
						"'Verdana', Helvetica, Arial, Sans-Serif"=>'Verdana', 
						"'Lucida Grande', Helvetica, Arial, Sans-Serif"=>'Lucida Grande',
						"'Courier New', Georgia, serif"=>'Courier New', 
						"'Georgia', Georgia, serif"=>'Georgia', 
						"'Palatino', Georgia, serif"=>'Palatino', 
					
					),

					'class'    => 'chosen_select_nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => __( 'Font family that will be used everywhere but heading tags', 'mbc-pretty-emails' ),
			);


			$settings[] = 	array(
				
				'title'    => __( 'Heading 1 font size', 'mbc-pretty-emails' ),
				'desc'     => __( 'H1 tag font size in pixels. Default <code>18</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_h1size',
				'type'     => 'number',
				'custom_attributes' => array(
						'min'  => 9,
						'step' => 1
				),
				'css'      => 'width:6em;',
				'default'  => '18',
				'autoload' => true
			);


			$settings[] = 	array(
				
				'title'    => __( 'Heading 1 font color', 'mbc-pretty-emails' ),
				'desc'     => __( 'Heading 1 font color. Default <code>#FFFFFF</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_h1color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#FFFFFF',
				'autoload' => true
			);


			$settings[] = 	array(
				
				'title'    => __( 'Heading 2 font size', 'mbc-pretty-emails' ),
				'desc'     => __( 'H2 tag font size in pixels. Default <code>18</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_h2size',
				'type'     => 'number',
				'custom_attributes' => array(
						'min'  => 9,
						'step' => 1
				),
				'css'      => 'width:6em;',
				'default'  => '16',
				'autoload' => true
			);

			$settings[] = 	array(
				
				'title'    => __( 'Heading 2 font color', 'mbc-pretty-emails' ),
				'desc'     => __( 'Heading 2 font color. Default <code>#505050</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_h2color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#505050',
				'autoload' => true
			);

			$settings[] = 	array(
				
				'title'    => __( 'Heading 3 font size', 'mbc-pretty-emails' ),
				'desc'     => __( 'H3 tag font size in pixels. Default <code>18</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_h3size',
				'type'     => 'number',
				'custom_attributes' => array(
						'min'  => 9,
						'step' => 1
				),
				'css'      => 'width:6em;',
				'default'  => '14',
				'autoload' => true
			);


			$settings[] = 	array(
				
				'title'    => __( 'Heading 3 font color', 'mbc-pretty-emails' ),
				'desc'     => __( 'Heading 3 font color. Default <code>#505050</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_h3color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#505050',
				'autoload' => true
			);

			$settings[] = array(
				
					'title'    => __( 'Headings font family', 'mbc-pretty-emails' ),
					'id'       => 'woocommerce_email_mbc_heading_font',
					'type'     => 'select',
					'default'  => "'Helvetica',Arial,sans-serif",
					'options'  => array(
						
						"'Arial', Arial, Sans-Serif"=> 'Arial',
						"'Arial Black', Arial, Sans-Serif"=>'Arial Black',
						"'Helvetica', Helvetica, Arial, Sans-Serif"=>'Helvetica',
						"'Impact', Helvetica, Arial, Sans-Serif"=>'Impact',
						"'Verdana', Helvetica, Arial, Sans-Serif"=>'Verdana', 
						"'Lucida Grande', Helvetica, Arial, Sans-Serif"=>'Lucida Grande',
						"'Courier New', Georgia, serif"=>'Courier New', 
						"'Georgia', Georgia, serif"=>'Georgia', 
						"'Palatino', Georgia, serif"=>'Palatino', 

					),

					'class'    => 'chosen_select_nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => __( 'Font family that will be used in every heading tags', 'mbc-pretty-emails' ),
			);

			$settings[] = 	array(
				
				'title'    => __( 'Download link font size', 'mbc-pretty-emails' ),
				'desc'     => __( 'Download link font size in pixels. Default <code>12</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_dlsize',
				'type'     => 'number',
				'custom_attributes' => array(
						'min'  => 9,
						'step' => 1
				),
				'css'      => 'width:6em;',
				'default'  => '12',
				'autoload' => true
			);

			$settings[] = 	array(
				
				'title'    => __( 'Download link font color', 'mbc-pretty-emails' ),
				'desc'     => __( 'Download link font color. Default <code>#000</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_dlcolor',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#000000',
				'autoload' => true
			);

		

			$settings[] = 	array(
				
				'title'    => __( 'Main border color', 'mbc-pretty-emails' ),
				'desc'     => __( 'Surrounding the main table. Default <code>#EEE</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_mbordercolor',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#eeeeee',
				'autoload' => true
			);


			$settings[] = 	array(
				
				'title'    => __( 'Order table border color', 'mbc-pretty-emails' ),
				'desc'     => __( 'Surrounding order tables. Default <code>#EEE</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_bordercolor',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#eeeeee',
				'autoload' => true
			);


			$settings[] = array(
				'title'   => __( 'Product Thumbnails', 'mbc-pretty-emails' ),
				'desc'    => __( 'Display product thumbnails in email', 'mbc-pretty-emails' ),
				'id'      => 'woocommerce_email_mbc_displayimage',
				'type'    => 'checkbox',
				'default' => 'yes',
			);


			$settings[] = 	array(
				'title'    => __( 'Product Thumbnails Size', 'mbc-pretty-emails' ),
				'desc'     => __( 'Product Thumbnails Size. Default <code>32</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_displayimage_size',
				'type'     => 'number',
				'custom_attributes' => array(
						'min'  => 32,
						'step' => 1
				),
				'css'      => 'width:6em;',
				'default'  => '32',
				'autoload' => true
			);
			
			
			$settings[] = array(
				'title'   => __( 'Product SKU', 'mbc-pretty-emails' ),
				'desc'    => __( 'Display sku in email', 'mbc-pretty-emails' ),
				'id'      => 'woocommerce_email_mbc_displaysku',
				'type'    => 'checkbox',
				'default' => 'yes',
			);



			$settings[] = array(
					'title'    => __( 'Extra link one', 'mbc-pretty-emails' ),
					'id'       => 'woocommerce_email_mbc_extra_link_one',
					'type'     => 'single_select_page',
					'default'  => '',
					'class'    => 'chosen_select_nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => __( 'Display a link in your email header.', 'mbc-pretty-emails' ),
			);

			$settings[] = array(
					'title'    => __( 'Extra link two', 'mbc-pretty-emails' ),
					'id'       => 'woocommerce_email_mbc_extra_link_two',
					'type'     => 'single_select_page',
					'default'  => '',
					'class'    => 'chosen_select_nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => __( 'Display a link in your email header.', 'mbc-pretty-emails' ),
			);

			$settings[] = array(
					'title'    => __( 'Extra link three', 'mbc-pretty-emails' ),
					'id'       => 'woocommerce_email_mbc_extra_link_three',
					'type'     => 'single_select_page',
					'default'  => '',
					'class'    => 'chosen_select_nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => __( 'Display a link in your email header.', 'mbc-pretty-emails' ),
			);

			$settings[] = 	array(
				
				'title'    => __( 'Extra link colors', 'mbc-pretty-emails' ),
				'desc'     => __( 'I you add nav menu in your header (extra links), then you can set a color. Default <code>#0000EE</code>.', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_extra_link_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#0000EE',
				'autoload' => true
			);


			$settings[] = array(
				'title'    => __( 'Facebook page URL', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Facebook page url to display your Facebook logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_facebook',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Facebook custom logo', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Facebook custom image url to display your own Facebook logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_facebook_img',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);


			$settings[] = array(
				'title'    => __( 'Twitter Profil URL', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Twitter profile page url to display your Twitter logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_twitter',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Twitter custom logo', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Twitter custom image url to display your own Twitter logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_twitter_img',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Instagram profile URL', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Instagram profile page url to display Instagram logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_instagram',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Instagram custom logo', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Instagram custom image url to display your own Instagram logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_instagram_img',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Pinterest profile URL', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Pinterest profile page url to display Pinterest logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_pinterest',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Pinterest custom logo', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Pinterest custom image url to display your own Pinterest logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_pinterest_img',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Google+ profile URL', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Google+ profile page url to display Google+ logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_google',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				'title'    => __( 'Google + custom logo', 'mbc-pretty-emails' ),
				'desc'     => __( 'Enter your Google + custom image url to display your own Google + logo in footer', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_google_img',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false
			);


			$settings[] = array(
				'title'    => __( 'Specific settings for the "customer processing order" email', 'mbc-pretty-emails' ),
				'desc'     => __( 'The text to appear in the introduction for the customer processing order email', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_cpo_intro',
				'css'      => 'width:100%; height: 75px;',
				'type'     => 'textarea',
				'default'  => __( "Your order has been received and is now being processed. Your order details are shown below for your reference:", 'woocommerce' ),
				'autoload' => true
			);

			$settings[] = array(
				'title'    => '',
				'desc'     => sprintf(__( 'Enter an image URL to display a banner on the processing order email. Upload your image using the <a href="%s">media uploader</a>.', 'mbc-pretty-emails' ), admin_url('media-new.php')),
				'id'       => 'woocommerce_email_mbc_cpo_intro_banner',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'desc_tip' => __( 'This setting only apply on the "Customer Processing Order email"', 'mbc-pretty-emails' ),
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				
					'id'       => 'woocommerce_email_mbc_cpo_intro_banner_position',
					'desc'     => __( 'Display the banner before or after the order table', 'mbc-pretty-emails' ),
					'type'     => 'select',
					'default'  => 'before',
					'options'  => array(
						
						'after'=>__('After order table','mbc-pretty-emails'),
						'before'=>__('Before order table','mbc-pretty-emails')
					),

					'desc_tip' => __( 'This setting only apply on the "Customer Processing Order email"', 'mbc-pretty-emails' ),
			);


			$settings[] = array(

				'desc'     => __( 'Enter an url to make that banner clickable', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_cpo_intro_banner_link',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false,
				'desc_tip' => __( 'This setting only apply on the "Customer Processing Order email"', 'mbc-pretty-emails' ),
			);


			$settings[] = array(

				'desc'     => __( 'Add an attachment (absolute url of a WordPress uploads file ), i.e. https://www.yoursite.com/wp-content/uploads/terms-and-conditions.pdf', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_cpo_attachment',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false,
				'desc_tip' => __( 'This setting only apply on the "Customer Processing Order email"', 'mbc-pretty-emails' ),
			
			);


			$settings[] = array(
				'title'    => __( 'Specific settings for the "customer completed order" email', 'mbc-pretty-emails' ),
				'desc'     => __( 'The text to appear in the introduction for the customer completed order email', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_cco_intro',
				'css'      => 'width:100%; height: 75px;',
				'type'     => 'textarea',
				'default'  => sprintf( __( "Hi there. Your recent order on %s has been completed. Your order details are shown below for your reference:", 'woocommerce' ), get_option( 'blogname' ) ),
				'autoload' => true
			);

			$settings[] = array(
				'title'    => '',
				'desc'     => sprintf(__( 'Enter an image URL to display a banner on the processing order email. Upload your image using the <a href="%s">media uploader</a>.', 'mbc-pretty-emails' ), admin_url('media-new.php')),
				'id'       => 'woocommerce_email_mbc_cco_intro_banner',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'desc_tip' => __( 'This setting only apply on the "Customer Completed Order email"', 'mbc-pretty-emails' ),
				'default'  => '',
				'autoload' => false
			);

			$settings[] = array(
				
					'id'       => 'woocommerce_email_mbc_cco_intro_banner_position',
					'desc'     => __( 'Display the banner before or after the order table', 'mbc-pretty-emails' ),
					'type'     => 'select',
					'default'  => 'before',
					'options'  => array(
						
						'after'=>__('After order table','mbc-pretty-emails'),
						'before'=>__('Before order table','mbc-pretty-emails')
					),

					'desc_tip' => __( 'This setting only apply on the "Customer Completed Order email"', 'mbc-pretty-emails' ),
			);


			$settings[] = array(

				'desc'     => __( 'Enter an url to make that banner clickable', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_cco_intro_banner_link',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false,
				'desc_tip' => __( 'This setting only apply on the "Customer Completed Order email"', 'mbc-pretty-emails' ),
			);

			$settings[] = array(

				'desc'     => __( 'Add an attachment (absolute url of a WordPress uploads file ), i.e. https://www.yoursite.com/uploads/terms-and-conditions.pdf', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_cco_attachment',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'autoload' => false,
				'desc_tip' => __( 'This setting only apply on the "Customer Completed Order email"', 'mbc-pretty-emails' ),
			
			);
			
			$settings[] = array(
				'title'    => __( 'Introduction for customer new account email', 'mbc-pretty-emails' ),
				'desc'     => __( 'The text to appear in the introduction for the customer new account email', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_cna_intro',
				'css'      => 'width:100%; height: 75px;',
				'type'     => 'textarea',
				'autoload' => true
			);

			$settings[] = array(
				'title'    => __( 'Introduction for customer on-hold order email', 'mbc-pretty-emails' ),
				'desc'     => __( 'The text to appear in the introduction for the customer on-hold order email', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_coh_intro',
				'css'      => 'width:100%; height: 75px;',
				'type'     => 'textarea',
				'autoload' => true
			);

			$settings[] = array(
				'title'    => __( 'Introduction for customer note email', 'mbc-pretty-emails' ),
				'desc'     => __( 'The text to appear in the introduction for the customer note email', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_cn_intro',
				'css'      => 'width:100%; height: 75px;',
				'type'     => 'textarea',
				'autoload' => true,
				'desc_tip' => __( 'Do not forget to include {{customer_note}} in your text where you want to display the actual note', 'mbc-pretty-emails' ),
			);


			$settings[] = array(
				'title'    => __( 'Introduction for customer invoice email', 'mbc-pretty-emails' ),
				'desc'     => __( 'The text to appear in the introduction for the customer invoice email', 'mbc-pretty-emails' ),
				'id'       => 'woocommerce_email_mbc_ci_intro',
				'css'      => 'width:100%; height: 75px;',
				'type'     => 'textarea',
				'autoload' => true,
				'desc_tip' => __( 'Do not forget to include {{customer_note}} in your text where you want to display the actual note', 'mbc-pretty-emails' ),
			);

			$settings[] = array(
				'title'   => __( 'Override new user email notification', 'mbc-pretty-emails' ),
				'desc'    => __( 'New user notication email will be styled using Pretty email template', 'mbc-pretty-emails' ),
				'id'      => 'woocommerce_email_mbc_wp_new_user_notification',
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array( 'type' => 'sectionend', 'id' => 'email_template_options_extra');

		
			return $settings;

		}

		public function install(){

			// Ne pas installer le plugin si WooCommerce n'est pas installer.

			if( !class_exists( 'Woocommerce' ) )
			wp_die('WooCommerce extension not found !');

		}


		public function custom_template($located, $template_name, $args, $template_path, $default_path){

			if( in_array( $template_name, apply_filters('woocommerce_pretty_emails_templates_array', $this->_templates ) ) ):

				if( strrpos($template_name, 'customer-processing-order') !== false )
				$this->_context = 'cpo';
				
				if( strrpos($template_name, 'customer-completed-order') !== false )
				$this->_context = 'cco';

				do_action('woocommerce_pretty_before_template');

				if( file_exists( get_stylesheet_directory().'/woocommerce-pretty-emails/'.$template_name ) )
						return get_stylesheet_directory().'/woocommerce-pretty-emails/'.$template_name;

				if( $template_name === 'emails/email-header.php' && get_option('woocommerce_email_mbc_header_theme','default') === 'centered'  )
						return plugin_dir_path(__FILE__).'/emails/themes/email-header-centered.php';

				return plugin_dir_path( __FILE__ ).$template_name;

			endif;

			return $located;
				
		}


		/**
		 * Preview email template
		 * @since 1.4
		 * @return string
		 */

		public function preview_emails() {

			if ( isset( $_GET['pretty_email_admin_new_order'] ) ) {
				
				$_preview_order_id = $this->get_last_valid_order();
				
				if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'pretty-preview-mail') ) {
					die( 'Security check' );
				}

				if( $_preview_order_id ) :

				switch ($_GET['pretty_email_admin_new_order']) {

					case 'WC_Email_Cancelled_Order':

						$email = WC()->mailer();
						$email->emails['WC_Email_Cancelled_Order']->object = wc_get_order( $_preview_order_id );
						echo $email->emails['WC_Email_Cancelled_Order']->get_content_html();

						break;

					case 'WC_Email_New_Order':

						$email = WC()->mailer();
						$email->emails['WC_Email_New_Order']->object = wc_get_order( $_preview_order_id );
						echo $email->emails['WC_Email_New_Order']->get_content_html();

						break;

					case 'WC_Email_Customer_On_Hold_Order':

						$email = WC()->mailer();
						$email->emails['WC_Email_Customer_On_Hold_Order']->object = wc_get_order( $_preview_order_id );
						echo $email->emails['WC_Email_Customer_On_Hold_Order']->get_content_html();

						break;
					
					
					case 'WC_Email_Customer_Processing_Order':
						
						$email = WC()->mailer();
						$email->emails['WC_Email_Customer_Processing_Order']->object = wc_get_order( $_preview_order_id );
						echo $email->emails['WC_Email_Customer_Processing_Order']->get_content_html();
						
						break;

						
					case 'WC_Email_Customer_Completed_Order':
						
						$email = WC()->mailer();
						$email->emails['WC_Email_Customer_Completed_Order']->object = wc_get_order( $_preview_order_id );
						echo $email->emails['WC_Email_Customer_Completed_Order']->get_content_html();
						
						break;

					case 'WC_Email_Customer_Invoice':

						$email = WC()->mailer();
						$email->emails['WC_Email_Customer_Invoice']->object = wc_get_order( $_preview_order_id );
						echo $email->emails['WC_Email_Customer_Invoice']->get_content_html();
						
						break;

					case 'WC_Email_Customer_Note':

						$email = WC()->mailer();
						$email->emails['WC_Email_Customer_Note']->object = wc_get_order( $_preview_order_id );
						$email->emails['WC_Email_Customer_Note']->customer_note = 'Lorem Ipsum';
						echo $email->emails['WC_Email_Customer_Note']->get_content_html();
						
					break;

					case 'WC_Email_Customer_New_Account':

						$email = WC()->mailer();
						$email->emails['WC_Email_Customer_New_Account']->object = wc_get_order( $_preview_order_id );
						$email->emails['WC_Email_Customer_New_Account']->user_login = 'Login';
						$email->emails['WC_Email_Customer_New_Account']->user_pass = 'Pass';
						$email->emails['WC_Email_Customer_New_Account']->password_generated = '******';
						echo $email->emails['WC_Email_Customer_New_Account']->get_content_html();
						
					break;

					case 'WC_Email_Customer_Refunded_Order':

						$email = WC()->mailer();
						$email->emails['WC_Email_Customer_Refunded_Order']->object = wc_get_order( $_preview_order_id );
						$email->emails['WC_Email_Customer_Refunded_Order']->refund = false;
						$email->emails['WC_Email_Customer_Refunded_Order']->partial_refund = false;

						echo $email->emails['WC_Email_Customer_Refunded_Order']->get_content_html();

					break;
						
				}

				endif;
			
				exit;
			}
		}

		public function get_last_valid_order(){

			$args = array(
				'posts_per_page'   => 1,
				'offset'           => 0,
				'orderby'          => 'post_date',
				'order'            => 'DESC',
				'post_type'        => 'shop_order',
				'post_status'      => 'wc-completed',
				'suppress_filters' => true );

			$orders = get_posts( $args );
			if( !empty($orders) )
				return  $orders[0]->ID;

			return false;
			
		}

		public function display_banners(){

				
			if( $this->_context ) :

				$position = get_option('woocommerce_email_mbc_'.$this->_context.'_intro_banner_position', 'before' );
				add_action( 'woocommerce_email_'.$position.'_order_table', array( $this, 'get_banner'), 999 );

			endif;



		}


		public function add_attachment(){

				
			if( $this->_context ) :

				$attachement = get_option('woocommerce_email_mbc_'.$this->_context.'_attachment' );
				
				if( strpos( $attachement, 'wp-content/uploads') !== false ){

					$path = explode('wp-content/', $attachement);
					$path = end($path);
					$path = WP_CONTENT_DIR.'/'.$path;

					if( file_exists( $path ) ) {

						$this->_path = $path;
						add_filter('woocommerce_email_attachments', array( $this, 'attach'), 99 );

					}

				}

			endif;


		}

		public function attach(){

			return $this->_path;

		}


		


		public function get_banner(){

			 $banner = '';
					
			 if ( $img = get_option( 'woocommerce_email_mbc_'.$this->_context.'_intro_banner' ) ) {
         					
         					$banner.= '<p style="text-align:center;margin:0;padding:0;">';
         					if ( $linkb = get_option( 'woocommerce_email_mbc_'.$this->_context.'_intro_banner_link' ) )
         					$banner.= '<a href="'.esc_url($linkb).'">';
            				$banner.= '<img border="0" src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name' ) . '"  align="top" style="width:100%; height:auto;" />';
          					if ( $linkb = get_option( 'woocommerce_email_mbc_'.$this->_context.'_intro_banner_link' ) )
         					$banner.= '</a>';
          					$banner.= '</p>';
             }

             echo apply_filters( 'woocommerce_email_mbc_'.$this->_context.'_intro_banner_html' , $banner );

		}



	}

	if ( !function_exists('wp_new_user_notification') && 'yes' === get_option( 'woocommerce_email_mbc_wp_new_user_notification' ) ) :

	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
		if ( $deprecated !== null ) {
			_deprecated_argument( __FUNCTION__, '4.3.1' );
		}

		global $wpdb, $wp_hasher;
		$user = get_userdata( $user_id );

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";

		@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

		// `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notifcation.
		if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
			return;
		}

		// Generate something random for a password reset key.
		$key = wp_generate_password( 20, false );

		/** This action is documented in wp-login.php */
		do_action( 'retrieve_password_key', $user->user_login, $key );

		// Now insert the key, hashed, into the DB.
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

		$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
		$message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . "\r\n\r\n";

		$message .= wp_login_url() . "\r\n";
			
		$mailer = WC()->mailer();
		$mailer->send( $user->user_email, sprintf(__('[%s] Your username and password info'), $blogname) , $mailer->wrap_message( sprintf(__('[%s] Your username and password info'), $blogname), $message ), '', '' );
	
	}

	endif;
	
	
	$load = new WooCommerce_Pretty_Emails();

}