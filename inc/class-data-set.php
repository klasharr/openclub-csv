<?php

namespace OpenClub;

use SSCMods\Fields\FieldValidatorManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Data_Set {


	/**
	 * @var \WP_Post $post
	 */
	private $post;


	/**
	 *
	 * @var array $data_rows
	 */
	private $data_rows = array();


	/**
	 * @var array $errors
	 */
	private $errors = array();


	/**
	 * @var Field_Validator_Manager $field_validator_manager
	 */
	private $field_validator_manager;

	/**
	 * @var array
	 */
	private $header_fields = array();


	/**
	 * Data_Set constructor.
	 *
	 * @param \WP_Post $post
	 */
	public function __construct( \WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * @param $line_number
	 * @param DTO $dto
	 * @param Parser $parser
	 *
	 * @throws \Exception
	 */
	public function push_row( $line_number, DTO $dto, Parser $parser) {

		if( $parser->get_group_by_field() ) {
			if( !$parser->field_validator_manager->is_valid_field( $parser->get_group_by_field() ) ) {
				throw new \Exception('The group by field: %s is invalid, please check field headings and settings',
					$parser->get_group_by_field() );
			}
			$a = $dto->get_data();

			$this->data_rows[ $a[$parser->get_group_by_field()]][] = $dto;

		} else {

			$this->validate_number( $line_number );
			$this->data_rows[ $line_number ] = $dto;
		}
	}


	/**
	 * @param int $line_number
	 * @param string $message
	 *
	 * @throws \Exception
	 */
	public function push_line_error_message( $line_number, $message ) {

		$this->validate_number( $line_number );
		$this->errors[ $line_number ] = $message;
	}

	/**
	 * @param array $header_fields
	 */
	public function set_header_fields( array $header_fields ){

		$this->header_fields = $header_fields;

	}

	/**
	 * @param Field_Validator_Manager $field_validator_manager
	 */
	public function set_field_validator_manager( Field_Validator_Manager $field_validator_manager ){

		$this->field_validator_manager = $field_validator_manager;
	}

	/**
	 * @param $line_number
	 *
	 * @throws \Exception
	 */
	private function validate_number( $line_number ){
		if( !is_numeric( $line_number ) || $line_number < 0 ){
			throw new \Exception('$line_number must have a positive integer value.');
		}
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function get_header_fields(){

		if(empty( $this->header_fields )){
			throw new \Exception('$header_fields has not been set');
		}

		return $this->header_fields;
	}

	/**
	 * @return Field_Validator_Manager
	 * @throws \Exception
	 */
	public function get_field_validator_manager(){

		if(empty( $this->field_validator_manager) ) {
			throw new \Exception('$field_validator_manager has not been set');
		}

		return $this->field_validator_manager;
	}

	/**
	 * @return array
	 */
	public function get_line_errors(){
		return $this->errors;
	}

	/**
	 * @return array 
	 */
	public function get_result_set(){
		return $this->data_rows;
	}

	/**
	 * @return bool
	 */
	public function has_data(){
		return !empty( $this->data_rows ) ? true: false;
	}

	/**
	 * @return bool
	 */
	public function has_errors(){
		return !empty( $this->errors ) ? true: false;
	}

	/**
	 * @return array
	 */
	public function get_errors(){
		return $this->errors;
	}

	/**
	 * @return array of DTO
	 */
	public function get_data(){
		return $this->data_rows;
	}
}