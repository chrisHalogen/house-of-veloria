<?php
/**
 * Contact Inquiries List Table
 *
 * @package HID_Simple_Commerce
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class HID_Contact_Inquiries_List_Table extends WP_List_Table {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => 'inquiry',
            'plural'   => 'inquiries',
            'ajax'     => false
        ));
    }

    /**
     * Get columns
     */
    public function get_columns() {
        return array(
            'cb'         => '<input type="checkbox" />',
            'id'         => __('ID', 'hid-simple-commerce'),
            'name'       => __('Name', 'hid-simple-commerce'),
            'email'      => __('Email', 'hid-simple-commerce'),
            'subject'    => __('Subject', 'hid-simple-commerce'),
            'status'     => __('Status', 'hid-simple-commerce'),
            'created_at' => __('Date', 'hid-simple-commerce'),
            'actions'    => __('Actions', 'hid-simple-commerce'),
        );
    }

    /**
     * Get sortable columns
     */
    public function get_sortable_columns() {
        return array(
            'id'         => array('id', true),
            'name'       => array('name', false),
            'email'      => array('email', false),
            'status'     => array('status', false),
            'created_at' => array('created_at', false),
        );
    }

    /**
     * Column default
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
                return '#' . $item->id;
            case 'name':
                return esc_html($item->name);
            case 'email':
                return '<a href="mailto:' . esc_attr($item->email) . '">' . esc_html($item->email) . '</a>';
            case 'subject':
                $subject_labels = array(
                    'general' => __('General Inquiry', 'hid-simple-commerce'),
                    'order'   => __('Order Question', 'hid-simple-commerce'),
                    'product' => __('Product Information', 'hid-simple-commerce'),
                    'return'  => __('Returns & Exchanges', 'hid-simple-commerce'),
                    'other'   => __('Other', 'hid-simple-commerce'),
                );
                return isset($subject_labels[$item->subject]) ? $subject_labels[$item->subject] : esc_html($item->subject);
            case 'status':
                if ($item->status === 'read') {
                    return '<span class="hid-badge" style="background: #2271b1; color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px;">' . __('Read', 'hid-simple-commerce') . '</span>';
                } else {
                    return '<span class="hid-badge" style="background: #d63638; color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px;">' . __('Unread', 'hid-simple-commerce') . '</span>';
                }
            case 'created_at':
                return date('M j, Y g:i A', strtotime($item->created_at));
            default:
                return '';
        }
    }

    /**
     * Column checkbox
     */
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="inquiry[]" value="%s" />', $item->id);
    }

    /**
     * Column actions
     */
    public function column_actions($item) {
        $view_url = add_query_arg(array(
            'page' => 'hid-contact-inquiries',
            'action' => 'view',
            'inquiry_id' => $item->id
        ), admin_url('admin.php'));

        $delete_url = wp_nonce_url(
            admin_url('admin-post.php?action=hid_delete_contact_inquiry&inquiry_id=' . $item->id),
            'delete_inquiry_' . $item->id
        );

        return '
            <a href="' . esc_url($view_url) . '" class="button button-small">' . __('View', 'hid-simple-commerce') . '</a>
            <a href="' . esc_url($delete_url) . '" class="button button-small" onclick="return confirm(\'' . esc_js(__('Are you sure you want to delete this inquiry?', 'hid-simple-commerce')) . '\');">' . __('Delete', 'hid-simple-commerce') . '</a>
        ';
    }

    /**
     * Get bulk actions
     */
    public function get_bulk_actions() {
        return array(
            'bulk_delete' => __('Delete', 'hid-simple-commerce'),
            'mark_read'   => __('Mark as Read', 'hid-simple-commerce'),
            'mark_unread' => __('Mark as Unread', 'hid-simple-commerce'),
        );
    }

    /**
     * Process bulk actions
     */
    public function process_bulk_action() {
        global $wpdb;
        $table = $wpdb->prefix . 'hid_contact_inquiries';

        // Bulk delete
        if ('bulk_delete' === $this->current_action()) {
            if (isset($_POST['inquiry']) && is_array($_POST['inquiry'])) {
                check_admin_referer('bulk-inquiries');
                
                foreach ($_POST['inquiry'] as $inquiry_id) {
                    $wpdb->delete($table, array('id' => absint($inquiry_id)), array('%d'));
                }
                
                wp_safe_redirect(add_query_arg('message', 'bulk_deleted', remove_query_arg(array('action', 'inquiry', '_wpnonce', '_wp_http_referer'))));
                exit;
            }
        }

        // Mark as read
        if ('mark_read' === $this->current_action()) {
            if (isset($_POST['inquiry']) && is_array($_POST['inquiry'])) {
                check_admin_referer('bulk-inquiries');
                
                foreach ($_POST['inquiry'] as $inquiry_id) {
                    $wpdb->update($table, array('status' => 'read'), array('id' => absint($inquiry_id)), array('%s'), array('%d'));
                }
                
                wp_safe_redirect(add_query_arg('message', 'marked_read', remove_query_arg(array('action', 'inquiry', '_wpnonce', '_wp_http_referer'))));
                exit;
            }
        }

        // Mark as unread
        if ('mark_unread' === $this->current_action()) {
            if (isset($_POST['inquiry']) && is_array($_POST['inquiry'])) {
                check_admin_referer('bulk-inquiries');
                
                foreach ($_POST['inquiry'] as $inquiry_id) {
                    $wpdb->update($table, array('status' => 'unread'), array('id' => absint($inquiry_id)), array('%s'), array('%d'));
                }
                
                wp_safe_redirect(add_query_arg('message', 'marked_unread', remove_query_arg(array('action', 'inquiry', '_wpnonce', '_wp_http_referer'))));
                exit;
            }
        }
    }

    /**
     * Extra controls
     */
    protected function extra_tablenav($which) {
        if ($which === 'top') {
            $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
            ?>
            <div class="alignleft actions">
                <select name="status" id="filter-by-status">
                    <option value=""><?php _e('All Statuses', 'hid-simple-commerce'); ?></option>
                    <option value="unread" <?php selected($status, 'unread'); ?>><?php _e('Unread', 'hid-simple-commerce'); ?></option>
                    <option value="read" <?php selected($status, 'read'); ?>><?php _e('Read', 'hid-simple-commerce'); ?></option>
                </select>
                <?php submit_button(__('Filter', 'hid-simple-commerce'), '', 'filter_action', false); ?>
            </div>
            <?php
        }
    }

    /**
     * Prepare items
     */
    public function prepare_items() {
        global $wpdb;
        $table = $wpdb->prefix . 'hid_contact_inquiries';

        // Process bulk actions
        $this->process_bulk_action();

        // Pagination
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        // Search
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        // Filter by status
        $status = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : '';

        // Sorting
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'created_at';
        $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';

        // Validate orderby
        $allowed_orderby = array('id', 'name', 'email', 'status', 'created_at');
        if (!in_array($orderby, $allowed_orderby)) {
            $orderby = 'created_at';
        }

        // Validate order
        $order = strtoupper($order);
        if (!in_array($order, array('ASC', 'DESC'))) {
            $order = 'DESC';
        }

        // Build WHERE clause
        $where = array('1=1');
        $where_values = array();

        if (!empty($search)) {
            $where[] = '(name LIKE %s OR email LIKE %s OR subject LIKE %s OR message LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($search) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }

        if (!empty($status)) {
            $where[] = 'status = %s';
            $where_values[] = $status;
        }

        $where_clause = implode(' AND ', $where);

        // Get total items
        if (!empty($where_values)) {
            $total_items = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE $where_clause",
                ...$where_values
            ));
        } else {
            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE $where_clause");
        }

        // Get items
        $query = "SELECT * FROM $table WHERE $where_clause ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $query_values = array_merge($where_values, array($per_page, $offset));
        
        if (!empty($where_values)) {
            $this->items = $wpdb->get_results($wpdb->prepare($query, ...$query_values));
        } else {
            $this->items = $wpdb->get_results($wpdb->prepare($query, $per_page, $offset));
        }

        // Set pagination args
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));

        // Set columns
        $this->_column_headers = array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns()
        );
    }
}

