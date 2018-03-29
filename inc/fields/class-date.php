<?php

namespace OpenClub\Fields;

use OpenClub\Data_Set_Input;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
};
require_once( 'class-base.php' );
require_once( 'interface-field-validator.php' );

class DateField extends Base_Field implements Field_Validator {

	public function __construct( $data, Data_Set_Input $input ) {
		parent::__construct( $data, $input );
	}

	public function validate( $value ) {

		if ( empty( trim( $value ) ) ) {
			throw new Validator_Field_Exception( 'Date field validation failed, no data' );
		}

		/** 
		 * @var $d DateTime 
		 */
		if ( ! $d = \DateTime::createFromFormat( $this->data['format'], $value ) ) {
			throw new Validator_Field_Exception( 'Date field validation failed, expected format: ' . $this->data['format'] . ', value is: ' . $value );

		}

		$errors = \DateTime::getLastErrors();

		if($errors['warning_count'] > 0 ){
			throw new Validator_Field_Exception( '[Warning] Date field validation failed, parsed date is invalid:' . $value );
		}

		if($errors['error_count'] > 0 ) {
			throw new Validator_Field_Exception( '[Error] Date field validation failed, parsed date is invalid:' . $value );
		}

		$month = $d->format('m');
		$day = $d->format('d');
		$year = $d->format('Y');

		if ( ! checkdate( $month, $day, $year ) ) {
			throw new Validator_Field_Exception( 'Date field validation failed, format is valid, invalid date. Got: ' . $value );
		}

	}

}