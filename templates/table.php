<?php
/**
if( $data->data_set->has_errors() && $data->config['error_messages' ] == "yes"  ) { ?>

	<h3 class='openclub_csv_error'>Errors</h3>
	<p>
		<?php foreach ( $data->data_set->get_errors() as $line_number => $message ) {
			echo \OpenClub\CSV_Util::get_formatted_csv_line_error_message( $message );
		} ?>
	</p>

<?php } **/


print_r($data->output->get_header_fields()) ;

print_r($data->output->get_rows()) ;

/**
<!--<table class='openclub_csv'>
	<tr><th>
	<?php  echo implode('</th><th>', $data->output->get_header_fields() ); ?>
	</tr>-->
<?php

/**

$class = '';

if( $line_data->has_validation_error()){
	$class = 'openclub_csv_error';
}

$out = '<tr class="'.$class.'">';

foreach( $data->output->get_rows() as $row) {

	foreach( $row as $fieldname => $values )
	$out .= '<td>' . $field_manager->get_field( $field_name )->format_value( $line_data->get_value( $field_name ) ) . '</td>';
}
$out .= "</tr>\n";





return $out;




/**
/** @var DTO $line_data
foreach($data->output->get_rows() as $row ){
	
	if( !$line_data->has_validation_error() ) {
		echo \OpenClub\CSV_Util::get_csv_table_row( $line_data, $data->data_set->get_field_manager() );
		continue;
	}
	if( $data->config['error_lines'] == "yes" ) {
		echo \OpenClub\CSV_Util::get_csv_table_row( $line_data, $data->data_set->get_field_manager() );
	}
}

echo "</table>\n";
 * **/