<?php
/**
 * Admin dashboard class
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Admin {

    /**
     * Initialize admin
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        
        // AJAX handlers
        add_action('wp_ajax_hid_save_product', array(__CLASS__, 'ajax_save_product'));
        add_action('wp_ajax_hid_delete_product', array(__CLASS__, 'ajax_delete_product'));
        add_action('wp_ajax_hid_save_category', array(__CLASS__, 'ajax_save_category'));
        add_action('wp_ajax_hid_get_category', array(__CLASS__, 'ajax_get_category'));
        add_action('wp_ajax_hid_delete_category', array(__CLASS__, 'ajax_delete_category'));
        add_action('wp_ajax_hid_update_order_status', array(__CLASS__, 'ajax_update_order_status'));
        add_action('wp_ajax_hid_confirm_payment', array(__CLASS__, 'ajax_confirm_payment'));
        add_action('wp_ajax_hid_export_products', array(__CLASS__, 'ajax_export_products'));
        add_action('wp_ajax_hid_export_orders', array(__CLASS__, 'ajax_export_orders'));
        add_action('wp_ajax_hid_get_variant_data', array(__CLASS__, 'ajax_get_variant_data'));
        add_action('wp_ajax_hid_newsletter_subscribe', array(__CLASS__, 'ajax_newsletter_subscribe'));
        add_action('wp_ajax_nopriv_hid_newsletter_subscribe', array(__CLASS__, 'ajax_newsletter_subscribe'));
    }

    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('HID Commerce', 'hid-simple-commerce'),
            __('HID Commerce', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce',
            array(__CLASS__, 'dashboard_page'),
            'dashicons-cart',
            25
        );

        // Dashboard
        add_submenu_page(
            'hid-commerce',
            __('Dashboard', 'hid-simple-commerce'),
            __('Dashboard', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce',
            array(__CLASS__, 'dashboard_page')
        );

        // Products
        add_submenu_page(
            'hid-commerce',
            __('Products', 'hid-simple-commerce'),
            __('Products', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce-products',
            array(__CLASS__, 'products_page')
        );

        // Categories
        add_submenu_page(
            'hid-commerce',
            __('Categories', 'hid-simple-commerce'),
            __('Categories', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce-categories',
            array(__CLASS__, 'categories_page')
        );

        // Orders
        add_submenu_page(
            'hid-commerce',
            __('Orders', 'hid-simple-commerce'),
            __('Orders', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce-orders',
            array(__CLASS__, 'orders_page')
        );

        // Customers
        add_submenu_page(
            'hid-commerce',
            __('Customers', 'hid-simple-commerce'),
            __('Customers', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce-customers',
            array(__CLASS__, 'customers_page')
        );

        // Communications
        add_submenu_page(
            'hid-commerce',
            __('Communications', 'hid-simple-commerce'),
            __('Communications', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce-communications',
            array(__CLASS__, 'communications_page')
        );

        // Settings
        add_submenu_page(
            'hid-commerce',
            __('Settings', 'hid-simple-commerce'),
            __('Settings', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce-settings',
            array(__CLASS__, 'settings_page')
        );

        // Documentation
        add_submenu_page(
            'hid-commerce',
            __('Documentation', 'hid-simple-commerce'),
            __('Documentation', 'hid-simple-commerce'),
            'manage_options',
            'hid-commerce-documentation',
            array(__CLASS__, 'documentation_page')
        );
    }

    /**
     * Register settings
     */
    public static function register_settings() {
        // General settings
        register_setting('hid_commerce_general', 'hid_commerce_admin_email');
        register_setting('hid_commerce_general', 'hid_commerce_use_text_logo');
        register_setting('hid_commerce_general', 'hid_commerce_site_logo');
        register_setting('hid_commerce_general', 'hid_commerce_color_primary');
        register_setting('hid_commerce_general', 'hid_commerce_color_secondary');
        register_setting('hid_commerce_general', 'hid_commerce_color_accent');
        register_setting('hid_commerce_general', 'hid_commerce_color_background');
        register_setting('hid_commerce_general', 'hid_commerce_color_white');
        register_setting('hid_commerce_general', 'hid_commerce_font_header');
        register_setting('hid_commerce_general', 'hid_commerce_font_body');
        register_setting('hid_commerce_general', 'hid_commerce_font_size_base');
        register_setting('hid_commerce_general', 'hid_commerce_enable_pickup');
        register_setting('hid_commerce_general', 'hid_commerce_pickup_address');

        // Currency settings
        register_setting('hid_commerce_currency', 'hid_commerce_currency_code');
        register_setting('hid_commerce_currency', 'hid_commerce_currency_symbol');
        register_setting('hid_commerce_currency', 'hid_commerce_currency_position');
        register_setting('hid_commerce_currency', 'hid_commerce_decimal_places');
        register_setting('hid_commerce_currency', 'hid_commerce_thousands_separator');
        register_setting('hid_commerce_currency', 'hid_commerce_decimal_separator');

        // Tax settings
        register_setting('hid_commerce_tax', 'hid_commerce_tax_enabled');
        register_setting('hid_commerce_tax', 'hid_commerce_tax_rate');
        register_setting('hid_commerce_tax', 'hid_commerce_tax_label');
        register_setting('hid_commerce_tax', 'hid_commerce_tax_method');

        // Inventory settings
        register_setting('hid_commerce_inventory', 'hid_commerce_low_stock_threshold');
        register_setting('hid_commerce_inventory', 'hid_commerce_out_of_stock_behavior');
        register_setting('hid_commerce_inventory', 'hid_commerce_allow_backorders');

        // Payment gateways
        register_setting('hid_commerce_payments', 'hid_commerce_paystack_enabled');
        register_setting('hid_commerce_payments', 'hid_commerce_paystack_public_key');
        register_setting('hid_commerce_payments', 'hid_commerce_paystack_secret_key');
        register_setting('hid_commerce_payments', 'hid_commerce_stripe_enabled');
        register_setting('hid_commerce_payments', 'hid_commerce_stripe_publishable_key');
        register_setting('hid_commerce_payments', 'hid_commerce_stripe_secret_key');
        register_setting('hid_commerce_payments', 'hid_commerce_paypal_enabled');
        register_setting('hid_commerce_payments', 'hid_commerce_paypal_client_id');
        register_setting('hid_commerce_payments', 'hid_commerce_paypal_secret');
        register_setting('hid_commerce_payments', 'hid_commerce_bank_transfer_enabled');
        register_setting('hid_commerce_payments', 'hid_commerce_bank_name');
        register_setting('hid_commerce_payments', 'hid_commerce_bank_account_number');
        register_setting('hid_commerce_payments', 'hid_commerce_bank_account_name');
        register_setting('hid_commerce_payments', 'hid_commerce_bank_routing_number');

        // Email settings
        register_setting('hid_commerce_email', 'hid_commerce_low_stock_alert_recipients');
        
        // Social Media settings
        register_setting('hid_commerce_social', 'hid_commerce_instagram_url');
        register_setting('hid_commerce_social', 'hid_commerce_facebook_url');
        register_setting('hid_commerce_social', 'hid_commerce_pinterest_url');
        register_setting('hid_commerce_social', 'hid_commerce_twitter_url');
        register_setting('hid_commerce_social', 'hid_commerce_youtube_url');
        register_setting('hid_commerce_social', 'hid_commerce_linkedin_url');
        register_setting('hid_commerce_social', 'hid_commerce_ig_app_id');
        register_setting('hid_commerce_social', 'hid_commerce_ig_app_secret');
        register_setting('hid_commerce_social', 'hid_commerce_ig_token');
    }

    /**
     * Dashboard page
     */
    public static function dashboard_page() {
        $db = HID_Commerce_Database::get_instance();
        
        // Get analytics data
        $total_revenue = $db->get_total_revenue();
        $total_orders = $db->get_orders_count_by_date();
        $best_sellers = $db->get_best_selling_products(5);
        $low_stock_products = $db->get_products_with_low_stock();
        $low_stock_variants = $db->get_variants_with_low_stock();
        $recent_orders = $db->get_orders(array('limit' => 10));
        $order_status_breakdown = $db->get_order_status_breakdown();

        include HID_COMMERCE_THEME_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Products page
     */
    public static function products_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($action == 'edit' && $product_id) {
            self::edit_product_page($product_id);
        } elseif ($action == 'add') {
            self::add_product_page();
        } else {
            self::list_products_page();
        }
    }

    /**
     * List products page
     */
    private static function list_products_page() {
        $db = HID_Commerce_Database::get_instance();
        
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

        $products = $db->get_products(array(
            'limit' => $per_page,
            'offset' => $offset,
            'search' => $search,
            'category_id' => $category_id,
            'status' => null, // Show all products regardless of status in admin
        ));

        $total = $db->get_products_count(array(
            'search' => $search,
            'category_id' => $category_id,
            'status' => null, // Count all products regardless of status in admin
        ));

        $categories = $db->get_categories();

        include HID_COMMERCE_THEME_DIR . 'admin/views/products-list.php';
    }

    /**
     * Add product page
     */
    private static function add_product_page() {
        $db = HID_Commerce_Database::get_instance();
        $categories = $db->get_categories();
        $product = null;

        include HID_COMMERCE_THEME_DIR . 'admin/views/product-edit.php';
    }

    /**
     * Edit product page
     */
    private static function edit_product_page($product_id) {
        $db = HID_Commerce_Database::get_instance();
        $product = $db->get_product($product_id);
        
        if (!$product) {
            wp_die(__('Product not found', 'hid-simple-commerce'));
        }

        $categories = $db->get_categories();
        $images = $db->get_product_images($product_id);
        $variant_attributes = $db->get_variant_attributes($product_id);
        $variants = $db->get_product_variants($product_id);

        include HID_COMMERCE_THEME_DIR . 'admin/views/product-edit.php';
    }

    /**
     * Categories page
     */
    public static function categories_page() {
        $db = HID_Commerce_Database::get_instance();
        $categories = $db->get_categories();

        include HID_COMMERCE_THEME_DIR . 'admin/views/categories.php';
    }

    /**
     * Orders page
     */
    public static function orders_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($action == 'view' && $order_id) {
            self::view_order_page($order_id);
        } else {
            self::list_orders_page();
        }
    }

    /**
     * List orders page
     */
    private static function list_orders_page() {
        $db = HID_Commerce_Database::get_instance();
        
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : null;

        $orders = $db->get_orders(array(
            'limit' => $per_page,
            'offset' => $offset,
            'search' => $search,
            'status' => $status,
        ));

        $total = $db->get_orders_count(array(
            'search' => $search,
            'status' => $status,
        ));

        include HID_COMMERCE_THEME_DIR . 'admin/views/orders-list.php';
    }

    /**
     * View order page
     */
    private static function view_order_page($order_id) {
        $db = HID_Commerce_Database::get_instance();
        $order = $db->get_order($order_id);
        
        if (!$order) {
            wp_die(__('Order not found', 'hid-simple-commerce'));
        }

        $order_items = $db->get_order_items($order_id);
        $customer = $order->customer_id ? $db->get_customer($order->customer_id) : null;

        include HID_COMMERCE_THEME_DIR . 'admin/views/order-view.php';
    }

    /**
     * Customers page
     */
    public static function customers_page() {
        include HID_COMMERCE_THEME_DIR . 'admin/views/customers-list.php';
    }

    /**
     * Communications page
     */
    public static function communications_page() {
        include HID_COMMERCE_THEME_DIR . 'admin/views/communications.php';
    }

    /**
     * Settings page
     */
    public static function settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

        include HID_COMMERCE_THEME_DIR . 'admin/views/settings.php';
    }

    /**
     * Documentation page
     */
    public static function documentation_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';
        include HID_COMMERCE_THEME_DIR . 'admin/views/documentation.php';
    }

    // ============ AJAX HANDLERS ============

    /**
     * AJAX: Save product
     */
    public static function ajax_save_product() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $db = HID_Commerce_Database::get_instance();

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => wp_kses_post($_POST['description']),
            'sku' => sanitize_text_field($_POST['sku']),
            'category_id' => !empty($_POST['category_id']) ? intval($_POST['category_id']) : null,
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'image_url' => !empty($_POST['image_url']) ? esc_url_raw($_POST['image_url']) : '',
            'has_variants' => isset($_POST['has_variants']) ? 1 : 0,
            'meta_title' => sanitize_text_field($_POST['meta_title']),
            'meta_description' => sanitize_textarea_field($_POST['meta_description']),
            'slug' => sanitize_title($_POST['slug']),
            'status' => !empty($_POST['status']) ? sanitize_text_field($_POST['status']) : 'publish',
        );

        // Handle pricing for simple products
        if (empty($data['has_variants'])) {
            $data['price'] = floatval($_POST['price']);
            $data['sale_price'] = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
            $data['stock_quantity'] = intval($_POST['stock_quantity']);
            $data['low_stock_threshold'] = intval($_POST['low_stock_threshold']);
        }

        if ($product_id) {
            $result = $db->update_product($product_id, $data);
        } else {
            $product_id = $db->create_product($data);
            $result = $product_id;
        }

        if ($result) {
            // Sync primary image to product_images table
            if (!empty($data['image_url'])) {
                $existing_images = $db->get_product_images($product_id);
                $has_primary = false;
                foreach ($existing_images as $img) {
                    if ($img->is_primary) {
                        if ($img->image_url !== $data['image_url']) {
                            global $wpdb;
                            $wpdb->update(
                                $wpdb->prefix . 'hid_product_images',
                                array('image_url' => $data['image_url']),
                                array('id' => $img->id)
                            );
                        }
                        $has_primary = true;
                        break;
                    }
                }
                
                if (!$has_primary) {
                    $db->add_product_image($product_id, $data['image_url'], true);
                }
            }

            // Sync gallery images
            if (isset($_POST['gallery_images'])) {
                global $wpdb;
                // remove existing non-primary images
                $wpdb->query($wpdb->prepare(
                    "DELETE FROM {$wpdb->prefix}hid_product_images WHERE product_id = %d AND is_primary = 0",
                    $product_id
                ));
                
                if (is_array($_POST['gallery_images'])) {
                    foreach ($_POST['gallery_images'] as $img_url) {
                        if (!empty($img_url)) {
                            // Ensure we don't add the primary image again as a gallery image
                            if ($img_url !== $data['image_url']) {
                                $db->add_product_image($product_id, esc_url_raw($img_url), false);
                            }
                        }
                    }
                }
            }
        }

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Product saved successfully', 'hid-simple-commerce'),
                'product_id' => $product_id
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to save product', 'hid-simple-commerce')));
        }
    }

    /**
     * AJAX: Delete product
     */
    public static function ajax_delete_product() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $db = HID_Commerce_Database::get_instance();

        if ($db->delete_product($product_id)) {
            wp_send_json_success(array('message' => __('Product deleted successfully', 'hid-simple-commerce')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete product', 'hid-simple-commerce')));
        }
    }

    /**
     * AJAX: Save category
     */
    public static function ajax_save_category() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $db = HID_Commerce_Database::get_instance();

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
            'parent_id' => !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null,
            'image_url' => esc_url_raw($_POST['image_url']),
            'display_order' => intval($_POST['display_order']),
        );

        if ($category_id) {
            $result = $db->update_category($category_id, $data);
        } else {
            $category_id = $db->create_category($data);
            $result = $category_id;
        }

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Category saved successfully', 'hid-simple-commerce'),
                'category_id' => $category_id
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to save category', 'hid-simple-commerce')));
        }
    }

    /**
     * AJAX: Get category
     */
    public static function ajax_get_category() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $db = HID_Commerce_Database::get_instance();

        $category = $db->get_category($category_id);

        if ($category) {
            wp_send_json_success(array(
                'category' => $category
            ));
        } else {
            wp_send_json_error(array('message' => __('Category not found', 'hid-simple-commerce')));
        }
    }

    /**
     * AJAX: Delete category
     */
    public static function ajax_delete_category() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $db = HID_Commerce_Database::get_instance();

        if ($db->delete_category($category_id)) {
            wp_send_json_success(array('message' => __('Category deleted successfully', 'hid-simple-commerce')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete category', 'hid-simple-commerce')));
        }
    }

    /**
     * AJAX: Update order status
     */
    public static function ajax_update_order_status() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $order_status = sanitize_text_field($_POST['order_status']);
        $payment_status = isset($_POST['payment_status']) ? sanitize_text_field($_POST['payment_status']) : null;

        $db = HID_Commerce_Database::get_instance();
        
        $data = array('order_status' => $order_status);
        if ($payment_status) {
            $data['payment_status'] = $payment_status;
        }

        if ($db->update_order($order_id, $data)) {
            wp_send_json_success(array('message' => __('Order status updated successfully', 'hid-simple-commerce')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update order status', 'hid-simple-commerce')));
        }
    }

    /**
     * AJAX: Confirm payment for bank transfer
     */
    public static function ajax_confirm_payment() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

        if (!$order_id) {
            wp_send_json_error(array('message' => __('Invalid order ID', 'hid-simple-commerce')));
        }

        $db = HID_Commerce_Database::get_instance();
        $order = $db->get_order($order_id);

        if (!$order) {
            wp_send_json_error(array('message' => __('Order not found', 'hid-simple-commerce')));
        }

        if ($order->payment_method !== 'bank_transfer') {
            wp_send_json_error(array('message' => __('This order does not use bank transfer payment method', 'hid-simple-commerce')));
        }

        if ($order->payment_status === 'paid') {
            wp_send_json_error(array('message' => __('Payment has already been confirmed', 'hid-simple-commerce')));
        }

        // Update payment status to paid
        $data = array(
            'payment_status' => 'paid',
            'order_status' => 'processing' // Move order to processing when payment is confirmed
        );

        if ($db->update_order($order_id, $data)) {
            // Send payment confirmation email to customer
            HID_Commerce_Email_Handler::send_payment_confirmation($order_id);

            wp_send_json_success(array('message' => __('Payment confirmed successfully! Order moved to processing.', 'hid-simple-commerce')));
        } else {
            wp_send_json_error(array('message' => __('Failed to confirm payment', 'hid-simple-commerce')));
        }
    }

    /**
     * AJAX: Export products
     */
    public static function ajax_export_products() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $db = HID_Commerce_Database::get_instance();
        $products = $db->get_products(array('limit' => 9999));

        $csv_data = array();
        $csv_data[] = array('ID', 'Name', 'Description', 'Price', 'Sale Price', 'SKU', 'Stock', 'Category ID', 'Featured', 'Status');

        foreach ($products as $product) {
            $csv_data[] = array(
                $product->id,
                $product->name,
                $product->description,
                $product->price,
                $product->sale_price,
                $product->sku,
                $product->stock_quantity,
                $product->category_id,
                $product->featured,
                $product->status
            );
        }

        wp_send_json_success(array('csv_data' => $csv_data));
    }

    /**
     * AJAX: Export orders
     */
    public static function ajax_export_orders() {
        check_ajax_referer('hid_commerce_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'hid-simple-commerce')));
        }

        $db = HID_Commerce_Database::get_instance();
        $orders = $db->get_orders(array('limit' => 9999));

        $csv_data = array();
        $csv_data[] = array('Order Number', 'Customer Name', 'Email', 'Phone', 'Total', 'Status', 'Payment Method', 'Date');

        foreach ($orders as $order) {
            $csv_data[] = array(
                $order->order_number,
                $order->customer_name,
                $order->customer_email,
                $order->customer_phone,
                $order->total_amount,
                $order->order_status,
                $order->payment_method,
                $order->created_at
            );
        }

        wp_send_json_success(array('csv_data' => $csv_data));
    }

    /**
     * AJAX: Get variant data
     */
    public static function ajax_get_variant_data() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
        
        if (!$product_id) {
            wp_send_json_error(array('message' => __('Invalid product', 'hid-simple-commerce')));
        }

        $db = HID_Commerce_Database::get_instance();
        $variant_attributes = $db->get_variant_attributes($product_id);
        $variants = $db->get_product_variants($product_id);

        wp_send_json_success(array(
            'attributes' => $variant_attributes,
            'variants' => $variants
        ));
    }

    /**
     * Handle newsletter subscription
     */
    public static function ajax_newsletter_subscribe() {
        // Verify nonce
        check_ajax_referer('hid_newsletter_nonce', 'nonce');
        
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $captcha_answer = isset($_POST['captcha']) ? absint($_POST['captcha']) : 0;
        $captcha_session = isset($_POST['captcha_session']) ? absint($_POST['captcha_session']) : 0;
        
        // Validate email
        if (empty($email) || !is_email($email)) {
            wp_send_json_error(array('message' => __('Please enter a valid email address.', 'hid-simple-commerce')));
        }
        
        // Validate captcha
        if ($captcha_answer !== $captcha_session) {
            wp_send_json_error(array('message' => __('Incorrect answer. Please try again.', 'hid-simple-commerce')));
        }
        
        global $wpdb;
        $customers_table = $wpdb->prefix . 'hid_customers';
        
        // Check if email already exists
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $customers_table WHERE email = %s",
            $email
        ));
        
        if ($existing) {
            // If they're already a customer, don't downgrade them to subscriber
            if ($existing->customer_type === 'customer') {
                wp_send_json_success(array('message' => __('You\'re already part of our community! Thank you for your continued support.', 'hid-simple-commerce')));
            } else {
                // Already a subscriber
                wp_send_json_error(array('message' => __('This email is already subscribed.', 'hid-simple-commerce')));
            }
            return;
        }
        
        // Insert new subscriber
        $result = $wpdb->insert(
            $customers_table,
            array(
                'email' => $email,
                'customer_type' => 'subscriber',
                'date_subscribed' => current_time('mysql'),
            ),
            array('%s', '%s', '%s')
        );
        
        if ($result) {
            // Trigger welcome email
            do_action('hid_commerce_subscriber_added', $email, null);
            
            wp_send_json_success(array('message' => __('Thank you for subscribing! Check your email for a welcome message.', 'hid-simple-commerce')));
        } else {
            wp_send_json_error(array('message' => __('Something went wrong. Please try again.', 'hid-simple-commerce')));
        }
    }
}

