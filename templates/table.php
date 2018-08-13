<?php echo \OpenClub\CSV_Display::template_output( $data, 'error_header' ); ?>
<table class='openclub_csv'>
	<tr>
		<th>
			<?php echo implode( '</th><th>', $data->output_data->get_header_fields() ); ?>
	</tr>
	<?php

	foreach ( $data->output_data->get_rows() as $row ) {
		if ( 0 === $row['error'] || ( 1 === $row['error'] && 'yes' === $data->config['error_lines'] ) ) {
			echo "<tr  class='" . esc_attr( $row['class'] ) . "'>\n";
			foreach ( $row['data'] as $fieldname => $values ) {
				echo "\t<td>" . esc_html( $values['formatted_value'] ) . "</td>\n";
			}
			echo "</tr>\n";
		}
	}
	?>
</table>