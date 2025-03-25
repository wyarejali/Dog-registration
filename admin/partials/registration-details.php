<?php
/**
 * Admin registration details template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Unserialize dogs data
$dogs = maybe_unserialize($registration->dogs);

// Generate delete URL with nonce
$delete_url = wp_nonce_url(
    add_query_arg(
        array(
            'page' => 'dog-eclipse-registration-details',
            'action' => 'delete',
            'registration' => $registration->id
        ),
        admin_url('admin.php')
    ),
    'delete_registration_' . $registration->id,
    'dog_eclipse_nonce'
);
?>

<div class="wrap dog-eclipse">
    <div class="dog-eclipse-details">
        <div class="dog-eclipse-header">
            <div class="dog-eclipse-title">
                <h1><?php _e('Registration Details', 'dog-eclipse'); ?></h1>
                <a href="<?php echo admin_url('admin.php?page=dog-eclipse-registrations'); ?>" class="back-link">
                    &laquo; <?php _e('Back to Registrations List', 'dog-eclipse'); ?>
                </a>
            </div>
            <div class="dog-eclipse-action">
                <a href="<?php echo esc_url($delete_url); ?>" class="button dog-btn delete-registration">
                    <?php _e('Delete Registration', 'dog-eclipse'); ?>
                </a>
            </div>
        </div>

        <div class="dog-registration-container">
            <div class="dog-owner-details">
                <h3><?php _e('Owner Details', 'dog-eclipse'); ?></h3>
                <table>
                    <tr>
                        <th><?php _e('Name', 'dog-eclipse'); ?></th>
                        <td><?php echo esc_html($registration->owner_name); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Address', 'dog-eclipse'); ?></th>
                        <td><?php echo esc_html($registration->address); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Phone', 'dog-eclipse'); ?></th>
                        <td><a href="tel:<?php echo esc_html($registration->phone); ?>"><?php echo esc_html($registration->phone); ?></a></td>
                    </tr>
                    <tr>
                        <th><?php _e('Vet Contact', 'dog-eclipse'); ?></th>
                        <td><?php echo esc_html($registration->vet_contact); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Emergency Contact', 'dog-eclipse'); ?></th>
                        <td><?php echo esc_html($registration->emergency_contact); ?></td>
                    </tr>


                    <tr class="section">
                        <th><?php _e('Agreement', 'dog-eclipse'); ?></th>
                        <td></td>
                    </tr>
                    <tr>
                        <th><?php _e('Consent', 'dog-eclipse'); ?></th>
                        <td class="<?php echo $registration->consent === 'yes' ? 'consent-yes': 'consent-no' ?>"><?php echo $registration->consent === 'yes' ? __('Yes', 'dog-eclipse') : __('No', 'dog-eclipse'); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Signed', 'dog-eclipse'); ?></th>
                        <td><?php echo esc_html($registration->signed); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Date', 'dog-eclipse'); ?></th>
                        <td><?php echo esc_html($registration->date); ?></td>
                    </tr>

                    <tr class="section">
                        <th><?php _e('Submission', 'dog-eclipse'); ?></th>
                        <td></td>
                    </tr>
                    <tr>
                        <th><?php _e('Date & Time', 'dog-eclipse'); ?></th>
                        <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($registration->submission_date)); ?></td>
                    </tr>
                </table>
            </div>

            <div class="dogs-details-wrapper">
                <h3><?php _e('Dog Details', 'dog-eclipse'); ?></h3>
                
                <div class="all-dogs">
                    <?php if (!empty($dogs) && is_array($dogs)) : ?>
                        <?php foreach ($dogs as $index => $dog) : ?>
                            <div class="dog-item">
                                <h4><?php _e('Dog', 'dog-eclipse'); ?> #<?php echo $index + 1; ?></h4>
                                <table>
                                    <tr>
                                        <th><?php _e('Name', 'dog-eclipse'); ?></th>
                                        <td><?php echo esc_html($dog['name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Breed/Sex', 'dog-eclipse'); ?></th>
                                        <td><?php echo esc_html($dog['breed_sex']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Age', 'dog-eclipse'); ?></th>
                                        <td><?php echo esc_html($dog['age']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Neutered', 'dog-eclipse'); ?></th>
                                        <td><?php echo esc_html($dog['neutered']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Feeding Guide', 'dog-eclipse'); ?></th>
                                        <td><?php echo nl2br(esc_html($dog['feeding_guide'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Medical Notes', 'dog-eclipse'); ?></th>
                                        <td><?php echo nl2br(esc_html($dog['medical_notes'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Vaccination Proof', 'dog-eclipse'); ?></th>
                                        <td><?php echo $dog['vaccination'] === 'yes' ? __('Yes', 'dog-eclipse') : __('No', 'dog-eclipse'); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Kennel Vaccination', 'dog-eclipse'); ?></th>
                                        <td><?php echo $dog['kennel_vaccination'] === 'yes' ? __('Yes', 'dog-eclipse') : __('No', 'dog-eclipse'); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Certificate', 'dog-eclipse'); ?></th>
                                        <td class="certificate">

                                            <?php
                                                if(empty($dog['certificate_path'])) { ?>
                                                    <span class="no-certificate"><?php _e('No certificate uploaded', 'dog-eclipse'); ?></span>
                                                <?php } else { ?>
                                                    <a href="<?php echo esc_url($dog['certificate_path']); ?>" target="_blank" class="button dog-btn">
                                                        <img src="<?php echo DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/img/external-link.png' ?>" alt="link"> <?php _e('View', 'dog-eclipse'); ?>
                                                    </a>
                                                    <a href="<?php echo esc_url($dog['certificate_path']); ?>" download class="button dog-btn">
                                                        <img src="<?php echo DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/img/download.png' ?>" alt="link">  <?php _e('Download', 'dog-eclipse'); ?>
                                                    </a>
                                               <?php }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p><?php _e('No dog details available.', 'dog-eclipse'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="additional-notes">
                    <h3><?php _e('Additional Notes', 'dog-eclipse'); ?></h3>
                    <p><?php echo nl2br(esc_html($registration->additional_notes)); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrap dog-eclipse-admin-wrap"> 
    <div class="registration-details">
        <?php if (!empty($registration->certificate_path)) : ?>
        <div class="section">
            <h3><?php _e('Certificate', 'dog-eclipse'); ?></h3>
            <div class="certificate-preview">
                <?php 
                $file_ext = pathinfo($registration->certificate_path, PATHINFO_EXTENSION);
                $image_extensions = array('jpg', 'jpeg', 'png', 'gif');
                
                if (in_array(strtolower($file_ext), $image_extensions)) {
                    // Display image preview
                    echo '<div class="certificate-image">';
                    echo '<img src="' . esc_url($registration->certificate_path) . '" alt="Certificate" style="max-width: 300px;">';
                    echo '</div>';
                }
                ?>
                <p>
                    <a href="<?php echo esc_url($registration->certificate_path); ?>" target="_blank" class="button">
                        <?php _e('View/Download Certificate', 'dog-eclipse'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>