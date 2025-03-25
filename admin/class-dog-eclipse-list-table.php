<?php
/**
 * List Table class for displaying registrations with delete functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load WP_List_Table if not loaded
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Dog_Eclipse_List_Table extends WP_List_Table {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => 'registration',
            'plural'   => 'registrations',
            'ajax'     => false
        ));
    }

    /**
     * Get columns
     */
    public function get_columns() {
        return array(
            'cb'             => '<input type="checkbox" />', // Add checkbox column
            'owner_name'     => __('Owner Name', 'dog-eclipse'),
            'phone'          => __('Phone', 'dog-eclipse'),
            'address'        => __('Address', 'dog-eclipse'),
            'dogs'        => __('Dogs', 'dog-eclipse'),
            'submission_date' => __('Date', 'dog-eclipse'),
            'actions'        => __('Actions', 'dog-eclipse')
        );
    }
        
    /**
     * Define sortable columns.
     */
    public function get_sortable_columns() {
        return [
            'owner_name'  => ['owner_name', true],  // Default sort by name ASC
            'submission_date'  => ['submission_date', true],  // Default sort by date DESC
        ];
    }

    /**
     * Get bulk actions
     */
    public function get_bulk_actions() {
        return array(
            'delete' => __('Delete', 'dog-eclipse')
        );
    }

    /**
     * Column for checkbox
     */
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="registration[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * Prepare items
     */
    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dog_registrations';
        
        // Set up pagination
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        // Handle search if present
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        $where = '';
        if (!empty($search)) {
            $where = $wpdb->prepare(
                "WHERE owner_name LIKE %s OR phone LIKE %s OR address LIKE %s", 
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }
    
        // Get total count for pagination
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");
    
        // Set up column headers
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // Handle sorting (Default: `submission_date DESC` to show newest first)
        $allowed_columns = ['owner_name', 'submission_date']; // Allowed sortable columns
        $orderby = isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], $allowed_columns) 
                    ? esc_sql($_REQUEST['orderby']) 
                    : 'submission_date';  // Default sort by date
    
        $order = isset($_REQUEST['order']) && strtoupper($_REQUEST['order']) === 'ASC' 
                    ? 'ASC' 
                    : 'DESC'; // Default: Descending order
        
        // Fetch sorted data
        $query = "SELECT * FROM $table_name $where ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $data = $wpdb->get_results($wpdb->prepare($query, $per_page, ($current_page - 1) * $per_page), ARRAY_A);
    
        // Set up pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
        
        $this->items = $data;
    }

    /**
     * Default column rendering
     */
    public function column_default($item, $column_name) {
        $view_url = admin_url('admin.php?page=dog-eclipse-registration-details&id=' . $item['id']);

        switch ($column_name) {
            case 'owner_name':

                return sprintf(
                    '<strong><a href="%s">%s</a></strong>',
                    esc_url($view_url),
                    esc_html($item['owner_name']),
                );
            case 'phone':
            case 'address':
                return esc_html($item[$column_name]);
            case 'dogs':
                $dogs = maybe_unserialize($item[$column_name]);
                return count($dogs);
            case 'submission_date':
                return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item[$column_name]));
            default:
                return print_r($item, true);
        }
    }

    /**
     * Actions column
     */
    public function column_actions($item) {
        $view_url = admin_url('admin.php?page=dog-eclipse-registration-details&id=' . $item['id']);
        $delete_url = wp_nonce_url(
            add_query_arg(
                array(
                    'page' => 'dog-eclipse-registrations',
                    'action' => 'delete',
                    'registration' => $item['id']
                ),
                admin_url('admin.php')
            ),
            'delete_registration_' . $item['id'],
            'dog_eclipse_nonce'
        );
        
        return sprintf(
            '<a href="%s" class="button button-small">%s</a> <a href="%s" class="button button-small delete-registration" data-id="%s">%s</a>',
            esc_url($view_url),
            __('View', 'dog-eclipse'),
            esc_url($delete_url),
            $item['id'],
            __('Delete', 'dog-eclipse')
        );
    }
}