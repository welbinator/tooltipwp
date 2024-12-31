<?php

namespace TooltipWP\Ajax;

add_action('wp_ajax_create_new_tooltip', 'create_new_tooltip_handler');

function create_new_tooltip_handler() {
    // Check for nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tooltipwp_create_nonce')) {
        wp_die('Security check failed');
    }

    // Sanitize and prepare post data
    $new_post = array(
        'post_title'   => sanitize_text_field($_POST['title']),
        'post_type'    => 'tooltip',
        'post_status'  => 'publish'
    );

    // Insert the post and get the new post ID
    $post_id = wp_insert_post($new_post);

    // Check if post creation was successful
    if ($post_id == 0) {
        wp_die('Error creating Tooltip');
    }

    // Save additional meta data
    if (isset($_POST['text'])) {
        update_post_meta($post_id, '_tooltip_text', sanitize_text_field($_POST['text']));
    }
    if (isset($_POST['class'])) {
        update_post_meta($post_id, '_tooltip_class', sanitize_text_field($_POST['class']));
    }
    if (isset($_POST['position'])) {
        update_post_meta($post_id, '_tooltip_position', sanitize_text_field($_POST['position']));
    }
    if (isset($_POST['icon'])) {
        update_post_meta($post_id, '_tooltip_icon', sanitize_text_field($_POST['icon']));
    }

    // Send back the new post ID
    echo $post_id;
    wp_die(); // Terminate AJAX execution
}

