<?php

if ( $data->config['error_messages'] && $data->output_data->get_errors() ) : ?>
	<div class='openclub_csv_error'>
		<h3><?php esc_html_e( 'Errors', 'openclub_csv' ); ?></h3>
		<p class='openclub_csv'>
			<?php foreach ( $data->output_data->get_errors() as $line_number => $error ) {
				echo esc_html__( 'Line', 'openclub_csv' ) . ':' . esc_html( ( $line_number + 1 ) . ' ' . $error ) . '<br/>';
			} ?>
		</p>
	</div>
<?php endif;