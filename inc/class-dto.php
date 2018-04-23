<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'class-dto.php' );

class DTO {

	/**
	 * @var $line_number int
	 */
	private $line_number;

	/**
	 * @var $data array
	 */
	private $data;


	/**
	 * @var $num_fields int
	 */
	private $num_fields;


	/**
	 * @var $has_validation_error bool
	 */
	private $has_validation_error = false;

	public function __construct( $line_number, array $data, $has_validation_error ) {

		if ( ! is_numeric( $line_number ) ) {
			throw new \DTO_Exception( 'Invalid line number passed: ' . $line_number );
		}

		if ( ! is_bool( $has_validation_error ) ) {
			throw new \DTO_Exception( '$has_validation_error must be a boolean.' );
		}

		if ( empty( $data ) ) {
			throw new \DTO_Exception( '$data cannot be empty' );
		}

		$this->line_number          = $line_number;
		$this->data                 = $data;
		$this->has_validation_error = $has_validation_error;
		$this->num_fields           = count( $data );

	}

	public function get_data( $return_as_string = false ) {

		return $return_as_string ? implode( ',', $this->data ) : $this->data;
	}

	public function __toString() {

		$validation_error_flag = $this->has_validation_error ? '[Validation ERROR]' : '';

		return $validation_error_flag . implode( ',', $this->data );
	}

	public function has_validation_error() {
		return $this->has_validation_error;
	}

	public function get_line_number() {
		return $this->line_number;
	}

	public function get_fields_count() {
		return $this->num_fields;
	}

	public function get_value( $key ) {
		return $this->data[ $key ];
	}

}