jQuery(document).ready(function($) {
    $('#create-new-tooltip').on('click', function() {
        // Prepare form data
        var formData = {
            'action': 'create_new_tooltip',
            'title': $('#new-tooltip-title').val(),
            'text': $('#new-tooltip-text').val(),
            'class': $('#new-tooltip-class').val(),
            'position': $('#new-tooltip-position').val(),
            'icon': $('#new-tooltip-icon').val(),
            'nonce': $('#tooltipwp_nonce').val()
        };

        // AJAX post request
        $.post(ajaxurl, formData, function(response) {
            alert('Tooltip Created: ID ' + response);
            // Clear the form or provide other feedback
        }).fail(function() {
            alert('Error: Tooltip could not be created');
        });
    });
});
