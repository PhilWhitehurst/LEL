<?php

/**
 * Customer processing order email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version     2.2.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";
echo "Thank you for your order at London Edinburgh London 2017.
To confirm your order, make your payment directly into our bank account. Our details are below.

Please use the order ID as your payment reference.

We must receive your payment within 14 days, or we will cancel your order.";


echo __("Your order has been received and is now being processed. Your order details are shown below for your reference:", 'woocommerce') . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text);

echo strtoupper(sprintf(__('Order number: %s', 'woocommerce'), $order->get_order_number())) . "\n";
echo date_i18n(__('jS F Y', 'woocommerce'), strtotime($order->order_date)) . "\n";

do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text);

echo "\n" . $order->email_order_items_table($order->is_download_permitted(), true, $order->has_status('processing'), '', '', true);

echo "==========\n\n";

if ($totals = $order->get_order_item_totals()) {
    foreach ($totals as $total) {
        echo $total['label'] . "\t " . $total['value'] . "\n";
    }
}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text);

do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text);

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'));
