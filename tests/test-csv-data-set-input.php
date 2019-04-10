<?php

namespace OpenClubCSV\Test;

use OpenClub\Data_Set_Input;
use OpenClub\Null_Filter;

require_once( 'class-base.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-data-set-input.php' );

/**
 * Class CSVDataSetInput
 * @package OpenClubCSV\Test
 * @group input
 */
class CSVDataSetInput extends Base {

	/**
	 * @var \OpenClub\Data_Set_Input
	 */
	private $csv_data_input;

	/**
	 * @var \WP_Post
	 */
	private $valid_post;

	public static function wpSetUpBeforeClass( \WP_UnitTest_Factory $factory ) {
	}

	private function get_data_set_input_object() {
		return new \OpenClub\Data_Set_Input();
	}

	public function setUp() {
		$this->csv_data_input = new \OpenClub\Data_Set_Input();
	}

	public function test_init_called_with_no_array_will_not_set_config() {

		$this->assertEquals( $this->csv_data_input->get_init_has_been_called(), false );

	}

	public function test_init_called_with_array_will_set_config() {

		$this->csv_data_input->init( $this->get_valid_test_config() );
		$this->assertEquals( $this->csv_data_input->get_init_has_been_called(), true );

	}

	public function test_constructor_passed_with_bad_arg_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'If set, $config must be an array.' );
		$o = new \OpenClub\Data_Set_Input( 'foo' );
	}

	public function test_init_called_with_bad_arg_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'If set, $config must be an array.' );
		$this->csv_data_input->init( true );
	}

	/**
	 * This will let us know if we change the real config defaults.
	 * If so, we can update the dummy data in the base class.
	 */
	public function test_default_config_is_same_as_test_data() {

		$this->assertEquals( $this->get_default_config(), \OpenClub\CSV_Display::get_config() );

	}

	// ########################### get_config() ###########################

	public function test_retrieving_config_with_bad_key_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'Attempt to retrieve invalid config key.' );

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->csv_data_input->get_config( 'ss' );

	}

	public function test_retrieving_config_with_default_config_is_correct() {

		$post   = $this->get_valid_post();
		$config = $this->get_default_config( array( 'post_id' => $post->ID ) );
		$this->csv_data_input->init( $config );

		$retrieved_config = array(
			'post'                    => $post,
			'post_id'                 => $post->ID,
			'filter'                  => new \OpenClub\Null_Filter(),
			'group_by_field'          => false,
			'overridden_fields'       => array(),
			'context'                 => false,
			'limit'                   => false,
			'display'                 => 'table',
			'future_items_only'       => false,
			'error_messages'          => true,
			'error_lines'             => true,
			'show_future_past_toggle' => false,
			'display_config'          => false,
			'plugin_template_dir'     => false,
		);

		$this->assertEquals( $retrieved_config, $this->csv_data_input->get_config() );

	}

	public function test_retrieving_config_with_sample_config_is_correct() {

		$post   = $this->get_valid_post();
		$config = $this->get_default_config( array( 'post_id' => $post->ID ) );

		$config['group_by_field']          = 'Date';
		$config['fields']                  = 'Event,Date';
		$config['error_messages']          = 'no';
		$config['error_lines']             = 'no';
		$config['show_future_past_toggle'] = 'yes';
		$config['context']                 = 'foo';
		$config['limit']                   = 10;
		$config['show_future_past_toggle'] = 'yes';
		$config['filter']                  = 'Empty_Description';
		$config['future_items_only']       = 'yes';
		$config['display_config']          = 'yes';
		$config['plugin_template_dir']     = 'TEST_DIR';

		$this->csv_data_input->init( $config );

		$retrieved_config = array(
			'post'                    => $post,
			'post_id'                 => $post->ID,
			'filter'                  => new \OpenClub\Empty_Description(),
			'group_by_field'          => 'Date',
			'overridden_fields'       => array( 'Event', 'Date' ),
			'context'                 => 'foo',
			'limit'                   => 10,
			'display'                 => 'table',
			'future_items_only'       => true,
			'error_messages'          => false,
			'error_lines'             => false,
			'show_future_past_toggle' => true,
			'display_config'          => true,
			'plugin_template_dir'     => 'TEST_DIR'
		);

		$this->assertEquals( $retrieved_config, $this->csv_data_input->get_config() );

	}


	// ########################### Post ID ###########################

	public function test_init_passing_empty_post_id_will_throw_exception() {

		$this->setExpectedException( 'Exception', '$post_id was not passed' );
		$this->csv_data_input->init( $this->get_default_config() );
	}

	public function test_init_non_numeric_empty_post_id_will_throw_exception() {

		$this->setExpectedException( 'Exception', '$post_id is not numeric' );
		$config            = $this->get_default_config();
		$config['post_id'] = 'hamster';
		$this->csv_data_input->init( $config );
	}

	// ########################### Group by field ###################################

	public function test_init_setting_empty_space_group_by_field_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'set_group_by_field called with invalid value, likely an empty space' );
		$config                   = $this->get_valid_test_config();
		$config['group_by_field'] = ' ';
		$this->csv_data_input->init( $config );
	}


	// ##################################### Error Messages #####################################

	public function test_init_setting_error_messages_with_bad_value_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'error_messages must be yes, no or not set. The default is yes.' );
		$config                   = $this->get_valid_test_config();
		$config['error_messages'] = 'hamster';
		$this->csv_data_input->init( $config );
	}

	public function test_init_setting_error_messages_with_int_one_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'error_messages must be yes, no or not set. The default is yes.' );
		$config                   = $this->get_valid_test_config();
		$config['error_messages'] = 1;
		$this->csv_data_input->init( $config );
	}

	public function test_init_setting_error_messages_with_default_will_return_true() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), true );
	}

	public function test_init_setting_error_messages_with_yes_will_return_true() {

		$config                   = $this->get_valid_test_config();
		$config['error_messages'] = 'yes';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), true );
	}

	public function test_init_setting_error_messages_with_no_will_return_true() {

		$config                   = $this->get_valid_test_config();
		$config['error_messages'] = 'no';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), false );
	}

	public function test_init_setting_error_messages_with_zero_will_return_false() {

		$config                   = $this->get_valid_test_config();
		$config['error_messages'] = 0;
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), false );
	}

	public function test_init_setting_error_messages_with_string_zero_will_return_false() {

		$config                   = $this->get_valid_test_config();
		$config['error_messages'] = '0';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), false );
	}

	// ##################################### Error Lines #####################################

	public function test_init_setting_error_lines_with_bad_value_will_throw_exception() {

		$config = $this->get_valid_test_config();

		$this->setExpectedException( 'Exception', 'error_lines must be yes, no or not set. The default is yes.' );
		$config                = $this->get_valid_test_config();
		$config['error_lines'] = 'hamster';
		$this->csv_data_input->init( $config );
	}


	public function test_init_setting_error_lines_with_default_will_return_true() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_lines(), true );
	}


	public function test_init_setting_error_lines_with_yes_will_return_true() {

		$config                = $this->get_valid_test_config();
		$config['error_lines'] = 'yes';
		$this->csv_data_input->init( $config );
		$this->assertEquals( true, $this->csv_data_input->get_error_lines() );
	}

	public function test_init_setting_error_lines_with_no_will_return_true() {

		$config                = $this->get_valid_test_config();
		$config['error_lines'] = 'no';
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_error_lines() );
	}

	public function test_init_setting_error_lines_with_not_yes_will_return_false() {

		$config                = $this->get_valid_test_config();
		$config['error_lines'] = 0;
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_error_lines() );

		$config['error_lines'] = '0';
		$o                     = new \OpenClub\Data_Set_Input( $config );
		$this->assertEquals( false, $o->get_error_lines() );

	}

	// #####################################  Future Items Only #####################################

	public function test_init_setting_future_items_only_with_bad_value_will_throw_exception() {

		$config = $this->get_valid_test_config();
		$this->setExpectedException( 'Exception', '$config[\'future_items_only\'] can be "yes", "no", 1, 2 or must not be set.' );
		$config['future_items_only'] = 'hamster';
		$this->csv_data_input->init( $config );
	}

	public function test_init_setting_future_items_will_return_correct_values() {

		$set_and_return_values = array(
			'yes' => true,
			2     => true,
			'no'  => false,
			1     => false,
		);

		$config = $this->get_valid_test_config();
		foreach ( $set_and_return_values as $set => $return ) {

			$config['future_items_only'] = $set;

			$o = $this->get_data_set_input_object();
			$o->init( $config );
			$this->assertEquals( $return, $o->get_future_items() );
		}
	}

	public function test_init_future_items_only_not_set_returns_false() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_future_items() );
	}

// #####################################  Display #####################################

	public function test_init_display_with_padded_string_throws_exception() {

		$this->setExpectedException( 'Exception', 'display cannot have empty spaces at the beginning or end.' );
		$config            = $this->get_valid_test_config();
		$config['display'] = ' hamster ';
		$this->csv_data_input->init( $config );
	}

	public function test_init_display_sets_correct_value() {

		$config            = $this->get_valid_test_config();
		$config['display'] = 'foo';
		$this->csv_data_input->init( $config );
		$this->assertEquals( 'foo', $this->csv_data_input->get_display() );
	}

// #####################################  group_by_field #####################################

	public function test_get_group_by_field_returns_correct_field() {

		$config                   = $this->get_valid_test_config();
		$config['group_by_field'] = 'Date';
		$this->csv_data_input->init( $config );
		$this->assertEquals( 'Date', $this->csv_data_input->get_group_by_field() );
	}

	public function test_has_group_by_field_returns_true_if_group_by_field_is_set() {

		$config                   = $this->get_valid_test_config();
		$config['group_by_field'] = 'Date';
		$this->csv_data_input->init( $config );
		$this->assertTrue( $this->csv_data_input->has_group_by_field() );
	}

	public function test_setting_group_by_field_with_invalid_field_will_throw_excpetion() {

		$this->setExpectedException( 'Exception', 'set_group_by_field called with invalid field.' );
		$config                   = $this->get_valid_test_config();
		$config['group_by_field'] = 'Datsse';
		$this->csv_data_input->init( $config );
	}

	public function test_has_group_by_field_returns_false_if_no_group_by_field_is_set() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertFalse( $this->csv_data_input->has_group_by_field() );
	}

// #####################################  Context #####################################

	public function test_setting_context_returns_same_value() {

		$config            = $this->get_valid_test_config();
		$config['context'] = 'hamster';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_context(), $config['context'] );
	}

	public function test_init_setting_no_field_overrides_will_set_empty_array() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_fields_override(), array() );
	}

// #####################################  Limit #####################################

	public function test_init_setting_limit_with_non_integer_will_throw_exception() {

		$config          = $this->get_valid_test_config();
		$config['limit'] = 'dd';
		$this->setExpectedException( 'Exception', '$limit must be an integer and greater than zero.' );
		$this->csv_data_input->init( $config );
	}

	public function test_init_setting_limit_with_negative_integer_will_throw_exception() {

		$config          = $this->get_valid_test_config();
		$config['limit'] = - 1;
		$this->setExpectedException( 'Exception', '$limit must be an integer and greater than zero.' );
		$this->csv_data_input->init( $config );
	}

	public function test_init_setting_limit_will_return_correct_value() {

		$config          = $this->get_valid_test_config();
		$config['limit'] = 10;
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_limit(), 10 );
	}

	public function test_init_not_setting_limit_will_return_null() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_limit(), false );
	}


// ################################### Fields ###################################


	public function test_init_setting_bad_field_overrides_will_throw_exception() {
		$this->setExpectedException( 'Exception', 'Field override error: field Hamster does not exist. Check the config.' );

		$config           = $this->get_valid_test_config();
		$config['fields'] = 'Event,Fare,Hamster';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_fields_override(), explode( ',', $config['fields'] ) );
	}

	public function test_setting_fields_returns_valid_fields() {

		$config           = $this->get_valid_test_config();
		$config['fields'] = 'Event,Date';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_fields_override(), explode( ',', $config['fields'] ) );
	}

	public function test_not_setting_fields_will_return_empty_array() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_fields_override(), array() );
	}

	// ################################### Filter ###################################

	public function test_get_filter_with_no_filter_set_returns_null_filter() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_filter(), new \OpenClub\Null_Filter() );
	}

	public function test_get_filter_with_filter_set_returns_correct_filter() {

		$config           = $this->get_valid_test_config();
		$config['filter'] = 'Empty_Description';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_filter(), new \OpenClub\Empty_Description() );
	}

	public function test_init_setting_non_existing_filter_will_throw_exception() {

		$config           = $this->get_valid_test_config();
		$config['filter'] = 'foo';
		$this->setExpectedException( 'Exception', 'Filter class \OpenClub\foo does not exist, check the value passed in $config[ \'filter\' ]' );
		$this->csv_data_input->init( $config );
	}


	// ############################# show_future_past_toggle ###########################

	public function test_init_setting_show_future_past_toggle_with_bad_value_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'get_boolean_from_config_value() called with hamster, expected yes or no' );
		$config                            = $this->get_valid_test_config();
		$config['show_future_past_toggle'] = 'hamster';
		$this->csv_data_input->init( $config );
	}


	public function test_init_setting_show_future_past_toggle_with_default_will_return_true() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_show_future_past_toggle(), false );
	}


	public function test_init_setting_show_future_past_toggle_with_yes_will_return_true() {

		$config                            = $this->get_valid_test_config();
		$config['show_future_past_toggle'] = 'yes';
		$this->csv_data_input->init( $config );
		$this->assertEquals( true, $this->csv_data_input->get_show_future_past_toggle() );
	}

	public function test_init_setting_show_future_past_toggle_with_no_will_return_true() {

		$config                            = $this->get_valid_test_config();
		$config['show_future_past_toggle'] = 'no';
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_show_future_past_toggle() );
	}

	public function test_init_setting_show_future_past_toggle_with_not_yes_will_return_false() {

		$config                            = $this->get_valid_test_config();
		$config['show_future_past_toggle'] = 0;
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_show_future_past_toggle() );

		$config['show_future_past_toggle'] = '0';
		$o                                 = new \OpenClub\Data_Set_Input( $config );
		$this->assertEquals( false, $o->get_show_future_past_toggle() );

	}

	// ############################# display_config ###########################

	public function test_init_setting_display_config_with_bad_value_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'get_boolean_from_config_value() called with hamster, expected yes or no' );
		$config                   = $this->get_valid_test_config();
		$config['display_config'] = 'hamster';
		$this->csv_data_input->init( $config );
	}


	public function test_init_setting_display_config_with_default_will_return_true() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_show_display_config_setting(), false );
	}


	public function test_init_setting_display_config_with_yes_will_return_true() {

		$config                   = $this->get_valid_test_config();
		$config['display_config'] = 'yes';
		$this->csv_data_input->init( $config );
		$this->assertEquals( true, $this->csv_data_input->get_show_display_config_setting() );
	}

	public function test_init_setting_display_config_with_no_will_return_true() {

		$config                   = $this->get_valid_test_config();
		$config['display_config'] = 'no';
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_show_display_config_setting() );
	}

	public function test_init_setting_display_config_with_not_yes_will_return_false() {

		$config                   = $this->get_valid_test_config();
		$config['display_config'] = 0;
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_show_display_config_setting() );

		$config['display_config'] = '0';
		$o                        = new \OpenClub\Data_Set_Input( $config );
		$this->assertEquals( false, $o->get_show_display_config_setting() );

	}
}