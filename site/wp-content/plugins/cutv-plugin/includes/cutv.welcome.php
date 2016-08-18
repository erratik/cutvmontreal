<?php
	global $wpvr_pages;
	$wpvr_pages = TRUE;


	$grid = array(
		// LINE 1
		array(
			//COL 1
			array(
				'icon'       => 'fa-dashboard' ,
				'title'      => __( 'Plugin Dashboard' , WPVR_LANG ) ,
				'txt'        => __( 'The dashboard shows everything you need to know in a fancy way with beautiful graphics.' , WPVR_LANG ) ,
				'btn_icon'   => 'fa-arrow-right' ,
				'btn_label'  => __( 'Open Dashboard' , WPVR_LANG ) ,
				'btn_link'   => admin_url( 'admin.php?page=wpvr' ) ,
				'btn_target' => '_self' ,
			) ,

			//COL 1
			array(
				'icon'       => 'fa-gear' ,
				'title'      => __( 'Plugin Options' , WPVR_LANG ) ,
				'txt'        => __( 'Customize every facet and step of the plugin. There is a setting for almost everything !' , WPVR_LANG ) ,
				'btn_icon'   => 'fa-arrow-right' ,
				'btn_label'  => __( 'Manage Options' , WPVR_LANG ) ,
				'btn_link'   => admin_url( 'admin.php?page=wpvr-options' ) ,
				'btn_target' => '_self' ,
			) ,

			//COL 1
			array(
				'icon'       => 'fa-cubes' ,
				'title'      => __( 'Plugin Addons' , WPVR_LANG ) ,
				'txt'        => __( 'Enhance the plugin functionalities with several addons, or add other video services.' , WPVR_LANG ) ,
				'btn_icon'   => 'fa-arrow-right' ,
				'btn_label'  => __( 'Browse Addons' , WPVR_LANG ) ,
				'btn_link'   => admin_url( 'admin.php?page=wpvr-addons' ) ,
				'btn_target' => '_self' ,
			) ,
		) ,

		// LINE 1
		array(
			//COL 1
			array(
				'icon'       => 'fa-support' ,
				'title'      => __( 'Support Forums' , WPVR_LANG ) ,
				'txt'        => __( 'Our Support Team is here to help and assist you until you get satisfied with WP Video Robot.' , WPVR_LANG ) ,
				'btn_icon'   => 'fa-arrow-right' ,
				'btn_label'  => __( 'Get Support' , WPVR_LANG ) ,
				'btn_link'   => 'http://support.wpvideorobot.com/forum/support/' ,
				'btn_target' => '_blank' ,
			) ,

			//COL 1
			array(
				'icon'       => 'fa-book' ,
				'title'      => __( 'Documentation' , WPVR_LANG ) ,
				'txt'        => __( 'A clear and concise documentation written up to help you use WP Video Robot.' , WPVR_LANG ) ,
				'btn_icon'   => 'fa-arrow-right' ,
				'btn_label'  => __( 'Read Documentation' , WPVR_LANG ) ,
				'btn_link'   => 'http://doc.wpvideorobot.com/' ,
				'btn_target' => '_blank' ,
			) ,

			//COL 1
			array(
				'icon'       => 'fa-graduation-cap' ,
				'title'      => __( 'Tutorials' , WPVR_LANG ) ,
				'txt'        => __( 'A fully fledged and regularly added tutorials to help you get the most of WP Video Robot.' , WPVR_LANG ) ,
				'btn_icon'   => 'fa-arrow-right' ,
				'btn_label'  => __( 'Read Tutorials' , WPVR_LANG ) ,
				'btn_link'   => 'http://support.wpvideorobot.com/tutorials/' ,
				'btn_target' => '_blank' ,
			) ,
		) ,

	);

?>
<div class = "wrap wpvr_wrap" style = "display:none;">

	<ul>
		<li></li>
	</ul>
	<button>create channel</button>
</div>
