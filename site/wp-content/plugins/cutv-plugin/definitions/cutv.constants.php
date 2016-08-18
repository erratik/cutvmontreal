<?php

	/* Core Constants */
	if( ! defined( 'CUTV_IS_ON' ) ) define( 'CUTV_IS_ON' , TRUE );
	if( ! defined( 'CUTV_LANG' ) ) define( 'CUTV_LANG' , 'cutv' );
	if( ! defined( 'CUTV_SLUG' ) ) define( 'CUTV_SLUG' , plugin_basename( CUTV_MAIN_FILE ) );
	if( ! defined( 'CUTV_TOKEN_REQUIRED' ) ) define( 'CUTV_TOKEN_REQUIRED' , TRUE );
	if( ! defined( 'CUTV_ALLOW_UNAUTH_API_ACCESS' ) ) define( 'CUTV_ALLOW_UNAUTH_API_ACCESS' , FALSE );
	if( ! defined( 'CUTV_IS_DEMO_SITE' ) ) define( 'CUTV_IS_DEMO_SITE' , TRUE );
	if( ! defined( 'CUTV_IS_DEMO_USER' ) ) define( 'CUTV_IS_DEMO_USER' , 999 );
	if( ! defined( 'CUTV_BULK_IMPORT_BUFFER' ) ) define( 'CUTV_BULK_IMPORT_BUFFER' , 10 );
	if( ! defined( 'CUTV_VIDEO_TYPE' ) ) define( 'CUTV_VIDEO_TYPE' , 'cutv_video' );
	if( ! defined( 'CUTV_SOURCE_TYPE' ) ) define( 'CUTV_SOURCE_TYPE' , 'cutv_source' );
	if( ! defined( 'CUTV_SFOLDER_TYPE' ) ) define( 'CUTV_SFOLDER_TYPE' , 'cutv_source_folder' );
	if( ! defined( 'CUTV_USER_CAPABILITY' ) ) define( 'CUTV_USER_CAPABILITY' , '*' );
	if( ! defined( 'CUTV_ALLOW_DEFAULT_API_CREDENTIALS' ) ) define( 'CUTV_ALLOW_DEFAULT_API_CREDENTIALS' , TRUE );
	if( ! defined( 'CUTV_SECURITY_WANTED_VIDEOS' ) ) define( 'CUTV_SECURITY_WANTED_VIDEOS' , 20 );
	if( ! defined( 'CUTV_SECURITY_WANTED_VIDEOS_HOUR' ) ) define( 'CUTV_SECURITY_WANTED_VIDEOS_HOUR' , 40 );
	if( ! defined( 'CUTV_TAGS_FROM_TITLE_ENABLE' ) ) define( 'CUTV_TAGS_FROM_TITLE_ENABLE' , TRUE );
	if( ! defined( 'CUTV_HIERARCHICAL_CATS_ENABLED' ) ) define( 'CUTV_HIERARCHICAL_CATS_ENABLED' , TRUE );
	if( ! defined( 'CUTV_IS_LOCKED_OUT' ) ) define( 'CUTV_IS_LOCKED_OUT' , FALSE );
	if( ! defined( 'CUTV_ASK_TO_RATE_TRIGGER' ) ) define( 'CUTV_ASK_TO_RATE_TRIGGER' , 10 );
	if( ! defined( 'CUTV_JS' ) ) define( 'CUTV_JS' , '##_@cutv@_##' );
	if( ! defined( 'CUTV_PARENT_META' ) ) define( 'CUTV_PARENT_META' , '_cutv_parent' );
	if( ! defined( 'CUTV_DEV_MODE' ) ) define( 'CUTV_DEV_MODE' , FALSE );
	if( ! defined( 'CUTV_ENABLE_ASYNC_RUN' ) ) define( 'CUTV_ENABLE_ASYNC_RUN' , TRUE );
	if( ! defined( 'CUTV_ENABLE_ASYNC_FETCH' ) ) define( 'CUTV_ENABLE_ASYNC_FETCH' , TRUE );
	if( ! defined( 'CUTV_ENABLE_ASYNC_DEBUG' ) ) define( 'CUTV_ENABLE_ASYNC_DEBUG' , FALSE );
	if( ! defined( 'CUTV_ASYNC_ADDING_BUFFER' ) ) define( 'CUTV_ASYNC_ADDING_BUFFER' , 5 );
	if( ! defined( 'CUTV_USE_LOCAL_FONTAWESOME' ) ) define( 'CUTV_USE_LOCAL_FONTAWESOME' , TRUE );
	if( ! defined( 'CUTV_HELPER_RESULTS_COUNT' ) ) define( 'CUTV_HELPER_RESULTS_COUNT' , 25 );
	if( ! defined( 'CUTV_ACTIONS_URL_ASYNC_FIX' ) ) define( 'CUTV_ACTIONS_URL_ASYNC_FIX' , FALSE );

	
	/* CONFIG CONSTANTS */
	
	/* NON ADMIN CAPS */
	if( ! defined( 'CUTV_NONADMIN_CAP_OPTIONS' ) ) define( 'CUTV_NONADMIN_CAP_OPTIONS' , TRUE );
	if( ! defined( 'CUTV_NONADMIN_CAP_IMPORT' ) ) define( 'CUTV_NONADMIN_CAP_IMPORT' , TRUE );
	if( ! defined( 'CUTV_NONADMIN_CAP_LOGS' ) ) define( 'CUTV_NONADMIN_CAP_LOGS' , TRUE );
	if( ! defined( 'CUTV_NONADMIN_CAP_ACTIONS' ) ) define( 'CUTV_NONADMIN_CAP_ACTIONS' , TRUE );
	if( ! defined( 'CUTV_NONADMIN_CAP_DEFERRED' ) ) define( 'CUTV_NONADMIN_CAP_DEFERRED' , TRUE );

	if( ! defined( 'CUTV_APPEND_SEPARATOR' ) ) define( 'CUTV_APPEND_SEPARATOR' , ' - ' );
	if( ! defined( 'CUTV_CRON_ENDPOINT' ) ) define( 'CUTV_CRON_ENDPOINT' , 'cutv-cron' );
	if( ! defined( 'CUTV_DISABLE_THUMBS_DOWNLOAD' ) ) define( 'CUTV_DISABLE_THUMBS_DOWNLOAD' , FALSE );

	/* FETAURES ENABLING */
	if( ! defined( 'CUTV_ENABLE_POST_FORMATS' ) ) define( 'CUTV_ENABLE_POST_FORMATS' , FALSE );
	if( ! defined( 'CUTV_ENABLE_SETTERS' ) ) define( 'CUTV_ENABLE_SETTERS' , FALSE );
	if( ! defined( 'CUTV_ENABLE_HARD_REFRESH' ) ) define( 'CUTV_ENABLE_HARD_REFRESH' , FALSE );
	if( ! defined( 'CUTV_ENABLE_ADDONS' ) ) define( 'CUTV_ENABLE_ADDONS' , FALSE );
	if( ! defined( 'CUTV_BATCH_ADDING_ENABLED' ) ) define( 'CUTV_BATCH_ADDING_ENABLED' , FALSE );
	if( ! defined( 'CUTV_EG_FIX' ) ) define( 'CUTV_EG_FIX' , FALSE );
	if( ! defined( 'CUTV_FULL_DESC' ) ) define( 'CUTV_FULL_DESC' , FALSE );
	if( ! defined( 'CUTV_ENABLE_DATA_FILLERS' ) ) define( 'CUTV_ENABLE_DATA_FILLERS' , FALSE );
	if( ! defined( 'CUTV_CHECK_PLUGIN_UPDATES' ) ) define( 'CUTV_CHECK_PLUGIN_UPDATES' , TRUE );
	if( ! defined( 'CUTV_CHECK_ADDONS_UPDATES' ) ) define( 'CUTV_CHECK_ADDONS_UPDATES' , TRUE );
	if( ! defined( 'CUTV_META_DEBUG_MODE' ) ) define( 'CUTV_META_DEBUG_MODE' , FALSE );
	if( ! defined( 'CUTV_API_RESPONSE_DEBUG' ) ) define( 'CUTV_API_RESPONSE_DEBUG' , FALSE );
	if( ! defined( 'CUTV_SMOOTH_SCREEN_ENABLED' ) ) define( 'CUTV_SMOOTH_SCREEN_ENABLED' , TRUE );
	if( ! defined( 'CUTV_ENABLE_ADMINBAR_MENU' ) ) define( 'CUTV_ENABLE_ADMINBAR_MENU' , TRUE );

	/* LIMITATIONS */
	
	
	if( ! defined( 'CUTV_MAX_POSTING_CATS' ) ) define( 'CUTV_MAX_POSTING_CATS' , 10 );
	if( ! defined( 'CUTV_MANAGE_PERPAGE' ) ) define( 'CUTV_MANAGE_PERPAGE' , 100 );
	if( ! defined( 'CUTV_DEFERRED_PERPAGE' ) ) define( 'CUTV_DEFERRED_PERPAGE' , 100 );
	if( ! defined( 'CUTV_UNWANTED_PERPAGE' ) ) define( 'CUTV_UNWANTED_PERPAGE' , 100 );
	if( ! defined( 'CUTV_MANAGE_LAYOUT' ) ) define( 'CUTV_MANAGE_LAYOUT' , 'bgrid' );
	if( ! defined( 'CUTV_UNCATEGORIZED' ) ) define( 'CUTV_UNCATEGORIZED' , 'uncategorized' );


	/* Defining DEFAULT VIMEO CREDENTIALS */
	define( 'CUTV_VIMEO_CLIENT_ID' , '36db8aea80f16298d7dfd938ad605af336b7f3d9' );
	define( 'CUTV_VIMEO_CLIENT_SECRET' , '5e0b277b5a5d0b7a9ffb24b705b8145245929c96' );
	define( 'CUTV_VIMEO_ACCESS_TOKEN' , 'ff55ac086a0aac77c439d508555e9df6' );

	/* Defining DEFAULT DAILYMOTION CREDENTIALS */
	define( 'CUTV_DAILYMOTION_CLIENT_ID' , '8e31e0a2b41c2c11049f' );
	define( 'CUTV_DAILYMOTION_CLIENT_SECRET' , 'b6af36711bb9c09cff0ed4226389093409c85df8' );

	/* Define DEFAULT YOUTUBE CREDENTIALS */
	define( 'CUTV_DEFAULT_YOUTUBE_API_KEY' , 'AIzaSyBbYhvq425Pxsqm-OjatqzLGMcqu9y2lJk' );


	define( 'CUTV_REQUIRED_PHP_VERSION' , '5.5.0' );
	define( 'CUTV_REQUIRED_PHP_MEMORY_LIMIT' , '128M' );
	define( 'CUTV_REQUIRED_PHP_POST_MAX_SIZE' , '8M' );
	define( 'CUTV_REQUIRED_PHP_MAX_INPUT_TIME' , '60' );
	define( 'CUTV_REQUIRED_PHP_MAX_EXECUTION_TIME' , '600' );


	/* CONFIG CONSTANTS */