<?php
/**
 * Criteria Helper class: Fetch and analyze criteria
 *
 * @static
 * @package  woocommerce-bacchus-gold-member
 * @subpackage lib/helpers
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 */
class WBGM_Criteria_Helper
{
	/**
	 * Parse user defined expression to valid boolean
	 *
	 * @since 0.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $slug Slug of the criteria
	 *
	 * @return boolean
	 */
	public static function parse_criteria( $slug ) {

		//if the slug is empty it satisfies
		//every condition
		if( empty($slug) ) {
			return true;
		}

		$conditions = self::arrange_criteria( $slug );
		if( empty($conditions) ) {
			return false;
		}

		$flag = false;
		foreach( $conditions as $condition ) {
			$real_value = self::get_real_value( $condition[0] );
			switch( $condition[1] ) {
				case '<':
					$flag = $real_value < $condition[2];
					break;

				case '>':
					$flag = $real_value > $condition[2];
					break;

				case '==':
					$flag = $real_value == $condition[2];
					break;

				case '!=':
					$flag = $real_value != $condition[2];
					break;
			}

			return $flag;
		}

		return false;
	}

	/**
	 * Get real values from data availble in cart
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $param Key
	 *
	 * @return integer|boolean
	 */
	public static function get_real_value( $param )
	{
		switch( $param ) {
			case 'num_products':
				return WBGM_Product_Helper::get_main_product_quantity_count();

			case 'total_price':
				return WC()->cart->cart_contents_total;
		}
		return false;
	}

	/**
	 * Filters and returns condition array
	 *
	 * @since 0.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $slug Slug of the criteria
	 *
	 * @return array
	 */
	public static function arrange_criteria( $slug )
	{
		$criteria = self::get_criteria( $slug );

		$filtered_conditions = array();
		if( ! empty($criteria) ) {
			/** @var array $conditions */
			$conditions = $criteria;

			unset( $conditions['name'] );
			unset( $conditions['slug'] );

			foreach( $conditions as $condition ) {
				$filtered_conditions[] = $condition;
			}
		}

		return $filtered_conditions;
	}

	/**
	 * Get criteria from slug
	 *
	 * @since 0.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $slug Slug of the criteria
	 *
	 * @return array|boolean
	 */
	public static function get_criteria( $slug )
	{
		/** @var array $all_criteria */
		$all_criteria = WBGM_Settings_Helper::get( '', false, 'criteria', false );
		if( empty($all_criteria) ) {
			return false;
		}

		foreach( $all_criteria as $criteria ) {
			if( $criteria['slug'] === $slug ) {
				return $criteria;
			}
		}

		return false;
	}
}
