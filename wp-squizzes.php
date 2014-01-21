<?php
<<<<<<< HEAD
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
=======
/*
Plugin Name: WP Squizzes
Plugin URI: https://github.com/diegoliv/WP-squizzes
Description: A Wordpress Plugin to create awesome quizzes, built on top of Backbone.js.
Version: 0.1
Author: Diego de Oliveira
Author Email: diego@favolla.com.br
License:

  Copyright 2013 Diego de Oliveira (diego@favolla.com.br)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

class WPSquizzes {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'WP Squizzes';
	const slug = 'wp_squizzes';
	
	/**
	 * Constructor
	 */
	function __construct() {
		//register an activation hook for the plugin
		register_activation_hook( __FILE__, array( &$this, 'install_wp_squizzes' ) );

		//Hook up to the init action
		add_action( 'init', array( &$this, 'init_wp_squizzes' ) );
	}
  
	/**
	 * Runs when the plugin is activated
	 */  
	function install_wp_squizzes() {
		// do not generate any output here
	}
  
	/**
	 * Runs when the plugin is initialized
	 */
	function init_wp_squizzes() {
		// Setup localization
		load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		// Load JavaScript and stylesheets
		$this->register_scripts_and_styles();

	
		if ( is_admin() ) {
			//this will run when in the WordPress admin
		} else {
			//this will run when on the frontend
		}

		/*
		 * TODO: Define custom functionality for your plugin here
		 *
		 * For more information: 
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'your_action_here', array( &$this, 'action_callback_method_name' ) );
		add_filter( 'your_filter_here', array( &$this, 'filter_callback_method_name' ) );    
	}

	function action_callback_method_name() {
		// TODO define your action method here
	}

	function filter_callback_method_name() {
		// TODO define your filter method here
	}
  
	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			$this->load_file( self::slug . '-admin-script', '/js/admin.js', true );
			$this->load_file( self::slug . '-admin-style', '/css/admin.css' );
		} else {
			$this->load_file( self::slug . '-script', '/js/widget.js', true );
			$this->load_file( self::slug . '-style', '/css/widget.css' );
		} // end if/else
	} // end register_scripts_and_styles
	
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') ); //depends on jquery
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	} // end load_file
  
} // end class
new WPSquizzes();

?>
>>>>>>> e2a378f43e19bf9cf685937656dd991e5d4d41f2
