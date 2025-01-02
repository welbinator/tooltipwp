jQuery(document).ready(function($) {
    console.log(tooltipData); // Log the entire data set
    tooltipData.forEach(function(tooltip) {
        console.log('Processing tooltip:', tooltip);
        $('.' + tooltip.class).each(function() {
            $(this).addClass('tooltipwp_tooltip');

            // Create an <i> element with the Font Awesome class
            var iconClass = 'tooltip-icon ' + tooltip.icon;
            var iconElement = $('<i>').addClass(iconClass);

            // Append the <i> element in the appropriate position based on tooltip.position
            if (tooltip.position === 'before') {
                $(this).prepend(iconElement); // Adds the tooltip before the element
            } else {
                $(this).append(iconElement); // Adds the tooltip after the element
            }

            iconElement.attr('data-tooltip', tooltip.text);
            console.log('Element:', this, 'Data-tooltip:', tooltip.text, 'Icon class:', iconClass, 'Position:', tooltip.position);
        });
    });
});
