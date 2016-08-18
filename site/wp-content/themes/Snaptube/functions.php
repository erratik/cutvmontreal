<?php
/**
 * Snaptube functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 */

// Define file directories
define('VH_HOME', get_template_directory());
define('VH_FUNCTIONS', get_template_directory() . '/functions');
define('VH_GLOBAL', get_template_directory() . '/functions/global');
define('VH_WIDGETS', get_template_directory() . '/functions/widgets');
define('VH_CUSTOM_PLUGINS', get_template_directory() . '/functions/plugins');
define('VH_ADMIN', get_template_directory() . '/functions/admin');
define('VH_ADMIN_IMAGES', get_template_directory_uri() . '/functions/admin/images');
define('VH_METABOXES', get_template_directory() . '/functions/admin/metaboxes');
define('VH_SIDEBARS', get_template_directory() . '/functions/admin/sidebars');


// Define theme URI
define('VH_URI', get_template_directory_uri() .'/');
define('VH_GLOBAL_URI', VH_URI . 'functions/global');

define('THEMENAME', 'Snaptube');
define('SHORTNAME', 'VH');
define('VH_HOME_TITLE', 'Home page');
define('VH_DEVELOPER_NAME_DISPLAY', 'Cohhe themes');
define('VH_DEVELOPER_URL', 'http://cohhe.com');

define('TESTENVIRONMENT', FALSE);

add_action('after_setup_theme', 'vh_setup');
add_filter('widget_text', 'do_shortcode');

// Set max content width
if (!isset($content_width)) {
	$content_width = 900;
}

if (!function_exists('vh_setup')) {

	function vh_setup() {

		// Load Admin elements
		require_once(VH_ADMIN . '/theme-options.php');
		require_once(VH_ADMIN . '/admin-interface.php');
		require_once(VH_ADMIN . '/menu-custom-field.php');
		require_once(VH_METABOXES . '/layouts.php');
		require_once(VH_METABOXES . '/contact_map.php');
		require_once(VH_SIDEBARS . '/multiple_sidebars.php');
		require_once(VH_FUNCTIONS . '/installer/importer/widgets-importer.php');
		require_once(VH_FUNCTIONS . '/installer/functions-themeinstall.php');

		// Widgets list
		$widgets = array (
			VH_WIDGETS . '/contactform.php',
			VH_WIDGETS . '/googlemap.php',
			VH_WIDGETS . '/social_links.php',
			VH_WIDGETS . '/advertisement.php',
			VH_WIDGETS . '/recent-posts-plus.php',
			VH_WIDGETS . '/fast-flickr-widget.php',
			VH_WIDGETS . '/contusBannerSlideshow.php'
		);

		// Load Widgets
		load_files($widgets);

		// Load global elements
		require_once(VH_GLOBAL . '/wp_pagenavi/wp-pagenavi.php');

		// if (file_exists(VH_CUSTOM_PLUGINS . '/landing-pages/landing-pages.php')) {
		// 	require_once(VH_CUSTOM_PLUGINS . '/landing-pages/landing-pages.php');
		// }

		// TGM plugins activation
		require_once(VH_FUNCTIONS . '/tgm-activation/class-tgm-plugin-activation.php');

		// Extend Visual Composer
		if (defined('WPB_VC_VERSION')) {
			require_once(VH_FUNCTIONS . '/visual_composer_extended.php');
		}

		// Shortcodes list
		$shortcodes = array (
			//VH_SHORTCODES . '/test.php'
		);

		// Load shortcodes
		load_files($shortcodes);

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();

		// Add default posts and comments RSS feed links to <head>.
		add_theme_support('automatic-feed-links');

		// If theme is activated them send to options page
		// if (is_admin() && isset($_GET['activated'])) {
		// 	wp_redirect(admin_url('admin.php?page=themeoptions'));
		// }

		if ( !is_admin() ) {
			add_filter( 'posts_join', 'vh_search_meta_data_join' );
			add_filter( 'posts_where', 'vh_search_meta_data_where' );

			add_filter( 'posts_join', 'vh_author_data_join' );
			add_filter( 'posts_where', 'vh_author_data_where' );
		}
	}
}

// Force Visual Composer to initialize as "built into the theme". This will hide certain tabs under the Settings->Visual Composer page
if(function_exists('vc_set_as_theme')) vc_set_as_theme();

function vh_search_meta_data_join($join) {
	global $wpdb;

	// Only join the post meta table if we are performing a search
	if ( get_query_var( 's' ) == '' ) {
		return $join;
	}

	// Only join the post meta table if we are on the Contacts Custom Post Type
	if ( !in_array('videogallery', get_query_var( 'post_type' ) ) ) {
		return $join;
	}

	// Join the post meta table
	$join .= " LEFT JOIN ".$wpdb->prefix."hdflvvideoshare_tags ON $wpdb->posts.ID = ".$wpdb->prefix."hdflvvideoshare_tags.media_id ";

	return $join;
}

function vh_search_meta_data_where($where) {
	global $wpdb;

	// Only join the post meta table if we are performing a search
	if ( get_query_var( 's' ) == '' ) {
		return $where;
	}

	// Only join the post meta table if we are on the Contacts Custom Post Type
	if ( !in_array('videogallery', get_query_var( 'post_type' ) ) ) {
		return $where;
	}

	// Get the start of the query, which is ' AND ((', and the rest of the query
	$startOfQuery = substr( $where, 0, 7 );
	$restOfQuery = substr( $where ,7 );

	// Inject our WHERE clause in between the start of the query and the rest of the query
	$where = $startOfQuery . "(" . $wpdb->prefix."hdflvvideoshare_tags.tags_name LIKE '%" . get_query_var( 's' ) . "%') OR " . $restOfQuery ." GROUP BY " . $wpdb->posts . ".id";

	// Return revised WHERE clause
	return $where;
}

function vh_author_data_join($join) {
	global $wpdb;

	// Only join the post meta table if we are performing a search
	if ( get_query_var('all_videos') != 'true' ) {
		return $join;
	}

	// Only join the post meta table if we are on the Contacts Custom Post Type
	if ( !in_array('videogallery', get_query_var( 'post_type' ) ) ) {
		return $join;
	}

	// Join the post meta table
	$join .= ", ".$wpdb->prefix."hdflvvideoshare ";

	return $join;
}

function vh_author_data_where($where) {
	global $wpdb;

	// Only join the post meta table if we are performing a search
	if ( get_query_var('all_videos') != 'true' ) {
		return $where;
	}

	// Only join the post meta table if we are on the Contacts Custom Post Type
	if ( !in_array('videogallery', get_query_var( 'post_type' ) ) ) {
		return $where;
	}

	// Inject our WHERE clause in between the start of the query and the rest of the query
	$where .= " AND (" . $wpdb->prefix."hdflvvideoshare.slug = " . $wpdb->prefix."posts.ID)";

	// Return revised WHERE clause
	return $where;
}

// Add quote post format support
add_theme_support( 'post-formats', array( 'quote' ) );


function vh_admin_notice() {
	if ( $_SERVER['SERVER_NAME'] == 'dev.cohhe.com' ) { ?>
		<div class="dev-environment">
			<p><?php echo 'This is dev environment!'; ?></p>
		</div>
	<?php
	}
}
add_action( 'admin_notices', 'vh_admin_notice' );

// Load Widgets
function load_files ($files) {
	foreach ($files as $file) {
		require_once($file);
	}
}

if (function_exists('add_theme_support')) {
	add_theme_support('post-thumbnails');

	// Default Post Thumbnail dimensions
	set_post_thumbnail_size(150, 150);
}

function the_excerpt_max_charlength($charlength) {
	$excerpt = get_the_excerpt();
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			echo mb_substr( $subex, 0, $excut );
		} else {
			echo $subex;
		}
		echo '...';
	} else {
		echo $excerpt;
	}
}

add_filter( 'comment_post_redirect', 'vh_comment_redirect' );
function vh_comment_redirect( $location ) {
	global $wpdb;
	return $_SERVER["HTTP_REFERER"]."#comment-".$wpdb->insert_id;
}

add_action('set_current_user', 'vh_hide_admin_bar');
function vh_hide_admin_bar() {
	if (!current_user_can('edit_posts')) {
		show_admin_bar(false);
	}
}

function vh_previous_comments_link( $label = '' ) {
	echo vh_get_previous_comments_link( $label );
}

function vh_get_comments_pagenum_link( $pagenum = 1, $max_page = 0 ) {
	global $wp_rewrite;

	$pagenum = (int) $pagenum;

	$result = $_SERVER["REQUEST_URI"];

	if ( 'newest' == get_option('default_comments_page') ) {
		if ( $pagenum != $max_page ) {
			if ( $wp_rewrite->using_permalinks() )
				$result = user_trailingslashit( trailingslashit($result) . 'comment-page-' . $pagenum, 'commentpaged');
			else
				$result = esc_url( add_query_arg( 'cpage', $pagenum, $result ) );
		}
	} elseif ( $pagenum > 1 ) {
		if ( $wp_rewrite->using_permalinks() )
			$result = user_trailingslashit( trailingslashit($result) . 'comment-page-' . $pagenum, 'commentpaged');
		else
			$result = esc_url( add_query_arg( 'cpage', $pagenum, $result ) );
	}

	$result .= '#comments';

	$result = apply_filters('get_comments_pagenum_link', $result);

	return $result;
}

function vh_get_previous_comments_link( $label = '' ) {
	if ( !is_singular() || !get_option('page_comments') )
		return;

	$page = get_query_var('cpage');

	if ( intval($page) <= 1 )
		return;

	$prevpage = intval($page) - 1;

	if ( empty($label) )
		$label = __('&laquo; Older Comments', 'vh');

	return '<a href="' . esc_url( vh_get_comments_pagenum_link( $prevpage ) ) . '" ' . apply_filters( 'previous_comments_link_attributes', '' ) . '>' . preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) .'</a>';
}

function vh_get_next_comments_link( $label = '', $max_page = 0 ) {
	global $wp_query;

	if ( !is_singular() || !get_option('page_comments') )
		return;

	$page = get_query_var('cpage');

	$nextpage = intval($page) + 1;

	if ( empty($max_page) )
		$max_page = $wp_query->max_num_comment_pages;

	if ( empty($max_page) )
		$max_page = get_comment_pages_count();

	if ( $nextpage > $max_page )
		return;

	if ( empty($label) )
		$label = __('Newer Comments &raquo;', 'vh');

	return '<a href="' . esc_url( vh_get_comments_pagenum_link( $nextpage ) ) . '" ' . apply_filters( 'next_comments_link_attributes', '' ) . '>'. preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) .'</a>';
}

function vh_next_comments_link( $label = '', $max_page = 0 ) {
	echo vh_get_next_comments_link( $label, $max_page );
}

function vh_register_widgets () {
	register_sidebar( array(
		'name'          => __( 'Normal', 'vh' ),
		'id'            => 'sidebar-1',
		'class'         => 'normal',
		'before_widget' => '<div class="widget">',
		'after_widget'  => '<div class="clearfix"></div></div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	) );

	register_sidebar( array(
		'name'          => __( 'Recent News', 'vh' ),
		'id'            => 'sidebar-2',
		'class'         => 'recent-news',
		'before_widget' => '<div class="widget">',
		'after_widget'  => '<div class="clearfix"></div></div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	) );
}
add_action( 'widgets_init', 'vh_register_widgets' );

function vh_imgresize($url, $width, $height = null, $postid, $crop = null, $single = true) {

	//validate inputs
	if ( $url == '' || $width == '' ) {
		return false;
	}

	if ( get_option('vh_upload_images', 'false') == 'false' || strpos($url,'ytimg.com') === false ) {
		return $url;
	}

	//define upload path & dir
	$upload_info = wp_upload_dir();
	$upload_dir  = $upload_info['basedir'];
	$upload_url  = $upload_info['baseurl'];
	$image_quality = '';

	if ( strpos($url,'sddefault') !== false ) {
		$image_quality = 'sd';
	} elseif ( strpos($url,'mqdefault') !== false ) {
		$image_quality = 'mq';
	}

	$saved_meta = get_post_meta($postid, 'video_'.$image_quality.'_image', true);

	//check if $img_url is local
	if ( $saved_meta && ( strpos($saved_meta,'sddefault') === false || strpos($saved_meta,'-sd') === false ) && ( strpos($saved_meta,'mqdefault') === false || strpos($saved_meta,'-mq') === false ) ) {
		return $saved_meta;
	} elseif (strpos($url, $upload_url) === false) {
		$url = vh_media_sideload_image($url, $postid, '', 'src', $image_quality);

		if ( is_wp_error($url) ) {
			return get_template_directory_uri().'/images/nothumbimage.jpg';
		} else {
			// Check WP version
			global $wp_version;

			if ( $image_quality != '' ) {
				if ( $image_quality == 'sd' ) {
					update_post_meta($postid, 'video_sd_image', $url);
				} else if ( $image_quality == 'mq' ) {
					update_post_meta($postid, 'video_mq_image', $url);
				}
			}
		}
	}

	//define path of image
	$rel_path = str_replace($upload_url, '', $url);
	$img_path = $upload_dir . $rel_path;

	//check if img path exists, and is an image indeed
	if (!file_exists($img_path) || !getimagesize($img_path)) {
		return false;
	}

	//get image info
	$info                  = pathinfo($img_path);
	$ext                   = $info['extension'];
	list($orig_w, $orig_h) = getimagesize($img_path);

	//get image size after cropping
	$dims  = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
	$dst_w = $dims[4];
	$dst_h = $dims[5];

	//use this to check if cropped image already exists, so we can return that instead
	$suffix       = "{$dst_w}x{$dst_h}";
	$dst_rel_path = str_replace('.' . $ext, '', $rel_path);
	$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

	if (!$dst_h) {
	//can't resize, so return original url
		$img_url = $url;
		$dst_w   = $orig_w;
		$dst_h   = $orig_h;

	//else check if cache exists
	} elseif (file_exists($destfilename) && getimagesize($destfilename)) {
		$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";

	//else, we resize the image and return the new resized image url
	} else {

		// Note: pre-3.5 fallback check 
		if (function_exists('wp_get_image_editor')) {

			$editor = wp_get_image_editor($img_path);

			if (is_wp_error($editor) || is_wp_error($editor->resize($width, $height, $crop)))
				return false;

			$resized_file = $editor->save();

			if (!is_wp_error($resized_file)) {
				$resized_rel_path = str_replace($upload_dir, '', $resized_file['path']);
				$img_url          = $upload_url . $resized_rel_path;
			} else {
				return false;
			}
		} else {

			$resized_img_path = image_resize($img_path, $width, $height, $crop);
			if (!is_wp_error($resized_img_path)) {
				$resized_rel_path = str_replace($upload_dir, '', $resized_img_path);
				$img_url          = $upload_url . $resized_rel_path;
			} else {
				return false;
			}
		}
	}

	//return the output
	if ($single) {
	//str return
		$image = $img_url;
	} else {
	//array return
		$image = array(
			0 => $img_url,
			1 => $dst_w,
			2 => $dst_h
		);
	}

	return $image;
}

function vh_media_sideload_image( $file, $post_id, $desc = null, $return = 'html', $image_type = '' ) {
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');

	if ( ! empty( $file ) ) {

		// Set variables for storage, fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
		$file_array = array();
		$file_array['name'] = basename( $matches[0] );
		$url_type = wp_check_filetype( basename( $matches[0] ) );

		// Download file to temp location.
		$tmp = download_url( $file );
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return $file_array['tmp_name'];
		}

		$filename = sanitize_file_name( preg_replace('/[^\00-\255]+/u', '', get_the_title( $post_id )).'-'.$image_type );
		$tmppath = pathinfo( $tmp );                                                        // extract path parts
		$new = $tmppath['dirname'] . "/". $filename . "-" . $image_type . "." . $tmppath['extension'];          // build new path
		rename($tmp, $new);                                                                 // renames temp file on server
		$tmp = $new;                                                                        // push new filename (in path) to be used in file array later

		// assemble file data (should be built like $_FILES since wp_handle_sideload() will be using)
		$file_array['tmp_name'] = $tmp;
		$file_array['name'] = $filename . "." . $url_type['ext'];

		// Do the validation and storage stuff.
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return $id;
		}

		$src = wp_get_attachment_url( $id );
	}

	// Finally, check to make sure the file has been saved, then return the HTML.
	if ( ! empty( $src ) ) {
		if ( $return === 'src' ) {
			return $src;
		}

		$alt = isset( $desc ) ? esc_attr( $desc ) : '';
		$html = "<img src='$src' alt='$alt' />";
		return $html;
	} else {
		return new WP_Error( 'image_sideload_failed' );
	}
}

function vh_get_attachment_id_by_url( $url ) {
	// Split the $url into two parts with the wp-content directory as the separator
	$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

	// Get the host of the current site and the host of the $url, ignoring www
	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

	// Return nothing if there aren't any $url parts or if the current host and $url host do not match
	if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
		return;
	}

	// Now we're going to quickly search the DB for any attachment GUID with a partial path match
	// Example: /uploads/2013/05/test-image.jpg
	global $wpdb;

	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );

	// Returns null if no attachment is found
	return $attachment[0];
}

add_action( 'wp_ajax_nopriv_snaptube_generate_form', 'vh_generate_header_form' );
add_action( 'wp_ajax_snaptube_generate_form', 'vh_generate_header_form' );
function vh_generate_header_form() {
	if ( isset($_POST['form_type']) ) {
		$form = $_POST['form_type'];
	} else {
		$form = 'login_button_form';
	}

	switch ( $form ) {
		case 'login_button_form':
			$output = '
			<span class="icon icon-close login_form_close"></span>
			<h1>' . __('Login', 'vh') . '</h1>
			<p class="p_login"><input class="loginusername" type="text" id="username" name="login" placeholder="' . __( 'Username', 'vh' ) . '"></p>
			<p class="p_password"><input class="loginpassword" type="password" id="password" name="password" placeholder="' . __( 'Password', 'vh' ) . '"></p>
			<p class="status"></p>
			<p class="p_button">
				<input id="login_button" class="btn-primary" type="submit" name="commit" value="' . __( 'Login', 'vh' ) . '">';
				if ( get_option('users_can_register', '0') ) {
					$output .= '<span>or </span><a href="javascript:void(1)" id="register_button_form">' . __( 'Sign up', 'vh' ) . '</a>';
				}
			$output .= '</p>' . wp_nonce_field( 'ajax-login-nonce', 'security' ) . '
			<a href="' . wp_lostpassword_url() . '" class="forgot_password">' . __( 'Forgot password', 'vh' ) . '</a>';
			break;
		case 'register_button_form':
			$output = '
			<span class="icon icon-close login_form_close"></span>
			<h1>' . __('Register', 'vh') . '</h1>
			<p class="p_login"><input class="loginusername" type="text" id="username" name="login" placeholder="' . __( 'Username', 'vh' ). '"></p>
			<p class="p_email"><input class="loginemail" type="text" id="email" name="email" placeholder="' . __( 'Email', 'vh' ) . '"></p>
			<p class="status"></p>
			<p class="p_button">
				<input id="register_button" class="btn-primary" type="submit" name="commit" value="' . __( 'Register', 'vh' ) . '">';
				if ( get_option('users_can_register', '0') ) {
					$output .= '<span>or </span><a href="javascript:void(1)" id="login_button_form">' . __( 'Login', 'vh' ) . '</a>';
				}
			$output .= '</p>' . wp_nonce_field( 'ajax-register-nonce', 'regsecurity' );
			break;
		default:
			break;
	}

	echo $output;

	die(1);
}

function vh_register_user() {
	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-register-nonce', 'regsecurity' );

	$username = sanitize_user($_POST['fullname']);
	$email = sanitize_email( $_POST['email']);
	
	// Register the user
	$user_register = register_new_user( $username, $email );
	if ( is_wp_error($user_register) ) {
		$error  = $user_register->get_error_codes();
		if( in_array('empty_user_login', $error) )
			echo json_encode(array('loggedin'=>false, 'message'=>__('Enter your username', 'vh'), 'for_input' => 'username'));
		elseif(in_array('username_exists',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('Username already exists.', 'vh'), 'for_input' => 'username'));
		elseif(in_array('email_exists',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('Email already exists.', 'vh'), 'for_input' => 'email'));
		elseif(in_array('empty_email',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('Enter email.', 'vh'), 'for_input' => 'email'));
		elseif(in_array('empty_username',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('Enter username.', 'vh'), 'for_input' => 'username'));
		else
			echo json_encode(array('loggedin'=>false, 'message'=>$error, 'for_input' => 'main'));
	} else {
		echo json_encode(array('loggedin'=>false, 'message'=>__('An email with your password was sent.', 'vh'), 'for_input' => 'main'));      
	}

	die(1);
}
add_action( 'wp_ajax_nopriv_ajax_register', 'vh_register_user' );

function vh_get_sidebar_menu($right = null) {
	$output = '';

	$side_menu_style  = get_option('vh_side_menu_style') ? get_option('vh_side_menu_style') : '';
	$suggested_videos = get_option('vh_suggested_videos') ? get_option('vh_suggested_videos') : '';
	
	$output .= '<div class="vc_col-sm-2 sidebar_menu ' . $side_menu_style . '">
		<div class="side_menu_seperator"></div>';

	ob_start();
	wp_nav_menu(
		array(
			'theme_location'  => 'primary-menu',
			'menu_class'      => 'primary-menu',
			'container'       => 'div',
			'container_class' => '',
			'depth'           => 2,
			'link_before'     => '',
			'link_after'      => '',
			'walker' => new description_walker()
		)
	);

	$output .= ob_get_contents();
	ob_end_clean();

	if ( $suggested_videos == '' ) {
		$output .= '</div><!--end of sidebars-->';
	}
	
	echo $output;
}

function vh_get_suggested_videos( $video_ids = null ) {
	$output = '';
	if ( $video_ids != null || $video_ids != '' ) {
		global $wpdb;
		$video_table = $wpdb->prefix.'hdflvvideoshare';
		$suggested_video = $wpdb->get_results("SELECT * FROM {$video_table} WHERE publish = '1' AND vid IN (" . $video_ids . ")");
		$htmlplayer_not_support  = __('Html5 Not support This video Format.', 'vh');
		$output = '';

		$output .= '<div class="suggested-video-container">
		<h2 class="suggested-text">' . __('Suggestions', 'vh') . '</h2>
		';

		foreach ($suggested_video as $video) {
			
			$embed_code = '<embed src="' . plugins_url() . '/contus-video-gallery/hdflvplayer/hdplayer.swf" id="vh-embed-code" flashvars="baserefW=' . get_option('siteurl') . '&amp;shareIcon=false&amp;email=false&amp;showPlaylist=false&amp;zoomIcon=false&amp;copylink=' . get_permalink($video->slug) . '&amp;embedplayer=true" width="100%" height="444px" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
			$reafile    = $video->file;

			$output .= '
			<div class="video_container">
				<div class="imgSidethumb">';

			$image_path     = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
			$file_type      = $video->file_type; // Video Type
			$imageFea       = $video->image; // Video Image
			$imageFea       = str_replace("/mq","/sd",$imageFea);
			$imageFea       = $video->image;
			$video_sd_image = 'mqdef';

			if ( $file_type == '2' ) {
				$imageFea = $image_path . $imageFea;
				$reafile  = $image_path . $reafile;
			}

			if ($imageFea == '') {  ##If there is no thumb image for video
				$imageFea = APPTHA_VGALLERY_BASEURL . 'images' . DS . 'nothumbimage.jpg';
				$video_sd_image .= ' noimage';
			} elseif ( $video->opimage == '' && $file_type != '3' ) {
				$upload_dir = wp_upload_dir();
				$imageFea = $upload_dir['baseurl'] . '/videogallery/' . $video->image;
			}

			$imageFea = vh_imgresize($imageFea, 185, 122, $video->slug);

			$thumb_href = 'href="' . get_permalink( $video->slug ) . '"';

			$output .= '<div class="video_image_container '.$video_sd_image.'">
							<a href="javascript:void(0);" class="video_play"></a>
							<a ' . $thumb_href . ' class="view_more"></a>
							<img src="' . $imageFea . '" alt="' . $video->name . '" class="related" />
							<div id="video_dialog" title="' . $video->name . '">';
							if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
								if( $file_type == 5 && !empty($video->embedcode) ) {
									$player_values = stripslashes($video->embedcode);
								 } else {
									 $mobile = vgallery_detect_mobile();
									if($mobile === true){
										## Check for youtube video
										if (preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $reafile, $vresult)) {
											$urlArray = explode("=", $vresult[0]);
											$video_id = trim($urlArray[1]);
											$reavideourl = "http://www.youtube.com/embed/$video_id";
											## Generate youtube embed code for html5 player
											$player_values = htmlentities('<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>');
										} else if ($file_type != 5) {        ## Check for upload, URL and RTMP videos
											if ($file_type == 4) {           ## For RTMP videos
												$streamer = str_replace("rtmp://", "http://", $media->streamer_path);
												$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
											}
											## Generate video code for html5 player
											$player_values = htmlentities('<video id="video" poster="' . $imageFea . '"   src="' . $reavideourl .'" autobuffer controls>' . $htmlplayer_not_support . '</video>');
										}
									} else {
										## Flash player code
										$player_values = '<embed src="' . plugins_url() . '/contus-video-gallery/hdflvplayer/hdplayer.swf" flashvars="baserefW=' . get_option('siteurl') . '&amp;mtype=playerModule&amp;vid=' . $video->vid . '" width="100%" height="444px" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
									}
								}

								if ( strpos($video->file,'soundcloud.com') !== false ) {
									$output .= '<input type="hidden" class="iframe_url" value="' . $video->file . '" />';
									$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
								} else {
									$output .= $player_values;
								}
							} else {
								if( strpos($video->file, 'v=') !== false ) {
									$video_link = explode('v=', $video->file);
									$output .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
									$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
								} elseif ( strpos($video->file, '/v/') !== false ) {
									$video_link = explode('/v/', $video->file);
									$output .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
									$output .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
								} elseif ( strpos($video->file, '/video/') !== false ) {
									$video_link = explode('/video/', $video->file);
									$video_link = explode('_', $video_link[1]);
									$output .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
									$output .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
								} else {

									if( $file_type == 5 && !empty($video->embedcode) ) {
										$output .= stripslashes($video->embedcode);
									}

									if ( $file_type == 3 ) {
										$videourl   = $reafile;
										$image_file = $imageFea;
									} elseif ( $file_type == 2 ) {
										$videourl   = $reafile;
										$output .= '<video width="100%" controls><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
									} else {
										$videourl   = $reafile;
										$image_file = $imageFea;
									}

									// $output .= do_shortcode('[video poster="' . $image_file . '" width="955" height="597" src="' . $videourl . '"]');
								}
							}
							$output .= '</div>
						</div>
						
					</div>';
			$output .='<div class="vid_info"><span><a ' . $thumb_href . ' class="videoHname">';

			// Displaying Video Title
			if (strlen($video->name) > 30) {
				$videoname = mb_substr($video->name, 0, 30) . '..';
			} else {
				$videoname = $video->name;
			}
			$output .= $videoname;
			$output .='</a></span></div>';
			$output .= '<div class="video_info">';
				if ($video->duration != 0.00) {
					$output .= '<div class="video-duration micon-clock">' . $video->duration . '</div>';
				}
				$output .= '<div class="video_views icon-eye">'. $video->hitcount . '</div>';
				$tc = wp_count_comments($video->slug);
				$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
				if ( function_exists('get_post_ul_meta') ) {
					$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($video->slug, "like") . '</div>'; 
				}
				$output .= '</div>
				<div class="suggested-video-desc">';
					$videodescription = '';
					if (strlen($video->description) > 80) { ## Displaying Video description
						$videodescription = mb_substr($video->description, 0, 80) . '..';
					} else {
						$videodescription = $video->description;
					}
					$output .= '<p>' . esc_html( $videodescription ) . '</p>
				</div>';
				$output .= '<div class="video_date icon-calendar">' . human_time_diff(get_the_time('U',$video->slug),current_time('timestamp')) .  ' ' . __('ago', 'vh') . '</div>';
				$output .= '<div class="video_author icon-user"><a href="'.get_author_posts_url( get_post_field( 'post_author', $video->slug ) ).'">'.get_userdata( get_post_field( 'post_author', $video->slug ) )->display_name.'</a></div>';
			$output .= '</div>';
		}
		$output .= '</div"></div>';
		$output .= '</div><!--end of sidebars-->';
	}
	echo $output;
}

global $frontControllerPath, $frontViewPath;
if (function_exists('get_playlist_id')) {
	// include_once ($frontModelPath . 'videoshortcode.php');
	include_once ($frontControllerPath . 'videoshortcodeController.php');
	include_once $frontViewPath . 'videoshortcode.php';
	// include_once WP_PLUGIN_DIR . '/' . $dirPage . '/ContusPopularVideos.php';

	if ( file_exists(WP_PLUGIN_DIR . '/snaptube-plugin/lib/functions/contusFunctions.php')) {
		include_once WP_PLUGIN_DIR . '/snaptube-plugin/lib/functions/contusFunctions.php';
	}
}

function vh_videohitCount_function($video_id) {
	global $wpdb;
	$vid = $video_id; ## Get video id from url
	$hitList = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "hdflvvideoshare WHERE vid='" . intval($vid) . "'");
	$hitCount = $hitList->hitcount; ## Get view count for particular video and increase it
	$hitInc = ++$hitCount;
	## Update Hit count here
	$wpdb->update($wpdb->prefix . "hdflvvideoshare", array('hitcount' => intval($hitInc)), array('vid' => intval($vid)));
}

function comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;

		$comments = get_comments('status=approve&post_id=' . $id);
		$separate_comments = separate_comments($comments);

		$comments_by_type = &$separate_comments;
		return count($comments_by_type['comment']);
	} else {
		return $count;
	}
}
add_filter('get_comments_number', 'comment_count', 0);

if (file_exists(VH_ADMIN . '/featured-images/featured-images.php')) {
	require_once(VH_ADMIN . '/featured-images/featured-images.php');
}

if( class_exists( 'vhFeaturedImages' ) ) {
	$i = 2;
	$posts_slideshow = ( get_option('vh_posts_slideshow_number') ) ? get_option('vh_posts_slideshow_number') : 5;

	while($i <= $posts_slideshow) {
		$args = array(
			'id'        => 'featured-image-'.$i,
			'post_type' => 'vh_accommodation', // Set this to post or page
			'labels'    => array(
				'name'   => 'Featured image '.$i,
				'set'    => 'Set featured image '.$i,
				'remove' => 'Remove featured image '.$i,
				'use'    => 'Use as featured image '.$i,
			)
		);

		new vhFeaturedImages( $args );

		$args = array(
			'id'        => 'featured-image-'.$i,
			'post_type' => 'vh_accommodation', // Set this to post or page
			'labels'    => array(
				'name'   => 'Featured image '.$i,
				'set'    => 'Set featured image '.$i,
				'remove' => 'Remove featured image '.$i,
				'use'    => 'Use as featured image '.$i,
			)
		);

		new vhFeaturedImages( $args );

		$args = array(
			'id'        => 'featured-image-'.$i,
			'post_type' => 'avada_portfolio', // Set this to post or page
			'labels'    => array(
				'name'   => 'Featured image '.$i,
				'set'    => 'Set featured image '.$i,
				'remove' => 'Remove featured image '.$i,
				'use'    => 'Use as featured image '.$i,
			)
		);

		new vhFeaturedImages( $args );

		$i++;
	}
}

function vh_post_image_module( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
	), $data ) );

	$post_id = $post->ID;
	$post_image = get_the_post_thumbnail($post_id, 'staff-image');
	$output = '';
	$tc = wp_count_comments($post_id);

	if ( $post_image != '' ) {
		$output .= '
		<div class="post-thumb">
			<div class="post-thumb-img-wrapper">
				<a href="' . get_permalink( $post_id ) . '" class="view_more"></a>
				<a href="'.get_permalink($post_id).'" class="link_image" title="Permalink to '.get_the_title($post_id).'">'.$post_image.'</a>
			</div>
			<div class="post-info-box">
				<div class="vc_post_comments icon-comment">' . $tc->total_comments . '</div>';
				if ( function_exists('get_post_ul_meta') ) {
					$output .= '<div class="vc_post_likes icon-heart">' . get_post_ul_meta($post_id,"like") . '</div>';
				}
			$output .= '
			</div>
		</div>';
	}

	return $output;
}
add_filter( 'vc_gitem_template_attribute_vh_post_image_module', 'vh_post_image_module', 10, 2 );

function vh_post_topmeta_module( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
	), $data ) );

	global $wpdb;
	$post_id = $post->ID;
	$post_image = get_the_post_thumbnail($post_id, 'staff-image');
	$output = '';
	$tc = wp_count_comments($post_id);
	$post_date_d = get_the_date( 'd', $post_id );
	$post_date_y = get_the_date( 'M, Y', $post_id );
	$where_comments = 'WHERE comment_approved = 1 AND user_id = ' . get_the_author_meta( 'ID' );
	$comment_count = $wpdb->get_var("SELECT COUNT( * ) AS total FROM {$wpdb->comments} {$where_comments}");
	$tags      = "";
	$i         = 0;
	$tag_count = count(get_the_tags( $post_id ));

	if ( get_the_tags( $post_id ) !== false ) {
		foreach (get_the_tags( $post_id ) as $tag) {
			if ( $i != $tag_count - 1 ) {
				$tags .= "<a href=" . get_tag_link( $tag->term_id ) . ">" . $tag->name . "</a>, ";
			} else {
				$tags .= "<a href=" . get_tag_link( $tag->term_id ) . ">" . $tag->name . "</a>";
			}
			$i++;
		}
	}

	if ( $post_image != '' ) {
		$output .= '
		<div class="post-date">
			<div class="post-date-container">
				<div class="post-date-left">
					<div class="post-date-day">' . $post_date_d . '</div>
					<div class="post-date-year">' . $post_date_y . '</div>
				</div>
				<div class="blog_postedby">' . get_avatar(get_the_author_meta( 'ID' )) . '</div>
			</div>';
			
			$output .= '
			<div class="blog-postedby-info">
				<a href="' . get_author_posts_url( get_post_field( "post_author", $post_id ) ) . '">'. __('by ', 'vh') . get_userdata( get_post_field( 'post_author', $post_id ) )->display_name . '</a>
				<div class="clearfix"></div>
				<div class="author-posts"><span class="icon-megaphone">' . count_user_posts( get_the_author_meta( 'ID' ) ) . __(' blog entries', 'vh') . '</span></div>
				<div class="author-comments"><span class="icon-comment">' . $comment_count . __(' comments', 'vh') . '</div>
			</div>';
			
		$output .= '
		</div>';
	}

	if ( $post_image != '' ) {
		$output .= '
		<h2 class="post-title">
			<a href="' . get_permalink( $post_id ) . '" class="link_title" title="' . __('Permalink to ', 'vh') . get_the_title( $post_id ) . '">' . get_the_title( $post_id ) . '</a>
		</h2>
		<div class="blog_info_box">
			<div class="category-link">
				<i class="entypo_icon icon-folder"></i>
				' . get_the_category_list(', ', '', $post_id) . '
			</div>
			<div class="tag-link">
				<i class="entypo_icon icon-tags"></i>
				' . $tags . '
			</div>
		</div>';
	} else {
		$output .= '
		<h2 class="post-title_nothumbnail">
			<a href="' . get_permalink( $post_id ) . '" class="link_title" title="' . __('Permalink to ', 'vh') . get_the_title( $post_id ) . '">' . get_the_title( $post_id ) . '</a>
		</h2>
		<div class="blog_info_box nothumbnail">
			<div class="category-link">
				<i class="entypo_icon icon-folder"></i>
				' . get_the_category_list(', ', '', $post_id) . '
			</div>
			<div class="tag-link">
				<i class="entypo_icon icon-tags"></i>
				' . $tags . '
			</div>
		</div>';
	}

	return $output;
}
add_filter( 'vc_gitem_template_attribute_vh_post_topmeta_module', 'vh_post_topmeta_module', 10, 2 );

function vh_post_bottom_module( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
	), $data ) );

	global $wpdb;
	$post_id = $post->ID;
	$post_image = get_the_post_thumbnail($post_id, 'staff-image');
	$output = '';
	$tc = wp_count_comments($post_id);
	$extra_class = '';
	if ( $post_image == '' ) {
		$extra_class = 'nothumbnail';
	}

	$output .= '
	<div class="entry-content ' . $extra_class . '">' . get_the_excerpt() . '</div>
	<span class="blog-read-more ' . $extra_class . '"><a href="' . get_the_permalink($post_id) . '" class="vc_read_more" title="' . __('Permalink to ', 'vh') . get_the_title( $post_id ) . '">'. __('Read more', "vh") . '</a></span>';

	return $output;
}
add_filter( 'vc_gitem_template_attribute_vh_post_bottom_module', 'vh_post_bottom_module', 10, 2 );

function tgm_cpt_search( $query ) {
	if ( $query->is_search )
		$query->set( 'post_type', array( 'page', 'post', 'videogallery' ) );
	return $query;
}
add_filter( 'pre_get_posts', 'tgm_cpt_search' );

function tgm_cpt_archive( $query ) {
	if ( !isset($_GET['type']) ) {
		$_GET['type'] = '';
	}

	if( is_archive() && get_query_var('all_videos') == 'true' && $query->is_main_query() ) {
		$query->set( 'post_type', array('videogallery') );
	}

	if( $query->is_author && $_GET['type'] == 'videogallery' ) {
		$query->set( 'post_type', array('post','videogallery'));
	}

	return $query;
}
add_filter( 'pre_get_posts', 'tgm_cpt_archive' );

function vh_add_query_vars_filter( $vars ) {
	$vars[] = "all_videos";

	return $vars;
}
add_filter( 'query_vars', 'vh_add_query_vars_filter' );

// Add new image sizes
if ( function_exists('add_image_size')) {
	add_image_size('gallery-small', 90, 90, true); // gallery-small gallery size
	add_image_size('app-image-large', 900, 310, true); // app-image-large image size
	add_image_size('staff-image', 520, 346, true); // staff-image image size
	add_image_size('large-image', 1755, 707, true); // large-image image size

	# Gallery image Cropped sizes
	add_image_size('gallery-large', 270, 270, true); // gallery-large gallery size
	add_image_size('gallery-medium', 125, 125, true); // gallery-medium gallery size
}

function vh_bp_get_member_avatar( $args = '' ) {
	global $members_template;

	$fullname = !empty( $members_template->member->fullname ) ? $members_template->member->fullname : $members_template->member->display_name;

	$defaults = array(
		'type'   => 'thumb',
		'width'  => 70,
		'height' => 70,
		'class'  => 'avatar',
		'id'     => false,
		'alt'    => sprintf( __( 'Profile picture of %s', 'vh' ), $fullname )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	return apply_filters( 'bp_get_member_avatar', bp_core_fetch_avatar( array( 'item_id' => $members_template->member->id, 'type' => $type, 'alt' => $alt, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height, 'email' => $members_template->member->user_email ) ) );
}

function vh_bp_member_avatar () {
	echo  vh_bp_get_member_avatar( $args );
}
add_filter('bp_member_avatar', 'vh_bp_member_avatar');

function vh_bp_get_activity_avatar( $args = '' ) {
	global $activities_template;

	$bp = buddypress();

	// On activity permalink pages, default to the full-size avatar
	$type_default = bp_is_single_activity() ? 'full' : 'thumb';

	// Within the activity comment loop, the current activity should be set
	// to current_comment. Otherwise, just use activity.
	$current_activity_item = isset( $activities_template->activity->current_comment ) ? $activities_template->activity->current_comment : $activities_template->activity;

	// Activity user display name
	$dn_default  = isset( $current_activity_item->display_name ) ? $current_activity_item->display_name : '';

	// Prepend some descriptive text to alt
	$alt_default = !empty( $dn_default ) ? sprintf( __( 'Profile picture of %s', 'vh' ), $dn_default ) : __( 'Profile picture', 'vh' );

	$defaults = array(
		'alt'     => $alt_default,
		'class'   => 'avatar',
		'email'   => false,
		'type'    => $type_default,
		'user_id' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( !isset( $height ) && !isset( $width ) ) {

		// Backpat
		if ( isset( $bp->avatar->full->height ) || isset( $bp->avatar->thumb->height ) ) {
			$height = ( 'full' == $type ) ? $bp->avatar->full->height : $bp->avatar->thumb->height;
		} else {
			$height = 70;
		}

		// Backpat
		if ( isset( $bp->avatar->full->width ) || isset( $bp->avatar->thumb->width ) ) {
			$width = ( 'full' == $type ) ? $bp->avatar->full->width : $bp->avatar->thumb->width;
		} else {
			$width = 70;
		}
	}

	// Primary activity avatar is always a user, but can be modified via a filter
	$object  = apply_filters( 'bp_get_activity_avatar_object_' . $current_activity_item->component, 'user' );
	$item_id = !empty( $user_id ) ? $user_id : $current_activity_item->user_id;
	$item_id = apply_filters( 'bp_get_activity_avatar_item_id', $item_id );

	// If this is a user object pass the users' email address for Gravatar so we don't have to refetch it.
	if ( 'user' == $object && empty( $user_id ) && empty( $email ) && isset( $current_activity_item->user_email ) )
		$email = $current_activity_item->user_email;

	return  bp_core_fetch_avatar( array(
		'item_id' => $item_id,
		'object'  => $object,
		'type'    => $type,
		'alt'     => $alt,
		'class'   => $class,
		'width'   => 70,
		'height'  => 70,
		'email'   => $email
	) ) ;
}
add_filter('bp_get_activity_avatar', 'vh_bp_get_activity_avatar');

// Public JS scripts
if (!function_exists('vh_scripts_method')) {
	function vh_scripts_method() {
		wp_deregister_script( 'prettyphoto');
		wp_register_script( 'prettyphoto', get_template_directory_uri() . '/js/jquery.prettyPhoto.js', array( 'jquery' ), '', true);

		// wp_enqueue_script('jquery');
		if( !is_admin() ) {
			wp_deregister_script('jquery');
			wp_register_script('jquery', "http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js", array(), '1.11.2', false);
			wp_enqueue_script('jquery');
		}

		wp_enqueue_script('prettyphoto');
		wp_enqueue_script('master', get_template_directory_uri() . '/js/master.js', array('jquery', 'prettyphoto'), '', TRUE);
		wp_enqueue_script('isotope', get_template_directory_uri() . '/js/jquery.isotope.min.js', array('jquery', 'master'), '', TRUE);
		wp_enqueue_script('isotope.layout', get_template_directory_uri() . '/js/isotope-masonry-horizontal.js', array('jquery', 'master'), '', TRUE);
		wp_enqueue_script('jquery-ui-tabs');
		
		// wp_enqueue_script('google-maps', '//maps.googleapis.com/maps/api/js?sensor=false', array(), '3', false);
		wp_enqueue_script('jquery.pushy', get_template_directory_uri() . '/js/nav/pushy.js', array('jquery'), '', TRUE);
		wp_enqueue_script('jquery.dots', get_template_directory_uri() . '/js/dots.js', array('jquery'), '', TRUE);
		wp_enqueue_script('jquery.jcarousel', get_template_directory_uri() . '/js/jquery.jcarousel.pack.js', array('jquery'), '', TRUE);

		if ( is_page_template( 'template-featured-slider.php' ) || get_post_type() == 'videogallery' ) {
			wp_enqueue_script('jquery.mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.min.js', array('jquery'), '', TRUE);
			wp_enqueue_script('jquery.kinetic', get_template_directory_uri() . '/js/smoothscroll/jquery.kinetic.js', array('jquery'), '', TRUE);
			wp_enqueue_script('jquery.smoothdivscroll', get_template_directory_uri() . '/js/smoothscroll/jquery.smoothdivscroll.js', array('jquery'), '', TRUE);
		}

		wp_enqueue_script('jquery.cookie', get_template_directory_uri() . '/js/jquery.cookie.js', array('jquery'), '', false);
		wp_enqueue_script('jquery.debouncedresize', get_template_directory_uri() . '/js/jquery.debouncedresize.js', array('jquery'), '', TRUE);

		wp_enqueue_script('modernizr', get_template_directory_uri() . '/js/modernizr.custom.js', array('jquery', 'master'), '', true);
		wp_enqueue_script('uiMorphingButton_fixed', get_template_directory_uri() . '/js/uiMorphingButton_fixed.js', array('jquery', 'master'), '', true);

		wp_enqueue_script( 'jquery-ui-dialog' );
		
		/* Only for contacts page */
		if ( is_page_template('template-contacts.php') ) {
			wp_enqueue_script('infobox', get_template_directory_uri() . '/js/infobox.js', array('jquery'), '', TRUE);
		}

		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		wp_localize_script( 'master', 'ajax_login_object', array( 
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'redirecturl'    => home_url(),
			'loadingmessage' => __('Sending user info, please wait...', 'vh'),
			'registermessage' => __('A password will be emailed to you for future use', "vh" )
		));
		wp_localize_script( 'master', 'my_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		wp_localize_script( 'master', 'follow_button_text', array( 
			'follow' => __('Follow', 'vh'),
			'unfollow' => __('Unfollow', 'vh'),
			'youlikeit' => __('You like it!', 'vh'),
			'showmore' => __('Show more', 'vh'),
			'showless' => __('Show less', 'vh')
		));
	}
}
add_action('wp_enqueue_scripts', 'vh_scripts_method');

// Admin JS scripts

function vh_admin_enqueue() {
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_style('jquery-ui-dialog', get_template_directory_uri() . '/functions/admin/jquery-ui-1.10.4.custom.min.css');
}
add_action( 'admin_enqueue_scripts', 'vh_admin_enqueue' );

// Public CSS files
if (!function_exists('vh_style_method')) {
	function vh_style_method() {
		wp_enqueue_style('master-css', get_stylesheet_directory_uri() . '/style.css');
		wp_enqueue_style('vh-normalize', get_stylesheet_directory_uri() . '/css/normalize.css');

		wp_register_style( 'prettyphoto', get_template_directory_uri() . '/css/prettyPhoto.css', false, '', 'screen' );

		if ( !wp_script_is('js_composer_front', 'enqueued') ) {
			wp_enqueue_style('js_composer_front', get_stylesheet_directory_uri() . '/css/js_composer_front.css');
		}

		// wp_enqueue_style('ch-jcarousel', get_stylesheet_directory_uri() . '/css/jcarousel.css');
		wp_enqueue_style('js_composer_front');
		wp_enqueue_style('prettyphoto');
		// wp_enqueue_style('superfish', get_stylesheet_directory_uri() . '/css/nav/superfish.css');
		wp_enqueue_style('vh-responsive', get_stylesheet_directory_uri() . '/css/responsive.css');
		wp_enqueue_style('pushy', get_stylesheet_directory_uri() . '/css/nav/pushy.css');

		wp_enqueue_style('component', get_stylesheet_directory_uri() . '/css/component.css');

		// wp_enqueue_style('prettycheckable', get_stylesheet_directory_uri() . '/css/prettyCheckable.css');

		// Load google fonts
		if (file_exists(TEMPLATEPATH . '/css/gfonts.css')) {
			wp_enqueue_style('front-gfonts', get_template_directory_uri() . '/css/gfonts.css');
		}

		/* Color scheme css */
		wp_enqueue_style('color-schemes-green', get_template_directory_uri() . '/css/color-schemes/green.css');
		wp_enqueue_style('color-schemes-yellow', get_template_directory_uri() . '/css/color-schemes/yellow.css');
		wp_enqueue_style('color-schemes-red', get_template_directory_uri() . '/css/color-schemes/red.css');
		wp_enqueue_style('color-schemes-gray', get_template_directory_uri() . '/css/color-schemes/gray.css');

	}
}
add_action('wp_enqueue_scripts', 'vh_style_method');

function ajax_login() {

	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-login-nonce', 'security' );

	// Nonce is checked, get the POST data and sign user on
	$info                  = array();
	$info['user_login']    = $_POST['username'];
	$info['user_password'] = $_POST['password'];
	$info['remember']      = true;

	$user_signon = wp_signon( $info, false );
	if ( is_wp_error($user_signon) ){
		echo json_encode(array('loggedin' => false, 'message' => __('Wrong username or password.', 'vh')));
	} else {
		echo json_encode(array('loggedin' => true, 'message' => __('Login successful, redirecting...', 'vh')));
	}

	die(1);
}

// Enable the user with no privileges to run ajax_login() in AJAX
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
add_action( 'wp_ajax_ajaxlogin', 'ajax_login' );

/* Filter categories */
function filter_categories($list) {

	$find    = '(';
	$replace = '[';
	$list    = str_replace( $find, $replace, $list );
	$find    = ')';
	$replace = ']';
	$list    = str_replace( $find, $replace, $list );

	return $list;
}
add_filter('wp_list_categories', 'filter_categories');

// Custom Login Logo
function vh_login_logo() {
	$login_logo = get_option('vh_login_logo');

	if ($login_logo != false) {
		echo '
	<style type="text/css">
		#login h1 a { background-image: url("' . $login_logo . '") !important; }
	</style>';
	}
}
add_action('login_head', 'vh_login_logo');

// Admin CSS
function vh_admin_css() {
	wp_enqueue_style( 'vh-admin-css', get_template_directory_uri() . '/functions/admin/css/wp-admin.css' );
}

add_action('admin_head','vh_admin_css');

// Sets the post excerpt length to 40 words.
function vh_excerpt_length($length) {
	return 39;
}
add_filter('excerpt_length', 'vh_excerpt_length');

// Change excerpt for custom post type
function vh_videos_excerpt( $excerpt ) {
	global $post;

	if (get_post_type() == 'videogallery' || get_post_type() === 'videogallery') {
		$excerpt = __('No excerpt for this video.', 'vh');
	}
	
	return $excerpt;
}
add_filter( 'get_the_excerpt', 'vh_videos_excerpt' );

// Returns a "Continue Reading" link for excerpts
function vh_continue_reading_link() {
	return ' </p><p><a href="' . esc_url(get_permalink()) . '" class="read_more_link">' . __('Read more', 'vh') . '</a>';
}

// // Remove read more link
// function vh_auto_excerpt_more($more) {
// 	return ' </p><p><a href="' . esc_url(get_permalink()) . '" class="read_more_link">' . __('Read more', 'vh') . '</a>';
// }
// add_filter('excerpt_more', 'vh_auto_excerpt_more');

function my_widget_class($params) {

	// its your widget so you add  your classes
	$classe_to_add = (strtolower(str_replace(array(' '), array(''), $params[0]['widget_name']))); // make sure you leave a space at the end
	$classe_to_add = 'class=" '.$classe_to_add . ' ';
	$params[0]['before_widget'] = str_replace('class="', $classe_to_add, $params[0]['before_widget']);

	return $params;
}
add_filter('dynamic_sidebar_params', 'my_widget_class');

// add_filter('widget_title', vh_my_title);
// function vh_my_title($title) {
// 	$title_parts = explode(' ', $title);
// 	$title = $title_parts[0].' '.'<strong>';

// 	for ($i=1; $i < count($title_parts); $i++) { 
// 		$title .= ' ' . $title_parts[$i];
// 	}
// 	$title .= '</strong>';
//     return $title;
// }

function videostream_detectmobile()
{
	$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';

	$mobile_browser = '0';

	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);

	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', $agent))
		$mobile_browser++;

	if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))
		$mobile_browser++;

	if(isset($_SERVER['HTTP_X_WAP_PROFILE']))
		$mobile_browser++;

	if(isset($_SERVER['HTTP_PROFILE']))
		$mobile_browser++;

	$mobile_ua = mb_substr($agent,0,4);
	$mobile_agents = array(
						'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
						'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
						'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
						'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
						'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
						'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
						'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
						'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
						'wapr','webc','winw','xda','xda-'
						);

	if(in_array($mobile_ua, $mobile_agents))
		$mobile_browser++;

	if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
		$mobile_browser++;

	// Pre-final check to reset everything if the user is on Windows
	if(strpos($agent, 'windows') !== false)
		$mobile_browser=0;

	// But WP7 is also Windows, with a slightly different characteristic
	if(strpos($agent, 'windows phone') !== false)
		$mobile_browser++;

	if($mobile_browser>0)
		return true;
	else
		return false;
}

function vh_ldc_like_counter_p( $text="Likes: ",$post_id=NULL ) {
	global $post;
	$ldc_return = '';

	if( empty($post_id) ) {
		$post_id = $post->ID;
	}

	if ( function_exists('get_post_ul_meta') ) {
		$ldc_return = "<span class='ldc-ul_cont_likes' onclick=\"alter_ul_post_values(this,'$post_id','like')\" >".$text."<span>".get_post_ul_meta($post_id,"like")."</span></span>";
	}

	return $ldc_return;
}

function vh_ldc_like_counter_v( $text="Likes: ",$post_id=NULL ) {
	global $post;
	$ldc_return = '';

	if( empty($post_id) ) {
		$post_id = $post->ID;
	}

	if ( function_exists('get_post_ul_meta') ) {
		if(isset($_COOKIE['ul_post_cnt'])) {
			$posts_present=$_COOKIE['ul_post_cnt'];
		} else {
			$posts_present=array();
		}
		if(in_array($post_id,$posts_present)) {
			$video_liked = ' liked';
			$text = __('You like it!', 'vh');
		} else {
			$video_liked = '';
		}
		$ldc_return = "<span class='ldc-ul_cont_likes icon-heart-empty".$video_liked."' onclick=\"alter_ul_post_values(this,'$post_id','like')\" >".$text."</span>";
	}

	return $ldc_return;
}

function vh_ldc_dislike_counter_p( $text="dislikes: ",$post_id=NULL ) {
	global $post;
	$ldc_return = '';

	if( empty($post_id) ) {
		$post_id = $post->ID;
	}

	if ( function_exists('get_post_ul_meta') ) {
		$ldc_return = "<span class='ldc-ul_cont_dislikes' onclick=\"alter_ul_post_values(this,'$post_id','dislike')\" >".$text."<span>".get_post_ul_meta($post_id,"dislike")."</span></span>";
	}
	
	return $ldc_return;
}

function vh_wp_tag_cloud_filter($return, $args) {
	return '<div class="tag_cloud_' . $args['taxonomy'] . '">' . $return . '</div>';
}
add_filter('wp_tag_cloud', 'vh_wp_tag_cloud_filter', 10, 2);
 
function vh_tribe_next_month_filter ( $input ) {
	$new_string = str_replace(' &raquo;', '', $input );
	return $new_string;
}
add_filter( 'tribe_events_the_next_month_link', 'vh_tribe_next_month_filter' );

function vh_tribe_previous_month_filter ( $input ) {
	$new_string = str_replace('&laquo; ', '', $input );
	return $new_string;
}
add_filter( 'tribe_events_the_previous_month_link', 'vh_tribe_previous_month_filter' );

// Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
function vh_page_menu_args($args) {
	$args['show_home'] = true;
	return $args;
}
add_filter('wp_page_menu_args', 'vh_page_menu_args');

function localization() {
	$lang = get_template_directory() . '/languages';
	load_theme_textdomain('vh', $lang);
}
add_action('after_setup_theme', 'localization');

function vh_check_watched_video( $videoId ) {
	if ( is_user_logged_in() && get_user_meta(get_current_user_id(), 'followed_video_categories', true) != '' ) {
		$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
		foreach ($user_followed_categories['videos'] as $key => $value) {
			foreach ($value as $vid_key => $vid_value) {
				if ( $vid_value['video_id'] == $videoId && $vid_value['watched'] == '0' ) {
					$value[$vid_key]['watched'] = '1';
					$user_followed_categories['videos'][$key] = $value;
				}
			}
		}
		update_user_meta(get_current_user_id(), 'followed_video_categories', json_encode($user_followed_categories));
	}
}

function vh_count_followed_videos( $followed_categories ) {
	$followed_videos = 0;

	if ( $followed_categories != '' ) {
		foreach ($followed_categories['videos'] as $video_value) {
			$followed_videos += count($video_value);
		}
	}

	return $followed_videos;
}

function vh_sortByOrder($a, $b) {
    return $a['watched'] - $b['watched'];
}

function vh_check_followed_categories() {
	global $wpdb;
	if ( is_user_logged_in() && get_user_meta(get_current_user_id(), 'followed_video_categories', true) != '' ) {
		$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
		foreach ($user_followed_categories['followed_categories'] as $value) {
			$old_videos = array();
			$new_videos = array();
			$current_videos = array();
			$sql = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare AS w
					INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play AS m ON m.media_id = w.vid
					INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist AS p on m.playlist_id = p.pid
					INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=w.slug
					WHERE w.publish='1' AND p.is_publish='1' AND m.playlist_id=".intval($value)." GROUP BY w.vid DESC";
			$playLists      = $wpdb->get_results($sql);

			foreach ($playLists as $p_value) {
				$current_videos[] = $p_value->vid;
			}

			foreach ($user_followed_categories['videos'][$value] as $key => $o_value) {
				$old_videos[] = $o_value['video_id'];
			}

			if ( count($current_videos) > count($old_videos) ) {
				$new_videos = array_diff($current_videos, $old_videos);
			
				if ( !empty($new_videos) ) {
					foreach ($new_videos as $n_value) {
						$user_followed_categories['videos'][$value][] = array('video_id' => $n_value, 'watched' => '0');
					}
					update_user_meta(get_current_user_id(), 'followed_video_categories', json_encode($user_followed_categories));
				}
			} else {
				$removable_videos = array_diff($old_videos, $current_videos);
				if ( !empty($removable_videos) ) {
					foreach ($user_followed_categories['videos'][$value] as $key => $r_value) {
						foreach ($removable_videos as $re_value) {
							if ( $r_value['video_id'] == $re_value) {
								unset($user_followed_categories['videos'][$value][$key]);
							}
						}
					}
					update_user_meta(get_current_user_id(), 'followed_video_categories', json_encode($user_followed_categories));
				}
			}

		}
	}
}

function vh_delete_followed_category( $category_id ) {
	$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
	
	foreach ($user_followed_categories['followed_categories'] as $key => $value) {
		if ( $value == $category_id ) {
			unset($user_followed_categories['followed_categories'][$key]);
			unset($user_followed_categories['videos'][$category_id]);
		}
	}
	if ( empty($user_followed_categories['followed_categories']) ) {
		update_user_meta(get_current_user_id(), 'followed_video_categories', '');
	} else {
		update_user_meta(get_current_user_id(), 'followed_video_categories', json_encode($user_followed_categories));
	}
}

add_action( 'wp_ajax_nopriv_followed_categories', 'vh_update_followed_categories' );
add_action( 'wp_ajax_followed_categories', 'vh_update_followed_categories' );
function vh_update_followed_categories() {
	$category_id = sanitize_text_field($_POST['category_id']);
	$action = sanitize_text_field($_POST['follow_action']);
	global $wpdb;

	if ( $action == 'add' ) {
		if ( get_user_meta(get_current_user_id(), 'followed_video_categories', true) == '' ) {
			$category_videos = $wpdb->get_results("SELECT v.vid, v.publish, p.media_id, p.playlist_id FROM " . $wpdb->prefix . "hdflvvideoshare v, " . $wpdb->prefix . "hdflvvideoshare_med2play p WHERE v.publish=1 AND p.media_id=v.vid AND p.playlist_id=" . $category_id . "");
			$followed_video_json = array();

			foreach ($category_videos as $value) {
				$followed_video_json[] = array('video_id' => $value->vid, 'watched' => '1');
			}

			$new_user_meta = array('followed_categories' => array($category_id), 'videos' => array( $category_id => $followed_video_json ));
			update_user_meta(get_current_user_id(), 'followed_video_categories', json_encode($new_user_meta));
		} else {
			$category_videos = $wpdb->get_results("SELECT v.vid, v.publish, p.media_id, p.playlist_id FROM " . $wpdb->prefix . "hdflvvideoshare v, " . $wpdb->prefix . "hdflvvideoshare_med2play p WHERE v.publish=1 AND p.media_id=v.vid AND p.playlist_id=" . $category_id . "");
			$followed_video_json = array();

			foreach ($category_videos as $value) {
				$followed_video_json[] = array('video_id' => $value->vid, 'watched' => '1');
			}

			$old_meta = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);

			$old_meta['videos'][$category_id] = $followed_video_json;
			$old_meta['followed_categories'][] = $category_id;

			update_user_meta(get_current_user_id(), 'followed_video_categories', json_encode($old_meta));
		}
	} else {
		vh_delete_followed_category( $category_id );
	}
	
	die(1);
}

// function theme_localization() {
//     // Load Theme textdomain
//     load_theme_textdomain('vh', get_template_directory() . '/languages');

//     // Include Theme text translation file
//     $locale = get_locale();
//     $locale_file = get_template_directory() . "/languages/$locale.php";
//     if ( is_readable( $locale_file ) ) {
//         require_once( $locale_file );
//     }
// }
// add_action( 'after_setup_theme', 'theme_localization' );

// function vh_tag_cloud_class($tag_string, $args) {
// 	foreach ( $args as $key => $value ) {
// 		$tag_string = preg_replace("/tag-link-(" . $value->id . ")/", $value->slug, $tag_string);
// 	}
// 	return $tag_string;
// }
// add_filter('wp_generate_tag_cloud', 'vh_tag_cloud_class', 10, 3);

// Register menus
function register_vh_menus () {
	register_nav_menus(
		array (
			'primary-menu' => __('Primary Menu', 'vh'),
			'footer-menu'  => __('Footer Menu', 'vh')
		)
	);
}
add_action('init', 'register_vh_menus');

// Adds classes to the array of body classes.
function vh_body_classes($classes) {
	global $wp_version;

	if ( class_exists('RevSliderOutput') ) {
		$slider_widget = get_option('widget_rev-slider-widget');

		if ( !empty($slider_widget) ) {
			$rev_slider_options = array_values(get_option('widget_rev-slider-widget'));
			$rev_slider_options = $rev_slider_options[0];

			$homepageCheck = $rev_slider_options["rev_slider_homepage"];
			$homepage = "";
			if($homepageCheck == "on") {
				$homepage = "homepage";
			}

			$pages = $rev_slider_options["rev_slider_pages"];
			if(!empty($pages)){
				if(!empty($homepage)){
					$homepage .= ",";
				}
				$homepage .= $pages;
			}
			$rev_slider = new RevSliderOutput();

			if ( $rev_slider->isPutIn($homepage) && !empty($homepage) ) {
				$classes[] = 'page-with-rev-slider-widget';
			}
		}
	}

	$thumbnail_tooltip_inside = get_option('vh_thumbnail_tooltip_inside') ? get_option('vh_thumbnail_tooltip_inside') : 'false';
	if ( $thumbnail_tooltip_inside == 'false') {
		$classes[] = 'thumbnail_tooltip_inside';
	}

	if (is_singular() && !is_home()) {
		$classes[] = 'singular';
	}

	if (is_search()) {
		$search_key = array_search('search', $classes);
		if ($search_key !== false) {
			unset($classes[$search_key]);
		}
	}

	// Color scheme class
	$vh_color_scheme = get_theme_mod( 'vh_color_scheme');

	if ( !empty($vh_color_scheme) ) {
		$classes[] = $vh_color_scheme;
	}

	// If blog shortcode
	global $post;
	if (isset($post->post_content) && false !== stripos($post->post_content, '[blog')) {
		$classes[] = 'page-template-blog';
	}

	// If video_category shortcode (categories)
	if (isset($post->post_content) && false !== stripos($post->post_content, '[video_category')) {
		$classes[] = 'page-template-categories';
	}
	
	// $fixed_menu = get_option('vh_fixed_menu') ? get_option('vh_fixed_menu') : 'true';
	// if ( $fixed_menu == 'true' ) {
	// 	$classes[] = 'fixed_menu';
	// }

	// Breadcrumbs class
	$disable_breadcrumb = get_option('vh_breadcrumb') ? get_option('vh_breadcrumb') : 'false';
	if (!is_home() && !is_front_page() && $disable_breadcrumb == 'false') {
		$classes[] = 'has_breadcrumb';
	}

	if ( $disable_breadcrumb == 'true' ) {
		$classes[] = 'breadcrumbs-disabled';
	}

	if ( version_compare($wp_version, '4.4', '>=') ) {
		$classes[] = 'wp-post-4-4';
	}

	return $classes;
}
add_filter('body_class', 'vh_body_classes');

function vh_css_settings() {

	// Vars
	$css        = array();
	$custom_css = get_option('vh_custom_css');

	// Custom CSS
	if(!empty($custom_css)) {
		array_push($css, $custom_css);
	}

	echo "
		<!-- Custom CSS -->
		<style type='text/css'>\n";

	if(!empty($css)) {
		foreach($css as $css_item) {
			echo $css_item . "\n";
		}
	}

	$fonts[SHORTNAME . "_primary_font_dark"] = ' html .main-inner p, .ac-device .description, .pricing-table .pricing-content .pricing-desc-1, body .vc_progress_bar .vc_single_bar .vc_label, .page-wrapper .member-desc, .page-wrapper .member-position, .page-wrapper .main-inner ul:not(.ui-tabs-nav) li, .commentlist .comment-content p, #comments-title, .page-wrapper .bg-style-2 p, #video_options .vc_message_box, .submit_video_container #playlistchecklist td, .submit_video_container, .wrapper .vc_gitem-col .entry-content, .video-page-desc, #author-description';
	$fonts[SHORTNAME . "_sidebar_font_dark"] = ' .sidebar-inner, .snaptube-contactform.widget input:not(.btn), .snaptube-recentpostsplus.widget .news-item p, .wrapper .text.widget p, .wrapper .sidebar-inner a';
	$fonts[SHORTNAME . "_headings_font_h1"]  = ' .wrapper h1';
	$fonts[SHORTNAME . "_headings_font_h2"]  = ' .page-wrapper h2, h2, .content .entry-title, .teaser_grid_container .post-title, .wrapper .wpb_thumbnails .isotope-item .post-title_nothumbnail a';
	$fonts[SHORTNAME . "_headings_font_h3"]  = ' .wrapper h3';
	$fonts[SHORTNAME . "_headings_font_h4"]  = ' .wrapper h4';
	$fonts[SHORTNAME . "_headings_font_h5"]  = ' .wrapper h5';
	$fonts[SHORTNAME . "_headings_font_h6"]  = ' .wrapper h6';
	$fonts[SHORTNAME . "_links_font"]        = ' .wpb_wrapper a:not(.videoHname), #author-link a';
	$fonts[SHORTNAME . "_widget"]            = ' .wrapper .sidebar-inner .item-title-bg h4, .wrapper .sidebar-inner .widget-title, .wrapper h3.widget-title a';
	$fonts[SHORTNAME . "_page_title"]        = ' body .wrapper .page-title h1';
	$fonts[SHORTNAME . "_video_title"]       = ' .video-page-info .page_title, .wrapper .video_player ul.video_module .vid_info > span a, .video_player.vid_thumbnail .video_module li .video_container .imgSidethumb .video_thumb_info, .wrapper .video_c_player ul.video_module .vid_info > span a, #video_jcarousel span, .wrapper .vc_gitem-col .post-title a, .wrapper .video-block-container.open-video .vid_info a, .video-block-container.video-home li .vid_info span, .more-author-videos .vid_info .videoHname, .suggested-video-container .vid_info .videoHname';
	$fonts[SHORTNAME . "_side_menu"]         = ' .sidebar_menu ul.primary-menu li a';

	// Custom fonts styling
	foreach ($fonts as $key => $font) {
		$output                 = '';
		$current['font-family'] = get_option($key . '_font_face');
		$current['font-size']   = get_option($key . '_font_size');
		$current['line-height'] = get_option($key . '_line_height');
		$current['color']       = get_option($key . '_font_color');
		$current['font-weight'] = get_option($key . '_weight');

		foreach ($current as $kkey => $item) {

			if ( $key == SHORTNAME . '_widget' ) {
				if (!empty($item)) {

					if ($kkey == 'font-size' || $kkey == 'line-height') {
						$ending = 'px';
					} else if ($kkey == 'color') {
						$before = '#';
					} else if ($kkey == 'font-family') {
						$before = "'";
						$ending = "'";
						$item   = str_replace("+", " ", $item);
					} else if ($kkey == 'font-weight' && $item == 'italic') {
						$kkey = 'font-style';
					} else if ($kkey == 'font-weight' && $item == 'bold_italic') {
						$kkey = 'font-style';
						$item = 'italic; font-weight: bold';
					}


					$output .= " " . $kkey . ": " . $before . $item . $ending . ";";
				}

			}

			$ending = '';
			$before = '';
			if (!empty($item) && $key != SHORTNAME . '_widget') {

				if ($kkey == 'font-size' || $kkey == 'line-height') {
					$ending = 'px';
				} else if ($kkey == 'color') {
					$before = '#';
				} else if ($kkey == 'font-family') {
					$before = "'";
					$ending = "'";
					$item   = str_replace("+", " ", $item);
				} else if ($kkey == 'font-weight' && $item == 'italic') {
					$kkey = 'font-style';
				} else if ($kkey == 'font-weight' && $item == 'bold_italic') {
					$kkey = 'font-style';
					$item = 'italic; font-weight: bold';
				}


				$output .= " " . $kkey . ": " . $before . $item . $ending . ";";
			}
		}


		if ( !empty($output) && !empty($font) && $key != SHORTNAME . '_widget' ) {
			echo $font . ' { ' . $output . ' }';
		}
		if ( !empty($output) && !empty($font) && $key == SHORTNAME . '_widget' ) {
			echo '@media (min-width: 1200px) { ' . $font . ' { ' . $output . ' } } ';
		}
	}

	echo "</style>\n";

}
add_action('wp_head','vh_css_settings', 99);

if (!function_exists('vh_posted_on')) {

	// Prints HTML with meta information for the current post.
	function vh_posted_on() {
		printf(__('<span>Posted: </span><a href="%1$s" title="%2$s" rel="bookmark">%4$s</a><span class="by-author"> by <a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span>', 'vh'),
			esc_url(get_permalink()),
			esc_attr(get_the_time()),
			esc_attr(get_the_date('c')),
			esc_html(get_the_date()),
			esc_url(get_author_posts_url(get_the_author_meta('ID'))),
			sprintf(esc_attr__('View all posts by %s', 'vh'), get_the_author()),
			esc_html(get_the_author())
		);
	}
}

function clear_nav_menu_item_id($id, $item, $args) {
	return "";
}
add_filter('nav_menu_item_id', 'clear_nav_menu_item_id', 10, 3);

function add_nofollow_cat( $text ) {
	$text = str_replace('rel="category"', "", $text);
	return $text;
}
add_filter( 'the_category', 'add_nofollow_cat' );

function ajax_contact() {
	if(!empty($_POST)) {
		$sitename = get_bloginfo('name');
		$siteurl  = home_url();
		$to       = isset($_POST['contact_to'])? trim($_POST['contact_to']) : '';
		$name     = isset($_POST['contact_name'])? trim($_POST['contact_name']) : '';
		$email    = isset($_POST['contact_email'])? trim($_POST['contact_email']) : '';
		$content  = isset($_POST['contact_content'])? trim($_POST['contact_content']) : '';

		$error = false;
		$error = ($to === '' || $email === '' || $content === '' || $name === '') ||
				 (!preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $email)) ||
				 (!preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $to));

		if($error == false) {
			$subject = "$sitename message from $name";
			$body    = "Site: $sitename ($siteurl) \n\nName: $name \n\nEmail: $email \n\nMessage: $content";
			$headers = "From: $name ($sitename) <$email>\r\nReply-To: $email\r\n";
			$sent    = wp_mail($to, $subject, $body, $headers);

			// If sent
			if ($sent) {
				echo 'sent';
				die();
			} else {
				echo 'error';
				die();
			}
		} else {
			echo _e('Please fill all fields!', 'vh');
			die();
		}
	}
}
add_action('wp_ajax_nopriv_contact_form', 'ajax_contact');
add_action('wp_ajax_contact_form', 'ajax_contact');

function addhttp($url) {
	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		$url = "http://" . $url;
	}
	return $url;
}

function checkShortcode($string) {
	global $post;
	if (isset($post->post_content) && false !== stripos($post->post_content, $string)) {
		return true;
	} else {
		return false;
	}
}

// custom comment fields
function vh_custom_comment_fields($fields) {
	global $post, $commenter;

	$fields['author'] = '<div class="comment_auth_email"><p class="comment-form-author">
							<input id="author" name="author" type="text" class="span4" placeholder="' . __( 'Name', 'vh' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" aria-required="true" size="30" />
						 </p>';

	$fields['email'] = '<p class="comment-form-email">
							<input id="email" name="email" type="text" class="span4" placeholder="' . __( 'Email', 'vh' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" aria-required="true" size="30" />
						</p></div>';

	$fields['url'] = '<p class="comment-form-url">
						<input id="url" name="url" type="text" class="span4" placeholder="' . __( 'Website', 'vh' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />
						</p>';

	$fields = array( $fields['author'], $fields['email'] );
	return $fields;
}
add_filter( 'comment_form_default_fields', 'vh_custom_comment_fields' );

if ( ! function_exists( 'vh_comment' ) ) {
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own ac_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 */
	function vh_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'vh' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'vh' ), '<span class="edit-link button blue">', '</span>' ); ?></p>
		<?php
				break;
			default :
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<div id="comment-<?php comment_ID(); ?>" class="comment">
				<div class="comment-meta">
					<div class="comment-author vcard">
						<?php
							$avatar_size = 70;
							echo get_avatar( $comment, $avatar_size );							
						?>
					</div><!-- .comment-author .vcard -->
				</div>

				<div class="comment-content">
					<?php echo '<span class="fn">' . get_comment_author_link() . '</span>' ?>
						<?php if ( $comment->comment_approved == '0' ) : ?>
						<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'vh' ); ?></em>
					<?php endif; ?>
					<?php comment_text(); ?>
					<div class="reply-edit-container">
						<span class="reply">
							<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'vh' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						</span><!-- end of reply -->
						<?php edit_comment_link( __( 'Edit', 'vh' ), '<span class="edit-link button blue">', '</span>' ); ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
			</div><!-- end of comment -->

		<?php
				break;
		endswitch;
	}
}

function vh_breadcrumbs() {

	$disable_breadcrumb = get_option('vh_breadcrumb') ? get_option('vh_breadcrumb') : 'false';
	$delimiter          = get_option('vh_breadcrumb_delimiter') ? get_option('vh_breadcrumb_delimiter') : '<span class="delimiter">></span>';

	$home   = 'Home'; // text for the 'Home' link
	$before = '<span class="current">'; // tag before the current crumb
	$after  = '</span>'; // tag after the current crumb

	if (!is_home() && !is_front_page() && $disable_breadcrumb == 'false') {
		global $post;
		$homeLink = home_url();

		$output = '<div class="breadcrumb">';
		$output .= '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

		if (is_category()) {
			global $wp_query;
			$cat_obj   = $wp_query->get_queried_object();
			$thisCat   = $cat_obj->term_id;
			$thisCat   = get_category($thisCat);
			$parentCat = get_category($thisCat->parent);
			if ($thisCat->parent != 0)
				$output .= get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');
			$output .= $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
		} elseif (is_day()) {
			$output .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			$output .= '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
			$output .= $before . get_the_time('d') . $after;
		} elseif (is_month()) {
			$output .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			$output .= $before . get_the_time('F') . $after;
		} elseif (is_year()) {
			$output .= $before . get_the_time('Y') . $after;
		} elseif (is_single() && !is_attachment()) {
			if (get_post_type() != 'post') {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				if ( $slug['slug'] != 'videogallery' ) {
					$output .= '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
				}
				$output .= $before . get_the_title() . $after;
			} else {
				$cat = get_the_category();
				$cat = $cat[0];
				$output .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
				$output .= $before . get_the_title() . $after;
			}
		} elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
			$post_type = get_post_type_object(get_post_type());

			if (!is_object($post_type)) {
				$post_type = new stdClass;
			}
			if ( empty($post_type->labels) ) {
				$post_type->labels = new stdClass;
			}
			if ( empty($post_type->labels->singular_name) ) {
				$post_type->labels->singular_name = '';
			}

			$output .= $before . $post_type->labels->singular_name . $after;
		} elseif (is_attachment()) {
			$parent = get_post($post->post_parent);
			$cat    = get_the_category($parent->ID);
			if ( isset($cat[0]) ) {
				$cat = $cat[0];
			}

			//$output .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
			$output .= '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
			$output .= $before . get_the_title() . $after;
		} elseif (is_page() && !$post->post_parent) {
			$output .= $before . get_the_title() . $after;
		} elseif (is_page() && $post->post_parent) {
			$parent_id   = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page          = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id     = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb) {
				$output .= $crumb . ' ' . $delimiter . ' ';
			}
			$output .= $before . get_the_title() . $after;
		} elseif (is_search()) {
			$output .= $before . 'Search results for "' . get_search_query() . '"' . $after;
		} elseif (is_tag()) {
			$output .= $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
		} elseif (is_author()) {
			global $vh_author;
			$userdata = get_userdata($vh_author);
			$output .= $before . 'Articles posted by ' . get_the_author() . $after;
		} elseif (is_404()) {
			$output .= $before . 'Error 404' . $after;
		}

		if (get_query_var('paged')) {
			if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
				$output .= ' (';
			$output .= __('Page', 'vh') . ' ' . get_query_var('paged');
			if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
				$output .= ')';
		}

		$output .= '</div>';

		return $output;
	}
}

/*
 * This theme supports custom background color and image, and here
 * we also set up the default background color.
 */
add_theme_support( 'custom-background', array(
	'default-color' => 'fff'
) );

/**
 * Add postMessage support for the Theme Customizer.
 */
function vh_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->add_section( 'color_scheme_section', array(
		'title'    => __( 'Color Scheme', 'vh' ),
		'priority' => 35,
	) );

	$wp_customize->add_setting( 'vh_color_scheme', array(
		'default'   => 'default-color-scheme',
		'transport' => 'postMessage'
	) );

	$wp_customize->add_control( new Customize_Scheme_Control( $wp_customize, 'vh_color_scheme', array(
		'label'    => 'Choose color scheme',
		'section'  => 'color_scheme_section',
		'settings' => 'vh_color_scheme',
	) ) );
}
add_action( 'customize_register', 'vh_customize_register' );

/**
 * Binds CSS and JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function vh_customize_preview_js_css() {
	wp_enqueue_script( 'vh-customizer-js', get_template_directory_uri() . '/functions/admin/js/theme-customizer.js', array( 'jquery', 'customize-preview' ), '', true );
}
add_action( 'customize_preview_init', 'vh_customize_preview_js_css' );

if (class_exists('WP_Customize_Control')) {
	class Customize_Scheme_Control extends WP_Customize_Control {
		public $type = 'radio';

		public function render_content() {
		?>
			<style>

				/* Customizer */
				.input_hidden {
					position: absolute;
					left: -9999px;
				}

				.radio-images img {
					margin-right: 4px;
					border: 2px solid #fff;
				}

				.radio-images img.selected {
					border: 2px solid #888;
					border-radius: 5px;
				}

				.radio-images label {
					display: inline-block;
					cursor: pointer;
				}
			</style>
			<script type="text/javascript">
				jQuery('.radio-images input:radio').addClass('input_hidden');
				jQuery('.radio-images img').live('click', function() {
					jQuery('.radio-images img').removeClass('selected');
					jQuery(this).addClass('selected');
				});
			</script>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="radio-images">
				<input type="radio" class="input_hidden" name="vh_color_scheme" <?php $this->link(); ?> id="default-color-scheme" value="default-color-scheme" />
				<label for="default-color-scheme">
					<img src="<?php echo get_template_directory_uri() . '/functions/admin/images/schemes/color-scheme-default.png'; ?>"<?php echo ( $this->value() == 'default-color-scheme' ) ? ' checked="checked" class="selected"' : ''; ?> style="width: 50px; height: 50px;" alt="Default Color Scheme" />
				</label>
				<input type="radio" class="input_hidden" name="vh_color_scheme" <?php $this->link(); ?> id="green-color-scheme" value="green-color-scheme" />
				<label for="green-color-scheme">
					<img src="<?php echo get_template_directory_uri() . '/functions/admin/images/schemes/color-scheme-green.png'; ?>"<?php echo ( $this->value() == 'green-color-scheme' ) ? ' checked="checked" class="selected"' : ''; ?> style="width: 50px; height: 50px;" alt="Green Color Scheme" />
				</label>
				<input type="radio" class="input_hidden" name="vh_color_scheme" <?php $this->link(); ?> id="red-color-scheme" value="red-color-scheme" />
				<label for="red-color-scheme">
					<img src="<?php echo get_template_directory_uri() . '/functions/admin/images/schemes/color-scheme-red.png'; ?>"<?php echo ( $this->value() == 'red-color-scheme' ) ? ' checked="checked" class="selected"' : ''; ?> style="width: 50px; height: 50px;" alt="Red Color Scheme" />
				</label>
				<input type="radio" class="input_hidden" name="vh_color_scheme" <?php $this->link(); ?> id="yellow-color-scheme" value="yellow-color-scheme" />
				<label for="yellow-color-scheme">
					<img src="<?php echo get_template_directory_uri() . '/functions/admin/images/schemes/color-scheme-yellow.png'; ?>"<?php echo ( $this->value() == 'yellow-color-scheme' ) ? ' checked="checked" class="selected"' : ''; ?> style="width: 50px; height: 50px;" alt="Yellow Color Scheme" />
				</label>
				<input type="radio" class="input_hidden" name="vh_color_scheme" <?php $this->link(); ?> id="gray-color-scheme" value="gray-color-scheme" />
				<label for="gray-color-scheme">
					<img src="<?php echo get_template_directory_uri() . '/functions/admin/images/schemes/color-scheme-gray.png'; ?>"<?php echo ( $this->value() == 'gray-color-scheme' ) ? ' checked="checked" class="selected"' : ''; ?> style="width: 50px; height: 50px;" alt="Gray Color Scheme" />
				</label>
			</div>
		<?php
		}
	}
}

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function vh_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		array(
			'name'               => 'Snaptube Functionality', // The plugin name
			'slug'               => 'snaptube-plugin', // The plugin slug (typically the folder name)
			'source'             => get_template_directory() . '/functions/tgm-activation/plugins/snaptube-plugin.zip', // The plugin source
			'required'           => true, // If false, the plugin is only 'recommended' instead of required
			'version'            => '2.2.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url'       => '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'               => 'Like Dislike Lite', // The plugin name
			'slug'               => 'like-dislike-counter-for-posts-pages-and-comments', // The plugin slug (typically the folder name)
			'source'             => get_template_directory() . '/functions/tgm-activation/plugins/like-dislike-counter-for-posts-pages-and-comments.zip', // The plugin source
			'required'           => false, // If false, the plugin is only 'recommended' instead of required
			'version'            => '1.3.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url'       => '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'               => 'WPBakery Visual Composer', // The plugin name
			'slug'               => 'js_composer', // The plugin slug (typically the folder name)
			'source'             => get_template_directory() . '/functions/tgm-activation/plugins/js_composer.zip', // The plugin source
			'required'           => true, // If false, the plugin is only 'recommended' instead of required
			'version'            => '4.12', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url'       => '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'               => 'Wordpress Video Gallery', // The plugin name
			'slug'               => 'contus-video-gallery', // The plugin slug (typically the folder name)
			'source'             => get_template_directory() . '/functions/tgm-activation/plugins/contus-video-gallery.zip', // The plugin source
			'required'           => true, // If false, the plugin is only 'recommended' instead of required
			'version'            => '3.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url'       => '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'               => 'WordPress Video Gallery BuddyPress Integration', // The plugin name
			'slug'               => 'snaptube-buddypress-integration', // The plugin slug (typically the folder name)
			'source'             => get_template_directory() . '/functions/tgm-activation/plugins/snaptube-buddypress-integration.zip', // The plugin source
			'required'           => false, // If false, the plugin is only 'recommended' instead of required
			'version'            => '2.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url'       => '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Contact Form 7', // The plugin name
			'slug'     				=> 'contact-form-7', // The plugin slug (typically the folder name)
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '4.4.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		)

	);

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> 'vh',         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_slug'    	=> 'themes.php', 				// Default parent menu slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> true,					   	// Automatically activate plugins after installation or not
		'message' 			=> '<div class="error" style="background-color: #FFFFE0;"><p><strong>IMPORTANT: After updating WordPress Video Gallery plugin don\'t forget to visit and save the <a href="' . admin_url ( "admin.php?page=hdflvvideosharesettings" ) . '">Settings</a> page.</strong></p></div>',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', 'vh' ),
			'menu_title'                       			=> __( 'Install Plugins', 'vh' ),
			'installing'                       			=> __( 'Installing Plugin: %s', 'vh' ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', 'vh' ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', 'vh' ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'vh' ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'vh' ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'vh_register_required_plugins' );


function vh_import_videos() {
	global $wpdb;

	$table_name     = $wpdb->prefix . 'hdflvvideoshare';
	$table_playlist = $wpdb->prefix . 'hdflvvideoshare_playlist';
	$table_med2play = $wpdb->prefix . 'hdflvvideoshare_med2play';
	$table_settings = $wpdb->prefix . 'hdflvvideoshare_settings';
	$table_vgads    = $wpdb->prefix . 'hdflvvideoshare_vgads';
	$table_tags     = $wpdb->prefix . 'hdflvvideoshare_tags';
	$posttable      = $wpdb->prefix . 'posts';

	// Insert sample videos
	$videoCategories = $wpdb->get_results( 'SELECT * FROM ' . $table_name );
	$post_id         = $wpdb->get_var( 'SELECT ID FROM ' . $posttable . ' ORDER BY ID DESC' );
	$postid          = array();

	// for ( $i = 0; $i < 79; $i++ ) {
	// 	$postid[$i] = $post_id + 1;
	// 	$post_id++;
	// }

	$current_user = wp_get_current_user();
	$member_id    = $current_user->ID;

	$wpdb->query("TRUNCATE TABLE ".$table_name);
	$wpdb->query("INSERT INTO " . $table_name . " (`vid`, `name`, `description`, `embedcode`, `file`, `streamer_path`, `hdfile`, `slug`, `file_type`, `duration`, `srtfile1`, `srtfile2`, `subtitle_lang1`, `subtitle_lang2`, `image`, `opimage`, `download`, `link`, `featured`, `hitcount`, `ratecount`, `rate`, `post_date`, `postrollads`, `prerollads`, `midrollads`, `imaad`, `publish`, `islive`, `member_id`, `ordering`) VALUES
				(34, 'Happy people in Tatras', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters.', '', 'http://www.youtube.com/watch?v=sGrHwBlf-7M', '', '', '67', 1, '4:04', '', '', '', '', 'http://i3.ytimg.com/vi/sGrHwBlf-7M/mqdefault.jpg', 'http://i3.ytimg.com/vi/sGrHwBlf-7M/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=sGrHwBlf-7M', '1', 1473, 0, 0, '2014-05-04 08:21:45', '1', '1', 1, 0, 1, 0, " . $member_id . ", 13),
				(35, '3D Animated Short Film', 'This short computer animated film was created by the Blender Institute, part of the Blender Foundation. Like the foundation''s previous film Elephants Dream, the film was made using Blender, a free software application for animation made by the same foundation.', '', 'http://www.youtube.com/watch?v=AcBut-jY0G8', '', '', '68', 1, '10:22', '', '', '', '', 'http://i3.ytimg.com/vi/AcBut-jY0G8/mqdefault.jpg', 'http://i3.ytimg.com/vi/AcBut-jY0G8/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=AcBut-jY0G8', '1', 105, 0, 0, '2014-05-04 08:27:14', '0', '0', 0, 0, 1, 0, " . $member_id . ", 14),
				(32, 'Antarctic Mountain Climbing', 'Accomplished climber Mike Libecki and photographer Cory Richards battle extreme cold and furious katabatic winds in an epic, ten-day climb to the summit of the untouched Bertha''s Tower at the bottom of the world.', '', 'http://www.youtube.com/watch?v=EULc7RgnM4c', '', '', '65', 1, '23:12', '', '', '', '', 'http://i3.ytimg.com/vi/EULc7RgnM4c/mqdefault.jpg', 'http://i3.ytimg.com/vi/EULc7RgnM4c/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=EULc7RgnM4c', '1', 72, 0, 0, '2014-05-04 08:05:21', '0', '0', 0, 0, 1, 0, " . $member_id . ", 12),
				(31, 'Go Where the Locals Go', 'In \"Where the Locals Go,\" you''ll find hundreds of beautifully photographed travel experiences, with nuggets of entertaining and insightful text informed by locals. Leave the tourist trail behind, and make your next trip truly authentic!', '', 'http://www.youtube.com/watch?v=DoCTVac9kn4', '', '', '64', 1, '1:31', '', '', '', '', 'http://i3.ytimg.com/vi/DoCTVac9kn4/mqdefault.jpg', 'http://i3.ytimg.com/vi/DoCTVac9kn4/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=DoCTVac9kn4', '1', 44, 0, 0, '2014-05-04 08:03:35', '0', '0', 0, 0, 1, 0, " . $member_id . ", 11),
				(30, 'Telling the Story', 'Get the inside story behind crafting the text in the world''s best known magazine directly from its renowned writers and editors.\r\n\r\nThe National Geographic Live! series brings thought-provoking presentations by today''s leading explorers, scientists, photographers, and performing artists right to your YouTube feed. Each presentation is filmed in front of a live audience at National Geographic headquarters in Washington, D.C. New clips air every Monday.', '', 'http://www.youtube.com/watch?v=r8YeqTTzBFo', '', '', '63', 1, '25:37', '', '', '', '', 'http://i3.ytimg.com/vi/r8YeqTTzBFo/mqdefault.jpg', 'http://i3.ytimg.com/vi/r8YeqTTzBFo/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=r8YeqTTzBFo', '1', 242, 0, 0, '2014-05-04 08:02:40', '0', '0', 0, 0, 1, 0, " . $member_id . ", 10),
				(29, 'The Aboriginal Homeland', 'Through her stunning photography, Amy Toensing touches upon the Aboriginal Australians'' cultural struggle, but celebrates these indigenous people''s unique way of life and their connection to their ancestral lands.\r\n\r\nUpcoming Events at National Geographic Live!\r\nhttp://events.nationalgeographic.com/events/\r\n\r\nThe National Geographic Live! series brings thought-provoking presentations by today''s leading explorers, scientists, photographers, and performing artists right to your YouTube feed. Each presentation is filmed in front of a live audience at National Geographic headquarters in Washington, D.C. New clips air every Monday.', '', 'http://www.youtube.com/watch?v=Y4PMZtRYmMs', '', '', '62', 1, '21:24', '', '', '', '', 'http://i3.ytimg.com/vi/Y4PMZtRYmMs/mqdefault.jpg', 'http://i3.ytimg.com/vi/Y4PMZtRYmMs/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=Y4PMZtRYmMs', '1', 42, 0, 0, '2014-05-04 08:01:19', '0', '0', 0, 0, 1, 0, " . $member_id . ", 9),
				(85, 'Read the Heart Line', 'Learn how to find and read the heart line from psychotherapist and palm reading expert Ellen Goldberg, M.A. in this Howcast video.\r\n\r\nThere are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.', '', 'http://www.youtube.com/watch?v=3yKjCZP7KUQ', '', '', '131', 1, '5:20', '', '', '', '', 'http://i3.ytimg.com/vi/3yKjCZP7KUQ/mqdefault.jpg', 'http://i3.ytimg.com/vi/3yKjCZP7KUQ/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=3yKjCZP7KUQ', '1', 18, 0, 0, '2014-05-07 14:37:33', '0', '0', 0, 0, 1, 0, " . $member_id . ", 54),
				(28, 'The Reindeer People', 'Spending years in the field, photographer Erika Larsen has gained unprecedented access into the lives, work, and culture of Scandinavia''s fascinating Sami people.\r\n\r\nThe National Geographic Live! series brings thought-provoking presentations by today''s leading explorers, scientists, photographers, and performing artists right to your YouTube feed. Each presentation is filmed in front of a live audience at National Geographic headquarters in Washington, D.C. New clips air every Monday.', '', 'http://www.youtube.com/watch?v=bPiKAhhEHXA', '', '', '61', 1, '22:41', '', '', '', '', 'http://i3.ytimg.com/vi/bPiKAhhEHXA/mqdefault.jpg', 'http://i3.ytimg.com/vi/bPiKAhhEHXA/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=bPiKAhhEHXA', '1', 1108, 0, 0, '2014-05-04 08:00:14', '0', '0', 0, 0, 1, 0, " . $member_id . ", 8),
				(27, 'Running Up Hill', 'Paulie and Tuffy scout a very difficult location to build a cabin.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sit amet fringilla orci. Aenean molestie dolor nibh, non accumsan orci fermentum vitae. Proin bibendum at diam tincidunt fermentum. Nunc venenatis volutpat erat, sit.', '', 'https://www.youtube.com/watch?v=l3qUvdy1Dh8', '', '', '60', 1, '53:48', '', '', '', '', 'http://i3.ytimg.com/vi/l3qUvdy1Dh8/mqdefault.jpg', 'http://i3.ytimg.com/vi/l3qUvdy1Dh8/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=l3qUvdy1Dh8', '0', 19, 0, 0, '2014-05-04 07:59:42', '0', '0', 0, 0, 1, 0, " . $member_id . ", 7),
				(23, 'Lightweight', 'Tim makes it REALLY difficult for a body builder to pick up a woman. NONE OF THE ABOVE AIRS MONDAYS at 10P.', '', 'https://www.youtube.com/watch?v=y_eLZr24iMQ', '', '', '56', 1, '12:43', '', '', '', '', 'http://i3.ytimg.com/vi/y_eLZr24iMQ/mqdefault.jpg', 'http://i3.ytimg.com/vi/y_eLZr24iMQ/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=y_eLZr24iMQ', '0', 32, 0, 0, '2014-05-04 07:55:09', '0', '0', 0, 0, 1, 0, " . $member_id . ", 4),
				(25, 'Replacing Florida''s Stolen Orchids', 'Wild orchids in South Florida were all but eliminated by humans turning them into disposable potted plants, beginning in the 1800s. A comeback is difficult because orchid seeds have only about a one-in-a-million chance of creating a new plant. But there''s an effort to bring the wild orchid population back to its former glory.\r\n\r\nPRODUCER: Gabriella Garcia-Pardo', '', 'http://www.youtube.com/watch?v=jntK5b-BwXI', '', '', '58', 1, '3:50', '', '', '', '', 'http://i3.ytimg.com/vi/jntK5b-BwXI/mqdefault.jpg', 'http://i3.ytimg.com/vi/jntK5b-BwXI/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=jntK5b-BwXI', '0', 8, 0, 0, '2014-05-04 07:56:18', '0', '0', 0, 0, 1, 0, " . $member_id . ", 5),
				(26, 'Foiled by a Balloon', 'What will happen if Tim puts a foil balloon into a microwave?  NONE OF THE ABOVE AIRS MONDAYS at 10P.', '', 'http://www.youtube.com/watch?v=5S4uZDMOQ7A', '', '', '59', 1, '1:06', '', '', '', '', 'http://i3.ytimg.com/vi/5S4uZDMOQ7A/mqdefault.jpg', 'http://i3.ytimg.com/vi/5S4uZDMOQ7A/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=5S4uZDMOQ7A', '0', 14, 0, 0, '2014-05-04 07:57:25', '0', '0', 0, 0, 1, 0, " . $member_id . ", 6),
				(17, 'Big Buck Bunny', 'Big Buck Bunny was the first project in the Blender Institute Amsterdam. This 10 minute movie has been made inspired by the best cartoon tradition.', '', 'http://www.youtube.com/watch?v=Vpg9yizPP_g', '', '', '23', 1, '1:47', '', '', '', '', 'http://i3.ytimg.com/vi/Vpg9yizPP_g/mqdefault.jpg', 'http://i3.ytimg.com/vi/Vpg9yizPP_g/maxresdefault.jpg', '', 'http://www.youtube.com/watch?v=Vpg9yizPP_g', '1', 94, 0, 0, '2013-08-06 13:53:12', '0', '0', 0, 0, 1, 0, " . $member_id . ", 16),
				(22, 'Kayapo Warrior Tribe', 'Portrait photographer Martin Schoeller travels to the remote Brazilian Amazon where the Kayapo people balance the colorful traditions of their heritage and the enticing commodities of the 21st century.\r\n\r\nThe National Geographic Live! series brings thought-provoking presentations by today''s leading explorers, scientists, photographers, and performing artists right to your YouTube feed. Each presentation is filmed in front of a live audience at National Geographic headquarters in Washington, D.C. New clips air every Monday.', '', 'http://www.youtube.com/watch?v=tDuK5Axe0-Y', '', '', '55', 1, '20:43', '', '', '', '', 'http://i3.ytimg.com/vi/tDuK5Axe0-Y/mqdefault.jpg', 'http://i3.ytimg.com/vi/tDuK5Axe0-Y/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=tDuK5Axe0-Y', '0', 115, 0, 0, '2014-05-04 07:53:57', '0', '0', 0, 0, 1, 0, " . $member_id . ", 3),
				(21, 'Exploring the Cosmic Dawn', 'Scientists are getting an unprecedented view of the early universe with the help of ALMA, the Atacama Large Millimeter/submillimeter Array. This group of antennae is the equivalent of a city-size telescopic lens, bringing cosmic nurseries for planets and stars into view.\r\n\r\nLearn more about ALMA and the cosmic dawn:\r\nhttp://www.nationalgeographic.com/cosmic-dawn/\r\n\r\nPHOTOGRAPHY: Dave Yoder, Peter Wintersteller, and Pilar Elorriaga\r\nEDITOR: Hans Weise\r\nMUSIC: John Kusiak', '', 'http://www.youtube.com/watch?v=4I8KJFIuHKE', '', '', '54', 1, '1:15', '', '', '', '', 'http://i3.ytimg.com/vi/4I8KJFIuHKE/mqdefault.jpg', 'http://i3.ytimg.com/vi/4I8KJFIuHKE/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=4I8KJFIuHKE', '0', 3, 0, 0, '2014-05-04 07:50:47', '0', '0', 0, 0, 1, 0, " . $member_id . ", 4),
				(20, 'Window to Your Health', 'Your eyes are tiny spheres of wonder. A doctor can find warning signs of high blood pressure, diabetes, and a whole range of other systemic health issues, just by examining your eyes. Ophthalmologist Neal Adams explains why the eye''s tissues and blood vessels make such a good barometer for wellness.', '', 'http://www.youtube.com/watch?v=BPAbANevTqM', '', '', '53', 1, '2:50', '', '', '', '', 'http://i3.ytimg.com/vi/BPAbANevTqM/mqdefault.jpg', 'http://i3.ytimg.com/vi/BPAbANevTqM/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=BPAbANevTqM', '1', 14, 0, 0, '2014-05-04 07:46:35', '0', '0', 0, 0, 1, 0, " . $member_id . ", 19),
				(36, 'Robinson Crusoe', 'This funny animation short movie was created by several students for their graduation project at ESMA (Montpellier, France).', '', 'http://www.youtube.com/watch?v=TKoB7XQJwlo', '', '', '69', 1, '2:27', '', '', '', '', 'http://i3.ytimg.com/vi/TKoB7XQJwlo/mqdefault.jpg', 'http://i3.ytimg.com/vi/TKoB7XQJwlo/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=TKoB7XQJwlo', '1', 150, 0, 0, '2014-05-04 08:28:55', '1', '1', 1, 0, 1, 0, " . $member_id . ", 15),
				(40, 'Ratatouille', 'Disney''s Ratatouille official movie game LIKE for more videos.', '', 'http://www.youtube.com/watch?v=BV1C4ZNtx2M', '', '', '73', 1, '2:7:42', '', '', '', '', 'http://i3.ytimg.com/vi/BV1C4ZNtx2M/mqdefault.jpg', 'http://i3.ytimg.com/vi/BV1C4ZNtx2M/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=BV1C4ZNtx2M', '1', 39, 0, 0, '2014-05-04 08:57:24', '0', '0', 0, 0, 1, 0, " . $member_id . ", 19),
				(37, 'Caminandes', 'Open Movie produced by Blender Institute - 3D Animated Short Film HD Read more about the Caminandes series on http://www.example.com', '', 'http://www.youtube.com/watch?v=yVh_e_BswDE', '', '', '70', 1, '2:27', '', '', '', '', 'http://i3.ytimg.com/vi/yVh_e_BswDE/mqdefault.jpg', 'http://i3.ytimg.com/vi/yVh_e_BswDE/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=yVh_e_BswDE', '1', 46, 0, 0, '2014-05-04 08:40:34', '0', '0', 0, 0, 1, 0, " . $member_id . ", 16),
				(38, 'Dum Spiro - 3D Animation', 'Dum Spiro is a full 3D animated cartoon movie HD, this animation short film uses Maya created by several students for their graduation project at ESMA (Montpellier, France).', '', 'https://www.youtube.com/watch?v=GJwKoIbBkZs', '', '', '71', 1, '6:22', '', '', '', '', 'http://i3.ytimg.com/vi/GJwKoIbBkZs/mqdefault.jpg', 'http://i3.ytimg.com/vi/GJwKoIbBkZs/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=GJwKoIbBkZs', '1', 33, 0, 0, '2014-05-04 08:41:09', '0', '0', 0, 0, 1, 0, " . $member_id . ", 17),
				(39, 'Animation Movie - Sintel', 'Sintel is the third animation movie produced by the Blender Foundation. This 3D animated short film was directed by Savannah College of Art and Design Student, Colin Levy.', '', 'http://www.youtube.com/watch?v=W3sNH41SeoM', '', '', '72', 1, '3:15', '', '', '', '', 'http://i3.ytimg.com/vi/W3sNH41SeoM/mqdefault.jpg', 'http://i3.ytimg.com/vi/W3sNH41SeoM/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=W3sNH41SeoM', '1', 148, 0, 0, '2014-05-04 08:41:34', '0', '0', 0, 0, 1, 0, " . $member_id . ", 18),
				(41, 'Animation Movie - FAT', 'FAT is a funny animation movie hd made by 3 students in 2011, the film received many selections and awards, please read the description for more details. Feel free to Like or Share the film!', '', 'http://www.youtube.com/watch?v=yltlJEdSAHw', '', '', '74', 1, '2:02', '', '', '', '', 'http://i3.ytimg.com/vi/yltlJEdSAHw/mqdefault.jpg', 'http://i3.ytimg.com/vi/yltlJEdSAHw/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=yltlJEdSAHw', '1', 52, 0, 0, '2014-05-04 08:58:39', '0', '0', 0, 0, 1, 0, " . $member_id . ", 20),
				(42, 'The Smurfs 2', 'The Smurfs 2 Full Movie Game - Video for Kids - Spongebob Squarepants TV! Dora the Explorer TV - Games for Kids 2013 :)', '', 'http://www.youtube.com/watch?v=vQbSGLaVJ5c', '', '', '75', 1, '1:34', '', '', '', '', 'http://i3.ytimg.com/vi/vQbSGLaVJ5c/mqdefault.jpg', 'http://i3.ytimg.com/vi/vQbSGLaVJ5c/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=vQbSGLaVJ5c', '1', 56, 0, 0, '2014-05-04 09:00:33', '0', '0', 0, 0, 1, 0, " . $member_id . ", 21),
				(43, 'Baby Hazel Games', 'Baby Hazel Games - Baby and Kids Games Movie Dora Games for Kids - Dora the Explorer. Kids PC Games Cartoons for Babies. There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour.', '', 'http://www.youtube.com/watch?v=JuyB7NO0EYY', '', '', '76', 1, '4:59', '', '', '', '', 'http://i3.ytimg.com/vi/JuyB7NO0EYY/mqdefault.jpg', 'http://i3.ytimg.com/vi/JuyB7NO0EYY/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=JuyB7NO0EYY', '1', 38, 0, 0, '2014-05-04 09:01:43', '0', '0', 0, 0, 1, 0, " . $member_id . ", 22),
				(44, 'Alice Amv', 'Alice Amv - Alice in Wonderland Full Movie Game 2014 - Full Alice tribute, Gameplays & Trailers Alice Madness Returns.', '', 'http://www.youtube.com/watch?v=yf1trNG2PE0', '', '', '77', 1, '3:57', '', '', '', '', 'http://i3.ytimg.com/vi/yf1trNG2PE0/mqdefault.jpg', 'http://i3.ytimg.com/vi/yf1trNG2PE0/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=yf1trNG2PE0', '1', 53, 0, 0, '2014-05-04 09:02:24', '0', '0', 0, 0, 1, 0, " . $member_id . ", 23),
				(45, 'Cupidon', 'Animation Movie Cupidon is a Animation Short Film HD. This short movie was created by several students for their graduation project at ESMA (Montpellier, France).', '', 'http://www.youtube.com/watch?v=gJ7xINoeFPk', '', '', '78', 1, '3:42', '', '', '', '', 'http://i3.ytimg.com/vi/gJ7xINoeFPk/mqdefault.jpg', 'http://i3.ytimg.com/vi/gJ7xINoeFPk/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=gJ7xINoeFPk', '1', 42, 0, 0, '2014-05-04 09:03:46', '0', '0', 0, 0, 1, 0, " . $member_id . ", 24),
				(46, 'Visit to the Leather Maker', 'Mick has a hard time bartering a tree burl for some leather goods.  THE LEGEND OF MICK DODGE AIRS TUESDAYS at 10P.', '', 'http://www.youtube.com/watch?v=hbuMIj9wrH0', '', '', '79', 1, '2:19', '', '', '', '', 'http://i3.ytimg.com/vi/hbuMIj9wrH0/mqdefault.jpg', 'http://i3.ytimg.com/vi/hbuMIj9wrH0/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=hbuMIj9wrH0', '1', 28, 0, 0, '2014-05-04 14:01:10', '0', '0', 0, 0, 1, 0, " . $member_id . ", 25),
				(47, '3D Short Film', 'Out of Bounds is a graduation film made at The Animation Workshop, Denmark, by Viktoria Piechowitz, Andreea Jebelean, Adrian Walt, Stephen C. Brossman, Laika A. Laursen, Natalia Marcos, Ida Mårtenson, Martin Zauner, Kirsten Bay Nielsen, Oldrich Holy, and Signe T. Schmidt.', '', 'https://www.youtube.com/watch?v=j6PbonHsqW0', '', '', '80', 1, '3:36', '', '', '', '', 'http://i3.ytimg.com/vi/j6PbonHsqW0/mqdefault.jpg', 'http://i3.ytimg.com/vi/j6PbonHsqW0/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=j6PbonHsqW0', '1', 72, 0, 0, '2014-05-04 14:03:28', '0', '0', 0, 0, 1, 0, " . $member_id . ", 26),
				(48, 'Ice Age 4', 'Arctic Games is a video game based on the film of the same name. You need to pick your team. If you pick Manny''s team,you will play as Ellie first (tunnel sliding race), then with Sid against Squint (long jump and glyptodon throw), Granny against Gupta (outdoor sliding race), Diego against Shira (jumping race), Peaches against Raz (slingshot target) and Manny against Gutt in another tunnel sliding game where you must smash the ice with the correct team sign.', '', 'http://www.youtube.com/watch?v=rWxJlfaaKWc', '', '', '81', 1, '10:52', '', '', '', '', 'http://i3.ytimg.com/vi/rWxJlfaaKWc/mqdefault.jpg', 'http://i3.ytimg.com/vi/rWxJlfaaKWc/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=rWxJlfaaKWc', '1', 51, 0, 0, '2014-05-04 14:05:55', '0', '0', 0, 0, 1, 0, " . $member_id . ", 27),
				(51, 'Electroshock', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.\r\n\r\nCreated, Directed & Animated by Hugo Jackson, Pascal Chandelier, Valentin Michel, Bastien MORTELEcque and Elliot Maren. With the voices of Christophe Lemoine, Ariane Aggiage, Michel Vigne, Laetitia Barbara, Philipe Peythieu and Véronique Augereau. Music by Thierry Jaoul, Jose Vicente and Hugo Jackson. Sound Design by José Vicente and Yoann Poncet, Studio des Aviateurs.', '', 'http://www.youtube.com/watch?v=hRHbeYZhtgA', '', '', '84', 1, '8:43', '', '', '', '', 'http://i3.ytimg.com/vi/hRHbeYZhtgA/mqdefault.jpg', 'http://i3.ytimg.com/vi/hRHbeYZhtgA/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=hRHbeYZhtgA', '1', 49, 0, 0, '2014-05-04 14:11:40', '0', '0', 0, 0, 1, 0, " . $member_id . ", 29),
				(52, 'Tolerantia', 'Animals are eukaryotic organisms of the kingdom Animalia or Metazoa. Their body the first 3D animated film produced in Bosnia and Herzegovina. Nominated for the Best European Short film by the European Film Academy 2008. Awarded with 8 more awards including the \"Heart of Sarajevo\" for the best short film on Sarajevo Film Festival 2008.\r\nDirecting, animation, sound - Ivan Ramadan\r\nMusic - traditional (Mostar Sevdah Reunion)', '', 'http://www.youtube.com/watch?v=FrjQrXc80cY', '', '', '85', 1, '6:21', '', '', '', '', 'http://i3.ytimg.com/vi/FrjQrXc80cY/mqdefault.jpg', 'http://i3.ytimg.com/vi/FrjQrXc80cY/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=FrjQrXc80cY', '1', 34, 0, 0, '2014-05-04 14:13:11', '0', '0', 0, 0, 1, 0, " . $member_id . ", 30),
				(53, 'Extraordinary Skies', 'One year—and more than 12,000 photos—in the making, \"Into the Atmosphere\" is photographer Michael Shainblum''s homage to the natural wonders of his native California.', '', 'http://www.youtube.com/watch?v=vLUNWYt3q1w', '', '', '86', 1, '3:46', '', '', '', '', 'http://i3.ytimg.com/vi/vLUNWYt3q1w/mqdefault.jpg', 'http://i3.ytimg.com/vi/vLUNWYt3q1w/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=vLUNWYt3q1w', '1', 7, 0, 0, '2014-05-04 14:40:22', '0', '0', 0, 0, 1, 0, " . $member_id . ", 31),
				(54, 'Norway Northern Lights', 'March 21, 2012 — Multicolored curtains of light fill the skies over northern Norway in a new time-lapse video made from aurora images taken this month. Filmmakers Claus and Anneliese Possberg used about 600 frames to create the video.', '', 'http://www.youtube.com/watch?v=izYiDDt6d8s', '', '', '89', 1, '4:26', '', '', '', '', 'http://i3.ytimg.com/vi/izYiDDt6d8s/mqdefault.jpg', 'http://i3.ytimg.com/vi/izYiDDt6d8s/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=izYiDDt6d8s', '1', 4, 0, 0, '2014-05-04 14:45:49', '0', '0', 0, 0, 1, 0, " . $member_id . ", 32),
				(55, 'Elemental Iceland', 'Photographer Stian Rekdal combined thousands of photos to create this time-lapse video showcasing Iceland''s natural beauty. He spent three weeks—and more than 3,000 miles—on the road and took more than 40,000 photos. He used about 3,500 of these to make the video.', '', 'http://www.youtube.com/watch?v=_vhf0RZg0fg', '', '', '90', 1, '2:47', '', '', '', '', 'http://i3.ytimg.com/vi/_vhf0RZg0fg/mqdefault.jpg', 'http://i3.ytimg.com/vi/_vhf0RZg0fg/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=_vhf0RZg0fg', '1', 5, 0, 0, '2014-05-04 15:40:05', '0', '0', 0, 0, 1, 0, " . $member_id . ", 33),
				(56, 'Commandments', 'Uploaded by Lokesh Bade. This channel is a Non-Profit one and all the videos are solely for ministry purposes only. The life of Moses from birth to receiving The Ten Commandments from The Lord God.\r\nAlmost close to The Holy scriptures.\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English.', '', 'https://www.youtube.com/watch?v=hPovrwqaErs', '', '', '91', 1, '1:43', '', '', '', '', 'http://i3.ytimg.com/vi/hPovrwqaErs/mqdefault.jpg', 'http://i3.ytimg.com/vi/hPovrwqaErs/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=hPovrwqaErs', '1', 142, 0, 0, '2014-05-06 12:58:20', '0', '0', 0, 0, 1, 0, " . $member_id . ", 34),
				(57, 'Rollin Wild', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', '', 'https://www.youtube.com/watch?v=yltlJEdSAHw', '', '', '92', 1, '2:02', '', '', '', '', 'http://i3.ytimg.com/vi/yltlJEdSAHw/mqdefault.jpg', 'http://i3.ytimg.com/vi/yltlJEdSAHw/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=yltlJEdSAHw', '1', 87, 0, 0, '2014-05-06 12:59:11', '0', '0', 0, 0, 1, 0, " . $member_id . ", 35),
				(75, 'Amazing nature', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.', '', 'http://www.youtube.com/watch?v=6v2L2UGZJAM', '', '', '116', 1, '13:29', '', '', '', '', 'http://i3.ytimg.com/vi/6v2L2UGZJAM/mqdefault.jpg', 'http://i3.ytimg.com/vi/6v2L2UGZJAM/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=6v2L2UGZJAM', '1', 40, 0, 0, '2014-05-06 14:24:00', '0', '0', 0, 0, 1, 0, " . $member_id . ", 46),
				(59, 'Wild Country', 'This documentary as well as all of the rest of these documentaries shown here are about important times and figures in history, historic places and people, archaeology, science, conspiracy theories, and education.\r\n\r\nThe Topics of these video documentaries cover just about everything including ancient history, Rome, Greece, Egypt, science, technology, nature, plants, animals, wildlife, environmental issues, global warming, natural disasters, planet earth, the solar system, the universe, modern physics, World wars, battles, military and combat technology, current events, education, biographies, television, archaeology, Illuminati, Area 51, crime, mafia, serial killers, paranormal, supernatural, cults, government cover-ups, the law and legal matters, news and current events, corruption, martial arts, space, aliens, ufos, conspiracy theories, Annunaki, Nibiru, Nephilim, satanic rituals, religion, strange phenomenon, origins of Mankind, monsters, mobsters, time travel', '', 'https://www.youtube.com/watch?v=EpFR_UYhOKY', '', '', '100', 1, '4:20', '', '', '', '', 'http://i3.ytimg.com/vi/EpFR_UYhOKY/mqdefault.jpg', 'http://i3.ytimg.com/vi/EpFR_UYhOKY/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=EpFR_UYhOKY', '1', 6, 0, 0, '2014-05-06 13:30:04', '0', '0', 0, 0, 1, 0, " . $member_id . ", 36),
				(74, 'Best Photos', 'Animals are eukaryotic organisms of the kingdom Animalia or Metazoa. Their body plan eventually becomes fixed as they develop, although some undergo a process of metamorphosis later on in their lives. Most animals are motile, meaning they can move spontaneously and independently. All animals must ingest other organisms or their products for sustenance (see Heterotroph).\r\n\r\nMost known animal phyla appeared in the fossil record as marine species during the Cambrian explosion, about 542 million years ago. Animals are divided into various sub-groups, including birds, mammals, amphibians, reptiles, fish and insects.', '', 'http://www.youtube.com/watch?v=uGACkEOJpR0', '', '', '115', 1, '6:41', '', '', '', '', 'http://i3.ytimg.com/vi/uGACkEOJpR0/mqdefault.jpg', 'http://i3.ytimg.com/vi/uGACkEOJpR0/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=uGACkEOJpR0', '1', 24, 0, 0, '2014-05-06 14:21:45', '0', '0', 0, 0, 1, 0, " . $member_id . ", 45),
				(61, 'Crater Lions', 'The lion (Panthera leo) is one of the four big cats in the genus Panthera and a member of the family Felidae. With some males exceeding 250 kg (550 lb) in weight, it is the second-largest living cat after the tiger. Wild lions currently exist in sub-Saharan Africa and in Asia (where an endangered remnant population resides in Gir Forest National Park in India) while other types of lions have disappeared from North Africa and Southwest Asia in historic times. Until the late Pleistocene, about 10,000 years ago, the lion was the most widespread large land mammal after humans. They were found in most of Africa, across Eurasia from western Europe to India, and in the Americas from the Yukon to Peru. The lion is a vulnerable species, having seen a major population decline in its African range of 30--50% per two decades during the second half of the 20th century. Lion populations are untenable outside designated reserves and national parks. Although the cause of the decline is not fully understood, habitat loss and conflicts with humans are currently the greatest causes of concern. Within Africa, the West African lion population is particularly endangered.', '', 'https://www.youtube.com/watch?v=PvhuzoqsshY', '', '', '102', 1, '1:08', '', '', '', '', 'http://i3.ytimg.com/vi/PvhuzoqsshY/mqdefault.jpg', 'http://i3.ytimg.com/vi/PvhuzoqsshY/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=PvhuzoqsshY', '1', 11, 0, 0, '2014-05-06 13:32:54', '0', '0', 0, 0, 1, 0, " . $member_id . ", 38),
				(62, 'The Jaguar', 'The Jaguar - year of the cat - animals wildlife nature.\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', '', 'https://www.youtube.com/watch?v=dxByg5SKxJA', '', '', '103', 1, '6:53', '', '', '', '', 'http://i3.ytimg.com/vi/dxByg5SKxJA/mqdefault.jpg', 'http://i3.ytimg.com/vi/dxByg5SKxJA/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=dxByg5SKxJA', '1', 7, 0, 0, '2014-05-06 13:33:49', '0', '0', 0, 0, 1, 0, " . $member_id . ", 39),
				(66, 'Great Photos', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', '', 'http://www.youtube.com/watch?v=r9t_sOsWfJM', '', '', '107', 1, '3:00', '', '', '', '', 'http://i3.ytimg.com/vi/r9t_sOsWfJM/mqdefault.jpg', 'http://i3.ytimg.com/vi/r9t_sOsWfJM/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=r9t_sOsWfJM', '1', 17, 0, 0, '2014-05-06 13:42:35', '0', '0', 0, 0, 1, 0, " . $member_id . ", 42),
				(76, 'Wild America', 'This film features some of the incredible wildlife seen in the American Southwest (Arizona, New Mexico, and California) and Mexico.  Rattlesnakes feature prominently, as well as other reptile species, birds, and sea mammals.  For such seemingly desolate environments (Mojave, Sonoran, and Chihuahuan Deserts), the biodiversity is astounding!', '', 'http://www.youtube.com/watch?v=e1e8rNqpMc4', '', '', '117', 1, '6:34', '', '', '', '', 'http://i3.ytimg.com/vi/e1e8rNqpMc4/mqdefault.jpg', 'http://i3.ytimg.com/vi/e1e8rNqpMc4/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=e1e8rNqpMc4', '1', 97, 0, 0, '2014-05-06 14:25:52', '0', '0', 0, 0, 1, 0, " . $member_id . ", 45),
				(65, 'African Wildlife', 'Our South African represents a sample of the hundreds of species of found in South Africa. (The term ''wildlife'' refers to mammals, birds, fish and reptiles that can be found in the wild). There are 299 mammal species in South Africa, of which 2 are critically endangered, 11 are endangered, 15 are vulnerable, and 13 are near-threatened (conservation status as assessed by the IUCN).', '', 'http://www.youtube.com/watch?v=apNSKQ5kZL0', '', '', '106', 1, '41:34', '', '', '', '', 'http://i3.ytimg.com/vi/apNSKQ5kZL0/mqdefault.jpg', 'http://i3.ytimg.com/vi/apNSKQ5kZL0/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=apNSKQ5kZL0', '1', 9, 0, 0, '2014-05-06 13:39:15', '0', '0', 0, 0, 1, 0, " . $member_id . ", 42),
				(70, 'Emperors of the Ice', 'Wildlife photographer Paul Nicklen dives beneath the Antarctic ice to capture the \"bubbly\" emperor penguins in action, and risks being mistaken for his subjects by some very hungry predators. There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration.', '', 'http://www.youtube.com/watch?v=wr4d2FfivA4', '', '', '111', 1, '20:39', '', '', '', '', 'http://i3.ytimg.com/vi/wr4d2FfivA4/mqdefault.jpg', 'http://i3.ytimg.com/vi/wr4d2FfivA4/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=wr4d2FfivA4', '1', 9, 0, 0, '2014-05-06 13:50:47', '0', '0', 0, 0, 1, 0, " . $member_id . ", 44),
				(69, 'The Call of Everest', 'Fifty years ago, Americans summited Mount Everest for the first time. To celebrate this anniversary, climbers Conrad Anker and Emily Harrington, writer Mark Jenkins, and naturalist Alton Byers meet to discuss the history and future of the world''s highest peak.', '', 'http://www.youtube.com/watch?v=megSEXmV0nQ', '', '', '110', 1, '31:39', '', '', '', '', 'http://i3.ytimg.com/vi/megSEXmV0nQ/mqdefault.jpg', 'http://i3.ytimg.com/vi/megSEXmV0nQ/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=megSEXmV0nQ', '1', 13, 0, 0, '2014-05-06 13:49:28', '0', '0', 0, 0, 1, 0, " . $member_id . ", 43),
				(77, 'Harsh Lighting', 'You''ve probably learned it''s a bad idea to shoot on the beach at noon due to harsh lighting. However, with proper technique using a reflector and the right camera settings, you can get some great shots with soft glow on your model.\r\n\r\nIn this quick tutorial, photographer Matt Hackney teaches how to get that soft look using  a 50mm lens.', '', 'http://www.youtube.com/watch?v=VS4G5SRuLSg', '', '', '118', 1, '2:45', '', '', '', '', 'http://i3.ytimg.com/vi/VS4G5SRuLSg/mqdefault.jpg', 'http://i3.ytimg.com/vi/VS4G5SRuLSg/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=VS4G5SRuLSg', '1', 161, 0, 0, '2014-05-06 14:26:20', '0', '0', 0, 0, 1, 0, " . $member_id . ", 46),
				(78, 'My best trip', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.', '', 'http://www.youtube.com/watch?v=WtQd6psqgis', '', '', '119', 1, '17:21', '', '', '', '', 'http://i3.ytimg.com/vi/WtQd6psqgis/mqdefault.jpg', 'http://i3.ytimg.com/vi/WtQd6psqgis/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=WtQd6psqgis', '1', 12, 0, 0, '2014-05-06 14:48:17', '0', '0', 0, 0, 1, 0, " . $member_id . ", 47),
				(73, 'Animals Photos', 'They have a few specialized reproductive cells, which undergo meiosis to produce smaller, motile spermatozoa or larger, non-motile ova. These fuse to form zygotes, which develop into new individuals. Many animals are also capable of asexual reproduction. This may take place through parthenogenesis, where fertile eggs are produced without mating, budding, or fragmentation. A zygote initially develops into a hollow sphere, called a blastula, which undergoes rearrangement and differentiation. In sponges, blastula larvae swim to a new location and develop into a new sponge. In most other groups, the blastula undergoes more complicated rearrangement. It first invaginates to form a gastrula with a digestive chamber, and two separate germ layers — an external ectoderm and an internal endoderm. In most cases, a mesoderm also develops between them. These germ layers then differentiate to form tissues and organs.', '', 'http://www.youtube.com/watch?v=QchpUZDDIGg', '', '', '114', 1, '6:41', '', '', '', '', 'http://i3.ytimg.com/vi/QchpUZDDIGg/mqdefault.jpg', 'http://i3.ytimg.com/vi/QchpUZDDIGg/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=QchpUZDDIGg', '1', 10, 0, 0, '2014-05-06 14:13:54', '0', '0', 0, 0, 1, 0, " . $member_id . ", 47),
				(79, 'Strobes & Daylight', 'In today''s Slanted Lens lighting tutorial we are out at the Bonneville Salt Flats. Our lesson will look at how to blend strobes lighting with daylight, so that the image does not look artificially lit. We will look at a method that I use to see what the strobes are doing in daylight and to help you better control them when the modeling lights are not bright enough to guide you. For our shots today we have an old wooden boat as a prop to work with. Out talent, Mary, will be wearing a dark vintage dress and be bare foot in the ice cold water. I wanted her to wear something dark so it will contrast against the light sky. In our last shot we will switch her to a light dress as it gets darker, so it''s easier to get her to stand out against the darker sky. Let''s take a look at shooting strobes in daylight. Keep those cameras rolling and keep on click''n.', '', 'http://www.youtube.com/watch?v=EhhZSFIzfgA', '', '', '120', 1, '6:39', '', '', '', '', 'http://i3.ytimg.com/vi/EhhZSFIzfgA/mqdefault.jpg', 'http://i3.ytimg.com/vi/EhhZSFIzfgA/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=EhhZSFIzfgA', '1', 18, 0, 0, '2014-05-06 14:50:35', '0', '0', 0, 0, 1, 0, " . $member_id . ", 48),
				(80, 'Recorded Webinar', 'On January 22, 2014, Profoto and Mark Wallace did a live webinar on the many different portrait looks you can achieve with just a single light source. Did you miss it? No worries. The webinar was recorded and is now available as a video.\r\n\r\nProfoto hosts free webinars once a month. Click the link to sign up, and we''ll send you a friendly reminder when the next one is about to start.', '', 'http://www.youtube.com/watch?v=2E3Jwgp3MDk', '', '', '121', 1, '56:37', '', '', '', '', 'http://i3.ytimg.com/vi/2E3Jwgp3MDk/mqdefault.jpg', 'http://i3.ytimg.com/vi/2E3Jwgp3MDk/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=2E3Jwgp3MDk', '1', 17, 0, 0, '2014-05-06 14:51:50', '0', '0', 0, 0, 1, 0, " . $member_id . ", 49),
				(81, 'Newfangled Pinup Girl', 'We''ve wanted to do a pinup photo shoot for a long time, but we also wanted to make it stand out from all of the other pinup photos out there by adding a concept to it.', '', 'http://www.youtube.com/watch?v=cBUsA-AoRBg', '', '', '122', 1, '7:47', '', '', '', '', 'http://i3.ytimg.com/vi/cBUsA-AoRBg/mqdefault.jpg', 'http://i3.ytimg.com/vi/cBUsA-AoRBg/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=cBUsA-AoRBg', '1', 50, 0, 0, '2014-05-06 14:55:07', '0', '0', 0, 0, 1, 0, " . $member_id . ", 50),
				(82, 'Bounce Flash', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.', '', 'http://www.youtube.com/watch?v=xTlBpNrxNLk', '', '', '123', 1, '6:11', '', '', '', '', 'http://i3.ytimg.com/vi/xTlBpNrxNLk/mqdefault.jpg', 'http://i3.ytimg.com/vi/xTlBpNrxNLk/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=xTlBpNrxNLk', '1', 42, 0, 0, '2014-05-07 06:11:47', '0', '0', 0, 0, 1, 0, " . $member_id . ", 51),
				(83, 'Take Pictures at Night', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.', '', 'http://www.youtube.com/watch?v=V6ypRbPzoPM', '', '', '124', 1, '14:02', '', '', '', '', 'http://i3.ytimg.com/vi/V6ypRbPzoPM/mqdefault.jpg', 'http://i3.ytimg.com/vi/V6ypRbPzoPM/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=V6ypRbPzoPM', '1', 4, 0, 0, '2014-05-07 06:13:49', '0', '0', 0, 0, 1, 0, " . $member_id . ", 52),
				(84, 'Digital photography', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.', '', 'http://www.youtube.com/watch?v=JwT5WzsaXv0', '', '', '125', 1, '3:31', '', '', '', '', 'http://i3.ytimg.com/vi/JwT5WzsaXv0/mqdefault.jpg', 'http://i3.ytimg.com/vi/JwT5WzsaXv0/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=JwT5WzsaXv0', '1', 12, 0, 0, '2014-05-07 06:15:03', '0', '0', 0, 0, 1, 0, " . $member_id . ", 53),
				(86, 'Love & Marriage', 'Learn how to use your chart to predict when you will fall in love or even get married in this Howcast video featuring famed astrologer Jenny Lynch.\r\n\r\nThere are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.', '', 'http://www.youtube.com/watch?v=ALB6mVmHptM', '', '', '132', 1, '1:34', '', '', '', '', 'http://i3.ytimg.com/vi/ALB6mVmHptM/mqdefault.jpg', 'http://i3.ytimg.com/vi/ALB6mVmHptM/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=ALB6mVmHptM', '1', 19, 0, 0, '2014-05-07 14:38:26', '0', '0', 0, 0, 1, 0, " . $member_id . ", 55),
				(87, 'Chart & Relationships', 'Learn what a composite chart is and what it can tell you about the true purpose of your relationships in this Howcast video featuring astrologer Jenny Lynch.\r\n\r\nThere are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.', '', 'http://www.youtube.com/watch?v=T-gOBMDAPv0', '', '', '133', 1, '1:21', '', '', '', '', 'http://i3.ytimg.com/vi/T-gOBMDAPv0/mqdefault.jpg', 'http://i3.ytimg.com/vi/T-gOBMDAPv0/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=T-gOBMDAPv0', '1', 9, 0, 0, '2014-05-07 14:39:38', '0', '0', 0, 0, 1, 0, " . $member_id . ", 56),
				(88, 'Predicting Compatibility', 'Learn how to predict compatibility with your astrological chart from famed astrologer Jenny Lynch in this Howcast video.\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English.', '', 'http://www.youtube.com/watch?v=ke2y1HfY3T8', '', '', '134', 1, '1:15', '', '', '', '', 'http://i3.ytimg.com/vi/ke2y1HfY3T8/mqdefault.jpg', 'http://i3.ytimg.com/vi/ke2y1HfY3T8/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=ke2y1HfY3T8', '1', 254, 0, 0, '2014-05-07 14:41:00', '0', '0', 0, 0, 1, 0, " . $member_id . ", 57),
				(89, 'First Date Tips', 'Pick up some first date tips for every zodiac sign from famed astrologer Jenny Lynch in this Howcast video.\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters.', '', 'http://www.youtube.com/watch?v=yicj8wQ2YIo', '', '', '135', 1, '2:01', '', '', '', '', 'http://i3.ytimg.com/vi/yicj8wQ2YIo/mqdefault.jpg', 'http://i3.ytimg.com/vi/yicj8wQ2YIo/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=yicj8wQ2YIo', '1', 587, 0, 0, '2014-05-07 14:41:48', '0', '0', 0, 0, 1, 0, " . $member_id . ", 58),
				(91, 'Christmas in Yellowstone', 'Stretching across more than 2.2 million acres of Wyoming, Montana, and Idaho is one of the greatest expanses of unspoiled nature and wildlife anywhere on Earth — Yellowstone National Park. Designated America''s first national park in 1872, Yellowstone now receives almost three million visitors each year. Yet only a small fraction of those who glimpse the park''s stunning vistas, geological wonders, and animal residents do so during the winter months, a time when nature''s inhospitality is matched only by its serenity.', '', 'https://www.youtube.com/watch?v=xYFZehtdWRs', '', '', '137', 1, '7:43', '', '', '', '', 'http://i3.ytimg.com/vi/xYFZehtdWRs/mqdefault.jpg', 'http://i3.ytimg.com/vi/xYFZehtdWRs/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=xYFZehtdWRs', '1', 9, 0, 0, '2014-05-07 15:13:51', '0', '0', 0, 0, 1, 0, " . $member_id . ", 59),
				(92, 'Adventure', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English.', '', 'http://www.youtube.com/watch?v=2E9ElqZOlaw', '', '', '138', 1, '3:06', '', '', '', '', 'http://i3.ytimg.com/vi/2E9ElqZOlaw/mqdefault.jpg', 'http://i3.ytimg.com/vi/2E9ElqZOlaw/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=2E9ElqZOlaw', '1', 109, 0, 0, '2014-05-07 15:16:38', '0', '0', 0, 0, 1, 0, " . $member_id . ", 60),
				(93, 'Alaska adventure', 'In 2012 I drove to Alaska to gather animal photos for magazines. \r\nThe adventure was amazing.\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English.', '', 'http://www.youtube.com/watch?v=u93vk9U8XUc', '', '', '139', 1, '14:48', '', '', '', '', 'http://i3.ytimg.com/vi/u93vk9U8XUc/mqdefault.jpg', 'http://i3.ytimg.com/vi/u93vk9U8XUc/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=u93vk9U8XUc', '1', 18, 0, 0, '2014-05-07 15:19:07', '0', '0', 0, 0, 1, 0, " . $member_id . ", 61),
				(94, 'Grizzly Eats My GoPro', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English.', '', 'http://www.youtube.com/watch?v=DWbXfkNuupM', '', '', '140', 1, '1:20', '', '', '', '', 'http://i3.ytimg.com/vi/DWbXfkNuupM/mqdefault.jpg', 'http://i3.ytimg.com/vi/DWbXfkNuupM/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=DWbXfkNuupM', '1', 20, 0, 0, '2014-05-07 15:20:42', '0', '0', 0, 0, 1, 0, " . $member_id . ", 62),
				(95, 'The Wild Photoshop', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English.', '', 'http://www.youtube.com/watch?v=L_jcOJb-N9s', '', '', '141', 1, '3:09', '', '', '', '', 'http://i3.ytimg.com/vi/L_jcOJb-N9s/mqdefault.jpg', 'http://i3.ytimg.com/vi/L_jcOJb-N9s/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=L_jcOJb-N9s', '1', 48, 0, 0, '2014-05-07 15:30:05', '0', '1', 1, 0, 1, 0, " . $member_id . ", 63),
				(96, 'Perfect Exposure', 'Join host Joe Brady as he puts the Sekonic meter and the ColorChecker Passport with Sekonic Grey Balance Card to work out in the field to get perfect color and perfect exposure when shooting landscape photography.\r\n\r\nYou will learn the benefits a handheld meter has over your camera''s metering and how creating a custom exposure profile can insure perfect exposures every time. Add to this the ColorChecker Passport''s ability to create a custom color profile for your Raw files and you will have the best starting point possible for your image edits.\r\n\r\nJoe will also explain how to factor in filter compensations and some tips and techniques to get the most tonal range out of your camera''s sensor.', '', 'http://www.youtube.com/watch?v=UUrNGhKS1h8', '', '', '144', 1, '1:11:47', '', '', '', '', 'http://i3.ytimg.com/vi/UUrNGhKS1h8/mqdefault.jpg', 'http://i3.ytimg.com/vi/UUrNGhKS1h8/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=UUrNGhKS1h8', '1', 55, 0, 0, '2014-05-08 12:01:27', '1', '1', 0, 0, 1, 0, " . $member_id . ", 64),
				(97, 'Create Color Depth', 'Animals are eukaryotic organisms of the kingdom Animalia or Metazoa. Their body many desktop packages and web page now use Lorem as model text and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', '', 'http://www.youtube.com/watch?v=MaLELeXtI24', '', '', '145', 1, '17:55', '', '', '', '', 'http://i3.ytimg.com/vi/MaLELeXtI24/mqdefault.jpg', 'http://i3.ytimg.com/vi/MaLELeXtI24/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=MaLELeXtI24', '1', 188, 0, 0, '2014-05-08 12:02:31', '0', '1', 1, 0, 1, 0, " . $member_id . ", 65),
				(98, 'Winter Landscapes', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sit amet fringilla orci. Aenean molestie dolor nibh, non accumsan orci fermentum vitae. Proin bibendum at diam tincidunt fermentum. Nunc venenatis volutpat erat, sit amet scelerisque libero vestibulum id. Maecenas vitae porttitor dolor. Mauris gravida lacus quis elit eleifend auctor sit amet sed orci. Ut nulla risus, luctus sit amet commodo eget, sollicitudin in magna. Etiam placerat eros ante, vitae interdum mi semper non. Vestibulum id fermentum odio.', '', 'http://www.youtube.com/watch?v=Q8Lg8e9hML4', '', '', '146', 1, '7:13', '', '', '', '', 'http://i3.ytimg.com/vi/Q8Lg8e9hML4/mqdefault.jpg', 'http://i3.ytimg.com/vi/Q8Lg8e9hML4/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=Q8Lg8e9hML4', '1', 118, 0, 0, '2014-05-08 12:06:09', '0', '0', 0, 0, 1, 0, " . $member_id . ", 66),
				(99, 'Winter in Bandon', 'Bandon is a delightfully charming Southern Oregon coastal town located 90 miles north of the California border. Its allure of nature''s beauty is undeniable even during the winter months.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sit amet fringilla orci. Aenean molestie dolor nibh, non accumsan orci fermentum vitae. Proin bibendum at diam tincidunt fermentum. Nunc venenatis volutpat erat, sit amet scelerisque libero vestibulum id. Maecenas vitae porttitor dolor. Mauris gravida lacus quis elit eleifend auctor sit amet sed orci. Ut nulla risus, luctus sit amet commodo eget, sollicitudin in magna. Etiam placerat eros ante, vitae interdum mi semper non. Vestibulum id fermentum odio.', '', 'http://www.youtube.com/watch?v=NG34swnz7xA', '', '', '147', 1, '4:31', '', '', '', '', 'http://i3.ytimg.com/vi/NG34swnz7xA/mqdefault.jpg', 'http://i3.ytimg.com/vi/NG34swnz7xA/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=NG34swnz7xA', '1', 6, 0, 0, '2014-05-08 12:06:58', '0', '0', 0, 0, 1, 0, " . $member_id . ", 67),
				(100, 'Earth of the year 2013', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sit amet fringilla orci. Aenean molestie dolor nibh, non accumsan orci fermentum vitae. Proin bibendum at diam tincidunt fermentum. Nunc venenatis volutpat erat, sit amet scelerisque libero vestibulum id. Maecenas vitae porttitor dolor. Mauris gravida lacus quis elit eleifend auctor sit amet sed orci. Ut nulla risus, luctus sit amet commodo eget, sollicitudin in magna. Etiam placerat eros ante, vitae interdum mi semper non. Vestibulum id fermentum odio.', '', 'https://www.youtube.com/watch?v=xno0yCTURmY', '', '', '148', 1, '10:01', '', '', '', '', 'http://i3.ytimg.com/vi/xno0yCTURmY/mqdefault.jpg', 'http://i3.ytimg.com/vi/xno0yCTURmY/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=xno0yCTURmY', '1', 8, 0, 0, '2014-05-08 12:18:25', '0', '0', 0, 0, 1, 0, " . $member_id . ", 68),
				(101, 'Amazing Sunset Photos', 'For much more than this, you can visit our global channel\r\nDont forget to subscribe our daily Channel.. Nothing says ''paradise'' quite like a great sunset photo. Capturing the mix of reds, yellows, oranges, and magentas in the evening sky is still one of my favo', '', 'https://www.youtube.com/watch?v=cFV-HcMEwXQ', '', '', '149', 1, '11:13', '', '', '', '', 'http://i3.ytimg.com/vi/cFV-HcMEwXQ/mqdefault.jpg', 'http://i3.ytimg.com/vi/cFV-HcMEwXQ/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=cFV-HcMEwXQ', '1', 377, 0, 0, '2014-05-08 12:19:37', '0', '1', 1, 0, 1, 0, " . $member_id . ", 69),
				(102, 'Unbelievable ', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sit amet fringilla orci. Aenean molestie dolor nibh, non accumsan orci fermentum vitae. Proin bibendum at diam tincidunt fermentum. Nunc venenatis volutpat erat, sit amet scelerisque libero vestibulum id. Maecenas vitae porttitor dolor. Mauris gravida lacus quis elit eleifend auctor sit amet sed orci. Ut nulla risus, luctus sit amet commodo eget, sollicitudin in magna. Etiam placerat eros ante, vitae interdum mi semper non. Vestibulum id fermentum odio.', '', 'https://www.youtube.com/watch?v=VlhJm_KkKEg', '', '', '150', 1, '1:32', '', '', '', '', 'http://i3.ytimg.com/vi/VlhJm_KkKEg/mqdefault.jpg', 'http://i3.ytimg.com/vi/VlhJm_KkKEg/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=VlhJm_KkKEg', '1', 20, 0, 0, '2014-05-08 12:21:11', '0', '0', 0, 0, 1, 0, " . $member_id . ", 70),
				(103, 'The Moon', 'The Moon is the only natural satellite of the Earth and the fifth largest moon in the Solar System. The Moon is in synchronous rotation with Earth, always showing the same face with its near side marked by dark volcanic maria that fill between the bright ancient crustal highlands and the prominent impact craters. It is the brightest object in the sky after the Sun, although its surface is actually dark, with a reflectance just slightly higher than that of worn asphalt. Its prominence in the sky and its regular cycle of phases have, since ancient times, made the Moon an important cultural influence on language, calendars, art and mythology. The Moon''s gravitational influence produces the ocean tides and the minute lengthening of the day. Dr. Eugene Shoemaker, Full moon, magnetic field, Neil Armstrong, Apollo 8, Apollo 11, luna, NASA, craters, astronomy, astrology, quantum, space', '', 'https://www.youtube.com/watch?v=H5yrwuDDgYE', '', '', '151', 1, '2:56', '', '', '', '', 'http://i3.ytimg.com/vi/H5yrwuDDgYE/mqdefault.jpg', 'http://i3.ytimg.com/vi/H5yrwuDDgYE/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=H5yrwuDDgYE', '1', 19, 0, 0, '2014-05-08 12:22:17', '0', '0', 0, 0, 1, 0, " . $member_id . ", 71),
				(104, 'People will live on Mars', 'Animals are eukaryotic organisms of the kingdom Animalia or Metazoa. Their body more than 100,000 people are eager to make themselves at home on another planet. They''ve applied for a one-way trip to Mars, hoping to be chosen to spend the rest of their lives on uncharted territory, according to an organization planning the manned missions.', '', 'http://www.youtube.com/watch?v=icN29cdmw_s', '', '', '152', 1, '4:07', '', '', '', '', 'http://i3.ytimg.com/vi/icN29cdmw_s/mqdefault.jpg', 'http://i3.ytimg.com/vi/icN29cdmw_s/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=icN29cdmw_s', '1', 15, 0, 0, '2014-05-08 12:23:06', '0', '0', 0, 0, 1, 0, " . $member_id . ", 72),
				(106, 'Colonize Mars', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sit amet fringilla orci. Aenean molestie dolor nibh, non accumsan orci fermentum vitae. Proin bibendum at diam tincidunt fermentum. Nunc venenatis volutpat erat, sit amet scelerisque libero vestibulum id. Maecenas vitae porttitor dolor. Mauris gravida lacus quis elit eleifend auctor sit amet sed orci. Ut nulla risus, luctus sit amet commodo eget, sollicitudin in magna. Etiam placerat eros ante, vitae interdum mi semper non. Vestibulum id fermentum odio.', '', 'http://www.youtube.com/watch?v=B-rEDyCo0ao', '', '', '154', 1, '44:26', '', '', '', '', 'http://i3.ytimg.com/vi/B-rEDyCo0ao/mqdefault.jpg', 'http://i3.ytimg.com/vi/B-rEDyCo0ao/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=B-rEDyCo0ao', '1', 8, 0, 0, '2014-05-08 13:28:08', '0', '0', 0, 0, 1, 0, " . $member_id . ", 73),
				(107, 'The Mars Underground', 'This film captures the spirit of Mars pioneers who refuse to let their dreams be put on hold by a slumbering space program. Their passionate urge to walk the soil of an alien world is infectious and inspirational. This film is the manifesto of the new space revolution.', '', 'http://www.youtube.com/watch?v=dRxskmMMnfc', '', '', '155', 1, '1:13:53', '', '', '', '', 'http://i3.ytimg.com/vi/dRxskmMMnfc/mqdefault.jpg', 'http://i3.ytimg.com/vi/dRxskmMMnfc/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=dRxskmMMnfc', '1', 7, 0, 0, '2014-05-08 13:28:32', '0', '0', 0, 0, 1, 0, " . $member_id . ", 74),
				(108, 'Mars Exploration Rover', 'NASA''s Mars Exploration Rover Mission (MER) is an ongoing robotic space mission involving two rovers, Spirit and Opportunity, exploring the planet Mars. It began in 2003 with the sending of the two rovers—MER-A Spirit and MER-B Opportunity—to explore the Martian surface and geology.\r\n\r\nThe mission''s scientific objective was to search for and characterize a wide range of rocks and soils that hold clues to past water activity on Mars. The mission is part of NASA''s Mars Exploration Program, which includes three previous successful landers: the two Viking program landers in 1976 and Mars Pathfinder probe in 1997.ence platform.', '', 'http://www.youtube.com/watch?v=7UUPiCVbZhE', '', '', '156', 1, '0:56', '', '', '', '', 'http://i3.ytimg.com/vi/7UUPiCVbZhE/mqdefault.jpg', 'http://i3.ytimg.com/vi/7UUPiCVbZhE/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=7UUPiCVbZhE', '1', 7, 0, 0, '2014-05-08 13:29:09', '0', '0', 0, 0, 1, 0, " . $member_id . ", 75),
				(109, '7 Minutes of Terror', 'NASA''s Curiosity rover is a 1-ton robot that will make an unprecedented Mars landing on Aug. 5, 2012. See how the risky maneuver will keep rover team members in suspense for 7 fateful minutes.', '', 'https://www.youtube.com/watch?v=VlhJm_KkKEg', '', '', '157', 1, '1:32', '', '', '', '', 'http://i3.ytimg.com/vi/VlhJm_KkKEg/mqdefault.jpg', 'http://i3.ytimg.com/vi/VlhJm_KkKEg/maxresdefault.jpg', '0', 'https://www.youtube.com/watch?v=VlhJm_KkKEg', '1', 78, 0, 0, '2014-05-08 13:30:08', '0', '0', 0, 0, 1, 0, " . $member_id . ", 76),
				(110, 'Discovery Space', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sit amet fringilla orci. Aenean molestie dolor nibh, non accumsan orci fermentum vitae. Proin bibendum at diam tincidunt fermentum. Nunc venenatis volutpat erat, sit amet scelerisque libero vestibulum id. Maecenas vitae porttitor dolor. Mauris gravida lacus quis elit eleifend auctor sit amet sed orci. Ut nulla risus, luctus sit amet commodo eget, sollicitudin in magna. Etiam placerat eros ante, vitae interdum mi semper non. Vestibulum id fermentum odio.', '', 'http://www.youtube.com/watch?v=tMVDWvHlrRc', '', '', '158', 1, '1:4:31', '', '', '', '', 'http://i3.ytimg.com/vi/tMVDWvHlrRc/mqdefault.jpg', 'http://i3.ytimg.com/vi/tMVDWvHlrRc/maxresdefault.jpg', '0', 'http://www.youtube.com/watch?v=tMVDWvHlrRc', '1', 113, 0, 0, '2014-05-08 13:31:49', '0', '0', 0, 0, 1, 0, " . $member_id . ", 77),
				(120, 'Holy Monks ', 'A shaolin master has the task of preparing two antagonic disciples with the same goal: turn them into good warriors who are always willing to help the weakest.\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English.', '', 'https://www.youtube.com/watch?v=G2hH1mwCYzE', '', '', '544', 1, '3:30', '', '', '', '', 'http://i3.ytimg.com/vi/G2hH1mwCYzE/mqdefault.jpg', 'http://i3.ytimg.com/vi/G2hH1mwCYzE/maxresdefault.jpg', '', 'https://www.youtube.com/watch?v=G2hH1mwCYzE', '0', 128, 0, 0, '2014-07-14 14:25:36', '', '', 0, 0, 1, 0, " . $member_id . ", 78);");


		// Video title array
	// 	$videoName = array(
	// 		0 => 'Happy people in Tatras',
	// 		1 => '3D Animated Short Film',
	// 		2 => 'Antarctic Mountain Climbing',
	// 		3 => 'Go Where the Locals Go',
	// 		4 => 'Telling the Story',
	// 		5 => 'The Aboriginal Homeland',
	// 		6 => 'Read the Heart Line',
	// 		7 => 'The Reindeer People',
	// 		8 => 'Running Up Hill',
	// 		9 => 'Lightweight',
	// 		10 => 'Replacing Florida\'s Stolen Orchids',
	// 		11 => 'Foiled by a Balloon',
	// 		12 => 'Big Buck Bunny',
	// 		13 => 'Kayapo Warrior Tribe',
	// 		14 => 'Exploring the Cosmic Dawn',
	// 		15 => 'Window to Your Health',
	// 		16 => 'Robinson Crusoe',
	// 		17 => 'Ratatouille',
	// 		18 => 'Caminandes',
	// 		19 => 'Dum Spiro - 3D Animation',
	// 		20 => 'Animation Movie - Sintel',
	// 		21 => 'Animation Movie - FAT',
	// 		22 => 'The Smurfs 2',
	// 		23 => 'Baby Hazel Games',
	// 		24 => 'Alice Amv',
	// 		25 => 'Cupidon',
	// 		26 => 'Visit to the Leather Maker',
	// 		27 => '3D Short Film',
	// 		28 => 'Ice Age 4',
	// 		29 => 'Electroshock',
	// 		30 => 'Tolerantia',
	// 		31 => 'Extraordinary Skies',
	// 		32 => 'Norway Northern Lights',
	// 		33 => 'Elemental Iceland',
	// 		34 => 'Commandments',
	// 		35 => 'Rollin Wild',
	// 		36 => 'Amazing nature',
	// 		37 => 'Wild Country',
	// 		38 => 'Best Photos',
	// 		39 => 'Crater Lions',
	// 		40 => 'The Jaguar',
	// 		41 => 'Great Photos',
	// 		42 => 'Wild America',
	// 		43 => 'African Wildlife',
	// 		44 => 'Emperors of the Ice',
	// 		45 => 'The Call of Everest',
	// 		46 => 'Harsh Lighting',
	// 		47 => 'My best trip',
	// 		48 => 'Animals Photos',
	// 		49 => 'Strobes & Daylight',
	// 		50 => 'Recorded Webinar',
	// 		51 => 'Newfangled Pinup Girl',
	// 		52 => 'Bounce Flash',
	// 		53 => 'Take Pictures at Night',
	// 		54 => 'Digital photography',
	// 		55 => 'Love & Marriage',
	// 		56 => 'Chart & Relationships',
	// 		57 => 'Predicting Compatibility',
	// 		58 => 'First Date Tips',
	// 		59 => 'Christmas in Yellowstone',
	// 		60 => 'Adventure',
	// 		61 => 'Alaska adventure',
	// 		62 => 'Grizzly Eats My GoPro',
	// 		63 => 'The Wild Photoshop',
	// 		64 => 'Perfect Exposure',
	// 		65 => 'Create Color Depth',
	// 		66 => 'Winter Landscapes',
	// 		67 => 'Winter in Bandon',
	// 		68 => 'Earth of the year 2013',
	// 		69 => 'Amazing Sunset Photos',
	// 		70 => 'Unbelievable',
	// 		71 => 'The Moon',
	// 		72 => 'People will live on Mars',
	// 		73 => 'Colonize Mars',
	// 		74 => 'The Mars Underground',
	// 		75 => 'Mars Exploration Rover',
	// 		76 => '7 Minutes of Terror',
	// 		77 => 'Discovery Space',
	// 		78 => 'Holy Monks'
	// 	);

	// 	// Insert all sample video in to post table
	// 	for ( $i = 1; $i <= 79; $i++ ) {
	// 		$j            = $i - 1;
	// 		$slug         = sanitize_title( $videoName[$j] );
	// 		$post_content = '[hdvideo id=' . $i . ']';
	// 		$postID       = $postid[$j];
	// 		$guid         = get_site_url() . '/?post_type=videogallery&#038;p=' . $postID;
			
	// 		$wpdb->query('INSERT INTO ' . $posttable . ' ( `post_author`,`post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count` )
	// 				VALUES
	// 				( "1","2011-11-15 07:22:39", "2011-11-15 07:22:39", "'.$post_content.'", "'.$videoName[$j].'", "", "publish", "open", "closed", "", "'.$slug.'", "", "", "2013-11-15 07:22:39", "2013-11-15 07:22:39", "", 0, "'.$guid.'", "0","videogallery", "", "0" )');
	// 	}
	// }

	$wpdb->query("TRUNCATE TABLE ".$table_playlist);
	// Insert sample categories
	$movieTrailer = $wpdb->get_results( 'SELECT * FROM ' . $table_playlist );
	if ( empty( $movieTrailer ) ) {

		$wpdb->query("INSERT INTO " . $table_playlist . " (`pid`, `playlist_name`, `playlist_slugname`, `playlist_desc`, `is_publish`, `playlist_order`) VALUES
						(2, 'Animation', 'animation', '', 1, 2),
						(3, 'Animals', 'animals', '', 1, 3),
						(10, 'Advertisement', 'advertisement', NULL, 1, 6),
						(7, 'Nature Landscapes', 'nature-landscapes', '', 1, 6),
						(8, 'Cosmos', '', '', 1, 7),
						(9, 'Travel and Living', 'travel-and-living', '', 1, 8);");
	}

	$wpdb->query("TRUNCATE TABLE ".$table_settings);
	// Update settings
	$videoSettings = $wpdb->get_results( 'SELECT * FROM ' . $table_settings );
	if ( empty( $videoSettings ) ) {
		$wpdb->query(
				'INSERT INTO ' . $table_settings . ' (`settings_id`, `autoplay`, `playlist`, `playlistauto`, `buffer`, `normalscale`, `fullscreenscale`, `logopath`, `logo_target`, `volume`, `logoalign`, `hdflvplayer_ads`, `HD_default`, `download`, `logoalpha`, `skin_autohide`, `stagecolor`, `embed_visible`, `view_visible`, `ratingscontrol`, `tagdisplay`, `categorydisplay`, `shareURL`, `playlistXML`, `debug`, `timer`, `zoom`, `email`, `fullscreen`, `width`, `height`, `display_logo`, `configXML`, `uploads`, `license`, `keyApps`, `keydisqusApps`, `preroll`, `postroll`, `feature`, `rowsFea`, `colFea`, `recent`, `rowsRec`, `colRec`, `popular`, `rowsPop`, `colPop`, `page`, `category_page`, `ffmpeg_path`, `stylesheet`, `comment_option`, `rowCat`, `colCat`, `rowMore`, `colMore`, `homecategory`, `bannercategory`, `banner_categorylist`, `hbannercategory`, `hbanner_categorylist`, `vbannercategory`, `vbanner_categorylist`, `bannerw`, `playerw`, `numvideos`, `gutterspace`, `default_player`, `player_colors`, `playlist_open`, `showPlaylist`, `midroll_ads`, `adsSkip`, `adsSkipDuration`, `relatedVideoView`, `imaAds`, `trackCode`, `showTag`, `shareIcon`, `volumecontrol`, `playlist_auto`, `progressControl`, `imageDefault`) VALUES
					(1, 1, 0, 0, 3, 2, 1, \'platoon.jpg\', \'\', 50, \'BL\', 0, 1, 1, 100, 1, \'\', 1, 1, 0, 0, 1, \'\', \'\', 0, 0, 0, 0, 0, 620, 350, 0, \'0\', \'wp-content/uploads/videogallery\', \'\', \'\', \'\', \'0\', \'0\', \'1\', \'3\', \'3\', \'1\', \'3\', \'3\', \'1\', \'3\', \'3\', \'20\', \'4\', \'\', \'\', 1, \'3\', \'3\', \'3\', \'3\', \'1\', \'popular\', 1, \'hpopular\', 1, \'vpopular\', 1, \'650\', \'450\', \'5\', 20, 0, \'a:42:{s:21:"sharepanel_up_BgColor";s:0:"";s:23:"sharepanel_down_BgColor";s:0:"";s:19:"sharepaneltextColor";s:0:"";s:15:"sendButtonColor";s:0:"";s:19:"sendButtonTextColor";s:0:"";s:9:"textColor";s:0:"";s:11:"skinBgColor";s:8:"0xdb4a3f";s:13:"seek_barColor";s:8:"0xdb4a3f";s:15:"buffer_barColor";s:8:"0x000000";s:13:"skinIconColor";s:8:"0x000000";s:11:"pro_BgColor";s:8:"0x000000";s:15:"playButtonColor";s:8:"0xdb4a3f";s:17:"playButtonBgColor";s:8:"0x000000";s:17:"playerButtonColor";s:8:"0xdb4a3f";s:19:"playerButtonBgColor";s:8:"0xdb4a3f";s:19:"relatedVideoBgColor";s:0:"";s:15:"scroll_barColor";s:0:"";s:14:"scroll_BgColor";s:0:"";s:11:"skinVisible";s:1:"1";s:12:"skin_opacity";s:3:"0.5";s:13:"subTitleColor";s:0:"";s:15:"subTitleBgColor";s:0:"";s:18:"subTitleFontFamily";s:0:"";s:16:"subTitleFontSize";s:0:"";s:16:"show_social_icon";s:1:"1";s:14:"show_posted_by";s:1:"1";s:18:"show_related_video";N;s:17:"recentvideo_order";s:2:"id";s:19:"related_video_count";s:3:"100";s:14:"report_visible";N;s:30:"amazon_bucket_access_secretkey";s:0:"";s:24:"amazon_bucket_access_key";s:0:"";s:18:"amazonbuckets_link";s:0:"";s:18:"amazonbuckets_name";s:0:"";s:20:"amazonbuckets_enable";s:1:"0";s:19:"user_allowed_method";s:3:"c,y";s:14:"iframe_visible";N;s:21:"googleadsense_visible";s:1:"0";s:13:"show_added_on";s:1:"1";s:20:"member_upload_enable";s:1:"1";s:9:"showTitle";N;s:13:"show_rss_icon";s:1:"1";}\', 1, 0, 0, 1, \'8\', \'center\', 0, \'\', 1, 0, 1, 0, 1, 1);');
	}

	$wpdb->query("TRUNCATE TABLE " . $wpdb->prefix . "hdflvvideoshare_med2play");
	## Update video and category details in med2play table
	$wpdb->query("INSERT INTO " . $wpdb->prefix . "hdflvvideoshare_med2play (`rel_id`, `media_id`, `playlist_id`, `porder`, `sorder`) VALUES
				(6, 27, 3, 0, 0),
				(7, 1, 2, 0, 0),
				(8, 2, 2, 0, 0),
				(9, 3, 2, 0, 0),
				(10, 4, 2, 0, 0),
				(11, 5, 2, 0, 0),
				(12, 6, 2, 0, 0),
				(13, 7, 3, 0, 0),
				(14, 8, 3, 0, 0),
				(15, 9, 3, 0, 0),
				(16, 10, 3, 0, 0),
				(17, 12, 4, 0, 0),
				(18, 13, 4, 0, 0),
				(19, 14, 5, 0, 0),
				(20, 15, 5, 0, 0),
				(21, 16, 2, 0, 0),
				(22, 17, 2, 0, 0),
				(23, 11, 4, 0, 0),
				(24, 7, 2, 0, 0),
				(25, 8, 2, 0, 0),
				(26, 9, 2, 0, 0),
				(27, 10, 2, 0, 0),
				(28, 11, 2, 0, 0),
				(29, 12, 2, 0, 0),
				(30, 13, 2, 0, 0),
				(31, 14, 2, 0, 0),
				(32, 15, 2, 0, 0),
				(33, 18, 1, 0, 0),
				(34, 18, 2, 0, 0),
				(35, 18, 3, 0, 0),
				(36, 19, 6, 0, 0),
				(37, 20, 7, 0, 0),
				(41, 21, 8, 0, 0),
				(40, 22, 9, 0, 0),
				(42, 23, 9, 0, 0),
				(43, 24, 9, 0, 0),
				(44, 25, 7, 0, 0),
				(45, 26, 9, 0, 0),
				(46, 27, 7, 0, 0),
				(47, 28, 9, 0, 0),
				(48, 29, 9, 0, 0),
				(49, 30, 9, 0, 0),
				(50, 31, 7, 0, 0),
				(51, 32, 9, 0, 0),
				(52, 33, 2, 0, 0),
				(53, 34, 9, 0, 0),
				(54, 35, 2, 0, 0),
				(55, 36, 2, 0, 0),
				(56, 37, 2, 0, 0),
				(57, 38, 2, 0, 0),
				(58, 39, 2, 0, 0),
				(59, 40, 2, 0, 0),
				(60, 41, 2, 0, 0),
				(61, 42, 2, 0, 0),
				(62, 43, 2, 0, 0),
				(63, 44, 2, 0, 0),
				(64, 45, 2, 0, 0),
				(65, 46, 9, 0, 0),
				(66, 47, 2, 0, 0),
				(67, 48, 2, 0, 0),
				(68, 49, 2, 0, 0),
				(69, 50, 2, 0, 0),
				(70, 51, 2, 0, 0),
				(71, 52, 2, 0, 0),
				(72, 53, 8, 0, 0),
				(73, 54, 8, 0, 0),
				(74, 55, 8, 0, 0),
				(75, 56, 2, 0, 0),
				(76, 57, 2, 0, 0),
				(77, 58, 3, 0, 0),
				(78, 59, 3, 0, 0),
				(79, 60, 3, 0, 0),
				(80, 61, 3, 0, 0),
				(81, 62, 3, 0, 0),
				(82, 63, 3, 0, 0),
				(83, 64, 3, 0, 0),
				(84, 65, 3, 0, 0),
				(85, 66, 3, 0, 0),
				(86, 67, 3, 0, 0),
				(87, 68, 3, 0, 0),
				(88, 69, 3, 0, 0),
				(89, 70, 3, 0, 0),
				(90, 71, 3, 0, 0),
				(91, 72, 3, 0, 0),
				(92, 73, 3, 0, 0),
				(93, 74, 3, 0, 0),
				(94, 75, 3, 0, 0),
				(95, 76, 3, 0, 0),
				(96, 77, 3, 0, 0),
				(97, 78, 7, 0, 0),
				(98, 79, 9, 0, 0),
				(99, 80, 9, 0, 0),
				(100, 81, 9, 0, 0),
				(101, 82, 9, 0, 0),
				(102, 83, 8, 0, 0),
				(103, 84, 7, 0, 0),
				(104, 85, 9, 0, 0),
				(105, 86, 9, 0, 0),
				(106, 87, 9, 0, 0),
				(107, 88, 9, 0, 0),
				(108, 89, 9, 0, 0),
				(109, 90, 3, 0, 0),
				(110, 90, 7, 0, 0),
				(111, 91, 7, 0, 0),
				(112, 92, 7, 0, 0),
				(113, 93, 7, 0, 0),
				(114, 94, 7, 0, 0),
				(115, 95, 7, 0, 0),
				(116, 96, 7, 0, 0),
				(117, 97, 7, 0, 0),
				(118, 98, 7, 0, 0),
				(119, 99, 7, 0, 0),
				(120, 100, 8, 0, 0);");
	flush_rewrite_rules();
}


// Get video permalink
function vh_get_video_permalink( $postid ) {
	global $wp_rewrite;

	$link = $wp_rewrite->get_page_permastruct(); ## check whether permalink enabled or not
	$video_details = get_post( $postid ); ## Get post detail from post id

	if ( ! empty( $link ) ) { ## Return SEO video URL if permalink enabled
		return get_site_url() . '/' . $video_details->post_type . '/' . $video_details->post_name . '/';
	} else { ## Return Non SEO video URL if permalink disabled
		return get_permalink($postid);
	}
}

function vh_vcSetAsTheme() {
	vc_set_as_theme( true );
}
add_action( 'vc_before_init', 'vh_vcSetAsTheme' );


if ( defined('snaptube_videogallery') ) {
	$api_status = get_option('videogallery_youtube_api', false);

	if ( !$api_status ) {
		global $wpdb;
		$videogallery_settings = $wpdb->prefix.'hdflvvideoshare_settings';
		$result = $wpdb->get_results("SELECT player_colors FROM $videogallery_settings");
		if ( !empty($result) ) {
			$results = unserialize($result['0']->player_colors);

			if ( !isset($results['youtube_key']) || ( isset($results['youtube_key']) && $results['youtube_key'] == '' ) ) {
				function vh_contus_plugin_notice() { ?>
					<div class="error">
						<p><?php _e( 'Wordpress Video Gallery plugin now requires YouTube API and it seems that you haven\'t set it up, head to our <a href="http://documentation.cohhe.com/snaptube/knowledgebase/youtube-api/">documentation</a>.', 'vh' ); ?></p>
					</div>
					<?php
				}
				add_action( 'admin_notices', 'vh_contus_plugin_notice' );
			} else {
				update_option('videogallery_youtube_api', true);
			}
		}
	}
}

add_action('after_switch_theme', 'vh_video_licence');
function vh_video_licence() {
	global $wpdb;
	$videogallery_settings = $wpdb->prefix.'hdflvvideoshare_settings';
	$result = $wpdb->get_results("SELECT license FROM $videogallery_settings");
	if ( !empty( $result) ) {
		$results = $result['0']->license;

		if ( $results == '' ) {
			$result = $wpdb->query("UPDATE $videogallery_settings SET license='snaptubeCONTUS'");
		}
	}
}