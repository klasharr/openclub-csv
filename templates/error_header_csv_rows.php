<?php

if ( $data->config['error_messages'] && $data->output_data->get_errors() ) {
	foreach ( $data->output_data->get_errors() as $line_number => $error ) {
		echo ( $line_number + 1 ) . ' ' . $error . \OpenClub\CSV_Display::br();
	}
}