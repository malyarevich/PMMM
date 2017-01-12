<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Multistep_Checkout {

	/**
	 * Single instance of WooCommerce Germanized Main Class
	 *
	 * @var object
	 */
	protected static $_instance = null;

	public $steps = array();

	public $compatibility = array();

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		if ( is_admin() ) 
			$this->admin_hooks();

		if ( ! $this->is_enabled() )
			return;

		add_action( 'wp_loaded', array( $this, 'init' ) );
		add_action( 'woocommerce_loaded', array( $this, 'template_hooks' ), 6 );

	}

	public function init() {

		$this->init_steps();

		$this->compatibility = array(
			'paypal-for-woocommerce/paypal-for-woocommerce.php' => 'PayPal_For_WooCommerce',
			'woocommerce-gateway-amazon-payments-advanced/amazon-payments-advanced.php' => 'Amazon_Payments_Advanced',
			'woocommerce-gateway-amazon-payments-advanced/woocommerce-gateway-amazon-payments-advanced.php' => 'Amazon_Payments_Advanced',
			'woocommerce-gateway-stripe/woocommerce-gateway-stripe.php' => 'Stripe',
		);

		$this->plugin_compatibility();

		add_action( 'get_header', array( $this, 'refresh_step_numbers' ) );

		add_action( 'woocommerce_before_checkout_form', array( $this, 'print_steps' ), 0, 1 );
		add_action( 'woocommerce_checkout_process', array( $this, 'check_step_submit' ), 0 );
		
		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'refresh_order_fragments' ), 1 );
		
		// Load checkout assets
		add_action( 'woocommerce_gzdp_frontend_styles', array( $this, 'set_styles' ) );
		add_action( 'woocommerce_gzdp_frontend_scripts', array( $this, 'set_scripts' ) );
		add_filter( 'body_class', array( $this, 'set_body_class' ), 0 );
		
		// Override form-checkout.php template if necessary
		add_filter( 'woocommerce_gzdp_filter_template', array( $this, 'stop_checkout_theme_override' ), 0, 3 );
		
	}

	public function plugin_compatibility() {

		foreach ( $this->compatibility as $plugin => $suffix ) {

			if ( WC_GZDP_Dependencies::instance()->is_plugin_activated( $plugin ) ) {
				$classname = 'WC_GZDP_Checkout_Compatibility_' . $suffix;
				$this->compatibility[ $plugin ] = new $classname();
			}			

		}
	}

	public function is_enabled() {
		return ( get_option( 'woocommerce_gzdp_checkout_enable' ) == 'yes' ? true : false );
	}

	public function set_body_class( $classes ) {

		if ( is_checkout() )
			$classes[] = 'woocommerce-multistep-checkout';

		return $classes;

	}

	public function set_styles( $assets ) {

		if ( ! is_checkout() )
			return;

		wp_register_style( 'wc-gzdp-checkout', WC_germanized_pro()->plugin_url() . '/assets/css/checkout-multistep' . $assets->suffix . '.css', array(), WC_GERMANIZED_PRO_VERSION );
		wp_enqueue_style( 'wc-gzdp-checkout' );

		wp_add_inline_style( 'wc-gzdp-checkout', $this->get_custom_css() );
	
	}

	public function get_custom_css() {

		$colors = apply_filters( 'woocommerce_gzdp_checkout_step_colors', array(
			
			'bg'				=> get_option( 'woocommerce_gzdp_checkout_bg', '#f9f9f9' ),
			'border'			=> get_option( 'woocommerce_gzdp_checkout_border_color', '#d4d4d4' ),
			'color'				=> get_option( 'woocommerce_gzdp_checkout_font_color', '#468847' ),
			'active_bg' 		=> get_option( 'woocommerce_gzdp_checkout_active_bg', '#d9edf7' ),
			'active_color' 		=> get_option( 'woocommerce_gzdp_checkout_active_font_color', '#3a87ad' ),
			'disabled_bg'		=> get_option( 'woocommerce_gzdp_checkout_disabled_bg', '#ededed' ),
			'disabled_color'	=> get_option( 'woocommerce_gzdp_checkout_disabled_font_color', '#999999' ),

		) );

		$css = '

		.woocommerce-multistep-checkout ul.nav-wizard {
		    background-color: ' . $colors[ 'bg' ] . ';
		    border-color: ' . $colors[ 'border' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard:before {
		    border-top-color: ' . $colors[ 'border' ] . ';
		    border-bottom-color: ' . $colors[ 'border' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard:after {
		    border-top-color: ' . $colors[ 'border' ] . ';
		    border-bottom-color: ' . $colors[ 'border' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard li a {
		    color: ' . $colors[ 'color' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard li:before {
		    border-left-color: ' . $colors[ 'border' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard li:after {
		    border-left-color: ' . $colors[ 'bg' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard li.active {
		    color: ' . $colors[ 'active_color' ] . ';
		    background: ' . $colors[ 'active_bg' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard li.active:after {
		    border-left-color: ' . $colors[ 'active_bg' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard li.active a,
		.woocommerce-multistep-checkout ul.nav-wizard li.active a:active,
		.woocommerce-multistep-checkout ul.nav-wizard li.active a:visited,
		.woocommerce-multistep-checkout ul.nav-wizard li.active a:focus {
		    color: ' . $colors[ 'active_color' ] . ';
		    background: ' . $colors[ 'active_bg' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard .active ~ li {
		    color: ' . $colors[ 'disabled_color' ] . ';
		    background: ' . $colors[ 'disabled_bg' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard .active ~ li:after {
		    border-left-color: ' . $colors[ 'disabled_bg' ] . ';
		}

		.woocommerce-multistep-checkout ul.nav-wizard .active ~ li a,
		.woocommerce-multistep-checkout ul.nav-wizard .active ~ li a:active,
		.woocommerce-multistep-checkout ul.nav-wizard .active ~ li a:visited,
		.woocommerce-multistep-checkout ul.nav-wizard .active ~ li a:focus {
		    color: ' . $colors[ 'disabled_color' ] . ';
		    background: ' . $colors[ 'disabled_bg' ] . ';
		}

		';
		
		return apply_filters( 'woocommerce_gzdp_checkout_custom_css', $css ); 

	}

	public function set_scripts( $assets ) {
		
		if ( ! is_checkout() || is_wc_endpoint_url() )
			return;

		// Multistep Checkout
		wp_register_script( 'wc-gzdp-checkout-multistep', WC_germanized_pro()->plugin_url() . '/assets/js/checkout-multistep' . $assets->suffix . '.js', array( 'wc-checkout' ), WC_GERMANIZED_PRO_VERSION, true );
		
		wp_localize_script( 'wc-gzdp-checkout-multistep', 'steps', $this->get_step_data() );
		wp_localize_script( 'wc-gzdp-checkout-multistep', 'multistep', array( 'wrapper' => 'step-wrapper' ) );

		wp_enqueue_script( 'wc-gzdp-checkout-multistep' );

		// Payment method compatibility
		if ( WC_GZDP_Dependencies::instance()->is_plugin_activated( 'woocommerce_payonecw/woocommerce_payonecw.php' ) ) {
			wp_register_script( 'wc-gzdp-checkout-multistep-payone', WC_germanized_pro()->plugin_url() . '/assets/js/checkout-multistep-payone-helper' . $assets->suffix . '.js', array( 'wc-gzdp-checkout-multistep' ), WC_GERMANIZED_PRO_VERSION, true );
			wp_enqueue_script( 'wc-gzdp-checkout-multistep-payone' );
		}

		// Paymill Helper
		if ( WC_GZDP_Dependencies::instance()->is_plugin_activated( 'paymill/paymill.php' ) ) {
			
			// Change submit id
			wp_register_script( 'wc-gzdp-paymill-multistep-helper', WC_germanized_pro()->plugin_url() . '/assets/js/checkout-multistep-paymill-helper.js', array( 'wc-gzdp-checkout-multistep' ), WC_GERMANIZED_PRO_VERSION, true );
			wp_enqueue_script( 'wc-gzdp-paymill-multistep-helper' );

			// Handle payment step
			wp_register_script( 'wc-gzdp-paymill-multistep-helper-submit', WC_germanized_pro()->plugin_url() . '/assets/js/checkout-multistep-paymill-submit-helper.js', array( 'paymill_bridge_custom' ), WC_GERMANIZED_PRO_VERSION, true );
			wp_enqueue_script( 'wc-gzdp-paymill-multistep-helper-submit' );

		}

		do_action( 'woocommerce_gzdp_checkout_scripts', $this, $assets ); 

	}

	public function stop_checkout_theme_override( $template, $template_name, $template_path ) {
		
		if ( $template_name == 'checkout/form-checkout.php' ) {

			// Maybe force template override
			if ( get_option( 'woocommerce_gzdp_checkout_force_template_override' ) == 'yes' )
				$template = WC()->plugin_path() . '/templates/' . $template_name;
			
			// Allow theme override by using woocommerce-germanized-pro/checkout/multistep/form-checkout.php template
			if ( $loc = locate_template( WC_germanized_pro()->template_path() . 'checkout/multistep/form-checkout.php' ) )
				$template = $loc;
			else
				$template = apply_filters( 'woocommerce_gzdp_checkout_template_not_found', $template, 'checkout/multistep/form-checkout.php', $template_path );

		}

		return $template;
	}

	public function refresh_order_fragments( $fragments ) {

		ob_start();
		$this->order_step_data();
		$data = ob_get_clean();

		// Unset woocommerce checkout payment refresh for step 2 (so that the data inserted by the user is not lost)
		if ( WC()->session->get( 'checkout_step' ) && 'payment' == WC()->session->get( 'checkout_step' ) ) {
			unset( $fragments['.woocommerce-checkout-payment' ]);
			$fragments = array_filter( $fragments );
		}

		$fragments[ '.woocommerce-gzpd-checkout-verify-data' ] = $data;

		return $fragments;
	}

	public function template_hooks() {
		
		if ( get_option( 'woocommerce_gzdp_checkout_verify_data_output' ) == 'yes' )
			add_action( 'woocommerce_review_order_after_payment', array( $this, 'order_step_data' ), ( wc_gzd_get_hook_priority( 'checkout_legal' ) - 5 ) );
	
	}

	public function order_step_data() {
		
		if ( WC()->session->get( 'checkout_posted' ) )
			WC()->checkout->posted = WC()->session->get( 'checkout_posted' );
		
		wc_get_template( 'checkout/multistep/data.php', array( 'multistep' => $this, 'checkout' => WC()->checkout ) );
	}

	public function check_step_submit() {

		// Init payment methods so that their hooks are registered before checking
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		if ( isset( $_POST[ 'wc_gzdp_step_submit' ] ) && ! empty( $_POST[ 'wc_gzdp_step_submit' ] ) ) {

			$action = ( isset( $_POST[ 'wc_gzdp_step_refresh' ] ) ? 'refresh' : 'submit' );

			if ( $step = $this->get_step( sanitize_text_field( $_POST[ 'wc_gzdp_step_submit' ] ) ) )
				do_action( 'woocommerce_gzdp_checkout_step_' . $step->get_id() . '_' . $action );

		}

	}

	public function get_step_names() {
		return apply_filters( 'woocommerce_gzdp_checkout_steps', array(
			'address'	=> get_option( 'woocommerce_gzdp_checkout_step_title_address', _x( 'Personal Data', 'multistep', 'woocommerce-germanized-pro' ) ),
			'payment'	=> get_option( 'woocommerce_gzdp_checkout_step_title_payment', _x( 'Payment', 'multistep', 'woocommerce-germanized-pro' ) ),
			'order'		=> get_option( 'woocommerce_gzdp_checkout_step_title_order', _x( 'Order', 'multistep', 'woocommerce-germanized-pro' ) ),
		) );
	}

	public function init_steps() {

		$steps = $this->get_step_names();

		foreach ( $steps as $key => $step ) {

			$classname = 'WC_GZDP_Checkout_Step_' . ucfirst( $key );

			if ( class_exists( $classname ) )
				array_push( $this->steps, new $classname( $key, $step ) );

		}

		$count = 0;

		foreach( $this->steps as $key => $step ) {

			if ( isset( $this->steps[ $key - 1 ] ) )
				$this->steps[ $key ]->prev = $this->steps[ $key - 1 ];

			if ( isset( $this->steps[ $key + 1 ] ) )
				$this->steps[ $key ]->next = $this->steps[ $key + 1 ];

			$this->steps[ $key ]->number = ++$count;

		}

	}

	public function refresh_step_numbers() {

		if ( is_checkout() ) {
			// Init cart and calculate totals to see whether cart needs payment
			WC()->cart->init();
			WC()->cart->calculate_totals();
		}
		
		$count = 0;

		$steps = $this->steps;

		foreach ( $steps as $key => $step ) {

			$steps[ $key ]->next = null;
			$steps[ $key ]->prev = null;
			$steps[ $key ]->number = 0;

			if ( ! $step->is_activated() )
				unset( $steps[ $key ] );

		}

		$steps = array_values( $steps );

		foreach( $steps as $key => $step ) {

			if ( isset( $steps[ $key - 1 ] ) )
				$step->prev = $steps[ $key - 1 ];

			if ( isset( $steps[ $key + 1 ] ) )
				$step->next = $steps[ $key + 1 ];

			$step->number = ++$count;

			foreach ( $this->steps as $org_step_key => $org_step ) {

				if ( $org_step->id == $step->id ) {
					$this->steps[ $org_step_key ] = $step;
					break;
				}

			}

		}

	}

	public function get_step( $id ) {

		foreach ( $this->steps as $step ) {

			if ( $step->get_id() == $id )
				return $step;

		}

		return false;

	}

	public function get_step_data() {

		$return = array();

		foreach ( $this->steps as $step ) {

			$tmp = array(
				'id' 				=> $step->get_id(),
				'title' 			=> $step->get_title(),
				'number' 			=> $step->get_number(),
				'selector'			=> $step->get_selector(),
				'submit_html'		=> $step->get_template( 'submit' ),
				'wrapper_classes'	=> $step->get_wrapper_classes(),
				'hide'				=> false,
			);

			array_push( $return, $tmp );

		}

		return $return;

	}

	public function admin_hooks() {
		add_filter( 'woocommerce_gzd_settings_sections', array( $this, 'register_section' ), 4 );
		add_filter( 'woocommerce_gzd_get_settings_checkout', array( $this, 'get_settings' ) );
	}

	public function print_steps() {
		wc_get_template( 'checkout/multistep/steps.php', array( 'multistep' => $this ) );
	}

	public function register_section( $sections ) {
		$sections[ 'checkout' ] = _x( 'Multistep Checkout', 'multistep', 'woocommerce-germanized-pro' );
		return $sections;
	}

	public function get_settings() {
		
		$settings = array(

			array( 'title' => _x( 'General', 'multistep', 'woocommerce-germanized-pro' ), 'type' => 'title', 'id' => 'checkout_general_options' ),

			array(
				'title' 	=> _x( 'Enable', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Enable Multistep Checkout.', 'multistep', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_checkout_enable',
				'type' 		=> 'checkbox',
				'default'	=> 'no',
			),

			array(
				'title' 	=> _x( 'Address step title', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a title for the first step (addresses).', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_step_title_address',
				'type' 		=> 'text',
				'default'	=> _x( 'Personal Data', 'multistep', 'woocommerce-germanized-pro' ),
			),

			array(
				'title' 	=> _x( 'Payment step title', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a title for the second step (payment).', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_step_title_payment',
				'type' 		=> 'text',
				'default'	=> _x( 'Payment', 'multistep', 'woocommerce-germanized-pro' ),
			),

			array(
				'title' 	=> _x( 'Verify step title', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a title for the second step (order verify).', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_step_title_order',
				'type' 		=> 'text',
				'default'	=> _x( 'Order', 'multistep', 'woocommerce-germanized-pro' ),
			),

			array(
				'title' 	=> _x( 'Payment Validation', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Enable field validation for payment step.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> _x( 'This option will enable field validation before continuing to step 3. Some Payment Plugins may require disabling this option.', 'multistep', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_checkout_payment_validation',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> _x( 'Force Template override', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Force to override your theme\'s form-checkout.php.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> sprintf( _x( 'Enable this mode if you are facing serious layout issues within checkout. To enable a custom multistep template add a template to your theme folder [%s]', 'multistep', 'woocommerce-germanized-pro' ), 'woocommerce-germanized-pro/checkout/multistep/form-checkout.php' ),
				'id' 		=> 'woocommerce_gzdp_checkout_force_template_override',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> _x( 'Data review', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Enable data review within the last step.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> _x( 'This option will add billing + shipping address as well as payment method to the last step right before order table to let customers review their data.', 'multistep', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_checkout_verify_data_output',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array( 'type' => 'sectionend', 'id' => 'checkout_general_options' ),

			array( 'title' => _x( 'Colors', 'multistep', 'woocommerce-germanized-pro' ), 'type' => 'title', 'id' => 'checkout_color_options' ),

			array(
				'title' 	=> _x( 'Background Color', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a background color for the step panel.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_bg',
				'type' 		=> 'color',
				'default'	=> '#f9f9f9',
			),

			array(
				'title' 	=> _x( 'Border Color', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a border color for the step panel.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_border_color',
				'type' 		=> 'color',
				'default'	=> '#d4d4d4',
			),

			array(
				'title' 	=> _x( 'Font Color', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a font color for the step panel.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_font_color',
				'type' 		=> 'color',
				'default'	=> '#468847',
			),

			array(
				'title' 	=> _x( 'Active Background Color', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a background color for the current step.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_active_bg',
				'type' 		=> 'color',
				'default'	=> '#d9edf7',
			),

			array(
				'title' 	=> _x( 'Active Font Color', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a font color for the current step.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_active_font_color',
				'type' 		=> 'color',
				'default'	=> '#3a87ad',
			),

			array(
				'title' 	=> _x( 'Disabled Background Color', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a background color for a disabled step.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_disabled_bg',
				'type' 		=> 'color',
				'default'	=> '#ededed',
			),

			array(
				'title' 	=> _x( 'Disabled Font Color', 'multistep', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a font color for a disabled step.', 'multistep', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_checkout_disabled_font_color',
				'type' 		=> 'color',
				'default'	=> '#999999',
			),

			array( 'type' => 'sectionend', 'id' => 'checkout_color_options' ),

		);

		return $settings;

	}

	public function get_formatted_billing_address() {
		// Formatted Addresses
		$address = apply_filters( 'woocommerce_gzdp_checkout_formatted_billing_address', array(
			'first_name' 	=> WC()->checkout->get_posted_address_data( "first_name", "billing" ),
			'last_name' 	=> WC()->checkout->get_posted_address_data( "last_name", "billing" ),
			'company'       => WC()->checkout->get_posted_address_data( "company", "billing" ),
			'address_1'     => WC()->checkout->get_posted_address_data( "address_1", "billing" ),
			'address_2'     => WC()->checkout->get_posted_address_data( "address_2", "billing" ),
			'city'          => WC()->checkout->get_posted_address_data( "city", "billing" ),
			'state'         => WC()->checkout->get_posted_address_data( "state", "billing" ),
			'postcode'      => WC()->checkout->get_posted_address_data( "postcode", "billing" ),
			'country'       => WC()->checkout->get_posted_address_data( "country", "billing" ),
		), $this );

		return WC()->countries->get_formatted_address( $address );
	}

	public function get_formatted_shipping_address() {

		if ( WC()->checkout->posted[ 'ship_to_different_address' ] == false || ( ! WC()->checkout->get_posted_address_data( "address_1", "shipping" ) && ! WC()->checkout->get_posted_address_data( "address_2", "shipping" ) ) )
			return false;

		// Formatted Addresses
		$address = apply_filters( 'woocommerce_gzdp_checkout_formatted_shipping_address', array(
			'first_name' 	=> WC()->checkout->get_posted_address_data( "first_name", "shipping" ),
			'last_name' 	=> WC()->checkout->get_posted_address_data( "last_name", "shipping" ),
			'company'       => WC()->checkout->get_posted_address_data( "company", "shipping" ),
			'address_1'     => WC()->checkout->get_posted_address_data( "address_1", "shipping" ),
			'address_2'     => WC()->checkout->get_posted_address_data( "address_2", "shipping" ),
			'city'          => WC()->checkout->get_posted_address_data( "city", "shipping" ),
			'state'         => WC()->checkout->get_posted_address_data( "state", "shipping" ),
			'postcode'      => WC()->checkout->get_posted_address_data( "postcode", "shipping" ),
			'country'       => WC()->checkout->get_posted_address_data( "country", "shipping" )
		), $this );

		return WC()->countries->get_formatted_address( $address );
	}

}

return WC_GZDP_Multistep_Checkout::instance();