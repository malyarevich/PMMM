<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Legal_Page_Helper {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		add_filter( 'woocommerce_email_attachments', array( $this, 'attach_pdfs' ), 0, 3 );
		add_action( 'woocommerce_gzd_attach_email_footer', array( $this, 'remove_plain_attachments' ), 0, 3 );

		if ( is_admin() ) 
			$this->admin_hooks();	

	}

	public function admin_hooks() {

		add_action( 'save_post', array( $this, 'save_page' ), 10, 3 );

		add_filter( 'woocommerce_gzd_settings_sections', array( $this, 'register_section' ), 3 );
		add_filter( 'woocommerce_gzd_get_settings_pdf', array( $this, 'get_settings' ) );
		add_filter( 'woocommerce_gzdp_legal_page_layout_settings_colors', array( $this, 'get_settings_colors' ) );
		add_filter( 'woocommerce_gzdp_legal_page_layout_settings_templates', array( $this, 'get_settings_templates' ) );
		add_filter( 'woocommerce_gzdp_legal_page_layout_settings_margins', array( $this, 'get_settings_margin' ) );
		add_filter( 'woocommerce_gzdp_legal_page_layout_settings_static_texts', array( $this, 'get_settings_static_text' ) );
		
		add_action( 'woocommerce_gzd_after_save_section_pdf', array( $this, 'check_pdf_template_version' ), 0, 1 );
		add_action( 'woocommerce_gzd_after_save_section_pdf', array( $this, 'regenerate_pdfs' ), 5, 1 );
		add_action( 'woocommerce_gzd_before_save_section_email', array( $this, 'section_before_save' ), 0, 1 );
		add_filter( 'woocommerce_germanized_settings_email', array( $this, 'pdf_attachment_settings' ), 100 );

		add_action( 'woocommerce_settings_legal_page_layout_options', array( $this, 'preview_button' ) );
		
		add_action( 'admin_init', array( $this, 'download_legal_page' ), 0 );
	}

	public function preview_button() {
		include_once( WC_Germanized_pro()->plugin_path() . '/includes/admin/views/html-pdf-settings-before.php' );
	}

	public function remove_plain_attachments( $do_attach, $mail, $page_key ) {

		$pages = $this->get_legal_page_ids();

		if ( isset( $pages[ $page_key ] ) ) {

			$id = $pages[ $page_key ];
			$legal = new WC_GZDP_Legal_Page( $id );

			if ( $legal->is_enabled() )
				return false;

		}

		return $do_attach;

	}

	public function attach_pdfs( $attachments, $mail_id, $mail ) {

		$legal_attachments = array();

		foreach ( $this->get_legal_page_ids() as $page => $id ) {

			$legal = new WC_GZDP_Legal_Page( $id );
			$templates = array();
			
			if ( get_option( 'woocommerce_gzd_mail_attach_' . $page ) )
				$templates = get_option( 'woocommerce_gzd_mail_attach_' . $page );
			
			if ( $legal->is_enabled() && in_array( $mail_id, $templates ) ) {
				array_push( $legal_attachments, $legal->get_pdf_path() );
			}

		}

		if ( ! empty( $legal_attachments ) )
			$attachments = array_merge( $legal_attachments, $attachments );

		return $attachments;

	}

	public function regenerate_pdfs( $settings ) {

		$pages = $this->get_legal_page_ids();
		
		foreach ( $pages as $page => $id ) {

			if ( ! get_post( $id ) )
				continue;

			$this->refresh_pdf( $id );

		}
	}

	public function section_before_save( $settings ) {

		$pages = $this->get_legal_page_ids();

		foreach ( $pages as $page => $id ) {

			$woo_option = 'woocommerce_' . $page . '_page_id';

			if ( get_option( $woo_option ) ) {

				if ( ! get_post( get_option( $woo_option ) ) )
					continue;

				$this->refresh_pdf( get_option( $woo_option ) );

			}

		}

	}

	public function download_legal_page() {
		if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'wc-gzdp-download-legal-page' ) {
			$page = false;
			if ( isset( $_GET[ 'preview' ] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wc-gzdp-download' ) ) {
				ob_start();
				$page = new WC_GZDP_Legal_Page_Preview();
				$page->generate_pdf();
				echo ob_get_clean();
				exit();
			} else if ( isset( $_GET[ 'id' ] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wc-gzdp-download' ) ) {
				$id = absint( $_GET[ 'id' ] );
				$page = new WC_GZDP_Legal_Page( $id );
				$page->refresh();
				if ( ! current_user_can( 'manage_woocommerce' ) )
					$page = false;
			} else {
				wp_die( __( 'Cheatin huh?', 'woocommerce-germanized-pro' ) );
			}
			if ( $page )
				WC_GZDP_Download_Handler::download( $page, ( ( isset( $_GET[ 'force' ] ) && $_GET[ 'force' ] ) ? true : false ) );
			wp_die( __( 'Missing permissions to download legal page', 'woocommerce-germanized-pro' ) );
		}
	}

	public function pdf_attachment_settings( $settings ) {

		// Remove
		$settings = WC_GZDP_Settings::instance()->remove_setting_area( $settings, 'email_attachment_options' );

		$mails = array();

		$pages = $this->get_legal_page_ids( false );

		$settings = array_merge( $settings, array(

			array( 'title' => __( 'Email Attachment Options', 'woocommerce-germanized-pro' ), 'type' => 'title', 'id' => 'email_attachment_options' ),

		) );

		foreach ( $pages as $id => $value ) {

			$plain_id = $id;
			$page_id = '';

			if ( ! $page_id = woocommerce_get_page_id( $plain_id ) )
				continue;

			if ( ! get_post( $page_id ) )
				continue;

			$legal_page = new WC_GZDP_Legal_Page( $page_id );

			$settings = array_merge( $settings, array(

				array(
					'title' 	=> $this->get_legal_page_title( $id ),
					'desc' 		=> ( $legal_page->get_pdf_path() ? sprintf( _x( 'Send %s as <a href="%s" target="_blank">PDF Attachment</a> instead of plain text.', 'legal-page', 'woocommerce-germanized-pro' ), $this->get_legal_page_title( $id ), $legal_page->get_pdf_url() ) : _x( 'Send as PDF Attachment instead of plain text.', 'legal-page', 'woocommerce-germanized-pro' ) ),
					'id' 		=> 'woocommerce_gzdp_legal_page_' . $plain_id . '_enabled',
					'default'	=> 'no',
					'type' 		=> 'checkbox',
				),

				array(
					'title' 	=> _x( 'Attachment', 'legal-page', 'woocommerce-germanized-pro' ),
					'desc' 		=> sprintf( _x( 'Manually choose PDF file to attach as %s. Leave empty to automatically generate PDF files from your pages\' text. Edit layout options <a href="%s">here</a>.', 'legal-page', 'woocommerce-germanized-pro' ), $this->get_legal_page_title( $id ), admin_url( 'admin.php?page=wc-settings&tab=germanized&section=pdf' ) ),
					'id' 		=> 'woocommerce_gzdp_legal_page_' . $plain_id . '_pdf',
					'default'	=> '',
					'data-type' => 'application/pdf',
					'type' 		=> 'gzdp_attachment',
				) 
			) );

		}

		$settings = array_merge( $settings, array( array( 'type' => 'sectionend', 'id' => 'contract_options' ) ) );

		return $settings;
	}

	public function register_section( $sections ) {
		$sections[ 'pdf' ] = _x( 'PDF', 'legal-pages', 'woocommerce-germanized-pro' );
		return $sections;
	}

	public function check_pdf_template_version() {
		$templates = array( 'woocommerce_gzdp_legal_page_template_attachment', 'woocommerce_gzdp_legal_page_template_attachment_first' );	
		foreach ( $templates as $template ) {
			
			if ( $file = get_option( $template ) ) {
				
				$file = get_attached_file( $file );

				if ( ! $file )
					continue;

				try {

					$posts = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish' ) );

					if ( ! empty( $posts ) ) {
				
						$page = new WC_GZDP_Legal_Page( $posts[0]->ID );
						$pdf = new WC_GZDP_Legal_Page_PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
						$pdf->set_object( $page );
						$pdf->setTemplate( $page->get_pdf_template() );
						$pdf->addPage();

					}
				
				} catch( Exception $e ) {
					
					delete_option( $template );
					WC_Admin_Settings::add_error( _x( 'Your PDF template seems be converted (version > 1.4) or compressed. Please convert (http://convert.neevia.com/pdfconvert/) your pdf file to version 1.4 or lower before uploading.', 'invoices', 'woocommerce-germanized-pro' ) );

				}
			}
		}
	}

	public function get_settings() {
		return WC_GZDP_PDF_Helper::instance()->get_layout_settings( 'legal_page' );
	}

	public function get_settings_colors( $settings ) {

		return array_merge( $settings, array(
			array(
				'title' 	=> _x( 'Footer border color', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a border color for your footer (if shown).', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_legal_page_footer_border_color',
				'type' 		=> 'color',
				'default'	=> '#CCC',
			),
		) );

	}

	public function get_settings_templates( $settings ) {

		$settings = array_merge( $settings, array(
			array(
				'title' 	=> _x( 'Show title', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc'		=> _x( 'Print page title above content.', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc_tip' 	=> _x( 'Optionally print the page title above the content. If you have embedded the title within the content, deactivate this option.', 'legal-page', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_legal_page_show_title',
				'type' 		=> 'checkbox',
				'default'	=> 'yes',
			),
		) );

		// Default: dont show footer
		foreach ( $settings as $key => $setting ) {

			if ( isset( $setting[ 'id' ] ) && $setting[ 'id' ] == 'woocommerce_gzdp_legal_page_show_footer' )
				$settings[ $key ][ 'default' ] = 'no';

		}

		return $settings;

	}

	public function get_settings_margin( $settings ) {

		return array_merge( $settings, array(
			array(
				'title' 	=> _x( 'Title top margin', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose if you wish to have a top margin before the title is outputted.', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_legal_page_title_margin_top',
				'type' 		=> 'gzdp_decimal',
				'default'	=> '',
			),
			array(
				'title' 	=> _x( 'Content top margin', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose if you wish to have a top margin before the content is outputted.', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_legal_page_content_margin_top',
				'type' 		=> 'gzdp_decimal',
				'default'	=> '',
			),
		) );

	}

	public function get_settings_static_text( $settings ) {

		return array_merge( $settings, array(
			array(
				'title' 	=> _x( 'Before content', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc' 	    => _x( 'You may want to display a static text right before the output of page content. You may use basic HTML elements and inline CSS for layouting. Use table layouts instead of divs and floatings. Margins and paddings will be ignored.', 'legal-page', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_legal_page_text_before_content',
				'default'	=> '',
				'css' 		=> 'width:70%; height: 85px;',
				'type' 		=> 'gzdp_editor',
			),

			array(
				'title' 	=> _x( 'After content', 'legal-page', 'woocommerce-germanized-pro' ),
				'desc' 	    => _x( 'You may want to display a static text right after the output of page content. You may use basic HTML elements and inline CSS for layouting. Use table layouts instead of divs and floatings. Margins and paddings will be ignored.', 'legal-page', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_legal_page_text_after_content',
				'default'	=> '',
				'css' 		=> 'width:70%; height: 85px;',
				'type' 		=> 'gzdp_editor',
			),
		) );

	}

	public function get_legal_page_title( $key ) {

		$titles = wc_gzd_get_email_attachment_order();

		if ( isset( $titles[ $key ] ) )
			return $titles[ $key ];

		return '';

	}

	public function get_legal_page_ids( $ignore_empty = true ) {

		$pages = wc_gzd_get_email_attachment_order();
		$ids = array();
		
		foreach ( $pages as $page => $title ) {
			$id = woocommerce_get_page_id ( $page );
			
			if ( $id == -1 && $ignore_empty )
				continue;
			
			$ids[ $page ] = woocommerce_get_page_id ( $page );
		}

		return $ids;

	}

	public function save_page( $post_id, $post, $update ) {

		$pages = $this->get_legal_page_ids();

		if ( wp_is_post_revision( $post_id ) || 'page' !== $post->post_type || ! in_array( $post_id, $pages ) )
			return;

		$this->refresh_pdf( $post_id );

	}

	public function refresh_pdf( $post_id ) {

		if ( ! get_post( $post_id ) )
			return;

		$legal = new WC_GZDP_Legal_Page( $post_id );
		
		if ( $legal->is_enabled() && ! get_option( $legal->get_option_page_slug_prefix() . '_pdf' ) )
			$legal->refresh();

	}

}

return WC_GZDP_Legal_Page_Helper::instance();