<?php
/**
 * Session cart management class
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Session {

    private static $cart_key = 'hid_commerce_cart';

    /**
     * Initialize session
     */
    public static function init() {
        if (!session_id()) {
            session_start();
        }

        // Register AJAX handlers
        add_action('wp_ajax_hid_add_to_cart', array(__CLASS__, 'ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_hid_add_to_cart', array(__CLASS__, 'ajax_add_to_cart'));
        
        add_action('wp_ajax_hid_update_cart', array(__CLASS__, 'ajax_update_cart'));
        add_action('wp_ajax_nopriv_hid_update_cart', array(__CLASS__, 'ajax_update_cart'));
        
        add_action('wp_ajax_hid_remove_from_cart', array(__CLASS__, 'ajax_remove_from_cart'));
        add_action('wp_ajax_nopriv_hid_remove_from_cart', array(__CLASS__, 'ajax_remove_from_cart'));
        
        add_action('wp_ajax_hid_get_cart', array(__CLASS__, 'ajax_get_cart'));
        add_action('wp_ajax_nopriv_hid_get_cart', array(__CLASS__, 'ajax_get_cart'));
    }

    /**
     * Get cart contents
     */
    public static function get_cart() {
        if (!isset($_SESSION[self::$cart_key])) {
            $_SESSION[self::$cart_key] = array();
        }
        return $_SESSION[self::$cart_key];
    }

    /**
     * Add item to cart
     */
    public static function add_to_cart($product_id, $quantity = 1, $variant_id = null, $price = 0) {
        $cart = self::get_cart();
        
        // Create unique key for cart item
        $cart_key = $variant_id ? "{$product_id}_{$variant_id}" : $product_id;

        if (isset($cart[$cart_key])) {
            // Update quantity if item already exists
            $cart[$cart_key]['quantity'] += $quantity;
        } else {
            // Add new item
            $cart[$cart_key] = array(
                'product_id' => $product_id,
                'variant_id' => $variant_id,
                'quantity' => $quantity,
                'price' => $price,
                'added_at' => time()
            );
        }

        $_SESSION[self::$cart_key] = $cart;
        return true;
    }

    /**
     * Update cart item quantity
     */
    public static function update_cart_item($cart_key, $quantity) {
        $cart = self::get_cart();

        if (isset($cart[$cart_key])) {
            if ($quantity <= 0) {
                unset($cart[$cart_key]);
            } else {
                $cart[$cart_key]['quantity'] = $quantity;
            }
            $_SESSION[self::$cart_key] = $cart;
            return true;
        }

        return false;
    }

    /**
     * Remove item from cart
     */
    public static function remove_from_cart($cart_key) {
        $cart = self::get_cart();

        if (isset($cart[$cart_key])) {
            unset($cart[$cart_key]);
            $_SESSION[self::$cart_key] = $cart;
            return true;
        }

        return false;
    }

    /**
     * Clear cart
     */
    public static function clear_cart() {
        $_SESSION[self::$cart_key] = array();
        return true;
    }

    /**
     * Get cart count
     */
    public static function get_cart_count() {
        $cart = self::get_cart();
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Get cart items with full details
     */
    public static function get_cart_items() {
        $cart = self::get_cart();
        $items = array();
        $db = HID_Commerce_Database::get_instance();

        foreach ($cart as $key => $item) {
            $product = $db->get_product($item['product_id']);
            
            if (!$product) {
                continue;
            }

            $cart_item = array(
                'cart_key' => $key,
                'product_id' => $item['product_id'],
                'product_name' => $product->name,
                'product_image' => $product->image_url,
                'variant_id' => $item['variant_id'],
                'variant_name' => '',
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
                'stock_available' => true,
            );

            // Get variant details if applicable
            if ($item['variant_id']) {
                $variant = $db->get_variant($item['variant_id']);
                if ($variant) {
                    $cart_item['variant_name'] = $variant->variant_display_name;
                    $cart_item['price'] = $variant->sale_price ?: $variant->price;
                    $cart_item['subtotal'] = $cart_item['price'] * $item['quantity'];
                    $cart_item['stock_available'] = $variant->stock_quantity >= $item['quantity'];
                    
                    if ($variant->image_url) {
                        $cart_item['product_image'] = $variant->image_url;
                    }
                }
            } else {
                // Check stock for simple products
                $cart_item['stock_available'] = $product->stock_quantity >= $item['quantity'];
            }

            $items[] = $cart_item;
        }

        return $items;
    }

    /**
     * Calculate cart totals
     */
    public static function calculate_totals() {
        $items = self::get_cart_items();
        $subtotal = 0;
        $tax_amount = 0;
        $total = 0;

        foreach ($items as $item) {
            $subtotal += $item['subtotal'];
        }

        // Calculate tax if enabled
        if (get_option('hid_commerce_tax_enabled', '0') == '1') {
            $tax_rate = (float) get_option('hid_commerce_tax_rate', 0);
            $tax_method = get_option('hid_commerce_tax_method', 'exclusive');

            if ($tax_method == 'exclusive') {
                $tax_amount = ($subtotal * $tax_rate) / 100;
                $total = $subtotal + $tax_amount;
            } else {
                // Inclusive
                $tax_amount = ($subtotal * $tax_rate) / (100 + $tax_rate);
                $total = $subtotal;
            }
        } else {
            $total = $subtotal;
        }

        return array(
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
            'total' => $total,
            'tax_label' => get_option('hid_commerce_tax_label', 'VAT'),
            'currency' => get_option('hid_commerce_currency_code', 'USD'),
        );
    }

    /**
     * Validate cart items (check stock availability)
     */
    public static function validate_cart() {
        $items = self::get_cart_items();
        $errors = array();

        foreach ($items as $item) {
            if (!$item['stock_available']) {
                $errors[] = sprintf(
                    __('%s is out of stock or insufficient quantity available.', 'hid-simple-commerce'),
                    $item['product_name'] . ($item['variant_name'] ? ' - ' . $item['variant_name'] : '')
                );
            }
        }

        return empty($errors) ? true : $errors;
    }

    // ============ AJAX HANDLERS ============

    /**
     * AJAX: Add to cart
     */
    public static function ajax_add_to_cart() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $variant_id = isset($_POST['variant_id']) ? intval($_POST['variant_id']) : null;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if (!$product_id) {
            wp_send_json_error(array('message' => __('Invalid product', 'hid-simple-commerce')));
        }

        $db = HID_Commerce_Database::get_instance();
        $product = $db->get_product($product_id);

        if (!$product) {
            wp_send_json_error(array('message' => __('Product not found', 'hid-simple-commerce')));
        }

        // Check if product requires variant selection
        if ($product->has_variants && !$variant_id) {
            wp_send_json_error(array('message' => __('Please select product options', 'hid-simple-commerce')));
        }

        // Get price
        $price = 0;
        $stock_available = false;

        if ($variant_id) {
            $variant = $db->get_variant($variant_id);
            if (!$variant) {
                wp_send_json_error(array('message' => __('Variant not found', 'hid-simple-commerce')));
            }
            $price = $variant->sale_price ?: $variant->price;
            $stock_available = $variant->stock_quantity >= $quantity;
        } else {
            $price = $product->sale_price ?: $product->price;
            $stock_available = $product->stock_quantity >= $quantity;
        }

        // Check stock availability
        if (!$stock_available) {
            wp_send_json_error(array('message' => __('Product is out of stock', 'hid-simple-commerce')));
        }

        // Add to cart
        self::add_to_cart($product_id, $quantity, $variant_id, $price);

        wp_send_json_success(array(
            'message' => __('Product added to cart', 'hid-simple-commerce'),
            'cart_count' => self::get_cart_count(),
            'cart_items' => self::get_cart_items(),
            'totals' => self::calculate_totals()
        ));
    }

    /**
     * AJAX: Update cart
     */
    public static function ajax_update_cart() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        $cart_key = isset($_POST['cart_key']) ? sanitize_text_field($_POST['cart_key']) : '';
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

        if (!$cart_key) {
            wp_send_json_error(array('message' => __('Invalid cart item', 'hid-simple-commerce')));
        }

        self::update_cart_item($cart_key, $quantity);

        wp_send_json_success(array(
            'message' => __('Cart updated', 'hid-simple-commerce'),
            'cart_count' => self::get_cart_count(),
            'cart_items' => self::get_cart_items(),
            'totals' => self::calculate_totals()
        ));
    }

    /**
     * AJAX: Remove from cart
     */
    public static function ajax_remove_from_cart() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        $cart_key = isset($_POST['cart_key']) ? sanitize_text_field($_POST['cart_key']) : '';

        if (!$cart_key) {
            wp_send_json_error(array('message' => __('Invalid cart item', 'hid-simple-commerce')));
        }

        self::remove_from_cart($cart_key);

        wp_send_json_success(array(
            'message' => __('Item removed from cart', 'hid-simple-commerce'),
            'cart_count' => self::get_cart_count(),
            'cart_items' => self::get_cart_items(),
            'totals' => self::calculate_totals()
        ));
    }

    /**
     * AJAX: Get cart
     */
    public static function ajax_get_cart() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        wp_send_json_success(array(
            'cart_count' => self::get_cart_count(),
            'cart_items' => self::get_cart_items(),
            'totals' => self::calculate_totals()
        ));
    }
}

