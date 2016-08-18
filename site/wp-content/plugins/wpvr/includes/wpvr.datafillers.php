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
		@require_once( $wpload );
	}
		
		
	//global $wpvr_options , $wpvr_default_options , $wpvr_token;	
	global $wpvr_filler_data , $wpvr_datafillers_presets;
	
	if( isset($_GET['delete_all_fillers']) ){
		
		update_option('wpvr_fillers', '' );
		echo wpvr_get_json_response( 'done' );
	
		return false;
	}
	
	if( isset($_GET['add_fillers_from_preset']) ){
		$wpvr_fillers = get_option('wpvr_fillers');
		if( $wpvr_fillers == '') $wpvr_fillers = array();
		$preset = $_GET['preset'] ;
		$preset_items = $wpvr_datafillers_presets[ $preset ][ 'items' ];
		foreach( (array) $preset_items as $filler ){
			if( $filler['from'] == 'custom_data' ){
				$wpvr_fillers[] = array(
					'from' => 'custom_data',
					'from_custom' => trim($filler['from_custom']),
					'to' => trim($filler['to']),
				);
			
			}else{
				$wpvr_fillers[] = array(
					'from' => trim($filler['from']),
					'to' => trim($filler['to']),
				);
			}
		}
		//new dBug( $preset_items );
		
		update_option('wpvr_fillers', $wpvr_fillers );
		echo wpvr_get_json_response( 'done' );
	
		return false;
	}
	if( isset($_GET['run_fillers']) ){
		$r = array(
			'found' => 0,
			'processed' => 0,
			'errors' => 0,
		);
		$wpvr_fillers = get_option('wpvr_fillers');
		global $wpdb ;
		$sql = "
			select 
				P.ID
			from
				$wpdb->posts P 
			where
				P.post_type = '".WPVR_VIDEO_TYPE."'	
				
		";
		$videos = $wpdb->get_results( $sql );
		
		//print_r( $videos );
		
		$r['found'] = count($videos) ;
		if( count($videos) != 0 ){
			foreach( (array) $videos as $video ){

				$video_id = $video->ID ;
				if( is_array($wpvr_fillers) && count( $wpvr_fillers) > 0 ){
					foreach( (array) $wpvr_fillers as $filler ){

						//Get Data to fill With
						if( $filler['from'] == 'wpvr_video_embed_code' ) {
							//Getting Embed Code
							$wpvr_video_id = get_post_meta( $video_id, 'wpvr_video_id', true );
							$wpvr_service = get_post_meta( $video_id, 'wpvr_video_service', true );
							$data = '<div class="wpvr_embed">'.wpvr_video_embed($wpvr_video_id , $autoPlay = false , $wpvr_service).'</div>';
						}elseif( $filler['from'] == 'custom_data' ) {
							$data = $filler['from_custom'] ;
						}elseif( $filler['from'] == 'wpvr_video_service_url_https' ) {
							$data = str_replace('http://','https://', get_post_meta( $video_id, 'wpvr_video_service_url', true ) );

						}elseif( $filler['from'] == 'wpvr_video_service_duration' ) {
							//Getting String Duration
							$wpvr_video_duration = get_post_meta( $video_id, 'wpvr_video_duration', true );
							$data = wpvr_get_duration_string( $wpvr_video_duration  );
						}else {
							$data = get_post_meta( $video_id , $filler['from'] , TRUE );
						}

						if( $filler['from'] != 'wpvr_dynamic_views' ) {
							//Fill The Custom Fields
							$ok = update_post_meta( $video_id , $filler['to'] , $data );
						}else $ok = true ;

						if( $ok === false ) $r['errors']++;
						else $r['processed']++;
					}
				}
				do_action( 'wpvr_event_run_dataFillers' , $video_id );
				
			}
		}
		//echo json_encode( $r ) ;
		//echo WPVR_JS . json_encode( $r ) . WPVR_JS ;
		echo wpvr_get_json_response( $r );
		return false;
	}
	if( isset($_GET['remove_filler']) ){
		$wpvr_fillers = get_option('wpvr_fillers');
		
		unset( $wpvr_fillers[ $_GET['k'] ] );
		update_option('wpvr_fillers', $wpvr_fillers );
		echo wpvr_get_json_response( 'done' );
		return false;
	}
	if( isset($_GET['add_filler']) ){
		$wpvr_fillers = get_option('wpvr_fillers');
		if( $wpvr_fillers == '') $wpvr_fillers = array();
		if( $_POST['filler_from'] == 'custom_data' ){
			$wpvr_fillers[] = array(
				'from' => 'custom_data',
				'from_custom' => trim($_POST['filler_from_custom']),
				'to' => trim($_POST['filler_to']),
			);
		}else{
			$wpvr_fillers[] = array(
				'from' => trim($_POST['filler_from']),
				'to' => trim($_POST['filler_to']),
			);
		}
		
		update_option('wpvr_fillers' , $wpvr_fillers);
		echo wpvr_get_json_response( 'done' );
		return false;
	}
	if( isset($_GET['show_fillers']) ){
		ob_start();
		$wpvr_fillers = get_option('wpvr_fillers');
		if( $wpvr_fillers == '' || count($wpvr_fillers) == 0) {
			?>
				<div class="wpvr_manage_noResults">
					<i class="fa fa-frown-o"></i><br />
					<?php echo __('There is no fillers to show.', WPVR_LANG ); ?>
				</div>
			
			<?php
			
			$output = ob_get_contents();
			ob_end_clean();
			
			echo wpvr_get_json_response( $output , 0 , '' , 0 );
			return false;
		}
		?>
		<div class="wpvr_filler_actions">
		
			<button 
				type="button" 
				id="wpvr_filler_run" 
				class="wpvr_button pull-right"
				url = "<?php echo WPVR_FILLERS_URL; ?>"
			>
				<i class="fa fa-bolt"></i>
				<?php _e('RUN FILLERS ON EXISTANT VIDEOS', WPVR_LANG ); ?>									
			</button>
			
			<button 
				type="button" 
				id="wpvr_filler_delete_all" 
				class="wpvr_button wpvr_black_button pull-left"
				url = "<?php echo WPVR_FILLERS_URL; ?>"
				is_demo="<?php echo WPVR_IS_DEMO ? 1 : 0 ; ?>"
			>
				<i class="fa fa-close"></i>
				<?php _e('DELETE ALL FILLERS', WPVR_LANG ); ?>									
			</button>
			<div class="wpvr_clearfix"></div><br/>
		</div>		
			
			
		<?php
		$countFillers = 0;
		foreach( (array) $wpvr_fillers as $k=>$filler){
			$countFillers++;
			if( $filler['from'] == 'custom_data' ) $from = '"'.$filler['from_custom'].'"';
			else $from = $wpvr_filler_data[ $filler['from'] ];
			?>
			<li class="filler">
				<div class="pull-left">
					<?php echo $from ?>
					<i class="fa fa-long-arrow-right"></i>
					<?php echo $filler['to']; ?>
				</div>
				
				
				<button 
					type="button" 
					id="" 
					class="wpvr_button wpvr_red_button pull-right wpvr_filler_remove"
					title="Remove this filler"
					url = "<?php echo WPVR_FILLERS_URL; ?>"
					k = "<?php echo $k ; ?>"
				>
					<i class="fa fa-remove"></i>
				</button>
				<div class="wpvr_clearfix"></div>
			</li>
			
			<?php
		}
		?> <div class="wpvr_clearfix"></div> <?php
		
		$output = ob_get_contents();
		ob_end_clean();
		echo wpvr_get_json_response( $output , 1 , '' , $countFillers );
		
		return false;
	}
	
	
	//$wpvr_fillers = get_option('wpvr_fillers');	new dBug( $wpvr_fillers );
	
	
?>


	<div id="dashboard-widgets" class="metabox-holder">
		<div id="postbox-container-1" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<!-- Add from Presets -->
				<div id="dashboard_right_now" class="postbox ">
					<h3 class="hndle"><span> <?php _e('Add from Presets', WPVR_LANG ); ?></span></h3>
					<div class="inside">
						<div class="main">
							<label for="filler_from"><?php _e('DataFiller Preset', WPVR_LANG ); ?></label><br/>
							<select class="wpvr_filler_input" name="filler_preset" id="filler_preset">
								<option value=""> - <?php _e('Choose a preset', WPVR_LANG ); ?> - </option>
								<?php foreach( (array) $wpvr_datafillers_presets as $preset ){ ?>
									<option value="<?php echo $preset['id']; ?>">
										<?php echo $preset['label']; ?>
									</option>
								<?php } ?>
							</select>
							
							<br/><br/>
							<div class="wpvr_clearfix"></div>
							<button 
								id="wpvr_filler_add_from_preset"
								class="pull-right wpvr_button"
								url = "<?php echo WPVR_FILLERS_URL; ?>"
								
							>
								<i class="fa fa-plus"></i>
								<?php _e('ADD FILLERS FROM PRESET', WPVR_LANG ); ?>										
							</button>
							<div class="wpvr_clearfix"></div>
							
							
						</div>
					</div>
				</div>
				
				
				<!-- Add Manually -->
				<div id="dashboard_right_now" class="postbox ">
					<h3 class="hndle"><span> <?php _e('Add a new Filler', WPVR_LANG ); ?></span></h3>
					<div class="inside">
						<div class="main">
							
							<form class="wpvr_filler_form">
								<label for="filler_from"><?php _e('Video Data to add', WPVR_LANG ); ?></label><br/>
								<select class="wpvr_filler_input" name="filler_from">
									<option value=""> - <?php _e('Choose a data', WPVR_LANG ); ?> - </option>
									<option value="wpvr_video_id">
										<?php _e('Video ID', WPVR_LANG ); ?>
									</option>
									<option value="wpvr_video_service">
										<?php _e('Video Service', WPVR_LANG ); ?>
									</option>
									
									<option value="wpvr_video_embed_code">
										<?php _e('Video Embed Code', WPVR_LANG ); ?>
									</option>
									
									<option value="wpvr_video_service_url">
										<?php _e('Video URL', WPVR_LANG ); ?>
									</option>
									<option value="wpvr_video_service_url_https">
										<?php _e('Video URL (https)', WPVR_LANG ); ?>
									</option>
									<option value="wpvr_video_service_duration">
										<?php _e('Video Duration', WPVR_LANG ); ?>
									</option>
									<option value="wpvr_video_service_thumb">
										<?php _e('Video Thumbnail', WPVR_LANG ); ?>
									</option>
									<option value="wpvr_video_service_views">
										<?php _e('Video Original Views', WPVR_LANG ); ?>
									</option>
									<option value="wpvr_dynamic_views">
										<?php _e('Dynamic Video Views', WPVR_LANG ); ?>
									</option>
									<option value="custom_data">
										<?php _e('Custom Data', WPVR_LANG ); ?>
									</option>
								</select><br/>
								<input 
									class="wpvr_filler_input" 
									id="filler_from_custom" 
									name="filler_from_custom" 
									type="text" 
									placeholder="<?php _e('Custom String', WPVR_LANG ); ?>"
									style="display:none;"
								/>
								<br/><br/>
								
								<label for="filler_to"><?php _e('Custom Field name to populate', WPVR_LANG ); ?></label><br/>
								<input class="wpvr_filler_input" name="filler_to" type="text" placeholder="<?php _e('Custom Field Name', WPVR_LANG ); ?>">
								<br/><br/>
								<div class="wpvr_clearfix"></div>
								<button 
									id="wpvr_filler_add"
									class="pull-right wpvr_button"
									url = "<?php echo WPVR_FILLERS_URL; ?>"
									form = "wpvr_filler_form"
								>
									<i class="wpvr_button_icon fa fa-plus"></i>
									<?php _e('ADD SINGLE FILLER', WPVR_LANG ); ?>										
								</button>
								<div class="wpvr_clearfix"></div>
								
								
							</form>
							
						</div>
					</div>
				</div>
				
			</div>
		</div>
		<div id="postbox-container-2" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				
				
				<div id="dashboard_right_now" class="postbox ">
					<h3 class="hndle"><span> Fillers </span></h3>
					<div class="inside">
						<div class="main">
							<div id="wpvr_filler_list"> LIST </div>
						</div>
					</div>
				</div>
				
				
			</div>
		</div>
	</div>