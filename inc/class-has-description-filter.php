<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/interface-filter.php' );

class Empty_Description implements Filter {

	/**
	 * @param DTO $dto
	 *
	 * @return bool
	 */
	public function is_filtered_out( DTO $dto ) {

		if ( empty( $dto->get_value( 'Description' ) ) ) {
			return true;
		}

	}

}