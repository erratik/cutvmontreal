<?php
	
	
	function wpvrfes_check_user_roles( $user_id = '' ) {
		global $wpvr_options , $user_ID;
		$slot           = wpvr_get_addon_options( WPVRFES_ID );
		
		$accepted_roles = $slot[ 'posting_roles' ];
		
		if( $user_id == '' ) $user_id = $user_ID;
		$user       = new WP_User( $user_id );
		$user_roles = $user->roles;
		if( $accepted_roles == null ) return TRUE;
		foreach( $user_roles as $role ) {
			if( in_array( $role , $accepted_roles ) ) return TRUE;
			if( $role == 'administrator' ) return TRUE;
		}
		return FALSE;
	}
	
	function fes_rpHash( $value ) {
		$hash  = 5381;
		$value = strtoupper( $value );
		for( $i = 0; $i < strlen( $value ); $i++ ) {
			$hash = ( fes_leftShift32( $hash , 5 ) + $hash ) + ord( substr( $value , $i ) );
		}
		return $hash;
	}

	// Perform a 32bit left shift 
	function fes_leftShift32( $number , $steps ) {
		// convert to binary (string) 
		$binary = decbin( $number );
		// left-pad with 0's if necessary 
		$binary = str_pad( $binary , 32 , "0" , STR_PAD_LEFT );
		// left shift manually 
		$binary = $binary . str_repeat( "0" , $steps );
		// get the last 32 bits 
		$binary = substr( $binary , strlen( $binary ) - 32 );
		// if it's a positive number return it 
		// otherwise return the 2's complement 
		return ( $binary{0} == "0"
			? bindec( $binary )
			:
			-( pow( 2 , 31 ) - bindec( substr( $binary , 1 ) ) ) );
	}

	function wpvrfes_get_video_id( $param , $service ) {
		if( $service == 'youtube' ) {
			////////////// YOUTUBE //////////////
			if( strpos( $param , 'youtube.com' ) === FALSE ) {
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
				$separator = strpos( $param , 'https://' ) ? 'http://vimeo.com/' : 'https://vimeo.com/';
				$x = explode( $separator , $param );
				if( ! isset( $x[ 1 ] ) ) {
					return FALSE;
				} else {
					$y = explode( '/' , $x[ 1 ] );
					return $y[ 0 ];
				}
			}
		} elseif( $service == 'dailymotion' ) {

			////////////// DAILYMOTION //////////////
			if( strpos( $param , 'dailymotion.com' ) === FALSE ) {
				return $param;
			} else {
				$separator = strpos( $param , 'https://' ) ? 'http://www.dailymotion.com/video/' : 'https://www.dailymotion.com/video/';
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
				$separator = strpos( $param , 'https://' ) ? 'http://www.ted.com/talks/' : 'https://www.ted.com/talks/';
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
				$separator = strpos( $param , 'https://' ) ? 'http://v.youku.com/v_show/id_' : 'https://v.youku.com/v_show/id_';
				$x = explode( $separator , $param );
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
	
	function wpvrfes_submit_video( $videoid , $videoservice , $submitter = array() ) {
		global $wpvr_imported;
		$slot = wpvr_get_addon_options( WPVRFES_ID );
		
		//new dBug( $slot );return false;
		
		
		// Parse Video ID
		$video_id = wpvrfes_get_video_id( $videoid , $videoservice );
		if( $video_id === FALSE ) {
			return array(
				'status'  => 0 ,
				'post_id' => '' ,
				'msg'     => 'Video ID not valid.' ,
				'video'   => array() ,
			);
		}
		$videoItem = wpvr_get_video_single_data( $video_id , $videoservice );
		
		if( $videoItem === FALSE ) {
			$out = array(
				'status'  => 0 ,
				'post_id' => '' ,
				'msg'     => 'Video Not Found.' ,
				'video'   => array() ,
			);
			return $out;
		}
		
		$videoItem[ 'origin' ]   = 'by FRONTEND SUBMISSION';
		$videoItem[ 'service' ]  = $videoservice;
		$videoItem[ 'id' ]       = $video_id;
		$videoItem[ 'postDate' ] = 'updated';
		
		$videoItem[ 'postAppend' ]     = 'off';
		$videoItem[ 'postAppendName' ] = FALSE;
		$videoItem[ 'description' ]    = $videoItem[ 'desc' ];
		$videoItem[ 'source_tags' ]    = array();
		$videoItem[ 'hqthumb' ]        = FALSE;
		
		if( $submitter[ 'userid' ] == '' ) $videoItem[ 'postAuthor' ] = $slot[ 'posting_author' ];
		else $videoItem[ 'postAuthor' ] = $submitter[ 'userid' ];
		
		if( $slot[ 'enable_categories' ] ) $videoItem[ 'postCats' ] = array( $submitter[ 'category' ] );
		else $videoItem[ 'postCats' ] = $slot[ 'posting_cats' ];
		$videoItem[ 'autoPublish' ] = $slot[ 'auto_publish' ];
		
		
		$videoItem[ 'sourceName' ] = 'Front End Submission';
		$videoItem[ 'sourceType' ] = 'frontend_submission';
		$videoItem[ 'sourceId' ]   = 0;
		$videoItem[ 'postContent' ]   = TRUE ;


		
		if( $slot[ 'skip_duplicates' ] === TRUE ) $allowDuplicates = FALSE;
		else $allowDuplicates = TRUE;
		
		$newPostId            = wpvr_add_video( $videoItem , $wpvr_imported , $allowDuplicates );
		$is_duplicate_message = '';
		if( $allowDuplicates === FALSE ) {
			//dups not allowed
			if( isset( $wpvr_imported[ 'youtube' ] ) ) {
				//is dup
				$is_duplicate_message = 'Error : Video already published on our site.';
			}
		}
		
		if( $newPostId === FALSE ) {
			if( $is_duplicate_message == '' ) $msg = 'Error.Video not Submitted.';
			else $msg = $is_duplicate_message;
			$out = array(
				'status'  => 0 ,
				'post_id' => '' ,
				'msg'     => $msg ,
				'video'   => array() ,
			);
			return $out;
		}
		if( ! is_array( $submitter ) || count( $submitter ) == 0 ) {
			foreach( $submitter as $key => $value ) {
				if( $value != '' )
					update_post_meta( $newPostId , 'wpvr_fes_submitter_' . $key , $value );
			}
		}
		//new dBug( $videoItem );
		if( strlen( $videoItem[ 'desc' ] > 50 ) ) $desc = substr( $videoItem[ 'desc' ] , 0 , 50 ) . ' ... ';
		else $desc = $videoItem[ 'desc' ];
		
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		$video_infos
			= '
			<div class="fes_video">
				<div class="fes_video_thumb">
					<img src="' . $videoItem[ 'thumb' ] . '" />
				</div>
				<div class="fes_video_title">' . $videoItem[ 'title' ] . '</div>
				<div class="fes_video_desc">' . $desc . '</div>
				<div class="fes_clearfix"></div>
			</div>
			<br/>
			<div class="fes_submit_again">
				<a class="" href="#"> SUBMIT ANOTHER VIDEO </a>
			</div>
			
		';
		
		$message = stripslashes( $slot[ 'submitted_message' ] );
		
		$out = array(
			'status'  => 1 ,
			'post_id' => $newPostId ,
			'msg'     => $message . '<br/><br/>' . $video_infos ,
			'video'   => $videoItem ,
		);
		return $out;
		
	}
	
	function wpvrfes_get_current_user_data() {
		global $current_user;
		$user_id = $current_user->ID;
		if( $current_user->ID == 0 ) {
			return array(
				'id'    => '' ,
				'name'  => '' ,
				'email' => '' ,
			);
		}
		return array(
			'id'    => $current_user->ID ,
			'name'  => $current_user->data->display_name ,
			'email' => $current_user->data->user_email ,

		);
	}

	function wpvrfes_render_form() {

		global $wpvr_vs;

		//@session_start();
		$token = bin2hex( openssl_random_pseudo_bytes( 16 ) );
		$slot  = wpvr_get_addon_options( WPVRFES_ID );
		if( $slot[ 'addon_enabled' ] === FALSE ) {
			return stripslashes( $slot[ 'closed_message' ] );
			return FALSE;
		}
		$_SESSION[ 'ajax_token' ] = $token;
		
		//new dBug( $slot );
		
		if( $slot[ 'enable_captcha' ] === TRUE ) {
			$captcha_field
				= '
				<div class="fes_field_wrap">
					<label class="fes_field_label"> Are you human ? </label>
					<div class="fes_clearfix"></div>
					<div class="fes_captcha_wrap"></div>
					<input 
						type="text" 
						class=" fes_field_input fes_captcha_field" 
						id="captcha" 
						name="captcha" 
						placeholder="" 
						value=""
					/>
					<div class="fes_clearfix"></div>
				</div>
			';
		} else $captcha_field = '';
		
		
		if( $slot[ 'enable_categories' ] === TRUE ) {
			$ids        = implode( ',' , $slot[ 'allowed_cats' ] );
			$categories = wpvr_get_categories_count( FALSE , TRUE , TRUE , $ids );
			
			//new dBug( $slot['allowed_cats'] );
			
			$categories_options = '';
			foreach( $categories as $cat ) {
				if( WPVRFES_ALLOW_UNCATEGORIZED && strtolower($cat[ 'label' ]) == 'uncategorized' ) {
					continue; 
				}
				$categories_options
					.= '
					<option value="' . $cat[ 'value' ] . '">
						' . $cat[ 'label' ] . ' (' . wpvr_numberK( $cat[ 'count' ] ) . ')
					</option>
				';
			}
			$categories_field
				= '
				<div class="fes_field_wrap">
					<label class="fes_field_label"> Post to </label>
					<div class="fes_clearfix"></div>
					<select 
						class=" fes_field_input fes_categories_field" 
						id="submitter_category" 
						name="submitter_category" 
					>
						' . $categories_options . '
					</select>
				</div>
			';
		} else $categories_field = '';
		
		$cUser = wpvrfes_get_current_user_data();
		
		
		if( $slot[ 'posting_users' ] != 'all' && ! is_user_logged_in() ) {
			$message = stripslashes( do_shortcode( $slot[ 'logged_only_message' ] ) );
			return $message;
		}
		
		if( $slot[ 'posting_roles' ] != '' && ! wpvrfes_check_user_roles( $cUser[ 'id' ] ) ) {
			$message = '' . stripslashes( do_shortcode( $slot[ 'bad_role_message' ] ) );
			return $message;
		}
		//d( $slot );
		$services_options = '';
		foreach( $wpvr_vs as $vs ) {
			$option_id = 'enable_' . $vs[ 'id' ];
			if( $slot[ $option_id ] ) {
				$services_options
					.= '
					<option value="' . $vs[ 'id' ] . '" >
						' . $vs[ 'label' ] . '
					</option>
				';
			}

		}

		$form
			= '
			<form class="fes_form_wrap">
				
				<input 
					type = "hidden" 
					name = "submitter_id" 
					id = "submitter_id" 
					value="' . $cUser[ 'id' ] . '"
				/>
				
				<!-- SUBMITTER NAME -->
				<div class="fes_field_wrap">
					<label class="fes_field_label"> Your Name </label>
					<input 
						type="text" 
						class=" fes_field_input" 
						id="submitter_name" 
						name="submitter_name" 
						placeholder="' . FES_SUBMITTER_NAME_PLACEHOLDER . '"
						value="' . $cUser[ 'name' ] . '"
					/>
				</div>
				
				<!-- SUBMITTER EMAIL -->
				<div class="fes_field_wrap">
					<label class="fes_field_label"> Your Email </label>
					<input 
						type="text" 
						class=" fes_field_input" 
						id="submitter_email" 
						name="submitter_email" 
						placeholder="' . FES_SUBMITTER_EMAIL_PLACEHOLDER . '"
						value="' . $cUser[ 'email' ] . '"
					/>
				</div>

				<!-- VIDEO SERVICE -->
				<div class="fes_field_wrap">
					<label class="fes_field_label"> Video Service </label>
					<select
						class="fes_field_input"
						id="video_service"
						name="video_service"
					>
						<option value=""> - Pick a service </option>
						' . $services_options . '
					</select>
				</div>

				<!-- VIDEO URL OR ID -->
				<div class="fes_field_wrap">
					<label class="fes_field_label"> Video URL or ID </label>
					<input 
						type="text" 
						class=" fes_field_input" 
						id="video_id" 
						name="video_id" 
						placeholder="' . FES_SUBMITTER_URL_PLACEHOLDER . '"
						value=""
					/>
				</div>
				
				' . $categories_field . '
				
				' . $captcha_field . '
				
				
				
				
				<div class="fes_response"></div>
				
				
				<button 
					class="fes_submit_video fes_button" 
					url="' . WPVRFES_ACTIONS_URL . '?fes_wpload&submit_video&ajax_token=' . $token . '"
					token = "' . $token . '"
				>
					<i class="fa fa-check fes_ready_icon"></i> 
					<i class="fa fa-cog fa-spin fes_loading_icon"></i> 
					SUBMIT VIDEO
				</button>
				
				<div class="fes_clearfix"></div>
				
				
				
			</form>
		
		
		';
		
		return $form;
		
	}
