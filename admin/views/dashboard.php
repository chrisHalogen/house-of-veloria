<?php
/**
 * Admin Dashboard View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap hid-commerce-admin">
    <h1><?php _e('HID Commerce Dashboard', 'hid-simple-commerce'); ?></h1>

    <div class="hid-dashboard-cards">
        <div class="hid-dashboard-card">
            <h3><?php _e('Total Revenue', 'hid-simple-commerce'); ?></h3>
            <div class="value"><?php echo HID_Commerce_Checkout::format_currency($total_revenue); ?></div>
        </div>

        <div class="hid-dashboard-card">
            <h3><?php _e('Total Orders', 'hid-simple-commerce'); ?></h3>
            <div class="value"><?php echo number_format($total_orders); ?></div>
        </div>

        <div class="hid-dashboard-card">
            <h3><?php _e('Low Stock Items', 'hid-simple-commerce'); ?></h3>
            <div class="value" style="color: #f0b429;"><?php echo count($low_stock_products) + count($low_stock_variants); ?></div>
        </div>

        <div class="hid-dashboard-card">
            <h3><?php _e('Pending Orders', 'hid-simple-commerce'); ?></h3>
            <div class="value" style="color: #3a5a8b;"><?php echo count(array_filter($recent_orders, function($o) { return $o->order_status == 'pending'; })); ?></div>
        </div>
    </div>

    <?php if (!empty($low_stock_products) || !empty($low_stock_variants)): ?>
        <div class="hid-low-stock-alert">
            <h3><?php _e('Low Stock Alert', 'hid-simple-commerce'); ?></h3>
            <p><?php _e('The following items are running low on stock:', 'hid-simple-commerce'); ?></p>
            <ul>
                <?php foreach ($low_stock_products as $product): ?>
                    <li><?php echo esc_html($product->name); ?> - <?php echo $product->stock_quantity; ?> left</li>
                <?php endforeach; ?>
                <?php foreach ($low_stock_variants as $variant): ?>
                    <li><?php echo esc_html($variant->product_name . ' - ' . $variant->variant_display_name); ?> - <?php echo $variant->stock_quantity; ?> left</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
        <div>
            <h2><?php _e('Recent Orders', 'hid-simple-commerce'); ?></h2>
            <table class="hid-admin-table">
                <thead>
                    <tr>
                        <th><?php _e('Order Number', 'hid-simple-commerce'); ?></th>
                        <th><?php _e('Customer', 'hid-simple-commerce'); ?></th>
                        <th><?php _e('Total', 'hid-simple-commerce'); ?></th>
                        <th><?php _e('Status', 'hid-simple-commerce'); ?></th>
                        <th><?php _e('Date', 'hid-simple-commerce'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td><a href="<?php echo admin_url('admin.php?page=hid-commerce-orders&action=view&id=' . $order->id); ?>"><?php echo esc_html($order->order_number); ?></a></td>
                            <td><?php echo esc_html($order->customer_name); ?></td>
                            <td><?php echo HID_Commerce_Checkout::format_currency($order->total_amount); ?></td>
                            <td><span class="hid-order-status <?php echo esc_attr($order->order_status); ?>"><?php echo esc_html($order->order_status); ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($order->created_at)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div>
            <h2><?php _e('Best Sellers', 'hid-simple-commerce'); ?></h2>
            <table class="hid-admin-table">
                <thead>
                    <tr>
                        <th><?php _e('Product', 'hid-simple-commerce'); ?></th>
                        <th><?php _e('Sold', 'hid-simple-commerce'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($best_sellers as $product): ?>
                        <tr>
                            <td><?php echo esc_html($product->product_name); ?></td>
                            <td><?php echo number_format($product->total_sold); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2 style="margin-top: 30px;"><?php _e('Order Status', 'hid-simple-commerce'); ?></h2>
            <table class="hid-admin-table">
                <tbody>
                    <?php foreach ($order_status_breakdown as $status): ?>
                        <tr>
                            <td><?php echo esc_html(ucfirst($status->order_status)); ?></td>
                            <td><strong><?php echo number_format($status->count); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

