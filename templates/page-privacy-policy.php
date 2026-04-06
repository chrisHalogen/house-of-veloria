<?php
/**
 * Template Name: Privacy Policy
 *
 * The privacy policy page template.
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
        <h1 class="page-hero-title"><?php _e('Privacy Policy', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('How we protect your information', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== POLICY CONTENT =========== -->
<section class="policy-section">
    <div class="section-container">
        <div class="policy-content">
            <p class="policy-updated"><?php _e('Last Updated:', 'hid-hov-theme'); ?> <?php echo date('F j, Y'); ?></p>

            <h2><?php _e('Introduction', 'hid-hov-theme'); ?></h2>
            <p><?php _e('House of Veloria ("we," "our," or "us") respects your privacy and is committed to protecting your personal data. This privacy policy explains how we collect, use, and safeguard your information when you visit our website or make a purchase.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Information We Collect', 'hid-hov-theme'); ?></h2>
            <p><?php _e('We collect information you provide directly to us, such as:', 'hid-hov-theme'); ?></p>
            <ul>
                <li><?php _e('Name, email address, phone number', 'hid-hov-theme'); ?></li>
                <li><?php _e('Shipping and billing address', 'hid-hov-theme'); ?></li>
                <li><?php _e('Payment information', 'hid-hov-theme'); ?></li>
                <li><?php _e('Order history and preferences', 'hid-hov-theme'); ?></li>
            </ul>

            <h2><?php _e('How We Use Your Information', 'hid-hov-theme'); ?></h2>
            <p><?php _e('We use the information we collect to:', 'hid-hov-theme'); ?></p>
            <ul>
                <li><?php _e('Process and fulfill your orders', 'hid-hov-theme'); ?></li>
                <li><?php _e('Send order confirmations and shipping updates', 'hid-hov-theme'); ?></li>
                <li><?php _e('Respond to your inquiries and provide customer support', 'hid-hov-theme'); ?></li>
                <li><?php _e('Send promotional communications (with your consent)', 'hid-hov-theme'); ?></li>
                <li><?php _e('Improve our website and services', 'hid-hov-theme'); ?></li>
            </ul>

            <h2><?php _e('Data Security', 'hid-hov-theme'); ?></h2>
            <p><?php _e('We implement appropriate security measures to protect your personal information. All payment transactions are encrypted using SSL technology.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Your Rights', 'hid-hov-theme'); ?></h2>
            <p><?php _e('You have the right to access, correct, or delete your personal data. Contact us at any time to exercise these rights.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Contact Us', 'hid-hov-theme'); ?></h2>
            <p><?php _e('If you have questions about this privacy policy, please contact us at:', 'hid-hov-theme'); ?></p>
            <p><?php echo esc_html(get_option('hid_commerce_contact_email', 'hello@houseofveloria.com')); ?></p>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

