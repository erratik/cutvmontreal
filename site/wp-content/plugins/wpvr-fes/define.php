<?php
	
	global 
	$wpvr_addons , 
	$wpvr_dynamics , 
	$wpvr_roles,
	$wpvrfes_options;
	
	// Constants
	define( 'WPVRFES_MAIN_FILE' , __FILE__ );
	define( 'WPVRFES_PATH' , plugin_dir_path( WPVRFES_MAIN_FILE ) );
	define( 'WPVRFES_URL' , plugin_dir_url( WPVRFES_MAIN_FILE ) );
	define( 'WPVRFES_ID' , 'wpvr-fes' );
	define( 'WPVRFES_SLOT_NAME' , 'wpvr-fes-options' );
	define( 'WPVRFES_ACTIONS_URL' , WPVRFES_URL . 'inc/actions.php' );
	define( 'WPVRFES_ALLOW_UNCATEGORIZED' , WPVRFES_URL . 'inc/actions.php' );
	

	//Defining The Addon Data	
	$addon_infos = array(
		'id'                => WPVRFES_ID ,
		'slot_name'         => WPVRFES_SLOT_NAME ,
		'title'             => 'Frontend Submissions' ,
		'description'       => 'Allow users to post their own videos from your site frontend.' ,
		'excerpt'           => 'Allow users to post their own videos from your site frontend.' ,
		'version'           => WPVRFES_VERSION ,
		'wpvr_version'      => WPVRFES_MIN_VERSION ,
		'thumbnail_url'     => WPVRFES_URL . 'wpvr-fes.jpg' ,
		'addon_url'         => 'https://store.wpvideorobot.com/addons/front-end-submission/' ,
		'doc'               => 'http://support.wpvideorobot.com/tutorials/front-end-submissions-addon-tutorial/' ,
		'dashboard_enabled' => FALSE ,
		'options_enabled'   => TRUE ,
		'infos_enabled'     => FALSE ,
		'free_addon'        => FALSE ,
		'other_tabs'        => array(
			'fes_messages' => array(
				'id'      => 'fes_messages' ,
				'label'   => 'Custom Messages' ,
				'icon'    => 'fa-comment' ,
				'content' => 'options' ,
			) ,
			'fes_services' => array(
				'id'      => 'fes_services' ,
				'label'   => 'Video Services' ,
				'icon'    => 'fa-youtube-play' ,
				'content' => 'options' ,
			) ,
		) ,
	);
	
	$addon_files = array(
		'infos' => WPVRFES_PATH . 'includes/infos.php' ,
		'dash'  => WPVRFES_PATH . 'includes/dash.php' ,
	);
	
	$addon_urls = array(
		'infos' => WPVRFES_URL . 'includes/infos.php' ,
		'dash'  => WPVRFES_URL . 'includes/dash.php' ,
	);
	
	
	//Defining Default Options
	$addon_defaults = array(
		'addon_enabled'       => TRUE ,
		'posting_cats'        => '' ,
		'allowed_cats'        => '' ,
		'posting_author'      => '' ,
		'posting_users'       => 'all' ,
		'posting_roles'       => '' ,
		'auto_publish'        => FALSE ,
		'enable_categories'   => FALSE ,
		
		'enable_captcha'      => FALSE ,
		'skip_duplicates'     => TRUE ,
		
		'closed_message'      => 'Frontend Submissions are closed.' ,
		'submitted_message'   => 'Thank You.' ,
		'logged_only_message' => 'You must be logged in to post videos.<br/><br/>[wpvr_fes_login_form]' ,
		'bad_role_message'    => "Your user role does not allow you to post videos." ,
	);
	
	/* Throw error if WPVR not installed */
	if( ! defined( 'WPVR_IS_ON' ) ) {
		?>
		<div class = "error">
			<p>
				<b><?php _e( 'WP Video Robot ERROR' , 'wpvr' ); ?></b> : <br/>
				<?php printf( __( 'In order to work properly, <strong>%s</strong> needs WP Video Robot.' , WPVRFES_ID ) , $addon_infos[ 'title' ] ); ?>
			</p>
		</div>
		<?php
		return FALSE;
	}
	
	
	/* Throw error if WPVR version not updated */
	if( version_compare( WPVR_VERSION , WPVRFES_MIN_VERSION , '<' ) ) {
		?>
		<div class = "error">
			<p>
				<b><?php _e( 'WP Video Robot ERROR' , 'wpvr' ); ?></b> : <br/>
				<?php printf( __( 'In order to work properly, <strong>%s</strong> needs WP Video Robot version <strong>%s</strong> at least.' , WPVRFES_ID ) , $addon_infos[ 'title' ] , WPVRFES_MIN_VERSION ); ?>
			</p>
		</div>
		<?php
		return FALSE;
	}

	global $wpvr_vs;

	$addon_options = array(
		
		'addon_enabled'       => array(
			'id'          => 'addon_enabled' ,
			'order'       => 0 ,
			'label'       => sprintf( __( 'Enable %s' , WPVRFES_ID ) , $addon_infos[ 'title' ] ) ,
			'desc'        => __( 'You can enable or disable the addon from this option.' , WPVRFES_ID ) .' <br/><br/>'.
				'Submission form shortcode : <strong>[wpvr_fes_form]</strong> <br/>'.
				'Submission form PHP function  : <strong>'.esc_html("<?php wpvrfes_render_form(); ?>").'</strong> <br/>' ,
			'type'        => 'switch' ,
			'masterOf'    => array(
				'posting_cats' ,
				'allowed_cats' ,
				'posting_author' ,
				'auto_publish' ,
				'closed_message' ,
				'submitted_message' ,
				'skip_duplicates' ,
				'enable_captcha' ,
				'posting_users' ,
				'posting_roles' ,
				'logged_only_message' ,
				'bad_role_message' ,
				'enable_categories' ,
			) ,

			'tabMasterOf' => array(
				'fes_services' ,
				'fes_messages' ,
			) ,

			'masterValue' => '1' ,
		) ,
		
		'posting_cats'        => array(
			'id'          => 'posting_cats' ,
			'order'       => 3 ,
			'label'       => __( 'Default Posting Categories' , WPVRFES_ID ) ,
			'maxItems'    => '255' ,
			'placeholder' => __( 'Pick one or more categories' , WPVRFES_ID ) ,
			'values'      => array() ,
			'source'      => 'all_categories' ,
			'desc'        => __( 'Select which categories submitted videos should be posted to by default.' , WPVRFES_ID ) ,
			'type'        => 'multiselect' ,

		) ,
		
		'allowed_cats'        => array(
			'id'          => 'allowed_cats' ,
			'order'       => 2 ,
			'label'       => __( 'Allowed Posting Categories' , WPVRFES_ID ) ,
			'maxItems'    => '255' ,
			'placeholder' => __( 'Pick one or more categories' , WPVRFES_ID ) ,
			'values'      => array() ,
			'source'      => 'all_categories' ,
			'desc'        => __( 'Select which categories can be chosen on frontend by submitters.' , WPVRFES_ID ) ,
			'type'        => 'multiselect' ,

		) ,
		
		
		'posting_users'       => array(
			'id'     => 'posting_users' ,
			'order'  => 10 ,
			'label'  => __( 'Restrict posting to logged users' , WPVRFES_ID ) ,
			'desc'   => __( 'Choose whether to restrict frontend posting to logged users only or to guests also.' , WPVRFES_ID ) ,
			'type'   => 'select' ,
			'values' => array(
				'all'    => 'All Users' ,
				'logged' => 'Logged Users Only' ,
			) ,
		) ,
		
		'posting_author'      => array(
			'id'     => 'posting_author' ,
			'order'  => 11 ,
			'label'  => __( 'Author to assign guests posts to' , WPVRFES_ID ) ,
			'desc'   => __( 'Select which author guests submitted videos should be assigned to.' , WPVRFES_ID ) ,
			'type'   => 'select' ,
			'values' => wpvr_get_authors( TRUE ) ,
		) ,
		
		'posting_roles'       => array(
			'id'          => 'posting_roles' ,
			'order'       => 12 ,
			'label'       => __( 'Restrict Posting to Some User Roles' , WPVRFES_ID ) ,
			'desc'        => __( 'Choose whether to restrict frontend posting to some user roles.' , WPVRFES_ID ) .
				'<br/>' . __( 'Leave empty to accept all roles.' , WPVRFES_ID ) ,
			'type'        => 'multiselect' ,
			'maxItems'    => '255' ,
			'placeholder' => __( 'Pick one or more user roles.' , WPVRFES_ID ) ,
			'values'      => $wpvr_roles[ 'available' ] ,
		) ,
		
		'auto_publish'        => array(
			'id'    => 'auto_publish' ,
			'order' => 1 ,
			'label' => __( 'AutoPublish Submitted Videos' , WPVRFES_ID ) ,
			'desc'  => __( "Choose whether to auto publish submitted videos or set post them as drafts." , WPVRFES_ID ) ,
			'type'  => 'switch' ,
		) ,
		
		'enable_categories'   => array(
			'id'    => 'enable_categories' ,
			'order' => 1 ,
			'label' => __( 'Enable Posting Categories on Frontend' , WPVRFES_ID ) ,
			'desc'  => __( "Choose whether to give the user to choose which categories he will submit videos to." , WPVRFES_ID ) ,
			'type'  => 'switch' ,
		) ,
		
		'skip_duplicates'     => array(
			'id'    => 'skip_duplicates' ,
			'order' => 2 ,
			'label' => __( 'Skip Duplicates' , WPVRFES_ID ) ,
			'desc'  => __( "Choose whether to refuse submission of already imported videos." , WPVRFES_ID ) ,
			'type'  => 'switch' ,
		) ,
		
		'enable_captcha'      => array(
			'id'    => 'enable_captcha' ,
			'order' => 4 ,
			'label' => __( 'Enable Captcha' , WPVRFES_ID ) ,
			'desc'  => __( "Choose whether to enable or not the captcha for real human verification on front end form." , WPVRFES_ID ) ,
			'type'  => 'switch' ,
		) ,
		
		
		'closed_message'      => array(
			'id'    => 'closed_message' ,
			'order' => 100 ,
			'tab'   => 'fes_messages' ,
			'label' => __( 'Closed Submissions Message' , WPVRFES_ID ) ,
			'desc'  => __( "Enter a message to show when frontend submissions are closed." , WPVRFES_ID ) ,
			'type'  => 'texteditor' ,
		) ,
		
		'submitted_message'   => array(
			'id'    => 'submitted_message' ,
			'order' => 101 ,
			'tab'   => 'fes_messages' ,
			'label' => __( 'Submit Success Message' , WPVRFES_ID ) ,
			'desc'  => __( "Enter a message to show when a video has been successfully submitted." , WPVRFES_ID ) ,
			'type'  => 'texteditor' ,
		) ,
		
		'logged_only_message' => array(
			'id'    => 'logged_only_message' ,
			'order' => 102 ,
			'tab'   => 'fes_messages' ,
			'label' => __( 'Login Restriction Message' , WPVRFES_ID ) ,
			'desc'  => __( "Enter a message to show when a guest should log in to post videos." , WPVRFES_ID ) ,
			'type'  => 'texteditor' ,
		) ,
		'bad_role_message'    => array(
			'id'    => 'bad_role_message' ,
			'order' => 103 ,
			'tab'   => 'fes_messages' ,
			'label' => __( 'Role Restriction Messsage' , WPVRFES_ID ) ,
			'desc'  => __( "Enter a message to show when a user does not have a sufficient role to post videos." , WPVRFES_ID ) ,
			'type'  => 'texteditor' ,
		) ,
	);

	//d( $wpvr_vs );

	foreach( $wpvr_vs as $vs ) {
		$addon_options[ 'addon_enabled' ][ 'masterOf' ][] = $vs[ 'id' ];

		$option_id = 'enable' . '_' . $vs[ 'id' ];

		$addon_defaults[ $option_id ] = FALSE;

		$addon_options[ $option_id ] = array(
			'id'    => $option_id ,
			'order' => 1 ,
			'label' => ucfirst( $vs[ 'label' ] ) ,
			//'label' => __( 'Enable' , WPVRFES_ID ) . ' ' . ucfirst( $vs[ 'label' ] ) ,
			'desc'  => __( "Choose whether to enable or disable this video service." , WPVRFES_ID ) ,
			'type'  => 'switch' ,
			'tab' => 'fes_services',
		);
	}

	
	$wpvr_addons[ WPVRFES_ID ] = array(
		'infos'    => $addon_infos ,
		'options'  => $addon_options ,
		'defaults' => $addon_defaults ,
		'files'    => $addon_files ,
		'urls'     => $addon_urls ,
	);

	
	/* DEFINE ADDON MENU ITEMS */
	add_action( 'admin_menu' , 'wpvrfes_admin_actions' );
	function wpvrfes_admin_actions() {
		add_submenu_page(
			'wpvr-addons' ,
			__( 'Frontend Submissions | WP Video Robot' , WPVRFES_ID ) ,
			' - Frontend Submiss.' ,
			'read' ,
			WPVRFES_ID ,
			'wpvrfes_render'
		);
		
		function wpvrfes_render() {
			if( ! WPVR_NONADMIN_CAP_MANAGE && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
				wpvr_refuse_access();
				return FALSE;
			}
			global $addon_id;
			$addon_id = WPVRFES_ID;
			
			include( WPVR_PATH . 'addons/wpvr.addons.php' );
		}
	}
	
	
	