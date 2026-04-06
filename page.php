<?php
/**
 * The template for displaying all pages
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
<section class="page-hero" style="min-height: 300px;">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php the_title(); ?></h1>
    </div>
</section>

<!-- =========== PAGE CONTENT =========== -->
<section class="page-content-section">
    <div class="section-container">
        <?php while (have_posts()): the_post(); ?>
            <div class="page-content">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

