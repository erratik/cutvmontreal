<?php

// function for getting Tag name starts
function vh_get_video_detail($vid) {
	global $wpdb;
	$select = "SELECT distinct w.vid,w.*,s.guid FROM " . $wpdb->prefix ."hdflvvideoshare w
			   INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play m ON m.media_id = w.vid
			   INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=m.playlist_id
			   INNER JOIN " . $wpdb->prefix  . "posts s ON s.ID=w.slug
			   WHERE w.slug='$vid' AND w.publish='1' AND p.is_publish='1' GROUP BY w.vid";

	$themediafiles = $wpdb->get_results($select);
	$getPlaylist   = $wpdb->get_results("SELECT playlist_id FROM ".$wpdb->prefix."hdflvvideoshare_med2play WHERE media_id='".intval($themediafiles[0]->vid)."' LIMIT 1");

	$info = array('playlist_id' => $getPlaylist[0]->playlist_id, 'vid_id' => $themediafiles[0]->vid);

	return $info;
}

function vh_video_carousel_f() {
	global $wpdb, $dirPage;

	$vh_swfPath = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';
	$vid        = get_the_ID();
	$output     = '';

	if (!empty($vid)) {
		$homeplayerData = vh_get_video_detail($vid);
		$fetched[]      = $homeplayerData;
	}

	// store video details in variables
	if (!empty($homeplayerData)) {
		$videoId           = $homeplayerData['vid_id'];
		$video_playlist_id = $homeplayerData['playlist_id'];
	}

	$pluginflashvars = "baserefW=" . get_option('siteurl');
	$width = '100%';
	$height = '444px';

	// Display videos starts here
	$select = "SELECT distinct(a.vid),b.playlist_id,name,guid,description,file,hdfile,file_type,duration,embedcode,image,opimage,download,link,featured,hitcount,slug,
				a.post_date,postrollads,prerollads FROM " . $wpdb->prefix . "hdflvvideoshare a
				INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play b ON a.vid=b.media_id
				INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=b.playlist_id
				INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=a.slug
				WHERE b.playlist_id=" . intval($video_playlist_id) . " AND a.vid != " . intval($videoId) . " and a.publish='1' AND p.is_publish='1'
				ORDER BY a.vid DESC";

	$related = $wpdb->get_results($select);

	if ($related != '') {

		// Slide Display Here
		$li_style   = 1;
		$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
		foreach ($related as $relFet) {
			
			$file_type    = $relFet->file_type; // Video Type
			$imageFea     = $relFet->image; // VIDEO IMAGE
			$imageFea     = str_replace("/mq","/sd",$imageFea);

			if ( $li_style == 1 || $li_style == 2 || $li_style == 4 || $li_style == 5 ) {
				$imageFea  = $relFet->image;
				$video_sd_image = 'mqdef';
			} else {
				$image_header = get_headers_curl($imageFea);
				if ( mb_substr($image_header, 9, 3) == '404' ) {
					$imageFea  = $relFet->image;
					$video_sd_image = 'mqdef';
				} else {
					$imageFea  = str_replace("/mq","/sd",$relFet->image);
					$video_sd_image = 'sdimg';
				}
			}
			
			$reafile   = $relFet->file; // VIDEO IMAGE
			$guid      = vh_get_video_permalink($relFet->slug); //guid
			
			if ($imageFea == '') {  //If there is no thumb image for video
				$imageFea = APPTHA_VGALLERY_BASEURL . 'images' . DS . 'nothumbimage.jpg';
			} else {
				if ($file_type == 2 || $file_type == 5 ) {          //For uploaded image
					$imageFea = $image_path . $imageFea;
				} else {
					if ( $video_sd_image == 'sdimg' ) {
						$imageFea = vh_imgresize($imageFea, 371, 278, $relFet->slug);
					} elseif ( $video_sd_image == 'mqdef' ) {
						$imageFea = vh_imgresize($imageFea, 152, 98, $relFet->slug);
					}
				}
			}
			// Embed player code
			if( $file_type == 5 && !empty($relFet->embedcode) ) {
				$relFetembedcode   = stripslashes($relFet->embedcode);
				$relFetiframewidth = preg_replace(array('/width="\d+"/i'),array(sprintf('width="%d"', $width)),$relFetembedcode);
				$player_values = htmlentities(preg_replace(array('/height="\d+"/i'),array(sprintf('height="%d"', $height)),$relFetiframewidth));
			} else {
				$mobile = vgallery_detect_mobile();
				if( $mobile === true ) {

					// Check for youtube video
					if ( preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $reafile, $vresult) ) {
						$urlArray    = explode("=", $vresult[0]);
						$video_id    = trim($urlArray[1]);
						$reavideourl = "http://www.youtube.com/embed/$video_id";

						// Generate youtube embed code for html5 player
						$player_values = htmlentities('<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>');
					} else if ( $file_type != 5 ) { // Check for upload, URL and RTMP videos
						if ( $file_type == 2 ) { // For uploaded image
							$reavideourl = $image_path . $reafile;
						} else if ( $file_type == 4 ) { // For RTMP videos
							$streamer    = str_replace("rtmp://", "http://", $media->streamer_path);
							$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
						} elseif ( strpos($reafile, 'soundcloud.com') !== false ) {
							$reavideourl = $reafile;
						}

						// Generate video code for html5 player
						$player_values = htmlentities('<video id="video" poster="' . $imageFea . '"   src="' . $reavideourl .'" autobuffer controls >' . __('This format is not supported', 'vh') . '</video>');
					}
				} else {
					// Flash player code
					$player_values = htmlentities('<embed src="' . $vh_swfPath . '" flashvars="' . $pluginflashvars . '&amp;mtype=playerModule&amp;vid='.$relFet->vid.'" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">');
				}
			}
			if (get_post_type() == 'videogallery' || get_post_type() === 'videogallery') {
				$thumb_href = 'href="'. $guid.'"';
			} else {
				$player_div = 'mediaspace';
				$videodivId = rand();

				if (isset($arguments['id'])) {
					$videodivId .= $arguments['id']; // get video id from short code
					$vid         = $arguments['id'];
				}

				$thumb_href = 'href="'.$relFet->guid.'"';
			}
			if ( strlen($relFet->name) > 25 ) { // Displaying Video Title
				$t_videoname = mb_substr($relFet->name, 0, 25) . '..';
			} else {
				$t_videoname = $relFet->name;
			}

			if ( $li_style == 1 || $li_style == 2 || $li_style == 4 || $li_style == 5 ) {
				$li_style_name = 'small';
			} elseif ( $li_style == 3 ) {
				$li_style_name = 'wide';
			} elseif ( $li_style == 6 ) {
				$li_style_name = 'full';
			}
			
			$output .= '<li class="item '.$li_style_name.' '.$video_sd_image.'"><a ' . $thumb_href . '><img src="' . $imageFea . '" class="'.$video_sd_image.'" alt="' . $relFet->name . '" width="auto" height="auto" /><span class="v_img_info">';
			
			## Related video top slider title length ##
			if ( strlen($relFet->name) > 15 ) { // Displaying Video Title
				if ( $li_style == 1 || $li_style == 2 || $li_style == 4 || $li_style == 5 ) {
					$videoname = mb_substr($relFet->name, 0, 15) . '..';
				} else {
					$videoname = mb_substr($relFet->name, 0, 30) . '..';
				}
			} else {
				$videoname = $relFet->name;
			}

			$output .= $videoname;

			$output .='</span></a></li>';
			if ( $li_style==6 ) {
				$li_style = 0;
			}
			$li_style++;
		}
	}  
		
	return $output;
}
?>