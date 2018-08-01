<!-- openclub-csv -->
<?php echo \OpenClub\CSV_Display::template_output( $data, 'error_header' );

/**
 * @see default template wp-content/plugins/openclub-csv/templates/future_past_toggle.php
 */
echo \OpenClub\CSV_Display::get_past_future_toggle_links( $data->config ); ?>

<table class='openclub_csv'>
	<tr>
		<th>
			<?php echo implode( '</th><th>', $data->output_data->get_header_fields() ); ?>
	</tr>
	<?php

	if ( empty( $data->config['group_by_field'] ) ) {
		echo '<p class="openclub_csv_error">Error: ' . esc_html__( 'No group by field', 'openclub_csv' ) .'</p>';
		return;
	}

	foreach ( $data->output_data->get_rows() as $grouped_field_value => $grouped_rows ) {

		foreach ( $grouped_rows as $row ) {

			if ( 0 === $row['error'] || ( 1 === $row['error'] && 'yes' === $data->config['error_lines'] ) ) {
				echo "<tr  class='" . esc_attr( $row['class'] ) . "'>";
				foreach ( $row['data'] as $fieldname => $values ) {
					echo '<td>' . esc_html( $values['formatted_value'] ) . '</td>';
				}
				echo "</tr>\n";
			}
		}
	}
	?>
</table>
