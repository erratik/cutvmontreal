<?php
	
	/* Add WP Video Robot shortcode for embeding videos on post */
	add_shortcode( 'wpvr' , 'wpvr_embed_shortcode' );
	function wpvr_embed_shortcode( $atts ) {
		$wpvr_video_id      = get_post_meta( $atts[ 'id' ] , 'wpvr_video_id' , TRUE );
		$wpvr_video_service = get_post_meta( $atts[ 'id' ] , 'wpvr_video_service' , TRUE );
		$player             = wpvr_video_embed(
			$wpvr_video_id ,
			$atts[ 'id' ] ,
			FALSE ,
			$wpvr_video_service
		);
		$embedCode          = '<div class="wpvr_embed">' . $player . '</div>';
		//new dBug( $wpvr_video_id);
		$views = get_post_meta( $atts[ 'id' ] , 'wpvr_video_views' , TRUE );
		update_post_meta( $atts[ 'id' ] , 'wpvr_video_views' , $views + 1 );
		wpvr_update_dynamic_video_views( $atts[ 'id' ] , $views + 1 );
		$embedCode = apply_filters( 'wpvr_replace_player_code' , $embedCode , $atts[ 'id' ] );
		
		return $embedCode;
	}
	
	/* Add WP Video Robot shortcode for embeding videos on post */
	add_shortcode( 'wpvr_views' , 'wpvr_views_shortcode' );
	function wpvr_views_shortcode( $atts ) {
		$wpvr_video_views = get_post_meta( $atts[ 'id' ] , 'wpvr_video_views' , TRUE );
		
		return $wpvr_video_views;
	}