<?php


	if( ! function_exists( 'cutv_render_selectized_field' ) ) {
		function cutv_render_selectized_field( $field , $value = '' ) {
			$field = cutv_extend( $field , array(
				'id'          => '' ,
				'name'        => '' ,
				'maxItems'    => '' ,
				'placeholder' => '' ,
				'values'      => array() ,
			) );
			?>
			<div class = "cutv_select_wrap">
				<input type = "hidden" value = "0" name = "<?php echo $field[ 'name' ]; ?>[]"/>
				<select
					class = "cutv_field_selectize "
					name = "<?php echo $field[ 'name' ]; ?>[]"
					id = "<?php echo $field[ 'name' ]; ?>"
					maxItems = "<?php echo $field[ 'maxItems' ]; ?>"
					placeholder = "<?php echo $field[ 'placeholder' ]; ?>"
				>
					<option value = ""> <?php echo $field[ 'placeholder' ]; ?> </option>
					<?php foreach ( (array) $field[ 'values' ] as $oValue => $oLabel ) { ?>
						<?php

						if( is_array( $value ) && in_array( $oValue , $value ) ) {
							$checked  = ' selected="selected" ';
							$oChecked = ' c="1" ';

						} elseif( ! is_array( $value ) && $oValue == $value ) {
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
	}

	/* Hooking function to extend existing metaboxes */
	if( ! function_exists( 'cutv_extend_metaboxes_fields' ) ) {
		function cutv_extend_metaboxes_fields( $metaboxes = array() , $metabox_id = '' , $additional_fields = array() ) {

			global $debug;

			if( ! is_array( $additional_fields ) || count( $additional_fields ) == 0 ) return $metaboxes;
			if( ! is_array( $metaboxes ) || count( $metaboxes ) == 0 ) return $metaboxes;
			if( $metabox_id == '' ) return $metaboxes;
			foreach ( $metaboxes as $k => $metabox ) {
				if( $metabox[ 'id' ] == $metabox_id ) {
					foreach ( $additional_fields as $new_field ) {
						$metaboxes[ $k ][ 'fields' ][] = $new_field;
					}
				}
			}

			return $metaboxes;
		}
	}

	/* Hooking Function to add new metaboxes */
	if( ! function_exists( 'cutv_add_custom_metaboxes' ) ) {
		function cutv_add_custom_metaboxes( $metaboxes = array() , $additional_metaboxes = array() ) {

			global $debug;
			if( ! is_array( $additional_metaboxes ) || count( $additional_metaboxes ) == 0 ) return $metaboxes;
			if( ! is_array( $metaboxes ) || count( $metaboxes ) == 0 ) return $metaboxes;
			foreach ( $additional_metaboxes as $k => $new_metabox ) {
				$metaboxes[] = $new_metabox;
			}

			return $metaboxes;
		}
	}
	
	/* Get Durations in seconds */
	if( ! function_exists( 'cutv_duration_to_seconds' ) ) {
		function cutv_duration_to_seconds( $duration ) {
			if( $duration == '' ) return 0;

			if( $duration == '' || $duration == '0' ) return 0;
			elseif( $duration == 'PTS' ) return 0;
			else {
				$durationObj = new DateInterval( $duration );

				return ( 60 * 60 * $durationObj->h ) + ( 60 * $durationObj->i ) + $durationObj->s;
			}
		}
	}
	
	/* Apply filters on videos Found */
	if( ! function_exists( 'cutv_filter_videos_found' ) ) {
		function cutv_filter_videos_found( $videosFound , $options ) {
			return $videosFound;

		}
	}

	/* run DataFillers  */
	if( ! function_exists( 'cutv_run_dataFillers' ) ) {
		function cutv_run_dataFillers( $newPostId ) {
			$cutv_fillers = get_option( 'cutv_fillers' );
			if( $cutv_fillers == '' ) $cutv_fillers = array();

			if( CUTV_ENABLE_DATA_FILLERS === TRUE ) {
				foreach ( $cutv_fillers as $filler ) {
					if( $filler[ 'from' ] == 'custom_data' ) $data = $filler[ 'from_custom' ];
					else $data = get_post_meta( $newPostId , $filler[ 'from' ] , TRUE );

					$ok = update_post_meta( $newPostId , $filler[ 'to' ] , $data );
				}
			}
		}
	}

	/* Get Video Formated Duration by post id */
	if( ! function_exists( 'cutv_get_duration' ) ) {
		function cutv_get_duration( $post_id = '' , $return_seconds = FALSE ) {
			if( $post_id == '' ) {
				global $post;
				$post_id = $post->ID;
			}
			$duration = get_post_meta( $post_id , 'cutv_video_duration' , TRUE );
			$r        = cutv_get_duration_string( $duration , $return_seconds );

			return $r;
		}
	}
	
	/* Get Video Formated Duration by post id */
	if( ! function_exists( 'cutv_get_duration_string' ) ) {
		function cutv_get_duration_string( $duration = '' , $return_seconds = FALSE ) {
			if( $duration == '' ) {
				return '';
			}
			if( $duration == '' || $duration == '0' ) return 'xx:xx:xx';
			elseif( $duration == 'PTS' ) return 'xx:xx:xx';
			else {
				$durationObj = new DateInterval( $duration );
				$duration    = ( 60 * 60 * $durationObj->h ) + ( 60 * $durationObj->i ) + $durationObj->s;
			}

			//new dBug( $durationObj );

			if( $return_seconds === TRUE ) return $duration;
			//new dBug($duration);

			if( $duration < 3600 ) {
				$r = gmdate( "i:s" , $duration );
			} elseif( $duration < 86400 ) {
				$r = gmdate( "H:i:s" , $duration );
			} else {
				$duration -= 86400;
				$r = gmdate( "j\d H:i:s" , $duration );
			}

			return $r;
		}
	}
	
	/*Get Videos Views*/
	if( ! function_exists( 'cutv_get_views' ) ) {
		function cutv_get_views( $post_id = '' ) {
			if( $post_id == '' ) {
				global $post;
				if( ! class_exists( $post ) || ! property_exists( $post , 'ID' ) ) return FALSE;
				$post_id = $post->ID;
			}

			return get_post_meta( $post_id , 'cutv_video_views' , TRUE );
		}
	}
	
	/* LEt's Start the plugin */
	if( ! function_exists( 'cutv_start_plugin' ) ) {
		function cutv_start_plugin( $product_slug = 'cutv' , $product_version = CUTV_VERSION , $output = FALSE ) {

			$act  = cutv_get_activation( $product_slug );
			$site = array(
				'version' => $product_version ,
				'url'     => get_bloginfo( 'url' ) ,
				'domain'  => $_SERVER[ 'SERVER_NAME' ] ,
				'ip'      => isset( $_SERVER[ 'SERVER_ADDR' ] ) ? $_SERVER[ 'SERVER_ADDR' ] : '' ,
			);

			if( $act[ 'act_status' ] != '1' ) {
				//Alert
				$alert = cutv_capi_alert(
					$product_slug ,
					$site[ 'domain' ] ,
					$site[ 'url' ] ,
					$site[ 'ip' ] ,
					$site[ 'version' ]
				);
				//cutv_set_debug( $alert , true );
				if( $output === TRUE && $alert[ 'status' ] != '1' ) {
					echo $alert[ 'msg' ];
				}
			}
		}
	}
	
	/*Get Videos Views*/
	if( ! function_exists( 'cutv_get_fields' ) ) {
		function cutv_get_fields( $field_name = '' , $post_id = '' ) {
			if( $post_id == '' ) {
				global $post;
				if( ! class_exists( $post ) || ! property_exists( $post , 'ID' ) ) return FALSE;
				$post_id = $post->ID;
			}
			$fields = array(
				'video_service'  => get_post_meta( $post_id , 'cutv_video_service' , TRUE ) ,
				'video_id'       => get_post_meta( $post_id , 'cutv_video_id' , TRUE ) ,
				'video_duration' => get_post_meta( $post_id , 'cutv_video_duration' , TRUE ) ,
				'video_url'      => get_post_meta( $post_id , 'cutv_video_service_url' , TRUE ) ,
				'video_thumb'    => get_post_meta( $post_id , 'cutv_video_service_icon' , TRUE ) ,
				'video_thumb_hq' => get_post_meta( $post_id , 'cutv_video_service_thumb' , TRUE ) ,
				'video_views'    => get_post_meta( $post_id , 'cutv_video_views' , TRUE ) ,
			);
			if( $field_name == '' ) return $fields;
			elseif( array_key_exists( $field_name , $fields ) ) return $fields[ $field_name ];
			else return FALSE;
		}
	}
	
	/* Embed Video Player Manually */
	if( ! function_exists( 'cutv_embed' ) ) {
		function cutv_embed( $post_id = '' , $autoplay = FALSE , $echo = TRUE ) {
			if( $post_id == '' ) {
				global $post;
				//if( !class_exists($$post) || !property_exists($post,'ID') ) return false;
				if( ( isset( $post ) && ( $post instanceof WP_Post ) ) || ! property_exists( $post , 'ID' ) ) return FALSE;
				$post_id = $post->ID;
			}
			$cutv_video_id = get_post_meta( $post_id , 'cutv_video_id' , TRUE );
			$cutv_service  = get_post_meta( $post_id , 'cutv_video_service' , TRUE );

			//new dBug( $cutv_service );

			$embedCode = '<div class="wpvr_embed">' . cutv_video_embed( $cutv_video_id , $post_id , $autoplay , $cutv_service ) . '</div>';
			if( $echo ) echo $embedCode;
			else return $embedCode;
		}
	}

	/* Check Customer */
	if( ! function_exists( 'cutv_check_customer' ) ) {
		function cutv_check_customer() {
			$cutv_activation = cutv_get_activation( 'cutv' );
			//_d( $cutv_activation );return false;
			if( $cutv_activation[ 'act_status' ] === 1 ) return FALSE;

			global $cutv_pages , $cutv_options;
			if( ! isset( $cutv_pages ) || ! $cutv_pages ) return FALSE;

			if( isset( $cutv_options[ 'purchaseCode' ] ) && $cutv_options[ 'purchaseCode' ] != '' )
				$cutv_activation_code = $cutv_options[ 'purchaseCode' ];
			else
				$cutv_activation_code = $cutv_activation[ 'act_code' ];

			$envato_cb = '<div class="pull-right"><input checked="checked" type="checkbox" name="is_envato" value="is_envato" id="is_envato" /><label for="is_envato"> Envato Code </label></div>';


			$af = '';
			$af .= '<div class="wpvr_activation_form">';
			$af .= '	<input type="hidden" id="cutv_activation_id" value="' . $cutv_activation[ 'act_id' ] . '" />';
			$af .= '	<p>' . addslashes( __( 'Please activate your licence of WP Video Robot' , CUTV_LANG ) ) . '.</p>';
			$af .= '	<label>' . addslashes( __( 'Your Email' , CUTV_LANG ) ) . '</label><br/>';
			$af .= '	<input type="text" id="cutv_user_email" class="wpvr_aform_input" value="' . $cutv_activation[ 'act_email' ] . '" placeholder="" />';
			$af .= '	<br/><br/>';
			$af .= '	<label>' . addslashes( __( 'Your Purchase Code' , CUTV_LANG ) ) . '</label>' . $envato_cb . '<br/>';
			$af .= '	<input type="text" id="cutv_user_code" class="wpvr_aform_input" value="' . $cutv_activation_code . '" placeholder="" /><br/>';
			$af .= '	<span class="pull-right">';
			$af .= '		<a class="link" target="_blank" href="' . CUTV_SUPPORT_URL . '/tutorials/where-to-find-my-envato-purchase-code/" title="Click here">';
			$af .= '			' . addslashes( __( 'WHERE TO FIND MY ENVATO PURCHASE CODE' , CUTV_LANG ) ) . '';
			$af .= '		</a>';
			$af .= '	</span>';
			$af .= '	<br/><br/>';
			$af .= '	<div class="wpvr_aform_result"></div>';
			$af .= '</div>';

			$activation_form = str_replace( PHP_EOL , '' , $af );
			$activation_form = str_replace( '\n' , '' , $activation_form );
			//return false;
			?>
			<script type = "text/javascript">
				jQuery(document).ready(function ($) {
					setTimeout(function () {
						var activationBox = cutv_show_loading({
							title: 'WP VIDEO ROBOT ACTIVATION',
							text: '<?php echo $activation_form; ?>',
							isModal: true,
							boxClass: 'activationBox',
							pauseButton: '<i class="fa fa-unlock" ></i> <?php echo addslashes( __( 'ACTIVATE MY COPY' , CUTV_LANG ) ); ?>',
							cancelButton: '<a href="<?php echo CUTV_CC_PAGE_URL; ?>" target="_blank"><i class="fa fa-shopping-cart" ></i><?php echo addslashes( __( 'BUY WP VIDEO ROBOT' , CUTV_LANG ) ); ?></a>',
						});

						activationBox.doPause(function () {
							var btn = $('.cutv_loading_pause', activationBox);
							var spinner = cutv_add_loading_spinner(btn, 'pull-right');
							var error_msg = "<?php echo addslashes( __( 'Please enter a valid email and your Purchase Code.' , CUTV_LANG ) ) . ''; ?>";
							var url = '<?php echo CUTV_ACTIONS_URL; ?>';
							var plugin_dashboard_url = '<?php echo admin_url( 'admin.php?page=cutv-welcome' , 'http' ); ?>';
							var icon_error = '<i style="margin-right:10px;font-size:20px;line-height:20px;" class="fa fa-exclamation-circle"></i>';

							var email = jQuery('#cutv_user_email').val();
							var code = jQuery('#cutv_user_code').val();
							var id = jQuery('#cutv_activation_id').val();
							var is_envato = jQuery('#is_envato').prop('checked');
							if (is_envato) is_envato = 1;
							else is_envato = 0;
							var ok = true;
							if (!cutv_validate_email(email)) {
								jQuery('#cutv_user_email').addClass('error');
								ok = false;
							} else {
								jQuery('#cutv_user_email').removeClass('error');
							}

							if (code == '') {
								jQuery('#cutv_user_code').addClass('error');
								ok = false;
							} else {
								jQuery('#cutv_user_code').removeClass('error');
							}

							if (!ok) {
								cutv_remove_loading_spinner(spinner);
								jQuery('.cutv_aform_result').html('<div class="werror">' + icon_error + error_msg + '</div>');
								return false;

							} else {
								jQuery('.cutv_aform_result').html('<div class="wwait"><i class = "fa fa-cog fa-spin"></i> Please Wait ...</div>');
								jQuery.ajax({
									type: "POST",
									url: url + '?activate_copy&cutv_wpload',
									data: {
										email: email,
										code: code,
										id: id,
										is_envato: is_envato,
									},
									success: function (data) {
										cutv_remove_loading_spinner(spinner);
										var $data = cutv_get_json(data);
										if ($data.status == '1') {
											activationBox.doHide();
											var activationBoxEnd = cutv_show_loading({
												title: 'WP VIDEO ROBOT ACTIVATION',
												text: $data.msg,
												isModal: false,
												pauseButton: cutv_localize.ok_button,
											});
											activationBoxEnd.doPause(function () {
												activationBoxEnd.remove();
												window.location.href = plugin_dashboard_url;
											});

										} else {
											cutv_remove_loading_spinner(spinner);
											jQuery('.cutv_aform_result').html('<div class="werror">' + icon_error + $data.msg + '</div>');
										}
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(thrownError);
										cutv_remove_loading_spinner(spinner);
									}
								});
							}
						});


					}, 1000);

				});
			</script>
			<?php

		}
	}

	/* Add Log Action */
	if( ! function_exists( 'cutv_add_log' ) ) {
		function cutv_add_log( $log_data = array() ) {
			global $wpdb;
			if( ! isset( $log_data[ 'status' ] ) ) $log_data[ 'status' ] = '';
			if( ! isset( $log_data[ 'icon' ] ) ) $log_data[ 'icon' ] = '';
			$log_table        = $wpdb->prefix . "cutv_log";
			$current_user     = wp_get_current_user();
			$current_username = $current_user->user_login;
			if( $current_username != '' ) {
				$executed_by = '<b>' . __( 'Executed by :' , CUTV_LANG ) . ' </b>' . $current_username;
			} else {
				$executed_by = '<b>' . __( 'Executed by :' , CUTV_LANG ) . ' </b> CRON';
			}
			array_unshift( $log_data[ 'log_msgs' ] , $executed_by );


			$rows_affected = $wpdb->insert(
				$log_table ,
				array(
					'status'   => $log_data[ 'status' ] ,
					'time'     => $log_data[ 'time' ] ,
					'type'     => $log_data[ 'type' ] ,
					'action'   => $log_data[ 'action' ] ,
					'object'   => $log_data[ 'object' ] ,
					'icon'     => $log_data[ 'icon' ] ,
					'log_msgs' => json_encode( $log_data[ 'log_msgs' ] ) ,
				)
			);
		}
	}
	
	/* GENERATE SWITCH BUTTON */
	if( ! function_exists( 'cutv_make_switch_button' ) ) {
		function cutv_make_switch_button( $inputName , $inputState = FALSE , $inputClassName = '' , $inputId = '' ) {
			if( $inputState == FALSE ) {
				$isChecked       = "";
				$isChecked_class = "";
			} else {
				$isChecked       = " checked ";
				$isChecked_class = "cutv-onoffswitch-checked";
			}

			if( $inputId == '' ) $inputId = $inputName;
			?>
			<div class = "cutv-onoffswitch <?php echo $isChecked_class; ?>">
				<input type = "checkbox" name = "<?php echo $inputName; ?>" class = "cutv-onoffswitch-checkbox <?php echo $inputClassName; ?>" id = "<?php echo $inputId; ?>" <?php echo $isChecked; ?>>
				<label class = "cutv-onoffswitch-label" for = "<?php echo $inputId; ?>">
				  <span class = "cutv-onoffswitch-inner">
						<span class = "cutv-onoffswitch-active"><span class = "cutv-onoffswitch-switch">ON</span></span>
						<span class = "cutv-onoffswitch-inactive"><span class = "cutv-onoffswitch-switch">OFF</span></span>
				  </span>
				</label>
			</div>

			<?php
		}
	}
	
	/* GET SWITCH BUTTON STATE */
	if( ! function_exists( 'cutv_get_button_state' ) ) {
		function cutv_get_button_state( $val , $invert = FALSE ) {
			if( $invert ) {
				if( $val == 'on' ) return TRUE;
				else return FALSE;
			} else {
				if( $val ) return 'on';
				else return "off";
			}
		}
	}
	
	/* Install new log Mysql Table */
	if( ! function_exists( 'cutv_mysql_install' ) ) {
		function cutv_mysql_install() {
			global $wpdb;
			global $jal_db_version;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$cutv_mysql_installed = get_option( 'cutv_mysql_installed' );
			if( $cutv_mysql_installed == '1' ) {
				//echo "Already installed";
				return FALSE;
			}
			$log_table          = $wpdb->prefix . "cutv_log";
			$existing_log_table = $wpdb->get_var( "show tables like '$log_table' " );
			if( $existing_log_table != $log_table ) {
				$sql
					      = "
				CREATE TABLE IF NOT EXISTS `$log_table` (
				  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
				  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `status` tinytext NOT NULL,
				  `type` tinytext NOT NULL,
				  `object` tinytext NOT NULL,
				  `icon` tinytext NOT NULL,
				  `action` text NOT NULL,
				  `log_msgs` text NOT NULL,
				  UNIQUE KEY `id` (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
				);";
				$creating = dbDelta( $sql );
				//echo "INSTALLING";
				update_option( 'cutv_mysql_installed' , 1 );

				return FALSE;
			}
		}
	}

	/* Show CUTV logo floated on left */
	if( ! function_exists( 'cutv_show_logo' ) ) {
		function cutv_show_logo() {
			?>
			<div class = "cutv_logo">
				<div class = "cutv_logo_img">
					<a href = "<?php echo CUTV_MAIN_URL; ?>" title = "WP Video Robot Website">
						<img src = "<?php echo CUTV_LOGO_SMALL; ?>" alt = "WP Video Robot LOGO"/>
					</a>
				</div>
				<div class = "cutv_logo_links">
					<a target = "_blank" href = "<?php echo CUTV_DOC_URL; ?>" title = "<?php _e( 'Read WP Video Robot Documentation' , CUTV_LANG ); ?>">
						<?php _e( 'Documentation' , CUTV_LANG ); ?>
					</a>|
					<a target = "_blank" href = "<?php echo CUTV_SUPPORT_URL; ?>" title = "<?php _e( 'Need Help ?' , CUTV_LANG ); ?>">
						<?php _e( 'Get Support' , CUTV_LANG ); ?>
					</a>|
					<span class = "cutv_header_version"><strong><?php echo CUTV_VERSION; ?></strong></span>
				</div>
			</div>
			<?php
		}
	}

