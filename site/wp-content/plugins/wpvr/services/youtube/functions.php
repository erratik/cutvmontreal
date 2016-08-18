<?php
	require_once( 'functions.api.php' );

	/* Inject Service Styles */
	$vs[ 'get_styles' ] = function () use ( $vs , $pid_suffix ) {
		$vs_id    = $vs[ 'id' ];
		$vs_color = $vs[ 'color' ];
		$styles
		          = "
			.wpvr_service_icon.$vs_id{ background-color:$vs_color;}\n
            .wpvr_source_icon_right.$vs_id{ background-color:$vs_color;}\n
            .wpvrArgs[service=$vs_id] , .wpvr_source_icon[service=$vs_id]{ border-color:$vs_color;}\n
		";

		return $styles;
	};

	/* Embed Video */
	$vs[ 'video_embed' ] = function ( $videoID , $post_id = '' , $autoPlay = TRUE , $add_styles = FALSE , $player_args = array() , $player_attributes = array() ) use ( $vs , $pid_suffix ) {
		global $wpvr_options , $wpvr_dynamics;

		$player_width  = '100%';
		$player_height = '459';
		$player_styles = '';

		if( $add_styles === TRUE )
			$player_styles = ' position:absolute !important;top:0 !important;left:0 !important;width:100% !important;height:100% !important; ';


		if( $wpvr_dynamics[ 'player_tags' ][ 'force_autoplay_disable' ] === TRUE ) $autoPlay = FALSE;

		$player_classes = implode( ' ' , $wpvr_dynamics[ 'player_classes' ] );

		$player_base_args = array(
			'rel'            => 0 ,
			'enablejsapi'    => 1 ,
			'showinfo'       => 0 ,
			'cc_load_policy' => 0 ,
			'modestbranding' => 1 ,
			'iv_load_policy' => 3 ,
			'wmode'          => 'transparent' ,
			'version'        => 3 ,
			'autohide'       => 1 ,
			'controls'       => ( $wpvr_options[ 'enableVideoControls' ] === TRUE ? 1 : 0 ) ,
			'autoplay'       => ( $autoPlay === TRUE ? 1 : 0 ) ,
		);

		$player_args = wpvr_extend( $player_args , $player_base_args );
		if( isset( $wpvr_dynamics[ 'player_options' ][ 'youtube' ] ) && is_array( $wpvr_dynamics[ 'player_options' ][ 'youtube' ] ) ) {
			$player_args = wpvr_extend( $player_args , $wpvr_dynamics[ 'player_options' ][ 'youtube' ] );
		}


		//_d( $wpvr_dynamics['player_options']['youtube'] );
		//_d( $player_args );

		$player_args_string = http_build_query( $player_args , '&amp;' );
		$player_src         = '//www.youtube.com/embed/' . $videoID . '?' . $player_args_string;

		$player_base_attributes = array(
			'video_id'        => $videoID ,
			'style'           => $player_styles ,
			'class'           => ' wpvr_iframe youtube ' . $player_classes ,
			'id'              => 'wpvr_iframe_' . $videoID ,
			'width'           => $player_width ,
			'height'          => $player_height ,
			'src'             => $player_src ,
			'frameborder'     => "0" ,
			'allowfullscreen' => "true" ,
			'allownetworking' => "internal" ,
		);
		$player_attributes      = wpvr_extend( $player_attributes , $player_base_attributes );

		//_d( $player_attributes );

		$player_attributes_string = wpvr_render_html_attributes( $player_attributes );

		$player = '<iframe ' . $player_attributes_string . ' ></iframe>';

		return $player;
	};

	/* GET YOUTUBE CHANNEL DATA */
	$vs[ 'get_trends_data' ] = function ( $param = null ) use ( $vs , $pid_suffix ) {
		return array();
	};


	/* GET YOUTUBE CHANNEL DATA */
	$vs[ 'get_channel_data' ] = function ( $param = null ) use ( $vs , $pid_suffix ) {
		
		// Init API Connection
		$api_handle = $vs[ 'api_auth' ]();
		if( $api_handle === FALSE ) return FALSE;

		// DEfine API URL Endpoint
		$api_url = $vs[ 'api_base' ] . 'channels';

		// Define API ARGUMENTS
		$api_args = array(
			'part'                  => 'snippet' ,
			'id'                    => $param ,
			$api_handle[ 'method' ] => $api_handle[ 'value' ] ,
		);

		// GET API RESPONSE AND DECODE DATA
		$api_response = wpvr_make_curl_request( $api_url , $api_args );
		$data         = json_decode( $api_response[ 'data' ] );

		//EXIT IF ERRORS FOUND
		if( $api_response[ 'status' ] != 200 ) {
			//echo "API ERROR";
			return FALSE;
		}

		// EXIT IF NO DATA RETURNED
		if( ! isset( $data->items[ 0 ] ) ) {
			return FALSE;
		}

		//RETURN SUBDATA
		return array(
			'name'    => $data->items[ 0 ]->snippet->title ,
			'thumb'   => $data->items[ 0 ]->snippet->thumbnails->medium->url ,
			'thumbHQ' => $data->items[ 0 ]->snippet->thumbnails->high->url ,
		);
	};

	/* GET YOUTUBE PLAYLIST DATA */
	$vs[ 'get_playlist_data' ] = function ( $param = null ) use ( $vs , $pid_suffix ) {

		// Init API Connection
		$api_handle = $vs[ 'api_auth' ]();
		if( $api_handle === FALSE ) return FALSE;

		// DEfine API URL Endpoint
		$api_url = $vs[ 'api_base' ] . 'playlists';

		// Define API ARGUMENTS
		$api_args = array(
			'part'                  => 'snippet' ,
			'id'                    => $param ,
			$api_handle[ 'method' ] => $api_handle[ 'value' ] ,
		);

		// GET API RESPONSE AND DECODE DATA
		$api_response = wpvr_make_curl_request( $api_url , $api_args );
		$data         = json_decode( $api_response[ 'data' ] );

		//EXIT IF ERRORS FOUND
		if( $api_response[ 'status' ] != 200 ) {
			return FALSE;
		}

		// EXIT IF NO DATA RETURNED
		if( ! isset( $data->items[ 0 ] ) ) {
			return FALSE;
		}

		//RETURN SUBDATA
		return array(
			'name'    => $data->items[ 0 ]->snippet->title ,
			'thumb'   => $data->items[ 0 ]->snippet->thumbnails->medium->url ,
			'thumbHQ' => $data->items[ 0 ]->snippet->thumbnails->high->url ,
		);
	};


	/* Render Subheader */
	$vs[ 'render_subheader' ] = function ( $source = null ) use ( $vs , $pid_suffix ) {
		$source_type = $source->type;
		$vs_type     = $vs[ 'types' ][ $source_type ];
		if( $vs_type[ 'subheader' ] === FALSE ) return '';

		$sub_data = $vs[ $vs_type[ 'subdata_function' ] ]( $source->$vs_type[ 'param' ] );
		if( $sub_data === FALSE ) return '';

		$output = '';

		if( $vs_type[ 'global_id' ] == 'trends' ) {
			if( $source->$vs_type[ 'param' ] == '' ) {
				$worldwide       = '<i class="wpvr_worldwide fa fa-globe"></i>';
				$subheader_title = __( 'Trends Worldwide' , WPVR_LANG );
			} else {
				$worldwide       = '';
				$subheader_title = __( 'Trends in' , WPVR_LANG ) . ' ' . wpvr_get_country_name( $source->$vs_type[ 'param' ] );
			}

			$output
				= '
				<div class="wpvr_subsource">
					<div class="wpvr_subsource_thumb wpvr_flags f32 ' . $vs_type[ 'global_id' ] . '">
						<span class="flag ' . strtolower( $source->$vs_type[ 'param' ] ) . ' "></span>
						' . $worldwide . '
					</div>
					<div class="wpvr_subsource_name ' . $vs_type[ 'global_id' ] . '">
						' . $subheader_title . '
					</div>
					<div class="wpvr_clearfix"></div>
				</div>
			';
		} elseif( $sub_data != FALSE ) {
			$output
				= '
				<div class="wpvr_subsource">
					<div class="wpvr_subsource_thumb ' . $vs_type[ 'global_id' ] . '">
						<img src="' . $sub_data[ 'thumb' ] . '" alt="' . $sub_data[ 'name' ] . '" />
					</div>
					<div class="wpvr_subsource_name ' . $vs_type[ 'global_id' ] . '">
						' . $sub_data[ 'name' ] . '
					</div>
					<div class="wpvr_clearfix"></div>
				</div>
			';
		}

		return $output;
	};


	/* Get Video Tags */
	$vs[ 'get_video_tags' ] = function ( $videoId ) use ( $vs , $pid_suffix ) {
		$video_url = "http://www.youtube.com/watch?v=" . $videoId;
		$all_tags  = @get_meta_tags( $video_url );
		if( ! isset( $all_tags[ 'keywords' ] ) ) return array();
		$tags = explode( ',' , $all_tags[ 'keywords' ] );

		return $tags;
	};

	/* GEt Video Stats */
	$vs[ 'get_video_stats' ] = function ( $videosFound , $options ) use ( $vs , $pid_suffix ) {
		global $wpvr_options , $wpvr_tokens;

		// Init API Connection
		$api_handle = $vs[ 'api_auth' ]();
		if( $api_handle === FALSE ) return FALSE;

		$api_url = $vs[ 'api_base' ] . 'videos';

		$api_args = array(
			'id'         => substr( $videosFound[ 'videosIds' ] , 0 , - 1 ) ,
			'part'       => 'contentDetails,statistics' ,
			'type'       => 'video' ,
			'maxResults' => '50' ,
		);

		$api_args[ $api_handle[ 'method' ] ] = $api_handle[ 'value' ];
		if( ! isset( $videosFound[ 'ch' ] ) || $videosFound[ 'ch' ] == '' ) $videosFound[ 'ch' ] = curl_init();
		$api_response = wpvr_make_curl_request( $api_url , $api_args , $videosFound[ 'ch' ] );

		$data   = $api_response[ 'data' ];
		$status = $api_response[ 'status' ];

		if( $status == 403 ) {
			//wpvr_render_error_notice( $vs[ 'msgs' ][ 'quota_exceeded' ] );
			return $videosFound;

		} elseif( $status != 200 ) {
			//wpvr_render_error_notice( $vs[ 'msgs' ][ 'api_error' ] );
			return $videosFound;
		}
		$data = json_decode( $data );

		//new dBug( $data );

		foreach ( $data->items as $item ) {
			$videosFound[ 'items' ][ $item->id ][ 'likes' ]    = @$item->statistics->likeCount;
			$videosFound[ 'items' ][ $item->id ][ 'dislikes' ] = @$item->statistics->dislikeCount;
			$videosFound[ 'items' ][ $item->id ][ 'views' ]    = @$item->statistics->viewCount;
			$videosFound[ 'items' ][ $item->id ][ 'duration' ] = @$item->contentDetails->duration;

			/*
			if(
				is_int($options['what']['havingViews'] ) &&
				$item->statistics->viewCount < $options['what']['havingViews'] &&
				$options['what']['havingViews'] != 0 &&
				$options['what']['havingViews'] != ''
			) {	unset( $videosFound['items'][$item->id] );	}

			if(
				is_int($options['what']['havingLikes'] ) &&
				$item->statistics->likeCount < $options['what']['havingLikes'] &&
				$options['what']['havingLikes'] != 0 &&
				$options['what']['havingLikes'] != ''
			) {	unset( $videosFound['items'][$item->id] );	}
			*/
		}

		return $videosFound;
	};

	/* Get Single Video Data */
	$vs[ 'get_single_video_data' ] = function ( $video_id ) use ( $vs , $pid_suffix ) {

		$api_handle = $vs[ 'api_auth' ]();
		if( $api_handle === FALSE ) {
			wpvr_add_notice( array(
				'title'     => 'WP Video Robot ERROR :' ,
				'class'     => 'error' , //updated or warning or error
				'content'   => $vs[ 'msgs' ][ 'authentication_error' ] ,
				'hidable'   => FALSE ,
				'is_dialog' => FALSE ,
				'show_once' => TRUE ,
			) );

			return FALSE;
		}
		
		$api_args                            = array(
			'maxResults'      => '50' ,
			'type'            => 'video' ,
			'videoEmbeddable' => 'true' ,
		);
		$api_args[ $api_handle[ 'method' ] ] = $api_handle[ 'value' ];
		
		$api_url            = $vs[ 'api_base' ] . 'videos';
		$api_args[ 'part' ] = "snippet,contentDetails,statistics";
		$api_args[ 'id' ]   = $video_id;
		$api_response       = wpvr_make_curl_request( $api_url , $api_args );
		$data               = json_decode( $api_response[ 'data' ] );
		$status             = $api_response[ 'status' ];


		//wpvr_set_debug( $api_response , true );
		
		$meta = array(
			'id'               => $video_id ,
			'status'           => FALSE ,
			'url'              => '' ,
			'desc'             => '' ,
			'title'            => '' ,
			'duration'         => '' ,
			'views'            => '' ,
			'originalPostDate' => '' ,
			'thumb'            => '' ,
			'hqthumb'          => '' ,
			'icon'             => '' ,
			'likes'            => '' ,
			'dislikes'         => '' ,
			'tags'             => array() ,
		);

		// No items found
		if( ! isset( $data->items[ 0 ] ) ) {
			wpvr_add_notice( array(
				'title'     => 'WP Video Robot ERROR :' ,
				'class'     => 'error' , //updated or warning or error
				'content'   => $vs[ 'msgs' ][ 'video_not_found' ] ,
				'hidable'   => FALSE ,
				'is_dialog' => FALSE ,
				'show_once' => TRUE ,
			) );

			return FALSE;
		}

		$item                       = $data->items[ 0 ];
		$meta[ 'url' ]              = 'http://www.youtube.com/watch?v=' . $video_id;
		$meta[ 'desc' ]             = $item->snippet->description;
		$meta[ 'title' ]            = $item->snippet->title;
		$meta[ 'duration' ]         = $item->contentDetails->duration;
		$nDate                      = new DateTime( $item->snippet->publishedAt );
		$meta[ 'originalPostDate' ] = $nDate->format( 'Y-m-d H:i:s' );
		$meta[ 'views' ]            = $item->statistics->viewCount;
		$meta[ 'likes' ]            = $item->statistics->likeCount;
		$meta[ 'dislikes' ]         = $item->statistics->dislikeCount;
		$meta[ 'tags' ]             = $vs[ 'get_video_tags' ]( $video_id );
		$meta[ 'icon' ]             = $item->snippet->thumbnails->default->url;
		$meta[ 'id' ]               = $video_id;

		$hqthumb_url = 'https://i.ytimg.com/vi/' . $video_id . '/maxresdefault.jpg';
		if( ! wpvr_curl_check_remote_file_exists( $hqthumb_url ) ) $hqthumb = FALSE;
		else $hqthumb = $hqthumb_url;

		$meta[ 'thumb' ]   = $item->snippet->thumbnails->high->url;
		$meta[ 'hqthumb' ] = $hqthumb;

		return $meta;
	};
	
	/* Fetch Videos */
	$vs_yt                   = $vs;
	$vs_yt[ 'fetch_videos' ] = function ( $videosFound , $options , $oldVideos ) use ( &$vs_yt , $pid_suffix ) {
		global $preDuplicates;
		global $default_videosFound , $default_fetching_options;

		//d( $options );

		//d( $videosFound );
		//d( $default_videosFound );
		//Default variables
		$default_fetching_options = wpvr_prepare_sOptions_fields( $default_fetching_options , null , $default = TRUE );
		$videosFound              = wpvr_extend( $videosFound , $default_videosFound );
		$options                  = wpvr_extend( $options , $default_fetching_options );
		//d( $videosFound );
		$api_handle = $vs_yt[ 'api_auth' ]();
		if( $api_handle === FALSE ) {
			return $videosFound;
		}

		if( $videosFound[ 'execTime' ] == '' ) $videosFound[ 'execTime' ] = microtime( TRUE );

		$api_args = array(
			'maxResults'      => '50' ,
			'type'            => 'video' ,
			'videoEmbeddable' => 'true' ,
		);


		$api_args[ $api_handle[ 'method' ] ] = $api_handle[ 'value' ];


		if( $videosFound[ 'nextPageToken' ] != '1' && $videosFound[ 'nextPageToken' ] != '' && $videosFound[ 'nextPageToken' ] != 'end' )
			$api_args[ 'pageToken' ] = $videosFound[ 'nextPageToken' ];

		$vs_type                                  = $vs_yt[ 'types' ][ $options[ 'what' ][ 'mode' ] ];
		$options[ 'what' ][ $vs_type[ 'param' ] ] = str_replace( ' ' , '' , $options[ 'what' ][ $vs_type[ 'param' ] ] );

		if( $options[ 'what' ][ 'mode' ] == 'search' . $pid_suffix ) {

			//d( $options );

			//Search params
			$api_url             = $vs_yt[ 'api_base' ] . 'search';
			$api_args[ 'part' ]  = "snippet";
			$api_args[ 'q' ]     = $options[ 'what' ][ $vs_type[ 'param' ] ];
			$api_args[ 'order' ] = $options[ 'what' ][ 'order' ];
			if( $options[ 'what' ][ 'searchContext' ] == 'byRegion' ) {
				if( $options[ 'what' ][ 'searchContextValue' ] != FALSE ) {
					//$api_args[ 'regionCode' ] = $options[ 'what' ][ 'searchContextValue' ];
					//$api_args[ 'relevanceLanguage' ] = 'en';
					//$api_args[ 'location' ]       = '33.533333,-7.583333';
					//$api_args[ 'location' ]       = '52.48278,13.447266';
					//$api_args[ 'locationRadius' ] = '30km';
				}
			} elseif( $options[ 'what' ][ 'searchContext' ] == 'byChannel' ) {
				if( $options[ 'what' ][ 'searchContextValue' ] != FALSE ) {
					$api_args[ 'channelId' ] = $options[ 'what' ][ 'searchContextValue' ];
				}
			}


			if( $options[ 'what' ][ 'havingViews' ] != '' && $options[ 'what' ][ 'era' ] != 0 ) {
				$era                           = $options[ 'what' ][ 'era' ];
				$dateBefore                    = date( 'Y-m-d\Th:i:s\Z' , strtotime( "-" . $era . " months" ) );
				$dateAfter                     = date( 'Y-m-d\Th:i:s\Z' , strtotime( "-" . ( $era + 1 ) . " months" ) );
				$api_args[ 'publishedBefore' ] = $dateBefore;
				$api_args[ 'publishedAfter' ]  = $dateAfter;
			}

		} elseif( $options[ 'what' ][ 'mode' ] == 'playlist' . $pid_suffix ) {
			//_d( $options['what'] );
			//Playlist params
			$api_url                  = $vs_yt[ 'api_base' ] . 'playlistItems';
			$api_args[ 'part' ]       = "snippet,status";
			$api_args[ 'playlistId' ] = $options[ 'what' ][ $vs_type[ 'param' ] ];
			$api_args[ 'playlistId' ] = preg_replace( '/\s+/' , '' , $api_args[ 'playlistId' ] );
			$api_args[ 'order' ]      = $options[ 'what' ][ 'order' ];

		} elseif( $options[ 'what' ][ 'mode' ] == 'channel' . $pid_suffix ) {

			//Channel params
			$api_url                 = $vs_yt[ 'api_base' ] . 'search';
			$api_args[ 'part' ]      = "snippet";
			$api_args[ 'channelId' ] = $options[ 'what' ][ $vs_type[ 'param' ] ];
			$api_args[ 'channelId' ] = preg_replace( '/\s+/' , '' , $api_args[ 'channelId' ] );
			$api_args[ 'order' ]     = $options[ 'what' ][ 'order' ];

		} elseif( $options[ 'what' ][ 'mode' ] == 'videos' . $pid_suffix ) {

			if( is_array( $options[ 'what' ][ $vs_type[ 'param' ] ] ) )
				$ids = implode( "," , $options[ 'what' ][ $vs_type[ 'param' ] ] );
			else
				$ids = $options[ 'what' ][ $vs_type[ 'param' ] ];
			//Import videos params
			$api_url            = $vs_yt[ 'api_base' ] . 'videos';
			$api_args[ 'part' ] = "snippet,contentDetails,statistics";
			$api_args[ 'id' ]   = preg_replace( '/\s+/' , '' , $ids );

		} elseif( $options[ 'what' ][ 'mode' ] == 'trends' . $pid_suffix ) {

			//Trendy videos params
			$api_url            = $vs_yt[ 'api_base' ] . 'videos';
			$api_args[ 'part' ] = "snippet";
			if( $options[ 'what' ][ $vs_type[ 'param' ] ] != "" )
				$api_args[ 'regionCode' ] = $options[ 'what' ][ $vs_type[ 'param' ] ];
			$api_args[ 'chart' ]      = 'mostPopular';
			$api_args[ 'maxResults' ] = 50;

			//$api_args[ 'regionCode' ] = '';
			//if( $options['what']['havingViews'] != '' && $options['what']['era'] != 0 ){
			$era = $options[ 'what' ][ 'era' ];
			//$era = 2;
			//d( $era );
			//$dateBefore                  = date( 'Y-m-d\Th:i:s\Z' , strtotime( "-" . $era . " months" ) );
			//$dateAfter                   = date( 'Y-m-d\Th:i:s\Z' , strtotime( "-" . ( $era + 1 ) . " months" ) );
			//$api_args[ 'updatedBefore' ] = $dateBefore;
			//$api_args[ 'updatedAfter' ]  = $dateAfter;
			//}

			//$dateBefore                    = date( 'Y-m-d\Th:i:s\Z' , strtotime( "-1 years" ) );
			//$api_args[ 'publishedBefore' ] = $dateBefore;

		} else {
			echo "UNKNOWN MODE! Exiting...";

			return $videosFound;
		}


		if( $options[ 'what' ][ 'publishedBefore' ] != '' ) {
			$dateBefore                    = date( 'Y-m-d\Th:i:s\Z' , strtotime( $options[ 'what' ][ 'publishedBefore' ] ) );
			$api_args[ 'publishedBefore' ] = $dateBefore;
		}
		if( $options[ 'what' ][ 'publishedAfter' ] != '' ) {
			$dateAfter                    = date( 'Y-m-d\Th:i:s\Z' , strtotime( $options[ 'what' ][ 'publishedAfter' ] ) );
			$api_args[ 'publishedAfter' ] = $dateAfter;
		}

		$api_args[ 'videoDefinition' ] = $options[ 'what' ][ 'videoQuality' ];


		if( $options[ 'what' ][ 'havingViews' ] != '' ) $api_args[ 'order' ] = 'viewCount';
		if( $options[ 'what' ][ 'videoDuration' ] != '' )
			$api_args[ 'videoDuration' ] = $options[ 'what' ][ 'videoDuration' ];


		if( ! isset( $videosFound[ 'ch' ] ) || $videosFound[ 'ch' ] == '' ) $videosFound[ 'ch' ] = curl_init();

		if( ! isset( $videosFound[ 'recalls' ] ) ) $videosFound[ 'recalls' ] = 0;

		$api_response = wpvr_make_curl_request( $api_url , $api_args , $videosFound[ 'ch' ] );

		//d( $options[ 'what' ] );
		//d( $api_response );
		if( WPVR_API_RESPONSE_DEBUG ) {
			d( $api_response );
		}

		$data   = json_decode( $api_response[ 'data' ] );
		$status = $api_response[ 'status' ];

		//new dBug( array($data,$api_url,$api_args,));
		//return $videosFound;
		//d( $data );
		if( $status == '400' ) {
			$errors = $data->error->errors;
			if( $errors[ 0 ]->reason == 'keyInvalid' ) {
				wpvr_render_error_notice( $vs_yt[ 'msgs' ][ 'bad_credentials' ] );

				return $videosFound;
			} elseif( $errors[ 0 ]->reason == 'invalidRegionCode' ) {
				wpvr_render_error_notice( $vs_yt[ 'msgs' ][ 'invalid_region' ] );

				return $videosFound;
			}

			return $videosFound;
		} elseif( $status == 403 ) {
			//d( $videosFound );
			wpvr_render_error_notice(
				'<strong>' . __( 'Source' , WPVR_LANG ) . '</strong> : ' .
				strtoupper( $videosFound[ 'source' ]->name ) . '<br/>' .
				$vs_yt[ 'msgs' ][ 'quota_exceeded' ]
			);

			return $videosFound;

		} elseif( $status == 404 ) {
			//d( $videosFound );
			wpvr_render_error_notice(
				'<strong>' . __( 'Source' , WPVR_LANG ) . '</strong> : ' .
				strtoupper( $videosFound[ 'source' ]->name ) . '<br/>' .
				$data->error->message
			);

			return $videosFound;

		} elseif( $status != 200 ) {
			wpvr_render_error_notice( $vs_yt[ 'msgs' ][ 'api_error' ] );

			return $videosFound;
		}
		//d( $data );
		if( property_exists( $data , 'nextPageToken' ) ) $videosFound[ 'nextPageToken' ] = $data->nextPageToken;
		else $videosFound[ 'nextPageToken' ] = "end";
		$videosFound[ 'totalResults' ] = $data->pageInfo->totalResults;
		$videosIds                     = "";
		$videosFound[ 'videosIds' ]    = "";


		foreach ( $data->items as $item ) {
			$videosFound[ 'absCount' ] ++;

			//new dBug($item);
			if( $options[ 'what' ][ 'mode' ] == 'playlist' . $pid_suffix ) $videoId = $item->snippet->resourceId->videoId;
			elseif( $options[ 'what' ][ 'mode' ] == 'search' . $pid_suffix ) $videoId = $item->id->videoId;
			elseif( $options[ 'what' ][ 'mode' ] == 'videos' . $pid_suffix ) $videoId = $item->id;
			elseif( $options[ 'what' ][ 'mode' ] == 'trends' . $pid_suffix ) $videoId = $item->id;
			elseif( $options[ 'what' ][ 'mode' ] == 'channel' . $pid_suffix ) $videoId = $item->id->videoId;
			else $videoId = 0;


			if( $videosFound[ 'real_count' ] >= $options[ 'how' ][ 'wantedResults' ] ) {
				//Getting Stats before ending
				$videosFound[ 'execTime' ] = round( microtime( TRUE ) - $videosFound[ 'execTime' ] , 2 );
				break;
			}


			if( array_key_exists( $videoId , $oldVideos ) ) {
				if( isset( $oldVideos[ $videoId ] ) && $oldVideos[ $videoId ] == 'unwanted' ) $videosFound[ 'unwantedCount' ] ++;
				else $videosFound[ 'dupCount' ] ++;
			}


			if(
				! $options[ 'how' ][ 'onlyNewVideos' ]
				|| (
					$options[ 'how' ][ 'onlyNewVideos' ]
					&& ! ( array_key_exists( $videoId , $oldVideos ) )
				)
			) {

				if( ! $options[ 'how' ][ 'onlyNewVideos' ] && array_key_exists( $videoId , $oldVideos ) ) {
					$videosFound[ 'dupCount' ] ++;
					$isDuplicate = TRUE;
				} else $isDuplicate = FALSE;


				//new dBug ($preDuplicates);

				if( ! ( array_key_exists( $videoId , $preDuplicates ) ) ) {
					$preDuplicates[ $videoId ] = 1;

					$videosIds .= $videoId . ",";
					$videosFound[ 'videosIds' ] .= $videoId . ",";

					if( $options[ 'how' ][ 'getVideoTags' ] == 'off' ) $video_tags = array();
					else {
						if( $isDuplicate === FALSE ) $video_tags = $vs_yt[ 'get_video_tags' ]( $videoId );
						else $video_tags = array();
					}

					$nDate            = new DateTime( $item->snippet->publishedAt );
					$originalPostDate = $nDate->format( 'Y-m-d H:i:s' );

					//new dBug($video_tags);
					if( property_exists( $item->snippet , 'thumbnails' ) ) {

						$hqthumb_url = 'https://i.ytimg.com/vi/' . $videoId . '/maxresdefault.jpg';
						if( ! wpvr_curl_check_remote_file_exists( $hqthumb_url ) ) $hqthumb = FALSE;
						else $hqthumb = $hqthumb_url;


						$videoItem = array(
							'id'               => $videoId ,
							'viewIcon'         => '<img style="" width="150" height="115" src="' . $item->snippet->thumbnails->default->url . '">' ,
							'title'            => $item->snippet->title ,
							'description'      => $item->snippet->description ,
							'hqthumb'          => $hqthumb ,
							'thumb'            => $item->snippet->thumbnails->high->url ,
							'icon'             => $item->snippet->thumbnails->default->url ,
							'url'              => 'http://www.youtube.com/watch?v=' . $videoId ,
							'originalPostDate' => $originalPostDate ,
							'likes'            => 0 ,
							'dislikes'         => 0 ,
							'views'            => 0 ,
							'duration'         => 0 ,
							'source_tags'      => $options[ 'how' ][ 'postTags' ] ,
							'tags'             => $video_tags ,
							'duplicate'        => $isDuplicate ,
							'postDate'         => $options[ 'how' ][ 'postDate' ] ,
							'postCats'         => $options[ 'how' ][ 'postCats' ] ,
							'postAuthor'       => $options[ 'how' ][ 'postAuthor' ] ,
							'autoPublish'      => $options[ 'how' ][ 'autoPublish' ] ,
							'sourceName'       => $options[ 'how' ][ 'sourceName' ] ,
							'sourceId'         => $options[ 'how' ][ 'sourceId' ] ,
							'sourceType'       => $options[ 'how' ][ 'sourceType' ] ,
							'postAppend'       => $options[ 'how' ][ 'postAppend' ] ,
							'postContent'      => $options[ 'how' ][ 'postContent' ] ,
							'postAppendName'   => $options[ 'how' ][ 'appendCustomText' ] ,
							'appendCustomText' => $options[ 'how' ][ 'appendCustomText' ] ,
							'appendSourceName' => $options[ 'how' ][ 'appendSourceName' ] ,
							'service'          => 'youtube' ,
						);

						$videosFound[ 'items' ][ $videoId ] = $videoItem;
						$oldVideos[ $videoId ]              = "tmp";
						$videosFound[ 'count' ] ++;
					}
				}
			}

			$videosFound[ 'real_count' ] = count( $videosFound[ 'items' ] );
		}

		if( $options[ 'how' ][ 'getVideosStats' ] ) $videosFound = $vs_yt[ 'get_video_stats' ]( $videosFound , $options );


		$videosFound = apply_filters( 'wpvr_filter_videos_found' , $videosFound , $options );
		//d( $videosFound );
		if( $videosFound[ 'totalResults' ] != 0 && $videosFound[ 'real_count' ] < $options[ 'how' ][ 'wantedResults' ] ) {
			if( $videosFound[ 'nextPageToken' ] == 'end' ) {
				//new dBug( $options['how']['sourceId']);
				update_post_meta( $options[ 'how' ][ 'sourceId' ] , 'wpvr_source_era' , $options[ 'what' ][ 'era' ] + 1 );
				//$videosFound['nextPageToken'] = '';
				$videosFound[ 'era' ] = $videosFound[ 'era' ] + 1;
				//$videosFound = wpvr_fetch_videos($videosFound,$options,$oldVideos);
				if( $options[ 'how' ][ 'debugMode' ] ) echo( '<br/> Found : ' . $videosFound[ 'count' ] . ' .... END SEARCH' );
			} else {
				if( $options[ 'how' ][ 'debugMode' ] ) echo( '<br/> Found : ' . $videosFound[ 'count' ] . ' .... need to recall' );
				$videosFound[ 'recalls' ] = $videosFound[ 'recalls' ] + 1;
				//$videosFound = wpvr_fetch_videos_youtube($videosFound,$options,$oldVideos);
				$videosFound = $vs_yt[ 'fetch_videos' ]( $videosFound , $options , $oldVideos );
			}
		} else {
			if( $options[ 'how' ][ 'debugMode' ] ) echo( '<br/> Found : ' . $videosFound[ 'count' ] . ' .... DONE !' );
			//curl_close($ch);
			//$videosFound['ch'] = '';
			$videosFound[ 'execTime' ] = round( microtime( TRUE ) - $videosFound[ 'execTime' ] , 2 );
		}

		//d( $videosFound['nextPageToken'] );
		return $videosFound;
	};
	$vs[ 'fetch_videos' ]    = $vs_yt[ 'fetch_videos' ];

	/* Get Channel Id from Username */
	$vs[ 'get_channel_id' ] = function ( $q ) use ( $vs , $pid_suffix ) {

		global $wpvr_options , $wpvr_tokens;

		// Init API Connection
		$api_handle = $vs[ 'api_auth' ]();
		if( $api_handle === FALSE ) {
			return array(
				'status' => FALSE ,
				'data'   => null ,
				'msg'    => __( 'Youtube API is not authorized.' , WPVR_LANG ) ,
			);
		}
		//_d( $api_handle );

		// DEfine API URL Endpoint
		$api_url = $vs[ 'api_base' ] . 'search';

		// Define API ARGUMENTS
		$api_args = array(
			'part'                  => 'snippet' ,
			'q'                     => $q ,
			'type'                  => 'channel' ,
			'maxResults'            => WPVR_HELPER_RESULTS_COUNT ,
			$api_handle[ 'method' ] => $api_handle[ 'value' ] ,
		);

		// GET API RESPONSE AND DECODE DATA
		$api_response = wpvr_make_curl_request( $api_url , $api_args );
		$data         = json_decode( $api_response[ 'data' ] );

		//_d( $api_response );

		//EXIT IF ERRORS FOUND
		if( $api_response[ 'status' ] != 200 ) {
			return array(
				'status' => FALSE ,
				'data'   => null ,
				'msg'    => __( 'Youtube API is not reachable.' , WPVR_LANG ) ,
			);
		}

		// EXIT IF NO DATA RETURNED
		if( ! isset( $data->items[ 0 ] ) ) {
			return array(
				'status' => FALSE ,
				'data'   => null ,
				'msg'    => __( 'Could not find any channel corresponding to the given username.' , WPVR_LANG ) ,
			);
		}
		$results = array();
		//_d( $data->items );


		foreach ( (array) $data->items as $item ) {
			//_d( $item );
			$name = ( $item->snippet->channelTitle != '' ) ? $item->snippet->channelTitle : $item->snippet->title;
			$name = preg_replace( '/(' . $q . ')/i' , '<span class="wpvr_helper_highlight">' . "$1" . '</span>' , $name );

			$results[] = array(
				'id'    => $item->snippet->channelId ,
				'name'  => $name ,
				'thumb' => $item->snippet->thumbnails->default->url ,
				'label' => __( 'channel' , WPVR_LANG ) ,
			);
		}

		return array(
			'status' => TRUE ,
			'data'   => $results ,
			'msg'    => '' ,
		);
		//return $data->items[ 0 ]->id;
	};


	/* Get Comments of a video */
	$vs_ytcm                   = $vs;
	$vs_ytcm[ 'get_comments' ] = function ( $video_id , $post_id , $cData ) use ( &$vs_ytcm , $pid_suffix ) {

		$cData = wpvr_extend( $cData , array(
			'ch'        => curl_init() ,
			'pageToken' => null ,
			'wanted'    => 20 ,
			'count'     => 0 ,
			'recalls'   => 0 ,
			'do_import' => FALSE ,
			'comments'  => array() ,
		) );

		//if( ! isset( $vs_ytcm ) ) return $cData;

		$api = $vs_ytcm[ 'api_auth' ]();
		if( $api === FALSE ) {
			$cData[ 'msg' ] = 'Youtube API not authorized.';

			return $cData;
		}

		$api_api_url = 'https://www.googleapis.com/youtube/v3/commentThreads';

		$api_api_params                 = array();
		$api_api_params[ 'part' ]       = "snippet,id,replies";
		$api_api_params[ 'videoId' ]    = $video_id;
		$api_api_params[ 'maxResults' ] = 100;

		$api_api_params[ $api[ 'method' ] ] = $api[ 'value' ];

		if( $cData[ 'pageToken' ] != null ) $api_api_params[ 'pageToken' ] = $cData[ 'pageToken' ];

		$api_response = wpvr_make_curl_request( $api_api_url , $api_api_params , $cData[ 'ch' ] );

		//_d( $api_response );

		$data = (array) json_decode( $api_response[ 'data' ] );
		//$status = $api_response['status'] ;
		if( ! isset( $data[ 'items' ] ) || ! is_array( $data[ 'items' ] ) || count( $data[ 'items' ] ) == 0 ) {
			$cData[ 'msg' ] = 'Impossible to parse API results.';

			return $cData;
		}

		foreach ( (array) $data[ 'items' ] as $item ) {
			if( $cData[ 'count' ] >= $cData[ 'wanted' ] ) return $cData;

			$comment      = $item->snippet->topLevelComment->snippet;
			$comment_date = date( 'Y-m-d H:i:s' , strtotime( $comment->publishedAt ) );
			$cData[ 'count' ] ++;
			$comment = array(
				'comment_post_ID'      => $post_id ,
				'comment_author'       => $comment->authorDisplayName ,
				'comment_author_email' => '' ,
				'comment_author_url'   => $comment->authorChannelUrl ,
				'comment_content'      => $comment->textDisplay ,
				'comment_type'         => '' ,
				'comment_parent'       => 0 ,
				'comment_author_IP'    => '127.0.0.1' ,
				'comment_agent'        => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)' ,
				'comment_date'         => $comment_date ,
				'comment_approved'     => 1 ,
			);
			if( $cData[ 'do_import' ] === TRUE ) {
				$comment_id              = wp_insert_comment( $comment );
				$comment[ 'comment_id' ] = $comment_id;
			}

			$cData[ 'comments' ][] = $comment;
		}
		if( $data[ 'nextPageToken' ] != '' && $cData[ 'count' ] <= $cData[ 'wanted' ] ) {
			$cData[ 'recalls' ] ++;
			$cData[ 'pageToken' ] = $data[ 'nextPageToken' ];

			return $vs_ytcm[ 'get_comments' ]( $video_id , $post_id , $cData );
		}

		return $cData;

	};
	$vs[ 'get_comments' ]      = $vs_ytcm[ 'get_comments' ];
