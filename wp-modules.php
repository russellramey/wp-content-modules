<?php
/**
* Plugin Name: WP Content Modules
* Plugin URI: http://russellramey.me/wordpress/wp-content-modules
* Description: Build pages modularly. Create simple, repeateable, blocks of content that can be used anywhere with the generated [module] shortcode.
* Version: 1.0
* Author: Russell Ramey
* Author URI: http://russellramey.me/
*/


/************************************************************************************
*** Module Styles
	Load front end css for displaying the content modules.
************************************************************************************/
// Wordpress action hook
add_action( 'wp_enqueue_scripts', 'load_module_css' );
function load_module_css() {
	// Plugin path
    $plugin_path = plugin_dir_url( __FILE__ );
    // Load styles to wp_head
    wp_enqueue_style( 'modules', $plugin_path . 'css/modules-style.css' );
}
