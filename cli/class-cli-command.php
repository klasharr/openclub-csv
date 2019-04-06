<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \OpenClub\CSV_Display;
use \OpenClub\Factory;
use \OpenClub\CSV_Util;
use \WP_CLI;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/cli/class-cli-base.php' );

Class CLI_Command extends CLI_Base {

	/**
	 * @todo standardize error validation output.
	 *
	 * @throws \Exception
	 * @throws \OpenClub\Exception
	 */
	public function list( $args ) {

		try {

			if ( empty( $args[0] ) || (int) $args[0] === 0 ) {
				throw new \Exception( 'The first argument must be a non zero integer value.' );
			}

			$id = $args[0];

			$output_data = $this->get_data(
				array(
					'post_id' => $id,
					'display' => 'default', // @todo it breaks if this missing, set the default elsewhere.
				)
			);

			WP_CLI::log( sprintf( '====== Retrieving data from ID %d =======', $id ) );

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

			WP_CLI::success( '====== Complete! ====== ' );

		} catch ( \Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

}