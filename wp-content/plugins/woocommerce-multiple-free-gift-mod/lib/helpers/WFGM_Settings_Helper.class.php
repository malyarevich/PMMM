 <?php
/**
 * Get all user settings and cache it for future use.
 *
 * @package  woocommerce-multiple-free-gift-mod
 * @subpackage lib
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 * @static
 */
class WFGM_Settings_Helper
{
	/* Option key prefix */
	const PREFIX = '_wfgm_';

	/* Flag to check if the setting is already fetched */
	private static $__initialized = false;

	/* Hold all settings */
	protected static $_settings = array();

	/**
	 * Prevent the instantiation of class using 
	 * private constructor
	 */
	private function __construct() {}

	/**
	 * Fetch settings from database if not already fetched.
	 *
	 * @access protected
	 * @static
	 * @see  get_option()
	 * 
	 * @return void
	 */
	protected static function __init()
	{
		if( self::$__initialized ) {
			return;
		}

		//fetch settings
		$settings['global_settings'] = get_option( self::PREFIX . 'global_settings' );
		$settings['global_options'][ self::PREFIX . 'global_enabled' ] = get_option( self::PREFIX . 'global_enabled' );
		$settings['global_options'][ self::PREFIX . 'popup_overlay' ] = get_option( self::PREFIX . 'popup_overlay' );

        $settings['global_options'][ self::PREFIX . 'so_product_page' ] = get_option( self::PREFIX . 'so_product_page' );
        $settings['global_options'][ self::PREFIX . 'so_add_more' ] = get_option( self::PREFIX . 'so_add_more' );
        $settings['global_options'][ self::PREFIX . 'so_congrat' ] = get_option( self::PREFIX . 'so_congrat' );
        $settings['global_options'][ self::PREFIX . 'so_congrat_save_money' ] = get_option( self::PREFIX . 'so_congrat_save_money' );
        $settings['global_options'][ self::PREFIX . 'so_deleted_gift' ] = get_option( self::PREFIX . 'so_deleted_gift' );

        $settings['global_options'][ self::PREFIX . 'so_product_page_enabled' ] = get_option( self::PREFIX . 'so_product_page_enabled' );
        $settings['global_options'][ self::PREFIX . 'so_add_more_enabled' ] = get_option( self::PREFIX . 'so_add_more_enabled' );
        $settings['global_options'][ self::PREFIX . 'so_congrat_enabled' ] = get_option( self::PREFIX . 'so_congrat_enabled' );
        $settings['global_options'][ self::PREFIX . 'so_congrat_save_money_enabled' ] = get_option( self::PREFIX . 'so_congrat_save_money_enabled' );
        $settings['global_options'][ self::PREFIX . 'so_deleted_gift_enabled' ] = get_option( self::PREFIX . 'so_deleted_gift_enabled' );

        $settings['global_options'][ self::PREFIX . 'popup_heading' ] = get_option( self::PREFIX . 'popup_heading' );
        $settings['global_options'][ self::PREFIX . 'popup_heading_msg' ] = get_option( self::PREFIX . 'popup_heading_msg' );
		$settings['global_options'][ self::PREFIX . 'invalid_condition_text' ] = get_option( self::PREFIX . 'invalid_condition_text' );
		$settings['global_options'][ self::PREFIX . 'popup_add_gift_text' ] = get_option( self::PREFIX . 'popup_add_gift_text' );
		$settings['global_options'][ self::PREFIX . 'popup_cancel_text' ] = get_option( self::PREFIX . 'popup_cancel_text' );
		$settings['global_options'][ self::PREFIX . 'ok_text' ] = get_option( self::PREFIX . 'ok_text' );
		$settings['global_options'][ self::PREFIX . 'type_text' ] = get_option( self::PREFIX . 'type_text' );
		$settings['global_options'][ self::PREFIX . 'free_item_text' ] = get_option( self::PREFIX . 'free_item_text' );
		$settings['criteria'] = get_option( self::PREFIX . 'criteria' );

		if( ! empty($settings) ) {
			self::$_settings = $settings;
		}

		self::$__initialized = true;
	}

	/**
	 * Forcefully reinitialze settings
	 *
	 * @access public
	 * @static
	 * 
	 * @return void
	 */
	public static function force_init()
	{
		self::$__initialized = false;
	}

	/**
	 * Check if setting is available.
	 *
	 * @access public
	 * @static
	 * 
	 * @return boolean
	 */
	public static function has_settings()
	{
		self::__init();
		return ! empty( self::$_settings );
	}

	/**
	 * Check settings array and return setting if available.
	 *
	 * @access public
	 * @static
	 * 
	 * @return string|boolean|object
	 */
	public static function get( $key, $bool = false, $type = 'global_settings', $prefix = true )
	{
		self::__init();

		if( $prefix ) {
			$key = self::PREFIX . $key;
		}

		if( empty($key) && isset(self::$_settings[ $type ]) ) {
			return self::$_settings[ $type ];
		}

		if( isset(self::$_settings[ $type ][ $key ]) ) {
			return $bool ? (bool) self::$_settings[ $type ][ $key ] : self::$_settings[ $type ][ $key ];
		}

		return false;
	}
	
}
