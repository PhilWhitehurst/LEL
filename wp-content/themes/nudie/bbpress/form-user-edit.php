<?php

/**
 * bbPress User Profile Edit Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<form class="form-horizontal" action="<?php bbp_user_profile_edit_url( bbp_get_displayed_user_id() ); ?>" method="post" enctype="multipart/form-data" role="form">

	<?php do_action( 'bbp_user_edit_before' ); ?>

	<fieldset>

		<legend><?php _e( 'Name', 'bbpress' ) ?></legend>

		<?php do_action( 'bbp_user_edit_before_name' ); ?>

		<div class="form-group">
			<label for="first_name" class="col-sm-2"><?php _e( 'First Name', 'bbpress' ) ?></label>
			<div class="col-sm-10">
				<input type="text" name="first_name" id="first_name" value="<?php bbp_displayed_user_field( 'first_name', 'edit' ); ?>" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
			</div>
		</div>

		<div class="form-group">
			<label for="last_name" class="col-sm-2"><?php _e( 'Last Name', 'bbpress' ) ?></label>
			<div class="col-sm-10">
				<input type="text" name="last_name" id="last_name" value="<?php bbp_displayed_user_field( 'last_name', 'edit' ); ?>" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
			</div>
		</div>

		<div class="form-group">
			<label for="nickname" class="col-sm-2"><?php _e( 'Nickname', 'bbpress' ); ?></label>
			<div class="col-sm-10">
				<input type="text" name="nickname" id="nickname" value="<?php bbp_displayed_user_field( 'nickname', 'edit' ); ?>" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
			</div>
		</div>

		<div class="form-group">
			<label for="display_name" class="col-sm-2"><?php _e( 'Display Name', 'bbpress' ) ?></label>
			<div class="col-sm-10">
				<?php bootstrap_bbpress_edit_user_display_name(); ?>
			</div>
		</div>

		<?php do_action( 'bbp_user_edit_after_name' ); ?>

	</fieldset>

	<fieldset>

		<legend><?php _e( 'Contact Info', 'bbpress' ) ?></legend>

		<?php do_action( 'bbp_user_edit_before_contact' ); ?>

		<div class="form-group">
			<label for="url" class="col-sm-2"><?php _e( 'Website', 'bbpress' ) ?></label>
			<div class="col-sm-10">
				<input type="text" name="url" id="url" value="<?php bbp_displayed_user_field( 'user_url', 'edit' ); ?>" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
			</div>
		</div>

		<?php foreach ( bbp_edit_user_contact_methods() as $name => $desc ) : ?>

		<div class="form-group">
			<label for="<?php echo esc_attr( $name ); ?>" class="col-sm-2"><?php echo apply_filters( 'user_' . $name . '_label', $desc ); ?></label>
			<div class="col-sm-10">
				<input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php bbp_displayed_user_field( $name, 'edit' ); ?>" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
			</div>
		</div>

		<?php endforeach; ?>

		<?php do_action( 'bbp_user_edit_after_contact' ); ?>

	</fieldset>

	<fieldset>

		<legend><?php bbp_is_user_home_edit() ? _e( 'About Yourself', 'bbpress' ) : _e( 'About the user', 'bbpress' ); ?></legend>

		<?php do_action( 'bbp_user_edit_before_about' ); ?>

		<div class="form-group">
			<label for="description" class="col-sm-2"><?php _e( 'Biographical Info', 'bbpress' ); ?></label>
			<div class="col-sm-10">
				<textarea name="description" id="description" class="form-control" rows="5" cols="30" tabindex="<?php bbp_tab_index(); ?>"><?php bbp_displayed_user_field( 'description', 'edit' ); ?></textarea>
			</div>
		</div>

		<?php do_action( 'bbp_user_edit_after_about' ); ?>

	</fieldset>

	<fieldset>

		<legend><?php _e( 'Account', 'bbpress' ) ?></legend>

		<?php do_action( 'bbp_user_edit_before_account' ); ?>

		<div class="form-group">
			<label for="user_login" class="col-sm-2"><?php _e( 'Username', 'bbpress' ); ?></label>
			<div class="col-sm-10">
				<input type="text" name="user_login" id="user_login" value="<?php bbp_displayed_user_field( 'user_login', 'edit' ); ?>" disabled="disabled" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
			</div>
		</div>

		<div class="form-group">
			<label for="email" class="col-sm-2"><?php _e( 'Email', 'bbpress' ); ?></label>
			<div class="col-sm-10">
				<input type="text" name="email" id="email" value="<?php bbp_displayed_user_field( 'user_email', 'edit' ); ?>" class="form-control" tabindex="<?php bbp_tab_index(); ?>" />
			</div>
			<?php

			// Handle address change requests
			$new_email = get_option( bbp_get_displayed_user_id() . '_new_email' );
			if ( !empty( $new_email ) && $new_email !== bbp_get_displayed_user_field( 'user_email', 'edit' ) ) : ?>

				<span class="updated inline">

					<?php printf( __( 'There is a pending email address change to <code>%1$s</code>. <a href="%2$s">Cancel</a>', 'bbpress' ), $new_email['newemail'], esc_url( self_admin_url( 'user.php?dismiss=' . bbp_get_current_user_id()  . '_new_email' ) ) ); ?>

				</span>

			<?php endif; ?>
		</div>

		<div id="password">

			<fieldset class="password">

				<div class="form-group">

					<label for="pass1" class="col-sm-2" ><?php _e( 'New Password', 'bbpress' ); ?></label>

					<div class="col-sm-5">
						<input type="password" name="pass1" id="pass1" class="form-control" size="16" value="" autocomplete="off" tabindex="<?php bbp_tab_index(); ?>" />
						<span class="help-block"><?php _e( 'If you would like to change the password type a new one. Otherwise leave this blank.', 'bbpress' ); ?></span>
					</div>

					<div class="col-sm-5">
						<input type="password" name="pass2" id="pass2" class="form-control" size="16" value="" autocomplete="off" tabindex="<?php bbp_tab_index(); ?>" />
						<span class="help-block"><?php _e( 'Type your new password again.', 'bbpress' ); ?></span><br />
					</div>

				</div>

				<div class="form-group">
					<div class="col-sm-10 col-sm-offset-2">
						<div id="pass-strength-result"></div>
						<span class="help-block indicator-hint"><?php _e( 'Your password should be at least ten characters long. Use upper and lower case letters, numbers, and symbols to make it even stronger.', 'bbpress' ); ?></span>
					</div>
				</div>

			</fieldset>

		</div>

		<?php do_action( 'bbp_user_edit_after_account' ); ?>

	</fieldset>

	<?php if ( current_user_can( 'edit_users' ) && ! bbp_is_user_home_edit() ) : ?>

	<fieldset>

		<legend><?php _e( 'User Role', 'bbpress' ); ?></legend>

		<?php do_action( 'bbp_user_edit_before_role' ); ?>

		<?php if ( is_multisite() && is_super_admin() && current_user_can( 'manage_network_options' ) ) : ?>

			<div class="form-group">
				<div class="col-sm-10 col-sm-offset-2" >
					<div class="checkbox">
						<label for="super_admin" ><input type="checkbox" id="super_admin" name="super_admin"<?php checked( is_super_admin( bbp_get_displayed_user_id() ) ); ?> tabindex="<?php bbp_tab_index(); ?>" /> <?php _e( 'Grant this user super admin privileges for the Network.', 'bbpress' ); ?></label>
					</div>
				</div>
			</div>

		<?php endif; ?>

		<?php bbp_get_template_part( 'form', 'user-roles' ); ?>

		<?php do_action( 'bbp_user_edit_after_role' ); ?>

	</fieldset>

	<?php endif; ?>

	<?php do_action( 'bbp_user_edit_after' ); ?>

	<fieldset>

		<legend><?php _e( 'Save Changes', 'bbpress' ); ?></legend>

		<div>

			<?php bbp_edit_user_form_fields(); ?>

			<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_user_edit_submit" name="bbp_user_edit_submit" class="btn btn-default"><?php bbp_is_user_home_edit() ? _e( 'Update Profile', 'bbpress' ) : _e( 'Update User', 'bbpress' ); ?></button>

		</div>

	</fieldset>

</form>