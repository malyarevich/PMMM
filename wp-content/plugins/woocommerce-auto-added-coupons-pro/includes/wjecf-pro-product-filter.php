<?php

defined('ABSPATH') or die();

/**
 * New in 2.3.6
 * 
 */
class WJECF_Pro_Product_Filter extends Abstract_WJECF_Plugin {
	public function __construct() {	
		$this->set_plugin_data( array(
			'description' => __( 'Advanced matching queries for products.', 'woocommerce-jos-autocoupon' ),
			'dependencies' => array(),
			'can_be_disabled' => true
		) );		

	}

	public function init_hook() {
		if ( ! class_exists('WC_Coupon') ) {
			return;
		}

		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'woocommerce_coupon_is_valid_for_product'), 10, 4 );
		add_action( 'wjecf_assert_coupon_is_valid', array( $this, 'assert_coupon_is_valid' ), 10, 2 );

	}

	/**
	 * Extra validation rules for coupons. Must throw Exception on failure
	 * @param WC_Coupon $coupon 
	 * @return void
	 */
	function assert_coupon_is_valid ( $coupon ) {
		$custom_fields = get_post_meta( $coupon->id, '_wjecf_custom_fields', true );

		if ( empty( $custom_fields ) ) {
			return;
		}

		$custom_fields_and = get_post_meta( $coupon->id, '_wjecf_custom_fields_and', true ) == 'yes';

		$cart = WC()->cart->get_cart();

		$match_was_found = false;
		foreach( $custom_fields as $cf ) {
			$does_match = false;			
			foreach( $cart as $cart_item_key => $cart_item ) {
				$_product = $cart_item['data'];
				if ( $this->product_matches_custom_field( $_product, $cf ) ) {
					$does_match = true;
					break; //we found a match in the cart
				}
			}

			if ( ! $does_match && $custom_fields_and ) {
				throw new Exception( WC_Coupon::E_WC_COUPON_NOT_APPLICABLE );; //fail immediately
			}

			$match_was_found |= $does_match;
			if ( $match_was_found && ! $custom_fields_and ) {
				break; //no need to look further
			}
		}

		if ( ! $match_was_found ) {
			throw new Exception( WC_Coupon::E_WC_COUPON_NOT_APPLICABLE );; //fail immediately
		}

	}	

	/**
	 * Extra validation rules for coupons.
	 * @param bool $valid 
	 * @param WC_Coupon $coupon 
	 * @return bool True if valid; False if not valid.
	 */
	function woocommerce_coupon_is_valid_for_product ( $valid, $product, $coupon, $values ) {
		$custom_fields_and = get_post_meta( $coupon->id, '_wjecf_custom_fields_and', true ) == 'yes';
		$custom_fields = get_post_meta( $coupon->id, '_wjecf_custom_fields', true );

		if ( empty( $custom_fields ) ) {
			return $valid;
		}

		foreach( $custom_fields as $cf ) {
			if ( $this->product_matches_custom_field( $product, $cf ) ) {
				return true;
			}
		}		

		return false;
	}	

	/**
	 * Tests whether product matches a single custom field
	 * @param WC_Product $product 
	 * @param array $cf array( 'key' => ..., 'value' => ... )
	 * @return bool
	 */
	private function product_matches_custom_field( $product, $cf ) {

		//TODO: Allow for custom fields on variations
		$metavalue = get_post_meta( $product->id, $cf['key'], true );

		if ( $metavalue !== false ) {
			$lines = preg_split('/\r\n|[\r\n]/', $cf['value'] );
			foreach( $lines as $line ) {		
				if ( $metavalue !== false && $this->smart_match( $line, $metavalue ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * match_string can be either a:
	 * regex                                e.g.  '/^blah$/'
	 * comparison                           e.g.  '> 5.5'
	 * literal (whole word) with wildcards  e.g.  'blah%'
	 * 
	 * @param string $match_string  Matching pattern
	 * @param string $metavalue Value to be compared
	 * @return bool
	 */
	private function smart_match( $match_string, $subject ) {

		//REGEX (NOTE: By default case sensitive; append i to the flag for case insensitive match)
		if (substr( $match_string, 0, 1 ) == '/' ) {
			return (bool) @preg_match($match_string, $subject);
		}

		//NUMERIC COMPARISON. Must use period as decimal separator
		$MATCH_NUMBER = "(-?(?:\d+|\d*.?\d*))";
		$MATCH_OPERATOR = "(<|>|<=|>=|=|==|<>|!=)";
		if ( preg_match( "/^" . $MATCH_OPERATOR . "\s*" . $MATCH_NUMBER . "$/", $match_string, $matches ) ) {
			//Find numbers in subject
			if ( preg_match( "/^\s*" . $MATCH_NUMBER . "\s*$/" , $subject, $subject_matches ) ) {
				$operator = $matches[1];
				$comp_value = $matches[2];

				$subject_value = $subject_matches[1];
				//echo "op: $operator  cv: $comp_value  val: $value\n";
				switch ( $operator ) {
					case '<': 
						return $subject_value < $comp_value;

					case '>': 
						return $subject_value > $comp_value;

					case '<=': 
						return $subject_value <= $comp_value;

					case '>=': 
						return $subject_value >= $comp_value;

					case '!=': 
					case '<>': 
						return $subject_value != $comp_value;

					case '=': 
					case '==': 
						return $subject_value == $comp_value;
						
				}
			}
			return false;
		}

		// Case insensitive whole word compare
		// % can be used as wildcard. e.g. pattern 'mus%'' will match any word starting with 'mus'
		$pattern = preg_quote( $match_string );
		$pattern = "/\b" . str_replace("%", ".*", $pattern) . "\b/i";
		return (bool) preg_match($pattern, $subject);
	}

/** ADMIN **/

	public function init_admin_hook() {
		add_action('woocommerce_coupon_options_usage_restriction', array( $this, 'on_woocommerce_coupon_options_usage_restriction' ), 10, 1);
		add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'on_process_shop_coupon_meta' ), 10, 2 );

		WJECF_ADMIN()->add_inline_style( '
			#woocommerce-coupon-data .wjecf-table { width:100%; display:table }

			#woocommerce-coupon-data .wjecf-table .tr {display:table-row }

			#woocommerce-coupon-data .wjecf-table .td,
			#woocommerce-coupon-data .wjecf-table .th { vertical-align:top;display:table-cell; padding-right: 2px }

			#woocommerce-coupon-data .wjecf-table .th { font-weight: bold }

			#woocommerce-coupon-data .wjecf-table .td input,
			#woocommerce-coupon-data .wjecf-table .td select,
			#woocommerce-coupon-data .wjecf-table .td textarea { width:100% }

			#woocommerce-coupon-data #wjecf-table-custom-field { margin-bottom: 1em; }
		' );

	}

	public function on_woocommerce_coupon_options_usage_restriction() {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

		$all_custom_fields = $this->get_all_custom_fields();
		//We simulate a table layout using spans; because a div or table can't be used within a p tag; and the p tag is used
		//in the settings API for alignment

		$coupon_custom_fields = get_post_meta( $thepostid, '_wjecf_custom_fields', true );

		//print_r($coupon_custom_fields);


?>
		<div class="options_group" id="wjecf-group-product-filter">
			<?php

				WJECF_ADMIN()->render_select_with_default( array(
					'id' => '_wjecf_custom_fields_and', 
					'label' => __( 'Custom Fields Operator', 'woocommerce-jos-autocoupon' ), 
					'options' => array( 'no' => __( 'OR', 'woocommerce-jos-autocoupon' ), 'yes' => __( 'AND', 'woocommerce-jos-autocoupon' ) ),
					'default_value' => 'no',
					'class' => 'wjecf-not-wide',
					'description' => __( 'Use AND if all of the custom fields must be in the cart to use this coupon (instead of only one of the custom fields).', 'woocommerce-jos-autocoupon' ),
					'desc_tip' => true
				) );

			?>
			<p class="form-field">
				<label for="wjecf-table-custom-field"><?php _e( 'Custom Fields' ); ?>
					<strong><span id="wjecf_custom_fields_and_label"></span></strong>
				</label>
				
				<span class="wjecf-table" id="wjecf-table-custom-field">
					<span class="tr">
						<span class="th"><?php _e( 'Name', 'woocommerce' ); ?></span>
						<span class="th"><?php _e( 'Value', 'woocommerce' ); ?>
							<?php echo WJECF_WC()->wc_help_tip( 
								__(
'If multiple lines are entered, only one of them needs to match. A whole word case insensitive match is executed by default and % can be used as wildcard. If a line starts with a forward slash (/) it is treated as a regular expression. NOTE: Regular expression matches are case sensitive by default; append flag i to the regular expression for a case insensitive match. 
Examples:
\'roc%\' matches \'Rock\' and also \'Rock the house\', but not \'Bedrock\'
\'/^rock$/i\' matches \'Rock\' but not \'Rock the house\'', 'woocommerce-jos-autocoupon' ) ); ?>
						</span>
						<span class="th"></span>
					</span>
					<?php
					if ( ! empty( $coupon_custom_fields ) ) {
						foreach ( $coupon_custom_fields as $cf ):
						?>
							<span class="tr">
								<span class="td"><input name="_wjecf_metakey[]" type="text" value='<?php echo esc_attr( $cf['key'] ); ?>'></span>
								<span class="td"><textarea name="_wjecf_metavalue[]" rows="2" cols="25"><?php echo esc_textarea( $cf['value'] ); ?></textarea></span>
								<span class="td"><a href="#">X</a></span>
							</span>
						<?php
						endforeach;
					}
					?>	
				</span>
				<label for="wjecf-select-custom-field"></label>
				<select id="wjecf-select-custom-field">
					<option value=""><?php _e( '&mdash; Select &mdash;' ); ?></option>
					<?php
						foreach ( $all_custom_fields as $custom_field ) {
							//if ( ! is_protected_meta( $custom_field, 'post' ) ) {
								echo "\n<option value='" . esc_attr($custom_field) . "'>" . esc_html($custom_field) . "</option>";
							//}
						}
					?>
				</select>
				<button id="wjecf-add-custom-field">Add</button>
			</p>			
		</div>
		<script type="text/javascript">
			if ( undefined !== jQuery ) {
				jQuery( function( $ ) {

					// FUNCTIONS //	

					var init = function() {

						update_table_visibility();
						update_add_button();

						update_wjecf_custom_fields_and();

						//Move our group below the WooCommerce product and category filter
						var domPosition = $("#exclude_product_categories").closest("div.options_group");
						if ( domPosition.length == 1 ) {
							$("#wjecf-group-product-filter").detach().insertAfter( domPosition );
						}

					}				

					//Toggle table visibility based on contents
					var update_table_visibility = function() {
						var has_rows = $("#wjecf-table-custom-field").find("span.tr").length > 1;
						$("#wjecf-table-custom-field").toggle( has_rows );
					}

					//Toggle 'Add'-button disabled/enabled state
					var update_add_button = function() {
						$("#wjecf-add-custom-field").prop( 'disabled', $("#wjecf-select-custom-field").val() == '' );
					}

					//Add a row to the table
					var add_table_row = function() {
						var value = $("#wjecf-select-custom-field").val();

						var html = '<span class="tr">';
						html += '<span class="td"><input name="_wjecf_metakey[]" type="text"></span>';
						html += '<span class="td"><textarea name="_wjecf_metavalue[]" rows="2" cols="25"></textarea></span>';
						html += '<span class="td"><a href="#">X</a></span>';
						html += '</span>';

						var elements = $.parseHTML( html );
						$( elements ).find("input").val( value );
						$( elements ).find("a").click( function( event ) {
							event.preventDefault();
							$(this).closest("span.tr").remove();
							update_table_visibility();
						} );

						$("#wjecf-table-custom-field").append( elements );
					}

					var update_wjecf_custom_fields_and = function() { 
						$("#wjecf_custom_fields_and_label").html( 
							$("#_wjecf_custom_fields_and").val() == 'yes' ? wjecf_admin_i18n.label_and : wjecf_admin_i18n.label_or
						);
					};					

					// EVENTS //

					//Disable the 'Add'-button if nothing selected
					$("#wjecf-select-custom-field").on( 'change', function( event ) {
						update_add_button();
					} );

					$("#wjecf-add-custom-field").click( function( event ) {
						event.preventDefault();
						add_table_row();
						update_table_visibility();						
					} );

					$("#wjecf-table-custom-field").find("a").click( function( event ) {
						event.preventDefault();
						$(this).closest("span.tr").remove();
						update_table_visibility();
					} );					

					$("#_wjecf_custom_fields_and").click( update_wjecf_custom_fields_and );

					//EXECUTE NOW!
					init();

				} );
			}
		</script>
<?php
	}

	public function on_process_shop_coupon_meta( $post_id, $post ) {
		//die( print_r( $_POST, true ) ) ;

		update_post_meta( $post_id, '_wjecf_custom_fields_and', isset( $_POST['_wjecf_custom_fields_and'] ) && $_POST['_wjecf_custom_fields_and'] == 'yes' ? 'yes' : 'no' );

		$meta_keys = isset( $_POST['_wjecf_metakey'] ) ? $_POST['_wjecf_metakey'] : array();
		$meta_values = isset( $_POST['_wjecf_metavalue'] ) ? $_POST['_wjecf_metavalue'] : array();

		$n = min( sizeof($meta_keys), sizeof($meta_values) );
		$custom_fields = array();
		for ( $i=0; $i < $n; $i++ ) {
			//Ignore empty keys or values
			if ( empty( $meta_keys[$i] ) || empty( $meta_values[$i] ) ) {
				continue;
			}

			//Sanitize lines and look for invalid regex
			$lines = preg_split('/\r\n|[\r\n]/', $meta_values[$i] );
			foreach( $lines as $key => $line ) {
				$line = trim( $line );
				if ( $line[0] == '/' && @preg_match($line, "") === false) {
					$lines[$key] = "INVALID REGEX: " . $line;
				}
			}

			//Ignore empty values
			$lines = array_filter( $lines );
			if (empty( $lines ) ) {
				continue;
			}

			$custom_fields[] = array('key' => $meta_keys[$i], 'value' => implode( "\n", $lines ) );
		}

		if ( empty( $custom_fields ) ) {
			delete_post_meta( $post_id, '_wjecf_custom_fields' );
		} else {
			update_post_meta( $post_id, '_wjecf_custom_fields', $custom_fields );
		}

	}	


	private function get_all_custom_fields( $limit = null ) {
		global $wpdb;

		$sql = "SELECT DISTINCT meta_key
			FROM $wpdb->postmeta pm
			LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id
			WHERE p.post_type = 'product'
			-- AND meta_key NOT BETWEEN '_' AND '_z'
			-- AND meta_key NOT LIKE %s
			ORDER BY meta_key";

		if ($limit !== null) {
			$sql .= sprintf( " LIMIT %d", $limit );
		}

		$keys = $wpdb->get_col( $wpdb->prepare( $sql, $wpdb->esc_like( '_' ) . '%' ) );

		if ( $keys ) {
			//Remove protected meta items
			//$keys = array_diff( $keys, array_filter( $keys, 'is_protected_meta' ) );
			natcasesort( $keys );
		}
		return $keys;
	}

/** END ADMIN **/

}