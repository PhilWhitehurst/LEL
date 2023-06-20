<?php

/**
 * Search Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<tr id="post-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

	<td>
		<!-- Don't display author for Forum results -->
	</td>

	<td>

		<div class="bbp-forum-title">

			<?php do_action( 'bbp_theme_before_forum_title' ); ?>

			<h3><?php _e( 'Forum: ', 'bbpress' ); ?><a href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a></h3>

			<?php do_action( 'bbp_theme_after_forum_title' ); ?>

		</div><!-- .bbp-forum-title -->

		<div class="bbp-forum-content">

			<?php do_action( 'bbp_theme_before_forum_content' ); ?>

			<?php bbp_forum_content(); ?>

			<?php do_action( 'bbp_theme_after_forum_content' ); ?>

		</div><!-- .bbp-forum-content -->

	</td>

</tr>