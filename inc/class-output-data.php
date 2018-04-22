<?php

namespace OpenClub;

/**
 *
 * @todo group by
 * @todo limit
 * @todo display override
 *
 * Class Output_Data
 * @package OpenClub
 */
class Output_Data {

	/**
	 * @var Data_Set
	 */
	private $data_set;

	/**
	 * @var array
	 */
	private $header_fields = array();

	/**
	 * @var array|void
	 */
	private $rows = array();

	/**
	 * @var Field_Manager
	 */
	private $field_manager;

	/**
	 * @var Data_Set_Input
	 */
	private $input;


	public function __construct( Data_Set_Input $input ) {

		$parser = Factory::get_parser( $input );

		$this->data_set = $parser->get_data();
		$this->input = $input;
		$this->field_manager = $this->data_set->get_field_manager();

		$this->header_fields = $this->field_manager->get_display_field_names();
		$this->normalise_rows();

	}

	public function exists(){
		return count( $this->rows ) > 0 ? true : false;
	}

	public function get_header(){
		return $this->header_fields;
	}

	public function get_rows(){
		return $this->rows;
	}

	public function get_header_fields() {

		return $this->header_fields;
	}


	private function normalise_rows(){

		$line_number = 1;

		/** @var DTO $dto */
		foreach( $this->data_set->get_data() as $dto ){

			foreach( $this->field_manager->get_display_field_names() as $field_name ) {

				$tmp[ $field_name ] = array(
					'value' => $dto->get_value( $field_name ),
					'formatted_value' => $this->field_manager->get_field( $field_name )->format_value( $dto->get_value( $field_name ) ),
					'validation_error' => $dto->has_validation_error() ? 1 : 0,
					'class' => $dto->has_validation_error() ? 'openclub_csv_error' : '',
					'display_default' => $this->field_manager->get_field( $field_name )->is_displayed(),
				);
			}

			$this->rows[ $line_number ] = $tmp;
			$line_number++;
		}
	}


	
}