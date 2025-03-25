/**
 * Public JavaScript for Dog Eclipse Registration
 */
jQuery(document).ready(function ($) {
    let dogCount = 1; // Start with one dog
    const maxDogs = 4; // Maximum number of dogs allowed

    // Add new dog button click handler
    $('.add-new-dog-btn').on('click', function () {
        if (dogCount >= maxDogs) {
            alert('Maximum ' + maxDogs + ' dogs allowed per registration.');
            return;
        }

        console.log('clicked');

        dogCount++;

        // Clone the first dog details container
        const $firstDog = $('.dog-details').first();
        const $newDog = $firstDog.clone();

        // Update IDs and clear values
        $newDog.find('input[type="text"]').each(function () {
            const oldId = $(this).attr('id');
            const newId = oldId + '_' + dogCount;
            $(this).attr('id', newId);
            $(this).attr('name', $(this).attr('name') + '_' + dogCount);
            $(this).val(''); // Clear value
        });

        // Update textarea fields
        $newDog.find('textarea').each(function () {
            const oldId = $(this).attr('id');
            const newId = oldId + '_' + dogCount;
            $(this).attr('id', newId);
            $(this).attr('name', $(this).attr('name') + '_' + dogCount);
            $(this).val(''); // Clear value
        });

        // Update checkbox fields
        $newDog.find('input[type="checkbox"]').each(function () {
            const oldId = $(this).attr('id');
            const newId = oldId + '_' + dogCount;
            $(this).attr('id', newId);
            $(this).attr('name', $(this).attr('name') + '_' + dogCount);
            $(this).prop('checked', false); // Uncheck

            // Also update the associated label's 'for' attribute
            const $label = $newDog.find('label[for="' + oldId + '"]');
            if ($label.length) {
                $label.attr('for', newId);
            }
        });

        // Update file input
        $newDog.find('input[type="file"]').each(function () {
            const oldId = $(this).attr('id');
            const newId = oldId + '_' + dogCount;
            $(this).attr('id', newId);
            $(this).attr('name', $(this).attr('name') + '_' + dogCount);
        });

        // Add remove button if not the first dog
        if (!$newDog.find('.remove-dog-btn').length) {
            $newDog.prepend(
                '<button type="button" class="remove-dog-btn">Remove</button>'
            );
        }

        // Add dog heading number
        $newDog.find('h2').text('Dog Details #' + dogCount);

        // Add the new dog section to the form before the "Add New Dog" button
        $newDog.insertBefore('.add-new-dog-btn');

        // Scroll to the new dog section
        $('html, body').animate(
            {
                scrollTop: $newDog.offset().top - 100,
            },
            500
        );
    });

    // Remove dog button click handler (delegated event)
    $(document).on('click', '.remove-dog-btn', function () {
        $(this).closest('.dog-details').remove();
        dogCount--;

        // Renumber the remaining dog sections
        $('.dog-details').each(function (index) {
            $(this)
                .find('h2')
                .text('Dog Details #' + (index + 1));
        });
    });

    // Hide success message after 3 seconds
    if ($('.dog-reg-success-message').length) {
        setTimeout(function () {
            $('.dog-reg-success-message').fadeOut();
        }, 3000);
    }
});
