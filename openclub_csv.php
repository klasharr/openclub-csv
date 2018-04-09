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

define( 'OPENCLUB_CSV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OPENCLUB_DEFAULT_FILTER_PRIORITY', 10 );

if ( class_exists( 'WP_CLI' ) ) {

	require_once( OPENCLUB_CSV_PLUGIN_DIR . '/cli/class-openclub.php' );
	$command = new OpenClub\CLI\OpenClub;
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

	return openclub_csv_get_display_table( $config );

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

		/**
		 * @var $input \OpenClub\Data_Set_Input
		 */
		$input = \OpenClub\Factory::get_data_input_object( $config[ 'post_id'] );

		/**
		 * @var \OpenClub\Data_Set $data_set
		 */
		$data_set = \OpenClub\CSV_Util::get_data_set( $input );

		if( $data_set->has_data() ) {

			if( $data_set->has_errors() && $config['error_messages' ] == "yes"  ) {
				$out .= "<h3 class='openclub_csv_error'>Errors</h3><p>";

				foreach($data_set->get_errors() as $line_number => $message ){
					$out .= \OpenClub\CSV_Util::get_formatted_csv_line_error_message($message);
				}
				$out .= '</p>';
			}

			$out .= "<table class='openclub_csv'>\n";
			$out .= \OpenClub\CSV_Util::get_csv_table_header( $data_set->get_field_validator_manager() );

			/** @var DTO $line_data */
			foreach($data_set->get_data() as $line_data ){
				if( !$line_data->has_validation_error() ) {
					$out .= \OpenClub\CSV_Util::get_csv_table_row( $line_data, $data_set->get_field_validator_manager() );
					continue;
				}
				if( $config['error_lines'] == "yes" ) {
					$out .= \OpenClub\CSV_Util::get_csv_table_row( $line_data, $data_set->get_field_validator_manager() );
				}
			}
			$out .= "</table>\n";
		} else {
			$out .= 'No data';
		}

	} catch( \Exception $e ) {
		$out .= 'Error: ' . $e->getMessage();
	}

	return $out;
}

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
function openclub_csv_example_data_set_filter( \OpenClub\Data_Set $data_set, $post ){
	return $data_set;
}
add_filter( 'openclub_csv_filter_data', 'openclub_csv_example_data_set_filter', OPENCLUB_DEFAULT_FILTER_PRIORITY, 2 );
