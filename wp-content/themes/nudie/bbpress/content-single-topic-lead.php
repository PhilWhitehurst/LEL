<?php

/**
 * Single Topic Lead Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_lead_topic' ); ?>

<div id="bbp-topic-<?php bbp_topic_id(); ?>-lead" class="bbp-lead-topic">

	<div class="bbp-lead-header">

		<div class="row">

			<div class="col-sm-2"><span><?php  _e( 'Creator',  'bbpress' ); ?></span></div>

			<div class="col-sm-10"><span>

				<?php _e( 'Topic', 'bbpress' ); ?>

				<span class="toggle-right">

				<?php bbp_topic_subscription_link( array( 'before' => '' ) ); ?>

				<?php bbp_topic_favorite_link(); ?>

				</span>

			</span></div><!-- .bbp-topic-content -->

		</div>

	</div><!-- .bbp-lead-header -->

	<div class="bbp-lead-body">

		<div id="post-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>

			<div class="row">

				<div class="col-sm-2">

					<div class="bbp-topic-author">

						<?php do_action( 'bbp_theme_before_topic_author_details' ); ?>

						<?php bbp_topic_author_link( array( 'sep' => '<br />', 'show_role' => true ) ); ?>

						<?php if ( bbp_is_user_keymaster() ) : ?>

							<?php do_action( 'bbp_theme_before_topic_author_admin_details' ); ?>

							<div class="bbp-topic-ip"><?php bbp_author_ip( bbp_get_topic_id() ); ?></div>

							<?php do_action( 'bbp_theme_after_topic_author_admin_details' ); ?>

						<?php endif; ?>

						<?php do_action( 'bbp_theme_after_topic_author_details' ); ?>

					</div><!-- .bbp-topic-author -->

				</div>

				<div class="col-sm-10">

					<div class="bbp-topic-content">

						<?php do_action( 'bbp_theme_before_topic_content' ); ?>

						<?php bbp_topic_content(); ?>

						<?php do_action( 'bbp_theme_after_topic_content' ); ?>

					</div><!-- .bbp-topic-content -->

				</div>

			</div>

			<div class="row">

				<div class="col-sm-offset-2 col-sm-10">

					<div class="bbp-meta">

						<span class="bbp-topic-post-date"><?php bbp_topic_post_date(); ?></span>

						<a href="<?php bbp_topic_permalink(); ?>" class="bbp-topic-permalink">#<?php bbp_topic_id(); ?></a>

						<?php do_action( 'bbp_theme_before_topic_admin_links' ); ?>

						<?php bbp_topic_admin_links(); ?>

						<?php do_action( 'bbp_theme_after_topic_admin_links' ); ?>

					</div><!-- .bbp-meta -->

				</div>

			</div>

		</div><!-- #post-<?php bbp_topic_id(); ?> -->

	</div><!-- .bbp-body -->

</div><!-- #bbp-topic-<?php bbp_topic_id(); ?>-lead -->

<?php do_action( 'bbp_template_after_lead_topic' ); ?>
