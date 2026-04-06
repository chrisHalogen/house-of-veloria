<?php
/**
 * Categories View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap hid-commerce-admin">
    <h1><?php _e('Product Categories', 'hid-simple-commerce'); ?></h1>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div>
            <h2><?php _e('Add New Category', 'hid-simple-commerce'); ?></h2>
            <form id="hid-category-form" method="post">
                <input type="hidden" name="category_id" value="">

                <table class="form-table">
                    <tr>
                        <th><label for="name"><?php _e('Name *', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="name" id="name" required class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><label for="description"><?php _e('Description', 'hid-simple-commerce'); ?></label></th>
                        <td><textarea name="description" id="description" rows="3" class="large-text"></textarea></td>
                    </tr>

                    <tr>
                        <th><label for="parent_id"><?php _e('Parent Category', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <select name="parent_id" id="parent_id">
                                <option value=""><?php _e('None (Top Level)', 'hid-simple-commerce'); ?></option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo esc_attr($cat->id); ?>"><?php echo esc_html($cat->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="image_url"><?php _e('Image', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="text" name="image_url" id="image_url" class="regular-text">
                            <button type="button" class="button hid-upload-image"><?php _e('Upload Image', 'hid-simple-commerce'); ?></button>
                            <div class="hid-image-preview"></div>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="display_order"><?php _e('Display Order', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="number" name="display_order" id="display_order" value="0" class="small-text"></td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" id="hid-save-category" class="button button-primary"><?php _e('Add Category', 'hid-simple-commerce'); ?></button>
                    <button type="button" id="hid-cancel-category-edit" class="button" style="display:none; margin-left:10px;"><?php _e('Cancel', 'hid-simple-commerce'); ?></button>
                </p>
            </form>
        </div>

        <div>
            <h2><?php _e('Existing Categories', 'hid-simple-commerce'); ?></h2>
            <table class="hid-admin-table wp-list-table widefat">
                <thead>
                    <tr>
                        <th><?php _e('Name', 'hid-simple-commerce'); ?></th>
                        <th><?php _e('Products', 'hid-simple-commerce'); ?></th>
                        <th><?php _e('Order', 'hid-simple-commerce'); ?></th>
                        <th><?php _e('Actions', 'hid-simple-commerce'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4"><?php _e('No categories found.', 'hid-simple-commerce'); ?></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <?php $product_count = $db->get_category_product_count($category->id); ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($category->name); ?></strong>
                                    <?php if ($category->description): ?>
                                        <br><small><?php echo esc_html($category->description); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($product_count); ?></td>
                                <td><?php echo esc_html($category->display_order); ?></td>
                                <td>
                                    <a href="#" class="hid-edit-category" data-category-id="<?php echo esc_attr($category->id); ?>"><?php _e('Edit', 'hid-simple-commerce'); ?></a> |
                                    <a href="#" class="hid-delete-category" data-category-id="<?php echo esc_attr($category->id); ?>" style="color: #dc3232;"><?php _e('Delete', 'hid-simple-commerce'); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

