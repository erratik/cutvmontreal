<?php

global $dirPage, $frontControllerPath, $wpdb, $dirPage;
if (function_exists('get_playlist_id')) {
	include_once ($frontControllerPath . 'videoshortcodeController.php');
	include_once ($frontControllerPath . 'videomoreController.php');
	include_once WP_PLUGIN_DIR.'/snaptube-plugin/lib/functions/contusFunctions.php';
}

// Label
function vh_gap($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'height' => 10,
	), $atts ) );

	$output = '<div class="gap" style="line-height: ' . absint($height) . 'px; height: ' . absint($height) . 'px;"></div>';

	return $output;
}
add_shortcode('vh_gap', 'vh_gap');

// Pricing table
function vh_pricing_table( $atts, $content = null ) {
	extract( shortcode_atts( array(
				'pricing_title'       => '',
				'pricing_block_color' => '',
				'content1'            => '',
				'content2'            => '',
				'price'               => '',
				'button_link'         => '',
				'el_class'            => '',
			), $atts ) );

	$attributes = '';
	$button_link = vc_build_link($button_link);

	if ( !empty($button_link['target']) ) {
		$attributes = ' target="' . $button_link['target'] . '"';
	}

	$output = '
	<div class="pricing-table pricing_color_' . $pricing_block_color . '">
		<div class="pricing-content">
			<div class="pricing-title">' . $pricing_title . '</div>
			<div class="pricing-desc-1">' . $content1 . '</div>
			<div class="pricing-desc-2">' . $content2 . '</div>
			<div class="pricing-price">' . $price . '</div>
			<div class="pricing-button"><a href="' . $button_link['url'] . '" class="no_before blue-button"' . $attributes . '>' . $button_link['title'] . '</a></div>
		</div>
	</div>';

	return $output;
}
add_shortcode('vh_pricing_table', 'vh_pricing_table');

function vh_featured_video_func($attributes) {

		extract(shortcode_atts(array(
			'videos' => '1',
		), $attributes));
		if ($videos > '5') {
			$videos = '5';
		}

		$pluginName = 'contus-video-gallery';

		global $wpdb, $dirPage;
		$site_url     = get_bloginfo('url');
		$bannervideos = "SELECT distinct w.*,p.playlist_name FROM " . $wpdb->prefix . "hdflvvideoshare w
			  INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play m ON m.media_id = w.vid
			  INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=m.playlist_id
			  WHERE featured='1' and publish='1' AND p.is_publish='1' GROUP BY w.vid ORDER BY w.ordering DESC LIMIT ". $videos;
		$bannerSlideShow = $wpdb->get_results($bannervideos);
		if(isset($dirPage)){
			$pluginName = $dirPage;
		}
		$output = '<script>
		window.onload = function(){
			var lt=false;
			vid = "fragment-'. $bannerSlideShow[0]->vid.'";
			sourceCode = document.getElementById(vid).innerHTML;
			embedCode  = sourceCode.replace("embecontus","embed");
			embedCode  = embedCode.replace("iframcontus","iframe");
			embedCode  = embedCode.replace("videcontus","video");
			if(lt==true){
			embedCode  = sourceCode.replace("EMBECONTUS","EMBED");
			embedCode  = embedCode.replace("IFRAMCONTUS","IFRAME");
			embedCode  = embedCode.replace("VIDECONTUS","IFRAME");
			}
			document.getElementById("nav-"+vid).className = "ui-tabs-nav-item ui-tabs-selected";
			document.getElementById("videoPlay").innerHTML = embedCode;
		}
		</script>';

		$output .= '<div id="featured" class="wpb_row">';
		if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
			$output .= '<div id="lofslidecontent45"	class="lof-slidecontent lof-snleft vc_col-sm-8">
						<div class="right_side">
						<div id="videoPlay" class="ui-tabs-panel" style="height:100%">
						</div>
						<input type="hidden" id="activeCSS" value="fragment-'. $bannerSlideShow[0]->vid . '" />';

			$bannertype = '';
			for ($i = 0; $i < count($bannerSlideShow); $i++) {
				if($bannertype=='category') {
					$baseref = "&pid=".$playid;
				} else {
					$baseref = "";
				}
				
				$output .= '<div id="fragment-' . $bannerSlideShow[$i]->vid . '" class="ui-tabs-panel" style="height:100%;float:right">';
				$file_type = $bannerSlideShow[$i]->file_type;
				if ($file_type == 5) {
					$bannerembedcode    = stripslashes($bannerSlideShow[$i]->embedcode);
					$banneriframecode   =  str_replace('<iframe', '<iframcontus', $bannerembedcode);
					$banneriframewidth  =  str_replace('width=', 'width="100%"', $banneriframecode);
					$output            .= str_replace('height=', 'height="444"', $banneriframewidth);
				} else {
					$mobile = videostream_detectmobile();
					if($mobile === true) {
						$videourl   = $bannerSlideShow[$i]->file;
						$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
						$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
						$imgurl     = $bannerSlideShow[$i]->image;
						$file_type  = $bannerSlideShow[$i]->file_type;

						// If there is no thumb image for video
						if ($imgurl == '') {
							$imgurl = $_imagePath . 'nothumbimage.jpg';
						} else {
							if ($file_type == 2) { ## For uploaded image
								$imgurl      = $image_path . $imgurl;
							}
						}

						if (preg_match('/www\.youtube\.com\/watch\?v=[^&]+/', $videourl, $vresult)) {
							$urlArray = explode("=", $vresult[0]);
							$videoid  = trim($urlArray[1]);

						$output .= '<iframcontus  type="text/html" width="100%" height="444" src="http://www.youtube.com/embed/' . $videoid. '" frameborder="0">
						</iframcontus>';

						} else { 
							if ($file_type == 2) { ## For uploaded image
								$videourl = $image_path . $videourl;
							} else if ($file_type == 4) { ## For RTMP videos
								$streamer = str_replace("rtmp://", "http://", $bannerSlideShow[$i]->streamer_path);
								$videourl = $streamer . '_definst_/mp4:' . $videourl . '/playlist.m3u8';
							}
						
							$output .= '<videcontus id="video" width="100%" height="444" poster="'. $imgurl .'" src="'
							. $videourl .'" autobuffer controls>Html5 Not support This video Format.</video>';
						}
					} else {
						$output .= '<embecontus
							src="'
							 . $site_url . '/wp-content/plugins/' . $pluginName . '/hdflvplayer/hdplayer.swf"
							flashvars="baserefW='
							 . $site_url . '&banner=true&mtype=playerModule&vid='
							 . $bannerSlideShow[$i]->vid . '&'
							 . $baseref . '&Preview='
							 . $bannerSlideShow[$i]->image . '"
							style="width:100%" allowFullScreen="true"
							allowScriptAccess="always" type="application/x-shockwave-flash"
							wmode="transparent"></embecontus>';
						}
					}
					$output .= '</div>';
			}
			$output .= '</div></div>';	
		} else {
			$output .= '
			<div id="lofslidecontent45" class="lof-slidecontent lof-snleft vc_col-sm-8">
				<div class="right_side">
					<div id="videoPlay" class="ui-tabs-panel" style="height:100%">
					</div>
				</div>
				<input type="hidden" id="activeCSS" value="fragment-'. $bannerSlideShow[0]->vid . '" />';
				$bannertype = '';
				for ( $i = 0; $i < count($bannerSlideShow); $i++ ) {
					if( $bannertype=='category' ) {
						$baseref = "&pid=" . $playid;
					} else {
						$baseref = "";
					}
					
					$output .= '<div id="fragment-' . $bannerSlideShow[$i]->vid . '" class="ui-tabs-panel" style="height:0px;float:right;width:0px">';
					$file_type = $bannerSlideShow[$i]->file_type;

					if ( $file_type == 5 ) {
						$bannerembedcode = stripslashes($bannerSlideShow[$i]->embedcode);
						$banneriframecode =  str_replace('<iframe', '<iframcontus', $bannerembedcode);
						$banneriframewidth =  str_replace('width=', 'width="100%"', $banneriframecode);
						$output .= str_replace('height=', 'height="444"', $banneriframewidth);
					} elseif ( $file_type == 2 ) {
						$videourl   = $image_path . $relFet->file;

						$output .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
					} else {
						$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
						$mobile     = videostream_detectmobile();

						if( $mobile === true ) {
							$videourl   = $bannerSlideShow[$i]->file;
							$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
							$imgurl     = $bannerSlideShow[$i]->image;
							$file_type  = $bannerSlideShow[$i]->file_type;

							// If there is no thumb image for video
							if ($imgurl == '') {
								$imgurl = $_imagePath . 'nothumbimage.jpg';
							} else {

								// For uploaded image
								if ($file_type == 2) {
									$imgurl = $image_path . $imgurl;
								}
							}

							if (preg_match('/www\.youtube\.com\/watch\?v=[^&]+/', $videourl, $vresult)) {
								$urlArray = explode("=", $vresult[0]);
								$videoid  = trim($urlArray[1]);

							$output .= '<iframcontus  type="text/html" width="100%" height="444" src="http://www.youtube.com/embed/' . $videoid. '" frameborder="0">
							</iframcontus>';

							} else { 
								if ($file_type == 2) { ## For uploaded image
									$videourl = $image_path . $videourl;
								} else if ($file_type == 4) { ## For RTMP videos
									$streamer = str_replace("rtmp://", "http://", $bannerSlideShow[$i]->streamer_path);
									$videourl = $streamer . '_definst_/mp4:' . $videourl . '/playlist.m3u8';
								}
								
								$output .= '<videcontus id="video" width="100%" height="444" poster="'. $imgurl .'" src="'. $videourl .'" autobuffer controls>Html5 Not support This video Format.</video>';
								
							}
				   		} else {
							if( strpos($bannerSlideShow[$i]->file, 'v=') !== false ) {
								$video_link = explode('v=', $bannerSlideShow[$i]->file);
								if ( strpos($bannerSlideShow[$i]->link, 'rel=') !== false ) {
									$video_rel = explode('?', $bannerSlideShow[$i]->link);
									$output .= '<iframe width="100%" height="444" src="//www.youtube.com/embed/' . $video_link[1] . '?' . $video_rel[1] . '" frameborder="0" allowfullscreen></iframe>';
								} else {
									$output .= '<iframe width="100%" height="444" src="//www.youtube.com/embed/' . $video_link[1] . '" frameborder="0" allowfullscreen></iframe>';
								}
							} elseif ( strpos($bannerSlideShow[$i]->file, '/v/') !== false ) {
								$video_link = explode('/v/', $bannerSlideShow[$i]->file);
								$output .= '<iframe src="//www.viddler.com/embed/' . $video_link[1] . '" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
							} elseif ( strpos($bannerSlideShow[$i]->file, '/video/') !== false ) {
								$video_link = explode('/video/', $bannerSlideShow[$i]->file);
								$video_link = explode('_', $video_link[1]);
								$output .= '<iframe frameborder="0" width="100%" height="444" src="//www.dailymotion.com/embed/video/' . $video_link[0] . '" allowfullscreen></iframe>';
							} else {
								//$output .= '<div style="display: none; position: relative; width: 642px; height: 444px;">' . do_shortcode('[video poster="' . $image_path . $bannerSlideShow[$i]->image . '" width="642" height="444" src="' . $image_path . $bannerSlideShow[$i]->file . '"]') . '</div>';
							}
						}
					}
					$output .= '</div>';
				}
			$output .= '</div>';
		}
		$output .= '<div class="page-bannershort vc_col-sm-4" id="gallery_banner_list" >
			<ul class="page-lof-navigator">';
		for ($i = 0; $i < count($bannerSlideShow); $i++) {
			$output .= '<li class="ui-tabs-nav-item " id="nav-fragment-'.$bannerSlideShow[$i]->vid.'">
				<div class="nav_container">
						<a href="#" class="switch_featured_video" id="fragment-'. $bannerSlideShow[$i]->vid.'">
							<div class="featured_outline"><div class="page-thumb-img"><div class="video_play">';
								$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
								$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
								$thumb_image = $bannerSlideShow[$i]->image; ## Get thumb image
								$file_type = $bannerSlideShow[$i]->file_type; ## Get file type of a video

									if ($thumb_image == '') { ## If there is no thumb image for video
										$thumb_image = $_imagePath . 'nothumbimage.jpg';
									} else {
										if ($file_type == 2 || $file_type == 5) {      ## For uploaded image
											$thumb_image = $image_path . $thumb_image;
										}
									}

								$output .= '<img src="'. "$thumb_image".'" alt="thumb image" /></div></div></div>
										   <div class="slide_video_info" >';
								$output .= '<div class="video_name">'.mb_substr($bannerSlideShow[$i]->name, 0, 25).'</div>';
								$output .= '<div class="video_views icon-eye">'. $bannerSlideShow[$i]->hitcount . '</div>';
								$tc = wp_count_comments($bannerSlideShow[$i]->slug);
								$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
								if ( function_exists('get_post_ul_meta') ) {
									$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($bannerSlideShow[$i]->slug, "like") . '</div>';
								}
								$output .= '</div>
					</a>
				</div>
			</li>';
			} 
		$output .= '</ul>
		</div></div>';
		return $output;
}
add_shortcode('featured-videos', 'vh_featured_video_func');

function vh_featured_video_slider_func($attributes) {
		extract(shortcode_atts(array(
			'videos' => '18',
		), $attributes));

		global $wpdb, $dirPage;

		$thumb_href      = '';
		$site_url        = get_bloginfo('url');
		$li_style        = 1;
		$bannervideos    = "SELECT distinct w.*,p.playlist_name,s.guid FROM " . $wpdb->prefix . "hdflvvideoshare w
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play m ON m.media_id = w.vid
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=m.playlist_id
							INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=w.slug
							WHERE featured='1' and publish='1' AND p.is_publish='1' GROUP BY w.vid ORDER BY w.ordering ASC LIMIT ". $videos;
		$bannerSlideShow = $wpdb->get_results($bannervideos);

		if( isset($dirPage) ) {
			$pluginName = $dirPage;
		}
		$output = '';

		for ($i = 0; $i < count($bannerSlideShow); $i++) {
			$guid = vh_get_video_permalink($bannerSlideShow[$i]->slug); // Guid

			$file_type = $bannerSlideShow[$i]->file_type; // Video Type
			$imageFea  = $bannerSlideShow[$i]->image; // Video Image
			$imageFea  = str_replace("/mq","/sd",$imageFea);

			if ( $li_style == 1 || $li_style == 2 || $li_style == 4 || $li_style == 5 ) {
				$imageFea       = $bannerSlideShow[$i]->image;
				$video_sd_image = 'mqdef';
			} else {
				$image_header = get_headers_curl($imageFea);
				if ( mb_substr($image_header, 9, 3) == '404' ) {
					$imageFea       = $bannerSlideShow[$i]->image;
					$video_sd_image = 'mqdef';
				} else {
					$imageFea       = str_replace("/mq","/sd", $bannerSlideShow[$i]->image);
					$video_sd_image = 'sdimg';
				}
			}
			

			if ( $li_style == 1 || $li_style == 2 || $li_style == 4 || $li_style == 5 ) {
				$li_style_name = 'small';
			} elseif ( $li_style == 3 ) {
				$li_style_name = 'wide';
			} elseif ( $li_style == 6 ) {
				$li_style_name = 'full';
			}

			// if ( $i == 0 ) {
			// 	$video_sd_image .= " active";
			// }

			## Featured video top slider title length ##
			if ( strlen($bannerSlideShow[$i]->name) > 15 ) { // Displaying Video Title
				if ( $li_style == 1 || $li_style == 2 || $li_style == 4 || $li_style == 5 ) {
					$videoname = mb_substr($bannerSlideShow[$i]->name, 0, 15) . '..';
				} else {
					$videoname = mb_substr($bannerSlideShow[$i]->name, 0, 30) . '..';
				}
			} else {
				$videoname = $bannerSlideShow[$i]->name;
			}

			$image_path  = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
			$_imagePath  = APPTHA_VGALLERY_BASEURL . 'images' . DS;
			$thumb_image = $bannerSlideShow[$i]->image; ## Get thumb image
			$file_type   = $bannerSlideShow[$i]->file_type; ## Get file type of a video

			if ($thumb_image == '') { ## If there is no thumb image for video
				$thumb_image = $_imagePath . 'nothumbimage.jpg';
			} else {
				if ($file_type == 2 || $file_type == 5) {      ## For uploaded image
					$imageFea = $image_path . $thumb_image;
				} else {
					if ( strpos($imageFea,'sddefault') !== false ) {
						$imageFea = vh_imgresize($imageFea, 431, 326, $bannerSlideShow[$i]->slug);
					} elseif ( strpos($imageFea,'mqdefault') !== false ) {
						$imageFea = vh_imgresize($imageFea, 185, 122, $bannerSlideShow[$i]->slug);
					}
				}
			}

			if ( get_post_type() == 'videogallery' || get_post_type() === 'videogallery' ) {
				$thumb_href = 'href="' . $guid . '"';
			} else {
				$thumb_href = 'href="' . $guid . '"';
			}

			$output .= '<li class="item ' . $li_style_name . ' ' . $video_sd_image . '">
							<a ' . $thumb_href . ' class="switch_leading_featured_video" id="t-'. $bannerSlideShow[$i]->vid.'">
								<img src="' . $imageFea . '" class="'.$video_sd_image.'" alt="' . $bannerSlideShow[$i]->name . '" width="auto" height="auto" />
								<span class="v_img_info">
									' . $videoname . '
								</span>
							</a>
						</li>';

			if ( $li_style == 6 ) {
				$li_style = 0;
			}
			$li_style++;
		}
	return $output;
}
add_shortcode('featured-videos-slider', 'vh_featured_video_slider_func');

function vh_featured_video_slider_leading_video_func($attributes) {
		extract(shortcode_atts(array(
			'videos' => '18',
		), $attributes));

		global $wpdb, $dirPage;

		$site_url     = get_bloginfo('url');
		$bannervideos = "SELECT distinct w.*,p.playlist_name FROM " . $wpdb->prefix . "hdflvvideoshare w
			  INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play m ON m.media_id = w.vid
			  INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=m.playlist_id
			  WHERE featured='1' and publish='1' AND p.is_publish='1' GROUP BY w.vid ORDER BY w.ordering ASC LIMIT ". $videos;
		$bannerSlideShow = $wpdb->get_results($bannervideos);
		if( isset($dirPage) ) {
			$pluginName = $dirPage;
		}
		$output = '<script>
		window.onload = function(){
			var lt = false;
			vid = "leading-fragment-'. $bannerSlideShow[0]->vid.'";
			sourceCode = document.getElementById(vid).innerHTML;
			embedCode  = sourceCode.replace("embecontus","embed");
			embedCode  = embedCode.replace("iframcontus","iframe");
			embedCode  = embedCode.replace("videcontus","video");

			if(lt==true) {
				embedCode  = sourceCode.replace("EMBECONTUS","EMBED");
				embedCode  = embedCode.replace("IFRAMCONTUS","IFRAME");
				embedCode  = embedCode.replace("VIDECONTUS","IFRAME");
			}
			document.getElementById("leading_videoPlay").innerHTML = embedCode;
		}
		</script>';

$output .= '<div id="featured" class="wpb_row"> 
<div id="lofslidecontent45"	class="lof-slidecontent lof-snleft vc_col-sm-12">
	<div class="right_side">
		<div id="leading_videoPlay" class="ui-tabs-panel" style="height:100%"></div>
	</div>';
$bannertype = '';
for ($i = 0; $i < count($bannerSlideShow); $i++) {
	if( $bannertype == 'category' ) {
		$baseref = "&pid=".$playid;
	} else {
		$baseref = "";
	}
	
			$output   .= '<div id="leading-fragment-' . $bannerSlideShow[$i]->vid . '" class="ui-tabs-panel" style="height:100%;float:right">';
			$file_type = $bannerSlideShow[$i]->file_type;
			if ( $file_type == 5 ) {
				$bannerembedcode   = stripslashes($bannerSlideShow[$i]->embedcode);
				$banneriframecode  =  str_replace('<iframe', '<iframcontus', $bannerembedcode);
				$banneriframewidth =  str_replace('width=', 'width="100%"', $banneriframecode);
				$output           .= str_replace('height=', 'height="444"', $banneriframewidth);
			} else {
				$mobile = videostream_detectmobile();

				if( $mobile === true ) {
					$videourl   = $bannerSlideShow[$i]->file;
					$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
					$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
					$imgurl     = $bannerSlideShow[$i]->image;
					$file_type  = $bannerSlideShow[$i]->file_type;

					// If there is no thumb image for video
					if ($imgurl == '') {
						$imgurl = $_imagePath . 'nothumbimage.jpg';
					} else {
						if ($file_type == 2) { ## For uploaded image
							$imgurl = $image_path . $imgurl;
						}
					}

					if (preg_match('/www\.youtube\.com\/watch\?v=[^&]+/', $videourl, $vresult)) {
						$urlArray = explode("=", $vresult[0]);
						$videoid  = trim($urlArray[1]);

						$output .= '<iframcontus type="text/html" width="100%" height="600" src="http://www.youtube.com/embed/' . $videoid. '" frameborder="0">
									</iframcontus>';

					} else { 
						if ($file_type == 2) { ## For uploaded image
							$videourl = $image_path . $videourl;
						} else if ($file_type == 4) { ## For RTMP videos
							$streamer = str_replace("rtmp://", "http://", $bannerSlideShow[$i]->streamer_path);
							$videourl = $streamer . '_definst_/mp4:' . $videourl . '/playlist.m3u8';
						}
						
						$output .= '<videcontus id="video" width="100%" height="600" poster="'. $imgurl .'" src="'
						. $videourl .'" autobuffer controls>HTML5 Not support This video Format.</video>';
						
					}
				} else {
					$output .= '<embecontus
					src="'
					 . $site_url.'/wp-content/plugins/'.$pluginName.'/hdflvplayer/hdplayer.swf"
					flashvars="baserefW='
					 . $site_url. '&banner=true&mtype=playerModule&vid='
					 . $bannerSlideShow[$i]->vid. '&'
					 . $baseref. '&Preview='
					 . $bannerSlideShow[$i]->image. '"
					style="width:100%" allowFullScreen="true"
					allowScriptAccess="always" type="application/x-shockwave-flash"
					wmode="transparent"></embecontus>';
			}
		}
		$output .= '</div>';
		}
		$output .= '</div>';	

		$output .= '
			</div>';

		return $output;
}
add_shortcode('featured-videos-slider-leading-video', 'vh_featured_video_slider_leading_video_func');

// video module
function vh_video_module( $atts ) {
	extract( shortcode_atts( array(
				'playlist_id' => '',
				'video_title' => '',
				'video_count' => '',
				'video_style' => '',
				'exclude'     => ''
			), $atts ) );
			global $wpdb, $dirPage;

			$vh_swfPath  = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';
			$exclude_sql = '';

			if ( $exclude != null ) {
				$exclude_sql = " AND a.vid NOT IN (" . $exclude . ") ";
			}

			if ( $playlist_id == null ) {
				$playlist_id = 1;
			}

			if ( $video_count == null ) {
				$vh_video_count = '';
			} else {
				$vh_video_count = ' LIMIT ' . $video_count;
			}
			
			if ( $video_style == null ) {
				$vh_video_style = ' default';
			} elseif ( $video_style == 'video_thumb' ) {
				$vh_video_style = ' vid_thumbnail';
			} elseif ( $video_style == 'video_list' ) {
				$vh_video_style = ' vid_list';
			}

			$videoId         = 99999999;
			$pluginflashvars = "baserefW=" . get_option('siteurl');
			$width           = '100%';
			$height          = '444px';

			## Display videos starts here
			$select = "SELECT distinct(a.vid),b.playlist_id,name,guid,description,file,hdfile,file_type,duration,embedcode,image,opimage,download,link,featured,hitcount,slug,
							a.post_date,postrollads,prerollads FROM " . $wpdb->prefix . "hdflvvideoshare a
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play b ON a.vid=b.media_id
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=b.playlist_id
							INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=a.slug
							WHERE b.playlist_id=" . intval($playlist_id) . " AND a.vid != " . intval($videoId) . $exclude_sql . " and a.publish='1' AND p.is_publish='1'
							ORDER BY a.vid DESC".$vh_video_count;
			$output = '';
			$output .= '<div class="video_player'.$vh_video_style.'">';
			if ( $video_title != null ) {
				$sql = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare AS w
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play AS m ON m.media_id = w.vid
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist AS p on m.playlist_id = p.pid
							INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=w.slug
							WHERE w.publish='1' AND p.is_publish='1' AND m.playlist_id=" . intval($playlist_id) . " GROUP BY w.vid DESC";
				$playLists      = $wpdb->get_results($sql);
				$playlistCount  = count($playLists);

				global $wpdb;
				$moreName = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE \"%[video_category]%\" AND post_status=\"publish\" AND post_type=\"page\" limit 1");
				$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=' . $playlist_id;

				if ( is_user_logged_in() ) {
					$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
					if ( $user_followed_categories != '' ) {
						foreach ($user_followed_categories['followed_categories'] as $value) {
							if ( $value != $playlist_id ) {
								$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
							} else {
								$follow_button = '<a href="javascript:void(0)" class="unfollow-category icon-minus-circled">' . __('Unfollow', 'vh') . '</a>';
								break;
							}
						}
					} else {
						$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
					}
				} else {
					$follow_button = '<a href="'.wp_login_url().'" class="follow-category-register icon-plus-circled">' . __('Follow', 'vh') . '</a>';
				}

				$output .= '<h2 class="video-module-title">' . $video_title . '<span class="title-video-count"><a href="' . $more_playlist_link . '" class="video-category-count">' . $playlistCount . __(' videos', 'vh') . '</a></span>' . $follow_button . '<input type="hidden" value="' . $playlist_id . '"><div class="followed_categories"></div></h2>';
			}
			
			$wpdb->get_results( $select );
			$result = $wpdb->num_rows;

			if ($result != '') {
			## Slide Display Here
			$output .= '<ul class="video_module">';
			$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
				foreach ($wpdb->get_results($select) as $relFet) {
					$file_type = $relFet->file_type; ## Video Type
					$imageFea  = $relFet->image; ##VIDEO IMAGE
					$imageFea  = str_replace("/mq","/sd",$imageFea);
					$author_id = get_post_field( 'post_author', $relFet->slug );
					
					if ( $video_style != 'video_thumb' && $video_style != 'video_list' ) {
						$image_header = get_headers_curl($imageFea);
						if ( mb_substr($image_header, 9, 3) == '404' ) {
							$imageFea  = $relFet->image;
							$video_sd_image = 'mqdef';
						} else {
							$imageFea  = str_replace("/mq","/sd",$relFet->image);
							$video_sd_image = 'sdimg';
						}
					} else {
						$imageFea  = $relFet->image;
						$video_sd_image = 'mqdef';
					}
					$reafile = $relFet->file; ##VIDEO IMAGE
					$guid    = vh_get_video_permalink($relFet->slug); ##guid
					
					if ( $imageFea == '' ) {  ##If there is no thumb image for video
						$imageFea = APPTHA_VGALLERY_BASEURL . 'images' . DS . 'nothumbimage.jpg';
						$video_sd_image .= ' noimage';
					} else {
						if ( $file_type == 2 || $file_type == 5 ) {          ##For uploaded image
							$imageFea = $image_path . $imageFea;
						} else {
							if ( strpos($imageFea,'sddefault') !== false ) {
								$imageFea = vh_imgresize($imageFea, 463, 346, $relFet->slug);
							} elseif ( strpos($imageFea,'mqdefault') !== false ) {
								$imageFea = vh_imgresize($imageFea, 152, 98, $relFet->slug);
							}
						}
					}
				
					## Embed player code
					if($file_type == 5 && !empty($relFet->embedcode)){
						$relFetembedcode   = stripslashes($relFet->embedcode);
						$relFetiframewidth = preg_replace(array('/width="\d+"/i'),array(sprintf('width="%d"', $width)),$relFetembedcode);
						$player_values = preg_replace(array('/height="\d+"/i'),array(sprintf('height="%d"', $height)),$relFetiframewidth);
					 } else {
						 $mobile = vgallery_detect_mobile();
						if( $mobile === true ) {
							## Check for youtube video
							if (preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $reafile, $vresult)) {
								$urlArray    = explode("=", $vresult[0]);
								$video_id    = trim($urlArray[1]);
								$reavideourl = "http://www.youtube.com/embed/$video_id";

								## Generate youtube embed code for html5 player
								$player_values = '<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>';
							} else if ($file_type != 5) { ## Check for upload, URL and RTMP videos
								if ($file_type == 2) { ## For uploaded image
									$reavideourl = $image_path . $reafile;
								} else if ($file_type == 4) {           ## For RTMP videos
									$streamer    = str_replace("rtmp://", "http://", $media->streamer_path);
									$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
								} elseif ( strpos($reafile, 'soundcloud.com') !== false ) {
									$reavideourl = $reafile;
								}
								## Generate video code for html5 player
								$player_values = '<video id="video" poster="' . $imageFea . '"   src="' . $reavideourl .'" autobuffer controls preload="metadata">' . __( 'Html5 Not support This video Format.', 'vh' ) . '</video>';
							}
						} else {
							## Flash player code
							$player_values = '<embed src="' . $vh_swfPath . '" flashvars="' . $pluginflashvars . '&amp;mtype=playerModule&amp;vid='.$relFet->vid.'" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
						}
					}
					if (get_post_type() == 'videogallery' || get_post_type() === 'videogallery') {
						$thumb_href = 'href="'. $guid.'"';
					} else {
						$player_div = 'mediaspace';
						$videodivId = rand();
						if (isset($arguments['id'])) {
							$videodivId .= $arguments['id']; ## get video id from short code
							$vid = $arguments['id'];
						}
						$thumb_href = 'href="'.$guid.'"';
					}
					if (strlen($relFet->name) > 25) { ## Displaying Video Title
						$t_videoname = mb_substr($relFet->name, 0, 25) . '..';
					} else {
						$t_videoname = $relFet->name;
					}
					$output .='<li><div class="video_container"><div class="imgSidethumb">';
					if ( $video_style == 'video_thumb' ) {
						$output .= '<div class="video_thumb_info">'.$t_videoname.'</div>
										<div class="video_thumb_description">
											<div class="video_info">
											<p>' . mb_substr($relFet->description, 0, 90) . '..</p>';


						$output .= '<div class="video_views icon-eye">'. $relFet->hitcount . '</div>';
						$tc      = wp_count_comments($relFet->slug);
						$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';

						if ( function_exists('get_post_ul_meta') ) {
							$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($relFet->slug, "like") . '</div>'; 
						}

						$output .= '</div>
						</div>';
					} elseif ( $video_style == '' ) {
						$output .= '<div class="video_info">';
									if ($relFet->duration != 0.00) {
										$output .= '<div class="video-duration micon-clock">' . $relFet->duration . '</div>';
									}
								$output .= '<div class="video_views icon-eye">'. $relFet->hitcount . '</div>';
						$tc      = wp_count_comments($relFet->slug);
						$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
						if ( function_exists('get_post_ul_meta') ) {
							$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($relFet->slug, "like") . '</div>'; 
						}
						$output .= '</div>';
					} elseif ( $video_style == 'video_list' ) {
						//$output .= '<div class="video-duration micon-clock">' . $relFet->duration . '</div>';
					}
					## VIDEO MODULE IFRAME ##
					$output .= '<div class="video_image_container '.$video_sd_image.'"><div class="video_hidden_wrapper">
									<a href="javascript:void(0);" class="video_play"></a>
									<a ' . $thumb_href . ' class="view_more"></a>
									<img src="' . $imageFea . '" alt="' . $relFet->name . '" class="related '.$video_sd_image.'" />
									<div id="video_dialog" title="' . $relFet->name . '">';
									if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
										$video_link = $relFet->file;
										if ( strpos($video_link,'soundcloud.com') !== false ) {
											$output .= '<input type="hidden" class="iframe_url" value="' . $video_link . '" />';
											$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
										} else {
											$output .= $player_values;
										}
									} else {
										if( strpos($relFet->file, 'v=') !== false ) {
											$video_link = explode('v=', $relFet->file);
											if ( strpos($relFet->link, 'rel=') !== false ) {
												$video_rel = explode('?', $relFet->link);
												$output .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '?' . $video_rel[1] . '" />';
											} else {
												$output .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
											}
											$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
										} elseif ( strpos($relFet->file, '/v/') !== false ) {
											$video_link = explode('/v/', $relFet->file);
											$output .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
											$output .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
										} elseif ( strpos($relFet->file, '/video/') !== false ) {
											$video_link = explode('/video/', $relFet->file);
											$video_link = explode('_', $video_link[1]);
											$output .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
											$output .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
										} else {

											if ( $file_type == 3 ) {
												$videourl   = $relFet->file;
												$image_file = $relFet->image;
											} elseif ( $file_type == 2 ) {
												$videourl   = $image_path . $relFet->file;
												$image_file = $image_path . $relFet->image;

												$output .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
											} else {
												$videourl   = $image_path . $relFet->file;
												$image_file = $image_path . $relFet->image;
											}

											if( $file_type == 5 && !empty($relFet->embedcode) ) {
												$output .= stripslashes($relFet->embedcode);
											 }

											// $output .= do_shortcode('[video poster="' . $image_file . '" width="640" height="409" src="' . $videourl . '"]');
										}
									}
									$output .= '</div></div>
								</div>
							</div>';
					$output .='<div class="vid_info"><span><a ' . $thumb_href . ' class="videoHname">';
					## Video module title length ##
					if ( strlen($relFet->name) > 30 ) { ## Displaying Video Title
						$videoname = mb_substr($relFet->name, 0, 30) . '..';
					} else {
						$videoname = $relFet->name;
					}
					$output .= $videoname;
					$output .='</a></span>';
					if ( $video_style == 'video_list' ) {
						$output .= '<div class="video_info">';
									if ($relFet->duration != 0.00) {
										$output .= '<div class="video-duration micon-clock">' . $relFet->duration . '</div>';
									}
								$output .= '<div class="video_views icon-eye">'. $relFet->hitcount . '</div>';
						$tc      = wp_count_comments($relFet->slug);
						$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
						if ( function_exists('get_post_ul_meta') ) {
							$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($relFet->slug, "like") . '</div>';
						} 
						$output .= '</div><div class="clearfix"></div>';
					}
					$description_length = 170;
					if ( $vh_video_style == ' vid_list' ) {
						$description_length = 70; 
					} elseif ( $vh_video_style == ' default' ) {
						$description_length = 170;
					} else {
						$description_length = 170;
					}

					if (strlen($relFet->description) > $description_length) { ## Displaying Video Description
						$videodescription = mb_substr($relFet->description, 0, $description_length) . '..';
					} else {
						$videodescription = $relFet->description;
					}
					$output .= '<div class="video_desc">' . $videodescription .  '</div>';
					if ( $video_style == 'video_list' || $video_style == NULL ) {
						$output .= '<div class="video_author icon-user">' . get_the_author_meta('display_name', $author_id) .  '</div>';
						$output .= '<div class="video_date icon-calendar">' . human_time_diff(get_the_time('U',$relFet->slug),current_time('timestamp')) .  ' ago</div>';
					}
					$output .='</div></div></li>';
				}

				if ( $video_style == 'video_thumb' ) {
					global $wpdb;
					$moreName = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE \"%[video_category]%\" AND post_status=\"publish\" AND post_type=\"page\" limit 1");
					$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=' . $playlist_id;

					$sql = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare AS w
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play AS m ON m.media_id = w.vid
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist AS p on m.playlist_id = p.pid
							INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=w.slug
							WHERE w.publish='1' AND p.is_publish='1' AND m.playlist_id=" . intval($playlist_id) . " GROUP BY w.vid DESC";
					$playLists = $wpdb->get_results($sql);
					if ( $video_count != null ) {
						$playlistCount  = count($playLists);
					} else {
						$playlistCount  = 0;
					}

					if ( $playlistCount > 0 ) {
						$output .= '<li><a href="' . $more_playlist_link . '" class="video_thumb_link"><span class="video_thumb_more">+</span>' . $playlistCount . __(' more', 'vh') . '</a></li>';
					}
				}
				
				$output .= '</ul>';
			}  
			$output .= '</div>';
			

	return $output;
}
add_shortcode('video-module', 'vh_video_module');

// video carousel
function vh_video_carousel( $atts ) {
	extract( shortcode_atts( array(
				'playlist_c_id'    => '',
				'video_c_title'    => '',
				'video_c_count'    => '',
				'video_c_style'    => '',
				'video_c_autoplay' => 'false',
				'video_c_speed'    => '2000',
				'rows'             => '1'
			), $atts ) );
			global $wpdb, $dirPage;
			$vh_swfPath = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';

			if ( $playlist_c_id == null ) {
				$playlist_c_id = 1;
			}

			if ($video_c_count>12) {
				$video_c_count=12;
			}

			if ( $video_c_count == null ) {
				$vh_video_c_count = '12';
				$vh_video_c_count = ' LIMIT ' . $vh_video_c_count;
			} else {
				$vh_video_c_count = ' LIMIT ' . $video_c_count;
			}
			
			if ( $video_c_style == null ) {
				$vh_video_style = 'default';
			} elseif ( $video_c_style == 'video_list' ) {
				$vh_video_style = 'vid_list';
			}

			$random          = rand();
			$videoId         = 99999999;
			$pluginflashvars = "baserefW=" . get_option('siteurl');
			$width           = '100%';
			$height          = '444px';
			$output          = '';

			if ( empty($playlist_id) ) {
				$playlist_id = '';
			}

			## Display videos starts here
			$select = "SELECT distinct(a.vid),b.playlist_id,name,guid,description,file,hdfile,file_type,duration,embedcode,image,opimage,download,link,featured,hitcount,slug,
						a.post_date,postrollads,prerollads FROM " . $wpdb->prefix . "hdflvvideoshare a
						INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play b ON a.vid=b.media_id
						INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=b.playlist_id
						INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=a.slug
						WHERE b.playlist_id=" . intval($playlist_c_id) . " AND a.vid != " . intval($videoId) . " and a.publish='1' AND p.is_publish='1'
						ORDER BY a.vid DESC" . $vh_video_c_count;

			if ( $video_c_autoplay == 'true' ) {
				if ( $rows >= 2 ) {
					$video_c_autoplay_output = 'interval: 2000,
									 target: "+=1",
									 autostart: true';
				} else {
					$video_c_autoplay_output = 'interval: 2000,
									 target: "+=2",
									 autostart: true';
				}
				
			} elseif ( $video_c_autoplay == 'false' ) {
				$video_c_autoplay_output = 'autostart: false';
			} else {
				if ( $rows >= 2 ) {
					$video_c_autoplay_output = 'autostart: true,
									 target: "+=1",
									 interval: ' . $video_c_autoplay;
				} else {
					$video_c_autoplay_output = 'autostart: true,
									 target: "+=2",
									 interval: ' . $video_c_autoplay;
				}
			}

			if ($video_c_speed == null) {
				$video_c_speed = '2000';
			} else {
				$video_c_speed =  $video_c_speed;
			}

			if ( $video_c_title != null ) {
				$sql = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare AS w
						INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play AS m ON m.media_id = w.vid
						INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist AS p on m.playlist_id = p.pid
						INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=w.slug
						WHERE w.publish='1' AND p.is_publish='1' AND m.playlist_id=" . intval($playlist_c_id) . " GROUP BY w.vid DESC";
				$playLists      = $wpdb->get_results($sql);
				$playlistCount  = count($playLists);

				global $wpdb;
				$moreName = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE \"%[video_category]%\" AND post_status=\"publish\" AND post_type=\"page\" limit 1");
				$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=' . $playlist_c_id;

				if ( is_user_logged_in() ) {
					$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
					if ( $user_followed_categories != '' ) {
						foreach ($user_followed_categories['followed_categories'] as $value) {
							if ( $value != $playlist_c_id ) {
								$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
							} else {
								$follow_button = '<a href="javascript:void(0)" class="unfollow-category icon-minus-circled">' . __('Unfollow', 'vh') . '</a>';
								break;
							}
						}
					} else {
						$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
					}
				} else {
					$follow_button = '<a href="'.wp_login_url().'" class="follow-category-register icon-plus-circled">' . __('Follow', 'vh') . '</a>';
				}

				$output .= '<h2 class="video-carousel-title carousel">' . $video_c_title . '<span class="title-video-count"><a href="' . $more_playlist_link . '" class="video-category-count">' . $playlistCount . __(' videos', 'vh') . '</a></span>' . $follow_button . '<input type="hidden" value="' . $playlist_c_id . '"><div class="followed_categories"></div></h2>';
			}
			
			$wpdb->get_results( $select );
			$result = $wpdb->num_rows;
			if ( $result != '' ) {
				$output .= '
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								[].slice.call( document.querySelectorAll( ".dotstyle.number_' . $random . ' > ul" ) ).forEach( function( nav ) {
									new DotNav( nav, {
										callback : function( idx ) {
										}
									});
								});
								jQuery(".imageSliderExt.number_' . $random . ' ul li").eq(0).addClass("current");

								var carousel_' . $random . ' = jQuery(".video_c_player.' . $vh_video_style . '.carousel_id_' . $random . '").on("jcarousel:scroll", function(event, carousel) {
									// jQuery(".video_c_player.' . $vh_video_style . '.carousel_id_' . $random . '").parent().css("visibility", "hidden");
									jQuery(".video_c_player.' . $vh_video_style . '.carousel_id_' . $random . '").parent().hide().fadeIn(700);

								}).on("jcarousel:create jcarousel:reload", function() {
									var element = $(this),
									width = element.innerWidth();';

									if ( $rows >= 2 ) {
										$output .= '
											if (width > 950) {
												width = width;
											} else if (width < 435) {
												width = width - 10;
											} else {
												width = width;
											}
											element.jcarousel("items").css("width", width + "px");

											if (width > 950) {
												width2 = (width / 2) - 15;
											} else if (width < 435) {
												width2 = width;
											} else {
												width2 = (width / 2) - 15;
											}
											element.jcarousel("items").find("li").css("width", width2 + "px");
											';
									} else {
										$output .= '
											if (width > 950) {
												width = (width / 2) - 15;
											
											} else if (width < 435) {
												width = width - 10;
											} else {
												width = (width / 2) - 15;
											}
											element.jcarousel("items").css("width", width + "px");';
									}

				$output .= '
									
								}).jcarousel({
									wrap: "circular",
									animation: {
										duration: 0
									}

								}).jcarouselAutoscroll({
									' . $video_c_autoplay_output . '
								});';

								if ( $video_c_autoplay != 'false' ) {
									$output .= '
										jQuery(".video_c_player.' . $vh_video_style . '.carousel_id_' . $random . ', .imageSliderExt.number_' . $random . '").hover(function() {
											jQuery(".video_c_player.' . $vh_video_style . '.carousel_id_' . $random . '").jcarouselAutoscroll("stop");
										}, function() {
											jQuery(".video_c_player.' . $vh_video_style . '.carousel_id_' . $random . '").jcarouselAutoscroll("start");
										});';
								}

								$output .= '
								jQuery(".imageSliderExt.number_' . $random . ' ul").on("jcarouselpagination:active", "li", function() {
									jQuery(this).addClass("current");
								}).on("jcarouselpagination:inactive", "li", function() {
									jQuery(this).removeClass("current");
								}).jcarouselPagination({
									carousel: carousel_' . $random . ',';

								if ( $rows >= 2 ) {
									$output .= '
									"perPage": 1,';
								} else {
									$output .= '
										
									"perPage": 2,';
								}

								$output .= '
									"item": function(page, carouselItems) {
										return \'<li><a href="#\' + page + \'"></a></li>\';
									}
								});

								jQuery(".imageSliderExt.number_' . $random . ' ul").append("<li><!-- dummy dot --></li>");
								
								jQuery(window).bind("debouncedresize", function() {
									jQuery(".video_c_player.' . $vh_video_style . '.carousel_id_' . $random . '").jcarousel("reload");
									jQuery(".imageSliderExt.number_' . $random . ' ul").append("<li><!-- dummy dot --></li>");

									jQuery(".dotstyle.number_' . $random . ' li").removeClass("current");
									jQuery(".dotstyle.number_' . $random . ' li:first-child").addClass("current");
								});
								jQuery(document).on("click", ".vc_tta-tab, .vc_tta-panel", function() {
									carousel_' . $random . '.jcarousel("reload");
								});
							});
						</script>';
			}

			$output .= '<div class="imageSliderExt dotstyle number_' . $random . ' dotstyle-dotmove"><ul></ul></div>
						<div class="overflow_hidden">
							<div class="video_c_player jcarousel c_rows_' . $rows . ' ' . $vh_video_style . ' carousel_id_' . $random . '">';

			if ($result != '') {
			## Slide Display Here
			
				$output    .= '<ul class="video_module carousel">';
				$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
				$i          = 0;

				foreach ( $wpdb->get_results($select) as $relFet ) {
					$file_type = $relFet->file_type; ## Video Type
					$imageFea  = $relFet->image; ##VIDEO IMAGE
					$imageFea  = str_replace("/mq","/sd",$imageFea);
					$author_id = get_post_field( 'post_author', $relFet->slug );

					if ( $video_c_style != 'video_thumb' && $video_c_style != 'video_list' ) {
						$image_header = get_headers_curl($imageFea);
						if ( mb_substr($image_header, 9, 3) == '404' ) {
							$imageFea  = $relFet->image;
							$video_sd_image = 'mqdef';
						} else {
							$imageFea  = str_replace("/mq","/sd",$relFet->image);
							$video_sd_image = 'sdimg';
						}
					} else {
						$imageFea  = $relFet->image;
						$video_sd_image = 'mqdef';
					}
					$reafile = $relFet->file; ##VIDEO IMAGE
					$guid = vh_get_video_permalink($relFet->slug); ##guid
					
					if ($imageFea == '') {  ##If there is no thumb image for video
						$imageFea = APPTHA_VGALLERY_BASEURL . 'images' . DS . 'nothumbimage.jpg';
						$video_sd_image .= ' noimage';
					} else {
						if ($file_type == 2 || $file_type == 5 ) {          ##For uploaded image
							$imageFea = $image_path . $imageFea;
						} else {
							if ( strpos($imageFea,'sddefault') !== false ) {
								$imageFea = vh_imgresize($imageFea, 462, 346, $relFet->slug);
							} elseif ( strpos($imageFea,'mqdefault') !== false ) {
								$imageFea = vh_imgresize($imageFea, 152, 98, $relFet->slug);
							}
						}
					}
					## Embed player code
					if($file_type == 5 && !empty($relFet->embedcode)){
						$relFetembedcode   = stripslashes($relFet->embedcode);
						$relFetiframewidth = preg_replace(array('/width="\d+"/i'),array(sprintf('width="%d"', $width)),$relFetembedcode);
						$player_values = preg_replace(array('/height="\d+"/i'),array(sprintf('height="%d"', $height)),$relFetiframewidth);
					 } else {
						 $mobile = vgallery_detect_mobile();
						if( $mobile === true ) {
							## Check for youtube video
							if ( preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $reafile, $vresult) ) {
								$urlArray    = explode("=", $vresult[0]);
								$video_id    = trim($urlArray[1]);
								$reavideourl = "http://www.youtube.com/embed/$video_id";
								## Generate youtube embed code for html5 player
								$player_values = '<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>';
							} else if ( $file_type != 5 ) { ## Check for upload, URL and RTMP videos
								if ( $file_type == 2 ) { ## For uploaded image
									$reavideourl = $image_path . $reafile;
								} else if ( $file_type == 4 ) {           ## For RTMP videos
									$streamer    = str_replace("rtmp://", "http://", $media->streamer_path);
									$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
								} elseif ( strpos($reafile, 'soundcloud.com') !== false ) {
									$reavideourl = $reafile;
								}
								## Generate video code for html5 player
								$player_values = '<video id="video" poster="' . $imageFea . '"   src="' . $reavideourl .'" autobuffer controls preload="metadata">' . __( 'Html5 Not support This video Format.', 'vh' ) . '</video>';
							}
						} else {
							## Flash player code
							$player_values = '<embed src="' . $vh_swfPath . '" flashvars="' . $pluginflashvars . '&amp;mtype=playerModule&amp;vid='.$relFet->vid.'" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
						}
					}
					if (get_post_type() == 'videogallery' || get_post_type() === 'videogallery') {
						$thumb_href = 'href="'. $guid.'"';
					} else {
						$player_div = 'mediaspace';
						$videodivId = rand();

						if (isset($arguments['id'])) {
							$videodivId .= $arguments['id']; ## get video id from short code
							$vid = $arguments['id'];
						}
						$thumb_href = 'href="'.$guid.'"';
					}
					if (strlen($relFet->name) > 25) { ## Displaying Video Title
						$t_videoname = mb_substr($relFet->name, 0, 25) . '..';
					} else {
						$t_videoname = $relFet->name;
					}

					if ( ($rows == 2 && ($i % 4 == 0 || $i == 0) ) || ($rows == 3 && ($i % 6 == 0 || $i == 0)) ) {
						if ( $i != 0 ) {
							$output .= '</div><!-- end of carousel_row-->';
						}
						$output .= '<div class="carousel_row">';
					}

					$output .='<li><div class="video_container"><div class="imgSidethumb">';
					if ( $video_c_style == 'video_thumb' ) {
						$output .= '<div class="video_thumb_info">'.$t_videoname.'</div>';
					} elseif ( $video_c_style == '' ) {
						$output .= '<div class="video_info">';
								if ($relFet->duration != 0.00) {
									$output .= '<div class="video-duration micon-clock">' . $relFet->duration . '</div>';
								}
								$output .= '<div class="video_views icon-eye">'. $relFet->hitcount . '</div>';
								$tc = wp_count_comments($relFet->slug);
								$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
								if ( function_exists('get_post_ul_meta') ) {
									$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($relFet->slug, "like") . '</div>'; 
								}
						$output .= '</div>';
					}
					## VIDEO CAROUSEL IFRAME ##
					$output .= '<div class="video_image_container '.$video_sd_image.'"><div class="video_hidden_wrapper">
									<a href="javascript:void(0);" class="video_play"></a>
									<a ' . $thumb_href . ' class="view_more"></a>
									<img src="' . $imageFea . '" alt="' . $relFet->name . '" class="related" />
									<div id="video_dialog" title="' . $relFet->name . '">';
									if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
										$video_link = $relFet->file;
										if ( strpos($video_link,'soundcloud.com') !== false ) {
											$output .= '<input type="hidden" class="iframe_url" value="' . $video_link . '" />';
											$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
										} else {
											$output .= $player_values;
										}
									} else {
										if( strpos($relFet->file, 'v=') !== false ) {
											$video_link = explode('v=', $relFet->file);
											if ( strpos($relFet->link, 'rel=') !== false ) {
												$video_rel = explode('?', $relFet->link);
												$output .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '?' . $video_rel[1] . '" />';
											} else {
												$output .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
											}
											$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
										} elseif ( strpos($relFet->file, '/v/') !== false ) {
											$video_link = explode('/v/', $relFet->file);
											$output .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
											$output .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
										} elseif ( strpos($relFet->file, '/video/') !== false ) {
											$video_link = explode('/video/', $relFet->file);
											$video_link = explode('_', $video_link[1]);
											$output .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
											$output .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
										} else {

											if ( $file_type == 3 ) {
												$videourl   = $reafile;
												$image_file = $imageFea;
											} elseif ( $file_type == 2 ) {
												$videourl   = $image_path . $reafile;

												$output .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
											} else {
												$videourl   = $image_path . $reafile;
												$image_file = $imageFea;
											}

											if( $file_type == 5 && !empty($relFet->embedcode) ) {
												$output .= stripslashes($relFet->embedcode);
											 }

											// $output .= do_shortcode('[video poster="' . $image_file . '" width="642" height="444" src="' . $videourl . '"]');
										}
									}
									$output .= '</div></div>
								</div>
							</div>';
					$output .='<div class="vid_info"><span><a ' . $thumb_href . ' class="videoHname">';
					## Video carousel module title length ##
					if (strlen($relFet->name) > 30) { ## Displaying Video Title
								$videoname = mb_substr($relFet->name, 0, 30) . '..';
							}
							else {
								$videoname = $relFet->name;
					}
					$output .= $videoname;
					$output .='</a></span>';
					if ( $video_c_style == 'video_list' ) {
						$output .= '<div class="video_info">';
						if ($relFet->duration != 0.00) {
							$output .= '<div class="video-duration micon-clock">' . $relFet->duration . '</div>';
						}
						$output .= '<div class="video_views icon-eye">'. $relFet->hitcount . '</div>';
						$tc     = wp_count_comments($relFet->slug);
						$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
						if ( function_exists('get_post_ul_meta') ) {
							$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($relFet->slug, "like") . '</div>';
						} 
						$output .= '</div><div class="clearfix"></div>';
					}
					$description_length = 210;
					if ( $vh_video_style == 'vid_list' ) {
						$description_length = 70;
					} else {
						$description_length = 210;
					}
					if (strlen($relFet->description) > $description_length) { ## Displaying Video Description
						$videodescription = mb_substr($relFet->description, 0, $description_length) . '..';
					} else {
						$videodescription = $relFet->description;
					}
					$output .= '<div class="video_desc">' . esc_html( $videodescription ) .  '</div>';
					if ( $video_c_style == 'video_list' || $video_c_style == NULL ) {
						$output .= '<div class="video_author icon-user">' . get_the_author_meta('display_name', $author_id) .  '</div>';
						$output .= '<div class="video_date icon-calendar">' . human_time_diff(get_the_time('U',$relFet->slug),current_time('timestamp')) .  ' ago</div>';
					}
					$output .='</div></div></li><!-- end of li-' . $i . '-->';

					if ( $rows >= 2 && ($i + 1) == $result ) {
						$output .= '</div><!-- end of carousel_row-->';
					}
					$i++;
				}

				if ( $video_c_style == 'video_list' ) {
					$carousel_button = ' vid_list';
				}
			}  
				$output .= '</ul></div></div>';
			

	return $output;
}
add_shortcode('video-carousel', 'vh_video_carousel');


if( class_exists('ContusMoreController') ) {
class ContusMoreViewEdited extends ContusMoreController { //CLASS FOR HOME PAGE STARTS

	public $_settingsData;
	public $_vId;
	public $_playid;
	public $_pagenum;

		public function __construct() {                                             ## contructor starts
			parent::__construct ();
			global $wp_query;
			$video_search = '';
			$this->_settingsData = getPluginSettings (); // Get player settings
			$this->_mPageid = morePageID (); // Get more page id
			$this->_vId = absint( filter_input ( INPUT_GET, 'vid' ) ); // Get vid from URL
			$this->_pagenum = absint ( get_query_var('paged') ); // Get current page number
			$this->_playid =  &$wp_query->query_vars ['playid'] ;
			$this->_userid = &$wp_query->query_vars ['userid'] ;
			
			// Get pid from URL
			$this->_viewslang = __ ( 'Views', 'video_gallery' );
			$this->_viewlang = __ ( 'View', 'video_gallery' );
			// Get search keyword
			$searchVal = str_replace ( ' ', '%20', __ ( 'Video Search ...', 'video_gallery' ) );
			if (isset ( $wp_query->query_vars ['video_search'] ) && $wp_query->query_vars ['video_search'] !== $searchVal) {
				$video_search = $wp_query->query_vars ['video_search'];
			}
			$this->_video_search = stripslashes ( urldecode ( $video_search ) );
			
			$this->_showF = 5;
			$this->_colF = $this->_settingsData->colMore; // get row of more page
			$this->_colCat = $this->_settingsData->colCat; // get column of more page
			$this->_rowCat = $this->_settingsData->rowCat; // get row of category videos
			$this->_perCat = $this->_colCat * $this->_rowCat; // get column of category videos
			$dir = dirname ( plugin_basename ( __FILE__ ) );
			$dirExp = explode ( '/', $dir );
			$this->_folder = $dirExp [0]; // Get plugin folder name
			$this->_site_url = get_site_url (); // Get base url
			$this->_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS; // Declare image path
		} //contructor ends

		function get_more_pageid() {//function for getting more page ID starts
			$moreName = $this->_wpdb->get_var("select ID from " . $this->_wpdb->prefix . "posts WHERE post_content LIKE '%[video_category]%' and post_status='publish' and post_type='page' limit 1");
			return $moreName;
		}
		// More page
		function video_more_pages($type) {                                          ## More PAGE FEATURED VIDEOS STARTS
			if (function_exists('homeVideo') != true) {
			$type_name   = '';
			$playlist_id = '';
			if ( empty($_GET['more']) ) {
				$_GET['more'] = '';
			}
			if ( !empty($_GET['followed']) && $_GET['followed'] == 'true' ) {
				$type = 'followed';
			}
				switch ($type) {
					case 'popular' : 
						$rowF = $this->_settingsData->rowMore; // row field of popular videos
						$colF = $this->_settingsData->colMore; // column field of popular videos
						$dataLimit = $rowF * $colF;
						$where = '';
						$thumImageorder = 'w.hitcount DESC';
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						$TypeOFvideos = $this->home_thumbdata ( $thumImageorder, $where, $pagenum, $dataLimit );
						$CountOFVideos = $this->countof_videos ( '', '', $thumImageorder, $where );
						$typename = __ ( 'Popular', 'video_gallery' );
						$type_name = 'popular';
						$morePage = '&more=pop';
						break; 
					
					case 'recent' :
						$rowF = $this->_settingsData->rowMore;
						$where = '';
						$colF = $this->_settingsData->colMore;
						$dataLimit = $rowF * $colF;
						$thumImageorder = 'w.vid DESC';
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						$TypeOFvideos = $this->home_thumbdata ( $thumImageorder, $where, $pagenum, $dataLimit );
						$CountOFVideos = $this->countof_videos ( '', '', $thumImageorder, $where );
						$typename = __ ( 'Recent', 'video_gallery' );
						$type_name = 'recent';
						$morePage = '&more=rec';
						break;
					case 'random':
						$rowF = $this->_settingsData->rowMore;
						$where = '';
						$colF = $this->_settingsData->colMore;
						$dataLimit = $rowF * $colF;
						$thumImageorder = 'w.vid DESC';
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						$TypeOFvideos = $this->home_thumbdata ( $thumImageorder, $where, $pagenum, $dataLimit );
						$CountOFVideos = $this->countof_videos ( '', '', $thumImageorder, $where );
						$typename = __ ( 'Random', 'video_gallery' );
						$type_name = 'random';
						$morePage = '&more=rand';
						break;
					case 'featured' :
						$thumImageorder = 'w.ordering ASC';
						$where = 'AND w.featured=1';
						$rowF = $this->_settingsData->rowMore;
						$colF = $this->_settingsData->colMore;
						$dataLimit = $rowF * $colF;
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						$player_color = unserialize( $this->_settingsData->player_colors);
						$recent_video_order = $player_color['recentvideo_order'];	
						if ($recent_video_order == 'id') {
							$thumImageorder = 'w.vid DESC';
						} elseif ($recent_video_order == 'hitcount') {
							$thumImageorder = 'w.' . $recent_video_order . ' DESC';
						} elseif ($recent_video_order == 'default') {
							$thumImageorder = 'w.ordering ASC';
						}  else {
							$thumImageorder = 'w.vid DESC';
						}
						$TypeOFvideos = $this->home_thumbdata ( $thumImageorder, $where, $pagenum, $dataLimit );
						$CountOFVideos = $this->countof_videos ( '', '', $thumImageorder, $where );
						$typename = __ ( 'Featured', 'video_gallery' );
						$type_name = 'featured';
						$morePage = '&more=fea';
						break;
					case 'cat' :
						$thumImageorder = absint( $this->_playid );
						$where = '';
						$rowF = $this->_settingsData->rowCat;
						$colF = $this->_settingsData->colCat;
						$dataLimit = $rowF * $colF;
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						$player_color = unserialize( $this->_settingsData->player_colors);
						$recent_video_order = $player_color['recentvideo_order'];
						if ($recent_video_order == 'id') {
							$default_order = 'w.vid DESC';
						} elseif ($recent_video_order == 'hitcount') {
							$default_order = 'w.' . $recent_video_order . ' DESC';
						} elseif ($recent_video_order == 'default') {
							$default_order = 'w.ordering ASC';
						}  else {
							$default_order = 'w.vid DESC';
						}
						$TypeOFvideos = $this->home_catthumbdata ( $thumImageorder, $pagenum, $dataLimit ,$default_order );
						$CountOFVideos = $this->countof_videos ( absint( $this->_playid ), '', $thumImageorder, $where );
						$typename = __ ( 'Category', 'video_gallery' );
						$morePage = '&playid=' . $thumImageorder;
						break;
					case 'user' :
						$thumImageorder = $this->_userid;
						$where = '';
						$rowF = $this->_settingsData->rowCat;
						$colF = $this->_settingsData->colCat;
						$dataLimit = $rowF * $colF;
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						$TypeOFvideos = $this->home_userthumbdata ( $thumImageorder, $pagenum, $dataLimit );
						$CountOFVideos = $this->countof_videos ( '', $this->_userid, $thumImageorder, $where );
						$typename = __ ( 'User', 'video_gallery' );
						$morePage = '&userid=' . $thumImageorder;
						break;
					case 'search' :
						$video_search = str_replace ( '%20', ' ', $this->_video_search );
						if ($this->_video_search == __ ( 'Video Search ...', 'video_gallery' )) {
							$video_search = '';
						}
						$thumImageorder = $video_search;
						$rowF = $this->_settingsData->rowMore;
						$colF = $this->_settingsData->colMore;
						$dataLimit = $rowF * $colF;
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						$TypeOFvideos = $this->home_searchthumbdata ( $thumImageorder, $pagenum, $dataLimit );
						$CountOFVideos = $this->countof_videosearch ( $thumImageorder );
						return $this->searchlist ( $video_search, $CountOFVideos, $TypeOFvideos, $this->_pagenum, $dataLimit );
						break;
					case 'followed' :
						$rowF = $this->_settingsData->rowCat; // category setting row value
						$colF = $this->_settingsData->colCat; // category setting column value
						$dataLimit = $rowF * $colF;
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						// $TypeOFvideos = $this->home_categoriesthumbdata ( $pagenum, $dataLimit );
						$CountOFVideos = $this->get_countof_videocategories ();
						$typename = __ ( 'Followed', 'video_gallery' );
						break;
					case 'categories' :
					default :
						$rowF = $this->_settingsData->rowCat; // category setting row value
						$colF = $this->_settingsData->colCat; // category setting column value
						$dataLimit = $rowF * $colF;
						$pagenum =  $this->_pagenum;
						if( empty($pagenum ) ) {
							$pagenum = 1;
						}
						$TypeOFvideos = $this->home_categoriesthumbdata ( $pagenum, $dataLimit );
						$CountOFVideos = $this->get_countof_videocategories ();
						$typename = __ ( 'Video Categories', 'video_gallery' );
						return $this->categorylist ( $CountOFVideos, $TypeOFvideos, $this->_pagenum, $dataLimit );
						break;
				}

				$class = $div = '';
				$ratearray = array("nopos1", "onepos1", "twopos1", "threepos1", "fourpos1", "fivepos1");
?>

<?php
				$pagenum = isset($this->_pagenum) ? absint($this->_pagenum) : 1;
				$div     = '<div class="video_wrapper" id="'. $type_name.'_video">';
				$div    .= '<style type="text/css"> .video-block {  margin-left:' . $this->_settingsData->gutterspace . 'px !important; } </style>';
				if ( $_GET['more'] == 'popular' ) {
					$div .= "<script type=\"text/javascript\">
								jQuery.cookie('vh_category_sorting', 'views', { path: '/' });
							</script>";
				} elseif ( $_GET['more'] == 'recent' ) {
					$div .= "<script type=\"text/javascript\">
								jQuery.cookie('vh_category_sorting', 'date', { path: '/' });
							</script>";
				}
				
					$div    .= '<div class="sorting-container">';
					$div    .= '	<div id="cat_layout" class="category_layout_container">
									<div class="category_layout">'.__('Layout:', 'vh').'</div>
									<div class="layout table icon-th clicked"></div>
									<div class="layout list icon-th-list"></div>
								</div>';
					if ( empty($_GET['followed']) ) {
					$div    .= '<div id="sorts" class="category_sort_container">
									<div class="sort_by">'.__('Sort by:', 'vh').'</div>
									<div class="sort_param date clicked" data-sort-value="date">'.__('Date', 'vh').'</div>
									<div class="sort_param title" data-sort-value="title">'.__('Title', 'vh').'</div>
									<div class="sort_param views" data-sort-value="views">'.__('Views', 'vh').'</div>';
									if ( function_exists('ldc_like_counter_p') ) {
										$div .= '<div class="sort_param likes" data-sort-value="likes">'.__('Likes', 'vh').'</div>';
									}
								$div    .= '</div>';
					}
					$div    .= '</div>';
				
				
					$div .= '<div class="video-block-container-wrapper">';

					if( $typename == 'Category' || $typename == 'Followed' ) {
						global $wpdb;

						if ( empty($_GET['followed']) || $_GET['followed'] != 'true' ) {
							$playlist_name = get_playlist_name(intval($this->_playid));
							$sql = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare AS w
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play AS m ON m.media_id = w.vid
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist AS p on m.playlist_id = p.pid
								INNER JOIN " . $this->_wpdb->prefix . "posts s ON s.ID=w.slug
								WHERE w.publish='1' AND p.is_publish='1' AND m.playlist_id=" . intval($this->_playid) . " GROUP BY w.vid DESC";

								$playLists      = $wpdb->get_results($sql);
								$playlistCount  = count($playLists);
						} else {

							$playlist_name = __('Followed', 'vh');
							$user_followed_videos = '';
							$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
							if ( $user_followed_categories != '' ) {
								foreach ( $user_followed_categories['videos'] as $value ) {
									foreach ( $value as $video ) {
										$user_followed_videos .= $video['video_id'].',';
									}	
								}
							}
							$user_followed_videos = rtrim($user_followed_videos, ',');
							$sql = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare AS w
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play AS m ON m.media_id = w.vid
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist AS p on m.playlist_id = p.pid
								INNER JOIN " . $this->_wpdb->prefix . "posts s ON s.ID=w.slug
								WHERE w.publish='1' AND p.is_publish='1' AND w.vid IN(" . $user_followed_videos . ") GROUP BY w.vid DESC LIMIT ".$pagenum.', '.$dataLimit;


							$sql2 = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare AS w
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play AS m ON m.media_id = w.vid
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist AS p on m.playlist_id = p.pid
								INNER JOIN " . $this->_wpdb->prefix . "posts s ON s.ID=w.slug
								WHERE w.publish='1' AND p.is_publish='1' AND w.vid IN(" . $user_followed_videos . ") GROUP BY w.vid DESC";

								$playLists      = $TypeOFvideos = $wpdb->get_results($sql);
								$playLists2      = $wpdb->get_results($sql2);
								$playlistCount  = $CountOFVideos = count($playLists2);
						}
						
						global $wpdb;
						$moreName = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE \"%[video_category]%\" AND post_status=\"publish\" AND post_type=\"page\" limit 1");
						$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=' . $this->_playid;

						if ( is_user_logged_in() ) {
							$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
							if ( $user_followed_categories != '' ) {
								foreach ($user_followed_categories['followed_categories'] as $value) {
									if ( $value != $this->_playid ) {
										$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
									} else {
										$follow_button = '<a href="javascript:void(0)" class="unfollow-category icon-minus-circled">' . __('Unfollow', 'vh') . '</a>';
										break;
									}
								}
							} else {
								$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
							}

							if ( !empty($_GET['followed']) && $_GET['followed'] == 'true' ) {
								$follow_button = '';
								$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=-1&followed=true';
							}
						} else {
							$follow_button = '<a href="'.wp_login_url().'" class="follow-category-register icon-plus-circled">' . __('Follow', 'vh') . '</a>';
						}

						$div .= '<div style="clear: both;"><h2 class="more_title">'.$playlist_name.'<span class="title-video-count"><a href="' . $more_playlist_link . '" class="video-category-count">' . $playlistCount . __(' videos', 'vh') . '</a></span>' . $follow_button . '<input type="hidden" value="' . $this->_playid . '"><div class="followed_categories"></div></h2></div>';

					} else if( $typename == 'User' ) {
						$user_name = get_user_name(intval($this->_userid));
						$div      .='<h2 class="more_title">'.$user_name.' </h2>';

					} else {
						$div .='<h2 class="more_title">' . $typename . ' '.__('Videos', 'vh').' </h2>';
					}

				if (!empty($TypeOFvideos)) {
					$j          = 0;
					$clearwidth = 0;
					$clear      = $fetched[$j] = '';
					$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);

					foreach ($TypeOFvideos as $video) {
						$duration[$j] = $video->duration;         ## VIDEO DURATION
						$imageFea[$j] = $video->image;            ## VIDEO IMAGE
						$imageFea[$j] = str_replace("/mq","/sd",$imageFea[$j]);
						$image_header = get_headers_curl($imageFea[$j]);

						if ( mb_substr($image_header, 9, 3) == '404' ) {
							$imageFea[$j]   = $video->image;
							$video_sd_image = 'mqdef';

						} else {
							$imageFea[$j]   = str_replace("/mq","/sd",$video->image);
							$video_sd_image = 'sdimg';
						}

						$file_type = $video->file_type;        ## Video Type
						$guid[$j]  = vh_get_video_permalink($video->slug);             ## guid

						if ( $imageFea[$j] == '' ) {                  ## If there is no thumb image for video
							$imageFea[$j] = $this->_imagePath . 'nothumbimage.jpg';

						} else {
							if ($file_type == 2 || $file_type == 5 ) {          //For uploaded image
								$imageFea[$j] = $image_path . $imageFea[$j];
							} else {
								if ( strpos($imageFea[$j],'sddefault') !== false ) {
									$imageFea[$j] = vh_imgresize($imageFea[$j], 299, 224, $video->slug);
								} elseif ( strpos($imageFea[$j],'mqdefault') !== false ) {
									$imageFea[$j] = vh_imgresize($imageFea[$j], 299, 168, $video->slug);
								}
							}
						}

						$vidF[$j]        = $video->vid;              ## VIDEO ID
						$nameF[$j]       = $video->name;             ## VIDEI NAME
						$hitcount[$j]    = $video->hitcount;         ## VIDEO HITCOUNT
						$slug[$j]        = $video->slug;             ## VIDEO SLUG
						$post_date[$j]   = $video->post_date;        ## VIDEO POST DATE
						$description[$j] = $video->description;      ## VIDEO description
						$ratecount[$j]   = $video->ratecount;        ## VIDEO RATECOUNT
						$rate[$j]        = $video->rate;             ## VIDEO RATE
						$file_types[$j]  = $video->file_type;        ## VIDEO RATE
						$files[$j]       = $video->file;             ## VIDEO RATE
						$links[$j]       = $video->link;             ## VIDEO RATE
						$embed_code[$j]  = $video->embedcode;        ## VIDEO RATE

						$vh_swfPath      = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';
						$pluginflashvars = "baserefW=" . get_option('siteurl');
						$width           = '100%';
						$height          = '444px';

						if ( !empty($this->_playid) && $this->_playid != '-1' ) {
							$fetched[$j]       = $video->playlist_name;
							$fetched_pslug[$j] = $video->playlist_slugname;
							$playlist_id[$j]   = $this->_playid;

						} else {
							$getPlaylist = $this->_wpdb->get_row("SELECT playlist_id FROM " . $this->_wpdb->prefix . "hdflvvideoshare_med2play WHERE media_id='".intval($vidF[$j])."'");

							if ( isset($getPlaylist->playlist_id) ) {
								$playlist_id[$j]   = $getPlaylist->playlist_id;       ## VIDEO CATEGORY ID
								$fetPlay[$j]       = $this->_wpdb->get_row("SELECT playlist_name,playlist_slugname FROM " . $this->_wpdb->prefix . "hdflvvideoshare_playlist WHERE pid='".intval($playlist_id[$j])."'");
								$fetched[$j]       = $fetPlay[$j]->playlist_name;     ## CATEOGORY NAME
								$fetched_pslug[$j] = $fetPlay[$j]->playlist_slugname;     ## CATEOGORY NAME
							}
						}
						$j++;
					}
					$div .= '<div>';

					if ( empty($_GET['followed']) ) {
						$extra_container_class = "";
					} else {
						$extra_container_class = "followed";
					}

					$more_width  = 100/intval($colF);

					$div .= '<style type="text/css">.wrapper .video-block { width: '.$more_width.'% !important; }</style>
					<ul class="video-block-container open-video ' . $extra_container_class . ' category-videos">';

					for ($j = 0; $j < count($TypeOFvideos); $j++) {
						## Single category page video title length ##
						if (strlen($nameF[$j]) > 30) { ## Displaying Video Title
							$videoname = mb_substr($nameF[$j], 0, 30) . '..';
						} else {
							$videoname = $nameF[$j];
						}

						if (strlen($description[$j]) > 80) { ## Displaying Video description
								$videodescription = mb_substr($description[$j], 0, 80) . '..';
						} else {
							$videodescription = $description[$j];
						}
						// if (($j % $colF) == 0 && $j!=0) { ## COLUMN COUNT
						// 		$div        .= '</ul><div class="clear"></div><ul class="video-block-container">';
						// 	}

						$file_type = $file_types[$j]; ## Video Type
						$reafile   = $files[$j]; ##VIDEO IMAGE
						$guid      = vh_get_video_permalink($slug[$j]); ##guid
						
						## Embed player code
						if($file_type == 5 && !empty($embed_code[$j])){
							$relFetembedcode   = stripslashes($embed_code[$j]);
							$relFetiframewidth = preg_replace(array('/width="\d+"/i'),array(sprintf('width="%d"', $width)),$relFetembedcode);
								if($mobile === true) {
									$player_values = $relFetiframewidth;
								} else {
									$player_values = preg_replace(array('/height="\d+"/i'),array(sprintf('height="%d"', $height)),$relFetiframewidth);
								}
						 } else {
							 $mobile = vgallery_detect_mobile();
							if( $mobile === true ) {

								## Check for youtube video
								if (preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $reafile, $vresult)) {
									$urlArray    = explode("=", $vresult[0]);
									$video_id    = trim($urlArray[1]);
									$reavideourl = "http://www.youtube.com/embed/$video_id";

									## Generate youtube embed code for html5 player
									$player_values = '<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>';
								} else if ($file_type != 5) { ## Check for upload, URL and RTMP videos
									if ($file_type == 2) { ## For uploaded image
										$reavideourl = $image_path . $reafile;
									} else if ($file_type == 4) {           ## For RTMP videos
										$streamer    = str_replace("rtmp://", "http://", $media->streamer_path);
										$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
									} elseif ( strpos($reafile, 'soundcloud.com') !== false ) {
										$reavideourl = $reafile;
									}
									## Generate video code for html5 player
									$player_values = '<video id="video" poster="' . $imageFea[$j] . '"   src="' . $reavideourl .'" autobuffer controls preload="metadata">' . __( 'Html5 Not support This video Format.', 'vh' ) . '</video>';
								}
							} else {
								## Flash player code
								$player_values = '<embed src="' . $vh_swfPath . '" flashvars="' . $pluginflashvars . '&amp;mtype=playerModule&amp;vid='.$vidF[$j].'" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
							}
						}
						## VIDEO MORE IFRAME ##
						$div .= '<li class="video-block">';
						$div .= '<div  class="video-thumbimg">
									<div class="video_image_container ' . $video_sd_image . '">
										<a href="javascript:void(0);" class="video_play"></a>
										<a href="' . $guid . '" class="view_more"></a>
										<img src="' . $imageFea[$j] . '" alt="' . $nameF[$j] . '" class="imgHome" title="' . $nameF[$j] . '" />
										<div id="video_dialog" title="' . $nameF[$j] . '">';
										if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
											if ( strpos($files[$j],'soundcloud.com') !== false ) {
												$div .= '<input type="hidden" class="iframe_url" value="' . $files[$j] . '" />';
												$div .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
											} else {
												$div .= $player_values;
											}
										} else {
												if( strpos($files[$j], 'v=') !== false ) {
													$video_link = explode('v=', $files[$j]);
													if ( strpos($links[$j], 'rel=') !== false ) {
														$video_rel = explode('?', $links[$j]);
														$div .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '?' . $video_rel[1] . '" />';
													} else {
														$div .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
													}
													$div .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
												} elseif ( strpos($files[$j], '/v/') !== false ) {
													$video_link = explode('/v/', $files[$j]);
													$div .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
													$div .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
												} elseif ( strpos($files[$j], '/video/') !== false ) {
													$video_link = explode('/video/', $files[$j]);
													$video_link = explode('_', $video_link[1]);
													$div .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
													$div .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
												} else {

													if ( $file_type == 3 ) {
														$videourl   = $reafile;
														$image_file = $imageFea[$j];
													} elseif ( $file_type == 2 ) {
														$videourl   = $image_path . $reafile;

														$div .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
													} else {
														$videourl   = $image_path . $reafile;
														$image_file = $imageFea[$j];
													}

													if( $file_type == 5 && !empty($embed_code[$j]) ) {
														$div .= stripslashes($embed_code[$j]);
													}

													// $div .= do_shortcode('[video poster="' . $image_file . '" width="642" height="444" src="' . $videourl . '"]');
												}
										}
										$div .= '</div>
									</div>';
						$div .= '<div class="video_info">';
						if ($duration[$j] != 0.00) {
							$div .= '<div class="video-duration micon-clock">' . $duration[$j] . '</div>';
						}
						$div .= '<div class="video_views icon-eye">'. $hitcount[$j] . '</div>';
						$tc  = wp_count_comments($slug[$j]);
						$div .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';

						if ( function_exists('get_post_ul_meta') ) {
							$div .= '<div class="video_likes icon-heart">'. get_post_ul_meta($slug[$j], "like") . '</div>';
						}

						$div .= '</div>'; 
						// if ($duration[$j] != 0.00) {
						// 	$div        .= '<span class="video_duration">'.$duration[$j] . '</span>';
						// }
						$div .= '</div>';
						$div .= '<input type="hidden" class="v_date" value="'.$post_date[$j].'">';
						$div .= '<div class="vid_info"><a href="' . $guid . '" class="videoHname"><span class="title">';
						$div .= $videoname;
						$div .= '</span></a>';
						$div .= '<div class="video_views icon-eye video_category_h">'. $hitcount[$j] . '</div>';
						$tc  = wp_count_comments($slug[$j]);
						$div .= '<div class="video_comments icon-comment video_category_h">'. $tc->total_comments . '</div>';

						if ( function_exists('get_post_ul_meta') ) {
							$div .= '<div class="video_likes icon-heart video_category_h">'. get_post_ul_meta($slug[$j], "like") . '</div>';
						}

						$div .= '<div class="video_c_description video_category_h">'.$videodescription.'</div>';
						$div .= '<div class="video_c_author icon-user video_category_h">'.get_the_author_link($slug[$j]).'</div>';
						$div .= '<div class="video_c_date icon-calendar video_category_h">'.human_time_diff(get_the_time('U',$slug[$j]),current_time('timestamp')).'</div>';

						// if (!empty($fetched[$j])) {
						// 	$playlist_url = get_playlist_permalink($this->_mPageid,$playlist_id[$j],$fetched_pslug[$j]);
						// 	$div        .= '<a  class="playlistName" href="' . $playlist_url . '"><span>' . $fetched[$j] . '</span></a>';
						// }
						## Rating starts here
						if ($this->_settingsData->ratingscontrol == 1) {
							if (isset($ratecount[$j]) && $ratecount[$j] != 0) {
								$ratestar = round($rate[$j] / $ratecount[$j]);
							} else {
								$ratestar = 0;
							}
							$div .= '<span class="ratethis1 '.$ratearray[$ratestar].'"></span>';
						} 
							## Rating ends and views starts here
							// if ($this->_settingsData->view_visible == 1) {
							// $div            .= '<span class="video_views">';
							// 	if($hitcount[$j]>1){
							// 		$viewlang   = $this->_viewslang;
							// 	} else {
							// 		   $viewlang = $this->_viewlang;
							// 	}
							// $div            .= $hitcount[$j] . '&nbsp;'.$viewlang;
							// $div            .= '</span>';
							// }
						$div            .= '</div>';
						$div            .= '</li>';
						## ELSE ENDS
					} ## FOR EACH ENDS
					$div .= '</ul>';
					$div .= '</div>';
					$div .= '<div class="clear"></div>';
				} else {
					if ( $typename=='Category' ) {
						$div .= __('No', 'vh').'&nbsp;' .__('Videos', 'vh'). '&nbsp;'.__('Under&nbsp;this&nbsp;Category', 'vh');
					} else {
						$div .= __('No', 'vh').'&nbsp;' . $typename . '&nbsp;'.__('Videos', 'vh');
					}
				}
				$div                        .= '</div>';

				##PAGINATION STARTS
				$total        = $CountOFVideos;
				$num_of_pages = ceil($total / $dataLimit);

				if ( $pagenum == 0 ) {
					$pagenum = 1;
				}

				$page_links = paginate_links ( array (
						'base' => esc_url( add_query_arg( 'paged', '%#%' ) ),
						'format' => '',
						'prev_text' => __ ( '&laquo;', 'aag' ),
						'next_text' => __ ( '&raquo;', 'aag' ),
						'total' => $num_of_pages,
						'current' => $pagenum 
				) );

				if ($page_links) {
					$div        .='<div class="tablenav"><div class="tablenav-pages" >' . $page_links . '</div></div>';
				}
				##PAGINATION ENDS

				$div .= '</div><!-- end of video-block-container-wrapper -->';

				return $div;
			}
		}

		function get_countof_videocategories() {								
			global $wpdb;
			$query = 'SELECT count( * ) FROM ' . $wpdb->prefix . 'hdflvvideoshare_playlist WHERE is_publish=1';
			return $this->_wpdb->get_var( $query );
		}

	function categoryList($CountOFVideos, $TypeOFvideos, $pagenum, $dataLimit) {

		global $wpdb;
		$playlist_id     = '';
		$div             = '';
		$pagenum         = isset($pagenum) ? absint($pagenum) : 1;       ## Calculating page number
		$start           = ( $pagenum - 1 ) * $dataLimit;                ## Video starting from
		$ratearray       = array("nopos1", "onepos1", "twopos1", "threepos1", "fourpos1", "fivepos1");
		$vh_swfPath      = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';
		$pluginflashvars = "baserefW=" . get_option('siteurl');
		$width           = '100%';
		$height          = '444px';
		$category_width  = 100/intval($this->_settingsData->colCat);

		$div .= '<style> .video-block { margin-left:' . $this->_settingsData->gutterspace . 'px !important; } </style>';
		$div .= '<div class="sorting-container">
					<div id="cat_layout" class="category_layout_container">
						<div class="category_layout">'.__('Layout:', 'vh').'</div>
						<div class="layout table icon-th clicked"></div>
						<div class="layout list icon-th-list"></div>
					</div>';
			$div .= '<div id="sorts" class="category_sort_container">
						<div class="sort_by">'.__('Sort by:', 'vh').'</div>
						<div class="sort_param date clicked" data-sort-value="date">'.__('Date', 'vh').'</div>
						<div class="sort_param title" data-sort-value="title">'.__('Title', 'vh').'</div>
						<div class="sort_param views" data-sort-value="views">'.__('Views', 'vh').'</div>';
						if ( function_exists('ldc_like_counter_p') ) {
							$div .= '<div class="sort_param likes" data-sort-value="likes">'.__('Likes', 'vh').'</div>';
						}
					$div .= '</div>
				</div>
				<div class="video-block-container-wrapper">
				<style type="text/css">.wrapper .video-block { width: '.$category_width.'% !important; }</style>';

		foreach ($TypeOFvideos as $catList) {
		## Fetch videos for every category
		   $sql            = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare AS w
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play AS m ON m.media_id = w.vid
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist AS p on m.playlist_id = p.pid
							INNER JOIN " . $this->_wpdb->prefix . "posts s ON s.ID=w.slug
							WHERE w.publish='1' AND p.is_publish='1' AND m.playlist_id=" . intval($catList->pid) . " GROUP BY w.vid DESC";
			$playLists      = $wpdb->get_results($sql);
			$playlistCount  = count($playLists);

			global $wpdb;
			$moreName = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE \"%[video_category]%\" AND post_status=\"publish\" AND post_type=\"page\" limit 1");
			$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=' . $catList->pid;

			if ( is_user_logged_in() ) {
				$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
				if ( $user_followed_categories != '' ) {
					foreach ($user_followed_categories['followed_categories'] as $value) {
						if ( $value != $catList->pid ) {
							$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
						} else {
							$follow_button = '<a href="javascript:void(0)" class="unfollow-category icon-minus-circled">' . __('Unfollow', 'vh') . '</a>';
							break;
						}
					}
				} else {
					$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
				}
			} else {
				$follow_button = '<a href="'.wp_login_url().'" class="follow-category-register icon-plus-circled">' . __('Follow', 'vh') . '</a>';
			}

			$div .='<div> <h4 class="clear more_title">' . $catList->playlist_name . '<span class="title-video-count"><a href="' . $more_playlist_link . '" class="video-category-count">' . $playlistCount . __(' videos', 'vh') . '</a></span>' . $follow_button . '<input type="hidden" value="' . $catList->pid . '"><div class="followed_categories"></div></h4></div>';
			
			if (!empty($playlistCount)) {
				$i          = 0;
				$inc        = 1;
				$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
				$div        .= '<ul class="video-block-container open-video category-videos">';
				foreach ($playLists as $playList) {

					$duration     = $playList->duration;
					$imageFea     = $playList->image;             ## VIDEO IMAGE
					$imageFea     = str_replace("/mq","/sd",$imageFea);
					$image_header = get_headers_curl($imageFea);
					if ( mb_substr($image_header, 9, 3) == '404' ) {
						$imageFea  = $playList->image;
						$video_sd_image = 'mqdef';
					} else {
						$imageFea  = str_replace("/mq","/sd",$playList->image);
						$video_sd_image = 'sdimg';
					}
					$file_type  = $playList->file_type;         ## Video Type
					$guid       = vh_get_video_permalink($playList->slug);              ## guid
					if ($imageFea == '') {                      ## If there is no thumb image for video
						$imageFea = $this->_imagePath . 'nothumbimage.jpg';
					} else {
						if ($file_type == 2 || $file_type == 5 ) {                  ## For uploaded image
							$imageFea = $image_path . $imageFea;
						} else {
							if ( strpos($imageFea,'sddefault') !== false ) {
								$imageFea = vh_imgresize($imageFea, 299, 224, $playList->slug);
							} elseif ( strpos($imageFea,'mqdefault') !== false ) {
								$imageFea = vh_imgresize($imageFea, 299, 168, $playList->slug);
							}
						}
					}
					## Category page video title length ##
					if (strlen($playList->name) > 30) {
						$playListName = mb_substr($playList->name, 0, 30) . "..";
					} else {
						$playListName = $playList->name;
					}

					if (strlen($playList->description) > 80) { ## Displaying Video description
						$videodescription = mb_substr($playList->description, 0, 80) . '..';
					} else {
						$videodescription = $playList->description;
					}

					$file_type = $playList->file_type; ## Video Type
					$reafile   = $playList->file; ##VIDEO IMAGE
					$guid      = vh_get_video_permalink($playList->slug); ##guid
					
					## Embed player code
					if($file_type == 5 && !empty($playList->embedcode)){
						$relFetembedcode   = stripslashes($playList->embedcode);
						$relFetiframewidth = preg_replace(array('/width="\d+"/i'),array(sprintf('width="%d"', $width)),$relFetembedcode);
						$player_values = preg_replace(array('/height="\d+"/i'),array(sprintf('height="%d"', $height)),$relFetiframewidth);
					 } else {
						$mobile = vgallery_detect_mobile();
						if( $mobile === true ) {

							## Check for youtube video
							if (preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $reafile, $vresult)) {
								$urlArray    = explode("=", $vresult[0]);
								$video_id    = trim($urlArray[1]);
								$reavideourl = "http://www.youtube.com/embed/$video_id";

								## Generate youtube embed code for html5 player
								$player_values = '<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>';
							} else if ($file_type != 5) { ## Check for upload, URL and RTMP videos
								if ($file_type == 2) { ## For uploaded image
									$reavideourl = $image_path . $reafile;
								} else if ($file_type == 4) {           ## For RTMP videos
									$streamer    = str_replace("rtmp://", "http://", $media->streamer_path);
									$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
								}
								## Generate video code for html5 player
								$player_values = '<video id="video" poster="' . $imageFea . '"   src="' . $reavideourl .'" autobuffer controls preload="metadata">' . __( 'Html5 Not support This video Format.', 'vh' ) . '</video>';
							}
						} else {
							## Flash player code
							$player_values = '<embed src="' . $vh_swfPath . '" flashvars="' . $pluginflashvars . '&amp;mtype=playerModule&amp;vid='.$playList->vid.'" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
						}
					}

					$div .= '<li class="video-block"><div class="video-thumbimg">';

					$div .= '<div class="video_info">';
								if ($playList->duration != 0.00) {
									$div .= '<div class="video-duration micon-clock">' . $playList->duration . '</div>';
								}
								$div .= '<div class="video_views icon-eye">'. $playList->hitcount . '</div>';
								$tc = wp_count_comments($playList->slug);
								$div .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
								if ( function_exists('get_post_ul_meta') ) {
									$div .= '<div class="video_likes icon-heart">'. get_post_ul_meta($playList->slug, "like") . '</div>';
								}
					$div .= '</div>
							<div class="video_image_container '.$video_sd_image.'">
								<a href="javascript:void(0);" class="video_play"></a>
								<a href="' . $guid . '" class="view_more"></a>
								<img src="' . $imageFea . '" alt="" class="imgHome" title="" />
								<div id="video_dialog" title="' . $playListName . '">';
								if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
									if ( strpos($playList->file,'soundcloud.com') !== false ) {
										$div .= '<input type="hidden" class="iframe_url" value="' . $playList->file . '" />';
										$div .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
									} else {
										$div .= $player_values;
									}
								} else {
									if( strpos($playList->file, 'v=') !== false ) {
										$video_link = explode('v=', $playList->file);
										if ( strpos($playList->link, 'rel=') !== false ) {
											$video_rel = explode('?', $playList->link);
											$div .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '?' . $video_rel[1] . '" />';
										} else {
											$div .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
										}
										$div .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
									} elseif ( strpos($playList->file, '/v/') !== false ) {
										$video_link = explode('/v/', $playList->file);
										$div .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
										$div .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
									} elseif ( strpos($playList->file, '/video/') !== false ) {
										$video_link = explode('/video/', $playList->file);
										$video_link = explode('_', $video_link[1]);
										$div .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
										$div .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
									} else {

										if ( $file_type == 3 ) {
											$videourl   = $reafile;
											$image_file = $imageFea;
										} elseif ( $file_type == 2 ) {
											$videourl   = $image_path . $reafile;

											$div .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
										} else {
											$videourl   = $image_path . $reafile;
											$image_file = $imageFea;
										}

										if( $file_type == 5 && !empty($playList->embedcode) ) {
											$div .= stripslashes($playList->embedcode);
										}

										// $div .= do_shortcode('[video poster="' . $image_file . '" width="642" height="444" src="' . $videourl . '"]');
									}
								}
								$div .= '</div>
							</div>';
					$div .= '<input type="hidden" class="v_date" value="'.$playList->post_date.'">';
					$div        .= '</div><div class="vid_info"><h5><a href="' . $guid . '" class="videoHname title">' . $playListName . '</a></h5>';
					$div .= '<div class="video_views icon-eye video_category_h">'. $playList->hitcount . '</div>';
						$tc = wp_count_comments($playList->slug);
						$div .= '<div class="video_comments icon-comment video_category_h">'. $tc->total_comments . '</div>';
						if ( function_exists('get_post_ul_meta') ) {
							$div .= '<div class="video_likes icon-heart video_category_h">'. get_post_ul_meta($playList->slug, "like") . '</div>';
						}
					$div .= '<div class="clearfix"></div>';
					if (strlen($videodescription) != null) {
						$div .= '<div class="video_c_description video_category_h">'.$videodescription.'</div>';
					}
					$div .= '<div class="clearfix"></div>';
					$div .= '<div class="video_c_author icon-user video_category_h">'.get_the_author_link($playList->slug).'</div>';
					$div .= '<div class="video_c_date icon-calendar video_category_h">'.human_time_diff(get_the_time('U',$playList->slug),current_time('timestamp')).'</div>';
					## Rating starts here
					if ($this->_settingsData->ratingscontrol == 1) {
							if (isset($playList->ratecount) && $playList->ratecount != 0) {
								$ratestar    = round($playList->rate / $playList->ratecount);
							} else {
								$ratestar    = 0;
							}
							$div             .= '<span class="ratethis1 '.$ratearray[$ratestar].'"></span>';
						}
					## Rating ends here
					$div        .= '</div></li>';

					if ($i > ($this->_perCat-2)) {
						break;
					} else {
						$i = $i + 1;
					}

				}
				$div            .= '</ul>';

				if (($playlistCount > 9)) {

					$div        .= '<a class="video-more-category" href="' . $this->_site_url . '/?page_id=' .  $moreName . '&playid=' . $catList->pid . '">'.__('More&nbsp;Videos', 'vh').'</a>';
				} else {
					$div        .= '<div align="right"> </div>';
				}
			} else {                                                        ## If there is no video for category
				$div            .= '<div class="no_videos_inside">'.__('No&nbsp;Videos&nbsp;for&nbsp;this&nbsp;Category', 'vh').'</div>';
			}
		}

		$div                    .= '<div class="clear"></div>';

		## PAGINATION STARTS
		$total        = $CountOFVideos;
		$num_of_pages = ceil($total / $dataLimit);
		
		if ( $pagenum == 0 ) {
			$pagenum = 1;
		}

		$page_links = paginate_links ( array (
				'base' => esc_url( add_query_arg( 'paged', '%#%' ) ),
				'format' => '',
				'prev_text' => __ ( '&laquo;', 'aag' ),
				'next_text' => __ ( '&raquo;', 'aag' ),
				'total' => $num_of_pages,
				'current' => $pagenum 
		) );

		if ($page_links) {
			$div        .= '<div class="tablenav"><div class="tablenav-pages" >' . $page_links . '</div></div>';
		}
		## PAGINATION ENDS

		$div .= '</div><!-- end of video-block-container-wrapper -->';

		return $div;
	}

	function searchList($video_search,$CountOFVideos, $TypeOFvideos, $pagenum, $dataLimit) {

		global $wpdb;
		$div        = '';
		$pagenum    = isset($pagenum) ? absint($pagenum) : 1;   ## Calculating page number
		$start      = ( $pagenum - 1 ) * $dataLimit;            ## Video starting from
		$limit      = $dataLimit;                               ## Video Limit
		$ratearray = array("nopos1", "onepos1", "twopos1", "threepos1", "fourpos1", "fivepos1");

		$div .='<div class="video_wrapper" id="video_search_result"><h3 class="entry-title">'.__('Search Results', 'vh').' - '.$video_search.'</h3>';
		$div .= '<style> .video-block { margin-left:' . $this->_settingsData->gutterspace . 'px !important; } </style>';

			## Fetch videos for every category
			if (!empty($TypeOFvideos)) {
				$i          = 0;
				$inc        = 0;
				$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
				$div        .= '<ul class="video-block-container">';

				foreach ($TypeOFvideos as $playList) {

					$duration   = $playList->duration;
					$imageFea   = $playList->image;         ## VIDEO IMAGE
					$file_type  = $playList->file_type;     ## Video Type
					$guid       = vh_get_video_permalink($playList->slug);         ## guid
					if ($imageFea == '') {                  ## If there is no thumb image for video
						$imageFea = $this->_imagePath . 'nothumbimage.jpg';
					} else {
						if ($file_type == 2 || $file_type == 5 ) {              ## For uploaded image
							$imageFea = $image_path . $imageFea;
						}
					}
					if (strlen($playList->name) > 30) {
						$playListName = mb_substr($playList->name, 0, 30) . "..";
					} else {
						$playListName = $playList->name;
					}
				if (($inc % $this->_colF ) == 0 && $inc!=0) { ## COLUMN COUNT
							$div .= '</ul><div class="clear"></div><ul class="video-block-container">';
						}
					$div        .= '<li class="video-block"><div class="video-thumbimg"><a href="' . $guid . '"><img src="' . $imageFea . '" alt="" class="imgHome" title="" /></a>';
					if ($duration != 0.00) {
						$div    .= '<span class="video_duration">' . $duration. '</span>';
					}
					$div        .= '</div><h5><a href="' . $guid . '" class="videoHname">' . $playListName . '</a></h5><div class="vid_info">';
					if (!empty($playList->playlist_name)) {
						$playlist_url = get_playlist_permalink($this->_mPageid,$playList->pid,$playList->playlist_slugname);
							$div .= '<h6 class="playlistName"><a href="' . $playlist_url . '">' . $playList->playlist_name . '</a></h6>';
						}
					
					## Rating ends and views starts here
					if ($this->_settingsData->view_visible == 1) {
						if($playList->hitcount>1){
								$viewlang = $this->_viewslang;
						} else {
								   $viewlang = $this->_viewlang;
						}
						$div        .= '<span class="video_views">' . $playList->hitcount . '&nbsp;'.$viewlang . '</span>';
					}
					$div        .= '</div></li>';

					$inc++;
				}
				$div            .= '</ul>';

			} else { ## If there is no video for category
				$div            .= '<div>'.__('No&nbsp;Videos&nbsp;Found', 'vh').'</div>';
			}
		$div                    .= '</div>';

		$div                    .= '<div class="clear"></div>';

		## PAGINATION STARTS
		$total          = $CountOFVideos;
		$num_of_pages   = ceil($total / $dataLimit);
		$video_search   = str_replace(" ", "%20", $video_search);
		$arr_params     = array ( 'pagenum' => '%#%');
		
		if ( $pagenum == 0 ) {
			$pagenum = 1;
		}

		$page_links = paginate_links ( array (
				'base' => esc_url( add_query_arg( 'paged', '%#%' ) ),
				'format' => '',
				'prev_text' => __ ( '&laquo;', 'aag' ),
				'next_text' => __ ( '&raquo;', 'aag' ),
				'total' => $num_of_pages,
				'current' => $pagenum 
		) );

		if ($page_links) {
			$div    .= '<div class="tablenav"><div class="tablenav-pages" >' . $page_links . '</div></div>';
		}

		## PAGINATION ENDS
		return $div;
	}
	## CATEGORY FUNCTION ENDS
}
}
## class over

function vh_video_category() {
 global $frontControllerPath, $frontModelPath, $frontViewPath, $wp_query;
	$playid = filter_input(INPUT_GET, 'playid'); 
	$more = &$wp_query->query_vars["more"];
	$playlist_name = &$wp_query->query_vars["playlist_name"];
	if (!empty($playlist_name)) {
		$playid = get_playlist_id($playlist_name);
	}
	$wp_query->query_vars["playid"] = $playid;

	$userid = filter_input(INPUT_GET, 'userid'); 
	$user_name = &$wp_query->query_vars["user_name"];
	$user_name = str_replace('%20', ' ', $user_name);
	if (!empty($user_name)) {
		$userid = get_user_id($user_name);
	}
	$wp_query->query_vars["userid"] = $userid;

	include_once ($frontControllerPath . 'videomoreController.php');
	$videoOBJ = new ContusMoreViewEdited();

	if (!empty($playid)){
		$more = 'cat';
	}    
	if (!empty($userid)){
		$more = 'user';
	}    
	$video_search = &$wp_query->query_vars["video_search"];
	if (!empty($video_search)){
		$more = 'search';
	}
	$contentvideoPlayer = $videoOBJ->video_more_pages($more);
	return $contentvideoPlayer;
}
add_shortcode('video_category', 'vh_video_category');

if ( class_exists('ContusVideoView') ) {

	class ContusVideoViewEdited extends ContusVideoController {       ##CLASS FOR HOME PAGE STARTS

		public $_settingsData;
		public $_videosData;
		public $_swfPath;
		public $_singlevideoData;
		public $_videoDetail;
		public $_vId;

		public function __construct() {                                             ##contructor starts
			parent::__construct();
			$this->_settingsData      = getPluginSettings();               ## Get player settings
			$this->_mPageid           = morePageID();                 ## Get more page id
			// $this->_feaMore           = $this->Video_count();                 ## Get featured videos count
			$this->_vId               = filter_input(INPUT_GET, 'vid');       ## Get vid from URL
			$this->_pId               = filter_input(INPUT_GET, 'pid');       ## Get pid from URL
			// $this->_tagname           = $this->Tag_detail($this->_vId);       ## Get tag detail for the current video
			$this->_pagenum           = filter_input(INPUT_GET, 'pagenum');   ## Get current page number
			$this->_showF             = 5;
			$this->_colCat            = $this->_settingsData->colCat;
			$this->_site_url          = get_site_url();
			// $this->_singlevideoData   = $this->home_playerdata();
			$this->_featuredvideodata = $this->home_featuredvideodata();      ## Get featured videos data
			$this->_viewslang         = __('Views', 'vh');
			$this->_viewlang          = __('View', 'vh');
			$dir                      = dirname(plugin_basename(__FILE__));
			$dirExp                   = explode('/', $dir);
			$this->_plugin_name       = $dirExp[0];                           ## Get plugin folder name
			$this->_bannerswfPath     = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer_banner.swf';     ## Declare banner swf path
			$this->_swfPath           = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';            ## Declare swf path
			$this->_imagePath         = APPTHA_VGALLERY_BASEURL . 'images' . DS;                                  ## Declare image path
		}

		function get_more_pageid() {   ##function for getting more page ID starts
			$moreName = $this->_wpdb->get_var("select ID from " . $this->_wpdb->prefix . "posts WHERE post_content LIKE '%[video_category]%' and post_status='publish' and post_type='page' limit 1");
			return $moreName;
		}

		##contructor ends
		function home_player() {                ## FUNCTION FOR HOME PAGE STARTS
			$settingsData = $this->_settingsData;
			$videoUrl     = $videoId = $thumb_image = $homeplayerData = $file_type = '';
			$mobile       = vgallery_detect_mobile();
			if (!empty($this->_featuredvideodata[0])){
				$homeplayerData = $this->_featuredvideodata[0];
			}
			$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
			$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
			if (!empty($homeplayerData)) {
				$videoUrl    = $homeplayerData->file;                ## Get video URL
				$videoId     = $homeplayerData->vid;                 ## Get Video ID
				$thumb_image = $homeplayerData->image;               ## Get thumb image
				$file_type   = $homeplayerData->file_type;           ## Get file type of a video
				if ($thumb_image == '') {       ## If there is no thumb image for video
					$thumb_image = $_imagePath . 'nothumbimage.jpg';
				} else {
					if ($file_type == 2 || $file_type == 5) {      ## For uploaded image
						$thumb_image = $image_path . $thumb_image;
					}
				}
			}

			$moduleName = "playerModule";
			$div        = '<div class="video_home_featured">'; ## video player starts
			## To increase hit count of a video
			$div .= '<script type="text/javascript" src="' . APPTHA_VGALLERY_BASEURL . 'js/script.js"></script>';
			$div .= '<style type="text/css" scoped> .video-block {margin-left:' . $settingsData->gutterspace . 'px !important; float:left;} </style>';
			$div .=' <script>
											var baseurl,folder,videoPage;
											baseurl = "' . $this->_site_url . '";
											folder  = "' . $this->_plugin_name . '";
											videoPage = "' . $this->_mPageid . '"; </script>';
			$baseref = '';
			if (!empty($this->_vId)) {
				$baseref .= '&amp;vid=' . $this->_vId;
			} else {
				$baseref .= '&amp;featured=true';
			}
			$div .='<div id="mediaspace" class="mediaspace" style="color: #666;">';

			// $div                            .='<h3 id="video_title" style="width:' . $settingsData->width . ';text-align: left;"  class="more_title">'.$homeplayerData->name.'</h3>';
			if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {

				## FLASH PLAYER STARTS HERE
				$div .='<div id="flashplayer">';
				if ($settingsData->default_player == 1) {
					$swf          = $this->_bannerswfPath;
					$showplaylist = "&amp;showPlaylist=true";
				} else {
					$swf          = $this->_swfPath;
					$showplaylist = '';
				}

				## Embed player code
				if($homeplayerData->file_type == 5 && !empty($homeplayerData->embedcode)){
				$playerembedcode  = stripslashes($homeplayerData->embedcode);
				$div             .=  str_replace('width=', 'width="'.$settingsData->width.'"', $playerembedcode);
				$div             .= '<script> current_video('.$homeplayerData->vid.',"'.$homeplayerData->name.'"); </script>';
				} else{
					if($mobile === true){
						if ((preg_match('/vimeo/', $videoUrl)) && ($videoUrl != '')) { ##IF VIDEO IS YOUTUBE
					$vresult                    = explode("/", $videoUrl);
					$div                  .="<iframe  type='text/html' src='http://player.vimeo.com/video/" . $vresult[3] . "' frameborder='0' width='100%' height='600px'></iframe>";
				} elseif (strpos($videoUrl, 'youtube') > 0) {
					$imgstr    = explode("v=", $videoUrl);
					$imgval    = explode("&", $imgstr[1]);
					$videoId1  = $imgval[0];
					$div      .="<iframe  type='text/html' src='http://www.youtube.com/embed/" . $videoId1 . "' frameborder='0' width='100%' height='600px'></iframe>";
				} else {    ##IF VIDEO IS UPLOAD OR DIRECT PATH
					if ($file_type == 2) {          ##For uploaded image
						$videoUrl = $image_path . $videoUrl;
					} else if ($file_type == 4) {          ##For RTMP videos
						$streamer = str_replace("rtmp://", "http://", $homeplayerData->streamer_path);
						$videoUrl = $streamer . '_definst_/mp4:' . $videoUrl . '/playlist.m3u8';
					}
					$div .="<video id='video' poster='" . $thumb_image . "'   src='" . $videoUrl . "' autobuffer controls preload='metadata'>" . __('Html5 Not support This video Format.', 'vh') . "</video>";
				}
					} else {
						## Flash player code
						$div .= '<embed id="player" src="' . $swf . '"  flashvars="baserefW=' . site_url() . $baseref . $showplaylist . '&amp;mtype=' . $moduleName . '" width="' . $settingsData->width . '" height="' . $settingsData->height . '"   allowFullScreen="true" allowScriptAccess="always" type="application/x-shockwave-flash" wmode="transparent" />';
					}
				}
				$div                            .='</div>';
				## FLASH PLAYER ENDS AND HTML5 PLAYER STARTS HERE
				$htmlvideo = '';

				$windo                          = '';
				$useragent                      = $_SERVER['HTTP_USER_AGENT'];
				if (strpos($useragent, 'Windows Phone') > 0)
					$windo                      = 'Windows Phone';
				##SCRIPT FOR CHECKING PLATFORM
				$div                            .= '<script>
												var txt =  navigator.platform ;
												var windo = "' . $windo . '";
												function failed(e)
												{
												if(txt =="iPod"|| txt =="iPad" || txt == "iPhone" || windo=="Windows Phone" || txt == "Linux armv7l" || txt == "Linux armv6l")
												{
												alert("' . __('Player doesnot support this video.', 'video_gallery') . '");
												}
												}
												</script>';
				## ERROR MESSAGE FOR VIDEO NOT SUPPORTED TO PLAYER ENDS
				## HTML5 ENDS

			} else {
				// $imgstr    = explode("v=", $videoUrl);
				// $imgval    = explode("&", $imgstr[1]);
				// $videoId1  = $imgval[0];
				$div .='<div id="flashplayer">';
				if( strpos($videoUrl, 'v=') !== false ) {
					$video_link = explode('v=', $videoUrl);
					$div .= '<iframe width="100%" height="608" src="//www.youtube.com/embed/' . $video_link[1] . '" frameborder="0" allowfullscreen></iframe>';
				} elseif ( strpos($videoUrl, '/v/') !== false ) {
					$video_link = explode('/v/', $videoUrl);
					$div .= '<iframe src="//www.viddler.com/embed/' . $video_link[1] . '" width="100%" height="608" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
				} elseif ( strpos($videoUrl, '/video/') !== false ) {
					$video_link = explode('/video/', $videoUrl);
					$video_link = explode('_', $video_link[1]);
					$div .= '<iframe frameborder="0" width="100%" height="608" src="//www.dailymotion.com/embed/video/' . $video_link[0] . '" allowfullscreen></iframe>';
				} else {

					if ( $file_type == 3 ) {
						$videourl   = $videoUrl;
						$image_file = $thumb_image;
					} else {
						$videourl   = $image_path . $videoUrl;
						$image_file = $thumb_image;
					}

					$div .= do_shortcode('[video poster="' . $thumb_image . '" width="955" height="590" src="' . $videourl . '"]');
				}
				$div .='</div>';
			}
			$div                        .= '<div id="video_tag" class="views"></div>';
			$div                        .= '</div>';
			$div                        .='</div>';
			return $div;
		}

			##FUNCTION FOR HOME PAGE PLAYER ENDS

		function home_thumb($type) {    ## HOME PAGE FEATURED VIDEOS STARTS
			if (function_exists('homeVideo') != true) {
				$output  = '';
				$TypeSet = '';
				$dir     = dirname(plugin_basename(__FILE__));
				$dirExp  = explode('/', $dir);
				$dirPage = $dirExp[0];
				$where = '';
				$TypeSet = $recent_video_order = $class = $divOutput = '';          
				$player_colors      = unserialize($this->_settingsData->player_colors);
				$recent_video_order = $player_colors ['recentvideo_order'];

				switch ($type) {
	              case 'popular' : 
	                $TypeSet        = $this->_settingsData->popular; 
	                $rowF             = $this->_settingsData->rowsPop;
	                $colF             = $this->_settingsData->colPop;
	                $dataLimit        = $rowF *  $colF;
	                $thumImageorder = 'w.hitcount DESC';
	                $typename       = __ ( 'Popular', APPTHA_VGALLERY );
	                $type_name      = $morePage = 'popular';
	                break;            
	              case 'recent' :
	                $TypeSet        = $this->_settingsData->recent; 
	                $rowF             = $this->_settingsData->rowsRec;
	                $colF             = $this->_settingsData->colRec;
	                $dataLimit        = $rowF *  $colF;
	                $thumImageorder = 'w.vid DESC';
	                $typename       = __ ( 'Recent', APPTHA_VGALLERY );
	                $type_name      = $morePage = 'recent';
	                break;            
	              case 'featured' :
	                $TypeSet          = $this->_settingsData->feature;
	                $rowF             = $this->_settingsData->rowsFea;
	                $colF             = $this->_settingsData->colFea;
	                $dataLimit        = $rowF *  $colF;
	                $where            = ' AND w.featured=1 ';              
	                $thumImageorder   = getVideoOrder ( $recent_video_order );                 
	                $typename         = __ ( 'Featured', APPTHA_VGALLERY );
	                $type_name        =  $morePage = 'featured';
	                break;            
	              case 'cat' :
	                if ($this->_settingsData->homecategory == 1) {
	                  $category_page  = $this->_settingsData->category_page;
	                  $rowF           = $this->_settingsData->rowCat;
	                  $colF           = $this->_settingsData->colCat;
	                  $dataLimit      = $rowF *  $colF;                
	                  $thumImageorder = getVideoOrder ( $recent_video_order );
	                  $typename       = __ ( 'Video Categories', APPTHA_VGALLERY );
	                }
	                break;
	              default:
	                break;
	          }  
	          if ($type == 'popular' ||  $type == 'recent' ||  $type == 'featured' ) {
	              /** Get home page thumb data and get count of videos */
	              $TypeOFvideos     = $this->home_thumbdata ( $thumImageorder, $where, $dataLimit );
	              $CountOFVideos    = $this->countof_home_thumbdata ( $thumImageorder, $where );
	          }
	          if ($type == 'cat') {
	              /** Get home page category thumb data and get count of videos */
	              $TypeOFvideos   = $this->home_categoriesthumbdata ( $this->_pagenum, $category_page );
	              $CountOFVideos  = getPlaylistCount ();
	              /** Call function to display category videos in home page */
	              return $this->categorylist ( $CountOFVideos, $TypeOFvideos, $this->_pagenum, $dataLimit, $category_page, $thumImageorder );
	          }

				$class           = $div = '';
				$ratearray       = array("nopos1", "onepos1", "twopos1", "threepos1", "fourpos1", "fivepos1");
				$image_path      = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
				$vh_swfPath      = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';
				$pluginflashvars = "baserefW=" . get_option('siteurl');
				$width           = '100%';
				$height          = '444px';

				$video_width  = 100/intval($colF);

				// Video page
				if ($TypeSet) {                                             ## CHECKING FAETURED VIDEOS ENABLE STARTS
					$div = '<div class="video_wrapper" id="' . $type_name . '_video">';
					$div .= '<style type="text/css" scoped> .video-block {margin-left:' . $this->_settingsData->gutterspace . 'px !important;float:left;}  </style><style type="text/css">.wrapper #'.$type_name.'_video .video-block { width: '.$video_width.'% !important; }</style>
';

					if (!empty($TypeOFvideos)) {
						$div .= '<h2 class="video_header">' . $typename . ' ' . __('Videos', 'vh') . '</h2>';
						$j    = 0;
						foreach ($TypeOFvideos as $video) {
							$duration[$j] = $video->duration;         ## VIDEO DURATION
							$imageFea[$j] = $video->image;            ## VIDEO IMAGE
							$imageFea[$j] = str_replace("/mq","/sd",$imageFea[$j]);
							$image_header = get_headers_curl($imageFea[$j]);
							if ( mb_substr($image_header, 9, 3) == '404' ) {
								$imageFea[$j]   = $video->image;
								$video_sd_image = 'mqdef';
							} else {
								$imageFea[$j]   = str_replace("/mq","/sd",$video->image);
								$video_sd_image = 'sdimg';
							}
							$file_type         = $video->file_type;        ## Video Type
							$playlist_id[$j]   = $video->pid;              ## VIDEO CATEGORY ID
							$fetched[$j]       = $video->playlist_name;    ## CATEOGORY NAME
							$fetched_pslug[$j] = $video->playlist_slugname;    ## CATEOGORY slug NAME
							$guid[$j]          = vh_get_video_permalink($video->slug);            ## guid
							if ($imageFea[$j] == '') {                      ## If there is no thumb image for video
								$imageFea[$j] = $this->_imagePath . 'nothumbimage.jpg';
							} else {
								if ($file_type == 2 || $file_type == 5) {          ##For uploaded image
									$imageFea[$j] = $image_path . $imageFea[$j];
								} else {
									if ( strpos($imageFea[$j],'sddefault') !== false ) {
										$imageFea[$j] = vh_imgresize($imageFea[$j], 299, 224, $video->slug);
									} elseif ( strpos($imageFea[$j],'mqdefault') !== false ) {
										$imageFea[$j] = vh_imgresize($imageFea[$j], 299, 168, $video->slug);
									}
								}
							}
							$vidF[$j]       = $video->vid;              ## VIDEO ID
							$nameF[$j]      = $video->name;             ## VIDEI NAME
							$hitcount[$j]   = $video->hitcount;         ## VIDEO HITCOUNT
							$ratecount[$j]  = $video->ratecount;        ## VIDEO RATECOUNT
							$slug[$j]       = $video->slug;             ## VIDEO SLUG
							$rate[$j]       = $video->rate;             ## VIDEO RATE
							$file_types[$j] = $video->file_type;        ## VIDEO RATE
							$files[$j]      = $video->file;             ## VIDEO RATE
							$links[$j]      = $video->link;             ## VIDEO RATE
							$embed_code[$j] = $video->embedcode;        ## VIDEO RATE
							$j++;
						}

						$div                    .= '<div class="video_thumb_content">';
						$div                    .= '<ul class="video-block-container video-home">';
						for ($j = 0; $j < count($TypeOFvideos); $j++) {
							$pos = strpos($imageFea[$j], '/sd');
							$pos2 = strpos($imageFea[$j], '-sd');
							if ( $pos === false && $pos2 === false ) {
								$video_sd_image = 'mqdef';
							} else {
								$video_sd_image = 'sdimg';
							}
							$class = '<div class="clear"></div>';

							$div .= '<li class="video-block">';
							$div .='<div  class="video-thumbimg">';
							$div .= '<div class="video_home_info">';
							if ($duration[$j] != 0.00) {
								$div .= '<div class="video-duration micon-clock">' . $duration[$j] . '</div>';
							}
							if ($this->_settingsData->view_visible == 1) {
								$div .='<span class="views icon-eye">' . $hitcount[$j] . '</span>';
							}
							$tc = wp_count_comments($slug[$j]);
							$div .='<span class="comments icon-comment">' . $tc->total_comments . '</span>';
							if ( function_exists('get_post_ul_meta') ) {
								$div .='<span class="likes icon-heart">' . get_post_ul_meta($slug[$j], "like") . '</span>';
							}

							$file_type = $file_types[$j]; ## Video Type
							$reafile   = $files[$j]; ##VIDEO IMAGE
							
							## Embed player code
							if($file_type == 5 && !empty($embed_code[$j])){
								$relFetembedcode   = stripslashes($embed_code[$j]);
								$relFetiframewidth = preg_replace(array('/width="\d+"/i'),array(sprintf('width="%d"', $width)),$relFetembedcode);
								$player_values = preg_replace(array('/height="\d+"/i'),array(sprintf('height="%d"', $height)),$relFetiframewidth);
							 } else {
								 $mobile = vgallery_detect_mobile();
								if( $mobile === true ) {

									## Check for youtube video
									if (preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $reafile, $vresult)) {
										$urlArray    = explode("=", $vresult[0]);
										$video_id    = trim($urlArray[1]);
										$reavideourl = "http://www.youtube.com/embed/$video_id";

										## Generate youtube embed code for html5 player
										$player_values = '<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>';
									} else if ($file_type != 5) { ## Check for upload, URL and RTMP videos
										if ($file_type == 2) { ## For uploaded image
											$reavideourl = $image_path . $reafile;
										} else if ($file_type == 4) {           ## For RTMP videos
											$streamer    = str_replace("rtmp://", "http://", $media->streamer_path);
											$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
										}
										## Generate video code for html5 player
										$player_values = '<video id="video" poster="' . $imageFea[$j] . '"   src="' . $reavideourl .'" autobuffer controls preload="metadata">' . __( 'Html5 Not support This video Format.', 'vh' ) . '</video>';
									}
								} else {
									## Flash player code
									$player_values = '<embed src="' . $vh_swfPath . '" flashvars="' . $pluginflashvars . '&amp;mtype=playerModule&amp;vid='.$vidF[$j].'" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
								}
							}
							## VIDEO HOME POPULAR/RECENT/FEATURED IFRAME ##
							$div .= '</div>
									<div class="video_image_container '.$video_sd_image.'">
										<a href="javascript:void(0);" class="video_play"></a>
										<a href="' . $guid[$j] . '" class="view_more"></a>
										<img src="' . $imageFea[$j] . '" alt="' . $nameF[$j] . '" class="imgHome" title="' . $nameF[$j] . '" />
										<div id="video_dialog" title="' . $nameF[$j] . '">';
										if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
											if ( strpos($files[$j],'soundcloud.com') !== false ) {
												$div .= '<input type="hidden" class="iframe_url" value="' . $files[$j] . '" />';
												$div .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
											} else {
												$div .= $player_values;
											}
										} else {
											if( strpos($files[$j], 'v=') !== false ) {
												$video_link = explode('v=', $files[$j]);
												if ( strpos($links[$j], 'rel=') !== false ) {
													$video_rel = explode('?', $links[$j]);
													$div .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '?' . $video_rel[1] . '" />';
												} else {
													$div .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
												}
												$div .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
											} elseif ( strpos($files[$j], '/v/') !== false ) {
												$video_link = explode('/v/', $files[$j]);
												$div .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
												$div .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
											} elseif ( strpos($files[$j], '/video/') !== false ) {
												$video_link = explode('/video/', $files[$j]);
												$video_link = explode('_', $video_link[1]);
												$div .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
												$div .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
											} else {

												if ( $file_type == 3 ) {
													$videourl   = $files[$j];
													$image_file = $imageFea[$j];
												} elseif ( $file_type == 2 ) {
													$videourl   = $image_path . $files[$j];

													$div .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
												} else {
													$videourl   = $image_path . $files[$j];
													$image_file = $imageFea[$j];
												}

												if( $file_type == 5 && !empty($embed_code[$j]) ) {
													$div .= stripslashes($embed_code[$j]);
												}

												// $div .= '<div style="width: 642px; height: 444px;">' . do_shortcode('[video poster="' . $image_file . '" width="642" height="444" src="' . $videourl . '"]') . '</div>';
											}
										}
										$output .= '</div>
									</div>';
							// if ($duration[$j] != 0.00) {
							//     $div            .= '<span class="video_duration">' . $duration[$j] . '</span>';
							// }
							
							$div .= '</div></div></div>';
							$div .= '<div class="vid_info"><a href="' . $guid[$j] . '" class="videoHname"><span>';
							## Video page Popular/Recent/Featured video title length ##
							if (strlen($nameF[$j]) > 30) {
								$div .= mb_substr($nameF[$j], 0, 30) . '..';
							} else {
								$div .= $nameF[$j];
							}
							$div .= '</span></a>';
							$div .= '</div>';
							// if ($fetched[$j] != '') {
							//     $playlist_url = get_playlist_permalink($this->_mPageid,$playlist_id[$j],$fetched_pslug[$j]);
							//     $div            .= '<a class="playlistName" href="'.$playlist_url.'"><span>' . $fetched[$j] . '</span></a>';
							// }
							if ($this->_settingsData->ratingscontrol == 1) {
								if (isset($ratecount[$j]) && $ratecount[$j] != 0) {
									$ratestar = round($rate[$j] / $ratecount[$j]);
								} else {
									$ratestar = 0;
								}
								$div .= '<span class="ratethis1 '.$ratearray[$ratestar].'"></span>';
							}
							// if ($this->_settingsData->view_visible == 1) {
							// if ($hitcount[$j] > 1)
							// 	$viewlang       = $this->_viewslang;
							// else
							// 	$viewlang       = $this->_viewlang;
							// $div                .= '<span class="video_views">' . $hitcount[$j] . '&nbsp;' . $viewlang;
							// $div                .= '</span>';
							// }
			
							$div .= '</li>';
						}       ##FOR EACH ENDS
						$div .= '</ul>';
						$div .= '</div>';
						$div .= '<div class="clear"></div>';

						if (($dataLimit < $CountOFVideos)) {        ##PAGINATION STARTS
							$div .= '<span class="more_title" ><a class="video-more" href="' . get_site_url() . '/?page_id=' . $this->get_more_pageid() . '&amp;more=' . $morePage .'">' . __('More&nbsp;Videos', 'vh') . '&nbsp;&#187;</a></span>';
							$div .= '<div class="clear"></div>';
						} else if (($dataLimit == $CountOFVideos)) {
							$div .= '<div style="float:right"></div>';
						}       ##PAGINATION ENDS
					}
					else
						$div .=__('No', 'vh') . ' ' . $typename . ' ' . __('Videos', 'vh');
						$div .= '</div>';
				}       ##CHECKING FAETURED VIDEOS ENABLE ENDS
				return $div;
			}
		}

		function categoryList($CountOFVideos, $TypeOFvideos, $pagenum, $dataLimit, $category_page) {
			global $wpdb;
			$div                = '';
			$ratearray = array("nopos1", "onepos1", "twopos1", "threepos1", "fourpos1", "fivepos1");
			$pagenum            = isset($pagenum) ? absint($pagenum) : 1;               ## Calculating page number
			$div                .= '<style scoped> .video-block { margin-left:' . $this->_settingsData->gutterspace . 'px !important;float:left;} </style>';
			foreach ($TypeOFvideos as $catList) {
			## Fetch videos for every category
				$sql            = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare as w
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play as m ON m.media_id = w.vid
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist as p on m.playlist_id = p.pid
								INNER JOIN " . $this->_wpdb->prefix . "posts s ON s.ID=w.slug
								WHERE w.publish='1' and p.is_publish='1' and m.playlist_id=" . intval($catList->pid) . " GROUP BY w.vid LIMIT " . $dataLimit;
				$playLists      = $wpdb->get_results($sql);

				$sql2            = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare as w
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play as m ON m.media_id = w.vid
								INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist as p on m.playlist_id = p.pid
								INNER JOIN " . $this->_wpdb->prefix . "posts s ON s.ID=w.slug
								WHERE w.publish='1' and p.is_publish='1' and m.playlist_id=" . intval($catList->pid) . " GROUP BY w.vid";
				$playLists2      = $wpdb->get_results($sql2);

				$playlistCount  = count($playLists);
				$playlistCount2  = count($playLists2);

				$moreName = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE \"%[video_category]%\" AND post_status=\"publish\" AND post_type=\"page\" limit 1");
				$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=' . $catList->pid;

				if ( is_user_logged_in() ) {
					$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
					if ( $user_followed_categories != '' ) {
						foreach ($user_followed_categories['followed_categories'] as $value) {
							if ( $value != $catList->pid ) {
								$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
							} else {
								$follow_button = '<a href="javascript:void(0)" class="unfollow-category icon-minus-circled">' . __('Unfollow', 'vh') . '</a>';
								break;
							}
						}
					} else {
						$follow_button = '<a href="javascript:void(0)" class="follow-category icon-plus-circled">' . __('Follow', 'vh') . '</a>';
					}
				} else {
					$follow_button = '<a href="'.wp_login_url().'" class="follow-category-register icon-plus-circled">' . __('Follow', 'vh') . '</a>';
				}

				$div .= '<div><h4 class="clear more_title">' . $catList->playlist_name . '<span class="title-video-count"><a href="' . $more_playlist_link . '" class="video-category-count">' . $playlistCount2 . __(' videos', 'vh') . '</a></span>' . $follow_button . '<input type="hidden" value="' . $catList->pid . '"><div class="followed_categories"></div></h4></div>';
				
				$category_width  = 100/intval($this->_settingsData->colCat);

				if (!empty($playlistCount)) {
					$inc        = 1;
					$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
					$div        .= '<div class="video_thumb_content"><style type="text/css">.wrapper .video-block { width: '.$category_width.'% !important; }</style>
<ul class="video-block-container video-home">';
					foreach ($playLists as $playList) {

						$duration   = $playList->duration;
						$imageFea   = $playList->image;         ## VIDEO IMAGE
						$imageFea  = str_replace("/mq","/sd",$imageFea);
							$image_header = get_headers_curl($imageFea);
							if ( mb_substr($image_header, 9, 3) == '404' ) {
								$imageFea  = $playList->image;
								$video_sd_image = 'mqdef';
							} else {
								$imageFea  = str_replace("/mq","/sd",$playList->image);
								$video_sd_image = 'sdimg';
							}
						$file_type  = $playList->file_type;     ## Video Type
						$guid       = vh_get_video_permalink($playList->slug);          ## guid - url for video detail page
						if ($imageFea == '') {                  ## If there is no thumb image for video
							$imageFea = $this->_imagePath . 'nothumbimage.jpg';
						} else {
							if ($file_type == 2 || $file_type == 5) {              ##For uploaded image
								$imageFea = $image_path . $imageFea;
							}
						}
						## Video page category video title length ##
						if (strlen($playList->name) > 30) {
							$playListName = mb_substr($playList->name, 0, 30) . "..";
						} else {
							$playListName = $playList->name;
						}

						$div .= '<li class="video-block"><div class="video-thumbimg">';
						$div .= '<div class="video_home_info">';
							if ($this->_settingsData->view_visible == 1) {
								$div .='<span class="views icon-eye">' . $playList->hitcount . '</span>';
							}
							$tc = wp_count_comments($playList->slug);
							$div .='<span class="comments icon-comment">' . $tc->total_comments . '</span>';
							if ( function_exists('get_post_ul_meta') ) {
								$div .='<span class="likes icon-heart">' . get_post_ul_meta($playList->slug, "like") . '</span>';
							}
							$div                 .= '</div>
							<div class="video_image_container '.$video_sd_image.'">
								<a href="javascript:void(0);" class="video_play"></a>
								<a href="' . $guid . '" class="view_more"></a>
								<img src="' . $imageFea . '" alt="' . $playList->name . '" class="imgHome" title="' . $playList->name . '" />
								<div id="video_dialog" title="' . $playList->name . '">';
								if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
									$embed_code = '<embed src="' . plugins_url() . '/contus-video-gallery/hdflvplayer/hdplayer.swf" id="vh-embed-code" flashvars="baserefW=' . get_option('siteurl') . '&amp;mtype=playerModule&amp;vid=92" width="100%" height="444px" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
									if( $file_type == 5 && !empty($embed_code) ) {
										$player_values = stripslashes($embed_code);
									 } else {
										 $mobile = vgallery_detect_mobile();
										if($mobile === true){
											## Check for youtube video
											if (preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $playList->file, $vresult)) {
												$urlArray = explode("=", $vresult[0]);
												$video_id = trim($urlArray[1]);
												$reavideourl = "http://www.youtube.com/embed/$video_id";
												## Generate youtube embed code for html5 player
												$player_values = htmlentities('<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>');
											} else if ($file_type != 5) {        ## Check for upload, URL and RTMP videos
												if ($file_type == 4) {           ## For RTMP videos
													$streamer = str_replace("rtmp://", "http://", $media->streamer_path);
													$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
												}
												## Generate video code for html5 player
												$player_values = htmlentities('<video id="video" poster="' . $imageFea . '"   src="' . $reavideourl .'" autobuffer controls preload="metadata">' . __( 'Html5 Not support This video Format.', 'vh' ) . '</video>');
											}
										} else {
											## Flash player code
											$player_values = '<embed src="' . plugins_url() . '/contus-video-gallery/hdflvplayer/hdplayer.swf" flashvars="baserefW=' . get_option('siteurl') . '&amp;mtype=playerModule&amp;vid=' . $playList->vid . '" width="100%" height="444px" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
										}
									}
									if ( strpos($playList->file,'soundcloud.com') !== false ) {
										$div .= '<input type="hidden" class="iframe_url" value="' . $playList->file . '" />';
										$div .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
									} else {
										$div .= $player_values;
									}
								} else {
									if( strpos($playList->file, 'v=') !== false ) {
										$video_link = explode('v=', $playList->file);
										if ( strpos($playList->link, 'rel=') !== false ) {
											$video_rel = explode('?', $playList->link);
											$div .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '?' . $video_rel[1] . '" />';
										} else {
											$div .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
										}
										$div .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
									} elseif ( strpos($playList->file, '/v/') !== false ) {
										$video_link = explode('/v/', $playList->file);
										$div .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
										$div .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
									} elseif ( strpos($playList->file, '/video/') !== false ) {
										$video_link = explode('/video/', $playList->file);
										$video_link = explode('_', $video_link[1]);
										$div .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
										$div .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
									} else {

										// if ( $file_type == 3 ) {
										// 	$videourl   = $files[$j];
										// 	$image_file = $imageFea[$j];
										// } else {
										// 	$videourl   = $image_path . $files[$j];
										// 	$image_file = $imageFea[$j];
										// }

										// $div .= do_shortcode('[video poster="' . $image_file . '" width="642" height="444" src="' . $videourl . '"]');
									}
								}
								$div .= '</div>
							</div>';
						// if ($duration != 0.00) {
						// 	$div    .= '<span class="video_duration">' . $duration . '</span>';
						// }
						$div        .= '</div><div class="vid_info"><a href="' . $guid . '" class="videoHname"><span>' . $playListName . '</span></a>';
						## Rating starts here
						if ($this->_settingsData->ratingscontrol == 1) {
								if (isset($playList->ratecount) && $playList->ratecount != 0) {
									$ratestar    = round($playList->rate / $playList->ratecount);
								} else {
									$ratestar    = 0;
								}
								$div             .= '<span class="ratethis1 '.$ratearray[$ratestar].'"></span>';
							}
						## Rating ends and views starts here
						// if ($this->_settingsData->view_visible == 1) {
						// if ($playList->hitcount > 1)
						// 	$viewlang = $this->_viewslang;
						// else
						// 	$viewlang = $this->_viewlang;

						// $div         .= '<span class="video_views">' . $playList->hitcount . '&nbsp;' . $viewlang . '</span>';
						// }
							
						$div         .= '</div></li>';

						// if (($inc % $this->_colCat ) == 0 && $inc != 0) {##COLUMN COUNT
						// 	$div     .= '</ul><div class="clear"></div><ul class="video-block-container">';
						// }
						// $inc++;
					}
					$div             .= '</ul></div>';

					if (($playlistCount2 > 9)) {
						// $more_playlist_link = get_playlist_permalink($this->_mPageid,$catList->pid,$catList->playlist_slugname);
						$div .= '<span class="more_title" ><a class="video-more" href="' . $more_playlist_link .'">' . __('More&nbsp;Videos', 'vh') . '</a></span>';
					} else {
						$div         .= '<div align="right"> </div>';
					}
				} else {            ## If there is no video for category
					$div             .='<div>' . __('No Videos for this Category', 'vh') . '</div>';
				}
			}

			$div                     .='<div class="clear"></div>';

			if ( $category_page != 0 ) {
																								// PAGINATION STARTS
				$total = $CountOFVideos;
				$num_of_pages = ceil( $total / $category_page );
				if ( $pagenum == 0 ) {
					$pagenum = 1;
				}
				$page_links   = paginate_links(
						array(
							'base' => esc_url( add_query_arg( 'pagenum', '%#%' ) ),
							'format' => '',
							'prev_text' => __( '&laquo;', 'aag' ),
							'next_text' => __( '&raquo;', 'aag' ),
							'total' => $num_of_pages,
							'current' => $pagenum
							)
						);

				if ( $page_links ) {
					$div .= '<div class="contus_tablenav"><div class="contus_tablenav-pages" >' . $page_links . '</div></div>';
				}
																								// PAGINATION ENDS
			}
			return $div;
		}
##CATEGORY FUNCTION ENDS
	}
}

function vh_video_homereplace() {
	global $frontControllerPath;
	include_once ($frontControllerPath . 'videohomeController.php');
	$pageOBJ = new ContusVideoViewEdited();
	$contentPlayer = $pageOBJ->home_player();
	$contentPopular = $pageOBJ->home_thumb('popular');
	$contentRecent = $pageOBJ->home_thumb('recent');
	$contentFeatured = $pageOBJ->home_thumb('featured');
	$contentCategories = $pageOBJ->home_thumb('cat');
	return $contentPlayer . $contentPopular . $contentRecent . $contentFeatured . $contentCategories;
}

add_shortcode('video_home', 'vh_video_homereplace');

// Followed video module
function vh_followed_video_module( $atts ) {
	extract( shortcode_atts( array(
				'video_title' => '',
				'video_count' => '',
				'video_style' => '',
				'excluded_video_count' => ''
			), $atts ) );
			global $wpdb, $dirPage;

			$vh_swfPath  = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';
			$user_followed_videos = $followed_videos_sorted = '';
			$user_followed_video_count = 0;
			$user_followed_new_video_count = 0;

			if ( $video_count == null ) {
				$vh_video_count = '';
			} else {
				$vh_video_count = ' LIMIT ' . $video_count;
			}
			
			if ( $video_style == null ) {
				$vh_video_style = ' default';
			} elseif ( $video_style == 'video_thumb' ) {
				$vh_video_style = ' vid_thumbnail';
			} elseif ( $video_style == 'video_list' ) {
				$vh_video_style = ' vid_list';
			}

			$videoId         = 99999999;
			$pluginflashvars = "baserefW=" . get_option('siteurl');
			$width           = '100%';
			$height          = '444px';

			$output = '';

			$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);

			if ( $user_followed_categories != '' && vh_count_followed_videos( $user_followed_categories ) > 0 ) {
			foreach ( $user_followed_categories['videos'] as $value ) {
				foreach ( $value as $video ) {
					$user_followed_videos .= $video['video_id'].',';
					$user_followed_videos_arr[$video['video_id']] = $video['watched'];
					$user_followed_video_count++;
				}	
			}

			foreach ( $user_followed_categories['videos'] as $value ) {
				foreach ( $value as $video ) {
					if ( $video['watched'] == '0' ) {
						$user_followed_new_video_count++;
					}
				}
			}

			$user_followed_videos = rtrim($user_followed_videos, ',');

			asort($user_followed_videos_arr);

			if ( $excluded_video_count != '' && $excluded_video_count != '0' ) {
				$i = 0;
				foreach ($user_followed_videos_arr as $key => $value) {
					$i++;
					if ( $i <= $excluded_video_count ) {
						continue;
					}
					$followed_videos_sorted .= $key.',';
				}
			} else {
				foreach ($user_followed_videos_arr as $key => $value) {
					$followed_videos_sorted .= $key.',';
				}
			}

			$followed_videos_sorted = rtrim($followed_videos_sorted, ',');

			## Display videos starts here
			$select = "SELECT distinct(a.vid),b.playlist_id,name,guid,description,file,hdfile,file_type,duration,embedcode,image,opimage,download,link,featured,hitcount,slug,
							a.post_date,postrollads,prerollads FROM " . $wpdb->prefix . "hdflvvideoshare a
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play b ON a.vid=b.media_id
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=b.playlist_id
							INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=a.slug
							WHERE a.vid IN(" . $followed_videos_sorted . ") AND a.publish='1' AND p.is_publish='1'
							GROUP BY a.vid
							ORDER BY FIND_IN_SET(a.vid, '" . $followed_videos_sorted . "')".$vh_video_count;

			$output = '';

			if ( $followed_videos_sorted != '' ) {
				$output .= '<div class="video_player'.$vh_video_style.'">';
					if ( $video_title != null ) {
						global $wpdb;

						$playlist_id = '';

						$moreName = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE \"%[video_category]%\" AND post_status=\"publish\" AND post_type=\"page\" limit 1");
						$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=-1&followed=true';

						$output .= '<h2 class="video-module-title"><span class="followed_title">' . $video_title . '</span><span class="title-video-count followed"><a href="' . $more_playlist_link . '" class="video-category-count">' . $user_followed_video_count . __(' videos', 'vh') . '</a><span> (' . $user_followed_new_video_count . ' ' . __('new', 'vh') . ')</span></span></h2>';
					}

					$wpdb->get_results( $select );
					$result = $wpdb->num_rows;

					if ($result != '') {
						## Slide Display Here
						$output .= '<ul class="video_module">';
						$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);

						foreach ($wpdb->get_results($select) as $relFet) {
							$file_type = $relFet->file_type; ## Video Type
							$imageFea  = $relFet->image; ##VIDEO IMAGE
							$imageFea  = str_replace("/mq","/sd",$imageFea);
							$author_id = get_post_field( 'post_author', $relFet->slug );
							
							if ( $video_style != 'video_thumb' && $video_style != 'video_list' ) {
								$image_header = get_headers_curl($imageFea);
								if ( mb_substr($image_header, 9, 3) == '404' ) {
									$imageFea  = $relFet->image;
									$video_sd_image = 'mqdef';
								} else {
									$imageFea  = str_replace("/mq","/sd",$relFet->image);
									$video_sd_image = 'sdimg';
								}
							} else {
								$imageFea  = $relFet->image;
								$video_sd_image = 'mqdef';
							}
							$reafile = $relFet->file; ##VIDEO IMAGE
							$guid    = vh_get_video_permalink($relFet->slug); ##guid
							
							if ( $imageFea == '' ) {  ##If there is no thumb image for video
								$imageFea = APPTHA_VGALLERY_BASEURL . 'images' . DS . 'nothumbimage.jpg';
								$video_sd_image .= ' noimage';
							} else {
								if ( $file_type == 2 || $file_type == 5 ) {          ##For uploaded image
									$imageFea = $image_path . $imageFea;
								} else {
									if ( strpos($imageFea,'sddefault') !== false ) {
										$imageFea = vh_imgresize($imageFea, 463, 346, $relFet->slug);
									} elseif ( strpos($imageFea,'mqdefault') !== false ) {
										$imageFea = vh_imgresize($imageFea, 152, 98, $relFet->slug);
									}
								}
							}
							## Embed player code
							if($file_type == 5 && !empty($relFet->embedcode)){
								$player_values   = stripslashes($relFet->embedcode);
							 } else {
								 $mobile = vgallery_detect_mobile();
								if( $mobile === true ) {

									## Check for youtube video
									if (preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $reafile, $vresult)) {
										$urlArray    = explode("=", $vresult[0]);
										$video_id    = trim($urlArray[1]);
										$reavideourl = "http://www.youtube.com/embed/$video_id";

										## Generate youtube embed code for html5 player
										$player_values = '<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>';
									} else if ($file_type != 5) { ## Check for upload, URL and RTMP videos
										if ($file_type == 2) { ## For uploaded image
											$reavideourl = $image_path . $reafile;
										} else if ($file_type == 4) {           ## For RTMP videos
											$streamer    = str_replace("rtmp://", "http://", $media->streamer_path);
											$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
										}
										## Generate video code for html5 player
										$player_values = '<video id="video" poster="' . $imageFea . '"   src="' . $reavideourl .'" autobuffer controls preload="metadata">' . __( 'Html5 Not support This video Format.', 'vh' ) . '</video>';
									}
								} else {
									## Flash player code
									$player_values = '<embed src="' . $vh_swfPath . '" flashvars="' . $pluginflashvars . '&amp;mtype=playerModule&amp;vid='.$relFet->vid.'" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
								}
							}
							if (get_post_type() == 'videogallery' || get_post_type() === 'videogallery') {
								$thumb_href = 'href="'. $guid.'"';
							} else {
								$player_div = 'mediaspace';
								$videodivId = rand();
								if (isset($arguments['id'])) {
									$videodivId .= $arguments['id']; ## get video id from short code
									$vid = $arguments['id'];
								}
								$thumb_href = 'href="'.$guid.'"';
							}
							if (strlen($relFet->name) > 25) { ## Displaying Video Title
								$t_videoname = mb_substr($relFet->name, 0, 25) . '..';
							} else {
								$t_videoname = $relFet->name;
							}
							$output .='<li><div class="video_container"><div class="imgSidethumb">';
							if ( $video_style == 'video_thumb' ) {
								$output .= '<div class="video_thumb_info">'.$t_videoname.'</div>
												<div class="video_thumb_description">
													<div class="video_info">
													<p>' . mb_substr($relFet->description, 0, 90) . '..</p>';

								$output .= '<div class="video_views icon-eye">'. $relFet->hitcount . '</div>';
								$tc      = wp_count_comments($relFet->slug);
								$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';

								if ( function_exists('get_post_ul_meta') ) {
									$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($relFet->slug, "like") . '</div>'; 
								}

								$output .= '</div>
								</div>';
							} elseif ( $video_style == '' ) {
								$output .= '<div class="video_info">';
											if ($relFet->duration != 0.00) {
												$output .= '<div class="video-duration micon-clock">' . $relFet->duration . '</div>';
											}
										$output .= '<div class="video_views icon-eye">'. $relFet->hitcount . '</div>';
								$tc      = wp_count_comments($relFet->slug);
								$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
								if ( function_exists('get_post_ul_meta') ) {
									$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($relFet->slug, "like") . '</div>'; 
								}
								$output .= '</div>';
							} elseif ( $video_style == 'video_list' ) {
								//$output .= '<div class="video-duration micon-clock">' . $relFet->duration . '</div>';
							}

							$new_video = '';
							$user_followed_categories = json_decode(get_user_meta(get_current_user_id(), 'followed_video_categories', true), true);
							if ( $user_followed_categories != '' ) {
								foreach ( $user_followed_categories['videos'] as $value ) {
									foreach ( $value as $video ) {
										if ( $video['video_id'] == $relFet->vid ) {
											if ( $video['watched'] == '0' ) {
												$new_video .= '<span class="new-video">' . __('New', 'vh') . '</span>';
											}
										}
									}	
								}
							}
							## FOLLOWED VIDEO IFRAME ##
							$output .= '<div class="video_image_container '.$video_sd_image.'"><div class="video_hidden_wrapper">
											<a href="javascript:void(0);" class="video_play"></a>
											<a ' . $thumb_href . ' class="view_more"></a>
											<img src="' . $imageFea . '" alt="' . $relFet->name . '" class="related '.$video_sd_image.'" />
											' . $new_video . '
											<div id="video_dialog" title="' . $relFet->name . '">';
											if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
												if ( strpos($relFet->file,'soundcloud.com') !== false ) {
													$output .= '<input type="hidden" class="iframe_url" value="' . $relFet->file . '" />';
													$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
												} else {
													$output .= $player_values;
												}
											} else {
												if( strpos($relFet->file, 'v=') !== false ) {
													$video_link = explode('v=', $relFet->file);
													if ( strpos($relFet->link, 'rel=') !== false ) {
														$video_rel = explode('?', $relFet->link);
														$output .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '?' . $video_rel[1] . '" />';
													} else {
														$output .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
													}
													$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
												} elseif ( strpos($relFet->file, '/v/') !== false ) {
													$video_link = explode('/v/', $relFet->file);
													$output .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
													$output .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
												} elseif ( strpos($relFet->file, '/video/') !== false ) {
													$video_link = explode('/video/', $relFet->file);
													$video_link = explode('_', $video_link[1]);
													$output .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
													$output .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
												} else {

													if ( $file_type == 3 ) {
														$videourl   = $relFet->file;
														$image_file = $relFet->image;
													} elseif ( $file_type == 2 ) {
														$videourl   = $image_path . $relFet->file;

														$output .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
													} else {
														$videourl   = $image_path . $relFet->file;
														$image_file = $image_path . $relFet->image;
													}

													if( $file_type == 5 && !empty($relFet->embedcode) ) {
														$output .= stripslashes($relFet->embedcode);
													}

													// $output .= do_shortcode('[video poster="' . $image_file . '" width="640" height="409" src="' . $videourl . '"]');
												}
											}
											$output .= '</div></div>
										</div>
									</div>';
							$output .='<div class="vid_info"><span><a ' . $thumb_href . ' class="videoHname">';
							## Followed video module title length ##
							if ( strlen($relFet->name) > 30 ) { ## Displaying Video Title
								$videoname = mb_substr($relFet->name, 0, 30) . '..';
							} else {
								$videoname = $relFet->name;
							}
							$output .= $videoname;
							$output .='</a></span>';
							if ( $video_style == 'video_list' ) {
								$output .= '<div class="video_info">';
											if ($relFet->duration != 0.00) {
												$output .= '<div class="video-duration micon-clock">' . $relFet->duration . '</div>';
											}
										$output .= '<div class="video_views icon-eye">'. $relFet->hitcount . '</div>';
								$tc      = wp_count_comments($relFet->slug);
								$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
								if ( function_exists('get_post_ul_meta') ) {
									$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($relFet->slug, "like") . '</div>';
								} 
								$output .= '</div><div class="clearfix"></div>';
							}
							$description_length = 170;
							if ( $vh_video_style == ' vid_list' ) {
								$description_length = 70; 
							} elseif ( $vh_video_style == ' default' ) {
								$description_length = 170;
							} else {
								$description_length = 170;
							}

							if (strlen($relFet->description) > $description_length) { ## Displaying Video Description
								$videodescription = mb_substr($relFet->description, 0, $description_length) . '..';
							} else {
								$videodescription = $relFet->description;
							}
							$output .= '<div class="video_desc">' . $videodescription .  '</div>';
							if ( $video_style == 'video_list' || $video_style == NULL ) {
								$output .= '<div class="video_author icon-user">' . get_the_author_meta('display_name', $author_id) .  '</div>';
								$output .= '<div class="video_date icon-calendar">' . human_time_diff(get_the_time('U',$relFet->slug),current_time('timestamp')) .  ' ago</div>';
							}
							$output .='</div></div></li>';
						}

						if ( $video_style == 'video_thumb' ) {
							global $wpdb;
							$moreName = $wpdb->get_var("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE \"%[video_category]%\" AND post_status=\"publish\" AND post_type=\"page\" limit 1");
							$more_playlist_link = get_site_url() . '/?page_id=' . $moreName . '&amp;playid=-1&amp;followed=true';

							$more_videos_count = $user_followed_video_count - intval($video_count);

							if ( $user_followed_video_count-intval($video_count) > 0 ) {
								$output .= '<li><a href="' . $more_playlist_link . '" class="video_thumb_link"><span class="video_thumb_more">+</span>' . $more_videos_count . __(' more', 'vh') . '</a></li>';
							}
						}
						
						$output .= '</ul>';
					}  
					$output .= '</div>';
				}
			} else {
				$output .= '
				<div class="video_player followed ' . $vh_video_style . '">
					<h2 class="video-module-title">
						<span class="followed_title">' . __('Followed', 'vh') . '</span>
					</h2>
					<div class="no-followed-videos"><p>' . __('Currently you are not following any category.', 'vh') . '</p></div>
				</div>';
			}

			
		
	return $output;
}
add_shortcode('followed-video-module', 'vh_followed_video_module');

function vh_vcgi_image( $atts ) {
	$output = '{{vh_post_image_module}}';
	return $output;
}
add_shortcode( 'vh_vcgi_image', 'vh_vcgi_image' );

function vh_vcgi_topmeta( $atts ) {
	$output = '{{vh_post_topmeta_module}}';
	return $output;
}
add_shortcode( 'vh_vcgi_topmeta', 'vh_vcgi_topmeta' );

function vh_vcgi_bottom( $atts ) {
	$output = '{{vh_post_bottom_module}}';
	return $output;
}
add_shortcode( 'vh_vcgi_bottom', 'vh_vcgi_bottom' );