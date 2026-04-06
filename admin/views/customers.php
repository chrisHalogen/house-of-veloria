<?php

/**
 * Customers View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap hid-commerce-admin">
    <h1><?php _e('Customers', 'hid-simple-commerce'); ?></h1>

    <div class="tablenav top">
        <form method="get" style="display: inline-block;">
            <input type="hidden" name="page" value="hid-commerce-customers">
            <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search customers...', 'hid-simple-commerce'); ?>">
            <button type="submit" class="button"><?php _e('Search', 'hid-simple-commerce'); ?></button>
        </form>
    </div>

    <table class="hid-admin-table wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Name', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Email', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Phone', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Orders', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Member Since', 'hid-simple-commerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="5"><?php _e('No customers found.', 'hid-simple-commerce'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $customer): ?>
                    <?php
                    // Get order count for customer
                    $order_count = $db->get_orders_count(array('search' => $customer->email));
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html($customer->name); ?></strong></td>
                        <td><?php echo esc_html($customer->email); ?></td>
                        <td><?php echo esc_html($customer->phone); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=hid-commerce-orders&s=' . urlencode($customer->email)); ?>">
                                <?php echo number_format($order_count); ?>
                            </a>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($customer->created_at)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>