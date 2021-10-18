# openclub-csv


[![Build Status](https://travis-ci.org/klasharr/openclub-csv.svg?branch=master)](https://travis-ci.org/klasharr/openclub-csv)

This is a WordPress plugin which allows you to use CSV data as a data source and display it on your website or process it. Some features:

 - it stores CSV data in a new custom post type which is used as a data source
 - applies validation and display rules to the CSV data fields
 - display the CSV data via shortcodes
 - has a set of basic table and list templates which you can override or add to with your own templates in your own plugins
 - API to make shortcodes using built in or custom templates
 - API to make CLI commands working with CSV data
 - filter API to create filters to exlude display data based on rules
 - WP_CLI API to write scripts and process CSV data
 - field display rules, data sorting, limit, grouping, basic time based functionality
 - data filter hook before passing to template files
 
### Example in use:

- [https://klausharris.de/example-csv/](https://klausharris.de/example-csv/) - Example page using data from this readme.

## Installation

Put this plugin in your WordPress plugins directory and enable. You will see a new CSV menu item in wp-admin, this lets you manage the CSV custom post types.
After installing the plugin and activating, go to Settings > Permalinks and without changing anything, hit 'Save'. This will flush the permalinks.

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

Or most usefully via shortcodes.


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

Grouping on the date field and filtering for future events only with a display limit.
```
[openclub_display_csv post_id=1361 group_by_field="Date" future_items_only="yes" limit="3"]
```

Selecting a different output template
```
[openclub_display_csv post_id=1361 display="grouped_date_table"]
```

Display the config for this data source and dislay
```
[openclub_display_csv post_id=1361 display="grouped_date_table" display_config="yes"]
```

Display data using a template file in your own plugin (more details below)

```
[openclub_display_csv post_id=1365 display="data_display" plugin_template_dir='MY_PLUGIN_DIR' ]
```

Pass the CSV data through a data filter in your own plugin  (more details below)

```
[openclub_display_csv post_id=1365 context='ssc_safety_teams_shortcode' ]
```



## Fields configuration explained

The fields content is basically content in the [PHP ini format](http://php.net/manual/en/function.parse-ini-file.php) and this is parsed internally by `parse_ini_file()`. Each CSV column will have a field name, and this field is described in the fields ini configuration. So far there are three field types; date, string and int. Others will follow.

The minimum field rules are that:

1. the field name must correspond to the CSV header line field name 
2. type must be present, currently types available are `date`,`int` and `string`.

Everything else is optional. Below I explain the same fields definition from above.

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

This is a basic field in this example with an optional max length validation.


3. Int

```
[<CSV field name>]
type = int
```

Validates only if the data is numeric.

### Optional rules for all field types

1. Options

```
options = 1100,1030,1830,1900,1400,1800,0830,TBA,0930
```

If data exists, it must be one of the defined options. The values above are examples.


2. Required
```
required = [true|false]
```
Is the field required?


3. Display

```
display = [true|false]
```

This controls the default display setting for a field and it can be overridden in your shortcodes. This is useful if you want to suppress display of certain fields.

4. Max field length

```
max-length = [int]
```


### Example shortcode implementation using the API

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

## CLI command to view CSV data from the shell

`wp openclub list <id>`

Where `<id>` is the post ID for an openclub_csv post type.


### Write your own command using the API

See an example command here [here](https://github.com/klasharr/openclub-csv/blob/master/cli/class-cli-command.php#L25). 

The minimum with the most basic error handling looks like this:

```
<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \WP_CLI;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/cli/class-cli-base.php' );

Class CLI_Command extends CLI_Base {

	public function list( $args ) {

		try {

			$output_data = $this->get_data(
				array(
					'post_id' => $args[0],
					'display' => 'default',
				)
			);

			foreach ( $output_data->get_rows() as $row ) {
				WP_CLI::log( CSV_Display::get_csv_row( $row ) );
			}
			
		} catch ( \Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

}
```

## How to use a template file in your own plugin and make the shortcode use that

1. Define the root directory for your plugin and assign to a constant e.g. `MY_PLUGIN_DIR`
2. Place the template file in a template directory in your plugin e.g. `MY_PLUGIN_DIR/templates/data_display.csv`
3. Set the shortcode config to use your plugin templates directory and your template file e.g.

Example:

```
[openclub_display_csv post_id=1365 display="data_display" plugin_template_dir='MY_PLUGIN_DIR']
```

## How to pass the data for the shortcode through a data filter

1. Set up the dependency to the openclub_csv in own plugin by copying [https://github.com/klasharr/openclub-csv/blob/master/inc/class-openclub-csv-dependency.php](https://github.com/klasharr/openclub-csv/blob/master/inc/class-openclub-csv-dependency.php) into your plugin file

2. Require it in your plugin by adding this code to your plugin root file

```
// ======================== Dependency check =======================
require_once( 'inc/class-openclub-csv-dependency.php' );
if(! Openclub_CSV_Dependency::check( __FILE__ ) ){
	return;
}
```

3. Add the filter to your plugin

`add_filter( 'openclub_csv_display_data', 'my_plugin_short_code_filter', 10, 2 );`

4. Add the filter function. Note: you must add the context check otherwise the filter will affect all shortcodes e.g.

```
function my_plugin_short_code_filter( \OpenClub\Output_Data $data, \OpenClub\Data_Set_Input $input ) {

     if ( 'my_shortcode_context' === $input->get_context() ) {

           // Get the data

           $tmp = $data->get_rows()

           // alter it then set it back

           $data->set_rows( $tmp );
     }
}

```

Example:

The following shortcode example sets:

- template file = `data_display.php`
- template file location = `SSC_PLUGIN_DIR.'/templates/safety_teams.php'` (rather than openclub_csv's template directory). This assumes that the plugin code has `SSC_PLUGIN_DIR` defined in code like this:

```
define( 'SSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
```

- data filter function, assumes the data filter has been set and context switch is `ssc_safety_teams_shortcode`

```
[openclub_display_csv post_id=1365 error_lines="yes" error_messages="yes"  display="safety_teams" plugin_template_dir="SSC_PLUGIN_DIR" context="ssc_safety_teams_shortcode" group_by_field="Team"]
```

## Can I use it?
Yes, I've been working on this and using it in production for three years and am confident it works, is robust and secure. It is aslo stable and has a good unit test suite. I'm not far off publishing on wordpress.org now, remaining tasks are:

- a security review, running it through WordPress.com VIP's PHPCS, double checking security escaping
- improve inline and wiki documentation

## Tested up to

WordPress 5.8.1
Jetpack 7.1
