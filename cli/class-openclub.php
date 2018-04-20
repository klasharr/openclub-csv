<?php

namespace OpenClub\CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \OpenClub\Factory;
use \OpenClub\CSV_Util;
use \WP_CLI;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );

Class OpenClub {

	/**
	 * @throws \Exception
	 * @throws \OpenClub\Exception
	 */
	public function post_content( $args ) {

		try {

			if ( empty( $args[0] ) || (int) $args[0] === 0 ) {
				throw new \Exception( 'The first argument must be a non zero integer value.' );
			}

			$this->post_id = $args[0];

		} catch ( \Exception $e ) {
			\WP_CLI::error( $e->getMessage() );
		}

		$input = \OpenClub\Factory::get_data_input_object( $this->post_id );

		/**
		 * @var Data_Set @data_set
		 */
		$data_set = \OpenClub\CSV_Util::get_data_set( $input );

		WP_CLI::log( sprintf( '====== Retrieving data from post %d =======', $input->get_post_id() ) );

		/** @var DTO $line_data */
		foreach($data_set->get_data() as $line_data ){
			WP_CLI::log( $line_data );
		}

		if($data_set->has_errors()) {
			WP_CLI::log( '====== Completed with errors! =======');
			return;
		}

		WP_CLI::success( '====== Success! ====== ' );

	}

	public function config_check( $args ){

		try {

			if ( empty( $args[0] ) || (int) $args[0] === 0 ) {
				throw new \Exception( 'The first argument must be a non zero integer value.' );
			}

			$this->post_id = $args[0];

		} catch ( \Exception $e ) {
			\WP_CLI::error( $e->getMessage() );
		}

		$input = \OpenClub\Factory::get_data_input_object( $this->post_id );

		//$input->set_override_display_field_names( array( 'Team','Date' ) );

		$parser = Factory::get_parser( $input );

		echo $parser->get_header_fields( false );

		WP_CLI::success( '====== Success! ====== ' );
	}


	private function as_bool_string( $value ) {

		return $value ? 'true' : 'false';
	}

	private function log_settings( $settings ){

		foreach( $settings as $field_name => $settings ) {
			WP_CLI::log( $field_name );
			foreach( $settings as $key => $value ) {
				WP_CLI::log( $key . ': ' . $value );
			}
			WP_CLI::log( '----------------------------' );
		}
	}


}


