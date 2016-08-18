<?php
/*
* Template Name: Contacts template
*/
get_header();

// $vh_map_title   = (get_post_meta( $post->ID, 'vh_map_title', true )) ? '<div class="infobox-title"><h3>' . addslashes(get_post_meta( $post->ID, 'vh_map_title', true )) . '</h3></div>' : '';
$vh_map_address = (get_post_meta( $post->ID, 'vh_map_address', true )) ? '<p class="map-icon">' . addslashes(get_post_meta( $post->ID, 'vh_map_address', true )) . '</p>' : '';
$vh_map_phone   = (get_post_meta( $post->ID, 'vh_map_phone', true )) ? '<p class="phone-icon">' . addslashes(get_post_meta( $post->ID, 'vh_map_phone', true )) . '</p>' : '';
$vh_map_email   = (get_post_meta( $post->ID, 'vh_map_email', true )) ? '<p class="mail-icon">' . addslashes(get_post_meta( $post->ID, 'vh_map_email', true )) . '</p>' : '';
$vh_map_text    = (get_post_meta( $post->ID, 'vh_map_text', true )) ? '<p class="info-icon">' . addslashes(get_post_meta( $post->ID, 'vh_map_text', true )) . '</p>' : '';
$vh_map_lat     = (get_post_meta( $post->ID, 'vh_map_lat', true )) ? get_post_meta( $post->ID, 'vh_map_lat', true ) : '';
$vh_map_long    = (get_post_meta( $post->ID, 'vh_map_long', true )) ? get_post_meta( $post->ID, 'vh_map_long', true ) : '';
?>
<?php if ( get_option('vh_google_maps_api_key') ) { ?>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=false&amp;key=<?php echo get_option('vh_google_maps_api_key'); ?>"></script>
<?php } ?>
<script type="text/javascript">
(function() {
	//var map, gMap = google.maps;
	window.onload = function() {

	var secheltLoc = new google.maps.LatLng(<?php echo $vh_map_lat; ?>, <?php echo $vh_map_long; ?>);

		var myMapOptions = {
			 zoom: 15,
			 center: secheltLoc,
			 scrollwheel: false,
			 mapTypeId: google.maps.MapTypeId.ROADMAP,
			 disableDefaultUI: true
		};
		var theMap = new google.maps.Map(document.getElementById("map"), myMapOptions);

		//var myIcon = new google.maps.MarkerImage("<?php echo get_template_directory_uri(); ?>/images/map-marker.png", null, null, null, new google.maps.Size(22,35));

		var marker = new google.maps.Marker({
			map: theMap,
			draggable: true,
			position: new google.maps.LatLng(<?php echo $vh_map_lat; ?>, <?php echo $vh_map_long; ?>),
			visible: true,
			//icon: myIcon
		});

		var myOptions = {
			// content: boxText,
			 disableAutoPan: false
			,maxWidth: 0
			,pixelOffset: new google.maps.Size(-215, -50)
			,zIndex: null
			,alignBottom: true
			,boxStyle: {
				background: ""
				,opacity: 1
				,width: "426px"
				,padding: "50px 0 4px 0"
			}
			,closeBoxURL: ""
			,infoBoxClearance: new google.maps.Size(1, 1)
			,isHidden: false
			,pane: "floatPane"
			,enableEventPropagation: false
		};

		google.maps.event.addListener(marker, "click", function (e) {
			ib.open(theMap, this);
		});

		var ib = new InfoBox(myOptions);
		ib.setContent('<div class="infobox"><div class="infobox-content"><i class="micon-home"></i><?php echo $vh_map_address; ?><i class="micon-phone"></i><?php echo $vh_map_phone; ?><i class="micon-mail-alt"></i><?php echo $vh_map_email; ?><i class="micon-info"></i><?php echo $vh_map_text; ?></div></div>');
		ib.open(theMap, marker);
	}
})();

</script>

<div class="video_carousel_container">
	<div id="map"></div>
	<div class="carousel_button_container">
		<div class="video_carousel_button icon-angle-up"></div>
	</div>
</div>
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
				if ( !is_front_page() && !is_home() ) { ?>
					<div class="page-title">
						<?php echo  the_title( '<h1>', '</h1>' ); ?>
					</div>
				<?php } ?>
				<?php
				if ( !is_front_page() && !is_home() ) {
					echo vh_breadcrumbs();
				} ?>
				<?php
				if ( isset($img[0]) ) { ?>
					<div class="entry-image">
						<img src="<?php echo $img[0]; ?>" class="open_entry_image <?php echo $span_size; ?>" alt="" />
					</div>
				<?php } ?>
				<div class="main-inner">
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