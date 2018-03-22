<?php
/*
 Plugin Name: OpenClub importer
 Plugin URI: TBD
 Description:
 Author: Klaus Harris
 Version: -
 Author URI: https://klaus.blog
 Text Domain: openclub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OPENCLUB_IMPORTER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( class_exists( 'WP_CLI' ) ) {

	require_once( OPENCLUB_IMPORTER_PLUGIN_DIR . '/cli/class-file-runner.php' );
	$runner = new OpenClub\CLI\File_Runner();
	WP_CLI::add_command( 'file-runner', $runner );

}

function openclub_importer_post_types_init() {

	$args = array(
		'label'               => esc_html__( 'CSV' ),
		'public'              => false,
		'show_ui'             => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'rewrite'             => false,
		'query_var'           => true,
		'menu_icon'           => 'dashicons-admin-page',
		'show_in_nav_menus'   => false,
		'show_in_rest'        => false,
		'menu_position'       => 30,
		'supports'            => array(
			'title',
			'editor',
			'revisions',
			'custom-fields',
		)
	);
	register_post_type( 'openclub-csv', $args );
}

add_action( 'init', 'openclub_importer_post_types_init' );

function openclub_importer_disable_wysiwyg( $default ) {

	global $post;

	if ( in_array( get_post_type( $post ), array(
		'openclub-csv',
	) ) ) {
		return false;
	}

	return $default;
}

add_filter( 'user_can_richedit', 'openclub_importer_disable_wysiwyg' );
