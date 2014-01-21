<?php
/**
 * Plugin Name.
 *
 * @package   WP_Squizzes
 * @author    Diego de Oliveira <diego@favolla.com.br>
 * @link      https://github.com/diegoliv/WP-squizzes
 * @copyright 2013 Diego de Oliveira
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @package WP_Squizzes
 * @author  Diego de Oliveira <diego@favolla.com.br>
 */
class WP_Squizzes {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'wp-squizzes';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	// constant
	protected $plugin_dir = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ), 0 );

		// register custom post type
		add_action( 'init', array( $this, 'register_cpt'), 0 );
		add_action( 'init', array( $this, 'register_taxonomy'), 0 );

		// Load shortcodes
		add_action( 'template_redirect', array( $this, 'shortcodes' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// AJAX call to get the questions for the quiz
		add_action( 'wp_ajax_nopriv_get_questions', array( $this, 'get_questions' ) );
        add_action( 'wp_ajax_get_questions', array( $this, 'get_questions' ) ); 
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return the plugin directory.
	 *
	 * @since    1.0.0
	 *
	 * @return   Plugin slug variable.
	 */
	public function get_plugin_directory() {
		$this->plugin_dir = WP_PLUGIN_DIR . '/'. $this->get_plugin_slug();
		return $this->plugin_dir;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		$mofile = $this->get_plugin_directory() . '/languages/' . $domain . '-' . $locale . '.mo';

		// load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_textdomain( $domain, $mofile );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post;

		wp_register_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/wpsq-ui-public.css', __FILE__ ), array(), self::VERSION );

		$options = get_option( 'wpsq-general-options' );

		if( !$options['remove_css'] ){

			if( has_shortcode( $post->post_content, 'wpsq-ui' ) ) {
				wp_enqueue_style( $this->plugin_slug . '-plugin-styles' );
			}

		}

	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/wpsq-ui-public.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
	}


	/**
	 * Register the custom post type with filters to change the default behavior
	 *
	 * @since     1.0.0
	 *
	 * @return    null
	 */
	function register_cpt() {

		$labels = apply_filters( 'wpsq_post_type_labels', array(
			'name'                => _x( 'Questions', 'Post Type General Name', $this->plugin_slug ),
			'singular_name'       => _x( 'Question', 'Post Type Singular Name', $this->plugin_slug ),
			'menu_name'           => __( 'Questions', $this->plugin_slug ),
			'parent_item_colon'   => __( 'Parent Question:', $this->plugin_slug ),
			'all_items'           => __( 'All Questions', $this->plugin_slug ),
			'view_item'           => __( 'View Questions', $this->plugin_slug ),
			'add_new_item'        => __( 'Add New Question', $this->plugin_slug ),
			'add_new'             => __( 'New Question', $this->plugin_slug ),
			'edit_item'           => __( 'Edit Question', $this->plugin_slug ),
			'update_item'         => __( 'Update Question', $this->plugin_slug ),
			'search_items'        => __( 'Search questions', $this->plugin_slug ),
			'not_found'           => __( 'No questions found', $this->plugin_slug ),
			'not_found_in_trash'  => __( 'No questions found in Trash', $this->plugin_slug ),
		) );

		$rewrite = array(
			'slug'                => apply_filters( 'wpsq_post_type_slug' , _x( 'question', 'Post Type Slug', $this->plugin_slug ) ),
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => false,
		);

		$args = apply_filters( 'wpsq_post_type_args', array(
			'label'               => _x( 'Questions', 'Post Type Label', $this->plugin_slug ),
			'description'         => __( 'Questions for the tests.', $this->plugin_slug ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'comments', 'revisions' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-editor-help', // dashicons - since WordPress 3.8
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		) );

		register_post_type( 'wpsq-question', $args );

	}


	/**
	 * Register the custom taxonomy with filters to change the default behavior
	 *
	 * @since     1.0.0
	 *
	 * @return    null
	 */
	function register_taxonomy()  {

		$labels = apply_filters( 'wpsq_taxonomy_labels' ,array(
			'name'                       => _x( 'Question Types', 'Taxonomy General Name', $this->plugin_slug ),
			'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', $this->plugin_slug ),
			'menu_name'                  => __( 'Types', $this->plugin_slug ),
			'all_items'                  => __( 'All Question Types', $this->plugin_slug ),
			'parent_item'                => __( 'Parent Question Type', $this->plugin_slug ),
			'parent_item_colon'          => __( 'Parent Question Type:', $this->plugin_slug ),
			'new_item_name'              => __( 'New Question Type Name', $this->plugin_slug ),
			'add_new_item'               => __( 'Add New Question Type', $this->plugin_slug ),
			'edit_item'                  => __( 'Edit Question Type', $this->plugin_slug ),
			'update_item'                => __( 'Update Question Type', $this->plugin_slug ),
			'separate_items_with_commas' => __( 'Separate question types with commas', $this->plugin_slug ),
			'search_items'               => __( 'Search question types', $this->plugin_slug ),
			'add_or_remove_items'        => __( 'Add or remove question types', $this->plugin_slug ),
			'choose_from_most_used'      => __( 'Choose from the most used question types', $this->plugin_slug ),
		) );

		$rewrite = array(
			'slug'                       => apply_filters( 'wpsq_taxonomy_slug', _x( 'question_types', 'Taxonomy Slug', $this->plugin_slug ) ),
			'with_front'                 => true,
			'hierarchical'               => false,
		);

		$args = apply_filters( 'wpsq_taxonomy_args', array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'query_var'					 => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => $rewrite,
		) );

		register_taxonomy( 'wpsq-types', 'wpsq-question', $args );

	}


	/**
	 * Add shortcode to render the UI for the course quiz at the front-end.
	 *
	 * @since    1.0.0
	 */
	public function frontend_ui_generator() {

		// load the scripts for the UI to work
		wp_enqueue_script( 'underscores' );
		wp_enqueue_script( 'backbone' );
		wp_enqueue_script( $this->plugin_slug . '-plugin-script' );

		$ts_vars = array( 
			'json' => admin_url( 'admin-ajax.php' ),
			'tplQuestion' => plugins_url( 'views/wpsq-question-tpl.html', __FILE__ ),
			'tplResults' => plugins_url( 'views/wpsq-results-tpl.html', __FILE__ ),
		);

		$ts_labels = apply_filters( 'wpsq-quiz-ui-labels', array( 
			'question' => __( 'Question', $this->plugin_slug ),
			'isnan' => __( 'Enter a valid number of questions!', $this->plugin_slug ),
			'noterms' => __( 'Please, check at least one type of question.', $this->plugin_slug ),
			'results' => __( 'Results', $this->plugin_slug ),
			'questionsTotal' => __( 'Total of Questions', $this->plugin_slug ),
			'questionsAnswered' => __( 'Questions Answered', $this->plugin_slug ),
			'questionsCorrect' => __( 'Correct Answers', $this->plugin_slug ),
			'questionsWrong' => __( 'Wrong Answers', $this->plugin_slug ),
			'questionsEach' => __( 'Show Results of each question', $this->plugin_slug ),
			'questionSelected' => __( 'Answer Selected', $this->plugin_slug ),
			'questionCorrect' => __( 'Correct Answer', $this->plugin_slug ),
			'none' => __( 'None', $this->plugin_slug ),
			'newQuiz' => __( 'New Quiz', $this->plugin_slug ),
		) );

		wp_localize_script( $this->plugin_slug . '-plugin-script', 'wpsq_vars', $ts_vars );
		wp_localize_script( $this->plugin_slug . '-plugin-script', 'wpsq_labels', $ts_labels );

		include_once( 'views/public.php' );

	}

	public function shortcodes(){
		add_shortcode( 'wpsq-ui', array( $this, 'frontend_ui_generator') );
	}


	/**
	 * Get questions based on parametes like type of courses and number of questions
	 *
	 * @since    1.0.0
	 * @return   JSON object
	 */

	function get_questions(){

		// set parameters
		$courses = $_REQUEST['courses'];
		$number = $_REQUEST['number'];

		// get number of posts based on parameters
		$questions = get_posts(	array(
				'post_type' => 'wpsq-question',
				'tax_query' => array(
					array(
						'taxonomy' => 'wpsq-types',
						'field' => 'id',
						'terms' => $courses
					)
				),
				'orderby' => 'rand',
				'post_status' => 'publish',
				'post_per_page' => $number,
				'no_found_rows' => true, // remove pagination stuff
			)
		);		

		// build the response array
		$response = array();

		foreach( $questions as $question ){

			// get post metadata
			$term_list = wp_get_post_terms( $question->ID, 'wpsq-types', array("fields" => "names") );
			$answers = get_post_meta( $question->ID, '_wpsq-answers', true );				

			$response[] = array(
				'courses' => $term_list, 
				'title' => $question->post_title,
				'question' => $question->post_content,
				'answers' => $answers['answers'],
				'answer_correct' => $answers['answer_correct']
			);

		}

		// send back data as JSON response
		wp_send_json( $response );

	}
}
