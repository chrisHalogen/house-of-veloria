<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

get_template_part('template-parts/header');
?>

<style>
    .error-404-section {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4rem 0;
        background: linear-gradient(135deg, var(--cream-linen) 0%, #fff 100%);
    }
    
    .error-404-content {
        text-align: center;
        max-width: 800px;
        padding: 3rem;
    }
    
    .error-404-number {
        font-size: 180px;
        font-family: var(--font-header);
        color: var(--velvet-rouge);
        line-height: 1;
        margin: 0;
        text-shadow: 2px 2px 0 var(--warm-gold);
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    
    .error-404-title {
        font-size: 42px;
        font-family: var(--font-header);
        color: var(--velvet-rouge);
        margin: 1.5rem 0 1rem;
    }
    
    .error-404-message {
        font-size: 18px;
        color: #666;
        margin: 1rem 0 2rem;
        line-height: 1.6;
    }
    
    .error-404-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
    }
    
    .error-404-actions .cta-button {
        min-width: 180px;
    }
    
    .error-404-icon {
        font-size: 120px;
        color: var(--warm-gold);
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .search-404 {
        max-width: 500px;
        margin: 2rem auto;
        position: relative;
    }
    
    .search-404 input {
        width: 100%;
        padding: 15px 50px 15px 20px;
        border: 2px solid var(--cream-linen);
        border-radius: 50px;
        font-size: 16px;
        font-family: var(--font-body);
        transition: all 0.3s ease;
    }
    
    .search-404 input:focus {
        outline: none;
        border-color: var(--warm-gold);
        box-shadow: 0 0 0 3px rgba(180, 160, 106, 0.1);
    }
    
    .search-404 button {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--velvet-rouge);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .search-404 button:hover {
        background: #6a0110;
    }
    
    .popular-links {
        margin-top: 3rem;
        padding-top: 3rem;
        border-top: 1px solid var(--cream-linen);
    }
    
    .popular-links h3 {
        font-family: var(--font-header);
        color: var(--velvet-rouge);
        font-size: 28px;
        margin-bottom: 1.5rem;
    }
    
    .popular-links-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .popular-link {
        padding: 1rem;
        background: white;
        border-radius: 8px;
        text-decoration: none;
        color: var(--velvet-rouge);
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .popular-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(71, 1, 8, 0.15);
        color: var(--warm-gold);
    }
    
    .popular-link i {
        font-size: 24px;
        display: block;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .error-404-number {
            font-size: 120px;
        }
        
        .error-404-title {
            font-size: 32px;
        }
        
        .error-404-message {
            font-size: 16px;
        }
        
        .error-404-actions {
            flex-direction: column;
        }
        
        .error-404-actions .cta-button {
            width: 100%;
        }
        
        .popular-links-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="error-404-section">
    <div class="error-404-content">
        <div class="error-404-icon">
            <i class="fas fa-gem"></i>
        </div>
        
        <h1 class="error-404-number">404</h1>
        
        <h2 class="error-404-title"><?php _e('Page Not Found', 'hid-hov-theme'); ?></h2>
        
        <p class="error-404-message">
            <?php _e('Oops! The page you\'re looking for seems to have disappeared. Like a rare gem, it might be hidden somewhere else or may no longer exist.', 'hid-hov-theme'); ?>
        </p>
        
        <div class="search-404">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="search" 
                       name="s" 
                       placeholder="<?php esc_attr_e('Search for products, pages...', 'hid-hov-theme'); ?>" 
                       value="<?php echo get_search_query(); ?>">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <div class="error-404-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="cta-button">
                <i class="fas fa-home"></i> <?php _e('Go Home', 'hid-hov-theme'); ?>
            </a>
            <a href="<?php echo esc_url(hid_get_shop_url()); ?>" class="cta-button" style="background: var(--warm-gold); border-color: var(--warm-gold);">
                <i class="fas fa-shopping-bag"></i> <?php _e('Shop Now', 'hid-hov-theme'); ?>
            </a>
        </div>
        
        <div class="popular-links">
            <h3><?php _e('Popular Pages', 'hid-hov-theme'); ?></h3>
            <div class="popular-links-grid">
                <a href="<?php echo esc_url(hid_get_shop_url()); ?>" class="popular-link">
                    <i class="fas fa-store"></i>
                    <strong><?php _e('Shop', 'hid-hov-theme'); ?></strong>
                </a>
                <a href="<?php echo esc_url(hid_get_page_url_by_template('page-curation-story.php')); ?>" class="popular-link">
                    <i class="fas fa-book-open"></i>
                    <strong><?php _e('Our Story', 'hid-hov-theme'); ?></strong>
                </a>
                <a href="<?php echo esc_url(home_url('/track-order/')); ?>" class="popular-link">
                    <i class="fas fa-shipping-fast"></i>
                    <strong><?php _e('Track Order', 'hid-hov-theme'); ?></strong>
                </a>
                <a href="<?php echo esc_url(hid_get_contact_url()); ?>" class="popular-link">
                    <i class="fas fa-envelope"></i>
                    <strong><?php _e('Contact Us', 'hid-hov-theme'); ?></strong>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

