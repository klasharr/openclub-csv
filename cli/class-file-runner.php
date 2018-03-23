<?php

namespace OpenClub\CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \OpenClub\Factory;
use \OpenClub\CSV_Util;
use \WP_CLI;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-file-runner-base.php' );

Class File_Runner extends  File_Runner_Base{

	/**
	 * @param $args
	 */
	public function __invoke( $args ) {
		parent::__invoke( $args );
	}

	/**
	 * @throws \Exception
	 * @throws \OpenClub\Exception
	 */
	public function execute() {

		$parser = Factory::get_parser();

		$parser->init( CSV_Util::get_csv_post( $this->post_id ) );

		$parsed_lines = $parser->get_data( Factory::get_null_filter() );

		foreach( $parsed_lines[ 'data' ] as $line ) {
			WP_CLI::log( $line );
		}

		WP_CLI::success( 'Success!' );
	}

}


