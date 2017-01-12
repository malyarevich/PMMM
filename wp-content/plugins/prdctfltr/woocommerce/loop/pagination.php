<?php

error_reporting( 
    E_ALL
);

	if ( ! defined( 'ABSPATH' ) ) exit;

	global $prdctfltr_global;

	$pf_pag_type = isset( $prdctfltr_global['pagination_type'] ) ? $prdctfltr_global['pagination_type'] : WC_Prdctfltr::$settings['wc_settings_prdctfltr_pagination_type'];

	if ( $pf_pag_type == 'prdctfltr-pagination-default' ) {

		global $wp_query;

		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}

	?>
		<nav class="prdctfltr-pagination prdctfltr-pagination-default">
			<?php
				echo paginate_links( apply_filters( 'prdctfltr_pagination_args', array(
					'base'         => esc_url( untrailingslashit( esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ) ) . '/?paged=%#%' ),
					'format'       => '',
					'add_args'     => false,
					'current'      => max( 1, !isset( $wp_query->query_vars['paged'] ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->query_vars['paged'] : $wp_query->query_vars['paged'] ),
					'total'        => !isset( $wp_query->max_num_pages ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->max_num_pages : $wp_query->max_num_pages,
					'prev_text'    => '&larr;',
					'next_text'    => '&rarr;',
					'type'         => 'list',
					'end_size'     => 3,
					'mid_size'     => 3
				) ) );
			?>
		</nav>
	<?php
	}
	else if ( $pf_pag_type == 'prdctfltr-pagination-load-more' ) {
		global $wp_query;

		$pf_found_posts = !isset( $wp_query->found_posts ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->found_posts : $wp_query->found_posts;
		$pf_per_page = !isset( $wp_query->query_vars['posts_per_page'] ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->query_vars['posts_per_page'] : $wp_query->query_vars['posts_per_page'];
		$pf_offset = isset( $wp_query->query_vars['offset'] ) ? $wp_query->query_vars['offset'] : 0;

	?>
		<nav class="prdctfltr-pagination prdctfltr-pagination-load-more">
		<?php
			if ( $pf_found_posts > 0 && $pf_found_posts > $pf_per_page + $pf_offset ) {
			?>
				<a href="#"><?php _e( 'Load More', 'prdctfltr' ); ?></a>
			<?php
			}
			else {
			?>
				<span><?php _e( 'No More Products!', 'prdctfltr' ); ?></span>
			<?php
			}
		?>
		</nav>
	<?php
	}

?>