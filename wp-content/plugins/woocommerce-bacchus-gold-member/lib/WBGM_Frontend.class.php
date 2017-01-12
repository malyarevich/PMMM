<?php
/**
 * Handle all frontend operation
 *
 * @package  woocommerce-bacchus-gold-member
 * @subpackage lib
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 */
class WBGM_Frontend
{
	/** @var boolean Denote if WBGM is enabled */
	protected $_wbgm_enabled;

	/** @var integer Number of gift items allowed */
	protected $_wbgm_gifts_allowed;

	/** @var array Gift products */
	protected $_wbgm_products;

	/** @var integer Minimum number of items in cart for gift */
	protected $_minimum_qty;

	/** @var string Free gift type */
	protected $_wbgm_type;

	/** @var double gold member balance */
	protected $_wbgm_balance;

	/** @var boolean Denotes if there is a valid criteria */
	protected $_wbgm_criteria;

	/** @var float price of product on current price */
	public $wbgm_cur_price;

    /** @var array of new gifts */
    protected $_wbgm_added_gifts;

    /** @var array of other events */
    protected $_wbgm_info_gifts;

    /** @var array of del gifts */
    protected $_wbgm_deleted_gifts;

    /** @var bool of show new gifts [false = showed]*/
    protected $_wbgm_flag_show_gifts;

	/**
	 * Constructor
	 *
	 * @see  get_option()
	 * @since  0.0.0
	 */
	public function __construct()
	{
		$this->_wbgm_type = 'global';
		$this->_minimum_qty = 1;

		/*$this->_wbgm_enabled = WBGM_Settings_Helper::get( $this->_wbgm_type . '_enabled', true, 'global_options' );*/
		$this->_wbgm_criteria = false;
		$this->_wbgm_gifts_allowed = 1;
		$this->_wbgm_added_gifts = array();
		$this->_wbgm_info_gifts = array();
		$this->_wbgm_deleted_gifts = array();

		self::__init_cookie();
		//Add hooks and filters
		self::__init();
	}

	/**
	 * Add require hooks and filters
	 *
	 * @see  add_action()
	 * @since  0.0.0
	 * @access private
	 */
	private function __init()
	{
		/*  Add free gifts ajax callback */
		add_action( 'wp_ajax_wbgm_add_bonus_gifts', array( $this, 'wbgm_ajax_add_bonus_gifts' ) );
		add_action( 'wp_ajax_nopriv_wbgm_add_bonus_gifts', array( $this, 'wbgm_ajax_add_bonus_gifts' ) );

		/* Update front-end */
		add_filter('woocommerce_add_to_cart_fragments', array( $this, 'wbgm_ajax_update_total'));

		/* Display gifts in frontend */
		add_action( 'wp_head', array( $this, 'validate_gifts' ) );
		add_action( 'wp_head', array( $this, 'wbgm_gold_page_status' ) );
		add_action( 'wp_footer', array( $this, 'wbgm_gold_page_status' ) );
		add_action( 'wp_head', array( $this, 'wbgm_gold_member_balance' ) );;
		add_action( 'wp_head', array( $this, 'wbgm_check_cart' ) );
        add_action( 'wp_head', array( $this, 'wbgm_ajax_update_total' ) );
        add_action( 'wp_head', array( $this, 'wbgm_custom_notice' ) );

        add_action( 'wp_head', array( $this, 'wbgm_gold_page' ) );

		/*add_action( 'wp_footer', array( $this, 'wbgm_gold_member_balance2' ) );*/
		/*add_action( 'woocommerce_cart_updated', array( $this, 'wbgm_gold_member_balance2' ) );
		add_action( 'woocommerce_cart_updated', array( $this, 'wbgm_gold_member_balance' ) );
		add_action( 'woocommerce_cart_updated', array( $this, 'wbgm_update' ) );*/
		add_action( 'wbgm_filter_gold_member_add_to_cart', array( $this, 'wbgm_prepare_free_product' ) );
        add_action( 'gform_pre_submission', array( $this, 'wbgm_gold_page' ), 10, 2 );

        /*add_filter( 'wbgm_top_info', array( $this, 'wbgm_add_info' ), 10, 2 );
        add_filter( 'wbgm_logo', array( $this, 'wbgm_add_logo' ), 10, 2 );*/
        add_filter( 'wbgm_balance', array( $this, 'wbgm_show_balance' ), 10, 2 );

        add_action('gform_post_submission', array( $this, 'wbgm_ajax_update_total'), 10, 2);

		/* Do not allow user to update quantity of gift items */
    	add_filter( 'woocommerce_is_sold_individually', array( $this, 'wbgm_disallow_qty_update' ), 10, 2 );

		/* Remove gifts when main item is removed */
		/*add_action( 'woocommerce_cart_item_removed', array( $this, 'wbgm_item_removed' ), 10, 2 );*/

	}

	/**
	 * Add require and enqueue script
	 *
	 * @see  wp_register_script(), wp_enqueue_script
	 * @since  0.0.0
	 * @access private
	 */
	private function __init_cookie()
	{
		/*  Add js cookie  */
		wp_enqueue_script( 'wbgm-js-cookie-script', plugins_url( '/js/js.cookie.js', dirname( __FILE__ ) ));
	}

	/**
	 * Overwrite default settings with actual settings
	 *
	 * @since  0.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function __get_actual_settings()
	{
		$total = WBGM_Product_Helper::get_main_product_count();
		if( 1 == $total ) {
			//single gift
			$post_id = $this->__get_post_id();
			if( empty($post_id) ) {
				return;
			}

			$wbgm_enabled = get_post_meta( $post_id, '_wbgm_single_gift_enabled', true );
			if( (bool) $wbgm_enabled ) {
				$this->_wbgm_type = 'single_gift';
				$this->_wbgm_enabled = $wbgm_enabled;
				$this->_wbgm_criteria = true;
				$this->_wbgm_gifts_allowed = get_post_meta( $post_id, '_wbgm_single_gift_allowed', true );
				$this->_wbgm_products = get_post_meta( $post_id, '_wbgm_single_gift_products', true );

				return;
			}
		}

		return $this->__hook_global_settings();
	}

	/**
	 * Fetch actual product id
	 *
	 * @since  0.0.0
	 * @access private
	 *
	 * @return integer|null
	 */
	private function __get_post_id()
	{
		$post_id = null;
		foreach( WC()->cart->cart_contents as $key => $content ) {
			$is_gift_product = ($content['plugin'] == 'wbgm');
			if( ! $is_gift_product ) {
				return $content['product_id'];
			}
		}

		return $post_id;
	}

	/**
	 * Hook global settings to actual settings
	 *
	 * @since  0.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function __hook_global_settings()
	{
		//look for global settings
		$_wbgm_global_settings = WBGM_Settings_Helper::get( '', false, 'global_settings', false );
		if( empty($_wbgm_global_settings) ) {
			return;
		}

		foreach( $_wbgm_global_settings as $setting ) {
			$gift_criteria = $setting['condition'];
			$criteria = WBGM_Criteria_Helper::parse_criteria( $gift_criteria );
			if( $criteria ) {
				$this->__set_actual_values( $setting );
				return;
			}
		}
	}

	/**
	 * Set required values
	 *
	 * @since  0.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function __set_actual_values( $setting )
	{
		$this->_wbgm_criteria = true;
		$this->_wbgm_gifts_allowed = $setting['num_allowed'];
		$this->_wbgm_products = ! empty( $setting['items'] ) ? array_unique( $setting['items'] ) : array();
	}

    /**
     * Create notice for changing by product.
     *
     * @since  0.0.0
     * @access public
     *
     * @param  int $product_id noticed item id
     * @param  int $quantity quantity changed of item
     * @param  string $notice_type type of changing
     *
     * @return void
     */
    public function create_notice( $product_id, $quantity, $notice_type )
    {
        $_product = wc_get_product($product_id);
        $link = $_product->post->guid;
        $title = $_product->post->post_title;
        switch ($notice_type) {
            case 'delete':
                $so_deleted_gift = WBGM_Settings_Helper::get('so_deleted_gift', false, 'global_options');
                if($so_deleted_gift) {
                    $so_deleted_gift = '{Y} x {title} wurde(n) aus dem Warenkorb entfernt.';
                }
                $so_deleted_gift = str_replace(
                    '{Y}',
                    $quantity,
                    $so_deleted_gift);
                $so_deleted_gift = str_replace(
                    '{title}',
                    '<a href="' . $link . '">' . $title . '</a>',
                    $so_deleted_gift);
                $this->_wbgm_deleted_gifts[$product_id] = ['message' => $so_deleted_gift, 'type' => 'error'];
                if(!is_cart()){
                    $this->wbgm_custom_notice();
                }
                break;
            case 'add':
                $so_congrat = WBGM_Settings_Helper::get('so_congrat', false, 'global_options');
                if($so_congrat) {
                    $so_congrat = '{Y} x {title} wurde als Gold Artikel hinzugefügt.';
                }
                $so_congrat = str_replace(
                    '{Y}',
                    $quantity,
                    $so_congrat);
                $so_congrat = str_replace(
                    '{title}',
                    '<a href="' . $link . '">' . $title . '</a>',
                    $so_congrat);
                $this->_wbgm_added_gifts[$product_id] = ['message' => $so_congrat, 'type' => 'success'];
                $this->wbgm_custom_notice();
                break;
            default:
        }
    }

    /**
     * Display custom_notice about gifts.
     *
     * @since 0.0.0
     * @access public
     *
     * @return void
     */
    public function wbgm_custom_notice()
    {
        $so_congrat_enabled = WBGM_Settings_Helper::get('so_congrat_enabled', false, 'global_options');
        $so_deleted_gift_enabled = WBGM_Settings_Helper::get('so_deleted_gift_enabled', false, 'global_options');
        $so_congrat_save_money_enabled = WBGM_Settings_Helper::get('so_congrat_save_money_enabled', false, 'global_options');

        if( $so_congrat_enabled ) {
            foreach ($this->_wbgm_added_gifts as $key => $added_item) {
                wc_add_notice($added_item['message'], $added_item['type']);
            }
        }
        if( $so_deleted_gift_enabled ) {
            foreach ($this->_wbgm_deleted_gifts as $key => $deleted_item) {
                wc_add_notice($deleted_item['message'], $deleted_item['type']);
            }
        }

        /**
         * BUG: with wc_add_notice($message, 'notice');
         * FIXED: in wbgm-styles.css
         * .woocommerce-info {
         *        display: block!important;}
         *
         */
        if( is_cart() ) {
            if( $so_congrat_save_money_enabled ) {
                foreach ($this->_wbgm_info_gifts as $key => $info_item) {
                    wc_add_notice($info_item['message'], $info_item['type']);
                }
            }
        }
    }

    /**
     * Add free item to cart.
     *
     * @since  0.0.0
     * @access public
     *
     * @return void
     */
    public function wbgm_ajax_add_bonus_gifts()
    {
        $is_added = false;
        $product_id = $_POST['wbgm_free_items'];

        foreach ( WC()->cart->get_cart() as $key => $content ) {
            if ( $content->product_id === $product_id ){
                $is_gift_product = ($content['plugin'] == 'wbgm');
                if( $is_gift_product ) {
                    WC()->cart->set_quantity( $content->product_id, $content->quantity + 1 );
                    $is_added = true;

                    $this->create_notice($content->product_id, $content->quantity + 1, 'add');
                }
            }
        }
        /*if( $is_added ) {
            $this->create_notice(
                $product_id,
                abs($quantity),
                'add'
            );
            return $is_added;
        }*/

        remove_filter( 'woocommerce_is_sold_individually', array( $this, 'wbgm_disallow_qty_update' ), 10);
        self::__get_actual_settings();
        $_price = $_POST['cur_price'];
        $this->wbgm_gold_member_balance();
        if ( $this->_wbgm_balance > $_price ) {
            $free_product = wbgm_Product_Helper::create_gift_variation( $product_id );
            wbgm_Product_Helper::add_free_product_to_cart( $product_id, $free_product );
            $this->create_notice($product_id, 1, 'add');
        } else {
            //wc_add_wp_error_notices('Your balance is low');
        }
        add_filter( 'woocommerce_is_sold_individually', array( $this, 'wbgm_disallow_qty_update' ), 10, 2 );
        wp_die();
    }

	/**
	 * Update frontend elements.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function wbgm_check_cart()
	{
	    if( !is_cart() ) {
	        return;
        }

        if( isset( $_COOKIE['wbgm_ac_info'] ) ) {

            echo '<script>console.log(' . json_encode( ) . ')</script>';
            echo '<script>console.log(' . json_encode( WC()->cart->get_cart() ) . ')</script>';
        }
	}

    /**
     * Recount gold member balance.
     *
     * @since  0.0.0
     * @access public
     *
     * @return double
     */
    public function wbgm_gold_member_balance()
    {
        global $post;

        if(!isset($this->wbgm_cur_price) and (get_post_meta( $post->ID, '_regular_price', true) != 0)){
            $this->wbgm_cur_price = get_post_meta( $post->ID, '_regular_price', true);
        }

        if( WBGM_Settings_Helper::get( 'global_enabled', true, 'global_options' ) ) {
            $total_free_cost = 0.0;
            $total_cost = WC()->cart->subtotal;
            foreach (WC()->cart->get_cart() as $key => $item) {
                if ($item['plugin'] == 'wbgm') {
                    $_product = wc_get_product( $item['product_id'] );
                    $total_free_cost += doubleval($_product->get_price()) * $item['quantity'];
                }
            }
            $this->_wbgm_balance = $total_cost * 0.1 - $total_free_cost;

        }
        return $this->_wbgm_balance;
    }

    /**
     * Recount gold member balance.
     *
     * @since  0.0.0
     * @access public
     *
     * @return string
     */
    public function wbgm_show_balance()
    {
        $this->wbgm_gold_member_balance();
        setlocale(LC_MONETARY, 'de_DE');
        return money_format('%.2n', $this->_wbgm_balance);
    }

    /**
     * Recount gold member balance.
     *
     * @since  0.0.0
     * @access public
     *
     * @return boolean
     */
    public function wbgm_is_show_bonus_add_btn()
    {
        $this->wbgm_gold_member_balance();
        if ( floatval( $this->wbgm_cur_price ) <= floatval( $this->wbgm_gold_member_balance() )) {
            return true;
        } else {
            return false;
        }
    }

	/**
	 * Disallow qty update in gift products.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @param  boolean $return  Is return product
	 * @param  object $product Product object
	 *
	 * @return integer|void
	 */
	public function wbgm_disallow_qty_update( $return, $product )
	{
	    if(! is_cart()) {
            return $return;
        }
	    $is_plugin_wbgm = false;
	    foreach (WC()->cart->get_cart() as $key => $item){
	        if( $product->variation_id == $item->variation_id) {
	            if( ($item['plugin'] == 'wbgm') ) {
                    $is_plugin_wbgm = true;
                }
            }
        }
		if( $is_plugin_wbgm ) {
			return $return;
		} else {
			return 1;
		}
	}

	/**
	 * Remove all gifts when main item is removed.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @param  string $cart_item_key Removed item key
	 * @param  object $cart          Cart object
	 *
	 * @return void
	 */
	public function wbgm_item_removed( $cart_item_key, $cart )
	{
		//no need to process further if qty is zero
		if( empty($cart->cart_contents) ) {
			return;
		}

		//check if removed item is a variation or main product
		$removed_item = $cart->removed_cart_contents[ $cart_item_key ];
		if( ! empty($removed_item['variation_id']) ) {
			return;
		}

		if( 'global' == $this->_wbgm_type && 0 == WBGM_Product_Helper::get_main_product_count() ) {
			foreach( $cart->cart_contents as $key => $content ) {
				WC()->cart->remove_cart_item( $key );
			}
		}
	}

	/**
	 * Remove gifts if the criteria is invalid.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function validate_gifts()
	{
        if( WBGM_Settings_Helper::get( 'global_enabled', true, 'global_options' ) ) {
            if (!$this->__gift_item_in_cart()) {
                return;
            }
            $this->wbgm_gold_member_balance();
            if ( $this->_wbgm_balance < 0.0 ) {
               $this->__remove_gift_products();
            }

            self::__get_actual_settings();
            /*self::_validate_single_gift_condition();

            $cart_items = WBGM_Product_Helper::get_gift_products_in_cart();
            if (!$this->_wbgm_criteria || !WBGM_Product_Helper::crosscheck_gift_items($cart_items, $this->_wbgm_products)) {
                //remove gift products
                if ($this->__remove_gift_products()) {
                    $this->__set_notice_text();
                }
            }*/
        } else {
            $this->__remove_gift_products();
        }
	}

	/**
	 * Set notice text.
	 *
	 * @since  0.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function __set_notice_text()
	{
		$noticeText = WBGM_Settings_Helper::get( 'invalid_condition_text', false, 'global_options' );
		if( false === $noticeText ) {
			$noticeText = WBGM_Common_Helper::translate( 'Gift items removed as gift criteria isnt fulfilled' );
		}

		WBGM_Common_Helper::fixed_notice( $noticeText );
	}

	/**
	 * Validate single gift condition.
	 *
	 * @since  0.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _validate_single_gift_condition()
	{
		if( 'single_gift' !== $this->_wbgm_type ) {
			return false;
		}

		$total_items_in_cart = WBGM_Product_Helper::get_main_product_count();
		if( 1 !== $total_items_in_cart ) {
			return false;
		}

		return $this->__remove_gift_products();
	}

	/**
	 * Remove gifts products.
	 *
	 * @since  0.0.0
	 * @access private
	 *
	 * @return boolean
	 */
	private function __remove_gift_products()
	{
		$removed = false;
		foreach( WC()->cart->get_cart() as $key => $content ) {
			if( $content['plugin'] == 'wbgm' ) {
				WC()->cart->remove_cart_item( $key );
				$removed = true;
                $this->create_notice(
                    $content['product_id'],
                    $content['quantity'],
                    'delete'
                );
			}
		}


		return $removed;
	}

	/**
	 * Display gift popup in frontend.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function display_gifts()
	{
		if( ! is_cart() ) {
			return;
		}

		if( $this->__gift_item_in_cart() ) {
			return;
		}

		self::__get_actual_settings();

		//check gift criteria
		if( ! $this->_check_global_gift_criteria() ) {
			return;
		}

		//enqueue required styles for this page
		wp_enqueue_style( 'wbgm-core-styles', plugins_url( '/css/wbgm-styles.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'wbgm-template-styles', plugins_url( '/templates/default/wbgm-default.css', dirname( __FILE__ ) ) );

		$items = WBGM_Product_Helper::get_cart_products();
		if( $items['count'] >= $this->_minimum_qty ) {
			$this->_show_gifts();
		}
	}

	/**
	 * Verify gold status on gold page in frontend.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function wbgm_gold_page($form = null)
	{
		if( ! is_page('gold') ) {
			return;
		}

        /*$this->wbgm_check_number('0041225691', 'Joerg', 'Goetz');*/
        /*if (isset($_COOKIE['wbgm_ac_info'])) {
            setcookie('wbgm_ac_info', '', time() - 3600, '/');
        }*/

		$is_valid = false;
        if (! ($form == null)) {
            if ($_SERVER['REQUEST_METHOD'] == "POST")
            {
                if( intval(urlencode($_POST['input_1'])) ) {
                    $is_valid = $this->wbgm_check_number(urlencode($_POST['input_1']), urlencode($_POST['input_2']), urlencode($_POST['input_3']));
                } else {
                    $is_valid = $this->wbgm_check_email(urlencode($_POST['input_4']), urlencode($_POST['input_2']), urlencode($_POST['input_3']));
                }
                if( !$is_valid ) {
                    $_POST['input_6'] = '';
                } else {
                    $_POST['input_6'] = '123';
                }
            }
        }
	}

	/**
	 * Prepare free gold product.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function wbgm_prepare_free_product()
	{
        global $post;
        if( $post->ID !== 50826 ) {
            /*enqueue required styles for this page*/
            wp_enqueue_style('wbgm-core-styles', plugins_url('/css/wbgm-styles.css', dirname(__FILE__)));
            wp_enqueue_style('wbgm-template-styles', plugins_url('/templates/default/wbgm-default.css', dirname(__FILE__)));
            include(PLUGIN_DIR . 'templates/default/template-default.php');
        }
	}

	/**
	 * Display gold status in frontend.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function wbgm_gold_page_status()
	{
	    if( WBGM_Settings_Helper::get( 'global_enabled', true, 'global_options' ) ) {
            if( isset( $_COOKIE['wbgm_ac_info'] ) ) {


                $logo_img = plugins_url('/templates/images/Bacchus_Gold_Logo.png', dirname(__FILE__));
                /*enqueue required styles for this page*/
                wp_enqueue_style( 'wbgm-template-styles', plugins_url( '/templates/default/wbgm-default.css', dirname( __FILE__ ) ) );

                include( PLUGIN_DIR . 'templates/default/template-gold-status.php' );
                echo '<script>jQuery(".tb-left").html(\'<div class="tb-text"><div class="wbgm-info-top" style="font-size: 12px; font-weight: 600;">Ihr Bacchus Gold Guthaben für diese Bestellung beträgt <span class="wbgm-top-balance"> ' . apply_filters('wbgm_balance') . ' </span><a href="/gold"><span class="glyphicon glyphicon-info-sign wbgm-info-sign" style="color: rgba(235, 181, 102, 1);"></span></a></div></div>\');</script>';
            }
	    }
	}

	/**
	 * Display gold members add-to-cart btn in frontend.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function wbgm_add_btn_add_to_cart_gold()
	{
        apply_filters('wbgm_filter_gold_member_add_to_cart');
	}

	/**
	 * Display logo in frontend.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function wbgm_add_logo()
	{
        if( WBGM_Settings_Helper::get( 'global_enabled', true, 'global_options' ) ) {
            if (isset($_COOKIE['wbgm_ac_info'])) {
                return '<div id="wbgm-logo" style="width:80px; display: inline-block;"><a href="/gold"><img src="' . plugins_url('/templates/images/Bacchus_Gold_Logo.png', dirname(__FILE__)) . '"></a></div>';
            }
        }
        return '';
	}

	/**
	 * Display logo in frontend.
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function wbgm_add_info()
	{
	    if( WBGM_Settings_Helper::get( 'global_enabled', true, 'global_options' ) ) {
            if (isset($_COOKIE['wbgm_ac_info'])) {
                return '<div class="wbgm-info-top" style="font-size: 12px; font-weight: 600;">Ihr Bacchus Gold Guthaben für diese Bestellung beträgt <span class="wbgm-top-balance"> ' . apply_filters('wbgm_balance') . ' </span><a href="/gold"><span class="glyphicon glyphicon-info-sign wbgm-info-sign" style="color: rgba(235, 181, 102, 1);"></span></a></div>';
            }
        }
        return 'Ihre Genuss-Reise durch die Welt der Weine';
	}

    /**
     * Check the data from form (Number way).
     *
     * @since  0.0.0
     * @access private
     *
     * @param int $number custom number (Bacchus Gold)
     * @param string $name name of member
     * @param string $last_name of member
     * @return boolean
     */
    private function wbgm_check_number( $number, $name, $last_name ) {
        $count_of_adds_zero = 10 - strlen($number);
        while($count_of_adds_zero > 0) {
            $number = '0' . $number;
            $count_of_adds_zero--;
        }
        $is_valid = false;
        foreach ( get_posts( array('numberposts' => 1000000, 'post_type' => 'wbgm_gmember_list' )) as $post_key => $post_value ) {

            echo '<script>console.log(' . json_encode($post_value ) . ')</script>';
            if( $post_value->post_title == $name . ' ' . $last_name . ' ' . $number ) {
				$ac_info = explode('|', $post_value->post_content);
				if( $this->wbgm_valid_date($ac_info[8])) {
					setcookie( 'wbgm_ac_info', $post_value->post_content, 0, '/' );
                    $is_valid = true;
				}
            }
        }
        return $is_valid;
    }

    /**
     * Check the data from form (Email way).
     *
     * @since  0.0.0
     * @access private
     *
     * @param int $email custom number (Bacchus Gold)
     * @param string $name name of member
     * @param string $last_name of member
     * @return boolean
     */
    private function wbgm_check_email( $email, $name, $last_name ) {
        $is_valid = false;
		foreach ( get_posts( array('numberposts' => 1000000, 'post_type' => 'wbgm_gmember_list' ))  as $post_key => $post_value ) {
			$pos = strripos(trim($post_value->post_title), ' ', -1);
			$substr = substr(trim($post_value->post_title), 0, $pos);
            if( trim($substr) == $name . ' ' . $last_name) {
				$ac_info = explode('|', $post_value->post_content);
				if( $this->wbgm_valid_date($ac_info[8]) && trim($email) == urlencode($ac_info[5])) {
					setcookie( 'wbgm_ac_info', $post_value->post_content, 0, '/' );
                    $is_valid = true;
				}
            }
        }
        return $is_valid;
    }

	/**
	 * Validate date with current date.
	 *
	 * @since 0.0.0
	 * @access public
	 *
	 * @param int $valid_date date for valid
	 * @return boolean
	 */
	public function wbgm_valid_date($valid_date)
	{
		$today = date("Ymd");
		if( $today < $valid_date ) {
			return true;
		}
		return false;
	}

	/**
	 * Display gifts.
	 *
	 * @since 0.0.0
	 * @access public
	 *
	 * @return void
	 */
	protected function _show_gifts()
	{
		if( ! $this->_wbgm_enabled ) {
			return;
		}

		if( empty($this->_wbgm_products) ) {
			return;
		}

		$wbgm_free_products = array();
		foreach( $this->_wbgm_products as $product ) {
			$wbgm_free_products[] = WBGM_Product_Helper::get_product_details( $product );
		}

		include( PLUGIN_DIR . 'templates/default/template-default.php' );
	}

	/**
	 * Check if global gift condition is satisfied.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return boolean
	 */
	protected function _check_global_gift_criteria()
	{
		if( 'single_gift' === $this->_wbgm_type ) {
			return true;
		}

		$gift_criteria = WBGM_Settings_Helper::get( 'global_gift_criteria' );
		if( empty($gift_criteria) ) {
			return true;
		}

		return WBGM_Criteria_Helper::parse_criteria( $gift_criteria );
	}

	/**
	 * Check if there is already gift item in the cart
	 *
	 * @since  0.0.0
	 * @access private
	 *
	 * @return boolean
	 */
	private function __gift_item_in_cart()
	{
		$cart = WC()->cart->get_cart();
		if ( count( $cart ) < 0 ) {
			return false;
		}

		foreach ( $cart as $cart_item_key => $values ) {
			$product = $values['data'];
            if($values['plugin'] == 'wbgm') {
                return true;
			}
		}

		return false;
	}

    /**
     * Update frontend elements.
     *
     * @since  0.0.0
     * @access public
     *
     * @param array $fragments input fragments
     * @return array
     */
	function wbgm_ajax_update_total($fragments){
	    global $post;
        if( WBGM_Settings_Helper::get( 'global_enabled', true, 'global_options' ) ) {
            if (isset($_COOKIE['wbgm_ac_info'])) {

                $fragments['.wbgm-info-top'] = $this->wbgm_add_info();

                $logo_img = plugins_url('/templates/images/Bacchus_Gold_Logo.png', dirname(__FILE__));
                $fragments['.tb-left>.tb-text'] = '<div class="tb-text"><div class="wbgm-info-top" style="font-size: 12px; font-weight: 600;">Ihr Bacchus Gold Guthaben für diese Bestellung beträgt <span class="wbgm-top-balance"> ' . apply_filters('wbgm_balance') . ' </span><a href="/gold"><span class="glyphicon glyphicon-info-sign wbgm-info-sign" style="color: rgba(235, 181, 102, 1);"></span></a></div></div>';
                $fragments['.wbgm-info-top'] = $this->wbgm_add_info();
                if ($this->wbgm_is_show_bonus_add_btn()) {
                    if ($post->post_ID !== 50826) {
                        $fragments['#wbgm-scripts'] = '<div id="wbgm-scripts"><script>jQuery("#wbgm-add-to-cart-bonus-form").show();</script></div>';
                    } else {
                        $fragments['#wbgm-scripts'] = '<div id="wbgm-scripts"><script>jQuery("#wbgm-add-to-cart-bonus-form").hide();</script></div>';
                    }

                } else {
                    $fragments['#wbgm-scripts'] = '<div id="wbgm-scripts"><script>jQuery("#wbgm-add-to-cart-bonus-form").hide();</script></div>';
                }
                $fragments['#mobile-logo'] = '<div id="mobile-logo" class="logo-center has-img clearfix" data-anim=""><a href="https://www.bacchus.de">' .
                    '<img class="standard" src="' . $logo_img . '" alt="Bacchus - Internationale Weine" height="68" width="210">' .
                    '<img class="retina" src="' . $logo_img . '" alt="Bacchus - Internationale Weine" height="68" width="210">' .
                    '<div class="text-logo"></div></a></div>';
                $fragments['#logo'] = '<div id="logo" class="col-sm-4 logo-center has-img clearfix" data-anim=""><a href="https://www.bacchus.de">' .
                    '<img class="standard" src="' . $logo_img . '" alt="Bacchus - Internationale Weine" height="68" width="200">' .
                    '<img class="retina" src="' . $logo_img . '" alt="Bacchus - Internationale Weine" height="68" width="200">' .
                    '<div class="text-logo"></div></a></div>';
            }
        }
        return $fragments;
    }

}

/* initialize */
new WBGM_Frontend();
