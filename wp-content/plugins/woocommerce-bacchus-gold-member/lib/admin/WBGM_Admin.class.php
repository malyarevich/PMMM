<?php
/**
 * Admin page
 *
 * @package woocommerce-bacchus-gold-member
 * @subpackage lib/admin
 *
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 */
class WBGM_Admin
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
			WBGM_Common_Helper::translate( 'Woocommerce Bacchus Gold Member' ),
			WBGM_Common_Helper::translate( 'Bacchus Gold Loyalty Program Plugin' ),
			'manage_options',
			'woocommerce-bacchus-gold-member',
			array( $this, 'main_menu_template' ),
			'dashicons-cart'
		);

		/*add_submenu_page(
			'woocommerce-bacchus-gold-member',
			WBGM_Common_Helper::translate( 'Gift Criteria' ) . ' - ' .
			WBGM_Common_Helper::translate( 'Bacchus Gold Loyalty Program Plugin' ),
			WBGM_Common_Helper::translate( 'Gift Criteria' ),
			'manage_options',
			'woocommerce-bacchus-gold-member-criteria',
			array( $this, '_wbgm_criteria_template' )
		);*/

		add_submenu_page(
			'woocommerce-bacchus-gold-member',
			WBGM_Common_Helper::translate( 'General Settings' ) . ' - ' .
			WBGM_Common_Helper::translate( 'Bacchus Gold Loyalty Program Plugin' ),
			WBGM_Common_Helper::translate( 'General Settings' ),
			'manage_options',
			'woocommerce-bacchus-gold-member-settings',
			array( $this, 'wbgm_general_settings' )
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
		wp_enqueue_style( 'wmfg-admin-styles', plugins_url( '/admin/css/wbgm-admin-styles.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( 'wbgm-selectize', plugins_url( '/admin/js/plugins/selectize/selectize.css', dirname( __FILE__ ) ) );

		//enqueue scripts
		wp_enqueue_script( 'wmfg-admin-scripts', plugins_url( '/admin/js/wbgm-admin-scripts.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-dialog' ) );
		wp_enqueue_script( 'wbgm-selectize-lib', plugins_url( '/admin/js/plugins/selectize/selectize.min.js', dirname( __FILE__ ) ), array( 'jquery' ) );
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
		if( ( isset($_POST['_wbgm_global_hidden']) && 'Y' == $_POST['_wbgm_global_hidden'] )
				&& wp_verify_nonce( $_POST['_wbgm_global_nonce'], '_wbgm_global_settings' ) ) {

			$wbgm_globally_enabled = isset( $_POST['wbgm_globally_enabled'] ) ? true : false;
			$enabled = update_option( '_wbgm_global_enabled', $wbgm_globally_enabled );

			if( isset($_POST['_wbgm_criteria']) ) {
				$user_criteria = $_POST['_wbgm_criteria'];

				//remove extra fields
				unset( $user_criteria['_wbgm_global_nonce'] );
				unset( $user_criteria['_wp_http_referer'] );
				unset( $user_criteria['_wbgm_global_hidden'] );

				$conditionSaved = update_option( '_wbgm_global_settings', $user_criteria );
				if( $enabled || $conditionSaved ) {
					WBGM_Common_Helper::success_notice(
						WBGM_Common_Helper::translate(
							'Gift conditions saved successfully'
						)
					);

					WBGM_Settings_Helper::force_init();
				} else {
					WBGM_Common_Helper::error_notice(
						WBGM_Common_Helper::translate(
							'There was a problem. Please try again.'
						)
					);
				}
			} else {
				if( get_option( '_wbgm_global_settings' ) !== false ) {
					if( delete_option( '_wbgm_global_settings' ) ) {
						WBGM_Common_Helper::success_notice(
							WBGM_Common_Helper::translate(
								'Gift conditions emptied successfully'
							)
						);
					}
				} else {
					WBGM_Common_Helper::error_notice(
						WBGM_Common_Helper::translate(
							'No gift conditions to save. You can add conditions by clicking <em>Add new gift condition</em> button'
						)
					);
				}
			}

			//update settings
			WBGM_Settings_Helper::force_init();
		}

		include 'pages/main_menu_page.php';
	}

	public function _wbgm_criteria_template()
	{
		if( ( isset($_POST['_wbgm_criteria_hidden']) && $_POST['_wbgm_criteria_hidden'] == 'Y' )
				&& wp_verify_nonce( $_POST['_wbgm_criteria_nonce'], '_wbgm_criteria_settings' ) ) {

			if( isset($_POST['_wbgm_criteria']) ) {
				$user_criteria = $_POST['_wbgm_criteria'];
				foreach( $user_criteria as &$criteria ) {
					$criteria['slug'] = sanitize_title( $criteria['name'] );
				}

				if( update_option( '_wbgm_criteria', $user_criteria ) ) {
					WBGM_Common_Helper::success_notice(
						WBGM_Common_Helper::translate(
							'Criteria saved successfully'
						)
					);

					WBGM_Settings_Helper::force_init();
				} else {
					WBGM_Common_Helper::error_notice(
						WBGM_Common_Helper::translate(
							'There was a problem. Please try again.'
						)
					);
				}
			} else {
				if( get_option( '_wbgm_criteria' ) !== false ) {
					if( delete_option( '_wbgm_criteria' ) ) {
						WBGM_Common_Helper::success_notice(
							WBGM_Common_Helper::translate(
								'Criteria saved successfully'
							)
						);
					}
				} else {
					WBGM_Common_Helper::error_notice(
						WBGM_Common_Helper::translate(
							'No criteria to save. You can add criteria by clicking <em>Add New Criteria</em> button'
						)
					);
				}
			}

			//update settings
			WBGM_Settings_Helper::force_init();
		}

		include 'pages/gift_criteria.php';
	}

	public function wbgm_general_settings()
	{
		if( ( isset($_POST['_wbgm_general_settings_submitted']) && $_POST['_wbgm_general_settings_submitted'] == 'Y' )
				&& wp_verify_nonce( $_POST['_wbgm_general_nonce'], 'wbgm_general_settings' ) ) {

			$popup_overlay = isset( $_POST['_wbgm_popup_overlay'] ) ? 1 : 0;

            $so_product_page_text = isset( $_POST['_wbgm_so_product_page'] )  ? $_POST['_wbgm_so_product_page'] : wbgm_Common_Helper::translate( 'On this product active special offer. For every {X} item we give {Y} free gift.' );
            $popup_so_product_page_enabled = isset( $_POST['_wbgm_so_product_page_enabled'] ) ? 1 : 0;
            $so_add_more_text = isset( $_POST['_wbgm_so_add_more'] )  ? $_POST['_wbgm_so_add_more'] : wbgm_Common_Helper::translate( 'Special offer. add more for gift.' );
            $popup_so_add_more_enabled = isset( $_POST['_wbgm_so_add_more_enabled'] ) ? 1 : 0;
            $so_congrat_text = isset( $_POST['_wbgm_so_congrat'] )  ? $_POST['_wbgm_so_congrat'] : wbgm_Common_Helper::translate( '{Y} x {title} wurde als Gold Artikel hinzugefügt.' );
            $popup_so_congrat_enabled = isset( $_POST['_wbgm_so_congrat_enabled'] ) ? 1 : 0;
            $so_congrat_save_money_text = isset( $_POST['_wbgm_so_congrat_save_money'] )  ? $_POST['_wbgm_so_congrat_save_money'] : wbgm_Common_Helper::translate( 'Congratulations! You save {sum(N*Y*price)} {currency}.' );
            $popup_so_congrat_save_money_enabled = isset( $_POST['_wbgm_so_congrat_save_money_enabled'] ) ? 1 : 0;
            $so_deleted_gift_text = isset( $_POST['_wbgm_so_deleted_gift'] )  ? $_POST['_wbgm_so_deleted_gift'] : wbgm_Common_Helper::translate( '{Y} x {title} wurde(n) aus dem Warenkorb entfernt.' );
            $popup_so_deleted_gift_enabled = isset( $_POST['_wbgm_so_deleted_gift_enabled'] ) ? 1 : 0;

			$popup_heading = isset( $_POST['_wbgm_popup_heading'] )  ? $_POST['_wbgm_popup_heading'] : wbgm_Common_Helper::translate( 'Take your free gift' );
            $popup_heading_msg = isset( $_POST['_wbgm_popup_heading_msg'] )  ? $_POST['_wbgm_popup_heading_msg'] : wbgm_Common_Helper::translate( 'Message for you!' );
			$invalid_text = isset( $_POST['_wbgm_invalid_condition_text'] )  ? $_POST['_wbgm_invalid_condition_text'] : wbgm_Common_Helper::translate( 'Gift items removed as gift criteria isnt fulfilled.' );
			$add_gift_text = isset( $_POST['_wbgm_btn_adding_to_cart_text'] ) ? $_POST['_wbgm_btn_adding_to_cart_text'] : wbgm_Common_Helper::translate( 'Bonus-Artikel wird hinzugefügt' );
			$cancel_text = isset( $_POST['_wbgm_popup_cancel_text'] ) ? $_POST['_wbgm_popup_cancel_text'] : wbgm_Common_Helper::translate( 'Nein danke' );
            $ok_text = isset( $_POST['_wbgm_ok_text'] ) ? $_POST['_wbgm_ok_text'] : wbgm_Common_Helper::translate( 'Okay' );
            $type_text = isset( $_POST['_wbgm_type_text'] ) ? $_POST['_wbgm_type_text'] : wbgm_Common_Helper::translate( 'Typ' );
            $free_item_text = isset( $_POST['_wbgm_free_item_text'] ) ? $_POST['_wbgm_free_item_text'] : wbgm_Common_Helper::translate( 'Gratis Bacchus Gold Artikel' );

            $so_product_page = update_option( '_wbgm_so_product_page', $so_product_page_text );
            $so_product_page_enabled = update_option( '_wbgm_so_product_page_enabled', $popup_so_product_page_enabled );
            $so_add_more = update_option( '_wbgm_so_add_more', $so_add_more_text );
            $so_add_more_enabled = update_option( '_wbgm_so_add_more_enabled', $popup_so_add_more_enabled );
            $so_congrat = update_option( '_wbgm_so_congrat', $so_congrat_text );
            $so_congrat_enabled = update_option( '_wbgm_so_congrat_enabled', $popup_so_congrat_enabled );
            $so_congrat_save_money = update_option( '_wbgm_so_congrat_save_money', $so_congrat_save_money_text );
            $so_congrat_save_money_enabled = update_option( '_wbgm_so_congrat_save_money_enabled', $popup_so_congrat_save_money_enabled );
            $so_deleted_gift = update_option( '_wbgm_so_deleted_gift', $so_deleted_gift_text );
            $so_deleted_gift_enabled = update_option( '_wbgm_so_deleted_gift_enabled', $popup_so_deleted_gift_enabled );

			$overlay = update_option( '_wbgm_popup_overlay', $popup_overlay );
			$heading = update_option( '_wbgm_popup_heading', $popup_heading );
            $heading_msg = update_option( '_wbgm_popup_heading_msg', $popup_heading_msg );
            $invalid = update_option( '_wbgm_invalid_condition_text', $invalid_text );
			$add_gift = update_option( '_wbgm_btn_adding_to_cart_text', $add_gift_text );
			$cancel = update_option( '_wbgm_popup_cancel_text', $cancel_text );
			$ok = update_option( '_wbgm_ok_text', $ok_text );
            $type = update_option( '_wbgm_type_text', $type_text );
            $free_item = update_option( '_wbgm_free_item_text', $free_item_text );

			if( $overlay || $heading || $heading_msg || $invalid || $add_gift || $cancel || $ok || $type || $free_item
                || $so_product_page || $so_product_page_enabled || $so_add_more || $so_add_more_enabled || $so_congrat
                || $so_congrat_enabled || $so_congrat_save_money || $so_congrat_save_money_enabled || $so_deleted_gift
                || $so_deleted_gift_enabled ) {
				wbgm_Common_Helper::success_notice(
					wbgm_Common_Helper::translate(
						'Settings saved successfully'
					)
				);

				//update settings
				WBGM_Settings_Helper::force_init();
			} else {
				WBGM_Common_Helper::error_notice(
					WBGM_Common_Helper::translate(
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
		if( ! $q ) {
			return 0;
		}

		$products = WBGM_Product_Helper::get_products( array( 's' => $q, 'posts_per_page' => 15 ) );
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
new WBGM_Admin();
