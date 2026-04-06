<?php

/**
 * Template Name: Shop Page
 *
 * The shop page template using plugin's shop functionality.
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

get_template_part('template-parts/header');

// Prevent page caching for non-logged-in users (helps with cache issues)
if (!is_user_logged_in()) {
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
}

// Hero image
$shop_hero_image = get_option('hid_commerce_shop_hero_image', 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=1920&q=80');
?>

<!-- =========== PAGE HERO =========== -->
<section class="page-hero" style="background-image: url('<?php echo esc_url($shop_hero_image); ?>');">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php _e('Our Collection', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('Curated elegance awaits', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== SHOP SECTION =========== -->
<?php
// Use the plugin's shop shortcode functionality
echo do_shortcode('[hid_shop]');
?>

<?php get_template_part('template-parts/footer'); ?>