<?php

/**
 * Replies Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_replies_loop' ); ?>

<div id="topic-<?php bbp_topic_id(); ?>-replies" class="forums bbp-replies">

	<div class="bbp-replies-header">

		<div class="row">

			<div class="col-sm-2"><span><?php  _e( 'Author',  'bbpress' ); ?></span></div>

			<div class="col-sm-10"><span>

				<?php if ( !bbp_show_lead_topic() ) : ?>

					<?php _e( 'Posts', 'bbpress' ); ?>

				<?php else : ?>

					<?php _e( 'Replies', 'bbpress' ); ?>

				<?php endif; ?>

			</span></div>

		</div>

	</div><!-- .bbp-replies-header -->

	<div class="bbp-replies-body">

		<?php if ( bbp_thread_replies() ) : ?>

			<ul>
			<?php bbp_list_replies( array( 'style' => 'ul' ) ); ?>
			</ul>

		<?php else : ?>

			<?php while ( bbp_replies() ) : bbp_the_reply(); ?>

				<?php bbp_get_template_part( 'loop', 'single-reply' ); ?>

			<?php endwhile; ?>

		<?php endif; ?>

	</div><!-- .bbp-replies-body -->

</div><!-- #topic-<?php bbp_topic_id(); ?>-replies -->

<?php do_action( 'bbp_template_after_replies_loop' ); ?>
