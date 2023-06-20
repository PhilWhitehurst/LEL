<?php

/**
 * Topics Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_topics_loop' ); ?>

<table id="bbp-forum-<?php bbp_forum_id(); ?>" class="bbp-topics table table-striped">

	<thead>

		<tr>
			<th class="bbp-topic-title"><?php _e( 'Topic', 'bbpress' ); ?></th>
			<th class="bbp-topic-voice-count"><?php _e( 'Voices', 'bbpress' ); ?></th>
			<th class="bbp-topic-reply-count"><?php bbp_show_lead_topic() ? _e( 'Replies', 'bbpress' ) : _e( 'Posts', 'bbpress' ); ?></th>
			<th class="bbp-topic-freshness"><?php _e( 'Freshness', 'bbpress' ); ?></th>
		</tr>

	</thead>

	<tbody>

		<?php while ( bbp_topics() ) : bbp_the_topic(); ?>

			<?php bbp_get_template_part( 'loop', 'single-topic' ); ?>

		<?php endwhile; ?>

	</tbody>

</table><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->

<?php do_action( 'bbp_template_after_topics_loop' ); ?>
