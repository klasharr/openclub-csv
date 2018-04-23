<?php

// @todo validation errors, display rules

?>
<table class='openclub_csv'>
	<tr>
		<th>
			<?php echo implode( '</th><th>', $data->output_data->get_header_fields() ); ?>
	</tr>
	<?php

	foreach ( $data->output_data->get_rows() as $row ) {

		echo "<tr>";
		foreach ( $row as $fieldname => $values ) {
			echo '<td class="' . $values['class'] . '">' . $values['formatted_value'] . '</td>';
		}
		echo "</tr>\n";
	}
	?>
</table>