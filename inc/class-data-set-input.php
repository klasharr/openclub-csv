<?php

namespace OpenClub;

require_once( 'class-csv-util.php' );

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
	 * @var int
	 */
	private $limit;


	/**
	 * @var bool
	 */
	private $reset_display_fields = false;


	/**
	 * @var array
	 */
	private $display_fields_overridden = array();

	/**
	 * @param $post_id int
	 *
	 * @throws \Exception
	 */
	public function __construct( $post_id ){

		if( !is_numeric( $post_id ) ) {
			throw new \Exception( '$post_id is not numeric' );
		}

		$this->post_id = $post_id;
		$this->post = CSV_Util::get_csv_post( $post_id );
	}

	/**
	 * @param Filter $filter
	 */
	public function set_filter( Filter $filter ){

		$this->filter = $filter;
	}

	/**
	 * @return \WP_Post
	 */
	public function get_post(){
		
		return $this->post;
	}

	/**
	 * @return Filter|Null_Filter
	 */
	public function get_filter(){

		if( empty( $this->filter ) ){
			return Factory::get_null_filter();
		}
		
		return $this->filter;
	}

	/**
	 * @return int
	 */
	public function get_post_id(){
		return $this->post_id;
	}


	public function reset_field_display_rules(){
		$this->reset_display_fields = true;
	}

	public function has_reset_display_fields(){
		return $this->reset_display_fields;
	}

	public function set_group_by_field( $group_by_field ){

		if( empty( trim( $group_by_field ) ) ) {
			throw new \Exception('$group_by_field cannot be empty' );
		}

		$this->group_by_field = $group_by_field;
	}

	public function get_group_by_field(){

		return $this->group_by_field;
	}


	public function has_group_by_field(){
		return $this->group_by_field ? true: false;
	}

	public function has_limit(){
		return $this->limit ? true : false;
	}

	/**
	 * @param $limit
	 */
	public function set_limit( $limit ){

		if( !is_numeric($limit ) || (int) $limit != $limit || $limit <= 0 ) {
			throw new \Exception( '$limit must be passed a positive integer.');
		}
		$this->limit = (int) $limit;
	}

	public function get_limit(){
		return $this->limit;
	}

	public function override_display_fields( $fields ){

		if(!is_array($fields)){
			throw new \Exception( '$fields must me passed as an array' );
		}
		$this->display_fields_overridden = $fields;
	}

	public function get_overridden_display_fields() {
		return !empty( $this->display_fields_overridden ) ? $this->display_fields_overridden : false;
	}
}