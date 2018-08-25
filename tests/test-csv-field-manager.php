<?php

namespace OpenClubCSV\Test;

require_once( 'class-base.php' );

/**
 * Class CSVFieldManager
 * @package OpenClubCSV\Test
 * @group failing
 */
class CSVFieldManager extends Base {
	//$post   = $this->get_valid_post();

	//$config = $this->get_valid_test_config();
	//$data_set_input = new \OpenClub\Data_Set_Input( $config );


	function test_validate_single_field_setting_with_valid_types() {

		$field_examples = $this->get_field_examples();

		$field_manager = new \OpenClub\Field_Manager();
		foreach ( $field_examples as $field_name => $config ) {
			$this->assertTrue( $field_manager->validate_single_field_setting( $field_name, $config ) );
		}
	}

	function test_validate_single_field_setting_with_invalid_type() {

		$file_name = OPENCLUB_CSV_PLUGIN_DIR . 'inc/fields/class-foo.php';
		$this->setExpectedException( 'Exception', $file_name . ' does not exist. Check the type setting in fields.' );

		$field_manager = new \OpenClub\Field_Manager();
		$field_manager->validate_single_field_setting( 'field_a', array( 'type' => 'foo' ) );
	}

	function test_validate_single_field_setting_with_illegal_name() {

		$field_name = '<script>alert()</script>Foo';

		$this->setExpectedException( 'Exception', 'Field ' . esc_html( $field_name ) . ' has invalid characters.' );

		$field_manager = new \OpenClub\Field_Manager();
		$field_manager->validate_single_field_setting( $field_name, array( 'type' => 'date' ) );
	}

	function test_validate_single_field_setting_with_no_type_throws_excpetion() {

		$this->setExpectedException( 'Exception', 'Field foo has no defined type, check fields setting.' );

		$field_manager = new \OpenClub\Field_Manager();
		$field_manager->validate_single_field_setting( 'foo', array() );
	}

	function test_validate_single_field_setting_with_empty_field_name_throws_exception() {

		$this->setExpectedException( 'Exception', 'You must set a field name.' );

		$field_manager = new \OpenClub\Field_Manager();
		$field_manager->validate_single_field_setting( ' ', array( 'type' => 'date' ) );
	}

	function test_setting_fields_and_retrieving_fields_keys_matches_config() {

		$data_set_input = new \OpenClub\Data_Set_Input( $this->get_valid_test_config());

		$field_manager = new \OpenClub\Field_Manager();
		$field_manager->init( $data_set_input );

		$this->assertEquals(
			$field_manager->get_all_registered_fields(),
			array_keys( $data_set_input->get_post()->field_settings )
		);
	}

	function test_setting_fields_and_retrieving_fields_objects_match_types() {

		$data_set_input = new \OpenClub\Data_Set_Input( $this->get_valid_test_config());

		$field_manager = new \OpenClub\Field_Manager();
		$field_manager->init( $data_set_input );

		foreach( $data_set_input->get_post()->field_settings as $field => $settings ) {
			$this->assertEquals( $settings['type'], $field_manager->get_field_type( $field ) );
		}
	}

	function test_setting_fields_and_retrieving_single_field_returns_field_object() {

		$data_set_input = new \OpenClub\Data_Set_Input( $this->get_valid_test_config());

		$field_manager = new \OpenClub\Field_Manager();
		$field_manager->init( $data_set_input );

		foreach( $data_set_input->get_post()->field_settings as $field => $settings ) {
			$this->assertEquals(
				'OpenClub\Fields\\' . ucwords( $settings['type'] ) . 'Field' ,
				get_class( $field_manager->get_field( $field ) )
			);
		}
	}


	function test_field_display_is_false_will_return_a_false_value() {

		$field_manager = new \OpenClub\Field_Manager();

		$this->assertTrue( $field_manager->field_display_is_true( array( 'display' => true ) ) );
		$this->assertFalse( $field_manager->field_display_is_true( array( 'display' => false ) ) );
	}
}
