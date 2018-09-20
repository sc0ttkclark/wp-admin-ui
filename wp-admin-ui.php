<?php

/**
 * Include this file to use the library.
 *
 * @package WP_Admin_UI
 *
 * @version 1.12
 * @author  Scott Kingsley Clark
 * @link    https://www.scottkclark.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Setup constants.
if ( ! defined( 'WP_ADMIN_UI_EXPORT_DIR' ) ) {
	define( 'WP_ADMIN_UI_EXPORT_DIR', WP_CONTENT_DIR . '/exports' );
}

if ( ! defined( 'WP_ADMIN_UI_EXPORT_URL' ) ) {
	define( 'WP_ADMIN_UI_EXPORT_URL', content_url( basename( WP_ADMIN_UI_EXPORT_DIR ) ) );
}

if ( did_action( 'plugins_loaded' ) || doing_action( 'plugins_loaded' ) ) {
	wp_admin_ui_init();
} else {
	add_action( 'plugins_loaded', 'wp_admin_ui_init' );
}

add_action( 'wp_ajax_wp_admin_ui_export_download', 'wp_admin_ui_export_download' );

/**
 * Include the admin library and handle downloads.
 */
function wp_admin_ui_init() {

	include_once 'src/class-wp-admin-ui.php';

}

/**
 * Handle export downloads.
 */
function wp_admin_ui_export_download() {

	// Handle export downloads.
	if ( ! isset( $_GET['_wpnonce'] ) || false === wp_verify_nonce( $_GET['_wpnonce'], 'wp-admin-ui-export' ) ) {
		wp_die( 'Invalid request.' );
	}

	$file = sanitize_text_field( $_GET['export'] );

	if ( empty( $file ) ) {
		wp_die( 'File not found.' );
	}

	$file = str_replace( array( '\\', '/', '..' ), '', $file );

	$file_path = WP_ADMIN_UI_EXPORT_DIR . '/' . $file;
	$file_url  = WP_ADMIN_UI_EXPORT_URL . '/' . $file;

	$file_path = realpath( $file_path );

	require_once ABSPATH . 'wp-admin/includes/file.php';

	/**
	 * @var $wp_filesystem WP_Filesystem_Base
	 */
	global $wp_filesystem;

	WP_Filesystem();

	if ( ! $wp_filesystem ) {
		wp_die( 'Cannot access file.' );
	}

	if ( ! $wp_filesystem->exists( $file_path ) ) {
		wp_die( 'File not found.' );
	}

	/**
	 * Allow plugins to hook into the export download before it is delivered.
	 *
	 * @param string $file_url  File URL.
	 * @param string $file_path File path.
	 * @param string $file      File name.
	 */
	do_action( 'wp_admin_ui_export_download', $file_url, $file_path, $file );

	wp_redirect( $file_url );
	die();

}
