<?php
/**
 * Plugin Name.
 *
 * @package   WP_Squizzes_Admin
 * @author    Diego de Oliveira <diego@favolla.com.br>
 * @link      https://github.com/diegoliv/WP-squizzes
 * @copyright 2013 Diego de Oliveira
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package WP_Squizzes_Admin
 * @author  Diego de Oliveira <diego@favolla.com.br>
 */

/**
 * WordPress Simple Settings
 *
 * A simple wrapper class to handle the Settings API. (props to Clif Griffin - github.com/clifgriffin/wordpress-simple-settings )
 *
 * Include the framework only if another plugin has not already done so
 */


class WP_Squizzes_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Plugin_Name" to the name of your initial plugin class
		 *
		 */
		$plugin = WP_Squizzes::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// include WPAlchemy PHP Class for metaboxes (github.com/farinspace/wpalchemy)
		add_action( 'admin_init', array( $this, 'include_metabox_class'), 0 );

		// add custom metaboxes for the plugin CPT
		add_action( 'admin_init', array( $this, 'custom_metaboxes'), 0 );

		// add settings for the plugin page
		add_action( 'admin_init', array( $this, 'set_settings'), 0 );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// add custom javascript to load another instance of tinyMCE
		add_action('admin_print_footer_scripts',array( $this, 'resolution_tinymce' ),99);

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), WP_Squizzes::VERSION );
		}

		if( $screen->post_type =='wpsq-question' && $screen->base == 'post'){
			wp_enqueue_style( $this->plugin_slug .'-metaboxes', plugins_url( 'assets/css/meta.css', __FILE__ ), array(), WP_Squizzes::VERSION );
		} 

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), WP_Squizzes::VERSION );
		}

	}

	/**
	 * Include WPAlchemy Metabox PHP Class.
	 *
	 * @since     1.0.0
	 *
	 */
	public function include_metabox_class() {
		include_once( 'includes/MetaBox.php' );
	}

	/**
	 * Callback to initiate the metaboxes.
	 *
	 * @since     1.0.0
	 *
	 */
	public function custom_metaboxes() {

		$ts_answers = new WPAlchemy_MetaBox( array(
		    'id' => '_wpsq-answers',
		    'title' => __( 'Answers', $this->plugin_slug ),
		    'types' => array('wpsq-question'),
		    'template' => 'wpsq-answers.php',
		    // 'template' => plugins_url( 'includes/wpsq-answers.php', __FILE__ )
		) );

		$ts_resolution = new WPAlchemy_MetaBox( array(
		    'id' => '_wpsq-resolution',
		    'title' => __( 'Resolution', $this->plugin_slug ),
		    'types' => array('wpsq-question'),
		    'template' => 'wpsq-resolution.php',
		    // 'template' => plugins_url( 'includes/wpsq-resolution.php', __FILE__ )
		) );

	}


	/**
	 * Print custom javascript to load another instance of tinyMCE.
	 *
	 * @since     1.0.0
	 *
	 */
	public function resolution_tinymce() {

		$screen = get_current_screen();

		if( $screen->post_type =='wpsq-question' && $screen->base == 'post'){

	    ?><script type="text/javascript">/* <![CDATA[ */
	        jQuery(function($) {
	            var i=1;
	            $('.customEditor textarea').each(function(e) {
	                var id = $(this).attr('id');
	 
	                if (!id) {
	                    id = 'customEditor-' + i++;
	                    $(this).attr('id',id);
	                }
	 
	                tinyMCE.execCommand('mceAddControl', false, id);
	                 
	            });

	            // $('#wpa_loop-answers').sortable();
	        });
	    /* ]]> */</script><?php

		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Quizzes', $this->plugin_slug ),
			__( 'Quizzes', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Register settings fields and sections for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function set_settings(){

	    global $settings;

	    $options = array(
			array(
				'group' => 'wpsq-general-options',
				'tab' => __( 'General Settings', $this->plugin_slug ),
			),
			array(
				'group' => 'wpsq-results-options',
				'tab' => __( 'Quiz Results', $this->plugin_slug ),
			),
		);

		// initiate the setting class
		$settings = new WPSQ_Settings_Wrapper( $this->plugin_slug, $options );

		// add settings sections
		$settings->add_sections( array(
			array(
				'id' => 'wpsq-general-section',
				'title' => __( 'General Settings', $this->plugin_slug ),
				'page' => 'wpsq-general-options',
			),
			array(
				'id' => 'wpsq-css-section',
				'title' => __( 'Quiz UI Styling', $this->plugin_slug ),
				'page' => 'wpsq-general-options',
			),
		) );

		// add settings fields
	    $settings->add_fields( 'wpsq-general-section', 'wpsq-general-options', array(
	    	array( 
	    		'id' => 'quiz-system-title',
	    		'label' => __( 'Title for the Quiz System', $this->plugin_slug ),
	    		'desc' => __( 'Insert the title for the Quiz System. If none is provided, the title is not showed.', $this->plugin_slug ),
	    		'size' => 'regular',
	    		'type' => 'text',
	    		'default' => __( 'Quizzes', $this->plugin_slug ),
	    	),
	    	array( 
	    		'id' => 'min_questions',
	    		'label' => __( 'Minimum number of questions', $this->plugin_slug ),
	    		'desc' => __( 'Insert the minimum number of questions required to start a quiz. Default: 1 (if none is provided).', $this->plugin_slug ),
	    		'size' => 'small',
	    		'type' => 'number',
	    		'default' => 2,
	    		'min' => 1
	    	),
	    	array( 
	    		'id' => 'max_questions',
	    		'label' => __( 'Maximum number of questions', $this->plugin_slug ),
	    		'desc' => __( 'Insert the maximum number of questions required to start a quiz. If the number provided is greater than the number of questions posted, then the total number of questions is used as the maximum number. Default: 30 (if none is provided).', $this->plugin_slug ),
	    		'size' => 'small',
	    		'type' => 'number',
	    		'default' => 30,
	    		'min' => 1
	    	),
	    ) );

		// add settings fields
	    $settings->add_fields( 'wpsq-css-section', 'wpsq-general-options', array(
	    	array( 
	    		'id' => 'remove_css',
	    		'label' => __( 'Remove default stylesheet', $this->plugin_slug ),
	    		'desc' => __( 'Check this if you want to remove the default stylesheet loaded with the plugin.', $this->plugin_slug ),
	    		'type' => 'checkbox',
	    	)
	    ) );
		

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		// finally, include the view of the settings page
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

}