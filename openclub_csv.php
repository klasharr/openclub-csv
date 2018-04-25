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

add_action( 'fm_post_openclub-csv', function () {

	$fm = new Fieldmanager_TextArea( array(
		'name'       => 'fields',
		'attributes' => array(
			'rows' => 10,
			'cols' => 80,
		),
	) );
	$fm->add_meta_box( 'CSV field descriptions', array( 'openclub-csv' ) );
} );


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
		openclub_csv_get_config_defaults(),
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

add_action( 'wp_head', 'openclub_csv_add_inline_css' );


function openclub_csv_view_content_page( $content ) {

	/**
	 * @post WP_Post
	 */
	global $post;

	if ( is_singular() && in_array( get_post_type( $post ), array( 'openclub-csv' ) ) ) {

		return openclub_csv_get_display_table(
			openclub_csv_get_config_defaults( array( 'post_id' => $post->ID ) )
		);

	}

	return $content;

}

add_filter( 'the_content', 'openclub_csv_view_content_page' );


function openclub_csv_get_display_table( $config ) {

	$out = '';

	try {

		/**
		 * @var $input \OpenClub\Data_Set_Input
		 */
		$input = \OpenClub\Factory::get_data_input_object( $config['post_id'] );

		if($config['group_by_field']){
			$input->set_group_by_field($config['group_by_field']);
		}

		/**
		 * @var $output \OpenClub\Output_Data
		 */
		$output_data = \OpenClub\Factory::get_output_data( $input );

		if ( !empty( $output_data ) && $output_data->exists() ) {

			$templates = \OpenClub\Factory::get_template_loader();

			$templates->set_template_data(
				array(
					'output_data' => $output_data,
					'config' => $config
				)
			);

			echo $templates->get_template( $config[ 'display' ] );

		} else {
			$out .= __( 'No data', 'openclub_csv' );
		}

	} catch ( \Exception $e ) {
		$out .= __( 'Error', 'openclub_csv' ).': ' . $e->getMessage();
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
function openclub_csv_example_data_set_filter( \OpenClub\Data_Set $data_set, $post ) {
	return $data_set;
}

add_filter( 'openclub_csv_filter_data', 'openclub_csv_example_data_set_filter', OPENCLUB_DEFAULT_FILTER_PRIORITY, 2 );

function openclub_csv_log_cli( $message ) {
	if ( class_exists( 'WP_CLI' ) ) {
		\WP_CLI::log( $message );
	}
}

/**
 * @param array $config
 *
 * @return array
 */
function openclub_csv_get_config_defaults( $config = array() ) {

	return array_replace(
		array(
			'post_id'            => null,
			'error_messages'     => "yes",
			'error_lines'        => "yes",
			'future_events_only' => null,
			'display'            => 'table',
			'fields'             => null,
			'group_by_field'     => null,
		),
		$config
	);

}