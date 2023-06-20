<?php

/**
 * Template Name: bbPress - User Login
 *
 * @package bbPress
 * @subpackage Theme
 */

// No logged in users
bbp_logged_in_redirect();

// Begin Template
get_header(); ?>

		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta-bbpress.php template. ?>

		<?php get_template_part( 'breadcrumbs' ); // Loads the loop-meta.php template. ?>

		<div class="container">

			<div id="content">

	<?php do_action( 'bbp_before_main_content' ); ?>

	<?php do_action( 'bbp_template_notices' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div id="bbp-login" class="bbp-login">

			<header class="entry-header page-header">

				<h1 class="entry-title"><?php the_title(); ?></h1>

			</header><!-- .entry-header -->

			<div class="entry-content">

				<?php the_content(); ?>

				<div id="bbpress-forums">

					<?php bbp_breadcrumb(); ?>

					<?php bbp_get_template_part( 'form', 'user-login' ); ?>

				</div>

			</div>

		</div><!-- #bbp-login -->

	<?php endwhile; ?>

	<?php do_action( 'bbp_after_main_content' ); ?>

			</div><!-- #content -->

		<?php get_sidebar( 'bbpress' ); // Loads the sidebar-bbpress.php template. ?>

		</div><!-- .container -->

<?php get_footer(); // Loads the footer.php template. ?>
