<?php

namespace TooltipWP\Functions;

add_action('add_meta_boxes', __NAMESPACE__ . '\\add_tooltip_creation_meta_box');

function add_tooltip_creation_meta_box() {
    add_meta_box(
        'tooltip-creation-box',            // Unique ID for the meta box
        'Create New Tooltip',              // Title of the meta box
        __NAMESPACE__ . '\\tooltip_creation_meta_box_html',  // Callback function to output the content
        ['post', 'page', 'your-other-cpt'],// Post types where the box should appear
        'side',                            // Context (side for right-hand side)
        'default'                          // Priority
    );
}

/**
 * Outputs the HTML for the tooltip creation meta box.
 *
 * @param WP_Post $post The current post object.
 */
function tooltip_creation_meta_box_html( $post ) {
    // Nonce field for security
    wp_nonce_field('tooltipwp_create_nonce', 'tooltipwp_nonce');
    ?>
    <div class="tooltip-creation-form">
        <p>
            <label for="new-tooltip-title">Tooltip Title:</label>
            <input type="text" id="new-tooltip-title" name="new_tooltip_title" value="" class="widefat">
        </p>
        <p>
            <label for="new-tooltip-text">Tooltip Text:</label>
            <input type="text" id="new-tooltip-text" name="new_tooltip_text" value="" class="widefat">
        </p>
        <p>
            <label for="new-tooltip-class">Tooltip Class:</label>
            <input type="text" id="new-tooltip-class" name="new_tooltip_class" value="" class="widefat">
        </p>
        <p>
            <label for="new-tooltip-position">Tooltip Position:</label>
            <select id="new-tooltip-position" name="new_tooltip_position" class="widefat">
                <option value="before">Before</option>
                <option value="after" selected>After</option>
            </select>
        </p>
        <p>
            <label for="new-tooltip-icon">Tooltip Icon (CSS class):</label>
            <input type="text" id="new-tooltip-icon" name="new_tooltip_icon" value="fa-solid fa-circle-info" class="widefat">
        </p>
        <p>
            <button type="button" id="create-new-tooltip" class="button button-primary">Create New Tooltip</button>
        </p>
    </div>
    <?php
}

function tooltipwp_enqueue_with_inline_styles() {
    // Enqueue the main stylesheet
    wp_enqueue_style('tooltipwp-style', plugins_url('assets/css/tooltipwp.css', __FILE__));

    // Add inline styles dynamically
    $background_color = get_option('tooltipwp_background_color', '#ffc0cb'); // Default to pink
    $text_color = get_option('tooltipwp_text_color', '#000000'); // Default to black
    $border_color = get_option('tooltipwp_border_color', $background_color); 
    $border_thickness = get_option('tooltipwp_border_thickness', '1px'); 

    $custom_css = "
    .tooltip-icon::after {
        background-color: {$background_color};
        color: {$text_color};
        border: {$border_thickness}px solid {$border_color};
    }";

    // Add inline styles
    wp_add_inline_style('tooltipwp-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'TooltipWP\\Functions\\tooltipwp_enqueue_with_inline_styles');



