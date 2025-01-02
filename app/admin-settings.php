<?php

// Hook into admin_menu to add the submenu page
add_action('admin_menu', 'tooltipwp_add_settings_page');

function tooltipwp_add_settings_page() {
    // Add a submenu page under the "Tooltips" menu
    add_submenu_page(
        'edit.php?post_type=tooltip', // Parent slug for the custom post type
        'Tooltips Settings', // Page title
        'Settings', // Submenu title
        'manage_options', // Capability required to access
        'tooltipwp-settings', // Menu slug
        'tooltipwp_render_settings_page' // Callback function to render the settings page
    );
}

// Callback function to render the settings page
function tooltipwp_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Tooltips Settings</h1>
        <form method="post" action="options.php">
            <?php
            // Output security fields for the registered setting
            settings_fields('tooltipwp_settings_group');
            // Output setting sections and their fields
            do_settings_sections('tooltipwp-settings');
            // Output the submit button
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'tooltipwp_register_settings');

function tooltipwp_register_settings() {
    // Register settings for the two color options
    register_setting('tooltipwp_settings_group', 'tooltipwp_background_color');
    register_setting('tooltipwp_settings_group', 'tooltipwp_text_color');

    // Add a settings section
    add_settings_section(
        'tooltipwp_settings_section', // ID
        'Tooltip Appearance', // Title
        'tooltipwp_settings_section_callback', // Callback function
        'tooltipwp-settings' // Page slug
    );

    // Add the "Tooltip Background Color" field
    add_settings_field(
        'tooltipwp_background_color', // Field ID
        'Tooltip Background Color', // Title
        'tooltipwp_background_color_callback', // Callback function
        'tooltipwp-settings', // Page slug
        'tooltipwp_settings_section' // Section ID
    );

    // Add the "Tooltip Text Color" field
    add_settings_field(
        'tooltipwp_text_color', // Field ID
        'Tooltip Text Color', // Title
        'tooltipwp_text_color_callback', // Callback function
        'tooltipwp-settings', // Page slug
        'tooltipwp_settings_section' // Section ID
    );
}

// Section callback
function tooltipwp_settings_section_callback() {
    echo '<p>Customize the appearance of your tooltips.</p>';
}

// Field callback for "Tooltip Background Color"
function tooltipwp_background_color_callback() {
    $value = get_option('tooltipwp_background_color', '#ffffff'); // Default to white
    echo '<input type="color" name="tooltipwp_background_color" value="' . esc_attr($value) . '">';
}

// Field callback for "Tooltip Text Color"
function tooltipwp_text_color_callback() {
    $value = get_option('tooltipwp_text_color', '#000000'); // Default to black
    echo '<input type="color" name="tooltipwp_text_color" value="' . esc_attr($value) . '">';
}
