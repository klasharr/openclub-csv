<?php

namespace OpenClub\CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use OpenClub\CSV_Display;
use \OpenClub\Factory;
use \OpenClub\CSV_Util;
use \WP_CLI;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );

Class OpenClub {

	/**
	 * @todo standardize error validation output.
	 *
	 * @throws \Exception
	 * @throws \OpenClub\Exception
	 */
	public function post_content( $args ) {

		try {

			if ( empty( $args[0] ) || (int) $args[0] === 0 ) {
				throw new \Exception( 'The first argument must be a non zero integer value.' );
			}

			$this->post_id = $args[0];

			/**
			 * @var $input \OpenClub\Data_Set_Input
			 */
			$input = \OpenClub\Factory::get_data_input_object(
				array(
					'post_id' => $this->post_id,
				)
			);

			/**
			 * @var $output \OpenClub\Output_Data
			 */
			$output_data = \OpenClub\Factory::get_output_data( $input );

			WP_CLI::log( sprintf( '====== Retrieving data from post %d =======', $input->get_post_id() ) );

			if ( $errors = $output_data->get_errors() ) {
				foreach ( $errors as $line_number => $error_message ) {
					WP_CLI::warning( sprintf( 'Error: %d Message: %s', $line_number, $error_message ) );
				}
				WP_CLI::error( '====== Aborted with errors! =======' );
			}

			// Do something with the data here.
			foreach ( $output_data->get_rows() as $row ) {

				WP_CLI::log( CSV_Display::get_csv_row( $row ) );
			}

			WP_CLI::success( '====== Success! ====== ' );

		} catch ( \Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

}


