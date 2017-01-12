<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Legal_Page_Preview extends WC_GZDP_Legal_Page {

	public function __construct( $page_id = null ) {
		$this->content_type = 'legal_page';
	}

	public function is_enabled() {
		return true;
	}

	public function get_title( $html = false ) {
		$title = __( 'Germanized Pro Preview PDF', 'legal-page', 'woocommerce-germanized-pro' );
		return $title;
	}

	public function get_content_pdf() {
		ob_start();
		include_once( WC_Germanized_pro()->plugin_path() . '/includes/admin/views/html-legal-page-preview-content.php' ); 	
		$html = ob_get_clean();
		return $html;
	}

	public function generate_pdf( $preview = false ) {
		return parent::generate_pdf( true );
	}

}

?>