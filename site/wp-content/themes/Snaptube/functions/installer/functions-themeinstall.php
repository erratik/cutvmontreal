<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $pagenow;

/**
* ----------------------------------------------------------------------
* Theme has been just activated
*/

if ( is_admin() && $pagenow == "themes.php" ) {
	// Update theme option '_basic_config_done'
	// if URL has ?basic_setup=completed variable set
	if ( isset($_GET['basic_setup']) && $_GET['basic_setup'] == 'completed' ) {
		update_option( SHORTNAME . '_basic_config_done', true);
		define ('LBMN_THEME_CONFUGRATED', true);
	}

	// Update theme option '_basic_config_done'
	// if URL has ?demoimport=completed variable set
	if ( isset($_GET['demoimport']) && $_GET['demoimport'] == 'completed' ) {
		update_option( SHORTNAME . '_democontent_imported', true);
	}

	if ( !get_option( SHORTNAME . '_hide_quicksetup' ) ) {
		add_action( 'admin_footer', 'lbmn_setmessage_themeinstall' );
	}
}

/**
 * ----------------------------------------------------------------------
 * Check if required plugins were manually installed
 */

// function lbmn_required_plugins_install_check() {
// 	$current_tgmpa_messages = get_settings_errors('tgmpa');

// 	if ( ! get_option( SHORTNAME . '_required_plugins_installed' ) ) {
// 	// Proceed only if '_required_plugins_installed' not already market as true

// 		$current_tgmpa_message = '';
// 		$current_tgmpa_messages = get_settings_errors('tgmpa_required');

// 		foreach ($current_tgmpa_messages as $message) {
// 			$current_tgmpa_message = $message['message'];
// 		}

// 		// If message has no link to install-required-plugins page then all
// 		// required plugins has been installed
// 		if ( $current_tgmpa_message != 'not_installed' ) {
// 			// Update theme option '_required_plugins_installed'
// 			update_option( SHORTNAME . '_required_plugins_installed', true);
// 		}
// 	}
// }
// add_action( 'admin_footer', 'lbmn_required_plugins_install_check' );
// get_settings_errors() do not return any results earlier than 'admin_footer'


/**
 * ----------------------------------------------------------------------
 * Output Theme Installer HTML
 */

function lbmn_setmessage_themeinstall() {
?>
<img src="<?php echo includes_url() . 'images/spinner.gif' ?>" class="theme-installer-spinner" style="position:fixed; left:50%; top:50%;" />
<style type="text/css">.vhman-message.quick-setup{display:none;}</style>
<div class="updated vhman-message quick-setup">
	<div class="message-container">
	<p class="before-header"><?php echo THEMENAME; ?> Quick Setup</p>
	<h4>Thank you for creating with <a href="<?php echo VH_DEVELOPER_URL; ?>" target="_blank"><?php echo VH_DEVELOPER_NAME_DISPLAY; ?></a>!</h4>
	<h5>Just a few steps left to release the full power of our theme.</h5>

	<!-- Step 1 -->
		<?php
			// Check is this step is already done
			if ( !get_option( SHORTNAME . '_required_plugins_installed') ) {
				echo '<p id="theme-setup-step-1" class="submit step-plugins">';
			} else {
				echo '<p id="theme-setup-step-1" class="submit step-plugins step-completed">';
			}
		?>
		<span class="step"><span class="number">1</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />

		<span class="step-body"><a href="<?php echo esc_url( add_query_arg( array('page' => 'install-required-plugins'), admin_url('themes.php') ) ); ?>" class="button button-primary" id="do_plugins-install">Install required plugins</a>
		<span class="step-description">
		Required action to get 100% functionality.<br />
		Installs Page Builder, Snatube functionality, etc.
		</span></span><br />
		<span class="error" style="display:none">Automatic plugin installation failed. Please try to <a href="/wp-admin/themes.php?page=install-required-plugins">install required plugins manually</a>.</span>
		</p>

	<!-- Step 2 -->

		<?php
			// Check is this step is already done
			if ( !get_option( SHORTNAME . '_democontent_imported') ) {
				echo '<p id="theme-setup-step-2" class="submit step-demoimport">';
			} else {
				echo '<p id="theme-setup-step-2" class="submit step-demoimport step-completed">';
			}
		?>
		<span class="step"><span class="number">2</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />
		<span class="step-body">
		<a href="#" class="button button-primary" id="do_demo-import">Import all demo content</a>
		<span class="step-description">
		Optional step to recreate theme demo website<br />
		on your server.
		</span></span><br />
		<span class="error" style="display:none">Something went wrong (<a href="#" class="show-error-log">show log</a>).</span>
		</p>

	<!-- Step 3 -->
		<p class="submit step-tour">
		<span class="step"><span class="number">3</span></span> 
		<span class="step-body">
			<a href="<?php echo esc_url( add_query_arg('theme_tour', 'true', admin_url('themes.php') ) ); ?>" class="button  button-primary">Take a quick tour</a> 
			<span class="step-description">2 minutes interactive introduction<br /> 
			to our theme basic controls.  </span>
		</span>
		</p>


	<p class="submit action-skip"> <a class="skip button-primary" href="<?php echo esc_url( add_query_arg('hide_quicksetup', 'true', admin_url('themes.php') ) ); ?>">Hide this message</a></p></div>
</div>
<style type="text/css">.theme-installer-spinner{display:none;}</style>
<style type="text/css">.vhman-message.quick-setup{display:block;}</style>
<?php
}

/**
* ----------------------------------------------------------------------
* Start basic theme settings setup process
*/
add_action( 'admin_notices', 'pvt_wordpress_content_importer' );
function pvt_wordpress_content_importer() {
	$theme_dir = get_template_directory();

	if ( is_admin() && isset($_GET['importcontent']) ) {
		if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				include $class_wp_importer;
			}
		}
		if ( ! class_exists('pvt_WP_Import') ) {
			$class_wp_import = $theme_dir . '/functions/installer/importer/wordpress-importer.php';
			if ( file_exists( $class_wp_import ) ) {
				include $class_wp_import;
			}
		}
		if ( class_exists( 'WP_Importer' ) && class_exists( 'pvt_WP_Import' ) ) {
			$importer = new pvt_WP_Import();
			$files_to_import = array();

			// Live Composer has links to images hard-coded, so before importing
			// media we need to check that the Settings > Media >
			// 'Organize my uploads into month- and year-based folders' unchecked
			// as on demo server. After import is done we set back original state
			// of this setting.

			if ( $_GET['importcontent'] == 'alldemocontent' ) {
				$import_path = $theme_dir . '/functions/installer/';
				$files_to_import[] = $import_path . 'import.xml';
			}

			// Start Import

			if ( file_exists( $class_wp_importer ) ) {
				// Import included images
				$importer->fetch_attachments = true;

				foreach ($files_to_import as $import_file) {
					if( is_file($import_file) ) {
						ob_start();
							$importer->import( $import_file );

							$log = ob_get_contents();
						ob_end_clean();

						// output log in the hidden div
						echo '<div class="ajax-log">';
						echo $log;
						echo '</div>';


						if ( stristr($log, 'error') || !stristr($log, 'All done.') ) {
							// Set marker div that will be fildered by ajax request
							echo '<div class="ajax-request-error"></div>';

							// output log in the div
							echo '<div class="ajax-error-log">';
							echo $log;
							echo '</div>';
						}

					} else {
						// Set marker div that will be fildered by ajax request
						echo '<div class="ajax-request-error"></div>';

						// output log in the div
						echo '<div class="ajax-error-log">';
						echo "Can't open file: " . $import_file . "</ br>";
						echo '</div>';
					}
				}

			} else {
				// Set marker div that will be fildered by ajax request
				echo '<div class="ajax-request-error"></div>';

				// output log in the div
				echo '<div class="ajax-error-log">';
				echo "Failed to load: " . $class_wp_import . "</ br>";
				echo '</div>';
			}
		}

		/**
		 * ----------------------------------------------------------------------
		 * Demo Content: Full
		 */

		if ( $_GET['importcontent'] == 'alldemocontent' ) {
			$import_path = $theme_dir . '/functions/installer/';

			// 1: Assign menus
			$locations = get_nav_menu_locations();

			$term = get_term_by('name', 'Side menu', 'nav_menu');
			$menu_id_primary = $term->term_id;

			// check if 'primary-menu' locaiton has no menu assigned
			if( !has_nav_menu('primary-menu') && isset($menu_id_primary) ) {
				// Attach saved before menu id to 'topbar' location
				$locations = get_nav_menu_locations();
				$locations['primary-menu'] = $menu_id_primary;
				set_theme_mod('nav_menu_locations', $locations);
			}


			$term = get_term_by('name', 'Footer menu', 'nav_menu');
			$menu_id_footer = $term->term_id;

			// check if 'footer-menu' locaiton has no menu assigned
			if( !has_nav_menu('footer-menu') && isset($menu_id_footer) ) {
				// Attach saved before menu id to 'topbar' location
				$locations = get_nav_menu_locations();
				$locations['footer-menu'] = $menu_id_footer;
				set_theme_mod('nav_menu_locations', $locations);
			}

			// 2: Import widgets
			$files_with_widgets_to_import = array();
			$files_with_widgets_to_import[] = $import_path . 'widgets.wie';

			foreach ($files_with_widgets_to_import as $file) {
				pvt_import_data( $file );
			}

			// 3: Use a static front page
			$home_page = get_page_by_title( VH_HOME_TITLE );
			update_option( 'page_on_front', $home_page->ID );
			update_option( 'show_on_front', 'page' );

			// 4: Import videos
			vh_import_videos();

			// 5: Use a static front page
			$home_page = get_page_by_title( VH_HOME_TITLE );
			update_option( 'page_on_front', $home_page->ID );
			update_option( 'show_on_front', 'page' );

		} // if $_GET['importcontent']

	} // is isset($_GET['importcontent'])
}

/**
* ----------------------------------------------------------------------
* Start a theme tour
*/

if ( is_admin() && isset($_GET['theme_tour'] ) && $pagenow == "themes.php" ) {
	// Register the pointer styles and scripts
	add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

	// Add pointer javascript
	add_action( 'admin_print_footer_scripts', 'add_pointer_scripts' );

	// enqueue javascripts and styles
	function enqueue_scripts()
	{
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	// Add the pointer javascript
	function add_pointer_scripts()
	{
		$pointer_content = '<h3>We use a theme options</h3>';
		$pointer_content .= '<p>Most of theme options are available for customization in theme options page.</p>';
	?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('#menu-appearance a[href="themes.php?page=themeoptions"]').pointer({
				// pointer_id: 'customizer_menu_link',
				content: '<?php echo $pointer_content; ?>',
				position: {
					 edge: 'left', //top, bottom, left, right
					 align: 'middle' //top, bottom, left, right, middle
				 },
				buttons: function( event, t ) {

					var $buttonClose = jQuery('<a class="button-secondary" style="margin-right:10px;" href="#">End Tour</a>');
					$buttonClose.bind( 'click.pointer', function() {

						t.element.pointer('close');
					});

					var buttons = $('<div class="tiptour-buttons">');
					buttons.append($buttonClose);
					buttons.append('<a class="button-primary" style="margin-right:10px;" href="<?php echo admin_url('themes.php?page=themeoptions#first-time-visit'); ?>">Go to Theme Options</a>');
					return buttons;
				},

				close: function() {
					// Once the close button is hit
					$.post( ajaxurl, {
						pointer: 'customizer_menu_link',
						action: 'dismiss-wp-pointer'
					});
				}
			}).pointer('open');

			$(".vhman-message.quick-setup .step-tour").addClass("step-completed");
		});
		//]]>
		</script>
	<?php
	}
	update_option( SHORTNAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}

/* Hide quick tour message block */
if ( is_admin() && isset($_GET['hide_quicksetup'] ) && $pagenow == "themes.php" ) {
	update_option( SHORTNAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}