<?php

/**
 * User Login Form
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" role="form">

	<fieldset>

		<legend><?php _e( 'Log In', 'bbpress' ); ?></legend>

		<div class="form-group">
			<label for="user_login"><?php _e( 'Username', 'bbpress' ); ?>: </label>
			<input type="text" name="log" value="<?php bbp_sanitize_val( 'user_login', 'text' ); ?>" size="20" id="user_login" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div class="form-group">
			<label for="user_pass"><?php _e( 'Password', 'bbpress' ); ?>: </label>
			<input type="password" name="pwd" value="<?php bbp_sanitize_val( 'user_pass', 'password' ); ?>" size="20" id="user_pass" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div class="checkbox">
			<label for="rememberme">
				<input type="checkbox" name="rememberme" value="forever" <?php checked( bbp_get_sanitize_val( 'rememberme', 'checkbox' ) ); ?> id="rememberme" tabindex="<?php bbp_tab_index(); ?>" /> <?php _e( 'Keep me signed in', 'bbpress' ); ?>
			</label>
		</div>

		<?php do_action( 'login_form' ); ?>

		<div class="form-group">
			<button type="submit" tabindex="<?php bbp_tab_index(); ?>" name="user-submit" class="btn btn-default"><?php _e( 'Log In', 'bbpress' ); ?></button>
			<?php bbp_user_login_fields(); ?>
		</div>

	</fieldset>

</form>
