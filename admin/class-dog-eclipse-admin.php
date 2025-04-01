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

        // handle assessment submissions
        add_action( 'admin_init', array( $this, 'handle_assessment_submission' ) );

        // Modify admin footer text
        add_filter( 'admin_footer_text', array( $this, 'modify_admin_footer_text' ) );

        // Modify admin footer version text
        add_filter( 'update_footer', array( $this, 'modify_admin_footer_version_text' ), 11 );
    }

    /**
     * Modify admin footer text when on the registrations page
     */
    public function modify_admin_footer_text( $text ) {
        global $pagenow;

        if ( $pagenow === 'admin.php' && isset( $_GET['page'] ) ) {
            if ( $_GET['page'] === 'dog-eclipse-registrations' || $_GET['page'] === 'dog-eclipse-registration-details' ) {
                $text .= ' | Dog Eclipse Registrations by <a href="https://wordpress.org/plugins/divinationkit-for-divi/" target="_blank">DiviNationKit</a>';
            }
        }

        return $text;
    }

    /**
     * Modify admin footer version text
     */
    public function modify_admin_footer_version_text( $text ) {
        global $pagenow;

        if ( $pagenow === 'admin.php' && isset( $_GET['page'] ) ) {
            if ( $_GET['page'] === 'dog-eclipse-registrations' || $_GET['page'] === 'dog-eclipse-registration-details' ) {
                $text .= ' | Dog Eclipse v-' . DOG_ECLIPSE_VERSION;
            }
        }

        return $text;
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

        if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'dog-eclipse-registrations' ) {
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

        } else if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'dog-eclipse-registration-details' ) {
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

            // Assessment deletion from details page
            if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'dog-eclipse-registration-details' ) {

                if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete_assessment' ) {

                    $table_name = $wpdb->prefix . 'dog_registrations';

                    $registration_id = intval( $_REQUEST['registration_id'] );
                    $dog_index       = intval( $_REQUEST['dog_index'] );
                    $assessment_key  = intval( $_REQUEST['assessment_key'] );

                    // Verify nonce
                    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'delete_assessment_action' ) ) {
                        wp_die( __( 'Security check failed', 'dog-eclipse' ) );
                    }

                    // Retrieve the registration data
                    $registration = $this->get_registration_by_id( $registration_id );
                    $dogs         = maybe_unserialize( $registration->dogs );

                    if ( isset( $dogs[$dog_index]['assessments'][$assessment_key] ) ) {
                        // Remove the assessment
                        unset( $dogs[$dog_index]['assessments'][$assessment_key] );

                        // Reindex the array to maintain proper keys
                        $dogs[$dog_index]['assessments'] = array_values( $dogs[$dog_index]['assessments'] );

                        // Update the registration data
                        $this->update_registration_dogs( $registration_id, $dogs );

                        // Redirect back with a success message
                        wp_redirect( add_query_arg( 'message', 'assessment_deleted', admin_url( 'admin.php?page=dog-eclipse-registration-details&id=' . $registration_id ) ) );
                        exit;
                    } else {
                        // Redirect back with an error message if the assessment is not found
                        wp_redirect( add_query_arg( 'message', 'assessment_not_found', $redirect_url ) );
                        exit;
                    }
                }
            }

            // Redirect after deletion if needed
            if ( !empty( $message ) ) {
                wp_redirect( add_query_arg( 'message', $message, $redirect_url ) );
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
     * Handle assessment submission
     */
    public function handle_assessment_submission() {

        if ( isset( $_POST['submit_assessment'] ) && check_admin_referer( 'add_assessment_action', 'add_assessment_nonce' ) ) {
            $registration_id = intval( $_POST['registration_id'] );
            $dog_index       = intval( $_POST['dog_index'] );
            $assessment      = sanitize_textarea_field( $_POST['assessment'] );
            $timestamp       = current_time( 'mysql' ); // Get the current date and time in WordPress format

            // Retrieve the registration data
            $registration = $this->get_registration_by_id( $registration_id );
            $dogs         = maybe_unserialize( $registration->dogs );

            if ( isset( $dogs[$dog_index] ) ) {
                // Initialize assessments array if not already set
                if ( !isset( $dogs[$dog_index]['assessments'] ) || !is_array( $dogs[$dog_index]['assessments'] ) ) {
                    $dogs[$dog_index]['assessments'] = array();
                }

                // Add the new assessment with timestamp
                $dogs[$dog_index]['assessments'][] = array(
                    'text'      => $assessment,
                    'submitted' => $timestamp,
                    'author'    => get_current_user_id(),
                );

                // Update the registration data
                $this->update_registration_dogs( $registration_id, $dogs );

                wp_redirect( add_query_arg( 'message', 'assessment_added', admin_url( 'admin.php?page=dog-eclipse-registration-details&id=' . $registration_id ) ) );
                exit;
            }
        }

    }

    /**
     * Get registration by ID
     */
    public function get_registration_by_id( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dog_registrations';

        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );
    }

    /**
     * Update registration dogs data
     */
    public function update_registration_dogs( $id, $dogs ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dog_registrations';
        $data       = array( 'dogs' => serialize( $dogs ) );
        $where      = array( 'id' => $id );

        $wpdb->update( $table_name, $data, $where );
    }

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