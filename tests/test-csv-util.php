<?php

namespace OpenClubCSV\Test;

/**
 * Class CSVUtilTest
 *
 * https://github.com/rnagle/wordpress-unit-tests/blob/master/tests/shortcode.php
 *
 * https://wordpress.stackexchange.com/questions/164121/testing-hooks-callback
 *
 * @package Openclub_Csv
 */

require_once( 'class-base.php' );

class CSV_Util_Test extends Base {

	public static function wpSetUpBeforeClass( \WP_UnitTest_Factory $factory ) {
	}

	function test_assert_get_csv_post_with_non_int_id_will_throw_exception() {

		$post_id = 'n';
		$this->setExpectedException( 'Exception', sprintf( '$post_id %s must be a numeric value.', $post_id ) );
		\OpenClub\CSV_Util::get_csv_post( $post_id );

	}

	function test_assert_get_csv_post_with_non_existing_id_will_throw_exception() {

		$post_id = 999999999;
		$this->setExpectedException( 'Exception', sprintf( '$post_id %d does not return a post object.', $post_id ) );
		\OpenClub\CSV_Util::get_csv_post( $post_id );

	}

	function test_assert_get_csv_post_with_id_that_isnt_openclub_csv_type_will_throw_exception() {

		$post = self::factory()->post->create_and_get( array(
				'post_title' => 'foo',
				'post_type'  => 'post',
			)
		);

		$this->setExpectedException( 'Exception', sprintf( '$post_id %d does not return a post object of post type openclub-csv.', $post->ID ) );
		\OpenClub\CSV_Util::get_csv_post( $post->ID );
	}


	function test_assert_get_csv_post_with_id_that_is_autodraft_will_throw_exception() {

		$post = self::factory()->post->create_and_get( array(
				'post_title'  => 'foo',
				'post_type'   => 'openclub-csv',
				'post_status' => 'auto-draft'
			)
		);

		$this->setExpectedException( 'Exception', sprintf( '$post_id %d returns an openclub-csv post type auto-draft.', $post->ID ) );
		\OpenClub\CSV_Util::get_csv_post( $post->ID );
	}

	function test_assert_get_csv_post_with_id_that_has_no_fields_throw_exception() {

		$post = self::factory()->post->create_and_get( array(
				'post_title' => 'foo',
				'post_type'  => 'openclub-csv',
			)
		);

		$this->setExpectedException( 'Exception', sprintf( '$post_id %d does not have a fields post meta set.', $post->ID ) );
		\OpenClub\CSV_Util::get_csv_post( $post->ID );
	}


	function test_assert_get_csv_post_with_invalid_fields_throws_exception() {

		$post = self::factory()->post->create_and_get( array(
				'post_title' => 'foo',
				'post_type'  => 'openclub-csv',
			)
		);

		update_post_meta( $post->ID, 'fields', 'foo' );
		$this->setExpectedException( 'Exception', sprintf( '$post_id %d has ini fields but they do not parse', $post->ID ) );
		\OpenClub\CSV_Util::get_csv_post( $post->ID );
	}

	function test_assert_get_csv_post_with_valid_id_and_valid_fields_returns_csv_post() {

		$post = self::factory()->post->create_and_get( array(
				'post_title' => 'foo',
				'post_type'  => 'openclub-csv',
			)
		);

		$field_values = $this->get_fields();

		update_post_meta( $post->ID, 'fields', $field_values );
		$post->field_settings = parse_ini_string( $field_values, true );
		$this->assertEquals( \OpenClub\CSV_Util::get_csv_post( $post->ID ), $post );

	}


	private function get_fields() {

		return <<<FIELDS
[Date]
type = string

[Event]
type = string

[Fare]
type = string
required = true
FIELDS;
	}

}
