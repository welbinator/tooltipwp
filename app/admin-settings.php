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
    // Register settings
    register_setting('tooltipwp_settings_group', 'tooltipwp_background_color');
    register_setting('tooltipwp_settings_group', 'tooltipwp_text_color');
    register_setting('tooltipwp_settings_group', 'tooltipwp_border_color');
    register_setting('tooltipwp_settings_group', 'tooltipwp_border_thickness');

    // Add a settings section
    add_settings_section(
        'tooltipwp_settings_section', // ID
        'Tooltip Appearance', // Title
        'tooltipwp_settings_section_callback', // Callback function
        'tooltipwp-settings' // Page slug
    );

    // Add fields
    add_settings_field(
        'tooltipwp_background_color',
        'Tooltip Background Color',
        'tooltipwp_background_color_callback',
        'tooltipwp-settings',
        'tooltipwp_settings_section'
    );

    add_settings_field(
        'tooltipwp_text_color',
        'Tooltip Text Color',
        'tooltipwp_text_color_callback',
        'tooltipwp-settings',
        'tooltipwp_settings_section'
    );

    add_settings_field(
        'tooltipwp_border_color',
        'Tooltip Border Color',
        'tooltipwp_border_color_callback',
        'tooltipwp-settings',
        'tooltipwp_settings_section'
    );

    add_settings_field(
        'tooltipwp_border_thickness',
        'Tooltip Border Thickness',
        'tooltipwp_border_thickness_callback',
        'tooltipwp-settings',
        'tooltipwp_settings_section'
    );
}

// Section callback
function tooltipwp_settings_section_callback() {
    echo '<p>Customize the appearance of your tooltips.</p>';
}

// Field callback for "Tooltip Background Color"
function tooltipwp_background_color_callback() {
    $value = get_option('tooltipwp_background_color', '#ffffff'); // Default to white
    $default = '#ffffff';
    echo '<input type="text" class="tooltipwp-color-picker" name="tooltipwp_background_color" value="' . esc_attr($value) . '" data-default-color="' . esc_attr($default) . '">';
    // echo '<button type="button" class="button tooltipwp-reset-color" data-default-color="' . esc_attr($default) . '">Reset</button>';
}

// Field callback for "Tooltip Text Color"
function tooltipwp_text_color_callback() {
    $value = get_option('tooltipwp_text_color', '#000000'); // Default to black
    $default = '#000000';
    echo '<input type="text" class="tooltipwp-color-picker" name="tooltipwp_text_color" value="' . esc_attr($value) . '" data-default-color="' . esc_attr($default) . '">';
    // echo '<button type="button" class="button tooltipwp-reset-color" data-default-color="' . esc_attr($default) . '">Reset</button>';
}

// Field callback for "Tooltip Border Color"
function tooltipwp_border_color_callback() {
    $value = get_option('tooltipwp_border_color', '#000000'); // Default to black
    $default = '#000000';
    echo '<input type="text" class="tooltipwp-color-picker" name="tooltipwp_border_color" value="' . esc_attr($value) . '" data-default-color="' . esc_attr($default) . '">';
    // echo '<button type="button" class="button tooltipwp-reset-color" data-default-color="' . esc_attr($default) . '">Reset</button>';
}

// Field callback for "Tooltip Border Thickness"
function tooltipwp_border_thickness_callback() {
    $value = get_option('tooltipwp_border_thickness', '1'); // Default to 1
    echo '<input type="number" name="tooltipwp_border_thickness" value="' . esc_attr($value) . '" min="0" step="1">';
}
