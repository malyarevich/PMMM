<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_GZDP_Install' ) ) :

/**
 * Installation related functions and hooks
 *
 * @class 		WC_GZD_Install
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_GZDP_Install {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		register_activation_hook( WC_GERMANIZED_PRO_PLUGIN_FILE, array( $this, 'init_install' ) );
		add_action( 'admin_init', array( $this, 'install' ), 0 );
		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
		add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
	}

	public function welcome_notice() {
		if ( ! get_option( '_wc_gzdp_welcome_notice' ) )
			return;
		
		include_once( WC_germanized_pro()->plugin_path() . '/includes/admin/views/html-welcome.php' );
		delete_option( '_wc_gzdp_welcome_notice' );
	}

	/**
	 * check_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'woocommerce_gzdp_version' ) != WC_germanized_pro()->version || get_option( 'woocommerce_gzdp_db_version' ) != WC_germanized_pro()->version ) ) {
			$this->init_install();
			$this->install();
			do_action( 'woocommerce_gzdp_updated' );
		}
	}

	public function init_install() {
		update_option( '_wc_gzdp_do_install', true );
	}

	/**
	 * Install WC_Germanized
	 */
	public function install() {

		if ( ! get_option( '_wc_gzdp_do_install' ) )
			return;

		$this->create_options();

		WC_germanized_pro()->create_upload_folder();
		
		// Queue upgrades
		$current_version    = get_option( 'woocommerce_gzdp_version', null );
		$current_db_version = get_option( 'woocommerce_gzdp_db_version', null );

		if ( ! $current_version )
			update_option( '_wc_gzdp_welcome_notice', 1 );
		else
			$this->update();

		update_option( 'woocommerce_gzdp_db_version', WC_germanized_pro()->version );

		// Update version
		update_option( 'woocommerce_gzdp_version', WC_germanized_pro()->version );

		// Update activation date
		update_option( 'woocommerce_gzdp_activation_date', date( 'Y-m-d' ) );

		// Unregister installation
		delete_option( '_wc_gzdp_do_install' );
	}

	/**
	 * Handle updates
	 */
	public function update() {
		
		$current_db_version = get_option( 'woocommerce_gzdp_db_version' );

		if ( version_compare( $current_db_version, '1.2.0', '<' ) )
			$this->upgrade_invoice_path();
		elseif ( version_compare( $current_db_version, '1.4.0', '<' ) )
			$this->upgrade_pdf_options();
		elseif ( version_compare( $current_db_version, '1.4.3', '<' ) )
			$this->upgrade_1_4_2();

	}

	public function upgrade_1_4_2() {

		$options = array(
			'margins' => 'first_page_margins',
			'page_numbers_bottom' => 'first_page_page_numbers_bottom',
		);

		$types = array( 'invoice', 'legal_page' );

		foreach ( $options as $org => $option ) {

			foreach ( $types as $type ) {

				// Set Bottom Margin
				if ( $org === 'margins' ) {
					$margins = get_option( 'woocommerce_gzdp_' . $type . '_' . $org );
					if ( ! is_array( $margins ) )
						$margins = array( 15, 15, 15 );
					$margins[3] = 25;

					update_option( 'woocommerce_gzdp_' . $type . '_' . $org, $margins );
				}

				update_option( 'woocommerce_gzdp_' . $type . '_' . $option, get_option( 'woocommerce_gzdp_' . $type . '_' . $org ) );
			}

		}

		$invoices = array(
			'invoice' => _x( 'Invoice', 'invoices', 'woocommerce-germanized-pro' ),
			'invoice_cancellation' => _x( 'Cancellation', 'invoices', 'woocommerce-germanized-pro' ),
		);

		foreach ( $invoices as $invoice_type => $title ) {

			// Invoice email heading
			$invoice_settings = get_option( 'woocommerce_customer_' . $invoice_type . '_settings' );
			
			if ( $invoice_settings && is_array( $invoice_settings ) ) {

				$types = array( 'subject', 'heading' );

				foreach ( $types as $type ) {
					
					if ( isset( $invoice_settings[ $type ] ) ) {
						
						$invoice_settings[ $type ] = str_replace( $title . ' {invoice_number}', '{invoice_number}', $invoice_settings[ $type ] );
						
						if ( $invoice_type == 'invoice_cancellation' ) {
							$invoice_settings[ $type ] = str_replace( 'zur Rechnung {invoice_number_parent}', 'zu {invoice_number_parent}', $invoice_settings[ $type ] );
						}

					}
				}

				update_option( 'woocommerce_customer_' . $invoice_type . '_settings', $invoice_settings );

	 		}

 		}

	}

	public function upgrade_pdf_options() {

		$rename = array(
			'woocommerce_gzdp_invoice_custom_font_names' => 'woocommerce_gzdp_pdf_custom_font_names',
			'woocommerce_gzdp_invoice_custom_fonts' => 'woocommerce_gzdp_invoice_custom_fonts',
			'woocommerce_gzdp_invoice_text_cancellation_after_table' => 'woocommerce_gzdp_invoice_cancellation_text_after_table',
			'woocommerce_gzdp_invoice_text_cancellation_before_table' => 'woocommerce_gzdp_invoice_cancellation_text_before_table',
			'woocommerce_gzdp_invoice_text_packing_slip_after_table' => 'woocommerce_gzdp_invoice_packing_slip_text_after_table',
			'woocommerce_gzdp_invoice_text_packing_slip_before_table' => 'woocommerce_gzdp_invoice_packing_slip_text_before_table',
		);

		foreach( $rename as $old => $new ) {
			if ( get_option( $old ) )
				update_option( $new, get_option( $old ) );
			delete_option( $old );
		}

	}

	public function upgrade_invoice_path() {
		
		// Go through invoices
		$invoices = get_posts( array( 'post_type' => 'invoice', 'posts_per_page' => -1, 'post_status' => 'any' ) );
		
		if ( ! empty( $invoices ) ) {
		
			foreach ( $invoices as $invoice ) {
				
				if ( $attachment = get_post_meta( $invoice->ID, '_invoice_attachment', true ) ) {

					$file = get_attached_file( $attachment );

					if ( $file ) {

						$upload_dir = WC_germanized_pro()->get_upload_dir();
						
						WC_germanized_pro()->set_upload_dir_filter();
						$path = str_replace( array( WC_germanized_pro()->plugin_path() . '/uploads', $upload_dir[ 'basedir' ] ), '', get_attached_file( $attachment ) );
						WC_germanized_pro()->unset_upload_dir_filter();
						
						$path = ltrim( $path, '/' );

						update_post_meta( $attachment, '_wp_attached_file', $path );
					}

				}

			}

		}

	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 *
	 * @access public
	 */
	function create_options() {

		include_once( WC()->plugin_path() . '/includes/admin/settings/class-wc-settings-page.php' );
		include_once( WC_germanized()->plugin_path() . '/includes/admin/settings/class-wc-gzd-settings-germanized.php' );
		
		$settings = new WC_GZD_Settings_Germanized();

		$options = $settings->get_settings();

		if ( get_option( 'woocommerce_gzdp_invoice_enable' ) != 'no' ) {
		
			$invoices = WC_GZDP_Invoice_Helper::instance();
			$options = array_merge( $options, $invoices->get_settings() );

		}

		// PDF Options
		$pdf = WC_GZDP_Legal_Page_Helper::instance();
		$options = array_merge( $options, $pdf->get_settings() );

		foreach ( $options as $value ) {
			if ( isset( $value[ 'id' ] ) && strpos( $value[ 'id' ], 'gzdp' ) !== false && isset( $value[ 'default' ] ) ) {
				$autoload = isset( $value[ 'autoload' ] ) ? (bool) $value[ 'autoload' ] : true;
				add_option( str_replace( '[]', '', $value[ 'id' ] ), $value[ 'default' ], '', ( $autoload ? 'yes' : 'no' ) );
			}
		}

	}

}

endif;

return new WC_GZDP_Install();
