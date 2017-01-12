<?php

/**
 * @package   GFP_Limit_Date_Range
 * @copyright 2014 gravity+
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Class GFP_Limit_Date_Range_Form_Editor
 *
 * Adds limit date range option to date field
 *
 * @since 1.0.0
 */
class GFP_Limit_Date_Range_Form_Editor {

	public function __construct () {
		add_action( 'admin_init', array( $this, 'admin_init' ), 10, 2 );
	}

	public function admin_init () {
		if ( 'gf_edit_forms' == RGForms::get( 'page' ) ) {
			add_action( 'gform_field_standard_settings', array( $this, 'gform_field_standard_settings' ), 10, 2 );
			add_action( 'gform_editor_js', array( $this, 'gform_editor_js' ) );
			add_filter( 'gform_noconflict_scripts', array( $this, 'gform_noconflict_scripts' ) );
		}
	}

	public function gform_field_standard_settings ( $position, $form_id ) {
		if ( 1250 == $position ) {
			require_once( trailingslashit( GFP_LIMIT_DATE_RANGE_PATH ) . 'includes/form-editor/views/field-setting-limit_date_range.php' );
		}
	}

	public function gform_editor_js () {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'gfp_limit_date_range_form_editor', trailingslashit( GFP_LIMIT_DATE_RANGE_URL ) . "includes/form-editor/js/limit-date-settings{$suffix}.js", array( 'gform_form_editor', 'jquery-ui-datepicker' ) );
	}

	public static function gform_noconflict_scripts ( $noconflict_scripts ) {
		$noconflict_scripts = array_merge( $noconflict_scripts, array( 'gfp_limit_date_range_form_editor' ) );

		return $noconflict_scripts;
	}

	public static function gform_tooltips ( $tooltips ) {
		$limit_date_range_tooltips = array(
			'form_field_limit_date_range' => '<h6>' . __( 'Limit Date Range', 'gfp-limit-date-range' ) . '</h6>' . __( 'Set a minimum and maximum date that can be selected', 'gfp-limit-date-range' ),
		);

		return array_merge( $tooltips, $limit_date_range_tooltips );
	}
}