<?php
	
	global $wpvr_addons , $wpvr_dynamics ;
	global $wpvrct_options ;
	
	// Constants
	define('WPVRCT_MAIN_FILE', __FILE__ );
	define('WPVRCT_PATH', plugin_dir_path( WPVRCT_MAIN_FILE ));
	define('WPVRCT_URL', plugin_dir_url( WPVRCT_MAIN_FILE ));		
	define('WPVRCT_ID', 'wpvr-ct' );
	define('WPVRCT_SLOT_NAME', WPVRCT_ID .'-options' );
	define('WPVRCT_ACTIONS_URL', WPVRCT_URL.'actions.php' );
	
	
	//Defining The Addon Data	
	$addon_infos = array(
		'id' 						=> WPVRCT_ID,
		'slot_name' 			=> WPVRCT_SLOT_NAME,
		'title' 					=> 'Custom Taxonomies',
		'description' 			=> 'Use Custom Taxonomies with your Imported Videos.',
		'excerpt' 				=> 'Use Custom Taxonomies with your Imported Videos.',
		'version' 				=> WPVRCT_VERSION ,
		'wpvr_version' 		=> WPVRCT_MIN_VERSION,
		'thumbnail_url' 		=> WPVRCT_URL.'wpvr-ct.jpg',
		'addon_url' 			=> 'https://store.wpvideorobot.com/addons/custom-taxonomies/',
		'dashboard_enabled'	=> false,
		'options_enabled'		=> true,
		'infos_enabled'		=> false,
		'free_addon'			=> true,
	);
	
	$addon_files = array(
		'infos' 		=> WPVRCT_PATH.'includes/infos.php',
		'dash' 		=> WPVRCT_PATH.'includes/dash.php',
	);
	
	$addon_urls = array(
		'infos' 		=> WPVRCT_URL.'includes/infos.php',
		'dash' 		=> WPVRCT_URL.'includes/dash.php',
	);
	
	
	//Defining Default Options
	$addon_defaults = array(
		'addon_enabled' 		=> true ,
		'video_taxonomies' 	=> '' ,
	);
	$addon_options = array(
		
		'addon_enabled' => array(
			'id'		=>	'addon_enabled' ,
			'order'		=>	0 ,
			'label' 	=> sprintf( __('Enable %s', WPVRCT_ID ) , $addon_infos['title'] ) ,
			'desc' 		=> __('You can enable or disable the addon from this option.', WPVRCT_ID ),
			'type' 	=> 'switch',
			'masterOf'		=> array(
				//'syndication_key',
				'video_taxonomies',
			) ,
			'masterValue' 	=> '1',
		),
		
		
		'video_taxonomies' => array(
			'id'		=>	'video_taxonomies' ,
			'order'		=>	2 ,
			'label' => __('Taxonomies to Handle', WPVRCT_ID ),
			'maxItems' 	=> '255',
			'placeholder' 	=> __('Pick one or more taxonomy', WPVRCT_ID ),
			'values' => array(),
			'source' => 'taxonomies',
			'desc' 		=> __('Choose the taxonomies you want the plugin to handle.', WPVRCT_ID ),
			'type' 	=> 'multiselect',
		),
	
		
	
	);
	
	$wpvr_addons[ WPVRCT_ID ] = array(
		'infos' 		=> $addon_infos ,
		'options' 		=> $addon_options ,
		'defaults' 		=> $addon_defaults ,
		'files' 		=> $addon_files ,
		'urls' 			=> $addon_urls ,
	);

	
	/* Throw error if WPVR not installed */
	if( !defined('WPVR_IS_ON') ){
		?>
		<div class="error">
			<p>
				<b><?php _e('WP Video Robot ERROR' ); ?></b> : <br/>
				<?php printf( __( 'In order to work properly, <strong>%s</strong> needs WP Video Robot.', WPVRCT_ID ), $addon_infos['title'] ); ?>
			</p>
		</div>
		<?php
		return false;
	}
	
	
	/* Throw error if WPVR version not updated */
	if( version_compare ( WPVR_VERSION , WPVRCT_MIN_VERSION , '<' ) ){
		?>
		<div class="error">
			<p>
				<b><?php _e('WP Video Robot ERROR','wpvr'); ?></b> : <br/>
				<?php printf( __( 'In order to work properly, <strong>%s</strong> needs WP Video Robot version <strong>%s</strong> at least.', WPVRCT_ID ), $addon_infos['title'] , WPVRCT_MIN_VERSION ); ?>
			</p>
		</div>
		<?php
		return false;
	}
	
	/* DEFINE ADDON MENU ITEMS */
	add_action('admin_menu', 'wpvrct_admin_actions');
	function wpvrct_admin_actions() {			
		add_submenu_page(
			'wpvr-addons',
			__('Custom Taxonomies | WP video Robot', WPVRCT_ID ),
			' - Custom Tax.',
			'read',
			WPVRCT_ID,
			'wpvrct_render'			
		);
		
		function wpvrct_render(){
			if( !WPVR_NONADMIN_CAP_MANAGE && !current_user_can( WPVR_USER_CAPABILITY ) ) { 
				wpvr_refuse_access(); return false; 
			}
			global $addon_id ;
			$addon_id = WPVRCT_ID;
			include(WPVR_PATH.'addons/wpvr.addons.php');  
		}
	}
	