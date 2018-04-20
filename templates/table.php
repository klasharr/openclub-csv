<?php

if( $data->data_set->has_errors() && $data->config['error_messages' ] == "yes"  ) { ?>

	<h3 class='openclub_csv_error'>Errors</h3>
	<p>
		<?php foreach ( $data->data_set->get_errors() as $line_number => $message ) {
			echo \OpenClub\CSV_Util::get_formatted_csv_line_error_message( $message );
		} ?>
	</p>

<?php } ?>

	<table class='openclub_csv'>
	<?php
	


echo \OpenClub\CSV_Util::get_csv_table_header( $data->data_set->get_field_manager() );

/** @var DTO $line_data */
foreach($data->data_set->get_data() as $line_data ){
	
	if( !$line_data->has_validation_error() ) {
		echo \OpenClub\CSV_Util::get_csv_table_row( $line_data, $data->data_set->get_field_manager() );
		continue;
	}
	if( $data->config['error_lines'] == "yes" ) {
		echo \OpenClub\CSV_Util::get_csv_table_row( $line_data, $data->data_set->get_field_manager() );
	}
}

echo "</table>\n";