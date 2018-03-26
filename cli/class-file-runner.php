<?php

namespace OpenClub\CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \OpenClub\Factory;
use \OpenClub\CSV_Util;
use \WP_CLI;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-file-runner-base.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-util.php' );

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

		/**
		 * @var Data_Set @data_set
		 */
		$data_set = \OpenClub\CSV_Util::get_data_set( $this->post_id );

		WP_CLI::log( sprintf( '====== Retrieving data from post %d =======', $this->post_id ) );

		/** @var DTO $line_data */
		foreach($data_set->get_data() as $line_data ){
			WP_CLI::log( $line_data );
		}

		if($data_set->has_errors()) {
			WP_CLI::log( '====== Completed with errors! =======');
			return;
		}

		WP_CLI::success( '====== Success! ====== ' );

	}

}


