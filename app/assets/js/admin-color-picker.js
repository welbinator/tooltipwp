jQuery(document).ready(function($) {
    // Initialize the WordPress color picker
    $('.tooltipwp-color-picker').each(function() {
        var $input = $(this);
        var defaultColor = $input.data('default-color'); // Get default color

        // Initialize wpColorPicker
        $input.wpColorPicker();

        // Add Reset button functionality
        $input.siblings('.tooltipwp-reset-color').on('click', function() {
            $input.wpColorPicker('color', defaultColor); // Reset to default color
        });
    });
});
