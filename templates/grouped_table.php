<!-- Openclub-programme -->
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
<?php endif;
?>
	<table class='openclub_csv'>
	<tr>
		<th>
			<?php echo implode( '</th><th>', $data->output_data->get_header_fields() ); ?>
	</tr>
<?php

/**
 * @todo smarter way to check for the lack of the group_by_field field. This will cause an endless loop
 */
if( !$data->config[ 'group_by_field' ] ) {
	echo __( 'No group by field', 'openclub_csv' );
	return;
}

foreach ( $data->output_data->get_rows() as $grouped_field_value => $grouped_rows ) {

	foreach( $grouped_rows as $row ) {

		if ( $row['error'] == 0 || ( $row['error'] == 1 && $data->config['error_lines'] == 'yes' ) ) {
			echo "<tr  class='" . $row['class'] . "'>";
			foreach ( $row['data'] as $fieldname => $values ) {
				echo '<td>' . $values['formatted_value'] . '</td>';
			}
			echo "</tr>\n";
		}

	}
}
?>
</table>
