<?php
/**
 * Customers List Admin View
 *
 * @package HID_Simple_Commerce
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Include the list table class
require_once HID_COMMERCE_THEME_DIR . 'admin/class-customers-list-table.php';

// Create an instance of the list table
$customers_table = new HID_Customers_List_Table();
$customers_table->prepare_items();

// Handle messages
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'deleted':
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Customer deleted successfully.', 'hid-simple-commerce') . '</p></div>';
            break;
        case 'bulk_deleted':
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Selected customers deleted successfully.', 'hid-simple-commerce') . '</p></div>';
            break;
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Customers & Subscribers', 'hid-simple-commerce'); ?></h1>
    <hr class="wp-header-end">

    <style>
        .hid-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .hid-badge-customer {
            background-color: #d4edda;
            color: #155724;
        }
        .hid-badge-subscriber {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .customers-stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            flex: 1;
        }
        .stat-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }
        .stat-box .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #470108;
        }
        .stat-box.subscribers .stat-number {
            color: #0c5460;
        }
    </style>

    <?php
    // Get statistics
    global $wpdb;
    $customers_table_name = $wpdb->prefix . 'hid_customers';
    $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table_name WHERE customer_type = 'customer'");
    $total_subscribers = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table_name WHERE customer_type = 'subscriber'");
    $total = $total_customers + $total_subscribers;
    ?>

    <div class="customers-stats">
        <div class="stat-box">
            <h3><?php _e('Total', 'hid-simple-commerce'); ?></h3>
            <div class="stat-number"><?php echo number_format((int)$total); ?></div>
        </div>
        <div class="stat-box">
            <h3><?php _e('Customers', 'hid-simple-commerce'); ?></h3>
            <div class="stat-number"><?php echo number_format((int)$total_customers); ?></div>
        </div>
        <div class="stat-box subscribers">
            <h3><?php _e('Subscribers', 'hid-simple-commerce'); ?></h3>
            <div class="stat-number"><?php echo number_format((int)$total_subscribers); ?></div>
        </div>
    </div>

    <form method="get">
        <input type="hidden" name="page" value="hid-commerce-customers" />
        <?php
        $customers_table->search_box(__('Search', 'hid-simple-commerce'), 'search');
        $customers_table->display();
        ?>
    </form>
</div>

