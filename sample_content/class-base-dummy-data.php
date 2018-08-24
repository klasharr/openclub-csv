<?php

namespace OpenClubCSV\Test;

abstract class Base_Dummy_Data {

	/**
	 * @var array
	 */
	protected $input_and_output_samples;

	/**
	 * @var string
	 */
	protected $test_data_key;


	public function __construct( $test_data_key, $data = array() ) {

		$this->set_input_and_output_samples();
		$this->test_data_key = $test_data_key;

		if( !empty( $data ) ) {

			$this->validate_data_array( $data );
			$this->input_and_output_samples[ $test_data_key ] = $data;
		}

		if( empty( $this->input_and_output_samples[ $test_data_key ] ) ) {
			throw new \Exception( 'Data key ' . $test_data_key . ' has not been set. See \Sailing_Programme_Data::set_input_and_output_samples()' );
		}

	}

	public function get( $type ) {
		return $this->input_and_output_samples[ $this->test_data_key ][ $type ];
	}

	private function validate_data_array( array $data ){

		$keys = array( 'post_content', 'html_output', 'config', 'fields' );

		foreach( $keys as $key ) {
			if( !array_key_exists( $key, $data ) ) {
				throw new \Exception( 'Invalid data passed to Base_Dummy_Data::__construct');
			}
		}
	}

}