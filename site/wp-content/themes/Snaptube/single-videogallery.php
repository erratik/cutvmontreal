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
	$span_size = 'span9';
} else {
	$span_size = 'span8';
}

global $dirPage, $frontControllerPath, $wpdb, $dirPage;
if (function_exists('get_playlist_id')) {
	include_once ($frontControllerPath . 'videoshortcodeController.php');
	include_once WP_PLUGIN_DIR.'/snaptube-plugin/lib/functions/contusFunctions.php';
	include WP_PLUGIN_DIR.'/snaptube-plugin/lib/functions/contus.php';
}

?>
<div class="video_carousel_container">
	<div id="video_jcarousel">
		<ul id="video_carousel">

			<?php echo vh_video_carousel_f(); ?>
		</ul>
	</div>
	<div class="carousel_button_container">
		<div class="video_carousel_button icon-angle-up"></div>
	</div>
</div>

<div class="page-<?php echo LAYOUT; ?> page-wrapper video">
	<div class="clearfix"></div>
	<div class="content vc_row wpb_row vc_row-fluid">
		<?php
		wp_reset_postdata();
		$suggested_videos = get_option('vh_suggested_videos') ? get_option('vh_suggested_videos') : '';
		vh_get_sidebar_menu('true');
		vh_get_suggested_videos($suggested_videos);
		?>

		<div class="<?php echo LAYOUT; ?>-pull">
			<div class="main-content vc_col-sm-10">
			<?php
			if ( get_post_type( $post ) == 'post' ) {
				echo '<h1 class="blog_title">' . __( 'Blog', 'vh' ) . '</h1>' .
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
								get_template_part( 'content', 'video' );  ?>
									<div class="clearfix"></div>
									<?php
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
		<div class="clearfix"></div>
	</div><!--end of content-->
	<div class="clearfix"></div>
</div><!--end of page-wrapper-->
<?php get_footer();