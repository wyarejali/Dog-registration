<?php
/**
 * Admin functionality class file with deletion capabilities
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Dog_Eclipse_Admin {

    /**
     * Initialize admin functionality
     */
    public function init() {
        // Add admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        // Register admin assets
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );

        // Handle deletion actions
        add_action( 'admin_init', array( $this, 'handle_deletion_actions' ) );
    }

    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __( 'Dog Registrations', 'dog-eclipse' ),
            __( 'Dog Registrations', 'dog-eclipse' ),
            'manage_options',
            'dog-eclipse-registrations',
            array( $this, 'display_registrations_page' ),
            'dashicons-pets',
            30
        );

        // Submenu page for viewing details
        add_submenu_page(
            'dog-eclipse-registrations',
            __( '', 'dog-eclipse' ),
            __( '', 'dog-eclipse' ),
            'manage_options',
            'dog-eclipse-registration-details',
            array( $this, 'display_registration_details' ),
            31
        );
    }

    /**
     * Register admin scripts and styles
     */
    public function register_admin_assets( $hook ) {
        // Only load on our admin pages
        if ( strpos( $hook, 'dog-eclipse' ) === false ) {
            return;
        }

        wp_register_style( 'dog-eclipse-admin-style', DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/css/dog-eclipse-admin.css', array(), DOG_ECLIPSE_VERSION );
        wp_enqueue_style( 'dog-eclipse-admin-style' );

        wp_register_script( 'dog-eclipse-admin-script', DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/js/dog-eclipse-admin.js', array( 'jquery' ), DOG_ECLIPSE_VERSION, true );

        // Add localized script variables
        wp_localize_script( 'dog-eclipse-admin-script', 'dog_eclipse_vars', array(
            'delete_confirm'      => __( 'Are you sure you want to delete this registration? This action cannot be undone.', 'dog-eclipse' ),
            'bulk_delete_confirm' => __( 'Are you sure you want to delete these registrations? This action cannot be undone.', 'dog-eclipse' ),
        ) );

        wp_enqueue_script( 'dog-eclipse-admin-script' );

    }

    /**
     * Handle deletion actions
     */
    public function handle_deletion_actions() {
        global $wpdb;
        $table_name   = '';
        $redirect_url = '';
        $message      = '';

        if ( isset( $_REQUEST['page'] ) || $_REQUEST['page'] == 'dog-eclipse-registrations' ) {
            global $wpdb;
            $table_name   = $wpdb->prefix . 'dog_registrations';
            $redirect_url = admin_url( 'admin.php?page=dog-eclipse-registrations' );
            $message      = '';

            // Single item deletion
            if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' && isset( $_REQUEST['registration'] ) ) {
                $registration_id = intval( $_REQUEST['registration'] );

                // Verify nonce
                if ( !isset( $_REQUEST['dog_eclipse_nonce'] ) || !wp_verify_nonce( $_REQUEST['dog_eclipse_nonce'], 'delete_registration_' . $registration_id ) ) {
                    wp_die( __( 'Security check failed', 'dog-eclipse' ) );
                }

                // Delete the registration
                $wpdb->delete( $table_name, array( 'id' => $registration_id ), array( '%d' ) );

                $message = 'deleted';
            }

            // Check if a bulk action was requested
            if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'bulk-delete' && isset( $_REQUEST['registration'] ) ) {
                // Check permissions
                if ( !current_user_can( 'manage_options' ) ) {
                    wp_die( __( 'You do not have sufficient permissions to access this page.', 'dog-eclipse' ) );
                }

                // Verify nonce
                check_admin_referer( 'bulk-registrations' );

                // Process bulk deletion
                global $wpdb;
                $table_name    = $wpdb->prefix . 'dog_registrations';
                $registrations = array_map( 'intval', $_REQUEST['registration'] );

                foreach ( $registrations as $id ) {
                    $wpdb->delete( $table_name, array( 'id' => $id ), array( '%d' ) );
                }

                // Redirect back to list page with message
                $count = count( $registrations );
                wp_redirect( add_query_arg( 'bulk-deleted', $count, admin_url( 'admin.php?page=dog-eclipse-registrations' ) ) );
                exit;
            }

        } else if ( isset( $_REQUEST['page'] ) || $_REQUEST['page'] == 'dog-eclipse-registration-details' ) {
            global $wpdb;
            $table_name   = $wpdb->prefix . 'dog_registrations';
            $redirect_url = admin_url( 'admin.php?page=dog-eclipse-registrations' );
            $message      = '';

            // Registration deletion from details page
            if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'dog-eclipse-registration-details' && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' ) {
                $registration_id = intval( $_REQUEST['registration'] );

                // Verify nonce
                if ( !isset( $_REQUEST['dog_eclipse_nonce'] ) || !wp_verify_nonce( $_REQUEST['dog_eclipse_nonce'], 'delete_registration_' . $registration_id ) ) {
                    wp_die( __( 'Security check failed', 'dog-eclipse' ) );
                }

                // Delete the registration
                $wpdb->delete( $table_name, array( 'id' => $registration_id ), array( '%d' ) );

                $redirect_url = admin_url( 'admin.php?page=dog-eclipse-registrations&message=deleted' );
                wp_redirect( $redirect_url );
                exit;
            }
        } else {
            return;
        }

        // Redirect after deletion if needed
        if ( !empty( $message ) ) {
            wp_redirect( add_query_arg( 'message', $message, $redirect_url ) );
            exit;
        }
    }

    /**
     * Handle bulk actions
     */
    // public function handle_bulk_actions() {
    //     if ( !isset( $_REQUEST['page'] ) || $_REQUEST['page'] !== 'dog-eclipse-registrations' ) {
    //         return;
    //     }

    // }

    /**
     * Display registrations list page
     */
    public function display_registrations_page() {
        // Check if the user has proper permissions
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'dog-eclipse' ) );
        }

        // Create an instance of our table class
        require_once DOG_ECLIPSE_PLUGIN_DIR . 'admin/class-dog-eclipse-list-table.php';
        $registrations_table = new Dog_Eclipse_List_Table();
        $registrations_table->prepare_items();

        // Display messages
        if ( isset( $_GET['message'] ) ) {
            $message     = sanitize_text_field( $_GET['message'] );
            $notice_type = 'success';

            switch ( $message ) {
            case 'deleted':
                $notice_message = __( 'Registration successfully deleted.', 'dog-eclipse' );
                break;
            case 'bulk-deleted':
                $notice_message = __( 'Selected registrations were successfully deleted.', 'dog-eclipse' );
                break;
            default:
                $notice_message = '';
                break;
            }

            if ( !empty( $notice_message ) ) {
                echo '<div class="notice notice-' . $notice_type . ' is-dismissible"><p>' . $notice_message . '</p></div>';
            }
        }

        // Include the admin view
        include DOG_ECLIPSE_PLUGIN_DIR . 'admin/partials/registrations-list.php';
    }

    /**
     * Display registration details page
     */
    public function display_registration_details() {
        // Check if the user has proper permissions
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'dog-eclipse' ) );
        }

        // Get registration ID from URL
        $id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        if ( $id <= 0 ) {
            wp_die( __( 'Invalid registration ID.', 'dog-eclipse' ) );
        }

        // Get registration data
        global $wpdb;
        $table_name   = $wpdb->prefix . 'dog_registrations';
        $registration = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );

        if ( !$registration ) {
            wp_die( __( 'Registration not found.', 'dog-eclipse' ) );
        }

        // Include the admin view
        include DOG_ECLIPSE_PLUGIN_DIR . 'admin/partials/registration-details.php';
    }
}