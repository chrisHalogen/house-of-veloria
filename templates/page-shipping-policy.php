<?php
/**
 * Template Name: Shipping Policy
 *
 * The shipping policy page template.
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
        <h1 class="page-hero-title"><?php _e('Shipping Policy', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('Delivery information and timeframes', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== POLICY CONTENT =========== -->
<section class="policy-section">
    <div class="section-container">
        <div class="policy-content">
            <p class="policy-updated"><?php _e('Last Updated:', 'hid-hov-theme'); ?> <?php echo date('F j, Y'); ?></p>

            <h2><?php _e('Processing Time', 'hid-hov-theme'); ?></h2>
            <p><?php _e('Orders are typically processed within 1-2 business days. During peak seasons, processing may take up to 3-5 business days.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Domestic Shipping (United States)', 'hid-hov-theme'); ?></h2>
            <table class="shipping-table">
                <thead>
                    <tr>
                        <th><?php _e('Method', 'hid-hov-theme'); ?></th>
                        <th><?php _e('Delivery Time', 'hid-hov-theme'); ?></th>
                        <th><?php _e('Cost', 'hid-hov-theme'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Standard Shipping', 'hid-hov-theme'); ?></td>
                        <td><?php _e('5-7 business days', 'hid-hov-theme'); ?></td>
                        <td><?php _e('$9.99 (Free over $200)', 'hid-hov-theme'); ?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Express Shipping', 'hid-hov-theme'); ?></td>
                        <td><?php _e('2-3 business days', 'hid-hov-theme'); ?></td>
                        <td>$19.99</td>
                    </tr>
                    <tr>
                        <td><?php _e('Overnight Shipping', 'hid-hov-theme'); ?></td>
                        <td><?php _e('Next business day', 'hid-hov-theme'); ?></td>
                        <td>$39.99</td>
                    </tr>
                </tbody>
            </table>

            <h2><?php _e('International Shipping', 'hid-hov-theme'); ?></h2>
            <p><?php _e('We ship to most countries worldwide. International shipping typically takes 7-14 business days depending on the destination.', 'hid-hov-theme'); ?></p>
            <p><?php _e('International customers are responsible for any customs duties or import taxes that may apply.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Order Tracking', 'hid-hov-theme'); ?></h2>
            <p><?php _e('Once your order ships, you will receive an email with tracking information. You can also track your order using our order tracking page.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Shipping Insurance', 'hid-hov-theme'); ?></h2>
            <p><?php _e('All orders are fully insured during transit at no additional cost. If your package is lost or damaged, please contact us immediately.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Delivery Signature', 'hid-hov-theme'); ?></h2>
            <p><?php _e('For orders over $500, a signature will be required upon delivery to ensure secure receipt.', 'hid-hov-theme'); ?></p>

            <h2><?php _e('Contact Us', 'hid-hov-theme'); ?></h2>
            <p><?php _e('For shipping inquiries, please contact us at:', 'hid-hov-theme'); ?></p>
            <p><?php echo esc_html(get_option('hid_commerce_contact_email', 'hello@houseofveloria.com')); ?></p>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

