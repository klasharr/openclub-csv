<?php

namespace OpenClub;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );

class Data_Set_Input {

	/**
	 * @var $post \WP_Post
	 */
	private $post;

	/**
	 * @var
	 */
	private $filter;

	/**
	 * @var $post_id int
	 */
	private $post_id;

	/**
	 * @var string
	 */
	private $group_by_field;

	/**
	 * @var string
	 */
	private $overridden_fields;

	/**
	 * @var mixed
	 */
	private $context;

	/**
	 * @var int
	 */
	private $limit;


	/**
	 * @var bool
	 */
	private $future_events_only;

	/**
	 * @param $post_id int
	 *
	 * @throws \Exception
	 */
	public function __construct( $config ) {

		if( empty( $config['post_id'] ) ) {
			throw new \Exception( '$post_id was not passed' );
		}

		if ( ! is_numeric( $config['post_id'] ) ) {
			throw new \Exception( '$post_id is not numeric' );
		}

		$this->post_id            = $config['post_id'];
		$this->post               = CSV_Util::get_csv_post( $this->post_id );
		$this->raw_display_fields = $this->post->field_settings;

		if ( ! empty( $config['group_by_field'] ) ) {
			$this->set_group_by_field( $config['group_by_field'] );
		}
		if ( ! empty( $config['fields'] ) ) {
			$this->set_fields_override( $config['fields'] );
		}

		if ( ! empty( $config['context'] ) ) {
			$this->set_context( $config['context'] );
		}

		if ( ! empty( $config['limit'] ) ) {
			$this->set_limit( $config['limit'] );
		}

		if ( ! empty( $config['future_events_only'] ) ) {
			$this->set_future_events_only( $config['future_events_only'] );
		}

		if ( ! empty( $config['filter'] ) ) {

			$class = "\OpenClub\\" . $config['filter'];
			if ( ! class_exists( $class ) ) {
				throw new \Exception( $class . ' does not exist, check the value passed in $config[ \'filter\' ]' );
			}

			$this->set_filter( new $class() );
		}
	}

	/**
	 * @param Filter $filter
	 */
	public function set_filter( Filter $filter ) {

		$this->filter = $filter;
	}

	/**
	 * @return \WP_Post
	 */
	public function get_post() {

		return $this->post;
	}

	public function get_filter() {

		if ( empty( $this->filter ) ) {
			return Factory::get_null_filter();
		}

		return $this->filter;
	}

	/**
	 * @return int
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	public function set_group_by_field( $group_by_field ) {

		if ( empty( $group_by_field ) ) {
			openclub_csv_log_cli( 'set_group_by_field() called with an empty value' );

			return;
		}

		if ( empty( trim( $group_by_field ) ) ) {
			throw new \Exception( 'set_group_by_field called with invalid value, likely an empty space' );
		}

		$this->group_by_field = $group_by_field;
	}

	public function get_group_by_field() {

		return $this->group_by_field;
	}


	public function has_group_by_field() {
		return $this->group_by_field ? true : false;
	}

	public function set_fields_override( $overridden_fields ) {

		if ( empty( $overridden_fields ) ) {
			return false;
		}

		$fields = explode( ',', $overridden_fields );

		foreach ( $fields as $field_name ) {
			if ( ! array_key_exists( $field_name, $this->raw_display_fields ) ) {
				throw new \Exception( 'Field override: ' . $field_name . ' is invalid. Check the shortcode' );
			}
		}
		$this->overridden_fields = $overridden_fields;
	}

	/**
	 * @return array
	 */
	public function get_overridden_fields() {
		return explode( ',', $this->overridden_fields );
	}

	public function has_overridden_fields() {
		return ! empty( $this->overridden_fields );
	}

	/**
	 * @return mixed
	 */
	public function get_context() {
		return $this->context;
	}

	/**
	 * @param mixed $context
	 */
	public function set_context( $context ) {
		$this->context = $context;
	}

	/**
	 * @return int
	 */
	public function get_limit() {
		return $this->limit;
	}

	/**
	 * @param mixed int|bool $limit
	 */
	public function set_limit( $limit ) {

		if ( $limit === false ) {
			return;
		}

		if ( (int) $limit != $limit ) {
			throw new \Exception( '$limit must be passed as an integer' );
		}
		if ( $limit <= 0 ) {
			throw new \Exception( '$limit must be greater than zero' );
		}

		$this->limit = (int) $limit;
	}

	/**
	 * @return bool
	 */
	public function is_show_future_events_only() {

		if ( 'yes' === $this->future_events_only ) {
			return true;
		}
	}

	/**
	 * @param $future_events_only int
	 *
	 * @throws \Exception
	 */
	public function set_future_events_only( $future_events_only ) {

		if ( ! empty( $future_events_only ) && "yes" !== $future_events_only ) {
			throw new \Exception( '$future_events_only can be "yes" or must not be set' );
		}
		$this->future_events_only = $future_events_only;
	}
}