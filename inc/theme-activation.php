<?php
/**
 * Theme Activation Functionality
 *
 * Handles database table creation and default options setup when theme is activated.
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Run activation tasks when theme is switched
 */
function hid_hov_theme_activate() {
    // Create database tables
    $tables_created = hid_hov_theme_create_tables();
    
    // Set default options
    hid_hov_theme_set_default_options();
    
    // Create upload directories
    hid_hov_theme_create_upload_directories();
    
    // Set database version and activation status
    update_option('hid_commerce_db_version', '1.0.0');
    update_option('hid_commerce_tables_created', $tables_created);
    update_option('hid_commerce_activation_timestamp', time());
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'hid_hov_theme_activate');

/**
 * Create database tables
 */
function hid_hov_theme_create_tables() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Create products table
    $table_products = $wpdb->prefix . 'hid_products';
    $sql_products = "CREATE TABLE $table_products (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        description longtext,
        price decimal(10,2) NULL,
        sale_price decimal(10,2) NULL,
        sku varchar(100),
        stock_quantity int(11) NULL,
        low_stock_threshold int(11) DEFAULT 10,
        featured tinyint(1) DEFAULT 0,
        image_url varchar(500),
        has_variants tinyint(1) DEFAULT 0,
        category_id bigint(20) UNSIGNED NULL,
        meta_title varchar(255),
        meta_description text,
        slug varchar(255),
        status varchar(20) DEFAULT 'publish',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_category (category_id),
        KEY idx_featured (featured),
        KEY idx_status (status),
        KEY idx_slug (slug)
    ) $charset_collate;";

    // Create product categories table
    $table_categories = $wpdb->prefix . 'hid_product_categories';
    $sql_categories = "CREATE TABLE $table_categories (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        slug varchar(255) NOT NULL,
        description text,
        parent_id bigint(20) UNSIGNED NULL,
        image_url varchar(500),
        display_order int(11) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_parent (parent_id),
        KEY idx_slug (slug)
    ) $charset_collate;";

    // Create product images table
    $table_images = $wpdb->prefix . 'hid_product_images';
    $sql_images = "CREATE TABLE $table_images (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        product_id bigint(20) UNSIGNED NOT NULL,
        image_url varchar(500) NOT NULL,
        image_order int(11) DEFAULT 0,
        is_primary tinyint(1) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_product (product_id)
    ) $charset_collate;";

    // Create product variant attributes table
    $table_variant_attrs = $wpdb->prefix . 'hid_product_variant_attributes';
    $sql_variant_attrs = "CREATE TABLE $table_variant_attrs (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        product_id bigint(20) UNSIGNED NOT NULL,
        attribute_name varchar(100) NOT NULL,
        attribute_order int(11) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_product (product_id)
    ) $charset_collate;";

    // Create product variants table
    $table_variants = $wpdb->prefix . 'hid_product_variants';
    $sql_variants = "CREATE TABLE $table_variants (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        product_id bigint(20) UNSIGNED NOT NULL,
        variant_attributes longtext,
        variant_display_name varchar(255),
        price decimal(10,2) NOT NULL,
        sale_price decimal(10,2) NULL,
        stock_quantity int(11) DEFAULT 0,
        sku varchar(100),
        image_url varchar(500),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_product (product_id),
        KEY idx_sku (sku)
    ) $charset_collate;";

    // Create customers table (for both subscribers and customers)
    $table_customers = $wpdb->prefix . 'hid_customers';
    $sql_customers = "CREATE TABLE $table_customers (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name varchar(255) NULL,
        email varchar(255) NOT NULL UNIQUE,
        phone varchar(50) NULL,
        shipping_address text,
        customer_type varchar(20) DEFAULT 'subscriber',
        date_subscribed datetime DEFAULT CURRENT_TIMESTAMP,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY idx_email (email),
        KEY idx_customer_type (customer_type)
    ) $charset_collate;";

    // Create orders table
    $table_orders = $wpdb->prefix . 'hid_orders';
    $sql_orders = "CREATE TABLE $table_orders (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        order_number varchar(50) NOT NULL UNIQUE,
        customer_id bigint(20) UNSIGNED NULL,
        customer_name varchar(255) NOT NULL,
        customer_email varchar(255) NOT NULL,
        customer_phone varchar(50),
        shipping_address text,
        shipping_location varchar(255),
        shipping_courier varchar(255),
        payment_method varchar(50) NOT NULL,
        payment_status varchar(50) DEFAULT 'pending',
        payment_proof_url varchar(500),
        order_status varchar(50) DEFAULT 'pending',
        order_notes text,
        subtotal decimal(10,2) DEFAULT 0,
        tax_amount decimal(10,2) DEFAULT 0,
        total_amount decimal(10,2) NOT NULL,
        currency varchar(10) DEFAULT 'USD',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_order_number (order_number),
        KEY idx_customer (customer_id),
        KEY idx_customer_email (customer_email),
        KEY idx_status (order_status),
        KEY idx_payment_status (payment_status)
    ) $charset_collate;";

    // Create order items table
    $table_order_items = $wpdb->prefix . 'hid_order_items';
    $sql_order_items = "CREATE TABLE $table_order_items (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        order_id bigint(20) UNSIGNED NOT NULL,
        product_id bigint(20) UNSIGNED NOT NULL,
        variant_id bigint(20) UNSIGNED NULL,
        product_name varchar(255) NOT NULL,
        variant_name varchar(255),
        quantity int(11) NOT NULL,
        price decimal(10,2) NOT NULL,
        subtotal decimal(10,2) NOT NULL,
        PRIMARY KEY (id),
        KEY idx_order (order_id),
        KEY idx_product (product_id)
    ) $charset_collate;";

    // Create contact inquiries table
    $table_contact_inquiries = $wpdb->prefix . 'hid_contact_inquiries';
    $sql_contact_inquiries = "CREATE TABLE $table_contact_inquiries (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        subject varchar(255) NOT NULL,
        message text NOT NULL,
        status varchar(50) DEFAULT 'unread',
        admin_notes text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_email (email),
        KEY idx_status (status),
        KEY idx_created_at (created_at)
    ) $charset_collate;";

    // Create jewelry requests table
    $table_jewelry_requests = $wpdb->prefix . 'hid_jewelry_requests';
    $sql_jewelry_requests = "CREATE TABLE $table_jewelry_requests (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        jewelry_type varchar(100) NOT NULL,
        budget_range varchar(100),
        description text NOT NULL,
        status varchar(50) DEFAULT 'pending',
        admin_notes text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_email (email),
        KEY idx_status (status),
        KEY idx_jewelry_type (jewelry_type),
        KEY idx_created_at (created_at)
    ) $charset_collate;";

    // Execute table creation
    $results = array();
    $results[] = dbDelta($sql_products);
    $results[] = dbDelta($sql_categories);
    $results[] = dbDelta($sql_images);
    $results[] = dbDelta($sql_variant_attrs);
    $results[] = dbDelta($sql_variants);
    $results[] = dbDelta($sql_customers);
    $results[] = dbDelta($sql_orders);
    $results[] = dbDelta($sql_order_items);
    $results[] = dbDelta($sql_contact_inquiries);
    $results[] = dbDelta($sql_jewelry_requests);
    
    // Verify tables were created
    $tables_to_check = array(
        $wpdb->prefix . 'hid_products',
        $wpdb->prefix . 'hid_product_categories',
        $wpdb->prefix . 'hid_product_images',
        $wpdb->prefix . 'hid_product_variant_attributes',
        $wpdb->prefix . 'hid_product_variants',
        $wpdb->prefix . 'hid_customers',
        $wpdb->prefix . 'hid_orders',
        $wpdb->prefix . 'hid_order_items',
        $wpdb->prefix . 'hid_contact_inquiries',
        $wpdb->prefix . 'hid_jewelry_requests',
    );
    
    $tables_exist = array();
    foreach ($tables_to_check as $table) {
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
        $tables_exist[$table] = $table_exists;
    }
    
    return $tables_exist;
}

/**
 * Set default plugin options
 */
function hid_hov_theme_set_default_options() {
    // General settings
    add_option('hid_commerce_admin_email', get_option('admin_email'));
    add_option('hid_commerce_site_logo', '');
    
    // Color scheme (Velvet Rouge theme)
    add_option('hid_commerce_color_primary', '#470108');
    add_option('hid_commerce_color_secondary', '#B4A06A');
    add_option('hid_commerce_color_accent', '#3a5a8b');
    add_option('hid_commerce_color_background', '#F8F4E9');
    add_option('hid_commerce_color_white', '#FFFFFF');
    
    // Typography
    add_option('hid_commerce_font_header', 'Dancing Script');
    add_option('hid_commerce_font_body', 'Montserrat');
    add_option('hid_commerce_font_size_base', '16');
    
    // Currency settings - Nigerian Naira
    add_option('hid_commerce_currency_code', 'NGN');
    add_option('hid_commerce_currency_symbol', '₦');
    add_option('hid_commerce_currency_position', 'before');
    add_option('hid_commerce_decimal_places', '2');
    add_option('hid_commerce_thousands_separator', ',');
    add_option('hid_commerce_decimal_separator', '.');
    
    // Tax settings
    add_option('hid_commerce_tax_enabled', '0');
    add_option('hid_commerce_tax_rate', '0');
    add_option('hid_commerce_tax_label', 'VAT');
    add_option('hid_commerce_tax_method', 'exclusive');
    
    // Inventory settings
    add_option('hid_commerce_low_stock_threshold', '10');
    add_option('hid_commerce_out_of_stock_behavior', 'show');
    add_option('hid_commerce_allow_backorders', '0');
    
    // Payment gateway settings
    add_option('hid_commerce_paystack_enabled', '0');
    add_option('hid_commerce_paystack_public_key', '');
    add_option('hid_commerce_paystack_secret_key', '');
    
    add_option('hid_commerce_stripe_enabled', '0');
    add_option('hid_commerce_stripe_publishable_key', '');
    add_option('hid_commerce_stripe_secret_key', '');
    
    add_option('hid_commerce_paypal_enabled', '0');
    add_option('hid_commerce_paypal_client_id', '');
    add_option('hid_commerce_paypal_secret', '');
    
    add_option('hid_commerce_bank_transfer_enabled', '1');
    add_option('hid_commerce_bank_name', '');
    add_option('hid_commerce_bank_account_number', '');
    add_option('hid_commerce_bank_account_name', '');
    add_option('hid_commerce_bank_routing_number', '');
    
    // Email settings
    add_option('hid_commerce_low_stock_alert_recipients', get_option('admin_email'));
}

/**
 * Create upload directories
 */
function hid_hov_theme_create_upload_directories() {
    $upload_dir = wp_upload_dir();
    $payment_proofs_dir = $upload_dir['basedir'] . '/payment-proofs';
    
    if (!file_exists($payment_proofs_dir)) {
        wp_mkdir_p($payment_proofs_dir);
        // Add .htaccess to protect the directory
        file_put_contents($payment_proofs_dir . '/.htaccess', 'Options -Indexes');
    }
}

/**
 * Theme deactivation cleanup
 */
function hid_hov_theme_deactivate() {
    // Clear any scheduled cron jobs
    wp_clear_scheduled_hook('hid_commerce_daily_tasks');
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('switch_theme', 'hid_hov_theme_deactivate');

/**
 * Admin notice to verify tables were created
 */
function hid_hov_theme_tables_notice() {
    // Show success message if tables were just recreated
    if (isset($_GET['hid_tables_recreated'])) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>HID HOV Theme:</strong> Database tables have been successfully recreated!</p>
        </div>
        <?php
        return;
    }
    
    $tables_created = get_option('hid_commerce_tables_created', array());
    
    if (empty($tables_created)) {
        return; // First activation, tables_created option doesn't exist yet
    }
    
    // Check if any tables are missing
    $missing_tables = array();
    foreach ($tables_created as $table => $exists) {
        if (!$exists) {
            $missing_tables[] = $table;
        }
    }
    
    if (!empty($missing_tables)) {
        ?>
        <div class="notice notice-error">
            <p><strong>HID HOV Theme:</strong> Some database tables were not created during activation.</p>
            <p><strong>Missing tables:</strong></p>
            <ul style="list-style: disc; margin-left: 20px;">
                <?php foreach ($missing_tables as $table): ?>
                    <li><code><?php echo esc_html($table); ?></code></li>
                <?php endforeach; ?>
            </ul>
            <p>
                <a href="<?php echo esc_url(add_query_arg('hid_recreate_tables', '1', admin_url('admin.php?page=hid-commerce'))); ?>" class="button button-primary">
                    Recreate Database Tables
                </a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'hid_hov_theme_tables_notice');

/**
 * Handle manual table recreation
 */
function hid_hov_theme_handle_table_recreation() {
    if (!isset($_GET['hid_recreate_tables']) || !current_user_can('manage_options')) {
        return;
    }
    
    // Recreate tables
    $tables_created = hid_hov_theme_create_tables();
    update_option('hid_commerce_tables_created', $tables_created);
    
    // Redirect with success message
    wp_safe_redirect(add_query_arg('hid_tables_recreated', '1', admin_url('admin.php?page=hid-commerce')));
    exit;
}
add_action('admin_init', 'hid_hov_theme_handle_table_recreation');

