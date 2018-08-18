<!-- openclub_csv table.php template -->
<?php

// The default template.

echo \OpenClub\CSV_Display::template_output( $data, 'config_output' );
echo \OpenClub\CSV_Display::template_output( $data, 'error_header' );
echo \OpenClub\CSV_Display::get_past_future_toggle_links( $data->config );

if( $data->config[ 'group_by_field' ] ) {

	foreach( $data->output_data->get_rows() as $group_key => $row_group ) {

		echo $group_key . \OpenClub\CSV_Display::br();?>
<table class='openclub_csv'>
	<tr>
		<th>
			<?php echo implode( '</th><th>', $data->output_data->get_header_fields() ); ?>
		</tr>
	<?php

	foreach ( $row_group as $row ) {
		if ( 0 === $row['error'] || ( 1 === $row['error'] && $data->config['error_lines'] ) ) {
			echo "<tr  class='" . esc_attr( $row['class'] ) . "'>\n";
			foreach ( $row['data'] as $fieldname => $values ) {
				echo "\t<td>" . esc_html( $values['formatted_value'] ) . "</td>\n";
			}
			echo "</tr>\n";
		}
	}
	?>
</table>
<?php	}

} else {
?>
<table class='openclub_csv'>
	<tr>
		<th>
			<?php echo implode( '</th><th>', $data->output_data->get_header_fields() ); ?>
	</tr>
	<?php

	foreach ( $data->output_data->get_rows() as $row ) {
		if ( 0 === $row['error'] || ( 1 === $row['error'] && $data->config['error_lines'] ) ) {
			echo "<tr  class='" . esc_attr( $row['class'] ) . "'>\n";
			foreach ( $row['data'] as $fieldname => $values ) {
				echo "\t<td>" . esc_html( $values['formatted_value'] ) . "</td>\n";
			}
			echo "</tr>\n";
		}
	}
	?>
</table><?php } ?>