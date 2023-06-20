<?php

/**
 * Template Name: bbPress - Topic Tags
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

		<div id="bbp-topic-tags" class="bbp-topic-tags">

			<header class="entry-header page-header">

				<h1 class="entry-title"><?php the_title(); ?></h1>

			</header><!-- .entry-header -->

			<div class="entry-content">

				<?php get_the_content() ? the_content() : _e( '<p>This is a collection of tags that are currently popular on our forums.</p>', 'bbpress' ); ?>

				<div id="bbpress-forums">

					<?php bbp_breadcrumb(); ?>

					<div id="bbp-topic-hot-tags">

						<?php wp_tag_cloud( array( 'smallest' => 9, 'largest' => 38, 'number' => 80, 'taxonomy' => bbp_get_topic_tag_tax_id() ) ); ?>

					</div>

				</div>

			</div>

		</div><!-- #bbp-topic-tags -->

	<?php endwhile; ?>

	<?php do_action( 'bbp_after_main_content' ); ?>

			</div><!-- #content -->

		<?php get_sidebar( 'bbpress' ); // Loads the sidebar-bbpress.php template. ?>

		</div><!-- .container -->

<?php get_footer(); // Loads the footer.php template. ?>