<?php
/*
 Plugin Name: OpenClub CSV
 Plugin URI: TBD
 Description:
 Author: Klaus Harris
 Version: 0.09
 Author URI: https://klaus.blog
 Text Domain: openclub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'OPENCLUB_CSV_PLUGIN_DIR' ) ) {
	define( 'OPENCLUB_CSV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

define( 'OPENCLUB_DEFAULT_FILTER_PRIORITY', 10 );
define( 'OPENCLUB_CSV_LOG_TEMPLATE_FILES_LOADED', false );

if ( class_exists( 'WP_CLI' ) ) {

	require_once( OPENCLUB_CSV_PLUGIN_DIR . '/cli/class-cli-command.php' );
	$command = new OpenClub\CLI_Command;
	WP_CLI::add_command( 'openclub', $command );

}

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );

function openclub_importer_post_types_init() {

	$args = array(
		'label'               => esc_html__( 'CSV' ),
		'public'              => true,
		'show_ui'             => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'rewrite'             => true,
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


/**
 * Disable the wysiwyg editor for this the openclub-csv custom post type
 *
 * @param $default
 *
 * @return bool
 */
function openclub_importer_disable_wysiwyg( $default ) {

	global $post;

	if ( in_array( get_post_type( $post ), array(
		'openclub-csv',
	), true ) ) {
		return false;
	}

	return $default;
}

add_filter( 'user_can_richedit', 'openclub_importer_disable_wysiwyg' );

/**
 * Default shortcode display
 *
 * Example usage:
 *
 * [openclub_display_csv post_id=102]
 *
 * [openclub_display_csv post_id=102 error_lines="yes" error_messages="yes" display="safety_teams" group_by_field="Team"]
 *
 * [openclub_display_csv post_id=102 error_lines="yes" error_messages="yes" display="grouped_table" group_by_field="Duty Date" future_items_only="yes" limit="10"]
 *
 * 'future_items_only' will only be active if these two settings are also set:
 *
 *      group_by_field="Date"
 *      display="grouped_table".
 *
 * The group_by_field must be of type Date.
 *
 * [openclub_display_csv post_id=102 error_lines="yes" error_messages="yes" display="grouped_table" group_by_field="Date" filter="SSC_Safety_Team" future_items_only="yes" limit="10"]
 *
 * filter="SSC_Safety_Team" will run each row through a filter the rows before display. In this case the filter is from another plugin, see an example filter here:
 *
 * openclub-csv/inc/class-null-filter.php
 *
 * @see \OpenClub\CSV_Display::get_config()
 *
 */
add_shortcode( 'openclub_display_csv', 'get_openclub_display_csv_shortcode' );

function get_openclub_display_csv_shortcode( $config ) {

	$config = shortcode_atts(
		OpenClub\CSV_Display::get_config(),
		$config
	);

	$config = openclub_csv_get_future_items_only_query_value( $config );

	return OpenClub\CSV_Display::get_html( $config );
}

add_action( 'wp_head', function () {
	?>
	<style>
		.openclub_csv_error {
			color: red;
		}

		table.openclub_csv th {
			background-color: #EFEFEF;
		}

		tr.bold td {
			font-weight: bold;
		}
	</style>
	<?php
} );

function openclub_csv_view_content_page( $content ) {

	/**
	 * @post WP_Post
	 */
	global $post;

	if ( is_singular() && in_array( get_post_type( $post ), array( 'openclub-csv' ), true ) ) {

		return OpenClub\CSV_Display::get_html(
			OpenClub\CSV_Display::get_config( array( 'post_id' => $post->ID ) )
		);
	}

	return $content;
}

add_filter( 'the_content', 'openclub_csv_view_content_page' );


function openclub_csv_robots_override( $output ) {

	$output .= "Disallow: /openclub_csv/\n";
	return $output;
}

add_filter( 'robots_txt', 'openclub_csv_robots_override', 0, 2 );


/**
 * @param \OpenClub\Data_Set $data_set
 * @param $post
 *
 * @return \OpenClub\Data_Set
 */
function openclub_csv_example_data_set_filter( \OpenClub\Data_Set $data_set, $post ) {
	return $data_set;
}

add_filter( 'openclub_csv_filter_data', 'openclub_csv_example_data_set_filter', OPENCLUB_DEFAULT_FILTER_PRIORITY, 2 );


function openclub_csv_log_cli( $message ) {
	if ( class_exists( 'WP_CLI' ) ) {
		\WP_CLI::log( $message );
	}
}

function openclub_add_custom_query_var( $vars ) {
	$vars[] = "fio";

	return $vars;
}

add_filter( 'query_vars', 'openclub_add_custom_query_var' );


function openclub_csv_get_future_items_only_query_value( array $config ) {

	$future_items_only = get_query_var( 'fio' );

	if ( isset( $future_items_only ) &&
	     in_array( (int) $future_items_only, array( 1, 2 ) )
	) {
		$config['future_items_only'] = ( 2 == $future_items_only ? "yes" : "no" );
	}

	return $config;
}





