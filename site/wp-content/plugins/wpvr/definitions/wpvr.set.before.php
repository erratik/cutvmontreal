<?php
	global $wpvr_default_options , $current_user;
	
	/* DEfault Options Values */
	$wpvr_default_options = array(
		'unwanted'              => array() ,
		'autoRunMode'           => TRUE ,
		'apiConnect'            => 'wizzard' ,
		'getPostDate'           => 'new' ,
		'getStats'              => FALSE ,
		'getTags'               => FALSE ,
		'getFullDesc'           => FALSE ,
		'onlyNewVideos'         => TRUE ,
		'orderVideos'           => 'relevance' ,
		'postFormat'            => 'video' ,
		'autoPublish'           => TRUE ,
		'postAuthor'            => 1 ,
		'postTags'              => '' ,
		'addVideoType'          => TRUE ,
		'videoThumb'            => FALSE ,
		'useCronTab'            => TRUE ,
		'enableManualAdding'    => TRUE ,
		'deferAdding'           => TRUE ,
		'deferBuffer'           => 10 ,
		'wantedVideos'          => 3 ,
		'randomize'             => FALSE ,
		'randomizeStep'         => 'empty' ,
		'autoClean'             => FALSE ,
		'autoEmbed'             => TRUE ,
		'playerAutoPlay'        => FALSE ,
		'wakeUpHours'           => FALSE ,
		'wakeUpHoursA'          => '00' ,
		'wakeUpHoursB'          => '23' ,
		'logsPerPage'           => 100 ,
		'videosPerPage'         => 30 ,
		'restrictVideos'        => TRUE ,
		'purchaseCode'          => '' ,
		'apiKey'                => WPVR_DEFAULT_YOUTUBE_API_KEY ,
		'voClientId'            => WPVR_VIMEO_CLIENT_ID ,
		'voClientSecret'        => WPVR_VIMEO_CLIENT_SECRET ,
		'dmClientSecret'        => WPVR_DAILYMOTION_CLIENT_SECRET ,
		'dmClientId'            => WPVR_DAILYMOTION_CLIENT_ID ,
		'timeZone'              => 'UTC' ,
		'enableVideoComments'   => TRUE ,
		'enableVideoControls'   => TRUE ,
		'removeVideoContent'    => FALSE ,
		'enableRewriteRule'     => FALSE ,
		'startWithServiceViews' => FALSE ,
		'permalinkBase'         => 'none' ,
		'customPermalinkBase'   => '' ,
		'enableContentSuffix'   => TRUE ,
		'contentSuffix'         => ' -- SUFFIX ' ,
		'enableContentPrefix'   => TRUE ,
		'contentPrefix'         => ' PREFIX -- ' ,
		'showMenuFor'           => $wpvr_roles[ 'default' ] ,
		'videoQuality'          => 'any' ,
		'videoDuration'         => 'any' ,
		'privateCPT'            => $wpvr_private_cpt ,
		'postContent'           => 'on' ,
		'publishedAfter'        => '' ,
		'publishedBefore'       => '' ,
		'unwantOnTrash'         => FALSE ,
		'unwantOnDelete'        => TRUE ,
	);
	
	
	/* Getting WP Options to SGD */
	$wpvr_cron_token   = get_option( 'wpvr_cron_token' );
	$wpvr_options      = get_option( 'wpvr_options' );
	$wpvr_activation   = get_option( 'wpvr_activation' );
	$wpvr_deferred     = get_option( 'wpvr_deferred' );
	$wpvr_deferred_ids = get_option( 'wpvr_deferred_ids' );
	$wpvr_imported     = get_option( 'wpvr_imported' );
	$wpvr_notices      = get_option( 'wpvr_notices' );
	$wpvr_unwanted     = get_option( 'wpvr_unwanted' );
	$wpvr_unwanted_ids = get_option( 'wpvr_unwanted_ids' );

	
	if( $wpvr_notices == '' ) {
		$wpvr_notices = array();
	}
	/* Define Sanbox */
	if( ! defined( 'WPVR_ENABLE_SANDBOX' ) ) {
		define( 'WPVR_ENABLE_SANDBOX' , FALSE );
	}


	if( ! defined( 'WPVR_ENABLE_YOUTUBE' ) ) {
		define( 'WPVR_ENABLE_YOUTUBE' , TRUE );
	}
	if( ! defined( 'WPVR_ENABLE_VIMEO' ) ) {
		define( 'WPVR_ENABLE_VIMEO' , TRUE );
	}
	if( ! defined( 'WPVR_ENABLE_DAILYMOTION' ) ) {
		define( 'WPVR_ENABLE_DAILYMOTION' , TRUE );
	}

	//Trying to optimize execution time if safemode is not enabled
	if( defined( 'WPVR_MAX_EXECUTION_TIME' ) && WPVR_MAX_EXECUTION_TIME ) {
		@ini_set( 'max_execution_time' , WPVR_MAX_EXECUTION_TIME );
	}
	
	/* defining $wpvr_options */
	if(
		( is_bool( $wpvr_options ) && $wpvr_options === FALSE )
		|| $wpvr_options == ''
		|| $wpvr_options == null
	) {
		update_option( 'wpvr_options' , $wpvr_default_options );
		$wpvr_options = $wpvr_default_options;
	}

	$wpvr_options = wpvr_extend( $wpvr_options , $wpvr_default_options );
	
	/* DEfining $wpvr_deferred */
	if( is_bool( $wpvr_deferred ) && $wpvr_deferred === FALSE ) {
		update_option( 'wpvr_deferred' , array() );
	}
	if( is_bool( $wpvr_unwanted ) && $wpvr_unwanted === FALSE ) {
		update_option( 'wpvr_unwanted' , array() );
	}

	if( is_bool( $wpvr_unwanted_ids ) && $wpvr_unwanted_ids === FALSE ) {
		update_option( 'wpvr_unwanted_ids' , array() );
	}
	
	if( $wpvr_cron_token == '' ) {
		$wpvr_cron_token = md5( uniqid( rand() , TRUE ) );
		update_option( 'wpvr_cron_token' , $wpvr_cron_token );
	}

	// Defining default timezone
	if( $wpvr_options[ 'timeZone' ] != '' && $wpvr_options[ 'timeZone' ] != null ) {
		date_default_timezone_set( $wpvr_options[ 'timeZone' ] );
	} else {
		date_default_timezone_set( 'UTC' );
	}
	
	$wpvr_addons = array();

