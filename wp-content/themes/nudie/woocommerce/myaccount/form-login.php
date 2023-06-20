<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php wc_print_notices(); ?>

<?php do_action('woocommerce_before_customer_login_form'); ?>

<?php if (get_option('woocommerce_enable_myaccount_registration')=='yes') : ?>

<div class="row" id="customer_login">

	<div class="col-sm-6">

<?php endif; ?>
		
		<div class="page-header">
			<h2 class="entry-title"><?php _e( 'Login', 'woocommerce' ); ?></h2>
		</div>

		<form method="post" class="login">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="form-group">
				<label for="username"><?php _e( 'Username or email', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" class="form-control" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>"/>
			</p>
			<p class="form-group">
				<label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input class="form-control" type="password" name="password" id="password" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-group">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<input type="submit" class="btn btn-default" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>" />
				<label for="rememberme" class="inline">
					<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
				</label>
			</p>
			<p class="form-group">
				<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php esc_attr_e( 'Lost your password?', 'woocommerce' ); ?></a>
			</p>
		
			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php if (get_option('woocommerce_enable_myaccount_registration')=='yes') : ?>

	</div>

	<div class="col-sm-6">

		<div class="page-header">
			<h2 class="entry-title"><?php _e( 'Register', 'woocommerce' ); ?></h2>
		</div>

		<form method="post" class="register">

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<div class="row">

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<div class="col-sm-6">

					<div class="form-group">
						<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
						<input type="text" class="form-control" name="username" id="reg_username" value="<?php if (isset($_POST['username'])) echo esc_attr($_POST['username']); ?>" />
					</div>

				</div>

				<div class="col-sm-6">

			<?php else : ?>

				<div class="col-sm-12">

			<?php endif; ?>

					<div class="form-group">
						<label for="reg_email"><?php _e( 'Email', 'woocommerce' ); ?> <span class="required">*</span></label>
						<input type="email" class="form-control" name="email" id="reg_email" value="<?php if (isset($_POST['email'])) echo esc_attr($_POST['email']); ?>" />
					</div>

				</div>

			</div>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

			<div class="row">

				<div class="col-sm-12">

					<div class="form-group">
						<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
						<input type="password" class="form-control" name="password" id="reg_password" />
					</div>

				</div>

			</div>

			<?php endif; ?>

			<!-- Spam Trap -->
			<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<?php do_action( 'register_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<input type="submit" class="btn btn-default" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>" />
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

</div>
<?php endif; ?>

<?php do_action('woocommerce_after_customer_login_form'); ?>