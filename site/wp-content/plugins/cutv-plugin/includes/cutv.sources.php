<?php


/* Defining channels as cutv_pages */
add_action( 'admin_notices' , 'cutv_channels_define_cutv_pages' );
function cutv_channels_define_cutv_pages() {
	$type = 'post';
	if( isset( $_GET[ 'post_type' ] ) ) {
		$type = $_GET[ 'post_type' ];
	}
	if( CUTV_CHANNEL_TYPE == $type ) {
		global $cutv_pages;
		$cutv_pages = TRUE;
	}
}

/* Defining Duplicate Channel actions */
add_action( 'admin_action_duplicate_channel' , 'cutv_channel_duplicate_fct' );
function cutv_channel_duplicate_fct() {
	global $wpdb;
	if( ! ( isset( $_GET[ 'post' ] ) || isset( $_POST[ 'post' ] ) || ( isset( $_REQUEST[ 'action' ] ) && 'duplicate_channel' == $_REQUEST[ 'action' ] ) ) ) {
		wp_die( 'No post to duplicate has been supplied!' );
	}
	$post_id = ( isset( $_GET[ 'post' ] ) ? $_GET[ 'post' ] : $_POST[ 'post' ] );
	cutv_duplicate_channel( $post_id , $singleDuplicate = TRUE );
}

/* Defining The Csutom channel type*/
add_action( 'init' , 'cutv_define_channels_post_type' , 0 );
function cutv_define_channels_post_type() {
	if( CUTV_META_DEBUG_MODE ) {
		$channels_support = array( 'custom-fields' );
	} else {
		$channels_support = array( '' );
	}

	$channels_support = apply_filters('cutv_extend_channels_support' , $channels_support );

	$labels = array(
		'name'               => _x( 'Channels' , 'Post Type General Name' , CUTV_LANG ) ,
		'singular_name'      => _x( 'Channel' , 'Post Type Singular Name' , CUTV_LANG ) ,
		'menu_name'          => __( 'Channels' , CUTV_LANG ) ,
		'parent_item_colon'  => __( 'Parent Channel:' , CUTV_LANG ) ,
		'all_items'          => __( 'All Channels' , CUTV_LANG ) ,
		'view_item'          => __( 'View Channel' , CUTV_LANG ) ,
		'add_new_item'       => __( 'Add New Channel' , CUTV_LANG ) ,
		'add_new'            => __( 'New Channel' , CUTV_LANG ) ,
		'edit_item'          => __( 'Edit Channel' , CUTV_LANG ) ,
		'update_item'        => __( 'Update Channel' , CUTV_LANG ) ,
		'search_items'       => __( 'Search channels' , CUTV_LANG ) ,
		'not_found'          => __( 'No channels found' , CUTV_LANG ) ,
		'not_found_in_trash' => __( 'No channels found in Trash' , CUTV_LANG ) ,
	);
	$args   = array(
		'label'               => __( 'channel' , CUTV_LANG ) ,
		'description'         => __( 'CUTV Channels' , CUTV_LANG ) ,
		'labels'              => $labels ,
		//'supports'            => array( 'title','custom-fields' ), //DEBUG LINE
		'supports'            => $channels_support ,
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
	register_post_type( CUTV_CHANNEL_TYPE , $args );
}

// Register Custom Taxonomy
add_action( 'init' , 'cutv_define_channel_folders' , 0 );
function cutv_define_channel_folders() {
	$labels = array(
		'name'                       => _x( 'Channel Folders' , 'Taxonomy General Name' , 'cutv_lang' ) ,
		'singular_name'              => _x( 'Channel Folder' , 'Taxonomy Singular Name' , 'cutv_lang' ) ,
		'menu_name'                  => __( 'Channel Folders' , 'cutv_lang' ) ,
		'all_items'                  => __( 'All Folders' , 'cutv_lang' ) ,
		'parent_item'                => __( 'Parent Folder' , 'cutv_lang' ) ,
		'parent_item_colon'          => __( 'Parent Folder:' , 'cutv_lang' ) ,
		'new_item_name'              => __( 'New Folder Name' , 'cutv_lang' ) ,
		'add_new_item'               => __( 'Add New Folder' , 'cutv_lang' ) ,
		'edit_item'                  => __( 'Edit Folder' , 'cutv_lang' ) ,
		'update_item'                => __( 'Update Folder' , 'cutv_lang' ) ,
		'view_item'                  => __( 'View Folder' , 'cutv_lang' ) ,
		'separate_items_with_commas' => __( 'Separate folders with commas' , 'cutv_lang' ) ,
		'add_or_remove_items'        => __( 'Add or remove folders' , 'cutv_lang' ) ,
		'choose_from_most_used'      => __( 'Choose from the most used' , 'cutv_lang' ) ,
		'popular_items'              => __( 'Popular folders' , 'cutv_lang' ) ,
		'search_items'               => __( 'Search folders' , 'cutv_lang' ) ,
		'not_found'                  => __( 'Not Found' , 'cutv_lang' ) ,
		'no_terms'                   => __( 'No folders' , 'cutv_lang' ) ,
		'items_list'                 => __( 'Folders list' , 'cutv_lang' ) ,
		'items_list_navigation'      => __( 'Folders list navigation' , 'cutv_lang' ) ,
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
	register_taxonomy( CUTV_SFOLDER_TYPE , array( 'cutv_channel' ) , $args );

}


/*Manage Custom Columns on Channels list */
add_filter( 'manage_edit-' . CUTV_CHANNEL_TYPE . '_columns' , 'cutv_channel_define_custom_columns' );
function cutv_channel_define_custom_columns( $columns ) {
	unset( $columns );
	$columns = array(
		'cb'             => '<input type="checkbox"/>' ,
		'name'           => __( 'Name' , CUTV_LANG ) ,
		'stats'          => __( 'Statistics' , CUTV_LANG ) ,
		'info'    => __( 'Informations' , CUTV_LANG ) ,
		'options' => __( 'Settings' , CUTV_LANG ) ,
		'status'         => __( 'Status' , CUTV_LANG ) ,
	);

	return $columns;
}

/*Manage Custom Columns on Channels list */
add_action( 'manage_'.CUTV_CHANNEL_TYPE.'_posts_custom_column' , 'cutv_channel_manage_custom_columns' );
function cutv_channel_manage_custom_columns( $column ) {
	global $post ;

	if( isset( $_GET['post_status'] ) && $_GET['post_status'] != '' ) $post_status = $_GET['post_status'] ;
	else $post_status = 'publish';
	//d( $post_status );
	echo cutv_get_channel_columns( $post->ID , $column , $post_status );
	if( $column == 'status' ) unset( $_SESSION[ 'tmp_channels_columns' ] );
}


/*Manage Custom Columns on Channels Folders list */
add_filter( 'manage_edit-' . CUTV_SFOLDER_TYPE . '_columns' , 'cutv_channel_folders_define_custom_columns' );
function cutv_channel_folders_define_custom_columns( $columns ) {
	$columns['actions'] = __('Channels Actions' , CUTV_LANG );
	return $columns;
}

/*Manage Custom Columns on Channels Folders list */
add_action( 'manage_'.CUTV_SFOLDER_TYPE.'_custom_column' , 'cutv_channel_folders_manage_custom_columns' , 10 , 3  );
function cutv_channel_folders_manage_custom_columns( $value, $column_name, $folder_id ) {
	//d( $column_name );
	if( $column_name == 'actions'){

		$testLink      = admin_url( 'admin.php?page=cutv&test_channels&folders=' . $folder_id, 'http' );
		$runLink       = admin_url( 'admin.php?page=cutv&run_channels&folders=' . $folder_id , 'http' );
		$exportLink    = admin_url( 'admin.php?page=cutv&export_channels&folders=' . $folder_id , 'http' );

		$more = apply_filters( 'cutv_extend_channel_folder_column_actions' , '' , $folder_id );

		return '
				<div class = "cutv_channel_action_button pull-left">
					<a href = "' . $testLink . '" target = "_blank">
						<i class = "cutv_link_icon fa fa-eye"></i>
						' . __( 'Test' , CUTV_LANG ) . '
					</a>
				</div>
				<div class = "cutv_channel_action_button pull-left ">
					<a href = "' . $runLink . '" target = "_blank">
						<i class = "cutv_link_icon fa fa-bolt"></i>
						' . __( 'Run' , CUTV_LANG ) . '
					</a>
				</div>
				<div class="cutv_clearfix"></div>
				<div class = "cutv_channel_action_button cutv_black_button">
					<a href = "' . $exportLink . '" target = "_blank">
						<i class = "cutv_link_icon fa fa-upload"></i>
						' . __( 'Export' , CUTV_LANG ) . '
					</a>
				</div>
				'.$more.'
			';
	}
	return $value;
}


/* Initialize metaboxes on channels */
add_action( 'init' , 'cutv_channel_init_metaboxes' , 9999 );
function cutv_channel_init_metaboxes() {
	if( ! class_exists( 'cutv_cmb_Meta_Box' ) ) {
		require_once( CUTV_PATH . '/assets/metabox/init.php' );
	}
}

/* Define Channels Metaboxes */
add_filter( 'cutv_cmb_meta_boxes' , 'cutv_channel_define_metaboxes' );
function cutv_channel_define_metaboxes( $meta_boxes ) {
	$prefix       = 'cutv_channel_';
	$authorsArray = cutv_get_authors( $invert = TRUE , $default = TRUE , $restrict = TRUE );

	global $cutv_types_ , $cutv_services , $post;

	global $cutv_hours , $cutv_days_names , $cutv_countries;


	if( CUTV_ACCEPT_EMPTY_CHANNEL_NAMES ) {
		$accept_empty_channel_name = 'canBeEmpty';
	} else {
		$accept_empty_channel_name = '';
	}

	if( CUTV_MAX_WANTED_VIDEOS === FALSE ) {
		$max_wanted_videos = '';
	} else {
		$max_wanted_videos = CUTV_MAX_WANTED_VIDEOS;
	}

	/* Extending Video Services Options */
	$video_service_options = array();
	$video_service_options = apply_filters( 'cutv_extend_video_services_options' , $video_service_options );

	/* Extending Video Services Types Options */
	$video_service_types_options = array();
	$video_service_types_options = apply_filters( 'cutv_extend_video_services_types_options' , $video_service_types_options );

	/* Extending Video Services Types Options */
	$channel_fields = array();
	$channel_fields = apply_filters( 'cutv_extend_video_services_types_fields' , $channel_fields , $prefix );

	$channel_basics       = array(
		array(
			'name' => __( 'Name' , CUTV_LANG ) ,
			'desc' => '' ,
			'id'   => $prefix . 'name' ,
			'type' => 'text' ,
		) ,
		array(
			'name'        => __( 'Video Service' , CUTV_LANG ) ,
			'desc'        => '' ,
			'id'          => $prefix . 'service' ,
			'type'        => 'radio_inline' ,
			'options'     => $video_service_options ,
			'default'     => 'youtube' ,
			'cutvClass'   => $accept_empty_channel_name ,
			'cutvService' => $max_wanted_videos ,
		) ,
		array(
			'name'        => __( 'Channel Type' , CUTV_LANG ) ,
			'desc'        => '' ,
			'id'          => $prefix . 'type' ,
			'type'        => 'radio_inline' ,
			'options'     => $video_service_types_options ,
			'default'     => '' ,
			'cutvClass'   => 'channelType' ,
			'cutvStyle'   => 'display:none;' ,
			'cutvService' => 'koko' ,
		) ,
	);
	$channel_infos_fields = array_merge( $channel_basics , $channel_fields );

	$meta_boxes[]   = array(
		'id'         => 'cutv_channel_metabox' ,
		'title'      => '<i class="fa fa-info-circle"></i> ' . __( 'Channel Information' , CUTV_LANG ) ,
		'pages'      => array( CUTV_CHANNEL_TYPE ) , // post type
		'context'    => 'normal' ,
		'priority'   => 'high' ,
		'show_names' => TRUE , // Show field names on the left
		'fields'     => $channel_infos_fields ,
	);
	$meta_boxes[]   = array(
		'id'         => 'cutv_channel_options_metabox' ,
		'title'      => '<i class="fa fa-search"></i> ' . __( 'Channel Fetching Options' , CUTV_LANG ) ,
		'pages'      => array( CUTV_CHANNEL_TYPE ) , // post type
		'context'    => 'normal' ,
		'priority'   => 'high' ,
		'show_names' => TRUE , // Show field names on the left
		'fields'     => array(
			array(
				'name'    => __( 'Wanted Videos' , CUTV_LANG ) ,
				'id'      => $prefix . 'wantedVideosBool' ,
				'type'    => 'select' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'custom'  => __( 'Custom number' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,
			array(
				'name'            => __( 'Number of videos' , CUTV_LANG ) ,
				'desc'            => __( 'How many videos to get at a time?' , CUTV_LANG ) . ' ' .
					__( 'Max' , CUTV_LANG ) . ' : ' . ( ( CUTV_MAX_WANTED_VIDEOS === FALSE ) ? __( 'Unlimited' , CUTV_LANG ) : CUTV_MAX_WANTED_VIDEOS ) ,
				'default'         => '5' ,
				'id'              => $prefix . 'wantedVideos' ,
				'type'            => 'text_small' ,
				'cutvStyle'       => 'display:none;' ,
				'cutvClass'       => 'cutv_has_master' ,
				'cutv_attributes' => array(
					'master_id'    => $prefix . 'wantedVideosBool' ,
					'master_value' => 'custom' ,
				) ,
			) ,
			array(
				'name'    => __( 'Order by' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'order' ,
				'type'    => 'select' ,
				'options' => array(
					'default'   => __( '- Default -' , CUTV_LANG ) ,
					'relevance' => __( 'Relevance' , CUTV_LANG ) ,
					'date'      => __( 'Date' , CUTV_LANG ) ,
					'viewCount' => __( 'Views' , CUTV_LANG ) ,
					'title'     => __( 'Title' , CUTV_LANG ) ,
					'rating'    => __( 'Rating' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,
			array(
				'name'    => __( 'Duplicates' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'onlyNewVideos' ,
				'type'    => 'select' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'on'      => __( 'Skip duplicates' , CUTV_LANG ) ,
					'off'     => __( 'Do not skip duplicates' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,
			array(
				'name'    => __( 'Statistics' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'getVideoStats' ,
				'type'    => 'select' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'on'      => __( 'Get Video Statistics' , CUTV_LANG ) ,
					'off'     => __( 'Do not get Video Statistics' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,
			array(
				'name'    => __( 'Tags' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'getVideoTags' ,
				'type'    => 'select' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'on'      => __( 'Get Video Tags' , CUTV_LANG ) ,
					'off'     => __( 'Do not get Video Tags' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,

		) ,
	);
	$edit_cats_link = admin_url( 'edit-tags.php?taxonomy=category' );
	$catsArray      = array(
		'' => __( 'Choose one or more categories' , CUTV_LANG ) ,
	);
	if( CUTV_HIERARCHICAL_CATS_ENABLED === TRUE ) {
		$cats = cutv_get_hierarchical_cats();
	} else {
		$cats = cutv_get_categories_count( $invert = FALSE , $get_empty = TRUE );
	}
	foreach ( $cats as $cat ) {
		$catsArray[ $cat[ 'value' ] ] = $cat[ 'label' ];
	}

	$tagsArray = array( '' => __( 'Enter one or more tags' , CUTV_LANG ) );


	$meta_boxes[] = array(
		'id'         => 'cutv_channel_posting_metabox' ,
		'title'      => '<i class="fa fa-cloud-upload"></i> ' . __( 'Channel Posting Options' , CUTV_LANG ) ,
		'pages'      => array( CUTV_CHANNEL_TYPE ) , // post type
		'context'    => 'normal' ,
		'priority'   => 'high' ,
		'show_names' => TRUE , // Show field names on the left
		'fields'     => array(
			array(
				'name'    => __( 'AutoPublish' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'autoPublish' ,
				'type'    => 'select' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'on'      => __( 'AutoPublish' , CUTV_LANG ) ,
					'off'     => __( 'Post as draft' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,
			array(
				'name'      => __( 'Post to' , CUTV_LANG ) . ' HIDDEN' ,
				'id'        => $prefix . 'postCats' ,
				'type'      => 'text' ,
				'default'   => '' ,
				'cutvClass' => 'cutv_selectize_values' ,
				'cutvStyle' => 'display:none;' ,
			) ,
			array(
				'name'         => __( 'Post to' , CUTV_LANG ) ,
				'desc'         => '<a href="' . $edit_cats_link . '" target="_blank">' .
					__( 'Edit or Add the Categories' , CUTV_LANG ) .
					'</a>'
			,
				'id'           => $prefix . 'postCats_' ,
				'type'         => 'select' ,
				'options'      => $catsArray ,
				'cutvClass'    => 'cutv_cmb_selectize' ,
				'cutvMaxItems' => CUTV_MAX_POSTING_CATS ,
				//'default' => '',
			) ,
			array(
				'name'    => __( 'Post Author' , CUTV_LANG ) ,
				'desc'    => __( 'Choose the author of the autoposting' , CUTV_LANG ) ,
				'id'      => $prefix . 'postAuthor' ,
				'type'    => 'select' ,
				'options' => $authorsArray ,
				'default' => 'default' ,
			) ,
			array(
				'name'    => __( 'Post Date' , CUTV_LANG ) ,
				'desc'    => __( 'Choose the date of the autoposting' , CUTV_LANG ) ,
				'id'      => $prefix . 'postDate' ,
				'type'    => 'select' ,
				'options' => array(
					'default'  => __( '- Default -' , CUTV_LANG ) ,
					'original' => __( 'Original Date' , CUTV_LANG ) ,
					'new'      => __( 'Updated Date' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,

			array(
				'name'    => __( 'Post Title Affix' , CUTV_LANG ) ,
				'desc'    => __( 'Choose to add the name of the channel or a custom text before or after the video title.' , CUTV_LANG ) ,
				'id'      => $prefix . 'postAppend' ,
				'type'    => 'select' ,
				'options' => array(
					'off'          => __( 'Disabled' , CUTV_LANG ) ,
					'before'       => __( 'Add channel name before the video title' , CUTV_LANG ) ,
					'after'        => __( 'Add channel name after the video title' , CUTV_LANG ) ,
					'customBefore' => __( 'Add custom text before the video title' , CUTV_LANG ) ,
					'customAfter'  => __( 'Add custom text after the video title' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,
			array(
				'name'            => __( 'Custom Text Affix' , CUTV_LANG ) ,
				'desc'            => __( 'Choose a custom text to add before or after the video title.' , CUTV_LANG ) ,
				'id'              => $prefix . 'appendCustomText' ,
				'default'         => '' ,
				'type'            => 'text' ,
				'cutvStyle'       => 'display:none;' ,
				'cutvClass'       => 'cutv_has_master' ,
				'cutv_attributes' => array(
					'master_id'    => $prefix . 'postAppend' ,
					'master_value' => 'customBefore,customAfter' ,
				) ,

			) ,
			array(
				'name'    => __( 'Post Tags' , CUTV_LANG ) ,
				'desc'    => __( 'Choose whether to auto apply tags to the imported videos of this channel.' , CUTV_LANG ) ,
				'id'      => $prefix . 'postTagsBool' ,
				'type'    => 'select' ,
				'options' => array(
					'disabled' => __( 'Disabled' , CUTV_LANG ) ,
					'default'  => __( 'Default Tags' , CUTV_LANG ) ,
					'custom'   => __( 'Custom Tags' , CUTV_LANG ) ,
				) ,
				'default' => 'disabled' ,
			) ,
			array(
				'name'            => __( 'Post Tags' , CUTV_LANG ) . '' ,
				'id'              => $prefix . 'postTags' ,
				'type'            => 'textarea' ,
				'desc'            => __( 'Enter your custom tags.' , CUTV_LANG ) ,
				'default'         => '' ,
				'cutvStyle'       => 'display:none;' ,
				'cutvClass'       => 'cutv_has_master' ,
				'cutv_attributes' => array(
					'master_id'    => $prefix . 'postTagsBool' ,
					'master_value' => 'custom' ,
				) ,
			) ,

			array(
				'name'    => __( 'Video Text Content' , CUTV_LANG ) ,
				'desc'    => __( 'Choose whether to import the video text content or not.' , CUTV_LANG ) ,
				'id'      => $prefix . 'postContent' ,
				'type'    => 'select' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'on'      => __( 'Post the video text content' , CUTV_LANG ) ,
					'off'     => __( 'Skip the video text content' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,

		) ,
	);

	$meta_boxes[] = array(
		'id'         => 'cutv_channel_filtering_metabox' ,
		'title'      => '<i class="fa fa-filter"></i> ' . __( 'Channel Filtering Options' , CUTV_LANG ) ,
		'pages'      => array( CUTV_CHANNEL_TYPE ) , // post type
		'context'    => 'normal' ,
		'priority'   => 'high' ,
		'show_names' => TRUE , // Show field names on the left
		'fields'     => array(
			array(
				'name'    => __( 'Published After' , CUTV_LANG ) ,
				'id'      => $prefix . 'publishedAfter_bool' ,
				'type'    => 'select' ,
				'desc'    => '' . __( 'Import only videos published after this date.' , CUTV_LANG ) . ' ' .
					__( 'Leave empty to ignore this criterion.' , CUTV_LANG ) .
					'<br/><strong>' . __( 'Supported only by Youtube and Dailymotion.' , CUTV_LANG ) . '</strong>' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'custom'  => __( 'Custom' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,
			array(
				'name'            => __( 'Published After' , CUTV_LANG ) . ' (Date)' ,
				'id'              => $prefix . 'publishedAfter' ,
				'type'            => 'text_date' ,
				'default'         => '' ,
				'cutvStyle'       => 'display:none;' ,
				'cutvClass'       => 'cutv_has_master' ,
				'cutv_attributes' => array(
					'master_id'    => $prefix . 'publishedAfter_bool' ,
					'master_value' => 'custom' ,
				) ,
			) ,

			array(
				'name' => __( 'Published Before' , CUTV_LANG ) ,
				'id'   => $prefix . 'publishedBefore_bool' ,
				'desc' => '' . __( 'Import only videos published before this date.' , CUTV_LANG ) . ' ' .
					__( 'Leave empty to ignore this criterion.' , CUTV_LANG ) .
					'<br/><strong>' . __( 'Supported only by Youtube and Dailymotion.' , CUTV_LANG ) . '</strong>' ,

				'type'    => 'select' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'custom'  => __( 'Custom' , CUTV_LANG ) ,
				) ,
				'default' => 'default' ,
			) ,

			array(
				'name'            => __( 'Published Before' , CUTV_LANG ) . ' (Date)' ,
				'id'              => $prefix . 'publishedBefore' ,
				'type'            => 'text_date' ,
				'default'         => '' ,
				'cutvStyle'       => 'display:none;' ,
				'cutvClass'       => 'cutv_has_master' ,
				'cutv_attributes' => array(
					'master_id'    => $prefix . 'publishedBefore_bool' ,
					'master_value' => 'custom' ,
				) ,
			) ,
			array(
				'name'    => __( 'Duration' , CUTV_LANG ) ,
				'desc'    => __( 'Filter fetched videos by duration. Note it works only for Search channels.' , CUTV_LANG ) .
					'<br/><strong>' . __( 'Supported only by Youtube and Dailymotion.' , CUTV_LANG ) . '</strong>' ,
				'id'      => $prefix . 'videoDuration' ,
				'type'    => 'select' ,
				'options' => array(
					'default' => __( '- Default -' , CUTV_LANG ) ,
					'any'     => __( 'All Videos' , CUTV_LANG ) ,
					'short'   => __( 'Videos less than 4min.' , CUTV_LANG ) ,
					'medium'  => __( 'Videos between 4min. and 20min.' , CUTV_LANG ) ,
					'long'    => __( 'Videos longer than 20min.' , CUTV_LANG ) ,

				) ,
				'default' => 'default' ,
			) ,
			array(
				'name'    => __( 'Video Quality' , CUTV_LANG ) ,
				'desc'    => __( 'Filter fetched videos by quality and definition.' , CUTV_LANG ) . '<br/>' .
					'<br/><strong>' . __( 'Supported only by Youtube, Vimeo and Dailymotion.' , CUTV_LANG ) . '</strong>' ,
				'id'      => $prefix . 'videoQuality' ,
				'type'    => 'select' ,
				'options' => array(
					'default'  => __( '- Default -' , CUTV_LANG ) ,
					'any'      => __( 'All Videos' , CUTV_LANG ) ,
					'high'     => __( 'Only High Definition Videos' , CUTV_LANG ) ,
					'standard' => __( 'Only Standard Definitions Videos' , CUTV_LANG ) ,
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
		//$shortcode = '[cutv id='.$post_id.']';
	} elseif( isset( $_POST[ 'post_ID' ] ) ) {
		$post_id = $_POST[ 'post_ID' ];
		//$shortcode = '[cutv id='.$post_id.']';
	} else {
		$post_id = "";
		//$shortcode = "Save First";
	}

	$actionButtons = cutv_render_channel_actions( $post_id );

	$meta_boxes[] = array(
		'id'         => 'cutv_channel_status_metabox' ,
		'title'      => '<i class="fa fa-play-circle"></i> ' . __( 'Channel Actions' , CUTV_LANG ) ,
		'pages'      => array( CUTV_CHANNEL_TYPE ) , // post type
		'context'    => 'side' ,
		'priority'   => 'high' ,
		'show_names' => FALSE , // Show field names on the left
		'fields'     => array(

			array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html_preload' ,
				'html'      => '<div style="text-align:center;">'.__('Loading ...' , CUTV_LANG ).'</div>' ,
				'type'      => 'show_html' ,
				'cutvClass' => 'cutv_metabox_html cutv_hide_when_loaded' ,
				'cutvStyle' => '' ,
			) ,

			array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $actionButtons[ 'test' ] ,
				'type'      => 'show_html' ,
				'cutvClass' => 'cutv_metabox_html cutv_show_when_loaded' ,
				'cutvStyle' => 'display:none;' ,
			) ,
			array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $actionButtons[ 'run' ] ,
				'type'      => 'show_html' ,
				'cutvClass' => 'cutv_metabox_html cutv_show_when_loaded' ,
				'cutvStyle' => 'display:none;' ,
			) ,

			array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $actionButtons[ 'clone' ] ,
				'type'      => 'show_html' ,
				'cutvClass' => 'cutv_metabox_html cutv_show_when_loaded' ,
				'cutvStyle' => 'display:none;' ,
			) ,

			array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $actionButtons[ 'save' ] ,
				'type'      => 'show_html' ,
				'cutvClass' => 'cutv_metabox_html cutv_show_when_loaded' ,
				'cutvStyle' => 'display:none;' ,
			) ,

			array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $actionButtons[ 'trash' ] ,
				'type'      => 'show_html' ,
				'cutvClass' => 'cutv_metabox_html cutv_show_when_loaded' ,
				'cutvStyle' => 'display:none;' ,
			) ,


			array(
				'name'      => __( 'Plugin Version' , CUTV_LANG ) ,
				'default'   => CUTV_VERSION ,
				'id'        => $prefix . 'plugin_version' ,
				'type'      => 'text_small' ,
				'cutvStyle' => 'display:none;' ,
			) ,
		) ,
	);
	$meta_boxes[] = array(
		'id'         => 'cutv_channel_scheduling_metabox' ,
		'title'      => '<i class="fa fa-calendar"></i> ' . __( 'Channel Schedule' , CUTV_LANG ) ,
		'pages'      => array( CUTV_CHANNEL_TYPE ) , // post type
		'context'    => 'side' ,
		'priority'   => 'high' ,
		'show_names' => TRUE , // Show field names on the left
		'fields'     => array(
			array(
				'name'    => __( 'Channel is active' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'status' ,
				'type'    => 'select' ,
				'options' => array(
					'on'  => __( 'YES' , CUTV_LANG ) ,
					'off' => __( 'NO' , CUTV_LANG ) ,
				) ,
				'default' => 'off' ,
			) ,
			array(
				'name'    => __( 'Scheduled Cron Job' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'schedule' ,
				'type'    => 'select' ,
				'options' => array(
					'hourly' => __( 'Run Hourly' , CUTV_LANG ) ,
					'daily'  => __( 'Run Daily' , CUTV_LANG ) ,
					'weekly' => __( 'Run Weekly' , CUTV_LANG ) ,
					'once'   => __( 'Run Once' , CUTV_LANG ) ,
				) ,
				'default' => 'hourly' ,
			) ,
			array(
				'name' => __( 'Choose a date' , CUTV_LANG ) ,
				'desc' => '' ,
				'id'   => $prefix . 'schedule_date' ,
				'type' => 'text_date_timestamp' ,

			) ,
			array(
				'name'    => __( 'Choose a day' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'schedule_day' ,
				'type'    => 'select' ,
				'options' => $cutv_days_names ,
				'default' => 'monday' ,
			) ,
			array(
				'name'    => __( 'Choose a time' , CUTV_LANG ) ,
				'desc'    => '' ,
				'id'      => $prefix . 'schedule_time' ,
				'type'    => 'select' ,
				'options' => $cutv_hours ,
				'default' => '04H00' ,
			) ,
		) ,
	);
	if( $post_id != '' ) {
		$channel = cutv_get_channel( $post_id );
		if( $channel != FALSE ) {
			$channel_type  = $channel->type;
			$wantedVideos = ( ! isset( $channel->wantedVideos ) || ( $channel->wantedVideos == '' ) ) ? 0 : $channel->wantedVideos;
			if( $channel_type == 'channel' ) {
				$subchannels       = count( cutv_parse_string( $channel->channelIds ) );
				$subchannels_label = __( 'channels' , CUTV_LANG );
				$subchannels_line  = ' <b>' . cutv_numberK( $subchannels , TRUE ) . '</b> ' . $subchannels_label . '<br/>';

			} elseif( $channel_type == 'playlist' ) {
				$subchannels       = count( cutv_parse_string( $channel->playlistIds ) );
				$subchannels_label = __( 'playlists' , CUTV_LANG );
				$subchannels_line  = ' <b>' . cutv_numberK( $subchannels , TRUE ) . '</b> ' . $subchannels_label . '<br/>';

			} else {
				$subchannels      = 0;
				$subchannels_line = '';
			}
			if( $subchannels > 1 ) {
				$wantedVideos = $wantedVideos * $subchannels;
			}

			//d($channel);
			$channel_stats_html = '';
			$channel_stats_html .= '<div  style="text-transform:uppercase;">';
			$channel_stats_html .= ' <b>' . cutv_numberK( $wantedVideos , TRUE ) . '</b> ' . __( 'Wanted videos' , CUTV_LANG ) . '<br/>';
			$channel_stats_html .= '<b>' . cutv_numberK( $channel->count_imported , TRUE ) . '</b> ' . __( 'Imported videos' , CUTV_LANG ) . '<br/>';
			$channel_stats_html .= $subchannels_line;
			$channel_stats_html .= __( 'TESTED' , CUTV_LANG ) . ' <strong>' . cutv_numberK( $channel->count_test , TRUE ) . '</strong> ' . __( 'times' , CUTV_LANG ) . '<br/>';
			$channel_stats_html .= __( 'RUN' , CUTV_LANG ) . ' <strong>' . cutv_numberK( $channel->count_run , TRUE ) . '</strong> ' . __( 'times' , CUTV_LANG ) . '<br/>';
			$channel_stats_html .= '</div>';
		} else {
			$channel_stats_html = '<div class="cutv_no_actions">' . __( 'Start by saving your channel' , CUTV_LANG ) . '</div>';
		}
	} else {
		$channel_stats_html = '<div class="cutv_no_actions">' . __( 'Start by saving your channel' , CUTV_LANG ) . '</div>';
	}
	$meta_boxes[] = array(
		'id'         => 'cutv_channel_stats_metabox' ,
		'title'      => '<i class="fa fa-bar-chart"></i> ' . __( 'Channel Stats' , CUTV_LANG ) ,
		'pages'      => array( CUTV_CHANNEL_TYPE ) , // post type
		'context'    => 'side' ,
		'priority'   => 'low' ,
		'show_names' => FALSE , // Show field names on the left
		'fields'     => array(
			array(
				'name'      => '' ,
				'desc'      => '' ,
				'id'        => $prefix . 'html' ,
				'html'      => $channel_stats_html ,
				'type'      => 'show_html' ,
				'cutvClass' => 'cutv_metabox_html' ,
			) ,
		) ,
	);
	$meta_boxes   = apply_filters( 'cutv_extend_channels_metaboxes' , $meta_boxes );

	return $meta_boxes;
}

/*Show CHANNEL TYPES select filter on channels list */
add_action( 'restrict_manage_posts' , 'cutv_channel_create_types_dropdown' );
function cutv_channel_create_types_dropdown() {
	$type = 'post';
	if( isset( $_GET[ 'post_type' ] ) ) {
		$type = $_GET[ 'post_type' ];
	}
	if( CUTV_CHANNEL_TYPE == $type ) {
		$typesArray = array();
		global $cutv_vs;

		foreach ( (array) $cutv_vs as $vs ) {
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
		<select name = "channel_type">
			<option value = ""><?php _e( 'Show all types' , CUTV_LANG ); ?></option>
			<?php
			$current_v = isset( $_GET[ 'channel_type' ] ) ? $_GET[ 'channel_type' ] : '';
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

/*Show CHANNEL TYPES select filter on channels list */
add_action( 'restrict_manage_posts' , 'cutv_channel_create_services_dropdown' );
function cutv_channel_create_services_dropdown() {
	$type = 'post';
	global $cutv_services;
	if( isset( $_GET[ 'post_type' ] ) ) {
		$type = $_GET[ 'post_type' ];
	}

	if( CUTV_CHANNEL_TYPE == $type ) {

		?>
		<select name = "channel_service">
			<option value = ""><?php _e( 'Show all services' , CUTV_LANG ); ?></option>
			<?php
			global $cutv_vs;
			$current_v = isset( $_GET[ 'channel_service' ] ) ? $_GET[ 'channel_service' ] : '';
			foreach ( $cutv_vs as $value => $vs ) {
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

/*Show CHANNEL FOLDERS  select filter on channels list */
add_action( 'restrict_manage_posts' , 'cutv_channel_create_folders_dropdown' );
function cutv_channel_create_folders_dropdown() {
	$type = 'post';
	global $cutv_services;
	if( isset( $_GET[ 'post_type' ] ) ) {
		$type = $_GET[ 'post_type' ];
	}

	if( CUTV_CHANNEL_TYPE == $type ) {
		$folders = cutv_get_folders_simple();
		?>
		<select name = "channel_folder">
			<option value = ""><?php _e( 'Show all folders' , CUTV_LANG ); ?></option>
			<?php
			global $cutv_vs;
			$current_v = isset( $_GET[ 'channel_folder' ] ) ? $_GET[ 'channel_folder' ] : '';
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


/*Show Authors select filter on channels list */
add_action( 'restrict_manage_posts' , 'cutv_channel_create_authors_dropdown' );
function cutv_channel_create_authors_dropdown() {
	if( isset( $_GET[ 'post_type' ] ) ) {
		$type = $_GET[ 'post_type' ];
	} else {
		$type = 'post';
	}
	if( CUTV_CHANNEL_TYPE == $type ) {
		$authorsArray = cutv_get_authors( $invert = FALSE , $default = FALSE , $restrict = TRUE );
		?>
		<select name = "channel_author">
			<option value = ""><?php _e( 'Show all authors' , CUTV_LANG ); ?></option>
			<?php
			$current_v = isset( $_GET[ 'channel_author' ] ) ? $_GET[ 'channel_author' ] : '';
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

/*Filtering channels list */
add_filter( 'pre_get_posts' , 'cutv_filter_channel_list' );
function cutv_filter_channel_list( $query ) {
	global $pagenow , $cutv_options;
	global $cutv_vs;

	if( isset( $_GET[ 'post_type' ] ) ) {
		$type = $_GET[ 'post_type' ];
	} else {
		$type = "post";
	}

	if( $type != CUTV_CHANNEL_TYPE || ! is_admin() || $pagenow != 'edit.php' ) {
		return $query;
	}

	$meta_query = $tax_query = array(
		'relation' => 'AND' ,
	);

	// Filtering By Channel Type
	if( isset( $_GET[ 'channel_type' ] ) && $_GET[ 'channel_type' ] != '' ) {
		$selected_types = array();
		$get_type       = $_GET[ 'channel_type' ];
		foreach ( (array) $cutv_vs as $vs ) {
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
				'key'     => 'cutv_channel_type' ,
				'value'   => $selected_types ,
				'compare' => 'IN' ,
			);
		}
	}

	// Filtering By Channel Service
	if( isset( $_GET[ 'channel_folder' ] ) && $_GET[ 'channel_folder' ] != '' ) {
		$tax_query[] = array(
			'taxonomy' => CUTV_SFOLDER_TYPE ,
			'field'    => 'term_id' ,
			'terms'    => array( $_GET[ 'channel_folder' ] ) ,
		);
	}

	// Filtering By Channel Service
	if( isset( $_GET[ 'channel_service' ] ) && $_GET[ 'channel_service' ] != '' ) {
		$selected_service = $_GET[ 'channel_service' ];
		$meta_query[]     = array(
			'key'   => 'cutv_channel_service' ,
			'value' => $selected_service ,
		);
	}

	// Filtering By Channel Author
	if( isset( $_GET[ 'channel_author' ] ) && $_GET[ 'channel_author' ] != '' ) {
		if( $_GET[ 'channel_author' ] == $cutv_options[ 'postAuthor' ] ) {
			$values = array( 'default' , $_GET[ 'channel_author' ] );
		} else {
			$values = array( $_GET[ 'channel_author' ] );
		}

		$meta_query[] = array(
			'key'     => 'cutv_channel_postAuthor' ,
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


/* Hide Publishing  button on edit channels screen */
add_action( 'admin_head-post.php' , 'cutv_channels_hide_publishing_actions' );
add_action( 'admin_head-post-new.php' , 'cutv_channels_hide_publishing_actions' );
function cutv_channels_hide_publishing_actions() {
	global $post;
	if( $post->post_type == CUTV_CHANNEL_TYPE ) {
		?>
		<style type = "text/css">
			#misc-publishing-actions, #minor-publishing-actions {
				display: none;
			}
		</style>
		<?php
	}
}


/* Customize WP Messages for channels editing */
add_filter( 'post_updated_messages' , 'cutv_channel_custom_updated_message' );
function cutv_channel_custom_updated_message( $messages ) {
	global $post , $post_ID;
	$testLink = admin_url( 'admin.php?page=cutv&test_channels&ids=' . $post->ID , 'http' );
	$runLink  = admin_url( 'admin.php?page=cutv&run_channels&ids=' . $post->ID , 'http' );

	$messages[ CUTV_CHANNEL_TYPE ] = array(
		0  => '' ,
		// Unused. Messages start at index 1.
		1  => sprintf( __( 'Channel updated. <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this channel</a> <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this channel</a>' , CUTV_LANG ) , $testLink , $runLink ) ,
		2  => __( 'Custom field updated.' , CUTV_LANG ) ,
		3  => __( 'Custom field deleted.' , CUTV_LANG ) ,
		4  => __( 'Channel updated.' , CUTV_LANG ) ,
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET[ 'revision' ] ) ? sprintf( __( 'Channel restored to revision from %s' , CUTV_LANG ) , wp_post_revision_title( (int) $_GET[ 'revision' ] , FALSE ) ) : FALSE ,
		6  => sprintf( __( 'Channel updated. <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this channel</a> <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this channel</a>' , CUTV_LANG ) , $testLink , $runLink ) ,
		//6 => sprintf( __('Channel published. <a target = "_blank" href="%s">Test Channel</a>', CUTV_LANG ), $testLink ),
		7  => __( 'Channel saved.' , CUTV_LANG ) ,
		8  => sprintf( __( 'Channel updated. <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this channel</a> <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this channel</a>' , CUTV_LANG ) , $testLink , $runLink ) ,
		//8 => sprintf( __('Channel submitted. <a target="_blank" href="%s">Test Channel</a>', CUTV_LANG ), $testLink ),
		9  => sprintf( __( 'Channel updated. <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this channel</a> <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this channel</a>' , CUTV_LANG ) , $testLink , $runLink ) ,
		//9 => sprintf( __('Channel scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Test channel</a>', CUTV_LANG ),
		// translators: Publish box date format, see http://php.net/date
		// date_i18n( __( 'M j, Y @ G:i' , CUTV_LANG ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __( 'Channel updated. <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-eye"></i>Test this channel</a> <a class="add-new-h2 cutv_notice_link" target = "_blank" href="%s"><i class="fa fa-bolt"></i>Run this channel</a>' , CUTV_LANG ) , $testLink , $runLink ) ,
		//10 => sprintf( __('Channel draft updated. <a target="_blank" href="%s">Test channel</a>', CUTV_LANG ), $testLink ),
	);

	return $messages;
}

/* Adding search filter for admin channels list screen */
add_filter( 'posts_join' , 'cutv_channel_search_join' );
function cutv_channel_search_join( $join ) {
	global $pagenow , $wpdb;
	// I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni"
	if( is_admin() && $pagenow == 'edit.php' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == CUTV_CHANNEL_TYPE && isset( $_GET[ 's' ] ) && $_GET[ 's' ] != '' ) {
		$join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
	}

	return $join;
}

add_filter( 'posts_where' , 'cutv_channel_search_where' );
function cutv_channel_search_where( $where ) {
	global $pagenow , $wpdb;
	// I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni"
	if( is_admin() && $pagenow == 'edit.php' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == CUTV_CHANNEL_TYPE && isset( $_GET[ 's' ] ) && $_GET[ 's' ] != '' ) {
		$where = preg_replace(
			"/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/" ,
			"(" . $wpdb->posts . ".post_title LIKE $1) OR ( " . $wpdb->postmeta . ".meta_key='cutv_channel_name' AND " . $wpdb->postmeta . ".meta_value LIKE $1)" , $where );
	}

	return $where;
}

/* Hiding channels of Inactive services */
//add_filter('parse_query' , 'cutv_show_only_active_services_channels_old');
function cutv_show_only_active_services_channels_old( $query ) {
	global $pagenow , $cutv_options , $cutv_vs_ids;

	if( isset( $_GET[ 'post_type' ] ) ) {
		$type = $_GET[ 'post_type' ];
	} else {
		$type = "post";
	}

	if( $type != CUTV_CHANNEL_TYPE || ! is_admin() || $pagenow != 'edit.php' ) {
		return $query;
	}

	$query_type = array();
	//d( $cutv_vs_ids );
	$query_service = array(
		'key'     => 'cutv_channel_service' ,
		'value'   => $cutv_vs_ids[ 'ids' ] ,
		'compare' => 'IN' ,
	);

	$query->query_vars[ 'meta_query' ] = array(
		'relation' => 'AND' ,
		$query_service ,
		$query_type ,
	);

	return $query;
}
