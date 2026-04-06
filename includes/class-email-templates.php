<?php
/**
 * Email template system class
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Email_Templates {

    /**
     * Get email template
     */
    public static function get_template($template_name, $variables = array()) {
        $template_path = HID_COMMERCE_THEME_DIR . 'templates/email/' . $template_name . '.php';
        
        if (!file_exists($template_path)) {
            return self::get_default_template($variables);
        }

        ob_start();
        extract($variables);
        include $template_path;
        return ob_get_clean();
    }

    /**
     * Get default template wrapper
     */
    private static function get_default_template($variables) {
        $logo_url = get_option('hid_commerce_site_logo', '');
        $primary_color = get_option('hid_commerce_color_primary', '#470108');
        $secondary_color = get_option('hid_commerce_color_secondary', '#B4A06A');
        $background_color = get_option('hid_commerce_color_background', '#F8F4E9');
        
        $content = isset($variables['content']) ? $variables['content'] : '';

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: "Montserrat", Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: ' . esc_attr($background_color) . ';
                    margin: 0;
                    padding: 0;
                }
                .email-wrapper {
                    max-width: 600px;
                    margin: 20px auto;
                    background: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .email-header {
                    background: ' . esc_attr($primary_color) . ';
                    padding: 30px;
                    text-align: center;
                }
                .email-header img {
                    max-width: 200px;
                    height: auto;
                }
                .email-body {
                    padding: 40px 30px;
                }
                .email-footer {
                    background: ' . esc_attr($background_color) . ';
                    padding: 20px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                }
                .button {
                    display: inline-block;
                    padding: 12px 30px;
                    background: ' . esc_attr($secondary_color) . ';
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 4px;
                    margin: 10px 0;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                table th,
                table td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #eee;
                }
                table th {
                    background: ' . esc_attr($background_color) . ';
                    font-weight: 600;
                }
            </style>
        </head>
        <body>
            <div class="email-wrapper">
                <div class="email-header">
                    ' . ($logo_url ? '<img src="' . esc_url($logo_url) . '" alt="Logo">' : '<h1 style="color: #ffffff; margin: 0;">' . get_bloginfo('name') . '</h1>') . '
                </div>
                <div class="email-body">
                    ' . $content . '
                </div>
                <div class="email-footer">
                    <p>&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. ' . __('All rights reserved.', 'hid-simple-commerce') . '</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Get order confirmation template
     */
    public static function get_order_confirmation_template($order, $order_items) {
        $content = '
        <h2 style="color: ' . get_option('hid_commerce_color_primary', '#470108') . '; font-family: \'Dancing Script\', cursive;">' . __('Order Confirmation', 'hid-simple-commerce') . '</h2>
        <p>' . sprintf(__('Hi %s,', 'hid-simple-commerce'), esc_html($order->customer_name)) . '</p>
        <p>' . __('Thank you for your order! Your order has been received and is being processed.', 'hid-simple-commerce') . '</p>
        
        <h3>' . __('Order Details', 'hid-simple-commerce') . '</h3>
        <p><strong>' . __('Order Number:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->order_number) . '</p>
        <p><strong>' . __('Order Date:', 'hid-simple-commerce') . '</strong> ' . date('F j, Y, g:i a', strtotime($order->created_at)) . '</p>
        <p><strong>' . __('Payment Method:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->payment_method) . '</p>
        
        <h3>' . __('Order Items', 'hid-simple-commerce') . '</h3>
        <table>
            <thead>
                <tr>
                    <th>' . __('Product', 'hid-simple-commerce') . '</th>
                    <th>' . __('Quantity', 'hid-simple-commerce') . '</th>
                    <th>' . __('Price', 'hid-simple-commerce') . '</th>
                    <th>' . __('Subtotal', 'hid-simple-commerce') . '</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($order_items as $item) {
            $product_name = esc_html($item->product_name);
            if ($item->variant_name) {
                $product_name .= ' - ' . esc_html($item->variant_name);
            }
            
            $content .= '
                <tr>
                    <td>' . $product_name . '</td>
                    <td>' . esc_html($item->quantity) . '</td>
                    <td>' . HID_Commerce_Checkout::format_currency($item->price) . '</td>
                    <td>' . HID_Commerce_Checkout::format_currency($item->subtotal) . '</td>
                </tr>';
        }
        
        $content .= '
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>' . __('Subtotal:', 'hid-simple-commerce') . '</strong></td>
                    <td><strong>' . HID_Commerce_Checkout::format_currency($order->subtotal) . '</strong></td>
                </tr>';
        
        if ($order->tax_amount > 0) {
            $tax_label = get_option('hid_commerce_tax_label', 'VAT');
            $content .= '
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>' . esc_html($tax_label) . ':</strong></td>
                    <td><strong>' . HID_Commerce_Checkout::format_currency($order->tax_amount) . '</strong></td>
                </tr>';
        }
        
        $content .= '
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>' . __('Total:', 'hid-simple-commerce') . '</strong></td>
                    <td><strong>' . HID_Commerce_Checkout::format_currency($order->total_amount) . '</strong></td>
                </tr>
            </tfoot>
        </table>
        
        <h3>' . __('Shipping Information', 'hid-simple-commerce') . '</h3>
        <p>' . nl2br(esc_html($order->shipping_address)) . '</p>
        <p><strong>' . __('Location:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->shipping_location) . '</p>
        <p><strong>' . __('Courier:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->shipping_courier) . '</p>
        
        ' . ($order->order_notes ? '<h3>' . __('Order Notes', 'hid-simple-commerce') . '</h3><p>' . nl2br(esc_html($order->order_notes)) . '</p>' : '') . '
        
        <p style="margin-top: 30px;">
            <a href="' . add_query_arg(array('order_number' => $order->order_number, 'email' => $order->customer_email), home_url('/track-order/')) . '" class="button">' . __('Track Your Order', 'hid-simple-commerce') . '</a>
        </p>
        ';

        return self::get_default_template(array('content' => $content));
    }

    /**
     * Get bank transfer instructions template
     */
    public static function get_bank_transfer_template($order, $bank_details) {
        $content = '
        <h2 style="color: ' . get_option('hid_commerce_color_primary', '#470108') . '; font-family: \'Dancing Script\', cursive;">' . __('Payment Instructions', 'hid-simple-commerce') . '</h2>
        <p>' . sprintf(__('Hi %s,', 'hid-simple-commerce'), esc_html($order->customer_name)) . '</p>
        <p>' . __('Thank you for your order. Please transfer the payment to the following bank account:', 'hid-simple-commerce') . '</p>
        
        <div style="background: ' . get_option('hid_commerce_color_background', '#F8F4E9') . '; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p><strong>' . __('Bank Name:', 'hid-simple-commerce') . '</strong> ' . esc_html($bank_details['bank_name']) . '</p>
            <p><strong>' . __('Account Number:', 'hid-simple-commerce') . '</strong> ' . esc_html($bank_details['account_number']) . '</p>
            <p><strong>' . __('Account Name:', 'hid-simple-commerce') . '</strong> ' . esc_html($bank_details['account_name']) . '</p>
            ' . ($bank_details['routing_number'] ? '<p><strong>' . __('Routing Number:', 'hid-simple-commerce') . '</strong> ' . esc_html($bank_details['routing_number']) . '</p>' : '') . '
            <p><strong>' . __('Amount to Transfer:', 'hid-simple-commerce') . '</strong> <span style="font-size: 24px; color: ' . get_option('hid_commerce_color_primary', '#470108') . ';">' . HID_Commerce_Checkout::format_currency($order->total_amount) . '</span></p>
        </div>
        
        <p><strong>' . __('Order Number:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->order_number) . '</p>
        <p>' . __('Please include your order number in the payment reference/description.', 'hid-simple-commerce') . '</p>
        <p>' . __('After making the transfer, please upload your payment proof through your order tracking page.', 'hid-simple-commerce') . '</p>
        
        <p style="margin-top: 30px;">
            <a href="' . add_query_arg(array('order_number' => $order->order_number, 'email' => $order->customer_email), home_url('/track-order/')) . '" class="button">' . __('Upload Payment Proof', 'hid-simple-commerce') . '</a>
        </p>
        ';

        return self::get_default_template(array('content' => $content));
    }

    /**
     * Get admin new order template
     */
    public static function get_admin_new_order_template($order, $order_items) {
        $content = '
        <h2 style="color: ' . get_option('hid_commerce_color_primary', '#470108') . '; font-family: \'Dancing Script\', cursive;">' . __('New Order Received', 'hid-simple-commerce') . '</h2>
        <p>' . __('You have received a new order.', 'hid-simple-commerce') . '</p>
        
        <h3>' . __('Order Details', 'hid-simple-commerce') . '</h3>
        <p><strong>' . __('Order Number:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->order_number) . '</p>
        <p><strong>' . __('Customer:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->customer_name) . '</p>
        <p><strong>' . __('Email:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->customer_email) . '</p>
        <p><strong>' . __('Phone:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->customer_phone) . '</p>
        <p><strong>' . __('Total:', 'hid-simple-commerce') . '</strong> ' . HID_Commerce_Checkout::format_currency($order->total_amount) . '</p>
        <p><strong>' . __('Payment Method:', 'hid-simple-commerce') . '</strong> ' . esc_html($order->payment_method) . '</p>
        
        <p style="margin-top: 30px;">
            <a href="' . admin_url('admin.php?page=hid-commerce-orders&action=view&id=' . $order->id) . '" class="button">' . __('View Order', 'hid-simple-commerce') . '</a>
        </p>
        ';

        return self::get_default_template(array('content' => $content));
    }

    /**
     * Get low stock alert template
     */
    public static function get_low_stock_alert_template($products) {
        $content = '
        <h2 style="color: ' . get_option('hid_commerce_color_primary', '#470108') . '; font-family: \'Dancing Script\', cursive;">' . __('Low Stock Alert', 'hid-simple-commerce') . '</h2>
        <p>' . __('The following products are running low on stock:', 'hid-simple-commerce') . '</p>
        
        <table>
            <thead>
                <tr>
                    <th>' . __('Product', 'hid-simple-commerce') . '</th>
                    <th>' . __('Current Stock', 'hid-simple-commerce') . '</th>
                    <th>' . __('Threshold', 'hid-simple-commerce') . '</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($products as $product) {
            $content .= '
                <tr>
                    <td>' . esc_html($product->name) . '</td>
                    <td>' . esc_html($product->stock_quantity) . '</td>
                    <td>' . esc_html($product->low_stock_threshold) . '</td>
                </tr>';
        }
        
        $content .= '
            </tbody>
        </table>
        
        <p style="margin-top: 30px;">
            <a href="' . admin_url('admin.php?page=hid-commerce-products') . '" class="button">' . __('Manage Products', 'hid-simple-commerce') . '</a>
        </p>
        ';

        return self::get_default_template(array('content' => $content));
    }
}

