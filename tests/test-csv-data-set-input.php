<?php

namespace OpenClubCSV\Test;

require_once( 'class-base.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-data-set-input.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-display.php' );

class CSVDataSetInput extends Base {

	public static function wpSetUpBeforeClass( \WP_UnitTest_Factory $factory ) {
	}

	/**
	 * This will let us know if we change the real config defaults.
	 * If so, we can update the dummy data in the base class.
	 */
	public function test_default_config_is_same_as_test_data(){
		
		$this->assertEquals( $this->get_default_config(), \OpenClub\CSV_Display::get_config() );

	}
	
}

