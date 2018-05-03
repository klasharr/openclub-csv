<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use OpenClub\Fields\Field_Exception;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-factory.php' );


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
	private $header_field_names = array();

	/**
	 * @var int|null
	 */
	private $header_field_names_count = null;

	/**
	 * @var $field_manager Field_Manager
	 */
	public $field_manager;


	/**
	 * @var array
	 */
	private $fields = array();


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
	public $input;


	/**
	 * Parser constructor.
	 *
	 * @param Data_Set_Input $input
	 */
	public function __construct( Data_Set_Input $input ) {

		$this->input = $input;

		if ( empty( trim( $this->input->get_post()->post_content ) ) ) {
			throw new \Exception( 'Post ID ' . $this->input->get_post()->ID . ' has no content.' );
		}

		$this->data_set      = Factory::get_data_set( $this->input->get_post() );
		$this->field_manager = Factory::get_field_manager( $this->input );
		$this->set_group_by_field( $input->get_group_by_field() );

	}


	public function set_group_by_field( $field ) {

		if ( ! empty( $field ) && ! $this->field_manager->get_field( $field, true ) ) {
			throw new \Exception( 'Trying to group on field: ' . $field . ' which does not exist' );
		}
		$this->group_by_field = $field;
	}

	public function get_group_by_field() {
		return $this->group_by_field;
	}

	/**
	 * @param $csv_line string
	 */
	private function set_header_field_names_and_count_from_csv( $csv_line ) {

		$csv_header_field_names = explode( ",", $csv_line );

		foreach ( $csv_header_field_names as $cvs_field_name ) {
			if ( ! $this->field_manager->get_field( trim( $cvs_field_name ) ) ) {
				throw new \Exception( 'Field name in CSV header is invalid, can not parse data.' );
			}
		}

		$this->header_field_names       = $csv_header_field_names;
		$this->header_field_names_count = count( $this->header_field_names );
	}

	/**
	 * @return array
	 */
	public function get_header_field_names( $return_as_array = true ) {

		return $return_as_array ? $this->header_field_names : implode( ',', $this->header_field_names );
	}

	/**
	 * @return int|null
	 */
	private function get_header_field_names_count() {
		return $this->header_field_names_count;
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

			$error_message        = '';
			$has_validation_error = false;

			if ( 0 === $line_number ) {
				$this->set_header_field_names_and_count_from_csv( $data_line );
				$line_number ++;
				continue;
			}

			if ( empty( trim( $data_line ) ) ) {
				continue;
			}

			$data_array = explode( ",", $data_line );

			if ( count( $data_array ) !== $this->header_field_names_count ) {
				throw new \Exception(
					sprintf( 'Post %d, line %d column count mismatch, expected %d columns.  Header columns are: %s. Data is: %s.',
						$this->input->get_post()->ID,
						$this->get_line_number( $line_number ),
						$this->get_header_field_names_count(),
						$this->get_header_field_names( false ),
						$data_line
					)
				);
			}

			$i                 = 0;
			$field_value_pairs = array();
			foreach ( $data_array as $i => $field ) {
				$field_value_pairs[ trim( $this->header_field_names[ $i ] ) ] = trim( $field );
			}

			try {

				$this->validate_data( $field_value_pairs );

			} catch ( Field_Exception $e ) {

				$error_message = sprintf( 'Field validation error line: %d %s', $this->get_line_number( $line_number ), $e->getMessage() );

				$this->log_cli_error( $error_message );
				$this->data_set->push_line_error_message( $this->get_line_number( $line_number ), $error_message );
				$has_validation_error = true;
			}

			try {

				/** @var $dto DTO */
				$dto = Factory::get_dto( $this->get_line_number( $line_number ), $field_value_pairs, $has_validation_error );

				if ( $this->input->get_filter()->is_filtered_out( $dto ) ) {
					$line_number ++;
					continue;
				}

				$row_meta = array(
					'line_number'    => $this->get_line_number( $line_number ),
					'group_by_field' => $this->get_group_by_field(),
					'field_manager'  => $this->field_manager,
				);

				$this->data_set->push_row( $row_meta, $dto );

			} catch ( DTO_Exception $e ) {

				$this->log_cli_error( $error_message );
				$this->data_set->push_line_error_message( $this->get_line_number( $line_number ), $e->getMessage() );
			}

			$line_number ++;
		}

		$this->data_set->set_header_field_names( $this->get_header_field_names() );
		$this->data_set->set_field_manager( $this->field_manager );

		$this->data_set = apply_filters( 'openclub_csv_filter_data', $this->data_set, $this->input->get_post() );

		return $this->data_set;

	}

	/**
	 * @param $field_value_pairs array
	 */
	private function validate_data( $field_value_pairs ) {

		foreach ( $field_value_pairs as $field_name => $value ) {
			$field_validator = $this->get_field_object( $field_name );
			$field_validator->validate( $value );
		}

	}

	/**
	 * @param $field_name
	 *
	 * @return bool|mixed
	 * @throws Exception
	 */
	private function get_field_object( $field_name ) {

		if ( isset( $this->fields[ $field_name ] ) ) {
			return $this->fields[ $field_name ];
		}

		if ( empty( $field_name ) ) {
			throw new \Exception( 'There\'s an empty column, please remove from the CSV.' );
		}

		$field = $this->field_manager->get_field( $field_name );

		if( !$field ) {
			throw new \Exception( 'A validator for ' . $field_name . ' does not exist, check the column name against the field setting in \'fields\' to see that they match.' );
		}

		$this->fields[ $field_name ] = $field;

		return $field;

	}

	private function get_line_number( $line_number ) {
		return ( $line_number - 1 );
	}


	private function log_cli_error( $error_message ) {
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::log( $error_message );
		}
	}


}