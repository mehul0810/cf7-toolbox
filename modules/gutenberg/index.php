<?php
/**
 * CF7 Toolbox - Gutenberg Block.
 *
 * Gutenberg Module will be used to place the contact form any where in the post using Gutenberg.
 *
 * @package CF7 - ToolBox
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 * @since   1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Enqueue the block's assets for the editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0
 */
function cf7_toolbox_block_editor_assets() {
	// Scripts.
	wp_enqueue_script(
		'cf7-toolbox-block',
		plugins_url( 'block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'block.js' )
	);

}

add_action( 'enqueue_block_editor_assets', 'cf7_toolbox_block_editor_assets' );

/**
 * Render Block of Contact Form 7 ShortCode.
 *
 * @param array $attributes List of attributes.
 * @param int   $form_id    Contact Form ID.
 *
 * @since 1.0
 *
 * @return string
 */
function cf7_toolbox_render_block( $attributes, $form_id ) {
	return do_shortcode( '[contact-form-7 id="' . $form_id . '"]' );
}

/**
 * Register Gutenberg block of CF7 ToolBox.
 *
 * @since 1.0
 */
register_block_type(
	'cf7-toolbox/contact-form',
	array(
		'render_callback' => 'cf7_toolbox_render_block',
	)
);

/**
 * Add Support of Rest API to Contact Form 7 CPT.
 *
 * @since 1.0
 */
function cf7_toolbox_extend_rest_api_support() {

	global $wp_post_types;

	$wp_post_types['wpcf7_contact_form']->show_in_rest = true;
	$wp_post_types['wpcf7_contact_form']->rest_base = 'cf7toolbox';
	$wp_post_types['wpcf7_contact_form']->rest_controller_class = 'WP_REST_Posts_Controller';
}

add_action( 'init', 'cf7_toolbox_extend_rest_api_support', 30 );


