<?php
	
	/* Load JS scripts(minified or normal) in admin area only */
	add_action( 'admin_head' , 'cutv__load_scripts' );
	function cutv__load_scripts() {
		
		if( cutv__DEV_MODE === FALSE && cutv__USE_MIN_JS === TRUE ) {
			$js_functions_file = cutv__URL . 'assets/js/cutv_.functions.min.js';
		} else {
			$js_functions_file = cutv__URL . 'assets/js/cutv_.functions.js';
		}
		
		$js_globals_array  = array(
			'functions_js' => $js_functions_file ,
			'api_auth_url' => cutv__AUTH_URL ,
			'cutv__js'      => cutv__JS ,
		
		);
		$js_localize_array = array(
			'confirm_import_sample_sources' => __( 'Do you really want to import demo sources ?' , cutv__LANG ) ,
			'save_source_first'             => __( 'Your source has changed. Please save it before testing it.' , cutv__LANG ) ,
			'save_source'                   => '<i class="fa fa-save"></i> ' . __( 'Save Source' , cutv__LANG ) ,
			'group_info'                    => __( 'Grouped Testing Info' , cutv__LANG ) ,
			'add_to_unwanted'               => __( 'Add to Unwanted' , cutv__LANG ) ,
			'remove_from_unwanted'          => __( 'Remove from Unwanted' , cutv__LANG ) ,
			'license_cancelled'             => __( 'Activation cancelled. You can now use your purchase code on your new domain.' , cutv__LANG ) ,
			'license_reset'                 => __( 'License reset.' , cutv__LANG ) ,
			'activation_cancel_confirm'     => __( 'Do you really want to cancel your activation ?' , cutv__LANG ) ,
			'licence_reset_confirm'         => __( 'Do you really want to reset this addon license ?' , cutv__LANG ) ,
			'action_done'                   => __( 'Action done successfully.' , cutv__LANG ) ,
			'select_preset'                 => __( 'Please select a dataFiller Preset.' , cutv__LANG ) ,
			'correct_entry'                 => __( 'Please enter both Data to Add and the custom field name where to add.' , cutv__LANG ) ,
			'confirm_add_from_preset'       => __( 'Do you really want to add all this preset fillers ?' , cutv__LANG ) ,
			'fillers_deleted'               => __( 'All the data fillers have been deleted successfully.' , cutv__LANG ) ,
			'confirm_delete_fillers'        => __( 'Do you really want to delete all the data fillers ?' , cutv__LANG ) ,
			'confirm_run_sources'           => __( 'Do you really want to run this source ?' , cutv__LANG ) ,
			'confirm_merge_items'           => __( 'Do you really want to merge the selected items ?' , cutv__LANG ) ,
			'confirm_merge_all_items'       => __( 'Do you really want to merge all the duplicates ? This make take some time.' , cutv__LANG ) ,
			'confirm_merge_dups'            => __( 'Do you really want to merge those duplicates ?' , cutv__LANG ) ,
			'is_now_connected'              => __( 'is now connected !' , cutv__LANG ) ,
			'confirm_cancel_access'         => __( 'Do you really want to cancel this access ?' , cutv__LANG ) ,
			'import_videos'                 => __( 'Import Videos' , cutv__LANG ) ,
			'wp_video_robot'                => __( 'WP Video Robot' , cutv__LANG ) ,
			'source_with_no_name'           => __( 'Do you really want to add this source without a name.' , cutv__LANG ) ,
			'source_with_no_type'           => __( 'Please choose a source type to continue.' , cutv__LANG ) ,
			'source_with_big_wanted'        => __( 'Wanted Videos are limited to' , cutv__LANG ) . ' : ' . cutv__MAX_WANTED_VIDEOS ,
			'video_preview'                 => __( 'Video Preview' , cutv__LANG ) ,
			'work_completed'                => __( 'Work Completed !' , cutv__LANG ) ,
			'videos_unanted_successfully'   => __( 'videos added to unwanted successfuly' , cutv__LANG ) ,
			'videos_added_successfully'     => __( 'videos added successfuly' , cutv__LANG ) ,
			'cancel_anyway'                 => ' <i class="fa fa-remove"></i> ' . __( 'Cancel anyway' , cutv__LANG ) ,
			'back_to_work'                  => __( 'Continue the work in progress' , cutv__LANG ) ,
			'reset_yes'                     => ' <i class="fa fa-check"></i> ' . __( 'Confirm Reset' , cutv__LANG ) ,
			'reset_no'                      => ' <i class="fa fa-remove"></i> ' . __( 'Cancel' , cutv__LANG ) ,
			'yes'                           => ' <i class="fa fa-check"></i> ' . __( 'Yes' , cutv__LANG ) ,
			'no'                            => ' <i class="fa fa-remove"></i> ' . __( 'No' , cutv__LANG ) ,
			'import_btn'                    => ' <i class="fa fa-download"></i> ' . __( 'Import' , cutv__LANG ) ,
			'are_you_sure'                  => __( 'Are you sure ?' , cutv__LANG ) ,
			'really_want_cancel'            => __( 'Do you really want to cancel the work in progress ?' , cutv__LANG ) ,
			'continue_button'               => ' <i class="fa fa-play"></i> ' . __( 'Continue' , cutv__LANG ) ,
			'cancel_button'                 => ' <i class="fa fa-remove"></i> ' . __( 'Cancel' , cutv__LANG ) ,
			'ok_button'                     => ' <i class="fa fa-check"></i> ' . __( 'OK' , cutv__LANG ) ,
			'export_button'                 => ' <i class="fa fa-download"></i> ' . __( 'Export' , cutv__LANG ) ,
			'dismiss_button'                => ' <i class="fa fa-close"></i> ' . __( 'DISMISS' , cutv__LANG ) ,
			'close_button'                  => ' <i class="fa fa-close"></i> ' . __( 'Close' , cutv__LANG ) ,
			'pause_button'                  => ' <i class="fa fa-pause"></i> ' . __( 'Pause' , cutv__LANG ) ,
			'options_set_to_default'        => __( 'cutv_ Options set to default !' , cutv__LANG ) ,
			'options_reset_confirm'         => __( 'Do you really want to reset options to default ?' , cutv__LANG ) ,
			'options_saved'                 => __( 'Options successfully saved' , cutv__LANG ) ,
			'addon_options_saved'           => __( 'Addon options successfully saved' , cutv__LANG ) ,
			'licences_saved'                => __( 'Licences successfully saved' , cutv__LANG ) ,
			'options_reset_confirm'         => __( 'Do you really want to reset options to default ?' , cutv__LANG ) ,
			'adding_selected_videos'        => __( ' Adding selected videos' , cutv__LANG ) ,
			'work_in_progress'              => __( 'Work in progress' , cutv__LANG ) ,
			'loading'                       => __( 'Loading' , cutv__LANG ) . ' <i class="wpvr__spinning_icon fa fa-cog fa-spin"></i> ' ,
			'loadingCenter'                 => '<div class="wpvr__loading_center"><br /><br />' . __( 'Please Wait ...' , cutv__LANG )
			                                   . ' <br/><br/><i class="wpvr__spinning_icon fa fa-cog fa-spin"></i></div>' ,
			'please_wait'                   => __( 'Please wait' , cutv__LANG ) ,
			'want_clear_log'                => __( 'Do you really want to clear the log ?' , cutv__LANG ) ,
			'system_infos'                  => __( 'System Informations' , cutv__LANG ) ,
			'item'                          => __( 'item' , cutv__LANG ) ,
			'items'                         => __( 'items' , cutv__LANG ) ,
			'confirm_delete_permanently'    => __( 'Do you really want to delete permanently the selected items ?' , cutv__LANG ) ,
			'want_remove_items'             => __( 'Do you really want to remove permanently the selected items ?' , cutv__LANG ) ,
			'videos_removed_successfully'   => __( 'video(s) removed from deferred' , cutv__LANG ) ,
			'showing'                       => __( 'Showing' , cutv__LANG ) ,
			'on'                            => __( 'on' , cutv__LANG ) ,
			'page'                          => __( 'Page' , cutv__LANG ) ,
			'seconds'                       => __( 'seconds' , cutv__LANG ) ,
			'videos_processed_successfully' => __( 'videos processed successfully' , cutv__LANG ) ,
			'duplicates_removed_in'         => __( 'duplicates removed in' , cutv__LANG ) ,
			'errorJSON'                     => __( 'Headers already sent by some other scripts. Error thrown :' , cutv__LANG ) ,
			'error'                         => __( 'Error' , cutv__LANG ) ,
			'confirm_run_fillers'           => __( 'Run fillers on existant videos ? This may take some time.' , cutv__LANG ) ,
			'confirm_remove_filler'         => __( 'Do you really want to remove this filler ?' , cutv__LANG ) ,
		);
		
		wp_enqueue_script( 'jquery' );
		//wp_enqueue_script('cutv__functions', cutv__URL.'assets/js/cutv_.functions.js' . '?version='.cutv__VERSION );
		
		
		if( cutv__DEV_MODE === FALSE && cutv__USE_MIN_JS === TRUE ) {
			$js_file = cutv__URL . 'assets/js/cutv_.scripts.min.js';
			
			wp_register_script( 'cutv__scripts' , $js_file . '?version=' . cutv__VERSION , array( 'jquery' ) );
			wp_localize_script( 'cutv__scripts' , 'cutv__localize' , $js_localize_array );
			wp_localize_script( 'cutv__scripts' , 'cutv__globals' , $js_globals_array );
			wp_enqueue_script( 'cutv__scripts' );
			
		} else {
			$js_file = cutv__URL . 'assets/js/cutv_.scripts.js';
			
			wp_register_script( 'cutv__scripts_chart' , cutv__URL . 'assets/js/cutv_.chart.min.js' );
			wp_enqueue_script( 'cutv__scripts_chart' );

			wp_register_script( 'cutv__scripts_clipboard' , cutv__URL . 'assets/js/cutv_.clipboard.min.js' );
			wp_enqueue_script( 'cutv__scripts_clipboard' );
			
			wp_register_script( 'cutv__scripts_selectize' , cutv__URL . 'assets/js/cutv_.selectize.min.js' );
			wp_enqueue_script( 'cutv__scripts_selectize' );
			
			wp_register_script( 'cutv__scripts_countup' , cutv__URL . 'assets/js/cutv_.countup.js' );
			wp_enqueue_script( 'cutv__scripts_countup' );
			
			wp_register_script( 'cutv__scripts_noui' , cutv__URL . 'assets/js/cutv_.slider.min.js' );
			wp_enqueue_script( 'cutv__scripts_noui' );
			
			wp_register_script( 'cutv__scripts' , $js_file . '?version=' . cutv__VERSION );
			wp_localize_script( 'cutv__scripts' , 'cutv__localize' , $js_localize_array );
			wp_localize_script( 'cutv__scripts' , 'cutv__globals' , $js_globals_array );
			wp_enqueue_script( 'cutv__scripts' );
		}
	}
	
	/* Load CSS files (minified or normal version) in admin area only */
	add_action( 'admin_head' , 'cutv__load_styles' );
	function cutv__load_styles() {
		
		if( cutv__USE_LOCAL_FONTAWESOME ) {
			wp_register_style( 'cutv__icons' , cutv__URL . 'assets/css/font-awesome.min.css' );
			wp_enqueue_style( 'cutv__icons' );
		} else {
			wp_register_style( 'cutv__icons' , cutv__FONTAWESOME_CSS_URL );
			wp_enqueue_style( 'cutv__icons' );
		}
		
		if( cutv__DEV_MODE === FALSE && cutv__USE_MIN_CSS === TRUE ) {
			
			$css_file = cutv__URL . 'assets/css/cutv_.styles.min.css';
			wp_register_style( 'cutv__styles' , $css_file . '?version=' . cutv__VERSION );
			wp_enqueue_style( 'cutv__styles' );
			
		} else {
			
			$css_file = cutv__URL . 'assets/css/cutv_.styles.css';
			wp_register_style( 'cutv__selectize' , cutv__URL . 'assets/css/cutv_.selectize.min.css' );
			wp_enqueue_style( 'cutv__selectize' );
			
			wp_register_style( 'cutv__noui_styles' , cutv__URL . 'assets/css/cutv_.slider.min.css' );
			wp_enqueue_style( 'cutv__noui_styles' );
			
			wp_register_style( 'cutv__flags_styles' , cutv__URL . 'assets/css/cutv_.flags.min.css' );
			wp_enqueue_style( 'cutv__flags_styles' );
			
			wp_register_style( 'cutv__styles' , $css_file . '?version=' . cutv__VERSION );
			wp_enqueue_style( 'cutv__styles' );
			
		}
		
		
		if( is_rtl() ) {
			wp_enqueue_style( 'cutv__styles_rtl' , cutv__URL . 'assets/css/cutv_.styles.rtl.css' );
		}
	}
	
	/* Load CSS fix for embeding youtube player */
	add_action( 'wp_head' , 'cutv__load_services_css_styles' , 120 );
	add_action( 'admin_head' , 'cutv__load_services_css_styles' , 120 );
	function cutv__load_services_css_styles() {
		global $cutv__vs;
		$css = '';
		$css .= '#adminmenu .menu-icon-video div.wp-menu-image:before {content: "\f126";}';
		$css .= '#adminmenu .menu-icon-source div.wp-menu-image:before {content: "\f179";}';

		if( count( $cutv__vs ) != 0 ) {
			foreach ( (array) $cutv__vs as $vs ) {
				if( cutv__DEV_MODE === TRUE ) {
					$css .= "/*cutv_ DEV MODE */\n";
					$css .= "#adminmenuback{display:none;}\n";
					$css .= "/*cutv_ DEV MODE */\n";
				}
				$css .= "/*cutv_ VIDEO SERVICE STYLES ( " . $vs[ 'label' ] . " ) */\n";
				//$css .= "/* START */\n";
				$css .= trim( preg_replace( '/\t+/' , '' , $vs[ 'get_styles' ]() ) );
				//$css .= "<!-- END -->\n";
				$css .= "/* cutv_ VIDEO SERVICE STYLES ( " . $vs[ 'label' ] . " ) */\n\n";
			}
		}
		echo "<style>\n $css\n </style>\n";
	}
	
	/* Load CSS fix for embeding youtube player */
	add_action( 'wp_head' , 'cutv__load_dynamic_css' , 100 );
	add_action( 'admin_head' , 'cutv__load_dynamic_css' , 100 );
	function cutv__load_dynamic_css() {
		global $cutv__status , $cutv__services;
		
		$css = '';
		$css .= '.cutv__embed .fluid-width-video-wrapper{ padding-top:56% !important; }';
		$css .= '.ad-container.ad-container-single-media-element-annotations.ad-overlay{ background:red !important; }';
		/*
		$css .= '.cutv__button{ background : '.cutv__BUTTON_BGCOLOR.' !important; color : '.cutv__BUTTON_COLOR.' !important; }';
		$css .= '.cutv__button:hover{ background : '.cutv__BUTTON_HOVER_BGCOLOR.' !important; color : '.cutv__BUTTON_HOVER_COLOR.' !important; }';
		*/
		foreach ( $cutv__status as $value => $data ) {
			$css .= '.cutv__video_status.' . $value . '{ background:' . $data[ 'color' ] . ' ;} ';
		}
		?>
		<style><?php echo $css; ?></style><?php
	}

	/* Load CSS fix for embeding youtube player */
	add_action( 'wp_footer' , 'cutv__load_css_fix' );
	function cutv__load_css_fix() {
		global $cutv__status , $cutv__services;
		
		$css = '';
		
		foreach ( $cutv__status as $value => $data ) {
			$css .= '.cutv__video_status.' . $value . '{ background-color:red;}';
		}
		
		
		?>
		<style>
			<?php echo $css; ?>
			.cutv__embed {
				position: relative !important;
				padding-bottom: 56.25% !important;
				padding-top: 30px !important;
				height: 0 !important;
				overflow: hidden !important;
			}
			
			.cutv__embed.cutv__vst_embed {
				padding-top: 0px !important;
			}
			
			.cutv__embed iframe, .cutv__embed object, .cutv__embed embed {
				position: absolute !important;
				top: 0 !important;
				left: 0 !important;
				width: 100% !important;
				height: 100% !important;
			}
		</style>
		<?php
	}
	
	add_filter( 'wp_head' , 'cutv__watermark' , 10000 );
	function cutv__watermark() {
		$act = cutv__get_activation( 'cutv_' );
		//_d( $act );
		if( $act[ 'act_status' ] == 1 ) {
			$licensed = " - License activated by " . $act[ 'buy_user' ] . ".";
		} else {
			$licensed = " - Not Activated. ";
		}
		echo "\n <!-- ##cutv_ : WP Video Robot version " . $act[ "act_version" ] . " " . $licensed . "--> \n";
	}