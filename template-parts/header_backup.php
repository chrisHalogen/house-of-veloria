<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="house-of-veloria-container">
    <!-- =========== HEADER =========== -->
    <header id="header" class="header">
        <nav class="nav-container">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                <?php 
                $use_text_logo = get_option('hid_commerce_use_text_logo', '1');
                if ($use_text_logo == '1'): ?>
                    <span class="logo-text">House Of Veloria</span>
                <?php else: ?>
                    <img src="<?php echo esc_url(hid_hov_theme_get_logo_url()); ?>" alt="<?php bloginfo('name'); ?>" class="logo-img" />
                <?php endif; ?>
            </a>

            <div class="nav-menu">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'nav-links',
                    'fallback_cb' => 'hid_nav_menu_fallback',
                    'fallback_location' => 'primary',
                    'walker' => new HID_Nav_Walker(),
                ));
                ?>

                <div class="nav-socials">
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

                <?php if (hid_is_shop_page()): ?>
                <div class="nav-cart">
                    <button class="cart-toggle" aria-label="<?php esc_attr_e('Toggle Cart', 'hid-hov-theme'); ?>">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count"><?php echo hid_get_cart_count(); ?></span>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <div class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <main>

