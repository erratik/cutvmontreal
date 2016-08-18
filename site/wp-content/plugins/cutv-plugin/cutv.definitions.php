<?php

	/* Debug Assets */
//	require_once( 'assets/php/dBug.php' );
//	require_once( 'assets/php/kint/Kint.class.php' );
//	cutvKint::$theme = 'aante-light';
//	function _e( $var ) { new dBug( $var ); }

	//require_once( 'assets/php/async/async.php' );
	//require_once( 'assets/php/async/background.php' );


	/* Predef Functions */
//	require_once( 'definitions/cutv.predef.php' );

	/* Defining Constants */
	require_once( 'definitions/cutv.constants.php' );

	/* Defining plugin links */
	require_once( 'definitions/cutv.urls.php' );

	/* Including Services definitons */
	add_action( 'plugins_loaded' , 'cutv_load_services_init' , 5 );
	function cutv_load_services_init() {

		/* Definings the plugin global variables */
//		require_once( 'definitions/cutv.globals.php' );
//
//		/* Wrapping up definitions */
//		require_once( 'definitions/cutv.set.before.php' );
//
//
//		/* Including Services definitons */
//		require_once( 'definitions/cutv.services.php' );
//
//		/* Definings the plugin default options values */
//		require_once( 'definitions/cutv.defaults.php' );
//
//		/* Loading dataFillers presets */
//		require_once( 'definitions/cutv.presets.php' );
//
//		/* Wrapping up definitions */
//		require_once( 'definitions/cutv.set.after.php' );

	}




	
	