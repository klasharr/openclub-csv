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

	protected function get_default_config() {

		return array(
			'post_id'                 => null,
			'error_messages'          => "yes",
			'error_lines'             => "yes",
			'future_events_only'      => null,
			'display'                 => 'table', // default template file table.php
			'fields'                  => null,
			'group_by_field'          => null,
			'context'                 => null,
			'limit'                   => false,
			'filter'                  => null,
			'show_future_past_toggle' => null,
			'display_config'          => false,
		);
	}

}