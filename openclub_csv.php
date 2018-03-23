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


if ( class_exists( 'WP_CLI' ) ) {

	require_once( OPENCLUB_CSV_PLUGIN_DIR . '/cli/class-file-runner.php' );
	$runner = new OpenClub\CLI\File_Runner();
	WP_CLI::add_command( 'file-runner', $runner );

}

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );

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


/**
 * A very rough P.O.C.
 *
 * [openclub_display_csv post_id="102"]
 *
 * @param $atts
 */
function openclub_csv_display_shortcode_callback( $atts ) {

	$atts = shortcode_atts(
		array(
			'post_id' => null,
		),
		$atts
	);

	try{

		$a = \OpenClub\CSV_Util::get_csv_content( $atts['post_id'] );

		if( !empty( $a[ 'data' ] ) ) {

			if(!empty( $a['errors'] ) ) {
				echo "<h3 class='openclub_csv_error'>Errors</h3><p>";

				foreach($a['errors'] as $line => $message ){
					echo \OpenClub\CSV_Util::get_formatted_csv_line_error_message($message);
				}
				echo '</p>';
			}

			echo "<table>";
			echo \OpenClub\CSV_Util::get_csv_table_header( $a[ 'header_fields' ] );

			/** @var DTO $line_data */
			foreach($a[ 'data' ] as $line_data ){
				//if( !$line_data->has_validation_error() ) {
					echo \OpenClub\CSV_Util::get_csv_table_row( $line_data );
				//}
			}
			echo "</table>";
		} else {
			echo 'No data';
		}

	} catch( \Exception $e ) {
		echo 'Error: ' . $e->getMessage() . ' Check the value passed to the shortcode is a valid post_id.';
	}

}
add_shortcode( 'openclub_display_csv', 'openclub_csv_display_shortcode_callback' );


function openclub_csv_add_inline_css() {
	?>
	<style>
		.openclub_csv_error {
			color: red;
		}
	</style>
	<?php
}
add_action('wp_head', 'openclub_csv_add_inline_css');



