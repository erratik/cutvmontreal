<?php
/*
* Extended Visual Composer plugin
*/

// Remove sidebar element
vc_remove_element("vc_widget_sidebar");
vc_remove_element("vc_images_carousel");
vc_remove_element("vc_toggle");
vc_remove_element("vc_tour");
vc_remove_element("vc_carousel");
vc_remove_element("vc_cta_button");

// Remove default WordPress widgets
vc_remove_element("vc_wp_search");
vc_remove_element("vc_wp_meta");
vc_remove_element("vc_wp_recentcomments");
vc_remove_element("vc_wp_calendar");
vc_remove_element("vc_wp_pages");
vc_remove_element("vc_wp_tagcloud");
vc_remove_element("vc_wp_custommenu");
vc_remove_element("vc_wp_text");
vc_remove_element("vc_wp_posts");
vc_remove_element("vc_wp_links");
vc_remove_element("vc_wp_categories");
vc_remove_element("vc_wp_archives");
vc_remove_element("vc_wp_rss");

vc_add_param("vc_row", array(
	"type"        => "dropdown",
	"class"       => "",
	"heading"     => __("Row style", "vh"),
	"admin_label" => true,
	"param_name"  => "type",
	"value"       => array(
		__( "Default", "vh" )           => "0",
		__( "Full Width - White", "vh" ) => "1"
	),
	"description" => ""
));

// Gap
vc_map( array(
	"name"     => __( "Gap", "vh" ),
	"base"     => "vh_gap",
	"icon"     => "icon-wpb-ui-gap-content",
	"class"    => "vh_vc_sc_gap",
	"category" => __( "by Snaptube", "vh" ),
	"params"   => array(
		array(
			"type"        => "textfield",
			"class"       => "",
			"heading"     => __( "Gap height", "vh" ),
			"admin_label" => true,
			"param_name"  => "height",
			"value"       => "10",
			"description" => __( "In pixels", "vh" )
		)
	)
) );

$colors_arr = array(
	__("Red", "vh")    => "red",
	__("Blue", "vh")   => "blue",
	__("Yellow", "vh") => "yellow",
	__("Green", "vh")  => "green"
);

// Update Buttons map
$colors_arr = array(__("Transparent", "vh") => "btn-transparent", __("Blue", "vh") => "btn-primary", __("Light Blue", "vh") => "btn-info", __("Green", "vh") => "btn-success", __("Yellow", "vh") => "btn-warning", __("Red", "vh") => "btn-danger", __("Inverse", "vh") => "btn-inverse");

$icons_arr = array(
	__("None", "vh")                     => "none",
	__("Address book icon", "vh")        => "wpb_address_book",
	__("Alarm clock icon", "vh")         => "wpb_alarm_clock",
	__("Anchor icon", "vh")              => "wpb_anchor",
	__("Application Image icon", "vh")   => "wpb_application_image",
	__("Arrow icon", "vh")               => "wpb_arrow",
	__("Asterisk icon", "vh")            => "wpb_asterisk",
	__("Hammer icon", "vh")              => "wpb_hammer",
	__("Balloon icon", "vh")             => "wpb_balloon",
	__("Balloon Buzz icon", "vh")        => "wpb_balloon_buzz",
	__("Balloon Facebook icon", "vh")    => "wpb_balloon_facebook",
	__("Balloon Twitter icon", "vh")     => "wpb_balloon_twitter",
	__("Battery icon", "vh")             => "wpb_battery",
	__("Binocular icon", "vh")           => "wpb_binocular",
	__("Document Excel icon", "vh")      => "wpb_document_excel",
	__("Document Image icon", "vh")      => "wpb_document_image",
	__("Document Music icon", "vh")      => "wpb_document_music",
	__("Document Office icon", "vh")     => "wpb_document_office",
	__("Document PDF icon", "vh")        => "wpb_document_pdf",
	__("Document Powerpoint icon", "vh") => "wpb_document_powerpoint",
	__("Document Word icon", "vh")       => "wpb_document_word",
	__("Bookmark icon", "vh")            => "wpb_bookmark",
	__("Camcorder icon", "vh")           => "wpb_camcorder",
	__("Camera icon", "vh")              => "wpb_camera",
	__("Chart icon", "vh")               => "wpb_chart",
	__("Chart pie icon", "vh")           => "wpb_chart_pie",
	__("Clock icon", "vh")               => "wpb_clock",
	__("Fire icon", "vh")                => "wpb_fire",
	__("Heart icon", "vh")               => "wpb_heart",
	__("Mail icon", "vh")                => "wpb_mail",
	__("Play icon", "vh")                => "wpb_play",
	__("Shield icon", "vh")              => "wpb_shield",
	__("Video icon", "vh")               => "wpb_video"
);

$target_arr = array(__("Same window", "vh") => "_self", __("New window", "vh") => "_blank");
$size_arr = array(__("Regular size", "vh") => "wpb_regularsize", __("Large", "vh") => "btn-large", __("Small", "vh") => "btn-small", __("Mini", "vh") => "btn-mini");

vc_map( array(
  "name" => __("Button", "vh"),
  "base" => "vc_button",
  "icon" => "icon-wpb-ui-button",
  "category" => __('Content', 'vh'),
  "params" => array(
    array(
      "type" => "textfield",
      "heading" => __("Text on the button", "vh"),
      "holder" => "button",
      "class" => "wpb_button",
      "param_name" => "title",
      "value" => __("Text on the button", "vh"),
      "description" => __("Text on the button.", "vh")
    ),
    array(
      "type" => "textfield",
      "heading" => __("URL (Link)", "vh"),
      "param_name" => "href",
      "description" => __("Button link.", "vh")
    ),
    array(
      "type" => "dropdown",
      "heading" => __("Target", "vh"),
      "param_name" => "target",
      "value" => $target_arr,
      "dependency" => Array('element' => "href", 'not_empty' => true)
    ),
    array(
      "type" => "dropdown",
      "heading" => __("Color", "vh"),
      "param_name" => "color",
      "value" => $colors_arr,
      "description" => __("Button color.", "vh")
    ),
    array(
      "type" => "dropdown",
      "heading" => __("Icon", "vh"),
      "param_name" => "icon",
      "value" => $icons_arr,
      "description" => __("Button icon.", "vh")
    ),
    array(
      "type" => "dropdown",
      "heading" => __("Size", "vh"),
      "param_name" => "size",
      "value" => $size_arr,
      "description" => __("Button size.", "vh")
    ),
    array(
      "type" => "textfield",
      "heading" => __("Extra class name", "vh"),
      "param_name" => "el_class",
      "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "vh")
    )
  ),
  "js_view" => 'VcButtonView'
) );

// Featured videos
vc_map( array(
	"name" => __("Featured videos", "vh"),
	"base" => "featured-videos",
	"class" => "",
	"icon" => "icon-wpb-ui-gap-content",
	"category" => __( "by Snaptube", "vh" ),

	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Number of featured videos", "vh"),
			"param_name" => "videos",
			"value" => "",
			"description" => __("How many featured videos to show. Note: maximum of 5", "vh")
			)
		)
) );

// Video module
$video_module_style = array(
	__("Default", "vh")    => "",
	__("Video thumbnails", "vh")   => "video_thumb",
	__("Video list", "vh")   => "video_list"
);
vc_map( array(
	"name" => __("Video module", "vh"),
	"base" => "video-module",
	"class" => "",
	"icon" => "icon-wpb-ui-gap-content",
	"category" => __( "by Snaptube", "vh" ),

	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title", "vh"),
			"param_name" => "video_title",
			"value" => "",
			"description" => __("Enter title for this module.", "vh")
			),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Category ID", "vh"),
			"param_name" => "playlist_id",
			"value" => "",
			"description" => __("Which category to show, leave it empty for first category. Available <a href=\"admin.php?page=playlist\">here</a>.", "vh")
			),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Displayed video count", "vh"),
			"param_name" => "video_count",
			"value" => '',
			"description" => __("Number of videos to display, leave empty for no limitation.", "vh")
			),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Exclude", "vh"),
			"param_name" => "exclude",
			"value" => "",
			"description" => __("Do you want to exclude particular videos? You can specify multiple ID's by separating them by comma. Example: 173,264,343,476")
			),
		array(
			"type" => "dropdown",
			"heading" => __("Video module style", "vh"),
			"param_name" => "video_style",
			"value" => $video_module_style,
			"description" => __("Select video module style.", "vh")
		    ),
		)
) );

// Video carousel
$video_carousel_style = array(
	__("Default", "vh")    => "",
	__("Video list", "vh") => "video_list"
);
$video_carousel_rows = array(
	__("1", "vh") => "1",
	__("2", "vh") => "2",
	__("3", "vh") => "3"
);
vc_map( array(
	"name" => __("Video carousel", "vh"),
	"base" => "video-carousel",
	"class" => "",
	"icon" => "icon-wpb-ui-gap-content",
	"category" => __( "by Snaptube", "vh" ),

	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title", "vh"),
			"param_name" => "video_c_title",
			"value" => "",
			"description" => __("Enter title for this module.", "vh")
			),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Category ID", "vh"),
			"param_name" => "playlist_c_id",
			"value" => "",
			"description" => __("Which category to show, leave it empty for first category. Available <a href=\"admin.php?page=playlist\">here</a>.", "vh")
			),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Displayed video count", "vh"),
			"param_name" => "video_c_count",
			"value" => '',
			"description" => __("Number of videos to display, leave empty for no limitation.", "vh")
			),
		array(
			"type" => "dropdown",
			"heading" => __("Video module style", "vh"),
			"param_name" => "video_c_style",
			"value" => $video_carousel_style,
			"description" => __("Select video module style.", "vh")
			),
		array(
			"type" => "textfield",
			"heading" => __("Carousel autoplay", "vh"),
			"param_name" => "video_c_autoplay",
			"value" => 'false',
			"description" => __("Will carousel autoplay when page loads. You can use true/false/time in ms", "vh")
			),
		array(
			"type" => "textfield",
			"heading" => __("Carousel speed", "vh"),
			"param_name" => "video_c_speed",
			"value" => '2000',
			"description" => __("Carousel animation speed, default 2000ms", "vh")
			),
		array(
			"type" => "dropdown",
			"heading" => __("Rows", "vh"),
			"param_name" => "rows",
			"value" => $video_carousel_rows,
			"description" => __("Select video module rows count.", "vh")
			),
		)
) );

// Video more
vc_map( array(
		"name" => __("Video category", "vh"),
		"base" => "video_category",
		"class" => "",
		"icon" => "icon-wpb-ui-gap-content",
		"category" => __( "by Snaptube", "vh" ),
	)
);

// Video more
vc_map( array(
		"name" => __("Video home", "vh"),
		"base" => "video_home",
		"class" => "",
		"icon" => "icon-wpb-ui-gap-content",
		"category" => __( "by Snaptube", "vh" ),
	)
);

// Followed Video module
$video_module_style = array(
	__("Default", "vh")    => "",
	__("Video thumbnails", "vh")   => "video_thumb",
	__("Video list", "vh")   => "video_list"
);
vc_map( array(
	"name" => __("Followed videos", "vh"),
	"base" => "followed-video-module",
	"class" => "",
	"icon" => "icon-wpb-ui-gap-content",
	"category" => __( "by Snaptube", "vh" ),

	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title", "vh"),
			"param_name" => "video_title",
			"value" => "",
			"description" => __("Enter title for this module.", "vh")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Displayed video count", "vh"),
			"param_name" => "video_count",
			"value" => '',
			"description" => __("Number of videos to display, leave empty for no limitation.", "vh")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Count of excluded videos", "vh"),
			"param_name" => "excluded_video_count",
			"value" => '',
			"description" => __("Number of videos to exclude starting with the first one.", "vh")
		),
		array(
			"type" => "dropdown",
			"heading" => __("Video module style", "vh"),
			"param_name" => "video_style",
			"value" => $video_module_style,
			"description" => __("Select video module style.", "vh")
		),
	)
) );

function vh_add_vc_grid_shortcodes( $shortcodes ) {
	$shortcodes['vh_vcgi_image'] = array(
		"name" => __("Snaptube - Image"),
		"base" => "vh_vcgi_image",
		"class" => "",
		"icon" => "icon-wpb-ui-gap-content",
		"category" => __( "by Snaptube", "vh" ),
		"params" => array(),
		"post_type" => Vc_Grid_Item_Editor::postType(),
		"show_settings_on_create" => false
	);

	$shortcodes['vh_vcgi_topmeta'] = array(
		"name" => __("Snaptube - Top meta"),
		"base" => "vh_vcgi_topmeta",
		"class" => "",
		"icon" => "icon-wpb-ui-gap-content",
		"category" => __( "by Snaptube", "vh" ),
		"params" => array(),
		"post_type" => Vc_Grid_Item_Editor::postType(),
		"show_settings_on_create" => false
	);

	$shortcodes['vh_vcgi_bottom'] = array(
		"name" => __("Snaptube - Bottom"),
		"base" => "vh_vcgi_bottom",
		"class" => "",
		"icon" => "icon-wpb-ui-gap-content",
		"category" => __( "by Snaptube", "vh" ),
		"params" => array(),
		"post_type" => Vc_Grid_Item_Editor::postType(),
		"show_settings_on_create" => false
	);

	return $shortcodes;
}
add_filter( 'vc_grid_item_shortcodes', 'vh_add_vc_grid_shortcodes', 100 );

?>