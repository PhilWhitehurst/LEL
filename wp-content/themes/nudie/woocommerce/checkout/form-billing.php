<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<div class="woocommerce-billing-fields">

	<div class="page-header">

	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3 class="entry-title"><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3 class="entry-title"><?php _e( 'Billing Details', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	</div>

	<?php do_action('woocommerce_before_checkout_billing_form', $checkout ); ?>

	<?php foreach ($checkout->checkout_fields['billing'] as $key => $field) : ?>

		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>

</div>