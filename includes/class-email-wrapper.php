<?php
/**
 * Simple Email Wrapper for sending branded emails
 *
 * @package HID_Simple_Commerce
 */

class HID_Commerce_Email {

    /**
     * Send a branded email using the theme's email template
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $message Email message content (HTML)
     * @return bool True if email sent successfully, false otherwise
     */
    public static function send_email($to, $subject, $message) {
        // Use the branded email template from HID_Commerce_Email_Handler
        $html_email = HID_Commerce_Email_Handler::get_email_template($subject, $message);
        
        // Set headers for HTML email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        );
        
        // Send email with error handling
        try {
            $result = wp_mail($to, $subject, $html_email, $headers);
            
            // Log if sending failed
            if (!$result) {
                error_log('HID Commerce Email: Failed to send email to ' . $to . ' with subject: ' . $subject);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log('HID Commerce Email Exception: ' . $e->getMessage());
            return false;
        }
    }
}

