<?php

/**
* Twitter widget
*/

class vh_twitter extends WP_Widget {

	public function vh_twitter() {
		$widget_ops = array(
			'classname'   => 'vh_twitter',
			'description' => __('Displays a twitter feed', 'vh')
		);
		parent::__construct('vh_twitter', __('Snaptube - Twitter', 'vh') , $widget_ops);
	}

	public function widget($args, $instance) {
		extract($args);
		$title      = apply_filters('widget_title', empty($instance['title']) ? __('Recent Tweets', 'vh') : $instance['title'], $instance, $this->id_base);
		$username   = $instance['username'];
		$user_array = explode(',', $username);

		foreach ($user_array as $key => $user) {
			$user_array[$key] = '"' . $user . '"';
		}

		$avatar_size = (int)$instance['avatar_size'];

		if (empty($avatar_size))
			$avatar_size = 'null';

		$count = (int)$instance['count'];
		if ($count < 1)
			$count = 1;

		if(!empty($user_array)) {

			echo $before_widget;

			if (!empty($title)) {
				echo $before_title . $title . $after_title;
			}

			$id = rand(1, 10000);

			// Load JS
			wp_enqueue_script( 'jquery-tweet', get_template_directory_uri() . '/js/jquery.tweet.js', array( 'jquery' ), '', TRUE );
			wp_localize_script( 'jquery-tweet', 'twitter_ajax', array( 'twitterurl' => plugins_url( 'index.php' , __FILE__ ) ) );

			?>
			<script type="text/javascript">
				jQuery(function($) {
					$("#twitter_container_<?php echo $id; ?>").tweet({
						username: [<?php echo implode(',', $user_array); ?>],
						count: <?php echo $count; ?>,
						avatar_size: <?php echo $avatar_size; ?>,
						seconds_ago_text: '<?php _e('about %d seconds ago', 'vh'); ?>',
						a_minutes_ago_text: '<?php _e('about a minute ago', 'vh'); ?>',
						minutes_ago_text: '<?php _e('about %d minutes ago', 'vh'); ?>',
						a_hours_ago_text: '<?php _e('about an hour ago', 'vh'); ?>',
						hours_ago_text: '<?php _e('about %d hours ago', 'vh'); ?>',
						a_day_ago_text: '<?php _e('about a day ago', 'vh'); ?>',
						days_ago_text: '<?php _e('about %d days ago', 'vh'); ?>',
						loading_text: 'loading..'
					});
				});
			</script>
			<div id="twitter_container_<?php echo $id; ?>" class="twitter_container <?php if ($avatar_size != 'null') { ?>tweet_avatar<?php } ?>"></div>

			<?php
			echo $after_widget;
		}
	}

	public function update($new_instance, $old_instance) {
		$instance                = $old_instance;
		$instance['title']       = strip_tags($new_instance['title']);
		$instance['username']    = strip_tags($new_instance['username']);
		$instance['avatar_size'] = $new_instance['avatar_size'] ? (int)$new_instance['avatar_size'] : '';
		$instance['count']       = (int)$new_instance['count'];
		return $instance;
	}

	public function form($instance) {
		$title       = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$username    = isset($instance['username']) ? esc_attr($instance['username']) : '';
		$avatar_size = isset($instance['avatar_size']) ? absint($instance['avatar_size']) : '';
		$count       = isset($instance['count']) ? absint($instance['count']) : 3;
		$display     = isset($instance['display']) ? $instance['display'] : 'latest';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'vh'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Usernames (separate with comas):', 'vh'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php _e('height and width of avatar if displayed (54px max)(optional)', 'vh'); ?></label>
			<input id="<?php echo $this->get_field_id('avatar_size'); ?>" name="<?php echo $this->get_field_name('avatar_size'); ?>" type="text" value="<?php echo $avatar_size; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('How many tweets to display?', 'vh'); ?></label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" size="3" />
		</p>
		<?php
	}
}
add_action('widgets_init', 'register_twitter_widget');
function register_twitter_widget() {
    register_widget('vh_twitter');
}