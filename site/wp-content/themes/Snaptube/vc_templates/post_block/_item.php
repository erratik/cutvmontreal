<?php
$block       = $block_data[0];
$settings    = $block_data[1];
$post_date_d = get_the_date( 'd', $post->id );
$post_date_y = get_the_date( 'M, Y', $post->id );

$tags      = "";
$i         = 0;
$tag_count = count(get_the_tags( $post->id ));

if ( get_the_tags( $post->id ) !== false ) {
	foreach (get_the_tags( $post->id ) as $tag) {
		if ( $i != $tag_count - 1 ) {
			$tags .= "<a href=" . get_tag_link( $tag->term_id ) . ">" . $tag->name . "</a>, ";
		} else {
			$tags .= "<a href=" . get_tag_link( $tag->term_id ) . ">" . $tag->name . "</a>";
		}
		$i++;
	}
}

if($block === 'title'): ?>
	<?php if (empty($post->thumbnail)): ?>
		<h2 class="post-title_nothumbnail">
			<?php echo !empty($settings[0]) && $settings[0]!='no_link' ? $this->getLinked($post, $post->title, $settings[0], 'link_title') : $post->title ?>
		</h2>
		<div class="blog_info_box nothumbnail">
			<div class="category-link">
				<i class="entypo_icon icon-folder"></i>
				<?php echo get_the_category_list(', ', '', $post->id); ?> 
			</div>
			<div class="tag-link">
				<i class="entypo_icon icon-tags"></i>
				<?php echo $tags; ?>
			</div>
		</div>
	<?php else: ?>
		<h2 class="post-title">
			<?php echo !empty($settings[0]) && $settings[0]!='no_link' ? $this->getLinked($post, $post->title, $settings[0], 'link_title') : $post->title ?>
		</h2>
		<div class="blog_info_box">
			<div class="category-link">
				<i class="entypo_icon icon-folder"></i>
				<?php echo get_the_category_list(', ', '', $post->id); ?> 
			</div>
			<div class="tag-link">
				<i class="entypo_icon icon-tags"></i>
				<?php echo $tags; ?>
			</div>
		</div>
	<?php endif ?>
<?php elseif($block === 'image' && !empty($post->thumbnail)): ?>
		<div class="post-thumb">
			<div class="post-thumb-img-wrapper">
				<?php echo "<a href=" . get_permalink( $post->id ) . " class='view_more'></a>"; ?>
				<?php echo !empty($settings[0]) && $settings[0]!='no_link' ? $this->getLinked($post, $post->thumbnail, $settings[0], 'link_image') : $post->thumbnail ?>
			</div>
			<div class="post-info-box">
				<?php $tc = wp_count_comments($post->id); ?>
				<div class="vc_post_comments icon-comment"><?php echo $tc->total_comments; ?></div>
				<?php 
				if ( function_exists('get_post_ul_meta') ) { ?>
						<div class="vc_post_likes icon-heart"><?php echo get_post_ul_meta($post->id,"like"); ?></div>
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
				<a href="<?php echo get_author_posts_url( get_post_field( "post_author", $post->id ) );?>"><?php echo __('by ', 'vh') . get_userdata( get_post_field( 'post_author', $post->id ) )->display_name;?></a>
				<div class="clearfix"></div>
				<div class="author-posts"><span class="icon-megaphone"><?php echo count_user_posts( get_the_author_meta( 'ID' ) ) . __(' blog entries', 'vh'); ?></span></div>
				<div class="author-comments"><span class="icon-comment"><?php echo $comment_count . __(' comments', 'vh'); ?></div>
			</div>
		</div>
<?php elseif($block === 'text'): ?>

		<?php 
		$extra_class = '';
		if (empty($post->thumbnail)) {
			$extra_class = 'nothumbnail';
		} ?>
		
		<?php if ( $post->content != '' || $post->excerpt != '' ) { ?>
			<div class="entry-content <?php echo $extra_class; ?>">
				<?php echo !empty($settings[0]) && $settings[0]==='text' ?  $post->content : $post->excerpt; ?>
			</div>
		<?php } ?>
<?php elseif($block === 'link'): ?>
		<?php 
		$extra_class = '';
		if (empty($post->thumbnail)) {
			$extra_class = 'nothumbnail';
		} ?>
		<span class="blog-read-more <?php echo $extra_class; ?>"><a href="<?php echo $post->link ?>" class="vc_read_more" title="<?php echo esc_attr(sprintf(__( 'Permalink to %s', "vh" ), $post->title_attribute)); ?>"<?php echo $this->link_target ?>><?php _e('Read more', "vh") ?></a></span>
<?php endif; ?>