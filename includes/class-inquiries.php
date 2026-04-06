<?php
/**
 * Contact Inquiries and Jewelry Requests Handler
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Inquiries {

    /**
     * Initialize the class
     */
    public static function init() {
        // AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_hid_submit_contact', array(__CLASS__, 'submit_contact_inquiry'));
        add_action('wp_ajax_nopriv_hid_submit_contact', array(__CLASS__, 'submit_contact_inquiry'));
        
        add_action('wp_ajax_hid_submit_jewelry_request', array(__CLASS__, 'submit_jewelry_request'));
        add_action('wp_ajax_nopriv_hid_submit_jewelry_request', array(__CLASS__, 'submit_jewelry_request'));
        
        // Admin actions
        add_action('admin_post_hid_delete_contact_inquiry', array(__CLASS__, 'delete_contact_inquiry'));
        add_action('admin_post_hid_delete_jewelry_request', array(__CLASS__, 'delete_jewelry_request'));
        
        // Email scheduled events
        add_action('hid_send_contact_inquiry_emails', array(__CLASS__, 'send_contact_inquiry_emails'), 10, 5);
        add_action('hid_send_jewelry_request_emails', array(__CLASS__, 'send_jewelry_request_emails'), 10, 6);
    }

    /**
     * Submit contact inquiry via AJAX
     */
    public static function submit_contact_inquiry() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hid_contact_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'hid-simple-commerce')));
            return;
        }

        // Verify captcha
        if (!isset($_POST['captcha_answer']) || !isset($_POST['captcha_expected'])) {
            wp_send_json_error(array('message' => __('Captcha verification failed.', 'hid-simple-commerce')));
            return;
        }

        if ((int)$_POST['captcha_answer'] !== (int)$_POST['captcha_expected']) {
            wp_send_json_error(array('message' => __('Incorrect captcha answer. Please try again.', 'hid-simple-commerce')));
            return;
        }

        // Sanitize input
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);

        // Validate
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            wp_send_json_error(array('message' => __('Please fill in all required fields.', 'hid-simple-commerce')));
            return;
        }

        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Please enter a valid email address.', 'hid-simple-commerce')));
            return;
        }

        // Insert into database
        global $wpdb;
        $table = $wpdb->prefix . 'hid_contact_inquiries';
        
        $result = $wpdb->insert(
            $table,
            array(
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'status' => 'unread',
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => __('Failed to submit inquiry. Please try again.', 'hid-simple-commerce')));
            return;
        }

        $inquiry_id = $wpdb->insert_id;

        // Schedule emails to be sent via WordPress cron (asynchronously)
        wp_schedule_single_event(time(), 'hid_send_contact_inquiry_emails', array($inquiry_id, $name, $email, $subject, $message));
        
        // Trigger WordPress cron to process the scheduled email immediately
        spawn_cron();

        wp_send_json_success(array(
            'message' => __('Thank you! Your message has been sent successfully. We\'ll get back to you within 24 hours.', 'hid-simple-commerce')
        ));
    }

    /**
     * Submit jewelry request via AJAX
     */
    public static function submit_jewelry_request() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hid_request_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'hid-simple-commerce')));
            return;
        }

        // Verify captcha
        if (!isset($_POST['captcha_answer']) || !isset($_POST['captcha_expected'])) {
            wp_send_json_error(array('message' => __('Captcha verification failed.', 'hid-simple-commerce')));
            return;
        }

        if ((int)$_POST['captcha_answer'] !== (int)$_POST['captcha_expected']) {
            wp_send_json_error(array('message' => __('Incorrect captcha answer. Please try again.', 'hid-simple-commerce')));
            return;
        }

        // Sanitize input
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $jewelry_type = sanitize_text_field($_POST['type']);
        $budget_range = sanitize_text_field($_POST['budget']);
        $description = sanitize_textarea_field($_POST['description']);

        // Validate
        if (empty($name) || empty($email) || empty($jewelry_type) || empty($description)) {
            wp_send_json_error(array('message' => __('Please fill in all required fields.', 'hid-simple-commerce')));
            return;
        }

        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Please enter a valid email address.', 'hid-simple-commerce')));
            return;
        }

        // Insert into database
        global $wpdb;
        $table = $wpdb->prefix . 'hid_jewelry_requests';
        
        $result = $wpdb->insert(
            $table,
            array(
                'name' => $name,
                'email' => $email,
                'jewelry_type' => $jewelry_type,
                'budget_range' => $budget_range,
                'description' => $description,
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => __('Failed to submit request. Please try again.', 'hid-simple-commerce')));
            return;
        }

        $request_id = $wpdb->insert_id;

        // Schedule emails to be sent via WordPress cron (asynchronously)
        wp_schedule_single_event(time(), 'hid_send_jewelry_request_emails', array($request_id, $name, $email, $jewelry_type, $budget_range, $description));
        
        // Trigger WordPress cron to process the scheduled email immediately
        spawn_cron();

        wp_send_json_success(array(
            'message' => __('Thank you! Your jewelry request has been submitted. We\'ll review it and get back to you soon.', 'hid-simple-commerce')
        ));
    }

    /**
     * Send contact inquiry emails
     */
    public static function send_contact_inquiry_emails($inquiry_id, $name, $email, $subject, $message) {
        $admin_email = get_option('hid_commerce_admin_email', get_option('admin_email'));
        
        // Email to admin
        $admin_subject = sprintf('[%s] New Contact Inquiry #%d', get_bloginfo('name'), $inquiry_id);
        $admin_message = '
            <h2>New Contact Inquiry</h2>
            <p><strong>Inquiry ID:</strong> #' . $inquiry_id . '</p>
            <p><strong>From:</strong> ' . esc_html($name) . ' (' . esc_html($email) . ')</p>
            <p><strong>Subject:</strong> ' . esc_html($subject) . '</p>
            <p><strong>Message:</strong></p>
            <p>' . nl2br(esc_html($message)) . '</p>
            <p><a href="' . admin_url('admin.php?page=hid-contact-inquiries&action=view&inquiry_id=' . $inquiry_id) . '">View in Admin</a></p>
        ';
        
        HID_Commerce_Email::send_email($admin_email, $admin_subject, $admin_message);

        // Email to customer
        $customer_subject = sprintf('[%s] We received your message', get_bloginfo('name'));
        $customer_message = '
            <h2>Thank You for Contacting Us</h2>
            <p>Dear ' . esc_html($name) . ',</p>
            <p>We have received your inquiry and will respond within 24 hours on business days.</p>
            <p><strong>Your Message:</strong></p>
            <p>' . nl2br(esc_html($message)) . '</p>
            <p>If you have any urgent concerns, please feel free to call us directly.</p>
            <p>Best regards,<br>' . get_bloginfo('name') . '</p>
        ';
        
        HID_Commerce_Email::send_email($email, $customer_subject, $customer_message);
    }

    /**
     * Send jewelry request emails
     */
    public static function send_jewelry_request_emails($request_id, $name, $email, $jewelry_type, $budget_range, $description) {
        $admin_email = get_option('hid_commerce_admin_email', get_option('admin_email'));
        
        // Email to admin
        $admin_subject = sprintf('[%s] New Jewelry Request #%d', get_bloginfo('name'), $request_id);
        $admin_message = '
            <h2>New Jewelry Request</h2>
            <p><strong>Request ID:</strong> #' . $request_id . '</p>
            <p><strong>From:</strong> ' . esc_html($name) . ' (' . esc_html($email) . ')</p>
            <p><strong>Jewelry Type:</strong> ' . esc_html(ucfirst($jewelry_type)) . '</p>
            <p><strong>Budget Range:</strong> ' . esc_html($budget_range ? $budget_range : 'Not specified') . '</p>
            <p><strong>Description:</strong></p>
            <p>' . nl2br(esc_html($description)) . '</p>
            <p><a href="' . admin_url('admin.php?page=hid-jewelry-requests&action=view&request_id=' . $request_id) . '">View in Admin</a></p>
        ';
        
        HID_Commerce_Email::send_email($admin_email, $admin_subject, $admin_message);

        // Email to customer
        $customer_subject = sprintf('[%s] We received your jewelry request', get_bloginfo('name'));
        $customer_message = '
            <h2>Thank You for Your Request</h2>
            <p>Dear ' . esc_html($name) . ',</p>
            <p>We have received your custom jewelry request and our team is reviewing it.</p>
            <p><strong>Request Details:</strong></p>
            <ul>
                <li><strong>Type:</strong> ' . esc_html(ucfirst($jewelry_type)) . '</li>
                <li><strong>Budget:</strong> ' . esc_html($budget_range ? $budget_range : 'Not specified') . '</li>
            </ul>
            <p><strong>Your Description:</strong></p>
            <p>' . nl2br(esc_html($description)) . '</p>
            <p>We\'ll search our network of artisans and get back to you with options that match your vision.</p>
            <p>Best regards,<br>' . get_bloginfo('name') . '</p>
        ';
        
        HID_Commerce_Email::send_email($email, $customer_subject, $customer_message);
    }

    /**
     * Delete contact inquiry
     */
    public static function delete_contact_inquiry() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized access', 'hid-simple-commerce'));
        }

        if (!isset($_GET['inquiry_id']) || !check_admin_referer('delete_inquiry_' . absint($_GET['inquiry_id']))) {
            wp_die(__('Security check failed', 'hid-simple-commerce'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'hid_contact_inquiries';
        $inquiry_id = absint($_GET['inquiry_id']);

        $wpdb->delete($table, array('id' => $inquiry_id), array('%d'));

        wp_safe_redirect(add_query_arg('message', 'deleted', admin_url('admin.php?page=hid-contact-inquiries')));
        exit;
    }

    /**
     * Delete jewelry request
     */
    public static function delete_jewelry_request() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized access', 'hid-simple-commerce'));
        }

        if (!isset($_GET['request_id']) || !check_admin_referer('delete_request_' . absint($_GET['request_id']))) {
            wp_die(__('Security check failed', 'hid-simple-commerce'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'hid_jewelry_requests';
        $request_id = absint($_GET['request_id']);

        $wpdb->delete($table, array('id' => $request_id), array('%d'));

        wp_safe_redirect(add_query_arg('message', 'deleted', admin_url('admin.php?page=hid-jewelry-requests')));
        exit;
    }
}

// Initialize
HID_Commerce_Inquiries::init();
