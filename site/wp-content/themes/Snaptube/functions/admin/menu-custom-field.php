<?php

require_once(VH_ADMIN . '/slt-file-select.php');

// Saves new field to postmeta for navigation
add_action('wp_update_nav_menu_item', 'custom_nav_update',10, 3);
function custom_nav_update($menu_id, $menu_item_db_id, $args ) {
	if ( isset($_REQUEST['menu-item-custom']) && is_array($_REQUEST['menu-item-custom']) ) {
		$custom_value = $_REQUEST['menu-item-custom'][$menu_item_db_id];
		update_post_meta( $menu_item_db_id, '_menu_item_custom', $custom_value );
	}
}

// Adds value of new field to $item object that will be passed to Walker_Nav_Menu_Edit_Custom
add_filter( 'wp_setup_nav_menu_item','custom_nav_item' );
function custom_nav_item($menu_item) {
	$menu_item->custom = get_post_meta( $menu_item->ID, '_menu_item_custom', true );
	return $menu_item;
}

add_filter( 'wp_edit_nav_menu_walker', 'custom_nav_edit_walker', 10, 2 );
function custom_nav_edit_walker($walker,$menu_id) {
	return 'Walker_Nav_Menu_Edit_Custom';
}

/**
 * Copied from Walker_Nav_Menu_Edit class in core
 *
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu {

/**
 * @see Walker_Nav_Menu::start_lvl()
 * @since 3.0.0
 *
 * @param string $output Passed by reference.
 */
function start_lvl(&$output, $depth = 0, $args = array()) {

}

/**
 * @see Walker_Nav_Menu::end_lvl()
 * @since 3.0.0
 *
 * @param string $output Passed by reference.
 */
function end_lvl(&$output, $depth = 0, $args = array()) {

}

/**
 * @see Walker::start_el()
 * @since 3.0.0
 *
 * @param string $output Passed by reference. Used to append additional content.
 * @param object $item Menu item data object.
 * @param int $depth Depth of menu item. Used for padding.
 * @param object $args
 */
function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id=0) {
	global $_wp_nav_menu_max_depth;
	$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

	$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

	ob_start();
	$item_id = esc_attr( $item->ID );
	$removed_args = array(
		'action',
		'customlink-tab',
		'edit-menu-item',
		'menu-item',
		'page-tab',
		'_wpnonce',
	);

	$original_title = '';
	if ( 'taxonomy' == $item->type ) {
		$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
		if ( is_wp_error( $original_title ) )
			$original_title = false;
	} elseif ( 'post_type' == $item->type ) {
		$original_object = get_post( $item->object_id );
		$original_title = $original_object->post_title;
	}

	$classes = array(
		'menu-item menu-item-depth-' . $depth,
		'menu-item-' . esc_attr( $item->object ),
		'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
	);

	$title = $item->title;

	if ( ! empty( $item->_invalid ) ) {
		$classes[] = 'menu-item-invalid';

		// translators: %s: title of menu item which is invalid
		$title = sprintf( '%s ' . __( '(Invalid)', 'vh' ), $item->title );
	} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
		$classes[] = 'pending';

		// translators: %s: title of menu item in draft status
		$title = sprintf( '%s ' . __('(Pending)', 'vh'), $item->title );
	}

	$title = empty( $item->label ) ? $title : $item->label;

	?>
	<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
		<dl class="menu-item-bar">
			<dt class="menu-item-handle">
				<span class="item-title"><?php echo esc_html( $title ); ?></span>
				<span class="item-controls">
					<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
					<span class="item-order hide-if-js">
						<a href="<?php
							echo wp_nonce_url(
								esc_url( add_query_arg(
									array(
										'action' => 'move-up-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
								) ),
								'move-menu_item'
							);
						?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
						|
						<a href="<?php
							echo wp_nonce_url(
								esc_url( add_query_arg(
									array(
										'action' => 'move-down-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
								) ),
								'move-menu_item'
							);
						?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
					</span>
					<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
						echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : esc_url( add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) ) );
					?>"><?php _e( 'Edit Menu Item', 'vh' ); ?></a>
				</span>
			</dt>
		</dl>

		<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
			<?php if( 'custom' == $item->type ) : ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo $item_id; ?>">
						<?php _e( 'URL', 'vh' ); ?><br />
						<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
					</label>
				</p>
			<?php endif; ?>
			<p class="description description-thin">
				<label for="edit-menu-item-title-<?php echo $item_id; ?>">
					<?php _e( 'Navigation Label', 'vh' ); ?><br />
					<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
				</label>
			</p>
			<p class="description description-thin">
				<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
					<?php _e( 'Title Attribute', 'vh' ); ?><br />
					<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
				</label>
			</p>
			<p class="field-link-target description">
				<label for="edit-menu-item-target-<?php echo $item_id; ?>">
					<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
					<?php _e( 'Open link in a new window/tab', 'vh' ); ?>
				</label>
			</p>
			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
					<?php _e( 'CSS Classes (optional)', 'vh' ); ?><br />
					<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
				</label>
			</p>
			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
					<?php _e( 'Link Relationship (XFN)', 'vh' ); ?><br />
					<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
				</label>
			</p>
			<p class="field-description description description-wide">
				<label for="edit-menu-item-description-<?php echo $item_id; ?>">
					<?php _e( 'Description', 'vh' ); ?><br />
					<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
					<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.', 'vh'); ?></span>
				</label>
			</p>
			<?php
			// This is the added field
			?>
			<p class="field-custom description description-wide">
				<label for="edit-menu-item-custom-<?php echo $item_id; ?>">
					<?php _e( 'Menu icon', 'vh' ); ?><br />
				</label>
			</p>
			<div class="dialog_container">
				<div id="menu_icon_dialog" title="Select your menu icon">
					<?php 
					$font_array = array('micon-glass', 'micon-lemon', 'micon-search', 'micon-mail', 'micon-mail-alt', 'micon-heart', 
						'micon-heart-empty', 'micon-star', 'micon-star-empty', 'micon-star-half', 'micon-star-half-alt', 'micon-user', 
						'micon-users', 'micon-male', 'micon-female', 'micon-video', 'micon-videocam', 'micon-picture', 'micon-camera', 
						'micon-camera-alt', 'micon-th-large', 'micon-th', 'micon-th-list', 'micon-ok', 'micon-ok-circled', 
						'micon-ok-circled2', 'micon-ok-squared', 'micon-cancel', 'micon-cancel-circled', 'micon-cancel-circled2', 
						'micon-plus', 'micon-plus-circled', 'micon-plus-squared', 'micon-plus-squared-alt', 'micon-minus', 
						'micon-minus-circled', 'micon-minus-squared', 'micon-minus-squared-alt', 'micon-help', 'micon-help-circled', 
						'micon-info-circled', 'micon-info', 'micon-home', 'micon-link', 'micon-unlink', 'micon-link-ext', 
						'micon-link-ext-alt', 'micon-attach', 'micon-lock', 'micon-lock-open', 'micon-lock-open-alt', 'micon-pin', 
						'micon-eye', 'micon-eye-off', 'micon-tag', 'micon-tags', 'micon-bookmark', 'micon-bookmark-empty', 'micon-flag', 
						'micon-flag-empty', 'micon-flag-checkered', 'micon-thumbs-up', 'micon-thumbs-down', 'micon-thumbs-up-alt', 
						'micon-thumbs-down-alt', 'micon-download', 'micon-upload', 'micon-download-cloud', 'micon-upload-cloud', 
						'micon-reply', 'micon-reply-all', 'micon-forward', 'micon-quote-left', 'micon-quote-right', 'micon-code', 
						'micon-export', 'micon-export-alt', 'micon-pencil', 'micon-pencil-squared', 'micon-edit', 'micon-print', 
						'micon-retweet', 'micon-keyboard', 'micon-gamepad', 'micon-comment', 'micon-chat', 'micon-comment-empty', 
						'micon-chat-empty', 'micon-bell', 'micon-bell-alt', 'micon-attention-alt', 'micon-attention', 
						'micon-attention-circled', 'micon-location', 'micon-direction', 'micon-compass', 'micon-trash', 'micon-doc', 
						'micon-docs', 'micon-doc-text', 'micon-doc-inv', 'micon-doc-text-inv', 'micon-folder', 'micon-folder-open', 
						'micon-folder-empty', 'micon-folder-open-empty', 'micon-box', 'micon-rss', 'micon-rss-squared', 'micon-phone', 
						'micon-phone-squared', 'micon-menu', 'micon-cog', 'micon-cog-alt', 'micon-wrench', 'micon-basket', 'micon-calendar', 
						'micon-calendar-empty', 'micon-login', 'micon-logout', 'micon-mic', 'micon-mute', 'micon-volume-off', 
						'micon-volume-down', 'micon-volume-up', 'micon-headphones', 'micon-clock', 'micon-lightbulb', 'micon-block', 
						'micon-resize-full', 'micon-resize-full-alt', 'micon-resize-small', 'micon-resize-vertical', 
						'micon-resize-horizontal', 'micon-move', 'micon-zoom-in', 'micon-zoom-out', 'micon-down-circled2', 
						'micon-up-circled2', 'micon-left-circled2', 'micon-right-circled2', 'micon-down-dir', 'micon-up-dir', 
						'micon-left-dir', 'micon-right-dir', 'micon-down-open', 'micon-left-open', 'micon-right-open', 'micon-up-open', 
						'micon-angle-left', 'micon-angle-right', 'micon-angle-up', 'micon-angle-down', 'micon-angle-circled-left', 
						'micon-angle-circled-right', 'micon-angle-circled-up', 'micon-angle-circled-down', 'micon-angle-double-left', 
						'micon-angle-double-right', 'micon-angle-double-up', 'micon-angle-double-down', 'micon-down', 'micon-left', 
						'micon-right', 'micon-up', 'micon-down-big', 'micon-left-big', 'micon-right-big', 'micon-up-big', 
						'micon-right-hand', 'micon-left-hand', 'micon-up-hand', 'micon-down-hand', 'micon-left-circled', 
						'micon-right-circled', 'micon-up-circled', 'micon-down-circled', 'micon-cw', 'micon-ccw', 'micon-arrows-cw', 
						'micon-level-up', 'micon-level-down', 'micon-shuffle', 'micon-exchange', 'micon-music', 'micon-collapse', 
						'micon-expand-right', 'micon-collapse-left', 'micon-play', 'micon-play-circled', 'micon-play-circled2', 
						'micon-stop', 'micon-pause', 'micon-to-end', 'micon-to-end-alt', 'micon-to-start', 'micon-to-start-alt', 
						'micon-fast-fw', 'micon-fast-bw', 'micon-eject', 'micon-target', 'micon-signal', 'micon-award', 'micon-desktop', 
						'micon-laptop', 'micon-tablet', 'micon-mobile', 'micon-inbox', 'micon-globe', 'micon-sun', 'micon-cloud', 
						'micon-flash', 'micon-moon', 'micon-umbrella', 'micon-flight', 'micon-fighter-jet', 'micon-leaf', 'micon-font', 
						'micon-bold', 'micon-italic', 'micon-text-height', 'micon-text-width', 'micon-align-left', 'micon-align-center', 
						'micon-align-right', 'micon-align-justify', 'micon-list', 'micon-indent-left', 'micon-indent-right', 
						'micon-list-bullet', 'micon-list-numbered', 'micon-strike', 'micon-underline', 'micon-superscript', 
						'micon-subscript', 'micon-table', 'micon-columns', 'micon-crop', 'micon-scissors', 'micon-paste', 
						'micon-briefcase', 'micon-suitcase', 'micon-ellipsis', 'micon-ellipsis-vert', 'micon-off', 'micon-road', 
						'micon-list-alt', 'micon-qrcode', 'micon-barcode', 'micon-book', 'micon-ajust', 'micon-tint', 'micon-check', 
						'micon-check-empty', 'micon-circle', 'micon-circle-empty', 'micon-dot-circled', 'micon-asterisk', 'micon-gift', 
						'micon-fire', 'micon-magnet', 'micon-chart-bar', 'micon-ticket', 'micon-credit-card', 'micon-floppy', 
						'micon-megaphone', 'micon-hdd', 'micon-key', 'micon-fork', 'micon-rocket', 'micon-bug', 'micon-certificate', 
						'micon-tasks', 'micon-filter', 'micon-beaker', 'micon-magic', 'micon-truck', 'micon-money', 'micon-euro', 
						'micon-pound', 'micon-dollar', 'micon-rupee', 'micon-yen', 'micon-rouble', 'micon-try', 'micon-won', 
						'micon-bitcoin', 'micon-sort', 'micon-sort-down', 'micon-sort-up', 'micon-sort-alt-up', 'micon-sort-alt-down', 
						'micon-sort-name-up', 'micon-sort-name-down', 'micon-sort-number-up', 'micon-sort-number-down', 'micon-hammer', 
						'micon-gauge', 'micon-sitemap', 'micon-spinner', 'micon-coffee', 'micon-food', 'micon-beer', 'micon-user-md', 
						'micon-stethoscope', 'micon-ambulance', 'micon-medkit', 'micon-h-sigh', 'micon-hospital', 'micon-building', 
						'micon-smile', 'micon-frown', 'micon-meh', 'micon-anchor', 'micon-terminal', 'micon-eraser', 'micon-puzzle', 
						'micon-shield', 'micon-extinguisher', 'micon-bullseye', 'micon-wheelchair', 'micon-adn', 'micon-android', 
						'micon-apple', 'micon-bitbucket', 'micon-bitbucket-squared', 'micon-css3', 'micon-dribbble', 'micon-dropbox', 
						'micon-facebook', 'micon-facebook-squared', 'micon-flickr', 'micon-foursquare', 'micon-github', 
						'micon-github-squared', 'micon-github-circled', 'micon-gittip', 'micon-gplus-squared', 'micon-gplus', 
						'micon-html5', 'micon-instagramm', 'micon-linkedin-squared', 'micon-linux', 'micon-linkedin', 'micon-maxcdn', 
						'micon-pagelines', 'micon-pinterest-circled', 'micon-pinterest-squared', 'micon-renren', 'micon-skype', 
						'micon-stackexchange', 'micon-stackoverflow', 'micon-trello', 'micon-tumblr', 'micon-tumblr-squared', 
						'micon-twitter-squared', 'micon-twitter', 'micon-vimeo-squared', 'micon-vkontakte', 'micon-weibo', 'micon-windows', 
						'micon-xing', 'micon-xing-squared', 'micon-youtube', 'micon-youtube-squared', 'micon-youtube-play', 'micon-blank', 
						'micon-expand');
						foreach ($font_array as $font) { ?>
						<div class="menu_icon" id="icon_<?php echo $item_id; ?>"><a href="#" class="<?php echo $font; ?>"></a></div>
					<?php } ?>
				</div>
				<input type="hidden" id="menu_icon_<?php echo $item_id; ?>" value="<?php echo esc_attr( $item->custom ) ?>" name="menu-item-custom[<?php echo $item_id; ?>]">
				<input type="button" class="menu_item_icon" value="Select icon">
				<input type="button" class="remove_menu_item_icon" value="Remove icon">
				<div class="selected_menu_icon <?php echo esc_attr( $item->custom ) ?>" id="selected_icon_<?php echo $item_id; ?>">Saved icon:</div>
			</div>
			<?php // ..end added field ?>
			<div class="menu-item-actions description-wide submitbox">
				<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
					<p class="link-to-original">
						<?php printf( __('Original: ', 'vh') . '%s', '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
					</p>
				<?php endif; ?>
				<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
				echo wp_nonce_url(
					esc_url( add_query_arg(
						array(
							'action'    => 'delete-menu-item',
							'menu-item' => $item_id,
						),
						remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
					) ),
					'delete-menu_item_' . $item_id
				); ?>"><?php _e('Remove', 'vh'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
					?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel', 'vh'); ?></a>
			</div>

			<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
			<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
			<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
			<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
			<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
			<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
		</div><!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
	<?php
		$output .= ob_get_clean();
	}
}

class description_walker extends Walker_Nav_Menu {
		function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
			global $wp_query;

			$attributes = $item_output = '';

			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = ' class="'. esc_attr( $class_names ) . ' ' . $item->custom .'"';

			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names . '>';

			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

			$item_output .= '<a' . $attributes . '>' . apply_filters( 'the_title', $item->title, $item->ID ) . '</a>';

			$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
		}
	}

// For "Our partners"
class partners_walker extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
		global $wp_query;

		$indent      = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes     = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . ' partner"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . '<img src="' . $item->custom . '" title="' . $item->attr_title . '" />';
		$item_output .= $args->link_after;

		  $item_output .= '</a>';
		$item_output .= $args->after;
		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
		}
}

class Walker_Nav_Menu_Dropdown extends Walker_Nav_Menu {
	function start_lvl(&$output, $depth = 0, $args = array()){
		$indent = str_repeat("\t", $depth); // don't output children opening tag (`<ul>`)
	}

	function end_lvl(&$output, $depth = 0, $args = array()){
		$indent = str_repeat("\t", $depth); // don't output children closing tag
	}

	function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
		$indent      = '';
		$value       = '';
		$class_names = '';
		$item_output = '';

		// add spacing to the title based on the depth
		$item->title = str_repeat("&nbsp;", $depth * 4).$item->title;

		$attributes = $indent . ' id="menu-item-'. $item->ID . '"' . $value . $class_names .'';
		$attributes .= ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' value="' . esc_attr( $item->url ) .'"' : '';

		$item_output .= '<option'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= '</option>';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

		// no point redefining this method too, we just replace the li tag...
		$output = str_replace('<li', '<option', $output);
	}

	function end_el(&$output, $item, $depth = 0, $args = array()) {
		//$output .= "</option>\n"; // replace closing </li> with the option tag
	}
}

/**
 * Class Name: multilevel_push_nav_walker
 * Description: A custom Wordpress nav walker to implement the navigation using the Wordpress built in menu manager.
*/

class multilevel_push_nav_walker extends Walker_Nav_Menu {
/* Start of the <ul>
		 *
		 * Note on $depth: Counterintuitively, $depth here means the "depth right before we start this menu".
		 *                   So basically add one to what you'd expect it to be
		 */
		function start_lvl(&$output, $depth = 0, $args = array()) {
			$tabs = str_repeat("\t", $depth);
			// If we are about to start the first submenu, we need to give it a dropdown-menu class
			if ($depth == 0 || $depth == 1 || $depth == 2 || $depth == 3) { //really, level-1 or level-2, because $depth is misleading here (see note above)
				$output .= "\n{$tabs}<div class=\"mp-level\">
									<div class=\"menu-title\"><h2 class=\"icon\">menu_item_name</h2></div>
									<a class=\"mp-back\" href=\"#\">" . __('Back', 'vh') . "</a>
									<ul class=\"dropdown-menu\">\n";
			} else {
				$output .= "\n{$tabs}<ul>\n";
			}
			return;
		}

		/* End of the <ul>
		 *
		 * Note on $depth: Counterintuitively, $depth here means the "depth right before we start this menu".
		 *                   So basically add one to what you'd expect it to be
		 */
		function end_lvl(&$output, $depth = 0, $args = array()) {
			if ($depth == 0) { // This is actually the end of the level-1 submenu ($depth is misleading here too!)

				// we don't have anything special, so we'll just leave an HTML comment for now
				$output .= '<!--.dropdown-->';
			}
			$tabs = str_repeat("\t", $depth);
			$output .= "\n{$tabs}</ul></div>\n";
			return;
		}

		/* Output the <li> and the containing <a>
		 * Note: $depth is "correct" at this level
		 */
		function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
			global $wp_query;
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			$class_names = $value = '';
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;

			/* If this item has a dropdown menu, add the 'dropdown' class */
			if ($item->hasChildren) {
				$classes[] = 'icon icon-arrow-left';
				// level-1 menus also need the 'dropdown-submenu' class
				if($depth == 1) {
					$classes[] = 'dropdown-submenu';
				}
			}

			$nav_menu_item = get_post( $item->menu_item_parent );
			if ( empty($nav_menu_item->post_title) ) {
				$nav_menu_object = get_post_meta($nav_menu_item->ID);
				$nav_menu_object_post = get_post( $nav_menu_object['_menu_item_object_id'][0] );

				$menu_title = $nav_menu_object_post->post_title;
			} else {
				$menu_title = $nav_menu_item->post_title;
			}

			$output = str_replace("menu_item_name", $menu_title, $output);

			/* This is the stock Wordpress code that builds the <li> with all of its attributes */
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = ' class="' . esc_attr( $class_names ) . '"';
			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			 $item_output = '';

			/* If this item has a dropdown menu, make clicking on this link toggle it */
			if ($item->hasChildren && $depth == 0) {
				$item_output .= '<a class="icon icon-display" href="#">';
			} else {
				$item_output .= '<a'. $attributes .'>';
			}

			$item_output .= apply_filters( 'the_title', $item->title, $item->ID );

			/* Output the actual caret for the user to click on to toggle the menu */
			if ($item->hasChildren && $depth == 0) {
				$item_output .= '</a>';
			} else {
				$item_output .= '</a>';
			}

			// $item_output .= $args->after;
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			return;
		}

		/* Close the <li>
		 * Note: the <a> is already closed
		 * Note 2: $depth is "correct" at this level
		 */
		function end_el (&$output, $item, $depth = 0, $args = array()) {
			$output .= '</li>';
			return;
		}

		/* Add a 'hasChildren' property to the item
		 * Code from: http://wordpress.org/support/topic/how-do-i-know-if-a-menu-item-has-children-or-is-a-leaf#post-3139633
		 */
		function display_element ($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
			// check whether this item has children, and set $item->hasChildren accordingly
			$element->hasChildren = isset($children_elements[$element->ID]) && !empty($children_elements[$element->ID]);

			// continue with normal behavior
			return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
		}
}


/**
 * Create HTML list of nav menu items.
 * Replacement for the native Walker, using the description.
 *
 * @see    http://wordpress.stackexchange.com/q/14037/
 * @author toscho, http://toscho.de
 */
class Footer_Walker extends Walker_Nav_Menu {
	/**
	 * Start the element output.
	 *
	 * @param  string $output Passed by reference. Used to append additional content.
	 * @param  object $item   Menu item data object.
	 * @param  int $depth     Depth of menu item. May be used for padding.
	 * @param  array $args    Additional strings.
	 * @return void
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$classes = empty ( $item->classes ) ? array () : (array) $item->classes;

		if (!is_object($args)) {
			$args = new stdClass;
		}

		if ( empty($item->attr_title) ) {
			$item->attr_title = '';
		}

		if ( empty($item->target) ) {
			$item->target = '';
		}

		if ( empty($item->xfn) ) {
			$item->xfn = '';
		}
		
		if ( empty($item->url) ) {
			$item->url = '';
		}
		
		if ( !isset($args->before) ) {
			$args->before = '';
		}
		
		if ( !isset($args->link_before) ) {
			$args->link_before = '';
		}
		
		if ( !isset($args->link_after) ) {
			$args->link_after = '';
		}
		
		if ( !isset($args->after) ) {
			$args->after = '';
		}
		
		$class_names = join(' ',
			apply_filters(
				'nav_menu_css_class',
				array_filter( $classes ),
				$item
			)
		);

		!empty( $class_names ) and $class_names = ' class="'. esc_attr( $class_names ) . '"';

		$output .= "<li id='menu-item-$item->ID' $class_names>";

		$attributes  = '';

		!empty( $item->attr_title ) and $attributes .= ' title="'  . esc_attr( $item->attr_title ) .'"';
		!empty( $item->target ) and $attributes .= ' target="' . esc_attr( $item->target     ) .'"';
		!empty( $item->xfn ) and $attributes .= ' rel="'    . esc_attr( $item->xfn        ) .'"';
		!empty( $item->url ) and $attributes .= ' href="'   . esc_attr( $item->url        ) .'"';

		// insert description for top level elements only
		// you may change this
		$description = ( ! empty ( $item->description ) and 0 == $depth ) ? '<small class="nav_desc">' . esc_attr( $item->description ) . '</small>' : '';

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$item_output = $args->before
			. "<a $attributes>"
			. $args->link_before
			. $title
			. '</a> '
			. $args->link_after
			. $description
			. $args->after;

		// Since $output is called by reference we don't need to return anything.
		$output .= apply_filters(
			'walker_nav_menu_start_el',
			$item_output,
			$item,
			$depth,
			$args
		);
	}
}