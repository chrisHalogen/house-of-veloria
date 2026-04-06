<?php
/**
 * The main template file
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

get_template_part('template-parts/header');
?>

<section class="collection-section">
    <div class="section-container">
        <?php if (have_posts()): ?>
            <div class="posts-grid">
                <?php while (have_posts()): the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <?php if (has_post_thumbnail()): ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="post-meta">
                                <span class="post-date"><?php echo get_the_date(); ?></span>
                            </div>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="cta-button"><?php _e('Read More', 'hid-hov-theme'); ?></a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <div class="pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '<i class="fas fa-chevron-left"></i>',
                    'next_text' => '<i class="fas fa-chevron-right"></i>',
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="no-posts">
                <h2><?php _e('No posts found', 'hid-hov-theme'); ?></h2>
                <p><?php _e('Check back soon for new content!', 'hid-hov-theme'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_template_part('template-parts/footer'); ?>

