<?php
/**
 * The front page template
 *
 * This template is used when a static front page is set.
 *
 * @package HID_HOV_Theme
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Check if the page has a custom template set
$template = get_page_template_slug();
if ($template && $template !== 'default') {
    // Load the custom template
    include(get_template_directory() . '/' . $template);
} else {
    // Default to homepage template
    include(get_template_directory() . '/templates/page-home.php');
}

