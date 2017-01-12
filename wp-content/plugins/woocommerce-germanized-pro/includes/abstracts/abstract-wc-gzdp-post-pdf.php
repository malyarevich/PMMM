<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Post_PDF {

	public $post;
	public $id;
	public $content_type;
	public $type;
	public $meta;

	public function __construct( $post ) {

		if ( is_numeric( $post ) )
			$post = get_post( $post );

		$this->post = $post;
		$this->id = $this->post->ID;
		$this->content_type = $this->post->post_type;
		$this->type = 'simple';
	}

	public function __get( $key ) {
		$value = get_post_meta( $this->id, '_' . $this->content_type . '_' . $key, true );
		return $value;
	}

	public function populate() {
		clean_post_cache( $this->id );
		$this->post = get_post( $this->id );
		$this->id = $this->post->ID;
	}

	public function is_type( $type ) {
		return $this->type === $type;
	}

	public function get_option( $key, $default = false, $suppress_typing = false ) {

		$option = get_option( 'woocommerce_gzdp_' . $this->content_type . '_' . $key, $default );

		if ( ! $suppress_typing && get_option( 'woocommerce_gzdp_' . $this->content_type . '_' . $this->type . '_' . $key, $default ) !== $default ) {
			
			$option = get_option( 'woocommerce_gzdp_' . $this->content_type . '_' . $this->type . '_' . $key, $default );
		
		}

		return $option;
	}

	public function get_color_option( $key ) {
		$option = $this->get_option( $key );
		
		if ( $option && ! empty( $option ) && strpos( $option, '#' ) === false )
			$option = '#' . $option;
		
		return $option;
	}

	public function get_static_pdf_text( $where = '' ) {
		
		if ( empty( $where ) )
			return false;
		
		$key = 'text_' . $where;

		$html = false;

		if ( 'simple' !== $this->type && 'yes' !== $this->get_option( 'table_content' ) ) {
			$html = $this->get_option( 'text_' . $where, '', true );
		} else {
			$html = $this->get_option( 'text_' . $where );
		}
		
		$GLOBALS[ 'pdf' ] = $this;

		do_action( 'woocommerce_gzdp_before_pdf_static_content', $this );

		if ( $html )
			$html = do_shortcode( wpautop( htmlspecialchars_decode( wp_unslash( $html ) ) ) );
		
		if ( $this->$key )
			$html = do_shortcode( wpautop( $this->$key ) );

		$html = apply_filters( 'woocommerce_gzdp_pdf_static_content', $html, $this, $where );
		
		return $html;
	}

	public function get_title( $html = false ) {
		$title = $this->post->post_title;
		return apply_filters( 'woocommerce_gzdp_pdf_title', $title );
	}

	public function get_title_pdf() {
		return apply_filters( 'woocommerce_gzdp_' . $this->content_type . '_title_pdf', $this->get_title( true ), $this );
	}

	public function get_font() {
		$font = $this->get_option( 'font', 'helvetica' );
		$dir = WC_GZDP_PDF_Helper::instance()->get_custom_font_upload_dir();
		
		// Use default font if font doesn't exist
		if ( $this->has_custom_font() && ! @file_exists( trailingslashit( $dir[ 'basedir' ] ) . $font . '.php' ) )
			$font = 'helvetica';
		
		return $font;
	}

	public function has_custom_font() {
		$default_fonts = WC_GZDP_PDF_Helper::instance()->get_fonts( true );
		if ( ! isset( $default_fonts[ $this->get_option( 'font' ) ] ) )
			return true;
		return false;
	}

	public function get_font_size() {
		return absint( $this->get_option( 'font_size', 8 ) );
	}

	public function get_date( $format ) {
		return date_i18n( $format, strtotime( $this->post->post_date ) );
	}

	public function refresh() {

		do_action( 'woocommerce_gzdp_before_pdf_refresh', $this );

		$this->populate();

		do_action( 'woocommerce_gzdp_before_pdf_generate', $this );

		$file = $this->generate_pdf();

		do_action( 'woocommerce_gzdp_before_pdf_save', $this );

		$this->save_attachment( $file );
	}

	public function save_attachment( $file ) {
		
		$filetype = wp_check_filetype( basename( $file ), null );
		$wp_upload_dir = WC_germanized_pro()->get_upload_dir();
		$file_path_relative = WC_germanized_pro()->get_relative_upload_path( $file );
		
		// Add new Attachment
		$attachment = array(
			'guid'           		=> $wp_upload_dir[ 'url' ] . '/' . basename( $file ),
			'post_mime_type' 		=> $filetype[ 'type' ],
			'post_title'     		=> $this->get_title(),
			'post_content'   		=> '',
			'post_status'    		=> 'private',
		);
		
		if ( $this->attachment ) {
		
			$attachment[ 'ID' ] = $this->attachment;
			wp_update_post( $attachment );
			update_post_meta( $this->attachment, '_wp_attached_file', $file_path_relative );
			$attach_id = $this->attachment;
		
		} else {
			$attach_id = wp_insert_attachment( $attachment, $file_path_relative, $this->id );
		}
		
		if ( ! $attach_id )
			return false;
		
		update_post_meta( $this->id, '_' . $this->content_type .'_attachment', $attach_id );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		
		do_action( 'woocommerce_gzdp_pdf_attachment_updated', $attach_id, $this );

	}

	public function get_pdf_template( $first = false ) {
		$template = ( $first ? $this->get_option( 'template_attachment_first' ) : $this->get_option( 'template_attachment' ) );
		if ( is_array( $template ) )
			$template = $template[0];
		return ( $template ? get_attached_file( $template ) : false );
	}

	public function has_pdf_footer() {
		return ( $this->get_option( 'show_footer' ) != 'no' ? true : false );
	}

	public function has_pdf_header() {
		return ( $this->get_option( 'show_header' ) != 'no' ? true : false );
	}

	public function has_pdf_header_first() {
		return ( $this->get_option( 'show_first_page_header' ) != 'no' ? true : false );
	}

	public function get_filename() {
		
		if ( $this->filename )
			return $this->filename;
		
		$upload_dir = WC_germanized_pro()->get_upload_dir();
		$filename = apply_filters( 'woocommerce_gzdp_' . $this->content_type . '_unique_filename', wp_unique_filename( $upload_dir[ 'path' ], sanitize_file_name( $this->get_title() ) . '.pdf' ), $this );
		update_post_meta( $this->id, '_' . $this->content_type . '_filename', $filename );
		
		return $filename;
	}

	public function has_attachment() {
		return ( $this->attachment ? true : false );
	}

	public function get_pdf_path() {
		if ( ! $this->attachment )
			return false;
		
		WC_germanized_pro()->set_upload_dir_filter();
		$attachment = get_attached_file( $this->attachment );
		WC_germanized_pro()->unset_upload_dir_filter();

		return $attachment;
	
	}

	public function locate_template( $template ) {
		
		$return = str_replace( '_', '-', $this->content_type ) . '/' . $template;

		if ( $this->type != 'simple' ) {
			
			$check = wc_locate_template( str_replace( '_', '-', $this->type ) . '/' . $template );
			
			if ( strpos( $check, 'woocommerce-germanized-pro' ) !== false )
				$return = str_replace( '_', '-', $this->type ) . '/' . $template;
		}

		return $return;
	}

	public function get_template_content( $template, $pdf ) {	
		$GLOBALS[ 'post_pdf' ] = $this;
		ob_start();
		wc_get_template( $template, array( $this->content_type => $this, 'total_width' => $pdf->getPageWidthRemaining(), 'pdf' => $pdf ) );
		$html = ob_get_clean();
		return $html;
	}

	public function get_pdf_url( $force = false ) {
		return admin_url( '?action=wc-gzdp-download-' . str_replace( '_', '-', $this->content_type ) . '&force=' . $force . '&_wpnonce=' . wp_create_nonce( 'wc-gzdp-download' ) . '&id=' . $this->id );
	}

	public function generate_pdf( $preview = false ) {

		$classname = 'WC_GZDP_PDF';
		$subclass_name = 'WC_GZDP_' . ucwords( str_replace( '-', '_', $this->content_type ) ) . '_PDF';
		
		if ( class_exists( $subclass_name ) )
			$classname = $subclass_name;

		$pdf = new $classname( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->set_object( $this );

		// LEFT, TOP, RIGHT
		$margins = $this->get_option( 'margins' );

		$pdf->setTemplate( $this->get_pdf_template() );
		$pdf->setTemplateFirst( $this->get_pdf_template( true ) );
		$pdf->SetAutoPageBreak( true, isset( $margins[3] ) ? $margins[3] : 50 );
		$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
		$pdf->setFontSubsetting( false );
		$pdf->SetMargins( $margins[0], $margins[1], $margins[2] );
		
		$tagvs = array(
			'p' => array( 
				0 => array( 'h' => 1, 'n' => 1 ), 
				1 => array( 'h' => 0.05, 'n'=> 1 )
			),
			'div' => array(
				0 => array( 'h' => 0, 'n' => 0 ),
				1 => array( 'h' => 0, 'n' => 0 ),
			),
		);

		$pdf->setHtmlVSpace( $tagvs );

		if ( $this->has_pdf_header_first() )
			$pdf->setCustomHeaderFirst( $this->get_template_content( $this->locate_template( 'header-first.php' ), $pdf ) );

		if ( $this->has_pdf_header() )
			$pdf->setCustomHeader( $this->get_template_content( $this->locate_template( 'header.php' ), $pdf ) );

		if ( $this->has_pdf_footer() )
			$pdf->setCustomFooter( $this->get_template_content( $this->locate_template( 'footer.php' ), $pdf ) );
		
		$pdf->addPage();

		$pdf->setObjectFont();

		$pdf->writeCustomHTML( $this->get_template_content( $this->locate_template( 'content.php' ), $pdf ), array( 'classes' => array( 'content' ) ) );

		$pdf->setY( $pdf->getY() + 5 );

		$upload_dir = WC_germanized_pro()->get_upload_dir();
		$filename = $this->get_filename();
		$path = trailingslashit( $upload_dir[ 'path' ] ) . $filename;
		
		$pdf->Output( $path, ( $preview ) ? 'I' : 'F' );

		return $path;
	}

	public function delete( $bypass_trash = false ) {
		if ( $this->attachment )
			wp_delete_post( $this->attachment, $bypass_trash );
		wp_delete_post( $this->id, $bypass_trash );
	}

}

?>