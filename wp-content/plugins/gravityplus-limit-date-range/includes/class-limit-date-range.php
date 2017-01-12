<?php
/**
 * @package   GFP_Limit_Date_Range
 * @copyright 2014 gravity+
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Main Class
 *
 * Loads everything
 *
 * @since 1.0.0
 */
class GFP_Limit_Date_Range {

	public function __construct () {

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

	}

	public function plugins_loaded () {

		if ( class_exists( 'GFForms' ) ) {

			$this->load_textdomain();

			require_once( trailingslashit( GFP_LIMIT_DATE_RANGE_PATH ) . 'includes/form-editor/class-form-editor.php' );
			require_once( trailingslashit( GFP_LIMIT_DATE_RANGE_PATH ) . 'includes/form-display/class-form-display.php' );
			require_once( trailingslashit( GFP_LIMIT_DATE_RANGE_PATH ) . 'includes/form-processor/class-form-processor.php' );
			require_once( trailingslashit( GFP_LIMIT_DATE_RANGE_PATH ) . 'includes/api/class-api.php' );

			new GFP_Limit_Date_Range_Form_Editor();
			new GFP_Limit_Date_Range_Form_Display();
			new GFP_Limit_Date_Range_Form_Processor();

		}
	}

	public function load_textdomain () {

		$gfp_limit_date_range_lang_dir = dirname( plugin_basename( GFP_LIMIT_DATE_RANGE_FILE ) ) . '/languages/';
		$gfp_limit_date_range_lang_dir = apply_filters( 'gfp_limit_date_range_language_dir', $gfp_limit_date_range_lang_dir );

		$locale = apply_filters( 'plugin_locale', get_locale(), 'gfp-limit-date-range' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'gfp-limit-date-range', $locale );

		$mofile_local  = $gfp_limit_date_range_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/gfp-limit-date-range/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			load_textdomain( 'gfp-limit-date-range', $mofile_global );
		}
		elseif ( file_exists( $mofile_local ) ) {
			load_textdomain( 'gfp-limit-date-range', $mofile_local );
		}
		else {
			load_plugin_textdomain( 'gfp-limit-date-range', false, $gfp_limit_date_range_lang_dir );
		}

	}

}