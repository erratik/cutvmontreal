<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
		<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		<?php
			global $vh_class;
			$logo_size_html = '';

			// Get theme logo
			$logo = get_option('vh_sitelogo');
			if($logo == false || $logo == '' ) {
				$logo = get_template_directory_uri() . '/images/logo.png';
			}

			// Get header bg image
			$favicon = get_option('vh_favicon');
			if ($favicon == false) {
				$favicon = get_template_directory_uri() . '/images/favicon.ico';
			}

			$website_logo_retina_ready = filter_var(get_option('vh_website_logo_retina'), FILTER_VALIDATE_BOOLEAN);
			if ((bool)$website_logo_retina_ready != false) {
				$logo_size = getimagesize($logo);
				$logo_size_html = ' style="height: ' . ($logo_size[1] / 2) . 'px;" height="' . ($logo_size[1] / 2) . '"';
			}

			// Social icons
			$menu_header_twitter_url   = get_option( 'vh_header_twitter_url' );
			$menu_header_flickr_url    = get_option( 'vh_header_flickr_url' );
			$menu_header_facebook_url  = get_option( 'vh_header_facebook_url' );
			$menu_header_google_url    = get_option( 'vh_header_google_url' );
			$menu_header_pinterest_url = get_option( 'vh_header_pinterest_url' );
			$menu_header_vkontakte_url = get_option( 'vh_header_vkontakte_url' );
			$menu_header_youtube_url   = get_option( 'vh_header_youtube_url' );
			$menu_login_header         = get_option( 'vh_login_header' );

			// Theme Options not saved? by default it should be enabled
			if($menu_login_header == false) {
				$menu_login_header = 'true';
			}

			$shadows = get_option( 'vh_element_shadows' );

			// Theme Options not saved? Shadows by default should be enabled
			if($shadows == false) {
				$shadows = 'true';
			}

			if ($shadows=='true') {
				$shadows = "shadows";
			} else {
				$shadows = "";
			}

			vh_check_followed_categories();
		?>
		<link rel="shortcut icon" href="<?php echo $favicon; ?>" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class($vh_class.$shadows); ?>>
		<?php if ( $_SERVER['SERVER_NAME'] == 'cohhe.com' ) { ?>
			<a href="http://themeforest.net/item/snaptube-premium-video-wordpress-theme/8026657" target="_blank" id="buy-now-ribbon"></a>
		<?php } ?>
		<div id="vh_loading_effect"></div>
		<div class="vh_wrapper" id="vh_wrappers">
		<div class="main-body-color"></div>
		<div class="overlay-hide"></div>
		<div class="pushy pushy-left">
			<div class="pushy_search"><?php get_search_form(); ?></div>
			<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary-menu',
						'menu_class'     => 'responsive-menu',
						'depth'          => 2,
						'link_before'    => '',
						'link_after'     => ''
					)
				);
			?>
		</div>
		<div class="wrapper st-effect-3 w_display_none" id="container">
			<div class="main">
				<header class="header vc_row wpb_row vc_row-fluid">
					<div class="top-header vc_col-sm-12">
						<div class="logo vc_col-sm-2">
							<a href="<?php echo home_url(); ?>"><img src="<?php echo $logo; ?>"<?php echo $logo_size_html ; ?> alt="<?php bloginfo('name'); ?>" /></a>
						</div>
						<div class="header_search"><?php get_search_form(); ?></div>
						<?php if ( $menu_login_header != 'false' ) {
								if ( is_user_logged_in() ) { ?>
									<div class="header-social-icons logout_icons vc_col-sm-4">
										<div class="header-icon logout-icon"><a href="<?php echo wp_logout_url( esc_url( home_url() ) ); ?>" class="micon-logout"></a></div>
								<?php } else { ?>
								<div class="header-social-icons login_icons vc_col-sm-4">
										<!-- <div class="header-icon user-icon"><a href="<?php echo site_url( 'wp-login.php?redirect_to=' . esc_url( home_url() ) ); ?>" class="icon-user"></a></div> -->
									
										<div class="morph-button morph-button-modal morph-button-modal-2 morph-button-fixed">
											<button type="button"><a href="#" class="icon-user user-icon"></a></button>
											<div class="morph-content active">
												<div>
													<div class="content-style-form content-style-form-1 login_form">
														<span class="icon icon-close login_form_close"></span>
														<h1><?php _e('Login', 'vh'); ?></h1>
														<p class="p_login"><input class="loginusername" type="text" id="username" name="login" placeholder="<?php _e( 'Username', 'vh' ); ?>"></p>
														<p class="p_password"><input class="loginpassword" type="password" id="password" name="password" placeholder="<?php _e( 'Password', 'vh' ); ?>"></p>
														<p class="status"></p>
														<p class="p_button">
															<input id="login_button" class="btn-primary" type="submit" name="commit" value="<?php _e( 'Login', 'vh' ); ?>">
															<?php if ( get_option('users_can_register', '0') ) { ?>
																<span>or </span><a href="javascript:void(1)" id="register_button_form"><?php _e( 'Sign up', 'vh' ); ?></a>
															<?php } ?>
														</p>
														<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
														<a href="<?php echo wp_lostpassword_url(); ?>" class="forgot_password"><?php _e( 'Forgot password', 'vh' ); ?></a>
													</div>
												</div>
											</div>
										</div><!-- morph-button -->
								<?php }
						} else { ?>
						<div class="header-social-icons vc_col-sm-4">
						<?php } ?>
						<?php if (!empty($menu_header_twitter_url)) { ?>
							<div class="header-icon twitter-icon"><a href="<?php echo $menu_header_twitter_url; ?>" class="icon-twitter-1" target="_blank"></a></div>
						<?php } ?>
						<?php if (!empty($menu_header_flickr_url)) { ?>
							<div class="header-icon flickr-icon"><a href="<?php echo $menu_header_flickr_url; ?>" class="icon-flickr" target="_blank"></a></div>
						<?php } ?>
						<?php if (!empty($menu_header_facebook_url)) { ?>
							<div class="header-icon facebook-icon"><a href="<?php echo $menu_header_facebook_url; ?>" class="icon-facebook" target="_blank"></a></div>
						<?php } ?>
						<?php if (!empty($menu_header_google_url)) { ?>
							<div class="header-icon google-icon"><a href="<?php echo $menu_header_google_url; ?>" class="icon-gplus" target="_blank"></a></div>
						<?php } ?>
						<?php if (!empty($menu_header_pinterest_url)) { ?>
							<div class="header-icon pinterest-icon"><a href="<?php echo $menu_header_pinterest_url; ?>" class="icon-pinterest" target="_blank"></a></div>
						<?php } ?>
						<?php if (!empty($menu_header_vkontakte_url)) { ?>
							<div class="header-icon vkontakte-icon"><a href="<?php echo $menu_header_vkontakte_url; ?>" class="icon-vkontakte" target="_blank"></a></div>
						<?php } ?>
						<?php if (!empty($menu_header_youtube_url)) { ?>
							<div class="header-icon youtube-icon"><a href="<?php echo $menu_header_youtube_url; ?>" class="icon-youtube" target="_blank"></a></div>
						<?php } ?>
					</div>
					<div class="menu-btn icon-menu-1"></div>
						<div class="clearfix"></div>
					</div>
				</header><!--end of header-->
				<div class="clearfix"></div>
				<?php
					wp_reset_postdata();
					$layout_type = get_post_meta(get_the_id(), 'layouts', true);

					if ( is_archive() || is_search() || is_404() || ( get_post_type() == 'tribe_events' && !is_single() ) ) {
						$layout_type = 'full';
					} else if ( is_home() ) {

						// Get the ID of your posts page
						$id = get_option('page_for_posts');

						$layout_type = get_post_meta($id, 'layouts', true) ? get_post_meta($id, 'layouts', true) : 'full';
					} elseif (empty($layout_type)) {
						$layout_type = get_option('vh_layout_style') ? get_option('vh_layout_style') : 'full';
					}

					switch ($layout_type) {
						case 'right':
							define('LAYOUT', 'sidebar-right');
							break;
						case 'full':
							define('LAYOUT', 'sidebar-no');
							break;
						case 'left':
							define('LAYOUT', 'sidebar-left');
							break;
					}