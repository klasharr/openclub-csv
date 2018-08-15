<?php
echo \OpenClub\CSV_Display::template_output( $data, 'error_header_csv_rows' );

echo implode( ',', $data->output_data->get_header_fields() ). "\n";
foreach( $data->output_data->get_rows() as $row ){
	echo \OpenClub\CSV_Display::get_csv_row( $row ) . "\n";
}