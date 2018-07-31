<?php

namespace OpenClub\Fields;

use OpenClub\Data_Set_Input;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
};

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/fields/class-base.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/fields/interface-field.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/fields/exception-field.php' );

class DateField extends Base_Field implements Field {

	/**
	 * @var int
	 */
	private $timestamp = null;


	public function __construct( $data, Data_Set_Input $input ) {
		parent::__construct( $data, $input );

		if ( empty( $this->data['input_format'] ) ) {
			throw new Field_Exception( 'Date field chosen but no input format specified, check the field description.' );
		}

		$date_pattern = '/^[djmn][\/\s]?[djSmn][\/\s]?[YyM]$/';

		if ( ! preg_match( $date_pattern, $this->data['input_format'] ) ) {
			throw new Field_Exception( 'Invalid input_format specified' );
		}

		if ( ! empty( $this->data['output_format'] ) && ! preg_match( $date_pattern, $this->data['output_format'] ) ) {
			throw new Field_Exception( 'Invalid output_format specified' );
		}

	}

	public function validate( $value ) {

		if ( empty( trim( $value ) ) ) {
			throw new Field_Exception( 'Date field validation failed, no data' );
		}

		/**
		 * @var \DateTime
		 */
		$datetime        = $this->get_date_time_object( $value );
		$this->timestamp = $datetime->getTimestamp();

	}

	private function get_date_time_object( $value ) {

		/**
		 * @var $datetime DateTime
		 */
		if ( ! $datetime = \DateTime::createFromFormat( $this->data['input_format'], $value ) ) {
			throw new Field_Exception( 'Date field validation failed, expected format: ' . $this->data['input_format'] . ', value is: ' . $value );

		}

		$errors = \DateTime::getLastErrors();

		if ( $errors['warning_count'] > 0 ) {
			throw new Field_Exception( '[Warning] Date field validation failed, parsed date is invalid:' . $value );
		}

		if ( $errors['error_count'] > 0 ) {
			throw new Field_Exception( '[Error] Date field validation failed, parsed date is invalid:' . $value );
		}

		$month = $datetime->format( 'm' );
		$day   = $datetime->format( 'd' );
		$year  = $datetime->format( 'Y' );

		if ( ! checkdate( $month, $day, $year ) ) {
			throw new Field_Exception( 'Date field validation failed, format is valid, invalid date. Got: ' . $value );
		}

		return $datetime;
	}

	/**
	 * @return int
	 */
	public function get_timestamp( $value ) {
		return $this->timestamp;
	}


	/**
	 * @param $value
	 *
	 * @return string
	 * @throws Field_Exception
	 */
	public function format_value( $value ) {

		/**
		 * @var \DateTime
		 */
		$datetime = $this->get_date_time_object( $value );

		if ( ! empty( $this->data['output_format'] ) ) {
			return $datetime->format( $this->data['output_format'] );
		}

		return $value;
	}

}