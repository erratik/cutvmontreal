<?php

	/* DEFINE ADDON LOCALLY */
	if( ! function_exists( 'wpvr_define_addon_locally' ) ) {
		function wpvr_define_addon_locally( $addon_id , $addons ) {
			global $wpvr_addons;

			//d( $addon_id );
			//d( $wpvr_addons[$addon_id] );


			if( ! isset( $wpvr_addons[ $addon_id ] ) ) return $addons;
			$addon_infos = $wpvr_addons[ $addon_id ][ 'infos' ];

			$led        = new stdClass();
			$led->label = 'Local';
			$led->icon  = 'fa-dot-circle-o';
			$led->class = 'local';

			$addons[ $addon_id ] = array(
				'id'            => $addon_id ,
				'title'         => $addon_infos[ 'title' ] ,
				'description'   => $addon_infos[ 'description' ] ,
				'excerpt'       => $addon_infos[ 'excerpt' ] ,
				'version'       => $addon_infos[ 'version' ] ,
				'version_date'  => '' ,
				'wpvr_version'  => $addon_infos[ 'wpvr_version' ] ,
				'thumbnail_url' => $addon_infos[ 'thumbnail_url' ] ,
				'addon_url'     => '#' ,
				'doc_url'       => FALSE ,
				'plugin_dir'    => $addon_id . '/' . $addon_id . '.php' ,
				'led'           => $led ,
			);

			//d( $addons );

			return $addons;
		}
	}

	/* SHOULD THE ADDON WORK OR NOT ? */
	if( ! function_exists( 'wpvr_get_addon_options' ) ) {
		function wpvr_get_addon_options( $addon_id , $addon_slot_name = null , $addon_defaults = null ) {
			global $wpvr_addons;
			//d( $addon_id );
			//d( $addon_slot_name );
			//d( $addon_defaults );

			if( $addon_slot_name == null ) {
				if( ! isset( $wpvr_addons[ $addon_id ] ) ) return FALSE;
				$slot = get_option( $wpvr_addons[ $addon_id ][ 'infos' ][ 'slot_name' ] );
			} else {
				$slot = get_option( $addon_slot_name );
			}

			if( $slot == '' ) $slot = array();
			if( $addon_defaults == null ) {
				if( ! isset( $wpvr_addons[ $addon_id ] ) ) return FALSE;
				$slot_defaults = $wpvr_addons[ $addon_id ][ 'defaults' ];
			} else {
				$slot_defaults = $addon_defaults;
			}

			return wpvr_extend( $slot , $slot_defaults , TRUE );

		}
	}

	/* SHOULD THE ADDON WORK OR NOT ? */
	if( ! function_exists( 'wpvr_render_addons_offers' ) ) {
		function wpvr_render_addons_offers() {
			?>
			<a href = "https://store.wpvideorobot.com/" target="_blank" title = "Buy 3 addons and get 30% OFF!">
				<div class = "wpvr_addon_offer">
					<img
						src = "<?php echo WPVR_URL . 'assets/images/offers.gif'; ?>"
						alt = "WPVR OFFER"
					/>
				</div>
			</a>
			<div class = "wpvr_clearfix"></div>
			<?php
		}
	}

	/* SHOULD THE ADDON WORK OR NOT ? */
	if( ! function_exists( 'wpvr_addon_should_work' ) ) {
		function wpvr_addon_should_work( $post = null , $slot ) {
			
			global $wpvr_dynamics;

			if( $post == null ) global $post;

			if( $slot[ 'addon_enabled' ] == '' || $slot[ 'addon_enabled' ] === FALSE ) return FALSE;
			if( isset( $slot[ 'video_categories' ] ) ) {
				$video_cats = $slot[ 'video_categories' ];
				if( isset( $video_cats[ '0' ] ) && $video_cats[ '0' ] == '0' ) unset( $video_cats[ '0' ] );
				if( count( $video_cats ) == 0 ) $video_cats = null;
			} else
				$video_cats = null;
			

			//new dBug( $video_cats );
			
			$current_cats = wp_get_post_categories( $post->ID );

			if( $video_cats == '' || $video_cats == array() || $video_cats == null ) $do_it = TRUE;
			else {
				$do_it = FALSE;
				if( count( $current_cats ) != 0 ) {
					foreach ( $current_cats as $i => $cat_id ) {
						if( in_array( $cat_id , $video_cats ) ) $do_it = TRUE;
					}
				}
			}

			//new dBug( $do_it );
			return $do_it;

		}
	}
	
	/* GET ADDONS DATA*/
	if( ! function_exists( 'wpvr_get_addons' ) ) {
		function wpvr_get_addons( $args = array() , $is_reloading = FALSE ) {
			$r = array(
				'status' => '' ,
				'msg'    => '' ,
				'items'  => array() ,
			);

			if( $is_reloading === FALSE ) {
				$r_ = get_option( 'wpvr_addons_list' );
				//new dBug( $r_ );
				if( $r_ == null || $r_ == '' || $r_ == FALSE ) {
					//echo "<br/> Getting Addons List from API (first)";
					return wpvr_get_addons( $args , TRUE );
				} else {
					//echo "<br/> REloading Addons List";
					return $r_;
				}
			} else {
				//echo "<br/> Getting Addons List from API (multi)";
				
				$args           = array();
				$addons_api_url = WPVR_API_REQ_URL;

				$url = wpvr_capi_build_query( $addons_api_url , array(
					'api_key'          => WPVR_API_REQ_KEY ,
					'action'           => 'get' ,
					'categories_slugs' => 'wpvr-addon' ,
					'get_addons'       => 1 ,
					'encrypt_results'  => 1 ,
					'only_results'     => 1 ,
				) );

				$capi = wpvr_capi_remote_get( $url , FALSE );

				//d( $capi );

				if( $capi[ 'status' ] != 200 ) {
					return array(
						'status' => FALSE ,
						'msg'    => 'Addons API unreachable.' ,
						'data'   => null ,
					);
				}
				$r = array(
					'status' => TRUE ,
					'msg'    => 'Last refresh : ' . date( 'Y-m-d @ H:i:s' ) ,
					'items'  => json_decode( base64_decode( $capi[ 'data' ] ) ) ,
				);
				
				update_option( 'wpvr_addons_list' , $r );

				return $r;
			}

		}
	}
	
	/* RENDER ADDONS LIST */
	if( ! function_exists( 'wpvr_render_addons_list' ) ) {
		function wpvr_render_addons_list( $addons , $installed = FALSE ) {

			//$addons = apply_filters( 'wpvr_extend_addons_list' , $addons );
			//d( $addons );
			if( count( $addons ) == 0 ) {
				if( $installed ) {
					wpvr_render_not_found( __( 'There is no addon installed.' , WPVR_LANG ) );
				} else {
					wpvr_render_not_found( __( 'There is no addon to show.' , WPVR_LANG ) );
				}

				return FALSE;
			}
			$addons_on = $addons_off = array();
			foreach ( (array) $addons as $id => $addon ) {
				if( $addon[ 'is_active' ] ) $addons_on[] = $addon;
				else $addons_off[] = $addon;
			}
			$addons = array_merge( $addons_on , $addons_off );

			$i = 0; ?>
			<?php foreach ( $addons as $id => $addon ) { ?>
				<?php
				if( $addon[ 'is_installed' ] ) {
					$addon_act = wpvr_get_activation( $addon[ 'id' ] );
					if( $addon_act[ 'act_status' ] == '1' ) $activated = "Licensed";
					else $activated = "Not Licensed";
				} else $activated = '';
				//d( $addon );

				?>
				<!-- ADDON -->
				<div class = "wpvr_addon_box" categories = "<?php echo $addon[ 'ledCat' ]; ?>">
					<div class = "wpvr_addon_box_thumb">
						<?php if( $addon[ 'is_installed' ] ) { ?>
							<?php if( $addon[ 'is_active' ] ) { ?>
								<div class = "wpvr_addon_box_status activated"> ACTIVE</div>
							<?php } else { ?>
								<div class = "wpvr_addon_box_status installed"> INACTIVE</div>
								<div class = "wpvr_addon_box_overlay"></div>
							<?php } ?>
						<?php } else { ?>
							<div class = "wpvr_addon_box_status installed"> NOT INSTALLED</div>
						<?php } ?>

						<?php if( $addon[ 'led' ]->class != '' && $addon[ 'led' ]->label != '' ) {
							$addon[ 'led' ] = (array) $addon[ 'led' ]; ?>
							<div class = "wpvr_addon_box_led <?php echo $addon[ 'led' ][ 'class' ]; ?>">
								<i class = "fa <?php echo $addon[ 'led' ][ 'icon' ]; ?>"></i> <?php echo $addon[ 'led' ][ 'label' ]; ?>
							</div>
						<?php } ?>

						<a target = "_blank" href = "<?php echo $addon[ 'link_url' ]; ?>" title = "Manage This Addon">
							<img src = "<?php echo $addon[ 'thumbnail_url' ]; ?>"/>
						</a>
					</div>
					<div class = "wpvr_addon_box_title">
						<?php echo $addon[ 'title' ]; ?>
					</div>
					<div class = "wpvr_addon_box_excerpt">
						<p class = "wpvr_addon_box_excerpt_content">
							<?php echo $addon[ 'excerpt' ]; ?>
						</p>
						<span class = "pull-left">
							<strong>v<?php echo $addon[ 'version' ]; ?>
								<?php if( $addon[ 'version_date' ] != '' ) { ?>
									(<?php echo $addon[ 'version_date' ]; ?>)
								<?php } ?>
							</strong>
						</span>
						<span class = "pull-right">
							<?php echo $activated; ?>
						</span>

						<p class = "wpvr_addon_box_links">
							<a target = "_blank" class = "wpvr_addon_link pull-left" href = "<?php echo $addon[ 'doc_url' ]; ?>"> Documentation </a>
							<a target = "_blank" class = "wpvr_addon_link pull-right " href = "<?php echo $addon[ 'addon_url' ]; ?>"> Learn More </a>
						</p>


					</div>
					<div class = "wpvr_addon_box_action">
						<?php if( $addon[ 'is_installed' ] ) { ?>
							<?php if( $addon[ 'is_active' ] ) { ?>
								<a href = "<?php echo $addon[ 'link_url' ]; ?>" class = "wpvr_addon_box_button half wpvr_submit_button wpvr_addon_manage">
									<div>
										<i class = "fa fa-gear"/></i>
										<span>MANAGE</span>
									</div>
								</a>
								<a href = "<?php echo $addon[ 'deactivate_url' ]; ?>" class = "wpvr_addon_box_button half wpvr_submit_button wpvr_addon_manage">
									<div>
										<i class = "fa fa-power-off"/></i>
										<span>DEACTIVATE</span>
									</div>
								</a>
							<?php } else { ?>
								<a href = "<?php echo $addon[ 'activate_url' ]; ?>" class = "wpvr_addon_box_button wpvr_submit_button wpvr_addon_manage">
									<div>
										<i class = "fa fa-power-off"/></i>
										<span>ACTIVATE</span>
									</div>
								</a>
							<?php } ?>
						<?php } else { ?>
							<a href = "<?php echo $addon[ 'link_url' ]; ?>" class = "wpvr_addon_box_button wpvr_submit_button wpvr_addon_get" title = "Get This Addon">
								<i class = "fa fa-download"/></i>
								<span>GET THIS ADD ON</span>
							</a>
						<?php } ?>
					</div>
				</div>
				<!-- ADDON -->
			<?php } ?>
			<?php
		}
	}
	
	/* RENDER ADDON OPTIONS */
	if( ! function_exists( 'wpvr_addons_licences_form_render' ) ) {
		function wpvr_addons_licences_form_render() {
			global $wpvr_addons;
			$wpvr_act = wpvr_get_activation( 'wpvr' );
			//$act = get_option( 'wpvr_activation' );
			//d( $wpvr_act );
			//$act['act_addons']['wpvr-monetizer'] = 'popo';


			if( count( $wpvr_addons ) == 0 ) {
				?>
				<div class = "wpvr_nothing">
					<i class = "fa fa-frown-o"></i><br/>
					<?php _e( 'There is no addon installed.' , WPVR_LANG ); ?>
				</div><br/><br/><br/>
				<?php
				return FALSE;
			}
			?>
			<div class = "wpvr_license">


				<form id = "wpvr_register_addons_licences_form" action = "<?php echo WPVR_ACTIONS_URL; ?>">
					<?php foreach ( $wpvr_addons as $slug => $addon ) { ?>
						<?php
						//d( $addon );

						$act = wpvr_get_activation( $slug );
						if( ! isset( $act[ 'buy_expires' ] ) ) $act[ 'buy_expires' ] = '(never)';
						//if( isset( $addon[ 'infos' ][ 'free_addon' ] ) && $addon[ 'infos' ][ 'free_addon' ] === TRUE ) continue;

						if( isset( $act[ 'act_addons' ][ $slug ] ) ) $licence = $act[ 'act_addons' ][ $slug ];
						else $licence = '';

						//d( $act );

						if( $act[ 'act_status' ] == 1 ) {
							$is_activated = 1;
							$span_class   = 'success';
							$span_msg     = '<i class="fa fa-circle wpvr_activated_icon"></i> ';
							$span_msg .= 'Activated on ' . $act[ 'act_date' ] . '';
							$span_msg .= ', Expires on ' . $act[ 'buy_expires' ] . '.';
							$span_msg
								.= '<a
								href = "javascript:;"
								class = "pull-right wpvr_reset_single_addon_licence"
								slug = "' . $slug . '"
								url="' . WPVR_ACTIONS_URL . '"
								>
								<i class = "fa fa-close"></i> Reset
							</a>';
						} else {
							$is_activated = 0;
							$span_class   = '';
							$span_msg     = '';
						}

						?>
						<div class = "wpvr_licence_data">
							<label>
								<strong>
									<?php echo $addon[ 'infos' ][ 'title' ]; ?>
								</strong>
								( version <?php echo $addon[ 'infos' ][ 'version' ]; ?> )
							</label>
							<br/>
							<input
								type = "text"
								id = "licence_<?php echo $slug; ?>"
								name = ""
								class = "wpvr_license_input "
								is_activated = "<?php echo $is_activated; ?>"
								slug = "<?php echo $slug; ?>"
								value = "<?php echo $act[ 'act_code' ]; ?>"
								version = "<?php echo $addon[ 'infos' ][ 'version' ]; ?>"
							/>


							<div
								class = "wpvr_addon_activation_message <?php echo $span_class; ?>"
								id = "wpvr_addon_licence_<?php echo $slug; ?>"
							><?php echo $span_msg; ?></div>

						</div>
					<?php } ?>
					<input type = "hidden" id = "act_email" name = "act_email" value = "<?php echo $wpvr_act[ 'act_email' ]; ?>"/>
					<input type = "hidden" id = "act_domain" name = "act_domain" value = "<?php echo $wpvr_act[ 'act_domain' ]; ?>"/>
					<input type = "hidden" id = "act_url" name = "act_url" value = "<?php echo $wpvr_act[ 'act_url' ]; ?>"/>
					<input type = "hidden" id = "act_ip" name = "act_ip" value = "<?php echo $wpvr_act[ 'act_ip' ]; ?>"/>
				</form>
				<button url = "<?php echo WPVR_ACTIONS_URL; ?>" id = "wpvr_reset_addon_licences" class = "wpvr_black_button pull-left wpvr_submit_button wpvr_large">
					<i class = "wpvr_button_icon fa fa-undo"></i><?php _e( 'Reset Addons Licences' , WPVR_LANG ); ?>
				</button>

				<button id = "wpvr_register_addon_licenses" class = "pull-right wpvr_submit_button wpvr_large">
					<i class = "wpvr_button_icon fa fa-save"></i><?php _e( 'Register Addon Licences' , WPVR_LANG ); ?>
				</button>
				<div class = "wpvr_clearfix"></div>
			</div>


			<?php
		}
	}
	
	/* RENDER ADDON OPTIONS */
	if( ! function_exists( 'wpvr_addon_option_render' ) ) {
		function wpvr_addon_option_render( $option , $value = null , $echo = TRUE ) {

			if( $echo === FALSE ) ob_start();


			if( is_string( $value ) ) {
				$option_value = stripslashes( $value );
			} else {
				$option_value = $value;
			}
			if( isset( $option[ 'tab_class' ] ) ) $tab_class = $option[ 'tab_class' ];
			else $tab_class = '';

			$option_name = $option[ 'id' ];

			//new dBug( $option );

			if( ! isset( $option[ 'masterOf' ] ) || ! is_array( $option[ 'masterOf' ] ) || count( $option[ 'masterOf' ] ) == 0 ) {
				$masterOf = '';
				$isMaster = '';
			} else {
				$masterOf = ' masterOf = "' . implode( ',' , $option[ 'masterOf' ] ) . '" ';
				$isMaster = 'isMaster';
			}

			if( ! isset( $option[ 'tabMasterOf' ] ) || ! is_array( $option[ 'tabMasterOf' ] ) || count( $option[ 'tabMasterOf' ] ) == 0 ) {
				$tabMasterOf = '';
				//$isMaster = '';
			} else {
				$tabMasterOf = ' tabMasterOf = "' . implode( ',' , $option[ 'tabMasterOf' ] ) . '" ';
				//$isMaster = 'isMaster';
			}


			if( ! isset( $option[ 'masterValue' ] ) ) $masterValue = '';
			else    $masterValue = ' masterValue = "' . $option[ 'masterValue' ] . '" ';

			if( ! isset( $option[ 'hasMasterValue' ] ) ) $hasMasterValue = '';
			else    $hasMasterValue = ' hasMasterValue = "' . $option[ 'hasMasterValue' ] . '" ';

			if( ! isset( $option[ 'class' ] ) ) $option_class = '';
			else    $option_class = $option[ 'class' ];


			//Switch Option Type
			if( $option[ 'type' ] == 'image' ) {
				$image_url = $option_value;
				//$default_img = pof_get_field_default_values( $field , TRUE );
				if( $image_url == '' ) $image_url = WPVR_NO_THUMB;
				if( $image_url == WPVR_NO_THUMB ) {
					$remove = '';
					$value  = '';
				} else {
					$remove = '<div class="wpvr_option_image_remove"><i class="fa fa-remove"></i></div>';
					$value  = $image_url;
				}

				$remove = '<button class="wpvr_option_image_remove"><i class="fa fa-remove"></i></button>';
				?>
				<div
					addon_id = "<?php echo $option[ 'id' ]; ?>"
					class = "wpvr_option on  <?php echo $tab_class; ?> "
					option_id = "<?php echo $option[ 'id' ]; ?>"
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<div class = "wpvr_option_button pull-right ">
						<div
							class = "wpvr_option_image_wrap"
							id = "wpvr_option_image_<?php echo $option[ 'id' ]; ?>"
							default = "<?php echo WPVR_NO_THUMB; ?>"
							option_type = "<?php echo $option[ 'type' ]; ?>"
						>
							<div class = "wpvr_option_image_thumb_wrap">
								<div class = "wpvr_option_image_thumb">
									<img class = "wpvr_option_thumb_img" src = "<?php echo $image_url; ?>"/>
									<?php echo $remove; ?>
								</div>
							</div>
							<div class = "wpvr_clearfix"></div>
							<input
								type = "text"
								name = "<?php echo $option_name; ?>"
								id = "<?php echo $option_name; ?>"
								class = "wpvr_option_image_thumb_input <?php echo $tab_class; ?> "
								value = "<?php echo $option_value; ?>"
								placeholder = "No image selected ..."
							/>
							<button class = "wpvr_button wpvr_black_button wpvr_full_width wpvr_option_image_thumb_button">
								<i class = "fa fa-upload"></i>
								Upload Image
							</button>
						</div>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>
						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<?php
				return;
				?>
				<div
					addon_id = "<?php echo $option[ 'id' ]; ?>"
					class = "wpvr_option <?php echo $tab_class; ?> <?php echo wpvr_get_button_state( $option_value ); ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<div class = "wpvr_option_button pull-right ">
						<?php wpvr_make_switch_button( $option_name , $option_value ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
				</div>

				<?php
			} elseif( $option[ 'type' ] == 'switch' ) {

				?>
				<div
					addon_id = "<?php echo $option[ 'id' ]; ?>"
					class = "wpvr_option <?php echo $tab_class; ?> <?php echo wpvr_get_button_state( $option_value ); ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<div class = "wpvr_option_button pull-right ">
						<?php wpvr_make_switch_button( $option_name , $option_value ); ?>
					</div>
					<div class = "option_text">
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
				</div>

				<?php
			} elseif( $option[ 'type' ] == 'texteditor' ) {

				$textarea_name = $option[ 'id' ];
				$option_name   = $option[ 'id' ] . '_editor';
				if( ! isset( $option[ 'rows' ] ) ) $option[ 'rows' ] = 5;

				if( ! isset( $option[ 'height' ] ) ) $option[ 'height' ] = 'default';

				if( $option[ 'height' ] == 'default' ) $heightClass = '';
				else $heightClass = $option[ 'height' ];

				?>
				<div
					class = "wpvr_option on <?php echo $tab_class; ?> <?php echo $isMaster; ?> <?php echo $heightClass; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<div class = "wpvr_texteditor pull-right">
						<?php
							$editor_settings = array(
								'textarea_rows' => 15 ,
								'textarea_name' => $textarea_name ,
								'editor_class'  => ' ' . $option_name . ' ' . $tab_class ,
								//'editor_id'  => 'yassine',
								'wpautop'       => TRUE ,
								'media_buttons' => TRUE ,
								'tinymce'       => array(
									//"selector" => '#yassine',
									"setup" => "function( editor ) {
										editor.on('change' , function(e){
											tinymce.triggerSave();
										});
									}" ,
								) ,
							);
							// d( $option_value );
							// d( $option_name );
							// d( $editor_settings );
							wp_editor(
								$option_value ,
								$option_name ,
								$editor_settings
							);
						?></div>
					<div>
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>

				<?php
			} elseif( $option[ 'type' ] == 'text' ) {
				?>
				<div
					class = "wpvr_option on <?php echo $tab_class; ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<input
						type = "text"
						class = "wpvr_options_input wpvr_large pull-right"
						id = "<?php echo $option_name; ?>"
						name = "<?php echo $option_name; ?>"
						value = "<?php echo $option_value; ?>"
					/>

					<div>
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
				</div>

				<?php
			} elseif( $option[ 'type' ] == 'text_small' ) {
				?>
				<div
					class = "wpvr_option on <?php echo $tab_class; ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<input
						type = "text"
						class = "wpvr_options_input pull-right"
						id = "<?php echo $option_name; ?>"
						name = "<?php echo $option_name; ?>"
						value = "<?php echo $option_value; ?>"
					/>

					<div>
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
				</div>

				<?php
			} elseif( $option[ 'type' ] == 'slider' ) {
				if( ! isset( $option[ 'class' ] ) ) $option[ 'class' ] = '';
				$token = bin2hex( openssl_random_pseudo_bytes( 10 ) );
				?>
				<div
					class = "wpvr_option on <?php echo $tab_class; ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<div class = "pull-right">
						<div
							class = "wpvr_option_slider_range pull-left <?php echo $option[ 'class' ]; ?>"
							slider_input = "<?php echo $option[ 'id' ]; ?>"
							slider_min = "<?php echo $option[ 'min' ]; ?>"
							slider_max = "<?php echo $option[ 'max' ]; ?>"
							slider_step = "<?php echo $option[ 'step' ]; ?>"
							slider_value = "<?php echo $option_value; ?>"
							id = "slider_<?php echo $token; ?>"
						></div>
						<div class = "wpvr_option_slider_input pull-left">
							<?php if( isset( $option[ 'unit' ] ) && $option[ 'unit' ] != '' ) { ?>
								<span class = "wpvr_option_slider_unit"><?php echo $option[ 'unit' ]; ?></span>
							<?php } ?>
							<input
								type = "text"
								class = "wpvr_options_input  wpvr_option_slider_input_text"
								name = "<?php echo $option_name; ?>"
								id = "<?php echo $option[ 'id' ]; ?>"
								value = '<?php echo( $option_value ); ?>'
								slider_id = "slider_<?php echo $token; ?>"
							/>

						</div>
						<div class = "wpvr_clearfix"></div>
					</div>

					<div>
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>

				<?php

			} elseif( $option[ 'type' ] == 'textarea' ) {
				?>
				<div
					class = "wpvr_option on <?php echo $tab_class; ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
				<textarea
					type = "text"
					class = "wpvr_options_textarea pull-right"
					id = "<?php echo $option_name; ?>"
					name = "<?php echo $option_name; ?>"
				><?php echo $option_value; ?></textarea>

					<div>
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>

				<?php
			} elseif( $option[ 'type' ] == 'select' ) {
				?>
				<div
					class = "wpvr_option on <?php echo $tab_class; ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<div class = "wpvr_option_button pull-right">
						<?php
							$isSelected = array();
							$ov         = $option[ 'values' ];
							foreach ( $ov as $v => $label ) {
								$isSelected[ $v ] = '';
							}
							$isSelected[ $option_value ] = ' selected="selected" ';

						?>
						<select
							class = "wpvr_option_select pull-right "
							name = "<?php echo $option_name; ?>"
							id = "<?php echo $option_name; ?>"
						>
							<?php foreach ( $option[ 'values' ] as $v => $label ) { ?>
								<option value = "<?php echo $v; ?>" <?php echo $isSelected[ $v ]; ?>>
									<?php echo $label; ?>
								</option>
							<?php } ?>
						</select>
					</div>
					<div>
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<?php
			} elseif( $option[ 'type' ] == 'multiselect' ) {
				?>
				<div
					class = "wpvr_option on <?php echo $tab_class; ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"
				>
					<div class = "wpvr_option_button pull-right">
						<?php

							if( ! isset( $option[ 'values' ] ) || ! is_array( $option[ 'values' ] ) ) {
								echo "NO OPTION DEFINED FOR THIS SELECT";
							} else {

								if( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'categories' ) {

									// GET ALL CATEGORIES
									$cats = wpvr_get_categories_count();
									foreach ( $cats as $cat ) {
										$option[ 'values' ][ $cat[ 'value' ] ] = $cat[ 'label' ] . ' (' . $cat[ 'count' ] . ')';
									}

								} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'all_categories' ) {

									// GET ALL CATEGORIES
									$cats = wpvr_get_categories_count( FALSE , TRUE );
									foreach ( $cats as $cat ) {
										$option[ 'values' ][ $cat[ 'value' ] ] = $cat[ 'label' ] . ' (' . $cat[ 'count' ] . ')';
									}

								} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'post_types' ) {

									// GET ALL POST TYPES
									$post_types = get_post_types( array(
										'public' => TRUE ,
									) );
									foreach ( $post_types as $cpt ) {
										$option[ 'values' ][ $cpt ] = $cpt;
									}


								} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'taxonomies' ) {

									// GET ALL TAXONOMIES
									$taxonomies = get_taxonomies( array(
										'_builtin' => FALSE ,
									) , 'objects' );
									foreach ( (array) $taxonomies as $tax ) {
										if( $tax->name != WPVR_SFOLDER_TYPE ) {
											$option[ 'values' ][ $tax->name ] = $tax->label;
										}
									}


								} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'post_types_ext' ) {
									$internal_cpts = array(
										//'page' ,
										'post' ,
										WPVR_VIDEO_TYPE ,
										'attachment' ,
										'revision' ,
										WPVR_SOURCE_TYPE ,
										'nav_menu_item' ,
									);
									// GET ALL POST TYPES
									$post_types = get_post_types( array(//'public' => true ,
									) );
									foreach ( $post_types as $cpt ) {
										if( ! in_array( $cpt , $internal_cpts ) )
											$option[ 'values' ][ $cpt ] = $cpt;
									}

								} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'tags' ) {

									// GET ALL TAGS
									$tags = get_tags();
									foreach ( $tags as $tag ) {
										$option[ 'values' ][ $tag->term_id ] = $tag->slug;
									}

								} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'authors' ) {

									// GET ALL AUTHORS
									$all_users = get_users( 'orderby=post_count&order=DESC' );
									foreach ( $all_users as $user ) {
										if( ! in_array( 'subscriber' , $user->roles ) )
											$option[ 'values' ][ $user->data->ID ] = $user->data->user_nicename;
									}

								} elseif( isset( $option[ 'source' ] ) && $option[ 'source' ] == 'services' ) {

									// GET ALL AUTHORS
									global $wpvr_vs;
									foreach ( $wpvr_vs as $vs ) {
										$option[ 'values' ][ $vs[ 'id' ] ] = $vs[ 'label' ];
									}

								}


								if( ! isset( $option[ 'maxItems' ] ) || $option[ 'maxItems' ] == 1 ) $mv = "1";
								elseif( $option[ 'maxItems' ] === FALSE ) $mv = '255';
								else $mv = $option[ 'maxItems' ];

								if( ! isset( $option[ 'placeholder' ] ) || $option[ 'placeholder' ] == '' )
									$option[ 'placeholder' ] = 'Pick one or more values';
								?>
								<div class = "wpvr_select_wrap">
									<input type = "hidden" value = "0" name = "<?php echo $option_name; ?>[]"/>
									<select
										class = "wpvr_field_selectize "
										name = "<?php echo $option_name; ?>[]"
										id = "<?php echo $option_name; ?>"
										maxItems = "<?php echo $mv; ?>"
										placeholder = "<?php echo $option[ 'placeholder' ]; ?>"
									>
										<option value = ""> <?php echo $option[ 'placeholder' ]; ?> </option>
										<?php foreach ( $option[ 'values' ] as $oValue => $oLabel ) { ?>
											<?php

											if( is_array( $option_value ) && in_array( $oValue , $option_value ) ) {
												$checked  = ' selected="selected" ';
												$oChecked = ' c="1" ';

											} elseif( ! is_array( $option_value ) && $oValue == $option_value ) {
												$checked  = ' selected="selected" ';
												$oChecked = ' c="1" ';
											} else {
												$checked  = '';
												$oChecked = ' c="0" ';
											}
											?>
											<option value = "<?php echo $oValue; ?>" <?php echo $checked; ?> <?php echo $oChecked; ?> >
												<?php echo $oLabel; ?>
											</option>
										<?php } ?>
									</select>
								</div>
								<?php
							}

						?>
					</div>
					<div>
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<?php
			} elseif( $option[ 'type' ] == 'list' ) {
				if( ! isset( $option[ 'maxItems' ] ) || $option[ 'maxItems' ] == 1 ) $mv = "1";
				elseif( $option[ 'maxItems' ] === FALSE ) $mv = '255';
				else $mv = $option[ 'maxItems' ];


				?>
				<div
					class = "wpvr_option on <?php echo $tab_class; ?> <?php echo $isMaster; ?>"
					option_id = "<?php echo $option[ 'id' ]; ?>"
					<?php echo $masterOf; ?>
					<?php echo $tabMasterOf; ?>
					<?php echo $masterValue; ?> <?php echo $hasMasterValue; ?>
					option_type = "<?php echo $option[ 'type' ]; ?>"

				>
					<div class = "wpvr_option_button pull-right">
						<input
							type = "text"
							class = "wpvr_selectize_list pull-right <?php echo $option_class; ?>"
							id = "<?php echo $option_name; ?>"
							name = "<?php echo $option_name; ?>"
							value = "<?php echo $option_value; ?>"
							placeholder = "<?php echo $option[ 'placeholder' ]; ?>"
							maxItems = "<?php echo $mv; ?>"


						/>

					</div>
					<div>
						<span class = "wpvr_option_title"><?php echo $option[ 'label' ]; ?></span><br/>

						<p class = "wpvr_option_desc"> <?php echo $option[ 'desc' ]; ?></p>
					</div>
					<div class = "wpvr_clearfix"></div>
				</div>
				<?php
			}


			if( $echo === FALSE ) {

				$rendered_option = ob_get_contents();
				//ob_get_flush();
				ob_get_clean();

				return $rendered_option;


			}

		}
	}


	/* RENDER ADDONS CATEGORIES */
	if( ! function_exists( 'wpvr_render_addons_categories' ) ) {
		function wpvr_render_addons_categories( $categories , $count ) {
			ksort( $categories );
			if( $count != 0 ) {

				?>
				<button class = "wpvr_button wpvr_white_button pull-left" cat = "wpvr_all">
					ALL (<?php echo $count; ?>)
				</button>
				<?php
			}
			foreach ( (array) $categories as $cat => $count ) {
				?>
				<button
					class = "wpvr_button  pull-left"
					cat = "<?php echo $cat; ?>"
				>
					<?php echo $cat . " ($count) "; ?>
				</button>
				<?php
			}
		}
	}