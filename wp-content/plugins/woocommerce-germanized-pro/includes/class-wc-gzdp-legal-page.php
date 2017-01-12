<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Legal_Page extends WC_GZDP_Post_PDF {

	public function __construct( $page_id ) {
		parent::__construct( $page_id );
		$this->content_type = 'legal_page';
	}

	public function is_enabled() {
		return ( get_option( $this->get_option_page_slug_prefix() . '_enabled' ) === 'yes' );
	}

	public function get_content_pdf() {
	 	$content = ( get_post_meta( $this->id, '_legal_text', true ) ? htmlspecialchars_decode( get_post_meta( $this->id, '_legal_text', true ) ) : htmlspecialchars_decode( $this->post->post_content ) );
	 	$content = $this->filter_html( apply_filters( 'the_content', $content ) );
	 	return apply_filters( 'woocommerce_gzdp_legal_page_pdf_content', $content, $this );
	}

	public function filter_html( $content ) {
		return strip_tags( $content, apply_filters( 'woocommerce_gzdp_legal_page_pdf_allowed_tags', '<a><b><blockquote><br><br/><dd><del><div><dl><dt><em><font><h1><h2><h3><h4><h5><h6><hr><hr/><i><img><li><ol><p><pre><small><span><strong><sub><sup><table><tcpdf><td><th><thead><tr><tt><u><ul>', $this ) );
	}

	public function has_attachment() {
		
		$has_attachment = parent::has_attachment();

		if ( ! $has_attachment && get_option( $this->get_option_page_slug_prefix() . '_pdf' ) )
			$has_attachment = true;

		return $has_attachment;

	}

	public function get_pdf_path() {
		
		$path = parent::get_pdf_path();

		$attachment = get_option( $this->get_option_page_slug_prefix() . '_pdf' );

		if ( $attachment && ! empty( $attachment ) )
			$path = get_attached_file( $attachment );

		return $path;

	}

	public function generate_pdf( $preview = false ) {
		
		// Remove shortcodes
		foreach ( apply_filters( 'woocommerce_gzdp_legal_page_shortcode_removal', array( 'revocation_form' ), $this ) as $shortcode ) {
			remove_shortcode( $shortcode );
			add_shortcode( $shortcode, array( $this, 'empty_shortcode' ) );
		}

		return parent::generate_pdf( $preview );
	}

	public function empty_shortcode( $args = array(), $content = '' ) {
		return '';
	}

	public function get_option_page_slug_prefix() {

		$pages = WC_GZDP_Legal_Page_Helper::instance()->get_legal_page_ids();
		$page_slug = false;

		foreach ( $pages as $page => $id ) {
			
			if ( $id == $this->id )
				$page_slug = $page;
		}
		
		if ( ! $page_slug )
			return false;		

		return 'woocommerce_gzdp_legal_page_' . $page_slug;

	}

}

?>