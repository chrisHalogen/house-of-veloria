<?php
/**
 * Template Name: Refund Policy
 *
 * The refund policy page template.
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
<section class="page-hero policy-hero" style="min-height: 300px;">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php _e('Refund Policy', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('Our commitment to your satisfaction', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== POLICY CONTENT =========== -->
<section class="policy-section">
    <div class="section-container">
        <div class="policy-content">
            <p class="policy-updated"><?php _e('Last Updated:', 'hid-hov-theme'); ?> <?php echo date('F j, Y'); ?></p>

            <h2><?php _e('30-Day Return Policy', 'hid-hov-theme'); ?></h2>
            <p><?php _e('We offer a 30-day return policy from the date of delivery. To be eligible for a return, items must be unworn, in original condition, and include all original packaging and tags.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('How to Request a Refund', 'hid-hov-theme'); ?></h2>
            <ol>
                <li><?php _e('Contact our customer service team within 30 days of receiving your order', 'hid-hov-theme'); ?></li>
                <li><?php _e('Provide your order number and reason for return', 'hid-hov-theme'); ?></li>
                <li><?php _e('Receive a prepaid return shipping label', 'hid-hov-theme'); ?></li>
                <li><?php _e('Ship the item back in its original packaging', 'hid-hov-theme'); ?></li>
            </ol>

            <h2><?php _e('Refund Processing', 'hid-hov-theme'); ?></h2>
            <p><?php _e('Once we receive your returned item, we will inspect it and notify you of the status of your refund. If approved, your refund will be processed within 5-7 business days to your original payment method.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Non-Returnable Items', 'hid-hov-theme'); ?></h2>
            <ul>
                <li><?php _e('Custom or personalized items', 'hid-hov-theme'); ?></li>
                <li><?php _e('Items marked as final sale', 'hid-hov-theme'); ?></li>
                <li><?php _e('Items showing signs of wear or damage', 'hid-hov-theme'); ?></li>
            </ul>

            <h2><?php _e('Exchanges', 'hid-hov-theme'); ?></h2>
            <p><?php _e('We offer free exchanges for different sizes within 30 days. Contact us to arrange an exchange.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Damaged or Defective Items', 'hid-hov-theme'); ?></h2>
            <p><?php _e('If you receive a damaged or defective item, please contact us immediately with photos. We will arrange a replacement or full refund at no cost to you.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Contact Us', 'hid-hov-theme'); ?></h2>
            <p><?php _e('For refund inquiries, please contact us at:', 'hid-hov-theme'); ?></p>
            <p><?php echo esc_html(get_option('hid_commerce_contact_email', 'hello@houseofveloria.com')); ?></p>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

