<?php

namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Filter {

	/**
	 * @param DTO $dto
	 *
	 * @return bool
	 */
	public function is_filtered_out( DTO $dto );

}