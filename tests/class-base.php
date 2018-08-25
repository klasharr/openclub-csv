<?php

namespace OpenClubCSV\Test;

require_once( 'sample_content/class-sailing-programme-data.php' );

class Base extends \WP_UnitTestCase {

	/**
	 * Dummy empty test till I figure out how to exclude this file.
	 * @return array
	 */
	public function testEmpty() {
		$stack = array();
		$this->assertTrue( empty( $stack ) );

		return $stack;
	}

	/**
	 * @param \OpenClubCSV\Test\Sailing_Programme_Data $test_data
	 *
	 * @return WP_Post
	 */
	protected function get_test_post_object( Sailing_Programme_Data $test_data ) {

		$post = self::factory()->post->create_and_get( array(
				'post_type'    => 'openclub-csv',
				'post_content' => $test_data->get( 'post_content' ),
			)
		);

		update_post_meta( $post->ID, 'fields', $test_data->get( 'fields' ) );

		return $post;

	}

	protected function get_valid_post( $post_content = array() ) {

		$post_content = array_replace( array(
			'post_title' => 'foo',
			'post_type'  => 'openclub-csv',
		), $post_content );
		
		$post = self::factory()->post->create_and_get( $post_content );

		/**
		 *  d    Day of the month, 2 digits with leading zeros    01 to 31
		 *  j    Day of the month without leading zeros    1 to 31
		 *  n    Numeric representation of a month, without leading zeros    1 through 12
		 *  m    Numeric representation of a month, with leading zeros    01 through 12
		 *  Y    A full numeric representation of a year, 4 digits    Examples: 1999 or 2003
		 *  y    A two digit representation of a year    Examples: 99 or 03
		 */

		$fields = <<<FIELDS
[Date]
type = date
input_format = j/n/y
output_format = d/m/Y

[Event]
type = string

[Fare]
type = string
required = true
FIELDS;

		update_post_meta( $post->ID, 'fields', $fields );
		$post->field_settings = parse_ini_string( $fields, true );

		return $post;

	}

	protected function get_default_config( array $array = array() ) {

		return array_replace(
			array(
				'post_id'                 => null,
				'error_messages'          => "yes",
				'error_lines'             => "yes",
				'future_items_only'       => null, // "yes" or "no"
				'display'                 => 'table', // default template file table.php
				'fields'                  => null, // must exist as fields in the csv header column
				'group_by_field'          => null, // must be a field in csv header column
				'context'                 => null, // string
				'limit'                   => null, // or an integer
				'filter'                  => null, // string
				'show_future_past_toggle' => null, // yes
				'display_config'          => null, // yes
			), $array
		);
	}

	protected function get_valid_test_config() {

		$post   = $this->get_valid_post();
		$config = $this->get_default_config( array( 'post_id' => $post->ID ) );

		return $config;
	}
	

	/**
	 * @return array
	 * @see /openclub-csv/inc/fields
	 */
	protected function get_field_examples() {

		return array(
			'field_a' => array(
				'type' => 'date',
				'input_format' => 'n/j/y',
			),
			'field_b' => array( 'type' => 'string' ),
			'field_c' => array( 'type' => 'int' ),
		);
	}
}