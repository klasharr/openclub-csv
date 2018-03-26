<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'class-field-validator-manager.php' );
require_once( 'class-null-filter.php' );
require_once( 'class-dto.php' );
require_once( 'class-parser.php' );
require_once( 'class-data-set.php' );

class Factory {

	/**
	 * @var array $data_sets
	 */
	private static $data_sets = array();

	public static function get_parser() {
		return new Parser;
	}

	public static function get_field_validator_manager( \WP_Post $post ) {
		return new Field_Validator_Manager( $post );
	}

	public static function get_null_filter() {
		return new Null_Filter();
	}

	public static function get_dto( int $line_number, array $data, $has_validation_error ) {
		return new DTO( $line_number, $data, $has_validation_error );
	}

	public static function get_field( $class_name, $config ) {

		$file = OPENCLUB_CSV_PLUGIN_DIR . 'inc/fields/class-' . strtolower( $config['type'] ) . '.php';

		if ( ! file_exists( $file ) ) {
			throw new \Exception( $file . ' does not exist' );
		}

		require_once( $file );

		$namespaced_class_name = '\OpenClub\\Fields\\' . $class_name;

		return new $namespaced_class_name( $config );
	}

	/**
	 * @param \WP_Post $post
	 *
	 * @return Data_Set
	 */
	public static function get_data_set( \WP_Post $post ){

		if( isset( self::$data_sets[ $post->ID] ) ) {
			return self::$data_sets[ $post->ID];
		}

		$data_set = new Data_Set( $post );
		self::$data_sets[ $post->ID] = $data_set;
		return $data_set;
	}
}