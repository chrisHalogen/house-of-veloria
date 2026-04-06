<?php
/**
 * The footer for our theme
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
?>

    </main>

    <!-- =========== FOOTER =========== -->
    <footer class="footer-enhanced">
        <div class="footer-grid">
            <!-- Brand Column -->
            <div class="footer-brand">
                <?php 
                $use_text_logo = get_option('hid_commerce_use_text_logo', '1');
                if ($use_text_logo == '1'): ?>
                    <div class="footer-logo-text">House Of Veloria</div>
                <?php else: ?>
                    <img src="<?php echo esc_url(hid_hov_theme_get_logo_url()); ?>" alt="<?php bloginfo('name'); ?>" class="footer-logo-img" />
                <?php endif; ?>
                <p><?php echo esc_html(get_bloginfo('description') ?: __('Curating timeless elegance for the discerning collector. Each piece tells a story of craftsmanship and beauty.', 'hid-hov-theme')); ?></p>
                <div class="footer-social">
                    <?php 
                    $instagram_url = get_option('hid_commerce_instagram_url');
                    $facebook_url = get_option('hid_commerce_facebook_url');
                    $pinterest_url = get_option('hid_commerce_pinterest_url');
                    $twitter_url = get_option('hid_commerce_twitter_url');
                    $youtube_url = get_option('hid_commerce_youtube_url');
                    $linkedin_url = get_option('hid_commerce_linkedin_url');
                    ?>
                    <?php if (!empty($instagram_url)): ?>
                        <a href="<?php echo esc_url($instagram_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Instagram', 'hid-hov-theme'); ?>"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($facebook_url)): ?>
                        <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Facebook', 'hid-hov-theme'); ?>"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($pinterest_url)): ?>
                        <a href="<?php echo esc_url($pinterest_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Pinterest', 'hid-hov-theme'); ?>"><i class="fab fa-pinterest"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($twitter_url)): ?>
                        <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Twitter', 'hid-hov-theme'); ?>"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($youtube_url)): ?>
                        <a href="<?php echo esc_url($youtube_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('YouTube', 'hid-hov-theme'); ?>"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($linkedin_url)): ?>
                        <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('LinkedIn', 'hid-hov-theme'); ?>"><i class="fab fa-linkedin"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-nav">
                <h4 class="footer-nav-title"><?php _e('Quick Links', 'hid-hov-theme'); ?></h4>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer-links',
                    'container' => false,
                    'menu_class' => 'footer-nav-list',
                    'fallback_cb' => 'hid_nav_menu_fallback',
                    'fallback_location' => 'footer-links',
                ));
                ?>
            </div>
            
            <!-- Customer Care -->
            <div class="footer-nav">
                <h4 class="footer-nav-title"><?php _e('Customer Care', 'hid-hov-theme'); ?></h4>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer-care',
                    'container' => false,
                    'menu_class' => 'footer-nav-list',
                    'fallback_cb' => 'hid_nav_menu_fallback',
                    'fallback_location' => 'footer-care',
                ));
                ?>
            </div>
            
            <!-- Policies -->
            <div class="footer-nav">
                <h4 class="footer-nav-title"><?php _e('Policies', 'hid-hov-theme'); ?></h4>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer-policies',
                    'container' => false,
                    'menu_class' => 'footer-nav-list',
                    'fallback_cb' => 'hid_nav_menu_fallback',
                    'fallback_location' => 'footer-policies',
                ));
                ?>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All Rights Reserved.', 'hid-hov-theme'); ?></p>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" aria-label="<?php esc_attr_e('Back to top', 'hid-hov-theme'); ?>">
        <i class="fas fa-chevron-up"></i>
    </button>
</div>

<?php wp_footer(); ?>
</body>
</html>

