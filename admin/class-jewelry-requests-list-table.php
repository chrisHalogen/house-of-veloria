<?php
/**
 * Jewelry Requests List Table
 *
 * @package HID_Simple_Commerce
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class HID_Jewelry_Requests_List_Table extends WP_List_Table {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => 'request',
            'plural'   => 'requests',
            'ajax'     => false
        ));
    }

    /**
     * Get columns
     */
    public function get_columns() {
        return array(
            'cb'           => '<input type="checkbox" />',
            'id'           => __('ID', 'hid-simple-commerce'),
            'name'         => __('Name', 'hid-simple-commerce'),
            'email'        => __('Email', 'hid-simple-commerce'),
            'jewelry_type' => __('Jewelry Type', 'hid-simple-commerce'),
            'budget_range' => __('Budget', 'hid-simple-commerce'),
            'status'       => __('Status', 'hid-simple-commerce'),
            'created_at'   => __('Date', 'hid-simple-commerce'),
            'actions'      => __('Actions', 'hid-simple-commerce'),
        );
    }

    /**
     * Get sortable columns
     */
    public function get_sortable_columns() {
        return array(
            'id'           => array('id', true),
            'name'         => array('name', false),
            'email'        => array('email', false),
            'jewelry_type' => array('jewelry_type', false),
            'status'       => array('status', false),
            'created_at'   => array('created_at', false),
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
            case 'jewelry_type':
                return esc_html(ucfirst($item->jewelry_type));
            case 'budget_range':
                return $item->budget_range ? esc_html($item->budget_range) : '<em style="color: #999;">Not specified</em>';
            case 'status':
                $status_colors = array(
                    'pending'     => '#d63638',
                    'reviewing'   => '#f0b849',
                    'contacted'   => '#2271b1',
                    'completed'   => '#00a32a',
                    'cancelled'   => '#999',
                );
                $color = isset($status_colors[$item->status]) ? $status_colors[$item->status] : '#999';
                return '<span class="hid-badge" style="background: ' . $color . '; color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px;">' . esc_html(ucfirst($item->status)) . '</span>';
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
        return sprintf('<input type="checkbox" name="request[]" value="%s" />', $item->id);
    }

    /**
     * Column actions
     */
    public function column_actions($item) {
        $view_url = add_query_arg(array(
            'page' => 'hid-jewelry-requests',
            'action' => 'view',
            'request_id' => $item->id
        ), admin_url('admin.php'));

        $delete_url = wp_nonce_url(
            admin_url('admin-post.php?action=hid_delete_jewelry_request&request_id=' . $item->id),
            'delete_request_' . $item->id
        );

        return '
            <a href="' . esc_url($view_url) . '" class="button button-small">' . __('View', 'hid-simple-commerce') . '</a>
            <a href="' . esc_url($delete_url) . '" class="button button-small" onclick="return confirm(\'' . esc_js(__('Are you sure you want to delete this request?', 'hid-simple-commerce')) . '\');">' . __('Delete', 'hid-simple-commerce') . '</a>
        ';
    }

    /**
     * Get bulk actions
     */
    public function get_bulk_actions() {
        return array(
            'bulk_delete'      => __('Delete', 'hid-simple-commerce'),
            'mark_reviewing'   => __('Mark as Reviewing', 'hid-simple-commerce'),
            'mark_contacted'   => __('Mark as Contacted', 'hid-simple-commerce'),
            'mark_completed'   => __('Mark as Completed', 'hid-simple-commerce'),
        );
    }

    /**
     * Process bulk actions
     */
    public function process_bulk_action() {
        global $wpdb;
        $table = $wpdb->prefix . 'hid_jewelry_requests';

        // Bulk delete
        if ('bulk_delete' === $this->current_action()) {
            if (isset($_POST['request']) && is_array($_POST['request'])) {
                check_admin_referer('bulk-requests');
                
                foreach ($_POST['request'] as $request_id) {
                    $wpdb->delete($table, array('id' => absint($request_id)), array('%d'));
                }
                
                wp_safe_redirect(add_query_arg('message', 'bulk_deleted', remove_query_arg(array('action', 'request', '_wpnonce', '_wp_http_referer'))));
                exit;
            }
        }

        // Status updates
        $status_actions = array(
            'mark_reviewing' => 'reviewing',
            'mark_contacted' => 'contacted',
            'mark_completed' => 'completed',
        );

        foreach ($status_actions as $action => $status) {
            if ($action === $this->current_action()) {
                if (isset($_POST['request']) && is_array($_POST['request'])) {
                    check_admin_referer('bulk-requests');
                    
                    foreach ($_POST['request'] as $request_id) {
                        $wpdb->update($table, array('status' => $status), array('id' => absint($request_id)), array('%s'), array('%d'));
                    }
                    
                    wp_safe_redirect(add_query_arg('message', 'status_updated', remove_query_arg(array('action', 'request', '_wpnonce', '_wp_http_referer'))));
                    exit;
                }
            }
        }
    }

    /**
     * Extra controls
     */
    protected function extra_tablenav($which) {
        if ($which === 'top') {
            $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
            $jewelry_type = isset($_GET['jewelry_type']) ? sanitize_text_field($_GET['jewelry_type']) : '';
            ?>
            <div class="alignleft actions">
                <select name="status" id="filter-by-status">
                    <option value=""><?php _e('All Statuses', 'hid-simple-commerce'); ?></option>
                    <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'hid-simple-commerce'); ?></option>
                    <option value="reviewing" <?php selected($status, 'reviewing'); ?>><?php _e('Reviewing', 'hid-simple-commerce'); ?></option>
                    <option value="contacted" <?php selected($status, 'contacted'); ?>><?php _e('Contacted', 'hid-simple-commerce'); ?></option>
                    <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'hid-simple-commerce'); ?></option>
                    <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'hid-simple-commerce'); ?></option>
                </select>
                
                <select name="jewelry_type" id="filter-by-type">
                    <option value=""><?php _e('All Types', 'hid-simple-commerce'); ?></option>
                    <option value="necklace" <?php selected($jewelry_type, 'necklace'); ?>><?php _e('Necklace', 'hid-simple-commerce'); ?></option>
                    <option value="earrings" <?php selected($jewelry_type, 'earrings'); ?>><?php _e('Earrings', 'hid-simple-commerce'); ?></option>
                    <option value="ring" <?php selected($jewelry_type, 'ring'); ?>><?php _e('Ring', 'hid-simple-commerce'); ?></option>
                    <option value="bracelet" <?php selected($jewelry_type, 'bracelet'); ?>><?php _e('Bracelet', 'hid-simple-commerce'); ?></option>
                    <option value="set" <?php selected($jewelry_type, 'set'); ?>><?php _e('Jewelry Set', 'hid-simple-commerce'); ?></option>
                    <option value="other" <?php selected($jewelry_type, 'other'); ?>><?php _e('Other', 'hid-simple-commerce'); ?></option>
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
        $table = $wpdb->prefix . 'hid_jewelry_requests';

        // Process bulk actions
        $this->process_bulk_action();

        // Pagination
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        // Search
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        // Filters
        $status = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : '';
        $jewelry_type = isset($_REQUEST['jewelry_type']) ? sanitize_text_field($_REQUEST['jewelry_type']) : '';

        // Sorting
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'created_at';
        $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';

        // Validate orderby
        $allowed_orderby = array('id', 'name', 'email', 'jewelry_type', 'status', 'created_at');
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
            $where[] = '(name LIKE %s OR email LIKE %s OR jewelry_type LIKE %s OR description LIKE %s)';
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

        if (!empty($jewelry_type)) {
            $where[] = 'jewelry_type = %s';
            $where_values[] = $jewelry_type;
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

