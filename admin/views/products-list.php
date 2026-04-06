<?php
/**
 * Products List View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap hid-commerce-admin">
    <h1>
        <?php _e('Products', 'hid-simple-commerce'); ?>
        <a href="<?php echo admin_url('admin.php?page=hid-commerce-products&action=add'); ?>" class="page-title-action"><?php _e('Add New', 'hid-simple-commerce'); ?></a>
        <a href="#" id="hid-export-products" class="page-title-action"><?php _e('Export', 'hid-simple-commerce'); ?></a>
        <a href="<?php echo HID_COMMERCE_THEME_URL . 'assets/demo-products.csv'; ?>" class="page-title-action" download><?php _e('Download Sample CSV', 'hid-simple-commerce'); ?></a>
    </h1>

    <div class="tablenav top">
        <form method="get" style="display: inline-block;">
            <input type="hidden" name="page" value="hid-commerce-products">
            <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search products...', 'hid-simple-commerce'); ?>">
            
            <select name="category_id">
                <option value=""><?php _e('All Categories', 'hid-simple-commerce'); ?></option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo esc_attr($category->id); ?>" <?php selected($category_id, $category->id); ?>><?php echo esc_html($category->name); ?></option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="button"><?php _e('Filter', 'hid-simple-commerce'); ?></button>
        </form>
    </div>

    <table class="hid-admin-table wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Image', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Name', 'hid-simple-commerce'); ?></th>
                <th><?php _e('SKU', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Price', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Stock', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Category', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Status', 'hid-simple-commerce'); ?></th>
                <th><?php _e('Actions', 'hid-simple-commerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8"><?php _e('No products found.', 'hid-simple-commerce'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <?php
                    $category_name = '';
                    if ($product->category_id) {
                        $cat = $db->get_category($product->category_id);
                        $category_name = $cat ? $cat->name : '';
                    }
                    ?>
                    <tr>
                        <td>
                            <?php if ($product->image_url): ?>
                                <img src="<?php echo esc_url($product->image_url); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo esc_html($product->name); ?></strong>
                            <?php if ($product->featured): ?>
                                <span style="color: #B4A06A;">(Featured)</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($product->sku); ?></td>
                        <td>
                            <?php if ($product->has_variants): ?>
                                <em><?php _e('Variable', 'hid-simple-commerce'); ?></em>
                            <?php else: ?>
                                <?php echo HID_Commerce_Checkout::format_currency($product->sale_price ?: $product->price); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product->has_variants): ?>
                                <em><?php _e('See variants', 'hid-simple-commerce'); ?></em>
                            <?php else: ?>
                                <?php echo HID_Commerce_Inventory::get_stock_status_html($product->id); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($category_name); ?></td>
                        <td><?php echo esc_html($product->status); ?></td>
                        <td>
                            <a href="<?php echo esc_url(hid_get_page_url_by_template('page-product-detail.php') . '?product_id=' . $product->id); ?>" target="_blank"><?php _e('View', 'hid-simple-commerce'); ?></a> |
                            <a href="<?php echo admin_url('admin.php?page=hid-commerce-products&action=edit&id=' . $product->id); ?>"><?php _e('Edit', 'hid-simple-commerce'); ?></a> |
                            <a href="#" class="hid-delete-product" data-product-id="<?php echo esc_attr($product->id); ?>" style="color: #dc3232;"><?php _e('Delete', 'hid-simple-commerce'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total > $per_page): ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                $total_pages = ceil($total / $per_page);
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'current' => $page,
                    'total' => $total_pages,
                ));
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

