<?php
	/* Require Ajax WP load */
	if( isset( $_GET[ 'wpvr_wpload' ] ) || isset( $_POST[ 'wpvr_wpload' ] ) ) {
		define( 'DOING_AJAX' , TRUE );
		//define('WP_ADMIN', true );
		$wpload = 'wp-load.php';
		while( ! is_file( $wpload ) ) {
			if( is_dir( '..' ) ) chdir( '..' );
			else die( 'EN: Could not find WordPress! FR : Impossible de trouver WordPress !' );
		}
		require_once( $wpload );
	}

	if( isset( $_GET[ 'get_all_duplicates' ] ) ) {
		$duplicates = wpvr_get_all_duplicates();
		if( $duplicates === FALSE ) {
			echo wpvr_get_json_response( 'There is no duplicates.' , 0 , 'Duplicates Returned.' );

			return FALSE;
		}
		$count = count( $duplicates );
		$msg   = "<strong>" . count( $duplicates ) . "</strong> duplicate(s) found. Do you want to continue ?";
		echo wpvr_get_json_response( $msg , 1 , 'Duplicates Returned.' );

		return FALSE;
	}
	if( isset( $_GET[ 'merge_all_duplicates' ] ) ) {
		$merge = wpvr_async_merge_all_dups();

		_d( $merge );
		//$msg   = "<strong>" . count( $duplicates ) . "</strong> duplicate(s) found. Do you want to continue ?";
		echo wpvr_get_json_response( $merge[ 'items' ] , 1 , 'Duplicates merged.' );

		return FALSE;
	}


	global $wpvr_vs;

	//global $wpvr_options , $wpvr_default_options , $wpvr_token;
	$wpvr_url        = WPVR_MANAGE_URL;
	$wpvr_bulk_url   = WPVR_IMPORT_URL;
	$wpvr_url_export = WPVR_ACTIONS_URL;
	$wpvr_dups_url   = admin_url( 'admin.php?page=wpvr&section=duplicates' , 'http' );

	global $wpvr_pages;
	$wpvr_pages = TRUE;

	global $is_DT;

	if( isset( $_GET[ 'merge_items' ] ) ) {
		global $wpvr_imported;
		$items = $_POST[ 'items' ];
		//_d( $items );
		//return false;
		if( $items == 'all' ) $items = array();
		$duplicates = wpvr_get_duplicate_videos(
			$items ,
			$limit = FALSE ,
			$debug = FALSE
		);
		//_d( count( $duplicates ) );
		//_d( ( $duplicates ) );
		$cleaner = wpvr_prepare_duplicate_videos( $duplicates , TRUE );
		//_d( $cleaner );
		$done = wpvr_process_duplicate_videos( $cleaner , TRUE );
		//_d( $done );


		global $wpvr_imported;

		update_option( 'wpvr_deferred' , array() );
		update_option( 'wpvr_deferred_ids' , array() );
		update_option( 'wpvr_imported' , array() );

		$imported      = wpvr_update_imported_videos();
		$wpvr_imported = get_option( 'wpvr_imported' );

		echo wpvr_get_json_response( $done );

		return FALSE;
	}

	if( isset( $_GET[ 'import_videos' ] ) ) {
		global $wpvr_imported;

		//new dBug( $_FILES );
		//new dBug( $_POST );

		$r = array(
			'status'   => '' ,
			'count'    => 0 ,
			'countDup' => 0 ,
			'items'    => '' ,
			'version'  => '' ,
			'type'     => '' ,
		);

		$imported_file_name = "tmp_import_" . mt_rand( 0 , 1000 );
		$imported_file      = WPVR_TMP_PATH . $imported_file_name;

		if( move_uploaded_file( $_FILES[ 'uploadedfile' ][ 'tmp_name' ] , $imported_file ) ) {
			//echo "The file ".  $_FILES['uploadedfile']['name']. " has been uploaded";
		} else {
			_e( "There was an error uploading the file, please try again!" , WPVR_LANG );

			return FALSE;
		}

		$json_data = file_get_contents( $imported_file );
		$json      = (array) json_decode( $json_data );
		unlink( $imported_file );


		if( ! isset( $json[ 'version' ] ) || ! isset( $json[ 'data' ] ) || ! isset( $json[ 'type' ] ) || $json[ 'type' ] != 'videos' ) {
			$r[ 'status' ] = 'invalid';
			echo json_encode( $r );

			return FALSE;
		}

		$ids       = array();
		$tmp       = array();
		$count_dup = 0;
		//new dBug( $json['data'] );
		foreach ( $json[ 'data' ] as $k => $v ) {
			$service  = $json[ 'data' ][ 'k' ]->__service;
			$video_id = $json[ 'data' ][ 'k' ]->__video_id;


			if( $_POST[ 'skipDup' ] != 'yes' || ( $_POST[ 'skipDup' ] == 'yes' && ! isset( $wpvr_imported[ $service ][ $video_id ] ) ) ) {

				$json[ 'data' ][ $k ]->skipDup     = $_POST[ 'skipDup' ];
				$json[ 'data' ][ $k ]->publishDate = $_POST[ 'publishDate' ];
				$json[ 'data' ][ $k ]->resetViews  = $_POST[ 'resetViews' ];

				$ids[]     = $k;
				$tmp[ $k ] = $json[ 'data' ][ $k ];
			} else {
				$count_dup ++;
			}
		}


		$r[ 'status' ]   = 'ok';
		$r[ 'version' ]  = $json[ 'version' ];
		$r[ 'type' ]     = $json[ 'type' ];
		$r[ 'items' ]    = $ids;
		$r[ 'count' ]    = count( $json[ 'data' ] );
		$r[ 'countDup' ] = $count_dup;


		$_SESSION[ 'wpvr_tmp_import' ] = $tmp;

		//new dBug( $r['items'] );return false;
		echo wpvr_get_json_response( $r );

		//echo json_encode( $r );


		return FALSE;
	}

	if( isset( $_GET[ 'get_video_preview' ] ) ) {
		$player_code = wpvr_video_embed(
			$_GET[ 'video_id' ] ,
			$_GET[ 'post_id' ] ,
			$autoPlay = TRUE ,
			$_GET[ 'service' ] ,
			'preview' ,
			array() ,
			array()
		);
		//$player_code = apply_filters('wpvr_replace_player_code' , $player_code , $_GET[ 'post_id' ] );
		//_d( $player_code );
		echo wpvr_get_json_response( $player_code );

		return FALSE;
	}

	if( isset( $_GET[ 'bulk_single_action' ] ) ) {
		$id     = $_POST[ 'video_id' ];
		$action = $_POST[ 'action' ];

		global $wpvr_imported;
		switch ( $action ) {
			case 'delete' :
				$video_id      = get_post_meta( $id , 'wpvr_video_id' , TRUE );
				$video_service = get_post_meta( $id , 'wpvr_video_service' , TRUE );
				$r             = wp_delete_post( $id , TRUE );
				unset( $wpvr_imported[ $video_service ][ $video_id ] );

				if( $r === FALSE ) $r = 'error';
				else $r = 'ok';
				break;
			case 'publish' :
				$status = get_post_status( $id );
				if( $status != 'publish' ) $r = wp_update_post( array( 'ID' => $id , 'post_status' => 'publish' ) );
				else $r = 'skipped';
				if( $r == 0 ) $r = 'error';
				break;
			case 'trash' :
				$status = get_post_status( $id );
				if( $status != 'trash' ) $r = wp_update_post( array( 'ID' => $id , 'post_status' => 'trash' ) );
				else $r = 'skipped';
				if( $r == 0 ) $r = 'error';
				break;
			case 'draft' :
				$status = get_post_status( $id );
				if( $status != 'draft' ) $r = wp_update_post( array( 'ID' => $id , 'post_status' => 'draft' ) );
				else $r = 'skipped';
				if( $r == 0 ) $r = 'error';
				break;
			case 'untrash' :
				$status = get_post_status( $id );
				if( $status != 'publish' ) $r = wp_update_post( array( 'ID' => $id , 'post_status' => 'publish' ) );
				else $r = 'skipped';
				if( $r == 0 ) $r = 'error';
				break;
			case 'pending' :
				$status = get_post_status( $id );
				if( $status != 'pending' ) $r = wp_update_post( array( 'ID' => $id , 'post_status' => 'pending' ) );
				else $r = 'skipped';
				if( $r == 0 ) $r = 'error';
				break;
			default:
				$r = 'noaction';
				break;
		}
		echo wpvr_get_json_response( $r , 1 , 'Bulk Action processed' );

		return FALSE;
	}
	if( isset( $_GET[ 'export_videos' ] ) ) {
		
		$ids = $_POST[ 'bulk_ids' ];

		//wpvr_remove_tmp_files();
		
		$videos = wpvr_get_videos( array(
			'ids'         => $ids ,
			'order'       => 'views' ,
			'meta_suffix' => TRUE ,
		) );

		//new dBug( $videos );

		$json_videos = json_encode( array(
			'data'    => $videos ,
			'version' => WPVR_VERSION ,
			'type'    => 'videos' ,
		) );
		$file        = "tmp_export_" . mt_rand( 0 , 1000 ) . '_@_videos';
		file_put_contents( WPVR_TMP_PATH . $file , $json_videos );
		$export_url = get_option( 'siteurl' ) . "/wpvr_export/" . $file;

		echo $export_url;

		return FALSE;
	}

	if( isset( $_GET[ 'export_all_videos' ] ) ) {
		
		//$ids = $_POST['bulk_ids'];

		//wpvr_remove_tmp_files();
		
		$videos = wpvr_get_videos( array(
			'meta_suffix' => TRUE ,
		) );


		$json_videos = json_encode( array(
			'data'    => $videos ,
			'version' => WPVR_VERSION ,
			'type'    => 'videos' ,
		) );
		$file        = "tmp_export_" . mt_rand( 0 , 1000 ) . '_@_videos';
		file_put_contents( WPVR_TMP_PATH . $file , $json_videos );
		$export_url = get_option( 'siteurl' ) . "/wpvr_export/" . $file;

		echo $export_url;

		return FALSE;
		
	}

	if( isset( $_GET[ 'refresh_manage_videos' ] ) ) {

		global $wpvr_status , $wpvr_services;
		global $wpvr_is_admin , $wpvr_options;

		$wpvr_is_admin = TRUE;

		if( isset( $_POST[ 'manage_layout' ] ) ) $layout = $_POST[ 'manage_layout' ];
		else    $layout = WPVR_MANAGE_LAYOUT;


		if( isset( $_GET[ 'filter_page' ] ) ) $filter_page = $_GET[ 'filter_page' ];
		if( isset( $_POST[ 'filter_page' ] ) ) $filter_page = $_POST[ 'filter_page' ];
		if( ! isset( $_GET[ 'filter_page' ] ) && ! isset( $_GET[ 'filter_page' ] ) ) $filter_page = 1;


		$args = array(
			'page'    => $filter_page ,
			'perpage' => $wpvr_options[ 'videosPerPage' ] ,
		);


		if( isset( $_POST[ 'filter_dates' ] ) ) $args[ 'date' ] = $_POST[ 'filter_dates' ];
		if( isset( $_POST[ 'filter_services' ] ) ) $args[ 'service' ] = $_POST[ 'filter_services' ];
		if( isset( $_POST[ 'filter_statuses' ] ) ) $args[ 'status' ] = $_POST[ 'filter_statuses' ];
		if( isset( $_POST[ 'filter_search' ] ) ) $args[ 'search' ] = $_POST[ 'filter_search' ];
		if( isset( $_POST[ 'filter_authors' ] ) ) $args[ 'author' ] = $_POST[ 'filter_authors' ];
		if( isset( $_POST[ 'filter_order' ] ) ) $args[ 'order' ] = $_POST[ 'filter_order' ];
		if( isset( $_POST[ 'filter_orderby' ] ) ) $args[ 'orderby' ] = $_POST[ 'filter_orderby' ];
		if( isset( $_POST[ 'filter_categories' ] ) ) $args[ 'category' ] = $_POST[ 'filter_categories' ];

		if( isset( $_POST[ 'dupsBy' ] ) ) {
			$args[ 'dupsBy' ] = $_POST[ 'dupsBy' ];
		}

		//$args['dupsBy'] = 'video_id';

		//echo "YASSINE";
		//d( $wpvr_vs );
		$return = wpvr_manage_videos( $args );

		//new dBug( $return );

		if( $return[ 'items_type' ] == 'duplicates' ) $dups = TRUE;
		else $dups = FALSE;


		if( $return[ 'total_results' ] == 0 || count( $return[ 'items' ] ) == 0 ) {
			//$return['html'] = ' NO RES !';
			echo wpvr_get_json_response( $return );

			return FALSE;
		}

		$return[ 'html' ] = '';

		// #wpvr_manage_videos
		$return[ 'html' ] .= '<div class="' . $layout . '" id="wpvr_manage_videos" url="' . $wpvr_url . '" url_export="' . $wpvr_url . '">';

		// .wpvr_manage_bulk_form
		$return[ 'html' ] .= '<div class="wpvr_manage_bulk_form" action="">';

		foreach ( $return[ 'items' ] as $item ) {

			//$showMe = $return['items_type'];

			if( $dups ) {
				$x              = explode( ',' , $item->ids );
				$showMe         = $x[ 0 ];
				$item_thumb_img = get_the_post_thumbnail( $x[ 0 ] , 'wpvr_hard_thumb' , array( 'css' => 'wpvr_video_thumb_img' ) );
			} else {
				//$item_thumb_img = get_the_post_thumbnail( $item->post_id , 'wpvr_hard_thumb' );
				$item_thumb_img = wp_get_attachment_image( get_post_thumbnail_id( $item->post_id ) , 'wpvr_hard_thumb' );
			}

			//new dBug( $thumb );
			$item_author = $item_categories = $item_postdate = '';

			if( $item_thumb_img == '' ) $item_thumb_img = '<img src="' . WPVR_NO_THUMB . '" />';

			//new dBug( $item );

			$item_duration   = wpvr_get_duration_string( $item->duration );
			$item_embed_code = wpvr_video_embed( $item->id , $autoPlay = TRUE , $item->service );
			$hideIt          = array();
			if( $item->duration == '' ) $hide[ 'duration' ] = 'hideIt';
			else $hide[ 'duration' ] = '';

			if( $item->status == '' ) $hide[ 'status' ] = 'hideIt';
			else $hide[ 'status' ] = '';

			if( $item->views == '' ) $hide[ 'views' ] = 'hideIt';
			else $hide[ 'views' ] = '';

			if( $item->service == '' ) $hide[ 'service' ] = 'hideIt';
			else $hide[ 'service' ] = '';

			/*
			if( strlen($item->description) > 500 )
				$item_description = substr(  strip_tags($item->description ) , 0 , min(500,strlen($item->description)) ) . ' <b>[...]</b>' ;
			else
				$item_description =   ($item->description ) ;
			*/
			$item_description = ( $item->description );
			if( $item->title == '' ) $item->title = '# ' . __( 'Untitled' , WPVR_LANG ) . ' #';
			if( $item->service == '' ) $item->service = 'unknown';

			// .wpvr_video
			$return[ 'html' ] .= '<div class="wpvr_video pull-left ' . $item->status . ' " id="video_' . $item->post_id . '" >';

			// .wpvr_video_cb
			$return[ 'html' ] .= '<input type="checkbox" class="wpvr_video_cb" name="bulk_ids[]" value ="' . $item->post_id . '" />';

			// .wpvr_video_head
			$return[ 'html' ] .= '<div class="wpvr_video_head">';
			
			$return[ 'html' ]
				.= '
				<div class = "wpvr_video_checked"><i class = "fa fa-check"></i></div>
			';
			

			// .wpvr_video_buttons
			$return[ 'html' ] .= '<div class="wpvr_video_buttons">';

			if( $dups ) {
				// .wpvr_video_merge
				$return[ 'html' ] .= '<div class="wpvr_video_merge pull-left noMargin" url="' . $wpvr_url . '" ids="' . $item->ids . '" views="' . $item->views . '">';
				$return[ 'html' ] .= '<i class="fa fa-magic" ></i><br/>' . __( 'Merge' , WPVR_LANG );
				$return[ 'html' ] .= '</div>'; // .wpvr_video_merge
			} else {
				// .wpvr_video_edit
				$return[ 'html' ] .= '<div class="wpvr_video_edit pull-left noMargin" link="' . get_edit_post_link( $item->post_id ) . '">';
				$return[ 'html' ] .= '<i class="fa fa-pencil" ></i><br/>' . __( 'Edit' , WPVR_LANG );
				$return[ 'html' ] .= '</div>'; // .wpvr_video_edit
			}

			// .wpvr_video_view
			$return[ 'html' ] .= '<div class="wpvr_video_view pull-right noMargin" url="' . $wpvr_url . '" post_id = "' . $item->post_id . '" service="' . $item->service . '" video_id="' . $item->id
			                     . '">';
			$return[ 'html' ] .= '<i class="fa fa-eye" ></i><br/>' . __( 'Preview' , WPVR_LANG );
			$return[ 'html' ] .= '</div>'; // .wpvr_video_view

			$return[ 'html' ] .= '<div class="wpvr_clearfix"></div>';

			$return[ 'html' ] .= '</div>';// .wpvr_video_buttons

			// .wpvr_service_icon
			$return[ 'html' ] .= '<div class="wpvr_service_icon ' . $item->service . ' wpvr_video_service ' . $hide[ 'service' ] . '">';
			$return[ 'html' ] .= $wpvr_vs[ $item->service ][ 'label' ];
			$return[ 'html' ] .= '</div>'; // .wpvr_service_icon


			// .wpvr_video_views
			$return[ 'html' ] .= '<div class="wpvr_video_views ' . $hide[ 'views' ] . '">' . wpvr_numberK( $item->views ) . ' ' . __( 'views' , WPVR_LANG ) . '</div>';

			if( $dups ) {
				// .wpvr_video_dupCount
				$return[ 'html' ] .= '<div class="wpvr_video_duration ' . $item->dupCount . '">' . wpvr_numberK( $item->dupCount ) . ' ' . __( 'dups' , WPVR_LANG ) . '</div>';
			} else {
				// .wpvr_video_duration
				$return[ 'html' ] .= '<div class="wpvr_video_duration ' . $hide[ 'duration' ] . '">' . $item_duration . '</div>';
			}

			// .wpvr_video_status.
			$return[ 'html' ] .= '<div class="wpvr_video_status ' . $hide[ 'status' ] . ' ' . $item->status . '">';
			$return[ 'html' ] .= '<i class="fa wpvr_video_status_icon ' . $wpvr_status[ $item->status ][ 'icon' ] . ' "></i>' . $wpvr_status[ $item->status ][ 'label' ];
			$return[ 'html' ] .= '</div>'; // .wpvr_video_status

			// .wpvr_video_thumb
			$return[ 'html' ] .= '<div class="wpvr_video_thumb ' . $item->service . '"> ' . $item_thumb_img . ' </div>';

			$return[ 'html' ] .= '</div>';  // .wpvr_video_head

			// .wpvr_video_title
			$return[ 'html' ] .= '<div class="wpvr_video_title">' . $item->title . '</div>';

			//$return['html'] .= '<div>'.$showMe.'</div>';

			if( ! $dups ) {

				// .wpvr_video_meta
				$return[ 'html' ] .= '<div class="wpvr_video_meta">';
				$return[ 'html' ] .= '<b>Posted by :</b> ' . get_the_author_meta( 'user_login' , $item->author );
				$return[ 'html' ] .= '<b>On : </b>' . ( $item->date );
				$return[ 'html' ] .= '</div>'; // .wpvr_video_meta

				//.wpvr_video_description
				$return[ 'html' ] .= '<div class="wpvr_video_description">' . $item_description . '</div>';

			}

			$return[ 'html' ] .= '</div>'; // .wpvr_video_head


		}

		$return[ 'html' ] .= '<div class="wpvr_clearfix"></div>';

		$return[ 'html' ] .= '</div>'; // #wpvr_manage_videos
		$return[ 'html' ] .= '</div>'; // .wpvr_manage_bulk_form


		$return[ 'debug' ] = '';
		echo wpvr_get_json_response( $return );

		//echo json_encode( $return );
		return FALSE;
	}

?>


<div class = "wrap wpvr_wrap" style = "display:none;">
	<?php if( ! $is_DT ) { ?>
		<?php wpvr_show_logo(); ?>
		<h2 class = "wpvr_title">
			<i class = "wpvr_title_icon fa fa-film"></i>
			<?php echo __( 'Manage Videos' , WPVR_LANG ); ?>
		</h2>
	<?php } ?>
	<div class = "wpvr_dashboard">
		<div id = "dashboard-widgets-wrap" class = "wpvr_nav_tab_content tab_c">
			<form
				class = "wpvr_manage_main_form"
				action = ""
				url = "<?php echo $wpvr_url; ?>"
				url_export = "<?php echo $wpvr_url_export; ?>"
				enctype = "multipart/form-data"
			>
				<div class = "wpvr_manage_wrapper">
					<?php if( ! $is_DT ) { ?>
						<div class = "wpvr_manage_head">

							<div class = "wpvr_manage_head_left pull-left noMargin">
								<button class = "wpvr_button pull-left wpvr_track_dups" url = "<?php echo $wpvr_dups_url; ?>">
									<i class = "wpvr_button_icon fa fa-copy"></i>
									<?php _e( 'Track Duplicates' , WPVR_LANG ); ?>
								</button>
								<button class = "wpvr_button pull-left wpvr_manage_exportAll" url = "<?php echo $wpvr_url; ?>">
									<i class = "wpvr_button_icon fa fa-upload"></i>
									<?php _e( 'Export All Videos' , WPVR_LANG ); ?>
								</button>
								<button
									class = "wpvr_button pull-left wpvr_manage_import"
									url = "<?php echo $wpvr_url; ?>"
									bulk_url = "<?php echo $wpvr_bulk_url; ?>"
									buffer = "<?php echo WPVR_BULK_IMPORT_BUFFER; ?>"
								>
									<i class = "wpvr_button_icon fa fa-download"></i>
									<?php _e( 'Import Videos' , WPVR_LANG ); ?>
								</button>
								<div class = "wpvr_manage_message"></div>

							</div>
							<div class = "wpvr_manage_head_right noMargin">
								<input class = "wpvr_manage_search_input" name = "filter_search" type = "text" placeholder = "<?php _e( 'Search Videos' , WPVR_LANG ); ?>"/>
								<button class = "wpvr_button wpvr_small wpvr_manage_search_button">
									<i class = "fa fa-search"></i> Search
								</button>
							</div>
							<div class = "wpvr_clearfix"></div>
						</div>
					<?php } ?>


					<div class = "wpvr_manage_bulk">
						<div class = "wpvr_manage_bulk_left pull-left noMargin">

							<?php if( $is_DT ) { ?>
								<button is_merging_all = "1" url = "<?php echo $wpvr_url; ?>" class = "wpvr_button wpvr_black_button pull-left wpvr_merge_selected_duplicates">
									<i class = "wpvr_button_icon fa fa-magic"></i>
									<?php _e( 'Merge all duplicates' , WPVR_LANG ); ?>
								</button>
							<?php } ?>

							<!-- manage_buttons -->
							<button class = "wpvr_button pull-left wpvr_manage_checkAll" state = "off">
								<i class = "wpvr_button_icon fa fa-check-square-o"></i>
								<?php _e( 'CHECK/UNCHECK ALL' , WPVR_LANG ); ?>
							</button>

							<!-- manage_buttons -->
						</div>
						<div class = "wpvr_manage_bulk_right  noMargin">

							<?php
								$active = array(
									'sgrid' => '' ,
									'bgrid' => '' ,
									'grid'  => '' ,
									'list'  => '' ,
								);

								$active[ WPVR_MANAGE_LAYOUT ] = 'active';

							?>


							<div class = "wpvr_manage_bulk_actions">

								<span class = "wpvr_manage_layout pull-right">
									<button class = "wpvr_icon_only wpvr_button wpvr_layout_btn <?php echo $active[ 'sgrid' ]; ?>" layout = "sgrid"><i class = "fa fa-table"></i></button>
									<button class = "wpvr_icon_only wpvr_button wpvr_layout_btn <?php echo $active[ 'bgrid' ]; ?>" layout = "bgrid"><i class = "fa fa-th"></i></button>
									<button class = "wpvr_icon_only wpvr_button wpvr_layout_btn <?php echo $active[ 'grid' ]; ?>" layout = "grid"><i class = "fa fa-th-large"></i></button>
									<button class = "wpvr_icon_only wpvr_button wpvr_layout_btn <?php echo $active[ 'list' ]; ?>" layout = "list"><i class = "fa fa-th-list"></i></button>
								</span>
								<input type = "hidden" class = "wpvr_manage_layout_hidden" name = "manage_layout" value = "<?php echo WPVR_MANAGE_LAYOUT; ?>"/>

								<?php if( $is_DT ) { ?>
									<span class = "wpvr_manage_bulk_actions_select" style = "display:none;">
										<button url = "<?php echo $wpvr_url; ?>" class = "wpvr_button pull-left wpvr_merge_selected_duplicates">
											<i class = "wpvr_button_icon fa fa-magic"></i>
											<?php echo __( 'Merge' , WPVR_LANG ); ?>
											<span class = "wpvr_count_checked"></span>
											<?php echo __( 'Selected Items' , WPVR_LANG ); ?>
										</button>
									</span>
								<?php } else { ?>
									<select class = "wpvr_manage_bulk_actions_select pull-left" style = "display:none;">
										<option class = "" value = "">
											- <?php _e( 'Bulk Actions' , WPVR_LANG ); ?> -
										</option>
										<option class = "" value = "export">
											<?php _e( 'Export' , WPVR_LANG ); ?>
										</option>
										<option class = "" value = "publish">
											<?php _e( 'Publish' , WPVR_LANG ); ?>
										</option>
										<option class = "" value = "pending">
											<?php _e( 'UnPublish' , WPVR_LANG ); ?>
										</option>
										<option class = "" value = "draft">
											<?php _e( 'Draft' , WPVR_LANG ); ?>
										</option>
										<option class = "" value = "trash">
											<?php _e( 'Trash' , WPVR_LANG ); ?>
										</option>
										<option class = "" value = "untrash">
											<?php _e( 'Restore' , WPVR_LANG ); ?>
										</option>
										<option class = "" value = "delete">
											<?php _e( 'Delete' , WPVR_LANG ); ?>
										</option>
									</select>
									<div class = "wpvr_button pull-left wpvr_manage_bulkApply" state = "off" style = "display:none;">
										<i class = "wpvr_button_icon fa fa-magic"></i>
										<?php _e( 'Apply to' , WPVR_LANG ); ?>
										<span class = "wpvr_count_checked"></span>
										<?php _e( 'items' , WPVR_LANG ); ?>
									</div>
								<?php } ?>


							</div>

						</div>
						<div class = "wpvr_clearfix"></div>
					</div>
					<div class = "wpvr_manage_sidebar pull-left noMargin">
						<?php if( FALSE ) { ?>
							<div class = "wpvr_manage_sidebar_tab pull-left noMargin active" id = "wpvr_manage_filter">
								<i class = "wpvr_manage_tab_icons  fa fa-filter"></i>
								FILTER
							</div>
							<div class = "wpvr_manage_sidebar_tab pull-right noMargin" id = "wpvr_manage_filter">
								<i class = "wpvr_manage_tab_icons fa fa-sort"></i>
								ORDER
							</div>
							<div class = "wpvr_clearfix"></div>
						<?php } ?>
						<div class = "wpvr_manage_sidebar_content">
							<?php if( ! $is_DT ) { ?>
								<div class = "wpvr_sidebar_toggle on">
									<span class = "is_on"><?php _e( 'Close All' , WPVR_LANG ); ?></span>
									<span class = "is_off"><?php _e( 'Show All' , WPVR_LANG ); ?></span>
								</div>
								
								<!-- FILTER BY SERVICE -->
								<?php $fcb_services = wpvr_manage_render_filters( 'services' ); ?>
								<?php if( $fcb_services ) { ?>
									<div class = "wpvr_manage_box open">
										<div class = "wpvr_manage_box_head">
											<i class = "fa fa-globe"></i>
											<?php _e( 'Filter by' , WPVR_LANG ); ?> <?php _e( 'Video Service' , WPVR_LANG ); ?>
											<i class = "pull-right caretDown fa fa-caret-down"></i>
											<i class = "pull-right caretUp fa fa-caret-up"></i>
										</div>
										<div class = "wpvr_manage_box_content">
											<?php echo $fcb_services; ?>
										</div>
									</div>
								<?php } ?>
								<!-- FILTER BY SERVICE -->
								<!-- FILTER BY DATES -->
								<?php $fcb_dates = wpvr_manage_render_filters( 'dates' ); ?>
								<?php if( $fcb_dates ) { ?>
									<div class = "wpvr_manage_box open">
										<div class = "wpvr_manage_box_head">
											<i class = "fa fa-clock-o"></i>
											<?php _e( 'Filter by' , WPVR_LANG ); ?> <?php _e( 'Post Dates' , WPVR_LANG ); ?>

											<i class = "pull-right caretDown fa fa-caret-down"></i>
											<i class = "pull-right caretUp fa fa-caret-up"></i>
										</div>
										<div class = "wpvr_manage_box_content">
											<?php echo $fcb_dates; ?>
										</div>
									</div>
								<?php } ?>
								<!-- FILTER BY DATES -->
								
								
								<!-- FILTER BY AUTHOR -->
								<?php $fcb_authors = wpvr_manage_render_filters( 'authors' ); ?>
								<?php if( $fcb_authors ) { ?>
									<div class = "wpvr_manage_box open">
										<div class = "wpvr_manage_box_head">
											<i class = " fa fa-user"></i>
											<?php _e( 'Filter by' , WPVR_LANG ); ?> <?php _e( 'Authors' , WPVR_LANG ); ?>

											<i class = "pull-right caretDown fa fa-caret-down"></i>
											<i class = "pull-right caretUp fa fa-caret-up"></i>
										</div>
										<div class = "wpvr_manage_box_content">
											<?php echo $fcb_authors; ?>
										</div>
									</div>
								<?php } ?>
								<!-- FILTER BY AUTHOR -->
								
								<!-- FILTER BY CAT -->
								<?php $fcb_categories = wpvr_manage_render_filters( 'categories' ); ?>
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
								
								
								<!-- FILTER BY Statuses -->
								<?php $fcb_statuses = wpvr_manage_render_filters( 'statuses' ); ?>
								<?php if( $fcb_statuses ) { ?>
									<div class = "wpvr_manage_box open">
										<div class = "wpvr_manage_box_head">
											<i class = "fa fa-tags"></i>
											<?php _e( 'Filter by' , WPVR_LANG ); ?> <?php _e( 'Video Status' , WPVR_LANG ); ?>

											<i class = "pull-right caretDown fa fa-caret-down"></i>
											<i class = "pull-right caretUp fa fa-caret-up"></i>
										</div>
										<div class = "wpvr_manage_box_content">
											<?php echo $fcb_statuses; ?>
										</div>
									</div>
								<?php } ?>
								<!-- FILTER BY Statuses -->


							<?php } ?>

							<?php if( $is_DT ) { ?>
								<!-- DUPTOOL  -->
								<div class = "wpvr_manage_box wpvrOrder open">
									<div class = "wpvr_manage_box_head ">
										<i class = "wpvr_manage_tab_icons fa fa-copy"></i>
										<?php _e( 'Duplicates Toolbox' , WPVR_LANG ); ?>
										<i class = "pull-right caretDown fa fa-caret-down"></i>
										<i class = "pull-right caretUp fa fa-caret-up"></i>

									</div>
									<div class = "wpvr_manage_box_content">
										
										<input type = "hidden" value = "0" class = "wpvr_manage_is_filtering" name = "is_filtering"/>
										<input type = "hidden" value = "video_id" class = "" name = "dupsBy"/>

										<div class = "wpvr_button wpvr_big_button wpvr_manage_refresh">
											<i class = "wpvr_button_icon fa fa-search"></i>
											<?php _e( 'Find Duplicates' , WPVR_LANG ); ?>
										</div>
									</div>
								</div>
								<!-- DUPTOOL  -->
							<?php } ?>

							<?php if( ! $is_DT ) { ?>
								<!-- ORDER  -->
								<div class = "wpvr_manage_box wpvrOrder open">
									<div class = "wpvr_manage_box_head ">
										<i class = "wpvr_manage_tab_icons fa fa-sort"></i>
										<?php _e( 'Order Results' , WPVR_LANG ); ?>
										<i class = "pull-right caretDown fa fa-caret-down"></i>
										<i class = "pull-right caretUp fa fa-caret-up"></i>

									</div>
									<div class = "wpvr_manage_box_content">

										<input type = "hidden" value = "0" class = "wpvr_manage_is_filtering" name = "is_filtering"/>

										<label>
											<?php _e( 'Order By' , WPVR_LANG ); ?>
										</label><br/>
										<select name = "filter_orderby">
											<option value = "date" selected = "selected"><?php _e( 'Date' , WPVR_LANG ); ?></option>
											<option value = "title"><?php _e( 'Title' , WPVR_LANG ); ?></option>
											<?php if( $is_DT ) { ?>
												<option value = "views"><?php _e( 'Total Views' , WPVR_LANG ); ?></option>
												<option value = "dupCount"><?php _e( 'Duplicates' , WPVR_LANG ); ?></option>
											<?php } ?>

										</select>
										<br/>
										<br/>
										<label>
											<?php _e( 'Order' , WPVR_LANG ); ?>
										</label><br/>
										<select name = "filter_order">
											<option value = "asc"><?php _e( 'Ascendant' , WPVR_LANG ); ?></option>
											<option value = "desc" selected = "selected"><?php _e( 'Descendant' , WPVR_LANG ); ?></option>
										</select>
										<br/><br/>

										<div class = "wpvr_button wpvr_manage_refresh">
											<i class = "wpvr_button_icon fa fa-sort"></i>
											<?php _e( 'Sort Results' , WPVR_LANG ); ?>
										</div>


									</div>
								</div>
								<!-- ORDER  -->
							<?php } ?>


							<div class = "wpvr_clearfix"></div>

						</div>

						
						<!-- manage_filter -->
					</div>
					<div class = "wpvr_manage_main">

						<div class = "wpvr_manage_page">
								<span class = "wpvr_manage_page_left ">
									<span class = "wpvr_manage_page_message"></span>
								</span>
								<span class = "wpvr_manage_page_right ">
									<span class = "wpvr_manage_page_select"></span>
								</span>


						</div>

						<?php
							if( ! $is_DT ) $refresh_once = "1";
							else $refresh_once = "";
						?>

						<div class = "wpvr_manage_content" is_fresh = "1" refresh_once = "<?php echo $refresh_once; ?>"></div>


						<div class = "wpvr_clearfix"></div>
					</div>
					<div class = "wpvr_clearfix"></div>

				</div>
			</form>
			<div class = "wpvr_clearfix"></div>


		</div>


	</div>
</div>