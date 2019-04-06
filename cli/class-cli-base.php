<?php
namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \OpenClub\CSV_Display;
use \OpenClub\Factory;
use \OpenClub\CSV_Util;
use \WP_CLI;

abstract class CLI_Base {

	/**
	 * @param array $args
	 *
	 * @return Data_Set_Input
	 */
	protected function get_data_set_input( array $args ) {

		return Factory::get_data_input_object( $args );
	}

	/**
	 * @param Data_Set_Input $input
	 *
	 * @return Output_Data
	 * @throws \Exception
	 */
	protected function get_output_data( Data_Set_Input $input ) {

		$data = Factory::get_output_data( $input );

		if ( ! $data->exists() ) {
			throw new \Exception( 'No data for $input' );
		}

		return $data;
	}

	/**
	 * @param $args
	 *
	 * @return Output_Data
	 * @throws \Exception
	 */
	public function get_data( array $args ){

		return $this->get_output_data(
			$this->get_data_set_input( $args )
		);
	}


}