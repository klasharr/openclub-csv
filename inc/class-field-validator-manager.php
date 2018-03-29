<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Field_Validator_Manager {

	/**
	 * @var $post WP_Post
	 */
	private $post;

	/**
	 * @var array
	 */
	private $fields = array();

	/**
	 * @var Data_Set_Input
	 */
	private $input;

	/**
	 * @param \WP_Post $post
	 */
	public function __construct( Data_Set_Input $input ) {

		$this->post = $input->get_post();
		$this->input = $input;

		if ( empty( $this->post->field_settings ) ) {
			return;
		}

		foreach ( $this->post->field_settings as $field => $config ) {

			if ( empty( $config['type'] ) ) {
				throw new \Exception( 'Field ' . $field . ' has no defined type, check fields setting.' );
			}

			$config['field_name'] = $field;

			$className              = ucwords( $config['type'] ) . 'Field';
			$this->fields[ $field ] = Factory::get_field( $className, $config, $this->input );
		}


	}

	public function is_valid_field( $field ){

		if( array_key_exists( $field, $this->fields)) {
			return true;
		}
	}

	public function has_validators() {
		return ! empty( $this->fields ) ? true : false;
	}

	public function get_validator( $key ) {

        if(!isset( $this->fields[ $key ] ) ) {
	        throw new \Exception( 'Validator '. $key . ' does not exist, check the column name.' );
        }

		return $this->fields[ $key ];
	}

	public function getDisplayFields() {

		$out = array();
		foreach( $this->fields as $fieldName => $validator ){

			/** @var $validator Field_Validator */
			if( $this->input->has_reset_display_fields() ){
				$out[] = $fieldName;
			} elseif( $validator->displayField() ) {
				$out[] = $fieldName;
			}
		}
		return $out;
	}


}