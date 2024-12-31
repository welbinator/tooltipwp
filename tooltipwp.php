<?php
/**
 * Plugin Name: ToolTipWP
 * Plugin URI: https://roadmapwp.com/tooltipwp
 * Description: A plugin to easily add tooltips to your WordPress site.
 * Version: 1.0.2
 * Author: James Welbes
 * Author URI: https://roadmapwp.com
 * License: GPL v2 or later
 * Text Domain: tooltipwp
 */

namespace TooltipWP;

defined( 'ABSPATH' ) || exit;

define('TOOLTIP_FOR_WP_PATH', plugin_dir_path(__FILE__));

require_once plugin_dir_path( __FILE__ ) . 'app/functions.php';

require_once plugin_dir_path( __FILE__ ) . 'app/ajax-handlers.php';

if (file_exists(TOOLTIP_FOR_WP_PATH . 'github-update.php')) {
    include TOOLTIP_FOR_WP_PATH . 'github-update.php';
} else {
    error_log('github-update.php not found in ' . TOOLTIP_FOR_WP_PATH);
}

/**
 * Enqueues scripts and styles for the plugin.
 *
 * Loads the necessary CSS and JS files for the plugin's functionality.
 */
function enqueue_scripts() {
	wp_enqueue_style( 'tooltipwp-fontawesome', plugins_url( 'build/scripts.css', __FILE__ ) );
	wp_enqueue_style( 'tooltipwp-style', plugins_url( 'app/assets/css/tooltipwp.css', __FILE__ ) );
	wp_enqueue_script( 'tooltipwp-build-script', plugins_url( 'build/scripts.js', __FILE__ ), array(), '1.0.2', true );
	wp_enqueue_script( 'tooltipwp-script', plugins_url( 'app/assets/js/tooltipwp.js', __FILE__ ), array( 'jquery' ), '1.0.2', true );
	localize_script();
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts' );

/**
 * Enqueues admin-specific scripts for the plugin.
 */
function enqueue_admin_scripts() {
    wp_enqueue_script(
        'tooltipwp-admin-script',
        plugins_url('app/assets/js/admin-scripts.js', __FILE__),
        array('jquery'),
        '1.0.2',
        true
    );

    // Localize script for AJAX
    wp_localize_script(
        'tooltipwp-admin-script',
        'ajax_object',
        array('ajaxurl' => admin_url('admin-ajax.php'))
    );
}

add_action('admin_enqueue_scripts', 'TooltipWP\\enqueue_admin_scripts');

/**
 * Localizes the tooltip script.
 *
 * Retrieves tooltip data from the database and passes it to the frontend
 * for use in the tooltipwp-script.
 */
function localize_script() {
	$args         = array(
		'post_type'      => 'tooltip',
		'posts_per_page' => -1,
	);
	$tooltips     = get_posts( $args );
	$tooltip_data = array();
	foreach ( $tooltips as $tooltip ) {
		$text     = get_post_meta( $tooltip->ID, '_tooltip_text', true );
		$class    = get_post_meta( $tooltip->ID, '_tooltip_class', true );
		$position = get_post_meta( $tooltip->ID, '_tooltip_position', true );
		$icon     = get_post_meta( $tooltip->ID, '_tooltip_icon', true );

		// Log individual values
		error_log( 'Tooltip ID: ' . $tooltip->ID );
		error_log( 'Text: ' . $text );
		error_log( 'Class: ' . $class );
		error_log( 'Position: ' . $position );
		error_log( 'Icon: ' . $icon );

		$tooltip_data[] = array(
			'text'     => $text,
			'class'    => $class,
			'position' => $position,
			'icon'     => $icon,
		);
	}
	error_log( print_r( $tooltip_data, true ) ); // Log tooltip data
	wp_localize_script( 'tooltipwp-script', 'tooltipData', $tooltip_data );
}

/**
 * Registers a custom post type for tooltips.
 *
 * This function creates a new post type in the WordPress admin
 * specifically for managing tooltip content.
 */
function register_tooltip_cpt() {
	$args = array(
		'public'   => true,
		'label'    => 'Tooltips',
		'supports' => array( 'title' ),
	);

	register_post_type( 'tooltip', $args );
}

add_action( 'init', 'TooltipWP\\register_tooltip_cpt' );

/**
 * Outputs the HTML for the tooltip position meta box.
 *
 * @param WP_Post $post The current post object.
 */
function position_meta_box_html( $post ) {
	$value = get_post_meta( $post->ID, '_tooltip_position', true );
	if ( ! $value ) {
		$value = 'after'; // Set default to 'after'
	}
	?>
	<input type="radio" name="tooltip_position" value="before" <?php checked( $value, 'before' ); ?>> Before<br>
	<input type="radio" name="tooltip_position" value="after" <?php checked( $value, 'after' ); ?>> After<br>
	<?php
}

/**
 * Adds meta boxes for the tooltip custom post type.
 *
 * Registers several meta boxes used for managing additional tooltip data
 * such as text, class, position, and icon.
 */
function add_tooltip_meta_boxes() {
	add_meta_box(
		'tooltip_text',
		'Tooltip Text',
		'TooltipWP\\tooltip_meta_box_html',
		'tooltip',
		'normal',
		'default'
	);

	add_meta_box(
		'tooltip_class',
		'Tooltip Class',
		'TooltipWP\\class_meta_box_html',
		'tooltip',
		'normal',
		'default'
	);

	add_meta_box(
		'tooltip_position',
		'Tooltip Position',
		'TooltipWP\\position_meta_box_html',
		'tooltip',
		'side',
		'default'
	);

	add_meta_box(
		'tooltip_icon',
		'Tooltip Icon',
		'TooltipWP\\icon_meta_box_html',
		'tooltip',
		'normal',
		'default'
	);
}

add_action( 'add_meta_boxes', 'TooltipWP\\add_tooltip_meta_boxes' );

/**
 * Outputs the HTML for the tooltip text meta box.
 *
 * @param WP_Post $post The current post object.
 */
function tooltip_meta_box_html( $post ) {
	$value = get_post_meta( $post->ID, '_tooltip_text', true );
	echo '<input type="text" name="tooltip_text" value="' . esc_attr( $value ) . '">';
}

/**
 * Outputs the HTML for the tooltip icon meta box.
 *
 * @param WP_Post $post The current post object.
 */
function icon_meta_box_html( $post ) {
	$value = get_post_meta( $post->ID, '_tooltip_icon', true );
	if ( ! $value ) {
		$value = 'fa-solid fa-circle-info'; // Default value as unicode
	}
	echo '<input type="text" name="tooltip_icon" value="' . esc_attr( $value ) . '">';
}

/**
 * Outputs the HTML for the tooltip class meta box.
 *
 * @param WP_Post $post The current post object.
 */
function class_meta_box_html( $post ) {
	$value = get_post_meta( $post->ID, '_tooltip_class', true );
	echo '<input type="text" name="tooltip_class" value="' . esc_attr( $value ) . '">';
}

/**
 * Saves the data from the tooltip meta boxes.
 *
 * Handles the saving of meta box data when a tooltip post is saved.
 *
 * @param int $post_id The ID of the current post being saved.
 */
function save_tooltip_meta_boxes_data( $post_id ) {
	error_log( 'POST data: ' . print_r( $_POST, true ) );
	if ( array_key_exists( 'tooltip_text', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_tooltip_text',
			$_POST['tooltip_text']
		);
	}
	if ( array_key_exists( 'tooltip_class', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_tooltip_class',
			$_POST['tooltip_class']
		);
	}
	if ( isset( $_POST['tooltip_position'] ) ) {
		update_post_meta( $post_id, '_tooltip_position', sanitize_text_field( $_POST['tooltip_position'] ) );
		error_log( 'Position saved: ' . sanitize_text_field( $_POST['tooltip_position'] ) );
	}
	if ( isset( $_POST['tooltip_icon'] ) ) {
		update_post_meta( $post_id, '_tooltip_icon', sanitize_text_field( $_POST['tooltip_icon'] ) );
		error_log( 'Icon saved: ' . sanitize_text_field( $_POST['tooltip_icon'] ) );
	}
}

add_action( 'save_post', 'TooltipWP\\save_tooltip_meta_boxes_data' );


/**
 * Function to execute during plugin activation.
 *
 * Sets up necessary plugin data and structures upon activation.
 */
function activate() {
	// Actions to perform on activation
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

/**
 * Function to execute during plugin deactivation.
 *
 * Handles clean-up tasks or resets when the plugin is deactivated.
 */
function deactivate() {
	// Actions to perform on deactivation
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );
