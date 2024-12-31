<?php

namespace TooltipWP\Functions;

add_action('add_meta_boxes', 'add_tooltip_creation_meta_box');

function add_tooltip_creation_meta_box() {
    add_meta_box(
        'tooltip-creation-box',            // Unique ID for the meta box
        'Create New Tooltip',              // Title of the meta box
        'tooltip_creation_meta_box_html',  // Callback function to output the content
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

