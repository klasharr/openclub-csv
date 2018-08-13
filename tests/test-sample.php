<?php
/**
 * Class SampleTest
 *
 * @package Openclub_Csv
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {

		global $post;

		$psost = $factory->post->create_and_get( array(
			'title' => 'foo',
			'post_type'    => 'openclub-csv',
		));

	}


	/**
	 * A single example test.
	 */
	function test_sample() {
		$this->assertFalse( openclub_importer_disable_wysiwyg( true ) );
	}
	

	function test_sample_b() {

		$a = array('a');

		$s = openclub_add_custom_query_var( $a );

		$this->assertEqualSets( $s, array('feo','a') );
	}
}
