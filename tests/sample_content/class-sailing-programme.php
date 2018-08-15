<?php

namespace OpenClubCSV\TestData;

require_once( 'class-base-dummy-openclub-csv.php' );

class Sailing_Programme extends Base_Dummy_Openclub_CSV {

	function set_input_and_output_samples(){

		$this->input_and_output_samples = array(
			'a' => $this->test_a_data(),
			'b' => $this->test_b_data(),
			'c' => $this->test_c_data(),
			'd' => $this->test_d_data(),
			'e' => $this->test_e_data(),
		);
	}

	function test_a_data(){

		$out = array();

		$out['post_content'] =  <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/9/18,Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Spn,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
CONTENT;

		$out['html_output'] =  <<<ROWS
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



	function test_b_data(){

		$out = array();

		$out['post_content'] =  <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/9/18,Winter Fun Sailing Winter Fun Sailing Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,0800,,,
CONTENT;

		$out['html_output'] =  <<<ROWS
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

	function test_c_data(){

		$out = array();

		$out['post_content'] =  <<<CONTENT
Day,Date,Event,Time,Team,Note,Junior
Sat,3/9/18,Winter Fun Sailing,1100,A,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
CONTENT;

		$out['html_output'] =  <<<ROWS
Date,Event,Team
09/03/2018,Winter Fun Sailing,A
24/03/2018,New Members Induction,
25/03/2018,BST STARTS - CLOCKS FORWARD 1 HOUR,
25/03/2018,Boat move and beach clean,
ROWS;

		$out['config'] = array(
			'display' => 'csv_rows',
			'fields' => 'Date,Event,Team',
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


	function test_d_data(){

		$out = array();

		$out['post_content'] =  <<<CONTENT
Day,Date,Event,Extra
Sat,3/9/18,Winter Fun Sailing
Sat,3/24/18,New Members Induction
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR
Sun,3/25/18,Boat move and beach clean
CONTENT;

		$out['html_output'] =  <<<ROWS
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
	function test_e_data(){

		$out = array();

		$out['post_content'] =  <<<CONTENT
Day,Date,Event
Sat,3/9/18,Winter Fun Sailing,Extra
CONTENT;

		$out['html_output'] =  <<<ROWS
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

}