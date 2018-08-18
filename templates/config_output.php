<?php

//var_dump($data->config);

if ( $data->config['display_config'] ) {

	echo '----------------------------------' . \OpenClub\CSV_Display::br();
	foreach( $data->config as $key => $value ) {

		if( $value === false ) {
			$value = 'false';
		}
		if( $value === null ) {
			$value = 'null';
		}
		if( $value === true ) {
			$value = 'true';
		}

		echo $key . ' = ' . $value .  \OpenClub\CSV_Display::br();
	}
	echo '----------------------------------' . \OpenClub\CSV_Display::br();
}