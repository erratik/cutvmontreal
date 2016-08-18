<?php

	/* Core Constants */
	if( ! defined( 'WPVR_IS_ON' ) ) define( 'WPVR_IS_ON' , TRUE );
	if( ! defined( 'WPVR_LANG' ) ) define( 'WPVR_LANG' , 'wpvr' );
	if( ! defined( 'WPVR_SLUG' ) ) define( 'WPVR_SLUG' , plugin_basename( WPVR_MAIN_FILE ) );
	if( ! defined( 'WPVR_TOKEN_REQUIRED' ) ) define( 'WPVR_TOKEN_REQUIRED' , TRUE );
	if( ! defined( 'WPVR_ALLOW_UNAUTH_API_ACCESS' ) ) define( 'WPVR_ALLOW_UNAUTH_API_ACCESS' , FALSE );
	if( ! defined( 'WPVR_IS_DEMO_SITE' ) ) define( 'WPVR_IS_DEMO_SITE' , TRUE );
	if( ! defined( 'WPVR_IS_DEMO_USER' ) ) define( 'WPVR_IS_DEMO_USER' , 999 );
	if( ! defined( 'WPVR_BULK_IMPORT_BUFFER' ) ) define( 'WPVR_BULK_IMPORT_BUFFER' , 10 );
	if( ! defined( 'WPVR_VIDEO_TYPE' ) ) define( 'WPVR_VIDEO_TYPE' , 'wpvr_video' );
	if( ! defined( 'WPVR_SOURCE_TYPE' ) ) define( 'WPVR_SOURCE_TYPE' , 'wpvr_source' );
	if( ! defined( 'WPVR_SFOLDER_TYPE' ) ) define( 'WPVR_SFOLDER_TYPE' , 'wpvr_source_folder' );
	if( ! defined( 'WPVR_USER_CAPABILITY' ) ) define( 'WPVR_USER_CAPABILITY' , '*' );
	if( ! defined( 'WPVR_ALLOW_DEFAULT_API_CREDENTIALS' ) ) define( 'WPVR_ALLOW_DEFAULT_API_CREDENTIALS' , TRUE );
	if( ! defined( 'WPVR_SECURITY_WANTED_VIDEOS' ) ) define( 'WPVR_SECURITY_WANTED_VIDEOS' , 20 );
	if( ! defined( 'WPVR_SECURITY_WANTED_VIDEOS_HOUR' ) ) define( 'WPVR_SECURITY_WANTED_VIDEOS_HOUR' , 40 );
	if( ! defined( 'WPVR_TAGS_FROM_TITLE_ENABLE' ) ) define( 'WPVR_TAGS_FROM_TITLE_ENABLE' , TRUE );
	if( ! defined( 'WPVR_HIERARCHICAL_CATS_ENABLED' ) ) define( 'WPVR_HIERARCHICAL_CATS_ENABLED' , TRUE );
	if( ! defined( 'WPVR_IS_LOCKED_OUT' ) ) define( 'WPVR_IS_LOCKED_OUT' , FALSE );
	if( ! defined( 'WPVR_ASK_TO_RATE_TRIGGER' ) ) define( 'WPVR_ASK_TO_RATE_TRIGGER' , 10 );
	if( ! defined( 'WPVR_JS' ) ) define( 'WPVR_JS' , '##_@wpvr@_##' );
	if( ! defined( 'WPVR_PARENT_META' ) ) define( 'WPVR_PARENT_META' , '_wpvr_parent' );
	if( ! defined( 'WPVR_DEV_MODE' ) ) define( 'WPVR_DEV_MODE' , FALSE );
	if( ! defined( 'WPVR_ENABLE_ASYNC_RUN' ) ) define( 'WPVR_ENABLE_ASYNC_RUN' , TRUE );
	if( ! defined( 'WPVR_ENABLE_ASYNC_FETCH' ) ) define( 'WPVR_ENABLE_ASYNC_FETCH' , TRUE );
	if( ! defined( 'WPVR_ENABLE_ASYNC_DEBUG' ) ) define( 'WPVR_ENABLE_ASYNC_DEBUG' , FALSE );
	if( ! defined( 'WPVR_ASYNC_ADDING_BUFFER' ) ) define( 'WPVR_ASYNC_ADDING_BUFFER' , 5 );
	if( ! defined( 'WPVR_USE_LOCAL_FONTAWESOME' ) ) define( 'WPVR_USE_LOCAL_FONTAWESOME' , TRUE );
	if( ! defined( 'WPVR_HELPER_RESULTS_COUNT' ) ) define( 'WPVR_HELPER_RESULTS_COUNT' , 25 );
	if( ! defined( 'WPVR_ACTIONS_URL_ASYNC_FIX' ) ) define( 'WPVR_ACTIONS_URL_ASYNC_FIX' , FALSE );

	
	/* CONFIG CONSTANTS */
	
	/* NON ADMIN CAPS */
	if( ! defined( 'WPVR_NONADMIN_CAP_OPTIONS' ) ) define( 'WPVR_NONADMIN_CAP_OPTIONS' , TRUE );
	if( ! defined( 'WPVR_NONADMIN_CAP_IMPORT' ) ) define( 'WPVR_NONADMIN_CAP_IMPORT' , TRUE );
	if( ! defined( 'WPVR_NONADMIN_CAP_LOGS' ) ) define( 'WPVR_NONADMIN_CAP_LOGS' , TRUE );
	if( ! defined( 'WPVR_NONADMIN_CAP_ACTIONS' ) ) define( 'WPVR_NONADMIN_CAP_ACTIONS' , TRUE );
	if( ! defined( 'WPVR_NONADMIN_CAP_DEFERRED' ) ) define( 'WPVR_NONADMIN_CAP_DEFERRED' , TRUE );

	if( ! defined( 'WPVR_APPEND_SEPARATOR' ) ) define( 'WPVR_APPEND_SEPARATOR' , ' - ' );
	if( ! defined( 'WPVR_CRON_ENDPOINT' ) ) define( 'WPVR_CRON_ENDPOINT' , 'wpvr-cron' );
	if( ! defined( 'WPVR_DISABLE_THUMBS_DOWNLOAD' ) ) define( 'WPVR_DISABLE_THUMBS_DOWNLOAD' , FALSE );

	/* FETAURES ENABLING */
	if( ! defined( 'WPVR_ENABLE_POST_FORMATS' ) ) define( 'WPVR_ENABLE_POST_FORMATS' , FALSE );
	if( ! defined( 'WPVR_ENABLE_SETTERS' ) ) define( 'WPVR_ENABLE_SETTERS' , FALSE );
	if( ! defined( 'WPVR_ENABLE_HARD_REFRESH' ) ) define( 'WPVR_ENABLE_HARD_REFRESH' , FALSE );
	if( ! defined( 'WPVR_ENABLE_ADDONS' ) ) define( 'WPVR_ENABLE_ADDONS' , FALSE );
	if( ! defined( 'WPVR_BATCH_ADDING_ENABLED' ) ) define( 'WPVR_BATCH_ADDING_ENABLED' , FALSE );
	if( ! defined( 'WPVR_EG_FIX' ) ) define( 'WPVR_EG_FIX' , FALSE );
	if( ! defined( 'WPVR_FULL_DESC' ) ) define( 'WPVR_FULL_DESC' , FALSE );
	if( ! defined( 'WPVR_ENABLE_DATA_FILLERS' ) ) define( 'WPVR_ENABLE_DATA_FILLERS' , FALSE );
	if( ! defined( 'WPVR_CHECK_PLUGIN_UPDATES' ) ) define( 'WPVR_CHECK_PLUGIN_UPDATES' , TRUE );
	if( ! defined( 'WPVR_CHECK_ADDONS_UPDATES' ) ) define( 'WPVR_CHECK_ADDONS_UPDATES' , TRUE );
	if( ! defined( 'WPVR_META_DEBUG_MODE' ) ) define( 'WPVR_META_DEBUG_MODE' , FALSE );
	if( ! defined( 'WPVR_API_RESPONSE_DEBUG' ) ) define( 'WPVR_API_RESPONSE_DEBUG' , FALSE );
	if( ! defined( 'WPVR_SMOOTH_SCREEN_ENABLED' ) ) define( 'WPVR_SMOOTH_SCREEN_ENABLED' , TRUE );
	if( ! defined( 'WPVR_ENABLE_ADMINBAR_MENU' ) ) define( 'WPVR_ENABLE_ADMINBAR_MENU' , TRUE );

	/* LIMITATIONS */
	
	
	if( ! defined( 'WPVR_MAX_POSTING_CATS' ) ) define( 'WPVR_MAX_POSTING_CATS' , 10 );
	if( ! defined( 'WPVR_MANAGE_PERPAGE' ) ) define( 'WPVR_MANAGE_PERPAGE' , 100 );
	if( ! defined( 'WPVR_DEFERRED_PERPAGE' ) ) define( 'WPVR_DEFERRED_PERPAGE' , 100 );
	if( ! defined( 'WPVR_UNWANTED_PERPAGE' ) ) define( 'WPVR_UNWANTED_PERPAGE' , 100 );
	if( ! defined( 'WPVR_MANAGE_LAYOUT' ) ) define( 'WPVR_MANAGE_LAYOUT' , 'bgrid' );
	if( ! defined( 'WPVR_UNCATEGORIZED' ) ) define( 'WPVR_UNCATEGORIZED' , 'uncategorized' );


	/* Defining DEFAULT VIMEO CREDENTIALS */
	define( 'WPVR_VIMEO_CLIENT_ID' , '36db8aea80f16298d7dfd938ad605af336b7f3d9' );
	define( 'WPVR_VIMEO_CLIENT_SECRET' , '5e0b277b5a5d0b7a9ffb24b705b8145245929c96' );
	define( 'WPVR_VIMEO_ACCESS_TOKEN' , 'ff55ac086a0aac77c439d508555e9df6' );

	/* Defining DEFAULT DAILYMOTION CREDENTIALS */
	define( 'WPVR_DAILYMOTION_CLIENT_ID' , '8e31e0a2b41c2c11049f' );
	define( 'WPVR_DAILYMOTION_CLIENT_SECRET' , 'b6af36711bb9c09cff0ed4226389093409c85df8' );

	/* Define DEFAULT YOUTUBE CREDENTIALS */
	define( 'WPVR_DEFAULT_YOUTUBE_API_KEY' , 'AIzaSyBbYhvq425Pxsqm-OjatqzLGMcqu9y2lJk' );


	define( 'WPVR_REQUIRED_PHP_VERSION' , '5.5.0' );
	define( 'WPVR_REQUIRED_PHP_MEMORY_LIMIT' , '128M' );
	define( 'WPVR_REQUIRED_PHP_POST_MAX_SIZE' , '8M' );
	define( 'WPVR_REQUIRED_PHP_MAX_INPUT_TIME' , '60' );
	define( 'WPVR_REQUIRED_PHP_MAX_EXECUTION_TIME' , '600' );


	/* CONFIG CONSTANTS */