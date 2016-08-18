<?php


	add_action( 'admin_head' , 'wpvr_vw_load_scripts' );
	function wpvr_vw_load_scripts() {
		wp_register_script( 'wpvr_vw_scripts' , WPVR_VW_URL . 'assets/scripts.js' );
	}
	

	add_action( 'wp_head' , 'wpvr_vw_load_dynamic_css' , 100 );
	function wpvr_vw_load_dynamic_css() {
		$css = '';
		$css .= '';
		wp_enqueue_style( 'wpvr_vw_custom_styles' , WPVR_VW_URL . 'assets/custom.css' );
		?>
		<style>
			<?php echo $css; ?>
		</style>
		<?php
	}

	add_action( 'init' , 'wpvr_vw_define_settings' , 1000 );
	function wpvr_vw_define_settings() {
		global $wpvr_vw_settings;

		$wpvr_vw_settings = array(
			'query'   => array() ,
			'content' => array() ,
			'theme'   => array() ,
		);

		$related_values = array(
			'none' => '- Disabled' ,
			'cats' => __( 'Same categories' , WPVR_VW_ID ) ,
			'tags' => __( 'Same tags' , WPVR_VW_ID ) ,

		);


		$wpvr_vw_settings[ 'query' ][ 'count' ] = array(
			'id'    => 'count' ,
			'label' => __( 'Videos Count' , WPVR_VW_ID ) ,
			'desc'  => __( 'Choose how many videos to show on that widget.' , WPVR_VW_ID ) ,
			'type'  => 'text_small' ,

		);

		$wpvr_vw_settings[ 'query' ][ 'categories' ] = array(
			'id'          => 'categories' ,
			'label'       => __( 'Video Categories' , WPVR_VW_ID ) ,
			'maxItems'    => '255' ,
			'placeholder' => __( 'Pick one or more categories' , WPVR_VW_ID ) ,
			'values'      => array() ,
			'source'      => 'categories' ,
			'desc'        => __( 'Show only videos that are associated with these categories. Leave empty for all categories.' , WPVR_VW_ID ) ,
			'type'        => 'multiselect' ,
		);

		$wpvr_vw_settings[ 'query' ][ 'taxonomies' ] = array();
		$taxonomies                                  = get_taxonomies( array(
			'_builtin' => FALSE ,
		) , 'objects' );
		foreach ( (array) $taxonomies as $tax ) {
			if( $tax->name == WPVR_SFOLDER_TYPE ) continue;
			$tax_options                                   = wpvr_get_taxonomy_terms( $tax->name );
			$wpvr_vw_settings[ 'query' ][ 'taxonomies' ][] = array(
				'id'          => 'tax_' . $tax->name ,
				'label'       => '- Taxonomy : ' . $tax->label ,
				'maxItems'    => '255' ,
				'placeholder' => sprintf( __( 'Pick one or more %s' , WPVR_VW_ID ) , $tax->label ) ,
				'values'      => $tax_options ,
				'desc'        => __( 'Show videos that are associated with this custom taxonomy. Leave empty for all.' , WPVR_VW_ID ) ,
				'type'        => 'multiselect' ,
			);

			$related_values[ $tax->name ] = sprintf( __( 'Same %s' , WPVR_VW_ID ) , $tax->label );

		}

		$wpvr_vw_settings[ 'query' ][ 'authors' ] = array(
			'id'          => 'authors' ,
			'label'       => __( 'Authors' , WPVR_VW_ID ) ,
			'maxItems'    => '255' ,
			'placeholder' => __( 'Pick one or more author' , WPVR_VW_ID ) ,
			'values'      => array() ,
			'source'      => 'authors' ,
			'desc'        => __( 'Show only videos associated with these authors. Leave empty for all authors.' , WPVR_VW_ID ) ,
			'type'        => 'multiselect' ,
		);

		$wpvr_vw_settings[ 'query' ][ 'post_types' ] = array(
			'id'          => 'post_types' ,
			'label'       => __( 'Post Types' , WPVR_VW_ID ) ,
			'maxItems'    => '255' ,
			'placeholder' => __( 'Pick one or more post type' , WPVR_VW_ID ) ,
			'values'      => array() ,
			'source'      => 'post_types' ,
			'desc'        => __( 'Show only videos with these post types. Leave empty for all post types.' , WPVR_VW_ID ) ,
			'type'        => 'multiselect' ,

		);

		$wpvr_vw_settings[ 'query' ][ 'tags' ] = array(
			'id'          => 'tags' ,
			'label'       => __( 'Tags' , WPVR_VW_ID ) ,
			'maxItems'    => '255' ,
			'placeholder' => __( 'Pick one or more tag' , WPVR_VW_ID ) ,
			'values'      => array() ,
			'source'      => 'tags' ,
			'desc'        => __( 'Show only videos associated with these tags. Leave empty for all tags.' , WPVR_VW_ID ) ,
			'type'        => 'multiselect' ,

		);

		$wpvr_vw_settings[ 'query' ][ 'order' ] = array(
			'id'     => 'order' ,
			'label'  => __( 'Order' , WPVR_VW_ID ) ,
			'values' => array(
				'desc' => __( 'Descending' , WPVR_VW_ID ) ,
				'asc'  => __( 'Ascending' , WPVR_VW_ID ) ,
			) ,
			'desc'   => __( 'Designates the ascending or descending order of the retreived videos.' , WPVR_VW_ID ) ,
			'type'   => 'select' ,

		);

		$wpvr_vw_settings[ 'query' ][ 'orderby' ] = array(
			'id'     => 'orderby' ,
			'label'  => __( 'Order By' , WPVR_VW_ID ) ,
			'values' => array(
				'post_id' => 'Post ID' ,
				'date'    => 'Date' ,
				'title'   => 'Title' ,
				'views'   => 'Views' ,
				'random'  => 'Random' ,
			) ,
			'desc'   => __( 'Choose the parameter you want to use to sort retreived videos.' , WPVR_VW_ID ) ,
			'type'   => 'select' ,
		);

		$wpvr_vw_settings[ 'query' ][ 'related' ] = array(
			'id'     => 'related' ,
			'label'  => __( 'Current Post Related' , WPVR_VW_ID ) ,
			'values' => $related_values ,
			'desc'   => __( 'Choose whether to retreive only the videos that are related to the current video.' , WPVR_VW_ID ) ,
			'type'   => 'select' ,
		);

		$wpvr_vw_settings[ 'content' ] = array(
			'show_widget_title' => array(
				'id'       => 'show_widget_title' ,
				'label'    => 'Enable Widget Title' ,
				'desc'     => __( 'Choose whether to show or hide the widget title . ' , WPVR_VW_ID ) ,
				'type'     => 'switch' ,
				'masterOf' => array( 'widget_title' ) ,
			) ,

			'widget_title' => array(
				'id'    => 'widget_title' ,
				'label' => 'Widget Title' ,
				'desc'  => __( 'The title of your widget . ' , WPVR_VW_ID ) ,
				'type'  => 'text' ,
			) ,

			'show_thumb' => array(
				'id'    => 'show_thumb' ,
				'label' => 'Enable Video Thumbnail' ,
				'desc'  => __( 'Choose whether to show or hide the video thumbnail . ' , WPVR_VW_ID ) ,
				'type'  => 'switch' ,
			) ,

			'show_title' => array(
				'id'    => 'show_title' ,
				'label' => 'Enable Video Title' ,
				'desc'  => __( 'Choose whether to show or hide the video title . ' , WPVR_VW_ID ) ,
				'type'  => 'switch' ,
			) ,

			'show_excerpt' => array(
				'id'    => 'show_excerpt' ,
				'label' => 'Enable Video Excerpt' ,
				'desc'  => __( 'Choose whether to show or hide the video excerpt . ' , WPVR_VW_ID ) ,
				'type'  => 'switch' ,
			) ,

			'show_duration' => array(
				'id'    => 'show_duration' ,
				'label' => 'Enable Video Duration' ,
				'desc'  => __( 'Choose whether to show or hide the video duration . ' , WPVR_VW_ID ) ,
				'type'  => 'switch' ,
			) ,

			'show_views'       => array(
				'id'    => 'show_views' ,
				'label' => 'Enable Video Views' ,
				'desc'  => __( 'Choose whether to show or hide the video views . ' , WPVR_VW_ID ) ,
				'type'  => 'switch' ,
			) ,
			'show_views_label' => array(
				'id'      => 'show_views_label' ,
				'label'   => 'Views Label' ,
				'desc'    => __( 'Enter your translated term of views. ' , WPVR_VW_ID ) ,
				'type'    => 'text' ,
				'default' => 'views' ,
			) ,

			//'show_morelink' => array(
			//	'id'    => 'show_morelink' ,
			//	'label' => 'Enable Read More Link' ,
			//	'desc'  => __( 'Choose whether to show or hide the read more link . ' , WPVR_VW_ID ) ,
			//	'type'  => 'switch' ,
			//) ,

			'show_playicon' => array(
				'id'    => 'show_playicon' ,
				'label' => 'Enable Play Icon' ,
				'desc'  => __( 'Choose whether to show or hide the play icon on the image . ' , WPVR_VW_ID ) ,
				'type'  => 'switch' ,
			) ,


		);

		//$wpvr_vw_settings[ 'theme' ] = array(
		//	'widget_theme' => array(
		//		'id'          => 'widget_theme' ,
		//		'label'       => 'Widget Appearence Theme' ,
		//		'desc'        => __( 'sqdqsdqsdqs qdqs dqd q . ' , WPVR_VW_ID ) ,
		//		'placeholder' => 'Choose me' ,
		//		'type'        => 'select' ,
		//		'values'      => array(
		//			1 => 'Theme A' ,
		//			2 => 'Theme B' ,
		//			3 => 'Theme C' ,
		//
		//
		//		) ,
		//	) ,
		//);
	}