<?php
/**
 * Plugin Name: Snaptube Functionality
 * Description: This contains all your site's core functionality so that it is theme independent.
 * Version: 2.2.3
 * Author: Cohhe
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

// Plugin Directory 
define( 'BE_DIR', dirname( __FILE__ ) );

define('VH_SHORTCODES', get_template_directory() . '/functions/admin/visual-composer');

// Scripts and Styles
include_once( BE_DIR . '/lib/scripts_and_styles.php' );

// Post Types
include_once( BE_DIR . '/lib/functions/post-types.php' );

// Taxonomies 
//include_once( BE_DIR . '/lib/functions/taxonomies.php' );

// Metaboxes
include_once( BE_DIR . '/lib/functions/metaboxes.php' );
 
// Shortcodes
include_once( BE_DIR . '/lib/functions/shortcodes.php' );

// Widgets
//include_once( BE_DIR . '/lib/widgets/widget-social.php' );

// Twitter widgets
include_once( BE_DIR . '/lib/widgets/twitter/twitter.php' );

// Editor Style Refresh
include_once( BE_DIR . '/lib/functions/editor-style-refresh.php' );

// General
include_once( BE_DIR . '/lib/functions/general.php' );

function vh_snaptube_localize() {
	load_plugin_textdomain( 'vh', false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
}
add_action( 'plugins_loaded', 'vh_snaptube_localize' );

function vh_remove_search_widget() {
	unregister_widget('Widget_ContusVideoSearch_init');
}
add_action( 'widgets_init', 'vh_remove_search_widget' );

class Widget_ContusVideoSearchEdited_init extends WP_Widget {    
    /**
     * Search widget init function
     */
    function Widget_ContusVideoSearchEdited_init() {
        /** Array to store search widget class name, description */
        $widget_ops = array ( 'classname' => 'Widget_ContusVideoSearchEdited_init ', 'description' => 'Search Videos Widget' );
        parent::__construct ( 'Widget_ContusVideoSearch', 'Contus Video Search', $widget_ops );
    }
    
    /**
     * This function is used to create search widget  
     * 
     * @param   object   $instance
     */
    function form($instance) {
        /** Set title for search widget */
        $instance = wp_parse_args ( ( array ) $instance, array ('title' => 'Video Search' ) );
        $title    = esc_attr ( $instance ['title'] );        
        /** Create search widget option in admin */
        ?>
        <p>
          <label for='<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>'>Title: 
              <input class='widefat' id='<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>' type='text'  
              name='<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>' 
              value='<?php echo esc_html( $title ); ?>' />
          </label>
        </p>
    <?php
    }
    
    /**
     * This function is used to update widget
     * 
     * @param unknown $new_instance
     * @param unknown $old_instance
     * @return unknown
     */
    function update($new_instance, $old_instance) {
        $instance           = $old_instance;
        $instance ['title'] = $new_instance ['title'];
        return $instance;
    }
    
    /**
     * This function is used to create search box for site
     * 
     * @param unknown $args
     * @param unknown $instance
     */
    function widget($args, $instance) {   
        $title      = empty ( $instance ['title'] ) ? ' ' : apply_filters ( 'widget_title', $instance ['title'] );     
        /** Call helper function to get more page id from db */
        $moreName   = morePageID();
        /** Search Widget starts and set title */
        /** Before widget functions search */
        echo $args['before_widget'];
        $searchVal  = __ ( 'Video Search &hellip;', 'vh' );        
        /** Search form to search videos */
        $div = '<div id="videos-search"  class="widget_search sidebar-wrap search-form-container "><h3 class="widget-title">' . $title . '</h3>
            <form role="search" method="get" id="search-form" class="search-form searchform clearfix" action="' . home_url ( '/' ) . '" >
              <label class="screen-reader-text" >' . __ ( 'Search for:', 'vh' ) . '</label>
              <input type="text" class="s field search-field" placeholder="' . $searchVal . '" value="" name="s" id="video_search"  />
              <input type="submit" class="search-submit submit" id="videosearchsubmit" value="' . __ ( 'Search', 'vh' ) . '" />
          </form> </div>';
        /** Display search videos widget  */
        echo $div;
        /** Search videos widget ends */
        /** After widget functions search */
        echo $args['after_widget'];
    }
}
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Widget_ContusVideoSearchEdited_init" );' ) ); 