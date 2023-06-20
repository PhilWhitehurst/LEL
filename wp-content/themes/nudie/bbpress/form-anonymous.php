<?php

/**
 * Anonymous User
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php if ( bbp_current_user_can_access_anonymous_user_form() ) : ?>

	<?php do_action( 'bbp_theme_before_anonymous_form' ); ?>

	<fieldset class="bbp-form">

		<legend><?php ( bbp_is_topic_edit() || bbp_is_reply_edit() ) ? _e( 'Author Information', 'bbpress' ) : _e( 'Your information:', 'bbpress' ); ?></legend>

		<?php do_action( 'bbp_theme_anonymous_form_extras_top' ); ?>

		<div class="form-group">
			<label for="bbp_anonymous_author"><?php _e( 'Name (required):', 'bbpress' ); ?></label><br />
			<input type="text" id="bbp_anonymous_author" class="form-control" value="<?php bbp_author_display_name(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_name" />
		</div>

		<div class="form-group">
			<label for="bbp_anonymous_email"><?php _e( 'Mail (will not be published) (required):', 'bbpress' ); ?></label><br />
			<input type="text" id="bbp_anonymous_email" class="form-control" value="<?php bbp_author_email(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_email" />
		</div>

		<div class="form-group">
			<label for="bbp_anonymous_website"><?php _e( 'Website:', 'bbpress' ); ?></label><br />
			<input type="text" id="bbp_anonymous_website" class="form-control" value="<?php bbp_author_url(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_website" />
		</div>

		<?php do_action( 'bbp_theme_anonymous_form_extras_bottom' ); ?>

	</fieldset>

	<?php do_action( 'bbp_theme_after_anonymous_form' ); ?>

<?php endif; ?>
