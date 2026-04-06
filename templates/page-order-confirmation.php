<?php
/**
 * Template Name: Order Confirmation
 *
 * The order confirmation page template.
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

get_template_part('template-parts/header');
?>

<!-- =========== PAGE HERO =========== -->
<section class="page-hero" style="background-image: url('https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=1920&q=80'); min-height: 300px;">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php _e('Order Confirmation', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('Thank you for your order', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== ORDER CONFIRMATION CONTENT =========== -->
<section class="order-confirmation-section">
    <div class="section-container">
        <?php echo do_shortcode('[hid_order_confirmation]'); ?>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo esc_url(hid_get_shop_url()); ?>" class="cta-button"><?php _e('Continue Shopping', 'hid-hov-theme'); ?></a>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

