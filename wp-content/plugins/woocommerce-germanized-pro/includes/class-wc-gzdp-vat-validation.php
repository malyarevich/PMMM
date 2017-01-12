<?php

class WC_GZDP_VAT_Validation {
	
	private $api_url = "http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl";
	private $client = null;
	private $options  = array( 'debug' => false );	
	
	private $valid = false;
	private $data = array();
	
	public function __construct( $options = array() ) {
		
		foreach( $options as $option => $value )
			$this->options[ $option ] = $value;
		
		if ( ! class_exists( 'SoapClient' ) )
			wp_die( __( 'SoapClient is required to enable VAT validation', 'woocommerce-germanized-pro' ) );
				
		try {
			$this->client = new SoapClient( $this->api_url, array( 'trace' => true ) );
		} catch( Exception $e ) {
			$this->valid = false;
		}

	}

	public function check( $country, $nr ) {

		$rs = null;

		try {

			$rs = $this->client->checkVat( array( 'countryCode' => $country, 'vatNumber' => $nr ) );

			if( $rs->valid ) {
				$this->valid = true;
				$this->data = array(
					'name' 		   => $this->parse_string( $rs->name ), 
					'address'      => $this->parse_string( $rs->address ),
				);
				return true;
			} else {
				$this->valid = false;
				$this->data = array();
			    return false;
			}

		} catch( SoapFault $e ) {
       		$this->valid = false;
       		$this->data = array();
       		return false;
    	}

	}

	public function is_valid() {
		return $this->valid;
	}
	
	public function get_name() {
		return $this->data[ 'name' ];
	}
	
	public function get_address() {
		return $this->data[ 'address' ];
	}
	
	public function is_debug() {
		return ( $this->options[ 'debug' ] === true );
	}

	private function parse_string( $string ) {
    	return ( $string != "---" ? $string : false );
	}
}

?>