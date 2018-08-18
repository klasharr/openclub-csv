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
	 * @var array
	 */
	private $config;

	/**
	 * @param $post_id int
	 *
	 * @throws \Exception
	 */
	public function __construct( $config ) {

		if ( empty( $config['post_id'] ) ) {
			throw new \Exception( '$post_id was not passed' );
		}

		if ( ! is_numeric( $config['post_id'] ) ) {
			throw new \Exception( '$post_id is not numeric' );
		}

		$this->post_id            = $config['post_id'];
		$this->post               = CSV_Util::get_csv_post( $this->post_id );
		//$this->raw_display_fields = $this->post->field_settings;

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


		if( !empty( $config['error_lines'] ) && !in_array($config['error_lines'], array( 'yes', 'no', 1, 0 ) ) ) {
			throw new \Exception( 'error_lines must be yes, no, 1, 0 or not set. The default is yes.' );
		}

		if( !empty( $config['error_messages'] ) && !in_array($config['error_messages'], array( 'yes', 'no', 1, 0 ) ) ) {

			throw new \Exception( 'error_messages must be yes, no, 1 or 0 or not set. The default is yes.' );
		}

		// @todo tidy up
		if( in_array( $config['error_messages'], array( 'yes', 1 ) ) ) {
			$config['error_messages'] = true;
		} elseif( in_array( $config['error_messages'], array( 'no', 0 ) )) {
			$config['error_messages'] = false;
		}

		if( in_array( $config['error_lines'], array( 'yes', 1 ) ) ) {
			$config['error_lines'] = true;
		} elseif( in_array( $config['error_lines'], array( 'no', 0 ) )) {
			$config['error_lines'] = false;
		}

		if( in_array( $config['display_config'], array( 'yes', 1 ) ) ) {
			$config['display_config'] = true;
		} elseif( in_array( $config['display_config'], array( 'no', 0 ) )) {
			$config['display_config'] = false;
		}

		if ( ! empty( $config['future_items_only'] ) && ! in_array( $config['future_items_only'], array( "yes", "no", 1, 2 ) ) ) {
			throw new \Exception( '$config[\'future_items_only\'] can be "yes", "no", 1, 2 or must not be set.' );
		}

		// @todo tidy up
		if ( ! empty( $config['future_items_only'] ) ) {
			switch( $config['future_items_only'] ) {
				case 2:
				case 'yes':
					$config['future_items_only'] = true;
					break;
				case 1:
				case 'no':
					$config['future_items_only'] = false;
					break;
			}
		} else {
			$config['future_items_only'] = false;
		}

		if ( ! empty( $config['filter'] ) ) {

			// This needs to be here rather than in the factory as this is simplest for plugins
			// extending this one.
			$class = "\OpenClub\\" . $config['filter'];
			if ( ! class_exists( $class ) ) {
				throw new \Exception( 'Filter class ' . $class . ' does not exist, check the value passed in $config[ \'filter\' ]' );
			}

			$this->set_filter( new $class() );
		}

		$this->config = $config;

	}


	public function get_config( $key = null ) {

		if( !empty( $key ) ) {
			return $this->config[ $key ];
		}

		return $this->config;
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
	public function get_overridden_fields() {

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
	 * @param mixed int|null $limit
	 */
	public function set_limit( $limit ) {

		if ( $limit === null ) {
			return;
		}

		if ( $limit <= 0 ) {
			throw new \Exception( '$limit must be an integer and greater than zero.' );
		}

		$this->limit = (int) $limit;
	}

	/**
	 * @return bool
	 */
	public function is_show_future_items_only() {

		return $this->config[ 'future_items_only' ];
	}


	public function get_set_config() {

		return array(
			'post' => $this->get_post(),
			'post_id' => $this->get_post_id(),
			'filter' => $this->get_filter(),
			'group_by_field' => $this->get_group_by_field(),
			'overridden_fields' => $this->get_overridden_fields(),
			'context' => $this->get_context(),
			'limit' => $this->get_limit(),
			'future_items_only' => $this->config[ 'future_items_only' ],
			'error_messages' => $this->config[ 'error_messages' ],
			'error_lines' => $this->config[ 'error_lines' ],
 		);

	}
}