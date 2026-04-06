<?php
/**
 * Template Name: Curation Story
 *
 * The curation story / about page template.
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
<section class="page-hero" style="background-image: url('https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=1920&q=80');">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php _e('Our Curation Journey', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('Discovering Timeless Pieces for You', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== INTRODUCTION SECTION =========== -->
<section class="story-section" style="padding: 5rem 0;">
    <div class="section-container">
        <div class="two-column">
            <div class="two-column-quote">
                "<?php _e('Every piece of jewelry we select tells a story. Our role is simply to find those stories and bring them to you.', 'hid-hov-theme'); ?>"
            </div>
            <div class="two-column-text">
                <p style="margin-bottom: 1.5rem;">
                    <?php _e('At House of Veloria, we believe that true luxury lies in the stories told by each shimmering piece. Born from a legacy of artisans and a passion for exceptional craftsmanship, our collection represents more than mere accessories, they are heirlooms in a modern world.', 'hid-hov-theme'); ?>
                </p>
                <p>
                    <?php _e('Our mission is to curate jewelry that transcends trends, pieces that speak to the soul and stand the test of time.', 'hid-hov-theme'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- =========== OUR PROCESS TIMELINE =========== -->
<section class="timeline-section">
    <div class="section-container">
        <h2 class="section-title"><?php _e('Our Process', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('From discovery to your collection, every step matters', 'hid-hov-theme'); ?></p>
        
        <div class="timeline">
            <!-- Step 1 -->
            <div class="timeline-content">
                <span class="timeline-number">1</span>
                <h3 class="timeline-title"><?php _e('Collecting the Best Available', 'hid-hov-theme'); ?></h3>
                <p class="timeline-text">
                    <?php _e('We seek out master craftsmen whose techniques have been perfected over generations, bringing their exceptional work to our collection.', 'hid-hov-theme'); ?>
                </p>
            </div>
            
            <!-- Step 2 -->
            <div class="timeline-content">
                <span class="timeline-number">2</span>
                <h3 class="timeline-title"><?php _e('Quality Assessment', 'hid-hov-theme'); ?></h3>
                <p class="timeline-text">
                    <?php _e('Each piece undergoes rigorous evaluation. We examine materials, craftsmanship, durability, and authenticity. Only the finest make it through.', 'hid-hov-theme'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-number">3</span>
                    <h3 class="timeline-title"><?php _e('Style Curation', 'hid-hov-theme'); ?></h3>
                    <p class="timeline-text">
                        <?php _e('Our style experts select pieces that balance timeless elegance with contemporary appeal. We seek jewelry that will be treasured for generations.', 'hid-hov-theme'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Step 4 -->
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-number">4</span>
                    <h3 class="timeline-title"><?php _e('Final Inspection', 'hid-hov-theme'); ?></h3>
                    <p class="timeline-text">
                        <?php _e('Before joining our collection, every piece receives a final inspection and is carefully photographed to showcase its true beauty and character.', 'hid-hov-theme'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========== THE VELORIA STANDARD =========== -->
<section class="collection-section" style="background: var(--cream-linen);">
    <div class="section-container">
        <h2 class="section-title"><?php _e('The Veloria Standard', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('What sets our collection apart', 'hid-hov-theme'); ?></p>
        
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-gem"></i>
                </div>
                <h3 class="feature-title"><?php _e('Material Integrity', 'hid-hov-theme'); ?></h3>
                <p class="feature-text">
                    <?php _e('We verify the authenticity and quality of every material. From ethically sourced gemstones to responsibly mined precious metals, integrity is non-negotiable.', 'hid-hov-theme'); ?>
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <h3 class="feature-title"><?php _e('Design Excellence', 'hid-hov-theme'); ?></h3>
                <p class="feature-text">
                    <?php _e('Each piece must demonstrate exceptional design, balancing artistry with wearability. We celebrate both classic traditions and innovative techniques.', 'hid-hov-theme'); ?>
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3 class="feature-title"><?php _e('Value Assurance', 'hid-hov-theme'); ?></h3>
                <p class="feature-text">
                    <?php _e('Our direct relationships with artisans ensure fair pricing. You receive exceptional quality at honest prices, with complete transparency about each piece.', 'hid-hov-theme'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- =========== BRAND VALUES =========== -->
<section class="values-section">
    <div class="section-container">
        <h2 class="section-title" style="color: var(--warm-gold);"><?php _e('Our Values', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle" style="color: var(--cream-linen); opacity: 0.9;"><?php _e('The principles that guide everything we do', 'hid-hov-theme'); ?></p>
        
        <div class="values-grid">
            <div class="value-item">
                <h4 class="value-title"><?php _e('Authenticity', 'hid-hov-theme'); ?></h4>
                <p class="value-text">
                    <?php _e('We never compromise on authenticity. Every piece is verified and comes with complete documentation of its origin and materials.', 'hid-hov-theme'); ?>
                </p>
            </div>
            
            <div class="value-item">
                <h4 class="value-title"><?php _e('Sustainability', 'hid-hov-theme'); ?></h4>
                <p class="value-text">
                    <?php _e('We partner with suppliers who share our commitment to ethical sourcing and sustainable practices in jewelry making.', 'hid-hov-theme'); ?>
                </p>
            </div>
            
            <div class="value-item">
                <h4 class="value-title"><?php _e('Craftsmanship', 'hid-hov-theme'); ?></h4>
                <p class="value-text">
                    <?php _e('We celebrate the art of jewelry making, supporting artisans who have devoted their lives to perfecting their craft.', 'hid-hov-theme'); ?>
                </p>
            </div>
            
            <div class="value-item">
                <h4 class="value-title"><?php _e('Legacy', 'hid-hov-theme'); ?></h4>
                <p class="value-text">
                    <?php _e('We curate pieces meant to be passed down through generations, creating lasting connections between past, present, and future.', 'hid-hov-theme'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- =========== CALL TO ACTION =========== -->
<section class="subscribe-section" style="padding: 5rem 0;">
    <div class="section-container" style="text-align: center;">
        <h2 class="section-title"><?php _e('Begin Your Journey', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle" style="margin-bottom: 2rem;">
            <?php _e('Discover the pieces waiting to become part of your story', 'hid-hov-theme'); ?>
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo esc_url(hid_get_shop_url()); ?>" class="cta-button"><?php _e('Explore Our Collection', 'hid-hov-theme'); ?></a>
            <a href="<?php echo esc_url(hid_get_contact_url()); ?>" class="cta-button" style="background: transparent; color: var(--warm-gold);"><?php _e('Request a Piece', 'hid-hov-theme'); ?></a>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

