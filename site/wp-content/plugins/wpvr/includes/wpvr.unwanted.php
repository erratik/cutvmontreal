<?php
	global $wpvr_unwanted , $wpvr_unwanted_ids , $wpvr_vs;
	//$wpvr_unwanted = $wpvr_deferred ;
	global $wpvr_pages;
	$wpvr_pages = TRUE;


	// Paging Prepare
	if( isset( $_GET[ 'p' ] ) ) {
		if( null !== ( $p_get = filter_input( INPUT_GET , 'p' , FILTER_VALIDATE_INT , FILTER_NULL_ON_FAILURE ) ) ) {
			$current_page = $p_get;
		} else $current_page = 1;
	} else $current_page = 1;

	if( isset( $_GET[ 'service' ] ) ) {
		$service = $_GET[ 'service' ];
	} else $service = 'all';
	
	
	$perpage = ( WPVR_UNWANTED_PERPAGE == 0 ) ? 1 : WPVR_UNWANTED_PERPAGE;
	$start   = $perpage * ( $current_page - 1 );
	$end     = $start + $perpage - 1;
	$total   = count( $wpvr_unwanted );
	
	$paging = array(
		'service' => $service ,
		'total'   => $total ,
		'pages'   => ceil( $total / $perpage ) ,
		'page'    => $current_page ,
		'start'   => $start ,
		'end'     => min( $end , $total - 1 ) ,
		'suffix'  => __( 'unwanted video(s) being skipped.' , WPVR_LANG ) ,
	);
	
	//d( $paging );
	
	$url = admin_url( 'admin.php?page=wpvr-unwanted' );

?>
<div class = "wrap wpvr_wrap" style = "display:none;">
	<?php wpvr_show_logo(); ?>
	<h2 class = "wpvr_title">
		<i class = "wpvr_title_icon fa fa-ban"></i>
		<?php echo __( 'Unwanted Videos' , WPVR_LANG ); ?>
	</h2>
	
	<div>
		<?php if( $paging[ 'total' ] == 0 ) { ?>
			<div class = "wpvr_nothing">
				<i class = "fa fa-frown-o"></i><br/>
				<?php _e( 'There is no unwanted video.' , WPVR_LANG ); ?>
			</div>
		<?php } else { ?>
			<div id = "message" class = "updated ">
				<div class = "wpvr_log_resume ">
					<div class = "wpvr_paging_text pull-left">
						<?php if( $paging[ 'total' ] == 0 ) { ?>
							<?php _e( 'There is no unwanted video.' , WPVR_LANG ); ?>
						<?php } else { ?>
							<strong><?php echo( $paging[ 'start' ] + 1 ); ?></strong> -
							<strong><?php echo( $paging[ 'end' ] + 1 ); ?></strong> on
							<strong><?php echo $paging[ 'total' ]; ?></strong> <?php echo $paging[ 'suffix' ]; ?>
						<?php } ?>
					</div>

					<div class = "wpvr_paging_select pull-right">
						<span> Page : </span>
						<select url = "<?php echo $url; ?>" class = "wpvr_select_page">
							<?php for( $i = 1; $i <= $paging[ 'pages' ]; $i++ ) { ?>
								<?php $sel = ( $paging[ 'page' ] == $i ) ? ' selected = "selected" ' : ''; ?>
								<option value = "<?php echo $i; ?>" <?php echo $sel; ?>>
									<?php echo $i; ?> on <?php echo $paging[ 'pages' ]; ?>
								</option>
							<?php } ?>
						</select>
					</div>
					<?php //d( $wpvr_vs ); ?>

					<div class = "wpvr_service_select pull-right">
						<span> Service : </span>
						<select url = "<?php echo $url; ?>" class = "wpvr_select_service">
							<option value="all">
								-- <?php echo __('All services' , WPVR_LANG ); ?>
							</option>
							<?php foreach( (array) $wpvr_vs as $vs ) { ?>
								<?php $sel = ( $paging[ 'service' ] == $vs['id'] ) ? ' selected = "selected" ' : ''; ?>
								<option value = "<?php echo $vs['id']; ?>" <?php echo $sel; ?>>
									<?php echo $vs['label']; ?>
								</option>
							<?php } ?>
						</select>
					</div>

					<div class = "wpvr_clearfix"></div>

				</div>
			</div>
			<div class = "wpvr_nothing" style = "display:none;">
				<?php _e( 'There is no unwanted video.' , WPVR_LANG ); ?>
			</div>
			<form id = "wpvr_test_form" class="wpvr_test_screen_wrap" url = "<?php echo WPVR_ACTIONS_URL; ?>" action = "test_remove_unwanted_videos">
			<div class = "wpvr_test_form_buttons top">
				<div class = "wpvr_button pull-left wpvr_test_form_toggleAll" state = "off">
					<i class = "wpvr_button_icon fa fa-check-square-o"></i>
					<?php _e( 'CHECK ALL VIDEOS' , WPVR_LANG ); ?>
				</div>
				<div class = "wpvr_button pull-left" id = "wpvr_test_form_refresh">
					<i class = "wpvr_button_icon fa fa-refresh"></i>
					<?php _e( 'REFRESH' , WPVR_LANG ); ?>
				</div>

				<button
					class = "wpvr_button wpvr_red_button pull-right wpvr_test_form_remove"
					id = "remove_unwanted"
					is_unwanted = "1"
				>
					<i class = "wpvr_button_icon fa fa-remove"></i>
					<?php _e( 'REMOVE SELECTED' , WPVR_LANG ); ?>
				</button>

			</div>
			<div class = "wpvr_clearfix"></div>
			<br/>

			<div class = "wpvr_unwanted_videos wpvr_videos">
				<div class = "wpvr_source_items" id = "">
					<?php //$wpvr_unwanted = json_decode($wpvr_unwanted); ?>

					<?php if( $paging[ 'total' ] == 0 ) { ?>
						<div class = "wpvr_nothing">
							<i class = "fa fa-frown-o"></i><br/>
							<?php _e( 'There is no unwanted video.' , WPVR_LANG ); ?>
						</div><br/><br/><br/>
					<?php } ?>

					<?php $i = 0; ?>

					<?php foreach( (array)$wpvr_unwanted as $video ) { ?>
						<?php
						//d( $i );
						if( $i < $paging[ 'start' ] ) {
							$i++;
							continue;
						}

						if( $paging['service'] != 'all' && $video['service'] != $paging['service'] ) {
							$i++;
							continue;
						}

						if( $i > $paging[ 'end' ] ) break;


						$i++;

						if( !isset( $wpvr_vs[ $video[ 'service' ] ] ) ) $vs_label = $video['service'];
						else $vs_label = $wpvr_vs[ $video[ 'service' ] ]['label'] ;

						?>
						<div class = "wpvr_video pull-left" id = "video_<?php echo $i; ?>">
							<input type = "checkbox" class = "wpvr_video_cb" name = "<?php echo $video[ 'id' ]; ?>" div_id = "<?php echo $i; ?>"/>

							<div class = "wpvr_video_head">
								<div class = "wpvr_video_adding">
									<i class = "fa fa-refresh fa-spin"></i>
								</div>
								<div class = "wpvr_video_checked">
									<i class = "fa fa-check"></i>
								</div>
								<div class = "wpvr_video_added">
									<i class = "fa fa-thumbs-up"></i>
								</div>

								<div class = "wpvr_service_icon <?php echo $video[ 'service' ]; ?> wpvr_video_service ">
									<?php echo strtoupper( $vs_label ); ?>
								</div>

								<div class = "wpvr_video_duration wpvr_video_unwanted">
									<i class = "fa fa-ban"></i>
									<?php echo __( 'UNWANTED' , WPVR_LANG ); ?>
								</div>
								<div class = "wpvr_video_thumb <?php echo $video[ 'service' ]; ?>">
									<img class = "wpvr_video_thumb" src = "<?php echo $video[ 'thumb' ]; ?>"/>
								</div>
							</div>
							<div class = "wpvr_video_title"><?php echo $video[ 'title' ]; ?></div>
						</div>
					<?php } ?>
					<div class = "wpvr_clearfix"></div>
				</div>
			</div>
			<div class = "wpvr_test_form_buttons bottom">
				<div class = "wpvr_button pull-left wpvr_test_form_toggleAll" state = "off">
					<i class = "wpvr_button_icon fa fa-check-square-o"></i>
					<?php _e( 'CHECK ALL VIDEOS' , WPVR_LANG ); ?>
				</div>
				<div class = "wpvr_button pull-left" id = "wpvr_test_form_refresh">
					<i class = "wpvr_button_icon fa fa-refresh"></i>
					<?php _e( 'REFRESH' , WPVR_LANG ); ?>
				</div>
				<div class = "wpvr_button  pull-left wpvr_goToTop">
					<i class = "wpvr_button_icon fa fa-arrow-up"></i>
					<?php echo __( 'To Top' , WPVR_LANG ); ?>
				</div>


				<button
					class = "wpvr_button wpvr_red_button pull-right wpvr_test_form_remove"
					id = "remove_unwanted"
					is_unwanted = "1"
				>
					<i class = "wpvr_button_icon fa fa-remove"></i>
					<?php _e( 'REMOVE SELECTED' , WPVR_LANG ); ?>
				</button>

			</div>
			<div class = "wpvr_clearfix"></div>
		</form>
		<?php } ?>
	</div>
	<div class = "wpvr_clearfix"></div>
</div>