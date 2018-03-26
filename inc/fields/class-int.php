<?php

namespace OpenClub\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once( 'class-base.php' );
require_once( 'interface-field-validator.php' );

class IntField extends Base_Field implements Field_Validator {

	public function __construct( $data ) {
		parent::__construct( $data );
	}

	public function validate( $value ) {
		parent::_validate( $value );

		return $this->is_valid_int( $value );
	}

	private function is_valid_int( $value ) {

		return is_numeric( $value ) ? true : false;
	}

}