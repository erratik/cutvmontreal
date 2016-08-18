<?php
	
	
	/* Defining sources as wpvr_pages */
	add_action( 'admin_notices' , 'wpvr_sources_define_wpvr_pages' );
	function wpvr_sources_define_wpvr_pages() {
		$type = 'post';
		if( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		}
		if( WPVR_SOURCE_TYPE == $type ) {
			global $wpvr_pages;
			$wpvr_pages = TRUE;
		}
	}
	
	/* Defining Duplicate Source actions */
	add_action( 'admin_action_duplicate_source' , 'wpvr_source_duplicate_fct' );
	function wpvr_source_duplicate_fct() {
		global $wpdb;
		if( ! ( isset( $_GET[ 'post' ] ) || isset( $_POST[ 'post' ] ) || ( isset( $_REQUEST[ 'action' ] ) && 'duplicate_source' == $_REQUEST[ 'action' ] ) ) ) {
			wp_die( 'No post to duplicate has been supplied!' );
		}
		$post_id = ( isset( $_GET[ 'post' ] ) ? $_GET[ 'post' ] : $_POST[ 'post' ] );
		wpvr_duplicate_source( $post_id , $singleDuplicate = TRUE );
	}
	
	/* Defining The Csutom source type*/
	add_action( 'init' , 'wpvr_define_sources_post_type' , 0 );
	function wpvr_define_sources_post_type() {
		if( WPVR_META_DEBUG_MODE ) {
			$sources_support = array( 'custom-fields' );
		} else {
			$sources_support = array( '' );
		}

		$sources_support = apply_filters('wpvr_extend_sources_support' , $sources_support );

		$labels = array(
			'name'               => _x( 'Sources' , 'Post Type General Name' , WPVR_LANG ) ,
			'singular_name'      => _x( 'Source' , 'Post Type Singular Name' , WPVR_LANG ) ,
			'menu_name'          => __( 'Sources' , WPVR_LANG ) ,
			'parent_item_colon'  => __( 'Parent Source:' , WPVR_LANG ) ,
			'all_items'          => __( 'All Sources' , WPVR_LANG ) ,
			'view_item'          => __( 'View Source' , WPVR_LANG ) ,
			'add_new_item'       => __( 'Add New Source' , WPVR_LANG ) ,
			'add_new'            => __( 'New Source' , WPVR_LANG ) ,
			'edit_item'          => __( 'Edit Source' , WPVR_LANG ) ,
			'update_item'        => __( 'Update Source' , WPVR_LANG ) ,
			'search_items'       => __( 'Search sources' , WPVR_LANG ) ,
			'not_found'          => __( 'No sources found' , WPVR_LANG ) ,
			'not_found_in_trash' => __( 'No sources found in Trash' , WPVR_LANG ) ,
		);
		$args   = array(
			'label'               => __( 'source' , WPVR_LANG ) ,
			'description'         => __( 'WPVR Sources' , WPVR_LANG ) ,
			'labels'              => $labels ,
			//'supports'            => array( 'title','custom-fields' ), //DEBUG LINE
			'supports'            => $sources_support ,
			'taxonomies'          => array( '' ) ,
			'hierarchical'        => FALSE ,
			'public'              => FALSE ,
			'show_ui'             => TRUE ,
			'show_in_menu'        => TRUE ,
			'show_in_nav_menus'   => FALSE ,
			'show_in_admin_bar'   => TRUE ,
			'menu_position'       => 5 ,
			'menu_icon'           => 'dashicons-search' ,
			'can_export'          => TRUE ,
			'has_archive'         => FALSE ,
			'exclude_from_search' => TRUE ,
			'publicly_queryable'  => FALSE ,
			'rewrite'             => FALSE ,
			'capability_type'     => 'page' ,
		);
		register_post_type( WPVR_SOURCE_TYPE , $args );
	}

	// Register Custom Taxonomy
	add_action( 'init' , 'wpvr_define_source_folders' , 0 );
	function wpvr_define_source_folders() {
		$labels = array(
			'name'                       => _x( 'Source Folders' , 'Taxonomy General Name' , 'wpvr_lang' ) ,
			'singular_name'              => _x( 'Source Folder' , 'Taxonomy Singular Name' , 'wpvr_lang' ) ,
			'menu_name'                  => __( 'Source Folders' , 'wpvr_lang' ) ,
			'all_items'                  => __( 'All Folders' , 'wpvr_lang' ) ,
			'parent_item'                => __( 'Parent Folder' , 'wpvr_lang' ) ,
			'parent_item_colon'          => __( 'Parent Folder:' , 'wpvr_lang' ) ,
			'new_item_name'              => __( 'New Folder Name' , 'wpvr_lang' ) ,
			'add_new_item'               => __( 'Add New Folder' , 'wpvr_lang' ) ,
			'edit_item'                  => __( 'Edit Folder' , 'wpvr_lang' ) ,
			'update_item'                => __( 'Update Folder' , 'wpvr_lang' ) ,
			'view_item'                  => __( 'View Folder' , 'wpvr_lang' ) ,
			'separate_items_with_commas' => __( 'Separate folders with commas' , 'wpvr_lang' ) ,
			'add_or_remove_items'        => __( 'Add or remove folders' , 'wpvr_lang' ) ,
			'choose_from_most_used'      => __( 'Choose from the most used' , 'wpvr_lang' ) ,
			'popular_items'              => __( 'Popular folders' , 'wpvr_lang' ) ,
			'search_items'               => __( 'Search folders' , 'wpvr_lang' ) ,
			'not_found'                  => __( 'Not Found' , 'wpvr_lang' ) ,
			'no_terms'                   => __( 'No folders' , 'wpvr_lang' ) ,
			'items_list'                 => __( 'Folders list' , 'wpvr_lang' ) ,
			'items_list_navigation'      => __( 'Folders list navigation' , 'wpvr_lang' ) ,
		);
		$args   = array(
			'labels'            => $labels ,
			'hierarchical'      => TRUE ,
			'public'            => FALSE ,
			'show_ui'           => TRUE ,
			'show_admin_column' => TRUE ,
			'show_in_nav_menus' => TRUE ,
			'show_tagcloud'     => FALSE ,
		);
		register_taxonomy( WPVR_SFOLDER_TYPE , array( 'wpvr_source' ) , $args );

	}

	
	/*Manage Custom Columns on Sources list */
	add_filter( 'manage_edit-' . WPVR_SOURCE_TYPE . '_columns' , 'wpvr_source_define_custom_columns' );
	function wpvr_source_define_custom_columns( $columns ) {
		unset( $columns );
		$columns = array(
			'cb'             => '<input type="checkbox"/>' ,
			'name'           => __( 'Name' , WPVR_LANG ) ,
			'stats'          => __( 'Statistics' , WPVR_LANG ) ,
			'info'    => __( 'Informations' , WPVR_LANG ) ,
			'options' => __( 'Settings' , WPVR_LANG ) ,
			'status'         => __( 'Status' , WPVR_LANG ) ,
		);
		
		return $columns;
	}
	
	/*Manage Custom Columns on Sources list */
	add_action( 'manage_'.WPVR_SOURCE_TYPE.'_posts_custom_column' , 'wpvr_source_manage_custom_columns' );
	function wpvr_source_manage_custom_columns( $column ) {
		global $post ;

		if( isset( $_GET['post_status'] ) && $_GET['post_status'] != '' ) $post_status = $_GET['post_status'] ;
		else $post_status = 'publish';
		//d( $post_status );
		echo wpvr_get_source_columns( $post->ID , $column , $post_status );
		if( $column == 'status' ) unset( $_SESSION[ 'tmp_sources_columns' ] );
	}
	
	
	/*Manage Custom Columns on Sources Folders list */
	add_filter( 'manage_edit-' . WPVR_SFOLDER_TYPE . '_columns' , 'wpvr_source_folders_define_custom_columns' );
	function wpvr_source_folders_define_custom_columns( $columns ) {
		$columns['actions'] = __('Sources Actions' , WPVR_LANG );
		return $columns;
	}
	
	/*Manage Custom Columns on Sources Folders list */
	add_action( 'manage_'.WPVR_SFOLDER_TYPE.'_custom_column' , 'wpvr_source_folders_manage_custom_columns' , 10 , 3  );
	function wpvr_source_folders_manage_custom_columns( $value, $column_name, $folder_id ) {
		//d( $column_name );
		if( $column_name == 'actions'){
			
			$testLink      = admin_url( 'admin.php?page=wpvr&test_sources&folders=' . $folder_id, 'http' );
			$runLink       = admin_url( 'admin.php?page=wpvr&run_sources&folders=' . $folder_id , 'http' );
			$exportLink    = admin_url( 'admin.php?page=wpvr&export_sources&folders=' . $folder_id , 'http' );
			
			$more = apply_filters( 'wpvr_extend_source_folder_column_actions' , '' , $folder_id );
			
			return '
				<div class = "wpvr_source_action_button pull-left">
					<a href = "' . $testLink . '" target = "_blank">
						<i class = "wpvr_link_icon fa fa-eye"></i>
						' . __( 'Test' , WPVR_LANG ) . '
					</a>
				</div>
				<div class = "wpvr_source_action_button pull-left ">
					<a href = "' . $runLink . '" target = "_blank">
						<i class = "wpvr_link_icon fa fa-bolt"></i>
						' . __( 'Run' , WPVR_LANG ) . '
					</a>
				</div>
				<div class="wpvr_clearfix"></div>
				<div class = "wpvr_source_action_button wpvr_black_button">
					<a href = "' . $exportLink . '" target = "_blank">
						<i class = "wpvr_link_icon fa fa-upload"></i>
						' . __( 'Export' , WPVR_LANG ) . '
					</a>
				</div>
				'.$more.'
			';
		}
		return $value;
	}
	

	/* Initialize metaboxes on sources */
	add_action( 'init' , 'wpvr_source_init_metaboxes' , 9999 );
	function wpvr_source_init_metaboxes() {
		if( ! class_exists( 'wpvr_cmb_Meta_Box' ) ) {
			require_once( WPVR_PATH . '/assets/metabox/init.php' );
		}
	}

	/* Define Sources Metaboxes */
	add_filter( 'wpvr_cmb_meta_boxes' , 'wpvr_source_define_metaboxes' );
	function wpvr_source_define_metaboxes( $meta_boxes ) {
		$prefix       = 'wpvr_source_';
		$authorsArray = wpvr_get_authors( $invert = TRUE , $default = TRUE , $restrict = TRUE );

		global $wpvr_types_ , $wpvr_services , $post;

		global $wpvr_hours , $wpvr_days_names , $wpvr_countries;


		if( WPVR_ACCEPT_EMPTY_SOURCE_NAMES ) {
			$accept_empty_source_name = 'canBeEmpty';
		} else {
			$accept_empty_source_name = '';
		}

		if( WPVR_MAX_WANTED_VIDEOS === FALSE ) {
			$max_wanted_videos = '';
		} else {
			$max_wanted_videos = WPVR_MAX_WANTED_VIDEOS;
		}

		/* Extending Video Services Options */
		$video_service_options = array();
		$video_service_options = apply_filters( 'wpvr_extend_video_services_options' , $video_service_options );

		/* Extending Video Services Types Options */
		$video_service_types_options = array();
		$video_service_types_options = apply_filters( 'wpvr_extend_video_services_types_options' , $video_service_types_options );

		/* Extending Video Services Types Options */
		$source_fields = array();
		$source_fields = apply_filters( 'wpvr_extend_video_services_types_fields' , $source_fields , $prefix );

		$source_basics       = array(
			array(
				'name' => __( 'Name' , WPVR_LANG ) ,
				'desc' => '' ,
				'id'   => $prefix . 'name' ,
				'type' => 'text' ,
			) ,
			array(
				'name'        => __( 'Video Service' , WPVR_LANG ) ,
				'desc'        => '' ,
				'id'          => $prefix . 'service' ,
				'type'        => 'radio_inline' ,
				'options'     => $video_service_options ,
				'default'     => 'youtube' ,
				'wpvrClass'   => $accept_empty_source_name ,
				'wpvrService' => $max_wanted_videos ,
			) ,
			array(
				'name'        => __( 'Source Type' , WPVR_LANG ) ,
				'desc'        => '' ,
				'id'          => $prefix . 'type' ,
				'type'        => 'radio_inline' ,
				'options'     => $video_service_types_options ,
				'default'     => '' ,
				'wpvrClass'   => 'sourceType' ,
				'wpvrStyle'   => 'display:none;' ,
				'wpvrService' => 'koko' ,
			) ,
		);
		$source_infos_fields = array_merge( $source_basics , $source_fields );

		$meta_boxes[]   = array(
			'id'         => 'wpvr_source_metabox' ,
			'title'      => '<i class="fa fa-info-circle"></i> ' . __( 'Source Information' , WPVR_LANG ) ,
			'pages'      => array( WPVR_SOURCE_TYPE ) , // post type
			'context'    => 'normal' ,
			'priority'   => 'high' ,
			'show_names' => TRUE , // Show field names on the left
			'fields'     => $source_infos_fields ,
		);
		$meta_boxes[]   = array(
			'id'         => 'wpvr_source_options_metabox' ,
			'title'      => '<i class="fa fa-search"></i> ' . __( 'Source Fetching Options' , WPVR_LANG ) ,
			'pages'      => array( WPVR_SOURCE_TYPE ) , // post type
			'context'    => 'normal' ,
			'priority'   => 'high' ,
			'show_names' => TRUE , // Show field names on the left
			'fields'     => array(
				array(
					'name'    => __( 'Wanted Videos' , WPVR_LANG ) ,
					'id'      => $prefix . 'wantedVideosBool' ,
					'type'    => 'select' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'custom'  => __( 'Custom number' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,
				array(
					'name'            => __( 'Number of videos' , WPVR_LANG ) ,
					'desc'            => __( 'How many videos to get at a time?' , WPVR_LANG ) . ' ' .
					                     __( 'Max' , WPVR_LANG ) . ' : ' . ( ( WPVR_MAX_WANTED_VIDEOS === FALSE ) ? __( 'Unlimited' , WPVR_LANG ) : WPVR_MAX_WANTED_VIDEOS ) ,
					'default'         => '5' ,
					'id'              => $prefix . 'wantedVideos' ,
					'type'            => 'text_small' ,
					'wpvrStyle'       => 'display:none;' ,
					'wpvrClass'       => 'wpvr_has_master' ,
					'wpvr_attributes' => array(
						'master_id'    => $prefix . 'wantedVideosBool' ,
						'master_value' => 'custom' ,
					) ,
				) ,
				array(
					'name'    => __( 'Order by' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'order' ,
					'type'    => 'select' ,
					'options' => array(
						'default'   => __( '- Default -' , WPVR_LANG ) ,
						'relevance' => __( 'Relevance' , WPVR_LANG ) ,
						'date'      => __( 'Date' , WPVR_LANG ) ,
						'viewCount' => __( 'Views' , WPVR_LANG ) ,
						'title'     => __( 'Title' , WPVR_LANG ) ,
						'rating'    => __( 'Rating' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,
				array(
					'name'    => __( 'Duplicates' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'onlyNewVideos' ,
					'type'    => 'select' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'on'      => __( 'Skip duplicates' , WPVR_LANG ) ,
						'off'     => __( 'Do not skip duplicates' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,
				array(
					'name'    => __( 'Statistics' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'getVideoStats' ,
					'type'    => 'select' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'on'      => __( 'Get Video Statistics' , WPVR_LANG ) ,
						'off'     => __( 'Do not get Video Statistics' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,
				array(
					'name'    => __( 'Tags' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'getVideoTags' ,
					'type'    => 'select' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'on'      => __( 'Get Video Tags' , WPVR_LANG ) ,
						'off'     => __( 'Do not get Video Tags' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,

			) ,
		);
		$edit_cats_link = admin_url( 'edit-tags.php?taxonomy=category' );
		$catsArray      = array(
			'' => __( 'Choose one or more categories' , WPVR_LANG ) ,
		);
		if( WPVR_HIERARCHICAL_CATS_ENABLED === TRUE ) {
			$cats = wpvr_get_hierarchical_cats();
		} else {
			$cats = wpvr_get_categories_count( $invert = FALSE , $get_empty = TRUE );
		}
		foreach ( $cats as $cat ) {
			$catsArray[ $cat[ 'value' ] ] = $cat[ 'label' ];
		}

		$tagsArray = array( '' => __( 'Enter one or more tags' , WPVR_LANG ) );


		$meta_boxes[] = array(
			'id'         => 'wpvr_source_posting_metabox' ,
			'title'      => '<i class="fa fa-cloud-upload"></i> ' . __( 'Source Posting Options' , WPVR_LANG ) ,
			'pages'      => array( WPVR_SOURCE_TYPE ) , // post type
			'context'    => 'normal' ,
			'priority'   => 'high' ,
			'show_names' => TRUE , // Show field names on the left
			'fields'     => array(
				array(
					'name'    => __( 'AutoPublish' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'autoPublish' ,
					'type'    => 'select' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'on'      => __( 'AutoPublish' , WPVR_LANG ) ,
						'off'     => __( 'Post as draft' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,
				array(
					'name'      => __( 'Post to' , WPVR_LANG ) . ' HIDDEN' ,
					'id'        => $prefix . 'postCats' ,
					'type'      => 'text' ,
					'default'   => '' ,
					'wpvrClass' => 'wpvr_selectize_values' ,
					'wpvrStyle' => 'display:none;' ,
				) ,
				array(
					'name'         => __( 'Post to' , WPVR_LANG ) ,
					'desc'         => '<a href="' . $edit_cats_link . '" target="_blank">' .
					                  __( 'Edit or Add the Categories' , WPVR_LANG ) .
					                  '</a>'
					,
					'id'           => $prefix . 'postCats_' ,
					'type'         => 'select' ,
					'options'      => $catsArray ,
					'wpvrClass'    => 'wpvr_cmb_selectize' ,
					'wpvrMaxItems' => WPVR_MAX_POSTING_CATS ,
					//'default' => '',
				) ,
				array(
					'name'    => __( 'Post Author' , WPVR_LANG ) ,
					'desc'    => __( 'Choose the author of the autoposting' , WPVR_LANG ) ,
					'id'      => $prefix . 'postAuthor' ,
					'type'    => 'select' ,
					'options' => $authorsArray ,
					'default' => 'default' ,
				) ,
				array(
					'name'    => __( 'Post Date' , WPVR_LANG ) ,
					'desc'    => __( 'Choose the date of the autoposting' , WPVR_LANG ) ,
					'id'      => $prefix . 'postDate' ,
					'type'    => 'select' ,
					'options' => array(
						'default'  => __( '- Default -' , WPVR_LANG ) ,
						'original' => __( 'Original Date' , WPVR_LANG ) ,
						'new'      => __( 'Updated Date' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,

				array(
					'name'    => __( 'Post Title Affix' , WPVR_LANG ) ,
					'desc'    => __( 'Choose to add the name of the source or a custom text before or after the video title.' , WPVR_LANG ) ,
					'id'      => $prefix . 'postAppend' ,
					'type'    => 'select' ,
					'options' => array(
						'off'          => __( 'Disabled' , WPVR_LANG ) ,
						'before'       => __( 'Add source name before the video title' , WPVR_LANG ) ,
						'after'        => __( 'Add source name after the video title' , WPVR_LANG ) ,
						'customBefore' => __( 'Add custom text before the video title' , WPVR_LANG ) ,
						'customAfter'  => __( 'Add custom text after the video title' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,
				array(
					'name'            => __( 'Custom Text Affix' , WPVR_LANG ) ,
					'desc'            => __( 'Choose a custom text to add before or after the video title.' , WPVR_LANG ) ,
					'id'              => $prefix . 'appendCustomText' ,
					'default'         => '' ,
					'type'            => 'text' ,
					'wpvrStyle'       => 'display:none;' ,
					'wpvrClass'       => 'wpvr_has_master' ,
					'wpvr_attributes' => array(
						'master_id'    => $prefix . 'postAppend' ,
						'master_value' => 'customBefore,customAfter' ,
					) ,

				) ,
				array(
					'name'    => __( 'Post Tags' , WPVR_LANG ) ,
					'desc'    => __( 'Choose whether to auto apply tags to the imported videos of this source.' , WPVR_LANG ) ,
					'id'      => $prefix . 'postTagsBool' ,
					'type'    => 'select' ,
					'options' => array(
						'disabled' => __( 'Disabled' , WPVR_LANG ) ,
						'default'  => __( 'Default Tags' , WPVR_LANG ) ,
						'custom'   => __( 'Custom Tags' , WPVR_LANG ) ,
					) ,
					'default' => 'disabled' ,
				) ,
				array(
					'name'            => __( 'Post Tags' , WPVR_LANG ) . '' ,
					'id'              => $prefix . 'postTags' ,
					'type'            => 'textarea' ,
					'desc'            => __( 'Enter your custom tags.' , WPVR_LANG ) ,
					'default'         => '' ,
					'wpvrStyle'       => 'display:none;' ,
					'wpvrClass'       => 'wpvr_has_master' ,
					'wpvr_attributes' => array(
						'master_id'    => $prefix . 'postTagsBool' ,
						'master_value' => 'custom' ,
					) ,
				) ,

				array(
					'name'    => __( 'Video Text Content' , WPVR_LANG ) ,
					'desc'    => __( 'Choose whether to import the video text content or not.' , WPVR_LANG ) ,
					'id'      => $prefix . 'postContent' ,
					'type'    => 'select' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'on'      => __( 'Post the video text content' , WPVR_LANG ) ,
						'off'     => __( 'Skip the video text content' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,

			) ,
		);

		$meta_boxes[] = array(
			'id'         => 'wpvr_source_filtering_metabox' ,
			'title'      => '<i class="fa fa-filter"></i> ' . __( 'Source Filtering Options' , WPVR_LANG ) ,
			'pages'      => array( WPVR_SOURCE_TYPE ) , // post type
			'context'    => 'normal' ,
			'priority'   => 'high' ,
			'show_names' => TRUE , // Show field names on the left
			'fields'     => array(
				array(
					'name'    => __( 'Published After' , WPVR_LANG ) ,
					'id'      => $prefix . 'publishedAfter_bool' ,
					'type'    => 'select' ,
					'desc'    => '' . __( 'Import only videos published after this date.' , WPVR_LANG ) . ' ' .
					             __( 'Leave empty to ignore this criterion.' , WPVR_LANG ) .
					             '<br/><strong>' . __( 'Supported only by Youtube and Dailymotion.' , WPVR_LANG ) . '</strong>' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'custom'  => __( 'Custom' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,
				array(
					'name'            => __( 'Published After' , WPVR_LANG ) . ' (Date)' ,
					'id'              => $prefix . 'publishedAfter' ,
					'type'            => 'text_date' ,
					'default'         => '' ,
					'wpvrStyle'       => 'display:none;' ,
					'wpvrClass'       => 'wpvr_has_master' ,
					'wpvr_attributes' => array(
						'master_id'    => $prefix . 'publishedAfter_bool' ,
						'master_value' => 'custom' ,
					) ,
				) ,

				array(
					'name' => __( 'Published Before' , WPVR_LANG ) ,
					'id'   => $prefix . 'publishedBefore_bool' ,
					'desc' => '' . __( 'Import only videos published before this date.' , WPVR_LANG ) . ' ' .
					          __( 'Leave empty to ignore this criterion.' , WPVR_LANG ) .
					          '<br/><strong>' . __( 'Supported only by Youtube and Dailymotion.' , WPVR_LANG ) . '</strong>' ,

					'type'    => 'select' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'custom'  => __( 'Custom' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,

				array(
					'name'            => __( 'Published Before' , WPVR_LANG ) . ' (Date)' ,
					'id'              => $prefix . 'publishedBefore' ,
					'type'            => 'text_date' ,
					'default'         => '' ,
					'wpvrStyle'       => 'display:none;' ,
					'wpvrClass'       => 'wpvr_has_master' ,
					'wpvr_attributes' => array(
						'master_id'    => $prefix . 'publishedBefore_bool' ,
						'master_value' => 'custom' ,
					) ,
				) ,
				array(
					'name'    => __( 'Duration' , WPVR_LANG ) ,
					'desc'    => __( 'Filter fetched videos by duration. Note it works only for Search sources.' , WPVR_LANG ) .
					             '<br/><strong>' . __( 'Supported only by Youtube and Dailymotion.' , WPVR_LANG ) . '</strong>' ,
					'id'      => $prefix . 'videoDuration' ,
					'type'    => 'select' ,
					'options' => array(
						'default' => __( '- Default -' , WPVR_LANG ) ,
						'any'     => __( 'All Videos' , WPVR_LANG ) ,
						'short'   => __( 'Videos less than 4min.' , WPVR_LANG ) ,
						'medium'  => __( 'Videos between 4min. and 20min.' , WPVR_LANG ) ,
						'long'    => __( 'Videos longer than 20min.' , WPVR_LANG ) ,

					) ,
					'default' => 'default' ,
				) ,
				array(
					'name'    => __( 'Video Quality' , WPVR_LANG ) ,
					'desc'    => __( 'Filter fetched videos by quality and definition.' , WPVR_LANG ) . '<br/>' .
					             '<br/><strong>' . __( 'Supported only by Youtube, Vimeo and Dailymotion.' , WPVR_LANG ) . '</strong>' ,
					'id'      => $prefix . 'videoQuality' ,
					'type'    => 'select' ,
					'options' => array(
						'default'  => __( '- Default -' , WPVR_LANG ) ,
						'any'      => __( 'All Videos' , WPVR_LANG ) ,
						'high'     => __( 'Only High Definition Videos' , WPVR_LANG ) ,
						'standard' => __( 'Only Standard Definitions Videos' , WPVR_LANG ) ,
					) ,
					'default' => 'default' ,
				) ,
			) ,
		);

		if( isset( $_GET[ 'post' ] ) ) {

			if( is_array( $_GET[ 'post' ] ) ) {
				return $meta_boxes;
			}

			$post_id = $_GET[ 'post' ];
			//$shortcode = '[wpvr id='.$post_id.']';
		} elseif( isset( $_POST[ 'post_ID' ] ) ) {
			$post_id = $_POST[ 'post_ID' ];
			//$shortcode = '[wpvr id='.$post_id.']';
		} else {
			$post_id = "";
			//$shortcode = "Save First";
		}
		
		$actionButtons = wpvr_render_source_actions( $post_id );

		$meta_boxes[] = array(
			'id'         => 'wpvr_source_status_metabox' ,
			'title'      => '<i class="fa fa-play-circle"></i> ' . __( 'Source Actions' , WPVR_LANG ) ,
			'pages'      => array( WPVR_SOURCE_TYPE ) , // post type
			'context'    => 'side' ,
			'priority'   => 'high' ,
			'show_names' => FALSE , // Show field names on the left
			'fields'     => array(
				
				array(
					'name'      => '' ,
					'desc'      => '' ,
					'id'        => $prefix . 'html_preload' ,
					'html'      => '<div style="text-align:center;">'.__('Loading ...' , WPVR_LANG ).'</div>' ,
					'type'      => 'show_html' ,
					'wpvrClass' => 'wpvr_metabox_html wpvr_hide_when_loaded' ,
					'wpvrStyle' => '' ,
				) ,

				array(
					'name'      => '' ,
					'desc'      => '' ,
					'id'        => $prefix . 'html' ,
					'html'      => $actionButtons[ 'test' ] ,
					'type'      => 'show_html' ,
					'wpvrClass' => 'wpvr_metabox_html wpvr_show_when_loaded' ,
					'wpvrStyle' => 'display:none;' ,
				) ,
				array(
					'name'      => '' ,
					'desc'      => '' ,
					'id'        => $prefix . 'html' ,
					'html'      => $actionButtons[ 'run' ] ,
					'type'      => 'show_html' ,
					'wpvrClass' => 'wpvr_metabox_html wpvr_show_when_loaded' ,
					'wpvrStyle' => 'display:none;' ,
				) ,

				array(
					'name'      => '' ,
					'desc'      => '' ,
					'id'        => $prefix . 'html' ,
					'html'      => $actionButtons[ 'clone' ] ,
					'type'      => 'show_html' ,
					'wpvrClass' => 'wpvr_metabox_html wpvr_show_when_loaded' ,
					'wpvrStyle' => 'display:none;' ,
				) ,

				array(
					'name'      => '' ,
					'desc'      => '' ,
					'id'        => $prefix . 'html' ,
					'html'      => $actionButtons[ 'save' ] ,
					'type'      => 'show_html' ,
					'wpvrClass' => 'wpvr_metabox_html wpvr_show_when_loaded' ,
					'wpvrStyle' => 'display:none;' ,
				) ,

				array(
					'name'      => '' ,
					'desc'      => '' ,
					'id'        => $prefix . 'html' ,
					'html'      => $actionButtons[ 'trash' ] ,
					'type'      => 'show_html' ,
					'wpvrClass' => 'wpvr_metabox_html wpvr_show_when_loaded' ,
					'wpvrStyle' => 'display:none;' ,
				) ,


				array(
					'name'      => __( 'Plugin Version' , WPVR_LANG ) ,
					'default'   => WPVR_VERSION ,
					'id'        => $prefix . 'plugin_version' ,
					'type'      => 'text_small' ,
					'wpvrStyle' => 'display:none;' ,
				) ,
			) ,
		);
		$meta_boxes[] = array(
			'id'         => 'wpvr_source_scheduling_metabox' ,
			'title'      => '<i class="fa fa-calendar"></i> ' . __( 'Source Schedule' , WPVR_LANG ) ,
			'pages'      => array( WPVR_SOURCE_TYPE ) , // post type
			'context'    => 'side' ,
			'priority'   => 'high' ,
			'show_names' => TRUE , // Show field names on the left
			'fields'     => array(
				array(
					'name'    => __( 'Source is active' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'status' ,
					'type'    => 'select' ,
					'options' => array(
						'on'  => __( 'YES' , WPVR_LANG ) ,
						'off' => __( 'NO' , WPVR_LANG ) ,
					) ,
					'default' => 'off' ,
				) ,
				array(
					'name'    => __( 'Scheduled Cron Job' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'schedule' ,
					'type'    => 'select' ,
					'options' => array(
						'hourly' => __( 'Run Hourly' , WPVR_LANG ) ,
						'daily'  => __( 'Run Daily' , WPVR_LANG ) ,
						'weekly' => __( 'Run Weekly' , WPVR_LANG ) ,
						'once'   => __( 'Run Once' , WPVR_LANG ) ,
					) ,
					'default' => 'hourly' ,
				) ,
				array(
					'name' => __( 'Choose a date' , WPVR_LANG ) ,
					'desc' => '' ,
					'id'   => $prefix . 'schedule_date' ,
					'type' => 'text_date_timestamp' ,

				) ,
				array(
					'name'    => __( 'Choose a day' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'schedule_day' ,
					'type'    => 'select' ,
					'options' => $wpvr_days_names ,
					'default' => 'monday' ,
				) ,
				array(
					'name'    => __( 'Choose a time' , WPVR_LANG ) ,
					'desc'    => '' ,
					'id'      => $prefix . 'schedule_time' ,
					'type'    => 'select' ,
					'options' => $wpvr_hours ,
					'default' => '04H00' ,
				) ,
			) ,
		);
		if( $post_id != '' ) {
			$source = wpvr_get_source( $post_id );
			if( $source != FALSE ) {
				$source_type  = $source->type;
				$wantedVideos = ( ! isset( $source->wantedVideos ) || ( $source->wantedVideos == '' ) ) ? 0 : $source->wantedVideos;
				if( $source_type == 'channel' ) {
					$subsources       = count( wpvr_parse_string( $source->channelIds ) );
					$subsources_label = __( 'channels' , WPVR_LANG );
					$subsources_line  = ' <b>' . wpvr_numberK( $subsources , TRUE ) . '</b> ' . $subsources_label . '<br/>';

				} elseif( $source_type == 'playlist' ) {
					$subsources       = count( wpvr_parse_string( $source->playlistIds ) );
					$subsources_label = __( 'playlists' , WPVR_LANG );
					$subsources_line  = ' <b>' . wpvr_numberK( $subsources , TRUE ) . '</b> ' . $subsources_label . '<br/>';

				} else {
					$subsources      = 0;
					$subsources_line = '';
				}
				if( $subsources > 1 ) {
					$wantedVideos = $wantedVideos * $subsources;
				}

				//d($source);
				$source_stats_html = '';
				$source_stats_html .= '<div  style="text-transform:uppercase;">';
				$source_stats_html .= ' <b>' . wpvr_numberK( $wantedVideos , TRUE ) . '</b> ' . __( 'Wanted videos' , WPVR_LANG ) . '<br/>';
				$source_stats_html .= '<b>' . wpvr_numberK( $source->count_imported , TRUE ) . '</b> ' . __( 'Imported videos' , WPVR_LANG ) . '<br/>';
				$source_stats_html .= $subsources_line;
				$source_stats_html .= __( 'TESTED' , WPVR_LANG ) . ' <strong>' . wpvr_numberK( $source->count_test , TRUE ) . '</strong> ' . __( 'times' , WPVR_LANG ) . '<br/>';
				$source_stats_html .= __( 'RUN' , WPVR_LANG ) . ' <strong>' . wpvr_numberK( $source->count_run , TRUE ) . '</strong> ' . __( 'times' , WPVR_LANG ) . '<br/>';
				$source_stats_html .= '</div>';
			} else {
				$source_stats_html = '<div class="wpvr_no_actions">' . __( 'Start by saving your source' , WPVR_LANG ) . '</div>';
			}
		} else {
			$source_stats_html = '<div class="wpvr_no_actions">' . __( 'Start by saving your source' , WPVR_LANG ) . '</div>';
		}
		$meta_boxes[] = array(
			'id'         => 'wpvr_source_stats_metabox' ,
			'title'      => '<i class="fa fa-bar-chart"></i> ' . __( 'Source Stats' , WPVR_LANG ) ,
			'pages'      => array( WPVR_SOURCE_TYPE ) , // post type
			'context'    => 'side' ,
			'priority'   => 'low' ,
			'show_names' => FALSE , // Show field names on the left
			'fields'     => array(
				array(
					'name'      => '' ,
					'desc'      => '' ,
					'id'        => $prefix . 'html' ,
					'html'      => $source_stats_html ,
					'type'      => 'show_html' ,
					'wpvrClass' => 'wpvr_metabox_html' ,
				) ,
			) ,
		);
		$meta_boxes   = apply_filters( 'wpvr_extend_sources_metaboxes' , $meta_boxes );

		return $meta_boxes;
	}

	/*Show SOURCE TYPES select filter on sources list */
	add_action( 'restrict_manage_posts' , 'wpvr_source_create_types_dropdown' );
	function wpvr_source_create_types_dropdown() {
		$type = 'post';
		if( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		}
		if( WPVR_SOURCE_TYPE == $type ) {
			$typesArray = array();
			global $wpvr_vs;

			foreach ( (array) $wpvr_vs as $vs ) {
				foreach ( (array) $vs[ 'types' ] as $vs_type ) {
					if( $vs_type[ 'global_id' ] == 'group_' ) {
						$label = 'Group';
					} else {
						$label = ucfirst( $vs_type[ 'global_id' ] );
					}
					$typesArray[ $vs_type[ 'global_id' ] ] = $label;
				}
			}

			?>
			<select name = "source_type">
				<option value = ""><?php _e( 'Show all types' , WPVR_LANG ); ?></option>
				<?php
					$current_v = isset( $_GET[ 'source_type' ] ) ? $_GET[ 'source_type' ] : '';
					foreach ( $typesArray as $value => $label ) {
						printf
						(
							'<option value="%s"%s>%s</option>' ,
							$value ,
							$value == $current_v ? ' selected="selected"' : '' ,
							$label
						);
					}
				?>
			</select>
			<?php
		}
	}

	/*Show SOURCE TYPES select filter on sources list */
	add_action( 'restrict_manage_posts' , 'wpvr_source_create_services_dropdown' );
	function wpvr_source_create_services_dropdown() {
		$type = 'post';
		global $wpvr_services;
		if( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		}

		if( WPVR_SOURCE_TYPE == $type ) {

			?>
			<select name = "source_service">
				<option value = ""><?php _e( 'Show all services' , WPVR_LANG ); ?></option>
				<?php
					global $wpvr_vs;
					$current_v = isset( $_GET[ 'source_service' ] ) ? $_GET[ 'source_service' ] : '';
					foreach ( $wpvr_vs as $value => $vs ) {
						if( ! isset( $vs[ 'skipThis' ] ) && ! $vs[ 'skipThis' ] ) {
							$s    = ( $vs[ 'id' ] == $current_v ) ? ' selected="selected"' : '';
							$echo = '<option value="' . $vs[ 'id' ] . '" ' . $s . ' >';
							$echo .= $vs[ 'label' ];
							$echo .= '</option>';
							echo $echo;
						}
					}
				?>
			</select>
			<?php
		}
	}

	/*Show SOURCE FOLDERS  select filter on sources list */
	add_action( 'restrict_manage_posts' , 'wpvr_source_create_folders_dropdown' );
	function wpvr_source_create_folders_dropdown() {
		$type = 'post';
		global $wpvr_services;
		if( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		}

		if( WPVR_SOURCE_TYPE == $type ) {
			$folders = wpvr_get_folders_simple();
			?>
			<select name = "source_folder">
				<option value = ""><?php _e( 'Show all folders' , WPVR_LANG ); ?></option>
				<?php
					global $wpvr_vs;
					$current_v = isset( $_GET[ 'source_folder' ] ) ? $_GET[ 'source_folder' ] : '';
					foreach ( $folders as $value => $label ) {
						$s    = ( $value == $current_v ) ? ' selected="selected"' : '';
						$echo = '<option value="' . $value . '" ' . $s . ' >';
						$echo .= $label;
						$echo .= '</option>';
						echo $echo;
					}
				?>
			</select>
			<?php
		}
	}


	/*Show Authors select filter on sources list */
	add_action( 'restrict_manage_posts' , 'wpvr_source_create_authors_dropdown' );
	function wpvr_source_create_authors_dropdown() {
		if( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		} else {
			$type = 'post';
		}
		if( WPVR_SOURCE_TYPE == $type ) {
			$authorsArray = wpvr_get_authors( $invert = FALSE , $default = FALSE , $restrict = TRUE );
			?>
			<select name = "source_author">
				<option value = ""><?php _e( 'Show all authors' , WPVR_LANG ); ?></option>
				<?php
					$current_v = isset( $_GET[ 'source_author' ] ) ? $_GET[ 'source_author' ] : '';
					foreach ( $authorsArray as $label => $value ) {
						printf
						(
							'<option value="%s"%s>%s</option>' ,
							$value ,
							$value == $current_v ? ' selected="selected"' : '' ,
							$label
						);
					}
				?>
			</select>
			<?php
		}
	}

	/*Filtering sources list */
	add_filter( 'pre_get_posts' , 'wpvr_filter_source_list' );
	function wpvr_filter_source_list( $query ) {
		global $pagenow , $wpvr_options;
		global $wpvr_vs;

		if( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		} else {
			$type = "post";
		}

		if( $type != WPVR_SOURCE_TYPE || ! is_admin() || $pagenow != 'edit.php' ) {
			return $query;
		}

		$meta_query = $tax_query = array(
			'relation' => 'AND' ,
		);

		// Filtering By Source Type
		if( isset( $_GET[ 'source_type' ] ) && $_GET[ 'source_type' ] != '' ) {
			$selected_types = array();
			$get_type       = $_GET[ 'source_type' ];
			foreach ( (array) $wpvr_vs as $vs ) {
				foreach ( (array) $vs[ 'types' ] as $vs_type ) {
					if( $vs_type[ 'global_id' ] == $get_type ) {
						if( ! isset( $a[ $vs_type[ 'global_id' ] ] ) ) {
							$a[ $vs_type[ 'global_id' ] ] = array();
						}
						$selected_types[] = $vs_type[ 'id' ];
					}
				}
			}

			if( count( $selected_types ) > 0 ) {
				$meta_query[] = array(
					'key'     => 'wpvr_source_type' ,
					'value'   => $selected_types ,
					'compare' => 'IN' ,
				);
			}
		}

		// Filtering By Source Service
		if( isset( $_GET[ 'source_folder' ] ) && $_GET[ 'source_folder' ] != '' ) {
			$tax_query[] = array(
				'taxonomy' => WPVR_SFOLDER_TYPE ,
				'field'    => 'term_id' ,
				'terms'    => array( $_GET[ 'source_folder' ] ) ,
			);
		}

		// Filtering By Source Service
		if( isset( $_GET[ 'source_service' ] ) && $_GET[ 'source_service' ] != '' ) {
			$selected_service = $_GET[ 'source_service' ];
			$meta_query[]     = array(
				'key'   => 'wpvr_source_service' ,
				'value' => $selected_service ,
			);
		}

		// Filtering By Source Author
		if( isset( $_GET[ 'source_author' ] ) && $_GET[ 'source_author' ] != '' ) {
			if( $_GET[ 'source_author' ] == $wpvr_options[ 'postAuthor' ] ) {
				$values = array( 'default' , $_GET[ 'source_author' ] );
			} else {
				$values = array( $_GET[ 'source_author' ] );
			}

			$meta_query[] = array(
				'key'     => 'wpvr_source_postAuthor' ,
				'value'   => $values ,
				'compare' => 'IN' ,
			);
		}


		//d( $meta_query );
		$query->set( 'meta_query' , $meta_query );
		$query->set( 'tax_query' , $tax_query );
		//d( $_GET);
		//d( $tax_query);
		//d( $query);
		return $query;
	}


	/* Hide Publishing  button on edit sources screen */
	add_action( 'admin_head-post.php' , 'wpvr_sources_hide_publishing_actions' );
	add_action( 'admin_head-post-new.php' , 'wpvr_sources_hide_publishing_actions' );
	function wpvr_sources_hide_publishing_actions() {
		global $post;
		if( $post->post_type == WPVR_SOURCE_TYPE ) {
			?>
			<style type = "text/css">
				#misc-publishing-actions, #minor-publishing-actions {
					display: none;
				}
			</style>
			<?php
		}
	}


	/* Customize WP Messages for sources editing */
	add_filter( 'post_updated_messages' , 'wpvr_source_custom_updated_message' );
	function wpvr_source_custom_updated_message( $messages ) {
		global $post , $post_ID;
		$testLink = admin_url( 'admin.php?page=wpvr&test_sources&ids=' . $post->ID , 'http' );
		$runLink  = admin_url( 'admin.php?page=wpvr&run_sources&ids=' . $post->ID , 'http' );

		$messages[ WPVR_SOURCE_TYPE ] = array(
			0  => '' ,
			// Unused. Messages start at index 1.
			1  => sprintf( __( 'Source updated. <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this source</a> <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this source</a>' , WPVR_LANG ) , $testLink , $runLink ) ,
			2  => __( 'Custom field updated.' , WPVR_LANG ) ,
			3  => __( 'Custom field deleted.' , WPVR_LANG ) ,
			4  => __( 'Source updated.' , WPVR_LANG ) ,
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET[ 'revision' ] ) ? sprintf( __( 'Source restored to revision from %s' , WPVR_LANG ) , wp_post_revision_title( (int) $_GET[ 'revision' ] , FALSE ) ) : FALSE ,
			6  => sprintf( __( 'Source updated. <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this source</a> <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this source</a>' , WPVR_LANG ) , $testLink , $runLink ) ,
			//6 => sprintf( __('Source published. <a target = "_blank" href="%s">Test Source</a>', WPVR_LANG ), $testLink ),
			7  => __( 'Source saved.' , WPVR_LANG ) ,
			8  => sprintf( __( 'Source updated. <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this source</a> <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this source</a>' , WPVR_LANG ) , $testLink , $runLink ) ,
			//8 => sprintf( __('Source submitted. <a target="_blank" href="%s">Test Source</a>', WPVR_LANG ), $testLink ),
			9  => sprintf( __( 'Source updated. <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this source</a> <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this source</a>' , WPVR_LANG ) , $testLink , $runLink ) ,
			//9 => sprintf( __('Source scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Test source</a>', WPVR_LANG ),
			// translators: Publish box date format, see http://php.net/date
			// date_i18n( __( 'M j, Y @ G:i' , WPVR_LANG ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __( 'Source updated. <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this source</a> <a class="add-new-h2 wpvr_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this source</a>' , WPVR_LANG ) , $testLink , $runLink ) ,
			//10 => sprintf( __('Source draft updated. <a target="_blank" href="%s">Test source</a>', WPVR_LANG ), $testLink ),
		);

		return $messages;
	}

	/* Adding search filter for admin sources list screen */
	add_filter( 'posts_join' , 'wpvr_source_search_join' );
	function wpvr_source_search_join( $join ) {
		global $pagenow , $wpdb;
		// I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni"
		if( is_admin() && $pagenow == 'edit.php' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == WPVR_SOURCE_TYPE && isset( $_GET[ 's' ] ) && $_GET[ 's' ] != '' ) {
			$join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
		}

		return $join;
	}

	add_filter( 'posts_where' , 'wpvr_source_search_where' );
	function wpvr_source_search_where( $where ) {
		global $pagenow , $wpdb;
		// I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni"
		if( is_admin() && $pagenow == 'edit.php' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == WPVR_SOURCE_TYPE && isset( $_GET[ 's' ] ) && $_GET[ 's' ] != '' ) {
			$where = preg_replace(
				"/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/" ,
				"(" . $wpdb->posts . ".post_title LIKE $1) OR ( " . $wpdb->postmeta . ".meta_key='wpvr_source_name' AND " . $wpdb->postmeta . ".meta_value LIKE $1)" , $where );
		}

		return $where;
	}

	/* Hiding sources of Inactive services */
	//add_filter('parse_query' , 'wpvr_show_only_active_services_sources_old');
	function wpvr_show_only_active_services_sources_old( $query ) {
		global $pagenow , $wpvr_options , $wpvr_vs_ids;

		if( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		} else {
			$type = "post";
		}

		if( $type != WPVR_SOURCE_TYPE || ! is_admin() || $pagenow != 'edit.php' ) {
			return $query;
		}

		$query_type = array();
		//d( $wpvr_vs_ids );
		$query_service = array(
			'key'     => 'wpvr_source_service' ,
			'value'   => $wpvr_vs_ids[ 'ids' ] ,
			'compare' => 'IN' ,
		);

		$query->query_vars[ 'meta_query' ] = array(
			'relation' => 'AND' ,
			$query_service ,
			$query_type ,
		);

		return $query;
	}
