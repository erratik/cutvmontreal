<?php
/*
*	Load Scripts and Styles
*/

// Public JS scripts
if (!function_exists('co_scripts_method')) {
	function co_scripts_method() {
		wp_enqueue_script('jquery');
		//wp_enqueue_script('co-master', plugins_url( 'js', __FILE__ ) . '/master.js', array('jquery'), '', TRUE);
		
	}
}
add_action('wp_enqueue_scripts', 'co_scripts_method');

// Public CSS files
if (!function_exists('co_style_method')) {
	function co_style_method() {
		//wp_enqueue_style('co-master-css', plugins_url( 'css', __FILE__ ) . '/master.css');

	}
}
add_action('wp_enqueue_scripts', 'co_style_method');
add_action('wp_head', 'co_style_method');
?>