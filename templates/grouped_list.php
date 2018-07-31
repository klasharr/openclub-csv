<!-- openclub-csv -->
<?php
if ( 'yes' === $data->config['error_messages'] && $data->output_data->get_errors() ) : ?>
	<div class='openclub_csv_error'>
		<h3><?php esc_html_e( 'Errors', 'openclub_csv' ); ?></h3>
		<p class='openclub_csv'>
			<?php foreach ( $data->output_data->get_errors() as $line_number => $error ) {
				echo esc_html__( 'Line', 'openclub_csv' ) . ':' . esc_html( ( $line_number + 1 ) . ' ' . $error ) . '<br/>';
			} ?>
		</p>
	</div>
<?php endif; ?>
<p>
	<?php
	/**
	 * @todo smarter way to check for the lack of the group_by_field field. This will cause an endless loop
	 */
	if ( ! $data->config['group_by_field'] ) {
		echo esc_html__( 'No group by field', 'openclub_csv' );

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