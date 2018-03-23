<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	 * @var $has_validation_error bool
	 */
	private $has_validation_error = false;

	public function __construct( $line_number, array $data, $has_validation_error ) {

		if ( !is_numeric( $line_number ) ) {
			throw new \Exception( 'Invalid line number passed: ' . $line_number );
		}

		if ( !is_bool( $has_validation_error )) {
			throw new \Exception( '$has_validation_error must be a boolean.' );
		}

		if ( empty( $data ) ) {
			throw new \Exception( '$data cannot be empty' );
		}

		$this->line_number = $line_number;
		$this->data        = $data;
		$this->has_validation_error = $has_validation_error;

	}

	public function get_data( $return_as_string = false ) {

		return $return_as_string ? implode( ',', $this->data ) : $this->data;
	}

	public function __toString() {

		$validation_error_flag = $this->has_validation_error ? '[ERROR]' : '';

		return $validation_error_flag . implode( ',', $this->data ) ;
	}

	public function has_validation_error(){
		return $this->has_validation_error;
	}

	public function get_line_number(){
		return $this->line_number;
	}

}