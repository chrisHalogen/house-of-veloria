<?php
/**
 * Product Card Template Part
 *
 * Used for displaying product cards on homepage and other areas.
 *
 * @package HID_HOV_Theme
 * 
 * Variables expected:
 * @var object $product Product object from database
 * @var string $badge_text Optional badge text (e.g., "New", "Featured")
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Ensure we have a product
if (empty($product)) {
    return;
}

// Get product image
$primary_image = $product->image_url;

// Get primary image from gallery if available
if (class_exists('HID_Commerce_Database')) {
    $db = HID_Commerce_Database::get_instance();
    $images = $db->get_product_images($product->id);
    
    if (!empty($images)) {
        foreach ($images as $img) {
            if ($img->is_primary) {
                $primary_image = $img->image_url;
                break;
            }
        }
    }
}

// Set default badge text
if (empty($badge_text) && $product->featured) {
    $badge_text = __('Featured', 'hid-hov-theme');
}
?>

<div class="product-card" data-product-id="<?php echo esc_attr($product->id); ?>">
    <div class="product-card-image">
        <?php if ($primary_image): ?>
            <img src="<?php echo esc_url($primary_image); ?>" alt="<?php echo esc_attr($product->name); ?>" />
        <?php endif; ?>
        <?php if (!empty($badge_text)): ?>
            <span class="product-card-badge"><?php echo esc_html($badge_text); ?></span>
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
    </div>
</div>

