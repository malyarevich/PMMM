<?php
/**
 * Product Helper class: Fetch and analyze products
 *
 * @static
 * @package  woocommerce-bacchus-gold-member
 * @subpackage lib/helpers
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 */
class WBGM_Product_Helper
{
	/**
	 * Fetch products based on given conditions
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @param  array  $options Query params
	 * @return WP_Query|null
	 */
	public static function get_products( $options = array(), $limit = 1000 )
	{
		$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => $limit,
			    'cache_results' => false
			);

		//merge default and user options
		$args = array_merge( $args, $options );

		$products = new WP_Query( $args );
		wp_reset_postdata();

		return $products;
	}

	/**
	 * Fetch all product categories
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return array<string,integer>
	 */
	public static function get_product_categories()
	{
		$args = array(
			'taxonomy'     => 'product_cat',
			'orderby'      => 'name',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
			'title_li'     => '',
			'hide_empty'   => 0
		);

		return get_categories( $args );
	}

    /**
     * Get id of special offer. Fetch product tag.
     *
     * @since  0.0.0
     * @access public
     * @static
     *
     * @param  int $prod_id Product to get tag
     *
     * @return int|bool
     */
    public static function get_product_tags($prod_id)
    {
        $sep = '|';
        $before = '';
        $after = '';
        $tags = get_the_term_list( $prod_id, 'product_tag', $before, $sep, $after );
        $terms = get_the_terms( $prod_id, 'product_tag' );

        preg_match_all('@rel="tag">(.?)</a>@', $tags, $match);
        $so_id = $match[1][0];

        if (is_int(intval($so_id))) {
            return intval($so_id);
        } else {
            foreach ($match as $so_row => $so_item) {
                if (is_int(intval($so_item[0]))) {
                    $so_id = $so_item[0];
                    return intval($so_id);
                }
            }
            return false;
        }
    }

	/**
	 * Fetch items added to the cart.
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return array<String> Items in cart
	 */
	public static function get_cart_products()
	{
		global $woocommerce;
		$cart_items = $woocommerce->cart->get_cart();

		$added_products = array();
		$added_products['count'] = count( $cart_items );
		if( ! empty($cart_items) ) {
			foreach( $cart_items as $cart_item ) {
				$added_products['ids'][] = $cart_item['product_id'];
				$added_products['objects'][] = $cart_item['data'];
			}
		}

		return $added_products;
	}

	/**
	 * Fetch gift items added to the cart.
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return array<String> Gift items in cart
	 */
	public static function get_gift_products_in_cart()
	{
		$free_items = array();
		$cart_items = WC()->cart->cart_contents;
		if( empty($cart_items) ) {
			return $free_items;
		}

		foreach( $cart_items as $key => $content ) {
			$is_gift_product = ! empty( $content['variation_id'] );
			if(  $is_gift_product ) {
				$free_items[] = $content['product_id'];
			}
		}

		return $free_items;
	}

	/**
	 * Fetch required product details for given product.
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @param  integer $product_id Product to get details of
	 *
	 * @return object
	 */
	public static function get_product_details( $product_id )
	{
		$options = array( 'p' => $product_id );
		$product_details = self::get_products( $options );

		$wbgm_product_details = array();
		if( ! empty($product_details) && ! empty($product_details->posts) ) {
			$wbgm_product_details['detail'] = $product_details->post;
			$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_details->post->ID ), 'thumbnail' );
			$wbgm_product_details['image'] = isset($product_image[0]) ? $product_image[0] : false;
		}

		return (object) $wbgm_product_details;
	}

	/**
	 * Create variation product for given item.
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @param  integer $product_id Product to create variation of
	 *
	 * @return integer Product variation id
	 */
	public static function create_gift_variation( $product_id )
	{
		//check if product variation already exists
		$product_variation = get_posts( array(
								'post_parent' => $product_id,
								's' => '_wbgm_gift_product',
								'post_type' => 'product_variation',
								'posts_per_page' => 1
							)
					);

		if( ! empty($product_variation) ) {
			//make price zero and mark it as wbgm_product
			update_post_meta( $product_variation[0]->ID, '_price', 0 );
			update_post_meta( $product_variation[0]->ID, '_regular_price', 0 );
			update_post_meta( $product_variation[0]->ID, '_wbgm_gift_product', 1 );

			return $product_variation[0]->ID;
		}

		//if product variation doesn't exist, add one
		$admin = get_users( 'orderby=nicename&role=administrator&number=1' );
		$variation = array(
			'post_author' => $admin[0]->ID,
			'post_status' => 'publish',
			'post_name' => 'product-' . $product_id . '-variation',
			'post_parent' => $product_id,
			'post_title' => '_wbgm_gift_product',
			'post_type' => 'product_variation',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
		);

		$post_id = wp_insert_post( $variation );
		update_post_meta( $post_id, '_price', 0 );
		update_post_meta( $post_id, '_regular_price', 0 );
		update_post_meta( $post_id, '_wbgm_gift_product', 1 );

		return $post_id;
	}

	/**
	 * Add free gift item to cart.
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @param integer $parent_product_id Main product id
	 * @param integer $product_id        Product variation id
	 *
	 * @return  boolean
	 */
	public static function add_free_product_to_cart( $parent_product_id, $product_id, $count = 1 )
	{


		$found = false;
		//check if product is already in cart
		if( count( WC()->cart->get_cart() ) > 0 ) {
			foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if( $_product->id == $product_id ) {
					$found = true;
				}
			}

            $so_type_text = WBGM_Settings_Helper::get('type_text', false, 'global_options');
            if( false === $so_type_text ) {
                $so_type_text = WBGM_Common_Helper::translate( 'Typ' );
            }
            $so_free_item_text = WBGM_Settings_Helper::get('free_item_text', false, 'global_options');
            if( false === $so_free_item_text ) {
                $so_free_item_text = WBGM_Common_Helper::translate( 'Gratis Bacchus Gold Artikel' );
            }

			// if product not found, add it
			if( ! $found ) {
                WC()->cart->add_to_cart(
                    $product_id,
                    $count,
                    $parent_product_id,
                    array( $so_type_text => $so_free_item_text),
                    array( 'plugin' => 'wbgm')
                );
				return true;
			}
		}
		return false;
	}

	/**
	 * Count total product excluding gift items
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return integer
	 */
	public static function get_main_product_count()
	{
		$count = 0;
		foreach( WC()->cart->cart_contents as $key => $content ) {
			$is_gift_product = ! empty( $content['variation_id'] );
			if( ! $is_gift_product ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Count total quantity excluding gift items
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return integer
	 */
	public static function get_main_product_quantity_count()
	{
		$count = 0;
		foreach( WC()->cart->cart_contents as $key => $content ) {
			$is_gift_product = ! empty( $content['variation_id'] );
			if(  !$is_gift_product ) {
				$count += (int) $content['quantity'];
			}
		}

		return $count;
	}

	/**
	 * Category wise product count
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return integer
	 */
	public static function get_category_products_count()
	{
		$products = array();
		foreach( WC()->cart->cart_contents as $key => $content ) {
			$is_gift_product = ! empty( $content['variation_id'] );
			if(  !$is_gift_product ) {
				$terms = get_the_terms( $content['product_id'], 'product_cat' );
				if( !empty($terms) ) {
					foreach( $terms as $term ) {
						if( isset($products[ $term->term_id ]) ) {
							$products[ $term->term_id ] += 1;
						} else {
							$products[ $term->term_id ] = 1;
						}
					}
				}
			}
		}

		return $products;
	}

	/**
	 * Return max from category products count
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return integer
	 */
	public static function get_max_category_products_count()
	{
		$products = self::get_category_products_count();

		return ! empty( $products ) ? max( $products ) : 0;
	}

	/**
	 * Category wise quantity count
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return integer
	 */
	public static function get_category_quantity_count()
	{
		$products = array();
		foreach( WC()->cart->cart_contents as $key => $content ) {
			$is_gift_product = !empty( $content['variation_id'] );
			if(  ! $is_gift_product ) {
				$terms = get_the_terms( $content['product_id'], 'product_cat' );
				if( ! empty($terms) ) {
					foreach( $terms as $term ) {
						if( isset($products[ $term->term_id ]) ) {
							$products[ $term->term_id ] += $content['quantity'];
						} else {
							$products[ $term->term_id ] = $content['quantity'];
						}
					}
				}
			}
		}

		return $products;
	}

	/**
	 * Return max from category quantity count
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @return integer
	 */
	public static function get_max_category_quantity_count()
	{
		$products = self::get_category_quantity_count();

		return ! empty($products) ? max($products) : 0;
	}

}
