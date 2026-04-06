<?php
/**
 * Order View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap hid-commerce-admin">
    <h1><?php _e('Order Details', 'hid-simple-commerce'); ?> - <?php echo esc_html($order->order_number); ?></h1>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <div>
            <div class="postbox">
                <div class="inside">
                    <h2><?php _e('Order Items', 'hid-simple-commerce'); ?></h2>
                    <table class="hid-admin-table">
                        <thead>
                            <tr>
                                <th><?php _e('Product', 'hid-simple-commerce'); ?></th>
                                <th><?php _e('Quantity', 'hid-simple-commerce'); ?></th>
                                <th><?php _e('Price', 'hid-simple-commerce'); ?></th>
                                <th><?php _e('Subtotal', 'hid-simple-commerce'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <?php echo esc_html($item->product_name); ?>
                                        <?php if ($item->variant_name): ?>
                                            <br><small><?php echo esc_html($item->variant_name); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html($item->quantity); ?></td>
                                    <td><?php echo HID_Commerce_Checkout::format_currency($item->price); ?></td>
                                    <td><?php echo HID_Commerce_Checkout::format_currency($item->subtotal); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;"><strong><?php _e('Subtotal:', 'hid-simple-commerce'); ?></strong></td>
                                <td><strong><?php echo HID_Commerce_Checkout::format_currency($order->subtotal); ?></strong></td>
                            </tr>
                            <?php if ($order->tax_amount > 0): ?>
                                <tr>
                                    <td colspan="3" style="text-align: right;"><strong><?php echo esc_html(get_option('hid_commerce_tax_label', 'VAT')); ?>:</strong></td>
                                    <td><strong><?php echo HID_Commerce_Checkout::format_currency($order->tax_amount); ?></strong></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3" style="text-align: right;"><strong><?php _e('Total:', 'hid-simple-commerce'); ?></strong></td>
                                <td><strong><?php echo HID_Commerce_Checkout::format_currency($order->total_amount); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <?php if ($order->order_notes): ?>
                        <h3><?php _e('Customer Notes', 'hid-simple-commerce'); ?></h3>
                        <p><?php echo nl2br(esc_html($order->order_notes)); ?></p>
                    <?php endif; ?>

                    <?php if ($order->payment_method == 'bank_transfer' && $order->payment_proof_url): ?>
                        <h3><?php _e('Payment Proof', 'hid-simple-commerce'); ?></h3>
                        <p><a href="<?php echo esc_url($order->payment_proof_url); ?>" target="_blank"><?php _e('View Payment Proof', 'hid-simple-commerce'); ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div>
            <div class="postbox">
                <div class="inside">
                    <h3><?php _e('Order Status', 'hid-simple-commerce'); ?></h3>
                    <select class="hid-order-status-select" data-order-id="<?php echo esc_attr($order->id); ?>">
                        <option value="pending" <?php selected($order->order_status, 'pending'); ?>><?php _e('Pending', 'hid-simple-commerce'); ?></option>
                        <option value="processing" <?php selected($order->order_status, 'processing'); ?>><?php _e('Processing', 'hid-simple-commerce'); ?></option>
                        <option value="completed" <?php selected($order->order_status, 'completed'); ?>><?php _e('Completed', 'hid-simple-commerce'); ?></option>
                        <option value="cancelled" <?php selected($order->order_status, 'cancelled'); ?>><?php _e('Cancelled', 'hid-simple-commerce'); ?></option>
                    </select>

                    <h3><?php _e('Payment Status', 'hid-simple-commerce'); ?></h3>
                    <p><span class="hid-order-status <?php echo esc_attr($order->payment_status); ?>"><?php echo esc_html($order->payment_status); ?></span></p>
                    
                    <?php if ($order->payment_method == 'bank_transfer' && $order->payment_status == 'pending'): ?>
                        <button type="button" class="button button-primary hid-confirm-payment-btn" data-order-id="<?php echo esc_attr($order->id); ?>" style="margin-top: 10px; width: 100%;">
                            <i class="fas fa-check-circle"></i> <?php _e('Confirm Payment Received', 'hid-simple-commerce'); ?>
                        </button>
                    <?php endif; ?>

                    <h3><?php _e('Customer Information', 'hid-simple-commerce'); ?></h3>
                    <p>
                        <strong><?php echo esc_html($order->customer_name); ?></strong><br>
                        <?php echo esc_html($order->customer_email); ?><br>
                        <?php echo esc_html($order->customer_phone); ?>
                    </p>

                    <h3><?php _e('Shipping Address', 'hid-simple-commerce'); ?></h3>
                    <p><?php echo nl2br(esc_html($order->shipping_address)); ?></p>
                    <p><strong><?php _e('Location:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($order->shipping_location); ?></p>
                    <p><strong><?php _e('Courier:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($order->shipping_courier); ?></p>

                    <h3><?php _e('Payment Method', 'hid-simple-commerce'); ?></h3>
                    <p><?php echo esc_html($order->payment_method); ?></p>

                    <h3><?php _e('Order Date', 'hid-simple-commerce'); ?></h3>
                    <p><?php echo date('F j, Y, g:i a', strtotime($order->created_at)); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

