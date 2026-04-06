<?php
/**
 * Checkout processing class
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Checkout {

    /**
     * Initialize checkout
     */
    public static function init() {
        add_action('wp_ajax_hid_process_checkout', array(__CLASS__, 'ajax_process_checkout'));
        add_action('wp_ajax_nopriv_hid_process_checkout', array(__CLASS__, 'ajax_process_checkout'));
        
        add_action('wp_ajax_hid_upload_payment_proof', array(__CLASS__, 'ajax_upload_payment_proof'));
        add_action('wp_ajax_nopriv_hid_upload_payment_proof', array(__CLASS__, 'ajax_upload_payment_proof'));
        
        add_action('wp_ajax_hid_get_customer_details', array(__CLASS__, 'ajax_get_customer_details'));
        add_action('wp_ajax_nopriv_hid_get_customer_details', array(__CLASS__, 'ajax_get_customer_details'));
    }

    /**
     * Process checkout
     */
    public static function process_checkout($data) {
        $db = HID_Commerce_Database::get_instance();

        // Validate customer information
        if (empty($data['customer_name']) || empty($data['customer_email']) || empty($data['customer_phone'])) {
            return new WP_Error('invalid_data', __('Please fill in all required fields', 'hid-simple-commerce'));
        }

        // Validate email
        if (!is_email($data['customer_email'])) {
            return new WP_Error('invalid_email', __('Please enter a valid email address', 'hid-simple-commerce'));
        }

        // Get cart items
        $cart_items = HID_Commerce_Session::get_cart_items();
        
        if (empty($cart_items)) {
            return new WP_Error('empty_cart', __('Your cart is empty', 'hid-simple-commerce'));
        }

        // Validate cart items and check stock
        $validation_errors = self::validate_cart_items($cart_items);
        if (!empty($validation_errors)) {
            return new WP_Error('validation_failed', implode('<br>', $validation_errors));
        }

        // Calculate totals
        $totals = HID_Commerce_Session::calculate_totals();

        // Create or update customer
        $customer_data = array(
            'name' => sanitize_text_field($data['customer_name']),
            'email' => sanitize_email($data['customer_email']),
            'phone' => sanitize_text_field($data['customer_phone']),
            'shipping_address' => sanitize_textarea_field($data['shipping_address']),
        );

        $customer_id = $db->create_customer($customer_data);

        // Handle payment proof upload if bank transfer
        $payment_proof_url = null;
        if ($data['payment_method'] == 'bank_transfer' && !empty($data['payment_proof_file'])) {
            $payment_proof_url = self::handle_payment_proof_upload($data['payment_proof_file']);
        }

        // Create order
        $order_data = array(
            'customer_id' => $customer_id,
            'customer_name' => $customer_data['name'],
            'customer_email' => $customer_data['email'],
            'customer_phone' => $customer_data['phone'],
            'shipping_address' => $customer_data['shipping_address'],
            'shipping_location' => sanitize_text_field($data['shipping_location']),
            'shipping_courier' => sanitize_text_field($data['shipping_courier']),
            'payment_method' => sanitize_text_field($data['payment_method']),
            'payment_status' => 'pending',
            'payment_proof_url' => $payment_proof_url,
            'order_status' => 'pending',
            'order_notes' => sanitize_textarea_field($data['order_notes']),
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'total_amount' => $totals['total'],
            'currency' => $totals['currency'],
        );

        $order_id = $db->create_order($order_data);

        if (!$order_id) {
            return new WP_Error('order_failed', __('Failed to create order', 'hid-simple-commerce'));
        }

        // Add order items
        foreach ($cart_items as $item) {
            $order_item_data = array(
                'order_id' => $order_id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'product_name' => $item['product_name'],
                'variant_name' => $item['variant_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            );

            $db->add_order_item($order_item_data);

            // Reduce stock
            HID_Commerce_Inventory::reduce_stock($item['product_id'], $item['variant_id'], $item['quantity']);
        }

        // Get order for return
        $order = $db->get_order($order_id);

        // Process payment
        if ($data['payment_method'] != 'bank_transfer') {
            $payment_result = HID_Commerce_Payments::process_payment($order, $data['payment_method']);
            
            if (is_wp_error($payment_result)) {
                return $payment_result;
            }
        }

        // Create or update customer record
        self::save_customer($data);
        
        // Trigger action hook for email notifications
        // Trigger action hook for email notifications
        // Note: For Paystack/Stripe, we wait for payment confirmation to trigger emails with correct status
        if ($data['payment_method'] == 'bank_transfer') {
             do_action('hid_commerce_order_created', $order_id);
        }

        // Clear cart
        HID_Commerce_Session::clear_cart();

        // Save customer details to session for future orders
        self::save_customer_to_session($customer_data);

        $response = array(
            'success' => true,
            'order_id' => $order_id,
            'order_number' => $order->order_number,
            'message' => __('Order placed successfully', 'hid-simple-commerce')
        );

        if (isset($payment_result) && is_array($payment_result)) {
            $response = array_merge($response, $payment_result);
        }

        return $response;
    }

    /**
     * Validate cart items
     */
    private static function validate_cart_items($cart_items) {
        $errors = array();
        $db = HID_Commerce_Database::get_instance();

        foreach ($cart_items as $item) {
            // Check if product exists
            $product = $db->get_product($item['product_id']);
            
            if (!$product) {
                $errors[] = sprintf(__('Product %s no longer exists', 'hid-simple-commerce'), $item['product_name']);
                continue;
            }

            // Check stock availability
            if ($item['variant_id']) {
                // Check variant stock
                $variant = $db->get_variant($item['variant_id']);
                
                if (!$variant) {
                    $errors[] = sprintf(__('Product variant %s no longer exists', 'hid-simple-commerce'), $item['product_name']);
                    continue;
                }

                if ($variant->stock_quantity < $item['quantity']) {
                    $errors[] = sprintf(
                        __('%s - %s: Only %d available in stock', 'hid-simple-commerce'),
                        $item['product_name'],
                        $item['variant_name'],
                        $variant->stock_quantity
                    );
                }
            } else {
                // Check product stock
                if ($product->stock_quantity < $item['quantity']) {
                    $errors[] = sprintf(
                        __('%s: Only %d available in stock', 'hid-simple-commerce'),
                        $item['product_name'],
                        $product->stock_quantity
                    );
                }
            }

            // Ensure variant products have variant selected
            if ($product->has_variants && !$item['variant_id']) {
                $errors[] = sprintf(__('%s requires option selection', 'hid-simple-commerce'), $item['product_name']);
            }
        }

        return $errors;
    }

    /**
     * Handle payment proof upload
     */
    private static function handle_payment_proof_upload($file) {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // Validate file type
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf');
        
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }

        // Set upload directory
        $upload_overrides = array(
            'test_form' => false,
            'upload_path' => wp_upload_dir()['basedir'] . '/payment-proofs/'
        );

        $movefile = wp_handle_upload($file, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            return $movefile['url'];
        }

        return false;
    }

    /**
     * Format currency
     */
    public static function format_currency($amount) {
        $symbol = get_option('hid_commerce_currency_symbol', '$');
        $position = get_option('hid_commerce_currency_position', 'before');
        $decimal_places = get_option('hid_commerce_decimal_places', 2);
        $thousands_sep = get_option('hid_commerce_thousands_separator', ',');
        $decimal_sep = get_option('hid_commerce_decimal_separator', '.');

        $formatted = number_format($amount, $decimal_places, $decimal_sep, $thousands_sep);

        if ($position == 'before') {
            return $symbol . $formatted;
        } else {
            return $formatted . $symbol;
        }
    }

    // ============ AJAX HANDLERS ============

    /**
     * AJAX: Process checkout
     */
    public static function ajax_process_checkout() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        $data = array(
            'customer_name' => isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '',
            'customer_email' => isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '',
            'customer_phone' => isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : '',
            'shipping_address' => isset($_POST['shipping_address']) ? sanitize_textarea_field($_POST['shipping_address']) : '',
            'shipping_location' => isset($_POST['shipping_location']) ? sanitize_text_field($_POST['shipping_location']) : '',
            'shipping_courier' => isset($_POST['shipping_courier']) ? sanitize_text_field($_POST['shipping_courier']) : '',
            'payment_method' => isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : '',
            'order_notes' => isset($_POST['order_notes']) ? sanitize_textarea_field($_POST['order_notes']) : '',
            'payment_proof_file' => isset($_FILES['payment_proof']) ? $_FILES['payment_proof'] : null,
        );

        if (isset($_POST['delivery_method']) && sanitize_text_field($_POST['delivery_method']) === 'pickup') {
            $admin_address = get_option('hid_commerce_pickup_address', '');
            $data['shipping_address'] = "Physical Pickup at:\n" . $admin_address;
            $data['shipping_location'] = "Pickup";
            $data['shipping_courier'] = "None";
        }

        $result = self::process_checkout($data);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ));
        }

        wp_send_json_success($result);
    }

    /**
     * AJAX: Upload payment proof
     */
    public static function ajax_upload_payment_proof() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        if (!isset($_FILES['payment_proof'])) {
            wp_send_json_error(array('message' => __('No file uploaded', 'hid-simple-commerce')));
        }

        $url = self::handle_payment_proof_upload($_FILES['payment_proof']);

        if ($url) {
            wp_send_json_success(array('url' => $url));
        } else {
            wp_send_json_error(array('message' => __('Failed to upload file', 'hid-simple-commerce')));
        }
    }

    /**
     * AJAX: Get customer details from session
     */
    public static function ajax_get_customer_details() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        $customer_data = self::get_customer_from_session();
        wp_send_json_success($customer_data);
    }

    /**
     * Save or update customer record
     *
     * @param array $data Order data containing customer information
     */
    private static function save_customer($data) {
        global $wpdb;
        
        $customers_table = $wpdb->prefix . 'hid_customers';
        $email = sanitize_email($data['customer_email']);
        
        // Check if customer/subscriber exists
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $customers_table WHERE email = %s",
            $email
        ));
        
        if ($existing) {
            // Always upgrade to 'customer' type when making a purchase
            $update_data = array(
                'customer_type' => 'customer',
            );
            
            // Update name if it's empty or provided value is different
            if (empty($existing->name) && !empty($data['customer_name'])) {
                $update_data['name'] = sanitize_text_field($data['customer_name']);
            }
            
            // Update phone if it's empty or provided value is different
            if (empty($existing->phone) && !empty($data['customer_phone'])) {
                $update_data['phone'] = sanitize_text_field($data['customer_phone']);
            }
            
            // Always update shipping address if provided
            if (!empty($data['shipping_address'])) {
                $update_data['shipping_address'] = sanitize_textarea_field($data['shipping_address']);
            }
            
            // Build format array dynamically based on update_data
            $format = array_fill(0, count($update_data), '%s');
            
            $wpdb->update(
                $customers_table,
                $update_data,
                array('email' => $email),
                $format,
                array('%s')
            );
        } else {
            // Insert new customer
            $wpdb->insert(
                $customers_table,
                array(
                    'email' => $email,
                    'name' => sanitize_text_field($data['customer_name']),
                    'phone' => sanitize_text_field($data['customer_phone']),
                    'shipping_address' => sanitize_textarea_field($data['shipping_address']),
                    'customer_type' => 'customer',
                    'date_subscribed' => current_time('mysql'),
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s')
            );
        }
    }

    /**
     * Save customer details to session
     *
     * @param array $customer_data Customer data (name, email, phone)
     */
    private static function save_customer_to_session($customer_data) {
        if (!session_id()) {
            session_start();
        }

        $_SESSION['hid_customer_details'] = array(
            'name' => $customer_data['name'],
            'email' => $customer_data['email'],
            'phone' => $customer_data['phone'],
        );
    }

    /**
     * Get customer details from session
     *
     * @return array Customer details or empty array
     */
    public static function get_customer_from_session() {
        if (!session_id()) {
            session_start();
        }

        return isset($_SESSION['hid_customer_details']) ? $_SESSION['hid_customer_details'] : array(
            'name' => '',
            'email' => '',
            'phone' => '',
        );
    }
}

// Initialize
add_action('init', array('HID_Commerce_Checkout', 'init'));

