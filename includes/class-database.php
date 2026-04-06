<?php
/**
 * Database operations class
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Database {

    private static $instance = null;
    private $wpdb;

    private function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ============ PRODUCTS ============

    public function create_product($data) {
        // Validation: If has_variants=true, enforce price=NULL
        if (!empty($data['has_variants']) && ($data['has_variants'] == 1 || $data['has_variants'] === true)) {
            $data['price'] = null;
            $data['sale_price'] = null;
            $data['stock_quantity'] = null;
        }

        // Validation: If has_variants=false, require price
        if (empty($data['has_variants']) && empty($data['price'])) {
            return new WP_Error('missing_price', __('Price is required for simple products', 'hid-simple-commerce'));
        }

        // Auto-generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = sanitize_title($data['name']);
        }

        // Prepare the format array to match the data fields
        $format = array();
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'price':
                case 'sale_price':
                    $format[] = '%f';
                    break;
                case 'stock_quantity':
                case 'low_stock_threshold':
                case 'featured':
                case 'has_variants':
                case 'category_id':
                    $format[] = '%d';
                    break;
                default:
                    $format[] = '%s';
                    break;
            }
        }

        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'hid_products',
            $data,
            $format
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    public function update_product($id, $data) {
        // Apply same validation as create
        if (!empty($data['has_variants']) && $data['has_variants'] == 1) {
            $data['price'] = null;
            $data['sale_price'] = null;
            $data['stock_quantity'] = null;
        }

        // Prepare the format array to match the data fields
        $format = array();
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'price':
                case 'sale_price':
                    $format[] = '%f';
                    break;
                case 'stock_quantity':
                case 'low_stock_threshold':
                case 'featured':
                case 'has_variants':
                case 'category_id':
                    $format[] = '%d';
                    break;
                default:
                    $format[] = '%s';
                    break;
            }
        }

        return $this->wpdb->update(
            $this->wpdb->prefix . 'hid_products',
            $data,
            array('id' => $id),
            $format,
            array('%d')
        );
    }

    public function get_product($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_products WHERE id = %d",
                $id
            )
        );
    }

    public function get_products($args = array()) {
        $defaults = array(
            'limit' => 20,
            'offset' => 0,
            'category_id' => null,
            'featured' => null,
            'status' => 'publish',
            'search' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
            'min_price' => null,
            'max_price' => null,
        );

        $args = wp_parse_args($args, $defaults);
        
        $where = array();
        $values = array();
        
        // Only filter by status if it's not null
        if ($args['status'] !== null) {
            $where[] = "status = %s";
            $values[] = $args['status'];
        }

        if ($args['category_id']) {
            $where[] = "category_id = %d";
            $values[] = $args['category_id'];
        }

        if ($args['featured'] !== null) {
            $where[] = "featured = %d";
            $values[] = $args['featured'];
        }

        if (!empty($args['search'])) {
            $where[] = "(name LIKE %s OR description LIKE %s OR sku LIKE %s)";
            $search_term = '%' . $this->wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }

        if ($args['min_price'] !== null) {
            $where[] = "price >= %f";
            $values[] = $args['min_price'];
        }

        if ($args['max_price'] !== null) {
            $where[] = "price <= %f";
            $values[] = $args['max_price'];
        }

        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $query = "SELECT * FROM {$this->wpdb->prefix}hid_products $where_clause";
        
        $query .= " ORDER BY {$args['orderby']} {$args['order']}";
        $query .= " LIMIT %d OFFSET %d";
        $values[] = $args['limit'];
        $values[] = $args['offset'];

        return $this->wpdb->get_results(
            $this->wpdb->prepare($query, $values)
        );
    }

    public function delete_product($id) {
        return $this->wpdb->delete(
            $this->wpdb->prefix . 'hid_products',
            array('id' => $id),
            array('%d')
        );
    }

    public function get_products_count($args = array()) {
        $defaults = array(
            'category_id' => null,
            'featured' => null,
            'status' => 'publish',
            'search' => '',
        );

        $args = wp_parse_args($args, $defaults);
        
        $where = array();
        $values = array();
        
        // Only filter by status if it's not null
        if ($args['status'] !== null) {
            $where[] = "status = %s";
            $values[] = $args['status'];
        }

        if ($args['category_id']) {
            $where[] = "category_id = %d";
            $values[] = $args['category_id'];
        }

        if ($args['featured'] !== null) {
            $where[] = "featured = %d";
            $values[] = $args['featured'];
        }

        if (!empty($args['search'])) {
            $where[] = "(name LIKE %s OR description LIKE %s OR sku LIKE %s)";
            $search_term = '%' . $this->wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }

        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $query = "SELECT COUNT(*) FROM {$this->wpdb->prefix}hid_products $where_clause";

        if (!empty($values)) {
        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare($query, $values)
        );
        } else {
            return (int) $this->wpdb->get_var($query);
        }
    }

    public function get_products_with_low_stock() {
        $query = "SELECT * FROM {$this->wpdb->prefix}hid_products 
                  WHERE has_variants = 0 
                  AND stock_quantity <= low_stock_threshold 
                  AND stock_quantity > 0 
                  AND status = 'publish'";
        
        return $this->wpdb->get_results($query);
    }

    // ============ CATEGORIES ============

    public function create_category($data) {
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = sanitize_title($data['name']);
        }

        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'hid_product_categories',
            $data
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    public function update_category($id, $data) {
        return $this->wpdb->update(
            $this->wpdb->prefix . 'hid_product_categories',
            $data,
            array('id' => $id)
        );
    }

    public function get_category($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_product_categories WHERE id = %d",
                $id
            )
        );
    }

    public function get_categories($parent_id = null) {
        if ($parent_id === null) {
            return $this->wpdb->get_results(
                "SELECT * FROM {$this->wpdb->prefix}hid_product_categories ORDER BY display_order ASC, name ASC"
            );
        }

        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_product_categories WHERE parent_id = %d ORDER BY display_order ASC, name ASC",
                $parent_id
            )
        );
    }

    public function delete_category($id) {
        return $this->wpdb->delete(
            $this->wpdb->prefix . 'hid_product_categories',
            array('id' => $id),
            array('%d')
        );
    }

    public function get_category_product_count($category_id) {
        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->wpdb->prefix}hid_products WHERE category_id = %d AND status = 'publish'",
                $category_id
            )
        );
    }

    // ============ PRODUCT IMAGES ============

    public function add_product_image($product_id, $image_url, $is_primary = false) {
        if ($is_primary) {
            // Remove primary flag from other images
            $this->wpdb->update(
                $this->wpdb->prefix . 'hid_product_images',
                array('is_primary' => 0),
                array('product_id' => $product_id)
            );
        }

        $max_order = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT MAX(image_order) FROM {$this->wpdb->prefix}hid_product_images WHERE product_id = %d",
                $product_id
            )
        );

        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'hid_product_images',
            array(
                'product_id' => $product_id,
                'image_url' => $image_url,
                'image_order' => ($max_order ?? 0) + 1,
                'is_primary' => $is_primary ? 1 : 0
            )
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    public function get_product_images($product_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_product_images WHERE product_id = %d ORDER BY image_order ASC",
                $product_id
            )
        );
    }

    public function delete_product_image($id) {
        return $this->wpdb->delete(
            $this->wpdb->prefix . 'hid_product_images',
            array('id' => $id),
            array('%d')
        );
    }

    public function update_image_order($id, $order) {
        return $this->wpdb->update(
            $this->wpdb->prefix . 'hid_product_images',
            array('image_order' => $order),
            array('id' => $id)
        );
    }

    // ============ VARIANT ATTRIBUTES ============

    public function add_variant_attribute($product_id, $attribute_name, $attribute_order = 0) {
        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'hid_product_variant_attributes',
            array(
                'product_id' => $product_id,
                'attribute_name' => $attribute_name,
                'attribute_order' => $attribute_order
            )
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    public function get_variant_attributes($product_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_product_variant_attributes WHERE product_id = %d ORDER BY attribute_order ASC",
                $product_id
            )
        );
    }

    public function delete_variant_attribute($id) {
        return $this->wpdb->delete(
            $this->wpdb->prefix . 'hid_product_variant_attributes',
            array('id' => $id),
            array('%d')
        );
    }

    // ============ VARIANTS ============

    public function create_variant($data) {
        // Convert attributes array to JSON if needed
        if (isset($data['variant_attributes']) && is_array($data['variant_attributes'])) {
            $data['variant_attributes'] = json_encode($data['variant_attributes']);
        }

        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'hid_product_variants',
            $data
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    public function update_variant($id, $data) {
        if (isset($data['variant_attributes']) && is_array($data['variant_attributes'])) {
            $data['variant_attributes'] = json_encode($data['variant_attributes']);
        }

        return $this->wpdb->update(
            $this->wpdb->prefix . 'hid_product_variants',
            $data,
            array('id' => $id)
        );
    }

    public function get_variant($id) {
        $variant = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_product_variants WHERE id = %d",
                $id
            )
        );

        if ($variant && !empty($variant->variant_attributes)) {
            $variant->variant_attributes = json_decode($variant->variant_attributes, true);
        }

        return $variant;
    }

    public function get_product_variants($product_id) {
        $variants = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_product_variants WHERE product_id = %d ORDER BY id ASC",
                $product_id
            )
        );

        foreach ($variants as $variant) {
            if (!empty($variant->variant_attributes)) {
                $variant->variant_attributes = json_decode($variant->variant_attributes, true);
            }
        }

        return $variants;
    }

    public function delete_variant($id) {
        return $this->wpdb->delete(
            $this->wpdb->prefix . 'hid_product_variants',
            array('id' => $id),
            array('%d')
        );
    }

    public function find_variant_by_attributes($product_id, $attributes) {
        $variants = $this->get_product_variants($product_id);
        
        foreach ($variants as $variant) {
            if ($this->attributes_match($variant->variant_attributes, $attributes)) {
                return $variant;
            }
        }

        return null;
    }

    private function attributes_match($variant_attrs, $search_attrs) {
        if (!is_array($variant_attrs) || !is_array($search_attrs)) {
            return false;
        }

        foreach ($search_attrs as $key => $value) {
            if (!isset($variant_attrs[$key]) || $variant_attrs[$key] != $value) {
                return false;
            }
        }

        return true;
    }

    public function get_variants_with_low_stock() {
        $threshold = get_option('hid_commerce_low_stock_threshold', 10);
        
        $query = "SELECT v.*, p.name as product_name 
                  FROM {$this->wpdb->prefix}hid_product_variants v
                  INNER JOIN {$this->wpdb->prefix}hid_products p ON v.product_id = p.id
                  WHERE v.stock_quantity <= %d 
                  AND v.stock_quantity > 0 
                  AND p.status = 'publish'";
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($query, $threshold)
        );
    }

    // ============ CUSTOMERS ============

    public function create_customer($data) {
        $existing = $this->get_customer_by_email($data['email']);
        
        if ($existing) {
            return $existing->id;
        }

        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'hid_customers',
            $data
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    public function update_customer($id, $data) {
        return $this->wpdb->update(
            $this->wpdb->prefix . 'hid_customers',
            $data,
            array('id' => $id)
        );
    }

    public function get_customer($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_customers WHERE id = %d",
                $id
            )
        );
    }

    public function get_customer_by_email($email) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_customers WHERE email = %s",
                $email
            )
        );
    }

    public function get_customers($args = array()) {
        $defaults = array(
            'limit' => 20,
            'offset' => 0,
            'search' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
        );

        $args = wp_parse_args($args, $defaults);
        
        $where = array("1=1");
        $values = array();

        if (!empty($args['search'])) {
            $where[] = "(name LIKE %s OR email LIKE %s OR phone LIKE %s)";
            $search_term = '%' . $this->wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }

        $where_clause = implode(' AND ', $where);
        $query = "SELECT * FROM {$this->wpdb->prefix}hid_customers WHERE $where_clause";
        
        $query .= " ORDER BY {$args['orderby']} {$args['order']}";
        $query .= " LIMIT %d OFFSET %d";
        $values[] = $args['limit'];
        $values[] = $args['offset'];

        if (empty($values)) {
            return $this->wpdb->get_results($query);
        }

        return $this->wpdb->get_results(
            $this->wpdb->prepare($query, $values)
        );
    }

    // ============ ORDERS ============

    public function create_order($data) {
        // Generate unique order number if not provided
        if (empty($data['order_number'])) {
            $data['order_number'] = $this->generate_order_number();
        }

        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'hid_orders',
            $data
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    private function generate_order_number() {
        $prefix = 'HOV';
        $timestamp = time();
        $random = wp_rand(1000, 9999);
        
        return $prefix . '-' . $timestamp . '-' . $random;
    }

    public function update_order($id, $data) {
        return $this->wpdb->update(
            $this->wpdb->prefix . 'hid_orders',
            $data,
            array('id' => $id)
        );
    }

    public function get_order($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_orders WHERE id = %d",
                $id
            )
        );
    }

    public function get_order_by_number($order_number) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_orders WHERE order_number = %s",
                $order_number
            )
        );
    }

    public function get_order_by_number_and_email($order_number, $email) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_orders WHERE order_number = %s AND customer_email = %s",
                $order_number,
                $email
            )
        );
    }

    public function get_orders($args = array()) {
        $defaults = array(
            'limit' => 20,
            'offset' => 0,
            'status' => null,
            'payment_status' => null,
            'search' => '',
            'date_from' => null,
            'date_to' => null,
            'orderby' => 'created_at',
            'order' => 'DESC',
        );

        $args = wp_parse_args($args, $defaults);
        
        $where = array("1=1");
        $values = array();

        if ($args['status']) {
            $where[] = "order_status = %s";
            $values[] = $args['status'];
        }

        if ($args['payment_status']) {
            $where[] = "payment_status = %s";
            $values[] = $args['payment_status'];
        }

        if (!empty($args['search'])) {
            $where[] = "(order_number LIKE %s OR customer_name LIKE %s OR customer_email LIKE %s)";
            $search_term = '%' . $this->wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }

        if ($args['date_from']) {
            $where[] = "created_at >= %s";
            $values[] = $args['date_from'];
        }

        if ($args['date_to']) {
            $where[] = "created_at <= %s";
            $values[] = $args['date_to'];
        }

        $where_clause = implode(' AND ', $where);
        $query = "SELECT * FROM {$this->wpdb->prefix}hid_orders WHERE $where_clause";
        
        $query .= " ORDER BY {$args['orderby']} {$args['order']}";
        $query .= " LIMIT %d OFFSET %d";
        $values[] = $args['limit'];
        $values[] = $args['offset'];

        return $this->wpdb->get_results(
            $this->wpdb->prepare($query, $values)
        );
    }

    public function get_orders_count($args = array()) {
        $defaults = array(
            'status' => null,
            'payment_status' => null,
            'search' => '',
            'date_from' => null,
            'date_to' => null,
        );

        $args = wp_parse_args($args, $defaults);
        
        $where = array("1=1");
        $values = array();

        if ($args['status']) {
            $where[] = "order_status = %s";
            $values[] = $args['status'];
        }

        if ($args['payment_status']) {
            $where[] = "payment_status = %s";
            $values[] = $args['payment_status'];
        }

        if (!empty($args['search'])) {
            $where[] = "(order_number LIKE %s OR customer_name LIKE %s OR customer_email LIKE %s)";
            $search_term = '%' . $this->wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }

        if ($args['date_from']) {
            $where[] = "created_at >= %s";
            $values[] = $args['date_from'];
        }

        if ($args['date_to']) {
            $where[] = "created_at <= %s";
            $values[] = $args['date_to'];
        }

        $where_clause = implode(' AND ', $where);
        $query = "SELECT COUNT(*) FROM {$this->wpdb->prefix}hid_orders WHERE $where_clause";

        if (empty($values)) {
            return (int) $this->wpdb->get_var($query);
        }

        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare($query, $values)
        );
    }

    // ============ ORDER ITEMS ============

    public function add_order_item($data) {
        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'hid_order_items',
            $data
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    public function get_order_items($order_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hid_order_items WHERE order_id = %d",
                $order_id
            )
        );
    }

    // ============ ANALYTICS ============

    public function get_total_revenue($date_from = null, $date_to = null) {
        $where = array("payment_status = 'paid' OR payment_status = 'completed'");
        $values = array();

        if ($date_from) {
            $where[] = "created_at >= %s";
            $values[] = $date_from;
        }

        if ($date_to) {
            $where[] = "created_at <= %s";
            $values[] = $date_to;
        }

        $where_clause = implode(' AND ', $where);
        $query = "SELECT SUM(total_amount) FROM {$this->wpdb->prefix}hid_orders WHERE $where_clause";

        if (empty($values)) {
            return (float) $this->wpdb->get_var($query);
        }

        return (float) $this->wpdb->get_var(
            $this->wpdb->prepare($query, $values)
        );
    }

    public function get_orders_count_by_date($date_from = null, $date_to = null) {
        $where = array("1=1");
        $values = array();

        if ($date_from) {
            $where[] = "created_at >= %s";
            $values[] = $date_from;
        }

        if ($date_to) {
            $where[] = "created_at <= %s";
            $values[] = $date_to;
        }

        $where_clause = implode(' AND ', $where);
        $query = "SELECT COUNT(*) FROM {$this->wpdb->prefix}hid_orders WHERE $where_clause";

        if (empty($values)) {
            return (int) $this->wpdb->get_var($query);
        }

        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare($query, $values)
        );
    }

    public function get_best_selling_products($limit = 10) {
        $query = "SELECT oi.product_id, oi.product_name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as total_revenue
                  FROM {$this->wpdb->prefix}hid_order_items oi
                  INNER JOIN {$this->wpdb->prefix}hid_orders o ON oi.order_id = o.id
                  WHERE o.payment_status IN ('paid', 'completed')
                  GROUP BY oi.product_id
                  ORDER BY total_sold DESC
                  LIMIT %d";

        return $this->wpdb->get_results(
            $this->wpdb->prepare($query, $limit)
        );
    }

    public function get_revenue_by_payment_method($date_from = null, $date_to = null) {
        $where = array("payment_status IN ('paid', 'completed')");
        $values = array();

        if ($date_from) {
            $where[] = "created_at >= %s";
            $values[] = $date_from;
        }

        if ($date_to) {
            $where[] = "created_at <= %s";
            $values[] = $date_to;
        }

        $where_clause = implode(' AND ', $where);
        $query = "SELECT payment_method, SUM(total_amount) as total, COUNT(*) as count
                  FROM {$this->wpdb->prefix}hid_orders 
                  WHERE $where_clause
                  GROUP BY payment_method";

        if (empty($values)) {
            return $this->wpdb->get_results($query);
        }

        return $this->wpdb->get_results(
            $this->wpdb->prepare($query, $values)
        );
    }

    public function get_order_status_breakdown() {
        $query = "SELECT order_status, COUNT(*) as count
                  FROM {$this->wpdb->prefix}hid_orders 
                  GROUP BY order_status";

        return $this->wpdb->get_results($query);
    }
}

