<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'WC_GZDP_Email_Customer_Invoice_Simple' ) ) :

/**
 * Customer Invoice
 *
 * An email sent to the customer via admin.
 *
 * @class 		WC_Email_Customer_Invoice
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 */
class WC_GZDP_Email_Customer_Invoice_Simple extends WC_Email {

	public $find;
	public $replace;

	public $send_pdf = true;
	public $invoice;
	public $subject_no_pdf;
	public $heading_no_pdf;

	public $template_html_no_pdf;
	public $template_plain_no_pdf;

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id             		= 'customer_invoice';
		$this->title          		= _x( 'Customer invoice', 'invoices', 'woocommerce-germanized-pro' );
		$this->description    		= _x( 'Customer invoice emails can be sent to the user containing PDF invoice as attachment.', 'invoices', 'woocommerce-germanized-pro' );

		$this->template_html  		= 'emails/customer-invoice-simple.php';
		$this->template_html_no_pdf = 'emails/customer-invoice.php';

		$this->template_plain  		 = 'emails/plain/customer-invoice-simple.php';
		$this->template_plain_no_pdf = 'emails/plain/customer-invoice.php';

		$this->subject       	 	= _x( '{invoice_number} for order {order_number} from {order_date}', 'invoices', 'woocommerce-germanized-pro' );
		$this->heading        		= _x( '{invoice_number} for order {order_number}', 'invoices', 'woocommerce-germanized-pro' );

		$this->subject_no_pdf 		= _x( 'Invoice for order {order_number} from {order_date}', 'invoices', 'woocommerce-germanized-pro' );
		$this->heading_no_pdf 		= _x( 'Invoice for order {order_number}', 'invoices', 'woocommerce-germanized-pro' );

		// Call parent constructor
		parent::__construct();

		$this->customer_email = true;
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $object ) {

		// Make it an object if not yet
		if ( ! is_object( $object ) ) {
			
			$object = get_post( $object );
			
			if ( $object->post_type == 'shop_order' )
				$object = wc_get_order( $object->ID );
			
			else if ( $object->post_type == 'invoice' )
				$object = wc_gzdp_get_invoice( $object->ID );
		}

		if ( is_object( $object ) ) {

			// Look for the actual invoice
			if ( $object instanceof WC_Order ) {

				$this->send_pdf = false;
				$this->object = $object;

				// Check if there are invoices
				$this->invoice = wc_gzdp_get_order_last_invoice( $object );

				if ( ! is_null( $this->invoice ) )
					$this->send_pdf = true;

			} else {

				$this->send_pdf = true;
				$this->object = wc_get_order( $object->order );
				$this->invoice = $object;

			}

			$this->find['order-date']      = '{order_date}';
			$this->find['order-number']    = '{order_number}';

			if ( $this->send_pdf ) {
				
				$recipient 			= $this->invoice->recipient;
				$this->recipient	= $recipient[ 'mail' ];
				$order_data 		= $this->invoice->order_data;

				$this->find['invoice-number']  = '{invoice_number}';

				$this->replace['order-date']     = date_i18n( wc_date_format(), strtotime( $order_data[ 'date' ] ) );
				$this->replace['order-number']   = $this->invoice->get_order_number();
				$this->replace['invoice-number'] = $this->invoice->get_title();

				$this->invoice->mark_as_sent();

			} else {

				$this->recipient               = $this->object->billing_email;
				$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
				$this->replace['order-number'] = $this->object->get_order_number();

			}

		}

		if ( ! $this->get_recipient() )
			return;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
		if ( $this->send_pdf ) {
			return apply_filters( 'woocommerce_email_subject_customer_invoice', $this->format_string( $this->subject ), $this->object );
		} else {
			return apply_filters( 'woocommerce_email_subject_customer_invoice_no_pdf', $this->format_string( $this->subject_no_pdf ), $this->object );
		}
	}

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
		if ( $this->send_pdf ) {
			return apply_filters( 'woocommerce_email_heading_customer_invoice', $this->format_string( $this->heading ), $this->object );
		} else {
			return apply_filters( 'woocommerce_email_heading_customer_invoice_no_pdf', $this->format_string( $this->heading_no_pdf ), $this->object );
		}
	}

	function get_attachments() {
		
		$attachments = parent::get_attachments();
		
		if ( $this->invoice )
			array_push( $attachments, $this->invoice->get_pdf_path() );
		
		return $attachments;
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		if ( ! $this->send_pdf ) {
			return $this->get_content_html_no_pdf();
		} else {
			ob_start();
			wc_get_template( $this->template_html, array(
				'invoice' 		=> $this->invoice,
				'order' 		=> $this->object,
				'show_pay_link' => $this->get_option( 'show_pay_link' ),
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'			=> $this
			) );
			return ob_get_clean();
		}
	}

	function get_content_html_no_pdf() {
		ob_start();
		wc_get_template( $this->template_html_no_pdf, array(
			'order' 		=> $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'			=> $this
		) );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		if ( ! $this->send_pdf ) {
			return $this->get_content_plain_no_pdf();
		} else {
			ob_start();
			wc_get_template( $this->template_plain, array(
				'invoice' 		=> $this->invoice,
				'order' 		=> $this->object,
				'show_pay_link' => $this->get_option( 'show_pay_link' ),
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'			=> $this
			) );
			return ob_get_clean();
		}
	}

	function get_content_plain_no_pdf() {
		ob_start();
		wc_get_template( $this->template_plain_no_pdf, array(
			'order' 		=> $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this
		) );
		return ob_get_clean();
	}

	/**
	 * Initialise settings form fields
	 */
	function init_form_fields() {

		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-germanized-pro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce-germanized-pro' ),
				'default' => 'yes'
			),
			'subject' => array(
				'title'         => __( 'Email Subject', 'woocommerce-germanized-pro' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce-germanized-pro' ), $this->subject ),
				'placeholder'   => '',
				'default'       => ''
			),
			'heading' => array(
				'title'         => __( 'Email Heading', 'woocommerce-germanized-pro' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce-germanized-pro' ), $this->heading ),
				'placeholder'   => '',
				'default'       => ''
			),
			'show_pay_link' => array(
				'title'         => __( 'Show pay link', 'woocommerce-germanized-pro' ),
				'type'          => 'checkbox',
				'label'			=> __( 'Enable pay link in Email', 'woocommerce-germanized-pro' ),
				'description'   => __( 'Show order pay link in invoice PDF Email if order status is set to pending.', 'woocommerce-germanized-pro' ),
				'default'       => 'no'
			),
			'subject_no_pdf' => array(
				'title'         => __( 'Email Subject (no PDF)', 'woocommerce-germanized-pro' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->subject_no_pdf ),
				'placeholder'   => '',
				'default'       => ''
			),
			'heading_no_pdf' => array(
				'title'         => __( 'Email Heading (no PDF)', 'woocommerce-germanized-pro' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->heading_no_pdf ),
				'placeholder'   => '',
				'default'       => ''
			),
			'email_type' => array(
				'title'         => __( 'Email type', 'woocommerce-germanized-pro' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', 'woocommerce-germanized-pro' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'       => $this->get_email_type_options(),
				'desc_tip'      => true
			)
		);

	}

}

endif;

return new WC_GZDP_Email_Customer_Invoice_Simple();