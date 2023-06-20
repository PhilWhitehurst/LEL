<?php

/**
 * User Lost Password Form
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<form method="post" action="<?php bbp_wp_login_action( array( 'action' => 'lostpassword', 'context' => 'login_post' ) ); ?>" role="form">

	<fieldset>

		<legend><?php _e( 'Lost Password', 'bbpress' ); ?></legend>

		<div class="form-group">
			<label for="user_login" class="hide"><?php _e( 'Username or Email', 'bbpress' ); ?>: </label>
			<input type="text" name="user_login" value="" size="20" id="user_login" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<?php do_action( 'login_form', 'resetpass' ); ?>

		<div class="form-group">
			<button type="submit" tabindex="<?php bbp_tab_index(); ?>" name="user-submit" class="btn btn-default"><?php _e( 'Reset My Password', 'bbpress' ); ?></button>
			<?php bbp_user_lost_pass_fields(); ?>
		</div>

	</fieldset>

</form>
