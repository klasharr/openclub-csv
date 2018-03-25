<?php
/*
 Plugin Name: OpenClub CSV
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

define( 'OPENCLUB_CSV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OPENCLUB_DEFAULT_FILTER_PRIORITY', 10 );

if ( class_exists( 'WP_CLI' ) ) {

	require_once( OPENCLUB_CSV_PLUGIN_DIR . '/cli/class-file-runner.php' );
	$runner = new OpenClub\CLI\File_Runner();
	WP_CLI::add_command( 'file-runner', $runner );

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


/**
 * A very rough P.O.C.
 *
 * [openclub_display_csv post_id="102"]
 *
 * @param $atts
 */
function openclub_csv_display_shortcode_callback( $config ) {

	$config = shortcode_atts(
		array(
			'post_id' => null,
			'error_messages' => "yes",
			'error_lines' => "yes",
		),
		$config
	);

	echo openclub_csv_get_display_table( $config );

}
add_shortcode( 'openclub_display_csv', 'openclub_csv_display_shortcode_callback' );


function openclub_csv_add_inline_css() {
	?>
	<style>
		.openclub_csv_error {
			color: red;
		}
		table.openclub_csv th {
			background-color: #EFEFEF;
		}
	</style>
	<?php
}
add_action('wp_head', 'openclub_csv_add_inline_css');



function openclub_csv_view_content_page( $content ) {

	/**
	 * @post WP_Post
	 */
	global $post;

	if ( is_singular() && in_array( get_post_type( $post ), array( 'openclub-csv' ) ) ) {

		$config = array(
			'post_id' => $post->ID,
			'error_messages' => "yes",
			'error_lines' => "yes",
		);

		return openclub_csv_get_display_table( $config );

	}

	return $content;

}

add_filter( 'the_content', 'openclub_csv_view_content_page' );


function openclub_csv_get_display_table( $config ) {

	$out = '';

	try{
		$a = \OpenClub\CSV_Util::get_csv_content( $config[ 'post_id'] );

		if( !empty( $a[ 'data' ] ) ) {

			if(!empty( $a['errors'] ) && $config['error_messages' ] == "yes"  ) {
				$out .= "<h3 class='openclub_csv_error'>Errors</h3><p>";

				foreach($a['errors'] as $line => $message ){
					$out .= \OpenClub\CSV_Util::get_formatted_csv_line_error_message($message);
				}
				$out .= '</p>';
			}

			$out .= "<table class='openclub_csv'>\n";
			$out .= \OpenClub\CSV_Util::get_csv_table_header( $a[ 'field_validator_manager' ] );

			/** @var DTO $line_data */
			foreach($a[ 'data' ] as $line_data ){
				if( !$line_data->has_validation_error() ) {
					$out .= \OpenClub\CSV_Util::get_csv_table_row( $line_data, $a[ 'field_validator_manager' ] );
					continue;
				}
				if( $config['error_lines'] == "yes" ) {
					$out .= \OpenClub\CSV_Util::get_csv_table_row( $line_data, $a[ 'field_validator_manager' ] );
				}
			}
			$out .= "</table>\n";
		} else {
			$out .= 'No data';
		}

	} catch( \Exception $e ) {
		$out .= 'Error: ' . $e->getMessage() . ' Check the value passed to the shortcode is a valid post_id.';
	}

	return $out;
}

function openclub_csv_robots_override( $output ) {

	$output .= "Disallow: /openclub_csv/\n";
	return $output;
}

add_filter( 'robots_txt', 'openclub_csv_robots_override', 0, 2 );



function openclub_csv_example_data_filter( $data, $post ){
	return $data;
}

add_filter( 'openclub_csv_filter_data', 'openclub_csv_example_data_filter', OPENCLUB_DEFAULT_FILTER_PRIORITY, 2 );