<?php
/**
 * Plugin Name: Dog Eclipse Registration
 * Plugin URI: https://divinationkit.com/plugins/dog-eclipse-registration
 * Description: A plugin to handle dog registration for Dog Eclipse. To display the registration form, use the shortcode [dog_registration_form].
 * Version: 1.1.2
 * Author: Wyarej Ali
 * Author URI: https://divinationkit.com
 * Text Domain: dog-eclipse
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'DOG_ECLIPSE_VERSION', '1.1.2' );
define( 'DOG_ECLIPSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DOG_ECLIPSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files
require_once DOG_ECLIPSE_PLUGIN_DIR . 'includes/class-dog-eclipse-registrations.php';
require_once DOG_ECLIPSE_PLUGIN_DIR . 'admin/class-dog-eclipse-admin.php';
require_once DOG_ECLIPSE_PLUGIN_DIR . 'public/class-dog-eclipse-public.php';

// Initialize the plugin
function dog_eclipse_init() {
    // Initialize main class
    $dog_eclipse = new Dog_Eclipse_Registrations();
    $dog_eclipse->init();

    // Initialize admin class if in admin area
    if ( is_admin() ) {
        $dog_eclipse_admin = new Dog_Eclipse_Admin();
        $dog_eclipse_admin->init();
    }

    // Initialize public class
    $dog_eclipse_public = new Dog_Eclipse_Public();
    $dog_eclipse_public->init();
}

add_action( 'plugins_loaded', 'dog_eclipse_init' );

// Activation hook
register_activation_hook( __FILE__, 'dog_eclipse_activate' );
function dog_eclipse_activate() {
    global $wpdb;

    $table_name      = $wpdb->prefix . 'dog_registrations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        owner_name varchar(100) NOT NULL,
        address text NOT NULL,
        phone varchar(50) NOT NULL,
        phone2 varchar(50) NOT NULL,
        vet_contact text,
        emergency_contact text,
        dogs longtext NOT NULL,
        additional_notes text,
        consent varchar(10) NOT NULL,
        signed varchar(100) NOT NULL,
        date varchar(50) NOT NULL,
        certificate_path text,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Deactivation hook
register_deactivation_hook( __FILE__, 'dog_eclipse_deactivate' );
function dog_eclipse_deactivate() {
    // Clean up if needed
    // TODO:: remove this after testing
    global $wpdb;
    $table_name = $wpdb->prefix . 'dog_registrations';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

// Uninstall hook
register_uninstall_hook( __FILE__, 'dog_eclipse_uninstall' );
function dog_eclipse_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dog_registrations';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
