<?php
/**
 * Post Ender is a simple plugin that allows you to add a message at the end of each post.
 * This can be done for either all posts or for specific posts using the "[post_ender]" shortcode.
 *
 * Thanks to Thord Daniel Hedengren and his book "Smashing WordPress" for the FooterNotes example code
 *
 * @author Tony Fardella <tfardella@gmail.com>
 * @version 1.1
 * @package post-ender
 */

/*
	Plugin Name:  Post Ender
	Plugin URI:   http://www.wirelesswombat.com/
	Description:  A simple plugin to allow you to add a message footer at the end of every post.
	Version:      1.1
	Author:       Tony Fardella
	Author URI:   http://www.wirelesswombat.com
	Text Domain:  post-ender
	Domain Path:  post-ender/localization

    Copyright 2011  Tony Fardella  (email : tfardella@gmail.com)

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

define( 'POSTENDER_OPTIONS_NAME', 'post_ender' );
define( 'POSTENDER_VERSION', '1.0' );
define( 'POSTENDER_MIN_WP_VERSION', '3.0' );

// Option default values
define( 'POSTENDER_POST_TEXT_DEFAULT', 'Enter text here to go at the beginning and/or end of your posts...' );
define( 'POSTENDER_ADD_TO_ALL_DEFAULT', 0 );
define( 'POSTENDER_TEXT_POSITION', 'end' );

if ( !class_exists("PostEnder") ) {

	class PostEnder {

		var $options;
		var $options_name = "post_ender";

		var $plugin_dir = "";
		var $css_dir = "";
		var $js_dir = "";
		var $images_dir = "";

		var $css_url = "";
		var $js_url = "";
		var $images_url = "";
		
		var $admin_css = array("post-footer-admin.css");
		var $admin_js = array("post-footer.js");
		var $frontend_css = array();
		var $frontend_js = array();
		
		function PostEnder() {

			// Full path and plugin basename of the main plugin file
			$this->plugin_file = dirname ( dirname ( __FILE__ ) ) . '/post-ender.php';
			$this->plugin_basename = plugin_basename ( $this->plugin_file );

			// Plugin directory names
			$this->plugin_path = dirname ( __FILE__ );			
			$this->css_dir = $this->plugin_path . '/css/';
			$this->js_dir = $this->plugin_path . '/scripts/';
			$this->images_dir = $this->plugin_path . '/images/';

			// Plugin URLs
			$this->css_url = plugins_url( 'css' , __FILE__ );
			$this->js_url = plugins_url( 'scripts' , __FILE__ );
			$this->images_url = plugins_url( 'images' , __FILE__ );
			
			// Load localizations if available
			load_plugin_textdomain ( 'postender' , false , 'post-ender/localization' );

			// Make sure our options are setup in the db
			$this->setup_options();
			$this->options = get_option ( POSTENDER_OPTIONS_NAME );
		} 
		/**
		 * init
		 * Actions that need to occur each time the plugin is started should go here
		 */
		function init() {

			// Instantiate the PostEnderFrontend or PostEnderAdmin Class
			// Deactivate and die if files can not be included
			if ( is_admin () ) {
				// Load the admin page code
				if ( @include ( dirname ( __FILE__ ) . '/inc/admin.php' ) ) {
					$PostEnderAdmin = new PostEnderAdmin ();
				} else {
					PostEnder::deactivate_and_die ( dirname ( __FILE__ ) . '/inc/admin.php' );
				}
			} else {
				// Load the frontend code
				if ( @include ( dirname ( __FILE__ ) . '/inc/frontend.php' ) ) {
					$PostEnderFrontend = new PostEnderFrontend ();
				} else {
					PostEnder::deactivate_and_die ( dirname ( __FILE__ ) . '/inc/frontend.php' );
				}
			}
		}
	
		/**
		 * Callback for the register_activation_hook
		 * Actions that need to occur when the plugin is activated should go here
		 */
		function plugin_activation() {
		}

		/***
		 * Callback for register_deactivation_hook
		 * Actions that need to occur when the plugin is deactivated should go here
		 */
		function plugin_deactivation() {
		}
		
		/***
		 * Callback for register_uninstall_hook
		 * Clean up the db when the plugin is uninstalled
		 */
		function plugin_uninstall() {
			delete_option( POSTENDER_OPTIONS_NAME );
		}

		/**
		 * Return the default option values
		 */
		function default_options() {
			$defaults = array (
				'post_text'		=> POSTENDER_POST_TEXT_DEFAULT,
				'add_to_all'	=> POSTENDER_ADD_TO_ALL_DEFAULT,	// 0 = don't add to all posts, 1 = add to all posts
				'text_position' => POSTENDER_TEXT_POSITION // start, end, or both
			);
			return $defaults;
		}
		
		/**
		 * Setup shared functionality for Admin and Front End
		 */

		// If any of the necessary files are not found we come here to deactivate the plugin and show an error message.
		function deactivate_and_die() {
			load_plugin_textdomain ( 'post-ender' , false , 'post-ender/localization' );
			$message = sprintf ( __( "Post Ender has been automatically deactivated because the file <strong>%s</strong> is missing. Please reinstall the plugin and reactivate." ) , $file );
			if ( ! function_exists ( 'deactivate_plugins' ) )
				include ( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins ( __FILE__ );
			wp_die ( $message );
		}


		// Set default options if they don't already exisit
		function setup_options() {
			if ( ! get_option ( POSTENDER_OPTIONS_NAME ) ) {
				$this->options = $this->default_options();
				add_option ( POSTENDER_OPTIONS_NAME , $this->options );
			}
		}

		/**
		 * Get specific option from the options array
		 */
		function get_option( $option ) {
			if ( isset ( $this->options[$option] ) ) {
				return $this->options[$option];
			} else {
				return false;
			}
		}

		/**
		 * Set specific option from the options array
		 */
		function set_option( $option, $value ) {
			$this->options[$option] = $value;
			update_option( POSTENDER_OPTIONS_NAME , $this->options );
		}
		
		/**
		 * Get the full URL to the plugin
		 */
		function plugin_url() {
			$plugin_url = plugins_url ( plugin_basename ( dirname ( __FILE__ ) ) );
			return $plugin_url;
		}
		
	} // End PostEnder class

} // End if PostEnder

/**
 * Setup initial hooks and actions for PostEnder plugin
 * 
 */

register_deactivation_hook( __FILE__, array('PostEnder', 'plugin_deactivate' ) );
register_uninstall_hook( __FILE__ , array( 'PostEnder', 'plugin_uninstall' ) );

add_action( 'init', array( 'PostEnder', 'init' ) );
