<?php

namespace OpenClub\CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \OpenClub\Factory;
use \WP_CLI;
use \Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once( OPENCLUB_IMPORTER_PLUGIN_DIR . '/inc/class-parser.php' );

Class File_Runner {

	/**
	 * @var $post_id int
	 */
	private $post_id;

	public function __invoke( $args ) {

		try {

			if ( empty( $args[0] ) || (int) $args[0] === 0 ) {
				throw new Exception( 'The first argument must be a non zero integer value.' );
			}

			$this->post_id = $args[0];

			$this->execute();

		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		//if ( empty( $this->flattenedEvents ) ) {
		////	WP_CLI::error( '$this->flattenedEvents is empty, most likely ->execute wasn\'t called' );
		//}

		/**
		 * @var $event EventDTO
		 */
		//foreach ( $this->flattenedEvents as $event ) {

		//	WP_CLI::log( $event );

		//}
		WP_CLI::success( 'Success!!' );
	}


	public function execute() {

		$parser = Factory::get_parser();

		$parser->init(
			$this->get_post()
		);

		$parsed_lines = $parser->get_data( Factory::get_null_filter() );

		foreach( $parsed_lines[ 'data' ] as $line ) {
			WP_CLI::log( $line );
		}

		return true;
	}

	private function get_post() {

		$post = get_post( $this->post_id );

		if ( empty( $post ) || ! is_a( $post, 'WP_Post' ) ) {
			throw new Exception (
				sprintf( '$this->post_id %d does not return a post object.', $this->post_id )
			);
		}

		if ( $post->post_type != 'openclub-csv' || $post->status == 'auto-draft' ) {
			throw new Exception (
				sprintf( '$this->post_id %d does not return a post object of type CSV.', $this->post_id )
			);
		}

		if ( $fields = get_post_meta( $this->post_id, 'fields', true ) ) {
			$post->field_settings = parse_ini_string( $fields, true );
		} else {
			throw new Exception (
				sprintf( '$this->post_id %d does not have a fields post meta set.', $this->post_id )
			);
		}

		return $post;
	}
}


