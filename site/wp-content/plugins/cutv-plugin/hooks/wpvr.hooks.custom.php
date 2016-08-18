<?php
	
	add_action( 'cutv__event_add_video_done' , 'cutv__add_notice_trigger_function' , 10 , 1 );
	function cutv__add_notice_trigger_function( $count_videos ) {
		if( cutv__ASK_TO_RATE_TRIGGER === FALSE ) {
			return FALSE;
		}
		global $current_user;
		$user_id = $current_user->ID;
		
		//update_option('koko' , $count_videos );
		
		if( get_user_meta( $user_id , 'cutv__user_has_voted' , TRUE ) == 1 ) {
			return FALSE;
		}
		$level_reached = cutv__is_reaching_level( $count_videos );
		if( $level_reached != FALSE ) {
			$message = "<p class='cutv__dialog_icon'><i class='fa fa-trophy'></i></p>" .
			           "<div class='cutv__dialog_msg'>" .
			           "<p>Hey, you just have crossed <strong>$count_videos</strong> videos imported with cutv_. That's Awesome !</p>" .
			           "<p>Could you please do us a big favor and give WP Video Robot a 5-star rating on Codecanyon ?" .
			           "<br/>That will help us spread the word and boost our motivation.</p>" .
			           "<strong>~pressaholic</strong>" .
			           "</div>";
			
			$token = bin2hex( openssl_random_pseudo_bytes( 16 ) );
			
			/*$cutv__notices = get_option('cutv__notices');
			foreach( $cutv__notices as $id=>$notice){
				if( strpos($id , 'rating_notice_' ) != false )  unset( $cutv__notices[ $id ] );
			}
			update_option('cutv__notices' , $cutv__notices);
			*/
			
			
			cutv__add_notice( array(
				'slug'               => "rating_notice_" . $level_reached ,
				'title'              => 'Congratulations !' ,
				'class'              => 'updated' , //updated or warning or error
				'content'            => $message ,
				'hidable'            => TRUE ,
				'is_dialog'          => TRUE ,
				'dialog_modal'       => FALSE ,
				'dialog_delay'       => 1500 ,
				//'dialog_ok_button' => '',
				'dialog_ok_button'   => ' <i class="fa fa-heart"></i> RATE cutv_ NOW' ,
				'dialog_hide_button' => '<i class="fa fa-close"></i> DISMISS ' ,
				'dialog_class'       => ' askToRate ' ,
				'dialog_ok_url'      => 'http://codecanyon.net/downloads#item-8619739' ,
			) );
			
		}
		
	}
	
	add_action( 'wp_trash_post' , 'cutv__add_unwanted_on_trash' );
	function cutv__add_unwanted_on_trash( $post_id ) {
		global $cutv__options;
		if( get_post_type( $post_id ) == cutv__VIDEO_TYPE && $cutv__options[ 'unwantOnTrash' ] === TRUE ) {
			cutv__unwant_videos( array( $post_id ) );
		}
	}
	
	add_action( 'before_delete_post' , 'cutv__add_unwanted_on_delete' );
	function cutv__add_unwanted_on_delete( $post_id ) {
		global $cutv__options;
		if( get_post_type( $post_id ) == cutv__VIDEO_TYPE && $cutv__options[ 'unwantOnDelete' ] === TRUE ) {
			cutv__unwant_videos( array( $post_id ) );
		}
	}
	
	add_action( 'admin_init' , 'cutv__demo_message_ignore' );
	function cutv__demo_message_ignore() {
		global $current_user;
		$user_id = $current_user->ID;
		/* If user clicks to ignore the notice, add that to their user meta */
		if( isset( $_GET[ 'cutv__show_demo_notice' ] ) && '0' == $_GET[ 'cutv__show_demo_notice' ] ) {
			add_user_meta( $user_id , 'cutv__show_demo_notice' , 'true' , TRUE );
		}
		
		if( isset( $_GET[ 'cutv__hide_notice' ] ) && $_GET[ 'cutv__hide_notice' ] != '' ) {
			add_user_meta( $user_id , $_GET[ 'cutv__hide_notice' ] , 'true' , TRUE );
		}
		
	}
	
	/* Define Custom Dashboard Widgets */
	add_action( 'wp_dashboard_setup' , 'cutv__custom_dashboard_widget' );
	function cutv__custom_dashboard_widget() {
		global $wp_meta_boxes;
		wp_add_dashboard_widget(
			'home_dashboard_widget' , //ID of the dashboard Widgets
			'WP Video Robot - Global Activity' , //Title of the dashboard Widgets
			'cutv__custom_dashboard_function' ,
			'side' ,
			'high'
		);
	}
	
	/* Define hourly running event */
	//add_action( 'cutv__hourly_event' , 'cutv__run_hourly' );
	//function cutv__run_hourly() {
	//	global $cutv__options;
	//	if( ! $cutv__options[ 'useCronTab' ] ) {
	//		include( cutv__PATH . 'cutv_.cron.php' );
	//	}
	//}
	
	
	/* Function to prevent from showing content on loops */
	add_action( 'the_content' , 'cutv__remove_flow_content' );
	function cutv__remove_flow_content( $html ) {
		if(
			is_admin()
			|| ! defined( 'cutv__REMOVE_FLOW_CONTENT' )
			|| cutv__REMOVE_FLOW_CONTENT === FALSE
			|| get_post_type() != cutv__VIDEO_TYPE
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
	add_action( 'term_links-post_tag' , 'cutv__remove_flow_tags' );
	function cutv__remove_flow_tags( $tags ) {
		if(
			is_admin()
			|| ! defined( 'cutv__REMOVE_FLOW_TAGS' )
			|| cutv__REMOVE_FLOW_TAGS === FALSE
			|| get_post_type() != cutv__VIDEO_TYPE
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
	add_action( 'post_thumbnail_html' , 'cutv__remove_thumb_single_function' );
	function cutv__remove_thumb_single_function( $html ) {
		if(
			is_admin()
			|| ! is_singular()
			|| ! defined( 'cutv__REMOVE_THUMB_SINGLE' )
			|| cutv__REMOVE_THUMB_SINGLE === FALSE
			|| get_post_type() != cutv__VIDEO_TYPE
		) {
			return $html;
		} else {
			return '';
		}
	}
	
	/* Function for replacing post thumbnail by embeded video player */
	add_action( 'post_thumbnail_html' , 'cutv__video_thumbnail_embed' , 20 , 2 );
	function cutv__video_thumbnail_embed( $html , $post_id ) {
		global $cutv__options , $cutv__is_admin;
		if( get_post_type() != cutv__VIDEO_TYPE ) return $html;
		if( $cutv__is_admin === TRUE || is_admin() || $cutv__options[ 'videoThumb' ] === FALSE ) {
			return $html;
		} else {
			if( is_singular() ) {
				return $html;
			} else {
				$cutv__video_id = get_post_meta( $post_id , 'cutv__video_id' , TRUE );
				$cutv__service  = get_post_meta( $post_id , 'cutv__video_service' , TRUE );
				$player        = cutv__video_embed(
					$cutv__video_id ,
					$post_id ,
					$autoPlay = FALSE ,
					$cutv__service
				);
				$embedCode     = '<div class="wpvr__embed">' . $player . '</div>';
				
				return $embedCode;
			}
		}
	}

	/* Function for replacing post thumbnail by embeded video player */
	add_filter( 'post_thumbnail_html' , 'cutv__video_thumbnail_use_service_thumb' , 20 , 2 );
	function cutv__video_thumbnail_use_service_thumb( $html , $post_id ) {
		global $cutv__options , $cutv__is_admin;
		if( get_post_type() != cutv__VIDEO_TYPE ) return $html;
		if( !cutv__DISABLE_THUMBS_DOWNLOAD) return $html;

		if( get_post_meta( $post_id , '_thumbnail_id' , TRUE ) == '' ){
			$service_image_url = get_post_meta( $post_id , 'cutv__video_service_thumb' , TRUE );
			return '<img src="'.$service_image_url.'" />' ;
		}else{
			return $html;
			//return get_post_meta( $post_id , '_thumbnail_id' );
		}
	}
	
	/* Add EG FIX content trick */
	add_action( 'the_content' , 'cutv__eg_content_hook_fix' );
	function cutv__eg_content_hook_fix( $content ) {
		if( get_post_type() == cutv__VIDEO_TYPE && cutv__EG_FIX === TRUE ) {
			$content = preg_replace_callback( "/<iframe (.+?)<\/iframe>/" , function ( $matches ) {
				return str_replace( $matches[ 1 ] , '>' , $matches[ 0 ] );
			} , $content );
		}
		
		return $content;
	}
	
	add_filter( 'the_content' , 'cutv__video_autoembed_function' , 100 );
	function cutv__video_autoembed_function( $content ) {
		global $post , $cutv__options , $cutv__dynamics;
		//d( $cutv__options );
		if( isset( $cutv__dynamics[ 'autoembed_done' ] ) && $cutv__dynamics[ 'autoembed_done' ] == 1 ) {
			return $content;
		}
		$disableAutoEmbed = get_post_meta( $post->ID , 'cutv__video_disableAutoEmbed' , TRUE );
		if( $disableAutoEmbed == 'default' || $disableAutoEmbed == '' ) {
			$disableAutoEmbed = $cutv__options[ 'autoEmbed' ] ? 'off' : 'on';
		}
		
		if( is_singular() && get_post_type() == cutv__VIDEO_TYPE ) {
			//d( $disableAutoEmbed );
			
			if( $disableAutoEmbed == 'on' ) {
				return $content;
			}
			
			$embedCode = cutv__render_modified_player( $post->ID );
			//d( $embedCode );
			$views = get_post_meta( $post->ID , 'cutv__video_views' , TRUE );
			update_post_meta( $post->ID , 'cutv__video_views' , $views + 1 );
			
			cutv__update_dynamic_video_views( $post->ID , $views + 1 );
			$text_content = '';
			$text_content .= stripslashes( $cutv__dynamics[ 'content_tags' ][ 'before' ] );
			$text_content .= $content;
			$text_content .= stripslashes( $cutv__dynamics[ 'content_tags' ][ 'after' ] );
			
			
			if( $cutv__options[ 'autoEmbed' ] ) {
				//$cutv__dynamics[ 'autoembed_done' ] = 1;
				if( $cutv__options[ 'removeVideoContent' ] ) {
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
	
	//add_action( 'add_meta_boxes' , 'cutv__adapt_cpt_meta_boxes' , 1000 );
	function cutv__adapt_cpt_meta_boxes() {
		
		global $wp_meta_boxes , $post;
		$cutv__mb = get_option( 'cutv__mb' );
		if( $cutv__mb == '' || $cutv__mb == array() ) return FALSE;
		if( $post->post_type != cutv__VIDEO_TYPE ) return FALSE;
		
		$theme = wp_get_theme(); // gets the current theme
		if( $theme->parent_theme == '' ) $theme_name = $theme->name;
		else $theme_name = $theme->parent_theme;
		if( ! isset( $cutv__mb[ $theme_name ] ) ) return FALSE;
		$mbs = $cutv__mb[ $theme_name ];
		
		foreach ( (array) $mbs[ 'side' ] as $id => $mb ) {
			$wp_meta_boxes[ cutv__VIDEO_TYPE ][ 'side' ][ $mb[ 'level' ] ][ $mb[ 'id' ] ] = $mb;
		}
		
		foreach ( (array) $mbs[ 'normal' ] as $id => $mb ) {
			$wp_meta_boxes[ cutv__VIDEO_TYPE ][ 'normal' ][ $mb[ 'level' ] ][ $mb[ 'id' ] ] = $mb;
		}
	}
	
	add_action( 'add_meta_boxes' , 'cutv__update_cpt_meta_boxes' , 1000 );
	function cutv__update_cpt_meta_boxes() {
		global $wp_meta_boxes , $cutv__getmb_unsupported_themes;
		
		//d( $_GET );
		
		$theme = wp_get_theme(); // gets the current theme
		if( $theme->parent_theme == '' ) $theme_name = $theme->name;
		else $theme_name = $theme->parent_theme;
		
		
		if( in_array( $theme_name , $cutv__getmb_unsupported_themes ) ) return FALSE;
		//d( $theme_name );
		
		
		//if( isset( $_GET[ 'cutv__reset_mb' ] ) && $_GET[ 'cutv__reset_mb' ] == '1' ) $cutv__mb = array();
		if( isset( $_GET[ 'cutv__clear_mb' ] ) && $_GET[ 'cutv__clear_mb' ] == 1 ) {
			update_option( 'cutv__mb' , array() );
			
			return FALSE;
		}
		if( ! isset( $_GET[ 'cutv__get_mb' ] ) || $_GET[ 'cutv__get_mb' ] != 1 ) return FALSE;
		//d( $_GET );
		
		$cutv__mb = get_option( 'cutv__mb' );
		if( $cutv__mb == '' ) $cutv__mb = array();
		if( isset( $_GET[ 'cutv__reset_mb' ] ) && $_GET[ 'cutv__reset_mb' ] == 1 ) $cutv__mb = array();
		
		//if( isset( $cutv__mb[ $theme_name ] ) ) return FALSE;
		$cutv__mb[ $theme_name ] = array(
			'theme'  => $theme ,
			'normal' => array() ,
			'side'   => array() ,
		);
		
		$mb_post_types = apply_filters( 'cutv__extend_mb_post_types' , array( 'post' ) );
		
		
		foreach ( (array) $mb_post_types as $post_type ) {
			
			//d( $post_type );
			if( ! isset( $wp_meta_boxes[ $post_type ] ) ) continue;
			//Cloning Normal metaboxes
			foreach ( (array) $wp_meta_boxes[ $post_type ][ 'normal' ] as $level => $mbs ) {
				//d( $mbs );
				foreach ( (array) $mbs as $mb ) {
					
					$mb[ 'level' ] = $level;
					
					$cutv__mb[ $theme_name ][ 'normal' ][ $mb[ 'id' ] ] = $mb;
				}
			}
			//Cloning Side metaboxes
			foreach ( (array) $wp_meta_boxes[ $post_type ][ 'side' ] as $level => $mbs ) {
				//d( $mbs );
				foreach ( (array) $mbs as $mb ) {
					$mb[ 'level' ] = $level;
					
					$cutv__mb[ $theme_name ][ 'side' ][ $mb[ 'id' ] ] = $mb;
				}
			}
		}
		//d( $cutv__mb );
		update_option( 'cutv__mb' , $cutv__mb );
		
		$msg  = __( 'New Theme Metaboxes detected and added.' , cutv__LANG ) . '<br/>' .
		        __( 'You can now handle your imported videos as any regular Wordpress post.' ) . '<br/><br/>' .
		        '<a id="cutv__get_mb_close" href="#">' . __( 'Close' , cutv__LANG ) . '</a>';
		$slug = cutv__add_notice( array(
			'title'     => 'WP Video Robot : ' ,
			'class'     => 'updated' , //updated or warning or error
			'content'   => $msg ,
			'hidable'   => FALSE ,
			'is_dialog' => FALSE ,
			'show_once' => TRUE ,
			'color'     => '#27A1CA' ,
			'icon'      => 'fa-cube' ,
		) );
		cutv__render_notice( $slug );
		cutv__remove_notice( $slug );
		
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