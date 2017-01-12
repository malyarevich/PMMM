<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_PDF_Helper {

	/**
	 * Single instance of WooCommerce Germanized Main Class
	 *
	 * @var object
	 */
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-germanized-pro' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-germanized-pro' ), '1.0' );
	}
	
	public function __construct() {
		if ( is_admin() ) 
			$this->admin_hooks();	
	}

	public function admin_hooks() {
		add_action( 'admin_notices', array( $this, 'set_upload_dir_not_exist_notice' ) );
		$this->custom_font_hooks();
	}

	public function custom_font_hooks() {
		// add_action( 'admin_init', array( $this, 'parse_custom_fonts' ) );
		add_filter( 'upload_mimes', array( $this, 'allow_ttf_upload' ) );
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'register_font_upload_dir_filter' ) );
		add_filter( 'wp_handle_upload', array( $this, 'unregister_font_upload_dir_filter' ) );
		add_action( 'admin_init', array( $this, 'check_font_regenerate' ) );
		add_filter( 'woocommerce_gzdp_pdf_fonts', array( $this, 'set_custom_fonts' ) );
	}

	public function set_upload_dir_not_exist_notice() {

		$dir = WC_germanized_pro()->get_upload_dir();

		if ( @is_dir( $dir[ 'basedir' ] ) )
			return;
		
		?>
			<div class="error">
				<p><?php printf( __( 'WooCommerce Germanized Pro upload directory doesn\'t seem to exist. Please create the folder %s and make sure that it is writeable.', 'woocommerce-germanized-pro' ), '<i>wp-content/uploads/wc-gzdp</i>' ); ?></p>
			</div>

		<?php
	}

	public function get_fonts( $remove_filters = false ) {
		
		$fonts = array(
			'courier'      => _x( 'Courier', 'font-name', 'woocommerce-germanized-pro' ),
			'helvetica'    => _x( 'Helvetica', 'font-name', 'woocommerce-germanized-pro' ),
			'times'        => _x( 'Times New Roman', 'font-name', 'woocommerce-germanized-pro' ),
		);

		if ( $remove_filters )
			return $fonts;

		return apply_filters( 'woocommerce_gzdp_pdf_fonts', $fonts );
	}

	public function get_pdf_version( $file ) {

		$content = file_get_contents( $file );
		if ( ! $content )
			return false;
		
		preg_match( '/\d\.\d/', substr( $content, 0, 10 ), $match );

		if ( isset( $match[0] ) )
			return $match[0];

		return false;
	}

	public function get_custom_fonts() {
		return get_option( 'woocommerce_gzdp_pdf_custom_font_names' );
	}

	public function set_custom_fonts( $fonts ) {
		if ( $custom = $this->get_custom_fonts() ) {
			foreach( (array) $custom as $new )
				$fonts[ $new ] = ucfirst( $new );
		}
		return $fonts;
	}

	public function get_custom_font_path( $font ) {
		$fonts = $this->get_custom_fonts();
		if ( ! $fonts || ! in_array( $font, (array) $fonts ) )
			return false;
		$dir = $this->get_custom_font_upload_dir();
		return trailingslashit( $dir[ 'basedir' ] ) . $font . '.php';
	}

	public function check_font_regenerate() {
		if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'wc-gzdp-regenerate-fonts' && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wc-gzdp-regenerate-fonts' ) )
			$this->parse_custom_fonts();
	}

	public function register_font_upload_dir_filter( $file ) {
		if ( strpos( $file[ 'name' ], '.ttf' ) !== false )
			add_filter( 'upload_dir', array( $this, "filter_font_upload_dir" ), 0, 1 );
		return $file;
	}

	public function filter_font_upload_dir( $args ) {
		$upload_base = trailingslashit( $args[ 'basedir' ] );
		$upload_url = trailingslashit( $args[ 'baseurl' ] );
		
		$args[ 'basedir' ] = apply_filters( 'wc_germanized_pro_upload_font_path', $upload_base . 'wc-gzdp/fonts' );
		$args[ 'baseurl' ] = apply_filters( 'wc_germanized_pro_upload_font_url', $upload_url . 'wc-gzdp/fonts' );
		$args[ 'path' ] = $args[ 'basedir' ];
		$args[ 'url' ] = $args[ 'baseurl' ];
		$args[ 'subdir' ] = '';

		return $args;
	}

	public function unregister_font_upload_dir_filter( $file ) {
		
		if ( strpos( $file[ 'file' ], '.ttf' ) !== false )
			$this->parse_custom_fonts();
		
		remove_filter( 'upload_dir', array( $this, "filter_font_upload_dir" ), 0, 1 );
		return $file;

	}

	public function allow_ttf_upload( $m ) {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_woocommerce' ) )
			return $m;
		$m[ 'ttf' ] = 'application/x-font-truetype';
		$m[ 'opentype' ] = 'font/opentype';
		return $m;
	}

	public function get_custom_font_upload_dir() {
		add_filter( 'upload_dir', array( $this, "filter_font_upload_dir" ), 0, 1 );
		$paths = wp_upload_dir();
		remove_filter( 'upload_dir', array( $this, "filter_font_upload_dir" ), 0, 1 );
		return $paths;
	}

	public function parse_custom_fonts() {

		$dirs = $this->get_custom_font_upload_dir();
		$path = $dirs[ 'basedir' ];

		if ( ! @is_dir( trailingslashit( $path ) ) )
			@mkdir( $path );

		if ( ! class_exists( 'TCPDF' ) )
			include_once( 'libraries/tcpdf/tcpdf.php' );
		
		$files = @glob( trailingslashit( $path ) .'*.{ttf}', GLOB_BRACE );
		$fonts = new TCPDF_Fonts();
		$font_data = array();

		if ( ! empty( $files ) ) {
			
			foreach( $files as $file ) {
				try {
					$fontname = $fonts->addTTFfont( $file, '', '', 32, trailingslashit( $path ) );
				} catch( Exception $e ) {
					
				}
			}

		}

		$font_files = @glob( trailingslashit( $path ) .'*.{php}', GLOB_BRACE );
		if ( ! empty( $font_files ) ) {
			foreach( $font_files as $file ) {
				$file_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename( $file ) );
				if ( ! empty( $file_name ) )
					array_push( $font_data, $file_name );
			}
		}

		if ( ! empty( $font_data ) )
			update_option( 'woocommerce_gzdp_pdf_custom_font_names', $font_data );
		else
			delete_option( 'woocommerce_gzdp_pdf_custom_font_names' );

	}

	public function get_layout_settings( $type = '' ) {

		$layout_settings_title = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_title', array(
			array( 'title' => _x( 'Layout', 'pdf', 'woocommerce-germanized-pro' ), 'type' => 'title', 'id' => $type . '_layout_options' ),
		) );

		$layout_settings_fonts = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_fonts', array(

			array(
				'title' 	=> _x( 'Default font size', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a default font size for your PDF.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_' . $type . '_font_size',
				'type' 		=> 'number',
				'default'	=> 8,
			),

			array(
				'title' 	=> _x( 'Custom Fonts', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip' 	=> _x( 'Upload your custom fonts as ttf here. You will then be able to use them as a font within your PDF.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc'		=> sprintf( _x( 'Please upload file in ttf-Format. Read more about custom font support <a href="%s" target="_blank">here</a>.', 'pdf', 'woocommerce-germanized-pro' ), 'https://vendidero.de/dokument/individuelle-schriftarten-in-rechnungen-nutzen' ),
				'id' 		=> 'woocommerce_gzdp_pdf_custom_fonts',
				'default'	=> '',
				'multiple'	=> true,
				'data-type' => 'application/x-font-truetype',
				'type' 		=> 'gzdp_attachment',
			),

			array(
				'title' 	=> _x( 'Default Font', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> sprintf( _x( 'Choose a default font for your PDF files. You may add custom fonts as .ttf file to your %s folder. After adding these fonts please use the recompile fonts button above.', 'pdf', 'woocommerce-germanized-pro' ), 'wp-content/uploads/wc-gzdp/fonts' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_font',
				'css' 		=> 'min-width:250px;',
				'default'	=> 'helvetica',
				'type' 		=> 'select',
				'class'		=> 'chosen_select_nostd',
				'options'	=>	WC_GZDP_PDF_Helper::instance()->get_fonts(),
				'desc_tip'	=>  true,
			),

		) );

		$layout_settings_general = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_general', array(

			array(
				'title' 	=> _x( 'Document margins', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Set the document margins (left, top, right, bottom). Bottom value will be used as page break.', 'pdf', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_margins[]',
				'type' 		=> 'gzdp_margins',
				'default'	=> array( 15, 15, 15, 50 ),
			),

			array(
				'title' 	=> _x( 'Firstpage margins ', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Optionally set specific document margins for first page only', 'pdf', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_first_page_margins[]',
				'type' 		=> 'gzdp_margins',
				'default'	=> array( 15, 15, 15, 50 ),
			),

			array(
				'title' 	=> _x( 'Custom PDF template', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip' 	=> _x( 'Choose a PDF template which will be used as background PDF template for your files.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc'		=> sprintf( _x( 'Please upload file in <a href="%s" target="_blank">DIN A4</a> format. See <a href="%s" target="_blank">example PDF</a> template for more information.', 'pdf', 'woocommerce-germanized-pro' ), 'https://de.wikipedia.org/wiki/A4-Format', 'https://vendidero.de/dokument/pdf-template-hinterlegen' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_template_attachment',
				'default'	=> '',
				'data-type' => 'application/pdf',
				'type' 		=> 'gzdp_attachment',
			),

			array(
				'title' 	=> _x( 'First page template', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip' 	=> _x( 'Select a PDF template that will be applied to the first PDF page only. If not is set, default template will be used instead.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc'		=> sprintf( _x( 'Please upload file in <a href="%s" target="_blank">DIN A4</a> format. See <a href="%s" target="_blank">example PDF</a> template for more information.', 'pdf', 'woocommerce-germanized-pro' ), 'https://de.wikipedia.org/wiki/A4-Format', 'https://vendidero.de/dokument/pdf-template-hinterlegen' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_template_attachment_first',
				'default'	=> '',
				'data-type' => 'application/pdf',
				'type' 		=> 'gzdp_attachment',
			),

		) );

		$layout_settings_templates = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_templates', array(

			array(
				'title' 	=> _x( 'Show header', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Show a default header.', 'pdf', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_show_header',
				'desc_tip'  => sprintf( _x( 'You may override this template in your theme %s', 'pdf', 'woocommerce-germanized-pro' ), '<br/>[woocommerce-germanized-pro/' . str_replace( '_', '-', $type ) . '/header.php]' ),
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> _x( 'Show footer', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Show a default footer.', 'pdf', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_show_footer',
				'desc_tip'  => sprintf( _x( 'You may override this template in your theme %s', 'pdf', 'woocommerce-germanized-pro' ), '<br/>[woocommerce-germanized-pro/' . str_replace( '_', '-', $type ) . '/footer.php]' ),
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> _x( 'Show first page header', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Show a custom first page header.', 'pdf', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_show_first_page_header',
				'desc_tip'  => sprintf( _x( 'You may override this template in your theme %s', 'pdf', 'woocommerce-germanized-pro' ), '<br/>[woocommerce-germanized-pro/' . str_replace( '_', '-', $type ) . '/header-first.php]' ),
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),

		) );

		$layout_settings_margins = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_margins', array(

			array(
				'title' 	=> _x( 'Firstpage top margin', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose if you wish to have a different margin from top for the first page only.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_' . $type .'_margin_top_first',
				'type' 		=> 'gzdp_decimal',
				'default'	=> '',
			),

		) );

		$layout_settings_page_numbers = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_page_numbers', array(

			array(
				'title' 	=> _x( 'Show page numbers', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Show page numbers at the bottom of each page.', 'pdf', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_show_page_numbers',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> _x( 'Page numbers format', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a format for displaying page numbers. Use {current} for the current page number and {total} for the number of total pages.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_' . $type . '_page_numbers_format',
				'type' 		=> 'text',
				'default'	=> sprintf( _x( 'Page %s of %s', 'pdf', 'woocommerce-germanized-pro' ), '{current}', '{total}' ),
			),

			array(
				'title' 	=> _x( 'Page numbers y-Position', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose absolute position of the page numbers from bottom of the document.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_' . $type . '_page_numbers_bottom',
				'type' 		=> 'gzdp_decimal',
				'default'	=> 45,
			),

			array(
				'title' 	=> _x( 'Y-Position First Page', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose absolute position of the page numbers for first page only.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_' . $type . '_first_page_page_numbers_bottom',
				'type' 		=> 'gzdp_decimal',
				'default'	=> 45,
			),

		) );

		$layout_settings_colors = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_colors', array(

			array(
				'title' 	=> _x( 'Default font color', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Choose a default font color for your PDF files.', 'pdf', 'woocommerce-germanized-pro' ),
				'desc_tip'	=> true,
				'id' 		=> 'woocommerce_gzdp_' . $type . '_font_color',
				'type' 		=> 'color',
				'default'	=> '#000',
			),

		) );

		$layout_settings_css = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_css', array(

			array(
				'title' 	=> _x( 'Custom CSS', 'pdf', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'You may add custom CSS Code to your PDF file to style certain options.', 'pdf', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_' . $type . '_custom_css',
				'default'	=> '',
				'desc_tip'	=> true,
				'css' 		=> 'width:70%; height: 85px;',
				'type' 		=> 'textarea',
			),

		) );

		$layout_settings_static_texts = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_static_texts', array() );

		$layout_settings_title_end = apply_filters( 'woocommerce_gzdp_' . $type . '_layout_settings_title_end', array(
			array( 'type' => 'sectionend', 'id' => 'invoice_layout_options' ),
		) );

		return array_merge( $layout_settings_title,
		$layout_settings_fonts,
		$layout_settings_general,
		$layout_settings_templates,
		$layout_settings_margins,
		$layout_settings_page_numbers,
		$layout_settings_colors,
		$layout_settings_css, 
		$layout_settings_static_texts, 
		$layout_settings_title_end );

	}

}
WC_GZDP_PDF_Helper::instance();