<?php

/**
 * @wordpress-plugin
 * Plugin Name: Gravity Forms Limit Date Range
 * Plugin URI: https://gravityplus.pro/gravity-forms-limit-date-range
 * Description: Limit the date range for a Gravity Forms Date field (Date Picker or Dropdown type)
 * Version: 2.1.1
 * Author: gravity+
 * Author URI: https://gravityplus.pro
 * Text Domain: gfp-limit-date-range
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package   GFP_Limit_Date_Range
 * @version   2.1.1
 * @author    gravity+ <support@gravityplus.pro>
 * @license   GPL-2.0+
 * @link      https://gravityplus.pro
 * @copyright 2014 gravity+
 *
 * last updated: February 25, 2014

 */

define( 'GFP_LIMIT_DATE_RANGE_FILE', __FILE__ );
define( 'GFP_LIMIT_DATE_RANGE_PATH', plugin_dir_path( __FILE__ ) );
define( 'GFP_LIMIT_DATE_RANGE_URL', plugin_dir_url( __FILE__ ) );

require_once( trailingslashit( GFP_LIMIT_DATE_RANGE_PATH ) . 'includes/class-limit-date-range.php' );

$gfp_limit_date_range = new GFP_Limit_Date_Range();