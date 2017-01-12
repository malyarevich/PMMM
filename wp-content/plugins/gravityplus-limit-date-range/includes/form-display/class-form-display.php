<?php

/**
 * @package   GFP_Limit_Date_Range
 * @copyright 2014 gravity+
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Class GFP_Limit_Date_Range_Form_Display
 *
 * Displays limited date ranges
 *
 * @since 1.0.0
 */
class GFP_Limit_Date_Range_Form_Display {

	/**
	 * Date range limit for field
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $date_range_limit = array();

	public function __construct() {

		add_action( 'gform_enqueue_scripts', array( $this, 'gform_enqueue_scripts' ), 10, 2 );
		add_filter( 'gform_date_min_year', array( $this, 'gform_date_min_year' ), 10, 3 );
		add_filter( 'gform_date_max_year', array( $this, 'gform_date_max_year' ), 10, 3 );
	}

	/**
	 * Add JS to limit date range for datepicker fields
	 *
	 * @since 1.0.0
	 *
	 * @param null $form
	 * @param null $ajax
	 */
	public function gform_enqueue_scripts( $form = null, $ajax = null ) {

		if ( ! $form == null ) {

			if ( GFP_Limit_Date_Range_API::has_datepicker_field_with_limit( $form ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 'gfp_limit_date_range_form_display', trailingslashit( GFP_LIMIT_DATE_RANGE_URL ) . "includes/form-display/js/datepicker{$suffix}.js", array(
					'jquery',
					'gform_datepicker_init'
				) );
				wp_localize_script( 'gfp_limit_date_range_form_display', 'gfp_limit_date_range_vars', array( 'fields' => GFP_Limit_Date_Range_API::get_date_fields_with_date_range_limit( $form, 'datepicker' ) ) );
			}
		}
	}

	/**
	 * Get date range limit for date dropdown field
	 *
	 * @since 2.1.0
	 *
	 * @param $field
	 */
	private function get_date_range_limit( $field ) {

		if ( empty( $this->date_range_limit[ $field[ 'id' ] ] ) ) {

			if ( GFP_Limit_Date_Range_API::is_datedropdown_date_field( $field ) && GFP_Limit_Date_Range_API::has_date_range_limit( $field ) ) {

				$this->date_range_limit[ $field[ 'id' ] ] = GFP_Limit_Date_Range_API::get_date_range_limit( $field );

			}
		}

	}

	/**
	 * Return minimum year if it exists
	 *
	 * @since 2.0.0
	 *
	 * @param string        $min_year
	 * @param array         $form
	 * @param GF_Field_Date $field
	 *
	 * @return string
	 */
	public function gform_date_min_year( $min_year, $form, $field ) {

		$this->get_date_range_limit( $field );

		if ( ! empty( $this->date_range_limit[ $field[ 'id' ] ][ 'min' ] ) ) {
			$min_year = $this->date_range_limit[ $field[ 'id' ] ][ 'min' ];
		}

		return $min_year;
	}

	/**
	 * Return maximum year if it exists
	 *
	 * @since 2.0.0
	 *
	 * @param string        $max_year
	 * @param array         $form
	 * @param GF_Field_Date $field
	 *
	 * @return string
	 */
	public function gform_date_max_year( $max_year, $form, $field ) {

		$this->get_date_range_limit( $field );

		if ( ! empty( $this->date_range_limit[ $field[ 'id' ] ][ 'max' ] ) ) {
			$max_year = $this->date_range_limit[ $field[ 'id' ] ][ 'max' ];
		}

		return $max_year;
	}

}