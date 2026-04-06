<?php
/**
 * HID HOV Theme Functions
 *
 * @package HID_HOV_Theme
 * @version 1.0.0
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('HID_COMMERCE_VERSION', '1.1.0');
define('HID_COMMERCE_THEME_DIR', get_template_directory() . '/');
define('HID_COMMERCE_THEME_URL', get_template_directory_uri() . '/');

/**
 * Theme Setup
 */
function hid_hov_theme_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'hid-hov-theme'),
        'footer-links' => __('Footer Quick Links', 'hid-hov-theme'),
        'footer-care' => __('Footer Customer Care', 'hid-hov-theme'),
        'footer-policies' => __('Footer Policies', 'hid-hov-theme'),
    ));

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for editor styles
    add_theme_support('editor-styles');

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Load text domain for translations
    load_theme_textdomain('hid-hov-theme', HID_COMMERCE_THEME_DIR . 'languages');
}
add_action('after_setup_theme', 'hid_hov_theme_setup');

/**
 * Autoloader for theme classes
 */
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'HID_Commerce_';

    // Base directory for the namespace prefix
    $base_dir = HID_COMMERCE_THEME_DIR . 'includes/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace underscores with hyphens and convert to lowercase
    $file = $base_dir . 'class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Initialize Theme Commerce Functionality
 */
function hid_hov_theme_init() {
    // Start session for cart persistence
    if (!session_id() && !headers_sent()) {
        session_start();
    }

    // Load required class files
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-database.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-session.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-admin.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-shortcodes.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-checkout.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-payments.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-email.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-email-wrapper.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-inventory.php';
    require_once HID_COMMERCE_THEME_DIR . 'includes/class-inquiries.php';

    // Load admin pages
    if (is_admin()) {
        require_once HID_COMMERCE_THEME_DIR . 'admin/inquiries-admin-pages.php';
    }

    // Initialize session cart
    HID_Commerce_Session::init();

    // Initialize admin
    if (is_admin()) {
        HID_Commerce_Admin::init();
    }

    // Initialize shortcodes
    HID_Commerce_Shortcodes::init();
}
add_action('after_setup_theme', 'hid_hov_theme_init', 20);

/**
 * Load theme activation functionality
 */
require_once HID_COMMERCE_THEME_DIR . 'inc/theme-activation.php';

/**
 * Load template helper functions
 */
require_once HID_COMMERCE_THEME_DIR . 'inc/template-functions.php';

/**
 * Load custom nav walker
 */
require_once HID_COMMERCE_THEME_DIR . 'inc/class-nav-walker.php';

/**
 * Load page setup utility
 */
require_once HID_COMMERCE_THEME_DIR . 'inc/page-setup.php';

/**
 * Enqueue frontend scripts and styles
 */
function hid_hov_theme_enqueue_scripts() {
    // Enqueue Google Fonts
    wp_enqueue_style(
        'hid-google-fonts',
        'https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );

    // Deregister any existing Font Awesome (e.g., from Elementor) and register our version
    wp_deregister_style('font-awesome');
    
    // Enqueue Font Awesome 6 from cdnjs
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
    
    // Add crossorigin attribute for Font Awesome
    add_filter('style_loader_tag', 'hid_add_font_awesome_attributes', 10, 2);

    // Enqueue main theme stylesheet
    wp_enqueue_style(
        'hid-theme-style',
        HID_COMMERCE_THEME_URL . 'assets/css/theme-style.css',
        array('font-awesome'),
        HID_COMMERCE_VERSION
    );

    // Enqueue commerce styles
    wp_enqueue_style(
        'hid-commerce-public',
        HID_COMMERCE_THEME_URL . 'assets/css/public-styles.css',
        array('hid-theme-style'),
        HID_COMMERCE_VERSION
    );

    // Enqueue Paystack Inline JS
    wp_enqueue_script(
        'paystack-inline',
        'https://js.paystack.co/v2/inline.js',
        array(),
        null,
        true
    );

    // Enqueue jQuery (WordPress includes it)
    wp_enqueue_script('jquery');

    // Enqueue main theme scripts
    wp_enqueue_script(
        'hid-theme-scripts',
        HID_COMMERCE_THEME_URL . 'assets/js/theme-scripts.js',
        array('jquery'),
        HID_COMMERCE_VERSION,
        true
    );

    // Localize theme script for newsletter subscription
    wp_localize_script('hid-theme-scripts', 'hidTheme', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('hid_newsletter_nonce'),
    ));

    // Enqueue commerce scripts
    wp_enqueue_script(
        'hid-commerce-public',
        HID_COMMERCE_THEME_URL . 'assets/js/public-scripts.js',
        array('jquery', 'hid-theme-scripts'),
        HID_COMMERCE_VERSION,
        true
    );

    // Localize script with AJAX URL and nonce
    wp_localize_script('hid-commerce-public', 'hidCommerce', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('hid_commerce_nonce'),
        'currency_symbol' => get_option('hid_commerce_currency_symbol', '₦'),
        'currency_position' => get_option('hid_commerce_currency_position', 'before'),
        'decimal_places' => get_option('hid_commerce_decimal_places', 2),
        'thousands_separator' => get_option('hid_commerce_thousands_separator', ','),
        'decimal_separator' => get_option('hid_commerce_decimal_separator', '.'),
        'cart_count' => class_exists('HID_Commerce_Session') ? HID_Commerce_Session::get_cart_count() : 0,
    ));
}
add_action('wp_enqueue_scripts', 'hid_hov_theme_enqueue_scripts');

/**
 * Add crossorigin attribute to Font Awesome stylesheet
 */
function hid_add_font_awesome_attributes($html, $handle) {
    if ($handle === 'font-awesome') {
        return str_replace("media='all'", "media='all' crossorigin='anonymous'", $html);
    }
    return $html;
}

/**
 * Enqueue admin scripts and styles
 */
function hid_hov_theme_enqueue_admin_scripts($hook) {
    // Only load on our admin pages
    if (strpos($hook, 'hid-commerce') === false) {
        return;
    }

    wp_enqueue_style(
        'hid-commerce-admin',
        HID_COMMERCE_THEME_URL . 'admin/css/admin-styles.css',
        array(),
        HID_COMMERCE_VERSION
    );

    wp_enqueue_script(
        'hid-commerce-admin',
        HID_COMMERCE_THEME_URL . 'admin/js/admin-scripts.js',
        array('jquery', 'wp-color-picker'),
        HID_COMMERCE_VERSION,
        true
    );

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_media();

    wp_localize_script('hid-commerce-admin', 'hidCommerceAdmin', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('hid_commerce_admin_nonce'),
    ));
}
add_action('admin_enqueue_scripts', 'hid_hov_theme_enqueue_admin_scripts');

/**
 * Register widget areas
 */
function hid_hov_theme_widgets_init() {
    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'hid-hov-theme'),
        'id'            => 'footer-widget',
        'description'   => __('Add widgets here to appear in footer.', 'hid-hov-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'hid_hov_theme_widgets_init');

/**
 * Add custom body classes
 */
function hid_hov_theme_body_classes($classes) {
    // Add class if we're on a commerce page
    if (is_page_template('page-shop.php')) {
        $classes[] = 'hid-shop-page';
    }
    
    if (is_front_page()) {
        $classes[] = 'hid-home-page';
    }

    return $classes;
}
add_filter('body_class', 'hid_hov_theme_body_classes');

/**
 * Custom template loader
 */
function hid_hov_theme_template_include($template) {
    // Check for page templates in templates directory
    if (is_page()) {
        $page_template = get_page_template_slug();
        
        if ($page_template && file_exists(HID_COMMERCE_THEME_DIR . 'templates/' . $page_template)) {
            return HID_COMMERCE_THEME_DIR . 'templates/' . $page_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'hid_hov_theme_template_include');

/**
 * Add page templates from templates directory
 */
function hid_hov_theme_add_page_templates($templates) {
    $custom_templates = array(
        'templates/page-home.php' => __('Homepage', 'hid-hov-theme'),
        'templates/page-shop.php' => __('Shop Page', 'hid-hov-theme'),
        'templates/page-curation-story.php' => __('Curation Story', 'hid-hov-theme'),
        'templates/page-contact.php' => __('Contact Page', 'hid-hov-theme'),
        'templates/page-testimonials.php' => __('Testimonials', 'hid-hov-theme'),
        'templates/page-shopping-guide.php' => __('Shopping Guide', 'hid-hov-theme'),
        'templates/page-privacy-policy.php' => __('Privacy Policy', 'hid-hov-theme'),
        'templates/page-terms.php' => __('Terms & Conditions', 'hid-hov-theme'),
        'templates/page-refund-policy.php' => __('Refund Policy', 'hid-hov-theme'),
        'templates/page-shipping-policy.php' => __('Shipping Policy', 'hid-hov-theme'),
        'templates/page-order-confirmation.php' => __('Order Confirmation', 'hid-hov-theme'),
        'templates/page-order-tracking.php' => __('Order Tracking', 'hid-hov-theme'),
    );

    return array_merge($templates, $custom_templates);
}
add_filter('theme_page_templates', 'hid_hov_theme_add_page_templates');

/**
 * Get theme logo URL
 */
function hid_hov_theme_get_logo_url() {
    $custom_logo_id = get_theme_mod('custom_logo');
    
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
        if ($logo_url) {
            return $logo_url;
        }
    }
    
    // Return default logo if no custom logo set
    $default_logo = get_option('hid_commerce_site_logo', '');
    if ($default_logo) {
        return $default_logo;
    }
    
    return 'https://hoveloria.com/wp-content/uploads/2025/11/cropped-logo2.png';
}

/**
 * Disable WordPress admin bar on frontend for non-admins
 */
function hid_hov_theme_admin_bar() {
    if (!current_user_can('manage_options') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'hid_hov_theme_admin_bar');


/**
 * Reset Commerce Data After Theme Switch
 * 
 * Clears all products, categories, orders, and customers data.
 * Keeps site options and theme settings intact.
 */
function hid_hov_theme_reset_commerce_data() {
    global $wpdb;

    // Tables to truncate/empty
    $tables = array(
        'hid_order_items',
        'hid_orders',
        'hid_customers',
        'hid_product_variants',
        'hid_product_variant_attributes',
        'hid_product_images',
        'hid_products',
        'hid_product_categories',
    );

    // Disable foreign key checks to avoid constraint errors
    $wpdb->query('SET FOREIGN_KEY_CHECKS = 0');

    foreach ($tables as $table) {
        $table_name = $wpdb->prefix . $table;
        
        // Check if table exists before trying to truncate
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
            $wpdb->query("TRUNCATE TABLE $table_name");
        }
    }

    // Re-enable foreign key checks
    $wpdb->query('SET FOREIGN_KEY_CHECKS = 1');
    
    // Optional: Log the reset
    error_log('HID Commerce data reset: Products, Categories, Orders, and Customers have been cleared.');
}
// add_action('after_switch_theme', 'hid_hov_theme_reset_commerce_data');

/**
 * Fetch Instagram Feed using Graph API
 */
function hid_get_instagram_feed() {
    $app_id = get_option('hid_commerce_ig_app_id');
    $app_secret = get_option('hid_commerce_ig_app_secret');
    $access_token = get_option('hid_commerce_ig_token');

    // Default static fallback images
    $static_images = array(
        'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=400&q=80',
        'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=400&q=80',
        'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=400&q=80',
        'https://images.unsplash.com/photo-1603561596112-0a132b757442?w=400&q=80',
        'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=400&q=80',
        'https://images.unsplash.com/photo-1602173574767-37ac01994b2a?w=400&q=80',
        'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400&q=80',
        'https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=400&q=80',
        'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=400&q=80',
    );

    $profile_url = get_option('hid_commerce_instagram_url', 'https://www.instagram.com/houseofveloria/');
    // Ensure URL has a trailing slash for aesthetics
    if (!empty($profile_url) && substr($profile_url, -1) !== '/') {
        $profile_url .= '/';
    }

    $feed = array();

    // Check if we have credentials
    if (empty($access_token)) {
        return self_format_fallback_images($static_images, $profile_url);
    }

    // Check transient cache
    $cached_feed = get_transient('hid_instagram_feed');
    if ($cached_feed !== false) {
        return $cached_feed;
    }

    // Make API Request
    $url = add_query_arg(
        array(
            'fields' => 'id,caption,media_type,media_url,permalink,thumbnail_url,children{media_type,media_url}',
            'access_token' => $access_token,
            'limit' => 25 // Fetch enough to filter down to 9 valid images
        ),
        'https://graph.instagram.com/me/media'
    );

    $response = wp_remote_get($url, array('timeout' => 15));

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return self_format_fallback_images($static_images, $profile_url);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['data'])) {
        return self_format_fallback_images($static_images, $profile_url);
    }

    // Process items
    foreach ($body['data'] as $item) {
        if (count($feed) >= 9) break;

        if ($item['media_type'] === 'IMAGE') {
            $feed[] = array(
                'url' => $item['media_url'],
                'link' => $item['permalink']
            );
        } elseif ($item['media_type'] === 'CAROUSEL_ALBUM' && !empty($item['children']['data'])) {
            // Find the first IMAGE child
            foreach ($item['children']['data'] as $child) {
                if ($child['media_type'] === 'IMAGE') {
                    $feed[] = array(
                        'url' => $child['media_url'],
                        'link' => $item['permalink']
                    );
                    break;
                }
            }
        }
    }

    // Pad with static images if we didn't find enough
    if (count($feed) < 9) {
        $needed = 9 - count($feed);
        for ($i = 0; $i < $needed; $i++) {
            $feed[] = array(
                'url' => $static_images[$i],
                'link' => $profile_url
            );
        }
    }

    // Cache for 2 hours
    set_transient('hid_instagram_feed', $feed, 2 * HOUR_IN_SECONDS);

    return $feed;
}

function self_format_fallback_images($static_images, $profile_url) {
    $feed = array();
    foreach (array_slice($static_images, 0, 9) as $img) {
        $feed[] = array(
            'url' => $img,
            'link' => $profile_url
        );
    }
    return $feed;
}
