<?php
/*
* Template Name: Featured Videos Slider template
*/
get_header();

?>
<div class="video_carousel_container">
	<div id="video_jcarousel">
		<ul id="video_carousel" class="featured-videos-slider">
			<?php echo do_shortcode('[featured-videos-slider]'); ?>
		</ul>
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
			<div class="main-content <?php echo (LAYOUT != 'sidebar-no') ? 'vc_col-sm-7' : 'vc_col-sm-10'; ?>">
				<div class="main-inner">
					<!-- <h3><?php _e('Featured video', 'vh'); ?></h3> -->
					<!-- <div class="featured-slider-leading-video"><?php /*echo do_shortcode('[featured-videos-slider-leading-video]');*/ ?></div> -->
					<?php
					if (have_posts ()) {
						while (have_posts()) {
							the_post();
							the_content();
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