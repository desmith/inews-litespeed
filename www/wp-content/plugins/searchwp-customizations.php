<?php
/*
Plugin Name: SearchWP Customizations
Description: Customizations for SearchWP
Version: 1.0.0
*/

// Add all hooks and custom code here.

// Force enable SearchWP's alternate indexer.
add_filter( 'searchwp\indexer\alternate', '__return_true' );

// Sort SearchWP Post, Page, and Custom Post Type Results by date in DESC order.
add_filter( 'searchwp\query\mods', function( $mods, $query ) {
	foreach ( $query->get_engine()->get_sources() as $source ) {
		$flag = 'post' . SEARCHWP_SEPARATOR;
		if ( 'post.' !== substr( $source->get_name(), 0, strlen( $flag ) ) ) {
			continue;
		}

		$mod = new \SearchWP\Mod( $source );

		$mod->order_by( function( $mod ) {
			return $mod->get_local_table_alias() . '.post_date';
		}, 'DESC', 1 );

		$mods[] = $mod;
	}

	return $mods;
}, 20, 2 );

