<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use OpenClub\Fields\Validator_Field_Exception;

require_once( 'class-factory.php' );


class Parser {

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var $line_errors array
	 */
	private $line_errors = array();

	/**
	 * @var array
	 */
	private $header_fields = array();

	/**
	 * @var int|null
	 */
	private $header_fields_count = null;

	/**
	 * @var $field_validator_manager Field_Validator_Manager
	 */
	private $field_validator_manager;


	/**
	 * @var array
	 */
	private $validators = array();


	public function __construct() {}

	/**
	 * @param WP_Post $post
	 *
	 * @throws Exception
	 */
	public function init( \WP_Post $post ) {

		if ( empty( trim( $post->post_content ) ) ) {
			throw new \Exception( '$post->post_content is empty' );
		}

		$this->field_validator_manager = Factory::get_field_validator_manager( $post );
		$this->content = $post->post_content;
	}


	/**
	 * @param $csv_line string
	 */
	private function set_header_from_csv( $csv_line ) {
		$this->header_fields       = explode( ",", $csv_line );
		$this->header_fields_count = count( $this->header_fields );
	}

	/**
	 * @return array
	 */
	private function get_header_fields( $return_array = true ) {

		return $return_array ? implode( ',', $this->header_fields ) : $this->header_fields;
	}

	/**
	 * @return int|null
	 */
	private function get_header_fields_count() {
		return $this->header_fields_count;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function get_data( Filter $filter ) {

		$out = array(
			'data'   => array(),
			'errors' => array(),
		);

		$data_file = explode( "\n", $this->content );

		$line_number = 0;
		foreach ( $data_file as $data_line ) {

			$error_message = false;
			$has_validation_error = false;

			if ( $line_number == 0 ) {
				$this->set_header_from_csv( $data_line );
				$this->get_header_fields_count();
				$line_number ++;
				continue;
			}

			if ( empty( trim( $data_line ) ) ) {
				continue;
			}

			$data_array = explode( ",", $data_line );

			if ( count( $data_array ) != $this->header_fields_count ) {
				throw new \Exception(
					sprint( 'Line %d column count mismatch, expected %d columns.  Header columns are: %s. Data is: %s.',
						$this->get_line_number($line_number),
						$this->get_header_fields_count(),
						$this->get_header_fields( false ),
						$data_line
					)
				);
			}

			$i = 0;
			foreach ( $data_array as $i => $field ) {
				$data[ trim( $this->header_fields[ $i ] ) ] = trim( $field );
			}

			try {

				$this->validate_data( $data );

			} catch ( Validator_Field_Exception $e ) {

				$error_message = sprintf( 'Field validation error line: %d %s', $this->get_line_number($line_number), $e->getMessage() );
				$this->line_errors[ $this->get_line_number($line_number) ] = $error_message;
				if ( class_exists( 'WP_CLI' ) ) {
					\WP_CLI::log( $error_message );
				}
				$has_validation_error = true;
			}



			try {

				/** @var $dto DTO */
				$dto = Factory::get_dto( $this->get_line_number($line_number), $data, $has_validation_error );

				if ( $filter->is_filtered_out( $dto ) ) {
					continue;
				}
				$out['data'][] = $dto;

			} catch ( \Exception $e ) {
				if ( class_exists( 'WP_CLI' ) ) {
					\WP_CLI::log( $e->getMessage() );
				}
				$this->line_errors[ $this->get_line_number($line_number) ] = $e->getMessage();
				continue;
			}

			$line_number ++;
		}

		$out['errors'] = $this->line_errors;

		return $out;
	}

	/**
	 * @param $data array
	 */
	private function validate_data( $data ) {

		foreach ( $data as $field_name => $value ) {
			$field_validator = $this->get_validator( $field_name );
			$field_validator->validate( $value );
		}

	}


	/**
	 * @param $field_name
	 *
	 * @return bool|mixed
	 * @throws Exception
	 */
	private function get_validator( $field_name ) {

		if ( isset( $this->validators[ $field_name ] ) ) {
			return $this->validators[ $field_name ];
		}

		if ( ! $field_validator = $this->field_validator_manager->get_validator( $field_name ) ) {
			throw new \Exception( 'A validator for ' . $field_name . ' does not exist, check the field name and field settings to see that they match.' );
		}

		$this->validators[ $field_name ] = $field_validator;

		return $field_validator;

	}

	private function get_line_number( $line_number ){
		return ( $line_number - 1 ) ;
	}
}