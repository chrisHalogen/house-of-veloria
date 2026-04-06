<?php
/**
 * Email Template and Notification Handler
 *
 * Handles all email communications with branded templates
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Email_Handler {

    /**
     * Initialize email hooks
     */
    public static function init() {
        // Hook into order creation
        add_action('hid_commerce_order_created', array(__CLASS__, 'send_order_confirmation'), 10, 1);
        add_action('hid_commerce_order_created', array(__CLASS__, 'send_admin_notification'), 10, 1);
        
        // Hook into newsletter subscription
        add_action('hid_commerce_subscriber_added', array(__CLASS__, 'send_welcome_email'), 10, 1);
    }

    /**
     * Get branded email template
     *
     * @param string $title Email title
     * @param string $content Email content (HTML)
     * @return string Full HTML email
     */
    public static function get_email_template($title, $content) {
        $site_name = get_bloginfo('name');
        $site_url = home_url('/');
        
        $template = '<!DOCTYPE html>
<html>
<head>
    <title>' . esc_html($title) . '</title>
    <style>
        body {
            font-family: "Montserrat", "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #470108;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #F8F4E9;
            margin: 0;
            font-size: 32px;
            font-family: "Dancing Script", cursive;
            font-weight: 400;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #470108;
            font-family: "Dancing Script", cursive;
            font-size: 28px;
            margin-top: 0;
        }
        .content p {
            margin: 15px 0;
            color: #333;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #B4A06A;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            font-size: 16px;
        }
        .button:hover {
            background-color: #9a8c5a;
            color: #ffffff !important;
        }
        a.button {
            color: #ffffff !important;
        }
        .info-box {
            background-color: #F8F4E9;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #B4A06A;
            margin: 20px 0;
        }
        .order-details {
            background-color: #fafafa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details th,
        .order-details td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .order-details th {
            background-color: #F8F4E9;
            color: #470108;
            font-weight: 700;
        }
        .total-row {
            font-weight: 700;
            font-size: 18px;
            color: #470108;
        }
        .footer {
            background-color: #470108;
            padding: 30px 20px;
            text-align: center;
            color: #F8F4E9;
        }
        .footer p {
            margin: 10px 0;
            font-size: 14px;
            color: #F8F4E9;
        }
        .footer a {
            color: #B4A06A;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #B4A06A;
            text-decoration: none;
            font-size: 20px;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 30px 0;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px 15px;
            }
            .header h1 {
                font-size: 24px;
            }
            .content h2 {
                font-size: 22px;
            }
            .button {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>' . esc_html($site_name) . '</h1>
        </div>
        <div class="content">
            ' . $content . '
        </div>
        <div class="footer">
            <p><strong>' . esc_html($site_name) . '</strong></p>
            <p>Timeless Elegance, Modern Grace</p>
            <div class="social-links">
                ' . self::get_social_links() . '
            </div>
            <div class="divider" style="background-color: #B4A06A; height: 1px; margin: 20px 40px;"></div>
            <p style="font-size: 12px; color: #B4A06A;">
                This email was sent to you from ' . esc_html($site_name) . '.<br>
                If you have any questions, please contact us at <a href="mailto:' . esc_attr(get_option('admin_email')) . '">' . esc_html(get_option('admin_email')) . '</a>
            </p>
            <p style="font-size: 11px; color: #9a8c5a;">
                &copy; ' . date('Y') . ' ' . esc_html($site_name) . '. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>';

        return $template;
    }

    /**
     * Get social media links for email footer
     */
    private static function get_social_links() {
        $social_links = '';
        
        $socials = array(
            'instagram' => get_option('hid_commerce_instagram_url'),
            'facebook' => get_option('hid_commerce_facebook_url'),
            'pinterest' => get_option('hid_commerce_pinterest_url'),
            'twitter' => get_option('hid_commerce_twitter_url'),
        );
        
        foreach ($socials as $platform => $url) {
            if (!empty($url)) {
                $social_links .= '<a href="' . esc_url($url) . '" style="margin: 0 8px; color: #B4A06A;">
                    <img src="https://img.icons8.com/ios-filled/20/B4A06A/' . $platform . '.png" alt="' . ucfirst($platform) . '" style="width: 20px; height: 20px;">
                </a>';
            }
        }
        
        return $social_links;
    }

    /**
     * Send order confirmation email to customer
     *
     * @param int $order_id Order ID
     */
    public static function send_order_confirmation($order_id) {
        global $wpdb;
        $db = HID_Commerce_Database::get_instance();
        
        $order = $db->get_order($order_id);
        if (!$order) {
            return;
        }
        
        $order_items = $db->get_order_items($order_id);
        
        // Build order items table
        $items_html = '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background-color: #F8F4E9;">
                    <th style="padding: 12px; text-align: left; color: #470108; border-bottom: 2px solid #B4A06A;">Product</th>
                    <th style="padding: 12px; text-align: center; color: #470108; border-bottom: 2px solid #B4A06A;">Qty</th>
                    <th style="padding: 12px; text-align: right; color: #470108; border-bottom: 2px solid #B4A06A;">Price</th>
                    <th style="padding: 12px; text-align: right; color: #470108; border-bottom: 2px solid #B4A06A;">Subtotal</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($order_items as $item) {
            $items_html .= '<tr>
                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0;">' . esc_html($item->product_name) . '</td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #e0e0e0;">' . esc_html($item->quantity) . '</td>
                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e0e0e0;">' . hid_format_currency($item->price) . '</td>
                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e0e0e0;">' . hid_format_currency($item->subtotal) . '</td>
            </tr>';
        }
        
        $items_html .= '<tr class="total-row">
                <td colspan="3" style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: #470108; border-top: 2px solid #B4A06A;">Total:</td>
                <td style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: #470108; border-top: 2px solid #B4A06A;">' . hid_format_currency($order->total_amount) . '</td>
            </tr>
        </tbody>
        </table>';
        
        // Build email content
        $greeting = !empty($order->customer_name) ? '<p>Hello ' . esc_html($order->customer_name) . ',</p>' : '<p>Hello,</p>';
        
        $content = '<h2>Thank You for Your Order!</h2>
            ' . $greeting . '
            <p>We\'ve received your order and we\'re thrilled to prepare your beautiful jewelry pieces for you!</p>
            
            <div class="info-box">
                <p style="margin: 0;"><strong>Order Number:</strong> ' . esc_html($order->order_number) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Order Date:</strong> ' . date('F j, Y', strtotime($order->created_at)) . '</p>
            </div>
            
            <h3 style="color: #470108; margin-top: 30px;">Order Details</h3>
            ' . $items_html . '
            
            <h3 style="color: #470108; margin-top: 30px;">Shipping Information</h3>
            <div class="info-box">
                <p style="margin: 0;"><strong>Shipping Address:</strong></p>
                <p style="margin: 5px 0 0 0;">' . nl2br(esc_html($order->shipping_address)) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Location:</strong> ' . esc_html($order->shipping_location) . '</p>
                <p style="margin: 5px 0 0 0;"><strong>Preferred Courier:</strong> ' . esc_html($order->shipping_courier) . '</p>
            </div>
            
            <h3 style="color: #470108; margin-top: 30px;">Payment Information</h3>
            <div class="info-box">
                <p style="margin: 0;"><strong>Payment Method:</strong> ' . esc_html(ucfirst($order->payment_method)) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Payment Status:</strong> ' . esc_html(ucfirst($order->payment_status)) . '</p>
            </div>
            
            <p style="margin-top: 30px;">We\'ll notify you once your order ships. You can track your order anytime:</p>
            <a href="' . esc_url(add_query_arg(array('order_number' => $order->order_number, 'email' => $order->customer_email), home_url('/track-order/'))) . '" class="button">Track Your Order</a>
            
            <p style="margin-top: 30px;">If you have any questions about your order, please don\'t hesitate to contact us.</p>';
        
        $subject = 'Order Confirmation - ' . $order->order_number . ' | ' . get_bloginfo('name');
        $html_email = self::get_email_template($subject, $content);
        
        // Set headers for HTML email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        );
        
        // Send email
        wp_mail($order->customer_email, $subject, $html_email, $headers);
    }

    /**
     * Send payment confirmation email to customer
     *
     * @param int $order_id Order ID
     */
    public static function send_payment_confirmation($order_id) {
        global $wpdb;
        $db = HID_Commerce_Database::get_instance();
        
        $order = $db->get_order($order_id);
        if (!$order) {
            return;
        }
        
        $order_items = $db->get_order_items($order_id);
        
        // Build order items table
        $items_html = '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background-color: #F8F4E9;">
                    <th style="padding: 12px; text-align: left; color: #470108; border-bottom: 2px solid #B4A06A;">Product</th>
                    <th style="padding: 12px; text-align: center; color: #470108; border-bottom: 2px solid #B4A06A;">Qty</th>
                    <th style="padding: 12px; text-align: right; color: #470108; border-bottom: 2px solid #B4A06A;">Price</th>
                    <th style="padding: 12px; text-align: right; color: #470108; border-bottom: 2px solid #B4A06A;">Subtotal</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($order_items as $item) {
            $items_html .= '<tr>
                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0;">' . esc_html($item->product_name) . '</td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #e0e0e0;">' . esc_html($item->quantity) . '</td>
                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e0e0e0;">' . hid_format_currency($item->price) . '</td>
                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e0e0e0;">' . hid_format_currency($item->subtotal) . '</td>
            </tr>';
        }
        
        $items_html .= '<tr class="total-row">
                <td colspan="3" style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: #470108; border-top: 2px solid #B4A06A;">Total:</td>
                <td style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: #470108; border-top: 2px solid #B4A06A;">' . hid_format_currency($order->total_amount) . '</td>
            </tr>
        </tbody>
        </table>';
        
        // Build email content
        $greeting = !empty($order->customer_name) ? '<p>Hello ' . esc_html($order->customer_name) . ',</p>' : '<p>Hello,</p>';
        
        $content = '<h2>Payment Confirmed! 🎉</h2>
            ' . $greeting . '
            <p>Great news! We\'ve confirmed your payment for order <strong>' . esc_html($order->order_number) . '</strong>.</p>
            
            <div style="background: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; color: #2e7d32; font-weight: 600;">✓ Payment Received</p>
                <p style="margin: 5px 0 0 0; color: #2e7d32;">Your order is now being processed and will be shipped soon!</p>
            </div>
            
            <h3 style="color: #470108; margin-top: 30px;">Order Summary</h3>
            <div class="info-box">
                <p style="margin: 0;"><strong>Order Number:</strong> ' . esc_html($order->order_number) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Order Date:</strong> ' . date('F j, Y', strtotime($order->created_at)) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Payment Method:</strong> Bank Transfer</p>
                <p style="margin: 10px 0 0 0;"><strong>Payment Status:</strong> <span style="color: #4caf50; font-weight: 600;">PAID ✓</span></p>
            </div>
            
            <h3 style="color: #470108; margin-top: 30px;">Order Details</h3>
            ' . $items_html . '
            
            <h3 style="color: #470108; margin-top: 30px;">Shipping Information</h3>
            <div class="info-box">
                <p style="margin: 0;"><strong>Shipping Address:</strong></p>
                <p style="margin: 5px 0 0 0;">' . nl2br(esc_html($order->shipping_address)) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Location:</strong> ' . esc_html($order->shipping_location) . '</p>
                <p style="margin: 5px 0 0 0;"><strong>Preferred Courier:</strong> ' . esc_html($order->shipping_courier) . '</p>
            </div>
            
            <p style="margin-top: 30px;">Need updates? Track your order anytime using your Order Number: <strong>' . esc_html($order->order_number) . '</strong></p>
            <a href="' . esc_url(add_query_arg(array('order_number' => $order->order_number, 'email' => $order->customer_email), home_url('/track-order/'))) . '" class="button">Track Your Order</a>
            
            <p style="margin-top: 30px;">Thank you for choosing House Of Veloria. We can\'t wait for you to receive your beautiful jewelry!</p>';
        
        $subject = 'Payment Confirmed - Order ' . $order->order_number . ' | ' . get_bloginfo('name');
        $html_email = self::get_email_template($subject, $content);
        
        // Set headers for HTML email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        );
        
        // Send email
        wp_mail($order->customer_email, $subject, $html_email, $headers);
    }

    /**
     * Send new order notification to admin
     *
     * @param int $order_id Order ID
     */
    public static function send_admin_notification($order_id) {
        global $wpdb;
        $db = HID_Commerce_Database::get_instance();
        
        $order = $db->get_order($order_id);
        if (!$order) {
            return;
        }

        $order_items = $db->get_order_items($order_id);
        
        // Build order items table
        $items_html = '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background-color: #F8F4E9;">
                    <th style="padding: 12px; text-align: left; color: #470108; border-bottom: 2px solid #B4A06A;">Product</th>
                    <th style="padding: 12px; text-align: center; color: #470108; border-bottom: 2px solid #B4A06A;">Qty</th>
                    <th style="padding: 12px; text-align: right; color: #470108; border-bottom: 2px solid #B4A06A;">Price</th>
                    <th style="padding: 12px; text-align: right; color: #470108; border-bottom: 2px solid #B4A06A;">Subtotal</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($order_items as $item) {
            $items_html .= '<tr>
                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0;">' . esc_html($item->product_name) . '</td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #e0e0e0;">' . esc_html($item->quantity) . '</td>
                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e0e0e0;">' . hid_format_currency($item->price) . '</td>
                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e0e0e0;">' . hid_format_currency($item->subtotal) . '</td>
            </tr>';
        }
        
        $items_html .= '<tr class="total-row">
                <td colspan="3" style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: #470108; border-top: 2px solid #B4A06A;">Total:</td>
                <td style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: #470108; border-top: 2px solid #B4A06A;">' . hid_format_currency($order->total_amount) . '</td>
            </tr>
        </tbody>
        </table>';
        
        // Build email content
        $content = '<h2>New Order Received!</h2>
            <p>You have received a new order on ' . get_bloginfo('name') . '.</p>
            
            <div class="info-box">
                <p style="margin: 0;"><strong>Order Number:</strong> ' . esc_html($order->order_number) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Order Date:</strong> ' . date('F j, Y \a\t g:i A', strtotime($order->created_at)) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Order Status:</strong> ' . esc_html(ucfirst($order->order_status)) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Payment Status:</strong> ' . esc_html(ucfirst($order->payment_status)) . '</p>
            </div>
            
            <h3 style="color: #470108; margin-top: 30px;">Customer Information</h3>
            <div class="info-box">
                <p style="margin: 0;"><strong>Name:</strong> ' . esc_html($order->customer_name) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Email:</strong> ' . esc_html($order->customer_email) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Phone:</strong> ' . esc_html($order->customer_phone) . '</p>
            </div>
            
            <h3 style="color: #470108; margin-top: 30px;">Order Details</h3>
            ' . $items_html . '
            
            <h3 style="color: #470108; margin-top: 30px;">Shipping Information</h3>
            <div class="info-box">
                <p style="margin: 0;"><strong>Address:</strong></p>
                <p style="margin: 5px 0 0 0;">' . nl2br(esc_html($order->shipping_address)) . '</p>
                <p style="margin: 10px 0 0 0;"><strong>Location:</strong> ' . esc_html($order->shipping_location) . '</p>
                <p style="margin: 5px 0 0 0;"><strong>Preferred Courier:</strong> ' . esc_html($order->shipping_courier) . '</p>
            </div>';
        
        if (!empty($order->order_notes)) {
            $content .= '<h3 style="color: #470108; margin-top: 30px;">Order Notes</h3>
            <div class="info-box">
                <p style="margin: 0;">' . nl2br(esc_html($order->order_notes)) . '</p>
            </div>';
        }
        
        $content .= '<p style="margin-top: 30px;">View and manage this order in your admin panel:</p>
            <a href="' . esc_url(admin_url('admin.php?page=hid-commerce-orders&action=view&id=' . $order_id)) . '" class="button">View Order in Admin</a>';
        
        $subject = 'New Order #' . $order->order_number . ' | ' . get_bloginfo('name');
        $html_email = self::get_email_template($subject, $content);
        
        // Set headers for HTML email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        );
        
        // Send to admin email
        $admin_email = get_option('hid_commerce_admin_email', get_option('admin_email'));
        wp_mail($admin_email, $subject, $html_email, $headers);
    }

    /**
     * Send welcome email to new subscriber
     *
     * @param string $email Subscriber email
     * @param string|null $name Subscriber name (optional)
     */
    public static function send_welcome_email($email, $name = null) {
        $greeting = !empty($name) ? '<p>Hello ' . esc_html($name) . ',</p>' : '<p>Hello,</p>';
        
        $content = '<h2>Welcome to ' . get_bloginfo('name') . '!</h2>
            ' . $greeting . '
            <p>Thank you for subscribing to our newsletter! We\'re thrilled to have you join our community of jewelry enthusiasts.</p>
            
            <p>As a subscriber, you\'ll be the first to know about:</p>
            <ul style="line-height: 2;">
                <li>New jewelry collections and exclusive pieces</li>
                <li>Special offers and promotions</li>
                <li>Jewelry care tips and styling advice</li>
                <li>Behind-the-scenes stories of our craftsmanship</li>
            </ul>
            
            <p style="margin-top: 30px;">Explore our exquisite collection of handcrafted jewelry:</p>
            <a href="' . esc_url(home_url('/shop/')) . '" class="button">Shop Now</a>
            
            <p style="margin-top: 30px;">We can\'t wait to share our passion for timeless elegance with you!</p>';
        
        $subject = 'Welcome to ' . get_bloginfo('name') . '!';
        $html_email = self::get_email_template($subject, $content);
        
        // Set headers for HTML email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        );
        
        // Send email
        wp_mail($email, $subject, $html_email, $headers);
    }

    /**
     * Send bulk email to customers/subscribers
     *
     * @param array $recipients Array of recipient data (email, name)
     * @param string $subject Email subject
     * @param string $message Email message content
     * @return array Result with success/failure counts
     */
    public static function send_bulk_email($recipients, $subject, $message) {
        $success_count = 0;
        $failure_count = 0;
        
        foreach ($recipients as $recipient) {
            $email = $recipient['email'];
            $name = isset($recipient['name']) ? $recipient['name'] : null;
            
            // Personalize greeting
            if (!empty($name)) {
                $personalized_message = '<p>Hello ' . esc_html($name) . ',</p>' . $message;
            } else {
                $personalized_message = '<p>Hello,</p>' . $message;
            }
            
            $html_email = self::get_email_template($subject, $personalized_message);
            
            // Set headers for HTML email
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
            );
            
            // Send email
            if (wp_mail($email, $subject, $html_email, $headers)) {
                $success_count++;
            } else {
                $failure_count++;
            }
            
            // Small delay to prevent overwhelming the mail server
            usleep(100000); // 0.1 second delay
        }
        
        return array(
            'success' => $success_count,
            'failure' => $failure_count,
            'total' => count($recipients)
        );
    }
}

// Initialize email handler
HID_Commerce_Email_Handler::init();
