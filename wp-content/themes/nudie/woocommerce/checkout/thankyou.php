<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $order ) : ?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<div class="alert alert-warning woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></div>

		<div class="woocommerce-thankyou-order-failed-actions">
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn btn-primary"><?php _e( 'Pay', 'woocommerce' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="btn btn-default"><?php _e( 'My Account', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>

	<?php else : ?>

		<div class="alert alert-success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></div>

		<table class="table table-striped order_details woocommerce-thankyou-order-details">
			<tr class="order">
				<th><?php _e( 'Order:', 'woocommerce' ); ?></th>
				<td><?php echo $order->get_order_number(); ?></td>
			</tr>
			<tr class="date">
				<th><?php _e( 'Date:', 'woocommerce' ); ?></th>
				<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></td>
			</tr>
			<tr class="total">
				<th><?php _e( 'Total:', 'woocommerce' ); ?></th>
				<td><?php echo $order->get_formatted_order_total(); ?></td>
			</tr>
			<?php if ( $order->payment_method_title ) : ?>
			<tr class="method">
				<th><?php _e( 'Payment method:', 'woocommerce' ); ?></th>
				<td><?php echo $order->payment_method_title; ?></td>
			</tr>
			<?php endif; ?>
		</table>

	<?php endif; ?>

	<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>

<?php else : ?>

	<div class="alert alert-success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></div>

<?php endif; ?>