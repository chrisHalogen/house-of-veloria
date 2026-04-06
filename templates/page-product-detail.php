<?php
/**
 * Template Name: Product Detail
 *
 * The product detail page template.
 *
 * @package HID_HOV_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_template_part('template-parts/header');

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$product = $product_id ? hid_get_product($product_id) : null;
$shop_url = hid_get_shop_url();

// Handle Error State
if (!$product || $product->status !== 'publish') {
    ?>
    <section class="section-container" style="padding: 100px 20px; text-align: center;">
        <h1 style="color: var(--velvet-rouge); margin-bottom: 20px;">Product Not Found</h1>
        <p>We couldn't find the product you're looking for. It may have been removed or the link is incorrect.</p>
        <p id="redirect-message" style="margin-top: 20px; font-weight: bold;">
            Redirecting to Shop in <span id="countdown">5</span> seconds...
        </p>
        <a href="<?php echo esc_url($shop_url); ?>" class="cta-button" style="margin-top: 20px;">Go to Shop Now</a>
    </section>

    <script>
        var countdown = 5;
        var timer = setInterval(function() {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = "<?php echo esc_url_raw($shop_url); ?>";
            }
        }, 1000);
    </script>
    <?php
    get_template_part('template-parts/footer');
    return; // Stop execution
}

// Product Found
$images = hid_get_product_images($product->id);
$primary_image = $product->image_url;
// Find primary image object if using gallery logic
if (!empty($images)) {
    foreach ($images as $img) {
        if ($img->is_primary) $primary_image = $img->image_url;
    }
}

// Related Products (Same category, exclude current)
$related_products = hid_get_products_with_category_limit(array(
    'category_id' => $product->category_id,
    'limit' => 4,
    'orderby' => 'created_at',
    'order' => 'DESC'
), 4, array($product->id));

// Stock Status
$is_in_stock = hid_is_in_stock($product->id, null, 1);
?>

<div class="product-detail-container" style="padding-top: 40px; padding-bottom: 80px;">
    <div class="section-container">
        <!-- Breadcrumbs (Optional but good) -->
        <div class="breadcrumbs" style="margin-bottom: 30px; font-size: 0.9em; color: #666;padding-top:5rem">
            <a href="<?php echo home_url('/'); ?>" style="font-weight: 600;">Home</a> / 
            <a href="<?php echo esc_url($shop_url); ?>" style="font-weight: 600;">Shop</a> / 
            <span><?php echo esc_html($product->name); ?></span>
        </div>

        <div class="product-layout" style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
            <!-- Gallery Section -->
            <div class="product-gallery">
                <div class="main-image" style="margin-bottom: 20px; border: 1px solid #eee;">
                    <img src="<?php echo esc_url($primary_image); ?>" alt="<?php echo esc_attr($product->name); ?>" id="main-product-image" style="width: 100%; height: 600px; object-fit: cover; display: block;">
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="gallery-thumbnails" style="display: flex; gap: 10px; overflow-x: auto;">
                        <?php foreach ($images as $img): ?>
                            <div class="thumbnail" style="width: 80px; height: 80px; cursor: pointer; border: 1px solid #ddd;" onclick="document.getElementById('main-product-image').src='<?php echo esc_url($img->image_url); ?>'">
                                <img src="<?php echo esc_url($img->image_url); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info Section -->
            <div class="product-info-column">
                <h1 class="product-title" style="font-family: var(--font-header); font-size: 3rem; color: var(--velvet-rouge); margin-bottom: 15px;"><?php echo esc_html($product->name); ?></h1>
                
                <div class="product-price" style="font-size: 1.5rem; color: var(--gold-warm); margin-bottom: 20px; font-weight: 600;">
                    <?php if ($product->sale_price): ?>
                        <span style="text-decoration: line-through; opacity: 0.6; margin-right: 10px; font-size: 0.8em;"><?php echo hid_format_currency($product->price); ?></span>
                        <span style="color: #d63638;"><?php echo hid_format_currency($product->sale_price); ?></span>
                    <?php else: ?>
                        <?php echo hid_format_currency($product->price); ?>
                    <?php endif; ?>
                </div>

                <div class="product-description" style="line-height: 1.6; margin-bottom: 30px; color: #333;">
                    <?php echo wp_kses_post($product->description); ?>
                </div>

                <!-- Add to Cart / Options -->
                <div class="product-actions-area" style="margin-bottom: 30px; padding: 20px; background: var(--cream-linen); border-radius: 4px;">
                    <?php if ($product->has_variants): ?>
                        <div class="variant-message" style="margin-bottom: 15px;">
                            <p>This product has options. Please select:</p>
                        </div>
                        <button type="button" class="cta-button hid-select-options" data-product-id="<?php echo esc_attr($product->id); ?>" style="width: 100%;">Select Options</button>
                    <?php else: ?>
                        <?php if ($is_in_stock): ?>
                            <div class="hid-add-to-cart-wrapper" style="display: flex; gap: 15px;">
                                <input type="number" class="hid-quantity-input" value="1" min="1" max="<?php echo esc_attr($product->stock_quantity); ?>" style="width: 80px; padding: 10px; border: 1px solid #ccc;">
                                <button type="button" class="cta-button hid-add-to-cart" data-product-id="<?php echo esc_attr($product->id); ?>" style="flex: 1;">Add to Cart</button>
                            </div>
                            <p class="stock-status in-stock" style="margin-top: 10px; color: #46b450;"><small>In Stock (<?php echo $product->stock_quantity; ?> available)</small></p>
                        <?php else: ?>
                            <button type="button" class="cta-button disabled" disabled style="width: 100%; opacity: 0.6; cursor: not-allowed;">Out of Stock</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div class="product-meta" style="font-size: 0.9em; color: #777; border-top: 1px solid #eee; padding-top: 20px;">
                    <p>SKU: <?php echo esc_html($product->sku); ?></p>
                    <p>Category: <?php 
                        $cat = $product->category_id ? (class_exists('HID_Commerce_Database') ? HID_Commerce_Database::get_instance()->get_category($product->category_id) : null) : null;
                        echo $cat ? esc_html($cat->name) : 'Uncategorized';
                    ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<section class="related-products-section" style="background-color: var(--cream-linen); padding: 60px 0;">
    <div class="section-container">
        <h2 class="section-title" style="font-size: 2rem;"><?php _e('Related Products', 'hid-hov-theme'); ?></h2>
        <?php if (!empty($related_products)): ?>
            <div class="products-grid">
                <?php foreach ($related_products as $r_product): ?>
                    <?php hid_render_product_card($r_product); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center;">No related products found.</p>
        <?php endif; ?>
    </div>
</section>

<style>
/* Responsive layout for product page */
@media (max-width: 768px) {
    .product-layout {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php get_template_part('template-parts/footer'); ?>
