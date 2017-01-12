<?php

defined('ABSPATH') or die();

//require_once( 'wjecf-pro-evalmath.php' );

/**
 * Miscellaneous Pro functions
 */
class WJECF_Pro_Controller extends WJECF_Controller {

	/**
	 * Singleton Instance
	 *
	 * @static
	 * @return Singleton Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {    
		parent::__construct();
		add_action('init', array( &$this, 'pro_controller_init' ));
	}

	public function pro_controller_init() {
		if ( ! class_exists('WC_Coupon') ) {
			return;
		}
        add_action( 'admin_init', array( $this, 'admin_init' ) );

		//Coupon columns
		add_filter( 'manage_shop_coupon_posts_columns', array( $this, 'admin_shop_coupon_columns' ), 20, 1 );
		add_action( 'manage_shop_coupon_posts_custom_column', array( $this, 'admin_render_shop_coupon_columns' ), 2 );
		
		//Frontend hooks
		add_action('woocommerce_coupon_loaded', array( $this, 'woocommerce_coupon_loaded' ), 10, 1);
		add_action('wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10);

	}

/* ADMIN HOOKS */
	public function admin_init() {	
		//Admin hooks
		add_action( 'wjecf_coupon_metabox_products', array( $this, 'wjecf_coupon_metabox_products' ), 11 );
		add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'process_shop_coupon_meta' ), 10, 2 );		
	}

//Admin

	public function wjecf_coupon_metabox_products() {
		echo '<div class="options_group wjecf_hide_on_product_discount">';
		echo '<h3>' . __( 'Discount on cart with excluded products', 'woocommerce-jos-autocoupon') . '</h3>';

		//=============================
		//2.2.3 Allow even if excluded items in cart
		woocommerce_wp_checkbox( array(
			'id'          => '_wjecf_allow_cart_excluded',
			'label'       => __( 'Allow discount on cart with excluded items', 'woocommerce-jos-autocoupon' ),
			'description' => __( 'Check this box to allow a \'Cart Discount\' coupon to be applied even when excluded items are in the cart (see tab \'usage restriction\').', 'woocommerce-jos-autocoupon' ),
		) );	
		echo '</div>';
	}

	public function process_shop_coupon_meta( $post_id, $post ) {
		//2.2.3
		$wjecf_allow_cart_excluded = isset( $_POST['_wjecf_allow_cart_excluded'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wjecf_allow_cart_excluded', $wjecf_allow_cart_excluded );

	}

	private $inject_coupon_columns = array();
	/**
	 * Inject custom columns on the Coupon Admin Page
	 *
	 * @param string $column_key The key to identify the column
	 * @param string $caption The title to show in the header
	 * @param callback $callback The function to call when rendering the column value ( Will be called with parameters $column_key, $post )
	 * @param string $after_column Optional, The key of the column after which the column should be injected, if omitted the column will be placed at the end
	 */
	public function inject_coupon_column( $column_key, $caption, $callback, $after_column = null ) {
		$this->inject_coupon_columns[ $column_key ] = array('caption' => $caption, 'callback' => $callback, 'after' => $after_column);
	}

	/**
     * Custom columns on coupon admin page
     *
     * @param array $columns
	 */
	public function admin_shop_coupon_columns( $columns ) {
		$new_columns = array();
		foreach( $columns as $key => $column ) {
			$new_columns[$key] = $column;
			foreach( $this->inject_coupon_columns as $inject_key => $inject_column ) {
				if ( $inject_column['after'] == $key ) {
					$new_columns[$inject_key] = $inject_column['caption'];
				}
			}
		}
		foreach( $this->inject_coupon_columns as $inject_key => $inject_column ) {
			if ( $inject_column['after'] == null || ! isset( $columns[ $inject_column['after'] ] ) ) {
				$new_columns[$inject_key] = $inject_column['caption'];
			}
		}
		return $new_columns;
	}

	/**
	 * Output custom columns for coupons
	 *
	 * @param string $column
	 */
	public function admin_render_shop_coupon_columns( $column ) {
		global $post;
		if ( isset( $this->inject_coupon_columns[$column]['callback'] ) ) {
			call_user_func( $this->inject_coupon_columns[$column]['callback'], $column, $post );
		}
	}		

//Frontend

	public function woocommerce_coupon_loaded ( $coupon ) {
		if ( ! is_admin() ) {
			//2.2.3 Allow coupon even if excluded products are not in the cart (required for Cart Discount with excluded products)
			if ( get_post_meta( $coupon->id, '_wjecf_allow_cart_excluded', true ) == 'yes' ) {
				//HACK: Overwrite the exclusions so WooCommerce will allow the coupon
				//These values are used in the WJECF_Controller->coupon_is_valid_for_product
				$this->overwrite_value( $coupon, 'exclude_product_ids', array() );
				$this->overwrite_value( $coupon, 'exclude_product_categories', array() );
				$this->overwrite_value( $coupon, 'exclude_sale_items', 'no' );
			}
		}
	}


	/**
	 * Include stylesheet
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'wjecf-style', plugins_url('assets/wjecf.css', dirname( __FILE__ ) ), array() ); 
	}


}
