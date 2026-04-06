<?php
/**
 * Template Name: Contact Page
 *
 * The contact page template.
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

get_template_part('template-parts/header');

$instagram_url = get_option('hid_commerce_instagram_url', '#');
$facebook_url = get_option('hid_commerce_facebook_url', '#');
$pinterest_url = get_option('hid_commerce_pinterest_url', '#');
$contact_email = get_option('hid_commerce_contact_email', 'clientservice@hoveloria.com');
$contact_phone = get_option('hid_commerce_contact_phone', '+234 814 097 4822');
?>

<!-- =========== PAGE HERO =========== -->
<section class="page-hero" style="background-image: url('https://images.unsplash.com/photo-1598560917505-59a3ad559071?w=1920&q=80'); min-height: 350px; height: 40vh;">
    <div class="page-hero-content">
        <h1 class="page-hero-title"><?php _e('Get in Touch', 'hid-hov-theme'); ?></h1>
        <p class="page-hero-subtitle"><?php _e("We're here to help you find the perfect piece", 'hid-hov-theme'); ?></p>
    </div>
</section>

<!-- =========== CONTACT SECTION =========== -->
<section style="padding: 4rem 0;">
    <div class="section-container">
        <div class="contact-layout">
            <!-- Contact Info Panel -->
            <div class="contact-info-panel">
                <h3 class="contact-info-title"><?php _e('Contact Information', 'hid-hov-theme'); ?></h3>
                
                <div class="contact-info-item">
                    <i class="fas fa-envelope contact-info-icon"></i>
                    <div class="contact-info-text">
                        <h4><?php _e('Email', 'hid-hov-theme'); ?></h4>
                        <p><a href="mailto:<?php echo esc_attr($contact_email); ?>" style="color: var(--warm-gold);"><?php echo esc_html($contact_email); ?></a></p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-phone contact-info-icon"></i>
                    <div class="contact-info-text">
                        <h4><?php _e('Phone', 'hid-hov-theme'); ?></h4>
                        <p><?php echo esc_html($contact_phone); ?></p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-clock contact-info-icon"></i>
                    <div class="contact-info-text">
                        <h4><?php _e('Response Time', 'hid-hov-theme'); ?></h4>
                        <p><?php _e('We respond to all inquiries within 24 hours', 'hid-hov-theme'); ?></p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-reply contact-info-icon"></i>
                    <div class="contact-info-text">
                        <h4><?php _e('Response Time', 'hid-hov-theme'); ?></h4>
                        <p><?php _e('Within 24 hours on business days', 'hid-hov-theme'); ?></p>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(180, 160, 106, 0.3);">
                    <h4 style="color: var(--warm-gold); margin-bottom: 1rem;"><?php _e('Follow Us', 'hid-hov-theme'); ?></h4>
                    <div class="footer-social">
                        <a href="<?php echo esc_url($instagram_url); ?>" aria-label="<?php esc_attr_e('Instagram', 'hid-hov-theme'); ?>"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo esc_url($facebook_url); ?>" aria-label="<?php esc_attr_e('Facebook', 'hid-hov-theme'); ?>"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo esc_url($pinterest_url); ?>" aria-label="<?php esc_attr_e('Pinterest', 'hid-hov-theme'); ?>"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-panel">
                <h3 style="font-family: var(--font-header); font-size: 2rem; color: var(--warm-gold); margin-bottom: 1.5rem;"><?php _e('Send Us a Message', 'hid-hov-theme'); ?></h3>
                
                <form id="contact-form">
                    <?php wp_nonce_field('hid_contact_nonce', 'contact_nonce'); ?>
                    <div class="form-group">
                        <label class="form-label" for="contact-name"><?php _e('Full Name *', 'hid-hov-theme'); ?></label>
                        <input type="text" id="contact-name" name="name" class="form-input" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-email"><?php _e('Email Address *', 'hid-hov-theme'); ?></label>
                        <input type="email" id="contact-email" name="email" class="form-input" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-subject"><?php _e('Subject *', 'hid-hov-theme'); ?></label>
                        <select id="contact-subject" name="subject" class="form-select" required>
                            <option value=""><?php _e('Select a topic...', 'hid-hov-theme'); ?></option>
                            <option value="general"><?php _e('General Inquiry', 'hid-hov-theme'); ?></option>
                            <option value="order"><?php _e('Order Question', 'hid-hov-theme'); ?></option>
                            <option value="product"><?php _e('Product Information', 'hid-hov-theme'); ?></option>
                            <option value="return"><?php _e('Returns & Exchanges', 'hid-hov-theme'); ?></option>
                            <option value="other"><?php _e('Other', 'hid-hov-theme'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-message"><?php _e('Message *', 'hid-hov-theme'); ?></label>
                        <textarea id="contact-message" name="message" class="form-textarea" placeholder="<?php esc_attr_e('How can we help you?', 'hid-hov-theme'); ?>" required></textarea>
                    </div>

                    <button type="submit" class="cta-button" style="width: 100%;"><?php _e('Send Message', 'hid-hov-theme'); ?></button>
                    <p class="form-feedback" id="contact-feedback"></p>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- =========== REQUEST ITEM SECTION =========== -->
<section class="request-form-section" id="request">
    <div class="section-container">
        <h2 class="section-title"><?php _e("Can't Find What You're Looking For?", 'hid-hov-theme'); ?></h2>
        <p class="section-subtitle" style="margin-bottom: 3rem;">
            <?php _e("Describe your dream piece and we'll search our network of artisans to find it for you", 'hid-hov-theme'); ?>
        </p>

        <form id="request-form" style="max-width: 900px; margin: 0 auto; background: var(--white); padding: 2.5rem; border: 1px solid #ddd;">
            <?php wp_nonce_field('hid_request_nonce', 'request_nonce'); ?>
            <div class="request-form-grid">
                <div class="form-group">
                    <label class="form-label" for="request-name"><?php _e('Your Name *', 'hid-hov-theme'); ?></label>
                    <input type="text" id="request-name" name="name" class="form-input" required />
                </div>

                <div class="form-group">
                    <label class="form-label" for="request-email"><?php _e('Email Address *', 'hid-hov-theme'); ?></label>
                    <input type="email" id="request-email" name="email" class="form-input" required />
                </div>

                <div class="form-group">
                    <label class="form-label" for="request-type"><?php _e('Jewelry Type *', 'hid-hov-theme'); ?></label>
                    <select id="request-type" name="type" class="form-select" required>
                        <option value=""><?php _e('Select type...', 'hid-hov-theme'); ?></option>
                        <option value="necklace"><?php _e('Necklace', 'hid-hov-theme'); ?></option>
                        <option value="earrings"><?php _e('Earrings', 'hid-hov-theme'); ?></option>
                        <option value="ring"><?php _e('Ring', 'hid-hov-theme'); ?></option>
                        <option value="bracelet"><?php _e('Bracelet', 'hid-hov-theme'); ?></option>
                        <option value="set"><?php _e('Jewelry Set', 'hid-hov-theme'); ?></option>
                        <option value="other"><?php _e('Other', 'hid-hov-theme'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="request-budget"><?php _e('Budget Range', 'hid-hov-theme'); ?></label>
                    <select id="request-budget" name="budget" class="form-select">
                        <option value=""><?php _e('Select budget...', 'hid-hov-theme'); ?></option>
                        <option value="under-200"><?php _e('Under $200', 'hid-hov-theme'); ?></option>
                        <option value="200-500"><?php _e('$200 - $500', 'hid-hov-theme'); ?></option>
                        <option value="500-1000"><?php _e('$500 - $1,000', 'hid-hov-theme'); ?></option>
                        <option value="over-1000"><?php _e('Over $1,000', 'hid-hov-theme'); ?></option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label" for="request-description"><?php _e('Describe Your Dream Piece *', 'hid-hov-theme'); ?></label>
                    <textarea id="request-description" name="description" class="form-textarea" placeholder="<?php esc_attr_e('Tell us about the piece you have in mind - style, materials, any inspiration images, etc.', 'hid-hov-theme'); ?>" required></textarea>
                </div>
            </div>

            <button type="submit" class="cta-button" style="width: 100%;"><?php _e('Submit Request', 'hid-hov-theme'); ?></button>
            <p class="form-feedback" id="request-feedback"></p>
        </form>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

