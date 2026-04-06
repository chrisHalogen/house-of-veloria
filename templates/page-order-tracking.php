<?php
/**
 * Template Name: Order Tracking
 *
 * The order tracking page template.
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
<section class="page-hero" style="background-image: url('https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=1920&q=80'); min-height: 300px;">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php _e('Track Your Order', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('Enter your order details below', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== ORDER TRACKING CONTENT =========== -->
<section class="order-tracking-section">
    <div class="section-container">
        <?php echo do_shortcode('[hid_order_lookup]'); ?>
    </div>
</section>

<!-- =========== NEED HELP SECTION =========== -->
<section class="subscribe-section" style="padding: 4rem 0;">
    <div class="section-container" style="text-align: center;">
        <h2 class="section-title"><?php _e('Need Help?', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle" style="margin-bottom: 2rem;">
            <?php _e("Can't find your order? Our team is here to help.", 'hid-hov-theme'); ?>
        </p>
        <a href="<?php echo esc_url(hid_get_contact_url()); ?>" class="cta-button"><?php _e('Contact Support', 'hid-hov-theme'); ?></a>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

