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
	 * @param \WP_Post $post
	 */
	public function __construct( \WP_Post $post ) {

		$this->post = $post;

		if ( empty( $this->post->field_settings ) ) {
			return;
		}

		foreach ( $this->post->field_settings as $field => $config ) {

			if ( empty( $config['type'] ) ) {
				throw new Exception( 'Field has no type' );
			}

			$config['field_name'] = $field;

			$className              = ucwords( $config['type'] ) . 'Field';
			$this->fields[ $field ] = Factory::get_field( $className, $config );
		}


	}

	public function has_validators() {
		return ! empty( $this->fields ) ? true : false;
	}

	public function get_validator( $key ) {

		return isset( $this->fields[ $key ] ) ? $this->fields[ $key ] : false;

	}


}