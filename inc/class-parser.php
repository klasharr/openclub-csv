<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use OpenClub\Fields\Validator_Field_Exception;

require_once( 'class-factory.php' );


class Parser {

	/**
	 * @var $post \WP_Post
	 */
	public $post;

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


	/**
	 * @var Data_Set $data_set
	 */
	private $data_set;

	/**
	 * @var null
	 */
	private $group_by_field = null;


	/**
	 * @var $input Data_Set_Input
	 */
	private $input;


	/**
	 * Parser constructor.
	 *
	 * @param Data_Set_Input $input
	 */
	public function __construct( Data_Set_Input $input ) {
		
		$this->input = $input;

		if ( empty( trim( $this->input->get_post()->post_content ) ) ) {
			throw new \Exception( 'Post ID '. $this->input->get_post() . ' has no content.' );
		}

		$this->data_set = Factory::get_data_set( $this->input->get_post() );
		$this->field_validator_manager = Factory::get_field_validator_manager( $this->input->get_post() );

	}


	public function set_group_by_field( $field ) {

		if( !$this->has_field( $field ) ) {
			throw new \Exception( 'Trying to group on field: '. $field .' which does not exist' );
		}
		$this->group_by_field = $field;
	}

	public function get_group_by_field() {
		return $this->group_by_field;
	}

	/**
	 * @param $csv_line string
	 */
	private function set_header_from_csv( $csv_line ) {
		
		$fields = explode( ",", $csv_line );

		$this->header_fields       = $fields;
		$this->header_fields_count = count( $this->header_fields );
	}

	/**
	 * @return array
	 */
	private function get_header_fields( $return_as_array = true ) {

		return $return_as_array ? $this->header_fields : implode( ',', $this->header_fields );
	}

	/**
	 * @return int|null
	 */
	private function get_header_fields_count() {
		return $this->header_fields_count;
	}

	/**
	 * @param Filter $filter
	 *
	 * @return mixed|Data_Set|void
	 * @throws \Exception
	 */
	public function get_data() {

		$data_file = explode( "\n", esc_html( $this->input->get_post()->post_content ) );

		$line_number = 0;
		foreach ( $data_file as $data_line ) {

			$error_message = '';
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
					sprintf( 'Line %d column count mismatch, expected %d columns.  Header columns are: %s. Data is: %s.',
						$this->get_line_number($line_number),
						$this->get_header_fields_count(),
						$this->get_header_fields( false ),
						$data_line
					)
				);
			}

			$i = 0;
			$field_value_pairs = array();
			foreach ( $data_array as $i => $field ) {
				$field_value_pairs[ trim( $this->header_fields[ $i ] ) ] = trim( $field );
			}

			try {

				$this->validate_data( $field_value_pairs );

			} catch ( Validator_Field_Exception $e ) {

				$error_message = sprintf( 'Field validation error line: %d %s', $this->get_line_number($line_number), $e->getMessage() );

				$this->log_cli_error( $error_message );
				$this->data_set->push_line_error_message( $this->get_line_number($line_number), $error_message );
				$has_validation_error = true;
			}

			try {

				/** @var $dto DTO */
				$dto = Factory::get_dto( $this->get_line_number($line_number), $field_value_pairs, $has_validation_error );

				if ( $this->input->get_filter()->is_filtered_out( $dto ) ) {
					$line_number ++;
					continue;
				}
				$this->data_set->push_row( $this->get_line_number($line_number), $dto );

			} catch ( DTO_Exception $e ) {

				$this->log_cli_error( $error_message );
				$this->data_set->push_line_error_message( $this->get_line_number($line_number), $e->getMessage() );
			}

			$line_number ++;
		}

		$this->data_set->set_header_fields( $this->get_header_fields() );
		$this->data_set->set_field_validator_manager( $this->field_validator_manager );

		$this->data_set = apply_filters( 'openclub_csv_filter_data', $this->data_set, $this->input->get_post() );

		return $this->data_set;
	}

	/**
	 * @param $field_value_pairs array
	 */
	private function validate_data( $field_value_pairs ) {

		foreach ( $field_value_pairs as $field_name => $value ) {
			$field_validator = $this->get_validator( $field_name );
			$field_validator->validate( $value );
		}

	}

	public function has_field( $field ) {
		return in_array( $field, $this->header_fields );
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

		if(empty($field_name)){
			throw new \Exception( 'There\'s an empty column, please remove from the CSV.' );
		}

		if ( ! $field_validator = $this->field_validator_manager->get_validator( $field_name ) ) {
			throw new \Exception( 'A validator for ' . $field_name . ' does not exist, check the column name against the field setting in \'fields\' to see that they match.' );
		}

		$this->validators[ $field_name ] = $field_validator;

		return $field_validator;

	}

	private function get_line_number( $line_number ){
		return ( $line_number - 1 ) ;
	}


	private function log_cli_error( $error_message ) {
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::log( $error_message );
		}
	}

}