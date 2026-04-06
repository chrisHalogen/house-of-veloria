<?php
/**
 * Shortcodes class
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Shortcodes {

    /**
     * Initialize shortcodes
     */
    public static function init() {
        add_shortcode('hid_products', array(__CLASS__, 'products_shortcode'));
        add_shortcode('hid_featured_products', array(__CLASS__, 'featured_products_shortcode'));
        add_shortcode('hid_shop', array(__CLASS__, 'shop_shortcode'));
        add_shortcode('hid_order_confirmation', array(__CLASS__, 'order_confirmation_shortcode'));
        add_shortcode('hid_order_lookup', array(__CLASS__, 'order_lookup_shortcode'));
        
        // AJAX handlers
        add_action('wp_ajax_hid_filter_products', array(__CLASS__, 'ajax_filter_products'));
        add_action('wp_ajax_nopriv_hid_filter_products', array(__CLASS__, 'ajax_filter_products'));
    }

    /**
     * Products shortcode
     */
    public static function products_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'columns' => 3,
            'featured' => false,
            'category' => '',
            'search' => 'no',
        ), $atts);

        $db = HID_Commerce_Database::get_instance();
        
        $args = array(
            'limit' => intval($atts['limit']),
            'featured' => $atts['featured'] === 'true' || $atts['featured'] === '1' ? 1 : null,
        );

        if (!empty($atts['category'])) {
            $args['category_id'] = intval($atts['category']);
        }

        $products = $db->get_products($args);
        
        ob_start();
        self::render_products_grid($products, $atts);
        return ob_get_clean();
    }

    /**
     * Featured products shortcode
     */
    public static function featured_products_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 6,
            'columns' => 3,
        ), $atts);

        $atts['featured'] = 'true';
        
        return self::products_shortcode($atts);
    }

    /**
     * Shop shortcode
     */
    public static function shop_shortcode($atts) {
        $atts = shortcode_atts(array(
            'columns' => 3,
        ), $atts);

        // Get filter from URL
        $filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '';
        $category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
        $page = isset($_GET['shop_page']) ? max(1, intval($_GET['shop_page'])) : 1;
        $per_page = 9;
        $offset = ($page - 1) * $per_page;

        $db = HID_Commerce_Database::get_instance();
        
        // Build query args based on filter
        $query_args = array(
            'limit' => $per_page,
            'offset' => $offset,
            'status' => 'publish',
        );

        if ($category_filter > 0) {
            $query_args['category_id'] = $category_filter;
        }
        
        if ($filter === 'new-arrivals') {
            $query_args['orderby'] = 'created_at';
            $query_args['order'] = 'DESC';
        } elseif ($filter === 'best-sellers') {
            // Get best sellers - will need special handling
            $best_sellers = $db->get_best_selling_products($per_page * 3);
            $product_ids = array_map(function($item) { return $item->product_id; }, $best_sellers);
        }
        
        // Get products
        if ($filter === 'best-sellers' && !empty($product_ids)) {
            $products = array();
            foreach (array_slice($product_ids, $offset, $per_page) as $product_id) {
                $product = $db->get_product($product_id);
                if ($product && $product->status === 'publish') {
                     // Filter by category if set
                     if ($category_filter > 0 && $product->category_id != $category_filter) {
                        continue;
                    }
                    $products[] = $product;
                }
            }
            $total_count = count($product_ids);
        } else {
            $products = $db->get_products($query_args);
            $count_args = $query_args;
            unset($count_args['limit'], $count_args['offset']);
            $total_count = $db->get_products_count($count_args);
        }
        
        $total_pages = ceil($total_count / $per_page);

        ob_start();
        ?>
        <div class="hid-commerce-shop" id="hid-commerce-shop" style="padding: 2rem 0;">
            <div class="hid-shop-header">
                <div class="hid-search-filter-bar">
                    <div class="hid-search-box">
                        <input type="text" id="hid-product-search" placeholder="<?php esc_attr_e('Search products...', 'hid-simple-commerce'); ?>">
                        <button type="button" id="hid-search-button" class="hid-button-primary">
                            <i class="fas fa-search"></i> <?php _e('Search', 'hid-simple-commerce'); ?>
                        </button>
                    </div>
                    
                    <div class="hid-filter-controls">
                        <?php $categories = $db->get_categories(); ?>
                        
                        <select id="hid-special-filter">
                            <option value=""><?php _e('All Products', 'hid-simple-commerce'); ?></option>
                            <option value="new-arrivals" <?php selected($filter, 'new-arrivals'); ?>><?php _e('New Arrivals', 'hid-simple-commerce'); ?></option>
                            <option value="best-sellers" <?php selected($filter, 'best-sellers'); ?>><?php _e('Best Sellers', 'hid-simple-commerce'); ?></option>
                        </select>
                        
                            <select id="hid-category-filter">
                                <option value=""><?php _e('All Categories', 'hid-simple-commerce'); ?></option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo esc_attr($category->id); ?>" <?php selected($category_filter, $category->id); ?>><?php echo esc_html($category->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        
                        <select id="hid-sort-products">
                            <option value="created_at-DESC"><?php _e('Newest First', 'hid-simple-commerce'); ?></option>
                            <option value="name-ASC"><?php _e('Name: A-Z', 'hid-simple-commerce'); ?></option>
                            <option value="name-DESC"><?php _e('Name: Z-A', 'hid-simple-commerce'); ?></option>
                            <option value="price-ASC"><?php _e('Price: Low to High', 'hid-simple-commerce'); ?></option>
                            <option value="price-DESC"><?php _e('Price: High to Low', 'hid-simple-commerce'); ?></option>
                        </select>
                        
                        <button type="button" id="hid-apply-filters" class="hid-button-primary"><?php _e('Apply Filters', 'hid-simple-commerce'); ?></button>
                        <button type="button" id="hid-clear-filters" class="hid-button-secondary"><?php _e('Clear', 'hid-simple-commerce'); ?></button>
                    </div>
                </div>
                
                <?php if ($filter || $category_filter): ?>
                <div class="hid-active-filter">
                    <strong><?php _e('Active Filter:', 'hid-simple-commerce'); ?></strong> 
                    <?php 
                    if ($filter) {
                        echo $filter === 'new-arrivals' ? __('New Arrivals', 'hid-simple-commerce') : __('Best Sellers', 'hid-simple-commerce');
                    }
                    if ($filter && $category_filter) echo ' + ';
                    if ($category_filter) {
                        $active_cat = $db->get_category($category_filter);
                        if ($active_cat) echo esc_html($active_cat->name);
                    }
                    ?> 
                    (<?php echo $total_count; ?> <?php _e('products', 'hid-simple-commerce'); ?>)
                </div>
                <?php endif; ?>
            </div>

            <div class="hid-shop-content">
                <div class="hid-products-area">
                <div id="hid-products-grid" class="hid-products-grid columns-<?php echo esc_attr($atts['columns']); ?>">
                <?php if (empty($products)): ?>
                    <div class="hid-no-products" style="text-align: center; padding: 50px 0; background: #f9f9f9; border-radius: 8px;">
                        <span style="font-size: 40px; margin-bottom: 20px; display: block;">🔍</span>
                        <h3 style="margin-bottom: 15px;"><?php _e('No Products Found', 'hid-simple-commerce'); ?></h3>
                        <p><?php _e('We couldn\'t find any products matching your selection.', 'hid-simple-commerce'); ?></p>
                        <a href="<?php echo esc_url(remove_query_arg(array('category', 'filter', 'shop_page'))); ?>" class="hid-button-primary" style="margin-top: 20px; display: inline-block;"><?php _e('View All Products', 'hid-simple-commerce'); ?></a>
                    </div>
                <?php else: ?>
                        <?php self::render_products_grid($products, $atts, false); ?>
                <?php endif; ?>
                </div>
                    
                    <?php if ($total_pages > 1): ?>
                    <div class="hid-shop-pagination">
                        <?php
                        $shop_url = remove_query_arg('shop_page');
                        
                        // Previous button
                        if ($page > 1):
                            $prev_url = add_query_arg('shop_page', $page - 1, $shop_url);
                        ?>
                            <a href="<?php echo esc_url($prev_url); ?>" class="hid-page-link hid-page-nav" data-page="<?php echo ($page - 1); ?>">
                                <i class="fas fa-chevron-left"></i> <?php _e('Previous', 'hid-simple-commerce'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        // Show pages 1-5
                        for ($i = 1; $i <= min(5, $total_pages); $i++):
                            $page_url = add_query_arg('shop_page', $i, $shop_url);
                            $is_current = ($i === $page);
                        ?>
                            <a href="<?php echo esc_url($page_url); ?>" class="hid-page-link <?php echo $is_current ? 'current' : ''; ?>" data-page="<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php 
                        // Show ellipsis if there are more pages
                        if ($total_pages > 5): ?>
                            <span class="hid-page-ellipsis">...</span>
                        <?php endif; ?>
                        
                        <?php
                        // Show page 10 if it exists and isn't already shown
                        if ($total_pages >= 10):
                            $page_url = add_query_arg('shop_page', 10, $shop_url);
                            $is_current = (10 === $page);
                        ?>
                            <a href="<?php echo esc_url($page_url); ?>" class="hid-page-link <?php echo $is_current ? 'current' : ''; ?>" data-page="10">
                                10
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        // Show page 20 if it exists and isn't already shown
                        if ($total_pages >= 20):
                            $page_url = add_query_arg('shop_page', 20, $shop_url);
                            $is_current = (20 === $page);
                        ?>
                            <a href="<?php echo esc_url($page_url); ?>" class="hid-page-link <?php echo $is_current ? 'current' : ''; ?>" data-page="20">
                                20
                            </a>
                        <?php endif; ?>
                        
                    <?php
                        // Next button
                        if ($page < $total_pages):
                            $next_url = add_query_arg('shop_page', $page + 1, $shop_url);
                        ?>
                            <a href="<?php echo esc_url($next_url); ?>" class="hid-page-link hid-page-nav" data-page="<?php echo ($page + 1); ?>">
                                <?php _e('Next', 'hid-simple-commerce'); ?> <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div id="hid-cart-sidebar" class="hid-cart-sidebar">
                    <h3><?php _e('Your Cart', 'hid-simple-commerce'); ?> <span class="cart-count">(0)</span></h3>
                    <div id="hid-cart-items"></div>
                    <div id="hid-cart-totals"></div>
                    <button type="button" id="hid-proceed-checkout" class="hid-button-primary" disabled><?php _e('Proceed to Checkout', 'hid-simple-commerce'); ?></button>
                </div>
            </div>

            <!-- Checkout Form Modal -->
            <div id="hid-checkout-modal" class="hid-modal" style="display:none;">
                <div class="hid-modal-content">
                    <span class="hid-modal-close">&times;</span>
                    <h2><?php _e('Checkout', 'hid-simple-commerce'); ?></h2>
                    
                    <form id="hid-checkout-form">
                        <div class="hid-form-section">
                            <h3><?php _e('Customer Information', 'hid-simple-commerce'); ?></h3>
                            <div class="hid-form-row">
                                <label><?php _e('Full Name *', 'hid-simple-commerce'); ?></label>
                                <input type="text" name="customer_name" required>
                            </div>
                            <div class="hid-form-row">
                                <label><?php _e('Email *', 'hid-simple-commerce'); ?></label>
                                <input type="email" name="customer_email" required>
                            </div>
                            <div class="hid-form-row">
                                <label><?php _e('Phone *', 'hid-simple-commerce'); ?></label>
                                <input type="tel" name="customer_phone" required>
                            </div>
                        </div>

                        <div class="hid-form-section">
                            <h3><?php _e('Shipping Information', 'hid-simple-commerce'); ?></h3>
                            <?php if (get_option('hid_commerce_enable_pickup', '0') === '1'): ?>
                            <div class="hid-form-row hid-delivery-methods" style="margin-bottom: 15px;">
                                <label style="display:inline-block; margin-right: 15px;">
                                    <input type="radio" name="delivery_method" value="delivery" checked> <?php _e('Delivery', 'hid-simple-commerce'); ?>
                                </label>
                                <label style="display:inline-block;">
                                    <input type="radio" name="delivery_method" value="pickup"> <?php _e('Physical Pickup', 'hid-simple-commerce'); ?>
                                </label>
                            </div>
                            <?php endif; ?>
                            
                            <div id="hid-delivery-fields">
                                <p style="font-size: 0.9em; color: #d00; margin-bottom: 10px;"><em><?php _e("We don't deliver outside Lagos State for now and delivery within Lagos takes 2 working days.", 'hid-simple-commerce'); ?></em></p>
                                <div class="hid-form-row">
                                    <label><?php _e('Shipping Address *', 'hid-simple-commerce'); ?></label>
                                    <textarea name="shipping_address" required></textarea>
                                </div>
                                <div class="hid-form-row">
                                    <label><?php _e('Location *', 'hid-simple-commerce'); ?></label>
                                    <input type="text" name="shipping_location" required>
                                </div>
                                <div class="hid-form-row">
                                    <label><?php _e('Preferred Courier *', 'hid-simple-commerce'); ?></label>
                                    <input type="text" name="shipping_courier" required>
                                </div>
                                <p class="hid-notice"><?php _e('Note: Shipping cost will be communicated to you based on your location and courier choice.', 'hid-simple-commerce'); ?></p>
                            </div>

                            <?php if (get_option('hid_commerce_enable_pickup', '0') === '1'): ?>
                            <div id="hid-pickup-details" style="display: none; background: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 10px;">
                                <strong><?php _e('Pickup Location:', 'hid-simple-commerce'); ?></strong><br>
                                <?php echo nl2br(esc_html(get_option('hid_commerce_pickup_address'))); ?>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var deliveryRadios = document.querySelectorAll('input[name="delivery_method"]');
                                    if(deliveryRadios.length === 0) return;
                                    
                                    var deliveryFields = document.getElementById('hid-delivery-fields');
                                    var pickupDetails = document.getElementById('hid-pickup-details');
                                    var shippingInputs = deliveryFields.querySelectorAll('input, textarea');
                                    
                                    function toggleFields() {
                                        var checkedRadio = document.querySelector('input[name="delivery_method"]:checked');
                                        if(!checkedRadio) return;
                                        
                                        var isDelivery = checkedRadio.value === 'delivery';
                                        
                                        if (isDelivery) {
                                            deliveryFields.style.display = 'block';
                                            pickupDetails.style.display = 'none';
                                        } else {
                                            deliveryFields.style.display = 'none';
                                            pickupDetails.style.display = 'block';
                                        }
                                        
                                        shippingInputs.forEach(function(input) {
                                            if(input.name) input.required = isDelivery;
                                        });
                                    }

                                    deliveryRadios.forEach(function(radio) {
                                        radio.addEventListener('change', toggleFields);
                                    });
                                    
                                    // Initial load
                                    toggleFields();
                                });
                            </script>
                            <?php endif; ?>
                        </div>

                        <div class="hid-form-section">
                            <h3><?php _e('Order Notes', 'hid-simple-commerce'); ?></h3>
                            <div class="hid-form-row">
                                <textarea name="order_notes" placeholder="<?php esc_attr_e('Any special instructions...', 'hid-simple-commerce'); ?>"></textarea>
                            </div>
                        </div>

                        <div class="hid-form-section">
                            <h3><?php _e('Order Summary', 'hid-simple-commerce'); ?></h3>
                            <div id="hid-checkout-order-summary"></div>
                        </div>

                        <div class="hid-form-section">
                            <h3><?php _e('Payment Method *', 'hid-simple-commerce'); ?></h3>
                            <?php
                            $payment_methods = HID_Commerce_Payments::get_enabled_methods();
                            foreach ($payment_methods as $key => $method):
                            ?>
                                <div class="hid-payment-method">
                                    <label>
                                        <input type="radio" name="payment_method" value="<?php echo esc_attr($key); ?>" required>
                                        <strong><?php echo esc_html($method['name']); ?></strong>
                                        <span class="payment-desc"><?php echo esc_html($method['description']); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <div id="hid-bank-transfer-details" style="display:none;">
                                <?php
                                $bank_details = HID_Commerce_Payments::get_bank_details();
                                if (!empty($bank_details['bank_name'])):
                                ?>
                                    <div class="hid-bank-details">
                                        <h4><?php _e('Bank Account Details', 'hid-simple-commerce'); ?></h4>
                                        <p><strong><?php _e('Bank Name:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($bank_details['bank_name']); ?></p>
                                        <p><strong><?php _e('Account Number:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($bank_details['account_number']); ?></p>
                                        <p><strong><?php _e('Account Name:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($bank_details['account_name']); ?></p>
                                        <?php if (!empty($bank_details['routing_number'])): ?>
                                            <p><strong><?php _e('Routing Number:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($bank_details['routing_number']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="hid-form-row">
                                        <label><?php _e('Upload Payment Proof', 'hid-simple-commerce'); ?></label>
                                        <input type="file" name="payment_proof" accept="image/*,application/pdf">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button type="submit" class="hid-button-primary hid-button-large"><?php _e('Place Order', 'hid-simple-commerce'); ?></button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Order confirmation shortcode
     */
    public static function order_confirmation_shortcode($atts) {
        $order_number = isset($_GET['order_number']) ? sanitize_text_field($_GET['order_number']) : '';
        
        if (empty($order_number)) {
            return '<p>' . __('Invalid order.', 'hid-simple-commerce') . '</p>';
        }

        $db = HID_Commerce_Database::get_instance();
        $order = $db->get_order_by_number($order_number);

        if (!$order) {
            return '<p>' . __('Order not found.', 'hid-simple-commerce') . '</p>';
        }

        $order_items = $db->get_order_items($order->id);

        ob_start();
        ?>
        <div class="hid-order-confirmation">
            <div class="hid-confirmation-header">
                <h2><?php _e('Thank You for Your Order!', 'hid-simple-commerce'); ?></h2>
                <p><?php _e('Your order has been received and is being processed.', 'hid-simple-commerce'); ?></p>
            </div>

            <div class="hid-order-details">
                <h3><?php _e('Order Details', 'hid-simple-commerce'); ?></h3>
                <p><strong><?php _e('Order Number:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($order->order_number); ?></p>
                <p><strong><?php _e('Order Date:', 'hid-simple-commerce'); ?></strong> <?php echo date('F j, Y, g:i a', strtotime($order->created_at)); ?></p>
                <p><strong><?php _e('Payment Method:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($order->payment_method); ?></p>
                <p><strong><?php _e('Order Status:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($order->order_status); ?></p>
            </div>

            <div class="hid-order-items">
                <h3><?php _e('Order Items', 'hid-simple-commerce'); ?></h3>
                <div class="hid-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th><?php _e('Product', 'hid-simple-commerce'); ?></th>
                            <th><?php _e('Quantity', 'hid-simple-commerce'); ?></th>
                            <th><?php _e('Price', 'hid-simple-commerce'); ?></th>
                            <th><?php _e('Subtotal', 'hid-simple-commerce'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo esc_html($item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : '')); ?></td>
                                <td><?php echo esc_html($item->quantity); ?></td>
                                <td><?php echo HID_Commerce_Checkout::format_currency($item->price); ?></td>
                                <td><?php echo HID_Commerce_Checkout::format_currency($item->subtotal); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong><?php _e('Subtotal:', 'hid-simple-commerce'); ?></strong></td>
                            <td><strong><?php echo HID_Commerce_Checkout::format_currency($order->subtotal); ?></strong></td>
                        </tr>
                        <?php if ($order->tax_amount > 0): ?>
                            <tr>
                                <td colspan="3"><strong><?php echo esc_html(get_option('hid_commerce_tax_label', 'VAT')); ?>:</strong></td>
                                <td><strong><?php echo HID_Commerce_Checkout::format_currency($order->tax_amount); ?></strong></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3"><strong><?php _e('Total:', 'hid-simple-commerce'); ?></strong></td>
                            <td><strong><?php echo HID_Commerce_Checkout::format_currency($order->total_amount); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>

            <div class="hid-tracking-info">
                <a href="<?php echo add_query_arg(array('order_number' => $order->order_number, 'email' => $order->customer_email), home_url('/track-order/')); ?>" class="hid-button-primary"><?php _e('Track Your Order', 'hid-simple-commerce'); ?></a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Order lookup shortcode
     */
    public static function order_lookup_shortcode($atts) {
        // Check both POST (form submission) and GET (email link) for parameters
        $order_number = '';
        $email = '';
        
        if (isset($_POST['order_number']) && isset($_POST['email'])) {
            // Form submission
            $order_number = sanitize_text_field($_POST['order_number']);
            $email = sanitize_email($_POST['email']);
        } elseif (isset($_GET['order_number']) && isset($_GET['email'])) {
            // Email link with parameters
            $order_number = sanitize_text_field($_GET['order_number']);
            $email = sanitize_email($_GET['email']);
        }

        ob_start();
        ?>
        <div class="hid-order-lookup">
            <?php if (!empty($order_number) && !empty($email)): ?>
                <?php
                $db = HID_Commerce_Database::get_instance();
                $order = $db->get_order_by_number_and_email($order_number, $email);

                if ($order):
                    $order_items = $db->get_order_items($order->id);
                ?>
                    <div class="hid-order-details">
                        <h2><?php _e('Order Details', 'hid-simple-commerce'); ?></h2>
                        <p><strong><?php _e('Order Number:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($order->order_number); ?></p>
                        <p><strong><?php _e('Order Status:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($order->order_status); ?></p>
                        <p><strong><?php _e('Payment Status:', 'hid-simple-commerce'); ?></strong> <?php echo esc_html($order->payment_status); ?></p>
                        <p><strong><?php _e('Order Date:', 'hid-simple-commerce'); ?></strong> <?php echo date('F j, Y', strtotime($order->created_at)); ?></p>
                        
                        <h3><?php _e('Items', 'hid-simple-commerce'); ?></h3>
                        <ul>
                            <?php foreach ($order_items as $item): ?>
                                <li><?php echo esc_html($item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : '') . ' x ' . $item->quantity); ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <p><strong><?php _e('Total:', 'hid-simple-commerce'); ?></strong> <?php echo HID_Commerce_Checkout::format_currency($order->total_amount); ?></p>
                    </div>
                <?php else: ?>
                    <p class="hid-error"><?php _e('Order not found. Please check your order number and email.', 'hid-simple-commerce'); ?></p>
                <?php endif; ?>
            <?php endif; ?>

            <form method="post" class="hid-order-lookup-form">
                <h2><?php _e('Track Your Order', 'hid-simple-commerce'); ?></h2>
                <div class="hid-form-row">
                    <label><?php _e('Order Number', 'hid-simple-commerce'); ?></label>
                    <input type="text" name="order_number" required value="<?php echo esc_attr($order_number); ?>">
                </div>
                <div class="hid-form-row">
                    <label><?php _e('Email Address', 'hid-simple-commerce'); ?></label>
                    <input type="email" name="email" required value="<?php echo esc_attr($email); ?>">
                </div>
                <button type="submit" class="hid-button-primary"><?php _e('Track Order', 'hid-simple-commerce'); ?></button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render products grid
     */
    private static function render_products_grid($products, $atts, $wrap = true) {
        if ($wrap) {
            echo '<div class="hid-products-grid columns-' . esc_attr($atts['columns']) . '">';
        }

        foreach ($products as $product) {
            self::render_product_card($product);
        }

        if ($wrap) {
            echo '</div>';
        }
    }

    /**
     * Render product card
     */
    private static function render_product_card($product) {
        $db = HID_Commerce_Database::get_instance();
        $images = $db->get_product_images($product->id);
        $primary_image = $product->image_url;
        
        // Get first image from gallery if available
        if (!empty($images)) {
            foreach ($images as $img) {
                if ($img->is_primary) {
                    $primary_image = $img->image_url;
                    break;
                }
            }
        }

        $stock_status = HID_Commerce_Inventory::get_stock_status($product->id, null);
        $is_in_stock = HID_Commerce_Inventory::is_in_stock($product->id, null, 1);
        ?>
        <div class="hid-product-card" data-product-id="<?php echo esc_attr($product->id); ?>" data-has-variants="<?php echo esc_attr($product->has_variants); ?>">
            <?php if ($primary_image): ?>
                <div class="hid-product-image">
                    <img src="<?php echo esc_url($primary_image); ?>" alt="<?php echo esc_attr($product->name); ?>">
                    <?php if ($product->featured): ?>
                        <span class="hid-featured-badge"><?php _e('Featured', 'hid-simple-commerce'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="hid-product-info">
                <h3 class="hid-product-title"><?php echo esc_html($product->name); ?></h3>
                
                <?php if (!$product->has_variants): ?>
                    <div class="hid-product-price">
                        <?php if ($product->sale_price): ?>
                            <span class="hid-price-regular"><?php echo HID_Commerce_Checkout::format_currency($product->price); ?></span>
                            <span class="hid-price-sale"><?php echo HID_Commerce_Checkout::format_currency($product->sale_price); ?></span>
                        <?php else: ?>
                            <span class="hid-price"><?php echo HID_Commerce_Checkout::format_currency($product->price); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="hid-stock-status">
                    <?php echo HID_Commerce_Inventory::get_stock_status_html($product->id, null); ?>
                </div>

                <div class="hid-product-actions">
                    <a href="<?php echo esc_url(hid_get_page_url_by_template('page-product-detail.php') . '?product_id=' . $product->id); ?>" class="hid-button-secondary" style="display: block; text-align: center; margin-bottom: 10px; width: 100%;"><?php _e('View Product', 'hid-simple-commerce'); ?></a>

                    <?php if ($product->has_variants): ?>
                        <button type="button" class="hid-button-primary hid-select-options" data-product-id="<?php echo esc_attr($product->id); ?>"><?php _e('Select Options', 'hid-simple-commerce'); ?></button>
                    <?php else: ?>
                        <?php if ($is_in_stock): ?>
                            <div class="hid-add-to-cart-wrapper">
                                <input type="number" class="hid-quantity-input" value="1" min="1" max="<?php echo esc_attr($product->stock_quantity); ?>">
                                <button type="button" class="hid-button-primary hid-add-to-cart" data-product-id="<?php echo esc_attr($product->id); ?>"><?php _e('Add to Cart', 'hid-simple-commerce'); ?></button>
                            </div>
                        <?php else: ?>
                            <button type="button" class="hid-button-disabled" disabled><?php _e('Out of Stock', 'hid-simple-commerce'); ?></button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX handler for filtering products
     */
    public static function ajax_filter_products() {
        check_ajax_referer('hid_commerce_nonce', 'nonce');

        $db = HID_Commerce_Database::get_instance();
        
        // Get filter parameters
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $category = isset($_GET['category']) ? intval($_GET['category']) : 0;
        $special_filter = isset($_GET['special_filter']) ? sanitize_text_field($_GET['special_filter']) : '';
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'created_at';
        $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = 9;
        $offset = ($page - 1) * $per_page;
        
        // Build query args with pagination
        $query_args = array(
            'status' => 'publish',
            'orderby' => $orderby,
            'order' => $order,
            'limit' => $per_page,
            'offset' => $offset,
        );
        
        // Add search filter
        if (!empty($search)) {
            $query_args['search'] = $search;
        }
        
        // Add category filter
        if ($category > 0) {
            $query_args['category_id'] = $category;
        }
        
        // Handle special filters
        if ($special_filter === 'new-arrivals') {
            $query_args['orderby'] = 'created_at';
            $query_args['order'] = 'DESC';
        } elseif ($special_filter === 'best-sellers') {
            // Get best sellers
            $best_sellers = $db->get_best_selling_products(100);
            $product_ids = array_map(function($item) { return $item->product_id; }, $best_sellers);
        }
        
        // Get products and count
        if ($special_filter === 'best-sellers' && !empty($product_ids)) {
            $all_products = array();
            foreach ($product_ids as $product_id) {
                $product = $db->get_product($product_id);
                if ($product && $product->status === 'publish') {
                    // Apply additional filters
                    if ($category > 0 && $product->category_id != $category) {
                        continue;
                    }
                    if (!empty($search) && stripos($product->name, $search) === false) {
                        continue;
                    }
                    $all_products[] = $product;
                }
            }
            $total_count = count($all_products);
            $products = array_slice($all_products, $offset, $per_page);
        } else {
            $products = $db->get_products($query_args);
            $count_args = $query_args;
            unset($count_args['limit'], $count_args['offset']);
            $total_count = $db->get_products_count($count_args);
        }
        
        $total_pages = ceil($total_count / $per_page);
        
        // Render products HTML
        ob_start();
        if (!empty($products)) {
            foreach ($products as $product) {
                self::render_product_card($product);
            }
            $html = ob_get_clean();
        } else {
            $html = '<div class="hid-no-products"><p>' . __('No products found matching your criteria.', 'hid-simple-commerce') . '</p></div>';
            ob_get_clean();
        }
        
        // Generate pagination HTML
        $pagination_html = '';
        if ($total_pages > 1) {
            ob_start();
            ?>
            <div class="hid-shop-pagination">
                <?php if ($page > 1): ?>
                    <a href="#" class="hid-page-link hid-page-nav" data-page="<?php echo ($page - 1); ?>">
                        <i class="fas fa-chevron-left"></i> <?php _e('Previous', 'hid-simple-commerce'); ?>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= min(5, $total_pages); $i++): ?>
                    <a href="#" class="hid-page-link <?php echo $i === $page ? 'current' : ''; ?>" data-page="<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($total_pages > 5): ?>
                    <span class="hid-page-ellipsis">...</span>
                <?php endif; ?>
                
                <?php if ($total_pages >= 10): ?>
                    <a href="#" class="hid-page-link <?php echo 10 === $page ? 'current' : ''; ?>" data-page="10">10</a>
                <?php endif; ?>
                
                <?php if ($total_pages >= 20): ?>
                    <a href="#" class="hid-page-link <?php echo 20 === $page ? 'current' : ''; ?>" data-page="20">20</a>
                <?php endif; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="#" class="hid-page-link hid-page-nav" data-page="<?php echo ($page + 1); ?>">
                        <?php _e('Next', 'hid-simple-commerce'); ?> <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php
            $pagination_html = ob_get_clean();
        }
        
        wp_send_json_success(array(
            'html' => $html,
            'pagination' => $pagination_html
        ));
    }
}

