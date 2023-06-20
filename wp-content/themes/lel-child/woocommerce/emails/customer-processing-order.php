<?php
/**
 * Customer processing order email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     1.6.4
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p>Thank you for your order at London Edinburgh London 2017.</p>
<?php
if ($order->payment_method_title === 'PayPal') {
    ?>
    <p>You have paid via PayPal.</p>
    <?php
} else {
    ?>
    <p>To confirm your order, make your payment directly into our bank account. Our details are below.<p>
    <p><strong>We must receive your payment within 14 days, or your order will be cancelled.</strong></p>
<?php } ?>


<?php do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text); ?>

<h2><?php printf(__('Order #%s', 'woocommerce'), $order->get_order_number()); ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
        <tr>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Product', 'woocommerce'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'woocommerce'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Price', 'woocommerce'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php echo $order->email_order_items_table($order->is_download_permitted(), true, $order->has_status('processing')); ?>
    </tbody>
    <tfoot>
        <?php
        if ($totals = $order->get_order_item_totals()) {
            $i = 0;
            foreach ($totals as $total) {
                $i++;
                ?><tr>
                    <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
                    <td style="text-align:left; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
                </tr><?php
    }
}
        ?>
    </tfoot>
</table>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_footer'); ?>
