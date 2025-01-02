jQuery(document).ready(function($) {
    console.log(tooltipData); // Log the entire data set
    tooltipData.forEach(function(tooltip) {
        console.log('Processing tooltip:', tooltip);
        $('.' + tooltip.class).each(function() {
            $(this).addClass('tooltipwp_tooltip');
            // $(this).attr('data-tooltip', tooltip.text);

            // Create an <i> element with the Font Awesome class
            var iconClass = 'tooltip-icon ' + tooltip.icon;
            var iconElement = $('<i>').addClass(iconClass);

            // Append the <i> element after the tooltip element
            $(this).append(iconElement);
            iconElement.attr('data-tooltip', tooltip.text);

            console.log('Element:', this, 'Data-tooltip:', tooltip.text, 'Icon class:', iconClass);
        });
    });
});