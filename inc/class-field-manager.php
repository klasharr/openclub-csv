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
	 * @var array of field objects
	 */
	private $fields = array();

	/**
	 * @var Data_Set_Input
	 */
	private $input;

	/**
	 * @var bool
	 */
	private $have_fields_to_display = false;

	/**
	 * @param \WP_Post $post
	 */
	public function __construct() {}

	public function init(  Data_Set_Input $input  ) {

		$this->post  = $input->get_post();
		$this->input = $input;

		if ( empty( $this->post->field_settings ) ) {
			return;
		}

		$this->populate_field_objects_array( $this->post->field_settings );

		if ( ! $this->have_fields_to_display ) {
			if ( ! class_exists( 'WP_CLI' ) ) {
				throw new \Exception( 'All fields have been set to not display, check your shortcode and also the fields meta value.' );
			} else {
				openclub_csv_log_cli( 'All fields have been set to not display.' );
			}
		}
	}


	/**
	 * @param $field_name
	 * @param $config
	 *
	 * @throws \Exception
	 */
	public function field_display_is_true( $config ) {

		if ( isset( $config['display'] ) && ! $config['display'] ) {
			return false;
		}

		return true;

	}

	private function populate_field_objects_array( array $field_settings ) {

		foreach ( $field_settings as $field_name => $config ) {

			$this->validate_single_field_setting( $field_name, $config );

			$config['field_name'] = $field_name;

			if ( $this->field_display_is_true( $config ) ) {
				$this->have_fields_to_display =    true;
				$config['display_field']      = true;
			} else {
				$config['display_field'] = false;
			}

			$className = ucwords( $config['type'] ) . 'Field';

			$this->fields[ $field_name ] = Factory::get_field( $className, $config, $this->input );

		}
	}

	public function validate_single_field_setting( $field_name, array $config ) {

		if( empty( trim( $field_name ) ) ) {
			throw new \Exception( 'You must set a field name.' );
		}

		if ( empty( $config['type'] ) ) {
			throw new \Exception( 'Field ' . $field_name . ' has no defined type, check fields setting.' );
		}

		if ( $field_name !== esc_html( $field_name ) ) {
			throw new \Exception( 'Field ' . esc_html( $field_name ) . ' has invalid characters.' );
		}

		$file = OPENCLUB_CSV_PLUGIN_DIR . 'inc/fields/class-' . strtolower( $config['type'] ) . '.php';

		if ( ! file_exists( $file ) ) {
			throw new \Exception( $file . ' does not exist. Check the type setting in fields.' );
		}
		return true;
	}


	public function has_fields() {
		return ! empty( $this->fields ) ? true : false;
	}


	public function get_field( $key, $simple_existence_check = false ) {

		if ( ! is_string( $key ) ) {
			throw new \Exception( '$key must be passed as a string' );
		}

		if ( ! $this->has_fields() ) {
			throw new \Exception( 'The fields have not been set.' );
		}

		if ( isset( $this->fields[ $key ] ) ) {
			return $this->fields[ $key ];
		}

		if ( $simple_existence_check ) {
			return false;
		}

		if ( ! isset( $this->fields[ $key ] ) ) {
			throw new \Exception( 'Field ' . $key . ' does not exist, check the column name.' );
		}

	}

	public function get_field_type( $key ) {

		if ( ! $this->has_fields() ) {
			throw new \Exception( 'The fields have not been set.' );
		}
		if ( ! isset( $this->fields[ $key ] ) ) {
			throw new \Exception( 'Field ' . $key . ' does not exist, check the column name.' );
		}
		return $this->fields[ $key ]->getType();
	}

	public function get_all_registered_fields() {

		return array_keys( $this->fields );
	}


	public function get_display_field_names() {

		$out = array();

		foreach ( $this->fields as $fieldName => $field ) {
			if ( $field->is_displayed() ) {
				$out[] = $fieldName;
			}
		}
		return $out;
	}
}