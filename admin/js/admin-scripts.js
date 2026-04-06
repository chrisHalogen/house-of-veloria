/**
 * HID Simple Commerce - Admin Scripts
 */

(function($) {
    'use strict';

    var HIDCommerceAdmin = {
        init: function() {
            this.bindEvents();
            this.initColorPickers();
            this.initMediaUpload();
        },

        bindEvents: function() {
            var self = this;

            // Save product
            $(document).on('submit', '#hid-product-form', function(e) {
                e.preventDefault();
                self.saveProduct($(this));
            });

            // Delete product
            $(document).on('click', '.hid-delete-product', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this product?')) {
                    self.deleteProduct($(this).data('product-id'));
                }
            });

            // Save category
            $(document).on('submit', '#hid-category-form', function(e) {
                e.preventDefault();
                self.saveCategory($(this));
            });

            // Edit category
            $(document).on('click', '.hid-edit-category', function(e) {
                e.preventDefault();
                self.editCategory($(this).data('category-id'));
            });

            // Cancel category edit
            $(document).on('click', '#hid-cancel-category-edit', function(e) {
                e.preventDefault();
                self.resetCategoryForm();
            });

            // Delete category
            $(document).on('click', '.hid-delete-category', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this category?')) {
                    self.deleteCategory($(this).data('category-id'));
                }
            });

            // Update order status
            $(document).on('change', '.hid-order-status-select', function() {
                var orderId = $(this).data('order-id');
                var status = $(this).val();
                self.updateOrderStatus(orderId, status);
            });

            // Confirm payment for bank transfer
            $(document).on('click', '.hid-confirm-payment-btn', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to confirm this payment?')) {
                    var orderId = $(this).data('order-id');
                    self.confirmPayment(orderId);
                }
            });

            // Add variant attribute
            $(document).on('click', '#hid-add-variant-attribute', function() {
                self.addVariantAttribute();
            });

            // Remove variant attribute
            $(document).on('click', '.hid-remove-attribute', function() {
                $(this).closest('.hid-variant-attribute').remove();
            });

            // Add variant
            $(document).on('click', '#hid-add-variant', function() {
                self.addVariant();
            });

            // Remove variant
            $(document).on('click', '.hid-remove-variant', function() {
                $(this).closest('.hid-variant-item').remove();
            });

            // Export products
            $(document).on('click', '#hid-export-products', function(e) {
                e.preventDefault();
                self.exportProducts();
            });

            // Export orders
            $(document).on('click', '#hid-export-orders', function(e) {
                e.preventDefault();
                self.exportOrders();
            });

            // Has variants checkbox
            $(document).on('change', '#hid-has-variants', function() {
                if ($(this).is(':checked')) {
                    $('.hid-simple-product-fields').hide();
                    $('.hid-variant-fields').show();
                } else {
                    $('.hid-simple-product-fields').show();
                    $('.hid-variant-fields').hide();
                }
            });
        },

        initColorPickers: function() {
            if ($.fn.wpColorPicker) {
                $('.hid-color-picker').wpColorPicker();
            }
        },

        initMediaUpload: function() {
            var self = this;

            // Primary Image Upload
            $(document).on('click', '.hid-upload-image', function(e) {
                e.preventDefault();
                
                var $button = $(this);
                var $input = $button.prev('input');
                var $preview = $button.siblings('.hid-image-preview');

                var frame = wp.media({
                    title: 'Select or Upload Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $input.val(attachment.url);
                    
                    if ($preview.length) {
                        $preview.html('<img src="' + attachment.url + '" style="max-width: 150px;">');
                    }
                });

                frame.open();
            });

            // Gallery Images Upload
            $(document).on('click', '#hid-add-gallery-images', function(e) {
                e.preventDefault();
                
                var frame = wp.media({
                    title: 'Select Gallery Images',
                    button: {
                        text: 'Add to Gallery'
                    },
                    multiple: 'add'
                });

                frame.on('select', function() {
                    var attachments = frame.state().get('selection').toJSON();
                    
                    attachments.forEach(function(attachment) {
                         var html = '<div class="hid-gallery-item">';
                         html += '<input type="hidden" name="gallery_images[]" value="' + attachment.url + '">';
                         html += '<img src="' + attachment.url + '" />';
                         html += '<button type="button" class="hid-remove-gallery-image"><i class="dashicons dashicons-trash"></i></button>';
                         html += '</div>';
                         
                         $('#hid-gallery-images').append(html);
                    });
                });

                frame.open();
            });

            // Remove Gallery Image
            $(document).on('click', '.hid-remove-gallery-image', function(e) {
                e.preventDefault();
                $(this).closest('.hid-gallery-item').remove();
            });
            
            // Initialize sortable if available
            if ($.fn.sortable) {
                $('#hid-gallery-images').sortable();
            }
        },

        saveProduct: function($form) {
            var formData = $form.serialize();
            formData += '&action=hid_save_product&nonce=' + hidCommerceAdmin.nonce;

            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        window.location.href = 'admin.php?page=hid-commerce-products';
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        },

        deleteProduct: function(productId) {
            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_delete_product',
                    nonce: hidCommerceAdmin.nonce,
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        },

        saveCategory: function($form) {
            var formData = $form.serialize();
            formData += '&action=hid_save_category&nonce=' + hidCommerceAdmin.nonce;

            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        },

        editCategory: function(categoryId) {
            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_get_category',
                    nonce: hidCommerceAdmin.nonce,
                    category_id: categoryId
                },
                success: function(response) {
                    if (response.success) {
                        var category = response.data.category;
                        $('input[name="category_id"]').val(category.id);
                        $('#name').val(category.name);
                        $('#description').val(category.description);
                        $('#parent_id').val(category.parent_id || '');
                        $('#display_order').val(category.display_order);
                        $('#image_url').val(category.image_url || '');
                        
                        // Update preview if image exists
                        if (category.image_url) {
                            $('.hid-image-preview').html('<img src="' + category.image_url + '" style="max-width: 150px;">');
                        } else {
                            $('.hid-image-preview').html('');
                        }
                        
                        // Update button text, show cancel button and scroll to form
                        $('#hid-save-category').text('Update Category');
                        $('#hid-cancel-category-edit').show();
                        $('html, body').animate({
                            scrollTop: $('#hid-category-form').offset().top - 50
                        }, 500);
                    } else {
                        alert(response.data.message || 'Error loading category');
                    }
                }
            });
        },

        resetCategoryForm: function() {
            $('#hid-category-form')[0].reset();
            $('input[name="category_id"]').val('');
            $('.hid-image-preview').html('');
            $('#hid-save-category').text('Add Category');
            $('#hid-cancel-category-edit').hide();
        },

        deleteCategory: function(categoryId) {
            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_delete_category',
                    nonce: hidCommerceAdmin.nonce,
                    category_id: categoryId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        },

        updateOrderStatus: function(orderId, status) {
            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_update_order_status',
                    nonce: hidCommerceAdmin.nonce,
                    order_id: orderId,
                    order_status: status
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        },

        confirmPayment: function(orderId) {
            var $button = $('.hid-confirm-payment-btn[data-order-id="' + orderId + '"]');
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_confirm_payment',
                    nonce: hidCommerceAdmin.nonce,
                    order_id: orderId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                        $button.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Confirm Payment Received');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Confirm Payment Received');
                }
            });
        },

        addVariantAttribute: function() {
            var html = '<div class="hid-variant-attribute">';
            html += '<input type="text" name="variant_attributes[]" placeholder="Attribute name (e.g., Size, Color)" class="regular-text">';
            html += '<button type="button" class="button hid-remove-attribute">Remove</button>';
            html += '</div>';
            
            $('#hid-variant-attributes-list').append(html);
        },

        addVariant: function() {
            var html = '<div class="hid-variant-item">';
            html += '<input type="text" name="variants[][name]" placeholder="Variant name" class="regular-text">';
            html += '<input type="number" name="variants[][price]" placeholder="Price" step="0.01" class="small-text">';
            html += '<input type="number" name="variants[][stock]" placeholder="Stock" class="small-text">';
            html += '<input type="text" name="variants[][sku]" placeholder="SKU" class="regular-text">';
            html += '<button type="button" class="button hid-remove-variant">Remove</button>';
            html += '</div>';
            
            $('#hid-variants-list').append(html);
        },

        exportProducts: function() {
            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_export_products',
                    nonce: hidCommerceAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Convert to CSV and download
                        var csv = convertArrayToCSV(response.data.csv_data);
                        downloadCSV(csv, 'products-export.csv');
                    }
                }
            });
        },

        exportOrders: function() {
            $.ajax({
                url: hidCommerceAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_export_orders',
                    nonce: hidCommerceAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var csv = convertArrayToCSV(response.data.csv_data);
                        downloadCSV(csv, 'orders-export.csv');
                    }
                }
            });
        }
    };

    // Utility functions
    function convertArrayToCSV(data) {
        var csv = '';
        
        data.forEach(function(row) {
            csv += row.map(function(cell) {
                return '"' + (cell || '').toString().replace(/"/g, '""') + '"';
            }).join(',');
            csv += '\n';
        });
        
        return csv;
    }

    function downloadCSV(csv, filename) {
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        
        if (link.download !== undefined) {
            var url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    // Initialize on document ready
    $(document).ready(function() {
        HIDCommerceAdmin.init();
    });

})(jQuery);

