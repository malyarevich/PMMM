<?php
/**
 * Single gift class
 *
 * @package  woocommerce-multiple-free-gift-mod
 * @subpackage lib
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 */
class WFGM_Single_Gift
{
	/**
	 * Constructor
	 *
	 * @see  add_action()
	 * @since  0.0.0
	 */
	public function __construct()
	{
		/* Woocommerce panel tab hooks */
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'create_admin_free_gift_tab' ) );
		add_action( 'woocommerce_product_write_panels', array( $this, 'wfgm_tab_contents' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_wfgm_tab' ) );
	}

	/**
	 * Free gift option tab in product add/edit
	 *
	 * @access public
	 * @since  0.0.0
	 * 
	 * @return void
	 */
	public function create_admin_free_gift_tab()
	{
?>
		<li class="wfgm_free_gift_tab">
			<a href="#wfgm_free_gift_tab">
				<?php echo WFGM_Common_Helper::translate( 'Free Gift Options' ) ?>
			</a>
		</li>
<?php
	}

	/**
	 * Free gift tab contents
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function wfgm_tab_contents()
	{
		$post_id = get_the_ID();
		$wfgm_enabled = get_post_meta( $post_id, '_wfgm_single_gift_enabled', true );
		$wfgm_products = get_post_meta( $post_id, '_wfgm_single_gift_products', true );
		$wfgm_gifts_allowed = get_post_meta( $post_id, '_wfgm_single_gift_allowed', true );
?>
		<div id="wfgm_free_gift_tab" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="form-field wfgm_form_field">
					<input type="checkbox" class="checkbox" style="" name="wfgm_single_gift_enabled" id="wfgm_single_gift_enabled" <?php echo $wfgm_enabled ? 'checked' : '' ?>>
					<label for="wfgm_single_gift_enabled" class="description">
						<?php echo WFGM_Common_Helper::translate( 'Enable free gift for this product.' ); ?>
					</label>
					<img class="help_tip" src="<?php echo WP_PLUGIN_URL  ?>/woocommerce/assets/images/help.png" height="16" width="16" data-tip="
					<?php
						echo WFGM_Common_Helper::translate(
							'Enabling single gift settings will overwrite global settings.'
						)
					?>" />
				</p>
			</div>
			<p class="wfgm-adjust-form-field-gap">
				<label><?php echo WFGM_Common_Helper::translate( 'Select Gift Products' ) ?></label>
				<img class="help_tip" src="<?php echo WP_PLUGIN_URL  ?>/woocommerce/assets/images/help.png" height="16" width="16" data-tip="
					<?php
						echo WFGM_Common_Helper::translate( 'Select single/multiple gift items you want to giveaway for free.' );
						echo '<br/><br/>';
						echo WFGM_Common_Helper::translate( 'Note that duplicate items are saved only once.' );
					?>" />
			</p>
			<div class="_wfgm-repeat">
				<?php echo self::get_ajax_product_selection_design( $wfgm_products, $post_id ); ?>
			</div>

			<p class="form-field wfgm_form_field">
				<label for="wfgm_gifts_allowed" class="description">
					<?php echo WFGM_Common_Helper::translate( 'Number of gifts allowed' ); ?>
				</label>
				<input type="text" class="input-text input-small" name="wfgm_single_gift_allowed" id="wfgm_gifts_allowed" value="<?php echo ( ! empty($wfgm_gifts_allowed) && $wfgm_gifts_allowed >= 0 ) ? $wfgm_gifts_allowed : 1 ?>" />
				<img class="help_tip" src="<?php echo WP_PLUGIN_URL  ?>/woocommerce/assets/images/help.png" height="16" width="16" data-tip="
					<?php
						echo WFGM_Common_Helper::translate(
							'Number of items user are allowed to select as a gift.
							Value zero or less will allow unlimited selection.')
					?>" />
			</p>
		</div>
<?php
	}

    /**
     * Select box to get products using ajax
     *
     * @since  0.0.0
     * @access private
     *
     * @param $wfgm_products Products selected previously
     * @param $post_id Post id
     *
     * @return string
     */
    private function get_ajax_product_selection_design( $wfgm_products, $post_id ) {
        $html = "<select class='wfgm-ajax-select' id='wfgm-select-" . uniqid() . "' name='_wfgm_single_gift_products[]' multiple='multiple'>";
        if ( ! empty( $wfgm_products ) ) {
            $product_list = WFGM_Product_Helper::get_products( array(
                'post__in'     => $wfgm_products,
                'post__not_in' => array( $post_id )
            ), - 1 );
            $products = $product_list->get_posts();
            if ( ! empty( $products ) ) {
                foreach ( $products as $product ) {
                    $product_id = $product->ID;
                    $selected   = in_array( $product_id, $wfgm_products );
                    $html .= "<option value='" . $product_id . "' " . ( $selected ? 'selected' : '' ) . ">" . $product->post_title . "</option>";
                }
            }
        }
        $html .= '</select>';

        return $html;
    }

	/**
	 * Save free gift tab contents
	 *
	 * @since  0.0.0
	 * @access public
	 *
	 * @param integer $post_id Current post id
	 *
	 * @return void
	 */
	public function process_wfgm_tab( $post_id )
	{
		$wfgm_enabled = ( isset($_POST['wfgm_single_gift_enabled']) && $_POST['wfgm_single_gift_enabled'] ) ? 1 : 0;
		$wfgm_gifts_allowed = ( isset($_POST['wfgm_single_gift_allowed']) && $_POST['wfgm_single_gift_allowed'] >= 0 ) ? $_POST['wfgm_single_gift_allowed'] : 1;
		if( ! (bool) $wfgm_enabled ) {
			delete_post_meta( $post_id, '_wfgm_single_gift_enabled' );
		} else {
			update_post_meta( $post_id, '_wfgm_single_gift_enabled', $wfgm_enabled );
		}

		update_post_meta( $post_id, '_wfgm_single_gift_allowed', $wfgm_gifts_allowed );
		if( ! empty($_POST['_wfgm_single_gift_products']) ) {
			$products = array_unique( $_POST['_wfgm_single_gift_products'] );
			update_post_meta( $post_id, '_wfgm_single_gift_products', $products );
		} else {
			delete_post_meta( $post_id, '_wfgm_single_gift_products' );
		}
	}

}

/* initialize */
new WFGM_Single_Gift();
