<?php
	
	
	/* Defining videos as wpvr_pages */
	add_action( 'admin_notices' , 'wpvr_videos_define_wpvr_pages' );
	function wpvr_videos_define_wpvr_pages() {
		$type = 'post';
		if( isset( $_GET[ 'post_type' ] ) ) $type = $_GET[ 'post_type' ];
		if( WPVR_VIDEO_TYPE == $type ) {
			global $wpvr_pages;
			$wpvr_pages = TRUE;
		}
	}
	
	/* Define custom video type */
	add_action( 'init' , 'wpvr_define_video_post_type' , 0 );
	function wpvr_define_video_post_type() {
		global $wpvr_options , $wpvr_dynamics;

		$videos_support = array( 'title' , 'editor' , 'author' , 'thumbnail' );

		if( WPVR_META_DEBUG_MODE ) $videos_support[] = 'custom-fields';

		if( $wpvr_options[ 'enableVideoComments' ] === TRUE ) {
			$videos_support[] = 'comments';
		}
		
		if( WPVR_ENABLE_POST_FORMATS ) {
			$videos_support[] = 'post-formats';
		}

		$videos_support = apply_filters( 'wpvr_extend_videos_support' , $videos_support );
		
		$labels = array(
			'name'               => _x( 'Videos' , 'Post Type General Name' , WPVR_LANG ) ,
			'singular_name'      => _x( 'Video' , 'Post Type Singular Name' , WPVR_LANG ) ,
			'menu_name'          => __( 'Videos' , WPVR_LANG ) ,
			'parent_item_colon'  => __( 'Parent Item:' , WPVR_LANG ) ,
			'all_items'          => __( 'All Videos' , WPVR_LANG ) ,
			'view_item'          => __( 'View Video' , WPVR_LANG ) ,
			'add_new_item'       => __( 'Add New Video' , WPVR_LANG ) ,
			'add_new'            => __( 'Add New' , WPVR_LANG ) ,
			'edit_item'          => __( 'Edit Video' , WPVR_LANG ) ,
			'update_item'        => __( 'Update Video' , WPVR_LANG ) ,
			'search_items'       => __( 'Search Video' , WPVR_LANG ) ,
			'not_found'          => __( 'Not found' , WPVR_LANG ) ,
			'not_found_in_trash' => __( 'Not found in Trash' , WPVR_LANG ) ,
		);
		$args   = array(
			'label'               => __( 'video' , WPVR_LANG ) ,
			'description'         => __( 'Video' , WPVR_LANG ) ,
			'labels'              => $labels ,
			'rewrite'             => FALSE ,
			'supports'            => $videos_support ,
			'taxonomies'          => array( 'category' , 'post_tag' ) ,
			'hierarchical'        => FALSE ,
			'public'              => TRUE ,
			'show_ui'             => TRUE ,
			'show_in_menu'        => TRUE ,
			'show_in_nav_menus'   => TRUE ,
			'show_in_admin_bar'   => TRUE ,
			'menu_position'       => 5 ,
			'menu_icon'           => 'dashicons-format-video' ,
			'can_export'          => TRUE ,
			'has_archive'         => FALSE ,
			'exclude_from_search' => FALSE ,
			'publicly_queryable'  => TRUE ,
			'capability_type'     => 'post' ,
		);
		register_post_type( WPVR_VIDEO_TYPE , $args );
	}
	
	
	/*Init Videos Editing metaboxes */
	add_action( 'init' , 'wpvr_video_init_metaboxes' , 9999 );
	function wpvr_video_init_metaboxes() {
		if( ! class_exists( 'wpvr_cmb_Meta_Box' ) ) {
			require_once( WPVR_PATH . '/assets/metabox/init.php' );
		}
	}
	
	/* Define Video Metaboxes */
	add_filter( 'wpvr_cmb_meta_boxes' , 'wpvr_video_metaboxes' );
	function wpvr_video_metaboxes( $meta_boxes ) {
		global $wpvr_options;
		$prefix = 'wpvr_video_';

		if( isset( $_GET[ 'post' ] ) ) {
			if( is_array( $_GET[ 'post' ] ) ) return $meta_boxes;
			else $post_id = $_GET[ 'post' ];
		} elseif( isset( $_POST[ 'post_ID' ] ) ) $post_id = $_POST[ 'post_ID' ];
		else $post_id = "";

		$shortcode_msg  = __( 'Embed this video in any post or page, simply by including this shortcode.' , WPVR_LANG );
		$shortcode_code = '[wpvr id=' . $post_id . ']';

		$unwanted_button = wpvr_render_add_unwanted_button( $post_id );
		$embed_button
		                 = '
			<div class="">

				<button
					dialog_title = "' . __( 'WPVR - Embed this video' , WPVR_LANG ) . '"
					msg = "' . $shortcode_msg . '"
					code = "' . $shortcode_code . '"
					class="wpvr_black_button wpvr_full_width wpvr_button wpvr_embed_video_btn"
				>
					<i class="fa fa-code" style="margin-right:5px;"></i>
					' . __( 'Embed This Video' , WPVR_LANG ) . '
				</button>
			</div>
		';
		
		$video_id      = get_post_meta( $post_id , 'wpvr_video_id' , TRUE );
		$video_service = get_post_meta( $post_id , 'wpvr_video_service' , TRUE );
		$preview_button
		               = '
			<div class="">

				<button
					url = "' . WPVR_MANAGE_URL . '"
					post_id = "' . $post_id . '"
					video_id = "' . $video_id . '"
					service = "' . $video_service . '"
					class="wpvr_black_button wpvr_full_width wpvr_button wpvr_video_view"
				>
					<i class="fa fa-eye" style="margin-right:5px;"></i>
					' . __( 'Preview This Video' , WPVR_LANG ) . '
				</button>
			</div>
		';

		$mb_fields   = array();
		$mb_fields[] = array(
			'name'      => __( 'Plugin Version' , WPVR_LANG ) ,
			'default'   => WPVR_VERSION ,
			'id'        => $prefix . 'plugin_version' ,
			'type'      => 'text_small' ,
			'wpvrStyle' => 'display:none;' ,
		);

		if( $post_id != '' ) {

			$mb_fields[] = array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $preview_button ,
				'type'      => 'show_html' ,
				'wpvrClass' => 'wpvr_metabox_html wpvr_action_btns' ,
			);
			
			$mb_fields[] = array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $embed_button ,
				'type'      => 'show_html' ,
				'wpvrClass' => 'wpvr_metabox_html wpvr_action_btns' ,
			);

			$mb_fields[] = array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $unwanted_button ,
				'type'      => 'show_html' ,
				'wpvrClass' => 'wpvr_metabox_html wpvr_action_btns' ,
			);

		} else {
			$mb_fields[] = array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => '<div class="wpvr_no_actions">' . __( 'Start by saving your video' , WPVR_LANG ) . '</div>' ,
				'type'      => 'show_html' ,
				'wpvrClass' => 'wpvr_metabox_html' ,
			);
		}

		$mb_fields = apply_filters( 'wpvr_extend_video_actions_fields' , $mb_fields );

		$meta_boxes[] = array(
			'id'         => 'wpvr_video_actions_metabox' ,
			'title'      => __( 'WPVR - Video Actions' , WPVR_LANG ) ,
			'pages'      => array( WPVR_VIDEO_TYPE ) , // post type
			'context'    => 'side' ,
			'priority'   => 'low' ,
			'show_names' => TRUE , // Show field names on the left
			'fields'     => $mb_fields ,
		);
		if( $wpvr_options[ 'enableManualAdding' ] ) {
			
			/* Extending Video Services Options */
			$video_service_options = array();
			$video_service_options = apply_filters( 'wpvr_extend_video_services_options' , $video_service_options );
			
			/* Extending Video Services Fields  */
			$video_fields = array();
			$video_fields = apply_filters( 'wpvr_extend_video_fields' , $video_fields , $prefix );
			//d( $video_fields );
			$video_services = array(
				array(
					'name'      => __( 'Video Service' , WPVR_LANG ) ,
					'desc'      => '' ,
					'id'        => $prefix . 'service' ,
					'type'      => 'radio_inline' ,
					'options'   => $video_service_options ,
					'wpvrClass' => 'videoService' ,
					//'wpvrStyle' => 'display:none;',
				) ,
			);

			$grabButton
				= '
				<div class="wpvr_manual_adding_btns" style="display:none;">
					<button class="pull-right wpvr_button wpvr_green_button wpvr_manual_import_trigger">
						<i class="fa fa-download"></i>
						Import Video
					</button>

					<button href="#" class="wpvr_black_button wpvr_button pull-right wpvr_toggle_grabbing_button" state="off">
						<i class="fa fa-check-square-o"></i>
						' . __( 'Toggle All' , WPVR_LANG ) . '
					</button>
				</div>
			';


			$video_choices = array(
				array(
					'name'        => __( 'Enable Grabbing' , WPVR_LANG ) ,
					'desc'        => '' ,
					'id'          => $prefix . 'enableManualAdding' ,
					'type'        => 'select' ,
					'options'     => array(
						'on'  => __( 'YES' , WPVR_LANG ) ,
						'off' => __( 'NO' , WPVR_LANG ) ,
					) ,
					'default'     => 'off' ,
					'description' => __( 'Enable this to get the video data' , WPVR_LANG ) . '. <br/>' . $grabButton ,
					'wpvrClass'   => '' ,
					//'wpvrStyle'   => 'display:none;' ,
				
				) ,
				array(
					'name'        => __( 'Title' , WPVR_LANG ) ,
					'desc'        => '' ,
					'id'          => $prefix . 'getTitle' ,
					'type'        => 'select' ,
					'options'     => array(
						'on'  => __( 'YES' , WPVR_LANG ) ,
						'off' => __( 'NO' , WPVR_LANG ) ,
					) ,
					'default'     => 'off' ,
					'description' => __( 'Grab the video Title' , WPVR_LANG ) . '.' ,
					'wpvrClass'   => 'wpvrManualOptions' ,
					'wpvrStyle'   => 'display:none;' ,
				) ,
				array(
					'name'        => __( 'Thumbnail' , WPVR_LANG ) ,
					'desc'        => '' ,
					'id'          => $prefix . 'getThumb' ,
					'type'        => 'select' ,
					'options'     => array(
						'on'  => __( 'YES' , WPVR_LANG ) ,
						'off' => __( 'NO' , WPVR_LANG ) ,
					) ,
					'default'     => 'off' ,
					'description' => __( 'Grab the video Thumbnail' , WPVR_LANG ) . '.' ,
					'wpvrClass'   => 'wpvrManualOptions' ,
					'wpvrStyle'   => 'display:none;' ,
				) ,
				array(
					'name'        => __( 'Description' , WPVR_LANG ) ,
					'desc'        => '' ,
					'id'          => $prefix . 'getDesc' ,
					'type'        => 'select' ,
					'options'     => array(
						'on'  => __( 'YES' , WPVR_LANG ) ,
						'off' => __( 'NO' , WPVR_LANG ) ,
					) ,
					'default'     => 'off' ,
					'description' => __( 'Grab the video Description' , WPVR_LANG ) . '.' ,
					'wpvrClass'   => 'wpvrManualOptions' ,
					'wpvrStyle'   => 'display:none;' ,
				) ,
				array(
					'name'        => __( 'Tags' , WPVR_LANG ) ,
					'desc'        => '' ,
					'id'          => $prefix . 'getTags' ,
					'type'        => 'select' ,
					'options'     => array(
						'on'  => __( 'YES' , WPVR_LANG ) ,
						'off' => __( 'NO' , WPVR_LANG ) ,
					) ,
					'default'     => 'off' ,
					'description' => __( 'Grab the video tags' , WPVR_LANG ) . '.' ,
					'wpvrClass'   => 'wpvrManualOptions' ,
					'wpvrStyle'   => 'display:none;' ,
				) ,
				array(
					'name'        => __( 'Post Date' , WPVR_LANG ) ,
					'desc'        => '' ,
					'id'          => $prefix . 'getPostDate' ,
					'type'        => 'select' ,
					'options'     => array(
						'on'  => __( 'YES' , WPVR_LANG ) ,
						'off' => __( 'NO' , WPVR_LANG ) ,
					) ,
					'default'     => 'off' ,
					'description' => __( 'Grab the video original post date' , WPVR_LANG ) . '.' ,
					'wpvrClass'   => 'wpvrManualOptions' ,
					'wpvrStyle'   => 'display:none;' ,
				) ,

				array(
					'name'        => __( 'AutoEmbed Player' , WPVR_LANG ) ,
					'desc'        => '' ,
					'id'          => $prefix . 'disableAutoEmbed' ,
					'type'        => 'select' ,
					'options'     => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'on'      => __( 'Do not embed the plugin video player' , WPVR_LANG ) ,
						'off'     => __( 'Embed the plugin video player' , WPVR_LANG ) ,
					) ,
					'default'     => 'off' ,
					'description' => __( 'Turn this on if you want to use this video as a normal post.' , WPVR_LANG ) ,
					'wpvrClass'   => '' ,
					//'wpvrStyle'   => 'display:none;' ,

				) ,
			
			);

			$please_choose = array();

			//$please_choose = array(
			//	array(
			//		'name'      => '' ,
			//		'desc'      => '' ,
			//		'id'        => 'wpvr_pick_a_service' ,
			//		'html'      => '<img src="'.WPVR_URL.'assets/images/pick_a_service.jpg" />' ,
			//		'type'      => 'show_html' ,
			//		'wpvrClass' => 'wpvr_metabox_html' ,
			//	) ,
			//);

			$video_manual_fields = array_merge(
				$video_services ,
				$please_choose ,
				$video_fields ,
				$video_choices
			);
			//d( $video_manual_fields );
			$meta_boxes[] = array(
				'id'         => 'wpvr_video_metabox' ,
				'title'      => __( 'WPVR - Manual Adding' , WPVR_LANG ) ,
				'pages'      => array( WPVR_VIDEO_TYPE ) , // post type
				'context'    => 'normal' ,
				'priority'   => 'high' ,
				'show_names' => TRUE , // Show field names on the left
				'fields'     => $video_manual_fields ,
			);
		}
		global $debug;
		$meta_boxes = apply_filters( 'wpvr_extend_videos_metaboxes' , $meta_boxes );

		//d( $meta_boxes );//new dBug( $debug);
		return $meta_boxes;
	}
	
	/* Define Video List Column */
	add_filter( 'manage_edit-' . WPVR_VIDEO_TYPE . '_columns' , 'wpvr_video_columns' );
	function wpvr_video_columns( $columns ) {
		unset( $columns );
		$columns = array(
			'cb'          => '<input type="checkbox"/>' ,
			'video_thumb' => __( 'Thumbnail' , WPVR_LANG ) ,
			'title'       => __( 'Title' , WPVR_LANG ) ,
			'video_meta'  => __( 'Informations' , WPVR_LANG ) ,
			'video_data'  => '' ,
		);

		return $columns;
	}
	
	add_action( 'manage_' . WPVR_VIDEO_TYPE . '_posts_custom_column' , 'wpvr_video_custom_columns' );
	function wpvr_video_custom_columns( $column ) {
		global $post , $wpvr_options , $wpvr_status , $wpvr_vs , $wpvr_services;
		global $wpvr_unwanted_ids;
		$duration                    = wpvr_get_duration( $post->ID );

		switch ( $column ) {
			case 'video_thumb':
				$service = get_post_meta( $post->ID , 'wpvr_video_service' , TRUE );
				$video_id            = get_post_meta( $post->ID , 'wpvr_video_id' , TRUE );
				$editLink            = get_edit_post_link( $post->ID );
				if( isset( $wpvr_unwanted_ids[ $service ][ $video_id ] ) ) $is_unwanted = TRUE;
				else $is_unwanted = FALSE;

				?>
				<div class = "wpvr_thumb_box">
					<div class = "wpvr_center wpvr_service_icon <?php echo $service; ?>">
						<?php echo $wpvr_vs[ $service ][ 'label' ]; ?>
					</div>
					<?php if( $is_unwanted ) { ?>
						<div class = "wpvr_center wpvr_is_unwanted">
							<i class = "fa fa-ban"></i>
							<span><?php echo __( 'UNWANTED' , WPVR_LANG ); ?></span>
						</div>
					<?php } ?>

					<div class = "wpvr_video_actions" style = "display:none;">

						<a class = "wpvr_submit_button wpvr_edit_video" href = "<?php echo $editLink; ?>">
							<i class = "wpvr_link_icon fa fa-pencil"></i>EDIT
						</a>
						<a
							href = "#"
							class = "wpvr_submit_button wpvr_preview_video wpvr_video_view"
							url = "<?php echo WPVR_MANAGE_URL; ?>"
							service = "<?php echo $service; ?>"
							video_id = "<?php echo $video_id; ?>"
							post_id = "<?php echo $post->ID; ?>"
						>
							<i class = "wpvr_link_icon fa fa-eye"></i>PREVIEW
						</a>
					</div>
					<div class = "wpvr_video_actions_overlay" style = "display:none;"></div>
					<?php
						$thumb = get_the_post_thumbnail( $post->ID , 'wpvr_hard_thumb' );
						if( $thumb == '' ) $thumb = '<img src="' . WPVR_NO_THUMB . '" />';

						echo $thumb;
					?>
				</div>
				<?php

				break;

			case 'video_data':
				$service = get_post_meta( $post->ID , 'wpvr_video_service' , TRUE );
				$status              = get_post_status( $post->ID );
				$wpvr_video_statuses = array( 'pending' , 'publish' , 'invalid' , 'draft' , 'trash' );

				$video_views = get_post_meta( $post->ID , 'wpvr_video_views' , TRUE );
				if( $video_views == '' ) $video_views = 0;


				?>
				<div style = "">

					<?php if( $service != '' ) { ?>
						<div class = "wpvr_center wpvr_service_icon <?php echo $service; ?>">
							<?php echo $wpvr_vs[ $service ][ 'label' ] . ' ' . __( 'video' , WPVR_LANG ); ?>
						</div>
					<?php } ?>

					<?php if( $duration != '' ) { ?>
						<div class = "wpvr_center wpvr_video_admin_duration">
							<?php echo $duration; ?>
						</div>
					<?php } ?>

					<div class = "wpvr_center wpvr_video_admin_views">
						<b><?php echo wpvr_numberK( $video_views ); ?></b> <?php _e( 'Views' , WPVR_LANG ); ?>
					</div>

					<?php if( in_array( $status , $wpvr_video_statuses ) ) { ?>
						<div class = "wpvr_center wpvr_video_status <?php echo $status; ?>">
							<i class = "fa wpvr_video_status_icon <?php echo $wpvr_status[ $status ][ 'icon' ]; ?>"></i>
							<?php echo $wpvr_status[ $status ][ 'label' ]; ?>
						</div>
					<?php } ?>


				</div>
				<?php
				$echo = "";
				$echo = apply_filters( 'wpvr_extend_video_list_data_column' , $echo , $post );
				echo $echo;

				break;

			case 'video_meta':
				$echo = '';

				$video_author         = '<br/>' . __( 'Posted by' , WPVR_LANG ) . ' <b>' . get_the_author() . '</b>';
				$video_cats           = wp_get_post_categories( $post->ID );
				$video_postdate       = wpvr_human_time_diff( $post->ID );
				$video_source_name    = get_post_meta( $post->ID , 'wpvr_video_sourceName' , TRUE );
				$comments             = wp_count_comments( $post->ID );
				$video_comments_count = $comments->total_comments;
				//d( $video_comments_count );return false;

				/* Echo Video Shortcode */
				$echo .= '<span class="wpvr_source_span">';
				$echo .= '<i class="fa fa-youtube-play"></i>';
				$echo .= '[wpvr id=' . $post->ID . ']';
				$echo .= '</span><br/>';


				/* Echo Video Post Date */
				$echo .= '<span class=" wpvr_source_span">';
				$echo .= '<i class="fa fa-clock-o"></i>';
				$echo .= __( 'Posted' , WPVR_LANG ) . ' <b>' . $video_postdate . '</b> <br/>';
				$echo .= '</span>';


				/* Echo Video Author */
				$echo .= '<span class=" wpvr_source_span">';
				$echo .= '<i class="fa fa-user"></i>';
				$echo .= __( 'Posted by' , WPVR_LANG );
				$echo .= ' <b>' . get_the_author() . '</b> <br/>';
				$echo .= '</span>';

				/* Echo Video Categories */
				if( count( $video_cats ) != 0 && $video_cats != FALSE ) {
					$cats = "";
					foreach ( $video_cats as $c ) {
						$cat = get_category( $c );
						$cats .= "<b>" . $cat->slug . "</b>, ";
					}
					$cats = substr( $cats , 0 , - 2 );
					$echo .= '<span class=" wpvr_source_span">';
					$echo .= '<i class="fa fa-folder-open"></i>';
					$echo .= __( 'Posted in' , WPVR_LANG ) . ' ' . $cats;
					$echo .= '</span><br/>';
				}
				/* Echo Video Source infos */
				if( $video_source_name != '' ) {
					$echo .= '<span class=" wpvr_source_span">';
					$echo .= '<i class="fa fa-search"></i>';
					$echo .= __( 'Source :' , WPVR_LANG );
					$echo .= ' <b>' . $video_source_name . '</b> <br/>';
					$echo .= '</span>';
				}

				/* Echo Video Autoembeding ? */
				$autoembed = get_post_meta( $post->ID , 'wpvr_video_disableAutoEmbed' , TRUE );
				if( $autoembed == 'on' ) {
					$echo .= '<span class=" wpvr_source_span">';
					$echo .= '<i class="fa fa-close"></i>';
					//$echo .= ' <b>' . $video_comments_count . '</b> ';
					$echo .= __( 'Autoembedding Disabled.' , WPVR_LANG );
					$echo .= '<br/></span>';
				}

				$echo .= '<span class=" wpvr_source_span">';
				$echo .= '<i class="fa fa-comments"></i>';
				$echo .= ' <b>' . $video_comments_count . '</b> ';
				$echo .= __( 'comments' , WPVR_LANG );
				$echo .= '<br/></span>';

				$echo = apply_filters( 'wpvr_extend_video_list_settings_column' , $echo , $post );

				echo $echo;

				break;
		}
	}
	
	
	/* Adding Manually a Video by ID */
	add_filter( 'wp_insert_post_data' , 'wpvr_manual_add_function' , '99' , 2 );
	function wpvr_manual_add_function( $data , $postarr ) {
		global $wpvr_vs , $wpvr_imported;
		
		$post_id = $postarr[ 'ID' ];

		if(
			! isset( $postarr[ 'wpvr_video_enableManualAdding' ] )
			|| $postarr[ 'wpvr_video_enableManualAdding' ] != "on"
		) return $data;

		if( isset( $postarr[ 'wpvr_video_service' ] ) && $postarr[ 'wpvr_video_service' ] != "" ) {
			$video_service = $postarr[ 'wpvr_video_service' ];
			$field_name    = 'wpvr_video_' . $wpvr_vs[ $video_service ][ 'pid' ] . 'Id';
			$video_id      = trim( wpvr_retreive_video_id_from_param(
				$postarr[ $field_name ] ,
				$video_service
			) );
			//$video_id      = $postarr[ $field_name ];
		} else return $data;

		
		//wpvr_reset_debug();
		//wpvr_set_debug( $video_id , TRUE );
		//wpvr_set_debug( $postarr , TRUE );
		$video_meta = $wpvr_vs[ $video_service ][ 'get_single_video_data' ]( $video_id );

		if( $video_meta === FALSE ) return $data;
		/**************************** PERSO PART ******************************************/
		/*********************************************************************************/
		//d( $postarr );
		if( $video_service == 'perso' ) {
			$old_id        = get_post_meta( $post_id , 'wpvr_video_id' , TRUE );
			$data[ 'ID' ]  = $post_id;
			$video_embed   = $video_id;
			$video_service = 'perso';
			$video_id      = ( $old_id != '' ) ? $old_id : md5( uniqid( rand() , TRUE ) );

			update_post_meta( $post_id , 'wpvr_video_id' , $video_id );
			//update_post_meta( $post_id , 'wpvr_video_embed_code' , $video_embed );
			update_post_meta( $post_id , 'wpvr_video_service' , $video_service );
			update_post_meta( $post_id , 'wpvr_video_enableManualAdding' , 'off' );

			//Datafillers
			wpvr_run_dataFillers( $post_id );
			do_action( 'wpvr_event_run_dataFillers' , $post_id );

			//WPVR Hooks
			do_action( 'wpvr_event_manually_add_video' , $video_meta , $post_id );
			do_action( 'wpvr_event_add_video' , $video_meta , $post_id );

			wpvr_add_notice( array(
				'title'       => 'WP Video Robot' ,
				//'class'     => 'updated' , //updated or warning or error
				'content'     => $wpvr_vs[ $video_service ][ 'msgs' ][ 'import_success' ] ,
				'hidable'     => TRUE ,
				'is_dialog'   => FALSE ,
				'show_once'   => TRUE ,
				'single_line' => TRUE ,
				'color'       => '#09B189' ,
				'icon'        => 'fa-thumbs-up' ,
			) );

			$wpvr_imported[ $video_service ][ $video_id ] = $post_id;
			update_option( 'wpvr_imported' , $wpvr_imported );


			return $data;
		}
		/*********************************************************************************/
		/**************************** PERSO PART ******************************************/

		$mOptions = array(
			'getTitle'    => TRUE ,
			'getDesc'     => TRUE ,
			'getThumb'    => TRUE ,
			'getTags'     => TRUE ,
			'getPostDate' => TRUE ,
		);
		
		if( isset( $postarr[ 'wpvr_video_getThumb' ] ) && $postarr[ 'wpvr_video_getThumb' ] == 'on' )
			$mOptions[ 'getThumb' ] = TRUE;
		else $mOptions[ 'getThumb' ] = FALSE;
		
		if( isset( $postarr[ 'wpvr_video_getTitle' ] ) && $postarr[ 'wpvr_video_getTitle' ] == 'on' )
			$mOptions[ 'getTitle' ] = TRUE;
		else $mOptions[ 'getTitle' ] = FALSE;
		
		if( isset( $postarr[ 'wpvr_video_getDesc' ] ) && $postarr[ 'wpvr_video_getDesc' ] == 'on' )
			$mOptions[ 'getDesc' ] = TRUE;
		else $mOptions[ 'getDesc' ] = FALSE;
		
		if( isset( $postarr[ 'wpvr_video_getTags' ] ) && $postarr[ 'wpvr_video_getTags' ] == 'on' )
			$mOptions[ 'getTags' ] = TRUE;
		else $mOptions[ 'getTags' ] = FALSE;

		if( isset( $postarr[ 'wpvr_video_getPostDate' ] ) && $postarr[ 'wpvr_video_getPostDate' ] == 'on' )
			$mOptions[ 'getPostDate' ] = TRUE;
		else $mOptions[ 'getPostDate' ] = FALSE;
		

		//update_post_meta( $post_id , 'wpvr_video_id' , $video_id );
		update_post_meta( $post_id , 'wpvr_video_id' , $video_meta[ 'id' ] );
		update_post_meta( $post_id , 'wpvr_video_duration' , $video_meta[ 'duration' ] );
		
		update_post_meta( $post_id , 'wpvr_sourceId' , '' );
		update_post_meta( $post_id , 'wpvr_sourceName' , 'Manual Adding' );
		update_post_meta( $post_id , 'wpvr_sourceType' , 'Manual' );
		
		update_post_meta( $post_id , 'wpvr_video_service_url' , $video_meta[ 'url' ] );
		update_post_meta( $post_id , 'wpvr_video_service_views' , $video_meta[ 'views' ] );
		update_post_meta( $post_id , 'wpvr_video_service_likes' , $video_meta[ 'likes' ] );
		update_post_meta( $post_id , 'wpvr_video_service_dislikes' , $video_meta[ 'dislikes' ] );
		update_post_meta( $post_id , 'wpvr_video_service_thumb' , $video_meta[ 'thumb' ] );
		update_post_meta( $post_id , 'wpvr_video_service_icon' , $video_meta[ 'icon' ] );
		update_post_meta( $post_id , 'wpvr_video_service_desc' , $video_meta[ 'desc' ] );

		$postarr[ $field_name ] = $video_meta[ 'id' ];
		
		$data[ 'ID' ] = $post_id;
		//$data[ $field_name ] = $video_meta['id'] ;

		wpvr_run_dataFillers( $post_id );
		do_action( 'wpvr_event_run_dataFillers' , $post_id );
		//title ?
		if( $mOptions[ 'getTitle' ] ) {
			$data[ 'post_title' ] = $video_meta[ 'title' ];
			$data[ 'post_name' ]  = sanitize_title( $video_meta[ 'title' ] );
		}
		
		//tags ?
		global $wpvr_tags_fix;
		if( $mOptions[ 'getTags' ] ) {
			if( is_array( $video_meta[ 'tags' ] ) ) $video_meta[ 'tags' ] = implode( ',' , $video_meta[ 'tags' ] );
			$wpvr_tags_fix = $video_meta[ 'tags' ];
		}
		
		//desc ?
		if( $mOptions[ 'getDesc' ] ) $data[ 'post_content' ] = $video_meta[ 'desc' ];
		
		
		//original post date ?
		if( $mOptions[ 'getPostDate' ] ) $data[ 'post_date' ] = $video_meta[ 'originalPostDate' ];
		
		
		//Thumb ?	
		if( $mOptions[ 'getThumb' ] ) {
			//$image_url = $video_meta[ 'thumb' ];
			
			if( $video_meta[ 'hqthumb' ] === FALSE ) $working_thumb = $video_meta[ 'thumb' ];
			else $working_thumb = $video_meta[ 'hqthumb' ];

			$featured_image_file     = wpvr_download_featured_image(
				$working_thumb ,
				$video_meta[ 'title' ] ,
				$video_meta[ 'desc' ] ,
				$post_id
			);
			$video_meta[ 'service' ] = $video_service;
			do_action( 'wpvr_event_add_video_thumbnail' , $video_meta , $post_id , $featured_image_file );

		}

		do_action( 'wpvr_event_add_video' , $video_meta , $post_id );
		wpvr_add_notice( array(
			'title'       => 'WP Video Robot' ,
			//'class'     => 'updated' , //updated or warning or error
			'content'     => $wpvr_vs[ $video_service ][ 'msgs' ][ 'import_success' ] ,
			'hidable'     => TRUE ,
			'is_dialog'   => FALSE ,
			'show_once'   => TRUE ,
			'single_line' => TRUE ,
			'color'       => '#09B189' ,
			'icon'        => 'fa-thumbs-up' ,
		) );


		$wpvr_imported[ $video_service ][ $video_id ] = $post_id;

		update_option( 'wpvr_imported' , $wpvr_imported );
		update_post_meta( $post_id , 'wpvr_video_enableManualAdding' , 'off' );

		return $data;
	}
	
	add_action( 'save_post' , 'wpvr_tags_fix_function' );
	function wpvr_tags_fix_function( $post_id ) {
		global $wpvr_tags_fix;
		if( ! ( empty( $wpvr_tags_fix ) ) ) {
			wp_set_object_terms( $post_id , $wpvr_tags_fix , 'post_tag' );
		}
	}
	
	
	/* HAck to allow Empty Video Title Adding */
	add_filter( 'pre_post_title' , 'wpvr_allow_empty_video_title_function' );
	add_filter( 'pre_post_content' , 'wpvr_allow_empty_video_title_function' );
	function wpvr_allow_empty_video_title_function( $value ) {
		if( empty( $value ) ) {
			return ' ';
		}

		return $value;
	}
	
	/* HAck to allow Empty Video Title Adding */
	add_filter( 'wp_insert_post_data' , 'wpvr_unmask_empty' );
	function wpvr_unmask_empty( $data ) {
		if( ' ' == $data[ 'post_title' ] ) $data[ 'post_title' ] = '';
		if( ' ' == $data[ 'post_content' ] ) $data[ 'post_content' ] = '';

		return $data;
	}
	
	/* Register 'INVALID' custom post status */
	add_action( 'init' , 'wpvr_video_status_invalid' );
	function wpvr_video_status_invalid() {
		register_post_status(
			'invalid' ,
			array(
				'label'                     => __( 'Invalid' , WPVR_LANG ) ,
				'public'                    => FALSE ,
				'show_in_admin_all_list'    => TRUE ,
				'show_in_admin_status_list' => TRUE ,
				'label_count'               => _n_noop( 'Invalid <span class="count">(%s)</span>' , 'Invalid <span class="count">(%s)</span>' ) ,
			)
		);
	}
	
	/* Add INVALID LABEL on invalid videos */
	add_action( 'admin_footer-post.php' , 'wpvr_video_status_invalid_list' );
	function wpvr_video_status_invalid_list() {
		global $post;
		$complete = '';
		$label    = '';
		if( $post->post_type == WPVR_VIDEO_TYPE ) {
			if( $post->post_status == 'invalid' ) {
				$complete = ' selected="selected"';
				$label    = '<span id="post-status-display"> Invalid </span>';
			}
			?>
			<script>
				//jQuery(document).ready(function($){
				jQuery("select#post_status").append("<option value='invalid' <?php echo $complete; ?> ><?php _e( 'Invalid' , WPVR_LANG ); ?></option>");
				jQuery(".misc-pub-section label").append("<?php echo $label; ?>");
				//});
			</script>
			<?php
		}
	}
	
	/* Return vodeo state if invalid */
	add_filter( 'display_post_states' , 'wpvr_video_status_invalid_state' );
	function wpvr_video_status_invalid_state( $states ) {
		global $post;
		$arg = get_query_var( 'post_status' );
		if( $arg != 'invalid' ) {
			if( $post->post_status == 'invalid' ) {
				return array( 'INVALID !' );
			}
		}

		return $states;
	}
	
	/* Add invalid status option on screen */
	add_action( 'admin_footer-edit.php' , 'wpvr_video_status_invalid_bulk' );
	function wpvr_video_status_invalid_bulk() {
		?>
		<script>
			//jQuery(document).ready(function($){
			jQuery(".inline-edit-status select ").append("<option value='invalid'><?php _e( 'Invalid' , WPVR_LANG ); ?></option>");
			//});
		</script>
		<?php
	}
	
	/* Create authors dropdown to filter videos */
	add_action( 'restrict_manage_posts' , 'wpvr_create_video_authors_dropdown' );
	function wpvr_create_video_authors_dropdown() {
		$type = 'post';
		if( isset( $_GET[ 'post_type' ] ) ) $type = $_GET[ 'post_type' ];
		
		if( $type == WPVR_VIDEO_TYPE ) {
			$authorsArray = wpvr_get_authors( $invert = FALSE , $default = FALSE );
			?>
			<select name = "video_author">
				<option value = ""><?php _e( 'Show all authors' , WPVR_LANG ); ?></option>
				<?php
					$current_v = isset( $_GET[ 'video_author' ] ) ? $_GET[ 'video_author' ] : '';
					foreach ( $authorsArray as $label => $value ) {
						printf(
							'<option value="%s"%s>%s</option>' ,
							$value ,
							$value == $current_v ? ' selected="selected"' : '' ,
							$label
						);
					}
				?>
			</select>
			<?php
		}
	}


	/* Create authors dropdown to filter videos */
	add_action( 'restrict_manage_posts' , 'wpvr_create_video_services_dropdown' );
	function wpvr_create_video_services_dropdown() {
		global $wpvr_vs;
		$type = 'post';
		if( isset( $_GET[ 'post_type' ] ) ) $type = $_GET[ 'post_type' ];
		//d( $wpvr_vs );
		if( $type == WPVR_VIDEO_TYPE ) {

			?>
			<select name = "service">
				<option value = ""><?php _e( 'Show all services' , WPVR_LANG ); ?></option>
				<?php
					$current_v = isset( $_GET[ 'service' ] ) ? $_GET[ 'service' ] : '';
					foreach ( $wpvr_vs as $id => $vs ) {
						printf(
							'<option value="%s"%s>%s</option>' ,
							$id ,
							$id == $current_v ? ' selected="selected"' : '' ,
							$vs[ 'label' ]
						);
					}
				?>
			</select>
			<?php
		}
	}

	
	/* Filter videos by author or restrict to owners and admin*/
	add_filter( 'parse_query' , 'wpvr_video_filter_by_author' );
	function wpvr_video_filter_by_author( $query ) {
		global $pagenow;
		global $wpvr_options;
		$current_user_id = get_current_user_id();
		
		$query_type = array();
		if( isset( $_GET[ 'post_type' ] ) ) $type = $_GET[ 'post_type' ];
		else $type = 'post';
		
		if( $type != WPVR_SOURCE_TYPE && $type != WPVR_VIDEO_TYPE ) return $query;
		if( $wpvr_options[ 'restrictVideos' ] && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			$query->query_vars[ 'author' ] = $current_user_id;
			
			$current_user_sources = count_many_users_posts( array( $current_user_id ) , WPVR_SOURCE_TYPE , FALSE );
			$current_user_videos  = count_many_users_posts( array( $current_user_id ) , WPVR_VIDEO_TYPE , FALSE );
			
			
			if( $current_user_sources[ $current_user_id ] == 0 || $current_user_videos[ $current_user_id ] == 0 ) {
				add_action( 'admin_notices' , 'wpvr_show_restriction_msg' );
				function wpvr_show_restriction_msg() {
					?>
					<div class = "error warning">
						<b><?php _e( 'WP Video Robot WARNING' , WPVR_LANG ); ?></b> : <br/>
						
						<p>
							<b><?php _e( 'Restriction mode is ON' , WPVR_LANG ); ?></b><br/>
							<?php _e( 'You can view or edit your own sources and videos only unless you have Admin role.' , WPVR_LANG ); ?>
						</p>
						
						<div class = "wpvr_clearfix"></div>
					</div>
					
					<?php
				}
			}
			
		}

		//Filtering by author
		if(
			WPVR_VIDEO_TYPE == $type
			&& is_admin()
			&& $pagenow == 'edit.php'
			&& isset( $_GET[ 'video_author' ] )
			&& $_GET[ 'video_author' ] != ''
		) {
			$query->query_vars[ 'author' ] = $_GET[ 'video_author' ];
		}

		//Filtering by service
		if(
			WPVR_VIDEO_TYPE == $type
			&& is_admin()
			&& $pagenow == 'edit.php'
			&& isset( $_GET[ 'service' ] )
			&& $_GET[ 'service' ] != ''
		) {
			//$query->query_vars[ 'author' ] = $_GET[ 'video_author' ];
			$query->set( 'meta_key' , 'wpvr_video_service' );
			$query->set( 'meta_value' , $_GET[ 'service' ] );
		}


		return $query;
	}
	
	
	/* Hiding sources of Inactive services */
	add_filter( 'parse_query' , 'wpvr_show_only_active_services_videos' );
	function wpvr_show_only_active_services_videos( $query ) {
		global $wpvr_vs_ids;
		
		if( $query->get( 'post_type' ) == WPVR_VIDEO_TYPE ) {
			$query->query_vars[ 'meta_query' ] = array(
				array(
					'key'     => 'wpvr_video_service' ,
					'value'   => $wpvr_vs_ids[ 'ids' ] ,
					'compare' => 'IN' ,
				) ,
			);
		}

		return $query;
	}