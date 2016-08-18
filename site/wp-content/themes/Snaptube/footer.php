<?php
/**
 * The template for displaying the footer.
 */

global $vh_is_footer;
$vh_is_footer = true;

// Get theme footer logo
$footer_logo = get_option('vh_site_footer_logo');
if($footer_logo == false || $footer_logo == '') {
	$footer_logo = get_template_directory_uri() . '/images/footer-logo.png';
}

$retina_logo_class = '';
$logo_size_html = '';

// Get theme footer logo dimensions
$website_footer_logo_retina_ready = filter_var(get_option('vh_website_footer_logo_retina'), FILTER_VALIDATE_BOOLEAN);
if ((bool)$website_footer_logo_retina_ready != false) {
	$logo_size = getimagesize($footer_logo);
	$logo_size_html = ' style="width: ' . ($logo_size[0] / 2) . 'px; height: ' . ($logo_size[1] / 2) . 'px;" width="' . ($logo_size[0] / 2) . '" height="' . ($logo_size[1] / 2) . '"';
	$retina_logo_class = 'retina';
}

// Footer copyright
$copyrights = get_option('vh_footer_copyright') ? get_option('vh_footer_copyright') : '&copy; Snaptube. All rights reserved, [year]';
$copyrights = str_replace( '[year]', date('Y'), $copyrights);

// Scroll to top option
$scroll_to_top = filter_var(get_option('vh_scroll_to_top'), FILTER_VALIDATE_BOOLEAN);

// Footer style
$footer_menu_style = get_option( 'vh_footer_menu_style' );
if ( !$footer_menu_style ) {
	$footer_menu_style = $footer_menu_class = 'modern';
}

if ( $footer_menu_style == 'simple' ) {
	$footer_menu_class = 'default';
} else {
	$footer_menu_class = $footer_menu_style;
}
?>
			</div><!--end of main-->
		</div><!--end of wrapper-->
		<div class="footer-wrapper vc_col-sm-12">
			<div class="footer-container <?php echo $footer_menu_class; ?> vc_row wpb_row vc_row-fluid">
				<div class="footer-content">
					<div class="clearfix"></div>
				</div>
				<div class="footer-inner vc_col-sm-12">
					<?php if ($footer_menu_style == 'modern') { ?>
						<div class="footer_seperator"><div class="footer_menu_sep"></div></div>
					<?php } ?>
					<div class="bottom-menu-container vc_col-sm-7">
						<?php
							wp_nav_menu(
								array(
									'theme_location'  => 'footer-menu',
									'menu_class'      => 'footer-menu',
									'container'       => 'div',
									'container_class' => '',
									'depth'           => 1,
									'link_before'     => '',
									'link_after'      => '',
									'walker'          => new Footer_Walker
								)
							);
						?>
					</div>
					<div class="footer_info">
						<a href="<?php echo home_url(); ?>"><img src="<?php echo $footer_logo; ?>"<?php echo $logo_size_html ; ?> class="<?php echo $retina_logo_class; ?>" alt="<?php bloginfo('name'); ?>" /></a>
						<div class="copyright"><?php echo $copyrights; ?></p>
					</div>
				</div>
				<?php

				// Theme Options not saved? by default it should be enabled
				if($scroll_to_top == false) {
					$scroll_to_top = 'true';
				}

				if ( $scroll_to_top == 'true' ) { ?>
				<div class="scroll-to-top icon-up-small"></div>
				<?php } ?>
			</div>
		</div>
		</div>
		<?php
			$fixed_menu    = filter_var(get_option('vh_fixed_menu'), FILTER_VALIDATE_BOOLEAN);
			$tracking_code = get_option( 'vh_tracking_code' ) ? get_option( 'vh_tracking_code' ) : '';
			if ( !empty( $tracking_code ) ) { ?>
				<!-- Tracking Code -->
				<?php
				echo '
					' . $tracking_code;
			}
			wp_footer();
		?>
	</body>
</html>