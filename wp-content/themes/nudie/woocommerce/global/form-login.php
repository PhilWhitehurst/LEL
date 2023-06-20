<?php
/**
 * Login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if (is_user_logged_in()) return;
?>
<form method="post" class="login form-horizontal" <?php if ( $hidden ) echo 'style="display:none;"'; ?>>
	
	<div class="form-group">
		<?php if ( $message ) echo wpautop( wptexturize( $message ) ); ?>
	</div>

	<div class="form-group">
		<label for="username" class="col-sm-3"><?php _e( 'Username or email', 'woocommerce' ); ?> <span class="required">*</span></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="username" id="username" />
		</div>
	</div>

	<div class="form-group">
		<label for="password" class="col-sm-3"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<div class="col-sm-9">
			<input class="form-control" type="password" name="password" id="password" />
		</div>
	</div>

	<div class="form-group">
		<?php wp_nonce_field( 'woocommerce-login' ); ?>
		<div class="col-sm-offset-3 col-sm-9">
			<input type="submit" class="btn btn-primary " name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>" />
			<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
			<label>
				<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
			</label>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<a class="lost_password" href="<?php echo esc_url( wp_lostpassword_url( home_url() ) ); ?>"><?php _e( 'Lost Password?', 'woocommerce' ); ?></a>
		</div>
	</div>

</form>