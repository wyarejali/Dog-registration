jQuery(document).ready(function ($) {
    console.log('Dog Eclipse Registration JS Loaded');

    $('#dog-registration-form').submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('action', 'handle_dog_registration');

        $.ajax({
            url: dogAjax.ajaxurl, // Ensure `ajaxurl` is defined in WordPress
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('.dog-submit-btn').val('Submitting...');
            },
            success: function (response) {
                if (response.success) {
                    $('#dog_reg_success')
                        .html(
                            '<p style="color: green;">' +
                                response.data.message +
                                '</p>'
                        )
                        .show();
                    $('#dog-registration-form')[0].reset(); // Reset form after successful submission
                } else {
                    $('#dog_reg_success')
                        .html(
                            '<p style="color: red;">' +
                                response.data.message +
                                '</p>'
                        )
                        .show();
                }
            },
            error: function () {
                $('#dog_reg_success')
                    .html(
                        '<p style="color: red;">An unexpected error occurred.</p>'
                    )
                    .show();
            },
        });
    });
});
