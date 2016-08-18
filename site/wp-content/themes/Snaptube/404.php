<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Snaptube
 */
get_header();

	$title_404 = get_option(SHORTNAME . '_404_title', "This is somewhat embarrassing, isn't it?");
	$title_msg = get_option(SHORTNAME . '_404_message', "It seems we can't find what you're looking for. Perhaps searching, or one of the links below, can help.");
?>
<div class="page-<?php echo LAYOUT; ?> page-wrapper search-no-results">
	<div class="clearfix"></div>
	<div class="content vc_row wpb_row vc_row-fluid">
		<?php
		wp_reset_postdata();
		$suggested_videos = get_option('vh_suggested_videos') ? get_option('vh_suggested_videos') : '';
		vh_get_sidebar_menu();
		vh_get_suggested_videos($suggested_videos);
		?>
		<div class="<?php echo LAYOUT; ?>-pull">
			<div class="main-content <?php echo (LAYOUT != 'sidebar-no') ? '' : 'vc_col-sm-10'; ?>">
				<div class="page-title">
					<h1><?php echo $title_404; ?></h1>
				</div>
				<div class="main-inner notfound">
					<div class="vc_row-fluid">
						<p><?php echo $title_msg; ?></p>
						<p>&nbsp;</p>
						<?php require("searchform.php"); ?>
						<p>&nbsp;</p>
					</div>
				</div>
			</div>
		</div>
		<?php $vh_is_in_sidebar = false; ?>
		<div class="clearfix"></div>
	</div><!--end of content-->
	<div class="clearfix"></div>
</div><!--end of page-wrapper-->
<?php get_footer();