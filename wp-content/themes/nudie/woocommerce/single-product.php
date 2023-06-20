<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); // Loads the header.php template. ?>

		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>

		<?php get_template_part( 'breadcrumbs' ); // Loads the breadcrumbs.php template. ?>

		<div class="container">

			<div id="content">

		<?php if ( have_posts() ) { ?>

			<?php while ( have_posts() ) { the_post(); ?>

				<?php wc_get_template_part( 'content', 'single-product' ); ?>
				
			<?php } // End while loop. ?>

		<?php } else { ?>

			<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

		<?php } // End if check. ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

			</div><!-- #content -->

		<?php get_sidebar( 'woocommerce' ); // Loads the sidebar-bbpress.php template. ?>

		</div><!-- .container -->

<?php get_footer(); // Loads the footer.php template. ?>