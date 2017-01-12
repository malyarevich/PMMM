<?php
/**
 * Handle all frontend operation
 *
 * @package  woocommerce-multiple-free-gift-mod
 * @subpackage lib
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 */
class WFGM_Frontend
{
    /** @var boolean Denote if WFGM is enabled */
    protected $_wfgm_enabled;

    /** @var integer Number of gift items allowed */
    protected $_wfgm_gifts_allowed;

    /** @var array Gift products */
    protected $_wfgm_products;

    /** @var integer Minimum number of items in cart for gift */
    protected $_minimum_qty;

    /** @var string Free gift type */
    protected $_wfgm_type;

    /** @var boolean Denotes if there is a valid criteria */
    protected $_wfgm_criteria;

    /** @var array of spesial offer params */
    protected $_wfgm_so_array;

    /** @var array of new gifts */
    protected $_wfgm_added_gifts;

    /** @var array of new gifts */
    protected $_wfgm_info_gifts;

    /** @var array of add more for gifts */
    protected $_wfgm_add_more_gifts;

    /** @var array of new gifts */
    protected $_wfgm_deleted_gifts;

    /** @var bool of show new gifts [false = showed]*/
    protected $_wfgm_flag_show_gifts;

    /**
     * Constructor
     *
     * @see  get_option()
     * @since  0.0.0
     */
    public function __construct()
    {
        $this->_wfgm_type = 'global';
        $this->_minimum_qty = 1;

        $this->_wfgm_enabled = WFGM_Settings_Helper::get( $this->_wfgm_type . '_enabled', true, 'global_options' );
        $this->_wfgm_criteria = false;
        $this->_wfgm_gifts_allowed = 1;
        $this->_wfgm_products = array();
        $this->_wfgm_so_array = array();
        $this->_wfgm_added_gifts = array();
        $this->_wfgm_info_gifts = array();
        $this->_wfgm_add_more_gifts = array();
        $this->_wfgm_deleted_gifts = array();
        $this->_wfgm_flag_show_gifts = false;

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
        add_action( 'wp_ajax_wfgm_add_gifts', array( $this, 'wfgm_ajax_add_free_gifts' ) );
        add_action( 'wp_ajax_nopriv_wfgm_add_gifts', array( $this, 'wfgm_ajax_add_free_gifts' ) );

        /* Display gifts in frontend */
        add_action( 'wp_head', array( $this, 'validate_gifts' ) );
        add_action( 'wp_head', array( $this, 'display_gifts' ) );
        add_action( 'wp_head', array( $this, 'display_special_offer' ) );
        add_action( 'wp_head', array( $this, 'wfgm_custom_notice' ) );

        /* Do not allow user to update quantity of gift items */
        add_filter( 'woocommerce_is_sold_individually', array( $this, 'wfgm_disallow_qty_update' ), 10, 2 );

        /* Remove gifts when main item is removed */
        add_action( 'woocommerce_cart_item_removed', array( $this, 'check_main_products_for_criteria' ), 10, 2 );

        /* Remove gifts when main item is changed */
        add_action( 'woocommerce_add_to_cart', array( $this, 'display_gifts' ), 10, 2 );

    }
    /**
     * Overwrite default settings with actual settings
     *
     * @since  0.0.0
     * @access private
     *
     * @return void|bool
     */
    private function __get_actual_settings()
    {
        $total = WFGM_Product_Helper::get_main_product_count();
        $this->__reload_so_array();

        if( 1 == $total ) {
            //single gift
            $post_id = $this->__get_post_id();
            if( empty($post_id) ) {
                return false;
            }

            $wfgm_enabled = get_post_meta( $post_id, '_wfgm_single_gift_enabled', true );
            if( (bool) $wfgm_enabled ) {
                $this->_wfgm_type = 'single_gift';
                $this->_wfgm_enabled = $wfgm_enabled;
                $this->_wfgm_criteria = true;
                $this->_wfgm_gifts_allowed = get_post_meta( $post_id, '_wfgm_single_gift_allowed', true );

                $this->_wfgm_products = get_post_meta( $post_id, WC_Product::get_post_data(), true );

                return true;
            }
        }

        return $this->__hook_global_settings();
    }

    /**
     * Reload actual special offers
     *
     * @since  0.0.0
     * @access private
     *
     * @return bool
     */
    private function __reload_so_array()
    {
        foreach (get_posts(array('numberposts' => 1000000)) as $post => $so_post) {
            if(intval(trim($so_post->post_title))){
                $temp = explode(',', $so_post->post_content);
                $this->_wfgm_so_array[$so_post->post_title] = [
                    'X' => $temp[0],
                    'Y' => $temp[1]];
            }
        }
        return true;
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
            $is_gift_product = $content['plugin'] == 'wfgm';
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
     * @return boolean
     */
    private function __hook_global_settings()
    {
        //look for global settings
        $wfgm_global_settings = WFGM_Settings_Helper::get( '', false, 'global_settings', false );
        if( empty($wfgm_global_settings) ) {
            return false;
        }

        foreach( $wfgm_global_settings as $setting ) {
            $gift_criteria = $setting['condition'];
            $criteria = WFGM_Criteria_Helper::parse_criteria( $gift_criteria );
            if( $criteria ) {
                $this->__set_actual_values( $setting );
                return true;
            }
        }
        return false;
    }

    /**
     * Set required values
     *
     * @since  0.0.0
     * @access private
     *
     * @param  array $setting setting of global setting
     *
     * @return void
     */
    private function __set_actual_values( $setting )
    {
        $this->_wfgm_criteria = true;
        $this->_wfgm_gifts_allowed = $setting['num_allowed'];
        $this->_wfgm_products = ! empty( $setting['items'] ) ? array_unique( $setting['items'] ) : array();
    }

    /**
     * Add free item to cart.
     *
     * @since  0.0.0
     * @access public
     *
     * @return void
     */
    public function wfgm_ajax_add_free_gifts()
    {
        remove_filter( 'woocommerce_is_sold_individually', array( $this, 'wfgm_disallow_qty_update' ), 10);
        /*check if gift item is valid*/
        self::__get_actual_settings();
        $item_count = 0;
        foreach( $_POST['wfgm_free_items'] as $item ) {

            $count = $_POST['wfgm_free_item_count'][$item_count];
            $free_product = WFGM_Product_Helper::create_gift_variation( $item );
            WFGM_Product_Helper::add_free_product_to_cart( $item, $free_product, $count );
            $item_count ++;
        }

        add_filter( 'woocommerce_is_sold_individually', array( $this, 'wfgm_disallow_qty_update' ), 10, 2 );
        wp_die();
    }

    /**
     * Add free item to cart.
     *
     * @since  0.0.0
     * @access public
     *
     * @param  int $product_id product id for add
     * @param  string $title title for item
     * @param  string $link link for item
     * @param  int $quantity quantity of free product
     *
     * @return boolean
     */
    public function wfgm_add_free_gifts( $product_id = 0, $title, $link, $quantity = 0)
    {
        $is_added = false;

        foreach ( WC()->cart->get_cart() as $key => $content ) {
            if ( $content->ID === $product_id ){
                $is_gift_product = $content['plugin'] == 'wfgm';
                if( $is_gift_product ) {
                    WC()->cart->set_quantity( $content->ID, $content->quantity + $quantity );
                    $is_added = true;
                }
            }
        }
        if( $is_added ) {
            $this->create_notice(
                $product_id,
                $title,
                $link,
                abs($quantity),
                'add'
            );
            return $is_added;
        }

        remove_filter( 'woocommerce_is_sold_individually', array( $this, 'wfgm_disallow_qty_update' ), 10);
        /*check if gift item is valid*/
        self::__get_actual_settings();
        $free_product = WFGM_Product_Helper::create_gift_variation( $product_id );
        $is_added = WFGM_Product_Helper::add_free_product_to_cart( $product_id, $free_product, $quantity );
        add_filter( 'woocommerce_is_sold_individually', array( $this, 'wfgm_disallow_qty_update' ), 10, 2 );

        $this->create_notice(
            $product_id,
            $title,
            $link,
            abs($quantity),
            'add'
        );
        return $is_added;

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
    public function wfgm_disallow_qty_update( $return, $product )
    {
        if( ! property_exists( $product, 'plugin' ) || ! $product['plugin'] == 'wfgm' ) {
            return $return;
        }

        $is_wfgm_variation = $product['plugin'] == 'wfgm';
        if( (bool) $is_wfgm_variation ) {
            return 1;
        }
        return $return;
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
    public function wfgm_item_removed( $cart_item_key, $cart )
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
        $this->__remove_gift_products();
        if( 'global' == $this->_wfgm_type && 0 == WFGM_Product_Helper::get_main_product_count() ) {
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
        if( ! $this->__gift_item_in_cart() ) {
            return;
        }

        self::__get_actual_settings();
        self::_validate_single_gift_condition();

        if( ! $this->_wfgm_criteria ) {
            //remove gift products
            if( $this->__remove_gift_products() ) {
                $this->__set_notice_text();
            }
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
        $noticeText = WFGM_Settings_Helper::get( 'invalid_condition_text', false, 'global_options' );
        if( false === $noticeText ) {
            $noticeText = WFGM_Common_Helper::translate( 'Gift items removed as gift criteria isnt fulfilled' );
        }

        WFGM_Common_Helper::fixed_notice( $noticeText );
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
        if( 'single_gift' !== $this->_wfgm_type ) {
            return false;
        }

        $total_items_in_cart = WFGM_Product_Helper::get_main_product_count();
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
    function __remove_gift_products()
    {
        $removed = false;
        foreach( WC()->cart->cart_contents as $key => $content ) {
            $is_gift_product = $content['plugin'] == 'wfgm';
            if( $is_gift_product ) {
                WC()->cart->remove_cart_item( $key );
                $removed = true;
                $this->create_notice(
                    $content['product_id'],
                    $content['data']->post->post_title,
                    $content['data']->post->guid,
                    $content['quantity'],
                    'delete'
                );
            }
        }
        return $removed;
    }

    /**
     * Remove gift product by $remove_id($product_id).
     *
     * @since  0.0.0
     * @access private
     *
     * @param  int $remove_id Removed item id
     * @param  string $title title for item
     * @param  string $link link for item
     * @param  int $quantity quantity changed of item
     *
     * @return boolean
     */
    private function __remove_gift_product( $remove_id, $title, $link, $quantity )
    {
        $removed = false;
        foreach( WC()->cart->cart_contents as $key => $content ) {
            $is_gift_product = ($content['plugin'] == 'wfgm');
            if( $is_gift_product ) {
                if( intval($content['product_id']) === intval($remove_id) ) {
                    WC()->cart->remove_cart_item( $key );
                    $removed = true;
                }
            }
        }
        $this->create_notice(
            $remove_id,
            $title,
            $link,
            $quantity,
            'delete'
        );
        return $removed;
    }

    /**
     * Create notice for changing by product.
     *
     * @since  0.0.0
     * @access public
     *
     * @param  int $product_id noticed item id
     * @param  string $title title for item
     * @param  string $link link for item
     * @param  int $quantity quantity changed of item
     * @param  string $notice_type type of changing
     *
     * @return void
     */
    public function create_notice( $product_id, $title, $link, $quantity, $notice_type )
    {
        switch ($notice_type) {
            case 'delete':
                $so_deleted_gift = WFGM_Settings_Helper::get('so_deleted_gift', false, 'global_options');
                $so_deleted_gift = str_replace(
                    '{Y}',
                    $quantity,
                    $so_deleted_gift);
                $so_deleted_gift = str_replace(
                    '{title}',
                    '<a href="' . $link . '">' . $title . '</a>',
                    $so_deleted_gift);
                $this->_wfgm_deleted_gifts[$product_id] = ['message' => $so_deleted_gift, 'type' => 'error'];
                break;
            case 'add':
                $so_congrat = WFGM_Settings_Helper::get('so_congrat', false, 'global_options');
                $so_congrat = str_replace(
                    '{Y}',
                    $quantity,
                    $so_congrat);
                $so_congrat = str_replace(
                    '{title}',
                    '<a href="' . $link . '">' . $title . '</a>',
                    $so_congrat);
                $this->_wfgm_added_gifts[$product_id] = ['message' => $so_congrat, 'type' => 'success'];
                break;
            default:
        }
    }

    /**
     * Remove excess gift in cart.
     *
     * @since  0.0.0
     * @access protected
     *
     * @return void
     */
    protected function _wfgm_remove_excess_gifts()
    {
        foreach (WC()->cart->cart_contents as $key => $gift_content) {
            $is_gift_product = $gift_content['plugin'] == 'wfgm';
            if ($is_gift_product) {
                $is_gift_has_main = false;
                foreach (WC()->cart->cart_contents as $key2 => $content) {
                    $is_gift_product = $content['plugin'] == 'wfgm';
                    if (!$is_gift_product) {
                        if ($gift_content['product_id'] == $content['product_id']) {
                            $is_gift_has_main = true;
                        }
                    }
                }
                if (!$is_gift_has_main) {
                    $this->__remove_gift_product(
                        $gift_content['product_id'],
                        $gift_content['data']->post->post_title,
                        $gift_content['data']->post->guid,
                        abs($gift_content['quantity'])
                    );
                }
            }
        }
    }

    /**
     * Reduce or add the amount of a gift product by $product_id.
     *
     * @since  0.0.0
     * @access public
     *
     * @param  int $product_id Reduced/Add item id
     * @param  string $title title for item
     * @param  string $link link for item
     * @param  int $quantity Remove/Add quantity of item
     *
     * @return boolean
     */
    public function qty_change_gift_product( $product_id, $title, $link, $quantity)
    {
        $reduced = false;

        foreach( WC()->cart->get_cart() as $key => $content ) {
            $is_gift_product = $content['plugin'] == 'wfgm';
            if( $is_gift_product ) {

                if( intval($content['product_id']) == intval($product_id) ) {

                    WC()->cart->set_quantity( $key, ($content['quantity'] - $quantity) );
                    $reduced = true;
                    if( $quantity > 0 )	{
                        $this->create_notice(
                            $product_id,
                            $title,
                            $link,
                            abs($quantity),
                            'delete'
                        );
                    } else {
                        $this->create_notice(
                            $product_id,
                            $title,
                            $link,
                            abs($quantity),
                            'add'
                        );
                    }
                }
            }
        }
        return $reduced;
    }

    /**
     * Check main products for criteria of gifts.
     *
     * @since  0.0.0
     * @access public
     *
     * @return boolean
     */
    public function check_main_products_for_criteria()
    {
        $is_need_reload_page = false;
        foreach( WC()->cart->cart_contents as $key => $content ) {
            $is_main_has_gift = false;
            $is_gift_product = $content['plugin'] == 'wfgm';
            if( ! $is_gift_product ) {
                $so_id = WFGM_Product_Helper::get_product_tags($content['product_id']);
                if( $so_id !== 0 ) {
                    if(is_int(intval($this->_wfgm_so_array[$so_id]['X'])) && is_int(intval($this->_wfgm_so_array[$so_id]['Y']))) {
                        $N = floor($content['quantity'] / $this->_wfgm_so_array[$so_id]['X']);
                    } else {
                        $N = 0;
                    }
                    foreach (WC()->cart->cart_contents as $key2 => $gift_content) {
                        $is_gift_product = $gift_content['plugin'] == 'wfgm';
                        if( $is_gift_product ) {
                            if( $content['product_id'] === $gift_content['product_id'] ) {
                                $is_main_has_gift = true;
                                if (intval($N * $this->_wfgm_so_array[$so_id]['Y']) !== intval($gift_content['quantity'])) {

                                    if( intval($N * $this->_wfgm_so_array[$so_id]['Y']) === 0 ) {
                                        $is_need_reload_page = $this->__remove_gift_product(
                                            $gift_content['product_id'],
                                            $gift_content['data']->post->post_title,
                                            $gift_content['data']->post->guid,
                                            abs($gift_content['quantity'])
                                        );

                                    } else {
                                        $reduce_quantity = intval($gift_content['quantity']) - intval($N * $this->_wfgm_so_array[$so_id]['Y']);
                                        $is_need_reload_page = $this->qty_change_gift_product(
                                            $gift_content['product_id'],
                                            $gift_content['data']->post->post_title,
                                            $gift_content['data']->post->guid,
                                            $reduce_quantity
                                        );
                                    }
                                }
                            }
                        }
                    }
                    if ( !$is_main_has_gift && $N > 0) {
                        $is_need_reload_page = $this->wfgm_add_free_gifts(
                            $content['product_id'],
                            $content['data']->post->post_title,
                            $content['data']->post->guid,
                            $N * $this->_wfgm_so_array[$so_id]['Y']
                        );
                    }
                }
            }
        }
        $this->_wfgm_remove_excess_gifts();

        return $is_need_reload_page;
    }/**
 * Display gift popup in frontend.
 *
 * @since  0.0.0
 * @access public
 *
 * @return void
 */
    public function display_gifts()
    {
        if( !is_cart() ){
            add_action( 'woocommerce_add_to_cart', array( $this, 'check_main_products_for_criteria' ), 10, 2 );
            add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'check_main_products_for_criteria' ), 10, 2 );
        }

        self::__get_actual_settings();
        $this->check_main_products_for_criteria();

        if( ! $this->_wfgm_enabled ) {
            return;
        }

        //enqueue required styles for this page
        wp_enqueue_style( 'wfgm-core-styles', plugins_url( '/css/wfgm-styles.css', dirname( __FILE__ ) ) );
        wp_enqueue_style( 'wfgm-template-styles', plugins_url( '/templates/default/wfgm-default.css', dirname( __FILE__ ) ) );
        wp_enqueue_script( 'wfgm-echo-script', plugins_url( '/js/js.echo.js', dirname( __FILE__ ) ));

        $items = WFGM_Product_Helper::get_cart_products();
        if( $items['count'] >= $this->_minimum_qty ) {
            $this->_show_gifts();
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
    public function wfgm_custom_notice()
    {
        $so_congrat_enabled = WFGM_Settings_Helper::get('so_congrat_enabled', false, 'global_options');
        $so_deleted_gift_enabled = WFGM_Settings_Helper::get('so_deleted_gift_enabled', false, 'global_options');
        $so_congrat_save_money_enabled = WFGM_Settings_Helper::get('so_congrat_save_money_enabled', false, 'global_options');
        $so_so_add_more_enabled = WFGM_Settings_Helper::get('so_add_more_enabled', false, 'global_options');

        if( $so_congrat_enabled ) {
            foreach ($this->_wfgm_added_gifts as $key => $added_item) {
                wc_add_notice($added_item['message'], $added_item['type']);
            }
        }

        if( $so_deleted_gift_enabled ) {
            foreach ($this->_wfgm_deleted_gifts as $key => $deleted_item) {
                wc_add_notice($deleted_item['message'], $deleted_item['type']);
            }
        }

        /*BUGS with wc_add_notice($message, 'notice');*/
        if( is_cart() ) {
            if( $so_congrat_save_money_enabled ) {
                foreach ($this->_wfgm_info_gifts as $key => $info_item) {
                    wc_add_notice($info_item['message'], $info_item['type']);
                }
            }
        }

        /*BUGS with wc_add_notice($message, 'notice');*/
        if( is_product() ) {
            if( $so_so_add_more_enabled ) {
                foreach ($this->_wfgm_add_more_gifts as $key => $more_item) {
                    wc_add_notice($more_item['message'], $more_item['type']);
                }
            }
        }
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
        if( is_cart() ) {
            include( PLUGIN_DIR . '/templates/default/template-congratulation.php' );
        }
    }

    /**
     * Check if global gift condition is satisfied.
     *
     * @since 0.0.0
     * @access protected
     *
     * @return boolean
     */
    protected function _check_global_gift_criteria()
    {
        if( 'single_gift' === $this->_wfgm_type ) {
            return true;
        }

        $gift_criteria = WFGM_Settings_Helper::get( 'global_gift_criteria' );
        if( empty($gift_criteria) ) {
            return true;
        }

        return WFGM_Criteria_Helper::parse_criteria( $gift_criteria );
    }

    /**
     * Check if product have special offer.
     *
     * @since 0.0.0
     * @access protected
     *
     * @return boolean
     */
    protected function _check_product_special_offer($post)
    {
        $so_id = WFGM_Product_Helper::get_product_tags($post->ID);
        if( $so_id === 0 ) {
            return false;
        }

        if( !array_key_exists($so_id, $this->_wfgm_so_array) ) {
            return false;
        }

        return true;
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
            if( property_exists( $product, 'plugin' ) && $values['plugin'] == 'wfgm' ) {
                $is_wfgm_variation = $values['plugin'] == 'wfgm';
                if( (bool) $is_wfgm_variation ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Display special offer popup in frontend (product page).
     *
     * @since  0.0.0
     * @access public
     *
     * @return void
     */
    public function display_special_offer()
    {
        if( ! is_product() ) {
            return;
        }
        global $product, $post;

        $this->__get_actual_settings();
        self::__get_actual_settings();
        //check gift criteria
        if( ! $this->_check_product_special_offer($post) ) {
            return;
        }

        //enqueue required styles for this page
        wp_enqueue_style( 'wfgm-core-styles', plugins_url( '/css/wfgm-styles.css', dirname( __FILE__ ) ) );
        wp_enqueue_style( 'wfgm-template-styles', plugins_url( '/templates/default/wfgm-default.css', dirname( __FILE__ ) ) );
        wp_enqueue_script( 'wfgm-echo-script', plugins_url( '/js/js.echo.js', dirname( __FILE__ ) ));
        wp_enqueue_script( 'wfgm-blockui-script', plugins_url( '/js/jquery.blockui.js', dirname( __FILE__ ) ));

        if( ! $this->_wfgm_enabled ) {
            return;
        }
        $product = WFGM_Product_Helper::get_product_details( $product );

        self::__get_actual_settings();

        include( PLUGIN_DIR . '/templates/default/template-product.php' );

    }

}

/* initialize */
new WFGM_Frontend();
