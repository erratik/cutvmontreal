<?php
	
	if( ! function_exists( 'wpvr_retreive_video_id_from_param' ) ) {
		function wpvr_retreive_video_id_from_param( $param , $service ) {
			if( $service == 'youtube' ) {
				////////////// YOUTUBE //////////////
				//https://youtu.be/uIi0xm_tlCU
				if( strpos( $param , 'youtu.be' ) !== FALSE ) {
					$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://youtu.be/' : 'http://youtu.be/';
					$x         = explode( $separator , $param );
					if( ! isset( $x[ 1 ] ) ) {
						return FALSE;
					} else {
						return $x[ 1 ];
					}
					
				} elseif( strpos( $param , 'youtube.com' ) === FALSE ) {
					return $param;
				} else {
					parse_str( parse_url( $param , PHP_URL_QUERY ) , $args );
					if( isset( $args[ 'v' ] ) ) {
						return $args[ 'v' ];
					} else {
						return FALSE;
					}
				}
			} elseif( $service == 'vimeo' ) {
				////////////// VIMEO //////////////
				if( strpos( $param , 'vimeo.com' ) === FALSE ) {
					return $param;
				} else {
					if( strpos( $param , 'www.vimeo' ) === FALSE ) {
						$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://vimeo.com/' : 'http://vimeo.com/';
					} else {
						$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://www.vimeo.com/' : 'http://www.vimeo.com/';
					}
					$x = explode( $separator , $param );
					if( ! isset( $x[ 1 ] ) ) {
						return FALSE;
					} else {
						$y = explode( '/' , $x[ 1 ] );
						
						return $y[ 0 ];
					}
				}
			} elseif( $service == 'facebook' ) {
				////////////// VIMEO //////////////
				if( strpos( $param , 'facebook.com' ) === FALSE ) {
					return $param;
				} else {
					$separator = '/videos/';
					$x         = explode( $separator , $param );
					if( ! isset( $x[ 1 ] ) ) {
						return FALSE;
					} else {
						$y = explode( '/' , $x[ 1 ] );
						
						return $y[ 0 ];
					}
				}
			} elseif( $service == 'dailymotion' ) {
				
				////////////// DAILYMOTION //////////////
				//http://dai.ly/x346uwt
				if( strpos( $param , 'dai.ly' ) !== FALSE ) {
					$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://dai.ly/' : 'http://dai.ly/';
					$x         = explode( $separator , $param );
					if( ! isset( $x[ 1 ] ) ) {
						return FALSE;
					} else {
						return $x[ 1 ];
					}
				} elseif( strpos( $param , 'dailymotion.com' ) === FALSE ) {
					return $param;
				} else {
					
					if( strpos( $param , 'www.dailymotion' ) !== FALSE ) {
						$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://www.dailymotion.com/video/' : 'http://www.dailymotion.com/video/';
					} else {
						$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://dailymotion.com/video/' : 'http://dailymotion.com/video/';
					}
					$x = explode( $separator , $param );
					if( ! isset( $x[ 1 ] ) ) {
						return FALSE;
					} else {
						$y = explode( '_' , $x[ 1 ] );
						
						return $y[ 0 ];
					}
				}
				
			} elseif( $service == 'ted' ) {
				
				////////////// TED //////////////
				if( strpos( $param , 'ted.com' ) === FALSE ) {
					return $param;
				} else {
					if( strpos( $param , 'www.ted.com' ) !== FALSE ) {
						$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://www.ted.com/talks/' : 'http://www.ted.com/talks/';
					} else {
						$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://ted.com/talks/' : 'http://ted.com/talks/';
					}
					$x = explode( $separator , $param );
					if( ! isset( $x[ 1 ] ) ) {
						return FALSE;
					} else {
						$y = explode( '/' , $x[ 1 ] );
						
						return $y[ 0 ];
					}
				}
				
			} elseif( $service == 'youku' ) {
				
				////////////// YOUKU //////////////
				if( strpos( $param , 'youku.com' ) === FALSE ) {
					return $param;
				} else {
					$separator = ( strpos( $param , 'https://' ) !== FALSE ) ? 'https://v.youku.com/v_show/id_' : 'http://v.youku.com/v_show/id_';
					$x         = explode( $separator , $param );
					if( ! isset( $x[ 1 ] ) ) {
						return FALSE;
					} else {
						$y = explode( '.' , $x[ 1 ] );
						
						return $y[ 0 ];
					}
				}
				
			} else {
				return $param;
			}
		}
	}
	
	if( ! function_exists( 'wpvr_get_system_info' ) ) {
		function wpvr_get_system_info() {
			$php_version = explode( '+' , PHP_VERSION );
			$infos       = array(
				'server'             => array(
					'label'  => __( 'Server Software' , WPVR_LANG ) ,
					'value'  => '<br/>' . $_SERVER[ 'SERVER_SOFTWARE' ] ,
					'status' => '' ,
				) ,
				'php_version'        => array(
					'label'  => __( 'PHP Version' , WPVR_LANG ) ,
					'value'  => $php_version[ 0 ] ,
					'status' => version_compare( PHP_VERSION , WPVR_REQUIRED_PHP_VERSION , '>=' ) ? 'good' : 'bad' ,
				) ,
				'memory_limit'       => array(
					'label'  => __( 'PHP Memory Limit' , WPVR_LANG ) ,
					'value'  => ini_get( 'memory_limit' ) ,
					'status' => '' ,
				) ,
				'post_max_size'      => array(
					'label'  => __( 'Post Max Size' , WPVR_LANG ) ,
					'value'  => ini_get( 'post_max_size' ) ,
					'status' => '' ,
				) ,
				'max_input_time '    => array(
					'label'  => __( 'Maximum Input Time' , WPVR_LANG ) ,
					'value'  => ini_get( 'max_input_time' ) ,
					'status' => '' ,
				) ,
				'max_execution_time' => array(
					'label'  => __( 'Maximum Execution Time' , WPVR_LANG ) ,
					'value'  => ini_get( 'max_execution_time' ) ,
					'status' => '' ,
				) ,
				'safe_mode'          => array(
					'label'  => __( 'PHP Safe Mode' , WPVR_LANG ) ,
					'value'  => ini_get( 'safe_mode' ) ? 'ON' : 'OFF' ,
					'status' => ini_get( 'safe_mode' ) ? 'bad' : 'good' ,
				) ,
				'cURL_status'        => array(
					'label'  => __( 'cURL Status' , WPVR_LANG ) ,
					'value'  => function_exists( 'curl_version' ) ? 'ON' : 'OFF' ,
					'status' => function_exists( 'curl_version' ) ? 'good' : 'bad' ,
				) ,
				'allow_url_fopen'    => array(
					'label'  => __( 'Allow URL Fopen' , WPVR_LANG ) ,
					'value'  => ini_get( 'allow_url_fopen' ) == '1' ? 'ON' : 'OFF' ,
					'status' => ini_get( 'allow_url_fopen' ) == '1' ? 'good' : 'bad' ,
				) ,
				'openssl_status'     => array(
					'label'  => __( 'OpenSSL Extension' , WPVR_LANG ) ,
					'value'  => extension_loaded( 'openssl' ) ? 'ON' : 'OFF' ,
					'status' => extension_loaded( 'openssl' ) ? 'good' : 'bad' ,
				) ,
				'folder_writable'    => array(
					'label'  => __( 'Plugin Folder Writable' , WPVR_LANG ) ,
					'value'  => ( is_writable( WPVR_PATH ) === TRUE ) ? 'ON' : 'OFF' ,
					'status' => ( is_writable( WPVR_PATH ) === TRUE ) ? 'good' : 'bad' ,
				) ,
			
			);
			
			$act  = wpvr_get_act_data( 'wpvr' );
			$wpvr = array(
				
				'wpvr_url' => array(
					'label'  => __( 'Website URL' , WPVR_LANG ) ,
					'value'  => WPVR_SITE_URL ,
					'status' => '' ,
				) ,
				
				'wpvr_version' => array(
					'label'  => __( 'WPVR Version' , WPVR_LANG ) ,
					'value'  => WPVR_VERSION ,
					'status' => '' ,
				) ,
				
				'wpvr_act_status' => array(
					'label'  => __( 'WPVR Activation Status' , WPVR_LANG ) ,
					'value'  => $act[ 'act_status' ] ,
					'status' => '' ,
				) ,
				
				'wpvr_act_code' => array(
					'label'  => __( 'WPVR Activation Code' , WPVR_LANG ) ,
					'value'  => $act[ 'act_code' ] ,
					'status' => '' ,
				) ,
				
				'wpvr_act_date' => array(
					'label'  => __( 'WPVR Activation Date' , WPVR_LANG ) ,
					'value'  => $act[ 'act_date' ] ,
					'status' => '' ,
				) ,
				
				'wpvr_act_id' => array(
					'label'  => __( 'WPVR Activation ID' , WPVR_LANG ) ,
					'value'  => $act[ 'act_id' ] ,
					'status' => '' ,
				) ,
			
			);
			
			return array(
				'sys'  => $infos ,
				'wpvr' => $wpvr ,
			);
			
		}
	}
	
	if( ! function_exists( 'wpvr_render_system_info' ) ) {
		function wpvr_render_system_info( $info_blocks ) {
			$html = " WP Video Robot : SYSTEM INFORMATION \n\r";
			foreach ( (array) $info_blocks as $infos ) {
				$html .= "----------------------------------------------------------------- \n\r";
				foreach ( (array) $infos as $info ) {
					
					if( is_bool( $info[ 'value' ] ) && $info[ 'value' ] === TRUE ) {
						$info[ 'value' ] = "TRUE";
					} elseif( is_bool( $info[ 'value' ] ) && $info[ 'value' ] === TRUE ) {
						$info[ 'value' ] = "FALSE";
					}
					$html .= " - " . $info[ 'label' ] . " : " . $info[ 'value' ] . " \n\r";
				}
				$html .= "----------------------------------------------------------------- \n\r";
			}
			
			return $html;
		}
	}
	
	if( ! function_exists( 'wpvr_get_service_labels' ) ) {
		function wpvr_get_service_labels( $data ) {
			global $wpvr_vs;
			if(
				! isset( $data[ 'sourceService' ] )
				|| ! isset( $wpvr_vs[ $data[ 'sourceService' ] ] )
				|| ! isset( $wpvr_vs[ $data[ 'sourceService' ] ][ 'types' ][ $data[ 'sourceType' ] ] )
			) {
				return array(
					'service'       => '' ,
					'service_label' => '' ,
					'type'          => '' ,
					'type_label'    => '' ,
					'type_HTML'     => '' ,
				);
			}
			
			return array(
				'service'       => $wpvr_vs[ $data[ 'sourceService' ] ][ 'id' ] ,
				'service_label' => $wpvr_vs[ $data[ 'sourceService' ] ][ 'label' ] ,
				'type'          => $wpvr_vs[ $data[ 'sourceService' ] ][ 'types' ][ $data[ 'sourceType' ] ][ 'id' ] ,
				'type_label'    => $wpvr_vs[ $data[ 'sourceService' ] ][ 'types' ][ $data[ 'sourceType' ] ][ 'label' ] ,
				'type_HTML'     => wpvr_render_vs_source_type(
					$wpvr_vs[ $data[ 'sourceService' ] ][ 'types' ][ $data[ 'sourceType' ] ] ,
					$wpvr_vs[ $data[ 'sourceService' ] ]
				) ,
			);
		}
	}
	
	if( ! function_exists( 'wpvr_utf8_converter' ) ) {
		function wpvr_utf8_converter( $array ) {
			array_walk_recursive( $array , function ( &$item , $key ) {
				if( is_string( $item ) && ! mb_detect_encoding( $item , 'utf-8' , TRUE ) ) {
					$item = utf8_encode( $item );
				}
			} );
			
			return $array;
		}
	}
	
	if( ! function_exists( 'render_source_insights' ) ) {
		function render_source_insights( $insights , $class = '' ) {
			?>
			
			<?php foreach ( (array) $insights as $insight ) { ?>
				<div
					class = "wpvr_source_insights_item pull-left <?php echo $class; ?>"
					title = "<?php echo $insight[ 'title' ]; ?>"
				>
				<span class = "wpvr_source_insights_item_icon">
					<i class = "fa <?php echo $insight[ 'icon' ]; ?>"></i>
				</span>
				<span class = "wpvr_source_insights_item_value">
					<?php echo $insight[ 'value' ]; ?>
				</span>
				</div>
			<?php } ?>
			<div class = "wpvr_clearfix"></div>
			
			<?php
		}
	}
	
	if( ! function_exists( 'wpvr_d' ) ) {
		function wpvr_d( $debug_response , $separator = FALSE ) {
			ob_start();
			d( $debug_response );
			$output = ob_get_clean();
			
			return $separator . $output . $separator;
		}
	}
	
	if( ! function_exists( 'wpvr_is_theme' ) ) {
		function wpvr_is_theme( $name ) {
			$theme = wp_get_theme();
			
			$possible_names = array(
				$theme->stylesheet ,
				$theme->template ,
				$theme->parent ,
				$theme->get( 'Name' ) ,
			);
			//d( $name ) ;
			//d( $possible_names ) ;
			return in_array( $name , $possible_names );
		}
	}
	
	if( ! function_exists( 'wpvr_object_to_array' ) ) {
		function wpvr_object_to_array( $obj ) {
			if( is_object( $obj ) ) $obj = (array) $obj;
			if( is_array( $obj ) ) {
				$new = array();
				foreach ( $obj as $key => $val ) {
					$new[ $key ] = wpvr_object_to_array( $val );
				}
			} else $new = $obj;
			
			return $new;
		}
	}
	
	if( ! function_exists( 'wpvr_chrono_time' ) ) {
		function wpvr_chrono_time( $start = FALSE , $round = 6 ) {
			$time = explode( ' ' , microtime() );
			if( $start === FALSE ) return $time[ 0 ] + $time[ 1 ];
			else {
				return round( wpvr_chrono_time() - $start , $round );
			}
			
			return TRUE;
		}
	}
	
	if( ! function_exists( 'wpvr_render_multiselect' ) ) {
		function wpvr_render_multiselect( $option , $value = null , $echo = TRUE ) {
			if( $echo === FALSE ) ob_start();
			
			
			if( is_string( $value ) ) $option_value = stripslashes( $value );
			else $option_value = $value;
			
			if( isset( $option[ 'tab_class' ] ) ) $tab_class = $option[ 'tab_class' ];
			else $tab_class = '';
			
			$option_name = $option[ 'id' ];
			
			//new dBug( $option );
			
			if( ! isset( $option[ 'masterOf' ] ) || ! is_array( $option[ 'masterOf' ] ) || count( $option[ 'masterOf' ] ) == 0 ) {
				$masterOf = '';
				$isMaster = '';
			} else {
				$masterOf = ' masterOf = "' . implode( ',' , $option[ 'masterOf' ] ) . '" ';
				$isMaster = 'isMaster';
			}
			
			if( ! isset( $option[ 'masterValue' ] ) ) $masterValue = '';
			else    $masterValue = ' masterValue = "' . $option[ 'masterValue' ] . '" ';
			
			if( ! isset( $option[ 'hasMasterValue' ] ) ) $hasMasterValue = '';
			else    $hasMasterValue = ' hasMasterValue = "' . $option[ 'hasMasterValue' ] . '" ';
			
			if( ! isset( $option[ 'class' ] ) ) $option_class = '';
			else    $option_class = $option[ 'class' ];
			
			if( ! isset( $option[ 'values' ] ) || ! is_array( $option[ 'values' ] ) ) {
				echo "NO OPTION DEFINED FOR THIS SELECT";
			} else {
				
				if( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'categories' ) {
					
					// GET ALL CATEGORIES
					$cats = wpvr_get_categories_count();
					foreach ( $cats as $cat ) {
						$option[ 'values' ][ $cat[ 'value' ] ] = $cat[ 'label' ] . ' (' . $cat[ 'count' ] . ')';
					}
					
				} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'all_categories' ) {
					
					// GET ALL CATEGORIES
					$cats = wpvr_get_categories_count( FALSE , TRUE );
					foreach ( $cats as $cat ) {
						$option[ 'values' ][ $cat[ 'value' ] ] = $cat[ 'label' ] . ' (' . $cat[ 'count' ] . ')';
					}
					
				} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'post_types' ) {
					
					// GET ALL POST TYPES
					$post_types = get_post_types( array(
						'public' => TRUE ,
					) );
					foreach ( $post_types as $cpt ) {
						$option[ 'values' ][ $cpt ] = $cpt;
					}
					
					
				} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'taxonomies' ) {
					
					// GET ALL TAXONOMIES
					$taxonomies = get_taxonomies( array(
						'_builtin' => FALSE ,
					) , 'objects' );
					foreach ( $taxonomies as $tax ) {
						//new dBug( $tax );
						$option[ 'values' ][ $tax->name ] = $tax->label;
					}
					
					
				} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'post_types_ext' ) {
					$internal_cpts = array(
						//'page' ,
						'post' ,
						WPVR_VIDEO_TYPE ,
						'attachment' ,
						'revision' ,
						WPVR_SOURCE_TYPE ,
						'nav_menu_item' ,
					);
					// GET ALL POST TYPES
					$post_types = get_post_types( array(//'public' => true ,
					) );
					foreach ( $post_types as $cpt ) {
						if( ! in_array( $cpt , $internal_cpts ) )
							$option[ 'values' ][ $cpt ] = $cpt;
					}
					
				} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'tags' ) {
					
					// GET ALL TAGS
					$tags = get_tags();
					foreach ( $tags as $tag ) {
						$option[ 'values' ][ $tag->term_id ] = $tag->slug;
					}
					
				} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'authors' ) {
					
					// GET ALL AUTHORS
					$all_users = get_users( 'orderby=post_count&order=DESC' );
					foreach ( $all_users as $user ) {
						if( ! in_array( 'subscriber' , $user->roles ) )
							$option[ 'values' ][ $user->data->ID ] = $user->data->user_nicename;
					}
					
				} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'services' ) {
					
					// GET ALL AUTHORS
					global $wpvr_vs;
					foreach ( $wpvr_vs as $vs ) {
						$option[ 'values' ][ $vs[ 'id' ] ] = $vs[ 'label' ];
					}
					
				}
			}
			
			if( ! isset( $option[ 'maxItems' ] ) || $option[ 'maxItems' ] == 1 ) $mv = "1";
			elseif( $option[ 'maxItems' ] === FALSE ) $mv = '255';
			else $mv = $option[ 'maxItems' ];
			
			if( ! isset( $option[ 'placeholder' ] ) || $option[ 'placeholder' ] == '' ) {
				$option[ 'placeholder' ] = 'Pick one or more values';
			}
			?>
			<div class = "wpvr_select_wrap">
				<input type = "hidden" value = "0" name = "<?php echo $option_name; ?>[]"/>
				<select
					class = "wpvr_field_selectize "
					name = "<?php echo $option_name; ?>[]"
					id = "<?php echo $option_name; ?>"
					maxItems = "<?php echo $mv; ?>"
					placeholder = "<?php echo $option[ 'placeholder' ]; ?>"
				>
					<option value = ""> <?php echo $option[ 'placeholder' ]; ?> </option>
					<?php foreach ( $option[ 'values' ] as $oValue => $oLabel ) { ?>
						<?php
						
						if( is_array( $option_value ) && in_array( $oValue , $option_value ) ) {
							$checked  = ' selected="selected" ';
							$oChecked = ' c="1" ';
							
						} elseif( ! is_array( $option_value ) && $oValue == $option_value ) {
							$checked  = ' selected="selected" ';
							$oChecked = ' c="1" ';
						} else {
							$checked  = '';
							$oChecked = ' c="0" ';
						}
						?>
						<option value = "<?php echo $oValue; ?>" <?php echo $checked; ?> <?php echo $oChecked; ?> >
							<?php echo $oLabel; ?>
						</option>
					<?php } ?>
				</select>
			</div>
			<?php
			
			if( $echo === FALSE ) {
				$rendered_option = ob_get_contents();
				ob_get_clean();
				
				return $rendered_option;
			}
			
		}
	}
	
	/* CHECKS IF A REMOTE FILE EXISTS */
	if( ! function_exists( 'wpvr_get_folders_simple' ) ) {
		function wpvr_get_folders_simple() {
			$terms   = get_terms( WPVR_SFOLDER_TYPE , array( 'hide_empty' => FALSE , ) );
			$folders = array();
			foreach ( $terms as $term ) {
				$folders[ $term->term_id ] = $term->name . ' (' . $term->count . ') ';
			}
			
			return $folders;
		}
	}
	
	/* CHECKS IF A REMOTE FILE EXISTS */
	if( ! function_exists( 'wpvr_curl_check_remote_file_exists' ) ) {
		function wpvr_curl_check_remote_file_exists( $url ) {
			$ch = curl_init( $url );
			curl_setopt( $ch , CURLOPT_NOBODY , TRUE );
			curl_exec( $ch );
			if( curl_getinfo( $ch , CURLINFO_HTTP_CODE ) == 200 ) $status = TRUE;
			else $status = FALSE;
			curl_close( $ch );
			
			return $status;
		}
	}
	
	/* GETTING REAL TIME DIFF */
	if( ! function_exists( 'wpvr_human_time_diff' ) ) {
		function wpvr_human_time_diff( $post_id ) {
			$post          = get_post( $post_id );
			$now_date_obj  = DateTime::createFromFormat( 'Y-m-d H:i:s' , current_time( 'Y-m-d H:i:s' ) );
			$now_date      = $now_date_obj->format( 'U' );
			$post_date_obj = DateTime::createFromFormat( 'Y-m-d H:i:s' , $post->post_date );
			$post_date     = $post_date_obj->format( 'U' );
			
			return human_time_diff( $post_date , $now_date ) . ' ago';
		}
	}
	
	/* GETTING ADD DATA FROM URL */
	if( ! function_exists( 'wpvr_extract_data_from_url' ) ) {
		function wpvr_extract_data_from_url( $html , $searches = array() ) {
			$results = array();
			if( count( $searches ) == 0 ) return array();
			foreach ( $searches as $s ) {
				
				if( $s[ 'target_name' ] === FALSE ) {
					if( $s[ 'marker_double_quotes' ] === TRUE ) {
						$marker = '<' . $s[ 'tag' ] . ' ' . $s[ 'marker_name' ] . '="' . $s[ 'marker_value' ] . '"';
					} else {
						$marker = "<" . $s[ 'tag' ] . " " . $s[ 'marker_name' ] . "='" . $s[ 'marker_value' ] . "'";
					}
					$x = explode( $marker , $html );
					//d($x );
					if( $x == $html ) {
						$results[ $s[ 'target' ] ] = FALSE;
						continue;
					}
					
					$z = array_pop( $x );
					$y = explode( '</' . $s[ 'tag' ] . '>' , $z );
					
					$tv                        = $y[ 0 ];
					$tv                        = str_replace( array( '<' , '>' , ',' , ' ' ) , '' , $tv );
					$results[ $s[ 'target' ] ] = $tv;
					continue;
				}
				
				
				if( $s[ 'marker_double_quotes' ] === TRUE ) {
					$marker = '' . $s[ 'marker_name' ] . '="' . $s[ 'marker_value' ] . '"';
				} else {
					$marker = "" . $s[ 'marker_name' ] . "='" . $s[ 'marker_value' ] . "'";
				}
				
				$x = explode( $marker , $html );
				//d( $marker );d( $x );
				
				if( $x[ 0 ] == $html ) {
					$results[ $s[ 'target' ] ] = FALSE;
					continue;
				}
				$y = explode( '<' . $s[ 'tag' ] , $x[ 0 ] );
				if( $y[ 0 ] == $x[ 0 ] ) {
					$results[ $s[ 'target' ] ] = FALSE;
					continue;
				}
				$z = array_pop( $y );
				if( $s[ 'target_double_quotes' ] === TRUE ) {
					$target = '' . $s[ 'target_name' ] . '="';
				} else {
					$target = "" . $s[ 'target_name' ] . "='";
				}
				//d( $target);
				$w = explode( $target , $z );
				if( $w == $z || ! isset( $w[ 1 ] ) ) {
					$results[ $s[ 'target' ] ] = FALSE;
					continue;
				}
				
				$target_value              = str_replace( '"' , "" , $w[ 1 ] );
				$target_value              = str_replace( "'" , "" , $target_value );
				$results[ $s[ 'target' ] ] = $target_value;
			}
			
			return $results;
		}
	}
	
	/* SETTING DEBUG VALUES */
	if( ! function_exists( 'wpvr_set_debug' ) ) {
		function wpvr_set_debug( $var = null , $append = FALSE ) {
			
			$new = get_option( 'wpvr_debug' );
			if( ! is_array( $new ) ) $new = array();
			if( $append === FALSE ) $new = array( $var );
			else $new[] = $var;
			
			update_option( 'wpvr_debug' , $new );
		}
	}
	
	/* ShOW UP DEBUG VALUES */
	if( ! function_exists( 'wpvr_get_debug' ) ) {
		function wpvr_get_debug( $var = null ) {
			
			$wpvr_debug = get_option( 'wpvr_debug' );
			d( $wpvr_debug );
		}
	}
	
	/* EMPTY DEBUG VALUES */
	if( ! function_exists( 'wpvr_reset_debug' ) ) {
		function wpvr_reset_debug() { update_option( 'wpvr_debug' , array() ); }
	}
	
	/* MAKE CURL REQUEST */
	if( ! function_exists( 'wpvr_make_curl_request' ) ) {
		function wpvr_make_curl_request( $api_url = '' , $api_args = array() , $curl_object = null , $debug = FALSE , $curl_options = array() , $get_headers = FALSE ) {
			
			$timer = wpvr_chrono_time();
			if( $curl_object === null || ! is_resource( $curl_object ) ) $curl_object = curl_init();
			if( is_array( $api_args ) && count( $api_args ) > 0 ) {
				$api_url .= '?' . http_build_query( $api_args );
			}
			//d( is_resource( $curl_object ) );
			curl_setopt( $curl_object , CURLOPT_URL , $api_url );
			curl_setopt( $curl_object , CURLOPT_SSL_VERIFYPEER , FALSE );
			curl_setopt( $curl_object , CURLOPT_RETURNTRANSFER , TRUE );

			$headers = FALSE;
			if( $get_headers ) {
				curl_setopt( $curl_object , CURLOPT_HEADER , TRUE );
				curl_setopt( $curl_object , CURLOPT_VERBOSE , TRUE );
			} else {
				curl_setopt( $curl_object , CURLOPT_HEADER , FALSE );
			}

			
			if( $curl_options != array() ) {
				foreach ( (array) $curl_options as $key => $value ) {
					curl_setopt( $curl_object , $key , $value );
				}
			}
			
			$data = curl_exec( $curl_object );
			//d( $data );
			if( $get_headers ) {
				$header_size = curl_getinfo( $curl_object , CURLINFO_HEADER_SIZE );
				$headers     = explode( "\n" , substr( $data , 0 , $header_size ) );
				$data        = substr( $data , $header_size );
			}

			if( $debug === TRUE ) {
				echo $data;
				d( $data );
				d( $api_url );
				d( $api_args );
			}
			$status = curl_getinfo( $curl_object , CURLINFO_HTTP_CODE );
			
			//curl_close( $curl_object );
			
			return array(
				'exec_time' => wpvr_chrono_time( $timer ) ,
				'status'    => $status ,
				'data'      => $data ,
				'json'      => (array) json_decode( $data ) ,
				'headers'   => $headers ,
				'caller'    => array(
					'url'  => $api_url ,
					'args' => $api_args ,
				) ,
			);
		}
	}
	
	/* Prepare JSON Reponse for ajax communications */
	if( ! function_exists( 'wpvr_get_json_response' ) ) {
		function wpvr_get_json_response( $data , $response_status = 1 , $response_msg = '' , $response_count = 0 ) {
			$response         = array(
				'status' => $response_status ,
				'msg'    => $response_msg ,
				'count'  => $response_count ,
				'data'   => $data ,
			);
			$encoded_response = WPVR_JS . json_encode( $response ) . WPVR_JS;
			
			return $encoded_response;
		}
	}
	
	/* Render HTML attributes from PHP array*/
	if( ! function_exists( 'wpvr_render_html_attributes' ) ) {
		function wpvr_render_html_attributes( $attr = array() ) {
			$output = '';
			if( ! is_array( $attr ) || count( $attr ) == 0 ) return $output;
			foreach ( $attr as $key => $value ) {
				if( $value == '' || empty( $value ) ) $output .= ' ' . $key . ' ';
				else $output .= ' ' . $key . ' = "' . $value . '" ';
			}
			
			//_d( $output );
			return $output;
		}
	}
	
	/* Update Dynamic Video Views custom fields */
	if( ! function_exists( 'wpvr_update_dynamic_video_views' ) ) {
		function wpvr_update_dynamic_video_views( $post_id , $new_views ) {
			$wpvr_fillers = get_option( 'wpvr_fillers' );
			$count        = 0;
			if( ! is_array( $wpvr_fillers ) || count( $wpvr_fillers ) == 0 ) return 0;
			foreach ( $wpvr_fillers as $filler ) {
				if( $filler[ 'from' ] == 'wpvr_dynamic_views' ) {
					update_post_meta( $post_id , $filler[ 'to' ] , $new_views );
					$count ++;
				}
			}
			
			return $count;
		}
	}
	
	/* Render NOt Found */
	if( ! function_exists( 'wpvr_render_video_permalink' ) ) {
		function wpvr_render_video_permalink( $post = null , $permalink_structure = null ) {
			if( $post == null ) global $post;
			
			if( $permalink_structure == null ) {
				global $wp_rewrite;
				$permalink_structure = $wp_rewrite->permalink_structure;
			}
			
			$var_names       = array(
				'%year%' ,
				'%monthnum%' ,
				'%day%' ,
				'%hour%' ,
				'%minute%' ,
				'%second%' ,
				'%post_id%' ,
				'%postname%' ,
				'%category%' ,
				'%author%' ,
			);
			$date            = DateTime::createFromFormat( 'Y-m-d H:i:s' , $post->post_date_gmt , new DateTimeZone( 'UTC' ) );
			$post_categories = wp_get_post_categories( $post->ID , array( 'fields' => 'slugs' ) );
			if( count( $post_categories ) == 0 || ! is_array( $post_categories ) ) $post_category = '';
			else $post_category = $post_categories[ 0 ];
			$var_values = array(
				$date->format( 'Y' ) ,
				$date->format( 'm' ) ,
				$date->format( 'd' ) ,
				$date->format( 'G' ) ,
				$date->format( 'i' ) ,
				$date->format( 's' ) ,
				$post->ID ,
				$post->post_name ,
				$post_category ,
				get_the_author_meta( 'user_nicename' , $post->post_author ) ,
			);
			$permalink  = WPVR_SITE_URL . str_replace( $var_names , $var_values , $permalink_structure );
			
			return $permalink;
			
		}
	}
	
	/* Render NOt Found */
	if( ! function_exists( 'wpvr_render_not_found' ) ) {
		function wpvr_render_not_found( $msg = '' ) {
			?>
			
			<div class = "wpvr_not_found">
				<i class = "fa fa-frown-o"></i><br/>
				<?php echo $msg; ?>
			</div>
			
			<?php
		}
	}
	
	/* Render buttons of Source Screen */
	if( ! function_exists( 'wpvr_render_source_actions' ) ) {
		function wpvr_render_source_actions( $post_id = '' ) {
			$o = array( 'test' => '' , 'run' => '' , 'save' => '' , 'trash' => '' , 'clone' => '' );
			
			$o[ 'save' ] .= '<br/><button id="wpvr_save_source_btn" class="wpvr_wide_button actionBtn wpvr_submit_button wpvr_black_button">';
			$o[ 'save' ] .= '<i class="wpvr_button_icon fa fa-save"></i>';
			$o[ 'save' ] .= '<span>' . __( 'Save Source' , WPVR_LANG ) . '</span>';
			$o[ 'save' ] .= '</button><br/>';
			
			
			if( $post_id == '' ) {
				$o[ 'test' ] = '<div class="wpvr_no_actions">' . __( 'Start by saving your source' , WPVR_LANG ) . '</div>';
				
				return $o;
			}
			
			$testLink  = admin_url( 'admin.php?page=wpvr&test_sources&ids=' . $post_id , 'http' );
			$runLink   = admin_url( 'admin.php?page=wpvr&run_sources&ids=' . $post_id , 'http' );
			$cloneLink = admin_url( 'admin.php?page=wpvr&clone_source=' . $post_id , 'http' );
			$trashLink = wpvr_get_post_links( $post_id , 'trash' );
			
			$o[ 'test' ] .= '<button ready="1" url="' . $testLink . '" id="wpvr_metabox_test" class="actionBtn wpvr_submit_button wpvr_metabox_button test">';
			$o[ 'test' ] .= '<i class="wpvr_button_icon fa fa-eye"></i>';
			$o[ 'test' ] .= '<span>' . __( 'Test Source' , WPVR_LANG ) . '</span>';
			$o[ 'test' ] .= '</button><br/>';
			
			$o[ 'run' ] .= '<button ready="1" url="' . $runLink . '" id="wpvr_metabox_run" class="actionBtn wpvr_submit_button wpvr_metabox_button run">';
			$o[ 'run' ] .= '<i class="wpvr_button_icon fa fa-bolt"></i>';
			$o[ 'run' ] .= '<span>' . __( 'Run Source' , WPVR_LANG ) . '</span>';
			$o[ 'run' ] .= '</button><br/>';
			
			$o[ 'clone' ] .= '<button url="' . $cloneLink . '" id="wpvr_metabox_clone" class="actionBtn wpvr_submit_button wpvr_metabox_button clone">';
			$o[ 'clone' ] .= '<i class="wpvr_button_icon fa fa-copy"></i>';
			$o[ 'clone' ] .= '<span>' . __( 'Clone Source' , WPVR_LANG ) . '</span>';
			$o[ 'clone' ] .= '</button><br/>';
			
			
			$o[ 'trash' ] .= '<button url="' . $trashLink
			                 . '" id="wpvr_trash_source_btn" class="wpvr_wide_button actionBtn wpvr_submit_button wpvr_red_button wpvr_metabox_button trash sameWindow">';
			$o[ 'trash' ] .= '<i class="wpvr_button_icon fa fa-trash-o"></i>';
			$o[ 'trash' ] .= '<span>' . __( 'Trash Source' , WPVR_LANG ) . '</span>';
			$o[ 'trash' ] .= '</button><br/>';
			
			
			return $o;
		}
	}
	
	/* Get taxonomies data from ids */
	if( ! function_exists( 'wpvr_get_tax_data' ) ) {
		function wpvr_get_tax_data( $taxonomy , $ids ) {
			global $wpdb;
			if( ! is_array( $ids ) ) return array();
			$ids    = "'" . implode( "','" , $ids ) . "'";
			$sql
			        = "
			select 
				T.term_id as id,
				T.slug,
				T.name
			from
				$wpdb->terms T 
				INNER JOIN $wpdb->term_taxonomy TT ON T.term_id  = TT.term_taxonomy_id
			where
				T.term_id IN ( $ids )
				AND TT.taxonomy = '$taxonomy'
		";
			$terms  = $wpdb->get_results( $sql );
			$return = array();
			foreach ( $terms as $term ) {
				$return[ $term->id ] = array(
					'id'   => $term->id ,
					'slug' => $term->slug ,
					'name' => $term->name ,
				);
			}
			
			return $return;
		}
	}
	
	/* Show An Update Is Availabe message function */
	if( ! function_exists( 'wpvr_show_available_update_message' ) ) {
		function wpvr_show_available_update_message() {
			global $wpvr_new_version_available , $wpvr_new_version_msg;
			?>
			<div class = "updated">
				<p>
					<strong>WP Video Robot</strong><br/>
					<?php _e( 'There is a new update available !' , WPVR_LANG ); ?> (<strong> Version <?php echo $wpvr_new_version_available; ?></strong>)
					
					<?php if( ! empty( $wpvr_new_version_msg ) ) { ?>
						<br/><br/><?php echo $wpvr_new_version_msg; ?>
					<?php } ?>
					
					<?php
						$link = WPVR_SITE_URL . "/wp-admin/plugin-install.php?tab=plugin-information&plugin=" . WPVR_LANG . "&section=changelog&TB_iframe=true&width=640&height=662";
						echo '<br/><br/><a href="' . $link . '" > UPDATE NOW </a>';
					?>
				
				</p>
			</div>
			<?php
		}
	}
	
	/*Draw Stress Graph for selected day */
	if( ! function_exists( 'wpvr_draw_stress_graph_by_day' ) ) {
		function wpvr_draw_stress_graph_by_day( $date , $hex_color ) {
			
			$stress_data = wpvr_get_schedule_stress( $date->format( 'Y-m-d' ) );
			
			//d( $stress_data );
			
			
			//new dBug( $stress_data );
			list( $r , $g , $b ) = sscanf( $hex_color , "#%02x%02x%02x" );
			$jsData = array(
				'name'               => 'Stress on ' . $date->format( 'Y-m-d' ) ,
				'fillColor'          => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.2)' ,
				'strokeColor'        => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.8)' ,
				'pointColor'         => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.8)' ,
				'pointHighlightFill' => 'rgba(255,255,255,0.9)' ,
				'labels'             => '' ,
				'count'              => '' ,
				'stress'             => '' ,
				'max'                => '' ,
			);
			foreach ( (array) $stress_data as $hour => $data ) {
				$jsData[ 'labels' ] .= ' "' . $hour . '" ,';
				$jsData[ 'count' ] .= ' ' . $data[ 'count' ] . ' ,';
				$jsData[ 'max' ] .= ' 100 ,';
				//$jsData['stress'] .= ' '.(100*round( $data['stress']/800 , 2 )).' ,';
				$jsData[ 'stress' ] .= $data[ 'wanted' ] . ' ,';
			}
			$jsData[ 'labels' ] = '[' . substr( $jsData[ 'labels' ] , 0 , - 1 ) . ']';
			$jsData[ 'count' ]  = '[' . substr( $jsData[ 'count' ] , 0 , - 1 ) . ']';
			$jsData[ 'stress' ] = '[' . substr( $jsData[ 'stress' ] , 0 , - 1 ) . ']';
			$jsData[ 'max' ]    = '[' . substr( $jsData[ 'max' ] , 0 , - 1 ) . ']';
			
			$graph_id = 'wpvr_chart_stress_graph-' . rand( 100 , 10000 );
			
			
			?>
			<!-- DAY STRESS GRAPH -->
			<div id = "" class = "postbox ">
				<h3 class = "hndle"><span> <?php echo __( 'Stress Forecast for :' , WPVR_LANG ) . ' ' . $date->format( 'l d F Y' ); ?> </span></h3>
				
				<div class = " inside">
					<div class = "wpvr_graph_wrapper" style = "width:100% !important; height:400px !important;">
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
							wpvr_draw_chart(
								$('#<?php echo $graph_id; ?>'),
								$('#<?php echo $graph_id; ?>_legend'),
								data_stress,
								'radar'
							);
						});
					</script>
				</div>
			</div>
			<?php
		}
	}
	
	if( ! function_exists( 'wpvr_async_draw_stress_graph_by_day' ) ) {
		function wpvr_async_draw_stress_graph_by_day( $date , $hex_color ) {
			$chart_id = 'wpvr_chart_stress_graph_' . rand( 0 , 1000000 );
			?>
			<!-- DAY STRESS GRAPH -->
			<div
				class = "wpvr_async_graph postbox"
				day = "<?php echo strtolower( $date->format( 'l' ) ); ?>"
				daylabel = "<?php echo( $date->format( 'Y-m-d' ) ); ?>"
				daytime = "<?php echo( $date->format( 'c' ) ); ?>"
				hex_color = "<?php echo $hex_color; ?>"
				url = "<?php echo WPVR_ACTIONS_URL; ?>"
				chart_id = "<?php echo $chart_id; ?>"
			>
				<h3 class = "hndle">
					<span>
						<?php echo ucfirst( $date->format( 'l' ) ) . ' ' . __( 'Stress Forecast' , WPVR_LANG ); ?>
					</span>
				</h3>
				
				<div class = " inside">
					<div class = "wpvr_insite_loading">
						<i class = "fa fa-refresh fa-spin"></i>
						<span>Please Wait ... </span>
					</div>
					<div class = "wpvr_graph_wrapper" style = "display:none;width:100% !important; height:400px !important;">
						<canvas id = "<?php echo $chart_id; ?>" width = "900" height = "400"></canvas>
					</div>
				</div>
			</div>
			<?php
		}
	}
	
	/* Generate stress schedule array */
	if( ! function_exists( 'wpvr_async_get_schedule_stress' ) ) {
		function wpvr_async_get_schedule_stress( $date = '' ) {
			$stress_data = FALSE;
			$stress_data = apply_filters( 'wpvr_extend_schedule_stress' , $stress_data , $date );
			
			return $stress_data;
		}
	}
	
	if( ! function_exists( 'wpvr_get_schedule_stress' ) ) {
		function wpvr_get_schedule_stress( $day = '' ) {
			global $wpvr_options , $wpvr_stress , $wpvr_days;
			//new dBug( $wpvr_days );
			
			if( $day == '' ) $day_name = $wpvr_days[ strtolower( date( 'N' ) ) ];
			else {
				$day_num  = strtolower( date( 'N' , strtotime( $day ) ) );
				$day_name = $wpvr_days[ $day_num ];
			}
			
			//new dBug( $day_name );
			
			$stress_per_hour = array(
				'00H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'01H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'02H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'03H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'04H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'05H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'06H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'07H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'08H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'09H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'10H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'11H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'12H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'13H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'14H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'15H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'16H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'17H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'18H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'19H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'20H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'21H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'22H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'23H00' => array( 'max' => $wpvr_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
			);
			$sources         = wpvr_get_sources( array(
				'status' => 'on' ,
			) );
			$sources         = wpvr_multiplicate_sources( $sources );
			foreach ( $sources as $source ) {
				//new dBug($source);
				
				//d( $source );
				
				$wantedVideos  = ( $source->wantedVideosBool == 'default' ) ? $wpvr_options[ 'wantedVideos' ] : $source->wantedVideos;
				$getTags       = ( $source->getVideoTags == 'default' ) ? $wpvr_options[ 'getTags' ] : ( ( $source->getVideoTags == 'on' ) ? TRUE : FALSE );
				$getStats      = ( $source->getVideoStats == 'default' ) ? $wpvr_options[ 'getStats' ] : ( ( $source->getVideoStats == 'on' ) ? TRUE : FALSE );
				$onlyNewVideos = ( $source->onlyNewVideos == 'default' ) ? $wpvr_options[ 'onlyNewVideos' ] : ( ( $source->onlyNewVideos == 'on' ) ? TRUE : FALSE );
				
				$source_stress = 0;
				if( $getTags ) $source_stress += $wantedVideos * $wpvr_stress[ 'getTags' ];
				if( $getStats ) $source_stress += $wantedVideos * $wpvr_stress[ 'getStats' ];
				if( $onlyNewVideos ) $source_stress += $wantedVideos * $wpvr_stress[ 'onlyNewVideos' ];
				$source_stress = $source_stress * $wpvr_stress[ 'wantedVideos' ] * $wpvr_stress[ 'base' ];
				
				if( $source->schedule == 'hourly' ) {
					foreach ( $stress_per_hour as $hour => $value ) {
						$myhour    = explode( 'H' , $hour );
						$isWorking = wpvr_is_working_hour( $myhour[ 0 ] );
						
						if( $isWorking ) {
							$stress_per_hour[ $hour ][ 'stress' ] += $source_stress;
							$stress_per_hour[ $hour ][ 'count' ] ++;
							$stress_per_hour[ $hour ][ 'wanted' ] += $wantedVideos;
							$stress_per_hour[ $hour ][ 'sources' ][] = $source;
						}
					}
				} elseif( $source->schedule == 'daily' ) {
					$myhour    = explode( 'H' , $source->scheduleTime );
					$isWorking = wpvr_is_working_hour( $myhour[ 0 ] );
					
					if( $isWorking ) {
						$stress_per_hour[ $source->scheduleTime ][ 'stress' ] += $source_stress;
						$stress_per_hour[ $source->scheduleTime ][ 'count' ] ++;
						$stress_per_hour[ $source->scheduleTime ][ 'wanted' ] += $wantedVideos;
						$stress_per_hour[ $source->scheduleTime ][ 'sources' ][] = $source;
					}
				} elseif( $source->schedule == 'weekly' ) {
					
					if( $day_name == $source->scheduleDay ) {
						
						$myhour    = explode( 'H' , $source->scheduleTime );
						$isWorking = wpvr_is_working_hour( $myhour[ 0 ] );
						
						if( $isWorking ) {
							$stress_per_hour[ $source->scheduleTime ][ 'stress' ] += $source_stress;
							$stress_per_hour[ $source->scheduleTime ][ 'count' ] ++;
							$stress_per_hour[ $source->scheduleTime ][ 'wanted' ] += $wantedVideos;
							$stress_per_hour[ $source->scheduleTime ][ 'sources' ][] = $source;
						}
					}
				}
			}
			
			return ( $stress_per_hour );
		}
	}
	
	/* Init cAPI */
	if( ! function_exists( 'wpvr_capi_init' ) ) {
		function wpvr_capi_init() {
			if( isset( $_GET[ 'capi' ] ) ) {
				if( isset( $_POST[ 'action' ] ) ) {
					wpvr_capi_do( $_POST[ 'action' ] , $_POST );
				} else echo "SILENCE IS GOLDEN.";
				exit;
			}
		}
	}
	
	/* Do cAPI */
	if( ! function_exists( 'wpvr_capi_do' ) ) {
		function wpvr_capi_do( $action , $_post ) {
			$r = array(
				'status' => FALSE ,
				'msg'    => '' ,
				'data'   => null ,
			);
			
			if( $action == 'add_notice' ) {
				if( ! isset( $_post[ 'notice' ] ) ) {
					$r[ 'status' ] = FALSE;
					$r[ 'msg' ]    = 'Notice variable missing. EXIT...';
					echo json_encode( $r );
				}
				$notice = (array) json_decode( base64_decode( $_post[ 'notice' ] ) );
				$slug   = wpvr_add_notice( $notice );
				if( $slug != FALSE ) {
					$r[ 'status' ] = TRUE;
					$r[ 'msg' ]    = 'Notice Added (slug = ' . $slug . '). DONE...';
					$r[ 'data' ]   = $slug;
					echo json_encode( $r );
				} else {
					$r[ 'status' ] = FALSE;
					$r[ 'msg' ]    = 'Error adding the notice. EXIT...';
					echo json_encode( $r );
				}
				
				return FALSE;
			}
			
			if( $action == 'get_activation' ) {
				
				$act = wpvr_get_activation( $_post[ 'slug' ] );
				
				echo json_encode( array(
					'status' => $act[ 'act_status' ] ,
					'msg'    => 'Activation returned.' ,
					'data'   => $act ,
				) );
				
				return FALSE;
			}
			
			if( $action == 'reset_activation' ) {
				
				wpvr_set_activation( $_post[ 'slug' ] , array() );
				echo json_encode( array(
					'status' => 1 ,
					'msg'    => 'Reset Completed.' ,
					'data'   => null ,
				) );
				
				return FALSE;
			}
			
			if( $action == 'reload_addons' ) {
				update_option( 'wpvr_addons_list' , '' );
				$r[ 'status' ] = TRUE;
				$r[ 'msg' ]    = 'ADDONS LIST RESET ...';
				echo json_encode( $r );
				
				return FALSE;
			}
			
		}
	}
	
	/*Get Act data even empty */
	if( ! function_exists( 'wpvr_can_show_menu_links' ) ) {
		function wpvr_can_show_menu_links( $user_id = '' ) {
			global $wpvr_options , $user_ID;
			
			if( $user_id == '' ) $user_id = $user_ID;
			$user       = new WP_User( $user_id );
			$user_roles = $user->roles;
			
			$super_roles = array( 'administrator' , 'superadmin' );
			foreach ( $user_roles as $role ) {
				if( in_array( $role , $super_roles ) ) return TRUE;
			}
			
			//d( $user_roles );
			
			
			if( $wpvr_options[ 'showMenuFor' ] == null ) return FALSE;
			foreach ( $wpvr_options[ 'showMenuFor' ] as $role ) {
				if( in_array( $role , $user_roles ) ) return TRUE;
			}
			
			return FALSE;
		}
	}
	
	/*Get Act data even empty */
	
	if( ! function_exists( 'wpvr_get_act_data' ) ) {
		function wpvr_get_act_data( $slug = 'wpvr' ) {
			global $wpvr_empty_activation;
			$wpvr_acts = get_option( 'wpvr_activations' );
			if( ! array( $wpvr_acts ) ) $wpvr_acts = array();
			if( ! isset( $wpvr_acts[ $slug ] ) ) $wpvr_acts[ $slug ] = $wpvr_empty_activation;
			
			if( ! isset( $wpvr_acts[ $slug ][ 'buy_expires' ] ) ) {
				$now                                 = new Datetime();
				$wpvr_acts[ $slug ][ 'buy_expires' ] = $now->format( 'Y-m-d H:i:s' );
			}
			
			if( $wpvr_acts[ $slug ] != '' ) {
				return array(
					'act_status'  => $wpvr_acts[ $slug ][ 'act_status' ] ,
					'act_id'      => $wpvr_acts[ $slug ][ 'act_id' ] ,
					'act_email'   => $wpvr_acts[ $slug ][ 'act_email' ] ,
					'act_code'    => $wpvr_acts[ $slug ][ 'act_code' ] ,
					'act_date'    => $wpvr_acts[ $slug ][ 'act_date' ] ,
					'buy_date'    => $wpvr_acts[ $slug ][ 'buy_date' ] ,
					'buy_user'    => $wpvr_acts[ $slug ][ 'buy_user' ] ,
					'buy_licence' => $wpvr_acts[ $slug ][ 'buy_licence' ] ,
					'act_addons'  => $wpvr_acts[ $slug ][ 'act_addons' ] ,
					'buy_expires' => $wpvr_acts[ $slug ][ 'buy_expires' ] ,
				);
			}
		}
	}
	
	if( ! function_exists( 'wpvr_set_act_data' ) ) {
		function wpvr_set_act_data( $slug = 'wpvr' , $new_data ) {
			$wpvr_acts = get_option( 'wpvr_activations' );
			if( ! array( $wpvr_acts ) ) $wpvr_acts = array();
			$wpvr_acts[ $slug ] = $new_data;
			update_option( 'wpvr_activations' , $wpvr_acts );
		}
	}
	
	if( ! function_exists( 'wpvr_refresh_act_data' ) ) {
		function wpvr_refresh_act_data( $slug = 'wpvr' , $do_refresh = FALSE ) {
			global $WPVR_SERVER;
			$act = wpvr_get_act_data( $slug );
			$url = wpvr_capi_build_query( WPVR_API_REQ_URL , array(
				'api_key'         => WPVR_API_REQ_KEY ,
				'action'          => 'check_license' ,
				'products_slugs'  => $slug ,
				'act_id'          => $act[ 'act_id' ] , //921
				'encrypt_results' => 1 ,
				'only_results'    => 1 ,
				'origin'          => $WPVR_SERVER[ 'HTTP_HOST' ] ,
			) );
			
			$response = wpvr_capi_remote_get( $url , FALSE );
			//d( $response );
			
			if( $response[ 'status' ] != 200 ) {
				echo "CAPI Unreachable !";
				
				return FALSE;
			}
			$fresh_license = json_decode( base64_decode( $response[ 'data' ] ) , TRUE );
			//d( $fresh_license );
			$new_data                  = $act;
			$new_data[ 'act_status' ]  = $fresh_license[ 'state' ];
			$new_data[ 'act_id' ]      = $fresh_license[ 'id' ];
			$new_data[ 'act_email' ]   = $fresh_license[ 'act_email' ];
			$new_data[ 'act_code' ]    = $fresh_license[ 'act_code' ];
			$new_data[ 'act_date' ]    = $fresh_license[ 'act_date' ];
			$new_data[ 'buy_date' ]    = $fresh_license[ 'buy_date' ];
			$new_data[ 'buy_user' ]    = $fresh_license[ 'buy_user' ];
			$new_data[ 'buy_licence' ] = 'inactive';
			$new_data[ 'act_addons' ]  = array();
			$new_data[ 'buy_expires' ] = $fresh_license[ 'buy_expires' ];
			if( $do_refresh ) wpvr_set_act_data( $slug , $new_data );
			
			return $new_data;
		}
	}
	
	
	if( ! function_exists( 'wpvr_license_is_expired' ) ) {
		function wpvr_license_is_expired( $slug ) {
			$new    = wpvr_refresh_act_data( $slug , TRUE );
			$now    = new Datetime();
			$expire = new Datetime( $new[ 'buy_expires' ] );
			
			return ( $now > $expire );
		}
	}
	
	
	//Set Activation
	if( ! function_exists( 'wpvr_set_activation' ) ) {
		function wpvr_set_activation( $product_slug = '' , $act = array() ) {
			global $wpvr_empty_activation;
			$act              = wpvr_extend( $act , $wpvr_empty_activation );
			$wpvr_activations = get_option( 'wpvr_activations' );
			if( ! array( $wpvr_activations ) ) $wpvr_activations = array();
			
			$wpvr_activations[ $product_slug ] = $act;
			
			update_option( 'wpvr_activations' , $wpvr_activations );
			
			
		}
	}
	// Is a free addon ?
	if( ! function_exists( 'wpvr_is_free_addon' ) ) {
		function wpvr_is_free_addon( $product_slug = '' ) {
			global $wpvr_addons;
			if(
				isset( $wpvr_addons[ $product_slug ][ 'infos' ][ 'free_addon' ] )
				&& $wpvr_addons[ $product_slug ][ 'infos' ][ 'free_addon' ] === TRUE
			) {
				return TRUE;
			} else {
				return FALSE;
			}
			
			
		}
	}

	// Get Multisite Actctivation
	if( ! function_exists( 'wpvr_get_multisite_activation' ) ) {
		function wpvr_get_multisite_activation( $product_slug = '' , $_blog_id = null , $first_only = FALSE ) {
			global $wpvr_empty_activation , $wpvr_addons;


			$blogs = wp_get_sites( array() );
			//d( $blogs );
			$returned_activations = array();
			$first_valid_activation = FALSE;
			foreach ( (array) $blogs as $blog ) {

				$blog_id = $blog[ 'blog_id' ];

				if( $_blog_id != null && $_blog_id != $blog_id ) continue;

				$wpvr_activations = get_blog_option( $blog_id , 'wpvr_activations' );

				//if( $product_slug == 'wpvr-fbvs' ){
				//	d( $wpvr_activations[ $product_slug ] );
				//}

				if( $wpvr_activations != FALSE ) {

					if( $product_slug == '' ) {
						$returned_activations[ $blog_id ] = $wpvr_activations;
					} elseif( isset( $wpvr_activations[ $product_slug ] ) ) {

						$returned_activations[ $blog_id ] = $wpvr_activations[ $product_slug ];
						if( $wpvr_activations[ $product_slug ]['act_status'] == 1 ){
							$first_valid_activation = $wpvr_activations[ $product_slug ] ;
						}
					} else {
						$returned_activations[ $blog_id ] = $wpvr_empty_activation;
					}

					//if( $first_only ) break;

				}


				//d( $blog['path'] );

				//d( $old_activations );
			}

			//d( $returned_activations );
			if( count( $returned_activations ) == 0 ) return FALSE;

			if( $first_only ) {
				//return array_pop( $returned_activations );
				return $first_valid_activation;
			}

			return $returned_activations;


			//$wpvr_activations = get_option( 'wpvr_activations' );
			//$old_activation   = get_option( 'wpvr_activation' );
			//
			//if( $product_slug == '' ) return $wpvr_activations;
			//if( ! array( $wpvr_activations ) ) $wpvr_activations = array();
			//
			//if( ! isset( $wpvr_activations[ $product_slug ] ) ) {
			//	if( $product_slug == 'wpvr' && is_array( $old_activation ) ) {
			//		$wpvr_activations[ $product_slug ] = $old_activation;
			//	} else {
			//		$wpvr_activations[ $product_slug ] = $wpvr_empty_activation;
			//	}
			//}
			//
			//return $wpvr_activations[ $product_slug ];

		}
	}

	// Get Actctivation
	if( ! function_exists( 'wpvr_get_activation' ) ) {
		function wpvr_get_activation( $product_slug = '' ) {
			global $wpvr_empty_activation , $wpvr_addons;

			$wpvr_activations = get_option( 'wpvr_activations' );
			$old_activation   = get_option( 'wpvr_activation' );

			if( $product_slug == '' ) return $wpvr_activations;
			if( ! array( $wpvr_activations ) ) $wpvr_activations = array();
			
			if( ! isset( $wpvr_activations[ $product_slug ] ) ) {
				if( $product_slug == 'wpvr' && is_array( $old_activation ) ) {
					$wpvr_activations[ $product_slug ] = $old_activation;
				} else {
					$wpvr_activations[ $product_slug ] = $wpvr_empty_activation;
				}
			}
			
			return $wpvr_activations[ $product_slug ];
			
		}
	}
	
	if( ! function_exists( 'wpvr_set_activation' ) ) {
		function wpvr_set_activation( $product_slug = '' , $activation ) {
			$wpvr_activations                  = get_option( 'wpvr_activations' );
			$wpvr_activations[ $product_slug ] = $activation;
			update_option( 'wpvr_activations' , $wpvr_activations );
		}
	}
	
	
	/* Useful function for tracking activation/deactivation errors */
	if( ! function_exists( 'wpvr_save_errors' ) ) {
		function wpvr_save_errors( $error ) {
			$errors = get_option( 'wpvr_errors' );
			if( ! is_array( $errors ) ) $errors = array();
			if( $error != '' ) $errors[] = $error;
			update_option( 'wpvr_errors' , $errors );
		}
	}
	
	if( ! function_exists( 'wpvr_reset_on_activation' ) ) {
		function wpvr_reset_on_activation() {
			global $wpvr_imported;
			
			//reset tables
			update_option( 'wpvr_deferred' , array() );
			update_option( 'wpvr_deferred_ids' , array() );
			update_option( 'wpvr_imported' , array() );
			
			//Update IMPORTED
			wpvr_update_imported_videos();
			$wpvr_imported = get_option( 'wpvr_imported' );
			
		}
	}
	
	
	/* GET CATEGORIES with count */
	if( ! function_exists( 'wpvr_get_categories_count' ) ) {
		function wpvr_get_categories_count( $invert = FALSE , $get_empty = FALSE , $hierarchy = FALSE , $ids = '' ) {
			$items = get_categories( $args = array(
				'type'         => array( WPVR_VIDEO_TYPE ) ,
				'child_of'     => 0 ,
				'parent'       => '' ,
				'orderby'      => 'name' ,
				'order'        => 'ASC' ,
				'hide_empty'   => 0 ,
				'hierarchical' => 0 ,
				'exclude'      => '' ,
				'include'      => $ids ,
				'number'       => '' ,
				'taxonomy'     => 'category' ,
				'pad_counts'   => FALSE ,
			
			) );
			
			//new dBug( $items );
			
			if( count( $items ) == 0 ) return array();
			$rCats = array();
			
			foreach ( $items as $item ) {
				
				$cat_item = array(
					'slug'  => $item->slug ,
					'label' => $item->name ,
					'value' => $item->term_id ,
					'count' => $item->count ,
				);
				
				if( $get_empty === TRUE ) {
					$rCats[ $cat_item[ 'value' ] ] = $cat_item;
				} else {
					if( $cat_item[ 'count' ] > 0 )
						$rCats[ $cat_item[ 'value' ] ] = $cat_item;
				}
			}
			
			return $rCats;
		}
	}
	
	
	/* GET CATEGORIES FOR DROPDOWN */
	if( ! function_exists( 'wpvr_get_categories' ) ) {
		function wpvr_get_categories( $invert = FALSE ) {
			
			$catsArray = array();
			$wp_cats   = get_categories( array(
				'type'       => array( 'post' , WPVR_VIDEO_TYPE ) ,
				'orderby'    => 'name' ,
				'hide_empty' => FALSE ,
			) );
			foreach ( $wp_cats as $cat ) {
				if( $invert ) $catsArray[ $cat->term_id ] = $cat->name;
				else $catsArray[ $cat->name ] = $cat->term_id;
			}
			
			return $catsArray;
		}
	}
	
	/* GET AUTHORS POST DATES */
	if( ! function_exists( 'wpvr_get_dates_count' ) ) {
		function wpvr_get_dates_count() {
			global $wpdb;
			$sql
				= "
			select 
				DATE_FORMAT( P.post_date ,'%M %Y') as label,
				DATE_FORMAT( P.post_date ,'%Y-%m') as value,
				count( distinct P.ID) as count
				
			FROM 
				$wpdb->posts P 
			WHERE 
				P.post_type = '" . WPVR_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
			GROUP BY 
				YEAR(P.post_date),MONTH(P.post_date)
				
		";
			
			$items = $wpdb->get_results( $sql , OBJECT );
			if( count( $items ) == 0 ) return array();
			$rDates = array();
			
			foreach ( $items as $item ) {
				$rDates[ $item->value ] = array(
					'label' => $item->label ,
					'value' => $item->value ,
					'count' => $item->count ,
				);
			}
			
			return $rDates;
		}
	}
	
	/* GET SERVICES FOR DROPDOWN */
	if( ! function_exists( 'wpvr_get_services_count' ) ) {
		function wpvr_get_services_count() {
			global $wpdb , $wpvr_services;
			global $wpvr_vs;
			$sql
				= "
			select 
				M_SERVICE.meta_value as value,
				1 as label,
				count(distinct P.ID) as found_videos
			FROM 
				$wpdb->posts P 
				INNER JOIN $wpdb->postmeta M_SERVICE ON P.ID = M_SERVICE.post_id
			WHERE 
				P.post_type = '" . WPVR_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
				AND (M_SERVICE.meta_key = 'wpvr_video_service' )
			GROUP BY M_SERVICE.meta_value
			ORDER BY found_videos DESC
				
		";
			//$sql =
			$items = $wpdb->get_results( $sql , OBJECT );
			
			if( count( $items ) == 0 ) return array();
			$rServices = array();
			
			foreach ( $items as $item ) {
				if( isset( $wpvr_vs[ $item->value ] ) ) {
					$rServices[ $item->value ] = array(
						'label' => $wpvr_vs[ $item->value ][ 'label' ] ,
						'value' => $item->value ,
						'count' => $item->found_videos ,
					);
				}
			}
			
			return $rServices;
		}
	}
	
	/* GET AUTHORS FOR DROPDOWN */
	if( ! function_exists( 'wpvr_get_authors_count' ) ) {
		function wpvr_get_authors_count() {
			global $wpdb;
			$sql
				= "
			select 
				U.user_login as label,
				U.ID as value,
				COUNT(distinct P.ID ) as count				
			FROM 
				$wpdb->posts P 
				left join $wpdb->users U on U.ID = P.post_author
			WHERE 
				P.post_type = '" . WPVR_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
			GROUP BY U.ID
		";
			
			$items = $wpdb->get_results( $sql , OBJECT );
			
			
			if( count( $items ) == 0 ) return array();
			$rItems = array();
			
			foreach ( $items as $item ) {
				$rItems[ $item->value ] = array(
					'label' => $item->label ,
					'value' => $item->value ,
					'count' => $item->count ,
				);
			}
			
			return $rItems;
			
		}
	}
	
	/* GET STATUSES FOR DROPDOWN */
	if( ! function_exists( 'wpvr_get_status_count' ) ) {
		function wpvr_get_status_count() {
			global $wpdb , $wpvr_status;
			$sql
				= "
			select 
				1 as label,
				P.post_status as value,
				COUNT(distinct P.ID ) as count				
			FROM 
				$wpdb->posts P 
			WHERE 
				P.post_type = '" . WPVR_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
			GROUP BY 
				P.post_status
		";
			
			$items = $wpdb->get_results( $sql , OBJECT );
			if( count( $items ) == 0 ) return array();
			$rItems = array();
			
			foreach ( $items as $item ) {
				if( isset( $wpvr_status[ $item->value ] ) ) {
					$rItems[ $item->value ] = array(
						'label' => $wpvr_status[ $item->value ][ 'label' ] ,
						'value' => $item->value ,
						'count' => $item->count ,
					);
				}
			}
			
			return $rItems;
			
		}
	}
	
	/* GET AUTHORS */
	if( ! function_exists( 'wpvr_get_authors' ) ) {
		function wpvr_get_authors( $invert = FALSE , $default = FALSE , $restrict = FALSE ) {
			$options      = array(
				'orderby' => 'login' ,
				'order'   => 'ASC' ,
				'show'    => 'login' ,
				'who'     => 'authors' ,
			);
			$blogusers    = get_users( $options );
			$authors      = array();
			$current_user = wp_get_current_user();
			//new dBug( $blogusers) ;
			
			if( current_user_can( WPVR_USER_CAPABILITY ) && $default ) {
				if( ! $invert ) $authors[ ' - Default - ' ] = "default";
				else $authors[ 'default' ] = ' - Default - ';
			}
			if( ! current_user_can( WPVR_USER_CAPABILITY ) ) {
				if( $invert ) $authors[ $current_user->ID ] = $current_user->user_login;
				else $authors[ $current_user->user_login ] = $current_user->ID;
				
				return $authors;
			} else {
				foreach ( $blogusers as $user ) {
					$user_id = $user->data->ID;
					if( $invert ) $authors[ $user->ID ] = $user->user_login;
					else $authors[ $user->user_login ] = $user->ID;
				}
				
				return $authors;
			}
		}
	}
	
	/* Returns formatted and abreviated number */
	if( ! function_exists( 'wpvr_numberK' ) ) {
		function wpvr_numberK( $n , $double = FALSE ) {
			
			if( $n <= 999 ) {
				if( $double && $n < 10 ) return '0' . $n;
				else return $n;
			} elseif( $n > 999 && $n < 999999 ) return round( $n / 1000 , 2 ) . 'K';
			elseif( $n > 999999 ) return round( $n / 1000000 , 2 ) . 'M';
			else return FALSE;
		}
	}
	
	/* Return formated duration */
	if( ! function_exists( 'wpvr_human_duration' ) ) {
		function wpvr_human_duration( $seconds ) {
			if( $seconds > 86400 ) {
				$seconds -= 86400;
				
				return ( gmdate( "j\d H:i:s" , $seconds ) );
			} else return ( gmdate( "H:i:s" , $seconds ) );
		}
	}
	
	/* DECIDE WETHER TO RUN CRON OR NO */
	if( ! function_exists( 'wpvr_doWork' ) ) {
		function wpvr_doWork() {
			global $wpvr_options;
			$doWork   = FALSE;
			$now      = new DateTime();
			$hour_now = $now->format( 'H' );
			if( $wpvr_options[ 'autoRunMode' ] === FALSE ) {
				//echo "AUTORUN MODE DISABLED ! ";
				return FALSE;
			}
			if( $wpvr_options[ 'wakeUpHours' ] ) {
				$wuhA = $wpvr_options[ 'wakeUpHoursA' ];
				$wuhB = $wpvr_options[ 'wakeUpHoursB' ];
				if( $wuhA == 'empty' || $wuhB == 'empty' ) $doWork = TRUE;
				else {
					$doWork = ( $hour_now >= $wuhA && $hour_now <= $wuhB );
				}
			} else $doWork = TRUE;
			
			return $doWork;
		}
	}
	
	/* Extends variables with default values */
	if( ! function_exists( 'wpvr_extend' ) ) {
		function wpvr_extend( $params , $params_def , $strict = FALSE ) {
			foreach ( $params_def as $key => $val ) {
				if( ! isset( $params[ $key ] ) ) {
					
					$params[ $key ] = $val;
					
				} elseif( $strict === FALSE && $params[ $key ] == "" && ! is_bool( $params[ $key ] ) ) {
					$params[ $key ] = $val;
					
				} elseif( isset( $params[ $key ] ) && is_bool( $params[ $key ] ) ) {
					
					
				}
			}
			
			return $params;
		}
	}
	
	/* Generate recursive log messages */
	if( ! function_exists( 'wpvr_recursive_log_msgs' ) ) {
		function wpvr_recursive_log_msgs( $log_msgs , $lineHTML ) {
			foreach ( $log_msgs as $msg ) {
				if( ! is_array( $msg ) ) {
					$lineHTML .= "<div class='wpvr_log_msgs'>" . $msg . "</div>";
				} else {
					$lineHTML .= "<div class='wpvr_log_msgs_rec'>";
					$lineHTML = wpvr_recursive_log_msgs( $msg , $lineHTML );
					$lineHTML .= "</div>";
				}
				
				return $lineHTML;
			}
		}
	}
	
	/* Return random post date according to wpvr options */
	if( ! function_exists( 'wpvr_random_postdate' ) ) {
		function wpvr_make_postdate( $post_date = '' ) {
			global $wpvr_options;
			
			if( $post_date == '' ) $post_date = new DateTime();
			else $post_date = new DateTime( $post_date );
			if( $wpvr_options[ 'randomize' ] && $wpvr_options[ 'randomizeStep' ] != 'empty' ) {
				$step = $wpvr_options[ 'randomizeStep' ];
				if( $step == "minute" ) $interval = new DateInterval( 'PT' . mt_rand( 0 , 60 ) . 'S' );
				elseif( $step == "hour" ) $interval = new DateInterval( 'PT' . mt_rand( 0 , 60 ) . 'M' );
				elseif( $step == "day" ) $interval = new DateInterval( 'PT' . mt_rand( 0 , 24 ) . 'H' );
				else return FALSE;
				
				$signs = array( '-' , '+' );
				if( $signs[ rand( 0 , 1 ) ] == '-' ) $post_date->add( $interval );
				else $post_date->add( $interval );
				
				return $post_date;
				
			} else {
				$post_date = new DateTime();
				
				return $post_date;
			}
		}
	}
	
	/* Generate Colors */
	if( ! function_exists( 'wpvr_generate_colors' ) ) {
		function wpvr_generate_colors( $ColorSteps = 0 ) {
			$flat_colors = array(
				'#D24D57' ,
				'#F22613' ,
				'#FF0000' ,
				'#D91E18' ,
				'#96281B' ,
				'#E74C3C' ,
				'#CF000F' ,
				'#C0392B' ,
				'#D64541' ,
				'#EF4836' ,
				'#DB0A5B' ,
				'#F64747' ,
				'#E08283' ,
				'#F62459' ,
				'#E26A6A' ,
				'#D2527F' ,
				'#F1A9A0' ,
				'#16A085' ,
				'#2ECC71' ,
				'#27AE60' ,
				'#3498DB' ,
				'#2980B9' ,
				'#9B59B6' ,
				'#8E44AD' ,
				'#34495E' ,
				'#2C3E50' ,
				'#2C3E50' ,
				'#F1C40F' ,
				'#F39C12' ,
				'#E67E22' ,
				'#D35400' ,
				'#E74C3C' ,
				'#C0392B' ,
				'#BDC3C7' ,
				'#95A5A6' ,
				'#7F8C8D' ,
				'#1F3A93' ,
				'#4B77BE' ,
				'#34495E' ,
				'#336E7B' ,
				'#22A7F0' ,
				'#3498DB' ,
				'#2C3E50' ,
				'#22313F' ,
				'#52B3D9' ,
				'#1F3A93' ,
				'#65C6BB' ,
				'#68C3A3' ,
				'#26A65B' ,
				'#66CC99' ,
				'#019875' ,
				'#1E824C' ,
				'#00B16A' ,
				'#1BA39C' ,
				'#2ABB9B' ,
				'#6C7A89' ,
				'#F89406' ,
				'#F9690E' ,
				'#EB974E' ,
				'#E67E22' ,
				'#F39C12' ,
				'#F4D03F' ,
				'#F7CA18' ,
				'#F5D76E' ,
				'#A1B9C7' ,
				'#334433' ,
				'#88aaaa' ,
				'#447799' ,
				'#bbeeff' ,
				'#EEEEEE' ,
				'#ECECEC' ,
				'#CCCCCC' ,
				'#003366' ,
				'#CCCC99' ,
				'#217C7E' ,
				'#9A3334' ,
				'#3399FF' ,
				'#F3EFE0' ,
			);
			
			shuffle( $flat_colors );
			
			$count = count( $flat_colors );
			if( $ColorSteps === FALSE ) return '#27A1CA';
			if( $ColorSteps == 0 ) return $flat_colors[ rand( 0 , $count - 1 ) ];
			
			return $flat_colors;
			
		}
	}
	
	/* Refuse Access for none Admin Users */
	if( ! function_exists( 'wpvr_refuse_access' ) ) {
		function wpvr_refuse_access( $a = FALSE ) {
			if( $a === FALSE ) {
				?>
				<div class = "wpvr_no_access" style = 'margin-top:50px;background: #fff;color: #444;font-family: "Open Sans", sans-serif;margin: 2em auto;padding: 1em 2em;max-width: 700px;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);box-shadow: 0 1px 3px rgba(0,0,0,0.13);'>
					<p>
					
					<h2> WP Video Robot </h2>
					<?php _e( 'You do not have sufficient permissions to access this page.' , WPVR_LANG ); ?>
					</p>
				</div>
				<?php
			} else {
				?>
				<div class = "wpvr_no_access error" style = 'margin-top:50px;background: #fff;color: #444;font-family: "Open Sans", sans-serif;margin: 2em auto;padding: 1em 2em;max-width: 700px;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);box-shadow: 0 1px 3px rgba(0,0,0,0.13);'>
					<h2> WP VIDEO ROBOT </h2>
					
					<p>
						<?php _e( 'Your copy licence is not activated.' , WPVR_LANG ); ?><br/>
						<?php _e( 'You cannot use WP VIDEO ROBOT.' , WPVR_LANG ); ?>
					
					</p>
				</div>
				<?php
			}
		}
	}
	
	/* Get customer update infos */
	if( ! function_exists( 'wpvr_get_customer_infos' ) ) {
		function wpvr_get_customer_infos() {
			global $wpvr_options;
			$customer_infos = array(
				'purchase_code'    => $wpvr_options[ 'purchaseCode' ] ,
				'site_name'        => get_bloginfo( 'name' ) ,
				'site_url'         => get_bloginfo( 'url' ) ,
				'site_description' => get_bloginfo( 'description' ) ,
				'site_language'    => ( is_rtl() ) ? 'RTL' : 'LTR' ,
				'admnin_email'     => get_bloginfo( 'admin_email' ) ,
				'wp_version'       => get_bloginfo( 'version' ) ,
				'wp_url'           => get_bloginfo( 'wpurl' ) ,
				'wp_rtl'           => is_rtl() ,
				'sources_stats'    => wpvr_sources_stats() ,
				'videos_stats'     => wpvr_videos_stats() ,
			);
			
			return ( base64_encode( json_encode( $customer_infos ) ) );
		}
	}
	
	/* Remove all tmp files from tmp directory */
	if( ! function_exists( 'wpvr_remove_tmp_files' ) ) {
		function wpvr_remove_tmp_files() {
			$dirHandle = opendir( WPVR_TMP_PATH );
			while( $file = readdir( $dirHandle ) ) {
				if( ! is_dir( $file ) )
					unlink( WPVR_TMP_PATH . "$file" );
			}
			closedir( $dirHandle );
		}
	}
	
	/* Make interval from two datetime */
	if( ! function_exists( 'wpvr_make_interval' ) ) {
		function wpvr_make_interval( $start , $end , $bool = TRUE ) {
			
			if( $start == '' || $end == '' ) return array();
			
			$workingHours = array();
			for ( $i = 0; $i < 24; $i ++ ) {
				if( strlen( $i ) == 1 ) $i = '0' . $i;
				$workingHours[ $i ] = ! $bool;
			}
			if( $start > $end ) {
				return wpvr_make_interval( $end , $start , ! $bool );
			} elseif( $start == $end ) {
				return array();
			} else {
				if( $start <= 12 && $end <= 12 ) {
					for ( $i = $start; $i <= $end; $i ++ ) {
						if( strlen( $i ) == 1 ) $i = '0' . $i;
						$workingHours[ $i ] = $bool;
					}
				} elseif( $start > 12 && $end > 12 ) {
					for ( $i = $start; $i <= $end; $i ++ ) {
						if( strlen( $i ) == 1 ) $i = '0' . $i;
						$workingHours[ $i ] = $bool;
					}
					
				} elseif( $start < 12 && $end > 12 ) {
					for ( $i = $start; $i < 12; $i ++ ) {
						if( strlen( $i ) == 1 ) $i = '0' . $i;
						$workingHours[ $i ] = $bool;
					}
					
					for ( $i = 12; $i <= $end; $i ++ ) {
						if( strlen( $i ) == 1 ) $i = '0' . $i;
						$workingHours[ $i ] = $bool;
					}
					
					$workingHours[ $start ] = $workingHours[ $end ] = TRUE;
					
				}
			}
			
			return $workingHours;
		}
	}
	
	/* Check if it is a working Hour */
	if( ! function_exists( 'wpvr_is_working_hour' ) ) {
		function wpvr_is_working_hour( $hour ) {
			global $wpvr_options;
			$wh = $wpvr_options[ 'wakeUpHours' ];
			
			if( $wh === FALSE ) return TRUE;
			
			$whA = $wpvr_options[ 'wakeUpHoursA' ];
			$whB = $wpvr_options[ 'wakeUpHoursB' ];
			
			$whArray = wpvr_make_interval( $whA , $whB , TRUE );
			if( isset( $whArray[ $hour ] ) ) return $whArray[ $hour ];
			else return array();
		}
	}
	
	/* Add WPVR Notice */
	if( ! function_exists( 'wpvr_render_notice' ) ) {
		function wpvr_render_notice( $notice = array() ) {
			global $current_user , $default_notice;
			$user_id = $current_user->ID;
			
			if( ! is_array( $notice ) ) {
				$notices = get_option( 'wpvr_notices' );
				$notice  = $notices[ $notice ];
			}
			
			$notice = wpvr_extend( $notice , $default_notice );
			
			if( $notice[ 'title' ] === FALSE ) $notice[ 'title' ] = '';
			elseif( $notice[ 'title' ] == '' ) $notice[ 'title' ] = 'WP VIDEO ROBOT';
			
			//d( $notice );
			
			if( isset( $notice[ 'single_line' ] ) && $notice[ 'single_line' ] === TRUE ) $line_break = '';
			else $line_break = '<br/>';
			$notice_style = $icon_style = "";
			
			if( isset( $notice[ 'color' ] ) && $notice[ 'color' ] != '' ) {
				$notice_style = ' border-color: ' . $notice[ 'color' ] . '; ';
				$icon_style   = ' color: ' . $notice[ 'color' ] . '; ';
			}
			
			if( isset( $notice[ 'icon' ] ) && $notice[ 'icon' ] != '' ) $icon = $notice[ 'icon' ];
			else $icon = '';
			
			if( $notice[ 'is_dialog' ] === TRUE ) {
				wpvr_render_dialog_notice( $notice );
				
				return TRUE;
			}
			
			/* Check that the user hasn't already clicked to ignore the message */
			if( ! get_user_meta( $user_id , $notice[ 'slug' ] ) ) {
				$hideLink = "?wpvr_hide_notice=" . $notice[ 'slug' ] . "";
				foreach ( $_GET as $key => $value ) {
					//d( $value );d( $key );
					if( is_string( $value ) && $key != 'wpvr_hide_notice' ) $hideLink .= "&$key=$value";
				}
				?>
				<div class = "error <?php echo $notice[ 'class' ]; ?> wpvr_wp_notice" style = "display:none; <?php echo $notice_style; ?>">
					<?php if( $icon != '' ) { ?>
						<div class = "pull-left wpvr_notice_icon" style = "<?php echo $icon_style; ?>">
							<i class = "fa <?php echo $icon; ?>"></i>
						</div>
					<?php } ?>
					<?php if( $notice[ 'hidable' ] ) { ?>
						<a class = "pull-right" href = "<?php echo $hideLink; ?>">
							<?php _e( 'Hide this notice' , WPVR_LANG ); ?>
						</a>
					<?php } ?>
					<div class = "wpvr_notice_content pull-left">
						<strong><?php echo $notice[ 'title' ]; ?></strong>
						<?php echo $line_break; ?>
						
						<div><?php echo $notice[ 'content' ]; ?></div>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<?php
			}
			
			if( isset( $notice[ 'show_once' ] ) && $notice[ 'show_once' ] === TRUE ) {
				wpvr_remove_notice( $notice[ 'slug' ] );
			}
			
		}
	}
	
	
	/* Add WPVR Notice */
	if( ! function_exists( 'wpvr_render_done_notice_redirect' ) ) {
		function wpvr_render_done_notice_redirect( $msg , $unique = TRUE ) {
			wpvr_add_notice( array(
				'title'     => 'WP Video Robot : ' ,
				'class'     => 'updated' , //updated or warning or error
				'content'   => $msg ,
				'hidable'   => FALSE ,
				'is_dialog' => FALSE ,
				'show_once' => TRUE ,
				'color'     => '#27A1CA' ,
				'icon'      => 'fa-check-circle' ,
			) );
		}
	}
	
	/* Add WPVR Notice */
	if( ! function_exists( 'wpvr_render_done_notice' ) ) {
		function wpvr_render_done_notice( $msg , $unique = TRUE ) {
			$error_notice_slug = wpvr_add_notice( array(
				'title'     => 'WP Video Robot : ' ,
				'class'     => 'updated' , //updated or warning or error
				'content'   => $msg ,
				'hidable'   => FALSE ,
				'is_dialog' => FALSE ,
				'show_once' => TRUE ,
				'color'     => '#27A1CA' ,
				'icon'      => 'fa-check-circle' ,
			) );
			wpvr_render_notice( $error_notice_slug );
			wpvr_remove_notice( $error_notice_slug );
		}
	}
	
	/* Add WPVR Notice */
	if( ! function_exists( 'wpvr_render_error_notice' ) ) {
		function wpvr_render_error_notice( $msg , $unique = TRUE ) {
			$error_notice_slug = wpvr_add_notice( array(
				'title'     => 'WP Video Robot ERROR :' ,
				'class'     => 'error' , //updated or warning or error
				'content'   => $msg ,
				'hidable'   => FALSE ,
				'is_dialog' => FALSE ,
				'show_once' => TRUE ,
				'color'     => '#E4503C' ,
				'icon'      => 'fa-exclamation-triangle' ,
			) );
			wpvr_render_notice( $error_notice_slug );
			wpvr_remove_notice( $error_notice_slug );
		}
	}
	
	/* Add WPVR Notice */
	if( ! function_exists( 'wpvr_add_notice' ) ) {
		function wpvr_add_notice( $notice = array() , $unique = TRUE , $multisite = FALSE ) {
			global $default_notice;
			if( ! $multisite ) {
				$notices = get_option( 'wpvr_notices' );
			} else {
				$notices = get_site_option( 'wpvr_notices' );
			}
			if( $notices == '' ) $notices = array();
			
			$notice           = wpvr_extend( $notice , $default_notice );
			$nowObj           = new Datetime();
			$notice[ 'date' ] = $nowObj->format( 'Y-m-d H:i:s' );
			if( $unique === TRUE ) $notices[ $notice[ 'slug' ] ] = $notice;
			else $notices[] = $notice;


			if( ! $multisite ) {
				update_option( 'wpvr_notices' , $notices );
			} else {
				update_site_option( 'wpvr_notices' , $notices );
			}
			//d( $notices );
			//return $notices;
			return $notice[ 'slug' ];
		}
	}
	
	/* TEsting if Count Videos has reached one of our levels */
	if( ! function_exists( 'wpvr_is_reaching_level' ) ) {
		function wpvr_is_reaching_level( $count ) {
			global $wpvr_rating_levels;
			foreach ( $wpvr_rating_levels as $level ) {
				if( $count >= $level ) return $level;
			}
			
			return FALSE;
		}
	}
	
	/* Add WPVR Notice */
	if( ! function_exists( 'wpvr_render_dialog_notice' ) ) {
		function wpvr_render_dialog_notice( $notice ) {
			//new dBug( $notice );
			global $current_user;
			$user_id = $current_user->ID;
			/* Check that the user hasn't already clicked to ignore the message */
			if( get_user_meta( $user_id , $notice[ 'slug' ] ) ) return FALSE;
			
			if( ! isset( $notice[ 'dialog_ok_url' ] ) ) $notice[ 'dialog_ok_url' ] = FALSE;
			if( $notice[ 'dialog_modal' ] === TRUE ) $isModal = 'true';
			else $isModal = 'false';
			
			?>
			<script type = "text/javascript">
				jQuery(document).ready(function ($) {
					
					setTimeout(function () {
						
						var noticeBoxArgs = {
							title: '<?php echo addslashes( $notice[ 'title' ] ); ?>',
							text: '<?php echo addslashes( $notice[ 'content' ] ); ?>',
							isModal: ( '<?php echo $isModal; ?>' === 'true' ),
							boxClass: 'noticeBox <?php echo $notice[ 'dialog_class' ]; ?>',
							<?php if( $notice[ 'dialog_ok_button' ] != FALSE ) { ?>
							pauseButton: '<?php echo addslashes( $notice[ 'dialog_ok_button' ] ); ?>',
							<?php } ?>
						};
						<?php if( $notice[ 'hidable' ] === TRUE ){ ?>
						noticeBoxArgs.cancelButton = '<?php echo addslashes( $notice[ 'dialog_hide_button' ] ); ?>';
						<?php } ?>
						var noticeBox = wpvr_show_loading(noticeBoxArgs);
						
						<?php if( $notice[ 'dialog_ok_url' ] === FALSE ) { ?>
						noticeBox.doPause(function () {
							noticeBox.remove();
						});
						<?php } else{ ?>
						noticeBox.doPause(function () {
							$('.wpvr_loading_cancel', noticeBox).attr('has_voted', 'yes').trigger('click');
							window.open('<?php echo $notice[ 'dialog_ok_url' ]; ?>', '_blank');
						});
						<?php } ?>
						
						<?php if( $notice[ 'hidable' ] === TRUE ){ ?>
						noticeBox.doCancel(function () {
							var btn = $('.wpvr_loading_cancel', noticeBox);
							var has_voted = btn.attr('has_voted');
							var btn_label = btn.html();
							$('i', btn).addClass('fa-spin');
							//btn.html( btn_label+' ....');
							$.ajax({
								type: "GET",
								url: '<?php echo WPVR_ACTIONS_URL; ?>',
								data: {
									wpvr_wpload: 1,
									dismiss_dialog_notice: 1,
									has_voted: has_voted,
									notice_slug: '<?php echo $notice[ 'slug' ]; ?>'
								},
								success: function (data) {
									//btn.html( btn_label);
									$('i', btn).removeClass('fa-spin');
									$json = wpvr_get_json(data);
									console.log($json);
									if ($json.status == '1' && $json.data == 'ok') noticeBox.remove();
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(thrownError);
								}
							});
						});
						<?php } ?>
						
						
					}, <?php echo $notice[ 'dialog_delay' ]; ?>);
					
				});
			</script>
			<?php
		}
	}
	
	/* Add WPVR Notice */
	if( ! function_exists( 'wpvr_remove_notice' ) ) {
		function wpvr_remove_notice( $notice_slug , $multisite = FALSE ) {
			$notices = $multisite ? get_site_option( 'wpvr_notices' ) : get_option( 'wpvr_notices' );
			if( $notices == '' ) $notices = array();
			foreach ( (array) $notices as $k => $notice ) {
				if( $notice[ 'slug' ] == $notice_slug ) {
					unset( $notices[ $k ] );
				}
			}

			if( $multisite ) {
				update_site_option( 'wpvr_notices' , $notices );
			} else {
				update_option( 'wpvr_notices' , $notices );
			}
			
			return $notices;
		}
	}
	
	/* Add WPVR Notice */
	if( ! function_exists( 'wpvr_remove_all_notices' ) ) {
		function wpvr_remove_all_notices() {
			update_option( 'wpvr_notices' , array() );
		}
	}
	
	/* Get Cats Recursively */
	if( ! function_exists( 'wpvr_rec_get_cats' ) ) {
		function wpvr_rec_get_cats( $hCats = array() , $parent_id = null , $level = 0 ) {
			global $wpvr_hierarchical_cats;
			$args = array(
				'type'         => array( WPVR_VIDEO_TYPE ) ,
				'child_of'     => 0 ,
				'parent'       => '' ,
				'orderby'      => 'name' ,
				'order'        => 'ASC' ,
				'hide_empty'   => 0 ,
				'hierarchical' => 0 ,
				'exclude'      => '' ,
				'include'      => '' ,
				'number'       => '' ,
				'taxonomy'     => 'category' ,
				'pad_counts'   => FALSE ,
			);
			if( $parent_id != null ) $args[ 'parent' ] = $parent_id;
			$items = get_categories( $args );
			$hCats = array();
			if( count( $items ) == 0 ) return $hCats;
			foreach ( $items as $item ) {
				$int_level = $level;
				if( $item->parent != 0 && $parent_id == null ) continue;
				$prefix = '';
				for ( $i = 0; $i < $level; $i ++ ) {
					$prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;';
				}
				$cat_item                 = array(
					'slug'  => $item->slug ,
					'label' => $prefix . $item->name . ' (' . $item->count . ') ' ,
					'value' => $item->term_id ,
					'count' => $item->count ,
					'level' => $level ,
				);
				$wpvr_hierarchical_cats[] = array(
					'label' => $cat_item[ 'label' ] ,
					'value' => $cat_item[ 'value' ] ,
				);
				$int_level ++;
				$hCats[ $item->term_id ] = array(
					'item' => $cat_item ,
					'subs' => wpvr_rec_get_cats( $hCats , $item->term_id , $int_level ) ,
				);
			}
			
			return $hCats;
		}
	}
	
	/* Get Hierarchical Array of Categories with Counts*/
	if( ! function_exists( 'wpvr_get_hierarchical_cats' ) ) {
		function wpvr_get_hierarchical_cats( $return_tree = FALSE ) {
			global $wpvr_hierarchical_cats;
			$tree_cats = wpvr_rec_get_cats();
			if( $return_tree ) return $tree_cats;
			else return $wpvr_hierarchical_cats;
		}
	}
	
	/* Get Taxonomy TErms array with count */
	if( ! function_exists( 'wpvr_get_taxonomy_terms' ) ) {
		function wpvr_get_taxonomy_terms( $taxonomy ) {
			$terms      = get_terms( $taxonomy , array(
				'orderby'    => 'name' ,
				'hide_empty' => FALSE ,
			) );
			$termsArray = array();
			foreach ( $terms as $term ) {
				$termsArray[ $term->term_id ] = $term->name . ' (' . $term->count . ') ';
			}
			
			return $termsArray;
		}
	}
	
	/* Check for performance security condition */
	if( ! function_exists( 'wpvr_max_fetched_videos_per_run' ) ) {
		function wpvr_max_fetched_videos_per_run() {
			global $wpvr_options;
			
			$sources = wpvr_get_sources( array( 'status' => 'on' ) );
			$sources = wpvr_multiplicate_sources( $sources );
			$data    = array();
			//new dBug( $sources );
			
			foreach ( $sources as $source ) {
				if( ! isset( $data[ $source->id ] ) )
					$data[ $source->id ] = array(
						'source_name'   => $source->name ,
						'wanted_videos' => 0 ,
						'sub_sources'   => 0 ,
						'warning'       => FALSE ,
					);
				$wantedVideos = ( $source->wantedVideosBool == 'default' ) ? $wpvr_options[ 'wantedVideos' ] : $source->wantedVideos;
				$data[ $source->id ][ 'wanted_videos' ] += $wantedVideos;
				$data[ $source->id ][ 'sub_sources' ] ++;
				
				if( $data[ $source->id ][ 'wanted_videos' ] > WPVR_SECURITY_WANTED_VIDEOS ) $data[ $source->id ][ 'warning' ] = TRUE;
				
			}
			
			return $data;
		}
	}
	
	
	/* Download Thumbnail from URL */
	if( ! function_exists( 'wpvr_download_featured_image' ) ) {
		function wpvr_download_featured_image( $image_url = '' , $image_title = '' , $image_desc = '' , $post_id = '' , $unique_id = '' ) {

			if( WPVR_DISABLE_THUMBS_DOWNLOAD === TRUE ) return '';

			if( $image_url == '' ) return FALSE;
			if( $unique_id == '' ) $unique_id = md5( uniqid( rand() , TRUE ) );
			
			$upload_dir     = wp_upload_dir(); // Set upload folder
			$image_data
			                =  // Get image data
			$file_extension = pathinfo( $image_url , PATHINFO_EXTENSION );
			$fe             = explode( '?' , $file_extension );
			$file_extension = $fe[ 0 ];
			if( $file_extension == '' || $file_extension == null ) $file_extension = 'jpg';
			$filename = sanitize_file_name( $image_title );
			if( preg_match( '/[^\x20-\x7f]/' , $filename ) ) $filename = md5( $filename );
			$filename_ext = $filename . '.' . $file_extension;
			//ppg_set_debug( $filename_ext , TRUE);
			
			//if( ! file_exists( $filename ) ) {
			if( wp_mkdir_p( $upload_dir[ 'path' ] ) ) $file = $upload_dir[ 'path' ] . '/' . $filename_ext;
			else $file = $upload_dir[ 'basedir' ] . '/' . $filename_ext;
			@file_put_contents( $file , @file_get_contents( $image_url ) );
			
			$wp_filetype = @wp_check_filetype( $filename_ext , null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype[ 'type' ] ,
				'post_title'     => $filename . "-attachment" ,
				'post_name'      => sanitize_title( $image_title . "-attachment" ) ,
				'post_content'   => $image_desc ,
				'post_excerpt'   => $filename ,
				'post_status'    => 'inherit' ,
			);
			if( $post_id != '' ) {
				$attach_id = @wp_insert_attachment( $attachment , $file , $post_id );
				update_post_meta( $attach_id , '_wp_attachment_image_alt' , $filename );
				@require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = @wp_generate_attachment_metadata( $attach_id , $file );
				@wp_update_attachment_metadata( $attach_id , $attach_data );
				@set_post_thumbnail( $post_id , $attach_id );
			} else {
				$attach_id = @wp_insert_attachment( $attachment , $file );
				update_post_meta( $attach_id , '_wp_attachment_image_alt' , $filename );
				@require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = @wp_generate_attachment_metadata( $attach_id , $file );
				@wp_update_attachment_metadata( $attach_id , $attach_data );
			}
			
			//wpvr_set_debug( $file );
			
			return $file;
			
		}
	}
	
	if( ! function_exists( 'wpvr_render_add_unwanted_button' ) ) {
		function wpvr_render_add_unwanted_button( $post_id ) {
			global $wpvr_unwanted_ids , $wpvr_unwanted;
			$video_id      = get_post_meta( $post_id , 'wpvr_video_id' , TRUE );
			$video_service = get_post_meta( $post_id , 'wpvr_video_service' , TRUE );
			//d( $wpvr_unwanted_ids );
			//d( $wpvr_unwanted_ids[$video_service] );
			if( $video_id == '' || $post_id == '' ) return '';
			if( isset( $wpvr_unwanted_ids[ $video_service ][ $video_id ] ) ) {
				$action = 'remove';
				$icon   = 'fa-undo';
				$label  = __( 'Remove from Unwanted' , WPVR_LANG );
				$class  = "wpvr_black_button";
			} else {
				$action = 'add';
				$icon   = 'fa-ban';
				$label  = __( 'Add to Unwanted' , WPVR_LANG );
				$class  = "wpvr_red_button";
				
			}
			
			$unwanted_button
				= '

				<button
					url = "' . WPVR_ACTIONS_URL . '"
					class=" ' . $class . ' wpvr_button wpvr_full_width wpvr_single_unwanted"
					post_id="' . $post_id . '"
					action="' . $action . '"
				>
					<i class="fa ' . $icon . '" iclass="' . $icon . '"></i>
					<span>' . $label . '</span>
				</button>
			';
			
			return $unwanted_button;
		}
	}
	
	if( ! function_exists( 'wpvr_async_balance_items' ) ) {
		function wpvr_async_balance_items( $items , $buffer ) {
			$k        = $j = 0;
			$balanced = array( 0 => array() , );
			foreach ( (array ) $items as $item_id => $item ) {
				if( $k >= $buffer ) {
					$k = 0;
					$j ++;
					$balanced[ $j ] = array();
				}
				
				$balanced[ $j ][ $item_id ] = $item;
				$k ++;
			}
			
			return $balanced;
		}
	}
	
	if( ! function_exists( 'wpvr_get_cron_url' ) ) {
		function wpvr_get_cron_url( $query = '' ) {
			global $wpvr_cron_token;
			
			return get_site_url( null , '/' . WPVR_CRON_ENDPOINT . '/' . $wpvr_cron_token . '/' . $query );
		}
	}

	if( ! function_exists( 'wpvr_render_copy_button' ) ) {
		function wpvr_render_copy_button( $target ) {

			?>
			<button
				class = "wpvr_copy_btn wpvr_button wpvr_black_button pull-right"
				data-clipboard-target = "#<?php echo $target; ?>"
				done = ""
			>
				<i class = "wpvr_green fa fa-check"></i>
				<i class = "wpvr_black fa fa-copy"></i>
				<span class = "wpvr_black"><?php echo __( 'COPY' , WPVR_LANG ); ?></span>
				<span class = "wpvr_green"><?php echo __( 'COPIED !' , WPVR_LANG ); ?></span>
			</button>
			<?php


		}
	}