<?php

/**
 * @package   GFP_Limit_Date_Range
 * @copyright 2014 gravity+
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Class GFP_Limit_Date_Range_API
 *
 * Provides public interface for interacting with date fields with limited date range
 *
 * @since 1.0.0
 */
class GFP_Limit_Date_Range_API {

	public static function has_datepicker_field_with_limit ( $form ) {
		$has_datepicker_field_with_limit = false;

		if ( is_array( $form[ 'fields' ] ) ) {
			foreach ( $form[ 'fields' ] as $field ) {
				if ( self::is_datepicker_date_field( $field ) && self::has_date_range_limit( $field ) ) {
					$has_datepicker_field_with_limit = true;
					break;
				}
			}
		}

		return $has_datepicker_field_with_limit;
	}

	public static function get_date_fields_with_date_range_limit ( $form, $type, $only_return_type = true ) {
		$fields = array();

		foreach ( $form[ 'fields' ] as $field ) {
			if ( $only_return_type && 'datepicker' == $type ) {
				if ( self::is_datepicker_date_field( $field ) && self::has_date_range_limit( $field ) ) {
					$fields[ $field[ 'id' ] ] = self::get_date_range_limit( $field );
				}
			}
			else if ( $only_return_type && 'datedropdown' == $type ) {
				if ( self::is_datedropdown_date_field( $field ) && self::has_date_range_limit( $field ) ) {
					$fields[ $field[ 'id' ] ] = self::get_date_range_limit( $field );
				}
			}
			else {
				if ( ( self::is_datepicker_date_field( $field ) || self::is_datedropdown_date_field( $field ) ) && self::has_date_range_limit( $field ) ) {
					$fields[ $field[ 'id' ] ] = self::get_date_range_limit( $field );
				}
			}
		}

		return $fields;

	}

	public static function has_date_range_limit ( $field ) {
		$has_date_range_limit = false;

		if ( ( ! empty( $field[ 'limitDateRange' ] ) && $field[ 'limitDateRange' ] ) && ( ! empty( $field[ 'limitDateRangeMinDate' ] ) || ! empty( $field[ 'limitDateRangeMaxDate' ] ) ) ) {
			$has_date_range_limit = true;
		}

		return $has_date_range_limit;
	}

	public static function get_date_range_limit ( $field ) {
		$limit = array();

		if ( self::has_date_range_limit( $field ) ) {
			$limit = array(
				'format' => empty( $field[ 'dateFormat' ] ) ? 'mdy' : $field[ 'dateFormat' ],
				'min'    => rgar( $field, 'limitDateRangeMinDate' ),
				'max'    => rgar( $field, 'limitDateRangeMaxDate' ) );
		}

		return $limit;
	}

	public static function is_datepicker_date_field ( $field ) {
		$is_datepicker_date_field = false;

		if ( 'date' == RGFormsModel::get_input_type( $field ) && 'datepicker' == rgar( $field, 'dateType' ) ) {
			$is_datepicker_date_field = true;
		}

		return $is_datepicker_date_field;
	}

	public static function is_datedropdown_date_field ( $field ) {
		$is_datedropdown_date_field = false;

		if ( 'date' == RGFormsModel::get_input_type( $field ) && 'datedropdown' == rgar( $field, 'dateType' ) ) {
			$is_datedropdown_date_field = true;
		}

		return $is_datedropdown_date_field;
	}

	public static function get_date_format ( $format_class ) {
		$format = 'mm/dd/yy';

		switch ( $format_class ) {
			case 'mdy':
				$format = 'mm/dd/yy';
				break;
			case 'dmy':
				$format = 'dd/mm/yy';
				break;
			case 'dmy_dash':
				$format = 'dd-mm-yy';
				break;
			case 'dmy_dot':
				$format = 'dd.mm.yy';
				break;
			case 'ymd_slash':
				$format = 'yy/mm/dd';
				break;
			case 'ymd_dash':
				$format = 'yy-mm-dd';
				break;
			case 'ymd_dot':
				$format = 'yy.mm.dd';
				break;
		}

		return $format;
	}
}