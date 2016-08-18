<?php
	
	global $wpvr_addons , $wpvr_dynamics;
	global $wpvr_vw_options;
	
	// Constants
	define( 'WPVR_VW_MAIN_FILE' , __FILE__ );
	define( 'WPVR_VW_PATH' , plugin_dir_path( WPVR_VW_MAIN_FILE ) );
	define( 'WPVR_VW_URL' , plugin_dir_url( WPVR_VW_MAIN_FILE ) );
	
	define( 'WPVR_VW_ID' , 'wpvr-vw' );
	define( 'WPVR_VW_SLOT_NAME' , 'wpvr-vw-options' );
	define( 'WPVR_VW_ACTIONS_URL' , WPVR_VW_URL . 'actions.php' );
	
	
	//Defining The Addon Data	
	$addon_infos = array(
		'id'                => WPVR_VW_ID ,
		'slot_name'         => WPVR_VW_SLOT_NAME ,
		'title'             => 'Video Widgets' ,
		'description'       => 'Easily show up your imported videos through widgets.' ,
		'excerpt'           => 'Easily show up your imported videos through widgets.' ,
		'version'           => WPVR_VW_VERSION ,
		'wpvr_version'      => WPVR_VW_MIN_VERSION ,
		'thumbnail_url'     => WPVR_VW_URL . 'wpvr-vw.jpg' ,
		'addon_url'         => 'https://store.wpvideorobot.com/addons/video-widgets/' ,
		'doc'               => 'http://support.wpvideorobot.com/tutorials/video-widgets-addon-tutorial/' ,
		'dashboard_enabled' => FALSE ,
		'options_enabled'   => TRUE ,
		'infos_enabled'     => FALSE ,
		'free_addon'        => TRUE ,
	);
	
	$addon_files = array(
		'infos' => WPVR_VW_PATH . 'includes/infos.php' ,
		'dash'  => WPVR_VW_PATH . 'includes/dash.php' ,
	);
	
	$addon_urls = array(
		'infos' => WPVR_VW_URL . 'includes/infos.php' ,
		'dash'  => WPVR_VW_URL . 'includes/dash.php' ,
	);
	
	
	//Defining Default Options
	$addon_defaults = array(
		'addon_enabled' => TRUE ,
	);

	$addon_options  = array(
		
		'addon_enabled' => array(
			'id'          => 'addon_enabled' ,
			'order'       => 0 ,
			'label'       => sprintf( __( 'Enable %s' , WPVR_VW_ID ) , $addon_infos[ 'title' ] ) ,
			'desc'        => __( 'You can enable or disable the addon from this option.' , WPVR_VW_ID ) ,
			'type'        => 'switch' ,
			'masterOf'    => array() ,
			'masterValue' => '1' ,
		) ,

	);
	
	$wpvr_addons[ WPVR_VW_ID ] = array(
		'infos'    => $addon_infos ,
		'options'  => $addon_options ,
		'defaults' => $addon_defaults ,
		'files'    => $addon_files ,
		'urls'     => $addon_urls ,
	);

	
	/* Throw error if WPVR not installed */
	if( ! defined( 'WPVR_IS_ON' ) ) {
		?>
		<div class = "error">
			<p>
				<b><?php _e( 'WP Video Robot ERROR' , 'wpvr' ); ?></b> : <br/>
				<?php printf( __( 'In order to work properly, <strong>%s</strong> needs WP Video Robot.' , WPVR_VW_ID ) , $addon_infos[ 'title' ] ); ?>
			</p>
		</div>
		<?php
		return FALSE;
	}
	
	
	/* Throw error if WPVR version not updated */
	if( version_compare( WPVR_VERSION , WPVR_VW_MIN_VERSION , '<' ) ) {
		?>
		<div class = "error">
			<p>
				<b><?php _e( 'WP Video Robot ERROR' , 'wpvr' ); ?></b> : <br/>
				<?php printf( __( 'In order to work properly, <strong>%s</strong> needs WP Video Robot version <strong>%s</strong> at least.' , WPVR_VW_ID ) , $addon_infos[ 'title' ] , WPVR_VW_MIN_VERSION ); ?>
			</p>
		</div>
		<?php
		return FALSE;
	}
	
	/* DEFINE ADDON MENU ITEMS */
	add_action( 'admin_menu' , 'wpvr_vw_admin_actions' );
	function wpvr_vw_admin_actions() {
		add_submenu_page(
			'wpvr-addons' ,
			__( 'Video Widgets | WP video Robot' , WPVR_VW_ID ) ,
			' - Video Widgets' ,
			'read' ,
			WPVR_VW_ID ,
			'wpvr_vw_render'
		);
		
		function wpvr_vw_render() {
			if( ! WPVR_NONADMIN_CAP_MANAGE && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
				wpvr_refuse_access();

				return FALSE;
			}
			global $addon_id;
			$addon_id = WPVR_VW_ID;
			include( WPVR_PATH . 'addons/wpvr.addons.php' );
		}
	}