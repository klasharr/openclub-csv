<?php

namespace OpenClub;

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

		if ( $post->post_type != 'openclub-csv' || $post->status == 'auto-draft' ) {
			throw new \Exception (
				sprintf( '$post_id %d does not return a post object of type CSV.', $post_id )
			);
		}

		if ( $post->status == 'auto-draft' ) {
			throw new \Exception (
				sprintf( '$post_id %d returns a openclub-csv post type auto-draft.', $post_id )
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
	 * @todo check correctness of filter.
	 *
	 * @param $post_id
	 * @param null $filter
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function get_csv_content( $post_id, $filter = null ) {

		$parser = Factory::get_parser();

		$parser->init( CSV_Util::get_csv_post( $post_id ) );

		if( $filter == null ) {
			return $parser->get_data( Factory::get_null_filter() );
		} else {
			return $parser->get_data( $filter );
		}

	}

}