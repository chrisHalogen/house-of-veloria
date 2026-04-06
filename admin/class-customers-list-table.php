<?php
/**
 * Customers List Table
 *
 * Extends WP_List_Table to display customers and subscribers
 *
 * @package HID_Simple_Commerce
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class HID_Customers_List_Table extends WP_List_Table {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => 'customer',
            'plural'   => 'customers',
            'ajax'     => false
        ));
    }

    /**
     * Get columns
     */
    public function get_columns() {
        return array(
            'cb'              => '<input type="checkbox" />',
            'name'            => __('Name', 'hid-simple-commerce'),
            'email'           => __('Email', 'hid-simple-commerce'),
            'phone'           => __('Phone', 'hid-simple-commerce'),
            'customer_type'   => __('Type', 'hid-simple-commerce'),
            'date_subscribed' => __('Subscribed', 'hid-simple-commerce'),
            'actions'         => __('Actions', 'hid-simple-commerce'),
        );
    }

    /**
     * Get sortable columns
     */
    public function get_sortable_columns() {
        return array(
            'name'            => array('name', false),
            'email'           => array('email', false),
            'customer_type'   => array('customer_type', false),
            'date_subscribed' => array('date_subscribed', true), // true = already sorted
        );
    }

    /**
     * Column default
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'name':
                return !empty($item->name) ? esc_html($item->name) : '<em style="color: #999;">' . __('Not provided', 'hid-simple-commerce') . '</em>';
            case 'email':
                return '<a href="mailto:' . esc_attr($item->email) . '">' . esc_html($item->email) . '</a>';
            case 'phone':
                return !empty($item->phone) ? esc_html($item->phone) : '<em style="color: #999;">' . __('Not provided', 'hid-simple-commerce') . '</em>';
            case 'customer_type':
                if ($item->customer_type === 'customer') {
                    return '<span class="hid-badge hid-badge-customer">' . __('Customer', 'hid-simple-commerce') . '</span>';
                } else {
                    return '<span class="hid-badge hid-badge-subscriber">' . __('Subscriber', 'hid-simple-commerce') . '</span>';
                }
            case 'date_subscribed':
                return date('M j, Y g:i A', strtotime($item->date_subscribed));
            default:
                return '';
        }
    }

    /**
     * Column checkbox
     */
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="customer[]" value="%s" />', $item->id);
    }

    /**
     * Column actions
     */
    public function column_actions($item) {
        $delete_url = wp_nonce_url(
            add_query_arg(array(
                'action' => 'delete',
                'customer_id' => $item->id
            )),
            'delete_customer_' . $item->id
        );

        return '<a href="' . esc_url($delete_url) . '" class="button button-small" onclick="return confirm(\'' . esc_js(__('Are you sure you want to delete this customer?', 'hid-simple-commerce')) . '\');">' . __('Delete', 'hid-simple-commerce') . '</a>';
    }

    /**
     * Get bulk actions
     */
    public function get_bulk_actions() {
        return array(
            'bulk_delete' => __('Delete', 'hid-simple-commerce')
        );
    }

    /**
     * Process bulk actions
     */
    public function process_bulk_action() {
        global $wpdb;
        $customers_table = $wpdb->prefix . 'hid_customers';

        // Single delete
        if ('delete' === $this->current_action()) {
            if (isset($_GET['customer_id']) && check_admin_referer('delete_customer_' . absint($_GET['customer_id']))) {
                $customer_id = absint($_GET['customer_id']);
                $wpdb->delete($customers_table, array('id' => $customer_id), array('%d'));
                
                wp_safe_redirect(add_query_arg('message', 'deleted', remove_query_arg(array('action', 'customer_id', '_wpnonce'))));
                exit;
            }
        }

        // Bulk delete
        if ('bulk_delete' === $this->current_action()) {
            if (isset($_POST['customer']) && is_array($_POST['customer'])) {
                check_admin_referer('bulk-customers');
                
                foreach ($_POST['customer'] as $customer_id) {
                    $wpdb->delete($customers_table, array('id' => absint($customer_id)), array('%d'));
                }
                
                wp_safe_redirect(add_query_arg('message', 'bulk_deleted', remove_query_arg(array('action', 'customer', '_wpnonce', '_wp_http_referer'))));
                exit;
            }
        }
    }

    /**
     * Display tablenav
     */
    protected function display_tablenav($which) {
        ?>
        <div class="tablenav <?php echo esc_attr($which); ?>">
            <?php if ($which === 'top'): ?>
                <div class="alignleft actions">
                    <?php $this->bulk_actions($which); ?>
                    <?php $this->extra_tablenav($which); ?>
                </div>
            <?php endif; ?>
            <?php
            $this->pagination($which);
            ?>
            <br class="clear" />
        </div>
        <?php
    }

    /**
     * Extra controls to be displayed between bulk actions and pagination
     */
    protected function extra_tablenav($which) {
        if ($which === 'top') {
            $customer_type = isset($_GET['customer_type']) ? sanitize_text_field($_GET['customer_type']) : '';
            ?>
            <select name="customer_type" id="filter-by-type">
                <option value=""><?php _e('All Types', 'hid-simple-commerce'); ?></option>
                <option value="subscriber" <?php selected($customer_type, 'subscriber'); ?>><?php _e('Subscribers', 'hid-simple-commerce'); ?></option>
                <option value="customer" <?php selected($customer_type, 'customer'); ?>><?php _e('Customers', 'hid-simple-commerce'); ?></option>
            </select>
            <?php
            submit_button(__('Filter', 'hid-simple-commerce'), '', 'filter_action', false);
        }
    }

    /**
     * Prepare items
     */
    public function prepare_items() {
        global $wpdb;
        $customers_table = $wpdb->prefix . 'hid_customers';

        // Process bulk actions
        $this->process_bulk_action();

        // Pagination
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        // Search
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        // Filter by type
        $customer_type = isset($_REQUEST['customer_type']) ? sanitize_text_field($_REQUEST['customer_type']) : '';

        // Sorting
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'date_subscribed';
        $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';

        // Validate orderby
        $allowed_orderby = array('name', 'email', 'customer_type', 'date_subscribed');
        if (!in_array($orderby, $allowed_orderby)) {
            $orderby = 'date_subscribed';
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
            $where[] = '(name LIKE %s OR email LIKE %s OR phone LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($search) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }

        if (!empty($customer_type)) {
            $where[] = 'customer_type = %s';
            $where_values[] = $customer_type;
        }

        $where_clause = implode(' AND ', $where);

        // Get total items
        if (!empty($where_values)) {
            $total_items = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $customers_table WHERE $where_clause",
                ...$where_values
            ));
        } else {
            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table WHERE $where_clause");
        }

        // Get items
        $query = "SELECT * FROM $customers_table WHERE $where_clause ORDER BY $orderby $order LIMIT %d OFFSET %d";
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

