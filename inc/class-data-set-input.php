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
	 * @var int
	 */
	private $future_items_only;

	/**
	 * @var string
	 */
	private $error_lines = false;

	/**
	 * @var string
	 */
	private $error_messages = false;

	/**
	 * @var string
	 */
	private $display;

	/**
	 * @var boolean
	 */
	private $init_called;

	public function __construct( $config = false) {

		if( !empty( $config ) && !is_array( $config ) ) {
			throw new \Exception( 'If set, $config must be an array.' );
		}

		if( !empty( $config ) ) {
			$this->init( $config );
		}

	}

	/**
	 * @param $post_id int
	 *
	 * @throws \Exception
	 */
	public function init( $config ) {

		if( $this->get_init_has_been_called() ) {
			throw new \Exception( 'init has been called already' );
		}

		$this->post = $this->get_post_object( $config );
		$this->post_id = $this->post->ID;

		if( ! empty( $config['error_messages'] )) {
			$this->set_error_messages( $config[ 'error_messages'] );
		}

		if( ! empty( $config['error_lines'] )) {
			$this->set_error_lines( $config[ 'error_lines'] );
		}

		if( !empty( $config['future_items_only'] ) ) {
			$this->set_future_items_value( $config['future_items_only'] );
		}

		$this->set_display( $config[ 'display' ] );

		if ( ! empty( $config['group_by_field'] ) ) {
			$this->set_group_by_field( $config['group_by_field'] );
		}

		if ( ! empty( $config['context'] ) ) {
			$this->set_context( $config['context'] );
		}

		if ( ! empty( $config['limit'] ) ) {
			$this->set_limit( $config['limit'] );
		}

		if ( ! empty( $config['fields'] ) ) {
			$this->set_fields_override( $config['fields'] );
		}

		if( ! empty( $config['filter'] )) {
			$this->set_filter( $config[ 'filter'] );
		}

		if( ! empty( $config['show_future_past_toggle'] )) {
			$this->set_show_future_past_toggle( $config['show_future_past_toggle'] );
		}

		if( ! empty( $config['display_config'] )) {
			$this->set_display_configuration_settings( $config['display_config'] );
		}

		$this->init_called = true;

	}

	public function get_init_has_been_called(){
		return $this->init_called;
	}


	private function set_display( $display ) {

		if( empty( $display ) ) {
			throw  new \Exception( 'display cannot be empty ' );
		}
		$this->display = $display;
	}

	private function get_display() {
		return $this->display;
	}

	private function set_show_future_past_toggle( $show_future_past_toggle ) {
		$this->show_future_past_toggle = $this->get_boolean_from_config_value( $show_future_past_toggle );
	}

	private function get_show_future_past_toggle() {
		return $this->show_future_past_toggle;
	}

	private function set_display_configuration_settings( $display_config ) {
		$this->display_config = $this->get_boolean_from_config_value( $display_config );
	}

	private function get_display_configuration_settings() {
		return $this->display_config;
	}


	private function set_error_messages( $error_messages ) {

		if( !empty( $error_messages ) && !in_array( $error_messages, array( 'yes', 'no' ) ) ) {
			throw new \Exception( 'error_messages must be yes, no or not set. The default is yes.' );
		}

		$this->error_messages = $this->get_boolean_from_config_value( $error_messages );
	}

	public function get_error_messages() {
		return $this->error_messages;
	}

	private function set_error_lines( $error_lines ) {

		if( !empty( $error_lines ) && !in_array( $error_lines, array( 'yes', 'no' ) ) ) {
			throw new \Exception( 'error_lines must be yes, no or not set. The default is yes.' );
		}

		$this->error_lines = $this->get_boolean_from_config_value( $error_lines );
	}

	public function get_error_lines() {
		return $this->error_lines;
	}

	private function get_post_object( array $config ) {

		if ( empty( $config['post_id'] ) ) {
			throw new \Exception( '$post_id was not passed' );
		}

		if ( ! is_numeric( $config['post_id'] ) ) {
			throw new \Exception( '$post_id is not numeric' );
		}

		return CSV_Util::get_csv_post( $config['post_id'] );

	}

	private function set_future_items_value( $value ) {
		
		if ( ! empty( $value ) && ! in_array( $value, array( "yes", "no", 1, 2 ) ) ) {
			throw new \Exception( '$config[\'future_items_only\'] can be "yes", "no", 1, 2 or must not be set.' );
		}


		if ( ! empty( $value )  ) {

			switch( $value ) {
				case 2:
				case 'yes':
					$this->future_items_only = true;
					return;
				case 1:
				case 'no':
					$this->future_items_only = false;
					return;
			}
		}
		$this->future_items_only = false;
	}

	public function get_future_items_value(){

		return $this->future_items_only;
	}

	private function get_boolean_from_config_value( $value ) {

		if( empty( $value ) ) {
			return false;
		}

		if( !in_array( $value, array( 'yes', 'no' ) ) ) {
			throw new \Exception( 'get_boolean_from_config_value() called with ' .$value . ', expected yes or no' );
		}

		return $value === 'yes' ? true: false;

	}


	/**
	 * @param Filter $filter
	 */
	public function set_filter_object( Filter $filter ) {

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


	public function set_filter( $filter ){

		if ( ! empty( $filter ) ) {

			// This needs to be here rather than in the factory as this is simplest for plugins
			// extending this one.
			$class = "\OpenClub\\" . $filter;
			if ( ! class_exists( $class ) ) {
				throw new \Exception( 'Filter class ' . $class . ' does not exist, check the value passed in $config[ \'filter\' ]' );
			}

			$this->set_filter_object( new $class() );
		}

	}



	/**
	 * @return int
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	private function set_group_by_field( $group_by_field ) {

		if ( empty( $group_by_field ) ) {
			throw new \Exception( 'set_group_by_field() called with an empty value' );

			return;
		}

		if ( empty( trim( $group_by_field ) ) ) {
			throw new \Exception( 'set_group_by_field called with invalid value, likely an empty space' );
		}

		if( !array_key_exists( $group_by_field, $this->post->field_settings ) ) {
			throw new \Exception( 'set_group_by_field called with invalid field.' );
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
			if ( ! array_key_exists( $field_name, $this->post->field_settings ) ) {
				throw new \Exception( 'Field override error: field ' . $field_name . ' does not exist. Check the config.' );
			}
		}
		$this->overridden_fields = $overridden_fields;
	}

	/**
	 * @return array
	 */
	public function get_fields_override() {

		if( empty( $this->overridden_fields ) ) {
			return array();
		}

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
	private function set_context( $context ) {
		$this->context = $context;
	}

	/**
	 * @return int
	 */
	public function get_limit() {
		return $this->limit;
	}

	/**
	 * @param mixed int|null $limit
	 */
	public function set_limit( $limit ) {

		if ( $limit === null ) {
			return;
		}

		$limit = $limit;

		if ( (int) $limit <= 0 ) {
			throw new \Exception( '$limit must be an integer and greater than zero.' );
		}

		$this->limit = $limit;
	}

	/**
	 * @return bool
	 */
	public function is_show_future_items_only() {

		return $this->config[ 'future_items_only' ];
	}


	public function get_config( $key = null ) {

		$config = array(
			'post' => $this->get_post(),
			'post_id' => $this->get_post_id(),
			'filter' => $this->get_filter(),
			'group_by_field' => $this->get_group_by_field(),
			'overridden_fields' => $this->get_fields_override(),
			'context' => $this->get_context(),
			'limit' => $this->get_limit(),
			'future_items_only' => $this->get_future_items_value(),
			'error_messages' => $this->get_error_messages(),
			'error_lines' => $this->error_lines(),
 		);

		if( !empty( $key ) ) {
			return $config[ $key ];
		}

		return $config;

	}
}