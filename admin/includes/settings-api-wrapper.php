<?php
/**
 * @package   WP_Squizzes_Admin
 * @author    Diego de Oliveira <diego@favolla.com.br>
 * @link      https://github.com/diegoliv/WP-squizzes
 * @copyright 2013 Diego de Oliveira
 */

/**
 * @package WP_Squizzes_Admin
 * @subpackage WPSQ_Settings_Wrapper
 * @author  Diego de Oliveira <diego@favolla.com.br>
 */

class WPSQ_Settings_Wrapper{

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $settings_tabs = array();

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_page = null;

	/**
	 * Initialize the plugin by registering a settings group
	 *
	 * @since     1.0.0
	 */
	function __construct( $page, $settings ) {

		foreach ( $settings as $setting ) {
		    register_setting( $setting['group'], $setting['group'], $this->sanitize_callback );

		    // build class variable to make tabs 
		    array_push( $this->settings_tabs , array(
		    	'id' => $setting['group'],
		    	'title' => $setting['tab'],
		    ) );
		}

	    $this->plugin_page = $page;

	}

	/**
	 * Register sections
	 *
	 * @since    1.0.0
	 */

	public function add_sections( $sections ){

		foreach( $sections as $section ) {

		    add_settings_section(  
		        $section['id'],   					// ID used to identify this section and with which to register options  
		        $section['title'],					// Title to be displayed on the administration page  
		        null,								// Callback used to render the description of the section  
		        $section['page']			        // Page on which to add this section of options  
		    );
		
		}

	}

	public function add_fields( $section, $page, $fields ){

	    // If options don't exist, create them.  
	    if( false == get_option( 'wpsq-options' ) ) {  
	        add_option( 'wpsq-options' );  
	    } // end if  

		foreach( $fields as $field ){

		    add_settings_field(  
		        $page .'_'. $field['id'],  
		        $field['label'], 
		        array( $this, 'ts_'. $field['type'] .'_callback' ),  
		        $page,  
		        $section,
		        array(
		        	'page' => $page,
		        	'id' => isset( $field['id'] ) ? $field['id'] : null,
		        	'desc' => ! empty( $field['desc'] ) ? $field['desc'] : null,
		        	'size' => ! empty( $field['size'] ) ? $field['size'] : null,
		        	'default' => isset( $field['default'] ) ? $field['default'] : null,
	        		'min' => isset( $field['num_min'] ) ? $field['num_min'] : null,
	        		'max' => isset( $field['num_max'] ) ? $field['num_max'] : null,
	        		'step' => isset( $field['num_step'] ) ? $field['num_step'] : null,
		        )
		    );
		}

	}

	public function make_tabs(){

		if( count( $this->settings_tabs ) > 1 ){

	        $html = '<h2 class="nav-tab-wrapper">';

			if ( isset( $_GET['id'] ) ) { 
				$active_tab = $_GET['id']; 
			} else { 
				$active_tab = $this->settings_tabs[0]['id']; 
			} 

	        foreach( $this->settings_tabs as $section ){

	        	$active = ( $active_tab == $section['id'] ) ? 'nav-tab-active' : '';
	            $html .= '<a href="?page='. $this->plugin_page .'&id='. $section['id'] .'" class="nav-tab '. $active .'">'. $section['title'] .'</a>';	        	
	        }  

	        $html .= '</h2>';
			
			echo $html;
		}

	}

	public function make_pages(){

			if ( isset( $_GET['id'] ) ) { 
				$active_tab = $_GET['id']; 
			} else { 
				$active_tab = $this->settings_tabs[0]['id']; 
			} 

		    settings_fields( $active_tab ); 
            do_settings_sections( $active_tab );

	}

	public function ts_text_callback( $args ){

		$option = get_option( $args['page'] );

		if( isset( $option[ $args['id'] ] ) ){
			$value = $option[ $args['id'] ];
		} else {
			$value = isset( $args['default'] ) ? esc_attr( stripslashes( $args['default'] ) ) : '';
		}

		$class = isset( $args['size'] ) ? $args['size'] : 'regular';

		$html = '<input type="text" class="'. $class .'-text" name="'. $args['page']. '['. $args['id'] .']" id="wpsq-options_'. $args['id'] .'" value="'. $value .'"><br>';
		
		if( isset($args['desc']) ){
			$html .= '<p class="description">'. $args['desc'] .'</p>';
		}

		echo $html;
	}
	public function ts_number_callback( $args ){

		$option = get_option( $args['page'] );

		if( isset( $option[ $args['id'] ] ) ){
			$value = $option[ $args['id'] ];
		} else {
			$value = isset( $args['default'] ) ? esc_attr( stripslashes( $args['default'] ) ) : '';
		}

		$class = isset( $args['size'] ) ? $args['size'] : 'regular';
		$min = isset( $args['min'] ) ? esc_attr( $args['min'] ) : 1;
		$max = isset( $args['max'] ) ? esc_attr( $args['max'] ) : 99999;
		$step = isset( $args['step'] ) ? esc_attr( $args['step'] ) : 1;

		$html = '<input type="number" class="'. $class .'-text" name="'. $args['page']. '['. $args['id'] .']" id="wpsq-options_'. $args['id'] .'" min="'. $min .'" max="'. $max .'" step="'. $step .'" value="'. $value .'"><br>';
		
		if( isset($args['desc']) ){
			$html .= '<p class="description">'. $args['desc'] .'</p>';
		}

		echo $html;
	}

	public function ts_checkbox_callback( $args ){

		$option = get_option( $args['page'] );

		if( isset( $option[ $args['id'] ] ) ){
			$checked = checked(1, $option[ $args['id'] ], false);
		} else {
			$checked = isset( $args['default'] ) ? $args['default'] : '';
		}

		$html = '<input type="checkbox" name="'. $args['page'] .'['. $args['id'] .']" id="wpsq-options_'. $args['id'] .'" value="1" ' . $checked . '>';
		
		if( isset($args['desc']) ){
			$html .= '<label for="wpsq-options_'. $args['id'] .'" class="description">'. $args['desc'] .'</label>';
		}


		echo $html;
	}

	public function sanitize_callback( $input ) {
    // Define the array for the updated options  
    $output = array();

    // Loop through each of the options sanitizing the data  
    foreach( $input as $key => $val ) {  

        if( isset ( $input[$key] ) ) {

            switch ($key) {              
                default:
                    $output[$key] = strip_tags( stripslashes( $input[$key] ) );              
                break;
            }

        }
    } 

    return apply_filters( $this->sanitize_callback, $output, $input );    
}

}