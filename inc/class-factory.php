<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'class-field-manager.php' );
require_once( 'class-null-filter.php' );
require_once( 'class-dto.php' );
require_once( 'class-parser.php' );
require_once( 'class-data-set.php' );
require_once( 'class-data-set-input.php' );
require_once( 'class-template-loader.php' );
require_once( 'class-output-data.php' );
require_once( 'class-csv-display.php' );

class Factory {

	/**
	 * @var array $data_sets
	 */
	private static $data_sets = array();

	/**
	 * @param $input Data_Set_Input
	 *
	 * @return Parser
	 */
	public static function get_parser( $input ) {
		return new Parser( $input );
	}

	/**
	 * @param Data_Set_Input $input
	 *
	 * @return Field_Manager
	 */
	public static function get_field_manager( Data_Set_Input $input ) {
		return new Field_Manager( $input );
	}

	public static function get_null_filter() {
		return new Null_Filter();
	}

	public static function get_dto( int $line_number, array $data, $has_validation_error ) {
		return new DTO( $line_number, $data, $has_validation_error );
	}

	public static function get_field( $class_name, $config, Data_Set_Input $input ) {

		$file = OPENCLUB_CSV_PLUGIN_DIR . 'inc/fields/class-' . strtolower( $config['type'] ) . '.php';

		if ( ! file_exists( $file ) ) {
			throw new \Exception( $file . ' does not exist' );
		}

		require_once( $file );

		$namespaced_class_name = '\OpenClub\\Fields\\' . $class_name;

		return new $namespaced_class_name( $config, $input );
	}

	/**
	 * @param \WP_Post $post
	 *
	 * @return Data_Set
	 */
	public static function get_data_set( \WP_Post $post ) {

		if ( isset( self::$data_sets[ $post->ID ] ) ) {
			return self::$data_sets[ $post->ID ];
		}

		$data_set                     = new Data_Set( $post );
		self::$data_sets[ $post->ID ] = $data_set;

		return $data_set;
	}


	/**
	 * @return Data_Set_Input
	 */
	public static function get_data_input_object( $post_id ) {
		return new Data_Set_Input( $post_id );
	}

	public static function get_template_loader() {
		return new Template_Loader;
	}

	public static function get_output_data( Data_Set_Input $input ) {
		return new Output_Data( $input );
	}

}