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


	public function __construct( $test_data_key ) {

		$this->set_input_and_output_samples();
		$this->test_data_key = $test_data_key;

	}

	public function get( $type ) {
		return $this->input_and_output_samples[ $this->test_data_key ][ $type ];
	}


}