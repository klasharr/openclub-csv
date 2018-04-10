<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Field_Manager {

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

		$have_fields_to_display = false;

		foreach ( $this->post->field_settings as $field => $config ) {

			if ( empty( $config['type'] ) ) {
				throw new \Exception( 'Field ' . $field . ' has no defined type, check fields setting.' );
			}

			$config['field_name'] = $field;

			$className              = ucwords( $config['type'] ) . 'Field';
			$this->fields[ $field ] = $o = Factory::get_field( $className, $config, $this->input );

			if($o->is_displayed()){
				$have_fields_to_display = true;
			}
		}

		if(!$have_fields_to_display ) {
			if( !class_exists( 'WP_CLI' ) ) {
				throw new \Exception( 'All fields have been set to not display, check your shortcode and also the fields meta value.' );
			} else {
				openclub_csv_log_cli( 'All fields have been set to not display.' );
			}
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

		if( !$this->has_validators() ) {
			throw new \Exception( 'The Validators have not been set.' );
		}

        if(!isset( $this->fields[ $key ] ) ) {
	        throw new \Exception( 'Validator '. $key . ' does not exist, check the column name.' );
        }

		return $this->fields[ $key ];
	}

	public function get_validator_type( $key ) {

		if( !$this->has_validators() ) {
			throw new \Exception( 'The Validators have not been set.' );
		}

		if(!isset( $this->fields[ $key ] ) ) {
			throw new \Exception( 'Validator '. $key . ' does not exist, check the column name.' );
		}

		return $this->fields[ $key ]->getType();
	}

	public function get_all_registered_fields(){
		$out = array();
		foreach( $this->fields as $fieldName => $field ){
			$out[] = $fieldName;
		}
		return $out;
	}


	public function get_display_fields() {

		$out = array();

		$overridden_display_fields = $this->input->get_overridden_display_fields();

		foreach( $this->fields as $fieldName => $field ){
			/** @var $field Field_Validator */
			if( !empty( $overridden_display_fields) ) {
				if(in_array( $fieldName, $overridden_display_fields ) ){
					$out[] = $fieldName;
				}
			} elseif ( $this->input->has_reset_display_fields() ) {
				$out[] = $fieldName;
			} elseif( $field->is_displayed() ) {
				$out[] = $fieldName;
			}
		}

		return $out;
	}



}