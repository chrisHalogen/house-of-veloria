<?php
/**
 * Admin Pages for Contact Inquiries and Jewelry Requests
 *
 * @package HID_Simple_Commerce
 */

// Add menu pages
add_action('admin_menu', 'hid_inquiries_admin_menu', 25);

function hid_inquiries_admin_menu() {
    // Contact Inquiries submenu
    add_submenu_page(
        'hid-commerce',
        __('Contact Inquiries', 'hid-simple-commerce'),
        __('Contact Inquiries', 'hid-simple-commerce'),
        'manage_options',
        'hid-contact-inquiries',
        'hid_contact_inquiries_page'
    );

    // Jewelry Requests submenu
    add_submenu_page(
        'hid-commerce',
        __('Jewelry Requests', 'hid-simple-commerce'),
        __('Jewelry Requests', 'hid-simple-commerce'),
        'manage_options',
        'hid-jewelry-requests',
        'hid_jewelry_requests_page'
    );
}

/**
 * Contact Inquiries Page
 */
function hid_contact_inquiries_page() {
    // Check if viewing a single inquiry
    if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['inquiry_id'])) {
        hid_view_contact_inquiry();
        return;
    }

    // Show list table
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Contact Inquiries', 'hid-simple-commerce'); ?></h1>
        
        <?php
        // Show messages
        if (isset($_GET['message'])) {
            $message = sanitize_text_field($_GET['message']);
            if ($message === 'deleted') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Inquiry deleted successfully.', 'hid-simple-commerce') . '</p></div>';
            } elseif ($message === 'bulk_deleted') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Inquiries deleted successfully.', 'hid-simple-commerce') . '</p></div>';
            } elseif ($message === 'marked_read') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Inquiries marked as read.', 'hid-simple-commerce') . '</p></div>';
            } elseif ($message === 'marked_unread') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Inquiries marked as unread.', 'hid-simple-commerce') . '</p></div>';
            } elseif ($message === 'updated') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Inquiry updated successfully.', 'hid-simple-commerce') . '</p></div>';
            }
        }
        ?>
        
        <hr class="wp-header-end">
        
        <form method="post">
            <?php
            require_once get_template_directory() . '/admin/class-contact-inquiries-list-table.php';
            $list_table = new HID_Contact_Inquiries_List_Table();
            $list_table->prepare_items();
            $list_table->search_box(__('Search', 'hid-simple-commerce'), 'inquiry');
            $list_table->display();
            ?>
        </form>
    </div>
    <?php
}

/**
 * View Single Contact Inquiry
 */
function hid_view_contact_inquiry() {
    global $wpdb;
    $table = $wpdb->prefix . 'hid_contact_inquiries';
    $inquiry_id = absint($_GET['inquiry_id']);
    
    $inquiry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $inquiry_id));
    
    if (!$inquiry) {
        echo '<div class="wrap"><h1>' . __('Inquiry Not Found', 'hid-simple-commerce') . '</h1></div>';
        return;
    }

    // Mark as read
    if ($inquiry->status === 'unread') {
        $wpdb->update($table, array('status' => 'read'), array('id' => $inquiry_id), array('%s'), array('%d'));
        $inquiry->status = 'read';
    }

    // Handle form submission for admin notes
    if (isset($_POST['save_notes']) && check_admin_referer('update_inquiry_' . $inquiry_id)) {
        $admin_notes = sanitize_textarea_field($_POST['admin_notes']);
        $new_status = sanitize_text_field($_POST['status']);
        
        $wpdb->update(
            $table,
            array('admin_notes' => $admin_notes, 'status' => $new_status),
            array('id' => $inquiry_id),
            array('%s', '%s'),
            array('%d')
        );
        
        wp_safe_redirect(add_query_arg('message', 'updated', wp_get_referer()));
        exit;
    }

    $subject_labels = array(
        'general' => __('General Inquiry', 'hid-simple-commerce'),
        'order'   => __('Order Question', 'hid-simple-commerce'),
        'product' => __('Product Information', 'hid-simple-commerce'),
        'return'  => __('Returns & Exchanges', 'hid-simple-commerce'),
        'other'   => __('Other', 'hid-simple-commerce'),
    );
    
    ?>
    <div class="wrap">
        <h1><?php printf(__('Contact Inquiry #%d', 'hid-simple-commerce'), $inquiry->id); ?></h1>
        <a href="<?php echo admin_url('admin.php?page=hid-contact-inquiries'); ?>" class="page-title-action"><?php _e('← Back to List', 'hid-simple-commerce'); ?></a>
        
        <div style="margin-top: 20px; background: white; padding: 20px; border: 1px solid #ccc;">
            <table class="form-table">
                <tr>
                    <th><?php _e('Date Received:', 'hid-simple-commerce'); ?></th>
                    <td><?php echo date('F j, Y g:i A', strtotime($inquiry->created_at)); ?></td>
                </tr>
                <tr>
                    <th><?php _e('Name:', 'hid-simple-commerce'); ?></th>
                    <td><?php echo esc_html($inquiry->name); ?></td>
                </tr>
                <tr>
                    <th><?php _e('Email:', 'hid-simple-commerce'); ?></th>
                    <td><a href="mailto:<?php echo esc_attr($inquiry->email); ?>"><?php echo esc_html($inquiry->email); ?></a></td>
                </tr>
                <tr>
                    <th><?php _e('Subject:', 'hid-simple-commerce'); ?></th>
                    <td><?php echo isset($subject_labels[$inquiry->subject]) ? $subject_labels[$inquiry->subject] : esc_html($inquiry->subject); ?></td>
                </tr>
                <tr>
                    <th><?php _e('Message:', 'hid-simple-commerce'); ?></th>
                    <td style="white-space: pre-wrap;"><?php echo esc_html($inquiry->message); ?></td>
                </tr>
            </table>

            <hr style="margin: 30px 0;">

            <form method="post">
                <?php wp_nonce_field('update_inquiry_' . $inquiry_id); ?>
                
                <table class="form-table">
                    <tr>
                        <th><label for="status"><?php _e('Status:', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <select name="status" id="status">
                                <option value="unread" <?php selected($inquiry->status, 'unread'); ?>><?php _e('Unread', 'hid-simple-commerce'); ?></option>
                                <option value="read" <?php selected($inquiry->status, 'read'); ?>><?php _e('Read', 'hid-simple-commerce'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="admin_notes"><?php _e('Admin Notes:', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <textarea name="admin_notes" id="admin_notes" rows="5" style="width: 100%; max-width: 600px;"><?php echo esc_textarea($inquiry->admin_notes ?? ''); ?></textarea>
                            <p class="description"><?php _e('Internal notes (not visible to customer)', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" name="save_notes" class="button button-primary"><?php _e('Save Changes', 'hid-simple-commerce'); ?></button>
                </p>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Jewelry Requests Page
 */
function hid_jewelry_requests_page() {
    // Check if viewing a single request
    if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['request_id'])) {
        hid_view_jewelry_request();
        return;
    }

    // Show list table
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Jewelry Requests', 'hid-simple-commerce'); ?></h1>
        
        <?php
        // Show messages
        if (isset($_GET['message'])) {
            $message = sanitize_text_field($_GET['message']);
            if ($message === 'deleted') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Request deleted successfully.', 'hid-simple-commerce') . '</p></div>';
            } elseif ($message === 'bulk_deleted') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Requests deleted successfully.', 'hid-simple-commerce') . '</p></div>';
            } elseif ($message === 'status_updated') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Request status updated.', 'hid-simple-commerce') . '</p></div>';
            } elseif ($message === 'updated') {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Request updated successfully.', 'hid-simple-commerce') . '</p></div>';
            }
        }
        ?>
        
        <hr class="wp-header-end">
        
        <form method="post">
            <?php
            require_once get_template_directory() . '/admin/class-jewelry-requests-list-table.php';
            $list_table = new HID_Jewelry_Requests_List_Table();
            $list_table->prepare_items();
            $list_table->search_box(__('Search', 'hid-simple-commerce'), 'request');
            $list_table->display();
            ?>
        </form>
    </div>
    <?php
}

/**
 * View Single Jewelry Request
 */
function hid_view_jewelry_request() {
    global $wpdb;
    $table = $wpdb->prefix . 'hid_jewelry_requests';
    $request_id = absint($_GET['request_id']);
    
    $request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $request_id));
    
    if (!$request) {
        echo '<div class="wrap"><h1>' . __('Request Not Found', 'hid-simple-commerce') . '</h1></div>';
        return;
    }

    // Handle form submission for admin notes
    if (isset($_POST['save_notes']) && check_admin_referer('update_request_' . $request_id)) {
        $admin_notes = sanitize_textarea_field($_POST['admin_notes']);
        $new_status = sanitize_text_field($_POST['status']);
        
        $wpdb->update(
            $table,
            array('admin_notes' => $admin_notes, 'status' => $new_status),
            array('id' => $request_id),
            array('%s', '%s'),
            array('%d')
        );
        
        wp_safe_redirect(add_query_arg('message', 'updated', wp_get_referer()));
        exit;
    }
    
    ?>
    <div class="wrap">
        <h1><?php printf(__('Jewelry Request #%d', 'hid-simple-commerce'), $request->id); ?></h1>
        <a href="<?php echo admin_url('admin.php?page=hid-jewelry-requests'); ?>" class="page-title-action"><?php _e('← Back to List', 'hid-simple-commerce'); ?></a>
        
        <div style="margin-top: 20px; background: white; padding: 20px; border: 1px solid #ccc;">
            <table class="form-table">
                <tr>
                    <th><?php _e('Date Submitted:', 'hid-simple-commerce'); ?></th>
                    <td><?php echo date('F j, Y g:i A', strtotime($request->created_at)); ?></td>
                </tr>
                <tr>
                    <th><?php _e('Name:', 'hid-simple-commerce'); ?></th>
                    <td><?php echo esc_html($request->name); ?></td>
                </tr>
                <tr>
                    <th><?php _e('Email:', 'hid-simple-commerce'); ?></th>
                    <td><a href="mailto:<?php echo esc_attr($request->email); ?>"><?php echo esc_html($request->email); ?></a></td>
                </tr>
                <tr>
                    <th><?php _e('Jewelry Type:', 'hid-simple-commerce'); ?></th>
                    <td><?php echo esc_html(ucfirst($request->jewelry_type)); ?></td>
                </tr>
                <tr>
                    <th><?php _e('Budget Range:', 'hid-simple-commerce'); ?></th>
                    <td><?php echo $request->budget_range ? esc_html($request->budget_range) : '<em>' . __('Not specified', 'hid-simple-commerce') . '</em>'; ?></td>
                </tr>
                <tr>
                    <th><?php _e('Description:', 'hid-simple-commerce'); ?></th>
                    <td style="white-space: pre-wrap;"><?php echo esc_html($request->description); ?></td>
                </tr>
            </table>

            <hr style="margin: 30px 0;">

            <form method="post">
                <?php wp_nonce_field('update_request_' . $request_id); ?>
                
                <table class="form-table">
                    <tr>
                        <th><label for="status"><?php _e('Status:', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <select name="status" id="status">
                                <option value="pending" <?php selected($request->status, 'pending'); ?>><?php _e('Pending', 'hid-simple-commerce'); ?></option>
                                <option value="reviewing" <?php selected($request->status, 'reviewing'); ?>><?php _e('Reviewing', 'hid-simple-commerce'); ?></option>
                                <option value="contacted" <?php selected($request->status, 'contacted'); ?>><?php _e('Contacted', 'hid-simple-commerce'); ?></option>
                                <option value="completed" <?php selected($request->status, 'completed'); ?>><?php _e('Completed', 'hid-simple-commerce'); ?></option>
                                <option value="cancelled" <?php selected($request->status, 'cancelled'); ?>><?php _e('Cancelled', 'hid-simple-commerce'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="admin_notes"><?php _e('Admin Notes:', 'hid-simple-commerce'); ?></label></th>
                        <td>
                            <textarea name="admin_notes" id="admin_notes" rows="5" style="width: 100%; max-width: 600px;"><?php echo esc_textarea($request->admin_notes ?? ''); ?></textarea>
                            <p class="description"><?php _e('Internal notes (not visible to customer)', 'hid-simple-commerce'); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" name="save_notes" class="button button-primary"><?php _e('Save Changes', 'hid-simple-commerce'); ?></button>
                </p>
            </form>
        </div>
    </div>
    <?php
}

