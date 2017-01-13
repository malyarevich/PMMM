<?php
/**
 * Single gift class
 *
 * @package  woocommerce-bacchus-gold-member
 * @subpackage lib
 * @author Yevgen <yevgen.slyuzkin@gmail.com>
 * @version 0.0.0
 */
class WBGM_Single_Gift
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
		add_action( 'woocommerce_product_write_panels', array( $this, 'wbgm_tab_contents' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_wbgm_tab' ) );
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
		<li class="wbgm_free_gift_tab">
			<a href="#wbgm_free_gift_tab">
				<?php echo WBGM_Common_Helper::translate( 'Free Gift Options' ) ?>
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
	public function wbgm_tab_contents()
	{
		$post_id = get_the_ID();
		$wbgm_enabled = get_post_meta( $post_id, '_wbgm_single_gift_enabled', true );
		$wbgm_products = get_post_meta( $post_id, '_wbgm_single_gift_products', true );
		$wbgm_gifts_allowed = get_post_meta( $post_id, '_wbgm_single_gift_allowed', true );
?>
		<div id="wbgm_free_gift_tab" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="form-field wbgm_form_field">
					<input type="checkbox" class="checkbox" style="" name="wbgm_single_gift_enabled" id="wbgm_single_gift_enabled" <?php echo $wbgm_enabled ? 'checked' : '' ?>>
					<label for="wbgm_single_gift_enabled" class="description">
						<?php echo WBGM_Common_Helper::translate( 'Enable free gift for this product.' ); ?>
					</label>
					<img class="help_tip" src="<?php echo WP_PLUGIN_URL  ?>/woocommerce/assets/images/help.png" height="16" width="16" data-tip="
					<?php
						echo WBGM_Common_Helper::translate(
							'Enabling single gift settings will overwrite global settings.'
						)
					?>" />
				</p>
			</div>
			<p class="wbgm-adjust-form-field-gap">
				<label><?php echo WBGM_Common_Helper::translate( 'Select Gift Products' ) ?></label>
				<img class="help_tip" src="<?php echo WP_PLUGIN_URL  ?>/woocommerce/assets/images/help.png" height="16" width="16" data-tip="
					<?php
						echo WBGM_Common_Helper::translate( 'Select single/multiple gift items you want to giveaway for free.' );
						echo '<br/><br/>';
						echo WBGM_Common_Helper::translate( 'Note that duplicate items are saved only once.' );
					?>" />
			</p>
			<div class="_wbgm-repeat">
				<?php echo self::get_ajax_product_selection_design( $wbgm_products, $post_id ); ?>
			</div>

			<p class="form-field wbgm_form_field">
				<label for="wbgm_gifts_allowed" class="description">
					<?php echo WBGM_Common_Helper::translate( 'Number of gifts allowed' ); ?>
				</label>
				<input type="text" class="input-text input-small" name="wbgm_single_gift_allowed" id="wbgm_gifts_allowed" value="<?php echo ( ! empty($wbgm_gifts_allowed) && $wbgm_gifts_allowed >= 0 ) ? $wbgm_gifts_allowed : 1 ?>" />
				<img class="help_tip" src="<?php echo WP_PLUGIN_URL  ?>/woocommerce/assets/images/help.png" height="16" width="16" data-tip="
					<?php
						echo WBGM_Common_Helper::translate(
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
     * @param array $wbgm_products Products selected previously
     * @param int $post_id Post id
     *
     * @return string
     */
    private function get_ajax_product_selection_design( $wbgm_products, $post_id ) {
        $html = "<select class='wbgm-ajax-select' id='wbgm-select-" . uniqid() . "' name='_wbgm_single_gift_products[]' multiple='multiple'>";
        if ( ! empty( $wbgm_products ) ) {
            $product_list = WBGM_Product_Helper::get_products( array(
                'post__in'     => $wbgm_products,
                'post__not_in' => array( $post_id )
            ), - 1 );
            $products     = $product_list->get_posts();
            if ( ! empty( $products ) ) {
                foreach ( $products as $product ) {
                    $product_id = $product->ID;
                    $selected   = in_array( $product_id, $wbgm_products );
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
	public function process_wbgm_tab( $post_id )
	{
		$wbgm_enabled = ( isset($_POST['_wbgm_single_gift_enabled']) && $_POST['_wbgm_single_gift_enabled'] ) ? 1 : 0;
		$wbgm_gifts_allowed = ( isset($_POST['_wbgm_single_gift_allowed']) && $_POST['_wbgm_single_gift_allowed'] >= 0 ) ? $_POST['_wbgm_single_gift_allowed'] : 1;
		if( ! (bool) $wbgm_enabled ) {
			delete_post_meta( $post_id, '_wbgm_single_gift_enabled' );
		} else {
			update_post_meta( $post_id, '_wbgm_single_gift_enabled', $wbgm_enabled );
		}

		update_post_meta( $post_id, '_wbgm_single_gift_allowed', $wbgm_gifts_allowed );
		if( ! empty($_POST['_wbgm_single_gift_products']) ) {
			$products = array_unique( $_POST['_wbgm_single_gift_products'] );
			update_post_meta( $post_id, '_wbgm_single_gift_products', $products );
		} else {
			delete_post_meta( $post_id, '_wbgm_single_gift_products' );
		}
	}

}

/* initialize */
new WBGM_Single_Gift();
