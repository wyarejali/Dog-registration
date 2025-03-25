<?php
    /**
     * Registration form template
     */

    // Exit if accessed directly
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }
?>


<?php if ( isset( $_GET['submitted'] ) ): ?>
    <div class="dog-reg-success-message"><b><?php _e( 'Success!', 'dog-eclipse' ); ?></b><?php _e( 'Submission successful!', 'dog-eclipse' ); ?></div>
<?php endif; ?>

<form class="dog-registration-form" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="der_form_submit">
    <?php wp_nonce_field( 'dog_eclipse_reg', 'dog_eclipse_reg_nonce' ); ?>

    <!-- Dog Details -->
    <h2><?php _e( 'Dog Details', 'dog-eclipse' ); ?></h2>
    <div class="dog-details">

        <div class="input-group">
            <div class="form-control">
                <input required type="text" name="dog_name" id="dog_name" placeholder="Dogs Name*">
            </div>
            <div class="form-control">
                <input required type="text" name="breed_sex" id="sex" placeholder="Breed/Sex*">
            </div>
            <div class="form-control">
                <input required type="text" name="age" id="age" placeholder="Age*">
            </div>
            <div class="form-control">
                <input required type="text" name="neutered" id="neutered" placeholder="Neutered Y/N*">
            </div>
        </div>

        <div class="form-control">
            <textarea name="feeding_guide" rows="3" id="Feeding Guide" placeholder="Feeding Guide"></textarea>
        </div>

        <div class="form-control">
            <textarea name="medical_notes" rows="3" id="medical_notes" placeholder="Medical Notes"></textarea>
        </div>

        <div class="input-group checkbox-group">
            <div class="form-control">
                <input type="checkbox" name="vaccination" id="vaccination">
                <label for="vaccination">Vaccination Proof Full Booster</label>
            </div>

            <div class="form-control">
                <input type="checkbox" name="kennel_vaccination" id="kennel_vaccination">
                <label for="kennel_vaccination">Kennel Vaccination</label>
            </div>
        </div>

        <div class="certificate-upload">
            <label for="certificate" class="form-control">
                <span>Upload Certificate</span>
                <input type="file" name="certificate" id="certificate">
            </label>
        </div>
    </div>

    <button type="button" class="add-new-dog-btn">Add New Dog</button>

    <!-- Additional notes -->
    <div class="form-control">
        <textarea name="additional_notes" rows="5" placeholder="Additional Notes / Belongings"></textarea>
    </div>

    <!-- Owner Details -->
    <h2><?php _e( 'Owner Details', 'dog-eclipse' ); ?></h2>
    <div class="owner-details">
        <div class="form-control">
            <input required type="text" name="owner_name" id="owner_name" placeholder="Name*">
        </div>

        <div class="form-control">
            <input required type="text" name="address" id="address" placeholder="Address*">
        </div>
        <div class="form-control">
            <input required type="number" name="phone" id="phone" placeholder="Phone*">
        </div>

        <div class="form-control">
            <input required type="text" name="vet_contact" id="vet_contact" placeholder="Vet Contact*">
        </div>

        <div class="form-control">
            <input required type="text" name="emergency_contact" id="emergency_contact" placeholder="Emergency Contact*">
        </div>
    </div>

    <div class="agreement">
        <p class="agreement-text">I Understand the inherent risks involved in my dog(s) exercising with other dogs whilst under super supervision.
            <b>
                <input type="radio" name="consent" id="do_consent" value="do_consent">
                <label for="do_consent">I do</label>
                <input type="radio" name="consent" id="no_consent" value="no_consent">
                <label for="no_consent">I do not</label>
            </b> consent to my dog(s) exercising with other dogs under supervision. Neither Dog Eclipse Limited nor its staff shall be liable for any injury to any dog caused by rough play, kennel cough or otherwise.</p>

        <div class="input-group">
            <div class="form-control">
                <input required type="text" name="signed" id="signed" placeholder="Signed*">
            </div>
            <div class="form-control">
                <input required type="date" name="date" id="date">
            </div>
        </div>
    </div>

    <p>
        <input class="dog-submit-btn" type="submit" value="<?php _e( 'Submit', 'dog-eclipse' ); ?>">
    </p>
</form>
