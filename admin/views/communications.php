<?php
/**
 * Communications Admin View - Bulk Email
 *
 * @package HID_Simple_Commerce
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['send_bulk_email']) && check_admin_referer('hid_bulk_email_nonce')) {
    $recipient_type = sanitize_text_field($_POST['recipient_type']);
    $subject = sanitize_text_field($_POST['email_subject']);
    $message = wp_kses_post($_POST['email_message']);
    
    if (empty($subject) || empty($message)) {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Subject and message are required.', 'hid-simple-commerce') . '</p></div>';
    } else {
        // Get recipients based on type
        global $wpdb;
        $customers_table = $wpdb->prefix . 'hid_customers';
        
        $where = '';
        if ($recipient_type === 'customers') {
            $where = "WHERE customer_type = 'customer'";
        } elseif ($recipient_type === 'subscribers') {
            $where = "WHERE customer_type = 'subscriber'";
        }
        
        $recipients = $wpdb->get_results("SELECT email, name FROM $customers_table $where");
        
        if (empty($recipients)) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . __('No recipients found.', 'hid-simple-commerce') . '</p></div>';
        } else {
            // Prepare recipients array
            $recipients_array = array();
            foreach ($recipients as $recipient) {
                $recipients_array[] = array(
                    'email' => $recipient->email,
                    'name' => $recipient->name
                );
            }
            
            // Send bulk email
            set_time_limit(600); // 10 minutes for large batches
            $result = HID_Commerce_Email_Handler::send_bulk_email($recipients_array, $subject, $message);
            
            echo '<div class="notice notice-success is-dismissible">
                <p>' . sprintf(
                    __('Email sent successfully to %d recipient(s). Failed: %d', 'hid-simple-commerce'),
                    $result['success'],
                    $result['failure']
                ) . '</p>
            </div>';
        }
    }
}
?>

<div class="wrap">
    <h1><?php _e('Communications', 'hid-simple-commerce'); ?></h1>
    <p class="description"><?php _e('Send bulk emails to your customers and subscribers.', 'hid-simple-commerce'); ?></p>

    <style>
        .email-form-container {
            background: #fff;
            padding: 30px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 900px;
        }
        .form-section {
            margin-bottom: 25px;
        }
        .form-section label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #23282d;
        }
        .form-section .description {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
        .form-section input[type="text"],
        .form-section select,
        .form-section textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-section textarea {
            min-height: 250px;
            font-family: inherit;
        }
        .preview-section {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .preview-section h4 {
            margin-top: 0;
            color: #470108;
        }
        .recipients-info {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .recipients-info p {
            margin: 5px 0;
            color: #0c5460;
        }
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            color: #856404;
        }
        .send-button {
            background-color: #470108;
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .send-button:hover {
            background-color: #6a0110;
        }
        .preview-button {
            margin-left: 10px;
            padding: 12px 24px;
        }
    </style>

    <?php
    // Get recipient counts
    global $wpdb;
    $customers_table = $wpdb->prefix . 'hid_customers';
    $total_all = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table");
    $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table WHERE customer_type = 'customer'");
    $total_subscribers = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table WHERE customer_type = 'subscriber'");
    ?>

    <div class="recipients-info">
        <p><strong><?php _e('Total Recipients Available:', 'hid-simple-commerce'); ?></strong></p>
        <p><?php printf(__('All: %d | Customers: %d | Subscribers: %d', 'hid-simple-commerce'), $total_all, $total_customers, $total_subscribers); ?></p>
    </div>

    <div class="info-box">
        <p><strong><?php _e('Important:', 'hid-simple-commerce'); ?></strong> <?php _e('Emails will be personalized with recipient names when available. For recipients without names, a generic greeting will be used.', 'hid-simple-commerce'); ?></p>
    </div>

    <div class="email-form-container">
        <form method="post" action="">
            <?php wp_nonce_field('hid_bulk_email_nonce'); ?>
            
            <div class="form-section">
                <label for="recipient_type"><?php _e('Send To:', 'hid-simple-commerce'); ?></label>
                <select name="recipient_type" id="recipient_type" required>
                    <option value="all"><?php printf(__('All (%d)', 'hid-simple-commerce'), $total_all); ?></option>
                    <option value="customers"><?php printf(__('Customers Only (%d)', 'hid-simple-commerce'), $total_customers); ?></option>
                    <option value="subscribers"><?php printf(__('Subscribers Only (%d)', 'hid-simple-commerce'), $total_subscribers); ?></option>
                </select>
                <p class="description"><?php _e('Select the audience for this email.', 'hid-simple-commerce'); ?></p>
            </div>

            <div class="form-section">
                <label for="email_subject"><?php _e('Email Subject:', 'hid-simple-commerce'); ?></label>
                <input type="text" name="email_subject" id="email_subject" placeholder="<?php esc_attr_e('Enter email subject...', 'hid-simple-commerce'); ?>" required />
                <p class="description"><?php _e('This will be the subject line of your email.', 'hid-simple-commerce'); ?></p>
            </div>

            <div class="form-section">
                <label for="email_message"><?php _e('Email Message:', 'hid-simple-commerce'); ?></label>
                <?php
                $settings = array(
                    'textarea_name' => 'email_message',
                    'textarea_rows' => 15,
                    'media_buttons' => false,
                    'teeny' => false,
                    'tinymce' => array(
                        'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo',
                        'toolbar2' => '',
                    ),
                );
                wp_editor('', 'email_message', $settings);
                ?>
                <p class="description">
                    <?php _e('Write your email message. HTML formatting is supported. Personalization: The greeting "Hello [Name]," will be added automatically when a name is available.', 'hid-simple-commerce'); ?>
                </p>
            </div>

            <div class="preview-section">
                <h4><?php _e('Email Preview', 'hid-simple-commerce'); ?></h4>
                <p style="color: #666; font-size: 13px;">
                    <?php _e('Your email will be wrapped in our branded House of Veloria template with elegant styling matching your website design.', 'hid-simple-commerce'); ?>
                </p>
            </div>

            <div class="form-section" style="margin-top: 30px;">
                <button type="submit" name="send_bulk_email" class="send-button" onclick="return confirm('<?php esc_js(_e('Are you sure you want to send this email? This action cannot be undone.', 'hid-simple-commerce')); ?>');">
                    <span class="dashicons dashicons-email-alt" style="margin-top: 4px;"></span>
                    <?php _e('Send Email', 'hid-simple-commerce'); ?>
                </button>
                <p class="description" style="margin-top: 15px; color: #d63638;">
                    <strong><?php _e('Warning:', 'hid-simple-commerce'); ?></strong> <?php _e('This will send emails to all selected recipients. Please review your message carefully before sending.', 'hid-simple-commerce'); ?>
                </p>
            </div>
        </form>
    </div>
</div>

