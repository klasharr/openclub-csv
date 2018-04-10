<?php

namespace OpenClub\Fields;

interface Field {

	public function validate( $value );

	public function getMessage();

}