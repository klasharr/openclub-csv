<?php

namespace OpenClubCSV\Test;

require_once( 'class-base-dummy-data.php' );

class Sailing_Programme_Data extends Base_Dummy_Data {

	function set_input_and_output_samples() {

		$this->input_and_output_samples = array(
			'invalid_string_option' => $this->test_data_invalid_string_option(),
			'validation_failure'    => $this->test_data_validation_failure(),
			'valid_content'         => $this->test_data_valid_content(),
			'invalid_heading_column'         => $this->test_data_invalid_heading_column(),
			'csv_row_column_mismatch' => $this->test_data_csv_row_column_mismatch(),
			'valid_shortcode_table_template' => $this->test_f_data(),
			'invalid_int'           => $this->test_data_invalid_integer(),
			'valid_date_conversion'   => $this->test_data_date_format_conversion_valid(),
			'invalid_date'   => $this->test_data_invalid_date(),
			'invalid_output_date_format'   => $this->test_data_invalid_output_date_format(),
			'invalid_input_date_format'   => $this->test_data_invalid_input_date_format(),

		);
	}

	function test_data_invalid_string_option() {

		$out = array();

		$out['post_content'] = <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/9/18,Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Spn,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
CONTENT;

		$out['html_output'] = <<<ROWS
3 Field validation error line: 2 Data error in field Day if present, expected one of "Sat,Sun,Thu,Tue,Mon,Fri" got "Spn"
Day,Date,Event,Time,Team,Note,Junior
Sat,09/03/2018,Winter Fun Sailing,1100,,,
Sat,24/03/2018,New Members Induction,1100,,,
Spn,25/03/2018,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,25/03/2018,Boat move and beach clean,1100,,,
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
input_format = n/j/y
output_format = d/m/Y

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


	function test_data_validation_failure() {

		$out = array();

		$out['post_content'] = <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/9/18,Winter Fun Sailing Winter Fun Sailing Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,0800,,,
CONTENT;

		$out['html_output'] = <<<ROWS
1 Field validation error line: 0 Data error in field Event value too long, a max length of  45 is expected. 56 given. Value: "Winter Fun Sailing Winter Fun Sailing Winter Fun Sailing"
4 Field validation error line: 3 Data error in field Time if present, expected one of "1100,1030,1830,1900,1400,1800,0830,TBA,0930" got "0800"
Day,Date,Event,Time,Team,Note,Junior
Sat,09/03/2018,Winter Fun Sailing Winter Fun Sailing Winter Fun Sailing,1100,,,
Sat,24/03/2018,New Members Induction,1100,,,
Sun,25/03/2018,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,25/03/2018,Boat move and beach clean,0800,,,
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
input_format = n/j/y
output_format = d/m/Y

[Event]
type = string
max-length = 45
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

	function test_data_valid_content() {

		$out = array();

		$out['post_content'] = <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/9/18,Winter Fun Sailing,1100,A,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
CONTENT;

		$out['html_output'] = <<<ROWS
Date,Event,Team
09/03/2018,Winter Fun Sailing,A
24/03/2018,New Members Induction,
25/03/2018,BST STARTS - CLOCKS FORWARD 1 HOUR,
25/03/2018,Boat move and beach clean,
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
			'fields'  => 'Date,Event,Team',
		);

		$out['fields'] = <<<FIELDS
[Day]
type = string
options = Sat,Sun,Thu,Tue,Mon,Fri

[Date]
type = date
input_format = n/j/y
output_format = d/m/Y

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


	function test_data_invalid_heading_column() {

		$out = array();

		$out['post_content'] = <<<CONTENT
Day,Date,Event,Extra
Sat,3/9/18,Winter Fun Sailing
Sat,3/24/18,New Members Induction
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR
Sun,3/25/18,Boat move and beach clean
CONTENT;

		$out['html_output'] = <<<ROWS
<p class="openclub_csv_error">Error: : Validator Extra does not exists, check the column name.</p>
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
input_format = n/j/y
output_format = d/m/Y

[Event]
type = string
max-length = 60
required = true
FIELDS;

		return $out;

	}

	/**
	 * @return array
	 *
	 * @see \CSVDisplayTest::test_post_content_with_column_mismatch_outputs_correct_data_to_csv_rows_template_file()
	 */
	function test_data_csv_row_column_mismatch() {

		$out = array();

		$out['post_content'] = <<<CONTENT
Day,Date,Event
Sat,3/9/18,Winter Fun Sailing,Extra
CONTENT;

		$out['html_output'] = <<<ROWS
<p class="openclub_csv_error">Error: : Post %d, line 0 column count mismatch, expected 3 columns.  Header columns are: Day,Date,Event. Data is: Sat,3/9/18,Winter Fun Sailing,Extra.</p>
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
		);

		$out['fields'] = <<<FIELDS
[Day]
type = string

[Date]
type = date
input_format = n/j/y

[Event]
type = string
FIELDS;

		return $out;

	}


	/**
	 * Shortcode data
	 *
	 * @return array
	 */
	function test_f_data() {

		$out = array();

		$out['post_content'] = <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/18/18,Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
CONTENT;

		$out['html_output'] = <<<ROWS
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

	function test_data_invalid_integer(){

		$out = array();

		$out['post_content'] = <<<CONTENT
Day,Number
Sat,1
Sat,s
CONTENT;

		$out['html_output'] = <<<ROWS
2 Field validation error line: 1 Field must be an integer.
Day,Number
Sat,1
Sat,s
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
		);

		$out['fields'] = <<<FIELDS
[Day]
type = string

[Number]
type = int

FIELDS;

		return $out;


	}

	function test_data_date_format_conversion_valid() {

		// d	Day of the month, 2 digits with leading zeros	01 to 31
		// j	Day of the month without leading zeros	1 to 31
		// n	Numeric representation of a month, without leading zeros	1 through 12
		// m	Numeric representation of a month, with leading zeros	01 through 12
		// Y	A full numeric representation of a year, 4 digits	Examples: 1999 or 2003
		// y	A two digit representation of a year	Examples: 99 or 03

		$out = array();

		$out['post_content'] = <<<CONTENT
Date
23/3/18
3/12/18
CONTENT;

		$out['html_output'] = <<<ROWS
Date
23/03/2018
03/12/2018
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
		);

		$out['fields'] = <<<FIELDS
[Date]
type = date
input_format = j/n/y
output_format = d/m/Y

FIELDS;

		return $out;

	}

	function test_data_invalid_date() {


		// d	Day of the month, 2 digits with leading zeros	01 to 31
		// j	Day of the month without leading zeros	1 to 31
		// n	Numeric representation of a month, without leading zeros	1 through 12
		// m	Numeric representation of a month, with leading zeros	01 through 12
		// Y	A full numeric representation of a year, 4 digits	Examples: 1999 or 2003
		// y	A two digit representation of a year	Examples: 99 or 03

		$out = array();

		$out['post_content'] = <<<CONTENT
Date
23/3/18
3/17/18
CONTENT;

		$out['html_output'] = <<<ROWS
<p class="openclub_csv_error">Error: : [Warning] Date field validation failed, parsed date is invalid:3/17/18</p>
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
		);

		$out['fields'] = <<<FIELDS
[Date]
type = date
input_format = j/n/y
output_format = d/m/Y

FIELDS;

		return $out;
	}


	function test_data_invalid_output_date_format() {


		// d	Day of the month, 2 digits with leading zeros	01 to 31
		// j	Day of the month without leading zeros	1 to 31
		// n	Numeric representation of a month, without leading zeros	1 through 12
		// m	Numeric representation of a month, with leading zeros	01 through 12
		// Y	A full numeric representation of a year, 4 digits	Examples: 1999 or 2003
		// y	A two digit representation of a year	Examples: 99 or 03

		$out = array();

		$out['post_content'] = <<<CONTENT
Date
23/3/18
3/17/18
CONTENT;

		$out['html_output'] = <<<ROWS
<p class="openclub_csv_error">Error: : Invalid output_format specified</p>
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
		);

		$out['fields'] = <<<FIELDS
[Date]
type = date
input_format = j/n/y
output_format = d/m/V

FIELDS;

		return $out;
	}



	function test_data_invalid_input_date_format() {


		// d	Day of the month, 2 digits with leading zeros	01 to 31
		// j	Day of the month without leading zeros	1 to 31
		// n	Numeric representation of a month, without leading zeros	1 through 12
		// m	Numeric representation of a month, with leading zeros	01 through 12
		// Y	A full numeric representation of a year, 4 digits	Examples: 1999 or 2003
		// y	A two digit representation of a year	Examples: 99 or 03

		$out = array();

		$out['post_content'] = <<<CONTENT
Date
23/3/18
3/17/18
CONTENT;

		$out['html_output'] = <<<ROWS
<p class="openclub_csv_error">Error: : Invalid input_format specified</p>
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
		);

		$out['fields'] = <<<FIELDS
[Date]
type = date
input_format = j/n/V
output_format = d/m/y

FIELDS;

		return $out;
	}



}