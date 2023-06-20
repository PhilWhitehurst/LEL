<?php

/**
 * Template Name: bbPress - Forums (Index)
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); ?>

		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta-bbpress.php template. ?>

		<?php get_template_part( 'breadcrumbs' ); // Loads the loop-meta.php template. ?>

		<div class="container">

			<div id="content">

	<?php do_action( 'bbp_before_main_content' ); ?>

	<?php do_action( 'bbp_template_notices' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div id="forum-front" class="bbp-forum-front">

			<header class="entry-header page-header">

				<h1 class="entry-title"><?php the_title(); ?></h1>

			</header><!-- .entry-header -->

			<div class="entry-content">

				<?php the_content(); ?>

				<?php bbp_get_template_part( 'content', 'archive-forum' ); ?>

			</div>

		</div><!-- #forum-front -->

	<?php endwhile; ?>

	<?php do_action( 'bbp_after_main_content' ); ?>

			</div><!-- #content -->

		<?php get_sidebar( 'bbpress' ); // Loads the sidebar-bbpress.php template. ?>

		</div><!-- .container -->

<?php get_footer(); // Loads the footer.php template. ?>