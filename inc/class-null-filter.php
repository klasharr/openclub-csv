<?php

namespace OpenClub;

require_once( 'interface-filter.php' );

class Null_Filter implements Filter {

	/**
	 * @param DTO $dto
	 *
	 * @return bool
	 */
	public function filter( DTO $dto ) {

		return true;

	}

}