<?php
	
	/* ADd Plugins Page cutv_ menu */
	add_filter( 'plugin_action_links_' . plugin_basename(cutv_MAIN_FILE), 'cutv_add_cutv_links_to_plugins_page' );
	function cutv_add_cutv_links_to_plugins_page( $links ) {
		$links[] = '<br/>';
		$links[] = '<a href="'. esc_url( admin_url( 'admin.php?page=cutv_-welcome' ) ).'" class="wpvr_first_actions_link" >'.__('Welcome','en').'</a>';
		$links[] = '<a href="'. esc_url( admin_url( 'admin.php?page=cutv_' ) ).'">'.__('Dashboard','en').'</a>';
		$links[] = '<a href="'. esc_url( admin_url( 'admin.php?page=cutv_-options' ) ).'">'.__('Options','en').'</a>';
		$links[] = '<a href="'. esc_url( admin_url( 'admin.php?page=cutv_-licences' ) ).'">'.__('Licenses','en').'</a>';
		return $links;
	}


	/* Define cutv_ menu items */
	add_action( 'admin_menu' , 'cutv_admin_actions' );
	function cutv_admin_actions() {
//		$can_show_menu_links = cutv_can_show_menu_links();
//		if( $can_show_menu_links === TRUE ) {

			add_menu_page(
				'en' ,
				'CUTV Admin' ,
				'read' ,
				'en' ,
				'cutv_action_render' ,
				CUTV_URL . "assets/images/wpadmin.icon.png"
			//'dashicons-lightbulb'
			);

			add_submenu_page(
				'en' ,
				__( 'WELCOME | CUTV Admin' , 'en' ) ,
				__( 'Welcome' , 'en' ) ,
				'read' ,
				'cutv-welcome' ,
				'cutv_welcome_render'
			);
//
//			add_submenu_page(
//				'en' ,
//				__( 'VIDEOS | CUTV Admin' , 'en' ) ,
//				__( 'Manage Videos' , 'en' ) ,
//				'read' ,
//				'cutv-manage' ,
//				'cutv_manage_render'
//			);
//
//			add_submenu_page(
//				'en' ,
//				__( 'OPTIONS | CUTV Admin' , 'en' ) ,
//				__( 'Manage Options' , 'en' ) ,
//				'read' ,
//				'cutv-options' ,
//				'cutv_options_render'
//			);
//
//			add_submenu_page(
//				'en' ,
//				__( 'LOG | CUTV Admin' , 'en' ) ,
//				__( 'Activity Logs' , 'en' ) ,
//				'read' ,
//				'cutv-log' ,
//				'cutv_log_render'
//			);
//
//			add_submenu_page(
//				'en' ,
//				__( 'DEFERRED VIDEOS | CUTV Admin' , 'en' ) ,
//				__( 'Deferred Videos' , 'en' ) ,
//				'read' ,
//				'cutv-deferred' ,
//				'cutv_deferred_render'
//			);
//
//			add_submenu_page(
//				'en' ,
//				__( 'UNWANTED VIDEOS | CUTV Admin' , 'en' ) ,
//				__( 'Unwanted Videos' , 'en' ) ,
//				'read' ,
//				'cutv-unwanted' ,
//				'cutv_unwanted_render'
//			);
//
//			add_submenu_page(
//				'en' ,
//				__( 'Import | CUTV Admin' , 'en' ) ,
//				__( 'Import Panel' , 'en' ) ,
//				'read' ,
//				'cutv-import' ,
//				'cutv_import_render'
//			);
//
//			add_submenu_page(
//				'en' ,
//				__( 'Licences | CUTV Admin' , 'en' ) ,
//				__( 'Manage Licences' , 'en' ) ,
//				'read' ,
//				'cutv-licences' ,
//				'cutv_licences_render'
//			);
//
//			if( CUTV_DEV_MODE === TRUE || CUTV_ENABLE_SANDBOX === TRUE ) {
//				add_submenu_page(
//					'en' ,
//					__( 'Sandbox | CUTV Admin' , 'en' ) ,
//					__( 'Sandbox' , 'en' ) ,
//					'read' ,
//					'cutv-sandbox' ,
//					'cutv_sandbox_render'
//				);
//			}

			/* Removing Main cutv_ Menu Item */
			global $menu;
			global $submenu;
			$submenu[ 'en' ][ 0 ][ 0 ] = __( 'Plugin Dashboard' , 'en' );
			//remove_submenu_page( 'en' , 'en' );
//		}
	}

	/* Add Menu of Addons */
	add_action( 'admin_menu' , 'cutv_addons_admin_actions' );
	function cutv_addons_admin_actions() {
		if( CUTV_ENABLE_ADDONS === TRUE ) {

			$can_show_menu_links = cutv_can_show_menu_links();
			if( $can_show_menu_links === TRUE ) {
				add_menu_page(
					'cutvM' ,
					'cutv Addons' ,
					'read' ,
					'cutv-addons' ,
					'cutv_addons_render' ,
					CUTV_URL . "assets/images/wpadmin.icon.png"
				);
				add_submenu_page(
					'cutv-addons' ,
					__( 'ADDONS | CUTV Admin' , 'en' ) ,
					__( 'Browse Addons' , 'en' ) ,
					'read' ,
					'cutv-addons' ,
					'cutv_addons_render'
				);

				/* Removing Main cutv_ Menu Item */
				global $menu , $submenu , $cutv_addons;
				//$submenu['cutv-addons'][0][0] = __('Browse Addons', 'en' );
			}
		}
	}

	/* Add Manage Videos Link To Videos Admin Menu */
	add_action( 'admin_menu' , 'cutv_add_manage_videos_link' );
	function cutv_add_manage_videos_link() {
		add_submenu_page(
			'edit.php?post_type=' . CUTV_VIDEO_TYPE ,
			'VIDEOS' ,
			__( 'Manage Videos' , 'en' ) ,
			'manage_options' ,
			'cutv_manage_videos' ,
			'cutv_manage_videos_render'
		);
	}


	add_filter( 'custom_menu_order' , 'cutv_reorder_addons_submenu' );
	function cutv_reorder_addons_submenu( $menu_ord ) {
		global $submenu;
		$a = $b = $c = array();
		if( ! isset( $submenu[ 'cutv-addons' ] ) ) {
			return $menu_ord;
		}

		foreach ( (array) $submenu[ 'cutv-addons' ] as $link ) {
			if( $link[ 2 ] == 'cutv-addons' ) {
				$a[] = $link;
			} elseif( strpos( $link[ 0 ] , '+' ) != FALSE ) {
				$a[] = $link;
			} else {
				$b[] = $link;
			}
		}
		$submenu[ 'cutv-addons' ] = array_merge( $a , $b );

		return $menu_ord;
	}

	/* Define cutv_ menu items */
	add_action( 'admin_bar_menu' , 'cutv_adminbar_actions' );
	function cutv_adminbar_actions() {
//		$can_show_menu_links = cutv_can_show_menu_links();
//
//		if( $can_show_menu_links === TRUE ) {
			add_menu_page(
				'en' ,
				'CUTV Admin' ,
				'read' ,
				'en' ,
				'cutv_action_render' ,
				CUTV_URL . "assets/images/wpadmin.icon.png"
			//'dashicons-lightbulb'
			);

			add_submenu_page(
				'en' ,
				__( 'WELCOME | CUTV Admin' , 'en' ) ,
				__( 'Welcome' , 'en' ) ,
				'read' ,
				'cutv-welcome' ,
				'cutv_welcome_render'
			);




			/* Removing Main cutv_ Menu Item */
			global $menu;
			global $submenu;
			$submenu[ 'en' ][ 0 ][ 0 ] = __( 'Plugin Dashboard' , 'en' );
			//remove_submenu_page( 'en' , 'en' );
//		}
	}

	/* Add Menu of Addons */
	add_action( 'admin_bar_menu' , 'cutv_addons_adminbar_actions' , 100 );
	function cutv_addons_adminbar_actions() {
		if( ! CUTV_ENABLE_ADMINBAR_MENU ) return FALSE;
//		if( CUTV_can_show_menu_links() ) {
			global $wp_admin_bar;

			// cutv_ MAIN TOP BUTTON
			$wp_admin_bar->add_menu( array(
				'id'    => 'cutv_ab' ,
				'title' => strtoupper( __( 'CUTV Admin' , 'en' ) ) ,
				'href'  => admin_url( 'admin.php?page=cutv_' ) ,
			) );

			// DASHBOARD TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab' ,
				'id'     => 'cutv_ab_dashboard' ,
				'title'  => __( 'CUTV DASHBOARD' , 'en' ) ,
				'href'   => admin_url( 'admin.php?page=cutv_' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_dashboard' ,
				'id'     => 'cutv_ab_dashboard_content' ,
				'title'  => __( 'Sources & Videos' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_&section=content' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_dashboard' ,
				'id'     => 'cutv_ab_dashboard_automation' ,
				'title'  => __( 'Automation Dashboard' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_&section=automation' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_dashboard' ,
				'id'     => 'cutv_ab_dashboard_duplicates' ,
				'title'  => __( 'Track Duplicates' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_&section=duplicates' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_dashboard' ,
				'id'     => 'cutv_ab_dashboard_datafillers' ,
				'title'  => __( 'DataFillers' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_&section=datafillers' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_dashboard' ,
				'id'     => 'cutv_ab_dashboard_setters' ,
				'title'  => __( 'Admin Actions' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_&section=setters' ) ,
			) );

			// OPTIONS TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab' ,
				'id'     => 'cutv_ab_options' ,
				'title'  => __( 'cutv OPTIONS' , 'en' ) ,
				'href'   => admin_url( 'admin.php?page=cutv_-options' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_options' ,
				'id'     => 'cutv_ab_options_general' ,
				'title'  => __( 'General Options' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_-options&section=general' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_options' ,
				'id'     => 'cutv_ab_options_fetching' ,
				'title'  => __( 'Fetching Options' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_-options&section=fetching' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_options' ,
				'id'     => 'cutv_ab_options_posting' ,
				'title'  => __( 'Posting Options' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_-options&section=posting' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_options' ,
				'id'     => 'cutv_ab_options_integration' ,
				'title'  => __( 'Integration Options' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_-options&section=integration' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_options' ,
				'id'     => 'cutv_ab_options_automation' ,
				'title'  => __( 'Automation Options' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_-options&section=automation' ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_options' ,
				'id'     => 'cutv_ab_options_api_keys' ,
				'title'  => __( 'API Access' , 'en' ) ,
				'href'   => admin_url(  'admin.php?page=cutv_-options&section=api_keys' ) ,
			) );

			// SOURCES TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab' ,
				'id'     => 'cutv_ab_sources' ,
				'title'  => strtoupper( __( 'cutv Sources' , 'en' ) ) ,
				'href'   => admin_url( 'edit.php?post_type=' . CUTV_SOURCE_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_sources' ,
				'id'     => 'cutv_ab_sources_all' ,
				'title'  => __( 'All Sources' , 'en' ) ,
				'href'   => admin_url( 'edit.php?post_type=' . CUTV_SOURCE_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_sources' ,
				'id'     => 'cutv_ab_sources_new' ,
				'title'  => __( 'New Source' , 'en' ) ,
				'href'   => admin_url( 'post-new.php?post_type=' . CUTV_SOURCE_TYPE ) ,
			) );

			// VIDEOS TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab' ,
				'id'     => 'cutv_ab_videos' ,
				'title'  => strtoupper( __( 'cutv Videos' , 'en' ) ) ,
				'href'   => admin_url( 'edit.php?post_type=' . CUTV_VIDEO_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_videos' ,
				'id'     => 'cutv_ab_videos_all' ,
				'title'  => __( 'All Videos' , 'en' ) ,
				'href'   => admin_url( 'edit.php?post_type=' . CUTV_VIDEO_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_videos' ,
				'id'     => 'cutv_ab_videos_new' ,
				'title'  => __( 'New Video' , 'en' ) ,
				'href'   => admin_url( 'post-new.php?post_type=' . CUTV_VIDEO_TYPE ) ,
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab_videos' ,
				'id'     => 'cutv_ab_videos_manage' ,
				'title'  => __( 'Manage Videos' , 'en' ) ,
				'href'   => admin_url( 'admin.php?page=cutv_-manage' ) ,
			) );

			//ADDONS TOP MENU
			if( CUTV_ENABLE_ADDONS === TRUE ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'cutv_ab' ,
					'id'     => 'cutv_ab_addons' ,
					'title'  => strtoupper( __( 'cutv Addons' , 'en' ) ) ,
					'href'   => admin_url( 'admin.php?page=cutv_-addons' ) ,
				) );
				$wp_admin_bar->add_menu( array(
					'parent' => 'cutv_ab_addons' ,
					'id'     => 'cutv_ab_addons_browse' ,
					'title'  => __( 'Browse Addons' , 'en' ) ,
					'href'   => admin_url( 'admin.php?page=cutv_-addons' ) ,
				) );
				global $cutv_addons;
				foreach ( (array) $cutv_addons as $addon ) {
					//d( $addon );

					$wp_admin_bar->add_node( array(
						'parent' => 'cutv_ab_addons' ,
						'id'     => 'adminbar-' . $addon[ 'infos' ][ 'id' ] ,
						'title'  => ' - ' . $addon[ 'infos' ][ 'title' ] ,
						'href'   => admin_url( 'admin.php?page=' . $addon[ 'infos' ][ 'id' ] ) ,
					) );
				}
			}

			// LICENSES TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab' ,
				'id'     => 'cutv_ab_licenses' ,
				'title'  => __( 'cutv Licenses' , 'en' ) ,
				'href'   => admin_url( 'admin.php?page=cutv_-licences' ) ,
			) );

			// ACTIVITY LOGS TOP MENU
			$wp_admin_bar->add_menu( array(
				'parent' => 'cutv_ab' ,
				'id'     => 'cutv_ab_logs' ,
				'title'  => __( 'cutv Activity Logs' , 'en' ) ,
				'href'   => admin_url( 'admin.php?page=cutv_-log' ) ,
			) );

			// SANDBOX
			if( CUTV_DEV_MODE === TRUE || CUTV_ENABLE_SANDBOX === TRUE ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'cutv_ab' ,
					'id'     => 'cutv_ab_sandbox' ,
					'title'  => __( 'Sandbox' , 'en' ) ,
					'href'   => admin_url( 'admin.php?page=cutv_-sandbox' ) ,
				) );
			}
//		}
	}

	/* restricting Actions for demo user */
	if( CUTV_IS_DEMO_SITE === TRUE ) {
		add_action( 'admin_init' , 'cutv_my_remove_menu_pages' );
		function cutv_my_remove_menu_pages() {

			global $user_ID;

			if( $user_ID == CUTV_IS_DEMO_USER ) {
				define( 'DISALLOW_FILE_EDIT' , TRUE );
				remove_menu_page( 'plugins.php' );
				remove_menu_page( 'users.php' );
				remove_menu_page( 'tools.php' );
			}
		}
	}


	/* Rendering Options */
	function cutv_manage_render() {
		if( ! CUTV_NONADMIN_CAP_MANAGE && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		include( CUTV_PATH . 'includes/cutv.manage.php' );
	}


	/* Rendering Addons */
	function cutv_welcome_render() {
		if( ! CUTV_NONADMIN_CAP_MANAGE && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		include( CUTV_PATH . 'includes/cutv.sources.php' );
	}




	/* Rendering Options */
	function cutv_options_render() {
		if( ! CUTV_NONADMIN_CAP_OPTIONS && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		include( CUTV_PATH . 'includes/cutv.options.php' );
	}

	/* Rendering Logs */
	function cutv_log_render() {
		if( ! CUTV_NONADMIN_CAP_LOGS && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		include( CUTV_PATH . 'includes/cutv.log.php' );
	}

	/* Rendering Deferred */
	function cutv_deferred_render() {
		if( ! CUTV_NONADMIN_CAP_DEFERRED && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		include( CUTV_PATH . 'includes/cutv.deferred.php' );
	}

	/* Rendering Deferred */
	function cutv_unwanted_render() {
		if( ! CUTV_NONADMIN_CAP_DEFERRED && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		include( CUTV_PATH . 'includes/cutv.unwanted.php' );
	}

	/* Rendering Actions */
	function cutv_action_render() {
		if( ! CUTV_NONADMIN_CAP_ACTIONS && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		global $cutv_pages;
		$cutv_pages = TRUE;
		include( CUTV_PATH . 'includes/cutv.actions.php' );
	}

	/* Rendering Import */
	function cutv_import_render() {
		if( ! CUTV_NONADMIN_CAP_IMPORT && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		include( CUTV_PATH . 'includes/cutv.import.php' );
	}

	function cutv_manage_videos_render() {
		if( ! CUTV_NONADMIN_CAP_IMPORT && ! current_user_can( CUTV_USER_CAPABILITY ) ) {
			cutv_refuse_access();

			return FALSE;
		}
		include( CUTV_PATH . 'includes/cutv.manage.php' );
	}

	function cutv_sandbox_render() {
		echo "<h2>CUTV Admin SANDBOX</h2><br/><br/>";
		include( CUTV_PATH . 'cutv.sandbox.php' );
	}