<?php
/***************************
 *
 */
class PostEnderFrontend extends PostEnder {

	/**
	 * Setup backend functionality in WordPress
	 */
	function PostEnderFrontend () {
		PostEnder::PostEnder ();

		// Add the custom css and Javascript files for the admin page that are 
		// listed in $this->frontend_css and $this->frontend_js vars.
		add_action('wp_enqueue_scripts', array( &$this , 'load_frontend_custom') );

		// Setup the filter to add the footer text to the post content
		add_filter("the_content", array ( &$this , "post_ender_append" ));
		
		// Setup the [post_ender] shortcode
		add_shortcode("post_ender", array ( &$this , "post_ender_shortcode"));
	}

	/**
	 * Add the post_text to the end of each post if it is displaying on a single page
	 * and the add_to_all option is checked
	 */
	function post_ender_append( $content ) {		
		if( is_single() && $this->get_option('add_to_all') && $this->get_option('post_text') ) {

			// Check to see if we need to put the text at the beginning or end of the post
			if( $this->get_option('text_position') == 'start' ) {
				$content = $this->get_option('post_text') . $content;
			}else if( $this->get_option('text_position') == 'end' ) {
				$content = $content . $this->get_option('post_text');				
			}else if( $this->get_option('text_position') == 'both' ) {
				$content = $this->get_option('post_text') . $content . $this->get_option('post_text');				
			}
		}
		return $content;
	}

	/**
	 * Add the post_text into a post if the shortcode [post_ender] is present and
	 * the add_to_all checkbox is not checked
	 */
	function post_ender_shortcode() {
		if( is_single() && !$this->get_option('add_to_all') ) {
			return $this->get_option("post_text");
		}
	}

	/**
	 * Add Javascript and stylesheet files
	 */
	function load_frontend_custom() {
		if( !empty($this->frontend_css) ) {
			$this->load_admin_css();			
		}
		
		if( !empty($this->frontend_js) ) {
			$this->load_admin_scripts();
		}
	}

	/** 
	 * Load CSS files listed in the $this->admin_css var
	 */
	function load_frontend_css() {		
		foreach($this->frontend_css as $css) {			
			if ( file_exists($this->css_dir . $css) ) {
				wp_register_style( $css, $this->css_url . "/" . $css );
		        wp_enqueue_style( $css );
			}
		}
	}
	
	/** 
	 * Load scripts listed in the $this->admin_js var
	 */
	function load_frontend_scripts() {
		foreach($this->frontend_js as $js) {
			if ( file_exists($this->js_dir . $js) ) {
				wp_deregister_script( $js );
				wp_register_script( $js, $this->js_url . "/" . $js );
				wp_enqueue_script( $js );
			}
		}
	}
	
} // End PostEnderFrontend class
