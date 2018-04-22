<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'class-factory.php' );

class CSV_Util {

	public static function get_csv_post( $post_id ) {

		if( !is_numeric( $post_id ) ) {
			throw new \Exception (
				sprintf( '$post_id %s must be a numeric value.', $post_id )
			);
		}

		/** @var $post \WP_Post  */
		$post = get_post( $post_id );

		if ( empty( $post ) || ! is_a( $post, 'WP_Post' ) ) {
			throw new \Exception (
				sprintf( '$post_id %d does not return a post object.', $post_id )
			);
		}

		if ( $post->post_type != 'openclub-csv' ) {
			throw new \Exception (
				sprintf( '$post_id %d does not return a post object of post type openclub-csv.', $post_id )
			);
		}

		if ( $post->status == 'auto-draft' ) {
			throw new \Exception (
				sprintf( '$post_id %d returns an openclub-csv post type auto-draft.', $post_id )
			);
		}

		if ( $fields = get_post_meta( $post_id, 'fields', true ) ) {
			$post->field_settings = parse_ini_string( $fields, true );
		} else {
			throw new \Exception (
				sprintf( '$post_id %d does not have a fields post meta set.', $post_id )
			);
		}
		return $post;

	}


	/**
	 * @param Data_Set_Input $input
	 *
	 * @return mixed|Data_Set|void
	 * @throws \Exception
	 */
	public static function get_data_set( Data_Set_Input $input ) {

		$parser = Factory::get_parser( $input );
		return $parser->get_data();
	}


	public static function get_csv_table_row( DTO $line_data, Field_Manager $field_manager ) {

		$class = '';
		if( $line_data->has_validation_error()){
			$class = 'openclub_csv_error';
		}

		$out = '<tr class="'.$class.'">';

		foreach( $field_manager->get_display_field_names() as $field_name ) {
			$out .= '<td>' . $field_manager->get_field( $field_name )->format_value( $line_data->get_value( $field_name ) ) . '</td>';
		}
		$out .= "</tr>\n";

		return $out;

	}


	public static function get_formatted_csv_line_error_message( $error_message ){
		return '<span class="openclub_csv_error">'.$error_message.'</span><br/>';
	}

}