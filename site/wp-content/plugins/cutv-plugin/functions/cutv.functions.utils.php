<?php
	
	if( ! function_exists( 'cutv_retreive_video_id_from_param' ) ) {
		function cutv_retreive_video_id_from_param( $param , $service ) {
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
	
	if( ! function_exists( 'cutv_get_system_info' ) ) {
		function cutv_get_system_info() {
			$php_version = explode( '+' , PHP_VERSION );
			$infos       = array(
				'server'             => array(
					'label'  => __( 'Server Software' , CUTV_LANG ) ,
					'value'  => '<br/>' . $_SERVER[ 'SERVER_SOFTWARE' ] ,
					'status' => '' ,
				) ,
				'php_version'        => array(
					'label'  => __( 'PHP Version' , CUTV_LANG ) ,
					'value'  => $php_version[ 0 ] ,
					'status' => version_compare( PHP_VERSION , CUTV_REQUIRED_PHP_VERSION , '>=' ) ? 'good' : 'bad' ,
				) ,
				'memory_limit'       => array(
					'label'  => __( 'PHP Memory Limit' , CUTV_LANG ) ,
					'value'  => ini_get( 'memory_limit' ) ,
					'status' => '' ,
				) ,
				'post_max_size'      => array(
					'label'  => __( 'Post Max Size' , CUTV_LANG ) ,
					'value'  => ini_get( 'post_max_size' ) ,
					'status' => '' ,
				) ,
				'max_input_time '    => array(
					'label'  => __( 'Maximum Input Time' , CUTV_LANG ) ,
					'value'  => ini_get( 'max_input_time' ) ,
					'status' => '' ,
				) ,
				'max_execution_time' => array(
					'label'  => __( 'Maximum Execution Time' , CUTV_LANG ) ,
					'value'  => ini_get( 'max_execution_time' ) ,
					'status' => '' ,
				) ,
				'safe_mode'          => array(
					'label'  => __( 'PHP Safe Mode' , CUTV_LANG ) ,
					'value'  => ini_get( 'safe_mode' ) ? 'ON' : 'OFF' ,
					'status' => ini_get( 'safe_mode' ) ? 'bad' : 'good' ,
				) ,
				'cURL_status'        => array(
					'label'  => __( 'cURL Status' , CUTV_LANG ) ,
					'value'  => function_exists( 'curl_version' ) ? 'ON' : 'OFF' ,
					'status' => function_exists( 'curl_version' ) ? 'good' : 'bad' ,
				) ,
				'allow_url_fopen'    => array(
					'label'  => __( 'Allow URL Fopen' , CUTV_LANG ) ,
					'value'  => ini_get( 'allow_url_fopen' ) == '1' ? 'ON' : 'OFF' ,
					'status' => ini_get( 'allow_url_fopen' ) == '1' ? 'good' : 'bad' ,
				) ,
				'openssl_status'     => array(
					'label'  => __( 'OpenSSL Extension' , CUTV_LANG ) ,
					'value'  => extension_loaded( 'openssl' ) ? 'ON' : 'OFF' ,
					'status' => extension_loaded( 'openssl' ) ? 'good' : 'bad' ,
				) ,
				'folder_writable'    => array(
					'label'  => __( 'Plugin Folder Writable' , CUTV_LANG ) ,
					'value'  => ( is_writable( CUTV_PATH ) === TRUE ) ? 'ON' : 'OFF' ,
					'status' => ( is_writable( CUTV_PATH ) === TRUE ) ? 'good' : 'bad' ,
				) ,
			
			);
			
			$act  = cutv_get_act_data( 'cutv' );
			$cutv = array(
				
				'cutv_url' => array(
					'label'  => __( 'Website URL' , CUTV_LANG ) ,
					'value'  => CUTV_SITE_URL ,
					'status' => '' ,
				) ,
				
				'cutv_version' => array(
					'label'  => __( 'CUTV Version' , CUTV_LANG ) ,
					'value'  => CUTV_VERSION ,
					'status' => '' ,
				) ,
				
				'cutv_act_status' => array(
					'label'  => __( 'CUTV Activation Status' , CUTV_LANG ) ,
					'value'  => $act[ 'act_status' ] ,
					'status' => '' ,
				) ,
				
				'cutv_act_code' => array(
					'label'  => __( 'CUTV Activation Code' , CUTV_LANG ) ,
					'value'  => $act[ 'act_code' ] ,
					'status' => '' ,
				) ,
				
				'cutv_act_date' => array(
					'label'  => __( 'CUTV Activation Date' , CUTV_LANG ) ,
					'value'  => $act[ 'act_date' ] ,
					'status' => '' ,
				) ,
				
				'cutv_act_id' => array(
					'label'  => __( 'CUTV Activation ID' , CUTV_LANG ) ,
					'value'  => $act[ 'act_id' ] ,
					'status' => '' ,
				) ,
			
			);
			
			return array(
				'sys'  => $infos ,
				'cutv' => $cutv ,
			);
			
		}
	}
	
	if( ! function_exists( 'cutv_render_system_info' ) ) {
		function cutv_render_system_info( $info_blocks ) {
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
	
	if( ! function_exists( 'cutv_get_service_labels' ) ) {
		function cutv_get_service_labels( $data ) {
			global $cutv_vs;
			if(
				! isset( $data[ 'sourceService' ] )
				|| ! isset( $cutv_vs[ $data[ 'sourceService' ] ] )
				|| ! isset( $cutv_vs[ $data[ 'sourceService' ] ][ 'types' ][ $data[ 'sourceType' ] ] )
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
				'service'       => $cutv_vs[ $data[ 'sourceService' ] ][ 'id' ] ,
				'service_label' => $cutv_vs[ $data[ 'sourceService' ] ][ 'label' ] ,
				'type'          => $cutv_vs[ $data[ 'sourceService' ] ][ 'types' ][ $data[ 'sourceType' ] ][ 'id' ] ,
				'type_label'    => $cutv_vs[ $data[ 'sourceService' ] ][ 'types' ][ $data[ 'sourceType' ] ][ 'label' ] ,
				'type_HTML'     => cutv_render_vs_source_type(
					$cutv_vs[ $data[ 'sourceService' ] ][ 'types' ][ $data[ 'sourceType' ] ] ,
					$cutv_vs[ $data[ 'sourceService' ] ]
				) ,
			);
		}
	}
	
	if( ! function_exists( 'cutv_utf8_converter' ) ) {
		function cutv_utf8_converter( $array ) {
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
					class = "cutv_source_insights_item pull-left <?php echo $class; ?>"
					title = "<?php echo $insight[ 'title' ]; ?>"
				>
				<span class = "cutv_source_insights_item_icon">
					<i class = "fa <?php echo $insight[ 'icon' ]; ?>"></i>
				</span>
				<span class = "cutv_source_insights_item_value">
					<?php echo $insight[ 'value' ]; ?>
				</span>
				</div>
			<?php } ?>
			<div class = "cutv_clearfix"></div>
			
			<?php
		}
	}
	
	if( ! function_exists( 'cutv_d' ) ) {
		function cutv_d( $debug_response , $separator = FALSE ) {
			ob_start();
			d( $debug_response );
			$output = ob_get_clean();
			
			return $separator . $output . $separator;
		}
	}
	
	if( ! function_exists( 'cutv_is_theme' ) ) {
		function cutv_is_theme( $name ) {
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
	
	if( ! function_exists( 'cutv_object_to_array' ) ) {
		function cutv_object_to_array( $obj ) {
			if( is_object( $obj ) ) $obj = (array) $obj;
			if( is_array( $obj ) ) {
				$new = array();
				foreach ( $obj as $key => $val ) {
					$new[ $key ] = cutv_object_to_array( $val );
				}
			} else $new = $obj;
			
			return $new;
		}
	}
	
	if( ! function_exists( 'cutv_chrono_time' ) ) {
		function cutv_chrono_time( $start = FALSE , $round = 6 ) {
			$time = explode( ' ' , microtime() );
			if( $start === FALSE ) return $time[ 0 ] + $time[ 1 ];
			else {
				return round( cutv_chrono_time() - $start , $round );
			}
			
			return TRUE;
		}
	}
	
	if( ! function_exists( 'cutv_render_multiselect' ) ) {
		function cutv_render_multiselect( $option , $value = null , $echo = TRUE ) {
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
					$cats = cutv_get_categories_count();
					foreach ( $cats as $cat ) {
						$option[ 'values' ][ $cat[ 'value' ] ] = $cat[ 'label' ] . ' (' . $cat[ 'count' ] . ')';
					}
					
				} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'all_categories' ) {
					
					// GET ALL CATEGORIES
					$cats = cutv_get_categories_count( FALSE , TRUE );
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
						CUTV_VIDEO_TYPE ,
						'attachment' ,
						'revision' ,
						CUTV_SOURCE_TYPE ,
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
					global $cutv_vs;
					foreach ( $cutv_vs as $vs ) {
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
			<div class = "cutv_select_wrap">
				<input type = "hidden" value = "0" name = "<?php echo $option_name; ?>[]"/>
				<select
					class = "cutv_field_selectize "
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
	if( ! function_exists( 'cutv_get_folders_simple' ) ) {
		function cutv_get_folders_simple() {
			$terms   = get_terms( CUTV_SFOLDER_TYPE , array( 'hide_empty' => FALSE , ) );
			$folders = array();
			foreach ( $terms as $term ) {
				$folders[ $term->term_id ] = $term->name . ' (' . $term->count . ') ';
			}
			
			return $folders;
		}
	}
	
	/* CHECKS IF A REMOTE FILE EXISTS */
	if( ! function_exists( 'cutv_curl_check_remote_file_exists' ) ) {
		function cutv_curl_check_remote_file_exists( $url ) {
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
	if( ! function_exists( 'cutv_human_time_diff' ) ) {
		function cutv_human_time_diff( $post_id ) {
			$post          = get_post( $post_id );
			$now_date_obj  = DateTime::createFromFormat( 'Y-m-d H:i:s' , current_time( 'Y-m-d H:i:s' ) );
			$now_date      = $now_date_obj->format( 'U' );
			$post_date_obj = DateTime::createFromFormat( 'Y-m-d H:i:s' , $post->post_date );
			$post_date     = $post_date_obj->format( 'U' );
			
			return human_time_diff( $post_date , $now_date ) . ' ago';
		}
	}
	
	/* GETTING ADD DATA FROM URL */
	if( ! function_exists( 'cutv_extract_data_from_url' ) ) {
		function cutv_extract_data_from_url( $html , $searches = array() ) {
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
	if( ! function_exists( 'cutv_set_debug' ) ) {
		function cutv_set_debug( $var = null , $append = FALSE ) {
			
			$new = get_option( 'cutv_debug' );
			if( ! is_array( $new ) ) $new = array();
			if( $append === FALSE ) $new = array( $var );
			else $new[] = $var;
			
			update_option( 'cutv_debug' , $new );
		}
	}
	
	/* ShOW UP DEBUG VALUES */
	if( ! function_exists( 'cutv_get_debug' ) ) {
		function cutv_get_debug( $var = null ) {
			
			$cutv_debug = get_option( 'cutv_debug' );
			d( $cutv_debug );
		}
	}
	
	/* EMPTY DEBUG VALUES */
	if( ! function_exists( 'cutv_reset_debug' ) ) {
		function cutv_reset_debug() { update_option( 'cutv_debug' , array() ); }
	}
	
	/* MAKE CURL REQUEST */
	if( ! function_exists( 'cutv_make_curl_request' ) ) {
		function cutv_make_curl_request( $api_url = '' , $api_args = array() , $curl_object = null , $debug = FALSE , $curl_options = array() , $get_headers = FALSE ) {
			
			$timer = cutv_chrono_time();
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
				'exec_time' => cutv_chrono_time( $timer ) ,
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
	if( ! function_exists( 'cutv_get_json_response' ) ) {
		function cutv_get_json_response( $data , $response_status = 1 , $response_msg = '' , $response_count = 0 ) {
			$response         = array(
				'status' => $response_status ,
				'msg'    => $response_msg ,
				'count'  => $response_count ,
				'data'   => $data ,
			);
			$encoded_response = CUTV_JS . json_encode( $response ) . CUTV_JS;
			
			return $encoded_response;
		}
	}
	
	/* Render HTML attributes from PHP array*/
	if( ! function_exists( 'cutv_render_html_attributes' ) ) {
		function cutv_render_html_attributes( $attr = array() ) {
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
	if( ! function_exists( 'cutv_update_dynamic_video_views' ) ) {
		function cutv_update_dynamic_video_views( $post_id , $new_views ) {
			$cutv_fillers = get_option( 'cutv_fillers' );
			$count        = 0;
			if( ! is_array( $cutv_fillers ) || count( $cutv_fillers ) == 0 ) return 0;
			foreach ( $cutv_fillers as $filler ) {
				if( $filler[ 'from' ] == 'cutv_dynamic_views' ) {
					update_post_meta( $post_id , $filler[ 'to' ] , $new_views );
					$count ++;
				}
			}
			
			return $count;
		}
	}
	
	/* Render NOt Found */
	if( ! function_exists( 'cutv_render_video_permalink' ) ) {
		function cutv_render_video_permalink( $post = null , $permalink_structure = null ) {
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
			$permalink  = CUTV_SITE_URL . str_replace( $var_names , $var_values , $permalink_structure );
			
			return $permalink;
			
		}
	}
	
	/* Render NOt Found */
	if( ! function_exists( 'cutv_render_not_found' ) ) {
		function cutv_render_not_found( $msg = '' ) {
			?>
			
			<div class = "cutv_not_found">
				<i class = "fa fa-frown-o"></i><br/>
				<?php echo $msg; ?>
			</div>
			
			<?php
		}
	}
	
	/* Render buttons of Source Screen */
	if( ! function_exists( 'cutv_render_source_actions' ) ) {
		function cutv_render_source_actions( $post_id = '' ) {
			$o = array( 'test' => '' , 'run' => '' , 'save' => '' , 'trash' => '' , 'clone' => '' );
			
			$o[ 'save' ] .= '<br/><button id="cutv_save_source_btn" class="wpvr_wide_button actionBtn cutv_submit_button cutv_black_button">';
			$o[ 'save' ] .= '<i class="wpvr_button_icon fa fa-save"></i>';
			$o[ 'save' ] .= '<span>' . __( 'Save Source' , CUTV_LANG ) . '</span>';
			$o[ 'save' ] .= '</button><br/>';
			
			
			if( $post_id == '' ) {
				$o[ 'test' ] = '<div class="wpvr_no_actions">' . __( 'Start by saving your source' , CUTV_LANG ) . '</div>';
				
				return $o;
			}
			
			$testLink  = admin_url( 'admin.php?page=cutv&test_sources&ids=' . $post_id , 'http' );
			$runLink   = admin_url( 'admin.php?page=cutv&run_sources&ids=' . $post_id , 'http' );
			$cloneLink = admin_url( 'admin.php?page=cutv&clone_source=' . $post_id , 'http' );
			$trashLink = cutv_get_post_links( $post_id , 'trash' );
			
			$o[ 'test' ] .= '<button ready="1" url="' . $testLink . '" id="cutv_metabox_test" class="actionBtn cutv_submit_button cutv_metabox_button test">';
			$o[ 'test' ] .= '<i class="wpvr_button_icon fa fa-eye"></i>';
			$o[ 'test' ] .= '<span>' . __( 'Test Source' , CUTV_LANG ) . '</span>';
			$o[ 'test' ] .= '</button><br/>';
			
			$o[ 'run' ] .= '<button ready="1" url="' . $runLink . '" id="cutv_metabox_run" class="actionBtn cutv_submit_button cutv_metabox_button run">';
			$o[ 'run' ] .= '<i class="wpvr_button_icon fa fa-bolt"></i>';
			$o[ 'run' ] .= '<span>' . __( 'Run Source' , CUTV_LANG ) . '</span>';
			$o[ 'run' ] .= '</button><br/>';
			
			$o[ 'clone' ] .= '<button url="' . $cloneLink . '" id="cutv_metabox_clone" class="actionBtn cutv_submit_button cutv_metabox_button clone">';
			$o[ 'clone' ] .= '<i class="wpvr_button_icon fa fa-copy"></i>';
			$o[ 'clone' ] .= '<span>' . __( 'Clone Source' , CUTV_LANG ) . '</span>';
			$o[ 'clone' ] .= '</button><br/>';
			
			
			$o[ 'trash' ] .= '<button url="' . $trashLink
			                 . '" id="cutv_trash_source_btn" class="wpvr_wide_button actionBtn cutv_submit_button cutv_red_button cutv_metabox_button trash sameWindow">';
			$o[ 'trash' ] .= '<i class="wpvr_button_icon fa fa-trash-o"></i>';
			$o[ 'trash' ] .= '<span>' . __( 'Trash Source' , CUTV_LANG ) . '</span>';
			$o[ 'trash' ] .= '</button><br/>';
			
			
			return $o;
		}
	}
	
	/* Get taxonomies data from ids */
	if( ! function_exists( 'cutv_get_tax_data' ) ) {
		function cutv_get_tax_data( $taxonomy , $ids ) {
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
	if( ! function_exists( 'cutv_show_available_update_message' ) ) {
		function cutv_show_available_update_message() {
			global $cutv_new_version_available , $cutv_new_version_msg;
			?>
			<div class = "updated">
				<p>
					<strong>WP Video Robot</strong><br/>
					<?php _e( 'There is a new update available !' , CUTV_LANG ); ?> (<strong> Version <?php echo $cutv_new_version_available; ?></strong>)
					
					<?php if( ! empty( $cutv_new_version_msg ) ) { ?>
						<br/><br/><?php echo $cutv_new_version_msg; ?>
					<?php } ?>
					
					<?php
						$link = CUTV_SITE_URL . "/wp-admin/plugin-install.php?tab=plugin-information&plugin=" . CUTV_LANG . "&section=changelog&TB_iframe=true&width=640&height=662";
						echo '<br/><br/><a href="' . $link . '" > UPDATE NOW </a>';
					?>
				
				</p>
			</div>
			<?php
		}
	}
	
	/*Draw Stress Graph for selected day */
	if( ! function_exists( 'cutv_draw_stress_graph_by_day' ) ) {
		function cutv_draw_stress_graph_by_day( $date , $hex_color ) {
			
			$stress_data = cutv_get_schedule_stress( $date->format( 'Y-m-d' ) );
			
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
			
			$graph_id = 'cutv_chart_stress_graph-' . rand( 100 , 10000 );
			
			
			?>
			<!-- DAY STRESS GRAPH -->
			<div id = "" class = "postbox ">
				<h3 class = "hndle"><span> <?php echo __( 'Stress Forecast for :' , CUTV_LANG ) . ' ' . $date->format( 'l d F Y' ); ?> </span></h3>
				
				<div class = " inside">
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
				</div>
			</div>
			<?php
		}
	}
	
	if( ! function_exists( 'cutv_async_draw_stress_graph_by_day' ) ) {
		function cutv_async_draw_stress_graph_by_day( $date , $hex_color ) {
			$chart_id = 'cutv_chart_stress_graph_' . rand( 0 , 1000000 );
			?>
			<!-- DAY STRESS GRAPH -->
			<div
				class = "cutv_async_graph postbox"
				day = "<?php echo strtolower( $date->format( 'l' ) ); ?>"
				daylabel = "<?php echo( $date->format( 'Y-m-d' ) ); ?>"
				daytime = "<?php echo( $date->format( 'c' ) ); ?>"
				hex_color = "<?php echo $hex_color; ?>"
				url = "<?php echo CUTV_ACTIONS_URL; ?>"
				chart_id = "<?php echo $chart_id; ?>"
			>
				<h3 class = "hndle">
					<span>
						<?php echo ucfirst( $date->format( 'l' ) ) . ' ' . __( 'Stress Forecast' , CUTV_LANG ); ?>
					</span>
				</h3>
				
				<div class = " inside">
					<div class = "cutv_insite_loading">
						<i class = "fa fa-refresh fa-spin"></i>
						<span>Please Wait ... </span>
					</div>
					<div class = "cutv_graph_wrapper" style = "display:none;width:100% !important; height:400px !important;">
						<canvas id = "<?php echo $chart_id; ?>" width = "900" height = "400"></canvas>
					</div>
				</div>
			</div>
			<?php
		}
	}
	
	/* Generate stress schedule array */
	if( ! function_exists( 'cutv_async_get_schedule_stress' ) ) {
		function cutv_async_get_schedule_stress( $date = '' ) {
			$stress_data = FALSE;
			$stress_data = apply_filters( 'cutv_extend_schedule_stress' , $stress_data , $date );
			
			return $stress_data;
		}
	}
	
	if( ! function_exists( 'cutv_get_schedule_stress' ) ) {
		function cutv_get_schedule_stress( $day = '' ) {
			global $cutv_options , $cutv_stress , $cutv_days;
			//new dBug( $cutv_days );
			
			if( $day == '' ) $day_name = $cutv_days[ strtolower( date( 'N' ) ) ];
			else {
				$day_num  = strtolower( date( 'N' , strtotime( $day ) ) );
				$day_name = $cutv_days[ $day_num ];
			}
			
			//new dBug( $day_name );
			
			$stress_per_hour = array(
				'00H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'01H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'02H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'03H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'04H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'05H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'06H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'07H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'08H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'09H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'10H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'11H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'12H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'13H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'14H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'15H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'16H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'17H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'18H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'19H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'20H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'21H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'22H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
				'23H00' => array( 'max' => $cutv_stress[ 'max' ] , 'stress' => 0 , 'count' => 0 , 'wanted' => 0 , 'sources' => array() , ) ,
			);
			$sources         = cutv_get_sources( array(
				'status' => 'on' ,
			) );
			$sources         = cutv_multiplicate_sources( $sources );
			foreach ( $sources as $source ) {
				//new dBug($source);
				
				//d( $source );
				
				$wantedVideos  = ( $source->wantedVideosBool == 'default' ) ? $cutv_options[ 'wantedVideos' ] : $source->wantedVideos;
				$getTags       = ( $source->getVideoTags == 'default' ) ? $cutv_options[ 'getTags' ] : ( ( $source->getVideoTags == 'on' ) ? TRUE : FALSE );
				$getStats      = ( $source->getVideoStats == 'default' ) ? $cutv_options[ 'getStats' ] : ( ( $source->getVideoStats == 'on' ) ? TRUE : FALSE );
				$onlyNewVideos = ( $source->onlyNewVideos == 'default' ) ? $cutv_options[ 'onlyNewVideos' ] : ( ( $source->onlyNewVideos == 'on' ) ? TRUE : FALSE );
				
				$source_stress = 0;
				if( $getTags ) $source_stress += $wantedVideos * $cutv_stress[ 'getTags' ];
				if( $getStats ) $source_stress += $wantedVideos * $cutv_stress[ 'getStats' ];
				if( $onlyNewVideos ) $source_stress += $wantedVideos * $cutv_stress[ 'onlyNewVideos' ];
				$source_stress = $source_stress * $cutv_stress[ 'wantedVideos' ] * $cutv_stress[ 'base' ];
				
				if( $source->schedule == 'hourly' ) {
					foreach ( $stress_per_hour as $hour => $value ) {
						$myhour    = explode( 'H' , $hour );
						$isWorking = cutv_is_working_hour( $myhour[ 0 ] );
						
						if( $isWorking ) {
							$stress_per_hour[ $hour ][ 'stress' ] += $source_stress;
							$stress_per_hour[ $hour ][ 'count' ] ++;
							$stress_per_hour[ $hour ][ 'wanted' ] += $wantedVideos;
							$stress_per_hour[ $hour ][ 'sources' ][] = $source;
						}
					}
				} elseif( $source->schedule == 'daily' ) {
					$myhour    = explode( 'H' , $source->scheduleTime );
					$isWorking = cutv_is_working_hour( $myhour[ 0 ] );
					
					if( $isWorking ) {
						$stress_per_hour[ $source->scheduleTime ][ 'stress' ] += $source_stress;
						$stress_per_hour[ $source->scheduleTime ][ 'count' ] ++;
						$stress_per_hour[ $source->scheduleTime ][ 'wanted' ] += $wantedVideos;
						$stress_per_hour[ $source->scheduleTime ][ 'sources' ][] = $source;
					}
				} elseif( $source->schedule == 'weekly' ) {
					
					if( $day_name == $source->scheduleDay ) {
						
						$myhour    = explode( 'H' , $source->scheduleTime );
						$isWorking = cutv_is_working_hour( $myhour[ 0 ] );
						
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
	if( ! function_exists( 'cutv_capi_init' ) ) {
		function cutv_capi_init() {
			if( isset( $_GET[ 'capi' ] ) ) {
				if( isset( $_POST[ 'action' ] ) ) {
					cutv_capi_do( $_POST[ 'action' ] , $_POST );
				} else echo "SILENCE IS GOLDEN.";
				exit;
			}
		}
	}
	
	/* Do cAPI */
	if( ! function_exists( 'cutv_capi_do' ) ) {
		function cutv_capi_do( $action , $_post ) {
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
				$slug   = cutv_add_notice( $notice );
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
				
				$act = cutv_get_activation( $_post[ 'slug' ] );
				
				echo json_encode( array(
					'status' => $act[ 'act_status' ] ,
					'msg'    => 'Activation returned.' ,
					'data'   => $act ,
				) );
				
				return FALSE;
			}
			
			if( $action == 'reset_activation' ) {
				
				cutv_set_activation( $_post[ 'slug' ] , array() );
				echo json_encode( array(
					'status' => 1 ,
					'msg'    => 'Reset Completed.' ,
					'data'   => null ,
				) );
				
				return FALSE;
			}
			
			if( $action == 'reload_addons' ) {
				update_option( 'cutv_addons_list' , '' );
				$r[ 'status' ] = TRUE;
				$r[ 'msg' ]    = 'ADDONS LIST RESET ...';
				echo json_encode( $r );
				
				return FALSE;
			}
			
		}
	}
	
	/*Get Act data even empty */
	if( ! function_exists( 'cutv_can_show_menu_links' ) ) {
		function cutv_can_show_menu_links( $user_id = '' ) {
			global $cutv_options , $user_ID;
			
			if( $user_id == '' ) $user_id = $user_ID;
			$user       = new WP_User( $user_id );
			$user_roles = $user->roles;
			
			$super_roles = array( 'administrator' , 'superadmin' );
			foreach ( $user_roles as $role ) {
				if( in_array( $role , $super_roles ) ) return TRUE;
			}
			
			//d( $user_roles );
			
			
			if( $cutv_options[ 'showMenuFor' ] == null ) return FALSE;
			foreach ( $cutv_options[ 'showMenuFor' ] as $role ) {
				if( in_array( $role , $user_roles ) ) return TRUE;
			}
			
			return FALSE;
		}
	}
	
	/*Get Act data even empty */
	
	if( ! function_exists( 'cutv_get_act_data' ) ) {
		function cutv_get_act_data( $slug = 'cutv' ) {
			global $cutv_empty_activation;
			$cutv_acts = get_option( 'cutv_activations' );
			if( ! array( $cutv_acts ) ) $cutv_acts = array();
			if( ! isset( $cutv_acts[ $slug ] ) ) $cutv_acts[ $slug ] = $cutv_empty_activation;
			
			if( ! isset( $cutv_acts[ $slug ][ 'buy_expires' ] ) ) {
				$now                                 = new Datetime();
				$cutv_acts[ $slug ][ 'buy_expires' ] = $now->format( 'Y-m-d H:i:s' );
			}
			
			if( $cutv_acts[ $slug ] != '' ) {
				return array(
					'act_status'  => $cutv_acts[ $slug ][ 'act_status' ] ,
					'act_id'      => $cutv_acts[ $slug ][ 'act_id' ] ,
					'act_email'   => $cutv_acts[ $slug ][ 'act_email' ] ,
					'act_code'    => $cutv_acts[ $slug ][ 'act_code' ] ,
					'act_date'    => $cutv_acts[ $slug ][ 'act_date' ] ,
					'buy_date'    => $cutv_acts[ $slug ][ 'buy_date' ] ,
					'buy_user'    => $cutv_acts[ $slug ][ 'buy_user' ] ,
					'buy_licence' => $cutv_acts[ $slug ][ 'buy_licence' ] ,
					'act_addons'  => $cutv_acts[ $slug ][ 'act_addons' ] ,
					'buy_expires' => $cutv_acts[ $slug ][ 'buy_expires' ] ,
				);
			}
		}
	}
	
	if( ! function_exists( 'cutv_set_act_data' ) ) {
		function cutv_set_act_data( $slug = 'cutv' , $new_data ) {
			$cutv_acts = get_option( 'cutv_activations' );
			if( ! array( $cutv_acts ) ) $cutv_acts = array();
			$cutv_acts[ $slug ] = $new_data;
			update_option( 'cutv_activations' , $cutv_acts );
		}
	}
	
	if( ! function_exists( 'cutv_refresh_act_data' ) ) {
		function cutv_refresh_act_data( $slug = 'cutv' , $do_refresh = FALSE ) {
			global $CUTV_SERVER;
			$act = cutv_get_act_data( $slug );
			$url = cutv_capi_build_query( CUTV_API_REQ_URL , array(
				'api_key'         => CUTV_API_REQ_KEY ,
				'action'          => 'check_license' ,
				'products_slugs'  => $slug ,
				'act_id'          => $act[ 'act_id' ] , //921
				'encrypt_results' => 1 ,
				'only_results'    => 1 ,
				'origin'          => $CUTV_SERVER[ 'HTTP_HOST' ] ,
			) );
			
			$response = cutv_capi_remote_get( $url , FALSE );
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
			if( $do_refresh ) cutv_set_act_data( $slug , $new_data );
			
			return $new_data;
		}
	}
	
	
	if( ! function_exists( 'cutv_license_is_expired' ) ) {
		function cutv_license_is_expired( $slug ) {
			$new    = cutv_refresh_act_data( $slug , TRUE );
			$now    = new Datetime();
			$expire = new Datetime( $new[ 'buy_expires' ] );
			
			return ( $now > $expire );
		}
	}
	
	
	//Set Activation
	if( ! function_exists( 'cutv_set_activation' ) ) {
		function cutv_set_activation( $product_slug = '' , $act = array() ) {
			global $cutv_empty_activation;
			$act              = cutv_extend( $act , $cutv_empty_activation );
			$cutv_activations = get_option( 'cutv_activations' );
			if( ! array( $cutv_activations ) ) $cutv_activations = array();
			
			$cutv_activations[ $product_slug ] = $act;
			
			update_option( 'cutv_activations' , $cutv_activations );
			
			
		}
	}
	// Is a free addon ?
	if( ! function_exists( 'cutv_is_free_addon' ) ) {
		function cutv_is_free_addon( $product_slug = '' ) {
			global $cutv_addons;
			if(
				isset( $cutv_addons[ $product_slug ][ 'infos' ][ 'free_addon' ] )
				&& $cutv_addons[ $product_slug ][ 'infos' ][ 'free_addon' ] === TRUE
			) {
				return TRUE;
			} else {
				return FALSE;
			}
			
			
		}
	}

	// Get Multisite Actctivation
	if( ! function_exists( 'cutv_get_multisite_activation' ) ) {
		function cutv_get_multisite_activation( $product_slug = '' , $_blog_id = null , $first_only = FALSE ) {
			global $cutv_empty_activation , $cutv_addons;


			$blogs = wp_get_sites( array() );
			//d( $blogs );
			$returned_activations = array();
			$first_valid_activation = FALSE;
			foreach ( (array) $blogs as $blog ) {

				$blog_id = $blog[ 'blog_id' ];

				if( $_blog_id != null && $_blog_id != $blog_id ) continue;

				$cutv_activations = get_blog_option( $blog_id , 'cutv_activations' );

				//if( $product_slug == 'cutv-fbvs' ){
				//	d( $cutv_activations[ $product_slug ] );
				//}

				if( $cutv_activations != FALSE ) {

					if( $product_slug == '' ) {
						$returned_activations[ $blog_id ] = $cutv_activations;
					} elseif( isset( $cutv_activations[ $product_slug ] ) ) {

						$returned_activations[ $blog_id ] = $cutv_activations[ $product_slug ];
						if( $cutv_activations[ $product_slug ]['act_status'] == 1 ){
							$first_valid_activation = $cutv_activations[ $product_slug ] ;
						}
					} else {
						$returned_activations[ $blog_id ] = $cutv_empty_activation;
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


			//$cutv_activations = get_option( 'cutv_activations' );
			//$old_activation   = get_option( 'cutv_activation' );
			//
			//if( $product_slug == '' ) return $cutv_activations;
			//if( ! array( $cutv_activations ) ) $cutv_activations = array();
			//
			//if( ! isset( $cutv_activations[ $product_slug ] ) ) {
			//	if( $product_slug == 'cutv' && is_array( $old_activation ) ) {
			//		$cutv_activations[ $product_slug ] = $old_activation;
			//	} else {
			//		$cutv_activations[ $product_slug ] = $cutv_empty_activation;
			//	}
			//}
			//
			//return $cutv_activations[ $product_slug ];

		}
	}

	// Get Actctivation
	if( ! function_exists( 'cutv_get_activation' ) ) {
		function cutv_get_activation( $product_slug = '' ) {
			global $cutv_empty_activation , $cutv_addons;

			$cutv_activations = get_option( 'cutv_activations' );
			$old_activation   = get_option( 'cutv_activation' );

			if( $product_slug == '' ) return $cutv_activations;
			if( ! array( $cutv_activations ) ) $cutv_activations = array();
			
			if( ! isset( $cutv_activations[ $product_slug ] ) ) {
				if( $product_slug == 'cutv' && is_array( $old_activation ) ) {
					$cutv_activations[ $product_slug ] = $old_activation;
				} else {
					$cutv_activations[ $product_slug ] = $cutv_empty_activation;
				}
			}
			
			return $cutv_activations[ $product_slug ];
			
		}
	}
	
	if( ! function_exists( 'cutv_set_activation' ) ) {
		function cutv_set_activation( $product_slug = '' , $activation ) {
			$cutv_activations                  = get_option( 'cutv_activations' );
			$cutv_activations[ $product_slug ] = $activation;
			update_option( 'cutv_activations' , $cutv_activations );
		}
	}
	
	
	/* Useful function for tracking activation/deactivation errors */
	if( ! function_exists( 'cutv_save_errors' ) ) {
		function cutv_save_errors( $error ) {
			$errors = get_option( 'cutv_errors' );
			if( ! is_array( $errors ) ) $errors = array();
			if( $error != '' ) $errors[] = $error;
			update_option( 'cutv_errors' , $errors );
		}
	}
	
	if( ! function_exists( 'cutv_reset_on_activation' ) ) {
		function cutv_reset_on_activation() {
			global $cutv_imported;
			
			//reset tables
			update_option( 'cutv_deferred' , array() );
			update_option( 'cutv_deferred_ids' , array() );
			update_option( 'cutv_imported' , array() );
			
			//Update IMPORTED
			cutv_update_imported_videos();
			$cutv_imported = get_option( 'cutv_imported' );
			
		}
	}
	
	
	/* GET CATEGORIES with count */
	if( ! function_exists( 'cutv_get_categories_count' ) ) {
		function cutv_get_categories_count( $invert = FALSE , $get_empty = FALSE , $hierarchy = FALSE , $ids = '' ) {
			$items = get_categories( $args = array(
				'type'         => array( CUTV_VIDEO_TYPE ) ,
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
	if( ! function_exists( 'cutv_get_categories' ) ) {
		function cutv_get_categories( $invert = FALSE ) {
			
			$catsArray = array();
			$wp_cats   = get_categories( array(
				'type'       => array( 'post' , CUTV_VIDEO_TYPE ) ,
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
	if( ! function_exists( 'cutv_get_dates_count' ) ) {
		function cutv_get_dates_count() {
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
				P.post_type = '" . CUTV_VIDEO_TYPE . "'
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
	if( ! function_exists( 'cutv_get_services_count' ) ) {
		function cutv_get_services_count() {
			global $wpdb , $cutv_services;
			global $cutv_vs;
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
				P.post_type = '" . CUTV_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
				AND (M_SERVICE.meta_key = 'cutv_video_service' )
			GROUP BY M_SERVICE.meta_value
			ORDER BY found_videos DESC
				
		";
			//$sql =
			$items = $wpdb->get_results( $sql , OBJECT );
			
			if( count( $items ) == 0 ) return array();
			$rServices = array();
			
			foreach ( $items as $item ) {
				if( isset( $cutv_vs[ $item->value ] ) ) {
					$rServices[ $item->value ] = array(
						'label' => $cutv_vs[ $item->value ][ 'label' ] ,
						'value' => $item->value ,
						'count' => $item->found_videos ,
					);
				}
			}
			
			return $rServices;
		}
	}
	
	/* GET AUTHORS FOR DROPDOWN */
	if( ! function_exists( 'cutv_get_authors_count' ) ) {
		function cutv_get_authors_count() {
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
				P.post_type = '" . CUTV_VIDEO_TYPE . "'
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
	if( ! function_exists( 'cutv_get_status_count' ) ) {
		function cutv_get_status_count() {
			global $wpdb , $cutv_status;
			$sql
				= "
			select 
				1 as label,
				P.post_status as value,
				COUNT(distinct P.ID ) as count				
			FROM 
				$wpdb->posts P 
			WHERE 
				P.post_type = '" . CUTV_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
			GROUP BY 
				P.post_status
		";
			
			$items = $wpdb->get_results( $sql , OBJECT );
			if( count( $items ) == 0 ) return array();
			$rItems = array();
			
			foreach ( $items as $item ) {
				if( isset( $cutv_status[ $item->value ] ) ) {
					$rItems[ $item->value ] = array(
						'label' => $cutv_status[ $item->value ][ 'label' ] ,
						'value' => $item->value ,
						'count' => $item->count ,
					);
				}
			}
			
			return $rItems;
			
		}
	}
	
	/* GET AUTHORS */
	if( ! function_exists( 'cutv_get_authors' ) ) {
		function cutv_get_authors( $invert = FALSE , $default = FALSE , $restrict = FALSE ) {
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
			
			if( current_user_can( CUTV_USER_CAPABILITY ) && $default ) {
				if( ! $invert ) $authors[ ' - Default - ' ] = "default";
				else $authors[ 'default' ] = ' - Default - ';
			}
			if( ! current_user_can( CUTV_USER_CAPABILITY ) ) {
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
	if( ! function_exists( 'cutv_numberK' ) ) {
		function cutv_numberK( $n , $double = FALSE ) {
			
			if( $n <= 999 ) {
				if( $double && $n < 10 ) return '0' . $n;
				else return $n;
			} elseif( $n > 999 && $n < 999999 ) return round( $n / 1000 , 2 ) . 'K';
			elseif( $n > 999999 ) return round( $n / 1000000 , 2 ) . 'M';
			else return FALSE;
		}
	}
	
	/* Return formated duration */
	if( ! function_exists( 'cutv_human_duration' ) ) {
		function cutv_human_duration( $seconds ) {
			if( $seconds > 86400 ) {
				$seconds -= 86400;
				
				return ( gmdate( "j\d H:i:s" , $seconds ) );
			} else return ( gmdate( "H:i:s" , $seconds ) );
		}
	}
	
	/* DECIDE WETHER TO RUN CRON OR NO */
	if( ! function_exists( 'cutv_doWork' ) ) {
		function cutv_doWork() {
			global $cutv_options;
			$doWork   = FALSE;
			$now      = new DateTime();
			$hour_now = $now->format( 'H' );
			if( $cutv_options[ 'autoRunMode' ] === FALSE ) {
				//echo "AUTORUN MODE DISABLED ! ";
				return FALSE;
			}
			if( $cutv_options[ 'wakeUpHours' ] ) {
				$wuhA = $cutv_options[ 'wakeUpHoursA' ];
				$wuhB = $cutv_options[ 'wakeUpHoursB' ];
				if( $wuhA == 'empty' || $wuhB == 'empty' ) $doWork = TRUE;
				else {
					$doWork = ( $hour_now >= $wuhA && $hour_now <= $wuhB );
				}
			} else $doWork = TRUE;
			
			return $doWork;
		}
	}
	
	/* Extends variables with default values */
	if( ! function_exists( 'cutv_extend' ) ) {
		function cutv_extend( $params , $params_def , $strict = FALSE ) {
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
	if( ! function_exists( 'cutv_recursive_log_msgs' ) ) {
		function cutv_recursive_log_msgs( $log_msgs , $lineHTML ) {
			foreach ( $log_msgs as $msg ) {
				if( ! is_array( $msg ) ) {
					$lineHTML .= "<div class='cutv_log_msgs'>" . $msg . "</div>";
				} else {
					$lineHTML .= "<div class='cutv_log_msgs_rec'>";
					$lineHTML = cutv_recursive_log_msgs( $msg , $lineHTML );
					$lineHTML .= "</div>";
				}
				
				return $lineHTML;
			}
		}
	}
	
	/* Return random post date according to cutv options */
	if( ! function_exists( 'cutv_random_postdate' ) ) {
		function cutv_make_postdate( $post_date = '' ) {
			global $cutv_options;
			
			if( $post_date == '' ) $post_date = new DateTime();
			else $post_date = new DateTime( $post_date );
			if( $cutv_options[ 'randomize' ] && $cutv_options[ 'randomizeStep' ] != 'empty' ) {
				$step = $cutv_options[ 'randomizeStep' ];
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
	if( ! function_exists( 'cutv_generate_colors' ) ) {
		function cutv_generate_colors( $ColorSteps = 0 ) {
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
	if( ! function_exists( 'cutv_refuse_access' ) ) {
		function cutv_refuse_access( $a = FALSE ) {
			if( $a === FALSE ) {
				?>
				<div class = "cutv_no_access" style = 'margin-top:50px;background: #fff;color: #444;font-family: "Open Sans", sans-serif;margin: 2em auto;padding: 1em 2em;max-width: 700px;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);box-shadow: 0 1px 3px rgba(0,0,0,0.13);'>
					<p>
					
					<h2> WP Video Robot </h2>
					<?php _e( 'You do not have sufficient permissions to access this page.' , CUTV_LANG ); ?>
					</p>
				</div>
				<?php
			} else {
				?>
				<div class = "cutv_no_access error" style = 'margin-top:50px;background: #fff;color: #444;font-family: "Open Sans", sans-serif;margin: 2em auto;padding: 1em 2em;max-width: 700px;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);box-shadow: 0 1px 3px rgba(0,0,0,0.13);'>
					<h2> WP VIDEO ROBOT </h2>
					
					<p>
						<?php _e( 'Your copy licence is not activated.' , CUTV_LANG ); ?><br/>
						<?php _e( 'You cannot use WP VIDEO ROBOT.' , CUTV_LANG ); ?>
					
					</p>
				</div>
				<?php
			}
		}
	}
	
	/* Get customer update infos */
	if( ! function_exists( 'cutv_get_customer_infos' ) ) {
		function cutv_get_customer_infos() {
			global $cutv_options;
			$customer_infos = array(
				'purchase_code'    => $cutv_options[ 'purchaseCode' ] ,
				'site_name'        => get_bloginfo( 'name' ) ,
				'site_url'         => get_bloginfo( 'url' ) ,
				'site_description' => get_bloginfo( 'description' ) ,
				'site_language'    => ( is_rtl() ) ? 'RTL' : 'LTR' ,
				'admnin_email'     => get_bloginfo( 'admin_email' ) ,
				'wp_version'       => get_bloginfo( 'version' ) ,
				'wp_url'           => get_bloginfo( 'wpurl' ) ,
				'wp_rtl'           => is_rtl() ,
				'sources_stats'    => cutv_sources_stats() ,
				'videos_stats'     => cutv_videos_stats() ,
			);
			
			return ( base64_encode( json_encode( $customer_infos ) ) );
		}
	}
	
	/* Remove all tmp files from tmp directory */
	if( ! function_exists( 'cutv_remove_tmp_files' ) ) {
		function cutv_remove_tmp_files() {
			$dirHandle = opendir( CUTV_TMP_PATH );
			while( $file = readdir( $dirHandle ) ) {
				if( ! is_dir( $file ) )
					unlink( CUTV_TMP_PATH . "$file" );
			}
			closedir( $dirHandle );
		}
	}
	
	/* Make interval from two datetime */
	if( ! function_exists( 'cutv_make_interval' ) ) {
		function cutv_make_interval( $start , $end , $bool = TRUE ) {
			
			if( $start == '' || $end == '' ) return array();
			
			$workingHours = array();
			for ( $i = 0; $i < 24; $i ++ ) {
				if( strlen( $i ) == 1 ) $i = '0' . $i;
				$workingHours[ $i ] = ! $bool;
			}
			if( $start > $end ) {
				return cutv_make_interval( $end , $start , ! $bool );
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
	if( ! function_exists( 'cutv_is_working_hour' ) ) {
		function cutv_is_working_hour( $hour ) {
			global $cutv_options;
			$wh = $cutv_options[ 'wakeUpHours' ];
			
			if( $wh === FALSE ) return TRUE;
			
			$whA = $cutv_options[ 'wakeUpHoursA' ];
			$whB = $cutv_options[ 'wakeUpHoursB' ];
			
			$whArray = cutv_make_interval( $whA , $whB , TRUE );
			if( isset( $whArray[ $hour ] ) ) return $whArray[ $hour ];
			else return array();
		}
	}
	
	/* Add CUTV Notice */
	if( ! function_exists( 'cutv_render_notice' ) ) {
		function cutv_render_notice( $notice = array() ) {
			global $current_user , $default_notice;
			$user_id = $current_user->ID;
			
			if( ! is_array( $notice ) ) {
				$notices = get_option( 'cutv_notices' );
				$notice  = $notices[ $notice ];
			}
			
			$notice = cutv_extend( $notice , $default_notice );
			
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
				cutv_render_dialog_notice( $notice );
				
				return TRUE;
			}
			
			/* Check that the user hasn't already clicked to ignore the message */
			if( ! get_user_meta( $user_id , $notice[ 'slug' ] ) ) {
				$hideLink = "?cutv_hide_notice=" . $notice[ 'slug' ] . "";
				foreach ( $_GET as $key => $value ) {
					//d( $value );d( $key );
					if( is_string( $value ) && $key != 'cutv_hide_notice' ) $hideLink .= "&$key=$value";
				}
				?>
				<div class = "error <?php echo $notice[ 'class' ]; ?> cutv_wp_notice" style = "display:none; <?php echo $notice_style; ?>">
					<?php if( $icon != '' ) { ?>
						<div class = "pull-left cutv_notice_icon" style = "<?php echo $icon_style; ?>">
							<i class = "fa <?php echo $icon; ?>"></i>
						</div>
					<?php } ?>
					<?php if( $notice[ 'hidable' ] ) { ?>
						<a class = "pull-right" href = "<?php echo $hideLink; ?>">
							<?php _e( 'Hide this notice' , CUTV_LANG ); ?>
						</a>
					<?php } ?>
					<div class = "cutv_notice_content pull-left">
						<strong><?php echo $notice[ 'title' ]; ?></strong>
						<?php echo $line_break; ?>
						
						<div><?php echo $notice[ 'content' ]; ?></div>
					</div>
					<div class = "cutv_clearfix"></div>
				</div>
				<?php
			}
			
			if( isset( $notice[ 'show_once' ] ) && $notice[ 'show_once' ] === TRUE ) {
				cutv_remove_notice( $notice[ 'slug' ] );
			}
			
		}
	}
	
	
	/* Add CUTV Notice */
	if( ! function_exists( 'cutv_render_done_notice_redirect' ) ) {
		function cutv_render_done_notice_redirect( $msg , $unique = TRUE ) {
			cutv_add_notice( array(
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
	
	/* Add CUTV Notice */
	if( ! function_exists( 'cutv_render_done_notice' ) ) {
		function cutv_render_done_notice( $msg , $unique = TRUE ) {
			$error_notice_slug = cutv_add_notice( array(
				'title'     => 'WP Video Robot : ' ,
				'class'     => 'updated' , //updated or warning or error
				'content'   => $msg ,
				'hidable'   => FALSE ,
				'is_dialog' => FALSE ,
				'show_once' => TRUE ,
				'color'     => '#27A1CA' ,
				'icon'      => 'fa-check-circle' ,
			) );
			cutv_render_notice( $error_notice_slug );
			cutv_remove_notice( $error_notice_slug );
		}
	}
	
	/* Add CUTV Notice */
	if( ! function_exists( 'cutv_render_error_notice' ) ) {
		function cutv_render_error_notice( $msg , $unique = TRUE ) {
			$error_notice_slug = cutv_add_notice( array(
				'title'     => 'WP Video Robot ERROR :' ,
				'class'     => 'error' , //updated or warning or error
				'content'   => $msg ,
				'hidable'   => FALSE ,
				'is_dialog' => FALSE ,
				'show_once' => TRUE ,
				'color'     => '#E4503C' ,
				'icon'      => 'fa-exclamation-triangle' ,
			) );
			cutv_render_notice( $error_notice_slug );
			cutv_remove_notice( $error_notice_slug );
		}
	}
	
	/* Add CUTV Notice */
	if( ! function_exists( 'cutv_add_notice' ) ) {
		function cutv_add_notice( $notice = array() , $unique = TRUE , $multisite = FALSE ) {
			global $default_notice;
			if( ! $multisite ) {
				$notices = get_option( 'cutv_notices' );
			} else {
				$notices = get_site_option( 'cutv_notices' );
			}
			if( $notices == '' ) $notices = array();
			
			$notice           = cutv_extend( $notice , $default_notice );
			$nowObj           = new Datetime();
			$notice[ 'date' ] = $nowObj->format( 'Y-m-d H:i:s' );
			if( $unique === TRUE ) $notices[ $notice[ 'slug' ] ] = $notice;
			else $notices[] = $notice;


			if( ! $multisite ) {
				update_option( 'cutv_notices' , $notices );
			} else {
				update_site_option( 'cutv_notices' , $notices );
			}
			//d( $notices );
			//return $notices;
			return $notice[ 'slug' ];
		}
	}
	
	/* TEsting if Count Videos has reached one of our levels */
	if( ! function_exists( 'cutv_is_reaching_level' ) ) {
		function cutv_is_reaching_level( $count ) {
			global $cutv_rating_levels;
			foreach ( $cutv_rating_levels as $level ) {
				if( $count >= $level ) return $level;
			}
			
			return FALSE;
		}
	}
	
	/* Add CUTV Notice */
	if( ! function_exists( 'cutv_render_dialog_notice' ) ) {
		function cutv_render_dialog_notice( $notice ) {
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
						var noticeBox = cutv_show_loading(noticeBoxArgs);
						
						<?php if( $notice[ 'dialog_ok_url' ] === FALSE ) { ?>
						noticeBox.doPause(function () {
							noticeBox.remove();
						});
						<?php } else{ ?>
						noticeBox.doPause(function () {
							$('.cutv_loading_cancel', noticeBox).attr('has_voted', 'yes').trigger('click');
							window.open('<?php echo $notice[ 'dialog_ok_url' ]; ?>', '_blank');
						});
						<?php } ?>
						
						<?php if( $notice[ 'hidable' ] === TRUE ){ ?>
						noticeBox.doCancel(function () {
							var btn = $('.cutv_loading_cancel', noticeBox);
							var has_voted = btn.attr('has_voted');
							var btn_label = btn.html();
							$('i', btn).addClass('fa-spin');
							//btn.html( btn_label+' ....');
							$.ajax({
								type: "GET",
								url: '<?php echo CUTV_ACTIONS_URL; ?>',
								data: {
									cutv_wpload: 1,
									dismiss_dialog_notice: 1,
									has_voted: has_voted,
									notice_slug: '<?php echo $notice[ 'slug' ]; ?>'
								},
								success: function (data) {
									//btn.html( btn_label);
									$('i', btn).removeClass('fa-spin');
									$json = cutv_get_json(data);
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
	
	/* Add CUTV Notice */
	if( ! function_exists( 'cutv_remove_notice' ) ) {
		function cutv_remove_notice( $notice_slug , $multisite = FALSE ) {
			$notices = $multisite ? get_site_option( 'cutv_notices' ) : get_option( 'cutv_notices' );
			if( $notices == '' ) $notices = array();
			foreach ( (array) $notices as $k => $notice ) {
				if( $notice[ 'slug' ] == $notice_slug ) {
					unset( $notices[ $k ] );
				}
			}

			if( $multisite ) {
				update_site_option( 'cutv_notices' , $notices );
			} else {
				update_option( 'cutv_notices' , $notices );
			}
			
			return $notices;
		}
	}
	
	/* Add CUTV Notice */
	if( ! function_exists( 'cutv_remove_all_notices' ) ) {
		function cutv_remove_all_notices() {
			update_option( 'cutv_notices' , array() );
		}
	}
	
	/* Get Cats Recursively */
	if( ! function_exists( 'cutv_rec_get_cats' ) ) {
		function cutv_rec_get_cats( $hCats = array() , $parent_id = null , $level = 0 ) {
			global $cutv_hierarchical_cats;
			$args = array(
				'type'         => array( CUTV_VIDEO_TYPE ) ,
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
				$cutv_hierarchical_cats[] = array(
					'label' => $cat_item[ 'label' ] ,
					'value' => $cat_item[ 'value' ] ,
				);
				$int_level ++;
				$hCats[ $item->term_id ] = array(
					'item' => $cat_item ,
					'subs' => cutv_rec_get_cats( $hCats , $item->term_id , $int_level ) ,
				);
			}
			
			return $hCats;
		}
	}
	
	/* Get Hierarchical Array of Categories with Counts*/
	if( ! function_exists( 'cutv_get_hierarchical_cats' ) ) {
		function cutv_get_hierarchical_cats( $return_tree = FALSE ) {
			global $cutv_hierarchical_cats;
			$tree_cats = cutv_rec_get_cats();
			if( $return_tree ) return $tree_cats;
			else return $cutv_hierarchical_cats;
		}
	}
	
	/* Get Taxonomy TErms array with count */
	if( ! function_exists( 'cutv_get_taxonomy_terms' ) ) {
		function cutv_get_taxonomy_terms( $taxonomy ) {
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
	if( ! function_exists( 'cutv_max_fetched_videos_per_run' ) ) {
		function cutv_max_fetched_videos_per_run() {
			global $cutv_options;
			
			$sources = cutv_get_sources( array( 'status' => 'on' ) );
			$sources = cutv_multiplicate_sources( $sources );
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
				$wantedVideos = ( $source->wantedVideosBool == 'default' ) ? $cutv_options[ 'wantedVideos' ] : $source->wantedVideos;
				$data[ $source->id ][ 'wanted_videos' ] += $wantedVideos;
				$data[ $source->id ][ 'sub_sources' ] ++;
				
				if( $data[ $source->id ][ 'wanted_videos' ] > CUTV_SECURITY_WANTED_VIDEOS ) $data[ $source->id ][ 'warning' ] = TRUE;
				
			}
			
			return $data;
		}
	}
	
	
	/* Download Thumbnail from URL */
	if( ! function_exists( 'cutv_download_featured_image' ) ) {
		function cutv_download_featured_image( $image_url = '' , $image_title = '' , $image_desc = '' , $post_id = '' , $unique_id = '' ) {

			if( CUTV_DISABLE_THUMBS_DOWNLOAD === TRUE ) return '';

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
			
			//cutv_set_debug( $file );
			
			return $file;
			
		}
	}
	
	if( ! function_exists( 'cutv_render_add_unwanted_button' ) ) {
		function cutv_render_add_unwanted_button( $post_id ) {
			global $cutv_unwanted_ids , $cutv_unwanted;
			$video_id      = get_post_meta( $post_id , 'cutv_video_id' , TRUE );
			$video_service = get_post_meta( $post_id , 'cutv_video_service' , TRUE );
			//d( $cutv_unwanted_ids );
			//d( $cutv_unwanted_ids[$video_service] );
			if( $video_id == '' || $post_id == '' ) return '';
			if( isset( $cutv_unwanted_ids[ $video_service ][ $video_id ] ) ) {
				$action = 'remove';
				$icon   = 'fa-undo';
				$label  = __( 'Remove from Unwanted' , CUTV_LANG );
				$class  = "cutv_black_button";
			} else {
				$action = 'add';
				$icon   = 'fa-ban';
				$label  = __( 'Add to Unwanted' , CUTV_LANG );
				$class  = "cutv_red_button";
				
			}
			
			$unwanted_button
				= '

				<button
					url = "' . CUTV_ACTIONS_URL . '"
					class=" ' . $class . ' cutv_button cutv_full_width cutv_single_unwanted"
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
	
	if( ! function_exists( 'cutv_async_balance_items' ) ) {
		function cutv_async_balance_items( $items , $buffer ) {
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
	
	if( ! function_exists( 'cutv_get_cron_url' ) ) {
		function cutv_get_cron_url( $query = '' ) {
			global $cutv_cron_token;
			
			return get_site_url( null , '/' . CUTV_CRON_ENDPOINT . '/' . $cutv_cron_token . '/' . $query );
		}
	}

	if( ! function_exists( 'cutv_render_copy_button' ) ) {
		function cutv_render_copy_button( $target ) {

			?>
			<button
				class = "cutv_copy_btn cutv_button cutv_black_button pull-right"
				data-clipboard-target = "#<?php echo $target; ?>"
				done = ""
			>
				<i class = "cutv_green fa fa-check"></i>
				<i class = "cutv_black fa fa-copy"></i>
				<span class = "cutv_black"><?php echo __( 'COPY' , CUTV_LANG ); ?></span>
				<span class = "cutv_green"><?php echo __( 'COPIED !' , CUTV_LANG ); ?></span>
			</button>
			<?php


		}
	}