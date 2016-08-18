<?php

	/* Require Ajax WP load */
	if( isset( $_GET[ 'wpvr_wpload' ] ) || isset( $_POST[ 'wpvr_wpload' ] ) ) {
		define( 'DOING_AJAX' , TRUE );
		//define('WP_ADMIN', true );
		$wpload = 'wp-load.php';
		while( ! is_file( $wpload ) ) {
			if( is_dir( '..' ) ) chdir( '..' );
			else die( 'EN: Could not find WordPress! FR : Impossible de trouver WordPress !' );
		}
		require_once( $wpload );
	}
	
	if( isset( $_POST[ 'render_form' ] ) ) {

		if( ! isset( $_POST[ 'encoded_data' ] ) ) {
			echo "NO DATA.";

			return FALSE;
		}
		
		echo wpvr_vw_render_form( $_POST[ 'encoded_data' ] );

		return FALSE;
	}

	if( isset( $_POST[ 'save_form' ] ) ) {
		$args = wpvr_vw_get_args_from_string( $_POST[ 'encoded_data' ] );
		echo base64_encode( json_encode( $args ) );

		return FALSE;
	}
	
	
	
	