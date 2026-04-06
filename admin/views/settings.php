<?php
/**
 * Settings View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap hid-commerce-admin">
    <h1><?php _e('HID Commerce Settings', 'hid-simple-commerce'); ?></h1>

    <div class="hid-settings-tabs">
        <nav>
            <a href="?page=hid-commerce-settings&tab=general" class="<?php echo $active_tab == 'general' ? 'active' : ''; ?>"><?php _e('General', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-settings&tab=currency" class="<?php echo $active_tab == 'currency' ? 'active' : ''; ?>"><?php _e('Currency', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-settings&tab=tax" class="<?php echo $active_tab == 'tax' ? 'active' : ''; ?>"><?php _e('Tax', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-settings&tab=inventory" class="<?php echo $active_tab == 'inventory' ? 'active' : ''; ?>"><?php _e('Inventory', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-settings&tab=payments" class="<?php echo $active_tab == 'payments' ? 'active' : ''; ?>"><?php _e('Payments', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-settings&tab=email" class="<?php echo $active_tab == 'email' ? 'active' : ''; ?>"><?php _e('Email', 'hid-simple-commerce'); ?></a>
            <a href="?page=hid-commerce-settings&tab=social" class="<?php echo $active_tab == 'social' ? 'active' : ''; ?>"><?php _e('Social Media', 'hid-simple-commerce'); ?></a>
        </nav>

        <?php if ($active_tab == 'general'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('hid_commerce_general'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="hid_commerce_admin_email"><?php _e('Admin Email', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="email" name="hid_commerce_admin_email" id="hid_commerce_admin_email" value="<?php echo esc_attr(get_option('hid_commerce_admin_email')); ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><?php _e('Logo Type', 'hid-simple-commerce'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="hid_commerce_use_text_logo" value="1" <?php checked(get_option('hid_commerce_use_text_logo', '1'), '1'); ?>>
                                <?php _e('Use text logo (House Of Veloria in Dancing Script font)', 'hid-simple-commerce'); ?>
                            </label>
                            <p class="description"><?php _e('Uncheck this to use a custom image logo instead', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_site_logo"><?php _e('Custom Logo Image', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="text" name="hid_commerce_site_logo" id="hid_commerce_site_logo" value="<?php echo esc_attr(get_option('hid_commerce_site_logo')); ?>" class="regular-text">
                            <button type="button" class="button hid-upload-image"><?php _e('Upload Logo', 'hid-simple-commerce'); ?></button>
                            <div class="hid-image-preview">
                                <?php if (get_option('hid_commerce_site_logo')): ?>
                                    <img src="<?php echo esc_url(get_option('hid_commerce_site_logo')); ?>" style="max-width: 200px; margin-top: 10px;">
                                <?php endif; ?>
                            </div>
                            <p class="description"><?php _e('Only used if "Use text logo" is unchecked above', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('Color Scheme', 'hid-simple-commerce'); ?></th>
                        <td>
                            <div class="hid-color-picker-group">
                                <div class="hid-color-field">
                                    <label><?php _e('Primary:', 'hid-simple-commerce'); ?></label>
                                    <input type="text" name="hid_commerce_color_primary" value="<?php echo esc_attr(get_option('hid_commerce_color_primary', '#470108')); ?>" class="hid-color-picker">
                                </div>
                                <div class="hid-color-field">
                                    <label><?php _e('Secondary:', 'hid-simple-commerce'); ?></label>
                                    <input type="text" name="hid_commerce_color_secondary" value="<?php echo esc_attr(get_option('hid_commerce_color_secondary', '#B4A06A')); ?>" class="hid-color-picker">
                                </div>
                                <div class="hid-color-field">
                                    <label><?php _e('Accent:', 'hid-simple-commerce'); ?></label>
                                    <input type="text" name="hid_commerce_color_accent" value="<?php echo esc_attr(get_option('hid_commerce_color_accent', '#3a5a8b')); ?>" class="hid-color-picker">
                                </div>
                                <div class="hid-color-field">
                                    <label><?php _e('Background:', 'hid-simple-commerce'); ?></label>
                                    <input type="text" name="hid_commerce_color_background" value="<?php echo esc_attr(get_option('hid_commerce_color_background', '#F8F4E9')); ?>" class="hid-color-picker">
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('Typography', 'hid-simple-commerce'); ?></th>
                        <td>
                            <p><label><?php _e('Header Font:', 'hid-simple-commerce'); ?> <input type="text" name="hid_commerce_font_header" value="<?php echo esc_attr(get_option('hid_commerce_font_header', 'Dancing Script')); ?>" class="regular-text"></label></p>
                            <p><label><?php _e('Body Font:', 'hid-simple-commerce'); ?> <input type="text" name="hid_commerce_font_body" value="<?php echo esc_attr(get_option('hid_commerce_font_body', 'Montserrat')); ?>" class="regular-text"></label></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('Enable Physical Pickup', 'hid-simple-commerce'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="hid_commerce_enable_pickup" value="1" <?php checked(get_option('hid_commerce_enable_pickup', '0'), '1'); ?>>
                                <?php _e('Allow customers to choose physical pickup at checkout', 'hid-simple-commerce'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="hid_commerce_pickup_address"><?php _e('Pickup Address', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <textarea name="hid_commerce_pickup_address" id="hid_commerce_pickup_address" rows="3" class="large-text"><?php echo esc_textarea(get_option('hid_commerce_pickup_address')); ?></textarea>
                            <p class="description"><?php _e('Displays this address to customers if they select physical pickup.', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

        <?php elseif ($active_tab == 'currency'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('hid_commerce_currency'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="hid_commerce_currency_code"><?php _e('Currency Code', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_currency_code" id="hid_commerce_currency_code" value="<?php echo esc_attr(get_option('hid_commerce_currency_code', 'USD')); ?>" class="small-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_currency_symbol"><?php _e('Currency Symbol', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_currency_symbol" id="hid_commerce_currency_symbol" value="<?php echo esc_attr(get_option('hid_commerce_currency_symbol', '$')); ?>" class="small-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_currency_position"><?php _e('Currency Position', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <select name="hid_commerce_currency_position" id="hid_commerce_currency_position">
                                <option value="before" <?php selected(get_option('hid_commerce_currency_position', 'before'), 'before'); ?>><?php _e('Before ($99)', 'hid-simple-commerce'); ?></option>
                                <option value="after" <?php selected(get_option('hid_commerce_currency_position'), 'after'); ?>><?php _e('After (99$)', 'hid-simple-commerce'); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_decimal_places"><?php _e('Decimal Places', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="number" name="hid_commerce_decimal_places" id="hid_commerce_decimal_places" value="<?php echo esc_attr(get_option('hid_commerce_decimal_places', '2')); ?>" class="small-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_thousands_separator"><?php _e('Thousands Separator', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_thousands_separator" id="hid_commerce_thousands_separator" value="<?php echo esc_attr(get_option('hid_commerce_thousands_separator', ',')); ?>" class="small-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_decimal_separator"><?php _e('Decimal Separator', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_decimal_separator" id="hid_commerce_decimal_separator" value="<?php echo esc_attr(get_option('hid_commerce_decimal_separator', '.')); ?>" class="small-text"></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

        <?php elseif ($active_tab == 'tax'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('hid_commerce_tax'); ?>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable Tax', 'hid-simple-commerce'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="hid_commerce_tax_enabled" value="1" <?php checked(get_option('hid_commerce_tax_enabled', '0'), '1'); ?>>
                                <?php _e('Enable tax calculation', 'hid-simple-commerce'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_tax_rate"><?php _e('Tax Rate (%)', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="number" name="hid_commerce_tax_rate" id="hid_commerce_tax_rate" value="<?php echo esc_attr(get_option('hid_commerce_tax_rate', '0')); ?>" step="0.01" class="small-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_tax_label"><?php _e('Tax Label', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_tax_label" id="hid_commerce_tax_label" value="<?php echo esc_attr(get_option('hid_commerce_tax_label', 'VAT')); ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_tax_method"><?php _e('Tax Calculation', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <select name="hid_commerce_tax_method" id="hid_commerce_tax_method">
                                <option value="exclusive" <?php selected(get_option('hid_commerce_tax_method', 'exclusive'), 'exclusive'); ?>><?php _e('Exclusive (added to price)', 'hid-simple-commerce'); ?></option>
                                <option value="inclusive" <?php selected(get_option('hid_commerce_tax_method'), 'inclusive'); ?>><?php _e('Inclusive (included in price)', 'hid-simple-commerce'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

        <?php elseif ($active_tab == 'inventory'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('hid_commerce_inventory'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="hid_commerce_low_stock_threshold"><?php _e('Low Stock Threshold', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="number" name="hid_commerce_low_stock_threshold" id="hid_commerce_low_stock_threshold" value="<?php echo esc_attr(get_option('hid_commerce_low_stock_threshold', '10')); ?>" class="small-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_out_of_stock_behavior"><?php _e('Out of Stock Behavior', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <select name="hid_commerce_out_of_stock_behavior" id="hid_commerce_out_of_stock_behavior">
                                <option value="show" <?php selected(get_option('hid_commerce_out_of_stock_behavior', 'show'), 'show'); ?>><?php _e('Show as unavailable', 'hid-simple-commerce'); ?></option>
                                <option value="hide" <?php selected(get_option('hid_commerce_out_of_stock_behavior'), 'hide'); ?>><?php _e('Hide product', 'hid-simple-commerce'); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('Allow Backorders', 'hid-simple-commerce'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="hid_commerce_allow_backorders" value="1" <?php checked(get_option('hid_commerce_allow_backorders', '0'), '1'); ?>>
                                <?php _e('Allow customers to purchase out-of-stock products', 'hid-simple-commerce'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

        <?php elseif ($active_tab == 'payments'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('hid_commerce_payments'); ?>
                
                <h2><?php _e('Bank Transfer', 'hid-simple-commerce'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable Bank Transfer', 'hid-simple-commerce'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="hid_commerce_bank_transfer_enabled" value="1" <?php checked(get_option('hid_commerce_bank_transfer_enabled', '1'), '1'); ?>>
                                <?php _e('Enable bank transfer payments', 'hid-simple-commerce'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_bank_name"><?php _e('Bank Name', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_bank_name" id="hid_commerce_bank_name" value="<?php echo esc_attr(get_option('hid_commerce_bank_name')); ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_bank_account_number"><?php _e('Account Number', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_bank_account_number" id="hid_commerce_bank_account_number" value="<?php echo esc_attr(get_option('hid_commerce_bank_account_number')); ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_bank_account_name"><?php _e('Account Name', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_bank_account_name" id="hid_commerce_bank_account_name" value="<?php echo esc_attr(get_option('hid_commerce_bank_account_name')); ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_bank_routing_number"><?php _e('Routing Number', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_bank_routing_number" id="hid_commerce_bank_routing_number" value="<?php echo esc_attr(get_option('hid_commerce_bank_routing_number')); ?>" class="regular-text"></td>
                    </tr>
                </table>

                <h2><?php _e('Paystack', 'hid-simple-commerce'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable Paystack', 'hid-simple-commerce'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="hid_commerce_paystack_enabled" value="1" <?php checked(get_option('hid_commerce_paystack_enabled', '0'), '1'); ?>>
                                <?php _e('Enable Paystack payments', 'hid-simple-commerce'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_paystack_public_key"><?php _e('Public Key', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_paystack_public_key" id="hid_commerce_paystack_public_key" value="<?php echo esc_attr(get_option('hid_commerce_paystack_public_key')); ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_paystack_secret_key"><?php _e('Secret Key', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_paystack_secret_key" id="hid_commerce_paystack_secret_key" value="<?php echo esc_attr(get_option('hid_commerce_paystack_secret_key')); ?>" class="regular-text"></td>
                    </tr>
                </table>

                <h2><?php _e('Stripe', 'hid-simple-commerce'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable Stripe', 'hid-simple-commerce'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="hid_commerce_stripe_enabled" value="1" <?php checked(get_option('hid_commerce_stripe_enabled', '0'), '1'); ?>>
                                <?php _e('Enable Stripe payments', 'hid-simple-commerce'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_stripe_publishable_key"><?php _e('Publishable Key', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_stripe_publishable_key" id="hid_commerce_stripe_publishable_key" value="<?php echo esc_attr(get_option('hid_commerce_stripe_publishable_key')); ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_stripe_secret_key"><?php _e('Secret Key', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_stripe_secret_key" id="hid_commerce_stripe_secret_key" value="<?php echo esc_attr(get_option('hid_commerce_stripe_secret_key')); ?>" class="regular-text"></td>
                    </tr>
                </table>

                <h2><?php _e('PayPal', 'hid-simple-commerce'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable PayPal', 'hid-simple-commerce'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="hid_commerce_paypal_enabled" value="1" <?php checked(get_option('hid_commerce_paypal_enabled', '0'), '1'); ?>>
                                <?php _e('Enable PayPal payments', 'hid-simple-commerce'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_paypal_client_id"><?php _e('Client ID', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_paypal_client_id" id="hid_commerce_paypal_client_id" value="<?php echo esc_attr(get_option('hid_commerce_paypal_client_id')); ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_paypal_secret"><?php _e('Secret', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_paypal_secret" id="hid_commerce_paypal_secret" value="<?php echo esc_attr(get_option('hid_commerce_paypal_secret')); ?>" class="regular-text"></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

        <?php elseif ($active_tab == 'email'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('hid_commerce_email'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="hid_commerce_low_stock_alert_recipients"><?php _e('Low Stock Alert Recipients', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="text" name="hid_commerce_low_stock_alert_recipients" id="hid_commerce_low_stock_alert_recipients" value="<?php echo esc_attr(get_option('hid_commerce_low_stock_alert_recipients', get_option('admin_email'))); ?>" class="large-text">
                            <p class="description"><?php _e('Comma-separated email addresses', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

        <?php elseif ($active_tab == 'social'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('hid_commerce_social'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="hid_commerce_instagram_url"><?php _e('Instagram URL', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="url" name="hid_commerce_instagram_url" id="hid_commerce_instagram_url" value="<?php echo esc_attr(get_option('hid_commerce_instagram_url')); ?>" class="regular-text" placeholder="https://instagram.com/yourprofile">
                            <p class="description"><?php _e('Enter your Instagram profile URL', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_facebook_url"><?php _e('Facebook URL', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="url" name="hid_commerce_facebook_url" id="hid_commerce_facebook_url" value="<?php echo esc_attr(get_option('hid_commerce_facebook_url')); ?>" class="regular-text" placeholder="https://facebook.com/yourpage">
                            <p class="description"><?php _e('Enter your Facebook page URL', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_pinterest_url"><?php _e('Pinterest URL', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="url" name="hid_commerce_pinterest_url" id="hid_commerce_pinterest_url" value="<?php echo esc_attr(get_option('hid_commerce_pinterest_url')); ?>" class="regular-text" placeholder="https://pinterest.com/yourprofile">
                            <p class="description"><?php _e('Enter your Pinterest profile URL', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_twitter_url"><?php _e('Twitter/X URL', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="url" name="hid_commerce_twitter_url" id="hid_commerce_twitter_url" value="<?php echo esc_attr(get_option('hid_commerce_twitter_url')); ?>" class="regular-text" placeholder="https://twitter.com/yourprofile">
                            <p class="description"><?php _e('Enter your Twitter/X profile URL', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_youtube_url"><?php _e('YouTube URL', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="url" name="hid_commerce_youtube_url" id="hid_commerce_youtube_url" value="<?php echo esc_attr(get_option('hid_commerce_youtube_url')); ?>" class="regular-text" placeholder="https://youtube.com/@yourchannel">
                            <p class="description"><?php _e('Enter your YouTube channel URL', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hid_commerce_linkedin_url"><?php _e('LinkedIn URL', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <input type="url" name="hid_commerce_linkedin_url" id="hid_commerce_linkedin_url" value="<?php echo esc_attr(get_option('hid_commerce_linkedin_url')); ?>" class="regular-text" placeholder="https://linkedin.com/company/yourcompany">
                            <p class="description"><?php _e('Enter your LinkedIn company URL', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><h3 style="margin: 0; padding-top: 15px; border-top: 1px solid #ccc;"><?php _e('Instagram API Settings', 'hid-simple-commerce'); ?></h3></th>
                        <td><p class="description" style="margin-top: 15px; border-top: 1px solid #ccc; padding-top: 15px;"><?php _e('Required to fetch the dynamic Instagram Feed on the home page.', 'hid-simple-commerce'); ?></p></td>
                    </tr>
                    <tr>
                        <th><label for="hid_commerce_ig_app_id"><?php _e('Instagram App ID', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_ig_app_id" id="hid_commerce_ig_app_id" value="<?php echo esc_attr(get_option('hid_commerce_ig_app_id')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="hid_commerce_ig_app_secret"><?php _e('Instagram App Secret', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_ig_app_secret" id="hid_commerce_ig_app_secret" value="<?php echo esc_attr(get_option('hid_commerce_ig_app_secret')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="hid_commerce_ig_token"><?php _e('Instagram Access Token', 'hid-simple-commerce'); ?></label></th>
                        <td><input type="text" name="hid_commerce_ig_token" id="hid_commerce_ig_token" value="<?php echo esc_attr(get_option('hid_commerce_ig_token')); ?>" class="large-text"></td>
                    </tr>
                </table>
                <p class="description" style="margin-top: 20px;">
                    <strong><?php _e('Note:', 'hid-simple-commerce'); ?></strong> <?php _e('Leave any field empty if you don\'t want to display that social media icon on your site.', 'hid-simple-commerce'); ?>
                </p>
                <?php submit_button(); ?>
            </form>
        <?php endif; ?>
    </div>
</div>

