<?php
class PostEnderAdmin extends PostEnder {

	/**
	 * Setup admin backend functionality
	 */
	function PostEnderAdmin () {
		PostEnder::PostEnder ();

		// Add the custom css and Javascript files for the admin page that are 
		// listed in $this->admin_css and $this->admin_js vars.
		add_action('admin_enqueue_scripts', array( &$this , 'load_admin_custom') );

		// Whitelist our options
		add_action ( 'admin_init' , array ( &$this , 'register_settings' ) );

		// Activate the options page
		add_action ( 'admin_menu' , array ( &$this , 'add_options_page' ) ) ;
	}
	
	/**
	 * Callback function for add_action -> admin_menu hook
	 * Added the options menu item to the Settings menu that brings up the new options page
	 */
	function add_options_page() {
		add_options_page('Post Ender Settings', 'Post Ender', 'administrator', __FILE__, array ( &$this , 'options_page' ));
	}
	
	/**
	 * Callback funtions for add_action -> admin_init
	 * Registers our option settings so they'll be available for use. Required to use the Settings API.
	 */
	function register_settings () {
		register_setting ( 'post_ender_options' , $this->options_name , array ( &$this , 'validate_options' ) );
	}

	/**
	 * Output the options page
	 * Displays the options page or an error message if that file is not found.
	 */
	function options_page () {
		if ( ! @include ( 'options_page.php' ) ) {
			_e ( sprintf ( '<div id="message" class="updated fade"><p>The options page for the <strong>Post Ender</strong> cannot be displayed.  The file <strong>%s</strong> is missing.  Please reinstall the plugin.</p></div>' , dirname ( __FILE__ ) . '/options-page.php' ) );
		}
	}

	/**
	 * Callback function for register_setting
	 * Validation routine for our options to sanitize the values input on the page
	 */
	function validate_options($options) {
		// see http://codex.wordpress.org/Data_Validation for more info
		$options['post_text'] = $options['post_text'] ;
		$options['add_to_all'] = wp_filter_nohtml_kses( $options['add_to_all'] );
		$options['text_position'] = wp_filter_nohtml_kses( $options['text_position'] );
		return $options;
	}

	/**
	 * Add Javascript and stylesheet files
	 */
	function load_admin_custom() {
		if( !empty($this->admin_css) ) {
			$this->load_admin_css();			
		}
		
		if( !empty($this->admin_js) ) {
			$this->load_admin_scripts();
		}
	}

	/**
	 * Load CSS files listed in the $this->admin_css var
	 */
	function load_admin_css() {		
		foreach($this->admin_css as $css) {			
			if ( file_exists($this->css_dir . $css) ) {
				wp_register_style( $css, $this->css_url . "/" . $css );
		        wp_enqueue_style( $css );
			}
		}
	}
	
	/**
	 * Load scripts listed in the $this->admin_js var
	 */
	function load_admin_scripts() {
		foreach($this->admin_js as $js) {
			if ( file_exists($this->js_dir . $js) ) {
				wp_deregister_script( $js );
				wp_register_script( $js, $this->js_url . "/" . $js );
				wp_enqueue_script( $js );
			}
		}
	}

} // end of PostEnderAdmin class
