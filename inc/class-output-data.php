<?php

namespace OpenClub;

/**
 *
 * @todo merge back to class-data-set.php
 *
 *
 * @todo group by
 * @todo limit
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
	public $rows = array();

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

		$this->data_set      = $parser->get_data();
		$this->input         = $input;
		$this->field_manager = $this->data_set->get_field_manager();
		$this->header_fields = $this->field_manager->get_display_field_names();
		$this->normalise_rows();

	}

	public function exists() {
		return count( $this->rows ) > 0 ? true : false;
	}

	public function get_header() {
		return $this->header_fields;
	}

	public function get_rows() {
		return $this->rows;
	}

	public function get_errors() {
		return $this->data_set->get_errors();
	}

	public function get_header_fields() {

		if ( $this->input->has_overridden_fields() ) {
			return $this->input->get_overridden_fields();
		}
		return $this->header_fields;
	}


	/**
	 *
	 * @throws \Exception
	 */
	private function normalise_rows() {

		$errors         = $this->get_errors();
		$group_by_field = null;

		$limit       = false;
		$line_number = 0;

		$field_names = $this->field_manager->get_display_field_names();

		if ( $this->input->has_overridden_fields() ) {
			$field_names = $this->input->get_overridden_fields();
		}

		$limit = $this->input->get_limit();

		$group_by_field = $this->input->get_group_by_field();

		if ( $group_by_field ) {

			$grouped_count = 1;

			foreach ( $this->data_set->get_rows() as $grouped_field_value => $rows ) {

				if ( 'date' === $this->field_manager->get_field_type( $this->input->get_group_by_field() ) &&
				     ( $this->input->is_show_future_events_only() && $grouped_field_value < time() )
				) {
					continue;
				}

				if ( $limit && ( $grouped_count > $limit ) ) {
					break;
				}

				$line_number = 0;

				/** @var DTO $dto */
				foreach ( $rows as $dto ) {
					$this->rows[ $grouped_field_value ][ $line_number ] = $this->get_row_data( $field_names, $dto, $errors, $line_number );
					$line_number ++;

				}
				$grouped_count ++;
			}

			return;

		} else {

			/** @var DTO $dto */
			foreach ( $this->data_set->get_rows() as $dto ) {

				if ( $limit && $line_number > $limit ) {
					break;
				}
				$this->rows[ $line_number ] = $this->get_row_data( $field_names, $dto, $errors, $line_number );
				$line_number ++;
			}
		}
	}

	/**
	 * @param array $field_names
	 * @param DTO $dto
	 * @param array $errors
	 * @param int $line_number
	 *
	 * @return array
	 * @throws \Exception
	 */
	private function get_row_data( array $field_names, DTO $dto, array $errors, int $line_number ) {

		$tmp = array();

		foreach ( $field_names as $field_name ) {

			$tmp[ $field_name ] = array(
				'value'           => esc_html( $dto->get_value( $field_name ) ),
				'formatted_value' => esc_html( 
					$this->field_manager->get_field( $field_name )->format_value( $dto->get_value( $field_name ) )
				),
				'display_default' => $this->field_manager->get_field( $field_name )->is_displayed(),
			);
		}

		return array(
			'data'          => $tmp,
			'class'         => $dto->has_validation_error() ? 'openclub_csv_error' : '',
			'error'         => $dto->has_validation_error() ? 1 : 0,
			'error_message' => $dto->has_validation_error() ? $errors[ $line_number ] : '',
		);
	}
}