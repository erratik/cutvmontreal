<?php
	
	
	/* Add your custom taxonomies to be handled by the imported videos */
	add_action( 'wp_loaded' , 'wpvrct_add_custom_taxonomies' );
	function wpvrct_add_custom_taxonomies() {
		$slot = wpvr_get_addon_options( WPVRCT_ID );
		if( $slot[ 'addon_enabled' ] === FALSE || count( $slot[ 'video_taxonomies' ] ) == 0 ) return FALSE;
		
		foreach ( (array) $slot[ 'video_taxonomies' ] as $taxonomy ) {
			if( $taxonomy != '0' ) register_taxonomy_for_object_type( $taxonomy , 'wpvr_video' );
		}
	}
	
	
	/* Adding New Fields for sources */
	add_filter( 'wpvr_extend_sources_metaboxes' , 'wpvrct_extend_sources_metaboxes' , 12 , 1 );
	function wpvrct_extend_sources_metaboxes( $metaboxes ) {
		global $debug;
		$slot = wpvr_get_addon_options( WPVRCT_ID );
		if( $slot[ 'addon_enabled' ] === FALSE || count( $slot[ 'video_taxonomies' ] ) == 0 ) return $metaboxes;
		
		//$prefix = 'wpvrct_';
		$new_fields = array();
		
		foreach ( (array) $slot[ 'video_taxonomies' ] as $taxname ) {
			
			if( $taxname == '0' ) continue;
			$taxonomy = get_taxonomy( $taxname );
			if( $taxonomy === FALSE ) continue;
			$taxlabel           = $taxonomy->labels->name;
			$field_id           = 'wpvr_cta_' . $taxname;
			$field_noid         = 'wpvr__cta_' . $taxname;
			$edit_taxonomy_link = admin_url( 'edit-tags.php?taxonomy=' . $taxname );
			
			
			$taxsArray = wpvr_get_taxonomy_terms( $taxname );
			
			if( count( $taxsArray ) == 0 ) {
				$taxsArray = array( '' => sprintf( __( 'No %s found.' , WPVR_LANG ) , $taxlabel ) , );
			} else {
				$taxsArray[ '' ] = sprintf( __( 'Choose one or more %s' , WPVR_LANG ) , $taxlabel );
			}
			
			
			$new_fields[] = array(
				'name'      => $taxlabel ,
				'id'        => $field_id ,
				'type'      => 'text' ,
				'default'   => '' ,
				'wpvrClass' => 'wpvr_selectize_values' ,
				'wpvrStyle' => 'display:none;' ,
			);
			$new_fields[] = array(
				'name'         => $taxlabel ,
				'desc'         => '<a href="' . $edit_taxonomy_link . '" target="_blank">' .
				                  sprintf( __( 'Edit or Add the %s.' , WPVR_LANG ) , $taxlabel ) .
				                  '</a>'
				,
				'id'           => '' . $field_noid . '' ,
				'type'         => 'select' ,
				'options'      => $taxsArray ,
				'wpvrClass'    => 'wpvr_cmb_selectize' ,
				'wpvrMaxItems' => WPVR_MAX_POSTING_CATS ,
				'wpvrService'  => $field_id ,
			);
			
		}
		$show_names = TRUE;
		if( count( $new_fields ) == 0 ) {
			$show_names   = FALSE;
			$new_fields[] = array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => 'wpvr_source_mb_html' ,
				'html'      => '<div class="wpvr_no_actions">' . __( 'There are no taxonomy terms found.' , WPVR_LANG ) . '</div>' ,
				'type'      => 'show_html' ,
				'wpvrClass' => 'wpvr_metabox_html' ,
			);
		}
		
		$new_metaboxes = array(
			array(
				'id'         => 'wpvrct_mb' ,
				'title'      => __( 'Source Custom Taxonomies' , WPVR_LANG ) ,
				'pages'      => array( WPVR_SOURCE_TYPE ) , // post type
				'context'    => 'normal' ,
				'priority'   => 'high' ,
				'show_names' => $show_names , // Show field names on the left
				'fields'     => $new_fields ,
			) ,
		);
		
		$metaboxes = wpvr_add_custom_metaboxes( $metaboxes , $new_metaboxes );
		
		return $metaboxes;
	}
	
	
	add_action( 'wpvr_event_add_video' , 'wpvrct_process_custom_taxonomies' , 100 , 2 );
	function wpvrct_process_custom_taxonomies( $videoItem , $new_post_id ) {
		
		$slot = wpvr_get_addon_options( WPVRCT_ID );
		if( $slot[ 'addon_enabled' ] === FALSE ) return FALSE;
		
		$source_metas = get_post_meta( $videoItem[ 'sourceId' ] );
		if( is_array( $source_metas ) && count( $source_metas ) > 0 ) {
			foreach ( (array) $source_metas as $key => $value ) {
				$x = explode( 'wpvr_cta_' , $key );
				if( $x[ 0 ] == $key ) continue;
				$taxname = $x[ 1 ];
				// $taxonomy = get_taxonomy( $taxname );
				// _d( $taxonomy->hierarchical );
				$taxterms = json_decode( $value[ 0 ] );
				$terms    = array();
				foreach ( $taxterms as $term ) {
					$terms[] = (int) $term;
				}
				
				// if( $taxonomy->hierarchical ) $terms = $taxterms ;
				// else {
				// $terms = array();
				// foreach( $taxterms as $term ) {
				// $tobj = get_term( $term , $taxname );
				// $terms[] = "'" . $tobj->name. "' " ;
				// }
				// $terms = implode(',' , $terms ) ;
				// }
				// _d( $taxname );
				// _d( $terms );
				// d( $taxterms );
				// _d( $taxterms );
				// _d( $new_post_id );
				
				wp_set_object_terms( $new_post_id , $terms , $taxname , TRUE );
			}
		}
	}
	
	
	