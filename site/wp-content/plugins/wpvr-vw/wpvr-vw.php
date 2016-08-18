<?php
	/*
	Plugin Name: WP Video Robot - Video Widgets
	Plugin URI: http://www.wpvideorobot.com
	Description: Easily show up your imported videos through widgets.
	Version: 1.5
	Author: pressaholic
	Author URI: http://www.pressaholic.com
	License: GPL2
	*/
	add_action( 'plugins_loaded' , 'wpvr_vw_addon_init' );
	function wpvr_vw_addon_init() {
		define( 'WPVR_VW_MIN_VERSION' , '1.8.3' );
		define( 'WPVR_VW_VERSION' , '1.5' );
		require_once( 'define.php' );
		require_once( 'functions.php' );
		require_once( 'hooks.php' );
		require_once( 'define.widgets.php' );
	}


	


	
