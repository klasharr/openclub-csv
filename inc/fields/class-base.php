<?php

namespace OpenClub\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once( 'class-base.php' );
require_once( 'interface-field-validator.php' );
require_once( 'exception-field-validator.php' );

abstract class Base_Field implements Field_Validator {

	/**
	 * @var $data array
	 */
	protected $data;

	/**
	 * @var $options array
	 */
	protected $options = array();

	/**
	 * @var $error_message string|bool
	 */
	private $error_message = false;

	/**
	 * @var $display_field bool
	 */
	private $display_field = true;

	/**
	 * @var bool
	 */
	private $required = false;

	public function __construct( $data ) {

		$this->data = $data;

		if ( isset( $this->data['options'] ) && ! array( $this->data['options'] ) ) {
			throw new Validator_Exception( 'options is not an array' );
		}


		if ( isset( $this->data['options'] ) ) {
			$this->options = explode( ',', $this->data['options'] );
		}

		$this->type = $data['type'];

		if ( ! empty( $this->data['required'] ) ) {
			$this->required = true;
		}

		if( isset( $this->data[ 'display' ] ) && !$this->data[ 'display' ] ){
			$this->display_field = false;
		}
	}

	protected function _validate( $value ) {

		if ( $this->isRequired() && empty( $value ) ) {
			throw new Validator_Field_Exception( 'Data error in field ' . $this->data['field_name'] . ' requires value' );
		}

		$this->stringHasValidLength( $value );
		$this->hasValidOption( $value );
	}

	private function hasValidOption( $value ) {

		if ( $this->hasOptions() && ! empty( $value ) ) {
			if ( ! $this->isValidOption( $value ) ) {
				throw new Validator_Field_Exception( 'Data error in field ' . $this->data['field_name'] . ' if present, expected one of "' . $this->getOptions( true ) . '" got "' . $value . '"' );
			}
		}
	}

	public function getType() {
		return $this->type;
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	abstract function validate( $value );

	protected function isValidOption( $value ) {

		if ( empty( $this->options ) ) {
			throw new Validator_Field_Exception( 'Data error in field ' . $this->data['field_name'] . 'is missing options.' );
		}

		return in_array( $value, $this->options );

	}

	/**
	 * For the case where a field as a number of options e.g.
	 *
	 * [options] => A,B,C,D,E,F,G,H,1,2,3,4,5,6,7,8,9
	 *
	 * @return bool
	 */
	public function hasOptions() {
		return count( $this->options ) > 0 ? true : false;
	}

	/**
	 * Return defined options as a string or array
	 *
	 * @param bool $string
	 *
	 * @return array|string
	 */
	public function getOptions( $string = false ) {

		if ( empty( $this->options ) ) {
			throw new Validator_Field_Exception( 'There are no options for defined for this field type' );
		}

		if ( $string ) {
			return implode( ',', $this->options );
		} else {
			return $this->options;
		}
	}

	/**
	 * @return string\null
	 */
	public function getMessage() {
		return $this->errorMessage;
	}

	protected function isValidInt( $value ) {

		return is_numeric( $value ) ? true : false;

	}


	public function isRequired() {
		return $this->required;
	}
	
	
	public function displayField(){
		return $this->display_field;
	}

	protected function stringHasValidLength( $value ) {
		if ( isset( $this->data['max-length'] ) && ! empty( $value ) && strlen( $value ) > $this->data['max-length'] ) {
			throw new Validator_Field_Exception( 'Data error in field ' . $this->data['field_name'] . ' value too long, a max length of  ' . $this->data['max-length'] . ' is expected. ' . strlen( $value ) . ' given. Value: "' . $value . '"' );
		}
	}
}