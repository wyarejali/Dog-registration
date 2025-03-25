/**
 * Admin JavaScript for Dog Eclipse Registration
 */
jQuery(document).ready(function ($) {
    // Single item deletion confirmation
    $(document).on('click', '.delete-registration', function (e) {
        if (!confirm(dog_eclipse_vars.delete_confirm)) {
            e.preventDefault();
        }
    });

    // Bulk actions confirmation
    $('#doaction, #doaction2').on('click', function (e) {
        var selectedAction = $(this).prev('select').val();

        if (selectedAction === 'bulk-delete') {
            // Check if any items are selected
            if ($('input[name="registration[]"]:checked').length === 0) {
                alert('Please select at least one item to delete.');
                e.preventDefault();
                return;
            }

            if (!confirm(dog_eclipse_vars.bulk_delete_confirm)) {
                e.preventDefault();
            }
        }
    });

    // Handle expandable sections in registration details
    $('.toggle-section').on('click', function () {
        const $section = $(this).closest('.section');
        $section.find('.section-content').slideToggle(200);
        $(this).toggleClass('expanded');
    });

    // Initialize datepicker for filtering (if jQuery UI is available)
    if ($.fn.datepicker) {
        $('.date-filter').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
        });
    }

    // Select all checkbox functionality
    $('.wp-list-table thead .check-column input[type="checkbox"]').on(
        'change',
        function () {
            var isChecked = $(this).prop('checked');
            $('.wp-list-table tbody .check-column input[type="checkbox"]').prop(
                'checked',
                isChecked
            );
        }
    );
});
