<?php

namespace OpenClub;

interface Filter {

	/**
	 * @param DTO $dto
	 *
	 * @return bool
	 */
	public function filter( DTO $dto );

}