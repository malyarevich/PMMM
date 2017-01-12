<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_GZDP_Settings {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_filter( 'woocommerce_germanized_settings', array( $this, 'register_settings' ), 0, 1 );
		add_action( 'woocommerce_gzd_after_save_section_', array( $this, 'after_save' ), 0, 1 );
	}

	public function register_settings( $settings ) {

		$remove = array( 'contract_options', 'vat_options', 'invoice_options', 'email_attachment_options' );
		
		foreach ( $remove as $rm )
			$settings = $this->remove_setting_area( $settings, $rm );

		$gzdp_contract_settings = array(

			array( 'title' => __( 'Contract', 'woocommerce-germanized-pro' ), 'type' => 'title', 'id' => 'contract_options' ),

			array(
				'title' 	=> __( 'Conclusion of Contract', 'woocommerce-germanized-pro' ),
				'desc'	    => __( 'Manually review and confirm an order before closing contract.', 'woocommerce-germanized-pro' ),
				'desc_tip' 	=> __( 'By default WooCommerce only allows closing contracts right after the order submit button has been clicked. This will trigger an email which is by default used to close a contract with the customer. You may want to close a contract only after you have manually reviewed the order. If you set this option, the first email after checkout does only confirm the incoming offer. Order status is set to on-hold and no payment information will be given. Now you have time to review the order within the backend and then click the confirm button (which will confirm the contract to the customer).', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_contract_after_confirmation',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),

			array( 'type' => 'sectionend', 'id' => 'contract_options' ),

		);

		$settings = $this->insert_after( $settings, "general_options", $gzdp_contract_settings, 'sectionend' );

		$gzdp_settings = array(

			array(	'title' => _x( 'Invoices', 'invoices', 'woocommerce-germanized-pro' ), 'type' => 'title', 'id' => 'invoice_options' ),

			array(
				'title' 	=> _x( 'Enable PDF invoices', 'invoices', 'woocommerce-germanized-pro' ),
				'desc' 		=> _x( 'Generate PDF invoices for orders', 'invoices', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_invoice_enable',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array( 'type' => 'sectionend', 'id' => 'invoice_options' ),

			array(	'title' => __( 'VAT', 'woocommerce-germanized-pro' ), 'type' => 'title', 'id' => 'vat_options' ),

			array(
				'title' 	=> __( 'VAT Check', 'woocommerce-germanized-pro' ),
				'desc' 		=> __( 'Remove VAT for business owners owning a valid VAT ID.', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_enable_vat_check',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'desc_tip'	=> sprintf( __( 'This will add a new field within your checkout (vat number). Customers from other EU states owning a valid VAT ID will be able to remove VAT. VAT ID is being validated through the European Union VAT service API. More information can be found <a href="%s" target="_blank">here</a>.', 'woocommerce-germanized-pro' ), 'http://ec.europa.eu/taxation_customs/vies/viesdisc.do' ),
			),

			array(
				'title' 	=> __( 'VAT ID Cache', 'woocommerce-germanized-pro' ),
				'desc' 		=> __( 'days', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_vat_check_cache',
				'default'	=> 7,
				'type' 		=> 'number',
				'desc_tip'  => __( 'Enable positive API reponse cache to ensure that validated VAT IDs won\'t get checked on every request (leads to better performance). Leave empty to disable caching.', 'woocommerce-germanized-pro' )
			),

			array(
				'title' 	=> __( 'VAT Check Login', 'woocommerce-germanized-pro' ),
				'desc' 		=> __( 'Remove VAT for users with valid VAT ID after login.', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_enable_vat_check_login',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'desc_tip'	=> __( 'This option will remove vat for customers with valid VAT ID directly after login. VAT ID for customers will be populated during checkout.', 'woocommerce-germanized-pro' ),
			),

			array(
				'title' 	=> __( 'Virtual Products B2B', 'woocommerce-germanized-pro' ),
				'desc' 		=> __( 'Do only sell virtual products to EU customers if they own a valid VAT ID.', 'woocommerce-germanized-pro' ),
				'id' 		=> 'woocommerce_gzdp_force_virtual_product_business',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'desc_tip'	=> __( 'This option will help you to stop selling virtual products to private customers from EU. You might want to choose this option to avoid administrative barriers regarding VAT calculation.', 'woocommerce-germanized-pro' ),
			),

			array( 'type' => 'sectionend', 'id' => 'vat_options' ),

		);
		
		$settings = $this->insert_after( $settings, "virtual_vat_options", $gzdp_settings, 'sectionend' );

		return $settings;
	}

	public function remove_setting_area( $settings, $remove ) {
		$start = false;
		foreach ( $settings as $key => $val ) {
			if ( isset( $val[ 'type' ] ) && $val[ 'type' ] == 'title' && $val[ 'id' ] == $remove )
				$start = true;
			else if ( isset( $val[ 'type' ] ) && $val[ 'type' ] == 'sectionend' && $val[ 'id' ] == $remove )
				$start = false;
			if ( $start )
				unset( $settings[ $key ] );
		}
		return $settings;
	}

	public function insert_after( $settings, $id, $insert = array(), $type = '' ) {
		if ( $key = $this->get_key_by_id( $settings, $id, $type ) ) {
			$key++;
			$settings = array_merge( array_merge( array_slice( $settings, 0, $key, true ), $insert ), array_slice( $settings, $key, count( $settings ) - 1, true ) );
		} else {
			$settings += $insert;
		}
		return $settings;
	}

	private function get_key_by_id( $settings, $id, $type = '' ) {
		if ( ! empty( $settings ) ) {
			
			foreach ( $settings as $key => $value ) {
				
				if ( isset( $value[ 'id' ] ) && $value[ 'id' ] == $id ) {
					
					if ( ! empty( $type ) && $type !== $value[ 'type' ] )
						continue;
					
					return $key;
				}
			}
		}
		return false;
	}

	public function remove_setting( $settings, $id ) {

		foreach ( $settings as $key => $value ) {
			if ( isset( $value[ 'id' ] ) && $id === $value[ 'id' ] )
				unset( $settings[ $key ] );
		}

		return array_filter( $settings );

	}
 
	public function after_save( $settings ) {
		// Check if SoapClient Class exists
		if ( isset( $_POST[ 'woocommerce_gzdp_enable_vat_check' ] ) && ! empty( $_POST[ 'woocommerce_gzdp_enable_vat_check' ] ) && ! class_exists( 'SoapClient' ) ) {
			$this->add_notice( __( 'To enable VAT check PHP 5.0.1 has to be installed (SoapClient is needed)', 'woocommerce-germanized-pro' ) );
			update_option( 'woocommerce_gzdp_enable_vat_check', 'no' );
		}
	}

	public function add_notice( $message, $type = 'error' ) {
		?>
		<div class="notice notice-<?php echo $type;?>">
			<p><?php echo $message; ?></p>
		</div>
		<?php
	}

}

return WC_GZDP_Settings::instance();