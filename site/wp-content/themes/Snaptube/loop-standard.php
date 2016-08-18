<?php
/**
 * The default template for displaying content
 *
 * @package WordPress
 * @subpackage Snaptube
 */

global $vh_from_home_page, $post;

$tc          = 0;
$excerpt     = get_the_excerpt();
$img         = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large-image');
$post_date_d = get_the_date( 'd', $post->ID );
$post_date_y = get_the_date( 'M, Y', $post->ID );

if ( $vh_from_home_page == TRUE ) {
	$span_class_index = 'vc_col-sm-6';
} else {
	if(LAYOUT != 'sidebar-no') {
		$span_class_index = 'vc_col-sm-9';
	} else {
		$span_class_index = 'vc_col-sm-12';
	}
}

$tags      = "";
$z         = 0;
$tag_count = count(get_the_tags( $post->ID ));

if (empty($_GET['type'])) {
	$_GET['type'] = '';
}

if ( get_the_tags( $post->ID ) !== false ) {
	foreach (get_the_tags( $post->ID ) as $tag) {
		if ( $z != $tag_count - 1 ) {
			$tags .= "<a href=" . get_tag_link( $tag->term_id ) . ">" . $tag->name . "</a>, ";
		} else {
			$tags .= "<a href=" . get_tag_link( $tag->term_id ) . ">" . $tag->name . "</a>";
		}
		$z++;
	}
}

$video_class = '';
if ( get_post_type() == 'videogallery' ) {
	$video_class = " videogallery";
}
?>									
				<?php if ( get_post_type() != 'videogallery' ) { ?>
					<li class="isotope-item <?php echo $span_class_index . $video_class; ?>">
					<div class="post-grid-item-wrapper">
						<div  <?php post_class(); ?>>
					<?php if ( empty($img[0]) ) { ?>
						<h2 class="post-title_nothumbnail">
							<a class="link_title" href="<?php echo get_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'vh'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a>
						</h2>
						<?php if ( get_the_tags( $post->ID ) !== false ) { ?>
						<div class="blog_info_box nothumbnail">
							<div class="category-link">
								<i class="entypo_icon icon-folder"></i>
								<?php echo get_the_category_list(', ', '', $post->ID); ?> 
							</div>
							<div class="tag-link">
								<i class="entypo_icon icon-tags"></i>
								<?php echo $tags; ?>
							</div>
						</div>
						<?php } ?>
					<?php } else { ?>
						<div class="post-thumb">
							<div class="post-thumb-img-wrapper">
								<a class="link_image" href="<?php echo get_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'vh'), the_title_attribute('echo=0')); ?>">
									<img src="<?php echo $img[0]; ?>" alt="">
								</a>
							</div>
							<div class="post-info-box">
								<?php $tc = wp_count_comments($post->ID); ?>
								<div class="vc_post_comments icon-comment"><?php echo $tc->total_comments; ?></div>
								<?php 
								if ( function_exists('get_post_ul_meta') ) { ?>
										<div class="vc_post_likes icon-heart"><?php echo get_post_ul_meta($post->ID,"like"); ?></div>
								<?php }; ?>
							</div>
						</div>
						<div class="post-date">
							<div class="post-date-container">
								<div class="post-date-left">
									<div class="post-date-day"><?php echo $post_date_d; ?></div>
									<div class="post-date-year"><?php echo $post_date_y; ?></div>
								</div>
								<div class="blog_postedby">
									<?php echo get_avatar(get_the_author_meta( 'ID' )); ?>
								</div>
							</div>
							<div class="blog-postedby-info">
								<?php
								global $wpdb;
								$where_comments = 'WHERE comment_approved = 1 AND user_id = ' . get_the_author_meta( 'ID' );
								$comment_count = $wpdb->get_var("SELECT COUNT( * ) AS total FROM {$wpdb->comments} {$where_comments}");

								?>
								<a href="<?php echo get_author_posts_url( get_post_field( "post_author", $post->ID ) );?>"><?php echo __('by ', 'vh') . get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name;?></a>
								<div class="clearfix"></div>
								<div class="author-posts"><span class="icon-megaphone"><?php echo count_user_posts( get_the_author_meta( 'ID' ) ) . __(' blog entries', 'vh'); ?></span></div>
								<div class="author-comments"><span class="icon-comment"><?php echo $comment_count . __(' comments', 'vh'); ?></div>
							</div>
						</div>
						<h2 class="post-title">
							<a class="link_title" href="<?php echo get_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'vh'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a>
						</h2>
						<?php if ( get_the_tags( $post->ID ) !== false ) { ?>
						<div class="blog_info_box">
							<div class="category-link">
								<i class="entypo_icon icon-folder"></i>
								<?php echo get_the_category_list(', ', '', $post->ID); ?> 
							</div>
							<div class="tag-link">
								<i class="entypo_icon icon-tags"></i>
								<?php echo $tags; ?>
							</div>
						</div>
						<?php } ?>
					<?php } ?>

					<?php
					$extra_class = '';
					if ( empty($img[0]) ) {
						$extra_class = 'nothumbnail';
					}
					?>

					<div class="entry-content <?php echo $extra_class; ?>">
						<?php
							if ( is_search() ) {
								if( empty($excerpt) ) {
									_e( 'No excerpt.', 'vh' );
								} else {
									the_excerpt();
								}
							} else {
								the_excerpt(__('Read more', 'vh'));
							}
						?>
					</div>
						</div>
					</div>
				</li>
				<?php } else {
						global $wpdb,$post;
						$video_table = $wpdb->prefix.'hdflvvideoshare';
						$video_slug = $post->ID;
						if ( get_query_var('all_videos') == 'true' ) {
							// All author videos
							$sid = '';
							preg_match_all('!\d+!', $post->post_content, $sid);
							$video = $wpdb->get_results("SELECT * FROM {$video_table} WHERE vid = ".$sid['0']['0']." AND publish = '1'");
						} else {
							// Joined search videos
							$sid = '';
							preg_match_all('!\d+!', $post->post_content, $sid);
							$video = $wpdb->get_results("SELECT * FROM {$video_table} WHERE vid = ".$sid['0']['0']." AND publish = '1'");
							if ( empty($video) ) {
								$video = $wpdb->get_results("SELECT * FROM {$video_table} WHERE vid = ".$post->ID." AND publish = '1'");
							}
							$video_post = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_content = '[hdvideo id=".$post->ID."]'");
							if ( !empty($video_post) ) {
								$post = get_post($video_post['0']->ID);
								setup_postdata($post);
							}
						}
						if ( !empty($video) ) {
						?>
						<li class="isotope-item <?php echo $span_class_index . $video_class; ?>">
						<div class="post-grid-item-wrapper">
							<div  <?php post_class(); ?>>
						<div class="post-thumb">
							<?php
							$image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
							$imageFea   = str_replace("/mq","/sd",$video[0]->image);
							$file_type  = $video[0]->file_type;

							if ( !empty($imageFea) ) {
								$image_header = get_headers_curl($imageFea);
							}

							if ( mb_substr($image_header, 9, 3) == '404' ) {
								$imageFea       = $video[0]->image;
								$video_sd_image = 'mqdef';
							} else {
								$imageFea       = str_replace("/mq","/sd", $video[0]->image);
								$video_sd_image = 'sdimg';
							} ?>
							<div class="post-thumb-img-wrapper <?php echo $video_sd_image; ?>">
								<div class="search-img-wrapper">
									<a class="link_image" href="<?php echo get_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'vh'), the_title_attribute('echo=0')); ?>">
										<?php
										if ( isset($imageFea) ) {

											if ( $file_type == 1 || $file_type == 3 ) {
												if ( strpos($imageFea,'sddefault') !== false ) {
													$image_file = vh_imgresize($imageFea, 431, 326, $video_slug);
												} elseif ( strpos($imageFea,'mqdefault') !== false ) {
													$image_file = vh_imgresize($imageFea, 185, 122, $video_slug);
												}
											} else {
												$image_file = $image_path . $imageFea;
											}
										?>
											<img src="<?php echo $image_file; ?>" alt="">
										<?php } ?>
									</a>
								</div>
							</div>
							<div class="post-info-box">
								<?php $tc = wp_count_comments($post->ID); ?>
								<div class="vc_post_comments icon-comment"><?php echo $tc->total_comments; ?></div>
								<?php 
								if ( function_exists('get_post_ul_meta') ) { ?>
										<div class="vc_post_likes icon-heart"><?php echo get_post_ul_meta($post->ID,"like"); ?></div>
								<?php }; ?>
							</div>
						</div>
						<div class="post-date">
							<div class="post-date-container">
								<div class="post-date-left">
									<div class="post-date-day"><?php echo date('j', strtotime($post->post_date)); ?></div>
									<div class="post-date-year"><?php echo date('M, Y', strtotime($post->post_date)); ?></div>
								</div>
								<div class="blog_postedby">
									<?php echo get_avatar(get_the_author_meta( 'ID' )); ?>
								</div>
							</div>
							<div class="blog-postedby-info">
								<?php
								global $wpdb;
								$where_comments = 'WHERE comment_approved = 1 AND user_id = ' . get_the_author_meta( 'ID' );
								$comment_count = $wpdb->get_var("SELECT COUNT( * ) AS total FROM {$wpdb->comments} {$where_comments}");

								$user_id = $post->post_author;
								$where_videos = 'WHERE publish = 1 AND member_id = ' . $user_id ;
								$video_count = $wpdb->get_var("SELECT COUNT( * ) AS total FROM {$video_table} {$where_videos}");

								?>
								<a href="<?php echo get_author_posts_url( get_post_field( "post_author", $post->ID ) );?>"><?php echo __('by ', 'vh') . get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name;?></a>
								<div class="clearfix"></div>
								<div class="author-posts"><span class="icon-videocam"><?php echo $video_count . __(' videos', 'vh'); ?></span></div>
								<div class="author-comments"><span class="icon-comment"><?php echo $comment_count . __(' comments', 'vh'); ?></div>
							</div>
						</div>
						<h2 class="post-title">
							<a class="link_title" href="<?php echo get_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'vh'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a>
						</h2>

					<?php
					$extra_class = '';
					
					?>

					<div class="entry-content <?php echo $extra_class; ?>">
						<?php
							if ( is_search() ) {
								if( empty($video[0]->description) ) {
									_e( 'No excerpt for this posting.', 'vh' );
								} else {
									if ( strlen(strip_tags($video[0]->description)) > 300 ) {
										echo mb_substr(strip_tags($video[0]->description), 0, 300) . "[..]";
									} else {
										echo strip_tags($video[0]->description);
									}
								}
							} else {
								the_excerpt(__('Read more', 'vh'));
							}
						?>
					</div>
				</div>
					</div>
				</li>
				<?php }
				}
