# openclub-csv


[![Build Status](https://travis-ci.org/klasharr/openclub-csv.svg?branch=master)](https://travis-ci.org/klasharr/openclub-csv)


A WordPress plugin which allows you to use CSV data as a data source and display it on your website.

 - store CSV data in a new custom post type and use this as a data source
 - apply validation and display rules to the CSV data fields, e.g. one input date format, a different output date format
 - set of basic table and list templates with overriding and custom template creation/usage in plugins or your theme
 - API to easily make shortcodes using optionally custom templates
 - filter API e.g. filter out data based on the value of a field
 - WP_CLI API to write scripts using CSV data
 - field display rules, data sorting, limit, grouping and basic date time rules e.g. only show data > current timestamp
 - early and late filters to alter data based on your own rules.



## Installation

Put this plugin in your WordPress plugins directory and enable. You will see a new CSV menu item in wp-admin, this lets you manage the CSV custom post types.

## Example Usage


Add a new CSV content item, the main content area will contain the CSV file content including header line e.g.

```
Day,Date,Event,Time,Team,Note,IsJunior
Sun,3/24/18,Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
Sat,3/31/18,The Opener,1400,3,,
Sun,4/1/18,Spring Series 1 of 10 - Spring Berthing starts,1100,4,,
```

Then add a custom field calls `fields` which contains the header field rules. The rules force validation on the CSV data and enforce default field display. This can be overwritten later. For the above CSV content the fields might look like this:


```
[Day]
type = string
options = Sat,Sun,Thu,Tue,Mon,Fri


[Date]
type = date
input_format = m/d/y
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
display = false

[Note]
type = string
max-length = 45
display = false

[IsJunior]
type = int
options = 1
display = false
```

## Viewing the content

View the CSV post types as you would any other piece of content in WordPress, each has a permalink defined:

`/openclub-csv/<your slug>/`


### Shortcodes

Also display the content via a configurable shortcode for example:

Default view the same as the CSV page view.
```
[openclub_display_csv post_id=1361]
```

Overriding the display field defaults, this will display the columns Date and Event only.
```
[openclub_display_csv post_id=1361 fields="Date,Event"]
```

Grouping on the date field and filtering for future events only.
```
[openclub_display_csv post_id=1361 group_by_field="Date" future_events_only="yes"  display="grouped_date_table" limit="3"]
```



#### Fields configuration explained

The fields content is basically content in the [PHP ini format](http://php.net/manual/en/function.parse-ini-file.php) and is parsed internally by `parse_ini_file()`. Each CSV column will have a field name, and this field is described in the fields ini configuration. So far there are three field types; date, string and int. Others will follow.

1. Date

```
[<CSV field name>]
type = date
input_format = m/d/y
output_format = d/m/Y
```

`output_format` is optional, `input_format` is required. The format corresponds to [PHP date formatting rules](http://php.net/manual/en/function.date.php). The currently supported date pattern can be seen [here](https://github.com/klasharr/openclub-csv/blob/master/inc/fields/class-date.php#L30).

```
$date_pattern = '/^[djmn][\/\s]?[djSmn][\/\s]?[YyM]$/';
```

2. String

```
[<CSV field name>]
type = string
max-length = 60
```

This is a basic field currently with a max length validation.


3. Int

```
[<CSV field name>]
type = int
```

Validates only if the data is numeric.

#### Extra rules for all fields

1. Options

```
options = 1100,1030,1830,1900,1400,1800,0830,TBA,0930
```

If data exists, it must be one of the defined options.


2. Required
```
required = true
```
Is the field required?

3. Display

```
display = false
```

This controls the default display setting for a field and it can be overridden in your shortcodes. This is useful if you want to suppress display of certain fields.



### Examples in production use:

- [http://www.swanagesailingclub.org.uk/](http://www.swanagesailingclub.org.uk/) - the 'Next Sailing Events' panel
- [http://www.swanagesailingclub.org.uk/social-events/](http://www.swanagesailingclub.org.uk/social-events/) - showing default table display
- [http://www.swanagesailingclub.org.uk/sailing-programme/2018/](http://www.swanagesailingclub.org.uk/sailing-programme/2018/) - using a custom plugin template
- [http://www.swanagesailingclub.org.uk/safety-teams-2018/](http://www.swanagesailingclub.org.uk/safety-teams-2018/) - another custom template


### Example shortcode implementation

Taken from another plugin using the API, see more [here](https://github.com/klasharr/ssc/blob/master/inc/shortcodes.php).

```
add_shortcode( 'ssc_safety_teams', function( $config ){

	$config = shortcode_atts(
		OpenClub\CSV_Display::get_config(
			array(
				'context' => 'ssc_safety_teams_shortcode',
			)),
		$config
	);

	return OpenClub\CSV_Display::get_html( $config, SSC_PLUGIN_DIR );
} );

```

### Example CLI command using the API

See [here](https://github.com/klasharr/openclub-csv/blob/master/cli/class-openclub.php). The minimum with no error handling looks like this:

```
/**
 * @var $input \OpenClub\Data_Set_Input
 */
$input = \OpenClub\Factory::get_data_input_object(
    array(
        'post_id' => $this->post_id,
    )
);

/**
 * @var $output_data \OpenClub\Output_Data
 */
$output_data = \OpenClub\Factory::get_output_data( $input );

foreach ( $output_data->get_rows() as $row ) {
    WP_CLI::log( CSV_Display::get_csv_row( $row ) );
}
```


### Can I use it?
Not yet, work still to do:

- on security
- code improvements and cleanup
- much more testing
- unit tests
- inline and wiki documentation
