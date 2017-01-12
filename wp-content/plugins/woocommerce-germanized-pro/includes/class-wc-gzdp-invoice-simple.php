<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Invoice_Simple extends WC_GZDP_Invoice {

	public function __construct( $invoice ) {
		parent::__construct( $invoice );
	}

	public function get_email_class() {
		return 'WC_Email_Customer_Invoice';
	}

}