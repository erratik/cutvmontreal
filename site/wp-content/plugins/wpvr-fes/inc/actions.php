<?php
	
	
	if( isset( $_GET[ 'fes_wpload' ] ) || isset( $_POST[ 'fes_wpload' ] ) ) {
		define( 'DOING_AJAX' , TRUE );
		//define('WP_ADMIN', true );
		$wpload = 'wp-load.php';
		while( ! is_file( $wpload ) ) {
			if( is_dir( '..' ) ) chdir( '..' );
			else die( 'EN: Could not find WordPress! FR : Impossible de trouver WordPress !' );
		}
		require_once( $wpload );
	}
	
	
	if( isset( $_GET[ 'submit_video' ] ) ) {
		
		//new dBug( $_POST);
		//new dBug( rpHash($_POST['captcha_value']) );
		//new dBug( $_POST['captcha_hash']) ;
		
		
		if( ! isset( $_SESSION[ 'ajax_token' ] ) || ( ! isset( $_GET[ 'ajax_token' ] ) ) ) {
			echo wpvr_get_json_response( null , 0 , 'Forbidden Ajax Access (ajax_token_undef).' );
			exit;
		} elseif( $_GET[ 'ajax_token' ] != $_SESSION[ 'ajax_token' ] ) {
			echo wpvr_get_json_response( null , 0 , 'Forbidden Ajax Access (ajax_token_wrong).' );
			exit;
		}
		
		if( $_SESSION[ 'ajax_token' ] == $_POST[ 'captcha_value' ] && $_SESSION[ 'ajax_token' ] == $_POST[ 'captcha_hash' ] ) {
			$valid_captcha   = TRUE;
			$captcha_message = "Captcha disabled.";
		} else {
			//echo "CAPTChA ENABLED";
			if( fes_rpHash( $_POST[ 'captcha_value' ] ) != $_POST[ 'captcha_hash' ] ) {
				$valid_captcha   = FALSE;
				$captcha_message = "Entered Captcha is not valid.";
			} else {
				$valid_captcha   = TRUE;
				$captcha_message = "Entered Captcha is valid.";
			}
		}
		
		//new dBug( $valid_captcha );
		//new dBug( $captcha_message );
		
		if( $valid_captcha === FALSE ) {
			//echo $captcha_message ;
			echo wpvr_get_json_response( null , 0 , $captcha_message );
			return FALSE;
		}
		if( ! isset( $_POST[ 'category' ] ) ) $_POST[ 'category' ] = '';
		$submitter = array(
			'userid'   => $_POST[ 'userid' ] ,
			'name'     => $_POST[ 'name' ] ,
			'email'    => $_POST[ 'email' ] ,
			'category' => $_POST[ 'category' ] ,
			//'month' => date('m/Y'),
		);


		$response = wpvrfes_submit_video(
			$_POST[ 'videoid' ] ,
			$_POST[ 'video_service' ] ,
			$submitter
		);
		echo wpvr_get_json_response( $response[ 'video' ] , $response[ 'status' ] , $response[ 'msg' ] );
		return TRUE;
		//echo wpvr_get_json_response( $response );
		
		
		//unset( $_SESSION['ajax_token'] );
		exit;
	}
?>