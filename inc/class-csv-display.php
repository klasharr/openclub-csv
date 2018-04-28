<?php

namespace OpenClub;


class CSV_Display {


	public static function get_html( $config, $plugin_directory = null ) {

		$out = '';

		try {

			/**
			 * @var $input \OpenClub\Data_Set_Input
			 */
			$input = \OpenClub\Factory::get_data_input_object( $config['post_id'] );

			if( !empty(  $config['group_by_field']) ) {
				$input->set_group_by_field($config['group_by_field']);
			}
			if( !empty(  $config[ 'fields' ]) ) {
				$input->set_fields_override( $config[ 'fields' ] );
			}
			if( !empty( $config[ 'group_by'] ) ){
				$input->set_group_by_field( $config[ 'group_by'] );
			}

			/**
			 * @var $output \OpenClub\Output_Data
			 */
			$output_data = \OpenClub\Factory::get_output_data( $input );

			if ( !empty( $output_data ) && $output_data->exists() ) {

				$templates = \OpenClub\Factory::get_template_loader();
				
				if( $plugin_directory ){    
					$templates->set_plugin_dir_path( $plugin_directory );
				}
				
				$templates->set_template_data(
					array(
						'output_data' => $output_data,
						'config' => $config
					)
				);

				echo $templates->get_template( $config[ 'display' ] );

			} else {
				$out .= __( 'No data', 'openclub_csv' );
			}

		} catch ( \Exception $e ) {
			$out .= __( 'Error', 'openclub_csv' ).': ' . $e->getMessage();
		}

		return $out;
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	public static function get_config( $config = array() ) {

		return array_replace(
			array(
				'post_id'            => null,
				'error_messages'     => "yes",
				'error_lines'        => "yes",
				'future_events_only' => null,
				'display'            => 'table',
				'fields'             => null,
				'group_by_field'     => null,
			),
			$config
		);
	}
}