<?php
/**
 * Class OpenClubCSVTest
 *
 * @package Openclub_Csv
 */

/**
 * Sample test case.
 */
class OpenClubCSVTest extends WP_UnitTestCase {


	function test_assert_openclub_csv_post_type_sets_wysiwyg_to_false() {

		global $post;

		$post = self::factory()->post->create_and_get( array(
				'post_title'     => 'foo',
				'post_type' => 'openclub-csv',
			)
		);

		$this->assertFalse( openclub_importer_disable_wysiwyg( true ) );

		unset( $post);
	}

	function test_adding_custom_variables_to_wordpress_get_vars_works() {

		$default = array( 'a', 'b', );
		$vars    = openclub_add_custom_query_var( $default );
		$this->assertEqualSets( $vars, array( 'b', 'feo', 'a' ) );

	}


	/**
	 * Just a POC
	 */
	function test_simple_shortcode(){


		add_shortcode( 'openclub_display_csv', 'get_openclub_display_csv_shortcode' );

		$post_id = self::set_sample_CSV_post();
		$this->assertEquals( do_shortcode( '[openclub_display_csv post_id='.$post_id.']'), self::get_simple_shortcode_output());
	}

	/**
	 * Just a POC
	 * 
	 * @return int
	 */
	function set_sample_CSV_post() {

		$content =  <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/18/18,Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
CONTENT;

		$fields = <<<FIELDS
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

		$post = self::factory()->post->create_and_get( array(
				'post_title'     => 'Sailing Programme',
				'post_type' => 'openclub-csv',
				'post_content' => $content,
			)
		);

		update_post_meta( $post->ID, 'fields', $fields );

		return $post->ID;

	}

	/**
	 * Just a POC
	 *
	 * @return string
	 */
	function get_simple_shortcode_output(){

		return <<<SHORTCODE
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
SHORTCODE;
	}

}



