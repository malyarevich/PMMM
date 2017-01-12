<?php
global $post, $product, $woocommerce_loop;

$label = __( 'Frequently Purchased Together', 'wc_recommender' );
$label = get_option( 'wc_recommender_label_fpt', $label );


$simularity_scores = woocommerce_recommender_get_purchased_together( $product->id, $activity_types );
$related = array();

if ( $simularity_scores ) {
	$related = array_keys( $simularity_scores );
}

if ( sizeof( $related ) == 0 )
	return;

$args = apply_filters( 'woocommerce_related_products_args', array(
    'post_type' => 'product',
    'ignore_sticky_posts' => 1,
    'no_found_rows' => 1,
    'posts_per_page' => -1,
    'orderby' => $orderby,
    'post__in' => $related
	) );


$woocommerce_loop['columns'] = $columns;

$products = get_posts( $args );
woocommerce_recommender_sort_posts( $products, $simularity_scores );

if ( $products && count( $products ) ) :

	if ( $posts_per_page ) {
		$parts = array_chunk( $products, $posts_per_page );
		$products = $parts[0];
	}
	?>
	<div style="clear:both;"></div>
	<div class="related products">

		<?php echo apply_filters( 'woocommerce_recommendation_engine_label_purchased_together', '<h2>' . $label . '</h2>' ); ?>

		<?php woocommerce_product_loop_start(); ?>
		<?php woocommerce_get_template_part( 'content', 'product' ); ?>

		<?php
		foreach ( $products as $post ) :
			setup_postdata( $post );
			?>

			<?php woocommerce_get_template_part( 'content', 'product' ); ?>

		<?php endforeach; // end of the loop.    ?>
		<?php wp_reset_postdata(); ?>
		<?php woocommerce_product_loop_end(); ?>

	</div>
	<div style="clear:both;"></div>
	<?php
endif;
wp_reset_postdata();
?>