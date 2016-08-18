<?php
	
	add_action('wp_head', 'wpvr_load_scripts');
	
	
	add_action('wp_footer', 'wpvrfes_load_front_scripts');
	function wpvrfes_load_front_scripts() {
		wp_register_script('wpvrfes_scripts_front', WPVRFES_URL.'assets/js/fes.front_scripts.js' , array('jquery', 'wpvr_scripts') );
		wp_enqueue_script('wpvrfes_scripts_front');	

		wp_register_script('wpvrfes_captcha_plugin', WPVRFES_URL.'assets/js/jquery.plugin.min.js' );
		wp_enqueue_script('wpvrfes_captcha_plugin');	
		
		
		wp_register_script('wpvrfes_captcha_realperson', WPVRFES_URL.'assets/js/jquery.realperson.min.js' );
		wp_enqueue_script('wpvrfes_captcha_realperson');	
		
	}
	
	add_action('wp_enqueue_scripts', 'wpvrfes_load_front_styles');
	function wpvrfes_load_front_styles() {
		//echo "############";
		wp_register_style('wpvrfes_styles_front', WPVRFES_URL.'assets/css/fes.front_styles.css' );
		wp_enqueue_style('wpvrfes_styles_front');

		wp_register_style('wpvrfes_styles_realperson', WPVRFES_URL.'assets/css/jquery.realperson.css' );
		wp_enqueue_style('wpvrfes_styles_realperson');		
	}
	
	
	//add_action('admin_head', 'wpvrfes_load_back_scripts');
	function wpvrfes_load_back_scripts() {
		wp_register_script('wpvrfes_scripts_back', WPVRFES_URL.'assets/js/back_scripts.js' );
		wp_enqueue_script('wpvrfes_scripts_back');		
	}

	
	add_shortcode( 'wpvr_fes_form', 'wpvrfes_form_shortcode_function' );
	function wpvrfes_form_shortcode_function( $atts, $content = "" ) {
		return wpvrfes_render_form();
	}
	
	add_shortcode( 'wpvr_fes_login_form', 'wpvrfes_login_form_shortcode' );
	function wpvrfes_login_form_shortcode() {
		if ( is_user_logged_in() ) return '';
		return wp_login_form( array( 'echo' => false ) );
	}
	
	/*
	add_action('init', 'wpvrfes_user_rewrite_rules');
	function wpvrfes_user_rewrite_rules() {
		add_rewrite_rule( 'fes/?([^/]*)$', 'index.php?fes-action=$matches[1]', 'top' );
		//add_rewrite_rule( 'bartenders/?([^/]*)$', 'index.php?btloop=1&btpaged=$matches[1]', 'top' );
	}
	
	add_filter( 'query_vars', 'wpvrfes_user_rewrite_rules_queryvar' );
	function wpvrfes_user_rewrite_rules_queryvar( $vars ) {
		$vars[] = 'fes-action';
		return $vars;
	}
	
	add_filter('template_include', 'wpvrfes_user_rewrite_rules_templates', 1, 1); 
	function wpvrfes_user_rewrite_rules_templates( $template ){
		global $wp_query , $btrule , $valid_btpages;
		if( isset( $wp_query->query_vars['fes-action'] )) $fes_action = $wp_query->query_vars['fes-action'] ;
		else $fes_action = false ;
		
		if ( $fes_action && $fes_action != "") {
			global $fes_action ;
			return WPVRFES_ACTIONS_URL ; 
		}else return $template; //Load normal template when $page_value != "true" as a fallback
	}
	*/
	
	