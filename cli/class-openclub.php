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

		/**
		 * @var $input \OpenClub\Data_Set_Input
		 */
		$input = \OpenClub\Factory::get_data_input_object( $this->post_id );

		/**
		 * @var $output \OpenClub\Output_Data
		 */
		$output_data = \OpenClub\Factory::get_output_data( $input );
		
		WP_CLI::log( sprintf( '====== Retrieving data from post %d =======', $input->get_post_id() ) );

		foreach ( $output_data->get_rows() as $row) {
			WP_CLI::log( $row['data']['First Name']['formatted_value'] . ' ' . $row['data']['Second name']['formatted_value'] );
		}

		if ( $output_data->get_errors() ) {
			WP_CLI::log( '====== Completed with errors! =======' );
			return;
		}

		WP_CLI::success( '====== Success! ====== ' );

	}


	private function log_settings( $settings ) {

		foreach ( $settings as $field_name => $settings ) {
			WP_CLI::log( $field_name );
			foreach ( $settings as $key => $value ) {
				WP_CLI::log( $key . ': ' . $value );
			}
			WP_CLI::log( '----------------------------' );
		}
	}

}


