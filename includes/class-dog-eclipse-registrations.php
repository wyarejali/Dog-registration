<?php
/**
 * Main plugin class file
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Dog_Eclipse_Registrations {

    /**
     * Initialize the plugin
     */
    public function init() {
        // Register assets
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );

        // Initialize shortcodes
        add_shortcode( 'dog_registration_form', array( $this, 'registration_form_shortcode' ) );

        // Handle form submission
        add_action( 'admin_post_der_form_submit', array( $this, 'handle_form_submission' ) );
        add_action( 'admin_post_nopriv_der_form_submit', array( $this, 'handle_form_submission' ) );
    }

    /**
     * Register and enqueue scripts and styles
     */
    public function register_assets() {
        // Register and enqueue CSS
        wp_register_style( 'dog-eclipse-style', DOG_ECLIPSE_PLUGIN_URL . 'public/css/dog-eclipse-public.css', array(), DOG_ECLIPSE_VERSION );
        wp_enqueue_style( 'dog-eclipse-style' );

        // Register and enqueue JS
        wp_register_script( 'dog-eclipse-script', DOG_ECLIPSE_PLUGIN_URL . 'public/js/dog-eclipse-public.js', array( 'jquery' ), DOG_ECLIPSE_VERSION, true );
        wp_enqueue_script( 'dog-eclipse-script' );
    }

    /**
     * Registration form shortcode
     */
    public function registration_form_shortcode() {
        ob_start();
        include DOG_ECLIPSE_PLUGIN_DIR . 'public/partials/registration-form.php';

        return ob_get_clean();
    }

    /**
     * Handle form submission
     */
    public function handle_form_submission() {
        // Verify nonce
        if ( !isset( $_POST['dog_eclipse_reg_nonce'] ) || !wp_verify_nonce( $_POST['dog_eclipse_reg_nonce'], 'dog_eclipse_reg' ) ) {
            wp_die( 'Security check failed', 'Security Error', array( 'response' => 403 ) );
        }

        // Process dog details
        $dogs = array();

        // Determine how many dogs were submitted
        $dog_count = 1;
        while ( isset( $_POST['dog_name' . ( $dog_count > 1 ? '_' . $dog_count : '' )] ) ) {
            $dog_count++;
        }
        $dog_count--; // Adjust count back down

        // Process each dog's details
        for ( $i = 1; $i <= $dog_count; $i++ ) {
            $suffix = $i > 1 ? '_' . $i : '';

            $name_field        = 'dog_name' . $suffix;
            $breed_field       = 'breed_sex' . $suffix;
            $age_field         = 'age' . $suffix;
            $neutered_field    = 'neutered' . $suffix;
            $feeding_field     = 'feeding_guide' . $suffix;
            $medical_field     = 'medical_notes' . $suffix;
            $vaccination_field = 'vaccination' . $suffix;
            $kennel_field      = 'kennel_vaccination' . $suffix;
            $certificate_field = 'certificate' . $suffix;

            // Only process if a dog name is provided
            if ( isset( $_POST[$name_field] ) && !empty( $_POST[$name_field] ) ) {
                // Handle file upload for this dog
                $certificate_path = '';
                if ( !empty( $_FILES[$certificate_field]['name'] ) ) {
                    if ( !function_exists( 'wp_handle_upload' ) ) {
                        require_once ABSPATH . 'wp-admin/includes/file.php';
                    }

                    $upload_overrides = array( 'test_form' => false );
                    $uploaded_file    = wp_handle_upload( $_FILES[$certificate_field], $upload_overrides );

                    if ( $uploaded_file && !isset( $uploaded_file['error'] ) ) {
                        $certificate_path = $uploaded_file['url'];
                    }
                }

                // Add dog to the array
                $dogs[] = array(
                    'name'               => sanitize_text_field( $_POST[$name_field] ),
                    'breed_sex'          => isset( $_POST[$breed_field] ) ? sanitize_text_field( $_POST[$breed_field] ) : '',
                    'age'                => isset( $_POST[$age_field] ) ? sanitize_text_field( $_POST[$age_field] ) : '',
                    'neutered'           => isset( $_POST[$neutered_field] ) ? sanitize_text_field( $_POST[$neutered_field] ) : '',
                    'feeding_guide'      => isset( $_POST[$feeding_field] ) ? sanitize_textarea_field( $_POST[$feeding_field] ) : '',
                    'medical_notes'      => isset( $_POST[$medical_field] ) ? sanitize_textarea_field( $_POST[$medical_field] ) : '',
                    'vaccination'        => isset( $_POST[$vaccination_field] ) ? 'yes' : 'no',
                    'kennel_vaccination' => isset( $_POST[$kennel_field] ) ? 'yes' : 'no',
                    'certificate_path'   => $certificate_path,
                );
            }
        }

        // Prepare data for database
        $data = array(
            'owner_name'        => sanitize_text_field( $_POST['owner_name'] ),
            'address'           => sanitize_text_field( $_POST['address'] ),
            'phone'             => sanitize_text_field( $_POST['phone'] ),
            'vet_contact'       => sanitize_text_field( $_POST['vet_contact'] ),
            'emergency_contact' => sanitize_text_field( $_POST['emergency_contact'] ),
            'dogs'              => serialize( $dogs ),
            'additional_notes'  => sanitize_textarea_field( $_POST['additional_notes'] ),
            'consent'           => isset( $_POST['consent'] ) && $_POST['consent'] === 'do_consent' ? 'yes' : 'no',
            'signed'            => sanitize_text_field( $_POST['signed'] ),
            'date'              => sanitize_text_field( $_POST['date'] ),
        );

        // Insert into database
        global $wpdb;
        $table_name = $wpdb->prefix . 'dog_registrations';
        $wpdb->insert( $table_name, $data );

        // Redirect back to form page with success message
        $redirect_url = add_query_arg( 'submitted', 'true', wp_get_referer() );
        wp_redirect( $redirect_url );
        exit;
    }
}