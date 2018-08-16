<?php

namespace OpenClubCSV\Test;

require_once( 'class-base.php' );

class CSVSecurityTest extends Base {

	public static function wpSetUpBeforeClass( \WP_UnitTest_Factory $factory ) {
	}


	/**
	 * @see OpenClubCSV\TestData\Sailing_Programme::test_e_data();
	 */
	function test_content_with_xss_will_be_secure() {

		$test_data = new Sailing_Programme_Data( 'xss_injection', $this->get_test_data_with_xss_injection() );
		$post      = $this->get_test_post_object( $test_data );

		$config            = $test_data->get( 'config' );
		$config['post_id'] = $post->ID;

		$s = \OpenClub\CSV_Display::get_html(
			\OpenClub\CSV_Display::get_config( $config )
		);

		$this->assertSame( sprintf(
			$test_data->get( 'html_output' ), $post->ID ),
			trim( $s ) );

	}

	function get_test_data_with_xss_injection() {
		
		$out = array();

		$out['post_content'] = "Description\n<script>alert('foo');</script>";
		$out['html_output'] = "Description\nalert(&#039;foo&#039;);";
		$out['config'] = array( 'display' => 'csv_rows' );
		$out['fields'] = "[Description]\ntype = string";

		return $out;
	}

}