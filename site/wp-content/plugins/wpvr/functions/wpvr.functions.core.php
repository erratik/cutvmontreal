<?php


	if( ! function_exists( 'wpvr_render_selectized_field' ) ) {
		function wpvr_render_selectized_field( $field , $value = '' ) {
			$field = wpvr_extend( $field , array(
				'id'          => '' ,
				'name'        => '' ,
				'maxItems'    => '' ,
				'placeholder' => '' ,
				'values'      => array() ,
			) );
			?>
			<div class = "wpvr_select_wrap">
				<input type = "hidden" value = "0" name = "<?php echo $field[ 'name' ]; ?>[]"/>
				<select
					class = "wpvr_field_selectize "
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
	if( ! function_exists( 'wpvr_extend_metaboxes_fields' ) ) {
		function wpvr_extend_metaboxes_fields( $metaboxes = array() , $metabox_id = '' , $additional_fields = array() ) {

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
	if( ! function_exists( 'wpvr_add_custom_metaboxes' ) ) {
		function wpvr_add_custom_metaboxes( $metaboxes = array() , $additional_metaboxes = array() ) {

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
	if( ! function_exists( 'wpvr_duration_to_seconds' ) ) {
		function wpvr_duration_to_seconds( $duration ) {
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
	if( ! function_exists( 'wpvr_filter_videos_found' ) ) {
		function wpvr_filter_videos_found( $videosFound , $options ) {
			return $videosFound;

		}
	}

	/* run DataFillers  */
	if( ! function_exists( 'wpvr_run_dataFillers' ) ) {
		function wpvr_run_dataFillers( $newPostId ) {
			$wpvr_fillers = get_option( 'wpvr_fillers' );
			if( $wpvr_fillers == '' ) $wpvr_fillers = array();

			if( WPVR_ENABLE_DATA_FILLERS === TRUE ) {
				foreach ( $wpvr_fillers as $filler ) {
					if( $filler[ 'from' ] == 'custom_data' ) $data = $filler[ 'from_custom' ];
					else $data = get_post_meta( $newPostId , $filler[ 'from' ] , TRUE );

					$ok = update_post_meta( $newPostId , $filler[ 'to' ] , $data );
				}
			}
		}
	}

	/* Get Video Formated Duration by post id */
	if( ! function_exists( 'wpvr_get_duration' ) ) {
		function wpvr_get_duration( $post_id = '' , $return_seconds = FALSE ) {
			if( $post_id == '' ) {
				global $post;
				$post_id = $post->ID;
			}
			$duration = get_post_meta( $post_id , 'wpvr_video_duration' , TRUE );
			$r        = wpvr_get_duration_string( $duration , $return_seconds );

			return $r;
		}
	}
	
	/* Get Video Formated Duration by post id */
	if( ! function_exists( 'wpvr_get_duration_string' ) ) {
		function wpvr_get_duration_string( $duration = '' , $return_seconds = FALSE ) {
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
	if( ! function_exists( 'wpvr_get_views' ) ) {
		function wpvr_get_views( $post_id = '' ) {
			if( $post_id == '' ) {
				global $post;
				if( ! class_exists( $post ) || ! property_exists( $post , 'ID' ) ) return FALSE;
				$post_id = $post->ID;
			}

			return get_post_meta( $post_id , 'wpvr_video_views' , TRUE );
		}
	}
	
	/* LEt's Start the plugin */
	if( ! function_exists( 'wpvr_start_plugin' ) ) {
		function wpvr_start_plugin( $product_slug = 'wpvr' , $product_version = WPVR_VERSION , $output = FALSE ) {

			$act  = wpvr_get_activation( $product_slug );
			$site = array(
				'version' => $product_version ,
				'url'     => get_bloginfo( 'url' ) ,
				'domain'  => $_SERVER[ 'SERVER_NAME' ] ,
				'ip'      => isset( $_SERVER[ 'SERVER_ADDR' ] ) ? $_SERVER[ 'SERVER_ADDR' ] : '' ,
			);

			if( $act[ 'act_status' ] != '1' ) {
				//Alert
				$alert = wpvr_capi_alert(
					$product_slug ,
					$site[ 'domain' ] ,
					$site[ 'url' ] ,
					$site[ 'ip' ] ,
					$site[ 'version' ]
				);
				//wpvr_set_debug( $alert , true );
				if( $output === TRUE && $alert[ 'status' ] != '1' ) {
					echo $alert[ 'msg' ];
				}
			}
		}
	}
	
	/*Get Videos Views*/
	if( ! function_exists( 'wpvr_get_fields' ) ) {
		function wpvr_get_fields( $field_name = '' , $post_id = '' ) {
			if( $post_id == '' ) {
				global $post;
				if( ! class_exists( $post ) || ! property_exists( $post , 'ID' ) ) return FALSE;
				$post_id = $post->ID;
			}
			$fields = array(
				'video_service'  => get_post_meta( $post_id , 'wpvr_video_service' , TRUE ) ,
				'video_id'       => get_post_meta( $post_id , 'wpvr_video_id' , TRUE ) ,
				'video_duration' => get_post_meta( $post_id , 'wpvr_video_duration' , TRUE ) ,
				'video_url'      => get_post_meta( $post_id , 'wpvr_video_service_url' , TRUE ) ,
				'video_thumb'    => get_post_meta( $post_id , 'wpvr_video_service_icon' , TRUE ) ,
				'video_thumb_hq' => get_post_meta( $post_id , 'wpvr_video_service_thumb' , TRUE ) ,
				'video_views'    => get_post_meta( $post_id , 'wpvr_video_views' , TRUE ) ,
			);
			if( $field_name == '' ) return $fields;
			elseif( array_key_exists( $field_name , $fields ) ) return $fields[ $field_name ];
			else return FALSE;
		}
	}
	
	/* Embed Video Player Manually */
	if( ! function_exists( 'wpvr_embed' ) ) {
		function wpvr_embed( $post_id = '' , $autoplay = FALSE , $echo = TRUE ) {
			if( $post_id == '' ) {
				global $post;
				//if( !class_exists($$post) || !property_exists($post,'ID') ) return false;
				if( ( isset( $post ) && ( $post instanceof WP_Post ) ) || ! property_exists( $post , 'ID' ) ) return FALSE;
				$post_id = $post->ID;
			}
			$wpvr_video_id = get_post_meta( $post_id , 'wpvr_video_id' , TRUE );
			$wpvr_service  = get_post_meta( $post_id , 'wpvr_video_service' , TRUE );

			//new dBug( $wpvr_service );

			$embedCode = '<div class="wpvr_embed">' . wpvr_video_embed( $wpvr_video_id , $post_id , $autoplay , $wpvr_service ) . '</div>';
			if( $echo ) echo $embedCode;
			else return $embedCode;
		}
	}

	/* Check Customer */
	if( ! function_exists( 'wpvr_check_customer' ) ) {
		function wpvr_check_customer() {
			$wpvr_activation = wpvr_get_activation( 'wpvr' );
			//_d( $wpvr_activation );return false;
			if( $wpvr_activation[ 'act_status' ] === 1 ) return FALSE;

			global $wpvr_pages , $wpvr_options;
			if( ! isset( $wpvr_pages ) || ! $wpvr_pages ) return FALSE;

			if( isset( $wpvr_options[ 'purchaseCode' ] ) && $wpvr_options[ 'purchaseCode' ] != '' )
				$wpvr_activation_code = $wpvr_options[ 'purchaseCode' ];
			else
				$wpvr_activation_code = $wpvr_activation[ 'act_code' ];

			$envato_cb = '<div class="pull-right"><input checked="checked" type="checkbox" name="is_envato" value="is_envato" id="is_envato" /><label for="is_envato"> Envato Code </label></div>';


			$af = '';
			$af .= '<div class="wpvr_activation_form">';
			$af .= '	<input type="hidden" id="wpvr_activation_id" value="' . $wpvr_activation[ 'act_id' ] . '" />';
			$af .= '	<p>' . addslashes( __( 'Please activate your licence of WP Video Robot' , WPVR_LANG ) ) . '.</p>';
			$af .= '	<label>' . addslashes( __( 'Your Email' , WPVR_LANG ) ) . '</label><br/>';
			$af .= '	<input type="text" id="wpvr_user_email" class="wpvr_aform_input" value="' . $wpvr_activation[ 'act_email' ] . '" placeholder="" />';
			$af .= '	<br/><br/>';
			$af .= '	<label>' . addslashes( __( 'Your Purchase Code' , WPVR_LANG ) ) . '</label>' . $envato_cb . '<br/>';
			$af .= '	<input type="text" id="wpvr_user_code" class="wpvr_aform_input" value="' . $wpvr_activation_code . '" placeholder="" /><br/>';
			$af .= '	<span class="pull-right">';
			$af .= '		<a class="link" target="_blank" href="' . WPVR_SUPPORT_URL . '/tutorials/where-to-find-my-envato-purchase-code/" title="Click here">';
			$af .= '			' . addslashes( __( 'WHERE TO FIND MY ENVATO PURCHASE CODE' , WPVR_LANG ) ) . '';
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
						var activationBox = wpvr_show_loading({
							title: 'WP VIDEO ROBOT ACTIVATION',
							text: '<?php echo $activation_form; ?>',
							isModal: true,
							boxClass: 'activationBox',
							pauseButton: '<i class="fa fa-unlock" ></i> <?php echo addslashes( __( 'ACTIVATE MY COPY' , WPVR_LANG ) ); ?>',
							cancelButton: '<a href="<?php echo WPVR_CC_PAGE_URL; ?>" target="_blank"><i class="fa fa-shopping-cart" ></i><?php echo addslashes( __( 'BUY WP VIDEO ROBOT' , WPVR_LANG ) ); ?></a>',
						});

						activationBox.doPause(function () {
							var btn = $('.wpvr_loading_pause', activationBox);
							var spinner = wpvr_add_loading_spinner(btn, 'pull-right');
							var error_msg = "<?php echo addslashes( __( 'Please enter a valid email and your Purchase Code.' , WPVR_LANG ) ) . ''; ?>";
							var url = '<?php echo WPVR_ACTIONS_URL; ?>';
							var plugin_dashboard_url = '<?php echo admin_url( 'admin.php?page=wpvr-welcome' , 'http' ); ?>';
							var icon_error = '<i style="margin-right:10px;font-size:20px;line-height:20px;" class="fa fa-exclamation-circle"></i>';

							var email = jQuery('#wpvr_user_email').val();
							var code = jQuery('#wpvr_user_code').val();
							var id = jQuery('#wpvr_activation_id').val();
							var is_envato = jQuery('#is_envato').prop('checked');
							if (is_envato) is_envato = 1;
							else is_envato = 0;
							var ok = true;
							if (!wpvr_validate_email(email)) {
								jQuery('#wpvr_user_email').addClass('error');
								ok = false;
							} else {
								jQuery('#wpvr_user_email').removeClass('error');
							}

							if (code == '') {
								jQuery('#wpvr_user_code').addClass('error');
								ok = false;
							} else {
								jQuery('#wpvr_user_code').removeClass('error');
							}

							if (!ok) {
								wpvr_remove_loading_spinner(spinner);
								jQuery('.wpvr_aform_result').html('<div class="werror">' + icon_error + error_msg + '</div>');
								return false;

							} else {
								jQuery('.wpvr_aform_result').html('<div class="wwait"><i class = "fa fa-cog fa-spin"></i> Please Wait ...</div>');
								jQuery.ajax({
									type: "POST",
									url: url + '?activate_copy&wpvr_wpload',
									data: {
										email: email,
										code: code,
										id: id,
										is_envato: is_envato,
									},
									success: function (data) {
										wpvr_remove_loading_spinner(spinner);
										var $data = wpvr_get_json(data);
										if ($data.status == '1') {
											activationBox.doHide();
											var activationBoxEnd = wpvr_show_loading({
												title: 'WP VIDEO ROBOT ACTIVATION',
												text: $data.msg,
												isModal: false,
												pauseButton: wpvr_localize.ok_button,
											});
											activationBoxEnd.doPause(function () {
												activationBoxEnd.remove();
												window.location.href = plugin_dashboard_url;
											});

										} else {
											wpvr_remove_loading_spinner(spinner);
											jQuery('.wpvr_aform_result').html('<div class="werror">' + icon_error + $data.msg + '</div>');
										}
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(thrownError);
										wpvr_remove_loading_spinner(spinner);
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
	if( ! function_exists( 'wpvr_add_log' ) ) {
		function wpvr_add_log( $log_data = array() ) {
			global $wpdb;
			if( ! isset( $log_data[ 'status' ] ) ) $log_data[ 'status' ] = '';
			if( ! isset( $log_data[ 'icon' ] ) ) $log_data[ 'icon' ] = '';
			$log_table        = $wpdb->prefix . "wpvr_log";
			$current_user     = wp_get_current_user();
			$current_username = $current_user->user_login;
			if( $current_username != '' ) {
				$executed_by = '<b>' . __( 'Executed by :' , WPVR_LANG ) . ' </b>' . $current_username;
			} else {
				$executed_by = '<b>' . __( 'Executed by :' , WPVR_LANG ) . ' </b> CRON';
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
	if( ! function_exists( 'wpvr_make_switch_button' ) ) {
		function wpvr_make_switch_button( $inputName , $inputState = FALSE , $inputClassName = '' , $inputId = '' ) {
			if( $inputState == FALSE ) {
				$isChecked       = "";
				$isChecked_class = "";
			} else {
				$isChecked       = " checked ";
				$isChecked_class = "wpvr-onoffswitch-checked";
			}

			if( $inputId == '' ) $inputId = $inputName;
			?>
			<div class = "wpvr-onoffswitch <?php echo $isChecked_class; ?>">
				<input type = "checkbox" name = "<?php echo $inputName; ?>" class = "wpvr-onoffswitch-checkbox <?php echo $inputClassName; ?>" id = "<?php echo $inputId; ?>" <?php echo $isChecked; ?>>
				<label class = "wpvr-onoffswitch-label" for = "<?php echo $inputId; ?>">
				  <span class = "wpvr-onoffswitch-inner">
						<span class = "wpvr-onoffswitch-active"><span class = "wpvr-onoffswitch-switch">ON</span></span>
						<span class = "wpvr-onoffswitch-inactive"><span class = "wpvr-onoffswitch-switch">OFF</span></span>
				  </span>
				</label>
			</div>

			<?php
		}
	}
	
	/* GET SWITCH BUTTON STATE */
	if( ! function_exists( 'wpvr_get_button_state' ) ) {
		function wpvr_get_button_state( $val , $invert = FALSE ) {
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
	if( ! function_exists( 'wpvr_mysql_install' ) ) {
		function wpvr_mysql_install() {
			global $wpdb;
			global $jal_db_version;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$wpvr_mysql_installed = get_option( 'wpvr_mysql_installed' );
			if( $wpvr_mysql_installed == '1' ) {
				//echo "Already installed";
				return FALSE;
			}
			$log_table          = $wpdb->prefix . "wpvr_log";
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
				update_option( 'wpvr_mysql_installed' , 1 );

				return FALSE;
			}
		}
	}

	/* Show WPVR logo floated on left */
	if( ! function_exists( 'wpvr_show_logo' ) ) {
		function wpvr_show_logo() {
			?>
			<div class = "wpvr_logo">
				<div class = "wpvr_logo_img">
					<a href = "<?php echo WPVR_MAIN_URL; ?>" title = "WP Video Robot Website">
						<img src = "<?php echo WPVR_LOGO_SMALL; ?>" alt = "WP Video Robot LOGO"/>
					</a>
				</div>
				<div class = "wpvr_logo_links">
					<a target = "_blank" href = "<?php echo WPVR_DOC_URL; ?>" title = "<?php _e( 'Read WP Video Robot Documentation' , WPVR_LANG ); ?>">
						<?php _e( 'Documentation' , WPVR_LANG ); ?>
					</a>|
					<a target = "_blank" href = "<?php echo WPVR_SUPPORT_URL; ?>" title = "<?php _e( 'Need Help ?' , WPVR_LANG ); ?>">
						<?php _e( 'Get Support' , WPVR_LANG ); ?>
					</a>|
					<span class = "wpvr_header_version"><strong><?php echo WPVR_VERSION; ?></strong></span>
				</div>
			</div>
			<?php
		}
	}

