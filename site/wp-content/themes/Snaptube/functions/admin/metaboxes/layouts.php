<?php
/*
 * Layout options
 */

$config = array(
	'id'       => 'vh_layouts',
	'title'    => __('Layouts', 'vh'),
	'pages'    => array('page', 'post', 'tribe_events'),
	'context'  => 'normal',
	'priority' => 'high',
);

$options = array(array(
	'name'    => __('Layout type', 'vh'),
	'id'      => 'layouts',
	'type'    => 'layouts',
	'only'    => 'page,post,tribe_events',
	'default' => get_option('default-layout'),
));

require_once(VH_METABOXES . '/add_metaboxes.php');
new create_meta_boxes($config, $options);