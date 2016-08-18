<?php
/*
 * Plugin Name: CUTV Management Plugin
 * Version: 0.0.1
 * Plugin URI: http://erratik.ca/
 * Description: CUTV Management Plugin development.
 * Author: Tayana Jacques
 * Author URI: http://erratik.ca/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: cutv-plugin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Tayana Jacques
 * @since 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;
define( 'CUTV_MAIN_FILE' , __FILE__ );
global $wpdb;
define ( 'HDFLVVIDEOSHARE', $wpdb->prefix . 'hdflvvideoshare' );
define ( 'WVG_PLAYLIST', $wpdb->prefix . 'hdflvvideoshare_playlist' );
define ( 'WVG_MED2PLAY', $wpdb->prefix . 'hdflvvideoshare_med2play' );

/* Including functions definitions */
require_once( 'cutv.definitions.php' );
require_once( 'cutv.hooks.php' );
require_once( 'cutv.functions.php' );
/* Including Sources CPT definitions */
//require_once( 'includes/cutv.sources.php' );


function cutv_add_channel() {
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
        global $wpdb;
        $channelName = $_REQUEST['channelName'];
        $description = $_REQUEST['description'];
        $slug = $_REQUEST['slug'];
        $cat_id = $_REQUEST['cat_id'];

        // Let's take the data that was sent and do something with it
//        if ( $channelName == 'Banana' ) {
//            $channelName = 'Apple';
//        }
//        $parent_term = term_exists( 'channel' ); // array is returned if taxonomy is given
//        $parent_term_id = $parent_term['term_id']; // get numeric term id

        $playlists = $wpdb->get_results( 'SELECT * FROM ' . WVG_PLAYLIST );

        $query = $wpdb->prepare("INSERT INTO " . WVG_PLAYLIST . " (pid, playlist_name, playlist_slugname, playlist_desc, is_publish, playlist_order) VALUES ( %d, %s, %s, %s, %d, %d )",
            array($cat_id, $channelName, $slug, $description, 1, count($playlists))
        );
        $wpdb->query( $query);


        $playlistsUpdated = $wpdb->get_results( 'SELECT * FROM ' . WVG_PLAYLIST );

        // Now we'll return it to the javascript function
        // Anything outputted will be returned in the response
        echo print_r($playlistsUpdated);

        // If you're debugging, it might be useful to see what was sent in the $_REQUEST
        // print_r($_REQUEST);

    }

    // Always die in functions echoing ajax content
    die();
}
add_action( 'wp_ajax_cutv_add_channel', 'cutv_add_channel' );

function cutv_snaptube_playlists() {
    global $wpdb;

    $playlists = $wpdb->get_results( 'SELECT * FROM ' . WVG_PLAYLIST);
//    print_r($playlists);
    foreach($playlists as $obj) {
        $args = array(
            'posts_per_page'   => -1,
            'category'         => $obj->pid,
//            'category_name'    => '',
            'orderby'          => 'playlist_order',
            'order'            => 'DESC',
            'post_type'        => 'wpvr_video',
            'post_status'      => 'publish',
        );
        $posts_array = get_posts( $args );
        //echo  "<h3>$obj->pid - $obj->playlist_name (". count($posts_array) .")</h3>";

//        print_r($posts_array);
        foreach ( $posts_array as $post ) {
            $cats = get_the_category( $post->ID );

            foreach ( $cats as $cat ) {
                $the_category = $cat;
                //echo "[$post->ID] $post->post_title | category: $cat->term_id<br>";
            }

        }
        //echo "<hr>";
    }
//    exit;



//    $query = $wpdb->prepare("INSERT INTO " . WVG_PLAYLIST . " (pid, playlist_name, playlist_slugname, playlist_desc, is_publish, playlist_order) VALUES ( %d, %s, %s, %s, %d, %d )",
//        array($cat_id, $channelName, $slug, $description, 1, count($playlists))
//    );
//    $wpdb->query( $query);
//
//
//    $playlistsUpdated = $wpdb->get_results( 'SELECT * FROM ' . WVG_PLAYLIST );

    // Now we'll return it to the javascript function
    // Anything outputted will be returned in the response
    echo 'boom';



}
add_action('init','cutv_snaptube_playlists');

//create a function that will attach our new 'channel' taxonomy to the 'post' post type
function add_channel_taxonomy_to_post(){

    //set the name of the taxonomy
    $taxonomy = 'channel';
    //set the post types for the taxonomy
    $object_type = 'page';

    //populate our array of names for our taxonomy
    $labels = array(
        'name'               => 'Channels',
        'singular_name'      => 'Channel',
        'search_items'       => 'Search Channels',
        'all_items'          => 'All Channels',
        'parent_item'        => 'Parent Channel',
        'parent_item_colon'  => 'Parent Channel:',
        'update_item'        => 'Update Channel',
        'edit_item'          => 'Edit Channel',
        'add_new_item'       => 'Add New Channel',
        'new_item_name'      => 'New Channel Name',
        'menu_name'          => 'Channel'
    );

    //define arguments to be used
    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'show_ui'           => true,
        'how_in_nav_menus'  => true,
        'public'            => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'channel')
    );

    //call the register_taxonomy function
    register_taxonomy($taxonomy, $object_type, $args);
}
add_action('init','add_channel_taxonomy_to_post');
// If you wanted to also use the function for non-logged in users (in a theme for example)
// add_action( 'wp_ajax_nopriv_cutv_add_channel', 'cutv_add_channel' );

// Load plugin class files
require_once( 'includes/class-wordpress-plugin-template.php' );
require_once( 'includes/class-wordpress-plugin-template-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-wordpress-plugin-template-admin-api.php' );
require_once( 'includes/lib/class-wordpress-plugin-template-post-type.php' );
require_once( 'includes/lib/class-wordpress-plugin-template-taxonomy.php' );


/**
 * Returns the main instance of CUTV_Channel to prevent the need to use globals.
 *
 * @since  0.0.1
 * @return object CUTV_Channel
 */
function CUTV_Channel () {
	$instance = CUTV_Channel::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = CUTV_Channel_Settings::instance( $instance );
	}

	return $instance;
}

CUTV_Channel();