<?php
/**
 * Metaboxes
 *
 * This file registers any custom metaboxes
 *
 * @package      Jobera_Functionality
 * @since        1.0.0
 */

/**
 * Create Metaboxes
 * @since 1.0.0
 */

function be_metaboxes( $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = 'vh_';

	// $meta_boxes[] = array(
	// 	'id'         => 'app_details',
	// 	'title'      => 'App details',
	// 	'pages'      => array( 'vh_app', ), // Post type
	// 	'context'    => 'normal',
	// 	'priority'   => 'high',
	// 	'show_names' => true, // Show field names on the left
	// 	'fields'     => array(
	// 		array(
	// 			'name' => 'Icon',
	// 			'desc' => 'Upload an image or enter an URL. Dimensions 90x90px',
	// 			'id'   => $prefix . 'icon',
	// 			'type' => 'file',
	// 		),
	// 		array(
	// 			'name' => 'Windows Store link',
	// 			'desc' => 'Please provide link to Windows Store',
	// 			'id'   => $prefix . 'windows_store_link',
	// 			'type' => 'text',
	// 		)
	// 	),
	// );

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes' , 'be_metaboxes' );
 
 
/**
 * Initialize Metabox Class
 * @since 1.0.0
 * see /lib/metabox/example-functions.php for more information
 *
 */

function be_initialize_cmb_meta_boxes() {
	if ( !class_exists( 'cmb_Meta_Box' ) ) {
		require_once( BE_DIR . '/lib/metabox/init.php' );
	}
}
add_action( 'init', 'be_initialize_cmb_meta_boxes', 9999 );