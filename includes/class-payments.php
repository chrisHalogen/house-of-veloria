<?php
/**
 * Payment gateway handler class
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Payments {

    /**
     * Process payment
     */
    public static function process_payment($order, $payment_method) {
        switch ($payment_method) {
            case 'paystack':
                return self::process_paystack($order);
            
            case 'stripe':
                return self::process_stripe($order);
            
            case 'paypal':
                return self::process_paypal($order);
            
            case 'bank_transfer':
                return self::process_bank_transfer($order);
            
            default:
                return new WP_Error('invalid_payment_method', __('Invalid payment method', 'hid-simple-commerce'));
        }
    }

    /**
     * Get enabled payment methods
     */
    public static function get_enabled_methods() {
        $methods = array();

        if (get_option('hid_commerce_bank_transfer_enabled', '1') == '1') {
            $methods['bank_transfer'] = array(
                'name' => __('Bank Transfer', 'hid-simple-commerce'),
                'description' => __('Transfer payment directly to our bank account', 'hid-simple-commerce'),
            );
        }

        if (get_option('hid_commerce_paystack_enabled', '0') == '1') {
            $methods['paystack'] = array(
                'name' => __('Paystack', 'hid-simple-commerce'),
                'description' => __('Pay with your card via Paystack', 'hid-simple-commerce'),
            );
        }

        if (get_option('hid_commerce_stripe_enabled', '0') == '1') {
            $methods['stripe'] = array(
                'name' => __('Stripe', 'hid-simple-commerce'),
                'description' => __('Pay with your card via Stripe', 'hid-simple-commerce'),
            );
        }

        if (get_option('hid_commerce_paypal_enabled', '0') == '1') {
            $methods['paypal'] = array(
                'name' => __('PayPal', 'hid-simple-commerce'),
                'description' => __('Pay with PayPal', 'hid-simple-commerce'),
            );
        }

        return $methods;
    }

    /**
     * Get bank account details
     */
    public static function get_bank_details() {
        return array(
            'bank_name' => get_option('hid_commerce_bank_name', ''),
            'account_number' => get_option('hid_commerce_bank_account_number', ''),
            'account_name' => get_option('hid_commerce_bank_account_name', ''),
            'routing_number' => get_option('hid_commerce_bank_routing_number', ''),
        );
    }

    /**
     * Process bank transfer
     */
    private static function process_bank_transfer($order) {
        // Bank transfer is manual, just return success
        // Order status remains "pending" until admin approves payment proof
        return array(
            'success' => true,
            'redirect' => false,
            'message' => __('Please transfer the payment to the provided bank account and upload proof of payment', 'hid-simple-commerce')
        );
    }

    /**
     * Process Paystack payment
     */
    private static function process_paystack($order) {
        $public_key = get_option('hid_commerce_paystack_public_key', '');
        
        if (empty($public_key)) {
            return new WP_Error('paystack_not_configured', __('Paystack is not configured', 'hid-simple-commerce'));
        }

        // Initialize Paystack transaction
        $url = 'https://api.paystack.co/transaction/initialize';
        
        $fields = array(
            'email' => $order->customer_email,
            'amount' => $order->total_amount * 100, // Convert to kobo/cents
            'currency' => $order->currency,
            'reference' => $order->order_number,
            'callback_url' => add_query_arg(
                array(
                    'hid_commerce_payment' => 'paystack',
                    'order_id' => $order->id,
                ),
                home_url('/')
            ),
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . get_option('hid_commerce_paystack_secret_key', ''),
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($fields),
            'timeout' => 60,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['status']) && $body['status'] == true) {
            return array(
                'success' => true,
                'redirect' => $body['data']['authorization_url'],
                'access_code' => $body['data']['access_code'],
                'reference' => $body['data']['reference'],
                'public_key' => $public_key,
                'customer_email' => $order->customer_email,
                'amount' => $order->total_amount,
            );
        }

        return new WP_Error('paystack_error', $body['message'] ?? __('Payment initialization failed', 'hid-simple-commerce'));
    }

    /**
     * Handle Paystack Webhook
     */
    public static function handle_paystack_webhook() {
        if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !isset($_GET['hid_commerce_webhook']) || $_GET['hid_commerce_webhook'] !== 'paystack') {
            return;
        }

        $input = @file_get_contents("php://input");
        $event = json_decode($input);

        $secret_key = get_option('hid_commerce_paystack_secret_key', '');

        if (!$event) {
            exit();
        }

        // Verify signature
        $header_signature = isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE']) ? $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] : '';
        if ($header_signature !== hash_hmac('sha512', $input, $secret_key)) {
            exit();
        }

        if ('charge.success' === $event->event) {
            $reference = $event->data->reference;
            
            // Find order by reference (order_number)
            $db = HID_Commerce_Database::get_instance();
            $order = $db->get_order_by_number($reference);

            if ($order && $order->payment_status !== 'paid') {
                $db->update_order($order->id, array(
                    'payment_status' => 'paid',
                    'order_status' => 'processing',
                ));
                
                // Send email notifications
                do_action('hid_commerce_order_created', $order->id);
            }
        }

        http_response_code(200);
        exit();
    }

    /**
     * AJAX: Update order status after Paystack popup payment
     */
    public static function ajax_update_paystack_order() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        $reference = isset($_POST['reference']) ? sanitize_text_field($_POST['reference']) : '';
        $order_number = isset($_POST['order_number']) ? sanitize_text_field($_POST['order_number']) : '';

        if (empty($reference) || empty($order_number)) {
            wp_send_json_error(array('message' => 'Missing reference or order number'));
        }

        // Verify with Paystack API first
        $url = 'https://api.paystack.co/transaction/verify/' . $reference;
        
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . get_option('hid_commerce_paystack_secret_key', ''),
            ),
        ));

        if (is_wp_error($response)) {
             wp_send_json_error(array('message' => 'Verification failed'));
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['data']['status']) && $body['data']['status'] == 'success') {
            $db = HID_Commerce_Database::get_instance();
            $order = $db->get_order_by_number($order_number);

            if ($order) {
                $db->update_order($order->id, array(
                    'payment_status' => 'paid',
                    'order_status' => 'processing',
                ));
                
                // Send email notifications now that payment is confirmed
                do_action('hid_commerce_order_created', $order->id);
                
                wp_send_json_success();
            } else {
                wp_send_json_error(array('message' => 'Order not found'));
            }
        } else {
             wp_send_json_error(array('message' => 'Payment verification returned failure'));
        }
    }

    /**
     * Process Stripe payment
     */
    private static function process_stripe($order) {
        $secret_key = get_option('hid_commerce_stripe_secret_key', '');
        
        if (empty($secret_key)) {
            return new WP_Error('stripe_not_configured', __('Stripe is not configured', 'hid-simple-commerce'));
        }

        // Create Stripe payment intent
        $url = 'https://api.stripe.com/v1/payment_intents';
        
        $fields = array(
            'amount' => $order->total_amount * 100, // Convert to cents
            'currency' => strtolower($order->currency),
            'description' => sprintf(__('Order %s', 'hid-simple-commerce'), $order->order_number),
            'metadata' => array(
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ),
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
            ),
            'body' => $fields,
            'timeout' => 60,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['client_secret'])) {
            return array(
                'success' => true,
                'client_secret' => $body['client_secret'],
                'publishable_key' => get_option('hid_commerce_stripe_publishable_key', ''),
            );
        }

        return new WP_Error('stripe_error', $body['error']['message'] ?? __('Payment initialization failed', 'hid-simple-commerce'));
    }

    /**
     * Process PayPal payment
     */
    private static function process_paypal($order) {
        $client_id = get_option('hid_commerce_paypal_client_id', '');
        $secret = get_option('hid_commerce_paypal_secret', '');
        
        if (empty($client_id) || empty($secret)) {
            return new WP_Error('paypal_not_configured', __('PayPal is not configured', 'hid-simple-commerce'));
        }

        // Get PayPal access token
        $token_url = 'https://api-m.paypal.com/v1/oauth2/token';
        
        $token_response = wp_remote_post($token_url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $secret),
            ),
            'body' => array('grant_type' => 'client_credentials'),
            'timeout' => 60,
        ));

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $token_body = json_decode(wp_remote_retrieve_body($token_response), true);
        $access_token = $token_body['access_token'] ?? '';

        if (empty($access_token)) {
            return new WP_Error('paypal_error', __('Failed to authenticate with PayPal', 'hid-simple-commerce'));
        }

        // Create PayPal order
        $order_url = 'https://api-m.paypal.com/v2/checkout/orders';
        
        $order_data = array(
            'intent' => 'CAPTURE',
            'purchase_units' => array(
                array(
                    'reference_id' => $order->order_number,
                    'amount' => array(
                        'currency_code' => $order->currency,
                        'value' => number_format($order->total_amount, 2, '.', ''),
                    ),
                ),
            ),
            'application_context' => array(
                'return_url' => add_query_arg(
                    array(
                        'hid_commerce_payment' => 'paypal',
                        'order_id' => $order->id,
                    ),
                    home_url('/')
                ),
                'cancel_url' => add_query_arg(
                    array(
                        'hid_commerce_payment' => 'paypal_cancel',
                        'order_id' => $order->id,
                    ),
                    home_url('/')
                ),
            ),
        );

        $order_response = wp_remote_post($order_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($order_data),
            'timeout' => 60,
        ));

        if (is_wp_error($order_response)) {
            return $order_response;
        }

        $order_body = json_decode(wp_remote_retrieve_body($order_response), true);

        if (isset($order_body['links'])) {
            foreach ($order_body['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    return array(
                        'success' => true,
                        'redirect' => $link['href'],
                    );
                }
            }
        }

        return new WP_Error('paypal_error', __('Failed to create PayPal order', 'hid-simple-commerce'));
    }

    /**
     * Handle payment callback
     */
    public static function handle_payment_callback() {
        if (!isset($_GET['hid_commerce_payment'])) {
            return;
        }

        $payment_method = sanitize_text_field($_GET['hid_commerce_payment']);
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

        if (!$order_id) {
            return;
        }

        $db = HID_Commerce_Database::get_instance();
        $order = $db->get_order($order_id);

        if (!$order) {
            return;
        }

        switch ($payment_method) {
            case 'paystack':
                self::verify_paystack_payment($order);
                break;
            
            case 'paypal':
                self::verify_paypal_payment($order);
                break;
        }
    }

    /**
     * Verify Paystack payment
     */
    private static function verify_paystack_payment($order) {
        $reference = isset($_GET['reference']) ? sanitize_text_field($_GET['reference']) : '';
        
        if (empty($reference)) {
            return;
        }

        $url = 'https://api.paystack.co/transaction/verify/' . $reference;
        
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . get_option('hid_commerce_paystack_secret_key', ''),
            ),
        ));

        if (is_wp_error($response)) {
            return;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['data']['status']) && $body['data']['status'] == 'success') {
            $db = HID_Commerce_Database::get_instance();
            $db->update_order($order->id, array(
                'payment_status' => 'paid',
                'order_status' => 'processing',
            ));

            wp_redirect(add_query_arg('order_number', $order->order_number, home_url('/order-confirmation/')));
            exit;
        }
    }

    /**
     * Verify PayPal payment
     */
    private static function verify_paypal_payment($order) {
        $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
        
        if (empty($token)) {
            return;
        }

        // Capture PayPal order
        $client_id = get_option('hid_commerce_paypal_client_id', '');
        $secret = get_option('hid_commerce_paypal_secret', '');

        // Get access token
        $token_url = 'https://api-m.paypal.com/v1/oauth2/token';
        
        $token_response = wp_remote_post($token_url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $secret),
            ),
            'body' => array('grant_type' => 'client_credentials'),
        ));

        if (is_wp_error($token_response)) {
            return;
        }

        $token_body = json_decode(wp_remote_retrieve_body($token_response), true);
        $access_token = $token_body['access_token'] ?? '';

        // Capture order
        $capture_url = 'https://api-m.paypal.com/v2/checkout/orders/' . $token . '/capture';
        
        $capture_response = wp_remote_post($capture_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ),
        ));

        if (is_wp_error($capture_response)) {
            return;
        }

        $capture_body = json_decode(wp_remote_retrieve_body($capture_response), true);

        if (isset($capture_body['status']) && $capture_body['status'] == 'COMPLETED') {
            $db = HID_Commerce_Database::get_instance();
            $db->update_order($order->id, array(
                'payment_status' => 'paid',
                'order_status' => 'processing',
            ));

            wp_redirect(add_query_arg('order_number', $order->order_number, home_url('/order-confirmation/')));
            exit;
        }
    }
}

// Handle payment callbacks
add_action('template_redirect', array('HID_Commerce_Payments', 'handle_payment_callback'));
// Handle webhooks
add_action('init', array('HID_Commerce_Payments', 'handle_paystack_webhook'));
// AJAX handler for updating order status after popup payment
add_action('wp_ajax_hid_update_paystack_order', array('HID_Commerce_Payments', 'ajax_update_paystack_order'));
add_action('wp_ajax_nopriv_hid_update_paystack_order', array('HID_Commerce_Payments', 'ajax_update_paystack_order'));

