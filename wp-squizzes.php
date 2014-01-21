<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   WP Squizzes
 * @author    Diego de Oliveira <diego@favolla.com.br>
 * @link      https://github.com/diegoliv/WP-squizzes
 * @copyright 2013 Diego de Oliveira
 *
 * @wordpress-plugin
 * Plugin Name:       WP Squizzes
 * Plugin URI:        https://github.com/diegoliv/WP-squizzes
 * Description:       Plugin that adds a space for quizzes, with questions, questions types, among others resources.
 * Version:           1.0.0
 * Author:            Diego de Oliveira
 * Author URI:        https://github.com/diegoliv/
 * Text Domain:       wp-squizzes
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-wp-squizzes.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'WP_Squizzes', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Squizzes', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'WP_Squizzes', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	// adds the Settings API Wrapper Class
	require_once( plugin_dir_path( __FILE__ ) .'admin/includes/settings-api-wrapper.php' );

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-squizzes-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Squizzes_Admin', 'get_instance' ) );


}
