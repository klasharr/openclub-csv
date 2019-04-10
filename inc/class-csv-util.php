<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'class-factory.php' );

class CSV_Util {

	public static function get_csv_post( $post_id ) {

		if ( ! is_numeric( $post_id ) ) {
			throw new \Exception (
				sprintf( '$post_id %s must be a numeric value.', $post_id )
			);
		}

		/** @var $post \WP_Post */
		$post = get_post( $post_id );

		if ( empty( $post ) || ! is_a( $post, 'WP_Post' ) ) {
			throw new \Exception (
				sprintf( '$post_id %d does not return a post object.', $post_id )
			);
		}

		if ( 'openclub-csv' !== $post->post_type ) {
			throw new \Exception (
				sprintf( '$post_id %d does not return a post object of post type openclub-csv.', $post_id )
			);
		}

		if ( 'auto-draft' === $post->post_status ) {
			throw new \Exception (
				sprintf( '$post_id %d returns an openclub-csv post type auto-draft.', $post_id )
			);
		}

		if ( $fields = get_post_meta( $post_id, 'fields', true ) ) {

			$post->field_settings = parse_ini_string( $fields, true );

			if( empty( $post->field_settings ) ) {
				throw new \Exception (
					sprintf( '$post_id %d has ini fields but they do not parse', $post_id )
				);
			}
		} else {
			throw new \Exception (
				sprintf( '$post_id %d does not have a fields post meta set.', $post_id )
			);
		}

		return $post;

	}

	/**
	 * @param array $config
	 *
	 * @return Output_Data
	 * @throws \Exception
	 */
	public static function get_output_data_from_config( array $config ) {

		/**
		 * @input Data_Set_Input
		 */
		$input_obj = Factory::get_data_input_object( $config );

		/**
		 * $output_data Output_Data
		 */
		$output_data = Factory::get_output_data( $input_obj );

		if ( ! $output_data->exists() ) {
			throw new \Exception( 'No data for $input_obj' );
		}

		return $output_data;
	}

	/**
	 * @param $input
	 *
	 * @return Output_Data
	 * @throws \Exception
	 */
	public static function get_output_data_from_input_object( Data_Set_Input $input_obj ){

		/**
		 * $output_data Output_Data
		 */
		$output_data = Factory::get_output_data( $input_obj );

		if ( ! $output_data->exists() ) {
			throw new \Exception( 'No data for $input_obj' );
		}

		return $output_data;
	}

}