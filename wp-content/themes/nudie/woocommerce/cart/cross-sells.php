<?php
/**
 * Cross-sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( ! $crosssells = WC()->cart->get_cross_sells() ) {
	return;
}

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => apply_filters( 'woocommerce_cross_sells_total', $posts_per_page ),
	'orderby'             => $orderby,
	'post__in'            => $crosssells,
	'meta_query'          => WC()->query->get_meta_query(),
);

$products                    = new WP_Query( $args );
$woocommerce_loop['name']    = 'cross-sells';
$woocommerce_loop['columns'] = apply_filters( 'woocommerce_cross_sells_columns', $columns );

if ( $products->have_posts() ) : ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="panel-title"><?php _e( 'You may be interested in&hellip;', 'woocommerce' ) ?></h2>
	</div>
	<div class="panel-body cross-sells crosssells">

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

wp_reset_query();