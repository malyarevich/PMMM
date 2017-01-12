<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Admin_Invoice_Export_Attachments extends WC_GZDP_Admin_Invoice_Export {

	public $filepath;

	public function __construct( $args = array() ) {
		$this->data_type = 'zip';
		parent::__construct( $args );
	}

	public function set_header( $content_type = 'application/zip' ) {
		parent::set_header( $content_type );
	}

	public function output() {
		$attachments = array();
		if ( $this->query->have_posts() ) {
			while( $this->query->have_posts() ) {
				$this->query->the_post();
				global $post;
				$invoice = WC_Germanized_Pro()->invoice_factory->get_invoice( $post );
				if ( $invoice->has_attachment() )
					$attachments[ $invoice->id ] = array( 'path' => $invoice->get_pdf_path(), 'filename' => $invoice->get_filename() );
			}
		}
		if ( ! empty( $attachments ) ) {
			$upload_dir = WC_germanized_pro()->get_upload_dir();
			$this->filename = wp_unique_filename( $upload_dir[ 'path' ], $this->filename );
			$this->filepath = trailingslashit( $upload_dir[ 'path' ] ) . $this->filename;
			$zip = new ZipArchive();
			$zip->open( $this->filepath, ZipArchive::CREATE );
			foreach ( $attachments as $attachment )
				$zip->addFile( $attachment[ 'path' ], $attachment[ 'filename' ] );
			$zip->close();
			header( 'Content-Length: ' . filesize( $this->filepath ) );
			readfile( $this->filepath );
		}
	}

}