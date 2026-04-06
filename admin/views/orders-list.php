<?php
/**
 * Orders List View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap hid-commerce-admin">
    <h1>
        <?php _e('Orders', 'hid-simple-commerce'); ?>
        <a href="#" id="hid-export-orders" class="page-title-action"><?php _e('Export', 'hid-simple-commerce'); ?></a>
    </h1>

    <div class="tablenav top">
        <form method="get" style="display: inline-block;">
            <input type="hidden" name="page" value="hid-commerce-orders">
            <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search orders...', 'hid-simple-commerce'); ?>">
            
            <select name="status">
                <option value=""><?php _e('All Statuses', 'hid-simple-commerce'); ?></option>
                <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'hid-simple-commerce'); ?></option>
                <option value="processing" <?php selected($status, 'processing'); ?>><?php _e('Processing', 'hid-simple-commerce'); ?></option>
                <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'hid-simple-commerce'); ?></option>
                <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'hid-simple-commerce'); ?></option>
            </select>
            
            <button type="submit" class="button"><?php _e('Filter', 'hid-simple-commerce'); ?></button>
        </form>
    </div>

    <table class="hid-admin-table wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Order Number', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Customer', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Total', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Payment Method', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Payment Status', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Order Status', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Date', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Actions', 'hid-simple-commerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="8"><?php _e('No orders found.', 'hid-simple-commerce'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong><?php echo esc_html($order->order_number); ?></strong></td>
                        <td>
                            <?php echo esc_html($order->customer_name); ?><br>
                            <small><?php echo esc_html($order->customer_email); ?></small>
                        </td>
                        <td><?php echo HID_Commerce_Checkout::format_currency($order->total_amount); ?></td>
                        <td><?php echo esc_html($order->payment_method); ?></td>
                        <td><span class="hid-order-status <?php echo esc_attr($order->payment_status); ?>"><?php echo esc_html($order->payment_status); ?></span></td>
                        <td><span class="hid-order-status <?php echo esc_attr($order->order_status); ?>"><?php echo esc_html($order->order_status); ?></span></td>
                        <td><?php echo date('M j, Y', strtotime($order->created_at)); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=hid-commerce-orders&action=view&id=' . $order->id); ?>"><?php _e('View', 'hid-simple-commerce'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total > $per_page): ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                $total_pages = ceil($total / $per_page);
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'current' => $page,
                    'total' => $total_pages,
                ));
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

