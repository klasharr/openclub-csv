<?php

namespace OpenClub\Fields;

interface Field_Validator {

	public function validate( $value );

	public function getMessage();

}