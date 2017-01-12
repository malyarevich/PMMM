<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! defined( 'K_PATH_FONTS' ) ) {
    $dir = WC_GZDP_PDF_Helper::instance()->get_custom_font_upload_dir();
    define ('K_PATH_FONTS', trailingslashit( $dir[ 'basedir' ] ) );
}

// Open Basedir Image/Cache Dir
if ( ! defined( 'K_PATH_IMAGES' ) && ini_get( 'open_basedir' ) ) {

    $tcpdf_dir = WC_germanized_pro()->plugin_path() . '/includes/libraries/tcpdf/';

    $dirs = array( $tcpdf_dir . 'examples/images/', $tcpdf_dir . 'images/', $tcpdf_dir );
    
    foreach ( $dirs as $dir ) {
        if ( @file_exists( $dir ) ) {
            define ( 'K_PATH_IMAGES', $dir );
            break;
        }
    }
}

if ( ! class_exists( 'TCPDF' ) )
	include_once( WC_germanized_pro()->plugin_path() . '/includes/libraries/tcpdf/tcpdf.php' );
if ( ! class_exists( 'FPDI' ) )
	include_once( WC_germanized_pro()->plugin_path() . '/includes/libraries/fpdi/fpdi.php' );

class WC_GZDP_PDF extends FPDI {

	public $css;
	public $type = '';
	public $object = null;
	public $pageTemplateFirst = false;
	public $pageTemplate = false;
	public $customFooter = false;
	public $customHeader = false;
	public $customHeaderFirst = false;
	public $posYtmp = false;

	public function set_type() {
		$this->type = 'default';
	}

	public function set_object( $object ) {
		$this->object = $object;
	}

	public function set_css() {
		$this->css = apply_filters( 'woocommerce_gzdp_' . $this->type . '_pdf_css', '
    		<style>
    			* {
    				color: ' . $this->object->get_color_option( 'font_color' ) . ';
    			}
 				p {
 					line-height: 1.5;
 				}
				.static {
					line-height: 1.5;
				}
				.right {
					float: right;
				}
                .footer-page-numbers {
                    font-size: 8pt;
                }
			</style>
		' );
	}

    public function AddPage( $orientation = '', $format = '', $keepmargins = false, $tocpage = false ) {
        // Manipulate autoPageBreak value before adding page for table compatibility border reasons
        $margins = $this->object->get_option( 'margins' );
        
        if ( $this->getPage() == 0 ) {
            $f_margins = $this->object->get_option( 'first_page_margins' );
            if ( isset( $f_margins[0] ) )
                $margins = $f_margins;
        }
        
        $this->SetMargins( $margins[0], $margins[1], $margins[2] );
        
        if ( isset( $margins[3] ) ) {
            $this->setAutoPageBreak( true, $margins[3] );
        }
        
        parent::AddPage( $orientation, $format, true, $tocpage );
    }

	public function Header() {
    	
    	$this->set_type();
    	$this->set_css();

    	if ( $this->getPage() == 1 && $this->pageTemplateFirst )
    		$this->setSourceFile( $this->pageTemplateFirst );
    	else if ( $this->pageTemplate )
    		$this->setSourceFile( $this->pageTemplate ); 
    	
        if ( ( $this->pageTemplateFirst && $this->getPage() == 1 ) || $this->pageTemplate ) {
			$tplidx = $this->importPage(1);
			$this->useTemplate($tplidx, 0, 0, 0, 0);
		}

		// Load Header
		if ( $this->getPage() == 1 && $this->customHeaderFirst )
			$this->writeCustomHTML( $this->customHeaderFirst, array( 'classes' => array( 'header-custom' ) ) );
		else if ( $this->customHeader )
			$this->writeCustomHTML( $this->customHeader, array( 'classes' => array( 'header-custom' ) ) );
		
		// Set Margin
		$margins = $this->object->get_option( 'margins' );
        $firstpage_margins = $this->object->get_option( 'first_page_margins' );
		$this->SetMargins( $margins[0], $margins[1], $margins[2] );

        if ( $this->getPage() == 1 && isset( $firstpage_margins[0] ) )
			$this->setMargins( $firstpage_margins[0], $firstpage_margins[1], $firstpage_margins[2] );

    }

    public function pagination_format() {
    	$default = sprintf( _x( 'Page %s of %s', 'pdf', 'woocommerce-germanized-pro' ), $this->PageNo(), $this->getAliasNbPages() );
    	
    	if ( $default = $this->object->get_option( 'page_numbers_format' ) )
    		$default = str_replace( array( '{current}', '{total}' ), array( $this->PageNo(), $this->getAliasNbPages() ), $default );
    	
    	return $default;
    }

    public function Footer() {
        // Page Numbers
        
        if ( 'yes' === $this->object->get_option( 'show_page_numbers' ) ) {

            $page_margin = $this->object->get_option( 'page_numbers_bottom' ) * -1;
            
            if ( $this->getPage() == 1 && $this->object->get_option( 'first_page_page_numbers_bottom' ) )
                $page_margin = $this->object->get_option( 'first_page_page_numbers_bottom' ) * -1;

            $this->SetY( $page_margin );
            $this->SetObjectFont();
       	    $this->writeCustomHTML( $this->pagination_format(), array( 'classes' => array( 'footer-page-numbers' ), 'align' => 'R' ) );
    	
        }

    	if ( $this->customFooter ) {

    		$this->SetY( $this->getY() + 5 );
    		$this->writeCustomHTML( $this->customFooter, array( 'classes' => array( 'footer-custom' ) ) );
            
    	}
    }

    public function setCustomHeaderFirst( $html ) {
    	$this->customHeaderFirst = $html;
    }

    public function setCustomHeader( $html ) {
    	$this->customHeader = $html;
    }

    public function setCustomFooter( $html ) {
    	$this->customFooter = $html;
    }

    public function setTemplateFirst( $tpl ) {
    	$this->pageTemplateFirst = $tpl;
    } 

    public function setTemplate( $tpl ) {
    	$this->pageTemplate = $tpl;
    } 

    public function setTmpY( $y ) {
    	$this->posYtmp = $this->getY();
    	$this->setY( $y );
    }

    public function resetTmpY() {
    	if ( $this->posYtmp )
    		$this->setY( $this->posYtmp );
    	$this->posYtmp = false;
    }

    public function MMtoPT( $mm ) {
    	return $mm * 2.833;
    }

    public function SetObjectFont() {
		$this->SetFont( $this->object->get_font(), '', $this->object->get_font_size() );
    }

    public function getPageWidthRemaining() {
    	return $this->MMtoPT( $this->getRemainingWidth() );
    }

    public function writeCustomHTML( $data, $args = array() ) {
    	$this->SetObjectFont();
    	$html = ( isset( $args[ 'before' ] ) ? $args[ 'before' ] : '<div' );
    	
    	if ( isset( $args[ 'classes' ] ) )
    		$html .= ' class="' . implode( ' ', $args[ 'classes' ] ) . '"';
    	
    	$html .= '>' . $data . ( isset( $args[ 'before' ] ) ? $args[ 'before' ] : '</div>' );
    	
    	$this->writeHTML( $this->css . $html, true, false, false, false, ( isset( $args[ 'align' ] ) ? $args[ 'align' ] : '' ) );
    }

    public function setCustomCSS( $css ) {
    	$this->css .= $css;
    }

}