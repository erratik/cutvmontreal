<?php
	
	add_action( 'wpvr_event_add_video_done' , 'wpvr_add_notice_trigger_function' , 10 , 1 );
	function wpvr_add_notice_trigger_function( $count_videos ) {
		if( WPVR_ASK_TO_RATE_TRIGGER === FALSE ) {
			return FALSE;
		}
		global $current_user;
		$user_id = $current_user->ID;
		
		//update_option('koko' , $count_videos );
		
		if( get_user_meta( $user_id , 'wpvr_user_has_voted' , TRUE ) == 1 ) {
			return FALSE;
		}
		$level_reached = wpvr_is_reaching_level( $count_videos );
		if( $level_reached != FALSE ) {
			$message = "<p class='wpvr_dialog_icon'><i class='fa fa-trophy'></i></p>" .
			           "<div class='wpvr_dialog_msg'>" .
			           "<p>Hey, you just have crossed <strong>$count_videos</strong> videos imported with WPVR. That's Awesome !</p>" .
			           "<p>Could you please do us a big favor and give WP Video Robot a 5-star rating on Codecanyon ?" .
			           "<br/>That will help us spread the word and boost our motivation.</p>" .
			           "<strong>~pressaholic</strong>" .
			           "</div>";
			
			$token = bin2hex( openssl_random_pseudo_bytes( 16 ) );
			
			/*$wpvr_notices = get_option('wpvr_notices');
			foreach( $wpvr_notices as $id=>$notice){
				if( strpos($id , 'rating_notice_' ) != false )  unset( $wpvr_notices[ $id ] );
			}
			update_option('wpvr_notices' , $wpvr_notices);
			*/
			
			
			wpvr_add_notice( array(
				'slug'               => "rating_notice_" . $level_reached ,
				'title'              => 'Congratulations !' ,
				'class'              => 'updated' , //updated or warning or error
				'content'            => $message ,
				'hidable'            => TRUE ,
				'is_dialog'          => TRUE ,
				'dialog_modal'       => FALSE ,
				'dialog_delay'       => 1500 ,
				//'dialog_ok_button' => '',
				'dialog_ok_button'   => ' <i class="fa fa-heart"></i> RATE WPVR NOW' ,
				'dialog_hide_button' => '<i class="fa fa-close"></i> DISMISS ' ,
				'dialog_class'       => ' askToRate ' ,
				'dialog_ok_url'      => 'http://codecanyon.net/downloads#item-8619739' ,
			) );
			
		}
		
	}
	
	add_action( 'wp_trash_post' , 'wpvr_add_unwanted_on_trash' );
	function wpvr_add_unwanted_on_trash( $post_id ) {
		global $wpvr_options;
		if( get_post_type( $post_id ) == WPVR_VIDEO_TYPE && $wpvr_options[ 'unwantOnTrash' ] === TRUE ) {
			wpvr_unwant_videos( array( $post_id ) );
		}
	}
	
	add_action( 'before_delete_post' , 'wpvr_add_unwanted_on_delete' );
	function wpvr_add_unwanted_on_delete( $post_id ) {
		global $wpvr_options;
		if( get_post_type( $post_id ) == WPVR_VIDEO_TYPE && $wpvr_options[ 'unwantOnDelete' ] === TRUE ) {
			wpvr_unwant_videos( array( $post_id ) );
		}
	}
	
	add_action( 'admin_init' , 'wpvr_demo_message_ignore' );
	function wpvr_demo_message_ignore() {
		global $current_user;
		$user_id = $current_user->ID;
		/* If user clicks to ignore the notice, add that to their user meta */
		if( isset( $_GET[ 'wpvr_show_demo_notice' ] ) && '0' == $_GET[ 'wpvr_show_demo_notice' ] ) {
			add_user_meta( $user_id , 'wpvr_show_demo_notice' , 'true' , TRUE );
		}
		
		if( isset( $_GET[ 'wpvr_hide_notice' ] ) && $_GET[ 'wpvr_hide_notice' ] != '' ) {
			add_user_meta( $user_id , $_GET[ 'wpvr_hide_notice' ] , 'true' , TRUE );
		}
		
	}
	
	/* Define Custom Dashboard Widgets */
	add_action( 'wp_dashboard_setup' , 'wpvr_custom_dashboard_widget' );
	function wpvr_custom_dashboard_widget() {
		global $wp_meta_boxes;
		wp_add_dashboard_widget(
			'home_dashboard_widget' , //ID of the dashboard Widgets
			'WP Video Robot - Global Activity' , //Title of the dashboard Widgets
			'wpvr_custom_dashboard_function' ,
			'side' ,
			'high'
		);
	}
	
	/* Define hourly running event */
	//add_action( 'wpvr_hourly_event' , 'wpvr_run_hourly' );
	//function wpvr_run_hourly() {
	//	global $wpvr_options;
	//	if( ! $wpvr_options[ 'useCronTab' ] ) {
	//		include( WPVR_PATH . 'wpvr.cron.php' );
	//	}
	//}
	
	
	/* Function to prevent from showing content on loops */
	add_action( 'the_content' , 'wpvr_remove_flow_content' );
	function wpvr_remove_flow_content( $html ) {
		if(
			is_admin()
			|| ! defined( 'WPVR_REMOVE_FLOW_CONTENT' )
			|| WPVR_REMOVE_FLOW_CONTENT === FALSE
			|| get_post_type() != WPVR_VIDEO_TYPE
		) {
			return $html;
		} else {
			if( ! is_singular() ) {
				return '';
			} else {
				return $html;
			}
		}
	}
	
	/* Function to prevent from showing tags on loops */
	add_action( 'term_links-post_tag' , 'wpvr_remove_flow_tags' );
	function wpvr_remove_flow_tags( $tags ) {
		if(
			is_admin()
			|| ! defined( 'WPVR_REMOVE_FLOW_TAGS' )
			|| WPVR_REMOVE_FLOW_TAGS === FALSE
			|| get_post_type() != WPVR_VIDEO_TYPE
		) {
			return $tags;
		} else {
			if( ! is_singular() ) {
				return array();
			} else {
				return $tags;
			}
		}
	}
	
	/* Function for whether to show thumbnail on single */
	add_action( 'post_thumbnail_html' , 'wpvr_remove_thumb_single_function' );
	function wpvr_remove_thumb_single_function( $html ) {
		if(
			is_admin()
			|| ! is_singular()
			|| ! defined( 'WPVR_REMOVE_THUMB_SINGLE' )
			|| WPVR_REMOVE_THUMB_SINGLE === FALSE
			|| get_post_type() != WPVR_VIDEO_TYPE
		) {
			return $html;
		} else {
			return '';
		}
	}
	
	/* Function for replacing post thumbnail by embeded video player */
	add_action( 'post_thumbnail_html' , 'wpvr_video_thumbnail_embed' , 20 , 2 );
	function wpvr_video_thumbnail_embed( $html , $post_id ) {
		global $wpvr_options , $wpvr_is_admin;
		if( get_post_type() != WPVR_VIDEO_TYPE ) return $html;
		if( $wpvr_is_admin === TRUE || is_admin() || $wpvr_options[ 'videoThumb' ] === FALSE ) {
			return $html;
		} else {
			if( is_singular() ) {
				return $html;
			} else {
				$wpvr_video_id = get_post_meta( $post_id , 'wpvr_video_id' , TRUE );
				$wpvr_service  = get_post_meta( $post_id , 'wpvr_video_service' , TRUE );
				$player        = wpvr_video_embed(
					$wpvr_video_id ,
					$post_id ,
					$autoPlay = FALSE ,
					$wpvr_service
				);
				$embedCode     = '<div class="wpvr_embed">' . $player . '</div>';
				
				return $embedCode;
			}
		}
	}

	/* Function for replacing post thumbnail by embeded video player */
	add_filter( 'post_thumbnail_html' , 'wpvr_video_thumbnail_use_service_thumb' , 20 , 2 );
	function wpvr_video_thumbnail_use_service_thumb( $html , $post_id ) {
		global $wpvr_options , $wpvr_is_admin;
		if( get_post_type() != WPVR_VIDEO_TYPE ) return $html;
		if( !WPVR_DISABLE_THUMBS_DOWNLOAD) return $html;

		if( get_post_meta( $post_id , '_thumbnail_id' , TRUE ) == '' ){
			$service_image_url = get_post_meta( $post_id , 'wpvr_video_service_thumb' , TRUE );
			return '<img src="'.$service_image_url.'" />' ;
		}else{
			return $html;
			//return get_post_meta( $post_id , '_thumbnail_id' );
		}
	}
	
	/* Add EG FIX content trick */
	add_action( 'the_content' , 'wpvr_eg_content_hook_fix' );
	function wpvr_eg_content_hook_fix( $content ) {
		if( get_post_type() == WPVR_VIDEO_TYPE && WPVR_EG_FIX === TRUE ) {
			$content = preg_replace_callback( "/<iframe (.+?)<\/iframe>/" , function ( $matches ) {
				return str_replace( $matches[ 1 ] , '>' , $matches[ 0 ] );
			} , $content );
		}
		
		return $content;
	}
	
	add_filter( 'the_content' , 'wpvr_video_autoembed_function' , 100 );
	function wpvr_video_autoembed_function( $content ) {
		global $post , $wpvr_options , $wpvr_dynamics;
		//d( $wpvr_options );
		if( isset( $wpvr_dynamics[ 'autoembed_done' ] ) && $wpvr_dynamics[ 'autoembed_done' ] == 1 ) {
			return $content;
		}
		$disableAutoEmbed = get_post_meta( $post->ID , 'wpvr_video_disableAutoEmbed' , TRUE );
		if( $disableAutoEmbed == 'default' || $disableAutoEmbed == '' ) {
			$disableAutoEmbed = $wpvr_options[ 'autoEmbed' ] ? 'off' : 'on';
		}
		
		if( is_singular() && get_post_type() == WPVR_VIDEO_TYPE ) {
			//d( $disableAutoEmbed );
			
			if( $disableAutoEmbed == 'on' ) {
				return $content;
			}
			
			$embedCode = wpvr_render_modified_player( $post->ID );
			//d( $embedCode );
			$views = get_post_meta( $post->ID , 'wpvr_video_views' , TRUE );
			update_post_meta( $post->ID , 'wpvr_video_views' , $views + 1 );
			
			wpvr_update_dynamic_video_views( $post->ID , $views + 1 );
			$text_content = '';
			$text_content .= stripslashes( $wpvr_dynamics[ 'content_tags' ][ 'before' ] );
			$text_content .= $content;
			$text_content .= stripslashes( $wpvr_dynamics[ 'content_tags' ][ 'after' ] );
			
			
			if( $wpvr_options[ 'autoEmbed' ] ) {
				//$wpvr_dynamics[ 'autoembed_done' ] = 1;
				if( $wpvr_options[ 'removeVideoContent' ] ) {
					return $embedCode . ' <br/> ';
				} else {
					return $embedCode . ' <br/> ' . $text_content;
				}
			} else {
				return $text_content;
			}
			
		} else {
			return $content;
		}
		
	}
	
	
	
	
	/*************************************/
	
	//add_action( 'add_meta_boxes' , 'wpvr_adapt_cpt_meta_boxes' , 1000 );
	function wpvr_adapt_cpt_meta_boxes() {
		
		global $wp_meta_boxes , $post;
		$wpvr_mb = get_option( 'wpvr_mb' );
		if( $wpvr_mb == '' || $wpvr_mb == array() ) return FALSE;
		if( $post->post_type != WPVR_VIDEO_TYPE ) return FALSE;
		
		$theme = wp_get_theme(); // gets the current theme
		if( $theme->parent_theme == '' ) $theme_name = $theme->name;
		else $theme_name = $theme->parent_theme;
		if( ! isset( $wpvr_mb[ $theme_name ] ) ) return FALSE;
		$mbs = $wpvr_mb[ $theme_name ];
		
		foreach ( (array) $mbs[ 'side' ] as $id => $mb ) {
			$wp_meta_boxes[ WPVR_VIDEO_TYPE ][ 'side' ][ $mb[ 'level' ] ][ $mb[ 'id' ] ] = $mb;
		}
		
		foreach ( (array) $mbs[ 'normal' ] as $id => $mb ) {
			$wp_meta_boxes[ WPVR_VIDEO_TYPE ][ 'normal' ][ $mb[ 'level' ] ][ $mb[ 'id' ] ] = $mb;
		}
	}
	
	add_action( 'add_meta_boxes' , 'wpvr_update_cpt_meta_boxes' , 1000 );
	function wpvr_update_cpt_meta_boxes() {
		global $wp_meta_boxes , $wpvr_getmb_unsupported_themes;
		
		//d( $_GET );
		
		$theme = wp_get_theme(); // gets the current theme
		if( $theme->parent_theme == '' ) $theme_name = $theme->name;
		else $theme_name = $theme->parent_theme;
		
		
		if( in_array( $theme_name , $wpvr_getmb_unsupported_themes ) ) return FALSE;
		//d( $theme_name );
		
		
		//if( isset( $_GET[ 'wpvr_reset_mb' ] ) && $_GET[ 'wpvr_reset_mb' ] == '1' ) $wpvr_mb = array();
		if( isset( $_GET[ 'wpvr_clear_mb' ] ) && $_GET[ 'wpvr_clear_mb' ] == 1 ) {
			update_option( 'wpvr_mb' , array() );
			
			return FALSE;
		}
		if( ! isset( $_GET[ 'wpvr_get_mb' ] ) || $_GET[ 'wpvr_get_mb' ] != 1 ) return FALSE;
		//d( $_GET );
		
		$wpvr_mb = get_option( 'wpvr_mb' );
		if( $wpvr_mb == '' ) $wpvr_mb = array();
		if( isset( $_GET[ 'wpvr_reset_mb' ] ) && $_GET[ 'wpvr_reset_mb' ] == 1 ) $wpvr_mb = array();
		
		//if( isset( $wpvr_mb[ $theme_name ] ) ) return FALSE;
		$wpvr_mb[ $theme_name ] = array(
			'theme'  => $theme ,
			'normal' => array() ,
			'side'   => array() ,
		);
		
		$mb_post_types = apply_filters( 'wpvr_extend_mb_post_types' , array( 'post' ) );
		
		
		foreach ( (array) $mb_post_types as $post_type ) {
			
			//d( $post_type );
			if( ! isset( $wp_meta_boxes[ $post_type ] ) ) continue;
			//Cloning Normal metaboxes
			foreach ( (array) $wp_meta_boxes[ $post_type ][ 'normal' ] as $level => $mbs ) {
				//d( $mbs );
				foreach ( (array) $mbs as $mb ) {
					
					$mb[ 'level' ] = $level;
					
					$wpvr_mb[ $theme_name ][ 'normal' ][ $mb[ 'id' ] ] = $mb;
				}
			}
			//Cloning Side metaboxes
			foreach ( (array) $wp_meta_boxes[ $post_type ][ 'side' ] as $level => $mbs ) {
				//d( $mbs );
				foreach ( (array) $mbs as $mb ) {
					$mb[ 'level' ] = $level;
					
					$wpvr_mb[ $theme_name ][ 'side' ][ $mb[ 'id' ] ] = $mb;
				}
			}
		}
		//d( $wpvr_mb );
		update_option( 'wpvr_mb' , $wpvr_mb );
		
		$msg  = __( 'New Theme Metaboxes detected and added.' , WPVR_LANG ) . '<br/>' .
		        __( 'You can now handle your imported videos as any regular Wordpress post.' ) . '<br/><br/>' .
		        '<a id="wpvr_get_mb_close" href="#">' . __( 'Close' , WPVR_LANG ) . '</a>';
		$slug = wpvr_add_notice( array(
			'title'     => 'WP Video Robot : ' ,
			'class'     => 'updated' , //updated or warning or error
			'content'   => $msg ,
			'hidable'   => FALSE ,
			'is_dialog' => FALSE ,
			'show_once' => TRUE ,
			'color'     => '#27A1CA' ,
			'icon'      => 'fa-cube' ,
		) );
		wpvr_render_notice( $slug );
		wpvr_remove_notice( $slug );
		
		?>
		<style>
			#poststuff {
				display: none;
			}
			
			.wrap h1 {
				visibility: hidden;
			}
		</style>
		<?php
		
		
	}