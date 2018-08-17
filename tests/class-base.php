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

	protected function get_valid_post() {

		$post = self::factory()->post->create_and_get( array(
				'post_title' => 'foo',
				'post_type'  => 'openclub-csv',
			)
		);

		$fields = <<<FIELDS
[Date]
type = date

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
				'future_events_only'      => null, // "yes" or "no"
				'display'                 => 'table', // default template file table.php
				'fields'                  => null, // must exist as fields in the csv header column
				'group_by_field'          => null, // must be a field in csv header column
				'context'                 => null, // string
				'limit'                   => null, // or an integer
				'filter'                  => null, // string
				'show_future_past_toggle' => null, // yes
				'display_config'          => null, // yes
			) , $array
		);
	}

}