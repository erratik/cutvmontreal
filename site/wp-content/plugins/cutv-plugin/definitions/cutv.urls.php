<?php
	
	
	/* API URLs */

	//if( ! defined( 'CUTV_ACTIVATE_URL' ) ) define( 'CUTV_ACTIVATE_URL' , CUTV_API_URL . 'req/index.php' );
	//if( ! defined( 'CUTV_ADDONS_URL' ) ) define( 'CUTV_ADDONS_URL' , CUTV_API_URL.'q/products/' );

	//CAPI Connections
	if( ! defined( 'CUTV_API_URL' ) ) define( 'CUTV_API_URL' , "http://capi.pressaholic.com/" );
	if( ! defined( 'CUTV_API_REQ_URL' ) ) define( 'CUTV_API_REQ_URL' , CUTV_API_URL . 'q/' );
	if( ! defined( 'CUTV_API_REQ_KEY' ) ) define( 'CUTV_API_REQ_KEY' , 'lilI1Hoka2e60D8BmL97413AhnBjlVw4' );

	//AUTH Connections
	if( ! defined( 'CUTV_AUTH_URL' ) ) define( 'CUTV_AUTH_URL' , 'http://auth.pressaholic.com/latest/' );
	if( ! defined( 'CUTV_AUTH_KEY' ) ) define( 'CUTV_AUTH_KEY' , '1glV7XMCa4Cz8XKgYE5q' );
	if( ! defined( 'CUTV_AUTH_CUSTOM_LIST' ) ) define( 'CUTV_AUTH_CUSTOM_LIST' , 'CUTV users' );


	/* Internal URLs */
	if( ! defined( 'CUTV_URL' ) ) define( 'CUTV_URL' , plugin_dir_url( CUTV_MAIN_FILE ) );
	if( ! defined( 'CUTV_CRON_URL' ) ) define( 'CUTV_CRON_URL' , CUTV_URL . "cutv.cron.php" );
	if( ! defined( 'CUTV_ACTIONS_URL' ) ) define( 'CUTV_ACTIONS_URL' , CUTV_URL . "includes/cutv.actions.php" );

	if( ! defined( 'CUTV_OPTIONS_URL' ) ) define( 'CUTV_OPTIONS_URL' , CUTV_URL . "includes/cutv.options.php" );
	if( ! defined( 'CUTV_SETTERS_URL' ) ) define( 'CUTV_SETTERS_URL' , CUTV_URL . "includes/cutv.setters.php" );
	if( ! defined( 'CUTV_MANAGE_URL' ) ) define( 'CUTV_MANAGE_URL' , CUTV_URL . "includes/cutv.manage.php" );
	if( ! defined( 'CUTV_IMPORT_URL' ) ) define( 'CUTV_IMPORT_URL' , CUTV_URL . "includes/cutv.import.php" );
	if( ! defined( 'CUTV_FILLERS_URL' ) ) define( 'CUTV_FILLERS_URL' , CUTV_URL . "includes/cutv.datafillers.php" );
	if( ! defined( 'CUTV_SITE_URL' ) ) define( 'CUTV_SITE_URL' , get_bloginfo( 'url' ) );
	if( ! defined( 'CUTV_DASHBOARD_URL' ) ) define( 'CUTV_DASHBOARD_URL' , admin_url( 'admin.php?page=cutv' ) );
	if( ! defined( 'CUTV_ACTIONS_URL_ASYNC' ) ) define( 'CUTV_ACTIONS_URL_ASYNC' , CUTV_SITE_URL . "/wp-content/plugins/cutv/includes/cutv.actions.php" );
	
	/* External URLs */
	if( ! defined( 'CUTV_MAIN_URL' ) ) define( 'CUTV_MAIN_URL' , "http://wpvideorobot.com" );
	if( ! defined( 'CUTV_DOC_URL' ) ) define( 'CUTV_DOC_URL' , "http://doc.wpvideorobot.com" );
	if( ! defined( 'CUTV_SUPPORT_URL' ) ) define( 'CUTV_SUPPORT_URL' , "http://support.wpvideorobot.com" );
	if( ! defined( 'CUTV_DEMOS_URL' ) ) define( 'CUTV_DEMOS_URL' , "http://wpvideorobot.com/demos/" );
	if( ! defined( 'CUTV_STORE_URL' ) ) define( 'CUTV_STORE_URL' , "http://store.wpvideorobot.com" );
	if( ! defined( 'CUTV_STORE_URL_SSL' ) ) define( 'CUTV_STORE_URL_SSL' , "https://store.wpvideorobot.com" );
	if( ! defined( 'CUTV_CC_PAGE_URL' ) ) define( 'CUTV_CC_PAGE_URL' , "http://codecanyon.net/item/wordpress-video-robot-plugin/8619739?ref=pressaholic" );
	if( ! defined( 'CUTV_FONTAWESOME_CSS_URL' ) ) define( 'CUTV_FONTAWESOME_CSS_URL' , "https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" );

	/* Internal PATHs */
	if( ! defined( 'CUTV_PATH' ) ) define( 'CUTV_PATH' , plugin_dir_path( CUTV_MAIN_FILE ) );
	$x = explode( 'cutv-plugin/' , CUTV_PATH );
	if( ! defined( 'CUTV_TMP_PATH' ) ) define( 'CUTV_TMP_PATH' , CUTV_PATH . 'tmp/' );
	if( ! defined( 'CUTV_LANG_FOLDER_PATH' ) ) define( 'CUTV_LANG_FOLDER_PATH' , CUTV_PATH . 'languages/' );
	if( ! defined( 'CUTV_CRON_PATH' ) ) define( 'CUTV_CRON_PATH' , CUTV_PATH . "cutv.cron.php" );
	if( ! defined( 'CUTV_CRON_FILE_PATH' ) ) define( 'CUTV_CRON_FILE_PATH' , CUTV_PATH . "/assets/php/cron.txt" );
	if( ! defined( 'CUTV_PLUGINS_PATH' ) ) define( 'CUTV_PLUGINS_PATH' , $x[ 0 ] );
	if( ! defined( 'CUTV_ERROR_FILE' ) ) define( 'CUTV_ERROR_FILE' , CUTV_PATH . 'error.log' );
	if( ! defined( 'CUTV_DASH_PATH' ) ) define( 'CUTV_DASH_PATH' , CUTV_PATH . 'includes/cutv.dashboard.php' );
	
	
	/* IMAGES */
	if( ! defined( 'CUTV_NO_THUMB' ) ) define( 'CUTV_NO_THUMB' , CUTV_URL . "assets/images/no_thumb.png" );
	if( ! defined( 'CUTV_LOGO_SMALL' ) ) define( 'CUTV_LOGO_SMALL' , CUTV_URL . "assets/images/logo.padded.small.png" );
	
	/* CHANGELOG */
	if( ! defined( 'CUTV_CHANGELOG_URL_ENABLED' ) ) define( 'CUTV_CHANGELOG_URL_ENABLED' , TRUE );
	if( ! defined( 'CUTV_CHANGELOG_URL' ) ) define( 'CUTV_CHANGELOG_URL' , 'http://support.wpvideorobot.com/tutorials/whats-new-on-cutv-184/' );
	
	
	
	
	
	