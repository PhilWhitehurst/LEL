<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices(); ?>

<form method="post" class="woocommerce-ResetPassword lost_reset_password">

   <div class="alert alert-warning"><?php echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'woocommerce') ); ?></div>

        <div class="row">

            <div class="col-sm-6">

                <div class="form-group">
                    <label for="password_1"><?php _e( 'New password', 'woocommerce' ); ?> <span class="required">*</span></label>
                    <input type="password" class="input-text" name="password_1" id="password_1" />
                </div>

            </div>

            <div class="col-sm-6">

                <div class="form-group">
                    <label for="password_2"><?php _e( 'Re-enter new password', 'woocommerce' ); ?> <span class="required">*</span></label>
                    <input type="password" class="input-text" name="password_2" id="password_2" />
                </div>

            </div>

        </div>

    </div>

    <input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
    <input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />

	<?php do_action( 'woocommerce_resetpassword_form' ); ?>

    <div class="form-group">
        <input type="hidden" name="wc_reset_password" value="true" />
        <input type="submit" class="btn btn-default" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>" /> 
    </div>

	<?php wp_nonce_field( 'reset_password' ); ?>

</form>
