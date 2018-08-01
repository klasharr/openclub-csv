<!-- openclub-csv -->
<?php echo \OpenClub\CSV_Display::template_output( $data, 'error_header' ); ?>

<p>
	<?php
	
	if ( empty( $data->config['group_by_field'] ) ) {
		echo '<p class="openclub_csv_error">Error: ' . esc_html__( 'No group by field', 'openclub_csv' ) .'</p>';
		return;
	}

	foreach ( $data->output_data->get_rows() as $grouped_field_value => $grouped_rows ) {

		echo "<p>" . esc_html__( $grouped_field_value ) . "</p>";

		foreach ( $grouped_rows as $row ) {

			if ( 0 === $row['error'] || ( 1 === $row['error'] && 'yes' === $data->config['error_lines'] ) ) {
				echo "<span  class='" . esc_attr( $row['class'] ) . "'>";
				foreach ( $row['data'] as $fieldname => $values ) {
					echo esc_html( $values['formatted_value'] ) . ',';
				}
				echo "</span><br/>\n";
			}
		}
		echo '</p>';
	}
	?>
</p>