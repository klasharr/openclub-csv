<?php

namespace OpenClub\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
};
require_once( 'class-base.php' );
require_once( 'interface-field-validator.php' );

class DateField extends Base_Field implements Field_Validator {

	public function __construct( $data ) {
		parent::__construct( $data );
	}

	public function validate( $value ) {

		if ( empty( trim( $value ) ) ) {
			throw new Validator_Field_Exception( 'Date field validation failed, no data' );
		}

		$parts = explode( '/', $value );

		if ( ! checkdate( $parts[0], $parts[1], $parts[2] ) ) {
			throw new Validator_Field_Exception( 'Date field validation failed, format is valid, invalid date. Got: ' . $value );
		}

		if ( ! $d = \DateTime::createFromFormat( $this->data['format'], $value ) ) {
			throw new Validator_Field_Exception( 'Date field validation failed, expected format: ' . $this->data['format'] . ', value is: ' . $value );
		}

	}

}