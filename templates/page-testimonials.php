<?php
/**
 * Template Name: Testimonials
 *
 * The testimonials page template.
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
<section class="page-hero" style="background-image: url('https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=1920&q=80');">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php _e('Client Stories', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('What our clients say about their Veloria experience', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== TESTIMONIALS GRID =========== -->
<section class="collection-section">
    <div class="section-container">
        <h2 class="section-title"><?php _e('Customer Reviews', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('Real stories from our valued clients', 'hid-hov-theme'); ?></p>
        
        <div class="testimonials-grid">
            <div class="testimonial-item">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">
                    "<?php _e("The craftsmanship is simply unparalleled. Each piece feels like a work of art, a modern heirloom I'll treasure forever.", 'hid-hov-theme'); ?>"
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Amina Suleiman</span>
                    <span class="author-location">Lagos, Nigeria</span>
                </div>
            </div>

            <div class="testimonial-item">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">
                    "<?php _e("I've been searching for a brand that captures both classic elegance and a modern feel. House of Veloria is it.", 'hid-hov-theme'); ?>"
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Chinedu Okonkwo</span>
                    <span class="author-location">Abuja, Nigeria</span>
                </div>
            </div>

            <div class="testimonial-item">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">
                    "<?php _e('From the moment I saw the collection, I knew this was special. The attention to detail is breathtaking.', 'hid-hov-theme'); ?>"
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Funmilayo Adebayo</span>
                    <span class="author-location">Port Harcourt, Nigeria</span>
                </div>
            </div>

            <div class="testimonial-item">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">
                    "<?php _e('The customer service was exceptional. They helped me find the perfect anniversary gift for my wife.', 'hid-hov-theme'); ?>"
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Emeka Nwachukwu</span>
                    <span class="author-location">Enugu, Nigeria</span>
                </div>
            </div>

            <div class="testimonial-item">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">
                    "<?php _e('Every piece I own from House of Veloria sparks conversations. The quality speaks for itself.', 'hid-hov-theme'); ?>"
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Zainab Bello</span>
                    <span class="author-location">Kano, Nigeria</span>
                </div>
            </div>

            <div class="testimonial-item">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">
                    "<?php _e('The packaging was beautiful and the delivery was incredibly fast. A truly luxury experience from start to finish.', 'hid-hov-theme'); ?>"
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Chika Obi</span>
                    <span class="author-location">Ibadan, Nigeria</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========== CTA SECTION =========== -->
<section class="subscribe-section" style="padding: 5rem 0;">
    <div class="section-container" style="text-align: center;">
        <h2 class="section-title"><?php _e('Experience Veloria', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle" style="margin-bottom: 2rem;">
            <?php _e('Join our growing family of satisfied customers', 'hid-hov-theme'); ?>
        </p>
        <a href="<?php echo esc_url(hid_get_shop_url()); ?>" class="cta-button"><?php _e('Shop Now', 'hid-hov-theme'); ?></a>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

