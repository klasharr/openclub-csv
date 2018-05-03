<?php
namespace OpenClub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \OpenClub\CSV_Display;
use \OpenClub\Factory;
use \OpenClub\CSV_Util;
use \WP_CLI;

abstract class CLI_Base {

	public function foo(){
		WP_CLI::log('foo');
	}

}