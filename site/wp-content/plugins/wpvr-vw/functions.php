<?php
	
	
	function wpvr_vw_render_videos( $videos , $wargs ) {

		if( ! is_array( $videos ) || count( $videos ) == 0 ) {
			echo '<p style="text-align:center;">Nothing to show.</p>';

			return FALSE;
		}
		
		?>
		<div class = "wpvr_widget_wrapper">
			<?php foreach ( (array) $videos as $video ) { ?>
				<div class = "wpvr_widget_video">
					<?php if( $wargs[ 'show_thumb' ] === TRUE ) { ?>
						<div class = "wpvr_widget_video_head">
							<a href = "<?php echo $video[ 'permalink' ]; ?>">

								<?php if( $wargs[ 'show_views' ] === TRUE ) { ?>
									<div class = "wpvr_widget_video_views">
										<?php echo wpvr_numberK( $video[ 'views' ] , FALSE ); ?>
										<?php echo ' ' . $wargs[ 'show_views_label' ]; ?>
									</div>
								<?php } ?>

								<?php if( $wargs[ 'show_playicon' ] === TRUE ) { ?>
									<div class = "wpvr_widget_video_play">

									</div>
								<?php } ?>

								<?php if( $wargs[ 'show_duration' ] === TRUE ) { ?>
									<div class = "wpvr_widget_video_duration">
										<?php echo $video[ 'duration' ]; ?>
									</div>
								<?php } ?>

								<div class = "wpvr_widget_video_thumb">
									<img class = "wpvr_video_thumb" src = "<?php echo $video[ 'thumb' ]; ?>"/>
								</div>
							
							</a>
						</div>
					<?php } ?>
					
					<?php if( $wargs[ 'show_title' ] === TRUE ) { ?>
						<div class = "wpvr_widget_video_title">
							<a href = "<?php echo $video[ 'permalink' ]; ?>">
								<?php echo $video[ 'title' ]; ?>
							</a>
						</div>
					<?php } ?>
					
					<?php if( $wargs[ 'show_excerpt' ] === TRUE ) { ?>
						<div class = "wpvr_widget_video_excerpt">
							<?php
								$exc = substr( $video[ 'content' ] , 0 , 100 );
								if( strlen( $video[ 'content' ] ) > 100 ) echo $exc . ' [...] ';
								else $exc;
							?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php
	}
	
	function wpvr_vw_get_args_from_string( $string ) {
		global $wpvr_vw_settings;
		$obj       = json_decode( stripslashes( $string ) );
		$full_data = array();
		foreach ( (array) $obj as $setting ) {
			$x = explode( '[]' , $setting->name );
			if( $x[ 0 ] != $setting->name ) {
				//multidim
				if( ! isset( $full_data[ $x[ 0 ] ] ) ) $full_data[ $x[ 0 ] ] = array();
				$full_data[ $x[ 0 ] ][] = $setting->value;
			} else {
				//linear
				$full_data[ $setting->name ] = $setting->value;
			}
		}
		
		foreach ( (array) $wpvr_vw_settings as $section_settings ) {
			foreach ( (array) $section_settings as $ws ) {
				if( isset( $ws[ 'type' ] ) && $ws[ 'type' ] == 'switch' && ! isset( $full_data[ $ws[ 'id' ] ] ) )
					$full_data[ $ws[ 'id' ] ] = FALSE;
				
				if( isset( $ws[ 'type' ] ) && $ws[ 'type' ] == 'switch' && isset( $full_data[ $ws[ 'id' ] ] ) && $full_data[ $ws[ 'id' ] ] == 'on' )
					$full_data[ $ws[ 'id' ] ] = TRUE;
				
			}
		}

		return $full_data;
	}

	function wpvr_vw_get_args( $encoded_data ) {

		$args = (array) json_decode( base64_decode( stripslashes( $encoded_data ) ) );
		//_d( $args );
		$args = wpvr_extend( $args , array(
			'widget_title'      => '' ,
			'show_widget_title' => TRUE ,

			'post_types' => '' ,
			'categories' => '' ,
			'tags'       => '' ,
			'authors'    => '' ,
			'count'      => 5 ,
			'order'      => 'desc' ,
			'orderby'    => 'post_id' ,

			'show_thumb'       => TRUE ,
			'show_title'       => TRUE ,
			'show_duration'    => TRUE ,
			'show_views'       => FALSE ,
			'show_views_label' => 'views' ,
			'show_excerpt'     => FALSE ,
			'show_morelink'    => FALSE ,
			'show_playicon'    => FALSE ,

			'widget_theme' => '1' ,
		) );
		foreach ( (array) $args as $arg_id => $arg ) {
			if( is_array( $arg ) && $arg == array( '0' => '0' ) ) $args[ $arg_id ] = array();
		}
		
		return $args;
		
		
	}
	
	function wpvr_vw_render_form( $encoded_data ) {
		global $wpvr_vw_settings;
		$args = wpvr_vw_get_args( $encoded_data );
		
		$query_settings = $content_settings = $theme_settings = '';

		foreach ( (array) $wpvr_vw_settings[ 'query' ] as $option ) {

			if( ! isset( $option[ 'id' ] ) && is_array( $option ) ) {
				foreach ( (array) $option as $suboption ) {
					if( ! isset( $args[ $suboption[ 'id' ] ] ) ) $args[ $suboption[ 'id' ] ] = '';
					$query_settings .= wpvr_addon_option_render( $suboption , $args[ $suboption[ 'id' ] ] , FALSE );
				}
			} else {
				if( ! isset( $args[ $option[ 'id' ] ] ) ) $args[ $option[ 'id' ] ] = '';
				$query_settings .= wpvr_addon_option_render( $option , $args[ $option[ 'id' ] ] , FALSE );
			}
		}
		foreach ( (array) $wpvr_vw_settings[ 'content' ] as $option ) {
			if( ! isset( $args[ $option[ 'id' ] ] ) ) $args[ $option[ 'id' ] ] = '';
			$content_settings .= wpvr_addon_option_render( $option , $args[ $option[ 'id' ] ] , FALSE );
		}
		foreach ( (array) $wpvr_vw_settings[ 'theme' ] as $option ) {
			if( ! isset( $args[ $option[ 'id' ] ] ) ) $args[ $option[ 'id' ] ] = '';
			$theme_settings .= wpvr_addon_option_render( $option , $args[ $option[ 'id' ] ] , FALSE );
		}
		
		$form
			= '
			<!-- TABS -->
			<div class="wpvr_nav_tabs pull-left">
				<div class="wpvr_nav_tab pull-left noMargin active" id="content"> 
					<i class="wpvr_tab_icon fa fa-flask"></i><br/>
					' . __( 'Widget Content' , WPVR_LANG ) . '
				</div>
				
				<div class="wpvr_nav_tab pull-left noMargin" id="query"> 
					<i class="wpvr_tab_icon fa fa-database"></i><br/>
					' . __( 'Widget Query' , WPVR_LANG ) . '
				</div>
				
				<div class="wpvr_nav_tab pull-left noMargin" id="theme" style="display:none;"> 
					<i class="wpvr_tab_icon fa fa-paint-brush"></i><br/>
					' . __( 'Widget Theme' , WPVR_LANG ) . '
				</div>
			</div>
			<!-- TABS -->
			<div class="wpvr_clearfix"></div>
			
			<form class="wpvr_widget_form_data">
			
				
				<div id="" class="wpvr_nav_tab_content tab_query">
					' . $query_settings . '
				</div>
				
				<div id="" class="wpvr_nav_tab_content tab_content active">
					' . $content_settings . '
				</div>
				
				<div id="" class="wpvr_nav_tab_content tab_theme">
					' . $theme_settings . '
				</div>
				
			</form>
			<div class="wpvr_clearfix"></div>
		';
		
		return $form;
		
	}

	function wpvr_vw_get_videos( $wargs ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		$videos     = array();
		$query_args = array();

		$query_args[ 'author__in' ]     = $wargs[ 'authors' ];
		$query_args[ 'category__in' ]   = $wargs[ 'categories' ];
		$query_args[ 'tag__in' ]        = $wargs[ 'tags' ];
		$query_args[ 'posts_per_page' ] = $wargs[ 'count' ];
		$query_args[ 'orderby' ]        = $wargs[ 'orderby' ];
		$query_args[ 'order' ]          = $wargs[ 'order' ];
		$query_args[ 'tax_query ' ]     = array();

		foreach ( (array) $wargs as $warg_id => $warg ) {
			if( count( $warg ) > 1 && substr( $warg_id , 0 , 4 ) == 'tax_' ) {
				$taxname = substr( $warg_id , 4 , strlen( $warg_id ) );
				if( ! isset( $query_args[ 'tax_query' ][ 'relation' ] ) ) {
					$query_args[ 'tax_query' ][ 'relation' ] = 'AND';
				}
				if( isset( $warg[ 0 ] ) ) unset( $warg[ 0 ] );
				$query_args[ 'tax_query' ][] = array(
					'taxonomy' => $taxname ,
					'field'    => 'term_id' ,
					'terms'    => $warg ,
					'operator' => 'IN' ,
				);
			}
		}
		//_d( $wargs );
		if( $post_type == WPVR_VIDEO_TYPE ) {
			$query_args[ 'post__not_in' ] = array( $post_id );
		}
		if( $post_type == WPVR_VIDEO_TYPE && $wargs[ 'related' ] == 'cats' ) {
			$related_categories = wp_get_post_categories( $post_id );
			if( count( $related_categories ) > 1 ) {
				$query_args[ 'category__in' ] = array_merge( $query_args[ 'category__in' ] , $related_categories );
			}
		} elseif( $post_type == WPVR_VIDEO_TYPE && $wargs[ 'related' ] == 'tags' ) {
			$related_tags = wp_get_post_terms( $post_id );
			if( count( $related_tags ) > 1 ) {
				$query_args[ 'tag__in' ] = array_merge( $query_args[ 'tag__in' ] , $related_tags );
			}
		} elseif( $post_type == WPVR_VIDEO_TYPE && $wargs[ 'related' ] != 'none' ) {
			$related_terms = array();
			$terms         = wp_get_post_terms( $post_id , $wargs[ 'related' ] );
			foreach ( (array) $terms as $term ) {
				$related_terms[] = $term->term_id;
			}
			if( count( $related_terms ) > 1 ) {
				$query_args[ 'tax_query' ][] = array(
					'taxonomy' => $wargs[ 'related' ] ,
					'field'    => 'term_id' ,
					'terms'    => $related_terms ,
					'operator' => 'IN' ,
				);
			}
		}

		//_d( $related_categories );

		if( isset( $wargs[ 'post_types' ] ) && $wargs[ 'post_types' ] != array() ) {
			$query_args[ 'post_type' ] = $wargs[ 'post_types' ];
		} else {
			$query_args[ 'post_type' ] = array( WPVR_VIDEO_TYPE );
		}

		//_d( $query_args );
		$q = new WP_Query( $query_args );
		if( $q->found_posts == 0 ) return $videos;
		foreach ( $q->posts as $video ) {
			if( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $video->ID ) ) {
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $video->ID ) , 'full' );
				if( ! $thumbnail[ 0 ] ) $thumb = '';
				else $thumb = $thumbnail[ 0 ];
			} else $thumb = '';


			$videos[] = array(
				'id'        => $video->ID ,
				'title'     => $video->post_title ,
				'excerpt'   => $video->post_excerpt ,
				'content'   => $video->post_content ,
				'permalink' => $video->guid ,
				'thumb'     => $thumb ,
				'views'     => get_post_meta( $video->ID , 'wpvr_video_views' , TRUE ) ,
				'duration'  => wpvr_get_duration( $video->ID ) ,
			);
		}

		return $videos;


		//return $args ;

	}
