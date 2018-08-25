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
		$this->assertEqualSets( $vars, array( 'b', 'fio', 'a' ) );

	}


	function test_simple_shortcode() {

		$test_data = $this->get_shortcode_test_data();

		$post = self::factory()->post->create_and_get( array(
				'post_type'    => 'openclub-csv',
				'post_content' => $test_data[ 'post_content' ],
			)
		);

		update_post_meta( $post->ID, 'fields', $test_data[ 'fields' ] );
		add_shortcode( 'openclub_display_csv', 'get_openclub_display_csv_shortcode' );

		$this->assertEquals(
			do_shortcode( '[openclub_display_csv post_id=' . $post->ID . ']' ),
			$test_data[ 'html_output' ]
		);
	}


	function test_adding_robots_override_appends_disallow() {

		$output = 'foo';
		$this->assertEquals( openclub_csv_robots_override( $output ), "fooDisallow: /openclub_csv/\n" );

	}


	/**
	 * @return array
	 */
	private function get_shortcode_test_data() {

		$out = array();

		$out['post_content'] = <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/18/18,Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
CONTENT;

		$out['html_output'] = <<<ROWS
<!-- openclub_csv table.php template -->
<table class='openclub_csv'>
	<tr>
		<th>
			Day</th><th>Date</th><th>Event</th><th>Time</th><th>Team</th><th>Note</th><th>Junior	</tr>
	<tr  class=''>
	<td>Sat</td>
	<td>18/03/18</td>
	<td>Winter Fun Sailing</td>
	<td>1100</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr  class=''>
	<td>Sat</td>
	<td>24/03/18</td>
	<td>New Members Induction</td>
	<td>1100</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr  class=''>
	<td>Sun</td>
	<td>25/03/18</td>
	<td>BST STARTS - CLOCKS FORWARD 1 HOUR</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr  class=''>
	<td>Sun</td>
	<td>25/03/18</td>
	<td>Boat move and beach clean</td>
	<td>1100</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
</table>
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
		);

		$out['fields'] = <<<FIELDS
[Day]
type = string
options = Sat,Sun,Thu,Tue,Mon,Fri


[Date]
type = date
input_format = m/d/y
output_format = d/m/y

[Event]
type = string
max-length = 60
required = true

[Time]
type = string
options = 1100,1030,1830,1900,1400,1800,0830,TBA,0930

[Team]
type = string
options = A,B,C,D,E,F,G,H,J,1,2,3,4,5,6,7,8,9

[Note]
type = string
max-length = 45

[Junior]
type = int
options = 1
FIELDS;
		return $out;
	}
}