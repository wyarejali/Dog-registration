<?php
    /**
     * Admin registrations list template with deletion capability
     */

    // Exit if accessed directly
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }
    // Display delete messages
    if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === 'true' ) {
        echo '<div class="notice notice-success is-dismissible"><p>' .
        __( 'Registration deleted successfully.', 'dog-eclipse' ) . '</p></div>';
    }

    if ( isset( $_GET['bulk-deleted'] ) ) {
        $count = intval( $_GET['bulk-deleted'] );
        echo '<div class="notice notice-success is-dismissible"><p>' .
        sprintf( _n( '%s registration deleted successfully.', '%s registrations deleted successfully.', $count, 'dog-eclipse' ), $count ) .
            '</p></div>';
    }

?>

<div class="wrap dog-eclipse">
    <h1><?php _e( 'Dog Eclipse Registrations', 'dog-eclipse' ); ?></h1>

    <div class="tablenav top">
        <div class="alignright actions">
            <form method="get">
                <input type="hidden" name="page" value="dog-eclipse-registrations">
                <input type="text" name="s" placeholder="<?php _e( 'Search...', 'dog-eclipse' ); ?>"
                       value="<?php echo isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : ''; ?>">
                <input type="submit" class="button" value="<?php _e( 'Search', 'dog-eclipse' ); ?>">
            </form>
        </div>
        <br class="clear">
    </div>

    <form method="post">
        <?php
            // Add nonce field for security
            wp_nonce_field( 'bulk-registrations' );

            $registrations_table->display();
        ?>
    </form>
</div>