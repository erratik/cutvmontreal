// On window load. This waits until images have loaded which is essential
/*global jQuery:false, my_ajax:false, on_resize:false */
/*jshint unused:false */
jQuery(window).load(function() {
	"use strict";

	jQuery('.wpb_thumbnails-fluid').isotope();

	jQuery('.overlay-hide').hide();

	jQuery('#vh_loading_effect').addClass( 'hide' ).delay(500).queue(function(next){
		jQuery(this).hide();
		next();
	});

	if ( jQuery(window).width() > 1200 ) {
		jQuery(window).scroll(function (event) {
			if ( jQuery(window).width() > 1200 ) {
				var scroll = $(window).scrollTop();

				if ( scroll > 700 ) {
					jQuery('.videoplayer.open_video.player').addClass('sidebar');
					setTimeout(function() {
						jQuery('.videoplayer.open_video.player').addClass('animation-ended');
					}, 500);
					setTimeout(function() {
						jQuery('.videoplayer.open_video.player').addClass('show-video');
					}, 300);
				} else {
					jQuery('.videoplayer.open_video.player').removeClass('sidebar');
					jQuery('.videoplayer.open_video.player').removeClass('animation-ended');
					jQuery('.videoplayer.open_video.player').removeClass('show-video');
				}
			}
		});
		
		jQuery('.videoplayer.open_video.player #flashplayer, .video-fade-effect').css('right', Math.ceil(jQuery('.page-wrapper').offset().left+15));
	};

	jQuery(window).bind("debouncedresize", function() {
		if ( jQuery(window).width() > 1200 ) {
			jQuery('.videoplayer.open_video.player #flashplayer, .video-fade-effect').css('right', Math.ceil(jQuery('.page-wrapper').offset().left+15));
		} else {
			jQuery('.videoplayer.open_video.player').removeClass('sidebar');
			jQuery('.videoplayer.open_video.player').removeClass('animation-ended');
			jQuery('.videoplayer.open_video.player').removeClass('show-video');
		}
	});
});

/*
Plugin: jQuery Parallax
Version 1.1.3
Author: Ian Lunn
Twitter: @IanLunn
Author URL: http://www.ianlunn.co.uk/
Plugin URL: http://www.ianlunn.co.uk/plugins/jquery-parallax/

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html
*/

jQuery(document).ready(function($) {
	"use strict";

	if ( jQuery('#buy-now-ribbon').length && window.self === window.top ) {
		jQuery('#buy-now-ribbon').show();
	};

	if ( !jQuery("body").hasClass("vc_responsive") ) {
		jQuery("body").addClass("vc_responsive");
	}

	// Fix top slider tap issue (mobile)
	if ( jQuery('#video_carousel li').length ) {
		var touch_startX = '0';
		var touch_startY = '0';

		jQuery('#video_carousel li').on('touchstart', function(e) {
			touch_startX = e.originalEvent.touches[0].pageX;
			touch_startY = e.originalEvent.touches[0].pageY;
		}).on('touchend', function(e) {
			var touch_endX = e.originalEvent.changedTouches[0].pageX;
			var touch_endY = e.originalEvent.changedTouches[0].pageY;

			// if ( ( touch_startX == touch_endX ) && ( touch_startY == touch_endY ) ) {
				e.preventDefault();
				window.location = jQuery(this).find('a').attr('href');
			// }
		});
	};

	if ( jQuery('.imgSidethumb, .video-block .video_image_container').length ) {
		var touch_startX = '0';
		var touch_startY = '0';

		jQuery('.imgSidethumb, .video-block .video_image_container').on('touchstart', function(e) {
			touch_startX = e.originalEvent.touches[0].pageX;
			touch_startY = e.originalEvent.touches[0].pageY;
		}).on('touchend', function(e) {
			var touch_endX = e.originalEvent.changedTouches[0].pageX;
			var touch_endY = e.originalEvent.changedTouches[0].pageY;

			if ( ( touch_startX == touch_endX ) && ( touch_startY == touch_endY ) ) {
				e.preventDefault();
				window.location = jQuery(this).find('a.view_more').attr('href');
			}
		});
	};

	$.event.special.tap = {
		setup: function(data, namespaces) {
			var $elem = $(this);
			$elem.bind('touchstart', $.event.special.tap.handler)
			.bind('touchmove', $.event.special.tap.handler)
			.bind('touchend', $.event.special.tap.handler);
		},

		teardown: function(namespaces) {
			var $elem = $(this);
			$elem.unbind('touchstart', $.event.special.tap.handler)
			.unbind('touchmove', $.event.special.tap.handler)
			.unbind('touchend', $.event.special.tap.handler);
		},

		handler: function(event) {
			// event.preventDefault();
			var $elem = $(this);
			$elem.data(event.type, 1);
			if (event.type === 'touchend' && !$elem.data('touchmove')) {
				event.type = 'tap';
				$.event.handle.apply(this, arguments);
			} else if ($elem.data('touchend')) {
				$elem.removeData('touchstart touchmove touchend');
			}
		}
	};

	// jQuery("body.shadows .video_c_player.default .video_module .imgSidethumb .video_image_container.sdimg").realshadow({
	// 	followMouse: false,   // default: true
	// 	// pageX: x,             // x coordinate of the light source
	// 	// pageY: y,             // y coordinate of the light source
	// 	 color: '94, 94, 94',    // shadow color, rgb 0..255, default: '0,0,0'
	// 	// type: 'drop' // shadow type
	// 	 angle: 5.5,
	// 	 length: 10,
	// 	 style: 'flat',
	// 	 opacity: 0.2
	// });

	jQuery('.video_container a.video_play, .video-block-container a.video_play').click(function(e) {
		e.preventDefault();
		var top_css = '';

		jQuery('.vh_wrapper').addClass('blur');
		jQuery('.overlay-hide').css({'background-color': 'rgba(0, 0, 0, 0.5)'}).show();
		jQuery(this).parent().find('#video_iframe').attr('src', jQuery(this).parent().find('.iframe_url').val());

		jQuery(this).parent().find('#video_dialog').dialog({ 
			modal: true, 
			width: 640,
			resizable: false,
			dialogClass: "spotlight",
			position: { my: "center center", at: "center center" },
			close: function() {
				jQuery(this).dialog('destroy');
				jQuery('.vh_wrapper').removeClass('blur');
				//jQuery('#video_dialog').remove();
				jQuery('.overlay-hide').removeAttr('style').hide();
			} 
		});
	});

	// Perform AJAX login on form submit
	jQuery('form#login').on('submit', function(e) {
		jQuery('form#login p.status').show().text(ajax_login_object.loadingmessage);
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_login_object.ajaxurl,
			data: { 
				'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
				'username': jQuery('form#login #username').val(), 
				'password': jQuery('form#login #password').val(), 
				'security': jQuery('form#login #security').val()
			},
			success: function(data) {
				jQuery('form#login p.status').text(data.message);
				if (data.loggedin == true) {
					document.location.href = ajax_login_object.redirecturl;
				}
			}
		});
		e.preventDefault();
	});

	jQuery('.morph-button button').on('click', function(e) {
		jQuery('.morph-content, .morph-button').addClass('active');
	});

	// Perform AJAX login on button click
	jQuery(document).on('click', '#login_button', function(e) {
		jQuery('.morph-content').addClass('login_error');
		jQuery('.content-style-form p.status').show().text(ajax_login_object.loadingmessage);
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_login_object.ajaxurl,
			data: { 
				'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
				'username': jQuery('.content-style-form #username').val(), 
				'password': jQuery('.content-style-form #password').val(), 
				'security': jQuery('.content-style-form #security').val()
			},
			success: function(data) {
				jQuery('.content-style-form p.status').text(data.message);
				if (data.loggedin == true) {
					document.location.href = ajax_login_object.redirecturl;
				}
			}
		});
		e.preventDefault();
	});

	// Generate register form
	jQuery(document).on('click', '#register_button_form, #login_button_form', function() {
		jQuery('.content-style-form').addClass('loading');
		jQuery.ajax({
			type: 'POST',
			url: my_ajax.ajaxurl,
			data: {"action": "snaptube_generate_form", form_type: jQuery(this).attr('id') },
			success: function(response) {
				jQuery('.content-style-form').html(response);
				jQuery('.content-style-form').removeClass('loading login_form');
				jQuery('.content-style-form').addClass('register_form');
				jQuery('.morph-content').removeClass('login_error register_error');

				return false;
			}
		});
	});

	jQuery(document).on('click', '#register_button', function() {
		jQuery('.morph-content').addClass('register_error');
		jQuery('.content-style-form .status').show().text(ajax_login_object.loadingmessage);
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: my_ajax.ajaxurl,
			data: { 
				'action': 'ajax_register',
				'email': jQuery('.content-style-form #email').val(),
				'fullname': jQuery('.content-style-form #username').val(),
				'regsecurity': jQuery('.content-style-form #regsecurity').val()
			},
			success: function(data) {
				jQuery('.content-style-form .status').show().text(data.message);
				if ( data.for_input == "main" ) {
					jQuery('.content-style-form input[type="text"]').val('');
				}
			}
		});
	});

	var $isotope_container = jQuery(".blog .wpb_thumbnails");

	$isotope_container.isotope({ straightAcross : true });

	// update columnWidth on window resize
	jQuery(window).bind("debouncedresize", function() {
		$isotope_container.isotope({

			// update columnWidth to a percentage of container width
			masonry: { columnWidth: $isotope_container.width() / 2 }
		});

		if ( jQuery(window).width() <= 767 ) {
			jQuery(".video-module-title").each(function(i, val) {
				if (jQuery(this).val() == '&nbsp;') {
					jQuery(this).hide();
				}
			});
		}
	});

	jQuery("ul.video_module li .video_container").hover(function() {
		jQuery(this).find(".imgSidethumb").animate({'background-size':'80px 80px'}, 250);
	});

	jQuery(".scroll-to-top").click(function() {
		jQuery("html, body").animate({ scrollTop: 0 }, "slow");
		return false;
	});

	if ( jQuery('body').hasClass('search-results') ) {
		jQuery(window).bind("debouncedresize", function() {
			jQuery('.wpb_thumbnails').isotope();
		});
	};

	jQuery('.package_button').click(function(e) {
		e.preventDefault();
		jQuery(this).parent().parent().find('input:radio').prop('checked', true);
		jQuery('#job_package_selection').submit();
	});

	(function() {
		var docElem = window.document.documentElement, didScroll, scrollPosition;

		// trick to prevent scrolling when opening/closing button
		function noScrollFn() {
			window.scrollTo( scrollPosition ? scrollPosition.x : 0, scrollPosition ? scrollPosition.y : 0 );
		}

		function noScroll() {
			window.removeEventListener( 'scroll', scrollHandler );
			window.addEventListener( 'scroll', noScrollFn );
		}

		function scrollFn() {
			window.addEventListener( 'scroll', scrollHandler );
		}

		function canScroll() {
			window.removeEventListener( 'scroll', noScrollFn );
			scrollFn();
		}

		function scrollHandler() {
			if( !didScroll ) {
				didScroll = true;
				setTimeout( function() { scrollPage(); }, 60 );
			}
		};

		function scrollPage() {
			scrollPosition = { x : window.pageXOffset || docElem.scrollLeft, y : window.pageYOffset || docElem.scrollTop };
			didScroll = false;
		};

		scrollFn();

		[].slice.call( document.querySelectorAll( '.morph-button' ) ).forEach( function( bttn ) {
			new UIMorphingButton( bttn, {
				closeEl : '.icon-close',
				onBeforeOpen : function() {
					// don't allow to scroll
					jQuery('.header-icon').first().find('a').css('border-right', '1px solid #eaeaea');
					noScroll();
				},
				onAfterOpen : function() {
					// can scroll again
					canScroll();
				},
				onBeforeClose : function() {
					// don't allow to scroll
					noScroll();
				},
				onAfterClose : function() {
					// can scroll again
					jQuery('.morph-content').removeClass('login_error');
					jQuery('p.status').hide();
					jQuery('.header-icon').first().find('a').css('border-right', 'transparent')
					canScroll();
				}
			} );
		} );

		// for demo purposes only
		[].slice.call( document.querySelectorAll( 'form button' ) ).forEach( function( bttn ) { 
			bttn.addEventListener( 'click', function( ev ) { ev.preventDefault(); } );
		} );
	})();

	jQuery('.icon-user.user-icon').click(function(e) {
		e.preventDefault();
	});

	var baseurl;
	baseurl = '<?php echo $site_url; ?>';

	function currentVideo(vid,videoids){
	for(var i = 0; i < videoids.length; i++){
		if(videoids[i]!=vid){
			var prev_fragment = document.getElementById('nav-fragment-'+videoids[i])
			prev_fragment.className = "ui-tabs-nav-item" ;
		}
	}
		var fragment = document.getElementById('nav-fragment-'+vid)
		fragment.className += " ui-tabs-selected" ;
	}

	jQuery('.switch_featured_video').click(function(e) {
		e.preventDefault();

		jQuery(".lof-snleft").animate({opacity: 0}, 0);

		var vid = jQuery(this).attr('id');
		var lt=false;

		sourceCode = document.getElementById(vid).innerHTML;
		embedCode  = sourceCode.replace('embecontus','embed');
		embedCode  = embedCode.replace('iframcontus','iframe');
		embedCode  = embedCode.replace('videcontus','video');
		if(lt==true){
			embedCode  = sourceCode.replace('EMBECONTUS','EMBED');
			embedCode  = embedCode.replace('IFRAMCONTUS','IFRAME');
			embedCode  = embedCode.replace('VIDECONTUS','IFRAME');
		}
		document.getElementById("nav-"+vid).className = 'ui-tabs-nav-item ui-tabs-selected';
		var removeSelectItem = document.getElementById("activeCSS").value;
		document.getElementById("nav-"+removeSelectItem).className = 'ui-tabs-nav-item';
		document.getElementById('videoPlay').innerHTML = embedCode;
		document.getElementById("activeCSS").value = vid;

		setTimeout(function() {
			jQuery(".lof-snleft").animate({opacity: 1}, 300);
		}, 400);
	});

	header_size();

	jQuery("#allowEmbed").click(function() {
		jQuery(this).toggleClass("embed-open");
		var embedFlag = document.getElementById("flagembed").value;
		if( embedFlag != 1 ) {
			jQuery("#embedcode").slideToggle();
			document.getElementById("flagembed").value = "1";
		} else {
			jQuery("#embedcode").slideToggle();
			document.getElementById("flagembed").value = "0";
		}
	});

	// if (!jQuery('.video_player.vid_thumbnail .video-module-title').attr('class').length) {
	// 	jQuery('.video_player.vid_thumbnail').css('margin-top', '37px');
	// };

	jQuery(".post-grid-item-wrapper").each(function(){
		if ( jQuery(this).children().first().hasClass('post-title') ) {
			jQuery(this).addClass('top_title');
		};
	});

	jQuery('.video-cat-thumb .vc_separator').click(function() {
		if ( !jQuery('.video-page-desc-wrapper').hasClass('active') ) {
			jQuery('.video-page-desc-wrapper').css("max-height", "none");
			var height = jQuery('.video-page-desc').height();
			jQuery('.video-page-desc-wrapper').css("max-height", "75px");
			jQuery('.video-page-desc-wrapper').animate({
				"max-height": height
			}, {
				duration: 200,
				easing: 'linear',
				queue: false,
				complete: function(){ 
					jQuery('.video-page-desc-wrapper').addClass('active');
					jQuery('.open_video_more').html(follow_button_text.showless);
					jQuery('.open_video_more').removeClass('icon-angle-down');
					jQuery('.open_video_more').addClass('icon-angle-up');
				}
			});
		} else {
			jQuery('.video-page-desc-wrapper').removeClass('active'); 
			jQuery('.video-page-desc-wrapper').animate({
				"max-height": "75px"
			}, {
				duration: 200,
				easing: 'linear',
				queue: false,
				complete: function(){ 
					jQuery('.open_video_more').html(follow_button_text.showmore);
					jQuery('.open_video_more').removeClass('icon-angle-up');
					jQuery('.open_video_more').addClass('icon-angle-down');
				}
			});
		}
	});

	jQuery('.video-page-desc #allowReport').click(function() {
		if ( !jQuery(this).hasClass('active') ) {
			var initialheight = jQuery('.video-page-desc').height();
			jQuery('#reportform').show();
			jQuery('.video-page-desc-wrapper').css("max-height", "none");
			var height = jQuery('.video-page-desc').height();
			jQuery('.video-page-desc-wrapper').css("max-height", initialheight);
			jQuery('.video-page-desc-wrapper').animate({
				"max-height": height
			}, {
				duration: 200,
				easing: 'linear',
				queue: false,
				complete: function(){ 
					jQuery('.video-page-desc-wrapper').addClass('active');
					jQuery('.open_video_more').html('Show less');
					jQuery('.open_video_more').removeClass('icon-angle-down');
					jQuery('.open_video_more').addClass('icon-angle-up');
				}
			});
		} else {
			// jQuery('.video-page-desc-wrapper').removeClass('active'); 
			// jQuery('.video-page-desc-wrapper').animate({
			// 	"max-height": "75px"
			// }, {
			// 	duration: 200,
			// 	easing: 'linear',
			// 	queue: false,
			// 	complete: function(){ 
			// 		jQuery('.open_video_more').html('Show more');
			// 		jQuery('.open_video_more').removeClass('icon-angle-up');
			// 		jQuery('.open_video_more').addClass('icon-angle-down');
			// 	}
			// });
		}
	});

	jQuery(".open_video_likes span").click(function() {
		if (!jQuery('.open_video_likes span').hasClass('liked')) {
			jQuery(".open_video_likes_count").text(parseInt(jQuery(".open_video_likes_count").text())+1)
		};
		jQuery(".open_video_likes span").addClass('liked');
		jQuery(".open_video_likes span").text(follow_button_text.youlikeit);
	});

	jQuery(".open_video_share .open-share-buttons").click(function() {
		jQuery(".video-socialshare").toggle("slide");
	});

	if (jQuery.cookie('vh_open_video_carousel') == null) {
		jQuery.cookie('vh_open_video_carousel', '1', { path: '/' });
	};

	if (jQuery.cookie('vh_open_video_carousel') == 0) {
		jQuery(".single-videogallery #video_jcarousel").slideToggle(1);
		jQuery(".single-videogallery .page-wrapper.video").addClass('closed');
		jQuery(".single-videogallery .video_carousel_button").addClass('icon-angle-down');
	};

	jQuery(".single-videogallery .video_carousel_button").click(function() {
		jQuery("#video_jcarousel").slideToggle(200);
		jQuery(".single-videogallery .video_carousel_button").toggleClass('icon-angle-down');
		jQuery(".page-wrapper.video").toggleClass('closed');
		if (jQuery.cookie('vh_open_video_carousel') == 0) {
			jQuery.cookie('vh_open_video_carousel', '1', { path: '/' });
		} else {
			jQuery.cookie('vh_open_video_carousel', '0', { path: '/' });
		};
	});

	// Contacts page arrow (open/close map)
	if (jQuery.cookie('vh_open_map') == null) {
		jQuery.cookie('vh_open_map', '1', { path: '/' });
	};

	if (jQuery.cookie('vh_open_map') == 0) {
		jQuery("#map").slideToggle(1);
		jQuery(".page-wrapper").addClass('closed');
		jQuery(".page-template-template-contacts-php .video_carousel_button").addClass('icon-angle-down');
	};

	jQuery(".page-template-template-contacts-php .video_carousel_button").click(function() {
		jQuery("#map").slideToggle(200);
		jQuery(".page-template-template-contacts-php .video_carousel_button").toggleClass('icon-angle-down');
		jQuery(".page-wrapper").toggleClass('closed');
		if (jQuery.cookie('vh_open_map') == 0) {
			jQuery.cookie('vh_open_map', '1', { path: '/' });
		} else {
			jQuery.cookie('vh_open_map', '0', { path: '/' });
		};
	});

	jQuery( function() {
		var containers = jQuery('#video_carousel').isotope({
			itemSelector: '.item',
			layoutMode: 'masonryHorizontal'
		})
	});

	if ( jQuery('#video_carousel').length != 0 ) {

		if ( jQuery('#video_carousel li').length == 0 ) {
			jQuery('.video_carousel_container').hide();
			jQuery('.page-wrapper').removeClass('video');
		};

		jQuery("#video_jcarousel").smoothDivScroll({
			mousewheelScrolling: true,
			autoScrollingMode: "onStart",
			touchScrolling: true
		});

		jQuery("#video_jcarousel").bind("mouseover", function(){
			jQuery("#video_jcarousel").smoothDivScroll("stopAutoScrolling");
		});

		// Mouse out
		jQuery("#video_jcarousel").bind("mouseout", function(){
			jQuery("#video_jcarousel").smoothDivScroll("startAutoScrolling");
		});

	}

	if ( jQuery('.featured-videos-slider').length != 0 ) {

		if ( jQuery('#video_carousel li').length == 0 ) {
			jQuery('.video_carousel_container').hide();
			jQuery('.page-wrapper').removeClass('video');
		};

		jQuery("#video_jcarousel").smoothDivScroll({
			touchScrolling: true
		});

	}

	jQuery('.follow-category').click(function() {
		vh_update_categories(jQuery(this).parent().find('input[type="hidden"]').val(), 'add', jQuery(this), my_ajax.ajaxurl)
	});

	jQuery('.unfollow-category').click(function() {
		vh_update_categories(jQuery(this).parent().find('input[type="hidden"]').val(), 'delete', jQuery(this), my_ajax.ajaxurl)
	});

	function vh_update_categories(id,action,button,ajaxurl) {
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "followed_categories", category_id: id, follow_action: action},
			success: function(response) {
				if ( button.hasClass('follow-category') ) {
					button.removeClass('follow-category');
					button.removeClass('icon-plus-circled');
					button.addClass('unfollow-category');
					button.addClass('icon-minus-circled');
					button.html(follow_button_text.unfollow);
				} else {
					button.removeClass('unfollow-category');
					button.removeClass('icon-minus-circled');
					button.addClass('follow-category');
					button.addClass('icon-plus-circled');
					button.html(follow_button_text.follow);
				}
				return false;
			}
		});
	}

	jQuery( function() {
		var $container_follow = jQuery('.video-block-container.followed');
		var $container = jQuery('.video-block-container.open-video:not(.followed)');
		if ( $container.hasClass('follow') ) {
			$container_follow.isotope({
				itemSelector: '.video-block',
				layoutMode: 'fitRows',
				transformsEnabled: true,
				animationOptions: {
					duration: 250,
					easing: 'swing',
					queue: true
				},
				animationEngine : "jquery"
			});
		} else {
			$container.isotope({
				itemSelector: '.video-block',
				layoutMode: 'fitRows',
				transformsEnabled: true,
				getSortData: {
					title: function ( elem ) {
						return jQuery(elem).find('.videoHname.title').text();
					},
					date: function ( elem ) {
						var some_date = jQuery(elem).find('.v_date').val();
						var date_string = some_date.split(' ');
						var date_parts = date_string['0'].split('-');
						var time_parts = date_string['1'].split(':');
						var ts = new Date(date_parts['0'], date_parts['1'], date_parts['2'], time_parts['0'], time_parts['1'], time_parts['2']);
						return Date.parse(ts);
					},
					views: function ( elem ) {
						return parseFloat( jQuery(elem).find('.video_info .video_views').text());
					},
					likes: function ( elem ) {
						return parseFloat( jQuery(elem).find('.video_info .video_likes').text());
					}
				},
				sortBy: 'date',
				sortAscending: false,
				animationOptions: {
					duration: 250,
					easing: 'swing',
					queue: true
				},
				animationEngine : "jquery"
			});
		}

		jQuery(".category_layout_container .layout.list").click(function() {
			if (!jQuery(this).hasClass('clicked')) {
				jQuery('.category_layout_container .layout.table').removeClass('clicked');
				jQuery(this).addClass('clicked');

				jQuery(".video-block-container-wrapper").fadeTo( 100, 0, function() {
					jQuery.cookie('vh_category_layout', 'list', { path: '/' });
					jQuery(".video-block").addClass('new_style');

					setTimeout(function() {
						$container.isotope();
						$container_follow.isotope();
						jQuery(".video-block-container-wrapper").fadeTo( 400, 1 );
					}, 400);
				});
			}
		});

		jQuery(".category_layout_container .layout.table").click(function() {
			if (!jQuery(this).hasClass('clicked')) {
				jQuery('.category_layout_container .layout.list').removeClass('clicked');
				jQuery(this).addClass('clicked');

				jQuery(".video-block-container-wrapper").fadeTo( 100, 0, function() {
					jQuery.cookie('vh_category_layout', 'table', { path: '/' });
					jQuery(".video-block").removeClass('new_style');

					setTimeout(function() {
						$container.isotope();
						$container_follow.isotope();
						jQuery(".video-block-container-wrapper").fadeTo( 400, 1 );
					}, 400);
				});
			}
		});

		// bind sort button click
		jQuery('#sorts').on( 'click', 'div:not(.sort_by)', function() {
			var sortValue = jQuery(this).attr('data-sort-value');
			if (sortValue == 'views' || sortValue == 'date' || sortValue == 'likes' ) {
				$container.isotope({
					sortBy: sortValue,
					sortAscending: false
				});
			} else {
				$container.isotope({
					sortBy: sortValue,
					sortAscending: true
				});
			}

			if ( sortValue == 'views' ) {
				jQuery.cookie('vh_category_sorting', 'views', { path: '/' });
			} else if ( sortValue == 'date' ) {
				jQuery.cookie('vh_category_sorting', 'date', { path: '/' });
			} else if ( sortValue == 'title' ) {
				jQuery.cookie('vh_category_sorting', 'title', { path: '/' });
			} else if ( sortValue == 'likes' ) {
				jQuery.cookie('vh_category_sorting', 'likes', { path: '/' });
			}
		});

		jQuery('.category_sort_container').each( function( i, divGroup ) {
			var divGroup = jQuery( divGroup );
			divGroup.on( 'click', 'div:not(.sort_by)', function() {
				divGroup.find('.clicked').removeClass('clicked');
				jQuery( this ).addClass('clicked');
			});
		});
	});

	jQuery( function() {
		var container = jQuery('.video-block-container.open-video');
		var sortValue = jQuery.cookie('vh_category_sorting');

		if ( sortValue == null ) {
			jQuery.cookie('vh_category_sorting', 'date', { path: '/' });
		} else if ( sortValue == 'date' || sortValue == 'views' || sortValue == 'likes' ) {
			if ( !container.hasClass('followed') ) {
				jQuery('.video-block-container.open-video').isotope({
					sortBy: sortValue,
					sortAscending: false
				});
			} else {
				jQuery('.video-block-container.open-video').isotope();
			}
		} else if ( sortValue == 'title' ) {
			if ( !container.hasClass('followed') ) {
				jQuery('.video-block-container.open-video').isotope({
					sortBy: sortValue,
					sortAscending: true
				});
			} else {
				jQuery('.video-block-container.open-video').isotope();
			}
		}

		if ( sortValue == 'date' ) {
			jQuery('.sort_param').removeClass('clicked');
			jQuery('.sort_param.date').addClass('clicked');
		} else if ( sortValue == 'views' ) {
			jQuery('.sort_param').removeClass('clicked');
			jQuery('.sort_param.views').addClass('clicked');
		} else if ( sortValue == 'title' ) {
			jQuery('.sort_param').removeClass('clicked');
			jQuery('.sort_param.title').addClass('clicked');
		} else if ( sortValue == 'likes' ) {
			jQuery('.sort_param').removeClass('clicked');
			jQuery('.sort_param.likes').addClass('clicked');
		}

		if ( jQuery.cookie('vh_category_layout') == 'list' ) {
			jQuery('.category_layout_container .layout.table').removeClass('clicked');
			jQuery('.category_layout_container .layout.list').addClass('clicked');
			jQuery(".category-videos .video-block").addClass('new_style');

			setTimeout(function() {
				jQuery('.category-videos.video-block-container.open-video').isotope();
			}, 500);
		} else if ( jQuery.cookie('vh_category_layout') == 'table' ) {
			jQuery('.category_layout_container .layout.list').removeClass('clicked');
			jQuery('.category_layout_container .layout.table').addClass('clicked');
			jQuery(".category-videos .video-block").removeClass('new_style');

			setTimeout(function() {
				jQuery('.category-videos.video-block-container.open-video').isotope();
			}, 500);
		}
	});

	jQuery( function() {
		setTimeout(function() {
		var $home_container = jQuery('.video-block-container.video-home').isotope({
			itemSelector: '.video-block',
			layoutMode: 'fitRows',
			transformsEnabled: true,
			animationOptions: {
				duration: 250,
				easing: 'swing',
				queue: true
			},
			animationEngine : "jquery"
		});
		}, 100);
	});

	jQuery(".tagcloud").each(function(index){
		var otags_a = jQuery(this).find("a"),
		otags_number = otags_a.length,
		otags_increment = 1 / otags_number,
		otags_opacity = "";

		jQuery(otags_a.get().reverse()).each(function(i,el) {
		el.id = i + 1;
		otags_opacity = el.id / otags_number - otags_increment;
		if (otags_opacity < 0.2)
			otags_opacity = 0.2;
		jQuery(this).css({ backgroundColor: 'rgba(150,150,150,'+otags_opacity+')' });
		});
	});

	if ( jQuery(window).width() >= 767 ) {
		jQuery("a.menu-trigger").click(function() {
			jQuery(".mp-menu").css({top: jQuery(document).scrollTop() });

			return false;
		});
	}

	jQuery(".fixed_menu .social-container").css({ 'top' : (jQuery(window).height()) - ( jQuery(".fixed_menu .social-container").height() + 60 ) });

	jQuery(".gallery-icon a").attr('rel', 'prettyphoto');

	jQuery("a[rel^='prettyPhoto']").prettyPhoto();

	jQuery(".hover_bottom_top")
	.mouseenter(function(){
		jQuery(this).animate({ bottom: "10px", opacity: "0.8" }, 300, function() {
			
		});
	}).mouseleave(function(){
		jQuery(this).animate({ bottom: "0px", opacity: "1" }, 300, function() {
			
		});
	});

	jQuery(".hover_top_to_bottom")
	.mouseenter(function(){
		jQuery(this).animate({ top: "3px", opacity: "0.8" }, 200, function() {
			
		});
	}).mouseleave(function(){
		jQuery(this).animate({ top: "0px", opacity: "1" }, 200, function() {
			
		});
	});

	jQuery(".snaptube-recentpostsplus.widget .news-item").last().css({"background":"transparent", "padding":"0", "marginBottom":"0"});
	jQuery(".snaptube-twitter.widget .tweet_list li").last().css({"background":"transparent", "padding":"0", "marginBottom":"0"});

	// Opacity hover effect
	jQuery(".opacity_hover").mouseenter(function() {
		var social = this;
		jQuery(social).animate({ opacity: "0.8" }, 80, function() {
			jQuery(social).animate({ opacity: "1.0" }, 80);
		});
	});

	var $window = $(window);
	var windowHeight = $window.height();

	$window.resize(function () {
		windowHeight = $window.height();
		jQuery(".fixed_menu .social-container").css({ 'top' : (jQuery(window).height()) - ( jQuery(".fixed_menu .social-container").height() + 60 ) });
	});

	/**
	 * jQuery.LocalScroll - Animated scrolling navigation, using anchors.
	 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
	 * Dual licensed under MIT and GPL.
	 * Date: 3/11/2009
	 * @author Ariel Flesler
	 * @version 1.2.7
	 **/
	;(function($){var l=location.href.replace(/#.*/,'');var g=$.localScroll=function(a){$('body').localScroll(a)};g.defaults={duration:1e3,axis:'y',event:'click',stop:true,target:window,reset:true};g.hash=function(a){if(location.hash){a=$.extend({},g.defaults,a);a.hash=false;if(a.reset){var e=a.duration;delete a.duration;$(a.target).scrollTo(0,a);a.duration=e}i(0,location,a)}};$.fn.localScroll=function(b){b=$.extend({},g.defaults,b);return b.lazy?this.bind(b.event,function(a){var e=$([a.target,a.target.parentNode]).filter(d)[0];if(e)i(a,e,b)}):this.find('a,area').filter(d).bind(b.event,function(a){i(a,this,b)}).end().end();function d(){return!!this.href&&!!this.hash&&this.href.replace(this.hash,'')==l&&(!b.filter||$(this).is(b.filter))}};function i(a,e,b){var d=e.hash.slice(1),f=document.getElementById(d)||document.getElementsByName(d)[0];if(!f)return;if(a)a.preventDefault();var h=$(b.target);if(b.lock&&h.is(':animated')||b.onBefore&&b.onBefore.call(b,a,f,h)===false)return;if(b.stop)h.stop(true);if(b.hash){var j=f.id==d?'id':'name',k=$('<a> </a>').attr(j,d).css({position:'absolute',top:$(window).scrollTop(),left:$(window).scrollLeft()});f[j]='';$('body').prepend(k);location=e.hash;k.remove();f[j]=d}h.scrollTo(f,b).trigger('notify.serialScroll',[f])}})(jQuery);
});

function header_size() {

	jQuery(window).on('touchmove', function(event) {
		set_height();
	});
	var win    = jQuery(window),
	header     = jQuery('.header .top-header'),
	logo       = jQuery('.header .top-header .logo img'),
	elements   = jQuery('.header, .top-header .header-social-icons div a, .top-header .logo, .top-header .header_search, .header_search .search .gray-form .footer_search_input, .top-header .menu-btn.icon-menu-1, .morph-button'),
	el_height  = jQuery(elements).filter(':first').height(),
	isMobile   = 'ontouchstart' in document.documentElement,
	set_height = function() {
		var st = win.scrollTop(), newH = 0;

		if(st < el_height/2) {
			newH = el_height - st;
			header.removeClass('header-small');
		} else {
			newH = el_height/2;
			header.addClass('header-small');
		}

		jQuery('.morph-button style').html('.morph-content.active{height: '+newH+'px;}.morph-button.active{height: '+newH+'px !important;}');

		elements.css({'height': newH + 'px', 'line-height': newH + 'px'});
		logo.css({'max-height': newH + 'px'});
	}

	if(!header.length) {
		return false;
	}

	win.scroll(set_height);
	set_height();
}

// debulked onresize handler

function on_resize(c,t){
	"use strict";

	var onresize=function(){clearTimeout(t);t=setTimeout(c,100);};return c;
}


function clearInput (input, inputValue) {
	"use strict";

	if (input.value === inputValue) {
		input.value = '';
	}
}

// function moveOffset() {
// 	if( jQuery(".full-width").length ) {
// 		var offset = jQuery(".full-width").position().left;
// 		jQuery(".full-width").css({
// 			width: jQuery('.main').width(),
// 			marginLeft: -offset
// 		});
// 	};
// };

jQuery(document).ready(function() {
	"use strict";

	// Top menu
	if( jQuery(".header .sf-menu").length ) {
		var menuOptions = {
			speed:      'fast',
			speedOut:   'fast',
			hoverClass: 'sfHover',
		}
		// initialise plugin
		var menu = jQuery('.header .sf-menu').superfish(menuOptions);
	}
	// !Top menu
	
	jQuery(".sidebar_menu ul.primary-menu > li.menu-item").mouseover(function(){
		var isHovered = !!jQuery(this).find('ul li').filter(function(){
			return jQuery(this).is(":hover");
		}).length;

		if (isHovered == false) {
			//jQuery(this).children('a').css({'padding-right': '44px'});
			jQuery(this).addClass('before-css');
		}
	}).mouseout(function() {
		//jQuery(this).children('a').css({'padding-right': '40px'});
		jQuery(this).removeClass('before-css');
	});

	jQuery(".sidebar_menu ul.primary-menu > li ul li.menu-item").hover(function(){
		// jQuery(this).children('a').css({'padding-right': '45px'});
		jQuery(this).addClass('before-css');
	}, function() {
		// jQuery(this).children('a').css({'padding-right': '40px'});
		jQuery(this).removeClass('before-css');
	});

	// Search widget
	jQuery('.search.widget .sb-icon-search').click(function(el){
		el.preventDefault();
		jQuery('.search.widget form').submit();
	});
	// !Seaarch widget

	// Search widget
	jQuery('.search-no-results .main-inner .sb-icon-search').click(function(el){
		el.preventDefault();
		jQuery('.search-no-results .main-inner .search form').submit();
	});
	// !Seaarch widget
	

	// Social icons hover effect
	jQuery(".social_links li a").mouseenter(function() {
		var social = this;
		jQuery(social).animate({ opacity: "0.5" }, 250, function() {
			jQuery(social).animate({ opacity: "1.0" }, 100);
		});
	});
	// !Social icons hover effect

	// Widget contact form - send
	jQuery("#contact_form").submit(function() {
		jQuery("#contact_form").parent().find("#error, #success").hide();
		var str = jQuery(this).serialize();
		jQuery.ajax({
			type: "POST",
			url: my_ajax.ajaxurl,
			data: 'action=contact_form&' + str,
			success: function(msg) {
				if(msg === 'sent') {
					jQuery("#contact_form").parent().find("#success").fadeIn("slow");
				} else {
					jQuery("#contact_form").parent().find("#error").fadeIn("slow");
				}
			}
		});
		return false;
	});
	// !Widget contact form - send

	/* Merge gallery */
	jQuery('.merge-gallery div').mouseenter(function() {
		jQuery(this).find('.gallery-caption').animate({
			bottom: jQuery(this).find('img').height()
		},250);
	}).mouseleave(function() {
		jQuery(this).find('.gallery-caption').animate({
			bottom: jQuery(this).find('img').height() + 150
		},250);
	});
});