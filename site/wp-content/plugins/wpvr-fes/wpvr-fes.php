<?php
	/*
	Plugin Name: WP Video Robot - Frontend Submissions
	Plugin URI: http://www.wpvideorobot.com
	Description: Allow users to post their own videos from your site frontend. 
	Version: 1.4
	Author: pressaholic
	Author URI: http://www.pressaholic.com
	License: GPL2
	*/
	add_action( 'plugins_loaded', 'wpvrfes_addon_init' );
	function wpvrfes_addon_init() {
		define('WPVRFES_MIN_VERSION', '1.8.2' );
		define('WPVRFES_VERSION', '1.4' );
		require_once('define.php');	
		require_once('config.php');	
		require_once('inc/functions.php');	
		require_once('inc/hooks.php');	
		
	}
	