<?php

/**
 * Template Name: Homepage
 *
 * The homepage template with dynamic products from database.
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

// Get products from database with category distribution
$categories = hid_get_categories();
$new_arrivals = hid_get_new_arrivals_distributed(6); // Max 3 per category
$new_arrival_ids = array_map(function ($p) {
    return $p->id;
}, $new_arrivals);
$featured_products = hid_get_featured_products_distributed(6); // Max 2 per category
$best_sellers = hid_get_best_sellers_distributed(15, $new_arrival_ids); // Get 15 products: top sellers + category-distributed fillers

// Debug mode for troubleshooting (only for admins)
$show_debug = isset($_GET['debug']) && current_user_can('manage_options');
if ($show_debug) {
    echo '<!-- DEBUG INFO: Categories: ' . count($categories) . ', New Arrivals: ' . count($new_arrivals) . ', Featured: ' . count($featured_products) . ', Best Sellers: ' . count($best_sellers) . ' -->';
}

// Hero image (can be customized via theme options)
$hero_image = get_option('hid_commerce_hero_image', 'https://hoveloria.com/wp-content/uploads/2026/01/hero-hoveloria.webp');
?>

<!-- =========== HERO SECTION =========== -->
<section class="hero-section" style="background-image: url('<?php echo esc_url($hero_image); ?>');">
    <div class="hero-content">
        <h1 class="hero-title"><?php echo esc_html(get_bloginfo('name')); ?></h1>
        <p class="hero-subtitle"><?php _e('Curated Elegance for the Discerning', 'hid-hov-theme'); ?></p>
        <a href="<?php echo esc_url(hid_get_shop_url()); ?>" class="cta-button"><?php _e('Explore Collection', 'hid-hov-theme'); ?></a>
    </div>
</section>

<!-- =========== NEW ARRIVALS =========== -->
<section class="collection-section">
    <div class="section-container">
        <h2 class="section-title"><?php _e('New Arrivals', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('The latest additions to our curated collection', 'hid-hov-theme'); ?></p>

        <div class="products-grid">
            <?php if (!empty($new_arrivals)): ?>
                <?php foreach ($new_arrivals as $product): ?>
                    <?php
                    $badge_text = __('New', 'hid-hov-theme');
                    hid_render_product_card($product, $badge_text);
                    ?>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Placeholder products when database is empty -->
                <div class="product-card">
                    <div class="product-card-image">
                        <img src="https://images.unsplash.com/photo-1602173574767-37ac01994b2a?w=500&q=80" alt="<?php esc_attr_e('Luna Pearl Necklace', 'hid-hov-theme'); ?>" />
                        <span class="product-card-badge"><?php _e('New', 'hid-hov-theme'); ?></span>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title"><?php _e('Luna Pearl Necklace', 'hid-hov-theme'); ?></h3>
                        <span class="product-card-price">$285</span>
                    </div>
                </div>
                <!-- ... other placeholders ... -->
                <div class="product-card">
                    <div class="product-card-image">
                        <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=500&q=80" alt="<?php esc_attr_e('Celestine Gold Ring', 'hid-hov-theme'); ?>" />
                        <span class="product-card-badge"><?php _e('New', 'hid-hov-theme'); ?></span>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title"><?php _e('Celestine Gold Ring', 'hid-hov-theme'); ?></h3>
                        <span class="product-card-price">$420</span>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-card-image">
                        <img src="https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=500&q=80" alt="<?php esc_attr_e('Aurora Drop Earrings', 'hid-hov-theme'); ?>" />
                        <span class="product-card-badge"><?php _e('New', 'hid-hov-theme'); ?></span>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title"><?php _e('Aurora Drop Earrings', 'hid-hov-theme'); ?></h3>
                        <span class="product-card-price">$195</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo esc_url(add_query_arg('filter', 'new-arrivals', hid_get_shop_url())); ?>" class="cta-button"><?php _e('View All New Arrivals', 'hid-hov-theme'); ?></a>
        </div>
    </div>
</section>

<!-- =========== STATS SECTION =========== -->
<section class="stats-section">
    <div class="section-container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">500+</span>
                <span class="stat-label"><?php _e('Pieces Curated', 'hid-hov-theme'); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label"><?php _e('Global Suppliers', 'hid-hov-theme'); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number">15K+</span>
                <span class="stat-label"><?php _e('Happy Customers', 'hid-hov-theme'); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number">99%</span>
                <span class="stat-label"><?php _e('Satisfaction Rate', 'hid-hov-theme'); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- =========== FEATURED COLLECTIONS =========== -->
<section class="collection-section">
    <div class="section-container">
        <h2 class="section-title"><?php _e('Our Collections', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('Discover handpicked treasures for every occasion', 'hid-hov-theme'); ?></p>

        <div class="collections-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach (array_slice($categories, 0, 4) as $category): ?>
                    <a href="<?php echo esc_url(add_query_arg('category', $category->id, hid_get_shop_url())); ?>" class="collection-card">
                        <?php if ($category->image_url): ?>
                            <img src="<?php echo esc_url($category->image_url); ?>" alt="<?php echo esc_attr($category->name); ?>" />
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&q=80" alt="<?php echo esc_attr($category->name); ?>" />
                        <?php endif; ?>
                        <div class="collection-card-overlay">
                            <h3 class="collection-card-title"><?php echo esc_html($category->name); ?></h3>
                            <span class="collection-card-count"><?php echo hid_get_category_product_count($category->id); ?> <?php _e('Items', 'hid-hov-theme'); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Placeholder when no categories exist -->
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--cream-linen); border-radius: 8px;">
                    <div style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;">✨</div>
                    <h3 style="font-size: 24px; margin-bottom: 10px; color: var(--warm-gold);"><?php _e('Collections In Progress', 'hid-hov-theme'); ?></h3>
                    <p style="color: #666; font-size: 16px;"><?php _e('Our curated collections are being carefully prepared. Check back soon!', 'hid-hov-theme'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========== BEST SELLERS CAROUSEL =========== -->
<section class="collection-section carousel-section" style="background: var(--cream-linen);">
    <div class="section-container">
        <h2 class="section-title">
            <a href="<?php echo esc_url(add_query_arg('filter', 'best-sellers', hid_get_shop_url())); ?>" style="color: inherit; text-decoration: none;">
                <?php _e('Best Sellers', 'hid-hov-theme'); ?>
            </a>
        </h2>
        <p class="section-subtitle"><?php _e('Our most loved pieces by discerning collectors', 'hid-hov-theme'); ?></p>

        <div class="carousel-wrapper">
            <div class="carousel-track" id="bestSellersCarousel">
                <?php if (!empty($best_sellers)): ?>
                    <?php foreach ($best_sellers as $product): ?>
                        <div class="carousel-slide">
                            <?php hid_render_product_card($product); ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Placeholder carousel when database is empty -->
                    <div class="carousel-slide">
                        <div class="product-card">
                            <div class="product-card-image">
                                <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=500&q=80" alt="The Miren Chain" />
                            </div>
                            <div class="product-card-info">
                                <h3 class="product-card-title"><?php _e('The Miren Chain', 'hid-hov-theme'); ?></h3>
                                <span class="product-card-price">$445</span>
                            </div>
                        </div>
                    </div>
                    <!-- ... other placeholders ... -->
                <?php endif; ?>
            </div>

            <div class="carousel-nav">
                <button class="carousel-btn" id="carouselPrev" aria-label="<?php esc_attr_e('Previous', 'hid-hov-theme'); ?>">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn" id="carouselNext" aria-label="<?php esc_attr_e('Next', 'hid-hov-theme'); ?>">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div style="text-align: center; margin-top: 3rem;">
             <a href="<?php echo esc_url(add_query_arg('filter', 'best-sellers', hid_get_shop_url())); ?>" class="cta-button"><?php _e('View All Best Sellers', 'hid-hov-theme'); ?></a>
        </div>
    </div>
</section>

<!-- =========== TRUST INDICATORS =========== -->
<section class="trust-badges" style="background-color: #ffffff;">
    <div class="section-container">
        <div class="trust-grid">
            <div class="trust-item">
                <div class="trust-icon"><i class="fas fa-shipping-fast"></i></div>
                <h4 class="trust-title"><?php _e('Free Shipping', 'hid-hov-theme'); ?></h4>
                <p class="trust-text"><?php _e('Complimentary delivery on orders over #200k', 'hid-hov-theme'); ?></p>
            </div>
            <div class="trust-item">
                <div class="trust-icon"><i class="fas fa-undo"></i></div>
                <h4 class="trust-title"><?php _e('5-Day Returns', 'hid-hov-theme'); ?></h4>
                <p class="trust-text"><?php _e('Hassle-free returns within 5 days', 'hid-hov-theme'); ?></p>
            </div>
            <div class="trust-item">
                <div class="trust-icon"><i class="fas fa-certificate"></i></div>
                <h4 class="trust-title"><?php _e('Authenticity Guaranteed', 'hid-hov-theme'); ?></h4>
                <p class="trust-text"><?php _e('Every piece verified and certified', 'hid-hov-theme'); ?></p>
            </div>
            <div class="trust-item">
                <div class="trust-icon"><i class="fas fa-lock"></i></div>
                <h4 class="trust-title"><?php _e('Secure Payment', 'hid-hov-theme'); ?></h4>
                <p class="trust-text"><?php _e('256-bit SSL encrypted checkout', 'hid-hov-theme'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- =========== NEWSLETTER SIGNUP =========== -->
<section class="subscribe-section" >
    <div class="section-container">
        <h2 class="section-title"><?php _e('Join the Veloria Circle', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle">
            <?php _e('Subscribe for exclusive access to new arrivals, special offers, and a', 'hid-hov-theme'); ?>
            <strong><?php _e('15% discount', 'hid-hov-theme'); ?></strong> <?php _e('on your first order.', 'hid-hov-theme'); ?>
        </p>
        <form class="subscribe-form" id="newsletter-form">
            <label for="newsletter-email" class="sr-only"><?php _e('Email Address', 'hid-hov-theme'); ?></label>
            <input
                type="email"
                id="newsletter-email"
                name="email"
                placeholder="<?php esc_attr_e('Enter your email address', 'hid-hov-theme'); ?>"
                required />

            <button type="submit" class="cta-button"><?php _e('Subscribe', 'hid-hov-theme'); ?></button>
        </form>
        <p class="form-feedback"></p>
        
        <!-- Captcha Modal -->
        <div id="newsletter-captcha-modal" class="hid-modal" style="display: none;">
            <div class="hid-modal-content" style="max-width: 500px;">
                <span class="hid-modal-close">&times;</span>
                <h2 style="margin-top: 0; color: var(--velvet-rouge);"><?php _e('Verify You\'re Human', 'hid-hov-theme'); ?></h2>
                <p style="margin-bottom: 30px;"><?php _e('Please solve this quick math problem to complete your subscription:', 'hid-hov-theme'); ?></p>
                
                <?php
                // Generate math captcha
                $num1 = rand(1, 10);
                $num2 = rand(1, 10);
                $captcha_answer = $num1 + $num2;
                ?>
                
                <form id="newsletter-captcha-form">
                    <input type="hidden" id="newsletter-email-hidden" name="email" />
                    
                    <div class="captcha-group" style="text-align: center; margin: 30px 0;">
                        <label for="captcha-answer" style="font-size: 24px; font-weight: 600; color: var(--velvet-rouge); display: block; margin-bottom: 15px;">
                            <?php printf(__('What is %d + %d?', 'hid-hov-theme'), $num1, $num2); ?>
                        </label>
                        <input
                            type="number"
                            id="captcha-answer"
                            name="captcha"
                            placeholder="<?php esc_attr_e('Your answer', 'hid-hov-theme'); ?>"
                            style="width: 150px; padding: 15px; text-align: center; font-size: 20px; border: 2px solid var(--cream-linen); border-radius: 8px;"
                            required />
                        <input type="hidden" name="captcha_session" value="<?php echo esc_attr($captcha_answer); ?>" />
                    </div>
                    
                    <button type="submit" class="cta-button" style="width: 100%; margin-top: 20px;">
                        <?php _e('Complete Subscription', 'hid-hov-theme'); ?>
                    </button>
                </form>
                <p class="captcha-feedback" style="text-align: center; margin-top: 20px;"></p>
            </div>
        </div>
        <p class="privacy-text">
            <?php _e('We respect your privacy. Unsubscribe anytime.', 'hid-hov-theme'); ?>
        </p>
    </div>
</section>

<!-- =========== TESTIMONIAL HIGHLIGHTS =========== -->
<section class="testimonials-section">
    <div class="section-container">
        <h2 class="section-title"><?php _e('What Our Clients Say', 'hid-hov-theme'); ?></h2>
        <div class="testimonial-carousel">
            <div class="testimonial-card active">
                <span class="quote-mark">"</span>
                <p class="testimonial-text">
                    <?php _e("The craftsmanship is simply unparalleled. Each piece feels like a work of art, a modern heirloom I'll treasure forever.", 'hid-hov-theme'); ?>
                </p>
                <span class="testimonial-author">- Chioma Okafor, Lagos</span>
            </div>
            <div class="testimonial-card">
                <span class="quote-mark">"</span>
                <p class="testimonial-text">
                    <?php _e("I've been searching for a brand that captures both classic elegance and a modern feel. House of Veloria is it.", 'hid-hov-theme'); ?>
                </p>
                <span class="testimonial-author">- Adaeze Nwankwo, Abuja</span>
            </div>
            <div class="testimonial-card">
                <span class="quote-mark">"</span>
                <p class="testimonial-text">
                    <?php _e("From the moment I saw the collection, I knew this was special. The attention to detail is breathtaking.", 'hid-hov-theme'); ?>
                </p>
                <span class="testimonial-author">- Blessing Adeleke, Port Harcourt</span>
            </div>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo esc_url(hid_get_page_url_by_template('page-testimonials.php')); ?>" class="cta-button" style="background: transparent; border-color: var(--warm-gold); color: var(--warm-gold);"><?php _e('Read All Reviews', 'hid-hov-theme'); ?></a>
        </div>
    </div>
</section>

<!-- =========== INSTAGRAM FEED =========== -->
<section class="instagram-section">
    <div class="section-container">
        <h2 class="section-title">@HouseofVeloria</h2>
        <p class="section-subtitle"><?php _e('Follow our journey and get inspired', 'hid-hov-theme'); ?></p>

        <div class="instagram-grid">
            <?php
            $instagram_feed = hid_get_instagram_feed();
            foreach ($instagram_feed as $item):
            ?>
                <div class="instagram-item">
                    <a href="<?php echo esc_url($item['link']); ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo esc_url($item['url']); ?>" alt="<?php esc_attr_e('Instagram post', 'hid-hov-theme'); ?>" />
                        <div class="instagram-overlay"><i class="fab fa-instagram"></i></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>