<?php

namespace OpenClub;

use \WP_Post;
use \Exception;

require_once( 'class-field-validator-manager.php' );
require_once( 'class-null-filter.php' );

class Factory {

	public static function get_parser() {
		return new Parser;
	}

	public static function get_field_validator_manager( WP_Post $post ) {
		return new Field_Validator_Manager( $post );
	}

	public static function get_null_filter() {
		return new Null_Filter();
	}

	public static function get_field( $class_name, $rules ) {

		$file = OPENCLUB_IMPORTER_PLUGIN_DIR . 'inc/fields/class-' . strtolower( $rules['type'] ) . '.php';

		if ( ! file_exists( $file ) ) {
			throw new Exception( $file . ' does not exist' );
		}

		require_once( $file );

		$namespaced_class_name = '\OpenClub\\Fields\\' . $class_name;

		return new $namespaced_class_name( $rules );
	}
}