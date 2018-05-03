<?php

namespace OpenClub;


class CSV_Display {

	/**
	 * @param array $row
	 *  Array(
	 *      [value] =>
	 *      [formatted_value] =>
	 *      [display_default] => 1
	 *  )
	 * @param bool $formatted_value
	 *
	 * @see Output_Data->get_row_data()
	 *
	 * @return string|void
	 */
	public static function get_csv_row( array $row, $formatted_value = true ){

		if( empty( $row[ 'data' ] ) ) {
			return __('empty', 'openclub_csv');
		}

		$out = array();
		foreach($row['data'] as $field_name => $values ){
			if( $formatted_value ) {
				$out[] = $values['formatted_value'];
			} else {
				$out[] = $values['value'];
			}
		}
		
		return implode( ',', $out );

	}


	/**
	 * @param $config
	 * @param null $plugin_directory - location of plugin templates directory, used for shortcodes in custom plugins
	 *   built on the openclub_csv API
	 *
	 * @return string
	 */
	public static function get_html( $config, $plugin_directory = null ) {

		$out = '';

		try {

			$config = self::set_default_display_template( $config );

			/**
			 * @var $input \OpenClub\Data_Set_Input
			 */
			$input = \OpenClub\Factory::get_data_input_object( $config );

			/**
			 * @var $output \OpenClub\Output_Data
			 */
			$output_data = \OpenClub\Factory::get_output_data( $input );

			/**
			 * Allow late altering of the data in plugins
			 */
			apply_filters( 'openclub_csv_display_data', $output_data, $input );

			if ( ! empty( $output_data ) && $output_data->exists() ) {

				/**
				 * @var $templates \OpenClub\Template_Loader
				 */
				$templates = \OpenClub\Factory::get_template_loader();

				if ( $plugin_directory ) {
					$templates->set_plugin_dir_path( $plugin_directory );
				}

				$templates->set_template_data(
					array(
						'output_data' => $output_data,
						'config'      => $config
					)
				);

				$out .= $templates->get_template( $config['display'] );

			} else {
				$out .= __( 'No data', 'openclub_csv' );
			}

		} catch ( \Exception $e ) {
			$out .= __( 'Error', 'openclub_csv' ) . ': ' . $e->getMessage();
		}

		return $out;
	}

	/**
	 * @param array $config
	 *
	 * post_id              - the post of type openclub-csv
	 * error_messages       - "yes" / "no"
	 * error_lines          -  "yes" / "no"
	 * future_events_only   - "yes" if grouping on a date field display events only in the future
	 * display              - the template name with .php ending. Placed in the theme or plugin templates directory.
	 *                          Theme templates take precedence over plugin templates
	 * fields               - choose which fields to display and order, overrides fields settings
	 * group_by_field       - group on a particular field, the template must be able to work with that
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
				'context'            => null,
				'limit'              => false,
				'filter'             => null,
			),
			$config
		);
	}

	private static function set_default_display_template( $config ) {

		if ( ! empty( $config['group_by_field'] ) && 'table' === $config['display'] ) {
			$config['display'] = 'grouped_list';
		}

		return $config;

	}
}