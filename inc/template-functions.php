<?php

/**
 * Template Helper Functions
 *
 * Provides helper functions for use in theme templates.
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get products from database
 *
 * @param array $args Query arguments
 * @return array Array of product objects
 */
function hid_get_products($args = array())
{
    if (!class_exists('HID_Commerce_Database')) {
        return array();
    }

    $db = HID_Commerce_Database::get_instance();
    return $db->get_products($args);
}

/**
 * Get featured products
 *
 * @param int $limit Number of products to retrieve
 * @return array Array of featured product objects
 */
function hid_get_featured_products($limit = 6)
{
    return hid_get_products(array(
        'limit' => $limit,
        'featured' => 1,
        'status' => 'publish',
    ));
}

/**
 * Get best selling products
 *
 * @param int $limit Number of products to retrieve
 * @return array Array of best selling product data
 */
function hid_get_best_sellers($limit = 6)
{
    if (!class_exists('HID_Commerce_Database')) {
        return array();
    }

    $db = HID_Commerce_Database::get_instance();
    return $db->get_best_selling_products($limit);
}

/**
 * Get new arrivals (recently added products)
 *
 * @param int $limit Number of products to retrieve
 * @return array Array of product objects
 */
function hid_get_new_arrivals($limit = 6)
{
    return hid_get_products(array(
        'limit' => $limit,
        'status' => 'publish',
        'orderby' => 'created_at',
        'order' => 'DESC',
    ));
}

/**
 * Get product categories
 *
 * @param int|null $parent_id Parent category ID (null for all)
 * @return array Array of category objects
 */
function hid_get_categories($parent_id = null)
{
    if (!class_exists('HID_Commerce_Database')) {
        return array();
    }

    $db = HID_Commerce_Database::get_instance();
    return $db->get_categories($parent_id);
}

/**
 * Get single product by ID
 *
 * @param int $product_id Product ID
 * @return object|null Product object or null
 */
function hid_get_product($product_id)
{
    if (!class_exists('HID_Commerce_Database')) {
        return null;
    }

    $db = HID_Commerce_Database::get_instance();
    return $db->get_product($product_id);
}

/**
 * Get product images
 *
 * @param int $product_id Product ID
 * @return array Array of image objects
 */
function hid_get_product_images($product_id)
{
    if (!class_exists('HID_Commerce_Database')) {
        return array();
    }

    $db = HID_Commerce_Database::get_instance();
    return $db->get_product_images($product_id);
}

/**
 * Format currency
 *
 * @param float $amount Amount to format
 * @return string Formatted currency string
 */
function hid_format_currency($amount)
{
    if (class_exists('HID_Commerce_Checkout')) {
        return HID_Commerce_Checkout::format_currency($amount);
    }

    $symbol = get_option('hid_commerce_currency_symbol', '₦');
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

/**
 * Get cart count
 *
 * @return int Number of items in cart
 */
function hid_get_cart_count()
{
    if (class_exists('HID_Commerce_Session')) {
        return HID_Commerce_Session::get_cart_count();
    }
    return 0;
}

/**
 * Get cart items
 *
 * @return array Array of cart items
 */
function hid_get_cart_items()
{
    if (class_exists('HID_Commerce_Session')) {
        return HID_Commerce_Session::get_cart_items();
    }
    return array();
}

/**
 * Get cart totals
 *
 * @return array Array of cart totals
 */
function hid_get_cart_totals()
{
    if (class_exists('HID_Commerce_Session')) {
        return HID_Commerce_Session::calculate_totals();
    }
    return array(
        'subtotal' => 0,
        'tax_amount' => 0,
        'total' => 0,
        'tax_label' => 'VAT',
        'currency' => 'USD',
    );
}

/**
 * Check if product is in stock
 *
 * @param int $product_id Product ID
 * @param int|null $variant_id Variant ID
 * @param int $quantity Quantity to check
 * @return bool True if in stock
 */
function hid_is_in_stock($product_id, $variant_id = null, $quantity = 1)
{
    if (class_exists('HID_Commerce_Inventory')) {
        return HID_Commerce_Inventory::is_in_stock($product_id, $variant_id, $quantity);
    }
    return true;
}

/**
 * Get stock status HTML
 *
 * @param int $product_id Product ID
 * @param int|null $variant_id Variant ID
 * @return string HTML string for stock status
 */
function hid_get_stock_status_html($product_id, $variant_id = null)
{
    if (class_exists('HID_Commerce_Inventory')) {
        return HID_Commerce_Inventory::get_stock_status_html($product_id, $variant_id);
    }
    return '<span class="in-stock">' . __('In Stock', 'hid-hov-theme') . '</span>';
}

/**
 * Render product card HTML
 *
 * @param object $product Product object
 * @param string $badge_text Optional badge text (e.g., "New", "Featured")
 * @return void
 */
function hid_render_product_card($product, $badge_text = '')
{
    if (!class_exists('HID_Commerce_Database')) {
        return;
    }

    $db = HID_Commerce_Database::get_instance();
    $images = $db->get_product_images($product->id);
    $primary_image = $product->image_url;

    // Get primary image from gallery if available
    if (!empty($images)) {
        foreach ($images as $img) {
            if ($img->is_primary) {
                $primary_image = $img->image_url;
                break;
            }
        }
    }

    $is_in_stock = hid_is_in_stock($product->id, null, 1);
?>
    <div class="product-card" data-product-id="<?php echo esc_attr($product->id); ?>" data-has-variants="<?php echo esc_attr($product->has_variants); ?>">
        <div class="product-card-image">
            <?php if ($primary_image): ?>
                <img src="<?php echo esc_url($primary_image); ?>" alt="<?php echo esc_attr($product->name); ?>" />
            <?php endif; ?>
            <?php if ($badge_text): ?>
                <span class="product-card-badge"><?php echo esc_html($badge_text); ?></span>
            <?php elseif ($product->featured): ?>
                <span class="product-card-badge"><?php _e('Featured', 'hid-hov-theme'); ?></span>
            <?php endif; ?>
        </div>
        <div class="product-card-info">
            <h3 class="product-card-title"><?php echo esc_html($product->name); ?></h3>
            <?php if (!$product->has_variants): ?>
                <span class="product-card-price">
                    <?php if ($product->sale_price): ?>
                        <span class="price-regular"><?php echo hid_format_currency($product->price); ?></span>
                        <span class="price-sale"><?php echo hid_format_currency($product->sale_price); ?></span>
                    <?php else: ?>
                        <?php echo hid_format_currency($product->price); ?>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
            
            <div class="product-card-actions" style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                <?php 
                $product_url = hid_get_page_url_by_template('page-product-detail.php') . '?product_id=' . $product->id;
                ?>
                <a href="<?php echo esc_url($product_url); ?>" class="cta-button" style="padding: 8px 15px; font-size: 0.8rem; flex: 1; text-align: center;"><?php _e('View', 'hid-hov-theme'); ?></a>
                
                <?php if ($product->has_variants): ?>
                    <button type="button" class="cta-button hid-select-options" data-product-id="<?php echo esc_attr($product->id); ?>" style="padding: 8px 15px; font-size: 0.8rem; flex: 1;"><?php _e('Options', 'hid-hov-theme'); ?></button>
                <?php else: ?>
                    <?php if ($is_in_stock): ?>
                        <button type="button" class="cta-button hid-add-to-cart" data-product-id="<?php echo esc_attr($product->id); ?>" style="padding: 8px 15px; font-size: 0.8rem; flex: 1;"><?php _e('Add To Cart', 'hid-hov-theme'); ?></button>
                    <?php else: ?>
                        <button type="button" class="cta-button" disabled style="padding: 8px 15px; font-size: 0.8rem; opacity: 0.6; cursor: not-allowed; flex: 1;"><?php _e('Sold', 'hid-hov-theme'); ?></button>
                    <?php endif; ?>
                <?php endif; ?>
                <input type="hidden" class="hid-quantity-input" value="1">
            </div>
        </div>
    </div>
<?php
}

/**
 * Render product card for shop page (with add to cart)
 *
 * @param object $product Product object
 * @return void
 */
function hid_render_shop_product_card($product)
{
    if (!class_exists('HID_Commerce_Database') || !class_exists('HID_Commerce_Inventory')) {
        return;
    }

    $db = HID_Commerce_Database::get_instance();
    $images = $db->get_product_images($product->id);
    $primary_image = $product->image_url;

    // Get primary image from gallery if available
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
                    <span class="hid-featured-badge"><?php _e('Featured', 'hid-hov-theme'); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="hid-product-info">
            <h3 class="hid-product-title"><?php echo esc_html($product->name); ?></h3>

            <?php if (!$product->has_variants): ?>
                <div class="hid-product-price">
                    <?php if ($product->sale_price): ?>
                        <span class="hid-price-regular"><?php echo hid_format_currency($product->price); ?></span>
                        <span class="hid-price-sale"><?php echo hid_format_currency($product->sale_price); ?></span>
                    <?php else: ?>
                        <span class="hid-price"><?php echo hid_format_currency($product->price); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="hid-stock-status">
                <?php echo HID_Commerce_Inventory::get_stock_status_html($product->id, null); ?>
            </div>

            <div class="hid-product-actions">
                <a href="<?php echo esc_url(hid_get_page_url_by_template('page-product-detail.php') . '?product_id=' . $product->id); ?>" class="hid-button-secondary" style="display: block; text-align: center; margin-bottom: 10px;"><?php _e('View Product', 'hid-hov-theme'); ?></a>
                
                <?php if ($product->has_variants): ?>
                    <button type="button" class="hid-button-primary hid-select-options" data-product-id="<?php echo esc_attr($product->id); ?>"><?php _e('Select Options', 'hid-hov-theme'); ?></button>
                <?php else: ?>
                    <?php if ($is_in_stock): ?>
                        <div class="hid-add-to-cart-wrapper">
                            <input type="number" class="hid-quantity-input" value="1" min="1" max="<?php echo esc_attr($product->stock_quantity); ?>">
                            <button type="button" class="hid-button-primary hid-add-to-cart" data-product-id="<?php echo esc_attr($product->id); ?>"><?php _e('Add to Cart', 'hid-hov-theme'); ?></button>
                        </div>
                    <?php else: ?>
                        <button type="button" class="hid-button-disabled" disabled><?php _e('Out of Stock', 'hid-hov-theme'); ?></button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
}

/**
 * Get page URL by template
 *
 * @param string $template Template filename
 * @return string Page URL or home URL if not found
 */
function hid_get_page_url_by_template($template)
{
    $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'templates/' . $template,
    ));

    if (!empty($pages)) {
        return get_permalink($pages[0]->ID);
    }

    return home_url('/');
}

/**
 * Get shop page URL
 *
 * @return string Shop page URL
 */
function hid_get_shop_url()
{
    return hid_get_page_url_by_template('page-shop.php');
}

/**
 * Get contact page URL
 *
 * @return string Contact page URL
 */
function hid_get_contact_url()
{
    return hid_get_page_url_by_template('page-contact.php');
}

/**
 * Get story page URL
 *
 * @return string Story page URL
 */
function hid_get_story_url()
{
    return hid_get_page_url_by_template('page-curation-story.php');
}

/**
 * Display navigation menu with fallback
 *
 * @param string $theme_location Menu location
 * @param array $args Additional menu arguments
 * @return void
 */
function hid_display_nav_menu($theme_location, $args = array())
{
    $defaults = array(
        'theme_location' => $theme_location,
        'container' => false,
        'menu_class' => 'nav-links',
        'fallback_cb' => 'hid_nav_menu_fallback',
        'fallback_location' => $theme_location,
    );

    $args = wp_parse_args($args, $defaults);

    wp_nav_menu($args);
}

/**
 * Navigation menu fallback
 *
 * @param array $args Menu arguments
 * @return void
 */
function hid_nav_menu_fallback($args)
{
    $location = isset($args['fallback_location']) ? $args['fallback_location'] : 'primary';

    echo '<ul class="' . esc_attr($args['menu_class']) . '">';

    switch ($location) {
        case 'primary':
            echo '<li><a href="' . home_url('/') . '" class="nav-link">' . __('Home', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_shop_url() . '" class="nav-link">' . __('Shop', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_story_url() . '" class="nav-link">' . __('Story', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_contact_url() . '" class="nav-link">' . __('Contact', 'hid-hov-theme') . '</a></li>';
            break;

        case 'footer-links':
            echo '<li><a href="' . home_url('/') . '">' . __('Home', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_shop_url() . '">' . __('Shop', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_story_url() . '">' . __('Our Story', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_page_url_by_template('page-testimonials.php') . '">' . __('Testimonials', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_contact_url() . '">' . __('Contact', 'hid-hov-theme') . '</a></li>';
            break;

        case 'footer-care':
            $shopping_guide_url = hid_get_page_url_by_template('page-shopping-guide.php');
            echo '<li><a href="' . $shopping_guide_url . '">' . __('Shopping Guide', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . $shopping_guide_url . '#sizing">' . __('Sizing Guide', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . $shopping_guide_url . '#shipping">' . __('Shipping Info', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . $shopping_guide_url . '#returns">' . __('Returns', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . $shopping_guide_url . '#faq">' . __('FAQs', 'hid-hov-theme') . '</a></li>';
            break;

        case 'footer-policies':
            echo '<li><a href="' . hid_get_page_url_by_template('page-privacy-policy.php') . '">' . __('Privacy Policy', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_page_url_by_template('page-terms.php') . '">' . __('Terms & Conditions', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_page_url_by_template('page-refund-policy.php') . '">' . __('Refund Policy', 'hid-hov-theme') . '</a></li>';
            echo '<li><a href="' . hid_get_page_url_by_template('page-shipping-policy.php') . '">' . __('Shipping Policy', 'hid-hov-theme') . '</a></li>';
            break;
    }

    echo '</ul>';
}

/**
 * Get category product count
 *
 * @param int $category_id Category ID
 * @return int Product count
 */
function hid_get_category_product_count($category_id)
{
    if (!class_exists('HID_Commerce_Database')) {
        return 0;
    }

    $db = HID_Commerce_Database::get_instance();
    return $db->get_category_product_count($category_id);
}

/**
 * Check if current page is shop page
 *
 * @return bool True if on shop page
 */
function hid_is_shop_page()
{
    return is_page_template('templates/page-shop.php');
}

/**
 * Check if current page is checkout page
 *
 * @return bool True if on checkout
 */
function hid_is_checkout()
{
    return hid_is_shop_page() && isset($_GET['checkout']);
}

/**
 * Get products with category distribution limit
 * Ensures no more than X products per category
 *
 * @param array $args Query arguments
 * @param int $max_per_category Maximum products per category
 * @param array $exclude_ids Product IDs to exclude
 * @return array Array of product objects
 */
function hid_get_products_with_category_limit($args = array(), $max_per_category = 3, $exclude_ids = array())
{
    if (!class_exists('HID_Commerce_Database')) {
        return array();
    }

    $db = HID_Commerce_Database::get_instance();

    // Get more products than needed to allow for category filtering
    $fetch_args = $args;
    $total_limit = isset($args['limit']) ? $args['limit'] : 18;
    $fetch_args['limit'] = $total_limit * 3; // Fetch 3x to ensure we have enough after filtering

    $all_products = $db->get_products($fetch_args);

    if (empty($all_products)) {
        return array();
    }

    // Filter out excluded IDs
    if (!empty($exclude_ids)) {
        $all_products = array_filter($all_products, function ($product) use ($exclude_ids) {
            return !in_array($product->id, $exclude_ids);
        });
    }

    // Distribute products by category
    $category_counts = array();
    $result = array();

    foreach ($all_products as $product) {
        $cat_id = $product->category_id ? $product->category_id : 0;

        if (!isset($category_counts[$cat_id])) {
            $category_counts[$cat_id] = 0;
        }

        if ($category_counts[$cat_id] < $max_per_category) {
            $result[] = $product;
            $category_counts[$cat_id]++;

            if (count($result) >= $total_limit) {
                break;
            }
        }
    }

    return $result;
}

/**
 * Get featured products with category distribution (max 2 per category)
 *
 * @param int $limit Total number of products
 * @return array Array of featured product objects
 */
function hid_get_featured_products_distributed($limit = 6)
{
    return hid_get_products_with_category_limit(array(
        'limit' => $limit,
        'featured' => 1,
        'status' => 'publish',
        'orderby' => 'created_at',
        'order' => 'DESC',
    ), 2); // Max 2 per category
}

/**
 * Get new arrivals with category distribution (max 3 per category)
 *
 * @param int $limit Total number of products
 * @return array Array of product objects
 */
function hid_get_new_arrivals_distributed($limit = 6)
{
    return hid_get_products_with_category_limit(array(
        'limit' => $limit,
        'status' => 'publish',
        'orderby' => 'created_at',
        'order' => 'DESC',
    ), 3); // Max 3 per category
}

/**
 * Get best sellers or latest products with category distribution (max 3 per category)
 * Excludes products already shown in new arrivals
 *
 * @param int $limit Total number of products (default 15)
 * @param array $exclude_ids Product IDs to exclude
 * @return array Array of product objects
 */
function hid_get_best_sellers_distributed($limit = 15, $exclude_ids = array())
{
    if (!class_exists('HID_Commerce_Database')) {
        return array();
    }

    $db = HID_Commerce_Database::get_instance();
    $result = array();
    $result_ids = array();

    // Step 1: Get actual top-selling products (from orders)
    $best_sellers = $db->get_best_selling_products($limit * 2); // Get more than needed

    if (!empty($best_sellers)) {
        foreach ($best_sellers as $product_data) {
            // Skip if in exclude list or already added
            if (in_array($product_data->product_id, $exclude_ids) || in_array($product_data->product_id, $result_ids)) {
                continue;
            }

            // Get full product details
            $product = $db->get_product($product_data->product_id);
            if ($product && $product->status === 'publish') {
                $result[] = $product;
                $result_ids[] = $product->id;

                if (count($result) >= $limit) {
                    return $result;
                }
            }
        }
    }

    // Step 2: If we don't have enough products, fill with category-distributed products
    if (count($result) < $limit) {
        $remaining = $limit - count($result);
        $all_exclude_ids = array_merge($exclude_ids, $result_ids);

        // Get products distributed across categories (3 per category)
        $filler_products = hid_get_products_with_category_limit(array(
            'limit' => $remaining * 2, // Get more to ensure we have enough after filtering
            'status' => 'publish',
            'orderby' => 'created_at',
            'order' => 'DESC',
        ), 3, $all_exclude_ids); // Max 3 per category

        foreach ($filler_products as $product) {
            if (!in_array($product->id, $result_ids)) {
                $result[] = $product;
                $result_ids[] = $product->id;

                if (count($result) >= $limit) {
                    break;
                }
            }
        }
    }

    return $result;
}
