<?php
	if( ! function_exists( 'wpvr_get_source_columns_stats' ) ) {
		function wpvr_get_source_columns_stats( $source ) {
			global $wpvr_vs , $service_not_enabled;
			if( ! isset( $wpvr_vs[ $source->service ] ) ) {
				return '';
			}

			$wantedVideos = ( ! isset( $source->wantedVideos ) || ( $source->wantedVideos == '' ) ) ? 0 : $source->wantedVideos;
			$videos       = ( ! isset( $source->count_imported ) ) ? 0 : $source->count_imported;
			$count_test   = ( ! isset( $source->count_test ) ) ? 0 : $source->count_test;
			$count_run    = ( ! isset( $source->count_run ) ) ? 0 : $source->count_run;

			if( $source->type == 'channel' ) {
				$subsources       = count( wpvr_parse_string( $source->channelIds ) );
				$subsources_label = __( 'channels' , WPVR_LANG );
				$subsources_line  = ' <b>' . wpvr_numberK( $subsources , TRUE ) . '</b> ' . $subsources_label . '<br/>';
			} elseif( $source->type == 'playlist' ) {
				$subsources       = count( wpvr_parse_string( $source->playlistIds ) );
				$subsources_label = __( 'playlists' , WPVR_LANG );
				$subsources_line  = ' <b>' . wpvr_numberK( $subsources , TRUE ) . '</b> ' . $subsources_label . '<br/>';
			} else {
				$subsources      = 0;
				$subsources_line = '';
			}
			if( $subsources > 1 ) $wantedVideos = $wantedVideos * $subsources;
			$more = apply_filters( 'wpvr_extend_source_column_stats' , '' , $source );

			return '
				<div  style="text-transform:uppercase;">
					<strong>' . wpvr_numberK( $wantedVideos , TRUE ) . '</strong> ' . __( 'Wanted videos' , WPVR_LANG ) . '<br/>
					<strong>' . wpvr_numberK( $videos , TRUE ) . '</strong> ' . __( 'Imported videos' , WPVR_LANG ) . '<br/>
					' . $subsources_line . '
					' . __( 'TESTED' , WPVR_LANG ) . ' <strong>' . wpvr_numberK( $count_test , TRUE ) . '</strong> ' . __( 'times' , WPVR_LANG ) . '<br/>
					' . __( 'RUN' , WPVR_LANG ) . ' <strong>' . wpvr_numberK( $count_run , TRUE ) . '</strong> ' . __( 'times' , WPVR_LANG ) . '<br/>
					' . $more . '
				</div>
			';
		}
	}
	if( ! function_exists( 'wpvr_get_source_columns_options' ) ) {
		function wpvr_get_source_columns_options( $source ) {
			global $wpvr_vs , $service_not_enabled;
			if( ! isset( $wpvr_vs[ $source->service ] ) ) {
				return '';
			}

			global $wpvr_options;

			/* FOLDERS */
			$folders = get_the_terms( $source->id , WPVR_SFOLDER_TYPE );
			if( $folders != FALSE ) {
				$folders_arr = array();
				foreach ( (array) $folders as $folder ) {
					$folders_arr[] = $folder->name;
				}
				$folders_str = implode( ', ' , $folders_arr );
			} else $folders_str = 'none';
			$folders_line
				= '
				<span class=" wpvr_source_span">
					<i class="fa fa-flag"></i>
					' . __( 'Folders' , WPVR_LANG ) . '  : <strong>' . $folders_str . '</strong>
				</span><br/>
			';

			/* POSTED BY */
			if( $source->postAuthor == 'default' ) $source->postAuthor = $wpvr_options[ 'postAuthor' ];
			$posting_author = get_userdata( $source->postAuthor );
			$posting_author_line
			                = '
				<span class=" wpvr_source_span">
					<i class="fa fa-user"></i>
					' . __( 'Posting as' , WPVR_LANG ) . ' <strong>' . $posting_author->user_login . '</strong>
				</span><br/>
			';

			/* SCHEDULED ? */
			if( $source->schedule == 'hourly' ) {
				$schedule_line_str = __( 'Scheduled' , WPVR_LANG ) . ' <b>' . __( 'every hour' , WPVR_LANG ) . '</b>';
			} elseif( $source->schedule == 'daily' ) {
				$schedule_line_str = __( 'Scheduled' , WPVR_LANG ) . ' <b>' . __( 'every' , WPVR_LANG ) . ' ' . __( 'day' , WPVR_LANG ) . ' ' . __( 'at' , WPVR_LANG ) . ' ' . $source->scheduleTime
				                     . '</b>';
			} elseif( $source->schedule == 'weekly' ) {
				$schedule_line_str = __( 'Scheduled' , WPVR_LANG ) . ' <b>' . __( 'every' , WPVR_LANG ) . ' ' . $source->scheduleDay . ' ' . __( 'at' , WPVR_LANG ) . ' ' . $source->scheduleTime
				                     . '</b>';
			} else {
				$schedule_line_str = __( 'Not scheduled' , WPVR_LANG );
			}
			$schedule_line_str = apply_filters( 'wpvr_extend_schedules_overview' , $schedule_line_str , $source->id );

			$schedule_line
				= '
				<span class=" wpvr_source_span">
					<i class="fa fa-calendar"></i>
					<strong>
						' . $schedule_line_str . '
					</strong>
				</span><br/>
			';

			/* POSTING CATS */
			$posting_cats_line = '';
			if( count( $source->postCats ) != 0 && $source->postCats != FALSE ) {
				$cats = array();
				foreach ( $source->postCats as $c ) {
					$cat = get_category( $c );
					if( $cat != null ) $cats[] = "<strong>" . $cat->slug . "</strong>, ";
				}
				$posting_cats_line
					= '
					<span class=" wpvr_source_span">
						<i class="fa fa-folder-open"></i>
						' . __( 'In' , WPVR_LANG ) . ' ' . implode( ', ' , $cats ) . '
					</span><br/>
				';
			}
			$more = apply_filters( 'wpvr_extend_source_column_options' , '' , $source );

			return '<br/>' .
			       $folders_line .
			       $posting_author_line .
			       $posting_cats_line .
			       $schedule_line .
			       $more;
		}
	}
	if( ! function_exists( 'wpvr_get_source_columns_name' ) ) {
		function wpvr_get_source_columns_name( $source ) {
			global $wpvr_vs , $service_not_enabled;


			//d( $source );
			$duplicateLink = 'admin.php?post=' . $source->id . '&action=duplicate_source';
			$editLink      = get_edit_post_link( $source->id );
			$trashLink     = wpvr_get_post_links( $source->id , 'trash' );
			$untrashLink   = wpvr_get_post_links( $source->id , 'untrash' );
			$deleteLink    = wpvr_get_post_links( $source->id , 'delete' );
			$testLink      = admin_url( 'admin.php?page=wpvr&test_sources&ids=' . $source->id , 'http' );
			$runLink       = admin_url( 'admin.php?page=wpvr&run_sources&ids=' . $source->id , 'http' );
			$exportLink    = admin_url( 'admin.php?page=wpvr&export_sources&ids=' . $source->id , 'http' );

			if( $source->name == '' ) $source->name = __( 'Untitled Source' , WPVR_LANG );

			$name_line
				= '
			<div class = "wpvr_source_name">
				' . strtoupper( $source->name ) . '
			</div>
		';
			if( ! isset( $wpvr_vs[ $source->service ] ) ) {
				$actions_line = $service_not_enabled;
			} else {
				if( $source->post_status == 'trash' ) {
					$actions_line
						= '
				<div class = "wpvr_source_links">
					<div class = "wpvr_source_action_button pull-left">
						<a href = "' . $untrashLink . '">
							<i class = "wpvr_link_icon fa fa-reply"></i>
							' . __( 'Untrash' , WPVR_LANG ) . '
						</a>
					</div>
					<div class = "wpvr_source_action_button pull-left">
						<a href = "' . $deleteLink . '">
							<i class = "wpvr_link_icon fa fa-remove"></i>
							' . __( 'Delete' , WPVR_LANG ) . '
						</a>
					</div>
				</div>
				';
				} else {
					$actions_line
						= '
					<div class = "wpvr_source_links">
						<div class = "wpvr_source_action_button pull-left">
							<a href = "' . $editLink . '">
								<i class = "wpvr_link_icon fa fa-pencil"></i>
								' . __( 'Edit' , WPVR_LANG ) . '
							</a>
						</div>
						<div class = "wpvr_source_action_button pull-left">
							<a href = "' . $trashLink . '">
								<i class = "wpvr_link_icon fa fa-trash"></i>
								' . __( 'Trash' , WPVR_LANG ) . '
							</a>
						</div>
						<div class = "wpvr_source_action_button pull-left">
							<a href = "' . $duplicateLink . '">
								<i class = "wpvr_link_icon fa fa-copy"></i>
								' . __( 'Clone' , WPVR_LANG ) . '
							</a>
						</div>
						<div class = "wpvr_source_action_button pull-left">
							<a href = "' . $testLink . '" target = "_blank">
								<i class = "wpvr_link_icon fa fa-eye"></i>
								' . __( 'Test' , WPVR_LANG ) . '
							</a>
						</div>
						<div class = "wpvr_source_action_button pull-left">
							<a href = "' . $runLink . '" target = "_blank">
								<i class = "wpvr_link_icon fa fa-bolt"></i>
								' . __( 'Run' , WPVR_LANG ) . '
							</a>
						</div>
						<div class = "wpvr_source_action_button pull-left">
							<a href = "' . $exportLink . '" target = "">
								<i class = "wpvr_link_icon fa fa-upload"></i>
								' . __( 'Export' , WPVR_LANG ) . '
							</a>
						</div>
					</div>
					';

				}
			}


			$more = apply_filters( 'wpvr_extend_source_column_name' , '' , $source );

			return $name_line . $actions_line . $more;
		}
	}
	if( ! function_exists( 'wpvr_get_source_columns_status' ) ) {
		function wpvr_get_source_columns_status( $source ) {
			global $wpvr_vs , $service_not_enabled;
			if( ! isset( $wpvr_vs[ $source->service ] ) ) {
				return '';
			}

			if( $source->post_status != 'trash' ) {
				$toggleLink = WPVR_ACTIONS_URL . '?wpvr_wpload&toggle_sources';
				ob_start();
				?>
				<i
					class = "fa fa-spin fa-refresh wpvr_toggle_loading "
					style = "display:none;"
					id = "toggle_loading_<?php echo $source->id; ?>"
				></i>
				<div
					class = "wpvr_source_status"
					id = "toggle_<?php echo $source->id; ?>"
					url = "<?php echo $toggleLink; ?>"
					status = "<?php echo $source->status; ?>"
				>
					<?php
						wpvr_make_switch_button(
							'' ,
							wpvr_get_button_state( $source->status , $invert = TRUE ) ,
							'wpvr_source_toggle' ,
							$source->id
						);
					?>
				</div>
				<?php
				$status_html = ob_get_contents();
				ob_get_clean();

				return $status_html;
			}
		}
	}
	if( ! function_exists( 'wpvr_get_source_columns_info' ) ) {
		function wpvr_get_source_columns_info( $source ) {
			global $wpvr_vs , $service_not_enabled;
			if( ! isset( $wpvr_vs[ $source->service ] ) ) {
				$echo = '';
			} else {

				$echo = '';

				$echo .= '' . wpvr_render_vs_source_type( $wpvr_vs[ $source->service ][ 'types' ][ $source->type ] , $wpvr_vs[ $source->service ] ) . '<br/><br/>';

				$echo .= '<span class=" wpvr_source_span">';
				$echo .= '<i class="fa fa-clock-o"></i>';
				$echo .= __( 'Added' , WPVR_LANG ) . ' <b>' . wpvr_human_time_diff( $source->id ) . '</b> <br/>';
				$echo .= '</span>';
				//$echo .= '<br/><br/>';
				$echo .= '<span class=" wpvr_source_span">';
				$echo .= '<i class="fa fa-user"></i>';
				$echo .= 'By <b>' . $source->sourceAuthor . '</b> <br/>';
				$echo .= '</span>';

				$echo .= '<span class=" wpvr_source_span">';
				$echo .= '<i class="fa fa-globe"></i>';
				$echo .= __( 'From' , WPVR_LANG ) . ' <b>' . $wpvr_vs[ $source->service ][ 'label' ] . '</b> <br/>';
				$echo .= '</span>';
			}

			$echo .= apply_filters( 'wpvr_extend_source_column_info' , '' , $source );

			return $echo;
		}
	}
	
	if( ! function_exists( 'wpvr_get_source_columns' ) ) {
		function wpvr_get_source_columns( $source_id , $column = 'all' , $post_status ) {

			if( ! isset( $_SESSION[ 'tmp_sources_columns' ] ) ) {
				$_SESSION[ 'tmp_sources_columns' ] = array();
			}
			if( ! isset( $_SESSION[ 'tmp_sources_columns' ][ $post_status ] ) ) {
				$_SESSION[ 'tmp_sources_columns' ][ $post_status ] = array();
			}

			if( ! isset( $_SESSION[ 'tmp_sources_columns' ][ $post_status ][ $source_id ] ) ) {
				$source              = wpvr_get_source( $source_id );
				$source->post_status = $post_status;

				$_SESSION[ 'tmp_sources_columns' ][ $post_status ][ $source_id ] = $source;
			} else {
				$source = $_SESSION[ 'tmp_sources_columns' ][ $post_status ][ $source_id ];
			}

			//d( $source );

			if( $column == 'all' ) {
				return array(
					'stats'   => wpvr_get_source_columns_stats( $source ) ,
					'options' => wpvr_get_source_columns_options( $source ) ,
					'info'    => wpvr_get_source_columns_info( $source ) ,
					'name'    => wpvr_get_source_columns_name( $source ) ,
					'status'  => wpvr_get_source_columns_status( $source ) ,
				);
			} elseif( $column == 'stats' ) {
				return wpvr_get_source_columns_stats( $source );
			} elseif( $column == 'options' ) {
				return wpvr_get_source_columns_options( $source );
			} elseif( $column == 'info' ) {
				return wpvr_get_source_columns_info( $source );
			} elseif( $column == 'name' ) {
				return wpvr_get_source_columns_name( $source );
			} elseif( $column == 'status' ) {
				return wpvr_get_source_columns_status( $source );
			} else {
				return '';
			}
		}
	}
	
	/* IMPORT A SINGLE SOURCE */
	if( ! function_exists( 'wpvr_import_source' ) ) {
		function wpvr_import_source( $source , $toggle_off = FALSE ) {
			
			$new_source_folders = $new_source_folders_ids = array();
			$new_source_cats    = $new_source_cats_slugs = array();
			
			
			//Get Post Cats
			foreach ( (array) $source->postCatsSlug as $cat ) {
				$catExists = term_exists( $cat->slug , 'category' );
				if( $catExists === null ) {
					$exists = 'NEWLY CREATED';
					$cat_id = wp_insert_category( array(
						'cat_name'             => $cat->name ,
						'category_description' => 'WP VIDEO ROBOT IMPORT' ,
						'category_nicename'    => $cat->slug ,
					) );
				} else {
					$cat_id = $catExists[ 'term_id' ];
					$exists = 'EXISTS';
				}
				
				$new_source_cats[]                = $cat_id;
				$new_source_cats_slugs[ $cat_id ] = array(
					'id'     => $cat_id ,
					'slug'   => $cat->slug ,
					'name'   => $cat->name ,
					'exists' => $exists ,
				);
			}
			if( isset( $source->folders ) ) {
				foreach ( (array) $source->folders as $folder ) {
					$catExists = term_exists( $folder->slug , WPVR_SFOLDER_TYPE );
					if( $catExists === null ) {
						$exists  = 'NEWLY CREATED';
						$term    = wp_insert_term(
							$folder->name , // the term
							WPVR_SFOLDER_TYPE , // the taxonomy
							array(
								'description' => 'WP VIDEO ROBOT IMPORT.' ,
								'slug'        => $folder->slug ,
							)
						);
						$term_id = $term[ 'term_id' ];
					} else {
						$term_id = $catExists[ 'term_id' ];
						$exists  = 'EXISTS';
					}
					$new_source_folders_ids[]       = $term_id;
					$new_source_folders[ $term_id ] = array(
						'id'     => $term_id ,
						'slug'   => $folder->slug ,
						'name'   => $folder->name ,
						'exists' => $exists ,
					);
					
				}
			}
			
			$new_source_id = wp_insert_post( array(
				'post_type'   => WPVR_SOURCE_TYPE ,
				'post_status' => 'publish' ,
			) );
			
			$forbidden_keys = array(
				'status' ,
				'service' ,
				'postCatsSlug' ,
				'folders' ,
			);
			
			
			// Get regular Metas
			update_post_meta( $new_source_id , 'wpvr_source_service' , $source->service );
			update_post_meta( $new_source_id , 'wpvr_source_status' , $toggle_off ? 'off' : $source->status );
			foreach ( $source as $key => $value ) {
				if( ! in_array( $key , $forbidden_keys ) ) {
					update_post_meta( $new_source_id , 'wpvr_source_' . $key , $value );
				}
			}
			
			
			if( count( $new_source_folders_ids ) != 0 ) {
				wp_set_post_terms(
					$new_source_id ,
					$new_source_folders_ids ,
					WPVR_SFOLDER_TYPE ,
					TRUE
				);
			}
			
			if( count( $new_source_cats ) != 0 ) {
				update_post_meta( $new_source_id , 'wpvr_source_postCats' , json_encode( $new_source_cats ) );
				update_post_meta( $new_source_id , 'wpvr_source_postCatsSlug' , json_encode( $new_source_cats_slugs ) );
			}
			
			
			return $new_source_id;
			
		}
	}
	
	/* GET A SINGLE SOURCE BY ID */
	if( ! function_exists( 'wpvr_get_source' ) ) {
		function wpvr_get_source( $source_id ) {
			
			$sources = wpvr_get_sources( array(
				'ids' => array( $source_id ) ,
			) );
			
			if( isset( $sources[ 0 ] ) ) $return = $sources[ 0 ];
			else $return = FALSE;
			
			return $return;
		}
	}
	
	/*GET SOURCES*/
	if( ! function_exists( 'wpvr_get_sources' ) ) {
		function wpvr_get_sources( $args = array() ) {
			global $wpdb;
			global $wpvr_options;
			
			if( ! is_array( $args ) ) {
				echo "BAD ARGUMENT FOR wpvr_get_sources";
				
				return FALSE;
			}
			
			$default_args = array(
				'ids'         => array() ,
				'status'      => '' , //on or off
				'type'        => '' ,
				'scheduled'   => '' , // now | inNextHour | today | empty_string
				'post_status' => array( 'publish' , 'trash' , 'draft' ) ,
				'orderby'     => 'P.post_date' ,
				'order'       => 'DESC' ,
				'by_id'       => FALSE ,
				'folders'     => array() ,
				'get_folders' => FALSE ,
			);
			
			$args      = wpvr_extend( $args , $default_args );
			$ids_array = "'" . implode( "','" , $args[ 'ids' ] ) . "'";
			
			
			//  new dBug( $args );
			
			$post_status = "'" . implode( "','" , $args[ 'post_status' ] ) . "'";
			//new dBug( $post_status );
			if( $args[ 'ids' ] != array() ) {
				$condIds = " AND P.ID IN (" . $ids_array . ") ";
			} else {
				$condIds = "";
			}
			
			if( $args[ 'type' ] == '' ) {
				$joinType = "";
				$condType = "";
			} elseif( in_array( $args[ 'type' ] , array( 'videos' , 'playlist' , 'trendy' , 'search' , 'channel' ) ) ) {
				$joinType = "INNER JOIN $wpdb->postmeta MType ON P.ID = MType.post_id	";
				$condType = " AND (MType.meta_key = 'wpvr_source_type' AND MType.meta_value = '" . $args[ 'type' ] . "') ";
			} else {
				echo "ERROR type param !";
				
				return array();
			}
			
			
			if( $args[ 'folders' ] != array() ) {
				$condFolders   = "AND WPTax.term_id IN ('" . implode( "','" , $args[ 'folders' ] ) . "')";
				$joinFolders
				               = "
					INNER JOIN $wpdb->term_relationships WPRelat on WPRelat.object_id = P.ID
					INNER JOIN $wpdb->term_taxonomy WPTax on WPTax.term_taxonomy_id = WPRelat.term_taxonomy_id
					INNER JOIN $wpdb->terms WPTerms on WPTerms.term_id = WPTax.term_id
				";
				$fieldsFolders = "GROUP_CONCAT( DISTINCT WPTerms.term_id separator ',') as folders ,";
				
			} else {
				$condFolders   = "";
				$joinFolders   = "";
				$fieldsFolders = "";
			}
			
			
			if( $args[ 'status' ] == '' ) {
				$joinStatus = "";
				$condStatus = "";
			} elseif( $args[ 'status' ] == 'on' ) {
				$joinStatus = "INNER JOIN $wpdb->postmeta MStatus ON P.ID = MStatus.post_id	";
				$condStatus = " AND (MStatus.meta_key = 'wpvr_source_status' AND MStatus.meta_value = 'on') ";
			} elseif( $args[ 'status' ] == 'off' ) {
				$joinStatus = "INNER JOIN $wpdb->postmeta MStatus ON P.ID = MStatus.post_id	";
				$condStatus = " AND (MStatus.meta_key = 'wpvr_source_status' AND MStatus.meta_value = 'off') ";
			} else {
				echo "ERROR status param !";
				
				return array();
			}
			$additional_metas_sql = "";
			//$additional_metas = array();
			//$additional_metas = apply_filters('wpvr_extend_sources_metas' , $additional_metas );
			//foreach( (array) $additional_metas as $meta ){
			//	$additional_metas_sql .= "GROUP_CONCAT(if(M.meta_key = 'wpvr_source_".$meta."' , M.meta_value , NULL ) SEPARATOR '' ) as ".$meta.", " ;
			//}
			//d( $additional_metas );
			//d( $additional_metas_sql );
			
			$video_services_group_concat_string = wpvr_render_video_services_sql_join_string();
			
			$querystr
				= "
			SELECT

				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_status' , M.meta_value , NULL ) SEPARATOR '') as status,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_name' , M.meta_value , NULL ) SEPARATOR '') as name,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_type' , M.meta_value , NULL ) SEPARATOR '') as type,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_service' , M.meta_value , NULL ) SEPARATOR '') as service,

				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_era' , M.meta_value , NULL ) SEPARATOR '' ) as era,

				" . $video_services_group_concat_string . "
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_wantedVideos' , M.meta_value , NULL ) SEPARATOR '' ) as wantedVideos,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_wantedVideosBool' , M.meta_value , NULL ) SEPARATOR '' ) as wantedVideosBool,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_order' , M.meta_value , NULL ) SEPARATOR '' ) as orderVideos,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_onlyNewVideos' , M.meta_value , NULL ) SEPARATOR '' ) as onlyNewVideos,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_getVideoStats' , M.meta_value , NULL ) SEPARATOR '' ) as getVideoStats,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_getVideoTags' , M.meta_value , NULL ) SEPARATOR '' ) as getVideoTags,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_autoPublish' , M.meta_value , NULL ) SEPARATOR '' ) as autoPublish,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_postAppend' , M.meta_value , NULL ) SEPARATOR '' ) as postAppend,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_appendCustomText' , M.meta_value , NULL ) SEPARATOR '' ) as appendCustomText,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_count_test' , M.meta_value , NULL ) SEPARATOR '' ) as count_test,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_count_run' , M.meta_value , NULL ) SEPARATOR '' ) as count_run,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_count_imported' , M.meta_value , NULL ) SEPARATOR '' ) as count_imported,
				GROUP_CONCAT( NULL SEPARATOR '' ) as postCatsSlug,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_postCats' , M.meta_value , NULL ) SEPARATOR '' ) as postCats,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_postAuthor' , M.meta_value , NULL ) SEPARATOR '' ) as postAuthor,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_postDate' , M.meta_value , NULL ) SEPARATOR '' ) as postDate,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_postTagsBool' , M.meta_value , NULL ) SEPARATOR '' ) as postTagsBool,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_postTags' , M.meta_value , NULL ) SEPARATOR '' ) as postTags,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_schedule' , M.meta_value , NULL ) SEPARATOR '' ) as schedule,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_schedule_time' , M.meta_value , NULL ) SEPARATOR '' ) as scheduleTime,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_schedule_day' , M.meta_value , NULL ) SEPARATOR '' ) as scheduleDay,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_schedule_date' , M.meta_value , NULL ) SEPARATOR '' ) as scheduleDate,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_publishedAfter_bool' , M.meta_value , NULL ) SEPARATOR '' ) as publishedAfter_bool,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_publishedAfter' , M.meta_value , NULL ) SEPARATOR '' ) as publishedAfter,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_publishedBefore_bool' , M.meta_value , NULL ) SEPARATOR '' ) as publishedBefore_bool,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_publishedBefore' , M.meta_value , NULL ) SEPARATOR '' ) as publishedBefore,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_havingViews' , M.meta_value , NULL ) SEPARATOR '' ) as havingViews,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_havingLikes' , M.meta_value , NULL ) SEPARATOR '' ) as havingLikes,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_videoQuality' , M.meta_value , NULL ) SEPARATOR '' ) as videoQuality,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_videoDuration' , M.meta_value , NULL ) SEPARATOR '' ) as videoDuration,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_postContent' , M.meta_value , NULL ) SEPARATOR '' ) as postContent,
				GROUP_CONCAT(if(M.meta_key = 'wpvr_source_status' , M.meta_value , NULL ) SEPARATOR '' ) as status,
				$fieldsFolders
				$additional_metas_sql
				P.post_author as sourceAuthor,
				P.ID as id
			FROM
				$wpdb->posts P
				INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
				$joinStatus
				$joinType
				$joinFolders
			WHERE
				P.post_status IN (" . $post_status . ")
				AND P.post_type = '" . WPVR_SOURCE_TYPE . "'
				$condStatus
				$condType
				$condIds
				$condFolders

			GROUP by P.ID
			ORDER BY " . $args[ 'orderby' ] . " " . $args[ 'order' ] . "
		";
			
			//d( $querystr );
			
			$sources = $wpdb->get_results( $querystr , OBJECT );
			
			//d( $sources );
			// d( $querystr );
			
			$s             = new DateTime( 'now' );
			$now           = array(
				'all'  => $s ,
				'day'  => strtolower( $s->format( 'l' ) ) ,
				'hour' => $s->format( 'h' ) ,
				'am'   => $s->format( 'A' ) ,
			);
			$sources_by_id = array();
			foreach ( $sources as $i => $source ) {
				$source_id = $source->id;
				
				if( $args[ 'get_folders' ] === TRUE ) {
					$folders     = array();
					$folders_obj = get_the_terms( $source->id , WPVR_SFOLDER_TYPE );
					foreach ( (array) $folders_obj as $folder ) {
						if( $folder === FALSE ) continue;
						$folders[] = array(
							'id'   => $folder->term_id ,
							'name' => $folder->name ,
							'slug' => $folder->slug ,
						);
					}
					$source->folders = $folders;
				} else {
					$source->folders = FALSE;
				}
				
				//$source->author = ;
				$source->postCats = json_decode( $source->postCats );
				
				//new dBug($postCats);
				$source->postCatsSlug = ( wpvr_get_tax_data( 'category' , $source->postCats ) );
				
				if( $source->era == '' ) {
					$source->era = 0;
				}
				if( $source->onlyNewVideos == 'default' ) {
					$source->onlyNewVideos = wpvr_get_button_state( $wpvr_options[ 'onlyNewVideos' ] );
				}
				if( $source->autoPublish == 'default' ) {
					$source->autoPublish = wpvr_get_button_state( $wpvr_options[ 'autoPublish' ] );
				}
				if( $source->getVideoStats == 'default' ) {
					$source->getVideoStats = wpvr_get_button_state( $wpvr_options[ 'getStats' ] );
				}
				if( $source->getVideoTags == 'default' ) {
					$source->getVideoTags = wpvr_get_button_state( $wpvr_options[ 'getTags' ] );
				}
				if( $source->postAuthor == 'default' ) {
					$source->postAuthor = ( $wpvr_options[ 'postAuthor' ] );
				}
				if( $source->postDate == 'default' ) {
					$source->postDate = ( $wpvr_options[ 'getPostDate' ] );
				}
				
				if( $source->publishedBefore_bool == 'default' ) $source->publishedBefore = $wpvr_options[ 'publishedBefore' ];
				
				if( $source->publishedAfter_bool == 'default' ) $source->publishedAfter = $wpvr_options[ 'publishedAfter' ];
				
				
				if( $source->orderVideos == 'default' ) {
					$source->orderVideos = ( $wpvr_options[ 'orderVideos' ] );
				}
				if( $source->wantedVideosBool == 'default' ) {
					$source->wantedVideos = ( $wpvr_options[ 'wantedVideos' ] );
				}
				if( $source->videoQuality == 'default' ) {
					$source->videoQuality = ( $wpvr_options[ 'videoQuality' ] );
				}
				if( $source->videoDuration == 'default' ) {
					$source->videoDuration = ( $wpvr_options[ 'videoDuration' ] );
				}
				
				if( $source->count_test == '' ) {
					$source->count_test = 0;
				}
				if( $source->count_run == '' ) {
					$source->count_run = 0;
				}
				if( $source->count_imported == '' ) {
					$source->count_imported = 0;
				}
				
				//$source->folders = explode( ',' , $source->folders );
				
				
				if( $source->postTagsBool == 'disabled' ) {
					$source->postTags = array();
				} elseif( $source->postTagsBool == 'default' ) {
					$source->postTags = explode( ',' , $wpvr_options[ 'postTags' ] );
				} else {
					$source->postTags = explode( ',' , $source->postTags );
				}
				
				//new dBug( $source->postTags );
				
				if( $args[ 'scheduled' ] != '' ) {
					//return $sources;
					if( in_array( $args[ 'scheduled' ] , array( 'now' , 'inNextHour' , 'today' ) ) ) {
						if( $source->scheduleTime == '' ) {
							continue;
						}
						$date = explode( 'H' , $source->scheduleTime );
						$hour = $date[ 0 ];
						
						if( $source->schedule == 'daily' && $args[ 'scheduled' ] == 'now' ) {
							if( $now[ 'hour' ] != $hour ) {
								unset( $sources[ $i ] );
							}
						} elseif( $source->schedule == 'weekly' && $args[ 'scheduled' ] == 'now' ) {
							if( $now[ 'hour' ] != $hour || $now[ 'day' ] != $source->scheduleDay ) {
								unset( $sources[ $i ] );
							}
						} elseif( $source->schedule == 'weekly' && $args[ 'scheduled' ] == 'today' ) {
							if( $now[ 'day' ] != $source->scheduleDay ) {
								unset( $sources[ $i ] );
							}
						}
					} else {
						echo "ERROR scheduled param";
						
						return array();
					}
				}
				$sources_by_id[ $source->id ] = $source;
			}
			if( $args[ 'by_id' ] ) return $sources_by_id;
			
			//new dBug ($sources);
			return $sources;
		}
	}
	
	/* MULTIPLE SOURCES */
	if( ! function_exists( 'wpvr_multiplicate_sources' ) ) {
		function wpvr_multiplicate_sources( $sources ) {
			global $wpvr_vs;
			$new_sources = array();
			foreach ( $sources as $source ) {
				
				
				if( isset( $wpvr_vs[ $source->service ][ 'types' ][ $source->type ][ 'multiplicate' ] ) )
					$multiplicate = $wpvr_vs[ $source->service ][ 'types' ][ $source->type ][ 'multiplicate' ];
				else $multiplicate = FALSE;
				
				
				if( $multiplicate != FALSE && $source->$multiplicate[ 'parent' ] != '' ) {
					$ids = explode( ',' , $source->$multiplicate[ 'parent' ] );
					foreach ( (array) $ids as $i => $id ) {
						if( $id != '' ) {
							$id = preg_replace( '/\s+/' , '' , $id );
							//d( $id );
							$k          = $i + 1;
							$new_source = clone $source;
							if( $source->name == '' ) {
								$source->name = __( 'Untitled Source' , WPVR_LANG );
							}
							
							if( $k != 1 ) {
								$new_source->name = $source->name . " ( #$k )";
							} else {
								$new_source->name = $source->name;
							}
							$new_source->sub_id                    = $source->id . "_$k";
							$new_source->$multiplicate[ 'parent' ] = '';
							$new_source->$multiplicate[ 'child' ]  = $id;
							array_push( $new_sources , $new_source );
							$new_source = new stdClass();
						}
					}
					
					
				} else {
					$new_sources[] = $source;
				}
			}
			
			return $new_sources;
		}
	}
	
	/* FETCH SOURCES */
	if( ! function_exists( 'wpvr_fetch_sources' ) ) {
		function wpvr_fetch_sources( $sources , $update_old_videos = FALSE , $debug = FALSE , $groupReturn = TRUE ) {
			
			global
			$wpvr_deferred_ids ,
			$wpvr_unwanted_ids ,
			$preDuplicates ,
			$wpvr_options ,
			$wpvr_vs;
			
			$sources = wpvr_multiplicate_sources( $sources );
			//new dBug( $sources );
			if( $update_old_videos ) $wpvr_imported = wpvr_update_imported_videos();
			else $wpvr_imported = get_option( 'wpvr_imported' );
			
			$videosToProcess = array(
				'count'         => array(
					'exec_time'     => 0 ,
					'count'         => 0 ,
					'absCount'      => 0 ,
					'dupCount'      => 0 ,
					'unwantedCount' => 0 ,
					'totalResults'  => 0 ,
					'count_sources' => 0 ,
					'count_total'   => count( $sources ) ,
				) ,
				'data'          => array(
					'count'         => 0 ,
					'totalResults'  => 0 ,
					'absCount'      => 0 ,
					'dupCount'      => 0 ,
					'unwantedCount' => 0 ,
					'execTime'      => 0 ,
					'exec_time'     => 0 ,
				) ,
				'items'         => array() ,
				'dataPerSource' => array() ,
			);
			$preDuplicates   = array();
			
			
			foreach ( (array) $sources as $source ) {
				//d( $source );
				/* SEARCH CONTEXT */
				$searchContext = FALSE;
				$searchContextValue = FALSE;
				if( $source->type == 'search_yt' ) {
					$metas = get_post_meta( $source->id );
					//d( $metas );
					$scType    = isset( $metas[ 'wpvr_source_searchContextType_yt' ] ) ? $metas[ 'wpvr_source_searchContextType_yt' ][ 0 ] : FALSE;
					$searchContext = $scType ;
					if( $scType == 'byRegion' ){
						$searchContext = $scType ;
						if( isset( $metas[ 'wpvr_source_searchContextRegion_yt' ] ) ) {
							$searchContextValue = $metas[ 'wpvr_source_searchContextRegion_yt' ][ 0 ];
						}
					}elseif( $scType == 'byChannel' ){
						$searchContext = $scType ;
						if( isset( $metas[ 'wpvr_source_searchContextChannel_yt' ] ) ) {
							$searchContextValue = $metas[ 'wpvr_source_searchContextChannel_yt' ][ 0 ];
						}
					}
				}
				/**********************/



				if( $source->postAppend == 'after' || $source->postAppend == 'before' ) {
					$vs      = $wpvr_vs[ $source->service ];
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
						'source'           => $source ,
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
						'searchContext'   => $searchContext ,
						'searchContextValue'   => $searchContextValue ,

					) ,
				);
				
				$sOptions = wpvr_prepare_sOptions_fields( $sOptions , $source );
				
				//d( $sOptions );return false;
				
				$tables = wpvr_prepare_tables_for_video_services(
					$wpvr_imported ,
					$wpvr_deferred_ids ,
					$wpvr_unwanted_ids
				);
				//global $wpvr_deferred ;
				//d( $wpvr_deferred );
				//d( $wpvr_deferred_ids );
				//d( $tables );
				
				
				$timer = wpvr_chrono_time();
				
				
				if( ! isset( $wpvr_vs[ $source->service ] ) ) continue;
				global $wpvr_vs;
				$videosFound                    = array();
				$videosFound[ 'source' ]        = $source;
				$videosFound[ 'nextPageToken' ] = '';
				
				$videosFound = $wpvr_vs[ $source->service ][ 'fetch_videos' ](
					$videosFound ,
					$sOptions ,
					$tables[ $source->service ][ 'merged' ]
				);
				
				//d( $videosFound );
				
				$exec_time                  = wpvr_chrono_time( $timer );
				$videosFound[ 'exec_time' ] = $exec_time;
				$videosFound[ 'source' ]    = $source;
				if( $debug ) {
					d( $videosFound );
				}
				
				if( ! $groupReturn ) {
					$videosToProcess[ 'items' ][] = $videosFound;
					
					$videosToProcess[ 'count' ][ 'count' ]         = $videosFound[ 'count' ] + $videosToProcess[ 'count' ][ 'count' ];
					$videosToProcess[ 'count' ][ 'absCount' ]      = $videosFound[ 'absCount' ] + $videosToProcess[ 'count' ][ 'absCount' ];
					$videosToProcess[ 'count' ][ 'dupCount' ]      = $videosFound[ 'dupCount' ] + $videosToProcess[ 'count' ][ 'dupCount' ];
					$videosToProcess[ 'count' ][ 'unwantedCount' ] = $videosFound[ 'unwantedCount' ] + $videosToProcess[ 'count' ][ 'unwantedCount' ];
					$videosToProcess[ 'count' ][ 'totalResults' ]  = $videosFound[ 'totalResults' ] + $videosToProcess[ 'count' ][ 'totalResults' ];
					$videosToProcess[ 'count' ][ 'exec_time' ]     = $videosFound[ 'exec_time' ] + $videosToProcess[ 'count' ][ 'exec_time' ];
					
					
				} else {
					$videosToProcess[ 'data' ][ 'count' ]         = $videosFound[ 'count' ] + $videosToProcess[ 'data' ][ 'count' ];
					$videosToProcess[ 'data' ][ 'absCount' ]      = $videosFound[ 'absCount' ] + $videosToProcess[ 'data' ][ 'absCount' ];
					$videosToProcess[ 'data' ][ 'dupCount' ]      = $videosFound[ 'dupCount' ] + $videosToProcess[ 'data' ][ 'dupCount' ];
					$videosToProcess[ 'data' ][ 'unwantedCount' ] = $videosFound[ 'unwantedCount' ] + $videosToProcess[ 'data' ][ 'unwantedCount' ];
					$videosToProcess[ 'data' ][ 'totalResults' ]  = $videosFound[ 'totalResults' ] + $videosToProcess[ 'data' ][ 'totalResults' ];
					$videosToProcess[ 'data' ][ 'exec_time' ]     = $videosFound[ 'exec_time' ] + $videosToProcess[ 'data' ][ 'exec_time' ];
					
					$dataPerSource                    = array();
					$dataPerSource[ 'sourceType' ]    = $videosFound[ 'source' ]->type;
					$dataPerSource[ 'sourceName' ]    = $videosFound[ 'source' ]->name;
					$dataPerSource[ 'sourceService' ] = $videosFound[ 'source' ]->service;
					$dataPerSource[ 'wantedVideos' ]  = $videosFound[ 'source' ]->wantedVideos;
					$dataPerSource[ 'count' ]         = $videosFound[ 'count' ];
					$dataPerSource[ 'absCount' ]      = $videosFound[ 'absCount' ];
					$dataPerSource[ 'dupCount' ]      = $videosFound[ 'dupCount' ];
					$dataPerSource[ 'unwantedCount' ] = $videosFound[ 'unwantedCount' ];
					$dataPerSource[ 'totalResults' ]  = $videosFound[ 'totalResults' ];
					$dataPerSource[ 'exec_time' ]     = $videosFound[ 'exec_time' ];
					$dataPerSource[ 'execTime' ]      = $videosFound[ 'exec_time' ];
					
					$videosToProcess[ 'dataPerSource' ][] = $dataPerSource;
					$videosToProcess[ 'items' ]           = array_merge( $videosToProcess[ 'items' ] , $videosFound[ 'items' ] );
				}
			}
			$preDuplicates = array();
			
			//d( $groupReturn );
			return $videosToProcess;
		}
	}
	
	/* RUN SOURCES asynchronously */
	if( ! function_exists( 'wpvr_run_sources_without_adding' ) ) {
		function wpvr_run_sources_without_adding( $sources , $is_autorun = FALSE , $echo_result = TRUE ) {
			global
			$wpvr_options ,
			$wpvr_imported ,
			$wpvr_act ,
			$wpvr_deferred ,
			$wpvr_deferred_ids ,
			$wpvr_unwanted_ids;
			
			foreach ( (array) $sources as $source ) {
				$count_run = get_post_meta( $source->id , 'wpvr_source_count_run' , TRUE );
				if( $count_run == '' ) $count_run = 1;
				else $count_run ++;
				update_post_meta( $source->id , 'wpvr_source_count_run' , $count_run );
			}
			
			
			$sourceResults = wpvr_fetch_sources(
				$sources ,
				WPVR_ENABLE_HARD_REFRESH ,
				WPVR_API_DEBUG_MODE , //debug_mode
				$groupReturn = TRUE
			);
			//d( $sourceResults );
			$log_msgs = array();
			//wpvr_set_debug( $sourceResults );
			foreach ( $sourceResults[ 'dataPerSource' ] as $data ) {
				
				if( ! isset( $data[ 'wantedVideos' ] ) ) $data[ 'wantedVideos' ] = 0;
				
				$labels = wpvr_get_service_labels( $data );
				
				$log_msgs[]
					= '
		
				<div class="wpvr_log_msgs_head">
					<div class = "wpvr_service_icon pull-left ' . $labels[ 'service' ] . '">
						' . strtoupper( $labels[ 'service_label' ] ) . '
					</div>
					<div class="wpvr_log_msgs_type pull-left">
					' . $labels[ 'type_HTML' ] . '
					</div>
					<div class="wpvr_log_msgs_title pull-left">
						SOURCE : ' . $data[ 'sourceName' ] . '
					</div>
					<div class="wpvr_clearfix"></div>
				</div>
				<div class="wpvr_clearfix"></div>
				<div>
					
					<div class="wpvr_log_msgs_nested">
						<b>Scan</b> : ' . wpvr_numberK( $data[ 'count' ] ) . ' items found on ' . wpvr_numberK( $data[ 'wantedVideos' ] ) . ' wanted items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Absolute Count</b> : ' . wpvr_numberK( $data[ 'absCount' ] ) . ' items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Duplicate Count</b> : ' . wpvr_numberK( $data[ 'dupCount' ] ) . ' items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Unwanted Count</b> : ' . wpvr_numberK( $data[ 'unwantedCount' ] ) . ' items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Total Results Count</b> : ' . wpvr_numberK( $data[ 'totalResults' ] ) . ' items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Execution Time</b> : ' . round( $data[ 'exec_time' ] , 3 ) . ' sec.
					</div>
				</div>
				<div class="wpvr_clearfix"></div>
			';
			}
			
			$action = $is_autorun ? __( "AUTO ASYNC RUNNING SOURCES" , WPVR_LANG ) : __( "MANUALLY ASYNC RUNNING SOURCES" , WPVR_LANG );
			
			wpvr_add_log( array(
				"time"     => date( 'Y-m-d H:i:s' ) , //Y-m-d H:i:s
				"action"   => $action , //Adding | FEtching | Running |
				"service"  => '' , //Adding | FEtching | Running |
				"type"     => 'run' , //Adding | FEtching | Running |
				"object"   => '#SOURCES#' , //
				"log_msgs" => $log_msgs ,
			) );
			
			$i                  = $count_deferred = $count_added = 0;
			$videos_to_be_added = array();
			foreach ( (array) $sourceResults[ 'items' ] as $video ) {
				
				if( $video[ 'postAppend' ] == 'customAfter' || $video[ 'postAppend' ] == 'customBefore' ) {
					$video[ 'postAppendName' ] = $video[ 'appendCustomText' ];
				} elseif( $video[ 'postAppend' ] == 'after' || $video[ 'postAppend' ] == 'before' ) {
					$video[ 'postAppendName' ] = $video[ 'appendSourceName' ];
				} else {
					$video[ 'postAppendName' ] = '';
				}
				
				$postDate                    = date( 'Y-m-d H:i:s' );
				$video[ 'importedPostDate' ] = $postDate;
				
				$video[ 'origin' ] = $is_autorun ? 'by AUTO RUN' : 'by MANUAL RUN';
				
				$videos_to_be_added[] = $video;
				
				$i ++;
			}
			//update_option( 'wpvr_deferred' , $wpvr_deferred );
			//update_option( 'wpvr_deferred_ids' , $wpvr_deferred_ids );
			
			return array(
				'videos' => $videos_to_be_added ,
				'counts' => $sourceResults[ 'data' ] ,
			
			
			);
			
		}
	}
	
	/* RUN SOURCES by source object dBug*/
	if( ! function_exists( 'wpvr_run_sources' ) ) {
		function wpvr_run_sources( $sources , $is_autorun = FALSE , $echo_result = TRUE ) {
			global
			$wpvr_vs ,
			$wpvr_options ,
			$wpvr_imported ,
			$wpvr_deferred ,
			$wpvr_deferred_ids ,
			$wpvr_unwanted_ids;
			
			$timer = wpvr_chrono_time();
			//if( ! isset( $wpvr_act[ 'act_status' ] ) || $wpvr_act[ 'act_status' ] != 1 ) {
			//	wpvr_refuse_access( TRUE );
			//	return FALSE;
			//}
			
			foreach ( $sources as $source ) {
				$source_id = $source->id;
				$count_run = get_post_meta( $source_id , 'wpvr_source_count_run' , TRUE );
				if( $count_run == '' ) {
					$count_run = 1;
				} else {
					$count_run ++;
				}
				update_post_meta( $source_id , 'wpvr_source_count_run' , $count_run );
			}
			
			
			$sourceResults = wpvr_fetch_sources(
				$sources ,
				WPVR_ENABLE_HARD_REFRESH ,
				WPVR_API_DEBUG_MODE , //debug_mode
				$groupReturn = TRUE
			);
			//d( $sourceResults );
			$log_msgs = array();
			if( $echo_result === FALSE && count( $sourceResults[ 'dataPerSource' ] ) == 0 ) {
				return array(
					'status'         => FALSE ,
					'msg'            => "NO ACTIVE SOURCES FOUND" ,
					'count_sources'  => count( $sources ) ,
					'count_added'    => 0 ,
					'count_deferred' => 0 ,
				);
			}
			foreach ( $sourceResults[ 'dataPerSource' ] as $data ) {
				
				$labels = wpvr_get_service_labels( $data );
				//d( $labels );
				
				if( ! isset( $data[ 'wantedVideos' ] ) ) $data[ 'wantedVideos' ] = 0;
				$log_msgs[]
					= '
					
				<div class="wpvr_log_msgs_head">
					<div class = "wpvr_service_icon pull-left ' . $labels[ 'service' ] . '">
						' . strtoupper( $labels[ 'service_label' ] ) . '
					</div>
					<div class="wpvr_log_msgs_type pull-left">
					' . $labels[ 'type_HTML' ] . '
					</div>
					<div class="wpvr_log_msgs_title pull-left">
						SOURCE : ' . $data[ 'sourceName' ] . '
					</div>
					<div class="wpvr_clearfix"></div>
				</div>
				<div class="wpvr_clearfix"></div>
				<div>
					
					<div class="wpvr_log_msgs_nested">
						<b>Scan</b> : ' . wpvr_numberK( $data[ 'count' ] ) . ' items found on ' . wpvr_numberK( $data[ 'wantedVideos' ] ) . ' wanted items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Absolute Count</b> : ' . wpvr_numberK( $data[ 'absCount' ] ) . ' items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Duplicate Count</b> : ' . wpvr_numberK( $data[ 'dupCount' ] ) . ' items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Total Results Count</b> : ' . wpvr_numberK( $data[ 'totalResults' ] ) . ' items
					</div>
					<div class="wpvr_log_msgs_nested">
						<b>Execution Time</b> : ' . round( $data[ 'exec_time' ] , 3 ) . ' sec.
					</div>
				</div>
				<div class="wpvr_clearfix"></div>
			';
			}
			
			if( $is_autorun ) {
				$action = __( "AUTO RUNNING SOURCES" , WPVR_LANG );
			} else {
				$action = __( "MANUALLY RUNNING SOURCES" , WPVR_LANG );
			}
			wpvr_add_log( array(
				"time"     => date( 'Y-m-d H:i:s' ) , //Y-m-d H:i:s
				"action"   => $action , //Adding | FEtching | Running |
				"service"  => $data[ 'sourceService' ] , //Adding | FEtching | Running |
				"type"     => 'run' , //Adding | FEtching | Running |
				"object"   => '#SOURCES#' , //
				"log_msgs" => $log_msgs ,
			) );
			
			
			$i              = 0;
			$count_deferred = 0;
			$count_added    = 0;
			foreach ( $sourceResults[ 'items' ] as $video ) {
				
				//$video['postAppend'] = $video->postAppend ;
				if( $video[ 'postAppend' ] == 'customAfter' || $video[ 'postAppend' ] == 'customBefore' ) {
					$video[ 'postAppendName' ] = $video[ 'appendCustomText' ];
				} elseif( $video[ 'postAppend' ] == 'after' || $video[ 'postAppend' ] == 'before' ) {
					$video[ 'postAppendName' ] = $video[ 'appendSourceName' ];
				} else {
					$video[ 'postAppendName' ] = '';
				}
				
				
				$i ++;
				if( $wpvr_options[ 'deferAdding' ] == 'on' && $i > $wpvr_options[ 'deferBuffer' ] ) {
					//I should define server timezone
					$postDate                    = date( 'Y-m-d H:i:s' );
					$video[ 'importedPostDate' ] = $postDate;
					
					$tables = wpvr_prepare_tables_for_video_services(
						$wpvr_imported ,
						$wpvr_deferred_ids ,
						$wpvr_unwanted_ids
					);
					
					$wpvr_deferred_ids                        = array();
					$wpvr_deferred_ids[ $video[ 'service' ] ] = $tables[ $video[ 'service' ] ][ 'wpvr_deferred_ids' ];
					
					$wpvr_deferred[] = $video;
					
					$wpvr_deferred_ids[ $video[ 'service' ] ][ $video[ 'id' ] ] = 'deferred';
					
					$count_deferred ++;
					if( $is_autorun ) {
						$video[ 'origin' ] = "by AUTO RUN";
					} else {
						$video[ 'origin' ] = "by RUN";
					}
					wpvr_add_log( array(
						"time"     => date( 'Y-m-d H:i:s' ) ,
						//Y-m-d H:i:s
						"action"   => __( 'DEFER A VIDEO ADDING' , WPVR_LANG ) . ' (' . $video[ 'origin' ] . ')' ,
						//Adding | FEtching | Running |
						"type"     => 'defer' ,
						//Adding | FEtching | Running |
						"icon"     => $video[ 'icon' ] ,
						//Adding | FEtching | Running |
						"object"   => $video[ 'id' ] ,
						//
						"log_msgs" => array(
							__( 'DEFERRED VIDEO' , WPVR_LANG ) ,
							__( 'TITLE' , WPVR_LANG ) . ' : ' . $video[ 'title' ] ,
						) ,
					) );
				} else {
					$count_added ++;
					if( $is_autorun ) {
						$video[ 'origin' ] = __( "by AUTO RUN" , WPVR_LANG );
					} else {
						$video[ 'origin' ] = __( "by RUN" , WPVR_LANG );
					}
					
					//new dBug( $video );
					//echo "<br/> BEFORE ADD :";
					//new dBug( $wpvr_imported );
					//d( $video );
					wpvr_add_video( $video , $wpvr_imported );
				}
			}
			update_option( 'wpvr_deferred' , $wpvr_deferred );
			update_option( 'wpvr_deferred_ids' , $wpvr_deferred_ids );
			
			if( $echo_result === FALSE ) {
				return array(
					'status'         => TRUE ,
					'msg'            => '' ,
					'exec_time'      => wpvr_chrono_time( $timer ) ,
					'count_added'    => $count_added ,
					'count_deferred' => $count_deferred ,
					'count_sources'  => count( $sources ) ,
					'count_skipped'  => $sourceResults[ 'data' ][ 'dupCount' ] ,
				);
				
			}
			?>
			<div class = "wrap wpvr_wrap">
			<h2 class = "wpvr_title">
				<?php wpvr_show_logo(); ?>
				<i class = "wpvr_title_icon fa fa-bolt	"></i>
				<?php echo __( 'Running Sources' , WPVR_LANG ); ?>
				<div class = "wpvr_clearfix"></div>
			</h2>
			
			<p>
				<?php echo '<b>' . $count_added . '</b> ' . __( 'video(s) successfully imported.' , WPVR_LANG ); ?>
				<br/><?php echo '<b>' . $count_deferred . '</b> ' . __( 'videos(s) deferred.' , WPVR_LANG ); ?>
				<br/><br/>
				<a href = "#" id = "backBtn">
					<?php echo __( 'Go Back' , WPVR_LANG ); ?>
				</a>
			</p>
			</div><?php
		}
	}
	
	/* TEST SOURCES by source object */
	if( ! function_exists( 'wpvr_test_sources' ) ) {
		function wpvr_test_sources( $sources ) {
			
			global $wpvr_act , $wpvr_vs , $wpvr_options;
			
			$session_token = md5( uniqid( rand() , TRUE ) );
			
			if( WPVR_ENABLE_ASYNC_FETCH ) {
				delete_option( 'async_debug' );
				$sourceResults = wpvr_async_fetch_sources(
					$sources ,
					WPVR_ENABLE_HARD_REFRESH ,
					WPVR_API_DEBUG_MODE
				);
			} else {
				$timer = wpvr_chrono_time();
				
				$sourceResults = wpvr_fetch_sources(
					$sources ,
					WPVR_ENABLE_HARD_REFRESH ,
					WPVR_API_DEBUG_MODE , //$debug_mode
					$groupReturn = FALSE
				);
				
				$sourceResults[ 'exec_time' ] = wpvr_chrono_time( $timer );
				
				if( count( $sourceResults[ 'items' ] ) == 0 ) return FALSE;
				
			}
			
			$sourceResults = (array) ( $sourceResults );
			//d( $sourceResults );
			
			//return FALSE;
			
			$tmp_results = array();
			
			
			if( WPVR_ENABLE_ASYNC_FETCH ) {
				$async = __( 'Asynchronous Execution' , WPVR_LANG );
			} else {
				$async = __( 'Regular Execution' , WPVR_LANG );
			}
			$info_message
				= '
				<div>
					<li>
						<strong>' . __( 'Grouping Mode' , WPVR_LANG ) . '</strong> : ' . $async . '
					</li>
					<li>
						<strong>' . $sourceResults[ 'count' ][ 'count_total' ] . '</strong> ' . __( 'Sources' , WPVR_LANG ) . '
						 ' . __( 'executed in' , WPVR_LANG ) . '
						<strong>' . round( $sourceResults[ 'exec_time' ] , 3 ) . '</strong> ' . __( 'seconds' , WPVR_LANG ) . '.
					</li>
					<li>
						<strong>' . __( 'Total Parsed ' , WPVR_LANG ) . '</strong> : ' . $sourceResults[ 'count' ][ 'absCount' ] . '
						' . __( 'videos' , WPVR_LANG ) . '
					</li>
					<li>
						<strong>' . __( 'Skipped Duplicates' , WPVR_LANG ) . '</strong> : ' . $sourceResults[ 'count' ][ 'dupCount' ] . '
						' . __( 'videos' , WPVR_LANG ) . '
					</li>
					<li>
						<strong>' . __( 'Skipped Unwanted' , WPVR_LANG ) . '</strong> : ' . $sourceResults[ 'count' ][ 'unwantedCount' ] . '
						' . __( 'videos' , WPVR_LANG ) . '
					</li>
					<li>
						<strong>' . __( 'Total Found' , WPVR_LANG ) . '</strong> : ' . wpvr_numberK( $sourceResults[ 'count' ][ 'totalResults' ] ) . '
						' . __( 'videos' , WPVR_LANG ) . '
					</li>
				</div>
			';
			//d($sourceResults);return false;
			
			?>
			<form id = "wpvr_test_form" class = "wpvr_test_screen_wrap" url = "<?php echo WPVR_ACTIONS_URL; ?>" style = "display:none;">
				<div class = "wpvr_test_form_buttons top">
					<button
						class = "wpvr_button wpvr_black_button pull-left wpvr_test_form_info"
						msg = "<?php echo $info_message; ?>"
					>
						<i class = "wpvr_button_icon fa fa-info-circle"></i><?php _e( 'Info' , WPVR_LANG ); ?>
					</button>
					<button class = "wpvr_button pull-left wpvr_test_form_toggleAll" state = "off">
						<i class = "wpvr_button_icon fa fa-check-square-o"></i>
						<?php _e( 'CHECK ALL VIDEOS' , WPVR_LANG ); ?>
					</button>
					<button
						class = "wpvr_button pull-left wpvr_collapse wpvr_collapse_sections open"
						zone_id = "all"
						is_btn = "1"
					>
						<i class = "fa fa-chevron-up openIcon"></i>
						<i class = "fa fa-chevron-down closeIcon"></i>
						<?php _e( 'Collapse All' , WPVR_LANG ); ?>
					</button>
					<button class = "wpvr_button pull-left" id = "wpvr_test_form_refresh">
						<i class = "wpvr_button_icon fa fa-refresh"></i><?php _e( 'SEARCH AGAIN' , WPVR_LANG ); ?>
					</button>
					<?php if( WPVR_BATCH_ADDING_ENABLED === TRUE ) { ?>
						<button class = "wpvr_button pull-right wpvr_test_form_add" session = "<?php echo $session_token; ?>">
							<i class = "wpvr_button_icon fa fa-download"></i>
							<?php _e( 'BATCH ADD SELECTED' , WPVR_LANG ); ?>
						</button>
					<?php } ?>
					<button class = "wpvr_red_button wpvr_button pull-right wpvr_test_form_unwanted" id = "wpvr_test_form_unwanted"
					        session = "<?php echo $session_token; ?>">
						<i class = "wpvr_button_icon fa fa-ban"></i>
						<?php echo __( 'ADD' , WPVR_LANG ) . '<span class="wpvr_count_checked"> </span>' . __( 'TO UNWANTED' , WPVR_LANG ); ?>
					</button>
					<button class = "wpvr_button pull-right wpvr_test_form_add_each wpvr_green_button" id = "wpvr_test_form_add_each"
					        session = "<?php echo $session_token; ?>">
						<i class = "wpvr_button_icon fa fa-cloud-download"></i>
						<?php echo __( 'ADD' , WPVR_LANG ) . '<span class="wpvr_count_checked"> </span>' . __( 'ITEMS' , WPVR_LANG ); ?>
					</button>
					<div class = "wpvr_clearfix"></div>
				</div>
				<div class = "wpvr_clearfix"></div>
				<div class = "wpvr_videos">
					<?php //d( $sourceResults ); ?>
					<?php if( ! isset( $sourceResults[ 'items' ] ) ) $sourceResults[ 'items' ] = array(); ?>
					<?php foreach ( (array) $sourceResults[ 'items' ] as $sourceResult ) { ?>
						<?php
						$sourceResult = (array) $sourceResult;
						//d( $sourceResult );
						$count_test = get_post_meta( $sourceResult[ 'source' ]->id , 'wpvr_source_count_test' , TRUE );
						if( $count_test == '' ) {
							$count_test = 1;
						} else {
							$count_test ++;
						}
						update_post_meta( $sourceResult[ 'source' ]->id , 'wpvr_source_count_test' , $count_test );
						
						$service = $sourceResult[ 'source' ]->service;
						
						
						$labels = wpvr_get_service_labels( array(
							'sourceService' => $sourceResult[ 'source' ]->service ,
							'sourceType'    => $sourceResult[ 'source' ]->type ,
						) );
						if( WPVR_ENABLE_ASYNC_FETCH ) {
							$fetch_title = __( 'Asynchron Testing of a Source' , WPVR_LANG );
						} else {
							$fetch_title = __( 'Regular Testing of a Source' , WPVR_LANG );
						}
						$log_head
							= '
			
							<div class="wpvr_log_msgs_head">
								<div class = "wpvr_service_icon pull-left ' . $labels[ 'service' ] . '">
									' . strtoupper( $labels[ 'service_label' ] ) . '
								</div>
								<div class="wpvr_log_msgs_type pull-left">
								' . $labels[ 'type_HTML' ] . '
								</div>
								<div class="wpvr_log_msgs_title pull-left">
									SOURCE : ' . $sourceResult[ 'source' ]->name . '
								</div>
								<div class="wpvr_clearfix"></div>
							</div>
							<div class="wpvr_clearfix"></div>
						';
						
						//LOG ACTIONS
						$log_msgs = array(
							$log_head ,
							//'<b>Service </b> : ' . strtoupper( $service ) . '' ,
							'<b>Scan</b> : ' . wpvr_numberK( $sourceResult[ 'count' ] ) . ' items found on ' . wpvr_numberK( $sourceResult[ 'source' ]->wantedVideos ) . ' wanted items' ,
							'<b> Absolute Count</b> : ' . wpvr_numberK( $sourceResult[ 'absCount' ] ) . ' items ' ,
							'<b> Duplicate Count</b> : ' . wpvr_numberK( $sourceResult[ 'dupCount' ] ) . ' items ' ,
							'<b> Unwanted Count</b> : ' . wpvr_numberK( $sourceResult[ 'unwantedCount' ] ) . ' items ' ,
							'<b>Total Results </b> : ' . wpvr_numberK( $sourceResult[ 'totalResults' ] ) . ' items ' ,
						);
						if( isset( $sourceResult[ 'exec_time' ] ) ) {
							$log_msgs[] = '<b>Exec Time </b> : ' . round( $sourceResult[ 'exec_time' ] , 3 ) . ' sec. ';
						}
						
						wpvr_add_log( array(
							"time"     => date( 'Y-m-d H:i:s' ) ,
							"action"   => strtoupper( $fetch_title ) ,
							"service"  => strtoupper( $sourceResult[ 'source' ]->service ) ,
							"type"     => 'test' ,
							//"object"   => $sourceResult[ 'source' ]->name ,
							"object"   => ' #SOURCES# ' ,
							"log_msgs" => $log_msgs ,
						) );
						
						
						if( isset( $sourceResult[ 'source' ]->sub_id ) ) {
							$sub_id = $sourceResult[ 'source' ]->sub_id;
						} else {
							$sub_id = $sourceResult[ 'source' ]->id;
						}
						
						if( $sourceResult[ 'source' ]->name == '' ) {
							$sourceResult[ 'source' ]->name = __( 'Untitled Source' , WPVR_LANG );
						}
						
						
						$vs      = $wpvr_vs[ $sourceResult[ 'source' ]->service ];
						$vs_type = $vs[ 'types' ][ $sourceResult[ 'source' ]->type ];
						
						$source_subheader = $vs[ 'render_subheader' ]( $sourceResult[ 'source' ] );
						//d( $source_subheader );
						
						?>
						
						<div class = "wpvr_source_result open" id = "source_<?php echo $sub_id; ?>">
							
							<div class = "wpvr_source_buttons pull-right">
								
								<button
									class = "wpvr_button pull-right wpvr_collapse wpvr_collapse_sections open"
									zone_id = "source_<?php echo $sub_id; ?>"
									is_btn = "1"
								>
									<i class = "fa fa-chevron-up openIcon"></i>
									<i class = "fa fa-chevron-down closeIcon"></i>
									<?php _e( 'Collapse' , WPVR_LANG ); ?>
								</button>
								
								<button class = "pull-right wpvr_button wpvr_check_all_section"
								        zone_id = "<?php echo $sub_id; ?>" state = "off">
									<i class = "wpvr_button_icon fa fa-check-square-o"></i>
									<?php _e( 'Check All Section' , WPVR_LANG ); ?>
								</button>
								
								<a class = "pull-right wpvr_button wpvr_black_button"
								   href = "<?php echo get_edit_post_link( $sub_id ); ?>"
								   target = "_blank"
								>
									<i class = "wpvr_link_icon fa fa-pencil"></i>EDIT SOURCE
								</a>
							
							</div>
							<div class = "wpvr_source_head pull-left">
								
								<div class = "wpvr_service_icon pull-left <?php echo $vs[ 'id' ]; ?>">
									<?php echo strtoupper( $vs[ 'label' ] ); ?>
								</div>
								<div class = "wpvr_service_icon_type pull-left">
									<?php echo wpvr_render_vs_source_type( $vs_type , $vs ); ?>
								</div>
								<div class = "wpvr_source_title pull-left">
									<?php echo $sourceResult[ 'source' ]->name; ?>
								</div>
								<div class = "wpvr_clearfix"></div>
							</div>
							<div class = "wpvr_clearfix"></div>
							<?php echo $source_subheader; ?>
							
							<?php
								
								
								if( ! isset( $sourceResult[ 'recalls' ] ) ) {
									$sourceResult[ 'recalls' ] = 0;
								}
								$posting_author = $sourceResult[ 'source' ]->postAuthor;
								$posting_cat    = $sourceResult[ 'source' ]->postCats;
								
								if( $posting_author == 'default' ) {
									$posting_author = $wpvr_options[ 'postAuthor' ];
								}
								
								$posting_author = get_userdata( $posting_author );
								
								
								$posting_insights = array();
								
								// AutoPublioshing ?
								$autoPublish        = ( $sourceResult[ 'source' ]->autoPublish == 'on' ) ? __( 'Posting as Draft' , WPVR_LANG ) : __( 'Autopublishing' , WPVR_LANG );
								$posting_insights[] = array(
									'title' => $autoPublish ,
									'icon'  => 'fa-thumb-tack' ,
									'value' => $autoPublish ,
								);
								
								// AutoPublioshing ?
								$postingDate        = ( $sourceResult[ 'source' ]->postDate == 'original' ) ? __( 'Original Date' , WPVR_LANG ) : __( 'Updated Date' , WPVR_LANG );
								$posting_insights[] = array(
									'title' => $postingDate ,
									'icon'  => 'fa-calendar' ,
									'value' => $postingDate ,
								);
								
								// Posting As
								$posting_insights[] = array(
									'title' => __( 'Posting as' , WPVR_LANG ) . ' : ' . $posting_author->user_login ,
									'icon'  => 'fa-user' ,
									'value' => __( 'Posting as' , WPVR_LANG ) . ' : ' . $posting_author->user_login ,
								);
								
								// Posting In
								if( is_array( $posting_cat ) && count( $posting_cat ) != 0 && $posting_cat != FALSE ) {
									$categories = array();
									foreach ( $posting_cat as $c ) {
										$cat          = get_category( $c );
										$categories[] = "<strong>" . $cat->slug . "</strong>";
									}
									$posting_insights[] = array(
										'title' => __( 'Posting as' , WPVR_LANG ) . ' : ' . $posting_author->user_login ,
										'icon'  => 'fa-folder-open' ,
										'value' => __( 'Posting in' , WPVR_LANG ) . ' : ' . join( ', ' , $categories ) ,
									);
								}
								
								$fetching_insights = array(
									
									// Scanned Count
									array(
										'title' => __( 'Scanned' , WPVR_LANG ) ,
										'icon'  => 'fa-line-chart' ,
										'value' => wpvr_numberK( $sourceResult[ 'count' ] ) . ' ' .
										           __( ' videos' , WPVR_LANG ) . ' / ' .
										           wpvr_numberK( $sourceResult[ 'source' ]->wantedVideos ) . ' ' .
										           __( 'wanted' , WPVR_LANG ) ,
									) ,
									
									// Absolute Count
									array(
										'title' => __( 'Absolute count' , WPVR_LANG ) ,
										'icon'  => 'fa-search' ,
										'value' => wpvr_numberK( $sourceResult[ 'real_count' ] ) . ' ' . __( 'parsed' , WPVR_LANG ) ,
									) ,
									
									// Duplicate count
									array(
										'title' => __( 'Duplicate count' , WPVR_LANG ) ,
										'icon'  => 'fa-copy' ,
										'value' => wpvr_numberK( $sourceResult[ 'dupCount' ] ) . ' ' . __( 'duplicates' , WPVR_LANG ) ,
									) ,
									
									// Unwanted count
									array(
										'title' => __( 'Unwanted count' , WPVR_LANG ) ,
										'icon'  => 'fa-ban' ,
										'value' => wpvr_numberK( $sourceResult[ 'unwantedCount' ] ) . ' ' . __( 'unwanted' , WPVR_LANG ) ,
									) ,
									
									// Total results
									array(
										'title' => __( 'Total results' , WPVR_LANG ) ,
										'icon'  => 'fa-sort-amount-desc' ,
										'value' => wpvr_numberK( $sourceResult[ 'totalResults' ] ) . ' ' . __( 'found' , WPVR_LANG ) ,
									) ,
									
									// Exec Time
									array(
										'title' => __( 'Execution Time' , WPVR_LANG ) ,
										'icon'  => 'fa-clock-o' ,
										'value' => round( $sourceResult[ 'exec_time' ] , 3 ) . ' ' . __( 'seconds' , WPVR_LANG ) ,
									) ,
									
									// Recalls
									array(
										'title' => __( 'Recalls' , WPVR_LANG ) ,
										'icon'  => 'fa-history' ,
										'value' => '<strong>' . wpvr_numberK( $sourceResult[ 'recalls' ] ) . '</strong> ' . __( 'recalls' , WPVR_LANG ) ,
									) ,
								
								);
							
							
							?>
							<div class = "wpvr_source_insights">
								<?php render_source_insights( $fetching_insights ); ?>
								<div class = "wpvr_clearfix"></div>
								<?php render_source_insights( $posting_insights , 'black' ); ?>
							</div>
							
							<?php $items_count = count( (array) $sourceResult[ 'items' ] ); ?>
							<?php //d( $items_count ); ?>
							
							<div
								class = "wpvr_source_noitems wpvr_source_items_closed wpvr_collapse_sections"
								zone_id = "source_<?php echo $sub_id; ?>"
								is_btn = "0"
							>


								<span class = "wpvr_source_noitems_btn">
									<i class = "fa fa-search-plus"></i>
								</span>
								<span class = "wpvr_source_noitems_count">
									<?php
										if( $items_count == 0 )
											echo __( 'No Item' , WPVR_LANG );
										elseif( $items_count == 1 )
											echo ' <strong> One </strong> ' . __( 'item' , WPVR_LANG );
										else
											echo '<strong>' . $items_count . '</strong> ' . __( 'items' , WPVR_LANG );
									?>
								</span>
							</div>
							
							<div class = "wpvr_source_items" id = "zone_<?php echo $sub_id; ?>">
								
								
								<?php if( $items_count == 0 ) { ?>
									
									<div class = "wpvr_source_noitems">
										<?php
											if( $sourceResult[ 'dupCount' ] != 0 ) {
												_e( 'No new videos were found for this source.' , WPVR_LANG );
											} else {
												_e( 'No videos were found for this source.' , WPVR_LANG );
											}
										?>
									</div>
								
								<?php } ?>
								<?php $video_k = 0; ?>
								<?php //d($sourceResult[ 'items' ]); ?>
								<?php foreach ( (array) $sourceResult[ 'items' ] as $k => $video ) { ?>
									
									<?php
									
									
									$vId = $sub_id . '_' . $video_k;
									
									$video_k ++;
									$video = (array) $video;
									if( ! isset( $video[ 'id' ] ) ) continue;
									
									$video[ 'postAppend' ] = $sourceResult[ 'source' ]->postAppend;
									if( $video[ 'postAppend' ] == 'customAfter' || $video[ 'postAppend' ] == 'customBefore' ) {
										$video[ 'postAppendName' ] = $sourceResult[ 'source' ]->appendCustomText;
									} elseif( $video[ 'postAppend' ] == 'after' || $video[ 'postAppend' ] == 'before' ) {
										$video[ 'postAppendName' ] = $video[ 'appendSourceName' ];
									} else {
										$video[ 'postAppendName' ] = '';
									}
									?>
									
									<?php if( isset( $tmp_results[ $vId ] ) ) {
										$preDuplicate = "preDuplicate";
									} else {
										$preDuplicate = "brandNew";
									} ?>
									
									<?php ?>
									<?php $tmp_results[ $video[ 'id' ] ] = $video; ?>
									
									<?php
									$video_views    = $video[ 'views' ];
									$video_duration = wpvr_get_duration_string( $video[ 'duration' ] );
									?>
									<div
										class = "wpvr_video pull-left <?php echo $video[ 'service' ]; ?>"
										id = "video_<?php echo $vId; ?>"
										video_id = "<?php echo $video[ 'id' ]; ?>"
									>
										<input type = "checkbox" class = "wpvr_video_cb" div_id = "<?php echo $vId; ?>" name = "<?php echo $video[ 'id' ]; ?>"/>
										
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
											<div class = "wpvr_video_unwanted">
												<i class = "fa fa-ban"></i>
											</div>
											
											<div
												class = "wpvr_service_icon <?php echo $video[ 'service' ]; ?> wpvr_video_service ">
												<?php echo $wpvr_vs[ $video[ 'service' ] ][ 'label' ]; ?>
											</div>
											<?php if( $video[ 'hqthumb' ] != FALSE ) { ?>
												<div class = "wpvr_service_hq">HQ</div>
											<?php } ?>
											<div class = "wpvr_video_views"
											     title = "<?php _e( 'Video Views' , WPVR_LANG ); ?>"
											     style = "text-align:left;"
											>
												<?php if( $video[ 'likes' ] != 0 ) { ?>
													<i class = "fa fa-heart"></i> <?php echo wpvr_numberK( $video[ 'likes' ] , FALSE ); ?>
													<br/>
												<?php } ?>
												<?php if( $video[ 'views' ] != 0 ) { ?>
													<i class = "fa fa-play-circle"></i> <?php echo wpvr_numberK( $video_views , FALSE ); ?>
												<?php } ?>
											</div>
											<div class = "wpvr_video_duration"
											     title = "<?php _e( 'Video Duration (seconds)' , WPVR_LANG ); ?>">
												<?php echo $video_duration; ?>
											</div>
											<div class = "wpvr_video_thumb <?php echo $video[ 'service' ]; ?>">
												<img class = "wpvr_video_thumb_img" src = "<?php echo $video[ 'icon' ]; ?>"/>
											</div>
										</div>
										<div class = "wpvr_video_title">
											<?php echo $video[ 'title' ]; ?>
											<?php //echo '<br/>'.$video['originalPostDate']; ?>
										</div>
									</div>
								<?php } ?>
								<div class = "wpvr_clearfix"></div>
							</div>
						</div>
					<?php } ?>
				</div>
				
				<div class = "wpvr_test_form_buttons bottom">
					<div class = "wpvr_button pull-left wpvr_test_form_toggleAll" state = "off">
						<i class = "wpvr_button_icon fa fa-check-square-o"></i>
						<?php _e( 'CHECK ALL VIDEOS' , WPVR_LANG ); ?>
					</div>
					<div class = "wpvr_button pull-left" id = "wpvr_test_form_refresh">
						<i class = "wpvr_button_icon fa fa-refresh"></i><?php _e( 'SEARCH AGAIN' , WPVR_LANG ); ?>
					</div>
					
					<div class = "wpvr_button  pull-left wpvr_goToTop">
						<i class = "wpvr_button_icon fa fa-arrow-up"></i>
						<?php echo __( 'To Top' , WPVR_LANG ); ?>
					</div>
					
					<?php if( WPVR_BATCH_ADDING_ENABLED === TRUE ) { ?>
						<div class = "wpvr_button pull-right wpvr_test_form_add" session = "<?php echo $session_token; ?>">
							<i class = "wpvr_button_icon fa fa-download"></i>
							<?php _e( 'BATCH ADD SELECTED' , WPVR_LANG ); ?>
						</div>
					<?php } ?>
					<div class = "wpvr_red_button wpvr_button pull-right wpvr_test_form_unwanted" id = "wpvr_test_form_unwanted_bottom"
					     session = "<?php echo $session_token; ?>">
						<i class = "wpvr_button_icon fa fa-ban"></i>
						<?php echo __( 'ADD' , WPVR_LANG ) . '<span class="wpvr_count_checked"> </span>' . __( 'TO UNWANTED' , WPVR_LANG ); ?>
					</div>
					<div class = "wpvr_button pull-right wpvr_green_button wpvr_test_form_add_each" id = "wpvr_test_form_add_each_bottom"
					     session = "<?php echo $session_token; ?>">
						<i class = "wpvr_button_icon fa fa-cloud-download"></i>
						<?php echo __( 'ADD' , WPVR_LANG ) . '<span class="wpvr_count_checked"> </span>' . __( 'ITEMS' , WPVR_LANG ); ?>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<div class = "wpvr_test_form_res"></div>
			</form>
			<?php
			if( ! isset( $_SESSION[ 'wpvr_tmp_results' ] ) ) {
				$_SESSION[ 'wpvr_tmp_results' ] = array();
			}
			$_SESSION[ 'wpvr_tmp_results' ][ $session_token ] = $tmp_results;
			
			
		}
	}
	
	/* DUPLICATE SOURCES */
	if( ! function_exists( 'wpvr_duplicate_source' ) ) {
		function wpvr_duplicate_source( $post_id , $singleDuplicate = FALSE ) {
			global $wpdb;
			$post            = get_post( $post_id );
			$current_user    = wp_get_current_user();
			$new_post_author = $current_user->ID;
			if( isset( $post ) && $post != null ) {
				$args        = array(
					'comment_status' => $post->comment_status ,
					'ping_status'    => $post->ping_status ,
					'post_author'    => $new_post_author ,
					'post_content'   => $post->post_content ,
					'post_excerpt'   => $post->post_excerpt ,
					'post_name'      => $post->post_name ,
					'post_parent'    => $post->post_parent ,
					'post_password'  => $post->post_password ,
					'post_status'    => 'publish' ,
					'post_title'     => $post->post_title ,
					'post_type'      => $post->post_type ,
					'to_ping'        => $post->to_ping ,
					'menu_order'     => $post->menu_order ,
				);
				$new_post_id = wp_insert_post( $args );
				$taxonomies  = get_object_taxonomies( $post->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
				foreach ( $taxonomies as $taxonomy ) {
					$post_terms = wp_get_object_terms( $post_id , $taxonomy , array( 'fields' => 'slugs' ) );
					wp_set_object_terms( $new_post_id , $post_terms , $taxonomy , FALSE );
				}
				$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
				if( count( $post_meta_infos ) != 0 ) {
					$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					foreach ( $post_meta_infos as $meta_info ) {
						$meta_key   = $meta_info->meta_key;
						$meta_value = addslashes( $meta_info->meta_value );
						if( $meta_key == 'wpvr_source_name' ) {
							$meta_value = $meta_value . ' (' . __( 'copy' , WPVR_LANG ) . ')';
						}
						
						$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
					}
					$sql_query .= implode( " UNION ALL " , $sql_query_sel );
					$wpdb->query( $sql_query );
				}
				
				update_post_meta( $new_post_id , 'wpvr_source_count_run' , 0 );
				update_post_meta( $new_post_id , 'wpvr_source_count_test' , 0 );
				update_post_meta( $new_post_id , 'wpvr_source_count_imported' , 0 );
				if( $singleDuplicate ) {
					wp_redirect( admin_url( 'edit.php?post_type=' . WPVR_SOURCE_TYPE ) );
				} else {
					return $new_post_id;
				}
				
			} else {
				wp_die( 'Post creation failed, could not find original post: ' . $post_id );
			}
		}
	}
	
	/* Get Sources Stats */
	if( ! function_exists( 'wpvr_sources_stats' ) ) {
		function wpvr_sources_stats( $group = FALSE ) {
			global $wpvr_options , $wpdb , $wpvr_vs , $wpvr_vs_ids;
			
			$source_stats_adapted = wpvr_prepare_source_stats_sql();
			
			//echo nl2br( $source_stats_adapted );
			
			$qMeta
				= "
			SELECT
				COUNT( distinct P.ID) as total,
				COUNT( distinct if(
					P.post_status = 'trash' , P.ID , NULL
				)) as trash,
				COUNT( distinct if(
					P.post_status = 'pending' , P.ID , NULL
				)) as pending,
				COUNT( distinct if(
					P.post_status = 'publish' , P.ID , NULL
				)) as publish,
				COUNT( distinct if(
					P.post_status = 'draft' , P.ID , NULL
				)) as draft,
				COUNT( distinct if(
					M.meta_key = 'wpvr_source_status' AND M.meta_value = 'on' , P.ID , NULL
				)) as active,
				COUNT( distinct if(
					M.meta_key = 'wpvr_source_status' AND M.meta_value = 'off' , P.ID , NULL
				)) as inactive,
				" . $source_stats_adapted . "
				
				1 as end

			FROM
				$wpdb->posts P
				INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
			WHERE
				1
				AND P.post_type = '" . WPVR_SOURCE_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','draft')
		";
			
			//echo nl2br( $source_stats_adapted );
			
			$rMeta = $wpdb->get_results( $qMeta , OBJECT );
			//echo $wpdb->last_error;d($rMeta);
			
			if( ! isset( $rMeta[ 0 ] ) ) {
				return FALSE;
			} else {
				if( $group === TRUE && count( $rMeta ) != 0 ) {
					$groupCount = array(
						'total'     => 0 ,
						'byStatus'  => array() ,
						'byState'   => array() ,
						'byType'    => array() ,
						'byService' => array() ,
					);
					
					$wpvr_vs_ids[ 'gids' ][] = 'groupType';
					
					foreach ( $rMeta[ 0 ] as $label => $count ) {
						if( $label == 'total' ) {
							$groupCount[ 'total' ] = $count;
						} elseif( in_array( $label , array( 'trash' , 'pending' , 'publish' , 'draft' ) ) ) {
							$groupCount[ 'byStatus' ][ $label ] = $count;
						} elseif( in_array( $label , array( 'active' , 'inactive' ) ) ) {
							$groupCount[ 'byState' ][ $label ] = $count;
							//}elseif(in_array($label , array('playlist','videos','search','channel','trends','groupType') )){
						} elseif( in_array( $label , $wpvr_vs_ids[ 'gids' ] ) ) {
							$groupCount[ 'byType' ][ $label ] = $count;
						} elseif( in_array( $label , $wpvr_vs_ids[ 'ids' ] ) ) {
							$groupCount[ 'byService' ][ $label ] = $count;
						}
						
						
					}
					
					return $groupCount;
				} else {
					return (array) $rMeta[ 0 ];
				}
			}
		}
	}
	
	/* Get Playlis Data from Channel Id */
	if( ! function_exists( 'wpvr_parse_string' ) ) {
		function wpvr_parse_string( $string ) {
			$items     = explode( ',' , $string );
			$new_items = array();
			if( count( $items ) == 0 ) {
				return array();
			} else {
				foreach ( $items as $item ) {
					if( $item != '' ) $new_items[] = $item;
				}
			}
			
			return $new_items;
		}
	}
	
	
	if( ! function_exists( 'wpvr_render_vs_source_type' ) ) {
		function wpvr_render_vs_source_type( $vs_type , $vs , $class = '' ) {
			return '
				<span class="wpvr_source_icon ' . $vs_type[ 'id' ] . ' ' . $class . '" service = "' . $vs[ 'id' ] . '" >
					' . $vs_type[ 'label' ] . '
				</span>
				<span class=" ' . $class . ' wpvr_source_icon_right ' . $vs_type[ 'id' ] . ' ' . $vs[ 'id' ] . ' " service="' . $vs[ 'id' ] . '" >
					<i class="fa wpvr_source_type_icon fa ' . $vs_type[ 'icon' ] . '"></i>
				</span>
			';
		}
	}
	/* FETCH SOURCES */
	
	if( ! function_exists( 'wpvr_async_fetch_sources' ) ) {
		function wpvr_async_fetch_sources( $sources , $update_old_videos = FALSE , $debug = FALSE ) {
			
			global $wpvr_deferred_ids , $wpvr_unwanted_ids , $preDuplicates , $wpvr_vs;
			$groupReturn = FALSE;
			$sources     = wpvr_multiplicate_sources( $sources );
			//d( $sources );
			//if( $update_old_videos ) $wpvr_imported = wpvr_update_imported_videos();
			//else $wpvr_imported = get_option( 'wpvr_imported' );
			
			$preDuplicates = array();
			
			//wpvr_reset_debug();
			$RCX         = new RollingCurlX( count( $sources ) );
			$token       = bin2hex( openssl_random_pseudo_bytes( 5 ) );
			$tmp_sources = 'wpvr_tmp_sources_' . $token;
			$tmp_done    = 'wpvr_tmp_done_' . $token;
			//$tmp_debug   = 'wpvr_async_debug_' . $token;
			$tmp_res = 'wpvr_tmp_res_' . $token;
			//$async_dups_name = 'wpvr_async_dups_' . $token;
			$timer = wpvr_chrono_time();
			
			update_option( $tmp_done , array(
				'exec_time' => null ,
				'count'     => array(
					'exec_time'     => 0 ,
					'count'         => 0 ,
					'absCount'      => 0 ,
					'dupCount'      => 0 ,
					'unwantedCount' => 0 ,
					'totalResults'  => 0 ,
					'count_sources' => 0 ,
					'count_total'   => count( $sources ) ,
				) ,
				'data'      => array() ,
				'raw'       => array() ,
				'videos'    => array() ,
			) );
			
			update_option( $tmp_res , array() );
			//update_option( $tmp_debug , '' );
			
			$sources_by_id = array();
			
			foreach ( (array) $sources as $source ) {
				if( ! isset( $source->sub_id ) ) $source->sub_id = $source->id;
				
				$sources_by_id[ $source->sub_id ] = $source;

				$actions_url = WPVR_ACTIONS_URL_ASYNC_FIX ? WPVR_ACTIONS_URL_ASYNC : WPVR_ACTIONS_URL;
				$url         = wpvr_capi_build_query( $actions_url , array(
					'wpvr_wpload'         => 1 ,
					'fetch_single_source' => 1 ,
					'source_id'           => $source->sub_id ,
					'token'               => $token ,
				) );
				//d( $source->sub_id );
				$RCX->addRequest(
					$url ,
					null ,
					'wpvr_async_fetch_sources_callback' ,
					array(
						'token'     => $token ,
						'source_id' => $source->sub_id ,
					) ,
					array(
						CURLOPT_FOLLOWLOCATION => FALSE ,
					)
				);
			}
			
			update_option( $tmp_sources , $sources_by_id );
			$RCX->execute();
			$done                = get_option( $tmp_done );
			$done[ 'exec_time' ] = wpvr_chrono_time( $timer );
			
			if( WPVR_ENABLE_ASYNC_DEBUG ) {
				$async_debug = $done;
				d( $async_debug );
			}
			
			//d( $done['raw'] );
			
			foreach ( (array) $done[ 'raw' ] as $raw_id => $raw ) {
				echo $raw[ 'echos' ];
			}
			
			//  d( $done );
			
			
			delete_option( $tmp_done );
			delete_option( $tmp_sources );
			delete_option( $tmp_res );
			
			return $done;
		}
	}
	
	if( ! function_exists( 'wpvr_async_run_sources' ) ) {
		function wpvr_async_run_sources( $sources , $is_autorun , $echo = FALSE ) {
			
			global $wpvr_imported , $wpvr_deferred , $wpvr_options;
			global $wpvr_deferred_ids , $wpvr_unwanted_ids;
			
			$run_report = array(
				'exec_time' => array(
					'fetching' => 0 ,
					'adding'   => 0 ,
				) ,
				'fetching'  => array(
					'exec_time'         => 0 ,
					'count_duplicates'  => 0 ,
					'count_unwanted'    => 0 ,
					'count_parsed'      => 0 ,
					'count_total'       => 0 ,
					'count_videos'      => 0 ,
					'count_sources'     => 0 ,
					'count_run_sources' => 0 ,
				) ,
				'adding'    => array(
					'exec_time' => 0 ,
					'found'     => 0 ,
					'added'     => 0 ,
					'fetched'   => 0 ,
					'errors'    => 0 ,
					'deferred'  => 0 ,
					'skipped'   => 0 ,
					'unwanted'  => 0 ,
					'sources'   => 0 ,
				) ,
			);
			
			$timer = wpvr_chrono_time();
			
			//wpvr_reset_debug();
			$sources     = wpvr_multiplicate_sources( $sources );
			$RCX         = new RollingCurlX( count( $sources ) );
			$token       = bin2hex( openssl_random_pseudo_bytes( 5 ) );
			$tmp_sources = 'wpvr_tmp_sources_' . $token;
			$tmp_done    = 'wpvr_tmp_done_' . $token;
			
			$run_report[ 'adding' ][ 'sources' ] = count( $sources );
			
			update_option( $tmp_done , array(
				'exec_time' => null ,
				'count'     => array(
					'count_duplicates' => 0 ,
					'count_unwanted'   => 0 ,
					'count_parsed'     => 0 ,
					'count_total'      => 0 ,
					'count_videos'     => 0 ,
					'count_sources'    => count( $sources ) ,
				) ,
				'data'      => array() ,
				'raw'       => array() ,
				'videos'    => array() ,
				'counts'    => array() ,
				'errors'    => array() ,
			) );
			
			$sources_by_id = array();
			foreach ( (array) $sources as $source ) {
				
				if( ! isset( $source->sub_id ) ) $source->sub_id = $source->id;
				
				$sources_by_id[ $source->sub_id ] = $source;

				$actions_url = WPVR_ACTIONS_URL_ASYNC_FIX ? WPVR_ACTIONS_URL_ASYNC : WPVR_ACTIONS_URL;
				$url         = wpvr_capi_build_query( $actions_url , array(
					'wpvr_wpload'              => 1 ,
					'run_single_source_before' => 1 ,
					'source_id'                => $source->sub_id ,
					'token'                    => $token ,
					'is_autorun'               => $is_autorun ? 1 : 0 ,
				) );

				$RCX->addRequest(
					$url ,
					null ,
					'wpvr_async_run_sources_callback' ,
					array(
						'token'      => $token ,
						'source_id'  => $source->id ,
						'is_autorun' => $is_autorun ? 1 : 0 ,
					) ,
					array(
						CURLOPT_FOLLOWLOCATION => FALSE ,
					)
				);
				
			}
			
			update_option( $tmp_sources , $sources_by_id );
			$RCX->execute();
			$done = get_option( $tmp_done );
			
			//$done[ 'exec_time' ] = wpvr_chrono_time( $timer );
			$run_report[ 'fetching' ]                        = $done[ 'count' ];
			$run_report[ 'fetching' ][ 'count_run_sources' ] = 0;
			
			$videos_fetched = array();
			$i              = 0;
			
			foreach ( (array) $done[ 'videos' ] as $subid => $videos ) {
				
				if( $videos === FALSE ) {
					echo $done[ 'errors' ][ $subid ];
				} else {
					$run_report[ 'fetching' ][ 'count_run_sources' ] ++;
					
					foreach ( (array) $videos as $id => $video ) {
						$i ++;
						$video = (array) $video;
						//d( $video['id']);
						if( ! isset( $video[ 'id' ] ) ) continue;
						$service  = $video[ 'service' ];
						$video_id = $video[ 'id' ];
						//d(  $wpvr_options[ 'deferAdding' ] );

						$is_defer = isset( $wpvr_deferred_ids[ $service ][ $video_id ] );
						$is_dup   = isset( $wpvr_imported[ $service ][ $video_id ] );

						//echo "$video_id : - ".($is_defer ? 'IS DEFER' : 'NOT DEFER')." - ".($is_dup ? 'IS DUPLICATE' : 'NOT DUPLICATE') ;
						//echo "<hr/>";

						if( isset( $wpvr_deferred_ids[ $service ][ $video_id ] ) ) {
							//Video is already on deferred list
							//$done[ $id ] = 'already_deferred';
							$run_report[ 'adding' ][ 'skipped' ] ++;
							
						} elseif( isset( $wpvr_unwanted_ids[ $service ][ $video_id ] ) ) {
							//Video is already on deferred list
							//$done[ $id ] = 'unwanted';
							$run_report[ 'adding' ][ 'unwanted' ] ++;
							
							
						} elseif( $wpvr_options[ 'deferAdding' ] == 'on' && $i > $wpvr_options[ 'deferBuffer' ] ) {
							//defer video
							//$done[ $id ] = 'deferred';
							//echo "<br/> i=$i - buffer=".$wpvr_options[ 'deferBuffer' ];
							
							$run_report[ 'adding' ][ 'deferred' ] ++;
							
							if( ! isset( $wpvr_deferred_ids[ $service ] ) ) {
								$wpvr_deferred_ids[ $service ] = array();
							}
							if( ! isset( $wpvr_deferred_ids[ $service ][ $video_id ] ) ) {
								$wpvr_deferred[]                            = $video;
								$wpvr_deferred_ids[ $service ][ $video_id ] = 'deferred';

								wpvr_add_log( array(
									"time"     => date( 'Y-m-d H:i:s' ) ,
									"action"   => __( 'DEFER A VIDEO ADDING' , WPVR_LANG ) . ' (' . $video[ 'origin' ] . ')' ,
									"type"     => 'defer' ,
									"icon"     => $video[ 'icon' ] ,
									"object"   => $video[ 'id' ] ,
									"log_msgs" => array(
										__( 'DEFERRED VIDEO' , WPVR_LANG ) ,
										__( 'TITLE' , WPVR_LANG ) . ' : ' . $video[ 'title' ] ,
									) ,
								) );
							}
						} else {
							$videos_fetched[ $video_id ] = $video;
						}
					}
				}
			}
			
			$run_report[ 'adding' ][ 'found' ] = $i;
			
			update_option( 'wpvr_deferred' , $wpvr_deferred );
			update_option( 'wpvr_deferred_ids' , $wpvr_deferred_ids );
			update_option( 'wpvr_unwanted_ids' , $wpvr_unwanted_ids );
			
			$run_report[ 'adding' ][ 'fetched' ]     = count( $videos_fetched );
			$run_report[ 'fetching' ][ 'exec_time' ] = wpvr_chrono_time( $timer );
			
			
			$timer = wpvr_chrono_time();
			$added = wpvr_async_add_videos( $videos_fetched , WPVR_ASYNC_ADDING_BUFFER );
			//$added = null;
			//d( $added );
			//$run_report[ 'exec_time' ][ 'adding' ] = wpvr_chrono_time( $timer );
			$run_report[ 'adding' ][ 'exec_time' ] = wpvr_chrono_time( $timer );
			$run_report[ 'adding' ][ 'added' ]     = $added[ 'count_done' ];
			$run_report[ 'adding' ][ 'errors' ]    = $added[ 'count_error' ];
			
			
			$parsed = $run_report[ 'fetching' ][ 'count_videos' ] +
			          $run_report[ 'fetching' ][ 'count_duplicates' ] +
			          $run_report[ 'fetching' ][ 'count_unwanted' ];
			
			$fetching_insights = array(
				
				array(
					'title' => __( 'Absolute Count' , WPVR_LANG ) ,
					'icon'  => 'fa-thumb-tack' ,
					'value' => wpvr_numberK( $run_report[ 'fetching' ][ 'count_sources' ] ) .
					           ' ' . __( 'Sources' , WPVR_LANG ) ,
				) ,
				
				array(
					'title' => __( 'Results Count' , WPVR_LANG ) ,
					'icon'  => 'fa-check' ,
					'value' => wpvr_numberK( $run_report[ 'fetching' ][ 'count_videos' ] ) .
					           ' ' . __( 'Returned' , WPVR_LANG ) ,
				) ,
				
				array(
					'title' => __( 'Absolute Count' , WPVR_LANG ) ,
					'icon'  => 'fa-search' ,
					'value' => wpvr_numberK( $parsed ) .
					           ' ' . __( 'Parsed' , WPVR_LANG ) ,
				) ,
				
				array(
					'title' => __( 'Duplicate Count' , WPVR_LANG ) ,
					'icon'  => 'fa-copy' ,
					'value' => wpvr_numberK( $run_report[ 'fetching' ][ 'count_duplicates' ] ) .
					           ' ' . __( 'Duplicates' , WPVR_LANG ) ,
				) ,
				
				array(
					'title' => __( 'Unwanted Count' , WPVR_LANG ) ,
					'icon'  => 'fa-ban' ,
					'value' => wpvr_numberK( $run_report[ 'fetching' ][ 'count_unwanted' ] ) .
					           ' ' . __( 'Unwanted' , WPVR_LANG ) ,
				) ,
				
				array(
					'title' => __( 'Total Found' , WPVR_LANG ) ,
					'icon'  => 'fa-sort-amount-desc' ,
					'value' => wpvr_numberK( $run_report[ 'fetching' ][ 'count_total' ] ) .
					           ' ' . __( 'Found' , WPVR_LANG ) ,
				) ,
				
				array(
					'title' => __( 'Execution Time' , WPVR_LANG ) ,
					'icon'  => 'fa-clock-o' ,
					'value' => round( $run_report[ 'fetching' ][ 'exec_time' ] , 3 ) .
					           ' ' . __( 'seconds' , WPVR_LANG ) ,
				) ,
			
			);
			
			$adding_insights = array(
				
				array(
					'title' => __( 'Added Count' , WPVR_LANG ) ,
					'icon'  => 'fa-download' ,
					'value' => wpvr_numberK( $run_report[ 'adding' ][ 'added' ] ) .
					           ' ' . __( 'Added videos' , WPVR_LANG ) ,
				) ,
				
				array(
					'title' => __( 'Deferred Count' , WPVR_LANG ) ,
					'icon'  => 'fa-mail-forward' ,
					'value' => wpvr_numberK( $run_report[ 'adding' ][ 'deferred' ] ) .
					           ' ' . __( 'Deferred videos' , WPVR_LANG ) ,
				) ,
				
				
				array(
					'title' => __( 'Errors Count' , WPVR_LANG ) ,
					'icon'  => 'fa-exclamation-circle' ,
					'value' => wpvr_numberK( $run_report[ 'adding' ][ 'errors' ] ) .
					           ' ' . __( 'Errors' , WPVR_LANG ) ,
				) ,
				
				array(
					'title' => __( 'Execution Time' , WPVR_LANG ) ,
					'icon'  => 'fa-clock-o' ,
					'value' => round( $run_report[ 'adding' ][ 'exec_time' ] , 3 ) .
					           ' ' . __( 'seconds' , WPVR_LANG ) ,
				) ,
			
			);
			
			if( $echo != FALSE ) {
				?>
				<div class = "wrap wpvr_wrap">
					<h2 class = "wpvr_title">
						<?php wpvr_show_logo(); ?>
						<i class = "wpvr_title_icon fa fa-bolt	"></i>
						<?php echo __( 'Running Sources' , WPVR_LANG ); ?>
						<div class = "wpvr_clearfix"></div>
					</h2>
					
					<?php
						if( WPVR_ENABLE_ASYNC_DEBUG ) d( $done );
					?>
					<div class = "wpvr_source_insights">
						<h3 class = "wpvr_source_insights_title">
							<?php _e( 'Fecthing Insights' , WPVR_LANG ); ?>
						</h3>
						<?php render_source_insights( $fetching_insights ); ?>
						
						<h3 class = "wpvr_source_insights_title">
							<?php _e( 'Adding Insights' , WPVR_LANG ); ?>
						</h3>
						<?php render_source_insights( $adding_insights , 'black' ); ?>
					
					</div>
					
					
					<br/><br/>
					<a href = "#" id = "backBtn">
						<?php echo __( 'Go Back' , WPVR_LANG ); ?>
					</a>
				
				</div>
				<?php
				
				
				//d( $run_report );
				
				
			}
			
			delete_option( $tmp_done );
			delete_option( $tmp_sources );

			//d( $tmp_done );
			//d( get_option($tmp_done) );

			
			return $run_report;
			
		}
	}
	
	if( ! function_exists( 'wpvr_async_fetch_sources_callback' ) ) {
		function wpvr_async_fetch_sources_callback( $response , $url , $request_info , $user_data , $time ) {
			
			global $async_debug;
			
			$token     = $user_data[ 'token' ];
			$source_id = $user_data[ 'source_id' ];
			
			$response_arr = explode( WPVR_JS , $response );
			if( ! isset( $response_arr[ 1 ] ) ) {
				$json  = FALSE;
				$echos = $response_arr[ 0 ];
			} elseif( isset( $response_arr[ 2 ] ) ) {
				$json  = (array) json_decode( $response_arr[ 1 ] );
				$echos = $response_arr[ 0 ] . ' ' . $response_arr[ 2 ];
			} else {
				$json  = (array) json_decode( $response_arr[ 1 ] );
				$echos = $response_arr[ 0 ];
			}
			
			//wpvr_set_debug( array(
			//	'response' => $response ,
			//	'arr' => $response_arr ,
			//	'echos' => $echos ,
			//	'url' => $url,
			//) );
			
			//$json     = (array) json_decode( $response );
			$tmp_done = 'wpvr_tmp_done_' . $token;
			//$tmp_debug = 'wpvr_async_debug_' . $token;
			$done = get_option( $tmp_done );
			//$async_dups_name = 'wpvr_async_dups_' . $token;
			//$async_dups      = get_option( $async_dups_name );
			
			//echo $response ;
			$json[ 'echos' ]     = $echos;
			$json[ 'exec_time' ] = $time / 1000;
			
			if( isset( $json[ 'done' ] ) ) {
				//d( $done );
				//d( $json );
				
				//$done[ 'exec_time' ] += $json[ 'exec_time' ];
				$done[ 'count' ][ 'count' ]         = $json[ 'count' ] + $done[ 'count' ][ 'count' ];
				$done[ 'count' ][ 'absCount' ]      = $json[ 'absCount' ] + $done[ 'count' ][ 'absCount' ];
				$done[ 'count' ][ 'dupCount' ]      = $json[ 'dupCount' ] + $done[ 'count' ][ 'dupCount' ];
				$done[ 'count' ][ 'unwantedCount' ] = $json[ 'unwantedCount' ] + $done[ 'count' ][ 'unwantedCount' ];
				$done[ 'count' ][ 'totalResults' ]  = $json[ 'totalResults' ] + $done[ 'count' ][ 'totalResults' ];
				$done[ 'count' ][ 'exec_time' ]     = $json[ 'exec_time' ] + $done[ 'count' ][ 'exec_time' ];
				
				$done[ 'data' ][ $source_id ] = $json[ 'source_info' ];
				$done[ 'items' ][]            = $json;
				
			}
			
			$done[ 'raw' ][ $source_id ] = array(
				'time'         => $time / 1000 ,
				'request_info' => $request_info ,
				'response'     => $response ,
				'json'         => $json ,
				'debug'        => get_option( 'async_debug' ) ,
				'echos'        => $echos ,
			);
			
			update_option( $tmp_done , $done );
			//update_option( $async_dups_name , $async_dups );
		}
	}
	
	if( ! function_exists( 'wpvr_async_run_sources_callback' ) ) {
		function wpvr_async_run_sources_callback( $response , $url , $request_info , $user_data , $time ) {
			//echo( $response );
			
			//return FALSE;
			
			//d( $user_data );
			$token        = $user_data[ 'token' ];
			$source_id    = $user_data[ 'source_id' ];
			$json_notices = '';
			$json_arr     = explode( WPVR_JS , $response );
			if( ! isset( $json_arr[ 1 ] ) ) {
				$json = FALSE;
				$json_notices .= $json_arr[ 0 ];
			} else {
				$json_notices .= $json_arr[ 0 ];
				$json = (array) json_decode( $json_arr[ 1 ] );
				if( isset( $json_arr[ 2 ] ) ) $json_notices .= $json_arr[ 2 ];
			}
			
			$tmp_done = 'wpvr_tmp_done_' . $token;
			$tmp_res  = 'wpvr_tmp_res_' . $token;
			$done     = get_option( $tmp_done );
			//echo $response ;
			$json[ 'exec_time' ] = $time / 1000;
			
			
			if( $json != FALSE && isset( $json[ 'data' ] ) && $json[ 'data' ] != FALSE ) {
				$done[ 'videos' ][ $json[ 'sub_id' ] ] = $json[ 'data' ]->videos;
				
				$done[ 'count' ][ 'count_duplicates' ] += $json[ 'data' ]->counts->dupCount;
				$done[ 'count' ][ 'count_unwanted' ] += $json[ 'data' ]->counts->unwantedCount;
				$done[ 'count' ][ 'count_parsed' ] += $json[ 'data' ]->counts->absCount;
				$done[ 'count' ][ 'count_total' ] += $json[ 'data' ]->counts->totalResults;
				$done[ 'count' ][ 'count_videos' ] += $json[ 'data' ]->counts->count;
				
				$json[ 'data' ]->counts->exec_time = $time / 1000;
				
				$done[ 'counts' ][ $json[ 'sub_id' ] ] = $json[ 'data' ]->counts;
				$done[ 'errors' ][ $source_id ]        = $json_notices;
				
			} else {
				$done[ 'errors' ][ $source_id ] = $json_notices;
				$done[ 'videos' ][ $source_id ] = FALSE;
				$done[ 'counts' ][ $source_id ] = FALSE;
			}
			$done[ 'raw' ][ $source_id ] = array(
				'time'         => $time / 1000 ,
				'request_info' => $request_info ,
				'response'     => $response ,
				'json'         => $json ,
			);
			
			update_option( $tmp_done , $done );
		}
	}