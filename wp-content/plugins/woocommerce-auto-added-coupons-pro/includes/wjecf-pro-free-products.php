<?php

defined('ABSPATH') or die();

class WJECF_Pro_Free_Products extends Abstract_WJECF_Plugin {

	public function __construct() {    
		$this->set_plugin_data( array(
			'description' => __( 'Allow free products to be added to the cart.', 'woocommerce-jos-autocoupon' ),
			'dependencies' => array(),
			'can_be_disabled' => true
		) );		
	}
	
	public function init_hook() {
		if ( ! class_exists('WC_Coupon') ) {
			return;
		}
		
		//Admin hooks
        add_action( 'admin_init', array( &$this, 'admin_init' ) );

        //Frontend hooks - logic
		if ( WJECF_WC()->check_woocommerce_version('2.3.0')) {
			add_action( 'woocommerce_after_calculate_totals', array( $this, 'update_free_products_in_cart' ), 23 ); // Must be AFTER hook if Auto Coupon
		} else {
			//WC Versions prior to 2.3.0 don't have after_calculate_totals hook, this is a fallback
			add_action( 'woocommerce_cart_updated',  array( $this, 'update_free_products_in_cart' ), 23 ); 
		}

		add_filter( 'woocommerce_add_cart_item', array( $this, 'filter_woocommerce_add_cart_item'), 90, 6); // overwrite values (price 0.00) if it's a free products
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'filter_woocommerce_get_cart_item_from_session' ), 90, 3); // overwrite values (price 0.00) if it's a free product

 		add_filter( 'wjecf_coupon_has_a_value', array( $this, 'filter_wjecf_coupon_has_a_value' ), 10, 2 ); // if coupon grants free products, it has a value! Required for Auto Coupons
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'filter_woocommerce_coupon_is_valid_for_product' ), 10, 4 ); //don't count the free items for coupon restrictions

		//Frontend hooks for 'Select free gift'
		add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'template_checkout_select_free_product' ) );
		add_action( 'woocommerce_cart_contents', array( $this, 'template_cart_select_free_product' ) );
		add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'woocommerce_update_cart_action_cart_updated' ) );

		add_action( 'woocommerce_before_checkout_process', array( $this, 'woocommerce_before_checkout_process' ) );
		add_action( 'woocommerce_cart_emptied', array( $this, 'woocommerce_cart_emptied' ) );

		//2.3.4-b6 AJAX support for 'Select free gift'
		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'filter_woocommerce_update_order_review_fragments' ), 10, 1);
		add_action( 'wp_footer', array( $this, 'ajax_script' ) );
		add_action( 'wc_ajax_wjecf_cart_select_free_product', array( $this, 'wc_ajax_wjecf_cart_select_free_product' ) );

		//Frontend hooks - Cart visualisation
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'filter_woocommerce_cart_item_remove_link' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'filter_woocommerce_cart_item_price' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'filter_woocommerce_cart_item_subtotal' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'filter_woocommerce_cart_item_quantity' ), 10, 2 );
	}


// ==================
// BEGIN - AJAX SUPPORT
// ==================

	/**
	 * (Since 2.3.4-b6)
	 * Add ajax support script to the cart page
	 */
	function ajax_script() {
		//Only append to cart page
		if ( ! is_cart() ) {
			return;
		}

		//Unfortunately the AJAX on the cart page is not easily overridable using fragments (as on the checkout page). 
		//Solution: Listen to ajax calls to the apply_coupon and remove_coupon wc-ajax url
		?>
		<script type="text/javascript">
		  if ( undefined !== jQuery ) {
				// script dependent on jQuery
				jQuery( function( $ ) {
					// wc_cart_params is required to continue, ensure the object exists
					if ( typeof wc_cart_params === 'undefined' ) {
						return false;
					}

					var get_url = function( endpoint ) {
						return wc_cart_params.wc_ajax_url.toString().replace(
							'%%endpoint%%',
							endpoint
						);
					};

					var wjecf_cart_select_free_product = {
						init: function() {
							this.update_select_free_product = this.update_select_free_product.bind( this );
							this.handle_ajax_success = this.handle_ajax_success.bind( this );

							$( document ).ajaxSuccess( this.handle_ajax_success );
							$( document ).on(
								'change input',
								'div.woocommerce > form .wjecf-select-free-products :input',
								this.input_changed );

						},
						handle_ajax_success: function( event, jqXHR, ajaxOptions, data ) {
							if (ajaxOptions.url == get_url( 'apply_coupon') || ajaxOptions.url == get_url( 'remove_coupon') ) {
								this.update_select_free_product(); //display/remove select free product
								this.update_cart(); //display/remove free product in cart
							}
						},
						update_cart: function() {
							//Update the cart form
							jQuery(document.body).trigger('wc_update_cart'); 
						},
						update_select_free_product: function() {
							$.ajax( {
								url:      get_url( 'wjecf_cart_select_free_product' ),
								dataType: 'html',
								success: function( response ) {
									$( '.wjecf-fragment-cart-select-free-product' ).replaceWith( response );
								}
							} );					
						},
						/**
						 * After and input is changed, enabled the update cart button.
						 */
						input_changed: function() {
							$( 'div.woocommerce > form input[name="update_cart"]' ).prop( 'disabled', false );
						},						
					}
					wjecf_cart_select_free_product.init();
				});
			}
		</script>
		<?php
	}

	/**
	 * (Since 2.3.4-b6)
	 * Get Select free product AJAX fragment
	 * 
	 */
	public function wc_ajax_wjecf_cart_select_free_product() {
		$this->template_cart_select_free_product();
		wp_die();
	}

	/**
	 * (Since 2.3.4-b6)
	 * Add Select free product to the AJAX fragments
	 * 
	 * @param array $fragments 
	 * @return array Updated fragments 
	 */
	public function filter_woocommerce_update_order_review_fragments( $fragments ) {
		ob_start();
		$this->template_checkout_select_free_product();
		$fragments[ '.wjecf-fragment-checkout-select-free-product' ] = ob_get_clean();

		return $fragments;
	}

// ==================
// END - AJAX SUPPORT
// ==================


	//Show 'select free gift'-radiobuttons on cart
	public function template_cart_select_free_product() {
		$free_gift_coupons = $this->get_applied_select_free_product_coupons();
		//if ( sizeof( $free_gift_coupons ) > 0 ) {
		WJECF()->include_template( 'cart/select-free-product.php', compact( 'free_gift_coupons' ) );
		//}
	}

	//Show 'select free gift'-radiobuttons during checkout
	public function template_checkout_select_free_product() {
		$free_gift_coupons = $this->get_applied_select_free_product_coupons();
		WJECF()->include_template( 'checkout/select-free-product.php', compact( 'free_gift_coupons' ) );
	}

	//Save selected free gift in the session
	public function woocommerce_update_cart_action_cart_updated( $cart_updated ) {
        if ( $this->form_get_selected_free_products() ) {
        	$cart_updated = true;
        }
        return $cart_updated;
    }

	//Don't checkout if no free gift selected
	public function woocommerce_before_checkout_process() {	 
        //First check if free product selection was posted
        $this->form_get_selected_free_products();

        foreach( WC()->cart->get_applied_coupons() as $coupon_code ) {
            $coupon = new WC_Coupon( $coupon_code );
            //Must the user select a free product?
            if ( $this->must_select_free_product( $coupon->id ) ) {
                $free_product_ids = WJECF()->get_plugin('WJECF_Pro_Free_Products')->get_coupon_free_product_ids( $coupon->id );
                $prod_id = $this->get_session_selected_product( $coupon_code );
                if ( ! in_array( $prod_id, $free_product_ids ) ) {
				 	wc_add_notice( __('Please select your free gift.', 'your_language_key'), 'error' );
					return;
                }
            }
        }
	}

	//Forget all selected products after successful checkout
	public function woocommerce_cart_emptied() {
		$this->clear_session_selected_products();
	}

	public function filter_wjecf_coupon_has_a_value ( $has_a_value, $coupon ) {
		//Tell autocoupon that the coupon has a value, if it is a free product coupon
		if ( ! $has_a_value && count( $this->get_coupon_free_product_ids( $coupon->id ) ) > 0 ) {
			$has_a_value = true;
		} elseif ( get_post_meta( $coupon->id, '_wjecf_bogo_matching_products', true ) == 'yes' ) {
			$bogo_products = $this->get_bogo_products_from_cart( $coupon );
			$has_a_value = ! empty( $bogo_products );
		}
		return $has_a_value;
	}

	//don't count the free items for coupon restrictions
	public function filter_woocommerce_coupon_is_valid_for_product ( $valid, $product, $coupon, $values = null ) {
		if ( $valid && isset( $values['_wjecf_free_product_coupon'] ) ) {
			$valid = false;
		}
		return $valid;
	}

	//Test for POST variables with free product selection
	private function form_get_selected_free_products() {
		$items_selected = false;
        foreach( WC()->cart->get_applied_coupons() as $coupon_code ) {
            $field_name = 'wjecf_free_sel_' . esc_attr( $coupon_code );
            if ( isset( $_POST[ $field_name ] ) ) {
            	$this->set_session_selected_product( $coupon_code, intval( $_POST[ $field_name ] ) );
                $items_selected = true;
            }
        }
        return $items_selected;
	}

	private function get_bogo_products_from_cart( $coupon ) {
		$bogos = array();

		$bogo = get_post_meta( $coupon->id, '_wjecf_bogo_matching_products', true ) == 'yes';
		if ( $bogo ) {
			$coupon_multiplies = get_post_meta( $coupon->id, '_wjecf_multiply_free_products', true ) == 'yes';

			foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( ! isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
					$_product = $cart_item['data'];
					if ( WJECF()->coupon_is_valid_for_product( $coupon, $_product, $cart_item ) ) {						
						// echo "<pre>";
						// var_dump(WC()->cart->get_cart());
						// flush();
						// die();
						$prod_or_var_id = WJECF()->get_product_or_variation_id( $_product );
						$bogos[ $prod_or_var_id ] = $coupon_multiplies ? $cart_item['quantity'] : 1;
					}
				}
			}
		}
		$this->log( "Bogos [" . $coupon->code . "] : " . implode( ", ", $bogos ) );
		return $bogos;
	}

	private $nest_catch = 0;
	public function update_free_products_in_cart() {
		$this->log( "()" );

		$this->nest_catch++ ;
		if ( $this->nest_catch > 10 ) {
			$this->log( "Nesting error" );
			return;
		}

		$coupons = WC()->cart->get_coupons();

		//Count the free products that should be in the cart [] prod_id => quantity ]
		$free_product_ids = array();

		//Count the bogo products that should be in the cart [] prod_id => quantity ]
		$bogo_product_ids = array();

		foreach ($coupons as $coupon) {

	        $coupon_free_product_ids = $this->get_coupon_free_product_ids( $coupon->id );
	        //Must user choose a gift? Then remove all others from the array
	        if ( $this->must_select_free_product( $coupon->id ) ) {
	        	$prod_id = $this->get_session_selected_product( $coupon->code );
	        	if ( in_array( $prod_id, $coupon_free_product_ids ) ) {
	        		$coupon_free_product_ids = array( $prod_id );
	        	} else {
	        		$coupon_free_product_ids = array();
	        	}
	        }

			//BOGO
			foreach( $this->get_bogo_products_from_cart( $coupon ) as $prod_id => $qty ) {
				$qty = apply_filters( 'wjecf_bogo_product_amount_for_coupon', $qty, $coupon ); // 2.3.3
				//If multiple rules, get the highest qty
				if ( ! isset ( $bogo_product_ids[$prod_id] ) || $bogo_product_ids[$prod_id] < $qty ) {
					$bogo_product_ids[$prod_id] = $qty;
				}
			}

			//FREE PRODUCT
			$qty = $this->get_product_multiplier_value( $coupon );
			$qty = apply_filters( 'wjecf_free_product_amount_for_coupon', $qty, $coupon ); // 2.3.3

			foreach($coupon_free_product_ids  as $prod_id ) {
				if (isset($free_product_ids[$prod_id])) {
					$free_product_ids[$prod_id] += $qty;
				} else {
					$free_product_ids[$prod_id] = $qty;
				}
			}
		}

		//Merge bogos with the free_product_ids array
		foreach( $bogo_product_ids as $prod_id => $qty ) {
			if ( isset( $free_product_ids[$prod_id] ) ) {
				$free_product_ids[$prod_id] += $qty;
			} else {
				$free_product_ids[$prod_id] = $qty;
			}
		}

		//NOW WE KNOW THE AMOUNT OF FREE PRODUCTS THAT SHOULD BE IN THE CART

		// Remove free products that don't apply anymore
		foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
				$_product = $cart_item['data'];
				$prod_or_var_id = WJECF()->get_product_or_variation_id( $_product );
				//$product_id = 'product_variation' == get_post_type( $cart_item['product_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
				if ( ! isset( $free_product_ids[ $prod_or_var_id ] ) ) {
					//WC()->cart->remove_cart_item( $cart_item_key );
					//2.2.5 WC()->cart->remove_cart_item CAUSES RECALCULATION AND COULD CAUSE AN INFINITE LOOP
					$adjust_quantity = WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
					do_action( 'woocommerce_remove_cart_item', $cart_item_key, $this );
					unset( WC()->cart->cart_contents[ $cart_item_key ] );

					//Adjust cart calculated values
					WC()->cart->cart_contents_count -= $adjust_quantity;
					WC()->cart->cart_contents_weight -= $_product->get_weight() * $adjust_quantity;

					//$this->log("Removing $prod_or_var_id");
					do_action( 'woocommerce_cart_item_removed', $cart_item_key, $this );

				}
			}
		}

		// Add free products or adjust the quantity
		foreach( $free_product_ids as $product_id => $qty ) {
			$cart_item_key = $this->set_free_product_amount_in_cart( $product_id, $qty );
		}
	}

	/**
	 * Adds the item to the cart as a free product. If already in the cart it adjuests the quantity
	 * 
	 * @param int $product_id The id of the product or varation
	 * @param int $quantity The amount to add
	 * @return string The cart_item_key
	 */
	private function set_free_product_amount_in_cart( $product_id, $quantity ) {
		$this->log( "( $product_id, $quantity )" );

		// Ensure we don't add a variation to the cart directly by variation ID
		if ( 'product_variation' == get_post_type( $product_id ) ) {
			$variation_id = $product_id;
			$product_id   = wp_get_post_parent_id( $variation_id );
			$variation = wc_get_product_variation_attributes( $variation_id );
		} else {
			$variation_id = 0;
			$variation = array();
		}
		// Get the product
		$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

		//Make sure stock is sufficient
		$qty_in_cart = 0;
		foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] == $product_id && ! isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
				$qty_in_cart += $cart_item['quantity'];
			}
		}
		$quantity = $this->product_available_stock( $product_data, $quantity + $qty_in_cart ) - $qty_in_cart ;
		if ( $quantity < 0 ) {
			$quantity = 0;
		}
		$quantity = apply_filters( 'wjecf_set_free_product_amount_in_cart', $quantity, $product_data ); // 2.3.3

		$cart_item_data = array( "_wjecf_free_product_coupon" => true );
		$cart_item_key  = $this->find_free_product_in_cart( $product_id, $variation_id, WC()->cart->get_cart() ); 


		// If cart_item_key is set, the item is already in the cart
		if ( $cart_item_key ) {
			$adjust_quantity = $quantity - WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
			WC()->cart->set_quantity( $cart_item_key, $quantity, false );
		} else {
			$adjust_quantity = $quantity;
			$cart_item_key = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

			// Add item after merging with $cart_item_data - hook to allow plugins to modify cart item
			WC()->cart->cart_contents[ $cart_item_key ] = apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
				'product_id'	=> $product_id,
				'variation_id'	=> $variation_id,
				'variation' 	=> $variation,
				'quantity' 		=> $quantity,
				'data'			=> $product_data
			) ), $cart_item_key );

			//Not calling this; causes calculation
			//	do_action( 'woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );
		}
		//Adjust cart calculated values
		WC()->cart->cart_contents_count += $adjust_quantity;
		WC()->cart->cart_contents_weight += $product_data->get_weight() * $adjust_quantity;
		$this->log( sprintf( "Adjust quantity with %d to %d", $adjust_quantity,WC()->cart->cart_contents_count ) );

		if ( isset( WC()->cart->cart_contents[$cart_item_key] ) ) { //Might be removed by qty=0 therefore check with isset
			//Move free items to the end of the cart
			$temp = WC()->cart->cart_contents[$cart_item_key];
			unset( WC()->cart->cart_contents[$cart_item_key] );
			WC()->cart->cart_contents[$cart_item_key] = $temp;
		}

		return $cart_item_key;
	}


	private function find_free_product_in_cart( $product_id, $variation_id, $cart_contents ) {
		foreach( $cart_contents as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
				if ( $cart_item['product_id'] == $product_id && $cart_item['variation_id'] == $variation_id ) {
					return $cart_item_key;
				}
			}
		}
		return false;
	}

	/**
	 * If the answer is no, it returns the available amount, otherwise it returns the requested amount
	 */
	private function product_available_stock( $product, $quantity ) {
		if ( ! $product->managing_stock() || $product->backorders_allowed() || $product->get_stock_quantity() >= $quantity ) {
			return $quantity;
		}
		return $product->get_stock_quantity();
	}	

	/**
	 * Show 'Free!' in the cart for free product
	 */
	public function filter_woocommerce_cart_item_price ( $price_html, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
			$price_html = apply_filters( 'wjecf_free_cart_item_price', __('Free!', 'woocommerce'), $price_html, $cart_item, $cart_item_key );
		}
		return $price_html;
	}

	/**
	 * Show 'Free!' in the cart for free product
	 */
	public function filter_woocommerce_cart_item_subtotal ( $price_html, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
			$price_html = apply_filters( 'wjecf_free_cart_item_subtotal', __('Free!', 'woocommerce'), $price_html, $cart_item, $cart_item_key );
		}
		return $price_html;
	}

	/**
	 * Quantity is readonly for free product
	 */
	public function filter_woocommerce_cart_item_quantity ( $product_quantity_html, $cart_item_key ) {		
		$cart_item = WJECF_WC()->get_cart_item( $cart_item_key );

		if ( isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
			$qty = intval($cart_item['quantity']);
			$product_quantity_html = sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="%d" />', $qty, $cart_item_key, $qty );
		}
		return $product_quantity_html;

	}


	/**
	 * Remove the 'remove item'-link
	 */
	public function filter_woocommerce_cart_item_remove_link ( $remove_html, $cart_item_key ) {
		$cart_contents = WC()->cart->get_cart();
		//Remove the link if it's a free item
		if ( isset( $cart_contents[$cart_item_key]['_wjecf_free_product_coupon']) ) {
			return '';
		}
		return $remove_html;
	}

	public function filter_woocommerce_add_cart_item ( $cart_item_data, $cart_item_key ) {
		return $this->overwrite_cart_item_data( $cart_item_data );
	}

	public function filter_woocommerce_get_cart_item_from_session ( $session_data, $values, $key ) {
		return $this->overwrite_cart_item_data( $session_data );
	}

	/**
	 * Overwrite product data if it belongs to a free product coupon
	 */
	private function overwrite_cart_item_data( $cart_item_data ) {
		if ( isset( $cart_item_data['_wjecf_free_product_coupon'] ) ) {
			$cart_item_data['data']->set_price( 0 );

			//$cart_item_data['data']->sold_individually = 'yes';
		}
		return $cart_item_data;


	}
	
/* ADMIN HOOKS */
	public function admin_init() {	
		//Inject columns
		if ( WJECF()->is_pro() ) {
			WJECF()->inject_coupon_column( 
				'_wjecf_free_products', 
				__( 'Free products', 'woocommerce-jos-autocoupon' ), 
				array( $this, 'admin_render_shop_coupon_columns' ), 'products'
			);
		}

		add_action( 'wjecf_coupon_metabox_products', array( $this, 'admin_coupon_metabox_products' ), 15, 2 );
		add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'process_shop_coupon_meta' ), 10, 2 );
	}


	public function admin_render_shop_coupon_columns( $column, $post ) {
		switch ( $column ) {
			case '_wjecf_free_products' :
				$free_product_ids = $this->get_coupon_free_product_ids( $post->ID );
				echo esc_html( implode( ', ', $free_product_ids ) );
				break;
		}
	}	
	
	public function admin_coupon_metabox_products( $thepostid, $post ) {
		
		//=============================
		//Title
		echo "<h3>" . esc_html( __( 'Free products', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";

        //=============================
        //Free product ids
        $free_product_ids = $this->get_coupon_free_product_ids( $thepostid );

        echo '<p class="form-field"><label>' . __( 'Free products', 'woocommerce' ) . '</label>';        
		WJECF_WC()->render_admin_product_selector( 'wjecf_free_product_ids', 'wjecf_free_product_ids', $free_product_ids, null );
		echo WJECF_WC()->wc_help_tip( __( 'Free products that will be added to the cart when this coupon is applied.', 'woocommerce-jos-autocoupon' ) );
        echo '</p>';

		//=============================
		//2.3.0 Select free product
		woocommerce_wp_checkbox( array(
			'id'          => '_wjecf_must_select_free_product',
			'label'       => __( 'Select one', 'woocommerce-jos-autocoupon' ),
			'description' => __( 'Check this box if the customer must choose from the free products.', 'woocommerce-jos-autocoupon' )
		) );

		//=============================
		//2.3.0 Select free product
		$message = $this->get_select_free_product_message( $thepostid, 'raw' );
		woocommerce_wp_text_input( array(
			'id'          => '_wjecf_select_free_product_message',
			'label'       => __( '\'Select your gift\'-message', 'woocommerce-jos-autocoupon' ),
			'placeholder' => __( 'Please choose your free gift:', 'woocommerce-jos-autocoupon' ),
			'description' => __( 'This message is displayed when the customer must choose a free product.', 'woocommerce-jos-autocoupon' ),
			'desc_tip'    => true,
			'value' => $message
		) );

		//=============================
		//2.2.2 Allow multiplying the free products
		woocommerce_wp_checkbox( array(
			'id'          => '_wjecf_multiply_free_products',
			'label'       => __( 'Allow multiplication of the free products', 'woocommerce-jos-autocoupon' ),
			'description' => '<b>' . __( 'EXPERIMENTAL: ', 'woocommerce-jos-autocoupon' ) . '</b>' . __( 'The amount of free products is multiplied every time the minimum spend, subtotal or quantity is reached.', 'woocommerce-jos-autocoupon' )
		) );

		//=============================
		//2.2.5 BOGO All matching products
		woocommerce_wp_checkbox( array(
			'id'          => '_wjecf_bogo_matching_products',
			'label'       => __( 'BOGO matching products', 'woocommerce-jos-autocoupon' ),
			'description' => '<b>' . __( 'EXPERIMENTAL: ', 'woocommerce-jos-autocoupon' ) . '</b>' 
			. __( 'Buy one or more of any of the matching products (see \'Usage Restriction\'-tab) and get one free. Check \'Allow multiplication\' to get one free item for every matching item in the cart.', 'woocommerce-jos-autocoupon' )
		) );		

	}
	
	public function process_shop_coupon_meta( $post_id, $post ) {
		$wjecf_free_product_ids = WJECF_ADMIN()->comma_separated_int_array( $_POST['wjecf_free_product_ids'] );
		update_post_meta( $post_id, '_wjecf_free_product_ids', $wjecf_free_product_ids );		

		$wjecf_coupon_multiplies = isset( $_POST['_wjecf_multiply_free_products'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wjecf_multiply_free_products', $wjecf_coupon_multiplies);

		$wjecf_bogo_matching_products = isset( $_POST['_wjecf_bogo_matching_products'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wjecf_bogo_matching_products', $wjecf_bogo_matching_products);

		//2.3.0
		$wjecf_select_free_product = isset( $_POST['_wjecf_must_select_free_product'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wjecf_must_select_free_product', $wjecf_select_free_product);
		
		//2.3.0
		$wjecf_select_free_product_message = isset( $_POST['_wjecf_select_free_product_message'] ) ? $_POST['_wjecf_select_free_product_message'] : '';
		update_post_meta( $post_id, '_wjecf_select_free_product_message', wp_kses_post( $wjecf_select_free_product_message ) );
	}	

/* FRONTEND HOOKS */

/* INTERNAL */	
	/**
	 * Get array of the free product ids.
	 * @param  int $coupon_id The coupon id
	 * @return array Id's of the free products or an empty array.
	 */	
	public function get_coupon_free_product_ids( $coupon_id ) {
		$v = array_filter( array_map( 'absint', explode( ',', get_post_meta( $coupon_id, '_wjecf_free_product_ids', true ) ) ) );
		return $v;
	}

	/**
	 * Must a free product be selected?
	 * @param type $coupon_id 
	 * @return bool
	 */
	public function must_select_free_product( $coupon_id ) {
		return get_post_meta( $coupon_id, '_wjecf_must_select_free_product', true ) == 'yes';
	}

	/**
	 * Get the 'select free gift'-message.
	 * @param int $coupon_id 
	 * @param string $context 'raw' or 'display'. If display is used, the translated value will be retrieved
	 * @return string|bool will be false if raw and empty.
	 */
	public function get_select_free_product_message( $coupon_id, $context = 'display' ) {
		$message = get_post_meta( $coupon_id, '_wjecf_select_free_product_message', true );
		if ( empty( $message ) ) {
			$message = 'Please choose your free gift:'; //Default
		}
		if ( $context == 'raw' ) {
			return $message;
		}
		//Get translated, escaped value
		return __( $message, 'woocommerce-jos-autocoupon' );

	}

	/**
	 * Get the 'select a free gift'-coupons that are currently in the cart
	 * @return array Array of WC_Coupon objects
	 */
	public function get_applied_select_free_product_coupons() {
		$coupons = array();
		foreach( WC()->cart->get_applied_coupons() as $coupon_code ) {
		    $coupon = new WC_Coupon( $coupon_code );
		    if ( $this->must_select_free_product( $coupon->id ) ) {
		    	$coupons[] = $coupon;
		    }
		}
		return $coupons;
	}


	private $session_selected_products = null;
	/**
	 * Gets the id of the selected product for the given coupon or false if nothing selected
	 * @param type $coupon_code 
	 * @return int|bool false if not found
	 */
	public function get_session_selected_product( $coupon_code ) {
		if ( $this->session_selected_products == null ) {
			$this->session_selected_products = WJECF()->get_session( 'selected_free_products', array() );
		}
		return isset( $this->session_selected_products[ $coupon_code ] ) ? $this->session_selected_products[ $coupon_code ] : false;
	}

	public function set_session_selected_product( $coupon_code, $product_id ) {
		if ( $this->session_selected_products == null ) {
			$this->session_selected_products = WJECF()->get_session( $coupon_code . 'selected_free_products', array() );
		}
		$this->session_selected_products[ $coupon_code ] = $product_id;
		WJECF()->set_session( 'selected_free_products', $this->session_selected_products );
//		WJECF()->set_session( $coupon_code . '_selected_product', $product_id );
	}

	public function clear_session_selected_products() {
		$this->session_selected_products = array();
		WJECF()->set_session( 'selected_free_products', $this->session_selected_products );
	}


	/**
	 * 1 if it's not a multiplying coupon, otherwise 1 or more
	 */
	public function get_product_multiplier_value( $coupon ) {
		if ( is_string( $coupon) ) {
			$coupon = new WC_Coupon( $coupon );
		}

		$coupon_multiplies = get_post_meta( $coupon->id, '_wjecf_multiply_free_products', true ) == 'yes';
		if ( ! $coupon_multiplies ) {
			return 1;
		}

		return WJECF()->get_coupon_multiplier_value( $coupon );
	}
	
}