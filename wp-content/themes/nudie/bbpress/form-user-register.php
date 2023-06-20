<?php

/**
 * User Registration Form
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" role="form">

	<fieldset>

		<legend><?php _e( 'Create an Account', 'bbpress' ); ?></legend>

		<div class="alert alert-info">
			<p><?php _e( 'Your username must be unique, and cannot be changed later.', 'bbpress' ) ?></p>
			<p><?php _e( 'We use your email address to email you a secure password and verify your account.', 'bbpress' ) ?></p>
		</div>

		<div class="form-group" >
			<label for="user_login"><?php _e( 'Username', 'bbpress' ); ?>: </label>
			<input type="text" name="user_login" value="<?php bbp_sanitize_val( 'user_login' ); ?>" size="20" id="user_login" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div class="form-group">
			<label for="user_email"><?php _e( 'Email', 'bbpress' ); ?>: </label>
			<input type="text" name="user_email" value="<?php bbp_sanitize_val( 'user_email' ); ?>" size="20" id="user_email" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<?php do_action( 'register_form' ); ?>

		<div class="form-group">
			<button type="submit" tabindex="<?php bbp_tab_index(); ?>" name="user-submit" class="btn btn-default"><?php _e( 'Register', 'bbpress' ); ?></button>
			<?php bbp_user_register_fields(); ?>
		</div>

	</fieldset>

</form>
