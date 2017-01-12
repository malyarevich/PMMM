<?php
/**
 * Common Helper class: Contain globally required modules
 *
 * @static
 * @package  woocommerce-multiple-free-gift-mod
 * @subpackage lib/helpers
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 */
class WFGM_Common_Helper
{
	/** Current version of the plugin */
	const VERSION = '0.0.0';

	/** @var string Plugin text domain */
	public static $textDomain = 'woocommerce-multiple-free-gift-mod';

	/**
	 * Localize text strings
	 *
	 * @since  0.0.0
	 * @see  __()
	 * 
	 * @return string
	 */
	public static function translate( $string )
	{
		return __( $string, self::$textDomain );
	}

	/**
	 * Displays error message with WordPress default theme.
	 *
	 * @since  0.0.0
	 * 
	 * @param  string $message Message to display
	 * @return void
	 */
	public static function error_notice( $message )
	{
		echo '<div class="error wfgm-error">';
		echo '<p>' . $message . '</p>';
		echo '</div>';
	}

	/**
	 * Displays success message with WordPress default theme.
	 *
	 * @since  0.0.0
	 * 
	 * @param  string $message Message to display
	 * @return void
	 */
	public static function success_notice( $message )
	{
		echo '<div class="updated wfgm-updated">';
		echo '<p>' . $message . '</p>';
		echo '</div>';
	}

	/**
	 * Displays fixed notice at the top of screen in frontend.
	 *
	 * @since  0.0.0
	 * 
	 * @param  string $message Message to display
	 * @return void
	 */
	public static function fixed_notice( $message )
	{
		echo '<div class="wfgm-fixed-notice">';
		echo '<p>' . $message . '<a class="wfgm-fixed-notice-remove" href="javascript:void(0)">x</a></p>';
		echo '</div>';
	}

}
