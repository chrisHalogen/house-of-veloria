<?php
/**
 * Inventory management class
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Inventory {

    /**
     * Check stock availability
     */
    public static function check_stock_availability($product_id, $variant_id = null, $quantity = 1) {
        $db = HID_Commerce_Database::get_instance();

        if ($variant_id) {
            $variant = $db->get_variant($variant_id);
            return $variant && $variant->stock_quantity >= $quantity;
        } else {
            $product = $db->get_product($product_id);
            return $product && $product->stock_quantity >= $quantity;
        }
    }

    /**
     * Reduce stock
     */
    public static function reduce_stock($product_id, $variant_id = null, $quantity = 1) {
        $db = HID_Commerce_Database::get_instance();

        if ($variant_id) {
            // Reduce variant stock
            $variant = $db->get_variant($variant_id);
            
            if ($variant) {
                $new_stock = max(0, $variant->stock_quantity - $quantity);
                $db->update_variant($variant_id, array('stock_quantity' => $new_stock));
                
                // Check low stock
                self::check_low_stock_variant($variant_id);
            }
        } else {
            // Reduce product stock
            $product = $db->get_product($product_id);
            
            if ($product) {
                $new_stock = max(0, $product->stock_quantity - $quantity);
                $db->update_product($product_id, array('stock_quantity' => $new_stock));
                
                // Check low stock
                self::check_low_stock_product($product_id);
            }
        }
    }

    /**
     * Check low stock for product
     */
    private static function check_low_stock_product($product_id) {
        $db = HID_Commerce_Database::get_instance();
        $product = $db->get_product($product_id);
        
        if (!$product || $product->has_variants) {
            return;
        }

        $threshold = $product->low_stock_threshold ?: get_option('hid_commerce_low_stock_threshold', 10);

        if ($product->stock_quantity <= $threshold && $product->stock_quantity > 0) {
            // Send low stock alert
            self::send_low_stock_notification(array($product));
        }
    }

    /**
     * Check low stock for variant
     */
    private static function check_low_stock_variant($variant_id) {
        $db = HID_Commerce_Database::get_instance();
        $variant = $db->get_variant($variant_id);
        
        if (!$variant) {
            return;
        }

        $product = $db->get_product($variant->product_id);
        $threshold = $product->low_stock_threshold ?: get_option('hid_commerce_low_stock_threshold', 10);

        if ($variant->stock_quantity <= $threshold && $variant->stock_quantity > 0) {
            // Send low stock alert for variant
            $variant_data = array(
                'name' => $product->name . ' - ' . $variant->variant_display_name,
                'stock_quantity' => $variant->stock_quantity,
                'low_stock_threshold' => $threshold,
            );
            
            self::send_low_stock_notification(array((object) $variant_data));
        }
    }

    /**
     * Send low stock notification
     */
    private static function send_low_stock_notification($products) {
        // Check if we've already sent a notification today for these products
        $sent_today = get_transient('hid_commerce_low_stock_notified');
        
        if (!is_array($sent_today)) {
            $sent_today = array();
        }

        $new_products = array();
        foreach ($products as $product) {
            $product_key = isset($product->id) ? $product->id : $product->name;
            
            if (!in_array($product_key, $sent_today)) {
                $new_products[] = $product;
                $sent_today[] = $product_key;
            }
        }

        if (!empty($new_products)) {
            HID_Commerce_Email::send_low_stock_alert($new_products);
            
            // Set transient to prevent multiple notifications in one day
            set_transient('hid_commerce_low_stock_notified', $sent_today, DAY_IN_SECONDS);
        }
    }

    /**
     * Get stock status
     */
    public static function get_stock_status($product_id, $variant_id = null) {
        $db = HID_Commerce_Database::get_instance();

        if ($variant_id) {
            $variant = $db->get_variant($variant_id);
            $stock = $variant ? $variant->stock_quantity : 0;
            $product = $db->get_product($product_id);
            $threshold = $product->low_stock_threshold ?: get_option('hid_commerce_low_stock_threshold', 10);
        } else {
            $product = $db->get_product($product_id);
            $stock = $product ? $product->stock_quantity : 0;
            $threshold = $product->low_stock_threshold ?: get_option('hid_commerce_low_stock_threshold', 10);
        }

        if ($stock <= 0) {
            return array(
                'status' => 'out_of_stock',
                'label' => __('Out of Stock', 'hid-simple-commerce'),
                'class' => 'out-of-stock',
                'color' => '#dc3232',
            );
        } elseif ($stock <= $threshold) {
            return array(
                'status' => 'low_stock',
                'label' => sprintf(__('Low Stock (%d left)', 'hid-simple-commerce'), $stock),
                'class' => 'low-stock',
                'color' => '#f0b429',
            );
        } else {
            return array(
                'status' => 'in_stock',
                'label' => __('In Stock', 'hid-simple-commerce'),
                'class' => 'in-stock',
                'color' => '#46b450',
            );
        }
    }

    /**
     * Get stock status display HTML
     */
    public static function get_stock_status_html($product_id, $variant_id = null) {
        $status = self::get_stock_status($product_id, $variant_id);
        
        return sprintf(
            '<span class="stock-status %s" style="color: %s;">%s</span>',
            esc_attr($status['class']),
            esc_attr($status['color']),
            esc_html($status['label'])
        );
    }

    /**
     * Check if product is in stock
     */
    public static function is_in_stock($product_id, $variant_id = null, $quantity = 1) {
        $db = HID_Commerce_Database::get_instance();

        if ($variant_id) {
            $variant = $db->get_variant($variant_id);
            return $variant && $variant->stock_quantity >= $quantity;
        } else {
            $product = $db->get_product($product_id);
            return $product && $product->stock_quantity >= $quantity;
        }
    }

    /**
     * Get all low stock products
     */
    public static function get_low_stock_products() {
        $db = HID_Commerce_Database::get_instance();
        $low_stock_products = $db->get_products_with_low_stock();
        $low_stock_variants = $db->get_variants_with_low_stock();

        return array(
            'products' => $low_stock_products,
            'variants' => $low_stock_variants,
        );
    }
}

