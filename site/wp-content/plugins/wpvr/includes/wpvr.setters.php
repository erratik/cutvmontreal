<?php

	/* Require Ajax WP load */
	if(isset($_GET['wpvr_wpload']) || isset($_POST['wpvr_wpload'])){
		define('DOING_AJAX', true );
		//define('WP_ADMIN', true );
		$wpload = 'wp-load.php' ;
		while( !is_file( $wpload ) ) {
			if( is_dir( '..' ) ) chdir( '..' );
			else die( 'EN: Could not find WordPress! FR : Impossible de trouver WordPress !' );
		}
		require_once( $wpload );
	}
	
	global $wpvr_pages, $wpvr_setters;
	$wpvr_pages = true ;
	
	
	if( isset( $_GET['reset_video_tables'] ) ){
		global $wpvr_imported;
		
		update_option( 'wpvr_deferred' , array() );
		update_option( 'wpvr_deferred_ids' , array() );
		update_option( 'wpvr_imported' , array() );
		
		$imported = wpvr_update_imported_videos();
		$wpvr_imported = get_option('wpvr_imported');
		echo wpvr_get_json_response( 'ok' );
		return false;
	}
	
	if( isset( $_GET['clear_deferred'] ) ){
		update_option('wpvr_deferred',array());
		echo wpvr_get_json_response( 'ok' );
		return false;
	}
	if( isset( $_GET['reset_cron_token'] ) ){
		update_option('wpvr_cron_token', '' );
		echo wpvr_get_json_response( 'ok' );
		return false;
	}

	if( isset( $_GET['reset_wpvr_tokens'] ) ){
		update_option('wpvr_tokens', '' );
		echo wpvr_get_json_response( 'ok' );
		return false;
	}

    if( isset( $_GET['reset_cron_data'] ) ){
        file_put_contents(WPVR_CRON_FILE_PATH , '');
        echo wpvr_get_json_response( 'ok' );
        return false;
    }

	if( isset( $_GET['clear_errors'] ) ){
		update_option('wpvr_errors' , array() );
		echo wpvr_get_json_response( 'ok' );
		return false;
	}
	
	if( isset( $_GET['reset_notices'] ) ){
		update_option('wpvr_notices' , array() );
		echo wpvr_get_json_response( 'ok' );
		return false;
	}
	
	if( isset( $_GET['show_errors'] ) ){
		$wpvr_errors = get_option('wpvr_errors');
		if( $wpvr_errors == null || $wpvr_errors == '' || count($wpvr_errors ) == 0 ){
			echo "There is no error.";
		}else{
			new dBug( $wpvr_errors );
		}
		//echo wpvr_get_json_response( 'ok' );
		return false;
	}
	
	if( isset( $_GET['remove_tmp'] ) ){
		wpvr_remove_tmp_files();
		echo wpvr_get_json_response( 'ok' );
		return false;
	}
	
	if( isset( $_GET['reset_activation'] ) ){
		update_option('wpvr_activation' , '');
		echo wpvr_get_json_response( 'ok' );
		return false;
	}
	
	
	/***********************************/
	
	
	$setters = array(
		'left' => array(),
		'right' => array(),
	);
	$i = 1 ;
	foreach( (array) $wpvr_setters as $setter ){
		$setter['id'] = $i ;
		
		if( !isset( $setter['show_result'] ) ) $setter['show_result'] = 0;
		
		if($i%2 == 0) $setters['right'][] = $setter ;
		else $setters['left'][] = $setter ;
		$i++;
	}
	
	//new dBug( $setters );
?>

	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<?php //new dBug( $wpvr_setters ); ?>
			
			<div id="postbox-container-1" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<?php foreach( (array) $setters['left'] as $setter) { ?>
						<div id="dashboard_right_now" class="postbox ">
							<h3 class="hndle"><span><?php echo $setter['title']; ?></span></h3>
							<div class="inside">
								<div class="main">
									<p><?php echo $setter['desc']; ?></p>	
									<br/><br/>
									<button 
										url="<?php echo WPVR_SETTERS_URL; ?>" 
										action="<?php echo $setter['action']; ?>" 
										id="<?php echo $setter['id']; ?>" 
										class="pull-right wpvr_submit_button wpvr_large wpvr_setter_button"
										is_demo="<?php echo WPVR_IS_DEMO ? 1 : 0 ; ?>"
										show_result="<?php echo $setter['show_result']; ?>"
									>
										<i class="wpvr_button_icon fa fa-bolt"></i>
										<?php echo $setter['button']; ?>
									</button>
									<div class="wpvr_clearfix"></div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			
			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<?php foreach( (array) $setters['right'] as $setter) { ?>
						<div id="dashboard_right_now" class="postbox ">
							<h3 class="hndle"><span><?php echo $setter['title']; ?></span></h3>
							<div class="inside">
								<div class="main">
									<p><?php echo $setter['desc']; ?></p>	
									<br/><br/>
									<button 
										url="<?php echo WPVR_SETTERS_URL; ?>" 
										action="<?php echo $setter['action']; ?>" 
										id="<?php echo $setter['id']; ?>" 
										class="pull-right wpvr_submit_button wpvr_large wpvr_setter_button"
										is_demo="<?php echo WPVR_IS_DEMO ? 1 : 0 ; ?>"
										show_result="<?php echo $setter['show_result']; ?>"
									>
										<i class="wpvr_button_icon fa fa-bolt"></i>
										<?php echo $setter['button']; ?>
									</button>
									<div class="wpvr_clearfix"></div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			
			
		</div>
	</div>
