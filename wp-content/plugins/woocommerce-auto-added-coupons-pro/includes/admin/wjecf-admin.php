<?php

defined('ABSPATH') or die();

if ( class_exists('WJECF_Admin') ) {
	return;
}

class WJECF_Admin extends Abstract_WJECF_Plugin {
	
	public function __construct() {    
		$this->set_plugin_data( array(
			'description' => __( 'Admin interface of WooCommerce Extended Coupon Features.', 'woocommerce-jos-autocoupon' ),
			'dependencies' => array(),
			'can_be_disabled' => false
		) );
	}

	public function init_admin_hook() {
        add_action( 'admin_notices', array( $this, 'admin_notices'));

		if ( ! WJECF_WC()->check_woocommerce_version('2.3.0') ) {
			$this->enqueue_notice( '<p>' . 
				__( '<strong>WooCommerce Extended Coupon Features:</strong> You are using an old version of WooCommerce. Updating of WooCommerce is recommended as using an outdated version might cause unexpected behaviour in combination with modern plugins.' ) 
				. '</p>', 'notice-warning' );
		}
		//Admin hooks
		add_filter( 'plugin_row_meta', array( $this, 'wjecf_plugin_meta' ), 10, 2 );
        add_action( 'admin_head', array( $this, 'on_admin_head'));

		add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'admin_coupon_options_tabs' ), 10, 1);
		add_action( 'woocommerce_coupon_data_panels', array( $this, 'admin_coupon_options_panels' ), 10, 0 );
		add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'process_shop_coupon_meta' ), 10, 2 );		
		
		add_action( 'wjecf_coupon_metabox_products', array( $this, 'admin_coupon_metabox_products' ), 10, 2 );
		add_action( 'wjecf_coupon_metabox_checkout', array( $this, 'admin_coupon_metabox_checkout' ), 10, 2 );
		add_action( 'wjecf_coupon_metabox_customer', array( $this, 'admin_coupon_metabox_customer' ), 10, 2 );
		add_action( 'wjecf_coupon_metabox_misc', array( $this, 'admin_coupon_metabox_misc' ), 10, 2 );

		WJECF_ADMIN()->add_inline_style( '
			#woocommerce-coupon-data .wjecf-not-wide { width:50% }
		');		

        //WORK IN PROGRESS: add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
	}	

// ===========================================================================
// START - ADMIN NOTICES
// Allows notices to be displayed on the admin pages
// ===========================================================================

	private $notices = array();
	
	/**
	 * Enqueue a notice to display on the admin page
	 * @param stirng $html Please embed in <p> tags
	 * @param string $class 
	 */
	public function enqueue_notice( $html, $class = 'notice-info' ) {
		$this->notices[] = array( 'class' => $class, 'html' => $html );
	}

	public function admin_notices() {
		foreach( $this->notices as $notice ) {
			echo '<div class="notice ' . $notice['class'] . '">';
			echo $notice['html'];
			echo '</div>';
		}
	}	

// ===========================================================================
// END - ADMIN NOTICES
// ===========================================================================

    public function action_admin_menu() {
        add_options_page( __( 'WooCommerce Extended Coupon Features', 'soft79-wc-pricing-rules' ), __( 'WooCommerce Extended Coupon Features', 'soft79-wc-pricing-rules' ), 'manage_options', 'wjecf_settings', array( &$this, 'action_admin_config_page' ) ); 
    }

    public function action_admin_config_page() {
?>
        <h2><?php _e( 'WooCommerce Extended Coupon Features', 'soft79-wc-pricing-rules' ); ?></h2>
        <form method="post" action="options.php"> 
        <?php 
        settings_fields( 'wjecf_settings' );
        do_settings_sections( 'wjecf_settings' );
        ?>
        <?php submit_button(); ?>
        </form>
        <h3><?php _e( 'Plugins', 'soft79-wc-pricing-rules' ); ?></h3>
        <ul>
<?php
        foreach ( WJECF()->get_plugins() as $name => $plugin ) {
        	echo "<li><h3>" . $plugin->get_plugin_class_name() . "</h3>\n";
        	echo "<p>" . $plugin->get_plugin_description() . "</p>\n";
        	echo "</li>\n";
        }
?>
		</ul>
<?php
        
    }   

	//2.3.6 Inline css
	private $admin_css = '';

	/**
	 * 2.3.6
	 * @return void
	 */
	function on_admin_head() {
	 	//Output inline style for the admin pages
		if ( ! empty( $this->admin_css ) ) {
			echo '<style type="text/css">' . $this->admin_css . '</style>';
			$this->admin_css = '';
		}

		//Enqueue scripts
		wp_enqueue_script( "wjecf-admin", WJECF()->plugin_url . "assets/js/wjecf-admin.js", array( 'jquery' ), WJECF()->version );
		wp_localize_script( 'wjecf-admin', 'wjecf_admin_i18n', array(
			'label_and' => __( '(AND)', 'woocommerce-jos-autocoupon' ),
			'label_or' => __( '(OR)',  'woocommerce-jos-autocoupon' )
		) );

	}


	//Add tabs to the coupon option page
	public function admin_coupon_options_tabs( $tabs ) {
		
		$tabs['extended_features_products'] = array(
			'label'  => __( 'Products', 'woocommerce-jos-autocoupon' ),
			'target' => 'wjecf_coupondata_products',
			'class'  => 'wjecf_coupondata_products',
		);

		$tabs['extended_features_checkout'] = array(
			'label'  => __( 'Checkout', 'woocommerce-jos-autocoupon' ),
			'target' => 'wjecf_coupondata_checkout',
			'class'  => 'wjecf_coupondata_checkout',
		);

		$tabs['extended_features_misc'] = array(
			'label'  => __( 'Miscellaneous', 'woocommerce-jos-autocoupon' ),
			'target' => 'wjecf_coupondata_misc',
			'class'  => 'wjecf_coupondata_misc',
		);

		return $tabs;
	}	

	//Add panels to the coupon option page
	public function admin_coupon_options_panels() {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		?>
			<div id="wjecf_coupondata_products" class="panel woocommerce_options_panel">
				<?php
					//Feed the panel with options
					do_action( 'wjecf_coupon_metabox_products', $thepostid, $post );
					$this->admin_coupon_data_footer();
				?>
			</div>
			<div id="wjecf_coupondata_checkout" class="panel woocommerce_options_panel">
				<?php
					do_action( 'wjecf_coupon_metabox_checkout', $thepostid, $post );
					do_action( 'wjecf_coupon_metabox_customer', $thepostid, $post );
					$this->admin_coupon_data_footer();
				?>
			</div>
			<div id="wjecf_coupondata_misc" class="panel woocommerce_options_panel">
				<?php
					//Allow other classes to inject options
					do_action( 'wjecf_woocommerce_coupon_options_extended_features', $thepostid, $post );
					do_action( 'wjecf_coupon_metabox_misc', $thepostid, $post );
					$this->admin_coupon_data_footer();
				?>
			</div>
		<?php		
	}

	public function admin_coupon_data_footer() {
		$documentation_url = plugins_url( 'docs/index.html', dirname( __FILE__ ) );
		if ( ! WJECF()->is_pro() ) {
			$documentation_url = 'http://www.soft79.nl/documentation/wjecf';
			?>			
			<h3><?php _e( 'Do you find WooCommerce Extended Coupon Features useful?', 'woocommerce-jos-autocoupon'); ?></h3>
			<p class="form-field"><label for="wjecf_donate_button"><?php
				echo esc_html( __('Express your gratitude', 'woocommerce-jos-autocoupon' ) );	
			?></label>
			<a id="wjecf_donate_button" href="<?php echo $this->get_donate_url(); ?>" target="_blank" class="button button-primary">
			<?php
				echo esc_html( __('Donate to the developer', 'woocommerce-jos-autocoupon' ) );	
			?></a><br>
			Or get the PRO version at <a href="http://www.soft79.nl" target="_blank">www.soft79.nl</a>.
			</p>
			<?php
		}
		//Documentation link
		echo '<h3>' . __( 'Documentation', 'woocommerce-jos-autocoupon' ) . '</h3>';
		echo '<p><a href="' . $documentation_url . '" target="_blank">' . 
		 	__( 'WooCommerce Extended Coupon Features Documentation', 'woocommerce-jos-autocoupon' ) . '</a></p>';

	}

	//Tab 'extended features'
	public function admin_coupon_metabox_products( $thepostid, $post ) {
		//See WooCommerce class-wc-meta-box-coupon-data.php function ouput
		
		echo "<h3>" . esc_html( __( 'Matching products', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";
		//=============================
		// AND instead of OR the products
		$this->render_select_with_default( array(
			'id' => '_wjecf_products_and', 
			'label' => __( 'Products Operator', 'woocommerce-jos-autocoupon' ), 
			'options' => array( 'no' => __( 'OR', 'woocommerce-jos-autocoupon' ), 'yes' => __( 'AND', 'woocommerce-jos-autocoupon' ) ),
			'default_value' => 'no',
			'class' => 'wjecf-not-wide',
			/* translators: OLD TEXT:  'Check this box if ALL of the products (see tab \'usage restriction\') must be in the cart to use this coupon (instead of only one of the products).' */
			'description' => __( 'Use AND if ALL of the products must be in the cart to use this coupon (instead of only one of the products).', 'woocommerce-jos-autocoupon' ),
			'desc_tip' => true
		) );		

		//=============================
		// 2.2.3.1 AND instead of OR the categories
		$this->render_select_with_default( array(
			'id' => '_wjecf_categories_and', 
			'label' => __( 'Categories Operator', 'woocommerce-jos-autocoupon' ), 
			'options' => array( 'no' => __( 'OR', 'woocommerce-jos-autocoupon' ), 'yes' => __( 'AND', 'woocommerce-jos-autocoupon' ) ),
			'default_value' => 'no',
			'class' => 'wjecf-not-wide',
			/* translators: OLD TEXT:  'Check this box if products from ALL of the categories (see tab \'usage restriction\') must be in the cart to use this coupon (instead of only one from one of the categories).' */
			'description' => __( 'Use AND if products from ALL of the categories must be in the cart to use this coupon (instead of only one from one of the categories).', 'woocommerce-jos-autocoupon' ),
			'desc_tip' => true
		) );		



		// Minimum quantity of matching products (product/category)
		woocommerce_wp_text_input( array( 
			'id' => '_wjecf_min_matching_product_qty', 
			'label' => __( 'Minimum quantity of matching products', 'woocommerce-jos-autocoupon' ), 
			'placeholder' => __( 'No minimum', 'woocommerce' ), 
			'description' => __( 'Minimum quantity of the products that match the given product or category restrictions (see tab \'usage restriction\'). If no product or category restrictions are specified, the total number of products is used.', 'woocommerce-jos-autocoupon' ), 
			'data_type' => 'decimal', 
			'desc_tip' => true
		) );
		
		// Maximum quantity of matching products (product/category)
		woocommerce_wp_text_input( array( 
			'id' => '_wjecf_max_matching_product_qty', 
			'label' => __( 'Maximum quantity of matching products', 'woocommerce-jos-autocoupon' ), 
			'placeholder' => __( 'No maximum', 'woocommerce' ), 
			'description' => __( 'Maximum quantity of the products that match the given product or category restrictions (see tab \'usage restriction\'). If no product or category restrictions are specified, the total number of products is used.', 'woocommerce-jos-autocoupon' ), 
			'data_type' => 'decimal', 
			'desc_tip' => true
		) );		

		// Minimum subtotal of matching products (product/category)
		woocommerce_wp_text_input( array( 
			'id' => '_wjecf_min_matching_product_subtotal', 
			'label' => __( 'Minimum subtotal of matching products', 'woocommerce-jos-autocoupon' ), 
			'placeholder' => __( 'No minimum', 'woocommerce' ), 
			'description' => __( 'Minimum price subtotal of the products that match the given product or category restrictions (see tab \'usage restriction\').', 'woocommerce-jos-autocoupon' ), 
			'data_type' => 'price', 
			'desc_tip' => true
		) );

		// Maximum subtotal of matching products (product/category)
		woocommerce_wp_text_input( array( 
			'id' => '_wjecf_max_matching_product_subtotal', 
			'label' => __( 'Maximum subtotal of matching products', 'woocommerce-jos-autocoupon' ), 
			'placeholder' => __( 'No maximum', 'woocommerce' ), 
			'description' => __( 'Maximum price subtotal of the products that match the given product or category restrictions (see tab \'usage restriction\').', 'woocommerce-jos-autocoupon' ), 
			'data_type' => 'price', 
			'desc_tip' => true
		) );
	}

	public function admin_coupon_metabox_checkout( $thepostid, $post ) {

		echo "<h3>" . esc_html( __( 'Checkout', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";

		//=============================
		// Shipping methods
		?>
		<p class="form-field"><label for="wjecf_shipping_methods"><?php _e( 'Shipping methods', 'woocommerce-jos-autocoupon' ); ?></label>
		<select id="wjecf_shipping_methods" name="wjecf_shipping_methods[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any shipping method', 'woocommerce-jos-autocoupon' ); ?>">
			<?php
				$coupon_shipping_method_ids = WJECF()->get_coupon_shipping_method_ids( $thepostid );
				$shipping_methods = WC()->shipping->load_shipping_methods();

				if ( $shipping_methods ) foreach ( $shipping_methods as $shipping_method ) {
					echo '<option value="' . esc_attr( $shipping_method->id ) . '"' . selected( in_array( $shipping_method->id, $coupon_shipping_method_ids ), true, false ) . '>' . esc_html( $shipping_method->method_title ) . '</option>';
				}
			?>
		</select><?php echo WJECF_WC()->wc_help_tip( __( 'One of these shipping methods must be selected in order for this coupon to be valid.', 'woocommerce-jos-autocoupon' ) ); ?>
		</p>
		<?php		
		
		//=============================
		// Payment methods
		?>
		<p class="form-field"><label for="wjecf_payment_methods"><?php _e( 'Payment methods', 'woocommerce-jos-autocoupon' ); ?></label>
		<select id="wjecf_payment_methods" name="wjecf_payment_methods[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any payment method', 'woocommerce-jos-autocoupon' ); ?>">
			<?php
				$coupon_payment_method_ids = WJECF()->get_coupon_payment_method_ids( $thepostid );
				//DONT USE WC()->payment_gateways->available_payment_gateways() AS IT CAN CRASH IN UNKNOWN OCCASIONS
				$payment_methods = WC()->payment_gateways->payment_gateways();
				if ( $payment_methods ) foreach ( $payment_methods as $payment_method ) {
					if ('yes' === $payment_method->enabled) {
						echo '<option value="' . esc_attr( $payment_method->id ) . '"' . selected( in_array( $payment_method->id, $coupon_payment_method_ids ), true, false ) . '>' . esc_html( $payment_method->title ) . '</option>';
					}
				}
			?>
		</select><?php echo WJECF_WC()->wc_help_tip( __( 'One of these payment methods must be selected in order for this coupon to be valid.', 'woocommerce-jos-autocoupon' ) ); ?>
		</p>
		<?php		
	}

	public function admin_coupon_metabox_customer( $thepostid, $post ) {

		//=============================
		//Title: "CUSTOMER RESTRICTIONS"
		echo "<h3>" . esc_html( __( 'Customer restrictions', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";
		echo "<p><span class='description'>" . __( 'If both a customer and a role restriction are supplied, matching either one of them will suffice.' , 'woocommerce-jos-autocoupon' ) . "</span></p>\n";
		
		//=============================
		// User ids
		?>
		<p class="form-field"><label><?php _e( 'Allowed Customers', 'woocommerce-jos-autocoupon' ); ?></label>
		<input type="hidden" class="wc-customer-search" data-multiple="true" style="width: 50%;" name="wjecf_customer_ids" data-placeholder="<?php _e( 'Any customer', 'woocommerce-jos-autocoupon' ); ?>" data-action="woocommerce_json_search_customers" data-selected="<?php
			$coupon_customer_ids = WJECF()->get_coupon_customer_ids( $thepostid );
			$json_ids    = array();
			
			foreach ( $coupon_customer_ids as $customer_id ) {
				$customer = get_userdata( $customer_id );
				if ( is_object( $customer ) ) {
					$json_ids[ $customer_id ] = $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . sanitize_email( $customer->user_email ) . ')';
				}
			}

			echo esc_attr( json_encode( $json_ids ) );
		?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" /> 
		<?php echo WJECF_WC()->wc_help_tip( __( 'Only these customers may use this coupon.', 'woocommerce-jos-autocoupon' ) ); ?>
		</p>
		<?php

		//=============================
		// User roles
		?>
		<p class="form-field"><label for="wjecf_customer_roles"><?php _e( 'Allowed User Roles', 'woocommerce-jos-autocoupon' ); ?></label>
		<select id="wjecf_customer_roles" name="wjecf_customer_roles[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any role', 'woocommerce-jos-autocoupon' ); ?>">
			<?php			
				$coupon_customer_roles = WJECF()->get_coupon_customer_roles( $thepostid );

				$available_customer_roles = array_reverse( get_editable_roles() );
				foreach ( $available_customer_roles as $role_id => $role ) {
					$role_name = translate_user_role($role['name'] );
	
					echo '<option value="' . esc_attr( $role_id ) . '"'
					. selected( in_array( $role_id, $coupon_customer_roles ), true, false ) . '>'
					. esc_html( $role_name ) . '</option>';
				}
			?>
		</select>
 		<?php echo WJECF_WC()->wc_help_tip( __( 'Only these User Roles may use this coupon.', 'woocommerce-jos-autocoupon' ) ); ?>
		</p>
		<?php	

		//=============================
		// Excluded user roles
		?>
		<p class="form-field"><label for="wjecf_excluded_customer_roles"><?php _e( 'Disallowed User Roles', 'woocommerce-jos-autocoupon' ); ?></label>
		<select id="wjecf_customer_roles" name="wjecf_excluded_customer_roles[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any role', 'woocommerce-jos-autocoupon' ); ?>">
			<?php			
				$coupon_excluded_customer_roles = WJECF()->get_coupon_excluded_customer_roles( $thepostid );

				foreach ( $available_customer_roles as $role_id => $role ) {
					$role_name = translate_user_role($role['name'] );
	
					echo '<option value="' . esc_attr( $role_id ) . '"'
					. selected( in_array( $role_id, $coupon_excluded_customer_roles ), true, false ) . '>'
					. esc_html( $role_name ) . '</option>';
				}
			?>
		</select>
 		<?php echo WJECF_WC()->wc_help_tip( __( 'These User Roles will be specifically excluded from using this coupon.', 'woocommerce-jos-autocoupon' ) ); ?>
		</p>
		<?php	
	}

	public function admin_coupon_metabox_misc( $thepostid, $post ) {
		echo "<h3>" . esc_html( __( 'Miscellaneous', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";
		//=============================
		//2.2.2 Allow if minimum spend not met
		woocommerce_wp_checkbox( array(
			'id'          => '_wjecf_allow_below_minimum_spend',
			'label'       => __( 'Allow when minimum spend not reached', 'woocommerce-jos-autocoupon' ),
			'description' => '<b>' . __( 'EXPERIMENTAL: ', 'woocommerce-jos-autocoupon' ) . '</b>' . __( 'Check this box to allow the coupon to be in the cart even when minimum spend (see tab \'usage restriction\') is not reached. Value of the discount will be 0 until minimum spend is reached.', 'woocommerce-jos-autocoupon' ),
		) );
	}

	public function process_shop_coupon_meta( $post_id, $post ) {
		$wjecf_min_matching_product_qty = isset( $_POST['_wjecf_min_matching_product_qty'] ) ? $_POST['_wjecf_min_matching_product_qty'] : '';
		update_post_meta( $post_id, '_wjecf_min_matching_product_qty', $wjecf_min_matching_product_qty );
				
		$wjecf_max_matching_product_qty = isset( $_POST['_wjecf_max_matching_product_qty'] ) ? $_POST['_wjecf_max_matching_product_qty'] : '';
		update_post_meta( $post_id, '_wjecf_max_matching_product_qty', $wjecf_max_matching_product_qty );

		//2.2.2
		$wjecf_min_matching_product_subtotal = isset( $_POST['_wjecf_min_matching_product_subtotal'] ) ? $_POST['_wjecf_min_matching_product_subtotal'] : '';
		update_post_meta( $post_id, '_wjecf_min_matching_product_subtotal', $wjecf_min_matching_product_subtotal );
				
		$wjecf_max_matching_product_subtotal = isset( $_POST['_wjecf_max_matching_product_subtotal'] ) ? $_POST['_wjecf_max_matching_product_subtotal'] : '';
		update_post_meta( $post_id, '_wjecf_max_matching_product_subtotal', $wjecf_max_matching_product_subtotal );

		$wjecf_products_and = $_POST['_wjecf_products_and'] == 'yes' ? 'yes' : 'no';
		update_post_meta( $post_id, '_wjecf_products_and', $wjecf_products_and );

		//2.2.3.1
		$wjecf_categories_and = $_POST['_wjecf_categories_and'] == 'yes' ? 'yes' : 'no';
		update_post_meta( $post_id, '_wjecf_categories_and', $wjecf_categories_and );
		
		//2.2.2
		$wjecf_allow_below_minimum_spend = isset( $_POST['_wjecf_allow_below_minimum_spend'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wjecf_allow_below_minimum_spend', $wjecf_allow_below_minimum_spend );
		
		$wjecf_shipping_methods = isset( $_POST['wjecf_shipping_methods'] ) ? $_POST['wjecf_shipping_methods'] : '';
		update_post_meta( $post_id, '_wjecf_shipping_methods', $wjecf_shipping_methods );		
		
		$wjecf_payment_methods = isset( $_POST['wjecf_payment_methods'] ) ? $_POST['wjecf_payment_methods'] : '';
		update_post_meta( $post_id, '_wjecf_payment_methods', $wjecf_payment_methods );		
		
		$wjecf_customer_ids = $this->comma_separated_int_array( $_POST['wjecf_customer_ids'] );
		update_post_meta( $post_id, '_wjecf_customer_ids', $wjecf_customer_ids );	

		$wjecf_customer_roles    = isset( $_POST['wjecf_customer_roles'] ) ? $_POST['wjecf_customer_roles'] : '';
		update_post_meta( $post_id, '_wjecf_customer_roles', $wjecf_customer_roles );	

		$wjecf_excluded_customer_roles = isset( $_POST['wjecf_excluded_customer_roles'] ) ? $_POST['wjecf_excluded_customer_roles'] : '';
		update_post_meta( $post_id, '_wjecf_excluded_customer_roles', $wjecf_excluded_customer_roles );	
		
	}

	/**
	 * 2.3.6
	 * Add inline style (css) to the admin page. Must be called BEFORE admin_head !
	 * @param string $css 
	 * @return void
	 */
	public function add_inline_style( $css ) {
		$this->admin_css .= $css;
	}


	/**
	 * 2.3.4
	 * Parse an array or comma separated string; make sure they are valid ints and return as comma separated string
	 * @param array|string $int_array 
	 * @return string comma separated int array
	 */
	public function comma_separated_int_array( $int_array ) {
		//Source can be a comma separated string (select2) , or an int array (chosen)
		if ( ! is_array( $int_array) ) {
			$int_array = explode( ',', $int_array );
		}
        return implode( ',', array_filter( array_map( 'intval', $int_array ) ) );
	}

	/**
	 * 2.3.6
	 * Renders a <SELECT> that has a default value. Relies on woocommerce_wp_select
	 * 
	 * field['default_value']:  The default value (if omitted the first option will be default)
	 * field['append_default_label']: If true or omitted the text '(DEFAULT)' will be appended to the default option caption
	 * 
	 * @param array $field see wc-meta-box-functions.php:woocommerce_wp_select
	 * @return void
	 */
	public function render_select_with_default( $field ) {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

		reset( $field['options'] ); //move to first for key()
		$default_value = isset( $field['default_value'] ) ? $field['default_value'] : key( $field['options'] );
		$append_default_label = isset( $field['append_default_label'] ) ? $field['append_default_label'] : true;

		if ( $append_default_label ) {
			$field['options'][$default_value] = sprintf( __( '%s (Default)', 'woocommerce-jos-autocoupon' ), $field['options'][$default_value] );
		}

		if ( ! isset( $field['value'] ) ) {
			$field['value'] = get_post_meta( $thepostid, $field['id'], true );
		}
		if ( empty( $field['value'] ) ) {
			$field['value'] = $default_value;
		}

		woocommerce_wp_select( $field );
	}

	public function render_admin_cat_selector( $dom_id, $field_name, $selected_ids, $placeholder = null ) {
		if ( $placeholder === null ) $placeholder = __( 'Search for a product…', 'woocommerce' );

		// Categories
		?>				
		<select id="<?php esc_attr_e( $dom_id ) ?>" name="<?php esc_attr_e( $field_name ) ?>[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( $placeholder ); ?>">
			<?php
				$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

				if ( $categories ) foreach ( $categories as $cat ) {
					echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $selected_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
				}
			?>
		</select>
		<?php	
	}

	/**
	 * Add donate-link to plugin page
	 */
	function wjecf_plugin_meta( $links, $file ) {
		if ( strpos( $file, 'woocommerce-jos-autocoupon.php' ) !== false ) {
			$links = array_merge( $links, array( '<a href="' . WJECF_Admin::get_donate_url() . '" title="Support the development" target="_blank">Donate</a>' ) );
		}
		return $links;
	}


	public static function get_donate_url() {
		return "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5T9XQBCS2QHRY&lc=NL&item_name=Jos%20Koenis&item_number=wordpress%2dplugin&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted";
	}
}
