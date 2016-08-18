<?php
	
	/* Add WP Video Robot shortcode for embeding videos on post */
	add_shortcode( 'cutv_' , 'cutv__embed_shortcode' );
	function cutv__embed_shortcode( $atts ) {
		$cutv__video_id      = get_post_meta( $atts[ 'id' ] , 'cutv__video_id' , TRUE );
		$cutv__video_service = get_post_meta( $atts[ 'id' ] , 'cutv__video_service' , TRUE );
		$player             = cutv__video_embed(
			$cutv__video_id ,
			$atts[ 'id' ] ,
			FALSE ,
			$cutv__video_service
		);
		$embedCode          = '<div class="wpvr__embed">' . $player . '</div>';
		//new dBug( $cutv__video_id);
		$views = get_post_meta( $atts[ 'id' ] , 'cutv__video_views' , TRUE );
		update_post_meta( $atts[ 'id' ] , 'cutv__video_views' , $views + 1 );
		cutv__update_dynamic_video_views( $atts[ 'id' ] , $views + 1 );
		$embedCode = apply_filters( 'cutv__replace_player_code' , $embedCode , $atts[ 'id' ] );
		
		return $embedCode;
	}
	
	/* Add WP Video Robot shortcode for embeding videos on post */
	add_shortcode( 'cutv__views' , 'cutv__views_shortcode' );
	function cutv__views_shortcode( $atts ) {
		$cutv__video_views = get_post_meta( $atts[ 'id' ] , 'cutv__video_views' , TRUE );
		
		return $cutv__video_views;
	}