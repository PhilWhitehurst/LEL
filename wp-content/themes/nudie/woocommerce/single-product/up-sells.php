<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $woocommerce_loop;

if ( ! $upsells = $product->get_upsells() ) {
	return;
}

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => WC()->query->get_meta_query()
);

$products                    = new WP_Query( $args );
$woocommerce_loop['name']    = 'up-sells';
$woocommerce_loop['columns'] = apply_filters( 'woocommerce_up_sells_columns', $columns );

if ( $products->have_posts() ) : ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="panel-title"><?php _e( 'You may also like&hellip;', 'woocommerce' ) ?></h2>
	</div>
	<div class="panel-body up-sells upsells">

		<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php do_action('nudie_before_loop_product'); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php do_action('nudie_after_loop_product'); ?>

			<?php endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>
</div>

<?php endif;

wp_reset_postdata();
