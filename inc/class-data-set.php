<?php

namespace OpenClub;

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
	 * @var array $rows
	 */
	private $rows = array();


	/**
	 * @var array $errors
	 */
	private $errors = array();


	/**
	 * @var Field_Manager $field_manager
	 */
	private $field_manager;

	/**
	 * @var array
	 */
	private $header_field_names = array();


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
	public function push_row( $row_meta, DTO $dto ) {

		if ( empty( $row_meta['field_manager'] ) ) {
			throw new \Exception( 'A field_manager must be passed' );
		}

		/* @var Field_Manager $field_manager */
		$field_manager = $row_meta['field_manager'];


		if ( ! empty( $row_meta['group_by_field'] ) ) {

			if ( 'date' === $field_manager->get_field_type( $row_meta['group_by_field'] ) ) {

				$date_field                                                                                 = $field_manager->get_field( $row_meta['group_by_field'] );
				$this->rows[ $date_field->get_timestamp( $dto->get_value( $row_meta['group_by_field'] ) ) ][] = $dto;

			} else {
				$this->rows[ $dto->get_value( $row_meta['group_by_field'] ) ][] = $dto;
			}

		} else {

			$this->validate_line_number( $row_meta['line_number'] );
			$this->rows[ $row_meta['line_number'] ] = $dto;
		}
	}


	/**
	 * @param int $line_number
	 * @param string $message
	 *
	 * @throws \Exception
	 */
	public function push_line_error_message( $line_number, $message ) {

		$this->validate_line_number( $line_number );
		$this->errors[ $line_number ] = $message;
	}

	/**
	 * @param array $header_fields_names
	 */
	public function set_header_field_names( array $header_field_names ) {

		$this->header_field_names = $header_field_names;
	}

	/**
	 * @param Field_Manager $field_manager
	 */
	public function set_field_manager( Field_Manager $field_manager ) {

		$this->field_manager = $field_manager;
	}

	/**
	 * @param $line_number
	 *
	 * @throws \Exception
	 */
	private function validate_line_number( $line_number ) {
		if ( ! is_numeric( $line_number ) || $line_number < 0 ) {
			throw new \Exception( '$line_number must have a positive integer value.' );
		}
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function get_header_field_names() {

		if ( empty( $this->header_field_names ) ) {
			throw new \Exception( '$header_field_names has not been set' );
		}

		return $this->header_field_names;
	}

	/**
	 * @return Field_Manager
	 * @throws \Exception
	 */
	public function get_field_manager() {

		if ( empty( $this->field_manager ) ) {
			throw new \Exception( '$field_manager has not been set' );
		}

		return $this->field_manager;
	}

	/**
	 * @return array
	 */
	public function get_line_errors() {
		return $this->errors;
	}

	/**
	 * @return bool
	 */
	public function has_data() {
		return ! empty( $this->rows ) ? true : false;
	}

	/**
	 * @return bool
	 */
	public function has_errors() {
		return ! empty( $this->errors ) ? true : false;
	}

	/**
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * @return array of DTO
	 */
	public function get_rows() {

		return $this->rows;
	}
}