<?php
	/*
	Plugin Name: WP Video Robot - Custom Taxonomies
	Plugin URI: http://www.wpvideorobot.com
	Description: Use Custom Taxonomies with your Imported Videos.
	Version: 1.5
	Author: pressaholic
	Author URI: http://www.pressaholic.com
	License: GPL2
	*/
	add_action( 'plugins_loaded', 'wpvrct_addon_init' );
	function wpvrct_addon_init() {
		define('WPVRCT_MIN_VERSION', '1.8.3' );
		define('WPVRCT_VERSION', '1.5' );
		require_once('define.php');	
		require_once('hooks.php');			
	}