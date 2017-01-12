<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Legal_Page_PDF extends WC_GZDP_PDF {

	public function set_type() {
		$this->type = 'legal';
	}

	public function set_css() {
		$this->css = apply_filters( 'woocommerce_gzdp_legal_page_pdf_css', '
    		<style>
    			* {
    				color: ' . $this->object->get_color_option( 'font_color' ) . ';
    			}
 				p {
 					line-height: 1.5;
 				}
				.footer-page-numbers {
					font-size: 8pt;
				}
				.footer-custom {
					border-top: 1px solid ' . $this->object->get_color_option( 'footer_border_color' ) . ';
					font-size: 7pt;
				}
				.footer-custom table {

				}
				' . $this->object->get_option( 'custom_css' ) . '
			</style>
    	' );
	}

}