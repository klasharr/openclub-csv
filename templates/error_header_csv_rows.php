<?php

if ( $data->config['display_config'] ) {
	echo "<pre>";
	print_r( $data->config );
	echo "</pre>";
}

if ( 'yes' === $data->config['error_messages'] && $data->output_data->get_errors() ) {
	foreach ( $data->output_data->get_errors() as $line_number => $error ) {
		echo ( $line_number + 1 ) . ' ' . $error . "\n";
	}
}