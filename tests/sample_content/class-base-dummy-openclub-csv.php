<?php

namespace OpenClubCSV\TestData;

abstract class Base_Dummy_Openclub_CSV {

	/**
	 * @var array
	 */
	protected $input_and_output_samples;

	/**
	 * @var string
	 */
	protected $test_data_letter;


	public function __construct( $test_data_letter ) {

		$this->set_input_and_output_samples();
		$this->test_data_letter = $test_data_letter;

	}

	public function get( $type ){
		return $this->input_and_output_samples[ $this->test_data_letter ][ $type ];
	}


}