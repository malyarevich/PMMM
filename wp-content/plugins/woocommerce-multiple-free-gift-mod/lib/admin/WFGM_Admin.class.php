<?php
/**
 * Admin page
 *
 * @package woocommerce-multiple-free-gift-mod
 * @subpackage lib/admin
 *
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 */
class WFGM_Admin
{
	/**
	 * Constructor
	 *
	 * @access public
	 * @since 0.0.0
	 *
	 * @see	 add_action()
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'main_menu' ) );

		//enqueue necessary scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		//register ajax call to fetch products
		add_action( 'wp_ajax_product_list_callback', array( $this, 'ajax_product_list_callback' ) );
	}

	/**
	 * Add main menu page
	 *
	 * @access public
	 * @see  add_menu_page()
	 * 
	 * @return void
	 */
	public function main_menu()
	{
		add_menu_page(
			WFGM_Common_Helper::translate( 'WooCommerce Multiple Free Gift Mod' ),
			WFGM_Common_Helper::translate( 'Woo Free Gift Mod' ),
			'manage_options',
			'woocommerce-multiple-free-gift-mod',
			array( $this, 'main_menu_template' ),
			'dashicons-cart'
		);

		/*add_submenu_page(
			'woocommerce-multiple-free-gift-mod',
			WFGM_Common_Helper::translate( 'Gift Criteria' ) . ' - ' .
			WFGM_Common_Helper::translate( 'WooCommerce Multiple Free Gift Mod' ),
			WFGM_Common_Helper::translate( 'Gift Criteria' ),
			'manage_options',
			'woocommerce-multiple-free-gift-mod-criteria',
			array( $this, 'wfgm_criteria_template' )
		);*/

		add_submenu_page(
			'woocommerce-multiple-free-gift-mod',
			WFGM_Common_Helper::translate( 'General Settings' ) . ' - ' .
			WFGM_Common_Helper::translate( 'WooCommerce Multiple Free Gift Mod' ),
			WFGM_Common_Helper::translate( 'General Settings' ),
			'manage_options',
			'woocommerce-multiple-free-gift-mod-settings',
			array( $this, 'wfgm_general_settings' )
		);
	}

	/**
	 * Enqueue required styles and scirpts for admin
	 *
	 * @access public
	 * @since  0.0.0
	 *
	 * @see  wp_enqueue_style()
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts()
	{
		//enqueue styles
		wp_enqueue_style( 'wmfg-admin-styles', plugins_url( '/admin/css/wfgm-admin-styles.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( 'wfgm-selectize', plugins_url( '/admin/js/plugins/selectize/selectize.css', dirname( __FILE__ ) ) );

		//enqueue scripts
		wp_enqueue_script( 'wmfg-admin-scripts', plugins_url( '/admin/js/wfgm-admin-scripts.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-dialog' ) );
		wp_enqueue_script( 'wfgm-selectize-lib', plugins_url( '/admin/js/plugins/selectize/selectize.min.js', dirname( __FILE__ ) ), array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-dialog', false, array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-sortable', false, array( 'jquery' ) );

		wp_localize_script(
			'wmfg-admin-scripts',
			'WMFG_SPECIFIC',
			array(
				'loading_url' => plugins_url( '/admin/img/loading.gif', dirname( __FILE__ ) ),
			)
		);
	}

	/**
	 * Main settings page template
	 *
	 * @access public
	 * @since 0.0.0
	 * 
	 * @return void
	 */
	public function main_menu_template()
	{
		if( ( isset($_POST['_wfgm_global_hidden']) && 'Y' == $_POST['_wfgm_global_hidden'] )
				&& wp_verify_nonce( $_POST['_wfgm_global_nonce'], 'wfgm_global_settings' ) ) {

			$wfgm_globally_enabled = isset( $_POST['wfgm_globally_enabled'] ) ? true : false;
			$enabled = update_option( '_wfgm_global_enabled', $wfgm_globally_enabled );

			if( isset($_POST['_wfgm_criteria']) ) {
				$user_criteria = $_POST['_wfgm_criteria'];

				//remove extra fields
				unset( $user_criteria['_wfgm_global_nonce'] );
				unset( $user_criteria['_wp_http_referer'] );
				unset( $user_criteria['_wfgm_global_hidden'] );

				$conditionSaved = update_option( '_wfgm_global_settings', $user_criteria );
				if( $enabled || $conditionSaved ) {
					WFGM_Common_Helper::success_notice(
						WFGM_Common_Helper::translate(
							'Gift conditions saved successfully'
						)
					);

					WFGM_Settings_Helper::force_init();
				} else {
					WFGM_Common_Helper::error_notice(
						WFGM_Common_Helper::translate(
							'There was a problem. Please try again.'
						)
					);
				}
			} else {
				if( get_option( '_wfgm_global_settings' ) !== false ) {
					if( delete_option( '_wfgm_global_settings' ) ) {
						WFGM_Common_Helper::success_notice(
							WFGM_Common_Helper::translate(
								'Gift conditions emptied successfully'
							)
						);
					}
				} else {
					WFGM_Common_Helper::error_notice(
						WFGM_Common_Helper::translate(
							'No gift conditions to save. You can add conditions by clicking <em>Add new gift condition</em> button'
						)
					);
				}
			}

			//update settings
			WFGM_Settings_Helper::force_init();
		}

		include 'pages/main_menu_page.php';
	}

	public function wfgm_criteria_template()
	{
		if( ( isset($_POST['_wfgm_criteria_hidden']) && $_POST['_wfgm_criteria_hidden'] == 'Y' )
				&& wp_verify_nonce( $_POST['_wfgm_criteria_nonce'], 'wfgm_criteria_settings' ) ) {

			if( isset($_POST['_wfgm_criteria']) ) {
				$user_criteria = $_POST['_wfgm_criteria'];
				foreach( $user_criteria as &$criteria ) {
					$criteria['slug'] = sanitize_title( $criteria['name'] );
				}

				if( update_option( '_wfgm_criteria', $user_criteria ) ) {
					WFGM_Common_Helper::success_notice(
						WFGM_Common_Helper::translate(
							'Criteria saved successfully'
						)
					);

					WFGM_Settings_Helper::force_init();
				} else {
					WFGM_Common_Helper::error_notice(
						WFGM_Common_Helper::translate(
							'There was a problem. Please try again.'
						)
					);
				}
			} else {
				if( get_option( '_wfgm_criteria' ) !== false ) {
					if( delete_option( '_wfgm_criteria' ) ) {
						WFGM_Common_Helper::success_notice(
							WFGM_Common_Helper::translate(
								'Criteria saved successfully'
							)
						);
					}
				} else {
					WFGM_Common_Helper::error_notice(
						WFGM_Common_Helper::translate(
							'No criteria to save. You can add criteria by clicking <em>Add New Criteria</em> button'
						)
					);
				}
			}

			//update settings
			WFGM_Settings_Helper::force_init();
		}

		/*include 'pages/gift_criteria.php';*/
	}

	public function wfgm_general_settings()
	{
		if( ( isset($_POST['_wfgm_general_settings_submitted']) && $_POST['_wfgm_general_settings_submitted'] == 'Y' )
				&& wp_verify_nonce( $_POST['_wfgm_general_nonce'], 'wfgm_general_settings' ) ) {

			$popup_overlay = isset( $_POST['_wfgm_popup_overlay'] ) ? 1 : 0;

            $so_product_page_text = isset( $_POST['_wfgm_so_product_page'] )  ? $_POST['_wfgm_so_product_page'] : WFGM_Common_Helper::translate( 'On this product active special offer. For every {X} item we give {Y} free gift.' );
            $popup_so_product_page_enabled = isset( $_POST['_wfgm_so_product_page_enabled'] ) ? 1 : 0;
            $so_add_more_text = isset( $_POST['_wfgm_so_add_more'] )  ? $_POST['_wfgm_so_add_more'] : WFGM_Common_Helper::translate( 'Special offer. add more for gift.' );
            $popup_so_add_more_enabled = isset( $_POST['_wfgm_so_add_more_enabled'] ) ? 1 : 0;
            $so_congrat_text = isset( $_POST['_wfgm_so_congrat'] )  ? $_POST['_wfgm_so_congrat'] : WFGM_Common_Helper::translate( '{Y} x {title} free gift(s) were added to your cart.' );
            $popup_so_congrat_enabled = isset( $_POST['_wfgm_so_congrat_enabled'] ) ? 1 : 0;
            $so_congrat_save_money_text = isset( $_POST['_wfgm_so_congrat_save_money'] )  ? $_POST['_wfgm_so_congrat_save_money'] : WFGM_Common_Helper::translate( 'Congratulations! You save {sum(N*Y*price)} {currency}.' );
            $popup_so_congrat_save_money_enabled = isset( $_POST['_wfgm_so_congrat_save_money_enabled'] ) ? 1 : 0;
            $so_deleted_gift_text = isset( $_POST['_wfgm_so_deleted_gift'] )  ? $_POST['_wfgm_so_deleted_gift'] : WFGM_Common_Helper::translate( '{Y} x {title} free gift(s) were deleted from your cart.' );
            $popup_so_deleted_gift_enabled = isset( $_POST['_wfgm_so_deleted_gift_enabled'] ) ? 1 : 0;

			$popup_heading = isset( $_POST['_wfgm_popup_heading'] )  ? $_POST['_wfgm_popup_heading'] : WFGM_Common_Helper::translate( 'Take your free gift' );
            $popup_heading_msg = isset( $_POST['_wfgm_popup_heading_msg'] )  ? $_POST['_wfgm_popup_heading_msg'] : WFGM_Common_Helper::translate( 'Message for you!' );
			$invalid_text = isset( $_POST['_wfgm_invalid_condition_text'] )  ? $_POST['_wfgm_invalid_condition_text'] : WFGM_Common_Helper::translate( 'Gift items removed as gift criteria isnt fulfilled.' );
			$add_gift_text = isset( $_POST['_wfgm_popup_add_gift_text'] ) ? $_POST['_wfgm_popup_add_gift_text'] : WFGM_Common_Helper::translate( 'Add Gifts' );
			$cancel_text = isset( $_POST['_wfgm_popup_cancel_text'] ) ? $_POST['_wfgm_popup_cancel_text'] : WFGM_Common_Helper::translate( 'No Thanks' );
            $ok_text = isset( $_POST['_wfgm_ok_text'] ) ? $_POST['_wfgm_ok_text'] : WFGM_Common_Helper::translate( 'Okay' );
            $type_text = isset( $_POST['_wfgm_type_text'] ) ? $_POST['_wfgm_type_text'] : WFGM_Common_Helper::translate( 'Type' );
            $free_item_text = isset( $_POST['_wfgm_free_item_text'] ) ? $_POST['_wfgm_free_item_text'] : WFGM_Common_Helper::translate( 'Free Item' );

            $so_product_page = update_option( '_wfgm_so_product_page', $so_product_page_text );
            $so_product_page_enabled = update_option( '_wfgm_so_product_page_enabled', $popup_so_product_page_enabled );
            $so_add_more = update_option( '_wfgm_so_add_more', $so_add_more_text );
            $so_add_more_enabled = update_option( '_wfgm_so_add_more_enabled', $popup_so_add_more_enabled );
            $so_congrat = update_option( '_wfgm_so_congrat', $so_congrat_text );
            $so_congrat_enabled = update_option( '_wfgm_so_congrat_enabled', $popup_so_congrat_enabled );
            $so_congrat_save_money = update_option( '_wfgm_so_congrat_save_money', $so_congrat_save_money_text );
            $so_congrat_save_money_enabled = update_option( '_wfgm_so_congrat_save_money_enabled', $popup_so_congrat_save_money_enabled );
            $so_deleted_gift = update_option( '_wfgm_so_deleted_gift', $so_deleted_gift_text );
            $so_deleted_gift_enabled = update_option( '_wfgm_so_deleted_gift_enabled', $popup_so_deleted_gift_enabled );

			$overlay = update_option( '_wfgm_popup_overlay', $popup_overlay );
			$heading = update_option( '_wfgm_popup_heading', $popup_heading );
            $heading_msg = update_option( '_wfgm_popup_heading_msg', $popup_heading_msg );
            $invalid = update_option( '_wfgm_invalid_condition_text', $invalid_text );
			$add_gift = update_option( '_wfgm_popup_add_gift_text', $add_gift_text );
			$cancel = update_option( '_wfgm_popup_cancel_text', $cancel_text );
			$ok = update_option( '_wfgm_ok_text', $ok_text );
            $type = update_option( '_wfgm_type_text', $type_text );
            $free_item = update_option( '_wfgm_free_item_text', $free_item_text );

			if( $overlay || $heading || $heading_msg || $invalid || $add_gift || $ok || $cancel || $so_product_page || $so_product_page_enabled ||
                $so_add_more || $so_add_more_enabled || $so_congrat || $so_congrat_enabled || $so_congrat_save_money ||
                $so_congrat_save_money_enabled || $so_deleted_gift || $so_deleted_gift_enabled ||
                $type || $free_item ) {
				WFGM_Common_Helper::success_notice(
					WFGM_Common_Helper::translate(
						'Settings saved successfully'
					)
				);

				//update settings
				WFGM_Settings_Helper::force_init();
			} else {
				WFGM_Common_Helper::error_notice(
					WFGM_Common_Helper::translate(
						'No changes to save.'
					)
				);
			}
		}

		include 'pages/general_settings.php';
	}

	public function ajax_product_list_callback()
	{
		$q = isset( $_GET['q'] ) ? $_GET['q'] : '';
		if ( ! $q ) {
			return 0;
		}

		$products = WFGM_Product_Helper::get_products( array( 's' => $q, 'posts_per_page' => 10000 ) );
		$list     = array();
		if ( ! empty( $products ) && ! empty( $products->posts ) ) {
			foreach ( $products->posts as $product ) {
				$list[] = array( 'id' => $product->ID, 'text' => $product->post_title );
			}
		}

		echo json_encode( array( 'options' => $list ) );
		wp_die();
	}
}

/** Initialize */
new WFGM_Admin();
