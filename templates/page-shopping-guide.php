<?php
/**
 * Template Name: Shopping Guide
 *
 * The shopping guide page template.
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
<section class="page-hero" style="background-image: url('https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=1920&q=80');">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php _e('Shopping Guide', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e('Everything you need to know', 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== GUIDE NAVIGATION =========== -->
<section class="guide-nav-section">
    <div class="section-container">
        <div class="guide-nav">
            <a href="#sizing" class="guide-nav-item"><?php _e('Sizing Guide', 'hid-hov-theme'); ?></a>
            <a href="#shipping" class="guide-nav-item"><?php _e('Shipping', 'hid-hov-theme'); ?></a>
            <a href="#returns" class="guide-nav-item"><?php _e('Returns', 'hid-hov-theme'); ?></a>
            <a href="#care" class="guide-nav-item"><?php _e('Jewelry Care', 'hid-hov-theme'); ?></a>
            <a href="#faq" class="guide-nav-item"><?php _e('FAQs', 'hid-hov-theme'); ?></a>
        </div>
    </div>
</section>

<!-- =========== SIZING GUIDE =========== -->
<section id="sizing" class="guide-section">
    <div class="section-container">
        <h2 class="section-title"><?php _e('Sizing Guide', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('Find your perfect fit', 'hid-hov-theme'); ?></p>
        
        <div class="guide-content">
            <h3><?php _e('Ring Sizing', 'hid-hov-theme'); ?></h3>
            <p><?php _e('We use US ring sizes. To determine your ring size, you can measure an existing ring or use our printable ring sizer.', 'hid-hov-theme'); ?></p>
            
            <h3><?php _e('Necklace Lengths', 'hid-hov-theme'); ?></h3>
            <ul>
                <li><strong><?php _e('Choker:', 'hid-hov-theme'); ?></strong> 14-16 inches</li>
                <li><strong><?php _e('Princess:', 'hid-hov-theme'); ?></strong> 17-19 inches</li>
                <li><strong><?php _e('Matinee:', 'hid-hov-theme'); ?></strong> 20-24 inches</li>
                <li><strong><?php _e('Opera:', 'hid-hov-theme'); ?></strong> 28-36 inches</li>
            </ul>
            
            <h3><?php _e('Bracelet Sizing', 'hid-hov-theme'); ?></h3>
            <p><?php _e('Measure your wrist and add 0.5-1 inch for a comfortable fit.', 'hid-hov-theme'); ?></p>
        </div>
    </div>
</section>

<!-- =========== SHIPPING =========== -->
<section id="shipping" class="guide-section" style="background: var(--cream-linen);">
    <div class="section-container">
        <h2 class="section-title"><?php _e('Shipping Information', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('Delivery options and timeframes', 'hid-hov-theme'); ?></p>
        
        <div class="guide-content">
            <h3><?php _e('Domestic Shipping (USA)', 'hid-hov-theme'); ?></h3>
            <ul>
                <li><strong><?php _e('Standard:', 'hid-hov-theme'); ?></strong> <?php _e('5-7 business days - $9.99 (Free over $200)', 'hid-hov-theme'); ?></li>
                <li><strong><?php _e('Express:', 'hid-hov-theme'); ?></strong> <?php _e('2-3 business days - $19.99', 'hid-hov-theme'); ?></li>
                <li><strong><?php _e('Overnight:', 'hid-hov-theme'); ?></strong> <?php _e('Next business day - $39.99', 'hid-hov-theme'); ?></li>
            </ul>
            
            <h3><?php _e('International Shipping', 'hid-hov-theme'); ?></h3>
            <p><?php _e('We ship to most countries worldwide. International shipping typically takes 7-14 business days. Shipping rates are calculated at checkout based on destination.', 'hid-hov-theme'); ?></p>
        </div>
    </div>
</section>

<!-- =========== RETURNS =========== -->
<section id="returns" class="guide-section">
    <div class="section-container">
        <h2 class="section-title"><?php _e('Returns & Exchanges', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('Our hassle-free return policy', 'hid-hov-theme'); ?></p>
        
        <div class="guide-content">
            <h3><?php _e('30-Day Return Policy', 'hid-hov-theme'); ?></h3>
            <p><?php _e('We offer a 30-day return policy on all unworn items in original condition with tags attached.', 'hid-hov-theme'); ?></p>
            
            <h3><?php _e('How to Return', 'hid-hov-theme'); ?></h3>
            <ol>
                <li><?php _e('Contact our customer service team', 'hid-hov-theme'); ?></li>
                <li><?php _e('Receive your prepaid return label', 'hid-hov-theme'); ?></li>
                <li><?php _e('Pack the item securely', 'hid-hov-theme'); ?></li>
                <li><?php _e('Drop off at your nearest carrier', 'hid-hov-theme'); ?></li>
            </ol>
            
            <h3><?php _e('Exchanges', 'hid-hov-theme'); ?></h3>
            <p><?php _e('Need a different size? Contact us and we will arrange an exchange at no additional cost.', 'hid-hov-theme'); ?></p>
        </div>
    </div>
</section>

<!-- =========== CARE =========== -->
<section id="care" class="guide-section" style="background: var(--cream-linen);">
    <div class="section-container">
        <h2 class="section-title"><?php _e('Jewelry Care', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('Keep your pieces looking beautiful', 'hid-hov-theme'); ?></p>
        
        <div class="guide-content">
            <h3><?php _e('General Care Tips', 'hid-hov-theme'); ?></h3>
            <ul>
                <li><?php _e('Remove jewelry before swimming, bathing, or exercising', 'hid-hov-theme'); ?></li>
                <li><?php _e('Apply perfume and cosmetics before putting on jewelry', 'hid-hov-theme'); ?></li>
                <li><?php _e('Store pieces separately to prevent scratching', 'hid-hov-theme'); ?></li>
                <li><?php _e('Clean regularly with a soft, lint-free cloth', 'hid-hov-theme'); ?></li>
            </ul>
            
            <h3><?php _e('Storage', 'hid-hov-theme'); ?></h3>
            <p><?php _e('Store your jewelry in the provided pouch or box, away from direct sunlight and humidity.', 'hid-hov-theme'); ?></p>
        </div>
    </div>
</section>

<!-- =========== FAQ =========== -->
<section id="faq" class="guide-section">
    <div class="section-container">
        <h2 class="section-title"><?php _e('Frequently Asked Questions', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle"><?php _e('Find answers to common questions', 'hid-hov-theme'); ?></p>
        
        <div class="faq-accordion">
            <div class="faq-item">
                <button class="faq-question">
                    <?php _e('Are your products authentic?', 'hid-hov-theme'); ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p><?php _e('Yes, all our jewelry is 100% authentic. Each piece comes with a certificate of authenticity and we stand behind the quality of every item we sell.', 'hid-hov-theme'); ?></p>
                </div>
            </div>
            
            <div class="faq-item">
                <button class="faq-question">
                    <?php _e('Do you offer gift wrapping?', 'hid-hov-theme'); ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p><?php _e('Yes! All orders come beautifully packaged in our signature gift boxes at no extra charge. We also offer premium gift wrapping options at checkout.', 'hid-hov-theme'); ?></p>
                </div>
            </div>
            
            <div class="faq-item">
                <button class="faq-question">
                    <?php _e('Can I track my order?', 'hid-hov-theme'); ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p><?php _e('Absolutely! Once your order ships, you will receive a tracking number via email. You can also track your order using our order tracking page.', 'hid-hov-theme'); ?></p>
                </div>
            </div>
            
            <div class="faq-item">
                <button class="faq-question">
                    <?php _e('Do you offer repairs?', 'hid-hov-theme'); ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p><?php _e('Yes, we offer repair services for all items purchased from House of Veloria. Contact our customer service team for more information.', 'hid-hov-theme'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========== CTA =========== -->
<section class="subscribe-section" style="padding: 5rem 0;">
    <div class="section-container" style="text-align: center;">
        <h2 class="section-title"><?php _e('Still Have Questions?', 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle" style="margin-bottom: 2rem;">
            <?php _e("Our team is here to help", 'hid-hov-theme'); ?>
        </p>
        <a href="<?php echo esc_url(hid_get_contact_url()); ?>" class="cta-button"><?php _e('Contact Us', 'hid-hov-theme'); ?></a>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

