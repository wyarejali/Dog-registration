<?php
/**
 * Admin registrations list template with deletion capability
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap dog-eclipse">
    <h1><?php _e('Dog Eclipse Registrations', 'dog-eclipse'); ?></h1>
    
    <div class="tablenav top">
        <div class="alignright actions">
            <form method="get">
                <input type="hidden" name="page" value="dog-eclipse-registrations">
                <input type="text" name="s" placeholder="<?php _e('Search...', 'dog-eclipse'); ?>" 
                       value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>">
                <input type="submit" class="button" value="<?php _e('Search', 'dog-eclipse'); ?>">
            </form>
        </div>
        <br class="clear">
    </div>
    
    <form method="post">
        <?php
        $registrations_table->prepare_items();
        // Adding nonce for bulk actions
        wp_nonce_field('bulk-' . $registrations_table->_args['plural']);
        $registrations_table->display();
        ?>
    </form>
</div>