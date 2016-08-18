<?php
	
	/* ADd Plugins Page WPVR menu */
	add_filter( 'plugin_action_links_' . plugin_basename(WPVR_MAIN_FILE), 'wpvr_add_wpvr_links_to_plugins_page' );
	function wpvr_add_wpvr_links_to_plugins_page( $links ) {
		$links[] = '<br/>';
		$links[] = '<a href="'. esc_url( admin_url( 'admin.php?page=wpvr-welcome' ) ).'" class="wpvr_first_actions_link" >'.__('Welcome',WPVR_LANG).'</a>';
		$links[] = '<a href="'. esc_url( admin_url( 'admin.php?page=wpvr' ) ).'">'.__('Dashboard',WPVR_LANG).'</a>';
		$links[] = '<a href="'. esc_url( admin_url( 'admin.php?page=wpvr-options' ) ).'">'.__('Options',WPVR_LANG).'</a>';
		$links[] = '<a href="'. esc_url( admin_url( 'admin.php?page=wpvr-licences' ) ).'">'.__('Licenses',WPVR_LANG).'</a>';
		return $links;
	}
	
	
	/* Define WPVR menu items */
	add_action( 'admin_menu' , 'wpvr_admin_actions' );
	function wpvr_admin_actions() {
		$can_show_menu_links = wpvr_can_show_menu_links();
		if( $can_show_menu_links === TRUE ) {
			
			add_menu_page(
				WPVR_LANG ,
				'WP Video Robot' ,
				'read' ,
				WPVR_LANG ,
				'wpvr_action_render' ,
				WPVR_URL . "assets/images/wpadmin.icon.png"
			//'dashicons-lightbulb'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'WELCOME | WP video Robot' , WPVR_LANG ) ,
				__( 'Welcome' , WPVR_LANG ) ,
				'read' ,
				'wpvr-welcome' ,
				'wpvr_welcome_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'VIDEOS | WP video Robot' , WPVR_LANG ) ,
				__( 'Manage Videos' , WPVR_LANG ) ,
				'read' ,
				'wpvr-manage' ,
				'wpvr_manage_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'OPTIONS | WP video Robot' , WPVR_LANG ) ,
				__( 'Manage Options' , WPVR_LANG ) ,
				'read' ,
				'wpvr-options' ,
				'wpvr_options_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'LOG | WP video Robot' , WPVR_LANG ) ,
				__( 'Activity Logs' , WPVR_LANG ) ,
				'read' ,
				'wpvr-log' ,
				'wpvr_log_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'DEFERRED VIDEOS | WP video Robot' , WPVR_LANG ) ,
				__( 'Deferred Videos' , WPVR_LANG ) ,
				'read' ,
				'wpvr-deferred' ,
				'wpvr_deferred_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'UNWANTED VIDEOS | WP Video Robot' , WPVR_LANG ) ,
				__( 'Unwanted Videos' , WPVR_LANG ) ,
				'read' ,
				'wpvr-unwanted' ,
				'wpvr_unwanted_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'Import | WP video Robot' , WPVR_LANG ) ,
				__( 'Import Panel' , WPVR_LANG ) ,
				'read' ,
				'wpvr-import' ,
				'wpvr_import_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'Licences | WP video Robot' , WPVR_LANG ) ,
				__( 'Manage Licences' , WPVR_LANG ) ,
				'read' ,
				'wpvr-licences' ,
				'wpvr_licences_render'
			);
			
			if( WPVR_DEV_MODE === TRUE || WPVR_ENABLE_SANDBOX === TRUE ) {
				add_submenu_page(
					WPVR_LANG ,
					__( 'Sandbox | WP Video Robot' , WPVR_LANG ) ,
					__( 'Sandbox' , WPVR_LANG ) ,
					'read' ,
					'wpvr-sandbox' ,
					'wpvr_sandbox_render'
				);
			}
			
			/* Removing Main WPVR Menu Item */
			global $menu;
			global $submenu;
			$submenu[ WPVR_LANG ][ 0 ][ 0 ] = __( 'Plugin Dashboard' , WPVR_LANG );
			//remove_submenu_page( WPVR_LANG , WPVR_LANG );
		}
	}
	
	/* Add Menu of Addons */
	add_action( 'admin_menu' , 'wpvr_addons_admin_actions' );
	function wpvr_addons_admin_actions() {
		if( WPVR_ENABLE_ADDONS === TRUE ) {
			
			$can_show_menu_links = wpvr_can_show_menu_links();
			if( $can_show_menu_links === TRUE ) {
				add_menu_page(
					'WPVRM' ,
					'WPVR Addons' ,
					'read' ,
					'wpvr-addons' ,
					'wpvr_addons_render' ,
					WPVR_URL . "assets/images/wpadmin.icon.png"
				);
				add_submenu_page(
					'wpvr-addons' ,
					__( 'ADDONS | WP video Robot' , WPVR_LANG ) ,
					__( 'Browse Addons' , WPVR_LANG ) ,
					'read' ,
					'wpvr-addons' ,
					'wpvr_addons_render'
				);
				
				/* Removing Main WPVR Menu Item */
				global $menu , $submenu , $wpvr_addons;
				//$submenu['wpvr-addons'][0][0] = __('Browse Addons', WPVR_LANG );
			}
		}
	}
	
	/* Add Manage Videos Link To Videos Admin Menu */
	add_action( 'admin_menu' , 'wpvr_add_manage_videos_link' );
	function wpvr_add_manage_videos_link() {
		add_submenu_page(
			'edit.php?post_type=' . WPVR_VIDEO_TYPE ,
			'VIDEOS' ,
			__( 'Manage Videos' , WPVR_LANG ) ,
			'manage_options' ,
			'wpvr_manage_videos' ,
			'wpvr_manage_videos_render'
		);
	}
	
	
	add_filter( 'custom_menu_order' , 'wpvr_reorder_addons_submenu' );
	function wpvr_reorder_addons_submenu( $menu_ord ) {
		global $submenu;
		$a = $b = $c = array();
		if( ! isset( $submenu[ 'wpvr-addons' ] ) ) {
			return $menu_ord;
		}
		
		foreach ( (array) $submenu[ 'wpvr-addons' ] as $link ) {
			if( $link[ 2 ] == 'wpvr-addons' ) {
				$a[] = $link;
			} elseif( strpos( $link[ 0 ] , '+' ) != FALSE ) {
				$a[] = $link;
			} else {
				$b[] = $link;
			}
		}
		$submenu[ 'wpvr-addons' ] = array_merge( $a , $b );
		
		return $menu_ord;
	}
	
	/* Define WPVR menu items */
	add_action( 'admin_bar_menu' , 'wpvr_adminbar_actions' );
	function wpvr_adminbar_actions() {
		$can_show_menu_links = wpvr_can_show_menu_links();
		
		if( $can_show_menu_links === TRUE ) {
			add_menu_page(
				WPVR_LANG ,
				'WP Video Robot' ,
				'read' ,
				WPVR_LANG ,
				'wpvr_action_render' ,
				WPVR_URL . "assets/images/wpadmin.icon.png"
			//'dashicons-lightbulb'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'WELCOME | WP video Robot' , WPVR_LANG ) ,
				__( 'Welcome' , WPVR_LANG ) ,
				'read' ,
				'wpvr-welcome' ,
				'wpvr_welcome_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'VIDEOS | WP video Robot' , WPVR_LANG ) ,
				__( 'Manage Videos' , WPVR_LANG ) ,
				'read' ,
				'wpvr-manage' ,
				'wpvr_manage_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'OPTIONS | WP video Robot' , WPVR_LANG ) ,
				__( 'Manage Options' , WPVR_LANG ) ,
				'read' ,
				'wpvr-options' ,
				'wpvr_options_render'
			);
			add_submenu_page(
				WPVR_LANG ,
				__( 'LOG | WP video Robot' , WPVR_LANG ) ,
				__( 'Activity Logs' , WPVR_LANG ) ,
				'read' ,
				'wpvr-log' ,
				'wpvr_log_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'DEFERRED VIDEOS | WP video Robot' , WPVR_LANG ) ,
				__( 'Deferred Videos' , WPVR_LANG ) ,
				'read' ,
				'wpvr-deferred' ,
				'wpvr_deferred_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'UNWANTED VIDEOS | WP Video Robot' , WPVR_LANG ) ,
				__( 'Unwanted Videos' , WPVR_LANG ) ,
				'read' ,
				'wpvr-unwanted' ,
				'wpvr_unwanted_render'
			);
			
			add_submenu_page(
				WPVR_LANG ,
				__( 'Import | WP video Robot' , WPVR_LANG ) ,
				__( 'Import Panel' , WPVR_LANG ) ,
				'read' ,
				'wpvr-import' ,
				'wpvr_import_render'
			);
			add_submenu_page(
				WPVR_LANG ,
				__( 'Licences | WP video Robot' , WPVR_LANG ) ,
				__( 'Manage Licences' , WPVR_LANG ) ,
				'read' ,
				'wpvr-licences' ,
				'wpvr_licences_render'
			);
			if( WPVR_DEV_MODE === TRUE || WPVR_ENABLE_SANDBOX === TRUE ) {
				add_submenu_page(
					WPVR_LANG ,
					__( 'Sandbox | WP Video Robot' , WPVR_LANG ) ,
					__( 'Sandbox' , WPVR_LANG ) ,
					'read' ,
					'wpvr-sandbox' ,
					'wpvr_sandbox_render'
				);
			}
			
			
			/* Removing Main WPVR Menu Item */
			global $menu;
			global $submenu;
			$submenu[ WPVR_LANG ][ 0 ][ 0 ] = __( 'Plugin Dashboard' , WPVR_LANG );
			//remove_submenu_page( WPVR_LANG , WPVR_LANG );
		}
	}
	
	/* Add Menu of Addons */
	add_action( 'admin_bar_menu' , 'wpvr_addons_adminbar_actions' , 100 );
	function wpvr_addons_adminbar_actions() {
		if( ! WPVR_ENABLE_ADMINBAR_MENU ) return FALSE;
		if( wpvr_can_show_menu_links() ) {
			global $wp_admin_bar;

			// WPVR MAIN TOP BUTTON
			$wp_admin_bar->add_menu( array(
				'id'    => 'wpvr_ab' ,
				'title' => strtoupper( __( 'WP Video Robot' , WPVR_LANG ) ) ,
				'href'  => admin_url( 'admin.php?page=wpvr' ) ,
			) );

			// DASHBOARD TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab' ,
				'id'     => 'wpvr_ab_dashboard' ,
				'title'  => __( 'WPVR DASHBOARD' , WPVR_LANG ) ,
				'href'   => admin_url( 'admin.php?page=wpvr' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_dashboard' ,
				'id'     => 'wpvr_ab_dashboard_content' ,
				'title'  => __( 'Sources & Videos' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr&section=content' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_dashboard' ,
				'id'     => 'wpvr_ab_dashboard_automation' ,
				'title'  => __( 'Automation Dashboard' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr&section=automation' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_dashboard' ,
				'id'     => 'wpvr_ab_dashboard_duplicates' ,
				'title'  => __( 'Track Duplicates' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr&section=duplicates' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_dashboard' ,
				'id'     => 'wpvr_ab_dashboard_datafillers' ,
				'title'  => __( 'DataFillers' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr&section=datafillers' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_dashboard' ,
				'id'     => 'wpvr_ab_dashboard_setters' ,
				'title'  => __( 'Admin Actions' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr&section=setters' ) ,
			) );

			// OPTIONS TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab' ,
				'id'     => 'wpvr_ab_options' ,
				'title'  => __( 'WPVR OPTIONS' , WPVR_LANG ) ,
				'href'   => admin_url( 'admin.php?page=wpvr-options' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_options' ,
				'id'     => 'wpvr_ab_options_general' ,
				'title'  => __( 'General Options' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr-options&section=general' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_options' ,
				'id'     => 'wpvr_ab_options_fetching' ,
				'title'  => __( 'Fetching Options' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr-options&section=fetching' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_options' ,
				'id'     => 'wpvr_ab_options_posting' ,
				'title'  => __( 'Posting Options' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr-options&section=posting' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_options' ,
				'id'     => 'wpvr_ab_options_integration' ,
				'title'  => __( 'Integration Options' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr-options&section=integration' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_options' ,
				'id'     => 'wpvr_ab_options_automation' ,
				'title'  => __( 'Automation Options' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr-options&section=automation' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_options' ,
				'id'     => 'wpvr_ab_options_api_keys' ,
				'title'  => __( 'API Access' , WPVR_LANG ) ,
				'href'   => admin_url(  'admin.php?page=wpvr-options&section=api_keys' ) ,
			) );

			// SOURCES TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab' ,
				'id'     => 'wpvr_ab_sources' ,
				'title'  => strtoupper( __( 'WPVR Sources' , WPVR_LANG ) ) ,
				'href'   => admin_url( 'edit.php?post_type=' . WPVR_SOURCE_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_sources' ,
				'id'     => 'wpvr_ab_sources_all' ,
				'title'  => __( 'All Sources' , WPVR_LANG ) ,
				'href'   => admin_url( 'edit.php?post_type=' . WPVR_SOURCE_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_sources' ,
				'id'     => 'wpvr_ab_sources_new' ,
				'title'  => __( 'New Source' , WPVR_LANG ) ,
				'href'   => admin_url( 'post-new.php?post_type=' . WPVR_SOURCE_TYPE ) ,
			) );

			// VIDEOS TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab' ,
				'id'     => 'wpvr_ab_videos' ,
				'title'  => strtoupper( __( 'WPVR Videos' , WPVR_LANG ) ) ,
				'href'   => admin_url( 'edit.php?post_type=' . WPVR_VIDEO_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_videos' ,
				'id'     => 'wpvr_ab_videos_all' ,
				'title'  => __( 'All Videos' , WPVR_LANG ) ,
				'href'   => admin_url( 'edit.php?post_type=' . WPVR_VIDEO_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_videos' ,
				'id'     => 'wpvr_ab_videos_new' ,
				'title'  => __( 'New Video' , WPVR_LANG ) ,
				'href'   => admin_url( 'post-new.php?post_type=' . WPVR_VIDEO_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab_videos' ,
				'id'     => 'wpvr_ab_videos_manage' ,
				'title'  => __( 'Manage Videos' , WPVR_LANG ) ,
				'href'   => admin_url( 'admin.php?page=wpvr-manage' ) ,
			) );

			//ADDONS TOP MENU
			if( WPVR_ENABLE_ADDONS === TRUE ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'wpvr_ab' ,
					'id'     => 'wpvr_ab_addons' ,
					'title'  => strtoupper( __( 'WPVR Addons' , WPVR_LANG ) ) ,
					'href'   => admin_url( 'admin.php?page=wpvr-addons' ) ,
				) );
				$wp_admin_bar->add_menu( array(
					'parent' => 'wpvr_ab_addons' ,
					'id'     => 'wpvr_ab_addons_browse' ,
					'title'  => __( 'Browse Addons' , WPVR_LANG ) ,
					'href'   => admin_url( 'admin.php?page=wpvr-addons' ) ,
				) );
				global $wpvr_addons;
				foreach ( (array) $wpvr_addons as $addon ) {
					//d( $addon );
					
					$wp_admin_bar->add_node( array(
						'parent' => 'wpvr_ab_addons' ,
						'id'     => 'adminbar-' . $addon[ 'infos' ][ 'id' ] ,
						'title'  => ' - ' . $addon[ 'infos' ][ 'title' ] ,
						'href'   => admin_url( 'admin.php?page=' . $addon[ 'infos' ][ 'id' ] ) ,
					) );
				}
			}

			// LICENSES TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab' ,
				'id'     => 'wpvr_ab_licenses' ,
				'title'  => __( 'WPVR Licenses' , WPVR_LANG ) ,
				'href'   => admin_url( 'admin.php?page=wpvr-licences' ) ,
			) );

			// ACTIVITY LOGS TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpvr_ab' ,
				'id'     => 'wpvr_ab_logs' ,
				'title'  => __( 'WPVR Activity Logs' , WPVR_LANG ) ,
				'href'   => admin_url( 'admin.php?page=wpvr-log' ) ,
			) );

			// SANDBOX
			if( WPVR_DEV_MODE === TRUE || WPVR_ENABLE_SANDBOX === TRUE ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'wpvr_ab' ,
					'id'     => 'wpvr_ab_sandbox' ,
					'title'  => __( 'Sandbox' , WPVR_LANG ) ,
					'href'   => admin_url( 'admin.php?page=wpvr-sandbox' ) ,
				) );
			}
		}
	}
	
	/* restricting Actions for demo user */
	if( WPVR_IS_DEMO_SITE === TRUE ) {
		add_action( 'admin_init' , 'wpvr_my_remove_menu_pages' );
		function wpvr_my_remove_menu_pages() {
			
			global $user_ID;
			
			if( $user_ID == WPVR_IS_DEMO_USER ) {
				define( 'DISALLOW_FILE_EDIT' , TRUE );
				remove_menu_page( 'plugins.php' );
				remove_menu_page( 'users.php' );
				remove_menu_page( 'tools.php' );
			}
		}
	}
	
	
	/* Rendering Options */
	function wpvr_manage_render() {
		if( ! WPVR_NONADMIN_CAP_MANAGE && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.manage.php' );
	}
	
	
	/* Rendering Addons */
	function wpvr_welcome_render() {
		if( ! WPVR_NONADMIN_CAP_MANAGE && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.welcome.php' );
	}
	
	/* Rendering Addons */
	function wpvr_addons_render() {
		if( ! WPVR_NONADMIN_CAP_MANAGE && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		//global $addon_id;
		//$addon_id = 'wpvrm';
		include( WPVR_PATH . 'addons/wpvr.addons.php' );
	}
	
	/* Rendering Licences */
	function wpvr_licences_render() {
		if( ! WPVR_NONADMIN_CAP_MANAGE && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.licences.php' );
	}
	
	
	/* Rendering Options */
	function wpvr_options_render() {
		if( ! WPVR_NONADMIN_CAP_OPTIONS && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.options.php' );
	}
	
	/* Rendering Logs */
	function wpvr_log_render() {
		if( ! WPVR_NONADMIN_CAP_LOGS && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.log.php' );
	}
	
	/* Rendering Deferred */
	function wpvr_deferred_render() {
		if( ! WPVR_NONADMIN_CAP_DEFERRED && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.deferred.php' );
	}
	
	/* Rendering Deferred */
	function wpvr_unwanted_render() {
		if( ! WPVR_NONADMIN_CAP_DEFERRED && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.unwanted.php' );
	}
	
	/* Rendering Actions */
	function wpvr_action_render() {
		if( ! WPVR_NONADMIN_CAP_ACTIONS && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		global $wpvr_pages;
		$wpvr_pages = TRUE;
		include( WPVR_PATH . 'includes/wpvr.actions.php' );
	}
	
	/* Rendering Import */
	function wpvr_import_render() {
		if( ! WPVR_NONADMIN_CAP_IMPORT && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.import.php' );
	}
	
	function wpvr_manage_videos_render() {
		if( ! WPVR_NONADMIN_CAP_IMPORT && ! current_user_can( WPVR_USER_CAPABILITY ) ) {
			wpvr_refuse_access();
			
			return FALSE;
		}
		include( WPVR_PATH . 'includes/wpvr.manage.php' );
	}
	
	function wpvr_sandbox_render() {
		echo "<h2>WP VIDEO ROBOT SANDBOX</h2><br/><br/>";
		include( WPVR_PATH . 'wpvr.sandbox.php' );
	}