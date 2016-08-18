<?php
	
	/* Function to declare PHP is too old ! */
	add_action( 'admin_notices' , 'cutv__show_php_too_old' );
	function cutv__show_php_too_old() {
		if( version_compare( PHP_VERSION , cutv__REQUIRED_PHP_VERSION , '<' ) ) {
			$php_version = explode( '+' , PHP_VERSION );
			?>
			<div class = "error">
				<p>
					<strong>WP Video Robot ERROR</strong><br/>
					<?php echo __( 'You are using PHP version ' , cutv__LANG ) . $php_version[ 0 ]; ?>.<br/>
					<?php printf( __( 'WP Video Robot needs version %s at least to work properly.' , cutv__LANG ) , cutv__REQUIRED_PHP_VERSION ); ?><br/>
					<?php echo __( 'Please upgrade PHP.' , cutv__LANG ); ?>
				</p>
			</div>
			<?php
		}
	}
	
	/* Function to show error message if cron not writable */
	add_action( 'admin_notices' , 'cutv__cron_file_permission_issue' );
	function cutv__cron_file_permission_issue() {
		$f = cutv__PATH . 'assets/php/cron.txt';
		if( is_writable( $f ) === FALSE ) {
			?>
			<div class = "error">
				<p>
					<strong>WP Video Robot ERROR</strong><br/>
					<?php echo __( 'The plugin cannot work automatically.' , cutv__LANG ); ?>
					<?php echo __( 'Please, make sure this file is writable :' , cutv__LANG ); ?>
					<strong><?php echo $f; ?></strong><br/>
					<?php echo __( 'If you cannot do that, contact your host.' , cutv__LANG ); ?>
				
				</p>
			</div>
			<?php
		}
	}
	
	/* Function to show cutv_ NOtices */
	add_action( 'admin_notices' , 'cutv__show_notices' );
	function cutv__show_notices() {
		$cutv__notices = get_option( 'cutv__notices' );
		if( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'do-plugin-upgrade' ) {
			return FALSE;
		}
		if( $cutv__notices == '' ) {
			return FALSE;
		}
		//d( $cutv__notices );
		foreach ( (array) $cutv__notices as $notice ) {
			if( ! isset( $notice[ 'is_manual' ] ) || $notice[ 'is_manual' ] === FALSE ) {
				cutv__render_notice( $notice );
			}
		}
	}

	/* Function to show cutv_ NOtices */
	add_action( 'admin_notices' , 'cutv__show_multisite_notices' );
	function cutv__show_multisite_notices() {
		if( !is_multisite() ) return FALSE;
		$cutv__notices = get_site_option( 'cutv__notices' );
		if( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'do-plugin-upgrade' ) {
			return FALSE;
		}
		if( $cutv__notices == '' ) {
			return FALSE;
		}
		//d( $cutv__notices );
		foreach ( (array) $cutv__notices as $notice ) {
			if( ! isset( $notice[ 'is_manual' ] ) || $notice[ 'is_manual' ] === FALSE ) {
				cutv__render_notice( $notice );
			}
		}
	}

	
	/* Function to show demo message */
	add_action( 'admin_notices' , 'cutv__show_demo_message' );
	function cutv__show_demo_message() {
		if( cutv__IS_DEMO ) {
			global $current_user;
			$user_id = $current_user->ID;
			/* Check that the user hasn't already clicked to ignore the message */
			if( ! get_user_meta( $user_id , 'cutv__show_demo_notice' ) ) {
				global $cutv__options;
				$hideLink = "?cutv__show_demo_notice=0";
				foreach ( $_GET as $key => $value ) {
					$hideLink .= "&$key=$value";
				}
				?>
				<div class = "updated">
					<div class = "cutv__demo_notice">
						<a class = "pull-right" href = "<?php echo $hideLink; ?>"><?php _e( 'Hide this notice' , cutv__LANG ); ?></a>
						
						<strong>WELCOME TO THE LIVE DEMO OF WP VIDEO ROBOT v<?php echo cutv__VERSION; ?></strong><br/><br/>
						
						<div class = "cutv__demo_notice_left">
							<i class = "fa fa-smile-o"></i>
						</div>
						<div class = "cutv__demo_notice_right">
							Feel free to play around with the options, add sources, test them and even schedule them to get a feel for how the plugin works. <br/>Don't forget to check this demo
							<a class = "cutv__notice_button" href = "<?php echo cutv__SITE_URL; ?>">FrontEnd</a> to see how do your imported videos render.
							<br/><b>You can also check out our several frontend demo sites <a class = "cutv__notice_button" href = "<?php echo cutv__DEMOS_URL; ?>" title = "FRONT END DEMOS">here</a></b>.
							<br/>The contents of the demo site is reset once a week.
						</div>
					</div>
					
					<div class = "cutv__clearfix"></div>
				</div>
				<?php
			}
		}
	}
	
	/* Display message to adapt old data */
	add_action( 'admin_notices' , 'cutv__adapt_check_imported' );
	function cutv__adapt_check_imported() {
		global $cutv__imported;
		$cutv__actions_url = admin_url( 'admin.php?page=cutv_&update_imported' , 'http' );
		//new dBug( $cutv__imported );
		if( isset( $_GET[ 'update_imported' ] ) ) {
			return FALSE;
		}
		if( $cutv__imported === FALSE ) {
			return FALSE;
		}
		if( $cutv__imported == '' || ! is_array( $cutv__imported ) ) {
			
			$notice = array();
			//d( $cutv__imported );
			
			?>
			<div class = "error warning cutv__wp_notice" style = "display:none;">
				<div>
					<b><?php _e( 'WP Video Robot WARNING' , cutv__LANG ); ?></b> : <br/>
					<?php _e( 'Looks like the anti duplicates filter is OFF.' , cutv__LANG ); ?>
					<br/>
					<a href = "<?php echo $cutv__actions_url; ?>">
						<?php echo __( 'Click here to turn it ON' , cutv__LANG ); ?>.
					</a>
				</div>
			
			</div>
			
			<?php
		}
	}
	
	/* Display message to adapt old data */
	add_action( 'admin_notices' , 'cutv__adapt_old_data_reminder' );
	function cutv__adapt_old_data_reminder() {
		if( isset( $_GET[ 'adapt_old_data' ] ) ) {
			return FALSE;
		}
		$cutv__actions_url = admin_url( 'admin.php?page=cutv_&adapt_old_data' , 'http' );
		$cutv__is_adapted  = get_option( 'cutv__is_adapted' );
		
		//new dBug( $cutv__is_adapted );
		
		if( $cutv__is_adapted != cutv__VERSION ) {
			global $wpdb;
			
			$sql_videos
				= "
                    SELECT
                        count(*)
                    FROM
                        $wpdb->posts P
                    WHERE P.ID IN(
                        SELECT
                            P.ID
                        FROM
                            $wpdb->posts P
                            INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
                        WHERE
                            P.post_type = '" . cutv__VIDEO_TYPE . "'
                            AND post_status != 'auto-draft'
                            AND M.meta_key = 'cutv__video_plugin_version'
                            AND M.meta_value < '" . cutv__VERSION . "'
                    )
                ";
			$sql_sources
				= "
                    SELECT
                        count(*)
                    FROM
                        $wpdb->posts P
                    WHERE P.ID IN(
                        SELECT
                            P.ID
                        FROM
                            $wpdb->posts P
                            INNER JOIN $wpdb->postmeta M ON P.ID = M.post_id
                        WHERE
                            P.post_type = '" . cutv__SOURCE_TYPE . "'
                            AND post_status != 'auto-draft'
                            AND M.meta_key = 'cutv__source_plugin_version'
                            AND M.meta_value < '" . cutv__VERSION . "'
                    )
                ";
			
			//$items = $wpdb->get_results( $sql_sources , OBJECT);
			//new dBug( $items );
			$count_sources = $wpdb->get_var( $sql_sources );
			$count_videos  = $wpdb->get_var( $sql_videos );
			
			if( $count_videos != 0 || $count_sources != 0 ) {
				$info_notice = array(
					'title'   => __( 'WP Video Robot WARNING' , cutv__LANG ) ,
					'class'   => 'warning' , //updated or warning or error
					'content' => '' .
					             __( 'Looks like you have some sources and videos from an older version of the plugin.' , cutv__LANG ) .
					             '<br/>' .
					             '<a href = "' . $cutv__actions_url . '">' .
					             __( 'Click here to adapt them to this new version' , cutv__LANG ) . ' ( ' . cutv__VERSION . ' )' .
					             '</a>'
					,
					'hidable' => FALSE ,
					'color'   => '#999' ,
					'icon'    => 'fa-info-circle' ,
				);
				cutv__render_notice( $info_notice );
			} else {
				update_option( 'cutv__is_adapted' , cutv__VERSION );
			}
		}
	}