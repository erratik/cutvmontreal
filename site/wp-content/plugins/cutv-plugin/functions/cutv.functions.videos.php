<?php


	/* Render Player with Dynamic Tags and options */
	if( ! function_exists( 'cutv_render_modified_player' ) ) {
		function cutv_render_modified_player( $post_id = null ) {
			global $cutv_options , $cutv_dynamics;

			if( $post_id == null ) {
				global $post;
			} else {
				$post = get_post( $post_id );
			}

			if( is_single() ) $auto_play = $cutv_options[ 'playerAutoPlay' ];
			else $auto_play = FALSE;

			$cutv_player_tags = $cutv_dynamics[ 'player_tags' ];
			$cutv_player_tags = apply_filters( 'cutv_extend_player_tags' , $cutv_player_tags );

			$cutv_video_id = get_post_meta( $post->ID , 'cutv_video_id' , TRUE );
			$cutv_service  = get_post_meta( $post->ID , 'cutv_video_service' , TRUE );
			if( isset( $cutv_dynamics[ 'player_options' ][ $cutv_service ] ) ) {
				$player_arguments = $cutv_dynamics[ 'player_options' ][ $cutv_service ];
			} else {
				$player_arguments = array();
			}


			$cutv_player = cutv_video_embed(
				$video_id = $cutv_video_id ,
				$post_id = $post->ID ,
				$auto_play ,
				$service = $cutv_service ,
				$add_style = FALSE ,
				$player_args = $player_arguments ,
				$player_attributes = array()
			);

			//d( $cutv_player );
			/*d( $video_id );d( $cutv_service );*/

			$embedCode = '';
			$embedCode .= $cutv_player_tags[ 'before_outer' ];
			$embedCode .= '<div class="wpvr_embed cutv_new ' . $cutv_player_tags[ 'embed_class' ] . '">';
			$embedCode .= $cutv_player_tags[ 'before_inner' ];
			$embedCode .= $cutv_player;
			$embedCode .= $cutv_player_tags[ 'after_inner' ];
			$embedCode .= '</div>';
			$embedCode .= $cutv_player_tags[ 'after_outer' ];

			return apply_filters( 'cutv_extend_video_player_rendering' , $embedCode , $post_id );

		}
	}
	
	/* Check if a video is valid */
	if( ! function_exists( 'cutv_is_valid_video' ) ) {
		function cutv_is_valid_video( $video_id , $video_service ) {
			return TRUE;
		}
	}

	/* Get a single youtube video data */
	if( ! function_exists( 'cutv_get_video_single_data' ) ) {
		function cutv_get_video_single_data( $video_id , $service = '' ) {
			global $cutv_vs;

			return $cutv_vs[ $service ][ 'get_single_video_data' ]( $video_id );
		}
	}

	/* ADD A VIDEO OBJECT  */
	if( ! function_exists( 'cutv_add_video' ) ) {
		function cutv_add_video( $videoItem , $_cutv_imported = array() , $allowDuplicates = FALSE ) {
			global $cutv_imported , $cutv_options , $cutv_vs;


			//_d( $videoItem );

			$return = array(
				'status'   => TRUE ,
				'errors'   => array() ,
				'messages' => array() ,
			);

			if( isset( $cutv_imported[ $videoItem[ 'service' ] ][ $videoItem[ 'id' ] ] ) ) return FALSE;

			/* If this video is already imported => Don't do anything */
			if( $allowDuplicates === FALSE ) {
				if( isset( $_cutv_imported[ $videoItem[ 'service' ] ][ $videoItem[ 'id' ] ] ) ) {
					return FALSE;
				}
			}

			/* Checking if we use the original Posting Date or create a new one */
			if( $videoItem[ 'postDate' ] == 'original' ) {
				$video_post_date = $videoItem[ 'originalPostDate' ];
			} else {
				if( isset( $videoItem[ 'importedPostDate' ] ) ) {
					$obj_post_date = cutv_make_postdate( $videoItem[ 'importedPostDate' ] );
				} else {
					$obj_post_date = cutv_make_postdate();
				}
				
				$video_post_date = $obj_post_date->format( 'Y-m-d H:i:s' );
			}

			//new dBug( $videoItem );

			/* Check if we publish the video or keep it on pending */
			if( $videoItem[ 'autoPublish' ] == 'on' ) {
				$video_status = "publish";
			} else {
				$video_status = "pending";
			}


			if( $videoItem[ 'postAppend' ] != 'off' && $videoItem[ 'postAppendName' ] != 'false' ) {
				if( $videoItem[ 'postAppend' ] == 'before' || $videoItem[ 'postAppend' ] == 'customBefore' ) {
					$video_title = $videoItem[ 'postAppendName' ] . CUTV_APPEND_SEPARATOR . $videoItem[ 'title' ];
				} elseif( $videoItem[ 'postAppend' ] == 'after' || $videoItem[ 'postAppend' ] == 'customAfter' ) {
					$video_title = $videoItem[ 'title' ] . CUTV_APPEND_SEPARATOR . $videoItem[ 'postAppendName' ];
				} else {
					$video_title = $videoItem[ 'title' ];
				}
			} else {
				$video_title = $videoItem[ 'title' ];
			}

			if( $videoItem[ 'service' ] == 'youtube' && $cutv_options[ 'getFullDesc' ] === TRUE ) {
				$video_meta = cutv_get_video_single_data( $videoItem[ 'id' ] , $videoItem[ 'service' ] );

				$videoItem[ 'description' ] = $video_meta[ 'desc' ];
			}

			$video_desc = $videoItem[ 'description' ];

			if( CUTV_EG_FIX === TRUE ) {
				$iframe                     = '<iframe src="http://www.youtube.com/embed/' . $videoItem[ 'id' ]
				                              . '?rel=0" width="560" height="315" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
				$videoItem[ 'description' ] = $iframe . $videoItem[ 'description' ];
			}

			$newPost = array(
				'post_title'  => $video_title ,
				'post_date'   => $video_post_date ,
				'post_status' => $video_status ,
				'post_type'   => CUTV_VIDEO_TYPE ,
				'post_author' => $videoItem[ 'postAuthor' ] ,
			);

			//_d( $videoItem );

			if( ! isset( $videoItem[ 'postContent' ] ) || $videoItem[ 'postContent' ] == 'default' ) {
				$videoItem[ 'postContent' ] = $cutv_options[ 'getFullDesc' ] ? 'on' : 'off';
			}

			if( isset( $videoItem[ 'postContent' ] ) && $videoItem[ 'postContent' ] == 'on' ) {
				$newPost[ 'post_content' ] = $videoItem[ 'description' ];
			}

			$newPostId = @wp_insert_post( $newPost );
			add_post_meta( $newPostId , 'cutv_video_id' , $videoItem[ 'id' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_sourceName' , $videoItem[ 'sourceName' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_sourceType' , $videoItem[ 'sourceType' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_sourceId' , $videoItem[ 'sourceId' ] , TRUE );

			if( CUTV_ENABLE_POST_FORMATS ) {
				global $cutv_options;
				set_post_format( $newPostId , $cutv_options[ 'postFormat' ] );
			}

			if( isset( $videoItem[ 'local_views' ] ) && is_numeric( $videoItem[ 'local_views' ] ) ) {
				add_post_meta( $newPostId , 'cutv_video_views' , $videoItem[ 'local_views' ] , TRUE );
			} else {
				if( $cutv_options[ 'startWithServiceViews' ] === TRUE )
					add_post_meta( $newPostId , 'cutv_video_views' , $videoItem[ 'views' ] , TRUE );
				else
					add_post_meta( $newPostId , 'cutv_video_views' , 0 , TRUE );
			}
			
			
			add_post_meta( $newPostId , 'cutv_video_service_views' , $videoItem[ 'views' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_service_url' , $videoItem[ 'url' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_service_thumb' , $videoItem[ 'thumb' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_service_icon' , $videoItem[ 'icon' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_duration' , $videoItem[ 'duration' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_service' , $videoItem[ 'service' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_service_date' , $videoItem[ 'originalPostDate' ] , TRUE );
			add_post_meta( $newPostId , 'cutv_video_service_desc' , $video_desc , TRUE );

			$videoItem_embedCode = cutv_video_embed(
				$videoItem[ 'id' ] ,
				$newPostId ,
				FALSE ,
				$videoItem[ 'service' ]
			);
			add_post_meta( $newPostId , 'cutv_video_embed_code' , $videoItem_embedCode , TRUE );


			add_post_meta( $newPostId , 'cutv_video_' . $cutv_vs[ $videoItem[ 'service' ] ][ 'pid' ] . 'Id' , $videoItem[ 'id' ] , TRUE );

			if( CUTV_TAGS_FROM_TITLE_ENABLE ) {
				$title_tags = explode( '-' , sanitize_file_name( $videoItem[ 'title' ] ) );
			} else {
				$title_tags = array();
			}

			$video_tags = array_merge(
				$title_tags ,
				$videoItem[ 'tags' ] ,
				$videoItem[ 'source_tags' ]
			);

			wp_set_post_tags( $newPostId , $video_tags , TRUE );
			wp_set_post_categories( $newPostId , ( $videoItem[ 'postCats' ] ) );
			wp_set_post_terms( $newPostId , 'en' , 'language' );

			//new dBug($videoItem);

			//Adding Thumbnail
			$upload_dir = wp_upload_dir(); // Set upload folder

			if( isset( $videoItem[ 'thumb_id' ] ) && $videoItem[ 'thumb_id' ] != '' ) {

				// Thumbnail is already on DB and SERVER, just assign it to the video
				set_post_thumbnail( $newPostId , $videoItem[ 'thumb_id' ] );

			} elseif( $videoItem[ 'thumb' ] != '' ) {

				if( $videoItem[ 'hqthumb' ] === FALSE ) $working_thumb = $videoItem[ 'thumb' ];
				else $working_thumb = $videoItem[ 'hqthumb' ];


				$featured_image_file = cutv_download_featured_image(
					$working_thumb ,
					$videoItem[ 'title' ] ,
					$videoItem[ 'description' ] ,
					$newPostId
				);

				do_action( 'cutv_event_add_video_thumbnail' , $videoItem , $newPostId , $featured_image_file );

			}

			cutv_run_dataFillers( $newPostId );

			//update old videos
			$_cutv_imported[ $videoItem[ 'service' ] ][ $videoItem[ 'id' ] ] = $newPostId;

			//new dBug( $_cutv_imported );

			update_option( 'cutv_imported' , $_cutv_imported );
			$cutv_imported = $_cutv_imported;


			$video_status = ( $videoItem[ 'autoPublish' ] == 'off' ) ? 'PENDING' : 'PUBLISHED';

			//UPDATE COUNTERS
			$videos_count = get_post_meta( $videoItem[ 'sourceId' ] , 'cutv_source_count_imported' , TRUE );
			if( $videos_count == '' ) {
				$videos_count = 1;
			} else {
				$videos_count ++;
			}
			update_post_meta( $videoItem[ 'sourceId' ] , 'cutv_source_count_imported' , $videos_count );

			//LOG ACTIONS
			cutv_add_log( array(
				"status"   => 'ok' ,
				//Y-m-d H:i:s
				"time"     => date( 'Y-m-d H:i:s' ) ,
				//Y-m-d H:i:s
				"action"   => __( 'ADDING A VIDEO' , CUTV_LANG ) . ' ( ' . $videoItem[ 'origin' ] . ' )' ,
				//Adding | FEtching | Running |
				"type"     => 'add' ,
				//Adding | FEtching | Running |
				"object"   => $videoItem[ 'id' ] ,
				//
				"icon"     => $videoItem[ 'icon' ] ,
				//
				"log_msgs" => array(
					'<b>' . __( 'SERVICE' , CUTV_LANG ) . ' </b>: ' . strtoupper( $videoItem[ 'service' ] ) ,
					'<b>' . __( 'TITLE' , CUTV_LANG ) . ' </b>: ' . $videoItem[ 'title' ] ,
					'<b>' . __( 'POSTED AS' , CUTV_LANG ) . ' </b>: ' . $video_status ,
					'<b>' . __( 'NEW POST ID' , CUTV_LANG ) . ' </b>: ' . $newPostId ,
				) ,
			) );
			$count_videos = 0;
			foreach ( $cutv_imported as $s => $service_videos ) {
				$count_videos += count( $service_videos );
			}

			//d( $videoItem );

			do_action( 'cutv_event_add_video' , $videoItem , $newPostId );
			do_action( 'cutv_event_add_video_done' , $count_videos );
			do_action( 'cutv_event_run_dataFillers' , $newPostId );

			//new dBug($log_msgs);

			return $newPostId;
		}
	}
	
	/*Get Videos*/
	if( ! function_exists( 'cutv_get_videos' ) ) {
		function cutv_get_videos( $args = array() ) {
			global $wpdb;
			global $cutv_options;
			$default_args = array(
				'ids'         => array() ,
				'vids'        => array() ,
				'meta_suffix' => FALSE ,
				'order'       => 'date' , //date,  views , title
			);
			$args         = cutv_extend( $args , $default_args );
			$ids_array    = "'" . implode( "','" , $args[ 'ids' ] ) . "'";
			$vids_array   = "'" . implode( "','" , $args[ 'vids' ] ) . "'";

			if( $args[ 'meta_suffix' ] ) {
				$meta = "__";
			} else {
				$meta = "";
			}

			if( $args[ 'order' ] == 'date' ) {
				$crit = "post_date";
			} elseif( $args[ 'order' ] == 'views' ) {
				$crit = "post_date";
			} elseif( $args[ 'order' ] == 'title' ) {
				$crit = "post_title";
			} else {
				echo "ERROR 'order' argument on cutv_get_videos function !";

				return FALSE;
			}

			$conds = array(
				'ids'  => '' ,
				'vids' => '' ,
			);

			if( $args[ 'ids' ] != array() ) $conds[ 'ids' ] = " AND P.ID IN (" . $ids_array . ") ";
			if( $args[ 'vids' ] != array() ) $conds[ 'vids' ] = " AND " . $meta . "videoId IN (" . $vids_array . ") ";


			$querystr
				= "
			SELECT 
				P.ID as ID, 
				P.ID as id, 
				P.post_author as post_author, 
				P.post_date as post_date, 
				P.post_date_gmt as post_date_gmt, 
				P.post_content as post_content, 
				P.post_title as post_title, 
				P.post_excerpt as post_excerpt, 
				P.post_status as post_status, 
				P.comment_status as comment_status, 
				P.ping_status as ping_status, 
				P.post_password as post_password, 
				P.post_name as post_name, 
				P.to_ping as to_ping, 
				P.pinged as pinged, 
				P.post_modified as post_modified, 
				P.post_modified_gmt as post_modified_gmt, 
				P.post_content_filtered as post_content_filtered, 
				P.post_parent as post_parent, 
				P.guid as guid, 
				P.menu_order as menu_order, 
				P.post_type as post_type, 
				P.post_mime_type as post_mime_type, 
				P.comment_count as comment_count, 
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_id' , M.meta_value , NULL ) SEPARATOR '') as " . $meta . "videoId,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_service' , M.meta_value , NULL ) SEPARATOR '') as " . $meta . "service,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_duration' , M.meta_value , NULL ) SEPARATOR '') as " . $meta . "duration,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_sourceName' , M.meta_value , NULL ) SEPARATOR '') as " . $meta . "sourceName,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_sourceId' , M.meta_value , NULL ) SEPARATOR '') as " . $meta . "sourceId,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_sourceType' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "sourceType,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_views' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "views,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_service_icon' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "youtubeIcon,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_service_thumb' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "youtubeThumb,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_service_url' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "youtubeUrl,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_service_views' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "youtubeViews,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_service_likes' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "youtubeDislikes,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_service_dislikes' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "youtubeLikes,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_enableManualAdding' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "enableManualAdding,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_getDesc' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "getDesc,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_getTitle' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "getTitle,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_getTags' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "getTags,
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_getThumb' , M.meta_value , NULL ) SEPARATOR '' ) as " . $meta . "getThumb,
				GROUP_CONCAT(DISTINCT if(WPTax.taxonomy = 'post_tag' , WPTerms.slug , NULL ) SEPARATOR ',' ) as slugTags,
				GROUP_CONCAT(DISTINCT if(WPTax.taxonomy = 'post_tag' , WPTerms.term_id , NULL ) SEPARATOR ',' ) as idTags,
				GROUP_CONCAT(DISTINCT if(WPTax.taxonomy = 'category' , WPTerms.slug , NULL ) SEPARATOR ',' ) as slugCats,
				GROUP_CONCAT(DISTINCT if(WPTax.taxonomy = 'category' , WPTerms.term_id , NULL ) SEPARATOR ',' ) as idCats,
				
				1 as end
			FROM 
				$wpdb->posts P 
				INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
				INNER JOIN $wpdb->term_relationships WPRelat on WPRelat.object_id = P.ID
				INNER JOIN $wpdb->term_taxonomy WPTax on WPTax.term_taxonomy_id = WPRelat.term_taxonomy_id
				INNER JOIN $wpdb->terms WPTerms on WPTerms.term_id = WPTax.term_id
			WHERE
				1
				AND P.post_type = '" . CUTV_VIDEO_TYPE . "'
				" . $conds[ 'ids' ] . "
			GROUP by
				P.ID
			HAVING
				1
				" . $conds[ 'vids' ] . "
			ORDER BY
				$crit DESC
		";
			//d( $querystr) ;
			$videos = $wpdb->get_results( $querystr , OBJECT );

			return $videos;
		}
	}
	
	/* UPDATE IMPORTED VIDEOS */
	if( ! function_exists( 'cutv_clear_imported_videos' ) ) {
		function cutv_clear_imported_videos() {
			global $cutv_vs;
			$cutv_imported = array();
			foreach ( $cutv_vs as $vs_id => $vs ) {
				$cutv_imported[ $vs_id ] = array();
			}
			update_option( 'cutv_imported' , $cutv_imported );
		}
	}
	
	/* UPDATE IMPORTED VIDEOS */
	if( ! function_exists( 'cutv_update_imported_videos' ) ) {
		function cutv_update_imported_videos() {
			global $wpdb , $cutv_vs;

			if( ! is_array( $cutv_vs ) || count( $cutv_vs ) == 0 ) return get_option( 'cutv_imported' );

			$sql
				= "
			select 
				P.ID as post_id, 
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_id' , M.meta_value , NULL ) SEPARATOR '') as video_id, 
				GROUP_CONCAT( DISTINCT if(M.meta_key = 'cutv_video_service' , M.meta_value , NULL ) SEPARATOR '') as video_service
			FROM 
				$wpdb->posts P 
				INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
			WHERE 
				P.post_type = '" . CUTV_VIDEO_TYPE . "'
			GROUP BY 
				P.ID
		";

			$videos        = $wpdb->get_results( $sql , OBJECT );
			$cutv_imported = array();
			foreach ( $cutv_vs as $vs_id => $vs ) {
				$cutv_imported[ $vs_id ] = array();
			}

			foreach ( $videos as $video ) {
				if( $video->video_service == '' ) $video->video_service = 'youtube';

				if( $video->video_id != '' ) {
					$cutv_imported[ $video->video_service ][ $video->video_id ] = $video->post_id;
				}
			}
			update_option( 'cutv_imported' , $cutv_imported );

			//d( $cutv_imported );
			return $cutv_imported;
		}
	}

	/* GET POST ACTION LINKS */
	if( ! function_exists( 'cutv_get_post_links' ) ) {
		function cutv_get_post_links( $post_id , $action ) {
			if( ! $post_id || ! is_numeric( $post_id ) ) {
				return FALSE;
			}
			if( $action == 'untrash' ) {
				$a = 'untrash-post_';
				$b = 'untrash';
			} elseif( $action == 'trash' ) {
				$a = 'trash-post_';
				$b = 'trash';
			} elseif( $action == 'delete' ) {
				$a = 'delete-post_';
				$b = 'delete';
			} else {
				return FALSE;
			}
			$_wpnonce = wp_create_nonce( $a . $post_id );

			return admin_url( 'post.php?post=' . $post_id . '&action=' . $b . '&_wpnonce=' . $_wpnonce );
		}
	}

	/* GENERATE VIDEO PLAYER EMBED CODE */
	if( ! function_exists( 'cutv_video_embed' ) ) {
		function cutv_video_embed( $videoID , $post_id = '' , $autoPlay = TRUE , $service = 'youtube' , $add_styles = FALSE , $player_args = array() , $player_attributes = array() ) {
			global $cutv_vs , $post;
			if( $post_id == '' && isset( $post->ID ) ) $post_id = $post->ID;
			if( $service == '' ) $service = 'youtube';
			if(
				! isset( $cutv_vs[ $service ] )
				|| ! isset( $cutv_vs[ $service ][ 'video_embed' ] )
			) {
				//echo $service . " Service not enabled";
				return FALSE;
			}

			return $cutv_vs[ $service ][ 'video_embed' ](
				$videoID ,
				$post_id ,
				$autoPlay ,
				$add_styles ,
				$player_args ,
				$player_attributes
			);
		}
	}

	/* Get Videos Stats by Author*/
	if( ! function_exists( 'cutv_videos_stats_author' ) ) {
		function cutv_videos_stats_author() {
			global $cutv_options;
			global $wpdb;
			$qMeta
				= "
			SELECT 
				U.id as user_id,
				U.user_login as user_login,
				COUNT( distinct P.ID) as count
			FROM 
				$wpdb->users U
				LEFT JOIN $wpdb->posts P ON U.ID = P.post_author
			WHERE
				P.post_type = '" . CUTV_VIDEO_TYPE . "'
		";

			$rMeta = $wpdb->get_results( $qMeta , OBJECT );
			if( ! isset( $rMeta[ 0 ] ) ) {
				return FALSE;
			} else {
				$vStats = (array) $rMeta[ 0 ];

				return $vStats;
			}
		}
	}
	
	/* Get Videos Stats*/
	if( ! function_exists( 'cutv_videos_stats' ) ) {
		function cutv_videos_stats() {
			global $wpdb;

			/* Getting by Cats */
			$qCat
				= "
			(
				select 
					WT.term_id as id,
					WT.slug as slug,
					WT.name as name,
					COUNT( DISTINCT P.ID ) as count
				FROM 
					$wpdb->posts P 
					LEFT JOIN $wpdb->term_relationships WTR on P.ID = WTR.object_id
					LEFT JOIN $wpdb->term_taxonomy WTT on WTR.term_taxonomy_id = WTT.term_taxonomy_id
					LEFT JOIN $wpdb->terms WT on WT.term_id = WTT.term_id
				WHERE 
					1
					and WTT.taxonomy= 'category'
					and P.post_type = '" . CUTV_VIDEO_TYPE . "'
					and P.post_status in ('trash','pending','publish','draft','invalid')				
				GROUP BY 
					WT.term_id
			)UNION(
				select 
					'nocat' as id,
					'NO CATS' as slug,
					'NO CATS' as name,
					COUNT( DISTINCT P.ID ) as count
				FROM 
					$wpdb->posts P 
				WHERE 
					1
					and P.post_type = '" . CUTV_VIDEO_TYPE . "'
					and P.post_status in ('trash','pending','publish','draft','invalid')				
				GROUP BY 
					1
			)
			
		";

			$sVideos = array(
				'byCat'    => array( 'total' => 0 , 'items' => array() ) ,
				'byAuthor' => array( 'total' => 0 , 'items' => array() ) ,
				'byStatus' => array( 'total' => 0 , 'items' => array() ) ,
			);

			$sCat     = $wpdb->get_results( $qCat , OBJECT );
			$with_cat = $wk = 0;
			if( count( $sCat ) == 0 ) {
				return FALSE;
			}
			foreach ( $sCat as $k => $cat ) {
				$sVideos[ 'byCat' ][ 'items' ][ $cat->name ] = $cat->count;
				if( $cat->id != 'nocat' ) {
					$with_cat += $cat->count;
				} else {
					$wk = $k;
				}
				$sVideos[ 'byCat' ][ 'total' ] += $cat->count;
			}
			$sVideos[ 'byCat' ][ 'items' ][ 'NO CATS' ] = $sCat[ $wk ]->count - $with_cat;
			$sVideos[ 'byCat' ][ 'total' ] -= $with_cat;

			/*Get by Author*/
			$qAuthor
				     = "
			select 
				U.id as id,
				U.user_login as user_login,
				COUNT( DISTINCT P.ID ) as count
			FROM $wpdb->posts P 
				LEFT JOIN $wpdb->users U on U.ID = P.post_author
			WHERE 
				1
				and P.post_type = '" . CUTV_VIDEO_TYPE . "'
				and P.post_status in ('trash','pending','publish','draft','invalid')				
			GROUP BY 
				U.ID
				
		";
			$sAuthor = $wpdb->get_results( $qAuthor , OBJECT );
			foreach ( $sAuthor as $k => $auth ) {
				$sVideos[ 'byAuthor' ][ 'items' ][ $auth->user_login ] = $auth->count;
				$sVideos[ 'byAuthor' ][ 'total' ] += $auth->count;
			}
			/* Getting By Status */
			$qStatus
				     = "
			SELECT 
				COUNT( distinct P.ID) as count,
				P.post_status as post_status
			FROM 
				$wpdb->posts P 
			WHERE
				1
				AND P.post_type = '" . CUTV_VIDEO_TYPE . "'
				and P.post_status in ('trash','pending','publish','draft','invalid')					
			GROUP BY P.post_status
				
		";
			$sStatus = $wpdb->get_results( $qStatus , OBJECT );
			foreach ( $sStatus as $k => $item ) {
				$sVideos[ 'byStatus' ][ 'items' ][ $item->post_status ] = $item->count;
				$sVideos[ 'byStatus' ][ 'total' ] += $item->count;
			}


			/* Getting By Service */
			$qService
				= "
			SELECT 
				COUNT( distinct P.ID) as total,
				COUNT( distinct if( 
					M.meta_key = 'cutv_video_service' AND M.meta_value = 'vimeo' , P.ID , NULL 
				)) as vimeo,
				COUNT( distinct if( 
					M.meta_key = 'cutv_video_service' AND M.meta_value = 'dailymotion' , P.ID , NULL 
				)) as dailymotion,
				COUNT( distinct if( 
					M.meta_key = 'cutv_video_service' AND M.meta_value = 'youtube' , P.ID , NULL 
				)) as youtube,
				COUNT( distinct if( 
					M.meta_key = 'cutv_video_service' AND (
						M.meta_value != 'youtube' AND M.meta_value != 'dailymotion' AND M.meta_value != 'vimeo'
					), P.ID , NULL 
				)) as unknown
				
			FROM 
				$wpdb->posts P 
				INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
			WHERE
				1
				AND P.post_type = '" . CUTV_VIDEO_TYPE . "'
				and P.post_status in ('trash','pending','publish','draft','invalid')
		";
			
			
			$sService = $wpdb->get_results( $qService , OBJECT );

			//new dBug( $sService );


			foreach ( $sService as $k => $item ) {
				$sVideos[ 'byService' ][ 'items' ][ 'youtube' ]     = $item->youtube;
				$sVideos[ 'byService' ][ 'items' ][ 'vimeo' ]       = $item->vimeo;
				$sVideos[ 'byService' ][ 'items' ][ 'dailymotion' ] = $item->dailymotion;
				$sVideos[ 'byService' ][ 'items' ][ 'unknown' ]     = $item->unknown;
				//$sVideos['byService']['items'][ 'total' ] = $item->total;
				//$sVideos['byStatus']['total'] += $item->count;
			}


			arsort( $sVideos[ 'byAuthor' ][ 'items' ] );
			arsort( $sVideos[ 'byCat' ][ 'items' ] );
			arsort( $sVideos[ 'byStatus' ][ 'items' ] );
			arsort( $sVideos[ 'byService' ][ 'items' ] );

			//new dBug($sVideos);
			return $sVideos;

		}
	}


	/*GET VIDEOS TO MANAGE THEM */
	if( ! function_exists( 'cutv_manage_videos' ) ) {
		function cutv_manage_videos( $args = array(), $items = array() ) {
			global $wpdb , $cutv_options;
			global $cutv_vs_ids;

			if( count( $cutv_vs_ids ) != 0 ) {
				$cutv_vs_ids_string          = " '" . implode( "', '" , $cutv_vs_ids[ 'ids' ] ) . "' ";
				$condition_active_services   = ' AND video_service IN ( ' . $cutv_vs_ids_string . ' ) ';
				$condition_active_services_2 = ' HAVING service IN ( ' . $cutv_vs_ids_string . ' ) ';
			} else {
				$condition_active_services   = '';
				$condition_active_services_2 = '';

			}

			//new dBug( $condition_active_services );

			if( ! is_array( $args ) ) {
				echo "BAD ARGUMENT FOR cutv_manage_videos";

				return FALSE;
			}

			$default_args  = array(
				'perpage'  => '10' ,
				'page'     => '1' ,
				'search'   => '' ,
				'ids'      => array() ,
				'status'   => array() ,
				'service'  => array() ,
				'author'   => array() ,
				'date'     => array() ,
				'category' => array() ,
				'orderby'  => '' ,
				'order'    => 'desc' ,
				'dupsBy'   => '' ,
				'getCats'  => FALSE ,
				'nopaging' => FALSE ,
				//'getCoreFields' => false,
			);
			$args          = cutv_extend( $args , $default_args );
			$fields_render = $limit_render = $conds_render = $joins_render = "";
			$fields        = $conds = $joins = array(
				'status'   => '' , //
				'search'   => '' , //
				'author'   => '' , //
				'date'     => '' , //
				'category' => '' ,
				'service'  => '' , //
				'getCats'  => '' , //
			);


			/* Get CATS */
			if( $args[ 'getCats' ] === TRUE ) {

				$joins[ 'getCats' ] = " LEFT JOIN $wpdb->term_relationships TR2 ON TR2.object_id = P.ID \n";
				$joins[ 'getCats' ] .= " LEFT JOIN $wpdb->term_taxonomy TT2 ON (TT2.taxonomy = 'category' AND TR2.term_taxonomy_id  = TT2.term_taxonomy_id) \n";
				$joins[ 'getCats' ] .= " LEFT JOIN $wpdb->terms T2 ON T2.term_id  = TT2.term_taxonomy_id \n";

				$fields[ 'getCats' ] .= " GROUP_CONCAT( DISTINCT T2.slug ORDER BY T2.slug ASC SEPARATOR ',') as cats_slugs, ";
				$fields[ 'getCats' ] .= " GROUP_CONCAT( DISTINCT T2.name ORDER BY T2.name ASC SEPARATOR ',') as cats_names, ";
			}

			/* APPLY ORDERS */
			if( $args[ 'orderby' ] != '' ) {
				$order = $args[ 'order' ];

				if( $args[ 'orderby' ] == "date" ) {
					$orderby = "P.post_date";
				} elseif( $args[ 'orderby' ] == "title" ) {
					$orderby = "P.post_title";
				} elseif( $args[ 'orderby' ] == "duration" ) {
					$orderby = "duration";
				} elseif( $args[ 'orderby' ] == "views" ) {
					$orderby = "views";
				} elseif( $args[ 'orderby' ] == "dupCount" ) {
					$orderby = "dupCount";
				}

				$order_render = " ORDER BY $orderby $order";
			} else {
				$order_render = " ORDER BY P.post_date DESC";
			}


			/* APPLY LIMIT */
			$actual_page = ( $args[ 'page' ] - 1 );
			$show_start  = $args[ 'perpage' ] * $actual_page;
			$length      = $args[ 'perpage' ];
			$show_end    = $show_start + $length;
			if( $args[ 'nopaging' ] != TRUE ) $limit_render = " LIMIT $show_start,$length ";

			/* Apply Search Filter */
			if( $args[ 'search' ] != '' ) {
				$q                 = $args[ 'search' ];
				$conds[ 'search' ] = " AND ( P.post_title LIKE '%" . $q . "%' OR P.post_title LIKE '%" . $q . "%' )";
			}


			/* Apply Date Filter */
			if( count( $args[ 'date' ] ) != 0 ) {
				$months = $years = '';
				foreach ( $args[ 'date' ] as $date ) {
					$x = explode( '-' , $date );
					$months .= "'" . $x[ 1 ] . "',";
					$years .= "'" . $x[ 0 ] . "',";
				}
				$months          = substr( $months , 0 , - 1 );
				$years           = substr( $years , 0 , - 1 );
				$conds[ 'date' ] = " AND ( YEAR(P.post_date) IN ($years) AND MONTH(P.post_date) IN ($months) ) ";
			}

			/* Apply author Filter */
			if( count( $args[ 'author' ] ) != 0 ) {
				$authors           = " '" . implode( "', '" , $args[ 'author' ] ) . "' ";
				$conds[ 'author' ] = " AND ( P.post_author IN ($authors)  ) ";
			}

			/* Apply Status Filter */
			if( count( $args[ 'status' ] ) != 0 ) {
				$statuses          = " '" . implode( "', '" , $args[ 'status' ] ) . "' ";
				$conds[ 'status' ] = " AND ( P.post_status IN ($statuses)  ) ";
			}

			/* Apply Cat Filter */
			if( count( $args[ 'category' ] ) != 0 ) {
				$category            = " '" . implode( "', '" , $args[ 'category' ] ) . "' ";
				$joins[ 'category' ] = " INNER JOIN $wpdb->term_relationships  TR ON TR.object_id = P.ID ";
				$joins[ 'category' ] .= " INNER JOIN $wpdb->term_taxonomy TT ON TR.term_taxonomy_id  = TT.term_taxonomy_id ";
				$conds[ 'category' ] = " AND ( TT.taxonomy = 'category' AND TT.term_id IN ( $category )	) ";
			}

			/* Apply Service Filter */
			if( count( $args[ 'service' ] ) != 0 ) {
				$services           = " '" . implode( "', '" , $args[ 'service' ] ) . "' ";
				$joins[ 'service' ] = " INNER JOIN $wpdb->postmeta M_SERVICE ON P.ID = M_SERVICE.post_id	";
				$conds[ 'service' ] = " AND (M_SERVICE.meta_key = 'cutv_video_service' AND M_SERVICE.meta_value IN ($services) ) ";
			}


			/* Rendering JOINS AND CONDS */
			foreach ( $joins as $join ) {
				$joins_render .= $join;
			}

			foreach ( $conds as $cond ) {
				$conds_render .= $cond;
			}

			foreach ( $fields as $field ) {
				$fields_render .= $field;
			}


			/* Is It DupToolBox ? */
			/********  HANDLE RESULTS ********/
			if( $args[ 'dupsBy' ] != '' ) {

				$dupsBy = $args[ 'dupsBy' ];

				$sql_all
					           = "
				SELECT 
					P.post_title as post_title , 
					P.post_date as post_date , 
					P.post_title as title , 
					P.video_service as service, 
					P.video_id as id , 
					count( distinct pid) as dupCount,
					SUM( video_views ) as views,
					GROUP_CONCAT( P.ID SEPARATOR ',' ) as ids,
					'' as duration,
					'publish' as status,
					'' as description,
					'' as post_id
				FROM (  
					SELECT 
						p.*, 
						GROUP_CONCAT(if(m.meta_key = 'cutv_video_service' , m.meta_value , NULL ) SEPARATOR '') as video_service,
						GROUP_CONCAT(if(m.meta_key = 'cutv_video_id' , m.meta_value , NULL ) SEPARATOR '') as video_id,
						GROUP_CONCAT(if(m.meta_key = 'cutv_video_views' , m.meta_value , NULL ) SEPARATOR '') as video_views,
						m.meta_value AS meta,
						m.post_id as pid
					FROM 
						$wpdb->posts AS p
						LEFT JOIN $wpdb->postmeta AS m ON p.ID = m.post_id
						WHERE 
							m.meta_key IN ('cutv_video_id','cutv_video_service' , 'cutv_video_views')
							AND p.post_status IN ('publish','draft','pending','trash','invalid' )
							AND p.post_type = '" . CUTV_VIDEO_TYPE . "'
						GROUP BY p.ID
						ORDER BY 
							m.meta_value ASC, p.post_date DESC
					) AS P
					WHERE
						1
						$condition_active_services
				 GROUP BY 
					$dupsBy
				HAVING dupCount > 1
				$order_render
				
			";

				$sql_all = cutv_get_duplicate_videos( array() , FALSE , FALSE , TRUE );
				//_d( $sql_all );

				$sql           = " $sql_all $limit_render	";
				$all           = $wpdb->get_results( $sql_all , OBJECT );
				$total_results = count( $all );
				$items         = $wpdb->get_results( $sql , OBJECT );
				$items_type    = "duplicates";
				$no_results_msg
					           = '
				<div class="wpvr_manage_noResults">
					<i class="fa fa-smile-o"></i><br />
					' . __( 'There are no duplicates.' , CUTV_LANG ) . '
				</div>
			';
			} else {
				$sql_all
					= "
						SELECT
							count( distinct P.ID)
						FROM
							$wpdb->posts P
							INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
							$joins_render
						WHERE
							1
							AND P.post_type = '" . CUTV_VIDEO_TYPE . "'
							AND P.post_status IN( 'publish','trash' ,'draft' , 'invalid' , 'pending' )
							$conds_render
						$order_render
				";

				$sql
					= "
						SELECT
							P.ID as post_id,
							P.post_title as title,
							P.guid as guid,
							P.post_content as description,
							P.post_status as status,
							P.post_date as date,
							P.post_author as author,
							$fields_render
							GROUP_CONCAT(DISTINCT if(M.meta_key = 'cutv_video_duration' , M.meta_value , NULL ) SEPARATOR '') as duration,
							GROUP_CONCAT(DISTINCT if(M.meta_key = 'cutv_video_service' , M.meta_value , NULL ) SEPARATOR '') as service,
							GROUP_CONCAT(DISTINCT if(M.meta_key = 'cutv_video_service_url' , M.meta_value , NULL ) SEPARATOR '') as service_url,
							GROUP_CONCAT(DISTINCT if(M.meta_key = 'cutv_video_service_views' , M.meta_value , NULL ) SEPARATOR '') as service_views,
							GROUP_CONCAT(DISTINCT if(M.meta_key = 'cutv_video_views' , M.meta_value , NULL ) SEPARATOR '') as views,
							GROUP_CONCAT(DISTINCT if(M.meta_key = 'cutv_video_id' , M.meta_value , NULL ) SEPARATOR '') as id
						FROM
							$wpdb->posts P
							INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
							$joins_render
						WHERE
							1
							AND P.post_type = '" . CUTV_VIDEO_TYPE . "'
							AND P.post_status IN( 'publish','trash' ,'draft' , 'invalid' , 'pending' )
							$conds_render
						GROUP by P.ID
						$condition_active_services_2
						$order_render
						$limit_render
				";

				$total_results = $wpdb->get_var( $sql_all );
				$items         = $wpdb->get_results( $sql , OBJECT );
				$items_type    = "videos";
				$no_results_msg
				               = '
					<div class="wpvr_manage_noResults">
						<i class="fa fa-frown-o"></i><br />
						' . __( 'There is no result to show.' , CUTV_LANG ) . '
					</div>
				';
			}

			//echo nl2br( $sql );new dBug( $args );
			//echo ( $total_results );

			$last_page = ceil( $total_results / $args[ 'perpage' ] );

			$return = array(
				'actual_page'    => $actual_page + 1 ,
				'last_page'      => $last_page ,
				'total_results'  => $total_results ,
				'show_start'     => $show_start + 1 ,
				'show_end'       => min( $show_end , $total_results ) ,
				'items'          => $items ,
				'html'           => '' ,
				'sql_error'      => $wpdb->last_error ,
				'sql'            => nl2br( $sql ) ,
				'no_results_msg' => $no_results_msg ,
				'items_type'     => $items_type ,
			);

			return $return;
		}
	}

	/* UNWANT VIDEOS */
	if( ! function_exists( 'cutv_unwant_videos' ) ) {
		function cutv_unwant_videos( $post_ids ) {
			global $cutv_unwanted , $cutv_unwanted_ids;
			foreach ( (array) $post_ids as $post_id ) {
				$metas = get_post_meta( $post_id );
				$video = array(
					'id'      => $metas[ 'cutv_video_id' ][ 0 ] ,
					'title'   => get_the_title( $post_id ) ,
					'thumb'   => $metas[ 'cutv_video_service_thumb' ][ 0 ] ,
					'service' => $metas[ 'cutv_video_service' ][ 0 ] ,
				);
				if( ! isset( $cutv_unwanted_ids[ $video[ 'service' ] ][ $video[ 'id' ] ] ) ) {
					$cutv_unwanted[]                                            = $video;
					$cutv_unwanted_ids[ $video[ 'service' ] ][ $video[ 'id' ] ] = 'unwanted';
				}

			}
			update_option( 'cutv_unwanted' , $cutv_unwanted );
			update_option( 'cutv_unwanted_ids' , $cutv_unwanted_ids );

			return TRUE;
		}
	}

	/* UNDO UNWANT VIDEOS */
	if( ! function_exists( 'cutv_undo_unwant_videos' ) ) {
		function cutv_undo_unwant_videos( $post_ids ) {
			global $cutv_unwanted , $cutv_unwanted_ids;
			foreach ( (array) $post_ids as $post_id ) {
				$metas    = get_post_meta( $post_id );
				$video_id = $metas[ 'cutv_video_id' ][ 0 ];
				$service  = $metas[ 'cutv_video_service' ][ 0 ];
				unset( $cutv_unwanted_ids[ $service ][ $video_id ] );
				foreach ( $cutv_unwanted as $k => $unwanted ) {
					if( $unwanted[ 'id' ] == $metas[ 'cutv_video_id' ][ 0 ] ) {
						unset( $cutv_unwanted[ $k ] );
					}
				}

			}
			update_option( 'cutv_unwanted' , $cutv_unwanted );
			update_option( 'cutv_unwanted_ids' , $cutv_unwanted_ids );

			return TRUE;
		}
	}

	/* Convert a post to a videoItem */
	if( ! function_exists( 'cutv_convert_post_to_videoItem' ) ) {
		function cutv_convert_post_to_videoItem( $post_id , $service = null ) {
			$post      = get_post( $post_id );
			$postmeta  = get_post_meta( $post_id );
			$thumbnail = get_the_post_thumbnail_url( $post_id , 'full' );
			//$thumb_meta = wp_get_attachment_metadata( get_post_thumbnail_id( $post_id ) , 'full' );
			//if( $thumb_meta != FALSE ) $thumb_file = CUTVWMT_UPLOAD_DIR . '/' . $thumb_meta[ 'file' ];
			//else $thumb_file = FALSE;

			$thumbs = cutv_get_post_thumbnail_files( $post_id );
			if( ! isset( $postmeta[ 'cutv_video_service_date' ] ) ) $service_date = '';
			else $service_date = $postmeta[ 'cutv_video_service_date' ][ 0 ];
			//_d( $thumbs );

			if( $service != null && ! isset( $postmeta[ 'cutv_video_service' ] ) ) $video_service = $service;
			else $video_service = $postmeta[ 'cutv_video_service' ][ 0 ];

			$videoItem = array(
				'id'               => $postmeta[ 'cutv_video_id' ][ 0 ] ,
				'viewIcon'         => '<img style="" width="150" height="115" src="' . $thumbnail . '">' ,
				'title'            => $post->post_title ,
				'description'      => $post->post_content ,
				'desc'             => $post->post_content ,
				'thumb'            => $thumbnail ,
				'hqthumb'          => $thumbnail ,
				'service'          => $video_service ,
				'icon'             => $thumbnail ,
				'url'              => $postmeta[ 'cutv_video_service_url' ][ 0 ] ,
				'originalPostDate' => $service_date ,
				'likes'            => 0 ,
				'dislikes'         => 0 ,
				'views'            => 0 ,
				'duration'         => $postmeta[ 'cutv_video_duration' ][ 0 ] ,
				'source_tags'      => array() ,
				'tags'             => array() ,
				'duplicate'        => FALSE ,
				'thumb_small'      => $thumbs[ 'cutv_wmt_thumb_small' ] ,
				'thumb_big'        => $thumbs[ 'cutv_wmt_thumb_big' ] ,
				'thumb_full'       => $thumbs[ 'full' ] ,
			);

			return $videoItem;
		}
	}

	/* REgenerate Thumbnails Files */
	if( ! function_exists( 'cutv_regenerate_thumbs' ) ) {
		function cutv_regenerate_thumbs( $post_ids ) {
			$done = array();
			if( ! is_array( $post_ids ) ) $post_ids = array( $post_ids );
			foreach ( (array) $post_ids as $post_id ) {
				$thumb_id     = get_post_thumbnail_id( $post_id );
				$fullsizepath = get_attached_file( $thumb_id );

				$images = cutv_get_post_thumbnail_files( $post_id );

				//_d( $images );

				$done[ $thumb_id ] = array();
				if( ! isset( $images[ 'cutv_wmt_thumb_small' ] ) || $images[ 'cutv_wmt_thumb_small' ] === FALSE ) {
					$done[ $thumb_id ][ 'cutv_wmt_thumb_small' ] = wp_update_attachment_metadata(
						$thumb_id ,
						wp_generate_attachment_metadata( $thumb_id , $fullsizepath )
					);
				}
				if( ! isset( $images[ 'cutv_wmt_thumb_big' ] ) || $images[ 'cutv_wmt_thumb_big' ] === FALSE ) {
					$done[ $thumb_id ][ 'cutv_wmt_thumb_big' ] = wp_update_attachment_metadata(
						$thumb_id ,
						wp_generate_attachment_metadata( $thumb_id , $fullsizepath )
					);
				}

				return $done;
			}
		}
	}


	if( ! function_exists( 'cutv_async_add_videos_callback' ) ) {
		function cutv_async_add_videos_callback( $response , $url , $request_info , $user_data , $time ) {

			$token    = $user_data[ 'token' ];
			$group_id = $user_data[ 'group_id' ];
			$json     = (array) json_decode( $response );
			$tmp_done = 'cutv_tmp_added_' . $token;
			$done     = get_option( $tmp_done );

			//echo $response ;
			foreach ( (array) $json as $post_id ) {
				if( $post_id === FALSE ) $done[ 'count_error' ] ++;
				else  $done[ 'count_done' ] ++;
			}
			$done[ 'adding' ][]         = $json;
			$done[ 'raw' ][ $group_id ] = array(
				'time'         => $time / 1000 ,
				'request_info' => $request_info ,
				'response'     => $response ,
				'json'         => $json ,
				//'debug'        => get_option( 'async_debug' ) ,
			);
			update_option( $tmp_done , $done );
		}
	}

	if( ! function_exists( 'cutv_async_add_videos' ) ) {
		function cutv_async_add_videos( $videos , $buffer ) {

			$taskers    = ( $buffer != 0 ) ? count( $videos ) / $buffer : 10;
			$RCX        = new RollingCurlX( $taskers );
			$token      = bin2hex( openssl_random_pseudo_bytes( 5 ) );
			$tmp_added  = 'cutv_tmp_added_' . $token;
			$tmp_videos = 'cutv_tmp_videos_' . $token;
			$timer      = cutv_chrono_time();


			update_option( $tmp_added , array(
				'exec_time'   => 0 ,
				'count_done'  => 0 ,
				'count_error' => 0 ,
				'adding'      => array() ,
				'raw'         => array() ,
			) );

			$videos_balanced = cutv_async_balance_items( $videos , $buffer );

			foreach ( (array) $videos_balanced as $group_id => $video ) {
				$async_json_url = cutv_capi_build_query( CUTV_ACTIONS_URL , array(
					'cutv_wpload'      => 1 ,
					'add_group_videos' => 1 ,
					'group_id'         => $group_id ,
					'token'            => $token ,
				) );
				//d( $source->sub_id );
				$RCX->addRequest(
					$async_json_url ,
					null ,
					'cutv_async_add_videos_callback' ,
					array(
						'token'    => $token ,
						'group_id' => $group_id ,
					) ,
					array(
						CURLOPT_FOLLOWLOCATION => FALSE ,
					)
				);
			}

			update_option( $tmp_videos , $videos_balanced );

			$RCX->execute();
			$done                = get_option( $tmp_added );
			$done[ 'exec_time' ] = cutv_chrono_time( $timer );
			delete_option( $tmp_added );
			delete_option( $tmp_videos );

			return ( $done );

		}
	}

	if( ! function_exists( 'cutv_add_videos' ) ) {
		function cutv_add_videos( $videos ) {

			global $cutv_imported;
			//global $cutv_deferred_ids , $cutv_unwanted_ids  ,$cutv_deferred , $cutv_options;

			$done = array();
			$i    = 0;
			foreach ( (array) $videos as $id => $video ) {
				$i ++;
				$done[ $id ] = cutv_add_video( $video , $cutv_imported , FALSE );
			}

			return $done;
		}
	}