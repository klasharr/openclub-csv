<?php

namespace OpenClubCSV\Test;

require_once( 'class-base.php' );

class OpenClubCSVTest extends Base {


	function test_assert_openclub_csv_post_type_sets_wysiwyg_to_false() {

		global $post;

		$post = self::factory()->post->create_and_get( array(
				'post_title' => 'foo',
				'post_type'  => 'openclub-csv',
			)
		);

		$this->assertFalse( openclub_importer_disable_wysiwyg( true ) );

		unset( $post );
	}

	function test_adding_custom_variables_to_wordpress_get_vars_works() {

		$default = array( 'a', 'b', );
		$vars    = openclub_add_custom_query_var( $default );
		$this->assertEqualSets( $vars, array( 'b', 'feo', 'a' ) );

	}


	function test_simple_shortcode() {

		add_shortcode( 'openclub_display_csv', 'get_openclub_display_csv_shortcode' );

		$test_data = new Sailing_Programme_Data( 'valid_shortcode_table_template' );
		$post      = $this->get_test_post_object( $test_data );

		$this->assertEquals( do_shortcode( '[openclub_display_csv post_id=' . $post->ID . ']' ), $test_data->get( 'html_output' ) );
	}


	function test_adding_robots_override_appends_disallow() {

		$output = 'foo';
		$this->assertEquals( openclub_csv_robots_override( $output ), "fooDisallow: /openclub_csv/\n" );

	}


}