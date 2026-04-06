/**
 * HID Simple Commerce - Public Scripts
 */

(function($) {
    'use strict';

    var HIDCommerce = {
        init: function() {
            this.loadCart();
            this.bindEvents();
            this.applyColorScheme();
        },

        bindEvents: function() {
            var self = this;

            // Add to cart
            $(document).on('click', '.hid-add-to-cart', function(e) {
                e.preventDefault();
                var $btn = $(this);
                
                // Try to get product ID from button first (robust for detail page and shop page)
                var productId = $btn.data('product-id');
                
                // If not on button, try to find it on closest card (legacy support)
                var $card = $btn.closest('.hid-product-card, .product-card');
                if (!productId && $card.length) {
                    productId = $card.data('product-id');
                }
                
                if (!productId) {
                    self.showNotification('error', 'Product ID not found');
                    return;
                }
                
                // Find quantity input
                var quantity = 1;
                // Check closest wrapper first (detail page / shop page)
                var $wrapper = $btn.closest('.hid-add-to-cart-wrapper');
                if ($wrapper.length) {
                    quantity = $wrapper.find('.hid-quantity-input').val();
                } 
                // Fallback to searching in card
                else if ($card.length) {
                    quantity = $card.find('.hid-quantity-input').val();
                } else {
                    // Try finding sibling input
                    quantity = $btn.parent().find('.hid-quantity-input').val();
                }
                
                quantity = quantity || 1;

                self.addToCart(productId, quantity, null);
            });

            // Select options for variable products
            $(document).on('click', '.hid-select-options', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                self.showVariantSelector(productId);
            });

            // Remove from cart
            $(document).on('click', '.hid-remove-item', function(e) {
                e.preventDefault();
                var cartKey = $(this).data('cart-key');
                self.removeFromCart(cartKey);
            });

            // Update cart quantity
            $(document).on('change', '.hid-cart-item-quantity', function() {
                var cartKey = $(this).data('cart-key');
                var quantity = $(this).val();
                self.updateCartQuantity(cartKey, quantity);
            });

            // Proceed to checkout
            $(document).on('click', '#hid-proceed-checkout', function(e) {
                e.preventDefault();
                self.showCheckoutModal();
            });

            // Close modal
            $(document).on('click', '.hid-modal-close', function() {
                $(this).closest('.hid-modal').hide();
            });

            // Payment method selection
            $(document).on('change', 'input[name="payment_method"]', function() {
                var method = $(this).val();
                
                if (method === 'bank_transfer') {
                    $('#hid-bank-transfer-details').slideDown();
                } else {
                    $('#hid-bank-transfer-details').slideUp();
                }
            });

            // Checkout form submit
            $(document).on('submit', '#hid-checkout-form', function(e) {
                e.preventDefault();
                self.processCheckout($(this));
            });

            // Product search - Enter key
            $('#hid-product-search').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                self.filterProducts();
                }
            });

            // Search button click
            $('#hid-search-button').on('click', function() {
                self.filterProducts();
            });

            // Apply filters button
            $('#hid-apply-filters').on('click', function() {
                self.filterProducts();
            });

            // Clear filters
            $('#hid-clear-filters').on('click', function() {
                $('#hid-product-search').val('');
                $('#hid-category-filter').val('');
                $('#hid-special-filter').val('');
                $('#hid-sort-products').val('created_at-DESC');
                
                // Reload the page to restore original state
                window.location.href = window.location.pathname;
            });
            
            // Handle pagination clicks for filtered results
            $(document).on('click', '.hid-shop-pagination .hid-page-link', function(e) {
                e.preventDefault();
                var page = $(this).data('page');
                if (page) {
                    self.filterProducts(page);
                }
            });
        },

        addToCart: function(productId, quantity, variantId) {
            var self = this;

            $.ajax({
                url: hidCommerce.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_add_to_cart',
                    nonce: hidCommerce.nonce,
                    product_id: productId,
                    quantity: quantity,
                    variant_id: variantId
                },
                success: function(response) {
                    if (response.success) {
                        self.updateCartDisplay(response.data);
                        self.showNotification('success', response.data.message);
                    } else {
                        self.showNotification('error', response.data.message);
                    }
                }
            });
        },

        removeFromCart: function(cartKey) {
            var self = this;

            $.ajax({
                url: hidCommerce.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_remove_from_cart',
                    nonce: hidCommerce.nonce,
                    cart_key: cartKey
                },
                success: function(response) {
                    if (response.success) {
                        self.updateCartDisplay(response.data);
                        self.showNotification('success', response.data.message);
                    }
                }
            });
        },

        updateCartQuantity: function(cartKey, quantity) {
            var self = this;

            $.ajax({
                url: hidCommerce.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_update_cart',
                    nonce: hidCommerce.nonce,
                    cart_key: cartKey,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        self.updateCartDisplay(response.data);
                    }
                }
            });
        },

        loadCart: function() {
            var self = this;

            $.ajax({
                url: hidCommerce.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_get_cart',
                    nonce: hidCommerce.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.updateCartDisplay(response.data);
                    }
                }
            });
        },

        updateCartDisplay: function(data) {
            // Update cart count
            $('.cart-count').each(function() {
                if ($(this).closest('.floating-cart-icon').length > 0) {
                     $(this).text(data.cart_count);
                } else {
                     $(this).text('(' + data.cart_count + ')');
                }
            });

            // Update cart items
            var $cartItems = $('#hid-cart-items');
            $cartItems.empty();

            if (data.cart_items.length === 0) {
                $cartItems.html('<p>' + 'Your cart is empty' + '</p>');
                $('#hid-proceed-checkout').prop('disabled', true);
            } else {
                $('#hid-proceed-checkout').prop('disabled', false);
                
                $.each(data.cart_items, function(index, item) {
                    var itemHtml = '<div class="hid-cart-item">';
                    
                    if (item.product_image) {
                        itemHtml += '<div class="hid-cart-item-image"><img src="' + item.product_image + '" alt="' + item.product_name + '"></div>';
                    }
                    
                    itemHtml += '<div class="hid-cart-item-info">';
                    itemHtml += '<div class="hid-cart-item-name">' + item.product_name;
                    if (item.variant_name) {
                        itemHtml += ' - ' + item.variant_name;
                    }
                    itemHtml += '</div>';
                    itemHtml += '<div class="hid-cart-item-price">' + formatCurrency(item.price) + '</div>';
                    itemHtml += '<div class="hid-cart-item-actions">';
                    itemHtml += '<input type="number" class="hid-cart-item-quantity" value="' + item.quantity + '" min="1" data-cart-key="' + item.cart_key + '">';
                    itemHtml += '<button class="hid-remove-item" data-cart-key="' + item.cart_key + '">Remove</button>';
                    itemHtml += '</div>';
                    itemHtml += '</div>';
                    itemHtml += '</div>';
                    
                    $cartItems.append(itemHtml);
                });
            }

            // Update cart totals
            var $totals = $('#hid-cart-totals');
            $totals.empty();

            var totalsHtml = '<div class="hid-total-row"><span>Subtotal:</span><span>' + formatCurrency(data.totals.subtotal) + '</span></div>';
            
            if (data.totals.tax_amount > 0) {
                totalsHtml += '<div class="hid-total-row"><span>' + data.totals.tax_label + ':</span><span>' + formatCurrency(data.totals.tax_amount) + '</span></div>';
            }
            
            totalsHtml += '<div class="hid-total-row grand-total"><span>Total:</span><span>' + formatCurrency(data.totals.total) + '</span></div>';
            
            $totals.html(totalsHtml);

            // Update checkout summary if modal is open
            if ($('#hid-checkout-modal').is(':visible')) {
                this.updateCheckoutSummary(data);
            }
        },

        showVariantSelector: function(productId) {
            var self = this;

            $.ajax({
                url: hidCommerce.ajaxurl,
                type: 'GET',
                data: {
                    action: 'hid_get_variant_data',
                    nonce: hidCommerce.nonce,
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        self.renderVariantModal(productId, response.data.attributes, response.data.variants);
                    }
                }
            });
        },

        renderVariantModal: function(productId, attributes, variants) {
            var modalHtml = '<div id="hid-variant-modal" class="hid-modal">';
            modalHtml += '<div class="hid-modal-content">';
            modalHtml += '<span class="hid-modal-close">&times;</span>';
            modalHtml += '<h2>Select Options</h2>';
            
            // Render attribute selectors
            $.each(attributes, function(index, attr) {
                modalHtml += '<div class="hid-variant-attribute">';
                modalHtml += '<label>' + attr.attribute_name + '</label>';
                modalHtml += '<select class="hid-attribute-selector" data-attribute="' + attr.attribute_name + '">';
                modalHtml += '<option value="">-- Select --</option>';
                
                // Get unique values for this attribute
                var values = {};
                $.each(variants, function(i, variant) {
                    var attrs = variant.variant_attributes;
                    if (attrs[attr.attribute_name]) {
                        values[attrs[attr.attribute_name]] = true;
                    }
                });
                
                $.each(Object.keys(values), function(i, value) {
                    modalHtml += '<option value="' + value + '">' + value + '</option>';
                });
                
                modalHtml += '</select>';
                modalHtml += '</div>';
            });
            
            modalHtml += '<div id="hid-selected-variant-info"></div>';
            modalHtml += '<button type="button" class="hid-button-primary hid-button-large" id="hid-add-variant-to-cart" data-product-id="' + productId + '" disabled>Add to Cart</button>';
            modalHtml += '</div>';
            modalHtml += '</div>';
            
            $('body').append(modalHtml);
            
            // Store variants data
            $('#hid-variant-modal').data('variants', variants);
            
            // Bind variant selection
            this.bindVariantSelection();
        },

        bindVariantSelection: function() {
            var self = this;
            
            $(document).on('change', '.hid-attribute-selector', function() {
                self.matchVariant();
            });
            
            $(document).on('click', '#hid-add-variant-to-cart', function() {
                var productId = $(this).data('product-id');
                var variantId = $(this).data('variant-id');
                
                if (variantId) {
                    self.addToCart(productId, 1, variantId);
                    $('#hid-variant-modal').remove();
                }
            });
        },

        matchVariant: function() {
            var selectedAttrs = {};
            var allSelected = true;
            
            $('.hid-attribute-selector').each(function() {
                var attr = $(this).data('attribute');
                var value = $(this).val();
                
                if (!value) {
                    allSelected = false;
                } else {
                    selectedAttrs[attr] = value;
                }
            });
            
            if (!allSelected) {
                $('#hid-add-variant-to-cart').prop('disabled', true);
                return;
            }
            
            // Find matching variant
            var variants = $('#hid-variant-modal').data('variants');
            var matchedVariant = null;
            
            $.each(variants, function(index, variant) {
                var match = true;
                $.each(selectedAttrs, function(key, value) {
                    if (variant.variant_attributes[key] !== value) {
                        match = false;
                        return false;
                    }
                });
                
                if (match) {
                    matchedVariant = variant;
                    return false;
                }
            });
            
            if (matchedVariant) {
                var price = matchedVariant.sale_price || matchedVariant.price;
                var inStock = matchedVariant.stock_quantity > 0;
                
                var infoHtml = '<div class="hid-variant-info">';
                infoHtml += '<p><strong>Price:</strong> ' + formatCurrency(price) + '</p>';
                infoHtml += '<p class="stock-status ' + (inStock ? 'in-stock' : 'out-of-stock') + '">';
                infoHtml += inStock ? 'In Stock (' + matchedVariant.stock_quantity + ')' : 'Out of Stock';
                infoHtml += '</p>';
                infoHtml += '</div>';
                
                $('#hid-selected-variant-info').html(infoHtml);
                $('#hid-add-variant-to-cart').prop('disabled', !inStock).data('variant-id', matchedVariant.id);
            } else {
                $('#hid-selected-variant-info').html('<p class="hid-error">This combination is not available</p>');
                $('#hid-add-variant-to-cart').prop('disabled', true);
            }
        },

        showCheckoutModal: function() {
            var self = this;
            this.updateCheckoutSummary();
            
            // Load customer details from session
            $.ajax({
                url: hidCommerce.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hid_get_customer_details',
                    nonce: hidCommerce.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // Prefill form fields
                        $('#hid-customer-name').val(response.data.name || '');
                        $('#hid-customer-email').val(response.data.email || '');
                        $('#hid-customer-phone').val(response.data.phone || '');
                    }
                }
            });
            
            $('#hid-checkout-modal').fadeIn();
        },

        updateCheckoutSummary: function(data) {
            if (!data) {
                // Load current cart data
                var self = this;
                $.ajax({
                    url: hidCommerce.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'hid_get_cart',
                        nonce: hidCommerce.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            self.renderCheckoutSummary(response.data);
                        }
                    }
                });
            } else {
                this.renderCheckoutSummary(data);
            }
        },

        renderCheckoutSummary: function(data) {
            var summaryHtml = '<table class="hid-checkout-summary-table">';
            summaryHtml += '<thead><tr><th>Product</th><th>Qty</th><th>Price</th></tr></thead>';
            summaryHtml += '<tbody>';
            
            $.each(data.cart_items, function(index, item) {
                summaryHtml += '<tr>';
                summaryHtml += '<td>' + item.product_name;
                if (item.variant_name) {
                    summaryHtml += ' - ' + item.variant_name;
                }
                summaryHtml += '</td>';
                summaryHtml += '<td>' + item.quantity + '</td>';
                summaryHtml += '<td>' + formatCurrency(item.subtotal) + '</td>';
                summaryHtml += '</tr>';
            });
            
            summaryHtml += '</tbody>';
            summaryHtml += '<tfoot>';
            summaryHtml += '<tr><td colspan="2">Subtotal:</td><td>' + formatCurrency(data.totals.subtotal) + '</td></tr>';
            
            if (data.totals.tax_amount > 0) {
                summaryHtml += '<tr><td colspan="2">' + data.totals.tax_label + ':</td><td>' + formatCurrency(data.totals.tax_amount) + '</td></tr>';
            }
            
            summaryHtml += '<tr class="grand-total"><td colspan="2">Total:</td><td>' + formatCurrency(data.totals.total) + '</td></tr>';
            summaryHtml += '</tfoot>';
            summaryHtml += '</table>';
            
            $('#hid-checkout-order-summary').html(summaryHtml);
        },

        processCheckout: function($form) {
            var self = this;
            var formData = new FormData($form[0]);
            formData.append('action', 'hid_process_checkout');
            formData.append('nonce', hidCommerce.nonce);

            var $submitBtn = $form.find('button[type="submit"]');
            $submitBtn.prop('disabled', true).html('<span class="hid-spinner"></span> Processing...');

            $.ajax({
                url: hidCommerce.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Handle Paystack Popup
                        if (response.data.access_code && response.data.public_key && window.PaystackPop) {
                            var paystack = new PaystackPop();
                            paystack.newTransaction({
                                key: response.data.public_key,
                                email: response.data.customer_email,
                                amount: response.data.amount * 100, // Optional but good practice
                                access_code: response.data.access_code,
                                onCancel: function() {
                                    // Fallback when closed: Show Order Pending notification
                                    var $notification = $('<div class="hid-notification hid-notification-info">Order Pending. Please complete payment or check your email.</div>');
                                    // Remove any existing notification first
                                    $('.hid-notification').remove();
                                    $submitBtn.before($notification);
                                    $submitBtn.prop('disabled', false).text('Place Order');
                                    
                                    setTimeout(function() {
                                        $notification.fadeOut(function() {
                                            $(this).remove();
                                        });
                                    }, 5000);
                                },
                                onSuccess: function(transaction) {
                                    // Update order status on server then redirect
                                    $.ajax({
                                        url: hidCommerce.ajaxurl,
                                        type: 'POST',
                                        data: {
                                            action: 'hid_update_paystack_order',
                                            nonce: hidCommerce.nonce,
                                            reference: transaction.reference,
                                            order_number: response.data.order_number
                                        },
                                        complete: function() {
                                            // Redirect to confirmation regardless of update success (webhook is backup)
                                            window.location.href = addQueryArg('order_number', response.data.order_number, '/order-confirmation/');
                                        }
                                    });
                                }
                            });
                        } else {
                            // Standard redirect (other methods or Paystack fallback)
                            if (response.data.redirect) {
                                window.location.href = response.data.redirect;
                            } else {
                                // Direct success (e.g. Bank Transfer)
                                window.location.href = addQueryArg('order_number', response.data.order_number, '/order-confirmation/');
                            }
                        }
                    } else {
                        self.showNotification('error', response.data.message);
                        $submitBtn.prop('disabled', false).text('Place Order');
                    }
                },
                error: function() {
                    self.showNotification('error', 'An error occurred. Please try again.');
                    $submitBtn.prop('disabled', false).text('Place Order');
                }
            });
        },

        filterProducts: function(page) {
            var search = $('#hid-product-search').val();
            var category = $('#hid-category-filter').val();
            var specialFilter = $('#hid-special-filter').val();
            var sort = $('#hid-sort-products').val().split('-');
            page = page || 1;

            // Show loading indicator
            $('#hid-products-grid').html('<div class="hid-loading"><i class="fas fa-spinner fa-spin"></i><p>Loading products...</p></div>');

            $.ajax({
                url: hidCommerce.ajaxurl,
                type: 'GET',
                data: {
                    action: 'hid_filter_products',
                    nonce: hidCommerce.nonce,
                    search: search,
                    category: category,
                    special_filter: specialFilter,
                    orderby: sort[0],
                    order: sort[1],
                    page: page
                },
                success: function(response) {
                    if (response.success) {
                        $('#hid-products-grid').html(response.data.html);
                        
                        // Update pagination
                        if (response.data.pagination) {
                            $('.hid-shop-pagination').html(response.data.pagination).show();
                        } else {
                            $('.hid-shop-pagination').hide();
                        }
                        
                        // Scroll to top of products
                        $('html, body').animate({
                            scrollTop: $('#hid-commerce-shop').offset().top - 100
                        }, 300);
                    } else {
                        $('#hid-products-grid').html('<div class="hid-no-products"><p>No products found matching your criteria.</p></div>');
                        $('.hid-shop-pagination').hide();
                    }
                },
                error: function() {
                    $('#hid-products-grid').html('<div class="hid-no-products"><p>Error loading products. Please try again.</p></div>');
                    $('.hid-shop-pagination').hide();
                }
            });
        },

        showNotification: function(type, message) {
            var notificationHtml = '<div class="hid-notification hid-notification-' + type + '">' + message + '</div>';
            $('body').append(notificationHtml);
            
            setTimeout(function() {
                $('.hid-notification').fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        },

        applyColorScheme: function() {
            // Color scheme will be applied via inline styles from PHP
            // or via CSS variables dynamically loaded
        }
    };

    // Utility functions
    function formatCurrency(amount) {
        var formatted = parseFloat(amount).toFixed(parseInt(hidCommerce.decimal_places));
        formatted = formatted.replace('.', hidCommerce.decimal_separator);
        
        // Add thousands separator
        var parts = formatted.split(hidCommerce.decimal_separator);
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, hidCommerce.thousands_separator);
        formatted = parts.join(hidCommerce.decimal_separator);
        
        if (hidCommerce.currency_position === 'before') {
            return hidCommerce.currency_symbol + formatted;
        } else {
            return formatted + hidCommerce.currency_symbol;
        }
    }

    function addQueryArg(key, value, url) {
        var separator = url.indexOf('?') !== -1 ? '&' : '?';
        return url + separator + key + '=' + encodeURIComponent(value);
    }

    // Debounce function
    $.debounce = function(wait, func) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    };

    // Initialize on document ready
    $(document).ready(function() {
        HIDCommerce.init();
        initContactForm();
        initJewelryRequestForm();
    });

    /**
     * Contact Form with Captcha
     */
    function initContactForm() {
        var $form = $('#contact-form');
        if ($form.length === 0) return;

        $form.on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                name: $('#contact-name').val(),
                email: $('#contact-email').val(),
                subject: $('#contact-subject').val(),
                message: $('#contact-message').val(),
                nonce: $('input[name="contact_nonce"]').val()
            };

            // Validate
            if (!formData.name || !formData.email || !formData.subject || !formData.message) {
                showFormFeedback('#contact-feedback', 'error', 'Please fill in all required fields.');
                return;
            }

            // Show captcha popup
            showCaptchaPopup(function(answer, expected) {
                formData.captcha_answer = answer;
                formData.captcha_expected = expected;
                submitContactForm(formData);
            });
        });
    }

    function submitContactForm(formData) {
        var $feedback = $('#contact-feedback');
        $feedback.text('Sending...').removeClass('error success').addClass('info');

        $.ajax({
            url: hidCommerce.ajaxurl,
            type: 'POST',
            data: {
                action: 'hid_submit_contact',
                ...formData
            },
            success: function(response) {
                if (response.success) {
                    showFormFeedback('#contact-feedback', 'success', response.data.message);
                    $('#contact-form')[0].reset();
                } else {
                    showFormFeedback('#contact-feedback', 'error', response.data.message);
                }
            },
            error: function(xhr, status, error) {
                showFormFeedback('#contact-feedback', 'error', 'An error occurred. Please try again.');
            }
        });
    }

    /**
     * Jewelry Request Form with Captcha
     */
    function initJewelryRequestForm() {
        var $form = $('#request-form');
        if ($form.length === 0) return;

        $form.on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                name: $('#request-name').val(),
                email: $('#request-email').val(),
                type: $('#request-type').val(),
                budget: $('#request-budget').val(),
                description: $('#request-description').val(),
                nonce: $('input[name="request_nonce"]').val()
            };

            // Validate
            if (!formData.name || !formData.email || !formData.type || !formData.description) {
                showFormFeedback('#request-feedback', 'error', 'Please fill in all required fields.');
                return;
            }

            // Show captcha popup
            showCaptchaPopup(function(answer, expected) {
                formData.captcha_answer = answer;
                formData.captcha_expected = expected;
                submitJewelryRequest(formData);
            });
        });
    }

    function submitJewelryRequest(formData) {
        var $feedback = $('#request-feedback');
        $feedback.text('Sending...').removeClass('error success').addClass('info');

        $.ajax({
            url: hidCommerce.ajaxurl,
            type: 'POST',
            data: {
                action: 'hid_submit_jewelry_request',
                ...formData
            },
            success: function(response) {
                if (response.success) {
                    showFormFeedback('#request-feedback', 'success', response.data.message);
                    $('#request-form')[0].reset();
                } else {
                    showFormFeedback('#request-feedback', 'error', response.data.message);
                }
            },
            error: function(xhr, status, error) {
                showFormFeedback('#request-feedback', 'error', 'An error occurred. Please try again.');
            }
        });
    }

    /**
     * Show Captcha Popup
     */
    function showCaptchaPopup(callback) {
        // Generate random math problem
        var num1 = Math.floor(Math.random() * 10) + 1;
        var num2 = Math.floor(Math.random() * 10) + 1;
        var expected = num1 + num2;

        // Create popup HTML - styled exactly like homepage newsletter captcha for consistency
        var popupHtml = `
            <div class="hid-modal" id="contact-captcha-modal" style="display: flex;">
                <div class="hid-modal-content" style="max-width: 500px; box-shadow: 0 10px 50px rgba(71, 1, 8, 0.3);">
                    <span class="hid-modal-close">&times;</span>
                    <h2 style="margin-top: 0; color: var(--velvet-rouge); font-family: var(--font-header); font-size: 28px; font-weight: 400;">Verify You're Human</h2>
                    <p style="margin-bottom: 30px; color: #666; line-height: 1.6;">Please solve this quick math problem to complete your submission:</p>
                    
                    <form id="contact-captcha-form">
                        <div class="captcha-group" style="text-align: center; margin: 30px 0;">
                            <label for="captcha-answer" style="font-size: 24px; font-weight: 600; color: var(--velvet-rouge); display: block; margin-bottom: 15px;">
                                What is ${num1} + ${num2}?
                            </label>
                            <input
                                type="number"
                                id="captcha-answer"
                                name="captcha"
                                placeholder="Your answer"
                                style="width: 150px; padding: 15px; text-align: center; font-size: 20px; border: 2px solid var(--cream-linen); border-radius: 8px; transition: border-color 0.3s ease;"
                                required />
                            <input type="hidden" name="captcha_session" value="${expected}" />
                        </div>
                        
                        <button type="submit" class="cta-button" style="width: 100%; margin-top: 20px; display: inline-block; background-color: var(--warm-gold); color: #ffffff; padding: 15px 30px; border: 2px solid var(--warm-gold); border-radius: 2px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: all 0.3s ease; font-size: 14px; font-family: var(--font-body);">
                            Complete Submission
                        </button>
                    </form>
                    <p class="captcha-feedback" style="text-align: center; margin-top: 20px;"></p>
                </div>
            </div>
        `;

        $('body').append(popupHtml);
        
        // Focus on input after modal is displayed
        setTimeout(function() {
            $('#captcha-answer').focus();
        }, 100);

        // Handle form submit
        $('#contact-captcha-form').on('submit', function(e) {
            e.preventDefault();
            var answer = parseInt($('#captcha-answer').val());
            if (isNaN(answer)) {
                $('.captcha-feedback').text('Please enter a valid number.').css({'color': '#d63638', 'display': 'block'});
                return;
            }
            if (answer !== expected) {
                $('.captcha-feedback').text('Incorrect answer. Please try again.').css({'color': '#d63638', 'display': 'block'});
                $('#captcha-answer').val('').focus();
                return;
            }
            $('#contact-captcha-modal').remove();
            callback(answer, expected);
        });

        // Handle close button
        $('.hid-modal-close').on('click', function() {
            $('#contact-captcha-modal').remove();
        });

        // Close on overlay click (outside modal content)
        $('#contact-captcha-modal').on('click', function(e) {
            if (e.target.id === 'contact-captcha-modal') {
                $('#contact-captcha-modal').remove();
            }
        });
    }

    /**
     * Show Form Feedback
     */
    function showFormFeedback(selector, type, message) {
        var $feedback = $(selector);
        $feedback.text(message)
            .removeClass('error success info')
            .addClass(type)
            .show();
    }

})(jQuery);

