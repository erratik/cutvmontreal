<?php

	/* Require Ajax WP load */
	if( isset( $_GET[ 'cutv_wpload' ] ) || isset( $_POST[ 'cutv_wpload' ] ) ) {
		define( 'DOING_AJAX' , TRUE );
		//define('WP_ADMIN', true );
		$wpload = 'wp-load.php';
		while( ! is_file( $wpload ) ) {
			if( is_dir( '..' ) ) chdir( '..' );
			else die( 'EN: Could not find WordPress! FR : Impossible de trouver WordPress !' );
		}
		require_once( $wpload );
	}

	if( isset( $_GET[ 'import_sample_sources' ] ) ) {
		global $cutv_vs;
		$done = array(
			'total' => 0 ,
			'count' => array() ,
		);
		foreach ( (array) $cutv_vs as $vs ) {
			$done[ 'count' ][ $vs[ 'id' ] ] = 0;
			$json_file                      = CUTV_PATH . 'assets/json/' . $vs[ 'id' ] . '.json';
			$json                           = (array) json_decode( file_get_contents( $json_file ) );
			if( ! isset( $json[ 'version' ] ) || ! isset( $json[ 'data' ] ) || ! isset( $json[ 'type' ] ) || $json[ 'type' ] != 'sources' ) {
				$done[ 'detail' ][ $vs[ 'id' ] ] = 'Invalid JSON file.';
				continue;
			}

			$sources = $json[ 'data' ];
			$done[ 'total' ] += count( $sources );
			foreach ( (array) $sources as $source ) {
				$s = cutv_import_source( $source , TRUE );
				$done[ 'count' ][ $vs[ 'id' ] ] ++;
			}
			//break;
		}
		echo cutv_get_json_response(
			$done[ 'count' ] ,
			1 ,
			$done[ 'total' ] . ' ' . __( 'sample sources added' , CUTV_LANG ) . '.'
		);


		return FALSE;
	}
	if( isset( $_GET[ 'async_merge_single_dup' ] ) ) {
		$master_id   = $_GET[ 'master_id' ];
		$ids         = $_GET[ 'duplicates_id' ];
		$video_views = isset( $_GET[ 'video_views' ] ) ? $_GET[ 'video_views' ] : 0;

		$json = array(
			'ids'              => $ids ,
			'status'           => array() ,
			'count_duplicates' => 0 ,
			'count_deleted'    => 0 ,
		);
		update_post_meta( $master_id , 'cutv_video_views' , $video_views );

		foreach ( (array) $ids as $id ) {
			if( $master_id == $id ) {
				$json[ 'status' ][ $id ] = 'Master. Skip deleting.';
				continue;
			}

			$json[ 'count_duplicates' ] ++;
			$json[ 'status' ][ $id ] = wp_delete_post( $id , TRUE ) ? 'Duplicate deleted.' : 'Error deleting this duplicate.';
			$json[ 'count_deleted' ] ++;
		}

		echo json_encode( $json );

		return FALSE;
	}


	if( isset( $_GET[ 'use_helper' ] ) ) {
		global $cutv_vs;

		$helper_result = FALSE;
		$service       = $_POST[ 'service' ];

		if( ! isset( $cutv_vs[ $service ] ) ) {
			echo cutv_get_json_response( null , 0 , 'Helper ERROR' );

			return FALSE;
		}


		//_d( $_POST );
		if( $_POST[ 'helper_type' ] == 'channel' ) {
			if( isset( $cutv_vs[ $service ][ 'get_channel_id' ] ) ) {
				$helper_result = $cutv_vs[ $service ][ 'get_channel_id' ]( $_POST[ 'helper_value' ] );
			}
		} elseif( $_POST[ 'helper_type' ] == 'searchByChannel' ) {
			if( isset( $cutv_vs[ $service ][ 'get_channel_id' ] ) ) {
				$helper_result = $cutv_vs[ $service ][ 'get_channel_id' ]( $_POST[ 'helper_value' ] );
			}
		} elseif( $_POST[ 'helper_type' ] == 'page' ) {
			if( isset( $cutv_vs[ $service ][ 'get_page_id' ] ) ) {
				$helper_result = $cutv_vs[ $service ][ 'get_page_id' ]( $_POST[ 'helper_value' ] );
			}
		} elseif( $_POST[ 'helper_type' ] == 'user' ) {
			if( isset( $cutv_vs[ $service ][ 'get_user_id' ] ) ) {
				$helper_result = $cutv_vs[ $service ][ 'get_user_id' ]( $_POST[ 'helper_value' ] );
			}
		}
		//_d( $helper_result );

		if( $helper_result === FALSE ) {
			echo cutv_get_json_response( null , 0 , 'Helper Action Function not defined.' );
		} elseif( $helper_result[ 'status' ] === FALSE ) {
			echo cutv_get_json_response( null , 0 , $helper_result[ 'msg' ] );
		} else {
			echo cutv_get_json_response( $helper_result[ 'data' ] , 1 , "Helper Result Returned." );
		}

		return FALSE;
	}
	if( isset( $_GET[ 'render_async_stress_graph' ] ) ) {
		$day       = $_GET[ 'day' ];
		$daylabel  = $_GET[ 'daylabel' ];
		$daytime   = $_GET[ 'daytime' ];
		$hex_color = $_GET[ 'hex_color' ];
		$chart_id  = $_GET[ 'chart_id' ];
		//echo "<br/> $daytime";
		$date = new Datetime( $daytime );
		//$stress_data = cutv_async_get_schedule_stress( FALSE , $day );
		$stress_data = FALSE;
		$stress_data = apply_filters( 'cutv_extend_schedule_stress' , $stress_data , $date );
		if( $stress_data === FALSE ) $stress_data = cutv_get_schedule_stress( $day );
		//_d( $stress_data );
		//$stress_data = cutv_async_get_schedule_stress( FALSE , $day );

		//Videos Colors
		list( $r , $g , $b ) = sscanf( $hex_color , "#%02x%02x%02x" );

		//Sources Colors
		list( $rr , $gg , $bb ) = sscanf( '#CCC' , "#%02x%02x%02x" );

		//MAx Colors
		list( $rmax , $gmax , $bmax ) = sscanf( '#E4503C' , "#%02x%02x%02x" );

		$jsData = array(
			'name'        => 'Stress on ' . $_GET[ 'daylabel' ] ,
			'fillColor'   => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.2)' ,
			'strokeColor' => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.8)' ,
			'pointColor'  => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.8)' ,

			'fillColor_bis'   => 'rgba(' . $rr . ',' . $gg . ',' . $bb . ',0.2)' ,
			'strokeColor_bis' => 'rgba(' . $rr . ',' . $gg . ',' . $bb . ',0.8)' ,
			'pointColor_bis'  => 'rgba(' . $rr . ',' . $gg . ',' . $bb . ',0.8)' ,

			'fillColor_max'   => 'rgba(' . $rmax . ',' . $gmax . ',' . $bmax . ',0.1)' ,
			'strokeColor_max' => 'rgba(' . $rmax . ',' . $gmax . ',' . $bmax . ',0.1)' ,
			'pointColor_max'  => 'rgba(' . $rmax . ',' . $gmax . ',' . $bmax . ',0.1)' ,

			'pointHighlightFill' => 'rgba(34,34,34,0.9)' ,
			'labels'             => '' ,
			'count'              => '' ,
			'stress'             => '' ,
			'sources'            => '' ,
			'videos'             => '' ,
			'max'                => '' ,
			'chart_id'           => $chart_id ,
		);
		//print_r( $stress_data );

		foreach ( (array) $stress_data as $hour => $data ) {
			$jsData[ 'labels' ] .= ' "' . $hour . '" ,';
			$jsData[ 'count' ] .= ' ' . $data[ 'count' ] . ' ,';
			$jsData[ 'max' ] .= ' ' . CUTV_SECURITY_WANTED_VIDEOS_HOUR . ' ,';
			$jsData[ 'stress' ] .= ' ' . ( 100 * round( $data[ 'stress' ] / 800 , 2 ) ) . ' ,';
			$jsData[ 'videos' ] .= $data[ 'wanted' ] . ' ,';
			$jsData[ 'sources' ] .= $data[ 'count' ] . ' ,';
		}
		$jsData[ 'labels' ]  = '[' . substr( $jsData[ 'labels' ] , 0 , - 1 ) . ']';
		$jsData[ 'count' ]   = '[' . substr( $jsData[ 'count' ] , 0 , - 1 ) . ']';
		$jsData[ 'stress' ]  = '[' . substr( $jsData[ 'stress' ] , 0 , - 1 ) . ']';
		$jsData[ 'videos' ]  = '[' . substr( $jsData[ 'videos' ] , 0 , - 1 ) . ']';
		$jsData[ 'sources' ] = '[' . substr( $jsData[ 'sources' ] , 0 , - 1 ) . ']';
		$jsData[ 'max' ]     = '[' . substr( $jsData[ 'max' ] , 0 , - 1 ) . ']';


		$json = array();

		$json[ 'html' ]
			= '
		<div class = "cutv_graph_wrapper_" style = "width:100% !important; height:400px !important;">
			<canvas id = "' . $chart_id . '" width = "900" height = "400"></canvas>
		</div>
		';

		$json[ 'js' ] = $jsData;
		echo cutv_get_json_response( $json , 1 );

		return FALSE;


		?>

		<div class = "cutv_graph_wrapper" style = "width:100% !important; height:400px !important;">
			<canvas id = "<?php echo $graph_id; ?>" width = "900" height = "400"></canvas>
		</div>
		<script>
			var data_stress = {
				labels: <?php echo $jsData[ 'labels' ]; ?>,
				datasets: [
					{
						label: "<?php echo $jsData[ 'name' ] . ""; ?>",
						fillColor: "<?php echo $jsData[ 'fillColor' ]; ?>",
						strokeColor: "<?php echo $jsData[ 'strokeColor' ]; ?>",
						pointColor: "<?php echo $jsData[ 'pointColor' ]; ?>",
						pointHighlightFill: "<?php echo $jsData[ 'pointHighlightFill' ]; ?>",
						data: <?php echo $jsData[ 'stress' ]; ?>,
					},
				]
			};
			jQuery(document).ready(function ($) {
				cutv_draw_chart(
					$('#<?php echo $graph_id; ?>'),
					$('#<?php echo $graph_id; ?>_legend'),
					data_stress,
					'radar'
				);
			});
		</script>

		<?php
		return FALSE;
	}

	if( isset( $_GET[ 'run_single_source_before' ] ) ) {
		$token       = $_GET[ 'token' ];
		$is_autorun  = ( $_GET[ 'is_autorun' ] == 1 ) ? TRUE : FALSE;
		$tmp_sources = 'cutv_tmp_sources_' . $token;
		$tmp_done    = 'cutv_tmp_done_' . $token;
		$sources     = get_option( $tmp_sources );
		$done        = get_option( $tmp_done );
		if( $sources == '' || $done == '' ) return FALSE;
		$source = $sources[ $_GET[ 'source_id' ] ];

		$run_res = cutv_run_sources_without_adding( array( $source ) , $is_autorun , FALSE );


		$data = array(
			'name'    => $source->name ,
			'service' => $source->service ,
			'id'      => $source->id ,
			'sub_id'  => $source->sub_id ,
			'data'    => $run_res ,
		);

		echo CUTV_JS . json_encode( $data ) . CUTV_JS;

		return FALSE;
	}
	if( isset( $_GET[ 'run_single_source' ] ) ) {
		$token       = $_GET[ 'token' ];
		$tmp_sources = 'cutv_tmp_sources_' . $token;
		$tmp_done    = 'cutv_tmp_done_' . $token;
		$sources     = get_option( $tmp_sources );
		$done        = get_option( $tmp_done );
		if( $sources == '' || $done == '' ) return FALSE;
		$source = $sources[ $_GET[ 'source_id' ] ];
		//d( $source );

		$data = cutv_run_sources( array( $source ) , TRUE , FALSE );
		//d( $data );
		$data[ 'name' ]    = $source->name;
		$data[ 'service' ] = $source->service;
		$data[ 'id' ]      = $source->id;

		echo json_encode( $data );

		return FALSE;
	}


	if( isset( $_GET[ 'add_group_videos' ] ) ) {

		global $cutv_deferred_ids , $cutv_unwanted_ids , $preDuplicates , $cutv_vs;


		$token      = $_GET[ 'token' ];
		$j          = $_GET[ 'group_id' ];
		$tmp_videos = 'cutv_tmp_videos_' . $token;
		$tmp_done   = 'cutv_tmp_added_' . $token;
		$tmp_res    = 'cutv_tmp_res_' . $token;
		$videos     = get_option( $tmp_videos );
		$done       = get_option( $tmp_done );

		if( $videos == '' || $done == '' ) return FALSE;

		//$return_adding = $videos[ $j ];

		echo json_encode( cutv_add_videos( $videos[ $j ] ) );

		return FALSE;
	}
	if( isset( $_GET[ 'fetch_single_source' ] ) ) {
		global $cutv_deferred_ids , $cutv_unwanted_ids , $preDuplicates , $cutv_vs;

		$preDuplicates = array();

		$token       = $_GET[ 'token' ];
		$tmp_sources = 'cutv_tmp_sources_' . $token;
		$tmp_done    = 'cutv_tmp_done_' . $token;
		$tmp_res     = 'cutv_tmp_res_' . $token;
		$sources     = get_option( $tmp_sources );
		$done        = get_option( $tmp_done );
		//$async_duplicates = get_option( 'cutv_async_dups_' . $token );
		if( $sources == '' ) {
			echo "### SOURCES EMPTY !";

			return FALSE;
		}

		$source = $sources[ $_GET[ 'source_id' ] ];

		//d( $source );


		// FETChSource
		if( $source->postAppend == 'after' || $source->postAppend == 'before' ) {
			$vs      = $cutv_vs[ $source->service ];
			$vs_type = $vs[ 'types' ][ $source->type ];
			if( isset ( $vs[ 'get_' . $vs_type[ 'global_id' ] . '_data' ] ) ) {
				$source_data = $vs[ 'get_' . $vs_type[ 'global_id' ] . '_data' ]( $source->$vs_type[ 'param' ] );
			} else {
				$source_data = '';
			}
			if( $source_data != '' && isset( $source_data[ 'name' ] ) ) {
				$appendSourceName = $source_data[ 'name' ];
			} else {
				$appendSourceName = '';
			}
		} else {
			$appendSourceName = '';
		}


		$sOptions = array(
			'how'  => array(
				'wantedResults'    => $source->wantedVideos ,
				'onlyNewVideos'    => $source->onlyNewVideos ,
				'getVideosStats'   => $source->getVideoStats ,
				'getVideoTags'     => $source->getVideoTags ,
				'debugMode'        => $debug ,
				'postDate'         => $source->postDate ,
				'postTags'         => $source->postTags ,
				'postCats'         => $source->postCats ,
				'postAuthor'       => $source->postAuthor ,
				'autoPublish'      => $source->autoPublish ,
				'sourceName'       => $source->name ,
				'sourceId'         => $source->id ,
				'sourceType'       => $source->type ,
				'postAppend'       => $source->postAppend ,
				'postContent'      => $source->postContent ,
				'appendCustomText' => $source->appendCustomText ,
				'appendSourceName' => $appendSourceName ,
			) ,
			'what' => array(
				'era'             => $source->era ,
				'mode'            => $source->type ,
				'service'         => $source->service ,
				'order'           => $source->orderVideos ,
				'videoQuality'    => $source->videoQuality ,
				'publishedAfter'  => $source->publishedAfter ,
				'publishedBefore' => $source->publishedBefore ,
				'havingViews'     => $source->havingViews ,
				'havingLikes'     => $source->havingLikes ,
				'videoDuration'   => $source->videoDuration ,
			) ,
		);

		$sOptions = cutv_prepare_sOptions_fields( $sOptions , $source );

		$tables = cutv_prepare_tables_for_video_services(
			$cutv_imported ,
			$cutv_deferred_ids ,
			$cutv_unwanted_ids
		);
		//d( $tables );
		if( isset( $tables[ $source->service ] ) ) {
			$tables_merged = $tables[ $source->service ][ 'merged' ];
		} else {
			$tables_merged = array();
		}


		if( ! isset( $cutv_vs[ $source->service ] ) ) {
			echo "### SERVICE UNDEFINED OR DISABLED !";

			return FALSE;
		}
		$videosFound                    = array();
		$videosFound[ 'source' ]        = $source;
		$videosFound[ 'nextPageToken' ] = '';

		$timer       = cutv_chrono_time();
		$videosFound = $cutv_vs[ $source->service ][ 'fetch_videos' ](
			$videosFound ,
			$sOptions ,
			$tables_merged
		);

		$exec_time = cutv_chrono_time( $timer );

		$videosFound[ 'exec_time' ] = $exec_time;
		$videosFound[ 'source' ]    = $source;
		$videosFound[ 'ch' ]        = 'curl Resource';


		$videosFound[ 'done' ] = 1;

		$videosFound[ 'source_info' ] = array(
			'name'    => $source->name ,
			'service' => $source->service ,
			'id'      => $source->id ,
		);


		$json_encoded = json_encode( cutv_utf8_converter( $videosFound ) );
		//d( $json_encoded );
		//d( $videosFound );
		//d( json_last_error() );
		//echo json_encode( $videosFound );

		echo CUTV_JS . $json_encoded . CUTV_JS;


		return FALSE;
	}

	if( isset( $_GET[ 'single' ] ) ) {
		$today = new DateTime();
		$data  = cutv_get_schedule_stress( $today->format( 'Y-m-d' ) );
		new dBug( $data );


		return FALSE;
	}
	if( isset( $_GET[ 'filler' ] ) ) {
		$cutv_fillers = get_option( 'cutv_fillers' );
		new dBug( $cutv_fillers );

		return FALSE;
	}

	if( isset( $_GET[ 'add_remove_unwanted' ] ) ) {
		global $cutv_unwanted , $cutv_unwanted_ids;
		$post_id = $_GET[ 'post_id' ];
		if( $_GET[ 'action' ] == 'add' ) {
			cutv_unwant_videos( array( $post_id ) );
		} elseif( $_GET[ 'action' ] == 'remove' ) {
			cutv_undo_unwant_videos( array( $post_id ) );
		}
		echo cutv_get_json_response( null , 1 );

		return FALSE;
	}

	if( isset( $_GET[ 'dismiss_dialog_notice' ] ) ) {
		global $current_user;
		$user_id = $current_user->ID;

		add_user_meta( $user_id , $_GET[ 'notice_slug' ] , 'true' , TRUE );

		if( isset( $_GET[ 'has_voted' ] ) ) add_user_meta( $user_id , 'cutv_user_has_voted' , 1 , TRUE );

		echo cutv_get_json_response( 'ok' );

		return FALSE;
	}

	if( isset( $_GET[ 'register_addon_licenses' ] ) ) {
		$return = array();
		$items  = json_decode( str_replace( "\\" , "" , $_POST[ 'items' ] ) );
		//_d( $items );

		foreach ( (array) $items as $item ) {

			//_d( $item );

			$api = cutv_capi_activate(
				$item->slug ,
				$item->code ,
				'store' ,
				$_POST[ 'email' ] ,
				$_POST[ 'domain' ] ,
				$_POST[ 'url' ] ,
				$_POST[ 'ip' ] ,
				$new_cinfos = '' ,
				$item->version
			);

			//_d( $api );

			if( $api[ 'status' ] == 0 ) {
				//echo cutv_get_json_response( null , 0 , '' . $api[ 'msg' ] );
				$data[ $item->slug ] = array(
					'status' => 0 ,
					'msg'    => $api[ 'msg' ] ,
					'data'   => null ,
				);
			} else {
				$now = new Datetime();
				if( $api[ 'data' ] != null ) {
					_d( $api['data'] );
					$new_act = array(
						'act_status'  => 1 ,
						'act_product' => $item->slug ,
						'act_id'      => $api[ 'data' ]->id ,
						'act_email'   => $_POST[ 'email' ] ,
						'act_code'    => $item->code ,
						'act_date'    => $now->format( 'Y-m-d H:i:s' ) ,
						'buy_date'    => $api[ 'data' ]->buy_date ,
						'buy_user'    => $api[ 'data' ]->buy_user ,
						'buy_licence' => $api[ 'data' ]->buy_licence ,
						'buy_expires' => $api[ 'data' ]->buy_expires ,
						'act_addons'  => array() ,
						'act_url'     => $_POST[ 'url' ] ,
						'act_domain'  => $_POST[ 'domain' ] ,
						'act_version' => $item->version ,
						'act_cinfos'  => '' ,
						'act_ip'      => $_POST[ 'ip' ] ,
					);
					//_d( $new_act );return false;
					cutv_set_activation( $item->slug , $new_act );
					$data[ $item->slug ] = array(
						'status' => 1 ,
						'msg'    => 'Activated.' ,
						'data'   => $new_act ,
					);
				} else {
					$data[ $item->slug ] = array(
						'status' => 2 ,
						'msg'    => 'Activated already.' ,
						'data'   => null ,
					);
				}

			}
			//return false;
		}

		echo cutv_get_json_response( $data , 1 , '' , count( $data ) );

		return FALSE;

	}
	if( isset( $_GET[ 'reset_addons_list' ] ) ) {

		update_option( 'cutv_addons_list' , null );
		echo "reset_ok";

		return FALSE;
	}

	if( isset( $_GET[ 'reset_single_addon_licence' ] ) ) {
		global $cutv_empty_activation;
		cutv_set_activation( $_GET[ 'slug' ] , $cutv_empty_activation );
		echo cutv_get_json_response( $_GET[ 'slug' ] , 1 , 'License reset.' );

		return FALSE;
	}
	if( isset( $_GET[ 'reset_addon_licenses' ] ) ) {

		$cutv_act                 = get_option( 'cutv_activation' );
		$cutv_act[ 'act_addons' ] = array();
		update_option( 'cutv_activation' , $cutv_act );
		echo "reset_ok";

		return FALSE;
	}

	if( isset( $_GET[ 'clone_source' ] ) ) {

		$new_post_id  = cutv_duplicate_source( $_GET[ 'clone_source' ] , FALSE );
		$redirect_url = admin_url( 'post.php?post=' . $new_post_id . '&action=edit' , 'http' );
		echo "LOADING ...";
		?>
		<script>
			window.location.href = '<?php echo $redirect_url; ?>';
		</script>
		<?php
		exit;

	}

	if( isset( $_GET[ 'fake_activation' ] ) ) {
		$cutv_activation = array(
			'status'        => TRUE ,
			'date'          => '2015-04-12' ,
			'purchase_code' => '00:00:00:00:00' ,
			'id'            => 0 ,
			'email'         => 'pressaholic@gmail.com' ,
		);
		update_option( 'cutv_activation' , $cutv_activation );

		return FALSE;

	}
	if( isset( $_GET[ 'reset_activation' ] ) ) {
		cutv_set_activation( 'cutv' , array() );
		echo cutv_get_json_response( null , 1 , 'Reset completed.' );

		return FALSE;

	}

	if( isset( $_GET[ 'cancel_activation' ] ) ) {
		$act = cutv_get_activation( 'cutv' );
		if( $act === FALSE ) {
			echo cutv_get_json_response( null , 0 , 'No activation found.' );
		}
		$api = cutv_capi_cancel_activation( $act[ 'act_code' ] );
		//if( $api['statsu'] ===

		//_d( $api );

		cutv_set_activation( 'cutv' , array() );
		echo cutv_get_json_response( null , 1 , 'Reset completed.' );

		return FALSE;

	}

	if( isset( $_GET[ 'activate_copy' ] ) ) {

		$code  = $_POST[ 'code' ];
		$email = $_POST[ 'email' ];
		$id    = $_POST[ 'id' ];

		$act = cutv_get_activation( 'cutv' );
		//_d( $act );
		$api = cutv_capi_activate(
			'cutv' ,
			$_POST[ 'code' ] ,
			$_POST[ 'is_envato' ] == 1 ? 'envato' : 'local' ,
			$_POST[ 'email' ] ,
			$new_domain = $act[ 'act_domain' ] ,
			$new_url = $act[ 'act_url' ] ,
			$new_ip = $act[ 'act_ip' ] ,
			$new_cinfos = '' ,
			$new_version = $act[ 'act_version' ]
		);
		//_d( $api );return false;


		if( $api[ 'status' ] == 0 || $api[ 'status' ] == '2' ) {
			echo cutv_get_json_response( null , 0 , '' . $api[ 'msg' ] );
		} else {
			$now     = new Datetime();
			$new_act = array(
				'act_status'  => 1 ,
				'act_product' => 'cutv' ,
				'act_id'      => $api[ 'data' ] ,
				'act_email'   => $_POST[ 'email' ] ,
				'act_code'    => $_POST[ 'code' ] ,
				'act_date'    => $now->format( 'Y-m-d H:i:s' ) ,
				'buy_date'    => '' ,
				'buy_user'    => '' ,
				'buy_licence' => '' ,
				'act_addons'  => array() ,
				'act_url'     => CUTV_SITE_URL ,
				'act_domain'  => $CUTV_SERVER[ 'HTTP_HOST' ] ,
				'act_version' => CUTV_VERSION ,
				'act_cinfos'  => '' ,
				'act_ip'      => $cutv_remote_ip ,
			);
			cutv_set_activation( 'cutv' , $new_act );
			echo cutv_get_json_response( $api[ 'data' ] , 1 , 'Thanks for purchasing WP Video Robot :)' );
		}

		return FALSE;
	}

	if( isset( $_GET[ 'testMe' ] ) ) {


		return FALSE;
	}


	if( isset( $_GET[ 'reset_tables' ] ) ) {
		update_option( 'cutv_deferred' , array() );
		update_option( 'cutv_deferred_ids' , array() );
		update_option( 'cutv_imported' , array() );

		echo "TABLES RESET SUCCESSFULLY";

		return FALSE;
	}


	if( isset( $_GET[ 'show_errors' ] ) ) {
		$errors = get_option( 'cutv_errors' );
		new dBug( $errors );

		return FALSE;
	}
	if( isset( $_GET[ 'clear_errors' ] ) ) {
		update_option( 'cutv_errors' , array() );
		echo "ERRORS CLEARED";

		return FALSE;
	}


	if( isset( $_GET[ 'adapt_old_data' ] ) ) {

		if( ! isset( $_GET[ 'hard_adapt_from_version' ] ) ) $current_version = get_option( 'cutv_is_adapted' );
		else $current_version = $_GET[ 'hard_adapt_from_version' ];
		//$current_version = '1.6.65';
		//d( $current_version );

		$msg = '';
		if( version_compare( $current_version , '1.5.0' , '<' ) ) {
			// If current version is older than 1.5.0
			cutv_adapt_v15();
			$msg .= '<br/> Adapted to version 1.5';
		}

		if( version_compare( $current_version , '1.6' , '<' ) ) {
			// If current version is older than 1.5.0
			cutv_adapt_v16();
			$msg .= '<br/> Adapted to version 1.6';
		}

		if( version_compare( $current_version , '1.6.35' , '<' ) ) {
			// If current version is older than 1.5.0
			cutv_adapt_v164();
			$msg .= '<br/> Adapted to version 1.6.35';
		}

		if( version_compare( $current_version , '1.6.65' , '<' ) ) {

			$msg .= " <br/> <strong> Adapted to version 1.6.65 </strong> <div> ";
			$msg .= cutv_adapt_v1665();
			$msg .= "</div>";
		}
		if( version_compare( $current_version , '1.7' , '<' ) ) {
			// If current version is older than x

			$msg .= " <br/><strong> Adapted to version 1.7 </strong> <div>";
			$msg .= cutv_adapt_v17();
			$msg .= "</div>";

		}
		if( version_compare( $current_version , '1.7.0' , '<' ) ) {
			$msg .= " <br/> <strong> Adapted to version 1.7.0 </strong> <div> ";
			$msg .= cutv_adapt_v170();
			$msg .= "</div>";
		}
		if( version_compare( $current_version , '1.8.1' , '<' ) ) {
			$msg .= " <br/> <strong> Adapted to version 1.8.1 </strong> <div> ";
			$msg .= cutv_adapt_v181();
			$msg .= "</div>";
		}

		if( version_compare( $current_version , '1.8.2' , '<' ) ) {
			$msg .= " <br/> <strong> Adapted to version 1.8.2 </strong> <div> ";
			$msg .= cutv_adapt_v182();
			$msg .= "</div>";
		}

		if( version_compare( $current_version , '1.8.4' , '<' ) ) {
			$msg .= " <br/> <strong> Adapted to version 1.8.4 </strong> <div> ";
			$msg .= cutv_adapt_v184();
			$msg .= "</div>";
		}

		update_option( 'cutv_is_adapted' , CUTV_VERSION );

		?>
		<div class = "wrap">
			<h2 class = "cutv_title">
				<?php cutv_show_logo(); ?>
				<i class = "cutv_title_icon fa fa-magic"></i>
				<?php echo __( 'WP Video Robot' , CUTV_LANG ) . ' - ' . __( 'Adapting Old Data' , CUTV_LANG ); ?>
			</h2>

			<p>

				<div class = "updated">
			<p><?php echo __( 'All your videos and sources have been successfully adapted !' , CUTV_LANG ); ?> </p>

			<p><?php echo $msg; ?></p>
		</div>
		<br/>
		<a href = "<?php echo CUTV_DASHBOARD_URL; ?>">
			<?php echo __( 'Go Back' , CUTV_LANG ); ?>
		</a>
		</p>
		</div>
		<?php
		return FALSE;
	}

	/*
	if(isset($_GET['run_hourly'])){
		$sources = cutv_get_sources(array(
			'ids' => array(),
			'status' => 'on', //on | off | empty_string
			'type' => '',
			'scheduled' => 'now', // now | inNextHour | today | empty_string
		));

		new dBug($sources);
		cutv_run_sources($sources);

		return false;
	}
	*/


	/**** DEVELOPMENT ACTIONS */
	/**********************************************************************/

	/*HANDLING DEFERRED VIDEOS */
	if( isset( $_GET[ 'show_deferred' ] ) ) {
		$cutv_deferred = get_option( 'cutv_deferred' );

		new dBug( $cutv_deferred );

		//update_option('cutv_deferred',array());

		return FALSE;
	}
	if( isset( $_GET[ 'add_deferred' ] ) ) {
		global $cutv_options;
		$cutv_imported = get_option( 'cutv_imported' );
		$cutv_deferred = get_option( 'cutv_deferred' );
		$k             = 0;
		foreach ( $cutv_deferred as $j => $videoItem ) {
			$k ++;
			if( $k > $cutv_options[ 'deferBuffer' ] ) break;
			$videoItem[ 'origin' ] = 'by DEFERRED RUN';
			unset( $cutv_deferred[ $j ] );
			cutv_add_video( $videoItem , $cutv_imported );
		}
		echo "<br/> $k added videos";
		update_option( 'cutv_deferred' , $cutv_deferred );

		return FALSE;
	}
	if( isset( $_GET[ 'clear_deferred' ] ) ) {
		update_option( 'cutv_deferred' , array() );

		return FALSE;
	}

	/*HANDLING OLD VIDEOS */
	if( isset( $_GET[ 'update_imported' ] ) ) {
		global $cutv_imported;

		$imported      = cutv_update_imported_videos();
		$cutv_imported = get_option( 'cutv_imported' );
		//new dBug($cutv_imported);
		//new dBug($imported );
		?>
		<div class = "wrap">
			<h2 class = "cutv_title">
				<?php cutv_show_logo(); ?>
				<i class = "cutv_title_icon fa fa-magic"></i>
				<?php echo __( 'WP Video Robot' , CUTV_LANG ) . ' - ' . __( 'ANTI DUPLICATES FILTER' , CUTV_LANG ); ?>
			</h2>

			<p>

				<div class = "updated">
			<p><?php echo __( 'The anti duplicates filter is now ON.' , CUTV_LANG ); ?> </p>
		</div>
		<br/>
		<a href = "#" id = "backBtn">
			<?php echo __( 'Go Back' , CUTV_LANG ); ?>
		</a>
		</p>
		</div>
		<?php
		return FALSE;
	}
	/**********************************************************************/
	/**** DEVELOPMENT ACTIONS */


	/* Exporting ALL Videos */
	if( isset( $_GET[ 'export_all_videos' ] ) ) {


		echo "EXPORT ALL VIDEOS";

		return FALSE;
	}
	/* Exporting ALL Sources */
	if( isset( $_GET[ 'export_all_sources' ] ) ) {


		echo "EXPORT ALL SOURCES";

		return FALSE;
	}

	/* Exporting Videos */
	if( isset( $_GET[ 'export_videos' ] ) ) {
		if( isset( $_GET[ 'ids' ] ) ) $ids = explode( ',' , $_GET[ 'ids' ] );
		else return FALSE;

		cutv_remove_tmp_files();

		$videos = cutv_get_videos( array(
			'ids'         => $ids ,
			'order'       => 'views' ,
			'meta_suffix' => TRUE ,
		) );


		$json_videos = json_encode( array(
			'data'    => $videos ,
			'version' => CUTV_VERSION ,
			'type'    => 'videos' ,
		) );
		$file        = "tmp_export_" . mt_rand( 0 , 1000 ) . '_@_videos';
		file_put_contents( CUTV_TMP_PATH . $file , $json_videos );
		$export_url = get_option( 'siteurl' ) . "/cutv_export/" . $file;
		?>
		<div class = "wrap">

			<h2 class = "cutv_title">
				<?php cutv_show_logo(); ?>
				<i class = "cutv_title_icon fa fa-upload"></i>
				<?php echo __( 'WP Video Robot' , CUTV_LANG ) . ' - ' . __( 'Exporting Videos' , CUTV_LANG ); ?>
			</h2>
			<iframe id = "cutv_iframe" src = "" style = "display:none; visibility:hidden;"></iframe>
			<p>

				<div class = "updated">
			<p><?php echo __( 'Videos were successfully exported !' , CUTV_LANG ); ?> </p>
		</div>
		<br/><br/>
		<?php echo __( 'Please wait, your download will shortly begin.' , CUTV_LANG ); ?> <br/><br/>
		<a href = "#" id = "backBtn">
			<?php echo __( 'Go Back' , CUTV_LANG ); ?>
		</a>
		</p>
		</div>
		<script>
			jQuery('#cutv_iframe').attr('src', "<?php echo $export_url; ?>");
			jQuery('#backBtn').click(function (e) {
				window.history.go(-1);
				e.preventDefault();
				return false;
			});
		</script>
		<?php
		return FALSE;
	}

	/*Retrieve channel ID */
	if( isset( $_GET[ 'remove_tmp' ] ) ) {
		cutv_remove_tmp_files();
		echo "tmp files removed !";

		return FALSE;
	}

	/*Retrieve channel ID */
	if( isset( $_GET[ 'retrieve_channel' ] ) ) {
		global $cutv_vs;
		$username = $_POST[ 'username' ];
		$service  = $_POST[ 'service' ];
		$channel  = $cutv_vs[ $service ][ 'get_channel_id' ]( $username );
		if( $channel[ 'status' ] === FALSE ) {
			echo cutv_get_json_response( null , 0 , $channel[ 'msg' ] );
		} else {
			echo cutv_get_json_response( $channel[ 'data' ] , 1 , "Channel ID Returned." );
		}

		return FALSE;
	}
	/* Exporting sources */
	if( isset( $_GET[ 'export_sources' ] ) ) {
		if( isset( $_GET[ 'ids' ] ) ) {
			$source_ids = explode( ',' , $_GET[ 'ids' ] );
			$sources    = cutv_get_sources( array(
				'ids'         => $source_ids ,
				'get_folders' => TRUE ,
			) );
			$message    = __( 'Could not find any source with the given IDs.' , CUTV_LANG );
		} elseif( isset( $_GET[ 'folders' ] ) ) {
			$source_folders = explode( ',' , $_GET[ 'folders' ] );
			$sources        = cutv_get_sources( array(
				'folders'     => $source_folders ,
				'get_folders' => TRUE ,
			) );
			$message        = __( 'Could not find any source with the given folder.' , CUTV_LANG );
		} else {
			echo "Invalid Testing Params";

			return FALSE;
		}

		cutv_remove_tmp_files();

		if( count( $sources ) == 0 ) {
			?>
			<div class = "wrap">
				<h2 class = "cutv_title">
					<?php cutv_show_logo(); ?>
					<i class = "cutv_title_icon fa fa-upload"></i>
					<?php echo __( 'WP Video Robot' , CUTV_LANG ) . ' - ' . __( 'Exporting sources' , CUTV_LANG ); ?>
				</h2>
				<div class = "cutv_manage_noResults">
					<i class = "fa fa-frown-o"></i><br/>
					<?php echo $message; ?>
				</div>
			</div>

			<?php
			return FALSE;
		}

		//d($sources);
		//return false;

		$json_sources = json_encode( array(
			'data'    => $sources ,
			'version' => CUTV_VERSION ,
			'type'    => 'sources' ,
		) );
		$file         = "tmp_export_" . mt_rand( 0 , 1000 ) . '_@_sources';
		file_put_contents( CUTV_TMP_PATH . $file , $json_sources );
		$export_url = get_option( 'siteurl' ) . "/cutv_export/" . $file;
		?>
		<div class = "wrap">
			<h2 class = "cutv_title">
				<?php cutv_show_logo(); ?>
				<i class = "cutv_title_icon fa fa-upload"></i>
				<?php echo __( 'WP Video Robot' , CUTV_LANG ) . ' - ' . __( 'Exporting sources' , CUTV_LANG ); ?>
			</h2>
			<iframe id = "cutv_iframe" src = "" style = "display:none; visibility:hidden;"></iframe>
			<p>

				<div class = "updated">
			<p><?php echo __( 'Sources were successfully exported !' , CUTV_LANG ); ?> </p>
		</div>
		<br/><br/>
		<?php echo __( 'Please wait, your download will shortly begin.' , CUTV_LANG ); ?> <br/><br/>
		<a href = "#" id = "backBtn">
			<?php echo __( 'Go Back' , CUTV_LANG ); ?>
		</a>
		</p>
		</div>
		<script>
			jQuery('#cutv_iframe').attr('src', "<?php echo $export_url; ?>");
			jQuery('#backBtn').click(function (e) {
				window.history.go(-1);
				e.preventDefault();
				return false;
			});
		</script>
		<?php
		return FALSE;
	}

	/* Run sources Action */
	if( isset( $_GET[ 'run_sources' ] ) ) {


		if( isset( $_GET[ 'ids' ] ) ) {
			$source_ids = explode( ',' , $_GET[ 'ids' ] );
			$sources    = cutv_get_sources( array(
				'ids' => $source_ids ,
			) );
			$message    = __( 'Could not find any source with the given IDs.' , CUTV_LANG );
		} elseif( isset( $_GET[ 'folders' ] ) ) {
			$source_folders = explode( ',' , $_GET[ 'folders' ] );
			$sources        = cutv_get_sources( array(
				'folders' => $source_folders ,
			) );
			$message        = __( 'Could not find any source with the given folder.' , CUTV_LANG );
		} else {
			echo "Invalid Testing Params";

			return FALSE;
		}


		?>
		<?php if( count( $sources ) != 0 ) { ?>
			<?php if( CUTV_ENABLE_ASYNC_RUN ) { ?>
				<?php $async = cutv_async_run_sources( $sources , FALSE , TRUE ); ?>
			<?php } else { ?>
				<?php $return = cutv_run_sources( $sources ); ?>
			<?php } ?>
		<?php } else { ?>
			<div class = "cutv_manage_noResults">
				<i class = "fa fa-frown-o"></i><br/>
				<?php echo $message; ?>
			</div>
		<?php } ?>

		<?php
		return FALSE;
	}
	/* test Sources Action */
	if( isset( $_GET[ 'test_sources' ] ) ) {

		if( isset( $_GET[ 'ids' ] ) ) {
			$source_ids = explode( ',' , $_GET[ 'ids' ] );
			$sources    = cutv_get_sources( array(
				'ids' => $source_ids ,
			) );
			$message    = __( 'Could not find any source with the given IDs.' , CUTV_LANG );
		} elseif( isset( $_GET[ 'folders' ] ) ) {
			$source_folders = explode( ',' , $_GET[ 'folders' ] );
			$sources        = cutv_get_sources( array(
				'folders' => $source_folders ,
			) );
			$message        = __( 'Could not find any source with the given folder.' , CUTV_LANG );
		} else {
			echo "Invalid Testing Params";

			return FALSE;
		}

		//d( $sources );
		// return false;
		//$sources = array();


		?>
		<div class = "cutv_wrap_loading" style = "line-height: 250px;text-align: center;font-size: 60px;color: #27A1CA;">
			<i class = "fa fa-cog fa-spin"></i>
		</div>
		<div class = "wrap cutv_wrap" style = "display:none;">

			<h2 class = "cutv_title">
				<?php cutv_show_logo(); ?>
				<i class = "cutv_title_icon fa fa-eye"></i>
				<?php echo __( 'Testing Sources' , CUTV_LANG ); ?>
			</h2>

			<div>
				<?php if( count( $sources ) != 0 ) { ?>
					<?php cutv_test_sources( $sources ); ?>
				<?php } else { ?>
					<div class = "cutv_manage_noResults">
						<i class = "fa fa-frown-o"></i><br/>
						<?php echo $message; ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php
		return FALSE;
	}
	/* TOggle Sources Action */
	if( isset( $_GET[ 'toggle_sources' ] ) ) {
		if( isset( $_POST[ 'ids' ] ) ) $source_ids = explode( ',' , $_POST[ 'ids' ] );
		else return FALSE;
		if( isset( $_POST[ 'status' ] ) ) $source_status = $_POST[ 'status' ];
		else return FALSE;
		foreach ( $source_ids as $id ) {
			update_post_meta( $id , 'cutv_source_status' , $source_status );
		}
		echo "DONE";

		return FALSE;
	}
	/* Add a single video from the test result screen */
	if( isset( $_GET[ 'test_add_single_video' ] ) ) {
		$video_id = $_POST[ 'video_id' ];

		$cutv_imported = get_option( 'cutv_imported' );
		//new dbug( $_SESSION['cutv_tmp_results'] ) ;
		if( isset( $_GET[ 'is_deferred' ] ) ) {
			$cutv_deferred = get_option( 'cutv_deferred' );
			foreach ( $cutv_deferred as $k => $deferred_video ) {
				if( $deferred_video[ 'id' ] == $video_id ) {
					$video             = $deferred_video;
					$video[ 'origin' ] = 'by MANUAL DEFER';
					unset( $cutv_deferred[ $k ] );
					break;
				}
			}
			update_option( 'cutv_deferred' , $cutv_deferred );
		} else {
			if( ! isset( $_POST[ 'session' ] ) || $_POST[ 'session' ] == '' ) {
				//echo "LOST TESTING TMP SESSION.";
				echo cutv_get_json_response( null , 0 , 'LOST TESTING TMP SESSION.' );

				return FALSE;
			} else    $session = $_POST[ 'session' ];
			if( ! isset( $_SESSION[ 'cutv_tmp_results' ][ $session ][ $video_id ] ) ) {
				echo cutv_get_json_response( null , 0 , 'NO VIDEO THERE' );

				return FALSE;
			} else {
				$video = $_SESSION[ 'cutv_tmp_results' ][ $session ][ $video_id ];
				unset( $_SESSION[ 'cutv_tmp_results' ][ $session ][ $video_id ] );
				$video[ 'origin' ] = "by TEST";
			}
		}

		//_d( $video );return false;

		//$adding_response = cutv_add_video($video , $cutv_imported );
		if( cutv_add_video( $video , $cutv_imported , $allowDuplicates = TRUE ) ) {
			/* Added with no message */
			echo cutv_get_json_response( $video );
		} else {
			echo cutv_get_json_response( $video , 0 , 'VIDEO NOT ADDED' );
		}

		//echo json_encode( $return );
		return FALSE;
	}

	if( isset( $_GET[ 'test_add_unwanted_single_video' ] ) ) {
		$video_id = $_POST[ 'video_id' ];

		$cutv_imported = get_option( 'cutv_imported' );
		//new dbug( $_SESSION['cutv_tmp_results'] ) ;
		if( isset( $_GET[ 'is_deferred' ] ) ) {
			$cutv_deferred = get_option( 'cutv_deferred' );
			foreach ( $cutv_deferred as $k => $deferred_video ) {
				if( $deferred_video[ 'id' ] == $video_id ) {
					$video             = $deferred_video;
					$video[ 'origin' ] = 'by MANUAL DEFER';
					unset( $cutv_deferred[ $k ] );
					break;
				}
			}
			update_option( 'cutv_deferred' , $cutv_deferred );
		} else {
			if( ! isset( $_POST[ 'session' ] ) || $_POST[ 'session' ] == '' ) {
				echo "LOST TESTING TMP SESSION.";

				return FALSE;
			} else    $session = $_POST[ 'session' ];
			if( ! isset( $_SESSION[ 'cutv_tmp_results' ][ $session ][ $video_id ] ) ) {
				echo "NO VIDEO THERE !";

				return FALSE;
			} else {
				$video = $_SESSION[ 'cutv_tmp_results' ][ $session ][ $video_id ];
				unset( $_SESSION[ 'cutv_tmp_results' ][ $session ][ $video_id ] );
				$video[ 'origin' ] = "by TEST";
			}
		}

		global $cutv_unwanted , $cutv_unwanted_ids;

		if( ! isset( $cutv_unwanted_ids[ $video[ 'service' ] ][ $video[ 'id' ] ] ) ) {
			$cutv_unwanted[]                                            = $video;
			$cutv_unwanted_ids[ $video[ 'service' ] ][ $video[ 'id' ] ] = 'unwanted';
		}


		update_option( 'cutv_unwanted' , $cutv_unwanted );
		update_option( 'cutv_unwanted_ids' , $cutv_unwanted_ids );
		echo cutv_get_json_response( $video );

		return FALSE;
	}

	/* BULK Add videos from deferred */
	if( isset( $_GET[ 'test_remove_deferred_videos' ] ) ) {
		if( ! isset( $_POST[ 'videos' ] ) ) {
			echo "NOTHING SELECTED";

			return FALSE;
		}
		$count             = 0;
		$cutv_deferred     = get_option( 'cutv_deferred' );
		$cutv_deferred_ids = get_option( 'cutv_deferred_ids' );
		foreach ( $cutv_deferred as $k => $vid ) {
			if( in_array( $vid[ 'id' ] , $_POST[ 'videos' ] ) ) {
				$count ++;
				unset( $cutv_deferred[ $k ] );
				unset( $cutv_deferred_ids[ $vid[ 'service' ] ][ $vid[ 'id' ] ] );
			}
		}
		update_option( 'cutv_deferred' , $cutv_deferred );
		update_option( 'cutv_deferred_ids' , $cutv_deferred_ids );

		echo $count;


		return FALSE;
	}

	// BULK Remove videos from unwanted
	/* BULK Add videos from the test result screen */
	if( isset( $_GET[ 'test_remove_unwanted_videos' ] ) ) {
		if( ! isset( $_POST[ 'videos' ] ) ) {
			echo "NOTHING SELECTED";

			return FALSE;
		}
		$count             = 0;
		$cutv_unwanted     = get_option( 'cutv_unwanted' );
		$cutv_unwanted_ids = get_option( 'cutv_unwanted_ids' );

		foreach ( $cutv_unwanted as $k => $vid ) {
			if( in_array( $vid[ 'id' ] , $_POST[ 'videos' ] ) ) {
				$count ++;
				unset( $cutv_unwanted[ $k ] );
				unset( $cutv_unwanted_ids[ $vid[ 'service' ] ][ $vid[ 'id' ] ] );
			}
		}
		update_option( 'cutv_unwanted' , $cutv_unwanted );
		update_option( 'cutv_unwanted_ids' , $cutv_unwanted_ids );
		echo $count;

		return FALSE;
	}

	if( isset( $_GET[ 'remove_all_notices' ] ) ) {
		cutv_remove_all_notices();
		echo "ALL NOTICES REMOVED";

		return FALSE;
	}

	if( isset( $_GET[ 'test_notice' ] ) ) {
		cutv_add_notice( array(
			'slug'    => '' ,
			'title'   => 'BE CAREFUL !!' ,
			'class'   => 'error' , //updated or warning or error
			'content' => 'YOUHOU CEST MOI :)' ,
			'hidable' => TRUE ,
		) , FALSE );
		echo "NOTICE ADDED";

		return FALSE;
	}


	/* BULK Add videos from the test result screen */
	if( isset( $_GET[ 'test_add_videos' ] ) ) {
		if( ! isset( $_SESSION[ 'cutv_tmp_results' ] ) ) {
			echo "ERROR NO TMP RESULTS";

			return FALSE;
		}

		if( ! isset( $_POST[ 'session' ] ) ) {
			echo "LOST TMP RESULTS";
			//return false;
		} else $session = '';


		$cutv_imported = get_option( 'cutv_imported' );
		if( isset( $_POST[ 'videos' ] ) ) {
			$count_videos_added = 0;
			foreach ( $_POST[ 'videos' ] as $i => $video_id ) {
				$video             = $_SESSION[ 'cutv_tmp_results' ][ $session ][ $video_id ];
				$video[ 'origin' ] = "by TEST";
				if( cutv_add_video( $video , $cutv_imported ) ) {
					$count_videos_added ++;
					?>
					<script>
						jQuery(document).ready(function ($) {
							$('.cutv_video#video_<?php echo $video_id; ?>').addClass('added');
							$('.cutv_video_cb[name=<?php echo $video_id; ?>]').remove();
						});
					</script>
					<?php
				}
			}
			echo $count_videos_added;
		}

		return FALSE;
	}


	/* SHOW DASHBOARD if no action requested */
	include( CUTV_DASH_PATH );