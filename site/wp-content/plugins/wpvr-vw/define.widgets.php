<?php
	add_action( 'widgets_init' , 'wpvr_vw_widgets_init_fct' , 100 );
	function wpvr_vw_widgets_init_fct() {
		register_widget( 'wpvr_vw_widget' );
	}

	class wpvr_vw_widget extends WP_Widget {
		// Register Widget
		function __construct() {
			parent::__construct(
				'wpvr_vw_widget' , // Base ID
				__( '# WPVR Video Widget' , WPVR_VW_ID ) , // Name
				array(
					'description' => __( 'WPVR Video Widget' , WPVR_VW_ID ) ,
				) // Args
			);
		}
		
		//Front-end display of widget.
		public function widget( $args , $instance ) {
			echo $args[ 'before_widget' ];
			if( ! empty( $instance[ 'data' ] ) ) {
				//d( $instance[ 'data' ] );
				$wargs  = wpvr_vw_get_args( $instance[ 'data' ] );
				//_d( $wargs );
				$videos = wpvr_vw_get_videos( $wargs );
				if( $wargs[ 'show_widget_title' ] === TRUE ) {
					echo $args[ 'before_title' ] . apply_filters( 'widget_title' , $wargs[ 'widget_title' ] ) . $args[ 'after_title' ];
				}
				wpvr_vw_render_videos( $videos , $wargs );
			}
			echo $args[ 'after_widget' ];
		}
		
		//Back-end widget form.
		public function form( $instance ) {

			$encoded_data = isset( $instance['data'] ) ? $instance['data'] : '' ;
			$widget_token = substr( md5( microtime() ) , rand( 0 , 26 ) , 15 );

			?>
			<div class = "wpvr_widget_form" url = "<?php echo WPVR_VW_ACTIONS_URL; ?>" id = "<?php echo $widget_token; ?>">
				<button
					class = "wpvr_black_button wpvr_submit_button wpvr_small wpvr_widget_button"
					style = "width:100%;"
					onclick = "event.preventDefault();wpvr_show_widget_settings_dialog('<?php echo $widget_token; ?>');"
				>
					<i class = "wpvr_button_icon fa fa-gears"></i>
					CONFIGURE WIDGET
				</button>
				<textarea
					style = "display:none;"
					class = "wpvr_widget_encoded_data"
					id = "<?php echo $this->get_field_id( 'data' ); ?>"
					name = "<?php echo $this->get_field_name( 'data' ); ?>"
				><?php echo esc_attr( $encoded_data ); ?></textarea>
				<div class = "wpvr_clearfix"></div>
			
			</div>
			<script src = "<?php echo WPVR_VW_URL . 'assets/scripts.js'; ?>"></script>
			<?php
		}
		
		//Sanitize widget form values as they are saved.
		public function update( $new_instance , $old_instance ) {
			$instance           = array();
			$instance[ 'data' ] = ( ! empty( $new_instance[ 'data' ] ) ) ? strip_tags( $new_instance[ 'data' ] ) : '';
			
			return $instance;
		}
	}