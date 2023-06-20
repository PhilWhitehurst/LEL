<?php
/**
 * Archive Template for the store
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

get_header(); // Loads the header.php template. ?>

		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>

		<?php endif; ?>

		<?php get_template_part( 'breadcrumbs' ); // Loads the breadcrumbs.php template. ?>

		<div class="container">

			<div id="content">

		<?php if ( have_posts() ) { ?>

			<?php do_action('woocommerce_before_shop_loop'); ?>

			<?php bootstrap_woocommerce_product_subcategories( array( 'before' => '<div class="products container-fluid">', 'after' => '</div>' )); ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_action('nudie_before_loop_product'); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php do_action('nudie_after_loop_product'); ?>

				<?php endwhile; ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php do_action('woocommerce_after_shop_loop'); ?>

		<?php } elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) { ?>

			<?php wc_get_template_part( 'loop/no-products-found.php' ); ?>

		<?php } ?>

		<?php do_action( 'woocommerce_pagination' ); /* woocommerce_pagination - gets pagination (10) and ordering (20) */ ?>		

			</div><!-- #content -->

		<?php get_sidebar( 'woocommerce' ); // Loads the sidebar-woocommerce.php template. ?>

		</div><!-- .container -->

<?php get_footer(); // Loads the footer.php template. ?>