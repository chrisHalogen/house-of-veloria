<?php
/**
 * Template Name: Terms & Conditions
 *
 * The terms and conditions page template.
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
        <h1 class="page-hero-title"><?php _e('Terms & Conditions', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('Please read these terms carefully', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== POLICY CONTENT =========== -->
<section class="policy-section">
    <div class="section-container">
        <div class="policy-content">
            <p class="policy-updated"><?php _e('Last Updated:', 'hid-hov-theme'); ?> <?php echo date('F j, Y'); ?></p>

            <h2><?php _e('Agreement to Terms', 'hid-hov-theme'); ?></h2>
            <p><?php _e('By accessing or using our website, you agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree with any part of these terms, you may not access our website or use our services.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Products and Services', 'hid-hov-theme'); ?></h2>
            <p><?php _e('All products displayed on our website are subject to availability. We reserve the right to limit quantities and discontinue products at any time.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Pricing and Payment', 'hid-hov-theme'); ?></h2>
            <p><?php _e('All prices are displayed in USD unless otherwise stated. We reserve the right to change prices at any time without notice. Payment must be received in full before orders are processed.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Shipping and Delivery', 'hid-hov-theme'); ?></h2>
            <p><?php _e('Delivery times are estimates and not guaranteed. We are not responsible for delays caused by carriers or customs processing.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Returns and Refunds', 'hid-hov-theme'); ?></h2>
            <p><?php _e('Please refer to our Refund Policy for detailed information about returns, exchanges, and refunds.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Intellectual Property', 'hid-hov-theme'); ?></h2>
            <p><?php _e('All content on this website, including images, text, and logos, is the property of House of Veloria and is protected by copyright laws.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Limitation of Liability', 'hid-hov-theme'); ?></h2>
            <p><?php _e('House of Veloria shall not be liable for any indirect, incidental, or consequential damages arising from the use of our website or products.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Contact Information', 'hid-hov-theme'); ?></h2>
            <p><?php _e('For questions about these terms, please contact us at:', 'hid-hov-theme'); ?></p>
            <p><?php echo esc_html(get_option('hid_commerce_contact_email', 'hello@houseofveloria.com')); ?></p>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

