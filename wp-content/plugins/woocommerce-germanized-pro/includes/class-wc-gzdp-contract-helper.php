<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Contract_Helper {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'add_emails' ) );
		add_filter( 'woocommerce_create_order', array( $this, 'set_default_order_status' ) );
		// Add order confirmation meta
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'set_order_confirmation_needed' ), 0, 1 );
		// Hide Payment info
		add_action( 'woocommerce_before_template_part', array( $this, 'hide_payment_info' ), 0, 4 );
		// Add Payment link to thankyou page
		add_action( 'woocommerce_before_template_part', array( $this, 'add_payment_link' ), 1, 4 );
		// Remove Payment Method redirect on Checkout
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'remove_gateway_redirect' ) );
		// Manipulate Subject for confirmation and processing
		add_filter( 'woocommerce_email_subject_customer_order_confirmation', array( $this, 'set_order_confirmation_subject' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_email_subject_customer_processing_order', array( $this, 'set_processing_order_subject' ), PHP_INT_MAX, 2 );
		// Add payment button to order confirmation
		add_action( 'woocommerce_email_before_order_table', array( $this, 'set_order_confirmation_payment_link' ), 0, 1 );
		// Filter processing order string translation
		add_filter( 'gettext', array( $this, 'set_order_confirmation_text' ), 10, 3 );
		// Rename email template to enable germanized attachment support
		add_filter( 'woocommerce_germanized_email_template_name', array( $this, 'rename_email_template' ), 0, 2 );
		// Remove Germanized hooks
		add_action( 'woocommerce_germanized_init', array( $this, 'remove_gzd_hooks' ) );
		add_action( 'after_setup_theme', array( $this, 'remove_gzd_frontend_hooks' ), 0 );
		// Do not send order completed mail if order has not been confirmed
		add_action( 'woocommerce_order_status_completed', array( $this, 'remove_notifcations' ), 0, 1 );

		add_action( 'woocommerce_email_order_details', array( $this, 'remove_email_payment_instructions' ), 0, 4 );

		if ( is_admin() )
			$this->admin_hooks();
	}

	public function remove_email_payment_instructions( $order, $sent_to_admin, $plain_text, $email ) {
		if ( $email->id === 'customer_processing_order' && wc_gzdp_order_needs_confirmation( $order->id ) ) {
			remove_all_actions( 'woocommerce_email_before_order_table' );
		}
	}

	public function remove_notifcations( $order_id ) {

		if ( wc_gzdp_order_needs_confirmation( $order_id ) ) {
					
			$mailer = WC()->mailer();
			$mails = $mailer->get_emails();
			
			remove_action( 'woocommerce_order_status_completed_notification', array( WC_germanized()->emails->get_email_instance_by_id( 'customer_completed_order' ), 'trigger' ) );

		}

	}

	public function remove_gzd_hooks() {
		remove_action( 'woocommerce_email_before_order_table', array( WC_germanized()->emails, 'email_pay_now_button' ), 0, 1 );
	}

	public function remove_gzd_frontend_hooks() {
		remove_action( 'woocommerce_thankyou', 'woocommerce_gzd_template_order_pay_now_button', wc_gzd_get_hook_priority( 'order_pay_now_button' ), 1 );
	}

	public function admin_hooks() {
		// Add Confirm Button to admin
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'admin_order_confirm_button' ), 0, 1 );
		// Save order hook
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'admin_check_order_confirmation' ), PHP_INT_MAX, 2 );
		// Admin Column Indicator
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'admin_order_table_title' ), 3 );
		// Admin Column action
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'admin_order_actions' ), PHP_INT_MAX, 2 );
		// Order email actions
		add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'admin_resend_order_emails' ), PHP_INT_MAX );
	}

	public function rename_email_template( $new, $tpl ) {
		if ( $new == 'customer_processing_order_pre' )
			$new = 'customer_processing_order';
		return $new;
	}
	
	public function set_order_confirmation_text( $translated, $original, $domain ) {
		$search = "Your order has been received and is now being processed. Your order details are shown below for your reference:";
		if ( $domain == 'woocommerce' && $original == $search )
			return __( "Your order has been processed. We are glad to confirm the order to you. Your order details are shown below for your reference:", "woocommerce-germanized-pro" );
		return $translated;
	}

	public function set_order_confirmation_payment_link( $order ) {
		
		$type = WC_germanized()->emails->get_current_email_object();
		if ( $type && $type->id == 'customer_order_confirmation' )
			$this->show_payment_link( $order->id );

	}

	/**
	 * Set processing order subject to static text for legal purposes (terms generator)
	 *  
	 * @param string $subject 
	 * @param object $order   
	 */
	public function set_order_confirmation_subject( $subject, $order ) {
		$email = WC()->mailer()->emails[ 'WC_GZDP_Email_Customer_Order_Confirmation' ];
		return $email->format_string( __( 'Your {site_title} order receipt', 'woocommerce-germanized-pro' ) );
	}

	/**
	 * Set processing order subject to static text for legal purposes (terms generator)
	 *  
	 * @param string $subject 
	 * @param object $order   
	 */
	public function set_processing_order_subject( $subject, $order ) {
		$email = WC()->mailer()->emails[ 'WC_Email_Customer_Processing_Order' ];
		return $email->format_string( __( 'Your order at {site_title}', 'woocommerce-germanized-pro' ) );
	}

	public function set_order_confirmation_needed( $order_id ) {
		update_post_meta( $order_id, '_order_needs_confirmation', true );
	}

	public function confirm_order( $order_id ) {
		
		$order = wc_get_order( $order_id );

		if ( ! wc_gzdp_order_needs_confirmation( $order_id ) )
			return false;

		$default_status =  apply_filters( 'woocommerce_default_order_status', 'pending' );

		// Init mailer to remove actions
		$mailer = WC()->mailer();
		$mails = $mailer->get_emails();

		$statuses = array(
			'woocommerce_order_status_' . $default_status . '_to_processing',
			'woocommerce_order_status_' . $default_status . '_to_completed',
			'woocommerce_order_status_' . $default_status . '_to_cancelled',
			'woocommerce_order_status_' . $default_status . '_to_on-hold',
		);
	
		// Temporarily remove all notification hooks		
		foreach ( $statuses as $status )
			remove_all_actions( $status . '_notification' );

		// Update to default
		$order->update_status( $default_status );

		// Fallback to ensure no fatal errors while trying to empty cart
		if ( WC()->cart == null )
			WC()->cart = new WC_Cart();

		$gateways = WC()->payment_gateways()->payment_gateways();
		$result = false;

		if ( isset( $gateways[ $order->payment_method ] ) ) {

			$gateway = $gateways[ $order->payment_method ];
			
			if ( is_object( $gateway ) ) {
				
				// Stop output
				ob_start();
				$result = $gateway->process_payment( $order_id );
				ob_end_clean();
			}

		}

		// Trigger Mail
		if ( $mail = WC_germanized()->emails->get_email_instance_by_id( 'customer_order_confirmation' ) ) {
			
			$mail->trigger( $order_id );
			delete_post_meta( $order_id, '_order_needs_confirmation' );
			do_action( 'woocommerce_gzdp_order_confirmed', $order );

		}

		return;

	}

	public function remove_gateway_redirect( $order_id ) {
		add_filter( 'woocommerce_cart_needs_payment', array( $this, 'cart_needs_payment_filter' ) );
		add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array( $this, 'stop_payment_completion' ), PHP_INT_MAX );
	}

	public function get_order_payment_url( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order->order_payment_info )
			return $order->order_payment_info;
		return false;
	}

	public function show_payment_link( $order_id ) {
		if ( $url = $this->get_order_payment_url( $order_id ) )
			wc_get_template( 'order/order-pay-now-button.php', array( 'url' => $url, 'order_id' => $order_id ) );
	}

	public function stop_payment_completion( $statuses ) {
		return array();
	}

	public function cart_needs_payment_filter() {
		return false;
	}

	public function add_payment_link( $template_name, $template_path, $located, $args ) {
		if ( $template_name != 'checkout/thankyou.php' )
			return;
		if ( ! isset( $args[ 'order' ] ) )
			return;
		$order = $args[ 'order' ];
		if ( wc_gzdp_order_needs_confirmation( $order ) || ! $order->needs_payment() )
			return;
		add_action( 'woocommerce_thankyou_' . $order->payment_method, array( $this, 'show_payment_link' ) );
	}

	public function hide_payment_info( $template_name, $template_path, $located, $args ) {
		if ( $template_name == 'checkout/thankyou.php' ) {
			if ( isset( $args[ 'order' ] ) ) {
				$order = $args[ 'order' ];
				if ( ! wc_gzdp_order_needs_confirmation( $order ) )
					return;
			}
			foreach( WC()->payment_gateways()->payment_gateways() as $key => $method ) {
				remove_all_actions( 'woocommerce_thankyou_' . $key );
			}
		}
	}

	public function set_default_order_status( $order_id ) {
		// Default order status
		add_filter( 'woocommerce_default_order_status', array( $this, 'default_order_status' ), PHP_INT_MAX );
		return $order_id;
	}

	public function default_order_status( $status ) {
		return 'on-hold';
	}

	public function add_emails( $emails ) {
		if ( ! isset( $emails[ 'WC_Email_Customer_Processing_Order' ] ) )
			return $emails;
		// Swap emails to disable automatic order confirmation
		$emails[ 'WC_GZDP_Email_Customer_Order_Confirmation' ] = include WC_germanized_pro()->plugin_path() . '/includes/emails/class-wc-gzdp-email-customer-order-confirmation.php';
		$emails[ 'WC_Email_Customer_Processing_Order' ]        = include WC_germanized_pro()->plugin_path() . '/includes/emails/class-wc-gzdp-email-customer-processing-order.php';
		return $emails;
	}

	public function admin_order_actions( $actions, $the_order ) {
		if ( wc_gzdp_order_needs_confirmation( $the_order ) ) {
			$actions = array();
			$actions[ 'complete' ] = array(
				'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_gzdp_confirm_order&order_id=' . $the_order->id ), 'woocommerce-gzdp-confirm-order' ),
				'name'      => __( 'Confirm Order', 'woocommerce-germanized-pro' ),
				'action'    => "complete"
			);
		}
		return $actions;
	}

	public function admin_order_table_title( $column ) {

		global $post, $woocommerce, $the_order;

		if ( empty( $the_order ) || $the_order->id != $post->ID )
			$the_order = wc_get_order( $post->ID );
		
		if ( ! wc_gzdp_order_needs_confirmation( $the_order ) )
			return;

		switch ( $column ) {
			case 'order_title' :
				echo '<small class="wc-gzdp-unconfirmed">' . __( 'Unconfirmed', 'woocommerce-germanized-pro' ) . '</small>';
			break;
		}
	}

	public function admin_check_order_confirmation( $post, $post_id ) {

		// Remove some actions
		remove_all_actions( 'woocommerce_cart_emptied' );

		$order = wc_get_order( $post );
		
		if ( isset( $_POST[ 'wc-gzdp-confirm-order' ] ) && ! empty( $_POST[ 'wc-gzdp-confirm-order' ] ) )
			$this->confirm_order( $order->id );
	}

	public function admin_order_confirm_button( $order ) {
		if ( ! wc_gzdp_order_needs_confirmation( $order ) )
			return;
		echo '<p class="wc-gzdp-submit-wrapper"><input type="hidden" id="wc-gzdp-confirm-order" name="wc-gzdp-confirm-order" value="" /><button type="submit" id="wc-gzdp-confirm-order-button" class="button button-primary">' . __( 'Confirm Order', 'woocommerce-germanized-pro' ) . '</button></p>';
	}

	public function admin_resend_order_emails( $emails ) {
		global $theorder;
		if ( isset( $theorder ) && wc_gzdp_order_needs_confirmation( $theorder->id ) ) {
			return array( 
				'customer_processing_order', 
				'customer_order_confirmation' 
			);
		} else {
			array_push( $emails, 'customer_order_confirmation' );
		}
		return $emails;
	}

}

return WC_GZDP_Contract_Helper::instance();