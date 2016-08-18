<?php 
global $dirPage, $frontControllerPath, $wpdb, $dirPage;
if (function_exists('get_playlist_id')) {
	include_once ($frontControllerPath . 'videoshortcodeController.php');
	include_once ($frontControllerPath . 'videomoreController.php');
	include_once WP_PLUGIN_DIR.'/snaptube-plugin/lib/functions/contusFunctions.php';
}

function vh_videogallery_jcar_js_css() {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'videogallery_jcar_js', APPTHA_VGALLERY_BASEURL . 'js/jquery.jcarousel.pack.js' );
		wp_enqueue_script( 'videogallery_jcar_js' );
		wp_register_style( 'videogallery_jcar_css', APPTHA_VGALLERY_BASEURL . 'css/jquery.jcarousel.css' );
		wp_enqueue_style( 'videogallery_jcar_css' );
		wp_register_style( 'videogallery_jcar_skin_css', APPTHA_VGALLERY_BASEURL . 'css/skins.min.css' );
		wp_enqueue_style( 'videogallery_jcar_skin_css' );
		// Jquery ui add for tooltip
		wp_register_script( 'videogallery_jquery-ui_js', APPTHA_VGALLERY_BASEURL . 'js/jquery-ui.js' );
		wp_enqueue_script( 'videogallery_jquery-ui_js' );
		wp_register_style( 'videogallery_jquery_ui_css', APPTHA_VGALLERY_BASEURL . 'css/jquery-ui.min.css' );
		wp_enqueue_style( 'videogallery_jquery_ui_css' );
		wp_register_script( 'videogallery_jcar_init_js', APPTHA_VGALLERY_BASEURL . 'js/mycarousel.js' );
		wp_enqueue_script( 'videogallery_jcar_init_js' );
}

function vh_video_shortcodeplace( $arguments = array() ) {
		global $frontControllerPath;
		vh_videogallery_jcar_js_css();
		include_once( $frontControllerPath . 'videoshortcodeController.php' );
		$pageOBJ       = new ContusVideoShortcodeViewEdited();
		$contentPlayer = $pageOBJ->hdflv_sharerender( $arguments );
		return $contentPlayer;
}
add_shortcode('hdvideo', 'vh_video_shortcodeplace');

class ContusVideoShortcodeViewEdited extends ContusVideoShortcodeController {
	public $_settingsData;
	public $_videosData;
	public $_swfPath;
	public $_singlevideoData;
	public $_videoDetail;
	public $_vId;
	public $_userEmail;
	public $_reportsend;
	public function __construct() {
		parent::__construct ();
		/** Get current video id 
		 * and report send response  */
		$this->_vId             = absint ( filter_input ( INPUT_GET, 'vid' ) );
		$this->_reportsent      = filter_input ( INPUT_POST, 'report_send' );
		
		/** Get post type, page post type 
		 * and  video more page id */
		$this->_post_type       = filter_input ( INPUT_GET, 'post_type' );
		$this->_page_post_type  = get_post_type( get_the_ID () );
		$this->_mPageid         = morePageID ();
		
		/** Get plugin site URL, 
		 * plugin images directory URL, 
		 * upload directory URL 
		 * and swf file URL   */
		$this->_site_url        = get_site_url ();
		$this->_imagePath       = getImagesDirURL ();
		$this->_uploadPath      = getUploadDirURL ();
		$this->_swfPath         = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';
		
		/** Function to check
		 * whether the SSL is enabled in site */
		$this->_protocolURL     = getPluginProtocol();
		/** Object for home page controller */
		$this->_contOBJ         = new ContusVideoController ();
		
	}
	public function url_to_custompostid($url) { 
		global $wp_rewrite;
		$url = apply_filters ('url_to_postid', $url );
		if (preg_match ( '#[?&]( p|page_id|attachment_id )=( \d+ )#', $url, $values )) {
			$id = absint ( $values [2]);
			if ($id)
				return $id;
		}
		
		$rewrite = $wp_rewrite->wp_rewrite_rules ();
		
		if (empty ( $rewrite ))
			return 0;
		$url_split = explode ( '#', $url );
		$url = $url_split [0];
		
		// Get rid of URL ?query=string
		$url_split = explode ( '?', $url );
		$url = $url_split [0];
		
		// Add 'www.' if it is absent and should be there
		if (false !== strpos ( home_url (), '://www.' ) && false === strpos ( $url, '://www.' ))
			$url = str_replace ( '://', '://www.', $url );
			
			// Strip 'www.' if it is present and shouldn't be
		if (false === strpos ( home_url (), '://www.' ))
			$url = str_replace ( '://www.', '://', $url );
			
			// Strip 'index.php/' if we're not using path info permalinks
		if (! $wp_rewrite->using_index_permalinks ())
			$url = str_replace ( 'index.php/', '', $url );
		
		if (false !== strpos ( $url, home_url () )) {
			// Chop off http://domain.com
			$url = str_replace ( home_url (), '', $url );
		} else {
			// Chop off /path/to/blog
			$home_path = parse_url ( home_url () );
			$home_path = isset ( $home_path ['path'] ) ? $home_path ['path'] : '';
			$url = str_replace ( $home_path, '', $url );
		}
		
		// Trim leading and lagging slashes
		$url = trim ( $url, '/' );
		
		$request = $url;
		
		// Look for matches.
		$request_match = $request;
		foreach ( ( array ) $rewrite as $match => $query ) {
			
			// If the requesting file is the anchor of the match, prepend it
			// to the path info.
			if (! empty ( $url ) && ($url != $request) && (strpos ( $match, $url ) === 0))
				$request_match = $url . '/' . $request;
			
			if (preg_match ( "!^$match!", $request_match, $matches )) {
				
				if ($wp_rewrite->use_verbose_page_rules && preg_match ( '/pagename=\$matches\[( [0-9]+ )\]/', $query, $varmatch )) {
					// this is a verbose page match, lets check to be sure about it
					if (get_page_by_path ( $matches [$varmatch [1]] ))
						continue;
				}
				
				// Got a match.
				// Trim the query of everything up to the '?'.
				$query = preg_replace ( '!^.+\?!', '', $query );
				
				// Substitute the substring matches into the query.
				$query = addslashes ( WP_MatchesMapRegex::apply ( $query, $matches ) );
				
				// Filter out non-public query vars
				global $wp;
				global $wpdb;
				parse_str ( $query, $query_vars );
				
				$query = array ();
				foreach ( ( array ) $query_vars as $key => $value ) {
					
					if (in_array ( $key, $wp->public_query_vars )) {
						$query [$key] = $value;
					}
				}
				$post_type = '';
				// Do the query
				if (! empty ( $query ['videogallery'] ))
					$post_type = 'videogallery';
				return $post_type;
			}
		}
		return 0;
	}

	function displayGoogleAdsense ($width, $vid, $videoid) {
		$ropen = 0;
		/** Get height & width for google adsense */
		if ($width > 468) {
		  $adstyle = "margin-left: -234px;";
		} else {
		  $margin_left = ($width - 100) / 2;
		  $adwidth = ($width - 100);
		  $adstyle = "width:" . $adwidth . "px;margin-left: -" . $margin_left . "px;";
		}
		 
		/** Display google adsense */
		$output   = '<div id="lightm"  style="' . $adstyle . 'height:76px;position:absolute;display:none;background:none !important;background-position: initial initial !important;background-repeat: initial initial !important;bottom: 60px;left: 50%;">
							<span id="divimgm" >
							  <img alt="close" id="closeimgm" src="' . APPTHA_VGALLERY_BASEURL . '/images/close.png" style="z-index: 10000000;width:48px;height:12px;cursor:pointer;top:-12px;" onclick="googleclose();"  />
							</span>
							<iframe  height="60" width="' . ($width - 100) . '" scrolling="no" align="middle" id="IFrameName" src="" name="IFrameName" marginheight="0" marginwidth="0" class="iframe_frameborder" ></iframe>
							</div>
							</div>';
		/** Get google adsense details for this video */
		$details  = $this->get_video_google_adsense_details ( $vid );
		$details1 = unserialize ( $details->googleadsense_details );
		/** Get close ad seconds */
		$closeadd = $details1 ['adsenseshow_time'];
		if (isset ( $details1 ['adsense_option'] ) && $details1 ['adsense_option'] == 'always_show') {
		  $closeadd = 0;
		}
		/** Get adsense reopen seconds */
		if (isset ( $details1 ['adsense_reopen'] ) && $details1 ['adsense_reopen'] == '1') {
		  $ropen  = $details1 ['adsense_reopen_time'];
		}
		/** Assign close ad , reopen values in script variable */
		$output   .= '<script type="text/javascript">
							  var pagepath  = "' . $this->_site_url . '/wp-admin/admin-ajax.php?action=googleadsense&vid=' . $videoid . '";
							  var closeadd = ' . $closeadd * 1000 . ';
							  var ropen = ' . $ropen * 1000 . ';
							</script> <script src="' . APPTHA_VGALLERY_BASEURL . 'js/googlead.js" type="text/javascript"></script>';

		return $output;
	  }
	
	/**
	 *  Display player details
	 */
	function hdflv_sharerender($arguments = array()) {
		global $wpdb;
		$output = $videourl = $imgurl = $player_div = $vid = $playlistid = $homeplayerData = $reavideourl = $ratecount = $rate = $plugin_css = $no_views = '';
		$video_playlist_id = $videoId = $hitcount = $show_posted_by = $show_social_icon= $show_related_video = 0;
		
		$videodivId = rand ();
		$image_path = str_replace ( 'plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL );
		$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
		$configXML = $wpdb->get_row ( 'SELECT playlist,ratingscontrol,view_visible,tagdisplay,categorydisplay,embed_visible,keydisqusApps,comment_option,keyApps,configXML,width,height,showTag,player_colors FROM ' . $wpdb->prefix . 'hdflvvideoshare_settings' );
		$flashvars = $pluginflashvars = 'baserefW=' . get_option ( 'siteurl' ); // generate flashvars detail for player starts here
		
		if (isset ( $arguments ['width'] )) {
			$width = $arguments ['width']; // get width from short code
		} else {
			$width = $configXML->width; // get width from settings
		}
		if (isset ( $arguments ['height'] )) {
			$height = $arguments ['height']; // get height from short code
		} else {
			$height = $configXML->height; // get height from settings
		}
		$showTags = $configXML->tagdisplay;
		$showRatting = $configXML->ratingscontrol;
		$player_color = unserialize($configXML->player_colors);
		$show_posted_by = $player_color['show_posted_by'];
		$show_social_icon = $player_color['show_social_icon'];
		$show_rss_icon   = $player_color['show_rss_icon'];
		$number_related_video = $player_color['related_video_count'];
		 if ( isset( $player_color['show_added_on'] ) ) {  $show_added_on = $player_color['show_added_on'];  } else { $show_added_on = '0';  }
		//Send report for  video
		if (isset ( $arguments ['id'] )) {
			$videodivId .= $arguments ['id']; // get video id from short code
			$vid = $arguments ['id'];
		}
		if (! empty ( $vid )) {
			$homeplayerData = $this->short_video_detail ( $vid, $number_related_video );
			$fetched [] = $homeplayerData;
		}
		// store video details in variables
		if (! empty ( $homeplayerData )) {
			$videoUrl = $homeplayerData->file;
			$videoId = $homeplayerData->vid;
			$video_title = $homeplayerData->name;
			$video_file_type = $homeplayerData->file_type;
			$video_thumb = $homeplayerData->image;
			if ($video_file_type == 2 || $video_file_type == 5) {
				if(strpos($video_thumb,'/' )){
					$video_thumb = $homeplayerData->image;
				}else{
					$video_thumb = $image_path . $homeplayerData->image;
				}
			}
			$video_playlist_id = $homeplayerData->playlist_id;
			$description = $homeplayerData->description;
			$tag_name = $homeplayerData->tags_name;
			$hitcount = $homeplayerData->hitcount;
			$uploadedby = $homeplayerData->display_name;
			$uploadedby_id = $homeplayerData->ID;
			$ratecount = $homeplayerData->ratecount;
			$rate = $homeplayerData->rate;
			$post_date = $homeplayerData->post_date;
		}
		// get Playlist detail
		$playlistData = $this->playlist_detail ($vid , $number_related_video );
		$incre = 0;
		$playlistname = $windo = $htmlvideo = '';
		
		if (isset ( $arguments ['playlistid'] )) {
			$videodivId .= $arguments ['playlistid']; // get playlist id from short code
			$playlistid = $arguments ['playlistid'];
			$flashvars .= '&amp;mtype=playerModule';
		}
		
		// generate flashvars detail for player starts here
		if (! empty ( $playlistid ) && ! empty ( $vid )) {
			$flashvars .= '&amp;pid=' . $playlistid;
			$flashvars .= '&amp;vid=' . $vid;
		} elseif (! empty ( $playlistid )) {
			$flashvars .= '&amp;pid=' . $playlistid . '&showPlaylist=true';
			$playlist_videos = $this->_contOBJ->video_pid_detail ( $playlistid, 'detailpage' , $number_related_video);
			if (! empty ( $playlist_videos )) {
				$videoId = $playlist_videos [0]->vid;
				$video_playlist_id = $playlist_videos [0]->playlist_id;
				$hitcount = $playlist_videos [0]->hitcount;
				$uploadedby = $playlist_videos [0]->display_name;
				$uploadedby_id = $playlist_videos [0]->ID;
				$ratecount = $playlist_videos [0]->ratecount;
				$rate = $playlist_videos [0]->rate;
				$fetched [] = $playlist_videos [0];
			}
		} else if ($this->_post_type !== 'videogallery' && $this->_page_post_type !== 'videogallery') {
			$flashvars .= '&amp;vid=' . $vid . '&showPlaylist=false';
		} else {
			$flashvars .= '&amp;vid=' . $vid;
		}
		if (isset ( $arguments ['flashvars'] )) {
			$flashvars .= '&amp;' . $arguments ['flashvars'];
		}
		if (! isset ( $arguments ['playlistid'] ) && isset ( $arguments ['id'] ) && $this->_post_type !== 'videogallery' && $this->_page_post_type !== 'videogallery') {
			$flashvars .= '&amp;playlist_autoplay=false&amp;playlist_auto=false';
		}
		// generate flashvars detail for player ends here
		
		$player_not_support = __ ( 'Player doesnot support this video.', 'video_gallery' );
		$htmlplayer_not_support = __ ( 'Html5 Not support This video Format.', 'video_gallery' );
		
		$output .= ' <script>
					var baseurl,folder,videoPage;
					baseurl = "' . $this->_site_url . '";
					folder  = "contus-video-gallery";
					videoPage = "' . $this->_mPageid . '"; 
					</script>';
		if (isset ( $arguments ['title'] ) && $arguments ['title'] == 'on') {
			$output .= '<h2 id="video_title' . $videodivId . '" class="videoplayer_title" ></h2>';
			$pluginflashvars .= $flashvars .= '&amp;videodata=current_video_' . $videodivId;
		}

		$current_url = get_permalink();

		$blog_title  = get_bloginfo('name');

		$output .= '<div class="video-socialshare-container">
			<span class="video_share_text">' . __('Share this video:', 'vh') . '</span>
			<div class="clearfix"></div>
			<!-- Facebook share Start -->
			<div class="video-socialshare" style="height: auto;">
				<div class="floatleft facebook">
					<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.6&appId=376094672578810";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, "script", "facebook-jssdk"));</script>
					<img src="' . get_template_directory_uri() . '/images/flat-icons/facebook-64.png" alt="">
					<div class="fb-share-button" data-href="'.$current_url.'" data-layout="button" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u='.urlencode($current_url).'&amp;src=sdkpreparse">Share</a></div>
				</div>
				<!-- Facebook share End and Twitter like Start -->

				<div class="floatleft ttweet">
					<a href="https://twitter.com/home?status=' . $video_title . ' ' . $current_url . ' via @' . $blog_title . '" onclick="window.open(\'https://twitter.com/home?status=' . $video_title . ' ' . $current_url . ' via @' . $blog_title . '\',null,\'height=290,width=550,status=yes,toolbar=no,menubar=no,location=no\'); return false;" class="twitter-share-button">
						<img src="' . get_template_directory_uri() . '/images/flat-icons/twitter-64.png" alt="">
					</a>
				</div>
				<!-- Twitter like End and Google plus one Start -->

				<div class="floatleft gplusshare">
					<img src="' . get_template_directory_uri() . '/images/flat-icons/googleplus-64.png" alt="">
					<script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script>
                    <div class="g-plusone" data-size="large" data-count="false" data-annotation="none" data-height="44"></div>
				</div>
				<!-- Google plus one End -->

				<div class="floatleft reddit-share">
					<a href="http://www.reddit.com/submit" onclick="window.open(\'http://www.reddit.com/submit?url=\' + encodeURIComponent(window.location) +  \'&title=' . urlencode($video_title) . '\',null,\'height=900,width=750,status=yes,toolbar=no,menubar=no,location=no\'); return false;">
						<img src="' . get_template_directory_uri() . '/images/flat-icons/reddit-64.png" alt="">
					</a>
				</div>
				<!-- Reddit one End -->
				<div class="floatleft thumblr-share">
					<a href="http://www.tumblr.com/share" title="Share on Tumblr" onclick="window.open(\'http://www.tumblr.com/share\',null,\'height=626,width=900,status=yes,toolbar=no,menubar=no,location=no\'); return false;" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:129px; height:20px; background:url(\'' . get_template_directory_uri() . '/images/flat-icons/tumblr-64.png\') top left no-repeat transparent;">Share on Tumblr</a>
				</div>
				<!-- Thumblr one End -->
				<script src="http://platform.tumblr.com/v1/share.js"></script>
				<div class="clearfix"></div>
			</div>
		</div>';

		$top_ad = get_option('vh_open_video_ad_top');
		if ( $top_ad != false && $top_ad != '' && function_exists('adrotate_return') ) {
			$output .= '<div class="open-video-top-ad">'.adrotate_ad($top_ad).'</div>';
		}

		if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
			// Player starts here
			$output .= '<div id="mediaspace' . $videodivId . '" class="videoplayer open_video player" >';
			$output .= '<div class="video-fade-effect"></div>';
			$mobile = vgallery_detect_mobile ();

			// Embed player code
			if (! empty ( $fetched ) && $fetched [0]->file_type == 5 && ! empty ( $fetched [0]->embedcode )) {
				$playerembedcode = stripslashes ( $fetched [0]->embedcode );
				$playeriframewidth = str_replace ( 'width=', 'width="' . $width . '"', $playerembedcode );
				if ( $mobile == true ) {
					$output .= $playerembedcode;
				} else {
					$output .= str_replace ( 'height=', 'height="' . $height . '"', $playeriframewidth );
				}
				$output .= '<script> current_video( ' . $fetched [0]->vid . ',"' . $fetched [0]->name . '" ); </script>';
			} else if ($mobile === true) {
				$output .= '<script> current_video( ' . $fetched [0]->vid . ',"' . $fetched [0]->name . '" ); </script>';
				// Get video detail for HTML5 player
				foreach ( $fetched as $media ) { // Load video details
					$videourl = $media->file;
					$imgurl = $media->image;
					$file_type = $media->file_type;
					$video_amazon_buckets = $media->amazon_buckets;
					if ($imgurl == '') { // If there is no thumb image for video
						$imgurl = $_imagePath . 'nothumbimage.jpg';
					} else {
						if ($file_type == 2 || $file_type == 5) { // For uploaded image
							// $image_path = 
							if( $file_type == 2 && $video_amazon_buckets == 1 && strpos( $imgurl , '/' ) ){
								$imgurl = $imgurl;
							}else{
								$imgurl = $image_path . $imgurl;
							}
						}
					}
				}
				if ($file_type == 1) {
					// Check for youtube video
					if (preg_match ( '/www\.youtube\.com\/watch\?v=[^&]+/', $videourl, $vresult )) {
						$urlArray = explode ( '=', $vresult [0] );
						$video_id = trim ( $urlArray [1] );
						$videourl = 'http://www.youtube.com/embed/' . $video_id;
						// Generate youtube embed code for html5 player
						$output .= '<iframe  type="text/html" width="'.$width.'" height="'.$height.'" src="' . $videourl . '" frameborder="0"></iframe>';
					} elseif (strpos ( $videourl, 'dailymotion' ) > 0 ) { // For dailymotion videos
						$split = explode ( "/", $videourl );
						$split_id = explode ( "_", $split [4] );
						$image_url = '';
						$video = $videourl = $previewurl = 'http://www.dailymotion.com/embed/video/' . $split_id [0]; 
												
						$output .= '<iframe src="' . $video . '?allowed_in_playlists=0" width="'.$width.'" height="'.$height.'"  class="iframe_frameborder" ></iframe>';
						
					} else if (strpos ( $videourl, 'viddler' ) > 0) { // For viddler videos
						$imgstr = explode ( '/', $videourl );
						$viddler_id =  $imgstr[4];
						$output .= '<iframe id="viddler-' . $viddler_id . '" width="'.$width.'" src="//www.viddler.com/embed/' . $viddler_id . '/?f=1&autoplay=0&player=full&secret=26392356&loop=false&nologo=false&hd=false" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
					}
				} else if ($file_type == 3) {
						 if(preg_match ( '/www\.youtube\.com\/watch\?v=[^&]+/', $videourl, $vresult ) ) {
							$urlArray = explode ( '=', $vresult [0] );
							$video_id = trim ( $urlArray [1] );
							$videourl = 'http://www.youtube.com/embed/' . $video_id;
							// Generate youtube embed code for html5 player
							$output .= '<iframe  type="text/html" width="'.$width.'" height="'.$height.'" src="' . $videourl . '" frameborder="0"></iframe>';
						 } else if ( strpos ( $videourl, 'viddler' ) > 0 ) {
							$imgstr = explode ( '/', $videourl );
							$viddler_id =  $imgstr[4];
							$output .= '<iframe id="viddler-' . $viddler_id . '" width="'.$width.'" src="//www.viddler.com/embed/' . $viddler_id . '/?f=1&autoplay=0&player=full&secret=26392356&loop=false&nologo=false&hd=false" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
						 } elseif (strpos ( $videourl, 'dailymotion' ) > 0 ) { // For dailymotion videos
							$split = explode ( "/", $videourl );
							$split_id = explode ( "_", $split [4] );
							$image_url = '';
							$video = $videourl = $previewurl = 'http://www.dailymotion.com/embed/video/' . $split_id [0]; 
							$output .= '<iframe src="' . $video . '?allowed_in_playlists=0" width="'.$width.'" height="'.$height.'"  class="iframe_frameborder" ></iframe>';
						 } else if ( strpos($videourl, 'soundcloud.com') !== false ) {
							$output .= '<iframe src="' . $videourl . '?allowed_in_playlists=0" width="'.$width.'" height="'.$height.'"  class="iframe_frameborder" ></iframe>';
						 } else {
							$output .= '<video widht="100%" id="video" poster="' . $imgurl . '"   src="' . $videourl . '" autobuffer controls preload="metadata">' . $htmlplayer_not_support . '</video>';
						 }
				 } else { // Check for upload, URL and RTMP videos
					if ($file_type == 2 || $file_type == 5) {
						$image_path = str_replace ( 'plugins/contus-video-gallery/', 'uploads/videogallery/', $image_path );
						if( $file_type == 2 && strpos( $videourl,'/') ){
							$videourl = $videourl;
						}else{
							$videourl = $image_path . $videourl;
						}
					} else if ($file_type == 4) { // For RTMP videos
						$streamer = str_replace ( 'rtmp://', 'http://', $media->streamer_path );
						$videourl = $streamer . '_definst_/mp4:' . $videourl . '/playlist.m3u8';
					}
					// Generate video code for html5 player
					$output .= '<video widht="100%" id="video" poster="' . $imgurl . '"   src="' . $videourl . '" autobuffer controls preload="metadata">' . $htmlplayer_not_support . '</video>';
				}
			} else {
				if ( $fetched [0]->file_type == '3' && strpos($fetched[0]->file,'soundcloud.com') !== false ) {
					$output .= '<div id="flashplayer"><iframe scrolling="no" frameborder="no" src="' . $fetched[0]->file . '"></iframe></div>';
				} else {
					$output .= '<div id="flashplayer"><embed src="' . $this->_swfPath . '" flashvars="' . $flashvars . '" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent"></div>';
				}

				// Google adsense code Start
				if ($player_color ['googleadsense_visible'] == 1 && !( $mobile) && ($this->_post_type === APPTHAVIDEOGALLERY || $this->_page_post_type === APPTHAVIDEOGALLERY)) {
				  if($homeplayerData->google_adsense && $homeplayerData->google_adsense_value) {
						$output .= '<div>';
						/**
						 * Call function to dipaly google adsense on player
						 */
						 $output .= $this->displayGoogleAdsense ( $width, $vid, $homeplayerData->vid );
				  }
				} 
			}
			
			$output .= '</div>';	

					if( isset($homeplayerData->google_adsense ) &&  $homeplayerData->google_adsense ) {
						$details = $this->get_video_google_adsense_details($vid);
						$details1 = unserialize($details->googleadsense_details);
						if (isset($details1['closeadd']))
						{
							$closeadd = $details1['adsenseshow_time'];
							$ropen = $details1['adsense_reopen_time'];
						$output .= '<script type="text/javascript"> var closeadd = ' . $closeadd * 1000 . ';
							var ropen = ' . $ropen * 1000 . ';</script>';
						}
					}
					// End Google adsense End.
			$useragent = $_SERVER ['HTTP_USER_AGENT'];
			if (strpos ( $useragent, 'Windows Phone' ) > 0) { // check for windows phone
				$windo = 'Windows Phone';
			}
			
			// Check platform
			$output .= ' <script>
					function current_video_' . $videodivId . '( video_id,d_title ){ 
						if( d_title == undefined )
						{
							document.getElementById( "video_title' . $videodivId . '" ).innerHTML="";
						 }
						else { 
							document.getElementById( "video_title' . $videodivId . '" ).innerHTML="";
							document.getElementById( "video_title' . $videodivId . '" ).innerHTML=d_title;
						}
					}
					var txt =  navigator.platform ;
					var windo = "' . $windo . '";
					function failed( e ) {
					if( txt =="iPod"|| txt =="iPad" || txt == "iPhone" || windo=="Windows Phone" || txt == "Linux armv7l" || txt == "Linux armv6l" )
					{
						alert( "' . $player_not_support . '" ); 
					} 
					}
					</script>';
			// player ends here
		} else {
			vh_videohitCount_function($videoId);
			$image_path = site_url().'/wp-content/uploads/videogallery/';
			$output .= '<div class="open_video player">';
				if( strpos($fetched[0]->file, 'v=') !== false ) {
					
					
					$youtube_link = $wpdb->get_results('SELECT link FROM ' . $wpdb->prefix . 'hdflvvideoshare WHERE vid = "'.$fetched[0]->vid.'"');
					$parsed_url = parse_url($youtube_link['0']->link);
					if( strpos($parsed_url['query'], 'v=') !== false ) {
						$video_link = explode('v=', $fetched[0]->file);
						$video_id = $video_link[1];
					} else {
						$video_link = explode('v=', $fetched[0]->file);
						$video_id = $video_link[1];
						$video_id .= '?' . $parsed_url['query'];
					}
					$output .= '<iframe width="100%" height="597" src="//www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
				} elseif ( strpos($fetched[0]->file, 'viddler') !== false ) {
					$video_link = explode('/v/', $fetched[0]->file);
					$output .= '<iframe src="//www.viddler.com/embed/' . $video_link[1] . '" width="100%" height="597" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
				} elseif ( strpos($fetched[0]->file, 'dailymotion') !== false ) {
					$video_link = explode('/video/', $fetched[0]->file);
					$video_link = explode('_', $video_link[1]);
					$output .= '<iframe frameborder="0" width="100%" height="597" src="//www.dailymotion.com/embed/video/' . $video_link[0] . '" allowfullscreen></iframe>';
				} else {

					if(!empty($fetched) && $fetched[0]->file_type == 5 && !empty($fetched[0]->embedcode)) {
						$playerembedcode   = stripslashes($fetched[0]->embedcode);
						$playeriframewidth =  str_replace('width=', 'width="'.$width.'"', $playerembedcode);
						$output .= str_replace('height=', 'height="'.$height.'"', $playeriframewidth);
					}

					// Get video detail for HTML5 player
					foreach ($fetched as $media) {          ## Load video details
						$imgurl    = $media->image;
						$file_type = $media->file_type;

						// If there is no thumb image for video
						if ($imgurl == '') {
							$imgurl = $_imagePath . 'nothumbimage.jpg';
						} else {

							// For uploaded image
							if ($file_type == 2  || $file_type == 5) {
								$imgurl = $image_path . $imgurl;
							}
						}
					}

					if ( $file_type == 3 ) {
						$videourl =  $videourl . $videoUrl;
						$output .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
					} elseif ( $file_type == 2 ) {
						$videourl = $image_path . $videourl . $videoUrl;
						$output .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
					} else {
						$videourl = $image_path . $videourl . $videoUrl;
					}

					// $output .= do_shortcode('[video poster="' . $imgurl . '" width="955" height="597" src="' . $videourl . '"]');
				}

			$output .= '</div>';
		}

		// Display description, views, tags, playlist names detail under player
		if ($this->_post_type !== 'videogallery' && $this->_page_post_type !== 'videogallery') {
			$plugin_css = 'shortcode';
		}
		if (isset ( $arguments ['views'] ) && $arguments ['views'] == 'on') {
			$videogalleryviews = true;
		} else {
			if (($this->_post_type === 'videogallery' || $this->_page_post_type === 'videogallery') && $configXML->view_visible == 1) {
				$videogalleryviews = true;
			} else {
				$videogalleryviews = false;
				$no_views = 'noviews';
			}
		}

		// Display schema
		$output .= '<div class="open-video-schema" style="display: none;" itemscope itemtype="http://schema.org/Video">
						<h1 itemprop="name">' . get_the_title() . '</h1>
						<img itemprop="image" src="' . $fetched['0']->image . '" />
						<p itemprop="description">' . __('View the video for more details', 'vh') . '.</p>
					</div>';

		$output .= '<div class="video-page-wrapper"><div class="video-page-container ' . $plugin_css . '"><div class="vido_info_container"><div class="video-page-info ' . $no_views . '">';
		if ($this->_post_type === 'videogallery' || $this->_page_post_type === 'videogallery') {
			$output .= '<div class="page_title">' . get_the_title() . '</div>';
			if ($show_added_on) {
				$output .= '<div class="post_date">'.human_time_diff(get_the_time('U',get_the_ID()),current_time('timestamp')) .  ' '.__('ago', 'vh').'</div>';
			}
		}
		$output .= '<div class="clearfix"></div>';
		if ($configXML->categorydisplay == 1) {
			$output .= '<div class="video_category icon-flow-cascade">'.$homeplayerData->playlist_name.'</div>';
		}
		$output .= '<div class="open_video_info">';
		$youtube_id = explode("=",$homeplayerData->file);
		if ( $youtube_id['0'] == "http://www.youtube.com/watch?v" || $youtube_id['0'] == "https://www.youtube.com/watch?v" ) {
			$url = "https://www.googleapis.com/youtube/v3/videos?id=".$youtube_id['1']."&part=contentDetails&key=AIzaSyCg5rWv9VfV_Ad8GKe7BigmRP7y530kNu4";
			$video_data = file_get_contents($url);
			$video_info = json_decode($video_data);
			$video_duration = $video_info->items['0']->contentDetails->duration;

			if ( $video_duration != null ) {
				$dt = new DateTime();
				$dt->add(new DateInterval($video_duration));
				$interval = $dt->diff(new DateTime());

				if ( $interval->h > 0 ) {
					$video_duration = $interval->h.":".$interval->i.":".$interval->s;
				} else {
					$video_duration = $interval->i.":".$interval->s;
				}
			} else {
				$video_duration = '';
			}

			if ( $video_duration ) {
				$output .= '<div class="video-duration micon-clock">' . $video_duration . '</div>';
			}
		}
		if ($videogalleryviews == true) {
			$output .='<span class="views icon-eye">' . $hitcount . '</span>';
		}

		if ($this->_post_type === 'videogallery' || $this->_page_post_type === 'videogallery') {
			$tc = wp_count_comments(get_the_ID());
			$output .='<span class="comments icon-comment">' . $tc->total_comments . '</span>';
			$output .= '</div>';
			$output .= '<div class="clear"></div>';
		}

		// Rating starts here			
		if ($this->_post_type === 'videogallery' || ($this->_page_post_type === 'videogallery')) {
			if ($configXML->ratingscontrol == 1) {
				$ratingscontrol = true;
			} else {
				$ratingscontrol = false;
			}
		} else if (isset ( $arguments ['ratingscontrol'] ) && $arguments ['ratingscontrol'] == 'on') {
			$ratingscontrol = true;
		} else {
			$ratingscontrol = false;
		}
		if ($ratingscontrol == true) {
			if (isset ( $ratecount ) && $ratecount != 0) {
				$ratestar = round ( $rate / $ratecount );
			} else {
				$ratestar = 0;
			}
			$output .= '<div class="video-page-rating">
						<div class="centermargin floatleft" >
						<div class="rateimgleft" id="rateimg" onmouseover="displayrating' . $videodivId . $vid . '( 0 );" onmouseout="resetvalue' . $videodivId . $vid . '();" >
						<div id="a' . $videodivId . $vid . '" class="floatleft"></div>
						<ul class="ratethis " id="rate' . $videodivId . $vid . '" >
						<li class="one" >
						<a title="1 Star Rating"  onclick="getrate' . $videodivId . $vid . '( 1 );"  onmousemove="displayrating' . $videodivId . $vid . '( 1 );" onmouseout="resetvalue' . $videodivId . $vid . '();">1</a>
						</li>
						<li class="two" >
						<a title="2 Star Rating"  onclick="getrate' . $videodivId . $vid . '( 2 );"  onmousemove="displayrating' . $videodivId . $vid . '( 2 );" onmouseout="resetvalue' . $videodivId . $vid . '();">2</a>
						</li>
						<li class="three" >
						<a title="3 Star Rating"  onclick="getrate' . $videodivId . $vid . '( 3 );"   onmousemove="displayrating' . $videodivId . $vid . '( 3 );" onmouseout="resetvalue' . $videodivId . $vid . '();">3</a>
						</li>
						<li class="four" >
						<a  title="4 Star Rating"  onclick="getrate' . $videodivId . $vid . '( 4 );"  onmousemove="displayrating' . $videodivId . $vid . '( 4 );" onmouseout="resetvalue' . $videodivId . $vid . '();"  >4</a>
						</li>
						<li class="five" >
						<a title="5 Star Rating"  onclick="getrate' . $videodivId . $vid . '( 5 );"  onmousemove="displayrating' . $videodivId . $vid . '( 5 );" onmouseout="resetvalue' . $videodivId . $vid . '();" >5</a>
						</li>
						</ul>
						<input type="hidden" name="videoid" id="videoid' . $videodivId . $vid . '" value="' . $videoId . '" />
						<input type="hidden" value="" id="storeratemsg' . $videodivId . $vid . '" />
						</div>
						<div class="rateright-views floatleft" >
						<span  class="clsrateviews"  id="ratemsg' . $videodivId . $vid . '" onmouseover="displayrating' . $videodivId . $vid . '( 0 );" onmouseout="resetvalue' . $videodivId . $vid . '();"> </span>
						<span  class="rightrateimg" id="ratemsg1' . $videodivId . $vid . '" onmouseover="displayrating' . $videodivId . $vid . '( 0 );" onmouseout="resetvalue' . $videodivId . $vid . '();">  </span>
						</div>
						</div>
						</div> ';
			$output .= '<div class="clear"></div>';
			$output .= '<script type="text/javascript">
						function ratecal' . $videodivId . $vid . '( rating,ratecount )
						{
						if( rating==1 )
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis onepos";
						else if( rating==2 )
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis twopos";
						else if( rating==3 )
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis threepos";
						else if( rating==4 )
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis fourpos";
						else if( rating==5 )
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis fivepos";
						else
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis nopos";
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Ratings : ', 'vh').'"+ratecount;
						} 
						';
			if (isset ( $ratestar ) && isset ( $ratecount )) {
				if ($ratecount == '') {
					$ratecount = 0;
				}
				$output .= 'ratecal' . $videodivId . $vid . '( ' . $ratestar . ',' . $ratecount . ' ); ';
			}
			$output .= 'function createObject' . $videodivId . $vid . '() {
						var request_type;
						var browser = navigator.appName;
						if( browser == "Microsoft Internet Explorer" ){
						request_type = new ActiveXObject( "Microsoft.XMLHTTP" );
						} else {
						request_type = new XMLHttpRequest();
						}
						return request_type;
						}
						var http = createObject' . $videodivId . $vid . '();
						var nocache = 0;
						function getrate' . $videodivId . $vid . '( t ) {
						if( t==1 ) {
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis onepos";
						document.getElementById( "a' . $videodivId . $vid . '" ).className="ratethis onepos";
						} else if( t==2 ) {
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis twopos";
						document.getElementById( "a' . $videodivId . $vid . '" ).className="ratethis twopos";
						} else if( t==3 ) {
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis threepos";
						document.getElementById( "a' . $videodivId . $vid . '" ).className="ratethis threepos";
						} else if( t==4 ) {
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis fourpos";
						document.getElementById( "a' . $videodivId . $vid . '" ).className="ratethis fourpos";
						} else if( t==5 ) {
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="ratethis fivepos";
						document.getElementById( "a' . $videodivId . $vid . '" ).className="ratethis fivepos";
						}
						document.getElementById( "rate' . $videodivId . $vid . '" ).style.display="none";
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Thanks for rating!', 'vh').'";
						var vid     = document.getElementById( "videoid' . $videodivId . $vid . '" ).value;
						nocache     = Math.random();
						http.open( "get", baseurl+"/wp-admin/admin-ajax.php?action=ratecount&vid="+vid+"&rate="+t,true ); //Rating calling
						http.onreadystatechange = insertReply' . $videodivId . $vid . ';
						http.send( null );
						document.getElementById( "rate' . $videodivId . $vid . '" ).style.visibility="disable";
						}
						function insertReply' . $videodivId . $vid . '() {
						if( http.readyState == 4 ) {
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Ratings : ', 'vh').'"+http.responseText;
						document.getElementById( "rate' . $videodivId . $vid . '" ).className="";
						document.getElementById( "storeratemsg' . $videodivId . $vid . '" ).value=http.responseText;
						}
						}

						function resetvalue' . $videodivId . $vid . '() {
						document.getElementById( "ratemsg1' . $videodivId . $vid . '" ).style.display="none";
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).style.display="block";
						if( document.getElementById( "storeratemsg' . $videodivId . $vid . '" ).value == "" ) {
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Ratings : ', 'vh') . $ratecount . '";
						} else {
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Ratings : ', 'vh').'"+document.getElementById( "storeratemsg' . $videodivId . $vid . '" ).value;
						}
						}
						function displayrating' . $videodivId . $vid . '( t ) {
						if( t==1 ) {
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Poor : ', 'vh').'";
						} else if( t==2 ) {
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Nothing Special : ', 'vh').'";
						} else if( t==3 ) {
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Worth Watching : ', 'vh').'";
						} else if( t==4 ) {
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Pretty Cool : ', 'vh').'";
						} else if( t==5 ) {
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).innerHTML="'.__('Awesome : ', 'vh').'";
						}
						document.getElementById( "ratemsg1' . $videodivId . $vid . '" ).style.display="none";
						document.getElementById( "ratemsg' . $videodivId . $vid . '" ).style.display="block";
						}
						</script>';
		}
		// Rating ends here
		$output .= '</div>';
		if ($this->_post_type === 'videogallery' || $this->_page_post_type === 'videogallery') {
			if (! empty ( $tag_name ) && $configXML->tagdisplay == 1) { // Tag display
				$output .= '<div class="video-page-tag"><strong>' . __ ( 'Tags', 'video_gallery' ) . '          </strong>: ' . $tag_name . ' ' . '</div>';
			}
			if (strpos ( $videoUrl, 'youtube' ) > 0) { // check video url is Youtube
				$imgstr = explode ( 'v=', $videoUrl );
				$imgval = explode ( '&', $imgstr [1] );
				$videoId1 = $imgval [0];
				$video_thumb = 'http://img.youtube.com/vi/' . $videoId1 . '/mqdefault.jpg';
			}
			$removequotedescription = str_replace ( '"', '', $description );
			$videodescription = str_replace ( "'", '', $removequotedescription );
			
			$blog_title = get_bloginfo ( 'name' );
			$current_url = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'] . '?random=' . rand ( 0, 100 );
			if ($video_file_type == 5) {
				$sd = '';
			} else {
				$sd = '%5Bvideo%5D%5Bheight%5D=360&amp;p%5Bvideo%5D%5Bsrc%5D=' . urlencode ( $this->_swfPath ) . '%3Ffile%3D' . urlencode ( $videoUrl ) . '%26baserefW%3D' . urlencode ( APPTHA_VGALLERY_BASEURL ) . '%2F%26vid%3D' . $vid . '%26embedplayer%3Dtrue%26HD_default%3Dtrue%26share%3Dfalse%26skin_autohide%3Dtrue%26showPlaylist%3Dfalse&amp;p';
			}
			$output .= '<div class="video-cat-thumb">';
			
			$output .= '<div class="video-page-desc-wrapper">';
			
			$output .= '<div class="video-page-desc">';

			if ( $configXML->showTag == 1) {
				$output .= $description;
			}
			
			if( $configXML->embed_visible == 1){
				$output .= '<div class="open_video_share">';

				## embed code
				if($fetched[0]->file_type == 5 && !empty($fetched[0]->embedcode)){
					$embed_code = stripslashes($fetched[0]->embedcode);
				} else {
					$embed_code = '<embed src="' . $this->_swfPath . '" id="vh-embed-code" flashvars="' . $flashvars . '&amp;shareIcon=false&amp;email=false&amp;showPlaylist=false&amp;zoomIcon=false&amp;copylink=' . get_permalink() . '&amp;embedplayer=true" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
				}
				$output .= '<span class="embed_text icon-code-1">' . __("Embed&nbsp;Code", "vh") . '</span>';
				
				## Display Social icons start here
				if (strpos($videoUrl, 'youtube') > 0) { ## check video url is Youtube
					$imgstr      = explode("v=", $videoUrl);
					$imgval      = explode("&", $imgstr[1]);
					$videoId1    = $imgval[0];
					$video_thumb = "http://img.youtube.com/vi/" . $videoId1 . "/mqdefault.jpg";
				} 
				$videodescription = str_replace('"', "", $description);
				$videodescription = str_replace("'", "", $videodescription);
				if($video_file_type == 5 ) {
					$sd = '';
				} else {
					$sd = "%5Bvideo%5D%5Bheight%5D=360&amp;p%5Bvideo%5D%5Bsrc%5D=" . urlencode($this->_swfPath) . "%3Ffile%3D" . urlencode($videoUrl) . "%26baserefW%3D" . urlencode(APPTHA_VGALLERY_BASEURL) . "%2F%26vid%3D" . $vid . "%26embedplayer%3Dtrue%26HD_default%3Dtrue%26share%3Dfalse%26skin_autohide%3Dtrue%26showPlaylist%3Dfalse&amp;p";
				}

				$output .= '
						<textarea onclick="this.select()" id="embedcode" name="embedcode" rows="7" >' . $embed_code . '</textarea>
						<div class="clearfix"></div>
						<input type="hidden" name="flagembed" id="flagembed" />';

				$output .= '</div>';
			}
			// if( isset($player_color['iframe_visible'] ) && ( $player_color['iframe_visible'] ) ){
			// 	$output .= '<a href="javascript::void(0);" onclick="view_iframe_code();" id="iframe_code" class="embed"><span class="embed_text">'.__('Iframe' , 'video_gallery').'</span><span class="embed_arrow"></span></a>';
					
			// }
			if( isset( $player_color['report_visible'] ) && ( $player_color['report_visible'] ) ){
				$output .='<a href="javascript:void(0)" class="embed" id="allowReport"><span class="embed_text">' . __ ( 'Report&nbsp;Video', 'video_gallery' ) . '</span><span class="embed_arrow"></span></a>';
			}
			if ($fetched [0]->file_type == 5 && ! empty ( $fetched [0]->embedcode )) {
				$embed_code = stripslashes ( $fetched [0]->embedcode );
			} else {
				$embed_code = '<embed src="' . $this->_swfPath . '" flashvars="' . $flashvars . '&amp;shareIcon=false&amp;email=false&amp;showPlaylist=false&amp;zoomIcon=false&amp;copylink=' . get_permalink () . '&amp;embedplayer=true" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
			}
			$admin_email = get_option('admin_email');
			$user_email   = $this->_userEmail; 
			$output .='<div id="report_video_response"></div><form name="reportform" id="reportform" style="display:none;" method="post" >';
			$output .='<div class="report-video-title">Report this video</div>';
			$output .='<img id="reportform_ajax_loader"  src="'.APPTHA_VGALLERY_BASEURL.'/images/ajax-loader.gif" />';
			$output .='<input type="radio" name="reportvideotype" id="reportvideotype" value="Violent or repulsive content">Violent or repulsive content<label class="reportvideotype" title="Violent or grapical content or content posted to shock viewers"></label><br>';
			$output .='<input type="radio" name="reportvideotype" id="reportvideotype" value="Hateful or abusive content" >Hateful or abusive content<label class="reportvideotype" title="Content that promotes harted against protected groups, abuses vulnerable individuals , or enganges in cyberling"></label><br>';
			$output .='<input type="radio" name="reportvideotype" id="reportvideotype" value="Harmful dangerous acts" >Harmful dangerous acts<label class="reportvideotype" title="Content  that includes acts that many results in  physical harm"></label><br>';
			$output .='<input type="radio" name="reportvideotype" id="reportvideotype" value="Spam or misleading">Spam or misleading<label class="reportvideotype" title="Content that is massively posted or otherwise misleading in nature"></label><br>';
			$output .='<input type="radio" name="reportvideotype" class="reportvideotype" id="reportvideotype" value="Child abuse">Child abuse<label class="reportvideotype" title="Content that includes sexual,predatory or abusive communication  towards minors"></label><br>';
			$output .='<input type="radio" name="reportvideotype" class="reportvideotype" id="reportvideotype" value="Sexual content">Sexual content<label class="reportvideotype" title="Includes graphic sexual activity, nutity and other sexual content"></label><br>';
			$output .='<input type="hidden" id="admin_email" value="'.$admin_email.'" name="admin_email" />';
			$output .='<input type="hidden" id="reporter_email" value="'.$user_email.'" name="reporter_email" />';
			$output .='<input type="hidden" id="video_title" value="'.$video_title.'" name="video_title" />';
			$output .='<input type="hidden" id="redirect_url" value="'.get_video_permalink( $this->_vId ).'" name="redirect_url" />';
			$output .='<input type="button" class="reportbutton" value="Send" onclick="return reportVideoSend();" name="reportsend" />';
			$output .='&nbsp;&nbsp;<input type="reset" onclick="return hideReportForm();" class="reportbutton" value="Cancel" id="ReportFormreset"  name="reportclear" />';
			$output .='</form>';
			$output .='<input type="hidden" name="reportvideo" id="reportvideo" />';
					
			$output .= '</div><!-- end of video-page-desc-->
						<div class="clearfix"></div>
					</div>
					<div class="vc_separator wpb_content_element vc_separator_align_center vc_el_width_100">
							<span class="vc_sep_holder vc_sep_holder_l"><span class="vc_sep_line"></span></span>
							<h4 class="open_video_more icon-angle-down">' . __('Show more', 'vh' ) . '</h4>
							<span class="vc_sep_holder vc_sep_holder_r"><span class="vc_sep_line"></span></span>
					</div>';			 	
		}

		$output .= '</div></div></div>';

		if ($this->_post_type === 'videogallery' || $this->_page_post_type === 'videogallery') {
			// Default Comments
			if ($configXML->comment_option == 1) {
				ob_start();
				comments_template('', true);
				$output .= ob_get_clean();
			}
			// Facebook Comments
			if ($configXML->comment_option == 2) {
				$output .= '<style type="text/css">#comments #respond,#comments.comments-area, #disqus_thread, .comments-link{ display: none!important; } </style>';
				$output .= '<div class="clear"></div>
						<h2 class="related-videos">' . __ ( 'Post Your Comments', 'video_gallery' ) . '</h2>
						<div id="fb-root"></div>
						<script>
						( function( d, s, id ) {
						var js, fjs = d.getElementsByTagName( s )[0];
						if ( d.getElementById( id ) ) return;
						js = d.createElement( s ); js.id = id;
						js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=' . $configXML->keyApps . '";
						fjs.parentNode.insertBefore( js, fjs );
						}( document, "script", "facebook-jssdk" ) );
						</script>';
				$output .= '<div class="fb-comments" data-href="' . get_permalink () . '" data-num-posts="5"></div>';
			} 				// Disqus Comment
			else if ($configXML->comment_option == 3) {
				$output .= '<style type="text/css">#comments #respond,#comments.comments-area, .comments-link{ display: none!important; } </style>';
				$output .= '<div id="disqus_thread"></div>
							<script type="text/javascript">
							var disqus_shortname = "' . $configXML->keydisqusApps . '";
							( function() {
							var dsq = document.createElement( "script" ); dsq.type = "text/javascript"; dsq.async = true;
							dsq.src = "http://"+ disqus_shortname + ".disqus.com/embed.js";
							( document.getElementsByTagName( "head" )[0] || document.getElementsByTagName( "body" )[0] ).appendChild( dsq );
							} )();
							</script>
							<noscript>' . __ ( 'Please enable JavaScript to view the', 'video_gallery' ) . ' <a href="http://disqus.com/?ref_noscript">' . __ ( 'comments powered by Disqus.', 'video_gallery' ) . '</a></noscript>
							<a href="http://disqus.com" class="dsq-brlink">' . __ ( 'comments powered by', 'video_gallery' ) . ' <span class="logo-disqus">' . __ ( 'Disqus', 'video_gallery' ) . '</span></a>';
			}
		}

		$output	.= '<div class="clearfix"></div></div>';

		$output	.= '
		<div class="video-container-sidebar">
			<div class="sidebar_border"></div>
				<div class="like-social">';
				if ( function_exists('get_post_ul_meta') ) {
					$output .= '
						<div class="open_video_likes">' . vh_ldc_like_counter_v('Like!') . '</div>
						<div class="open_video_likes_count">' . get_post_ul_meta(get_the_ID(), "like") . '</div>';
				}
				$output .= '
				<div class="clearfix">
			</div>
		</div>';	

		vh_check_watched_video( $videoId );

		global $wpdb, $post;
		$user_id        = $post->post_author;
		$video_table    = $wpdb->prefix.'hdflvvideoshare';
		$where_comments = 'WHERE comment_approved = 1 AND user_id = ' . $user_id ;
		$comment_count  = $wpdb->get_var("SELECT COUNT( * ) AS total FROM {$wpdb->comments} {$where_comments}");
		$where_videos   = 'WHERE publish = 1 AND member_id = ' . $user_id ;
		$video_count    = $wpdb->get_var("SELECT COUNT( * ) AS total FROM {$video_table} {$where_videos}");
		$auth_desc_html = '';
		$max            = $video_count;
		$done           = false;

		if ( get_the_author_meta( 'description' ) ) {
			$author_desc = '';
			if ( strlen(strip_tags(get_the_author_meta( 'description' ))) > 100 ) {
				$author_desc = mb_substr(strip_tags(get_the_author_meta( 'description' )), 0, 100) . '..';
			} else {
				$author_desc = get_the_author_meta( 'description' );
			}
			$auth_desc_html = '<div class="author-description"><p>' . $author_desc . '</p></div>';
		}
		$more_videos = 'WHERE publish = 1 AND member_id = ' . $user_id;
		$video_more  = $wpdb->get_results("SELECT * FROM {$video_table} {$more_videos}");
		shuffle($video_more);

		$output .= '
			<div id="author-info" class="video-author-info">
			<span class="video-author-text">' . __('Video author:', 'vh') . '</span>
				<div class="avatar_box">
					<div id="author-avatar">'
						. get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'vh_author_bio_avatar_size', 70 ) ) .
					'</div>
				</div>
				<div id="author-description">
					<div class="video-author-name">' . get_the_author() . '</div>
					<div class="author-videos"><span class="icon-videocam">' . $video_count . __(' videos', 'vh') . '</span></div>
					<div class="author-comments"><span class="icon-comment">' . $comment_count . __(' comments', 'vh') . '</div>
					<div class="clearfix"></div>';
				$output .= '</div>
				<div class="author-info-lower">';
				$output .= $auth_desc_html
					.'
					<div class="more-author-videos">
					<span class="author-more-text">' . __('Other videos by ', 'vh') . get_the_author() . ':</span>';
					for ($i=0; $i < $video_count; $i++) {
						$author_video = $video_more[$i];
						$output .='
						<div class="video_container">
							<div class="imgSidethumb">';

							$file_type = $author_video->file_type; // Video Type
							$imageFea  = $author_video->image; // Video Image
							$imageFea  = str_replace("/mq","/sd",$imageFea);

							if ( !empty($imageFea) ) {
								$image_header = get_headers_curl($imageFea);
							}

							if ( mb_substr($image_header, 9, 3) == '404' ) {
								$imageFea       = $author_video->image;
								$video_sd_image = 'mqdef';
							} else {
								$imageFea       = str_replace("/mq","/sd", $author_video->image);
								$video_sd_image = 'sdimg';
							}

							if ($imageFea == '') {  ##If there is no thumb image for video
								$imageFea = APPTHA_VGALLERY_BASEURL . 'images' . DS . 'nothumbimage.jpg';
								$video_sd_image .= ' noimage';
							} elseif ( $author_video->opimage == '' &&  $file_type != 3 ) {
								$upload_dir = wp_upload_dir();
								$imageFea = $upload_dir['baseurl'] . '/videogallery/' . $author_video->image;
							}

							$thumb_href = 'href="' . get_permalink( $author_video->slug ) . '"';

							if ( $file_type == 3 ) {
								$image_file = $imageFea;
							} elseif ( $file_type == 2 ) {
								$imageFea = $image_path . $imageFea;
							} else {
								$image_file = $image_path . $imageFea;
							}

							$imageFea = vh_imgresize($imageFea, 185, 122, $author_video->slug);
							## OPEN VIDEO PAGE ##
							$output .= '<div class="video_image_container '.$video_sd_image.'">
										<a href="javascript:void(0);" class="video_play"></a>
										<a ' . $thumb_href . ' class="view_more"></a>
										<img src="' . $imageFea . '" alt="' . $author_video->name . '" class="related" />
										<div id="video_dialog" title="' . $author_video->name . '">';
										if ( get_option('vh_html5_videos') == 'false' || get_option('vh_html5_videos') == false ) {
											if( $file_type == 5 && !empty($embed_code) ) {
												$relFetembedcode = stripslashes($embed_code);
												$relFetiframewidth = preg_replace(array('/width="\d+"/i'),array(sprintf('width="%d"', $width)),$relFetembedcode);
													if($mobile === true){
														$player_values = htmlentities($relFetiframewidth);
													} else {
														$player_values = preg_replace(array('/height="\d+"/i'),array(sprintf('height="%d"', $height)),$relFetiframewidth);
													}
											 } else {
												 $mobile = vgallery_detect_mobile();
												if($mobile === true){
													## Check for youtube video
													if (preg_match("/www\.youtube\.com\/watch\?v=[^&]+/", $author_video->file, $vresult)) {
														$urlArray = explode("=", $vresult[0]);
														$video_id = trim($urlArray[1]);
														$reavideourl = "http://www.youtube.com/embed/$video_id";
														## Generate youtube embed code for html5 player
														$player_values = htmlentities('<iframe  type="text/html" src="' . $reavideourl . '" frameborder="0"></iframe>');
													} else if ($file_type != 5) {        ## Check for upload, URL and RTMP videos
														if ($file_type == 2) {                  ## For uploaded image
															$reavideourl = $image_path . $reafile;
														} else if ($file_type == 4) {           ## For RTMP videos
															$streamer = str_replace("rtmp://", "http://", $media->streamer_path);
															$reavideourl = $streamer . '_definst_/mp4:' . $reafile . '/playlist.m3u8';
														}
														## Generate video code for html5 player
														$player_values = htmlentities('<video id="video" poster="' . $imageFea . '"   src="' . $reavideourl .'" autobuffer controls preload="metadata">' . $htmlplayer_not_support . '</video>');
													}
												} else {
													## Flash player code
													$player_values = '<embed src="' . $this->_swfPath . '" flashvars="' . $pluginflashvars . '&amp;mtype=playerModule&amp;vid='.$author_video->vid.'" width="100%" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" wmode="transparent">';
												}
											}
											if ( strpos($author_video->file,'soundcloud.com') !== false ) {
												$output .= '<input type="hidden" class="iframe_url" value="' . $author_video->file . '" />';
												$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
											} else {
												$output .= $player_values;
											}
										} else {

											if( strpos($author_video->file, 'v=') !== false ) {
												$video_link = explode('v=', $author_video->file);
												$output .= '<input type="hidden" class="iframe_url" value="//www.youtube.com/embed/' . $video_link[1] . '" />';
												$output .= '<iframe id="video_iframe" width="100%" height="444" src="about:blank" frameborder="0" allowfullscreen></iframe>';
											} elseif ( strpos($author_video->file, '/v/') !== false ) {
												$video_link = explode('/v/', $author_video->file);
												$output .= '<input type="hidden" class="iframe_url" value="//www.viddler.com/embed/' . $video_link[1] . '" />';
												$output .= '<iframe id="video_iframe" src="about:blank" width="100%" height="444" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
											} elseif ( strpos($author_video->file, '/video/') !== false ) {
												$video_link = explode('/video/', $author_video->file);
												$video_link = explode('_', $video_link[1]);
												$output .= '<input type="hidden" class="iframe_url" value="//www.dailymotion.com/embed/video/' . $video_link[0] . '" />';
												$output .= '<iframe id="video_iframe" frameborder="0" width="100%" height="444" src="about:blank" allowfullscreen></iframe>';
											} else {

												if( $file_type == 5 && !empty($embed_code) ) {
													$relFetembedcode = stripslashes($embed_code);
													$relFetiframewidth = preg_replace(array('/width="\d+"/i'),array(sprintf('width="%d"', $width)),$relFetembedcode);
													$output .= preg_replace(array('/height="\d+"/i'),array(sprintf('height="%d"', $height)),$relFetiframewidth);

												 }

												// Get video detail for HTML5 player
												foreach ($fetched as $media) {          ## Load video details
													$imgurl    = $media->image;
													$file_type = $media->file_type;

													// If there is no thumb image for video
													if ($imgurl == '') {
														$imgurl = $_imagePath . 'nothumbimage.jpg';
													} else {

														// For uploaded image
														if ($file_type == 2  || $file_type == 5) {
															$imgurl = $image_path . $imgurl;
														}
													}
												}

												if ( $file_type == 3 ) {
													$videourl =  $videourl . $videoUrl;
												} elseif ( $file_type == 2 ) {
													$videourl = $image_path . $videoUrl;

													$output .= '<video width="100%" controls preload="metadata"><source src="' . $videourl . '" type="video/mp4">' . __("Your browser does not support the video tag.", "vh") . '</video>';
												} else {
													$videourl = $image_path . $videourl . $videoUrl;
												}

												// $output .= do_shortcode('[video poster="' . $imgurl . '" width="955" height="597" src="' . $videourl . $videoUrl . '"]');
											}

										}
										$output .= '</div>
									</div>
									
								</div>';
							$output .='<div class="vid_info"><span><a ' . $thumb_href . ' class="videoHname">';
							## Open video title length ##
							if (strlen($author_video->name) > 30) { ## Displaying Video Title
								$videoname = mb_substr($author_video->name, 0, 30) . '..';
							} else {
								$videoname = $author_video->name;
							}
							$output .= $videoname;
							$output .='</a></span></div>';
							$output .= '<div class="video_info">';
							if ($author_video->duration != 0.00) {
								$output .= '<div class="video-duration micon-clock">' . $author_video->duration . '</div>';
							}
							$output .= '<div class="video_views icon-eye">'. $author_video->hitcount . '</div>';
							$tc = wp_count_comments($author_video->slug);
							$output .= '<div class="video_comments icon-comment">'. $tc->total_comments . '</div>';
							if ( function_exists('get_post_ul_meta') ) {
								$output .= '<div class="video_likes icon-heart">'. get_post_ul_meta($author_video->slug, "like") . '</div>'; 
							}
							$output .= '</div>
						</div>';

						if ( $i >= 1 ) {
							break;
						}
					}
					$permalink_structure = get_option('permalink_structure');

					if ( $permalink_structure == '' ) {
						$url_symbol = '&';
					} else {
						$url_symbol = '?';
					}
					
					$output .= '</div>
					<div class="clearfix"></div>
					<div id="author-link">
						<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . $url_symbol . 'all_videos=true" rel="author">'
							 . sprintf(esc_attr__('View all %s videos', 'vh'), $video_count) . 
						'</a>
					</div>
				</div>
				<div class="clearfix"></div>';
				$sidebar_ad = get_option('vh_open_video_ad_sidebar');
				if ( $sidebar_ad != false && $sidebar_ad != '' && function_exists('adrotate_return') ) {
					$output	.= '<div class="open-video-sidebar-ad">'.adrotate_ad($sidebar_ad).'</div>';
				}
			$output	.= '
			</div>';
		$output .= '</div>';

		$bottom_ad = get_option('vh_open_video_ad_bottom');
		if ( $bottom_ad != false && $bottom_ad != '' && function_exists('adrotate_return') ) {
			$output	.= '<div class="open-video-bottom-ad">'.adrotate_ad($bottom_ad).'</div>';
		}

		return $output;
	}
	/**
	 * function for get related video
	 * 
	 * @global type $wpdb
	 * @param type $videoID        	
	 * @return type $related_video
	 */
	public function get_related_videos($vid) {
		global $wpdb;
		$video_playlist_id = $wpdb->get_var ( "SELECT playlist_id FROM " . $wpdb->prefix . "hdflvvideoshare_med2play WHERE media_id='$vid'" );
		$Limit = $wpdb->get_var("SELECT related_video_count FROM " . $wpdb->prefix . "hdflvvideoshare_settings LIMIT 1");
		if( empty($Limit) ) {
			 $Limit=100 ;
		}
		$sql = "SELECT distinct a.*,s.guid,b.playlist_id,p.playlist_name,p.playlist_slugname
									  FROM " . $wpdb->prefix . "hdflvvideoshare a
									  INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play b ON a.vid=b.media_id
									  INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=b.playlist_id
									  INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=a.slug
									  WHERE b.playlist_id=" . $video_playlist_id . " AND a.publish='1' AND p.is_publish='1' GROUP BY a.vid ORDER BY a.vid DESC LIMIT ".$Limit;
		
		$related_videos = $wpdb->get_results ( $sql );
		return $related_videos;
	}
} // class over

class Widget_ContusPopularVideos_initEdited extends WP_Widget {
	function Widget_ContusPopularVideos_initEdited() {
		$widget_ops = array (
				'classname' => 'Widget_ContusPopularVideos_initEdited ',
				'description' => 'Contus Popular Videos' 
		);
		parent::__construct ( 'Widget_ContusPopularVideos_initEdited', 'Contus Popular Videos', $widget_ops );
	}
	function form($instance) {
		$instance = wp_parse_args ( ( array ) $instance, array (
				'title' => 'Popular Videos',
				'show' => '3' 
		) );
		// These are our own options
		$title = esc_attr ( $instance ['title'] );
		$show =  isset( $instance['show'] ) ? absint( $instance['show'] ) : 3;
		?>
<p>
	<label for='<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>'>Title:
		<input class='widefat'
		id='<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>'
		name='<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>'
		type='text' value='<?php echo esc_html( $title ); ?>' />
	</label>
</p>
<p>
	<label for='<?php echo esc_html( $this->get_field_id( 'show' ) ); ?>'>Show:
		<input class='widefat'
		id='<?php echo esc_html( $this->get_field_id( 'show' ) ); ?>'
		name='<?php echo esc_html( $this->get_field_name( 'show' ) ); ?>'
		type='text' value='<?php echo esc_html( $show ); ?>' />
	</label>
</p>
<?php
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance ['title'] = $new_instance ['title'];
		$instance ['show'] = $new_instance ['show'];
		return $instance;
	}
	function widget($args, $instance) {
		extract ( $args, EXTR_SKIP );
		
		$title = empty ( $instance ['title'] ) ? ' ' : apply_filters ( 'widget_title', $instance ['title'] );
		global $wpdb;
		$site_url = get_site_url ();
		$dir = dirname ( plugin_basename ( __FILE__ ) );
		$dirExp = explode ( '/', $dir );
		$dirPage = $dirExp [0];
		?>
<!-- For Getting The Page Id More and Video Page-->
<?php
		$moreName = $wpdb->get_var ( 'SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE post_content LIKE "%[videomore]%" AND post_status="publish" AND post_type="page" LIMIT 1' );
		$settings_result = $wpdb->get_row ( 'SELECT ratingscontrol,view_visible FROM ' . $wpdb->prefix . 'hdflvvideoshare_settings WHERE settings_id="1"' );
		$more_videos_link = get_morepage_permalink ( $moreName, 'popular' );
?>
<!-- For Popular videos -->
<?php
		$fetched = '';
		$ratearray = array (
				'nopos1',
				'onepos1',
				'twopos1',
				'threepos1',
				'fourpos1',
				'fivepos1' 
		);
		$viewslang = __ ( 'Views', 'video_gallery' );
		$viewlang = __ ( 'View', 'video_gallery' );
		echo $before_widget;
		$div = '<div id="popular-videos" class="sidebar-wrap "><h3 class="widget-title"><a href="' . $more_videos_link .'">' . $title . '</a></h3>';
		if ($instance ['show']) {
			$show = $instance ['show'];
		} else {
			$show = 3;
		}
		
		$sql = 'SELECT DISTINCT a.*,s.guid,b.playlist_id,p.playlist_name FROM ' . $wpdb->prefix . 'hdflvvideoshare a
					INNER JOIN ' . $wpdb->prefix . 'hdflvvideoshare_med2play b ON a.vid=b.media_id
					INNER JOIN ' . $wpdb->prefix . 'hdflvvideoshare_playlist p ON p.pid=b.playlist_id
					INNER JOIN ' . $wpdb->prefix . 'posts s ON s.ID=a.slug
					WHERE a.publish=1 AND p.is_publish=1 GROUP BY a.vid ORDER BY a.hitcount DESC LIMIT ' . $show;
		$populars = $wpdb->get_results ( $sql );
		if (! empty ( $populars )) {
			$fetched = $populars [0]->playlist_name;
		}
		$moreCount = $wpdb->get_results ( 'SELECT COUNT(a.vid) AS contus FROM ' . $wpdb->prefix . 'hdflvvideoshare a
					INNER JOIN ' . $wpdb->prefix . 'hdflvvideoshare_med2play b ON a.vid=b.media_id
					INNER JOIN ' . $wpdb->prefix . 'hdflvvideoshare_playlist p ON p.pid=b.playlist_id
					WHERE a.publish=1 AND p.is_publish=1' );
		$countP = $moreCount [0]->contus;
		$div .= '<ul class="ulwidget">';
		// were there any posts found?
		if (! empty ( $populars )) {
			// posts were found, loop through them
			$image_path = str_replace ( 'plugins/' . $dirPage . '/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL );
			$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
			foreach ( $populars as $popular ) {
				$file_type = $popular->file_type; // Video Type
				$imagePop = $popular->image; // VIDEO IMAGE
				$guid = get_video_permalink ( $popular->slug ); // guid
				if ($imagePop == '') { // If there is no thumb image for video
					$imagePop = $_imagePath . 'nothumbimage.jpg';
				} else {
					if ($file_type == 2 || $file_type == 5) { // For uploaded image
						if( $file_type == 2 &&  strpos( $imagePop , '/' )){
							$imagePop = $imagePop;
					
						}else{
							$imagePop = $image_path . $imagePea;
						}
					}
					if( $file_type == 3 ){
						$imageFea = $imagePop;
					}
				}
				$name = strlen ( $popular->name );
				// output to screen
				$div .= '<li class="clearfix sideThumb">';
				$div .= '<div class="imgBorder"><a href="' . $guid . '" title="'.$popular->name.'"><img src="' . $imagePop . '" alt="' . $popular->name . '" class="img" width="120" height="80" style="width: 120px; height: 80px;"  /></a>';
				if ($popular->duration != 0.00) {
					$div .= '<span class="video_duration">' . $popular->duration . '</span>';
				}
				$div .= '</div>';
				$div .= '<div class="side_video_info"><a class="videoHname" title="'.$popular->name.'" href="' . $guid . '">';
				if ($name > 25) {
					$div .= substr ( $popular->name, 0, 25 ) . '..';
				} else {
					$div .= $popular->name;
				}
				$div .= '</a><div class="clear"></div>';
				if ($settings_result->view_visible == 1) {
					if ($popular->hitcount > 1) {
						$viewlanguage = $viewslang;
					} else {
						$viewlanguage = $viewlang;
					}
					$div .= '<span class="views">' . $popular->hitcount . ' ' . $viewlanguage;
					$div .= '</span>';
				}
				// Rating starts here
				if ($settings_result->ratingscontrol == 1) {
					if (isset ( $popular->ratecount ) && $popular->ratecount != 0) {
						$ratestar = round ( $popular->rate / $popular->ratecount );
					} else {
						$ratestar = 0;
					}
					$div .= '<span class="ratethis1 ' . $ratearray [$ratestar] . '"></span>';
				}
				// Rating ends here
				$div .= '<div class="clear"></div>';
				$div .= '</div>';
				$div .= '</li>';
			}
		} else
			$div .= '<li>' . __ ( 'No Popular videos', 'video_gallery' ) . '</li>';
			// end list
		if (($show < $countP) || ($show == $countP)) {
			$div .= '<li><div class="right video-more"><a href="' . $more_videos_link . '">' . __ ( 'More&nbsp;Videos', 'video_gallery' ) . '&nbsp;&#187;</a></div>';
			$div .= '<div class="clear"></div></li>';
		} else {
			$div .= '<li><div align="right"> </div></li>';
		}
		$div .= '</ul></div>';
		echo balanceTags ( $div );
		echo $after_widget;
	}
}

// Run code and init
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Widget_ContusPopularVideos_initEdited");' ) ); //adding product tag widget

class widget_ContusFeaturedVideos_initEdited extends WP_Widget {
	function widget_ContusFeaturedVideos_initEdited() {
		$widget_ops = array (
				'classname' => 'widget_ContusFeaturedVideos_initEdited ',
				'description' => 'Contus Featured Videos' 
		);
		parent::__construct ( 'widget_ContusFeaturedVideos_initEdited', 'Contus Featured Videos', $widget_ops );
	}
	function form($instance) {
		$instance = wp_parse_args ( ( array ) $instance, array (
				'title' => 'Featured Videos',
				'show' => '3' 
		) );
		// These are our own options
		$options = get_option ( 'widget_ContusVideoCategory' );
		$title = esc_attr ( $instance ['title'] );
		$show =  isset( $instance['show'] ) ? absint( $instance['show'] ) : 3;
		?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>">Title: <input
		class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
		name="<?php echo $this->get_field_name('title'); ?>" type="text"
		value="<?php echo $title; ?>" /></label>
</p>
<p>
	<label for="<?php echo $this->get_field_id('show'); ?>">Show: <input
		class="widefat" id="<?php echo $this->get_field_id('show'); ?>"
		name="<?php echo $this->get_field_name('show'); ?>" type="text"
		value="<?php echo $show; ?>" /></label>
</p>
<?php
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance ['title'] = $new_instance ['title'];
		$instance ['show'] = $new_instance ['show'];
		return $instance;
	}
	function widget($args, $instance) {
		// and after_title are the array keys." - These are set up by the theme
		extract ( $args, EXTR_SKIP );
		$title = empty ( $instance ['title'] ) ? ' ' : apply_filters ( 'widget_title', $instance ['title'] );
		if (! empty ( $title ))
			// WIDGET CODE GOES HERE
			$tt = 1;
		global $wpdb, $wp_version, $popular_posts_current_ID;
		// These are our own options
		$options = get_option ( 'widget_ContusFeaturedVideos' );
		// $title = $instance['title']; // Title in sidebar for widget
		
		if ($instance ['show']) {
			if( absint( $instance['show'] ) ){
				$show = $instance ['show'];
			}else{
				$show =3;	
			}
		} else {
			$show = 3;
		}
			
		$excerpt = $options ['excerpt']; // Showing the excerpt or not
		$exclude = $options ['exclude']; // Categories to exclude
		$site_url = get_site_url ();
		$dir = dirname ( plugin_basename ( __FILE__ ) );
		$dirExp = explode ( '/', $dir );
		$dirPage = $dirExp [0];
		?>
<!-- Recent videos -->
<script type="text/javascript">
	var baseurl;
	baseurl = '<?php echo $site_url; ?>';
	folder  = '<?php echo $dirPage; ?>'
</script>
<!-- For Getting The Page Id More and Video-->
<?php
		$moreName = $wpdb->get_var ( "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_content LIKE '%[videomore]%' AND post_status='publish' AND post_type='page' LIMIT 1" );
		$settings_result = $wpdb->get_row ( "SELECT ratingscontrol,view_visible FROM " . $wpdb->prefix . "hdflvvideoshare_settings WHERE settings_id='1'" );
		$more_videos_link = get_morepage_permalink ( $moreName, 'featured' );
		?>
<!-- For Featured Videos -->
<?php
		echo $before_widget;
		$fetched = '';
		$ratearray = array (
				"nopos1",
				"onepos1",
				"twopos1",
				"threepos1",
				"fourpos1",
				"fivepos1" 
		);
		$viewslang = __ ( 'Views', 'video_gallery' );
		$viewlang = __ ( 'View', 'video_gallery' );
		$div = '<div id="featured-videos"  class="sidebar-wrap ">
							<h3 class="widget-title"><a href="'.$more_videos_link.'">' . $title . '</a></h3>';		
		$sql = "SELECT DISTINCT a.*,s.guid,b.playlist_id,p.playlist_name FROM " . $wpdb->prefix . "hdflvvideoshare a
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play b ON a.vid=b.media_id
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=b.playlist_id
							INNER JOIN " . $wpdb->prefix . "posts s ON s.ID=a.slug
							WHERE a.publish='1' AND p.is_publish='1' AND a.featured='1' GROUP BY a.vid ORDER BY a.ordering ASC  LIMIT " . $show;
		$features = $wpdb->get_results ( $sql );
		if (! empty ( $features )) {
			$playlist_id = $features [0]->playlist_id;
			$fetched = $features [0]->playlist_name;
		}
		$moreF = $wpdb->get_results ( "SELECT COUNT(a.vid) AS contus FROM " . $wpdb->prefix . "hdflvvideoshare a
							INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play b ON a.vid=b.media_id INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist p ON p.pid=b.playlist_id WHERE a.publish='1' AND p.is_publish='1' AND a.featured='1'" );
		$countF = $moreF [0]->contus;
		$div .= '<ul class="ulwidget">';
		
		// were there any posts found?
		if (! empty ( $features )) {
			// posts were found, loop through them
			$image_path = str_replace ( 'plugins/' . $dirPage . '/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL );
			$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
			
			foreach ( $features as $feature ) {
				$file_type = $feature->file_type; // Video Type
				$imageFea = $feature->image; // VIDEO IMAGE
				$guid = get_video_permalink ( $feature->slug ); // guid
				if ($imageFea == '') { // If there is no thumb image for video
					$imageFea = $_imagePath . 'nothumbimage.jpg';
				} else {
					if ($file_type == 2 || $file_type == 5) { // For uploaded image
						if( $file_type == 2 &&  strpos( $imageFea , '/' )){
							$imageFea = $imageFea;
					
						}else{
							$imageFea = $image_path . $imageFea;
						}
					}
					if( $file_type == 3 ){
						$imageFea = $imageFea;
					}
				}
				$vidF = $feature->vid;
				$name = strlen ( $feature->name );
				// output to screen
				$div .= '<li class="clearfix sideThumb">';
				$div .= '<div class="imgBorder"><a href="' . $guid . '" title="'.$feature->name.'"><img src="' . $imageFea . '" alt="' . $feature->name . '"  class="img" width="120" height="80" style="width: 120px; height: 80px;"  /></a>';
				if ($feature->duration != 0.00) {
					$div .= '<span class="video_duration">' . $feature->duration . '</span>';
				}
				$div .= '</div>';
				$div .= '<div class="side_video_info"><a title="'.$feature->name.'" class="videoHname" href="' . $guid . '">';
				if ($name > 25) {
					$div .= substr ( $feature->name, 0, 25 ) . '..';
				} else {
					$div .= $feature->name;
				}
				$div .= '</a>';
				$div .= '<div class="clear"></div>';
				if ($settings_result->view_visible == 1) {
					if ($feature->hitcount > 1) {
						$viewlanguage = $viewslang;
					} else {
						$viewlanguage = $viewlang;
					}
					$div .= '<span class="views">' . $feature->hitcount . ' ' . $viewlanguage . '</span>';
				}
				
				// Rating starts here
				if ($settings_result->ratingscontrol == 1) {
					if (isset ( $feature->ratecount ) && $feature->ratecount != 0) {
						$ratestar = round ( $feature->rate / $feature->ratecount );
					} else {
						$ratestar = 0;
					}
					$div .= '<span class="ratethis1 ' . $ratearray [$ratestar] . '"></span>';
				}
				// Rating ends here
				
				$div .= '<div class="clear"></div>';
				$div .= '<div class="clear"></div>';
				$div .= '</div>';
				$div .= '</li>';
			}
		} else
			$div .= "<li>" . __ ( 'No Featured Videos', 'video_gallery' ) . "</li>";
			// end list
		if (($show < $countF) || ($show == $countF)) {
			$div .= '<li><div class="video-more"><a href="' . $more_videos_link . '">' . __ ( 'More&nbsp;Videos', 'video_gallery' ) . '&nbsp;&#187;</a></div>';
			$div .= '<div class="clear"></div></li>';
		} else {
			$div .= '<li><div align="right"> </div></li>';
		}
		$div .= '</ul></div>';
		echo $div;
		// echo widget closing tag
		echo $after_widget;
	}
}

// Run code and init
add_action ( 'widgets_init', create_function ( '', 'return register_widget("widget_ContusFeaturedVideos_initEdited");' ) ); // adding product tag widget

class Widget_ContusVideoCategory_initEdited extends WP_Widget {

	function Widget_ContusVideoCategory_initEdited() {
		$widget_ops = array( 'classname' => 'Widget_ContusVideoCategory_initEdited ', 'description' => 'Contus Video Categories' );
		parent::__construct( 'Widget_ContusVideoCategory_initEdited', 'Contus Video Category', $widget_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Video Categories', 'show' => '3' ) );
		## These are our own options
		$title = esc_attr( $instance['title'] );
		$show  = isset( $instance['show'] )?absint( $instance['show'] ):3;
		?>
		<p><label for='<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>'>Title: <input class='widefat' id='<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>' name='<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>' type='text' value='<?php echo esc_html( $title ); ?>' /></label></p>
		<p><label for='<?php echo esc_html( $this->get_field_id( 'show' ) ); ?>'>Show: <input class='widefat' id='<?php echo esc_html( $this->get_field_id( 'show' ) ); ?>' name='<?php echo esc_html( $this->get_field_name( 'show' ) ); ?>' type='text' value='<?php echo esc_html( $show ); ?>' /></label></p>
				<?php
			}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['show']  = (int)$new_instance['show'];
		return $instance;
	}

	function widget( $args, $instance ) {
		## and after_title are the array keys." - These are set up by the theme
		extract( $args, EXTR_SKIP );
		$title = empty( $instance['title']) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
		## WIDGET CODE GOES HERE
		global $wpdb;
		## These are our own options
		//Show no of  video  in  this  widgets
		if($instance['show']){
			if( absint( $instance['show'] ) ) {
				$show = $instance['show'];
			}else{
				$show = 3;
			} 
		}else{
			$show = 3;		
		}
		?>
		<!-- Recent videos For Getting The Page Id More and Video-->
		<?php
		$moreName = $wpdb->get_var( 'SELECT ID from ' . $wpdb->prefix . 'posts WHERE post_content LIKE "%[videomore]%" and post_status="publish" and post_type="page" limit 1' );
		## For video category
		$sql = 'SELECT * FROM ' . $wpdb->prefix . 'hdflvvideoshare_playlist WHERE is_publish=1 LIMIT ' . $show;
		$features = $wpdb->get_results( $sql );
		$moreCategories   = $wpdb->get_results( 'SELECT COUNT(*) AS contus FROM ' . $wpdb->prefix . 'hdflvvideoshare_playlist WHERE is_publish=1' );
		$countCategories  = $moreCategories[0]->contus;
		$div = '';
		$more_videos_link = get_morepage_permalink( $moreName, 'categories' );
		echo $before_widget; 
		$div .= '<div id="videos-category"  class="widget widget_categories sidebar-wrap "> <h3 class="widget-title"><a href="' . $more_videos_link . '">' . $title . '</a></h3>';
		$div .= '<ul class="ulwidget clearfix">';
		## were there any posts found?
		if ( ! empty( $features ) ) {
			## posts were found, loop through them
			foreach ( $features as $feature ) {
				$fetched = $feature->playlist_name;
				$playlist_slugname = $feature->playlist_slugname;
				$playlist_id = $feature->pid;
				$div .= '<li>';
				$playlist_url = get_playlist_permalink( $moreName, $playlist_id, $playlist_slugname );
				$div .= '<a class="videoHname "  href="' . $playlist_url . '">' . $fetched . '</a>';
				$div .= '</li>';
			}
		} else {
			$div .= '<li>' . __( 'No Categories', 'video_gallery' ) . '</li>';
		}
		## end list
		if ( ( $show < $countCategories ) ) {
			$div .= '<li><div class="right video-more"><a href="' . $more_videos_link . '">' . __( 'More Categories', 'video_gallery' ) . ' &#187;</a></div></li>';
		}
		$div .= '</ul></div>';
		echo balanceTags( $div );
		echo $after_widget; 
	}

}
add_action( 'widgets_init', create_function( '', 'return register_widget("Widget_ContusVideoCategory_initEdited" );' ) ); ##adding product tag widget

class Widget_ContusRecentVideos_initEdited extends WP_Widget {
	function Widget_ContusRecentVideos_initEdited() {
		$widget_ops = array (
				'classname' => 'Widget_ContusRecentVideos_initEdited ',
				'description' => 'Contus Recent Videos' 
		);
		parent::__construct ( 'Widget_ContusRecentVideos_initEdited', 'Contus Recent Videos', $widget_ops );
	}
	function form($instance) {
		$instance = wp_parse_args ( ( array ) $instance, array (
				'title' => 'Recent Videos',
				'show' => '3' 
		) );
		// These are our own options
		$title = esc_attr ( $instance ['title'] );
		$show =  isset( $instance['show'] ) ? absint( $instance['show'] ) : 3;
		?>
<p>
	<label for='<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>'>Title:
		<input class='widefat'
		id='<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>'
		name='<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>'
		type='text' value='<?php echo esc_html( $title ); ?>' />
	</label>
</p>
<p>
	<label for='<?php echo esc_html( $this->get_field_id( 'show' ) ); ?>'>Show:
		<input class='widefat'
		id='<?php echo esc_html( $this->get_field_id( 'show' ) ); ?>'
		name='<?php echo esc_html( $this->get_field_name( 'show' ) ); ?>'
		type='text' value='<?php echo  $show ; ?>' />
	</label>
</p>
<?php
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance ['title'] = $new_instance ['title'];
		$instance ['show'] = (int)$new_instance ['show'];
		return $instance;
	}
	function widget($args, $instance) {
		// and after_title are the array keys." - These are set up by the theme
		extract ( $args, EXTR_SKIP );
		$title = empty ( $instance ['title'] ) ? ' ' : apply_filters ( 'widget_title', $instance ['title'] );
		global $wpdb;
		// These are our own options
		$site_url = get_site_url ();
		$dir = dirname ( plugin_basename ( __FILE__ ) );
		$dirExp = explode ( '/', $dir );
		$dirPage = $dirExp [0];
		?>
<!-- For Getting The Page Id More and Video-->
<?php
		$moreName = $wpdb->get_var ( 'SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE post_content LIKE "%[videomore]%" AND post_status="publish" AND post_type="page" LIMIT 1' );
		$settings_result = $wpdb->get_row ( 'SELECT ratingscontrol,view_visible FROM ' . $wpdb->prefix . 'hdflvvideoshare_settings WHERE settings_id=1' );
		$more_videos_link = get_morepage_permalink ( $moreName, 'recent' );
		?>
<!-- Recent videos -->
<?php
		$fetched = '';
		$ratearray = array (
				'nopos1',
				'onepos1',
				'twopos1',
				'threepos1',
				'fourpos1',
				'fivepos1' 
		);
		$viewslang = __ ( 'Views', 'video_gallery' );
		$viewlang = __ ( 'View', 'video_gallery' );
		echo $before_widget;
		$div = '<div id="recent-videos" class="sidebar-wrap "><h3 class="widget-title"><a href="' .$more_videos_link .'">' . $title . '</a></h3>';
		if ($instance ['show']) {
			if( absint( $instance['show'] ) ) {
				$show = $instance['show'];
			}
			$show = 3;
		} else {
			$show = 3;
		}
		$sql = 'SELECT DISTINCT a.*,s.guid,b.playlist_id,p.playlist_name FROM ' . $wpdb->prefix . 'hdflvvideoshare a
				INNER JOIN ' . $wpdb->prefix . 'hdflvvideoshare_med2play b ON a.vid=b.media_id
				INNER JOIN ' . $wpdb->prefix . 'hdflvvideoshare_playlist p ON p.pid=b.playlist_id
				INNER JOIN ' . $wpdb->prefix . 'posts s ON s.ID=a.slug
				WHERE a.publish=1 AND p.is_publish=1 GROUP BY a.vid ORDER BY a.vid DESC LIMIT ' . $show;
		$posts = $wpdb->get_results ( $sql );
		if (! empty ( $posts )) {
			$fetched = $posts [0]->playlist_name;
		}
		$moreR = $wpdb->get_results ( 'SELECT count(a.vid) as contus from ' . $wpdb->prefix . 'hdflvvideoshare a
				INNER JOIN ' . $wpdb->prefix . 'hdflvvideoshare_med2play b ON a.vid=b.media_id
				INNER JOIN ' . $wpdb->prefix . 'hdflvvideoshare_playlist p ON p.pid=b.playlist_id
				WHERE a.publish=1 AND p.is_publish=1 ORDER BY a.vid DESC' );
		$countR = $moreR [0]->contus;
		$div .= '<ul class="ulwidget">';
		// were there any posts found?
		if (! empty ( $posts )) {
			// posts were found, loop through them
			$image_path = str_replace ( 'plugins/' . $dirPage . '/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL );
			$_imagePath = APPTHA_VGALLERY_BASEURL . 'images' . DS;
			foreach ( $posts as $post ) {
				$file_type = $post->file_type; // Video Type
				$image = $post->image;
				$guid = get_video_permalink ( $post->slug ); // guid
				if ($image == '') { // If there is no thumb image for video
					$image = $_imagePath . 'nothumbimage.jpg';
				} else {
					if ($file_type == 2 || $file_type == 5) { // For uploaded image
 
						if( $file_type ==  2  &&  strpos($image ,  '/'  )) {
							$image = $image;
						}else{
							$image = $image_path . $image;
						}
					}
					if( $file_type ==3 ) {
						$image = $image;
					}
				}
				$name = strlen ( $post->name );
				// output to screen
				$div .= '<li class="clearfix sideThumb">';
				$div .= '<div class="imgBorder"><a href="' . $guid . '" title="'.$post->name.'" ><img src="' . $image . '" alt="' . $post->name . '" class="img" width="120" height="80" style="width: 120px; height: 80px;" /></a>';
				if ($post->duration != 0.00) {
					$div .= '<span class="video_duration">' . $post->duration . '</span>';
				}
				$div .= '</div>';
				$div .= '<div class="side_video_info"><a title="'.$post->name.'" class="videoHname" href="' . $guid . '">';
				if ($name > 25) {
					$div .= substr ( $post->name, 0, 25 ) . '..';
				} else {
					$div .= $post->name;
				}
				$div .= '</a><div class="clear"></div>';
				if ($settings_result->view_visible == 1) {
					if ($post->hitcount > 1) {
						$viewlanguage = $viewslang;
					} else {
						$viewlanguage = $viewlang;
					}
					$div .= '<span class="views">' . $post->hitcount . ' ' . $viewlanguage;
					$div .= '</span>';
				}
				// Rating starts here
				if ($settings_result->ratingscontrol == 1) {
					if (isset ( $post->ratecount ) && $post->ratecount != 0) {
						$ratestar = round ( $post->rate / $post->ratecount );
					} else {
						$ratestar = 0;
					}
					$div .= '<span class="ratethis1 ' . $ratearray [$ratestar] . '"></span>';
				}
				// Rating ends here
				$div .= '<div class="clear"></div>';
				$div .= '</div>';
				$div .= '</li>';
			}
		} else {
			$div .= '<li>' . __ ( 'No recent Videos', 'video_gallery' ) . '</li>';
		}
		// end list
		if (($show < $countR) || ($show == $countR)) {
			$more_videos_link = get_morepage_permalink ( $moreName, 'recent' );
			$div .= '<li><div class="right video-more"><a href="' . $more_videos_link . '">' . __ ( 'More&nbsp;Videos', 'video_gallery' ) . '&nbsp;&#187;</a></div>';
			$div .= '<div class="clear"></div></li>';
		}
		$div .= '</ul></div>';
		echo balanceTags ( $div );
		echo $after_widget;
	}
	// Register widget for use
}

// Run code and init
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Widget_ContusRecentVideos_initEdited");' ) );

// Remove contus widgets
function vh_remove_contus_widgets() {
	unregister_widget('widget_ContusFeaturedVideos_init');
	unregister_widget('Widget_ContusPopularVideos_init');
	unregister_widget('Widget_ContusRecentVideos_init');
	unregister_widget('Widget_ContusRelatedVideos_init');
	unregister_widget('Widget_ContusVideoCategory_init');
}
add_action( 'widgets_init', 'vh_remove_contus_widgets' );

remove_action( 'wp_head', 'add_meta_details', 1);
add_action( 'wp_head', 'vh_add_meta_details', 1);
function vh_add_meta_details() {
	global $post;
	$tags_name = '';
	/** Get video id for meta details */
	$videoID  = pluginVideoID ();
	$output = '<script type="text/javascript">var baseurl = "' . site_url() . '";var adminurl = "' . admin_url() . '";</script>';
	/** If video is not empty then get video details */
	if (! empty ( $videoID )) {
		/** Get video details for given video id */
		$video_count  = videoDetails ( $videoID, '' );
		
		/** Check video details are exist */
		if (! empty ( $video_count )) {
		  /** Get video name  */
		  $videoname    = $video_count->name;
		  if(isset($video_count->tags_name)) {
			/** Get tags name  */
			$tags_name    = $video_count->tags_name;
		  }
		  /** Get swf file URL path  */
		  $swfPath      = APPTHA_VGALLERY_BASEURL . 'hdflvplayer' . DS . 'hdplayer.swf';
		  /** Get video page URL */
		  $videoPageURL = get_video_permalink ( $video_count->slug );
		  /** Get thumb description for og:description */
		  $description      = get_bloginfo('name');
		  if ($video_count->description) {
			  $description  = $video_count->description;
		  }
		  /** Get rating value for rich snippet */
		  $rateSnippet      = getRatingValue ( $video_count->rate, $video_count->ratecount, 'calc' );          
		  /** Get thumb image for og:image */
		  $imageFea         = getImagesValue ( $video_count->image, $video_count->file_type, $video_count->amazon_buckets, '');
		  /** Check video url is YouTube */
		  if (strpos ( $imageFea, 'youtube' ) > 0 || strpos ( $imageFea, 'ytimg' ) > 0) {
			  /** Get YouTube video thumb image */
			  $imgstr       = explode ( '/', $imageFea );
			  $imageFea     = 'http://img.youtube.com/vi/' . $imgstr [4] . '/hqdefault.jpg';
			  $output      .= '<meta property="og:video" content="https://youtube.com/v/'.$imgstr [4].'"/>';
		  }
		  if ( get_option('vh_upload_images') == 'true' ) {
		  	$imageFea = get_post_meta(url_to_postid(get_permalink()), 'video_sd_image', true);
		  	if ( $imageFea == '' ) {
		  		$imageFea = get_post_meta(url_to_postid(get_permalink()), 'video_mq_image', true);
		  		if ( $imageFea == '' ) {
		  			$imageFea         = getImagesValue ( $video_count->image, $video_count->file_type, $video_count->amazon_buckets, '');
		  		}
		  	}
		  }
		  /** Add meta details in the page for current video */
		  $output     .= '<meta name="description" content="' . strip_tags ( $description ) . '" />
			  <meta name="keyword" content="' . $tags_name . '" />
			  <link rel="image_src" href="' . $imageFea . '"/>
			  <link rel="canonical" href="' . $videoPageURL . '"/>
			  <meta property="og:image" content="' . $imageFea . '"/>
			  <meta property="og:type" content="video"/>
			  <meta property="og:url" content="' . $videoPageURL . '"/>
			  <meta property="og:title" content="' . $videoname . '"/>
			  <meta property="og:description" content="' . strip_tags ( $description ) . '"/>
			  <meta name="viewport" content="width=device-width">
			  <meta name="twitter:card" content="summary" />
				<meta name="twitter:title" content="' . $videoname . '" />
				<meta name="twitter:description" content="' . strip_tags ( $description ) . '" />
				<meta name="twitter:image" content="' . $imageFea . '" />';    
		  /** Check if SSL is enabled in site
		   * If it is enbaled then add og:video, og:video:type, 
		   * og:video:secure_url in meta details to play video in facebook */
		  if (is_ssl () && $_SERVER ['SERVER_PORT'] == 443) {
			$output .= '<meta property="og:video:type" content="application/x-shockwave-flash" />
				<meta property="og:video" content="' . $swfPath . '?vid=' . $videoID . '&baserefW=' . APPTHA_VGALLERY_BASEURL . '&embedplayer=true" />
				<meta property="og:video:secure_url" content="' . $swfPath . '?vid=' . $videoID . '&baserefW=' . APPTHA_VGALLERY_BASEURL . '&embedplayer=true" />';
		  }
		  /** Set rich snippet details */        
		  $output .= '<div id="video-container" class="" itemscope itemid="" itemtype="http://schema.org/VideoObject">';
		  $output .= '<link itemprop="url" href="' . $videoPageURL . '"/>';
		  $output .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
		  $output .= '<meta itemprop="ratingValue" content="' . $rateSnippet . '"/>
				  <meta itemprop="ratingCount" content="' . $video_count->ratecount . '"/></div>
				  <div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
				  <meta itemprop="name" content="' . $videoname . '" />
				  <meta itemprop="thumbnail" content="' . $imageFea . '" />
				  <meta itemprop="description" content="' . strip_tags ( $description ) . '" />
			  </div>
			  <meta itemprop="image" content="' . $imageFea . '" />
			  <meta itemprop="thumbnailUrl" content="' . $imageFea . '" />
			  <meta itemprop="embedURL" content="' . $swfPath . '" />
			  </div>';
		}
	}
	/** Display meta details */
	echo $output;
}