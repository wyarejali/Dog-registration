<?php
    /**
     * Admin registration details template
     */

    // Exit if accessed directly
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    // Unserialize dogs data
    $dogs = maybe_unserialize( $registration->dogs );

    // Generate delete URL with nonce
    $delete_url = wp_nonce_url(
        add_query_arg(
            array(
                'page'         => 'dog-eclipse-registration-details',
                'action'       => 'delete',
                'registration' => $registration->id,
            ),
            admin_url( 'admin.php' )
        ),
        'delete_registration_' . $registration->id,
        'dog_eclipse_nonce'
    );

?>


<div class="wrap dog-eclipse">
    <div class="dog-eclipse-details">
        <div class="dog-eclipse-title">
            <h1><?php _e( 'Registration Details', 'dog-eclipse' ); ?></h1>
        </div>

        <?php
            if ( isset( $_GET['message'] ) ) {
                $message = sanitize_text_field( $_GET['message'] );

                if ( $message === 'assessment_added' ) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Assessment successfully added.', 'dog-eclipse' ) . '</p></div>';
                } elseif ( $message === 'assessment_deleted' ) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Assessment successfully deleted.', 'dog-eclipse' ) . '</p></div>';
                }
            }
        ?>

        <div class="dog-registration-container">
            <div class="dog-owner-details">
                <h3><?php _e( 'Owner Details', 'dog-eclipse' ); ?></h3>
                <div class="owner-details-wrapper">
                    <table>
                        <tr class="section">
                            <th><?php _e( 'Contact', 'dog-eclipse' ); ?></th>
                            <td></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Name', 'dog-eclipse' ); ?></th>
                            <td><?php echo esc_html( $registration->owner_name ); ?></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Address', 'dog-eclipse' ); ?></th>
                            <td><?php echo esc_html( $registration->address ); ?></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Phone', 'dog-eclipse' ); ?></th>
                            <td><a href="tel:<?php echo esc_html( $registration->phone ); ?>"><?php echo esc_html( $registration->phone ); ?></a></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Backup Phone', 'dog-eclipse' ); ?></th>
                            <td><a href="tel:<?php echo esc_html( $registration->phone2 ); ?>"><?php echo esc_html( $registration->phone2 ); ?></a></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Vet Contact', 'dog-eclipse' ); ?></th>
                            <td><?php echo esc_html( $registration->vet_contact ); ?></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Emergency Contact', 'dog-eclipse' ); ?></th>
                            <td><?php echo esc_html( $registration->emergency_contact ); ?></td>
                        </tr>


                        <tr class="section">
                            <th><?php _e( 'Agreement', 'dog-eclipse' ); ?></th>
                            <td></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Consent', 'dog-eclipse' ); ?></th>
                            <td class="<?php echo $registration->consent === 'yes' ? 'consent-yes' : 'consent-no' ?>"><?php echo $registration->consent === 'yes' ? __( 'Yes', 'dog-eclipse' ) : __( 'No', 'dog-eclipse' ); ?></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Signed', 'dog-eclipse' ); ?></th>
                            <td><?php echo esc_html( $registration->signed ); ?></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Date', 'dog-eclipse' ); ?></th>
                            <td><?php echo esc_html( $registration->date ); ?></td>
                        </tr>

                        <tr class="section">
                            <th><?php _e( 'Submission', 'dog-eclipse' ); ?></th>
                            <td></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Date & Time', 'dog-eclipse' ); ?></th>
                            <td><?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $registration->submission_date ) ); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="additional-notes">
                    <h3><?php _e( 'Additional Notes', 'dog-eclipse' ); ?></h3>
                    <p><?php echo nl2br( esc_html( $registration->additional_notes ) ); ?></p>
                </div>

            </div>

            <div class="dogs-details-wrapper">
                <div class="dog-details-head">
                    <h3><?php _e( 'Dog Details', 'dog-eclipse' ); ?></h3>
                    <div class="dog-eclipse-action">
                        <a href="<?php echo esc_url( $delete_url ); ?>" class="delete-registration delete-item">
                        <img src="<?php echo DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/img/trash.svg' ?>" alt="trash">
                        <?php _e( 'Delete Registration', 'dog-eclipse' ); ?>
                        </a>
                    </div>
                </div>

                <div class="all-dogs">
                    <?php if ( !empty( $dogs ) && is_array( $dogs ) ): ?>
<?php foreach ( $dogs as $index => $dog ): ?>
                            <div class="dog-item">
                                <h4><?php _e( 'Dog', 'dog-eclipse' ); ?> #<?php echo $index + 1; ?></h4>
                                <table>
                                    <tr>
                                        <th><?php _e( 'Name', 'dog-eclipse' ); ?></th>
                                        <td><?php echo esc_html( $dog['name'] ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Breed/Sex', 'dog-eclipse' ); ?></th>
                                        <td><?php echo esc_html( $dog['breed_sex'] ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Age', 'dog-eclipse' ); ?></th>
                                        <td><?php echo esc_html( $dog['age'] ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Neutered', 'dog-eclipse' ); ?></th>
                                        <td><?php echo esc_html( $dog['neutered'] ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Kennels', 'dog-eclipse' ); ?></th>
                                        <td><?php echo nl2br( esc_html( $dog['kennels'] ) ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Vaccination Proof', 'dog-eclipse' ); ?></th>
                                        <td><?php echo nl2br( esc_html( $dog['vaccination'] ) ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Kennel Vaccination', 'dog-eclipse' ); ?></th>
                                        <td><?php echo nl2br( esc_html( $dog['kennel_vaccination'] ) ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Certificate', 'dog-eclipse' ); ?></th>
                                        <td class="certificate">

                                            <?php
                                            if ( empty( $dog['certificate_path'] ) ) {?>
                                                    <span class="no-certificate"><?php _e( 'No certificate uploaded', 'dog-eclipse' ); ?></span>
                                                <?php } else {?>
                                                    <a href="<?php echo esc_url( $dog['certificate_path'] ); ?>" target="_blank" class="button dog-btn">
                                                        <img src="<?php echo DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/img/external-link.svg' ?>" alt="link"><?php _e( 'View', 'dog-eclipse' ); ?>
                                                    </a>
                                                    <a href="<?php echo esc_url( $dog['certificate_path'] ); ?>" download class="button dog-btn">
                                                        <img src="<?php echo DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/img/download.svg' ?>" alt="link"><?php _e( 'Download', 'dog-eclipse' ); ?>
                                                    </a>
                                               <?php }
                                               ?>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Assessments area start -->
                                 <div class="assessments">
                                    <h3><?php _e( 'Assessments', 'dog-eclipse' ); ?></h3>
                                    <?php if ( !empty( $dog['assessments'] ) && is_array( $dog['assessments'] ) ): ?>
<?php foreach ( $dog['assessments'] as $key => $assessment ): ?>
                                            <div class="assessment-item">
                                                <div class="assessment-head">
                                                    <!-- get author name by id  -->
                                                    <?php
                                                        $user        = get_user_by( 'id', $assessment['author'] );
                                                        $author_name = $user->display_name;
                                                    ?>
                                                    <div class="assessment-meta">
                                                        <p class="author">
                                                            <img src="<?php echo DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/img/user.svg' ?>" alt="user">
                                                            <?php echo $author_name; ?>
                                                        </p>
                                                        <p class="date">
                                                            <img src="<?php echo DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/img/calendar.svg' ?>" alt="calendar">
                                                            <?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $assessment['submitted'] ) ); ?>
                                                        </p>
                                                    </div>
                                                    <span class="separator"></span>
                                                    <a href="<?php echo esc_url( add_query_arg( array(
                                                                     'page'            => 'dog-eclipse-registration-details',
                                                                     'action'          => 'delete_assessment',
                                                                     'registration_id' => $registration->id,
                                                                     'dog_index'       => $index,
                                                                     'assessment_key'  => $key,
                                                                 '_wpnonce'        => wp_create_nonce( 'delete_assessment_action' ),
                                                             ), admin_url( 'admin.php' ) ) ); ?>" class="delete-assessment-link">
                                                            <img src="<?php echo DOG_ECLIPSE_PLUGIN_URL . 'admin/assets/img/trash.svg' ?>" alt="delete">
                                                            </a>
                                                </div>
                                                <p><?php echo esc_html( $assessment['text'] ); ?></p>
                                            </div>
                                        <?php endforeach; ?>
<?php else: ?>
                                        <p><?php _e( 'No assessments.', 'dog-eclipse' ); ?></p>
                                    <?php endif; ?>

                                 </div>
                                <div class="add-assessment">
                                    <h4><?php _e( 'Add Assessment', 'dog-eclipse' ); ?></h4>
                                    <form method="post" action="">
                                        <textarea required name="assessment" id="assessment" cols="30" rows="3"></textarea>
                                        <input type="hidden" name="dog_index" value="<?php echo esc_attr( $index ); ?>">
                                        <input type="hidden" name="registration_id" value="<?php echo esc_attr( $registration->id ); ?>">
                                        <?php wp_nonce_field( 'add_assessment_action', 'add_assessment_nonce' ); ?>
                                        <input type="submit" name="submit_assessment" value="<?php _e( '+ Add New Assessment', 'dog-eclipse' ); ?>">
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
<?php else: ?>
                        <p><?php _e( 'No dog details available.', 'dog-eclipse' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>