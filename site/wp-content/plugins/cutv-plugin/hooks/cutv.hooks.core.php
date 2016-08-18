<?php
	
	/* Plugin Init Action Hook */
	add_action( 'init' , 'cutv__init' );
	function cutv__init() {
		/*starting a PHP session if not already started */
		if( ! session_id() ) @session_start();
//		cutv__mysql_install();
		add_image_size( 'cutv__hard_thumb' , 200 , 150 , TRUE ); // Hard Crop Mode
		add_image_size( 'cutv__soft_thumb' , 200 , 150 ); // Soft Crop Mode
//		cutv__capi_init();
	}
	
	add_action( 'plugins_loaded' , 'cutv__load_addons_activation_hooks' , 5 );
	function cutv__load_addons_activation_hooks() {
		$x           = explode( 'cutv_' , cutv__MAIN_FILE );
		$plugins_dir = $x[ 0 ];
//		$addons_obj = cutv__get_addons( array() , FALSE );
//		if( isset( $addons_obj[ 'items' ] ) && count( $addons_obj[ 'items' ] ) != 0 ) {
//			foreach ( $addons_obj[ 'items' ] as $addon ) {
//				$addon_main_file = $plugins_dir . str_replace( '/' , "\\" , $addon->plugin_dir );
//				register_activation_hook(
//					$addon_main_file ,
//					function () use ( $addon ) {
//						cutv__start_plugin( $addon->id , $addon->version , FALSE );
//					}
//				);
//			}
//		}
	}
	
	/* Loading cutv_ translation files */
	add_action( 'plugins_loaded' , 'cutv__load_textdomain' );
	function cutv__load_textdomain() {
		load_plugin_textdomain( cutv__LANG , FALSE , dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );
	}
	
	/* Loading the cutv_ Superwrap HEADER*/
	add_action( 'load-edit.php' , 'cutv__add_slug_edit_screen_header' , - 1 );
	function cutv__add_slug_edit_screen_header() {
		if( cutv__SMOOTH_SCREEN_ENABLED === TRUE ) {
			$screen = get_current_screen();
			if( $screen->id == 'edit-' . cutv__SOURCE_TYPE || $screen->id == 'edit-' . cutv__VIDEO_TYPE ) {
				?><div class = "cutv__super_wrap" style = " transition:visibility 1s ease-in-out;visibility:hidden;"><!-- SUPER_WRAP --><?php
			}
		}
	}
	
	/* Loading the cutv_ Superwrap FOOTER */
	add_action( 'admin_footer' , 'cutv__add_slug_edit_screen_footer' , 999999999999 );
	function cutv__add_slug_edit_screen_footer() {
		if( cutv__SMOOTH_SCREEN_ENABLED === TRUE ) {
			$screen = get_current_screen();
			if( $screen->id == 'edit-' . cutv__SOURCE_TYPE || $screen->id == 'edit-' . cutv__VIDEO_TYPE ) {
				?><!-- SUPER_WRAP --><?php
			}
		}
	}
	
	/*Fix For pagination Category 1/2 */
	add_filter( 'request' , 'cutv__remove_page_from_query_string' );
	function cutv__remove_page_from_query_string( $query_string ) {
		if( isset( $query_string[ 'name' ] ) && $query_string[ 'name' ] == 'page' && isset( $query_string[ 'page' ] ) ) {
			unset( $query_string[ 'name' ] );
			// 'page' in the query_string looks like '/2', so i'm spliting it out
			list( $delim , $page_index ) = split( '/' , $query_string[ 'page' ] );
			$query_string[ 'paged' ] = $page_index;
		}
		
		return $query_string;
	}
	
	/*Fix For pagination Category 2/2 */
	add_filter( 'request' , 'cutv__fix_category_pagination' );
	function cutv__fix_category_pagination( $qs ) {
		if( isset( $qs[ 'category_name' ] ) && isset( $qs[ 'paged' ] ) ) {
			$qs[ 'post_type' ] = get_post_types( $args = array(
				'public'   => TRUE ,
				'_builtin' => FALSE ,
			) );
			array_push( $qs[ 'post_type' ] , 'post' );
		}
		
		return $qs;
	}
	
	/* Actions to be done on the activation of cutv_ */
	register_activation_hook( cutv__MAIN_FILE , 'cutv__activation' );
	function cutv__activation() {
		
		cutv__reset_on_activation();
		
		cutv__start_plugin( 'cutv_' , cutv__VERSION , FALSE );
		
		if ( ! get_option( 'cutv__flush_rewrite_rules_flag' ) ) {
			add_option( 'cutv__flush_rewrite_rules_flag', TRUE );
		}
		
		wp_schedule_event( time() , 'hourly' , 'cutv__hourly_event' );
		cutv__save_errors( ob_get_contents() );
		//cutv__set_debug( ob_get_contents() , TRUE );
		flush_rewrite_rules();
		
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	
	/* Actions to be done on the DEactivation of cutv_ */
	register_deactivation_hook( cutv__MAIN_FILE , 'cutv__deactivation' );
	function cutv__deactivation() {
		wp_clear_scheduled_hook( 'cutv__hourly_event' );
		//flush_rewrite_rules();
		cutv__save_errors( ob_get_contents() );
		//cutv__set_debug( ob_get_contents() , TRUE );
	}
	
	register_deactivation_hook( cutv__MAIN_FILE , 'flush_rewrite_rules' );
	
	/* Set Autoupdate Hook */
	//add_action( 'init' , 'cutv__activate_autoupdate' , 100 );
//	function cutv__activate_autoupdate() {
//		global $cutv__addons;
//
//		//Check cutv_ updates
//		if( cutv__CHECK_PLUGIN_UPDATES ) {
//			new cutv__autoupdate_product (
//				cutv__VERSION , // Current Version of the product (ex 1.7.0)
//				cutv__SLUG , // Product Plugin Slug (ex cutv_/cutv_.php')
//				FALSE // Update zip url ? (ex TRUE or FALSE ),
//			);
//		}
//
//		//Check for active addons updates
//		if( cutv__CHECK_ADDONS_UPDATES ) {
//			$addons_obj = cutv__get_addons( array() , FALSE );
//			//d( $cutv__addons );
//			if( !is_multisite() ){
//				if( isset( $addons_obj[ 'items' ] ) && count( $addons_obj[ 'items' ] ) != 0 ) {
//					foreach ( $addons_obj[ 'items' ] as $addon ) {
//						//continue;
//						if( ! isset( $cutv__addons[ $addon->id ] ) ) {
//							continue;
//						}
//						if( ! is_plugin_active( $addon->plugin_dir ) ) {
//							continue;
//						}
//						$local_version = $cutv__addons[ $addon->id ][ 'infos' ][ 'version' ];
//						//d( $local_version );
//						//d( $addon->id );
//						new cutv__autoupdate_product (
//							$local_version , // Current Version of the product (ex 1.7.0)
//							$addon->plugin_dir , // Product Plugin Slug (ex cutv_/cutv_.php')
//							FALSE // Update zip url ? (ex TRUE or FALSE ),
//						);
//
//					}
//				}
//			}else{
//				if( isset( $addons_obj[ 'items' ] ) && count( $addons_obj[ 'items' ] ) != 0 ) {
//					foreach ( $addons_obj[ 'items' ] as $addon ) {
//						if( ! isset( $cutv__addons[ $addon->id ] ) ) {
//							continue;
//						}
//
//						//d( $addon->id );
//						//d( is_plugin_active_for_network( $addon->plugin_dir ));
//
//						if( ! is_plugin_active_for_network( $addon->plugin_dir ) ) {
//							continue;
//						}
//
//
//						$local_version = $cutv__addons[ $addon->id ][ 'infos' ][ 'version' ];
//						//d( $local_version );
//						//d( $addon->id );
//						new cutv__autoupdate_product (
//							$local_version , // Current Version of the product (ex 1.7.0)
//							$addon->plugin_dir , // Product Plugin Slug (ex cutv_/cutv_.php')
//							FALSE // Update zip url ? (ex TRUE or FALSE ),
//						);
//
//						//d( $addon );
//						//$plugin = explode('/' , $addon->plugin_dir );
//						//$plugin_data = get_plugin_data( $plugin[1] , $markup = true, $translate = true );
//						//d( $plugin_data );
//
//
//
//
//
//					}
//				}
//			}
//		}
//
//	}
//
	/* Activation */
	//add_action( 'admin_footer' , 'cutv__check_customer' );
	//add_action( 'admin_footer' , 'cutv__check_customer' );

	/* Add query video custom post types on pre get posts action */
	add_filter( 'pre_get_posts' , 'cutv__include_custom_post_type_queries' , 1000 , 1 );
	function cutv__include_custom_post_type_queries( $query ) {
		global $cutv__options , $cutv__private_cpt;
		$getOut = FALSE;
		
		//d( DOING_AJAX );
		if( $query->is_page() ) {
			return $query;
		}
		
		if( ! defined( 'DOING_AJAX' ) || DOING_AJAX === FALSE ) {
			if( is_admin() ) {
				return $query;
			}
		}
		
		$cutv__private_query_vars = array(
			'product_cat' ,
			'download_artist' ,
			'download_tag' ,
			'download_category' ,
		);
		$cutv__private_query_vars = apply_filters( 'cutv__extend_private_query_vars' , $cutv__private_query_vars );
		
		foreach ( $query->query_vars as $key => $val ) {
			if( in_array( $key , $cutv__private_query_vars ) ) {
				return $query;
			}
		}
		
		if( $cutv__options[ 'privateCPT' ] == null ) {
			$cutv__private_cpt = array();
		} else {
			$cutv__private_cpt = $cutv__options[ 'privateCPT' ];
		}
		$cutv__private_cpt = apply_filters( 'cutv__extend_private_cpt' , $cutv__private_cpt );
		
		
		//_d( $query->get( 'post_type' ) );
		
		//This line is Bugging with TrueMag Theme
		//if( isset($query->query_vars['suppress_filters']) && $query->query_vars['suppress_filters'] ) return $query;
		
		
		//echo "#IAM OUT";
		//_d( $cutv__private_cpt );
		if( $cutv__options[ 'addVideoType' ] === TRUE ) {
			
			$supported = $query->get( 'post_type' );
			//_d( $supported );
			//new dBug( $cutv__options['privateCPT'] );
			//new dBug( $cutv__private_cpt );
			if( is_array( $supported ) ) {
				foreach ( $supported as $s ) {
					if( in_array( $s , $cutv__private_cpt ) ) {
						$getOut = TRUE;
					}
				}
			} else {
				$getOut = in_array( $supported , $cutv__private_cpt );
			}
			
			if( $getOut === TRUE ) {
				return $query;
			} elseif( $supported == 'post' || $supported == '' ) {
				$supported = array( 'post' , cutv__VIDEO_TYPE );
			} elseif( is_array( $supported ) ) {
				array_push( $supported , cutv__VIDEO_TYPE );
			} elseif( is_string( $supported ) ) {
				$supported = array( $supported , cutv__VIDEO_TYPE );
			}
			//echo "newSuported = ";new dBug( $supported );
			
			$query->set( 'post_type' , $supported );
			
			return $query;
		}
	}