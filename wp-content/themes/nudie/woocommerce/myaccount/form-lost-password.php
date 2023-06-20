<?php
/**
 * Lost password form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wc_print_notices(); ?>

<form method="post" class="lost_reset_password">

    <div class="alert alert-warning"><?php echo apply_filters( 'woocommerce_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></div>

    <div class="form-group">
        <label for="user_login"><?php _e( 'Username or email', 'woocommerce' ); ?></label>
        <input class="form-control" type="text" name="user_login" id="user_login" />
    </div>

    <?php do_action( 'woocommerce_lostpassword_form' ); ?>

    <div class="form-group">
        <input type="hidden" name="wc_reset_password" value="true" />
        <input type="submit" class="btn btn-default" value="<?php esc_attr_e( 'Reset Password', 'woocommerce' ); ?>" /> 
    </div>
    
    <?php wp_nonce_field( 'lost_password' ); ?>

</form>