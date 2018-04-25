<?php

if( $data->config[ 'error_messages' ] == 'yes' && $data->output_data->get_errors()) : ?>
	<div class='openclub_csv_error'>
		<h3><?php echo __( 'Errors', 'openclub_csv' );?></h3>
		<p class='openclub_csv'>
			<?php foreach($data->output_data->get_errors() as $line_number => $error ) { ?>
				Line: <?php echo  ( $line_number + 1 ). ' ' . $error ?>
				<br/>
			<?php } ?>
		</p>
	</div>
<?php endif; ?>
<p>
	<?php

	foreach ( $data->output_data->get_rows() as $grouped_field_value => $grouped_rows ) {

		echo "<p><strong>$grouped_field_value</strong></br>";

		foreach ( $grouped_rows as $row ) {

			if ( $row['error'] == 0 || ( $row['error'] == 1 && $data->config['error_lines'] == 'yes' ) ) {
				echo "<span  class='" . $row['class'] . "'>";
				foreach ( $row['data'] as $fieldname => $values ) {
					echo $values['formatted_value'] . ',';
				}
				echo "</span><br/>\n";
			}
		}
		echo '</p>';
	}
	?>
</p>