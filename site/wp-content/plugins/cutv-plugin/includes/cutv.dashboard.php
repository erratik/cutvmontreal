<?php


	global $wpvr_colors , $wpvr_status , $wpvr_services , $wpvr_types_;
	global $wpvr_vs;
	//$max_wanted_videos = wpvr_max_fetched_videos_per_run();
	wp_localize_script( 'wp-api', 'wpApiSettings', array( 'root' => esc_url_raw( rest_url() ), 'nonce' => wp_create_nonce( 'wp_rest' ) ) );
	wp_enqueue_script( 'wp-api' );
?>

<script>
	jQuery(document).ready(function($) {

		// We'll pass this variable to the PHP function cutv_add_channel


		// CREATE A WPVR CATEGORY
		$('body').on('click', '#add-category-button', function(){
			var channelName = $('[name="cutv-new-channel-name"]').val();
			var slug = channelName.toLowerCase();
				slug = slug.replace(/ /g, '-');

			_cutv.ajax(wpApiSettings.root + 'wp/v2/categories', {
					description: 'Mlkshk flannel deep v marfa hashtag brooklyn.',
					name: channelName,
					slug: slug
				}
			).then(function (data) {
				console.log(data);
				_cutv.ajax(ajaxurl, {
						'action' : 'cutv_add_channel',
						channelName: data.name,
						slug: data.slug,
						description: data.description,
						cat_id: data.id
					}
				).then(function (data) {
					console.log(data);
				});
			});

		});


		var _cutv = {
			ajax : function(url, data, options) {
				return new MakePromise({ url: url, data: data, options: options });
			}
		};

		DEBUG_LEVEL = window.location.hostname == 'cutv.dev' ? 3 : 0;
		if (navigator.appName == "Microsoft Internet Explorer") DEBUG_LEVEL = 0;
		function log(options) {

			var defaults = {
				msg: null,
				level: DEBUG_LEVEL,
				group: false,
				color: 'blue'
			};
			$.extend(defaults, options);

			if ( DEBUG_LEVEL > 2 && navigator.appName != "Microsoft Internet Explorer") {
				console.log("%c" + options.msg, "color:"+options.color+";");
			}
		}
		function MakePromise(options){

			//log({msg: "A promise is being made...", color: 'purple' });

			var defaults = {
				method: 'POST',
				cache: true,
				showErrors: true,
				success: function(result) {
					//log({msg:"Promise went through!", color: 'purple' });
					//console.groupEnd();
					promise.resolve(result);
				},
				error: function(jqXHR, textStatus, error) {

					if ( jqXHR.status == 400 ) {
						errorMessage = jqXHR.responseText;
						log({msg: "%c(╯°□°）╯ should be accompanied by custom message to display", color: 'red' });
						log({msg: errorMessage , color: 'red' });


					} else {
						log({msg: "%c(╯°□°）╯", color: 'red' });
						errorMessage = { error: jqXHR.message, statusCode: jqXHR.code};
					}

					promise.reject(errorMessage);
				}
			};

			$.extend(options, defaults);
//			console.log(options, defaults);

			var promise = $.Deferred();
			$.ajax({
				type: options.method,
				url: options.url,
				data: options.data,
				success: options.success,
				error: options.error,
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
				}
			});

			return promise;
		}
	});

</script>

<div class = "wrap wpvr_wrap" style = "visibility:hidden;">
	<?php wpvr_show_logo(); ?>
	<h2 class = "wpvr_title">
		<i class = "wpvr_title_icon fa fa-dashboard"></i>
		<?php echo __( 'Dashboard' , CUTV_LANG ); ?>
	</h2>
	<?php
		
		global $wpvr_pages;
		$wpvr_pages = TRUE;

		
		$active = array(
			'content'     => '' ,
			'automation'  => '' ,
			'duplicates'  => '' ,
			'datafillers' => '' ,
			'setters'     => '' ,
		);
		
		if( ! isset( $_GET[ 'section' ] ) || ! isset( $active[ $_GET[ 'section' ] ] ) ) {
			$active[ 'content' ] = 'active';
		} else {
			
			$active[ $_GET[ 'section' ] ] = 'active';
		}
		//echo $_GET['tab'] ;
	?>

	<div class = "wpvr_nav_tabs pull-left">


		<div title = "<?php _e( 'Sources & Videos' , CUTV_LANG ); ?>" class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'content' ]; ?>" id = "a">
			<i class = "wpvr_tab_icon fa fa-bar-chart"></i><br/>
			<span><?php _e( 'Sources & Videos' , CUTV_LANG ); ?> </span>
		</div>


		<?php if( CUTV_ENABLE_SETTERS === TRUE ) { ?>
			<div title = "<?php _e( 'Admin Actions' , CUTV_LANG ); ?>" class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'setters' ]; ?>" id = "e">
				<i class = "wpvr_tab_icon fa fa-hand-o-up"></i><br/>
				<span><?php _e( 'Admin Actions' , CUTV_LANG ); ?></span>
			</div>
		<?php } ?>


		<span class = "wpvr_version_helper">
			<?php echo "v" . CUTV_VERSION; ?>
		</span>

		<div class = "wpvr_clearfix"></div>
	</div>
	<div class = "wpvr_clearfix"></div>
	<div class = "wpvr_dashboard">

		
		<!-- DUPLICATES DASHBOARD -->
		<div id = "" class = "wpvr_nav_tab_content tab_c">
			
			<?php
				global $is_DT;
				$is_DT = TRUE;
				//include( 'wpvr.manage.php' );
			?>
		
		</div>

		<?php $sources_stats = wpvr_sources_stats( $group = TRUE ); ?>
		<?php $video_stats = wpvr_videos_stats(); ?>


		<!-- SOURCE & VIDEOS DASHBOARD -->
		<div id = "" class = "wpvr_nav_tab_content tab_a">
			<div id = "dashboard-widgets" class = "metabox-holder" style="display: flex;">
				
				<?php
					//$sources_stats = wpvr_sources_stats( $group = true );
					//$video_stats = wpvr_videos_stats(  );

					//_d( $sources_stats );

					//_d( $video_stats);

					$new_video_link  = CUTV_SITE_URL . '/wp-admin/post-new.php?post_type=' . CUTV_VIDEO_TYPE;
					$new_source_link = CUTV_SITE_URL . '/wp-admin/post-new.php?post_type=' . CUTV_SOURCE_TYPE;
				?>
				
				<!-- LEFT DASHBOARD WIDGETS -->
				<div class = "postbox-container">
					<!-- VIDEOS WIDGET -->
					<div id = "" class = "postbox ">
						<h3 class = "hndle"><span> <?php _e( 'YOUR VIDEOS' , CUTV_LANG ); ?> </span></h3>

						<div class = "inside">
							<div>
								<div class = "wpvr_graph_wrapper" style = "width:100% !important; height:400px !important;">
									<div class = "wpvr_graph_fact">
										<?php if( $video_stats != FALSE ) { ?>
											<span><?php echo wpvr_numberK( $video_stats[ 'byStatus' ][ 'total' ] ); ?></span><br/>
											<?php _e( 'videos' , CUTV_LANG ); ?>
										<?php } else { ?>
											<div class = "wpvr_message">
												<i class = "fa fa-frown-o"></i><br/>
												<?php _e( 'There is no video.' , CUTV_LANG ); ?>
											</div>
											<p>
												<a href = "<?php echo $new_video_link; ?>" class = "wpvr_black_button wpvr_submit_button wpvr_graph_button">
													<i class = "fa fa-plus"></i>
													<?php _e( 'Import your first video.' , CUTV_LANG ); ?>
												</a>
											</p>
										<?php } ?>
									</div>
									<canvas id = "wpvr_chart_videos_by_status" width = "900" height = "400"></canvas>
								</div>
								<?php if( count( $video_stats[ 'byStatus' ][ 'items' ] ) != 0 ) { ?>
									<script>
										var data_videos_by_status = [
											<?php foreach( (array) $video_stats[ 'byStatus' ][ 'items' ] as $label=>$count){ ?>
											<?php if( $label == 'total' ) continue; ?>

											{
												value: parseInt(<?php echo $count; ?>),
												color: '<?php echo $wpvr_status[ $label ][ 'color' ]; ?>',
												label: '<?php echo strtoupper( $wpvr_status[ $label ][ 'label' ] ); ?>',
											},
											<?php } ?>
										];
										jQuery(document).ready(function ($) {
											wpvr_draw_chart(
												$('#wpvr_chart_videos_by_status'),
												$('#wpvr_chart_videos_by_status_legend'),
												data_videos_by_status,
												'donut'
											);
										});
									</script>
								<?php } ?>
							</div>
							<?php if( $sources_stats[ 'total' ] != 0 ) { ?>
								<div class = "wpvr_widget_legend">
									<div id = "wpvr_chart_videos_by_status_legend"></div>
								</div>
							<?php } ?>
							<div class = "wpvr_clearfix"></div>
						</div>
					</div>
					<!-- VIDEOS WIDGET -->




				</div>
				<!-- LEFT DASHBOARD WIDGETS -->

				<!-- RIGHT DASHBOARD WIDGETS -->
				<div id = "postbox-container-2" class = "postbox-container" style="flex: 1 0 auto;">

					<div id = "" class = "meta-box-sortables">


						<!-- FILTER BY CAT -->
						<?php $fcb_categories = cutv_manage_render_filters( 'categories' ); ?>
						<?php if( $fcb_categories ) { ?>
							<div class = "wpvr_manage_box open">
								<div class = "wpvr_manage_box_head">
									<i class = " fa fa-folder-open"></i>
									<?php _e( 'Filter by' , WPVR_LANG ); ?> <?php _e( 'Categories' , WPVR_LANG ); ?>
									<i class = "pull-right caretDown fa fa-caret-down"></i>
									<i class = "pull-right caretUp fa fa-caret-up"></i>
								</div>
								<div class = "wpvr_manage_box_content">
									<?php echo $fcb_categories; ?>
								</div>
							</div>
						<?php } ?>
						<!-- FILTER BY CAT -->

						<!-- VIDEOS WIDGET 2 -->
						<?php if( $video_stats != FALSE ) { ?>
							<div id = "" class = "postbox ">
								<h3 class = "hndle"><span> <?php _e( 'YOUR VIDEOS - By Category' , CUTV_LANG ); ?> </span></h3>

								<div class = "inside">
									<div class = "wpvr_widget_pie pull-left">
										<div class = "wpvr_graph_wrapper" style = "width:100% !important;">
											<canvas id = "wpvr_chart_videos_by_cat" width = "250" height = "250"></canvas>
										</div>

										<script>
											var data_videos_by_cat = [
												<?php $i = 0; ?>
												<?php $pColors = wpvr_generate_colors( count( $video_stats[ 'byCat' ][ 'items' ] ) ); ?>

												<?php foreach( (array) $video_stats[ 'byCat' ][ 'items' ] as $label=>$count){ ?>

												{
													value: parseInt(<?php echo $count; ?>),
													color: '<?php echo $pColors[ $i ]; ?>',
													label: '<?php echo addslashes( strtoupper( $label ) ); ?>',
												},
												<?php $i ++; ?>
												<?php } ?>
											];
											jQuery(document).ready(function ($) {
												wpvr_draw_chart(
													$('#wpvr_chart_videos_by_cat'),
													$('#wpvr_chart_videos_by_cat_legend'),
													data_videos_by_cat,
													'donut'
												);
											});
										</script>
									</div>
									<div class = "wpvr_widget_legend pull-left">
										<div id = "wpvr_chart_videos_by_cat_legend"></div>
									</div>
									<div class = "wpvr_clearfix"></div>
								</div>
							</div>
						<?php } ?>
						<!-- VIDEOS WIDGET 2 -->

						<?php if( FALSE ) { ?>
							<!-- BOX -->
							<div id = "" class = "postbox ">
								<h3 class = "hndle"><span> <?php _e( 'BOX TITLE' , CUTV_LANG ); ?> </span></h3>

								<div class = "inside">

									BOX 0


								</div>
							</div>
							<!-- BOX -->
						<?php } ?>

					</div>
				</div>
				<!-- RIGHT DASHBOARD WIDGETS -->



			</div>
		</div>
		<!-- SOURCE & VIDEOS DASHBOARD -->



	</div>