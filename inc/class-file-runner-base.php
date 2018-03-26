<?php

namespace OpenClub\CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );

abstract class File_Runner_Base {

	/**
	 * @var $post_id int
	 */
	protected $post_id;


	public function __invoke( $args ) {

		try {

			if ( empty( $args[0] ) || (int) $args[0] === 0 ) {
				throw new \Exception( 'The first argument must be a non zero integer value.' );
			}

			$this->post_id = $args[0];

			$this->execute();

		} catch ( \Exception $e ) {
			\WP_CLI::error( $e->getMessage() );
		}

	}

	abstract function execute();
}