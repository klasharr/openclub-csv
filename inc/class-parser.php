<?php

namespace OpenClub;

use WP_CLI;
use Exception;
use WP_Post;
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


	public function __construct() {
	}

	/**
	 * @param WP_Post $post
	 *
	 * @throws Exception
	 */
	public function init( WP_Post $post ) {

		if ( empty( trim( $post->post_content ) ) ) {
			throw new Exception( '$post->post_content is empty' );
		}

		$this->field_validator_manager = Factory::get_field_validator_manager( $post );

		$this->content = $post->post_content;
	}


	/**
	 * @param $csv_line string
	 */
	private function set_header( $csv_line ) {
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

			$error_message = '';

			if ( $line_number == 0 ) {
				$this->set_header( $data_line );
				$this->get_header_fields_count();
				$line_number ++;
				continue;
			}

			if ( empty( trim( $data_line ) ) ) {
				break;
			}

			$data_array = explode( ",", $data_line );

			if ( count( $data_array ) != $this->header_fields_count ) {
				throw new Exception(
					sprint( 'Line %d column count mismatch, expected %d columns.  Header columns are: %s. Data is: %s.',
						$line_number,
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

				$error_message = sprintf( 'Field validation error line: %d %s', $line_number, $e->getMessage() );
				if ( class_exists( 'WP_CLI' ) ) {
					WP_CLI::log( $error_message );
				} else {
					$this->line_errors[ $line_number ] = array(
						'line'  => $line_number,
						'error' => $error_message
					);

				}
				continue;
			}

			try {

				//print_r( $data );
				/** @var $dto DTO */
				//$dto = new DTO( $line_number, $data );

			} catch ( Exception $e ) {
				if ( class_exists( 'WP_CLI' ) ) {
					WP_CLI::log( $error_message );
				} else {
					$this->line_errors[ $line ] = array(
						'line'  => $line_number,
						'error' => $error_message
					);
				}
				continue;
			}

			/**
			 * if ( ! $filter->filter( $dto ) ) {
			 * continue;
			 * }
			 *
			 * $out['data'][ $dto->getDate() ][] = $dto;
			 */
			$line_number ++;
		}

		$out['errors'] = $this->line_errors;

		return $out;
	}

	/**
	 *
	 * @param $data array
	 *
	 * For example where Day will map to a validator object.
	 *
	 * array(
	 *   Day => Sun
	 *   Date => 12/09/18
	 *   Team => A
	 * )
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
			throw new Exception( 'A validator for ' . $field_name . ' does not exist, check the field name and field settings to see that they match.' );
		}

		$this->validators[ $field_name ] = $field_validator;

		return $field_validator;

	}
}