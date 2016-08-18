<?php
	
	/* Function to redirect download request to permalink structure */
	add_action( 'template_include' , 'cutv__download_export_file' );
	function cutv__download_export_file( $template ) {
		$ext = 'json';
		$tab = explode( '/cutv__export/' , $_SERVER[ 'REQUEST_URI' ] );
		
		
		if( $tab[ 0 ] == '' ) {
			$xtab = explode( '_@_' , $tab[ 1 ] );
			//d( $xtab );return false;
			if( ! isset( $xtab[ 1 ] ) || $xtab[ 1 ] == '' ) {
				
				$a = explode( '*' , $xtab[ 0 ] );
				
				if( isset( $a[ 1 ] ) ) {
					$type            = '';
					$file_name       = $tab[ 1 ];
					$export_filename = $a[ 0 ] . '.' . $a[ 1 ];
				} elseif( strpos( $tab[ 1 ] , 'sysinfo' ) != - 1 ) {
					$type            = "";
					$file_name       = $tab[ 1 ];
					$export_filename = "cutv__system_info.txt";
				} else {
					//All types
					$type            = "";
					$file_name       = $tab[ 1 ];
					$export_filename = "cutv__export." . $ext;
				}
			} else {
				if( $xtab[ 1 ] == 'options' ) {
					//options
					$type = "options";
				} elseif( $xtab[ 1 ] == 'sources' ) {
					//sources
					$type = "sources";
				} elseif( $xtab[ 1 ] == 'videos' ) {
					//Videos
					$type = "videos";
				} elseif( $xtab[ 1 ] == 'sysinfo' ) {
					//Videos
					$type = "system_info";
					$ext  = "txt";
				}
				$file_name       = $xtab[ 0 ];
				$export_filename = "cutv__export_" . $type . "." . $ext;
			}
			
			
			$file = cutv__TMP_PATH . '' . $tab[ 1 ];
			header( "Content-type: application/x-msdownload" , TRUE , 200 );
			header( "Content-Disposition: attachment; filename=" . $export_filename );
			header( "Pragma: no-cache" );
			header( "Expires: 0" );
			readfile( $file );
			exit();
		} else {
			return $template;
		}
	}
	
	/* Remove Video Post Type slug from permalink */
	add_filter( 'post_type_link' , 'cutv__remove_video_slug' , 10 , 3 );
	function cutv__remove_video_slug( $post_link , $post , $leavename ) {
		global $cutv__options;
		
		if( cutv__VIDEO_TYPE != $post->post_type || 'publish' != $post->post_status ) {
			return $post_link;
		}
		
		if( $cutv__options[ 'enableRewriteRule' ] === TRUE ) {
			$post_link = cutv__render_video_permalink( $post , "/%postname%/" );
			if( $cutv__options[ 'permalinkBase' ] === 'none' ) {
				
				$base = '';
				
			} elseif( $cutv__options[ 'permalinkBase' ] === 'category' ) {
				
				$terms = wp_get_object_terms( $post->ID , 'category' );
				if( ! is_wp_error( $terms ) && ! empty( $terms ) && is_object( $terms[ 0 ] ) ) {
					$taxonomy_slug = $terms[ 0 ]->slug;
				} else {
					$taxonomy_slug = cutv__UNCATEGORIZED;
				}
				
				if( $taxonomy_slug == '' ) {
					$base = '';
				} else {
					$base = '/' . $taxonomy_slug . '';
				}
				
			} elseif( $cutv__options[ 'permalinkBase' ] === 'custom' ) {
				
				if( $cutv__options[ 'customPermalinkBase' ] == '' ) {
					$base = '';
				} else {
					$base = '/' . $cutv__options[ 'customPermalinkBase' ] . '';
				}
				
			}
			
			$permalink = str_replace( cutv__SITE_URL , cutv__SITE_URL . $base , $post_link );
			
			return $permalink;
		} else {
			
			return cutv__render_video_permalink( $post );
		}
	}

	add_action( 'init' , 'cutv__add_cron_endpoint' );
	function cutv__add_cron_endpoint() {
		add_rewrite_tag( '%cutv__cron%' , '([^&]+)' );
		add_rewrite_rule( cutv__CRON_ENDPOINT . '/([^&]+)/?' , 'index.php?cutv__cron=$matches[1]' , 'top' );
		flush_rewrite_rules();
	}


	add_action( 'template_redirect' , 'cutv__process_cron_call' , 1000 );
	function cutv__process_cron_call() {
		global $wp_query , $cron_data_file;
		$token = $wp_query->get( 'cutv__cron' );
		if( ! $token ) return;
		//$_GET['debug'] = true ;
		$_GET[ 'token' ] = $token;
		//d( $_GET );
		
		if( ! is_multisite() ) {
			$cron_data_file = cutv__PATH . "assets/php/cron.txt";
		} else {
			$site_id        = get_current_blog_id();
			$cron_data_file = cutv__PATH  . "assets/php/cron_" . $site_id . ".txt";
		}
		include( cutv__PATH . 'cutv_.cron.php' );
		exit;
	}