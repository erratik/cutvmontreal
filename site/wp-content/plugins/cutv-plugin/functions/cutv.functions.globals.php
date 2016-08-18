<?php
	

	/* Show Home dashboard Stats */
	if( ! function_exists( 'cutv_custom_dashboard_function' ) ) {
		function cutv_custom_dashboard_function() {
			global $cutv_colors , $cutv_services , $cutv_types_ , $cutv_status;
			$sources_stats = cutv_sources_stats();
			$videos_stats  = cutv_videos_stats();
			$cutv_deferred = get_option( 'cutv_deferred' );
			if( $cutv_deferred == '' ) $videos_deferred = 0;
			else $videos_deferred = count( $cutv_deferred );

			$newVideoLink  = CUTV_SITE_URL . '/wp-admin/post-new.php?post_type=' . CUTV_VIDEO_TYPE;
			$newSourceLink = CUTV_SITE_URL . '/wp-admin/post-new.php?post_type=' . CUTV_SOURCE_TYPE;
			$dashboardLink = CUTV_SITE_URL . '/wp-admin/admin.php?page=cutv';
			$deferredLink  = CUTV_SITE_URL . '/wp-admin/admin.php?page=cutv-deferred';
			$unwantedLink  = CUTV_SITE_URL . '/wp-admin/admin.php?page=cutv-unwanted';
			$reviewLink    = CUTV_SITE_URL . '/wp-admin/edit.php?post_status=pending&post_type=' . CUTV_VIDEO_TYPE;
			$optionsLink   = CUTV_SITE_URL . '/wp-admin/admin.php?page=cutv-options';
			$manageLink    = CUTV_SITE_URL . '/wp-admin/admin.php?page=cutv-manage';
			$addonsLink    = CUTV_SITE_URL . '/wp-admin/admin.php?page=cutv-addons';
			?>

			<?php $video_stats = cutv_videos_stats(); ?>
			<?php $sources_stats = cutv_sources_stats( $group = TRUE ); ?>

			<div class = "cutv_clearfix"></div>


			<h4 class = "cutv_dashboard_title">
				<?php _e( 'Your videos' , CUTV_LANG ); ?>
			</h4>

			<div class = "cutv_clearfix"></div>


			<!-- VIDEOS BY STATUS -->
			<div>
				<div>
					<div class = "cutv_graph_wrapper" style = "width:100% !important; height:400px !important;">
						<div class = "cutv_graph_fact">
							<?php if( $video_stats != FALSE ) { ?>
								<span><?php echo cutv_numberK( $video_stats[ 'byStatus' ][ 'total' ] ); ?></span><br/>
								<?php _e( 'videos' , CUTV_LANG ); ?>
							<?php } else { ?>
								<i class = "fa fa-frown-o"></i><br/>
								<?php _e( 'There is no video.' , CUTV_LANG ); ?>
							<?php } ?>
						</div>
						<canvas id = "cutv_chart_videos_by_status" width = "900" height = "400"></canvas>
					</div>

					<script>
						var data_videos_by_status = [
							<?php foreach( (array) $video_stats[ 'byStatus' ][ 'items' ] as $label=>$count){ ?>
							<?php if( $label == 'total' ) continue; ?>
							{
								value: parseInt(<?php echo $count; ?>),
								color: '<?php echo $cutv_status[ $label ][ 'color' ]; ?>',
								label: '<?php echo strtoupper( $cutv_status[ $label ][ 'label' ] ); ?>',
							},
							<?php } ?>
						];
						jQuery(document).ready(function ($) {
							cutv_draw_chart(
								$('#cutv_chart_videos_by_status'),
								$('#cutv_chart_videos_by_status_legend'),
								data_videos_by_status,
								'donut'
							);
						});
					</script>
				</div>
				<div class = "cutv_widget_legend">
					<div id = "cutv_chart_videos_by_status_legend"></div>
					<div class = "cutv_dashboard_center">
						<a href = "<?php echo $newVideoLink; ?>">
							<button class = "cutv_dashboard_button cutv_dashboard_half">
								<i class = "cutv_link_icon fa fa-plus"></i>
								<?php _e( 'Add New Video' , CUTV_LANG ); ?>
							</button>
						</a>
						<a href = "<?php echo $reviewLink; ?>">
							<button class = "cutv_dashboard_button cutv_dashboard_half">
								<i class = "cutv_link_icon fa fa-flag"></i>
								<?php _e( 'Review Videos' , CUTV_LANG ); ?>
							</button>
						</a>
						<a href = "<?php echo $deferredLink; ?>">
							<button class = "cutv_dashboard_button cutv_dashboard_half">
								<i class = "cutv_link_icon fa fa-inbox"></i>
								<?php _e( 'Deferred Videos' , CUTV_LANG ); ?>
							</button>
						</a>
						<a href = "<?php echo $unwantedLink; ?>">
							<button class = "cutv_dashboard_button cutv_dashboard_half">
								<i class = "cutv_link_icon fa fa-ban"></i>
								<?php _e( 'Unwanted Videos' , CUTV_LANG ); ?>
							</button>
						</a>
					</div>
				</div>
				<div class = "cutv_clearfix"></div>
			</div>
			<div class = "cutv_clearfix"></div>

			<hr/>
			<h4 class = "cutv_dashboard_title">
				<?php _e( 'Your Sources' , CUTV_LANG ); ?>
			</h4>
			<!-- VIDEOS BY STATUS -->
			<div>
				<div>
					<div class = "cutv_graph_wrapper" style = "width:100% !important; height:400px !important;">
						<div class = "cutv_graph_fact">
							<?php if( $sources_stats[ 'total' ] != 0 ) { ?>
								<span><?php echo cutv_numberK( $sources_stats[ 'total' ] ); ?></span><br/>
								<?php _e( 'sources' , CUTV_LANG ); ?>
							<?php } else { ?>
								<i class = "fa fa-frown-o"></i><br/>
								<?php _e( 'There is no source.' , CUTV_LANG ); ?>
							<?php } ?>
						</div>
						<canvas id = "cutv_chart_sources_by_status" width = "900" height = "400"></canvas>
					</div>

					<script>
						var data_sources_by_status = [
							<?php if( count( $sources_stats[ 'byType' ] ) != 0 && is_array( $sources_stats[ 'byType' ] ) ){ ?>
							<?php foreach( (array) $sources_stats[ 'byType' ] as $label=>$count){ ?>
							<?php if( $label == 'total' ) continue; ?>
							<?php if( $label == 'groupType' ) $label = 'group_dm'; ?>
							{
								value: parseInt(<?php echo $count; ?>),
								color: '<?php echo $cutv_colors[ 'sourceTypes' ][ $label ]; ?>',
								label: '<?php echo strtoupper( $label ); ?>',
							},
							<?php } ?>
							<?php } ?>
						];
						jQuery(document).ready(function ($) {
							cutv_draw_chart(
								$('#cutv_chart_sources_by_status'),
								$('#cutv_chart_sources_by_status_legend'),
								data_sources_by_status,
								'donut'
							);
						});
					</script>
				</div>
				<div class = "cutv_widget_legend">
					<div id = "cutv_chart_sources_by_status_legend"></div>
					<div class = "cutv_dashboard_center">
						<a href = "<?php echo $newSourceLink; ?>">
							<button class = "cutv_dashboard_button cutv_dashboard_half">
								<i class = "cutv_link_icon fa fa-plus"></i>
								<?php _e( 'Add New Source' , CUTV_LANG ); ?>
							</button>
						</a>
					</div>
				</div>
				<div class = "cutv_clearfix"></div>
			</div>
			<div class = "cutv_clearfix"></div>

			<div class = "cutv_dashboard_center">
				<a href = "<?php echo $dashboardLink; ?>">
					<button class = "cutv_dashboard_button cutv_dashboard_half">
						<i class = " cutv_link_icon fa fa-dashboard"></i>
						<?php _e( 'View Dashboard' , CUTV_LANG ); ?>
					</button>
				</a>
				<a href = "<?php echo $manageLink; ?>">
					<button class = "cutv_dashboard_button cutv_dashboard_half">
						<i class = "cutv_link_icon  fa fa-film"></i>
						<?php _e( 'Manage Videos' , CUTV_LANG ); ?>
					</button>
				</a>
				<a href = "<?php echo $optionsLink; ?>">
					<button class = "cutv_dashboard_button cutv_dashboard_half">
						<i class = "cutv_link_icon fa fa-wrench"></i>
						<?php _e( 'Manage Options' , CUTV_LANG ); ?>
					</button>
				</a>

				<a href = "<?php echo $addonsLink; ?>">
					<button class = "cutv_dashboard_button cutv_dashboard_half">
						<i class = "cutv_link_icon fa fa-cubes"></i>
						<?php _e( 'Browse Addons' , CUTV_LANG ); ?>
					</button>
				</a>
			</div>
			<br/><br/>
			<div class = "cutv_dashboard_version pull-left">
				<?php echo __( 'You are using WP Video Robot' , CUTV_LANG ) . '  <b>' . CUTV_VERSION . '</b>'; ?>
			</div>
			<div class = "cutv_dashboard_links pull-right">
				<a href = "#" url = "<?php echo CUTV_OPTIONS_URL; ?>" id = "cutv_system_infos">
					<i class = "cutv_link_icon fa fa-info"></i> System Infos
				</a> |
				<a href = "<?php echo CUTV_SUPPORT_URL; ?>"><?php _e( 'Get Support' , CUTV_LANG ); ?></a>
			</div>
			<div class = "cutv_clearfix"></div>
			<?php
			
			
			return FALSE;

		}
	}

	/* Get Playlis Data from Channel Id */
	if( ! function_exists( 'cutv_get_country_name' ) ) {
		function cutv_get_country_name( $country_code ) {
			global $cutv_countries;

			return $cutv_countries[ $country_code ];
		}
	}
	
	/* Render manage_filters */
	if( ! function_exists( 'cutv_manage_render_filters' ) ) {
		function cutv_manage_render_filters( $filter_name , $button = TRUE ) {

			global $cutv_status , $cutv_services;
			$filter_class = 'cutv_manage';

			if( $filter_name == 'authors' ) {
				$filter = cutv_get_authors_count();
				$prefix = 'filter_authors';
			} elseif( $filter_name == 'dates' ) {

				$filter = cutv_get_dates_count();
				$prefix = 'filter_dates';

			} elseif( $filter_name == 'categories' ) {
				$filter = cutv_get_categories_count(false, true);
				$prefix = 'filter_categories';
			} elseif( $filter_name == 'services' ) {
				$filter = cutv_get_services_count();
				$prefix = 'filter_services';
			} elseif( $filter_name == 'statuses' ) {
				$filter = cutv_get_status_count();
				$prefix = 'filter_statuses';

			}
			//new dBug( $filter);		return false;
			$render = '';
			//$render .= 	//'<input type="hidden" name="'.$prefix.'[]" value="0">'.
			$render .= '<div class="wpvr_manage_box_content_inner">';
			$render .= '<ul id="' . $filter_class . '_' . $prefix . '" class="' . $filter_class . ' wpvr_manage_check_ul">';
			
			if( count( $filter ) == 0 ) return FALSE;
			foreach ( (array) $filter as $value => $data ) {


				if( $filter_name == 'services' ) {
					$label = '<span class="wpvr_service_icon ' . $data[ 'value' ] . '"> ' . $data[ 'label' ] . ' </span>';
				} elseif( $filter_name == 'statuses' ) {
					$icon  = '<i class="wpvr_video_status_icon fa ' . $cutv_status[ $data[ 'value' ] ][ 'icon' ] . ' "></i>';
					$label = '<span class="wpvr_video_status ' . $data[ 'value' ] . '"> ' . $icon . $data[ 'label' ] . ' </span>';
				} else
					$label = $data[ 'label' ];


				$render .= '<li id="category-289">' .
				           '<label class="selectit">' .
				           '<input type="checkbox" name="' . $prefix . '[]" value="' . $data[ 'value' ] . '" />' .
				           $label .
				           '<span class="wpvr_filter_count" >' .
				           cutv_numberK( $data[ 'count' ] ) .
				           '</span>' .
				           '</label>' .
				           '</li>';

			}

			$render .= '</ul>';
			$render .= '</div>';

			$render
				.= '
<div>
	<h3>New channel name</h3>
	<input type="text" name="cutv-new-channel-name">
</div>
				<div class="wpvr_button" id="add-category-button">
					<i class="wpvr_button_icon fa fa-refresh"></i>
					' . __( 'ADD CHANNEL' , CUTV_LANG ) . '
				</div>
			';

			return $render;
		}
	}