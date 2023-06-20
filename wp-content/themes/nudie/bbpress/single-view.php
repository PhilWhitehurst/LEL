<?php
// File Security Check
if ( ! function_exists( 'wp' ) && ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}

get_header(); // Loads the header.php template. ?>

		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>

		<?php get_template_part( 'breadcrumbs' ); // Loads the loop-meta.php template. ?>

		<div class="container">

			<div id="content">

			<?php do_action( 'bbp_before_main_content' ); ?>

			<?php do_action( 'bbp_template_notices' ); ?>

			<div id="bbp-view-<?php bbp_view_id(); ?>" class="bbp-view">

				<h1 class="entry-title"><?php bbp_view_title(); ?></h1>

				<div class="entry-content">

					<?php bbp_get_template_part( 'content', 'single-view' ); ?>

				</div>

			</div><!-- #bbp-view-<?php bbp_view_id(); ?> -->

			<?php do_action( 'bbp_after_main_content' ); ?>

			</div><!-- #content -->

		<?php get_sidebar( 'bbpress' ); // Loads the sidebar-bbpress.php template. ?>

		</div><!-- .container -->

<?php get_footer(); // Loads the footer.php template. ?>