<?php
/**
 * Order Customer Details
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) )  {
	exit; // Exit if accessed directly
}
?>
<header class="page-header">
	<h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>
</header>
<table class="shop_table customer_details table table-striped">
<?php
	if ($order->billing_email) {
		echo '<tr><th>'.__( 'Email:', 'woocommerce' ).'</th><td data-title="' . __( 'Email', 'woocommerce' ) . '">' . $order->billing_email . '</td></tr>';
	}	
	if ($order->billing_phone) {
		echo '<tr><th>'.__( 'Telephone:', 'woocommerce' ).'</th><td data-title="' . __( 'Telephone', 'woocommerce' ) . '">' . $order->billing_phone . '</td></tr>';
	}

	// Additional customer details hook
	do_action( 'woocommerce_order_details_after_customer_details', $order );
?>
</table>

<?php if ( ! wc_ship_to_billing_address_only() && get_option('woocommerce_calc_shipping') !== 'no' ) : ?>

<div class="row addresses">

	<div class="col-sm-6">

<?php endif; ?>

		<header class="page-header">
			<h3><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>
		</header>

		<address>
			<?php
				if ( !$order->get_formatted_billing_address() )  {
					_e( 'N/A', 'woocommerce' ); 
				} else {
					echo $order->get_formatted_billing_address();
				}
			?>
		</address>

<?php if ( ! wc_ship_to_billing_address_only() && get_option('woocommerce_calc_shipping') !== 'no' ) : ?>

	</div>

	<div class="col-sm-6">

		<header class="page-header">
			<h3><?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>
		</header>

		<address>
			<?php
				if ( !$order->get_formatted_shipping_address() ) {
					_e( 'N/A', 'woocommerce' ); 
				} else {
					echo $order->get_formatted_shipping_address();
				}
			?>
		</address>

	</div>

</div>

<?php endif; ?>

<div class="clear"></div>
