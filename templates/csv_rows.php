<?php

echo \OpenClub\CSV_Display::template_output( $data, 'config_output' );
echo \OpenClub\CSV_Display::template_output( $data, 'error_header_csv_rows' );

if( $data->config[ 'group_by_field' ] ) {

	foreach( $data->output_data->get_rows() as $group_key => $row_group ){
		
		echo $group_key . \OpenClub\CSV_Display::br();
		
		echo implode( ',', $data->output_data->get_header_fields() ) . \OpenClub\CSV_Display::br();
		
		foreach( $row_group as $row ) {
			if ( 0 === $row['error'] || ( 1 === $row['error'] && $data->config['error_lines'] ) ) {
				echo \OpenClub\CSV_Display::get_csv_row( $row ) . \OpenClub\CSV_Display::br();
			}
		}
		echo '----------------------------------------' . \OpenClub\CSV_Display::br();
	}

} else {

	echo implode( ',', $data->output_data->get_header_fields() ) . \OpenClub\CSV_Display::br();

	foreach( $data->output_data->get_rows() as $row ) {
		if ( 0 === $row['error'] || ( 1 === $row['error'] && $data->config['error_lines'] ) ) {
			echo \OpenClub\CSV_Display::get_csv_row( $row ) . \OpenClub\CSV_Display::br();
		}
	}
}



