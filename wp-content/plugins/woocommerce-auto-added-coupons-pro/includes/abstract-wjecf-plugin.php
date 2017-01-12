<?php

abstract class Abstract_WJECF_Plugin {

//Override these functions in the WJECF plugin

    public function init_hook() {}
    public function init_admin_hook() {}

//

	/**
	 * Log a message (for debugging)
	 *
	 * @param string $message The message to log
	 *
	 */
	protected function log ( $message ) {
		WJECF()->log( $message, 1 );
	}

	private $plugin_data = array();

	/**
	 *  Information about the WJECF plugin
	 * @param string|null $key The data to look up. Will return an array with all data when omitted
	 * @return mixed
	 */
	protected function get_plugin_data( $key = null ) {
		$default_data = array(
			'description' => '',
			'can_be_disabled' => false,
			'dependencies' => array()
		);
		$plugin_data = array_merge( $default_data, $this->plugin_data );
		if ( $key === null ) { 
			return $plugin_data;
		}
		return $plugin_data[$key];
	}

	/**
	 *  Set information about the WJECF plugin
	 * @param array $plugin_data The data for this plugin
	 * @return void
	 */
	protected function set_plugin_data( $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

    /**
     *  Get the description if this WJECF plugin.
     * @return string
     */
	public function get_plugin_description() {
		return $this->get_plugin_data( 'description' );
	}

    /**
     *  Get the class name of this WJECF plugin.
     * @return string
     */
	public function get_plugin_class_name() {
		return get_class( $this );
	}

    public function get_plugin_dependencies() {
        return $this->get_plugin_data( 'dependencies' );
    }    

    public function plugin_is_enabled() {
        return true;
    }

}