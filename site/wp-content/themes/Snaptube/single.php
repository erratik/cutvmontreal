<?php
/**
 * Single template file.
 */
get_header();

$layout_type = get_post_meta(get_the_id(), 'layouts', true);

if(empty($layout_type)) {
	$layout_type = get_option('vh_layout_style') ? get_option('vh_layout_style') : 'full';
}

$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'offer-image-large' );

if ( LAYOUT == 'sidebar-no' ) {
	$span_size = 'vc_col-sm-9';
} else {
	$span_size = 'vc_col-sm-8';
}

?>
<div class="page-<?php echo LAYOUT; ?> page-wrapper">
	<div class="clearfix"></div>
	<div class="content vc_row wpb_row vc_row-fluid">
		<?php
		wp_reset_postdata();
		$suggested_videos = get_option('vh_suggested_videos') ? get_option('vh_suggested_videos') : '';
		vh_get_sidebar_menu('true');
		vh_get_suggested_videos($suggested_videos);
		?>

		<div class="<?php echo LAYOUT; ?>-pull">
			<div class="main-content <?php echo (LAYOUT != 'sidebar-no') ? 'vc_col-sm-7' : 'vc_col-sm-10'; ?>">
			<?php
			if ( get_post_type( $post ) == 'post' ) {
				echo '<div class="page-title"><h1>' . __( 'Blog', 'vh' ) . '</h1></div>' .
				vh_breadcrumbs();
			} elseif ( !is_front_page() && !is_home() ) { ?>
				<div class="page-title">
					<?php echo  the_title( '<h1>', '</h1>' );?>
			</div>
			<?php echo vh_breadcrumbs(); ?>
			<?php } ?>
				<div class="main-inner">
					<div class="vc_row-fluid">
						<?php
						if ( have_posts() ) {
							while ( have_posts() ) {
								the_post();
								get_template_part( 'content', 'single' ); 
								if ( get_post_type( $post ) == 'post' ) { ?>
									<div class="clearfix"></div>
									<div class="comments_container vc_col-sm-12">
											<nav class="nav-single">
												<span class="nav-previous"><i></i><?php previous_post_link( '%link', ' %title' ); ?></span>
												<?php posts_nav_link(); ?>
												<span class="nav-next"><i></i><?php next_post_link( '%link', '%title ' ); ?></span>
												<div class="clearfix"></div>
											</nav><!-- .nav-single -->
										<div class="clearfix"></div>
										<?php
										echo '<div class="comment_seperator"></div>';
										comments_template( '', true ); ?>
									</div>
									<?php
								}
							}
						} else {
							echo '
								<h2>Nothing Found</h2>
								<p>Sorry, it appears there is no content in this section.</p>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
		if (LAYOUT == 'sidebar-right') {
		?>
		<div class="vc_col-sm-3 pull-right <?php echo LAYOUT; ?>">
			<div class="sidebar-inner">
			<?php
				global $vh_is_in_sidebar;
				$vh_is_in_sidebar = true;
				generated_dynamic_sidebar();
			?>
			<div class="clearfix"></div>
			</div>
		</div><!--end of span3-->
		<?php } ?>
		<?php $vh_is_in_sidebar = false; ?>
		<div class="clearfix"></div>
	</div><!--end of content-->
	<div class="clearfix"></div>
</div><!--end of page-wrapper-->
<?php get_footer();