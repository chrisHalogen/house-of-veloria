<?php
/**
 * Product Edit View
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_edit = !empty($product);
?>

<div class="wrap hid-commerce-admin">
    <h1><?php echo $is_edit ? __('Edit Product', 'hid-simple-commerce') : __('Add New Product', 'hid-simple-commerce'); ?></h1>

    <form id="hid-product-form" method="post">
        <input type="hidden" name="product_id" value="<?php echo $is_edit ? esc_attr($product->id) : ''; ?>">

        <table class="form-table hid-form-table">
            <tr>
                <th><label for="name"><?php _e('Product Name *', 'hid-simple-commerce'); ?></label></th>
                <td><input type="text" name="name" id="name" value="<?php echo $is_edit ? esc_attr($product->name) : ''; ?>" required class="regular-text"></td>
            </tr>

            <tr>
                <th><label for="description"><?php _e('Description', 'hid-simple-commerce'); ?></label></th>
                <td><textarea name="description" id="description" rows="5"><?php echo $is_edit ? esc_textarea($product->description) : ''; ?></textarea></td>
            </tr>

            <tr>
                <th><label for="sku"><?php _e('SKU', 'hid-simple-commerce'); ?></label></th>
                <td><input type="text" name="sku" id="sku" value="<?php echo $is_edit ? esc_attr($product->sku) : ''; ?>" class="regular-text"></td>
            </tr>

            <tr>
                <th><label for="category_id"><?php _e('Category', 'hid-simple-commerce'); ?></label></th>
                <td>
                    <select name="category_id" id="category_id">
                        <option value=""><?php _e('No Category', 'hid-simple-commerce'); ?></option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo esc_attr($category->id); ?>" <?php echo $is_edit && $product->category_id == $category->id ? 'selected' : ''; ?>><?php echo esc_html($category->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="image_url"><?php _e('Primary Image', 'hid-simple-commerce'); ?></label></th>
                <td>
                    <input type="text" name="image_url" id="image_url" value="<?php echo $is_edit ? esc_attr($product->image_url) : ''; ?>" class="regular-text">
                    <button type="button" class="button hid-upload-image"><?php _e('Upload Image', 'hid-simple-commerce'); ?></button>
                    <div class="hid-image-preview">
                        <?php if ($is_edit && $product->image_url): ?>
                            <img src="<?php echo esc_url($product->image_url); ?>" style="max-width: 150px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th><label><?php _e('Product Gallery', 'hid-simple-commerce'); ?></label></th>
                <td>
                    <div id="hid-product-gallery-container" class="hid-gallery-container">
                        <div id="hid-gallery-images" class="hid-gallery-images">
                            <?php if ($is_edit && !empty($images)): ?>
                                <?php foreach ($images as $img): ?>
                                    <?php if (!$img->is_primary): ?>
                                        <div class="hid-gallery-item">
                                            <input type="hidden" name="gallery_images[]" value="<?php echo esc_url($img->image_url); ?>">
                                            <img src="<?php echo esc_url($img->image_url); ?>" />
                                            <button type="button" class="hid-remove-gallery-image"><i class="dashicons dashicons-trash"></i></button>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button" id="hid-add-gallery-images"><?php _e('Add Gallery Images', 'hid-simple-commerce'); ?></button>
                        <p class="description"><?php _e('Drag and drop to reorder images.', 'hid-simple-commerce'); ?></p>
                    </div>
                    <!-- Add some basic CSS for gallery -->
                    <style>
                        .hid-gallery-images {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 10px;
                            margin-bottom: 10px;
                        }
                        .hid-gallery-item {
                            position: relative;
                            width: 100px;
                            height: 100px;
                            border: 1px solid #ddd;
                            background: #f0f0f0;
                        }
                        .hid-gallery-item img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                        .hid-remove-gallery-image {
                            position: absolute;
                            top: -5px;
                            right: -5px;
                            background: red;
                            color: white;
                            border: none;
                            border-radius: 50%;
                            width: 20px;
                            height: 20px;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 0;
                        }
                        .hid-remove-gallery-image .dashicons {
                            font-size: 14px;
                            width: 14px;
                            height: 14px;
                        }
                    </style>
                </td>
            </tr>

            <tr>
                <th><label for="has_variants"><?php _e('Product Type', 'hid-simple-commerce'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="has_variants" id="hid-has-variants" value="1" <?php echo $is_edit && $product->has_variants ? 'checked' : ''; ?>>
                        <?php _e('This product has variants', 'hid-simple-commerce'); ?>
                    </label>
                </td>
            </tr>
        </table>

        <div class="hid-simple-product-fields" style="<?php echo $is_edit && $product->has_variants ? 'display:none;' : ''; ?>">
            <h2><?php _e('Simple Product Options', 'hid-simple-commerce'); ?></h2>
            <table class="form-table hid-form-table">
                <tr>
                    <th><label for="price"><?php _e('Price *', 'hid-simple-commerce'); ?></label></th>
                    <td><input type="number" name="price" id="price" value="<?php echo $is_edit ? esc_attr($product->price) : ''; ?>" step="0.01" class="small-text"></td>
                </tr>

                <tr>
                    <th><label for="sale_price"><?php _e('Sale Price', 'hid-simple-commerce'); ?></label></th>
                    <td><input type="number" name="sale_price" id="sale_price" value="<?php echo $is_edit ? esc_attr($product->sale_price) : ''; ?>" step="0.01" class="small-text"></td>
                </tr>

                <tr>
                    <th><label for="stock_quantity"><?php _e('Stock Quantity *', 'hid-simple-commerce'); ?></label></th>
                    <td><input type="number" name="stock_quantity" id="stock_quantity" value="<?php echo $is_edit ? esc_attr($product->stock_quantity) : ''; ?>" class="small-text"></td>
                </tr>

                <tr>
                    <th><label for="low_stock_threshold"><?php _e('Low Stock Threshold', 'hid-simple-commerce'); ?></label></th>
                    <td><input type="number" name="low_stock_threshold" id="low_stock_threshold" value="<?php echo $is_edit ? esc_attr($product->low_stock_threshold) : '10'; ?>" class="small-text"></td>
                </tr>
            </table>
        </div>

        <div class="hid-variant-fields" style="<?php echo $is_edit && $product->has_variants ? '' : 'display:none;'; ?>">
            <h2><?php _e('Product Variants', 'hid-simple-commerce'); ?></h2>
            <p><?php _e('For variable products, define variant attributes below and then create variants for each combination.', 'hid-simple-commerce'); ?></p>
            
            <h3><?php _e('Variant Attributes', 'hid-simple-commerce'); ?></h3>
            <div id="hid-variant-attributes-list">
                <?php if ($is_edit && !empty($variant_attributes)): ?>
                    <?php foreach ($variant_attributes as $attr): ?>
                        <div class="hid-variant-attribute">
                            <input type="text" name="variant_attributes[]" value="<?php echo esc_attr($attr->attribute_name); ?>" class="regular-text">
                            <button type="button" class="button hid-remove-attribute"><?php _e('Remove', 'hid-simple-commerce'); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="button" id="hid-add-variant-attribute"><?php _e('Add Attribute', 'hid-simple-commerce'); ?></button>

            <h3 style="margin-top: 30px;"><?php _e('Variants', 'hid-simple-commerce'); ?></h3>
            <div id="hid-variants-list">
                <?php if ($is_edit && !empty($variants)): ?>
                    <?php foreach ($variants as $variant): ?>
                        <div class="hid-variant-item">
                            <input type="text" name="variants[][name]" value="<?php echo esc_attr($variant->variant_display_name); ?>" placeholder="Variant name" class="regular-text">
                            <input type="number" name="variants[][price]" value="<?php echo esc_attr($variant->price); ?>" placeholder="Price" step="0.01" class="small-text">
                            <input type="number" name="variants[][stock]" value="<?php echo esc_attr($variant->stock_quantity); ?>" placeholder="Stock" class="small-text">
                            <input type="text" name="variants[][sku]" value="<?php echo esc_attr($variant->sku); ?>" placeholder="SKU" class="regular-text">
                            <button type="button" class="button hid-remove-variant"><?php _e('Remove', 'hid-simple-commerce'); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="button" id="hid-add-variant"><?php _e('Add Variant', 'hid-simple-commerce'); ?></button>
        </div>

        <h2><?php _e('SEO Settings', 'hid-simple-commerce'); ?></h2>
        <table class="form-table hid-form-table">
            <tr>
                <th><label for="meta_title"><?php _e('Meta Title', 'hid-simple-commerce'); ?></label></th>
                <td><input type="text" name="meta_title" id="meta_title" value="<?php echo $is_edit ? esc_attr($product->meta_title) : ''; ?>" class="regular-text"></td>
            </tr>

            <tr>
                <th><label for="meta_description"><?php _e('Meta Description', 'hid-simple-commerce'); ?></label></th>
                <td><textarea name="meta_description" id="meta_description" rows="3"><?php echo $is_edit ? esc_textarea($product->meta_description) : ''; ?></textarea></td>
            </tr>

            <tr>
                <th><label for="slug"><?php _e('URL Slug', 'hid-simple-commerce'); ?></label></th>
                <td><input type="text" name="slug" id="slug" value="<?php echo $is_edit ? esc_attr($product->slug) : ''; ?>" class="regular-text"></td>
            </tr>
        </table>

        <h2><?php _e('Product Settings', 'hid-simple-commerce'); ?></h2>
        <table class="form-table hid-form-table">
            <tr>
                <th><label for="featured"><?php _e('Featured', 'hid-simple-commerce'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="featured" id="featured" value="1" <?php echo $is_edit && $product->featured ? 'checked' : ''; ?>>
                        <?php _e('Mark as featured product', 'hid-simple-commerce'); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th><label for="status"><?php _e('Status', 'hid-simple-commerce'); ?></label></th>
                <td>
                    <select name="status" id="status">
                        <option value="publish" <?php echo (!$is_edit || ($is_edit && $product->status == 'publish')) ? 'selected' : ''; ?>><?php _e('Published', 'hid-simple-commerce'); ?></option>
                        <option value="draft" <?php echo $is_edit && $product->status == 'draft' ? 'selected' : ''; ?>><?php _e('Draft', 'hid-simple-commerce'); ?></option>
                    </select>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary button-large"><?php echo $is_edit ? __('Update Product', 'hid-simple-commerce') : __('Add Product', 'hid-simple-commerce'); ?></button>
            <a href="<?php echo admin_url('admin.php?page=hid-commerce-products'); ?>" class="button button-large"><?php _e('Cancel', 'hid-simple-commerce'); ?></a>
        </p>
    </form>
</div>

