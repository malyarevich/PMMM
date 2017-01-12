<?php

/**
 * @package   GFP_Limit_Date_Range
 * @copyright 2014 gravity+
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Class GFP_Limit_Date_Range_Form_Processor
 *
 * Handles form submission
 *
 * @since 1.0.0
 */
class GFP_Limit_Date_Range_Form_Processor {

	public function __construct() {

		add_filter( 'gform_validation', array( $this, 'gform_validation' ), 10, 2 );

	}

	/**
	 * Validates all datepicker date fields with a range limit
	 *
	 * @since 1.0.0
	 *
	 * @param $validation_result
	 *
	 * @return array
	 */
	public function gform_validation( $validation_result ) {

		$form = $validation_result[ 'form' ];

		$current_page = rgpost( 'gform_source_page_number_' . $form[ 'id' ] ) ? rgpost( 'gform_source_page_number_' . $form[ 'id' ] ) : 1;

		foreach ( $form[ 'fields' ] as &$field ) {

			$field_page = $field[ 'pageNumber' ];

			$hidden = RGFormsModel::is_field_hidden( $form, $field, array() );

			if ( $field_page == $current_page && ! $hidden ) {

				$is_datepicker_field = GFP_Limit_Date_Range_API::is_datepicker_date_field( $field );

				if ( ! $is_datepicker_field ) {
					continue;
				}

				$has_date_range_limit = GFP_Limit_Date_Range_API::has_date_range_limit( $field );

				if ( $is_datepicker_field && $has_date_range_limit ) {

					$field_value = rgpost( "input_{$field['id']}" );

					if ( ! empty( $field_value ) ) {

						$is_valid = $this->is_valid_date( $field_value, GFP_Limit_Date_Range_API::get_date_range_limit( $field ) );

						if ( ! $is_valid ) {

							$validation_result[ 'is_valid' ] = false;

							$field[ 'failed_validation' ]  = true;
							$field[ 'validation_message' ] = empty( $field[ 'errorMessage' ] ) ? __( 'Invalid date.', 'gfp-limit-date-range' ) : $field[ 'errorMessage' ];

						}

					}
				}
			}

		}

		$validation_result[ 'form' ] = $form;

		return $validation_result;

	}

	/**
	 * Check to see if date is within date range
	 *
	 * {@internal The time is added to the dates because DateTime uses current time if no time is set, meaning the time
	 *would be slightly different for each date, possibly causing unexpected behavior}}
	 *
	 * @since 1.0.0
	 *
	 * @param $date
	 * @param $date_range_limit
	 *
	 * @return bool
	 */
	private function is_valid_date( $date, $date_range_limit ) {

		$is_valid_date = false;
		$check_max     = true;

		$format          = GFP_Limit_Date_Range_API::get_date_format( $date_range_limit[ 'format' ] );
		$datetime_format = $this->convert_date_format_for_datetime( $format );

		$datetime = date_create_from_format( $datetime_format, $date . ' 00:00:00' );
		$date     = $datetime->format( 'Y-m-d' );

		$min_date       = rgar( $date_range_limit, 'min' );
		$min_date_limit = ! empty( $min_date );

		$max_date       = rgar( $date_range_limit, 'max' );
		$max_date_limit = ! empty( $max_date );

		if ( $min_date_limit ) {

			$min_datetime = date_create_from_format( $datetime_format, $date_range_limit[ 'min' ] . ' 00:00:00' );
			$min_date     = $min_datetime->format( 'Y-m-d' );

			if ( $date >= $min_date ) {
				$is_valid_date = true;
			} else {
				$check_max = false;
			}

		}

		if ( $check_max && $max_date_limit ) {

			$max_datetime = date_create_from_format( $datetime_format, $date_range_limit[ 'max' ] . ' 00:00:00' );
			$max_date     = $max_datetime->format( 'Y-m-d' );

			if ( $date <= $max_date ) {
				$is_valid_date = true;
			} else {
				$is_valid_date = false;
			}

		}

		return $is_valid_date;
	}

	/**
	 * Convert date format into required format for DateTime objects
	 *
	 * @since 1.0.0
	 *
	 * @param $format_string
	 *
	 * @return string
	 */
	private function convert_date_format_for_datetime( $format_string ) {

		$parsed_format = preg_split( '/([\\.\\/\\-])/', $format_string, null, PREG_SPLIT_DELIM_CAPTURE );

		$datetime_format = '';

		$delimiter = $parsed_format[ 1 ];

		foreach ( $parsed_format as $index => $format_var ) {

			$var = substr( $parsed_format[ $index ], 0, 1 );

			if ( $delimiter !== $var ) {

				if ( 'y' == $var ) {

					$var = 'Y';

				}

				$datetime_format .= $var;

				if ( count( $parsed_format ) - 1 > $index ) {

					$datetime_format .= $delimiter;

				}

			}

		}

		$datetime_format .= ' H:i:s';

		return $datetime_format;

	}
}