<?php
	
	/* Require Ajax WP load */
	if( isset( $_GET[ 'wpvr_wpload' ] ) || isset( $_POST[ 'wpvr_wpload' ] ) ) {
		define( 'DOING_AJAX' , TRUE );
		//define('WP_ADMIN', true );
		$wpload = 'wp-load.php';
		while( ! is_file( $wpload ) ) {
			if( is_dir( '..' ) ) {
				chdir( '..' );
			} else {
				die( 'EN: Could not find WordPress! FR : Impossible de trouver WordPress !' );
			}
		}
		require_once( $wpload );
	}
	
	global $wpvr_options , $wpvr_default_options , $wpvr_cron_token , $wpvr_vs;
	
	
	$wpvr_hard_refresh_url = admin_url( 'admin.php?page=wpvr&update_imported' );
	
	
	/* Clear Token */
	if( isset( $_GET[ 'clear_token' ] ) ) {
		$service = $_GET[ 'service' ];
		global $wpvr_tokens;
		$wpvr_tokens[ $service ] = array(
			'access_token'  => '' ,
			'refresh_token' => '' ,
		);
		update_option( 'wpvr_tokens' , $wpvr_tokens );
		
		return FALSE;
	}
	
	/* Set Token from Autho */
	if( isset( $_GET[ 'set_token' ] ) ) {
		
		$token = array(
			'access_token'  => $_GET[ 'access_token' ] ,
			'refresh_token' => $_GET[ 'refresh_token' ] ,
		);
		
		$tokens                       = get_option( 'wpvr_tokens' );
		$tokens[ $_GET[ 'service' ] ] = $token;
		//d( $token );
		//d( $tokens );
		update_option( 'wpvr_tokens' , $tokens );
		
		?>
		<script> window.close(); </script><?php
		
		
		return FALSE;
	}
	
	if( isset( $_GET[ 'update_wakeUpHours' ] ) ) {
		?>
		<?php $workingHours = wpvr_make_interval( $_GET[ 'start' ] , $_GET[ 'end' ] ); ?>
		<?php foreach ( $workingHours as $wh => $state ) { ?>
			<?php if( $state === TRUE ) { ?>
				<div title = "AUTORUN ON" class = "wpvr_wh is_working"><?php echo $wh . 'H'; ?></div>
			<?php } else { ?>
				<div title = "AUTORUN OFF" class = "wpvr_wh"><?php echo $wh . 'H'; ?></div>
			<?php } ?>
		<?php } ?>
		<div class = "wpvr_clearfix"></div>
		<?php
		return FALSE;
	}
	
	
	/* Show System Infos */
	if( isset( $_GET[ 'system_infos' ] ) ) {
		
		ob_start();
		
		$info      = wpvr_get_system_info();
		$sys_infos = $info[ 'sys' ];
		
		if( isset( $_GET[ 'do_export' ] ) ) {
			
			
			//wpvr_remove_tmp_files();
			
			$systeminfo_txt = wpvr_render_system_info( $info );
			$file           = "tmp_export_" . mt_rand( 0 , 1000 ) . "_sysinfo.txt";
			file_put_contents( WPVR_TMP_PATH . $file , $systeminfo_txt );
			$export_url = get_option( 'siteurl' ) . "/wpvr_export/" . $file;
			//echo wpvr_get_json_response($export_url , 1 , 'File Created.' );
			
			?>
			<iframe id = "wpvr_iframe" src = "" style = "display:none; visibility:hidden;"></iframe>
			<script>
				jQuery('#wpvr_iframe').attr('src', "<?php echo $export_url; ?>");
			</script>
			<?php
			
			
			return FALSE;
		}
		
		
		foreach ( $sys_infos as $sys ) {
			if( ! is_bool( $sys[ 'value' ] ) ) {
				$value = $sys[ 'value' ];
			} elseif( $sys[ 'value' ] ) {
				$value = "TRUE";
			} else {
				$value = "FALSE";
			}
			?>
			<div class = "wpvr_syst_info <?php echo $sys[ 'status' ]; ?>">
				<?php if( $sys[ 'status' ] == 'good' ) { ?>
					<i class = "fa fa-check"></i>
				<?php } elseif( $sys[ 'status' ] == 'bad' ) { ?>
					<i class = "fa fa-ban"></i>
				<?php } else { ?>
					<i class = "fa fa-cog"></i>
				<?php } ?>
				
				<strong><?php echo $sys[ 'label' ]; ?></strong> : <?php echo $sys[ 'value' ]; ?>
			</div>
			<?php
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		echo wpvr_get_json_response( $output , 1 );
		
		return FALSE;
	}
	
	/* reset Option */
	if( isset( $_GET[ 'reset_options' ] ) ) {
		global $wpvr_default_options , $wpvr_default_tokens;
		update_option( 'wpvr_options' , $wpvr_default_options );
		update_option( 'wpvr_tokens' , $wpvr_default_tokens );
		echo wpvr_get_json_response( null , 1 , 'Options Reset.' );
		exit;
	}
	
	
	/* Export Options */
	if( isset( $_GET[ 'export_options' ] ) ) {
		global $wpvr_options , $wpvr_tokens;
		
		$wpvr_options[ 'tokens' ] = $wpvr_tokens;
		
		//wpvr_remove_tmp_files();
		
		$json_options = array(
			'data'    => ( $wpvr_options ) ,
			'version' => WPVR_VERSION ,
			'type'    => 'options' ,
		);
		$file         = "tmp_export_" . mt_rand( 0 , 1000 ) . '_@_options';
		file_put_contents( WPVR_TMP_PATH . $file , json_encode( $json_options ) );
		$export_url = get_option( 'siteurl' ) . "/wpvr_export/" . $file;
		
		
		?>
		<iframe id = "wpvr_iframe" src = "" style = "display:none; visibility:hidden;"></iframe>
		<script>
			jQuery('#wpvr_iframe').attr('src', "<?php echo $export_url; ?>");
		</script>
		<?php
		exit;
	}
	
	/* SAVE OPTIONS */
	if( isset( $_GET[ 'save_options' ] ) ) {
		$new_options = $wpvr_options;
		
		
		if( isset( $_POST[ 'wpvr_options_autoRunMode' ] ) ) {
			$new_options[ 'autoRunMode' ] = TRUE;
		} else {
			$new_options[ 'autoRunMode' ] = FALSE;
		}
		
		
		if( isset( $_POST[ 'wpvr_options_restrictVideos' ] ) ) {
			$new_options[ 'restrictVideos' ] = TRUE;
		} else {
			$new_options[ 'restrictVideos' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_getTags' ] ) ) {
			$new_options[ 'getTags' ] = TRUE;
		} else {
			$new_options[ 'getTags' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_getFullDesc' ] ) ) {
			$new_options[ 'getFullDesc' ] = TRUE;
		} else {
			$new_options[ 'getFullDesc' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_getStats' ] ) ) {
			$new_options[ 'getStats' ] = TRUE;
		} else {
			$new_options[ 'getStats' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_addVideoType' ] ) ) {
			$new_options[ 'addVideoType' ] = TRUE;
		} else {
			$new_options[ 'addVideoType' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_enableVideoControls' ] ) ) {
			$new_options[ 'enableVideoControls' ] = TRUE;
		} else {
			$new_options[ 'enableVideoControls' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_enableVideoComments' ] ) ) {
			$new_options[ 'enableVideoComments' ] = TRUE;
		} else {
			$new_options[ 'enableVideoComments' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_enableRewriteRule' ] ) ) {
			$new_options[ 'enableRewriteRule' ] = TRUE;
		} else {
			$new_options[ 'enableRewriteRule' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_videoThumb' ] ) ) {
			$new_options[ 'videoThumb' ] = TRUE;
		} else {
			$new_options[ 'videoThumb' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_enableManualAdding' ] ) ) {
			$new_options[ 'enableManualAdding' ] = TRUE;
		} else {
			$new_options[ 'enableManualAdding' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_useCronTab' ] ) ) {
			$new_options[ 'useCronTab' ] = TRUE;
		} else {
			$new_options[ 'useCronTab' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_randomize' ] ) ) {
			$new_options[ 'randomize' ] = TRUE;
		} else {
			$new_options[ 'randomize' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_onlyNewVideos' ] ) ) {
			$new_options[ 'onlyNewVideos' ] = TRUE;
		} else {
			$new_options[ 'onlyNewVideos' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_startWithServiceViews' ] ) ) {
			$new_options[ 'startWithServiceViews' ] = TRUE;
		} else {
			$new_options[ 'startWithServiceViews' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_autoPublish' ] ) ) {
			$new_options[ 'autoPublish' ] = TRUE;
		} else {
			$new_options[ 'autoPublish' ] = FALSE;
		}
		
		//if( isset( $_POST[ 'wpvr_options_autoClean' ] ) ) $new_options[ 'autoClean' ] = TRUE;
		//else $new_options[ 'autoClean' ] = FALSE;
		
		if( isset( $_POST[ 'wpvr_options_autoEmbed' ] ) ) {
			$new_options[ 'autoEmbed' ] = TRUE;
		} else {
			$new_options[ 'autoEmbed' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_unwantOnDelete' ] ) ) $new_options[ 'unwantOnDelete' ] = TRUE;
		else $new_options[ 'unwantOnDelete' ] = FALSE;
		
		if( isset( $_POST[ 'wpvr_options_unwantOnTrash' ] ) ) $new_options[ 'unwantOnTrash' ] = TRUE;
		else $new_options[ 'unwantOnTrash' ] = FALSE;
		
		
		if( isset( $_POST[ 'wpvr_options_removeVideoContent' ] ) ) {
			$new_options[ 'removeVideoContent' ] = TRUE;
		} else {
			$new_options[ 'removeVideoContent' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_playerAutoPlay' ] ) ) {
			$new_options[ 'playerAutoPlay' ] = TRUE;
		} else {
			$new_options[ 'playerAutoPlay' ] = FALSE;
		}
		
		
		if( isset( $_POST[ 'wpvr_options_max' ] ) ) {
			$new_options[ 'max' ] = $_POST[ 'wpvr_options_max' ];
		} else {
			$new_options[ 'max' ] = "";
		}
		
		if( isset( $_POST[ 'wpvr_options_purchaseCode' ] ) ) {
			$new_options[ 'purchaseCode' ] = $_POST[ 'wpvr_options_purchaseCode' ];
		} else {
			$new_options[ 'purchaseCode' ] = "";
		}
		
		if( isset( $_POST[ 'wpvr_options_publishedAfter' ] ) ) {
			$new_options[ 'publishedAfter' ] = $_POST[ 'wpvr_options_publishedAfter' ];
		} else {
			$new_options[ 'publishedAfter' ] = "";
		}
		if( isset( $_POST[ 'wpvr_options_publishedBefore' ] ) ) {
			$new_options[ 'publishedBefore' ] = $_POST[ 'wpvr_options_publishedBefore' ];
		} else {
			$new_options[ 'publishedBefore' ] = "";
		}
		
		//Generated Option
		if( isset( $_POST[ 'showMenuFor' ] ) ) {
			$new_options[ 'showMenuFor' ] = $_POST[ 'showMenuFor' ];
		} else {
			$new_options[ 'showMenuFor' ] = '';
		}
		
		if( isset( $_POST[ 'privateCPT' ] ) ) {
			$new_options[ 'privateCPT' ] = $_POST[ 'privateCPT' ];
		} else {
			$new_options[ 'privateCPT' ] = '';
		}
		
		if( isset( $_POST[ 'wpvr_options_apiConnect' ] ) ) {
			$new_options[ 'apiConnect' ] = $_POST[ 'wpvr_options_apiConnect' ];
		} else {
			$new_options[ 'apiConnect' ] = '';
		}
		
		/* YOUTUBE API KEY */
		if( isset( $_POST[ 'wpvr_options_apiKey' ] ) ) {
			$new_options[ 'apiKey' ] = $_POST[ 'wpvr_options_apiKey' ];
		} else {
			$new_options[ 'apiKey' ] = "";
		}
		
		/* DAILYMOTION API KEY */
		if( isset( $_POST[ 'wpvr_options_dmClientId' ] ) ) {
			$new_options[ 'dmClientId' ] = $_POST[ 'wpvr_options_dmClientId' ];
		} else {
			$new_options[ 'dmClientId' ] = "";
		}
		if( isset( $_POST[ 'wpvr_options_dmClientSecret' ] ) ) {
			$new_options[ 'dmClientSecret' ] = $_POST[ 'wpvr_options_dmClientSecret' ];
		} else {
			$new_options[ 'dmClientSecret' ] = "";
		}
		
		/* viemo API KEY */
		if( isset( $_POST[ 'wpvr_options_voClientId' ] ) ) {
			$new_options[ 'voClientId' ] = $_POST[ 'wpvr_options_voClientId' ];
		} else {
			$new_options[ 'voClientId' ] = "";
		}
		if( isset( $_POST[ 'wpvr_options_voClientSecret' ] ) ) {
			$new_options[ 'voClientSecret' ] = $_POST[ 'wpvr_options_voClientSecret' ];
		} else {
			$new_options[ 'voClientSecret' ] = "";
		}
		
		
		if( isset( $_POST[ 'wpvr_options_logsPerPage' ] ) ) {
			$new_options[ 'logsPerPage' ] = $_POST[ 'wpvr_options_logsPerPage' ];
		} else {
			$new_options[ 'logsPerPage' ] = "";
		}

		if( isset( $_POST[ 'wpvr_options_videosPerPage' ] ) ) {
			$new_options[ 'videosPerPage' ] = $_POST[ 'wpvr_options_videosPerPage' ];
		} else {
			$new_options[ 'videosPerPage' ] = "";
		}
		
		if( isset( $_POST[ 'wpvr_options_wantedVideos' ] ) ) {
			$new_options[ 'wantedVideos' ] = $_POST[ 'wpvr_options_wantedVideos' ];
		} else {
			$new_options[ 'wantedVideos' ] = '';
		}
		
		if( isset( $_POST[ 'wpvr_options_postTags' ] ) ) {
			$new_options[ 'postTags' ] = $_POST[ 'wpvr_options_postTags' ];
		} else {
			$new_options[ 'postTags' ] = '';
		}
		
		if( isset( $_POST[ 'wpvr_options_deferBuffer' ] ) && $_POST[ 'wpvr_options_deferBuffer' ] != '' ) {
			$new_options[ 'deferBuffer' ] = $_POST[ 'wpvr_options_deferBuffer' ];
		} else {
			$new_options[ 'deferBuffer' ] = $wpvr_default_options[ 'deferBuffer' ];
		}
		
		if( isset( $_POST[ 'wpvr_options_randomizeStep' ] ) && $_POST[ 'wpvr_options_randomizeStep' ] != '' ) {
			$new_options[ 'randomizeStep' ] = $_POST[ 'wpvr_options_randomizeStep' ];
		} else {
			$new_options[ 'randomizeStep' ] = $wpvr_default_options[ 'randomizeStep' ];
		}
		
		if( isset( $_POST[ 'wpvr_options_customPermalinkBase' ] ) && $_POST[ 'wpvr_options_customPermalinkBase' ] != '' ) {
			$new_options[ 'customPermalinkBase' ] = $_POST[ 'wpvr_options_customPermalinkBase' ];
		} else {
			$new_options[ 'customPermalinkBase' ] = $wpvr_default_options[ 'customPermalinkBase' ];
		}
		
		if( isset( $_POST[ 'wpvr_options_permalinkBase' ] ) && $_POST[ 'wpvr_options_permalinkBase' ] != '' ) {
			$new_options[ 'permalinkBase' ] = $_POST[ 'wpvr_options_permalinkBase' ];
		} else {
			$new_options[ 'permalinkBase' ] = $wpvr_default_options[ 'permalinkBase' ];
		}
		
		if( isset( $_POST[ 'wpvr_options_wakeUpHours' ] ) ) {
			$new_options[ 'wakeUpHours' ] = TRUE;
		} else {
			$new_options[ 'wakeUpHours' ] = FALSE;
		}
		
		if( isset( $_POST[ 'wpvr_options_wakeUpHoursA' ] ) && $_POST[ 'wpvr_options_wakeUpHoursA' ] != '' ) {
			$new_options[ 'wakeUpHoursA' ] = $_POST[ 'wpvr_options_wakeUpHoursA' ];
		} else {
			$new_options[ 'wakeUpHoursA' ] = $wpvr_default_options[ 'wakeUpHoursA' ];
		}
		
		if( isset( $_POST[ 'wpvr_options_wakeUpHoursB' ] ) && $_POST[ 'wpvr_options_wakeUpHoursB' ] != '' ) {
			$new_options[ 'wakeUpHoursB' ] = $_POST[ 'wpvr_options_wakeUpHoursB' ];
		} else {
			$new_options[ 'wakeUpHoursB' ] = $wpvr_default_options[ 'wakeUpHoursB' ];
		}
		
		if( isset( $_POST[ 'wpvr_options_timeZone' ] ) && $_POST[ 'wpvr_options_timeZone' ] != '' ) {
			$new_options[ 'timeZone' ] = $_POST[ 'wpvr_options_timeZone' ];
		} else {
			$new_options[ 'timeZone' ] = $wpvr_default_options[ 'timeZone' ];
		}
		
		
		if( isset( $_POST[ 'wpvr_options_getPostDate' ] ) ) {
			$new_options[ 'getPostDate' ] = $_POST[ 'wpvr_options_getPostDate' ];
		} else {
			$new_options[ 'getPostDate' ] = "";
		}
		
		if( isset( $_POST[ 'wpvr_options_postContent' ] ) ) {
			$new_options[ 'postContent' ] = $_POST[ 'wpvr_options_postContent' ];
		} else {
			$new_options[ 'postContent' ] = "";
		}
		
		if( isset( $_POST[ 'wpvr_options_orderVideos' ] ) ) {
			$new_options[ 'orderVideos' ] = $_POST[ 'wpvr_options_orderVideos' ];
		} else {
			$new_options[ 'orderVideos' ] = "";
		}
		
		if( WPVR_ENABLE_POST_FORMATS ) {
			if( isset( $_POST[ 'wpvr_options_postFormat' ] ) ) {
				$new_options[ 'postFormat' ] = $_POST[ 'wpvr_options_postFormat' ];
			} else {
				$new_options[ 'postFormat' ] = "";
			}
		}
		
		if( isset( $_POST[ 'wpvr_options_postAuthor' ] ) ) {
			$new_options[ 'postAuthor' ] = $_POST[ 'wpvr_options_postAuthor' ];
		} else {
			$new_options[ 'postAuthor' ] = "";
		}
		
		if( isset( $_POST[ 'wpvr_options_videoQuality' ] ) ) {
			$new_options[ 'videoQuality' ] = $_POST[ 'wpvr_options_videoQuality' ];
		} else {
			$new_options[ 'videoQuality' ] = "";
		}
		
		if( isset( $_POST[ 'wpvr_options_videoDuration' ] ) ) {
			$new_options[ 'videoDuration' ] = $_POST[ 'wpvr_options_videoDuration' ];
		} else {
			$new_options[ 'videoDuration' ] = "";
		}
		
		if( isset( $_POST[ 'wpvr_options_deferAdding' ] ) && $_POST[ 'wpvr_options_deferAdding' ] != 'off' ) {
			$new_options[ 'deferAdding' ] = TRUE;
		} else {
			$new_options[ 'deferAdding' ] = FALSE;
		}
		
		update_option( 'wpvr_options' , $new_options );
		$wpvr_options = $new_options;
		echo wpvr_get_json_response( null , 1 , 'Options Saved' );
		exit;
	}
	
	global $wpvr_pages;
	$wpvr_pages = TRUE;
?>
<div class = "wrap wpvr_wrap" style = "visibility:hidden;">
	
	<h2 class = "wpvr_title">
		<?php wpvr_show_logo(); ?>
		<i class = "wpvr_title_icon fa fa-wrench	"></i>
		<?php echo __( 'Manage Options' , WPVR_LANG ); ?>
		
		<div class = "wpvr_clearfix"></div>
	</h2>
	
	
	<form
		name = "wpvr_options"
		id = "wpvr_options"
		method = "post"
		action = "<?php echo WPVR_OPTIONS_URL; ?>"
		is_demo = "<?php echo WPVR_IS_DEMO ? 1 : 0; ?>"
	>
		
		
		<div class = "">
			<div id = "wpvr_options_wrapper">
				
				<?php
					
					$active = array(
						'general'     => '' ,
						'fetching'    => '' ,
						'posting'     => '' ,
						'integration' => '' ,
						'automation'  => '' ,
						'api_keys'    => '' ,
						'advanced'    => '' ,
					);
					
					if( ! isset( $_GET[ 'section' ] ) || ! isset( $active[ $_GET[ 'section' ] ] ) ) {
						$active[ 'general' ] = 'active';
					} else {
						$active[ $_GET[ 'section' ] ] = 'active';
					}
					//echo $_GET['tab'] ;
				?>
				<div class = "wpvr_nav_tabs pull-left">
					<div title = "<?php _e( 'General' , WPVR_LANG ); ?>" class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'general' ]; ?>" id = "a">
						<i class = "wpvr_tab_icon fa fa-rocket"></i><br/>
						<span><?php _e( 'General' , WPVR_LANG ); ?></span>
					</div>
					<div title = "<?php _e( 'Fetching' , WPVR_LANG ); ?>" class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'fetching' ]; ?>" id = "ap">
						<i class = "wpvr_tab_icon fa fa-search"></i><br/>
						<span><?php _e( 'Fetching' , WPVR_LANG ); ?></span>
					</div>
					<div title = "<?php _e( 'Posting' , WPVR_LANG ); ?>" class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'posting' ]; ?>" id = "b">
						<i class = "wpvr_tab_icon fa fa-cloud-download"></i><br/>
						<span><?php _e( 'Posting' , WPVR_LANG ); ?></span>
					</div>
					<div title = "<?php _e( 'Integration' , WPVR_LANG ); ?>" class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'integration' ]; ?>" id = "d">
						<i class = "wpvr_tab_icon fa fa-plug"></i><br/>
						<span><?php _e( 'Integration' , WPVR_LANG ); ?></span>
					</div>
					<div title = "<?php _e( 'Automation' , WPVR_LANG ); ?>" class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'automation' ]; ?>" id = "c">
						<i class = "wpvr_tab_icon fa fa-gears"></i><br/>
						<span><?php _e( 'Automation' , WPVR_LANG ); ?></span>
					</div>
					<div title = "<?php _e( 'API Access' , WPVR_LANG ); ?>" class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'api_keys' ]; ?>" id = "f">
						<i class = "wpvr_tab_icon fa fa-key"></i><br/>
						<span><?php _e( 'API Access' , WPVR_LANG ); ?></span>
					</div>
					<span class = "wpvr_version_helper">
						<?php echo "v" . WPVR_VERSION; ?>
					</span>
					
					<div class = "wpvr_clearfix"></div>
				</div>
				<div class = "wpvr_clearfix"></div>
				<div class = "result_options"></div>
				<input
					type = "hidden"
					name = "save_options"
					value = "1"
				/>
				<button
					url = "<?php echo WPVR_OPTIONS_URL; ?>"
					id = "wpvr_system_infos"
					class = "pull-left wpvr_button wpvr_black_button wpvr_medium"
				>
					<i class = "fa fa-info-circle"></i>
					<?php _e( 'Show System Infos' , WPVR_LANG ); ?>
				</button>

				<!-- REMOVED ON 1.8.3 for problems -->
				<?php if( FALSE ) { ?>
					<button
						url = "<?php echo admin_url( 'post-new.php' ) . '?wpvr_get_mb=1'; ?>"
						id = "wpvr_get_metaboxes"
						class = "wpvr_black_button pull-left wpvr_button wpvr_medium"
					>
						<i class = "wpvr_button_icon fa fa-cube"></i>
						<?php _e( 'Get Themes Metaboxes' , WPVR_LANG ); ?>
					</button>
				<?php } ?>
				

				<button id = "wpvr_save_options" class = "actionBtn pull-right wpvr_submit_button wpvr_save_options">
					<i class = "wpvr_button_icon fa fa-save"></i><?php _e( 'Save options' , WPVR_LANG ); ?>
				</button>
				
				<?php do_action( 'wpvr_screen_options_top' ); ?>
				
				
				<div class = "wpvr_clearfix"></div>
				<!-- GENERAL OPTIONS -->
				
				<!-- timeZone -->
				<div class = "wpvr_option tab_a on">
					
					<div class = "pull-right wpvr_timezone_field">
						<?php
							global $wpvr_timezones;
							$wpvr_timezones_array = array();
							foreach ( (array) $wpvr_timezones as $g => $gZone ) {
								foreach ( (array) $gZone as $gValue => $gLabel ) {
									$wpvr_timezones_array[ $gValue ] = $gLabel;
								}
							}

							wpvr_render_selectized_field( array(
								'name'        => 'wpvr_timeZone' ,
								'placeholder' => 'Pick your timezone' ,
								'values'      => $wpvr_timezones_array ,
								//'maxItems'    => 1 ,

							) , $wpvr_options[ 'timeZone' ] );

						?>
					</div>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Default Time Zone' , WPVR_LANG ); ?> </span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Choose your default timezone.' , WPVR_LANG ); ?> </p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /timeZone -->
				
				
				<!-- enableManualAdding -->
				<div class = "wpvr_option tab_a <?php echo wpvr_get_button_state( $wpvr_options[ 'enableManualAdding' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_enableManualAdding' , $wpvr_options[ 'enableManualAdding' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Enable manual video adding' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Enable grabbing a single video by its id.' , WPVR_LANG ); ?></p>
					</div>
				</div>
				<!-- enableManualAdding -->

				<!-- deferAdding NEW -->
				<div class = "wpvr_option tab_a on">
					<div class = "wpvr_option_button pull-right">
						<?php //wpvr_make_switch_button( 'wpvr_options_deferAdding' , $wpvr_options[ 'deferAdding' ] ); ?>
						<?php
							$isSelected = array( 'on' => '' , 'off' => '' );
							$v          = $wpvr_options[ 'deferAdding' ] ? 'on' : 'off';

							$isSelected[ $v ] = ' selected="selected" ';
						?>
						<select
							class = "wpvr_option_select pull-right "
							name = "wpvr_options_deferAdding"
							id = "wpvr_options_deferAdding"
							style = "margin-left: 0px;"
						>
							<option value = "on" <?php echo $isSelected[ 'on' ]; ?>>
								<?php _e( 'Enabled' , WPVR_LANG ); ?>
							</option>
							<option value = "off" <?php echo $isSelected[ 'off' ]; ?>>
								<?php _e( 'Disabled' , WPVR_LANG ); ?>
							</option>
						</select>
					</div>
					<?php
						if( $wpvr_options[ 'deferAdding' ] ) {
							$select_state = "";
						} else {
							$select_state = " readonly ";
						}
					?>
					<input
						type = "text"
						class = "wpvr_options_input pull-right enabledBy_deferAdding"
						id = "wpvr_options_deferBuffer"
						name = "wpvr_options_deferBuffer"
						style = "margin-top: 7px;"
						value = "<?php echo $wpvr_options[ 'deferBuffer' ]; ?>"
						<?php echo $select_state; ?>
					/>

					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Defer video adding' , WPVR_LANG ); ?></span><br/>

						<p class = "wpvr_option_desc"> <?php _e( 'Limit the the number of added videos at once. Enable this option to improve performances.' , WPVR_LANG ); ?></p>
					</div>
				</div>
				<!-- deferAdding -->
				
				
				<!-- addVideoType *** -->
				<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'addVideoType' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_addVideoType' , $wpvr_options[ 'addVideoType' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Auto-include videos in your site queries' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Enable this option to subjoin imported videos to all your existant wordpress queries without changing your theme files.' , WPVR_LANG ); ?></p>
					</div>
				</div>
				<!-- addVideoType -->
				
				<!-- enableVideoComments *** -->
				<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'enableVideoComments' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_enableVideoComments' , $wpvr_options[ 'enableVideoComments' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Enable Comments on Imported Videos' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Enable this option to add comments support to the imported videos.' , WPVR_LANG ); ?></p>
					</div>
				</div>
				<!-- enableVideoComments -->
				
				<!-- enableVideoControls *** -->
				<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'enableVideoControls' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_enableVideoControls' , $wpvr_options[ 'enableVideoControls' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Enable Player Controls on Imported Videos' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc">
							<?php _e( 'Enable this option to show up or hide player controls on imported videos.' , WPVR_LANG ); ?><br/>
							<?php _e( 'Works only for Youtube videos.' , WPVR_LANG ); ?>
						</p>
					</div>
				</div>
				<!-- enableVideoControls -->
				
				<!-- enableRewriteRule *** -->
				<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'enableRewriteRule' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<div style = "width:100px;float:right;">
							<?php wpvr_make_switch_button( 'wpvr_options_enableRewriteRule' , $wpvr_options[ 'enableRewriteRule' ] ); ?>
						</div>
						<?php
							$isSelected                                     = array( 'none' => '' , 'category' => '' , 'custom' => '' );
							$isSelected[ $wpvr_options[ 'permalinkBase' ] ] = ' selected="selected" ';
							
							if( $wpvr_options[ 'permalinkBase' ] != 'custom' ) {
								$hideIt = "display:none;";
							} else {
								$hideIt = "";
							}
							
							if( $wpvr_options[ 'enableRewriteRule' ] === FALSE ) {
								$hideMe = "display:none;";
							} else {
								$hideMe = "";
							}
							
							//new dBug( $wpvr_options );
						
						?>
						<div class = "wpvr_clearfix"><br/></div>
						<div class = "wpvr_options_enableRewriteRule_helper" style = "<?php echo $hideMe; ?>">
							<select
								class = "wpvr_option_select pull-right "
								name = "wpvr_options_permalinkBase"
								id = "wpvr_options_permalinkBase"
							>
								<option value = "none" <?php echo $isSelected[ 'none' ]; ?>>
									<?php _e( 'No Permalink Base' , WPVR_LANG ); ?>
								</option>
								<option value = "category" <?php echo $isSelected[ 'category' ]; ?>>
									<?php _e( 'Category Permalink Base' , WPVR_LANG ); ?>
								</option>
								<option value = "custom" <?php echo $isSelected[ 'custom' ]; ?>>
									<?php _e( 'Custom Permalink Base' , WPVR_LANG ); ?>
								</option>
							</select>
							
							<div class = "wpvr_clearfix"><br/></div>
							
							<input
								type = "text"
								class = "wpvr_options_input wpvr_large pull-right"
								id = "wpvr_options_customPermalinkBase"
								name = "wpvr_options_customPermalinkBase"
								value = "<?php echo $wpvr_options[ 'customPermalinkBase' ]; ?>"
								style = "<?php echo $hideIt; ?>"
								placeholder = "Custom Permalink Base"
							/>
							
							<div class = "wpvr_clearfix"><br/></div>
						</div>
					
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Enable Permalink Rewrite' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc">
							<?php _e( 'Enable this option to activate videos permalink rewrite.' , WPVR_LANG ); ?><br/>
							<?php _e( 'Turn off this option to handle permalinks from the WP Permalink Settings screen.' , WPVR_LANG ); ?><br/>
							
							<br/>
						
						<div class = "wpvr_options_enableRewriteRule_helper" style = "<?php echo $hideMe; ?>">
							<b>No Permalink Base </b> : domain.com/my-imported-video-title <br/>
							<b>Category Permalink Base </b> : domain.com/my-category/my-imported-video-title <br/>
							<b>Custom Permalink Base </b> : domain.com/my-custom-text/my-imported-video-title <br/>
						</div>
						</p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- enableRewriteRule -->
				
				<!-- videoThumb *** -->
				<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'videoThumb' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_videoThumb' , $wpvr_options[ 'videoThumb' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Embed Video Instead of Image Thumbnail' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Enable this option to replace in the loop the post thumbnails by embeded video players.' , WPVR_LANG ); ?></p>
					</div>
				</div>
				<!-- videoThumb -->
				
				
				<!-- restrictVideos *** -->
				<div class = "wpvr_option tab_a <?php echo wpvr_get_button_state( $wpvr_options[ 'restrictVideos' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_restrictVideos' , $wpvr_options[ 'restrictVideos' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Restrict videos to their authors' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Restrict edition and listing of imported videos to admin and respective authors.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- /restrictVideos -->
				
				<!-- autoEmbed -->
				<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'autoEmbed' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_autoEmbed' , $wpvr_options[ 'autoEmbed' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'AutoEmbed Videos Player in Content' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Automatically embed youtube video player on single template without editing your theme files.' , WPVR_LANG ); ?>
							<br/><?php _e( 'Turn this off to embed the player manually so you can customize it.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- / autoEmbed -->
				
				<!-- restrictVideos *** -->
				<div class = "wpvr_option tab_a <?php echo wpvr_get_button_state( $wpvr_options[ 'unwantOnTrash' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_unwantOnTrash' , $wpvr_options[ 'unwantOnTrash' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Auto unwant when trashed' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Choose whether to automatically add videos to unwanted when you trash them.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- /restrictVideos -->
				
				<!-- restrictVideos *** -->
				<div class = "wpvr_option tab_a <?php echo wpvr_get_button_state( $wpvr_options[ 'unwantOnDelete' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_unwantOnDelete' , $wpvr_options[ 'unwantOnDelete' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Auto unwant when deleted' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Choose whether to automatically add videos to unwanted when you delete them permanently.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- /restrictVideos -->
				
				<!-- removeVideoContent -->
				<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'removeVideoContent' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_removeVideoContent' , $wpvr_options[ 'removeVideoContent' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Remove Video Text Content' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Show only the video player ?' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- / removeVideoContent -->
				
				<!-- playerAutoPlay -->
				<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'playerAutoPlay' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_playerAutoPlay' , $wpvr_options[ 'playerAutoPlay' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'AutoPlay Embedded Player in Content' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Automatically play videos on single view.' , WPVR_LANG ); ?>
						</p>
					</div>
				</div>
				<!-- / playerAutoPlay -->
				
				<!-- autoClean -->
				<!-- DEPRECATED SINCE v1.7.5 -->
				<?php if( FALSE ) { ?>
					<div class = "wpvr_option tab_d <?php echo wpvr_get_button_state( $wpvr_options[ 'autoClean' ] ); ?>">
						<div class = "wpvr_option_button pull-right">
							<?php wpvr_make_switch_button( 'wpvr_options_autoClean' , $wpvr_options[ 'autoClean' ] ); ?>
						</div>
						<div class = "option_text">
							<span class = "wpvr_option_title"><?php _e( 'Enable Auto Clean Mode' , WPVR_LANG ); ?></span><br/>
							
							<p class = "wpvr_option_desc"> <?php _e( 'Automatically change video status to invalid for deleted or unreachable youtube videos.' , WPVR_LANG ); ?> </p>
						</div>
					</div>
				<?php } ?>
				<!-- / autoClean -->
				
				
				<!-- Logs Per Page -->
				<div class = "wpvr_option tab_a">
					<input
						type = "text"
						class = "wpvr_options_input pull-right"
						id = "wpvr_options_logsPerPage"
						name = "wpvr_options_logsPerPage"
						value = "<?php echo $wpvr_options[ 'logsPerPage' ]; ?>"
					/>
					
					<div>
						<span class = "wpvr_option_title"><?php _e( 'Logs per page' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Number of log lines to display per page.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- / Logs Per Page -->

				<!-- Videos Per Page -->
				<div class = "wpvr_option tab_a">
					<input
						type = "text"
						class = "wpvr_options_input pull-right"
						id = "wpvr_options_videosPerPage"
						name = "wpvr_options_videosPerPage"
						value = "<?php echo $wpvr_options[ 'videosPerPage' ]; ?>"
					/>

					<div>
						<span class = "wpvr_option_title"><?php _e( 'Videos per page' , WPVR_LANG ); ?></span><br/>

						<p class = "wpvr_option_desc">
							<?php _e( 'Number of videos to display per page.' , WPVR_LANG ); ?><br/>
							<?php _e( 'Works on Manage Videos, Duplicates, Deferred Videos, Unwanted Videos screens.' , WPVR_LANG ); ?>


						</p>
					</div>
				</div>
				<!-- / Videos Per Page -->
				
				
				<!-- USE ADVANCED CREDENTIALS -->
				<div class = "wpvr_option tab_f">
					<div class = "wpvr_option_button pull-right">
						<?php
							$isSelected                                  = array( 'advanced' => '' , 'wizzard' => '' );
							$isSelected[ $wpvr_options[ 'apiConnect' ] ] = ' selected="selected" ';
							if( $wpvr_options[ 'apiConnect' ] == 'wizzard' ) {
								$hideAdvanced = "display:none !important;";
								$hideWizzard  = "";
								$class        = "is_advanced";
							} else {
								$hideWizzard  = "display:none !important;";
								$hideAdvanced = "";
								$class        = "is_wizard";
							}
							
							$hideWizzard = $hideAdvanced = "";
							
							if( isset( $_GET[ 'section' ] ) ) {
								$skipFade = '0';
							} else {
								$skipFade = 1;
							}
						
						?>
						<select class = "wpvr_option_select pull-right " name = "wpvr_options_apiConnect" id = "wpvr_options_apiConnect">
							<option value = "advanced" <?php echo $isSelected[ 'advanced' ]; ?>>
								<?php _e( 'Use API Advanced Credentials' , WPVR_LANG ); ?>
							</option>
							<option value = "wizzard" <?php echo $isSelected[ 'wizzard' ]; ?>>
								<?php _e( 'Use API Wizard' , WPVR_LANG ); ?>
							</option>
						</select>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'API Connection' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc">
							<?php _e( 'You can choose whether to grant access to Video Services through the API Wizard,' , WPVR_LANG ); ?><br/>
							<?php _e( 'or use the crendentials defined below to connect to Video Services APIs.' , WPVR_LANG ); ?> <br/>
							<strong><?php _e( 'Important Notice' , WPVR_LANG ); ?></strong> :<br/>
							<?php _e( 'All API accesses granted manually through the API wizard, are revoked within 24 up to 48 hours by video services APIs.' , WPVR_LANG ); ?> <br/>
						</p>
					</div>
				</div>
				<!-- /USE ADVANCED CREDENTIALS -->
				
				
				<div class = "wpvr_option wpvr_api_advanced_credentials tab_f <?php echo $class; ?>">
					
					<div class = "tabFix advanced_wrap">
						
						<!-- youtube_apiKey -->
						<div class = "wpvr_option_inside  first_option tabFix">
							<div class = "pull-right align-right">
								<label for = "wpvr_options_apiKey">Youtube Api Key</label><br/>
								<input
									type = "text"
									class = "wpvr_options_input wpvr_large pull-right"
									id = "wpvr_options_apiKey"
									name = "wpvr_options_apiKey"
									value = "<?php echo $wpvr_options[ 'apiKey' ]; ?>"
								/>
								
								<div class = "wpvr_clearfix"></div>
							</div>
							<div>
								<span class = "wpvr_option_title"><?php _e( 'Youtube API Credentials' , WPVR_LANG ); ?></span><br/>
								
								<p class = "wpvr_option_desc">
									<?php _e( 'Enter your Youtube API Key to make the plugin work.' , WPVR_LANG ); ?>
									<br/><br/>
									
									<a class = "link" target = "_blank" href = "http://support.wpvideorobot.com.com/tutorials/where-to-find-youtube-api-key/" title = "Click here">
										<?php _e( 'WHERE TO FIND MY YOUTUBE API KEY' , WPVR_LANG ); ?>
									</a>
								</p>
							</div>
							<div class = "wpvr_clearfix"></div>
						</div>
						<!-- /youtube_apiKey -->
						
						
						<!-- vimeo_apiKey -->
						<div class = "wpvr_option_inside tabFix">
							<div class = "pull-right align-right">
								<label for = "wpvr_options_voClientId">Vimeo Client ID</label><br/>
								<input
									type = "text"
									class = "wpvr_options_input wpvr_large pull-right"
									id = "wpvr_options_voClientId"
									name = "wpvr_options_voClientId"
									value = "<?php echo $wpvr_options[ 'voClientId' ]; ?>"
								/>
								
								<div class = "wpvr_clearfix"></div>
								<label for = "wpvr_options_voClientSecret">Vimeo Client Secret</label><br/>
								<input
									type = "text"
									class = "wpvr_options_input wpvr_large pull-right"
									id = "wpvr_options_voClientSecret"
									name = "wpvr_options_voClientSecret"
									value = "<?php echo $wpvr_options[ 'voClientSecret' ]; ?>"
								/>
								
								<div class = "wpvr_clearfix"></div>
							
							</div>
							<div>
								<span class = "wpvr_option_title"><?php _e( 'Vimeo API Credentials' , WPVR_LANG ); ?></span><br/>
								
								<p class = "wpvr_option_desc">
									<?php _e( 'Enter your Vimeo Credentials to make the plugin work with Vimeo.' , WPVR_LANG ); ?>
									<br/><br/>
									<a class = "link" target = "_blank" href = "http://support.wpvideorobot.com.com/tutorials/where-to-find-vimeo-crendentials" title = "Click here">
										<?php _e( 'WHERE TO FIND MY VIMEO CREDENTIALS' , WPVR_LANG ); ?>
									</a>
								</p>
							</div>
							<div class = "wpvr_clearfix"></div>
						</div>
						<!-- /vimeo_apiKey -->
						
						<!-- dm_apiKey -->
						<div class = "wpvr_option_inside tabFix">
							<div class = "pull-right align-right">
								<label for = "wpvr_options_dmClientId">DailyMotion Client ID</label><br/>
								<input
									type = "text"
									class = "wpvr_options_input wpvr_large pull-right"
									id = "wpvr_options_dmClientId"
									name = "wpvr_options_dmClientId"
									value = "<?php echo $wpvr_options[ 'dmClientId' ]; ?>"
								/>
								
								<div class = "wpvr_clearfix"></div>
								<label for = "wpvr_options_dmClientSecret">DailyMotion Client Secret</label><br/>
								<input
									type = "text"
									class = "wpvr_options_input wpvr_large pull-right"
									id = "wpvr_options_dmClientSecret"
									name = "wpvr_options_dmClientSecret"
									value = "<?php echo $wpvr_options[ 'dmClientSecret' ]; ?>"
								/>
								
								<div class = "wpvr_clearfix"></div>
							</div>
							<div>
								<span class = "wpvr_option_title"><?php _e( 'DailyMotion API Credentials' , WPVR_LANG ); ?></span><br/>
								
								<p class = "wpvr_option_desc">
									<?php _e( 'Enter your DailyMotion Credentials to make the plugin work with DailyMotion.' , WPVR_LANG ); ?>
									<br/><br/>
									<a class = "link" target = "_blank" href = "http://support.wpvideorobot.com.com/tutorials/where-to-find-dailymotion-crendentials/" title = "Click here">
										<?php _e( 'WHERE TO FIND MY DAILYMOTION CREDENTIALS' , WPVR_LANG ); ?>
									</a>
								</p>
							</div>
							<div class = "wpvr_clearfix"></div>
						</div>
						<!-- /dm_apiKey -->
					</div>
					
					<div class = "tabFix wizard_wrap">
						<?php if( count( $wpvr_vs ) != 0 ) { ?>
							<?php foreach ( $wpvr_vs as $service ) { ?>
								<?php if( isset( $service[ 'disable_manual_authentication' ] ) && $service[ 'disable_manual_authentication' ] === TRUE ) {
									continue;
								} ?>
								<!-- access -->
								<?php $vs_access = $service[ 'validate_token' ](); ?>
								<?php $on = ( $vs_access === FALSE ) ? '' : 'on'; ?>
								<?php //d( $youku_access ); ?>
								<div class = "wpvr_grid_option <?php echo $on; ?>" service = "<?php echo $service[ 'id' ]; ?>">
									<div class = "wpvr_grid_option_head">
											<span class = "wpvr_option_title">
												<?php printf( __( '%s API Access' , WPVR_LANG ) , ucfirst( $service[ 'label' ] ) ); ?>
											</span><br/>
										
										<p class = "wpvr_option_desc">
											<?php printf( __( 'Grant Access to %s to use its official API.' , WPVR_LANG ) , ucfirst( $service[ 'label' ] ) ); ?>
											<br/><br/>
										</p>
									</div>
									<div class = "wpvr_grid_option_buttons">
										<div class = "wpvr_token_state <?php echo $on; ?>" service = "<?php echo $service[ 'id' ]; ?>">
											<div class = "is_false">
												<button class = "wpvr_grid_button wpvr_submit_button red wpvr_token_ok">
													<i class = "fa fa-thumbs-down"></i> &nbsp;
													<?php printf( __( '%s API is not connected' , WPVR_LANG ) , ucfirst( $service[ 'label' ] ) ); ?>
												</button>
												<?php
													$auth_url = wpvr_capi_build_query( WPVR_AUTH_URL , array(
														'key'           => WPVR_AUTH_KEY ,
														'service'       => $service[ 'id' ] ,
														'url_back'      => WPVR_OPTIONS_URL ,
														'url_back_args' => base64_encode( json_encode( array(
															'wpvr_wpload' => 1 ,
															'set_token'   => 1 ,
															'' ,
														) ) ) ,
														'list'          => WPVR_AUTH_CUSTOM_LIST ,
														'first_call'    => 1 ,
													
													) );
												?>
												<button
													service = "<?php echo $service[ 'id' ]; ?>"
													class = "wpvr_grid_button wpvr_button wpvr_get_token"
													local = "<?php echo urlencode( WPVR_OPTIONS_URL ); ?>"
													auth_url = "<?php echo $auth_url; ?>"
												>
													<?php printf( __( 'Grant Access To %s' , WPVR_LANG ) , ucfirst( $service[ 'label' ] ) ); ?>
													&nbsp;&nbsp;&nbsp; <i class = "fa fa-chevron-right"></i>
												</button>
											
											</div>
											<div class = "is_true">
												<button class = "wpvr_grid_button wpvr_submit_button green wpvr_token_ok">
													<i class = "fa fa-thumbs-up"></i> &nbsp;
													<?php printf( __( '%s API is connected' , WPVR_LANG ) , ucfirst( $service[ 'label' ] ) ); ?>
												</button>
												<button
													service = "<?php echo $service[ 'id' ]; ?>"
													url = "<?php echo( WPVR_OPTIONS_URL ); ?>"
													class = "wpvr_grid_button wpvr_submit_button cancel wpvr_remove_token wpvr_grey_button "
													local = "<?php echo urlencode( WPVR_OPTIONS_URL ); ?>">
													<i class = "fa fa-remove"></i>
													<?php _e( 'Cancel This Access' , WPVR_LANG ); ?>
												</button>
											</div>
										</div>
										<div class = "wpvr_clearfix"></div>
									</div>
									
									<div class = "wpvr_clearfix"></div>
								</div>
								<!-- /access -->
							<?php } ?>
						<?php } ?>
						<div class = "wpvr_clearfix"></div>
					</div>
				
				</div>
				
				
				<!-- showMenuOn -->
				<?php
					global $wpvr_roles;
					$option = array(
						'id'          => 'showMenuFor' ,
						'order'       => 10 ,
						'label'       => __( 'User roles with enabled WPVR links' , WPVR_LANG ) ,
						'maxItems'    => '255' ,
						'placeholder' => __( 'Pick one or more user roles.' , WPVR_LANG ) ,
						'values'      => $wpvr_roles[ 'available' ] ,
						'desc'        => __( 'Choose which user roles will have WPVR menu links enabled.' , WPVR_LANG ) ,
						'type'        => 'multiselect' ,
						'default'     => '' ,
						'tab_class'   => 'tab_a' ,
					);
					wpvr_addon_option_render( $option , $wpvr_options[ 'showMenuFor' ] );
				
				?>
				<!-- /showMenuOn -->
				
				
				<!-- showMenuOn -->
				<?php
					global $wpvr_roles;
					$option = array(
						'id'          => 'privateCPT' ,
						'order'       => 11 ,
						'label'       => __( 'Private Custom Post Types' , WPVR_LANG ) ,
						'maxItems'    => '255' ,
						'placeholder' => __( 'Pick one or more custom post type.' , WPVR_LANG ) ,
						'values'      => array() ,
						'source'      => 'post_types_ext' ,
						'desc'        => __( 'Choose which other custom post types the plugin should not conflict with.' , WPVR_LANG ) ,
						'type'        => 'multiselect' ,
						'tab_class'   => 'tab_d' ,
					);
					wpvr_addon_option_render( $option , $wpvr_options[ 'privateCPT' ] );
				
				?>
				<!-- /showMenuOn -->
				
				
				<!-- /GENEARAL OPTIONS -->
				
				
				<!-- /FETCHING & POSTING -->
				<!-- wantedVideos -->
				<div class = "wpvr_option tab_ap on">
					<?php
						if( ! defined( 'WPVR_MAX_WANTED_VIDEOS' ) || WPVR_MAX_WANTED_VIDEOS === FALSE ) {
							$wanted_video_limit = __( 'Unlimited' , WPVR_LANG );
							$max_value          = '';
						} else {
							$wanted_video_limit = __( 'Limited to' , WPVR_LANG ) . ' : ' . WPVR_MAX_WANTED_VIDEOS;
							$max_value          = WPVR_MAX_WANTED_VIDEOS;
						}
					?>
					
					<input
						type = "text"
						class = "wpvr_options_input pull-right"
						id = "wpvr_options_wantedVideos"
						name = "wpvr_options_wantedVideos"
						value = "<?php echo $wpvr_options[ 'wantedVideos' ]; ?>"
						max_value = "<?php echo $max_value; ?>"
					/>
					
					<div>
						<span class = "wpvr_option_title"><?php _e( 'Default number of videos fetched' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc">
							<?php _e( 'Number of videos to get by default per source.' , WPVR_LANG ); ?>
							
							(<i><?php echo $wanted_video_limit; ?></i>)
						</p>
					
					</div>
				</div>
				<!-- wantedVideos -->
				
				<!-- orderVideos -->
				<div class = "wpvr_option tab_ap on">
					<?php
						$isSelected                                   = array( 'title' => '' , 'relevance' => '' , 'date' => '' , 'viewCount' => '' );
						$isSelected[ $wpvr_options[ 'orderVideos' ] ] = ' selected="selected" ';
					?>
					<select class = "wpvr_option_select pull-right " name = "wpvr_options_orderVideos" id = "wpvr_options_orderVideos">
						<option value = "relevance" <?php echo $isSelected[ 'relevance' ]; ?>>
							<?php _e( 'Relevance' , WPVR_LANG ); ?>
						</option>
						<option value = "date" <?php echo $isSelected[ 'date' ]; ?>>
							<?php _e( 'Date' , WPVR_LANG ); ?>
						</option>
						<option value = "viewCount" <?php echo $isSelected[ 'viewCount' ]; ?>>
							<?php _e( 'Views' , WPVR_LANG ); ?>
						</option>
						<option value = "title" <?php echo $isSelected[ 'title' ]; ?>>
							<?php _e( 'Title' , WPVR_LANG ); ?>
						</option>
					</select>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Default Order criterion' , WPVR_LANG ); ?> </span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Default criterion for ordering fetched videos.' , WPVR_LANG ); ?> </p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /orderVideos -->
				
				<!-- getPostDate -->
				<div class = "wpvr_option tab_b on">
					<?php
						$isSelected                                   = array( 'original' => '' , 'new' => '' );
						$isSelected[ $wpvr_options[ 'getPostDate' ] ] = ' selected="selected" ';
					?>
					<select class = "wpvr_option_select pull-right " name = "wpvr_options_getPostDate" id = "wpvr_options_getPostDate">
						<option value = "original" <?php echo $isSelected[ 'original' ]; ?>>
							<?php _e( 'Use Original Post Date' , WPVR_LANG ); ?>
						</option>
						<option value = "new" <?php echo $isSelected[ 'new' ]; ?>>
							<?php _e( 'Use Import Date ' , WPVR_LANG ); ?>
						</option>
					
					</select>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Default Post Date' , WPVR_LANG ); ?> </span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Default post date for imported videos.' , WPVR_LANG ); ?> </p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /postDate -->
				
				<!-- getPostContent -->
				<div class = "wpvr_option tab_b on">
					<?php
						$isSelected = array( 'off' => '' , 'on' => '' );
						
						$isSelected[ $wpvr_options[ 'postContent' ] ] = ' selected="selected" ';
					?>
					<select class = "wpvr_option_select pull-right " name = "wpvr_options_postContent" id = "wpvr_options_postContent">
						<option value = "on" <?php echo $isSelected[ 'on' ]; ?>>
							<?php _e( 'Import & Post Video Text Content' , WPVR_LANG ); ?>
						</option>
						<option value = "off" <?php echo $isSelected[ 'off' ]; ?>>
							<?php _e( 'Skip Video Text Content' , WPVR_LANG ); ?>
						</option>
					</select>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Default Post Content' , WPVR_LANG ); ?> </span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Default post video text content for imported videos.' , WPVR_LANG ); ?> </p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /getPostContent -->
				
				<?php if( WPVR_ENABLE_POST_FORMATS ) { ?>
					<!-- postFormat -->
					<div class = "wpvr_option tab_b on">
						<?php
							// 0 aside image video audio quote link gallery
							$isSelected                                  = array(
								'0'       => '' ,
								'aside'   => '' ,
								'image'   => '' ,
								'video'   => '' ,
								'audio'   => '' ,
								'quote'   => '' ,
								'link'    => '' ,
								'gallery' => '' ,
							);
							$isSelected[ $wpvr_options[ 'postFormat' ] ] = ' selected="selected" ';
						?>
						<select class = "wpvr_option_select pull-right " name = "wpvr_options_postFormat" id = "wpvr_options_postFormat">
							<option value = "0" <?php echo $isSelected[ '0' ]; ?>>
								<?php _e( 'Standard' , WPVR_LANG ); ?>
							</option>
							<option value = "aside" <?php echo $isSelected[ 'aside' ]; ?>>
								<?php _e( 'Aside' , WPVR_LANG ); ?>
							</option>
							<option value = "image" <?php echo $isSelected[ 'image' ]; ?>>
								<?php _e( 'Image' , WPVR_LANG ); ?>
							</option>
							<option value = "video" <?php echo $isSelected[ 'video' ]; ?>>
								<?php _e( 'Video' , WPVR_LANG ); ?>
							</option>
							<option value = "audio" <?php echo $isSelected[ 'audio' ]; ?>>
								<?php _e( 'Audio' , WPVR_LANG ); ?>
							</option>
							<option value = "quote" <?php echo $isSelected[ 'quote' ]; ?>>
								<?php _e( 'Quote' , WPVR_LANG ); ?>
							</option>
							<option value = "link" <?php echo $isSelected[ 'link' ]; ?>>
								<?php _e( 'Link' , WPVR_LANG ); ?>
							</option>
							<option value = "gallery" <?php echo $isSelected[ 'gallery' ]; ?>>
								<?php _e( 'Gallery' , WPVR_LANG ); ?>
							</option>
						</select>
						
						<div class = "option_text">
							<span class = "wpvr_option_title"><?php _e( 'Default Post Format' , WPVR_LANG ); ?> </span><br/>
							
							<p class = "wpvr_option_desc"> <?php _e( 'Default post format to apply to all the imported videos.' , WPVR_LANG ); ?> </p>
						</div>
						<div class = "wpvr_clearfix"></div>
					</div>
					<!-- /postFormat -->
				<?php } ?>
				
				<!-- postTags -->
				<div class = "wpvr_option tab_b on">
					<input
						type = "text"
						class = "wpvr_options_input wpvr_large  pull-right"
						id = "wpvr_options_postTags"
						name = "wpvr_options_postTags"
						value = "<?php echo $wpvr_options[ 'postTags' ]; ?>"
					/>
					
					<div>
							<span class = "wpvr_option_title">
								<?php _e( 'Default Post Tags' , WPVR_LANG ); ?>
							</span><br/>
						
						<p class = "wpvr_option_desc">
							<?php _e( 'Tags to add automatically to imported videos.' , WPVR_LANG ); ?> <br/>
							<?php _e( 'Comma separated.' , WPVR_LANG ); ?>
						</p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /postTags -->
				
				
				<!-- postAuthor -->
				<div class = "wpvr_option tab_b on">
					<?php $authorsArray = wpvr_get_authors( $invert = TRUE , $default = FALSE , $restrict = FALSE ); ?>
					<?php //new dBug( $authorsArray );?>
					<select class = "wpvr_option_select pull-right " name = "wpvr_options_postAuthor" id = "wpvr_options_postAuthor">
						<?php foreach ( $authorsArray as $author_id => $author_name ) { ?>
							<?php if( $wpvr_options[ 'postAuthor' ] == $author_id ) {
								$authorSelected = ' selected = "selected" ';
							} else {
								$authorSelected = '';
							} ?>
							<option value = "<?php echo $author_id; ?>" <?php echo $authorSelected; ?>>
								<?php echo $author_name; ?>
							</option>
						<?php } ?>
					</select>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Default Posting Author' , WPVR_LANG ); ?> </span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Default Author for autoposting' , WPVR_LANG ); ?> </p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /postAuthor -->
				
				<!-- videoQuality -->
				<div class = "wpvr_option tab_ap on">
					<?php
						$qualities = array(
							'any'      => __( 'All Videos' , WPVR_LANG ) ,
							'high'     => __( 'Only High Definition Videos' , WPVR_LANG ) ,
							'standard' => __( 'Only Standard Definitions Videos' , WPVR_LANG ) ,
						);
					?>
					<select class = "wpvr_option_select pull-right " name = "wpvr_options_videoQuality" id = "wpvr_options_videoQuality">
						<?php foreach ( $qualities as $qValue => $qLabel ) { ?>
							<?php if( $wpvr_options[ 'videoQuality' ] == $qValue ) {
								$qSelected = ' selected = "selected" ';
							} else {
								$qSelected = '';
							} ?>
							<option value = "<?php echo $qValue; ?>" <?php echo $qSelected; ?>>
								<?php echo $qLabel; ?>
							</option>
						<?php } ?>
					</select>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Default Video Quality' , WPVR_LANG ); ?> </span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Choose what quality should sources filter by default.' , WPVR_LANG ); ?> </p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /videoQuality -->
				
				<!-- Default PublishedAfter -->
				<div class = "wpvr_option tab_ap on">
					<input
						type = "text"
						class = "wpvr_options_input wpvr_large  pull-right"
						id = "wpvr_options_publishedAfter"
						name = "wpvr_options_publishedAfter"
						placeholder = "Format : mm/dd/YYYY"
						value = "<?php echo $wpvr_options[ 'publishedAfter' ]; ?>"
					/>
					
					<div>
							<span class = "wpvr_option_title">
								<?php _e( 'Default Published After Date' , WPVR_LANG ); ?>
							</span><br/>
						
						<p class = "wpvr_option_desc">
							<?php echo '' . __( 'Import only videos published after this date.' , WPVR_LANG ) . ' ' .
							           __( 'Leave empty to ignore this criterion.' , WPVR_LANG ) .
							           '<br/><strong>' . __( 'Supported only by Youtube and Dailymotion.' , WPVR_LANG ) . '</strong>'; ?>
							<br/> Format : mm/dd/YYYY
						</p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /PublishedAfter -->
				
				<!-- Default PublishedBefore -->
				<div class = "wpvr_option tab_ap on">
					<input
						type = "text"
						class = "wpvr_options_input wpvr_large  pull-right"
						id = "wpvr_options_publishedBefore"
						name = "wpvr_options_publishedBefore"
						placeholder = "Format : mm/dd/YYYY"
						value = "<?php echo $wpvr_options[ 'publishedBefore' ]; ?>"
					/>
					
					<div>
							<span class = "wpvr_option_title">
								<?php _e( 'Default Published Before Date' , WPVR_LANG ); ?>
							</span><br/>
						
						<p class = "wpvr_option_desc">
							<?php echo '' . __( 'Import only videos published before this date.' , WPVR_LANG ) . ' ' .
							           __( 'Leave empty to ignore this criterion.' , WPVR_LANG ) .
							           '<br/><strong>' . __( 'Supported only by Youtube and Dailymotion.' , WPVR_LANG ) . '</strong>'; ?>
							<br/> Format : mm/dd/YYYY
						</p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /PublishedAfter -->
				
				<!-- videoDuration -->
				<div class = "wpvr_option tab_ap on">
					<?php
						$qualities = array(
							'any'    => __( 'All Videos' , WPVR_LANG ) ,
							'short'  => __( 'Videos less than 4min.' , WPVR_LANG ) ,
							'medium' => __( 'Videos between 4min. and 20min.' , WPVR_LANG ) ,
							'long'   => __( 'Videos longer than 20min.' , WPVR_LANG ) ,
						
						);
					?>
					<select class = "wpvr_option_select pull-right " name = "wpvr_options_videoDuration" id = "wpvr_options_videoDuration">
						<?php foreach ( $qualities as $qValue => $qLabel ) { ?>
							<?php if( $wpvr_options[ 'videoDuration' ] == $qValue ) {
								$qSelected = ' selected = "selected" ';
							} else {
								$qSelected = '';
							} ?>
							<option value = "<?php echo $qValue; ?>" <?php echo $qSelected; ?>>
								<?php echo $qLabel; ?>
							</option>
						<?php } ?>
					</select>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Default Video Duration' , WPVR_LANG ); ?> </span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Choose what duration should sources filter by default.' , WPVR_LANG ); ?> </p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- /videoDuration -->
				
				
				<!-- NoDuplicates -->
				<div class = "wpvr_option tab_ap <?php echo wpvr_get_button_state( $wpvr_options[ 'onlyNewVideos' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_onlyNewVideos' , $wpvr_options[ 'onlyNewVideos' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Skip Duplicates' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Enable this option to import only new videos. Duplicates will be skipped.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- /NoDuplicates -->
				
				<!-- GetStats -->
				<div class = "wpvr_option tab_ap <?php echo wpvr_get_button_state( $wpvr_options[ 'getStats' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_getStats' , $wpvr_options[ 'getStats' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Get Video Stats' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Grab Youtube views, duration and likes. You can improve performances by setting this option to off.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- /GetStats -->
				
				<!-- GetTags -->
				<div class = "wpvr_option tab_ap <?php echo wpvr_get_button_state( $wpvr_options[ 'getTags' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_getTags' , $wpvr_options[ 'getTags' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Get Video Tags' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Grab Official Youtube tags (meta keywords). You can improve performances by setting this option to off.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- /GetTags -->
				
				<!-- getFullDesc -->
				<div class = "wpvr_option tab_ap <?php echo wpvr_get_button_state( $wpvr_options[ 'getFullDesc' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_getFullDesc' , $wpvr_options[ 'getFullDesc' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Get Video Full Desc.' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Grab the video full description ? You can improve performances by setting this option to off.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- /getFullDesc -->
				
				
				<!-- autoPublish -->
				<div class = "wpvr_option tab_b <?php echo wpvr_get_button_state( $wpvr_options[ 'autoPublish' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_autoPublish' , $wpvr_options[ 'autoPublish' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Auto Publish ?' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Automatically publish imported videos. If you sety this option to off, imported videos will get pending status until you review them.' , WPVR_LANG ); ?> </p>
					</div>
				</div>
				<!-- /autoPublish -->
				
				<!-- randomize -->
				<div class = "wpvr_option tab_b <?php echo wpvr_get_button_state( $wpvr_options[ 'randomize' ] ); ?>">
					<div class = "wpvr_option_button pull-right" enabler = "randomize">
						<?php wpvr_make_switch_button( 'wpvr_options_randomize' , $wpvr_options[ 'randomize' ] ); ?>
					</div>
					<?php
						if( $wpvr_options[ 'randomize' ] ) {
							$select_state = "";
						} else {
							$select_state = " disabled ";
						}
						
						$isSelected                                     = array( 'empty' => '' , 'minute' => '' , 'hour' => '' , 'day' => '' );
						$isSelected[ $wpvr_options[ 'randomizeStep' ] ] = ' selected="selected" ';
					?>
					<select
						class = "wpvr_option_select pull-right  enabledBy_randomize"
						name = "wpvr_options_randomizeStep"
						id = "wpvr_options_randomizeStep"
						<?php echo $select_state; ?>
					>
						<option value = "empty" <?php echo $isSelected[ 'empty' ]; ?>> <?php _e( 'Random Step' , WPVR_LANG ); ?> </option>
						<option value = "minute" <?php echo $isSelected[ 'minute' ]; ?>> +/- <?php _e( 'One minute' , WPVR_LANG ); ?></option>
						<option value = "hour" <?php echo $isSelected[ 'hour' ]; ?>> +/- <?php _e( 'One hour' , WPVR_LANG ); ?> </option>
						<option value = "day" <?php echo $isSelected[ 'day' ]; ?>> +/- <?php _e( 'One day' , WPVR_LANG ); ?> </option>
					</select>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Randomize Posting Date' , WPVR_LANG ); ?> </span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Set a random post date for imported videos. You can choose random precision.' , WPVR_LANG ); ?> </p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<!-- randomize -->
				
				<!-- startWithServiceViews -->
				<div class = "wpvr_option tab_b <?php echo wpvr_get_button_state( $wpvr_options[ 'startWithServiceViews' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_startWithServiceViews' , $wpvr_options[ 'startWithServiceViews' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Start local views count with Video Service views count ?' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc">
							<?php _e( 'Enable this option to start your imported views count with the real video service views count.' , WPVR_LANG ); ?> <br/>
							<?php _e( 'If you disable this option, the local views count will start at 0.' , WPVR_LANG ); ?>
						</p>
					</div>
				</div>
				<!-- /startWithServiceViews -->
				

				<!-- /FETCHING & POSTING -->
				
				
				<!-- AUTOMATION -->
				<!-- Test Mode *** -->
				<?php $cron_url = wpvr_get_cron_url(); ?>
				<div class = "wpvr_option tab_c <?php echo wpvr_get_button_state( $wpvr_options[ 'autoRunMode' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_autoRunMode' , $wpvr_options[ 'autoRunMode' ] ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title">
							<?php _e( 'Enable AutoRun Mode' , WPVR_LANG ); ?>
						</span><br/>
						<p class = "wpvr_option_desc">
							<?php _e( 'Disable this option to stop the plugin from working in background.' , WPVR_LANG ); ?>
						</p>

						<div class = "wpvr_cond_div">
							<?php do_action('wpvr_autorun_option_description'); ?>
							<br/><strong>CRON URL</strong>

							<div class = "wpvr_code_url">
								<span class = "pull-left" id = "wpvr_code_url">
									<?php echo $cron_url; ?>
								</span>
								<?php wpvr_render_copy_button( 'wpvr_code_url' ); ?>
							</div>

							<br/><strong>Crontab line to add ( via URL )</strong>

							<div class = "wpvr_code_url">
								<span class = "pull-left" id = "wpvr_code_url_cron">
									<?php echo ' */10 * * * * wget -q -O /dev/null ' . $cron_url; ?>
								</span>
								<?php wpvr_render_copy_button( 'wpvr_code_url_cron' ); ?>
							</div>
							<br/><strong>Crontab line to add ( via PATH )</strong>

							<div class = "wpvr_code_url">
								<span class = "pull-left" id = "wpvr_code_url_cron_path">
									<?php echo ' */10 * * * * php -f ' . WPVR_CRON_PATH . ' ' . $wpvr_cron_token; ?>
								</span>
								<?php wpvr_render_copy_button( 'wpvr_code_url_cron_path' ); ?>
							</div>


							<br/>
							<a href = "http://support.wpvideorobot.com.com/how-to-configure-cron-on-wp-video-robot/"> <?php _e( 'Help on Cron Configuring' , WPVR_LANG ); ?> </a> |
							<a href = "https://store.wpvideorobot.com/addons/autopilot/" target = "_blank"> <?php _e( 'Discover AutoPilot' , WPVR_LANG ); ?> </a> |
							<a href = "http://support.wpvideorobot.com"><?php _e( 'Get Support' , WPVR_LANG ); ?></a>
						</div>

					</div>
				</div>
				<!-- /Test Mode -->
				
				<!-- useCronTab -->
				<?php if( FALSE ) { ?>
					<div class = "wpvr_option tab_c <?php echo wpvr_get_button_state( $wpvr_options[ 'useCronTab' ] ); ?>">
						<div class = "wpvr_option_button pull-right">
							<?php wpvr_make_switch_button( 'wpvr_options_useCronTab' , $wpvr_options[ 'useCronTab' ] ); ?>
						</div>
						<div>
							<span class = "wpvr_option_title"><?php _e( 'Use a Real Cron Service' , WPVR_LANG ); ?></span><br/>

							<div class = "wpvr_option_desc">

								<strong><?php _e( "If set to OFF" , "wpvr" ); ?> :</strong><br/>
								<?php _e( "WP Video Robot will use the cron service built right into WordPress. You don't have to set up anything else to get the plugin work, uou only have to visit your site (backend or frontend) regularly to trigger the cron." , WPVR_LANG ); ?>
								<br/>
								<br/>
								<strong><?php _e( "If set to ON" , "wpvr" ); ?> :</strong><br/>
								<?php _e( "WP Video Robot will use a real cron service. You have to configure a triggered call to the following URL each minute. For that you can use Unix Cron (available on most servers) or a free cron service website (crondash.com for example)." , WPVR_LANG ); ?>
							

							</div>
						</div>
						<div class = "wpvr_clearfix"></div>
					</div>
					<!-- /useWpCron -->
				<?php } ?>
				
				<!-- wakeUpHours -->
				<div class = "wpvr_option tab_c <?php echo wpvr_get_button_state( $wpvr_options[ 'wakeUpHours' ] ); ?>">
					<div class = "wpvr_option_button pull-right">
						<?php wpvr_make_switch_button( 'wpvr_options_wakeUpHours' , $wpvr_options[ 'wakeUpHours' ] ); ?>
					</div>
					<?php
						if( $wpvr_options[ 'wakeUpHours' ] ) {
							$select_state = "";
						} else {
							$select_state = " disabled ";
						}
						
						$isSelected                                    = array(
							'empty' => '' ,
							'00'    => '' ,
							'01'    => '' ,
							'02'    => '' ,
							'03'    => '' ,
							'04'    => '' ,
							'05'    => '' ,
							'06'    => '' ,
							'07'    => '' ,
							'08'    => '' ,
							'09'    => '' ,
							'10'    => '' ,
							'11'    => '' ,
							'12'    => '' ,
							'13'    => '' ,
							'14'    => '' ,
							'15'    => '' ,
							'16'    => '' ,
							'17'    => '' ,
							'18'    => '' ,
							'19'    => '' ,
							'20'    => '' ,
							'21'    => '' ,
							'22'    => '' ,
							'23'    => '' ,
						);
						$isSelected[ $wpvr_options[ 'wakeUpHoursB' ] ] = ' selected="selected" ';
					?>
					<select
						class = "wpvr_option_select pull-right  enabledBy_wakeUpHours wpvr_wh_updater"
						name = "wpvr_options_wakeUpHoursB"
						id = "wpvr_options_wakeUpHoursB"
						<?php echo $select_state; ?>
					>
						<option value = "" <?php echo $isSelected[ 'empty' ]; ?>> <?php _e( 'End Time' , WPVR_LANG ); ?></option>
						<option value = "00" <?php echo $isSelected[ '00' ]; ?>> 00H00</option>
						<option value = "01" <?php echo $isSelected[ '01' ]; ?>> 01H00</option>
						<option value = "02" <?php echo $isSelected[ '02' ]; ?>> 02H00</option>
						<option value = "03" <?php echo $isSelected[ '03' ]; ?>> 03H00</option>
						<option value = "04" <?php echo $isSelected[ '04' ]; ?>> 04H00</option>
						<option value = "05" <?php echo $isSelected[ '05' ]; ?>> 05H00</option>
						<option value = "06" <?php echo $isSelected[ '06' ]; ?>> 06H00</option>
						<option value = "07" <?php echo $isSelected[ '07' ]; ?>> 07H00</option>
						<option value = "08" <?php echo $isSelected[ '08' ]; ?>> 08H00</option>
						<option value = "09" <?php echo $isSelected[ '09' ]; ?>> 09H00</option>
						<option value = "10" <?php echo $isSelected[ '10' ]; ?>> 10H00</option>
						<option value = "11" <?php echo $isSelected[ '11' ]; ?>> 11H00</option>
						<option value = "12" <?php echo $isSelected[ '12' ]; ?>> 12H00</option>
						<option value = "13" <?php echo $isSelected[ '13' ]; ?>> 13H00</option>
						<option value = "14" <?php echo $isSelected[ '14' ]; ?>> 14H00</option>
						<option value = "15" <?php echo $isSelected[ '15' ]; ?>> 15H00</option>
						<option value = "16" <?php echo $isSelected[ '16' ]; ?>> 16H00</option>
						<option value = "17" <?php echo $isSelected[ '17' ]; ?>> 17H00</option>
						<option value = "18" <?php echo $isSelected[ '18' ]; ?>> 18H00</option>
						<option value = "19" <?php echo $isSelected[ '19' ]; ?>> 19H00</option>
						<option value = "20" <?php echo $isSelected[ '20' ]; ?>> 20H00</option>
						<option value = "21" <?php echo $isSelected[ '21' ]; ?>> 21H00</option>
						<option value = "22" <?php echo $isSelected[ '22' ]; ?>> 22H00</option>
						<option value = "23" <?php echo $isSelected[ '23' ]; ?>> 23H00</option>
					</select>
					
					
					<?php
						$isSelected                                    = array(
							'empty' => '' ,
							'00'    => '' ,
							'01'    => '' ,
							'02'    => '' ,
							'03'    => '' ,
							'04'    => '' ,
							'05'    => '' ,
							'06'    => '' ,
							'07'    => '' ,
							'08'    => '' ,
							'09'    => '' ,
							'10'    => '' ,
							'11'    => '' ,
							'12'    => '' ,
							'13'    => '' ,
							'14'    => '' ,
							'15'    => '' ,
							'16'    => '' ,
							'17'    => '' ,
							'18'    => '' ,
							'19'    => '' ,
							'20'    => '' ,
							'21'    => '' ,
							'22'    => '' ,
							'23'    => '' ,
						);
						$isSelected[ $wpvr_options[ 'wakeUpHoursA' ] ] = ' selected="selected" ';
					?>
					<select
						class = "wpvr_option_select pull-right  enabledBy_wakeUpHours wpvr_wh_updater"
						name = "wpvr_options_wakeUpHoursA"
						id = "wpvr_options_wakeUpHoursA"
						<?php echo $select_state; ?>
					>
						<option value = "" <?php echo $isSelected[ 'empty' ]; ?>> <?php _e( 'Start Time' , WPVR_LANG ); ?> </option>
						<option value = "00" <?php echo $isSelected[ '00' ]; ?>> 00H00</option>
						<option value = "01" <?php echo $isSelected[ '01' ]; ?>> 01H00</option>
						<option value = "02" <?php echo $isSelected[ '02' ]; ?>> 02H00</option>
						<option value = "03" <?php echo $isSelected[ '03' ]; ?>> 03H00</option>
						<option value = "04" <?php echo $isSelected[ '04' ]; ?>> 04H00</option>
						<option value = "05" <?php echo $isSelected[ '05' ]; ?>> 05H00</option>
						<option value = "06" <?php echo $isSelected[ '06' ]; ?>> 06H00</option>
						<option value = "07" <?php echo $isSelected[ '07' ]; ?>> 07H00</option>
						<option value = "08" <?php echo $isSelected[ '08' ]; ?>> 08H00</option>
						<option value = "09" <?php echo $isSelected[ '09' ]; ?>> 09H00</option>
						<option value = "10" <?php echo $isSelected[ '10' ]; ?>> 10H00</option>
						<option value = "11" <?php echo $isSelected[ '11' ]; ?>> 11H00</option>
						<option value = "12" <?php echo $isSelected[ '12' ]; ?>> 12H00</option>
						<option value = "13" <?php echo $isSelected[ '13' ]; ?>> 13H00</option>
						<option value = "14" <?php echo $isSelected[ '14' ]; ?>> 14H00</option>
						<option value = "15" <?php echo $isSelected[ '15' ]; ?>> 15H00</option>
						<option value = "16" <?php echo $isSelected[ '16' ]; ?>> 16H00</option>
						<option value = "17" <?php echo $isSelected[ '17' ]; ?>> 17H00</option>
						<option value = "18" <?php echo $isSelected[ '18' ]; ?>> 18H00</option>
						<option value = "19" <?php echo $isSelected[ '19' ]; ?>> 19H00</option>
						<option value = "20" <?php echo $isSelected[ '20' ]; ?>> 20H00</option>
						<option value = "21" <?php echo $isSelected[ '21' ]; ?>> 21H00</option>
						<option value = "22" <?php echo $isSelected[ '22' ]; ?>> 22H00</option>
						<option value = "23" <?php echo $isSelected[ '23' ]; ?>> 23H00</option>
					</select>
					
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php _e( 'Working Hours' , WPVR_LANG ); ?></span><br/>
						
						<p class = "wpvr_option_desc"> <?php _e( 'Define a time interval where WP Video Robot is authorized to perform its automatic tasks.' , WPVR_LANG ); ?></p>
					</div>
					<div class = "wpvr_clearfix"></div>
					<div
						class = "wpvr_wh_wrapper"
						url = "<?php echo WPVR_OPTIONS_URL; ?>"
						id = "wpvr_options_wakeUpHours_graph"
					>
						<?php $workingHours = wpvr_make_interval( $wpvr_options[ 'wakeUpHoursA' ] , $wpvr_options[ 'wakeUpHoursB' ] ); ?>
						<?php foreach ( $workingHours as $wh => $state ) { ?>
							<?php if( $state === TRUE ) { ?>
								<div title = "AUTORUN ON" class = "wpvr_wh is_working"><?php echo $wh . 'H'; ?></div>
							<?php } else { ?>
								<div title = "AUTORUN OFF" class = "wpvr_wh"><?php echo $wh . 'H'; ?></div>
							<?php } ?>
						<?php } ?>
						<div class = "wpvr_clearfix"></div>
					</div>
				
				</div>
				<!-- wakeUpHours -->
				
				<!-- /AUTOMATION -->
				
				<?php do_action( 'wpvr_screen_options_bottom' ); ?>
				
				<button id = "wpvr_save_options_bis" class = "pull-right actionBtn wpvr_submit_button wpvr_save_options">
					<i class = "wpvr_button_icon fa fa-save"></i><?php _e( 'Save options' , WPVR_LANG ); ?>
				</button>
				<div class = "result_options"></div>
				<button url = "<?php echo WPVR_OPTIONS_URL; ?>" id = "wpvr_reset_options" class = "pull-left wpvr_submit_button actionBtn wpvr_reset_default wpvr_black_button">
					<i class = "wpvr_button_icon fa fa-undo"></i><?php _e( 'Reset To Default options' , WPVR_LANG ); ?>
				</button>
				
				<button url = "<?php echo WPVR_OPTIONS_URL; ?>" id = "wpvr_export_options" class = "pull-left wpvr_submit_button actionBtn wpvr_export_options">
					<i class = "wpvr_button_icon fa fa-upload"></i><?php _e( 'Export options' , WPVR_LANG ); ?>
				</button>
				
				<?php if( FALSE ) { ?>
					<a target = "_blank" href = "<?php echo $wpvr_hard_refresh_url; ?>" id = "" class = "pull-left wpvr_submit_button actionBtn">
						<i class = "wpvr_button_icon fa fa-refresh"></i>
						<?php _e( 'Hard Refresh Tables' , WPVR_LANG ); ?>
					</a>
				<?php } ?>
				
				<div class = "wpvr_clearfix"></div>
			
			
			</div>
			<!-- /run_options -->
		</div>
		<!-- /wpvr_navcontents -->
		<div class = "wpvr_diagnostic"></div>
	
	
	</form>
	<div id = "wpvr_export" src = "" style = "display:none; visibility:hidden;"></div>
</div><!-- /bootStyled -->