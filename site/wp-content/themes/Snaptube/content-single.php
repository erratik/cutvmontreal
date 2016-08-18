<?php
/**
 * The template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage Snaptube
 */

global $vh_blog_image_layout;

$show_sep       = FALSE;
$style          = '';
$clear          = '';
$excerpt        = get_the_excerpt();
$top_left       = "";
$small_image    = FALSE;
$post_date_d    = get_the_date( 'd. M' );
$post_date_m    = get_the_date( 'Y' );
$is_author_desc = '';
$post_id = $post->ID;

$show_date = isset( $show_date ) ? $show_date : NULL;

if ( get_the_author_meta( 'description' ) ) { 
	$is_author_desc = ' is_author_desc';
}

// Determine blog image size
if ( LAYOUT == 'sidebar-no' ) {
	$clear     = ' style="float: none;"';
	$img_style = ' style="margin-left: 0;"';
} else {
	$small_image = TRUE;
	$img_style   = ' style="margin-left: 0;"';
}
$img           = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large-image' );
$entry_utility = '';

$entry_utility .= '<div class="page_title">' . get_the_title() . '</div><div class="post_date">'.human_time_diff(get_the_time('U',$post_id),current_time('timestamp')) .  ' ' . __('ago', 'vh') . '</div>
	<div class="entry-top-utility">';
	if ( 'post' == get_post_type() ) {

		$entry_utility .= '<div class="blog_like_dislike"><span class="post_dislikes icon-heart-broken">' . vh_ldc_dislike_counter_p('') . '</span>';
		$entry_utility .= '<span class="post_likes icon-heart">' . vh_ldc_like_counter_p('') . '</span></div>';


		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'vh' ) );
		if ( $categories_list ) {
			$entry_utility .= '
			<div class="category-link">
			<i class="entypo_icon icon-folder"></i>
			' . sprintf( __( '<span class="%1$s"></span> %2$s', 'vh' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
			$show_sep = TRUE;
			$entry_utility .= '
			</div>';
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'vh' ) );
		if ( $tags_list ) {
			$style = '';
			$entry_utility .= '
			<div class="tag-link"' . $style . '>
			<i class="entypo_icon icon-tags"></i>
			' . sprintf( __( '<span class="%1$s"></span> %2$s', 'vh' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
			$show_sep = true;
			$entry_utility .= '
			</div>';
		}
	}
	if ( $show_sep ) {
		$entry_utility .= '
		<div class="sep">&nbsp;</div>';
	}
	$entry_utility .= '
	<div class="clearfix"></div>
	</div>';
?>
<div class="entry no_left_margin first-entry <?php echo $is_author_desc; ?> <?php if ( !isset($img[0]) ) { echo ' no-image'; } ?><?php echo (LAYOUT != 'sidebar-no') ? ' vc_col-sm-12' : ' vc_col-sm-12'; ?>">
	<div class="entry-image vh_animate_when_almost_visible with_full_image <?php echo $vh_blog_image_layout . $is_author_desc; ?>"<?php echo $clear; ?>>
		<?php
		$i                 = 2;
		$posts_slideshow   = ( get_option('vh_posts_slideshow_number') ) ? get_option('vh_posts_slideshow_number') : 5;
		$attachments_count = 1;

		while( $i <= $posts_slideshow ) {
			$attachment_id = kd_mfi_get_featured_image_id( 'featured-image-' . $i, 'post' );
			if( $attachment_id ) {
				$attachments_count = ++$attachments_count;
			}
			$i++;
		}
		?>
		<div class="image_wrapper">
			<?php if ( get_post_thumbnail_id() != "" ): ?>
				<div class="post_info">
				<?php  
				$tc = wp_count_comments($post_id); 
					echo '<span class="comments icon-comment">' . $tc->total_comments . '</span>';
					if ( function_exists('get_post_ul_meta') ) {
						echo '<span class="blog_likes icon-heart">' . get_post_ul_meta($post_id,"like") . '</span>';
					}
				?>
				</div>
			<?php endif ?>
			<?php
			if ( $attachments_count > 1 ) { ?>

			<div class="cr-container no_left_margin" id="cr-container">
				<div class="cr-content-wrapper" id="cr-content-wrapper">
					<div class="cr-content-container" id="content-1" style="display:block;">
						<img src="<?php echo $img[0]; ?>"class="cr-img"/>
					</div>
					<?php
					$i = 2;
					while( $i <= $attachments_count ) {
						$attachment_id = kd_mfi_get_featured_image_id( 'featured-image-' . $i, 'post' );
						if( $attachment_id ) {
							$attachment_image = wp_get_attachment_image_src( $attachment_id, 'offer-image-large' );
							?>
							<div class="cr-content-container" id="content-<?php echo $i; ?>">
								<img src="<?php echo $attachment_image[0]; ?>"class="cr-img"/>
							</div>
						<?php }
						$i++;
					}
					?>
				</div>
				<div class="cr-thumbs">
					<?php
						$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'blog-image' );
						if( $img  ) { ?>
							<div data-content="content-1" class="cr-selected"><img src="<?php echo $img [0]; ?>" /></div><?php
						}

					$i = 2;
					while( $i <= $attachments_count ) {
						$attachment_id = kd_mfi_get_featured_image_id( 'featured-image-' . $i, 'post' );
						if( $attachment_id ) {
							$attachment_image = wp_get_attachment_image_src( $attachment_id, 'blog-image' );
							?>
							<div data-content="content-<?php echo $i; ?>"><img src="<?php echo $attachment_image[0]; ?>" /></div>
						<?php }
						$i++;
					}
					?>
				</div>
			</div>
			<script type="text/javascript">
				jQuery(window).load(function(){
					jQuery('#cr-container').crotator({
						autoplay    : false,
						thumbsCount : 6
					});
				});
			</script>
			<?php
			} elseif ( isset($img[0]) && $attachments_count <= 1 ) { ?>
				<img src="<?php echo $img[0]; ?> "<?php echo $img_style; ?> class="open_entry_image" alt="" />
			<?php }
			?>
		</div>
		<div class="entry-content">
				<?php 
					echo '<div class="title-and-utility';
					if ( $show_date == 'false' ) { echo ' no_left_margin'; };
					echo '">';
					echo $entry_utility;
					echo '<div class="clearfix"></div>';
					echo '</div>';
				?>
			<div class="clearfix"></div>
			<?php
			if ( is_search() ) {
				the_excerpt();
				if( empty($excerpt) )
					echo 'No excerpt for this posting.';

			} else {
				the_content(__('Read more', 'vh'));
				wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'vh' ) . '</span>', 'after' => '</div>', 'link_before' => '<span class="page-link-wrap">', 'link_after' => '</span>', ) );
			}
			?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
	<?php
	// If a user has filled out their description, show a bio on their entries
	if ( get_post_type( $post ) == 'post' && get_the_author_meta( 'description' ) ) { ?>
	<div id="author-info">
		<div class="avatar_box">
			<div id="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'vh_author_bio_avatar_size', 70 ) ); ?>
			</div>
		</div><!-- end of author-avatar -->
		<div id="author-description">
			<div class="author-name"><?php printf( esc_attr__( 'Author: %s', 'vh' ), get_the_author() ); ?></div>
			<div class="clearfix"></div>
			<p><?php the_author_meta( 'description' ); ?></p>
			<div id="author-link">
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
					<?php printf( __( 'View all posts', 'vh' ), get_the_author() ); ?>
				</a>
			</div><!-- end of author-link	-->
		</div><!-- end of author-description -->
		<div class="clearfix"></div>
	</div><!-- end of entry-author-info -->
	<?php } ?>
</div>